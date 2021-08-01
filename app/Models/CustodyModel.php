<?php

namespace App\Models;

use App\Helpers\AppHelper;
use Exception;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Continue_;
use stdClass;

class CustodyModel
{

    // public static function getDriverNoneWithdrawItems($driverId){

    //     $query = DB::table(DB::raw(DatabaseViews::shipment_items_view()))
    //     ->where("shipment_driverId", "=", $driverId)
    //     ->where("shipment_status", "=", ShipmentModel::SHIPMENT_STATUS_APPROVED)
    //     ->whereRaw("IFNULL(shItem_status,0) = 0")
    //     ->select("*")
    //     ->get();
    //     return $query;
    // }

    public static function getDriverRequiredItem($driverId, $sku){

        $query = DB::table(DB::raw(DatabaseViews::shipment_items_view()))
        ->where("shipment_status", "=", ShipmentModel::SHIPMENT_STATUS_APPROVED)
        ->where("shipment_driverId", "=", $driverId)
        ->where("sku","=",$sku)
        ->selectRaw("sku, fullItemName,
        SUM(shItem_qty) AS requiredQty,
        IFNULL((SELECT IFNULL(custody_qty,0) FROM custody WHERE custody_driverId=$driverId AND custody_sku=sku),0) AS qty,
        IFNULL((SELECT IFNULL(custody_qty,0)-IFNULL(custody_usedQty,0) FROM custody WHERE custody_driverId=$driverId AND custody_sku=sku),0) AS remainingQty")
        ->groupBy("sku")
        ->groupBy("fullItemName")
        ->groupBy("qty")
        ->groupBy("remainingQty");
        return $query->first();
    }

    public static function getDriverRequiredItems($driverId){

        $query = DB::table(DB::raw(DatabaseViews::shipment_items_view()))
        ->where("shipment_status", "=", ShipmentModel::SHIPMENT_STATUS_APPROVED)
        ->where("shipment_driverId", "=", $driverId)
        ->selectRaw("sku, fullItemName,client_name,
        SUM(shItem_qty) AS requiredQty,
        IFNULL((SELECT IFNULL(custody_qty,0) FROM custody WHERE custody_driverId=$driverId AND custody_sku=sku),0) AS qty,
        IFNULL((SELECT IFNULL(custody_qty,0)-IFNULL(custody_usedQty,0) FROM custody WHERE custody_driverId=$driverId AND custody_sku=sku),0) AS remainingQty")
        ->groupBy('item_clientId')
        ->groupBy("sku")
        ->groupBy("fullItemName")
        ->groupBy("qty")
        ->groupBy("remainingQty");
        return $query->get();
    }

    // public static function withdrawItem($driverId,$itemSku,$qty){

    //     try{
    //         DB::beginTransaction();

    //         $query = DB::table(DB::raw(DatabaseViews::shipment_items_view()))
    //         ->where("shipment_status", "=", ShipmentModel::SHIPMENT_STATUS_APPROVED)
    //         ->where("shipment_driverId", "=", $driverId)
    //         ->where("shItem_sku", "=", $itemSku)
    //         ->whereRaw("IFNULL(shItem_status,0) <= 2")
    //         ->selectRaw("*")
    //         ->get();

    //         if (!empty($query))
    //         foreach($query as $item){

    //             $data = array();
    //             if ($qty > 0){

    //                 if ($qty > $item->shItem_qty) continue;

    //                 $data['shItem_status'] = ShipmentModel::SHIPMENT_ITEM_STATUS_WITHDRAW;
    //                 $qty = $qty - $item->shItem_qty;
    //             }
    //             else{
    //                 $data['shItem_status'] = ShipmentModel::SHIPMENT_ITEM_STATUS_OUTSTOCK;
    //             }

    //             $affected = DB::table('shipment_items')
    //             ->where('shItem_id', $item->shItem_id)
    //             ->update($data);
    //         }

    //         if ($qty <> 0){
    //             DB::rollBack();
    //             return "QTY greater than total items";
    //         }

    //         ///////////////////////////////////////////////////////////////////////////////////////

    //         $remainingItems = self::getDriverNoneWithdrawItems($driverId);
    //         if (!empty($remainingItems) && count($remainingItems) == 0){
    //             $affected = DB::table('shipments')
    //             ->where('shipment_status', ShipmentModel::SHIPMENT_STATUS_APPROVED)
    //             ->where('shipment_driverId', $driverId)
    //             ->update(['shipment_status'=>ShipmentModel::SHIPMENT_STATUS_SHIPPED]);
    //         }


    //         DB::commit();
    //         return true;
    //     }
    //     catch(Exception $e){
    //         DB::rollBack();
    //         return "Can't withdraw items";
    //     }
    //     return false;

    // }

    public static function withdrawItem($driverId,$itemSku,$qty){

        try{
            if (empty($driverId) || empty($itemSku)) return "invalid data";

            DB::beginTransaction();

            $item = self::getDriverRequiredItem($driverId, $itemSku);

            if (empty($item)){
                DB::rollBack();
                return "invalid item";
            }

            if (($item->remainingQty + intval($qty)) > $item->requiredQty){
                DB::rollBack();
                return "Qty + remaining greater than required  items";
            }

            $custodyItem = self::getItemCustody($driverId,$itemSku);

            $custodyData = array();

            if (empty($custodyItem->custody_id)){
                $custodyData['custody_qty'] = $qty;
                $custodyData['custody_usedQty'] = 0;
                $custodyData['custody_driverId'] = $driverId;
                $custodyData['custody_sku'] = $itemSku;
                $result = DB::table('custody')->insertGetId($custodyData);
            }
            else{
                // if ($qty < $custodyItem->custody_usedQty){
                //     DB::rollBack();
                //     return "new Qty less than used Qty";
                // }
                $custodyData['custody_qty'] = $custodyItem->custody_qty + intval($qty);
                $affected = DB::table('custody')
                ->where('custody_driverId', $driverId)
                ->where('custody_sku', $itemSku)
                ->update($custodyData);
            }

            DB::commit();
            return true;
        }
        catch(Exception $e){
            DB::rollBack();
            return "Can't withdraw items";
        }
        return false;

    }

    public static function checkCustodyWithdrawed($driverId)
    {
         // make sure that no required items remaining
         $requiredItems = self::getDriverRequiredItems($driverId);
         if (!empty($requiredItems)){

             $allItemsWithdrawed = true;
             foreach($requiredItems as $i)
                 if ($i->remainingQty <= 0) $allItemsWithdrawed = false;

             if ($allItemsWithdrawed == true){
                 $affected = DB::table('shipments')
                 ->where('shipment_status', ShipmentModel::SHIPMENT_STATUS_APPROVED)
                 ->where('shipment_driverId', $driverId)
                 ->update(['shipment_status'=>ShipmentModel::SHIPMENT_STATUS_SHIPPED]);

                 $shipments = PlanModel::getDriverShippedShipments($driverId);
                 foreach($shipments as $shipment){
                     AppHelper::smsMisrSend(
                         ClientModel::getClientByID(
                         $shipment->shipment_clientId),
                         $shipment->shipment_customerPhone,
                         'test shipped');
                 }

             }
         }
    }

    public static function adjustCash($driverId,$shipmentId,$value){

        try{
            if (empty($driverId) || empty($shipmentId)) return "invalid data";

            DB::beginTransaction();

            $custodyShipment = ShipmentModel::getShipmentByID($shipmentId);
            if ($custodyShipment->shipment_driverId != $driverId){
                DB::rollBack();
                return "invalid shipment";
            }

            if (floatval($value) > ($custodyShipment->totalDeliveredItemValue + $custodyShipment->shipment_fees - $custodyShipment->shipment_driverPaid)){
                DB::rollBack();
                return "value greater than total due value";
            }

            $shipmentData = array();
            $shipmentData['shipment_driverPaid'] = $custodyShipment->shipment_driverPaid + floatval($value);
            $affected = DB::table('shipments')
            ->where('shipment_id', $shipmentId)
            ->where('shipment_driverId', $driverId)
            ->update($shipmentData);

            DB::commit();
            return true;
        }
        catch(Exception $e){
            DB::rollBack();
            return "Can't adjust shipment cash";
        }
        return false;

    }

    public static function adjustCustody($driverId,$itemSku,$qty){
        try{
            if (empty($driverId) || empty($itemSku)) return "invalid data";

            DB::beginTransaction();

            $custodyItem = self::getItemCustody($driverId,$itemSku);

            if ($qty > $custodyItem->remainingQty){
                DB::rollBack();
                return "Qty greater than remaining items";
            }

            $custodyData = array();

            $custodyData['custody_usedQty'] = $custodyItem->custody_usedQty + intval($qty);
            $affected = DB::table('custody')
            ->where('custody_driverId', $driverId)
            ->where('custody_sku', $itemSku)
            ->update($custodyData);

            DB::commit();
            return true;
        }
        catch(Exception $e){
            DB::rollBack();
            return "Can't withdraw items";
        }
        return false;

    }


    public static function getItemCustody($driverId, $sku){
        $query = DB::table(DB::raw(DatabaseViews::custody_view()))
        ->where("custody_driverId","=",$driverId)
        ->where("custody_sku","=",$sku)
        ->select('*');
        return $query->first();
    }

    public static function getTotalCustodyAndCash($driverId = 0){

        $query = DB::table(DB::raw(DatabaseViews::drivers_view()))
        ->select('*');

        if (empty($driverId)){
            $query = $query->where("totalCustody",">",0)
                           ->orWhere("totalCash",">",0)->get();
        }
        else{
            $query = $query->where("driver_id","=",$driverId)->first();
        }

        return $query;

    }

    public static function getDriverCash($driverId){

        $query = DB::table(DB::raw(DatabaseViews::shipments_view()))
        ->where("shipment_driverId","=",$driverId)
        ->where("shipment_paymentMethodId","<>",3)
        ->where("shipment_status","=",ShipmentModel::SHIPMENT_STATUS_COMPLETED)
        ->whereRaw("IFNULL(shipment_driverPaid,0) < (totalDeliveredItemValue+shipment_fees)")
        ->selectRaw('*')
        ->get();

        return $query;
    }

    public static function getDriverCustody($driverId){

        $query = DB::table(DB::raw(DatabaseViews::custody_items_view()))
        ->where("custody_driverId","=",$driverId)
        ->where("remainingQty","<>",0)
        ->select('*')
        ->get();

        return $query;
    }

}

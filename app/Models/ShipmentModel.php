<?php

namespace App\Models;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Helpers\AppHelper;
use DateTime;

use function PHPUnit\Framework\returnSelf;

class ShipmentModel
{
    public const SHIPMENT_STATUS_PENDING = 0;
    public const SHIPMENT_STATUS_APPROVED = 1;
    public const SHIPMENT_STATUS_SHIPPED = 2;
    public const SHIPMENT_STATUS_COMPLETED = 3;
    public const SHIPMENT_STATUS_CANCELLED = 4;

    public const SHIPMENT_TYPE_NEW = 1;
    public const SHIPMENT_TYPE_FOLLOWUP = 2;

    // public const SHIPMENT_SMS_NONE = 2;
    // public const SHIPMENT_SMS_PENDING = 2;
    // public const SHIPMENT_SMS_SHIPPED = 2;
    // public const SHIPMENT_SMS_COMPLETED = 2;

    public const SHIPMENT_ITEM_STATUS_NONE = 0;
    public const SHIPMENT_ITEM_STATUS_DELIVERED = 1;
    public const SHIPMENT_ITEM_STATUS_RETURNED = 2;
    // Read data using username and password

    public static function getAllShipments($status = -1, $type = 0, $from = null, $to = null)
    {
        $query = DB::table(DB::raw(DatabaseViews::shipments_view()))
            ->select('*')
            ->orderBy('shipment_id', 'DESC');

        if ($status >= 0){
            $query->where("shipment_status","=",$status);
        }

        if (!empty($from)){
            $query->where("shipment_date","<=",$from);
        }
        if (!empty($to)){
            $query->where("shipment_date",">=",$to);
        }

        if (!empty($type)){
            if ($type == self::SHIPMENT_TYPE_NEW)
                $query->whereRaw("shipment_followupDate IS NULL");
            elseif($type == self::SHIPMENT_TYPE_FOLLOWUP)
                $query->whereRaw("shipment_followupDate IS NOT NULL");
        }

        return $query->get();
    }

    public static function getShipmentByID($shipmentID)
    {
        $query = DB::table(DB::raw(DatabaseViews::shipments_view()))
            ->select('*')
            ->where("shipment_id", "=", $shipmentID)
            ->first();

        if (empty($query)) {
            return "Error Getting shipment!";
        }

        $query->items = DB::table(DB::raw(DatabaseViews::shipment_items_view()))
        ->select('*')
        ->where("shipment_id", "=", $shipmentID)
        ->get();

        foreach($query->items as $item){
            AppHelper::setItemImage($item);
        }

        return $query;
    }

    public static function getFollowupShipments(\DateTime $date)
    {
        $query = DB::table(DB::raw(DatabaseViews::shipments_view()))
            ->select('*')
            ->where("shipment_status","=",self::SHIPMENT_STATUS_PENDING)
            ->whereRaw("DATE(shipment_followupDate) = '" . $date->format('Y-m-d')."'");

        $query->orderBy('shipment_id', 'DESC');

        return $query->get();
    }

    // public static function insertShipment(array $shipmentData)
    // {
    //     try {

    //         $query = DB::table('shipments')
    //                 ->select('shipment_id')
    //                 ->where("shipment_ref" , "=", $shipmentData['shipment_ref'])
    //                 ->first();

    //         if (!empty($query))return $query->shipment_id;

    //         $shipmentData['shipment_created'] = date("Y-m-d H:i:s");
    //         $result = DB::table('shipments')->insertGetId($shipmentData);

    //         if  (empty($result)){
    //             //rollbacl
    //             return "Error Saving shipment!";
    //         }

    //         return $result;

    //     } catch (Exception $e) {
    //         return "Error Saving shipment!";
    //     }
    // }

    // public static function insertShipmentProduct(array $shipmentProductData)
    // {
    //     try {

    //         $query = DB::table('shipment_products')
    //                 ->select('shItem_id')
    //                 ->where("shItem_shipmentId" , "=", $shipmentProductData['shItem_shipmentId'])
    //                 ->where("shItem_sku" , "=", $shipmentProductData['shItem_sku'])
    //                 ->first();

    //         if (!empty($query))return $query->shItem_id;

    //         $result = DB::table('shipment_products')->insertGetId($shipmentProductData);

    //         if  (empty($result)){
    //             //rollbacl
    //             return "Error Saving Product!";
    //         }

    //         return $result;

    //     } catch (Exception $e) {
    //         return "Error Saving Product!";
    //     }
    // }

    public static function saveShipmentById(array $shipmentdata)
    {
        $isNew = empty($shipmentdata['shipment_id']) || $shipmentdata['shipment_id'] == 0;

        if ($isNew && !empty($shipmentdata['shipment_ref'])) {
            $query = DB::table('shipments')
                ->select('shipment_id')
                ->where("shipment_ref", "=", $shipmentdata['shipment_ref'])
                ->first();

            // if (!empty($query->shipment_id))
            //     return "referance already exists";
            if (empty($query->shipment_id)){
                $isNew = true;
            }
            else{
                $isNew = false;
                $shipmentdata['shipment_id'] = $query->shipment_id;
            }
        }

        return self::saveShipment($shipmentdata,$isNew);
    }

    public static function saveShipmentByRef(array $shipmentdata)
    {
        $isNew = empty($shipmentdata['shipment_ref']) || $shipmentdata['shipment_ref'] == 0;

        if (!$isNew) {
            $query = DB::table('shipments')
                ->select('shipment_id')
                ->where("shipment_ref", "=", $shipmentdata['shipment_ref'])
                ->first();

            if (empty($query->shipment_id)){
                $isNew = true;
            }
            else{
                $isNew = false;
                $shipmentdata['shipment_id'] = $query->shipment_id;
            }
        }

        return self::saveShipment($shipmentdata,$isNew);
    }
    private static function saveShipment(array $shipmentdata,$isNew)
    {
        try {
            if (empty($shipmentdata['shipment_zoneId'])){
                $state = empty($shipmentdata['shipment_customerState'])?'':$shipmentdata['shipment_customerState'];
                $city = empty($shipmentdata['shipment_customerCity'])?'':$shipmentdata['shipment_customerCity'];
                // $address = empty($shipmentdata['shipment_customerAddress'])?"":$shipmentdata['shipment_customerAddress'];
                $zone = ZoneModel::detectZone($state, $city);

                if (!empty($zone->region_zoneId))
                    $shipmentdata['shipment_zoneId'] = $zone->region_zoneId;
                else
                    $shipmentdata['shipment_zoneId'] = 0;
            }

            $result = "";
            if ($isNew) {
                if (empty($shipmentdata['shipment_key']))
                $shipmentdata['shipment_key'] = AppHelper::generateRandomString();
                $shipmentdata['shipment_created'] = date("Y-m-d H:i:s");
                $result = DB::table('shipments')->insertGetId($shipmentdata);

//                if ($shipmentdata['shipment_status'] == self::SHIPMENT_STATUS_PENDING){
//                    AppHelper::smsMisrSend(
//                        ClientModel::getClientByID($shipmentdata['shipment_clientId']),'',
//                        'test pending',
//                        $shipmentdata['shipment_customerPhone']
//                        ,'test');
//                }

            } else {
                $shipmentdata['shipment_updated'] = date("Y-m-d H:i:s");
                $affected = DB::table('shipments')
                ->where('shipment_id', $shipmentdata['shipment_id'])
                ->update($shipmentdata);
                $result = $shipmentdata['shipment_id'];
            }

            return (empty($result) ? "Error Saving shipment!" : $result);
        } catch (Exception $e) {
            return "Error Saving shipment!";
        }
    }

    public static function saveShipmentItem(array $itemdata)
    {
        try {

            $query = DB::table('shipment_items')
                ->select('shItem_id')
                ->where("shItem_shipmentId", "=", $itemdata['shItem_shipmentId'])
                ->where("shItem_sku", "=", $itemdata['shItem_sku'])
                ->first();

            $isNew = empty($query);

            $result = "";
            if ($isNew) {
                $result = DB::table('shipment_items')->insertGetId($itemdata);
            } else {
                $affected = DB::table('shipment_items')
                ->where('shItem_id', $query->shItem_id)
                ->update($itemdata);
                $result = $query->shItem_id;
            }

            if (empty($result))
                return "Error Saving shipment item!";

            // DB::statement("UPDATE shipments SET shipment_total = (SELECT SUM(op_price*op_qty) FROM shipment_products WHERE op_shipmentId = ".$opdata['op_shipmentId'].")");

            return $result;
        } catch (Exception $e) {
            return "Error Saving item!";
        }
    }

    public static function deleteShipment($id)
    {

        $result = DB::table('shipments')
            ->where('shipment_id', $id)
            ->delete();
        if (!$result) {
            return $result;
        }

        return true;
    }

    public static function deleteShipmentByRef($ref, $clientId)
    {
        try{
            $result = DB::table('shipments')
            ->where('shipment_ref', $ref)
            ->where('shipment_clientId', $clientId)
            ->delete();

            return true;
        }
        catch(Exception $e){
            return false;
        }
    }

    public static function followupShipment($shipmentId, \DateTime $date)
    {
        try {
            $shipmentdata['shipment_updated'] = date("Y-m-d H:i:s");
            $shipmentdata['shipment_followupDate'] = $date->format('Y-m-d');
            $shipmentdata['shipment_status'] = self::SHIPMENT_STATUS_PENDING;
            $shipmentdata['shipment_driverId'] = DB::raw('NULL');

            $affected = DB::table('shipments')
            ->where('shipment_id', $shipmentId)
            ->update($shipmentdata);

            return true;
        } catch (Exception $e) {
            return "Error Saving shipment!";
        }
    }

    public static function approveShipment($shipmentId)
    {
        try {
                $shipmentdata['shipment_updated'] = date("Y-m-d H:i:s");
                $shipmentdata['shipment_status'] = self::SHIPMENT_STATUS_APPROVED;

                $affected = DB::table('shipments')
                ->where('shipment_id', $shipmentId)
                ->update($shipmentdata);

            return true;
        } catch (Exception $e) {
            return "Error Saving shipment!";
        }
    }

    public static function cancelShipment( $shipmentId, $driverId = 0)
    {
        try {
                $shipmentdata['shipment_updated'] = date("Y-m-d H:i:s");
                $shipmentdata['shipment_status'] = self::SHIPMENT_STATUS_CANCELLED;
                // $shipmentdata['shipment_cancelNote'] = $note;

                $affected = DB::table('shipments')
                ->where('shipment_id', $shipmentId);

                if (!empty($driverId)){
                    $affected->where('shipment_driverId', $driverId);
                }
                else{
                    $shipmentdata['shipment_driverId'] = DB::raw('NULL');
                }

                $affected->update($shipmentdata);

            return true;
        } catch (Exception $e) {
            return "Error Saving shipment!";
        }
    }

    public static function deliverShipment($driverId, $shipmentId, array $items)
    {
        try {
            if (empty($shipmentId) || empty($items) || count($items) <= 0) return "invalid data";

            DB::beginTransaction();

            foreach($items as $item){

                $itemCustody = DB::table(DB::raw(DatabaseViews::custody_view()))
                ->where("custody_driverId","=",$driverId)
                ->where("custody_sku","=",$item->shItem_sku)
                ->select('*')
                ->first();

                if (empty($itemCustody->remainingQty) || $itemCustody->remainingQty < $item->shItem_qty){
                    return "Not enough custody qty";
                    DB::rollBack();
                }

                $shItem = DB::table(DB::raw(DatabaseViews::shipment_items_view()))
                ->where("shipment_driverId","=",$driverId)
                ->where("shItem_shipmentId","=",$shipmentId)
                ->where("shItem_sku","=",$item->shItem_sku)
                ->select('*')
                ->first();

                if (empty($shItem->shItem_id)){
                    // return "invalid item";
                    // DB::rollBack();
                    $shItemData = array();
                    $shItemData['shItem_shipmentId'] = $shipmentId;
                    $shItemData['shItem_sku'] = $item->shItem_sku;
                    $shItemData['shItem_price'] = $item->shItem_price;
                    $shItemData['shItem_qty'] = $item->shItem_qty;

                    $shItemId = DB::table('shipment_items')->insertGetId($shItemData);
                }
                else{
                    $shItemId = $shItem->shItem_id;
                }

                $affected = DB::table('custody')
                ->where("custody_driverId","=",$driverId)
                ->where("custody_sku","=",$item->shItem_sku)
                ->update(['custody_usedQty'=>($itemCustody->custody_usedQty + $item->shItem_qty)]);

                $affected = DB::table('shipment_items')
                ->where("shItem_id","=",$shItemId)
                ->where("shItem_sku","=",$item->shItem_sku)
                ->where("shItem_shipmentId","=",$shipmentId)
                ->update(['shItem_status'=>self::SHIPMENT_ITEM_STATUS_DELIVERED]);
            }

            ////////////////////////////////////////////////////////////////////////////////

            $shipmentdata['shipment_updated'] = date("Y-m-d H:i:s");
            $shipmentdata['shipment_status'] = self::SHIPMENT_STATUS_COMPLETED;
            $shipmentdata['shipment_completeDate'] = date("Y-m-d H:i:s");
            $affected = DB::table('shipments')
            ->where('shipment_driverId', $driverId)
            ->where('shipment_id', $shipmentId)
            ->update($shipmentdata);

            $shipment = self::getShipmentByID($shipmentId);
            AppHelper::smsMisrSend(
                ClientModel::getClientByID($shipment->shipment_clientId),
                $shipment->customerPhone,
                'test completed');

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            return "Error Saving shipment!";
        }
    }

    public static function assignZone($shipmentId, $zoneId)
    {
        try {
                $shipmentdata['shipment_updated'] = date("Y-m-d H:i:s");
                $shipmentdata['shipment_zoneId'] = $zoneId;

                $affected = DB::table('shipments')
                ->where('shipment_id', $shipmentId)
                ->update($shipmentdata);

            return true;
        } catch (Exception $e) {
            return "Error Saving shipment!";
        }
    }

    public static function getShipmentLogs($shipmentId, $userId=0,$userType='', $date='')
    {
        try{
            $query = DB::table("shipment_logs")
                        ->select('*')
                        ->where("log_shipmentId", "=", $shipmentId);

            if (!empty($userId) && !empty($userType))
                $query->where("log_userId",$userId)
                    ->where("log_userType",$userType);

            return $query->get();
        }
        catch(Exception $e){
            return "Error getting logs";
        }
    }

    public static function insertShipmentLog($shipmentId, $userId, $userType, $message)
    {
        try {
            if (empty($shipmentId) || empty($userId) || empty($userType) || empty($message)) return;

            $log = array();
            $log['log_date'] =  date("Y-m-d H:i:s");
            $log['log_userType'] = $userType;
            $log['log_userId'] = $userId;
            $log['log_shipmentId'] = $shipmentId;
            $log['log_message'] = $message;

            DB::table('shipment_logs')->insert($log);

        } catch (Exception $e) {
            return "Error Saving log!";
        }
    }

    public static function clearShipmentLogs($shipmentId)
    {
        try {
            $result = DB::table('shipment_logs')
            ->where('log_shipmentId', $shipmentId)
            ->delete();

        } catch (Exception $e) {
            return "Error Saving region!";
        }
    }

    public static function notifyCustomer( $shipmentId, $message)
    {
        try {

            $shipment = self::getShipmentByID($shipmentId);
            if (empty($shipment->shipment_id) || empty($shipment->shipment_clientId)) return;

            $client = ClientModel::getClientByID($shipment->shipment_clientId);
            if ( empty($client->client_id)) return;


            if (!empty($shipment->shipment_customerPhone)){

                $smsResult = AppHelper::smsMisrSend(
                    $client->client_smsUser,
                    $client->client_smsPassword,
                    $client->client_smsSenderId,
                    $shipment->shipment_customerPhone,$message);

                self::insertShipmentLog($shipmentId,0,'system','SMS sent to '.$shipment->shipment_customerPhone."\nResult: ".$smsResult->code);

                $shipmentdata['shipment_updated'] = date("Y-m-d H:i:s");
                $shipmentdata['shipment_smsStatus'] = self::SHIPMENT_STATUS_CANCELLED;
                DB::table('shipments')
                ->where('shipment_id', $shipmentId)
                ->update($shipmentdata);
            }

            // if (!empty($shipment->shipment_customerEmail)){


            //     self::insertShipmentLog($shipmentId,0,'system','Email sent to '.$shipment->shipment_customerEmail);
            // }

            return true;
        } catch (Exception $e) {
            return "Error Saving shipment!";
        }
    }
}

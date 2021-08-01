<?php

namespace App\Models;

use App\Helpers\AppHelper;
use Exception;
use Illuminate\Support\Facades\DB;
use stdClass;

class PlanModel
{

    // public static function getScheduledShipments(\DateTime $date, array $zones=null)
    // {
    //     $query = DB::table(DB::raw(DatabaseViews::shipments_view()))
    //         ->select('*')
    //         ->whereRaw("DATE(shipment_scheduleDate) = '" . $date->format('Y-m-d')."'");

    //     if (!empty($zones) && count($zones) > 0){
    //         $query->whereRaw("IFNULL(shipment_zoneId, 0) IN (". implode(",",$zones) .")");
    //     }

    //     $query->orderBy('shipment_id', 'DESC');

    //     return $query->get();
    // }

    public static function getApprovedShipments(array $zones=null)
    {
        $query = DB::table(DB::raw(DatabaseViews::shipments_view()))
            ->select('*')
            ->where("shipment_status","=",ShipmentModel::SHIPMENT_STATUS_APPROVED);

        if (!empty($zones) && count($zones) > 0){
            $query->whereRaw("IFNULL(shipment_zoneId, 0) IN (". implode(",",$zones) .")");
        }

        $query->orderBy('shipment_customerId', 'DESC');

        return $query->get();
    }

    public static function getNonePlannedShipments()
    {
        $query = DB::table(DB::raw(DatabaseViews::shipments_view()))
            ->select('*')
            ->where("shipment_status","=",1)
            ->where("isPlanned","=","0");

        $query->orderBy('shipment_id', 'DESC');

        return $query->get();
    }

    public static function assignToDriver($driverId, array $shipments)
    {
        try {
            if (empty($driverId) || empty($shipments) || count($shipments) < 0) return "invalid data";

            $shipmentData = array();
            $shipmentData['shipment_driverId'] = $driverId;
            $shipmentData['shipment_planDate'] = date("Y-m-d");

            $query = DB::table('shipments');

            if (!empty($shipments) && count($shipments) > 0){
                $query->whereRaw("IFNULL(shipment_id, 0) IN (". implode(",",$shipments) .")");
            }

            $query->update($shipmentData);

            return true;

        } catch (Exception $e) {
            DB::rollBack();
            return "Error Saving Zone!";
        }
    }

    public static function unAssignShipments(array $shipments)
    {
        try {
            if (empty($shipments) || count($shipments) < 0) return "invalid data";

            $shipmentData = array();
            $shipmentData['shipment_driverId'] = DB::raw('NULL');
            $shipmentData['shipment_planDate'] = DB::raw('NULL');
            $shipmentData['shipment_status'] = ShipmentModel::SHIPMENT_STATUS_APPROVED;

            $query = DB::table('shipments');

            if (!empty($shipments) && count($shipments) > 0){
                $query->whereRaw("IFNULL(shipment_id, 0) IN (". implode(",",$shipments) .")");
            }

            $query->update($shipmentData);

            return true;

        } catch (Exception $e) {
            return "Error Saving Zone!";
        }
    }

    public static function getDriverShippedShipments($driverId){
        $query = DB::table(DB::raw(DatabaseViews::shipments_view()))
        ->select('*')
        ->where("shipment_status","=",ShipmentModel::SHIPMENT_STATUS_SHIPPED)
        ->where("isPlanned","=","1")
        ->where("shipment_driverId","=",$driverId)
        ->orderBy('shipment_zoneId', 'ASC')
        ->orderBy('shipment_id', 'ASC');

        // $query->orderBy('shipment_id', 'DESC');

        return $query->get();
    }

    public static function getPlan(array $drivers=null)
    {
        $query = DB::table(DB::raw(DatabaseViews::shipments_view()))
            ->select('*')
            ->where("shipment_status","=",ShipmentModel::SHIPMENT_STATUS_APPROVED)
            ->where("shipment_status","=",ShipmentModel::SHIPMENT_STATUS_SHIPPED)
            ->orderBy('shipment_zoneId', 'ASC')
            ->orderBy('shipment_id', 'ASC');

        if (!empty($drivers) && count($drivers) > 0){
            $query->whereRaw("IFNULL(shipment_driverId, 0) IN (". implode(",",$drivers) .")");
        }

        $query->orderBy('shipment_customerId', 'DESC');

        return $query->get();
    }

    // public static function getZonePlan($zoneId){
    //     $query = DB::table(DB::raw(DatabaseViews::shipments_view()))
    //     ->select('*')
    //     ->where("shipment_status","=",1)
    //     ->where("isPlanned","=","1")
    //     ->where("shipment_zoneId","=",$zoneId);

    //     // $query->orderBy('shipment_id', 'DESC');

    //     return $query->get();
    // }

}

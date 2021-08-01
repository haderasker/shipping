<?php

namespace App\Models;

use App\Helpers\AppHelper;
use Exception;
use Illuminate\Support\Facades\DB;

class ZoneModel
{

    public static function getAllZones()
    {
        $query = DB::table(DB::raw(DatabaseViews::zones_view()))
            ->select('*')
            ->orderBy('zone_id', 'DESC');

        return $query->get();
    }

    public static function getZoneById($zoneId)
    {
        $query = DB::table(DB::raw(DatabaseViews::zones_view()))
            ->select('*')
            ->where("zone_id", "=", $zoneId)
            ->first();

        if (!empty($query->zone_id)) {
            return $query;
        } else {
            return "Error Getting Customer!";
        }
    }

    public static function detectZone($state, $city)
    {
        if (empty($state) || empty($city)) return null;

        $query = DB::table("zone_regions")
            ->select('*')
            ->where("region_state",$state)
            ->where("region_city",$city);

        return $query->first();

    }

    public static function saveZone(array $zoneData, array $regionsData)
    {
        try {

            $isNew = empty($zoneData['zone_id']) || $zoneData['zone_id'] == 0;

            $zoneId = "";
            if ($isNew) {
                $zoneId = DB::table('zones')->insertGetId($zoneData);
            } else {
                $affected = DB::table('zones')
                ->where('zone_id', $zoneData['zone_id'])
                ->update($zoneData);
                $zoneId = $zoneData['zone_id'];
            }

            if (!empty($regionsData)){
                self::deleteZoneRegions($zoneId);

                foreach($regionsData as $region){
                    $region['region_zoneId'] = $zoneId;
                    DB::table('zone_regions')->insert($region);
                }
            }

            return $zoneId;
        } catch (Exception $e) {
            return "Error Saving Zone!";
        }
    }

    public static function deleteZone($id)
    {
        try{
            $result = DB::table('zones')
                        ->where('zone_id', $id)
                        ->delete();
            return true;
        }
        catch(Exception $e){
            return "Error deleting zone";
        }
    }

    public static function getZoneRegions($zoneId)
    {
        $query = DB::table("zone_regions")
            ->select('*')
            ->where("region_zoneId", "=", $zoneId)
            ->get();

        if (!empty($query)) {
            return $query;
        } else {
            return "Error Getting regions!";
        }
    }

    public static function deleteZoneRegions($zoneId)
    {
        try {
            $result = DB::table('zone_regions')
            ->where('region_zoneId', $zoneId)
            ->delete();

        } catch (Exception $e) {
            return "Error Saving region!";
        }
    }


}

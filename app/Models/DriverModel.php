<?php

namespace App\Models;

use Exception;
use Illuminate\Support\Facades\DB;

class DriverModel
{
    // Read data using username and password
    public static function driverLogin($username, $password)
    {
        $result = DB::table('drivers')
            ->selectRaw("*,(CASE driver_role WHEN 1 THEN 'Admin' WHEN 2 THEN 'Operator' END) AS role_name")
            //				 ->join('lawyer_profile lp', 'u.driver_id = lp.lawyer_userID', 'left')
            ->where("driver_name", "=", $username)
            ->where("driver_password", "=", $password)
            ->where("driver_status", "=", 1)
            ->first();

        if (!empty($result)) {

            $result->login = new \DateTime('now');
            $result->login = $result->login->format('D d - h:i A');
        } else {
            $result = "Invalid Username and/or Password";
        }
        return $result;
    }

    public static function getAllDrivers($status = array())
    {
        $query = DB::table('drivers')
            ->select('*')
            ->orderBy('driver_id', 'DESC');

        if (is_array($status) && count($status) > 0)
            $query->whereIn("driver_status", implode(",", $status));

        return $query->get();
    }

    public static function getDriverByID($driverId)
    {
        $query = DB::table('drivers')
            ->select('*')
            //				 ->join('lawyer_profile lp', 'u.driver_id = lp.lawyer_userID', 'left')
            ->where("driver_id", "=", $driverId)
            ->first();

        if (!empty($query)) {
            return $query;
        } else {
            return "Error Getting Driver!";
        }
    }

    public static function saveDriver(array $driverData)
    {
        try {

            $isNew = empty($driverData['driver_id']) || $driverData['driver_id'] == 0;

            if ($isNew) {
                $query = DB::table('drivers')
                    ->select('driver_id')
                    ->where("driver_name", "=", $driverData['driver_name'])
                    ->first();

                if ($query)
                    return "User already exists!";
            }

            $result = "";
            if ($isNew) {
                $result = DB::table('drivers')->insertGetId($driverData);
            } else {
                $affected = DB::table('drivers')
                ->where('driver_id', $driverData['driver_id'])
                ->update($driverData);
                $result = $driverData['driver_id'];
            }

            return (empty($result) ? "Error Saving User!" : $result);
        } catch (Exception $e) {
            return "Error Saving User!";
        }
    }

    public static function deleteDriver($id)
    {

        $result = DB::table('driver')
            ->where('driver_id', $id)
            ->delete();
        if (!$result) {
            return $result;
        }

        return true;
    }

}

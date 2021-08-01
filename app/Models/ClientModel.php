<?php

namespace App\Models;

use App\Helpers\AppHelper;
use Exception;
use Illuminate\Support\Facades\DB;

class ClientModel
{

    public static function getAllClients($status = array(), $role = 0)
    {
        $query = DB::table(DB::raw(DatabaseViews::clients_view()))
            ->select('*')
            ->orderBy('client_id', 'DESC');

        return $query->get();
    }

    public static function getClientByID($clientId)
    {
        $query = DB::table(DB::raw(DatabaseViews::clients_view()))
            ->select('*')
            //				 ->join('lawyer_profile lp', 'u.user_id = lp.lawyer_userID', 'left')
            ->where("client_id", "=", $clientId)
            ->first();

        if (!empty($query)) {
            return $query;
        } else {
            return "Error Getting Customer!";
        }
    }

    public static function getClientByCode($clientCode)
    {
        $query = DB::table(DB::raw(DatabaseViews::clients_view()))
            ->select('*')
            //				 ->join('lawyer_profile lp', 'u.user_id = lp.lawyer_userID', 'left')
            ->where("client_code", "=", $clientCode)
            ->where("client_status", "=", 1)
            ->first();

        if (!empty($query)) {
            return $query;
        } else {
            return "Error Getting Customer!";
        }
    }

    public static function saveClient(array $clientData)
    {
        try {

            $isNew = empty($clientData['client_id']) || $clientData['client_id'] == 0;

            // if ($isNew) {
            //     $query = DB::table('clients')
            //         ->select('client_id')
            //         ->where("client_code", "=", $clientData['client_code'])
            //         ->first();

            //         $isNew = empty($query);
            // }

            $result = "";
            if ($isNew) {
                $clientData['client_created'] = date("Y-m-d H:i:s");
                $clientData['client_code'] = AppHelper::generateRandomString(6);
                $result = DB::table('clients')->insertGetId($clientData);
            } else {
                $clientData['client_updated'] = date("Y-m-d H:i:s");
                $affected = DB::table('clients')
                ->where('client_id', $clientData['client_id'])
                ->update($clientData);
                $result = $clientData['client_id'];
            }

            return (empty($result) ? "Error Saving Client!" : $result);
        } catch (Exception $e) {
            return "Error Saving Client!";
        }
    }

    public static function deleteClient($id)
    {

        $result = DB::table('clients')
            ->where('client_id', $id)
            ->delete();
        if (!$result) {
            return $result;
        }

        return true;
    }

}

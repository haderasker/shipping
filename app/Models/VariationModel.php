<?php

namespace App\Models;

use Exception;
use Illuminate\Support\Facades\DB;

class VariationModel
{

    // Read data using username and password

    public static function getAllVariations()
    {
        $query = DB::table('variations')
            ->select('*')
            ->orderBy('variation_id', 'DESC');

        return $query->get();
    }

    public static function getVariationByID($catID)
    {
        $query = DB::table('variations')
            ->select('*')
            //				 ->join('lawyer_profile lp', 'u.user_id = lp.lawyer_userID', 'left')
            ->where("variation_id", "=", $catID)
            ->first();

        if (!empty($query)) {
            return $query;
        } else {
            return "Error Getting Variation!";
        }
    }
    public static function insertVariation(array $vardata)
    {
        try {

            if (empty($vardata['variation_name']))
                return "Invalid variation name";

            $query = DB::table('variations')
                ->select('variation_id')
                ->where("variation_name", "=", strtolower($vardata['variation_name']))
                ->first();

            if (!empty($query))return $query->variation_id;

            $result = DB::table('variations')->insertGetId($vardata);

            return (empty($result) ? "Error Saving Variation!" : $result);
        } catch (Exception $e) {
            return "Error Saving Variation!";
        }
    }
    public static function saveVariation(array $vardata)
    {
        try {

            $isNew = empty($vardata['variation_id']) || $vardata['variation_id'] == 0;

            if ($isNew) {
                $query = DB::table('variations')
                    ->select('variation_id')
                    ->where("variation_name", "=", $vardata['variation_name'])
                    ->first();

                if ($query)
                    return "Variation already exists!";
            }

            $result = "";
            if ($isNew) {
                $result = DB::table('variations')->insertGetId($vardata);
            } else {
                $affected = DB::table('variations')
                ->where('variation_id', $vardata['variation_id'])
                ->update($vardata);
                $result = $vardata['variation_id'];
            }

            return (empty($result) ? "Error Saving Variation!" : $result);
        } catch (Exception $e) {
            return "Error Saving Variation!";
        }
    }

    public static function variationInUse($id){
        $query = DB::table('product_variations')
                ->select(DB::raw('COUNT(*) AS c'))
                ->where("iv_variationId", "=", $id)
                ->first();

        return !empty($query->c);

    }

    public static function deleteVariation($id)
    {

        if (self::variationInUse($id)){
            return "Variation in use!";
        }

        $result = DB::table('variations')
            ->where('variation_id', $id)
            ->delete();
        // if (!$result) {
        //     return $result;
        // }

        return true;
    }

    //	public function getLawyerByMail($userMail){
    //		$this->db->select('*')
    //		         DB::table('lw_account')
    ////				 ->join('lawyer_profile lp', 'u.user_id = lp.lawyer_userID', 'left')
    //                 ->where("lawyer_mail='".$userMail."'")
    //		         ->limit(1);
    //		$query = $this->db->get();
    //
    //		if ($query->num_rows() == 1) {
    //			return $query->result()[0];
    //		} else {
    //			return false;
    //		}
    //	}
    //	public function getLawyerByQR($qrCode) {
    //		$this->db->select('*')
    //		         DB::table('lw_account')
    ////				 ->join('lawyer_profile lp', 'u.user_id = lp.lawyer_userID', 'left')
    //                 ->where("lawyer_qr='".$qrCode."'")
    //		         ->limit(1);
    //		$query = $this->db->get();
    //
    //		if ($query->num_rows() == 1) {
    //			return $query->result()[0];
    //		} else {
    //			return false;
    //		}
    //	}
    //	public function setLawyerToken($userID,$token) {
    //		$data = array(
    //			'lawyer_resetToken' => $token,
    //			'lawyer_resetTime'  => NOW()
    //		);
    //		$this->db->where('lawyer_id', $userID);
    //		return $this->db->update( 'lw_account', $data );
    //	}
    //	public function setLawyerQR($userID,$qrCode) {
    //		$data = array(
    //			'lawyer_qr' => $qrCode
    //		);
    //		$this->db->where('lawyer_id', $userID);
    //		return $this->db->update( 'lw_account', $data );
    //	}

}

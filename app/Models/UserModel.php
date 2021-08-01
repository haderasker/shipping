<?php

namespace App\Models;

use Exception;
use Illuminate\Support\Facades\DB;

class UserModel
{

    // Read data using username and password
    public static function userLogin($username, $password)
    {
        $result = DB::table('users')
            ->selectRaw("*,(CASE user_role WHEN 1 THEN 'Admin' WHEN 2 THEN 'Operator' END) AS role_name")
            //				 ->join('lawyer_profile lp', 'u.user_id = lp.lawyer_userID', 'left')
            ->where("user_name", "=", $username)
            ->where("user_password", "=", $password)
            ->where("user_status", "=", 1)
            ->first();

        if (!empty($result)) {

            $result->login = new \DateTime('now');
            $result->login = $result->login->format('D d - h:i A');
        } else {
            $result = "Invalid Username and/or Password";
        }
        return $result;
    }

    public static function getAllUsers($status = array(), $role = 0)
    {
        $query = DB::table('users')
            ->select('*')
            ->orderBy('user_id', 'DESC');

        if (is_array($status) && count($status) > 0)
            $query->whereIn("user_status", implode(",", $status));

        if ($role > 0)
            $query->where("user_role", "=", $role);

        return $query->get();
    }

    public static function getUserByID($userID)
    {
        $query = DB::table('users')
            ->select('*')
            //				 ->join('lawyer_profile lp', 'u.user_id = lp.lawyer_userID', 'left')
            ->where("user_id", "=", $userID)
            ->first();

        if (!empty($query)) {
            return $query;
        } else {
            return "Error Getting User!";
        }
    }

    public static function saveUser(array $userdata)
    {
        try {

            $isNew = empty($userdata['user_id']) || $userdata['user_id'] == 0;

            if ($isNew) {
                $query = DB::table('users')
                    ->select('user_id')
                    ->where("user_name", "=", $userdata['user_name'])
                    ->first();

                if ($query)
                    return "User already exists!";
            }

            $result = "";
            if ($isNew) {
                $result = DB::table('users')->insertGetId($userdata);
            } else {
                $affected = DB::table('users')
                ->where('user_id', $userdata['user_id'])
                ->update($userdata);
                $result = $userdata['user_id'];
            }

            return (empty($result) ? "Error Saving User!" : $result);
        } catch (Exception $e) {
            return "Error Saving User!";
        }
    }

    public static function deleteUser($id)
    {

        $result = DB::table('users')
            ->where('user_id', $id)
            ->delete();
        if (!$result) {
            return $result;
        }

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

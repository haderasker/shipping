<?php

namespace App\Models;

use Exception;
use Illuminate\Support\Facades\DB;

class CustomerModel
{

    public static function getAllCustomers($status = array(), $role = 0)
    {
        $query = DB::table(DB::raw(DatabaseViews::customers_view()))
            ->select('*')
            ->orderBy('customer_id', 'DESC');

        return $query->get();
    }

    public static function getCustomerByID($customerID)
    {
        $query = DB::table(DB::raw(DatabaseViews::customers_view()))
            ->select('*')
            //				 ->join('lawyer_profile lp', 'u.user_id = lp.lawyer_userID', 'left')
            ->where("customer_id", "=", $customerID)
            ->first();

        if (!empty($query)) {
            return $query;
        } else {
            return "Error Getting Customer!";
        }
    }

    // public static function getOrCreateCustomer(array $customerData)
    // {
    //     try {

    //         $query = DB::table('customers')
    //                 ->select('customer_id')
    //                 ->where("customer_name" , "=", $customerData['customer_name'])
    //                 ->where("customer_phone" , "=", $customerData['customer_phone'])
    //                 ->first();

    //         if (!empty($query))return $query->customer_id;

    //         $customerData['customer_created'] = date("Y-m-d H:i:s");
    //         $result = DB::table('customers')->insertGetId($customerData);

    //         if  (empty($result)){
    //             //rollbacl
    //             return "Error Saving Customer!";
    //         }

    //         return $result;

    //     } catch (Exception $e) {
    //         return "Error Saving Customer!";
    //     }
    // }

    public static function saveCustomerById(array $customerData)
    {
        $isNew = empty($customerData['customer_id']) || $customerData['customer_id'] == 0;

        return self::saveCustomer($customerData, $isNew);
    }

    public static function saveCustomerByEmail(array $customerData)
    {
        $isNew = empty($customerData['customer_email']);

        if (!$isNew) {
            $query = DB::table('customers')
                ->select('customer_id')
                ->where("customer_email", "=", $customerData['customer_email'])
                ->first();

            if (empty($query->customer_id)){
                $isNew = true;
            }
            else{
                $isNew = false;
                $customerData['customer_id'] = $query->customer_id;
            }
        }

        return self::saveCustomer($customerData,$isNew);
    }

    public static function saveCustomerByRef(array $customerData)
    {
        $isNew = empty($customerData['customer_ref']) || $customerData['customer_ref'] == 0;

        if (!$isNew) {
            $query = DB::table('customers')
                ->select('customer_id')
                ->where("customer_ref", "=", $customerData['customer_ref'])
                ->first();

            if (empty($query->customer_id)){
                $isNew = true;
            }
            else{
                $isNew = false;
                $customerData['customer_id'] = $query->customer_id;
            }
        }

        return self::saveCustomer($customerData,$isNew);
    }

    private static function saveCustomer(array $customerData, $isNew)
    {
        try {
            $result = "";
            if ($isNew) {
                $customerData['customer_created'] = date("Y-m-d H:i:s");
                $result = DB::table('customers')->insertGetId($customerData);
            } else {
                $customerData['customer_updated'] = date("Y-m-d H:i:s");
                $affected = DB::table('customers')
                ->where('customer_id', $customerData['customer_id'])
                ->update($customerData);
                $result = $customerData['customer_id'];
            }

            return (empty($result) ? "Error Saving Customer!" : $result);
        } catch (Exception $e) {
            return "Error Saving Customer!";
        }
    }

    public static function deleteCustomer($id)
    {

        $result = DB::table('customers')
            ->where('customer_id', $id)
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

<?php

namespace App\Helpers;

use App\Models\ShipmentModel;
use PhpParser\Node\Stmt\Break_;

use function PHPUnit\Framework\returnSelf;

class AppHelper
{

    public static function createMessage($msg,$type){

        return '<div class="alert alert-'.strtolower($type).' mt-1 alert-validation-msg" role="alert">
                    <div class="alert-body">
                        <i data-feather="info" class="mr-50 align-middle"></i>
                        <span>'.$msg.'</span>
                    </div>
                </div>';
    }

    public static function getShipmentStatusLabel($status){
        if (empty($status))
            $status = 0;

        $statusLabel="";
        switch ($status) {
            case ShipmentModel::SHIPMENT_STATUS_PENDING:
                $statusLabel = '<div class="badge badge-pill badge-light-warning">Pending</div>';
                break;
            // case 1:
            //     $statusLabel = '<div class="badge badge-pill badge-light-secondary">Scheduled</div>';
            //     break;
            case ShipmentModel::SHIPMENT_STATUS_APPROVED:
                $statusLabel = '<div class="badge badge-pill badge-light-primary">Approved</div>';
                break;
            case ShipmentModel::SHIPMENT_STATUS_SHIPPED:
                $statusLabel = '<div class="badge badge-pill badge-light-secondary">Shipped</div>';
                break;
            case ShipmentModel::SHIPMENT_STATUS_COMPLETED:
                $statusLabel = '<div class="badge badge-pill badge-light-success">Completed</div>';
                break;
            // case 3:
            //     $statusLabel = '<div class="badge badge-pill badge-light-success">Delivered</div>';
            //     break;
            // case 4:
            //     $statusLabel = '<div class="badge badge-pill badge-light-danger">Returned</div>';
            //     break;
            case ShipmentModel::SHIPMENT_STATUS_CANCELLED:
                $statusLabel = '<div class="badge badge-pill badge-light-danger">Cancelled</div>';
                break;
        }
        return $statusLabel;
    }

    public static function getItemStatusLabel($status){
        if (empty($status))
            $status = 0;

        $statusLabel="";
        switch ($status) {
            case ShipmentModel::SHIPMENT_ITEM_STATUS_NONE:
                $statusLabel = '<div class="badge badge-pill badge-light-secondary">None</div>';
                break;
            // case ShipmentModel::SHIPMENT_ITEM_STATUS_WITHDRAW:
            //     $statusLabel = '<div class="badge badge-pill badge-light-warning">Withdraw</div>';
            //     break;
            // case ShipmentModel::SHIPMENT_ITEM_STATUS_OUTSTOCK:
            //     $statusLabel = '<div class="badge badge-pill badge-light-secondary">Out Of Stock</div>';
            //     break;
            case ShipmentModel::SHIPMENT_ITEM_STATUS_DELIVERED:
                $statusLabel = '<div class="badge badge-pill badge-light-success">Delivered</div>';
                break;
            case ShipmentModel::SHIPMENT_ITEM_STATUS_RETURNED:
                $statusLabel = '<div class="badge badge-pill badge-light-danger">Returned</div>';
                break;
        }
        return $statusLabel;
    }

    public static function setItemImage($item){
        $image = url('')."/img/empty.jpg";

        if (!empty($item->item_id)){
            $imgPath = "img/items/".$item->item_id;
            if (file_exists(public_path($imgPath)))
                $image = url('')."/".$imgPath;
        }

        $item->item_image = $image;
    }

    public static function smsMisrSend($user,$password,$senderId, $mobile, $message)
    {

        if (empty($user) ||
            empty($password) ||
            empty($senderId) ||
            empty($mobile) ||
            empty($message) ) return false;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,"https://smsmisr.com/api/webapi");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "username=$user&password=$password&sender=$senderId&mobile=$mobile&message=$message&language=1");

        // In real life you should use something like:
        // curl_setopt($ch, CURLOPT_POSTFIELDS,
        //          http_build_query(array('postvar1' => 'value1')));

        // Receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close ($ch);


        $server_output = json_decode($server_output);

        return $server_output;

    }
    public static function canView($module)
    {
        $role = session('login_info')->user_role;
        if (empty($role) || $role == 0) return false;
        if ($role == 1) return true;

        switch(strtolower($module)){
            case 'report':
                switch ($role) {
                    case 2:
                        return false;
                    case 3:
                        return false;
                }
                break;
            case 'shipment':
                switch ($role) {
                    case 2:
                        return true;
                    case 3:
                        return true;
                }
                break;
            case 'item':
                switch ($role) {
                    case 2:
                        return false;
                    case 3:
                        return false;
                }
                break;
            case 'variation':
                switch ($role) {
                    case 2:
                        return false;
                    case 3:
                        return false;
                }
                break;
            case 'customer':
                switch ($role) {
                    case 2:
                        return false;
                    case 3:
                        return false;
                }
                break;
            default:
                return false;
        }

    }

    public static function canEdit($module)
    {
        if (!self::canView($module)) return false;

        $role = session('login_info')->user_role;
        if (empty($role) || $role == 0) return false;
        if ($role == 1) return true;

        switch(strtolower($module)){
            case 'shipment':
                switch ($role) {
                    case 2:
                        return false;
                    case 3:
                        return false;
                }
                break;
            case 'item':
                switch ($role) {
                    case 2:
                        return false;
                    case 3:
                        return false;
                }
                break;
            case 'variation':
                switch ($role) {
                    case 2:
                        return false;
                    case 3:
                        return false;
                }
                break;
            case 'customer':
                switch ($role) {
                    case 2:
                        return false;
                    case 3:
                        return false;
                }
                break;
            default:
                return false;
        }

    }

    public static function canDelete($module)
    {
        if (!self::canView($module)) return false;

        $role = session('login_info')->user_role;
        if (empty($role) || $role == 0) return false;
        if ($role == 1) return true;

        switch(strtolower($module)){
            case 'shipment':
                switch ($role) {
                    case 2:
                        return false;
                    case 3:
                        return false;
                }
                break;
            case 'item':
                switch ($role) {
                    case 2:
                        return false;
                    case 3:
                        return false;
                }
                break;
            case 'variation':
                switch ($role) {
                    case 2:
                        return false;
                    case 3:
                        return false;
                }
                break;
            case 'customer':
                switch ($role) {
                    case 2:
                        return false;
                    case 3:
                        return false;
                }
                break;
            default:
                return false;
        }

    }

    public static function encrypt($string)
    {

        // Store the cipher method
        $ciphering = "AES-128-CTR";

        // Use OpenSSl Encryption method
        // $iv_length = openssl_cipher_iv_length($ciphering);
        $options = 0;

        // Non-NULL Initialization Vector for encryption
        $encryption_iv = '1234567891011121';

        // Store the encryption key
        $encryption_key = env('APP_KEY');

        // Use openssl_encrypt() function to encrypt the data
        $encryption = openssl_encrypt($string, $ciphering, $encryption_key, $options, $encryption_iv);

        // Display the encrypted string
        return $encryption;

    }

    public static function decrypt($string)
    {
        // Store the cipher method
        $ciphering = "AES-128-CTR";

        // Use OpenSSl Encryption method
        // $iv_length = openssl_cipher_iv_length($ciphering);
        $options = 0;

        // Non-NULL Initialization Vector for decryption
        $decryption_iv = '1234567891011121';

        // Store the decryption key
        $decryption_key = env('APP_KEY');

        // Use openssl_decrypt() function to decrypt the data
        $decryption=openssl_decrypt ($string, $ciphering, $decryption_key, $options, $decryption_iv);

        // Display the decrypted string
        return $decryption;

    }

    public static function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
?>

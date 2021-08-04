<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\ClientModel;
use App\Models\CustomerModel;
use App\Models\DefinitionModel;
use App\Models\ItemModel;
use App\Models\ShipmentModel;
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Support\Facades\Storage;
use Facade\FlareClient\Stacktrace\File as StacktraceFile;
use Faker\Provider\File as ProviderFile;
use File;
use stdClass;

use function PHPUnit\Framework\returnSelf;

class WoocommerceController extends BaseController {

    private function saveOrder($payload, $clientCode, $status = -1){
        dd($payload);

        $client = ClientModel::getClientByCode($clientCode);
        if (empty($client->client_id)){
            return "invalid Client";
        }

        /////////////////////////////////////////////////////////
        $customer = array();

        $customer['customer_ref'] = $payload->customer_id;

        $customer['customer_name'] = empty($payload->shipping->first_name) ? $payload->billing->first_name : $payload->shipping->first_name;
        $customer['customer_name'] .= ' ';
        $customer['customer_name'] .= empty($payload->shipping->last_name) ? $payload->billing->last_name : $payload->shipping->last_name;

        $customer['customer_city'] = empty($payload->shipping->city) ? $payload->billing->city : $payload->shipping->city;
        $customer['customer_state'] = empty($payload->shipping->state) ? $payload->billing->state : $payload->shipping->state;
        $customer['customer_postcode'] = empty($payload->shipping->postcode) ? $payload->billing->postcode : $payload->shipping->postcode;
        $customer['customer_country'] = empty($payload->shipping->country) ? $payload->billing->country : $payload->shipping->country;
        $customer['customer_address'] = empty($payload->shipping->address_1) ? $payload->billing->address_1 : $payload->shipping->address_1;
        $customer['customer_remark'] = empty($payload->shipping->address_2) ? $payload->billing->address_2 : $payload->shipping->address_2;

        $customer['customer_email'] = $payload->billing->email;
        $customer['customer_phone'] = $payload->billing->phone;

        $customerId = CustomerModel::saveCustomerByEmail($customer);

        if (!is_numeric($customerId))
            return $customerId;

        /////////////////////////////////////////////////////////

        $shipment = array();
        // $shipment['shipment_id'];
        $shipment['shipment_ref'] = $payload->id;
        // $shipment['shipment_date'] = $payload->date_modified;
        $shipment['shipment_key'] = $payload->order_key;

        if ($status >= 0)
            $shipment['shipment_status'] = $status; //ShipmentModel::SHIPMENT_STATUS_PENDING;

        $shipment['shipment_fees'] = $payload->shipping_total;

        $paymentMethod = DefinitionModel::getPaymentMethodByCode($payload->payment_method);
        if (!empty($paymentMethod->method_id)){
            $shipment['shipment_paymentMethodId'] = $paymentMethod->method_id;
        }

        $shipment['shipment_notes'] = $payload->customer_note;
        $shipment['shipment_clientId'] = $client->client_id;
        $shipment['shipment_customerId'] = $customerId;
        $shipment['shipment_customerCity'] = $customer['customer_city'];
        $shipment['shipment_customerState'] = $customer['customer_state'];
        $shipment['shipment_customerPostcode'] = $customer['customer_postcode'];
        $shipment['shipment_customerCountry'] = $customer['customer_country'];
        $shipment['shipment_customerAddress'] = $customer['customer_address'];
        $shipment['shipment_customerRemark'] = $customer['customer_remark'];
        $shipment['shipment_customerEmail'] = $customer['customer_email'];
        $shipment['shipment_customerPhone'] = $customer['customer_phone'];

        $shipmentId = ShipmentModel::saveShipmentByRef($shipment);

        if (!is_numeric($shipmentId))
            return $shipmentId;
        /////////////////////////////////////////////////////////////////////

        foreach($payload->line_items as $i ){
            //save item
            $item = [
                'item_clientId'=>$client->client_id,
                'item_code'=>$i->product_id,
                'item_ref'=>$i->product_id,
                'item_name'=>$i->name,
                'item_sku'=>(empty($i->sku)?$i->product_id:$i->sku),
                'item_price'=>$i->total,
            ];
            $itemId = ItemModel::insertItem($item);
            if (!is_numeric($itemId))
                return $itemId;

                //save shipment item
            $shItem = [
                'shItem_shipmentId'=>$shipmentId,
                'shItem_sku'=>$item['item_sku'],
                'shItem_price'=>$i->total,
                'shItem_qty'=>$i->quantity,
            ];
            $shItemId = ShipmentModel::saveShipmentItem($shItem);
            if (!is_numeric($shItemId))
                return $shItemId;
        }
    }

    public function order_create(Request $request)
    {
        if (empty($_GET['client']))
        return 'invalid client';

        $payload = file_get_contents('php://input');
        if (empty($payload))
        return 'invalid payload';

        // echo ($payload);
        // Storage::put('json.json', $payload);

        $payload = json_decode($payload);

        if ($payload->status != "processing" && $payload->status != "team-order") return;

        //////////////////////////////////////////////////////////

        // if ($payload->status == '' || $payload->status == ''){
        //     return "Paypassed";
        // }


        $shipmentId = $this->saveOrder($payload, $_GET['client'], ShipmentModel::SHIPMENT_STATUS_PENDING);

        /////////////////////////////////////////////////////////////////////

        return $shipmentId;

        Storage::put('json.json', json_encode($payload->line_items));
    }

    public function order_update(Request $request)
    {
        if (empty($_GET['client']))
        return 'invalid client';

        $payload = file_get_contents('php://input');
        if (empty($payload))
        return 'invalid payload';

        // echo ($payload);
        Storage::put('json.json', $payload);

        $payload = json_decode($payload);

        //if ($payload->status != "processing" && $payload->status != "team-order") return;

        //////////////////////////////////////////////////////////

        // if ($payload->status == '' || $payload->status == ''){
        //     return "Paypassed";
        // }


        $shipmentId = $this->saveOrder($payload, $_GET['client']);

        /////////////////////////////////////////////////////////////////////

        return $shipmentId;

    }

    public function order_delete(Request $request)
    {
        if (empty($_GET['client']))
        return 'invalid client';

        $payload = file_get_contents('php://input');
        if (empty($payload))
        return 'invalid payload';

        // echo ($payload);
        // Storage::put('json.json', $payload);

        $payload = json_decode($payload);

        ShipmentModel::deleteShipmentByRef($payload->id ,$_GET['client']);
    }

}

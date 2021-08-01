<?php
namespace App\Http\Controllers\web;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Support\Facades\DB;
use App\Models\ShipmentModel;
use App\Models\CustomerModel;
use App\Models\ItemModel;

use App\Helpers\AppHelper;
use App\Helpers\SimpleXLS;
use App\Helpers\SimpleXLSX;
use App\Models\ClientModel;
use App\Models\DefinitionModel;
use App\Models\ZoneModel;
use DateTime;
use stdClass;

class ShipmentController extends BaseAppController {

	public function index(Request $request)
	{
        $data = null;

        if (!empty($_GET['range']) && strpos($_GET['range'], '|') !== false){
            $range = explode('|',$_GET['range']);

            $data = ShipmentModel::getAllShipments(0, $range[0],$range[1]);
        }
        else{
            $data = ShipmentModel::getAllShipments();
        }

		return view('shipments',array('data'=>$data));
	}
    public function pending(Request $request)
	{
        $followup = 0;
        if (isset($_GET['followup'])){
            if ($_GET['followup'] == '1')
                $followup = ShipmentModel::SHIPMENT_TYPE_NEW;
            elseif ($_GET['followup'] == '2')
                $followup = ShipmentModel::SHIPMENT_TYPE_FOLLOWUP;
        }
        else{
            $followup = ShipmentModel::SHIPMENT_TYPE_NEW;
        }

        $zones = ZoneModel::getAllZones();
		$data = ShipmentModel::getAllShipments(ShipmentModel::SHIPMENT_STATUS_PENDING, $followup);
		return view('shipments_pending',array('zones'=>$zones,'data'=>$data));
	}
    // public function scheduled(Request $request)
	// {
	// 	$data = ShipmentModel::getAllShipments(1);
	// 	return view('shipments_scheduled',array('data'=>$data));
	// }
    public function approved(Request $request)
	{
		$data = ShipmentModel::getAllShipments(ShipmentModel::SHIPMENT_STATUS_APPROVED);
		return view('shipments_approved',array('data'=>$data));
	}
    public function shipped(Request $request)
	{
		$data = ShipmentModel::getAllShipments(2);
		return view('shipments_shipped',array('data'=>$data));
	}
    // public function delivered(Request $request)
	// {
	// 	$data = ShipmentModel::getAllShipments(3);
	// 	return view('shipments_delivered',array('data'=>$data));
	// }
    // public function returned(Request $request)
	// {
	// 	$data = ShipmentModel::getAllShipments(4);
	// 	return view('shipments_returned',array('data'=>$data));
	// }
    public function completed(Request $request)
	{
		$data = ShipmentModel::getAllShipments(3);
		return view('shipments_completed',array('data'=>$data));
	}
    public function cancelled(Request $request)
	{
		$data = ShipmentModel::getAllShipments(4);
		return view('shipments_cancelled',array('data'=>$data));
	}

	public function view(Request $request, $id=0)
	{
		$result = null;
		$shipment = array();

        if ($id > 0){
            $shipment = ShipmentModel::getShipmentByID($id);
        }
        $payMethods = DefinitionModel::getAllPaymentMethods();
        $clients = ClientModel::getAllClients();
        $customers = CustomerModel::getAllCustomers();
        $zones = ZoneModel::getAllZones();
        // $items = ItemModel::getAllItems();

		return view('edit_shipment',array('message'=>$result,
                'shipment'=>$shipment,
                'payMethods'=>$payMethods,
                'clients'=>$clients,
                'customers'=>$customers,
                'zones'=>$zones));
	}

    public function followup(Request $request){
        if (empty($_POST['date']))
        return "invalid date!";

        if (empty($_POST['shipments']))
        return "Please select at least one shipment!";

        $date = $_POST['date'];
        $shipments = $_POST['shipments'];

        foreach($shipments as $id){
            ShipmentModel::followupShipment($id,new DateTime($date));
        }

        return "true";
    }

    public function cancel(Request $request){

        if (empty($_POST['shipments']))
        return "Please select at least one shipment!";

        foreach($_POST['shipments'] as $id){
            ShipmentModel::cancelShipment($id);
        }

        return "true";
    }

    public function approve(Request $request){

        if (empty($_POST['shipments']))
        return "Please select at least one shipment!";

        foreach($_POST['shipments'] as $id){
            ShipmentModel::approveShipment($id);
        }

        return "true";
    }

    public function assignZone(Request $request){

        if (empty($_POST['shipmentId']) || empty($_POST['zoneId']))
        return "Invalid request!";

        $result = ShipmentModel::assignZone($_POST['shipmentId'], $_POST['zoneId']);

        if (is_string($result))
        return $result;
        else
        return "true";
    }

    public function import(Request $request){
        $manifest = $request->file('manifest');

        if (strtolower($manifest->extension()) == "xlsx")
            $manifest = SimpleXLSX::parse($manifest->path());
        else
            $manifest = SimpleXLS::parse($manifest->path());

        if (!$manifest) {
            echo SimpleXLSX::parseError();
            exit();
        }

        DB::beginTransaction();

        for ($i = 1; $i < count($manifest->rows()); $i++){
            $r = $manifest->rows()[$i];

            $sku = empty($r[12])?$r[10]:$r[12];

            if (empty($r[11]) || empty($sku))//item name and sku
                continue;

            if (empty(ItemModel::getItemBySku($sku)->item_id)){
                DB::rollBack();
                print "Item ".$r[11]." with SKU ".$sku." doesn't exists!";
                exit();
            }

            $customer = array();
            $customer['customer_name'] = $r[41];
            $customer['customer_phone'] = $r[42];
            $customer['customer_address'] = $r[43];
            $customer['customer_town'] = $r[44];
            $customer['customer_district'] = $r[45];
            $customer['customer_area'] = $r[46];
            $customer['customer_state'] = $r[47];
            $customer['customer_country'] = $r[48];
            $customer['customer_zipcode'] = $r[49];
            $customer['customer_remark'] = $r[50];

            $customerResult = CustomerModel::insertCustomer($customer);
            if (!is_numeric($customerResult)){
                DB::rollBack();
                print $customerResult;
                exit();
            }

            $shipment = array();
            $shipment['shipment_customerId'] = $customerResult;
            $shipment['shipment_ref'] = $r[0];
            $shipment['shipment_trackingNo'] = $r[3];
            $shipment['shipment_date'] = $r[8];
            $shipment['shipment_itemCount'] = $r[22];
            $shipment['shipment_total'] = $r[38];
            $shipment['shipment_status'] = 1; //pending
            $shipment['shipment_notes'] = $r[52];

            $shipmentResult = ShipmentModel::insertShipment($shipment);
            if (!is_numeric($shipmentResult)){
                DB::rollBack();
                print $shipmentResult;
                exit();
            }

            $shipmentItem = array();
            $shipmentItem['op_shipmentId'] = $shipmentResult;
            $shipmentItem['op_sku'] = $sku;
            $shipmentItem['op_price'] = $r[14];
            $shipmentItem['op_dealPrice'] = $r[15];
            $shipmentItem['op_qty'] = $r[16];

            $itemResult = ShipmentModel::insertShipmentItem($shipmentItem);
            if (!is_numeric($itemResult)){
                DB::rollBack();
                print $itemResult;
                exit();
            }

        }

        DB::commit();
        return "true";

    }

	public function save(Request $request){

        if (!AppHelper::canEdit('shipment')){
            print 'You are not permitted for this operation';
            exit();
        }

        // if (empty($_POST['orderRef'])) {
        //     print "Please enter order referance";
        //     exit();
        // }

        if (empty($_POST['client'])) {
            print "You select a client";
            exit();
        }
        if (empty($_POST['customer'])) {
            print "You must select a customer";
            exit();
        }
        if (empty($_POST['items'])) {
            print "You must add at least one item";
            exit();
        }

        $shipment = array();

        if (!empty($_POST['shipment-id']))
            $shipment['shipment_id'] =$_POST['shipment-id'];

        // $shipment['shipment_ref'] = $_POST['shipmentRef'];
        // $shipment['shipment_trackingNo'] = $_POST['tracking'];

        if (!empty($_POST['fdate']))
        $shipment['shipment_followupDate'] = $_POST['fdate'];

        // if (!empty($_POST['dimensions']))
        $shipment['shipment_dimensions'] = (empty($_POST['dimensions'])?0: $_POST['dimensions']);

        // if (!empty($_POST['weight']))
        $shipment['shipment_weight'] = (empty($_POST['weight'])?0:$_POST['weight']);

        // if (!empty($_POST['fees']))
        $shipment['shipment_fees'] = (empty($_POST['fees'])?0:$_POST['fees']);

        // if (!empty($_POST['paymethod']))
        $shipment['shipment_paymentMethodId'] = (empty($_POST['paymethod'])?0:$_POST['paymethod']);

        // if (!empty($_POST['client']))
        $shipment['shipment_clientId'] = (empty($_POST['client'])?0:$_POST['client']);

        // if (!empty($_POST['customer']))
        $shipment['shipment_customerId'] = (empty($_POST['customer'])?0:$_POST['customer']);

        // if (!empty($_POST['phone']))
        $shipment['shipment_customerPhone'] = (empty($_POST['phone'])?'':$_POST['phone']);

        // if (!empty($_POST['postcode']))
        $shipment['shipment_customerPostcode'] = (empty($_POST['postcode'])?'':$_POST['postcode']);

        // if (!empty($_POST['state']))
        $shipment['shipment_customerState'] = (empty($_POST['state'])?'':$_POST['state']);

        // if (!empty($_POST['city']))
        $shipment['shipment_customerCity'] = (empty($_POST['city'])?'':$_POST['city']);

        // if (!empty($_POST['address']))
        $shipment['shipment_customerAddress'] = (empty($_POST['address'])?'':$_POST['address']);

        // if (!empty($_POST['remark']))
        $shipment['shipment_customerRemark'] = (empty($_POST['remark'])?'':$_POST['remark']);

        // if (!empty($_POST['notes']))
        $shipment['shipment_notes'] = (empty($_POST['notes'])?'':$_POST['notes']);

        // if (!empty($_POST['status']))
        $shipment['shipment_status'] = (empty($_POST['status'])?0:$_POST['status']);

        $shipmentResult = ShipmentModel::saveShipmentById($shipment);
        if (!is_numeric($shipmentResult)){
            return $shipmentResult;
            exit();
        }

        foreach($_POST['items'] as $p){
            $arr =explode("|", $p);

            $item = array();
            $item['shItem_shipmentId'] = $shipmentResult;
            $item['shItem_sku'] = $arr[0];
            $item['shItem_price'] = $arr[1];
            $item['shItem_qty'] = $arr[2];

            // if ($shipment['shipment_status'] <= ShipmentModel::SHIPMENT_STATUS_APPROVED)
            //     $item['shItem_status'] = ShipmentModel::SHIPMENT_ITEM_STATUS_NONE;

            if (!is_numeric($item['shItem_qty'])){
                return "You must add item price and QTY";
                exit();
            }

            $itemResult = ShipmentModel::saveShipmentItem($item);
            if (!is_numeric($itemResult)){
                return $itemResult;
                exit();
            }
        }

        return "true";

	}

	////////////////////AJAX///////////////////////////

	public function delete(Request $request, $id){

        if (!AppHelper::canDelete('shipment')){
            print 'You are not permitted for this operation';
            exit();
        }

		$result = CustomerModel::deleteCustomer($id);

		if ($result !== true){
			print $result;
			exit();
		}

		print "true";
	}


//	public function edit($id)
//	{
//
//		$this->load->view('employee/template_main',array('template'=>'employee/edit_employee', 'employeeName'=>$days));
//	}
//
//	public function save()
//	{
//
//
//		$data["user_name"] = $_POST['full_name');
//		$data["user_email"] = $_POST['email');
//		$data["user_password"] = $_POST['password');
//		$data["user_timezone"] = $_POST['timezone');
//		$data["user_country"] = $_POST['country');
//		$data["user_status"] = $_POST['status');
//
//		$result = EmployeeModel::saveEmployee($data);
//
//
//
//		$this->load->view('employee/template_main',array('template'=>'employee/edit_employee','message'=>$result,'data'=>$data));
//	}

}

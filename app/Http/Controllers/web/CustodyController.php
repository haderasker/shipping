<?php
namespace App\Http\Controllers\web;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Routing\Controller as BaseController;

use App\Helpers\AppHelper;
use App\Models\DriverModel;
use App\Models\CustodyModel;
use App\Models\CustomerModel;
use stdClass;

class CustodyController extends BaseAppController {

	public function index(Request $request)
	{
		// $data = PlanModel::getAllCustody();
		// return view('custody',array('data'=>$data));
        return abort(404);
	}

    public function withdrawal(Request $request)
	{
		$drivers = DriverModel::getAllDrivers();
		return view('custody_withdrawal',array('drivers'=>$drivers));
	}

    public function adjustment(Request $request)
	{
		$data = CustodyModel::getTotalCustodyAndCash();
		return view('custody_adjustment',array('data'=>$data));
	}

	public function view_adjust(Request $request, $id=0)
	{
		$result = null;
		$cash = array();
        $items = array();
        $driver = array();

        if (!AppHelper::canView('user')){
            $result = 'You are not permitted for this operation';
        }
        else{
            if ($id > 0){
                $driver = DriverModel::getDriverByID($id);
                if (!empty($driver->driver_id)){
                    $cash = CustodyModel::getDriverCash($id);
                    $items = CustodyModel::getDriverCustody($id);
                }
                // $custody = is_object($result)?$result:null;
                // $result = is_string($result)?$result:null;
            }
        }

		return view('edit_custody_adjust',array('message'=>$result,
                                                'driver'=>$driver,
                                                'cash'=>$cash,
                                                'items'=>$items));
	}

    public function getDriverItemTotals(Request $request, $id=0){
        $data = CustodyModel::getDriverRequiredItems($id);
        return $data;
    }

    public function withdraw(Request $request){
        // $data = CustodyModel::getDriverCustodyItemTotals($id);

        if (empty($_POST['driverId']) || empty($_POST['items']) || count($_POST['items']) <= 0)
            return "invalid request";

        foreach($_POST['items'] as $item){

            $itemArr = explode("|", $item);
            if (empty($itemArr[0])) return "invalid request";

            if (empty($itemArr[1]) || $itemArr[1] <= 0)  continue;

            $result = CustodyModel::withdrawItem($_POST['driverId'],$itemArr[0],$itemArr[1]);

            if ($result !== true)
                return $result;
        }

        CustodyModel::checkCustodyWithdrawed($_POST['driverId']);

        return "true";
    }

    public function adjustCash(Request $request){
        if (empty($_POST['driverId']) || empty($_POST['shipments']) || count($_POST['shipments']) <= 0)
        return "invalid request";

        foreach($_POST['shipments'] as $shipment){

            $shipmentArr = explode("|", $shipment);
            if (empty($shipmentArr[0])) return "invalid request";

            if (empty($shipmentArr[1]) || $shipmentArr[1] <= 0)  continue;

            $result = CustodyModel::adjustCash($_POST['driverId'],$shipmentArr[0],$shipmentArr[1]);

            if ($result !== true)
                return $result;
        }

        return "true";
    }

    public function adjustItems(Request $request){
        if (empty($_POST['driverId']) || empty($_POST['items']) || count($_POST['items']) <= 0)
        return "invalid request";

        foreach($_POST['items'] as $item){

            $itemArr = explode("|", $item);
            if (empty($itemArr[0])) return "invalid request";

            if (empty($itemArr[1]) || $itemArr[1] <= 0)  continue;

            $result = CustodyModel::adjustCustody($_POST['driverId'],$itemArr[0],$itemArr[1]);

            if ($result !== true)
                return $result;
        }
        return "true";
    }

}

<?php
namespace App\Http\Controllers\web;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Routing\Controller as BaseController;

use App\Helpers\AppHelper;
use App\Models\ZoneModel;
use App\Models\DriverModel;
use App\Models\PlanModel;
use App\Models\ShipmentModel;
use DateTime;
use stdClass;

class PlanController extends BaseAppController {

	public function index(Request $request)
	{
		// $zones = ZoneModel::getAllZones();
        // $drivers = DriverModel::getAllDrivers();

		// return view('daily_plan',array('zones'=>$zones, 'drivers'=>$drivers));
        return $this->dailyPlan($request);
	}

	public function dailyPlan(Request $request)
	{
		$zones = ZoneModel::getAllZones();
        $drivers = DriverModel::getAllDrivers();

		return view('daily_plan',array('zones'=>$zones, 'drivers'=>$drivers));
	}

	public function driverPlan(Request $request)
	{
		$zones = ZoneModel::getAllZones();
        $drivers = DriverModel::getAllDrivers();

		return view('driver_plan',array('drivers'=>$drivers));
	}
	// public function getScheduledShipments(Request $request)
	// {
    //     if (empty($_POST['date']))
    //     return 'invalid date';

    //     if (empty($_POST['zones']))
    //     return 'invalid zone';

    //     $d = DateTime::createFromFormat('Y-m-d', $_POST['date']);
    //     $data = PlanModel::getScheduledShipments($d, $_POST['zones'] );
    //     $noneplanned = PlanModel::getNonePlannedShipments($d);

	// 	return json_encode(array('data'=>$data,'nonePlanned'=>count($noneplanned)));
	// }

    public function getApprovedShipments(Request $request)
	{

        if (empty($_POST['zones']))
        return 'invalid zone';

        $data = PlanModel::getApprovedShipments($_POST['zones'] );
        $noneplanned = PlanModel::getNonePlannedShipments();

		return json_encode(array('data'=>$data,'nonePlanned'=>count($noneplanned)));
	}

	public function assignToDriver(Request $request){

        if (!AppHelper::canEdit('plan')){
            print 'You are not permitted for this operation';
            exit();
        }

        if (empty($_POST['driverId'])){
            print "Please select a driver";
            exit();
        }
        if (empty($_POST['shipments'])){
            print "Please select shipments";
            exit();
        }

        $result = PlanModel::assignToDriver($_POST['driverId'], $_POST['shipments']);
        if ($result == true)
            return "true";
        else
            return $result;

	}

    public function unAssignShipments(Request $request)
	{
        if (!AppHelper::canEdit('plan')){
            print 'You are not permitted for this operation';
            exit();
        }

        if (empty($_POST['shipments'])){
            print "Please select shipments";
            exit();
        }

        $result = PlanModel::unAssignShipments($_POST['shipments']);
        if ($result == true)
            return "true";
        else
            return $result;
	}

    public function getDriversPlan(Request $request)
	{

        if (empty($_POST['drivers']))
        return 'invalid driver';

        $data = PlanModel::getDriverShippedShipments($_POST['drivers']);

		return json_encode(array('data'=>$data));
	}

	// public function delete(Request $request, $id){

    //     if (!AppHelper::canDelete('user')){
    //         print 'You are not permitted for this operation';
    //         exit();
    //     }

	// 	$result = UserModel::deleteUser($id);

	// 	if ($result !== true){
	// 		print $result;
	// 		exit();
	// 	}

	// 	print "true";
	// }


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

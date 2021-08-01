<?php
namespace App\Http\Controllers\web;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Routing\Controller as BaseController;

use App\Helpers\AppHelper;
use App\Models\ClientModel;
use App\Models\DriverModel;
use App\Models\PlanModel;
use App\Models\ZoneModel;
use stdClass;

class ReportController extends BaseAppController {

	public function index(Request $request)
	{
		return abort(404);
	}

	public function planByDriver(Request $request,$id)
	{
        $drivers = DriverModel::getAllDrivers();
		$data = PlanModel::getDriverShippedShipments($id);
		return view('reports.plan_by_driver',array('drivers'=>$drivers, 'data'=>$data));
	}
	public function planByZone(Request $request,$id)
	{
		$zones = ZoneModel::getAllZones();
        $data = PlanModel::getZonePlan($id);
		return view('reports.plan_by_zone',array('zones'=>$zones,'data'=>$data));
	}
	public function deliveredItems(Request $request,$driver)
	{
		return view('reports.plan_by_zone',array('data'=>$data));
	}
    public function returnedItems(Request $request,$driver)
	{
		return view('reports.plan_by_zone',array('data'=>$data));
	}



}

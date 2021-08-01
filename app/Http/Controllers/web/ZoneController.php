<?php
namespace App\Http\Controllers\web;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Routing\Controller as BaseController;

use App\Helpers\AppHelper;
use App\Models\ZoneModel;
use PhpParser\Node\Stmt\Continue_;
use stdClass;

class ZoneController extends BaseAppController {

	public function index(Request $request)
	{
		$data = ZoneModel::getAllZones();
		return view('zones',array('data'=>$data));
	}

	public function view(Request $request, $id=0)
	{
		$result = null;
		$zone = array();
        $regions = array();

        if (!AppHelper::canView('zone')){
            $result = 'You are not permitted for this operation';
        }
        else{
            if ($id > 0){
                $result = ZoneModel::getZoneByID($id);
                $zone = is_object($result)?$result:null;
                $result = is_string($result)?$result:null;

                if (!empty($zone->zone_id))
                $regions = ZoneModel::getZoneRegions($zone->zone_id);
            }
        }

		return view('edit_zone',array('message'=>$result,'zone'=>$zone, 'regions'=>$regions));
	}

	public function save(Request $request){

        if (!AppHelper::canEdit('zone')){
            print 'You are not permitted for this operation';
            exit();
        }

        $zone = array();

        if (empty($_POST['name'])){
            print "Please Enter Name";
            exit();
        }
        // if (empty($_POST['fees'])){
        //     print "Please Enter fees";
        //     exit();
        // }
        // if (empty($_POST['city'])){
        //     print "Please Enter city keywords";
        //     exit();
        // }
        // if (empty($_POST['address'])){
        //     print "Please enter address keywords";
        //     exit();
        // }

        if (!empty($_POST['zone-id']))
            $zone['zone_id'] = $_POST['zone-id'];

        $zone['zone_name'] = $_POST['name'];
        $zone['zone_fees'] = (empty($_POST['fees'])?0:intval($_POST['fees']));

        $regionsData = array();

        if (!empty($_POST['regions']))
        foreach($_POST['regions'] as $region){

            $regionArr = explode("|", $region);
            if (empty($regionArr[0]) || empty($regionArr[1])) continue;

            $regionsData[]=array('region_state'=>$regionArr[0], 'region_city'=>$regionArr[1]);
        }

        $result = ZoneModel::saveZone($zone, $regionsData);
        if (is_numeric($result))
            return "true";
        else
            return $result;

	}

	////////////////////AJAX///////////////////////////

	public function delete(Request $request, $id){

        if (!AppHelper::canDelete('user')){
            print 'You are not permitted for this operation';
            exit();
        }

		$result = UserModel::deleteUser($id);

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

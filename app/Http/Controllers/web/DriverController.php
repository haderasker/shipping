<?php
namespace App\Http\Controllers\web;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Routing\Controller as BaseController;

use App\Helpers\AppHelper;
use App\Models\DriverModel;

use stdClass;

class DriverController extends BaseAppController {

	public function index(Request $request)
	{
		$data = DriverModel::getAllDrivers();
		return view('drivers',array('data'=>$data));
	}

	public function view(Request $request, $id=0)
	{
		$result = null;
		$driver = array();

        if (!AppHelper::canView('driver')){
            $result = 'You are not permitted for this operation';
        }
        else{
            if ($id > 0){
                $result = DriverModel::getDriverByID($id);
                $driver = is_object($result)?$result:null;
                $result = is_string($result)?$result:null;
            }
        }

		return view('edit_driver',array('message'=>$result,'driver'=>$driver));
	}

	public function save(Request $request){

        if (!AppHelper::canEdit('driver')){
            print 'You are not permitted for this operation';
            exit();
        }

        $driver = array();

        if (empty($_POST['name'])){
            print "Please Enter username";
            exit();
        }
        if (empty($_POST['email'])){
            print "Please enter an email";
            exit();
        }
        if (empty($_POST['password'])){
            print "Please Enter password";
            exit();
        }
        if (empty($_POST['status'])){
            print "Please select status";
            exit();
        }

        if (!empty($_POST['user-id']))
            $driver['driver_id'] = $_POST['driver-id'];

        $driver['driver_name'] = $_POST['name'];
        $driver['driver_nationalId'] = $_POST['nationalid'];
        $driver['driver_phone'] = $_POST['phone'];
        $driver['driver_email'] = $_POST['email'];
        $driver['driver_password'] = $_POST['password'];
        $driver['driver_status'] = $_POST['status'];

        $result = DriverModel::saveDriver($driver);
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

		$result = DriverModel::deleteUser($id);

		if ($result !== true){
			print $result;
			exit();
		}

		print "true";
	}

}

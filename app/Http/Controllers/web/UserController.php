<?php
namespace App\Http\Controllers\web;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Routing\Controller as BaseController;

use App\Helpers\AppHelper;
use App\Models\UserModel;

use stdClass;

class UserController extends BaseAppController {

	public function index(Request $request)
	{
		$data = UserModel::getAllUsers();
		return view('users',array('data'=>$data));
	}

	public function view(Request $request, $id=0)
	{
		$result = null;
		$user = array();

        if (!AppHelper::canView('user')){
            $result = 'You are not permitted for this operation';
        }
        else{
            if ($id > 0){
                $result = UserModel::getUserByID($id);
                $user = is_object($result)?$result:null;
                $result = is_string($result)?$result:null;
            }
        }

		return view('edit_user',array('message'=>$result,'user'=>$user));
	}

	public function save(Request $request){

        if (!AppHelper::canEdit('user')){
            print 'You are not permitted for this operation';
            exit();
        }

        $user = array();

        if (empty($_POST['full_name'])){
            print "Please Enter Full Name";
            exit();
        }
        if (empty($_POST['name'])){
            print "Please Enter username";
            exit();
        }
        if (empty($_POST['password'])){
            print "Please Enter password";
            exit();
        }
        if (empty($_POST['role'])){
            print "Please select role";
            exit();
        }
        if (empty($_POST['status'])){
            print "Please select status";
            exit();
        }

        if (!empty($_POST['user-id']))
            $user['user_id'] = $_POST['user-id'];

        $user['user_fullName'] = $_POST['full_name'];
        $user['user_name'] = $_POST['name'];
        $user['user_password'] = $_POST['password'];
        $user['user_role'] = $_POST['role'];
        $user['user_status'] = $_POST['status'];

        $result = UserModel::saveUser($user);
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

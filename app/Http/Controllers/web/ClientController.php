<?php
namespace App\Http\Controllers\web;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Routing\Controller as BaseController;

use App\Helpers\AppHelper;
use App\Models\ClientModel;

use stdClass;

class ClientController extends BaseAppController {

	public function index(Request $request)
	{
		$data = ClientModel::getAllClients();
		return view('clients',array('data'=>$data));
	}

	public function view(Request $request, $id=0)
	{
		$result = null;
		$client = array();

        if (!AppHelper::canView('client')){
            $result = 'You are not permitted for this operation';
        }
        else{
            if ($id > 0){
                $result = ClientModel::getClientByID($id);
                $client = is_object($result)?$result:null;
                $result = is_string($result)?$result:null;
            }
        }

		return view('edit_client',array('message'=>$result,'client'=>$client));
	}

	public function save(Request $request){

        if (!AppHelper::canEdit('client')){
            print 'You are not permitted for this operation';
            exit();
        }

        $client = array();

        if (empty($_POST['name'])){
            print "Please Enter username";
            exit();
        }
        if (empty($_POST['email'])){
            print "Please Enter Email";
            exit();
        }
        if (empty($_POST['password'])){
            print "Please Enter password";
            exit();
        }

        if (!empty($_POST['client-id']))
            $client['client_id'] = $_POST['client-id'];

        $client['client_name'] = $_POST['name'];
        $client['client_email'] = $_POST['password'];
        $client['client_password'] = $_POST['password'];
        $client['client_nationalId'] = $_POST['nationalid'];
        $client['client_followupName'] = $_POST['fname'];
        $client['client_followupPhone'] = $_POST['fphone'];
        $client['client_status'] = $_POST['status'];

        $client['client_smsUser'] = $_POST['smsuser'];
        $client['client_smsPassword'] = $_POST['smspassword'];
        $client['client_smsSenderId'] = $_POST['smssender'];

        $result = ClientModel::saveClient($client);
        if (is_numeric($result))
            return "true";
        else
            return $result;

	}

	////////////////////AJAX///////////////////////////

	public function delete(Request $request, $id){

        if (!AppHelper::canDelete('client')){
            print 'You are not permitted for this operation';
            exit();
        }

		$result = ClientModel::deleteClient($id);

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

<?php
namespace App\Http\Controllers\web;

use App\Helpers\AppHelper;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Routing\Controller as BaseController;

use App\Http\Controllers\web\BaseAppController;
use App\Models\CustomerModel;

use stdClass;

class CustomerController extends BaseAppController {

	public function index(Request $request)
	{
		$data = CustomerModel::getAllCustomers();
		return view('customers',array('data'=>$data));
	}

	public function view(Request $request, $id=0)
	{
        $result = null;
        $customer = array();

        if (!AppHelper::canView('customer')){
            $result = 'You are not permitted for this operation';
        }
        else{
            if ($id > 0){
                $result = CustomerModel::getCustomerByID($id);
                $customer = is_object($result)?$result:null;
                $result = is_string($result)?$result:null;
            }
        }

		return view('edit_customer',array('message'=>$result,'customer'=>$customer));
	}

	public function save(Request $request){

        if (!AppHelper::canEdit('customer')){
            print 'You are not permitted for this operation';
            exit();
        }

        $customer = array();

        if (empty($_POST['name'])){
            print "Please Enter Customer Name";
            exit();
        }
        if (empty($_POST['phone'])){
            print "Please Enter Customer Phone";
            exit();
        }

        if (!empty($_POST['customer-id']))
            $customer['customer_id'] = $_POST['customer-id'];

        $customer['customer_name'] = $_POST['name'];
        $customer['customer_phone'] = $_POST['phone'];
        $customer['customer_address'] = $_POST['address'];
        $customer['customer_town'] = $_POST['town'];
        $customer['customer_district'] = $_POST['district'];
        $customer['customer_area'] = $_POST['area'];
        $customer['customer_state'] = $_POST['state'];
        $customer['customer_country'] = $_POST['country'];
        $customer['customer_zipcode'] = $_POST['zipcode'];
        $customer['customer_remark'] = $_POST['remark'];

        $result = CustomerModel::saveCustomer($customer);
        if (is_numeric($result))
            return "true";
        else
            return $result;

	}

	////////////////////AJAX///////////////////////////

	public function delete(Request $request, $id){

        if (!AppHelper::canDelete('customer')){
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

<?php
namespace App\Http\Controllers\web;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Routing\Controller as BaseController;

use App\Helpers\AppHelper;
use App\Models\VariationModel;

use stdClass;

use function PHPUnit\Framework\returnSelf;

class VariationController extends BaseAppController {

	public function index(Request $request)
	{
		$data = VariationModel::getAllVariations();
		return view('variations',array('data'=>$data));
	}

	public function view(Request $request, $id=0)
	{
		$variation = array();
        if (!AppHelper::canView('item')){
            $result = 'You are not permitted for this operation';
        }
        else{
            if ($id > 0){
                $variation = VariationModel::getVariationByID($id);
            }
        }

		return view('edit_variation',array('variation'=>$variation,'message'=>$result));
	}

	public function save(Request $request){

        if (!AppHelper::canEdit('variation')){
            print 'You are not permitted for this operation';
            exit();
        }

        if (empty($_POST['name'])) {
            print "Please enter variation name";
            exit();
        }

        $variation = array();

        if (!empty($_POST['variation-id']))
            $variation['variation_id'] = $_POST['variation-id'];

        $variation['variation_name'] = $_POST['name'];

        $result =VariationModel::saveVariation($variation);
        if (is_numeric($result))
            return "true";
        else
            return $result;

	}

	////////////////////AJAX///////////////////////////

	public function delete(Request $request, $id){

        if (!AppHelper::canDelete('variation')){
            print 'You are not permitted for this operation';
            exit();
        }

		$result = VariationModel::deleteVariation($id);

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

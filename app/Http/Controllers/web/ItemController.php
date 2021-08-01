<?php
namespace App\Http\Controllers\web;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Support\Facades\DB;
use App\Models\ItemModel;
use App\Models\VariationModel;

use App\Helpers\AppHelper;
use App\Helpers\SimpleXLS;
use App\Helpers\SimpleXLSX;

use File;
use stdClass;

class ItemController extends BaseAppController {

	public function index(Request $request)
	{
		$data = ItemModel::getAllItems();
		return view('items',array('data'=>$data));
	}

	public function view(Request $request, $id=0)
	{
        $result = null;
        $item = array();

        if (!AppHelper::canView('item')){
            $result = 'You are not permitted for this operation';
        }
        else{
            if ($id > 0){
                $item = ItemModel::getItemByID($id);

                if (!empty($item->item_sku))
                    $item->variations = ItemModel::getItemVariationsBySku($item->item_sku);
            }

            $variations = VariationModel::getAllVariations();
        }

		return view('edit_item',array('message'=>$result,
        'item'=>$item,
        'variations'=>$variations));
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

        for ($i = 4; $i < count($manifest->rows()); $i++){
            $r = $manifest->rows()[$i];

            $item = array();
			$item['item_ref'] = $r[0];
			$item['item_name'] = preg_replace('/[\x{10000}-\x{10FFFF}]/u', "\xEF\xBF\xBD", $r[1]);

	        // $item['item_varId'] = $r[4];

			$item['item_sku'] = $r[4];
			$item['item_price'] = is_numeric($r[6])?$r[6]:0;
			$item['item_stock'] = is_numeric($r[7])?$r[7]:0;

            if (empty($item['item_name']) || empty($item['item_sku'])){
                DB::rollBack();
                print "Invalid item name or SKU";
                exit();
            }
                // continue;

            $result = ItemModel::insertItem($item);
            if (!is_numeric($result)){
                DB::rollBack();
                print $result;
                exit();
            }

            if ($r[5]){//has variations

                $variationId = VariationModel::insertVariation(array('variation_name'=>$r[3]));
                if (!is_numeric($variationId)){
                    DB::rollBack();
                    print $result;
                    exit();
                }

                $pv = array();
                $pv['iv_itemSku'] = $item['item_sku'];
                $pv['iv_variationId'] = $variationId;
                $pv['iv_variationSku'] = $r[5];

                $result = ItemModel::insertItemVariation($pv);
                if (!is_numeric($result)){
                    DB::rollBack();
                    print $result;
                    exit();
                }

            }

        }

        DB::commit();
		return "true";
    }

	public function save(Request $request, $data){

        if (!AppHelper::canEdit('item')){
            print 'You are not permitted for this operation';
            exit();
        }

        if (empty($_POST['name'])) {
            print "Please enter item name";
            exit();
        }
        if (empty($_POST['code'])) {
            print "Please enter item code";
            exit();
        }
        if (empty($_POST['sku'])) {
            print "Please enter item SKU";
            exit();
        }

        $item = array();

        if (!empty($_POST['item-id']))
            $item['item_id'] =$_POST['item-id'];

        $item['item_code'] = $_POST['code'];
        $item['item_ref'] = $_POST['ref'];
        $item['item_name'] = $_POST['name'];
        // $item['item_varId'] = $_POST['variations'];
        $item['item_sku'] = $_POST['sku'];

        // $item['item_cost'] = empty($_POST['cost'])?0:$_POST['cost'];
        // $item['item_price'] = empty($_POST['price'])?0:$_POST['price'];
        $item['item_stock'] = empty($_POST['stock'])?0:$_POST['stock'];

        $result = ItemModel::saveItem($item);
        if (is_numeric($result)){
            $image = $request->file('image');
            $itemImgFilename = public_path('img/items/'.$result);
            // return $itemImgFilename;
            if ($image){
                if(File::exists($itemImgFilename))
                    File::delete($itemImgFilename);
                File::copy($image->getRealPath(), $itemImgFilename);
            }

            $variations = array();
            if (!empty($_POST['itemVariations'])) {

                foreach($_POST['itemVariations'] as $v){
                    $arr =explode("|", $v);
                    $pv = array();

                    if (!empty($arr[0]))
                        $pv['iv_id'] = $arr[0];

                    $pv['iv_itemSku'] = $item['item_sku'];
                    $pv['iv_variationId'] = $arr[1];
                    $pv['iv_variationSku'] = $arr[2];

                    if (!is_numeric($pv['iv_variationId']) || empty($pv['iv_variationSku'])){
                        return "Variation or Sku is empty";
                        exit();
                    }

                    $variations[]=$pv;
                }

            }
            $pvResult = ItemModel::saveItemVariations($item['item_sku'], $variations);
            if ($pvResult !== true){
                return $pvResult;
                exit();
            }

            return "true";
        }
        else
            return $result;

	}

	////////////////////AJAX///////////////////////////

	public function delete(Request $request, $id){

        if (!AppHelper::canDelete('item')){
            print 'You are not permitted for this operation';
            exit();
        }

		$result = ItemModel::deleteItem($id);

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

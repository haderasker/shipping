<?php
namespace App\Http\Controllers\web;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Routing\Controller as BaseController;

class BaseAppController extends BaseController {

	function valueToString( $value ){
		return ( !is_bool( $value ) ?  $value : ($value ? 'true' : 'false' )  );
	}

}

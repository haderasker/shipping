<?php
namespace App\Http\Controllers\web;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class AutoRouteController extends BaseController
{

public function index(Request $request, $controller,$method="",$parmeter=""){
    $controller = ucfirst($controller);

    if(empty($method)){
        // e.g: example.com/users
        // will hit App\Http\Controllers\Users\Users.php Class Index method
        $app = \App("App\Http\Controllers\\web\\$controller"."Controller");
        return $app->index($request);
    }



    // e.g: example.com/users/list
    // will hit App\Http\Controllers\Users\Users.php Class List method
    // If user.php has List method then $parameters will pass
    $app = \App("App\Http\Controllers\\web\\$controller"."Controller");
    if(method_exists($app, $method)){
        return $app->$method($request,$parmeter);
    }



    // If you have a folder User and have multiple class in users folder, and want to access other class
    // e.g: example.com/users/groups
    // will hit App\Http\Controllers\Users\Groups.php Class Index method
    $method = ucfirst($method); //Now method will be use as Class name
    $app = \App("App\Http\Controllers\\web\\$controller"."Controller\\$method");
    return $app->index($request);
}

}

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\WoocommerceController;
use App\Http\Controllers\api\DriverController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['prefix' => 'woocommerce'], function() {
    Route::post('order_create',  [WoocommerceController::class, 'order_create']);
    Route::post('order_update',  [WoocommerceController::class, 'order_create']);
    Route::post('order_delete',  [WoocommerceController::class, 'order_create']);
});

Route::post('/driver/login', [DriverController::class, 'login']);
Route::group(['prefix' => 'driver', 'middleware' => ['jwt.verify']], function() {
    Route::get('me', [DriverController::class, 'me']);
    Route::get('logout', [DriverController::class, 'logout']);
    Route::get('dashboard', [DriverController::class, 'dashboard']);
    Route::get('cash', [DriverController::class, 'cash']);
    Route::get('custody', [DriverController::class, 'custody']);
    Route::get('planned_shipments', [DriverController::class, 'plannedShipments']);
    Route::get('shipment_items/{id?}', [DriverController::class, 'shipmentItems']);
    Route::post('deliver_shipment/{id?}', [DriverController::class, 'deliverShipment']);
    Route::post('cancel_shipment/{id?}', [DriverController::class, 'cancelShipment']);
    Route::post('followup_shipment/{id?}', [DriverController::class, 'followupShipment']);
});

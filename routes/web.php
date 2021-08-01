<?php

use App\Http\Controllers\web\AutoRoute;
use Illuminate\Support\Facades\Route;
use App\Models\Accounts;
use App\Http\Controllers\web\AutoRouteController;
use App\Http\Controllers\web\AccountController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {

    return view('login');
});

Route::group(['prefix' => 'account'], function () {
    // Route::get('detail', function ($account_id)    {
    //     // Matches The accounts/{account_id}/detail URL
    // });

    Route::get('/login', [AccountController::class, 'login']);
    Route::post('dologin', [AccountController::class, 'dologin']);
});

Route::match(["get","post"], '/{controller}/{method?}/{parameter?}', [AutoRouteController::class,'index'])->middleware('check.session');

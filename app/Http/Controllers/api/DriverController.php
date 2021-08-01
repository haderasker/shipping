<?php

namespace App\Http\Controllers\api;

use App\Models\CustodyModel;
use JWTAuth;
use App\Models\jwt\jwtDriverModel;
use App\Models\LogModel;
use App\Models\PlanModel;
use App\Models\ShipmentModel;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller as BaseController;
use stdClass;

use function PHPUnit\Framework\returnSelf;

class DriverController extends BaseController
{
    function __construct()
    {
        \Config::set('jwt.user', jwtDriverModel::class);
        \Config::set('auth.providers', ['users' => [
                'driver' => 'eloquent',
                'model' => jwtDriverModel::class,
            ]]);
    }

    // public function register(Request $request)
    // {
    // 	//Validate data
    //     $data = $request->only('name', 'email', 'password');
    //     $validator = Validator::make($data, [
    //         'name' => 'required|string',
    //         'email' => 'required|email|unique:users',
    //         'password' => 'required|string|min:6|max:50'
    //     ]);

    //     //Send failed response if request is not valid
    //     if ($validator->fails()) {
    //         return response()->json(['error' => $validator->messages()], 200);
    //     }

    //     //Request is valid, create new user
    //     $user = User::create([
    //     	'name' => $request->name,
    //     	'email' => $request->email,
    //     	'password' => bcrypt($request->password)
    //     ]);

    //     //User created, return success response
    //     return response()->json([
    //         'success' => true,
    //         'message' => 'User created successfully',
    //         'data' => $user
    //     ], Response::HTTP_OK);
    // }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // valid credential
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:1|max:50'
        ]);

        // //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => 'email and password is required'], 200);
        }

        //Request is validated
        //Create token
        try {
            $login = array('driver_email' => $credentials['email'], 'password' => $credentials['password']);
            if (! $token = JWTAuth::attempt($login)) {
                return response()->json([
                	'error' => 'Login credentials are invalid.',
                ], 200);
            }
        }
        catch (JWTException $e) {
    	    return $credentials;
            return response()->json([
                	'error' => 'Could not create token.',
                ], 200);
        }

 		//Token created, return with success response and jwt token
        return response()->json([
            'token' => $token,
            // 'expires' => JWTAuth::getPayload($token)['exp'],
            'expires' => JWTAuth::setToken($token)->getPayload()->get('exp'),
        ]);
    }

    public function logout(Request $request)
    {
        //valid credential
        // $validator = Validator::make($request->only('token'), [
        //     'token' => 'required'
        // ]);

        // //Send failed response if request is not valid
        // if ($validator->fails()) {
        //     return response()->json(['error' => $validator->messages()], 200);
        // }

		//Request is validated, do logout
        try {
            JWTAuth::invalidate($request->token);

            return response()->json([
                'success' => true,
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'error' => 'Sorry, user cannot be logged out'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function me(Request $request){
        $authUser = JWTAuth::authenticate($request->bearerToken());
        return response()->json($authUser);
    }

    public function dashboard(Request $request)
    {

        return response()->json(['user' => $user]);
    }

    public function cash(Request $request)
    {
        $custody = new stdClass();

        $authUser = JWTAuth::authenticate($request->bearerToken());

        $custody->totalCash = CustodyModel::getTotalCustodyAndCash($authUser->driver_id)->totalCash;
        $custody->shipments = CustodyModel::getDriverCash($authUser->driver_id);

        return response()->json($custody);
    }

    public function custody(Request $request)
    {
        $custody = new stdClass();

        $authUser = JWTAuth::authenticate($request->bearerToken());

        $custody->totalItems = CustodyModel::getTotalCustodyAndCash($authUser->driver_id)->totalCustody;
        $custody->items = CustodyModel::getDriverCustody($authUser->driver_id);

        return response()->json($custody);
    }

    public function plannedShipments(Request $request)
    {
        $authUser = JWTAuth::authenticate($request->bearerToken());
        $plan = PlanModel::getDriverShippedShipments($authUser->driver_id);
        return response()->json($plan);
    }

   public function shipmentItems(Request $request, $id)
    {
        $authUser = JWTAuth::authenticate($request->bearerToken());
        $shipment = ShipmentModel::getShipmentByID($id);

        if ($shipment->shipment_driverId != $authUser->driver_id)
            return response()->json(['error'=>'shipment doesnt belong to the driver']);
        else
            return response()->json($shipment->items);
    }

    public function deliverShipment(Request $request,$id)
    {
        $authUser = JWTAuth::authenticate($request->bearerToken());
        $shipment = ShipmentModel::getShipmentByID($id);

        if ($shipment->shipment_driverId != $authUser->driver_id)
            return response()->json(['error'=>'shipment doesnt belong to the driver']);

        $payload = file_get_contents('php://input');
        if (empty($payload))
        return 'invalid payload';

        $payload = json_decode($payload);

        $result = ShipmentModel::deliverShipment($authUser->driver_id, $id, $payload->items);
        if ($result === true)
            return response()->json(['success' => true]);
        else
            return response()->json(['error'=>$result]);

    }

    public function cancelShipment(Request $request,$id)
    {
        $authUser = JWTAuth::authenticate($request->bearerToken());
        $shipment = ShipmentModel::getShipmentByID($id);

        if ($shipment->shipment_driverId != $authUser->driver_id)
            return response()->json(['error'=>'shipment doesnt belong to the driver']);

        $payload = file_get_contents('php://input');
        if (empty($payload))
        return 'invalid payload';

        $payload = json_decode($payload);

        $result = ShipmentModel::cancelShipment( $shipment->shipment_id, $authUser->driver_id);
        if ($result == true)
            return response()->json(['success' => true]);
        else
            return response()->json(['error'=>$result]);

    }

    public function followupShipment(Request $request,$id)
    {
        $authUser = JWTAuth::authenticate($request->bearerToken());
        $shipment = ShipmentModel::getShipmentByID($id);

        if ($shipment->shipment_driverId != $authUser->driver_id)
            return response()->json(['error'=>'shipment doesnt belong to the driver']);

        $payload = file_get_contents('php://input');
        if (empty($payload))
        return 'invalid payload';

        $payload = json_decode($payload);

        $date = DateTime::createFromFormat('Y-m-d', $payload->date);
        $result = ShipmentModel::followupShipment($shipment->shipment_id,$date);
        if ($result == true){
            ShipmentModel::insertShipmentLog($shipment->shipment_id,$shipment->shipment_driverId,'driver',$payload->notes);
            return response()->json(['success' => true]);
        }
        else
            return response()->json(['error'=>$result]);

    }

}

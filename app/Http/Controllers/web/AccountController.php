<?php
namespace App\Http\Controllers\web;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

use App\Models\UserModel;

class AccountController extends BaseController {

    public function index(Request $request)
    {
        return view('login');
    }

	public function login()
	{
		return view('login');
	}

	// Check for user login process
	public function dologin(Request $request)
	{

        $validatedData = $request->validate([
            'name' => ['required'],
            'password' => ['required'],
        ]);

		if ($validatedData == FALSE) {
			if(session()->has('login_info')){
				return redirect('/dashboard');
			}else{
				return redirect('/account/login');
			}
		} else {
			$result = UserModel::userLogin($_POST['name'],$_POST['password']);
			//$result = TRUE;
			if (is_string($result)){
				return view('login',array('error_message' => $result));
			} else {
				// Add user data in session
				session(['login_info'=>$result]);
				return redirect('/dashboard');
			}
		}
	}

    // Logout from admin page
	public function logout(Request $request) {

		session()->forget('login_info');
//		$data['message'] = 'Successfully Logout';
//		$this->load->view('login_form', $data);

//		return redirect('/account/'.$area);
		return redirect('/');
	}

	// AJAX /////////////////////////////////////////////////////////////////////////////////////////////////////////

}

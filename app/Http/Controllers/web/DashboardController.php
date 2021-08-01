<?php
namespace App\Http\Controllers\web;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Routing\Controller as BaseController;

use App\Helpers\AppHelper;
// use App\Models\StudentModel;
// use App\Models\CourseModel;

class DashboardController extends BaseAppController {

	public function index(Request $request)
	{
		// $student_statistics = StudentModel::getAdminStatistics();
		// $session_statistics = CourseModel::getSessionAdminStatistics();
		// $course_statistics = CourseModel::getCourseAdminStatistics();
		// $plan_statistics = CourseModel::getPlanAdminStatistics();

		// return view('dashboard',array(
		//                                           'student_statistics'=>$student_statistics,
		//                                           'session_statistics'=>$session_statistics,
		//                                           'course_statistics'=>$course_statistics,
		//                                           'plan_statistics'=>$plan_statistics));

        return view('dashboard');
	}
}

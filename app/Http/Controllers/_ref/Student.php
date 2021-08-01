<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Routing\Controller as BaseController;

use App\Models\StudentModel;
use App\Models\TimezoneModel;
// use App\Models\DefinitionModel;
use App\Models\CourseModel;
use App\Models\EmployeeModel;
use stdClass;

class Student extends BaseAppController {

	public function index(Request $request)
	{
		$employee = session('login_info');
		$supportId = ($employee->employee_role == 1 ? 0:$employee->employee_id);

		//print $supportId; exit();

		$data = StudentModel::getAllStudents(array(1,2),$supportId);
		return view('employee/students',array('data'=>$data));
	}

	public function reviews(Request $request)
	{
		$data = StudentModel::getReviewStudents();
		return view('employee/students_review',array('data'=>$data));
	}

	public function view(Request $request, $id=0)
	{
		$result = null;
		$student = array();

		if (isset($_POST['submit'])){//save

            if (!empty($id))
			    $student['student_id'] = $id;

			$student['student_name'] = $_POST['full_name'];
			$student['student_email'] = $_POST['email'];
			$student['student_password'] = $_POST['password'];
			$student['student_timezoneId'] = $_POST['timezone'];
			$student['student_countryId'] = $_POST['country'];
			$student['student_gender'] = $_POST['gender'];
			$student['student_age'] = $_POST['age'];
			$student['student_phone'] = $_POST['phone'];
			$student['student_prefteacher'] = $_POST['prefteacher'];
			$student['student_employeeId'] = $_POST['employee'];
			$student['student_status'] = $_POST['status'];
			// $student->student_packageId = $_POST['package'];
			$student['student_notes'] = $_POST['notes'];

			$result = $this->save($request, $student);
		}
		else{//get
			if ($id > 0){
				$result = StudentModel::getStudentByID($id);
				$student = is_object($result)?$result:null;
				$result = is_string($result)?$result:null;
			}
			else
				$student['student_status'] = 3;
		}

		return view('employee/edit_student',array('message'=>$result,
                                                    'student'=>$student,
                                                    'employees'=>EmployeeModel::getAllEmployees(0,2),
                                                    'timezones'=> TimezoneModel::getAllTimezones(),
                                                    'countries'=>TimezoneModel::getAllCountries()));
	}

	private function save(Request $request, $data){

        $validatedData = $request->validate([
            'full_name' => ['required'],
            'email' => ['required'],
            'password' => ['required'],
        ]);

		if ($validatedData == FALSE) {
			return "Please fill all required fields";
		}
		else{
			$result = StudentModel::saveStudent($data);
			if (is_numeric($result))
				return redirect('/student/profile/'.$result);
			else
				return $result;
		}

//		$this->load->view('employee/template_main',array('template'=>'employee/edit_employee','message'=>$msg,'data'=>$data));
	}

	public function courses(Request $request, $id)
	{
		$student = StudentModel::getStudentByID($id);
		$courses = CourseModel::getStudentCourses($id);
		return view('employee/student_courses',array(
		                                          'courses'=>$courses,
		                                          'student'=>$student));
	}
//
//	public function sessions($id)
//	{
//		$this->load->view('template_drawer',array('template'=>'employee/student_sessions', 'studentName'=>'Test Student'));
//	}

	public function profile(Request $request, $id)
	{
		$student = StudentModel::getStudentByID($id);
		$sessions = CourseModel::getStudentActiveCourseSessions($id);
		$courses = CourseModel::getStudentCourses($id);
		$family = StudentModel::getFamily($id);

		return view('employee/student_profile',array('student'=>$student,
                                            'sessions'=>$sessions,
                                            'courses'=>$courses,
                                            'family'=>$family,
		));
	}


	/////////////////AJAX//////////////////////////////////////

	public function delete(Request $request, $id){
//		$content = json_decode($request->getContent(), true);
//		print_r($id); exit();

		$result = StudentModel::deleteStudent($id);

		if ($result !== true){
			print $result;
			exit();
		}

		print "true";
	}

	public function getNoneAssignedStudents(Request $request){
		$result = StudentModel::getNoneAssignedStudents();
		print json_encode($result);
	}

	public function addFamilyMember(Request $request){
		$content = json_decode($request->getContent(), true);
//		print_r($request); exit();

		if (empty($content['full_name'])){
			print "Please Enter Full Name!";
			exit();
		}

		if (empty($content['relation'])){
			print "Please Select Relation!";
			exit();
		}

		$parent = StudentModel::getStudentByID($content['parent_id']);

//		print_r($parent);exit();

		if (empty($parent->student_id)){
			print "Invalid Parent!";
			exit();
		}

//		print_r($request);

		$student = array();
		if (empty($content['selected_child']) || intval($content['selected_child']) <= 0){
			//$student->student_id = $id;
			$student['student_parentId'] = $parent->student_id;
			$student['student_parentRelation'] = $content['relation'];
			$student['student_notes'] = $content['notes'];

			$student['student_name'] = $content['full_name'];
			$student['student_timezoneId'] = $parent->student_timezoneId;
			$student['student_countryId'] = $parent->student_countryId;
			$student['student_gender'] = $content['gender'];
			$student['student_age'] = $content['age'];
			$student['student_phone'] = $content['phone'];
			$student['student_prefteacher'] = $content['preftutor'];
			$student['student_status'] = $content['status'];
		}
		else{
			$student['student_id'] = $content['selected_child'];
			$student['student_parentId'] = $parent->student_id;
			$student['student_parentRelation'] = $content['relation'];
			$student['student_notes'] = $content['notes'];
		}
//		print_r($student); exit();
		$result = StudentModel::saveStudent($student);
		if (!is_numeric($result)){
			print $result;
			exit();
		}

		print "true";
	}
}

<?php 
 
 namespace App\Controllers;

use App\Core\Controller;
use App\Models\Enrollee;
use App\Models\Employee;
use App\Models\Student;

 class EnroleeController extends Controller
 {

    public function index()
    {
         $user = Employee::find('1003'); 
         
        $this->render('students/enrollee',['user' => $user]);
    }

    public function allEnrollees()
    {
        header('Content-Type: application/json');
        $active_count = Enrollee::allEnrollee(true);

        echo json_encode($active_count);

    }

  

  public function find($id)
    {
         header('Content-Type: application/json');
          $test =  Enrollee::find($id);

          $firstname = $test['first_name'];
          $surname = $test['surname'];
          $sex = $test['sex'];


        Student::create([
            'student_number' => 3321312,
            'first_name' => $firstname,
            'last_name' => $surname,
            'gender' => $sex
        ]);

          

        echo json_encode($test);


    }
 }
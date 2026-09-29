<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Employee;
use App\Models\Student;

 class HomeController extends Controller
 {
  

    public function index()
    {   
        
        $cpuUsage = 0;

        $cmd = "wmic cpu get loadpercentage";
        @exec($cmd, $output);
        if (!empty($output[1])) {
              $cpuUsage = (int)$output[1];
        }

        $user = Employee::find('1003'); 
      
        $this->render('/home', ['cpu' => $cpuUsage , 'user' => $user]);
    }

    public function countNumber()
    {
        header('Content-Type: application/json');
         $active_count = Student::countActiveStudents();
        echo json_encode($active_count );     

    }


    
 }
 
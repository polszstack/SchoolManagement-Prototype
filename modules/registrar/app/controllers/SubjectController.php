<?php 

namespace App\Controllers;

use App\Core\Controller;
use App\Helper\Logger;
use App\Helper\Response;
use App\Models\Employee;
use App\Models\Subject;


class SubjectController extends Controller
{

    public function index()
    {

      
        $user = Employee::find('1003'); 
        
        $this->render('/acad/subject', ['user' => $user]);

    }


    public function allSubjects()
    {
        $subject = Subject::allSubject();
        Response::json($subject);
    }


    public function store()
    {

         header('Content-Type: application/json');

        $errors = [];

        $subject_code = trim($_POST['subject_code'] ?? '');
        $subject_name = trim($_POST['subject_name'] ?? '');
        $subject_lab = trim($_POST['subject_lab'] ?? '');
        $subject_lecture = trim($_POST['subject_lecture'] ?? '');
        $subject_unit = trim($_POST['subject_unit'] ?? '');

        if ($subject_unit === '') {
         $errors['subject_unit'] = 'Subject Unit is required.';
        }

        if ($subject_lecture === '') {
         $errors['subject_lecture'] = 'Subject Lecture is required.';
        }

        if ($subject_lab === '') {
         $errors['subject_lab'] = 'Subject Lab is required.';
        }

        if ($subject_code === '') {
         $errors['subject_code'] = 'Subject Code is required.';
        }

        if ($subject_name === '') {
         $errors['subject_name'] = 'Subject Name is required.';
        }

        if (!empty($errors)) {
            echo json_encode([
                'status' => 'error',
                'errors' => $errors
            ]);
            return;
        }

        Subject::create([

            'code' => strtoupper($subject_code),
            'name' => $subject_name,
            'units' => $subject_unit,
            'lecture_hours' => $subject_lecture,
            'lab_hours' => $subject_lab 
        ]);

        
        Logger::log(
        "Created A New Subject",
         "Created A New Subject Information for System"
         );


        echo json_encode([
            'status' => 'success',
            'message' => 'Strand created successfully.'
        ]);

    }


    public function destroy()
    {
         header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);


        if (!empty($data['ids'])) {

        $array_num_id = count($data['ids']);

        Logger::log(
        "Delete Subject Input",
         "Deleting $array_num_id item/s Subject Information"
         );


            $deleted = Subject::deleteMany($data['ids']);
            echo json_encode(['success' => (bool)$deleted]);
            exit;
        }

        echo json_encode(['success' => false]);
        exit;

    }

}
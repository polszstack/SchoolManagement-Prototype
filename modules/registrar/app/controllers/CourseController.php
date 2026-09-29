<?php 


namespace App\Controllers;

use App\Core\Controller;
use App\Helper\Logger;
use App\Models\Course;
use App\Models\Employee;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;




class CourseController extends Controller {

    public function index()
    {

        $user = Employee::find('1003'); 
      
        $this->render('/acad/course', ['user' => $user]);

    }

    public function allCourse()
    {
         header('Content-Type: application/json');
        $course = Course::allCourses(true);
        
        echo json_encode($course);

    }

    public function show($id)
    {
        
        header('Content-Type: application/json');
        $course = Course::find($id);
        echo json_encode($course );       

    }


    public function destroy()
    {

         header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);


        if (!empty($data['ids'])) {

        $array_num_id = count($data['ids']);

        Logger::log(
        "Delete Course Input",
         "Deleting $array_num_id item/s Course Information"
         );


            $deleted = Course::deleteMany($data['ids']);
            echo json_encode(['success' => (bool)$deleted]);
            exit;
        }

        echo json_encode(['success' => false]);
        exit;
    }


    public function coursePDF()
    {   

         Logger::log(
        "Get A PDF Course student Report",
         "Downloading a PDF file contains Course Information"
         );


         $dompdf = new Dompdf();
    
        $bcp_logo = $this->imageRender('bcp-logo.png');
        $ched_logo = $this->imageRender('ched.png');

         $courses = Course::allCourses(false);
        
         ob_start();
        
    
        $this->render('/pdf/course',[

            'courses' => $courses,
            'school_image' => $bcp_logo,
            'ched_image' => $ched_logo,
            ]);

        $html = ob_get_clean();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        
        $dompdf->stream("courses.pdf", ["Attachment" => false]);
        

    }


    public function courseExcel()
    {

         Logger::log(
        "Get A Excel Course student Report",
         "Downloading a Excel file contains Course Information"
         );

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Course Code');
        $sheet->setCellValue('B1', 'Course Name');
        $sheet->setCellValue('C1', 'Years');

        $courses = Course::allCourses(false);

        $row = 2;

        foreach($courses as $course)
        {

            $sheet->setCellValue('A' . $row, $course['code'] ?? 'null');
            $sheet->setCellValue('B' . $row, $course['name']);
            $sheet->setCellValue('C' . $row, $course['years']);
        
            $row++ ;

        }


        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="courses.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');

        exit;
    }



    public function courseCSV()
    {

         Logger::log(
        "Get A CSV Course student Report",
         "Downloading a CSV file contains Course Information"
         );
  
        while (ob_get_level()) {
            ob_end_clean();
        }

        

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();


        $courses = Course::allCourses(false);

    
        $sheet->setCellValue('A1', 'Course Code');
        $sheet->setCellValue('B1', 'Course Name');
        $sheet->setCellValue('C1', 'Years');
        $sheet->setCellValue('D1', 'Course');


        $row = 2;

        foreach ($courses as $course) {
            $sheet->setCellValue("A$row", $course['code']);
            $sheet->setCellValue("B$row", $course['name']);
            $sheet->setCellValue("C$row", $course['years']);
            $row++;
        }

        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="course.csv"');
        header('Cache-Control: max-age=0');

        $writer = new Csv($spreadsheet);

        
        $writer->setDelimiter(',');
        $writer->setEnclosure('"');
        $writer->setLineEnding("\r\n");
        $writer->setUseBOM(true); 

        $writer->save('php://output');
        exit;

    }

    public function store()
    {

        header('Content-Type: application/json');

        $errors = [];

        $course_code = trim($_POST['course_code'] ?? '');
        $course_name = trim($_POST['course_name'] ?? '');
        $course_year = trim($_POST['course_year'] ?? '');

        if ($course_code === '') {
        $errors['course_code'] = 'Course Code is required.';
        }

        if ($course_name === '') {
            $errors['course_name'] = 'Course Name is required.';
        }

        if (!empty($errors)) {
            echo json_encode([
                'status' => 'error',
                'errors' => $errors
            ]);
            return;
        }

        Course::create([

            'code' => $course_code,
            'name' => $course_name,
            'years' => $course_year 
        ]);

        
        Logger::log(
        "Created A New Course",
         "Created A New Course Information for System"
         );

        echo json_encode([
            'status' => 'success',
            'message' => 'Course created successfully.'
        ]);

    }

    public function update($id)
    {

         header('Content-Type: application/json');

        $errors = [];

        $course_code = trim($_POST['edit_course_code'] ?? '');
        $course_name = trim($_POST['edit_course_name'] ?? '');
        $course_year = trim($_POST['edit_course_year'] ?? '');

        if ($course_code === '') {
        $errors['edit_course_code'] = 'Course Code is required.';
        }

        if ($course_name === '') {
            $errors['edit_course_name'] = 'Course Name is required.';
        }

        if (!empty($errors)) {
            echo json_encode([
                'status' => 'error',
                'errors' => $errors
            ]);
            return;
        }

        Course::update($id,[

            'code' => $course_code,
            'name' => $course_name,
            'years' => $course_year 
        ]);

         Logger::log(
        "Updated A Course",
         "Updated the Course Information."
         );

        echo json_encode([
            'status' => 'success',
            'message' => 'Course updated successfully.'
        ]);

    }


    public function edit($id)
    {
        header('Content-Type: application/json');
        $course = Course::find($id);
        echo json_encode($course);    
    }

    public function imageRender($image_name)
    {

     $path = $_SERVER['DOCUMENT_ROOT'].BASE_URL."/assets/images/$image_name";
     $type = pathinfo($path, PATHINFO_EXTENSION);
     $data = file_get_contents($path);
     $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    
     return $base64;

    }



}
<?php 


namespace App\Controllers;

use App\Core\Controller;
use App\Helper\Logger;
use App\Models\Employee;
use App\Models\Student;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;




class StudentController extends Controller {


    public function index()
    {   
        $user = Employee::find('1003'); 
        
        $this->render('/students/index', ['user' => $user]);
    
    }

    public function studentData()
    {
       

        header('Content-Type: application/json');
        $active_count = Student::allStudents(true);


        echo json_encode($active_count);

    }

    public function studentExcel()
    {
        $status = $_GET['status'];



        Logger::log(
        "Get A Excel {$status} student Report",
         "Downloading a Excel file contains {$status} students information"
         );



        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Student Number');
        $sheet->setCellValue('B1', 'First Name');
        $sheet->setCellValue('C1', 'Last Name');
        $sheet->setCellValue('D1', 'Course');

        $students = Student::allStudents(false);

        $row = 2;

        foreach($students as $student)
        {

            $sheet->setCellValue('A' . $row, $student['student_number']);
            $sheet->setCellValue('B' . $row, $student['first_name']);
            $sheet->setCellValue('C' . $row, $student['last_name']);
            $sheet->setCellValue('D' . $row, $student['course']);
        
            $row++ ;

        }
    
   

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="students.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');

        exit;
    }

    public function studentPDF()
    {
       
        $status = $_GET['status'];
   

          Logger::log(
                "Get A PDF {$status} student Report",
                "Downloading a PDF file contains {$status} students information"
            );

        $dompdf = new Dompdf();
        

        $status = ucfirst($_GET['status']);
        $bcp_logo = $this->imageRender('bcp-logo.png');
        $ched_logo = $this->imageRender('ched.png');

         $students = Student::allStudents(false);
        
         ob_start();
        
    
        $this->render('/pdf/student',[

            'students' => $students,
            'school_image' => $bcp_logo,
            'ched_image' => $ched_logo,
            'status' => $status
            ]);

        $html = ob_get_clean();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        
        $dompdf->stream("{$status}_student.pdf", ["Attachment" => false]);
        
    }



    public function studentCSV()
    {
        $status = $_GET['status'];

        while (ob_get_level()) {
            ob_end_clean();
        }

          Logger::log(
                "Get A CSV {$status} student Report",
                "Downloading a CSV file contains {$status} students information"
            );

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();


        $students = Student::allStudents(false); 

    
        $sheet->setCellValue('A1', 'Student Number');
        $sheet->setCellValue('B1', 'First Name');
        $sheet->setCellValue('C1', 'Last Name');
        $sheet->setCellValue('D1', 'Course');


        $row = 2;

        foreach ($students as $student) {
            $sheet->setCellValue("A$row", $student['student_number']);
            $sheet->setCellValue("B$row", $student['first_name']);
            $sheet->setCellValue("C$row", $student['last_name']);
            $sheet->setCellValue("D$row", $student['course']);
            $row++;
        }

        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="students.csv"');
        header('Cache-Control: max-age=0');

        $writer = new Csv($spreadsheet);

        
        $writer->setDelimiter(',');
        $writer->setEnclosure('"');
        $writer->setLineEnding("\r\n");
        $writer->setUseBOM(true); 

        $writer->save('php://output');
        exit;
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
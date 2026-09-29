<?php

  namespace App\Controllers;

  use App\Core\Controller;
  use App\Helper\Logger;
  use App\Models\Employee;
  use App\Models\SchoolYear;
  use App\Models\Semester;
  use Dompdf\Dompdf;
  use PhpOffice\PhpSpreadsheet\Spreadsheet;
  use PhpOffice\PhpSpreadsheet\Writer\Csv;
  use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

  class SemesterController extends Controller 
  {

    public function index()
    {

        $school_year = SchoolYear::all();

         $user = Employee::find('1003'); 
      
        $this->render('/acad/semester', ['user' => $user,'school_year' => $school_year]);

    }

    public function allSemester()
    {
        header('Content-Type: application/json');
        $semester = Semester::allSemester();
        echo json_encode($semester );
    }


    public function store()
    {

       header('Content-Type: application/json');

        $errors = [];

        $semester_name = trim($_POST['semester_name'] ?? '');
        $is_active = trim($_POST['is_active'] ?? '');
           $school_year_id = trim($_POST['school_year_id'] ?? '');

        if ($semester_name === '') {
        $errors['semester_name'] = 'Semester Name is required.';
        }

        if (!empty($errors)) {
            echo json_encode([
                'status' => 'error',
                'errors' => $errors
            ]);
            return;
        }

          if($is_active == true){

        Semester::updateStatus();

        }

       
        Semester::create([
            'name' => $semester_name,
            'is_active' => $is_active,
            'school_year_id' => $school_year_id,
        ]);

          
        Logger::log(
        "Created A New School Semester",
         "Created A New Schoool Semester Information for System"
         );

        echo json_encode([
            'status' => 'success',
            'message' => 'Semester created successfully.'
        ]);


    }

    public function semesterPdf()
    {   

        Logger::log(
        "Get A PDF of Semester Report",
         "Downloading a PDF file contains Semester Information"
         );


         $dompdf = new Dompdf();
    
        $bcp_logo = $this->imageRender('bcp-logo.png');
        $ched_logo = $this->imageRender('ched.png');

         $semesters = Semester::allSemester(false);
        
         ob_start();
        
    
        $this->render('/pdf/semester',[

            'semesters' => $semesters,
            'school_image' => $bcp_logo,
            'ched_image' => $ched_logo,

            ]);

        $html = ob_get_clean();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        
        $dompdf->stream("semester.pdf", ["Attachment" => false]);


    }


    public function semesterExcel()
    {

          Logger::log(
        "Get A Excel of Semester Report",
         "Downloading a Excel file contains Semester Information"
         );

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Semester');
        $sheet->setCellValue('B1', 'Status');
        $sheet->setCellValue('C1', 'School Year');

        $semesters = Semester::allSemester(false);

        $row = 2;

        foreach($semesters as $semester)
        {

            $sheet->setCellValue('A' . $row, $semester['semester']);
            $sheet->setCellValue('B' . $row, $semester['status'] ? 'active' : 'inactive');
            $sheet->setCellValue('C' . $row, $semester['school_year']);
        
            $row++ ;

        }


        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="semester.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');

        exit;


    }


    public function semesterCsv()
    {
        
           Logger::log(
        "Get A CSV of School Year Report",
         "Downloading a CSV file contains School Year Information"
         );
  
        while (ob_get_level()) {
            ob_end_clean();
        }

        

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();


         $semesters = Semester::allSemester(false);

    
         $sheet->setCellValue('A1', 'Semester');
        $sheet->setCellValue('B1', 'Status');
        $sheet->setCellValue('C1', 'School Year');


        $row = 2;

        foreach($semesters as $semester)
        {

            $sheet->setCellValue('A' . $row, $semester['semester']);
            $sheet->setCellValue('B' . $row, $semester['status'] ? 'active' : 'inactive');
            $sheet->setCellValue('C' . $row, $semester['school_year']);
        
            $row++ ;

        }

        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="semester.csv"');
        header('Cache-Control: max-age=0');

        $writer = new Csv($spreadsheet);

        
        $writer->setDelimiter(',');
        $writer->setEnclosure('"');
        $writer->setLineEnding("\r\n");
        $writer->setUseBOM(true); 

        $writer->save('php://output');
        exit;
    }




     public function update($id)
    {


         header('Content-Type: application/json');


        Logger::log(
        "Updated A New School Year",
         "Updated A New Schoool Year Information for System"
         );

        
         Semester::updateStatus();

         Semester::update($id,[

            'is_active' => true,

         ]);

        echo json_encode([
            'status' => 'success',
            'message' => 'Semester Updated successfully.'
        ]);



    }


    public function destroy()
    {

         header('Content-Type: application/json');

        $data = json_decode(file_get_contents('php://input'), true);

         Logger::log(
        "Deleted A Semester Record",
         "Deleted A Semester Record Information for System"
         );

        if (!empty($data['ids'])) {
            $deleted = Semester::deleteMany($data['ids']);
            echo json_encode(['success' => (bool)$deleted]);
            exit;
        }

        echo json_encode(['success' => false]);
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
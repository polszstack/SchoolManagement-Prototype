<?php 

 namespace App\Controllers;
 use App\Core\Controller;
use App\Helper\Logger;
use App\Models\Employee;
use App\Models\SchoolYear;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

 class SchoolYearController extends Controller {

    public function index()
    {
        
        $user = Employee::find('1003'); 
      
        $this->render('/acad/schoolYear', ['user' => $user]);

    }

    public function allSchoolYear()
    {

        header('Content-Type: application/json');
        $all_school_year = SchoolYear::allSchoolYear();
        echo json_encode($all_school_year );

    }

    public function store()
    {

        header('Content-Type: application/json');

        $errors = [];

        $school_year_name = trim($_POST['school_year_name'] ?? '');
        $is_active = trim($_POST['is_active'] ?? '');
     

        if ($school_year_name === '') {
        $errors['school_year_name'] = 'School Year Name is required.';
        }

        if (!empty($errors)) {
            echo json_encode([
                'status' => 'error',
                'errors' => $errors
            ]);
            return;
        }

        if($is_active == true){

        SchoolYear::updateStatus();

        }

       
        SchoolYear::create([
            'name' => $school_year_name,
            'is_active' => $is_active,
            
        ]);

        
        Logger::log(
        "Created A New School Year",
         "Created A New Schoool Year Information for System"
         );

        echo json_encode([
            'status' => 'success',
            'message' => 'School Year created successfully.'
        ]);

    }

    public function schoolYearPdf()
    {

         Logger::log(
        "Get A PDF of School Year Report",
         "Downloading a PDF file contains School Year Information"
         );


         $dompdf = new Dompdf();
    
        $bcp_logo = $this->imageRender('bcp-logo.png');
        $ched_logo = $this->imageRender('ched.png');

         $school_years = SchoolYear::allSchoolYear(false);
        
         ob_start();
        
    
        $this->render('/pdf/schoolYear',[

            'school_years' => $school_years,
            'school_image' => $bcp_logo,
            'ched_image' => $ched_logo,

            ]);

        $html = ob_get_clean();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        
        $dompdf->stream("school_years.pdf", ["Attachment" => false]);
        

    }

    public function schoolYearExcel()
    {

           Logger::log(
        "Get A Excel of School Year Report",
         "Downloading a Excel file contains School Year Information"
         );

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'School Year');
        $sheet->setCellValue('B1', 'Status');

        $school_years = SchoolYear::allSchoolYear(false);

        $row = 2;

        foreach($school_years as $school_year)
        {

            $sheet->setCellValue('A' . $row, $school_year['name']);
            $sheet->setCellValue('B' . $row, $school_year['is_active'] ? 'active' : 'inactive');
        
            $row++ ;

        }


        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="school_year.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');

        exit;

    }


    public function schoolYearCsv()
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


        $school_years = SchoolYear::allSchoolYear(false);

    
        $sheet->setCellValue('A1', 'School Year');
        $sheet->setCellValue('B1', 'Status');


        $row = 2;

        foreach ($school_years as $school_year) {
            $sheet->setCellValue("A$row", $school_year['name']);
            $sheet->setCellValue("B$row", $school_year['is_active'] ? 'active' : 'inactive');
            $row++;
        }

        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="school_year.csv"');
        header('Cache-Control: max-age=0');

        $writer = new Csv($spreadsheet);

        
        $writer->setDelimiter(',');
        $writer->setEnclosure('"');
        $writer->setLineEnding("\r\n");
        $writer->setUseBOM(true); 

        $writer->save('php://output');
        exit;


    }


   

    public function destroy()
    {

         header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);

         Logger::log(
        "Deleted A New School Year",
         "Deleted A New Schoool Year Information for System"
         );

        if (!empty($data['ids'])) {
            $deleted = SchoolYear::deleteMany($data['ids']);
            echo json_encode(['success' => (bool)$deleted]);
            exit;
        }

        echo json_encode(['success' => false]);
        exit;

    }



    public function update($id)
    {

        
         header('Content-Type: application/json');


        Logger::log(
        "Updated A New School Year",
         "Updated A New Schoool Year Information for System"
         );

        
         SchoolYear::updateStatus();

         SchoolYear::update($id,[

            'is_active' => true,

         ]);

        echo json_encode([
            'status' => 'success',
            'message' => 'Course updated successfully.'
        ]);


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
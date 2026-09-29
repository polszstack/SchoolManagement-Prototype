<?php 

namespace App\Controllers;

use App\Core\Controller;
use App\Helper\Logger;
use App\Models\Employee;
use App\Models\Strand;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StrandController extends Controller
{

    public function index()
    {

         $user = Employee::find('1003'); 
      
        $this->render('/acad/strand', ['user' => $user]);

        

    }


    public function allStrands()
    {
        header('Content-Type: application/json');
        $strands = Strand::allStrands();
        echo json_encode($strands );
    }


    public function store()
    {

        header('Content-Type: application/json');

        $errors = [];

        $strand_code = trim($_POST['strand_code'] ?? '');
        $strand_name = trim($_POST['strand_name'] ?? '');
      

        if ($strand_code === '') {
         $errors['strand_code'] = 'Strand Code is required.';
        }

        if ($strand_name === '') {
         $errors['strand_name'] = 'Strand Name is required.';
        }

        if (!empty($errors)) {
            echo json_encode([
                'status' => 'error',
                'errors' => $errors
            ]);
            return;
        }

        Strand::create([

            'code' => strtoupper($strand_code),
            'name' => $strand_name,
        ]);

        
        Logger::log(
        "Created A New Strand",
         "Created A New Strand Information for System"
         );


        echo json_encode([
            'status' => 'success',
            'message' => 'Strand created successfully.'
        ]);

    }


    public function show($id)
    {
        
        header('Content-Type: application/json');
        $strand = Strand::find($id);
        echo json_encode($strand );       

    }

    public function destroy()
    {

         header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);


        if (!empty($data['ids'])) {

        $array_num_id = count($data['ids']);

        Logger::log(
        "Delete Strand Input",
         "Deleting $array_num_id item/s Course Information"
         );


            $deleted = Strand::deleteMany($data['ids']);
            echo json_encode(['success' => (bool)$deleted]);
            exit;
        }

        echo json_encode(['success' => false]);
        exit;
    }

    public function strandPdf()
    {
    
         Logger::log(
        "Get A PDF of Strands Report",
         "Downloading a PDF file contains Strands Information"
         );


         $dompdf = new Dompdf();
    
        $bcp_logo = $this->imageRender('bcp-logo.png');
        $ched_logo = $this->imageRender('ched.png');

         $strands = Strand::allStrands(false);
        
         ob_start();
        
    
        $this->render('/pdf/strand',[

            'strands' => $strands,
            'school_image' => $bcp_logo,
            'ched_image' => $ched_logo,

            ]);

        $html = ob_get_clean();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        
        $dompdf->stream("strands.pdf", ["Attachment" => false]);



    }


    public function strandExcel()
    {

         Logger::log(
        "Get A Excel of Strand Report",
         "Downloading a Excel file contains Semester Information"
         );

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Code');
        $sheet->setCellValue('B1', 'Name');

        $strands = Strand::allStrands(false);

        $row = 2;

        foreach($strands as $strand)
        {

            $sheet->setCellValue('A' . $row, $strand['code']);
            $sheet->setCellValue('B' . $row, $strand['name']);
        
            $row++ ;

        }


        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="strands.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');

        exit;



    }


    public function strandCsv()
    {

           Logger::log(
        "Get A CSV of Strand Report",
         "Downloading a CSV file contains Strand Information"
         );
  
        while (ob_get_level()) {
            ob_end_clean();
        }

        $spreadsheet = new Spreadsheet();


        $strands = Strand::allStrands(false);

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Code');
        $sheet->setCellValue('B1', 'Name');

        $strands = Strand::allStrands(false);

        $row = 2;

        foreach($strands as $strand)
        {

            $sheet->setCellValue('A' . $row, $strand['code']);
            $sheet->setCellValue('B' . $row, $strand['name']);
        
            $row++ ;

        }
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="strands.csv"');
        header('Cache-Control: max-age=0');

        $writer = new Csv($spreadsheet);

        
        $writer->setDelimiter(',');
        $writer->setEnclosure('"');
        $writer->setLineEnding("\r\n");
        $writer->setUseBOM(true); 

        $writer->save('php://output');
        exit;


    }

    public function edit($id)
    {
        header('Content-Type: application/json');
        $course = Strand::find($id);
        echo json_encode($course);    
    }

    public function update($id)
    {

         header('Content-Type: application/json');

        $errors = [];

        $strand_code = trim($_POST['edit_strand_code'] ?? '');
        $strand_name = trim($_POST['edit_strand_name'] ?? '');
      

        if ($strand_code === '') {
        $errors['edit_strand_code'] = 'Strand Code is required.';
        }

        if ($strand_name === '') {
            $errors['edit_strand_name'] = 'Strand Name is required.';
        }

        if (!empty($errors)) {
            echo json_encode([
                'status' => 'error',
                'errors' => $errors
            ]);
            return;
        }

        Strand::update($id,[
            'code' => strtoupper($strand_code),
            'name' => $strand_name,
        ]);

         Logger::log(
        "Updated A Strand",
         "Updated the Strand Information."
         );

        echo json_encode([
            'status' => 'success',
            'message' => 'Strand updated successfully.'
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

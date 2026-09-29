<?php 

 namespace App\Controllers;

 use App\Core\Controller;
 use App\Helper\Response;
 use App\Models\Activity;
 use App\Models\Employee;
 use Dompdf\Dompdf;
 use PhpOffice\PhpSpreadsheet\Spreadsheet;
 use PhpOffice\PhpSpreadsheet\Writer\Csv;
 use PhpOffice\PhpSpreadsheet\Writer\Xlsx;



 class ActivityController extends Controller
 {

      public function index()
      {
        $user = Employee::find('1003'); 
        $this->render('/settings/activity', ['user' => $user]);
      }  
    
      public function show($id)
      {
    
        $activity = Activity::find($id);      
        Response::json($activity);

      }


      public function allActivity()
      {

        $all_activity = Activity::allActivity();
        Response::json($all_activity);

      }

      public function activityPDF(): void
     {
       
        $dompdf = new Dompdf();
        
        $range = ucfirst($_GET['range']);
        $bcp_logo = $this->imageRender('bcp-logo.png');
        $ched_logo = $this->imageRender('ched.png');

         $activity = Activity::allActivity(false);
        
         ob_start();
        
    
        $this->render('/pdf/activity',[

            'activities' => $activity,
            'school_image' => $bcp_logo,
            'ched_image' => $ched_logo,
            'range' => $range
            ]);

        $html = ob_get_clean();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        
        $dompdf->stream("{$range}_activity_logs.pdf", ["Attachment" => false]);
        
    }


    public function activityExcel()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'User_id');
        $sheet->setCellValue('B1', 'Action');
        $sheet->setCellValue('C1', 'Description');
        $sheet->setCellValue('D1', 'Created_At');
        $sheet->setCellValue('E1', 'IP_Address');

        $activities = Activity::allActivity(false);

        $row = 2;

        foreach($activities as $activity)
        {

            $sheet->setCellValue('A' . $row, $activity['user_id'] ?? 'null');
            $sheet->setCellValue('B' . $row, $activity['action']);
            $sheet->setCellValue('C' . $row, $activity['description']);
            $sheet->setCellValue('D' . $row, $activity['created_at']);
            $sheet->setCellValue('E' . $row, $activity['ip_address']);
        
            $row++ ;

        }
  

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="activity_logs.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');

        exit;
    }

     public function activityCSV()
    {

  
        while (ob_get_level()) {
            ob_end_clean();
        }

        

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();


        $activities = Activity::allCourses(false);

  
        $sheet->setCellValue('A1', 'User_id');
        $sheet->setCellValue('B1', 'Action');
        $sheet->setCellValue('C1', 'Description');
        $sheet->setCellValue('D1', 'Created_At');
        $sheet->setCellValue('E1', 'IP_Address');



        $row = 2;

        foreach ($activities as $activity) {
    
            $sheet->setCellValue('A' . $row, $activity['user_id'] ?? 'null');
            $sheet->setCellValue('B' . $row, $activity['action']);
            $sheet->setCellValue('C' . $row, $activity['description']);
            $sheet->setCellValue('D' . $row, $activity['created_at']);
            $sheet->setCellValue('E' . $row, $activity['ip_address']);

            $row++;
        }

        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="activity_log.csv"');
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


        if (!empty($data['ids'])) {
            $deleted = Activity::deleteMany($data['ids']);
            echo json_encode(['success' => (bool)$deleted]);
            exit;
        }

        echo json_encode(['success' => false]);
        exit;
    }

     public function allActivityWithoutPagination()
      {

         $all_activity = Activity::allActivity(false);
         Response::json($all_activity);         

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


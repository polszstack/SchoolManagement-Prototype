<?php 


 namespace App\Controllers;

 use App\Core\Controller;
use App\Helper\Logger;
use App\Helper\Response;
use App\Models\Approval;
use App\Models\Department;
use App\Models\Employee;
use Exception;

 class ReportApprovalController extends Controller
 {

    public function index()
    {   

        $departments = Department::all();
        $user = Employee::find('1003'); 
      
        $this->render('reports/report_approval', ['departments' => $departments,'user' => $user]);

    }

    public function allApproval()
    {
        $all_activity = Approval::allApproval();
        Response::json($all_activity);
    }

  

    public function store()
    {

        header('Content-Type: application/json');

        $errors = [];

        $title = trim($_POST['report_title'] ?? '');
            $description = trim($_POST['report_description'] ?? '');
            $submit_by = 1003; 

            $filePath = null; 

    
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../uploads/approvals/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            $tmpName = $_FILES['attachment']['tmp_name'];
            $originalName = basename($_FILES['attachment']['name']);
            $ext = pathinfo($originalName, PATHINFO_EXTENSION);

            $allowed = ['jpg','jpeg','png','pdf'];
            if (!in_array(strtolower($ext), $allowed)) {
                throw new Exception("File type not allowed.");
            }

            $newFileName = 'approval_' . uniqid() . '.' . $ext;
            $filePath = $uploadDir . $newFileName;

            if (!move_uploaded_file($tmpName, $filePath)) {
                throw new Exception("Failed to move uploaded file.");
            }

            $filePath = 'uploads/approvals/' . $newFileName;
        }


        if ($title === '') {
        $errors['title'] = 'School Year Name is required.';
        }

        if (!empty($errors)) {
            echo json_encode([
                'status' => 'error',
                'errors' => $errors
            ]);
            return;
        }
       
         Approval::create([
                'title' => $title,
                'description' => $description,
                'submit_by' => $submit_by,
                'file_path' => $filePath
            ]);

            Logger::log(
                "Created A New Report Approval",
                "Created A New Report Approval for System"
            );

        echo json_encode([
            'status' => 'success',
            'message' => 'New Report  created successfully.'
        ]);

    }


     public function destroy()
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);


        if (!empty($data['ids'])) {
            $deleted = Approval::deleteMany($data['ids']);
            echo json_encode(['success' => (bool)$deleted]);
            exit;
        }

        echo json_encode(['success' => false]);
        exit;
    }


    public function approved($id)
    {

         header('Content-Type: application/json');

     
        Approval::update($id,[

            'decision' => 'Approved',
            'approved_at' => date('Y-m-d H:i:s'),
            'approver_id' => 1003 
        ]);

        
         Logger::log(
        "Approval of Request",
         "Approval the Request Information."
         );

        echo json_encode([
            'status' => 'success',
            'message' => 'Course updated successfully.'
        ]);


    }


    public function reject($id)
    {
         header('Content-Type: application/json');

     
        Approval::update($id,[

            'decision' => 'Rejected',
            'approved_at' => date('Y-m-d H:i:s'),
            'approver_id' => null 
        ]);

        
         Logger::log(
        "Reject of Request",
         "Reject the Request Information."
         );

        echo json_encode([
            'status' => 'success',
            'message' => 'Course updated successfully.'
        ]);
    }

   


 }
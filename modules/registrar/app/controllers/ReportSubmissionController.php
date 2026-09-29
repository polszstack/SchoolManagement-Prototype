<?php 


  namespace App\Controllers;

  use App\Core\Controller;
  use App\Helper\Logger;
  use App\Helper\Response;
  use App\Models\Department;
  use App\Models\Employee;
  use App\Models\Report;
  use App\Models\Submission;
  use Exception;

 class ReportSubmissionController extends Controller 
 {

    public function index()
    { 

        $departments = Department::all();
        $reports = Report::all();
        $user = Employee::find('1003'); 
      
        $this->render('reports/report_submission', ['user' => $user, 'reports' => $reports, 'departments' => $departments ]);

    }


    public function allReports()
    {
       $all_activity = Submission::allReports();
        Response::json($all_activity);
    }


     public function store()
    {

        header('Content-Type: application/json');

        $errors = [];

        $title = trim($_POST['report_title'] ?? '');
            $description = trim($_POST['report_description'] ?? '');
            $report = trim($_POST['report']);
            $department = trim($_POST['department']);
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
       
         Submission::create([
                'title' => $title,
                'report_type' => $report,
                'description' => $description,
                'submitted_by' => $submit_by,
                'file_path' => $filePath,
                'department_id' => $department,
                'submitted_at' => date('Y-m-d H:i:s'),
            ]);

            Logger::log(
                "Created A New Report Submission",
                "Created A New Report Submission for System"
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
            $deleted = Submission::deleteMany($data['ids']);
            echo json_encode(['success' => (bool)$deleted]);
            exit;
        }

        echo json_encode(['success' => false]);
        exit;
    }



 }

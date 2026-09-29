<?php

require_once __DIR__ . '/../models/Departments.php';
require_once __DIR__ . '/../models/Reports.php';
require_once __DIR__ . '/../models/ReportTypes.php';
require_once __DIR__ . '/../models/Employee.php';

class ReportSubmmisionManagementController
{
    public function index()
    {
        $departmentsModel = new Departments();
        $reportsModel    = new Reports();
        $reportTypeModel = new ReportType();
        $employeeModel    = new Employee();

        $departments = $departmentsModel->all();
        $reports     = $reportsModel->all();
        $reportTypes = $reportTypeModel->all();
        $employees   = $employeeModel->all();

        require __DIR__ . '/../views/report-submission-management/index.php';
    }

    public function create()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header("Location: " . ($_SERVER['HTTP_REFERER'] ?? "index.php?url=report-submission-management"));
                exit;
            }

            $title        = trim($_POST['title'] ?? null);
            $description  = trim($_POST['description'] ?? null);
            $department   = $_POST['department'] ?? null;
            $submitted_by = $_POST['submitted_by'] ?? null;
            $report_type = $_POST['report_type'] ?? null;

            if (!$title || !$description || !$department || !$submitted_by || !$report_type) {
                throw new Exception("All required fields must be filled.");
            }

            $filePath = null;
            if (!empty($_FILES['attachment']['name'])) {

                $uploadDir = __DIR__ . "/../../public/files/reports/";
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $ext = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
                $fileName = time() . "_" . substr(md5(uniqid()), 0, 6) . "." . $ext;

                $target = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['attachment']['tmp_name'], $target)) {
                    $filePath = "files/reports/" . $fileName;
                }
            }

            $data = [
                'title'         => $title,
                'description'   => $description,
                'department_id' => $department,
                'submitted_by'  => $submitted_by,
                'file_path'     => $filePath,
                'report_type'   => $report_type
            ];

            $reportModel = new Reports();
            $reportModel->create($data);

            $_SESSION['success'] = "Report submitted successfully.";
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? "index.php?url=report-submission-management"));
        exit;
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reportId = $_POST['report_id'] ?? null;

            if (!$reportId) {
                $_SESSION['error'] = "Invalid request.";
            } else {
                $reportModel = new Reports();
                $reportModel->delete((int)$reportId);
                $_SESSION['success'] = "Report deleted successfully.";
            }

            header("Location: " . ($_SERVER['HTTP_REFERER'] ?? "index.php?url=report-submission-management"));
            exit;
        }
    }
}

<?php

require_once __DIR__ . '/../models/Issues.php';
require_once __DIR__ . '/../models/Departments.php';
require_once __DIR__ . '/../models/Employee.php';

class ConcernIssueTrackingController
{
    public function index()
    {
        $issuesModel      = new Issues();
        $departmentsModel = new Departments();
        $employeeModel    = new Employee();

        $status = $_GET['status'] ?? 'all';
        $issues = $status === 'all' ? $issuesModel->all() : $issuesModel->getByStatus($status);

        if ($status === 'all') {
            $issues = $issuesModel->all();
        } else {
            $issues = $issuesModel->getByStatus($status);
        }

        $departments = $departmentsModel->all();
        $employees   = $employeeModel->all();

        require __DIR__ . '/../views/concerns-issue-tracking/index.php';
    }

    public function create()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header("Location: " . ($_SERVER['HTTP_REFERER'] ?? "index.php?url=concern-issue-tracking"));
                exit;
            }

            $title        = trim($_POST['title'] ?? null);
            $description  = trim($_POST['description'] ?? null);
            $department   = $_POST['department'] ?? null;
            $submitted_by = $_POST['submitted_by'] ?? null;

            if (!$title || !$description || !$department || !$submitted_by) {
                throw new Exception("All required fields must be filled.");
            }

            $attachmentPath = null;

            // Handle attachment
            if (!empty($_FILES['attachment']['name'])) {
                $uploadDir = __DIR__ . "/../../public/files/issues/";
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

                $fileExt = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
                $hash    = substr(md5(uniqid('', true)), 0, 6);
                $fileName = time() . "_" . $hash . "." . $fileExt;

                $targetPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['attachment']['tmp_name'], $targetPath)) {
                    $attachmentPath = "files/issues/" . $fileName;
                }
            }

            $data = [
                'title'        => $title,
                'description'  => $description,
                'department'   => $department,
                'submitted_by' => $submitted_by,
                'file_path'    => $attachmentPath
            ];

            $issuesModel = new Issues();
            $issuesModel->create($data);

            $_SESSION['success'] = "Concern submitted successfully.";
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage() ?: "Something went wrong while submitting the concern.";
        }

        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? "index.php?url=concern-issue-tracking"));
        exit;
    }
    public function resolve()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $issueId = $_POST['issue_id'] ?? null;

            if (!$issueId) {
                $_SESSION['error'] = "Invalid request.";
            } else {
                $issuesModel = new Issues();
                $issuesModel->updateStatus((int)$issueId, 'resolved');
                $_SESSION['success'] = "Issue marked as resolved.";
            }

            header("Location: " . ($_SERVER['HTTP_REFERER'] ?? "index.php?url=concern-issue-tracking"));
            exit;
        }
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $issueId = $_POST['issue_id'] ?? null;

            if (!$issueId) {
                $_SESSION['error'] = "Invalid request.";
            } else {
                $issuesModel = new Issues();
                $issuesModel->delete((int)$issueId);
                $_SESSION['success'] = "Issue deleted successfully.";
            }

            header("Location: " . ($_SERVER['HTTP_REFERER'] ?? "index.php?url=concern-issue-tracking"));
            exit;
        }
    }
}

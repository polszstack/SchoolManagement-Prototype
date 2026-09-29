<?php

require_once __DIR__ . '/../models/Approvals.php';
require_once __DIR__ . '/../models/Departments.php';
require_once __DIR__ . '/../models/Employee.php';

class ApprovalDecisionSupportController
{
    public function index()
    {
        $approvalModel    = new Approvals();
        $departmentsModel = new Departments();
        $employeeModel    = new Employee();

        $departmentId = $_GET['department'] ?? null;

        if ($departmentId) {
            $approvals = $approvalModel->getByDepartment($departmentId);
        } else {
            $approvals = $approvalModel->all();
        }

        $departments = $departmentsModel->all();

        $employees = $employeeModel->all();

        require __DIR__ . '/../views/approval-decision-support/index.php';
    }

    public function create()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header("Location: " . ($_SERVER['HTTP_REFERER'] ?? "index.php?url=approval-decision-support"));
                exit;
            }

            $title      = trim($_POST['title'] ?? null);
            $description = trim($_POST['description'] ?? null);
            $department  = $_POST['department'] ?? null;
            $submit_by   = $_POST['submit_by'] ?? null;
            $approver_id = $_POST['approver_id'] ?? null;

            $attachmentName = null;
            if (!empty($_FILES['attachment']['name'])) {
                $uploadDir = __DIR__ . "/../../public/files/";
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

                $fileExt = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
                $hash = substr(md5(uniqid('', true)), 0, 6);
                $attachmentName = time() . "_" . $hash . "." . $fileExt;

                $targetPath = $uploadDir . $attachmentName;

                if (move_uploaded_file($_FILES['attachment']['tmp_name'], $targetPath)) {
                    $attachmentName = "files/" . $attachmentName;
                } else {
                    $attachmentName = null;
                }
            }

            $data = [
                'title'       => $title,
                'description' => $description,
                'remarks'     => null,
                'submit_by'   => $submit_by ?: null,
                'department'  => $department ?: null,
                'approver_id' => $approver_id ?: null,
                'file_path'   => $attachmentName
            ];

            $approvalModel = new Approvals();
            $approvalModel->create($data);

            $_SESSION['success'] = "Approval request submitted successfully.";
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage() ?: "Something went wrong while submitting the approval.";
        }

        $redirectTo = $_SERVER['HTTP_REFERER'] ?? "index.php?url=approval-decision-support";
        header("Location: $redirectTo");
        exit;
    }

    public function updateDecision()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("Invalid request method.");
            }

            $approvalId = $_POST['approval_id'] ?? null;
            $action     = $_POST['action'] ?? null;
            $remarks    = trim($_POST['remarks'] ?? null);

            if (!$approvalId || !in_array($action, ['approved', 'rejected'])) {
                throw new Exception("Invalid input.");
            }

            $approvalsModel = new Approvals();

            $approvalsModel->updateDecision($approvalId, $action, $remarks);

            $_SESSION['success'] = "Approval updated successfully.";
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $approvalId = $_POST['approval_id'] ?? null;

            if (!$approvalId) {
                $_SESSION['error'] = "Invalid input. Approval ID is missing.";
            } else {
                $approvalsModel = new Approvals();
                $deleted = $approvalsModel->delete((int)$approvalId);

                if ($deleted) {
                    $_SESSION['success'] = "Approval deleted successfully.";
                } else {
                    $_SESSION['error'] = "Failed to delete approval.";
                }
            }

            header("Location: " . ($_SERVER['HTTP_REFERER'] ?? "index.php?url=approval-decision-support"));
            exit;
        }
    }
}

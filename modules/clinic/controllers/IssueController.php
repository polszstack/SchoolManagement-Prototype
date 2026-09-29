<?php
include_once __DIR__ . '/../../../auth/session.php';
include_once __DIR__ . '/../classes/Issues.php';
include_once __DIR__ . '/../classes/User.php';

header('Content-Type: application/json');

$userClass = new User();
$userInfo  = $userClass->userSession();

if (!$userInfo) {
    echo json_encode(['success' => false, 'message' => 'Session expired.']);
    exit;
}

$issuesClass = new Issues();
$action      = $_REQUEST['action'] ?? '';

switch ($action) {

    // ------------------------------------------------------------------
    // GET – fetch issues (with optional status filter + search)
    // ------------------------------------------------------------------
    case 'list':
        $status = $_GET['status'] ?? 'all';
        $search = trim($_GET['search'] ?? '');

        $issues = $issuesClass->getIssues($status, $search);
        echo json_encode(['success' => true, 'data' => $issues]);
        break;

    // ------------------------------------------------------------------
    // GET – fetch single issue detail
    // ------------------------------------------------------------------
    case 'get':
        $issueId = (int) ($_GET['issue_id'] ?? 0);

        if (!$issueId) {
            echo json_encode(['success' => false, 'message' => 'Invalid issue ID.']);
            exit;
        }

        $issue = $issuesClass->getIssue($issueId);

        if (!$issue) {
            echo json_encode(['success' => false, 'message' => 'Issue not found.']);
            exit;
        }

        echo json_encode(['success' => true, 'data' => $issue]);
        break;

    // ------------------------------------------------------------------
    // POST – submit a new issue (with optional file attachment)
    // ------------------------------------------------------------------
    case 'submit':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
            exit;
        }

        $title        = trim($_POST['title']       ?? '');
        $description  = trim($_POST['description'] ?? '');
        $departmentId = (int) ($_POST['department'] ?? 0);
        $submittedBy  = $_SESSION['employee_id']   ?? null;

        if ($title === '') {
            echo json_encode(['success' => false, 'message' => 'Title is required.']);
            exit;
        }
        if (!$departmentId) {
            echo json_encode(['success' => false, 'message' => 'Please select a department.']);
            exit;
        }
        if (!$submittedBy) {
            echo json_encode(['success' => false, 'message' => 'Session expired.']);
            exit;
        }

        // --- Optional file attachment ---
        $filePath = null;
        if (!empty($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {

            $allowedMimes = [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'image/png',
                'image/jpeg',
            ];
            $allowedExts  = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'png', 'jpg', 'jpeg'];
            $maxSizeBytes = 10 * 1024 * 1024;

            $fileExt  = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
            $fileMime = mime_content_type($_FILES['file']['tmp_name']);

            if (!in_array($fileExt, $allowedExts, true) || !in_array($fileMime, $allowedMimes, true)) {
                echo json_encode(['success' => false, 'message' => 'Invalid file type.']);
                exit;
            }
            if ($_FILES['file']['size'] > $maxSizeBytes) {
                echo json_encode(['success' => false, 'message' => 'File exceeds the 10 MB size limit.']);
                exit;
            }

            $uploadDir = __DIR__ . '/../../../uploads/concerns/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            $uniqueName = uniqid('concern_', true) . '.' . $fileExt;
            if (!move_uploaded_file($_FILES['file']['tmp_name'], $uploadDir . $uniqueName)) {
                echo json_encode(['success' => false, 'message' => 'Failed to save the uploaded file.']);
                exit;
            }

            $filePath = 'uploads/concerns/' . $uniqueName;
        }

        $issueId = $issuesClass->submitIssue($title, $description, $departmentId, $submittedBy, $filePath);
        echo json_encode(['success' => true, 'message' => 'Issue logged successfully.', 'data' => ['issue_id' => $issueId]]);
        break;

    // ------------------------------------------------------------------
    // POST – update issue status (open ↔ resolved)
    // ------------------------------------------------------------------
    case 'update_status':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
            exit;
        }

        $issueId = (int) ($_POST['issue_id'] ?? 0);
        $status  = trim($_POST['status']   ?? '');

        if (!$issueId) {
            echo json_encode(['success' => false, 'message' => 'Invalid issue ID.']);
            exit;
        }

        $result = $issuesClass->updateStatus($issueId, $status);

        if (!$result) {
            echo json_encode(['success' => false, 'message' => 'Invalid status or update failed.']);
            exit;
        }

        echo json_encode(['success' => true, 'message' => 'Status updated.']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action.']);
        break;
}
<?php
include_once __DIR__ . '/../../../auth/session.php';
include_once __DIR__ . '/../classes/Report.php';
include_once __DIR__ . '/../classes/Department.php';
include_once __DIR__ . '/../classes/User.php';

header('Content-Type: application/json');

// ── Guard: only accept XHR / fetch ───────────────────────────────────────────
$userClass = new User();
$userInfo  = $userClass->userSession();

if (!$userInfo) {
    echo json_encode(['success' => false, 'message' => 'Session expired.']);
    exit;
}

$reportClass = new Report();
$action      = $_REQUEST['action'] ?? '';

// ── Router ────────────────────────────────────────────────────────────────────
switch ($action) {

    // ------------------------------------------------------------------
    // GET  list (optionally filtered by department)
    // ------------------------------------------------------------------
    case 'list':
        $departmentId = isset($_GET['department_id']) && $_GET['department_id'] !== ''
            ? (int) $_GET['department_id']
            : null;

        // School Directress sees all; others scoped to their department
        if ($userInfo['role'] !== 'School Directress' && $departmentId === null) {
            $departmentId = $_SESSION['department_id'] ?? null;
        }

        $reports = $reportClass->getReports($departmentId);
        echo json_encode(['success' => true, 'data' => $reports]);
        break;

    // ------------------------------------------------------------------
    // POST submit a new report
    // ------------------------------------------------------------------
    case 'submit':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
            exit;
        }

        $title      = trim($_POST['title']       ?? '');
        $description = trim($_POST['description'] ?? '');
        $reportType = (int) ($_POST['report_type'] ?? 0);

        if ($title === '') {
            echo json_encode(['success' => false, 'message' => 'Title is required.']);
            exit;
        }
        if ($reportType === 0) {
            echo json_encode(['success' => false, 'message' => 'Please select a report type.']);
            exit;
        }

        // File validation
        if (empty($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'A valid file upload is required.']);
            exit;
        }

        $allowedMimes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];
        $allowedExts  = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];
        $maxSizeBytes = 10 * 1024 * 1024;

        $fileExt  = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        $fileMime = mime_content_type($_FILES['file']['tmp_name']);

        if (!in_array($fileExt, $allowedExts, true) || !in_array($fileMime, $allowedMimes, true)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type. Only PDF, Word, and Excel files are allowed.']);
            exit;
        }
        if ($_FILES['file']['size'] > $maxSizeBytes) {
            echo json_encode(['success' => false, 'message' => 'File exceeds the 10 MB size limit.']);
            exit;
        }

        $uploadDir = __DIR__ . '/../../../uploads/reports/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $uniqueName = uniqid('report_', true) . '.' . $fileExt;
        $destPath   = $uploadDir . $uniqueName;

        if (!move_uploaded_file($_FILES['file']['tmp_name'], $destPath)) {
            echo json_encode(['success' => false, 'message' => 'Failed to save the uploaded file.']);
            exit;
        }

        $filePath = 'uploads/reports/' . $uniqueName;
        $reportId = $reportClass->submitReport($title, $description, $filePath, $reportType);

        echo json_encode(['success' => true, 'message' => 'Report submitted successfully.', 'data' => ['report_id' => $reportId]]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action.']);
        break;
}
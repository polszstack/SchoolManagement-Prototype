<?php
ini_set('display_errors', 0);
error_reporting(0);

include_once __DIR__ . '/../../../auth/session.php';
include_once __DIR__ . '/../classes/Approval.php';
include_once __DIR__ . '/../classes/User.php';

header('Content-Type: application/json');

$userClass = new User();
$userInfo  = $userClass->userSession();

if (!$userInfo) {
    echo json_encode(['success' => false, 'message' => 'Session expired.']);
    exit;
}

$action        = $_POST['action'] ?? $_GET['action'] ?? '';
$approvalClass = new Approval();
$departmentId  = $userInfo['department_id'] ?? null;

switch ($action) {

    // ── SUBMIT ───────────────────────────────────────────────────────────────────
    case 'submit':
        $title       = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $submitBy    = $_SESSION['employee_id'] ?? null;

        if (!$title) {
            echo json_encode(['success' => false, 'message' => 'Title is required.']);
            exit;
        }
        if (!$submitBy) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized. Please log in.']);
            exit;
        }

        $filePath = null;
        if (!empty($_FILES['attachment']['name'])) {
            $uploadDir = __DIR__ . '/../uploads/approvals/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            $allowedTypes = [
                'application/pdf', 'image/jpeg', 'image/png',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ];
            $fileType = mime_content_type($_FILES['attachment']['tmp_name']);

            if (!in_array($fileType, $allowedTypes)) {
                echo json_encode(['success' => false, 'message' => 'Invalid file type. Allowed: PDF, JPG, PNG, DOC, DOCX.']);
                exit;
            }
            if ($_FILES['attachment']['size'] > 5 * 1024 * 1024) {
                echo json_encode(['success' => false, 'message' => 'File exceeds the 5 MB limit.']);
                exit;
            }

            $ext      = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
            $fileName = uniqid('approval_', true) . '.' . $ext;

            if (!move_uploaded_file($_FILES['attachment']['tmp_name'], $uploadDir . $fileName)) {
                echo json_encode(['success' => false, 'message' => 'Failed to upload file.']);
                exit;
            }
            $filePath = 'uploads/approvals/' . $fileName;
        }

        $result = $approvalClass->submitApproval($title, $submitBy, $description, $filePath);
        echo json_encode([
            'success' => (bool) $result,
            'message' => $result ? 'Approval submitted successfully.' : 'Failed to submit. Please try again.'
        ]);
        break;

    // ── GET ───────────────────────────────────────────────────────────────────
    case 'get':
        $filter    = $_GET['filter'] ?? 'all';
        $approvals = $approvalClass->getApprovals($filter, $departmentId);
        echo json_encode(['success' => true, 'data' => $approvals]);
        break;

    // ── APPROVE ───────────────────────────────────────────────────────────────
    case 'approve':
        $approvalId = intval($_POST['approval_id'] ?? 0);
        $remarks    = trim($_POST['remarks'] ?? '');
        $approverId = $_SESSION['employee_id'] ?? null;

        if (!$approvalId) { echo json_encode(['success' => false, 'message' => 'Invalid approval ID.']); exit; }
        if (!$approverId) { echo json_encode(['success' => false, 'message' => 'Unauthorized.']); exit; }

        $result = $approvalClass->updateDecision($approvalId, 'approved', $approverId, $remarks);
        echo json_encode(['success' => (bool) $result, 'message' => $result ? 'Approval accepted.' : 'Failed to update decision.']);
        break;

    // ── REJECT ────────────────────────────────────────────────────────────────
    case 'reject':
        $approvalId = intval($_POST['approval_id'] ?? 0);
        $remarks    = trim($_POST['remarks'] ?? '');
        $approverId = $_SESSION['employee_id'] ?? null;

        if (!$approvalId) { echo json_encode(['success' => false, 'message' => 'Invalid approval ID.']); exit; }
        if (!$approverId) { echo json_encode(['success' => false, 'message' => 'Unauthorized.']); exit; }

        $result = $approvalClass->updateDecision($approvalId, 'rejected', $approverId, $remarks);
        echo json_encode(['success' => (bool) $result, 'message' => $result ? 'Approval rejected.' : 'Failed to update decision.']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action.']);
        break;
}
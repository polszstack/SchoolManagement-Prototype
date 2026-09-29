<?php
include_once __DIR__ . '/../../../auth/session.php';
include_once __DIR__ . '/../classes/Students.php';
include_once __DIR__ . '/../classes/User.php';
// NOTE: paths above mirror the reports controller — adjust depth/folder
// names if StudentsController.php doesn't live in the same location.

header('Content-Type: application/json');

// ── Guard: only accept authenticated requests ────────────────────────────────
$userClass = new User();
$userInfo  = $userClass->userSession();

if (!$userInfo) {
    echo json_encode(['success' => false, 'message' => 'Session expired.']);
    exit;
}

$studentsClass = new Students();
$action        = $_REQUEST['action'] ?? '';

// ── Router ────────────────────────────────────────────────────────────────────
switch ($action) {

    // ------------------------------------------------------------------
    // GET  list (filtered + paginated)
    // ------------------------------------------------------------------
    case 'list':
        $filters = [
            'search'     => trim($_GET['search'] ?? ''),
            'year_level' => $_GET['year_level'] ?? '',
            'section'    => $_GET['section'] ?? '',
            'course'     => $_GET['course'] ?? '',
            'risk'       => $_GET['risk'] ?? '',
            'status'     => $_GET['status'] ?? '',
        ];
        $page     = max(1, (int) ($_GET['page'] ?? 1));
        $pageSize = 10;

        $result   = $studentsClass->getList($filters, $page, $pageSize);
        $students = $result['rows']; // already formatted (initials, year_section, updated_at_display) by Students::getList()

        echo json_encode([
            'success' => true,
            'data' => [
                'students' => $students,
                'pagination' => [
                    'total'      => $result['total'],
                    'page'       => $page,
                    'pageSize'   => $pageSize,
                    'count'      => count($students),
                    'totalPages' => (int) ceil($result['total'] / $pageSize),
                ],
            ],
        ]);
        break;

    // ------------------------------------------------------------------
    // GET  profile (overview + all history tabs)
    // ------------------------------------------------------------------
    case 'profile':
        $studentNumber = $_GET['student_number'] ?? '';
        if ($studentNumber === '') {
            echo json_encode(['success' => false, 'message' => 'student_number is required.']);
            exit;
        }

        $overview = $studentsClass->getOverview($studentNumber);
        if (!$overview) {
            echo json_encode(['success' => false, 'message' => 'Student not found.']);
            exit;
        }

        if (!empty($overview['birth_date'])) {
            $overview['birth_date'] = date('M d, Y', strtotime($overview['birth_date']));
        }

        $overview['remarks_history']     = formatHistoryRows($studentsClass->getRemarks($studentNumber));
        $overview['appointment_history'] = formatHistoryRows($studentsClass->getAppointmentHistory($studentNumber));
        $overview['incident_history']    = formatHistoryRows($studentsClass->getIncidentHistory($studentNumber));
        $overview['documents']           = formatHistoryRows($studentsClass->getDocuments($studentNumber));

        $cases = $studentsClass->getCaseHistory($studentNumber);
        $overview['case_history'] = formatCaseHistory($cases);
        $overview['case_summary'] = summarizeCases($cases);

        echo json_encode(['success' => true, 'data' => $overview]);
        break;

    // ------------------------------------------------------------------
    // POST save a guidance remark
    // ------------------------------------------------------------------
    case 'save_remark':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
            exit;
        }

        $input         = json_decode(file_get_contents('php://input'), true) ?? [];
        $studentNumber = $input['student_number'] ?? '';
        $remarks       = trim($input['remarks'] ?? '');

        if ($studentNumber === '' || $remarks === '') {
            echo json_encode(['success' => false, 'message' => 'student_number and remarks are required.']);
            exit;
        }

        $ok = $studentsClass->saveRemark($studentNumber, $remarks);
        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'Remark saved successfully.' : 'Failed to save remark.',
        ]);
        break;

    // ------------------------------------------------------------------
    // POST upload a student document
    // ------------------------------------------------------------------
    case 'upload_document':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
            exit;
        }

        $studentNumber = $_POST['student_number'] ?? '';
        $documentType  = $_POST['document_type'] ?? '';

        if ($studentNumber === '') {
            echo json_encode(['success' => false, 'message' => 'student_number is required.']);
            exit;
        }

        if (empty($_FILES['document_file']) || $_FILES['document_file']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'A valid file upload is required.']);
            exit;
        }

        $allowedMimes = [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];
        $allowedExts  = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
        $maxSizeBytes = 10 * 1024 * 1024;

        $fileExt  = strtolower(pathinfo($_FILES['document_file']['name'], PATHINFO_EXTENSION));
        $fileMime = mime_content_type($_FILES['document_file']['tmp_name']);

        if (!in_array($fileExt, $allowedExts, true) || !in_array($fileMime, $allowedMimes, true)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type. Only PDF, Word, and image files are allowed.']);
            exit;
        }
        if ($_FILES['document_file']['size'] > $maxSizeBytes) {
            echo json_encode(['success' => false, 'message' => 'File exceeds the 10 MB size limit.']);
            exit;
        }

        $uploadDir = __DIR__ . "/../../../uploads/students/{$studentNumber}/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $uniqueName = uniqid('doc_', true) . '.' . $fileExt;
        $destPath   = $uploadDir . $uniqueName;

        if (!move_uploaded_file($_FILES['document_file']['tmp_name'], $destPath)) {
            echo json_encode(['success' => false, 'message' => 'Failed to save the uploaded file.']);
            exit;
        }

        $filePath = "uploads/students/{$studentNumber}/{$uniqueName}";

        $ok = $studentsClass->saveDocument([
            'student_number' => $studentNumber,
            'uploaded_by'    => $userInfo['employee_id'] ?? null,
            'document_type'  => $documentType,
            'file_name'      => $_FILES['document_file']['name'],
            'file_path'      => $filePath,
        ]);

        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'Document uploaded successfully.' : 'Failed to save document record.',
        ]);
        break;

    // ------------------------------------------------------------------
    // GET  export
    // NOTE: stubbed. Confirm desired format (CSV vs XLSX vs PDF) before building.
    // ------------------------------------------------------------------
    case 'export':
        echo json_encode(['success' => false, 'message' => 'Export not implemented yet — confirm format (CSV/XLSX/PDF) first.']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action.']);
        break;
}

// ── Formatting helpers (display formatting kept out of Students.php) ────────
function formatHistoryRows(array $rows): array
{
    return array_map(function ($row) {
        if (!empty($row['date'])) {
            $row['date'] = date('M d, Y', strtotime($row['date']));
        }
        if (!empty($row['uploaded_at'])) {
            $row['date'] = date('M d, Y', strtotime($row['uploaded_at']));
        }
        return $row;
    }, $rows);
}

function formatCaseHistory(array $cases): array
{
    return array_map(function ($case) {
        $case['opened_at_display'] = date('M d, Y', strtotime($case['opened_at']));
        $case['closed_at_display'] = !empty($case['closed_at']) ? date('M d, Y', strtotime($case['closed_at'])) : null;
        return $case;
    }, $cases);
}

// Quick-glance counts for the Overview badge cluster (e.g. "4 Cases — 1 In
// Progress, 3 Closed"), broken down by both status and case_type so a
// pattern like "mostly self-referrals" vs "mostly incidents" is visible
// without opening the full Cases tab.
function summarizeCases(array $cases): array
{
    $summary = [
        'total' => count($cases),
        'by_status' => ['Open' => 0, 'In Progress' => 0, 'Closed' => 0],
        'by_type' => ['Referral' => 0, 'Walk-in' => 0, 'Self Referral' => 0, 'Incident' => 0],
    ];

    foreach ($cases as $case) {
        if (isset($summary['by_status'][$case['status']])) {
            $summary['by_status'][$case['status']]++;
        }
        if (isset($summary['by_type'][$case['case_type']])) {
            $summary['by_type'][$case['case_type']]++;
        }
    }

    return $summary;
}
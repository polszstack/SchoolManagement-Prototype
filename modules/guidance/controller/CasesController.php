<?php
include_once __DIR__ . '/../../../auth/session.php';
include_once __DIR__ . '/../classes/Cases.php';
include_once __DIR__ . '/../classes/User.php';
// NOTE: paths mirror StudentsController.php — adjust depth/folder names
// if CasesController.php doesn't live at the same location.

header('Content-Type: application/json');

// ── Guard: only accept authenticated requests ────────────────────────────────
$userClass = new User();
$userInfo  = $userClass->userSession();

if (!$userInfo) {
    echo json_encode(['success' => false, 'message' => 'Session expired.']);
    exit;
}

$casesClass = new Cases();
$action     = $_REQUEST['action'] ?? '';

// ── Router ────────────────────────────────────────────────────────────────────
switch ($action) {

    // ------------------------------------------------------------------
    // GET  list (filtered + paginated)
    // ------------------------------------------------------------------
    case 'list':
        $filters = [
            'search'       => trim($_GET['search'] ?? ''),
            'status'       => $_GET['status'] ?? '',
            'priority'     => $_GET['priority'] ?? '',
            'case_type'    => $_GET['case_type'] ?? '',
            'counselor_id' => $_GET['counselor_id'] ?? '',
        ];
        $page     = max(1, (int) ($_GET['page'] ?? 1));
        $pageSize = 10;

        $result = $casesClass->getList($filters, $page, $pageSize);
        $cases  = array_map('formatCaseRow', $result['rows']);

        echo json_encode([
            'success' => true,
            'data' => [
                'cases' => $cases,
                'pagination' => [
                    'total'      => $result['total'],
                    'page'       => $page,
                    'pageSize'   => $pageSize,
                    'count'      => count($cases),
                    'totalPages' => (int) ceil($result['total'] / $pageSize),
                ],
            ],
        ]);
        break;

    // ------------------------------------------------------------------
    // GET  details (overview + referral + counseling sessions)
    // ------------------------------------------------------------------
    case 'details':
        $caseId = (int) ($_GET['case_id'] ?? 0);
        if (!$caseId) {
            echo json_encode(['success' => false, 'message' => 'case_id is required.']);
            exit;
        }

        $overview = $casesClass->getCaseOverview($caseId);
        if (!$overview) {
            echo json_encode(['success' => false, 'message' => 'Case not found.']);
            exit;
        }

        $overview['opened_at_display'] = date('M d, Y', strtotime($overview['opened_at']));
        $overview['closed_at_display'] = !empty($overview['closed_at']) ? date('M d, Y', strtotime($overview['closed_at'])) : null;

        $overview['referral'] = $overview['case_type'] === 'Referral'
            ? formatReferral($casesClass->getReferral($caseId))
            : null;

        $overview['sessions'] = array_map('formatSession', $casesClass->getCounselingSessions($caseId));

        echo json_encode(['success' => true, 'data' => $overview]);
        break;

    // ------------------------------------------------------------------
    // GET  counselors (Guidance and Counseling Office department, for
    // assignment dropdowns)
    // ------------------------------------------------------------------
    case 'counselors':
        echo json_encode(['success' => true, 'data' => $casesClass->getCounselors()]);
        break;

    // ------------------------------------------------------------------
    // GET  unlinked_incidents — incidents with no case yet, for the
    // "Select Incident" picker when case_type = 'Incident'
    // ------------------------------------------------------------------
    case 'unlinked_incidents':
        $incidents = array_map('formatUnlinkedIncident', $casesClass->getUnlinkedIncidents());
        echo json_encode(['success' => true, 'data' => $incidents]);
        break;

    // ------------------------------------------------------------------
    // POST create a case directly (Walk-in / Self Referral), or from an
    // existing incident (case_type = 'Incident' — strictly requires an
    // incident_id; there is no freeform "Incident" case without one)
    // ------------------------------------------------------------------
    case 'create_case':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
            exit;
        }

        $input    = json_decode(file_get_contents('php://input'), true) ?? [];
        $caseType = $input['case_type'] ?? '';
        $counselorId = $input['counselor_id'] ?? '';

        if ($counselorId === '' || !in_array($caseType, ['Walk-in', 'Self Referral', 'Incident'], true)) {
            echo json_encode(['success' => false, 'message' => 'counselor_id and a valid case_type are required.']);
            exit;
        }

        try {
            if ($caseType === 'Incident') {
                $incidentId = $input['incident_id'] ?? '';
                if ($incidentId === '') {
                    echo json_encode(['success' => false, 'message' => 'An existing incident must be selected for an Incident-type case.']);
                    exit;
                }

                $caseId = $casesClass->createCaseFromIncident([
                    'incident_id'   => $incidentId,
                    'counselor_id'  => $counselorId,
                    'priority'      => $input['priority'] ?? 'Medium',
                    'summary'       => trim($input['summary'] ?? ''),
                ]);
            } else {
                $studentNumber = $input['student_number'] ?? '';
                if ($studentNumber === '') {
                    echo json_encode(['success' => false, 'message' => 'student_number is required.']);
                    exit;
                }

                $caseId = $casesClass->createCase([
                    'student_number' => $studentNumber,
                    'counselor_id'   => $counselorId,
                    'case_type'      => $caseType,
                    'priority'       => $input['priority'] ?? 'Medium',
                    'summary'        => trim($input['summary'] ?? ''),
                ]);
            }

            echo json_encode(['success' => true, 'message' => 'Case created successfully.', 'data' => ['case_id' => $caseId]]);
        } catch (RuntimeException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    // ------------------------------------------------------------------
    // POST submit a referral (creates the case + referral together)
    // ------------------------------------------------------------------
    case 'submit_referral':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? [];

        $required = ['student_number', 'counselor_id', 'referred_by', 'referral_source', 'referral_reason'];
        foreach ($required as $field) {
            if (empty($input[$field])) {
                echo json_encode(['success' => false, 'message' => "{$field} is required."]);
                exit;
            }
        }

        $caseId = $casesClass->submitReferral([
            'student_number'  => $input['student_number'],
            'counselor_id'    => $input['counselor_id'],
            'referred_by'     => $input['referred_by'],
            'referral_source' => $input['referral_source'],
            'referral_reason' => trim($input['referral_reason']),
            'priority'        => $input['priority'] ?? 'Medium',
            'referral_date'   => $input['referral_date'] ?? date('Y-m-d'),
            'remarks'         => trim($input['remarks'] ?? ''),
        ]);

        echo json_encode(['success' => true, 'message' => 'Referral submitted successfully.', 'data' => ['case_id' => $caseId]]);
        break;

    // ------------------------------------------------------------------
    // POST review a referral (accept / reject)
    // ------------------------------------------------------------------
    case 'review_referral':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
            exit;
        }

        $input    = json_decode(file_get_contents('php://input'), true) ?? [];
        $caseId   = (int) ($input['case_id'] ?? 0);
        $decision = $input['decision'] ?? '';

        if (!$caseId || !in_array($decision, ['accept', 'reject'], true)) {
            echo json_encode(['success' => false, 'message' => 'case_id and a valid decision (accept/reject) are required.']);
            exit;
        }

        $ok = $casesClass->reviewReferral($caseId, $decision, trim($input['remarks'] ?? ''));
        echo json_encode([
            'success' => $ok,
            'message' => $ok ? ('Referral ' . ($decision === 'accept' ? 'accepted.' : 'rejected. Case closed.')) : 'Failed to update referral.',
        ]);
        break;

    // ------------------------------------------------------------------
    // POST update case status (Open / In Progress / Closed / reopen)
    // ------------------------------------------------------------------
    case 'update_status':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
            exit;
        }

        $input  = json_decode(file_get_contents('php://input'), true) ?? [];
        $caseId = (int) ($input['case_id'] ?? 0);
        $status = $input['status'] ?? '';

        if (!$caseId || !in_array($status, ['Open', 'In Progress', 'Closed'], true)) {
            echo json_encode(['success' => false, 'message' => 'case_id and a valid status are required.']);
            exit;
        }

        $ok = $casesClass->updateStatus($caseId, $status);
        echo json_encode(['success' => $ok, 'message' => $ok ? 'Case status updated.' : 'Failed to update status.']);
        break;

    // ------------------------------------------------------------------
    // POST assign / reassign counselor
    // ------------------------------------------------------------------
    case 'assign_counselor':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
            exit;
        }

        $input       = json_decode(file_get_contents('php://input'), true) ?? [];
        $caseId      = (int) ($input['case_id'] ?? 0);
        $counselorId = (int) ($input['counselor_id'] ?? 0);

        if (!$caseId || !$counselorId) {
            echo json_encode(['success' => false, 'message' => 'case_id and counselor_id are required.']);
            exit;
        }

        $ok = $casesClass->assignCounselor($caseId, $counselorId);
        echo json_encode(['success' => $ok, 'message' => $ok ? 'Counselor assigned.' : 'Failed to assign counselor.']);
        break;

    // ------------------------------------------------------------------
    // POST set case priority
    // ------------------------------------------------------------------
    case 'set_priority':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
            exit;
        }

        $input    = json_decode(file_get_contents('php://input'), true) ?? [];
        $caseId   = (int) ($input['case_id'] ?? 0);
        $priority = $input['priority'] ?? '';

        if (!$caseId || !in_array($priority, ['Low', 'Medium', 'High', 'Critical'], true)) {
            echo json_encode(['success' => false, 'message' => 'case_id and a valid priority are required.']);
            exit;
        }

        $ok = $casesClass->setPriority($caseId, $priority);
        echo json_encode(['success' => $ok, 'message' => $ok ? 'Priority updated.' : 'Failed to update priority.']);
        break;

    // ------------------------------------------------------------------
    // POST record a counseling session
    // ------------------------------------------------------------------
    case 'record_session':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
            exit;
        }

        $input  = json_decode(file_get_contents('php://input'), true) ?? [];
        $caseId = (int) ($input['case_id'] ?? 0);

        if (!$caseId || empty($input['session_type'])) {
            echo json_encode(['success' => false, 'message' => 'case_id and session_type are required.']);
            exit;
        }

        $sessionId = $casesClass->recordSession([
            'case_id'          => $caseId,
            'counselor_id'     => $userInfo['employee_id'] ?? ($input['counselor_id'] ?? null),
            'session_date'     => $input['session_date'] ?? date('Y-m-d H:i:s'),
            'session_type'     => $input['session_type'],
            'duration_minutes' => $input['duration_minutes'] ?? null,
            'session_notes'    => trim($input['session_notes'] ?? ''),
            'recommendations'  => trim($input['recommendations'] ?? ''),
            'next_session'     => $input['next_session'] ?? null,
        ]);

        echo json_encode(['success' => true, 'message' => 'Session recorded.', 'data' => ['session_id' => $sessionId]]);
        break;

    // ------------------------------------------------------------------
    // POST edit a counseling session
    // ------------------------------------------------------------------
    case 'update_session':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
            exit;
        }

        $input     = json_decode(file_get_contents('php://input'), true) ?? [];
        $sessionId = (int) ($input['session_id'] ?? 0);

        if (!$sessionId || empty($input['session_type'])) {
            echo json_encode(['success' => false, 'message' => 'session_id and session_type are required.']);
            exit;
        }

        $ok = $casesClass->updateSession($sessionId, [
            'session_type'     => $input['session_type'],
            'duration_minutes' => $input['duration_minutes'] ?? null,
            'session_notes'    => trim($input['session_notes'] ?? ''),
            'recommendations'  => trim($input['recommendations'] ?? ''),
            'next_session'     => $input['next_session'] ?? null,
        ]);

        echo json_encode(['success' => $ok, 'message' => $ok ? 'Session updated.' : 'Failed to update session.']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action.']);
        break;
}

// ── Formatting helpers ───────────────────────────────────────────────────────
function formatUnlinkedIncident(array $incident): array
{
    $incident['incident_date_display'] = date('M d, Y g:i A', strtotime($incident['incident_date']));
    return $incident;
}

function formatCaseRow(array $row): array
{
    $row['opened_at_display'] = date('M d, Y', strtotime($row['opened_at']));
    $row['closed_at_display'] = !empty($row['closed_at']) ? date('M d, Y', strtotime($row['closed_at'])) : null;
    return $row;
}

function formatReferral(?array $referral): ?array
{
    if (!$referral) return null;
    $referral['referral_date_display'] = date('M d, Y', strtotime($referral['referral_date']));
    return $referral;
}

function formatSession(array $session): array
{
    $session['session_date_display'] = date('M d, Y g:i A', strtotime($session['session_date']));
    $session['next_session_display'] = !empty($session['next_session'])
        ? date('M d, Y g:i A', strtotime($session['next_session']))
        : null;
    return $session;
}
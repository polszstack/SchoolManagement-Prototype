<?php
include_once __DIR__ . '/../../../auth/session.php';
include_once __DIR__ . '/../classes/Incidents.php';
include_once __DIR__ . '/../classes/User.php';
// NOTE: paths mirror StudentsController.php/CasesController.php/
// AppointmentsController.php — adjust depth/folder names if this file
// doesn't live at the same location.

header('Content-Type: application/json');

// ── Guard: only accept authenticated requests ────────────────────────────────
$userClass = new User();
$userInfo  = $userClass->userSession();

if (!$userInfo) {
    echo json_encode(['success' => false, 'message' => 'Session expired.']);
    exit;
}

$incidentsClass = new Incidents();
$action         = $_REQUEST['action'] ?? '';

// ── Router ────────────────────────────────────────────────────────────────────
switch ($action) {

    // ------------------------------------------------------------------
    // GET  list (filtered + paginated)
    // ------------------------------------------------------------------
    case 'list':
        $filters = [
            'search'        => trim($_GET['search'] ?? ''),
            'severity'      => $_GET['severity'] ?? '',
            'status'        => $_GET['status'] ?? '',
            'incident_type' => $_GET['incident_type'] ?? '',
        ];
        $page     = max(1, (int) ($_GET['page'] ?? 1));
        $pageSize = 10;

        $result    = $incidentsClass->getList($filters, $page, $pageSize);
        $incidents = array_map('formatIncidentRow', $result['rows']);

        echo json_encode([
            'success' => true,
            'data' => [
                'incidents' => $incidents,
                'pagination' => [
                    'total'      => $result['total'],
                    'page'       => $page,
                    'pageSize'   => $pageSize,
                    'count'      => count($incidents),
                    'totalPages' => (int) ceil($result['total'] / $pageSize),
                ],
            ],
        ]);
        break;

    // ------------------------------------------------------------------
    // GET  details
    // ------------------------------------------------------------------
    case 'details':
        $incidentId = (int) ($_GET['incident_id'] ?? 0);
        if (!$incidentId) {
            echo json_encode(['success' => false, 'message' => 'incident_id is required.']);
            exit;
        }

        $overview = $incidentsClass->getIncidentOverview($incidentId);
        if (!$overview) {
            echo json_encode(['success' => false, 'message' => 'Incident not found.']);
            exit;
        }

        echo json_encode(['success' => true, 'data' => formatIncidentDetail($overview)]);
        break;

    // ------------------------------------------------------------------
    // GET  incident_types (distinct list, for the filter dropdown)
    // ------------------------------------------------------------------
    case 'incident_types':
        echo json_encode(['success' => true, 'data' => $incidentsClass->getIncidentTypes()]);
        break;

    // ------------------------------------------------------------------
    // POST create incident report
    // ------------------------------------------------------------------
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? [];

        $required = ['student_number', 'incident_type', 'incident_date', 'description'];
        foreach ($required as $field) {
            if (empty($input[$field])) {
                echo json_encode(['success' => false, 'message' => "{$field} is required."]);
                exit;
            }
        }

        $incidentId = $incidentsClass->createIncident([
            'student_number' => $input['student_number'],
            'reported_by'    => $userInfo['employee_id'] ?? null,
            'incident_type'  => trim($input['incident_type']),
            'severity'       => $input['severity'] ?? 'Minor',
            'incident_date'  => str_replace('T', ' ', $input['incident_date']),
            'location'       => trim($input['location'] ?? ''),
            'description'    => trim($input['description']),
        ]);

        echo json_encode(['success' => true, 'message' => 'Incident reported successfully.', 'data' => ['incident_id' => $incidentId]]);
        break;

    // ------------------------------------------------------------------
    // POST edit incident report
    // ------------------------------------------------------------------
    case 'update':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
            exit;
        }

        $input      = json_decode(file_get_contents('php://input'), true) ?? [];
        $incidentId = (int) ($input['incident_id'] ?? 0);

        if (!$incidentId || empty($input['incident_type']) || empty($input['incident_date']) || empty($input['description'])) {
            echo json_encode(['success' => false, 'message' => 'incident_id, incident_type, incident_date, and description are required.']);
            exit;
        }

        $ok = $incidentsClass->updateIncident($incidentId, [
            'incident_type' => trim($input['incident_type']),
            'severity'      => $input['severity'] ?? 'Minor',
            'incident_date' => str_replace('T', ' ', $input['incident_date']),
            'location'      => trim($input['location'] ?? ''),
            'description'   => trim($input['description']),
        ]);

        echo json_encode(['success' => $ok, 'message' => $ok ? 'Incident updated.' : 'Failed to update incident.']);
        break;

    // ------------------------------------------------------------------
    // POST update status only (Reported / Investigating / Resolved / Closed)
    // ------------------------------------------------------------------
    case 'update_status':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
            exit;
        }

        $input      = json_decode(file_get_contents('php://input'), true) ?? [];
        $incidentId = (int) ($input['incident_id'] ?? 0);
        $status     = $input['status'] ?? '';

        if (!$incidentId || !in_array($status, ['Reported', 'Investigating', 'Resolved', 'Closed'], true)) {
            echo json_encode(['success' => false, 'message' => 'incident_id and a valid status are required.']);
            exit;
        }

        $ok = $incidentsClass->updateStatus($incidentId, $status);
        echo json_encode(['success' => $ok, 'message' => $ok ? 'Status updated.' : 'Failed to update status.']);
        break;

    // ------------------------------------------------------------------
    // POST record disciplinary action / resolution
    // ------------------------------------------------------------------
    case 'record_resolution':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
            exit;
        }

        $input       = json_decode(file_get_contents('php://input'), true) ?? [];
        $incidentId  = (int) ($input['incident_id'] ?? 0);
        $actionTaken = trim($input['action_taken'] ?? '');
        $actionDate  = $input['action_date'] ?? '';

        if (!$incidentId || $actionTaken === '' || $actionDate === '') {
            echo json_encode(['success' => false, 'message' => 'incident_id, action_taken, and action_date are required.']);
            exit;
        }

        $ok = $incidentsClass->recordResolution($incidentId, $actionTaken, $actionDate, $input['status'] ?? 'Resolved');
        echo json_encode(['success' => $ok, 'message' => $ok ? 'Resolution recorded.' : 'Failed to record resolution.']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action.']);
        break;
}

// ── Formatting helpers ───────────────────────────────────────────────────────
function formatIncidentRow(array $row): array
{
    $row['incident_date_display'] = date('M d, Y', strtotime($row['incident_date']));
    return $row;
}

function formatIncidentDetail(array $row): array
{
    $row['incident_date_display'] = date('M d, Y g:i A', strtotime($row['incident_date']));
    $row['action_date_display']   = !empty($row['action_date']) ? date('M d, Y', strtotime($row['action_date'])) : null;
    $row['created_at_display']    = date('M d, Y', strtotime($row['created_at']));
    return $row;
}
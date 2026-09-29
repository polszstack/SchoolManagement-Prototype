<?php
include_once __DIR__ . '/../../../auth/session.php';
include_once __DIR__ . '/../classes/Analytics.php';
include_once __DIR__ . '/../classes/User.php';
// NOTE: paths mirror StudentsController.php/CasesController.php/
// AppointmentsController.php/IncidentsController.php — adjust depth/
// folder names if this file doesn't live at the same location.

header('Content-Type: application/json');

// ── Guard: only accept authenticated requests ────────────────────────────────
$userClass = new User();
$userInfo  = $userClass->userSession();

if (!$userInfo) {
    echo json_encode(['success' => false, 'message' => 'Session expired.']);
    exit;
}

$analyticsClass = new Analytics();
$action         = $_REQUEST['action'] ?? '';

// ── Router ────────────────────────────────────────────────────────────────────
switch ($action) {

    // ------------------------------------------------------------------
    // GET  breakdowns (the 3 mini-chart bars — refreshable independent
    // of the report generator below)
    // ------------------------------------------------------------------
    case 'breakdowns':
        echo json_encode([
            'success' => true,
            'data' => [
                'cases_by_status'        => $analyticsClass->getCasesByStatus(),
                'appointments_by_status' => $analyticsClass->getAppointmentsByStatus(),
                'incidents_by_severity'  => $analyticsClass->getIncidentsBySeverity(),
            ],
        ]);
        break;

    // ------------------------------------------------------------------
    // GET  generate — dispatches to the right report by type
    // ------------------------------------------------------------------
    case 'generate':
        $type = $_GET['type'] ?? '';
        $validTypes = [
            'student_counseling', 'referral', 'appointment', 'incident',
            'monthly_guidance', 'yearly_guidance', 'counselor_workload', 'student_risk',
        ];

        if (!in_array($type, $validTypes, true)) {
            echo json_encode(['success' => false, 'message' => 'Invalid report type.']);
            exit;
        }

        $filters = [
            'date_from' => $_GET['date_from'] ?? '',
            'date_to'   => $_GET['date_to'] ?? '',
            'year'      => $_GET['year'] ?? date('Y'),
            'month'     => $_GET['month'] ?? date('n'),
        ];

        $rows = $analyticsClass->generateReport($type, $filters);

        echo json_encode([
            'success' => true,
            'data' => [
                'type'  => $type,
                'rows'  => $rows,
                'count' => count($rows),
            ],
        ]);
        break;

    // ------------------------------------------------------------------
    // GET  export
    // NOTE: stubbed. Confirm desired format (CSV/XLSX/PDF) before building.
    // ------------------------------------------------------------------
    case 'export':
        echo json_encode(['success' => false, 'message' => 'Export not implemented yet — confirm format (PDF/Excel) first.']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action.']);
        break;
}
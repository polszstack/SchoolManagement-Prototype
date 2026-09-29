<?php

/**
 * StudentMonitoringController
 *
 * The current page renders all data server-side in one pass and does
 * filtering/CSV export entirely client-side in student-monitoring.js —
 * so there's no AJAX call hitting this file yet.
 *
 * It's included here for architectural consistency with the rest of the
 * module (StudentsController, AnnouncementController, etc.) and so a
 * server-side "list" endpoint is ready if you later want to move to
 * fetch()-driven filtering/pagination instead of the current
 * filter-in-the-DOM approach.
 */

include_once __DIR__ . "/../../../auth/session.php";
include_once __DIR__ . "/../classes/StudentMonitoring.php";

header('Content-Type: application/json');

$model  = new StudentMonitoring();
$action = $_REQUEST['action'] ?? '';

switch ($action) {

    case 'list':
        $data = $model->getDashboardData();
        echo json_encode([
            'success' => $data['error'] === null,
            'message' => $data['error'] ?? 'OK',
            'data'    => [
                'students' => $data['students'],
                'courses'  => $data['courses'],
                'stats'    => $data['stats'],
            ],
        ]);
        break;

    default:
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Unknown action.',
            'data'    => null,
        ]);
        break;
}
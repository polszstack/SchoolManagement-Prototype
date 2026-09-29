<?php
include_once __DIR__ . '/../../../auth/session.php';
include_once __DIR__ . '/../classes/Appointments.php';
include_once __DIR__ . '/../classes/User.php';
// NOTE: paths mirror StudentsController.php/CasesController.php — adjust
// depth/folder names if this file doesn't live at the same location.

header('Content-Type: application/json');

// ── Guard: only accept authenticated requests ────────────────────────────────
$userClass = new User();
$userInfo  = $userClass->userSession();

if (!$userInfo) {
    echo json_encode(['success' => false, 'message' => 'Session expired.']);
    exit;
}

$appointmentsClass = new Appointments();
$action            = $_REQUEST['action'] ?? '';

// ── Router ────────────────────────────────────────────────────────────────────
switch ($action) {

    // ------------------------------------------------------------------
    // GET  list (filtered + paginated; date window computed from
    // view=daily|weekly + date)
    // ------------------------------------------------------------------
    case 'list':
        $view = $_GET['view'] ?? 'daily';
        $date = $_GET['date'] ?? date('Y-m-d');

        [$dateFrom, $dateTo] = computeDateWindow($view, $date);

        $filters = [
            'search'       => trim($_GET['search'] ?? ''),
            'status'       => $_GET['status'] ?? '',
            'meeting_type' => $_GET['meeting_type'] ?? '',
            'counselor_id' => $_GET['counselor_id'] ?? '',
            'date_from'    => $dateFrom,
            'date_to'      => $dateTo,
        ];
        $page     = max(1, (int) ($_GET['page'] ?? 1));
        $pageSize = 10;

        $result       = $appointmentsClass->getList($filters, $page, $pageSize);
        $appointments = array_map('formatAppointmentRow', $result['rows']);

        echo json_encode([
            'success' => true,
            'data' => [
                'appointments' => $appointments,
                'pagination' => [
                    'total'      => $result['total'],
                    'page'       => $page,
                    'pageSize'   => $pageSize,
                    'count'      => count($appointments),
                    'totalPages' => (int) ceil($result['total'] / $pageSize),
                ],
            ],
        ]);
        break;

    // ------------------------------------------------------------------
    // GET  details
    // ------------------------------------------------------------------
    case 'details':
        $appointmentId = (int) ($_GET['appointment_id'] ?? 0);
        if (!$appointmentId) {
            echo json_encode(['success' => false, 'message' => 'appointment_id is required.']);
            exit;
        }

        $overview = $appointmentsClass->getAppointmentOverview($appointmentId);
        if (!$overview) {
            echo json_encode(['success' => false, 'message' => 'Appointment not found.']);
            exit;
        }

        echo json_encode(['success' => true, 'data' => formatAppointmentDetail($overview)]);
        break;

    // ------------------------------------------------------------------
    // GET  counselors
    // ------------------------------------------------------------------
    case 'counselors':
        echo json_encode(['success' => true, 'data' => $appointmentsClass->getCounselors()]);
        break;

    // ------------------------------------------------------------------
    // GET  open_cases — Open/In Progress cases (all students), for the
    // "Case" picker in the booking modal
    // ------------------------------------------------------------------
    case 'open_cases':
        echo json_encode(['success' => true, 'data' => $appointmentsClass->getOpenCases()]);
        break;

    // ------------------------------------------------------------------
    // GET  booked_times — existing bookings for a counselor on a date,
    // used by the booking modal to show/avoid conflicts
    // ------------------------------------------------------------------
    case 'booked_times':
        $counselorId = (int) ($_GET['counselor_id'] ?? 0);
        $date        = $_GET['date'] ?? '';

        if (!$counselorId || $date === '') {
            echo json_encode(['success' => false, 'message' => 'counselor_id and date are required.']);
            exit;
        }

        $times = $appointmentsClass->getBookedTimes($counselorId, $date);
        $times = array_map(fn($t) => date('g:i A', strtotime($t)), $times);

        echo json_encode(['success' => true, 'data' => $times]);
        break;

    // ------------------------------------------------------------------
    // POST book an appointment (counselor booking on behalf of a student)
    // ------------------------------------------------------------------
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? [];

        $required = ['student_number', 'counselor_id', 'appointment_date', 'purpose'];
        foreach ($required as $field) {
            if (empty($input[$field])) {
                echo json_encode(['success' => false, 'message' => "{$field} is required."]);
                exit;
            }
        }

        try {
            $appointmentId = $appointmentsClass->createAppointment([
                'student_number'   => $input['student_number'],
                'counselor_id'     => $input['counselor_id'],
                'case_id'          => $input['case_id'] ?? null,
                'appointment_date' => str_replace('T', ' ', $input['appointment_date']),
                'purpose'          => trim($input['purpose']),
                'meeting_type'     => $input['meeting_type'] ?? 'Face-to-Face',
            ]);
            echo json_encode(['success' => true, 'message' => 'Appointment booked successfully.', 'data' => ['appointment_id' => $appointmentId]]);
        } catch (RuntimeException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    // ------------------------------------------------------------------
    // POST approve / complete / no_show (simple status transitions)
    // ------------------------------------------------------------------
    case 'approve':
    case 'complete':
    case 'no_show':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
            exit;
        }

        $input         = json_decode(file_get_contents('php://input'), true) ?? [];
        $appointmentId = (int) ($input['appointment_id'] ?? 0);

        if (!$appointmentId) {
            echo json_encode(['success' => false, 'message' => 'appointment_id is required.']);
            exit;
        }

        $statusMap = ['approve' => 'Approved', 'complete' => 'Completed', 'no_show' => 'No Show'];
        $ok = $appointmentsClass->updateStatus($appointmentId, $statusMap[$action]);

        echo json_encode(['success' => $ok, 'message' => $ok ? 'Appointment updated.' : 'Failed to update appointment.']);
        break;

    // ------------------------------------------------------------------
    // POST reject (maps to status = 'Cancelled', with a reason in remarks)
    // ------------------------------------------------------------------
    case 'reject':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
            exit;
        }

        $input         = json_decode(file_get_contents('php://input'), true) ?? [];
        $appointmentId = (int) ($input['appointment_id'] ?? 0);

        if (!$appointmentId) {
            echo json_encode(['success' => false, 'message' => 'appointment_id is required.']);
            exit;
        }

        $ok = $appointmentsClass->updateStatus($appointmentId, 'Cancelled', trim($input['reason'] ?? ''));
        echo json_encode(['success' => $ok, 'message' => $ok ? 'Appointment rejected.' : 'Failed to reject appointment.']);
        break;

    // ------------------------------------------------------------------
    // POST reschedule
    // ------------------------------------------------------------------
    case 'reschedule':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
            exit;
        }

        $input         = json_decode(file_get_contents('php://input'), true) ?? [];
        $appointmentId = (int) ($input['appointment_id'] ?? 0);
        $newDate       = $input['appointment_date'] ?? '';

        if (!$appointmentId || $newDate === '') {
            echo json_encode(['success' => false, 'message' => 'appointment_id and appointment_date are required.']);
            exit;
        }

        try {
            $ok = $appointmentsClass->reschedule($appointmentId, str_replace('T', ' ', $newDate));
            echo json_encode(['success' => $ok, 'message' => $ok ? 'Appointment rescheduled.' : 'Failed to reschedule.']);
        } catch (RuntimeException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    // ------------------------------------------------------------------
    // POST add remarks
    // ------------------------------------------------------------------
    case 'add_remarks':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
            exit;
        }

        $input         = json_decode(file_get_contents('php://input'), true) ?? [];
        $appointmentId = (int) ($input['appointment_id'] ?? 0);
        $remarks       = trim($input['remarks'] ?? '');

        if (!$appointmentId || $remarks === '') {
            echo json_encode(['success' => false, 'message' => 'appointment_id and remarks are required.']);
            exit;
        }

        $ok = $appointmentsClass->addRemarks($appointmentId, $remarks);
        echo json_encode(['success' => $ok, 'message' => $ok ? 'Remarks saved.' : 'Failed to save remarks.']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action.']);
        break;
}

// ── Helpers ──────────────────────────────────────────────────────────────────
function computeDateWindow(string $view, string $date): array
{
    $d = new DateTime($date);

    if ($view === 'weekly') {
        // Monday-Sunday window containing $date
        $dayOfWeek = (int) $d->format('N'); // 1 (Mon) - 7 (Sun)
        $monday = (clone $d)->modify('-' . ($dayOfWeek - 1) . ' days');
        $sunday = (clone $monday)->modify('+6 days');
        return [$monday->format('Y-m-d 00:00:00'), $sunday->format('Y-m-d 23:59:59')];
    }

    // daily
    return [$d->format('Y-m-d 00:00:00'), $d->format('Y-m-d 23:59:59')];
}

function formatAppointmentRow(array $row): array
{
    $row['time_display'] = date('g:i A', strtotime($row['appointment_date']));
    $row['date_display'] = date('M d, Y', strtotime($row['appointment_date']));
    return $row;
}

function formatAppointmentDetail(array $row): array
{
    $row['appointment_date_display'] = date('M d, Y g:i A', strtotime($row['appointment_date']));
    $row['created_at_display'] = date('M d, Y', strtotime($row['created_at']));
    return $row;
}
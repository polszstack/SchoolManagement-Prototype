<?php
include_once __DIR__ . '/../../../auth/session.php';
include_once __DIR__ . '/../../../database/db.php';
include_once __DIR__ . '/../classes/Employee.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$action = $_GET['action'] ?? null;

switch ($action) {

    case 'get_by_department':
        $department_id = isset($_GET['department_id']) ? intval($_GET['department_id']) : null;

        if (!$department_id) {
            echo json_encode(['success' => false, 'message' => 'Department ID is required']);
            exit;
        }

        try {
            $database = new Database();
            $pdo = $database->getConnection();

            $stmt = $pdo->prepare("
                SELECT 
                    sms.employee_id,
                    sms.first_name,
                    sms.last_name,
                    sp.position_name
                FROM sms_employee sms
                LEFT JOIN sd_position sp ON sms.position = sp.position_id
                WHERE sms.department = :department_id
                AND sms.status = 'active'
                ORDER BY sms.last_name, sms.first_name
            ");
            $stmt->execute([':department_id' => $department_id]);
            $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'employees' => $employees]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
        break;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid or missing action']);
        break;
}
<?php
include_once __DIR__ . '/../../../auth/session.php';
include_once __DIR__ . '/../../../database/db.php';
include_once __DIR__ . '/../classes/Department.php';

error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? null;

switch ($action) {

    case 'get_departments':
        try {
            $database = new Database();
            $pdo = $database->getConnection();

            $stmt = $pdo->query("
                SELECT 
                    d.department_id,
                    d.department_name,
                    CONCAT(e.first_name, ' ', e.last_name) AS head_name,
                    COUNT(emp.employee_id) AS employee_count
                FROM sd_department d
                LEFT JOIN sms_employee e   ON d.department_head = e.employee_id
                LEFT JOIN sms_employee emp ON emp.department = d.department_id
                GROUP BY d.department_id, d.department_name, e.first_name, e.last_name
                ORDER BY d.department_name ASC
            ");

            $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $departments]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
        break;

    case 'add_department':
        if ($method !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $department_name = isset($input['department_name']) ? trim($input['department_name']) : null;

        if (!$department_name) {
            echo json_encode(['success' => false, 'message' => 'Department name is required']);
            exit;
        }

        try {
            $department = new Department();
            $result = $department->addDepartment($department_name);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Department added successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to add department']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
        break;

    case 'assign_head':
        if ($method !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $input         = json_decode(file_get_contents('php://input'), true);
        $department_id = isset($input['department_id']) ? intval($input['department_id']) : null;
        $employee_id   = isset($input['employee_id'])   ? intval($input['employee_id'])   : null;

        if (!$department_id || !$employee_id) {
            echo json_encode(['success' => false, 'message' => 'Department and employee are required']);
            exit;
        }

        try {
            $database = new Database();
            $pdo = $database->getConnection();

            $check = $pdo->prepare("
                SELECT employee_id FROM sms_employee 
                WHERE employee_id = :employee_id AND department = :department_id
            ");
            $check->execute([
                ':employee_id'   => $employee_id,
                ':department_id' => $department_id
            ]);

            if (!$check->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Employee does not belong to this department']);
                exit;
            }

            $stmt = $pdo->prepare("
                UPDATE sd_department 
                SET department_head = :employee_id 
                WHERE department_id = :department_id
            ");
            $stmt->execute([
                ':employee_id'   => $employee_id,
                ':department_id' => $department_id
            ]);

            echo json_encode(['success' => true, 'message' => 'Department head assigned successfully']);

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
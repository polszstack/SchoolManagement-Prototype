<?php
include_once __DIR__ . '/../../../database/db.php';

Class Employee {
    private $conn;
    private $employeeid;
    private $firstname;
    private $lastname;
    private $middlename;
    private $department;
    private $position;
    private $status; 


    public function __construct($pdo = null) {
        if ($pdo instanceof PDO) {
            $this->conn = $pdo;
        } else {
            $database = new Database();
            $this->conn = $database->getConnection();
        }
    }

    public function getEmployees() {
    $sql = "SELECT 
                sms.employee_id,
                sms.first_name,
                sms.middle_name,
                sms.last_name,
                sd.department_name,
                sp.position_name,
                sms.status
            FROM sms_employee sms
            LEFT JOIN sd_department sd 
                ON sms.department = sd.department_id
            LEFT JOIN sd_position sp
                ON sms.position = sp.position_id
            ORDER BY sms.employee_id";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEmployeeId() {
        return $_SESSION['employee_id'] ?? null;
    }

    public function getEmployeeName() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $employeeId = $_SESSION['employee_id'] ?? null;

        if ($employeeId) {
            $sql = "SELECT first_name, last_name FROM sms_employee WHERE employee_id = :employee_id LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':employee_id', $employeeId);
            $stmt->execute();
            $employee = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($employee) {
                return htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']);
            }
        }
        return 'Unknown User';
    }
    
    public function getEmployeePosition() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $employeeId = $_SESSION['employee_id'] ?? null;

        if ($employeeId) {
            $sql = "SELECT sp.position_name 
                    FROM sms_employee sms
                    LEFT JOIN sd_position sp ON sms.position = sp.position_id
                    WHERE sms.employee_id = :employee_id LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':employee_id', $employeeId);
            $stmt->execute();
            $employee = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($employee) {
                return htmlspecialchars($employee['position_name']);
            }
        }
        return 'Unknown Position';
    }
}


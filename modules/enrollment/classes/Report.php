<?php
    include_once __DIR__ . '/../../../database/db.php';

    class Report {
        private $conn;
        private $reportId;
        private $reportType;
        private $title;
        private $description;
        private $filePath;
        private $employeeId;
        private $submissionDate;
        private $department;
        private $status;

        public function __construct($pdo = null) {
            if ($pdo instanceof PDO) {
                $this->conn = $pdo;
            } else {
                $database = new Database();
                $this->conn = $database->getConnection();
            }
        }

        public function getReports($department_id = null) {
            $sql = "SELECT 
                        r.report_id,
                        r.title,
                        r.description,
                        r.file_path,
                        r.submitted_at,
                        d.department_name,
                        CONCAT(e.first_name, ' ', e.last_name) AS submitted_by,
                        rt.report_type AS report_type
                    FROM `sd_reports` r
                    LEFT JOIN `sd_department` d ON r.department_id = d.department_id
                    LEFT JOIN `sms_employee` e ON r.submitted_by = e.employee_id
                    LEFT JOIN `sd_report_type` rt ON r.report_type = rt.type_id";
            if (!is_null($department_id)) {
                $sql .= " WHERE r.department_id = :department_id";
            }
            $sql .= " ORDER BY r.submitted_at DESC";
            $stmt = $this->conn->prepare($sql);
            if (!is_null($department_id)) {
                $stmt->execute([':department_id' => $department_id]);
            } else {
                $stmt->execute();
            }
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function submitReport($title, $description, $file_path, $report_type) {
            $submitted_by = $_SESSION['employee_id']; // or whatever your session key is
            // Fetch department from employee record directly
            $stmt = $this->conn->prepare("SELECT department FROM sms_employee WHERE employee_id = :employee_id");
            $stmt->execute([':employee_id' => $submitted_by]);
            $employee = $stmt->fetch(PDO::FETCH_ASSOC);
            $department_id = $employee['department'] ?? null;
            $sql = "INSERT INTO `sd_reports` 
                        (`title`, `description`, `file_path`, `report_type`, `department_id`, `submitted_by`)
                    VALUES 
                        (:title, :description, :file_path, :report_type, :department_id, :submitted_by)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':title'         => $title,
                ':description'   => $description,
                ':file_path'     => $file_path,
                ':report_type'   => $report_type,
                ':department_id' => $department_id,
                ':submitted_by'  => $submitted_by
            ]);
            return $this->conn->lastInsertId();
        }

        public function getReportTypesByDepartment($department_id) {
        if ($department_id === null) {
            $sql = "SELECT type_id, report_type FROM `sd_report_type` ORDER BY report_type ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
        } else {
            $sql = "SELECT type_id, report_type FROM `sd_report_type` WHERE department_id = :department_id ORDER BY report_type ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':department_id' => $department_id]);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

        public function filterByDepartment($department_id) {
            $sql = "SELECT 
                        r.report_id,
                        r.title,
                        r.description,
                        r.file_path,
                        r.submitted_at,
                        d.department_name,
                        CONCAT(e.first_name, ' ', e.last_name) AS submitted_by,
                        rt.report_type AS report_type
                    FROM `sd_reports` r
                    LEFT JOIN `sd_department` d ON r.department_id = d.department_id
                    LEFT JOIN `sms_employee` e ON r.submitted_by = e.employee_id
                    LEFT JOIN `sd_report_type` rt ON r.report_type = rt.type_id
                    WHERE r.department_id = :department_id
                    ORDER BY r.submitted_at DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':department_id' => $department_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }


    }
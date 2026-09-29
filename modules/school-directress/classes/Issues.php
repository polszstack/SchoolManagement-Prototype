<?php
include_once __DIR__ . '/../../../database/db.php';

class Issues {
    private $conn;

    public function __construct($pdo = null) {
        if ($pdo instanceof PDO) {
            $this->conn = $pdo;
        } else {
            $database = new Database();
            $this->conn = $database->getConnection();
        }
    }

    public function getDepartments() {
        $stmt = $this->conn->prepare("SELECT department_id, department_name FROM sd_department ORDER BY department_name");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function submitIssue($title, $department_id, $submitted_by, $file_path = null) {
        $stmt = $this->conn->prepare("
            INSERT INTO sd_issues (title, department, submitted_by, file_path)
            VALUES (:title, :department, :submitted_by, :file_path)
        ");
        $stmt->execute([
            ':title'        => $title,
            ':department'   => $department_id,
            ':submitted_by' => $submitted_by,
            ':file_path'    => $file_path,
        ]);
        return $this->conn->lastInsertId();
    }

    public function getIssues($status = 'all', $search = '', $departmentId = null) {
        $sql = "
            SELECT
                i.issue_id,
                i.title,
                i.file_path,
                d.department_name,
                CONCAT(e.first_name, ' ', e.last_name) AS submitted_by,
                i.submitted_on,
                i.status
            FROM sd_issues i
            JOIN sd_department d ON i.department = d.department_id
            JOIN sms_employee e ON i.submitted_by = e.employee_id
            WHERE 1=1
        ";
        $params = [];

        // Restrict to own department unless school directress (null = see all)
        if ($departmentId !== null) {
            $sql .= " AND i.department = :department_id";
            $params[':department_id'] = $departmentId;
        }

        if ($status !== 'all') {
            $sql .= " AND i.status = :status";
            $params[':status'] = $status;
        }

        if (!empty($search)) {
            $sql .= " AND (i.title LIKE :search OR CONCAT(e.first_name, ' ', e.last_name) LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY i.submitted_on DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getIssue($issue_id) {
        $stmt = $this->conn->prepare("
            SELECT
                i.*,
                d.department_name,
                CONCAT(e.first_name, ' ', e.last_name) AS submitted_by_name
            FROM sd_issues i
            JOIN sd_department d ON i.department = d.department_id
            JOIN sms_employee e ON i.submitted_by = e.employee_id
            WHERE i.issue_id = :issue_id
        ");
        $stmt->execute([':issue_id' => $issue_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateStatus($issue_id, $status) {
        $allowed = ['open', 'resolved'];
        if (!in_array($status, $allowed)) return false;

        $stmt = $this->conn->prepare("
            UPDATE sd_issues SET status = :status WHERE issue_id = :issue_id
        ");
        return $stmt->execute([':status' => $status, ':issue_id' => $issue_id]);
    }
}
?>
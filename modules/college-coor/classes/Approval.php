<?php
include_once __DIR__ . '/../../../database/db.php';

class Approval {
    private $conn;

    public function __construct($pdo = null) {
        if ($pdo instanceof PDO) {
            $this->conn = $pdo;
        } else {
            $database = new Database();
            $this->conn = $database->getConnection();
        }
    }

    public function getApprovals($filter = 'all', $departmentId = null) {
        $sql = "SELECT 
                    a.approval_id,
                    a.title,
                    CONCAT(e1.first_name, ' ', e1.last_name) AS submit_by,
                    CONCAT(e2.first_name, ' ', e2.last_name) AS approver_id,
                    a.submitted_on,
                    a.description,
                    a.remarks,
                    a.approved_at,
                    a.decision,
                    d.department_name,
                    a.file_path
                FROM sd_approvals a
                LEFT JOIN sms_employee e1 ON a.submit_by = e1.employee_id
                LEFT JOIN sms_employee e2 ON a.approver_id = e2.employee_id
                LEFT JOIN sd_department d ON e1.department = d.department_id
                WHERE 1=1";

        $params = [];

        // Always filter by the logged-in user's department
        if ($departmentId !== null) {
            $sql .= " AND e1.department = :department_id";
            $params[':department_id'] = $departmentId;
        }

        if ($filter === 'pending') {
            $sql .= " AND a.decision = 'pending'";
        } elseif ($filter === 'approved') {
            $sql .= " AND a.decision = 'approved'";
        } elseif ($filter === 'rejected') {
            $sql .= " AND a.decision = 'rejected'";
        }

        $sql .= " ORDER BY a.submitted_on DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function submitApproval($title, $submitBy, $description, $filePath = null) {
        $stmt = $this->conn->prepare("
            INSERT INTO sd_approvals (title, submit_by, description, file_path)
            VALUES (:title, :submit_by, :description, :file_path)
        ");
        $stmt->bindParam(':title',       $title);
        $stmt->bindParam(':submit_by',   $submitBy);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':file_path',   $filePath);
        return $stmt->execute();
    }

    public function updateDecision($approvalId, $decision, $approverId, $remarks = null) {
        $stmt = $this->conn->prepare("
            UPDATE sd_approvals
            SET decision    = :decision,
                approver_id = :approver_id,
                remarks     = :remarks,
                approved_at = NOW()
            WHERE approval_id = :approval_id
        ");
        $stmt->bindParam(':decision',    $decision);
        $stmt->bindParam(':approver_id', $approverId);
        $stmt->bindParam(':remarks',     $remarks);
        $stmt->bindParam(':approval_id', $approvalId);
        return $stmt->execute();
    }
}
?>
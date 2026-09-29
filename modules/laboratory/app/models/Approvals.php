<?php

require_once __DIR__ . '/core/DatabaseTwo.php';

class Approvals extends DatabaseTwo
{
    protected $table = 'sd_approvals';
    public function __construct()
    {
        $this->conn = DatabaseTwo::getInstance()->getConnection();
    }
    public function all()
    {
        $stmt = $this->conn->prepare("
        SELECT a.*, d.department_name
        FROM {$this->table} a
        LEFT JOIN sd_department d ON a.department = d.department_id
        ORDER BY a.submitted_on DESC
    ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByDepartment($departmentId)
    {
        $stmt = $this->conn->prepare("
        SELECT a.*, d.department_name
        FROM {$this->table} a
        LEFT JOIN sd_department d ON a.department = d.department_id
        WHERE a.department = :department
        ORDER BY a.submitted_on DESC
    ");
        $stmt->bindParam(':department', $departmentId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(array $data)
    {
        $stmt = $this->conn->prepare("
        INSERT INTO sd_approvals 
        (title, description, remarks, submitted_on, submit_by, department, approver_id, decision, file_path)
        VALUES
        (:title, :description, :remarks, NOW(), :submit_by, :department, :approver_id, 'Pending', :file_path)
    ");

        $stmt->execute([
            ':title' => $data['title'],
            ':description' => $data['description'],
            ':remarks' => $data['remarks'] ?? null,
            ':submit_by' => $data['submit_by'],
            ':department' => $data['department'],
            ':approver_id' => $data['approver_id'] ?? null,
            ':file_path' => $data['file_path'] ?? null
        ]);

        return $this->conn->lastInsertId();
    }

    public function updateDecision(int $approvalId, string $decision, ?string $remarks = null)
    {
        $stmt = $this->conn->prepare("
        UPDATE {$this->table} 
        SET decision = :decision, 
            remarks = :remarks, 
            approved_at = NOW()
        WHERE approval_id = :approval_id
    ");

        $stmt->execute([
            ':decision' => $decision,
            ':remarks' => $remarks,
            ':approval_id' => $approvalId
        ]);

        return $stmt->rowCount();
    }

    public function delete(int $approvalId): bool
    {
        $stmt = $this->conn->prepare("
        DELETE FROM sd_approvals 
        WHERE approval_id = :approval_id
    ");
        return $stmt->execute([':approval_id' => $approvalId]);
    }
}

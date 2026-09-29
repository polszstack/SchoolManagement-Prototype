<?php

require_once __DIR__ . '/core/DatabaseTwo.php';

class Issues extends DatabaseTwo
{
    protected $table = 'sms_employee';

    public function __construct()
    {
        $this->conn = DatabaseTwo::getInstance()->getConnection();
    }

    public function all(): array
    {
        $stmt = $this->conn->prepare("
            SELECT i.*, d.department_name, CONCAT(e.first_name, ' ', e.last_name) AS submitted_by_name
            FROM sd_issues i
            LEFT JOIN sd_department d ON i.department = d.department_id
            LEFT JOIN sms_employee e ON i.submitted_by = e.employee_id
            ORDER BY i.submitted_on DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(array $data): bool
    {
        $stmt = $this->conn->prepare("
            INSERT INTO sd_issues (title, description, department, submitted_by, file_path)
            VALUES (:title, :description, :department, :submitted_by, :file_path)
        ");

        return $stmt->execute([
            ':title'        => $data['title'],
            ':description'  => $data['description'],
            ':department'   => $data['department'],
            ':submitted_by' => $data['submitted_by'],
            ':file_path'    => $data['file_path']
        ]);
    }
    public function updateStatus(int $issueId, string $status): bool
    {
        $stmt = $this->conn->prepare("
            UPDATE sd_issues 
            SET status = :status, updated_at = NOW()
            WHERE issue_id = :issue_id
        ");
        return $stmt->execute([
            ':status'   => $status,
            ':issue_id' => $issueId
        ]);
    }
    public function getByStatus(string $status)
    {
        $stmt = $this->conn->prepare("
            SELECT i.*, d.department_name, CONCAT(e.first_name, ' ', e.last_name) AS submitted_by_name
            FROM sd_issues i
            LEFT JOIN sd_department d ON i.department = d.department_id
            LEFT JOIN sms_employee e ON i.submitted_by = e.employee_id
            WHERE i.status = :status
            ORDER BY i.submitted_on DESC
        ");
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete(int $issueId)
    {
        $stmt = $this->conn->prepare("DELETE FROM sd_issues WHERE issue_id = :id");
        $stmt->bindParam(':id', $issueId, PDO::PARAM_INT);
        $stmt->execute();
    }
}

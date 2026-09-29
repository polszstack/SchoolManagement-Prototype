<?php

require_once __DIR__ . '/core/DatabaseTwo.php';

class Reports extends DatabaseTwo
{
    protected $table = 'sd_reports';
    public function __construct()
    {
        $this->conn = DatabaseTwo::getInstance()->getConnection();
    }
    public function all()
    {
        $stmt = $this->conn->prepare("
        SELECT 
            r.report_id,
            r.title,
            r.description,
            r.file_path,
            r.status,
            r.submitted_at,

            d.department_name,

            CONCAT(e.first_name, ' ', e.last_name) AS submitted_by,

            rt.report_type AS report_type

        FROM sd_reports r

        LEFT JOIN sd_department d 
            ON r.department_id = d.department_id

        LEFT JOIN sms_employee e 
            ON r.submitted_by = e.employee_id

        LEFT JOIN sd_report_type rt 
            ON r.report_type = rt.type_id

        ORDER BY r.submitted_at DESC
    ");

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function create(array $data)
    {
        $stmt = $this->conn->prepare("
        INSERT INTO sd_reports 
        (title, description, file_path, department_id, submitted_by, report_type, status, submitted_at)
        VALUES 
        (:title, :description, :file_path, :department_id, :submitted_by, :report_type, 'Pending', NOW())
    ");

        return $stmt->execute([
            ':title'         => $data['title'],
            ':description'   => $data['description'],
            ':file_path'     => $data['file_path'],
            ':department_id' => $data['department_id'],
            ':submitted_by'  => $data['submitted_by'],
            ':report_type'   => $data['report_type']
        ]);
    }

    public function delete(int $reportId)
    {
        $stmt = $this->conn->prepare("DELETE FROM sd_reports WHERE report_id = :report_id");
        return $stmt->execute([':report_id' => $reportId]);
    }
}

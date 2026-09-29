<?php

require_once __DIR__ . '/core/DatabaseTwo.php';

class ReportType extends DatabaseTwo
{
    protected $table = 'sd_report_type';
    protected $conn;

    public function __construct()
    {
        $this->conn = DatabaseTwo::getInstance()->getConnection();
    }

    /**
     * Fetch all report types
     * @return array
     */
    public function all(): array
    {
        $stmt = $this->conn->prepare("
            SELECT type_id, report_type, department_id
            FROM sd_report_type
            ORDER BY report_type ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
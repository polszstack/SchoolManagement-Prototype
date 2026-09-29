<?php

require_once __DIR__ . '/core/DatabaseTwo.php';

class Departments extends DatabaseTwo
{
    protected $table = 'sd_department';
    public function __construct()
    {
        $this->conn = DatabaseTwo::getInstance()->getConnection();
    }

    public function all()
    {
        $stmt = $this->conn->prepare("
            SELECT *
            FROM {$this->table}
            ORDER BY department_name ASC
        ");

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
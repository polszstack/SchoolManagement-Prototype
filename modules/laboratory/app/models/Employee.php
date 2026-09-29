<?php

require_once __DIR__ . '/core/DatabaseTwo.php';

class Employee extends DatabaseTwo
{
    protected $table = 'sms_employee';

    public function __construct()
    {
        $this->conn = DatabaseTwo::getInstance()->getConnection();
    }

    /**
     * Find an employee by ID
     */
    public function findById(int $employeeId)
    {
        $stmt = $this->conn->prepare("
            SELECT * FROM {$this->table} 
            WHERE employee_id = :employee_id
        ");
        $stmt->bindParam(':employee_id', $employeeId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Find the head of a department
     */
    public function findDepartmentHead(int $departmentId)
    {
        $stmt = $this->conn->prepare("
            SELECT e.* 
            FROM {$this->table} e
            INNER JOIN sd_department d ON d.department_head = e.employee_id
            WHERE d.department_id = :department_id
        ");
        $stmt->bindParam(':department_id', $departmentId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Optional: get all employees
     */
    public function all()
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} ORDER BY last_name ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
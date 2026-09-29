<?php

include_once __DIR__ . '/../../../database/db.php';

class Student {
    private $student_number;
    private $first_name;
    private $last_name;
    private $middle_name;
    private $course;
    private $year_level;
    private $gender;
    private $conn;

    public function __construct($pdo = null) {
        if ($pdo instanceof PDO) {
            $this->conn = $pdo;
        } else {
            $database = new Database();
            $this->conn = $database->getConnection();
        }
    }
    
    public function getStudentByNumber($student_number) {
        $stmt = $this->conn->prepare("SELECT * FROM rgr_students WHERE student_number = ?");
        $stmt->execute([$student_number]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getStudentName($student_number) {
        $stmt = $this->conn->prepare("SELECT first_name, last_name FROM rgr_students WHERE student_number = ?");
        $stmt->execute([$student_number]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ✅ ADD THIS METHOD
    public function getAllStudents() {
        $stmt = $this->conn->prepare("SELECT * FROM rgr_students ORDER BY last_name ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
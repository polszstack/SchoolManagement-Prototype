<?php
class StudentManager {
    private $conn;
    public function __construct($conn) { $this->conn = $conn; }
    public function getAllStudents() {
        $stmt = $this->conn->prepare("SELECT s.student_number, CONCAT(s.first_name, ' ', COALESCE(s.middle_name, ''), ' ', s.last_name) as full_name, s.course, s.year_level, s.section, s.academic_status FROM rgr_students s");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getStudentDetails($studentId) {
        $stmt = $this->conn->prepare("SELECT s.*, CONCAT(s.first_name, ' ', COALESCE(s.middle_name, ''), ' ', s.last_name) as full_name FROM rgr_students s WHERE s.student_number = ?");
        $stmt->execute([$studentId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

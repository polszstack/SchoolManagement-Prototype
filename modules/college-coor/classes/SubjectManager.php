<?php
class SubjectManager {
    private $conn;
    public function __construct($conn) { $this->conn = $conn; }
    public function getAllSubjects() {
        $stmt = $this->conn->prepare("SELECT * FROM rgr_subjects");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

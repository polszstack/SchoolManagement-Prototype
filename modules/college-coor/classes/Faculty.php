<?php
class Faculty {
    private $conn;
    private $table = "cc_faculty";

    public $id;
    public $faculty_code;
    public $first_name;
    public $last_name;
    public $email;
    public $department;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY last_name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getByCode($code) {
        $query = "SELECT * FROM " . $this->table . " WHERE faculty_code = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $code);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
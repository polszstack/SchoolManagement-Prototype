<?php
class SectionManager {
    private $conn;
    private $table = "cc_sections";

    public $id;
    public $section_code;
    public $grade_level;
    public $program;
    public $semester;
    public $school_year;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT 
                    cs.*, 
                    CONCAT(COALESCE(cf.first_name, ''), ' ', COALESCE(cf.last_name, '')) as adviser_name
                  FROM " . $this->table . " cs
                  LEFT JOIN cc_faculty cf ON cs.adviser_id = cf.id
                  ORDER BY cs.section_code";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getBySemester($semester, $school_year) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE semester = ? AND school_year = ? 
                  ORDER BY section_code";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $semester);
        $stmt->bindParam(2, $school_year);
        $stmt->execute();
        return $stmt;
    }
}
?>
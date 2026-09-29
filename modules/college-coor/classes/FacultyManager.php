<?php
class FacultyManager {
    private $conn;
    public function __construct($conn) { $this->conn = $conn; }
    public function getFacultyLoad() {
        $sql = "SELECT 
            f.id,
            CONCAT(f.first_name, ' ', f.last_name) AS faculty_name,
            f.department,
            COUNT(DISTINCT cs.id) AS classes_assigned,
            COALESCE(SUM(s.units), 0) AS total_units,
            15 AS max_load,
            0 AS conflicts
        FROM cc_faculty f
        LEFT JOIN cc_schedule cs ON f.id = cs.faculty_id
        LEFT JOIN rgr_subjects s ON cs.subject_code = s.code
        GROUP BY f.id, f.first_name, f.last_name, f.department
        ORDER BY f.id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

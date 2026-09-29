<?php
require_once __DIR__ . '/../config/database.php';

class Course {
    private $conn;
    private $table = 'enr_courses';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Create new course
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (course_code, course_name, description, duration_years, total_units, is_active)
                  VALUES (:code, :name, :desc, :duration, :units, :active)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':code', $data['course_code']);
        $stmt->bindParam(':name', $data['course_name']);
        $stmt->bindParam(':desc', $data['description']);
        $stmt->bindParam(':duration', $data['duration_years']);
        $stmt->bindParam(':units', $data['total_units']);
        $stmt->bindParam(':active', $data['is_active']);
        
        return $stmt->execute();
    }

    // Get all active courses
    public function getAllActive() {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE is_active = 1 
                  ORDER BY course_code ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get course by ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get course by code
    public function getByCode($code) {
        $query = "SELECT * FROM " . $this->table . " WHERE course_code = :code LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':code', $code);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update course
    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " 
                  SET course_code = :code,
                      course_name = :name,
                      description = :desc,
                      duration_years = :duration,
                      total_units = :units,
                      is_active = :active
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':code', $data['course_code']);
        $stmt->bindParam(':name', $data['course_name']);
        $stmt->bindParam(':desc', $data['description']);
        $stmt->bindParam(':duration', $data['duration_years']);
        $stmt->bindParam(':units', $data['total_units']);
        $stmt->bindParam(':active', $data['is_active']);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    // Delete course (soft delete)
    public function delete($id) {
        $query = "UPDATE " . $this->table . " SET is_active = 0 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Get total active courses
    public function getTotalActive() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE is_active = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // Get most selected courses
    public function getMostSelected($limit = 5) {
        $query = "SELECT c.*, COUNT(cs.id) as count 
                  FROM " . $this->table . " c
                  LEFT JOIN enr_course_selections cs ON c.id = cs.course_id
                  WHERE c.is_active = 1
                  GROUP BY c.id
                  ORDER BY count DESC
                  LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
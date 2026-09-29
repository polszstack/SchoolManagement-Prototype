<?php
require_once __DIR__ . '/../config/database.php';

class Announcement {
    private $conn;
    private $table = 'enr_announcements';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Create announcement
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (title, content, target_audience, created_by, publish_date, expiry_date)
                  VALUES 
                  (:title, :content, :target, :created_by, :publish_date, :expiry_date)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':content', $data['content']);
        $stmt->bindParam(':target', $data['target_audience']);
        $stmt->bindParam(':created_by', $data['created_by']);
        $stmt->bindParam(':publish_date', $data['publish_date']);
        $stmt->bindParam(':expiry_date', $data['expiry_date']);
        
        return $stmt->execute();
    }

    // Get all active announcements
    public function getAllActive() {
        $today = date('Y-m-d');
        $query = "SELECT a.*, u.username as created_by_name
                  FROM " . $this->table . " a
                  JOIN enr_users u ON a.created_by = u.id
                  WHERE a.is_published = 1 
                  AND a.publish_date <= :today
                  AND (a.expiry_date IS NULL OR a.expiry_date >= :today)
                  ORDER BY a.publish_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':today', $today);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get announcements for applicants
    public function getForApplicants() {
        $today = date('Y-m-d');
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE is_published = 1 
                  AND (target_audience = 'all' OR target_audience = 'applicants')
                  AND publish_date <= :today
                  AND (expiry_date IS NULL OR expiry_date >= :today)
                  ORDER BY publish_date DESC
                  LIMIT 5";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':today', $today);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get announcements for students
    public function getForStudents() {
        $today = date('Y-m-d');
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE is_published = 1 
                  AND (target_audience = 'all' OR target_audience = 'students')
                  AND publish_date <= :today
                  AND (expiry_date IS NULL OR expiry_date >= :today)
                  ORDER BY publish_date DESC
                  LIMIT 5";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':today', $today);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get announcement by ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update announcement
    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " 
                  SET title = :title,
                      content = :content,
                      target_audience = :target,
                      publish_date = :publish_date,
                      expiry_date = :expiry_date,
                      is_published = :is_published
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':content', $data['content']);
        $stmt->bindParam(':target', $data['target_audience']);
        $stmt->bindParam(':publish_date', $data['publish_date']);
        $stmt->bindParam(':expiry_date', $data['expiry_date']);
        $stmt->bindParam(':is_published', $data['is_published']);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    // Delete announcement
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Publish announcement
    public function publish($id) {
        $query = "UPDATE " . $this->table . " 
                  SET is_published = 1 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Unpublish announcement
    public function unpublish($id) {
        $query = "UPDATE " . $this->table . " 
                  SET is_published = 0 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>
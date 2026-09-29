<?php
include_once __DIR__ . '/../../../database/db.php';

class Counselor {
    private $conn;
    private $db;
    private $table = 'gd_counselor';

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    /**
     * Get all counselors
     */
    public function getAllCounselors() {
        $sql = "SELECT * FROM {$this->table} 
                ORDER BY last_name, first_name";
        
        $stmt = $this->db->executeQuery($sql);
        return $stmt->fetchAll();
    }

    /**
     * Get counselor by ID
     */
    public function getCounselorById($id) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE id = :id 
                LIMIT 1";
        
        $params = [':id' => $id];
        $stmt = $this->db->executeQuery($sql, $params);
        
        return $stmt->fetch();
    }

    /**
     * Get counselor by email
     */
    public function getCounselorByEmail($email) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE email = :email 
                LIMIT 1";
        
        $params = [':email' => $email];
        $stmt = $this->db->executeQuery($sql, $params);
        
        return $stmt->fetch();
    }

    /**
     * Create new counselor
     */
    public function createCounselor($data) {
        $sql = "INSERT INTO {$this->table} 
                (first_name, last_name, email, contact_number, specialization, created_at) 
                VALUES 
                (:first_name, :last_name, :email, :contact_number, :specialization, NOW())";
        
        $params = [
            ':first_name' => $data['first_name'],
            ':last_name' => $data['last_name'],
            ':email' => $data['email'],
            ':contact_number' => $data['contact_number'] ?? null,
            ':specialization' => $data['specialization'] ?? null
        ];
        
        return $this->db->executeQuery($sql, $params);
    }

    /**
     * Update counselor
     */
    public function updateCounselor($id, $data) {
        $sql = "UPDATE {$this->table} 
                SET first_name = :first_name,
                    last_name = :last_name,
                    email = :email,
                    contact_number = :contact_number,
                    specialization = :specialization,
                    updated_at = NOW()
                WHERE id = :id";
        
        $params = [
            ':id' => $id,
            ':first_name' => $data['first_name'],
            ':last_name' => $data['last_name'],
            ':email' => $data['email'],
            ':contact_number' => $data['contact_number'] ?? null,
            ':specialization' => $data['specialization'] ?? null
        ];
        
        return $this->db->executeQuery($sql, $params);
    }

    /**
     * Delete counselor
     */
    public function deleteCounselor($id) {
        // First check if counselor has any sessions
        $checkSql = "SELECT COUNT(*) as count FROM gd_sessions WHERE counselor_id = :id";
        $checkParams = [':id' => $id];
        $checkStmt = $this->db->executeQuery($checkSql, $checkParams);
        $result = $checkStmt->fetch();
        
        if ($result['count'] > 0) {
            throw new Exception("Cannot delete counselor with existing sessions");
        }
        
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $params = [':id' => $id];
        
        return $this->db->executeQuery($sql, $params);
    }

    /**
     * Get counselor sessions
     */
    public function getCounselorSessions($counselor_id) {
        $sql = "SELECT s.*, 
                       stu.first_name as student_fname, 
                       stu.last_name as student_lname,
                       stu.course
                FROM gd_sessions s
                JOIN rgr_students stu ON s.student_id = stu.student_number
                WHERE s.counselor_id = :counselor_id
                ORDER BY s.session_date DESC";
        
        $params = [':counselor_id' => $counselor_id];
        $stmt = $this->db->executeQuery($sql, $params);
        
        return $stmt->fetchAll();
    }

    /**
     * Get counselor statistics
     */
    public function getCounselorStats($counselor_id) {
        $stats = [];
        
        // Total sessions
        $sql = "SELECT COUNT(*) as total_sessions,
                       COUNT(DISTINCT student_id) as unique_students
                FROM gd_sessions 
                WHERE counselor_id = :counselor_id";
        
        $params = [':counselor_id' => $counselor_id];
        $stmt = $this->db->executeQuery($sql, $params);
        $stats['overview'] = $stmt->fetch();
        
        // Sessions by status
        $sql = "SELECT status, COUNT(*) as count 
                FROM gd_sessions 
                WHERE counselor_id = :counselor_id 
                GROUP BY status";
        
        $stmt = $this->db->executeQuery($sql, $params);
        $stats['by_status'] = $stmt->fetchAll();
        
        // Sessions by type
        $sql = "SELECT session_type, COUNT(*) as count 
                FROM gd_sessions 
                WHERE counselor_id = :counselor_id 
                GROUP BY session_type";
        
        $stmt = $this->db->executeQuery($sql, $params);
        $stats['by_type'] = $stmt->fetchAll();
        
        return $stats;
    }

    /**
     * Search counselors
     */
    public function searchCounselors($keyword) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE first_name LIKE :keyword 
                   OR last_name LIKE :keyword 
                   OR email LIKE :keyword
                   OR specialization LIKE :keyword
                ORDER BY last_name, first_name
                LIMIT 50";
        
        $params = [':keyword' => "%$keyword%"];
        $stmt = $this->db->executeQuery($sql, $params);
        
        return $stmt->fetchAll();
    }

    /**
     * Get full name
     */
    public function getFullName($counselor) {
        return $counselor['first_name'] . " " . $counselor['last_name'];
    }
}
?>
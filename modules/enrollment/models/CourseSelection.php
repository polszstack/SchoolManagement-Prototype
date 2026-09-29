<?php
require_once __DIR__ . '/../config/database.php';

class CourseSelection {
    private $conn;
    private $table = 'enr_course_selections';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Add course selection
    public function add($applicant_id, $course_id, $is_continuous = false) {
        $query = "INSERT INTO " . $this->table . " 
                  (applicant_id, course_id, is_continuous, status)
                  VALUES (:applicant_id, :course_id, :is_continuous, 'pending')";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':applicant_id', $applicant_id);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->bindParam(':is_continuous', $is_continuous, PDO::PARAM_BOOL);
        
        return $stmt->execute();
    }

    // Get selections by applicant
    public function getByApplicant($applicant_id) {
        $query = "SELECT cs.*, c.course_code, c.course_name, c.duration_years
                  FROM " . $this->table . " cs
                  JOIN enr_courses c ON cs.course_id = c.id
                  WHERE cs.applicant_id = :applicant_id
                  ORDER BY cs.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':applicant_id', $applicant_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update selection status
    public function updateStatus($id, $status, $remarks = null) {
        $query = "UPDATE " . $this->table . " 
                  SET status = :status, remarks = :remarks 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':remarks', $remarks);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Approve selection
    public function approve($id) {
        return $this->updateStatus($id, 'approved');
    }

    // Reject selection
    public function reject($id, $remarks) {
        return $this->updateStatus($id, 'rejected', $remarks);
    }

    // Check if applicant already selected course
    public function exists($applicant_id, $course_id) {
        $query = "SELECT id FROM " . $this->table . " 
                  WHERE applicant_id = :applicant_id AND course_id = :course_id 
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':applicant_id', $applicant_id);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // Remove selection
    public function remove($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>
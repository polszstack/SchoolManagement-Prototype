<?php
require_once __DIR__ . '/../config/database.php';

class Applicant {
    private $conn;
    private $table = 'enr_applicants';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Generate unique application number
    private function generateApplicationNumber() {
        $year = date('Y');
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " 
                  WHERE YEAR(submitted_at) = :year";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year', $year);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $sequence = str_pad($row['count'] + 1, 5, '0', STR_PAD_LEFT);
        return 'APP-' . $year . '-' . $sequence;
    }

    // Calculate age from birthdate
    private function calculateAge($birthdate) {
        $birth = new DateTime($birthdate);
        $today = new DateTime();
        $age = $today->diff($birth)->y;
        return $age;
    }

    // Create new applicant
    public function create($data) {
        try {
            $this->conn->beginTransaction();

            // Generate application number
            $application_number = $this->generateApplicationNumber();
            
            // Calculate age
            $age = $this->calculateAge($data['date_of_birth']);
            
            // Complete address
            $address_complete = $data['barangay'] . ', ' . $data['city'] . ', ' . $data['province'];

            $query = "INSERT INTO " . $this->table . " 
                      (user_id, application_number, surname, first_name, middle_name, suffix, sex,
                       address_barangay, address_city, address_province, address_complete,
                       school_last_attended, year_graduated, email, date_of_birth, place_of_birth,
                       age, civil_status, contact_number, parent_full_name, parent_contact, parent_address)
                      VALUES 
                      (:user_id, :app_num, :surname, :firstname, :middlename, :suffix, :sex,
                       :barangay, :city, :province, :complete_addr,
                       :school, :year_grad, :email, :dob, :pob,
                       :age, :civil_status, :contact, :parent_name, :parent_contact, :parent_address)";

            $stmt = $this->conn->prepare($query);
            
            // Bind parameters
            $stmt->bindParam(':user_id', $data['user_id']);
            $stmt->bindParam(':app_num', $application_number);
            $stmt->bindParam(':surname', $data['surname']);
            $stmt->bindParam(':firstname', $data['first_name']);
            $stmt->bindParam(':middlename', $data['middle_name']);
            $stmt->bindParam(':suffix', $data['suffix']);
            $stmt->bindParam(':sex', $data['sex']);
            $stmt->bindParam(':barangay', $data['barangay']);
            $stmt->bindParam(':city', $data['city']);
            $stmt->bindParam(':province', $data['province']);
            $stmt->bindParam(':complete_addr', $address_complete);
            $stmt->bindParam(':school', $data['school_last_attended']);
            $stmt->bindParam(':year_grad', $data['year_graduated']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':dob', $data['date_of_birth']);
            $stmt->bindParam(':pob', $data['place_of_birth']);
            $stmt->bindParam(':age', $age);
            $stmt->bindParam(':civil_status', $data['civil_status']);
            $stmt->bindParam(':contact', $data['contact_number']);
            $stmt->bindParam(':parent_name', $data['parent_full_name']);
            $stmt->bindParam(':parent_contact', $data['parent_contact']);
            $stmt->bindParam(':parent_address', $data['parent_address']);

            if($stmt->execute()) {
                $applicant_id = $this->conn->lastInsertId();
                $this->conn->commit();
                return [
                    'success' => true, 
                    'applicant_id' => $applicant_id, 
                    'app_number' => $application_number
                ];
            }
            
            $this->conn->rollBack();
            return ['success' => false, 'message' => 'Failed to create applicant record'];
            
        } catch(PDOException $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Get applicant by user ID
    public function getByUserId($user_id) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE user_id = :user_id 
                  ORDER BY id DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get applicant by ID
    public function getById($id) {
        $query = "SELECT a.*, u.username, u.email 
                  FROM " . $this->table . " a
                  JOIN enr_users u ON a.user_id = u.id
                  WHERE a.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get all pending applicants
    public function getAllPending() {
        $query = "SELECT a.*, u.username, u.email,
                  (SELECT COUNT(*) FROM enr_documents d WHERE d.applicant_id = a.id) as doc_count
                  FROM " . $this->table . " a
                  JOIN enr_users u ON a.user_id = u.id
                  WHERE a.status = 'pending'
                  ORDER BY a.submitted_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get verified applicants
    public function getVerified() {
        $query = "SELECT a.*, u.username, u.email,
                  (SELECT COUNT(*) FROM enr_documents d WHERE d.applicant_id = a.id) as doc_count
                  FROM " . $this->table . " a
                  JOIN enr_users u ON a.user_id = u.id
                  WHERE a.status = 'verified'
                  ORDER BY a.submitted_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get recent applicants
    public function getRecent($limit = 10) {
        $query = "SELECT a.*, u.username, u.email,
                  (SELECT course_code FROM enr_course_selections cs 
                   JOIN enr_courses c ON cs.course_id = c.id 
                   WHERE cs.applicant_id = a.id LIMIT 1) as course_code
                  FROM " . $this->table . " a
                  JOIN enr_users u ON a.user_id = u.id
                  ORDER BY a.submitted_at DESC
                  LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Verify applicant
    public function verify($applicant_id) {
        $query = "UPDATE " . $this->table . " 
                  SET status = 'verified', updated_at = NOW() 
                  WHERE id = :id AND status = 'pending'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $applicant_id);
        return $stmt->execute();
    }

    // Reject applicant
    public function reject($applicant_id, $remarks = null) {
        $query = "UPDATE " . $this->table . " 
                  SET status = 'rejected', updated_at = NOW() 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $applicant_id);
        return $stmt->execute();
    }

    // Get total applications this year
    public function getTotalThisYear() {
        $year = date('Y');
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " 
                  WHERE YEAR(submitted_at) = :year";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year', $year);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // Get pending count
    public function getPendingCount() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " 
                  WHERE status = 'pending'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // Count documents per applicant
    public function countDocuments($applicant_id) {
        $query = "SELECT COUNT(*) as total FROM enr_documents WHERE applicant_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $applicant_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // Count course selections
    public function countCourseSelections($applicant_id) {
        $query = "SELECT COUNT(*) as total FROM enr_course_selections WHERE applicant_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $applicant_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // Update applicant information
    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " SET
                  surname = :surname, first_name = :firstname, 
                  middle_name = :middlename, suffix = :suffix,
                  sex = :sex, address_barangay = :barangay,
                  address_city = :city, address_province = :province,
                  school_last_attended = :school, year_graduated = :year_grad,
                  date_of_birth = :dob, place_of_birth = :pob,
                  civil_status = :civil_status, contact_number = :contact,
                  parent_full_name = :parent_name, parent_contact = :parent_contact,
                  parent_address = :parent_address, updated_at = NOW()
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Calculate new age if birthdate changed
        $age = $this->calculateAge($data['date_of_birth']);
        
        $stmt->bindParam(':surname', $data['surname']);
        $stmt->bindParam(':firstname', $data['first_name']);
        $stmt->bindParam(':middlename', $data['middle_name']);
        $stmt->bindParam(':suffix', $data['suffix']);
        $stmt->bindParam(':sex', $data['sex']);
        $stmt->bindParam(':barangay', $data['barangay']);
        $stmt->bindParam(':city', $data['city']);
        $stmt->bindParam(':province', $data['province']);
        $stmt->bindParam(':school', $data['school_last_attended']);
        $stmt->bindParam(':year_grad', $data['year_graduated']);
        $stmt->bindParam(':dob', $data['date_of_birth']);
        $stmt->bindParam(':pob', $data['place_of_birth']);
        $stmt->bindParam(':civil_status', $data['civil_status']);
        $stmt->bindParam(':contact', $data['contact_number']);
        $stmt->bindParam(':parent_name', $data['parent_full_name']);
        $stmt->bindParam(':parent_contact', $data['parent_contact']);
        $stmt->bindParam(':parent_address', $data['parent_address']);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    // Get applicants by a specific status
    public function getByStatus($status) {
        $query = "SELECT a.*, u.username, u.email,
                  (SELECT course_code FROM enr_course_selections cs 
                   JOIN enr_courses c ON cs.course_id = c.id 
                   WHERE cs.applicant_id = a.id LIMIT 1) as course_code
                  FROM " . $this->table . " a
                  JOIN enr_users u ON a.user_id = u.id
                  WHERE a.status = :status
                  ORDER BY a.submitted_at DESC";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
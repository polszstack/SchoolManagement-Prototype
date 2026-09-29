<?php
require_once __DIR__ . '/../config/database.php';

class Student {
    private $conn;
    private $table = 'enr_students';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Generate student number
    private function generateStudentNumber() {
        $year = date('Y');
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " 
                  WHERE YEAR(enrolled_at) = :year";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year', $year);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $sequence = str_pad($row['count'] + 1, 5, '0', STR_PAD_LEFT);
        return 'STU-' . $year . '-' . $sequence;
    }

    // Convert applicant to student (UPDATED with section_id)
    public function convertFromApplicant($applicant_id, $course_id, $section_id, $admin_id) {
        try {
            $this->conn->beginTransaction();

            // Get applicant details
            $applicant_query = "SELECT * FROM enr_applicants WHERE id = :id";
            $stmt = $this->conn->prepare($applicant_query);
            $stmt->bindParam(':id', $applicant_id);
            $stmt->execute();
            $applicant = $stmt->fetch(PDO::FETCH_ASSOC);

            if(!$applicant) {
                throw new Exception('Applicant not found');
            }

            if($applicant['status'] != 'verified') {
                throw new Exception('Applicant must be verified first');
            }

            // Update applicant status
            $update_app = "UPDATE enr_applicants SET status = 'converted' WHERE id = :id";
            $stmt = $this->conn->prepare($update_app);
            $stmt->bindParam(':id', $applicant_id);
            $stmt->execute();

            // Update user type
            $update_user = "UPDATE enr_users SET user_type = 'student' WHERE id = :user_id";
            $stmt = $this->conn->prepare($update_user);
            $stmt->bindParam(':user_id', $applicant['user_id']);
            $stmt->execute();

            // Generate student number
            $student_number = $this->generateStudentNumber();

            // Insert into students table with section_id
            $query = "INSERT INTO " . $this->table . " 
                      (applicant_id, student_number, user_id, course_id, section_id, year_level, enrollment_status)
                      VALUES 
                      (:applicant_id, :student_number, :user_id, :course_id, :section_id, 1, 'enrolled')";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':applicant_id', $applicant_id);
            $stmt->bindParam(':student_number', $student_number);
            $stmt->bindParam(':user_id', $applicant['user_id']);
            $stmt->bindParam(':course_id', $course_id);
            $stmt->bindParam(':section_id', $section_id);
            $stmt->execute();

            $this->conn->commit();
            return [
                'success' => true, 
                'student_number' => $student_number,
                'student_id' => $this->conn->lastInsertId()
            ];
        } catch(Exception $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Get student by ID (UPDATED with section)
    public function getById($id) {
        $query = "SELECT s.*, a.first_name, a.surname, a.middle_name, a.suffix,
                         a.email, a.contact_number, a.address_complete,
                         c.course_code, c.course_name,
                         sec.section_code, sec.section_name
                  FROM " . $this->table . " s
                  JOIN enr_applicants a ON s.applicant_id = a.id
                  JOIN enr_courses c ON s.course_id = c.id
                  LEFT JOIN enr_sections sec ON s.section_id = sec.id
                  WHERE s.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get student by user ID (UPDATED with section)
    public function getByUserId($user_id) {
        $query = "SELECT s.*, a.first_name, a.surname, a.middle_name, a.suffix,
                         a.email, a.contact_number, a.address_complete,
                         c.course_code, c.course_name,
                         sec.section_code, sec.section_name
                  FROM " . $this->table . " s
                  JOIN enr_applicants a ON s.applicant_id = a.id
                  JOIN enr_courses c ON s.course_id = c.id
                  LEFT JOIN enr_sections sec ON s.section_id = sec.id
                  WHERE s.user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Ensure suffix key exists
        if ($result && !isset($result['suffix'])) {
            $result['suffix'] = '';
        }
        
        return $result;
    }

    // Get student by student number (UPDATED with section)
    public function getByStudentNumber($student_number) {
        $query = "SELECT s.*, a.first_name, a.surname, a.middle_name, a.suffix,
                         a.email, a.contact_number, a.address_complete,
                         c.course_code, c.course_name,
                         sec.section_code, sec.section_name
                  FROM " . $this->table . " s
                  JOIN enr_applicants a ON s.applicant_id = a.id
                  JOIN enr_courses c ON s.course_id = c.id
                  LEFT JOIN enr_sections sec ON s.section_id = sec.id
                  WHERE s.student_number = :student_number";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':student_number', $student_number);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Ensure suffix key exists
        if ($result && !isset($result['suffix'])) {
            $result['suffix'] = '';
        }
        
        return $result;
    }

    // Get all students (UPDATED with section)
    public function getAll($status = 'enrolled') {
        $query = "SELECT s.*, a.first_name, a.surname, a.middle_name, a.suffix,
                         c.course_code, c.course_name,
                         sec.section_code, sec.section_name
                  FROM " . $this->table . " s
                  JOIN enr_applicants a ON s.applicant_id = a.id
                  JOIN enr_courses c ON s.course_id = c.id
                  LEFT JOIN enr_sections sec ON s.section_id = sec.id
                  WHERE s.enrollment_status = :status
                  ORDER BY s.enrolled_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Ensure suffix key exists for all records
        foreach ($results as &$row) {
            if (!isset($row['suffix'])) {
                $row['suffix'] = '';
            }
        }
        
        return $results;
    }

    // Get total students
    public function getTotalStudents() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " 
                  WHERE enrollment_status = 'enrolled'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // Get total students by status
    public function getTotalByStatus($status) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " 
                  WHERE enrollment_status = :status";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // Search students by name or student number (UPDATED with section)
    public function searchStudents($keyword) {
        $keyword = '%' . $keyword . '%';
        $query = "SELECT s.*, a.first_name, a.surname, a.middle_name, a.suffix,
                         c.course_code, c.course_name,
                         sec.section_code, sec.section_name
                  FROM " . $this->table . " s
                  JOIN enr_applicants a ON s.applicant_id = a.id
                  JOIN enr_courses c ON s.course_id = c.id
                  LEFT JOIN enr_sections sec ON s.section_id = sec.id
                  WHERE s.student_number LIKE :keyword 
                  OR a.first_name LIKE :keyword 
                  OR a.surname LIKE :keyword 
                  OR a.middle_name LIKE :keyword
                  ORDER BY s.enrolled_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':keyword', $keyword);
        $stmt->execute();
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Ensure suffix key exists for all records
        foreach ($results as &$row) {
            if (!isset($row['suffix'])) {
                $row['suffix'] = '';
            }
        }
        
        return $results;
    }

    // Get students by year level (UPDATED with section)
    public function getByYearLevel($year_level) {
        $query = "SELECT s.*, a.first_name, a.surname, a.middle_name, a.suffix,
                         c.course_code, c.course_name,
                         sec.section_code, sec.section_name
                  FROM " . $this->table . " s
                  JOIN enr_applicants a ON s.applicant_id = a.id
                  JOIN enr_courses c ON s.course_id = c.id
                  LEFT JOIN enr_sections sec ON s.section_id = sec.id
                  WHERE s.year_level = :year_level 
                  AND s.enrollment_status = 'enrolled'
                  ORDER BY a.surname ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year_level', $year_level);
        $stmt->execute();
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Ensure suffix key exists for all records
        foreach ($results as &$row) {
            if (!isset($row['suffix'])) {
                $row['suffix'] = '';
            }
        }
        
        return $results;
    }

    // Get students by multiple filters (UPDATED with section)
    public function getFilteredStudents($filters = []) {
        $query = "SELECT s.*, a.first_name, a.surname, a.middle_name, a.suffix,
                         c.course_code, c.course_name,
                         sec.section_code, sec.section_name
                  FROM " . $this->table . " s
                  JOIN enr_applicants a ON s.applicant_id = a.id
                  JOIN enr_courses c ON s.course_id = c.id
                  LEFT JOIN enr_sections sec ON s.section_id = sec.id
                  WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['status']) && $filters['status'] != 'all') {
            $query .= " AND s.enrollment_status = :status";
            $params[':status'] = $filters['status'];
        }
        
        if (!empty($filters['course_id'])) {
            $query .= " AND s.course_id = :course_id";
            $params[':course_id'] = $filters['course_id'];
        }
        
        if (!empty($filters['year_level'])) {
            $query .= " AND s.year_level = :year_level";
            $params[':year_level'] = $filters['year_level'];
        }
        
        if (!empty($filters['section_id'])) {
            $query .= " AND s.section_id = :section_id";
            $params[':section_id'] = $filters['section_id'];
        }
        
        $query .= " ORDER BY a.surname ASC, a.first_name ASC";
        
        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Ensure suffix key exists for all records
        foreach ($results as &$row) {
            if (!isset($row['suffix'])) {
                $row['suffix'] = '';
            }
        }
        
        return $results;
    }

    // Export students data (UPDATED with section)
    public function exportStudents($filters = []) {
        $students = $this->getFilteredStudents($filters);
        
        $export_data = [];
        foreach ($students as $s) {
            $export_data[] = [
                'Student Number' => $s['student_number'],
                'Last Name' => $s['surname'],
                'First Name' => $s['first_name'],
                'Middle Name' => $s['middle_name'] ?? '',
                'Suffix' => $s['suffix'] ?? '',
                'Course Code' => $s['course_code'],
                'Course Name' => $s['course_name'],
                'Section' => $s['section_code'] ?? 'N/A',
                'Year Level' => $s['year_level'],
                'Status' => $s['enrollment_status'],
                'Enrolled Date' => $s['enrolled_at']
            ];
        }
        
        return $export_data;
    }

    // Update student section (NEW METHOD)
    public function updateSection($student_id, $section_id) {
        $query = "UPDATE " . $this->table . " 
                  SET section_id = :section_id 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':section_id', $section_id);
        $stmt->bindParam(':id', $student_id);
        return $stmt->execute();
    }

    // Get students by section (NEW METHOD)
    public function getBySection($section_id) {
        $query = "SELECT s.*, a.first_name, a.surname, a.middle_name, a.suffix,
                         c.course_code, c.course_name
                  FROM " . $this->table . " s
                  JOIN enr_applicants a ON s.applicant_id = a.id
                  JOIN enr_courses c ON s.course_id = c.id
                  WHERE s.section_id = :section_id 
                  AND s.enrollment_status = 'enrolled'
                  ORDER BY a.surname ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':section_id', $section_id);
        $stmt->execute();
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Ensure suffix key exists for all records
        foreach ($results as &$row) {
            if (!isset($row['suffix'])) {
                $row['suffix'] = '';
            }
        }
        
        return $results;
    }

    // Update student status
    public function updateStatus($student_id, $status) {
        $query = "UPDATE " . $this->table . " 
                  SET enrollment_status = :status 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $student_id);
        return $stmt->execute();
    }

    // Update year level
    public function updateYearLevel($student_id, $year_level) {
        $query = "UPDATE " . $this->table . " 
                  SET year_level = :year_level 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year_level', $year_level);
        $stmt->bindParam(':id', $student_id);
        return $stmt->execute();
    }

    // Get students by course (UPDATED with section)
    public function getByCourse($course_id) {
        $query = "SELECT s.*, a.first_name, a.surname, a.middle_name, a.suffix,
                         c.course_code, c.course_name,
                         sec.section_code, sec.section_name
                  FROM " . $this->table . " s
                  JOIN enr_applicants a ON s.applicant_id = a.id
                  JOIN enr_courses c ON s.course_id = c.id
                  LEFT JOIN enr_sections sec ON s.section_id = sec.id
                  WHERE s.course_id = :course_id 
                  AND s.enrollment_status = 'enrolled'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->execute();
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Ensure suffix key exists for all records
        foreach ($results as &$row) {
            if (!isset($row['suffix'])) {
                $row['suffix'] = '';
            }
        }
        
        return $results;
    }

    // Graduate student
    public function graduate($student_id) {
        $query = "UPDATE " . $this->table . " 
                  SET enrollment_status = 'graduated' 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $student_id);
        return $stmt->execute();
    }

    // Drop student
    public function drop($student_id, $reason = null) {
        $query = "UPDATE " . $this->table . " 
                  SET enrollment_status = 'dropped' 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $student_id);
        return $stmt->execute();
    }
}
?>
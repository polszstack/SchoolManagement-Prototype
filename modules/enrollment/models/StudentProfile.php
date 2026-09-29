<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Document.php';
require_once __DIR__ . '/FileUploader.php';
require_once __DIR__ . '/Section.php';

class StudentProfile {
    private $conn;
    private $table = 'enr_students';
    private $document;
    private $fileUploader;
    private $section;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->document = new Document();
        $this->fileUploader = new FileUploader();
        $this->section = new Section();
    }

    // Get complete student profile with all details including section
    public function getCompleteProfile($user_id) {
        $query = "SELECT 
                    s.*,
                    a.first_name, 
                    a.surname, 
                    a.middle_name, 
                    a.suffix,
                    a.sex,
                    a.date_of_birth,
                    a.place_of_birth,
                    a.age,
                    a.civil_status,
                    a.contact_number,
                    a.email,
                    a.address_barangay,
                    a.address_city,
                    a.address_province,
                    a.address_complete,
                    a.school_last_attended,
                    a.year_graduated,
                    a.parent_full_name,
                    a.parent_contact,
                    a.parent_address,
                    a.application_number,
                    a.submitted_at as application_date,
                    c.course_code,
                    c.course_name,
                    c.description as course_description,
                    c.duration_years,
                    c.total_units,
                    sec.id as section_id,
                    sec.section_code,
                    sec.section_name,
                    sec.academic_year,
                    sec.semester,
                    sec.max_students,
                    (SELECT COUNT(*) FROM enr_students WHERE section_id = sec.id AND enrollment_status = 'enrolled') as current_students
                  FROM " . $this->table . " s
                  INNER JOIN enr_applicants a ON s.applicant_id = a.id
                  INNER JOIN enr_courses c ON s.course_id = c.id
                  LEFT JOIN enr_sections sec ON s.section_id = sec.id
                  WHERE s.user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get student by ID with section details
    public function getStudentById($student_id) {
        $query = "SELECT 
                    s.*,
                    a.first_name, 
                    a.surname, 
                    a.middle_name, 
                    a.suffix,
                    a.email,
                    a.contact_number,
                    a.address_complete,
                    c.course_code,
                    c.course_name,
                    sec.id as section_id,
                    sec.section_code,
                    sec.section_name,
                    sec.academic_year,
                    sec.semester,
                    sec.max_students,
                    (SELECT COUNT(*) FROM enr_students WHERE section_id = sec.id AND enrollment_status = 'enrolled') as section_current_students
                  FROM " . $this->table . " s
                  INNER JOIN enr_applicants a ON s.applicant_id = a.id
                  INNER JOIN enr_courses c ON s.course_id = c.id
                  LEFT JOIN enr_sections sec ON s.section_id = sec.id
                  WHERE s.id = :student_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':student_id', $student_id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get section details for a student
    public function getStudentSection($student_id) {
        try {
            $query = "SELECT s.section_id, sec.*,
                             (SELECT COUNT(*) FROM enr_students WHERE section_id = sec.id AND enrollment_status = 'enrolled') as current_students
                      FROM enr_students s
                      LEFT JOIN enr_sections sec ON s.section_id = sec.id
                      WHERE s.id = :student_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':student_id', $student_id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting student section: " . $e->getMessage());
            return null;
        }
    }

    // Get all students in the same section (classmates)
    public function getClassmates($student_id, $limit = null) {
        try {
            // First get the student's section
            $query = "SELECT section_id FROM enr_students WHERE id = :student_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':student_id', $student_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$result || !$result['section_id']) {
                return [];
            }
            
            $section_id = $result['section_id'];
            
            // Get all students in the same section except current student
            $query = "SELECT 
                        s.id,
                        s.student_number,
                        s.year_level,
                        a.first_name,
                        a.surname,
                        a.middle_name,
                        a.suffix,
                        a.contact_number,
                        a.email
                      FROM enr_students s
                      INNER JOIN enr_applicants a ON s.applicant_id = a.id
                      WHERE s.section_id = :section_id 
                        AND s.id != :student_id
                        AND s.enrollment_status = 'enrolled'
                      ORDER BY a.surname ASC, a.first_name ASC";
            
            if ($limit) {
                $query .= " LIMIT :limit";
            }
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':section_id', $section_id);
            $stmt->bindParam(':student_id', $student_id);
            if ($limit) {
                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            }
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting classmates: " . $e->getMessage());
            return [];
        }
    }

    // Get classmates count
    public function getClassmatesCount($student_id) {
        try {
            $query = "SELECT section_id FROM enr_students WHERE id = :student_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':student_id', $student_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$result || !$result['section_id']) {
                return 0;
            }
            
            $section_id = $result['section_id'];
            
            $query = "SELECT COUNT(*) as total 
                      FROM enr_students 
                      WHERE section_id = :section_id 
                        AND id != :student_id
                        AND enrollment_status = 'enrolled'";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':section_id', $section_id);
            $stmt->bindParam(':student_id', $student_id);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch(PDOException $e) {
            error_log("Error getting classmates count: " . $e->getMessage());
            return 0;
        }
    }

    // Check if student has a section assigned
    public function hasSection($student_id) {
        try {
            $query = "SELECT section_id FROM enr_students WHERE id = :student_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':student_id', $student_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return !empty($result['section_id']);
        } catch(PDOException $e) {
            error_log("Error checking section: " . $e->getMessage());
            return false;
        }
    }

    // Get available sections for the student's course and year level
    public function getAvailableSections($student_id) {
        try {
            // Get student's course and year level
            $query = "SELECT course_id, year_level FROM enr_students WHERE id = :student_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':student_id', $student_id);
            $stmt->execute();
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$student) {
                return [];
            }
            
            // Get available sections
            return $this->section->getAvailableSections($student['course_id'], $student['year_level']);
            
        } catch(PDOException $e) {
            error_log("Error getting available sections: " . $e->getMessage());
            return [];
        }
    }

    // Assign student to a section (admin function)
    public function assignToSection($student_id, $section_id) {
        try {
            // Check if section exists and has available slots
            if (!$this->section->hasAvailableSlots($section_id)) {
                return ['success' => false, 'message' => 'Section is full or does not exist'];
            }
            
            // Update student's section
            $query = "UPDATE enr_students SET section_id = :section_id WHERE id = :student_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':section_id', $section_id);
            $stmt->bindParam(':student_id', $student_id);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Student assigned to section successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to assign student to section'];
            }
        } catch(PDOException $e) {
            error_log("Error assigning to section: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    // Remove student from current section
    public function removeFromSection($student_id) {
        try {
            $query = "UPDATE enr_students SET section_id = NULL WHERE id = :student_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':student_id', $student_id);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error removing from section: " . $e->getMessage());
            return false;
        }
    }

    // Get section schedule (if you have a schedules table)
    public function getSectionSchedule($section_id) {
        // This is a placeholder - you need to create enr_schedules table first
        return [];
    }

    // Get student documents using Document model
    public function getStudentDocuments($student_id) {
        try {
            // First get the applicant_id from students table
            $query = "SELECT applicant_id FROM enr_students WHERE id = :student_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':student_id', $student_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) {
                return [];
            }
            
            $applicant_id = $result['applicant_id'];
            
            // Use Document model to get documents
            return $this->document->getByApplicant($applicant_id);
            
        } catch(PDOException $e) {
            error_log("Error getting student documents: " . $e->getMessage());
            return [];
        }
    }

    // Get pending documents count
    public function getPendingDocumentsCount($student_id) {
        try {
            $query = "SELECT applicant_id FROM enr_students WHERE id = :student_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':student_id', $student_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) {
                return 0;
            }
            
            $applicant_id = $result['applicant_id'];
            
            $documents = $this->document->getPendingByApplicant($applicant_id);
            return count($documents);
            
        } catch(PDOException $e) {
            error_log("Error getting pending documents count: " . $e->getMessage());
            return 0;
        }
    }

    // Get document requirements
    public function getDocumentRequirements() {
        return $this->document->getRequirements();
    }

    // Upload new document
    public function uploadDocument($student_id, $file, $document_type) {
        try {
            // Get applicant_id
            $query = "SELECT applicant_id FROM enr_students WHERE id = :student_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':student_id', $student_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) {
                return ['success' => false, 'message' => 'Student not found'];
            }
            
            $applicant_id = $result['applicant_id'];
            
            // Upload file using FileUploader
            $upload_result = $this->fileUploader->upload($file, $applicant_id, $document_type);
            
            if (!$upload_result['success']) {
                return $upload_result;
            }
            
            // Save to database using Document model
            if ($this->document->upload($applicant_id, $document_type, $upload_result['file_data'])) {
                return ['success' => true, 'message' => 'Document uploaded successfully'];
            } else {
                // Delete uploaded file if database insert fails
                $this->fileUploader->delete($upload_result['file_data']['path']);
                return ['success' => false, 'message' => 'Failed to save document record'];
            }
            
        } catch(Exception $e) {
            error_log("Error uploading document: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error uploading document'];
        }
    }

    // Delete document
    public function deleteDocument($document_id) {
        try {
            return $this->document->delete($document_id);
        } catch(Exception $e) {
            error_log("Error deleting document: " . $e->getMessage());
            return false;
        }
    }

    // Get enrollment summary (placeholder - needs enrollments table)
    public function getEnrollmentSummary($student_id) {
        // This is a placeholder - you need to create enr_enrollments table first
        return [
            'total_subjects' => 0,
            'passed_subjects' => 0,
            'failed_subjects' => 0,
            'average_grade' => 0,
            'semesters_completed' => 0
        ];
    }

    // Get current enrollment (placeholder - needs enrollments table)
    public function getCurrentEnrollment($student_id) {
        // This is a placeholder - you need to create enr_enrollments table first
        return [];
    }

    // Get academic history (placeholder - needs enrollments table)
    public function getAcademicHistory($student_id) {
        // This is a placeholder - you need to create enr_enrollments table first
        return [];
    }

    // Update contact information
    public function updateContactInfo($student_id, $data) {
        try {
            // First get the applicant_id from students table
            $query = "SELECT applicant_id FROM enr_students WHERE id = :student_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':student_id', $student_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) {
                return false;
            }
            
            $applicant_id = $result['applicant_id'];
            
            // Update enr_applicants table
            $query = "UPDATE enr_applicants 
                      SET contact_number = :contact_number,
                          email = :email,
                          address_barangay = :barangay,
                          address_city = :city,
                          address_province = :province,
                          address_complete = :complete_addr,
                          updated_at = NOW()
                      WHERE id = :applicant_id";
            
            $stmt = $this->conn->prepare($query);
            $complete_addr = $data['barangay'] . ', ' . $data['city'] . ', ' . $data['province'];
            
            $stmt->bindParam(':contact_number', $data['contact_number']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':barangay', $data['barangay']);
            $stmt->bindParam(':city', $data['city']);
            $stmt->bindParam(':province', $data['province']);
            $stmt->bindParam(':complete_addr', $complete_addr);
            $stmt->bindParam(':applicant_id', $applicant_id);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error updating contact info: " . $e->getMessage());
            return false;
        }
    }

    // Update parent information
    public function updateParentInfo($student_id, $data) {
        try {
            // First get the applicant_id from students table
            $query = "SELECT applicant_id FROM enr_students WHERE id = :student_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':student_id', $student_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) {
                return false;
            }
            
            $applicant_id = $result['applicant_id'];
            
            // Update enr_applicants table
            $query = "UPDATE enr_applicants 
                      SET parent_full_name = :parent_name,
                          parent_contact = :parent_contact,
                          parent_address = :parent_address,
                          updated_at = NOW()
                      WHERE id = :applicant_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':parent_name', $data['parent_full_name']);
            $stmt->bindParam(':parent_contact', $data['parent_contact']);
            $stmt->bindParam(':parent_address', $data['parent_address']);
            $stmt->bindParam(':applicant_id', $applicant_id);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error updating parent info: " . $e->getMessage());
            return false;
        }
    }
}
?>
<?php
require_once __DIR__ . '/../config/database.php';

class Section {
    private $conn;
    private $table = 'enr_sections';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Create new section
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (section_code, section_name, course_id, year_level, max_students, academic_year, semester, is_active)
                  VALUES 
                  (:section_code, :section_name, :course_id, :year_level, :max_students, :academic_year, :semester, :is_active)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':section_code', $data['section_code']);
        $stmt->bindParam(':section_name', $data['section_name']);
        $stmt->bindParam(':course_id', $data['course_id']);
        $stmt->bindParam(':year_level', $data['year_level']);
        $stmt->bindParam(':max_students', $data['max_students']);
        $stmt->bindParam(':academic_year', $data['academic_year']);
        $stmt->bindParam(':semester', $data['semester']);
        $stmt->bindParam(':is_active', $data['is_active']);
        
        return $stmt->execute();
    }

    // Get all active sections
    public function getAllActive() {
        $query = "SELECT s.*, c.course_code, c.course_name,
                         (SELECT COUNT(*) FROM enr_students WHERE section_id = s.id AND enrollment_status = 'enrolled') as current_students
                  FROM " . $this->table . " s
                  JOIN enr_courses c ON s.course_id = c.id
                  WHERE s.is_active = 1
                  ORDER BY s.academic_year DESC, s.semester, c.course_code, s.year_level";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get sections by course and year level
    public function getByCourseAndYear($course_id, $year_level, $academic_year = null, $semester = null) {
        if (!$academic_year) {
            $academic_year = $this->getCurrentAcademicYear();
        }
        if (!$semester) {
            $semester = $this->getCurrentSemester();
        }

        $query = "SELECT s.*, 
                         (SELECT COUNT(*) FROM enr_students WHERE section_id = s.id AND enrollment_status = 'enrolled') as current_students,
                         (s.max_students - (SELECT COUNT(*) FROM enr_students WHERE section_id = s.id AND enrollment_status = 'enrolled')) as available_slots
                  FROM " . $this->table . " s
                  WHERE s.course_id = :course_id 
                  AND s.year_level = :year_level
                  AND s.academic_year = :academic_year
                  AND s.semester = :semester
                  AND s.is_active = 1
                  HAVING available_slots > 0
                  ORDER BY s.section_code";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->bindParam(':year_level', $year_level);
        $stmt->bindParam(':academic_year', $academic_year);
        $stmt->bindParam(':semester', $semester);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get section by ID
    public function getById($id) {
        $query = "SELECT s.*, c.course_code, c.course_name,
                         (SELECT COUNT(*) FROM enr_students WHERE section_id = s.id AND enrollment_status = 'enrolled') as current_students
                  FROM " . $this->table . " s
                  JOIN enr_courses c ON s.course_id = c.id
                  WHERE s.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update section
    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " 
                  SET section_code = :section_code,
                      section_name = :section_name,
                      course_id = :course_id,
                      year_level = :year_level,
                      max_students = :max_students,
                      academic_year = :academic_year,
                      semester = :semester,
                      is_active = :is_active
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':section_code', $data['section_code']);
        $stmt->bindParam(':section_name', $data['section_name']);
        $stmt->bindParam(':course_id', $data['course_id']);
        $stmt->bindParam(':year_level', $data['year_level']);
        $stmt->bindParam(':max_students', $data['max_students']);
        $stmt->bindParam(':academic_year', $data['academic_year']);
        $stmt->bindParam(':semester', $data['semester']);
        $stmt->bindParam(':is_active', $data['is_active']);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    // Delete section (soft delete)
    public function delete($id) {
        $query = "UPDATE " . $this->table . " SET is_active = 0 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Get current academic year
    public function getCurrentAcademicYear() {
        $year = date('Y');
        $month = date('m');
        
        // If month is June to December, academic year is current year - next year
        if ($month >= 6) {
            return $year . '-' . ($year + 1);
        } else {
            // If month is January to May, academic year is previous year - current year
            return ($year - 1) . '-' . $year;
        }
    }

    // Get current semester
    public function getCurrentSemester() {
        $month = date('m');
        
        if ($month >= 6 && $month <= 10) {
            return '1st Semester';
        } elseif ($month >= 11 || $month <= 3) {
            return '2nd Semester';
        } else {
            return 'Summer';
        }
    }

    // Get available sections for enrollment
    public function getAvailableSections($course_id, $year_level) {
        $academic_year = $this->getCurrentAcademicYear();
        $semester = $this->getCurrentSemester();
        
        return $this->getByCourseAndYear($course_id, $year_level, $academic_year, $semester);
    }

    // Check if section has available slots
    public function hasAvailableSlots($section_id) {
        $query = "SELECT s.max_students,
                         (SELECT COUNT(*) FROM enr_students WHERE section_id = s.id AND enrollment_status = 'enrolled') as enrolled
                  FROM " . $this->table . " s
                  WHERE s.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $section_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result && $result['enrolled'] < $result['max_students'];
    }

    // Get total sections count
    public function getTotalSections() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE is_active = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // Get sections by course
    public function getByCourse($course_id) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE course_id = :course_id AND is_active = 1
                  ORDER BY year_level, section_code";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
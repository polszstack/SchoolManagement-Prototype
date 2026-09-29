<?php
require_once __DIR__ . '/../config/database.php';

class Report {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Get application statistics
    public function getApplicationStats($year = null) {
        if(!$year) $year = date('Y');
        
        try {
            $query = "SELECT 
                        COUNT(*) as total_applications,
                        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                        SUM(CASE WHEN status = 'verified' THEN 1 ELSE 0 END) as verified,
                        SUM(CASE WHEN status = 'converted' THEN 1 ELSE 0 END) as converted,
                        SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
                      FROM enr_applicants 
                      WHERE YEAR(submitted_at) = :year";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':year', $year);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Ensure all keys exist with default values
            return [
                'total_applications' => (int)($result['total_applications'] ?? 0),
                'pending' => (int)($result['pending'] ?? 0),
                'verified' => (int)($result['verified'] ?? 0),
                'converted' => (int)($result['converted'] ?? 0),
                'rejected' => (int)($result['rejected'] ?? 0)
            ];
        } catch (PDOException $e) {
            error_log("Error in getApplicationStats: " . $e->getMessage());
            return [
                'total_applications' => 0,
                'pending' => 0,
                'verified' => 0,
                'converted' => 0,
                'rejected' => 0
            ];
        }
    }

    // Get monthly applications
    public function getMonthlyApplications($year = null) {
        if(!$year) $year = date('Y');
        
        try {
            $query = "SELECT 
                        MONTH(submitted_at) as month,
                        COUNT(*) as count
                      FROM enr_applicants 
                      WHERE YEAR(submitted_at) = :year
                      GROUP BY MONTH(submitted_at)
                      ORDER BY month ASC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':year', $year);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Create an array with all 12 months (1-12) with default 0 counts
            $monthlyData = [];
            for ($i = 1; $i <= 12; $i++) {
                $monthlyData[$i] = 0;
            }
            
            // Fill in actual data
            foreach ($results as $row) {
                $month = (int)$row['month'];
                $monthlyData[$month] = (int)$row['count'];
            }
            
            // Format for the chart (convert to indexed array)
            $formatted = [];
            foreach ($monthlyData as $month => $count) {
                $formatted[] = [
                    'month' => $month,
                    'count' => $count
                ];
            }
            
            return $formatted;
        } catch (PDOException $e) {
            error_log("Error in getMonthlyApplications: " . $e->getMessage());
            return [];
        }
    }

    // Get course enrollment report
    public function getCourseEnrollmentReport() {
        try {
            $query = "SELECT 
                        c.course_code,
                        c.course_name,
                        COUNT(DISTINCT s.id) as enrolled_students,
                        (SELECT COUNT(*) FROM enr_course_selections cs 
                         WHERE cs.course_id = c.id) as pending_applications
                      FROM enr_courses c
                      LEFT JOIN enr_students s ON c.id = s.course_id 
                          AND s.enrollment_status = 'enrolled'
                      WHERE c.is_active = 1
                      GROUP BY c.id, c.course_code, c.course_name
                      ORDER BY enrolled_students DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Ensure numeric values
            foreach ($results as &$row) {
                $row['enrolled_students'] = (int)($row['enrolled_students'] ?? 0);
                $row['pending_applications'] = (int)($row['pending_applications'] ?? 0);
            }
            
            return $results;
        } catch (PDOException $e) {
            error_log("Error in getCourseEnrollmentReport: " . $e->getMessage());
            return [];
        }
    }

    // Get demographic report - FIXED VERSION
    public function getDemographicReport() {
        $result = [
            'gender' => [],
            'civil_status' => []
        ];
        
        try {
            // Get gender distribution - using 'sex' column instead of 'gender'
            $genderQuery = "SELECT 
                              sex as gender_value,
                              COUNT(*) as count
                            FROM enr_applicants 
                            WHERE sex IS NOT NULL AND sex != ''
                            GROUP BY sex";
            
            $stmt = $this->conn->prepare($genderQuery);
            $stmt->execute();
            $genderResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Format gender results with proper 'gender' key
            foreach ($genderResults as $row) {
                $result['gender'][] = [
                    'gender' => $row['gender_value'],
                    'count' => (int)$row['count']
                ];
            }
            
            // Get civil status distribution - using 'civil_status' column
            $civilQuery = "SELECT 
                             civil_status,
                             COUNT(*) as count
                           FROM enr_applicants 
                           WHERE civil_status IS NOT NULL AND civil_status != ''
                           GROUP BY civil_status";
            
            $stmt = $this->conn->prepare($civilQuery);
            $stmt->execute();
            $civilResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Format civil status results
            foreach ($civilResults as $row) {
                $result['civil_status'][] = [
                    'civil_status' => $row['civil_status'],
                    'count' => (int)$row['count']
                ];
            }
            
            return $result;
            
        } catch (PDOException $e) {
            error_log("Error in getDemographicReport: " . $e->getMessage());
            return $result;
        }
    }

    // Get document submission report
    public function getDocumentReport() {
        try {
            $query = "SELECT 
                        document_type,
                        COUNT(*) as total_uploaded,
                        SUM(CASE WHEN status = 'verified' THEN 1 ELSE 0 END) as verified,
                        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                        SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
                      FROM enr_documents 
                      GROUP BY document_type
                      ORDER BY total_uploaded DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getDocumentReport: " . $e->getMessage());
            return [];
        }
    }

    // Get student population report
    public function getStudentPopulationReport() {
        try {
            $query = "SELECT 
                        c.course_code,
                        c.course_name,
                        s.year_level,
                        COUNT(s.id) as total_students
                      FROM enr_students s
                      JOIN enr_courses c ON s.course_id = c.id
                      WHERE s.enrollment_status = 'enrolled'
                      GROUP BY c.id, s.year_level
                      ORDER BY c.course_code, s.year_level";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getStudentPopulationReport: " . $e->getMessage());
            return [];
        }
    }

    // Get conversion rate
    public function getConversionRate($year = null) {
        if(!$year) $year = date('Y');
        
        try {
            $query = "SELECT 
                        (SELECT COUNT(*) FROM enr_applicants WHERE YEAR(submitted_at) = :year) as total_applicants,
                        (SELECT COUNT(*) FROM enr_students WHERE YEAR(enrolled_at) = :year) as total_enrolled";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':year', $year);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $total_applicants = (int)($result['total_applicants'] ?? 0);
            $total_enrolled = (int)($result['total_enrolled'] ?? 0);
            
            $conversion_rate = 0;
            if($total_applicants > 0) {
                $conversion_rate = round(($total_enrolled / $total_applicants) * 100, 2);
            }
            
            return [
                'total_applicants' => $total_applicants,
                'total_enrolled' => $total_enrolled,
                'conversion_rate' => $conversion_rate
            ];
        } catch (PDOException $e) {
            error_log("Error in getConversionRate: " . $e->getMessage());
            return [
                'total_applicants' => 0,
                'total_enrolled' => 0,
                'conversion_rate' => 0
            ];
        }
    }

    // Get all reports summary
    public function getSummaryReport() {
        try {
            require_once 'Student.php';
            require_once 'Course.php';
            require_once 'Applicant.php';
            
            $student = new Student();
            $course = new Course();
            $applicant = new Applicant();
            
            return [
                'applications' => $this->getApplicationStats(),
                'students' => [
                    'total' => $student->getTotalStudents()
                ],
                'courses' => [
                    'total' => $course->getTotalActive()
                ],
                'conversion' => $this->getConversionRate(),
                'recent_applications' => $applicant->getRecent(5)
            ];
        } catch (Exception $e) {
            error_log("Error in getSummaryReport: " . $e->getMessage());
            return [];
        }
    }
}
?>
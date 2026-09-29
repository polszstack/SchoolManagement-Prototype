
<?php
include_once __DIR__ . '/../../../database/db.php';

class DatabaseHelper {
    public function getUpcomingEvents($limit = 5) {
        $stmt = $this->conn->prepare("SELECT * FROM cc_events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get calendar events with upcoming status
    public function getCalendarEvents() {
        try {
            $stmt = $this->conn->prepare("SELECT event_title, event_date, start_time, status FROM cc_events WHERE status = 'upcoming' ORDER BY event_date ASC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    // Summary methods for dashboard
    public function getTotalProgram() {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM rgr_courses");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    public function getTotalStudents() {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM rgr_students");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    public function getTotalFaculty() {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM cc_faculty");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    public function getTotalSections() {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM cc_sections");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    public function getTotalSubjects() {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM rgr_subjects");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // Chart Data Methods
    public function getStudentsPerProgram() {
        try {
            // Use the actual enrolled students to compute counts per course
            $stmt = $this->conn->prepare("SELECT 
                course as program_code,
                COUNT(*) as student_count
            FROM rgr_students
            WHERE course IS NOT NULL AND course != ''
            GROUP BY course
            ORDER BY student_count DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public function getFacultyLoadDistribution() {
        try {
            $stmt = $this->conn->prepare("SELECT 
                CONCAT(f.first_name, ' ', f.last_name) as faculty_name,
                COUNT(DISTINCT cs.id) as classes_assigned,
                COALESCE(SUM(s.units), 0) as total_units
            FROM cc_faculty f
            LEFT JOIN cc_schedule cs ON f.id = cs.faculty_id
            LEFT JOIN rgr_subjects s ON cs.subject_code = s.code
            GROUP BY f.id, f.first_name, f.last_name
            ORDER BY total_units DESC
            LIMIT 10");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public function getStudentAcademicStatus() {
        try {
            $stmt = $this->conn->prepare("SELECT 
                CASE 
                    WHEN academic_status = 'active' THEN 'Active'
                    WHEN academic_status = 'inactive' THEN 'Inactive'
                    WHEN academic_status = 'graduated' THEN 'Graduated'
                    ELSE academic_status
                END as status,
                COUNT(*) as count
            FROM rgr_students
            GROUP BY academic_status
            ORDER BY 
                CASE academic_status
                    WHEN 'active' THEN 1
                    WHEN 'inactive' THEN 2
                    WHEN 'graduated' THEN 3
                    ELSE 4
                END");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Get recent activities from all modules
     */
    public function getRecentActivities($limit = 15) {
        try {
            $activities = [];

            // 1. Recent Events
            try {
                $stmt = $this->conn->prepare("SELECT event_id, event_title, 'Academic Event' as activity_type, created_at FROM cc_events ORDER BY created_at DESC LIMIT 5");
                $stmt->execute();
                $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($events as $event) {
                    $activities[] = [
                        'id' => $event['event_id'],
                        'type' => $event['activity_type'],
                        'description' => 'Created event: ' . $event['event_title'],
                        'module' => 'Academic Events',
                        'timestamp' => $event['created_at']
                    ];
                }
            } catch (Exception $e) {}

            // 2. Recent Appointments
            try {
                $stmt = $this->conn->prepare("SELECT appointment_id, appointment_type, 'Appointment Scheduled' as activity_title, created_at FROM cc_appointments ORDER BY created_at DESC LIMIT 5");
                $stmt->execute();
                $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($appointments as $apt) {
                    $activities[] = [
                        'id' => $apt['appointment_id'],
                        'type' => $apt['activity_title'],
                        'description' => 'Scheduled ' . strtolower($apt['appointment_type']) . ' appointment',
                        'module' => 'Appointments',
                        'timestamp' => $apt['created_at']
                    ];
                }
            } catch (Exception $e) {}

            // 3. Recent Schedule Assignments
            try {
                $stmt = $this->conn->prepare("SELECT cs.id, CONCAT(f.first_name, ' ', f.last_name) as faculty_name, cs.subject_code, cs.created_at 
                    FROM cc_schedule cs 
                    LEFT JOIN cc_faculty f ON cs.faculty_id = f.id 
                    ORDER BY cs.created_at DESC LIMIT 5");
                $stmt->execute();
                $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($schedules as $sch) {
                    $activities[] = [
                        'id' => $sch['id'],
                        'type' => 'Faculty Assignment',
                        'description' => 'Assigned ' . ($sch['faculty_name'] ?? 'Faculty') . ' to ' . $sch['subject_code'],
                        'module' => 'Faculty Schedule',
                        'timestamp' => $sch['created_at']
                    ];
                }
            } catch (Exception $e) {}

            // 4. Recent Section Updates
            try {
                $stmt = $this->conn->prepare("SELECT section_id, section_code, CONCAT(f.first_name, ' ', f.last_name) as adviser, cc_sections.created_at 
                    FROM cc_sections
                    LEFT JOIN cc_faculty f ON cc_sections.adviser_id = f.id
                    ORDER BY cc_sections.created_at DESC LIMIT 5");
                $stmt->execute();
                $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($sections as $sec) {
                    $activities[] = [
                        'id' => $sec['section_id'],
                        'type' => 'Section Management',
                        'description' => 'Section ' . $sec['section_code'] . ' assigned to ' . ($sec['adviser'] ?? 'TBA'),
                        'module' => 'Academics',
                        'timestamp' => $sec['created_at']
                    ];
                }
            } catch (Exception $e) {}

            // 5. Recent Student Enrollments
            try {
                $stmt = $this->conn->prepare("SELECT student_id, id_number, first_name, last_name, created_at 
                    FROM rgr_students 
                    ORDER BY created_at DESC LIMIT 5");
                $stmt->execute();
                $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($students as $stud) {
                    $activities[] = [
                        'id' => $stud['student_id'],
                        'type' => 'Student Enrollment',
                        'description' => 'New student enrolled: ' . $stud['first_name'] . ' ' . $stud['last_name'],
                        'module' => 'Enrollment',
                        'timestamp' => $stud['created_at']
                    ];
                }
            } catch (Exception $e) {}

            // Sort by timestamp descending
            usort($activities, function($a, $b) {
                return strtotime($b['timestamp']) - strtotime($a['timestamp']);
            });

            // Return limited results
            return array_slice($activities, 0, $limit);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Reusable fetchAll method for OOP controllers
     */
    public function fetchAll($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
}
?>

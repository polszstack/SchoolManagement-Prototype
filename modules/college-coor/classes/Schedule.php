<?php
class Schedule {
    private $conn;
    private $table = "cc_schedule";

    public $id;
    public $room;
    public $official_time;
    public $start_time;
    public $end_time;
    public $day_of_week;
    public $subject_code;
    public $grade_section_id;
    public $faculty_id;
    public $semester;
    public $school_year;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get schedule by date and time (existing method)
    public function getByDateTime($date, $time_breaker) {
        $day = date('l', strtotime($date));
        
        $query = "SELECT s.*, f.first_name, f.last_name, sec.section_code 
                  FROM " . $this->table . " s
                  LEFT JOIN cc_faculty f ON s.faculty_id = f.id
                  LEFT JOIN cc_sections sec ON s.grade_section_id = sec.id
                  WHERE s.day_of_week = ? 
                  AND s.official_time = ? 
                  AND s.semester = ? 
                  AND s.school_year = ?
                  ORDER BY s.room";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $day);
        $stmt->bindParam(2, $time_breaker);
        $stmt->bindParam(3, $this->semester);
        $stmt->bindParam(4, $this->school_year);
        $stmt->execute();
        
        return $stmt;
    }

    // Create new schedule (existing method)
    public function create() {
    try {
        // Use proper INSERT syntax with column names and VALUES
        $query = "INSERT INTO " . $this->table . " 
                  (room, official_time, start_time, end_time, day_of_week, 
                   subject_code, grade_section_id, faculty_id, semester, school_year) 
                  VALUES 
                  (:room, :official_time, :start_time, :end_time, :day_of_week,
                   :subject_code, :grade_section_id, :faculty_id, :semester, :school_year)";
        
        $stmt = $this->conn->prepare($query);
        
        // Don't clean data if it's already clean (optional)
        // But keep it for security
        $this->room = htmlspecialchars(strip_tags($this->room));
        $this->official_time = htmlspecialchars(strip_tags($this->official_time));
        $this->start_time = htmlspecialchars(strip_tags($this->start_time));
        $this->end_time = htmlspecialchars(strip_tags($this->end_time));
        $this->day_of_week = htmlspecialchars(strip_tags($this->day_of_week));
        $this->subject_code = htmlspecialchars(strip_tags($this->subject_code));
        $this->grade_section_id = (int)$this->grade_section_id; // Ensure integer
        $this->faculty_id = (int)$this->faculty_id; // Ensure integer
        $this->semester = htmlspecialchars(strip_tags($this->semester));
        $this->school_year = htmlspecialchars(strip_tags($this->school_year));
        
        // Bind parameters with proper types
        $stmt->bindParam(":room", $this->room, PDO::PARAM_STR);
        $stmt->bindParam(":official_time", $this->official_time, PDO::PARAM_STR);
        $stmt->bindParam(":start_time", $this->start_time, PDO::PARAM_STR);
        $stmt->bindParam(":end_time", $this->end_time, PDO::PARAM_STR);
        $stmt->bindParam(":day_of_week", $this->day_of_week, PDO::PARAM_STR);
        $stmt->bindParam(":subject_code", $this->subject_code, PDO::PARAM_STR);
        $stmt->bindParam(":grade_section_id", $this->grade_section_id, PDO::PARAM_INT);
        $stmt->bindParam(":faculty_id", $this->faculty_id, PDO::PARAM_INT);
        $stmt->bindParam(":semester", $this->semester, PDO::PARAM_STR);
        $stmt->bindParam(":school_year", $this->school_year, PDO::PARAM_STR);
        
        if ($stmt->execute()) {
            return true;
        }
        
        // If we get here, execute failed
        $error = $stmt->errorInfo();
        error_log("Schedule creation failed: " . print_r($error, true));
        
        // For debugging, you might want to see the actual error
        throw new PDOException("Execute failed: " . ($error[2] ?? 'Unknown error'));
        
    } catch (PDOException $e) {
        error_log("PDOException in Schedule::create(): " . $e->getMessage());
        
        // Re-throw if you want to catch it in the calling code
        throw $e;
        
        // return false; // Comment this out if re-throwing
    }
}


    // NEW: Delete schedule
     public function delete() {
    try {
        // delete attendance first
        $stmt1 = $this->conn->prepare(
            "DELETE FROM mon_attendance WHERE schedule_id = ?"
        );
        $stmt1->execute([$this->id]);

        // delete schedule
        $stmt2 = $this->conn->prepare(
            "DELETE FROM cc_schedule WHERE id = ?"
        );
        return $stmt2->execute([$this->id]);

    } catch (PDOException $e) {
        echo $e->getMessage();
        return false;
    }
}

    // NEW: Get all schedules with filters
    public function getAllWithFilters($semester, $school_year, $day_of_week = null, $room = null) {
        $query = "SELECT s.*, f.first_name, f.last_name, f.faculty_code,
                         sec.section_code, sec.grade_level, sec.program
                  FROM " . $this->table . " s
                  LEFT JOIN cc_faculty f ON s.faculty_id = f.id
                  LEFT JOIN cc_sections sec ON s.grade_section_id = sec.id
                  WHERE s.semester = ? AND s.school_year = ?";
        
        $params = [$semester, $school_year];
        
        if ($day_of_week) {
            $query .= " AND s.day_of_week = ?";
            $params[] = $day_of_week;
        }
        
        if ($room) {
            $query .= " AND s.room = ?";
            $params[] = $room;
        }
        
        $query .= " ORDER BY 
                    FIELD(s.day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'),
                    s.start_time, s.room";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        
        return $stmt;
    }

    // NEW: Get distinct days for a semester and school year
    public function getDistinctDays($semester, $school_year) {
        $query = "SELECT DISTINCT day_of_week 
                  FROM " . $this->table . " 
                  WHERE semester = ? AND school_year = ? 
                  ORDER BY FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $semester);
        $stmt->bindParam(2, $school_year);
        $stmt->execute();
        
        return $stmt;
    }

    // NEW: Get distinct rooms for a semester and school year
    public function getDistinctRooms($semester, $school_year) {
        $query = "SELECT DISTINCT room 
                  FROM " . $this->table . " 
                  WHERE semester = ? AND school_year = ? 
                  ORDER BY room";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $semester);
        $stmt->bindParam(2, $school_year);
        $stmt->execute();
        
        return $stmt;
    }

    // NEW: Get schedules by faculty
    public function getByFaculty($faculty_id, $semester, $school_year) {
        $query = "SELECT s.*, sec.section_code, sec.grade_level
                  FROM " . $this->table . " s
                  LEFT JOIN cc_sections sec ON s.grade_section_id = sec.id
                  WHERE s.faculty_id = ? 
                  AND s.semester = ? 
                  AND s.school_year = ?
                  ORDER BY s.day_of_week, s.start_time";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $faculty_id);
        $stmt->bindParam(2, $semester);
        $stmt->bindParam(3, $school_year);
        $stmt->execute();
        
        return $stmt;
    }

    // NEW: Get schedules by section
    public function getBySection($section_id, $semester, $school_year) {
        $query = "SELECT s.*, f.first_name, f.last_name, f.faculty_code
                  FROM " . $this->table . " s
                  LEFT JOIN cc_faculty f ON s.faculty_id = f.id
                  WHERE s.grade_section_id = ? 
                  AND s.semester = ? 
                  AND s.school_year = ?
                  ORDER BY s.day_of_week, s.start_time";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $section_id);
        $stmt->bindParam(2, $semester);
        $stmt->bindParam(3, $school_year);
        $stmt->execute();
        
        return $stmt;
    }

    // NEW: Get schedules by room
    public function getByRoom($room, $semester, $school_year) {
        $query = "SELECT s.*, f.first_name, f.last_name, sec.section_code
                  FROM " . $this->table . " s
                  LEFT JOIN cc_faculty f ON s.faculty_id = f.id
                  LEFT JOIN cc_sections sec ON s.grade_section_id = sec.id
                  WHERE s.room = ? 
                  AND s.semester = ? 
                  AND s.school_year = ?
                  ORDER BY s.day_of_week, s.start_time";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $room);
        $stmt->bindParam(2, $semester);
        $stmt->bindParam(3, $school_year);
        $stmt->execute();
        
        return $stmt;
    }

    // NEW: Get schedule statistics
    public function getStatistics($semester, $school_year) {
        $query = "SELECT 
                    COUNT(*) as total_schedules,
                    COUNT(DISTINCT room) as total_rooms,
                    COUNT(DISTINCT faculty_id) as total_faculty,
                    COUNT(DISTINCT grade_section_id) as total_sections,
                    COUNT(DISTINCT subject_code) as total_subjects
                  FROM " . $this->table . " 
                  WHERE semester = ? AND school_year = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $semester);
        $stmt->bindParam(2, $school_year);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // NEW: Check for schedule conflicts
    public function checkConflict($room, $day_of_week, $start_time, $end_time, $semester, $school_year, $exclude_id = null) {
        $query = "SELECT COUNT(*) as conflict_count 
                  FROM " . $this->table . " 
                  WHERE room = ? 
                  AND day_of_week = ? 
                  AND semester = ? 
                  AND school_year = ?
                  AND (
                    (start_time < ? AND end_time > ?) OR
                    (start_time < ? AND end_time > ?) OR
                    (start_time >= ? AND end_time <= ?)
                  )";
        
        if ($exclude_id) {
            $query .= " AND id != ?";
        }
        
        $stmt = $this->conn->prepare($query);
        
        if ($exclude_id) {
            $stmt->bindParam(1, $room);
            $stmt->bindParam(2, $day_of_week);
            $stmt->bindParam(3, $semester);
            $stmt->bindParam(4, $school_year);
            $stmt->bindParam(5, $end_time);
            $stmt->bindParam(6, $start_time);
            $stmt->bindParam(7, $end_time);
            $stmt->bindParam(8, $start_time);
            $stmt->bindParam(9, $start_time);
            $stmt->bindParam(10, $end_time);
            $stmt->bindParam(11, $exclude_id);
        } else {
            $stmt->bindParam(1, $room);
            $stmt->bindParam(2, $day_of_week);
            $stmt->bindParam(3, $semester);
            $stmt->bindParam(4, $school_year);
            $stmt->bindParam(5, $end_time);
            $stmt->bindParam(6, $start_time);
            $stmt->bindParam(7, $end_time);
            $stmt->bindParam(8, $start_time);
            $stmt->bindParam(9, $start_time);
            $stmt->bindParam(10, $end_time);
        }
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['conflict_count'] > 0;
    }

    // NEW: Get weekly schedule summary
    public function getWeeklySummary($semester, $school_year) {
        $query = "SELECT 
                    day_of_week,
                    COUNT(*) as total_classes,
                    GROUP_CONCAT(DISTINCT room ORDER BY room) as rooms,
                    MIN(start_time) as earliest_start,
                    MAX(end_time) as latest_end
                  FROM " . $this->table . " 
                  WHERE semester = ? AND school_year = ?
                  GROUP BY day_of_week
                  ORDER BY FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $semester);
        $stmt->bindParam(2, $school_year);
        $stmt->execute();
        
        return $stmt;
    }

    // NEW: Search schedules
    public function search($keyword, $semester, $school_year) {
        $query = "SELECT s.*, f.first_name, f.last_name, f.faculty_code,
                         sec.section_code, sec.grade_level
                  FROM " . $this->table . " s
                  LEFT JOIN cc_faculty f ON s.faculty_id = f.id
                  LEFT JOIN cc_sections sec ON s.grade_section_id = sec.id
                  WHERE (s.room LIKE ? OR s.subject_code LIKE ? 
                         OR f.first_name LIKE ? OR f.last_name LIKE ?
                         OR sec.section_code LIKE ?)
                  AND s.semester = ? AND s.school_year = ?
                  ORDER BY s.day_of_week, s.start_time";
        
        $stmt = $this->conn->prepare($query);
        $search_pattern = "%{$keyword}%";
        $stmt->bindParam(1, $search_pattern);
        $stmt->bindParam(2, $search_pattern);
        $stmt->bindParam(3, $search_pattern);
        $stmt->bindParam(4, $search_pattern);
        $stmt->bindParam(5, $search_pattern);
        $stmt->bindParam(6, $semester);
        $stmt->bindParam(7, $school_year);
        $stmt->execute();
        
        return $stmt;
    }

    // NEW: Check if schedule exists
    public function scheduleExists($room, $day_of_week, $start_time, $subject_code, $semester, $school_year) {
        $query = "SELECT id FROM " . $this->table . " 
                  WHERE room = ? AND day_of_week = ? 
                  AND start_time = ? AND subject_code = ?
                  AND semester = ? AND school_year = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $room);
        $stmt->bindParam(2, $day_of_week);
        $stmt->bindParam(3, $start_time);
        $stmt->bindParam(4, $subject_code);
        $stmt->bindParam(5, $semester);
        $stmt->bindParam(6, $school_year);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
}
?>
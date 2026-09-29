<?php
class ProgramManager {
    private $conn;
    public function __construct($conn) { $this->conn = $conn; }
    public function getAllPrograms() {
        $stmt = $this->conn->prepare("SELECT * FROM rgr_courses");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getCurriculum($programId) {
        // Sample: group by year/semester
        $stmt = $this->conn->prepare("SELECT * FROM curriculum WHERE program_id = ? ORDER BY year_level, semester, subject_code");
        $stmt->execute([$programId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $curriculum = [];
        foreach ($rows as $row) {
            $year = $row['year_level'];
            $sem = $row['semester'];
            if (!isset($curriculum[$year])) $curriculum[$year] = [];
            if (!isset($curriculum[$year][$sem])) $curriculum[$year][$sem] = [];
            $curriculum[$year][$sem][] = [
                'code' => $row['subject_code'],
                'name' => $row['subject_name'],
                'units' => $row['units']
            ];
        }
        // Format for JS
        $result = [];
        foreach ($curriculum as $year => $semesters) {
            $semArr = [];
            foreach ($semesters as $sem => $subjects) {
                $semArr[] = ['semester' => $sem, 'subjects' => $subjects];
            }
            $result[] = ['year_level' => $year, 'semesters' => $semArr];
        }
        return $result;
    }
}

<?php

/**
 * StudentMonitoring
 *
 * Handles all data access for the Student Monitoring page (School Directress
 * module). Reads directly from the flat rgr_students table — no joins.
 * Follows the same pattern as Employee.php / Announcement.php: optional
 * constructor injection, falling back to Database::getConnection().
 */
class StudentMonitoring
{
    private PDO $conn;

    public function __construct(?PDO $conn = null)
    {
        if ($conn) {
            $this->conn = $conn;
        } else {
            require_once __DIR__ . '/../../../database/db.php';
            $database   = new Database();
            $this->conn = $database->getConnection();
        }
    }

    /**
     * Fetches students, distinct courses, and computed stats in one call.
     * If the query fails, returns empty results with $error set rather
     * than masking the failure with fake data.
     */
    public function getDashboardData(): array
    {
        try {
            $students = $this->getStudents();
            $courses  = $this->getDistinctCourses();

            return [
                'students' => $students,
                'courses'  => $courses,
                'stats'    => $this->computeStats($students),
                'error'    => null,
            ];
        } catch (PDOException $e) {
            return [
                'students' => [],
                'courses'  => [],
                'stats'    => $this->computeStats([]),
                'error'    => $e->getMessage(),
            ];
        }
    }

    public function getStudents()
    {
        $stmt = $this->conn->query("
            SELECT
                student_number, first_name, middle_name, last_name,
                gender, birth_date, course, year_level, section,
                email, phone, address, academic_status,
                graduated_at, created_at, updated_at
            FROM rgr_students
            ORDER BY created_at DESC
        ");

        return $stmt->fetchAll();
    }

    /** No separate courses table — pull the distinct values used in rgr_students itself. */
    public function getDistinctCourses(): array
    {
        $stmt = $this->conn->query("
            SELECT DISTINCT course
            FROM rgr_students
            WHERE course IS NOT NULL AND course != ''
            ORDER BY course
        ");

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function computeStats(array $students): array
    {
        $stats = ['total' => count($students), 'active' => 0, 'inactive' => 0, 'graduated' => 0];

        foreach ($students as $r) {
            $key = $r['academic_status'];
            if (isset($stats[$key])) {
                $stats[$key]++;
            }
        }

        return $stats;
    }

    /* ── Presentation helpers ──────────────────────────────────────── */

    public static function fullName(array $r): string
    {
        return trim($r['first_name'] . ' ' . ($r['middle_name'] ? $r['middle_name'][0] . '. ' : '') . $r['last_name']);
    }

    public static function initials(array $r): string
    {
        return strtoupper(substr($r['first_name'], 0, 1) . substr($r['last_name'], 0, 1));
    }

    public static function age(?string $birthDate): ?int
    {
        if (!$birthDate) {
            return null;
        }
        return (int) date_diff(date_create($birthDate), date_create('today'))->y;
    }

    public static function statusMeta(string $s): array
    {
        $map = [
            'active'    => ['label' => 'Active',    'cls' => 's-active'],
            'inactive'  => ['label' => 'Inactive',  'cls' => 's-inactive'],
            'graduated' => ['label' => 'Graduated', 'cls' => 's-graduated'],
        ];

        return $map[$s] ?? ['label' => ucfirst($s), 'cls' => 's-default'];
    }

    /** year_level is stored as YEAR(4) but treated as a 1–4(-ish) standing, not a calendar year. */
    public static function yearLabel($y): string
    {
        $y      = (int) $y;
        $labels = ['', '1st', '2nd', '3rd', '4th', '5th'];
        if ($y < 1) {
            return '—';
        }
        return ($labels[$y] ?? "{$y}th") . ' Yr';
    }

}
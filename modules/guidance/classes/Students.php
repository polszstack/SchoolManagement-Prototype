<?php
/**
 * Students.php
 * Model / data-access layer for the Student Profile Management module.
 *
 * Responsible for all SQL. Returns plain arrays — no HTTP, no JSON, no
 * request handling here. StudentsController is the only thing that
 * should talk to this class.
 *
 * rgr_students columns (confirmed): student_number, first_name,
 * middle_name, last_name, gender, birth_date, course, year_level,
 * section, email, phone, address, academic_status, graduated_at.
 * Note: no guardian fields and no photo column exist on this table —
 * the UI/mockup has been adjusted to drop those.
 *
 * History tab sources (confirmed):
 *   - gd_counseling_sessions and gd_referrals both belong to a case
 *     (case_id NOT NULL) — joined through gd_cases.student_number.
 *   - gd_appointments and gd_incidents link directly via student_number
 *     (case_id is optional/nullable on those two).
 */

include_once __DIR__ . '/../../../database/db.php';
// NOTE: adjust the relative path above to wherever database/db.php actually
// sits relative to this file — copied from the Report.php pattern, matching
// depth may differ depending on where Students.php lives in your structure.

class Students
{
    private $conn;

    public function __construct($pdo = null)
    {
        if ($pdo instanceof PDO) {
            $this->conn = $pdo;
        } else {
            $database = new Database();
            $this->conn = $database->getConnection();
        }
    }

    /* ---------------------------------------------------------------
       List + pagination
    --------------------------------------------------------------- */
    public function getList(array $filters, int $page, int $pageSize): array
    {
        [$whereSql, $params] = $this->buildListFilters($filters);
        $offset = ($page - 1) * $pageSize;

        $total = $this->countList($whereSql, $params);

        $sql = "
            SELECT
                p.profile_id,
                s.student_number,
                CONCAT(s.last_name, ', ', s.first_name) AS name,
                s.year_level,
                s.section,
                s.course,
                p.risk_level,
                p.guidance_status,
                p.updated_at
            FROM gd_student_profiles p
            JOIN rgr_students s ON s.student_number = p.student_number
            {$whereSql}
            ORDER BY p.updated_at DESC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }
        $stmt->bindValue(':limit', $pageSize, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $rows = array_map([$this, 'formatListRow'], $stmt->fetchAll(PDO::FETCH_ASSOC));

        return [
            'rows'  => $rows,
            'total' => $total,
        ];
    }

    private function formatListRow(array $row): array
    {
        $row['initials']            = $this->initialsFromName($row['name']);
        $row['year_section']        = "Year {$row['year_level']} - {$row['section']}";
        $row['updated_at_display']  = date('M d, Y', strtotime($row['updated_at']));
        return $row;
    }

    private function initialsFromName(string $name): string
    {
        $parts   = array_filter(preg_split('/[\s,]+/', $name));
        $letters = array_map(fn($p) => mb_strtoupper(mb_substr($p, 0, 1)), array_slice($parts, 0, 2));
        return implode('', $letters);
    }

    private function countList(string $whereSql, array $params): int
    {
        $sql = "
            SELECT COUNT(*)
            FROM gd_student_profiles p
            JOIN rgr_students s ON s.student_number = p.student_number
            {$whereSql}
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    private function buildListFilters(array $filters): array
    {
        $where  = [];
        $params = [];

        if (!empty($filters['search'])) {
            $where[] = '(s.first_name LIKE :search OR s.last_name LIKE :search OR s.student_number LIKE :search)';
            $params['search'] = "%{$filters['search']}%";
        }
        if (!empty($filters['year_level'])) {
            $where[] = 's.year_level = :year_level';
            $params['year_level'] = $filters['year_level'];
        }
        if (!empty($filters['section'])) {
            $where[] = 's.section = :section';
            $params['section'] = $filters['section'];
        }
        if (!empty($filters['course'])) {
            $where[] = 's.course = :course';
            $params['course'] = $filters['course'];
        }
        if (!empty($filters['risk'])) {
            $where[] = 'p.risk_level = :risk';
            $params['risk'] = $filters['risk'];
        }
        if (!empty($filters['status'])) {
            $where[] = 'p.guidance_status = :status';
            $params['status'] = $filters['status'];
        }

        $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
        return [$whereSql, $params];
    }

    /* ---------------------------------------------------------------
       Summary counts (stat cards)
       NOTE: 'total' reflects the whole currently-enrolled student
       population (rgr_students where academic_status='active'), not
       just students who already have a guidance profile/case. The
       other three counts are still scoped to gd_student_profiles since
       "active/monitoring/high risk" are guidance-specific states.
    --------------------------------------------------------------- */
    public function getSummaryCounts(): array
    {
        $totalStmt = $this->conn->prepare("
            SELECT COUNT(*) AS total
            FROM rgr_students
            WHERE academic_status = 'active'
        ");
        $totalStmt->execute();
        $total = (int) $totalStmt->fetchColumn();

        $stmt = $this->conn->prepare("
            SELECT
                SUM(guidance_status = 'Active') AS active,
                SUM(guidance_status = 'Monitoring') AS monitoring,
                SUM(risk_level = 'High') AS high_risk
            FROM gd_student_profiles
        ");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'total'      => $total,
            'active'     => (int) ($row['active'] ?? 0),
            'monitoring' => (int) ($row['monitoring'] ?? 0),
            'high_risk'  => (int) ($row['high_risk'] ?? 0),
        ];
    }

    /* ---------------------------------------------------------------
       Profile overview
    --------------------------------------------------------------- */
    public function getOverview(string $studentNumber): ?array
    {
        $stmt = $this->conn->prepare("
            SELECT
                s.student_number,
                CONCAT(s.last_name, ', ', s.first_name) AS name,
                s.year_level,
                s.section,
                s.course,
                s.gender,
                s.birth_date,
                s.email,
                s.phone,
                s.address,
                s.academic_status,
                p.risk_level,
                p.guidance_status
            FROM gd_student_profiles p
            JOIN rgr_students s ON s.student_number = p.student_number
            WHERE s.student_number = :student_number
        ");
        $stmt->execute(['student_number' => $studentNumber]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /* ---------------------------------------------------------------
       Guidance remarks
       NOTE: given schema has a single `remarks` text column, not a
       history table. This currently returns/stores just the latest
       entry. See saveRemark() note below.
    --------------------------------------------------------------- */
    public function getRemarks(string $studentNumber): array
    {
        $stmt = $this->conn->prepare("
            SELECT remarks, updated_at
            FROM gd_student_profiles
            WHERE student_number = :student_number
        ");
        $stmt->execute(['student_number' => $studentNumber]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row || empty($row['remarks'])) {
            return [];
        }

        return [[
            'date' => $row['updated_at'],
            'by'   => '', // no author column in the current schema
            'text' => $row['remarks'],
        ]];
    }

    public function saveRemark(string $studentNumber, string $remarks): bool
    {
        // Overwrites the single remarks column. Switch to an INSERT into a
        // dedicated gd_student_remarks history table if a running log is
        // needed instead of "latest remark only".
        $stmt = $this->conn->prepare("
            UPDATE gd_student_profiles
            SET remarks = :remarks, updated_at = NOW()
            WHERE student_number = :student_number
        ");
        return $stmt->execute([
            'remarks' => $remarks,
            'student_number' => $studentNumber,
        ]);
    }

    /* ---------------------------------------------------------------
       History tabs — confirmed schema. Counseling and referrals belong
       to a case (case_id NOT NULL), so both join through gd_cases to
       reach the student. Appointments and incidents link directly via
       student_number (case_id is optional/nullable on those two).
       Counseling/Referral detail is intentionally NOT surfaced here
       anymore — the profile was too crowded. getCaseHistory() below
       gives the case-level summary; drilling into a specific case's
       referral/session detail happens by navigating to the Cases module.
    --------------------------------------------------------------- */
    public function getCaseHistory(string $studentNumber): array
    {
        $stmt = $this->conn->prepare("
            SELECT
                c.case_id,
                c.case_number,
                c.case_type,
                c.priority,
                c.status,
                c.summary,
                c.opened_at,
                c.closed_at,
                CONCAT(e.first_name, ' ', e.last_name) AS counselor_name
            FROM gd_cases c
            JOIN sms_employee e ON e.employee_id = c.counselor_id
            WHERE c.student_number = :student_number
            ORDER BY c.opened_at DESC
        ");
        $stmt->execute(['student_number' => $studentNumber]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAppointmentHistory(string $studentNumber): array
    {
        $stmt = $this->conn->prepare("
            SELECT
                appointment_date AS date,
                purpose AS title,
                status,
                remarks AS `desc`,
                meeting_type
            FROM gd_appointments
            WHERE student_number = :student_number
            ORDER BY appointment_date DESC
        ");
        $stmt->execute(['student_number' => $studentNumber]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getIncidentHistory(string $studentNumber): array
    {
        $stmt = $this->conn->prepare("
            SELECT
                incident_date AS date,
                incident_type AS title,
                description AS `desc`,
                severity,
                status
            FROM gd_incidents
            WHERE student_number = :student_number
            ORDER BY incident_date DESC
        ");
        $stmt->execute(['student_number' => $studentNumber]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ---------------------------------------------------------------
       Documents
    --------------------------------------------------------------- */
    public function getDocuments(string $studentNumber): array
    {
        $stmt = $this->conn->prepare("
            SELECT document_id AS id, file_name AS name, document_type AS type, uploaded_at AS date
            FROM gd_student_documents
            WHERE student_number = :student_number
            ORDER BY uploaded_at DESC
        ");
        $stmt->execute(['student_number' => $studentNumber]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function saveDocument(array $data): bool
    {
        $stmt = $this->conn->prepare("
            INSERT INTO gd_student_documents
                (student_number, uploaded_by, document_type, file_name, file_path)
            VALUES
                (:student_number, :uploaded_by, :document_type, :file_name, :file_path)
        ");
        return $stmt->execute([
            'student_number' => $data['student_number'],
            'uploaded_by'    => $data['uploaded_by'],
            'document_type'  => $data['document_type'],
            'file_name'      => $data['file_name'],
            'file_path'      => $data['file_path'],
        ]);
    }
}
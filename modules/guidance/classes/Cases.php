<?php
/**
 * Cases.php
 * Model / data-access layer for the Case Management module
 * (Referral + Counseling), matching the Students.php pattern.
 *
 * Confirmed tables: gd_cases, gd_referrals, gd_counseling_sessions,
 * rgr_students, sms_employee (+ sd_position for counselor scoping).
 *
 * Business-logic decisions locked in with the project owner:
 *   - Case number format: CASE-YYYY-#### (year-scoped sequence)
 *   - Accepting a referral bumps an 'Open' case to 'In Progress'
 *   - Rejecting a referral closes the case (status='Closed', closed_at=NOW())
 *   - "Counselor" = any employee in department 8 (Guidance and
 *     Counseling Office), regardless of specific position
 *   - Cancelled/No-show appointments are out of scope here (Appointments module)
 */

include_once __DIR__ . '/../../../database/db.php';
// NOTE: adjust this relative path if Cases.php doesn't sit at the same
// folder depth as Students.php / Report.php.

class Cases
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
                c.case_id,
                c.case_number,
                c.student_number,
                CONCAT(s.last_name, ', ', s.first_name) AS student_name,
                c.case_type,
                c.priority,
                c.status,
                c.counselor_id,
                CONCAT(e.first_name, ' ', e.last_name) AS counselor_name,
                c.opened_at,
                c.closed_at
            FROM gd_cases c
            JOIN rgr_students s ON s.student_number = c.student_number
            JOIN sms_employee e ON e.employee_id = c.counselor_id
            {$whereSql}
            ORDER BY c.opened_at DESC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }
        $stmt->bindValue(':limit', $pageSize, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $rows = array_map(function ($row) {
            $row['initials'] = $this->initialsFromName($row['student_name']);
            return $row;
        }, $stmt->fetchAll(PDO::FETCH_ASSOC));

        return ['rows' => $rows, 'total' => $total];
    }

    private function countList(string $whereSql, array $params): int
    {
        $sql = "
            SELECT COUNT(*)
            FROM gd_cases c
            JOIN rgr_students s ON s.student_number = c.student_number
            JOIN sms_employee e ON e.employee_id = c.counselor_id
            {$whereSql}
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    private function buildListFilters(array $filters): array
    {
        $where = [];
        $params = [];

        if (!empty($filters['search'])) {
            $where[] = '(s.first_name LIKE :search OR s.last_name LIKE :search OR c.case_number LIKE :search)';
            $params['search'] = "%{$filters['search']}%";
        }
        if (!empty($filters['status'])) {
            $where[] = 'c.status = :status';
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['priority'])) {
            $where[] = 'c.priority = :priority';
            $params['priority'] = $filters['priority'];
        }
        if (!empty($filters['case_type'])) {
            $where[] = 'c.case_type = :case_type';
            $params['case_type'] = $filters['case_type'];
        }
        if (!empty($filters['counselor_id'])) {
            $where[] = 'c.counselor_id = :counselor_id';
            $params['counselor_id'] = $filters['counselor_id'];
        }

        $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
        return [$whereSql, $params];
    }

    /* ---------------------------------------------------------------
       Case detail (overview + referral + sessions)
    --------------------------------------------------------------- */
    public function getCaseOverview(int $caseId): ?array
    {
        $stmt = $this->conn->prepare("
            SELECT
                c.case_id,
                c.case_number,
                c.student_number,
                CONCAT(s.last_name, ', ', s.first_name) AS student_name,
                c.case_type,
                c.priority,
                c.status,
                c.summary,
                c.counselor_id,
                CONCAT(e.first_name, ' ', e.last_name) AS counselor_name,
                c.opened_at,
                c.closed_at
            FROM gd_cases c
            JOIN rgr_students s ON s.student_number = c.student_number
            JOIN sms_employee e ON e.employee_id = c.counselor_id
            WHERE c.case_id = :case_id
        ");
        $stmt->execute(['case_id' => $caseId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function getReferral(int $caseId): ?array
    {
        $stmt = $this->conn->prepare("
            SELECT
                r.referral_id,
                r.referred_by,
                CONCAT(e.first_name, ' ', e.last_name) AS referred_by_name,
                r.referral_source,
                r.referral_reason,
                r.referral_status,
                r.referral_date,
                r.remarks
            FROM gd_referrals r
            JOIN sms_employee e ON e.employee_id = r.referred_by
            WHERE r.case_id = :case_id
            ORDER BY r.referral_date DESC
            LIMIT 1
        ");
        $stmt->execute(['case_id' => $caseId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function getCounselingSessions(int $caseId): array
    {
        $stmt = $this->conn->prepare("
            SELECT
                session_id,
                session_date,
                session_type,
                duration_minutes,
                session_notes,
                recommendations,
                next_session
            FROM gd_counseling_sessions
            WHERE case_id = :case_id
            ORDER BY session_date DESC
        ");
        $stmt->execute(['case_id' => $caseId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ---------------------------------------------------------------
       Case creation
    --------------------------------------------------------------- */
    public function createCase(array $data): int
    {
        // A case implies ongoing guidance involvement — the student must
        // have a gd_student_profiles row to show up in the Students
        // module's caseload view at all. Auto-create a default one
        // (Low risk, Active status) if this is their first-ever case.
        $this->ensureStudentProfile($data['student_number']);

        $caseNumber = $this->generateCaseNumber();

        $stmt = $this->conn->prepare("
            INSERT INTO gd_cases
                (student_number, counselor_id, case_number, case_type, priority, status, summary, opened_at)
            VALUES
                (:student_number, :counselor_id, :case_number, :case_type, :priority, 'Open', :summary, NOW())
        ");
        $stmt->execute([
            'student_number' => $data['student_number'],
            'counselor_id'   => $data['counselor_id'],
            'case_number'    => $caseNumber,
            'case_type'      => $data['case_type'],
            'priority'       => $data['priority'] ?? 'Medium',
            'summary'        => $data['summary'] ?? null,
        ]);

        return (int) $this->conn->lastInsertId();
    }

    /**
     * Creates a default gd_student_profiles row for this student if one
     * doesn't already exist. Idempotent/safe to call on every case
     * creation — existing profiles (and their real risk_level/
     * guidance_status/remarks) are never touched or overwritten.
     */
    private function ensureStudentProfile(string $studentNumber): void
    {
        $stmt = $this->conn->prepare("
            SELECT profile_id FROM gd_student_profiles WHERE student_number = :student_number
        ");
        $stmt->execute(['student_number' => $studentNumber]);
        if ($stmt->fetchColumn()) {
            return; // profile already exists — leave it exactly as-is
        }

        $stmt = $this->conn->prepare("
            INSERT INTO gd_student_profiles (student_number, risk_level, guidance_status)
            VALUES (:student_number, 'Low', 'Active')
        ");
        $stmt->execute(['student_number' => $studentNumber]);
    }

    /**
     * Submit a referral: creates the parent case (case_type='Referral')
     * and the gd_referrals row together in one transaction, since a
     * referral cannot exist without its case (case_id NOT NULL).
     */
    public function submitReferral(array $data): int
    {
        $this->conn->beginTransaction();
        try {
            $caseId = $this->createCase([
                'student_number' => $data['student_number'],
                'counselor_id'   => $data['counselor_id'],
                'case_type'      => 'Referral',
                'priority'       => $data['priority'] ?? 'Medium',
                'summary'        => $data['referral_reason'] ?? null,
            ]);

            $stmt = $this->conn->prepare("
                INSERT INTO gd_referrals
                    (case_id, referred_by, referral_source, referral_reason, referral_status, referral_date, remarks)
                VALUES
                    (:case_id, :referred_by, :referral_source, :referral_reason, 'Pending', :referral_date, :remarks)
            ");
            $stmt->execute([
                'case_id'         => $caseId,
                'referred_by'     => $data['referred_by'],
                'referral_source' => $data['referral_source'],
                'referral_reason' => $data['referral_reason'],
                'referral_date'   => $data['referral_date'] ?? date('Y-m-d'),
                'remarks'         => $data['remarks'] ?? null,
            ]);

            $this->conn->commit();
            return $caseId;
        } catch (Throwable $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    /**
     * Unlinked incidents (case_id IS NULL) across all students, for the
     * "Select Incident" picker shown when case_type = 'Incident'. Incident
     * selection is the source of truth for which student the case is
     * for — the student_number field in the UI gets auto-filled/locked
     * from whichever incident is picked, not the other way around.
     */
    public function getUnlinkedIncidents(): array
    {
        $stmt = $this->conn->prepare("
            SELECT
                i.incident_id,
                i.student_number,
                CONCAT(s.last_name, ', ', s.first_name) AS student_name,
                i.incident_type,
                i.severity,
                i.incident_date,
                i.location,
                i.description
            FROM gd_incidents i
            JOIN rgr_students s ON s.student_number = i.student_number
            WHERE i.case_id IS NULL
            ORDER BY i.incident_date DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Creates an Incident-type case from an existing, unlinked incident
     * record, then writes the link back onto that incident (case_id).
     * student_number is pulled from the incident row itself server-side
     * (not trusted from client input) so the case can never end up
     * attached to a different student than the incident actually is.
     * The WHERE case_id IS NULL guard prevents a race where the same
     * incident gets linked to two cases if double-submitted.
     *
     * @throws RuntimeException if the incident doesn't exist or is
     *         already linked to a case
     */
    public function createCaseFromIncident(array $data): int
    {
        $stmt = $this->conn->prepare("
            SELECT student_number, incident_type, description
            FROM gd_incidents
            WHERE incident_id = :incident_id AND case_id IS NULL
        ");
        $stmt->execute(['incident_id' => $data['incident_id']]);
        $incident = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$incident) {
            throw new RuntimeException('This incident no longer exists or is already linked to a case.');
        }

        $this->conn->beginTransaction();
        try {
            $caseId = $this->createCase([
                'student_number' => $incident['student_number'],
                'counselor_id'   => $data['counselor_id'],
                'case_type'      => 'Incident',
                'priority'       => $data['priority'] ?? 'Medium',
                'summary'        => $data['summary'] ?: $incident['description'],
            ]);

            $update = $this->conn->prepare("
                UPDATE gd_incidents SET case_id = :case_id
                WHERE incident_id = :incident_id AND case_id IS NULL
            ");
            $update->execute(['case_id' => $caseId, 'incident_id' => $data['incident_id']]);

            if ($update->rowCount() === 0) {
                // Someone else linked this incident in the moment between
                // our SELECT check and this UPDATE — bail out entirely.
                throw new RuntimeException('This incident was just linked to another case. Please refresh and try again.');
            }

            $this->conn->commit();
            return $caseId;
        } catch (Throwable $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    private function generateCaseNumber(): string
    {
        $year = date('Y');
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) FROM gd_cases WHERE case_number LIKE :pattern
        ");
        $stmt->execute(['pattern' => "CASE-{$year}-%"]);
        $next = ((int) $stmt->fetchColumn()) + 1;
        return sprintf('CASE-%s-%04d', $year, $next);
    }

    /* ---------------------------------------------------------------
       Case updates
    --------------------------------------------------------------- */
    public function updateStatus(int $caseId, string $status): bool
    {
        if ($status === 'Closed') {
            $stmt = $this->conn->prepare("
                UPDATE gd_cases SET status = 'Closed', closed_at = NOW() WHERE case_id = :case_id
            ");
        } elseif ($status === 'Open') {
            // Reopening: clear closed_at
            $stmt = $this->conn->prepare("
                UPDATE gd_cases SET status = 'Open', closed_at = NULL WHERE case_id = :case_id
            ");
        } else {
            $stmt = $this->conn->prepare("
                UPDATE gd_cases SET status = :status WHERE case_id = :case_id
            ");
            $stmt->bindValue(':status', $status);
        }
        $stmt->bindValue(':case_id', $caseId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function assignCounselor(int $caseId, int $counselorId): bool
    {
        $stmt = $this->conn->prepare("UPDATE gd_cases SET counselor_id = :counselor_id WHERE case_id = :case_id");
        return $stmt->execute(['counselor_id' => $counselorId, 'case_id' => $caseId]);
    }

    public function setPriority(int $caseId, string $priority): bool
    {
        $stmt = $this->conn->prepare("UPDATE gd_cases SET priority = :priority WHERE case_id = :case_id");
        return $stmt->execute(['priority' => $priority, 'case_id' => $caseId]);
    }

    /**
     * Accept: referral_status='Accepted'; bumps case to 'In Progress' if
     * it's still 'Open'.
     * Reject: referral_status='Rejected'; closes the case.
     */
    public function reviewReferral(int $caseId, string $decision, ?string $remarks = null): bool
    {
        $this->conn->beginTransaction();
        try {
            $status = $decision === 'accept' ? 'Accepted' : 'Rejected';

            $stmt = $this->conn->prepare("
                UPDATE gd_referrals
                SET referral_status = :status, remarks = :remarks
                WHERE case_id = :case_id
            ");
            $stmt->execute(['status' => $status, 'remarks' => $remarks, 'case_id' => $caseId]);

            if ($decision === 'accept') {
                $stmt = $this->conn->prepare("
                    UPDATE gd_cases SET status = 'In Progress' WHERE case_id = :case_id AND status = 'Open'
                ");
                $stmt->execute(['case_id' => $caseId]);
            } else {
                $this->updateStatus($caseId, 'Closed');
            }

            $this->conn->commit();
            return true;
        } catch (Throwable $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    /* ---------------------------------------------------------------
       Counseling sessions
    --------------------------------------------------------------- */
    public function recordSession(array $data): int
    {
        $stmt = $this->conn->prepare("
            INSERT INTO gd_counseling_sessions
                (case_id, counselor_id, session_date, session_type, duration_minutes, session_notes, recommendations, next_session)
            VALUES
                (:case_id, :counselor_id, :session_date, :session_type, :duration_minutes, :session_notes, :recommendations, :next_session)
        ");
        $stmt->execute([
            'case_id'          => $data['case_id'],
            'counselor_id'     => $data['counselor_id'],
            'session_date'     => $data['session_date'] ?? date('Y-m-d H:i:s'),
            'session_type'     => $data['session_type'],
            'duration_minutes' => $data['duration_minutes'] ?? null,
            'session_notes'    => $data['session_notes'] ?? null,
            'recommendations'  => $data['recommendations'] ?? null,
            'next_session'     => $data['next_session'] ?: null,
        ]);
        return (int) $this->conn->lastInsertId();
    }

    public function updateSession(int $sessionId, array $data): bool
    {
        $stmt = $this->conn->prepare("
            UPDATE gd_counseling_sessions SET
                session_type = :session_type,
                duration_minutes = :duration_minutes,
                session_notes = :session_notes,
                recommendations = :recommendations,
                next_session = :next_session
            WHERE session_id = :session_id
        ");
        return $stmt->execute([
            'session_type'     => $data['session_type'],
            'duration_minutes' => $data['duration_minutes'] ?? null,
            'session_notes'    => $data['session_notes'] ?? null,
            'recommendations'  => $data['recommendations'] ?? null,
            'next_session'     => $data['next_session'] ?: null,
            'session_id'       => $sessionId,
        ]);
    }

    /* ---------------------------------------------------------------
       Counselors (whole Guidance and Counseling Office department)
    --------------------------------------------------------------- */
    public function getCounselors(): array
    {
        $stmt = $this->conn->prepare("
            SELECT e.employee_id, CONCAT(e.first_name, ' ', e.last_name) AS name, p.position_name
            FROM sms_employee e
            JOIN sd_position p ON p.position_id = e.position
            WHERE e.department = 8 AND e.status = 'active'
            ORDER BY e.last_name ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ---------------------------------------------------------------
       Helpers
    --------------------------------------------------------------- */
    private function initialsFromName(string $name): string
    {
        $parts = array_filter(preg_split('/[\s,]+/', $name));
        $letters = array_map(fn($p) => mb_strtoupper(mb_substr($p, 0, 1)), array_slice($parts, 0, 2));
        return implode('', $letters);
    }
}
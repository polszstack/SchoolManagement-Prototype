<?php
/**
 * Incidents.php
 * Model / data-access layer for the Incident Management module.
 *
 * Confirmed table: gd_incidents (student_number, reported_by,
 * case_id nullable, incident_type, severity, incident_date, location,
 * description, action_taken, action_date, status).
 *
 * NOTE (intentional, per project owner): this build does NOT yet include
 * the cross-module "Escalate to Case" flow (auto-creating a gd_cases row
 * from an incident and writing case_id back). case_id linking here is
 * manual only — a plain optional field on create/edit. The smarter
 * escalation workflow is a planned follow-up once all four modules exist.
 * Evidence upload is also explicitly out of scope for this pass.
 */

include_once __DIR__ . '/../../../database/db.php';
// NOTE: adjust this relative path if Incidents.php doesn't sit at the
// same folder depth as Students.php / Cases.php / Appointments.php.

class Incidents
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
                i.incident_id,
                i.student_number,
                CONCAT(s.last_name, ', ', s.first_name) AS student_name,
                i.reported_by,
                CONCAT(e.first_name, ' ', e.last_name) AS reported_by_name,
                i.case_id,
                c.case_number,
                i.incident_type,
                i.severity,
                i.incident_date,
                i.status
            FROM gd_incidents i
            JOIN rgr_students s ON s.student_number = i.student_number
            JOIN sms_employee e ON e.employee_id = i.reported_by
            LEFT JOIN gd_cases c ON c.case_id = i.case_id
            {$whereSql}
            ORDER BY i.incident_date DESC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }
        $stmt->bindValue(':limit', $pageSize, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return ['rows' => $stmt->fetchAll(PDO::FETCH_ASSOC), 'total' => $total];
    }

    private function countList(string $whereSql, array $params): int
    {
        $sql = "
            SELECT COUNT(*)
            FROM gd_incidents i
            JOIN rgr_students s ON s.student_number = i.student_number
            JOIN sms_employee e ON e.employee_id = i.reported_by
            LEFT JOIN gd_cases c ON c.case_id = i.case_id
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
            $where[] = '(s.first_name LIKE :search OR s.last_name LIKE :search OR s.student_number LIKE :search OR i.incident_type LIKE :search)';
            $params['search'] = "%{$filters['search']}%";
        }
        if (!empty($filters['severity'])) {
            $where[] = 'i.severity = :severity';
            $params['severity'] = $filters['severity'];
        }
        if (!empty($filters['status'])) {
            $where[] = 'i.status = :status';
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['incident_type'])) {
            $where[] = 'i.incident_type = :incident_type';
            $params['incident_type'] = $filters['incident_type'];
        }

        $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
        return [$whereSql, $params];
    }

    /* ---------------------------------------------------------------
       Detail
    --------------------------------------------------------------- */
    public function getIncidentOverview(int $incidentId): ?array
    {
        $stmt = $this->conn->prepare("
            SELECT
                i.incident_id,
                i.student_number,
                CONCAT(s.last_name, ', ', s.first_name) AS student_name,
                i.reported_by,
                CONCAT(e.first_name, ' ', e.last_name) AS reported_by_name,
                i.case_id,
                c.case_number,
                i.incident_type,
                i.severity,
                i.incident_date,
                i.location,
                i.description,
                i.action_taken,
                i.action_date,
                i.status,
                i.created_at
            FROM gd_incidents i
            JOIN rgr_students s ON s.student_number = i.student_number
            JOIN sms_employee e ON e.employee_id = i.reported_by
            LEFT JOIN gd_cases c ON c.case_id = i.case_id
            WHERE i.incident_id = :incident_id
        ");
        $stmt->execute(['incident_id' => $incidentId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /* ---------------------------------------------------------------
       Create / Edit
       NOTE: case_id is intentionally NEVER settable from here — it can
       only ever be written by Cases::createCaseFromIncident(), so every
       incident starts unlinked and stays that way until Cases links it.
    --------------------------------------------------------------- */
    public function createIncident(array $data): int
    {
        $stmt = $this->conn->prepare("
            INSERT INTO gd_incidents
                (student_number, reported_by, incident_type, severity, incident_date, location, description, status)
            VALUES
                (:student_number, :reported_by, :incident_type, :severity, :incident_date, :location, :description, 'Reported')
        ");
        $stmt->execute([
            'student_number' => $data['student_number'],
            'reported_by'    => $data['reported_by'],
            'incident_type'  => $data['incident_type'],
            'severity'       => $data['severity'] ?? 'Minor',
            'incident_date'  => $data['incident_date'],
            'location'       => $data['location'] ?? null,
            'description'    => $data['description'],
        ]);
        return (int) $this->conn->lastInsertId();
    }

    public function updateIncident(int $incidentId, array $data): bool
    {
        $stmt = $this->conn->prepare("
            UPDATE gd_incidents SET
                incident_type = :incident_type,
                severity = :severity,
                incident_date = :incident_date,
                location = :location,
                description = :description
            WHERE incident_id = :incident_id
        ");
        return $stmt->execute([
            'incident_type' => $data['incident_type'],
            'severity'      => $data['severity'],
            'incident_date' => $data['incident_date'],
            'location'      => $data['location'] ?? null,
            'description'   => $data['description'],
            'incident_id'   => $incidentId,
        ]);
    }

    /* ---------------------------------------------------------------
       Status / resolution
    --------------------------------------------------------------- */
    public function updateStatus(int $incidentId, string $status): bool
    {
        $stmt = $this->conn->prepare("UPDATE gd_incidents SET status = :status WHERE incident_id = :incident_id");
        return $stmt->execute(['status' => $status, 'incident_id' => $incidentId]);
    }

    public function recordResolution(int $incidentId, string $actionTaken, string $actionDate, string $status = 'Resolved'): bool
    {
        $stmt = $this->conn->prepare("
            UPDATE gd_incidents
            SET action_taken = :action_taken, action_date = :action_date, status = :status
            WHERE incident_id = :incident_id
        ");
        return $stmt->execute([
            'action_taken' => $actionTaken,
            'action_date'  => $actionDate,
            'status'       => $status,
            'incident_id'  => $incidentId,
        ]);
    }

    /* ---------------------------------------------------------------
       Filter helper: distinct incident types already in use
    --------------------------------------------------------------- */
    public function getIncidentTypes(): array
    {
        $stmt = $this->conn->prepare("SELECT DISTINCT incident_type FROM gd_incidents ORDER BY incident_type ASC");
        $stmt->execute();
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'incident_type');
    }
}
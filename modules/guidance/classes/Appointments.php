<?php
include_once __DIR__ . '/../../../database/db.php';
// NOTE: adjust this relative path if Appointments.php doesn't sit at the
// same folder depth as Students.php / Cases.php.

class Appointments
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
                a.appointment_id,
                a.student_number,
                CONCAT(s.last_name, ', ', s.first_name) AS student_name,
                a.counselor_id,
                CONCAT(e.first_name, ' ', e.last_name) AS counselor_name,
                a.case_id,
                a.appointment_date,
                a.purpose,
                a.meeting_type,
                a.status
            FROM gd_appointments a
            JOIN rgr_students s ON s.student_number = a.student_number
            JOIN sms_employee e ON e.employee_id = a.counselor_id
            {$whereSql}
            ORDER BY a.appointment_date ASC
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
            FROM gd_appointments a
            JOIN rgr_students s ON s.student_number = a.student_number
            JOIN sms_employee e ON e.employee_id = a.counselor_id
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
            $where[] = '(s.first_name LIKE :search OR s.last_name LIKE :search OR s.student_number LIKE :search)';
            $params['search'] = "%{$filters['search']}%";
        }
        if (!empty($filters['status'])) {
            $where[] = 'a.status = :status';
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['meeting_type'])) {
            $where[] = 'a.meeting_type = :meeting_type';
            $params['meeting_type'] = $filters['meeting_type'];
        }
        if (!empty($filters['counselor_id'])) {
            $where[] = 'a.counselor_id = :counselor_id';
            $params['counselor_id'] = $filters['counselor_id'];
        }
        // date_from / date_to define the daily or weekly window
        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $where[] = 'a.appointment_date BETWEEN :date_from AND :date_to';
            $params['date_from'] = $filters['date_from'];
            $params['date_to']   = $filters['date_to'];
        }

        $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
        return [$whereSql, $params];
    }

    /* ---------------------------------------------------------------
       Detail
    --------------------------------------------------------------- */
    public function getAppointmentOverview(int $appointmentId): ?array
    {
        $stmt = $this->conn->prepare("
            SELECT
                a.appointment_id,
                a.student_number,
                CONCAT(s.last_name, ', ', s.first_name) AS student_name,
                a.counselor_id,
                CONCAT(e.first_name, ' ', e.last_name) AS counselor_name,
                a.case_id,
                a.appointment_date,
                a.purpose,
                a.meeting_type,
                a.status,
                a.remarks,
                a.created_at
            FROM gd_appointments a
            JOIN rgr_students s ON s.student_number = a.student_number
            JOIN sms_employee e ON e.employee_id = a.counselor_id
            WHERE a.appointment_id = :appointment_id
        ");
        $stmt->execute(['appointment_id' => $appointmentId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /* ---------------------------------------------------------------
       Booking (counselor books on behalf of a student — walk-in style)
    --------------------------------------------------------------- */
    public function isDoubleBooked(int $counselorId, string $appointmentDate, ?int $excludeAppointmentId = null): bool
    {
        $sql = "
            SELECT COUNT(*) FROM gd_appointments
            WHERE counselor_id = :counselor_id
              AND appointment_date = :appointment_date
              AND status NOT IN ('Cancelled', 'No Show')
        ";
        $params = ['counselor_id' => $counselorId, 'appointment_date' => $appointmentDate];

        if ($excludeAppointmentId !== null) {
            $sql .= ' AND appointment_id != :exclude_id';
            $params['exclude_id'] = $excludeAppointmentId;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return ((int) $stmt->fetchColumn()) > 0;
    }

    /* ---------------------------------------------------------------
       Open/In Progress cases (across all students), for the "Case"
       picker in the booking modal. Selecting a case is the source of
       truth for student + counselor — those fields get auto-filled/
       locked from whichever case is picked, matching the same pattern
       used for incident selection in Cases. Closed cases are excluded
       since booking a new appointment against an already-closed case
       shouldn't happen silently.
    --------------------------------------------------------------- */
    public function getOpenCases(): array
    {
        $stmt = $this->conn->prepare("
            SELECT
                c.case_id,
                c.case_number,
                c.student_number,
                CONCAT(s.last_name, ', ', s.first_name) AS student_name,
                c.case_type,
                c.status,
                c.counselor_id,
                CONCAT(e.first_name, ' ', e.last_name) AS counselor_name
            FROM gd_cases c
            JOIN rgr_students s ON s.student_number = c.student_number
            JOIN sms_employee e ON e.employee_id = c.counselor_id
            WHERE c.status IN ('Open', 'In Progress')
            ORDER BY c.opened_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @throws RuntimeException if the slot is already booked
     */
    public function createAppointment(array $data): int
    {
        if ($this->isDoubleBooked((int) $data['counselor_id'], $data['appointment_date'])) {
            throw new RuntimeException('This counselor already has an appointment at that exact date/time.');
        }

        $stmt = $this->conn->prepare("
            INSERT INTO gd_appointments
                (student_number, counselor_id, case_id, appointment_date, purpose, meeting_type, status)
            VALUES
                (:student_number, :counselor_id, :case_id, :appointment_date, :purpose, :meeting_type, 'Pending')
        ");
        $stmt->execute([
            'student_number'   => $data['student_number'],
            'counselor_id'     => $data['counselor_id'],
            'case_id'          => $data['case_id'] ?: null,
            'appointment_date' => $data['appointment_date'],
            'purpose'          => $data['purpose'],
            'meeting_type'     => $data['meeting_type'] ?? 'Face-to-Face',
        ]);

        return (int) $this->conn->lastInsertId();
    }

    /* ---------------------------------------------------------------
       Status transitions
    --------------------------------------------------------------- */
    public function updateStatus(int $appointmentId, string $status, ?string $remarks = null): bool
    {
        $sql = "UPDATE gd_appointments SET status = :status";
        $params = ['status' => $status, 'appointment_id' => $appointmentId];

        if ($remarks !== null) {
            $sql .= ", remarks = :remarks";
            $params['remarks'] = $remarks;
        }
        $sql .= " WHERE appointment_id = :appointment_id";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * @throws RuntimeException if the new slot is already booked
     */
    public function reschedule(int $appointmentId, string $newDate): bool
    {
        $current = $this->getAppointmentOverview($appointmentId);
        if (!$current) {
            throw new RuntimeException('Appointment not found.');
        }

        if ($this->isDoubleBooked((int) $current['counselor_id'], $newDate, $appointmentId)) {
            throw new RuntimeException('This counselor already has an appointment at that exact date/time.');
        }

        $stmt = $this->conn->prepare("
            UPDATE gd_appointments SET appointment_date = :appointment_date WHERE appointment_id = :appointment_id
        ");
        return $stmt->execute(['appointment_date' => $newDate, 'appointment_id' => $appointmentId]);
    }

    public function addRemarks(int $appointmentId, string $remarks): bool
    {
        $stmt = $this->conn->prepare("UPDATE gd_appointments SET remarks = :remarks WHERE appointment_id = :appointment_id");
        return $stmt->execute(['remarks' => $remarks, 'appointment_id' => $appointmentId]);
    }

    /* ---------------------------------------------------------------
       Availability helpers
    --------------------------------------------------------------- */
    public function getCounselorSchedule(int $counselorId, string $dayOfWeek): array
    {
        $stmt = $this->conn->prepare("
            SELECT start_time, end_time, availability
            FROM gd_counselor_schedules
            WHERE counselor_id = :counselor_id AND day_of_week = :day_of_week
        ");
        $stmt->execute(['counselor_id' => $counselorId, 'day_of_week' => $dayOfWeek]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBookedTimes(int $counselorId, string $date): array
    {
        $stmt = $this->conn->prepare("
            SELECT appointment_date
            FROM gd_appointments
            WHERE counselor_id = :counselor_id
              AND DATE(appointment_date) = :date
              AND status NOT IN ('Cancelled', 'No Show')
            ORDER BY appointment_date ASC
        ");
        $stmt->execute(['counselor_id' => $counselorId, 'date' => $date]);
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'appointment_date');
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
}
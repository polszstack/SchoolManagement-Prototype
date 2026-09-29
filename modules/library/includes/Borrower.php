<?php
/**
 * Borrower
 * lbr_borrowers table — with rgr_students lookup integration.
 */
class Borrower
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ------------------------------------------------------------------
    // List borrowers (with live loan counts)
    // ------------------------------------------------------------------
    public function list(
        string $search = '',
        string $type   = '',
        int    $page   = 1,
        int    $limit  = 10
    ): array {
        $where  = ['1=1'];
        $params = [];

        if ($search) {
            $where[] = '(br.name LIKE ? OR br.borrower_id LIKE ? OR br.email LIKE ?)';
            $s       = "%{$search}%";
            $params  = array_merge($params, [$s, $s, $s]);
        }
        if ($type) { $where[] = 'br.type = ?'; $params[] = $type; }

        $whereStr = implode(' AND ', $where);
        $offset   = ($page - 1) * $limit;

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM lbr_borrowers br WHERE {$whereStr}");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        $stmt = $this->db->prepare("
            SELECT
                br.*,
                COUNT(t.id)                                              AS total_borrowed,
                SUM(CASE WHEN t.status = 'active'  THEN 1 ELSE 0 END)  AS currently_borrowed,
                SUM(CASE WHEN t.status = 'overdue' THEN 1 ELSE 0 END)  AS overdue_count
            FROM lbr_borrowers br
            LEFT JOIN lbr_transactions t ON t.borrower_id = br.id
            WHERE {$whereStr}
            GROUP BY br.id
            ORDER BY br.id DESC
            LIMIT {$limit} OFFSET {$offset}
        ");
        $stmt->execute($params);

        return [
            'borrowers' => $stmt->fetchAll(),
            'total'     => $total,
            'pages'     => (int)ceil($total / $limit),
            'page'      => $page,
        ];
    }

    // ------------------------------------------------------------------
    // Find single borrower by PK
    // ------------------------------------------------------------------
    public function find(int $id): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM lbr_borrowers WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // ------------------------------------------------------------------
    // Check duplicate borrower_id
    // ------------------------------------------------------------------
    public function idExists(string $borrowerId): bool
    {
        $stmt = $this->db->prepare('SELECT id FROM lbr_borrowers WHERE borrower_id = ?');
        $stmt->execute([$borrowerId]);
        return (bool)$stmt->fetch();
    }

    // ------------------------------------------------------------------
    // Search rgr_students — for the "import student" lookup
    // Returns students NOT yet registered as library borrowers
    // ------------------------------------------------------------------
    public function searchStudents(string $search = '', int $limit = 20): array
    {
        $params = [];
        $where  = ['1=1'];

        if ($search) {
            $where[] = "(
                CONCAT(s.first_name,' ',s.last_name) LIKE ?
                OR CONCAT(s.first_name,' ',s.middle_name,' ',s.last_name) LIKE ?
                OR s.student_number LIKE ?
                OR s.email LIKE ?
                OR s.course LIKE ?
            )";
            $s = "%{$search}%";
            $params = array_merge($params, [$s, $s, $s, $s, $s]);
        }

        // Only show students not already in lbr_borrowers
        $where[] = "s.student_number NOT IN (
            SELECT CAST(br.borrower_id AS UNSIGNED)
            FROM lbr_borrowers br
            WHERE br.borrower_id REGEXP '^[0-9]+$'
        )";

        $whereStr = implode(' AND ', $where);
        $params[] = $limit;

        $stmt = $this->db->prepare("
            SELECT
                s.student_number,
                s.first_name,
                s.middle_name,
                s.last_name,
                s.course,
                s.year_level,
                s.section,
                s.email,
                s.phone,
                s.address,
                s.academic_status,
                CONCAT(s.first_name, ' ',
                    CASE WHEN s.middle_name IS NOT NULL AND s.middle_name != ''
                         THEN CONCAT(s.middle_name, ' ') ELSE '' END,
                    s.last_name) AS full_name
            FROM rgr_students s
            WHERE {$whereStr}
            ORDER BY s.last_name, s.first_name
            LIMIT ?
        ");
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ------------------------------------------------------------------
    // Import a student from rgr_students into lbr_borrowers
    // ------------------------------------------------------------------
    public function importFromStudent(int $studentNumber): array|false
    {
        // Fetch student
        $stmt = $this->db->prepare("
            SELECT * FROM rgr_students WHERE student_number = ?
        ");
        $stmt->execute([$studentNumber]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$student) return false;

        // Check if already imported
        $check = $this->db->prepare("SELECT id FROM lbr_borrowers WHERE borrower_id = ?");
        $check->execute([(string)$studentNumber]);
        if ($check->fetch()) return false;

        $fullName = trim($student['first_name']
            . ($student['middle_name'] ? ' ' . $student['middle_name'] : '')
            . ' ' . $student['last_name']);

        $grade = $student['course']
            ? ($student['course'] . ($student['year_level'] ? ' - Year ' . $student['year_level'] : ''))
            : null;

        $insertStmt = $this->db->prepare("
            INSERT INTO lbr_borrowers
                (name, borrower_id, email, phone, type, grade, address, active)
            VALUES (?, ?, ?, ?, 'Student', ?, ?, 1)
        ");
        $insertStmt->execute([
            $fullName,
            (string)$studentNumber,
            $student['email']   ?: null,
            $student['phone']   ?: null,
            $grade,
            $student['address'] ?: null,
        ]);

        return [
            'id'          => (int)$this->db->lastInsertId(),
            'name'        => $fullName,
            'borrower_id' => (string)$studentNumber,
        ];
    }

    // ------------------------------------------------------------------
    // Mutations
    // ------------------------------------------------------------------
    public function create(
        string  $name,
        string  $borrowerId,
        ?string $email   = null,
        ?string $phone   = null,
        string  $type    = 'Student',
        ?string $grade   = null,
        ?string $address = null
    ): int {
        $stmt = $this->db->prepare(
            'INSERT INTO lbr_borrowers (name, borrower_id, email, phone, type, grade, address)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$name, $borrowerId, $email, $phone, $type, $grade, $address]);
        return (int)$this->db->lastInsertId();
    }

    public function update(
        int     $id,
        string  $name,
        ?string $email   = null,
        ?string $phone   = null,
        string  $type    = 'Student',
        ?string $grade   = null,
        ?string $address = null
    ): void {
        $this->db->prepare(
            'UPDATE lbr_borrowers
             SET name=?, email=?, phone=?, type=?, grade=?, address=?
             WHERE id=?'
        )->execute([$name, $email, $phone, $type, $grade, $address, $id]);
    }

    public function delete(int $id): void
    {
        $this->db->prepare('DELETE FROM lbr_borrowers WHERE id = ?')->execute([$id]);
    }
}

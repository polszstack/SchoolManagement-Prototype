<?php
/**
 * ActivityLog
 * Reads/writes lbr_activity_log AND reads rgr_activity_log.
 */
class ActivityLog
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // Insert into lbr_activity_log
    public function log(string $action, string $details, string $user = 'Librarian'): void
    {
        try {
            $this->db->prepare(
                'INSERT INTO lbr_activity_log (action, details, user) VALUES (:action, :details, :user)'
            )->execute([':action' => $action, ':details' => $details, ':user' => $user]);
        } catch (PDOException) {
            // Non-fatal
        }
    }

    // Recent lbr_activity_log entries
    public function recent(int $limit = 20): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM lbr_activity_log ORDER BY created_at DESC LIMIT :limit'
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Recent rgr_activity_log entries
    public function recentRegistrar(int $limit = 20): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT
                    r.id,
                    r.action,
                    r.description   AS details,
                    r.ip_address,
                    r.created_at,
                    CONCAT(e.first_name, ' ', e.last_name) AS user
                FROM rgr_activity_log r
                LEFT JOIN user_account  ua ON ua.user_id    = r.user_id
                LEFT JOIN sms_employee  e  ON e.employee_id = ua.employee_id
                ORDER BY r.created_at DESC
                LIMIT :limit
            ");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException) {
            return [];
        }
    }

    // Combined feed — merges both logs, sorted by date
    public function recentCombined(int $limit = 20): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT action, details, user, created_at, 'library' AS source
                FROM lbr_activity_log

                UNION ALL

                SELECT
                    r.action,
                    r.description AS details,
                    COALESCE(CONCAT(e.first_name, ' ', e.last_name), 'System') AS user,
                    r.created_at,
                    'registrar' AS source
                FROM rgr_activity_log r
                LEFT JOIN user_account ua ON ua.user_id    = r.user_id
                LEFT JOIN sms_employee e  ON e.employee_id = ua.employee_id

                ORDER BY created_at DESC
                LIMIT :limit
            ");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException) {
            return $this->recent($limit); // fallback
        }
    }
}

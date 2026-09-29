<?php
/**
 * Settings
 * Reads and writes the lbr_settings table.
 */
class Settings
{
    private PDO $db;

    private static array $allowed = [
        'library_name',
        'max_borrow_days',
        'max_books_per_member',
        'daily_fine_rate',
        'auto_save',
    ];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /** Return all settings as key => value. */
    public function all(): array
    {
        try {
            $rows = $this->db->query('SELECT setting_key, setting_value FROM lbr_settings')->fetchAll();
            $out  = [];
            foreach ($rows as $row) {
                $out[$row['setting_key']] = $row['setting_value'];
            }
            return $out;
        } catch (PDOException) {
            return [];
        }
    }

    /** Fetch a single setting value. */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->all()[$key] ?? $default;
    }

    /** Persist an associative array of settings. */
    public function save(array $input): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO lbr_settings (setting_key, setting_value)
             VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?'
        );
        foreach (self::$allowed as $key) {
            if (isset($input[$key])) {
                $val = trim($input[$key]);
                $stmt->execute([$key, $val, $val]);
            }
        }
    }

    /** Mark transactions overdue and recalculate fines. Non-fatal. */
    public function updateOverdueStatus(): void
    {
        try {
            $fineRate = (float)($this->get('daily_fine_rate') ?? 0.50);
            $this->db->prepare("
                UPDATE lbr_transactions
                SET
                    status = 'overdue',
                    fine   = DATEDIFF(CURDATE(), due_date) * :rate
                WHERE
                    status      != 'returned'
                    AND return_date IS NULL
                    AND due_date  <  CURDATE()
            ")->execute([':rate' => $fineRate]);
        } catch (PDOException) {
            // Non-fatal
        }
    }
}

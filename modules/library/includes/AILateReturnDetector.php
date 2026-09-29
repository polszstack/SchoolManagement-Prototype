<?php
// ============================================================
// includes/AILateReturnDetector.php
// ============================================================

class AILateReturnDetector
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function runDailyCheck(): array
    {
        $results = [];
        $activeTransactions = $this->getActiveTransactions();

        foreach ($activeTransactions as $txn) {
            $score        = $this->computeRiskScore($txn);
            $daysUntilDue = (int) $txn['days_until_due'];
            $stage        = $this->getReminderStage($daysUntilDue, $txn['status']);

            if ($stage && !$this->reminderAlreadySent($txn['id'], $stage)) {
                $threshold = (float) $this->getSetting('ai_reminder_threshold', 60);

                if ($score >= $threshold || $txn['status'] === 'overdue') {
                    $sent = $this->sendReminder($txn, $score, $stage);
                    $results[] = [
                        'borrower'   => $txn['borrower_name'],
                        'book'       => $txn['book_title'],
                        'risk_score' => $score,
                        'stage'      => $stage,
                        'sent'       => $sent,
                    ];
                }
            }
        }

        return $results;
    }

    public function getAtRiskTransactions(): array
    {
        $transactions = $this->getActiveTransactions();
        $list = [];

        foreach ($transactions as $txn) {
            $score  = $this->computeRiskScore($txn);
            $list[] = array_merge($txn, ['risk_score' => $score]);
        }

        usort($list, fn($a, $b) => $b['risk_score'] <=> $a['risk_score']);
        return $list;
    }

    public function computeRiskScore(array $txn): float
    {
        $score      = 0.0;
        $borrowerId = (int) $txn['borrower_id'];

        $stmt = $this->db->prepare("
            SELECT
                COUNT(*) AS total,
                SUM(was_late) AS late_count,
                AVG(CASE WHEN was_late=1 THEN days_late ELSE 0 END) AS avg_days_late
            FROM lbr_borrow_history
            WHERE borrower_id = ?
        ");
        $stmt->execute([$borrowerId]);
        $history = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($history['total'] > 0) {
            $lateRate  = $history['late_count'] / $history['total'];
            $score    += $lateRate * 40;
            $avgLate   = (float) ($history['avg_days_late'] ?? 0);
            $score    += min($avgLate / 14 * 20, 20);
        } else {
            $score += 15;
        }

        $daysUntilDue = (int) $txn['days_until_due'];
        if ($daysUntilDue < 0)      $score += 25;
        elseif ($daysUntilDue === 0) $score += 20;
        elseif ($daysUntilDue <= 1)  $score += 18;
        elseif ($daysUntilDue <= 3)  $score += 12;
        elseif ($daysUntilDue <= 7)  $score += 5;

        $genre = $txn['genre'] ?? 'General';
        $stmt2 = $this->db->prepare("
            SELECT COUNT(*) AS total, SUM(was_late) AS late_count
            FROM lbr_borrow_history
            WHERE genre = ?
        ");
        $stmt2->execute([$genre]);
        $genreStats = $stmt2->fetch(PDO::FETCH_ASSOC);

        if ($genreStats['total'] > 5) {
            $score += ($genreStats['late_count'] / $genreStats['total']) * 10;
        }

        if (strtolower($txn['borrower_type'] ?? '') === 'student') {
            $score += 3;
        }

        return min(round($score, 2), 100.0);
    }

    private function getReminderStage(int $daysUntilDue, string $status): ?string
    {
        if ($status === 'overdue') return 'overdue';
        if ($daysUntilDue === 1)  return '1_day';
        if ($daysUntilDue === 3)  return '3_day';
        return null;
    }

    private function reminderAlreadySent(int $transactionId, string $stage): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM lbr_reminders
            WHERE transaction_id = ?
              AND reminder_stage = ?
              AND sent_at >= CURDATE()
        ");
        $stmt->execute([$transactionId, $stage]);
        return (int) $stmt->fetchColumn() > 0;
    }

    private function sendReminder(array $txn, float $riskScore, string $stage): bool
    {
        $smsEnabled   = $this->getSetting('ai_sms_enabled', 'false') === 'true';
        $emailEnabled = $this->getSetting('ai_email_enabled', 'true') === 'true';
        $type         = ($smsEnabled && $emailEnabled) ? 'both' : ($smsEnabled ? 'sms' : 'email');
        $message      = $this->buildMessage($txn, $stage, $riskScore);

        $stmt = $this->db->prepare("
            INSERT INTO lbr_reminders
                (transaction_id, borrower_id, reminder_type, reminder_stage, risk_score, status, message_preview)
            VALUES (?, ?, ?, ?, ?, 'sent', ?)
        ");
        $stmt->execute([
            $txn['id'],
            $txn['borrower_id'],
            $type,
            $stage,
            $riskScore,
            substr($message, 0, 500),
        ]);

        $logStmt = $this->db->prepare("
            INSERT INTO lbr_activity_log (action, details, user)
            VALUES ('AI Reminder Sent', ?, 'AI System')
        ");
        $logStmt->execute(["Sent {$stage} reminder to {$txn['borrower_name']} for '{$txn['book_title']}' (Risk: {$riskScore}%)"]);

        return true;
    }

    private function buildMessage(array $txn, string $stage, float $riskScore): string
    {
        $name        = $txn['borrower_name'];
        $book        = $txn['book_title'];
        $dueDate     = $txn['due_date'];
        $daysLeft    = (int) $txn['days_until_due'];
        $libraryName = $this->getSetting('library_name', 'School Library');

        switch ($stage) {
            case '3_day':
                return "Hi {$name}! 📚 Friendly reminder from {$libraryName}: '{$book}' is due in 3 days ({$dueDate}). Please return it on time to avoid a fine. Thank you!";
            case '1_day':
                return "⚠️ Hi {$name}! '{$book}' is due TOMORROW ({$dueDate}). Please return it to {$libraryName} to avoid fines.";
            case 'overdue':
                $daysLate = abs($daysLeft);
                $fine     = round($daysLate * 0.50, 2);
                return "🚨 OVERDUE NOTICE — Hi {$name}, '{$book}' was due on {$dueDate} ({$daysLate} day(s) ago). Fine: \${$fine}. Please return it to {$libraryName} immediately.";
            default:
                return "Reminder from {$libraryName}: Please return '{$book}' by {$dueDate}.";
        }
    }

    private function getActiveTransactions(): array
    {
        $stmt = $this->db->query("
            SELECT
                t.id,
                t.borrower_id,
                t.book_id,
                t.borrow_date,
                t.due_date,
                t.status,
                DATEDIFF(t.due_date, CURDATE()) AS days_until_due,
                b.title   AS book_title,
                b.genre,
                br.name   AS borrower_name,
                br.email  AS borrower_email,
                br.phone  AS borrower_phone,
                br.type   AS borrower_type
            FROM lbr_transactions t
            JOIN lbr_books     b  ON b.id  = t.book_id
            JOIN lbr_borrowers br ON br.id = t.borrower_id
            WHERE t.status IN ('active', 'overdue')
            ORDER BY t.due_date ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecentReminders(int $limit = 10): array
    {
        $stmt = $this->db->prepare("
            SELECT
                r.*,
                br.name  AS borrower_name,
                b.title  AS book_title
            FROM lbr_reminders r
            JOIN lbr_borrowers br ON br.id = r.borrower_id
            JOIN lbr_transactions t ON t.id = r.transaction_id
            JOIN lbr_books b ON b.id = t.book_id
            ORDER BY r.sent_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getSetting(string $key, string $default = ''): string
    {
        $stmt = $this->db->prepare("SELECT setting_value FROM lbr_settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $val = $stmt->fetchColumn();
        return $val !== false ? (string) $val : $default;
    }
}
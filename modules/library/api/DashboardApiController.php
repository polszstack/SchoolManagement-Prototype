<?php
/**
 * DashboardApiController
 * Handles dashboard stats and reporting actions:
 *   get_stats | get_report | get_activity
 */
class DashboardApiController
{
    private PDO         $db;
    private ActivityLog $log;

    public function __construct()
    {
        $this->db  = Database::getInstance();
        $this->log = new ActivityLog();
    }

    // ------------------------------------------------------------------
    // Dispatch
    // ------------------------------------------------------------------

    public function handle(string $action, array $input): never
    {
        match ($action) {
            'get_stats'              => $this->getStats(),
            'get_report'             => $this->getReport(),
            'get_activity'           => $this->getActivity(),
            'get_registrar_activity' => $this->getRegistrarActivity(),
            default                  => Response::error("Unknown dashboard action: {$action}"),
        };
    }

    // ------------------------------------------------------------------
    // Actions
    // ------------------------------------------------------------------

    private function getStats(): never
    {
        $totalBooks     = (int)$this->db->query("SELECT COUNT(*) FROM lbr_books")->fetchColumn();
        $availableBooks = (int)$this->db->query("SELECT COUNT(*) FROM lbr_books WHERE status = 'available'")->fetchColumn();
        $borrowedBooks  = (int)$this->db->query("SELECT COUNT(*) FROM lbr_books WHERE status = 'borrowed'")->fetchColumn();
        $totalMembers   = (int)$this->db->query("SELECT COUNT(*) FROM lbr_borrowers WHERE active = 1")->fetchColumn();
        $overdueBooks   = (int)$this->db->query("SELECT COUNT(*) FROM lbr_transactions WHERE status = 'overdue'")->fetchColumn();
        $totalFines     = $this->db->query("SELECT COALESCE(SUM(fine),0) FROM lbr_transactions WHERE status = 'overdue'")->fetchColumn();

        // Inventory health: books by condition
        $inventoryHealth = $this->db->query(
            "SELECT `condition`, COUNT(*) AS count FROM lbr_books GROUP BY `condition` ORDER BY FIELD(`condition`,'Excellent','Good','Fair','Poor')"
        )->fetchAll();

        // Due today
        $dueTodayCount = (int)$this->db->query(
            "SELECT COUNT(*) FROM lbr_transactions WHERE status='active' AND due_date = CURDATE()"
        )->fetchColumn();

        // Due within 3 days
        $dueSoonCount = (int)$this->db->query(
            "SELECT COUNT(*) FROM lbr_transactions WHERE status='active' AND due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 3 DAY)"
        )->fetchColumn();

        // Active borrowed books with borrower details (for tracking panel)
        $activeBorrowings = $this->db->query("
            SELECT
                t.id AS transaction_id,
                t.borrow_date,
                t.due_date,
                t.status,
                DATEDIFF(t.due_date, CURDATE()) AS days_until_due,
                b.title  AS book_title,
                b.genre,
                b.cover_url,
                b.cover_local,
                b.id     AS book_id,
                br.name  AS borrower_name,
                br.borrower_id AS member_id,
                br.type  AS borrower_type
            FROM lbr_transactions t
            JOIN lbr_books     b  ON b.id  = t.book_id
            JOIN lbr_borrowers br ON br.id = t.borrower_id
            WHERE t.status IN ('active','overdue')
            ORDER BY t.due_date ASC
            LIMIT 10
        ")->fetchAll();

        $recentBooks = $this->db->query(
            "SELECT id, title, author, genre, status, cover_url, cover_local, added_date
             FROM lbr_books ORDER BY added_date DESC, id DESC LIMIT 4"
        )->fetchAll();

        $recentActivity = $this->log->recentCombined(10);

        // Genre inventory breakdown for pie/bar
        $genreInventory = $this->db->query(
            "SELECT genre, COUNT(*) AS total,
                SUM(CASE WHEN status='available' THEN 1 ELSE 0 END) AS available,
                SUM(CASE WHEN status='borrowed'  THEN 1 ELSE 0 END) AS borrowed
             FROM lbr_books GROUP BY genre ORDER BY total DESC LIMIT 8"
        )->fetchAll();

        Response::json([
            'total_books'      => $totalBooks,
            'available_books'  => $availableBooks,
            'borrowed_books'   => $borrowedBooks,
            'total_members'    => $totalMembers,
            'overdue_books'    => $overdueBooks,
            'total_fines'      => number_format((float)$totalFines, 2),
            'due_today'        => $dueTodayCount,
            'due_soon'         => $dueSoonCount,
            'inventory_health' => $inventoryHealth,
            'active_borrowings'=> $activeBorrowings,
            'genre_inventory'  => $genreInventory,
            'recent_books'     => $recentBooks,
            'recent_activity'  => $recentActivity,
        ]);
    }

    private function getReport(): never
    {
        $stats = [
            'total_books'   => (int)$this->db->query("SELECT COUNT(*) FROM lbr_books")->fetchColumn(),
            'available'     => (int)$this->db->query("SELECT COUNT(*) FROM lbr_books WHERE status='available'")->fetchColumn(),
            'borrowed'      => (int)$this->db->query("SELECT COUNT(*) FROM lbr_books WHERE status='borrowed'")->fetchColumn(),
            'overdue'       => (int)$this->db->query("SELECT COUNT(*) FROM lbr_transactions WHERE status='overdue'")->fetchColumn(),
            'total_members' => (int)$this->db->query("SELECT COUNT(*) FROM lbr_borrowers")->fetchColumn(),
            'total_fines'   => number_format(
                (float)$this->db->query("SELECT COALESCE(SUM(fine),0) FROM lbr_transactions WHERE status='overdue'")->fetchColumn(),
                2
            ),
        ];

        $genreBreakdown = $this->db->query(
            "SELECT genre, COUNT(*) AS count FROM lbr_books GROUP BY genre ORDER BY count DESC"
        )->fetchAll();

        $memberBreakdown = $this->db->query(
            "SELECT type, COUNT(*) AS count FROM lbr_borrowers GROUP BY type ORDER BY count DESC"
        )->fetchAll();

        $overdueList = $this->db->query("
            SELECT t.due_date, t.fine, b.title AS book_title, br.name AS borrower_name
            FROM lbr_transactions t
            JOIN lbr_books     b  ON b.id  = t.book_id
            JOIN lbr_borrowers br ON br.id = t.borrower_id
            WHERE t.status = 'overdue'
            ORDER BY t.due_date ASC
        ")->fetchAll();

        $topBorrowed = $this->db->query("
            SELECT b.title, b.author, COUNT(t.id) AS borrow_count
            FROM lbr_transactions t
            JOIN lbr_books b ON b.id = t.book_id
            GROUP BY b.id
            ORDER BY borrow_count DESC
            LIMIT 5
        ")->fetchAll();

        Response::json(compact('stats', 'genreBreakdown', 'memberBreakdown', 'overdueList', 'topBorrowed'));
    }

    private function getActivity(): never
    {
        $limit    = (int)($_GET['limit'] ?? 20);
        $activity = $this->log->recentCombined($limit);
        Response::json(['activity' => $activity]);
    }

    private function getRegistrarActivity(): never
    {
        $limit    = (int)($_GET['limit'] ?? 20);
        $activity = $this->log->recentRegistrar($limit);
        Response::json(['activity' => $activity]);
    }
}

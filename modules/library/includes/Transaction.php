<?php
/**
 * Transaction
 * Encapsulates all database operations for the lbr_transactions table.
 */
class Transaction
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ------------------------------------------------------------------
    // Queries
    // ------------------------------------------------------------------

    /**
     * Paginated list of transactions with joined book/borrower info.
     *
     * @return array{transactions: array, total: int, pages: int, page: int}
     */
    public function list(
        string $status = '',
        int    $page   = 1,
        int    $limit  = 10,
        string $search = ''
    ): array {
        $where  = ['1=1'];
        $params = [];

        if ($status) { $where[] = 't.status = ?'; $params[] = $status; }
        if ($search) {
            $where[] = '(b.title LIKE ? OR br.name LIKE ? OR br.borrower_id LIKE ?)';
            $s = "%{$search}%";
            $params = array_merge($params, [$s, $s, $s]);
        }

        $whereStr = implode(' AND ', $where);
        $offset   = ($page - 1) * $limit;

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM lbr_transactions t WHERE {$whereStr}");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        $stmt = $this->db->prepare("
            SELECT t.*,
                   b.title      AS book_title,
                   b.author     AS book_author,
                   b.cover_url,
                   b.cover_local,
                   br.name      AS borrower_name,
                   br.borrower_id AS member_id,
                   br.type      AS member_type
            FROM lbr_transactions t
            JOIN lbr_books       b  ON b.id  = t.book_id
            JOIN lbr_borrowers   br ON br.id = t.borrower_id
            WHERE {$whereStr}
            ORDER BY t.created_at DESC
            LIMIT {$limit} OFFSET {$offset}
        ");
        $stmt->execute($params);

        return [
            'transactions' => $stmt->fetchAll(),
            'total'        => $total,
            'pages'        => (int)ceil($total / $limit),
            'page'         => $page,
        ];
    }

    /** Find a single transaction (with book title) by PK. */
    public function find(int $id): array|false
    {
        $stmt = $this->db->prepare("
            SELECT t.*, b.title
            FROM lbr_transactions t
            JOIN lbr_books b ON b.id = t.book_id
            WHERE t.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /** Count active/overdue loans for a borrower. */
    public function activeLoanCount(int $borrowerId): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM lbr_transactions
             WHERE borrower_id = ? AND status IN ('active','overdue')"
        );
        $stmt->execute([$borrowerId]);
        return (int)$stmt->fetchColumn();
    }

    /** Get borrowing history for a book. */
    public function historyForBook(int $bookId, int $limit = 10): array
    {
        $stmt = $this->db->prepare("
            SELECT t.*, br.name AS borrower_name, br.borrower_id AS member_id
            FROM lbr_transactions t
            JOIN lbr_borrowers br ON br.id = t.borrower_id
            WHERE t.book_id = ?
            ORDER BY t.borrow_date DESC
            LIMIT {$limit}
        ");
        $stmt->execute([$bookId]);
        return $stmt->fetchAll();
    }

    /** Get borrowing history for a borrower. */
    public function historyForBorrower(int $borrowerId): array
    {
        $stmt = $this->db->prepare("
            SELECT t.*, b.title AS book_title, b.author AS book_author
            FROM lbr_transactions t
            JOIN lbr_books b ON b.id = t.book_id
            WHERE t.borrower_id = ?
            ORDER BY t.borrow_date DESC
        ");
        $stmt->execute([$borrowerId]);
        return $stmt->fetchAll();
    }

    // ------------------------------------------------------------------
    // Mutations
    // ------------------------------------------------------------------

    /** Open a new borrow transaction. Returns the new PK. */
    public function create(
        int    $bookId,
        int    $borrowerId,
        string $borrowDate,
        string $dueDate,
        string $notes = ''
    ): int {
        $stmt = $this->db->prepare(
            'INSERT INTO lbr_transactions (book_id, borrower_id, borrow_date, due_date, notes)
             VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([$bookId, $borrowerId, $borrowDate, $dueDate, $notes]);
        return (int)$this->db->lastInsertId();
    }

    /** Mark a transaction as returned. */
    public function markReturned(int $id, string $returnDate, string $condition, float $fine): void
    {
        $this->db->prepare(
            "UPDATE lbr_transactions
             SET status='returned', return_date=?, `condition`=?, fine=?
             WHERE id=?"
        )->execute([$returnDate, $condition, $fine, $id]);
    }
}

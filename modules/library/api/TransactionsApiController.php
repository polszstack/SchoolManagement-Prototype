<?php
/**
 * TransactionsApiController
 * Handles all transaction-related API actions:
 *   get_transactions | borrow_book | return_book
 */
class TransactionsApiController
{
    private Transaction $transaction;
    private Book        $book;
    private Borrower    $borrower;
    private Settings    $settings;
    private ActivityLog $log;

    public function __construct()
    {
        $this->transaction = new Transaction();
        $this->book        = new Book();
        $this->borrower    = new Borrower();
        $this->settings    = new Settings();
        $this->log         = new ActivityLog();
    }

    // ------------------------------------------------------------------
    // Dispatch
    // ------------------------------------------------------------------

    public function handle(string $action, array $input): never
    {
        match ($action) {
            'get_transactions' => $this->getTransactions(),
            'borrow_book'      => $this->borrowBook($input),
            'return_book'      => $this->returnBook($input),
            default            => Response::error("Unknown transactions action: {$action}"),
        };
    }

    // ------------------------------------------------------------------
    // Actions
    // ------------------------------------------------------------------

    private function getTransactions(): never
    {
        $status = $_GET['status'] ?? '';
        $search = $_GET['search'] ?? '';
        $page   = max(1, (int)($_GET['page']  ?? 1));
        $limit  = (int)($_GET['limit'] ?? 10);

        Response::json($this->transaction->list($status, $page, $limit, $search));
    }

    private function borrowBook(array $input): never
    {
        $bookId     = (int)($input['book_id']     ?? 0);
        $borrowerId = (int)($input['borrower_id'] ?? 0);
        $borrowDays = (int)($input['borrow_days'] ?? 14);
        $notes      = trim($input['notes']        ?? '');

        if (!$bookId || !$borrowerId) {
            Response::error('Book and Member are required.');
        }

        // Validate book
        $book = $this->book->find($bookId);
        if (!$book) Response::error('Book not found.', 404);
        if ($book['status'] !== 'available') {
            Response::error('Book is not available for borrowing.');
        }

        // Validate borrower
        $borrowerRow = $this->borrower->find($borrowerId);
        if (!$borrowerRow) Response::error('Member not found.', 404);

        // Enforce borrow limit
        $maxBooks = (int)($this->settings->get('max_books_per_member') ?? 3);
        if ($this->transaction->activeLoanCount($borrowerId) >= $maxBooks) {
            Response::error("Member has reached the maximum borrow limit ({$maxBooks} books).");
        }

        $borrowDate = date('Y-m-d');
        $dueDate    = date('Y-m-d', strtotime("+{$borrowDays} days"));

        $this->transaction->create($bookId, $borrowerId, $borrowDate, $dueDate, $notes);
        $this->book->setStatus($bookId, 'borrowed');

        $this->log->log(
            'Book Borrowed',
            "\"{$book['title']}\" borrowed by {$borrowerRow['name']} (due {$dueDate})"
        );

        Response::success("Book borrowed successfully! Due: {$dueDate}");
    }

    private function returnBook(array $input): never
    {
        $transId   = (int)($input['transaction_id'] ?? 0);
        $condition = trim($input['condition']        ?? 'Good');
        $fine      = (float)($input['fine']          ?? 0);

        if (!$transId) Response::error('Transaction ID required.');

        $trans = $this->transaction->find($transId);
        if (!$trans)                            Response::error('Transaction not found.', 404);
        if ($trans['status'] === 'returned')    Response::error('Book already returned.');

        $returnDate = date('Y-m-d');
        $this->transaction->markReturned($transId, $returnDate, $condition, $fine);
        $this->book->setStatus($trans['book_id'], 'available', $condition);

        $fineMsg = $fine > 0 ? " (Fine: ₱{$fine})" : '';
        $this->log->log('Book Returned', "\"{$trans['title']}\" returned{$fineMsg}");

        $successMsg = 'Book returned successfully!' . ($fine > 0 ? " Fine collected: ₱{$fine}" : '');
        Response::success($successMsg);
    }
}

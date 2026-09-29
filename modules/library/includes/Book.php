<?php
/**
 * Book
 * Encapsulates all database operations for the lbr_books table.
 */
class Book
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
     * Paginated, filterable list of books.
     *
     * @return array{books: array, total: int, pages: int, page: int}
     */
    public function list(
        string $search = '',
        string $genre  = '',
        string $status = '',
        int    $page   = 1,
        int    $limit  = 10
    ): array {
        $where  = ['1=1'];
        $params = [];

        if ($search) {
            $where[]  = '(b.title LIKE ? OR b.author LIKE ? OR b.isbn LIKE ? OR b.genre LIKE ?)';
            $s        = "%{$search}%";
            $params   = array_merge($params, [$s, $s, $s, $s]);
        }
        if ($genre)  { $where[] = 'b.genre = ?';  $params[] = $genre; }
        if ($status) { $where[] = 'b.status = ?'; $params[] = $status; }

        $whereStr = implode(' AND ', $where);
        $offset   = ($page - 1) * $limit;

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM lbr_books b WHERE {$whereStr}");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        $stmt = $this->db->prepare(
            "SELECT * FROM lbr_books b WHERE {$whereStr} ORDER BY b.id DESC LIMIT {$limit} OFFSET {$offset}"
        );
        $stmt->execute($params);

        return [
            'books' => $stmt->fetchAll(),
            'total' => $total,
            'pages' => (int)ceil($total / $limit),
            'page'  => $page,
        ];
    }

    /** Find a single book by its PK. Returns false if not found. */
    public function find(int $id): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM lbr_books WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /** Return distinct genres for dropdowns. */
    public function genres(): array
    {
        return $this->db->query('SELECT DISTINCT genre FROM lbr_books ORDER BY genre')
                        ->fetchAll(PDO::FETCH_COLUMN);
    }

    // ------------------------------------------------------------------
    // Mutations
    // ------------------------------------------------------------------

    /** Insert a new book. Returns the new PK. */
    public function create(
        string  $title,
        string  $author,
        string  $genre       = 'General',
        ?int    $year        = null,
        ?string $isbn        = null,
        string  $description = '',
        ?string $coverUrl    = null
    ): int {
        if ($isbn && !$coverUrl) {
            $coverUrl = "https://covers.openlibrary.org/b/isbn/{$isbn}-L.jpg";
        }

        $stmt = $this->db->prepare(
            'INSERT INTO lbr_books (title, author, genre, year, isbn, description, cover_url)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$title, $author, $genre, $year, $isbn, $description, $coverUrl]);
        return (int)$this->db->lastInsertId();
    }

    /** Update an existing book. */
    public function update(
        int     $id,
        string  $title,
        string  $author,
        string  $genre       = 'General',
        ?int    $year        = null,
        ?string $isbn        = null,
        string  $description = '',
        string  $condition   = 'Good',
        ?string $coverUrl    = null
    ): void {
        if ($isbn && !$coverUrl) {
            $coverUrl = "https://covers.openlibrary.org/b/isbn/{$isbn}-L.jpg";
        }

        $this->db->prepare(
            'UPDATE lbr_books
             SET title=?, author=?, genre=?, year=?, isbn=?, description=?, `condition`=?, cover_url=?
             WHERE id=?'
        )->execute([$title, $author, $genre, $year, $isbn, $description, $condition, $coverUrl, $id]);
    }

    /** Delete a book by PK. */
    public function delete(int $id): void
    {
        $this->db->prepare('DELETE FROM lbr_books WHERE id = ?')->execute([$id]);
    }

    /** Set the status of a book (available / borrowed / lost / damaged). */
    public function setStatus(int $id, string $status, ?string $condition = null): void
    {
        if ($condition !== null) {
            $this->db->prepare('UPDATE lbr_books SET status=?, `condition`=? WHERE id=?')
                     ->execute([$status, $condition, $id]);
        } else {
            $this->db->prepare('UPDATE lbr_books SET status=? WHERE id=?')
                     ->execute([$status, $id]);
        }
    }

    /** Save an uploaded cover filename for a book. */
    public function setCoverLocal(int $id, string $filename): void
    {
        $this->db->prepare('UPDATE lbr_books SET cover_local=? WHERE id=?')
                 ->execute([$filename, $id]);
    }
}

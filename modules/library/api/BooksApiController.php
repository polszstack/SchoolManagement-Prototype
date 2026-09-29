<?php
/**
 * BooksApiController
 * Handles all book-related API actions:
 *   get_books | get_book | add_book | update_book | delete_book | upload_cover | get_genres
 */
class BooksApiController
{
    private Book        $book;
    private ActivityLog $log;

    public function __construct()
    {
        $this->book = new Book();
        $this->log  = new ActivityLog();
    }

    // ------------------------------------------------------------------
    // Dispatch
    // ------------------------------------------------------------------

    public function handle(string $action, array $input): never
    {
        match ($action) {
            'get_books'    => $this->getBooks(),
            'get_book'     => $this->getBook(),
            'add_book'     => $this->addBook($input),
            'update_book'  => $this->updateBook($input),
            'delete_book'  => $this->deleteBook($input),
            'upload_cover' => $this->uploadCover(),
            'get_genres'   => $this->getGenres(),
            default        => Response::error("Unknown books action: {$action}"),
        };
    }

    // ------------------------------------------------------------------
    // Actions
    // ------------------------------------------------------------------

    private function getBooks(): never
    {
        $search = $_GET['search'] ?? '';
        $genre  = $_GET['genre']  ?? '';
        $status = $_GET['status'] ?? '';
        $page   = max(1, (int)($_GET['page']  ?? 1));
        $limit  = (int)($_GET['limit'] ?? 10);

        Response::json($this->book->list($search, $genre, $status, $page, $limit));
    }

    private function getBook(): never
    {
        $id   = (int)($_GET['id'] ?? 0);
        $book = $this->book->find($id);

        if (!$book) {
            Response::error('Book not found.', 404);
        }

        $transaction        = new Transaction();
        $book['history']    = $transaction->historyForBook($id);

        Response::json($book);
    }

    private function addBook(array $input): never
    {
        $title  = trim($input['title']  ?? '');
        $author = trim($input['author'] ?? '');
        if (!$title || !$author) {
            Response::error('Title and Author are required.');
        }

        $id = $this->book->create(
            title:       $title,
            author:      $author,
            genre:       trim($input['genre']       ?? 'General'),
            year:        !empty($input['year']) ? (int)$input['year'] : null,
            isbn:        trim($input['isbn']        ?? '') ?: null,
            description: trim($input['description'] ?? ''),
            coverUrl:    trim($input['cover_url']   ?? '') ?: null,
        );

        $this->log->log('Book Added', "Added \"{$title}\" by {$author}");
        Response::success("Book \"{$title}\" added successfully!", ['id' => $id]);
    }

    private function updateBook(array $input): never
    {
        $id    = (int)($input['id']     ?? 0);
        $title = trim($input['title']  ?? '');
        $author = trim($input['author'] ?? '');

        if (!$id || !$title || !$author) {
            Response::error('ID, Title and Author are required.');
        }

        $this->book->update(
            id:          $id,
            title:       $title,
            author:      $author,
            genre:       trim($input['genre']       ?? 'General'),
            year:        !empty($input['year']) ? (int)$input['year'] : null,
            isbn:        trim($input['isbn']        ?? '') ?: null,
            description: trim($input['description'] ?? ''),
            condition:   trim($input['condition']   ?? 'Good'),
            coverUrl:    trim($input['cover_url']   ?? '') ?: null,
        );

        $this->log->log('Book Updated', "Updated \"{$title}\"");
        Response::success("Book \"{$title}\" updated successfully!");
    }

    private function deleteBook(array $input): never
    {
        $id = (int)($input['id'] ?? $_GET['id'] ?? 0);
        if (!$id) Response::error('Book ID required.');

        $book = $this->book->find($id);
        if (!$book) Response::error('Book not found.', 404);
        if ($book['status'] === 'borrowed') {
            Response::error('Cannot delete a book that is currently borrowed.');
        }

        $this->book->delete($id);
        $this->log->log('Book Deleted', "Deleted \"{$book['title']}\"");
        Response::success('Book deleted successfully!');
    }

    private function uploadCover(): never
    {
        if (!isset($_FILES['cover']) || $_FILES['cover']['error'] !== UPLOAD_ERR_OK) {
            Response::error('File upload failed.');
        }

        $file    = $_FILES['cover'];
        $bookId  = (int)($_POST['book_id'] ?? 0);
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $finfo   = finfo_open(FILEINFO_MIME_TYPE);
        $mime    = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowed, true)) {
            Response::error('Invalid file type. Use JPG, PNG, WebP, or GIF.');
        }

        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'cover_' . ($bookId ?: uniqid()) . '_' . time() . '.' . $ext;
        $dest     = UPLOAD_DIR . $filename;

        if (!is_dir(UPLOAD_DIR)) {
            mkdir(UPLOAD_DIR, 0755, true);
        }

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            Response::error('Failed to save file.', 500);
        }

        if ($bookId) {
            $this->book->setCoverLocal($bookId, $filename);
            $this->log->log('Cover Uploaded', "Uploaded cover for book ID {$bookId}");
        }

        Response::success('Cover uploaded!', ['filename' => $filename, 'url' => UPLOAD_URL . $filename]);
    }

    private function getGenres(): never
    {
        Response::json(['genres' => $this->book->genres()]);
    }
}

<?php
/**
 * BorrowersApiController
 * Handles all borrower-related API actions including rgr_students integration.
 */
class BorrowersApiController
{
    private Borrower    $borrower;
    private Transaction $transaction;
    private ActivityLog $log;

    public function __construct()
    {
        $this->borrower    = new Borrower();
        $this->transaction = new Transaction();
        $this->log         = new ActivityLog();
    }

    public function handle(string $action, array $input): never
    {
        match ($action) {
            'get_borrowers'    => $this->getBorrowers(),
            'get_borrower'     => $this->getBorrower(),
            'add_borrower'     => $this->addBorrower($input),
            'update_borrower'  => $this->updateBorrower($input),
            'delete_borrower'  => $this->deleteBorrower($input),
            'search_students'  => $this->searchStudents(),
            'import_student'   => $this->importStudent($input),
            default            => Response::error("Unknown borrowers action: {$action}"),
        };
    }

    private function getBorrowers(): never
    {
        $search = $_GET['search'] ?? '';
        $type   = $_GET['type']   ?? '';
        $page   = max(1, (int)($_GET['page']  ?? 1));
        $limit  = (int)($_GET['limit'] ?? 10);
        Response::json($this->borrower->list($search, $type, $page, $limit));
    }

    private function getBorrower(): never
    {
        $id       = (int)($_GET['id'] ?? 0);
        $borrower = $this->borrower->find($id);
        if (!$borrower) Response::error('Member not found.', 404);
        $borrower['history'] = $this->transaction->historyForBorrower($id);
        Response::json($borrower);
    }

    private function addBorrower(array $input): never
    {
        $name       = trim($input['name']        ?? '');
        $borrowerId = trim($input['borrower_id'] ?? '');

        if (!$name || !$borrowerId) Response::error('Name and Member ID are required.');
        if ($this->borrower->idExists($borrowerId)) Response::error('Member ID already exists.');

        $id = $this->borrower->create(
            name:       $name,
            borrowerId: $borrowerId,
            email:      trim($input['email']   ?? '') ?: null,
            phone:      trim($input['phone']   ?? '') ?: null,
            type:       trim($input['type']    ?? 'Student'),
            grade:      trim($input['grade']   ?? '') ?: null,
            address:    trim($input['address'] ?? '') ?: null,
        );

        $this->log->log('Member Added', "Added member \"{$name}\" ({$borrowerId})");
        Response::success("Member \"{$name}\" added successfully!", ['id' => $id]);
    }

    private function updateBorrower(array $input): never
    {
        $id   = (int)($input['id']   ?? 0);
        $name = trim($input['name'] ?? '');
        if (!$id || !$name) Response::error('ID and Name required.');

        $this->borrower->update(
            id:      $id,
            name:    $name,
            email:   trim($input['email']   ?? '') ?: null,
            phone:   trim($input['phone']   ?? '') ?: null,
            type:    trim($input['type']    ?? 'Student'),
            grade:   trim($input['grade']   ?? '') ?: null,
            address: trim($input['address'] ?? '') ?: null,
        );

        $this->log->log('Member Updated', "Updated member \"{$name}\"");
        Response::success('Member updated successfully!');
    }

    private function deleteBorrower(array $input): never
    {
        $id = (int)($input['id'] ?? $_GET['id'] ?? 0);
        if (!$id) Response::error('Borrower ID required.');

        if ($this->transaction->activeLoanCount($id) > 0) {
            Response::error('Cannot delete a member with active or overdue books.');
        }

        $borrower = $this->borrower->find($id);
        if (!$borrower) Response::error('Member not found.', 404);

        $this->borrower->delete($id);
        $this->log->log('Member Deleted', "Deleted member \"{$borrower['name']}\"");
        Response::success('Member deleted successfully!');
    }

    // ── Search rgr_students ───────────────────────────────────────────
    private function searchStudents(): never
    {
        $search = trim($_GET['search'] ?? '');
        $limit  = (int)($_GET['limit'] ?? 20);

        if (strlen($search) < 2) {
            Response::json(['students' => [], 'message' => 'Type at least 2 characters to search.']);
        }

        $students = $this->borrower->searchStudents($search, $limit);
        Response::json(['students' => $students, 'total' => count($students)]);
    }

    // ── Import single student from rgr_students ───────────────────────
    private function importStudent(array $input): never
    {
        $studentNumber = (int)($input['student_number'] ?? 0);
        if (!$studentNumber) Response::error('Student number is required.');

        $result = $this->borrower->importFromStudent($studentNumber);
        if (!$result) Response::error('Student not found or already registered as a member.');

        $this->log->log(
            'Student Imported',
            "Imported student \"{$result['name']}\" (#{$result['borrower_id']}) from Registrar"
        );
        Response::success(
            "Student \"{$result['name']}\" imported successfully!",
            ['id' => $result['id'], 'borrower_id' => $result['borrower_id']]
        );
    }
}

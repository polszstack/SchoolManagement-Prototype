<?php
// ============================================================
// index.php — Application Entry Point
// ============================================================

// 1. Start session
if (session_status() === PHP_SESSION_NONE) session_start();

// 2. Guard — redirect to login if not authenticated

// 3. Load app
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/includes/Settings.php';
require_once __DIR__ . '/includes/ActivityLog.php';
require_once __DIR__ . '/includes/Book.php';
require_once __DIR__ . '/includes/Borrower.php';
require_once __DIR__ . '/includes/Transaction.php';

class LibraryPageController
{
    private Settings $settings;

    public function __construct()
    {
        (new Settings())->updateOverdueStatus();
        $this->settings = new Settings();
    }

    public function render(): void
    {
        $libraryName  = $this->settings->get('library_name', 'BCP School Library');
        $employeeName     = $_SESSION['first_name ']  ?? 'Librarian';
        $userRole     = $_SESSION['role']     ?? 'Staff';
        $userPosition = $_SESSION['position'] ?? $userRole;
        $userInitial  = strtoupper(substr($employeeName, 0, 1));

        $currentDate  = date('D, M j Y');
        $currentYear  = date('Y');
        $phpVersion   = PHP_VERSION;
        $dbHost       = DB_HOST;
        $dbName       = DB_NAME;
        $dbCharset    = DB_CHARSET;

        require __DIR__ . '/views/layout.php';
    }
}

(new LibraryPageController())->render();

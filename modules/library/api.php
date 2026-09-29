<?php
// ============================================================
// api.php — REST API Router
// ============================================================
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/includes/Response.php';
require_once __DIR__ . '/includes/ActivityLog.php';
require_once __DIR__ . '/includes/Settings.php';
require_once __DIR__ . '/includes/Book.php';
require_once __DIR__ . '/includes/Borrower.php';
require_once __DIR__ . '/includes/Transaction.php';
require_once __DIR__ . '/includes/AILateReturnDetector.php';
require_once __DIR__ . '/includes/AIBookRecommender.php';
require_once __DIR__ . '/api/BooksApiController.php';
require_once __DIR__ . '/api/BorrowersApiController.php';
require_once __DIR__ . '/api/TransactionsApiController.php';
require_once __DIR__ . '/api/DashboardApiController.php';
require_once __DIR__ . '/api/SettingsApiController.php';
require_once __DIR__ . '/api/AIApiController.php';

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('Access-Control-Allow-Origin: *');

(new Settings())->updateOverdueStatus();

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

$input = [];
if (in_array($method, ['POST', 'PUT', 'DELETE'], true)) {
    $raw = file_get_contents('php://input');
    if ($raw) {
        $input = json_decode($raw, true) ?? [];
    }
    $input = array_merge($input, $_POST);
}

$routes = [
    'get_books'           => BooksApiController::class,
    'get_book'            => BooksApiController::class,
    'add_book'            => BooksApiController::class,
    'update_book'         => BooksApiController::class,
    'delete_book'         => BooksApiController::class,
    'upload_cover'        => BooksApiController::class,
    'get_genres'          => BooksApiController::class,
    'get_borrowers'       => BorrowersApiController::class,
    'get_borrower'        => BorrowersApiController::class,
    'add_borrower'        => BorrowersApiController::class,
    'update_borrower'     => BorrowersApiController::class,
    'delete_borrower'     => BorrowersApiController::class,
    'search_students'     => BorrowersApiController::class,
    'import_student'      => BorrowersApiController::class,
    'get_transactions'    => TransactionsApiController::class,
    'borrow_book'         => TransactionsApiController::class,
    'return_book'         => TransactionsApiController::class,
    'get_stats'              => DashboardApiController::class,
    'get_report'             => DashboardApiController::class,
    'get_activity'           => DashboardApiController::class,
    'get_registrar_activity' => DashboardApiController::class,
    'get_settings'        => SettingsApiController::class,
    'save_settings'       => SettingsApiController::class,
    'ai_risk_report'      => AIApiController::class,
    'ai_run_reminders'    => AIApiController::class,
    'ai_recommendations'  => AIApiController::class,
    'ai_borrower_profile' => AIApiController::class,
    'ai_reminder_log'     => AIApiController::class,
];

try {
    if (!$action) Response::error('No action specified.', 400);
    if (!isset($routes[$action])) Response::error("Unknown action: {$action}", 400);
    $controllerClass = $routes[$action];
    (new $controllerClass())->handle($action, $input);
} catch (PDOException $e) {
    Response::error('Database error: ' . $e->getMessage(), 500);
} catch (Throwable $e) {
    Response::error('Server error: ' . $e->getMessage(), 500);
}

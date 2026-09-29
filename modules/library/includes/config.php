<?php
// ============================================================
// includes/config.php — Connected to SMS database
// ============================================================
define('DB_HOST',    'localhost');
define('DB_USER',    'root');       // ← your MySQL username
define('DB_PASS',    '');           // ← your MySQL password
define('DB_NAME',    'sms');        // ← your actual database name
define('DB_CHARSET', 'utf8mb4');

define('UPLOAD_DIR', __DIR__ . '/../uploads/covers/');
define('UPLOAD_URL', 'uploads/covers/');

// ============================================================
// Autoloader
// ============================================================
spl_autoload_register(function (string $class): void {
    $base = __DIR__ . '/';
    $dirs = [$base, $base . '../controllers/', $base . '../api/'];
    foreach ($dirs as $dir) {
        $file = $dir . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

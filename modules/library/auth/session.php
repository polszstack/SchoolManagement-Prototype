<?php
// ============================================================
// auth/session.php — Simple session guard
// Only used by files that explicitly include it
// ============================================================
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Find login.php relative to document root
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME']);
    $docroot = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '/');
    $scriptUrl = str_replace($docroot, '', $script);
    $dir = rtrim(dirname($scriptUrl), '/');

    // Walk up directories until we find login.php
    $parts = explode('/', trim($dir, '/'));
    $found = '';
    for ($i = count($parts); $i >= 0; $i--) {
        $try = '/' . implode('/', array_slice($parts, 0, $i)) . '/login.php';
        if (file_exists($docroot . $try)) {
            $found = $try;
            break;
        }
    }
    if (!$found) $found = '/login.php';

    header('Location: ' . $found);
    exit;
}

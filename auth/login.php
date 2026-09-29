<?php
require_once __DIR__ . '/../database/db.php';

function ensureWritableSessionPath(): void {
    $currentPath = session_save_path();
    if ($currentPath && is_dir($currentPath) && is_writable($currentPath)) {
        return;
    }

    $fallbackPath = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'prototype_sessions';
    if (!is_dir($fallbackPath)) {
        mkdir($fallbackPath, 0777, true);
    }
    session_save_path($fallbackPath);
}

function verifyMonitoringPassword(string $password, string $hash): bool {
    if (password_verify($password, $hash)) {
        return true;
    }

    return strlen($hash) === 32 && hash_equals(strtolower($hash), md5($password));
}

function monitoringRedirectFor(string $role, string $requestedRedirect = ''): string {
    $allowedRedirects = [
        'modules/monitoring/public/index.php?page=dashboard',
        'modules/monitoring/public/index.php?page=mobile-attendance',
        'modules/monitoring/public/index.php?page=mobile-facilities',
    ];

    if ($requestedRedirect !== '' && in_array($requestedRedirect, $allowedRedirects, true)) {
        return '/' . ltrim($requestedRedirect, '/');
    }

    return '/modules/monitoring/public/index.php?page=dashboard';
}

ensureWritableSessionPath();
session_start();

header('Content-Type: application/json');

define('MAX_ATTEMPTS', 3);
define('LOCKOUT_TIME', 60);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

try {
    $username = trim($_POST['username'] ?? $_POST['employee_id'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        echo json_encode(['success' => false, 'locked' => false, 'message' => 'Username and password are required.']);
        exit();
    }

    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $key = 'login_attempts_' . $ip . '_' . $username;

    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['count' => 0, 'last_attempt' => time()];
    }

    $attempts = &$_SESSION[$key];
    $elapsed = time() - $attempts['last_attempt'];

    if ($elapsed > LOCKOUT_TIME) {
        $attempts = ['count' => 0, 'last_attempt' => time()];
        $elapsed = 0;
    }

    if ($attempts['count'] >= MAX_ATTEMPTS) {
        echo json_encode([
            'success' => false,
            'locked' => true,
            'remaining_seconds' => max(0, (int) (LOCKOUT_TIME - $elapsed)),
            'message' => 'Too many failed attempts.',
        ]);
        exit();
    }

    $conn = (new Database())->getConnection();
    $stmt = $conn->prepare("
        SELECT id, username, password, fullname, email, role, status, last_login, created_at
        FROM mon_users
        WHERE username = :username
          AND status = 'active'
        LIMIT 1
    ");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !verifyMonitoringPassword($password, (string) $user['password'])) {
        $attempts['count']++;
        $attempts['last_attempt'] = time();
        $remainingAttempts = MAX_ATTEMPTS - $attempts['count'];

        if ($remainingAttempts <= 0) {
            echo json_encode([
                'success' => false,
                'locked' => true,
                'remaining_seconds' => LOCKOUT_TIME,
                'message' => 'Too many failed attempts. Account locked for 1 minute.',
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'locked' => false,
                'remaining_attempts' => $remainingAttempts,
                'message' => 'Invalid Username or Password.',
            ]);
        }
        exit();
    }

    unset($_SESSION[$key]);
    session_regenerate_id(true);

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['employee_id'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['role_name'] = ucfirst($user['role']);
    $_SESSION['department_id'] = null;
    $_SESSION['department_name'] = 'Monitoring';
    $_SESSION['fullname'] = $user['fullname'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['sensitive_auth'] = true;
    $_SESSION['sensitive_last_activity'] = time();
    $_SESSION['sensitive_timeout'] = isset($_POST['remember_me']) && $_POST['remember_me'] === 'on'
        ? 2592000
        : 900;

    if (isset($_POST['remember_me']) && $_POST['remember_me'] === 'on') {
        setcookie(session_name(), session_id(), time() + 2592000, '/');
        $_SESSION['remember_me'] = true;
    }

    $update = $conn->prepare("UPDATE mon_users SET last_login = NOW() WHERE id = :id");
    $update->execute(['id' => $user['id']]);

    unset($user['password']);

    echo json_encode([
        'success' => true,
        'user' => $user,
        'redirect' => monitoringRedirectFor((string) $user['role'], trim($_POST['redirect'] ?? '')),
    ]);
    exit();
} catch (Throwable $e) {
    error_log("Monitoring login error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'locked' => false,
        'message' => 'Login server error. Please check the mon_users table and database connection.',
    ]);
    exit();
}
?>

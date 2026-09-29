<?php
include_once __DIR__ . '/../../../auth/session.php';

// Set default value for admin_name if not set
$admin_name = $admin_name ?? 'Admin';
$user_role = $user_role ?? 'Administrator';
require_once '../config/database.php';
require_once '../models/Student.php';

// Check if user is admin
if ($_SESSION['user_type'] != 'admin') {
    header('Location: ../index.php');
    exit();
}

$student_id = isset($_GET['id']) ? $_GET['id'] : 0;

if ($student_id) {
    $student = new Student();
    if ($student->graduate($student_id)) {
        $_SESSION['success'] = 'Student marked as graduated successfully';
    } else {
        $_SESSION['error'] = 'Failed to update student status';
    }
}

header('Location: students.php');
exit();
?>
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student = new Student();
    $student_id = $_POST['student_id'];
    $year_level = $_POST['year_level'];
    
    if ($student->updateYearLevel($student_id, $year_level)) {
        $_SESSION['success'] = 'Student year level updated successfully';
    } else {
        $_SESSION['error'] = 'Failed to update student year level';
    }
}

header('Location: students.php');
exit();
?>
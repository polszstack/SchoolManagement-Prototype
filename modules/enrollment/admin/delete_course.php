<?php
include_once __DIR__ . '/../../../auth/session.php';

// Set default value for admin_name if not set
$admin_name = $admin_name ?? 'Admin';
$user_role = $user_role ?? 'Administrator';
require_once '../config/database.php';
require_once '../models/Course.php';

$course = new Course();
$id = $_GET['id'] ?? 0;

if($id) {
    $course->delete($id);
    header("Location: courses.php?msg=deleted");
    exit();
}
?>
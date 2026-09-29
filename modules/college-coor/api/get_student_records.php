<?php
require_once('../classes/StudentManager.php');
require_once('../../../database/db.php');
header('Content-Type: application/json');

$database = new Database();
$conn = $database->getConnection();
$manager = new StudentManager($conn);
echo json_encode($manager->getAllStudents());
?>

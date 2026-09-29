<?php
require_once('../classes/StudentManager.php');
require_once('../../../database/db.php');
header('Content-Type: application/json');
$manager = new StudentManager($conn);
$id = $_GET['id'] ?? '';
echo json_encode($manager->getStudentDetails($id));

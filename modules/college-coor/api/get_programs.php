<?php
require_once('../classes/ProgramManager.php');
require_once('../../../database/db.php');
header('Content-Type: application/json');
$manager = new ProgramManager($conn);
echo json_encode($manager->getAllPrograms());

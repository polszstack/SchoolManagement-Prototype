<?php
require_once('../classes/ProgramManager.php');
require_once('../../../database/db.php');
header('Content-Type: application/json');
$programId = $_GET['program_id'] ?? 0;

if (!$programId) {
    echo json_encode(['error' => 'Program ID required']);
    exit;
}

try {
    $database = new Database();
    $conn = $database->getConnection();

    // Query to get curriculum with subjects
    $query = "SELECT c.year_level, c.semester, s.subject_code, s.subject_name, s.units
              FROM curriculum c
              LEFT JOIN subjects s ON c.subject_id = s.subject_id
              WHERE c.program_id = ?
              ORDER BY c.year_level, c.semester, s.subject_code";

    $stmt = $conn->prepare($query);
    $stmt->execute([$programId]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Group by year and semester
    $curriculum = [];
    foreach ($results as $row) {
        $year = $row['year_level'];
        $sem = $row['semester'];
        if (!isset($curriculum[$year])) {
            $curriculum[$year] = [];
        }
        if (!isset($curriculum[$year][$sem])) {
            $curriculum[$year][$sem] = [];
        }
        $curriculum[$year][$sem][] = [
            'subject_code' => $row['subject_code'],
            'subject_name' => $row['subject_name'],
            'units' => $row['units']
        ];
    }

    echo json_encode($curriculum);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>

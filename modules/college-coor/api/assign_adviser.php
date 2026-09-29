<?php
header('Content-Type: application/json');
require_once(__DIR__ . '/../../../database/db.php');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $section_id = isset($_POST['section_id']) ? (int)$_POST['section_id'] : null;
    $faculty_id = isset($_POST['faculty_id']) ? (int)$_POST['faculty_id'] : null;

    if (!$section_id || !$faculty_id) {
        throw new Exception('Missing required fields: section_id and faculty_id');
    }

    $database = new Database();
    $conn = $database->getConnection();

    // Update cc_sections with adviser_id
    $updateStmt = $conn->prepare("UPDATE cc_sections SET adviser_id = :faculty_id WHERE id = :section_id");
    $updateStmt->bindParam(':section_id', $section_id, PDO::PARAM_INT);
    $updateStmt->bindParam(':faculty_id', $faculty_id, PDO::PARAM_INT);
    
    if ($updateStmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Adviser assigned successfully'
        ]);
    } else {
        throw new Exception('Failed to update adviser');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>

<?php
/**
 * Get Events Statistics API
 * Return summary stats for dashboard
 */

require_once dirname(dirname(dirname(__DIR__))) . '/database/db.php';

header('Content-Type: application/json');

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Total events
    $totalQuery = "SELECT COUNT(*) as total FROM cc_events";
    $totalStmt = $conn->prepare($totalQuery);
    $totalStmt->execute();
    $total = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Upcoming events
    $upcomingQuery = "SELECT COUNT(*) as total FROM cc_events WHERE event_date > CURDATE() OR (event_date = CURDATE() AND start_time > CURTIME())";
    $upcomingStmt = $conn->prepare($upcomingQuery);
    $upcomingStmt->execute();
    $upcoming = $upcomingStmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Ongoing events (today)
    $ongoingQuery = "SELECT COUNT(*) as total FROM cc_events WHERE event_date = CURDATE()";
    $ongoingStmt = $conn->prepare($ongoingQuery);
    $ongoingStmt->execute();
    $ongoing = $ongoingStmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Completed events
    $completedQuery = "SELECT COUNT(*) as total FROM cc_events WHERE status = 'Completed' OR event_date < CURDATE()";
    $completedStmt = $conn->prepare($completedQuery);
    $completedStmt->execute();
    $completed = $completedStmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    echo json_encode([
        'success' => true,
        'stats' => [
            'total' => $total,
            'upcoming' => $upcoming,
            'ongoing' => $ongoing,
            'completed' => $completed
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>

<?php
/**
 * Test API Connection
 * Use this to verify your API is working
 */

header('Content-Type: application/json');

try {
    require_once dirname(dirname(dirname(__DIR__))) . '/database/db.php';
    require_once dirname(__DIR__) . '/classes/EventManager.php';
    
    $database = new Database();
    $conn = $database->getConnection();
    
    // Test database connection
    $stmt = $conn->query("SELECT 1");
    
    // Get table info
    $tables = $conn->query("SHOW TABLES LIKE 'cc_events'");
    $tableExists = $tables->rowCount() > 0;
    
    // Get column names
    $columns = [];
    if ($tableExists) {
        $colQuery = $conn->query("DESCRIBE cc_events");
        while ($col = $colQuery->fetch(PDO::FETCH_ASSOC)) {
            $columns[] = $col['Field'];
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'API is working',
        'database' => 'Connected',
        'table_exists' => $tableExists,
        'columns' => $columns,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Buffer output to prevent accidental whitespace/buffer flushing before headers
ob_start();

function respondError($message, $code = 500) {
    if (!headers_sent()) {
        http_response_code($code);
        header('Content-Type: application/json');
    }
    echo json_encode(['success' => false, 'message' => $message]);
    if (ob_get_length()) {
        ob_end_flush();
    }
    exit;
}

// Get the event_id from POST or GET
$event_id = isset($_POST['event_id']) ? (int)$_POST['event_id'] : (isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0);

if (!$event_id) {
    respondError('Event ID is required', 400);
}

// Include database connection
require_once dirname(dirname(dirname(__DIR__))) . '/database/db.php';

// Create database connection
$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    respondError('Database connection failed', 500);
}

// Fetch event from database
try {
    $stmt = $conn->prepare("SELECT * FROM cc_events WHERE event_id = :event_id");
    $stmt->bindValue(':event_id', $event_id, PDO::PARAM_INT);
    $stmt->execute();
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$event) {
        respondError('Event not found', 404);
    }
} catch (Exception $e) {
    respondError('Error fetching event: ' . $e->getMessage(), 500);
}

// Check if TCPDF is available, otherwise use simple HTML to PDF approach
$useLibrary = false;

// Try to use TCPDF
if (file_exists(dirname(dirname(dirname(__DIR__))) . '/vendor/autoload.php')) {
    require_once dirname(dirname(dirname(__DIR__))) . '/vendor/autoload.php';
    if (class_exists('TCPDF')) {
        $useLibrary = true;
    }
}

if (!$useLibrary) {
    // Fall back to DOMPDF if available (from registrar module)
    if (file_exists(dirname(dirname(__DIR__)) . '/registrar/vendor/autoload.php')) {
        require_once dirname(dirname(__DIR__)) . '/registrar/vendor/autoload.php';
        if (class_exists('Dompdf\Dompdf')) {
            $useLibrary = true;
            $useDompdf = true;
        }
    }
}

// Generate PDF content
$html = generateEventPDF($event);

// NOTE: TCPDF is optionally loaded via composer/autoload when available.
// The following stub exists only to satisfy static analysis tools (Intelephense) that
// otherwise report "Undefined type 'TCPDF'".
if (false) {
    /**
     * Dummy TCPDF class used only for static analysis (Intelephense) to prevent
     * undefined type/method errors. The real TCPDF library is loaded via composer
     * autoload when available.
     */
    class TCPDF {
        public function AddPage() {}
        public function SetFont($family, $style = '', $size = 0) {}
        public function writeHTML($html) {}
        public function Output($name = '', $dest = '') {}
    }
}

try {
    // If library available, use it; otherwise output basic HTML
    if (isset($useDompdf) && $useDompdf) {
        // Use DOMPDF
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // Ensure no stray output before headers
        if (ob_get_length()) {
            ob_clean();
        }

        // Download PDF
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="Event_' . date('Y_m_d_His') . '_' . $event['event_id'] . '.pdf"');
        header('Cache-Control: public, must-revalidate, max-age=0');
        header('Pragma: public');
        
        echo $dompdf->output();
    } else {
        // Use TCPDF if available
        if (class_exists('TCPDF')) {
            /** @noinspection PhpUndefinedClassInspection */
            $pdf = new \TCPDF();
            $pdf->AddPage();
            $pdf->SetFont('helvetica', '', 10);
            $pdf->writeHTML($html);
            
            // Ensure no stray output before headers
            if (ob_get_length()) {
                ob_clean();
            }

            // Download PDF
            $pdf->Output('Event_' . date('Y_m_d_His') . '_' . $event['event_id'] . '.pdf', 'D');
        } else {
            // Fallback: Output as HTML with print styles
            if (ob_get_length()) {
                ob_clean();
            }

            header('Content-Type: text/html; charset=utf-8');
            header('Content-Disposition: attachment; filename="Event_' . date('Y_m_d_His') . '.html"');
            echo $html;
        }
    }
} catch (Throwable $t) {
    respondError('PDF generation failed: ' . $t->getMessage(), 500);
}

exit;

/**
 * Generate PDF HTML content for an event
 */
function generateEventPDF($event) {
    $html = <<<'HTML'
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Event Details</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            body {
                font-family: Arial, Helvetica, sans-serif;
                color: #333;
                line-height: 1.6;
                background: white;
            }
            
            .container {
                width: 100%;
                max-width: 8.5in;
                margin: 0 auto;
                padding: 20px;
            }
            
            .header {
                border-bottom: 3px solid #0d6efd;
                margin-bottom: 30px;
                padding-bottom: 15px;
            }
            
            .header h1 {
                font-size: 28px;
                color: #0d6efd;
                margin-bottom: 5px;
            }
            
            .header p {
                color: #666;
                font-size: 12px;
            }
            
            .event-title {
                font-size: 24px;
                font-weight: bold;
                color: #333;
                margin-top: 20px;
                margin-bottom: 20px;
            }
            
            .section {
                margin-bottom: 20px;
            }
            
            .section-title {
                font-size: 14px;
                font-weight: bold;
                color: #0d6efd;
                border-bottom: 1px solid #ddd;
                padding-bottom: 8px;
                margin-bottom: 10px;
            }
            
            .field {
                margin-bottom: 12px;
                display: flex;
            }
            
            .field-label {
                font-weight: bold;
                color: #555;
                width: 150px;
                min-width: 150px;
            }
            
            .field-value {
                color: #333;
                flex: 1;
            }
            
            .status-badge {
                display: inline-block;
                padding: 4px 12px;
                border-radius: 4px;
                font-size: 12px;
                font-weight: bold;
                color: white;
            }
            
            .status-upcoming { background-color: #6c757d; }
            .status-ongoing { background-color: #0d6efd; }
            .status-completed { background-color: #28a745; }
            .status-cancelled { background-color: #dc3545; }
            
            .footer {
                border-top: 1px solid #ddd;
                padding-top: 15px;
                margin-top: 30px;
                font-size: 11px;
                color: #666;
                text-align: center;
            }
            
            .two-column {
                display: flex;
                gap: 30px;
            }
            
            .column {
                flex: 1;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>Event Details</h1>
                <p>College Coordinator Module - SMS System</p>
            </div>
            
            <div class="event-title">EVENT_TITLE</div>
            
            <div class="two-column">
                <div class="column">
                    <div class="section">
                        <div class="section-title">Basic Information</div>
                        
                        <div class="field">
                            <div class="field-label">Event Type:</div>
                            <div class="field-value">EVENT_TYPE</div>
                        </div>
                        
                        <div class="field">
                            <div class="field-label">Event Date:</div>
                            <div class="field-value">EVENT_DATE</div>
                        </div>
                        
                        <div class="field">
                            <div class="field-label">Time:</div>
                            <div class="field-value">START_TIME to END_TIME</div>
                        </div>
                        
                        <div class="field">
                            <div class="field-label">Location:</div>
                            <div class="field-value">LOCATION</div>
                        </div>
                    </div>
                </div>
                
                <div class="column">
                    <div class="section">
                        <div class="section-title">Status & Audience</div>
                        
                        <div class="field">
                            <div class="field-label">Status:</div>
                            <div class="field-value">
                                <span class="status-badge status-STATUS_CLASS">STATUS_TEXT</span>
                            </div>
                        </div>
                        
                        <div class="field">
                            <div class="field-label">Target Audience:</div>
                            <div class="field-value">TARGET_AUDIENCE</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="section">
                <div class="section-title">Description</div>
                <div class="field">
                    <div class="field-value">DESCRIPTION</div>
                </div>
            </div>
            
            <div class="footer">
                <p>Generated on: PDF_DATE | Document ID: DOC_ID</p>
            </div>
        </div>
    </body>
    </html>
    HTML;
    
    // Replace placeholders with actual data
    $replacements = [
        'EVENT_TITLE' => htmlspecialchars($event['event_title'] ?? 'N/A'),
        'EVENT_TYPE' => htmlspecialchars($event['event_type'] ?? 'N/A'),
        'EVENT_DATE' => formatEventDate($event['event_date'] ?? ''),
        'START_TIME' => htmlspecialchars($event['start_time'] ?? 'N/A'),
        'END_TIME' => htmlspecialchars($event['end_time'] ?? 'N/A'),
        'LOCATION' => htmlspecialchars($event['location'] ?? 'N/A'),
        'STATUS_CLASS' => strtolower($event['status'] ?? 'unknown'),
        'STATUS_TEXT' => htmlspecialchars($event['status'] ?? 'N/A'),
        'TARGET_AUDIENCE' => htmlspecialchars($event['target_audience'] ?? 'N/A'),
        'DESCRIPTION' => nl2br(htmlspecialchars($event['description'] ?? 'No description provided')),
        'PDF_DATE' => date('F d, Y H:i:s'),
        'DOC_ID' => 'EV-' . str_pad($event['event_id'], 5, '0', STR_PAD_LEFT) . '-' . date('YmdHis')
    ];
    
// Replace all placeholders
    foreach ($replacements as $placeholder => $value) {
        $html = str_replace($placeholder, $value, $html);
    }
    
    return $html;
}

/**
 * Format event date for display
 */
function formatEventDate($dateStr) {
    if (!$dateStr) return 'N/A';
    try {
        $date = new DateTime($dateStr);
        return $date->format('F d, Y');
    } catch (Exception $e) {
        return htmlspecialchars($dateStr);
    }
}
?>

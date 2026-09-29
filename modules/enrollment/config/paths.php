<?php
// config/paths.php
// Central configuration for all file paths

// Base URL for accessing uploaded files from the web
define('UPLOAD_BASE_URL', '/enrollment-website/uploads/requirements/');

// Base physical path on server
define('UPLOAD_BASE_PATH', 'C:/xampp/htdocs/enrollment-website/uploads/requirements/');

// Alternative dynamic base path detection (uncomment if needed)
// define('UPLOAD_BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/enrollment-website/uploads/requirements/');

// Helper function to get correct document URL
function getDocumentUrl($file_path) {
    if (empty($file_path)) return '#';
    
    // Extract filename and applicant_id from stored path
    $filename = basename($file_path);
    
    // Try to extract applicant_id from path
    preg_match('/(\d+)\//', $file_path, $matches);
    $applicant_id = $matches[1] ?? '';
    
    if ($applicant_id) {
        return UPLOAD_BASE_URL . $applicant_id . '/' . $filename;
    }
    
    // Fallback: just return the filename with base URL
    return UPLOAD_BASE_URL . $filename;
}

// Helper function to get correct physical file path
function getDocumentPath($file_path) {
    if (empty($file_path)) return '';
    
    $filename = basename($file_path);
    preg_match('/(\d+)\//', $file_path, $matches);
    $applicant_id = $matches[1] ?? '';
    
    if ($applicant_id) {
        return UPLOAD_BASE_PATH . $applicant_id . '/' . $filename;
    }
    
    return UPLOAD_BASE_PATH . $filename;
}
?>
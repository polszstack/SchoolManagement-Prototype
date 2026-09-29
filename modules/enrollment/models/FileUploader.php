<?php
class FileUploader {
    private $upload_dir;
    private $allowed_types;
    private $max_size;

    public function __construct() {
        // Using realpath to ensure the path is solid for the OS
        $this->upload_dir = __DIR__ . '/../uploads/requirements/';
        $this->allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
        $this->max_size = 5242880; // 5MB
    }

    // Upload single file
    public function upload($file, $applicant_id, $document_type) {
        // 1. Create directory if not exists
        $target_dir = $this->upload_dir . $applicant_id . '/';
        if(!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // 2. SANITIZE document type
        // This removes slashes, spaces, and special chars that cause "Failed to open stream"
        $safe_doc_type = preg_replace('/[^A-Za-z0-9_\-]/', '_', $document_type);

        // 3. Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $safe_doc_type . '_' . time() . '_' . uniqid() . '.' . $extension;
        $target_file = $target_dir . $filename;

        // 4. Validate file type
        if(!in_array($file['type'], $this->allowed_types)) {
            return ['success' => false, 'message' => 'Invalid file type. Allowed: JPG, PNG, GIF, PDF'];
        }

        // 5. Validate file size
        if($file['size'] > $this->max_size) {
            return ['success' => false, 'message' => 'File too large. Max size: 5MB'];
        }

        // 6. Upload file
        if(move_uploaded_file($file['tmp_name'], $target_file)) {
            return [
                'success' => true,
                'file_data' => [
                    'name' => $filename,
                    'path' => 'uploads/requirements/' . $applicant_id . '/' . $filename,
                    'size' => $file['size'],
                    'type' => $file['type']
                ]
            ];
        }

        return ['success' => false, 'message' => 'Failed to upload file'];
    }

    // Upload multiple files
    public function uploadMultiple($files, $applicant_id) {
        $results = [];
        
        foreach($files['name'] as $key => $name) {
            if($files['error'][$key] == 0) {
                $file = [
                    'name' => $files['name'][$key],
                    'type' => $files['type'][$key],
                    'tmp_name' => $files['tmp_name'][$key],
                    'error' => $files['error'][$key],
                    'size' => $files['size'][$key]
                ];
                
                // Note: Using 'bulk_file' as a placeholder document_type
                $results[] = $this->upload($file, $applicant_id, 'bulk_file');
            }
        }
        
        return $results;
    }

    // Delete file
    public function delete($file_path) {
        $full_path = __DIR__ . '/../' . $file_path;
        if(file_exists($full_path)) {
            return unlink($full_path);
        }
        return false;
    }
}
?>
<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/paths.php';

class Document {
    private $conn;
    private $table = 'enr_documents';
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Upload document - saves to correct location and stores relative path in DB
     */
    public function upload($applicant_id, $document_type, $file_data) {
        try {
            // Create applicant-specific directory if it doesn't exist
            $target_dir = UPLOAD_BASE_PATH . $applicant_id . '/';
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            // Generate unique filename to prevent overwrites
            $file_extension = pathinfo($file_data['name'], PATHINFO_EXTENSION);
            $file_name = pathinfo($file_data['name'], PATHINFO_FILENAME);
            $unique_filename = $file_name . '_' . time() . '_' . uniqid() . '.' . $file_extension;
            
            $target_file = $target_dir . $unique_filename;
            
            // Move uploaded file to correct location
            if (move_uploaded_file($file_data['tmp_name'], $target_file)) {
                // Store relative path in database (for backward compatibility)
                $relative_path = 'uploads/requirements/' . $applicant_id . '/' . $unique_filename;
                
                $query = "INSERT INTO " . $this->table . " 
                          (applicant_id, document_type, file_name, file_path, file_size, mime_type, status)
                          VALUES 
                          (:applicant_id, :doc_type, :file_name, :file_path, :file_size, :mime_type, 'pending')";
                
                $stmt = $this->conn->prepare($query);
                
                $stmt->bindParam(':applicant_id', $applicant_id);
                $stmt->bindParam(':doc_type', $document_type);
                $stmt->bindParam(':file_name', $unique_filename);
                $stmt->bindParam(':file_path', $relative_path);
                $stmt->bindParam(':file_size', $file_data['size']);
                $stmt->bindParam(':mime_type', $file_data['type']);
                
                return $stmt->execute();
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Document upload error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get document by ID with full URL
     */
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $doc = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Add full URL to the document data
        if ($doc) {
            $doc['full_url'] = $this->getDocumentUrl($doc);
            $doc['full_path'] = $this->getDocumentPath($doc);
        }
        
        return $doc;
    }

    /**
     * Get documents by applicant with full URLs
     */
    public function getByApplicant($applicant_id) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE applicant_id = :applicant_id 
                  ORDER BY uploaded_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':applicant_id', $applicant_id);
        $stmt->execute();
        
        $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Add full URLs to each document
        foreach ($documents as &$doc) {
            $doc['full_url'] = $this->getDocumentUrl($doc);
            $doc['full_path'] = $this->getDocumentPath($doc);
        }
        
        return $documents;
    }

    /**
     * Get all pending documents with full URLs
     */
    public function getAllPending() {
        $query = "SELECT d.*, a.first_name, a.surname, a.application_number, a.id as applicant_id
                  FROM " . $this->table . " d
                  JOIN enr_applicants a ON d.applicant_id = a.id
                  WHERE d.status = 'pending'
                  ORDER BY d.uploaded_at ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Add full URLs to each document
        foreach ($documents as &$doc) {
            $doc['full_url'] = $this->getDocumentUrl($doc);
            $doc['full_path'] = $this->getDocumentPath($doc);
        }
        
        return $documents;
    }

    /**
     * Get full URL for document
     */
    private function getDocumentUrl($doc) {
        return getDocumentUrl($doc['file_path'] ?? '');
    }

    /**
     * Get full physical path for document
     */
    private function getDocumentPath($doc) {
        return getDocumentPath($doc['file_path'] ?? '');
    }

    /**
     * Verify document
     */
    public function verify($document_id, $verified_by) {
        $query = "UPDATE " . $this->table . " 
                  SET status = 'verified', 
                      verified_by = :verified_by, 
                      verified_at = NOW() 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':verified_by', $verified_by);
        $stmt->bindParam(':id', $document_id);
        
        if($stmt->execute()) {
            // Check if all documents for this applicant are verified
            $this->checkAllDocumentsVerified($document_id);
            return true;
        }
        return false;
    }
/**
 * Get all verified documents
 */
public function getAllVerified() {
    $query = "SELECT d.*, a.first_name, a.surname, a.application_number, a.id as applicant_id
              FROM " . $this->table . " d
              JOIN enr_applicants a ON d.applicant_id = a.id
              WHERE d.status = 'verified'
              ORDER BY d.verified_at DESC";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    
    $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Add full URLs to each document
    foreach ($documents as &$doc) {
        $doc['full_url'] = $this->getDocumentUrl($doc);
        $doc['full_path'] = $this->getDocumentPath($doc);
    }
    
    return $documents;
}

/**
 * Get all rejected documents
 */
public function getAllRejected() {
    $query = "SELECT d.*, a.first_name, a.surname, a.application_number, a.id as applicant_id
              FROM " . $this->table . " d
              JOIN enr_applicants a ON d.applicant_id = a.id
              WHERE d.status = 'rejected'
              ORDER BY d.verified_at DESC";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    
    $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Add full URLs to each document
    foreach ($documents as &$doc) {
        $doc['full_url'] = $this->getDocumentUrl($doc);
        $doc['full_path'] = $this->getDocumentPath($doc);
    }
    
    return $documents;
}
    /**
     * Reject document
     */
    public function reject($document_id, $remarks, $verified_by) {
        $query = "UPDATE " . $this->table . " 
                  SET status = 'rejected', 
                      remarks = :remarks,
                      verified_by = :verified_by, 
                      verified_at = NOW() 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':remarks', $remarks);
        $stmt->bindParam(':verified_by', $verified_by);
        $stmt->bindParam(':id', $document_id);
        return $stmt->execute();
    }

    /**
     * Verify all documents for an applicant
     */
    public function verifyAllByApplicant($applicant_id, $verified_by) {
        $query = "UPDATE " . $this->table . " 
                  SET status = 'verified', 
                      verified_by = :verified_by, 
                      verified_at = NOW() 
                  WHERE applicant_id = :applicant_id AND status = 'pending'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':verified_by', $verified_by);
        $stmt->bindParam(':applicant_id', $applicant_id);
        return $stmt->execute();
    }

    /**
     * Check if all documents are verified for an applicant
     */
    private function checkAllDocumentsVerified($document_id) {
        // Get applicant_id from document
        $query = "SELECT applicant_id FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $document_id);
        $stmt->execute();
        $doc = $stmt->fetch(PDO::FETCH_ASSOC);
        $applicant_id = $doc['applicant_id'];

        // Count pending documents
        $query = "SELECT COUNT(*) as pending FROM " . $this->table . " 
                  WHERE applicant_id = :applicant_id AND status = 'pending'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':applicant_id', $applicant_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // If no pending documents, auto-verify the applicant
        if($result['pending'] == 0) {
            require_once 'Applicant.php';
            $applicant = new Applicant();
            $applicant->verify($applicant_id);
        }
    }

    /**
     * Delete document
     */
    public function delete($id) {
        // Get document first
        $doc = $this->getById($id);
        
        if (!$doc) {
            return false;
        }
        
        // Delete from database
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        if($stmt->execute()) {
            // Delete physical file if exists
            if (isset($doc['full_path']) && file_exists($doc['full_path'])) {
                unlink($doc['full_path']);
                
                // Try to remove directory if empty
                $dir = dirname($doc['full_path']);
                if (is_dir($dir) && count(scandir($dir)) == 2) { // Only . and ..
                    rmdir($dir);
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Get document requirements
     */
    public function getRequirements() {
        $query = "SELECT * FROM enr_document_requirements 
                  WHERE is_active = 1 
                  ORDER BY sort_order ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get only pending documents for a specific applicant
     */
    public function getPendingByApplicant($applicant_id) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE applicant_id = :applicant_id 
                  AND status = 'pending'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':applicant_id', $applicant_id);
        $stmt->execute();
        
        $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Add full URLs to each document
        foreach ($documents as &$doc) {
            $doc['full_url'] = $this->getDocumentUrl($doc);
            $doc['full_path'] = $this->getDocumentPath($doc);
        }
        
        return $documents;
    }
}

?>
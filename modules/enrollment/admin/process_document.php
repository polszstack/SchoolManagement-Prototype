<?php
include_once __DIR__ . '/../../../auth/session.php';

// Set default value for admin_name if not set
$admin_name = $admin_name ?? 'Admin';
$user_role = $user_role ?? 'Administrator';
require_once '../config/database.php';
require_once '../models/Document.php';
require_once '../models/Applicant.php';

$document = new Document();
$applicant = new Applicant();

if(isset($_GET['action'])) {
    $action = $_GET['action'];
    $doc_id = $_GET['id'] ?? 0;
    
    if($action == 'verify') {
        $document->verify($doc_id, $_SESSION['user_id']);
        
        // Check if all documents are verified
        $doc_data = $document->getById($doc_id);
        $pending_docs = $document->getPendingByApplicant($doc_data['applicant_id']);
        
        if(count($pending_docs) == 0) {
            $applicant->verify($doc_data['applicant_id']);
        }
        
        header("Location: verify_documents.php?msg=verified");
        exit();
    }
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if($_POST['action'] == 'reject') {
        $document->reject($_POST['document_id'], $_POST['remarks'], $_SESSION['user_id']);
        header("Location: verify_documents.php?msg=rejected");
        exit();
    }
}
?>
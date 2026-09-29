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

// Handle verification actions
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['verify_document'])) {
        $document->verify($_POST['document_id'], $_SESSION['user_id']);
        header("Location: verify_documents.php?msg=verified");
        exit();
    } elseif(isset($_POST['reject_document'])) {
        $document->reject($_POST['document_id'], $_POST['remarks'], $_SESSION['user_id']);
        header("Location: verify_documents.php?msg=rejected");
        exit();
    } elseif(isset($_POST['verify_all'])) {
        $document->verifyAllByApplicant($_POST['applicant_id'], $_SESSION['user_id']);
        $applicant->verify($_POST['applicant_id']);
        header("Location: verify_documents.php?msg=all_verified");
        exit();
    }
}

$pending_documents = $document->getAllPending();
$page_title = 'Verify Documents';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Documents - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="stylesheet" href="../css/styles.css">
    <style>
        .content { margin-left: 250px; padding: 20px; }
        .document-preview {
            max-width: 200px;
            max-height: 150px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <?php include_once '../includes/sidebar_admin.php'; ?>
    
    <div class="content">
        <h2 class="mb-4"><i class="fas fa-check-circle"></i> Document Verification</h2>

        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php 
                    if($_GET['msg'] == 'verified') echo 'Document verified successfully!';
                    if($_GET['msg'] == 'rejected') echo 'Document rejected successfully!';
                    if($_GET['msg'] == 'all_verified') echo 'All documents verified successfully! Applicant status updated.';
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Group by Applicant -->
        <?php 
        $grouped_docs = [];
        foreach($pending_documents as $doc) {
            $grouped_docs[$doc['applicant_id']]['applicant'] = $doc['first_name'] . ' ' . $doc['surname'];
            $grouped_docs[$doc['applicant_id']]['app_number'] = $doc['application_number'];
            $grouped_docs[$doc['applicant_id']]['documents'][] = $doc;
        }
        ?>

        <?php if(empty($grouped_docs)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No pending documents for verification.
            </div>
        <?php endif; ?>

        <?php foreach($grouped_docs as $applicant_id => $group): ?>
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">
                        <i class="fas fa-user"></i> <?php echo $group['applicant']; ?>
                        <small class="text-muted">(<?php echo $group['app_number']; ?>)</small>
                    </h5>
                </div>
                <form method="POST" class="d-inline">
                    <input type="hidden" name="applicant_id" value="<?php echo $applicant_id; ?>">
                    <button type="submit" name="verify_all" class="btn btn-success btn-sm"
                            onclick="return confirm('Verify all documents for this applicant?')">
                        <i class="fas fa-check-double"></i> Verify All
                    </button>
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Document Type</th>
                                <th>File Name</th>
                                <th>Uploaded</th>
                                <th>Size</th>
                                <th>Preview</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($group['documents'] as $doc): ?>
                            <tr>
                                <td>
                                    <strong><?php echo $doc['document_type']; ?></strong>
                                </td>
                                <td>
                                    <i class="fas fa-file-<?php echo strpos($doc['mime_type'], 'pdf') !== false ? 'pdf' : 'image'; ?>"></i>
                                    <?php echo $doc['file_name']; ?>
                                </td>
                                <td><?php echo date('M d, Y h:i A', strtotime($doc['uploaded_at'])); ?></td>
                                <td><?php echo round($doc['file_size'] / 1024, 2); ?> KB</td>
                                <td>
    <a href="<?php echo $doc['full_url']; ?>" target="_blank" 
       class="btn btn-sm btn-info">
        <i class="fas fa-eye"></i> View
    </a>
</td>
                                </td>
                                <td>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="document_id" value="<?php echo $doc['id']; ?>">
                                        <button type="submit" name="verify_document" 
                                                class="btn btn-sm btn-success"
                                                onclick="return confirm('Verify this document?')">
                                            <i class="fas fa-check"></i> Verify
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            onclick="showRejectModal(<?php echo $doc['id']; ?>)">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Reject Document</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="document_id" id="reject_doc_id">
                        <div class="mb-3">
                            <label class="form-label">Remarks <span class="text-danger">*</span></label>
                            <textarea name="remarks" class="form-control" rows="4" 
                                      placeholder="Please specify the reason for rejection..." required></textarea>
                        </div>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> The applicant will be notified of this rejection and can re-upload the document.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="reject_document" class="btn btn-danger">
                            <i class="fas fa-times-circle"></i> Reject Document
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showRejectModal(docId) {
            document.getElementById('reject_doc_id').value = docId;
            new bootstrap.Modal(document.getElementById('rejectModal')).show();
        }
    </script>
</body>
</html>
<?php
include_once __DIR__ . '/../../../auth/session.php';

// Set default value for admin_name if not set
$admin_name = $admin_name ?? 'Admin';
$user_role = $user_role ?? 'Administrator';
require_once '../config/database.php';
require_once '../models/Document.php';

$document = new Document();

// Add these methods to Document.php first (see below)
$pending_documents = $document->getAllPending();
$verified_documents = $document->getAllVerified();
$rejected_documents = $document->getAllRejected();
$page_title = 'Documents';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documents - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        .content { margin-left: 250px; padding: 20px; }
        .badge-pending { background-color: #ffc107; }
        .badge-verified { background-color: #28a745; }
        .badge-rejected { background-color: #dc3545; }
        .remarks-text { 
            max-width: 250px; 
            white-space: nowrap; 
            overflow: hidden; 
            text-overflow: ellipsis; 
            cursor: help;
        }
        .table td { vertical-align: middle; }
    </style>
</head>
<body>
    <?php include_once '../includes/sidebar_admin.php'; ?>
    
    <div class="content">
        <h2 class="mb-4"><i class="fas fa-file-alt"></i> Document Management</h2>

        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php 
                    if($_GET['msg'] == 'verified') echo 'Document verified successfully!';
                    if($_GET['msg'] == 'rejected') echo 'Document rejected successfully!';
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" id="documentTabs">
                            <li class="nav-item">
                                <a class="nav-link <?php echo !isset($_GET['tab']) || $_GET['tab'] == 'pending' ? 'active' : ''; ?>" 
                                   href="?tab=pending">
                                    Pending Verification
                                    <?php if(count($pending_documents) > 0): ?>
                                    <span class="badge bg-danger"><?php echo count($pending_documents); ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo isset($_GET['tab']) && $_GET['tab'] == 'verified' ? 'active' : ''; ?>" 
                                   href="?tab=verified">
                                    Verified
                                    <?php if(count($verified_documents) > 0): ?>
                                    <span class="badge bg-success"><?php echo count($verified_documents); ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo isset($_GET['tab']) && $_GET['tab'] == 'rejected' ? 'active' : ''; ?>" 
                                   href="?tab=rejected">
                                    Rejected
                                    <?php if(count($rejected_documents) > 0): ?>
                                    <span class="badge bg-secondary"><?php echo count($rejected_documents); ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <?php 
                        $current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'pending';
                        ?>
                        
                        <!-- Pending Documents -->
                        <?php if($current_tab == 'pending'): ?>
                        <div class="tab-pane fade show active">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Applicant</th>
                                            <th>Application #</th>
                                            <th>Document Type</th>
                                            <th>File Name</th>
                                            <th>Uploaded</th>
                                            <th>Size</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(empty($pending_documents)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                <i class="fas fa-check-circle text-success fa-2x mb-2"></i><br>
                                                No pending documents for verification
                                            </td>
                                        </tr>
                                        <?php else: ?>
                                            <?php foreach($pending_documents as $doc): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($doc['first_name'] . ' ' . $doc['surname']); ?></strong>
                                                </td>
                                                <td><?php echo htmlspecialchars($doc['application_number']); ?></td>
                                                <td><?php echo htmlspecialchars($doc['document_type']); ?></td>
                                                <td>
                                                    <i class="fas fa-file-<?php echo strpos($doc['mime_type'], 'pdf') !== false ? 'pdf' : 'image'; ?>"></i>
                                                    <?php echo htmlspecialchars($doc['file_name']); ?>
                                                </td>
                                                <td><?php echo date('M d, Y h:i A', strtotime($doc['uploaded_at'])); ?></td>
                                                <td><?php echo round($doc['file_size'] / 1024, 2); ?> KB</td>
                                                <td>
                                                    <a href="<?php echo $doc['full_url']; ?>" target="_blank" 
                                                       class="btn btn-sm btn-info" title="View Document">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="process_document.php?action=verify&id=<?php echo $doc['id']; ?>" 
                                                       class="btn btn-sm btn-success" 
                                                       onclick="return confirm('Verify this document?')"
                                                       title="Verify Document">
                                                        <i class="fas fa-check"></i>
                                                    </a>
                                                    <button onclick="rejectDocument(<?php echo $doc['id']; ?>)" 
                                                            class="btn btn-sm btn-danger" title="Reject Document">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Verified Documents -->
                        <?php if($current_tab == 'verified'): ?>
                        <div class="tab-pane fade show active">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Applicant</th>
                                            <th>Application #</th>
                                            <th>Document Type</th>
                                            <th>File Name</th>
                                            <th>Uploaded</th>
                                            <th>Verified</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(empty($verified_documents)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                <i class="fas fa-folder-open fa-2x mb-2"></i><br>
                                                No verified documents yet
                                            </td>
                                        </tr>
                                        <?php else: ?>
                                            <?php foreach($verified_documents as $doc): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($doc['first_name'] . ' ' . $doc['surname']); ?></strong>
                                                </td>
                                                <td><?php echo htmlspecialchars($doc['application_number']); ?></td>
                                                <td><?php echo htmlspecialchars($doc['document_type']); ?></td>
                                                <td>
                                                    <i class="fas fa-file-<?php echo strpos($doc['mime_type'], 'pdf') !== false ? 'pdf' : 'image'; ?>"></i>
                                                    <?php echo htmlspecialchars($doc['file_name']); ?>
                                                </td>
                                                <td><?php echo date('M d, Y h:i A', strtotime($doc['uploaded_at'])); ?></td>
                                                <td><?php echo date('M d, Y h:i A', strtotime($doc['verified_at'])); ?></td>
                                                <td>
                                                    <a href="<?php echo $doc['full_url']; ?>" target="_blank" 
                                                       class="btn btn-sm btn-info" title="View Document">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Rejected Documents -->
                        <?php if($current_tab == 'rejected'): ?>
                        <div class="tab-pane fade show active">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Applicant</th>
                                            <th>Application #</th>
                                            <th>Document Type</th>
                                            <th>File Name</th>
                                            <th>Uploaded</th>
                                            <th>Rejection Reason</th>
                                            <th>Rejected On</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(empty($rejected_documents)): ?>
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">
                                                <i class="fas fa-check-circle text-success fa-2x mb-2"></i><br>
                                                No rejected documents
                                            </td>
                                        </tr>
                                        <?php else: ?>
                                            <?php foreach($rejected_documents as $doc): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($doc['first_name'] . ' ' . $doc['surname']); ?></strong>
                                                </td>
                                                <td><?php echo htmlspecialchars($doc['application_number']); ?></td>
                                                <td><?php echo htmlspecialchars($doc['document_type']); ?></td>
                                                <td>
                                                    <i class="fas fa-file-<?php echo strpos($doc['mime_type'], 'pdf') !== false ? 'pdf' : 'image'; ?>"></i>
                                                    <?php echo htmlspecialchars($doc['file_name']); ?>
                                                </td>
                                                <td><?php echo date('M d, Y h:i A', strtotime($doc['uploaded_at'])); ?></td>
                                                <td>
                                                    <span class="badge bg-danger" title="<?php echo htmlspecialchars($doc['remarks']); ?>">
                                                        <i class="fas fa-exclamation-circle"></i> 
                                                        <?php 
                                                            $remarks = htmlspecialchars($doc['remarks']);
                                                            echo strlen($remarks) > 30 ? substr($remarks, 0, 30) . '...' : $remarks;
                                                        ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('M d, Y h:i A', strtotime($doc['verified_at'])); ?></td>
                                                <td>
                                                    <a href="<?php echo $doc['full_url']; ?>" target="_blank" 
                                                       class="btn btn-sm btn-info" title="View Document">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="process_document.php">
                    <div class="modal-header">
                        <h5 class="modal-title">Reject Document</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="document_id" id="reject_doc_id">
                        <input type="hidden" name="action" value="reject">
                        <div class="mb-3">
                            <label class="form-label">Remarks <span class="text-danger">*</span></label>
                            <textarea name="remarks" class="form-control" rows="4" required 
                                      placeholder="Please specify reason for rejection (will be visible to applicant)"></textarea>
                            <small class="text-muted">The applicant will be notified of this rejection reason.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-times-circle"></i> Reject Document
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function rejectDocument(id) {
            document.getElementById('reject_doc_id').value = id;
            new bootstrap.Modal(document.getElementById('rejectModal')).show();
        }
    </script>
</body>
</html>
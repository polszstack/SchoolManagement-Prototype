<?php
// Include the session file (now modified to bypass auth)
include_once __DIR__ . '/../../../auth/session.php';

// Set default value for admin_name if not set
$admin_name = $admin_name ?? 'Admin';
$user_role = $user_role ?? 'Administrator';

require_once '../config/database.php';
require_once '../models/Applicant.php';
require_once '../models/Student.php';
require_once '../models/Course.php';
require_once '../models/Report.php';

$applicant = new Applicant();
$page_title = 'Applications';

// Handle status filter
$status_filter = $_GET['status'] ?? 'all';
if($status_filter == 'all') {
    $applications = $applicant->getRecent(100);
} else {
    $applications = $applicant->getByStatus($status_filter);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applications - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/styles.css">
    <title><?php echo $pageTitle ?? 'School Management System'; ?></title>
    
    <!-- Add your main script.js file -->
    <script src="../js/script.js" type="module"></script>
    <style>
        .content { margin-left: 250px; padding: 20px; margin-top: 45px; }
    </style>
</head>
<header>
    <div class="hamburger">
      <span></span>
      <span></span>
      <span></span>
    </div>
    <div class="realtime" id="realtimeClock" aria-live="polite">--:-- </div>
</header>
<body>
    <?php include_once '../includes/sidebar_admin.php'; ?>
    
    <div class="content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-file-signature"></i> Applications</h2>
            <a href="reports.php?type=applications" class="btn btn-success">
                <i class="fas fa-download"></i> Export Report
            </a>
        </div>

        <!-- Status Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="btn-group">
                    <a href="?status=all" class="btn btn-outline-secondary <?php echo $status_filter == 'all' ? 'active' : ''; ?>">
                        All
                    </a>
                    <a href="?status=pending" class="btn btn-outline-warning <?php echo $status_filter == 'pending' ? 'active' : ''; ?>">
                        Pending
                    </a>
                    <a href="?status=verified" class="btn btn-outline-info <?php echo $status_filter == 'verified' ? 'active' : ''; ?>">
                        Verified
                    </a>
                    <a href="?status=converted" class="btn btn-outline-success <?php echo $status_filter == 'converted' ? 'active' : ''; ?>">
                        Converted
                    </a>
                    <a href="?status=rejected" class="btn btn-outline-danger <?php echo $status_filter == 'rejected' ? 'active' : ''; ?>">
                        Rejected
                    </a>
                </div>
            </div>
        </div>

        <!-- Applications Table -->
        <div class="card">
            <div class="card-header">
                <h5>Application List</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="applicationsTable">
                        <thead>
                            <tr>
                                <th>Application #</th>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>Course</th>
                                <th>Submitted</th>
                                <th>Documents</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($applications as $app): ?>
                            <tr>
                                <td><strong><?php echo $app['application_number']; ?></strong></td>
                                <td><?php echo $app['first_name'] . ' ' . $app['surname']; ?></td>
                                <td>
                                    <small>
                                        <i class="fas fa-phone"></i> <?php echo $app['contact_number']; ?><br>
                                        <i class="fas fa-envelope"></i> <?php echo $app['email']; ?>
                                    </small>
                                </td>
                                <td><?php echo $app['course_code'] ?? 'Not selected'; ?></td>
                                <td><?php echo date('M d, Y', strtotime($app['submitted_at'])); ?></td>
                                <td>
                                    <span class="badge bg-info"><?php echo $applicant->countDocuments($app['id']); ?> files</span>
                                </td>
                                <td>
                                    <?php
                                    $badge_color = [
                                        'pending' => 'warning',
                                        'verified' => 'info',
                                        'converted' => 'success',
                                        'rejected' => 'danger'
                                    ];
                                    ?>
                                    <span class="badge bg-<?php echo $badge_color[$app['status']]; ?>">
                                        <?php echo ucfirst($app['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="view_application.php?id=<?php echo $app['id']; ?>" 
                                       class="btn btn-sm btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if($app['status'] == 'pending'): ?>
                                    <a href="verify_documents.php?applicant_id=<?php echo $app['id']; ?>" 
                                       class="btn btn-sm btn-success" title="Verify">
                                        <i class="fas fa-check-circle"></i>
                                    </a>
                                    <?php endif; ?>
                                    <?php if($app['status'] == 'verified'): ?>
                                    <a href="convert_student.php?applicant_id=<?php echo $app['id']; ?>" 
                                       class="btn btn-sm btn-primary" title="Convert">
                                        <i class="fas fa-user-graduate"></i>
                                    </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
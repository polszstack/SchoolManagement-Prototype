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
$student = new Student();
$course = new Course();
$report = new Report();

$total_applications = $applicant->getTotalThisYear();
$total_students = $student->getTotalStudents();
$pending_verification = $applicant->getPendingCount();
$popular_courses = $course->getMostSelected(5);
$recent_applications = $applicant->getRecent(5);
$stats = $report->getApplicationStats();
$page_title = 'Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Enrollment System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/pages/admindashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            <h2><i class="fas fa-tachometer-alt"></i> Dashboard</h2>
            <div>
                <span class="badge bg-primary p-3">
                    <i class="fas fa-calendar"></i> <?php echo date('F d, Y'); ?>
                </span>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="stat-card bg-gradient-primary">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-uppercase">Total Applications</h6>
                            <h2 class="mb-0"><?php echo $total_applications; ?></h2>
                            <small>This Year</small>
                        </div>
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="mt-3">
                        <small class="opacity-75">
                            <i class="fas fa-clock"></i> Pending: <?php echo $stats['pending'] ?? 0; ?>
                        </small>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card bg-gradient-success">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-uppercase">Total Students</h6>
                            <h2 class="mb-0"><?php echo $total_students; ?></h2>
                            <small>Enrolled</small>
                        </div>
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="mt-3">
                        <small class="opacity-75">
                            <i class="fas fa-check-circle"></i> Converted: <?php echo $stats['converted'] ?? 0; ?>
                        </small>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card bg-gradient-warning">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-uppercase">Pending Verification</h6>
                            <h2 class="mb-0"><?php echo $pending_verification; ?></h2>
                            <small>Awaiting Review</small>
                        </div>
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="mt-3">
                        <small class="opacity-75">
                            <i class="fas fa-file-alt"></i> Documents to check
                        </small>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card bg-gradient-info">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-uppercase">Active Courses</h6>
                            <h2 class="mb-0"><?php echo $course->getTotalActive(); ?></h2>
                            <small>Programs Offered</small>
                        </div>
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="mt-3">
                        <small class="opacity-75">
                            <i class="fas fa-trending-up"></i> Most selected: <?php echo $popular_courses[0]['course_code'] ?? 'N/A'; ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Row -->
        <div class="row">
            <!-- Recent Applications -->
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h5 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-file-signature"></i> Recent Applications
                        </h5>
                        <a href="applications.php" class="btn btn-sm btn-primary">View All</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Application #</th>
                                        <th>Name</th>
                                        <th>Course</th>
                                        <th>Submitted</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($recent_applications as $app): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo $app['application_number']; ?></strong>
                                        </td>
                                        <td><?php echo $app['first_name'] . ' ' . $app['surname']; ?></td>
                                        <td>
                                            <?php if($app['course_code']): ?>
                                                <span class="badge bg-info"><?php echo $app['course_code']; ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Not selected</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small><?php echo date('M d, Y', strtotime($app['submitted_at'])); ?></small>
                                        </td>
                                        <td>
                                            <?php 
                                            $status_class = [
                                                'pending' => 'warning',
                                                'verified' => 'info',
                                                'converted' => 'success',
                                                'rejected' => 'danger'
                                            ];
                                            $status = $app['status'];
                                            ?>
                                            <span class="badge bg-<?php echo $status_class[$status] ?? 'secondary'; ?> badge-status">
                                                <i class="fas fa-<?php echo $status == 'pending' ? 'clock' : ($status == 'verified' ? 'check-circle' : ($status == 'converted' ? 'user-graduate' : 'times-circle')); ?>"></i>
                                                <?php echo ucfirst($status); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="view_application.php?id=<?php echo $app['id']; ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="verify_documents.php?applicant_id=<?php echo $app['id']; ?>" 
                                               class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-check-circle"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-lg-4">
                <!-- Course Chart Card -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h5 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-chart-pie"></i> Most Selected Courses
                        </h5>
                        <button onclick="exportCourseChart()" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-download"></i> Export
                        </button>
                    </div>
                    <div class="card-body">
                        <canvas id="courseChart" 
                                data-labels='<?php echo json_encode(array_column($popular_courses, 'course_code')); ?>'
                                data-values='<?php echo json_encode(array_column($popular_courses, 'count')); ?>'
                                height="250">
                        </canvas>
                        
                        <!-- Course list below chart -->
                        <div class="mt-3">
                            <?php foreach($popular_courses as $index => $course): ?>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>
                                    <span class="badge" style="background-color: <?php 
                                        $colors = ['#3498db', '#e74c3c', '#2ecc71', '#f39c12', '#9b59b6'];
                                        echo $colors[$index % count($colors)];
                                    ?>; color: white; width: 20px; height: 20px; border-radius: 4px; display: inline-block; margin-right: 8px;"></span>
                                    <?php echo $course['course_code']; ?>
                                </span>
                                <span class="badge bg-secondary"><?php echo $course['count'] ?? 0; ?> selections</span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h5 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-bolt"></i> Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="verify_documents.php" class="btn btn-outline-primary">
                                <i class="fas fa-file-alt"></i> Verify Documents
                                <?php if($pending_verification > 0): ?>
                                <span class="badge bg-danger float-end"><?php echo $pending_verification; ?></span>
                                <?php endif; ?>
                            </a>
                            <a href="convert_student.php" class="btn btn-outline-success">
                                <i class="fas fa-user-graduate"></i> Convert to Student
                            </a>
                            <a href="reports.php" class="btn btn-outline-info">
                                <i class="fas fa-chart-bar"></i> Generate Reports
                            </a>
                            <a href="courses.php" class="btn btn-outline-warning">
                                <i class="fas fa-book"></i> Manage Courses
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
    // Initialize chart when document is ready
    document.addEventListener('DOMContentLoaded', function() {
        // Let the external JS handle the chart
        // Just define the export function here
    });

    function exportCourseChart() {
        const canvas = document.getElementById('courseChart');
        if (!canvas) {
            alert('Course chart not found');
            return;
        }
        
        const link = document.createElement('a');
        link.download = 'course-selections-chart.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
    }
</script>
    
                                </body>
</html>
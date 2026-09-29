<?php
include_once __DIR__ . '/../../../auth/session.php';

// Set default value for admin_name if not set
$admin_name = $admin_name ?? 'Admin';
$user_role = $user_role ?? 'Administrator';
require_once '../config/database.php';
require_once '../models/Report.php';
require_once '../models/Applicant.php';
require_once '../models/Student.php';

$report = new Report();
$applicant = new Applicant();
$student = new Student();

$year = $_GET['year'] ?? date('Y');
$application_stats = $report->getApplicationStats($year);
$monthly_applications = $report->getMonthlyApplications($year);
$course_enrollment = $report->getCourseEnrollmentReport();
$conversion_rate = $report->getConversionRate($year);
$demographics = $report->getDemographicReport();
$page_title = 'Reports';

// Debug output - remove in production
// echo '<pre>'; print_r($monthly_applications); echo '</pre>';
// echo '<pre>'; print_r($demographics); echo '</pre>';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/pages/admin-reports.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title><?php echo $pageTitle ?? 'School Management System'; ?></title>
    <style>
        .content { margin-left: 250px; padding: 20px; margin-top: 45px; }
        .report-card { padding: 20px; border-radius: 10px; margin-bottom: 20px; }
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
            <h2><i class="fas fa-chart-bar"></i> Reports & Analytics</h2>
            <div>
                <select class="form-select d-inline-block w-auto" onchange="window.location.href='?year='+this.value">
                    <?php for($y = date('Y'); $y >= date('Y')-5; $y--): ?>
                    <option value="<?php echo $y; ?>" <?php echo $y == $year ? 'selected' : ''; ?>>
                        Year <?php echo $y; ?>
                    </option>
                    <?php endfor; ?>
                </select>
                <button class="btn btn-success" onclick="window.print()">
                    <i class="fas fa-print"></i> Print Report
                </button>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="report-card bg-primary text-white">
                    <h6>Total Applications</h6>
                    <h2><?php echo $application_stats['total_applications'] ?? 0; ?></h2>
                    <small>Year <?php echo $year; ?></small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="report-card bg-success text-white">
                    <h6>Enrolled Students</h6>
                    <h2><?php echo $conversion_rate['total_enrolled'] ?? 0; ?></h2>
                    <small>Converted this year</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="report-card bg-info text-white">
                    <h6>Conversion Rate</h6>
                    <h2><?php echo $conversion_rate['conversion_rate'] ?? 0; ?>%</h2>
                    <small>Applicants to Students</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="report-card bg-warning text-white">
                    <h6>Pending Review</h6>
                    <h2><?php echo $application_stats['pending'] ?? 0; ?></h2>
                    <small>Awaiting verification</small>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Monthly Applications (<?php echo $year; ?>)</h5>
                    </div>
                    <div class="card-body">
                        <?php if(empty($monthly_applications)): ?>
                            <div class="alert alert-info">No monthly application data available for <?php echo $year; ?></div>
                        <?php endif; ?>
                        <canvas id="monthlyChart" 
                                data-monthly='<?php echo json_encode($monthly_applications); ?>'
                                data-year="<?php echo $year; ?>" 
                                height="300"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Application Status</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $pending = $application_stats['pending'] ?? 0;
                        $verified = $application_stats['verified'] ?? 0;
                        $converted = $application_stats['converted'] ?? 0;
                        $rejected = $application_stats['rejected'] ?? 0;
                        ?>
                        <?php if($pending + $verified + $converted + $rejected == 0): ?>
                            <div class="alert alert-info">No status data available</div>
                        <?php endif; ?>
                        <canvas id="statusChart"
                                data-pending="<?php echo $pending; ?>"
                                data-verified="<?php echo $verified; ?>"
                                data-converted="<?php echo $converted; ?>"
                                data-rejected="<?php echo $rejected; ?>"
                                height="250"></canvas>
                        <div class="mt-3">
                            <div class="d-flex justify-content-between">
                                <span><span class="badge bg-warning">Pending</span></span>
                                <span><?php echo $pending; ?></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span><span class="badge bg-info">Verified</span></span>
                                <span><?php echo $verified; ?></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span><span class="badge bg-success">Converted</span></span>
                                <span><?php echo $converted; ?></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span><span class="badge bg-danger">Rejected</span></span>
                                <span><?php echo $rejected; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Enrollment Report -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Course Enrollment Report</h5>
            </div>
            <div class="card-body">
                <?php if(empty($course_enrollment)): ?>
                    <div class="alert alert-info">No course enrollment data available</div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Course Code</th>
                                <th>Course Name</th>
                                <th>Enrolled Students</th>
                                <th>Pending Applications</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($course_enrollment as $course): ?>
                            <tr>
                                <td><strong><?php echo $course['course_code'] ?? ''; ?></strong></td>
                                <td><?php echo $course['course_name'] ?? ''; ?></td>
                                <td>
                                    <span class="badge bg-success"><?php echo $course['enrolled_students'] ?? 0; ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-warning"><?php echo $course['pending_applications'] ?? 0; ?></span>
                                </td>
                                <td>
                                    <?php echo ($course['enrolled_students'] ?? 0) + ($course['pending_applications'] ?? 0); ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Demographics Row -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Applicant Demographics</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        // Extract gender data from demographics
                        $male = 0;
                        $female = 0;
                        $other = 0;
                        
                        if (!empty($demographics['gender']) && is_array($demographics['gender'])) {
                            foreach ($demographics['gender'] as $item) {
                                $gender = strtolower($item['gender'] ?? '');
                                if ($gender == 'male') $male = (int)($item['count'] ?? 0);
                                elseif ($gender == 'female') $female = (int)($item['count'] ?? 0);
                                else $other += (int)($item['count'] ?? 0);
                            }
                        }
                        
                        $total_gender = $male + $female + $other;
                        ?>
                        <?php if($total_gender == 0): ?>
                            <div class="alert alert-info">No gender data available</div>
                        <?php endif; ?>
                        <canvas id="genderChart"
                                data-male="<?php echo $male; ?>"
                                data-female="<?php echo $female; ?>"
                                data-other="<?php echo $other; ?>"
                                height="250"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Civil Status Distribution</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        // Extract civil status data from demographics
                        $single = 0;
                        $married = 0;
                        $widowed = 0;
                        $separated = 0;
                        $others = 0;
                        
                        if (!empty($demographics['civil_status']) && is_array($demographics['civil_status'])) {
                            foreach ($demographics['civil_status'] as $item) {
                                $status = strtolower($item['civil_status'] ?? '');
                                if ($status == 'single') $single = (int)($item['count'] ?? 0);
                                elseif ($status == 'married') $married = (int)($item['count'] ?? 0);
                                elseif ($status == 'widowed') $widowed = (int)($item['count'] ?? 0);
                                elseif ($status == 'separated' || $status == 'divorced') $separated = (int)($item['count'] ?? 0);
                                else $others += (int)($item['count'] ?? 0);
                            }
                        }
                        
                        $total_civil = $single + $married + $widowed + $separated + $others;
                        ?>
                        <?php if($total_civil == 0): ?>
                            <div class="alert alert-info">No civil status data available</div>
                        <?php endif; ?>
                        <canvas id="civilStatusChart"
                                data-single="<?php echo $single; ?>"
                                data-married="<?php echo $married; ?>"
                                data-widowed="<?php echo $widowed; ?>"
                                data-separated="<?php echo $separated; ?>"
                                data-others="<?php echo $others; ?>"
                                height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/pages/admin-reports.js"></script>
</body>
</html>
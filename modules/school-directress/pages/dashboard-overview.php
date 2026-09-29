<?php
include_once __DIR__ . "/../../../auth/session.php";
include_once __DIR__ . "/../classes/Employee.php";
include_once __DIR__ . "/../classes/Overview.php";
include_once __DIR__ . "/../classes/StudentMonitoring.php";
include_once __DIR__ . "/../../../database/db.php";

$employeeClass = new Employee();
$employeeName  = $employeeClass->getEmployeeName();

$studentClass = new StudentMonitoring();
$totalStudents = count($studentClass->getStudents());

$database = new Database();
$conn     = $database->getConnection();

?>


<!-- Module Header -->
<div class="module-header">
    <h1>School Directress Dashboard</h1>
    <p>Here's an overview of your dashboard and recent activities.</p>
</div>

<!-- Welcome Banner -->
<div class="welcome-banner">
    <div class="welcome-text">
        <h2>Welcome back, <?= htmlspecialchars($employeeName) ?>!</h2>
        <p>Here's a quick snapshot of the school's current status.</p>
    </div>
    <div class="welcome-date">
        <div class="date-day"><?= date('d') ?></div>
        <div class="date-month"><?= date('F Y') ?></div>
    </div>
</div>
    
<!-- Row 1: Enrollment & Personnel -->
<p class="cards-section-label">Enrollment &amp; Personnel</p>
<div class="summary-cards-grid">

    <div class="summary-card card-accent-primary">
        <div class="card-value"><?= number_format($totalStudents) ?></div>
        <div class="card-label">Total Enrolled Students</div>
    </div>

    <div class="summary-card card-accent-secondary">
        <div class="card-value"><?= number_format($pendingApplicants) ?></div>
        <div class="card-label">Pending Applicants</div>
    </div>

    <div class="summary-card card-accent-muted">
        <div class="card-value"><?= number_format($activeEmployees) ?></div>
        <div class="card-label">Active Employees</div>
    </div>

    <div class="summary-card card-accent-primary">
        <div class="card-value"><?= number_format($todayClinic) ?></div>
        <div class="card-label">Clinic Visits Today</div>
    </div>

</div>

<!-- Row 2: Reports & Operations -->
<p class="cards-section-label">Reports &amp; Operations</p>
<div class="summary-cards-grid">

    <div class="summary-card card-accent-primary">
        <div class="card-value"><?= number_format($pendingReports) ?></div>
        <div class="card-label">Pending Reports</div>
    </div>

    <div class="summary-card card-accent-secondary">
        <div class="card-value"><?= number_format($pendingApprovals) ?></div>
        <div class="card-label">Pending Approvals</div>
    </div>

    <div class="summary-card card-accent-muted">
        <div class="card-value"><?= number_format($openIssues) ?></div>
        <div class="card-label">Open Department Issues</div>
    </div>

    <div class="summary-card card-accent-primary">
        <div class="card-value"><?= number_format($overdueBooks) ?></div>
        <div class="card-label">Overdue Library Books</div>
    </div>

</div>
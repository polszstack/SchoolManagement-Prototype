
<div class="sidebar">
    <div class="school-logo">
        <img src="/sms/assets/bcp-logo.png" alt="School Logo">
        <div class="sidebar-icons">
            <i class="fas fa-bell"></i>
            <i class="fas fa-envelope"></i>
        </div>
    </div>

    <div class="sidebar-header">
        <div class="user_avatar">
            <i class="fas fa-user-circle"></i>
        </div>
        <h1>ADMIN SYSTEM</h1>
        <div class="employee_name"><?php echo htmlspecialchars($admin_name); ?></div>
        <div class="employee_id"><?php echo htmlspecialchars($_SESSION['employee_id'] ?? ''); ?></div>
    </div>

    <div class="separator"></div>

    <h2>MAIN MENU</h2>

    <ul>
        <li>
            <a href="dashboard.php" class="menu-link <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="applications.php" class="menu-link <?php echo $current_page == 'applications.php' ? 'active' : ''; ?>">
                <i class="fas fa-file-signature"></i> Applications
            </a>
        </li>
        <li>
            <a href="documents.php" class="menu-link <?php echo $current_page == 'documents.php' ? 'active' : ''; ?>">
                <i class="fas fa-file-alt"></i> Documents
            </a>
        </li>
        <li>
            <a href="students.php" class="menu-link <?php echo $current_page == 'students.php' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i> Students
            </a>
        </li>
        <li>
            <a href="courses.php" class="menu-link <?php echo $current_page == 'courses.php' ? 'active' : ''; ?>">
                <i class="fas fa-book"></i> Courses
            </a>
        </li>
        <li>
            <a href="sections.php" class="menu-link <?php echo $current_page == 'sections.php' ? 'active' : ''; ?>">
                <i class="fas fa-book"></i> Sections
            </a>
        </li>
        <li>
            <a href="reports.php" class="menu-link <?php echo $current_page == 'reports.php' ? 'active' : ''; ?>">
                <i class="fas fa-chart-bar"></i> Reports
            </a>
        </li>
    </ul>

    <div class="separator"></div>

    <h3>VERIFICATION</h3>

    <ul>
        <li>
            <a href="verify_documents.php" class="menu-link <?php echo $current_page == 'verify_documents.php' ? 'active' : ''; ?>">
                <i class="fas fa-check-circle"></i> Verify Documents
            </a>
        </li>
    </ul>
    
    <div class="separator"></div>

    <h3>REQUEST AND CONCERN</h3>

    <ul>
        <li>
            <a href="approval-submission.php" class="menu-link <?php echo $current_page == 'approval-submission.php' ? 'active' : ''; ?>">
                <i class="fas fa-check-circle"></i> Approval Submission
            </a>
        </li>
        <li>
            <a href="concern-submission.php" class="menu-link <?php echo $current_page == 'concern-submission.php' ? 'active' : ''; ?>">
                <i class="fas fa-check-circle"></i> Concern Submission
            </a>
        </li>
        <li>
            <a href="report-submission.php" class="menu-link <?php echo $current_page == 'report-submission.php' ? 'active' : ''; ?>">
                <i class="fas fa-check-circle"></i> Report Submission
            </a>
        </li>
    </ul>

    <div class="separator"></div>

    <ul>
        <li>
            <a href="/sms/auth/logout.php" class="menu-link">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</div>

<!-- Overlay for mobile -->
<div class="sidebar-overlay"></div>
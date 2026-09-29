<div class="sidebar">
    <div class="p-3">
        <h4><i class="fas fa-graduation-cap"></i> Applicant Portal</h4>
        <hr class="bg-light">
        <div class="user-info text-white">
            <i class="fas fa-user-circle fa-2x"></i>
            <p class="mt-2"><?php echo $_SESSION['username']; ?></p>
            <small>Applicant</small>
        </div>
    </div>
    <nav>
        <a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="application_form.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'application_form.php' ? 'active' : ''; ?>">
            <i class="fas fa-file-alt"></i> Application Form
        </a>
        <a href="course_selection.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'course_selection.php' ? 'active' : ''; ?>">
            <i class="fas fa-book"></i> Course Selection
        </a>
        <a href="documents_upload.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'documents_upload.php' ? 'active' : ''; ?>">
            <i class="fas fa-upload"></i> Documents Upload
        </a>
        <a href="announcements.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'announcements.php' ? 'active' : ''; ?>">
            <i class="fas fa-bullhorn"></i> Announcements
        </a>
        <a href="../logout.php">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </nav>
</div>

<style>
.sidebar {
    min-height: 100vh;
    background: #2c3e50;
    color: white;
    position: fixed;
    width: 250px;
    left: 0;
    top: 0;
}
.sidebar a {
    color: white;
    text-decoration: none;
    padding: 12px 20px;
    display: block;
    transition: 0.3s;
}
.sidebar a:hover {
    background: #34495e;
}
.sidebar .active {
    background: #3498db;
}
.content {
    margin-left: 250px;
    padding: 20px;
}
.user-info {
    text-align: center;
    padding: 10px;
    background: #34495e;
    border-radius: 5px;
}
</style>
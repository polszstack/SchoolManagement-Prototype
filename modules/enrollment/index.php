<?php
session_start();

// Check if user is logged in
if(isset($_SESSION['user_id']) && isset($_SESSION['user_type'])) {
    // Only allow admin access
    if($_SESSION['user_type'] == 'admin') {
        header("Location: admin/dashboard.php");
        exit();
    } 
    // If not admin, redirect to appropriate dashboard or logout
    else {
        // Clear session and redirect to login
        session_destroy();
        header("Location: admin/dashboard.php?error=unauthorized");
        exit();
    }
} 
// If not logged in, redirect to login page
else {
    header("Location: admin/dashboard.php");
    exit();
}
?>
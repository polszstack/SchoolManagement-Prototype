<?php
session_start();

function checkAuth() {
    if(!isset($_SESSION['user_id'])) {
        header("Location: ../login.php");
        exit();
    }
}

function checkAdmin() {
    checkAuth();
    if($_SESSION['user_type'] != 'admin') {
        header("Location: ../index.php");
        exit();
    }
}

function checkApplicant() {
    checkAuth();
    if($_SESSION['user_type'] != 'applicant') {
        header("Location: ../admin/dashboard.php");
        exit();
    }
}

function checkStudent() {
    checkAuth();
    if($_SESSION['user_type'] != 'student') {
        header("Location: ../login.php");
        exit();
    }
}
?>
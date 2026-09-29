<?php

include_once __DIR__ . "/../../../database/db.php";

$database = new Database();
$conn     = $database->getConnection();     


$totalStudents     = $conn->query("SELECT COUNT(*) FROM enr_students WHERE enrollment_status = 'enrolled'")->fetchColumn();
$pendingApplicants = $conn->query("SELECT COUNT(*) FROM enr_applicants WHERE status = 'pending'")->fetchColumn();
$activeEmployees   = $conn->query("SELECT COUNT(*) FROM sms_employee WHERE status = 'active'")->fetchColumn();
$pendingReports    = $conn->query("SELECT COUNT(*) FROM sd_reports WHERE status = 'Pending'")->fetchColumn();
$openIssues        = $conn->query("SELECT COUNT(*) FROM sd_issues WHERE status = 'open'")->fetchColumn();
$pendingApprovals  = $conn->query("SELECT COUNT(*) FROM sd_approvals WHERE decision = 'Pending'")->fetchColumn();
$overdueBooks      = $conn->query("SELECT COUNT(*) FROM lbr_transactions WHERE status = 'overdue'")->fetchColumn();
$todayClinic       = $conn->query("SELECT COUNT(*) FROM cln_clinic_visits WHERE DATE(visit_date) = CURDATE()")->fetchColumn();
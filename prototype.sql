-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 29, 2026 at 05:48 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `prototype`
--

-- --------------------------------------------------------

--
-- Table structure for table `cc_schedule`
--

CREATE TABLE `cc_schedule` (
  `id` int(11) NOT NULL,
  `faculty_name` varchar(150) NOT NULL,
  `subject_name` varchar(150) NOT NULL,
  `section_name` varchar(100) NOT NULL,
  `room_name` varchar(100) DEFAULT NULL,
  `schedule_type` enum('Class','Break Time') DEFAULT 'Class',
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `status` enum('Scheduled','Completed','Cancelled') DEFAULT 'Scheduled',
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `schedule_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cc_schedule`
--

INSERT INTO `cc_schedule` (`id`, `faculty_name`, `subject_name`, `section_name`, `room_name`, `schedule_type`, `start_time`, `end_time`, `status`, `day_of_week`, `schedule_date`, `created_at`, `updated_at`) VALUES
(1, 'Dr. Alice Smith', 'Data Structures', 'CS-2A', 'Room 301', 'Class', '08:00:00', '09:30:00', 'Completed', 'Monday', '2026-08-10', '2026-08-09 11:50:33', '2026-08-09 11:50:33'),
(2, 'Dr. Alice Smith', 'Break', 'CS-3B', 'Room 303', 'Break Time', '09:30:00', '10:00:00', 'Completed', 'Monday', '2026-08-10', '2026-08-09 11:50:33', '2026-08-09 12:39:34'),
(3, 'Prof. Bob Jones', 'Calculus I', 'MATH-1B', 'Room 105', 'Class', '10:00:00', '11:30:00', 'Completed', 'Monday', '2026-08-10', '2026-08-09 11:50:33', '2026-08-09 11:50:33'),
(4, 'Mr. Charles Lee', 'Database Management', 'CS-3B', 'Lab 1', 'Class', '13:00:00', '15:00:00', 'Scheduled', 'Monday', '2026-08-10', '2026-08-09 11:50:33', '2026-08-09 11:50:33'),
(5, 'Dr. Sarah Connor', 'Physics 101', 'PHY-1A', 'Room 202', 'Class', '09:00:00', '11:00:00', 'Scheduled', 'Tuesday', '2026-08-11', '2026-08-09 11:50:33', '2026-08-09 11:50:33'),
(6, 'Dr. Sarah Connor', 'Lunch Break', 'N/A', 'Faculty Lounge', 'Break Time', '11:00:00', '12:00:00', 'Scheduled', 'Tuesday', '2026-08-11', '2026-08-09 11:50:33', '2026-08-09 11:50:33'),
(7, 'Prof. Alan Turing', 'Discrete Mathematics', 'CS-1A', 'Room 305', 'Class', '12:00:00', '13:30:00', 'Cancelled', 'Tuesday', '2026-08-11', '2026-08-09 11:50:33', '2026-08-09 11:50:33'),
(8, 'Dr. Alice Smith', 'Algorithms', 'CS-2A', 'Room 301', 'Class', '08:00:00', '10:00:00', 'Scheduled', 'Wednesday', '2026-08-12', '2026-08-09 11:50:33', '2026-08-09 11:50:33'),
(9, 'Mr. Charles Lee', 'Web Development', 'CS-3A', 'Lab 2', 'Class', '10:30:00', '12:30:00', 'Scheduled', 'Wednesday', '2026-08-12', '2026-08-09 11:50:33', '2026-08-09 11:50:33'),
(10, 'Mr. Charles Lee', 'Coffee Break', 'N/A', NULL, 'Break Time', '12:30:00', '13:00:00', 'Scheduled', 'Wednesday', '2026-08-12', '2026-08-09 11:50:33', '2026-08-09 11:50:33'),
(11, 'Prof. Bob Jones', 'Linear Algebra', 'MATH-2A', 'Room 105', 'Class', '07:30:00', '09:00:00', 'Scheduled', 'Thursday', '2026-08-13', '2026-08-09 11:50:33', '2026-08-09 11:50:33'),
(12, 'Dr. Sarah Connor', 'Quantum Mechanics', 'PHY-4A', 'Room 205', 'Class', '09:30:00', '12:30:00', 'Scheduled', 'Thursday', '2026-08-13', '2026-08-09 11:50:33', '2026-08-09 11:50:33'),
(13, 'Prof. Alan Turing', 'Machine Learning', 'CS-4C', 'Lab 1', 'Class', '13:30:00', '16:30:00', 'Scheduled', 'Thursday', '2026-08-13', '2026-08-09 11:50:33', '2026-08-09 11:50:33'),
(14, 'Dr. Alice Smith', 'Data Structures', 'CS-2B', 'Room 302', 'Class', '08:00:00', '09:30:00', 'Scheduled', 'Friday', '2026-08-14', '2026-08-09 11:50:33', '2026-08-09 11:50:33'),
(15, 'Prof. Bob Jones', 'Faculty Meeting', 'All', 'Conference Room', 'Break Time', '10:00:00', '12:00:00', 'Scheduled', 'Friday', '2026-08-14', '2026-08-09 11:50:33', '2026-08-09 11:50:33'),
(16, 'Mr. Charles Lee', 'Database Management', 'CS-3B', 'Lab 1', 'Class', '13:00:00', '15:00:00', 'Scheduled', 'Friday', '2026-08-14', '2026-08-09 11:50:33', '2026-08-09 11:50:33'),
(17, 'Prof. Alan Turing', 'Software Engineering', 'CS-3A', 'Lab 3', 'Class', '09:00:00', '12:00:00', 'Scheduled', 'Saturday', '2026-08-15', '2026-08-09 11:50:33', '2026-08-09 11:50:33'),
(18, 'Prof. Alan Turing', 'Lunch Break', 'N/A', NULL, 'Break Time', '12:00:00', '13:00:00', 'Scheduled', 'Saturday', '2026-08-15', '2026-08-09 11:50:33', '2026-08-09 11:50:33'),
(19, 'Dr. Sarah Connor', 'Physics Lab', 'PHY-1B', 'Lab 4', 'Class', '13:00:00', '16:00:00', 'Cancelled', 'Saturday', '2026-08-15', '2026-08-09 11:50:33', '2026-08-09 11:50:33'),
(20, 'Dr. Alice Smith', 'Capstone Project', 'CS-4A', 'Room 401', 'Class', '16:00:00', '18:00:00', 'Scheduled', 'Saturday', '2026-08-15', '2026-08-09 11:50:33', '2026-08-09 11:50:33'),
(21, 'Dr. Alice Smith', 'Remedial Data Structures', 'CS-2A', 'Room 301', 'Class', '09:00:00', '11:00:00', 'Scheduled', 'Sunday', '2026-08-16', '2026-08-09 11:57:36', '2026-08-09 11:57:36'),
(22, 'Dr. Alice Smith', 'Break', 'N/A', NULL, 'Break Time', '11:00:00', '11:30:00', 'Scheduled', 'Sunday', '2026-08-16', '2026-08-09 11:57:36', '2026-08-09 11:57:36'),
(23, 'Mr. Charles Lee', 'Web Dev Bootcamp', 'Open Section', 'Lab 1', 'Class', '13:00:00', '16:00:00', 'Scheduled', 'Sunday', '2026-08-16', '2026-08-09 11:57:36', '2026-08-09 11:57:36'),
(38, 'Prof. Maria Santos', 'Communication Skills', 'BSIT 1A', 'Room 101', 'Class', '08:00:00', '10:00:00', 'Scheduled', 'Friday', '2026-08-14', '2026-08-09 11:13:46', '2026-08-09 11:13:46'),
(39, 'Dr. Juan Cruz', 'Discrete Math', 'BSIT 2A', 'Room 201', 'Class', '10:00:00', '12:00:00', 'Scheduled', 'Friday', '2026-08-14', '2026-08-09 11:13:46', '2026-08-09 11:13:46'),
(40, 'Break', 'Break Time', 'All Sections', 'Cafeteria', 'Break Time', '12:00:00', '13:00:00', 'Scheduled', 'Friday', '2026-08-14', '2026-08-09 11:13:46', '2026-08-09 11:13:46'),
(41, 'Prof. Ana Garcia', 'Networking', 'BSIT 2B', 'Room 202', 'Class', '13:00:00', '15:00:00', 'Scheduled', 'Friday', '2026-08-14', '2026-08-09 11:13:46', '2026-08-09 11:13:46'),
(42, 'Dr. Pedro Reyes', 'Earth Science', 'BSIT 1C', 'Room 103', 'Class', '15:00:00', '17:00:00', 'Scheduled', 'Friday', '2026-08-14', '2026-08-09 11:13:46', '2026-08-09 11:13:46'),
(43, 'Dr. Juan Cruz', 'Special Math Class', 'BSIT 1A', 'Room 101', 'Class', '08:00:00', '10:00:00', 'Scheduled', 'Saturday', '2026-08-15', '2026-08-09 11:13:46', '2026-08-09 11:13:46'),
(44, 'Prof. Maria Santos', 'Writing Workshop', 'BSIT 2A', 'Room 201', 'Class', '10:00:00', '12:00:00', 'Scheduled', 'Saturday', '2026-08-15', '2026-08-09 11:13:46', '2026-08-09 11:13:46'),
(45, 'Break', 'Break Time', 'All Sections', 'Cafeteria', 'Break Time', '12:00:00', '13:00:00', 'Scheduled', 'Saturday', '2026-08-15', '2026-08-09 11:13:46', '2026-08-09 11:13:46'),
(46, 'Prof. Ana Garcia', 'IT Seminar', 'BSIT 2B', 'Room 202', 'Class', '13:00:00', '15:00:00', 'Scheduled', 'Saturday', '2026-08-15', '2026-08-09 11:13:46', '2026-08-09 11:13:46');

-- --------------------------------------------------------

--
-- Table structure for table `mon_attendance_archive`
--

CREATE TABLE `mon_attendance_archive` (
  `id` int(11) NOT NULL,
  `original_id` int(11) NOT NULL,
  `schedule_id` int(11) DEFAULT NULL,
  `faculty_name` varchar(100) DEFAULT NULL,
  `course_section` varchar(50) DEFAULT NULL,
  `subject_code` varchar(20) DEFAULT NULL,
  `room` varchar(50) DEFAULT NULL,
  `student_count` int(11) DEFAULT 0,
  `attendance_date` date DEFAULT NULL,
  `check_time` datetime DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `is_online` tinyint(1) DEFAULT NULL,
  `meeting_link` text DEFAULT NULL,
  `meeting_screenshot` varchar(255) DEFAULT NULL,
  `face_to_face_image` varchar(255) DEFAULT NULL,
  `verified_by` varchar(100) DEFAULT NULL,
  `verification_method` varchar(50) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `archived_by` varchar(100) DEFAULT NULL,
  `archived_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mon_attendance_records`
--

CREATE TABLE `mon_attendance_records` (
  `id` int(11) NOT NULL,
  `schedule_id` int(11) DEFAULT NULL,
  `faculty_name` varchar(100) NOT NULL,
  `course_section` varchar(50) NOT NULL,
  `subject_code` varchar(20) NOT NULL,
  `room` varchar(50) NOT NULL,
  `student_count` int(11) DEFAULT 0,
  `attendance_date` date NOT NULL,
  `check_time` datetime NOT NULL,
  `status` enum('present','absent','late','excused','online','nt','eb','ed','ob','at','pending') NOT NULL,
  `is_online` tinyint(1) DEFAULT 0,
  `meeting_link` text DEFAULT NULL,
  `meeting_screenshot` varchar(255) DEFAULT NULL,
  `face_to_face_image` varchar(255) DEFAULT NULL,
  `verified_by` varchar(100) NOT NULL,
  `verification_method` enum('physical_check','online_monitoring','room_visit') NOT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mon_attendance_records`
--

INSERT INTO `mon_attendance_records` (`id`, `schedule_id`, `faculty_name`, `course_section`, `subject_code`, `room`, `student_count`, `attendance_date`, `check_time`, `status`, `is_online`, `meeting_link`, `meeting_screenshot`, `face_to_face_image`, `verified_by`, `verification_method`, `remarks`, `created_at`) VALUES
(8, 21, 'Dr. Alice Smith', 'CS-2A', 'Remedial Data Struct', 'Room 301', 20, '2026-08-09', '2026-08-09 19:58:49', 'present', 0, '', NULL, 'uploads/face_to_face/face_20260809_195849_17247c61fb2a496e.jpg', 'Administrator', 'physical_check', 'rffdgj', '2026-08-09 11:58:49'),
(9, 22, 'Dr. Alice Smith', 'N/A', 'Break', 'N/A', 10, '2026-08-09', '2026-08-09 20:34:47', 'eb', 0, '', NULL, 'uploads/face_to_face/face_20260809_203447_3286cb69babbdca3.png', 'Administrator', 'physical_check', 'hehw', '2026-08-09 12:34:47'),
(10, 23, 'Mr. Charles Lee', 'Open Section', 'Web Dev Bootcamp', 'Lab 1', 15, '2026-08-09', '2026-08-09 21:05:16', 'online', 1, 'http://192.168.100.10:8080/mobile-attendance', 'uploads/meeting_screenshots/screenshot_20260809_210516_bd3a0e67affaba1a.png', NULL, 'Administrator', 'online_monitoring', '', '2026-08-09 13:05:16'),
(12, 1, 'Dr. Alice Smith', 'CS-2A', 'Data Structures', 'Room 301', 15, '2026-08-10', '2026-08-10 13:39:56', 'eb', 0, '', NULL, 'uploads/face_to_face/face_20260810_133956_c2d068c0e20e0fa9.jpg', 'Administrator', 'physical_check', 'y3yeyw', '2026-08-10 23:04:43'),
(13, 5, 'Dr. Sarah Connor', 'PHY-1A', 'Physics 101', 'Room 202', 30, '2026-08-25', '2026-08-25 22:56:41', 'present', 0, '', NULL, 'uploads/face_to_face/face_20260825_225641_52cef2ca06959e3d.jpg', 'Administrator', 'physical_check', 'hehehey', '2026-08-25 14:56:41');

-- --------------------------------------------------------

--
-- Table structure for table `mon_facilities`
--

CREATE TABLE `mon_facilities` (
  `id` int(11) NOT NULL,
  `room_number` varchar(50) NOT NULL,
  `equipment_type` enum('chair','switch','light','aircon','projector','computer','other') NOT NULL,
  `equipment_name` varchar(100) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `functional_quantity` int(11) DEFAULT 0,
  `damaged_quantity` int(11) DEFAULT 0,
  `status` enum('complete','incomplete','needs_repair') DEFAULT 'complete',
  `monitored_by` varchar(100) DEFAULT NULL,
  `last_checked` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mon_facility_reports`
--

CREATE TABLE `mon_facility_reports` (
  `id` int(11) NOT NULL,
  `facility_id` int(11) DEFAULT NULL,
  `room_number` varchar(50) NOT NULL,
  `broken_equipment` varchar(100) NOT NULL,
  `equipment_type` enum('chair','switch','light','aircon','projector','computer','other') NOT NULL,
  `quantity_damaged` int(11) DEFAULT 1,
  `description` text DEFAULT NULL,
  `damage_image` varchar(255) DEFAULT NULL,
  `reported_by` varchar(100) NOT NULL,
  `report_date` date NOT NULL,
  `status` enum('reported','in_progress','resolved','replaced') DEFAULT 'reported',
  `resolved_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mon_facility_reports`
--

INSERT INTO `mon_facility_reports` (`id`, `facility_id`, `room_number`, `broken_equipment`, `equipment_type`, `quantity_damaged`, `description`, `damage_image`, `reported_by`, `report_date`, `status`, `resolved_date`, `created_at`) VALUES
(3, NULL, '302', 'broken monitor', 'computer', 1, 'broken monitor ', 'uploads/facility_damage/damage_20260810_141919_2f4d0bdf8548.jpg', 'Administrator', '2026-08-10', 'reported', NULL, '2026-08-10 06:19:19'),
(4, NULL, '101', 'chair', '', 1, 'broken leg ', NULL, 'Administrator', '2026-08-10', 'reported', NULL, '2026-08-10 23:07:29'),
(5, NULL, '404', 'white board', 'other', 1, 'sira', 'uploads/facility_damage/damage_20260820_211131_51863e344817.jpg', 'Administrator', '2026-08-20', 'reported', NULL, '2026-08-20 13:11:31'),
(6, NULL, '404', 'broken chair', 'other', 1, 'Chair is broken and unstable', NULL, 'Administrator', '2026-08-20', 'reported', NULL, '2026-08-20 13:33:36'),
(7, NULL, '101', 'broken leg', 'chair', 1, 'broken leg', NULL, 'Administrator', '2026-08-10', 'reported', NULL, '2026-08-09 22:05:19'),
(8, NULL, '302', 'broken monitor', 'computer', 1, 'broken monitor', 'uploads/facility_damage/damage_20260810_141919_2f4d0bdf8548.jpg', 'Administrator', '2026-08-10', 'reported', NULL, '2026-08-09 22:19:19');

-- --------------------------------------------------------

--
-- Table structure for table `mon_facility_reports_archive`
--

CREATE TABLE `mon_facility_reports_archive` (
  `id` int(11) NOT NULL,
  `original_id` int(11) NOT NULL,
  `facility_id` int(11) DEFAULT NULL,
  `room_number` varchar(50) NOT NULL,
  `broken_equipment` varchar(100) NOT NULL,
  `equipment_type` enum('chair','switch','light','aircon','projector','computer','other') NOT NULL,
  `quantity_damaged` int(11) DEFAULT 1,
  `description` text DEFAULT NULL,
  `damage_image` varchar(255) DEFAULT NULL,
  `reported_by` varchar(100) NOT NULL,
  `report_date` date NOT NULL,
  `status` varchar(50) DEFAULT 'reported',
  `resolved_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `archived_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mon_users`
--

CREATE TABLE `mon_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` enum('admin','monitor','viewer') DEFAULT 'monitor',
  `status` enum('active','inactive') DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mon_users`
--

INSERT INTO `mon_users` (`id`, `username`, `password`, `fullname`, `email`, `role`, `status`, `last_login`, `created_at`) VALUES
(1, 'admin', '0192023a7bbd73250516f069df18b500', 'Administrator', 'admin@system.com', 'admin', 'active', '2026-09-16 22:28:42', '2026-08-08 18:41:19'),
(2, 'monitor', 'a808370b7b147e3533d54538bbcf13a9', 'Monitor User', 'monitor@system.com', 'monitor', 'active', NULL, '2026-08-08 18:41:19'),
(3, 'viewer', '49e5e739ea41d635246cd9cd21af17c4', 'Viewer User', 'viewer@system.com', 'viewer', 'active', NULL, '2026-08-08 18:41:19');

-- --------------------------------------------------------

--
-- Table structure for table `mon_visitors`
--

CREATE TABLE `mon_visitors` (
  `id` int(11) NOT NULL,
  `visitor_name` varchar(100) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `home_address` text DEFAULT NULL,
  `id_type` enum('school_id','government_id','company_id','other') DEFAULT 'government_id',
  `id_number` varchar(50) DEFAULT NULL,
  `vehicle_type` enum('car','motorcycle','bicycle','none') DEFAULT 'none',
  `vehicle_plate` varchar(20) DEFAULT NULL,
  `id_attachment` varchar(255) DEFAULT NULL,
  `id_approved` tinyint(1) DEFAULT 0,
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `additional_notes` text DEFAULT NULL,
  `purpose_of_visit` text NOT NULL,
  `person_to_visit` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `time_in` datetime NOT NULL,
  `time_out` datetime DEFAULT NULL,
  `visit_duration` varchar(20) DEFAULT NULL,
  `status` enum('inside','left','pending_approval','approved') DEFAULT 'pending_approval',
  `monitored_by` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `qr_code` varchar(100) DEFAULT NULL,
  `privacy_consent` tinyint(1) DEFAULT 0,
  `registration_ip` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mon_visitors_archive`
--

CREATE TABLE `mon_visitors_archive` (
  `id` int(11) NOT NULL,
  `original_id` int(11) NOT NULL,
  `visitor_name` varchar(100) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `home_address` text DEFAULT NULL,
  `id_type` varchar(50) DEFAULT NULL,
  `id_number` varchar(50) DEFAULT NULL,
  `vehicle_type` varchar(20) DEFAULT NULL,
  `vehicle_plate` varchar(20) DEFAULT NULL,
  `id_attachment` varchar(255) DEFAULT NULL,
  `id_approved` tinyint(1) DEFAULT 0,
  `purpose_of_visit` text NOT NULL,
  `person_to_visit` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `time_in` datetime NOT NULL,
  `status` varchar(50) DEFAULT NULL,
  `monitored_by` varchar(100) DEFAULT NULL,
  `qr_code` varchar(100) DEFAULT NULL,
  `archived_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mon_visitors_archive`
--

INSERT INTO `mon_visitors_archive` (`id`, `original_id`, `visitor_name`, `contact_number`, `email`, `home_address`, `id_type`, `id_number`, `vehicle_type`, `vehicle_plate`, `id_attachment`, `id_approved`, `purpose_of_visit`, `person_to_visit`, `department`, `time_in`, `status`, `monitored_by`, `qr_code`, `archived_at`) VALUES
(1, 22, 'Mr Bean', '09354521888', 'mrbean@gmailmcom', 'citrus', 'government_id', NULL, 'none', 'h78i', 'uploads/visitor_ids/ID_6a79020ea749b_20260810.jpg', 1, 'meeting', 'visit', 'Registrar', '2026-08-10 06:41:18', 'inside', NULL, 'VIS-6A79020EA7B2C-20260810', '2026-08-10 22:53:22');

-- --------------------------------------------------------

--
-- Table structure for table `mon_visitor_audit_logs`
--

CREATE TABLE `mon_visitor_audit_logs` (
  `id` int(11) NOT NULL,
  `visitor_id` int(11) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `performed_by` int(11) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sms_employee`
--

CREATE TABLE `sms_employee` (
  `employee_id` bigint(100) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `role` int(100) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT NULL,
  `date_hired` date DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `department` int(100) DEFAULT NULL,
  `position` int(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sms_employee`
--

INSERT INTO `sms_employee` (`employee_id`, `first_name`, `middle_name`, `last_name`, `role`, `status`, `date_hired`, `user_id`, `department`, `position`) VALUES
(1001, 'Andrie', 'Sta Maria', 'Elbambuena', 1, 'active', '2026-03-01', NULL, 1, 2),
(1002, 'El ', 'Dikoalam', 'Yazier', 2, 'active', NULL, 12, 2, 6),
(1003, 'Carl James', 'Dialam', 'Langres', 3, 'active', NULL, 16, 3, 11),
(1004, 'Mj ', 'Dontknow', 'Estomaguio', 4, 'active', NULL, 10, 4, 19),
(1005, 'JohnDale', 'Dekoalam', 'Cabab', 5, 'active', NULL, 14, 5, 22),
(1006, 'Justin', 'Dunknow', 'Hereda', 6, 'active', NULL, 17, 6, 27),
(1007, 'Johary', 'Dekodinalam', 'Dimatingkal', 8, 'active', NULL, 13, 8, 33),
(1008, 'John Paul ', 'Dunno', 'Corre', 7, 'active', NULL, 15, 7, 29),
(1009, 'Sayurie', 'Donno', 'Torres', 9, 'active', NULL, 11, 9, 36),
(1010, 'Dc. Milagros', 'O.', 'Luang', 1, 'active', NULL, 18, 1, 1),
(1011, 'Jhon Carlo', 'IDK', 'Garcia', 10, 'active', NULL, 19, 12, 41);

-- --------------------------------------------------------

--
-- Table structure for table `user_account`
--

CREATE TABLE `user_account` (
  `user_id` int(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `employee_id` bigint(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `remember_token` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_account`
--

INSERT INTO `user_account` (`user_id`, `password`, `employee_id`, `email`, `created_at`, `remember_token`) VALUES
(1, '$2y$10$1P2v4toYP6ZNlkbLhkwufOsPI3e4zH0Rm401NmI3ghVRs762h9p/.', 1001, NULL, '2026-03-15 13:27:25', NULL),
(10, '$2y$10$6KIkuZSXS8Xz5F8412fbHuC/iTy5rPY0pCH95h1LfzmvhQLmuNlx2', 1004, NULL, '2026-03-15 14:15:13', NULL),
(11, '$2y$10$.46Nwr3ERHyItVQi5h42KObJTsmuZsdWunRu.QNOWNjeuse.nQin.', 1009, NULL, '2026-03-16 04:33:42', NULL),
(12, '$2y$10$QP30TpWyZXtKZ/kSvp9Rt.A8RCPtvNAKxRMr08Jn1JCnBt1JZCMOm', 1002, NULL, '2026-03-16 05:29:17', NULL),
(13, '$2y$10$OPykXcYyFeG2LLU0J8.e5e8SsVbV/IdWLyNq2xjpcBaKUG55th24S', 1007, NULL, '2026-03-16 05:39:36', NULL),
(14, '$2y$10$MjlfZZgenEGdtQawnwYAhu/y/sztb37lkF6nIZlY4Lr2wcjbvM09S', 1005, NULL, '2026-03-16 05:46:51', NULL),
(15, '$2y$10$m9eI4tmhtIhhiUaYNAVBWe9hBjNHoEwB15yMekpq8IanPx0PrsNCq', 1008, NULL, '2026-03-16 06:03:14', NULL),
(16, '$2y$10$kPPk77eqmMzFwZsVT1rwKO2qm1uY8jMyjg0okJyujdPOWqV9Ro1k2', 1003, NULL, '2026-03-16 07:18:38', NULL),
(17, '$2y$10$VCeB5AlD2X/RHuWm24BIIuByXygltj6rWQmnxkj3VvMCZYGCgu1.m', 1006, NULL, '2026-03-16 07:18:59', NULL),
(18, '$2y$10$Op84BtojjINdj5CuiRND2O/7/PRnVoNI6zU/IDha7jB3hxciesg/q', 1010, NULL, '2026-03-18 08:52:08', NULL),
(19, '$2y$10$TIcMlZAW1FkYpCij85ewduduAFOMRRdOozS3IWSnMJVtOJn51T2Iu', 1011, NULL, '2026-08-06 02:57:24', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cc_schedule`
--
ALTER TABLE `cc_schedule`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mon_attendance_archive`
--
ALTER TABLE `mon_attendance_archive`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mon_attendance_records`
--
ALTER TABLE `mon_attendance_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `schedule_id` (`schedule_id`);

--
-- Indexes for table `mon_facilities`
--
ALTER TABLE `mon_facilities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mon_facility_reports`
--
ALTER TABLE `mon_facility_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `facility_id` (`facility_id`);

--
-- Indexes for table `mon_facility_reports_archive`
--
ALTER TABLE `mon_facility_reports_archive`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mon_visitors`
--
ALTER TABLE `mon_visitors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `qr_code` (`qr_code`);

--
-- Indexes for table `mon_visitors_archive`
--
ALTER TABLE `mon_visitors_archive`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mon_visitor_audit_logs`
--
ALTER TABLE `mon_visitor_audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `visitor_id` (`visitor_id`);

--
-- Indexes for table `sms_employee`
--
ALTER TABLE `sms_employee`
  ADD PRIMARY KEY (`employee_id`),
  ADD KEY `demo_employee_ibfk_1` (`user_id`),
  ADD KEY `demo_employee_ibfk_2` (`role`),
  ADD KEY `department` (`department`),
  ADD KEY `position` (`position`);

--
-- Indexes for table `user_account`
--
ALTER TABLE `user_account`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cc_schedule`
--
ALTER TABLE `cc_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `mon_attendance_archive`
--
ALTER TABLE `mon_attendance_archive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `mon_attendance_records`
--
ALTER TABLE `mon_attendance_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `mon_facilities`
--
ALTER TABLE `mon_facilities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mon_facility_reports`
--
ALTER TABLE `mon_facility_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `mon_facility_reports_archive`
--
ALTER TABLE `mon_facility_reports_archive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `mon_visitors`
--
ALTER TABLE `mon_visitors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `mon_visitors_archive`
--
ALTER TABLE `mon_visitors_archive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `mon_visitor_audit_logs`
--
ALTER TABLE `mon_visitor_audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `sms_employee`
--
ALTER TABLE `sms_employee`
  MODIFY `employee_id` bigint(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1012;

--
-- AUTO_INCREMENT for table `user_account`
--
ALTER TABLE `user_account`
  MODIFY `user_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `mon_attendance_records`
--
ALTER TABLE `mon_attendance_records`
  ADD CONSTRAINT `mon_attendance_records_ibfk_1` FOREIGN KEY (`schedule_id`) REFERENCES `cc_schedule` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `mon_facility_reports`
--
ALTER TABLE `mon_facility_reports`
  ADD CONSTRAINT `mon_facility_reports_ibfk_1` FOREIGN KEY (`facility_id`) REFERENCES `mon_facilities` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `mon_visitor_audit_logs`
--
ALTER TABLE `mon_visitor_audit_logs`
  ADD CONSTRAINT `mon_visitor_audit_logs_ibfk_1` FOREIGN KEY (`visitor_id`) REFERENCES `mon_visitors` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

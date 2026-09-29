-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 27, 2026 at 02:37 PM
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
-- Database: `sms`
--

-- --------------------------------------------------------

--
-- Table structure for table `cc_events`
--

CREATE TABLE `cc_events` (
  `event_id` int(11) NOT NULL,
  `template_id` int(11) DEFAULT NULL,
  `event_title` varchar(255) NOT NULL,
  `event_type` enum('Academic','Meeting','Seminar','Institutional Event','Cultural Event','Sports Event','Orientation','Other') NOT NULL,
  `event_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `target_audience` varchar(100) DEFAULT 'All Students',
  `status` enum('upcoming','ongoing','completed','cancelled') NOT NULL DEFAULT 'upcoming',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cc_event_templates`
--

CREATE TABLE `cc_event_templates` (
  `template_id` int(11) NOT NULL,
  `template_name` varchar(100) NOT NULL,
  `event_type` enum('Academic','Meeting','Seminar','Institutional Event','Cultural Event','Sports Event','Orientation','Other') NOT NULL,
  `default_title` varchar(255) NOT NULL,
  `default_description` text DEFAULT NULL,
  `default_location` varchar(255) DEFAULT NULL,
  `default_target_audience` varchar(100) DEFAULT NULL,
  `default_status` enum('upcoming','ongoing','completed','cancelled') DEFAULT 'upcoming',
  `priority` enum('Normal','High','Urgent') DEFAULT 'Normal',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cc_faculty`
--

CREATE TABLE `cc_faculty` (
  `id` int(11) NOT NULL,
  `faculty_code` varchar(50) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cc_faculty`
--

INSERT INTO `cc_faculty` (`id`, `faculty_code`, `first_name`, `last_name`, `email`, `department`, `created_at`) VALUES
(1, 'DJAURIGUE', 'D.', 'Jaurigue', 'djaurigue@university.edu', 'Information System', '2026-01-23 20:10:10'),
(2, 'MCPANGILINAN', 'M.C.', 'Pangilinan', 'mcpangilinan@university.edu', 'Information System', '2026-01-23 20:10:10'),
(3, 'P. Arevalo', 'Philip', 'Arevalo', 'parevalo@university.edu', 'Information System', '2026-01-23 20:10:10'),
(9, 'R. Talento', 'Rusty', 'Talento', 'rtalento@university.edu', 'Information System', '2026-01-23 20:53:23');

-- --------------------------------------------------------

--
-- Table structure for table `cc_faculty_load`
--

CREATE TABLE `cc_faculty_load` (
  `id` int(11) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `school_year_id` int(11) DEFAULT NULL,
  `semester_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cc_faculty_load`
--

INSERT INTO `cc_faculty_load` (`id`, `faculty_id`, `section_id`, `subject_id`, `school_year_id`, `semester_id`, `created_at`) VALUES
(1, 3, 13, 15, 31, 14, '2026-08-05 21:51:44'),
(2, 1, 2, 16, 31, 13, '2026-08-05 21:53:14'),
(3, 1, 4, 16, 31, 13, '2026-08-06 01:35:18');

-- --------------------------------------------------------

--
-- Table structure for table `cc_room`
--

CREATE TABLE `cc_room` (
  `id` int(11) NOT NULL,
  `room_code` varchar(20) NOT NULL,
  `room_name` varchar(100) NOT NULL,
  `building` varchar(100) DEFAULT 'Main Building',
  `floor` enum('1st Floor','2nd Floor','3rd Floor','4th Floor') NOT NULL,
  `room_type` enum('Lecture Room','Computer Laboratory','Science Laboratory','Library','Office','AVR','Court Room','Other') NOT NULL,
  `capacity` int(11) DEFAULT 40,
  `status` enum('Available','Maintenance','Unavailable') DEFAULT 'Available',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cc_room`
--

INSERT INTO `cc_room` (`id`, `room_code`, `room_name`, `building`, `floor`, `room_type`, `capacity`, `status`, `created_at`) VALUES
(1, '101', 'Room 101', 'Main Building', '1st Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:03:33'),
(2, '102', 'Room 102', 'Main Building', '1st Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:03:33'),
(3, '103', 'Room 103', 'Main Building', '1st Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:03:33'),
(4, '104', 'Room 104', 'Main Building', '1st Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:03:33'),
(5, '105', 'Room 105', 'Main Building', '1st Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:03:33'),
(6, '106', 'Room 106', 'Main Building', '1st Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:03:33'),
(7, '107', 'Room 107', 'Main Building', '1st Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:03:33'),
(8, '108', 'Room 108', 'Main Building', '1st Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:03:33'),
(9, 'COMLAB1', 'Computer Laboratory 1', 'Main Building', '1st Floor', 'Computer Laboratory', 50, 'Available', '2026-08-07 08:03:33'),
(10, '202', 'Room 202', 'Main Building', '2nd Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:05:53'),
(11, '203', 'Room 203', 'Main Building', '2nd Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:05:53'),
(12, '204', 'Room 204', 'Main Building', '2nd Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:05:53'),
(13, '205', 'Room 205', 'Main Building', '2nd Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:05:53'),
(14, '206', 'Room 206', 'Main Building', '2nd Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:05:53'),
(15, '207', 'Room 207', 'Main Building', '2nd Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:05:53'),
(16, '208', 'Room 208', 'Main Building', '2nd Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:05:53'),
(17, '210', 'Room 210', 'Main Building', '2nd Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:05:53'),
(18, '211', 'Room 211', 'Main Building', '2nd Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:05:53'),
(19, '212', 'Room 212', 'Main Building', '2nd Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:05:53'),
(20, '213', 'Room 213', 'Main Building', '2nd Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:05:53'),
(21, '214', 'Room 214', 'Main Building', '2nd Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:05:53'),
(22, '215', 'Room 215', 'Main Building', '2nd Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:05:53'),
(23, '216', 'Room 216', 'Main Building', '2nd Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:05:53'),
(24, 'PSYCHLAB', 'Psychology Laboratory', 'Main Building', '2nd Floor', 'Science Laboratory', 50, 'Available', '2026-08-07 08:05:53'),
(25, 'ITLAB', 'IT Laboratory', 'Main Building', '2nd Floor', 'Computer Laboratory', 50, 'Available', '2026-08-07 08:05:53'),
(26, '302', 'Room 302', 'Main Building', '3rd Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:09:36'),
(27, '303', 'Room 303', 'Main Building', '3rd Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:09:36'),
(28, '304', 'Room 304', 'Main Building', '3rd Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:09:36'),
(29, '305', 'Room 305', 'Main Building', '3rd Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:09:36'),
(30, '306', 'Room 306', 'Main Building', '3rd Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:09:36'),
(31, '307', 'Room 307', 'Main Building', '3rd Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:09:36'),
(32, '308', 'Room 308', 'Main Building', '3rd Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:09:36'),
(33, '309', 'Room 309', 'Main Building', '3rd Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:09:36'),
(34, '310', 'Room 310', 'Main Building', '3rd Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:09:36'),
(35, '311', 'Room 311', 'Main Building', '3rd Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:09:36'),
(36, '312', 'Room 312', 'Main Building', '3rd Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:09:36'),
(37, '313', 'Room 313', 'Main Building', '3rd Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:09:36'),
(38, 'ETLAB1', 'Educational Technology Room 1', 'Main Building', '3rd Floor', 'Computer Laboratory', 50, 'Available', '2026-08-07 08:09:36'),
(39, 'ETLAB2', 'Educational Technology Room 2', 'Main Building', '3rd Floor', 'Computer Laboratory', 50, 'Available', '2026-08-07 08:09:36'),
(40, 'CHEMLAB', 'Chemistry Laboratory', 'Main Building', '3rd Floor', 'Science Laboratory', 50, 'Available', '2026-08-07 08:09:36'),
(41, '403', 'Room 403', 'Main Building', '4th Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:11:15'),
(42, '404', 'Room 404', 'Main Building', '4th Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:11:15'),
(43, '405', 'Room 405', 'Main Building', '4th Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:11:15'),
(44, '406', 'Room 406', 'Main Building', '4th Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:11:15'),
(45, '407', 'Room 407', 'Main Building', '4th Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:11:15'),
(46, '408', 'Room 408', 'Main Building', '4th Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:11:15'),
(47, '409', 'Room 409', 'Main Building', '4th Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:11:15'),
(48, '410', 'Room 410', 'Main Building', '4th Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:11:15'),
(49, '411', 'Room 411', 'Main Building', '4th Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:11:15'),
(50, '412', 'Room 412', 'Main Building', '4th Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:11:15'),
(51, '413', 'Room 413', 'Main Building', '4th Floor', 'Lecture Room', 50, 'Available', '2026-08-07 08:11:15'),
(52, 'PHYSICSLAB', 'Physics Laboratory', 'Main Building', '4th Floor', 'Science Laboratory', 50, 'Available', '2026-08-07 08:11:15'),
(53, 'FORENSICLAB', 'Forensics Laboratory', 'Main Building', '4th Floor', 'Science Laboratory', 50, 'Available', '2026-08-07 08:11:15'),
(54, 'QDLAB', 'Questioned Documents Laboratory', 'Main Building', '4th Floor', 'Science Laboratory', 50, 'Available', '2026-08-07 08:11:15'),
(55, 'COMLAB4', 'Computer Laboratory 4', 'Main Building', '4th Floor', 'Computer Laboratory', 50, 'Available', '2026-08-07 08:11:15');

-- --------------------------------------------------------

--
-- Table structure for table `cc_schedule`
--

CREATE TABLE `cc_schedule` (
  `id` int(11) NOT NULL,
  `faculty_load_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `schedule_type` enum('Class','Break Time') DEFAULT 'Class',
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `status` enum('Scheduled','Completed','Cancelled') DEFAULT 'Scheduled',
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday') NOT NULL,
  `section_id` int(11) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `school_year_id` int(11) NOT NULL,
  `semester_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cc_sections`
--

CREATE TABLE `cc_sections` (
  `id` int(11) NOT NULL,
  `section_code` varchar(50) DEFAULT NULL,
  `grade_level` varchar(50) DEFAULT NULL,
  `program_id` int(11) DEFAULT NULL,
  `school_year_id` int(11) DEFAULT NULL,
  `semester_id` int(11) DEFAULT NULL,
  `adviser_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cc_sections`
--

INSERT INTO `cc_sections` (`id`, `section_code`, `grade_level`, `program_id`, `school_year_id`, `semester_id`, `adviser_id`, `created_at`) VALUES
(1, 'BSIS-1001', '1st Year', 26, 31, 13, 3, '2026-07-29 10:51:28'),
(2, 'BSIS-1002', '1st Year', 26, 31, 13, 2, '2026-07-29 10:51:28'),
(3, 'BSIS-1003', '1st Year', 26, 31, 13, 3, '2026-07-29 10:51:28'),
(4, 'BSIS-1004', '1st Year', 26, 31, 13, 9, '2026-07-29 10:51:28'),
(5, 'BSIS-2001', '2nd Year', 26, 31, 13, 1, '2026-07-29 10:51:28'),
(6, 'BSIS-2002', '2nd Year', 26, 31, 13, 9, '2026-07-29 10:51:28'),
(7, 'BSIS-2003', '2nd Year', 26, 31, 13, 2, '2026-07-29 10:51:28'),
(8, 'BSIS-3001', '3rd Year', 26, 31, 13, 1, '2026-07-29 10:51:28'),
(9, 'BSIS-3002', '3rd Year', 26, 31, 13, 2, '2026-07-29 10:51:28'),
(10, 'BSIS-4001', '4th Year', 26, 31, 13, 1, '2026-07-29 10:51:28'),
(11, 'BSIS-4002', '4th Year', 26, 31, 13, 2, '2026-07-29 10:51:28'),
(12, 'BSIS-1001', '1st Year', 26, 31, 14, 9, '2026-07-29 10:51:28'),
(13, 'BSIS-1002', '1st Year', 26, 31, 14, 3, '2026-07-29 10:51:28'),
(14, 'BSIS-1003', '1st Year', 26, 31, 14, NULL, '2026-07-29 10:51:28'),
(15, 'BSIS-1004', '1st Year', 26, 31, 14, NULL, '2026-07-29 10:51:28'),
(16, 'BSIS-2001', '2nd Year', 26, 31, 14, NULL, '2026-07-29 10:51:28'),
(17, 'BSIS-2002', '2nd Year', 26, 31, 14, NULL, '2026-07-29 10:51:28'),
(18, 'BSIS-2003', '2nd Year', 26, 31, 14, NULL, '2026-07-29 10:51:28'),
(19, 'BSIS-3001', '3rd Year', 26, 31, 14, NULL, '2026-07-29 10:51:28'),
(20, 'BSIS-3002', '3rd Year', 26, 31, 14, NULL, '2026-07-29 10:51:28'),
(21, 'BSIS-4001', '4th Year', 26, 31, 14, 9, '2026-07-29 10:51:28'),
(22, 'BSIS-4002', '4th Year', 26, 31, 14, NULL, '2026-07-29 10:51:28');

-- --------------------------------------------------------

--
-- Table structure for table `cc_section_faculty`
--

CREATE TABLE `cc_section_faculty` (
  `id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `role` varchar(50) DEFAULT NULL,
  `school_year_id` int(11) NOT NULL,
  `semester_id` int(11) NOT NULL,
  `status` enum('Active','Completed','Reassigned','Cancelled') NOT NULL DEFAULT 'Active',
  `assigned_at` datetime NOT NULL DEFAULT current_timestamp(),
  `ended_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cln_clinic_visits`
--

CREATE TABLE `cln_clinic_visits` (
  `visit_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `visit_date` datetime NOT NULL,
  `reason` text DEFAULT NULL,
  `diagnosis` text DEFAULT NULL,
  `treatment` text DEFAULT NULL,
  `follow_up_date` date DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Completed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cln_departments`
--

CREATE TABLE `cln_departments` (
  `department_id` int(11) NOT NULL,
  `department_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cln_doctors`
--

CREATE TABLE `cln_doctors` (
  `doctor_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `specialization` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cln_incidents`
--

CREATE TABLE `cln_incidents` (
  `incident_id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `incident_type` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `reported_by` varchar(50) DEFAULT NULL,
  `date_reported` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'Open',
  `resolved_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cln_inventory_log`
--

CREATE TABLE `cln_inventory_log` (
  `inventory_id` int(11) NOT NULL,
  `medication_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `action` enum('ADD','REMOVE') NOT NULL,
  `action_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cln_medications`
--

CREATE TABLE `cln_medications` (
  `medication_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `unit` varchar(20) DEFAULT NULL,
  `dosage` varchar(50) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cln_patients`
--

CREATE TABLE `cln_patients` (
  `patient_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cln_roles`
--

CREATE TABLE `cln_roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cln_staff`
--

CREATE TABLE `cln_staff` (
  `staff_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `enr_announcements`
--

CREATE TABLE `enr_announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `target_audience` enum('all','applicants','students','admins') DEFAULT 'all',
  `created_by` int(11) NOT NULL,
  `is_published` tinyint(4) DEFAULT 1,
  `publish_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enr_announcements`
--

INSERT INTO `enr_announcements` (`id`, `title`, `content`, `target_audience`, `created_by`, `is_published`, `publish_date`, `expiry_date`, `created_at`) VALUES
(1, 'Enrollment for AY 2024-2025', 'Enrollment for Academic Year 2024-2025 will start on June 1, 2024. Please complete all requirements.', 'all', 1, 1, '2026-02-13', '2026-03-15', '2026-02-13 00:22:38'),
(2, 'Document Submission Reminder', 'All applicants are reminded to submit complete requirements to avoid delays in processing.', 'applicants', 1, 1, '2026-02-13', '2026-02-28', '2026-02-13 00:22:38'),
(3, 'New Course Offerings', 'We are pleased to announce new course offerings for the upcoming semester: Data Science and Artificial Intelligence.', 'students', 1, 1, '2026-02-13', '2026-04-14', '2026-02-13 00:22:38');

-- --------------------------------------------------------

--
-- Table structure for table `enr_applicants`
--

CREATE TABLE `enr_applicants` (
  `applicant_id` int(11) NOT NULL,
  `surname` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `suffix` varchar(10) DEFAULT NULL,
  `admission_type` enum('freshmen','transferee','returnee','senior_high') DEFAULT NULL,
  `working_student` enum('Yes','No') DEFAULT NULL,
  `sex` enum('Male','Female','Other') NOT NULL,
  `address_barangay` varchar(100) NOT NULL,
  `address_city` varchar(100) NOT NULL,
  `address_province` varchar(100) NOT NULL,
  `address_complete` text DEFAULT NULL,
  `school_last_attended` varchar(150) NOT NULL,
  `year_graduated` year(4) NOT NULL,
  `how_hear` varchar(50) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `date_of_birth` date NOT NULL,
  `place_of_birth` varchar(150) NOT NULL,
  `age` int(11) DEFAULT NULL,
  `civil_status` enum('Single','Married','Divorced','Widowed') NOT NULL,
  `religion` varchar(50) DEFAULT NULL,
  `contact_number` varchar(20) NOT NULL,
  `facebook` varchar(100) DEFAULT NULL,
  `messenger` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `parent_full_name` varchar(150) NOT NULL,
  `parent_contact` varchar(20) DEFAULT NULL,
  `parent_address` text DEFAULT NULL,
  `course_id` int(10) DEFAULT NULL,
  `preferred_section_id` int(11) DEFAULT NULL,
  `status` enum('pending','converted','rejected') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `enr_courses`
--

CREATE TABLE `enr_courses` (
  `id` int(11) NOT NULL,
  `course_code` varchar(20) NOT NULL,
  `course_name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `duration_years` int(11) DEFAULT 4,
  `total_units` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enr_courses`
--

INSERT INTO `enr_courses` (`id`, `course_code`, `course_name`, `description`, `duration_years`, `total_units`, `is_active`, `created_at`) VALUES
(1, 'BSCS', 'Bachelor of Science in Computer Science', 'Four-year program focusing on computer theory, algorithms, and software development', 4, 142, 1, '2026-02-13 00:22:37'),
(2, 'BSIT', 'Bachelor of Science in Information Technology', 'Four-year program focusing on IT infrastructure, networking, and web development', 4, 138, 1, '2026-02-13 00:22:37'),
(3, 'BSIS', 'Bachelor of Science in Information Systems', 'Four-year program focusing on business information systems and database management', 4, 136, 1, '2026-02-13 00:22:37'),
(4, 'BSCE', 'Bachelor of Science in Civil Engineering', 'Five-year program focusing on civil infrastructure design and construction', 5, 180, 1, '2026-02-13 00:22:37'),
(5, 'BSME', 'Bachelor of Science in Mechanical Engineering', 'Five-year program focusing on mechanical systems and manufacturing', 5, 178, 1, '2026-02-13 00:22:37'),
(6, 'BSED', 'Bachelor of Secondary Education', 'Four-year teacher education program with specialization', 4, 140, 1, '2026-02-13 00:22:37'),
(7, 'BSBA', 'Bachelor of Science in Business Administration', 'Four-year business program with majors in management, marketing, and finance', 4, 135, 1, '2026-02-13 00:22:37'),
(8, 'BSA', 'Bachelor of Science in Accountancy', 'Four-year program focusing on accounting and auditing', 4, 148, 1, '2026-02-13 00:22:37'),
(9, 'BSCrim', 'Bachelor of Science in Criminology', 'Four-year program focusing on criminology and law enforcement', 4, 144, 1, '2026-02-13 00:22:37'),
(10, 'BSN', 'Bachelor of Science in Nursing', 'Four-year program focusing on nursing practice and patient care', 4, 160, 1, '2026-02-13 00:22:37');

-- --------------------------------------------------------

--
-- Table structure for table `enr_course_selections`
--

CREATE TABLE `enr_course_selections` (
  `id` int(11) NOT NULL,
  `applicant_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `is_continuous` tinyint(1) DEFAULT 0,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enr_course_selections`
--

INSERT INTO `enr_course_selections` (`id`, `applicant_id`, `course_id`, `is_continuous`, `status`, `remarks`, `created_at`) VALUES
(1, 2, 3, 0, 'pending', NULL, '2026-03-05 10:39:45'),
(2, 5, 4, 0, 'pending', NULL, '2026-03-16 06:17:05'),
(3, 8, 4, 0, 'pending', NULL, '2026-03-18 02:46:10'),
(4, 9, 4, 0, 'pending', NULL, '2026-03-18 03:09:02'),
(5, 9, 8, 0, 'pending', NULL, '2026-03-18 03:09:15'),
(6, 9, 9, 0, 'pending', NULL, '2026-03-18 03:10:15'),
(7, 9, 3, 0, 'pending', NULL, '2026-03-18 03:10:16'),
(8, 9, 6, 0, 'pending', NULL, '2026-03-18 03:10:18'),
(9, 9, 2, 0, 'pending', NULL, '2026-03-18 03:10:20');

-- --------------------------------------------------------

--
-- Table structure for table `enr_documents`
--

CREATE TABLE `enr_documents` (
  `id` int(11) NOT NULL,
  `applicant_id` int(11) NOT NULL,
  `document_type` varchar(50) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int(11) DEFAULT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `status` enum('pending','verified','rejected') DEFAULT 'pending',
  `verified_by` int(11) DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enr_documents`
--

INSERT INTO `enr_documents` (`id`, `applicant_id`, `document_type`, `file_name`, `file_path`, `file_size`, `mime_type`, `status`, `verified_by`, `verified_at`, `remarks`, `uploaded_at`) VALUES
(1, 1, 'Barangay Clearance', 'Barangay_Clearance_1770942756_698e71241c1e4.png', 'uploads/requirements/1/Barangay_Clearance_1770942756_698e71241c1e4.png', 101458, 'image/png', 'verified', 1, '2026-03-05 10:41:46', NULL, '2026-02-13 00:32:36'),
(2, 2, 'PSA Birth Certificate', 'PSA_Birth_Certificate_1772946897_69ad05d1e0e54.jpg', 'uploads/requirements/2/PSA_Birth_Certificate_1772946897_69ad05d1e0e54.jpg', 52078, 'image/jpeg', 'verified', 1, '2026-03-08 05:15:22', NULL, '2026-03-08 05:14:57'),
(3, 3, 'PSA Birth Certificate', 'PSA_Birth_Certificate_1772947438_69ad07eef31c1.jpg', 'uploads/requirements/3/PSA_Birth_Certificate_1772947438_69ad07eef31c1.jpg', 85017, 'image/jpeg', 'verified', 1, '2026-03-08 05:24:47', NULL, '2026-03-08 05:23:58'),
(4, 4, 'PSA Birth Certificate', 'PSA_Birth_Certificate_1773546293_69b62b357a24b.pdf', 'uploads/requirements/4/PSA_Birth_Certificate_1773546293_69b62b357a24b.pdf', 316222, 'application/pdf', 'verified', 1, '2026-03-15 03:45:14', NULL, '2026-03-15 03:44:53'),
(5, 5, 'PSA Birth Certificate', 'PSA_Birth_Certificate_1773641849_69b7a079973c0.pdf', 'uploads/requirements/5/PSA_Birth_Certificate_1773641849_69b7a079973c0.pdf', 218669, 'application/pdf', 'verified', 1, '2026-03-16 06:18:28', NULL, '2026-03-16 06:17:29'),
(6, 6, 'PSA Birth Certificate', 'PSA_Birth_Certificate_1773744341_69b930d5c45b6.pdf', 'uploads/requirements/6/PSA_Birth_Certificate_1773744341_69b930d5c45b6.pdf', 218669, 'application/pdf', 'verified', 11, '2026-03-17 10:46:46', NULL, '2026-03-17 10:45:41'),
(7, 6, 'Form 138 / Report Card', 'Form_138___Report_Card_1773745600_69b935c01beea.pdf', 'uploads/requirements/6/Form_138___Report_Card_1773745600_69b935c01beea.pdf', 316222, 'application/pdf', 'rejected', NULL, '2026-03-17 14:34:32', 'jhasdhbas', '2026-03-17 11:06:40'),
(8, 8, 'PSA Birth Certificate', 'PSA_Birth_Certificate_1773801866_69ba118a003f4.pdf', 'uploads/requirements/8/PSA_Birth_Certificate_1773801866_69ba118a003f4.pdf', 218669, 'application/pdf', 'rejected', NULL, '2026-03-18 02:57:05', 'incomplete documents', '2026-03-18 02:44:20'),
(9, 9, 'PSA Birth Certificate', 'PSA_Birth_Certificate_1773803335_69ba1747c6afe.pdf', 'uploads/requirements/9/PSA_Birth_Certificate_1773803335_69ba1747c6afe.pdf', 218669, 'application/pdf', 'verified', NULL, '2026-03-18 03:11:58', NULL, '2026-03-18 03:08:50');

-- --------------------------------------------------------

--
-- Table structure for table `enr_document_requirements`
--

CREATE TABLE `enr_document_requirements` (
  `id` int(11) NOT NULL,
  `requirement_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_required` tinyint(4) DEFAULT 1,
  `is_active` tinyint(4) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enr_document_requirements`
--

INSERT INTO `enr_document_requirements` (`id`, `requirement_name`, `description`, `is_required`, `is_active`, `sort_order`) VALUES
(1, 'PSA Birth Certificate', 'Original PSA issued birth certificate', 1, 1, 1),
(2, 'Form 138 / Report Card', 'High school report card with general average', 1, 1, 2),
(3, 'Certificate of Good Moral', 'Certificate of good moral character from last school', 1, 1, 3),
(4, 'Honorable Dismissal', 'Honorable dismissal from previous school', 1, 1, 4),
(5, '2x2 ID Picture', 'Recent 2x2 ID picture with white background', 1, 1, 5),
(6, 'NSO/PSA Marriage Certificate', 'For married female applicants only', 0, 1, 6),
(7, 'ESC Certificate', 'For ESC grantees', 0, 1, 7),
(8, 'Voter\'s ID', 'For local applicants', 0, 1, 8),
(9, 'Barangay Clearance', 'Barangay clearance for residency', 0, 1, 9),
(10, 'Medical Certificate', 'Medical clearance from accredited clinic', 1, 1, 10);

-- --------------------------------------------------------

--
-- Table structure for table `enr_enrollments`
--

CREATE TABLE `enr_enrollments` (
  `enrollment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `school_year` varchar(20) NOT NULL,
  `enrollment_date` date NOT NULL,
  `enrollment_status` enum('enrolled','dropped','completed') DEFAULT 'enrolled',
  `academic_standing` varchar(50) DEFAULT 'Good Standing',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `schedule_id` int(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `enr_requirements`
--

CREATE TABLE `enr_requirements` (
  `requirement_id` int(11) NOT NULL,
  `requirement_name` varchar(100) NOT NULL,
  `requirement_category` enum('freshmen','transferee','continuing') NOT NULL,
  `is_mandatory` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `enr_sections`
--

CREATE TABLE `enr_sections` (
  `id` int(11) NOT NULL,
  `section_code` varchar(50) NOT NULL,
  `section_name` varchar(255) NOT NULL,
  `course_id` int(11) NOT NULL,
  `year_level` int(11) NOT NULL,
  `max_students` int(11) NOT NULL DEFAULT 40,
  `academic_year` varchar(20) NOT NULL,
  `semester` varchar(50) NOT NULL,
  `is_active` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enr_sections`
--

INSERT INTO `enr_sections` (`id`, `section_code`, `section_name`, `course_id`, `year_level`, `max_students`, `academic_year`, `semester`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'BSCS-1A', 'Bachelor of Science in Computer Science - 1A', 1, 1, 40, '2025-2026', '2nd Semester', 1, '2026-03-15 04:06:17', NULL),
(2, 'BSCS-1B', 'Bachelor of Science in Computer Science - 1B', 1, 1, 40, '2025-2026', '2nd Semester', 1, '2026-03-15 04:06:17', NULL),
(3, 'BSCS-2A', 'Bachelor of Science in Computer Science - 2A', 1, 2, 35, '2025-2026', '2nd Semester', 1, '2026-03-15 04:06:17', NULL),
(4, 'BSCS-3A', 'Bachelor of Science in Computer Science - 3A', 1, 3, 30, '2025-2026', '2nd Semester', 1, '2026-03-15 04:06:17', NULL),
(5, 'BSCS-4A', 'Bachelor of Science in Computer Science - 4A', 1, 4, 25, '2025-2026', '2nd Semester', 1, '2026-03-15 04:06:17', NULL),
(6, 'BSIT-1A', 'Bachelor of Science in Information Technology - 1A', 2, 1, 40, '2025-2026', '2nd Semester', 1, '2026-03-15 04:06:17', NULL),
(7, 'BSIT-1B', 'Bachelor of Science in Information Technology - 1B', 2, 1, 40, '2025-2026', '2nd Semester', 1, '2026-03-15 04:06:17', NULL),
(8, 'BSIT-2A', 'Bachelor of Science in Information Technology - 2A', 2, 2, 35, '2025-2026', '2nd Semester', 1, '2026-03-15 04:06:17', NULL),
(9, 'BSIT-3A', 'Bachelor of Science in Information Technology - 3A', 2, 3, 30, '2025-2026', '2nd Semester', 1, '2026-03-15 04:06:17', NULL),
(10, 'BSIT-4A', 'Bachelor of Science in Information Technology - 4A', 2, 4, 25, '2025-2026', '2nd Semester', 1, '2026-03-15 04:06:17', NULL),
(11, 'BSIS-1A', 'Bachelor of Science in Information Systems - 1A', 3, 1, 40, '2025-2026', '2nd Semester', 1, '2026-03-15 04:06:17', NULL),
(12, 'BSIS-2A', 'Bachelor of Science in Information Systems - 2A', 3, 2, 35, '2025-2026', '2nd Semester', 1, '2026-03-15 04:06:17', NULL),
(13, 'BSIS-3A', 'Bachelor of Science in Information Systems - 3A', 3, 3, 30, '2025-2026', '2nd Semester', 1, '2026-03-15 04:06:17', NULL),
(14, 'BSIS-4A', 'Bachelor of Science in Information Systems - 4A', 3, 4, 25, '2025-2026', '2nd Semester', 1, '2026-03-15 04:06:17', NULL),
(15, 'BSCE-1A', 'Bachelor of Science in Civil Engineering - 1A', 4, 1, 35, '2025-2026', '2nd Semester', 1, '2026-03-15 04:06:17', NULL),
(16, 'BSCE-2A', 'Bachelor of Science in Civil Engineering - 2A', 4, 2, 30, '2025-2026', '2nd Semester', 1, '2026-03-15 04:06:17', NULL),
(17, 'BSCE-3A', 'Bachelor of Science in Civil Engineering - 3A', 4, 3, 25, '2025-2026', '2nd Semester', 1, '2026-03-15 04:06:17', NULL),
(18, 'BSCE-4A', 'Bachelor of Science in Civil Engineering - 4A', 4, 4, 20, '2025-2026', '2nd Semester', 1, '2026-03-15 04:06:17', NULL),
(19, 'BSCE-5A', 'Bachelor of Science in Civil Engineering - 5A', 4, 5, 15, '2025-2026', '2nd Semester', 1, '2026-03-15 04:06:17', NULL),
(20, 'BSME-1A', 'Bachelor of Science in Mechanical Engineering - 1A', 5, 1, 35, '2025-2026', '2nd Semester', 1, '2026-03-15 04:06:17', NULL),
(21, 'BSME-2A', 'Bachelor of Science in Mechanical Engineering - 2A', 5, 2, 30, '2025-2026', '2nd Semester', 1, '2026-03-15 04:06:17', NULL),
(22, 'BSME-3A', 'Bachelor of Science in Mechanical Engineering - 3A', 5, 3, 25, '2025-2026', '2nd Semester', 1, '2026-03-15 04:06:17', NULL),
(23, 'BSME-4A', 'Bachelor of Science in Mechanical Engineering - 4A', 5, 4, 20, '2025-2026', '2nd Semester', 1, '2026-03-15 04:06:17', NULL),
(24, 'BSME-5A', 'Bachelor of Science in Mechanical Engineering - 5A', 5, 5, 15, '2025-2026', '2nd Semester', 1, '2026-03-15 04:06:17', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `enr_students`
--

CREATE TABLE `enr_students` (
  `student_id` int(11) NOT NULL,
  `applicant_id` int(11) NOT NULL,
  `student_number` varchar(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `course_id` int(10) NOT NULL,
  `section_id` int(11) DEFAULT NULL,
  `year_level` int(11) DEFAULT 1,
  `enrollment_status` enum('enrolled','on_leave','graduated','dropped') DEFAULT 'enrolled',
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `followup_date` date DEFAULT NULL,
  `followup_notes` text DEFAULT NULL,
  `followup_status` enum('pending','done') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `enr_student_requirements`
--

CREATE TABLE `enr_student_requirements` (
  `student_requirement_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `requirement_id` int(11) NOT NULL,
  `is_submitted` tinyint(1) DEFAULT 0,
  `submitted_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `enr_users`
--

CREATE TABLE `enr_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `user_type` enum('admin','applicant','student') DEFAULT 'applicant',
  `status` tinyint(4) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enr_users`
--

INSERT INTO `enr_users` (`id`, `username`, `password`, `email`, `user_type`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', '0192023a7bbd73250516f069df18b500', 'admin@enrollment.edu', 'admin', 1, '2026-02-13 00:22:37', '2026-02-13 00:22:37'),
(2, 'applicant', 'c4af02478c5542b008abcc11b16da6bc', 'applicant@test.com', 'applicant', 1, '2026-02-13 00:22:38', '2026-02-13 00:22:38'),
(3, 'student', 'ad6a280417a0f533d8b670c61667e1a0', 'student@test.com', 'student', 1, '2026-02-13 00:22:38', '2026-02-13 00:22:38'),
(4, 'Eds', '1e3052092fc7e36795d385e156c106ff', 'abcdefg@gmail.com', 'student', 1, '2026-03-05 10:39:13', '2026-03-08 05:15:34'),
(5, 'Yazier', '5c6568d29e4af0b3e7f10bba7f777aca', 'yazier@gmail.com', 'student', 1, '2026-03-08 05:22:33', '2026-03-08 05:25:07'),
(7, 'Testing', '3e063cd0170b3030316f691acd850a5b', 'testing@gmail.com', 'student', 1, '2026-03-15 03:44:17', '2026-03-15 04:13:52'),
(9, 'Qweasd', '10d0044037640bdf3c427dbe7e72b58f', 'jkfghkdfs@gmail.com', 'student', 1, '2026-03-16 06:16:16', '2026-03-16 07:27:53'),
(11, 'Beaa', 'bfd59291e825b5f2bbf1eb76569f8fe7', 'akjsdhv@gmail.com', 'student', 1, '2026-03-17 10:43:51', '2026-03-17 10:49:03'),
(14, 'Harold', '200820e3227815ed1756a6b531e7e0d2', 'ksjrgo@gmail.com', 'applicant', 1, '2026-03-18 02:11:43', '2026-03-18 02:11:43'),
(15, 'Rusty', 'cbbb72fc62a0bab8604aa583a8e56dff', 'Rusty@gmail.com', 'applicant', 1, '2026-03-18 02:41:27', '2026-03-18 02:41:27'),
(16, 'Mark', '7ff135854376850e9711bd75ce942e07', 'Mark@gmail.com', 'student', 1, '2026-03-18 03:07:30', '2026-03-18 03:13:10');

-- --------------------------------------------------------

--
-- Table structure for table `gd_appointments`
--

CREATE TABLE `gd_appointments` (
  `appointment_id` bigint(20) NOT NULL,
  `student_number` int(250) NOT NULL,
  `counselor_id` bigint(100) NOT NULL,
  `case_id` bigint(20) DEFAULT NULL,
  `appointment_date` datetime NOT NULL,
  `purpose` varchar(255) NOT NULL,
  `meeting_type` enum('Face-to-Face','Online') DEFAULT 'Face-to-Face',
  `status` enum('Pending','Approved','Completed','Cancelled','No Show') DEFAULT 'Pending',
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gd_appointments`
--

INSERT INTO `gd_appointments` (`appointment_id`, `student_number`, `counselor_id`, `case_id`, `appointment_date`, `purpose`, `meeting_type`, `status`, `remarks`, `created_at`) VALUES
(2, 51, 1007, NULL, '2026-08-02 10:16:00', 'adasdasfasfsadsads', 'Online', 'Cancelled', 'ayokooo', '2026-08-02 02:16:18'),
(3, 1070, 1007, NULL, '2026-08-02 10:21:00', 'fuyfjhfjhf', 'Face-to-Face', 'Completed', NULL, '2026-08-02 02:21:27'),
(4, 885, 1007, 5, '2026-08-04 05:03:00', 'sdasdasd', 'Online', 'Completed', NULL, '2026-08-04 03:03:57'),
(5, 885, 1007, 6, '2026-08-08 13:36:00', 'hjhjhj', 'Face-to-Face', 'Completed', NULL, '2026-08-08 05:36:23');

-- --------------------------------------------------------

--
-- Table structure for table `gd_cases`
--

CREATE TABLE `gd_cases` (
  `case_id` bigint(20) NOT NULL,
  `student_number` int(250) NOT NULL,
  `counselor_id` bigint(100) NOT NULL,
  `case_number` varchar(30) NOT NULL,
  `case_type` enum('Referral','Walk-in','Self Referral','Incident') NOT NULL,
  `priority` enum('Low','Medium','High','Critical') DEFAULT 'Medium',
  `status` enum('Open','In Progress','Closed') DEFAULT 'Open',
  `summary` text DEFAULT NULL,
  `opened_at` datetime NOT NULL,
  `closed_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gd_cases`
--

INSERT INTO `gd_cases` (`case_id`, `student_number`, `counselor_id`, `case_number`, `case_type`, `priority`, `status`, `summary`, `opened_at`, `closed_at`, `created_at`, `updated_at`) VALUES
(5, 885, 1007, 'CASE-2026-0001', 'Incident', 'Medium', 'Closed', 'afsdasdsa', '2026-08-04 11:03:09', '2026-08-04 11:26:06', '2026-08-04 03:03:09', '2026-08-04 03:26:06'),
(6, 885, 1007, 'CASE-2026-0002', 'Referral', 'Medium', 'Open', 'dasdsadsads', '2026-08-04 12:10:47', NULL, '2026-08-04 04:10:47', '2026-08-04 04:10:47');

-- --------------------------------------------------------

--
-- Table structure for table `gd_counseling_sessions`
--

CREATE TABLE `gd_counseling_sessions` (
  `session_id` bigint(20) NOT NULL,
  `case_id` bigint(20) NOT NULL,
  `counselor_id` bigint(100) NOT NULL,
  `session_date` datetime NOT NULL,
  `session_type` enum('Academic','Behavioral','Career','Personal','Family','Mental Health') NOT NULL,
  `duration_minutes` int(11) DEFAULT NULL,
  `session_notes` longtext DEFAULT NULL,
  `recommendations` longtext DEFAULT NULL,
  `next_session` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gd_counseling_sessions`
--

INSERT INTO `gd_counseling_sessions` (`session_id`, `case_id`, `counselor_id`, `session_date`, `session_type`, `duration_minutes`, `session_notes`, `recommendations`, `next_session`, `created_at`) VALUES
(4, 5, 1007, '2026-09-08 13:35:00', 'Behavioral', 30, '', '', NULL, '2026-08-08 05:35:42');

-- --------------------------------------------------------

--
-- Table structure for table `gd_counselor_schedules`
--

CREATE TABLE `gd_counselor_schedules` (
  `schedule_id` bigint(20) NOT NULL,
  `counselor_id` bigint(100) NOT NULL,
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `availability` enum('Available','Unavailable') DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gd_incidents`
--

CREATE TABLE `gd_incidents` (
  `incident_id` bigint(20) NOT NULL,
  `student_number` int(250) NOT NULL,
  `reported_by` bigint(100) NOT NULL,
  `case_id` bigint(20) DEFAULT NULL,
  `incident_type` varchar(100) NOT NULL,
  `severity` enum('Minor','Moderate','Major','Critical') DEFAULT 'Minor',
  `incident_date` datetime NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `description` longtext NOT NULL,
  `action_taken` text DEFAULT NULL,
  `action_date` date DEFAULT NULL,
  `status` enum('Reported','Investigating','Resolved','Closed') DEFAULT 'Reported',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gd_incidents`
--

INSERT INTO `gd_incidents` (`incident_id`, `student_number`, `reported_by`, `case_id`, `incident_type`, `severity`, `incident_date`, `location`, `description`, `action_taken`, `action_date`, `status`, `created_at`) VALUES
(9, 885, 1007, NULL, 'sprain', 'Major', '2026-08-02 10:15:00', 'comlab 1', 'sdasdsadsa', 'Asads', '2026-08-04', 'Resolved', '2026-08-02 02:15:28'),
(10, 885, 1007, 5, 'kantutan sa comlab', 'Critical', '2026-08-02 10:22:00', 'comlab 1', 'ghfhgfj', 'dasdasd', '2026-08-04', 'Resolved', '2026-08-02 02:22:34');

-- --------------------------------------------------------

--
-- Table structure for table `gd_referrals`
--

CREATE TABLE `gd_referrals` (
  `referral_id` bigint(20) NOT NULL,
  `case_id` bigint(20) NOT NULL,
  `referred_by` bigint(100) NOT NULL,
  `referral_source` enum('Teacher','Adviser','Principal','Parent','Self','Other') NOT NULL,
  `referral_reason` text NOT NULL,
  `referral_status` enum('Pending','Accepted','Rejected') DEFAULT 'Pending',
  `referral_date` date NOT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gd_referrals`
--

INSERT INTO `gd_referrals` (`referral_id`, `case_id`, `referred_by`, `referral_source`, `referral_reason`, `referral_status`, `referral_date`, `remarks`) VALUES
(1, 6, 1004, 'Other', 'dasdsadsads', 'Pending', '2026-08-04', '');

-- --------------------------------------------------------

--
-- Table structure for table `gd_student_documents`
--

CREATE TABLE `gd_student_documents` (
  `document_id` bigint(20) NOT NULL,
  `student_number` int(250) NOT NULL,
  `uploaded_by` bigint(100) NOT NULL,
  `document_type` varchar(100) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gd_student_documents`
--

INSERT INTO `gd_student_documents` (`document_id`, `student_number`, `uploaded_by`, `document_type`, `file_name`, `file_path`, `uploaded_at`) VALUES
(1, 885, 1007, 'Consent Form', 'GROUP-1-PATIENT-MANAGEMENT (1).pdf', 'uploads/students/885/doc_6a7162cb9728e7.01441374.pdf', '2026-08-04 03:55:55');

-- --------------------------------------------------------

--
-- Table structure for table `gd_student_profiles`
--

CREATE TABLE `gd_student_profiles` (
  `profile_id` bigint(20) NOT NULL,
  `student_number` int(250) NOT NULL,
  `risk_level` enum('Low','Moderate','High') DEFAULT 'Low',
  `guidance_status` enum('Active','Monitoring','Closed') DEFAULT 'Active',
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gd_student_profiles`
--

INSERT INTO `gd_student_profiles` (`profile_id`, `student_number`, `risk_level`, `guidance_status`, `remarks`, `created_at`, `updated_at`) VALUES
(3, 885, 'Low', 'Active', 'adsadasdsad', '2026-08-04 03:03:09', '2026-08-04 03:04:16');

-- --------------------------------------------------------

--
-- Table structure for table `lab_crim_damage`
--

CREATE TABLE `lab_crim_damage` (
  `id` int(10) NOT NULL,
  `category` varchar(120) NOT NULL,
  `code` varchar(120) DEFAULT NULL,
  `description` varchar(120) NOT NULL,
  `status` varchar(120) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lab_crim_inventory`
--

CREATE TABLE `lab_crim_inventory` (
  `id` int(10) NOT NULL,
  `item_img` varchar(120) NOT NULL,
  `category` varchar(120) NOT NULL,
  `total` varchar(120) NOT NULL,
  `available` varchar(120) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lab_crim_inventory`
--

INSERT INTO `lab_crim_inventory` (`id`, `item_img`, `category`, `total`, `available`, `created_at`) VALUES
(3, 'uploads/1773547475_RobloxScreenShot20250628_101209384.png', 'dwadwa', '2', '2', '2026-03-15 04:04:35');

-- --------------------------------------------------------

--
-- Table structure for table `lab_damage`
--

CREATE TABLE `lab_damage` (
  `id` int(10) NOT NULL,
  `category` varchar(120) NOT NULL,
  `code` varchar(120) DEFAULT NULL,
  `description` varchar(120) NOT NULL,
  `status` varchar(120) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lab_damage`
--

INSERT INTO `lab_damage` (`id`, `category`, `code`, `description`, `status`, `created_at`) VALUES
(11, 'Beaker', '1213dwadaw', 'test', 'Unfixed', '2026-03-13 09:32:41'),
(13, 'Test Tube', '12', 'dawdwa1', 'Unfixed', '2026-03-14 17:10:20'),
(15, 'Microscope', '232', 'sfdasf', 'Unfixed', '2026-03-17 09:58:01'),
(16, 'Microscope', '29278', 'xdfsdg', 'Unfixed', '2026-03-17 09:59:21'),
(17, 'Microscope', 'h001', 'gtytrft', 'Fixed', '2026-03-18 06:33:56');

-- --------------------------------------------------------

--
-- Table structure for table `lab_he_borrows`
--

CREATE TABLE `lab_he_borrows` (
  `id` int(120) NOT NULL,
  `borrower` varchar(120) NOT NULL,
  `borrower_name` varchar(120) NOT NULL,
  `lab_type` varchar(120) NOT NULL,
  `item` varchar(120) NOT NULL,
  `quantity` int(120) NOT NULL,
  `borrowed_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `returned_date` datetime NOT NULL,
  `status` varchar(120) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lab_inventory`
--

CREATE TABLE `lab_inventory` (
  `id` int(10) NOT NULL,
  `item_img` varchar(120) DEFAULT NULL,
  `category` varchar(120) NOT NULL,
  `total` int(10) NOT NULL,
  `available` int(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lab_inventory`
--

INSERT INTO `lab_inventory` (`id`, `item_img`, `category`, `total`, `available`, `created_at`) VALUES
(9, 'uploads/1773430077_main-qimg-806eb7c37c1ca2e4ae25e49eefd9d146.jpg', 'test', 2, 23, '2026-03-13 19:27:57');

-- --------------------------------------------------------

--
-- Table structure for table `lab_it_damage`
--

CREATE TABLE `lab_it_damage` (
  `id` int(10) NOT NULL,
  `category` varchar(120) NOT NULL,
  `code` varchar(120) DEFAULT NULL,
  `description` varchar(120) NOT NULL,
  `status` varchar(120) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lab_it_inventory`
--

CREATE TABLE `lab_it_inventory` (
  `id` int(10) NOT NULL,
  `item_img` varchar(120) NOT NULL,
  `lab_type` varchar(120) NOT NULL,
  `category` varchar(120) NOT NULL,
  `code` int(120) DEFAULT NULL,
  `total` int(120) NOT NULL,
  `available` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lab_physic_borrow`
--

CREATE TABLE `lab_physic_borrow` (
  `id` int(120) NOT NULL,
  `borrower` varchar(120) NOT NULL,
  `borrower_name` varchar(120) NOT NULL,
  `item` varchar(120) NOT NULL,
  `quantity` int(120) NOT NULL,
  `borrowed_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `returned_date` datetime NOT NULL,
  `status` varchar(120) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lab_physic_damage`
--

CREATE TABLE `lab_physic_damage` (
  `id` int(10) NOT NULL,
  `category` varchar(120) NOT NULL,
  `code` varchar(120) DEFAULT NULL,
  `description` varchar(120) NOT NULL,
  `status` varchar(120) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lab_physic_inventory`
--

CREATE TABLE `lab_physic_inventory` (
  `id` int(10) NOT NULL,
  `category` varchar(120) NOT NULL,
  `item_img` varchar(120) NOT NULL,
  `total` varchar(120) NOT NULL,
  `available` varchar(120) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lab_physic_inventory`
--

INSERT INTO `lab_physic_inventory` (`id`, `category`, `item_img`, `total`, `available`, `created_at`) VALUES
(4, 'Mouse', 'uploads/1773546202_RobloxScreenShot20250630_011200077.png', '100', '22', '2026-03-15 03:43:22'),
(5, 'Tube Test', 'uploads/1773741447_RobloxScreenShot20250630_011200077.png', '65', '343', '2026-03-17 09:57:27'),
(6, 'Tube Test', 'uploads/1773741460_RobloxScreenShot20250705_021720620.png', '65', '343', '2026-03-17 09:57:40'),
(7, 'Tube Test', 'uploads/1773741469_RobloxScreenShot20250705_021711851.png', '65', '343', '2026-03-17 09:57:49'),
(8, 'fsdfds', 'uploads/1773741541_download.jpg', '121', '12', '2026-03-17 09:59:01'),
(10, 'pobigu', 'uploads/1773815436_RobloxScreenShot20250705_021711851.png', '50', '32', '2026-03-18 06:30:36');

-- --------------------------------------------------------

--
-- Table structure for table `lab_psych_borrow`
--

CREATE TABLE `lab_psych_borrow` (
  `id` int(120) NOT NULL,
  `borrower` varchar(120) NOT NULL,
  `borrower_name` varchar(120) NOT NULL,
  `lab_type` varchar(120) NOT NULL,
  `quantity` int(120) NOT NULL,
  `borrowed_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `returned_date` datetime NOT NULL,
  `status` int(120) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lab_psych_inventory`
--

CREATE TABLE `lab_psych_inventory` (
  `id` int(10) NOT NULL,
  `item_img` varchar(120) NOT NULL,
  `category` varchar(120) NOT NULL,
  `total` int(10) NOT NULL,
  `available` int(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lab_psych_inventory`
--

INSERT INTO `lab_psych_inventory` (`id`, `item_img`, `category`, `total`, `available`, `created_at`) VALUES
(3, 'uploads/1773548260_RobloxScreenShot20250705_021711851.png', 'dwadwa', 2, 1, '2026-03-15 04:17:40');

-- --------------------------------------------------------

--
-- Table structure for table `lbr_activity_log`
--

CREATE TABLE `lbr_activity_log` (
  `id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `user` varchar(100) DEFAULT 'Librarian',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lbr_activity_log`
--

INSERT INTO `lbr_activity_log` (`id`, `action`, `details`, `user`, `created_at`) VALUES
(1, 'System Initialized', 'Library Management System database created with sample data', 'System', '2026-03-15 02:56:43'),
(2, 'Books Added', 'Added 12 sample books to the library collection', 'System', '2026-03-15 02:56:43'),
(3, 'Members Added', 'Added 7 sample library members', 'System', '2026-03-15 02:56:43'),
(4, 'Book Borrowed', '\"Brave New World\" borrowed by David Brown (due 2026-03-22)', 'Librarian', '2026-03-18 06:05:24'),
(5, 'Book Borrowed', '\"The Little Prince\" borrowed by Bob Martinez (due 2026-03-23)', 'Librarian', '2026-03-18 06:05:37'),
(6, 'Book Borrowed', '\"The Hunger Games\" borrowed by Alice Johnson (due 2026-03-20)', 'Librarian', '2026-03-18 06:05:52'),
(7, 'Book Returned', '\"The Hunger Games\" returned (Fine: ₱10)', 'Librarian', '2026-03-18 06:50:43'),
(8, 'Student Imported', 'Imported student \"Dangelo Prohaska Balistreri\" (#4792) from Registrar', 'Librarian', '2026-03-18 06:52:58'),
(9, 'Book Borrowed', '\"Charlotte\'s Web\" borrowed by Dangelo Prohaska Balistreri (due 2026-03-21)', 'Librarian', '2026-03-18 06:55:37'),
(10, 'Student Imported', 'Imported student \"Heather Gibson Marks\" (#3287) from Registrar', 'Librarian', '2026-03-18 07:08:51'),
(11, 'Member Added', 'Added member \"rick roll\" (3516843468)', 'Librarian', '2026-03-18 07:12:08');

-- --------------------------------------------------------

--
-- Table structure for table `lbr_books`
--

CREATE TABLE `lbr_books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `genre` varchar(100) DEFAULT 'General',
  `year` int(11) DEFAULT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('available','borrowed','lost','damaged') DEFAULT 'available',
  `condition` enum('Excellent','Good','Fair','Poor') DEFAULT 'Excellent',
  `cover_url` varchar(500) DEFAULT NULL,
  `cover_local` varchar(255) DEFAULT NULL,
  `added_date` date DEFAULT curdate(),
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lbr_books`
--

INSERT INTO `lbr_books` (`id`, `title`, `author`, `genre`, `year`, `isbn`, `description`, `status`, `condition`, `cover_url`, `cover_local`, `added_date`, `last_updated`) VALUES
(1, 'To Kill a Mockingbird', 'Harper Lee', 'Fiction', 1960, '9780061935466', 'A Pulitzer Prize-winning masterwork of honor and injustice in the deep South—and the heroism of one man in the face of blind and violent hatred.', 'available', 'Excellent', 'https://covers.openlibrary.org/b/isbn/9780061935466-L.jpg', NULL, '2026-03-15', '2026-03-15 02:56:43'),
(2, '1984', 'George Orwell', 'Science Fiction', 1949, '9780451524935', 'A dystopian novel set in a totalitarian society where Big Brother watches everything and independent thought is a crime.', 'available', 'Good', 'https://covers.openlibrary.org/b/isbn/9780451524935-L.jpg', NULL, '2026-03-15', '2026-03-15 02:56:43'),
(3, 'The Great Gatsby', 'F. Scott Fitzgerald', 'Fiction', 1925, '9780743273565', 'A story of the fabulously wealthy Jay Gatsby and his love for the beautiful Daisy Buchanan.', 'borrowed', 'Good', 'https://covers.openlibrary.org/b/isbn/9780743273565-L.jpg', NULL, '2026-03-15', '2026-03-15 02:56:43'),
(4, 'Harry Potter and the Sorcerer\'s Stone', 'J.K. Rowling', 'Fantasy', 1997, '9780590353427', 'The first book in the beloved Harry Potter series following a young wizard\'s journey at Hogwarts School of Witchcraft and Wizardry.', 'available', 'Excellent', 'https://covers.openlibrary.org/b/isbn/9780590353427-L.jpg', NULL, '2026-03-15', '2026-03-15 02:56:43'),
(5, 'The Hunger Games', 'Suzanne Collins', 'Science Fiction', 2008, '9780439023481', 'In a dark future, teenager Katniss Everdeen volunteers to take her sister\'s place in the deadly Hunger Games.', 'available', 'Poor', 'https://covers.openlibrary.org/b/isbn/9780439023481-L.jpg', NULL, '2026-03-15', '2026-03-18 06:50:43'),
(6, 'Pride and Prejudice', 'Jane Austen', 'Romance', 1813, '9780141439518', 'The story of Elizabeth Bennet and her evolving relationship with the proud Mr. Darcy in Regency England.', 'available', 'Excellent', 'https://covers.openlibrary.org/b/isbn/9780141439518-L.jpg', NULL, '2026-03-15', '2026-03-15 02:56:43'),
(7, 'The Hobbit', 'J.R.R. Tolkien', 'Fantasy', 1937, '9780547928227', 'Bilbo Baggins, a hobbit who enjoys a comfortable life, is swept into an epic quest to reclaim treasure from a dragon.', 'available', 'Good', 'https://covers.openlibrary.org/b/isbn/9780547928227-L.jpg', NULL, '2026-03-15', '2026-03-15 02:56:43'),
(8, 'Animal Farm', 'George Orwell', 'Fiction', 1945, '9780452284241', 'A satirical allegorical novella about a group of farm animals who rebel against their human farmer.', 'available', 'Fair', 'https://covers.openlibrary.org/b/isbn/9780452284241-L.jpg', NULL, '2026-03-15', '2026-03-15 02:56:43'),
(9, 'The Catcher in the Rye', 'J.D. Salinger', 'Fiction', 1951, '9780316769174', 'A story about teenage rebellion and alienation told through the eyes of Holden Caulfield.', 'borrowed', 'Good', 'https://covers.openlibrary.org/b/isbn/9780316769174-L.jpg', NULL, '2026-03-15', '2026-03-15 02:56:43'),
(10, 'Brave New World', 'Aldous Huxley', 'Science Fiction', 1932, '9780060850524', 'A dystopian novel set in a futuristic World State where citizens are environmentally engineered into intelligence castes.', 'borrowed', 'Excellent', 'https://covers.openlibrary.org/b/isbn/9780060850524-L.jpg', NULL, '2026-03-15', '2026-03-18 06:05:24'),
(11, 'Charlotte\'s Web', 'E.B. White', 'Children', 1952, '9780064400558', 'The story of a pig named Wilbur and his friendship with a barn spider named Charlotte.', 'borrowed', 'Good', 'https://covers.openlibrary.org/b/isbn/9780064400558-L.jpg', NULL, '2026-03-15', '2026-03-18 06:55:37'),
(12, 'The Little Prince', 'Antoine de Saint-Exupéry', 'Children', 1943, '9780156012195', 'A poetic tale about a young prince who travels from planet to planet and lands on Earth.', 'borrowed', 'Excellent', 'https://covers.openlibrary.org/b/isbn/9780156012195-L.jpg', NULL, '2026-03-15', '2026-03-18 06:05:37');

-- --------------------------------------------------------

--
-- Table structure for table `lbr_borrowers`
--

CREATE TABLE `lbr_borrowers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `borrower_id` varchar(50) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `type` enum('Student','Teacher','Staff') DEFAULT 'Student',
  `grade` varchar(30) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `join_date` date DEFAULT curdate(),
  `active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lbr_borrowers`
--

INSERT INTO `lbr_borrowers` (`id`, `name`, `borrower_id`, `email`, `phone`, `type`, `grade`, `address`, `join_date`, `active`) VALUES
(1, 'Alice Johnson', 'STU-001', 'alice@school.edu', '555-0101', 'Student', 'Grade 10', NULL, '2024-01-15', 1),
(2, 'Bob Martinez', 'STU-002', 'bob@school.edu', '555-0102', 'Student', 'Grade 11', NULL, '2024-01-20', 1),
(3, 'Carol Williams', 'STU-003', 'carol@school.edu', '555-0103', 'Student', 'Grade 9', NULL, '2024-02-01', 1),
(4, 'David Brown', 'STU-004', 'david@school.edu', '555-0104', 'Student', 'Grade 12', NULL, '2024-02-10', 1),
(5, 'Ms. Emily Chen', 'TCH-001', 'emily.chen@school.edu', '555-0201', 'Teacher', 'Science Dept', NULL, '2023-09-01', 1),
(6, 'Mr. James Wilson', 'TCH-002', 'james.wilson@school.edu', '555-0202', 'Teacher', 'English Dept', NULL, '2023-09-01', 1),
(7, 'Sarah Davis', 'STF-001', 'sarah.davis@school.edu', '555-0301', 'Staff', 'Administration', NULL, '2023-08-15', 1),
(8, 'Dangelo Prohaska Balistreri', '4792', NULL, NULL, 'Student', 'BSTM - Year 2002', NULL, '2026-03-18', 1),
(9, 'Heather Gibson Marks', '3287', NULL, NULL, 'Student', 'BSTM - Year 2003', NULL, '2026-03-18', 1),
(10, 'rick roll', '3516843468', 'tenegraverona@gmail.com', NULL, 'Student', 'college bsis', 'pabahay 2000 muzon south', '2026-03-18', 1);

-- --------------------------------------------------------

--
-- Table structure for table `lbr_settings`
--

CREATE TABLE `lbr_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lbr_settings`
--

INSERT INTO `lbr_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'library_name', 'School Library Management System', '2026-03-15 02:56:43'),
(2, 'max_borrow_days', '14', '2026-03-15 02:56:43'),
(3, 'max_books_per_member', '3', '2026-03-15 02:56:43'),
(4, 'daily_fine_rate', '0.50', '2026-03-15 02:56:43'),
(5, 'auto_save', 'true', '2026-03-15 02:56:43');

-- --------------------------------------------------------

--
-- Table structure for table `lbr_transactions`
--

CREATE TABLE `lbr_transactions` (
  `id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `borrower_id` int(11) NOT NULL,
  `borrow_date` date NOT NULL,
  `due_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `status` enum('active','returned','overdue') DEFAULT 'active',
  `condition` varchar(50) DEFAULT NULL,
  `fine` decimal(10,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lbr_transactions`
--

INSERT INTO `lbr_transactions` (`id`, `book_id`, `borrower_id`, `borrow_date`, `due_date`, `return_date`, `status`, `condition`, `fine`, `notes`, `created_at`) VALUES
(1, 3, 1, '2026-03-10', '2026-03-24', NULL, 'overdue', NULL, 53.50, NULL, '2026-03-15 02:56:43'),
(2, 9, 2, '2026-02-23', '2026-03-09', NULL, 'overdue', NULL, 61.00, NULL, '2026-03-15 02:56:43'),
(3, 10, 4, '2026-03-18', '2026-03-22', NULL, 'overdue', NULL, 54.50, '', '2026-03-18 06:05:24'),
(4, 12, 2, '2026-03-18', '2026-03-23', NULL, 'overdue', NULL, 54.00, '', '2026-03-18 06:05:37'),
(5, 5, 1, '2026-03-18', '2026-03-20', '2026-03-18', 'returned', 'Poor', 10.00, '', '2026-03-18 06:05:52'),
(6, 11, 8, '2026-03-18', '2026-03-21', NULL, 'overdue', NULL, 55.00, 'ok', '2026-03-18 06:55:37');

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
-- Table structure for table `rgr_activity_log`
--

CREATE TABLE `rgr_activity_log` (
  `id` int(10) NOT NULL,
  `user_id` int(10) DEFAULT NULL,
  `action` varchar(250) NOT NULL,
  `description` varchar(250) NOT NULL,
  `ip_address` varchar(250) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rgr_activity_log`
--

INSERT INTO `rgr_activity_log` (`id`, `user_id`, `action`, `description`, `ip_address`, `created_at`, `updated_at`) VALUES
(9, NULL, 'Get A PDF all student Report', 'Downloading a PDF file contains all students information', '::1', '2026-02-23 18:47:18', '2026-02-23 18:47:18'),
(10, NULL, 'Get A eXCEL all student Report', 'Downloading a Excel file contains all students information', '::1', '2026-02-25 06:46:53', '2026-02-25 06:46:53'),
(11, NULL, 'Get A PDF all student Report', 'Downloading a PDF file contains all students information', '127.0.0.1', '2026-02-25 09:00:35', '2026-02-25 09:00:35'),
(12, NULL, 'Get A PDF all student Report', 'Downloading a PDF file contains all students information', '127.0.0.1', '2026-02-25 09:02:21', '2026-02-25 09:02:21'),
(13, NULL, 'Get A PDF all student Report', 'Downloading a PDF file contains all students information', '::1', '2026-02-25 09:05:28', '2026-02-25 09:05:28'),
(14, NULL, 'Get A PDF all student Report', 'Downloading a PDF file contains all students information', '::1', '2026-02-25 09:05:51', '2026-02-25 09:05:51'),
(42, NULL, 'Created A New Course', 'Created A New Course Information for System', '::1', '2026-02-26 20:35:42', '2026-02-26 20:35:42'),
(43, NULL, 'Get A PDF Course student Report', 'Downloading a PDF file contains Course Information', '::1', '2026-02-26 20:38:49', '2026-02-26 20:38:49'),
(44, NULL, 'Created A New Course', 'Created A New Course Information for System', '::1', '2026-02-26 20:40:35', '2026-02-26 20:40:35'),
(45, NULL, 'Created A New Course', 'Created A New Course Information for System', '::1', '2026-02-26 21:23:02', '2026-02-26 21:23:02'),
(66, NULL, 'Created A New Course', 'Created A New Course Information for System', '::1', '2026-02-28 02:23:34', '2026-02-28 02:23:34'),
(75, NULL, 'Delete Course Input', 'Deleting 1 item/s Course Information', '::1', '2026-02-28 18:03:07', '2026-02-28 18:03:07'),
(76, NULL, 'Delete Course Input', 'Deleting 3 item/s Course Information', '::1', '2026-02-28 18:03:19', '2026-02-28 18:03:19'),
(77, NULL, 'Created A New Course', 'Created A New Course Information for System', '::1', '2026-02-28 18:03:41', '2026-02-28 18:03:41'),
(78, NULL, 'Delete Course Input', 'Deleting 1 item/s Course Information', '::1', '2026-02-28 18:03:48', '2026-02-28 18:03:48'),
(79, NULL, 'Created A New Course', 'Created A New Course Information for System', '::1', '2026-02-28 18:20:39', '2026-02-28 18:20:39'),
(80, NULL, 'Created A New Course', 'Created A New Course Information for System', '::1', '2026-02-28 20:08:42', '2026-02-28 20:08:42'),
(81, NULL, 'Delete Course Input', 'Deleting 2 item/s Course Information', '::1', '2026-02-28 20:14:47', '2026-02-28 20:14:47'),
(82, NULL, 'Created A New Course', 'Created A New Course Information for System', '::1', '2026-02-28 20:23:28', '2026-02-28 20:23:28'),
(83, NULL, 'Created A New Course', 'Created A New Course Information for System', '::1', '2026-02-28 20:24:32', '2026-02-28 20:24:32'),
(84, NULL, 'Delete Course Input', 'Deleting 2 item/s Course Information', '::1', '2026-03-01 15:05:54', '2026-03-01 15:05:54'),
(114, NULL, 'Created A New School Year', 'Created A New Schoool Year Information for System', '::1', '2026-03-02 18:45:05', '2026-03-02 18:45:05'),
(115, NULL, 'Created A New Course', 'Created A New Course Information for System', '::1', '2026-03-02 18:45:52', '2026-03-02 18:45:52'),
(116, NULL, 'Created A New School Year', 'Created A New Schoool Year Information for System', '::1', '2026-03-02 19:03:48', '2026-03-02 19:03:48'),
(117, NULL, 'Created A New School Year', 'Created A New Schoool Year Information for System', '::1', '2026-03-02 19:25:59', '2026-03-02 19:25:59'),
(118, NULL, 'Created A New School Year', 'Created A New Schoool Year Information for System', '::1', '2026-03-02 21:39:05', '2026-03-02 21:39:05'),
(119, NULL, 'Created A New School Year', 'Created A New Schoool Year Information for System', '::1', '2026-03-02 21:39:14', '2026-03-02 21:39:14'),
(120, NULL, 'Created A New School Year', 'Created A New Schoool Year Information for System', '::1', '2026-03-02 21:39:23', '2026-03-02 21:39:23'),
(121, NULL, 'Updated A New School Year', 'Updated A New Schoool Year Information for System', '::1', '2026-03-02 22:01:53', '2026-03-02 22:01:53'),
(122, NULL, 'Delete Course Input', 'Deleting 1 item/s Course Information', '::1', '2026-03-02 22:02:12', '2026-03-02 22:02:12'),
(123, NULL, 'Updated A New School Year', 'Updated A New Schoool Year Information for System', '::1', '2026-03-03 15:06:56', '2026-03-03 15:06:56'),
(124, NULL, 'Updated A New School Year', 'Updated A New Schoool Year Information for System', '::1', '2026-03-03 15:07:16', '2026-03-03 15:07:16'),
(125, NULL, 'Created A New School Year', 'Created A New Schoool Year Information for System', '::1', '2026-03-03 15:07:29', '2026-03-03 15:07:29'),
(126, NULL, 'Updated A New School Year', 'Updated A New Schoool Year Information for System', '::1', '2026-03-03 15:07:36', '2026-03-03 15:07:36'),
(127, NULL, 'Created A New Course', 'Created A New Course Information for System', '::1', '2026-03-03 15:23:36', '2026-03-03 15:23:36'),
(128, NULL, 'Delete Course Input', 'Deleting 1 item/s Course Information', '::1', '2026-03-03 15:23:45', '2026-03-03 15:23:45'),
(129, NULL, 'Get A PDF all student Report', 'Downloading a PDF file contains all students information', '::1', '2026-03-04 04:28:21', '2026-03-04 04:28:21'),
(130, NULL, 'Get A PDF all student Report', 'Downloading a PDF file contains all students information', '::1', '2026-03-04 04:29:30', '2026-03-04 04:29:30'),
(131, NULL, 'Get A PDF all student Report', 'Downloading a PDF file contains all students information', '::1', '2026-03-04 04:29:32', '2026-03-04 04:29:32'),
(132, NULL, 'Get A PDF all student Report', 'Downloading a PDF file contains all students information', '::1', '2026-03-04 04:29:33', '2026-03-04 04:29:33'),
(133, NULL, 'Get A PDF all student Report', 'Downloading a PDF file contains all students information', '::1', '2026-03-04 04:29:43', '2026-03-04 04:29:43'),
(134, NULL, 'Get A PDF all student Report', 'Downloading a PDF file contains all students information', '::1', '2026-03-04 04:29:54', '2026-03-04 04:29:54'),
(135, NULL, 'Get A PDF all student Report', 'Downloading a PDF file contains all students information', '::1', '2026-03-04 04:32:56', '2026-03-04 04:32:56'),
(136, NULL, 'Get A PDF all student Report', 'Downloading a PDF file contains all students information', '::1', '2026-03-04 04:33:06', '2026-03-04 04:33:06'),
(137, NULL, 'Get A PDF all student Report', 'Downloading a PDF file contains all students information', '::1', '2026-03-04 04:33:44', '2026-03-04 04:33:44'),
(138, NULL, 'Get A PDF all student Report', 'Downloading a PDF file contains all students information', '::1', '2026-03-04 04:35:41', '2026-03-04 04:35:41'),
(139, NULL, 'Get A PDF all student Report', 'Downloading a PDF file contains all students information', '::1', '2026-03-04 04:36:53', '2026-03-04 04:36:53'),
(140, NULL, 'Get A PDF all student Report', 'Downloading a PDF file contains all students information', '::1', '2026-03-04 04:36:53', '2026-03-04 04:36:53'),
(141, NULL, 'Get A PDF all student Report', 'Downloading a PDF file contains all students information', '::1', '2026-03-04 04:42:10', '2026-03-04 04:42:10'),
(142, NULL, 'Get A PDF all student Report', 'Downloading a PDF file contains all students information', '::1', '2026-03-04 04:42:21', '2026-03-04 04:42:21'),
(143, NULL, 'Get A PDF all student Report', 'Downloading a PDF file contains all students information', '::1', '2026-03-04 04:42:23', '2026-03-04 04:42:23'),
(164, NULL, 'Get A PDF Course student Report', 'Downloading a PDF file contains Course Information', '::1', '2026-03-04 07:32:50', '2026-03-04 07:32:50'),
(165, NULL, 'Get A PDF all student Report', 'Downloading a PDF file contains all students information', '::1', '2026-03-04 07:33:07', '2026-03-04 07:33:07'),
(172, NULL, 'Deleted A New School Year', 'Deleted A New Schoool Year Information for System', '::1', '2026-03-05 00:16:41', '2026-03-05 00:16:41'),
(173, NULL, 'Created A New School Year', 'Created A New Schoool Year Information for System', '::1', '2026-03-05 01:25:00', '2026-03-05 01:25:00'),
(174, NULL, 'Deleted A Semester Record', 'Deleted A Semester Record Information for System', '::1', '2026-03-05 01:26:01', '2026-03-05 01:26:01'),
(175, NULL, 'Created A New School Year', 'Created A New Schoool Year Information for System', '::1', '2026-03-05 01:26:13', '2026-03-05 01:26:13'),
(176, NULL, 'Created A New School Year', 'Created A New Schoool Year Information for System', '::1', '2026-03-05 01:26:27', '2026-03-05 01:26:27'),
(177, NULL, 'Updated A New School Year', 'Updated A New Schoool Year Information for System', '::1', '2026-03-05 01:26:41', '2026-03-05 01:26:41'),
(178, NULL, 'Created A New School Year', 'Created A New Schoool Year Information for System', '::1', '2026-03-05 06:38:03', '2026-03-05 06:38:03'),
(179, NULL, 'Created A New School Year', 'Created A New Schoool Year Information for System', '::1', '2026-03-05 06:38:24', '2026-03-05 06:38:24'),
(180, NULL, 'Updated A New School Year', 'Updated A New Schoool Year Information for System', '::1', '2026-03-05 06:46:31', '2026-03-05 06:46:31'),
(181, NULL, 'Updated A New School Year', 'Updated A New Schoool Year Information for System', '::1', '2026-03-05 06:47:01', '2026-03-05 06:47:01'),
(182, NULL, 'Get A PDF Course student Report', 'Downloading a PDF file contains Course Information', '::1', '2026-03-05 06:47:11', '2026-03-05 06:47:11'),
(183, NULL, 'Get A PDF Course student Report', 'Downloading a PDF file contains Course Information', '::1', '2026-03-05 07:06:52', '2026-03-05 07:06:52'),
(184, NULL, 'Get A PDF Course student Report', 'Downloading a PDF file contains Course Information', '::1', '2026-03-05 07:07:18', '2026-03-05 07:07:18'),
(185, NULL, 'Get A PDF Course student Report', 'Downloading a PDF file contains Course Information', '::1', '2026-03-05 07:07:27', '2026-03-05 07:07:27'),
(186, NULL, 'Get A PDF Course student Report', 'Downloading a PDF file contains Course Information', '::1', '2026-03-05 07:08:03', '2026-03-05 07:08:03'),
(187, NULL, 'Get A PDF Course student Report', 'Downloading a PDF file contains Course Information', '::1', '2026-03-05 07:10:41', '2026-03-05 07:10:41'),
(188, NULL, 'Get A PDF Course student Report', 'Downloading a PDF file contains Course Information', '::1', '2026-03-05 07:25:52', '2026-03-05 07:25:52'),
(189, NULL, 'Get A PDF Course student Report', 'Downloading a PDF file contains Course Information', '::1', '2026-03-05 07:26:08', '2026-03-05 07:26:08'),
(190, NULL, 'Get A PDF Course student Report', 'Downloading a PDF file contains Course Information', '::1', '2026-03-05 07:26:57', '2026-03-05 07:26:57'),
(213, NULL, 'Get A CSV of School Year Report', 'Downloading a CSV file contains School Year Information', '::1', '2026-03-05 19:24:38', '2026-03-05 19:24:38'),
(214, NULL, 'Created A New Course', 'Created A New Course Information for System', '::1', '2026-03-05 19:32:23', '2026-03-05 19:32:23'),
(215, NULL, 'Updated A Course', 'Updated the Course Information.', '::1', '2026-03-05 19:32:42', '2026-03-05 19:32:42'),
(216, NULL, 'Get A PDF of School Year Report', 'Downloading a PDF file contains School Year Information', '::1', '2026-03-05 19:32:50', '2026-03-05 19:32:50'),
(217, NULL, 'Get A PDF all student Report', 'Downloading a PDF file contains all students information', '::1', '2026-03-05 21:56:45', '2026-03-05 21:56:45'),
(218, NULL, 'Get A PDF Course student Report', 'Downloading a PDF file contains Course Information', '::1', '2026-03-05 21:57:04', '2026-03-05 21:57:04'),
(219, NULL, 'Updated A Course', 'Updated the Course Information.', '::1', '2026-03-06 03:03:40', '2026-03-06 03:03:40'),
(220, NULL, 'Updated A Course', 'Updated the Course Information.', '::1', '2026-03-06 03:03:46', '2026-03-06 03:03:46'),
(221, NULL, 'Get A PDF Course student Report', 'Downloading a PDF file contains Course Information', '::1', '2026-03-06 03:03:50', '2026-03-06 03:03:50'),
(222, NULL, 'Delete Course Input', 'Deleting 1 item/s Course Information', '::1', '2026-03-06 03:05:30', '2026-03-06 03:05:30'),
(223, NULL, 'Created A New Course', 'Created A New Course Information for System', '::1', '2026-03-06 03:06:02', '2026-03-06 03:06:02'),
(224, NULL, 'Get A Excel Course student Report', 'Downloading a Excel file contains Course Information', '::1', '2026-03-06 03:16:42', '2026-03-06 03:16:42'),
(225, NULL, 'Created A New Course', 'Created A New Course Information for System', '::1', '2026-03-06 03:24:15', '2026-03-06 03:24:15'),
(226, NULL, 'Created A New School Year', 'Created A New Schoool Year Information for System', '::1', '2026-03-06 03:38:01', '2026-03-06 03:38:01'),
(227, NULL, 'Deleted A New School Year', 'Deleted A New Schoool Year Information for System', '::1', '2026-03-06 03:45:31', '2026-03-06 03:45:31'),
(232, NULL, 'Get A CSV Course student Report', 'Downloading a CSV file contains Course Information', '::1', '2026-03-06 07:10:29', '2026-03-06 07:10:29'),
(233, NULL, 'Deleted A Semester Record', 'Deleted A Semester Record Information for System', '::1', '2026-03-06 17:40:13', '2026-03-06 17:40:13'),
(234, NULL, 'Get A PDF all student Report', 'Downloading a PDF file contains all students information', '::1', '2026-03-06 17:48:32', '2026-03-06 17:48:32'),
(235, NULL, 'Created A New School Semester', 'Created A New Schoool Semester Information for System', '::1', '2026-03-06 18:07:39', '2026-03-06 18:07:39'),
(236, NULL, 'Created A New Strand', 'Created A New Strand Information for System', '::1', '2026-03-06 18:31:19', '2026-03-06 18:31:19'),
(237, NULL, 'Created A New Strand', 'Created A New Strand Information for System', '::1', '2026-03-06 18:41:52', '2026-03-06 18:41:52'),
(238, NULL, 'Created A New Strand', 'Created A New Strand Information for System', '::1', '2026-03-06 18:44:12', '2026-03-06 18:44:12'),
(239, NULL, 'Created A New Strand', 'Created A New Strand Information for System', '::1', '2026-03-06 18:44:45', '2026-03-06 18:44:45'),
(240, NULL, 'Get A CSV Course student Report', 'Downloading a CSV file contains Course Information', '::1', '2026-03-06 18:44:53', '2026-03-06 18:44:53'),
(241, NULL, 'Delete Course Input', 'Deleting 1 item/s Course Information', '::1', '2026-03-06 18:47:26', '2026-03-06 18:47:26'),
(242, NULL, 'Delete Course Input', 'Deleting 1 item/s Course Information', '::1', '2026-03-06 18:47:43', '2026-03-06 18:47:43'),
(243, NULL, 'Delete Course Input', 'Deleting 1 item/s Course Information', '::1', '2026-03-06 18:47:44', '2026-03-06 18:47:44'),
(244, NULL, 'Delete Strand Input', 'Deleting 1 item/s Course Information', '::1', '2026-03-06 18:48:36', '2026-03-06 18:48:36'),
(245, NULL, 'Delete Strand Input', 'Deleting 1 item/s Course Information', '::1', '2026-03-06 18:48:41', '2026-03-06 18:48:41'),
(246, NULL, 'Delete Strand Input', 'Deleting 2 item/s Course Information', '::1', '2026-03-06 18:48:49', '2026-03-06 18:48:49'),
(247, NULL, 'Updated A Strand', 'Updated the Strand Information.', '::1', '2026-03-06 19:23:49', '2026-03-06 19:23:49'),
(248, NULL, 'Updated A Strand', 'Updated the Strand Information.', '::1', '2026-03-06 19:23:51', '2026-03-06 19:23:51'),
(249, NULL, 'Updated A Strand', 'Updated the Strand Information.', '::1', '2026-03-06 19:24:02', '2026-03-06 19:24:02'),
(250, NULL, 'Updated A Strand', 'Updated the Strand Information.', '::1', '2026-03-06 19:24:19', '2026-03-06 19:24:19'),
(251, NULL, 'Updated A Strand', 'Updated the Strand Information.', '::1', '2026-03-06 19:24:38', '2026-03-06 19:24:38'),
(252, NULL, 'Updated A Strand', 'Updated the Strand Information.', '::1', '2026-03-06 19:25:23', '2026-03-06 19:25:23'),
(253, NULL, 'Updated A Strand', 'Updated the Strand Information.', '::1', '2026-03-06 19:25:40', '2026-03-06 19:25:40'),
(254, NULL, 'Created A New Strand', 'Created A New Strand Information for System', '::1', '2026-03-06 19:26:51', '2026-03-06 19:26:51'),
(255, NULL, 'Delete Strand Input', 'Deleting 1 item/s Course Information', '::1', '2026-03-06 19:26:59', '2026-03-06 19:26:59'),
(258, NULL, 'Get A PDF of Semester Report', 'Downloading a PDF file contains Semester Information', '::1', '2026-03-07 18:32:41', '2026-03-07 18:32:41'),
(259, NULL, 'Get A PDF of Semester Report', 'Downloading a PDF file contains Semester Information', '::1', '2026-03-07 18:33:42', '2026-03-07 18:33:42'),
(260, NULL, 'Get A PDF of Semester Report', 'Downloading a PDF file contains Semester Information', '::1', '2026-03-07 18:33:58', '2026-03-07 18:33:58'),
(261, NULL, 'Get A PDF of Strands Report', 'Downloading a PDF file contains Strands Information', '::1', '2026-03-07 18:34:38', '2026-03-07 18:34:38'),
(262, NULL, 'Get A PDF of Strands Report', 'Downloading a PDF file contains Strands Information', '::1', '2026-03-07 18:34:54', '2026-03-07 18:34:54'),
(263, NULL, 'Get A Excel of Strand Report', 'Downloading a Excel file contains Semester Information', '::1', '2026-03-07 18:40:30', '2026-03-07 18:40:30'),
(264, NULL, 'Get A CSV of School Year Report', 'Downloading a CSV file contains School Year Information', '::1', '2026-03-07 18:43:15', '2026-03-07 18:43:15'),
(265, NULL, 'Updated A Strand', 'Updated the Strand Information.', '::1', '2026-03-07 18:44:04', '2026-03-07 18:44:04'),
(266, NULL, 'Updated A Strand', 'Updated the Strand Information.', '::1', '2026-03-07 18:44:09', '2026-03-07 18:44:09'),
(267, NULL, 'Get A CSV of Strand Report', 'Downloading a CSV file contains Strand Information', '::1', '2026-03-07 18:47:48', '2026-03-07 18:47:48'),
(268, NULL, 'Get A Excel of Strand Report', 'Downloading a Excel file contains Semester Information', '::1', '2026-03-07 18:48:12', '2026-03-07 18:48:12'),
(269, NULL, 'Delete Course Input', 'Deleting 1 item/s Course Information', '::1', '2026-03-08 05:36:43', '2026-03-08 05:36:43'),
(270, NULL, 'Get A PDF all student Report', 'Downloading a PDF file contains all students information', '::1', '2026-03-09 14:52:05', '2026-03-09 14:52:05'),
(271, NULL, 'Get A PDF all student Report', 'Downloading a PDF file contains all students information', '::1', '2026-03-09 15:59:37', '2026-03-09 15:59:37'),
(272, NULL, 'Get A PDF all student Report', 'Downloading a PDF file contains all students information', '::1', '2026-03-09 16:05:23', '2026-03-09 16:05:23'),
(275, NULL, 'Delete Course Input', 'Deleting 1 item/s Course Information', '::1', '2026-03-11 08:04:21', '2026-03-11 08:04:21'),
(276, NULL, 'Get A PDF Course student Report', 'Downloading a PDF file contains Course Information', '::1', '2026-03-11 08:55:21', '2026-03-11 08:55:21'),
(277, NULL, 'Get A PDF Course student Report', 'Downloading a PDF file contains Course Information', '::1', '2026-03-11 09:01:14', '2026-03-11 09:01:14'),
(278, NULL, 'Get A PDF of School Year Report', 'Downloading a PDF file contains School Year Information', '::1', '2026-03-11 09:17:13', '2026-03-11 09:17:13'),
(279, NULL, 'Created A New Subject', 'Created A New Subject Information for System', '::1', '2026-03-12 03:25:14', '2026-03-12 03:25:14'),
(280, NULL, 'Created A New Subject', 'Created A New Subject Information for System', '::1', '2026-03-12 03:25:18', '2026-03-12 03:25:18'),
(281, NULL, 'Created A New Subject', 'Created A New Subject Information for System', '::1', '2026-03-12 03:25:28', '2026-03-12 03:25:28'),
(282, NULL, 'Created A New Subject', 'Created A New Subject Information for System', '::1', '2026-03-12 03:25:29', '2026-03-12 03:25:29'),
(283, NULL, 'Created A New Subject', 'Created A New Subject Information for System', '::1', '2026-03-12 03:25:29', '2026-03-12 03:25:29'),
(284, NULL, 'Created A New Subject', 'Created A New Subject Information for System', '::1', '2026-03-12 03:26:25', '2026-03-12 03:26:25'),
(285, NULL, 'Get A CSV Course student Report', 'Downloading a CSV file contains Course Information', '::1', '2026-03-12 03:26:43', '2026-03-12 03:26:43'),
(286, NULL, 'Created A New Subject', 'Created A New Subject Information for System', '::1', '2026-03-12 03:27:06', '2026-03-12 03:27:06'),
(287, NULL, 'Delete Subject Input', 'Deleting 2 item/s Course Information', '::1', '2026-03-12 03:32:19', '2026-03-12 03:32:19'),
(288, NULL, 'Delete Subject Input', 'Deleting 1 item/s Course Information', '::1', '2026-03-12 03:34:12', '2026-03-12 03:34:12'),
(289, NULL, 'Delete Subject Input', 'Deleting 1 item/s Course Information', '::1', '2026-03-12 03:36:02', '2026-03-12 03:36:02'),
(290, NULL, 'Delete Subject Input', 'Deleting 1 item/s Subject Information', '::1', '2026-03-12 03:39:37', '2026-03-12 03:39:37'),
(291, NULL, 'Delete Subject Input', 'Deleting 1 item/s Subject Information', '::1', '2026-03-12 03:41:40', '2026-03-12 03:41:40'),
(292, NULL, 'Delete Subject Input', 'Deleting 1 item/s Subject Information', '::1', '2026-03-12 03:42:39', '2026-03-12 03:42:39'),
(293, NULL, 'Delete Strand Input', 'Deleting 1 item/s Course Information', '::1', '2026-03-12 03:44:39', '2026-03-12 03:44:39'),
(294, NULL, 'Delete Subject Input', 'Deleting 1 item/s Subject Information', '::1', '2026-03-12 03:47:49', '2026-03-12 03:47:49'),
(295, NULL, 'Get A CSV Course student Report', 'Downloading a CSV file contains Course Information', '::1', '2026-03-12 03:48:23', '2026-03-12 03:48:23'),
(296, NULL, 'Delete Subject Input', 'Deleting 1 item/s Subject Information', '::1', '2026-03-12 03:49:19', '2026-03-12 03:49:19'),
(297, NULL, 'Delete Subject Input', 'Deleting 1 item/s Subject Information', '::1', '2026-03-12 03:49:54', '2026-03-12 03:49:54'),
(298, NULL, 'Delete Subject Input', 'Deleting 1 item/s Subject Information', '::1', '2026-03-12 03:51:40', '2026-03-12 03:51:40'),
(299, NULL, 'Delete Subject Input', 'Deleting 1 item/s Subject Information', '::1', '2026-03-12 03:53:50', '2026-03-12 03:53:50'),
(300, NULL, 'Delete Subject Input', 'Deleting 1 item/s Subject Information', '::1', '2026-03-12 03:54:14', '2026-03-12 03:54:14'),
(301, NULL, 'Delete Subject Input', 'Deleting 1 item/s Subject Information', '::1', '2026-03-12 03:54:21', '2026-03-12 03:54:21'),
(302, NULL, 'Delete Subject Input', 'Deleting 1 item/s Subject Information', '::1', '2026-03-12 03:55:21', '2026-03-12 03:55:21'),
(303, NULL, 'Delete Subject Input', 'Deleting 1 item/s Subject Information', '::1', '2026-03-12 03:55:26', '2026-03-12 03:55:26'),
(304, NULL, 'Delete Subject Input', 'Deleting 1 item/s Subject Information', '::1', '2026-03-12 03:57:10', '2026-03-12 03:57:10'),
(305, NULL, 'Deleted A Semester Record', 'Deleted A Semester Record Information for System', '::1', '2026-03-12 03:59:14', '2026-03-12 03:59:14'),
(306, NULL, 'Delete Subject Input', 'Deleting 3 item/s Subject Information', '::1', '2026-03-12 04:01:04', '2026-03-12 04:01:04'),
(307, NULL, 'Delete Subject Input', 'Deleting 4 item/s Subject Information', '::1', '2026-03-12 04:01:16', '2026-03-12 04:01:16'),
(308, NULL, 'Created A New Subject', 'Created A New Subject Information for System', '::1', '2026-03-12 04:01:50', '2026-03-12 04:01:50'),
(309, NULL, 'Get A PDF all student Report', 'Downloading a PDF file contains all students information', '::1', '2026-03-15 03:33:15', '2026-03-15 03:33:15'),
(310, NULL, 'Created A New Course', 'Created A New Course Information for System', '::1', '2026-03-15 04:37:52', '2026-03-15 04:37:52'),
(311, NULL, 'Delete Course Input', 'Deleting 1 item/s Course Information', '::1', '2026-03-15 04:38:00', '2026-03-15 04:38:00'),
(312, NULL, 'Created A New School Year', 'Created A New Schoool Year Information for System', '::1', '2026-03-15 04:38:15', '2026-03-15 04:38:15'),
(313, NULL, 'Deleted A New School Year', 'Deleted A New Schoool Year Information for System', '::1', '2026-03-15 04:38:22', '2026-03-15 04:38:22'),
(316, NULL, 'Get A PDF all student Report', 'Downloading a PDF file contains all students information', '::1', '2026-03-15 04:40:59', '2026-03-15 04:40:59'),
(317, NULL, 'Get A PDF all student Report', 'Downloading a PDF file contains all students information', '::1', '2026-03-15 04:41:04', '2026-03-15 04:41:04'),
(318, NULL, 'Get A PDF Course student Report', 'Downloading a PDF file contains Course Information', '::1', '2026-03-15 04:41:14', '2026-03-15 04:41:14'),
(319, NULL, 'Updated A New School Year', 'Updated A New Schoool Year Information for System', '::1', '2026-03-15 04:42:33', '2026-03-15 04:42:33'),
(320, NULL, 'Get A PDF all student Report', 'Downloading a PDF file contains all students information', '::1', '2026-03-15 04:42:43', '2026-03-15 04:42:43'),
(321, NULL, 'Get A PDF all student Report', 'Downloading a PDF file contains all students information', '::1', '2026-03-15 06:42:46', '2026-03-15 06:42:46'),
(322, NULL, 'Get A PDF all student Report', 'Downloading a PDF file contains all students information', '::1', '2026-03-15 06:44:12', '2026-03-15 06:44:12'),
(323, NULL, 'Created A New Report Approval', 'Created A New Report Approval for System', '::1', '2026-03-17 07:01:17', '2026-03-17 07:01:17'),
(324, NULL, 'Get A PDF all student Report', 'Downloading a PDF file contains all students information', '192.168.68.139', '2026-03-17 09:02:27', '2026-03-17 09:02:27'),
(325, NULL, 'Updated A New School Year', 'Updated A New Schoool Year Information for System', '192.168.68.139', '2026-03-17 09:02:44', '2026-03-17 09:02:44'),
(326, NULL, 'Get A PDF all student Report', 'Downloading a PDF file contains all students information', '192.168.68.139', '2026-03-17 09:02:47', '2026-03-17 09:02:47'),
(327, NULL, 'Updated A New School Year', 'Updated A New Schoool Year Information for System', '192.168.68.139', '2026-03-17 09:02:55', '2026-03-17 09:02:55'),
(328, NULL, 'Get A PDF all student Report', 'Downloading a PDF file contains all students information', '192.168.68.139', '2026-03-17 09:02:57', '2026-03-17 09:02:57'),
(329, NULL, 'Created A New Strand', 'Created A New Strand Information for System', '192.168.68.139', '2026-03-17 09:05:25', '2026-03-17 09:05:25'),
(330, NULL, 'Delete Strand Input', 'Deleting 1 item/s Course Information', '192.168.68.139', '2026-03-17 09:05:53', '2026-03-17 09:05:53'),
(331, NULL, 'Created A New Subject', 'Created A New Subject Information for System', '192.168.68.139', '2026-03-17 09:06:35', '2026-03-17 09:06:35'),
(332, NULL, 'Created A New Subject', 'Created A New Subject Information for System', '192.168.68.139', '2026-03-17 09:08:02', '2026-03-17 09:08:02'),
(333, NULL, 'Delete Subject Input', 'Deleting 1 item/s Subject Information', '192.168.68.139', '2026-03-17 09:08:10', '2026-03-17 09:08:10'),
(334, NULL, 'Created A New Course', 'Created A New Course Information for System', '192.168.68.139', '2026-03-17 10:00:22', '2026-03-17 10:00:22'),
(335, NULL, 'Delete Course Input', 'Deleting 1 item/s Course Information', '192.168.68.139', '2026-03-17 10:00:28', '2026-03-17 10:00:28'),
(336, NULL, 'Delete Subject Input', 'Deleting 1 item/s Subject Information', '192.168.68.139', '2026-03-17 10:00:42', '2026-03-17 10:00:42'),
(337, NULL, 'Created A New Report Approval', 'Created A New Report Approval for System', '::1', '2026-03-17 13:09:17', '2026-03-17 13:09:17'),
(338, NULL, 'Created A New Course', 'Created A New Course Information for System', '10.110.161.222', '2026-03-18 04:56:18', '2026-03-18 04:56:18'),
(339, NULL, 'Updated A Course', 'Updated the Course Information.', '10.110.161.222', '2026-03-18 05:15:58', '2026-03-18 05:15:58'),
(340, NULL, 'Updated A Course', 'Updated the Course Information.', '10.110.161.222', '2026-03-18 05:16:04', '2026-03-18 05:16:04'),
(341, NULL, 'Get A PDF all student Report', 'Downloading a PDF file contains all students information', '10.110.161.222', '2026-03-18 05:16:24', '2026-03-18 05:16:24'),
(342, NULL, 'Updated A New School Year', 'Updated A New Schoool Year Information for System', '10.110.161.222', '2026-03-18 05:16:38', '2026-03-18 05:16:38'),
(343, NULL, 'Get A PDF all student Report', 'Downloading a PDF file contains all students information', '10.110.161.222', '2026-03-18 05:16:42', '2026-03-18 05:16:42'),
(344, NULL, 'Updated A New School Year', 'Updated A New Schoool Year Information for System', '10.110.161.222', '2026-03-18 05:16:58', '2026-03-18 05:16:58');

-- --------------------------------------------------------

--
-- Table structure for table `rgr_class_offerings`
--

CREATE TABLE `rgr_class_offerings` (
  `id` int(10) NOT NULL,
  `subject_id` int(10) NOT NULL,
  `semester_id` int(10) NOT NULL,
  `teacher_id` int(10) NOT NULL,
  `room_id` int(10) NOT NULL,
  `year_level` varchar(250) NOT NULL,
  `strand_id` int(10) DEFAULT NULL,
  `course_id` int(10) DEFAULT NULL,
  `section_name` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rgr_class_schedules`
--

CREATE TABLE `rgr_class_schedules` (
  `id` int(10) NOT NULL,
  `class_offering_id` int(10) NOT NULL,
  `day` varchar(250) NOT NULL,
  `start_time` date NOT NULL,
  `end_time` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rgr_courses`
--

CREATE TABLE `rgr_courses` (
  `id` int(10) NOT NULL,
  `code` varchar(250) NOT NULL,
  `name` varchar(250) NOT NULL,
  `years` int(10) NOT NULL,
  `description` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rgr_courses`
--

INSERT INTO `rgr_courses` (`id`, `code`, `name`, `years`, `description`) VALUES
(26, 'BSIS', 'Bachelor of Science in Information Systems', 4, ''),
(34, 'BSHM', 'Bachelor of Science in Hospitality Management', 4, ''),
(35, 'BSCrim', 'Bachelor of Science in Criminology', 4, ''),
(38, 'BSIT', 'Bachelor of Science in Information Technology', 1, ''),
(41, 'BSPsych', 'Bachelor of Science in Psychology', 4, '');

-- --------------------------------------------------------

--
-- Table structure for table `rgr_curriculums`
--

CREATE TABLE `rgr_curriculums` (
  `id` int(10) NOT NULL,
  `course_id` int(10) DEFAULT NULL,
  `curriculum_name` varchar(120) NOT NULL,
  `effective_year` int(10) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rgr_curriculum_subjects`
--

CREATE TABLE `rgr_curriculum_subjects` (
  `id` int(10) NOT NULL,
  `curriculum_id` int(10) NOT NULL,
  `subject_id` int(10) NOT NULL,
  `year_level` int(10) NOT NULL,
  `semester` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rgr_document_requests`
--

CREATE TABLE `rgr_document_requests` (
  `id` int(10) NOT NULL,
  `request_number` varchar(250) NOT NULL,
  `student_id` int(10) DEFAULT NULL,
  `document_type` enum('TOR','COR') NOT NULL,
  `semester_id` int(10) DEFAULT NULL,
  `school_year_id` int(10) DEFAULT NULL,
  `purpose` varchar(250) NOT NULL,
  `copies` int(10) NOT NULL,
  `image_file_path` varchar(250) DEFAULT NULL,
  `status` enum('processing','pending','rejected','released','ready for release','verified') NOT NULL DEFAULT 'pending',
  `requested_at` date NOT NULL,
  `processed_by` int(10) DEFAULT NULL,
  `processed_at` date DEFAULT NULL,
  `released_by` int(10) DEFAULT NULL,
  `released_at` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rgr_enrollments`
--

CREATE TABLE `rgr_enrollments` (
  `id` int(10) NOT NULL,
  `student_id` int(10) NOT NULL,
  `semester_id` int(10) NOT NULL,
  `year_level` int(10) NOT NULL,
  `strand_id` int(10) DEFAULT NULL,
  `course_id` int(10) DEFAULT NULL,
  `status` varchar(250) NOT NULL,
  `is_locked` int(10) NOT NULL,
  `created_at` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rgr_enrollment_subjects`
--

CREATE TABLE `rgr_enrollment_subjects` (
  `id` int(10) NOT NULL,
  `enrollment_id` int(10) NOT NULL,
  `subject_id` int(10) NOT NULL,
  `teacher_id` int(10) NOT NULL,
  `schedule` varchar(250) NOT NULL,
  `room` varchar(250) NOT NULL,
  `final_grade` int(10) NOT NULL,
  `created_at` date NOT NULL DEFAULT current_timestamp(),
  `updated_at` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rgr_events`
--

CREATE TABLE `rgr_events` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `start` datetime NOT NULL,
  `end` datetime DEFAULT NULL,
  `all_day` tinyint(1) NOT NULL DEFAULT 0,
  `background_color` varchar(255) DEFAULT NULL,
  `border_color` varchar(255) DEFAULT NULL,
  `text_color` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rgr_events`
--

INSERT INTO `rgr_events` (`id`, `title`, `description`, `start`, `end`, `all_day`, `background_color`, `border_color`, `text_color`, `created_at`, `updated_at`) VALUES
(96, 'Warning', 'test', '2026-02-17 00:00:00', '2026-02-19 00:00:00', 0, '#ffc107', NULL, NULL, '2026-02-09 17:45:26', '2026-02-09 17:45:26'),
(100, 'test', 'test', '2026-02-10 00:00:00', '2026-02-12 00:00:00', 0, '#dc3545', NULL, NULL, '2026-02-17 03:48:37', '2026-02-17 03:48:37'),
(101, 'try', 'try', '2026-02-19 00:00:00', '2026-02-22 00:00:00', 0, '#0d6efd', NULL, NULL, '2026-02-17 03:49:12', '2026-02-17 03:49:12'),
(102, 'cache', 'cache', '2026-02-20 00:00:00', '2026-02-22 00:00:00', 0, '#198754', NULL, NULL, '2026-02-17 03:49:30', '2026-02-17 03:49:30');

-- --------------------------------------------------------

--
-- Table structure for table `rgr_notifications`
--

CREATE TABLE `rgr_notifications` (
  `id` int(10) NOT NULL,
  `user_id` int(10) DEFAULT NULL,
  `student_id` int(10) DEFAULT NULL,
  `type` varchar(50) NOT NULL,
  `reference_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rgr_rooms`
--

CREATE TABLE `rgr_rooms` (
  `id` int(10) NOT NULL,
  `name` varchar(250) NOT NULL,
  `building` varchar(250) DEFAULT NULL,
  `capacity` int(10) DEFAULT NULL,
  `type` varchar(250) NOT NULL DEFAULT 'lecture'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rgr_school_years`
--

CREATE TABLE `rgr_school_years` (
  `id` int(10) NOT NULL,
  `name` varchar(250) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rgr_school_years`
--

INSERT INTO `rgr_school_years` (`id`, `name`, `is_active`, `created_at`, `updated_at`) VALUES
(28, '2025-2026', 1, '2026-03-05 06:38:03', '2026-03-18 05:16:58'),
(29, '2024-2025', 0, '2026-03-06 03:38:01', '2026-03-18 05:16:58');

-- --------------------------------------------------------

--
-- Table structure for table `rgr_semesters`
--

CREATE TABLE `rgr_semesters` (
  `id` int(10) NOT NULL,
  `name` varchar(250) NOT NULL,
  `school_year_id` int(10) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rgr_semesters`
--

INSERT INTO `rgr_semesters` (`id`, `name`, `school_year_id`, `is_active`, `created_at`, `updated_at`) VALUES
(9, '2nd Semester', 28, 1, '2026-03-05 06:38:24', '2026-03-05 06:47:01'),
(11, '2nd Semester', 29, 0, '2026-03-06 18:07:39', '2026-03-06 18:07:39');

-- --------------------------------------------------------

--
-- Table structure for table `rgr_strands`
--

CREATE TABLE `rgr_strands` (
  `id` int(10) NOT NULL,
  `name` varchar(250) NOT NULL,
  `code` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rgr_strands`
--

INSERT INTO `rgr_strands` (`id`, `name`, `code`) VALUES
(2, 'Humanities and Social Sciences', 'HUMSS');

-- --------------------------------------------------------

--
-- Table structure for table `rgr_students`
--

CREATE TABLE `rgr_students` (
  `student_number` int(250) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) NOT NULL,
  `gender` enum('male','female') DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `course` varchar(255) DEFAULT NULL,
  `year_level` year(4) DEFAULT NULL,
  `section` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `academic_status` enum('active','inactive','graduated') NOT NULL DEFAULT 'active',
  `graduated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rgr_students`
--

INSERT INTO `rgr_students` (`student_number`, `first_name`, `middle_name`, `last_name`, `gender`, `birth_date`, `course`, `year_level`, `section`, `email`, `phone`, `address`, `academic_status`, `graduated_at`, `created_at`, `updated_at`) VALUES
(0, 'Edison', NULL, 'Verceles', 'male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active', NULL, NULL, NULL),
(51, 'Edison', NULL, 'Verceles', 'male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active', NULL, NULL, NULL),
(86, 'Edison', NULL, 'Verceles', 'male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active', NULL, NULL, NULL),
(123, 'Edison', NULL, 'Verceles', 'male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active', NULL, NULL, NULL),
(508, 'Bertram', 'Zemlak', 'Stehr', 'male', NULL, 'BSCrim', '2004', NULL, NULL, NULL, NULL, 'graduated', '2025-12-31 11:14:45', '2025-09-27 19:30:41', '2026-02-01 23:41:03'),
(529, 'Zackary', 'Koelpin', 'Parisian', 'male', NULL, 'BSCrim', '2004', NULL, NULL, NULL, NULL, 'inactive', NULL, '2023-07-04 04:54:14', '2026-02-02 00:07:43'),
(555, 'Emilia', 'Bailey', 'Lakin', 'female', NULL, 'BSTM', '2004', NULL, NULL, NULL, NULL, 'graduated', '2024-08-09 17:20:01', '2023-06-13 00:24:55', '2026-02-01 23:41:03'),
(625, 'Maximilian', 'Rosenbaum', 'Fritsch', 'male', NULL, 'BSAIS', '2004', NULL, NULL, NULL, NULL, 'graduated', '2023-03-07 03:37:06', '2022-10-11 06:44:46', '2026-02-02 01:10:42'),
(684, 'Cole', 'Trantow', 'Heller', 'male', NULL, 'BSTM', '2001', NULL, NULL, NULL, NULL, 'active', NULL, '2022-09-26 10:29:38', '2026-02-02 00:07:43'),
(685, 'Danielle', 'Nitzsche', 'Sauer', 'male', NULL, 'BSIS', '2004', NULL, NULL, NULL, NULL, 'graduated', '2025-10-18 06:33:39', '2025-07-30 08:24:39', '2026-02-02 00:07:43'),
(769, 'Dallin', 'Spencer', 'Heidenreich', 'female', NULL, 'BSAIS', '2004', NULL, NULL, NULL, NULL, 'graduated', '2024-10-29 21:26:46', '2024-06-19 02:34:15', '2026-02-02 01:10:42'),
(807, 'Archibald', 'Leuschke', 'Crona', 'female', NULL, 'BSAIS', '2002', NULL, NULL, NULL, NULL, 'active', NULL, '2023-05-11 17:07:09', '2026-02-02 00:07:43'),
(846, 'Morris', 'Stamm', 'McKenzie', 'female', NULL, 'BSIS', '2003', NULL, NULL, NULL, NULL, 'inactive', NULL, '2023-08-03 21:59:19', '2026-02-01 23:41:03'),
(881, 'Lauren', 'Dooley', 'Beahan', 'female', NULL, 'BSAIS', '2004', NULL, NULL, NULL, NULL, 'active', NULL, '2023-08-22 22:35:39', '2026-01-31 04:48:20'),
(885, 'Kamren', 'Oberbrunner', 'Langworth', 'male', NULL, 'BSTM', '2001', NULL, NULL, NULL, NULL, 'inactive', NULL, '2024-11-21 15:21:09', '2026-02-02 00:07:43'),
(1070, 'Anna', 'Anderson', 'Kautzer', 'female', NULL, 'BSIS', '2004', NULL, NULL, NULL, NULL, 'inactive', NULL, '2024-09-10 21:45:47', '2026-02-01 23:41:03'),
(1087, 'Kailey', 'Herman', 'Terry', 'male', NULL, 'BSTM', '2003', NULL, NULL, NULL, NULL, 'inactive', NULL, '2023-07-11 18:32:24', '2026-02-02 00:07:43'),
(1195, 'Kameron', 'Veum', 'Runolfsson', 'female', NULL, 'BSIS', '2002', NULL, NULL, NULL, NULL, 'active', NULL, '2023-02-18 03:25:09', '2026-02-02 00:07:43'),
(1259, 'Jaylan', 'Green', 'Kuvalis', 'female', NULL, 'BSAIS', '2003', NULL, NULL, NULL, NULL, 'inactive', NULL, '2022-02-15 08:31:56', '2026-02-02 00:07:43'),
(1286, 'Adah', 'Breitenberg', 'Braun', 'female', NULL, 'BSTM', '2003', NULL, NULL, NULL, NULL, 'inactive', NULL, '2022-09-26 23:35:35', '2026-02-02 00:07:43'),
(1351, 'Caterina', 'Beer', 'Effertz', 'female', NULL, 'BSCrim', '2001', NULL, NULL, NULL, NULL, 'inactive', NULL, '2023-09-21 18:56:07', '2026-02-02 00:07:43'),
(1354, 'Cheyanne', 'Cormier', 'Mitchell', 'female', NULL, 'BSIS', '2002', NULL, NULL, NULL, NULL, 'active', NULL, '2022-03-12 10:00:39', '2026-02-01 23:41:03'),
(1435, 'Kian', 'Hauck', 'Ritchie', 'female', NULL, 'BSIS', '2002', NULL, NULL, NULL, NULL, 'active', NULL, '2025-08-29 18:17:41', '2026-02-01 23:41:03'),
(1487, 'Agustin', 'Russel', 'Huel', 'female', NULL, 'BSIS', '2003', NULL, NULL, NULL, NULL, 'active', NULL, '2023-08-30 03:57:44', '2026-02-01 23:41:03'),
(1711, 'Justine', 'Hackett', 'Rohan', 'male', NULL, 'BSCrim', '2004', NULL, NULL, NULL, NULL, 'inactive', NULL, '2025-12-18 02:32:19', '2026-01-31 04:48:20'),
(1720, 'Moriah', 'Wisozk', 'Daugherty', 'male', NULL, 'BSAIS', '2004', NULL, NULL, NULL, NULL, 'graduated', '2025-06-03 19:26:16', '2025-03-03 06:42:54', '2026-02-01 23:41:03'),
(1767, 'Kendall', 'Thiel', 'Wuckert', 'male', NULL, 'BSTM', '2004', NULL, NULL, NULL, NULL, 'active', NULL, '2025-01-15 11:20:01', '2026-02-02 00:07:43'),
(1923, 'Kirstin', 'D\'Amore', 'Carter', 'female', NULL, 'BSAIS', '2004', NULL, NULL, NULL, NULL, 'graduated', '2025-11-03 05:52:48', '2023-07-24 14:47:02', '2026-02-01 23:41:03'),
(2073, 'Bernie', 'Schroeder', 'Steuber', 'female', NULL, 'BSIS', '2004', NULL, NULL, NULL, NULL, 'inactive', NULL, '2022-05-20 10:45:52', '2026-02-02 00:07:43'),
(2091, 'Jana', 'Emard', 'Bosco', 'male', NULL, 'BSCrim', '2002', NULL, NULL, NULL, NULL, 'inactive', NULL, '2023-02-23 14:40:43', '2026-02-02 01:10:42'),
(2093, 'Erick', 'West', 'Cremin', 'female', NULL, 'BSTM', '2004', NULL, NULL, NULL, NULL, 'active', NULL, '2025-01-24 12:26:10', '2026-01-31 04:48:20'),
(2095, 'Alysa', 'Friesen', 'Langworth', 'male', NULL, 'BSCrim', '2001', NULL, NULL, NULL, NULL, 'active', NULL, '2023-06-19 11:03:11', '2026-02-02 00:07:43'),
(2130, 'Marguerite', 'Wiegand', 'Swaniawski', 'female', NULL, 'BSAIS', '2001', NULL, NULL, NULL, NULL, 'inactive', NULL, '2023-07-12 01:04:59', '2026-02-02 00:07:43'),
(2183, 'Osvaldo', 'Effertz', 'Blick', 'male', NULL, 'BSCrim', '2004', NULL, NULL, NULL, NULL, 'graduated', '2025-04-09 22:06:37', '2025-01-30 18:38:31', '2026-01-31 04:48:20'),
(2191, 'Erwin', 'Zieme', 'Hessel', 'female', NULL, 'BSAIS', '2004', NULL, NULL, NULL, NULL, 'graduated', '2023-02-27 20:05:05', '2022-08-23 20:22:42', '2026-01-31 04:48:20'),
(2366, 'Dafdasf', NULL, 'Testing', 'male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active', NULL, NULL, NULL),
(2398, 'Ryann', 'Waters', 'Rogahn', 'female', NULL, 'BSIS', '2002', NULL, NULL, NULL, NULL, 'inactive', NULL, '2023-05-13 07:01:21', '2026-02-02 00:07:43'),
(2452, 'Harold', 'Anderson', 'Marvin', 'male', NULL, 'BSTM', '2001', NULL, NULL, NULL, NULL, 'active', NULL, '2023-01-17 12:30:33', '2026-02-01 23:41:03'),
(2459, 'Natalie', 'Streich', 'Casper', 'male', NULL, 'BSTM', '2003', NULL, NULL, NULL, NULL, 'inactive', NULL, '2022-08-29 21:48:45', '2026-02-02 01:10:42'),
(2460, 'Gage', 'Mosciski', 'Herzog', 'female', NULL, 'BSAIS', '2004', NULL, NULL, NULL, NULL, 'graduated', '2026-01-18 10:30:57', '2025-09-11 00:34:09', '2026-02-02 00:07:43'),
(2552, 'Reinhold', 'Schneider', 'Cole', 'female', NULL, 'BSTM', '2002', NULL, NULL, NULL, NULL, 'inactive', NULL, '2022-10-13 08:49:35', '2026-02-01 23:41:03'),
(2617, 'Clemens', 'Kautzer', 'Weber', 'male', NULL, 'BSIS', '2003', NULL, NULL, NULL, NULL, 'active', NULL, '2022-10-24 13:29:09', '2026-01-31 04:48:20'),
(2743, 'Lexi', 'Kris', 'Stracke', 'female', NULL, 'BSTM', '2004', NULL, NULL, NULL, NULL, 'active', NULL, '2022-12-26 17:49:31', '2026-02-01 23:41:03'),
(2798, 'America', 'Brown', 'Wilderman', 'male', NULL, 'BSAIS', '2004', NULL, NULL, NULL, NULL, 'graduated', '2024-08-06 02:59:52', '2022-02-16 04:58:00', '2026-02-02 00:07:43'),
(2980, 'Damon', 'Feest', 'Hermiston', 'female', NULL, 'BSTM', '2002', NULL, NULL, NULL, NULL, 'active', NULL, '2024-08-24 18:14:54', '2026-01-31 04:48:20'),
(3053, 'Jaylen', 'Bruen', 'Huel', 'female', NULL, 'BSIS', '2002', NULL, NULL, NULL, NULL, 'active', NULL, '2022-08-07 00:53:03', '2026-01-31 04:48:20'),
(3100, 'Ansley', 'Zboncak', 'Kris', 'male', NULL, 'BSIS', '2004', NULL, NULL, NULL, NULL, 'graduated', '2024-01-04 13:59:13', '2023-10-24 08:57:24', '2026-02-02 00:07:43'),
(3200, 'Maryjane', 'Jones', 'Stoltenberg', 'female', NULL, 'BSCrim', '2001', NULL, NULL, NULL, NULL, 'inactive', NULL, '2022-08-26 12:16:26', '2026-01-31 04:48:20'),
(3280, 'Demetrius', 'Crona', 'Torphy', 'male', NULL, 'BSAIS', '2004', NULL, NULL, NULL, NULL, 'graduated', '2025-03-06 09:40:07', '2025-03-01 21:27:02', '2026-02-01 23:41:03'),
(3287, 'Heather', 'Gibson', 'Marks', 'female', NULL, 'BSTM', '2003', NULL, NULL, NULL, NULL, 'active', NULL, '2023-08-11 16:14:01', '2026-02-02 00:07:43'),
(3309, 'Greyson', 'Gerhold', 'Lockman', 'female', NULL, 'BSAIS', '2004', NULL, NULL, NULL, NULL, 'inactive', NULL, '2024-11-03 05:34:44', '2026-01-31 04:48:20'),
(3456, 'Dafdasf', NULL, 'Testing', 'male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active', NULL, NULL, NULL),
(3458, 'Danielle', 'McLaughlin', 'Hickle', 'male', NULL, 'BSTM', '2004', NULL, NULL, NULL, NULL, 'active', NULL, '2023-07-29 13:02:23', '2026-01-31 04:48:20'),
(3473, 'Aida', 'Moore', 'McKenzie', 'female', NULL, 'BSTM', '2004', NULL, NULL, NULL, NULL, 'graduated', '2026-01-12 20:10:15', '2025-07-06 02:12:16', '2026-02-02 00:07:43'),
(3475, 'Crystel', 'Bruen', 'Doyle', 'male', NULL, 'BSTM', '2003', NULL, NULL, NULL, NULL, 'active', NULL, '2023-02-13 03:15:22', '2026-01-31 04:48:20'),
(3482, 'Akeem', 'McGlynn', 'Harvey', 'male', NULL, 'BSTM', '2004', NULL, NULL, NULL, NULL, 'inactive', NULL, '2022-04-14 03:53:28', '2026-02-02 00:07:43'),
(3510, 'Tessie', 'Halvorson', 'Altenwerth', 'female', NULL, 'BSTM', '2004', NULL, NULL, NULL, NULL, 'inactive', NULL, '2023-12-13 19:47:03', '2026-02-01 23:41:03'),
(3568, 'Micheal', 'Brakus', 'Emard', 'female', NULL, 'BSAIS', '2004', NULL, NULL, NULL, NULL, 'graduated', '2025-03-13 08:07:23', '2022-11-02 09:16:16', '2026-02-01 23:41:03'),
(3598, 'Kariane', 'Schuppe', 'Stracke', 'male', NULL, 'BSCrim', '2003', NULL, NULL, NULL, NULL, 'active', NULL, '2022-10-08 17:04:33', '2026-01-31 04:48:20'),
(3681, 'Casper', 'Klocko', 'Nitzsche', 'male', NULL, 'BSTM', '2002', NULL, NULL, NULL, NULL, 'inactive', NULL, '2023-07-07 14:08:02', '2026-02-02 00:07:43'),
(3683, 'Kayli', 'Wolf', 'Rath', 'male', NULL, 'BSTM', '2003', NULL, NULL, NULL, NULL, 'active', NULL, '2022-06-22 19:44:26', '2026-01-31 04:48:20'),
(3703, 'Juan', NULL, 'Dela Cruz', 'male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active', NULL, NULL, NULL),
(3749, 'Timmothy', 'Torphy', 'Ward', 'male', NULL, 'BSCrim', '2002', NULL, NULL, NULL, NULL, 'inactive', NULL, '2023-04-09 18:40:07', '2026-02-02 00:07:43'),
(3835, 'Arnulfo', 'Parisian', 'Balistreri', 'male', NULL, 'BSIS', '2003', NULL, NULL, NULL, NULL, 'inactive', NULL, '2025-07-02 05:54:00', '2026-01-31 04:48:20'),
(3941, 'Chris', 'Hessel', 'Ernser', 'male', NULL, 'BSAIS', '2004', NULL, NULL, NULL, NULL, 'graduated', '2024-08-03 18:02:53', '2022-12-28 17:25:41', '2026-02-01 23:41:03'),
(3999, 'Adrain', 'Legros', 'Franecki', 'male', NULL, 'BSTM', '2004', NULL, NULL, NULL, NULL, 'active', NULL, '2025-05-08 17:14:44', '2026-01-31 04:48:20'),
(4088, 'Jeanie', 'Daugherty', 'O\'Conner', 'male', NULL, 'BSIS', '2004', NULL, NULL, NULL, NULL, 'inactive', NULL, '2024-12-27 03:51:20', '2026-01-31 04:48:20'),
(4094, 'Shanna', 'Cole', 'Cruickshank', 'male', NULL, 'BSCrim', '2004', NULL, NULL, NULL, NULL, 'graduated', '2025-11-04 22:35:12', '2023-08-22 19:04:35', '2026-01-31 04:48:20'),
(4102, 'Cornell', 'Batz', 'Zboncak', 'female', NULL, 'BSCrim', '2002', NULL, NULL, NULL, NULL, 'inactive', NULL, '2025-11-27 05:00:45', '2026-02-01 23:41:03'),
(4159, 'Adalberto', 'O\'Hara', 'Hills', 'male', NULL, 'BSIS', '2004', NULL, NULL, NULL, NULL, 'graduated', '2024-01-07 09:47:49', '2023-07-27 00:30:34', '2026-01-31 04:48:20'),
(4227, 'Leatha', 'Morissette', 'Bins', 'male', NULL, 'BSIS', '2004', NULL, NULL, NULL, NULL, 'graduated', '2026-01-31 09:14:35', '2025-12-13 03:48:30', '2026-02-02 00:07:43'),
(4269, 'Wilfred', 'Cole', 'Towne', 'female', NULL, 'BSTM', '2004', NULL, NULL, NULL, NULL, 'active', NULL, '2025-11-27 11:53:29', '2026-01-31 04:48:20'),
(4295, 'Tito', 'Jerde', 'Sanford', 'male', NULL, 'BSAIS', '2002', NULL, NULL, NULL, NULL, 'active', NULL, '2024-10-18 19:27:27', '2026-01-31 04:48:20'),
(4298, 'Andreanne', 'Altenwerth', 'Price', 'male', NULL, 'BSAIS', '2004', NULL, NULL, NULL, NULL, 'graduated', '2025-11-15 10:45:38', '2024-11-30 02:23:39', '2026-02-01 23:41:03'),
(4383, 'Jessica', 'Hauck', 'Prohaska', 'female', NULL, 'BSIS', '2002', NULL, NULL, NULL, NULL, 'active', NULL, '2023-11-01 17:40:14', '2026-01-31 04:48:20'),
(4385, 'Darby', 'Johnson', 'Mueller', 'female', NULL, 'BSTM', '2003', NULL, NULL, NULL, NULL, 'active', NULL, '2024-06-12 22:29:13', '2026-02-02 00:07:43'),
(4412, 'Orin', 'Lehner', 'Beatty', 'male', NULL, 'BSTM', '2002', NULL, NULL, NULL, NULL, 'inactive', NULL, '2024-05-28 23:11:55', '2026-02-02 00:07:43'),
(4486, 'Noble', 'Stark', 'Shanahan', 'female', NULL, 'BSAIS', '2001', NULL, NULL, NULL, NULL, 'inactive', NULL, '2024-03-29 13:03:58', '2026-02-02 00:07:43'),
(4526, 'Aiyana', 'Doyle', 'Nolan', 'female', NULL, 'BSIS', '2003', NULL, NULL, NULL, NULL, 'active', NULL, '2022-11-24 12:06:34', '2026-02-01 23:41:03'),
(4602, 'Karlie', 'Torp', 'Deckow', 'female', NULL, 'BSAIS', '2004', NULL, NULL, NULL, NULL, 'graduated', '2025-06-23 10:11:17', '2024-11-05 00:25:31', '2026-02-02 00:07:43'),
(4648, 'General', 'Erdman', 'Funk', 'male', NULL, 'BSTM', '2004', NULL, NULL, NULL, NULL, 'graduated', '2024-06-25 04:52:49', '2023-10-15 04:48:33', '2026-02-02 00:07:43'),
(4668, 'Theron', 'Durgan', 'Kunze', 'female', NULL, 'BSTM', '2001', NULL, NULL, NULL, NULL, 'active', NULL, '2024-09-04 20:11:35', '2026-02-01 23:41:03'),
(4792, 'Dangelo', 'Prohaska', 'Balistreri', 'male', NULL, 'BSTM', '2002', NULL, NULL, NULL, NULL, 'active', NULL, '2024-03-23 05:49:28', '2026-02-02 01:10:42'),
(4799, 'Hadley', 'Herman', 'Emard', 'female', NULL, 'BSCrim', '2002', NULL, NULL, NULL, NULL, 'active', NULL, '2024-03-13 15:45:54', '2026-02-02 00:07:43'),
(4811, 'Hermina', 'Cartwright', 'Kshlerin', 'male', NULL, 'BSCrim', '2004', NULL, NULL, NULL, NULL, 'graduated', '2026-01-16 17:44:35', '2022-08-18 21:54:34', '2026-02-02 00:07:43'),
(4866, 'Bart', 'Little', 'Hessel', 'male', NULL, 'BSCrim', '2003', NULL, NULL, NULL, NULL, 'inactive', NULL, '2025-11-25 19:56:25', '2026-02-01 23:41:03'),
(4950, 'Keenan', 'Douglas', 'Wilderman', 'male', NULL, 'BSTM', '2004', NULL, NULL, NULL, NULL, 'graduated', '2025-08-31 12:44:39', '2023-06-24 17:50:53', '2026-02-01 23:41:03'),
(4952, 'Pamela', 'Ortiz', 'Predovic', 'female', NULL, 'BSCrim', '2004', NULL, NULL, NULL, NULL, 'active', NULL, '2022-03-16 05:38:24', '2026-02-02 00:07:43'),
(5102, 'Wallace', 'Gerhold', 'Senger', 'female', NULL, 'BSAIS', '2002', NULL, NULL, NULL, NULL, 'active', NULL, '2023-01-02 20:20:44', '2026-02-01 23:41:03'),
(5125, 'Herman', 'Batz', 'Russel', 'female', NULL, 'BSIS', '2003', NULL, NULL, NULL, NULL, 'inactive', NULL, '2025-04-25 18:08:26', '2026-02-02 00:07:43'),
(5204, 'Malvina', 'Stanton', 'Rodriguez', 'female', NULL, 'BSIS', '2004', NULL, NULL, NULL, NULL, 'graduated', '2024-03-19 06:45:53', '2024-03-13 08:02:56', '2026-01-31 04:48:20'),
(5228, 'Kaelyn', 'Maggio', 'Murazik', 'male', NULL, 'BSIS', '2004', NULL, NULL, NULL, NULL, 'graduated', '2025-09-17 17:45:57', '2025-06-18 09:53:37', '2026-02-02 00:07:43'),
(5274, 'Elyasen', NULL, 'Yazier', 'male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active', NULL, NULL, NULL),
(5301, 'Brent', 'Schmitt', 'Pouros', 'male', NULL, 'BSTM', '2004', NULL, NULL, NULL, NULL, 'graduated', '2025-02-09 15:32:03', '2024-10-20 18:45:43', '2026-02-01 23:41:03'),
(5338, 'Tyra', 'Mertz', 'Muller', 'female', NULL, 'BSIS', '2003', NULL, NULL, NULL, NULL, 'inactive', NULL, '2025-03-28 05:17:38', '2026-02-02 00:07:43'),
(5339, 'Edythe', 'Altenwerth', 'Robel', 'male', NULL, 'BSTM', '2004', NULL, NULL, NULL, NULL, 'graduated', '2023-12-21 23:46:30', '2022-12-08 08:29:09', '2026-01-31 04:48:20'),
(5341, 'Susie', 'Rodriguez', 'Friesen', 'female', NULL, 'BSAIS', '2004', NULL, NULL, NULL, NULL, 'graduated', '2025-08-19 12:52:56', '2023-12-03 23:04:24', '2026-01-31 04:48:20'),
(5363, 'Uriah', 'Fisher', 'Beier', 'female', NULL, 'BSIS', '2002', NULL, NULL, NULL, NULL, 'inactive', NULL, '2024-04-25 23:22:31', '2026-01-31 04:48:20'),
(5437, 'Eliza', 'Fisher', 'Wisoky', 'male', NULL, 'BSTM', '2004', NULL, NULL, NULL, NULL, 'active', NULL, '2023-09-22 14:38:08', '2026-02-02 01:10:42'),
(5455, 'Bridie', 'Heidenreich', 'Huel', 'male', NULL, 'BSCrim', '2004', NULL, NULL, NULL, NULL, 'graduated', '2025-03-02 09:24:04', '2024-09-09 18:33:23', '2026-02-01 23:41:03'),
(5606, 'Erling', 'Ortiz', 'Flatley', 'female', NULL, 'BSTM', '2004', NULL, NULL, NULL, NULL, 'graduated', '2026-01-27 21:07:04', '2022-06-21 23:40:43', '2026-02-01 23:41:03'),
(5651, 'Tommie', 'Kohler', 'Mann', 'female', NULL, 'BSAIS', '2002', NULL, NULL, NULL, NULL, 'active', NULL, '2024-03-25 14:07:39', '2026-01-31 04:48:20'),
(5683, 'Cecil', 'Kris', 'Davis', 'female', NULL, 'BSIS', '2004', NULL, NULL, NULL, NULL, 'active', NULL, '2023-10-11 19:06:58', '2026-02-02 00:07:43'),
(5770, 'Elta', 'Rolfson', 'Turcotte', 'female', NULL, 'BSIS', '2004', NULL, NULL, NULL, NULL, 'graduated', '2025-12-10 03:09:14', '2025-11-02 01:03:37', '2026-02-02 00:07:43'),
(5772, 'Jude', 'Zieme', 'Leannon', 'female', NULL, 'BSIS', '2004', NULL, NULL, NULL, NULL, 'graduated', '2026-01-05 02:09:18', '2025-10-03 09:50:21', '2026-01-31 04:48:20'),
(5784, 'Rod', 'Gislason', 'Kautzer', 'female', NULL, 'BSAIS', '2004', NULL, NULL, NULL, NULL, 'graduated', '2026-01-15 14:51:04', '2022-05-06 16:13:07', '2026-02-01 23:41:03'),
(5800, 'Devin', 'Emmerich', 'Mohr', 'female', NULL, 'BSIS', '2004', NULL, NULL, NULL, NULL, 'active', NULL, '2025-07-27 10:15:35', '2026-02-01 23:41:03'),
(5816, 'Branson', 'Jenkins', 'Bergstrom', 'male', NULL, 'BSAIS', '2004', NULL, NULL, NULL, NULL, 'inactive', NULL, '2023-08-28 17:14:12', '2026-02-02 00:07:43'),
(5819, 'Lane', 'Grant', 'Stamm', 'female', NULL, 'BSTM', '2001', NULL, NULL, NULL, NULL, 'active', NULL, '2023-02-28 17:54:28', '2026-02-01 23:41:03'),
(5860, 'Madelyn', 'Roob', 'Kiehn', 'female', NULL, 'BSCrim', '2002', NULL, NULL, NULL, NULL, 'active', NULL, '2025-09-16 15:01:47', '2026-02-02 00:07:43'),
(5865, 'Rae', 'Bergstrom', 'Zemlak', 'male', NULL, 'BSAIS', '2004', NULL, NULL, NULL, NULL, 'graduated', '2024-10-03 09:14:58', '2023-04-24 11:24:30', '2026-01-31 04:48:20'),
(5935, 'Earline', 'Paucek', 'Veum', 'female', NULL, 'BSAIS', '2004', NULL, NULL, NULL, NULL, 'graduated', '2025-06-25 12:36:03', '2025-05-21 09:31:01', '2026-02-01 23:41:03'),
(6062, 'Kyleigh', 'Hegmann', 'Harber', 'male', NULL, 'BSAIS', '2004', NULL, NULL, NULL, NULL, 'graduated', '2026-01-08 20:09:38', '2024-10-10 08:42:40', '2026-01-31 04:48:20'),
(6125, 'Golda', 'Carroll', 'Wehner', 'male', NULL, 'BSIS', '2003', NULL, NULL, NULL, NULL, 'inactive', NULL, '2023-09-14 01:03:53', '2026-02-01 23:41:03'),
(6233, 'Columbus', 'Emmerich', 'Hill', 'male', NULL, 'BSCrim', '2004', NULL, NULL, NULL, NULL, 'graduated', '2025-02-12 13:26:28', '2023-10-02 08:51:20', '2026-02-01 23:41:03'),
(6241, 'Yvonne', 'Hoeger', 'Ankunding', 'female', NULL, 'BSAIS', '2002', NULL, NULL, NULL, NULL, 'active', NULL, '2024-04-01 06:46:53', '2026-02-02 00:07:43'),
(6252, 'Sherman', 'Littel', 'Bergstrom', 'male', NULL, 'BSAIS', '2004', NULL, NULL, NULL, NULL, 'graduated', '2024-01-15 00:06:19', '2023-12-23 07:24:25', '2026-02-01 23:41:03'),
(6323, 'Naomie', 'Roob', 'O\'Keefe', 'male', NULL, 'BSIS', '2002', NULL, NULL, NULL, NULL, 'inactive', NULL, '2022-02-10 03:24:37', '2026-02-01 23:41:03'),
(6399, 'Caterina', 'Kulas', 'Roberts', 'female', NULL, 'BSTM', '2004', NULL, NULL, NULL, NULL, 'active', NULL, '2025-10-14 00:45:12', '2026-02-02 00:07:43'),
(6411, 'Spencer', 'Vandervort', 'Sporer', 'female', NULL, 'BSCrim', '2003', NULL, NULL, NULL, NULL, 'active', NULL, '2022-10-24 23:37:41', '2026-02-01 23:41:03'),
(6413, 'Greta', 'Lemke', 'Reynolds', 'male', NULL, 'BSCrim', '2002', NULL, NULL, NULL, NULL, 'inactive', NULL, '2023-08-01 09:42:25', '2026-02-01 23:41:03'),
(6547, 'Jon', 'Welch', 'Tremblay', 'female', NULL, 'BSCrim', '2004', NULL, NULL, NULL, NULL, 'graduated', '2025-10-25 17:35:17', '2022-11-23 15:53:42', '2026-01-31 04:48:20'),
(6550, 'Constantin', 'Fritsch', 'Hickle', 'male', NULL, 'BSIS', '2004', NULL, NULL, NULL, NULL, 'active', NULL, '2023-12-15 18:12:23', '2026-02-01 23:41:03'),
(6659, 'Justice', 'Swaniawski', 'Rowe', 'male', NULL, 'BSIS', '2001', NULL, NULL, NULL, NULL, 'inactive', NULL, '2024-10-14 10:32:28', '2026-02-02 00:07:43'),
(6699, 'Fernando', 'Torphy', 'Labadie', 'male', NULL, 'BSTM', '2004', NULL, NULL, NULL, NULL, 'graduated', '2025-04-30 00:28:24', '2023-12-11 16:24:45', '2026-01-31 04:48:20'),
(7026, 'Madyson', 'Lang', 'Dietrich', 'female', NULL, 'BSTM', '2003', NULL, NULL, NULL, NULL, 'inactive', NULL, '2025-02-14 17:18:05', '2026-01-31 04:48:20'),
(7063, 'Brooke', 'Weissnat', 'Runte', 'female', NULL, 'BSCrim', '2004', NULL, NULL, NULL, NULL, 'graduated', '2023-05-25 16:47:52', '2023-02-25 17:07:45', '2026-02-02 00:07:43'),
(7121, 'Leonor', 'Metz', 'Johnston', 'female', NULL, 'BSAIS', '2001', NULL, NULL, NULL, NULL, 'inactive', NULL, '2023-06-21 16:29:15', '2026-01-31 04:48:20'),
(7125, 'Assunta', 'Feest', 'Kerluke', 'female', NULL, 'BSCrim', '2004', NULL, NULL, NULL, NULL, 'inactive', NULL, '2022-08-14 09:39:30', '2026-01-31 04:48:20'),
(7308, 'Jacinthe', 'Doyle', 'Predovic', 'male', NULL, 'BSIS', '2004', NULL, NULL, NULL, NULL, 'graduated', '2023-12-27 19:36:24', '2023-10-23 10:11:14', '2026-01-31 04:48:20'),
(7437, 'Adela', 'Kemmer', 'Blick', 'female', NULL, 'BSAIS', '2002', NULL, NULL, NULL, NULL, 'active', NULL, '2025-02-18 15:25:05', '2026-02-01 23:41:03'),
(7563, 'Kira', 'Johnston', 'Terry', 'female', NULL, 'BSCrim', '2004', NULL, NULL, NULL, NULL, 'graduated', '2025-09-11 07:31:00', '2024-06-08 12:25:45', '2026-02-02 01:10:42'),
(7589, 'Timothy', 'Gleichner', 'Torphy', 'male', NULL, 'BSCrim', '2004', NULL, NULL, NULL, NULL, 'inactive', NULL, '2023-10-31 22:56:09', '2026-01-31 04:48:20'),
(7651, 'Angelita', 'Cartwright', 'Zieme', 'female', NULL, 'BSIS', '2001', NULL, NULL, NULL, NULL, 'active', NULL, '2022-08-27 21:30:14', '2026-02-02 00:07:43'),
(7863, 'Adela', 'Mraz', 'Trantow', 'male', NULL, 'BSTM', '2002', NULL, NULL, NULL, NULL, 'inactive', NULL, '2022-06-01 16:25:43', '2026-02-01 23:41:03'),
(7917, 'Corine', 'Lind', 'Johnson', 'female', NULL, 'BSAIS', '2003', NULL, NULL, NULL, NULL, 'inactive', NULL, '2024-07-14 20:52:24', '2026-02-01 23:41:03'),
(8007, 'Mac', 'Torphy', 'Crooks', 'female', NULL, 'BSIS', '2001', NULL, NULL, NULL, NULL, 'inactive', NULL, '2023-08-27 04:51:10', '2026-01-31 04:48:20'),
(8043, 'Elyasen', NULL, 'Yazier', 'male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active', NULL, NULL, NULL),
(8145, 'Derick', 'Hamill', 'Erdman', 'female', NULL, 'BSIS', '2002', NULL, NULL, NULL, NULL, 'active', NULL, '2023-07-15 12:00:52', '2026-02-02 00:07:43'),
(8237, 'Damian', 'Price', 'Dickens', 'male', NULL, 'BSIS', '2003', NULL, NULL, NULL, NULL, 'inactive', NULL, '2023-08-25 20:25:26', '2026-02-02 00:07:43'),
(8389, 'Milan', 'Hartmann', 'O\'Hara', 'female', NULL, 'BSIS', '2003', NULL, NULL, NULL, NULL, 'inactive', NULL, '2022-05-18 07:01:57', '2026-01-31 04:48:20'),
(8436, 'Travis', 'Reinger', 'Waelchi', 'female', NULL, 'BSIS', '2004', NULL, NULL, NULL, NULL, 'graduated', '2025-03-19 16:18:18', '2024-09-15 16:56:01', '2026-02-02 00:07:43'),
(8521, 'Cooper', 'Beer', 'Olson', 'male', NULL, 'BSIS', '2004', NULL, NULL, NULL, NULL, 'graduated', '2025-06-11 01:39:53', '2022-12-27 15:06:28', '2026-02-02 00:07:43'),
(8527, 'Judah', 'Dietrich', 'Murazik', 'female', NULL, 'BSIS', '2004', NULL, NULL, NULL, NULL, 'graduated', '2025-04-04 03:16:11', '2022-12-05 10:58:30', '2026-02-01 23:41:03'),
(8588, 'Tyra', 'Flatley', 'Connelly', 'male', NULL, 'BSCrim', '2004', NULL, NULL, NULL, NULL, 'graduated', '2024-02-28 20:03:42', '2022-09-01 03:45:50', '2026-02-02 01:10:42'),
(8719, 'Urban', 'Friesen', 'Dare', 'female', NULL, 'BSCrim', '2004', NULL, NULL, NULL, NULL, 'graduated', '2026-01-03 19:40:49', '2025-03-20 01:32:41', '2026-01-31 04:48:20'),
(8735, 'Chaz', 'Gleason', 'Gottlieb', 'female', NULL, 'BSAIS', '2004', NULL, NULL, NULL, NULL, 'active', NULL, '2025-05-08 19:53:01', '2026-01-31 04:48:20'),
(8781, 'Oran', 'Beahan', 'Wolf', 'male', NULL, 'BSCrim', '2004', NULL, NULL, NULL, NULL, 'graduated', '2023-12-12 05:36:05', '2023-09-20 08:31:10', '2026-02-02 00:07:43'),
(8800, 'Ismael', 'Thiel', 'Barrows', 'female', NULL, 'BSIS', '2004', NULL, NULL, NULL, NULL, 'graduated', '2024-12-20 22:27:00', '2024-06-26 01:55:42', '2026-02-01 23:41:03'),
(9004, 'Madelyn', 'Schmidt', 'McClure', 'male', NULL, 'BSTM', '2004', NULL, NULL, NULL, NULL, 'graduated', '2025-01-16 14:59:28', '2025-01-12 00:32:32', '2026-01-31 04:48:20'),
(9070, 'Vella', 'Keeling', 'Windler', 'female', NULL, 'BSTM', '2002', NULL, NULL, NULL, NULL, 'active', NULL, '2024-07-07 13:19:52', '2026-02-02 01:10:42'),
(9119, 'Fannie', 'Upton', 'Bradtke', 'male', NULL, 'BSCrim', '2001', NULL, NULL, NULL, NULL, 'inactive', NULL, '2024-11-05 01:48:29', '2026-02-01 23:41:03'),
(9128, 'Clint', 'Strosin', 'O\'Connell', 'female', NULL, 'BSTM', '2002', NULL, NULL, NULL, NULL, 'active', NULL, '2025-08-20 07:41:43', '2026-01-31 04:48:20'),
(9212, 'Kirstin', 'Huels', 'Schneider', 'male', NULL, 'BSTM', '2004', NULL, NULL, NULL, NULL, 'graduated', '2025-04-13 14:05:41', '2023-12-02 06:03:37', '2026-02-02 01:10:42'),
(9267, 'Golda', 'Glover', 'Monahan', 'male', NULL, 'BSCrim', '2003', NULL, NULL, NULL, NULL, 'active', NULL, '2023-08-15 23:42:47', '2026-02-01 23:41:03'),
(9281, 'Serena', 'Rutherford', 'Berge', 'female', NULL, 'BSCrim', '2004', NULL, NULL, NULL, NULL, 'inactive', NULL, '2022-11-22 22:15:33', '2026-02-01 23:41:03'),
(9303, 'Queen', 'Blanda', 'Veum', 'female', NULL, 'BSTM', '2004', NULL, NULL, NULL, NULL, 'graduated', '2023-05-26 07:42:45', '2023-02-13 09:58:45', '2026-02-02 00:07:43'),
(9325, 'Meghan', 'Bernhard', 'Bergstrom', 'female', NULL, 'BSAIS', '2001', NULL, NULL, NULL, NULL, 'active', NULL, '2024-02-07 05:26:48', '2026-02-01 23:41:03'),
(9519, 'Viola', 'Bergnaum', 'Heaney', 'male', NULL, 'BSAIS', '2001', NULL, NULL, NULL, NULL, 'inactive', NULL, '2025-08-17 02:43:26', '2026-02-01 23:41:03'),
(9551, 'Clemmie', 'Volkman', 'O\'Reilly', 'male', NULL, 'BSTM', '2001', NULL, NULL, NULL, NULL, 'inactive', NULL, '2023-11-11 04:18:30', '2026-01-31 04:48:20'),
(9620, 'Gaston', 'Thompson', 'Will', 'male', NULL, 'BSTM', '2001', NULL, NULL, NULL, NULL, 'active', NULL, '2025-10-11 05:19:16', '2026-01-31 04:48:20'),
(9642, 'Frieda', 'Stehr', 'Ryan', 'male', NULL, 'BSCrim', '2004', NULL, NULL, NULL, NULL, 'graduated', '2025-08-13 10:05:49', '2025-03-20 23:40:14', '2026-02-02 01:10:42'),
(9684, 'Hellen', 'Morar', 'Stiedemann', 'male', NULL, 'BSCrim', '2001', NULL, NULL, NULL, NULL, 'active', NULL, '2023-04-29 18:58:31', '2026-02-02 01:10:42'),
(9796, 'Victor', 'Romaguera', 'Christiansen', 'female', NULL, 'BSCrim', '2004', NULL, NULL, NULL, NULL, 'graduated', '2025-12-10 22:50:48', '2024-03-03 06:06:16', '2026-01-31 04:48:20'),
(9822, 'Ralph', 'Rosenbaum', 'O\'Kon', 'female', NULL, 'BSAIS', '2001', NULL, NULL, NULL, NULL, 'inactive', NULL, '2024-11-18 03:44:44', '2026-02-02 01:10:42'),
(9905, 'Keegan', 'Murazik', 'Hansen', 'male', NULL, 'BSIS', '2004', NULL, NULL, NULL, NULL, 'graduated', '2025-10-01 01:02:39', '2025-08-27 10:15:26', '2026-01-31 04:48:20'),
(9916, 'Francesca', 'Kunde', 'Witting', 'female', NULL, 'BSIS', '2004', NULL, NULL, NULL, NULL, 'graduated', '2024-06-27 02:02:48', '2022-10-05 02:44:54', '2026-02-02 01:10:42'),
(9931, 'Lempi', 'Rodriguez', 'Kassulke', 'male', NULL, 'BSAIS', '2004', NULL, NULL, NULL, NULL, 'graduated', '2025-10-31 23:31:23', '2025-06-30 22:45:11', '2026-02-01 23:41:03'),
(9986, 'Macey', 'Miller', 'O\'Reilly', 'female', NULL, 'BSAIS', '2004', NULL, NULL, NULL, NULL, 'graduated', '2025-09-09 12:55:40', '2023-01-23 12:32:00', '2026-02-01 23:41:03'),
(3321312, 'Elyasen', NULL, 'Yazier', 'male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `rgr_subjects`
--

CREATE TABLE `rgr_subjects` (
  `id` int(10) NOT NULL,
  `code` varchar(250) NOT NULL,
  `name` varchar(250) NOT NULL,
  `units` int(11) NOT NULL,
  `lecture_hours` int(10) NOT NULL,
  `lab_hours` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rgr_subjects`
--

INSERT INTO `rgr_subjects` (`id`, `code`, `name`, `units`, `lecture_hours`, `lab_hours`) VALUES
(5, 'ENG1', 'English for Purposive Communication', 2, 3, 0),
(9, 'IS001', 'Introduction to Information System', 3, 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `rgr_teachers`
--

CREATE TABLE `rgr_teachers` (
  `id` int(10) NOT NULL,
  `employee_id` int(10) DEFAULT NULL,
  `first_name` varchar(250) NOT NULL,
  `last_name` varchar(250) NOT NULL,
  `email` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sd_announcements`
--

CREATE TABLE `sd_announcements` (
  `announcement_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `publish_date` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `image_file` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sd_announcements`
--

INSERT INTO `sd_announcements` (`announcement_id`, `title`, `message`, `publish_date`, `created_by`, `image_file`) VALUES
(42, NULL, NULL, '2026-03-18 00:00:00', 1001, 'ann_69ba346d16e7e6.24963525.jpg'),
(43, NULL, NULL, '2026-03-18 00:00:00', 1010, 'ann_69ba717c300612.73689549.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `sd_approvals`
--

CREATE TABLE `sd_approvals` (
  `approval_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `submitted_on` datetime DEFAULT NULL,
  `submit_by` bigint(100) DEFAULT NULL,
  `department` int(11) DEFAULT NULL,
  `approver_id` bigint(20) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `decision` enum('Approved','Rejected','Pending') DEFAULT 'Pending',
  `file_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sd_approvals`
--

INSERT INTO `sd_approvals` (`approval_id`, `title`, `description`, `remarks`, `submitted_on`, `submit_by`, `department`, `approver_id`, `approved_at`, `decision`, `file_path`) VALUES
(18, 'Sample 1', '', '', NULL, 1004, NULL, 1001, '2026-08-07 17:52:53', 'Approved', 'uploads/approvals/approval_69b780e1d46ea2.08407274.pdf'),
(19, 'Sample 2', '', '', NULL, 1001, NULL, 1001, '2026-03-16 13:33:02', 'Rejected', 'uploads/approvals/approval_69b781aaca59c6.10525276.pdf'),
(20, 'sdadsadsa', '', '', NULL, 1009, NULL, 1001, '2026-08-07 17:52:54', 'Approved', 'uploads/approvals/approval_69b78b4c86d0d3.16285597.pdf'),
(21, 'Sample 1', '', '', NULL, 1002, NULL, 1001, '2026-08-07 17:52:57', 'Approved', 'uploads/approvals/approval_69b79561be3351.95577412.pdf'),
(22, 'Sample 3', '', '', NULL, 1007, NULL, 1001, '2026-08-07 17:52:56', 'Approved', 'uploads/approvals/approval_69b797ddb48f46.81821739.pdf'),
(23, 'Sample 3', '', '', NULL, 1005, NULL, 1001, '2026-07-31 16:18:58', 'Rejected', 'uploads/approvals/approval_69b799b4110929.36952499.pdf'),
(24, 'dsdasdas', '', '', NULL, 1008, NULL, 1001, '2026-08-07 17:52:57', 'Approved', 'uploads/approvals/approval_69b79f56f2d104.85520681.pdf'),
(25, 'fsffsdfs', '', '', NULL, 1008, NULL, 1001, '2026-03-17 12:19:33', 'Approved', 'uploads/approvals/approval_69b8d63c297868.44131655.docx'),
(27, 'waddwadaw', 'bjdhkkdja', '', '2026-03-17 17:58:59', 1009, 8, 1001, '2026-08-07 17:52:53', 'Approved', NULL),
(28, 'test', 'test', '', '2026-03-17 18:04:40', NULL, 9, 1001, '2026-08-07 17:52:52', 'Approved', NULL),
(29, 'test', 'fjfj', '', NULL, 1003, NULL, 1001, '2026-08-07 17:52:59', 'Approved', 'uploads/approvals/approval_69b9527d22d69.jpg'),
(30, 'dfsdf', '', '', NULL, 1004, NULL, 1001, '2026-08-07 17:53:03', 'Approved', 'uploads/approvals/approval_69ba503686b249.21575866.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `sd_department`
--

CREATE TABLE `sd_department` (
  `department_id` int(100) NOT NULL,
  `department_name` varchar(100) DEFAULT NULL,
  `department_head` bigint(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sd_department`
--

INSERT INTO `sd_department` (`department_id`, `department_name`, `department_head`) VALUES
(1, 'Office of the Directress', NULL),
(2, 'Admissions and Enrollment', NULL),
(3, 'Registrar\'s Office', NULL),
(4, 'School Clinic', NULL),
(5, 'Library', NULL),
(6, 'Laboratory ', NULL),
(7, 'Operations Monitoring', NULL),
(8, 'Guidance and Counseling Office', 1007),
(9, 'Academic Affairs', 1009),
(12, 'Recruitment', NULL),
(14, 'Information System', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sd_issues`
--

CREATE TABLE `sd_issues` (
  `issue_id` bigint(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `department` int(100) NOT NULL,
  `submitted_by` bigint(20) NOT NULL,
  `submitted_on` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('open','resolved') NOT NULL DEFAULT 'open',
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `file_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sd_issues`
--

INSERT INTO `sd_issues` (`issue_id`, `title`, `description`, `department`, `submitted_by`, `submitted_on`, `status`, `updated_at`, `file_path`) VALUES
(20, 'Sample 1', '', 4, 1004, '2026-03-16 12:02:24', 'open', '2026-03-16 13:32:54', 'uploads/issues/issue_69b780d0199462.30837965.pdf'),
(21, 'Sample 2', '', 1, 1001, '2026-03-16 12:06:31', 'open', NULL, 'uploads/issues/issue_69b781c7618945.22013948.pdf'),
(22, 'sadasdasd', '', 9, 1009, '2026-03-16 12:47:02', 'open', NULL, 'uploads/issues/issue_69b78b460a5335.99879484.pdf'),
(23, 'Sample 2', '', 2, 1002, '2026-03-16 13:30:17', 'open', NULL, 'uploads/issues/issue_69b79569bcae82.17216626.pdf'),
(24, 'Sample 2', '', 8, 1007, '2026-03-16 13:40:37', 'open', NULL, 'uploads/issues/issue_69b797d52749f3.61164867.pdf'),
(25, 'Sample 2', '', 5, 1005, '2026-03-16 13:48:26', 'open', NULL, 'uploads/issues/issue_69b799aac91c50.12531845.pdf'),
(26, 'dasdasdasd', '', 7, 1008, '2026-03-16 14:12:46', 'open', NULL, 'uploads/issues/issue_69b79f5ee82e47.52956687.pdf'),
(27, 'fhgfh', '', 9, 1009, '2026-03-17 12:10:45', 'resolved', '2026-03-17 12:11:59', 'uploads/issues/issue_69b8d4457fcbe8.26237426.png'),
(28, 'Hello Mother Father', '', 8, 1007, '2026-03-17 12:12:17', 'open', NULL, 'uploads/issues/issue_69b8d4a15a3897.93728867.jpg'),
(29, 'fsdfdsdfs', '', 8, 1007, '2026-03-17 12:24:28', 'open', NULL, 'uploads/issues/issue_69b8d77c34f183.99565017.jpg'),
(30, 'gege', '', 8, 1007, '2026-03-17 12:37:13', 'open', NULL, 'uploads/issues/issue_69b8da794d3369.14019155.jpg'),
(31, 'afsafasdas', '', 4, 1004, '2026-03-17 16:56:33', 'resolved', '2026-03-17 16:56:53', 'uploads/issues/issue_69b917410e3682.59828306.png'),
(32, '564', '', 4, 1004, '2026-03-18 15:11:37', 'open', NULL, 'uploads/issues/issue_69ba5029242f61.53709495.pdf'),
(33, 'Sample Issue', '', 9, 1009, '2026-03-18 17:45:25', 'open', NULL, 'uploads/issues/issue_69ba7435ee6d14.80341184.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `sd_position`
--

CREATE TABLE `sd_position` (
  `position_id` int(10) NOT NULL,
  `position_name` varchar(100) DEFAULT NULL,
  `department` int(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sd_position`
--

INSERT INTO `sd_position` (`position_id`, `position_name`, `department`) VALUES
(1, 'School Directress', 1),
(2, 'Executive Assistant ', 1),
(3, 'Administrative Officer', 1),
(4, 'Secretary', 1),
(5, 'Records Staff', 1),
(6, 'Admission Officer', 2),
(7, 'Enrollment Coordinator', 2),
(8, 'Enrollment Clerk', 2),
(9, 'Document Verifier', 2),
(10, 'School Registrar', 3),
(11, 'Assistant Registrar', 3),
(12, 'Records Officer', 3),
(13, 'Transcript Processor', 3),
(14, 'Scheduling Officer ', 3),
(15, 'Records Clerk', 3),
(16, 'School Nurse ', 4),
(17, 'School Physician ', 4),
(18, 'Medical Assistant ', 4),
(19, 'Clinical Aide', 4),
(20, 'Health Records Staff', 4),
(21, 'Head Librarian ', 5),
(22, 'Assistant Librarian', 5),
(23, 'Library Staff', 5),
(24, 'Library Aide', 5),
(25, 'Laboratory Supervisor', 6),
(26, 'Lab Technician', 6),
(27, 'Lab Assistant', 6),
(28, 'Safety Assistant', 6),
(29, 'Monitoring Officer', 7),
(30, 'Attendance Officer', 7),
(31, 'Discipline Officer', 7),
(32, 'Compliance Staff', 7),
(33, 'Guidance Counselor', 8),
(34, 'Counseling Assistant', 8),
(35, 'Case Manager', 8),
(36, 'College Coordinator', 9),
(37, 'Program Chair', 9),
(38, 'Academic Secretary', 9),
(39, 'Faculty Coordinatory', 9),
(40, 'Curriculum Officer ', 9),
(41, 'Recruitment Officer', 12);

-- --------------------------------------------------------

--
-- Table structure for table `sd_reports`
--

CREATE TABLE `sd_reports` (
  `report_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `submitted_by` bigint(20) DEFAULT NULL,
  `status` enum('Pending','Approved','Returned') DEFAULT 'Pending',
  `submitted_at` datetime DEFAULT current_timestamp(),
  `report_type` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sd_reports`
--

INSERT INTO `sd_reports` (`report_id`, `title`, `description`, `file_path`, `department_id`, `submitted_by`, `status`, `submitted_at`, `report_type`) VALUES
(28, 'Sample 1', '', 'uploads/reports/report_69b780bacf34d6.15115163.pdf', 4, 1004, 'Pending', '2026-03-16 12:02:02', 13),
(29, 'Sample 2', '', 'uploads/reports/report_69b781bd1acf39.96001867.pdf', 1, 1001, 'Pending', '2026-03-16 12:06:21', 4),
(30, 'asdasdsad', '', 'uploads/reports/report_69b78b3e894484.07349849.pdf', 9, 1009, 'Pending', '2026-03-16 12:46:54', 25),
(31, 'Sample 3', '', 'uploads/reports/report_69b7957b84f415.04711894.pdf', 2, 1002, 'Pending', '2026-03-16 13:30:35', 7),
(32, 'Sample 1', '', 'uploads/reports/report_69b797cccff0e0.49659657.pdf', 8, 1007, 'Pending', '2026-03-16 13:40:28', 23),
(33, 'Sample 1', '', 'uploads/reports/report_69b799a0e8b638.41306710.pdf', 5, 1005, 'Pending', '2026-03-16 13:48:16', 16),
(34, 'dsadsadasd', '', 'uploads/reports/report_69b79f663949a2.60519511.pdf', 7, 1008, 'Pending', '2026-03-16 14:12:54', 22),
(36, 'pipehhh', '', 'uploads/reports/report_69ba11b31e76b0.25445082.pdf', 4, 1004, 'Pending', '2026-03-18 10:45:07', 13),
(37, 'labo', '', 'uploads/reports/report_69ba468abc1d98.10568920.pdf', 4, 1004, 'Pending', '2026-03-18 14:30:34', 15),
(38, 'ATTENDANCE', '', 'uploads/reports/report_69ba4b19a87ff3.23135058.docx', 7, 1008, 'Pending', '2026-03-18 14:50:01', 21),
(39, 'report', '', 'uploads/reports/report_69ba4ffb703ba7.17480080.pdf', 4, 1004, 'Pending', '2026-03-18 15:10:51', 13),
(40, 'Sample Report', '', 'uploads/reports/report_69ba7218cd27a5.61071600.docx', 9, 1009, 'Pending', '2026-03-18 17:36:24', 25),
(41, 'asdasda', '', 'uploads/reports/report_6a5887e543da60.99413787.pdf', 8, 1007, 'Pending', '2026-07-16 15:27:33', 23);

-- --------------------------------------------------------

--
-- Table structure for table `sd_report_type`
--

CREATE TABLE `sd_report_type` (
  `type_id` int(11) NOT NULL,
  `report_type` varchar(100) DEFAULT NULL,
  `department_id` int(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sd_report_type`
--

INSERT INTO `sd_report_type` (`type_id`, `report_type`, `department_id`) VALUES
(1, 'Enrollment Summary Report', 1),
(2, 'Financial Summary Report', 1),
(3, 'Academic Performance Summary', 1),
(4, 'Attendance Summary Report', 1),
(5, 'Incident Summary Report', 1),
(6, 'New Enrollees Report', 2),
(7, 'Enrollment Status Report', 2),
(8, 'Section Capacity Report', 2),
(9, 'Student Master List Report', 3),
(10, 'Grade Report (Per Term)', 3),
(11, 'Student Academic History Report', 3),
(12, 'Graduation Eligibility Report', 3),
(13, 'Clinic Visit Log Report', 4),
(14, 'Student Medical Record Summary', 4),
(15, 'Incident / Injury Report', 4),
(16, 'Borrowed Books Report', 5),
(17, 'Overdue Books Report', 5),
(18, 'Library Inventory Summary Report', 5),
(19, 'Laboratory Equipment Inventory Report', 6),
(20, 'Damaged Equipment Report', 6),
(21, 'Attendance Monitoring Report', 7),
(22, 'At-Risk Student Report', 7),
(23, 'Counseling Session Report', 8),
(24, 'Discipline Case Report', 8),
(25, 'Course Enrollment Report', 9),
(26, 'Faculty Load Report', 9),
(27, 'Graduation Progress Report', 9);

-- --------------------------------------------------------

--
-- Table structure for table `sd_roles`
--

CREATE TABLE `sd_roles` (
  `role_id` int(10) NOT NULL,
  `role_name` char(100) DEFAULT NULL,
  `department` int(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sd_roles`
--

INSERT INTO `sd_roles` (`role_id`, `role_name`, `department`) VALUES
(1, 'School Directress', 1),
(2, 'Enrollment Officer', 2),
(3, 'Registrar', 3),
(4, 'Clinic Staff', 4),
(5, 'Librarian', 5),
(6, 'Laboratory Staff', 6),
(7, 'Monitoring Officer', 7),
(8, 'Guidance Counselor', 8),
(9, 'College Coordinator', 9),
(10, 'Recruitment', 12);

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
-- Indexes for table `cc_events`
--
ALTER TABLE `cc_events`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `fk_event_template` (`template_id`);

--
-- Indexes for table `cc_event_templates`
--
ALTER TABLE `cc_event_templates`
  ADD PRIMARY KEY (`template_id`);

--
-- Indexes for table `cc_faculty`
--
ALTER TABLE `cc_faculty`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cc_faculty_load`
--
ALTER TABLE `cc_faculty_load`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_section_subject` (`section_id`,`subject_id`),
  ADD UNIQUE KEY `uq_faculty_load` (`section_id`,`subject_id`,`school_year_id`,`semester_id`),
  ADD KEY `idx_faculty` (`faculty_id`),
  ADD KEY `idx_section` (`section_id`),
  ADD KEY `idx_subject` (`subject_id`),
  ADD KEY `idx_school_year` (`school_year_id`),
  ADD KEY `idx_semester` (`semester_id`);

--
-- Indexes for table `cc_room`
--
ALTER TABLE `cc_room`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `room_code` (`room_code`);

--
-- Indexes for table `cc_schedule`
--
ALTER TABLE `cc_schedule`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_faculty_schedule` (`faculty_id`,`day_of_week`,`start_time`,`end_time`,`semester_id`,`school_year_id`),
  ADD UNIQUE KEY `uk_section_schedule` (`section_id`,`day_of_week`,`start_time`,`end_time`,`semester_id`,`school_year_id`),
  ADD UNIQUE KEY `uk_room_schedule` (`room_id`,`day_of_week`,`start_time`,`end_time`,`semester_id`,`school_year_id`),
  ADD KEY `fk_schedule_load` (`faculty_load_id`),
  ADD KEY `fk_schedule_subject` (`subject_id`),
  ADD KEY `fk_schedule_sy` (`school_year_id`),
  ADD KEY `fk_schedule_sem` (`semester_id`);

--
-- Indexes for table `cc_sections`
--
ALTER TABLE `cc_sections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_section` (`section_code`,`semester_id`,`school_year_id`),
  ADD KEY `fk_section_program` (`program_id`);

--
-- Indexes for table `cc_section_faculty`
--
ALTER TABLE `cc_section_faculty`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_section_faculty_role` (`section_id`,`faculty_id`,`role`,`school_year_id`,`semester_id`),
  ADD KEY `fk_sectionfaculty_sy` (`school_year_id`),
  ADD KEY `fk_sectionfaculty_sem` (`semester_id`),
  ADD KEY `fk_sectionfaculty_faculty` (`faculty_id`);

--
-- Indexes for table `cln_clinic_visits`
--
ALTER TABLE `cln_clinic_visits`
  ADD PRIMARY KEY (`visit_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `cln_departments`
--
ALTER TABLE `cln_departments`
  ADD PRIMARY KEY (`department_id`);

--
-- Indexes for table `cln_doctors`
--
ALTER TABLE `cln_doctors`
  ADD PRIMARY KEY (`doctor_id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `cln_incidents`
--
ALTER TABLE `cln_incidents`
  ADD PRIMARY KEY (`incident_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `cln_inventory_log`
--
ALTER TABLE `cln_inventory_log`
  ADD PRIMARY KEY (`inventory_id`),
  ADD KEY `medication_id` (`medication_id`);

--
-- Indexes for table `cln_medications`
--
ALTER TABLE `cln_medications`
  ADD PRIMARY KEY (`medication_id`);

--
-- Indexes for table `cln_patients`
--
ALTER TABLE `cln_patients`
  ADD PRIMARY KEY (`patient_id`);

--
-- Indexes for table `cln_roles`
--
ALTER TABLE `cln_roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `cln_staff`
--
ALTER TABLE `cln_staff`
  ADD PRIMARY KEY (`staff_id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `enr_announcements`
--
ALTER TABLE `enr_announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_publish` (`publish_date`,`expiry_date`),
  ADD KEY `idx_target` (`target_audience`);

--
-- Indexes for table `enr_applicants`
--
ALTER TABLE `enr_applicants`
  ADD PRIMARY KEY (`applicant_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `preferred_section_id` (`preferred_section_id`);

--
-- Indexes for table `enr_courses`
--
ALTER TABLE `enr_courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `course_code` (`course_code`),
  ADD KEY `idx_course_code` (`course_code`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indexes for table `enr_course_selections`
--
ALTER TABLE `enr_course_selections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_applicant_course` (`applicant_id`,`course_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `enr_documents`
--
ALTER TABLE `enr_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `verified_by` (`verified_by`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_applicant` (`applicant_id`);

--
-- Indexes for table `enr_document_requirements`
--
ALTER TABLE `enr_document_requirements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_order` (`sort_order`);

--
-- Indexes for table `enr_enrollments`
--
ALTER TABLE `enr_enrollments`
  ADD PRIMARY KEY (`enrollment_id`),
  ADD UNIQUE KEY `unique_enrollment` (`student_id`,`section_id`,`school_year`),
  ADD KEY `section_id` (`section_id`);

--
-- Indexes for table `enr_requirements`
--
ALTER TABLE `enr_requirements`
  ADD PRIMARY KEY (`requirement_id`);

--
-- Indexes for table `enr_sections`
--
ALTER TABLE `enr_sections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `section_code` (`section_code`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `idx_academic_year` (`academic_year`),
  ADD KEY `idx_semester` (`semester`),
  ADD KEY `idx_year_level` (`year_level`);

--
-- Indexes for table `enr_students`
--
ALTER TABLE `enr_students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `student_number` (`student_number`),
  ADD KEY `applicant_id` (`applicant_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `section_id` (`section_id`);

--
-- Indexes for table `enr_student_requirements`
--
ALTER TABLE `enr_student_requirements`
  ADD PRIMARY KEY (`student_requirement_id`),
  ADD UNIQUE KEY `unique_student_requirement` (`student_id`,`requirement_id`),
  ADD KEY `requirement_id` (`requirement_id`);

--
-- Indexes for table `enr_users`
--
ALTER TABLE `enr_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_user_type` (`user_type`);

--
-- Indexes for table `gd_appointments`
--
ALTER TABLE `gd_appointments`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `idx_appt_student` (`student_number`),
  ADD KEY `idx_appt_emp` (`counselor_id`),
  ADD KEY `idx_appt_case` (`case_id`);

--
-- Indexes for table `gd_cases`
--
ALTER TABLE `gd_cases`
  ADD PRIMARY KEY (`case_id`),
  ADD UNIQUE KEY `case_number` (`case_number`),
  ADD KEY `idx_case_student` (`student_number`),
  ADD KEY `idx_case_counselor` (`counselor_id`);

--
-- Indexes for table `gd_counseling_sessions`
--
ALTER TABLE `gd_counseling_sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `idx_cs_case` (`case_id`),
  ADD KEY `idx_cs_emp` (`counselor_id`);

--
-- Indexes for table `gd_counselor_schedules`
--
ALTER TABLE `gd_counselor_schedules`
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `idx_sched_emp` (`counselor_id`);

--
-- Indexes for table `gd_incidents`
--
ALTER TABLE `gd_incidents`
  ADD PRIMARY KEY (`incident_id`),
  ADD KEY `idx_inc_student` (`student_number`),
  ADD KEY `idx_inc_emp` (`reported_by`),
  ADD KEY `idx_inc_case` (`case_id`);

--
-- Indexes for table `gd_referrals`
--
ALTER TABLE `gd_referrals`
  ADD PRIMARY KEY (`referral_id`),
  ADD KEY `idx_ref_case` (`case_id`),
  ADD KEY `idx_ref_emp` (`referred_by`);

--
-- Indexes for table `gd_student_documents`
--
ALTER TABLE `gd_student_documents`
  ADD PRIMARY KEY (`document_id`),
  ADD KEY `idx_doc_student` (`student_number`),
  ADD KEY `idx_doc_emp` (`uploaded_by`);

--
-- Indexes for table `gd_student_profiles`
--
ALTER TABLE `gd_student_profiles`
  ADD PRIMARY KEY (`profile_id`),
  ADD KEY `idx_profile_student` (`student_number`);

--
-- Indexes for table `lab_crim_damage`
--
ALTER TABLE `lab_crim_damage`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lab_crim_inventory`
--
ALTER TABLE `lab_crim_inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lab_damage`
--
ALTER TABLE `lab_damage`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lab_he_borrows`
--
ALTER TABLE `lab_he_borrows`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lab_inventory`
--
ALTER TABLE `lab_inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lab_it_damage`
--
ALTER TABLE `lab_it_damage`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lab_it_inventory`
--
ALTER TABLE `lab_it_inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lab_physic_borrow`
--
ALTER TABLE `lab_physic_borrow`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lab_physic_damage`
--
ALTER TABLE `lab_physic_damage`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lab_physic_inventory`
--
ALTER TABLE `lab_physic_inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lab_psych_borrow`
--
ALTER TABLE `lab_psych_borrow`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lab_psych_inventory`
--
ALTER TABLE `lab_psych_inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lbr_activity_log`
--
ALTER TABLE `lbr_activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `lbr_books`
--
ALTER TABLE `lbr_books`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_isbn` (`isbn`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_genre` (`genre`);

--
-- Indexes for table `lbr_borrowers`
--
ALTER TABLE `lbr_borrowers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `borrower_id` (`borrower_id`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_borrower_id` (`borrower_id`);

--
-- Indexes for table `lbr_settings`
--
ALTER TABLE `lbr_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `lbr_transactions`
--
ALTER TABLE `lbr_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_book_id` (`book_id`),
  ADD KEY `idx_borrower_id` (`borrower_id`);

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
-- Indexes for table `rgr_activity_log`
--
ALTER TABLE `rgr_activity_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rgr_class_offerings`
--
ALTER TABLE `rgr_class_offerings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_class_offering_subject` (`subject_id`),
  ADD KEY `fk_class_offering_semester` (`semester_id`),
  ADD KEY `fk_class_offering_teacher` (`teacher_id`),
  ADD KEY `fk_class_offering_room` (`room_id`),
  ADD KEY `fk_class_offering_strand` (`strand_id`),
  ADD KEY `fk_class_offering_course` (`course_id`);

--
-- Indexes for table `rgr_class_schedules`
--
ALTER TABLE `rgr_class_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_schedules` (`class_offering_id`);

--
-- Indexes for table `rgr_courses`
--
ALTER TABLE `rgr_courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rgr_curriculums`
--
ALTER TABLE `rgr_curriculums`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_curriculum_course` (`course_id`);

--
-- Indexes for table `rgr_curriculum_subjects`
--
ALTER TABLE `rgr_curriculum_subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_curriculum_sub_curriculum` (`curriculum_id`),
  ADD KEY `fk_curriculum_sub_subject` (`subject_id`);

--
-- Indexes for table `rgr_document_requests`
--
ALTER TABLE `rgr_document_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rgr_enrollments`
--
ALTER TABLE `rgr_enrollments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_enrollment_course` (`course_id`),
  ADD KEY `fk_enrollment_semester` (`semester_id`),
  ADD KEY `fk_enrollment_student` (`student_id`),
  ADD KEY `fk_enrollment_strand` (`strand_id`);

--
-- Indexes for table `rgr_enrollment_subjects`
--
ALTER TABLE `rgr_enrollment_subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_enrollment_sub_teacher` (`teacher_id`),
  ADD KEY `fk_enrollment_sub_enrollments` (`enrollment_id`),
  ADD KEY `fk_enrollment_subject` (`subject_id`);

--
-- Indexes for table `rgr_events`
--
ALTER TABLE `rgr_events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rgr_notifications`
--
ALTER TABLE `rgr_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rgr_rooms`
--
ALTER TABLE `rgr_rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rgr_school_years`
--
ALTER TABLE `rgr_school_years`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rgr_semesters`
--
ALTER TABLE `rgr_semesters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_semesters_school_year` (`school_year_id`);

--
-- Indexes for table `rgr_strands`
--
ALTER TABLE `rgr_strands`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rgr_students`
--
ALTER TABLE `rgr_students`
  ADD UNIQUE KEY `students_student_number_unique` (`student_number`);

--
-- Indexes for table `rgr_subjects`
--
ALTER TABLE `rgr_subjects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rgr_teachers`
--
ALTER TABLE `rgr_teachers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sd_announcements`
--
ALTER TABLE `sd_announcements`
  ADD PRIMARY KEY (`announcement_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `sd_approvals`
--
ALTER TABLE `sd_approvals`
  ADD PRIMARY KEY (`approval_id`),
  ADD KEY `approver_id` (`approver_id`),
  ADD KEY `department` (`department`),
  ADD KEY `submit_by` (`submit_by`);

--
-- Indexes for table `sd_department`
--
ALTER TABLE `sd_department`
  ADD PRIMARY KEY (`department_id`),
  ADD KEY `department_head` (`department_head`);

--
-- Indexes for table `sd_issues`
--
ALTER TABLE `sd_issues`
  ADD PRIMARY KEY (`issue_id`),
  ADD KEY `submitted_by` (`submitted_by`),
  ADD KEY `department` (`department`);

--
-- Indexes for table `sd_position`
--
ALTER TABLE `sd_position`
  ADD PRIMARY KEY (`position_id`),
  ADD KEY `department` (`department`);

--
-- Indexes for table `sd_reports`
--
ALTER TABLE `sd_reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `submitted_by` (`submitted_by`),
  ADD KEY `report_type` (`report_type`);

--
-- Indexes for table `sd_report_type`
--
ALTER TABLE `sd_report_type`
  ADD PRIMARY KEY (`type_id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `sd_roles`
--
ALTER TABLE `sd_roles`
  ADD PRIMARY KEY (`role_id`),
  ADD KEY `department` (`department`);

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
-- AUTO_INCREMENT for table `cc_events`
--
ALTER TABLE `cc_events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `cc_event_templates`
--
ALTER TABLE `cc_event_templates`
  MODIFY `template_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `cc_faculty`
--
ALTER TABLE `cc_faculty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `cc_faculty_load`
--
ALTER TABLE `cc_faculty_load`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cc_room`
--
ALTER TABLE `cc_room`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `cc_schedule`
--
ALTER TABLE `cc_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cc_sections`
--
ALTER TABLE `cc_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `cc_section_faculty`
--
ALTER TABLE `cc_section_faculty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `cln_clinic_visits`
--
ALTER TABLE `cln_clinic_visits`
  MODIFY `visit_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cln_departments`
--
ALTER TABLE `cln_departments`
  MODIFY `department_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cln_doctors`
--
ALTER TABLE `cln_doctors`
  MODIFY `doctor_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cln_incidents`
--
ALTER TABLE `cln_incidents`
  MODIFY `incident_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cln_inventory_log`
--
ALTER TABLE `cln_inventory_log`
  MODIFY `inventory_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cln_medications`
--
ALTER TABLE `cln_medications`
  MODIFY `medication_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cln_patients`
--
ALTER TABLE `cln_patients`
  MODIFY `patient_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cln_roles`
--
ALTER TABLE `cln_roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cln_staff`
--
ALTER TABLE `cln_staff`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `enr_announcements`
--
ALTER TABLE `enr_announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `enr_applicants`
--
ALTER TABLE `enr_applicants`
  MODIFY `applicant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `enr_courses`
--
ALTER TABLE `enr_courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `enr_course_selections`
--
ALTER TABLE `enr_course_selections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `enr_documents`
--
ALTER TABLE `enr_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `enr_document_requirements`
--
ALTER TABLE `enr_document_requirements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `enr_enrollments`
--
ALTER TABLE `enr_enrollments`
  MODIFY `enrollment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `enr_requirements`
--
ALTER TABLE `enr_requirements`
  MODIFY `requirement_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `enr_sections`
--
ALTER TABLE `enr_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `enr_students`
--
ALTER TABLE `enr_students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `enr_student_requirements`
--
ALTER TABLE `enr_student_requirements`
  MODIFY `student_requirement_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `enr_users`
--
ALTER TABLE `enr_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `gd_appointments`
--
ALTER TABLE `gd_appointments`
  MODIFY `appointment_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `gd_cases`
--
ALTER TABLE `gd_cases`
  MODIFY `case_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `gd_counseling_sessions`
--
ALTER TABLE `gd_counseling_sessions`
  MODIFY `session_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `gd_counselor_schedules`
--
ALTER TABLE `gd_counselor_schedules`
  MODIFY `schedule_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gd_incidents`
--
ALTER TABLE `gd_incidents`
  MODIFY `incident_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `gd_referrals`
--
ALTER TABLE `gd_referrals`
  MODIFY `referral_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `gd_student_documents`
--
ALTER TABLE `gd_student_documents`
  MODIFY `document_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `gd_student_profiles`
--
ALTER TABLE `gd_student_profiles`
  MODIFY `profile_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `lab_crim_damage`
--
ALTER TABLE `lab_crim_damage`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lab_crim_inventory`
--
ALTER TABLE `lab_crim_inventory`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `lab_damage`
--
ALTER TABLE `lab_damage`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `lab_he_borrows`
--
ALTER TABLE `lab_he_borrows`
  MODIFY `id` int(120) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lab_inventory`
--
ALTER TABLE `lab_inventory`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `lab_it_damage`
--
ALTER TABLE `lab_it_damage`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lab_it_inventory`
--
ALTER TABLE `lab_it_inventory`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lab_physic_borrow`
--
ALTER TABLE `lab_physic_borrow`
  MODIFY `id` int(120) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lab_physic_damage`
--
ALTER TABLE `lab_physic_damage`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lab_physic_inventory`
--
ALTER TABLE `lab_physic_inventory`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `lab_psych_borrow`
--
ALTER TABLE `lab_psych_borrow`
  MODIFY `id` int(120) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lab_psych_inventory`
--
ALTER TABLE `lab_psych_inventory`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `lbr_activity_log`
--
ALTER TABLE `lbr_activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `lbr_books`
--
ALTER TABLE `lbr_books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `lbr_borrowers`
--
ALTER TABLE `lbr_borrowers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `lbr_settings`
--
ALTER TABLE `lbr_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `lbr_transactions`
--
ALTER TABLE `lbr_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `mon_attendance_archive`
--
ALTER TABLE `mon_attendance_archive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `mon_attendance_records`
--
ALTER TABLE `mon_attendance_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
-- AUTO_INCREMENT for table `rgr_activity_log`
--
ALTER TABLE `rgr_activity_log`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1092;

--
-- AUTO_INCREMENT for table `rgr_class_offerings`
--
ALTER TABLE `rgr_class_offerings`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rgr_class_schedules`
--
ALTER TABLE `rgr_class_schedules`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rgr_courses`
--
ALTER TABLE `rgr_courses`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `rgr_curriculums`
--
ALTER TABLE `rgr_curriculums`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `rgr_curriculum_subjects`
--
ALTER TABLE `rgr_curriculum_subjects`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `rgr_document_requests`
--
ALTER TABLE `rgr_document_requests`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `rgr_enrollments`
--
ALTER TABLE `rgr_enrollments`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rgr_enrollment_subjects`
--
ALTER TABLE `rgr_enrollment_subjects`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rgr_events`
--
ALTER TABLE `rgr_events`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT for table `rgr_notifications`
--
ALTER TABLE `rgr_notifications`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `rgr_rooms`
--
ALTER TABLE `rgr_rooms`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rgr_school_years`
--
ALTER TABLE `rgr_school_years`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `rgr_semesters`
--
ALTER TABLE `rgr_semesters`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `rgr_strands`
--
ALTER TABLE `rgr_strands`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `rgr_subjects`
--
ALTER TABLE `rgr_subjects`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `rgr_teachers`
--
ALTER TABLE `rgr_teachers`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sd_announcements`
--
ALTER TABLE `sd_announcements`
  MODIFY `announcement_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `sd_approvals`
--
ALTER TABLE `sd_approvals`
  MODIFY `approval_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `sd_department`
--
ALTER TABLE `sd_department`
  MODIFY `department_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `sd_issues`
--
ALTER TABLE `sd_issues`
  MODIFY `issue_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `sd_position`
--
ALTER TABLE `sd_position`
  MODIFY `position_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `sd_reports`
--
ALTER TABLE `sd_reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `sd_report_type`
--
ALTER TABLE `sd_report_type`
  MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `sd_roles`
--
ALTER TABLE `sd_roles`
  MODIFY `role_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
-- Constraints for table `cc_events`
--
ALTER TABLE `cc_events`
  ADD CONSTRAINT `fk_event_template` FOREIGN KEY (`template_id`) REFERENCES `cc_event_templates` (`template_id`);

--
-- Constraints for table `cc_faculty_load`
--
ALTER TABLE `cc_faculty_load`
  ADD CONSTRAINT `fk_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `cc_faculty` (`id`),
  ADD CONSTRAINT `fk_faculty_load_school_year` FOREIGN KEY (`school_year_id`) REFERENCES `rgr_school_years` (`id`),
  ADD CONSTRAINT `fk_faculty_load_semester` FOREIGN KEY (`semester_id`) REFERENCES `rgr_semesters` (`id`),
  ADD CONSTRAINT `fk_section` FOREIGN KEY (`section_id`) REFERENCES `cc_sections` (`id`),
  ADD CONSTRAINT `fk_subject` FOREIGN KEY (`subject_id`) REFERENCES `rgr_subjects` (`id`);

--
-- Constraints for table `cc_schedule`
--
ALTER TABLE `cc_schedule`
  ADD CONSTRAINT `fk_schedule_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `cc_faculty` (`id`),
  ADD CONSTRAINT `fk_schedule_load` FOREIGN KEY (`faculty_load_id`) REFERENCES `cc_faculty_load` (`id`),
  ADD CONSTRAINT `fk_schedule_room` FOREIGN KEY (`room_id`) REFERENCES `cc_room` (`id`),
  ADD CONSTRAINT `fk_schedule_section` FOREIGN KEY (`section_id`) REFERENCES `cc_sections` (`id`),
  ADD CONSTRAINT `fk_schedule_sem` FOREIGN KEY (`semester_id`) REFERENCES `rgr_semesters` (`id`),
  ADD CONSTRAINT `fk_schedule_subject` FOREIGN KEY (`subject_id`) REFERENCES `rgr_subjects` (`id`),
  ADD CONSTRAINT `fk_schedule_sy` FOREIGN KEY (`school_year_id`) REFERENCES `rgr_school_years` (`id`);

--
-- Constraints for table `cc_sections`
--
ALTER TABLE `cc_sections`
  ADD CONSTRAINT `fk_section_program` FOREIGN KEY (`program_id`) REFERENCES `rgr_courses` (`id`);

--
-- Constraints for table `cc_section_faculty`
--
ALTER TABLE `cc_section_faculty`
  ADD CONSTRAINT `fk_sectionfaculty_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `cc_faculty` (`id`),
  ADD CONSTRAINT `fk_sectionfaculty_section` FOREIGN KEY (`section_id`) REFERENCES `cc_sections` (`id`),
  ADD CONSTRAINT `fk_sectionfaculty_sem` FOREIGN KEY (`semester_id`) REFERENCES `rgr_semesters` (`id`),
  ADD CONSTRAINT `fk_sectionfaculty_sy` FOREIGN KEY (`school_year_id`) REFERENCES `rgr_school_years` (`id`);

--
-- Constraints for table `cln_clinic_visits`
--
ALTER TABLE `cln_clinic_visits`
  ADD CONSTRAINT `cln_clinic_visits_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `cln_patients` (`patient_id`),
  ADD CONSTRAINT `cln_clinic_visits_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `cln_doctors` (`doctor_id`);

--
-- Constraints for table `cln_doctors`
--
ALTER TABLE `cln_doctors`
  ADD CONSTRAINT `cln_doctors_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `cln_departments` (`department_id`);

--
-- Constraints for table `cln_incidents`
--
ALTER TABLE `cln_incidents`
  ADD CONSTRAINT `cln_incidents_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `cln_patients` (`patient_id`);

--
-- Constraints for table `cln_inventory_log`
--
ALTER TABLE `cln_inventory_log`
  ADD CONSTRAINT `cln_inventory_log_ibfk_1` FOREIGN KEY (`medication_id`) REFERENCES `cln_medications` (`medication_id`);

--
-- Constraints for table `cln_staff`
--
ALTER TABLE `cln_staff`
  ADD CONSTRAINT `cln_staff_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `cln_departments` (`department_id`),
  ADD CONSTRAINT `cln_staff_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `cln_roles` (`role_id`);

--
-- Constraints for table `enr_announcements`
--
ALTER TABLE `enr_announcements`
  ADD CONSTRAINT `enr_announcements_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `enr_users` (`id`);

--
-- Constraints for table `enr_applicants`
--
ALTER TABLE `enr_applicants`
  ADD CONSTRAINT `enr_applicants_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `rgr_courses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `enr_applicants_ibfk_2` FOREIGN KEY (`preferred_section_id`) REFERENCES `cc_sections` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `enr_course_selections`
--
ALTER TABLE `enr_course_selections`
  ADD CONSTRAINT `enr_course_selections_ibfk_1` FOREIGN KEY (`applicant_id`) REFERENCES `enr_applicants` (`applicant_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enr_course_selections_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `enr_courses` (`id`);

--
-- Constraints for table `enr_documents`
--
ALTER TABLE `enr_documents`
  ADD CONSTRAINT `enr_documents_ibfk_1` FOREIGN KEY (`applicant_id`) REFERENCES `enr_applicants` (`applicant_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enr_documents_ibfk_2` FOREIGN KEY (`verified_by`) REFERENCES `enr_users` (`id`);

--
-- Constraints for table `enr_enrollments`
--
ALTER TABLE `enr_enrollments`
  ADD CONSTRAINT `enr_enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `enr_students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enr_enrollments_ibfk_2` FOREIGN KEY (`section_id`) REFERENCES `cc_sections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `enr_sections`
--
ALTER TABLE `enr_sections`
  ADD CONSTRAINT `enr_sections_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `enr_courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `enr_students`
--
ALTER TABLE `enr_students`
  ADD CONSTRAINT `enr_students_ibfk_1` FOREIGN KEY (`applicant_id`) REFERENCES `enr_applicants` (`applicant_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enr_students_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `rgr_courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enr_students_ibfk_3` FOREIGN KEY (`section_id`) REFERENCES `cc_sections` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `enr_student_requirements`
--
ALTER TABLE `enr_student_requirements`
  ADD CONSTRAINT `enr_student_requirements_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `enr_students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enr_student_requirements_ibfk_2` FOREIGN KEY (`requirement_id`) REFERENCES `enr_requirements` (`requirement_id`) ON DELETE CASCADE;

--
-- Constraints for table `gd_appointments`
--
ALTER TABLE `gd_appointments`
  ADD CONSTRAINT `fk_appt_case` FOREIGN KEY (`case_id`) REFERENCES `gd_cases` (`case_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_appt_emp` FOREIGN KEY (`counselor_id`) REFERENCES `sms_employee` (`employee_id`),
  ADD CONSTRAINT `fk_appt_student` FOREIGN KEY (`student_number`) REFERENCES `rgr_students` (`student_number`) ON DELETE CASCADE;

--
-- Constraints for table `gd_cases`
--
ALTER TABLE `gd_cases`
  ADD CONSTRAINT `fk_case_counselor` FOREIGN KEY (`counselor_id`) REFERENCES `sms_employee` (`employee_id`),
  ADD CONSTRAINT `fk_case_student` FOREIGN KEY (`student_number`) REFERENCES `rgr_students` (`student_number`) ON DELETE CASCADE;

--
-- Constraints for table `gd_counseling_sessions`
--
ALTER TABLE `gd_counseling_sessions`
  ADD CONSTRAINT `fk_cs_case` FOREIGN KEY (`case_id`) REFERENCES `gd_cases` (`case_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cs_emp` FOREIGN KEY (`counselor_id`) REFERENCES `sms_employee` (`employee_id`);

--
-- Constraints for table `gd_counselor_schedules`
--
ALTER TABLE `gd_counselor_schedules`
  ADD CONSTRAINT `fk_sched_emp` FOREIGN KEY (`counselor_id`) REFERENCES `sms_employee` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `gd_incidents`
--
ALTER TABLE `gd_incidents`
  ADD CONSTRAINT `fk_inc_case` FOREIGN KEY (`case_id`) REFERENCES `gd_cases` (`case_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_inc_emp` FOREIGN KEY (`reported_by`) REFERENCES `sms_employee` (`employee_id`),
  ADD CONSTRAINT `fk_inc_student` FOREIGN KEY (`student_number`) REFERENCES `rgr_students` (`student_number`) ON DELETE CASCADE;

--
-- Constraints for table `gd_referrals`
--
ALTER TABLE `gd_referrals`
  ADD CONSTRAINT `fk_ref_case` FOREIGN KEY (`case_id`) REFERENCES `gd_cases` (`case_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ref_emp` FOREIGN KEY (`referred_by`) REFERENCES `sms_employee` (`employee_id`);

--
-- Constraints for table `gd_student_documents`
--
ALTER TABLE `gd_student_documents`
  ADD CONSTRAINT `fk_doc_emp` FOREIGN KEY (`uploaded_by`) REFERENCES `sms_employee` (`employee_id`),
  ADD CONSTRAINT `fk_doc_student` FOREIGN KEY (`student_number`) REFERENCES `rgr_students` (`student_number`) ON DELETE CASCADE;

--
-- Constraints for table `gd_student_profiles`
--
ALTER TABLE `gd_student_profiles`
  ADD CONSTRAINT `fk_gd_profile_student` FOREIGN KEY (`student_number`) REFERENCES `rgr_students` (`student_number`) ON DELETE CASCADE;

--
-- Constraints for table `lbr_transactions`
--
ALTER TABLE `lbr_transactions`
  ADD CONSTRAINT `lbr_transactions_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `lbr_books` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lbr_transactions_ibfk_2` FOREIGN KEY (`borrower_id`) REFERENCES `lbr_borrowers` (`id`) ON DELETE CASCADE;

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

--
-- Constraints for table `rgr_class_offerings`
--
ALTER TABLE `rgr_class_offerings`
  ADD CONSTRAINT `fk_class_offering_course` FOREIGN KEY (`course_id`) REFERENCES `rgr_courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_class_offering_room` FOREIGN KEY (`room_id`) REFERENCES `rgr_rooms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_class_offering_semester` FOREIGN KEY (`semester_id`) REFERENCES `rgr_semesters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_class_offering_strand` FOREIGN KEY (`strand_id`) REFERENCES `rgr_strands` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_class_offering_subject` FOREIGN KEY (`subject_id`) REFERENCES `rgr_subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_class_offering_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `rgr_teachers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rgr_class_schedules`
--
ALTER TABLE `rgr_class_schedules`
  ADD CONSTRAINT `fk_schedules` FOREIGN KEY (`class_offering_id`) REFERENCES `rgr_class_offerings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rgr_curriculums`
--
ALTER TABLE `rgr_curriculums`
  ADD CONSTRAINT `fk_curriculum_course` FOREIGN KEY (`course_id`) REFERENCES `rgr_courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rgr_curriculum_subjects`
--
ALTER TABLE `rgr_curriculum_subjects`
  ADD CONSTRAINT `fk_curriculum_sub_curriculum` FOREIGN KEY (`curriculum_id`) REFERENCES `rgr_curriculums` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_curriculum_sub_subject` FOREIGN KEY (`subject_id`) REFERENCES `rgr_subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rgr_enrollments`
--
ALTER TABLE `rgr_enrollments`
  ADD CONSTRAINT `fk_enrollment_course` FOREIGN KEY (`course_id`) REFERENCES `rgr_courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_enrollment_semester` FOREIGN KEY (`semester_id`) REFERENCES `rgr_semesters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_enrollment_strand` FOREIGN KEY (`strand_id`) REFERENCES `rgr_strands` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_enrollment_student` FOREIGN KEY (`student_id`) REFERENCES `rgr_students` (`student_number`) ON DELETE CASCADE;

--
-- Constraints for table `rgr_enrollment_subjects`
--
ALTER TABLE `rgr_enrollment_subjects`
  ADD CONSTRAINT `fk_enrollment_sub_enrollments` FOREIGN KEY (`enrollment_id`) REFERENCES `rgr_enrollments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_enrollment_sub_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `rgr_teachers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_enrollment_subject` FOREIGN KEY (`subject_id`) REFERENCES `rgr_subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rgr_semesters`
--
ALTER TABLE `rgr_semesters`
  ADD CONSTRAINT `fk_semesters_school_year` FOREIGN KEY (`school_year_id`) REFERENCES `rgr_school_years` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sd_announcements`
--
ALTER TABLE `sd_announcements`
  ADD CONSTRAINT `sd_announcements_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `sms_employee` (`employee_id`);

--
-- Constraints for table `sd_approvals`
--
ALTER TABLE `sd_approvals`
  ADD CONSTRAINT `sd_approvals_ibfk_1` FOREIGN KEY (`approver_id`) REFERENCES `sms_employee` (`employee_id`),
  ADD CONSTRAINT `sd_approvals_ibfk_2` FOREIGN KEY (`department`) REFERENCES `sd_department` (`department_id`),
  ADD CONSTRAINT `sd_approvals_ibfk_3` FOREIGN KEY (`submit_by`) REFERENCES `sms_employee` (`employee_id`);

--
-- Constraints for table `sd_department`
--
ALTER TABLE `sd_department`
  ADD CONSTRAINT `sd_department_ibfk_1` FOREIGN KEY (`department_head`) REFERENCES `sms_employee` (`employee_id`);

--
-- Constraints for table `sd_issues`
--
ALTER TABLE `sd_issues`
  ADD CONSTRAINT `sd_issues_ibfk_1` FOREIGN KEY (`submitted_by`) REFERENCES `sms_employee` (`employee_id`),
  ADD CONSTRAINT `sd_issues_ibfk_2` FOREIGN KEY (`department`) REFERENCES `sd_department` (`department_id`);

--
-- Constraints for table `sd_position`
--
ALTER TABLE `sd_position`
  ADD CONSTRAINT `sd_position_ibfk_1` FOREIGN KEY (`department`) REFERENCES `sd_department` (`department_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `sd_reports`
--
ALTER TABLE `sd_reports`
  ADD CONSTRAINT `sd_reports_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `sd_department` (`department_id`),
  ADD CONSTRAINT `sd_reports_ibfk_2` FOREIGN KEY (`submitted_by`) REFERENCES `sms_employee` (`employee_id`),
  ADD CONSTRAINT `sd_reports_ibfk_3` FOREIGN KEY (`report_type`) REFERENCES `sd_report_type` (`type_id`);

--
-- Constraints for table `sd_report_type`
--
ALTER TABLE `sd_report_type`
  ADD CONSTRAINT `sd_report_type_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `sd_department` (`department_id`);

--
-- Constraints for table `sd_roles`
--
ALTER TABLE `sd_roles`
  ADD CONSTRAINT `sd_roles_ibfk_1` FOREIGN KEY (`department`) REFERENCES `sd_department` (`department_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `sms_employee`
--
ALTER TABLE `sms_employee`
  ADD CONSTRAINT `sms_employee_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_account` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sms_employee_ibfk_2` FOREIGN KEY (`role`) REFERENCES `sd_roles` (`role_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sms_employee_ibfk_3` FOREIGN KEY (`department`) REFERENCES `sd_department` (`department_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sms_employee_ibfk_4` FOREIGN KEY (`position`) REFERENCES `sd_position` (`position_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `user_account`
--
ALTER TABLE `user_account`
  ADD CONSTRAINT `user_account_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `sms_employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

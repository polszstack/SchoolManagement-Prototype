-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 17, 2026 at 03:58 PM
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
-- Database: `bestlink`
--

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
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `application_number` varchar(20) NOT NULL,
  `surname` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `suffix` varchar(10) DEFAULT NULL,
  `sex` enum('Male','Female','Other') NOT NULL,
  `address_barangay` varchar(100) NOT NULL,
  `address_city` varchar(100) NOT NULL,
  `address_province` varchar(100) NOT NULL,
  `address_complete` text DEFAULT NULL,
  `school_last_attended` varchar(150) NOT NULL,
  `year_graduated` year(4) NOT NULL,
  `email` varchar(100) NOT NULL,
  `date_of_birth` date NOT NULL,
  `place_of_birth` varchar(150) NOT NULL,
  `age` int(11) DEFAULT NULL,
  `civil_status` enum('Single','Married','Divorced','Widowed') NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `parent_full_name` varchar(150) NOT NULL,
  `parent_contact` varchar(20) DEFAULT NULL,
  `parent_address` text DEFAULT NULL,
  `status` enum('pending','verified','converted','rejected') DEFAULT 'pending',
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enr_applicants`
--

INSERT INTO `enr_applicants` (`id`, `user_id`, `application_number`, `surname`, `first_name`, `middle_name`, `suffix`, `sex`, `address_barangay`, `address_city`, `address_province`, `address_complete`, `school_last_attended`, `year_graduated`, `email`, `date_of_birth`, `place_of_birth`, `age`, `civil_status`, `contact_number`, `parent_full_name`, `parent_contact`, `parent_address`, `status`, `submitted_at`, `updated_at`) VALUES
(1, 2, 'APP-2026-00001', 'Dela Cruz', 'Juan', 'Santos', NULL, 'Male', 'Poblacion', 'Manila', 'Metro Manila', 'Poblacion, Manila, Metro Manila', 'Manila High School', '2023', 'applicant@test.com', '2005-01-15', 'Manila', 19, 'Single', '09123456789', 'Maria Dela Cruz', '09123456788', NULL, 'converted', '2026-02-13 00:22:38', '2026-02-13 00:22:38'),
(2, 4, 'APP-2026-00002', 'Verceles', 'Edison', '', '', 'Male', 'aehaerh', 'dsfhdsfhsdfgdf', 'earhaer', 'aehaerh, dsfhdsfhsdfgdf, earhaer', 'Aehadf', '2023', 'abcdefg@gmail.com', '2004-03-14', 'adfb', 21, 'Single', '5464573657', 'Adfhadfh', '9278358235', 'rhsrthrsr', 'converted', '2026-03-05 10:39:13', '2026-03-08 05:15:34'),
(3, 5, 'APP-2026-00003', 'Yazier', 'Elyasen', 'Jainal', '', 'Male', 'aedsfda', 'aewdfadsf', 'afdssdsf', 'aedsfda, aewdfadsf, afdssdsf', 'Sm adhabsjbdaskjbd', '2023', 'yazier@gmail.com', '2004-05-15', 'kjabsDKasj', 21, 'Single', '9262063178', 'Dsfjdlasf', '9262781789', 'sahdbhbadsjAH', 'converted', '2026-03-08 05:22:33', '2026-03-08 05:25:07'),
(4, 7, 'APP-2026-00004', 'Testing', 'Dafdasf', 'Dafdas', 'dsafads', 'Male', 'SAJKBDKHAS', 'DJSBVFKHDB', 'asjbskajbdsa', 'SAJKBDKHAS, DJSBVFKHDB, asjbskajbdsa', 'Asjdbsajkd', '2022', 'testing@gmail.com', '2004-06-08', 'asjdksabd', 21, 'Single', '9123456789', 'Asjkdaskhd', '0262063178', 'asjasjbdhkas', 'converted', '2026-03-15 03:44:17', '2026-03-15 04:13:52'),
(5, 9, 'APP-2026-00005', 'Rgaga', 'Ergergqerg', 'Werdbfqreg', '', 'Male', 'ewefsd', 'dsfgesrg', 'ergadg', 'ewefsd, dsfgesrg, ergadg', 'Dfgsdfg', '2025', 'jkfghkdfs@gmail.com', '2004-03-01', 'ergerg', 22, 'Single', '9836463423', 'Dfgsd', '9836463423', 'sjdfhg', 'converted', '2026-03-16 06:16:16', '2026-03-16 07:27:53'),
(6, 11, 'APP-2026-00006', 'Vill', 'Beaa', 'Rega', 'qerg', 'Female', 'bdfg', 'eqrfaaregadg', 'adfg', 'bdfg, eqrfaaregadg, adfg', 'Agadsg', '2025', 'akjsdhv@gmail.com', '2005-01-01', 'dafba', 21, 'Single', '9876543456', 'Dfgad', '9748263452', 'adgasd', 'converted', '2026-03-17 10:43:51', '2026-03-17 10:49:03');

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
(2, 5, 4, 0, 'pending', NULL, '2026-03-16 06:17:05');

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
(7, 6, 'Form 138 / Report Card', 'Form_138___Report_Card_1773745600_69b935c01beea.pdf', 'uploads/requirements/6/Form_138___Report_Card_1773745600_69b935c01beea.pdf', 316222, 'application/pdf', 'rejected', NULL, '2026-03-17 14:34:32', 'jhasdhbas', '2026-03-17 11:06:40');

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
  `id` int(11) NOT NULL,
  `applicant_id` int(11) NOT NULL,
  `student_number` varchar(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `section_id` int(11) DEFAULT NULL,
  `year_level` int(11) DEFAULT 1,
  `enrollment_status` enum('enrolled','on_leave','graduated','dropped') DEFAULT 'enrolled',
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enr_students`
--

INSERT INTO `enr_students` (`id`, `applicant_id`, `student_number`, `user_id`, `course_id`, `section_id`, `year_level`, `enrollment_status`, `enrolled_at`) VALUES
(1, 1, 'STU-2026-00001', 3, 1, 1, 1, 'enrolled', '2026-02-13 00:22:38'),
(3, 3, 'STU-2026-00002', 5, 4, NULL, 1, 'enrolled', '2026-03-08 05:25:07'),
(4, 4, 'STU-2026-00003', 7, 4, 15, 1, 'enrolled', '2026-03-15 04:13:52'),
(5, 5, 'STU-2026-00004', 9, 4, 15, 1, 'enrolled', '2026-03-16 07:27:53'),
(6, 6, 'STU-2026-00005', 11, 4, 15, 1, 'enrolled', '2026-03-17 10:49:03');

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
(11, 'Beaa', 'bfd59291e825b5f2bbf1eb76569f8fe7', 'akjsdhv@gmail.com', 'student', 1, '2026-03-17 10:43:51', '2026-03-17 10:49:03');

--
-- Indexes for dumped tables
--

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
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `application_number` (`application_number`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_application_number` (`application_number`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_email` (`email`);

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
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `applicant_id` (`applicant_id`),
  ADD UNIQUE KEY `student_number` (`student_number`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `idx_student_number` (`student_number`),
  ADD KEY `idx_enrollment_status` (`enrollment_status`),
  ADD KEY `section_id` (`section_id`);

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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `enr_announcements`
--
ALTER TABLE `enr_announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `enr_applicants`
--
ALTER TABLE `enr_applicants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `enr_courses`
--
ALTER TABLE `enr_courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `enr_course_selections`
--
ALTER TABLE `enr_course_selections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `enr_documents`
--
ALTER TABLE `enr_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `enr_document_requirements`
--
ALTER TABLE `enr_document_requirements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `enr_sections`
--
ALTER TABLE `enr_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `enr_students`
--
ALTER TABLE `enr_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `enr_users`
--
ALTER TABLE `enr_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `enr_announcements`
--
ALTER TABLE `enr_announcements`
  ADD CONSTRAINT `enr_announcements_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `enr_users` (`id`);

--
-- Constraints for table `enr_applicants`
--
ALTER TABLE `enr_applicants`
  ADD CONSTRAINT `enr_applicants_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `enr_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `enr_course_selections`
--
ALTER TABLE `enr_course_selections`
  ADD CONSTRAINT `enr_course_selections_ibfk_1` FOREIGN KEY (`applicant_id`) REFERENCES `enr_applicants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enr_course_selections_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `enr_courses` (`id`);

--
-- Constraints for table `enr_documents`
--
ALTER TABLE `enr_documents`
  ADD CONSTRAINT `enr_documents_ibfk_1` FOREIGN KEY (`applicant_id`) REFERENCES `enr_applicants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enr_documents_ibfk_2` FOREIGN KEY (`verified_by`) REFERENCES `enr_users` (`id`);

--
-- Constraints for table `enr_sections`
--
ALTER TABLE `enr_sections`
  ADD CONSTRAINT `enr_sections_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `enr_courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `enr_students`
--
ALTER TABLE `enr_students`
  ADD CONSTRAINT `enr_students_ibfk_1` FOREIGN KEY (`applicant_id`) REFERENCES `enr_applicants` (`id`),
  ADD CONSTRAINT `enr_students_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `enr_users` (`id`),
  ADD CONSTRAINT `enr_students_ibfk_3` FOREIGN KEY (`course_id`) REFERENCES `enr_courses` (`id`),
  ADD CONSTRAINT `enr_students_ibfk_4` FOREIGN KEY (`section_id`) REFERENCES `enr_sections` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

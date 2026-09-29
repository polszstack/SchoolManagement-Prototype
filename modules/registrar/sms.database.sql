/*
SQLyog Community v13.3.0 (64 bit)
MySQL - 10.4.32-MariaDB : Database - sms
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `enr_announcements` */

DROP TABLE IF EXISTS `enr_announcements`;

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

/*Data for the table `enr_announcements` */

/*Table structure for table `enr_applicants` */

DROP TABLE IF EXISTS `enr_applicants`;

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

/*Data for the table `enr_applicants` */

/*Table structure for table `enr_course_selections` */

DROP TABLE IF EXISTS `enr_course_selections`;

CREATE TABLE `enr_course_selections` (
  `id` int(11) NOT NULL,
  `applicant_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `is_continuous` tinyint(1) DEFAULT 0,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `enr_course_selections` */

/*Table structure for table `enr_courses` */

DROP TABLE IF EXISTS `enr_courses`;

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

/*Data for the table `enr_courses` */

/*Table structure for table `enr_document_requirements` */

DROP TABLE IF EXISTS `enr_document_requirements`;

CREATE TABLE `enr_document_requirements` (
  `id` int(11) NOT NULL,
  `requirement_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_required` tinyint(4) DEFAULT 1,
  `is_active` tinyint(4) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `enr_document_requirements` */

/*Table structure for table `enr_documents` */

DROP TABLE IF EXISTS `enr_documents`;

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

/*Data for the table `enr_documents` */

/*Table structure for table `lbr_activity_log` */

DROP TABLE IF EXISTS `lbr_activity_log`;

CREATE TABLE `lbr_activity_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `action` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `user` varchar(100) DEFAULT 'Librarian',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `lbr_activity_log` */

insert  into `lbr_activity_log`(`id`,`action`,`details`,`user`,`created_at`) values 
(1,'System Initialized','Library Management System database created with sample data','System','2026-03-15 10:56:43'),
(2,'Books Added','Added 12 sample books to the library collection','System','2026-03-15 10:56:43'),
(3,'Members Added','Added 7 sample library members','System','2026-03-15 10:56:43');

/*Table structure for table `lbr_books` */

DROP TABLE IF EXISTS `lbr_books`;

CREATE TABLE `lbr_books` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_isbn` (`isbn`),
  KEY `idx_status` (`status`),
  KEY `idx_genre` (`genre`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `lbr_books` */

insert  into `lbr_books`(`id`,`title`,`author`,`genre`,`year`,`isbn`,`description`,`status`,`condition`,`cover_url`,`cover_local`,`added_date`,`last_updated`) values 
(1,'To Kill a Mockingbird','Harper Lee','Fiction',1960,'9780061935466','A Pulitzer Prize-winning masterwork of honor and injustice in the deep South—and the heroism of one man in the face of blind and violent hatred.','available','Excellent','https://covers.openlibrary.org/b/isbn/9780061935466-L.jpg',NULL,'2026-03-15','2026-03-15 10:56:43'),
(2,'1984','George Orwell','Science Fiction',1949,'9780451524935','A dystopian novel set in a totalitarian society where Big Brother watches everything and independent thought is a crime.','available','Good','https://covers.openlibrary.org/b/isbn/9780451524935-L.jpg',NULL,'2026-03-15','2026-03-15 10:56:43'),
(3,'The Great Gatsby','F. Scott Fitzgerald','Fiction',1925,'9780743273565','A story of the fabulously wealthy Jay Gatsby and his love for the beautiful Daisy Buchanan.','borrowed','Good','https://covers.openlibrary.org/b/isbn/9780743273565-L.jpg',NULL,'2026-03-15','2026-03-15 10:56:43'),
(4,'Harry Potter and the Sorcerer\'s Stone','J.K. Rowling','Fantasy',1997,'9780590353427','The first book in the beloved Harry Potter series following a young wizard\'s journey at Hogwarts School of Witchcraft and Wizardry.','available','Excellent','https://covers.openlibrary.org/b/isbn/9780590353427-L.jpg',NULL,'2026-03-15','2026-03-15 10:56:43'),
(5,'The Hunger Games','Suzanne Collins','Science Fiction',2008,'9780439023481','In a dark future, teenager Katniss Everdeen volunteers to take her sister\'s place in the deadly Hunger Games.','available','Good','https://covers.openlibrary.org/b/isbn/9780439023481-L.jpg',NULL,'2026-03-15','2026-03-15 10:56:43'),
(6,'Pride and Prejudice','Jane Austen','Romance',1813,'9780141439518','The story of Elizabeth Bennet and her evolving relationship with the proud Mr. Darcy in Regency England.','available','Excellent','https://covers.openlibrary.org/b/isbn/9780141439518-L.jpg',NULL,'2026-03-15','2026-03-15 10:56:43'),
(7,'The Hobbit','J.R.R. Tolkien','Fantasy',1937,'9780547928227','Bilbo Baggins, a hobbit who enjoys a comfortable life, is swept into an epic quest to reclaim treasure from a dragon.','available','Good','https://covers.openlibrary.org/b/isbn/9780547928227-L.jpg',NULL,'2026-03-15','2026-03-15 10:56:43'),
(8,'Animal Farm','George Orwell','Fiction',1945,'9780452284241','A satirical allegorical novella about a group of farm animals who rebel against their human farmer.','available','Fair','https://covers.openlibrary.org/b/isbn/9780452284241-L.jpg',NULL,'2026-03-15','2026-03-15 10:56:43'),
(9,'The Catcher in the Rye','J.D. Salinger','Fiction',1951,'9780316769174','A story about teenage rebellion and alienation told through the eyes of Holden Caulfield.','borrowed','Good','https://covers.openlibrary.org/b/isbn/9780316769174-L.jpg',NULL,'2026-03-15','2026-03-15 10:56:43'),
(10,'Brave New World','Aldous Huxley','Science Fiction',1932,'9780060850524','A dystopian novel set in a futuristic World State where citizens are environmentally engineered into intelligence castes.','available','Excellent','https://covers.openlibrary.org/b/isbn/9780060850524-L.jpg',NULL,'2026-03-15','2026-03-15 10:56:43'),
(11,'Charlotte\'s Web','E.B. White','Children',1952,'9780064400558','The story of a pig named Wilbur and his friendship with a barn spider named Charlotte.','available','Good','https://covers.openlibrary.org/b/isbn/9780064400558-L.jpg',NULL,'2026-03-15','2026-03-15 10:56:43'),
(12,'The Little Prince','Antoine de Saint-Exupéry','Children',1943,'9780156012195','A poetic tale about a young prince who travels from planet to planet and lands on Earth.','available','Excellent','https://covers.openlibrary.org/b/isbn/9780156012195-L.jpg',NULL,'2026-03-15','2026-03-15 10:56:43');

/*Table structure for table `lbr_borrowers` */

DROP TABLE IF EXISTS `lbr_borrowers`;

CREATE TABLE `lbr_borrowers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `borrower_id` varchar(50) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `type` enum('Student','Teacher','Staff') DEFAULT 'Student',
  `grade` varchar(30) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `join_date` date DEFAULT curdate(),
  `active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `borrower_id` (`borrower_id`),
  KEY `idx_type` (`type`),
  KEY `idx_borrower_id` (`borrower_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `lbr_borrowers` */

insert  into `lbr_borrowers`(`id`,`name`,`borrower_id`,`email`,`phone`,`type`,`grade`,`address`,`join_date`,`active`) values 
(1,'Alice Johnson','STU-001','alice@school.edu','555-0101','Student','Grade 10',NULL,'2024-01-15',1),
(2,'Bob Martinez','STU-002','bob@school.edu','555-0102','Student','Grade 11',NULL,'2024-01-20',1),
(3,'Carol Williams','STU-003','carol@school.edu','555-0103','Student','Grade 9',NULL,'2024-02-01',1),
(4,'David Brown','STU-004','david@school.edu','555-0104','Student','Grade 12',NULL,'2024-02-10',1),
(5,'Ms. Emily Chen','TCH-001','emily.chen@school.edu','555-0201','Teacher','Science Dept',NULL,'2023-09-01',1),
(6,'Mr. James Wilson','TCH-002','james.wilson@school.edu','555-0202','Teacher','English Dept',NULL,'2023-09-01',1),
(7,'Sarah Davis','STF-001','sarah.davis@school.edu','555-0301','Staff','Administration',NULL,'2023-08-15',1);

/*Table structure for table `lbr_settings` */

DROP TABLE IF EXISTS `lbr_settings`;

CREATE TABLE `lbr_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `lbr_settings` */

insert  into `lbr_settings`(`id`,`setting_key`,`setting_value`,`updated_at`) values 
(1,'library_name','School Library Management System','2026-03-15 10:56:43'),
(2,'max_borrow_days','14','2026-03-15 10:56:43'),
(3,'max_books_per_member','3','2026-03-15 10:56:43'),
(4,'daily_fine_rate','0.50','2026-03-15 10:56:43'),
(5,'auto_save','true','2026-03-15 10:56:43');

/*Table structure for table `lbr_transactions` */

DROP TABLE IF EXISTS `lbr_transactions`;

CREATE TABLE `lbr_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `book_id` int(11) NOT NULL,
  `borrower_id` int(11) NOT NULL,
  `borrow_date` date NOT NULL,
  `due_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `status` enum('active','returned','overdue') DEFAULT 'active',
  `condition` varchar(50) DEFAULT NULL,
  `fine` decimal(10,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_book_id` (`book_id`),
  KEY `idx_borrower_id` (`borrower_id`),
  CONSTRAINT `lbr_transactions_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `lbr_books` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lbr_transactions_ibfk_2` FOREIGN KEY (`borrower_id`) REFERENCES `lbr_borrowers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `lbr_transactions` */

insert  into `lbr_transactions`(`id`,`book_id`,`borrower_id`,`borrow_date`,`due_date`,`return_date`,`status`,`condition`,`fine`,`notes`,`created_at`) values 
(1,3,1,'2026-03-10','2026-03-24',NULL,'active',NULL,0.00,NULL,'2026-03-15 10:56:43'),
(2,9,2,'2026-02-23','2026-03-09',NULL,'overdue',NULL,3.00,NULL,'2026-03-15 10:56:43');

/*Table structure for table `mon_attendance` */

DROP TABLE IF EXISTS `mon_attendance`;

CREATE TABLE `mon_attendance` (
  `id` int(11) NOT NULL,
  `schedule_id` int(11) DEFAULT NULL,
  `attendance_date` date DEFAULT NULL,
  `faculty_id` int(11) DEFAULT NULL,
  `status` enum('Present','Late','Absent','Official Business','Early Dismissal','Academic Tour','No Teacher','Early Break') DEFAULT 'Present',
  `remarks` int(11) DEFAULT NULL,
  `class_type` enum('onsite','online') DEFAULT 'onsite',
  `online_platform` varchar(50) DEFAULT NULL,
  `meeting_link` text DEFAULT NULL,
  `meeting_id` varchar(100) DEFAULT NULL,
  `meeting_password` varchar(100) DEFAULT NULL,
  `online_attendance_file` varchar(255) DEFAULT NULL,
  `internet_status` enum('stable','unstable','intermittent') DEFAULT NULL,
  `connectivity_issues` text DEFAULT NULL,
  `recorded_by` varchar(100) DEFAULT NULL,
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `mon_attendance` */

/*Table structure for table `mon_courses` */

DROP TABLE IF EXISTS `mon_courses`;

CREATE TABLE `mon_courses` (
  `id` int(11) NOT NULL,
  `course_code` varchar(50) DEFAULT NULL,
  `course_name` varchar(200) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `credits` int(11) DEFAULT 3,
  `department` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `mon_courses` */

/*Table structure for table `mon_enrollment` */

DROP TABLE IF EXISTS `mon_enrollment`;

CREATE TABLE `mon_enrollment` (
  `id` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  `faculty_id` int(11) DEFAULT NULL,
  `semester` varchar(50) DEFAULT NULL,
  `school_year` varchar(50) DEFAULT NULL,
  `enrollment_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `mon_enrollment` */

/*Table structure for table `mon_facilities_monitor` */

DROP TABLE IF EXISTS `mon_facilities_monitor`;

CREATE TABLE `mon_facilities_monitor` (
  `id` int(11) NOT NULL,
  `room` varchar(100) NOT NULL,
  `issue_type` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `priority` enum('Low','Medium','High','Urgent') DEFAULT 'Medium',
  `reported_by` varchar(100) DEFAULT NULL,
  `status` enum('Pending','Fixed') DEFAULT 'Pending',
  `date_reported` date DEFAULT NULL,
  `date_fixed` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `mon_facilities_monitor` */

/*Table structure for table `mon_faculty` */

DROP TABLE IF EXISTS `mon_faculty`;

CREATE TABLE `mon_faculty` (
  `id` int(11) NOT NULL,
  `faculty_code` varchar(50) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `mon_faculty` */

/*Table structure for table `mon_program_monitor` */

DROP TABLE IF EXISTS `mon_program_monitor`;

CREATE TABLE `mon_program_monitor` (
  `id` int(11) NOT NULL,
  `program` varchar(100) NOT NULL,
  `semester` varchar(50) NOT NULL,
  `school_year` varchar(50) NOT NULL,
  `student_count` int(11) NOT NULL DEFAULT 0,
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `mon_program_monitor` */

/*Table structure for table `mon_reports` */

DROP TABLE IF EXISTS `mon_reports`;

CREATE TABLE `mon_reports` (
  `id` int(11) NOT NULL,
  `report_type` varchar(50) NOT NULL,
  `title` varchar(200) NOT NULL,
  `semester` varchar(50) DEFAULT NULL,
  `school_year` varchar(50) DEFAULT NULL,
  `report_date` date NOT NULL,
  `summary` text DEFAULT NULL,
  `generated_by` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `mon_reports` */

/*Table structure for table `mon_schedule` */

DROP TABLE IF EXISTS `mon_schedule`;

CREATE TABLE `mon_schedule` (
  `id` int(11) NOT NULL,
  `room` varchar(20) DEFAULT NULL,
  `official_time` varchar(50) DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `day_of_week` varchar(20) DEFAULT NULL,
  `subject_code` varchar(50) DEFAULT NULL,
  `grade_section_id` int(11) DEFAULT NULL,
  `faculty_id` int(11) DEFAULT NULL,
  `semester` varchar(50) DEFAULT NULL,
  `school_year` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `mon_schedule` */

/*Table structure for table `mon_sections` */

DROP TABLE IF EXISTS `mon_sections`;

CREATE TABLE `mon_sections` (
  `id` int(11) NOT NULL,
  `section_code` varchar(50) DEFAULT NULL,
  `grade_level` varchar(50) DEFAULT NULL,
  `program` varchar(100) DEFAULT NULL,
  `semester` varchar(50) DEFAULT NULL,
  `school_year` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `mon_sections` */

/*Table structure for table `mon_utilities_monitor` */

DROP TABLE IF EXISTS `mon_utilities_monitor`;

CREATE TABLE `mon_utilities_monitor` (
  `id` int(11) NOT NULL,
  `utility_type` varchar(50) NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  `semester` varchar(50) DEFAULT NULL,
  `school_year` varchar(50) DEFAULT NULL,
  `reading_date` date NOT NULL,
  `usage_value` decimal(10,2) NOT NULL DEFAULT 0.00,
  `unit` varchar(20) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `recorded_by` varchar(100) DEFAULT NULL,
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `mon_utilities_monitor` */

/*Table structure for table `mon_visitors_log` */

DROP TABLE IF EXISTS `mon_visitors_log`;

CREATE TABLE `mon_visitors_log` (
  `id` int(11) NOT NULL,
  `visitor_name` varchar(255) NOT NULL,
  `purpose` text DEFAULT NULL,
  `person_to_visit` varchar(255) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `id_presented` varchar(100) DEFAULT NULL,
  `recorded_by` varchar(100) DEFAULT NULL,
  `time_in` datetime DEFAULT current_timestamp(),
  `time_out` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `mon_visitors_log` */

/*Table structure for table `rgr_activity_log` */

DROP TABLE IF EXISTS `rgr_activity_log`;

CREATE TABLE `rgr_activity_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) DEFAULT NULL,
  `action` varchar(250) NOT NULL,
  `description` varchar(250) NOT NULL,
  `ip_address` varchar(250) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=323 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `rgr_activity_log` */

insert  into `rgr_activity_log`(`id`,`user_id`,`action`,`description`,`ip_address`,`created_at`,`updated_at`) values 
(9,NULL,'Get A PDF all student Report','Downloading a PDF file contains all students information','::1','2026-02-24 02:47:18','2026-02-24 02:47:18'),
(10,NULL,'Get A eXCEL all student Report','Downloading a Excel file contains all students information','::1','2026-02-25 14:46:53','2026-02-25 14:46:53'),
(11,NULL,'Get A PDF all student Report','Downloading a PDF file contains all students information','127.0.0.1','2026-02-25 17:00:35','2026-02-25 17:00:35'),
(12,NULL,'Get A PDF all student Report','Downloading a PDF file contains all students information','127.0.0.1','2026-02-25 17:02:21','2026-02-25 17:02:21'),
(13,NULL,'Get A PDF all student Report','Downloading a PDF file contains all students information','::1','2026-02-25 17:05:28','2026-02-25 17:05:28'),
(14,NULL,'Get A PDF all student Report','Downloading a PDF file contains all students information','::1','2026-02-25 17:05:51','2026-02-25 17:05:51'),
(42,NULL,'Created A New Course','Created A New Course Information for System','::1','2026-02-27 04:35:42','2026-02-27 04:35:42'),
(43,NULL,'Get A PDF Course student Report','Downloading a PDF file contains Course Information','::1','2026-02-27 04:38:49','2026-02-27 04:38:49'),
(44,NULL,'Created A New Course','Created A New Course Information for System','::1','2026-02-27 04:40:35','2026-02-27 04:40:35'),
(45,NULL,'Created A New Course','Created A New Course Information for System','::1','2026-02-27 05:23:02','2026-02-27 05:23:02'),
(66,NULL,'Created A New Course','Created A New Course Information for System','::1','2026-02-28 10:23:34','2026-02-28 10:23:34'),
(75,NULL,'Delete Course Input','Deleting 1 item/s Course Information','::1','2026-03-01 02:03:07','2026-03-01 02:03:07'),
(76,NULL,'Delete Course Input','Deleting 3 item/s Course Information','::1','2026-03-01 02:03:19','2026-03-01 02:03:19'),
(77,NULL,'Created A New Course','Created A New Course Information for System','::1','2026-03-01 02:03:41','2026-03-01 02:03:41'),
(78,NULL,'Delete Course Input','Deleting 1 item/s Course Information','::1','2026-03-01 02:03:48','2026-03-01 02:03:48'),
(79,NULL,'Created A New Course','Created A New Course Information for System','::1','2026-03-01 02:20:39','2026-03-01 02:20:39'),
(80,NULL,'Created A New Course','Created A New Course Information for System','::1','2026-03-01 04:08:42','2026-03-01 04:08:42'),
(81,NULL,'Delete Course Input','Deleting 2 item/s Course Information','::1','2026-03-01 04:14:47','2026-03-01 04:14:47'),
(82,NULL,'Created A New Course','Created A New Course Information for System','::1','2026-03-01 04:23:28','2026-03-01 04:23:28'),
(83,NULL,'Created A New Course','Created A New Course Information for System','::1','2026-03-01 04:24:32','2026-03-01 04:24:32'),
(84,NULL,'Delete Course Input','Deleting 2 item/s Course Information','::1','2026-03-01 23:05:54','2026-03-01 23:05:54'),
(114,NULL,'Created A New School Year','Created A New Schoool Year Information for System','::1','2026-03-03 02:45:05','2026-03-03 02:45:05'),
(115,NULL,'Created A New Course','Created A New Course Information for System','::1','2026-03-03 02:45:52','2026-03-03 02:45:52'),
(116,NULL,'Created A New School Year','Created A New Schoool Year Information for System','::1','2026-03-03 03:03:48','2026-03-03 03:03:48'),
(117,NULL,'Created A New School Year','Created A New Schoool Year Information for System','::1','2026-03-03 03:25:59','2026-03-03 03:25:59'),
(118,NULL,'Created A New School Year','Created A New Schoool Year Information for System','::1','2026-03-03 05:39:05','2026-03-03 05:39:05'),
(119,NULL,'Created A New School Year','Created A New Schoool Year Information for System','::1','2026-03-03 05:39:14','2026-03-03 05:39:14'),
(120,NULL,'Created A New School Year','Created A New Schoool Year Information for System','::1','2026-03-03 05:39:23','2026-03-03 05:39:23'),
(121,NULL,'Updated A New School Year','Updated A New Schoool Year Information for System','::1','2026-03-03 06:01:53','2026-03-03 06:01:53'),
(122,NULL,'Delete Course Input','Deleting 1 item/s Course Information','::1','2026-03-03 06:02:12','2026-03-03 06:02:12'),
(123,NULL,'Updated A New School Year','Updated A New Schoool Year Information for System','::1','2026-03-03 23:06:56','2026-03-03 23:06:56'),
(124,NULL,'Updated A New School Year','Updated A New Schoool Year Information for System','::1','2026-03-03 23:07:16','2026-03-03 23:07:16'),
(125,NULL,'Created A New School Year','Created A New Schoool Year Information for System','::1','2026-03-03 23:07:29','2026-03-03 23:07:29'),
(126,NULL,'Updated A New School Year','Updated A New Schoool Year Information for System','::1','2026-03-03 23:07:36','2026-03-03 23:07:36'),
(127,NULL,'Created A New Course','Created A New Course Information for System','::1','2026-03-03 23:23:36','2026-03-03 23:23:36'),
(128,NULL,'Delete Course Input','Deleting 1 item/s Course Information','::1','2026-03-03 23:23:45','2026-03-03 23:23:45'),
(129,NULL,'Get A PDF all student Report','Downloading a PDF file contains all students information','::1','2026-03-04 12:28:21','2026-03-04 12:28:21'),
(130,NULL,'Get A PDF all student Report','Downloading a PDF file contains all students information','::1','2026-03-04 12:29:30','2026-03-04 12:29:30'),
(131,NULL,'Get A PDF all student Report','Downloading a PDF file contains all students information','::1','2026-03-04 12:29:32','2026-03-04 12:29:32'),
(132,NULL,'Get A PDF all student Report','Downloading a PDF file contains all students information','::1','2026-03-04 12:29:33','2026-03-04 12:29:33'),
(133,NULL,'Get A PDF all student Report','Downloading a PDF file contains all students information','::1','2026-03-04 12:29:43','2026-03-04 12:29:43'),
(134,NULL,'Get A PDF all student Report','Downloading a PDF file contains all students information','::1','2026-03-04 12:29:54','2026-03-04 12:29:54'),
(135,NULL,'Get A PDF all student Report','Downloading a PDF file contains all students information','::1','2026-03-04 12:32:56','2026-03-04 12:32:56'),
(136,NULL,'Get A PDF all student Report','Downloading a PDF file contains all students information','::1','2026-03-04 12:33:06','2026-03-04 12:33:06'),
(137,NULL,'Get A PDF all student Report','Downloading a PDF file contains all students information','::1','2026-03-04 12:33:44','2026-03-04 12:33:44'),
(138,NULL,'Get A PDF all student Report','Downloading a PDF file contains all students information','::1','2026-03-04 12:35:41','2026-03-04 12:35:41'),
(139,NULL,'Get A PDF all student Report','Downloading a PDF file contains all students information','::1','2026-03-04 12:36:53','2026-03-04 12:36:53'),
(140,NULL,'Get A PDF all student Report','Downloading a PDF file contains all students information','::1','2026-03-04 12:36:53','2026-03-04 12:36:53'),
(141,NULL,'Get A PDF all student Report','Downloading a PDF file contains all students information','::1','2026-03-04 12:42:10','2026-03-04 12:42:10'),
(142,NULL,'Get A PDF all student Report','Downloading a PDF file contains all students information','::1','2026-03-04 12:42:21','2026-03-04 12:42:21'),
(143,NULL,'Get A PDF all student Report','Downloading a PDF file contains all students information','::1','2026-03-04 12:42:23','2026-03-04 12:42:23'),
(164,NULL,'Get A PDF Course student Report','Downloading a PDF file contains Course Information','::1','2026-03-04 15:32:50','2026-03-04 15:32:50'),
(165,NULL,'Get A PDF all student Report','Downloading a PDF file contains all students information','::1','2026-03-04 15:33:07','2026-03-04 15:33:07'),
(172,NULL,'Deleted A New School Year','Deleted A New Schoool Year Information for System','::1','2026-03-05 08:16:41','2026-03-05 08:16:41'),
(173,NULL,'Created A New School Year','Created A New Schoool Year Information for System','::1','2026-03-05 09:25:00','2026-03-05 09:25:00'),
(174,NULL,'Deleted A Semester Record','Deleted A Semester Record Information for System','::1','2026-03-05 09:26:01','2026-03-05 09:26:01'),
(175,NULL,'Created A New School Year','Created A New Schoool Year Information for System','::1','2026-03-05 09:26:13','2026-03-05 09:26:13'),
(176,NULL,'Created A New School Year','Created A New Schoool Year Information for System','::1','2026-03-05 09:26:27','2026-03-05 09:26:27'),
(177,NULL,'Updated A New School Year','Updated A New Schoool Year Information for System','::1','2026-03-05 09:26:41','2026-03-05 09:26:41'),
(178,NULL,'Created A New School Year','Created A New Schoool Year Information for System','::1','2026-03-05 14:38:03','2026-03-05 14:38:03'),
(179,NULL,'Created A New School Year','Created A New Schoool Year Information for System','::1','2026-03-05 14:38:24','2026-03-05 14:38:24'),
(180,NULL,'Updated A New School Year','Updated A New Schoool Year Information for System','::1','2026-03-05 14:46:31','2026-03-05 14:46:31'),
(181,NULL,'Updated A New School Year','Updated A New Schoool Year Information for System','::1','2026-03-05 14:47:01','2026-03-05 14:47:01'),
(182,NULL,'Get A PDF Course student Report','Downloading a PDF file contains Course Information','::1','2026-03-05 14:47:11','2026-03-05 14:47:11'),
(183,NULL,'Get A PDF Course student Report','Downloading a PDF file contains Course Information','::1','2026-03-05 15:06:52','2026-03-05 15:06:52'),
(184,NULL,'Get A PDF Course student Report','Downloading a PDF file contains Course Information','::1','2026-03-05 15:07:18','2026-03-05 15:07:18'),
(185,NULL,'Get A PDF Course student Report','Downloading a PDF file contains Course Information','::1','2026-03-05 15:07:27','2026-03-05 15:07:27'),
(186,NULL,'Get A PDF Course student Report','Downloading a PDF file contains Course Information','::1','2026-03-05 15:08:03','2026-03-05 15:08:03'),
(187,NULL,'Get A PDF Course student Report','Downloading a PDF file contains Course Information','::1','2026-03-05 15:10:41','2026-03-05 15:10:41'),
(188,NULL,'Get A PDF Course student Report','Downloading a PDF file contains Course Information','::1','2026-03-05 15:25:52','2026-03-05 15:25:52'),
(189,NULL,'Get A PDF Course student Report','Downloading a PDF file contains Course Information','::1','2026-03-05 15:26:08','2026-03-05 15:26:08'),
(190,NULL,'Get A PDF Course student Report','Downloading a PDF file contains Course Information','::1','2026-03-05 15:26:57','2026-03-05 15:26:57'),
(213,NULL,'Get A CSV of School Year Report','Downloading a CSV file contains School Year Information','::1','2026-03-06 03:24:38','2026-03-06 03:24:38'),
(214,NULL,'Created A New Course','Created A New Course Information for System','::1','2026-03-06 03:32:23','2026-03-06 03:32:23'),
(215,NULL,'Updated A Course','Updated the Course Information.','::1','2026-03-06 03:32:42','2026-03-06 03:32:42'),
(216,NULL,'Get A PDF of School Year Report','Downloading a PDF file contains School Year Information','::1','2026-03-06 03:32:50','2026-03-06 03:32:50'),
(217,NULL,'Get A PDF all student Report','Downloading a PDF file contains all students information','::1','2026-03-06 05:56:45','2026-03-06 05:56:45'),
(218,NULL,'Get A PDF Course student Report','Downloading a PDF file contains Course Information','::1','2026-03-06 05:57:04','2026-03-06 05:57:04'),
(219,NULL,'Updated A Course','Updated the Course Information.','::1','2026-03-06 11:03:40','2026-03-06 11:03:40'),
(220,NULL,'Updated A Course','Updated the Course Information.','::1','2026-03-06 11:03:46','2026-03-06 11:03:46'),
(221,NULL,'Get A PDF Course student Report','Downloading a PDF file contains Course Information','::1','2026-03-06 11:03:50','2026-03-06 11:03:50'),
(222,NULL,'Delete Course Input','Deleting 1 item/s Course Information','::1','2026-03-06 11:05:30','2026-03-06 11:05:30'),
(223,NULL,'Created A New Course','Created A New Course Information for System','::1','2026-03-06 11:06:02','2026-03-06 11:06:02'),
(224,NULL,'Get A Excel Course student Report','Downloading a Excel file contains Course Information','::1','2026-03-06 11:16:42','2026-03-06 11:16:42'),
(225,NULL,'Created A New Course','Created A New Course Information for System','::1','2026-03-06 11:24:15','2026-03-06 11:24:15'),
(226,NULL,'Created A New School Year','Created A New Schoool Year Information for System','::1','2026-03-06 11:38:01','2026-03-06 11:38:01'),
(227,NULL,'Deleted A New School Year','Deleted A New Schoool Year Information for System','::1','2026-03-06 11:45:31','2026-03-06 11:45:31'),
(232,NULL,'Get A CSV Course student Report','Downloading a CSV file contains Course Information','::1','2026-03-06 15:10:29','2026-03-06 15:10:29'),
(233,NULL,'Deleted A Semester Record','Deleted A Semester Record Information for System','::1','2026-03-07 01:40:13','2026-03-07 01:40:13'),
(234,NULL,'Get A PDF all student Report','Downloading a PDF file contains all students information','::1','2026-03-07 01:48:32','2026-03-07 01:48:32'),
(235,NULL,'Created A New School Semester','Created A New Schoool Semester Information for System','::1','2026-03-07 02:07:39','2026-03-07 02:07:39'),
(236,NULL,'Created A New Strand','Created A New Strand Information for System','::1','2026-03-07 02:31:19','2026-03-07 02:31:19'),
(237,NULL,'Created A New Strand','Created A New Strand Information for System','::1','2026-03-07 02:41:52','2026-03-07 02:41:52'),
(238,NULL,'Created A New Strand','Created A New Strand Information for System','::1','2026-03-07 02:44:12','2026-03-07 02:44:12'),
(239,NULL,'Created A New Strand','Created A New Strand Information for System','::1','2026-03-07 02:44:45','2026-03-07 02:44:45'),
(240,NULL,'Get A CSV Course student Report','Downloading a CSV file contains Course Information','::1','2026-03-07 02:44:53','2026-03-07 02:44:53'),
(241,NULL,'Delete Course Input','Deleting 1 item/s Course Information','::1','2026-03-07 02:47:26','2026-03-07 02:47:26'),
(242,NULL,'Delete Course Input','Deleting 1 item/s Course Information','::1','2026-03-07 02:47:43','2026-03-07 02:47:43'),
(243,NULL,'Delete Course Input','Deleting 1 item/s Course Information','::1','2026-03-07 02:47:44','2026-03-07 02:47:44'),
(244,NULL,'Delete Strand Input','Deleting 1 item/s Course Information','::1','2026-03-07 02:48:36','2026-03-07 02:48:36'),
(245,NULL,'Delete Strand Input','Deleting 1 item/s Course Information','::1','2026-03-07 02:48:41','2026-03-07 02:48:41'),
(246,NULL,'Delete Strand Input','Deleting 2 item/s Course Information','::1','2026-03-07 02:48:49','2026-03-07 02:48:49'),
(247,NULL,'Updated A Strand','Updated the Strand Information.','::1','2026-03-07 03:23:49','2026-03-07 03:23:49'),
(248,NULL,'Updated A Strand','Updated the Strand Information.','::1','2026-03-07 03:23:51','2026-03-07 03:23:51'),
(249,NULL,'Updated A Strand','Updated the Strand Information.','::1','2026-03-07 03:24:02','2026-03-07 03:24:02'),
(250,NULL,'Updated A Strand','Updated the Strand Information.','::1','2026-03-07 03:24:19','2026-03-07 03:24:19'),
(251,NULL,'Updated A Strand','Updated the Strand Information.','::1','2026-03-07 03:24:38','2026-03-07 03:24:38'),
(252,NULL,'Updated A Strand','Updated the Strand Information.','::1','2026-03-07 03:25:23','2026-03-07 03:25:23'),
(253,NULL,'Updated A Strand','Updated the Strand Information.','::1','2026-03-07 03:25:40','2026-03-07 03:25:40'),
(254,NULL,'Created A New Strand','Created A New Strand Information for System','::1','2026-03-07 03:26:51','2026-03-07 03:26:51'),
(255,NULL,'Delete Strand Input','Deleting 1 item/s Course Information','::1','2026-03-07 03:26:59','2026-03-07 03:26:59'),
(258,NULL,'Get A PDF of Semester Report','Downloading a PDF file contains Semester Information','::1','2026-03-08 02:32:41','2026-03-08 02:32:41'),
(259,NULL,'Get A PDF of Semester Report','Downloading a PDF file contains Semester Information','::1','2026-03-08 02:33:42','2026-03-08 02:33:42'),
(260,NULL,'Get A PDF of Semester Report','Downloading a PDF file contains Semester Information','::1','2026-03-08 02:33:58','2026-03-08 02:33:58'),
(261,NULL,'Get A PDF of Strands Report','Downloading a PDF file contains Strands Information','::1','2026-03-08 02:34:38','2026-03-08 02:34:38'),
(262,NULL,'Get A PDF of Strands Report','Downloading a PDF file contains Strands Information','::1','2026-03-08 02:34:54','2026-03-08 02:34:54'),
(263,NULL,'Get A Excel of Strand Report','Downloading a Excel file contains Semester Information','::1','2026-03-08 02:40:30','2026-03-08 02:40:30'),
(264,NULL,'Get A CSV of School Year Report','Downloading a CSV file contains School Year Information','::1','2026-03-08 02:43:15','2026-03-08 02:43:15'),
(265,NULL,'Updated A Strand','Updated the Strand Information.','::1','2026-03-08 02:44:04','2026-03-08 02:44:04'),
(266,NULL,'Updated A Strand','Updated the Strand Information.','::1','2026-03-08 02:44:09','2026-03-08 02:44:09'),
(267,NULL,'Get A CSV of Strand Report','Downloading a CSV file contains Strand Information','::1','2026-03-08 02:47:48','2026-03-08 02:47:48'),
(268,NULL,'Get A Excel of Strand Report','Downloading a Excel file contains Semester Information','::1','2026-03-08 02:48:12','2026-03-08 02:48:12'),
(269,NULL,'Delete Course Input','Deleting 1 item/s Course Information','::1','2026-03-08 13:36:43','2026-03-08 13:36:43'),
(270,NULL,'Get A PDF all student Report','Downloading a PDF file contains all students information','::1','2026-03-09 22:52:05','2026-03-09 22:52:05'),
(271,NULL,'Get A PDF all student Report','Downloading a PDF file contains all students information','::1','2026-03-09 23:59:37','2026-03-09 23:59:37'),
(272,NULL,'Get A PDF all student Report','Downloading a PDF file contains all students information','::1','2026-03-10 00:05:23','2026-03-10 00:05:23'),
(275,NULL,'Delete Course Input','Deleting 1 item/s Course Information','::1','2026-03-11 16:04:21','2026-03-11 16:04:21'),
(276,NULL,'Get A PDF Course student Report','Downloading a PDF file contains Course Information','::1','2026-03-11 16:55:21','2026-03-11 16:55:21'),
(277,NULL,'Get A PDF Course student Report','Downloading a PDF file contains Course Information','::1','2026-03-11 17:01:14','2026-03-11 17:01:14'),
(278,NULL,'Get A PDF of School Year Report','Downloading a PDF file contains School Year Information','::1','2026-03-11 17:17:13','2026-03-11 17:17:13'),
(279,NULL,'Created A New Subject','Created A New Subject Information for System','::1','2026-03-12 11:25:14','2026-03-12 11:25:14'),
(280,NULL,'Created A New Subject','Created A New Subject Information for System','::1','2026-03-12 11:25:18','2026-03-12 11:25:18'),
(281,NULL,'Created A New Subject','Created A New Subject Information for System','::1','2026-03-12 11:25:28','2026-03-12 11:25:28'),
(282,NULL,'Created A New Subject','Created A New Subject Information for System','::1','2026-03-12 11:25:29','2026-03-12 11:25:29'),
(283,NULL,'Created A New Subject','Created A New Subject Information for System','::1','2026-03-12 11:25:29','2026-03-12 11:25:29'),
(284,NULL,'Created A New Subject','Created A New Subject Information for System','::1','2026-03-12 11:26:25','2026-03-12 11:26:25'),
(285,NULL,'Get A CSV Course student Report','Downloading a CSV file contains Course Information','::1','2026-03-12 11:26:43','2026-03-12 11:26:43'),
(286,NULL,'Created A New Subject','Created A New Subject Information for System','::1','2026-03-12 11:27:06','2026-03-12 11:27:06'),
(287,NULL,'Delete Subject Input','Deleting 2 item/s Course Information','::1','2026-03-12 11:32:19','2026-03-12 11:32:19'),
(288,NULL,'Delete Subject Input','Deleting 1 item/s Course Information','::1','2026-03-12 11:34:12','2026-03-12 11:34:12'),
(289,NULL,'Delete Subject Input','Deleting 1 item/s Course Information','::1','2026-03-12 11:36:02','2026-03-12 11:36:02'),
(290,NULL,'Delete Subject Input','Deleting 1 item/s Subject Information','::1','2026-03-12 11:39:37','2026-03-12 11:39:37'),
(291,NULL,'Delete Subject Input','Deleting 1 item/s Subject Information','::1','2026-03-12 11:41:40','2026-03-12 11:41:40'),
(292,NULL,'Delete Subject Input','Deleting 1 item/s Subject Information','::1','2026-03-12 11:42:39','2026-03-12 11:42:39'),
(293,NULL,'Delete Strand Input','Deleting 1 item/s Course Information','::1','2026-03-12 11:44:39','2026-03-12 11:44:39'),
(294,NULL,'Delete Subject Input','Deleting 1 item/s Subject Information','::1','2026-03-12 11:47:49','2026-03-12 11:47:49'),
(295,NULL,'Get A CSV Course student Report','Downloading a CSV file contains Course Information','::1','2026-03-12 11:48:23','2026-03-12 11:48:23'),
(296,NULL,'Delete Subject Input','Deleting 1 item/s Subject Information','::1','2026-03-12 11:49:19','2026-03-12 11:49:19'),
(297,NULL,'Delete Subject Input','Deleting 1 item/s Subject Information','::1','2026-03-12 11:49:54','2026-03-12 11:49:54'),
(298,NULL,'Delete Subject Input','Deleting 1 item/s Subject Information','::1','2026-03-12 11:51:40','2026-03-12 11:51:40'),
(299,NULL,'Delete Subject Input','Deleting 1 item/s Subject Information','::1','2026-03-12 11:53:50','2026-03-12 11:53:50'),
(300,NULL,'Delete Subject Input','Deleting 1 item/s Subject Information','::1','2026-03-12 11:54:14','2026-03-12 11:54:14'),
(301,NULL,'Delete Subject Input','Deleting 1 item/s Subject Information','::1','2026-03-12 11:54:21','2026-03-12 11:54:21'),
(302,NULL,'Delete Subject Input','Deleting 1 item/s Subject Information','::1','2026-03-12 11:55:21','2026-03-12 11:55:21'),
(303,NULL,'Delete Subject Input','Deleting 1 item/s Subject Information','::1','2026-03-12 11:55:26','2026-03-12 11:55:26'),
(304,NULL,'Delete Subject Input','Deleting 1 item/s Subject Information','::1','2026-03-12 11:57:10','2026-03-12 11:57:10'),
(305,NULL,'Deleted A Semester Record','Deleted A Semester Record Information for System','::1','2026-03-12 11:59:14','2026-03-12 11:59:14'),
(306,NULL,'Delete Subject Input','Deleting 3 item/s Subject Information','::1','2026-03-12 12:01:04','2026-03-12 12:01:04'),
(307,NULL,'Delete Subject Input','Deleting 4 item/s Subject Information','::1','2026-03-12 12:01:16','2026-03-12 12:01:16'),
(308,NULL,'Created A New Subject','Created A New Subject Information for System','::1','2026-03-12 12:01:50','2026-03-12 12:01:50'),
(309,NULL,'Get A PDF all student Report','Downloading a PDF file contains all students information','::1','2026-03-15 11:33:15','2026-03-15 11:33:15'),
(310,NULL,'Created A New Course','Created A New Course Information for System','::1','2026-03-15 12:37:52','2026-03-15 12:37:52'),
(311,NULL,'Delete Course Input','Deleting 1 item/s Course Information','::1','2026-03-15 12:38:00','2026-03-15 12:38:00'),
(312,NULL,'Created A New School Year','Created A New Schoool Year Information for System','::1','2026-03-15 12:38:15','2026-03-15 12:38:15'),
(313,NULL,'Deleted A New School Year','Deleted A New Schoool Year Information for System','::1','2026-03-15 12:38:22','2026-03-15 12:38:22'),
(316,NULL,'Get A PDF all student Report','Downloading a PDF file contains all students information','::1','2026-03-15 12:40:59','2026-03-15 12:40:59'),
(317,NULL,'Get A PDF all student Report','Downloading a PDF file contains all students information','::1','2026-03-15 12:41:04','2026-03-15 12:41:04'),
(318,NULL,'Get A PDF Course student Report','Downloading a PDF file contains Course Information','::1','2026-03-15 12:41:14','2026-03-15 12:41:14'),
(319,NULL,'Updated A New School Year','Updated A New Schoool Year Information for System','::1','2026-03-15 12:42:33','2026-03-15 12:42:33'),
(320,NULL,'Get A PDF all student Report','Downloading a PDF file contains all students information','::1','2026-03-15 12:42:43','2026-03-15 12:42:43'),
(321,NULL,'Get A PDF all student Report','Downloading a PDF file contains all students information','::1','2026-03-15 14:42:46','2026-03-15 14:42:46'),
(322,NULL,'Get A PDF all student Report','Downloading a PDF file contains all students information','::1','2026-03-15 14:44:12','2026-03-15 14:44:12');

/*Table structure for table `rgr_class_offerings` */

DROP TABLE IF EXISTS `rgr_class_offerings`;

CREATE TABLE `rgr_class_offerings` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `subject_id` int(10) NOT NULL,
  `semester_id` int(10) NOT NULL,
  `teacher_id` int(10) NOT NULL,
  `room_id` int(10) NOT NULL,
  `year_level` varchar(250) NOT NULL,
  `strand_id` int(10) DEFAULT NULL,
  `course_id` int(10) DEFAULT NULL,
  `section_name` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_class_offering_subject` (`subject_id`),
  KEY `fk_class_offering_semester` (`semester_id`),
  KEY `fk_class_offering_teacher` (`teacher_id`),
  KEY `fk_class_offering_room` (`room_id`),
  KEY `fk_class_offering_strand` (`strand_id`),
  KEY `fk_class_offering_course` (`course_id`),
  CONSTRAINT `fk_class_offering_course` FOREIGN KEY (`course_id`) REFERENCES `rgr_courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_class_offering_room` FOREIGN KEY (`room_id`) REFERENCES `rgr_rooms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_class_offering_semester` FOREIGN KEY (`semester_id`) REFERENCES `rgr_semesters` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_class_offering_strand` FOREIGN KEY (`strand_id`) REFERENCES `rgr_strands` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_class_offering_subject` FOREIGN KEY (`subject_id`) REFERENCES `rgr_subjects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_class_offering_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `rgr_teachers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `rgr_class_offerings` */

/*Table structure for table `rgr_class_schedules` */

DROP TABLE IF EXISTS `rgr_class_schedules`;

CREATE TABLE `rgr_class_schedules` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `class_offering_id` int(10) NOT NULL,
  `day` varchar(250) NOT NULL,
  `start_time` date NOT NULL,
  `end_time` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_schedules` (`class_offering_id`),
  CONSTRAINT `fk_schedules` FOREIGN KEY (`class_offering_id`) REFERENCES `rgr_class_offerings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `rgr_class_schedules` */

/*Table structure for table `rgr_courses` */

DROP TABLE IF EXISTS `rgr_courses`;

CREATE TABLE `rgr_courses` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `code` varchar(250) NOT NULL,
  `name` varchar(250) NOT NULL,
  `years` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `rgr_courses` */

insert  into `rgr_courses`(`id`,`code`,`name`,`years`) values 
(26,'BSIS','Bachelor of Science in Information Systems',4),
(34,'BSHM','Bachelor of Science in Hospitality Management',4),
(35,'BSCrim','Bachelor of Science in Criminology',4),
(38,'BSIT','Bachelor of Science in Information Technology',1);

/*Table structure for table `rgr_curriculum_subjects` */

DROP TABLE IF EXISTS `rgr_curriculum_subjects`;

CREATE TABLE `rgr_curriculum_subjects` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `curriculum_id` int(10) NOT NULL,
  `subject_id` int(10) NOT NULL,
  `year_level` int(10) NOT NULL,
  `semester` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_curriculum_sub_curriculum` (`curriculum_id`),
  KEY `fk_curriculum_sub_subject` (`subject_id`),
  CONSTRAINT `fk_curriculum_sub_curriculum` FOREIGN KEY (`curriculum_id`) REFERENCES `rgr_curriculums` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_curriculum_sub_subject` FOREIGN KEY (`subject_id`) REFERENCES `rgr_subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `rgr_curriculum_subjects` */

/*Table structure for table `rgr_curriculums` */

DROP TABLE IF EXISTS `rgr_curriculums`;

CREATE TABLE `rgr_curriculums` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `type` varchar(250) NOT NULL,
  `strand_id` int(10) DEFAULT NULL,
  `course_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_curriculum_strand` (`strand_id`),
  KEY `fk_curriculum_course` (`course_id`),
  CONSTRAINT `fk_curriculum_course` FOREIGN KEY (`course_id`) REFERENCES `rgr_courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_curriculum_strand` FOREIGN KEY (`strand_id`) REFERENCES `rgr_strands` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `rgr_curriculums` */

/*Table structure for table `rgr_enrollment_subjects` */

DROP TABLE IF EXISTS `rgr_enrollment_subjects`;

CREATE TABLE `rgr_enrollment_subjects` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `enrollment_id` int(10) NOT NULL,
  `subject_id` int(10) NOT NULL,
  `teacher_id` int(10) NOT NULL,
  `schedule` varchar(250) NOT NULL,
  `room` varchar(250) NOT NULL,
  `final_grade` int(10) NOT NULL,
  `created_at` date NOT NULL DEFAULT current_timestamp(),
  `updated_at` date NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_enrollment_sub_teacher` (`teacher_id`),
  KEY `fk_enrollment_sub_enrollments` (`enrollment_id`),
  KEY `fk_enrollment_subject` (`subject_id`),
  CONSTRAINT `fk_enrollment_sub_enrollments` FOREIGN KEY (`enrollment_id`) REFERENCES `rgr_enrollments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_enrollment_sub_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `rgr_teachers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_enrollment_subject` FOREIGN KEY (`subject_id`) REFERENCES `rgr_subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `rgr_enrollment_subjects` */

/*Table structure for table `rgr_enrollments` */

DROP TABLE IF EXISTS `rgr_enrollments`;

CREATE TABLE `rgr_enrollments` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `student_id` int(10) NOT NULL,
  `semester_id` int(10) NOT NULL,
  `year_level` int(10) NOT NULL,
  `strand_id` int(10) DEFAULT NULL,
  `course_id` int(10) DEFAULT NULL,
  `status` varchar(250) NOT NULL,
  `is_locked` int(10) NOT NULL,
  `created_at` date NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_enrollment_course` (`course_id`),
  KEY `fk_enrollment_semester` (`semester_id`),
  KEY `fk_enrollment_student` (`student_id`),
  KEY `fk_enrollment_strand` (`strand_id`),
  CONSTRAINT `fk_enrollment_course` FOREIGN KEY (`course_id`) REFERENCES `rgr_courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_enrollment_semester` FOREIGN KEY (`semester_id`) REFERENCES `rgr_semesters` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_enrollment_strand` FOREIGN KEY (`strand_id`) REFERENCES `rgr_strands` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_enrollment_student` FOREIGN KEY (`student_id`) REFERENCES `rgr_students` (`student_number`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `rgr_enrollments` */

/*Table structure for table `rgr_events` */

DROP TABLE IF EXISTS `rgr_events`;

CREATE TABLE `rgr_events` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `start` datetime NOT NULL,
  `end` datetime DEFAULT NULL,
  `all_day` tinyint(1) NOT NULL DEFAULT 0,
  `background_color` varchar(255) DEFAULT NULL,
  `border_color` varchar(255) DEFAULT NULL,
  `text_color` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `rgr_events` */

insert  into `rgr_events`(`id`,`title`,`description`,`start`,`end`,`all_day`,`background_color`,`border_color`,`text_color`,`created_at`,`updated_at`) values 
(96,'Warning','test','2026-02-17 00:00:00','2026-02-19 00:00:00',0,'#ffc107',NULL,NULL,'2026-02-10 01:45:26','2026-02-10 01:45:26'),
(100,'test','test','2026-02-10 00:00:00','2026-02-12 00:00:00',0,'#dc3545',NULL,NULL,'2026-02-17 11:48:37','2026-02-17 11:48:37'),
(101,'try','try','2026-02-19 00:00:00','2026-02-22 00:00:00',0,'#0d6efd',NULL,NULL,'2026-02-17 11:49:12','2026-02-17 11:49:12'),
(102,'cache','cache','2026-02-20 00:00:00','2026-02-22 00:00:00',0,'#198754',NULL,NULL,'2026-02-17 11:49:30','2026-02-17 11:49:30');

/*Table structure for table `rgr_rooms` */

DROP TABLE IF EXISTS `rgr_rooms`;

CREATE TABLE `rgr_rooms` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `building` varchar(250) DEFAULT NULL,
  `capacity` int(10) DEFAULT NULL,
  `type` varchar(250) NOT NULL DEFAULT 'lecture',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `rgr_rooms` */

/*Table structure for table `rgr_school_years` */

DROP TABLE IF EXISTS `rgr_school_years`;

CREATE TABLE `rgr_school_years` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `rgr_school_years` */

insert  into `rgr_school_years`(`id`,`name`,`is_active`,`created_at`,`updated_at`) values 
(28,'2025-2026',1,'2026-03-05 14:38:03','2026-03-15 12:42:33'),
(29,'2024-2025',0,'2026-03-06 11:38:01','2026-03-06 11:38:01');

/*Table structure for table `rgr_semesters` */

DROP TABLE IF EXISTS `rgr_semesters`;

CREATE TABLE `rgr_semesters` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `school_year_id` int(10) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_semesters_school_year` (`school_year_id`),
  CONSTRAINT `fk_semesters_school_year` FOREIGN KEY (`school_year_id`) REFERENCES `rgr_school_years` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `rgr_semesters` */

insert  into `rgr_semesters`(`id`,`name`,`school_year_id`,`is_active`,`created_at`,`updated_at`) values 
(9,'2nd Semester',28,1,'2026-03-05 14:38:24','2026-03-05 14:47:01'),
(11,'2nd Semester',29,0,'2026-03-07 02:07:39','2026-03-07 02:07:39');

/*Table structure for table `rgr_strands` */

DROP TABLE IF EXISTS `rgr_strands`;

CREATE TABLE `rgr_strands` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `code` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `rgr_strands` */

insert  into `rgr_strands`(`id`,`name`,`code`) values 
(2,'Humanities and Social Sciences','HUMSS');

/*Table structure for table `rgr_students` */

DROP TABLE IF EXISTS `rgr_students`;

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
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `students_student_number_unique` (`student_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `rgr_students` */

insert  into `rgr_students`(`student_number`,`first_name`,`middle_name`,`last_name`,`gender`,`birth_date`,`course`,`year_level`,`section`,`email`,`phone`,`address`,`academic_status`,`graduated_at`,`created_at`,`updated_at`) values 
(508,'Bertram','Zemlak','Stehr','male',NULL,'BSCrim',2004,NULL,NULL,NULL,NULL,'graduated','2025-12-31 19:14:45','2025-09-28 03:30:41','2026-02-02 07:41:03'),
(529,'Zackary','Koelpin','Parisian','male',NULL,'BSCrim',2004,NULL,NULL,NULL,NULL,'inactive',NULL,'2023-07-04 12:54:14','2026-02-02 08:07:43'),
(555,'Emilia','Bailey','Lakin','female',NULL,'BSTM',2004,NULL,NULL,NULL,NULL,'graduated','2024-08-10 01:20:01','2023-06-13 08:24:55','2026-02-02 07:41:03'),
(625,'Maximilian','Rosenbaum','Fritsch','male',NULL,'BSAIS',2004,NULL,NULL,NULL,NULL,'graduated','2023-03-07 11:37:06','2022-10-11 14:44:46','2026-02-02 09:10:42'),
(684,'Cole','Trantow','Heller','male',NULL,'BSTM',2001,NULL,NULL,NULL,NULL,'active',NULL,'2022-09-26 18:29:38','2026-02-02 08:07:43'),
(685,'Danielle','Nitzsche','Sauer','male',NULL,'BSIS',2004,NULL,NULL,NULL,NULL,'graduated','2025-10-18 14:33:39','2025-07-30 16:24:39','2026-02-02 08:07:43'),
(769,'Dallin','Spencer','Heidenreich','female',NULL,'BSAIS',2004,NULL,NULL,NULL,NULL,'graduated','2024-10-30 05:26:46','2024-06-19 10:34:15','2026-02-02 09:10:42'),
(807,'Archibald','Leuschke','Crona','female',NULL,'BSAIS',2002,NULL,NULL,NULL,NULL,'active',NULL,'2023-05-12 01:07:09','2026-02-02 08:07:43'),
(846,'Morris','Stamm','McKenzie','female',NULL,'BSIS',2003,NULL,NULL,NULL,NULL,'inactive',NULL,'2023-08-04 05:59:19','2026-02-02 07:41:03'),
(881,'Lauren','Dooley','Beahan','female',NULL,'BSAIS',2004,NULL,NULL,NULL,NULL,'active',NULL,'2023-08-23 06:35:39','2026-01-31 12:48:20'),
(885,'Kamren','Oberbrunner','Langworth','male',NULL,'BSTM',2001,NULL,NULL,NULL,NULL,'inactive',NULL,'2024-11-21 23:21:09','2026-02-02 08:07:43'),
(1070,'Anna','Anderson','Kautzer','female',NULL,'BSIS',2004,NULL,NULL,NULL,NULL,'inactive',NULL,'2024-09-11 05:45:47','2026-02-02 07:41:03'),
(1087,'Kailey','Herman','Terry','male',NULL,'BSTM',2003,NULL,NULL,NULL,NULL,'inactive',NULL,'2023-07-12 02:32:24','2026-02-02 08:07:43'),
(1195,'Kameron','Veum','Runolfsson','female',NULL,'BSIS',2002,NULL,NULL,NULL,NULL,'active',NULL,'2023-02-18 11:25:09','2026-02-02 08:07:43'),
(1259,'Jaylan','Green','Kuvalis','female',NULL,'BSAIS',2003,NULL,NULL,NULL,NULL,'inactive',NULL,'2022-02-15 16:31:56','2026-02-02 08:07:43'),
(1286,'Adah','Breitenberg','Braun','female',NULL,'BSTM',2003,NULL,NULL,NULL,NULL,'inactive',NULL,'2022-09-27 07:35:35','2026-02-02 08:07:43'),
(1351,'Caterina','Beer','Effertz','female',NULL,'BSCrim',2001,NULL,NULL,NULL,NULL,'inactive',NULL,'2023-09-22 02:56:07','2026-02-02 08:07:43'),
(1354,'Cheyanne','Cormier','Mitchell','female',NULL,'BSIS',2002,NULL,NULL,NULL,NULL,'active',NULL,'2022-03-12 18:00:39','2026-02-02 07:41:03'),
(1435,'Kian','Hauck','Ritchie','female',NULL,'BSIS',2002,NULL,NULL,NULL,NULL,'active',NULL,'2025-08-30 02:17:41','2026-02-02 07:41:03'),
(1487,'Agustin','Russel','Huel','female',NULL,'BSIS',2003,NULL,NULL,NULL,NULL,'active',NULL,'2023-08-30 11:57:44','2026-02-02 07:41:03'),
(1711,'Justine','Hackett','Rohan','male',NULL,'BSCrim',2004,NULL,NULL,NULL,NULL,'inactive',NULL,'2025-12-18 10:32:19','2026-01-31 12:48:20'),
(1720,'Moriah','Wisozk','Daugherty','male',NULL,'BSAIS',2004,NULL,NULL,NULL,NULL,'graduated','2025-06-04 03:26:16','2025-03-03 14:42:54','2026-02-02 07:41:03'),
(1767,'Kendall','Thiel','Wuckert','male',NULL,'BSTM',2004,NULL,NULL,NULL,NULL,'active',NULL,'2025-01-15 19:20:01','2026-02-02 08:07:43'),
(1923,'Kirstin','D\'Amore','Carter','female',NULL,'BSAIS',2004,NULL,NULL,NULL,NULL,'graduated','2025-11-03 13:52:48','2023-07-24 22:47:02','2026-02-02 07:41:03'),
(2073,'Bernie','Schroeder','Steuber','female',NULL,'BSIS',2004,NULL,NULL,NULL,NULL,'inactive',NULL,'2022-05-20 18:45:52','2026-02-02 08:07:43'),
(2091,'Jana','Emard','Bosco','male',NULL,'BSCrim',2002,NULL,NULL,NULL,NULL,'inactive',NULL,'2023-02-23 22:40:43','2026-02-02 09:10:42'),
(2093,'Erick','West','Cremin','female',NULL,'BSTM',2004,NULL,NULL,NULL,NULL,'active',NULL,'2025-01-24 20:26:10','2026-01-31 12:48:20'),
(2095,'Alysa','Friesen','Langworth','male',NULL,'BSCrim',2001,NULL,NULL,NULL,NULL,'active',NULL,'2023-06-19 19:03:11','2026-02-02 08:07:43'),
(2130,'Marguerite','Wiegand','Swaniawski','female',NULL,'BSAIS',2001,NULL,NULL,NULL,NULL,'inactive',NULL,'2023-07-12 09:04:59','2026-02-02 08:07:43'),
(2183,'Osvaldo','Effertz','Blick','male',NULL,'BSCrim',2004,NULL,NULL,NULL,NULL,'graduated','2025-04-10 06:06:37','2025-01-31 02:38:31','2026-01-31 12:48:20'),
(2191,'Erwin','Zieme','Hessel','female',NULL,'BSAIS',2004,NULL,NULL,NULL,NULL,'graduated','2023-02-28 04:05:05','2022-08-24 04:22:42','2026-01-31 12:48:20'),
(2398,'Ryann','Waters','Rogahn','female',NULL,'BSIS',2002,NULL,NULL,NULL,NULL,'inactive',NULL,'2023-05-13 15:01:21','2026-02-02 08:07:43'),
(2452,'Harold','Anderson','Marvin','male',NULL,'BSTM',2001,NULL,NULL,NULL,NULL,'active',NULL,'2023-01-17 20:30:33','2026-02-02 07:41:03'),
(2459,'Natalie','Streich','Casper','male',NULL,'BSTM',2003,NULL,NULL,NULL,NULL,'inactive',NULL,'2022-08-30 05:48:45','2026-02-02 09:10:42'),
(2460,'Gage','Mosciski','Herzog','female',NULL,'BSAIS',2004,NULL,NULL,NULL,NULL,'graduated','2026-01-18 18:30:57','2025-09-11 08:34:09','2026-02-02 08:07:43'),
(2552,'Reinhold','Schneider','Cole','female',NULL,'BSTM',2002,NULL,NULL,NULL,NULL,'inactive',NULL,'2022-10-13 16:49:35','2026-02-02 07:41:03'),
(2617,'Clemens','Kautzer','Weber','male',NULL,'BSIS',2003,NULL,NULL,NULL,NULL,'active',NULL,'2022-10-24 21:29:09','2026-01-31 12:48:20'),
(2743,'Lexi','Kris','Stracke','female',NULL,'BSTM',2004,NULL,NULL,NULL,NULL,'active',NULL,'2022-12-27 01:49:31','2026-02-02 07:41:03'),
(2798,'America','Brown','Wilderman','male',NULL,'BSAIS',2004,NULL,NULL,NULL,NULL,'graduated','2024-08-06 10:59:52','2022-02-16 12:58:00','2026-02-02 08:07:43'),
(2980,'Damon','Feest','Hermiston','female',NULL,'BSTM',2002,NULL,NULL,NULL,NULL,'active',NULL,'2024-08-25 02:14:54','2026-01-31 12:48:20'),
(3053,'Jaylen','Bruen','Huel','female',NULL,'BSIS',2002,NULL,NULL,NULL,NULL,'active',NULL,'2022-08-07 08:53:03','2026-01-31 12:48:20'),
(3100,'Ansley','Zboncak','Kris','male',NULL,'BSIS',2004,NULL,NULL,NULL,NULL,'graduated','2024-01-04 21:59:13','2023-10-24 16:57:24','2026-02-02 08:07:43'),
(3200,'Maryjane','Jones','Stoltenberg','female',NULL,'BSCrim',2001,NULL,NULL,NULL,NULL,'inactive',NULL,'2022-08-26 20:16:26','2026-01-31 12:48:20'),
(3280,'Demetrius','Crona','Torphy','male',NULL,'BSAIS',2004,NULL,NULL,NULL,NULL,'graduated','2025-03-06 17:40:07','2025-03-02 05:27:02','2026-02-02 07:41:03'),
(3287,'Heather','Gibson','Marks','female',NULL,'BSTM',2003,NULL,NULL,NULL,NULL,'active',NULL,'2023-08-12 00:14:01','2026-02-02 08:07:43'),
(3309,'Greyson','Gerhold','Lockman','female',NULL,'BSAIS',2004,NULL,NULL,NULL,NULL,'inactive',NULL,'2024-11-03 13:34:44','2026-01-31 12:48:20'),
(3458,'Danielle','McLaughlin','Hickle','male',NULL,'BSTM',2004,NULL,NULL,NULL,NULL,'active',NULL,'2023-07-29 21:02:23','2026-01-31 12:48:20'),
(3473,'Aida','Moore','McKenzie','female',NULL,'BSTM',2004,NULL,NULL,NULL,NULL,'graduated','2026-01-13 04:10:15','2025-07-06 10:12:16','2026-02-02 08:07:43'),
(3475,'Crystel','Bruen','Doyle','male',NULL,'BSTM',2003,NULL,NULL,NULL,NULL,'active',NULL,'2023-02-13 11:15:22','2026-01-31 12:48:20'),
(3482,'Akeem','McGlynn','Harvey','male',NULL,'BSTM',2004,NULL,NULL,NULL,NULL,'inactive',NULL,'2022-04-14 11:53:28','2026-02-02 08:07:43'),
(3510,'Tessie','Halvorson','Altenwerth','female',NULL,'BSTM',2004,NULL,NULL,NULL,NULL,'inactive',NULL,'2023-12-14 03:47:03','2026-02-02 07:41:03'),
(3568,'Micheal','Brakus','Emard','female',NULL,'BSAIS',2004,NULL,NULL,NULL,NULL,'graduated','2025-03-13 16:07:23','2022-11-02 17:16:16','2026-02-02 07:41:03'),
(3598,'Kariane','Schuppe','Stracke','male',NULL,'BSCrim',2003,NULL,NULL,NULL,NULL,'active',NULL,'2022-10-09 01:04:33','2026-01-31 12:48:20'),
(3681,'Casper','Klocko','Nitzsche','male',NULL,'BSTM',2002,NULL,NULL,NULL,NULL,'inactive',NULL,'2023-07-07 22:08:02','2026-02-02 08:07:43'),
(3683,'Kayli','Wolf','Rath','male',NULL,'BSTM',2003,NULL,NULL,NULL,NULL,'active',NULL,'2022-06-23 03:44:26','2026-01-31 12:48:20'),
(3749,'Timmothy','Torphy','Ward','male',NULL,'BSCrim',2002,NULL,NULL,NULL,NULL,'inactive',NULL,'2023-04-10 02:40:07','2026-02-02 08:07:43'),
(3835,'Arnulfo','Parisian','Balistreri','male',NULL,'BSIS',2003,NULL,NULL,NULL,NULL,'inactive',NULL,'2025-07-02 13:54:00','2026-01-31 12:48:20'),
(3941,'Chris','Hessel','Ernser','male',NULL,'BSAIS',2004,NULL,NULL,NULL,NULL,'graduated','2024-08-04 02:02:53','2022-12-29 01:25:41','2026-02-02 07:41:03'),
(3999,'Adrain','Legros','Franecki','male',NULL,'BSTM',2004,NULL,NULL,NULL,NULL,'active',NULL,'2025-05-09 01:14:44','2026-01-31 12:48:20'),
(4088,'Jeanie','Daugherty','O\'Conner','male',NULL,'BSIS',2004,NULL,NULL,NULL,NULL,'inactive',NULL,'2024-12-27 11:51:20','2026-01-31 12:48:20'),
(4094,'Shanna','Cole','Cruickshank','male',NULL,'BSCrim',2004,NULL,NULL,NULL,NULL,'graduated','2025-11-05 06:35:12','2023-08-23 03:04:35','2026-01-31 12:48:20'),
(4102,'Cornell','Batz','Zboncak','female',NULL,'BSCrim',2002,NULL,NULL,NULL,NULL,'inactive',NULL,'2025-11-27 13:00:45','2026-02-02 07:41:03'),
(4159,'Adalberto','O\'Hara','Hills','male',NULL,'BSIS',2004,NULL,NULL,NULL,NULL,'graduated','2024-01-07 17:47:49','2023-07-27 08:30:34','2026-01-31 12:48:20'),
(4227,'Leatha','Morissette','Bins','male',NULL,'BSIS',2004,NULL,NULL,NULL,NULL,'graduated','2026-01-31 17:14:35','2025-12-13 11:48:30','2026-02-02 08:07:43'),
(4269,'Wilfred','Cole','Towne','female',NULL,'BSTM',2004,NULL,NULL,NULL,NULL,'active',NULL,'2025-11-27 19:53:29','2026-01-31 12:48:20'),
(4295,'Tito','Jerde','Sanford','male',NULL,'BSAIS',2002,NULL,NULL,NULL,NULL,'active',NULL,'2024-10-19 03:27:27','2026-01-31 12:48:20'),
(4298,'Andreanne','Altenwerth','Price','male',NULL,'BSAIS',2004,NULL,NULL,NULL,NULL,'graduated','2025-11-15 18:45:38','2024-11-30 10:23:39','2026-02-02 07:41:03'),
(4383,'Jessica','Hauck','Prohaska','female',NULL,'BSIS',2002,NULL,NULL,NULL,NULL,'active',NULL,'2023-11-02 01:40:14','2026-01-31 12:48:20'),
(4385,'Darby','Johnson','Mueller','female',NULL,'BSTM',2003,NULL,NULL,NULL,NULL,'active',NULL,'2024-06-13 06:29:13','2026-02-02 08:07:43'),
(4412,'Orin','Lehner','Beatty','male',NULL,'BSTM',2002,NULL,NULL,NULL,NULL,'inactive',NULL,'2024-05-29 07:11:55','2026-02-02 08:07:43'),
(4486,'Noble','Stark','Shanahan','female',NULL,'BSAIS',2001,NULL,NULL,NULL,NULL,'inactive',NULL,'2024-03-29 21:03:58','2026-02-02 08:07:43'),
(4526,'Aiyana','Doyle','Nolan','female',NULL,'BSIS',2003,NULL,NULL,NULL,NULL,'active',NULL,'2022-11-24 20:06:34','2026-02-02 07:41:03'),
(4602,'Karlie','Torp','Deckow','female',NULL,'BSAIS',2004,NULL,NULL,NULL,NULL,'graduated','2025-06-23 18:11:17','2024-11-05 08:25:31','2026-02-02 08:07:43'),
(4648,'General','Erdman','Funk','male',NULL,'BSTM',2004,NULL,NULL,NULL,NULL,'graduated','2024-06-25 12:52:49','2023-10-15 12:48:33','2026-02-02 08:07:43'),
(4668,'Theron','Durgan','Kunze','female',NULL,'BSTM',2001,NULL,NULL,NULL,NULL,'active',NULL,'2024-09-05 04:11:35','2026-02-02 07:41:03'),
(4792,'Dangelo','Prohaska','Balistreri','male',NULL,'BSTM',2002,NULL,NULL,NULL,NULL,'active',NULL,'2024-03-23 13:49:28','2026-02-02 09:10:42'),
(4799,'Hadley','Herman','Emard','female',NULL,'BSCrim',2002,NULL,NULL,NULL,NULL,'active',NULL,'2024-03-13 23:45:54','2026-02-02 08:07:43'),
(4811,'Hermina','Cartwright','Kshlerin','male',NULL,'BSCrim',2004,NULL,NULL,NULL,NULL,'graduated','2026-01-17 01:44:35','2022-08-19 05:54:34','2026-02-02 08:07:43'),
(4866,'Bart','Little','Hessel','male',NULL,'BSCrim',2003,NULL,NULL,NULL,NULL,'inactive',NULL,'2025-11-26 03:56:25','2026-02-02 07:41:03'),
(4950,'Keenan','Douglas','Wilderman','male',NULL,'BSTM',2004,NULL,NULL,NULL,NULL,'graduated','2025-08-31 20:44:39','2023-06-25 01:50:53','2026-02-02 07:41:03'),
(4952,'Pamela','Ortiz','Predovic','female',NULL,'BSCrim',2004,NULL,NULL,NULL,NULL,'active',NULL,'2022-03-16 13:38:24','2026-02-02 08:07:43'),
(5102,'Wallace','Gerhold','Senger','female',NULL,'BSAIS',2002,NULL,NULL,NULL,NULL,'active',NULL,'2023-01-03 04:20:44','2026-02-02 07:41:03'),
(5125,'Herman','Batz','Russel','female',NULL,'BSIS',2003,NULL,NULL,NULL,NULL,'inactive',NULL,'2025-04-26 02:08:26','2026-02-02 08:07:43'),
(5204,'Malvina','Stanton','Rodriguez','female',NULL,'BSIS',2004,NULL,NULL,NULL,NULL,'graduated','2024-03-19 14:45:53','2024-03-13 16:02:56','2026-01-31 12:48:20'),
(5228,'Kaelyn','Maggio','Murazik','male',NULL,'BSIS',2004,NULL,NULL,NULL,NULL,'graduated','2025-09-18 01:45:57','2025-06-18 17:53:37','2026-02-02 08:07:43'),
(5301,'Brent','Schmitt','Pouros','male',NULL,'BSTM',2004,NULL,NULL,NULL,NULL,'graduated','2025-02-09 23:32:03','2024-10-21 02:45:43','2026-02-02 07:41:03'),
(5338,'Tyra','Mertz','Muller','female',NULL,'BSIS',2003,NULL,NULL,NULL,NULL,'inactive',NULL,'2025-03-28 13:17:38','2026-02-02 08:07:43'),
(5339,'Edythe','Altenwerth','Robel','male',NULL,'BSTM',2004,NULL,NULL,NULL,NULL,'graduated','2023-12-22 07:46:30','2022-12-08 16:29:09','2026-01-31 12:48:20'),
(5341,'Susie','Rodriguez','Friesen','female',NULL,'BSAIS',2004,NULL,NULL,NULL,NULL,'graduated','2025-08-19 20:52:56','2023-12-04 07:04:24','2026-01-31 12:48:20'),
(5363,'Uriah','Fisher','Beier','female',NULL,'BSIS',2002,NULL,NULL,NULL,NULL,'inactive',NULL,'2024-04-26 07:22:31','2026-01-31 12:48:20'),
(5437,'Eliza','Fisher','Wisoky','male',NULL,'BSTM',2004,NULL,NULL,NULL,NULL,'active',NULL,'2023-09-22 22:38:08','2026-02-02 09:10:42'),
(5455,'Bridie','Heidenreich','Huel','male',NULL,'BSCrim',2004,NULL,NULL,NULL,NULL,'graduated','2025-03-02 17:24:04','2024-09-10 02:33:23','2026-02-02 07:41:03'),
(5606,'Erling','Ortiz','Flatley','female',NULL,'BSTM',2004,NULL,NULL,NULL,NULL,'graduated','2026-01-28 05:07:04','2022-06-22 07:40:43','2026-02-02 07:41:03'),
(5651,'Tommie','Kohler','Mann','female',NULL,'BSAIS',2002,NULL,NULL,NULL,NULL,'active',NULL,'2024-03-25 22:07:39','2026-01-31 12:48:20'),
(5683,'Cecil','Kris','Davis','female',NULL,'BSIS',2004,NULL,NULL,NULL,NULL,'active',NULL,'2023-10-12 03:06:58','2026-02-02 08:07:43'),
(5770,'Elta','Rolfson','Turcotte','female',NULL,'BSIS',2004,NULL,NULL,NULL,NULL,'graduated','2025-12-10 11:09:14','2025-11-02 09:03:37','2026-02-02 08:07:43'),
(5772,'Jude','Zieme','Leannon','female',NULL,'BSIS',2004,NULL,NULL,NULL,NULL,'graduated','2026-01-05 10:09:18','2025-10-03 17:50:21','2026-01-31 12:48:20'),
(5784,'Rod','Gislason','Kautzer','female',NULL,'BSAIS',2004,NULL,NULL,NULL,NULL,'graduated','2026-01-15 22:51:04','2022-05-07 00:13:07','2026-02-02 07:41:03'),
(5800,'Devin','Emmerich','Mohr','female',NULL,'BSIS',2004,NULL,NULL,NULL,NULL,'active',NULL,'2025-07-27 18:15:35','2026-02-02 07:41:03'),
(5816,'Branson','Jenkins','Bergstrom','male',NULL,'BSAIS',2004,NULL,NULL,NULL,NULL,'inactive',NULL,'2023-08-29 01:14:12','2026-02-02 08:07:43'),
(5819,'Lane','Grant','Stamm','female',NULL,'BSTM',2001,NULL,NULL,NULL,NULL,'active',NULL,'2023-03-01 01:54:28','2026-02-02 07:41:03'),
(5860,'Madelyn','Roob','Kiehn','female',NULL,'BSCrim',2002,NULL,NULL,NULL,NULL,'active',NULL,'2025-09-16 23:01:47','2026-02-02 08:07:43'),
(5865,'Rae','Bergstrom','Zemlak','male',NULL,'BSAIS',2004,NULL,NULL,NULL,NULL,'graduated','2024-10-03 17:14:58','2023-04-24 19:24:30','2026-01-31 12:48:20'),
(5935,'Earline','Paucek','Veum','female',NULL,'BSAIS',2004,NULL,NULL,NULL,NULL,'graduated','2025-06-25 20:36:03','2025-05-21 17:31:01','2026-02-02 07:41:03'),
(6062,'Kyleigh','Hegmann','Harber','male',NULL,'BSAIS',2004,NULL,NULL,NULL,NULL,'graduated','2026-01-09 04:09:38','2024-10-10 16:42:40','2026-01-31 12:48:20'),
(6125,'Golda','Carroll','Wehner','male',NULL,'BSIS',2003,NULL,NULL,NULL,NULL,'inactive',NULL,'2023-09-14 09:03:53','2026-02-02 07:41:03'),
(6233,'Columbus','Emmerich','Hill','male',NULL,'BSCrim',2004,NULL,NULL,NULL,NULL,'graduated','2025-02-12 21:26:28','2023-10-02 16:51:20','2026-02-02 07:41:03'),
(6241,'Yvonne','Hoeger','Ankunding','female',NULL,'BSAIS',2002,NULL,NULL,NULL,NULL,'active',NULL,'2024-04-01 14:46:53','2026-02-02 08:07:43'),
(6252,'Sherman','Littel','Bergstrom','male',NULL,'BSAIS',2004,NULL,NULL,NULL,NULL,'graduated','2024-01-15 08:06:19','2023-12-23 15:24:25','2026-02-02 07:41:03'),
(6323,'Naomie','Roob','O\'Keefe','male',NULL,'BSIS',2002,NULL,NULL,NULL,NULL,'inactive',NULL,'2022-02-10 11:24:37','2026-02-02 07:41:03'),
(6399,'Caterina','Kulas','Roberts','female',NULL,'BSTM',2004,NULL,NULL,NULL,NULL,'active',NULL,'2025-10-14 08:45:12','2026-02-02 08:07:43'),
(6411,'Spencer','Vandervort','Sporer','female',NULL,'BSCrim',2003,NULL,NULL,NULL,NULL,'active',NULL,'2022-10-25 07:37:41','2026-02-02 07:41:03'),
(6413,'Greta','Lemke','Reynolds','male',NULL,'BSCrim',2002,NULL,NULL,NULL,NULL,'inactive',NULL,'2023-08-01 17:42:25','2026-02-02 07:41:03'),
(6547,'Jon','Welch','Tremblay','female',NULL,'BSCrim',2004,NULL,NULL,NULL,NULL,'graduated','2025-10-26 01:35:17','2022-11-23 23:53:42','2026-01-31 12:48:20'),
(6550,'Constantin','Fritsch','Hickle','male',NULL,'BSIS',2004,NULL,NULL,NULL,NULL,'active',NULL,'2023-12-16 02:12:23','2026-02-02 07:41:03'),
(6659,'Justice','Swaniawski','Rowe','male',NULL,'BSIS',2001,NULL,NULL,NULL,NULL,'inactive',NULL,'2024-10-14 18:32:28','2026-02-02 08:07:43'),
(6699,'Fernando','Torphy','Labadie','male',NULL,'BSTM',2004,NULL,NULL,NULL,NULL,'graduated','2025-04-30 08:28:24','2023-12-12 00:24:45','2026-01-31 12:48:20'),
(7026,'Madyson','Lang','Dietrich','female',NULL,'BSTM',2003,NULL,NULL,NULL,NULL,'inactive',NULL,'2025-02-15 01:18:05','2026-01-31 12:48:20'),
(7063,'Brooke','Weissnat','Runte','female',NULL,'BSCrim',2004,NULL,NULL,NULL,NULL,'graduated','2023-05-26 00:47:52','2023-02-26 01:07:45','2026-02-02 08:07:43'),
(7121,'Leonor','Metz','Johnston','female',NULL,'BSAIS',2001,NULL,NULL,NULL,NULL,'inactive',NULL,'2023-06-22 00:29:15','2026-01-31 12:48:20'),
(7125,'Assunta','Feest','Kerluke','female',NULL,'BSCrim',2004,NULL,NULL,NULL,NULL,'inactive',NULL,'2022-08-14 17:39:30','2026-01-31 12:48:20'),
(7308,'Jacinthe','Doyle','Predovic','male',NULL,'BSIS',2004,NULL,NULL,NULL,NULL,'graduated','2023-12-28 03:36:24','2023-10-23 18:11:14','2026-01-31 12:48:20'),
(7437,'Adela','Kemmer','Blick','female',NULL,'BSAIS',2002,NULL,NULL,NULL,NULL,'active',NULL,'2025-02-18 23:25:05','2026-02-02 07:41:03'),
(7563,'Kira','Johnston','Terry','female',NULL,'BSCrim',2004,NULL,NULL,NULL,NULL,'graduated','2025-09-11 15:31:00','2024-06-08 20:25:45','2026-02-02 09:10:42'),
(7589,'Timothy','Gleichner','Torphy','male',NULL,'BSCrim',2004,NULL,NULL,NULL,NULL,'inactive',NULL,'2023-11-01 06:56:09','2026-01-31 12:48:20'),
(7651,'Angelita','Cartwright','Zieme','female',NULL,'BSIS',2001,NULL,NULL,NULL,NULL,'active',NULL,'2022-08-28 05:30:14','2026-02-02 08:07:43'),
(7863,'Adela','Mraz','Trantow','male',NULL,'BSTM',2002,NULL,NULL,NULL,NULL,'inactive',NULL,'2022-06-02 00:25:43','2026-02-02 07:41:03'),
(7917,'Corine','Lind','Johnson','female',NULL,'BSAIS',2003,NULL,NULL,NULL,NULL,'inactive',NULL,'2024-07-15 04:52:24','2026-02-02 07:41:03'),
(8007,'Mac','Torphy','Crooks','female',NULL,'BSIS',2001,NULL,NULL,NULL,NULL,'inactive',NULL,'2023-08-27 12:51:10','2026-01-31 12:48:20'),
(8145,'Derick','Hamill','Erdman','female',NULL,'BSIS',2002,NULL,NULL,NULL,NULL,'active',NULL,'2023-07-15 20:00:52','2026-02-02 08:07:43'),
(8237,'Damian','Price','Dickens','male',NULL,'BSIS',2003,NULL,NULL,NULL,NULL,'inactive',NULL,'2023-08-26 04:25:26','2026-02-02 08:07:43'),
(8389,'Milan','Hartmann','O\'Hara','female',NULL,'BSIS',2003,NULL,NULL,NULL,NULL,'inactive',NULL,'2022-05-18 15:01:57','2026-01-31 12:48:20'),
(8436,'Travis','Reinger','Waelchi','female',NULL,'BSIS',2004,NULL,NULL,NULL,NULL,'graduated','2025-03-20 00:18:18','2024-09-16 00:56:01','2026-02-02 08:07:43'),
(8521,'Cooper','Beer','Olson','male',NULL,'BSIS',2004,NULL,NULL,NULL,NULL,'graduated','2025-06-11 09:39:53','2022-12-27 23:06:28','2026-02-02 08:07:43'),
(8527,'Judah','Dietrich','Murazik','female',NULL,'BSIS',2004,NULL,NULL,NULL,NULL,'graduated','2025-04-04 11:16:11','2022-12-05 18:58:30','2026-02-02 07:41:03'),
(8588,'Tyra','Flatley','Connelly','male',NULL,'BSCrim',2004,NULL,NULL,NULL,NULL,'graduated','2024-02-29 04:03:42','2022-09-01 11:45:50','2026-02-02 09:10:42'),
(8719,'Urban','Friesen','Dare','female',NULL,'BSCrim',2004,NULL,NULL,NULL,NULL,'graduated','2026-01-04 03:40:49','2025-03-20 09:32:41','2026-01-31 12:48:20'),
(8735,'Chaz','Gleason','Gottlieb','female',NULL,'BSAIS',2004,NULL,NULL,NULL,NULL,'active',NULL,'2025-05-09 03:53:01','2026-01-31 12:48:20'),
(8781,'Oran','Beahan','Wolf','male',NULL,'BSCrim',2004,NULL,NULL,NULL,NULL,'graduated','2023-12-12 13:36:05','2023-09-20 16:31:10','2026-02-02 08:07:43'),
(8800,'Ismael','Thiel','Barrows','female',NULL,'BSIS',2004,NULL,NULL,NULL,NULL,'graduated','2024-12-21 06:27:00','2024-06-26 09:55:42','2026-02-02 07:41:03'),
(9004,'Madelyn','Schmidt','McClure','male',NULL,'BSTM',2004,NULL,NULL,NULL,NULL,'graduated','2025-01-16 22:59:28','2025-01-12 08:32:32','2026-01-31 12:48:20'),
(9070,'Vella','Keeling','Windler','female',NULL,'BSTM',2002,NULL,NULL,NULL,NULL,'active',NULL,'2024-07-07 21:19:52','2026-02-02 09:10:42'),
(9119,'Fannie','Upton','Bradtke','male',NULL,'BSCrim',2001,NULL,NULL,NULL,NULL,'inactive',NULL,'2024-11-05 09:48:29','2026-02-02 07:41:03'),
(9128,'Clint','Strosin','O\'Connell','female',NULL,'BSTM',2002,NULL,NULL,NULL,NULL,'active',NULL,'2025-08-20 15:41:43','2026-01-31 12:48:20'),
(9212,'Kirstin','Huels','Schneider','male',NULL,'BSTM',2004,NULL,NULL,NULL,NULL,'graduated','2025-04-13 22:05:41','2023-12-02 14:03:37','2026-02-02 09:10:42'),
(9267,'Golda','Glover','Monahan','male',NULL,'BSCrim',2003,NULL,NULL,NULL,NULL,'active',NULL,'2023-08-16 07:42:47','2026-02-02 07:41:03'),
(9281,'Serena','Rutherford','Berge','female',NULL,'BSCrim',2004,NULL,NULL,NULL,NULL,'inactive',NULL,'2022-11-23 06:15:33','2026-02-02 07:41:03'),
(9303,'Queen','Blanda','Veum','female',NULL,'BSTM',2004,NULL,NULL,NULL,NULL,'graduated','2023-05-26 15:42:45','2023-02-13 17:58:45','2026-02-02 08:07:43'),
(9325,'Meghan','Bernhard','Bergstrom','female',NULL,'BSAIS',2001,NULL,NULL,NULL,NULL,'active',NULL,'2024-02-07 13:26:48','2026-02-02 07:41:03'),
(9519,'Viola','Bergnaum','Heaney','male',NULL,'BSAIS',2001,NULL,NULL,NULL,NULL,'inactive',NULL,'2025-08-17 10:43:26','2026-02-02 07:41:03'),
(9551,'Clemmie','Volkman','O\'Reilly','male',NULL,'BSTM',2001,NULL,NULL,NULL,NULL,'inactive',NULL,'2023-11-11 12:18:30','2026-01-31 12:48:20'),
(9620,'Gaston','Thompson','Will','male',NULL,'BSTM',2001,NULL,NULL,NULL,NULL,'active',NULL,'2025-10-11 13:19:16','2026-01-31 12:48:20'),
(9642,'Frieda','Stehr','Ryan','male',NULL,'BSCrim',2004,NULL,NULL,NULL,NULL,'graduated','2025-08-13 18:05:49','2025-03-21 07:40:14','2026-02-02 09:10:42'),
(9684,'Hellen','Morar','Stiedemann','male',NULL,'BSCrim',2001,NULL,NULL,NULL,NULL,'active',NULL,'2023-04-30 02:58:31','2026-02-02 09:10:42'),
(9796,'Victor','Romaguera','Christiansen','female',NULL,'BSCrim',2004,NULL,NULL,NULL,NULL,'graduated','2025-12-11 06:50:48','2024-03-03 14:06:16','2026-01-31 12:48:20'),
(9822,'Ralph','Rosenbaum','O\'Kon','female',NULL,'BSAIS',2001,NULL,NULL,NULL,NULL,'inactive',NULL,'2024-11-18 11:44:44','2026-02-02 09:10:42'),
(9905,'Keegan','Murazik','Hansen','male',NULL,'BSIS',2004,NULL,NULL,NULL,NULL,'graduated','2025-10-01 09:02:39','2025-08-27 18:15:26','2026-01-31 12:48:20'),
(9916,'Francesca','Kunde','Witting','female',NULL,'BSIS',2004,NULL,NULL,NULL,NULL,'graduated','2024-06-27 10:02:48','2022-10-05 10:44:54','2026-02-02 09:10:42'),
(9931,'Lempi','Rodriguez','Kassulke','male',NULL,'BSAIS',2004,NULL,NULL,NULL,NULL,'graduated','2025-11-01 07:31:23','2025-07-01 06:45:11','2026-02-02 07:41:03'),
(9986,'Macey','Miller','O\'Reilly','female',NULL,'BSAIS',2004,NULL,NULL,NULL,NULL,'graduated','2025-09-09 20:55:40','2023-01-23 20:32:00','2026-02-02 07:41:03');

/*Table structure for table `rgr_subjects` */

DROP TABLE IF EXISTS `rgr_subjects`;

CREATE TABLE `rgr_subjects` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `code` varchar(250) NOT NULL,
  `name` varchar(250) NOT NULL,
  `units` int(11) NOT NULL,
  `lecture_hours` int(10) NOT NULL,
  `lab_hours` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `rgr_subjects` */

insert  into `rgr_subjects`(`id`,`code`,`name`,`units`,`lecture_hours`,`lab_hours`) values 
(5,'ENG1','English for Purposive Communication',2,3,0),
(9,'IS001','Introduction to Information System',3,2,2);

/*Table structure for table `rgr_teachers` */

DROP TABLE IF EXISTS `rgr_teachers`;

CREATE TABLE `rgr_teachers` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `employee_id` int(10) DEFAULT NULL,
  `first_name` varchar(250) NOT NULL,
  `last_name` varchar(250) NOT NULL,
  `email` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `rgr_teachers` */

/*Table structure for table `sd_announcements` */

DROP TABLE IF EXISTS `sd_announcements`;

CREATE TABLE `sd_announcements` (
  `announcement_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `publish_date` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `image_file` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`announcement_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `sd_announcements_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `sms_employee` (`employee_id`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `sd_announcements` */

/*Table structure for table `sd_approvals` */

DROP TABLE IF EXISTS `sd_approvals`;

CREATE TABLE `sd_approvals` (
  `approval_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `submitted_on` datetime DEFAULT NULL,
  `submit_by` bigint(100) DEFAULT NULL,
  `department` int(11) DEFAULT NULL,
  `approver_id` bigint(20) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `decision` enum('Approved','Rejected','Pending') DEFAULT 'Pending',
  `file_path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`approval_id`),
  KEY `approver_id` (`approver_id`),
  KEY `department` (`department`),
  KEY `submit_by` (`submit_by`),
  CONSTRAINT `sd_approvals_ibfk_1` FOREIGN KEY (`approver_id`) REFERENCES `sms_employee` (`employee_id`),
  CONSTRAINT `sd_approvals_ibfk_2` FOREIGN KEY (`department`) REFERENCES `sd_department` (`department_id`),
  CONSTRAINT `sd_approvals_ibfk_3` FOREIGN KEY (`submit_by`) REFERENCES `sms_employee` (`employee_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `sd_approvals` */

insert  into `sd_approvals`(`approval_id`,`title`,`description`,`remarks`,`submitted_on`,`submit_by`,`department`,`approver_id`,`approved_at`,`decision`,`file_path`) values 
(10,'episode 1','pwede po bang manood ng bold','',NULL,1022,NULL,1022,'2026-03-15 12:09:21','Approved','uploads/approvals/approval_69b6251a6e7277.00051729.pdf');

/*Table structure for table `sd_department` */

DROP TABLE IF EXISTS `sd_department`;

CREATE TABLE `sd_department` (
  `department_id` int(100) NOT NULL AUTO_INCREMENT,
  `department_name` varchar(100) DEFAULT NULL,
  `department_head` bigint(100) DEFAULT NULL,
  PRIMARY KEY (`department_id`),
  KEY `department_head` (`department_head`),
  CONSTRAINT `sd_department_ibfk_1` FOREIGN KEY (`department_head`) REFERENCES `sms_employee` (`employee_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `sd_department` */

insert  into `sd_department`(`department_id`,`department_name`,`department_head`) values 
(1,'Office of the Directress',1022),
(2,'Admissions and Enrollment',1014),
(3,'Registrar\'s Office',1003),
(4,'School Clinic',NULL),
(5,'Library',1017),
(6,'Laboratory ',NULL),
(7,'Operations Monitoring',1019),
(8,'Guidance and Counseling Office',1020),
(9,'Academic Affairs',1021);

/*Table structure for table `sd_issues` */

DROP TABLE IF EXISTS `sd_issues`;

CREATE TABLE `sd_issues` (
  `issue_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `department` int(100) NOT NULL,
  `submitted_by` bigint(20) NOT NULL,
  `submitted_on` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('open','resolved') NOT NULL DEFAULT 'open',
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `file_path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`issue_id`),
  KEY `submitted_by` (`submitted_by`),
  KEY `department` (`department`),
  CONSTRAINT `sd_issues_ibfk_1` FOREIGN KEY (`submitted_by`) REFERENCES `sms_employee` (`employee_id`),
  CONSTRAINT `sd_issues_ibfk_2` FOREIGN KEY (`department`) REFERENCES `sd_department` (`department_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `sd_issues` */

insert  into `sd_issues`(`issue_id`,`title`,`description`,`department`,`submitted_by`,`submitted_on`,`status`,`updated_at`,`file_path`) values 
(3,'dasdsd','sdadsdasdsa',2,1022,'2026-03-08 12:30:29','open','2026-03-08 12:31:33',NULL),
(4,'fsdf','asdsdasd',9,1022,'2026-03-08 12:32:20','resolved','2026-03-15 15:42:51','uploads/issues/issue_69acfbd4d342d3.13800182.pdf');

/*Table structure for table `sd_position` */

DROP TABLE IF EXISTS `sd_position`;

CREATE TABLE `sd_position` (
  `position_id` int(10) NOT NULL AUTO_INCREMENT,
  `position_name` varchar(100) DEFAULT NULL,
  `department` int(100) DEFAULT NULL,
  PRIMARY KEY (`position_id`),
  KEY `department` (`department`),
  CONSTRAINT `sd_position_ibfk_1` FOREIGN KEY (`department`) REFERENCES `sd_department` (`department_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `sd_position` */

insert  into `sd_position`(`position_id`,`position_name`,`department`) values 
(1,'School Directress',1),
(2,'Executive Assistant ',1),
(3,'Administrative Officer',1),
(4,'Secretary',1),
(5,'Records Staff',1),
(6,'Admission Officer',2),
(7,'Enrollment Coordinator',2),
(8,'Enrollment Clerk',2),
(9,'Document Verifier',2),
(10,'School Registrar',3),
(11,'Assistant Registrar',3),
(12,'Records Officer',3),
(13,'Transcript Processor',3),
(14,'Scheduling Officer ',3),
(15,'Records Clerk',3),
(16,'School Nurse ',4),
(17,'School Physician ',4),
(18,'Medical Assistant ',4),
(19,'Clinical Aide',4),
(20,'Health Records Staff',4),
(21,'Head Librarian ',5),
(22,'Assistant Librarian',5),
(23,'Library Staff',5),
(24,'Library Aide',5),
(25,'Laboratory Supervisor',6),
(26,'Lab Technician',6),
(27,'Lab Assistant',6),
(28,'Safety Assistant',6),
(29,'Monitoring Officer',7),
(30,'Attendance Officer',7),
(31,'Discipline Officer',7),
(32,'Compliance Staff',7),
(33,'Guidance Counselor',8),
(34,'Counseling Assistant',8),
(35,'Case Manager',8),
(36,'College Coordinator',9),
(37,'Program Chair',9),
(38,'Academic Secretary',9),
(39,'Faculty Coordinatory',9),
(40,'Curriculum Officer ',9);

/*Table structure for table `sd_report_type` */

DROP TABLE IF EXISTS `sd_report_type`;

CREATE TABLE `sd_report_type` (
  `type_id` int(11) NOT NULL AUTO_INCREMENT,
  `report_type` varchar(100) DEFAULT NULL,
  `department_id` int(100) DEFAULT NULL,
  PRIMARY KEY (`type_id`),
  KEY `department_id` (`department_id`),
  CONSTRAINT `sd_report_type_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `sd_department` (`department_id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `sd_report_type` */

insert  into `sd_report_type`(`type_id`,`report_type`,`department_id`) values 
(1,'Enrollment Summary Report',1),
(2,'Financial Summary Report',1),
(3,'Academic Performance Summary',1),
(4,'Attendance Summary Report',1),
(5,'Incident Summary Report',1),
(6,'New Enrollees Report',2),
(7,'Enrollment Status Report',2),
(8,'Section Capacity Report',2),
(9,'Student Master List Report',3),
(10,'Grade Report (Per Term)',3),
(11,'Student Academic History Report',3),
(12,'Graduation Eligibility Report',3),
(13,'Clinic Visit Log Report',4),
(14,'Student Medical Record Summary',4),
(15,'Incident / Injury Report',4),
(16,'Borrowed Books Report',5),
(17,'Overdue Books Report',5),
(18,'Library Inventory Summary Report',5),
(19,'Laboratory Equipment Inventory Report',6),
(20,'Damaged Equipment Report',6),
(21,'Attendance Monitoring Report',7),
(22,'At-Risk Student Report',7),
(23,'Counseling Session Report',8),
(24,'Discipline Case Report',8),
(25,'Course Enrollment Report',9),
(26,'Faculty Load Report',9),
(27,'Graduation Progress Report',9);

/*Table structure for table `sd_reports` */

DROP TABLE IF EXISTS `sd_reports`;

CREATE TABLE `sd_reports` (
  `report_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `submitted_by` bigint(20) DEFAULT NULL,
  `status` enum('Pending','Approved','Returned') DEFAULT 'Pending',
  `submitted_at` datetime DEFAULT current_timestamp(),
  `report_type` int(11) DEFAULT NULL,
  PRIMARY KEY (`report_id`),
  KEY `department_id` (`department_id`),
  KEY `submitted_by` (`submitted_by`),
  KEY `report_type` (`report_type`),
  CONSTRAINT `sd_reports_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `sd_department` (`department_id`),
  CONSTRAINT `sd_reports_ibfk_2` FOREIGN KEY (`submitted_by`) REFERENCES `sms_employee` (`employee_id`),
  CONSTRAINT `sd_reports_ibfk_3` FOREIGN KEY (`report_type`) REFERENCES `sd_report_type` (`type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `sd_reports` */

/*Table structure for table `sd_roles` */

DROP TABLE IF EXISTS `sd_roles`;

CREATE TABLE `sd_roles` (
  `role_id` int(10) NOT NULL AUTO_INCREMENT,
  `role_name` char(100) DEFAULT NULL,
  `department` int(100) DEFAULT NULL,
  PRIMARY KEY (`role_id`),
  KEY `department` (`department`),
  CONSTRAINT `sd_roles_ibfk_1` FOREIGN KEY (`department`) REFERENCES `sd_department` (`department_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `sd_roles` */

insert  into `sd_roles`(`role_id`,`role_name`,`department`) values 
(1,'School Directress',1),
(2,'Enrollment Officer',2),
(3,'Registrar',3),
(4,'Clinic Staff',4),
(5,'Librarian',5),
(6,'Laboratory Staff',6),
(7,'Monitoring Officer',7),
(8,'Guidance Counselor',8),
(9,'College Coordinator',9);

/*Table structure for table `sms_employee` */

DROP TABLE IF EXISTS `sms_employee`;

CREATE TABLE `sms_employee` (
  `employee_id` bigint(100) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) DEFAULT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `role` int(100) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT NULL,
  `date_hired` date DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `department` int(100) DEFAULT NULL,
  `position` int(100) DEFAULT NULL,
  PRIMARY KEY (`employee_id`),
  KEY `demo_employee_ibfk_1` (`user_id`),
  KEY `demo_employee_ibfk_2` (`role`),
  KEY `department` (`department`),
  KEY `position` (`position`),
  CONSTRAINT `sms_employee_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_account` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `sms_employee_ibfk_2` FOREIGN KEY (`role`) REFERENCES `sd_roles` (`role_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `sms_employee_ibfk_3` FOREIGN KEY (`department`) REFERENCES `sd_department` (`department_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `sms_employee_ibfk_4` FOREIGN KEY (`position`) REFERENCES `sd_position` (`position_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1025 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `sms_employee` */

insert  into `sms_employee`(`employee_id`,`first_name`,`middle_name`,`last_name`,`role`,`status`,`date_hired`,`user_id`,`department`,`position`) values 
(1003,'Rhea','Fransisco','Villones',3,'active','2026-02-15',9,3,11),
(1014,'Juan','Cruz','Dela Cruz',2,'active','2021-02-10',NULL,2,6),
(1015,'Ana','Mendez','Lopez',3,'active','2019-08-20',NULL,3,11),
(1016,'Carlos','Riyad','Garcia',7,'active','2022-01-05',NULL,7,29),
(1017,'Liza','Pelo','Torres',5,'active','2020-11-12',NULL,5,21),
(1018,'Ramon','Jeno','Santos',6,'active','2021-09-18',NULL,6,6),
(1019,'Nina','Fila','Velasco',7,'active','2022-03-22',NULL,7,7),
(1020,'Pedro','Layag','Mendoza',8,'active','2019-07-30',NULL,8,33),
(1021,'Catherine','Abantas','Reyes',9,'active','2021-05-14',NULL,9,9),
(1022,'Andrie','Sta Maria','Elbambuena',1,'active','2026-03-01',2,1,2),
(1023,'Rico','Dilam','Mandreza',8,'active','2026-03-12',NULL,8,34),
(1024,'Gieca','Greeley','Banta',4,NULL,NULL,NULL,4,19);

/*Table structure for table `user_account` */

DROP TABLE IF EXISTS `user_account`;

CREATE TABLE `user_account` (
  `user_id` int(100) NOT NULL AUTO_INCREMENT,
  `password` char(100) DEFAULT NULL,
  `employee_id` bigint(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `remember_token` int(1) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `employee_id` (`employee_id`),
  CONSTRAINT `user_account_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `sms_employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `user_account` */

insert  into `user_account`(`user_id`,`password`,`employee_id`,`email`,`created_at`,`remember_token`) values 
(2,'$2y$10$4jl2N3.qV0y2mlpnZOycUOA8wmJTW7sQst5UTw0DOx4Ei5rkWODSW',1022,NULL,'2026-03-01 17:57:56',NULL),
(9,'$2y$10$KS7sXMmhFZtPBsA/cR8uV.rxgMbVEhtzuDxM.J.M0HKFxERrpnV4S',1003,NULL,'2026-03-15 13:20:35',NULL);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- ============================================================
-- database.sql — BCP School Library Management System
-- Run this in your MySQL/MariaDB server
-- Database: school_library
-- ============================================================

CREATE DATABASE IF NOT EXISTS `school_library`
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;

USE `school_library`;

-- ============================================================
-- BOOKS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS `lbr_books` (
    `id`          INT AUTO_INCREMENT PRIMARY KEY,
    `title`       VARCHAR(255) NOT NULL,
    `author`      VARCHAR(255) NOT NULL,
    `genre`       VARCHAR(100) DEFAULT 'General',
    `year`        YEAR DEFAULT NULL,
    `isbn`        VARCHAR(30)  DEFAULT NULL,
    `description` TEXT         DEFAULT NULL,
    `cover_url`   VARCHAR(500) DEFAULT NULL,
    `cover_local` VARCHAR(255) DEFAULT NULL,
    `status`      ENUM('available','borrowed','lost','damaged') DEFAULT 'available',
    `condition`   ENUM('Excellent','Good','Fair','Poor') DEFAULT 'Good',
    `added_date`  DATE         DEFAULT (CURDATE()),
    INDEX idx_status (status),
    INDEX idx_genre  (genre)
) ENGINE=InnoDB;

-- ============================================================
-- BORROWERS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS `lbr_borrowers` (
    `id`          INT AUTO_INCREMENT PRIMARY KEY,
    `name`        VARCHAR(255) NOT NULL,
    `borrower_id` VARCHAR(50)  NOT NULL UNIQUE,
    `email`       VARCHAR(255) DEFAULT NULL,
    `phone`       VARCHAR(30)  DEFAULT NULL,
    `type`        ENUM('Student','Teacher','Staff') DEFAULT 'Student',
    `grade`       VARCHAR(100) DEFAULT NULL,
    `address`     TEXT         DEFAULT NULL,
    `active`      TINYINT(1)   DEFAULT 1,
    `join_date`   DATE         DEFAULT (CURDATE()),
    INDEX idx_borrower_id (borrower_id),
    INDEX idx_type        (type)
) ENGINE=InnoDB;

-- ============================================================
-- TRANSACTIONS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS `lbr_transactions` (
    `id`          INT AUTO_INCREMENT PRIMARY KEY,
    `book_id`     INT NOT NULL,
    `borrower_id` INT NOT NULL,
    `borrow_date` DATE NOT NULL,
    `due_date`    DATE NOT NULL,
    `return_date` DATE DEFAULT NULL,
    `status`      ENUM('active','returned','overdue') DEFAULT 'active',
    `condition`   VARCHAR(50)    DEFAULT 'Good',
    `fine`        DECIMAL(10,2)  DEFAULT 0.00,
    `notes`       TEXT           DEFAULT NULL,
    `created_at`  TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (book_id)     REFERENCES lbr_books(id)     ON DELETE CASCADE,
    FOREIGN KEY (borrower_id) REFERENCES lbr_borrowers(id) ON DELETE CASCADE,
    INDEX idx_status     (status),
    INDEX idx_borrower   (borrower_id),
    INDEX idx_book       (book_id),
    INDEX idx_due_date   (due_date)
) ENGINE=InnoDB;

-- ============================================================
-- SETTINGS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS `lbr_settings` (
    `id`            INT AUTO_INCREMENT PRIMARY KEY,
    `setting_key`   VARCHAR(100) NOT NULL UNIQUE,
    `setting_value` TEXT         DEFAULT NULL
) ENGINE=InnoDB;

-- ============================================================
-- ACTIVITY LOG TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS `lbr_activity_log` (
    `id`         INT AUTO_INCREMENT PRIMARY KEY,
    `action`     VARCHAR(100) NOT NULL,
    `details`    TEXT         DEFAULT NULL,
    `user`       VARCHAR(100) DEFAULT 'Librarian',
    `created_at` TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB;

-- ============================================================
-- AI FEATURE TABLES
-- ============================================================
CREATE TABLE IF NOT EXISTS `lbr_borrow_history` (
    `id`           INT AUTO_INCREMENT PRIMARY KEY,
    `borrower_id`  INT NOT NULL,
    `book_id`      INT NOT NULL,
    `genre`        VARCHAR(100) DEFAULT NULL,
    `borrow_date`  DATE NOT NULL,
    `due_date`     DATE NOT NULL,
    `return_date`  DATE DEFAULT NULL,
    `days_late`    INT  DEFAULT 0,
    `was_late`     TINYINT(1) DEFAULT 0,
    `fine_charged` DECIMAL(10,2) DEFAULT 0.00,
    `created_at`   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (borrower_id) REFERENCES lbr_borrowers(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id)     REFERENCES lbr_books(id)     ON DELETE CASCADE,
    INDEX idx_borrower (borrower_id),
    INDEX idx_book     (book_id),
    INDEX idx_genre    (genre),
    INDEX idx_was_late (was_late)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `lbr_reminders` (
    `id`              INT AUTO_INCREMENT PRIMARY KEY,
    `transaction_id`  INT NOT NULL,
    `borrower_id`     INT NOT NULL,
    `reminder_type`   ENUM('sms','email','both') DEFAULT 'email',
    `reminder_stage`  ENUM('3_day','1_day','overdue','final') DEFAULT '3_day',
    `risk_score`      DECIMAL(5,2) DEFAULT 0.00,
    `sent_at`         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `status`          ENUM('sent','failed','pending') DEFAULT 'pending',
    `message_preview` TEXT DEFAULT NULL,
    FOREIGN KEY (transaction_id) REFERENCES lbr_transactions(id) ON DELETE CASCADE,
    FOREIGN KEY (borrower_id)    REFERENCES lbr_borrowers(id)    ON DELETE CASCADE,
    INDEX idx_transaction (transaction_id),
    INDEX idx_borrower    (borrower_id),
    INDEX idx_sent_at     (sent_at)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `lbr_book_similarity` (
    `id`               INT AUTO_INCREMENT PRIMARY KEY,
    `book_id_a`        INT NOT NULL,
    `book_id_b`        INT NOT NULL,
    `similarity_score` DECIMAL(5,4) DEFAULT 0.0000,
    `computed_at`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (book_id_a) REFERENCES lbr_books(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id_b) REFERENCES lbr_books(id) ON DELETE CASCADE,
    UNIQUE INDEX idx_pair (book_id_a, book_id_b),
    INDEX idx_score (similarity_score)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `lbr_recommendations` (
    `id`                  INT AUTO_INCREMENT PRIMARY KEY,
    `borrower_id`         INT NOT NULL,
    `book_id`             INT NOT NULL,
    `recommendation_type` ENUM('genre_based','collaborative','trending','new_arrival') DEFAULT 'genre_based',
    `score`               DECIMAL(5,4) DEFAULT 0.0000,
    `was_borrowed`        TINYINT(1) DEFAULT 0,
    `shown_at`            TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (borrower_id) REFERENCES lbr_borrowers(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id)     REFERENCES lbr_books(id)     ON DELETE CASCADE,
    INDEX idx_borrower (borrower_id),
    INDEX idx_book     (book_id)
) ENGINE=InnoDB;

-- ============================================================
-- DEFAULT SETTINGS
-- ============================================================
INSERT INTO `lbr_settings` (`setting_key`, `setting_value`) VALUES
    ('library_name',          'BCP School Library'),
    ('max_borrow_days',       '14'),
    ('max_books_per_member',  '3'),
    ('daily_fine_rate',       '0.50'),
    ('auto_save',             'true'),
    ('ai_reminder_enabled',   'true'),
    ('ai_reminder_threshold', '60'),
    ('ai_sms_enabled',        'false'),
    ('ai_email_enabled',      'true'),
    ('ai_recommendations_count', '5'),
    ('ai_rec_min_similarity', '0.3')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- ============================================================
-- SAMPLE DATA
-- ============================================================
INSERT INTO `lbr_books` (`title`, `author`, `genre`, `year`, `isbn`, `description`, `status`) VALUES
('The Great Gatsby',       'F. Scott Fitzgerald', 'Fiction',     1925, '9780743273565', 'A story of the fabulously wealthy Jay Gatsby.',      'available'),
('To Kill a Mockingbird',  'Harper Lee',          'Fiction',     1960, '9780061935466', 'A gripping tale of racial injustice.',                'available'),
('1984',                   'George Orwell',       'Science Fiction', 1949, '9780451524935', 'A dystopian social science fiction novel.',       'available'),
('Pride and Prejudice',    'Jane Austen',         'Romance',     1813, '9780141439518', 'A romantic novel of manners.',                        'available'),
('The Alchemist',          'Paulo Coelho',        'Fiction',     1988, '9780062315007', 'A philosophical novel about following your dreams.',  'available'),
('Harry Potter and the Sorcerer''s Stone', 'J.K. Rowling', 'Fantasy', 1997, '9780439708180', 'A young wizard discovers his magical heritage.', 'available'),
('The Diary of a Young Girl', 'Anne Frank',       'Biography',   1947, '9780553577129', 'The diary of a young Jewish girl during WWII.',      'available'),
('A Brief History of Time', 'Stephen Hawking',   'Science',     1988, '9780553380163', 'An introduction to cosmology.',                       'available')
;

INSERT INTO `lbr_borrowers` (`name`, `borrower_id`, `email`, `type`, `grade`) VALUES
('Juan dela Cruz',    'STU-001', 'juan@bcp.edu.ph',    'Student', 'Grade 11 - STEM'),
('Maria Santos',      'STU-002', 'maria@bcp.edu.ph',   'Student', 'Grade 12 - ABM'),
('Prof. Jose Reyes',  'TCH-001', 'jreyes@bcp.edu.ph',  'Teacher', 'Science Department'),
('Ana Gonzales',      'STU-003', 'ana@bcp.edu.ph',     'Student', 'Grade 10'),
('Roberto Lim',       'STF-001', 'rlim@bcp.edu.ph',    'Staff',   'Admin Office')
;

INSERT INTO `lbr_activity_log` (`action`, `details`, `user`) VALUES
('System Started',   'BCP Library Management System initialized', 'System'),
('Books Added',      '8 sample books added to the catalog',       'Admin'),
('Members Added',    '5 sample members registered',               'Admin')
;

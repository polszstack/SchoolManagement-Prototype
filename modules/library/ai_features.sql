-- ============================================================
-- ai_features.sql — Add AI tables to the existing sms database
-- Run this AFTER importing sms_database.sql
-- ============================================================

USE `sms`;

CREATE TABLE IF NOT EXISTS `lbr_borrow_history` (
    `id`           INT AUTO_INCREMENT PRIMARY KEY,
    `borrower_id`  INT NOT NULL,
    `book_id`      INT NOT NULL,
    `genre`        VARCHAR(100) DEFAULT NULL,
    `borrow_date`  DATE NOT NULL,
    `due_date`     DATE NOT NULL,
    `return_date`  DATE DEFAULT NULL,
    `days_late`    INT DEFAULT 0,
    `was_late`     TINYINT(1) DEFAULT 0,
    `fine_charged` DECIMAL(10,2) DEFAULT 0.00,
    `created_at`   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (borrower_id) REFERENCES lbr_borrowers(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id)     REFERENCES lbr_books(id)     ON DELETE CASCADE,
    INDEX idx_borrower (borrower_id),
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
    INDEX idx_sent_at     (sent_at)
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
    FOREIGN KEY (book_id)     REFERENCES lbr_books(id)     ON DELETE CASCADE
) ENGINE=InnoDB;

-- Populate borrow_history from existing transactions
INSERT IGNORE INTO lbr_borrow_history
    (borrower_id, book_id, genre, borrow_date, due_date, return_date, days_late, was_late, fine_charged)
SELECT
    t.borrower_id, t.book_id, b.genre,
    t.borrow_date, t.due_date, t.return_date,
    CASE WHEN t.return_date IS NOT NULL THEN DATEDIFF(t.return_date, t.due_date)
         WHEN t.status = 'overdue' THEN DATEDIFF(CURDATE(), t.due_date) ELSE 0 END,
    CASE WHEN t.status = 'overdue' THEN 1 WHEN t.return_date > t.due_date THEN 1 ELSE 0 END,
    t.fine
FROM lbr_transactions t
JOIN lbr_books b ON b.id = t.book_id;

-- AI settings
INSERT INTO lbr_settings (setting_key, setting_value) VALUES
    ('ai_reminder_enabled',    'true'),
    ('ai_reminder_threshold',  '60'),
    ('ai_sms_enabled',         'false'),
    ('ai_email_enabled',       'true'),
    ('ai_recommendations_count','5'),
    ('ai_rec_min_similarity',  '0.3')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

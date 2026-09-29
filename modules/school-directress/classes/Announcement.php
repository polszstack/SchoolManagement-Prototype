<?php

include_once __DIR__ . '/../../../database/db.php';

class Announcement {
    private $conn;
    private $uploadDir = __DIR__ . '/../uploads/announcements/';

    public function __construct($pdo = null) {
        if ($pdo instanceof PDO) {
            $this->conn = $pdo;
        } else {
            $database = new Database();
            $this->conn = $database->getConnection();
        }

        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    public function postAnnouncement($title, $message, $publishDate, $createdBy, $imageFile = null) {
        $imageFilename = null;

        if ($imageFile && $imageFile['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $mimeType     = (new finfo(FILEINFO_MIME_TYPE))->file($imageFile['tmp_name']);

            if (!in_array($mimeType, $allowedTypes, true)) {
                return ['success' => false, 'message' => 'Invalid image type.'];
            }

            if ($imageFile['size'] > 5 * 1024 * 1024) {
                return ['success' => false, 'message' => 'Image must be 5 MB or smaller.'];
            }

            $imageFilename = uniqid('ann_', true) . '.' . strtolower(pathinfo($imageFile['name'], PATHINFO_EXTENSION));
            move_uploaded_file($imageFile['tmp_name'], $this->uploadDir . $imageFilename);
        }

        $sql = "INSERT INTO `sd_announcements` (`title`, `message`, `publish_date`, `created_by`, `image_file`)
                VALUES (:title, :message, :publish_date, :created_by, :image_file)";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':title'        => $title,
            ':message'      => $message,
            ':publish_date' => $publishDate,
            ':created_by'   => $createdBy,
            ':image_file'   => $imageFilename,
        ]);

        return $this->conn->lastInsertId();
    }

    public function getAnnouncement() {
        $sql = "SELECT 
                    a.announcement_id,
                    a.title,
                    a.message,
                    a.publish_date,
                    a.image_file,
                    CONCAT(e.first_name, ' ', e.last_name) AS created_by
                FROM `sd_announcements` a
                LEFT JOIN `sms_employee` e ON a.created_by = e.employee_id
                ORDER BY a.publish_date DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
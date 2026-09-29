<?php
include_once __DIR__ . '/../../../auth/session.php';
include_once __DIR__ . '/../classes/Announcement.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$publishDate = trim($_POST['publish_date'] ?? '');
$createdBy   = $_SESSION['employee_id']   ?? null;

if ($publishDate === '') {
    echo json_encode(['success' => false, 'message' => 'Publish date is required.']);
    exit;
}

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $publishDate)) {
    echo json_encode(['success' => false, 'message' => 'Invalid publish date format.']);
    exit;
}

if (!$createdBy) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Please log in again.']);
    exit;
}

$imageFile = $_FILES['image'] ?? null;

$announcement = new Announcement();
$result       = $announcement->postAnnouncement(null, null, $publishDate, $createdBy, $imageFile);

if (is_array($result) && isset($result['success']) && $result['success'] === false) {
    echo json_encode($result);
    exit;
}

if ($result) {
    echo json_encode([
        'success'         => true,
        'message'         => 'Announcement posted successfully.',
        'announcement_id' => $result,
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to post announcement. Please try again.']);
}
<?php
include_once __DIR__ . '/../../../auth/session.php';

// Set default value for admin_name if not set
$admin_name = $admin_name ?? 'Admin';
$user_role = $user_role ?? 'Administrator';
require_once '../config/database.php';
require_once '../models/Section.php';

header('Content-Type: application/json');

// Check if required parameters are present
if (!isset($_GET['course_id']) || !isset($_GET['year_level'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'Missing required parameters'
    ]);
    exit;
}

$course_id = intval($_GET['course_id']);
$year_level = intval($_GET['year_level']);

try {
    $section = new Section();
    
    // Use the getAvailableSections method from your model
    $sections = $section->getAvailableSections($course_id, $year_level);
    
    // Format the sections data for the frontend
    $formattedSections = [];
    foreach ($sections as $sec) {
        $formattedSections[] = [
            'id' => $sec['id'],
            'section_code' => $sec['section_code'],
            'section_name' => $sec['section_name'],
            'max_students' => $sec['max_students'],
            'current_students' => $sec['current_students'],
            'available_slots' => $sec['available_slots'],
            'academic_year' => $sec['academic_year'],
            'semester' => $sec['semester']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'sections' => $formattedSections,
        'academic_year' => $section->getCurrentAcademicYear(),
        'semester' => $section->getCurrentSemester()
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error loading sections: ' . $e->getMessage()
    ]);
}
<?php
include_once __DIR__ . '/../../../auth/session.php';

// Set default value for admin_name if not set
$admin_name = $admin_name ?? 'Admin';
$user_role = $user_role ?? 'Administrator';
require_once '../config/database.php';
require_once '../models/Student.php';
require_once '../models/Applicant.php';
require_once '../models/Section.php';


$page_title = 'Student Details';
$student = new Student();
$applicant = new Applicant();
$section = new Section();

// Get student ID from URL
$student_id = isset($_GET['id']) ? $_GET['id'] : 0;

// Get student details
$student_details = $student->getById($student_id);

if (!$student_details) {
    header('Location: students.php?error=Student not found');
    exit();
}

// Get applicant details
$applicant_details = $applicant->getById($student_details['applicant_id']);

// Get section details if available
$section_details = null;
if (!empty($student_details['section_id'])) {
    $section_details = $section->getById($student_details['section_id']);
}

include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php include '../includes/sidebar_admin.php'; ?>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>
                    <i class="fas fa-user-graduate"></i> 
                    Student Details: <?php echo htmlspecialchars($student_details['student_number']); ?>
                </h2>
                <div>
                    <a href="edit_student.php?id=<?php echo $student_id; ?>" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="students.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>

            <!-- Student Status Banner -->
            <div class="alert alert-<?php 
                echo $student_details['enrollment_status'] == 'enrolled' ? 'success' : 
                    ($student_details['enrollment_status'] == 'on_leave' ? 'warning' : 
                    ($student_details['enrollment_status'] == 'graduated' ? 'info' : 'danger')); 
            ?> d-flex align-items-center">
                <i class="fas fa-<?php 
                    echo $student_details['enrollment_status'] == 'enrolled' ? 'check-circle' : 
                        ($student_details['enrollment_status'] == 'on_leave' ? 'clock' : 
                        ($student_details['enrollment_status'] == 'graduated' ? 'graduation-cap' : 'times-circle')); 
                ?> fa-2x me-3"></i>
                <div>
                    <strong>Enrollment Status: <?php echo ucfirst(str_replace('_', ' ', $student_details['enrollment_status'])); ?></strong><br>
                    <?php if($student_details['enrollment_status'] == 'enrolled'): ?>
                        Student is currently enrolled and active.
                    <?php elseif($student_details['enrollment_status'] == 'on_leave'): ?>
                        Student is currently on leave.
                    <?php elseif($student_details['enrollment_status'] == 'graduated'): ?>
                        Student has graduated.
                    <?php else: ?>
                        Student has dropped out.
                    <?php endif; ?>
                </div>
            </div>

            <!-- Section Information Card -->
            <?php if($section_details): ?>
            <div class="section-card mb-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="section-code"><?php echo htmlspecialchars($section_details['section_code']); ?></div>
                        <div class="section-name"><?php echo htmlspecialchars($section_details['section_name']); ?></div>
                        <div class="mt-2">
                            <span class="badge bg-light text-dark me-2">
                                <i class="fas fa-calendar"></i> AY <?php echo $section_details['academic_year']; ?>
                            </span>
                            <span class="badge bg-light text-dark me-2">
                                <i class="fas fa-clock"></i> <?php echo $section_details['semester']; ?>
                            </span>
                            <span class="badge bg-light text-dark">
                                <i class="fas fa-users"></i> Slots: <?php echo $section_details['current_students']; ?>/<?php echo $section_details['max_students']; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="alert alert-warning mb-4">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>No Section Assigned</strong> - This student has not been assigned to any section yet.
                <a href="assign_section.php?student_id=<?php echo $student_id; ?>" class="alert-link">Assign Section Now</a>
            </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-id-card"></i> Student Profile</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <th>Student Number:</th>
                                    <td><span class="badge bg-secondary"><?php echo htmlspecialchars($student_details['student_number']); ?></span></td>
                                </tr>
                                <tr>
                                    <th>Full Name:</th>
                                    <td>
                                        <strong>
                                        <?php 
                                        echo htmlspecialchars(
                                            $student_details['first_name'] . ' ' . 
                                            $student_details['middle_name'] . ' ' . 
                                            $student_details['surname'] . ' ' . 
                                            ($student_details['suffix'] ?? '')
                                        ); 
                                        ?>
                                        </strong>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Course:</th>
                                    <td>
                                        <strong><?php echo htmlspecialchars($student_details['course_code']); ?></strong>
                                        <br>
                                        <small><?php echo htmlspecialchars($student_details['course_name']); ?></small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Year Level:</th>
                                    <td>
                                        <?php 
                                        $year_labels = ['', '1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year'];
                                        echo '<span class="badge bg-info">' . ($year_labels[$student_details['year_level']] ?? $student_details['year_level'] . ' Year') . '</span>';
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Section:</th>
                                    <td>
                                        <?php if($section_details): ?>
                                            <span class="badge bg-primary">
                                                <i class="fas fa-users"></i> <?php echo htmlspecialchars($section_details['section_code']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Not Assigned</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo $student_details['enrollment_status'] == 'enrolled' ? 'success' : 
                                                ($student_details['enrollment_status'] == 'on_leave' ? 'warning' : 
                                                ($student_details['enrollment_status'] == 'graduated' ? 'info' : 'danger')); 
                                        ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $student_details['enrollment_status'])); ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Enrolled Date:</th>
                                    <td><?php echo date('F d, Y', strtotime($student_details['enrolled_at'])); ?></td>
                                </tr>
                            </table>

                            <?php if($section_details): ?>
                            <div class="mt-3 p-2 bg-light rounded">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i>
                                    Section Academic Year: <?php echo $section_details['academic_year']; ?> | 
                                    Semester: <?php echo $section_details['semester']; ?>
                                </small>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Quick Actions Card -->
                    <div class="card mt-4">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0"><i class="fas fa-cog"></i> Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <?php if($section_details): ?>
                                <a href="change_section.php?student_id=<?php echo $student_id; ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-exchange-alt"></i> Change Section
                                </a>
                                <?php else: ?>
                                <a href="assign_section.php?student_id=<?php echo $student_id; ?>" class="btn btn-success">
                                    <i class="fas fa-plus-circle"></i> Assign Section
                                </a>
                                <?php endif; ?>
                                <a href="update_student_year.php?id=<?php echo $student_id; ?>" class="btn btn-outline-info">
                                    <i class="fas fa-arrow-up"></i> Update Year Level
                                </a>
                                <a href="student_grades.php?id=<?php echo $student_id; ?>" class="btn btn-outline-warning">
                                    <i class="fas fa-chart-line"></i> View Grades
                                </a>
                                <a href="student_schedule.php?id=<?php echo $student_id; ?>" class="btn btn-outline-success">
                                    <i class="fas fa-calendar-alt"></i> View Schedule
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-8 mb-4">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-user"></i> Personal Information</h5>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <tr>
                                    <th width="200">Email:</th>
                                    <td><?php echo htmlspecialchars($student_details['email']); ?></td>
                                </tr>
                                <tr>
                                    <th>Contact Number:</th>
                                    <td><?php echo htmlspecialchars($student_details['contact_number']); ?></td>
                                </tr>
                                <tr>
                                    <th>Address:</th>
                                    <td><?php echo htmlspecialchars($student_details['address_complete']); ?></td>
                                </tr>
                                <tr>
                                    <th>Date of Birth:</th>
                                    <td><?php echo date('F d, Y', strtotime($applicant_details['date_of_birth'])); ?> (Age: <?php echo $applicant_details['age']; ?>)</td>
                                </tr>
                                <tr>
                                    <th>Place of Birth:</th>
                                    <td><?php echo htmlspecialchars($applicant_details['place_of_birth']); ?></td>
                                </tr>
                                <tr>
                                    <th>Sex:</th>
                                    <td><?php echo htmlspecialchars($applicant_details['sex']); ?></td>
                                </tr>
                                <tr>
                                    <th>Civil Status:</th>
                                    <td><?php echo htmlspecialchars($applicant_details['civil_status']); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Parent Information -->
                    <div class="card mt-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-users"></i> Parent/Guardian Information</h5>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <tr>
                                    <th width="200">Full Name:</th>
                                    <td><?php echo htmlspecialchars($applicant_details['parent_full_name']); ?></td>
                                </tr>
                                <tr>
                                    <th>Contact Number:</th>
                                    <td><?php echo htmlspecialchars($applicant_details['parent_contact']); ?></td>
                                </tr>
                                <tr>
                                    <th>Address:</th>
                                    <td><?php echo htmlspecialchars($applicant_details['parent_address']); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Educational Background -->
                    <div class="card mt-4">
                        <div class="card-header bg-warning">
                            <h5 class="mb-0"><i class="fas fa-graduation-cap"></i> Educational Background</h5>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <tr>
                                    <th width="200">Last School Attended:</th>
                                    <td><?php echo htmlspecialchars($applicant_details['school_last_attended']); ?></td>
                                </tr>
                                <tr>
                                    <th>Year Graduated:</th>
                                    <td><?php echo htmlspecialchars($applicant_details['year_graduated']); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
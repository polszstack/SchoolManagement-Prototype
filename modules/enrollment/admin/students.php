<?php
include_once __DIR__ . '/../../../auth/session.php';

// Set default value for admin_name if not set
$admin_name = $admin_name ?? 'Admin';
$user_role = $user_role ?? 'Administrator';
require_once '../config/database.php';
require_once '../models/Student.php';
require_once '../models/Course.php';

$page_title = 'Manage Students';
$student = new Student();
$course = new Course();

// Get filter parameters
$status = isset($_GET['status']) ? $_GET['status'] : 'enrolled';
$course_id = isset($_GET['course_id']) ? $_GET['course_id'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Get students based on filters
if (!empty($search)) {
    $students = $student->searchStudents($search);
} elseif (!empty($course_id)) {
    $students = $student->getByCourse($course_id);
} else {
    $students = $student->getAll($status);
}

// Get all courses for filter - USING THE CORRECT METHOD NAME
$courses = $course->getAllActive();  // Changed from getAll() to getAllActive()

// Get statistics
$total_enrolled = $student->getTotalStudents();
$total_graduated = $student->getTotalByStatus('graduated');
$total_dropped = $student->getTotalByStatus('dropped');

include '../includes/header.php';
?>

<!-- Rest of your HTML code remains exactly the same -->
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php include '../includes/sidebar_admin.php'; ?>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-users"></i> Student Management</h2>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Total Enrolled</h6>
                                    <h3><?php echo $total_enrolled; ?></h3>
                                </div>
                                <i class="fas fa-user-graduate fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Graduated</h6>
                                    <h3><?php echo $total_graduated; ?></h3>
                                </div>
                                <i class="fas fa-graduation-cap fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-danger">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Dropped</h6>
                                    <h3><?php echo $total_dropped; ?></h3>
                                </div>
                                <i class="fas fa-user-times fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="enrolled" <?php echo $status == 'enrolled' ? 'selected' : ''; ?>>Enrolled</option>
                                <option value="graduated" <?php echo $status == 'graduated' ? 'selected' : ''; ?>>Graduated</option>
                                <option value="dropped" <?php echo $status == 'dropped' ? 'selected' : ''; ?>>Dropped</option>
                                <option value="all" <?php echo $status == 'all' ? 'selected' : ''; ?>>All</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Course</label>
                            <select name="course_id" class="form-select">
                                <option value="">All Courses</option>
                                <?php foreach ($courses as $c): ?>
                                <option value="<?php echo $c['id']; ?>" <?php echo $course_id == $c['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($c['course_code']); ?> - <?php echo htmlspecialchars($c['course_name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Search by name or student number" 
                                   value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Students Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Student List</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Student #</th>
                        <th>Full Name</th>
                        <th>Course</th>
                        <th>Year Level</th>
                        <th>Status</th>
                        <th>Enrolled Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="7" class="text-center">No students found</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($students as $s): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($s['student_number']); ?></strong>
                            </td>
                            <td>
                                <?php 
                                // Build full name safely checking if keys exist
                                $full_name = $s['surname'] . ', ' . $s['first_name'];
                                
                                // Add middle name if it exists and not empty
                                if (isset($s['middle_name']) && !empty($s['middle_name'])) {
                                    $full_name .= ' ' . $s['middle_name'];
                                }
                                
                                // Add suffix if it exists and not empty - FIXED
                                if (isset($s['suffix']) && !empty($s['suffix'])) {
                                    $full_name .= ' ' . $s['suffix'];
                                }
                                
                                echo htmlspecialchars($full_name);
                                ?>
                            </td>
                            <td>
                                <?php 
                                echo htmlspecialchars($s['course_code'] ?? 'N/A'); 
                                ?>
                                <br>
                                <small class="text-muted"><?php echo htmlspecialchars($s['course_name'] ?? ''); ?></small>
                            </td>
                            <td>
                                <?php 
                                $year_labels = ['', '1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year'];
                                $year_level = $s['year_level'] ?? 1;
                                echo $year_labels[$year_level] ?? $year_level . ' Year';
                                ?>
                            </td>
                            <td>
                                <?php
                                $status_class = [
                                    'enrolled' => 'success',
                                    'graduated' => 'primary',
                                    'dropped' => 'danger'
                                ];
                                $enrollment_status = $s['enrollment_status'] ?? 'enrolled';
                                $badge_class = $status_class[$enrollment_status] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?php echo $badge_class; ?>">
                                    <?php echo ucfirst($enrollment_status); ?>
                                </span>
                            </td>
                            <td>
                                <?php 
                                $enrolled_at = isset($s['enrolled_at']) ? date('M d, Y', strtotime($s['enrolled_at'])) : 'N/A';
                                echo $enrolled_at;
                                ?>
                            </td>
                            <td>
                                <a href="view_student.php?id=<?php echo $s['id']; ?>" 
                                   class="btn btn-sm btn-info" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button type="button" 
                                        class="btn btn-sm btn-warning" 
                                        onclick="updateYearLevel(<?php echo $s['id']; ?>, <?php echo $s['year_level'] ?? 1; ?>)"
                                        title="Update Year Level">
                                    <i class="fas fa-arrow-up"></i>
                                </button>
                                <?php if (($s['enrollment_status'] ?? '') == 'enrolled'): ?>
                                <button type="button" 
                                        class="btn btn-sm btn-success" 
                                        onclick="graduateStudent(<?php echo $s['id']; ?>)"
                                        title="Graduate Student">
                                    <i class="fas fa-graduation-cap"></i>
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Update Year Level Modal -->
<div class="modal fade" id="updateYearModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Year Level</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="updateYearForm" method="POST" action="update_student_year.php">
                <div class="modal-body">
                    <input type="hidden" name="student_id" id="student_id">
                    <div class="mb-3">
                        <label class="form-label">Select Year Level</label>
                        <select name="year_level" id="year_level" class="form-select" required>
                            <option value="1">1st Year</option>
                            <option value="2">2nd Year</option>
                            <option value="3">3rd Year</option>
                            <option value="4">4th Year</option>
                            <option value="5">5th Year</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Year Level</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateYearLevel(studentId, currentYear) {
    document.getElementById('student_id').value = studentId;
    document.getElementById('year_level').value = currentYear;
    new bootstrap.Modal(document.getElementById('updateYearModal')).show();
}

function graduateStudent(studentId) {
    if (confirm('Are you sure you want to mark this student as graduated?')) {
        window.location.href = 'graduate_student.php?id=' + studentId;
    }
}
</script>
<style>
    
</style>

<?php include '../includes/footer.php'; ?>
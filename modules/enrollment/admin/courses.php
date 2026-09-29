<?php
include_once __DIR__ . '/../../../auth/session.php';

// Set default value for admin_name if not set
$admin_name = $admin_name ?? 'Admin';
$user_role = $user_role ?? 'Administrator';
require_once '../config/database.php';
require_once '../models/Course.php';

$course = new Course();
$courses = $course->getAllActive();
$page_title = 'Courses';

// Handle add/edit course
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['add_course'])) {
        $data = [
            'course_code' => strtoupper($_POST['course_code']),
            'course_name' => $_POST['course_name'],
            'description' => $_POST['description'],
            'duration_years' => $_POST['duration_years'],
            'total_units' => $_POST['total_units'],
            'is_active' => 1
        ];
        $course->create($data);
        header("Location: courses.php?msg=added");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/styles.css">
    <title><?php echo $pageTitle ?? 'School Management System'; ?></title>
    
    <!-- Add your main script.js file -->
    <script src="../js/script.js" type="module"></script>
    <style>
        .content { margin-left: 250px; padding: 20px; margin-top: 45px; }
    </style>
</head>
<header>
    <div class="hamburger">
      <span></span>
      <span></span>
      <span></span>
    </div>
    <div class="realtime" id="realtimeClock" aria-live="polite">--:-- </div>
</header>
<body>
    <?php include_once '../includes/sidebar_admin.php'; ?>
    
    <div class="content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-book"></i> Courses Management</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                <i class="fas fa-plus"></i> Add New Course
            </button>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php 
                    if($_GET['msg'] == 'added') echo 'Course added successfully!';
                    if($_GET['msg'] == 'updated') echo 'Course updated successfully!';
                    if($_GET['msg'] == 'deleted') echo 'Course deleted successfully!';
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h5>Active Courses</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Course Code</th>
                                <th>Course Name</th>
                                <th>Duration</th>
                                <th>Total Units</th>
                                <th>Students Enrolled</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($courses as $c): ?>
                            <tr>
                                <td><strong><?php echo $c['course_code']; ?></strong></td>
                                <td><?php echo $c['course_name']; ?></td>
                                <td><?php echo $c['duration_years']; ?> years</td>
                                <td><?php echo $c['total_units']; ?> units</td>
                                <td>
                                    <span class="badge bg-success">0 enrolled</span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="editCourse(<?php echo $c['id']; ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteCourse(<?php echo $c['id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Course Modal -->
    <div class="modal fade" id="addCourseModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Course</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Course Code</label>
                                <input type="text" name="course_code" class="form-control" 
                                       placeholder="e.g., BSCS" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Course Name</label>
                                <input type="text" name="course_name" class="form-control" 
                                       placeholder="Bachelor of Science in Computer Science" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Duration (Years)</label>
                                <input type="number" name="duration_years" class="form-control" 
                                       value="4" min="1" max="6" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Total Units</label>
                                <input type="number" name="total_units" class="form-control" 
                                       placeholder="e.g., 142" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_course" class="btn btn-primary">Add Course</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function deleteCourse(id) {
            if(confirm('Are you sure you want to delete this course?')) {
                window.location.href = 'delete_course.php?id=' + id;
            }
        }
        
        function editCourse(id) {
            window.location.href = 'edit_course.php?id=' + id;
        }
    </script>
</body>
</html>
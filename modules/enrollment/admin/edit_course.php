<?php
include_once __DIR__ . '/../../../auth/session.php';

// Set default value for admin_name if not set
$admin_name = $admin_name ?? 'Admin';
$user_role = $user_role ?? 'Administrator';
require_once '../config/database.php';
require_once '../models/Course.php';

$course = new Course();
$id = $_GET['id'] ?? 0;
$course_data = $course->getById($id);

if(!$course_data) {
    header("Location: courses.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [
        'course_code' => strtoupper($_POST['course_code']),
        'course_name' => $_POST['course_name'],
        'description' => $_POST['description'],
        'duration_years' => $_POST['duration_years'],
        'total_units' => $_POST['total_units'],
        'is_active' => $_POST['is_active']
    ];
    
    $course->update($id, $data);
    header("Location: courses.php?msg=updated");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="stylesheet" href="../css/styles.css">

</head>
<body>
    <?php include_once '../includes/sidebar_admin.php'; ?>
    
    <div class="content">
        <h2><i class="fas fa-edit"></i> Edit Course</h2>
        
        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Course Code</label>
                            <input type="text" name="course_code" class="form-control" 
                                   value="<?php echo $course_data['course_code']; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Course Name</label>
                            <input type="text" name="course_name" class="form-control" 
                                   value="<?php echo $course_data['course_name']; ?>" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"><?php echo $course_data['description']; ?></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Duration (Years)</label>
                            <input type="number" name="duration_years" class="form-control" 
                                   value="<?php echo $course_data['duration_years']; ?>" min="1" max="6" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Total Units</label>
                            <input type="number" name="total_units" class="form-control" 
                                   value="<?php echo $course_data['total_units']; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select name="is_active" class="form-select">
                                <option value="1" <?php echo $course_data['is_active'] ? 'selected' : ''; ?>>Active</option>
                                <option value="0" <?php echo !$course_data['is_active'] ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                    <hr>
                    <div class="text-end">
                        <a href="courses.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Course
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
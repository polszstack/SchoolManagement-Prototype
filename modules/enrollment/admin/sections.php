<?php
include_once __DIR__ . '/../../../auth/session.php';

// Set default value for admin_name if not set
$admin_name = $admin_name ?? 'Admin';
$user_role = $user_role ?? 'Administrator';
require_once '../config/database.php';
require_once '../models/Section.php';
require_once '../models/Course.php';

$section = new Section();
$course = new Course();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $result = $section->create($_POST);
                $message = $result ? 'Section added successfully' : 'Error adding section';
                break;
            case 'edit':
                $result = $section->update($_POST['id'], $_POST);
                $message = $result ? 'Section updated successfully' : 'Error updating section';
                break;
            case 'delete':
                $result = $section->delete($_POST['id']);
                $message = $result ? 'Section deleted successfully' : 'Error deleting section';
                break;
        }
    }
}

$sections = $section->getAllActive();
$courses = $course->getAllActive();
$page_title = 'Manage Sections';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Sections - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="stylesheet" href="../css/styles.css">
       <title><?php echo $pageTitle ?? 'School Management System'; ?></title>
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
            <h2><i class="fas fa-users"></i> Manage Sections</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSectionModal">
                <i class="fas fa-plus-circle"></i> Add New Section
            </button>
        </div>

        <?php if (isset($message)): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Section Code</th>
                                <th>Section Name</th>
                                <th>Course</th>
                                <th>Year Level</th>
                                <th>Max Students</th>
                                <th>Current</th>
                                <th>Academic Year</th>
                                <th>Semester</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sections as $sec): ?>
                            <tr>
                                <td><strong><?php echo $sec['section_code']; ?></strong></td>
                                <td><?php echo $sec['section_name']; ?></td>
                                <td><?php echo $sec['course_code']; ?></td>
                                <td><?php echo $sec['year_level']; ?></td>
                                <td><?php echo $sec['max_students']; ?></td>
                                <td>
                                    <span class="badge <?php echo $sec['current_students'] < $sec['max_students'] ? 'bg-success' : 'bg-warning'; ?>">
                                        <?php echo $sec['current_students']; ?> enrolled
                                    </span>
                                </td>
                                <td><?php echo $sec['academic_year']; ?></td>
                                <td><?php echo $sec['semester']; ?></td>
                                <td>
                                    <span class="badge <?php echo $sec['is_active'] ? 'bg-success' : 'bg-secondary'; ?>">
                                        <?php echo $sec['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="editSection(<?php echo htmlspecialchars(json_encode($sec)); ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteSection(<?php echo $sec['id']; ?>, '<?php echo $sec['section_code']; ?>')">
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

    <!-- Add Section Modal -->
    <div class="modal fade" id="addSectionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="fas fa-plus-circle"></i> Add New Section</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Section Code</label>
                            <input type="text" name="section_code" class="form-control" required 
                                   placeholder="e.g., BSCS-1A">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Section Name</label>
                            <input type="text" name="section_name" class="form-control" required 
                                   placeholder="e.g., Bachelor of Science in Computer Science - 1A">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Course</label>
                            <select name="course_id" class="form-select" required>
                                <option value="">-- Select Course --</option>
                                <?php foreach ($courses as $c): ?>
                                    <option value="<?php echo $c['id']; ?>">
                                        <?php echo $c['course_code']; ?> - <?php echo $c['course_name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Year Level</label>
                                    <select name="year_level" class="form-select" required>
                                        <option value="1">1st Year</option>
                                        <option value="2">2nd Year</option>
                                        <option value="3">3rd Year</option>
                                        <option value="4">4th Year</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Max Students</label>
                                    <input type="number" name="max_students" class="form-control" required value="40" min="1">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Academic Year</label>
                                    <select name="academic_year" class="form-select" required>
                                        <?php
                                        $current_year = date('Y');
                                        for ($i = -1; $i <= 2; $i++) {
                                            $start = $current_year + $i;
                                            $end = $start + 1;
                                            $year_range = $start . '-' . $end;
                                            $selected = ($i == 0) ? 'selected' : '';
                                            echo "<option value=\"$year_range\" $selected>$year_range</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Semester</label>
                                    <select name="semester" class="form-select" required>
                                        <option value="1st Semester" <?php echo date('m') >= 6 && date('m') <= 10 ? 'selected' : ''; ?>>1st Semester</option>
                                        <option value="2nd Semester" <?php echo date('m') >= 11 || date('m') <= 3 ? 'selected' : ''; ?>>2nd Semester</option>
                                        <option value="Summer">Summer</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" class="form-check-input" value="1" checked>
                                <label class="form-check-label">Active</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Section</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Section Modal -->
    <div class="modal fade" id="editSectionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Section</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Section Code</label>
                            <input type="text" name="section_code" id="edit_section_code" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Section Name</label>
                            <input type="text" name="section_name" id="edit_section_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Course</label>
                            <select name="course_id" id="edit_course_id" class="form-select" required>
                                <option value="">-- Select Course --</option>
                                <?php foreach ($courses as $c): ?>
                                    <option value="<?php echo $c['id']; ?>">
                                        <?php echo $c['course_code']; ?> - <?php echo $c['course_name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Year Level</label>
                                    <select name="year_level" id="edit_year_level" class="form-select" required>
                                        <option value="1">1st Year</option>
                                        <option value="2">2nd Year</option>
                                        <option value="3">3rd Year</option>
                                        <option value="4">4th Year</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Max Students</label>
                                    <input type="number" name="max_students" id="edit_max_students" class="form-control" required min="1">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Academic Year</label>
                                    <select name="academic_year" id="edit_academic_year" class="form-select" required>
                                        <?php
                                        $current_year = date('Y');
                                        for ($i = -1; $i <= 2; $i++) {
                                            $start = $current_year + $i;
                                            $end = $start + 1;
                                            $year_range = $start . '-' . $end;
                                            echo "<option value=\"$year_range\">$year_range</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Semester</label>
                                    <select name="semester" id="edit_semester" class="form-select" required>
                                        <option value="1st Semester">1st Semester</option>
                                        <option value="2nd Semester">2nd Semester</option>
                                        <option value="Summer">Summer</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" id="edit_is_active" class="form-check-input" value="1">
                                <label class="form-check-label">Active</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Update Section</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Section Modal -->
    <div class="modal fade" id="deleteSectionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="delete_id">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Delete Section</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete section <strong id="delete_section_code"></strong>?</p>
                        <p class="text-danger">This action cannot be undone. Students in this section will need to be reassigned.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete Section</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editSection(section) {
            document.getElementById('edit_id').value = section.id;
            document.getElementById('edit_section_code').value = section.section_code;
            document.getElementById('edit_section_name').value = section.section_name;
            document.getElementById('edit_course_id').value = section.course_id;
            document.getElementById('edit_year_level').value = section.year_level;
            document.getElementById('edit_max_students').value = section.max_students;
            document.getElementById('edit_academic_year').value = section.academic_year;
            document.getElementById('edit_semester').value = section.semester;
            document.getElementById('edit_is_active').checked = section.is_active == 1;
            
            new bootstrap.Modal(document.getElementById('editSectionModal')).show();
        }

        function deleteSection(id, code) {
            document.getElementById('delete_id').value = id;
            document.getElementById('delete_section_code').textContent = code;
            new bootstrap.Modal(document.getElementById('deleteSectionModal')).show();
        }
    </script>
</body>
</html>
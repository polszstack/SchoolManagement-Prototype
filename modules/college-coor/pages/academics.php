<?php
// Academics Module Dashboard
require_once(__DIR__ . '/../classes/ProgramManager.php');
require_once(__DIR__ . '/../classes/SubjectManager.php');
require_once(__DIR__ . '/../classes/SectionManager.php');
require_once(__DIR__ . '/../classes/FacultyManager.php');
require_once(__DIR__ . '/../classes/StudentManager.php');

require_once(__DIR__ . '/../../../database/db.php');

$database = new Database();
$conn = $database->getConnection();

$programManager = new ProgramManager($conn);
$facultyManager = new FacultyManager($conn);




?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academics Module</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/academics-management.css">
</head>
<body>
<div class="container-fluid mt-4">
    <h2 class="mb-4">Academics Module</h2>
    <ul class="nav nav-tabs mb-3" id="academicsTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="programs-tab" data-bs-toggle="tab" data-bs-target="#programs" type="button" role="tab">Programs & Curriculum</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="subjects-tab" data-bs-toggle="tab" data-bs-target="#subjects" type="button" role="tab">Subjects</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="sections-tab" data-bs-toggle="tab" data-bs-target="#sections" type="button" role="tab">Sections</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="faculty-tab" data-bs-toggle="tab" data-bs-target="#faculty" type="button" role="tab">Faculty Load</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="students-tab" data-bs-toggle="tab" data-bs-target="#students" type="button" role="tab">Student Records</button>
        </li>
    </ul>
    <div class="tab-content" id="academicsTabsContent">
        <div class="tab-pane fade show active" id="programs" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>Programs & Curriculum</h4>
                <input type="text" id="programSearch" class="form-control w-25" placeholder="Search programs...">
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="programsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Program Code</th>
                            <th>Program Name</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $programs = $programManager->getAllPrograms();
                        foreach ($programs as $program): ?>
                            <tr>
                                <td><?= htmlspecialchars($program['program_code']) ?></td>
                                <td><?= htmlspecialchars($program['program_name']) ?></td>
                                <td><?= htmlspecialchars($program['description'] ?? 'N/A') ?></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary view-curriculum-btn" data-program-id="<?= $program['program_id'] ?>" data-program-name="<?= htmlspecialchars($program['program_name']) ?>">View Curriculum</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>


        <div class="tab-pane fade" id="subjects" role="tabpanel">

<div class="d-flex justify-content-between align-items-center mb-3 mt-3">
    <h4>Subjects Management</h4>
    <input type="text" id="subjectSearch" class="form-control w-25" placeholder="Search subjects...">
</div>

<div class="table-responsive">
<table class="table table-bordered table-hover">

<thead class="table-light">
<tr>
<th>Subject Code</th>
<th>Subject Name</th>
<th>Units</th>
<th>Description</th>
<th>Actions</th>
</tr>
</thead>

<tbody>

<?php
$subjects = $subjectManager->getAllSubjects();

foreach ($subjects as $subject): ?>

<tr>

<td><?= htmlspecialchars($subject['subject_code']) ?></td>
<td><?= htmlspecialchars($subject['subject_name']) ?></td>
<td><?= htmlspecialchars($subject['units']) ?></td>
<td><?= htmlspecialchars($subject['description']) ?></td>

<td>

<button class="btn btn-sm btn-info"
onclick="viewSubjectDetails(
'<?= htmlspecialchars($subject['subject_code']) ?>',
'<?= htmlspecialchars($subject['subject_name']) ?>',
'<?= htmlspecialchars($subject['units']) ?>',
'<?= htmlspecialchars($subject['description']) ?>'
)">
View
</button>

</td>

</tr>

<?php endforeach; ?>

</tbody>
</table>
</div>
</div>
        
        <div class="tab-pane fade" id="sections" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-bordered table-hover school-table" id="sectionsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Section ID</th>
                            <th>Section Name</th>
                            <th>Grade Level</th>
                            <th>Adviser</th>
                            <th>School Year</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="5" class="text-center">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="tab-pane fade" id="faculty" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-bordered table-hover school-table" id="facultyLoadTable">
                    <thead class="table-light">
                        <tr>
                            <th>Faculty Name</th>
                            <th>Subject Code</th>
                            <th>Subject Name</th>
                            <th>Section</th>
                            <th>Total Units</th>
                            <th>Total Classes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="6" class="text-center">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="tab-pane fade" id="students" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-bordered table-hover school-table" id="studentRecordsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Student ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Contect No</th>
                            <th>Gender</th>
                            <th>Program</th>
                            <th>Grade Level</th>
                            <th>Section</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="9" class="text-center">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>



<!-- Curriculum Modal -->
<div class="modal fade" id="curriculumModal" tabindex="-1" aria-labelledby="curriculumModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="curriculumModalLabel">Curriculum Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="curriculumModalBody">
        <!-- Curriculum details will be loaded here -->
      </div>
    </div>
  </div>
</div>

<!-- Student Details Modal -->
<div class="modal fade" id="studentModal" tabindex="-1" aria-labelledby="studentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="studentModalLabel">Student Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="studentModalBody">
        <!-- Student details will be loaded here -->
      </div>
    </div>
  </div>
</div>

<!-- VIEW SUBJECT MODAL -->
<div class="modal fade" id="viewSubjectModal" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">

<div class="modal-header">
<h5 class="modal-title">Subject Details</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body" id="viewSubjectBody"></div>

</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/javas/academics.js"></script>


</body>
</html>

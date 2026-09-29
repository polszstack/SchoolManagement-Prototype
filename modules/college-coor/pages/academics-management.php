<?php
require_once(__DIR__ . '/../classes/ProgramManager.php');
require_once(__DIR__ . '/../classes/SubjectManager.php');
require_once(__DIR__ . '/../classes/SectionManager.php');
require_once(__DIR__ . '/../classes/FacultyManager.php');
require_once(__DIR__ . '/../classes/StudentManager.php');
require_once(__DIR__ . '/../../../database/db.php');

$database = new Database();
$conn = $database->getConnection();

$programManager = new ProgramManager($conn);
$subjectManager = new SubjectManager($conn);
$sectionManager = new SectionManager($conn);
$facultyManager = new FacultyManager($conn);
$studentManager = new StudentManager($conn);

// Fetch data
$programs = $programManager->getAllPrograms() ?? [];
$subjects = $subjectManager->getAllSubjects() ?? [];
$sectionsStmt = $sectionManager->getAll();
$sections = $sectionsStmt ? $sectionsStmt->fetchAll(PDO::FETCH_ASSOC) : [];
$facultyLoads = $facultyManager->getFacultyLoad() ?? [];
$students = $studentManager->getAllStudents() ?? [];

// Sample statistics
$stats = [
    'programs' => count($programs),
    'subjects' => count($subjects),
    'sections' => count($sections),
    'faculty' => count($facultyLoads),
    'students' => count($students)
];
?>

<style>
    /* Academics Management Styles */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background-color: #f8f9fa;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
    }

    .academics-page {
        max-width: 1400px;
        margin: 0 auto;
        padding: 20px;
    }

    /* Page Header */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid #dee2e6;
    }

    .page-header h1 {
        font-size: 32px;
        font-weight: 700;
        color: #1a202c;
        margin-bottom: 5px;
    }

    .page-header p {
        color: #6c757d;
        font-size: 14px;
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 15px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border-left: 4px solid #0d6efd;
    }

    .stat-card.subjects { border-left-color: #6f42c1; }
    .stat-card.sections { border-left-color: #198754; }
    .stat-card.faculty { border-left-color: #fd7e14; }
    .stat-card.students { border-left-color: #dc3545; }

    .stat-card h6 {
        font-size: 13px;
        color: #6c757d;
        text-transform: uppercase;
        margin-bottom: 8px;
        font-weight: 600;
    }

    .stat-card h3 {
        font-size: 28px;
        font-weight: 700;
        color: #1a202c;
    }

    /* Tabs */
    .academics-tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 30px;
        border-bottom: 2px solid #dee2e6;
        overflow-x: auto;
        padding-bottom: 0;
    }

    .academics-tab {
        background: none;
        border: none;
        padding: 12px 20px;
        font-size: 14px;
        font-weight: 500;
        color: #6c757d;
        cursor: pointer;
        border-bottom: 3px solid transparent;
        transition: all 0.3s ease;
        white-space: nowrap;
    }

    .academics-tab:hover {
        color: #0d6efd;
    }

    .academics-tab.active {
        color: #0d6efd;
        border-bottom-color: #0d6efd;
    }

    /* Panels */
    .academics-panel {
        display: none;
        animation: fadeIn 0.3s ease-in;
    }

    .academics-panel.active {
        display: block;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    /* Search & Filters Section */
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        gap: 15px;
    }

    .section-header h3 {
        font-size: 20px;
        font-weight: 600;
        color: #1a202c;
        margin: 0;
    }

    .search-filter-container {
        display: flex;
        gap: 15px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .search-box {
        flex: 1;
        min-width: 250px;
    }

    .search-box input {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ced4da;
        border-radius: 5px;
        font-size: 14px;
    }

    .search-box input:focus {
        outline: none;
        border-color: #0d6efd;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
    }

    .filter-select {
        padding: 10px 12px;
        border: 1px solid #ced4da;
        border-radius: 5px;
        font-size: 14px;
        background: white;
    }

    .filter-select:focus {
        outline: none;
        border-color: #0d6efd;
    }

    .btn {
        padding: 10px 16px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s;
    }

    .btn-primary {
        background-color: #0d6efd;
        color: white;
    }

    .btn-primary:hover {
        background-color: #0b5ed7;
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #5c636a;
    }

    .btn-sm {
        padding: 6px 10px;
        font-size: 12px;
    }

    .btn-info {
        background-color: #17a2b8;
        color: white;
    }

    .btn-info:hover {
        background-color: #138496;
    }

    .btn-danger {
        background-color: #dc3545;
        color: white;
    }

    .btn-danger:hover {
        background-color: #bb2d3b;
    }

    /* Table */
    .table-card {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .table-responsive {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead {
        background-color: #f8f9fa;
    }

    th {
        padding: 12px;
        text-align: left;
        font-size: 13px;
        font-weight: 600;
        color: #333;
        border-bottom: 2px solid #dee2e6;
    }

    td {
        padding: 12px;
        border-bottom: 1px solid #dee2e6;
        font-size: 14px;
    }

    tbody tr:hover {
        background-color: #f8f9fa;
    }

    .actions {
        display: flex;
        gap: 8px;
        justify-content: flex-start;
    }

    /* Status Badges */
    .badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .badge-success {
        background-color: #d4edda;
        color: #155724;
    }

    .badge-warning {
        background-color: #fff3cd;
        color: #856404;
    }

    .badge-danger {
        background-color: #f8d7da;
        color: #721c24;
    }

    .badge-info {
        background-color: #d1ecf1;
        color: #0c5460;
    }

    /* Modal */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }

    .modal.show {
        display: flex;
    }

    .modal-dialog {
        background: white;
        border-radius: 8px;
        max-width: 600px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    }

    .modal-header {
        padding: 20px;
        border-bottom: 1px solid #dee2e6;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-title {
        font-size: 18px;
        font-weight: 600;
        color: #1a202c;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
        color: #6c757d;
    }

    .modal-close:hover {
        color: #000;
    }

    .modal-body {
        padding: 20px;
    }

    .modal-footer {
        padding: 15px 20px;
        border-top: 1px solid #dee2e6;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    /* Forms */
    .form-group {
        margin-bottom: 15px;
    }

    .form-label {
        display: block;
        margin-bottom: 6px;
        font-size: 14px;
        font-weight: 500;
        color: #333;
    }

    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ced4da;
        border-radius: 5px;
        font-size: 14px;
        font-family: inherit;
    }

    .form-control:focus {
        outline: none;
        border-color: #0d6efd;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
    }

    .form-control.required::after {
        content: ' *';
        color: #dc3545;
    }

    .required { color: #dc3545; }

    /* Empty States */
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #6c757d;
    }

    .empty-state-icon {
        font-size: 48px;
        margin-bottom: 15px;
        opacity: 0.5;
    }

    .empty-state h5 {
        color: #333;
        margin-bottom: 10px;
    }

    /* Utilities */
    .text-muted { color: #6c757d; }
    .text-center { text-align: center; }
    .mb-20 { margin-bottom: 20px; }
    .mt-20 { margin-top: 20px; }

    @media (max-width: 768px) {
        .section-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .search-filter-container {
            flex-direction: column;
        }

        .search-box {
            min-width: 100%;
        }

        .table-responsive {
            font-size: 12px;
        }

        .actions {
            flex-direction: column;
        }

        .btn {
            width: 100%;
        }
    }
</style>

<div class="academics-page">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1><i class="fas fa-graduation-cap"></i> Academics Management</h1>
            <p>Monitor programs, curriculum, subjects, sections, faculty load, and student records</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <h6>Programs</h6>
            <h3><?php echo $stats['programs']; ?></h3>
        </div>
        <div class="stat-card subjects">
            <h6>Subjects</h6>
            <h3><?php echo $stats['subjects']; ?></h3>
        </div>
        <div class="stat-card sections">
            <h6>Sections</h6>
            <h3><?php echo $stats['sections']; ?></h3>
        </div>
        <div class="stat-card faculty">
            <h6>Faculty Load</h6>
            <h3><?php echo $stats['faculty']; ?></h3>
        </div>
        <div class="stat-card students">
            <h6>Students</h6>
            <h3><?php echo $stats['students']; ?></h3>
        </div>
    </div>

    <!-- Tabs -->
    <div class="academics-tabs">
        <button class="academics-tab active" data-tab="programs">
            <i class="fas fa-book"></i> Programs & Curriculum
        </button>
        <button class="academics-tab" data-tab="subjects">
            <i class="fas fa-list"></i> Subjects
        </button>
        <button class="academics-tab" data-tab="sections">
            <i class="fas fa-layer-group"></i> Sections
        </button>
        <button class="academics-tab" data-tab="faculty">
            <i class="fas fa-users"></i> Faculty Load
        </button>
        <button class="academics-tab" data-tab="students">
            <i class="fas fa-user-students"></i> Student Records
        </button>
    </div>

    <!-- TAB: Programs & Curriculum -->
    <div class="academics-panel active" id="tab-programs">
        <div class="section-header">
            <h3>Programs & Curriculum</h3>
            <p class="text-muted" style="margin: 0;"><small>Read-only monitoring</small></p>
        </div>

        <div class="search-filter-container">
            <div class="search-box">
                <input type="text" id="searchPrograms" placeholder="Search programs...">
            </div>
            <select class="filter-select" id="filterProgramStatus">
                <option value="">All Status</option>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
            </select>
        </div>

        <div class="table-card">
            <div class="table-responsive">
                <table id="programsTable">
                    <thead>
                        <tr>
                            <th>Program Code</th>
                            <th>Program Name</th>
                            <th>Curriculum Version</th>
                            <th>Total Units</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($programs)): ?>
                            <?php foreach ($programs as $prog): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($prog['code'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($prog['name'] ?? 'Program'); ?></td>
                                    <td>v2.0 (2024)</td>
                                    <td><?php echo intval($prog['years'] ?? 4) * 30; ?></td>
                                    <td><span class="badge badge-success">Active</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="viewCurriculumDetails('<?php echo $prog['id'] ?? 'N/A'; ?>')">
                                            View Details
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">No programs found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB: Subjects -->
    <div class="academics-panel" id="tab-subjects">
        <div class="section-header">
            <h3>Subject Catalog</h3>
            <p class="text-muted" style="margin: 0;"><small>Mostly read-only monitoring</small></p>
        </div>

        <div class="search-filter-container">
            <div class="search-box">
                <input type="text" id="searchSubjects" placeholder="Search subjects...">
            </div>
            <select class="filter-select" id="filterSubjectYear">
                <option value="">All Year Levels</option>
                <option value="1st">1st Year</option>
                <option value="2nd">2nd Year</option>
                <option value="3rd">3rd Year</option>
                <option value="4th">4th Year</option>
            </select>
        </div>

        <div class="table-card">
            <div class="table-responsive">
                <table id="subjectsTable">
                    <thead>
                        <tr>
                            <th>Subject Code</th>
                            <th>Subject Name</th>
                            <th>Units</th>
                            <th>Year Level</th>
                            <th>Prerequisites</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($subjects)): ?>
                            <?php foreach ($subjects as $subj): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($subj['code'] ?? 'SUBJ101'); ?></td>
                                    <td><?php echo htmlspecialchars($subj['name'] ?? 'Subject'); ?></td>
                                    <td><?php echo htmlspecialchars($subj['units'] ?? '3'); ?></td>
                                    <td><?php echo htmlspecialchars($subj['year_level'] ?? '1st'); ?></td>
                                    <td><?php echo htmlspecialchars($subj['prerequisites'] ?? 'None'); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="viewSubjectDetails('<?php echo $subj['id'] ?? 'N/A'; ?>')">
                                            View Details
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">No subjects found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB: Sections -->
    <div class="academics-panel" id="tab-sections">
        <div class="section-header">
            <h3>Manage Sections</h3>
        </div>

        <div class="search-filter-container">
            <div class="search-box">
                <input type="text" id="searchSections" placeholder="Search sections...">
            </div>
            <select class="filter-select" id="filterSectionYear">
                <option value="">All Year Levels</option>
                <option value="1st">1st Year</option>
                <option value="2nd">2nd Year</option>
                <option value="3rd">3rd Year</option>
                <option value="4th">4th Year</option>
            </select>
            <select class="filter-select" id="filterSectionStatus">
                <option value="">All Status</option>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
                <option value="Full">Full</option>
            </select>
        </div>

        <div class="table-card">
            <div class="table-responsive">
                <table id="sectionsTable">
                    <thead>
                        <tr>
                            <th>Section Code</th>
                            <th>Program</th>
                            <th>Grade Level</th>
                            <th>Semester</th>
                            <th>School Year</th>
                            <th>Adviser</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($sections)): ?>
                            <?php foreach ($sections as $sec): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($sec['section_code'] ?? 'SEC-001'); ?></td>
                                    <td><?php echo htmlspecialchars($sec['program'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($sec['grade_level'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($sec['semester'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($sec['school_year'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($sec['adviser_name'] ?? 'Not Assigned'); ?></td>
                                    <td>
                                        <span class="badge badge-success">Active</span>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <button class="btn btn-sm btn-info" onclick="editSection('<?php echo $sec['id'] ?? 'N/A'; ?>')">
                                                Edit
                                            </button>
                                            <button class="btn btn-sm btn-info" onclick="assignAdviser('<?php echo $sec['id'] ?? 'N/A'; ?>', '<?php echo htmlspecialchars($sec['code'] ?? 'SEC-001'); ?>')">
                                                Assign
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted">No sections found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB: Faculty Load -->
    <div class="academics-panel" id="tab-faculty">
        <div class="section-header">
            <h3>Manage Faculty Load</h3>
        </div>

        <div class="search-filter-container">
            <div class="search-box">
                <input type="text" id="searchFaculty" placeholder="Search faculty...">
            </div>
            <select class="filter-select" id="filterLoadStatus">
                <option value="">All Status</option>
                <option value="Fully Loaded">Fully Loaded</option>
                <option value="Underloaded">Underloaded</option>
                <option value="Overloaded">Overloaded</option>
            </select>
        </div>

        <div class="table-card">
            <div class="table-responsive">
                <table id="facultyTable">
                    <thead>
                        <tr>
                            <th>Faculty Name</th>
                            <th>Department</th>
                            <th>Classes Assigned</th>
                            <th>Total Units</th>
                            <th>Max Load</th>
                            <th>Load Status</th>
                            <th>Conflicts</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($facultyLoads)): ?>
                            <?php foreach ($facultyLoads as $fac): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($fac['faculty_name'] ?? 'Dr. Smith'); ?></td>
                                    <td><?php echo htmlspecialchars($fac['department'] ?? 'CS'); ?></td>
                                    <td><?php echo htmlspecialchars($fac['classes_assigned'] ?? '4'); ?></td>
                                    <td><?php echo htmlspecialchars($fac['total_units'] ?? '12'); ?></td>
                                    <td><?php echo htmlspecialchars($fac['max_load'] ?? '15'); ?></td>
                                    <td>
                                        <?php
                                        $totalUnits = intval($fac['total_units'] ?? 0);
                                        $maxLoad = intval($fac['max_load'] ?? 15);
                                        if ($totalUnits >= $maxLoad) {
                                            $loadStatus = 'Fully Loaded';
                                            $badgeClass = 'badge-success';
                                        } elseif ($totalUnits > $maxLoad) {
                                            $loadStatus = 'Overloaded';
                                            $badgeClass = 'badge-danger';
                                        } else {
                                            $loadStatus = 'Underloaded';
                                            $badgeClass = 'badge-warning';
                                        }
                                        ?>
                                        <span class="badge <?php echo $badgeClass; ?>"><?php echo $loadStatus; ?></span>
                                    </td>
                                    <td>
                                        <span class="badge badge-info"><?php echo htmlspecialchars($fac['conflicts'] ?? '0'); ?></span>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <button class="btn btn-sm btn-info" onclick="viewFacultyLoadDetails('<?php echo $fac['id'] ?? 'N/A'; ?>')">
                                                View
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted">No faculty loads found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB: Student Records -->
    <div class="academics-panel" id="tab-students">
        <div class="section-header">
            <h3>Student Records</h3>
            <p class="text-muted" style="margin: 0;"><small>Read-only monitoring</small></p>
        </div>

        <div class="search-filter-container">
            <div class="search-box">
                <input type="text" id="searchStudents" placeholder="Search students by ID or name...">
            </div>
            <select class="filter-select" id="filterStudentStatus">
                <option value="">All Status</option>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
                <option value="Graduated">Graduated</option>
                <option value="On Leave">On Leave</option>
            </select>
            <select class="filter-select" id="filterStudentProgram">
                <option value="">All Programs</option>
                <?php foreach ($programs as $prog): ?>
                    <option value="<?php echo htmlspecialchars($prog['code']); ?>"><?php echo htmlspecialchars($prog['code']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="table-card">
            <div class="table-responsive">
                <table id="studentsTable">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Program</th>
                            <th>Year Level</th>
                            <th>GPA</th>
                            <th>Academic Status</th>
                            <th>Deficiencies</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($students)): ?>
                            <?php foreach ($students as $std): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($std['student_number'] ?? 'STU-0001'); ?></td>
                                    <td><?php echo htmlspecialchars($std['full_name'] ?? 'Student Name'); ?></td>
                                    <td><?php echo htmlspecialchars($std['course'] ?? 'BSIS'); ?></td>
                                    <td><?php echo htmlspecialchars($std['year_level'] ?? '2nd'); ?></td>
                                    <td>3.5</td>
                                    <td>
                                        <?php
                                        $status = strtolower($std['academic_status'] ?? 'active');
                                        $badgeClass = $status === 'active' ? 'badge-success' : ($status === 'inactive' ? 'badge-warning' : 'badge-danger');
                                        $badgeText = ucfirst($status);
                                        ?>
                                        <span class="badge <?php echo $badgeClass; ?>"><?php echo $badgeText; ?></span>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">0</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="viewStudentRecord('<?php echo $std['student_number'] ?? 'N/A'; ?>')">
                                            View Profile
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted">No students found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODALS -->

<!-- Curriculum Details Modal -->
<div class="modal" id="curriculumDetailsModal">
    <div class="modal-dialog">
        <div class="modal-header">
            <h5 class="modal-title">Curriculum Details</h5>
            <button type="button" class="modal-close" onclick="closeCurriculumModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="form-label">Program</label>
                <input type="text" class="form-control" id="currProgramName" readonly>
            </div>
            <div class="form-group">
                <label class="form-label">Curriculum Version</label>
                <input type="text" class="form-control" id="currVersion" readonly>
            </div>
            <div class="form-group">
                <label class="form-label">Total Units</label>
                <input type="text" class="form-control" id="currUnits" readonly>
            </div>
            <div class="form-group">
                <label class="form-label">Subject Flow</label>
                <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; max-height: 300px; overflow-y: auto;">
                    <div id="subjectFlowList"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Subject Details Modal -->
<div class="modal" id="subjectDetailsModal">
    <div class="modal-dialog">
        <div class="modal-header">
            <h5 class="modal-title">Subject Details</h5>
            <button type="button" class="modal-close" onclick="closeSubjectModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="form-label">Subject Code</label>
                <input type="text" class="form-control" id="subjCode" readonly>
            </div>
            <div class="form-group">
                <label class="form-label">Subject Name</label>
                <input type="text" class="form-control" id="subjName" readonly>
            </div>
            <div class="form-group">
                <label class="form-label">Units</label>
                <input type="text" class="form-control" id="subjUnits" readonly>
            </div>
            <div class="form-group">
                <label class="form-label">Prerequisites</label>
                <input type="text" class="form-control" id="subjPrereqs" readonly>
            </div>
            <div class="form-group">
                <label class="form-label">Curriculum Mapping</label>
                <input type="text" class="form-control" id="subjCurr" readonly>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Section Modal -->
<div class="modal" id="sectionModal">
    <div class="modal-dialog">
        <div class="modal-header">
            <h5 class="modal-title" id="sectionModalTitle">Add Section</h5>
            <button type="button" class="modal-close" onclick="closeSectionModal()">&times;</button>
        </div>
        <form id="sectionForm">
            <div class="modal-body">
                <input type="hidden" id="sectionId">
                <div class="form-group">
                    <label class="form-label">Section Code <span class="required">*</span></label>
                    <input type="text" class="form-control" id="sectionCode" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Program <span class="required">*</span></label>
                    <select class="form-control" id="sectionProgram" required>
                        <option value="">Select Program</option>
                        <?php foreach ($programs as $prog): ?>
                            <option value="<?php echo htmlspecialchars($prog['code']); ?>"><?php echo htmlspecialchars($prog['code']); ?> - <?php echo htmlspecialchars($prog['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Year Level <span class="required">*</span></label>
                    <select class="form-control" id="sectionYear" required>
                        <option value="">Select Year</option>
                        <option value="1st">1st Year</option>
                        <option value="2nd">2nd Year</option>
                        <option value="3rd">3rd Year</option>
                        <option value="4th">4th Year</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Capacity <span class="required">*</span></label>
                    <input type="number" class="form-control" id="sectionCapacity" min="1" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeSectionModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Section</button>
            </div>
        </form>
    </div>
</div>

<!-- Assign Adviser Modal -->
<div class="modal" id="adviserModal">
    <div class="modal-dialog">
        <div class="modal-header">
            <h5 class="modal-title">Assign Adviser</h5>
            <button type="button" class="modal-close" onclick="closeAdviserModal()">&times;</button>
        </div>
        <form id="adviserForm" data-custom-submit>
            <div class="modal-body">
                <input type="hidden" id="adviserSectionId">
                <div class="form-group">
                    <label class="form-label">Section</label>
                    <input type="text" class="form-control" id="adviserSection" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label">Select Adviser <span class="required">*</span></label>
                    <select class="form-control" id="adviserSelect" required>
                        <option value="">Choose Faculty Member</option>
                        <?php foreach ($facultyLoads as $faculty): ?>
                            <option value="<?php echo htmlspecialchars($faculty['faculty_name']); ?>" data-id="<?php echo htmlspecialchars($faculty['id']); ?>">
                                <?php echo htmlspecialchars($faculty['faculty_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeAdviserModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Assign</button>
            </div>
        </form>
    </div>
</div>

<!-- Add/Edit Faculty Load Modal -->
<div class="modal" id="facultyLoadModal">
    <div class="modal-dialog">
        <div class="modal-header">
            <h5 class="modal-title" id="facultyLoadModalTitle">Assign Faculty Load</h5>
            <button type="button" class="modal-close" onclick="closeFacultyLoadModal()">&times;</button>
        </div>
        <form id="facultyLoadForm">
            <div class="modal-body">
                <input type="hidden" id="facultyLoadId">
                <div class="form-group">
                    <label class="form-label">Faculty Member <span class="required">*</span></label>
                    <select class="form-control" id="facultySelect" required>
                        <option value="">Select Faculty</option>
                        <option value="Dr. Maria Santos">Dr. Maria Santos</option>
                        <option value="Prof. Juan Reyes">Prof. Juan Reyes</option>
                        <option value="Dr. Rita Ocampo">Dr. Rita Ocampo</option>
                        <option value="Prof. Ramon Miguel">Prof. Ramon Miguel</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Section/Class <span class="required">*</span></label>
                    <select class="form-control" id="loadSectionSelect" required>
                        <option value="">Select Section</option>
                        <option value="IT-1A">IT-1A</option>
                        <option value="IT-1B">IT-1B</option>
                        <option value="CS-2A">CS-2A</option>
                        <option value="EN-3A">EN-3A</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Subject Code <span class="required">*</span></label>
                    <input type="text" class="form-control" id="loadSubject" placeholder="e.g., SUBJ101" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Load (Units) <span class="required">*</span></label>
                    <input type="number" class="form-control" id="loadUnits" min="1" max="6" value="3" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Schedule <span class="required">*</span></label>
                    <input type="text" class="form-control" id="loadSchedule" placeholder="e.g., MWF 9:00-10:30" required>
                </div>
                <div style="background: #fff3cd; padding: 10px; border-radius: 5px; margin-top: 15px; display: none;" id="conflictWarning">
                    <small><strong>⚠️ Schedule Conflict Detected:</strong> This faculty member has another class during this time.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeFacultyLoadModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Assign Load</button>
            </div>
        </form>
    </div>
</div>

<!-- Faculty Load Details Modal -->
<div class="modal" id="facultyLoadDetailsModal">
    <div class="modal-dialog" style="max-width: 700px;">
        <div class="modal-header">
            <h5 class="modal-title">Faculty Load Details</h5>
            <button type="button" class="modal-close" onclick="closeFacultyLoadDetailsModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="form-label"><strong>Faculty Name</strong></label>
                <p id="detailFacultyName"></p>
            </div>
            <div class="form-group">
                <label class="form-label"><strong>Department</strong></label>
                <p id="detailDepartment"></p>
            </div>
            <div class="form-group">
                <label class="form-label"><strong>Class Schedule</strong></label>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f8f9fa;">
                            <th style="padding: 8px; text-align: left; border-bottom: 1px solid #dee2e6;">Subject Code</th>
                            <th style="padding: 8px; text-align: left; border-bottom: 1px solid #dee2e6;">Section</th>
                            <th style="padding: 8px; text-align: left; border-bottom: 1px solid #dee2e6;">Day</th>
                            <th style="padding: 8px; text-align: left; border-bottom: 1px solid #dee2e6;">Time</th>
                            <th style="padding: 8px; text-align: left; border-bottom: 1px solid #dee2e6;">Room</th>
                        </tr>
                    </thead>
                    <tbody id="detailScheduleList">
                        <tr>
                            <td colspan="5" style="padding: 8px; text-align: center; color: #6c757d;">No schedules assigned</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeFacultyLoadDetailsModal()">Close</button>
        </div>
    </div>
</div>

<!-- Student Record Modal -->
<div class="modal" id="studentRecordModal">
    <div class="modal-dialog">
        <div class="modal-header">
            <h5 class="modal-title">Student Record</h5>
            <button type="button" class="modal-close" onclick="closeStudentModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                <div class="form-group">
                    <label class="form-label">Student ID</label>
                    <input type="text" class="form-control" id="stdRecordId" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" id="stdRecordName" readonly>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                <div class="form-group">
                    <label class="form-label">Program</label>
                    <input type="text" class="form-control" id="stdRecordProgram" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label">Year Level</label>
                    <input type="text" class="form-control" id="stdRecordYear" readonly>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                <div class="form-group">
                    <label class="form-label">GPA</label>
                    <input type="text" class="form-control" id="stdRecordGPA" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <input type="text" class="form-control" id="stdRecordStatus" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label">Deficiencies</label>
                    <input type="text" class="form-control" id="stdRecordDeficiencies" readonly>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Curriculum Progress</label>
                <div style="background: #f8f9fa; padding: 15px; border-radius: 5px;">
                    <div style="margin-bottom: 10px;">
                        <strong>Total Units Required: 120</strong>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <strong>Completed Units: <span id="stdCompletedUnits">45</span></strong>
                    </div>
                    <div style="background: white; border-radius: 5px; overflow: hidden; height: 20px; margin-bottom: 10px;">
                        <div style="background: linear-gradient(to right, #28a745, #28a745); height: 100%; width: 37.5%; display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; font-weight: bold;">37.5%</div>
                    </div>
                    <div style="font-size: 12px; color: #6c757d;">
                        <strong>Remaining Units: <span id="stdRemainingUnits">75</span></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Section Students Modal -->
<div class="modal" id="sectionStudentsModal">
    <div class="modal-dialog">
        <div class="modal-header">
            <h5 class="modal-title">Section Students</h5>
            <button type="button" class="modal-close" onclick="closeSectionStudentsModal()">&times;</button>
        </div>
        <div class="modal-body">
            <input type="text" class="form-control" id="sectionStudentSearch" placeholder="Search students..." style="margin-bottom: 15px;">
            <div style="max-height: 400px; overflow-y: auto;">
                <table style="width: 100%; font-size: 13px;">
                    <thead>
                        <tr style="background: #f8f9fa;">
                            <th style="padding: 8px; text-align: left;">Student ID</th>
                            <th style="padding: 8px; text-align: left;">Name</th>
                            <th style="padding: 8px; text-align: left;">Status</th>
                        </tr>
                    </thead>
                    <tbody id="sectionStudentsList">
                        <tr>
                            <td colspan="3" style="padding: 8px; text-align: center; color: #6c757d;">No students loaded</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    // Tab Switching
    document.querySelectorAll('.academics-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            const tabName = tab.getAttribute('data-tab');
            
            document.querySelectorAll('.academics-panel').forEach(panel => {
                panel.classList.remove('active');
            });
            document.querySelectorAll('.academics-tab').forEach(t => {
                t.classList.remove('active');
            });
            
            document.getElementById(`tab-${tabName}`).classList.add('active');
            tab.classList.add('active');
        });
    });

    // Modal Functions
    function closeModalById(modalId) {
        document.getElementById(modalId).classList.remove('show');
    }

    function openModalById(modalId) {
        document.getElementById(modalId).classList.add('show');
    }

    // Curriculum Details
    function viewCurriculumDetails(programId) {
        openModalById('curriculumDetailsModal');
        document.getElementById('currProgramName').value = 'Bachelor of Science in Information Technology';
        document.getElementById('currVersion').value = 'v2.0 (2023)';
        document.getElementById('currUnits').value = '120';
        
        const subjectFlow = `
            <strong>1st Year, 1st Semester:</strong><br>
            • SUBJ101 - Introduction to IT (3 units)<br>
            • MATH101 - Calculus I (4 units)<br>
            • ENG101 - English I (3 units)<br><br>
            
            <strong>1st Year, 2nd Semester:</strong><br>
            • SUBJ102 - Programming Fundamentals (3 units)<br>
            • MATH102 - Calculus II (4 units)<br>
            • ENG102 - English II (3 units)<br><br>
            
            <strong>2nd Year and Beyond...</strong><br>
            (Additional subjects following curriculum structure)
        `;
        document.getElementById('subjectFlowList').innerHTML = subjectFlow;
    }

    function closeCurriculumModal() {
        closeModalById('curriculumDetailsModal');
    }

    // Subject Details
    function viewSubjectDetails(subjectId) {
        openModalById('subjectDetailsModal');
        document.getElementById('subjCode').value = 'SUBJ101';
        document.getElementById('subjName').value = 'Introduction to IT';
        document.getElementById('subjUnits').value = '3';
        document.getElementById('subjPrereqs').value = 'None';
        document.getElementById('subjCurr').value = 'BS-IT, BS-CS (1st Year, Core)';
    }

    function closeSubjectModal() {
        closeModalById('subjectDetailsModal');
    }

    // Section Management
    function openAddSectionModal() {
        document.getElementById('sectionId').value = '';
        document.getElementById('sectionModalTitle').textContent = 'Add Section';
        document.getElementById('sectionCode').value = '';
        document.getElementById('sectionProgram').value = '';
        document.getElementById('sectionYear').value = '';
        document.getElementById('sectionCapacity').value = '';
        openModalById('sectionModal');
    }

    function editSection(sectionId) {
        document.getElementById('sectionId').value = sectionId;
        document.getElementById('sectionModalTitle').textContent = 'Edit Section';
        document.getElementById('sectionCode').value = 'IT-1A';
        document.getElementById('sectionProgram').value = 'BS-IT';
        document.getElementById('sectionYear').value = '1st';
        document.getElementById('sectionCapacity').value = '45';
        openModalById('sectionModal');
    }

    function closeSectionModal() {
        closeModalById('sectionModal');
    }

    function assignAdviser(sectionId, sectionCode) {
        document.getElementById('adviserSectionId').value = sectionId;
        document.getElementById('adviserSection').value = sectionCode || 'SEC-001';
        document.getElementById('adviserSelect').value = '';
        openModalById('adviserModal');
    }

    function closeAdviserModal() {
        closeModalById('adviserModal');
    }

    function viewSectionStudents(sectionId) {
        openModalById('sectionStudentsModal');
        const sampleStudents = [
            { id: 'STU-0001', name: 'Maria Santos Garcia', status: 'Active' },
            { id: 'STU-0002', name: 'Juan Carlos Reyes', status: 'Active' },
            { id: 'STU-0003', name: 'Ana Maria Cruz', status: 'Active' },
            { id: 'STU-0004', name: 'Miguel Fernando Lopez', status: 'Active' },
            { id: 'STU-0005', name: 'Rosa Isabel Ocampo', status: 'Active' }
        ];
        
        const tbody = document.getElementById('sectionStudentsList');
        tbody.innerHTML = sampleStudents.map(std => `
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #dee2e6;">${std.id}</td>
                <td style="padding: 8px; border-bottom: 1px solid #dee2e6;">${std.name}</td>
                <td style="padding: 8px; border-bottom: 1px solid #dee2e6;"><span class="badge badge-success">${std.status}</span></td>
            </tr>
        `).join('');
    }

    function closeSectionStudentsModal() {
        closeModalById('sectionStudentsModal');
    }

    // Faculty Load Management
    function openAddFacultyLoadModal() {
        document.getElementById('facultyLoadId').value = '';
        document.getElementById('facultyLoadModalTitle').textContent = 'Assign Faculty Load';
        document.getElementById('facultySelect').value = '';
        document.getElementById('loadSectionSelect').value = '';
        document.getElementById('loadSubject').value = '';
        document.getElementById('loadUnits').value = '3';
        document.getElementById('loadSchedule').value = '';
        document.getElementById('conflictWarning').style.display = 'none';
        openModalById('facultyLoadModal');
    }

    function editFacultyLoad(loadId) {
        document.getElementById('facultyLoadId').value = loadId;
        document.getElementById('facultyLoadModalTitle').textContent = 'Edit Faculty Load';
        document.getElementById('facultySelect').value = 'Dr. Maria Santos';
        document.getElementById('loadSectionSelect').value = 'IT-1A';
        document.getElementById('loadSubject').value = 'SUBJ101';
        document.getElementById('loadUnits').value = '3';
        document.getElementById('loadSchedule').value = 'MWF 9:00-10:30';
        document.getElementById('conflictWarning').style.display = 'none';
        openModalById('facultyLoadModal');
    }

    function deleteFacultyLoad(loadId) {
        if (confirm('Are you sure you want to delete this faculty load assignment?')) {
            // Handle delete
            alert('Faculty load deleted successfully');
        }
    }

    function closeFacultyLoadModal() {
        closeModalById('facultyLoadModal');
    }

    function viewFacultyLoadDetails(facultyId) {
        // Find faculty data from table
        const rows = document.querySelectorAll('#facultyTable tbody tr');
        let facultyName = '';
        let department = '';
        
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            if (cells.length > 0) {
                facultyName = cells[0].textContent;
                department = cells[1].textContent;
            }
        });
        
        // Set faculty info
        document.getElementById('detailFacultyName').textContent = facultyName || 'N/A';
        document.getElementById('detailDepartment').textContent = department || 'N/A';
        
        // Fetch schedule data from server
        fetch(`/sms/modules/college-coor/api/get_faculty_schedule.php?faculty_id=${facultyId}`)
            .then(response => response.json())
            .then(data => {
                const scheduleList = document.getElementById('detailScheduleList');
                
                if (data.success && data.schedules.length > 0) {
                    scheduleList.innerHTML = data.schedules.map(schedule => `
                        <tr>
                            <td style="padding: 8px; border-bottom: 1px solid #dee2e6;">${schedule.subject_code || 'N/A'}</td>
                            <td style="padding: 8px; border-bottom: 1px solid #dee2e6;">${schedule.section_code || 'N/A'}</td>
                            <td style="padding: 8px; border-bottom: 1px solid #dee2e6;">${schedule.day_of_week || 'N/A'}</td>
                            <td style="padding: 8px; border-bottom: 1px solid #dee2e6;">${schedule.start_time || 'N/A'} - ${schedule.end_time || 'N/A'}</td>
                            <td style="padding: 8px; border-bottom: 1px solid #dee2e6;">${schedule.room || 'N/A'}</td>
                        </tr>
                    `).join('');
                } else {
                    scheduleList.innerHTML = '<tr><td colspan="5" style="padding: 8px; text-align: center; color: #6c757d;">No schedules assigned</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error fetching schedule:', error);
                document.getElementById('detailScheduleList').innerHTML = '<tr><td colspan="5" style="padding: 8px; text-align: center; color: #dc3545;">Error loading schedules</td></tr>';
            });
        
        openModalById('facultyLoadDetailsModal');
    }

    function closeFacultyLoadDetailsModal() {
        closeModalById('facultyLoadDetailsModal');
    }

    // Student Record
    function viewStudentRecord(studentId) {
        openModalById('studentRecordModal');
        document.getElementById('stdRecordId').value = studentId;
        document.getElementById('stdRecordName').value = 'Maria Santos Garcia';
        document.getElementById('stdRecordProgram').value = 'BSIS';
        document.getElementById('stdRecordYear').value = '2nd Year';
        document.getElementById('stdRecordGPA').value = '3.65';
        document.getElementById('stdRecordStatus').value = 'Good Standing';
        document.getElementById('stdRecordDeficiencies').value = '0';
        document.getElementById('stdCompletedUnits').textContent = '45';
        document.getElementById('stdRemainingUnits').textContent = '75';
    }

    function closeStudentModal() {
        closeModalById('studentRecordModal');
    }

    // Form Submissions
    document.getElementById('sectionForm')?.addEventListener('submit', (e) => {
        e.preventDefault();
        alert('Section saved successfully');
        closeSectionModal();
    });

    document.getElementById('adviserForm')?.addEventListener('submit', (e) => {
        e.preventDefault();
        
        const sectionId = document.getElementById('adviserSectionId').value;
        const adviserSelect = document.getElementById('adviserSelect');
        const facultyId = adviserSelect.options[adviserSelect.selectedIndex].getAttribute('data-id');
        
        console.log('Assigning adviser - Section ID:', sectionId, 'Faculty ID:', facultyId);
        
        // Validate inputs
        if (!sectionId || !facultyId) {
            alert('Please select a valid adviser');
            return;
        }

        // Send to API endpoint
        const formData = new FormData();
        formData.append('section_id', sectionId);
        formData.append('faculty_id', facultyId);

        fetch('/sms/modules/college-coor/api/assign_adviser.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('API Response Status:', response.status);
            if (!response.ok) {
                throw new Error('Network response was not ok - Status: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('API Response Data:', data);
            if (data.success) {
                alert(data.message);
                closeAdviserModal();
                // Reload the page to show updated adviser from database
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error assigning adviser: ' + error.message);
        });
    });

    document.getElementById('facultyLoadForm')?.addEventListener('submit', (e) => {
        e.preventDefault();
        alert('Faculty load assigned successfully');
        closeFacultyLoadModal();
    });

    // Close modals when clicking outside
    window.addEventListener('click', (e) => {
        if (e.target.classList.contains('modal')) {
            e.target.classList.remove('show');
        }
    });

    // ================================================================
    // FACULTY LOAD - DYNAMIC LOADING AND AUTO-REFRESH
    // ================================================================
    
    // Helper function to escape HTML
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Load faculty load data dynamically from API
    function loadFacultyLoadData() {
        const tbody = document.getElementById('facultyTable')?.querySelector('tbody');
        if (!tbody) return;
        
        // Show loading state
        tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted"><i class="fas fa-spinner fa-spin"></i> Loading faculty load...</td></tr>';
        
        fetch('/sms/modules/college-coor/api/get_faculty_load.php')
            .then(response => {
                if (!response.ok) throw new Error('API request failed');
                return response.json();
            })
            .then(facultyLoads => {
                if (!facultyLoads || facultyLoads.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No faculty loads found</td></tr>';
                    return;
                }
                
                // Build HTML for each faculty load row
                tbody.innerHTML = facultyLoads.map(fac => {
                    const totalUnits = parseInt(fac.total_units) || 0;
                    const maxLoad = parseInt(fac.max_load) || 15;
                    let loadStatus = 'Underloaded';
                    let badgeClass = 'badge-warning';
                    
                    if (totalUnits >= maxLoad) {
                        loadStatus = 'Fully Loaded';
                        badgeClass = 'badge-success';
                    } else if (totalUnits > maxLoad) {
                        loadStatus = 'Overloaded';
                        badgeClass = 'badge-danger';
                    }
                    
                    return `
                        <tr>
                            <td>${escapeHtml(fac.faculty_name)}</td>
                            <td>${escapeHtml(fac.department)}</td>
                            <td>${fac.classes_assigned}</td>
                            <td>${totalUnits}</td>
                            <td>${maxLoad}</td>
                            <td><span class="badge ${badgeClass}">${loadStatus}</span></td>
                            <td><span class="badge badge-info">${fac.conflicts || 0}</span></td>
                            <td>
                                <div class="actions">
                                    <button class="btn btn-sm btn-info" onclick="viewFacultyLoadDetails('${fac.id}')">
                                        View
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                }).join('');
            })
            .catch(error => {
                console.error('Error loading faculty load:', error);
                tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Failed to load faculty load data</td></tr>';
            });
    }
    
    // Refresh faculty load data (can be called from other pages/modules)
    function refreshFacultyLoad() {
        loadFacultyLoadData();
    }
    
    // Make it globally accessible for other pages to call
    window.refreshFacultyLoad = refreshFacultyLoad;
    
    // Load faculty load data when page loads
    document.addEventListener('DOMContentLoaded', function() {
        loadFacultyLoadData();
    });
    
    // Also load when tab is clicked
    const facultyTab = document.querySelector('[data-tab="faculty"]');
    if (facultyTab) {
        facultyTab.addEventListener('click', function() {
            loadFacultyLoadData();
        });
    }
    
    // Load when page is dynamically loaded (via page-switcher)
    window.addEventListener('page:loaded', function(e) {
        if (e.detail && e.detail.page === 'academics-management') {
            loadFacultyLoadData();
        }
    });
    
    // Also listen for when coming FROM class-scheduling (schedule was created/updated/deleted)
    window.addEventListener('page:loaded', function(e) {
        if (e.detail && e.detail.page === 'class-scheduling') {
            // Signal that faculty load data needs refresh when user navigates to academics
            sessionStorage.setItem('refreshFacultyLoad', 'true');
        }
    });
    
    // Check if we need to refresh on page load
    window.addEventListener('page:loaded', function(e) {
        if (e.detail && e.detail.page === 'academics-management') {
            if (sessionStorage.getItem('refreshFacultyLoad') === 'true') {
                sessionStorage.removeItem('refreshFacultyLoad');
                // Delay slightly to ensure DOM is ready
                setTimeout(loadFacultyLoadData, 100);
            }
        }
    });
    
    // ================================================================
    // SEARCH FUNCTIONALITY FOR ALL TABLES
    // ================================================================
    
    // Initialize all search functionality
    function initializeAllSearches() {
        // Programs search
        attachTableSearch('searchPrograms', 'programsTable');
        
        // Subjects search
        attachTableSearch('searchSubjects', 'subjectsTable');
        
        // Sections search
        attachTableSearch('searchSections', 'sectionsTable');
        
        // Faculty search
        attachTableSearch('searchFaculty', 'facultyTable');
        
        // Students search
        attachTableSearch('searchStudents', 'studentsTable');
    }
    
    // Attach search functionality to a table
    function attachTableSearch(searchInputId, tableId) {
        const searchInput = document.getElementById(searchInputId);
        const table = document.getElementById(tableId);
        
        if (!searchInput || !table) return;
        
        const tbody = table.querySelector('tbody');
        if (!tbody) return;
        
        // Store original rows
        const originalRows = Array.from(tbody.querySelectorAll('tr')).filter(row => 
            !row.classList.contains('no-data')
        );
        
        if (originalRows.length === 0) return;
        
        // Search function
        function performSearch() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            let visibleCount = 0;
            
            originalRows.forEach(row => {
                const cells = row.querySelectorAll('td');
                let matches = false;
                
                if (!searchTerm) {
                    matches = true;
                } else {
                    // Search in all cells
                    matches = Array.from(cells).some(cell => 
                        cell.textContent.toLowerCase().includes(searchTerm)
                    );
                }
                
                row.style.display = matches ? '' : 'none';
                if (matches) visibleCount++;
            });
            
            // Handle no results
            if (visibleCount === 0 && searchTerm) {
                const noDataRow = tbody.querySelector('tr.no-data');
                if (!noDataRow) {
                    const newRow = document.createElement('tr');
                    newRow.className = 'no-data';
                    const cellCount = originalRows[0]?.querySelectorAll('td').length || 6;
                    newRow.innerHTML = `<td colspan="${cellCount}" style="text-align: center; padding: 20px; color: #999;">No results found for "${searchTerm}"</td>`;
                    
                    // Remove existing rows temporarily
                    originalRows.forEach(row => tbody.removeChild(row));
                    tbody.appendChild(newRow);
                    
                    // Store the no-data row reference for later
                    tbody.dataset.noDataRow = 'true';
                }
            } else if (visibleCount > 0) {
                const noDataRow = tbody.querySelector('tr.no-data');
                if (noDataRow) {
                    noDataRow.remove();
                    // Restore original rows
                    tbody.innerHTML = '';
                    const filteredRows = originalRows.filter(row => {
                        const cells = row.querySelectorAll('td');
                        if (!searchTerm) return true;
                        return Array.from(cells).some(cell => 
                            cell.textContent.toLowerCase().includes(searchTerm)
                        );
                    });
                    filteredRows.forEach(row => tbody.appendChild(row));
                }
            }
        }
        
        // Attach event listeners
        searchInput.addEventListener('keyup', performSearch);
        searchInput.addEventListener('change', performSearch);
    }
    
    // Initialize searches when page loads
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(initializeAllSearches, 100);
        loadFacultyLoadData();
    });
    
    // Re-initialize when tab switches
    window.addEventListener('page:loaded', function(e) {
        if (e.detail && e.detail.page === 'academics-management') {
            setTimeout(initializeAllSearches, 100);
        }
    });
</script>

<link rel="stylesheet" href="css/pages/academics-management.css">
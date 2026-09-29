<?php
// Include the session file (now modified to bypass auth)
include_once __DIR__ . '/../../../auth/session.php';

// Set default value for admin_name if not set
$admin_name = $admin_name ?? 'Admin';
$user_role = $user_role ?? 'Administrator';

require_once '../config/database.php';
require_once '../models/Applicant.php';
require_once '../models/Student.php';
require_once '../models/Course.php';
require_once '../models/Report.php';
require_once '../models/Section.php';

$applicant = new Applicant();
$student = new Student();
$course = new Course();
$section = new Section();

// Handle conversion
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['convert_to_student'])) {
    // Validate section has available slots
    if (!$section->hasAvailableSlots($_POST['section_id'])) {
        $error = "Selected section is already full or unavailable.";
    } else {
        $result = $student->convertFromApplicant(
            $_POST['applicant_id'], 
            $_POST['course_id'],
            $_POST['section_id'],
            $_SESSION['user_id']
        );
        
        if($result['success']) {
            header("Location: convert_student.php?msg=success&student_number=" . $result['student_number']);
            exit();
        } else {
            $error = $result['message'];
        }
    }
}

$verified_applicants = $applicant->getVerified();
$courses = $course->getAllActive();
$page_title = 'Convert to Student';

// Get current academic year and semester for display
$current_academic_year = $section->getCurrentAcademicYear();
$current_semester = $section->getCurrentSemester();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Convert to Student - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        .section-loading {
            text-align: center;
            padding: 20px;
            display: none;
        }
        .section-loading.active {
            display: block;
        }
        .section-select-container {
            display: none;
        }
        .section-select-container.active {
            display: block;
        }
        .no-sections-message {
            display: none;
            text-align: center;
            padding: 20px;
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            border-radius: 5px;
            color: #856404;
        }
        .no-sections-message.active {
            display: block;
        }
        .section-option-full {
            color: #dc3545;
            font-style: italic;
        }
        .section-option-available {
            color: #28a745;
        }
        .section-option-warning {
            color: #ffc107;
        }
    </style>
</head>
<body>
    <?php include_once '../includes/sidebar_admin.php'; ?>
    
    <div class="content">
        <h2 class="mb-4"><i class="fas fa-user-graduate"></i> Convert Applicants to Students</h2>

        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> 
                Successfully converted to student! 
                Student Number: <strong><?php echo htmlspecialchars($_GET['student_number']); ?></strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if(empty($verified_applicants)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> 
                No verified applicants available for conversion. 
                Please verify documents first.
                <hr>
                <a href="verify_documents.php" class="btn btn-primary btn-sm">
                    <i class="fas fa-check-circle"></i> Go to Document Verification
                </a>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach($verified_applicants as $app): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card student-card h-100">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-user-check"></i> 
                                <?php echo htmlspecialchars($app['application_number']); ?>
                            </h6>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">
                                <?php echo htmlspecialchars($app['first_name'] . ' ' . $app['surname']); ?>
                            </h5>
                            <p class="card-text">
                                <small class="text-muted">
                                    <i class="fas fa-school"></i> <?php echo htmlspecialchars($app['school_last_attended']); ?><br>
                                    <i class="fas fa-calendar"></i> Batch <?php echo htmlspecialchars($app['year_graduated']); ?><br>
                                    <i class="fas fa-file-alt"></i> Documents: <?php echo $applicant->countDocuments($app['id']); ?> files
                                </small>
                            </p>
                            <div class="d-grid">
                                <button type="button" class="btn btn-success" 
                                        onclick="openConvertModal(<?php echo $app['id']; ?>, '<?php echo htmlspecialchars(addslashes($app['first_name'] . ' ' . $app['surname'])); ?>')">
                                    <i class="fas fa-user-graduate"></i> Convert to Student
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Convert Modal -->
    <div class="modal fade" id="convertModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" id="convertForm">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-user-graduate"></i> Convert to Student
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" onclick="resetModal()"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="applicant_id" id="convert_applicant_id">
                        
                        <div class="text-center mb-4">
                            <i class="fas fa-user-circle fa-4x text-success"></i>
                            <h4 class="mt-2" id="applicant_name"></h4>
                            <p class="text-muted">Applicant will be converted to student</p>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Select Program/Course</label>
                                    <select name="course_id" id="course_id" class="form-select form-select-lg" required onchange="loadSections()">
                                        <option value="">-- Choose Course --</option>
                                        <?php foreach($courses as $c): ?>
                                            <option value="<?php echo $c['id']; ?>">
                                                <?php echo htmlspecialchars($c['course_code']); ?> - <?php echo htmlspecialchars($c['course_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Year Level</label>
                                    <select name="year_level" id="year_level" class="form-select form-select-lg" required onchange="loadSections()">
                                        <option value="">-- Select Year Level --</option>
                                        <option value="1">1st Year</option>
                                        <option value="2">2nd Year</option>
                                        <option value="3">3rd Year</option>
                                        <option value="4">4th Year</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Select Section</label>
                            
                            <!-- Academic Year and Semester Info -->
                            <div class="alert alert-light border mb-3">
                                <small>
                                    <i class="fas fa-calendar-alt"></i> 
                                    Current Academic Year: <strong><?php echo $current_academic_year; ?></strong> | 
                                    Semester: <strong><?php echo $current_semester; ?></strong>
                                </small>
                            </div>
                            
                            <!-- Loading Spinner -->
                            <div id="section-loading" class="section-loading">
                                <div class="spinner-border text-success" role="status">
                                    <span class="visually-hidden">Loading sections...</span>
                                </div>
                                <p class="mt-2">Loading available sections...</p>
                            </div>
                            
                            <!-- Section Dropdown Container -->
                            <div id="section-container" class="section-select-container">
                                <select name="section_id" id="section_id" class="form-select" required>
                                    <option value="">-- Select Section --</option>
                                </select>
                                <small class="text-muted" id="selected-section-info"></small>
                            </div>
                            
                            <!-- No Sections Message -->
                            <div id="no-sections-message" class="no-sections-message">
                                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                <h5>No Available Sections</h5>
                                <p>There are no available sections for the selected course and year level in <?php echo $current_academic_year; ?> (<?php echo $current_semester; ?>).</p>
                                <div class="mt-3">
                                    <a href="sections.php" class="btn btn-sm btn-primary" target="_blank">
                                        <i class="fas fa-plus-circle"></i> Create New Section
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle"></i>
                            <strong>Note:</strong> Student will be enrolled with the following details:
                            <ul class="mb-0 mt-2">
                                <li>Generate a unique student number</li>
                                <li>Convert applicant account to student account</li>
                                <li>Assign to selected section</li>
                                <li>Enroll student in selected course</li>
                                <li>Update application status to "Converted"</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="resetModal()">Cancel</button>
                        <button type="submit" name="convert_to_student" id="convertBtn" class="btn btn-success" disabled>
                            <i class="fas fa-user-graduate"></i> Confirm Conversion
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    let convertModal;
    
    document.addEventListener('DOMContentLoaded', function() {
        convertModal = new bootstrap.Modal(document.getElementById('convertModal'));
    });
    
    function openConvertModal(applicantId, applicantName) {
        document.getElementById('convert_applicant_id').value = applicantId;
        document.getElementById('applicant_name').textContent = applicantName;
        
        // Reset form
        document.getElementById('convertForm').reset();
        resetSectionUI();
        
        convertModal.show();
    }
    
    function resetModal() {
        document.getElementById('convertForm').reset();
        resetSectionUI();
    }
    
    function resetSectionUI() {
        document.getElementById('section-loading').classList.remove('active');
        document.getElementById('section-container').classList.remove('active');
        document.getElementById('no-sections-message').classList.remove('active');
        document.getElementById('convertBtn').disabled = true;
        document.getElementById('section_id').innerHTML = '<option value="">-- Select Section --</option>';
        document.getElementById('selected-section-info').innerHTML = '';
    }
    
    function loadSections() {
        const courseId = document.getElementById('course_id').value;
        const yearLevel = document.getElementById('year_level').value;
        
        if (!courseId || !yearLevel) {
            resetSectionUI();
            return;
        }
        
        // Show loading, hide others
        document.getElementById('section-loading').classList.add('active');
        document.getElementById('section-container').classList.remove('active');
        document.getElementById('no-sections-message').classList.remove('active');
        document.getElementById('convertBtn').disabled = true;
        
        // Fetch sections via AJAX
        fetch(`get_sections.php?course_id=${courseId}&year_level=${yearLevel}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('section-loading').classList.remove('active');
                
                if (data.success && data.sections.length > 0) {
                    // Populate sections dropdown
                    const select = document.getElementById('section_id');
                    select.innerHTML = '<option value="">-- Select Section --</option>';
                    
                    data.sections.forEach(section => {
                        const option = document.createElement('option');
                        option.value = section.id;
                        
                        // Calculate fill percentage
                        const fillPercentage = (section.current_students / section.max_students) * 100;
                        let statusClass = '';
                        let statusText = '';
                        
                        if (section.available_slots <= 0) {
                            option.disabled = true;
                            statusText = ' (FULL)';
                            statusClass = 'section-option-full';
                        } else if (section.available_slots <= 5) {
                            statusText = ` (${section.available_slots} slots left - LIMITED)`;
                            statusClass = 'section-option-warning';
                        } else {
                            statusText = ` (${section.available_slots} slots available)`;
                            statusClass = 'section-option-available';
                        }
                        
                        option.textContent = `${section.section_code} - ${section.section_name}${statusText}`;
                        option.className = statusClass;
                        
                        // Add data attributes for info display
                        option.dataset.currentStudents = section.current_students;
                        option.dataset.maxStudents = section.max_students;
                        option.dataset.availableSlots = section.available_slots;
                        
                        select.appendChild(option);
                    });
                    
                    document.getElementById('section-container').classList.add('active');
                    
                    // Add change event listener for section info
                    select.addEventListener('change', function() {
                        const selected = this.options[this.selectedIndex];
                        if (this.value) {
                            document.getElementById('convertBtn').disabled = false;
                            document.getElementById('selected-section-info').innerHTML = 
                                `<i class="fas fa-info-circle"></i> Selected section has ${selected.dataset.availableSlots} available slots out of ${selected.dataset.maxStudents}`;
                        } else {
                            document.getElementById('convertBtn').disabled = true;
                            document.getElementById('selected-section-info').innerHTML = '';
                        }
                    });
                    
                } else {
                    // No sections available
                    document.getElementById('no-sections-message').classList.add('active');
                    document.getElementById('convertBtn').disabled = true;
                }
            })
            .catch(error => {
                console.error('Error loading sections:', error);
                document.getElementById('section-loading').classList.remove('active');
                document.getElementById('no-sections-message').classList.add('active');
                document.getElementById('no-sections-message').innerHTML = `
                    <i class="fas fa-exclamation-circle fa-2x mb-2"></i>
                    <h5>Error Loading Sections</h5>
                    <p>There was an error loading sections. Please try again.</p>
                    <small class="text-muted">${error.message}</small>
                `;
            });
    }
    </script>
</body>
</html>
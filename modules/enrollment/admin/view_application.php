<?php
include_once __DIR__ . '/../../../auth/session.php';

// Set default value for admin_name if not set
$admin_name = $admin_name ?? 'Admin';
$user_role = $user_role ?? 'Administrator';
require_once '../config/database.php';
require_once '../models/Applicant.php';
require_once '../models/Document.php';
require_once '../models/CourseSelection.php';

$applicant = new Applicant();
$document = new Document();
$course_selection = new CourseSelection();

$id = $_GET['id'] ?? 0;
$applicant_data = $applicant->getById($id);

if(!$applicant_data) {
    header("Location: applications.php");
    exit();
}

$documents = $document->getByApplicant($id);
$selected_courses = $course_selection->getByApplicant($id);
$page_title = 'View Application';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Application</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <?php include_once '../includes/sidebar_admin.php'; ?>
    
    <div class="content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-file-alt"></i> Application Details</h2>
            <div>
                <a href="applications.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                <?php if($applicant_data['status'] == 'pending'): ?>
                <a href="verify_documents.php?applicant_id=<?php echo $id; ?>" class="btn btn-success">
                    <i class="fas fa-check-circle"></i> Verify Documents
                </a>
                <?php elseif($applicant_data['status'] == 'verified'): ?>
                <a href="convert_student.php?applicant_id=<?php echo $id; ?>" class="btn btn-primary">
                    <i class="fas fa-user-graduate"></i> Convert to Student
                </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Personal Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <th width="40%">Application Number:</th>
                                <td><?php echo $applicant_data['application_number']; ?></td>
                            </tr>
                            <tr>
                                <th>Full Name:</th>
                                <td><?php echo $applicant_data['first_name'] . ' ' . 
                                          $applicant_data['middle_name'] . ' ' . 
                                          $applicant_data['surname'] . ' ' . 
                                          $applicant_data['suffix']; ?></td>
                            </tr>
                            <tr>
                                <th>Sex:</th>
                                <td><?php echo $applicant_data['sex']; ?></td>
                            </tr>
                            <tr>
                                <th>Date of Birth:</th>
                                <td><?php echo date('F d, Y', strtotime($applicant_data['date_of_birth'])); ?></td>
                            </tr>
                            <tr>
                                <th>Place of Birth:</th>
                                <td><?php echo $applicant_data['place_of_birth']; ?></td>
                            </tr>
                            <tr>
                                <th>Age:</th>
                                <td><?php echo $applicant_data['age']; ?></td>
                            </tr>
                            <tr>
                                <th>Civil Status:</th>
                                <td><?php echo $applicant_data['civil_status']; ?></td>
                            </tr>
                            <tr>
                                <th>Contact Number:</th>
                                <td><?php echo $applicant_data['contact_number']; ?></td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td><?php echo $applicant_data['email']; ?></td>
                            </tr>
                            <tr>
                                <th>Address:</th>
                                <td><?php echo $applicant_data['address_complete']; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Educational Background</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <th width="40%">School Last Attended:</th>
                                <td><?php echo $applicant_data['school_last_attended']; ?></td>
                            </tr>
                            <tr>
                                <th>Year Graduated:</th>
                                <td><?php echo $applicant_data['year_graduated']; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Parent/Guardian Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <th width="40%">Full Name:</th>
                                <td><?php echo $applicant_data['parent_full_name']; ?></td>
                            </tr>
                            <tr>
                                <th>Contact Number:</th>
                                <td><?php echo $applicant_data['parent_contact']; ?></td>
                            </tr>
                            <tr>
                                <th>Address:</th>
                                <td><?php echo $applicant_data['parent_address']; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0">Course Selections</h5>
                    </div>
                    <div class="card-body">
                        <?php if(count($selected_courses) > 0): ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Course Code</th>
                                        <th>Course Name</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($selected_courses as $course): ?>
                                    <tr>
                                        <td><?php echo $course['course_code']; ?></td>
                                        <td><?php echo $course['course_name']; ?></td>
                                        <td>
                                            <?php echo $course['is_continuous'] ? 'Continuous/Transferee' : 'Freshman'; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $course['status'] == 'approved' ? 'success' : 
                                                    ($course['status'] == 'pending' ? 'warning' : 'danger'); 
                                            ?>">
                                                <?php echo ucfirst($course['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <p class="text-muted mb-0">No course selections yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">Uploaded Documents</h5>
                    </div>
                    <div class="card-body">
                        <?php if(count($documents) > 0): ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Document Type</th>
                                        <th>File Name</th>
                                        <th>Uploaded</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($documents as $doc): ?>
                                    <tr>
                                        <td><?php echo $doc['document_type']; ?></td>
                                        <td><?php echo $doc['file_name']; ?></td>
                                        <td><?php echo date('M d, Y', strtotime($doc['uploaded_at'])); ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $doc['status'] == 'verified' ? 'success' : 
                                                    ($doc['status'] == 'pending' ? 'warning' : 'danger'); 
                                            ?>">
                                                <?php echo ucfirst($doc['status']); ?>
                                            </span>
                                        </td>
                                        <td>
    <a href="<?php echo $doc['full_url']; ?>" target="_blank" 
       class="btn btn-sm btn-info">
        <i class="fas fa-eye"></i> View
    </a>
</td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <p class="text-muted mb-0">No documents uploaded yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
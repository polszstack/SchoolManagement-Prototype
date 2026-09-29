<?php
include_once __DIR__ . '/../../../auth/session.php';

// Set default value for admin_name if not set
$admin_name = $admin_name ?? 'Admin';
$user_role = $user_role ?? 'Administrator';
include __DIR__ . '/../classes/Report.php';
include __DIR__ . '/../classes/Department.php';
include __DIR__ . '/../classes/User.php';

/*User Class*/
$userClass = new User();
$userInfo = $userClass->userSession();

/*Report Class*/
$reportClass = new Report();
$myDepartmentId = (int) ($_SESSION['department_id'] ?? 0);
$reportTypes = $reportClass->getReportTypesByDepartment($myDepartmentId);

// Get reports - you need to define this variable
$reports = $reportClass->getReports() ?? []; // Make sure this method exists

/*Department Class*/
$departmentClass = new Department();
$departments = $departmentClass->getAllDepartments();

include '../includes/header.php';
include '../includes/sidebar_admin.php';
?>

<!-- Add wrapper div with margin for sidebar -->
 <link rel="stylesheet" href="../css/pages/report-submission-management.css">
<div class="module-header" style="padding-left: 0;">
        <h1>Report Submission</h1>
    </div>

    <div class="module-content" style="padding: 0;">
        <div class="rsm-controls">
            <div class="rsm-upload">
                <h3>Submit Report</h3>
                <form id="report-upload-form" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="report-title">Title</label>
                        <input id="report-title" name="title" type="text" placeholder="Enter report title" required>
                    </div>

                    <div class="form-group">
                        <label for="report-type">Report Type</label>
                        <select id="report-type" name="report_type" required>
                            <option value="">-- Select Type --</option>
                                <?php foreach ($reportTypes as $type): ?>
                                    <option value="<?= htmlspecialchars($type['type_id']) ?>">
                                        <?= htmlspecialchars($type['report_type']) ?>
                                    </option>
                                <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="report-file">File</label>
                        <input class="file-btn" id="report-file" name="file" type="file" accept=".pdf,.doc,.docx,.xls,.xlsx" required>
                        <span id="file-error" style="display:none; color:red; font-size:0.85rem;"></span>
                    </div>

                    <div id="upload-progress" style="display:none; margin-bottom:10px;">
                        <div style="background:#e9ecef; border-radius:4px; overflow:hidden;">
                            <div id="progress-bar" style="height:8px; width:0%; background:#28a745; transition:width 0.3s;"></div>
                        </div>
                        <small id="progress-text">0%</small>
                    </div>

                    <div class="form-actions">
                        <button type="submit" id="submit-btn" class="btn-upload">Submit Report</button>
                        <button type="reset" id="reset-btn" class="btn-cancel">Clear</button>
                    </div>
                </form>
            </div>

            <div class="form-section" style="margin-top: 20px;">
                <div class="rsm-list table-responsive">
                    <div class="rsm-list-head">
                        <h3 style="margin: 0; color: var(--color5); font-size: 1.25rem;">Report List</h3>
                        <div class="rsm-filters">
                            <!-- Add filters here if needed -->
                        </div>
                    </div>
                    <table class="rsm-table">
                        <thead>
                            <tr>
                                <th>Report ID</th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Department</th>
                                <th>Submitted By</th>
                                <th>Submitted At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($reports)) : ?>
                                <?php foreach ($reports as $report) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($report['report_id']) ?></td>
                                    <td><?= htmlspecialchars($report['title']) ?></td>
                                    <td><?= htmlspecialchars($report['report_type'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($report['department_name'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($report['submitted_by'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($report['submitted_at'] ?? 'N/A') ?></td>
                                    <td>
                                        <?php if (!empty($report['file_path'])) : ?>
                                            <a style="color:var(--color2); text-decoration:none; border:1px solid var(--color4); padding:6px 8px; border-radius:6px; display:inline-block;" href="<?= htmlspecialchars($report['file_path']) ?>" target="_blank" class="btn-view">View</a>
                                        <?php else : ?>
                                            <span class="muted">No file</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr class="no-data">
                                    <td colspan="7" class="muted">No reports found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
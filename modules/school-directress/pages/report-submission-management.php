<?php
include_once __DIR__ . '/../../../auth/session.php';
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


/*Department Class*/
$departmentClass = new Department();
$departments = $departmentClass->getAllDepartments();
?>

<div class="module-header">
    <h1>Report Submissions & Management</h1>
    <p>View and manage submitted reports from teachers and staff.</p>
</div>

<div class="module-content">
    <div class="rsm-list-header">
        <h3>Report List</h3>
        <div class="rsm-header-actions">
            <div class="rsm-filters">
                <select id="rsm-filter" class="rsm-select">
                    <option value="">All Departments</option>
                    <?php foreach ($departments as $dept) : ?>
                        <option value="<?= htmlspecialchars($dept['department_id']) ?>">
                            <?= htmlspecialchars($dept['department_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input class="rsm-search" id="rsm-search" type="search" placeholder="Search by title or submitter">
            </div>
            <button type="button" id="rsm-open-modal" class="rsm-add-btn">+ Submit Report</button>
        </div>
    </div>

    <div class="rsm-list">
        <div class="rsm-table-wrapper">
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
                                <td class="rsm-actions-cell">
                                    <?php if (!empty($report['file_path'])) : ?>
                                        <a href="<?= htmlspecialchars($report['file_path']) ?>" target="_blank" class="rsm-btn-view">View</a>
                                    <?php else : ?>
                                        <span class="rsm-muted">No file</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr class="rsm-no-data">
                            <td colspan="7">No reports found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Submit Report Modal -->
    <div class="rsm-modal-overlay" id="rsm-modal-overlay">
        <div class="rsm-modal" role="dialog" aria-modal="true" aria-labelledby="rsm-modal-title">
            <div class="rsm-modal-header">
                <h3 id="rsm-modal-title">Submit Report</h3>
                <button type="button" class="rsm-modal-close" id="rsm-modal-close" aria-label="Close">&times;</button>
            </div>

            <div class="rsm-modal-body">
                <form id="report-upload-form" class="rsm-form" enctype="multipart/form-data" data-skip>

                    <div class="rsm-form-group">
                        <label for="report-title">Title</label>
                        <input type="text" id="report-title" name="title" placeholder="Enter report title" required>
                    </div>

                    <div class="rsm-form-group">
                        <label for="report-type">Report Type</label>
                        <select id="report-type" name="report_type" required>
                            <option value="">-- Select Type --</option>
                            <?php foreach ($reportTypes as $type) : ?>
                                <option value="<?= htmlspecialchars($type['type_id']) ?>">
                                    <?= htmlspecialchars($type['report_type']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="rsm-form-group">
                        <label for="report-file">File</label>
                        <input class="rsm-file" id="report-file" name="file" type="file" accept=".pdf,.doc,.docx,.xls,.xlsx" required>
                        <span id="file-error" class="rsm-file-error" style="display:none;"></span>
                    </div>

                    <div id="upload-progress" class="rsm-upload-progress" style="display:none;">
                        <div class="rsm-progress-track">
                            <div id="progress-bar" class="rsm-progress-bar" style="width:0%;"></div>
                        </div>
                        <small id="progress-text">0%</small>
                    </div>

                    <div class="rsm-actions">
                        <button type="submit" id="submit-btn" class="rsm-btn-submit">Submit Report</button>
                        <button type="reset" id="reset-btn" class="rsm-btn-cancel">Clear</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
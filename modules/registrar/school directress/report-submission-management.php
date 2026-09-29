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
$reports     = $reportClass->getReports($_SESSION['department_id'] ?? null);
$reportTypes = $reportClass->getReportTypesByDepartment(
    $userInfo['role'] === 'School Directress' ? null : $_SESSION['department_id']
);

/*Department Class*/
$departmentClass = new Department();
$departments = $departmentClass->getAllDepartments();
?>

<div class="module-header">
    <h1>Report Submissions & Management</h1>
    <p>View and manage submitted reports from teachers and staff.</p>
</div>

<div class="module-content">
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
                        <?php var_dump($reportTypes); // ADD THIS LINE ?>
                        <?php foreach ($reportTypes as $type) : ?>
                            <option value="<?= $type['type_id'] ?>"><?= htmlspecialchars($type['report_type']) ?></option>
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

        <div class="form-section">
            <div class="rsm-list table-responsive">
                <div class="rsm-list-head">
                    <h3>Report List</h3>
                    <div class="rsm-filters">
                        <select id="rsm-filter">
                            <option value="">All Departments</option>
                            <?php foreach ($departments as $dept) : ?>
                                <option value="<?= $dept['department_id'] ?>"><?= htmlspecialchars($dept['department_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input class="rsm-search" id="rsm-search" type="search" placeholder="Search by title or submitter">
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
                                <td><?= htmlspecialchars($report['department_name']) ?></td>
                                <td><?= htmlspecialchars($report['submitted_by']) ?></td>
                                <td><?= htmlspecialchars($report['submitted_at']) ?></td>
                                <td>
                                    <?php if ($report['file_path']) : ?>
                                        <a style="color:var(--color2); text-decoration:none;" href="<?= htmlspecialchars($report['file_path']) ?>" target="_blank" class="btn-view">View</a>
                                    <?php else : ?>
                                        <span class="muted">No file</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr class="no-data">
                                <td colspan="8" class="muted">No reports found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
include_once __DIR__ . '/../../../auth/session.php';
include_once __DIR__ . '/../classes/Approval.php';
include_once __DIR__ . '/../classes/Department.php';

$approvalClass = new Approval();
$approvals = $approvalClass->getApprovals();

$departmentClass = new Department();
$departments = $departmentClass->getAllDepartments();
?>

<div class="approval-module">
    <div class="module-header">
        <h1>Approval & Decision Support</h1>
        <p>Simple approval queue for submitted requests and reports — approve or reject with optional remarks.</p>
    </div>

    <div class="module-content">

        <div class="approval-section-header">
            <h3>Approval Queue</h3>

            <div class="approval-header-actions">
                <div class="approval-filter">
                    <select id="department-filter" class="ads-select">
                        <option value="">All Departments</option>
                        <?php foreach ($departments as $dept) : ?>
                            <option value="<?= htmlspecialchars($dept['department_id']) ?>">
                                <?= htmlspecialchars($dept['department_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="button" id="approval-open-modal" class="approval-add-btn">
                    + Submit for Approval
                </button>
            </div>
        </div>

        <div class="approval-queue-section">
            <div class="ads-queue">
                <div class="table-responsive">
                    <table class="ads-table">
                        <thead>
                            <tr>
                                <th>Approval ID</th>
                                <th>Title</th>
                                <th>Submitted By</th>
                                <th>Department</th>
                                <th>Decided By</th>
                                <th>Decision</th>
                                <th class="actions-header">Actions</th>
                                <th class="attachment-header">Attachment</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (!empty($approvals)) : ?>
                                <?php foreach ($approvals as $approval) : ?>
                                    <tr data-approval-id="<?= htmlspecialchars($approval['approval_id']) ?>">
                                        <td><?= htmlspecialchars($approval['approval_id']) ?></td>
                                        <td><?= htmlspecialchars($approval['title'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($approval['submit_by'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($approval['department_name'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($approval['approver_id'] ?? 'N/A') ?></td>
                                        <td>
                                            <span class="badge badge-<?= htmlspecialchars($approval['decision'] ?? 'pending') ?>">
                                                <?= htmlspecialchars($approval['decision'] ?? 'N/A') ?>
                                            </span>
                                        </td>
                                        <td class="actions-cell">
                                            <div class="actions">
                                                <button type="button" class="btn-approve" data-id="<?= htmlspecialchars($approval['approval_id']) ?>">
                                                    Approve
                                                </button>
                                                <button type="button" class="btn-reject" data-id="<?= htmlspecialchars($approval['approval_id']) ?>">
                                                    Reject
                                                </button>
                                            </div>
                                        </td>
                                        <td class="attachment-cell">
                                            <?php if (!empty($approval['file_path'])) : ?>
                                                <a href="<?= htmlspecialchars($approval['file_path']) ?>" target="_blank" class="attachment-link">
                                                    View
                                                </a>
                                            <?php else : ?>
                                                <span class="muted">No file</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr class="no-data">
                                    <td colspan="8" class="muted">No approvals found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Submission Modal -->
    <div class="approval-modal-overlay" id="approval-modal-overlay">
        <div class="approval-modal" role="dialog" aria-modal="true" aria-labelledby="approval-modal-title">
            <div class="approval-modal-header">
                <h3 id="approval-modal-title">Submit for Approval</h3>
                <button type="button" class="approval-modal-close" id="approval-modal-close" aria-label="Close">&times;</button>
            </div>

            <div class="approval-modal-body">
                <div class="approval-upload">
                    <form id="approval-upload-form" enctype="multipart/form-data" data-skip>
                        <div class="approval-form-group">
                            <label for="approval-title">Title</label>
                            <input type="text" id="approval-title" name="title" required>
                        </div>

                        <div class="approval-form-group">
                            <label for="approval-attachment">Attachment (optional)</label>
                            <input class="file-btn" type="file" id="approval-attachment" name="attachment">
                        </div>

                        <div class="approval-form-group">
                            <button type="submit" class="approval-submit-btn" id="approval-submit-btn">
                                Submit for Approval
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
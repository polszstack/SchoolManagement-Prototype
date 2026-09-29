<?php
include_once __DIR__ . '../classes/Approval.php';
include_once __DIR__ . '../classes/Department.php';

$approvalClass = new Approval();
$approvals = $approvalClass->getApprovals();

$departmentClass = new Department();
$departments = $departmentClass->getAllDepartments();
?>

<div class="module-header">
    <h1>Approval Submission</h1>
</div>

<div class="module-content">
    <div class="approval-submission">
        <div class="approval-upload">
            <h3>Submit for Approval</h3>
            <form id="approval-upload-form" enctype="multipart/form-data" data-skip>
                <div class="form-group">
                    <label for="approval-title">Title</label>
                    <input type="text" id="approval-title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="approval-attachment">Attachment (optional)</label>
                    <input class="file-btn" type="file" id="approval-attachment" name="attachment">
                </div>
                <div class="form-group">
                    <button type="submit" class="approval-submit-btn" id="approval-submit-btn">
                        Submit for Approval
                    </button>
                </div>
            </form>    
        </div>
    </div>

    <div class="form-section">
        <div class="form-section-header">
            <h3 style="margin: 0;color: var(--color5); font-size: 1.25rem;">Approval Queue</h3>
            <div class="ads-filter">
            </div>
        </div>

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
                            <th>Attachment</th>
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
                                    <td>
                                        <?php if ($approval['file_path']) : ?>
                                            <a style="color:var(--color2); text-decoration:none; border:1px solid var(--color4); padding:4px 8px; border-radius:4px;" href="<?= htmlspecialchars($approval['file_path']) ?>" target="_blank">View</a>
                                        <?php else : ?>
                                            <span class="muted">No file</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr class="no-data">
                                <td colspan="10" class="muted">No approvals found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
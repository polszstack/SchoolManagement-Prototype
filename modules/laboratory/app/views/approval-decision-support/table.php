<?php require __DIR__ . '/../partials/notif.php' ?>

<table class="table table-striped table-hover align-middle text-[12px]">
    <thead class="table-dark text-nowrap">
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Submitted By</th>
            <th>Department</th>
            <th>Decided By</th>
            <th>Status</th>
            <th>Attachment</th>
            <th>Approved At</th>
            <th>Decision</th>
            <th>Remarks</th>
            <th class="text-center">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($approvals)) : ?>
            <?php foreach ($approvals as $approval) : ?>
                <tr data-approval-id="<?= htmlspecialchars($approval['approval_id']) ?>">
                    <td class="text-nowrap"><?= htmlspecialchars($approval['approval_id']) ?></td>
                    <td><strong><?= htmlspecialchars($approval['title'] ?? 'N/A') ?></strong></td>
                    <td><?= htmlspecialchars($approval['submit_by'] ?? 'N/A') ?></td>
                    <td>
                        <span class="badge bg-secondary">
                            <?= htmlspecialchars($approval['department_name'] ?? 'N/A') ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($approval['approver_id'] ?? '-') ?></td>
                    <td>
                        <?php
                        $decision = strtolower($approval['decision'] ?? 'pending');
                        $badgeClass = match ($decision) {
                            'approved' => 'bg-success',
                            'rejected' => 'bg-danger',
                            default => 'bg-warning text-dark'
                        };
                        ?>
                        <span class="badge <?= $badgeClass ?>"><?= ucfirst($decision) ?></span>
                    </td>
                    <td>
                        <?php if (!empty($approval['file_path'])): ?>
                            <a href="<?= BASE_URL . '/public/' . ltrim($approval['file_path'], '/') ?>"
                                class="btn btn-sm btn-outline-primary" target="_blank">
                                View File
                            </a>
                        <?php else: ?>
                            <span class="text-muted">No file</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center text-nowrap">
                        <?php if (($approval['decision'] ?? '') === 'Rejected'): ?>
                            Rejected
                        <?php else: ?>
                            <?= !empty($approval['approved_at']) ? date('M d, Y', strtotime($approval['approved_at'])) : '-' ?>
                        <?php endif; ?>
                    </td>
                    <td class="text-center text-nowrap">
                        <?= ucfirst($approval['decision'] ?? '-') ?>
                    </td>
                    <td class="text-center text-nowrap">
                        <?= htmlspecialchars($approval['remarks'] ?? '-') ?>
                    </td>
                    <td class="text-center">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-success btn-sm btn-decision"
                                data-id="<?= $approval['approval_id'] ?>" data-action="approved">
                                Approve
                            </button>
                            <button type="button" class="btn btn-danger btn-sm btn-decision"
                                data-id="<?= $approval['approval_id'] ?>" data-action="rejected">
                                Reject
                            </button>
                            <form method="POST" action="approval-decision-support/delete" class="d-inline">
                                <input type="hidden" name="approval_id" value="<?= htmlspecialchars($approval['approval_id']) ?>">
                                <button type="submit" class="btn btn-secondary btn-sm">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="11" class="text-center text-muted">No approvals found</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
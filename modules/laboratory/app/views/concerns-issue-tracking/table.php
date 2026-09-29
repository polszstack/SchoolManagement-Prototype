<?php require __DIR__ . '/../partials/notif.php' ?>

<table class="table table-bordered table-hover align-middle text-[12px] concern-table">
    <thead class="table-dark text-nowrap">
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Department</th>
            <th>Submitted By</th>
            <th>Submitted On</th>
            <th>Status</th>
            <th>Attachment</th>
            <th class="text-center">Actions</th>
        </tr>
    </thead>
    <tbody id="concernTableBody">
        <?php if (!empty($issues)) : ?>
            <?php foreach ($issues as $issue) : ?>
                <tr>
                    <td><?= htmlspecialchars($issue['issue_id']) ?></td>
                    <td>
                        <strong><?= htmlspecialchars($issue['title']) ?></strong>
                    </td>
                    <td>
                        <span class="badge bg-secondary">
                            <?= htmlspecialchars($issue['department_name'] ?? 'N/A') ?>
                        </span>
                    </td>
                    <td>
                        <?= htmlspecialchars($issue['submitted_by_name'] ?? 'N/A') ?>
                    </td>
                    <td class="text-nowrap">
                        <?= date('M d, Y', strtotime($issue['submitted_on'])) ?>
                    </td>
                    <td>
                        <?php
                        $status = strtolower($issue['status']);
                        $badgeClass = $status === 'resolved' ? 'bg-success' : 'bg-warning text-dark';
                        ?>
                        <span class="badge <?= $badgeClass ?>">
                            <?= ucfirst($status) ?>
                        </span>
                    </td>
                    <td>
                        <?php if (!empty($issue['file_path'])): ?>
                            <a href="http://localhost/SchoolManagementSystem/Modules/laboratory/public/<?= ltrim($issue['file_path'], '/') ?>"
                                class="btn btn-sm btn-outline-primary"
                                target="_blank">
                                View
                            </a>
                        <?php else: ?>
                            <span class="text-muted">No file</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <?php if ($issue['status'] !== 'resolved'): ?>
                            <form method="POST" action="concern-issue-tracking/resolve">
                                <input type="hidden" name="issue_id" value="<?= $issue['issue_id'] ?>">
                                <input type="hidden" name="status" value="resolved">
                                <button type="submit" class="btn btn-sm btn-success">Resolve</button>
                            </form>
                        <?php else: ?>
                            <span class="badge bg-success">Resolved</span>
                        <?php endif; ?>
                        <form method="POST" action="concern-issue-tracking/delete" class="d-inline">
                            <input type="hidden" name="issue_id" value="<?= htmlspecialchars($issue['issue_id']) ?>">
                            <button type="submit" class="btn btn-sm btn-danger mt-2">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="8" class="text-center text-muted py-3">
                    No records found
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
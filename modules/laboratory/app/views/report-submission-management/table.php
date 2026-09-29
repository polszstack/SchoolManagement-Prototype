<?php require __DIR__ . '/../partials/notif.php' ?>

<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle text-sm rsm-table">
        <thead class="table-dark text-nowrap">
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Type</th>
                <th>Description</th>
                <th>Department</th>
                <th>Submitted By</th>
                <th>Submitted At</th>
                <th>File</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($reports)) : ?>
                <?php foreach ($reports as $report) : ?>
                    <tr>
                        <td><?= htmlspecialchars($report['report_id']) ?></td>
                        <td><?= htmlspecialchars($report['title']) ?></td>
                        <td><?= htmlspecialchars($report['report_type'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($report['description'] ?? 'N/A') ?></td>
                        <td>
                            <span class="badge bg-secondary">
                                <?= htmlspecialchars($report['department_name'] ?? 'N/A') ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($report['submitted_by'] ?? 'N/A') ?></td>
                        <td class="text-nowrap"><?= date('M d, Y', strtotime($report['submitted_at'])) ?></td>
                        <td class="text-center">
                            <?php if (!empty($report['file_path'])): ?>
                                <a href="http://localhost/SchoolManagementSystem/Modules/laboratory/public/<?= ltrim($report['file_path'], '/') ?>"
                                    class="btn btn-sm btn-outline-primary"
                                    target="_blank">
                                    View
                                </a>
                            <?php else: ?>
                                <span class="text-muted">No file</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="POST" action="report-submission-management/delete" class="d-inline">
                                <input type="hidden" name="report_id" value="<?= $report['report_id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this report?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="8" class="text-center text-muted py-3">
                        No reports found.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<div class="modal fade" id="createApprovalDSModal" tabindex="-1" aria-labelledby="createApprovalDSLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="createApprovalDSLabel">➕ Create Approval</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="approval-upload-form" enctype="multipart/form-data" method="POST" action="approval-decision-support/create">
                    <div class="mb-3">
                        <label for="approval-title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="approval-title" name="title" required>
                    </div>

                    <div class="mb-3">
                        <label for="approval-description" class="form-label">Description</label>
                        <textarea class="form-control" id="approval-description" name="description" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="approval-department" class="form-label">Department</label>
                        <select class="form-select" id="approval-department" name="department" required>
                            <option value="">Select Department</option>
                            <?php foreach ($departments as $dept) : ?>
                                <option value="<?= $dept['department_id'] ?>">
                                    <?= htmlspecialchars($dept['department_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="approval-approver" class="form-label">Select Approver (optional)</label>
                        <select class="form-select" id="approval-approver" name="approver_id">
                            <option value="">-- None --</option>
                            <?php foreach ($employees as $emp): ?>
                                <option value="<?= $emp['employee_id'] ?>">
                                    <?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="approval-submitter" class="form-label">Submit By (optional)</label>
                        <select class="form-select" name="submit_by">
                            <option value="">-- None --</option>
                            <?php foreach ($employees as $emp): ?>
                                <option value="<?= $emp['employee_id'] ?>" <?= ($_SESSION['employee_id'] ?? 0) == $emp['employee_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="approval-attachment" class="form-label">Attachment (optional)</label>
                        <input type="file" class="form-control" id="approval-attachment" name="attachment">
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Submit for Approval</button>
                </form>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="createconcernsITModal" tabindex="-1" aria-labelledby="createconcernsITLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="createconcernsITLabel">➕ Create Concern</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="concern-log-form" method="POST" enctype="multipart/form-data"
                    action="concern-issue-tracking/create">

                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control"
                            placeholder="Short summary of the concern" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"
                            placeholder="Detailed description" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Department</label>
                        <select class="form-select" name="department" required>
                            <option value="">Select Department</option>
                            <?php foreach ($departments as $dept) : ?>
                                <option value="<?= $dept['department_id'] ?>">
                                    <?= htmlspecialchars($dept['department_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Submitted By</label>
                        <select class="form-select" name="submitted_by">
                            <?php foreach ($employees as $emp): ?>
                                <option value="<?= $emp['employee_id'] ?>"
                                    <?= ($_SESSION['employee_id'] ?? 0) == $emp['employee_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Attachment <span class="text-muted">(optional)</span>
                        </label>
                        <input type="file" class="form-control" name="attachment"
                            accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg">
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            Submit Concern
                        </button>
                        <button type="reset" class="btn btn-outline-secondary w-100">
                            Clear
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="createconcernsITModal" tabindex="-1" aria-labelledby="createconcernsITLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">➕ Create Report</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form method="POST" enctype="multipart/form-data" action="report-submission-management/create">

                    <div class="row g-3">

                        <div class="col-12">
                            <label class="form-label fw-semibold">Title</label>
                            <input type="text" name="title" class="form-control"
                                placeholder="Enter report title" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="3"
                                placeholder="Enter report details" required></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Department</label>
                            <select class="form-select" name="department" required>
                                <option value="">Select Department</option>
                                <?php foreach ($departments as $dept) : ?>
                                    <option value="<?= $dept['department_id'] ?>">
                                        <?= htmlspecialchars($dept['department_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Report Type</label>
                            <select class="form-select" name="report_type" required>
                                <option value="">Select Report Type</option>
                                <?php foreach ($reportTypes as $type): ?>
                                    <option value="<?= $type['type_id'] ?>">
                                        <?= htmlspecialchars($type['report_type']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Submitted By</label>
                            <select class="form-select" name="submitted_by" required>
                                <?php foreach ($employees as $emp): ?>
                                    <option value="<?= $emp['employee_id'] ?>"
                                        <?= ($_SESSION['employee_id'] ?? 0) == $emp['employee_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Attachment <span class="text-muted">(optional)</span>
                            </label>
                            <input type="file" name="attachment" class="form-control"
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg">
                        </div>

                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary px-4">
                            Submit Report
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
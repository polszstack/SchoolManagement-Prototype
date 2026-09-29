<div class="module-header mb-3">
    <h1 class="mb-1 text-[28px]">Report Submissions & Management</h1>
    <p class="text-muted mb-0" style="font-size: 13px;">
        View and manage submitted reports from teachers and staff.
    </p>
</div>

<div class="module-content">
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <form method="GET" class="d-inline-block">
                        <select name="department" id="rsm-filter" class="form-select form-select-sm" style="width: 150px;" onchange="this.form.submit()">
                            <option value="">All Departments</option>
                            <?php foreach ($departments as $dept) : ?>
                                <option value="<?= $dept['department_id'] ?>" <?= (isset($_GET['department']) && $_GET['department'] == $dept['department_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($dept['department_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>

                    <input class="form-control form-control-sm"
                        id="rsm-search"
                        type="search"
                        placeholder="Search by title or submitter"
                        style="width: 200px;">
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createconcernsITModal">
                        ➕ Create
                    </button>
                </div>
            </div>
            <?php require __DIR__ . '/table.php' ?>
        </div>
    </div>
</div>
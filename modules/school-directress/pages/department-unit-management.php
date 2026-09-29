<?php
include_once __DIR__ . '/../../../auth/session.php';
include_once __DIR__ . '/../classes/User.php';
include_once __DIR__ . '/../classes/Department.php';

$user = new User();
$user_info = $user->userSession();

$department = new Department();
$departmentsWithDetails = $department->getDepartmentsWithDetails();
?>

<div class="module-header">
    <h1>Department & Unit Management</h1>
    <p>Add and manage departments, units, and staff assignments within the system.</p>
</div>

<div class="module-content dept-module">
    <div class="tab-container">
        <ul class="tab-list">
            <li class="tab-item active" data-tab="departments">Departments</li>
            <li class="tab-item" data-tab="add-department">Add Department</li>
            <li class="tab-item" data-tab="assign-department-head">Assign Department Head</li>
        </ul>

        <div class="tab-content active" id="departments">
            <div class="dept-card">
                <div class="dept-table-wrapper">
                    <table class="dept-table">
            <div class="table-responsive">
                <div class="form-section">
                    <table class="department-table">
                        <thead>
                            <tr>
                                <th>Department Name</th>
                                <th>Department Head</th>
                                <th>Number of Department Members</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($departmentsWithDetails)): ?>
                                <?php foreach ($departmentsWithDetails as $dept): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($dept['department_name']) ?></td>
                                        <td>
                                            <?= !empty($dept['department_head_name'])
                                                ? htmlspecialchars($dept['department_head_name'])
                                                : '-' ?>
                                        </td>
                                        <td><?= htmlspecialchars($dept['employee_count']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="dept-empty">No departments found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="tab-content" id="add-department">
            <div class="dept-card dept-form-card">
                <h3>Add Department</h3>

                <form id="add-department-form" data-skip class="dept-form">
                    <div id="add-dept-banner" class="dept-banner" style="display:none;"></div>

                    <div class="dept-form-group">
                        <label for="dept-name">Department Name</label>
                        <input type="text" id="dept-name" name="dept-name" placeholder="Enter department name">
                    </div>

                    <div class="dept-form-group">
                        <label for="dept-desc">Description</label>
                        <textarea id="dept-desc" name="dept-desc" rows="3" placeholder="Enter department description"></textarea>
                    </div>

                    <div class="dept-form-actions">
                        <button type="submit" class="dept-btn-save">Save Department</button>
                        <button type="button" class="dept-btn-cancel">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="tab-content" id="assign-department-head">
            <div class="dept-card dept-form-card">
                <h3>Assign Department Head</h3>

                <form id="assign-head-form" data-skip class="dept-form">
                    <div id="assign-head-banner" class="dept-banner" style="display:none;"></div>

                    <div class="dept-form-group">
                        <label for="dept-select">Select Department</label>
                        <select id="dept-select" name="dept-select">
                            <option value="">-- Select Department --</option>
                            <?php foreach ($departmentsWithDetails as $dept): ?>
                                <option value="<?= htmlspecialchars($dept['department_id']) ?>">
                                    <?= htmlspecialchars($dept['department_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="dept-form-group">
                        <label for="employee-select">Select Employee</label>
                        <select id="employee-select" name="employee-select" disabled>
                            <option value="">-- Select a department first --</option>
                        </select>
                        <span id="employee-load-status" class="dept-form-hint"></span>
                    </div>

                    <div class="dept-form-actions">
                        <button type="submit" id="btn-assign-head" class="dept-btn-save" disabled>
                            Assign as Head
                        </button>
                        <button type="button" class="dept-btn-cancel">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
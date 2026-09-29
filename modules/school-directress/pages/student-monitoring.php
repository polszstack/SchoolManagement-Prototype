<?php
include_once __DIR__ . '/../../../auth/session.php';
include_once __DIR__ . '/../classes/StudentMonitoring.php';

$model = new StudentMonitoring();
$data  = $model->getDashboardData();

$students = $data['students'];
$courses  = $data['courses'];
$error    = $data['error'];
?>

<div class="module-header">
    <h1>Student Monitoring</h1>
    <p>Track and manage student information and status</p>
</div>

<div class="module-content">

    <?php if ($error): ?>
    <div class="alert error" style="margin-bottom:1.5rem;">
        Database unavailable — <?= htmlspecialchars(substr($error, 0, 120)) ?>
    </div>
    <?php endif; ?>

    <!-- ── Toolbar: search + filters + export ── -->
    <div class="sm-toolbar">
        <div class="sm-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" id="smSearch" placeholder="Search by name, student no., email…" autocomplete="off">
        </div>

        <select class="sm-sel" id="smStatus">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="graduated">Graduated</option>
        </select>

        <select class="sm-sel" id="smCourse">
            <option value="">All Courses</option>
            <?php foreach ($courses as $c): ?>
            <option value="<?= htmlspecialchars($c) ?>"><?= htmlspecialchars($c) ?></option>
            <?php endforeach; ?>
        </select>

        <select class="sm-sel" id="smYear">
            <option value="">All Year Levels</option>
            <?php for ($y = 1; $y <= 5; $y++): ?>
            <option value="<?= $y ?>"><?= StudentMonitoring::yearLabel($y) ?></option>
            <?php endfor; ?>
        </select>

        <button type="button" id="smExportBtn" class="sm-btn-primary">Export CSV</button>
    </div>

    <!-- ── Table ── -->
    <div class="sm-table-wrap">
        <table class="sm-table">
            <thead>
                <tr>
                    <th></th>
                    <th>Student</th>
                    <th>Student No.</th>
                    <th>Course</th>
                    <th>Status</th>
                    <th>Year</th>
                    <th>Section</th>
                    <th>Enrolled</th>
                </tr>
            </thead>
            <tbody id="smBody">

            <?php if (empty($students)): ?>
            <tr><td colspan="8">
                <div class="sm-empty">No students found.</div>
            </td></tr>

            <?php else: foreach ($students as $s):
                $name = StudentMonitoring::fullName($s);
                $sm   = StudentMonitoring::statusMeta($s['academic_status']);
                $age  = StudentMonitoring::age($s['birth_date']);
                $rid  = 'smd-' . $s['student_number'];
            ?>
            <!-- DATA ROW -->
            <tr class="sm-row"
                data-name="<?= htmlspecialchars(strtolower($name)) ?>"
                data-snum="<?= htmlspecialchars(strtolower((string) $s['student_number'])) ?>"
                data-email="<?= htmlspecialchars(strtolower($s['email'] ?? '')) ?>"
                data-status="<?= htmlspecialchars($s['academic_status']) ?>"
                data-course="<?= htmlspecialchars($s['course'] ?? '') ?>"
                data-year="<?= (int) $s['year_level'] ?>"
                data-sex="<?= htmlspecialchars($s['gender'] ?? '') ?>"
                onclick="smToggle(this,'<?= $rid ?>')"
            >
                <td><div class="sm-toggle">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m6 9 6 6 6-6"/></svg>
                </div></td>

                <td>
                    <div class="sm-student">
                        <div class="sm-avatar <?= $s['gender'] === 'female' ? 'sm-avatar--f' : '' ?>"><?= StudentMonitoring::initials($s) ?></div>
                        <div>
                            <div class="sm-name"><?= htmlspecialchars($name) ?></div>
                            <div class="sm-meta"><?= htmlspecialchars(ucfirst($s['gender'] ?? '—')) ?> · <?= htmlspecialchars($s['email'] ?? '—') ?></div>
                        </div>
                    </div>
                </td>

                <td><span class="sm-mono"><?= htmlspecialchars($s['student_number']) ?></span></td>

                <td><?= htmlspecialchars($s['course'] ?? '—') ?></td>

                <td><span class="sm-badge <?= $sm['cls'] ?>"><?= $sm['label'] ?></span></td>

                <td><span class="sm-year"><?= StudentMonitoring::yearLabel($s['year_level']) ?></span></td>

                <td>
                    <?php if (!empty($s['section'])): ?>
                    <span class="sm-section-tag"><?= htmlspecialchars($s['section']) ?></span>
                    <?php else: ?>
                    <span class="sm-unassigned">Unassigned</span>
                    <?php endif; ?>
                </td>

                <td style="font-size:0.8rem;color:var(--color5);white-space:nowrap;">
                    <?= $s['created_at'] ? date('M j, Y', strtotime($s['created_at'])) : '—' ?>
                </td>
            </tr>

            <!-- DETAIL ROW -->
            <tr class="sm-detail-row" id="<?= $rid ?>">
                <td class="sm-detail-cell" colspan="8">
                    <div class="sm-detail-inner">
                        <div>
                            <div class="sm-fl__k">Phone</div>
                            <div class="sm-fl__v"><?= htmlspecialchars($s['phone'] ?? '—') ?></div>
                        </div>
                        <div>
                            <div class="sm-fl__k">Birth Date</div>
                            <div class="sm-fl__v">
                                <?= $s['birth_date'] ? date('F j, Y', strtotime($s['birth_date'])) : '—' ?>
                                <?php if ($age !== null): ?>
                                <span style="color:var(--color5);font-size:0.72rem;"> (<?= $age ?> yrs old)</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div>
                            <div class="sm-fl__k">Address</div>
                            <div class="sm-fl__v"><?= htmlspecialchars($s['address'] ?? '—') ?></div>
                        </div>
                        <div>
                            <div class="sm-fl__k">Academic Status</div>
                            <div class="sm-fl__v"><?= htmlspecialchars(ucfirst($s['academic_status'])) ?></div>
                        </div>
                        <?php if ($s['academic_status'] === 'graduated' && $s['graduated_at']): ?>
                        <div>
                            <div class="sm-fl__k">Graduated On</div>
                            <div class="sm-fl__v"><?= date('F j, Y', strtotime($s['graduated_at'])) ?></div>
                        </div>
                        <?php endif; ?>
                        <div>
                            <div class="sm-fl__k">Record Created</div>
                            <div class="sm-fl__v"><?= $s['created_at'] ? date('F j, Y', strtotime($s['created_at'])) : '—' ?></div>
                        </div>
                        <div>
                            <div class="sm-fl__k">Last Updated</div>
                            <div class="sm-fl__v"><?= $s['updated_at'] ? date('F j, Y', strtotime($s['updated_at'])) : '—' ?></div>
                        </div>
                    </div>
                </td>
            </tr>

            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <!-- ── Footer: "Showing X-Y of Z" + numbered pagination ── -->
    <div class="sm-footer">
        <span class="sm-showing" id="smShowing"></span>
        <div class="sm-page-nav" id="smPageNav"></div>
    </div>

</div>
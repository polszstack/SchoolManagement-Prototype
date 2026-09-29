<?php
/**
 * appointments.php
 * Module: Appointment Management (counselor-facing side)
 *
 * Renders server-side directly from the Appointments model, same pattern
 * as students.php / cases.php. AppointmentsController.php (AJAX) is only
 * used afterward — for filtering, daily/weekly view switching, booking,
 * and status actions (approve/reject/reschedule/complete/no-show/remarks).
 *
 * NOTE: student-facing features (request appointment, cancel, view own
 * status/history) are intentionally NOT built here — those belong to a
 * separate student portal per the project owner's confirmation.
 */

include_once __DIR__ . '/../../../auth/session.php';
include_once __DIR__ . '/../classes/Appointments.php';

$appointmentsClass = new Appointments();

$view = $_GET['view'] ?? 'daily';
$date = $_GET['date'] ?? date('Y-m-d');

$d = new DateTime($date);
if ($view === 'weekly') {
    $dayOfWeek = (int) $d->format('N');
    $monday = (clone $d)->modify('-' . ($dayOfWeek - 1) . ' days');
    $sunday = (clone $monday)->modify('+6 days');
    $dateFrom = $monday->format('Y-m-d 00:00:00');
    $dateTo   = $sunday->format('Y-m-d 23:59:59');
} else {
    $dateFrom = $d->format('Y-m-d 00:00:00');
    $dateTo   = $d->format('Y-m-d 23:59:59');
}

$filters = [
    'search'       => trim($_GET['search'] ?? ''),
    'status'       => $_GET['status'] ?? '',
    'meeting_type' => $_GET['meeting_type'] ?? '',
    'counselor_id' => $_GET['counselor_id'] ?? '',
    'date_from'    => $dateFrom,
    'date_to'      => $dateTo,
];
$page     = max(1, (int) ($_GET['page'] ?? 1));
$pageSize = 10;

$result           = $appointmentsClass->getList($filters, $page, $pageSize);
$appointments     = $result['rows'];
$totalAppointments = $result['total'];
$totalPages       = (int) ceil($totalAppointments / $pageSize);

$counselors = $appointmentsClass->getCounselors();
$openCases  = $appointmentsClass->getOpenCases();

function apt_status_badge_class($status) {
    return match ($status) {
        'Pending' => 'apt-badge--pending',
        'Approved' => 'apt-badge--approved',
        'Completed' => 'apt-badge--completed',
        'No Show' => 'apt-badge--noshow',
        default => 'apt-badge--cancelled',
    };
}
?>

<div class="module-header">
    <h1>Appointments</h1>
</div>

<div class="module-content">

    <!-- Toolbar -->
    <div class="apt-toolbar">
        <div class="apt-toolbar__filters">
            <div class="apt-search-box">
                <i class="fa fa-search"></i>
                <input type="text" id="aptSearchInput" placeholder="Search by student..." value="<?= htmlspecialchars($filters['search']) ?>">
            </div>

            <input type="date" class="apt-filter-date" id="aptDateInput" value="<?= htmlspecialchars($date) ?>">

            <div class="apt-view-toggle" id="aptViewToggle">
                <button type="button" data-view="daily" class="<?= $view === 'daily' ? 'apt-view-toggle--active' : '' ?>">Daily</button>
                <button type="button" data-view="weekly" class="<?= $view === 'weekly' ? 'apt-view-toggle--active' : '' ?>">Weekly</button>
            </div>

            <select class="apt-filter-select" id="aptFilterStatus">
                <option value="" <?= $filters['status'] === '' ? 'selected' : '' ?>>All Status</option>
                <option value="Pending" <?= $filters['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                <option value="Approved" <?= $filters['status'] === 'Approved' ? 'selected' : '' ?>>Approved</option>
                <option value="Completed" <?= $filters['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                <option value="Cancelled" <?= $filters['status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                <option value="No Show" <?= $filters['status'] === 'No Show' ? 'selected' : '' ?>>No Show</option>
            </select>

            <select class="apt-filter-select" id="aptFilterMeetingType">
                <option value="" <?= $filters['meeting_type'] === '' ? 'selected' : '' ?>>All Types</option>
                <option value="Face-to-Face" <?= $filters['meeting_type'] === 'Face-to-Face' ? 'selected' : '' ?>>Face-to-Face</option>
                <option value="Online" <?= $filters['meeting_type'] === 'Online' ? 'selected' : '' ?>>Online</option>
            </select>

            <select class="apt-filter-select" id="aptFilterCounselor">
                <option value="">All Counselors</option>
                <?php foreach ($counselors as $c): ?>
                    <option value="<?= htmlspecialchars($c['employee_id']) ?>" <?= $filters['counselor_id'] == $c['employee_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="apt-toolbar__actions">
            <button type="button" class="apt-btn" id="aptBookBtn">+ Book Appointment</button>
        </div>
    </div>

    <!-- Appointment list -->
    <div class="apt-table-wrapper">
        <table class="apt-table">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Student</th>
                    <th>Counselor</th>
                    <th>Type</th>
                    <th>Purpose</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($appointments)): ?>
                <tr><td colspan="6" class="apt-table__empty">No appointments found for the selected filters.</td></tr>
                <?php else: ?>
                    <?php foreach ($appointments as $a): ?>
                    <tr class="apt-row" data-appointment-id="<?= htmlspecialchars($a['appointment_id']) ?>">
                        <td class="apt-time"><?= date('g:i A', strtotime($a['appointment_date'])) ?><br><span class="apt-student-sub"><?= date('M d', strtotime($a['appointment_date'])) ?></span></td>
                        <td>
                            <div class="apt-student-name"><?= htmlspecialchars($a['student_name']) ?></div>
                            <div class="apt-student-sub">#<?= htmlspecialchars($a['student_number']) ?></div>
                        </td>
                        <td><?= htmlspecialchars($a['counselor_name']) ?></td>
                        <td><span class="apt-badge apt-badge--meeting-type"><?= htmlspecialchars($a['meeting_type']) ?></span></td>
                        <td><?= htmlspecialchars($a['purpose']) ?></td>
                        <td><span class="apt-badge <?= apt_status_badge_class($a['status']) ?>"><?= htmlspecialchars($a['status']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="apt-pagination">
        <span>
            <?php if ($totalAppointments > 0): ?>
                Showing <?= (($page - 1) * $pageSize) + 1 ?>-<?= min($page * $pageSize, $totalAppointments) ?> of <?= $totalAppointments ?> appointments
            <?php else: ?>
                No appointments found
            <?php endif; ?>
        </span>
        <div class="apt-pagination__pages">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <button class="apt-pagination__page <?= $i === $page ? 'apt-pagination__page--active' : '' ?>" data-page="<?= $i ?>"><?= $i ?></button>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
                <button class="apt-pagination__page" data-page="<?= $page + 1 ?>">&rsaquo;</button>
            <?php endif; ?>
        </div>
    </div>

</div>

<!-- ============================================================
     Appointment Detail / Action Modal (populated via AJAX on row click)
     ============================================================ -->
<div class="apt-modal-overlay" id="aptDetailModal">
    <div class="apt-modal">
        <div class="apt-modal__header">
            <h3>Appointment Details</h3>
            <button type="button" class="apt-modal__close" id="aptDetailCloseBtn">&times;</button>
        </div>
        <div class="apt-modal__body">
            <div class="apt-field-grid">
                <div><div class="apt-field-label">Student</div><div class="apt-field-value" data-field="student_name"></div></div>
                <div><div class="apt-field-label">Counselor</div><div class="apt-field-value" data-field="counselor_name"></div></div>
                <div><div class="apt-field-label">Date &amp; Time</div><div class="apt-field-value" data-field="appointment_date_display"></div></div>
                <div><div class="apt-field-label">Meeting Type</div><div class="apt-field-value" data-field="meeting_type"></div></div>
                <div><div class="apt-field-label">Status</div><div class="apt-field-value" data-field="status"></div></div>
                <div><div class="apt-field-label">Requested On</div><div class="apt-field-value" data-field="created_at_display"></div></div>
            </div>
            <div class="apt-form-group">
                <label>Purpose</label>
                <div class="apt-field-value" data-field="purpose"></div>
            </div>
            <div class="apt-form-group">
                <label>Remarks</label>
                <textarea id="aptRemarksInput" placeholder="Add remarks about this appointment..."></textarea>
            </div>
        </div>
        <div class="apt-modal__footer" style="flex-direction: column; align-items: stretch;">
            <div class="apt-modal__actions-row" id="aptActionButtons"></div>
            <div style="display:flex; justify-content:flex-end; gap:8px;">
                <button type="button" class="apt-btn apt-btn--ghost apt-btn--sm" id="aptSaveRemarksBtn">Save Remarks</button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================
     Reschedule Modal
     ============================================================ -->
<div class="apt-modal-overlay" id="aptRescheduleModal">
    <div class="apt-modal">
        <div class="apt-modal__header">
            <h3>Reschedule Appointment</h3>
            <button type="button" class="apt-modal__close" id="aptRescheduleCloseBtn">&times;</button>
        </div>
        <form id="aptRescheduleForm" data-skip>
            <div class="apt-modal__body">
                <div class="apt-form-group">
                    <label>New Date &amp; Time</label>
                    <input type="datetime-local" name="appointment_date" required>
                </div>
            </div>
            <div class="apt-modal__footer">
                <button type="button" class="apt-btn apt-btn--ghost apt-btn--sm" id="aptRescheduleCancelBtn">Cancel</button>
                <button type="submit" class="apt-btn apt-btn--sm">Confirm Reschedule</button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================
     Reject Modal (reason -> stored in remarks)
     ============================================================ -->
<div class="apt-modal-overlay" id="aptRejectModal">
    <div class="apt-modal">
        <div class="apt-modal__header">
            <h3>Reject Appointment</h3>
            <button type="button" class="apt-modal__close" id="aptRejectCloseBtn">&times;</button>
        </div>
        <form id="aptRejectForm" data-skip>
            <div class="apt-modal__body">
                <div class="apt-form-group">
                    <label>Reason</label>
                    <textarea name="reason" placeholder="Why is this appointment being rejected..." required></textarea>
                </div>
            </div>
            <div class="apt-modal__footer">
                <button type="button" class="apt-btn apt-btn--ghost apt-btn--sm" id="aptRejectCancelBtn">Cancel</button>
                <button type="submit" class="apt-btn apt-btn--danger apt-btn--sm">Reject</button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================
     Book Appointment Modal
     ============================================================ -->
<div class="apt-modal-overlay" id="aptBookModal">
    <div class="apt-modal">
        <div class="apt-modal__header">
            <h3>Book Appointment</h3>
            <button type="button" class="apt-modal__close" id="aptBookCloseBtn">&times;</button>
        </div>
        <form id="aptBookForm" data-skip>
            <div class="apt-modal__body">
                <div class="apt-form-group">
                    <label>Case (optional — link this appointment to an existing case)</label>
                    <select name="case_id" id="aptBookCase">
                        <option value="">No case (general appointment)</option>
                        <?php foreach ($openCases as $c): ?>
                            <option value="<?= htmlspecialchars($c['case_id']) ?>"
                                    data-student="<?= htmlspecialchars($c['student_number']) ?>"
                                    data-student-name="<?= htmlspecialchars($c['student_name']) ?>"
                                    data-counselor="<?= htmlspecialchars($c['counselor_id']) ?>"
                                    data-counselor-name="<?= htmlspecialchars($c['counselor_name']) ?>">
                                #<?= htmlspecialchars($c['case_number']) ?> — <?= htmlspecialchars($c['student_name']) ?> (<?= htmlspecialchars($c['case_type']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="apt-form-group">
                    <label>Student Number</label>
                    <input type="text" name="student_number" id="aptBookStudentNumber" placeholder="e.g. 2023001" required>
                </div>
                <div class="apt-form-group">
                    <label>Counselor</label>
                    <select name="counselor_id" id="aptBookCounselor" required>
                        <option value="">Select counselor</option>
                        <?php foreach ($counselors as $c): ?>
                            <option value="<?= htmlspecialchars($c['employee_id']) ?>"><?= htmlspecialchars($c['name']) ?> (<?= htmlspecialchars($c['position_name']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="apt-form-group">
                    <label>Date &amp; Time</label>
                    <input type="datetime-local" name="appointment_date" id="aptBookDateTime" required>
                </div>
                <div class="apt-form-group" id="aptAvailabilityBox" style="display:none;">
                    <div class="apt-availability-box">
                        <strong>Already booked that day for this counselor:</strong>
                        <div id="aptAvailabilitySlots"></div>
                    </div>
                </div>
                <div class="apt-form-group">
                    <label>Meeting Type</label>
                    <select name="meeting_type">
                        <option value="Face-to-Face">Face-to-Face</option>
                        <option value="Online">Online</option>
                    </select>
                </div>
                <div class="apt-form-group">
                    <label>Purpose</label>
                    <textarea name="purpose" placeholder="What is this appointment for..." required></textarea>
                </div>
            </div>
            <div class="apt-modal__footer">
                <button type="button" class="apt-btn apt-btn--ghost apt-btn--sm" id="aptBookCancelBtn">Cancel</button>
                <button type="submit" class="apt-btn apt-btn--sm">Book Appointment</button>
            </div>
        </form>
    </div>
</div>
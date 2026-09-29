<?php
/**
 * cases.php
 * Module: Case Management (Referral + Counseling)
 *
 * Renders server-side directly from the Cases model, same pattern as
 * students.php / approval-submission.php. CasesController.php (AJAX) is
 * only used afterward — for filtering, opening the case drawer, creating
 * cases/referrals, reviewing referrals, and recording sessions.
 */

include_once __DIR__ . '/../../../auth/session.php';
include_once __DIR__ . '/../classes/Cases.php';

$casesClass = new Cases();

$filters = [
    'search'       => trim($_GET['search'] ?? ''),
    'status'       => $_GET['status'] ?? '',
    'priority'     => $_GET['priority'] ?? '',
    'case_type'    => $_GET['case_type'] ?? '',
    'counselor_id' => $_GET['counselor_id'] ?? '',
];
$page     = max(1, (int) ($_GET['page'] ?? 1));
$pageSize = 10;

$result     = $casesClass->getList($filters, $page, $pageSize);
$cases      = $result['rows'];
$totalCases = $result['total'];
$totalPages = (int) ceil($totalCases / $pageSize);

$counselors = $casesClass->getCounselors();

function case_status_badge_class($status) {
    return match ($status) {
        'Open' => 'case-badge--status-open',
        'In Progress' => 'case-badge--status-progress',
        default => 'case-badge--status-closed',
    };
}

function case_priority_badge_class($priority) {
    return match ($priority) {
        'Critical' => 'case-badge--priority-critical',
        'High' => 'case-badge--priority-high',
        'Medium' => 'case-badge--priority-medium',
        default => 'case-badge--priority-low',
    };
}
?>

<div class="module-header">
    <h1>Cases</h1>
</div>

<div class="module-content">

    <!-- Toolbar -->
    <div class="case-toolbar">
        <div class="case-toolbar__filters">
            <div class="case-search-box">
                <i class="fa fa-search"></i>
                <input type="text" id="caseSearchInput" placeholder="Search by student or case #..." value="<?= htmlspecialchars($filters['search']) ?>">
            </div>

            <select class="case-filter-select" id="caseFilterStatus">
                <option value="" <?= $filters['status'] === '' ? 'selected' : '' ?>>All Status</option>
                <option value="Open" <?= $filters['status'] === 'Open' ? 'selected' : '' ?>>Open</option>
                <option value="In Progress" <?= $filters['status'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                <option value="Closed" <?= $filters['status'] === 'Closed' ? 'selected' : '' ?>>Closed</option>
            </select>

            <select class="case-filter-select" id="caseFilterPriority">
                <option value="" <?= $filters['priority'] === '' ? 'selected' : '' ?>>All Priority</option>
                <option value="Low" <?= $filters['priority'] === 'Low' ? 'selected' : '' ?>>Low</option>
                <option value="Medium" <?= $filters['priority'] === 'Medium' ? 'selected' : '' ?>>Medium</option>
                <option value="High" <?= $filters['priority'] === 'High' ? 'selected' : '' ?>>High</option>
                <option value="Critical" <?= $filters['priority'] === 'Critical' ? 'selected' : '' ?>>Critical</option>
            </select>

            <select class="case-filter-select" id="caseFilterType">
                <option value="" <?= $filters['case_type'] === '' ? 'selected' : '' ?>>All Case Types</option>
                <option value="Referral" <?= $filters['case_type'] === 'Referral' ? 'selected' : '' ?>>Referral</option>
                <option value="Walk-in" <?= $filters['case_type'] === 'Walk-in' ? 'selected' : '' ?>>Walk-in</option>
                <option value="Self Referral" <?= $filters['case_type'] === 'Self Referral' ? 'selected' : '' ?>>Self Referral</option>
                <option value="Incident" <?= $filters['case_type'] === 'Incident' ? 'selected' : '' ?>>Incident</option>
            </select>

            <select class="case-filter-select" id="caseFilterCounselor">
                <option value="">All Counselors</option>
                <?php foreach ($counselors as $c): ?>
                    <option value="<?= htmlspecialchars($c['employee_id']) ?>" <?= $filters['counselor_id'] == $c['employee_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="case-toolbar__actions">
            <button type="button" class="case-btn" id="caseCreateBtn">+ Create Case</button>
        </div>
    </div>

    <!-- Case list -->
    <div class="case-table-wrapper">
        <table class="case-table">
            <thead>
                <tr>
                    <th>Case #</th>
                    <th>Student</th>
                    <th>Type</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Counselor</th>
                    <th>Opened</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($cases)): ?>
                <tr><td colspan="7" class="case-table__empty">No cases found for the selected filters.</td></tr>
                <?php else: ?>
                    <?php foreach ($cases as $c): ?>
                    <tr class="case-row" data-case-id="<?= htmlspecialchars($c['case_id']) ?>">
                        <td class="case-number">#<?= htmlspecialchars($c['case_number']) ?></td>
                        <td>
                            <div class="case-student-name"><?= htmlspecialchars($c['student_name']) ?></div>
                            <div class="case-student-sub">#<?= htmlspecialchars($c['student_number']) ?></div>
                        </td>
                        <td><?= htmlspecialchars($c['case_type']) ?></td>
                        <td><span class="case-badge <?= case_priority_badge_class($c['priority']) ?>"><?= htmlspecialchars($c['priority']) ?></span></td>
                        <td><span class="case-badge <?= case_status_badge_class($c['status']) ?>"><?= htmlspecialchars($c['status']) ?></span></td>
                        <td><?= htmlspecialchars($c['counselor_name']) ?></td>
                        <td><?= date('M d, Y', strtotime($c['opened_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="case-pagination">
        <span>
            <?php if ($totalCases > 0): ?>
                Showing <?= (($page - 1) * $pageSize) + 1 ?>-<?= min($page * $pageSize, $totalCases) ?> of <?= $totalCases ?> cases
            <?php else: ?>
                No cases found
            <?php endif; ?>
        </span>
        <div class="case-pagination__pages">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <button class="case-pagination__page <?= $i === $page ? 'case-pagination__page--active' : '' ?>" data-page="<?= $i ?>"><?= $i ?></button>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
                <button class="case-pagination__page" data-page="<?= $page + 1 ?>">&rsaquo;</button>
            <?php endif; ?>
        </div>
    </div>

</div>

<!-- ============================================================
     Case Detail Drawer (populated live via AJAX on row click)
     ============================================================ -->
<div class="case-overlay" id="caseOverlay"></div>
<div class="case-drawer" id="caseDrawer">

    <div class="case-drawer__header">
        <div>
            <div class="case-drawer__number" id="caseDrawerNumber"></div>
            <div class="case-drawer__title" id="caseDrawerTitle"></div>
            <div class="case-drawer__badges" id="caseDrawerBadges"></div>
        </div>
        <button type="button" class="case-drawer__close" id="caseDrawerCloseBtn">&times;</button>
    </div>

    <div class="case-tabs">
        <button class="case-tab case-tab--active" data-tab="overview">Overview</button>
        <button class="case-tab" data-tab="referral" id="caseReferralTabBtn">Referral</button>
        <button class="case-tab" data-tab="sessions">Counseling Sessions</button>
    </div>

    <div class="case-drawer__body">

        <!-- Overview -->
        <div class="case-tab-panel case-tab-panel--active" data-panel="overview">
            <div class="case-quick-actions">
                <button type="button" class="case-btn case-btn--sm" id="caseAssignCounselorBtn">Assign Counselor</button>
                <button type="button" class="case-btn case-btn--sm" id="caseUpdateStatusBtn">Update Status</button>
                <button type="button" class="case-btn case-btn--ghost case-btn--sm" id="caseSetPriorityBtn">Set Priority</button>
            </div>
            <div class="case-field-grid">
                <div><div class="case-field-label">Case Type</div><div class="case-field-value" data-field="case_type"></div></div>
                <div><div class="case-field-label">Priority</div><div class="case-field-value" data-field="priority"></div></div>
                <div><div class="case-field-label">Counselor</div><div class="case-field-value" data-field="counselor_name"></div></div>
                <div><div class="case-field-label">Status</div><div class="case-field-value" data-field="status"></div></div>
                <div><div class="case-field-label">Opened</div><div class="case-field-value" data-field="opened_at_display"></div></div>
                <div><div class="case-field-label">Closed</div><div class="case-field-value" data-field="closed_at_display"></div></div>
            </div>
            <div class="case-summary-label">Summary</div>
            <div class="case-summary-box" data-field="summary"></div>
        </div>

        <!-- Referral -->
        <div class="case-tab-panel" data-panel="referral">
            <div id="caseReferralContent"></div>
        </div>

        <!-- Counseling Sessions -->
        <div class="case-tab-panel" data-panel="sessions">
            <div class="case-record-btn-row">
                <button type="button" class="case-btn case-btn--sm" id="caseRecordSessionBtn">+ Record Session</button>
            </div>
            <div class="case-session-list" id="caseSessionList"></div>
        </div>

    </div>
</div>

<!-- ============================================================
     Create Case Modal
     ============================================================ -->
<div class="case-modal-overlay" id="caseCreateModal">
    <div class="case-modal">
        <div class="case-modal__header">
            <h3>Create Case</h3>
            <button type="button" class="case-drawer__close" id="caseCreateCloseBtn">&times;</button>
        </div>
        <form id="caseCreateForm" data-skip>
            <div class="case-modal__body">
                <div class="case-form-group">
                    <label>Case Type</label>
                    <select name="case_type" id="caseCreateType" required>
                        <option value="">Select type</option>
                        <option value="Walk-in">Walk-in</option>
                        <option value="Self Referral">Self Referral</option>
                        <option value="Incident">Incident</option>
                        <option value="Referral">Referral</option>
                    </select>
                </div>

                <!-- Incident-only: strict picker, no freeform Incident case without one -->
                <div class="case-form-group case-form-group--hidden" id="caseIncidentPickerGroup">
                    <label>Select Incident</label>
                    <select name="incident_id" id="caseIncidentSelect">
                        <option value="">Loading incidents...</option>
                    </select>
                </div>
                <div class="case-form-group case-form-group--hidden" id="caseIncidentInfoGroup">
                    <div class="case-summary-box" id="caseIncidentInfoBox"></div>
                </div>

                <div class="case-form-group" id="caseStudentNumberGroup">
                    <label>Student Number</label>
                    <input type="text" name="student_number" id="caseCreateStudentNumber" placeholder="e.g. 2023001" required>
                </div>
                <div class="case-form-group">
                    <label>Counselor</label>
                    <select name="counselor_id" required>
                        <option value="">Select counselor</option>
                        <?php foreach ($counselors as $c): ?>
                            <option value="<?= htmlspecialchars($c['employee_id']) ?>"><?= htmlspecialchars($c['name']) ?> (<?= htmlspecialchars($c['position_name']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="case-form-group">
                    <label>Priority</label>
                    <select name="priority">
                        <option value="Low">Low</option>
                        <option value="Medium" selected>Medium</option>
                        <option value="High">High</option>
                        <option value="Critical">Critical</option>
                    </select>
                </div>
                <div class="case-form-group" id="caseCreateSummaryGroup">
                    <label>Summary</label>
                    <textarea name="summary" placeholder="Brief description of the case..."></textarea>
                </div>

                <!-- Referral-only fields, shown when Case Type = Referral -->
                <div class="case-form-group case-form-group--hidden" id="caseReferralFields1">
                    <label>Referred By (Employee ID)</label>
                    <input type="text" name="referred_by" placeholder="Employee ID of the referring staff">
                </div>
                <div class="case-form-group case-form-group--hidden" id="caseReferralFields2">
                    <label>Referral Source</label>
                    <select name="referral_source">
                        <option value="Teacher">Teacher</option>
                        <option value="Adviser">Adviser</option>
                        <option value="Principal">Principal</option>
                        <option value="Parent">Parent</option>
                        <option value="Self">Self</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="case-form-group case-form-group--hidden" id="caseReferralFields3">
                    <label>Referral Reason</label>
                    <textarea name="referral_reason" placeholder="Why is this student being referred..."></textarea>
                </div>
            </div>
            <div class="case-modal__footer">
                <button type="button" class="case-btn case-btn--ghost case-btn--sm" id="caseCreateCancelBtn">Cancel</button>
                <button type="submit" class="case-btn case-btn--sm">Create Case</button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================
     Record / Edit Counseling Session Modal
     ============================================================ -->
<div class="case-modal-overlay" id="caseSessionModal">
    <div class="case-modal">
        <div class="case-modal__header">
            <h3 id="caseSessionModalTitle">Record Counseling Session</h3>
            <button type="button" class="case-drawer__close" id="caseSessionCloseBtn">&times;</button>
        </div>
        <form id="caseSessionForm" data-skip>
            <input type="hidden" name="session_id" value="">
            <div class="case-modal__body">
                <div class="case-form-group">
                    <label>Session Type</label>
                    <select name="session_type" required>
                        <option value="Academic">Academic</option>
                        <option value="Behavioral">Behavioral</option>
                        <option value="Career">Career</option>
                        <option value="Personal">Personal</option>
                        <option value="Family">Family</option>
                        <option value="Mental Health">Mental Health</option>
                    </select>
                </div>
                <div class="case-form-group">
                    <label>Session Date</label>
                    <input type="datetime-local" name="session_date">
                </div>
                <div class="case-form-group">
                    <label>Duration (minutes)</label>
                    <input type="number" name="duration_minutes" value="30" min="1">
                </div>
                <div class="case-form-group">
                    <label>Session Notes</label>
                    <textarea name="session_notes" placeholder="What was discussed..."></textarea>
                </div>
                <div class="case-form-group">
                    <label>Recommendations</label>
                    <textarea name="recommendations" placeholder="Next steps, referrals, etc."></textarea>
                </div>
                <div class="case-form-group">
                    <label>Schedule Next Session (optional)</label>
                    <input type="datetime-local" name="next_session">
                </div>
            </div>
            <div class="case-modal__footer">
                <button type="button" class="case-btn case-btn--ghost case-btn--sm" id="caseSessionCancelBtn">Cancel</button>
                <button type="submit" class="case-btn case-btn--sm">Save Session</button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================
     Assign Counselor / Update Status / Set Priority Modal (shared shell)
     ============================================================ -->
<div class="case-modal-overlay" id="caseQuickActionModal">
    <div class="case-modal">
        <div class="case-modal__header">
            <h3 id="caseQuickActionTitle">Update</h3>
            <button type="button" class="case-drawer__close" id="caseQuickActionCloseBtn">&times;</button>
        </div>
        <form id="caseQuickActionForm" data-skip>
            <div class="case-modal__body" id="caseQuickActionBody"></div>
            <div class="case-modal__footer">
                <button type="button" class="case-btn case-btn--ghost case-btn--sm" id="caseQuickActionCancelBtn">Cancel</button>
                <button type="submit" class="case-btn case-btn--sm">Save</button>
            </div>
        </form>
    </div>
</div>
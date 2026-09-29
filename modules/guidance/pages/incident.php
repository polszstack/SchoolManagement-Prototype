<?php
/**
 * incidents.php
 * Module: Incident Management
 *
 * Renders server-side directly from the Incidents model, same pattern as
 * students.php / cases.php / appointments.php. IncidentsController.php
 * (AJAX) is only used afterward — for filtering, creating/editing
 * incidents, updating status, and recording resolutions.
 *
 * NOTE (intentional, per project owner): the "Escalate to Case" trigger
 * button and evidence upload are deliberately NOT built in this pass.
 * case_id linking is fully one-directional — it can ONLY be written by
 * Cases::createCaseFromIncident() via the Cases module's "Select
 * Incident" picker. This page displays the link (read-only) but never
 * sets or changes it.
 */

include_once __DIR__ . '/../../../auth/session.php';
include_once __DIR__ . '/../classes/Incidents.php';

$incidentsClass = new Incidents();

$filters = [
    'search'        => trim($_GET['search'] ?? ''),
    'severity'      => $_GET['severity'] ?? '',
    'status'        => $_GET['status'] ?? '',
    'incident_type' => $_GET['incident_type'] ?? '',
];
$page     = max(1, (int) ($_GET['page'] ?? 1));
$pageSize = 10;

$result        = $incidentsClass->getList($filters, $page, $pageSize);
$incidents     = $result['rows'];
$totalIncidents = $result['total'];
$totalPages    = (int) ceil($totalIncidents / $pageSize);

$incidentTypes = $incidentsClass->getIncidentTypes();

function incdnt_severity_badge_class($severity) {
    return match ($severity) {
        'Critical' => 'incdnt-badge--severity-critical',
        'Major' => 'incdnt-badge--severity-major',
        'Moderate' => 'incdnt-badge--severity-moderate',
        default => 'incdnt-badge--severity-minor',
    };
}

function incdnt_status_badge_class($status) {
    return match ($status) {
        'Reported' => 'incdnt-badge--status-reported',
        'Investigating' => 'incdnt-badge--status-investigating',
        'Resolved' => 'incdnt-badge--status-resolved',
        default => 'incdnt-badge--status-closed',
    };
}
?>

<div class="module-header">
    <h1>Incidents</h1>
</div>

<div class="module-content">

    <!-- Toolbar -->
    <div class="incdnt-toolbar">
        <div class="incdnt-toolbar__filters">
            <div class="incdnt-search-box">
                <i class="fa fa-search"></i>
                <input type="text" id="incdntSearchInput" placeholder="Search by student or type..." value="<?= htmlspecialchars($filters['search']) ?>">
            </div>

            <select class="incdnt-filter-select" id="incdntFilterSeverity">
                <option value="" <?= $filters['severity'] === '' ? 'selected' : '' ?>>All Severity</option>
                <option value="Minor" <?= $filters['severity'] === 'Minor' ? 'selected' : '' ?>>Minor</option>
                <option value="Moderate" <?= $filters['severity'] === 'Moderate' ? 'selected' : '' ?>>Moderate</option>
                <option value="Major" <?= $filters['severity'] === 'Major' ? 'selected' : '' ?>>Major</option>
                <option value="Critical" <?= $filters['severity'] === 'Critical' ? 'selected' : '' ?>>Critical</option>
            </select>

            <select class="incdnt-filter-select" id="incdntFilterStatus">
                <option value="" <?= $filters['status'] === '' ? 'selected' : '' ?>>All Status</option>
                <option value="Reported" <?= $filters['status'] === 'Reported' ? 'selected' : '' ?>>Reported</option>
                <option value="Investigating" <?= $filters['status'] === 'Investigating' ? 'selected' : '' ?>>Investigating</option>
                <option value="Resolved" <?= $filters['status'] === 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                <option value="Closed" <?= $filters['status'] === 'Closed' ? 'selected' : '' ?>>Closed</option>
            </select>

            <select class="incdnt-filter-select" id="incdntFilterType">
                <option value="">All Types</option>
                <?php foreach ($incidentTypes as $type): ?>
                    <option value="<?= htmlspecialchars($type) ?>" <?= $filters['incident_type'] === $type ? 'selected' : '' ?>><?= htmlspecialchars($type) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="incdnt-toolbar__actions">
            <button type="button" class="incdnt-btn" id="incdntCreateBtn">+ Report Incident</button>
        </div>
    </div>

    <!-- Incident list -->
    <div class="incdnt-table-wrapper">
        <table class="incdnt-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Student</th>
                    <th>Type</th>
                    <th>Severity</th>
                    <th>Status</th>
                    <th>Reported By</th>
                    <th>Linked Case</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($incidents)): ?>
                <tr><td colspan="7" class="incdnt-table__empty">No incidents found for the selected filters.</td></tr>
                <?php else: ?>
                    <?php foreach ($incidents as $i): ?>
                    <tr class="incdnt-row" data-incident-id="<?= htmlspecialchars($i['incident_id']) ?>">
                        <td><?= date('M d, Y', strtotime($i['incident_date'])) ?></td>
                        <td>
                            <div class="incdnt-student-name"><?= htmlspecialchars($i['student_name']) ?></div>
                            <div class="incdnt-student-sub">#<?= htmlspecialchars($i['student_number']) ?></div>
                        </td>
                        <td><?= htmlspecialchars($i['incident_type']) ?></td>
                        <td><span class="incdnt-badge <?= incdnt_severity_badge_class($i['severity']) ?>"><?= htmlspecialchars($i['severity']) ?></span></td>
                        <td><span class="incdnt-badge <?= incdnt_status_badge_class($i['status']) ?>"><?= htmlspecialchars($i['status']) ?></span></td>
                        <td><?= htmlspecialchars($i['reported_by_name']) ?></td>
                        <td>
                            <?php if (!empty($i['case_number'])): ?>
                                <span class="incdnt-case-link">#<?= htmlspecialchars($i['case_number']) ?></span>
                            <?php else: ?>
                                <span class="incdnt-case-none">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="incdnt-pagination">
        <span>
            <?php if ($totalIncidents > 0): ?>
                Showing <?= (($page - 1) * $pageSize) + 1 ?>-<?= min($page * $pageSize, $totalIncidents) ?> of <?= $totalIncidents ?> incidents
            <?php else: ?>
                No incidents found
            <?php endif; ?>
        </span>
        <div class="incdnt-pagination__pages">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <button class="incdnt-pagination__page <?= $i === $page ? 'incdnt-pagination__page--active' : '' ?>" data-page="<?= $i ?>"><?= $i ?></button>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
                <button class="incdnt-pagination__page" data-page="<?= $page + 1 ?>">&rsaquo;</button>
            <?php endif; ?>
        </div>
    </div>

</div>

<!-- ============================================================
     Incident Detail / Action Modal (populated via AJAX on row click)
     ============================================================ -->
<div class="incdnt-modal-overlay" id="incdntDetailModal">
    <div class="incdnt-modal">
        <div class="incdnt-modal__header">
            <h3>Incident Details</h3>
            <button type="button" class="incdnt-modal__close" id="incdntDetailCloseBtn">&times;</button>
        </div>
        <div class="incdnt-modal__body">
            <div class="incdnt-field-grid">
                <div><div class="incdnt-field-label">Student</div><div class="incdnt-field-value" data-field="student_name"></div></div>
                <div><div class="incdnt-field-label">Reported By</div><div class="incdnt-field-value" data-field="reported_by_name"></div></div>
                <div><div class="incdnt-field-label">Type</div><div class="incdnt-field-value" data-field="incident_type"></div></div>
                <div><div class="incdnt-field-label">Severity</div><div class="incdnt-field-value" data-field="severity"></div></div>
                <div><div class="incdnt-field-label">Date &amp; Time</div><div class="incdnt-field-value" data-field="incident_date_display"></div></div>
                <div><div class="incdnt-field-label">Location</div><div class="incdnt-field-value" data-field="location"></div></div>
                <div><div class="incdnt-field-label">Status</div><div class="incdnt-field-value" data-field="status"></div></div>
                <div><div class="incdnt-field-label">Linked Case</div><div class="incdnt-field-value" data-field="case_number"></div></div>
            </div>
            <div class="incdnt-summary-label">Description</div>
            <div class="incdnt-summary-box" data-field="description"></div>

            <div id="incdntResolutionSection">
                <div class="incdnt-summary-label" style="margin-top:12px;">Disciplinary Action / Resolution</div>
                <div class="incdnt-summary-box" data-field="action_taken"></div>
                <div class="incdnt-field-grid" style="margin-top:8px;">
                    <div><div class="incdnt-field-label">Resolution Date</div><div class="incdnt-field-value" data-field="action_date_display"></div></div>
                </div>
            </div>
        </div>
        <div class="incdnt-modal__footer" style="flex-direction: column; align-items: stretch;">
            <div class="incdnt-modal__actions-row" id="incdntActionButtons"></div>
        </div>
    </div>
</div>

<!-- ============================================================
     Create / Edit Incident Modal
     ============================================================ -->
<div class="incdnt-modal-overlay" id="incdntFormModal">
    <div class="incdnt-modal">
        <div class="incdnt-modal__header">
            <h3 id="incdntFormModalTitle">Report Incident</h3>
            <button type="button" class="incdnt-modal__close" id="incdntFormCloseBtn">&times;</button>
        </div>
        <form id="incdntForm" data-skip>
            <input type="hidden" name="incident_id" value="">
            <div class="incdnt-modal__body">
                <div class="incdnt-form-group">
                    <label>Student Number</label>
                    <input type="text" name="student_number" id="incdntFormStudentNumber" placeholder="e.g. 2023001" required>
                </div>
                <div class="incdnt-form-row">
                    <div class="incdnt-form-group">
                        <label>Incident Type</label>
                        <input type="text" name="incident_type" placeholder="e.g. Classroom Disruption" required>
                    </div>
                    <div class="incdnt-form-group">
                        <label>Severity</label>
                        <select name="severity">
                            <option value="Minor">Minor</option>
                            <option value="Moderate">Moderate</option>
                            <option value="Major">Major</option>
                            <option value="Critical">Critical</option>
                        </select>
                    </div>
                </div>
                <div class="incdnt-form-row">
                    <div class="incdnt-form-group">
                        <label>Date &amp; Time</label>
                        <input type="datetime-local" name="incident_date" required>
                    </div>
                    <div class="incdnt-form-group">
                        <label>Location</label>
                        <input type="text" name="location" placeholder="e.g. Room 204">
                    </div>
                </div>
                <div class="incdnt-form-group">
                    <label>Description</label>
                    <textarea name="description" placeholder="What happened..." required></textarea>
                </div>
                <div class="incdnt-form-group">
                    <label>Linked Case</label>
                    <div class="incdnt-summary-box" id="incdntFormCaseDisplay">Not linked to a case. Cases get linked from the Cases module's "Select Incident" picker, not here.</div>
                </div>
            </div>
            <div class="incdnt-modal__footer">
                <button type="button" class="incdnt-btn incdnt-btn--ghost incdnt-btn--sm" id="incdntFormCancelBtn">Cancel</button>
                <button type="submit" class="incdnt-btn incdnt-btn--sm">Save Incident</button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================
     Record Resolution Modal
     ============================================================ -->
<div class="incdnt-modal-overlay" id="incdntResolutionModal">
    <div class="incdnt-modal">
        <div class="incdnt-modal__header">
            <h3>Record Resolution</h3>
            <button type="button" class="incdnt-modal__close" id="incdntResolutionCloseBtn">&times;</button>
        </div>
        <form id="incdntResolutionForm" data-skip>
            <div class="incdnt-modal__body">
                <div class="incdnt-form-group">
                    <label>Disciplinary Action Taken</label>
                    <textarea name="action_taken" placeholder="What action was taken..." required></textarea>
                </div>
                <div class="incdnt-form-group">
                    <label>Resolution Date</label>
                    <input type="date" name="action_date" required>
                </div>
                <div class="incdnt-form-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="Resolved">Resolved</option>
                        <option value="Closed">Closed</option>
                    </select>
                </div>
            </div>
            <div class="incdnt-modal__footer">
                <button type="button" class="incdnt-btn incdnt-btn--ghost incdnt-btn--sm" id="incdntResolutionCancelBtn">Cancel</button>
                <button type="submit" class="incdnt-btn incdnt-btn--sm">Save Resolution</button>
            </div>
        </form>
    </div>
</div>
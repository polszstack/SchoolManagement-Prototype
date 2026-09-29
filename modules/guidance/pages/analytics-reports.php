<?php
/**
 * analytics-reports.php
 * Module: Analytics & Reports
 *
 * The 3 breakdown mini-charts render server-side directly (same pattern
 * as other modules' initial data). The report generator itself is
 * AJAX-driven via controllers/AnalyticsReportsController.php since the
 * report type + filters + results table are all interactive.
 */

include_once __DIR__ . '/../../../auth/session.php';
include_once __DIR__ . '/../classes/Analytics.php';

$analyticsClass = new Analytics();

$casesByStatus       = $analyticsClass->getCasesByStatus();
$appointmentsByStatus = $analyticsClass->getAppointmentsByStatus();
$incidentsBySeverity = $analyticsClass->getIncidentsBySeverity();

function rprt_max_count(array $rows): int
{
    $max = 1;
    foreach ($rows as $row) {
        $max = max($max, (int) $row['count']);
    }
    return $max;
}
?>

<div class="module-header">
    <h1>Analytics &amp; Reports</h1>
</div>

<div class="module-content">

    <!-- Breakdown mini-charts -->
    <div class="rprt-breakdown-grid">
        <div class="rprt-breakdown-card">
            <h3>Cases by Status</h3>
            <?php $max = rprt_max_count($casesByStatus); ?>
            <?php foreach ($casesByStatus as $row): ?>
                <div class="rprt-bar-row">
                    <span class="rprt-bar-label"><?= htmlspecialchars($row['label']) ?></span>
                    <div class="rprt-bar-track">
                        <div class="rprt-bar-fill" style="width: <?= round(($row['count'] / $max) * 100) ?>%;"></div>
                    </div>
                    <span class="rprt-bar-value"><?= htmlspecialchars($row['count']) ?></span>
                </div>
            <?php endforeach; ?>
            <?php if (empty($casesByStatus)): ?><div class="rprt-empty">No case data yet.</div><?php endif; ?>
        </div>

        <div class="rprt-breakdown-card">
            <h3>Appointments by Status</h3>
            <?php $max = rprt_max_count($appointmentsByStatus); ?>
            <?php foreach ($appointmentsByStatus as $row): ?>
                <div class="rprt-bar-row">
                    <span class="rprt-bar-label"><?= htmlspecialchars($row['label']) ?></span>
                    <div class="rprt-bar-track">
                        <div class="rprt-bar-fill" style="width: <?= round(($row['count'] / $max) * 100) ?>%;"></div>
                    </div>
                    <span class="rprt-bar-value"><?= htmlspecialchars($row['count']) ?></span>
                </div>
            <?php endforeach; ?>
            <?php if (empty($appointmentsByStatus)): ?><div class="rprt-empty">No appointment data yet.</div><?php endif; ?>
        </div>

        <div class="rprt-breakdown-card">
            <h3>Incidents by Severity</h3>
            <?php $max = rprt_max_count($incidentsBySeverity); ?>
            <?php foreach ($incidentsBySeverity as $row): ?>
                <div class="rprt-bar-row">
                    <span class="rprt-bar-label"><?= htmlspecialchars($row['label']) ?></span>
                    <div class="rprt-bar-track">
                        <div class="rprt-bar-fill rprt-bar-fill--severity" style="width: <?= round(($row['count'] / $max) * 100) ?>%;"></div>
                    </div>
                    <span class="rprt-bar-value"><?= htmlspecialchars($row['count']) ?></span>
                </div>
            <?php endforeach; ?>
            <?php if (empty($incidentsBySeverity)): ?><div class="rprt-empty">No incident data yet.</div><?php endif; ?>
        </div>
    </div>

    <!-- Report generator -->
    <div class="rprt-generator">
        <div class="rprt-generator__header">
            <h3>Generate a Report</h3>
        </div>
        <div class="rprt-generator__controls">
            <select class="rprt-filter-select" id="rprtReportType">
                <option value="student_counseling">Student Counseling Report</option>
                <option value="referral">Referral Report</option>
                <option value="appointment">Appointment Report</option>
                <option value="incident">Incident Report</option>
                <option value="monthly_guidance">Monthly Guidance Report</option>
                <option value="yearly_guidance">Yearly Guidance Report</option>
                <option value="counselor_workload">Counselor Workload Report</option>
                <option value="student_risk">Student Risk Summary</option>
            </select>

            <!-- date_from/date_to: used by counseling/referral/appointment/incident/workload -->
            <div class="rprt-filter-group" id="rprtDateRangeGroup">
                <input type="date" class="rprt-filter-date" id="rprtDateFrom">
                <span class="rprt-filter-sep">to</span>
                <input type="date" class="rprt-filter-date" id="rprtDateTo">
            </div>

            <!-- month+year: used by monthly_guidance -->
            <div class="rprt-filter-group" id="rprtMonthGroup" style="display:none;">
                <select class="rprt-filter-select" id="rprtMonth">
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= $m ?>" <?= $m == date('n') ? 'selected' : '' ?>><?= date('F', mktime(0,0,0,$m,1)) ?></option>
                    <?php endfor; ?>
                </select>
                <select class="rprt-filter-select" id="rprtYearForMonth">
                    <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                        <option value="<?= $y ?>"><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <!-- year only: used by yearly_guidance -->
            <div class="rprt-filter-group" id="rprtYearGroup" style="display:none;">
                <select class="rprt-filter-select" id="rprtYear">
                    <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                        <option value="<?= $y ?>"><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <button type="button" class="rprt-btn" id="rprtGenerateBtn">Generate</button>
        </div>

        <div class="rprt-generator__actions" id="rprtExportActions" style="display:none;">
            <button type="button" class="rprt-btn rprt-btn--ghost rprt-btn--sm" id="rprtExportPdfBtn">Export to PDF</button>
            <button type="button" class="rprt-btn rprt-btn--ghost rprt-btn--sm" id="rprtExportExcelBtn">Export to Excel</button>
            <button type="button" class="rprt-btn rprt-btn--ghost rprt-btn--sm" id="rprtPrintBtn">Print</button>
        </div>

        <div class="rprt-results-wrapper" id="rprtResultsWrapper">
            <div class="rprt-empty">Select a report type and click Generate.</div>
        </div>
    </div>

</div>
<?php
/**
 * dashboard-overview.php
 * Landing page shown when a Guidance Counselor logs in.
 *
 * Fully server-rendered — no JS/controller needed here at all, since
 * every widget is read-only "quick glance" data with no filters or
 * interactivity. "Quick Report Generation" is just a link over to the
 * Analytics & Reports page (?page=analytics-reports), using the same
 * data-page sidebar-link convention the router already intercepts.
 */

include_once __DIR__ . '/../../../auth/session.php';
include_once __DIR__ . '/../classes/Analytics.php';

$analyticsClass = new Analytics();

$summary          = $analyticsClass->getDashboardSummary();
$todaysAppts      = $analyticsClass->getTodaysAppointmentsList();
$pendingReferrals = $analyticsClass->getPendingReferralsList();
$highRiskStudents = $analyticsClass->getHighRiskStudentsList();
$recentIncidents  = $analyticsClass->getRecentIncidentsList();

function dshbrd_severity_badge_class($severity) {
    return match ($severity) {
        'Critical' => 'dshbrd-badge--severity-critical',
        'Major' => 'dshbrd-badge--severity-major',
        'Moderate' => 'dshbrd-badge--severity-moderate',
        default => 'dshbrd-badge--severity-minor',
    };
}
?>

<div class="module-header">
    <h1>Dashboard Overview</h1>
</div>

<div class="module-content">

    <!-- Quick-glance stat cards -->
    <div class="dshbrd-stats-grid">
        <div class="dshbrd-stat-card dshbrd-stat-card--accent">
            <span class="dshbrd-stat-card__label">Total Active Cases</span>
            <span class="dshbrd-stat-card__value"><?= htmlspecialchars($summary['active_cases']) ?></span>
        </div>
        <div class="dshbrd-stat-card">
            <span class="dshbrd-stat-card__label">Students Under Monitoring</span>
            <span class="dshbrd-stat-card__value"><?= htmlspecialchars($summary['students_monitoring']) ?></span>
        </div>
        <div class="dshbrd-stat-card">
            <span class="dshbrd-stat-card__label">Today's Appointments</span>
            <span class="dshbrd-stat-card__value"><?= htmlspecialchars($summary['todays_appointments']) ?></span>
        </div>
        <div class="dshbrd-stat-card">
            <span class="dshbrd-stat-card__label">Pending Referrals</span>
            <span class="dshbrd-stat-card__value"><?= htmlspecialchars($summary['pending_referrals']) ?></span>
        </div>
        <div class="dshbrd-stat-card dshbrd-stat-card--high">
            <span class="dshbrd-stat-card__label">High-Risk Students</span>
            <span class="dshbrd-stat-card__value"><?= htmlspecialchars($summary['high_risk_students']) ?></span>
        </div>
        <div class="dshbrd-stat-card">
            <span class="dshbrd-stat-card__label">Recent Incidents (7 days)</span>
            <span class="dshbrd-stat-card__value"><?= htmlspecialchars($summary['recent_incidents']) ?></span>
        </div>
        <div class="dshbrd-stat-card">
            <span class="dshbrd-stat-card__label">Counseling Sessions This Month</span>
            <span class="dshbrd-stat-card__value"><?= htmlspecialchars($summary['monthly_counseling_sessions']) ?></span>
        </div>
        <div class="dshbrd-stat-card dshbrd-stat-card--action">
            <span class="dshbrd-stat-card__label">Need a report?</span>
            <a href="?page=analytics-reports" data-page="analytics-reports" class="dshbrd-btn dshbrd-btn--sm">Generate Report</a>
        </div>
    </div>

    <!-- Widget grid -->
    <div class="dshbrd-widget-grid">

        <div class="dshbrd-widget">
            <div class="dshbrd-widget__header">
                <h3>Today's Appointments</h3>
            </div>
            <div class="dshbrd-widget__body">
                <?php if (empty($todaysAppts)): ?>
                    <div class="dshbrd-empty">No appointments scheduled for today.</div>
                <?php else: ?>
                    <?php foreach ($todaysAppts as $a): ?>
                        <div class="dshbrd-list-item">
                            <div class="dshbrd-list-item__time"><?= date('g:i A', strtotime($a['appointment_date'])) ?></div>
                            <div class="dshbrd-list-item__body">
                                <div class="dshbrd-list-item__title"><?= htmlspecialchars($a['student_name']) ?></div>
                                <div class="dshbrd-list-item__sub"><?= htmlspecialchars($a['purpose']) ?></div>
                            </div>
                            <span class="dshbrd-badge"><?= htmlspecialchars($a['status']) ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="dshbrd-widget">
            <div class="dshbrd-widget__header">
                <h3>Pending Referrals</h3>
            </div>
            <div class="dshbrd-widget__body">
                <?php if (empty($pendingReferrals)): ?>
                    <div class="dshbrd-empty">No pending referrals.</div>
                <?php else: ?>
                    <?php foreach ($pendingReferrals as $r): ?>
                        <div class="dshbrd-list-item">
                            <div class="dshbrd-list-item__time"><?= date('M d', strtotime($r['referral_date'])) ?></div>
                            <div class="dshbrd-list-item__body">
                                <div class="dshbrd-list-item__title"><?= htmlspecialchars($r['student_name']) ?></div>
                                <div class="dshbrd-list-item__sub">#<?= htmlspecialchars($r['case_number']) ?> &middot; <?= htmlspecialchars($r['referral_source']) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="dshbrd-widget">
            <div class="dshbrd-widget__header">
                <h3>High-Risk Students</h3>
            </div>
            <div class="dshbrd-widget__body">
                <?php if (empty($highRiskStudents)): ?>
                    <div class="dshbrd-empty">No students currently flagged high-risk.</div>
                <?php else: ?>
                    <?php foreach ($highRiskStudents as $s): ?>
                        <div class="dshbrd-list-item">
                            <div class="dshbrd-list-item__body">
                                <div class="dshbrd-list-item__title"><?= htmlspecialchars($s['student_name']) ?></div>
                                <div class="dshbrd-list-item__sub">#<?= htmlspecialchars($s['student_number']) ?> &middot; <?= htmlspecialchars($s['guidance_status']) ?></div>
                            </div>
                            <span class="dshbrd-badge dshbrd-badge--high-risk">High Risk</span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="dshbrd-widget">
            <div class="dshbrd-widget__header">
                <h3>Recent Incident Reports</h3>
            </div>
            <div class="dshbrd-widget__body">
                <?php if (empty($recentIncidents)): ?>
                    <div class="dshbrd-empty">No recent incidents.</div>
                <?php else: ?>
                    <?php foreach ($recentIncidents as $i): ?>
                        <div class="dshbrd-list-item">
                            <div class="dshbrd-list-item__time"><?= date('M d', strtotime($i['incident_date'])) ?></div>
                            <div class="dshbrd-list-item__body">
                                <div class="dshbrd-list-item__title"><?= htmlspecialchars($i['student_name']) ?></div>
                                <div class="dshbrd-list-item__sub"><?= htmlspecialchars($i['incident_type']) ?></div>
                            </div>
                            <span class="dshbrd-badge <?= dshbrd_severity_badge_class($i['severity']) ?>"><?= htmlspecialchars($i['severity']) ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>

</div>
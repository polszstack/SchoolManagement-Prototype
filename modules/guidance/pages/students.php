<?php
/**
 * students.php
 * Module: Student Profile Management (Guidance Office)
 *
 * Renders server-side directly from the Students model, same pattern as
 * approval-submission.php. StudentsController.php (AJAX) is only used
 * afterward for search/filter refreshes, remarks, and document uploads —
 * not for this initial render.
 *
 * NOTE: rgr_students.year_level is defined as YEAR(4), which normally
 * stores a calendar year (e.g. 2026), not an ordinal level like "1st Year".
 * This mockup assumes it's actually being used to store 1-4 as a year
 * standing — please confirm which it really is; if it's a literal
 * calendar year, the filter options/labels below need to change to list
 * actual years instead of "1st/2nd/3rd/4th Year".
 */

include_once __DIR__ . '/../../../auth/session.php';
include_once __DIR__ . '/../classes/Students.php';

$studentsClass = new Students();

$summary = $studentsClass->getSummaryCounts();

$filters = [
    'search'     => trim($_GET['search'] ?? ''),
    'year_level' => $_GET['year_level'] ?? '',
    'section'    => $_GET['section'] ?? '',
    'course'     => $_GET['course'] ?? '',
    'risk'       => $_GET['risk'] ?? '',
    'status'     => $_GET['status'] ?? '',
];
$page     = max(1, (int) ($_GET['page'] ?? 1));
$pageSize = 10;

$result        = $studentsClass->getList($filters, $page, $pageSize);
$students       = $result['rows'];
$totalStudents  = $result['total'];
$totalPages     = (int) ceil($totalStudents / $pageSize);

// The modal is populated live via AJAX (students.js -> StudentsController.php)
// the moment "View Profile" is clicked, so no profile detail needs to be
// queried on this initial page render. $profileDetail below only exists to
// give the modal markup real field names/structure to bind to; every value
// gets overwritten on first click.
$profileDetail = [
    'student_number' => '', 'name' => '', 'year_level' => '', 'section' => '',
    'course' => '', 'gender' => '', 'birth_date' => '', 'email' => '', 'phone' => '',
    'academic_status' => '', 'risk_level' => 'Low', 'guidance_status' => 'Active',
    'remarks_history' => [], 'case_history' => [], 'case_summary' => ['total' => 0],
    'appointment_history' => [], 'incident_history' => [], 'documents' => [],
];

function std_risk_badge_class($level) {
    return match ($level) {
        'High' => 'std-badge--risk-high',
        'Moderate' => 'std-badge--risk-moderate',
        default => 'std-badge--risk-low',
    };
}

function std_status_badge_class($status) {
    return match ($status) {
        'Active' => 'std-badge--status-active',
        'Monitoring' => 'std-badge--status-monitoring',
        default => 'std-badge--status-closed',
    };
}
?>

<div class="module-header">
    <h1>Students</h1>
</div>

<div class="module-content">

    <!-- Summary at a glance -->
    <div class="std-stats-row">
        <div class="std-stat-card">
            <span class="std-stat-card__label">Total Students</span>
            <span class="std-stat-card__value"><?= htmlspecialchars($summary['total']) ?></span>
        </div>
        <div class="std-stat-card std-stat-card--accent">
            <span class="std-stat-card__label">Active Cases</span>
            <span class="std-stat-card__value"><?= htmlspecialchars($summary['active']) ?></span>
        </div>
        <div class="std-stat-card">
            <span class="std-stat-card__label">Under Monitoring</span>
            <span class="std-stat-card__value"><?= htmlspecialchars($summary['monitoring']) ?></span>
        </div>
        <div class="std-stat-card std-stat-card--high">
            <span class="std-stat-card__label">High Risk</span>
            <span class="std-stat-card__value"><?= htmlspecialchars($summary['high_risk']) ?></span>
        </div>
    </div>

    <!-- Toolbar: search + filters -->
    <div class="std-toolbar">
        <div class="std-toolbar__filters">
            <div class="std-search-box">
                <i class="fa fa-search"></i>
                <input type="text" id="stdSearchInput" placeholder="Search by name or student number..." value="<?= htmlspecialchars($filters['search']) ?>">
            </div>

            <select class="std-filter-select" id="stdFilterYear">
                <option value="" <?= $filters['year_level'] === '' ? 'selected' : '' ?>>All Year Levels</option>
                <option value="1" <?= $filters['year_level'] == 1 ? 'selected' : '' ?>>1st Year</option>
                <option value="2" <?= $filters['year_level'] == 2 ? 'selected' : '' ?>>2nd Year</option>
                <option value="3" <?= $filters['year_level'] == 3 ? 'selected' : '' ?>>3rd Year</option>
                <option value="4" <?= $filters['year_level'] == 4 ? 'selected' : '' ?>>4th Year</option>
            </select>

            <select class="std-filter-select" id="stdFilterSection">
                <option value="" <?= $filters['section'] === '' ? 'selected' : '' ?>>All Sections</option>
                <option value="BSCS-3A" <?= $filters['section'] === 'BSCS-3A' ? 'selected' : '' ?>>BSCS-3A</option>
                <option value="BSBA-2B" <?= $filters['section'] === 'BSBA-2B' ? 'selected' : '' ?>>BSBA-2B</option>
                <option value="BSIT-4A" <?= $filters['section'] === 'BSIT-4A' ? 'selected' : '' ?>>BSIT-4A</option>
            </select>

            <select class="std-filter-select" id="stdFilterCourse">
                <option value="" <?= $filters['course'] === '' ? 'selected' : '' ?>>All Courses</option>
                <option value="BS Computer Science" <?= $filters['course'] === 'BS Computer Science' ? 'selected' : '' ?>>BS Computer Science</option>
                <option value="BS Information Technology" <?= $filters['course'] === 'BS Information Technology' ? 'selected' : '' ?>>BS Information Technology</option>
                <option value="BS Business Administration" <?= $filters['course'] === 'BS Business Administration' ? 'selected' : '' ?>>BS Business Administration</option>
            </select>

            <select class="std-filter-select" id="stdFilterRisk">
                <option value="" <?= $filters['risk'] === '' ? 'selected' : '' ?>>All Risk Levels</option>
                <option value="Low" <?= $filters['risk'] === 'Low' ? 'selected' : '' ?>>Low</option>
                <option value="Moderate" <?= $filters['risk'] === 'Moderate' ? 'selected' : '' ?>>Moderate</option>
                <option value="High" <?= $filters['risk'] === 'High' ? 'selected' : '' ?>>High</option>
            </select>

            <select class="std-filter-select" id="stdFilterStatus">
                <option value="" <?= $filters['status'] === '' ? 'selected' : '' ?>>All Guidance Status</option>
                <option value="Active" <?= $filters['status'] === 'Active' ? 'selected' : '' ?>>Active</option>
                <option value="Monitoring" <?= $filters['status'] === 'Monitoring' ? 'selected' : '' ?>>Monitoring</option>
                <option value="Closed" <?= $filters['status'] === 'Closed' ? 'selected' : '' ?>>Closed</option>
            </select>
        </div>

        <div class="std-toolbar__actions">
            <button type="button" class="std-btn std-btn--outline" id="stdExportBtn">
                <i class="fa fa-download"></i> Export
            </button>
        </div>
    </div>

    <!-- Student list -->
    <div class="std-table-wrapper">
        <table class="std-table">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Year &amp; Section</th>
                    <th>Course</th>
                    <th>Risk Level</th>
                    <th>Guidance Status</th>
                    <th>Last Updated</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($students)): ?>
                <tr>
                    <td colspan="7" class="std-table__empty">No students found for the selected filters.</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($students as $s): ?>
                    <tr>
                        <td>
                            <div class="std-table__student-cell">
                                <div class="std-avatar"><?= htmlspecialchars($s['initials']) ?></div>
                                <div>
                                    <div class="std-table__name"><?= htmlspecialchars($s['name']) ?></div>
                                    <div class="std-table__subtext">#<?= htmlspecialchars($s['student_number']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td>Year <?= htmlspecialchars($s['year_level']) ?> - <?= htmlspecialchars($s['section']) ?></td>
                        <td><?= htmlspecialchars($s['course']) ?></td>
                        <td>
                            <span class="std-badge <?= std_risk_badge_class($s['risk_level']) ?>">
                                <?= htmlspecialchars($s['risk_level']) ?>
                            </span>
                        </td>
                        <td>
                            <span class="std-badge <?= std_status_badge_class($s['guidance_status']) ?>">
                                <?= htmlspecialchars($s['guidance_status']) ?>
                            </span>
                        </td>
                        <td class="std-table__subtext"><?= date('M d, Y', strtotime($s['updated_at'])) ?></td>
                        <td>
                            <button type="button"
                                    class="std-btn std-btn--outline std-btn--sm std-view-profile-btn"
                                    data-profile-id="<?= htmlspecialchars($s['profile_id']) ?>"
                                    data-student-number="<?= htmlspecialchars($s['student_number']) ?>">
                                View Profile
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="std-pagination">
        <span>
            <?php if ($totalStudents > 0): ?>
                Showing <?= (($page - 1) * $pageSize) + 1 ?>-<?= min($page * $pageSize, $totalStudents) ?> of <?= $totalStudents ?> students
            <?php else: ?>
                No students found
            <?php endif; ?>
        </span>
        <div class="std-pagination__pages">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <button class="std-pagination__page <?= $i === $page ? 'std-pagination__page--active' : '' ?>" data-page="<?= $i ?>"><?= $i ?></button>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
                <button class="std-pagination__page" data-page="<?= $page + 1 ?>">&rsaquo;</button>
            <?php endif; ?>
        </div>
    </div>

</div>

<!-- ============================================================
     Student Profile Modal (example populated with $profileDetail)
     ============================================================ -->
<div class="std-modal-overlay" id="stdProfileModal">
    <div class="std-modal">

        <div class="std-modal__header">
            <div class="std-modal__title">
                <div class="std-avatar" id="stdModalAvatar" style="width:44px;height:44px;font-size:14px;"></div>
                <div>
                    <h2><?= htmlspecialchars($profileDetail['name']) ?></h2>
                    <div class="std-modal__subtitle">
                        #<?= htmlspecialchars($profileDetail['student_number']) ?> &middot;
                        <?= htmlspecialchars($profileDetail['course']) ?> &middot;
                        Year <?= htmlspecialchars($profileDetail['year_level']) ?> - <?= htmlspecialchars($profileDetail['section']) ?>
                    </div>
                </div>
            </div>
            <button type="button" class="std-modal__close" id="stdCloseModalBtn">&times;</button>
        </div>

        <div class="std-tabs">
            <button class="std-tab std-tab--active" data-tab="overview">Overview</button>
            <button class="std-tab" data-tab="remarks">Guidance &amp; Remarks</button>
            <button class="std-tab" data-tab="cases">Cases</button>
            <button class="std-tab" data-tab="appointments">Appointment History</button>
            <button class="std-tab" data-tab="incidents">Incident History</button>
            <button class="std-tab" data-tab="documents">Documents</button>
        </div>

        <div class="std-modal__body">

            <!-- Overview -->
            <div class="std-tab-panel std-tab-panel--active" data-panel="overview">
                <div class="std-profile-grid">
                    <div>
                        <div class="std-profile-field__label">Student Number</div>
                        <div class="std-profile-field__value">#<?= htmlspecialchars($profileDetail['student_number']) ?></div>
                    </div>
                    <div>
                        <div class="std-profile-field__label">Course</div>
                        <div class="std-profile-field__value"><?= htmlspecialchars($profileDetail['course']) ?></div>
                    </div>
                    <div>
                        <div class="std-profile-field__label">Year Level &amp; Section</div>
                        <div class="std-profile-field__value">Year <?= htmlspecialchars($profileDetail['year_level']) ?> - <?= htmlspecialchars($profileDetail['section']) ?></div>
                    </div>
                    <div>
                        <div class="std-profile-field__label">Gender</div>
                        <div class="std-profile-field__value"><?= htmlspecialchars(ucfirst($profileDetail['gender'])) ?></div>
                    </div>
                    <div>
                        <div class="std-profile-field__label">Birth Date</div>
                        <div class="std-profile-field__value"><?= htmlspecialchars($profileDetail['birth_date']) ?></div>
                    </div>
                    <div>
                        <div class="std-profile-field__label">Email</div>
                        <div class="std-profile-field__value"><?= htmlspecialchars($profileDetail['email']) ?></div>
                    </div>
                    <div>
                        <div class="std-profile-field__label">Phone</div>
                        <div class="std-profile-field__value"><?= htmlspecialchars($profileDetail['phone']) ?></div>
                    </div>
                    <div>
                        <div class="std-profile-field__label">Academic Status</div>
                        <div class="std-profile-field__value"><?= htmlspecialchars(ucfirst($profileDetail['academic_status'])) ?></div>
                    </div>
                    <div>
                        <div class="std-profile-field__label">Risk Level</div>
                        <span class="std-badge <?= std_risk_badge_class($profileDetail['risk_level']) ?>">
                            <?= htmlspecialchars($profileDetail['risk_level']) ?>
                        </span>
                    </div>
                    <div>
                        <div class="std-profile-field__label">Guidance Status</div>
                        <span class="std-badge <?= std_status_badge_class($profileDetail['guidance_status']) ?>">
                            <?= htmlspecialchars($profileDetail['guidance_status']) ?>
                        </span>
                    </div>
                </div>

                <div class="std-profile-field__label" style="margin-top:16px;">Case Involvement</div>
                <div class="std-case-summary" id="stdCaseSummary">
                    <span class="std-badge std-badge--status-active">0 Cases</span>
                </div>
            </div>

            <!-- Guidance & Remarks -->
            <div class="std-tab-panel" data-panel="remarks">
                <form class="std-remarks-form" id="stdRemarksForm" data-skip>
                    <div class="std-profile-field__label">Add Guidance Remark</div>
                    <textarea name="remarks" placeholder="Enter observation, recommendation, or update..."></textarea>
                    <div class="std-remarks-form__actions">
                        <button type="submit" class="std-btn std-btn--sm">Save Remark</button>
                    </div>
                </form>

                <div class="std-remarks-history">
                    <?php foreach ($profileDetail['remarks_history'] as $r): ?>
                    <div class="std-remark-item">
                        <div class="std-remark-item__meta"><?= htmlspecialchars($r['date']) ?> &middot; <?= htmlspecialchars($r['by']) ?></div>
                        <div class="std-remark-item__text"><?= htmlspecialchars($r['text']) ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Cases -->
            <div class="std-tab-panel" data-panel="cases">
                <div class="std-case-list" id="stdCaseList">
                    <div class="std-table__empty">Loading cases...</div>
                </div>
            </div>

            <!-- Appointment History -->
            <div class="std-tab-panel" data-panel="appointments">
                <div class="std-history-list">
                    <?php foreach ($profileDetail['appointment_history'] as $h): ?>
                    <div class="std-history-item">
                        <div class="std-history-item__date"><?= htmlspecialchars($h['date']) ?></div>
                        <div class="std-history-item__body">
                            <div class="std-history-item__title"><?= htmlspecialchars($h['title']) ?></div>
                            <div class="std-history-item__desc"><?= htmlspecialchars($h['desc']) ?></div>
                        </div>
                        <span class="std-badge std-badge--status-active std-history-item__status"><?= htmlspecialchars($h['status']) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Incident History -->
            <div class="std-tab-panel" data-panel="incidents">
                <div class="std-history-list">
                    <?php foreach ($profileDetail['incident_history'] as $h): ?>
                    <div class="std-history-item">
                        <div class="std-history-item__date"><?= htmlspecialchars($h['date']) ?></div>
                        <div class="std-history-item__body">
                            <div class="std-history-item__title"><?= htmlspecialchars($h['title']) ?></div>
                            <div class="std-history-item__desc"><?= htmlspecialchars($h['desc']) ?></div>
                        </div>
                        <span class="std-badge std-badge--status-closed std-history-item__status"><?= htmlspecialchars($h['status']) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Documents -->
            <div class="std-tab-panel" data-panel="documents">
                <form class="std-upload-row" id="stdUploadForm" data-skip>
                    <select name="document_type">
                        <option value="Consent Form">Consent Form</option>
                        <option value="Assessment">Assessment</option>
                        <option value="Medical Record">Medical Record</option>
                        <option value="Other">Other</option>
                    </select>
                    <input type="file" name="document_file">
                    <button type="submit" class="std-btn std-btn--sm">Upload</button>
                </form>

                <div class="std-doc-list">
                    <?php foreach ($profileDetail['documents'] as $d): ?>
                    <div class="std-doc-item">
                        <div class="std-doc-item__info">
                            <i class="fa fa-file-pdf-o"></i>
                            <div>
                                <div class="std-doc-item__name"><?= htmlspecialchars($d['name']) ?></div>
                                <div class="std-doc-item__meta"><?= htmlspecialchars($d['type']) ?> &middot; <?= htmlspecialchars($d['date']) ?></div>
                            </div>
                        </div>
                        <button type="button" class="std-btn std-btn--ghost std-btn--sm">Download</button>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </div>
</div>
<?php
include_once __DIR__ . '/../../../database/db.php';
include_once __DIR__ . '/../classes/student.php';

$studentClass = new Student();
$students = $studentClass->getAllStudents();
if (!$students) $students = [];

// Generate CSRF token
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
?>

<div class="module-header">
    <h1>Clinic Visit &amp; Consultation</h1>
</div>

<div class="module-content">

    <!-- ── Result Flash ──────────────────────────────────────────────────── -->
    <div id="result" class="cv-result" style="display:none;">
        <h2>Consultation Submitted</h2>
        <div id="resultContent"></div>
        <button type="button" id="closeResultBtn" class="cv-btn cv-btn--info">Close</button>
    </div>

    <!-- Error message container -->
    <div id="errorMessage" class="cv-error" style="display:none; background:#ffebee; border:1px solid #ef9a9a; padding:12px; border-radius:6px; margin-bottom:16px; color:#c62828;">
        <strong>Error:</strong> <span id="errorText"></span>
        <button type="button" style="float:right; background:none; border:none; color:#c62828; cursor:pointer; font-weight:bold;" onclick="this.parentElement.style.display='none'">×</button>
    </div>

    <!-- ── Student Search ────────────────────────────────────────────────── -->
    <div class="cv-form-group">
        <label for="studentNumber">Enter Student Number:</label>
        <div class="cv-inline">
            <input type="text" id="studentNumber" placeholder="e.g. 2021-00001">
            <button type="button" id="searchBtn" class="cv-btn cv-btn--primary">Search</button>
            <button type="button" id="clearBtn"  class="cv-btn cv-btn--danger">Clear</button>
        </div>
    </div>

    <!-- ── Student Dropdown ──────────────────────────────────────────────── -->
    <div class="cv-form-group">
        <label for="studentSelect">Or Select from List:</label>
        <select id="studentSelect">
            <option value="">-- Select a Student --</option>
            <?php foreach ($students as $s) : ?>
                <option
                    value="<?= htmlspecialchars($s['student_number']) ?>"
                    data-id="<?= htmlspecialchars($s['id'] ?? '') ?>"
                    data-firstname="<?= htmlspecialchars($s['first_name']) ?>"
                    data-lastname="<?= htmlspecialchars($s['last_name']) ?>"
                    data-gender="<?= htmlspecialchars($s['gender'] ?? '') ?>"
                    data-birthdate="<?= htmlspecialchars($s['date_of_birth'] ?? '') ?>"
                    data-course="<?= htmlspecialchars($s['course'] ?? '') ?>"
                    data-yearlevel="<?= htmlspecialchars($s['year_level'] ?? '') ?>"
                >
                    <?= htmlspecialchars($s['student_number']) ?> –
                    <?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- ── Clinic Form ───────────────────────────────────────────────────── -->
    <form id="clinicForm" method="POST">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
        <input type="hidden" id="studentId" value="">

        <!-- Patient Info -->
        <fieldset class="cv-fieldset">
            <legend>Patient Information</legend>
            <div class="cv-grid cv-grid--2">
                <div class="cv-field">
                    <label>Student Number</label>
                    <input type="text" id="studentNumberDisplay" readonly>
                </div>
                <div class="cv-field">
                    <label>Full Name <span class="cv-required">*</span></label>
                    <input type="text" id="patientName" required readonly>
                </div>
                <div class="cv-field">
                    <label>Age</label>
                    <input type="text" id="patientAge" readonly>
                </div>
                <div class="cv-field">
                    <label>Gender</label>
                    <input type="text" id="patientGender" readonly>
                </div>
                <div class="cv-field">
                    <label>Course</label>
                    <input type="text" id="patientCourse" readonly>
                </div>
                <div class="cv-field">
                    <label>Year Level</label>
                    <input type="text" id="patientYearLevel" readonly>
                </div>
            </div>
        </fieldset>

        <!-- Vital Signs -->
        <fieldset class="cv-fieldset">
            <legend>Vital Signs</legend>
            <div class="cv-grid cv-grid--3">
                <div class="cv-field">
                    <label>Temperature (°C)</label>
                    <input type="number" id="temperature" step="0.1" min="35" max="42" placeholder="36.5" disabled>
                </div>
                <div class="cv-field">
                    <label>Blood Pressure (mmHg)</label>
                    <input type="text" id="bloodPressure" placeholder="120/80" pattern="\d{2,3}/\d{2,3}" title="Please use format: 120/80" disabled>
                </div>
                <div class="cv-field">
                    <label>Heart Rate (bpm)</label>
                    <input type="number" id="heartRate" min="40" max="200" placeholder="75" disabled>
                </div>
                <div class="cv-field">
                    <label>Respiratory Rate</label>
                    <input type="number" id="respiratoryRate" min="8" max="40" placeholder="16" disabled>
                </div>
                <div class="cv-field">
                    <label>O₂ Saturation (%)</label>
                    <input type="number" id="oxygenSaturation" min="70" max="100" placeholder="98" disabled>
                </div>
            </div>
        </fieldset>

        <!-- Appointment Details -->
        <fieldset class="cv-fieldset">
            <legend>Appointment Details</legend>
            <div class="cv-grid cv-grid--2">
                <div class="cv-field">
                    <label>Nurse <span class="cv-required">*</span></label>
                    <select id="nurse" required disabled>
                        <option value="">Select Nurse</option>
                        <option value="Nurse Maria Carla Serenas">Nurse Serenas</option>
                        <option value="Nurse John Dela Cruz">Nurse John Dela Cruz</option>
                        <option value="Nurse Ana Reyes">Nurse Ana Reyes</option>
                        <option value="Nurse Michael Tan">Nurse Michael Tan</option>
                    </select>
                </div>
                <div class="cv-field">
                    <label>Visit Type <span class="cv-required">*</span></label>
                    <select id="visitType" required disabled>
                        <option value="">Select Visit Type</option>
                        <option value="Check-up">Check-up</option>
                        <option value="Consultation">Consultation</option>
                        <option value="Emergency">Emergency</option>
                        <option value="Follow-up">Follow-up</option>
                        <option value="Medication">Medication</option>
                    </select>
                </div>
                <div class="cv-field">
                    <label>Date <span class="cv-required">*</span></label>
                    <input type="date" id="appointmentDate" required disabled>
                </div>
                <div class="cv-field">
                    <label>Time <span class="cv-required">*</span></label>
                    <input type="time" id="appointmentTime" required disabled>
                </div>
            </div>
        </fieldset>

        <!-- Consultation Notes -->
        <fieldset class="cv-fieldset">
            <legend>Consultation Notes</legend>
            <div class="cv-grid cv-grid--2">
                <div class="cv-field">
                    <label>Symptoms</label>
                    <textarea id="symptoms" rows="3" placeholder="Enter symptoms…" disabled></textarea>
                </div>
                <div class="cv-field">
                    <label>Diagnosis</label>
                    <textarea id="diagnosis" rows="3" placeholder="Enter diagnosis…" disabled></textarea>
                </div>
                <div class="cv-field">
                    <label>Treatment / Medication</label>
                    <textarea id="treatment" rows="3" placeholder="Enter treatment…" disabled></textarea>
                </div>
                <div class="cv-field">
                    <label>Additional Notes</label>
                    <textarea id="notes" rows="3" placeholder="Additional notes…" disabled></textarea>
                </div>
            </div>
        </fieldset>

        <div class="cv-form-actions">
            <button type="submit" id="submitBtn" class="cv-btn cv-btn--primary" disabled>Submit Consultation</button>
            <button type="button" id="resetFormBtn" class="cv-btn cv-btn--danger">Reset Form</button>
        </div>
    </form><!-- /clinicForm -->

    <!-- ════════════════════════════════════════════════════════════════════ -->
    <!--  AUDIT / CONSULTATION LOG                                           -->
    <!-- ════════════════════════════════════════════════════════════════════ -->
    <div class="cv-audit-section">
        <div class="cv-audit-header">
            <h2 class="cv-audit-title">
                <span class="cv-audit-icon">📋</span> Consultation Log
            </h2>
            <div class="cv-audit-toolbar">
                <input type="text" id="auditSearch" class="cv-audit-search"
                       placeholder="Search by student, nurse, visit type…">
                <button type="button" id="auditSearchBtn" class="cv-btn cv-btn--primary cv-btn--sm">Search</button>
                <button type="button" id="auditClearBtn"  class="cv-btn cv-btn--secondary cv-btn--sm">Clear</button>
                <button type="button" id="auditExportBtn" class="cv-btn cv-btn--success cv-btn--sm">⬇ Export CSV</button>
            </div>
        </div>

        <!-- Stats bar -->
        <div class="cv-audit-stats" id="auditStats">
            Loading records…
        </div>

        <!-- Table -->
        <div class="cv-table-wrap">
            <table class="cv-table" id="auditTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student #</th>
                        <th>Patient Name</th>
                        <th>Nurse</th>
                        <th>Visit Type</th>
                        <th>Date &amp; Time</th>
                        <th>Vital Signs</th>
                        <th>Diagnosis</th>
                        <th>Submitted At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="auditTableBody">
                    <tr><td colspan="10" class="cv-no-data">Loading…</td></tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="cv-audit-pagination" id="auditPagination"></div>
    </div><!-- /cv-audit-section -->
</div><!-- /module-content -->

<!-- ════════════════════════════════════════════════════════════════════════ -->
<!--  DETAIL MODAL                                                           -->
<!-- ════════════════════════════════════════════════════════════════════════ -->
<div id="auditModal" class="cv-modal" style="display:none;" role="dialog" aria-modal="true">
    <div class="cv-modal-backdrop" id="auditModalBackdrop"></div>
    <div class="cv-modal-box">
        <div class="cv-modal-header">
            <h3 id="modalTitle">Consultation Detail</h3>
            <button type="button" id="modalCloseBtn" class="cv-modal-close" aria-label="Close">&times;</button>
        </div>
        <div class="cv-modal-body" id="modalBody">Loading…</div>
        <div class="cv-modal-footer">
            <button type="button" id="modalPrintBtn" class="cv-btn cv-btn--info">🖨 Print</button>
            <button type="button" id="modalCloseBtn2" class="cv-btn cv-btn--secondary">Close</button>
        </div>
    </div>
</div>

<!-- ════════════════════════════════════════════════════════════════════════ -->
<!--  HIDDEN PRINT AREA                                                      -->
<!-- ════════════════════════════════════════════════════════════════════════ -->
<div id="printArea" style="display:none;"></div>

<!-- ════════════════════════════════════════════════════════════════════════ -->
<!--  STYLES                                                                 -->
<!-- ════════════════════════════════════════════════════════════════════════ -->
<style>
/* ── Variables ─────────────────────────────────────────────── */
:root {
    --cv-primary:   #1a6fc4;
    --cv-primary-d: #155a9e;
    --cv-danger:    #d63031;
    --cv-danger-d:  #b52828;
    --cv-success:   #00897b;
    --cv-success-d: #00695c;
    --cv-info:      #2980b9;
    --cv-info-d:    #1f6fa0;
    --cv-secondary: #636e72;
    --cv-border:    #dfe6e9;
    --cv-bg:        #f8fafc;
    --cv-white:     #ffffff;
    --cv-text:      #2d3436;
    --cv-muted:     #636e72;
    --cv-radius:    6px;
    --cv-shadow:    0 2px 8px rgba(0,0,0,.08);
}

/* ── Buttons ───────────────────────────────────────────────── */
.cv-btn {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 9px 18px; border: none; border-radius: var(--cv-radius);
    font-size: 14px; font-weight: 600; cursor: pointer;
    transition: background .18s, transform .1s;
}
.cv-btn:active { transform: scale(.97); }
.cv-btn:disabled { opacity: .5; cursor: not-allowed; }
.cv-btn--sm { padding: 6px 12px; font-size: 13px; }
.cv-btn--primary   { background: var(--cv-primary);   color: #fff; }
.cv-btn--primary:hover:not(:disabled)   { background: var(--cv-primary-d); }
.cv-btn--danger    { background: var(--cv-danger);    color: #fff; }
.cv-btn--danger:hover:not(:disabled)    { background: var(--cv-danger-d); }
.cv-btn--success   { background: var(--cv-success);   color: #fff; }
.cv-btn--success:hover:not(:disabled)   { background: var(--cv-success-d); }
.cv-btn--info      { background: var(--cv-info);      color: #fff; }
.cv-btn--info:hover:not(:disabled)      { background: var(--cv-info-d); }
.cv-btn--secondary { background: var(--cv-secondary); color: #fff; }
.cv-btn--secondary:hover:not(:disabled) { background: #4a545a; }

/* ── Form helpers ──────────────────────────────────────────── */
.cv-form-group   { margin-bottom: 14px; }
.cv-form-group label { display: block; font-weight: 600; margin-bottom: 5px; color: var(--cv-text); }
.cv-inline       { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
.cv-inline input { flex: 1; min-width: 180px; }
.cv-required     { color: var(--cv-danger); }

.cv-fieldset {
    border: 1px solid var(--cv-border);
    border-radius: var(--cv-radius);
    padding: 16px 18px;
    margin-bottom: 16px;
    background: var(--cv-white);
    box-shadow: var(--cv-shadow);
}
.cv-fieldset legend {
    font-weight: 700; color: var(--cv-primary);
    padding: 0 8px; font-size: 14px; letter-spacing: .4px;
}
.cv-grid { display: grid; gap: 12px; }
.cv-grid--2 { grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); }
.cv-grid--3 { grid-template-columns: repeat(auto-fill, minmax(170px, 1fr)); }
.cv-field label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px; color: var(--cv-text); }

input[type="text"], input[type="number"], input[type="date"],
input[type="time"], select, textarea {
    width: 100%; padding: 8px 10px;
    border: 1px solid var(--cv-border); border-radius: var(--cv-radius);
    font-size: 14px; color: var(--cv-text); background: var(--cv-white);
    box-sizing: border-box; transition: border-color .15s;
}
input:focus, select:focus, textarea:focus {
    outline: none; border-color: var(--cv-primary);
    box-shadow: 0 0 0 3px rgba(26,111,196,.12);
}
input[readonly], input:disabled, select:disabled, textarea:disabled {
    background: #f1f3f5; color: var(--cv-muted); cursor: not-allowed;
}
input:invalid { border-color: #ff6b6b; }
textarea { resize: vertical; }

.cv-form-actions {
    display: flex; gap: 10px; justify-content: center; margin-top: 18px;
}

/* ── Result flash ──────────────────────────────────────────── */
.cv-result {
    background: #e8f5e9; border: 1px solid #a5d6a7;
    border-radius: var(--cv-radius); padding: 16px;
    margin-bottom: 20px; color: #1b5e20;
}
.cv-result h2 { margin-top: 0; color: #2e7d32; }

.cv-error {
    background: #ffebee; border: 1px solid #ef9a9a;
    border-radius: var(--cv-radius); padding: 16px;
    margin-bottom: 20px; color: #c62828;
}

/* ── Audit Section ─────────────────────────────────────────── */
.cv-audit-section {
    margin-top: 36px;
    border: 1px solid var(--cv-border);
    border-radius: var(--cv-radius);
    background: var(--cv-white);
    box-shadow: var(--cv-shadow);
    overflow: hidden;
}
.cv-audit-header {
    display: flex; align-items: center; flex-wrap: wrap;
    gap: 10px; padding: 14px 18px;
    background: linear-gradient(135deg, #1a6fc4 0%, #0d4f96 100%);
    color: #fff;
}
.cv-audit-title {
    font-size: 18px; font-weight: 700; margin: 0; flex: 1;
    display: flex; align-items: center; gap: 8px;
}
.cv-audit-icon { font-size: 20px; }
.cv-audit-toolbar { display: flex; gap: 6px; align-items: center; flex-wrap: wrap; }
.cv-audit-search {
    padding: 6px 10px; border-radius: var(--cv-radius);
    border: none; font-size: 13px; width: 220px;
    background: rgba(255,255,255,.9); color: var(--cv-text);
}
.cv-audit-search:focus { outline: none; background: #fff; }

.cv-audit-stats {
    padding: 8px 18px; font-size: 13px;
    background: #f0f4fa; color: var(--cv-muted);
    border-bottom: 1px solid var(--cv-border);
}

/* ── Table ─────────────────────────────────────────────────── */
.cv-table-wrap { overflow-x: auto; }
.cv-table {
    width: 100%; border-collapse: collapse; font-size: 13px;
}
.cv-table thead th {
    background: #f8fafc; padding: 10px 12px;
    text-align: left; font-weight: 700; color: var(--cv-text);
    border-bottom: 2px solid var(--cv-border);
    white-space: nowrap;
}
.cv-table tbody tr { border-bottom: 1px solid #f0f4f8; transition: background .12s; }
.cv-table tbody tr:hover { background: #f5f9ff; }
.cv-table td { padding: 9px 12px; vertical-align: middle; color: var(--cv-text); }
.cv-no-data { text-align: center; color: var(--cv-muted); padding: 24px !important; }

/* Visit type badges */
.cv-badge {
    display: inline-block; padding: 2px 8px;
    border-radius: 20px; font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .4px;
}
.cv-badge--check-up     { background: #e3f2fd; color: #1565c0; }
.cv-badge--consultation { background: #e8f5e9; color: #2e7d32; }
.cv-badge--emergency    { background: #ffebee; color: #c62828; }
.cv-badge--follow-up    { background: #fff3e0; color: #e65100; }
.cv-badge--medication   { background: #f3e5f5; color: #6a1b9a; }
.cv-badge--default      { background: #eceff1; color: #37474f; }

/* Action buttons in table */
.cv-row-actions { display: flex; gap: 4px; }
.cv-btn-icon {
    padding: 4px 9px; border: none; border-radius: 4px;
    font-size: 12px; cursor: pointer; font-weight: 600;
    transition: opacity .15s;
}
.cv-btn-icon:hover { opacity: .8; }
.cv-btn-icon--view   { background: #e3f2fd; color: #1565c0; }
.cv-btn-icon--delete { background: #ffebee; color: #c62828; }

/* ── Pagination ────────────────────────────────────────────── */
.cv-audit-pagination {
    padding: 10px 18px; display: flex;
    align-items: center; justify-content: flex-end;
    gap: 4px; flex-wrap: wrap;
    border-top: 1px solid var(--cv-border);
    background: #fafbfc;
}
.cv-page-btn {
    padding: 5px 11px; border: 1px solid var(--cv-border);
    border-radius: 4px; background: #fff; cursor: pointer;
    font-size: 13px; color: var(--cv-text); transition: all .15s;
}
.cv-page-btn:hover:not(:disabled)   { background: var(--cv-primary); color: #fff; border-color: var(--cv-primary); }
.cv-page-btn.active  { background: var(--cv-primary); color: #fff; border-color: var(--cv-primary); font-weight: 700; }
.cv-page-btn:disabled { opacity: .4; cursor: not-allowed; }
.cv-page-ellipsis { color: var(--cv-muted); padding: 0 4px; }

/* ── Modal ─────────────────────────────────────────────────── */
.cv-modal { position: fixed; inset: 0; z-index: 9999; display: flex; align-items: center; justify-content: center; }
.cv-modal-backdrop { position: absolute; inset: 0; background: rgba(0,0,0,.45); }
.cv-modal-box {
    position: relative; background: #fff;
    border-radius: 10px; width: 700px; max-width: 95vw;
    max-height: 90vh; display: flex; flex-direction: column;
    box-shadow: 0 20px 60px rgba(0,0,0,.25);
    animation: cvModalIn .2s ease;
}
@keyframes cvModalIn {
    from { opacity: 0; transform: translateY(-20px) scale(.97); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}
.cv-modal-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 20px; border-bottom: 1px solid var(--cv-border);
    background: linear-gradient(135deg, #1a6fc4 0%, #0d4f96 100%);
    border-radius: 10px 10px 0 0;
}
.cv-modal-header h3 { margin: 0; color: #fff; font-size: 17px; }
.cv-modal-close {
    background: none; border: none; color: rgba(255,255,255,.8);
    font-size: 26px; cursor: pointer; line-height: 1;
    padding: 0 4px; transition: color .15s;
}
.cv-modal-close:hover { color: #fff; }
.cv-modal-body { padding: 20px; overflow-y: auto; flex: 1; }
.cv-modal-footer {
    padding: 12px 20px; border-top: 1px solid var(--cv-border);
    display: flex; gap: 8px; justify-content: flex-end;
    background: #fafbfc; border-radius: 0 0 10px 10px;
}

/* ── Modal detail layout ───────────────────────────────────── */
.cv-detail-grid {
    display: grid; grid-template-columns: repeat(auto-fill, minmax(200px,1fr));
    gap: 10px; margin-bottom: 14px;
}
.cv-detail-card {
    background: #f8fafc; border: 1px solid var(--cv-border);
    border-radius: 6px; padding: 10px 12px;
}
.cv-detail-card .cv-dc-label {
    font-size: 11px; font-weight: 700; color: var(--cv-muted);
    text-transform: uppercase; letter-spacing: .5px; margin-bottom: 3px;
}
.cv-detail-card .cv-dc-value {
    font-size: 14px; color: var(--cv-text); font-weight: 600;
}
.cv-detail-section-title {
    font-size: 13px; font-weight: 700; color: var(--cv-primary);
    text-transform: uppercase; letter-spacing: .5px;
    border-bottom: 2px solid #e8f0fe; padding-bottom: 4px;
    margin: 16px 0 8px;
}
.cv-detail-notes { font-size: 13px; color: var(--cv-text); line-height: 1.6; }
.cv-detail-notes p { margin: 4px 0; }
.cv-detail-notes strong { color: var(--cv-muted); font-weight: 600; }

/* ── Print styles ──────────────────────────────────────────── */
@media print {
    body > *:not(#printArea) { display: none !important; }
    #printArea {
        display: block !important;
        font-family: Arial, sans-serif; font-size: 13px; color: #000;
    }
    .no-print { display: none !important; }
}
</style>

<!-- ════════════════════════════════════════════════════════════════════════ -->
<!--  JAVASCRIPT                                                             -->
<!-- ════════════════════════════════════════════════════════════════════════ -->
<script>
(function () {
'use strict';

// ── State ────────────────────────────────────────────────────────────────────
let auditPage       = 1;
let auditSearch     = '';
let auditTotalPages = 1;
let currentModalId  = null;
const csrfToken     = '<?= $csrf_token ?>';

// ── Helpers ──────────────────────────────────────────────────────────────────
function esc(v) {
    if (v === null || v === undefined) return '';
    const d = document.createElement('div');
    d.textContent = String(v);
    return d.innerHTML;
}

function showError(message) {
    const errorDiv = document.getElementById('errorMessage');
    const errorText = document.getElementById('errorText');
    errorText.textContent = message;
    errorDiv.style.display = 'block';
    setTimeout(() => { errorDiv.style.display = 'none'; }, 5000);
}

function calculateAge(birthdate) {
    if (!birthdate) return '';
    const today = new Date(), dob = new Date(birthdate);
    let age = today.getFullYear() - dob.getFullYear();
    const m = today.getMonth() - dob.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;
    return age;
}

function visitBadge(type) {
    const key = (type || '').toLowerCase().replace(/\s+/g, '-');
    const cls = ['check-up', 'consultation', 'emergency', 'follow-up', 'medication'].includes(key) ? key : 'default';
    return `<span class="cv-badge cv-badge--${cls}">${esc(type)}</span>`;
}

function formatDateTime(dateStr, timeStr) {
    if (!dateStr) return '—';
    return esc(dateStr) + (timeStr ? ' ' + esc(timeStr) : '');
}

function vitalSummary(row) {
    const parts = [];
    if (row.temperature)     parts.push(`${row.temperature}°C`);
    if (row.blood_pressure)  parts.push(`BP ${row.blood_pressure}`);
    if (row.heart_rate)      parts.push(`HR ${row.heart_rate}`);
    if (row.oxygen_sat)      parts.push(`O₂ ${row.oxygen_sat}%`);
    return parts.length ? esc(parts.join(' | ')) : '<span style="color:#aaa">—</span>';
}

function formatSubmittedAt(dt) {
    if (!dt) return '—';
    try {
        const d = new Date(dt);
        if (isNaN(d.getTime())) return dt;
        return d.toLocaleDateString('en-PH', { year:'numeric', month:'short', day:'numeric' }) +
               ' ' + d.toLocaleTimeString('en-PH', { hour:'2-digit', minute:'2-digit' });
    } catch {
        return dt;
    }
}

// ── Form: enable/disable fields ──────────────────────────────────────────────
const FORM_FIELDS = [
    'temperature','bloodPressure','heartRate','respiratoryRate','oxygenSaturation',
    'nurse','visitType','appointmentDate','appointmentTime',
    'symptoms','diagnosis','treatment','notes','submitBtn'
];

function enableFormFields(on) {
    FORM_FIELDS.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.disabled = !on;
    });
}

function resetForm() {
    ['studentNumberDisplay','patientName','patientAge','patientGender','patientCourse','patientYearLevel','studentId'].forEach(id => {
        const el = document.getElementById(id); if (el) el.value = '';
    });
    ['temperature','bloodPressure','heartRate','respiratoryRate','oxygenSaturation'].forEach(id => {
        const el = document.getElementById(id); if (el) el.value = '';
    });
    ['nurse','visitType','symptoms','diagnosis','treatment','notes'].forEach(id => {
        const el = document.getElementById(id); if (el) el.value = '';
    });
    document.getElementById('appointmentDate').value = '';
    document.getElementById('appointmentTime').value = '';
    document.getElementById('studentSelect').value   = '';
    document.getElementById('studentNumber').value   = '';
    enableFormFields(false);
}

function fillPatientInfo(data) {
    document.getElementById('studentId').value             = data.id || '';
    document.getElementById('studentNumberDisplay').value  = data.student_number || '';
    document.getElementById('patientName').value           = (data.first_name + ' ' + data.last_name).trim();
    document.getElementById('patientGender').value         = data.gender || '';
    document.getElementById('patientCourse').value         = data.course || '';
    document.getElementById('patientYearLevel').value      = data.year_level || '';
    document.getElementById('patientAge').value            = calculateAge(data.birthdate);
    enableFormFields(true);
    
    // Set default date/time
    const now = new Date();
    document.getElementById('appointmentDate').value = now.toISOString().split('T')[0];
    document.getElementById('appointmentTime').value = 
        now.getHours().toString().padStart(2,'0') + ':' + 
        now.getMinutes().toString().padStart(2,'0');
}

// ── Student Search / Select ──────────────────────────────────────────────────
document.getElementById('searchBtn').addEventListener('click', function () {
    const num = document.getElementById('studentNumber').value.trim();
    if (!num) { showError('Please enter a student number.'); return; }
    
    const opt = Array.from(document.getElementById('studentSelect').options)
                     .find(o => o.value === num);
    if (opt) {
        document.getElementById('studentSelect').value = num;
        fillPatientInfo({
            id: opt.dataset.id,
            student_number: opt.value,
            first_name: opt.dataset.firstname, 
            last_name: opt.dataset.lastname,
            gender: opt.dataset.gender, 
            birthdate: opt.dataset.birthdate,
            course: opt.dataset.course, 
            year_level: opt.dataset.yearlevel
        });
    } else {
        showError('Student not found.');
        resetForm();
    }
});

document.getElementById('studentNumber').addEventListener('keydown', function (e) {
    if (e.key === 'Enter') { e.preventDefault(); document.getElementById('searchBtn').click(); }
});

document.getElementById('clearBtn').addEventListener('click', function () {
    document.getElementById('studentNumber').value = ''; 
    resetForm();
});

document.getElementById('studentSelect').addEventListener('change', function () {
    const opt = this.options[this.selectedIndex];
    if (!opt.value) { resetForm(); return; }
    document.getElementById('studentNumber').value = opt.value;
    fillPatientInfo({
        id: opt.dataset.id,
        student_number: opt.value,
        first_name: opt.dataset.firstname, 
        last_name: opt.dataset.lastname,
        gender: opt.dataset.gender, 
        birthdate: opt.dataset.birthdate,
        course: opt.dataset.course, 
        year_level: opt.dataset.yearlevel
    });
});

document.getElementById('resetFormBtn').addEventListener('click', function () {
    if (confirm('Reset the form?')) resetForm();
});

document.getElementById('closeResultBtn').addEventListener('click', function () {
    document.getElementById('result').style.display = 'none';
});

// ── BP format validation ──────────────────────────────────────────────────────
document.getElementById('bloodPressure').addEventListener('input', function () {
    const isValid = !this.value || /^\d{2,3}\/\d{2,3}$/.test(this.value);
    this.setCustomValidity(isValid ? '' : 'Use format 120/80');
    this.style.borderColor = isValid ? '' : '#ff6b6b';
});

// ── Form Submit → save to DB ─────────────────────────────────────────────────
document.getElementById('clinicForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    // Validate required fields
    const required = {
        'patientName': 'Patient name',
        'nurse': 'Nurse',
        'visitType': 'Visit type',
        'appointmentDate': 'Appointment date',
        'appointmentTime': 'Appointment time'
    };

    for (const [id, label] of Object.entries(required)) {
        const el = document.getElementById(id);
        if (!el || !el.value) {
            showError(`Please fill in ${label}`);
            el?.focus();
            return;
        }
    }

    // Validate BP format if provided
    const bp = document.getElementById('bloodPressure').value;
    if (bp && !/^\d{2,3}\/\d{2,3}$/.test(bp)) {
        showError('Blood pressure must be in format: 120/80');
        document.getElementById('bloodPressure').focus();
        return;
    }

    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Saving…';

    const payload = {
        csrf_token: csrfToken,
        student_id:       document.getElementById('studentId').value,
        student_number:   document.getElementById('studentNumberDisplay').value,
        patient_name:     document.getElementById('patientName').value,
        patient_age:      document.getElementById('patientAge').value,
        patient_gender:   document.getElementById('patientGender').value,
        patient_course:   document.getElementById('patientCourse').value,
        patient_year:     document.getElementById('patientYearLevel').value,
        nurse:            document.getElementById('nurse').value,
        visit_type:       document.getElementById('visitType').value,
        appointment_date: document.getElementById('appointmentDate').value,
        appointment_time: document.getElementById('appointmentTime').value,
        temperature:      document.getElementById('temperature').value || null,
        blood_pressure:   bp || null,
        heart_rate:       document.getElementById('heartRate').value || null,
        respiratory_rate: document.getElementById('respiratoryRate').value || null,
        oxygen_sat:       document.getElementById('oxygenSaturation').value || null,
        symptoms:         document.getElementById('symptoms').value || null,
        diagnosis:        document.getElementById('diagnosis').value || null,
        treatment:        document.getElementById('treatment').value || null,
        notes:            document.getElementById('notes').value || null,
    };

    try {
        const response = await fetch('../handlers/save_consultation.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const data = await response.json();
        
        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Save failed');
        }

        // Show result flash
        const rd = document.getElementById('resultContent');
        rd.innerHTML = `
            <p><strong>Patient:</strong> ${esc(payload.patient_name)}
               (${esc(payload.patient_gender || 'N/A')}, ${esc(payload.patient_age || '?')} yrs)</p>
            <p><strong>Student #:</strong> ${esc(payload.student_number || 'N/A')}</p>
            <p><strong>Nurse:</strong> ${esc(payload.nurse)} &nbsp;|&nbsp;
               <strong>Visit Type:</strong> ${esc(payload.visit_type)}</p>
            <p><strong>Date &amp; Time:</strong> ${esc(payload.appointment_date)} at ${esc(payload.appointment_time)}</p>
            <p style="color:#388e3c;font-weight:700;">✔ Consultation saved (Record #${data.id})</p>`;
        document.getElementById('result').style.display = 'block';
        document.getElementById('result').scrollIntoView({ behavior: 'smooth' });

        resetForm();
        loadAuditLog(1, ''); // refresh the audit table
    } catch (error) {
        showError('Error saving consultation: ' + error.message);
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Submit Consultation';
    }
});

// ════════════════════════════════════════════════════════════════════════════
//  AUDIT LOG
// ════════════════════════════════════════════════════════════════════════════

async function loadAuditLog(page = 1, search = '') {
    auditPage = page;
    auditSearch = search;

    const tbody = document.getElementById('auditTableBody');
    tbody.innerHTML = '<tr><td colspan="10" class="cv-no-data">Loading…</td></tr>';

    try {
        const params = new URLSearchParams({
            page: auditPage,
            search: auditSearch,
            limit: 10
        });
        
        const response = await fetch(`../handlers/get_consultations.php?${params}`);
        const data = await response.json();
        
        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Failed to load records');
        }
        
        renderAuditTable(data);
        renderAuditPagination(data);
        renderAuditStats(data);
    } catch (error) {
        tbody.innerHTML = `<tr><td colspan="10" class="cv-no-data">Error loading records: ${esc(error.message)}</td></tr>`;
    }
}

function renderAuditTable(res) {
    const tbody = document.getElementById('auditTableBody');
    if (!res.data || res.data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="10" class="cv-no-data">No consultation records found.</td></tr>';
        return;
    }

    let html = '';
    let rowNum = (res.currentPage - 1) * (res.limit || 10) + 1;
    
    res.data.forEach(function (r) {
        html += `<tr>
            <td style="color:var(--cv-muted);font-size:12px;">${rowNum++}</td>
            <td><strong>${esc(r.student_number || '—')}</strong></td>
            <td>${esc(r.patient_name)}<br>
                <small style="color:var(--cv-muted);">${esc(r.patient_course || '')} ${r.patient_year ? '– Yr ' + esc(r.patient_year) : ''}</small>
            </td>
            <td style="font-size:12px;">${esc(r.nurse)}</td>
            <td>${visitBadge(r.visit_type)}</td>
            <td style="white-space:nowrap;font-size:12px;">
                ${esc(r.appointment_date)}<br>
                <span style="color:var(--cv-muted);">${esc(r.appointment_time || '')}</span>
            </td>
            <td style="font-size:11px;color:var(--cv-muted);">${vitalSummary(r)}</td>
            <td style="font-size:12px;">${r.diagnosis ? esc(r.diagnosis).substring(0,60) + (r.diagnosis.length > 60 ? '…' : '') : '<span style="color:#aaa">—</span>'}</td>
            <td style="font-size:11px;color:var(--cv-muted);white-space:nowrap;">${formatSubmittedAt(r.submitted_at)}</td>
            <td>
                <div class="cv-row-actions">
                    <button type="button" class="cv-btn-icon cv-btn-icon--view"
                            data-id="${r.id}" title="View details">👁 View</button>
                    <button type="button" class="cv-btn-icon cv-btn-icon--delete"
                            data-id="${r.id}" data-name="${esc(r.patient_name)}" title="Delete">🗑</button>
                </div>
            </td>
        </tr>`;
    });
    tbody.innerHTML = html;

    // Bind action buttons
    tbody.querySelectorAll('.cv-btn-icon--view').forEach(function (btn) {
        btn.addEventListener('click', function () { openModal(parseInt(this.dataset.id)); });
    });
    tbody.querySelectorAll('.cv-btn-icon--delete').forEach(function (btn) {
        btn.addEventListener('click', function () { deleteRecord(parseInt(this.dataset.id), this.dataset.name); });
    });
}

function renderAuditStats(res) {
    const el = document.getElementById('auditStats');
    if (res.total === 0) {
        el.textContent = 'No records found.';
        return;
    }
    const start = (res.currentPage - 1) * (res.limit || 10) + 1;
    const end = Math.min(res.currentPage * (res.limit || 10), res.total);
    el.innerHTML = `Showing <strong>${start}</strong>–<strong>${end}</strong>
        of <strong>${res.total}</strong> consultation record${res.total !== 1 ? 's' : ''}
        ${auditSearch ? ' &nbsp;·&nbsp; Filtered by: <em>"' + esc(auditSearch) + '"</em>' : ''}`;
}

function renderAuditPagination(res) {
    auditTotalPages = res.totalPages || 1;
    const container = document.getElementById('auditPagination');
    if (auditTotalPages <= 1) { container.innerHTML = ''; return; }

    const cur = res.currentPage, tot = auditTotalPages;
    let html = '';

    const btn = (label, page, disabled, active) =>
        `<button type="button" class="cv-page-btn${active ? ' active' : ''}"
                 data-page="${page}" ${disabled ? 'disabled' : ''}>${label}</button>`;

    html += btn('«', 1, cur === 1, false);
    html += btn('‹', cur - 1, cur === 1, false);

    const win = 2, start = Math.max(1, cur - win), end = Math.min(tot, cur + win);
    if (start > 1) html += `<span class="cv-page-ellipsis">…</span>`;
    for (let i = start; i <= end; i++) html += btn(i, i, false, i === cur);
    if (end < tot) html += `<span class="cv-page-ellipsis">…</span>`;

    html += btn('›', cur + 1, cur === tot, false);
    html += btn('»', tot, cur === tot, false);

    container.innerHTML = html;

    container.querySelectorAll('.cv-page-btn:not([disabled])').forEach(function (b) {
        b.addEventListener('click', function () { loadAuditLog(parseInt(this.dataset.page), auditSearch); });
    });
}

// ── Delete record ────────────────────────────────────────────────────────────
async function deleteRecord(id, name) {
    if (!confirm(`Delete consultation record for "${name}"?\nThis cannot be undone.`)) return;

    try {
        const response = await fetch('../handlers/delete_consultation.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                id: id,
                csrf_token: csrfToken 
            })
        });
        
        const data = await response.json();
        
        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Delete failed');
        }
        
        loadAuditLog(auditPage, auditSearch);
        showError(''); // Clear any previous errors
    } catch (error) {
        showError('Delete failed: ' + error.message);
    }
}

// ── Modal ────────────────────────────────────────────────────────────────────
async function openModal(id) {
    currentModalId = id;
    document.getElementById('auditModal').style.display = 'flex';
    document.getElementById('modalBody').innerHTML = 'Loading…';

    try {
        const response = await fetch(`../handlers/get_consultations.php?action=single&id=${id}`);
        const data = await response.json();
        
        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Failed to load record');
        }
        
        renderModal(data.data);
    } catch (error) {
        document.getElementById('modalBody').innerHTML = 'Error: ' + esc(error.message);
    }
}

function renderModal(r) {
    document.getElementById('modalTitle').textContent =
        `Consultation #${r.id} — ${r.patient_name}`;

    const na = v => v || '<span style="color:#aaa">—</span>';

    document.getElementById('modalBody').innerHTML = `
        <div class="cv-detail-section-title">Patient Information</div>
        <div class="cv-detail-grid">
            <div class="cv-detail-card"><div class="cv-dc-label">Student #</div><div class="cv-dc-value">${esc(r.student_number || '—')}</div></div>
            <div class="cv-detail-card"><div class="cv-dc-label">Full Name</div><div class="cv-dc-value">${esc(r.patient_name)}</div></div>
            <div class="cv-detail-card"><div class="cv-dc-label">Age</div><div class="cv-dc-value">${na(esc(r.patient_age))}</div></div>
            <div class="cv-detail-card"><div class="cv-dc-label">Gender</div><div class="cv-dc-value">${na(esc(r.patient_gender))}</div></div>
            <div class="cv-detail-card"><div class="cv-dc-label">Course</div><div class="cv-dc-value">${na(esc(r.patient_course))}</div></div>
            <div class="cv-detail-card"><div class="cv-dc-label">Year Level</div><div class="cv-dc-value">${na(esc(r.patient_year))}</div></div>
        </div>

        <div class="cv-detail-section-title">Appointment Details</div>
        <div class="cv-detail-grid">
            <div class="cv-detail-card"><div class="cv-dc-label">Nurse</div><div class="cv-dc-value">${esc(r.nurse)}</div></div>
            <div class="cv-detail-card"><div class="cv-dc-label">Visit Type</div><div class="cv-dc-value">${visitBadge(r.visit_type)}</div></div>
            <div class="cv-detail-card"><div class="cv-dc-label">Date</div><div class="cv-dc-value">${esc(r.appointment_date)}</div></div>
            <div class="cv-detail-card"><div class="cv-dc-label">Time</div><div class="cv-dc-value">${esc(r.appointment_time)}</div></div>
            <div class="cv-detail-card"><div class="cv-dc-label">Submitted At</div><div class="cv-dc-value">${formatSubmittedAt(r.submitted_at)}</div></div>
        </div>

        <div class="cv-detail-section-title">Vital Signs</div>
        <div class="cv-detail-grid">
            <div class="cv-detail-card"><div class="cv-dc-label">Temperature</div><div class="cv-dc-value">${r.temperature ? esc(r.temperature) + ' °C' : '—'}</div></div>
            <div class="cv-detail-card"><div class="cv-dc-label">Blood Pressure</div><div class="cv-dc-value">${na(esc(r.blood_pressure))}</div></div>
            <div class="cv-detail-card"><div class="cv-dc-label">Heart Rate</div><div class="cv-dc-value">${r.heart_rate ? esc(r.heart_rate) + ' bpm' : '—'}</div></div>
            <div class="cv-detail-card"><div class="cv-dc-label">Resp. Rate</div><div class="cv-dc-value">${r.respiratory_rate ? esc(r.respiratory_rate) + ' /min' : '—'}</div></div>
            <div class="cv-detail-card"><div class="cv-dc-label">O₂ Saturation</div><div class="cv-dc-value">${r.oxygen_sat ? esc(r.oxygen_sat) + ' %' : '—'}</div></div>
        </div>

        <div class="cv-detail-section-title">Consultation Notes</div>
        <div class="cv-detail-notes">
            <p><strong>Symptoms:</strong> ${na(esc(r.symptoms))}</p>
            <p><strong>Diagnosis:</strong> ${na(esc(r.diagnosis))}</p>
            <p><strong>Treatment / Medication:</strong> ${na(esc(r.treatment))}</p>
            <p><strong>Additional Notes:</strong> ${na(esc(r.notes))}</p>
        </div>`;
}

function closeModal() {
    document.getElementById('auditModal').style.display = 'none';
    currentModalId = null;
}

document.getElementById('modalCloseBtn').addEventListener('click', closeModal);
document.getElementById('modalCloseBtn2').addEventListener('click', closeModal);
document.getElementById('auditModalBackdrop').addEventListener('click', closeModal);
document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeModal(); });

// ── Print ────────────────────────────────────────────────────────────────────
document.getElementById('modalPrintBtn').addEventListener('click', function () {
    const body = document.getElementById('modalBody').innerHTML;
    const title = document.getElementById('modalTitle').textContent;
    const printArea = document.getElementById('printArea');
    printArea.innerHTML = `<h2>${esc(title)}</h2>${body}`;
    window.print();
});

// ── Audit search ─────────────────────────────────────────────────────────────
document.getElementById('auditSearchBtn').addEventListener('click', function () {
    auditSearch = document.getElementById('auditSearch').value.trim();
    loadAuditLog(1, auditSearch);
});

document.getElementById('auditSearch').addEventListener('keydown', function (e) {
    if (e.key === 'Enter') { e.preventDefault(); document.getElementById('auditSearchBtn').click(); }
});

document.getElementById('auditClearBtn').addEventListener('click', function () {
    document.getElementById('auditSearch').value = '';
    loadAuditLog(1, '');
});

// ── Export CSV ───────────────────────────────────────────────────────────────
document.getElementById('auditExportBtn').addEventListener('click', function () {
    const rows = document.querySelectorAll('#auditTable tbody tr');
    if (!rows.length || (rows.length === 1 && rows[0].querySelector('.cv-no-data'))) {
        showError('No data to export.');
        return;
    }

    const headers = ['ID','Student #','Patient Name','Nurse','Visit Type',
                     'Date','Time','Temperature','BP','HR','RR','O2 Sat',
                     'Symptoms','Diagnosis','Treatment','Notes','Submitted At'];
    
    const csvRows = [];
    
    // Add headers
    csvRows.push(headers.map(h => `"${h}"`).join(','));

    // Add data rows
    rows.forEach(function (tr) {
        if (tr.querySelector('.cv-no-data')) return;
        
        const rowData = [];
        const cells = tr.querySelectorAll('td');
        
        // Skip the actions column (last one)
        for (let i = 0; i < cells.length - 1; i++) {
            let text = cells[i].textContent.trim().replace(/\s+/g, ' ');
            // Escape quotes and wrap in quotes if contains comma
            if (text.includes(',') || text.includes('"') || text.includes('\n')) {
                text = '"' + text.replace(/"/g, '""') + '"';
            }
            rowData.push(text);
        }
        
        csvRows.push(rowData.join(','));
    });

    const blob = new Blob([csvRows.join('\n')], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'consultation_log_' + new Date().toISOString().split('T')[0] + '.csv';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(link.href);
});

// ── Init ─────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    enableFormFields(false);
    loadAuditLog(1, '');
});

})();
</script>
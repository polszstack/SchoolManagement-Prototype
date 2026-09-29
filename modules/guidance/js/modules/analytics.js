/**
 * analytics-reports.js
 * Module: Analytics & Reports
 *
 * Talks to controller/AnalyticsReportsController.php via fetch(). Every
 * response is shaped as { success, message?, data? }.
 */

(function () {
    'use strict';

    const API_URL = 'controller/AnalyticsReportsController.php';
    const PAGE_KEY = 'analytics-reports'; // must match the Page class's $_GET['page'] key

    let els = {};

    function queryElements() {
        return {
            reportType: document.getElementById('rprtReportType'),
            dateRangeGroup: document.getElementById('rprtDateRangeGroup'),
            dateFrom: document.getElementById('rprtDateFrom'),
            dateTo: document.getElementById('rprtDateTo'),
            monthGroup: document.getElementById('rprtMonthGroup'),
            month: document.getElementById('rprtMonth'),
            yearForMonth: document.getElementById('rprtYearForMonth'),
            yearGroup: document.getElementById('rprtYearGroup'),
            year: document.getElementById('rprtYear'),
            generateBtn: document.getElementById('rprtGenerateBtn'),
            exportActions: document.getElementById('rprtExportActions'),
            exportPdfBtn: document.getElementById('rprtExportPdfBtn'),
            exportExcelBtn: document.getElementById('rprtExportExcelBtn'),
            printBtn: document.getElementById('rprtPrintBtn'),
            resultsWrapper: document.getElementById('rprtResultsWrapper'),
        };
    }

    // Column definitions per report type — keeps the results table
    // generic instead of hardcoding 8 separate table layouts.
    const REPORT_COLUMNS = {
        student_counseling: [
            { key: 'session_date', label: 'Date' }, { key: 'student_name', label: 'Student' },
            { key: 'session_type', label: 'Type' }, { key: 'duration_minutes', label: 'Duration (min)' },
            { key: 'counselor_name', label: 'Counselor' }, { key: 'case_number', label: 'Case #' },
        ],
        referral: [
            { key: 'referral_date', label: 'Date' }, { key: 'student_name', label: 'Student' },
            { key: 'referral_source', label: 'Source' }, { key: 'referral_status', label: 'Status' },
            { key: 'case_number', label: 'Case #' },
        ],
        appointment: [
            { key: 'appointment_date', label: 'Date' }, { key: 'student_name', label: 'Student' },
            { key: 'counselor_name', label: 'Counselor' }, { key: 'meeting_type', label: 'Type' },
            { key: 'status', label: 'Status' }, { key: 'purpose', label: 'Purpose' },
        ],
        incident: [
            { key: 'incident_date', label: 'Date' }, { key: 'student_name', label: 'Student' },
            { key: 'incident_type', label: 'Type' }, { key: 'severity', label: 'Severity' },
            { key: 'status', label: 'Status' }, { key: 'reported_by_name', label: 'Reported By' },
        ],
        monthly_guidance: [
            { key: 'period', label: 'Period' }, { key: 'cases_opened', label: 'Cases Opened' },
            { key: 'cases_closed', label: 'Cases Closed' }, { key: 'referrals_submitted', label: 'Referrals' },
            { key: 'sessions_held', label: 'Sessions' }, { key: 'appointments_completed', label: 'Appointments Completed' },
            { key: 'incidents_reported', label: 'Incidents' },
        ],
        yearly_guidance: [
            { key: 'period', label: 'Month' }, { key: 'cases_opened', label: 'Cases Opened' },
            { key: 'cases_closed', label: 'Cases Closed' }, { key: 'referrals_submitted', label: 'Referrals' },
            { key: 'sessions_held', label: 'Sessions' }, { key: 'appointments_completed', label: 'Appointments Completed' },
            { key: 'incidents_reported', label: 'Incidents' },
        ],
        counselor_workload: [
            { key: 'counselor_name', label: 'Counselor' }, { key: 'position_name', label: 'Position' },
            { key: 'total_cases', label: 'Total Cases' }, { key: 'active_cases', label: 'Active Cases' },
            { key: 'total_appointments', label: 'Appointments' }, { key: 'total_sessions', label: 'Sessions' },
        ],
        student_risk: [
            { key: 'student_name', label: 'Student' }, { key: 'course', label: 'Course' },
            { key: 'risk_level', label: 'Risk Level' }, { key: 'guidance_status', label: 'Guidance Status' },
            { key: 'total_cases', label: 'Total Cases' }, { key: 'total_incidents', label: 'Total Incidents' },
        ],
    };

    document.addEventListener('DOMContentLoaded', init);
    window.addEventListener('page:loaded', (e) => {
        if (e.detail && e.detail.page === PAGE_KEY) init();
    });

    function init() {
        els = queryElements();
        if (!els.generateBtn) return; // fragment not currently in .container

        bindReportTypeToggle();
        bindGenerateButton();
        bindExportButtons();
        toggleFilterGroups(els.reportType.value);
    }

    function bindReportTypeToggle() {
        els.reportType?.addEventListener('change', () => toggleFilterGroups(els.reportType.value));
    }

    function toggleFilterGroups(type) {
        const showDateRange = ['student_counseling', 'referral', 'appointment', 'incident', 'counselor_workload'].includes(type);
        const showMonth = type === 'monthly_guidance';
        const showYear = type === 'yearly_guidance';

        els.dateRangeGroup.style.display = showDateRange ? '' : 'none';
        els.monthGroup.style.display = showMonth ? '' : 'none';
        els.yearGroup.style.display = showYear ? '' : 'none';
    }

    function bindGenerateButton() {
        els.generateBtn?.addEventListener('click', async () => {
            const type = els.reportType.value;
            const params = new URLSearchParams({ action: 'generate', type });

            if (els.dateFrom.value) params.set('date_from', els.dateFrom.value);
            if (els.dateTo.value) params.set('date_to', els.dateTo.value);
            if (els.monthGroup.style.display !== 'none') {
                params.set('month', els.month.value);
                params.set('year', els.yearForMonth.value);
            }
            if (els.yearGroup.style.display !== 'none') {
                params.set('year', els.year.value);
            }

            toggleButtonLoading(els.generateBtn, true, 'Generating...');
            els.resultsWrapper.innerHTML = `<div class="rprt-empty">Loading...</div>`;

            try {
                const res = await fetch(`${API_URL}?${params.toString()}`);
                const payload = await res.json();
                if (!payload.success) throw new Error(payload.message || 'Request failed');

                renderResults(payload.data.type, payload.data.rows);
                els.exportActions.style.display = payload.data.rows.length ? 'flex' : 'none';
            } catch (err) {
                console.error('Failed to generate report:', err);
                els.resultsWrapper.innerHTML = `<div class="rprt-empty">Something went wrong generating this report.</div>`;
                els.exportActions.style.display = 'none';
            } finally {
                toggleButtonLoading(els.generateBtn, false, 'Generate');
            }
        });
    }

    function renderResults(type, rows) {
        const columns = REPORT_COLUMNS[type] || [];

        if (!rows.length) {
            els.resultsWrapper.innerHTML = `<div class="rprt-empty">No data found for this report/filter combination.</div>`;
            return;
        }

        const thead = columns.map(c => `<th>${escapeHtml(c.label)}</th>`).join('');
        const tbody = rows.map(row => {
            const cells = columns.map(c => `<td>${escapeHtml(formatCell(c.key, row[c.key]))}</td>`).join('');
            return `<tr>${cells}</tr>`;
        }).join('');

        els.resultsWrapper.innerHTML = `
            <div class="rprt-table-wrapper">
                <table class="rprt-table">
                    <thead><tr>${thead}</tr></thead>
                    <tbody>${tbody}</tbody>
                </table>
            </div>
            <div class="rprt-results-count">${rows.length} record${rows.length === 1 ? '' : 's'}</div>
        `;
    }

    function formatCell(key, value) {
        if (value === null || value === undefined || value === '') return '—';
        if (key.endsWith('_date')) {
            const d = new Date(value);
            if (!isNaN(d.getTime())) {
                return key === 'appointment_date' || key === 'session_date'
                    ? d.toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit' })
                    : d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            }
        }
        return value;
    }

    function bindExportButtons() {
        els.exportPdfBtn?.addEventListener('click', () => runExport('pdf'));
        els.exportExcelBtn?.addEventListener('click', () => runExport('excel'));
        els.printBtn?.addEventListener('click', () => window.print());
    }

    async function runExport(format) {
        try {
            const res = await fetch(`${API_URL}?action=export&format=${format}`);
            const payload = await res.json();
            alert(payload.message || 'Export is not available yet.');
        } catch (err) {
            console.error('Export failed:', err);
            alert('Export is not available yet.');
        }
    }

    /* ---------------------------------------------------------
       Helpers
    --------------------------------------------------------- */
    function toggleButtonLoading(btn, isLoading, label) {
        if (!btn) return;
        btn.disabled = isLoading;
        btn.textContent = label;
    }

    function escapeHtml(value) {
        if (value === null || value === undefined) return '';
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
})();
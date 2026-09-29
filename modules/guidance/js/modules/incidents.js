/**
 * incidents.js
 * Module: Incident Management
 *
 * Talks to controller/IncidentsController.php via fetch(). Every
 * response is shaped as { success, message?, data? }.
 */

(function () {
    'use strict';

    const API_URL = 'controller/IncidentsController.php';
    // NOTE: the Page class's $_GET['page'] key is 'incident' (singular),
    // even though this file and the model/controller are named with a
    // trailing 's'. This must match pages/incident.php's routing key.
    const PAGE_KEY = 'incident';

    /* ---------------------------------------------------------
       Element references — rebuilt fresh on every init() call.
       See students.js for the full explanation.
    --------------------------------------------------------- */
    let els = {};

    function queryElements() {
        return {
            searchInput: document.getElementById('incdntSearchInput'),
            filterSeverity: document.getElementById('incdntFilterSeverity'),
            filterStatus: document.getElementById('incdntFilterStatus'),
            filterType: document.getElementById('incdntFilterType'),
            tableBody: document.querySelector('.incdnt-table tbody'),
            pagination: document.querySelector('.incdnt-pagination'),

            createBtn: document.getElementById('incdntCreateBtn'),
            formModal: document.getElementById('incdntFormModal'),
            formModalTitle: document.getElementById('incdntFormModalTitle'),
            form: document.getElementById('incdntForm'),
            formCloseBtn: document.getElementById('incdntFormCloseBtn'),
            formCancelBtn: document.getElementById('incdntFormCancelBtn'),

            detailModal: document.getElementById('incdntDetailModal'),
            detailCloseBtn: document.getElementById('incdntDetailCloseBtn'),
            resolutionSection: document.getElementById('incdntResolutionSection'),
            actionButtons: document.getElementById('incdntActionButtons'),

            resolutionModal: document.getElementById('incdntResolutionModal'),
            resolutionForm: document.getElementById('incdntResolutionForm'),
            resolutionCloseBtn: document.getElementById('incdntResolutionCloseBtn'),
            resolutionCancelBtn: document.getElementById('incdntResolutionCancelBtn'),
        };
    }

    let currentPage = 1;
    let activeIncidentId = null;
    let activeIncidentData = null;

    document.addEventListener('DOMContentLoaded', init);
    window.addEventListener('page:loaded', (e) => {
        if (e.detail && e.detail.page === PAGE_KEY) init();
    });

    function init() {
        els = queryElements();
        if (!els.tableBody) return;

        currentPage = 1;
        activeIncidentId = null;
        activeIncidentData = null;

        bindFilterEvents();
        bindTableEvents();
        bindPaginationEvents();
        bindFormModal();
        bindDetailModal();
        bindResolutionModal();
    }

    /* ---------------------------------------------------------
       Filters / search
    --------------------------------------------------------- */
    function bindFilterEvents() {
        let debounceTimer;
        const triggerSearch = () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => { currentPage = 1; loadIncidents(); }, 350);
        };

        els.searchInput?.addEventListener('input', triggerSearch);
        [els.filterSeverity, els.filterStatus, els.filterType]
            .forEach(sel => sel?.addEventListener('change', () => { currentPage = 1; loadIncidents(); }));
    }

    function getFilterParams(extra = {}) {
        return new URLSearchParams({
            search: els.searchInput?.value.trim() || '',
            severity: els.filterSeverity?.value || '',
            status: els.filterStatus?.value || '',
            incident_type: els.filterType?.value || '',
            page: currentPage,
            ...extra,
        });
    }

    async function loadIncidents() {
        setTableLoading(true);
        try {
            const params = getFilterParams();
            const res = await fetch(`${API_URL}?action=list&${params.toString()}`);
            const payload = await res.json();
            if (!payload.success) throw new Error(payload.message || 'Request failed');

            renderRows(payload.data.incidents || []);
            renderPagination(payload.data.pagination || null);
        } catch (err) {
            console.error('Failed to load incidents:', err);
            if (els.tableBody) {
                els.tableBody.innerHTML = `<tr><td colspan="7" class="incdnt-table__empty">Something went wrong loading incidents.</td></tr>`;
            }
        } finally {
            setTableLoading(false);
        }
    }

    function setTableLoading(isLoading) {
        if (isLoading && els.tableBody) {
            els.tableBody.innerHTML = `<tr><td colspan="7" class="incdnt-table__empty">Loading incidents...</td></tr>`;
        }
    }

    function renderRows(incidents) {
        if (!els.tableBody) return;
        if (!incidents.length) {
            els.tableBody.innerHTML = `<tr><td colspan="7" class="incdnt-table__empty">No incidents found for the selected filters.</td></tr>`;
            return;
        }
        els.tableBody.innerHTML = incidents.map(rowTemplate).join('');
    }

    function rowTemplate(i) {
        const caseLink = i.case_number
            ? `<span class="incdnt-case-link">#${escapeHtml(i.case_number)}</span>`
            : `<span class="incdnt-case-none">—</span>`;

        return `
            <tr class="incdnt-row" data-incident-id="${escapeHtml(i.incident_id)}">
                <td>${escapeHtml(i.incident_date_display)}</td>
                <td>
                    <div class="incdnt-student-name">${escapeHtml(i.student_name)}</div>
                    <div class="incdnt-student-sub">#${escapeHtml(i.student_number)}</div>
                </td>
                <td>${escapeHtml(i.incident_type)}</td>
                <td><span class="incdnt-badge ${severityBadgeClass(i.severity)}">${escapeHtml(i.severity)}</span></td>
                <td><span class="incdnt-badge ${statusBadgeClass(i.status)}">${escapeHtml(i.status)}</span></td>
                <td>${escapeHtml(i.reported_by_name)}</td>
                <td>${caseLink}</td>
            </tr>
        `;
    }

    function severityBadgeClass(severity) {
        if (severity === 'Critical') return 'incdnt-badge--severity-critical';
        if (severity === 'Major') return 'incdnt-badge--severity-major';
        if (severity === 'Moderate') return 'incdnt-badge--severity-moderate';
        return 'incdnt-badge--severity-minor';
    }

    function statusBadgeClass(status) {
        if (status === 'Reported') return 'incdnt-badge--status-reported';
        if (status === 'Investigating') return 'incdnt-badge--status-investigating';
        if (status === 'Resolved') return 'incdnt-badge--status-resolved';
        return 'incdnt-badge--status-closed';
    }

    /* ---------------------------------------------------------
       Pagination
    --------------------------------------------------------- */
    function bindPaginationEvents() {
        els.pagination?.addEventListener('click', (e) => {
            const btn = e.target.closest('.incdnt-pagination__page');
            if (!btn || btn.classList.contains('incdnt-pagination__page--active')) return;
            const page = btn.dataset.page;
            if (page) { currentPage = parseInt(page, 10); loadIncidents(); }
        });
    }

    function renderPagination(pagination) {
        if (!pagination || !els.pagination) return;
        const { total, page, totalPages, pageSize, count } = pagination;

        const summary = els.pagination.querySelector('span');
        if (summary) {
            summary.textContent = total > 0
                ? `Showing ${(page - 1) * pageSize + 1}-${(page - 1) * pageSize + count} of ${total} incidents`
                : 'No incidents found';
        }

        const pagesWrap = els.pagination.querySelector('.incdnt-pagination__pages');
        if (!pagesWrap) return;
        let html = '';
        for (let i = 1; i <= totalPages; i++) {
            html += `<button class="incdnt-pagination__page ${i === page ? 'incdnt-pagination__page--active' : ''}" data-page="${i}">${i}</button>`;
        }
        if (page < totalPages) html += `<button class="incdnt-pagination__page" data-page="${page + 1}">&rsaquo;</button>`;
        pagesWrap.innerHTML = html;
    }

    /* ---------------------------------------------------------
       Row click -> detail modal
    --------------------------------------------------------- */
    function bindTableEvents() {
        els.tableBody?.addEventListener('click', (e) => {
            const row = e.target.closest('.incdnt-row');
            if (!row) return;
            openDetailModal(row.dataset.incidentId);
        });
    }

    /* ---------------------------------------------------------
       Create / Edit modal
    --------------------------------------------------------- */
    function bindFormModal() {
        els.createBtn?.addEventListener('click', () => openFormModal(null));
        els.formCloseBtn?.addEventListener('click', closeFormModal);
        els.formCancelBtn?.addEventListener('click', closeFormModal);

        els.form?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(els.form);
            const incidentId = formData.get('incident_id');
            const isEdit = !!incidentId;
            const action = isEdit ? 'update' : 'create';
            const body = Object.fromEntries(formData.entries());
            if (!isEdit) delete body.incident_id;

            const submitBtn = els.form.querySelector('button[type="submit"]');
            toggleButtonLoading(submitBtn, true, 'Saving...');

            try {
                const res = await fetch(`${API_URL}?action=${action}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(body),
                });
                const payload = await res.json();
                if (!payload.success) throw new Error(payload.message || 'Request failed');

                closeFormModal();
                loadIncidents();
                if (isEdit && activeIncidentId) openDetailModal(activeIncidentId);
            } catch (err) {
                console.error('Failed to save incident:', err);
                alert(err.message || 'Could not save the incident. Please try again.');
            } finally {
                toggleButtonLoading(submitBtn, false, 'Save Incident');
            }
        });
    }

    function openFormModal(incident) {
        els.form?.reset();
        const isEdit = !!incident;

        if (els.formModalTitle) els.formModalTitle.textContent = isEdit ? 'Edit Incident' : 'Report Incident';

        if (isEdit) {
            els.form.querySelector('[name="incident_id"]').value = incident.incident_id;
            els.form.querySelector('[name="student_number"]').value = incident.student_number;
            els.form.querySelector('[name="incident_type"]').value = incident.incident_type;
            els.form.querySelector('[name="severity"]').value = incident.severity;
            els.form.querySelector('[name="location"]').value = incident.location || '';
            els.form.querySelector('[name="description"]').value = incident.description || '';
            els.form.querySelector('#incdntFormCaseDisplay').textContent = incident.case_number
                ? `Linked to case #${incident.case_number} — this can only be changed from the Cases module.`
                : 'Not linked to a case. Cases get linked from the Cases module\'s "Select Incident" picker, not here.';
            if (incident.incident_date) {
                els.form.querySelector('[name="incident_date"]').value = toDatetimeLocal(incident.incident_date);
            }
            // Student number shouldn't change on edit — same student the incident was filed for
            els.form.querySelector('[name="student_number"]').readOnly = true;
        } else {
            els.form.querySelector('[name="student_number"]').readOnly = false;
        }

        els.formModal?.classList.add('incdnt-modal-overlay--open');
    }

    function closeFormModal() {
        els.formModal?.classList.remove('incdnt-modal-overlay--open');
    }

    function toDatetimeLocal(value) {
        const d = new Date(value);
        if (isNaN(d.getTime())) return '';
        const pad = (n) => String(n).padStart(2, '0');
        return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
    }

    /* ---------------------------------------------------------
       Detail / action modal
    --------------------------------------------------------- */
    function bindDetailModal() {
        els.detailCloseBtn?.addEventListener('click', closeDetailModal);
    }

    async function openDetailModal(incidentId) {
        if (!incidentId) return;
        activeIncidentId = incidentId;

        try {
            const res = await fetch(`${API_URL}?action=details&incident_id=${encodeURIComponent(incidentId)}`);
            const payload = await res.json();
            if (!payload.success) throw new Error(payload.message || 'Request failed');
            activeIncidentData = payload.data;
            populateDetailModal(payload.data);
            els.detailModal?.classList.add('incdnt-modal-overlay--open');
        } catch (err) {
            console.error('Failed to load incident details:', err);
        }
    }

    function closeDetailModal() {
        els.detailModal?.classList.remove('incdnt-modal-overlay--open');
        activeIncidentId = null;
        activeIncidentData = null;
    }

    function populateDetailModal(data) {
        document.querySelectorAll('#incdntDetailModal [data-field]').forEach(el => {
            const field = el.dataset.field;
            let value = data[field];
            if (field === 'case_number') value = data.case_number ? `#${data.case_number}` : 'Not linked to a case';
            if (field === 'action_taken' && !value) value = 'No resolution recorded yet.';
            if (field === 'action_date_display' && !value) value = '—';
            el.textContent = value ?? '—';
        });

        const buttons = [];
        buttons.push(`<button type="button" class="incdnt-btn incdnt-btn--ghost incdnt-btn--sm" id="incdntEditBtn">Edit</button>`);

        if (data.status !== 'Resolved' && data.status !== 'Closed') {
            buttons.push(`<button type="button" class="incdnt-btn incdnt-btn--sm" id="incdntResolveBtn">Record Resolution</button>`);
        }
        if (data.status === 'Reported') {
            buttons.push(`<button type="button" class="incdnt-btn incdnt-btn--ghost incdnt-btn--sm" id="incdntInvestigatingBtn">Mark as Investigating</button>`);
        }
        if (data.status === 'Resolved') {
            buttons.push(`<button type="button" class="incdnt-btn incdnt-btn--ghost incdnt-btn--sm" id="incdntCloseBtn">Close Incident</button>`);
        }

        els.actionButtons.innerHTML = buttons.join('');

        document.getElementById('incdntEditBtn')?.addEventListener('click', () => openFormModal(activeIncidentData));
        document.getElementById('incdntResolveBtn')?.addEventListener('click', openResolutionModal);
        document.getElementById('incdntInvestigatingBtn')?.addEventListener('click', () => updateStatus('Investigating'));
        document.getElementById('incdntCloseBtn')?.addEventListener('click', () => updateStatus('Closed'));
    }

    async function updateStatus(status) {
        if (!activeIncidentId) return;
        try {
            const res = await fetch(`${API_URL}?action=update_status`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ incident_id: activeIncidentId, status }),
            });
            const payload = await res.json();
            if (!payload.success) throw new Error(payload.message || 'Request failed');

            openDetailModal(activeIncidentId);
            loadIncidents();
        } catch (err) {
            console.error('Failed to update status:', err);
            alert(err.message || 'Could not update status. Please try again.');
        }
    }

    /* ---------------------------------------------------------
       Record Resolution modal
    --------------------------------------------------------- */
    function bindResolutionModal() {
        els.resolutionCloseBtn?.addEventListener('click', closeResolutionModal);
        els.resolutionCancelBtn?.addEventListener('click', closeResolutionModal);

        els.resolutionForm?.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (!activeIncidentId) return;

            const formData = new FormData(els.resolutionForm);
            const body = Object.fromEntries(formData.entries());
            body.incident_id = activeIncidentId;

            const submitBtn = els.resolutionForm.querySelector('button[type="submit"]');
            toggleButtonLoading(submitBtn, true, 'Saving...');

            try {
                const res = await fetch(`${API_URL}?action=record_resolution`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(body),
                });
                const payload = await res.json();
                if (!payload.success) throw new Error(payload.message || 'Request failed');

                closeResolutionModal();
                openDetailModal(activeIncidentId);
                loadIncidents();
            } catch (err) {
                console.error('Failed to record resolution:', err);
                alert(err.message || 'Could not save the resolution. Please try again.');
            } finally {
                toggleButtonLoading(submitBtn, false, 'Save Resolution');
            }
        });
    }

    function openResolutionModal() {
        els.resolutionForm?.reset();
        els.resolutionModal?.classList.add('incdnt-modal-overlay--open');
    }
    function closeResolutionModal() {
        els.resolutionModal?.classList.remove('incdnt-modal-overlay--open');
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
/**
 * appointments.js
 * Module: Appointment Management (counselor-facing side)
 *
 * Talks to controller/AppointmentsController.php via fetch(). Every
 * response is shaped as { success, message?, data? }.
 */

(function () {
    'use strict';

    const API_URL = 'controller/AppointmentsController.php';
    const PAGE_KEY = 'appointments'; // must match the Page class's $_GET['page'] key

    /* ---------------------------------------------------------
       Element references — rebuilt fresh on every init() call.
       See students.js for the full explanation.
    --------------------------------------------------------- */
    let els = {};

    function queryElements() {
        return {
            searchInput: document.getElementById('aptSearchInput'),
            dateInput: document.getElementById('aptDateInput'),
            viewToggle: document.getElementById('aptViewToggle'),
            filterStatus: document.getElementById('aptFilterStatus'),
            filterMeetingType: document.getElementById('aptFilterMeetingType'),
            filterCounselor: document.getElementById('aptFilterCounselor'),
            tableBody: document.querySelector('.apt-table tbody'),
            pagination: document.querySelector('.apt-pagination'),

            bookBtn: document.getElementById('aptBookBtn'),
            bookModal: document.getElementById('aptBookModal'),
            bookForm: document.getElementById('aptBookForm'),
            bookCloseBtn: document.getElementById('aptBookCloseBtn'),
            bookCancelBtn: document.getElementById('aptBookCancelBtn'),
            bookCase: document.getElementById('aptBookCase'),
            bookStudentNumber: document.getElementById('aptBookStudentNumber'),
            bookCounselor: document.getElementById('aptBookCounselor'),
            bookDateTime: document.getElementById('aptBookDateTime'),
            availabilityBox: document.getElementById('aptAvailabilityBox'),
            availabilitySlots: document.getElementById('aptAvailabilitySlots'),

            detailModal: document.getElementById('aptDetailModal'),
            detailCloseBtn: document.getElementById('aptDetailCloseBtn'),
            actionButtons: document.getElementById('aptActionButtons'),
            remarksInput: document.getElementById('aptRemarksInput'),
            saveRemarksBtn: document.getElementById('aptSaveRemarksBtn'),

            rescheduleModal: document.getElementById('aptRescheduleModal'),
            rescheduleForm: document.getElementById('aptRescheduleForm'),
            rescheduleCloseBtn: document.getElementById('aptRescheduleCloseBtn'),
            rescheduleCancelBtn: document.getElementById('aptRescheduleCancelBtn'),

            rejectModal: document.getElementById('aptRejectModal'),
            rejectForm: document.getElementById('aptRejectForm'),
            rejectCloseBtn: document.getElementById('aptRejectCloseBtn'),
            rejectCancelBtn: document.getElementById('aptRejectCancelBtn'),
        };
    }

    let currentPage = 1;
    let currentView = 'daily';
    let activeAppointmentId = null;

    document.addEventListener('DOMContentLoaded', init);
    window.addEventListener('page:loaded', (e) => {
        if (e.detail && e.detail.page === PAGE_KEY) init();
    });

    function init() {
        els = queryElements();
        if (!els.tableBody) return;

        currentPage = 1;
        activeAppointmentId = null;
        currentView = document.querySelector('.apt-view-toggle--active')?.dataset.view || 'daily';

        bindFilterEvents();
        bindTableEvents();
        bindPaginationEvents();
        bindBookModal();
        bindDetailModal();
        bindRescheduleModal();
        bindRejectModal();
    }

    /* ---------------------------------------------------------
       Filters / date / view toggle
    --------------------------------------------------------- */
    function bindFilterEvents() {
        let debounceTimer;
        const triggerSearch = () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => { currentPage = 1; loadAppointments(); }, 350);
        };

        els.searchInput?.addEventListener('input', triggerSearch);
        els.dateInput?.addEventListener('change', () => { currentPage = 1; loadAppointments(); });
        [els.filterStatus, els.filterMeetingType, els.filterCounselor]
            .forEach(sel => sel?.addEventListener('change', () => { currentPage = 1; loadAppointments(); }));

        els.viewToggle?.addEventListener('click', (e) => {
            const btn = e.target.closest('button[data-view]');
            if (!btn) return;
            currentView = btn.dataset.view;
            els.viewToggle.querySelectorAll('button').forEach(b => b.classList.toggle('apt-view-toggle--active', b === btn));
            currentPage = 1;
            loadAppointments();
        });
    }

    function getFilterParams(extra = {}) {
        return new URLSearchParams({
            search: els.searchInput?.value.trim() || '',
            date: els.dateInput?.value || '',
            view: currentView,
            status: els.filterStatus?.value || '',
            meeting_type: els.filterMeetingType?.value || '',
            counselor_id: els.filterCounselor?.value || '',
            page: currentPage,
            ...extra,
        });
    }

    async function loadAppointments() {
        setTableLoading(true);
        try {
            const params = getFilterParams();
            const res = await fetch(`${API_URL}?action=list&${params.toString()}`);
            const payload = await res.json();
            if (!payload.success) throw new Error(payload.message || 'Request failed');

            renderRows(payload.data.appointments || []);
            renderPagination(payload.data.pagination || null);
        } catch (err) {
            console.error('Failed to load appointments:', err);
            if (els.tableBody) {
                els.tableBody.innerHTML = `<tr><td colspan="6" class="apt-table__empty">Something went wrong loading appointments.</td></tr>`;
            }
        } finally {
            setTableLoading(false);
        }
    }

    function setTableLoading(isLoading) {
        if (isLoading && els.tableBody) {
            els.tableBody.innerHTML = `<tr><td colspan="6" class="apt-table__empty">Loading appointments...</td></tr>`;
        }
    }

    function renderRows(appointments) {
        if (!els.tableBody) return;
        if (!appointments.length) {
            els.tableBody.innerHTML = `<tr><td colspan="6" class="apt-table__empty">No appointments found for the selected filters.</td></tr>`;
            return;
        }
        els.tableBody.innerHTML = appointments.map(rowTemplate).join('');
    }

    function rowTemplate(a) {
        return `
            <tr class="apt-row" data-appointment-id="${escapeHtml(a.appointment_id)}">
                <td class="apt-time">${escapeHtml(a.time_display)}<br><span class="apt-student-sub">${escapeHtml(a.date_display)}</span></td>
                <td>
                    <div class="apt-student-name">${escapeHtml(a.student_name)}</div>
                    <div class="apt-student-sub">#${escapeHtml(a.student_number)}</div>
                </td>
                <td>${escapeHtml(a.counselor_name)}</td>
                <td><span class="apt-badge apt-badge--meeting-type">${escapeHtml(a.meeting_type)}</span></td>
                <td>${escapeHtml(a.purpose)}</td>
                <td><span class="apt-badge ${statusBadgeClass(a.status)}">${escapeHtml(a.status)}</span></td>
            </tr>
        `;
    }

    function statusBadgeClass(status) {
        if (status === 'Pending') return 'apt-badge--pending';
        if (status === 'Approved') return 'apt-badge--approved';
        if (status === 'Completed') return 'apt-badge--completed';
        if (status === 'No Show') return 'apt-badge--noshow';
        return 'apt-badge--cancelled';
    }

    /* ---------------------------------------------------------
       Pagination
    --------------------------------------------------------- */
    function bindPaginationEvents() {
        els.pagination?.addEventListener('click', (e) => {
            const btn = e.target.closest('.apt-pagination__page');
            if (!btn || btn.classList.contains('apt-pagination__page--active')) return;
            const page = btn.dataset.page;
            if (page) { currentPage = parseInt(page, 10); loadAppointments(); }
        });
    }

    function renderPagination(pagination) {
        if (!pagination || !els.pagination) return;
        const { total, page, totalPages, pageSize, count } = pagination;

        const summary = els.pagination.querySelector('span');
        if (summary) {
            summary.textContent = total > 0
                ? `Showing ${(page - 1) * pageSize + 1}-${(page - 1) * pageSize + count} of ${total} appointments`
                : 'No appointments found';
        }

        const pagesWrap = els.pagination.querySelector('.apt-pagination__pages');
        if (!pagesWrap) return;
        let html = '';
        for (let i = 1; i <= totalPages; i++) {
            html += `<button class="apt-pagination__page ${i === page ? 'apt-pagination__page--active' : ''}" data-page="${i}">${i}</button>`;
        }
        if (page < totalPages) html += `<button class="apt-pagination__page" data-page="${page + 1}">&rsaquo;</button>`;
        pagesWrap.innerHTML = html;
    }

    /* ---------------------------------------------------------
       Row click -> detail modal
    --------------------------------------------------------- */
    function bindTableEvents() {
        els.tableBody?.addEventListener('click', (e) => {
            const row = e.target.closest('.apt-row');
            if (!row) return;
            openDetailModal(row.dataset.appointmentId);
        });
    }

    /* ---------------------------------------------------------
       Book Appointment modal
    --------------------------------------------------------- */
    function bindBookModal() {
        els.bookBtn?.addEventListener('click', () => {
            els.bookForm?.reset();
            els.availabilityBox.style.display = 'none';
            setCaseLock(false);
            els.bookModal?.classList.add('apt-modal-overlay--open');
        });
        els.bookCloseBtn?.addEventListener('click', closeBookModal);
        els.bookCancelBtn?.addEventListener('click', closeBookModal);

        els.bookCase?.addEventListener('change', () => {
            const option = els.bookCase.selectedOptions[0];
            const caseId = els.bookCase.value;

            if (!caseId) {
                setCaseLock(false);
                return;
            }

            // Selecting a case is the source of truth for student + counselor
            // — same pattern as picking an incident in Cases.
            if (els.bookStudentNumber) els.bookStudentNumber.value = option.dataset.student || '';
            if (els.bookCounselor) els.bookCounselor.value = option.dataset.counselor || '';
            setCaseLock(true);
        });

        const checkAvailability = async () => {
            const counselorId = els.bookCounselor?.value;
            const dateTime = els.bookDateTime?.value;
            if (!counselorId || !dateTime) { els.availabilityBox.style.display = 'none'; return; }

            const date = dateTime.split('T')[0];
            try {
                const res = await fetch(`${API_URL}?action=booked_times&counselor_id=${counselorId}&date=${date}`);
                const payload = await res.json();
                if (payload.success) {
                    if (payload.data.length) {
                        els.availabilitySlots.innerHTML = payload.data.map(t => `<span class="apt-availability-slot">${escapeHtml(t)}</span>`).join('');
                        els.availabilityBox.style.display = 'block';
                    } else {
                        els.availabilityBox.style.display = 'none';
                    }
                }
            } catch (err) {
                console.error('Failed to check availability:', err);
            }
        };
        els.bookCounselor?.addEventListener('change', checkAvailability);
        els.bookDateTime?.addEventListener('change', checkAvailability);

        els.bookForm?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(els.bookForm);
            const body = Object.fromEntries(formData.entries());

            const submitBtn = els.bookForm.querySelector('button[type="submit"]');
            toggleButtonLoading(submitBtn, true, 'Booking...');

            try {
                const res = await fetch(`${API_URL}?action=create`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(body),
                });
                const payload = await res.json();
                if (!payload.success) throw new Error(payload.message || 'Request failed');

                closeBookModal();
                loadAppointments();
            } catch (err) {
                console.error('Failed to book appointment:', err);
                alert(err.message || 'Could not book the appointment. Please try again.');
            } finally {
                toggleButtonLoading(submitBtn, false, 'Book Appointment');
            }
        });
    }

    function closeBookModal() {
        els.bookModal?.classList.remove('apt-modal-overlay--open');
    }

    function setCaseLock(locked) {
        if (els.bookStudentNumber) els.bookStudentNumber.readOnly = locked;
        // Counselor is a <select> — readOnly doesn't apply to selects, and
        // `disabled` would exclude it from FormData on submit, so lock it
        // visually with a CSS class instead (value still submits normally).
        els.bookCounselor?.classList.toggle('apt-locked', locked);
    }

    /* ---------------------------------------------------------
       Detail / Action modal
    --------------------------------------------------------- */
    function bindDetailModal() {
        els.detailCloseBtn?.addEventListener('click', closeDetailModal);

        els.saveRemarksBtn?.addEventListener('click', async () => {
            if (!activeAppointmentId) return;
            const remarks = els.remarksInput?.value.trim();
            if (!remarks) return;

            toggleButtonLoading(els.saveRemarksBtn, true, 'Saving...');
            try {
                const res = await fetch(`${API_URL}?action=add_remarks`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ appointment_id: activeAppointmentId, remarks }),
                });
                const payload = await res.json();
                if (!payload.success) throw new Error(payload.message || 'Request failed');
                closeDetailModal();
                loadAppointments();
            } catch (err) {
                console.error('Failed to save remarks:', err);
                alert(err.message || 'Could not save remarks.');
            } finally {
                toggleButtonLoading(els.saveRemarksBtn, false, 'Save Remarks');
            }
        });
    }

    async function openDetailModal(appointmentId) {
        if (!appointmentId) return;
        activeAppointmentId = appointmentId;

        try {
            const res = await fetch(`${API_URL}?action=details&appointment_id=${encodeURIComponent(appointmentId)}`);
            const payload = await res.json();
            if (!payload.success) throw new Error(payload.message || 'Request failed');
            populateDetailModal(payload.data);
            els.detailModal?.classList.add('apt-modal-overlay--open');
        } catch (err) {
            console.error('Failed to load appointment details:', err);
        }
    }

    function closeDetailModal() {
        els.detailModal?.classList.remove('apt-modal-overlay--open');
        activeAppointmentId = null;
    }

    function populateDetailModal(data) {
        document.querySelectorAll('#aptDetailModal [data-field]').forEach(el => {
            el.textContent = data[el.dataset.field] ?? '—';
        });
        if (els.remarksInput) els.remarksInput.value = data.remarks || '';

        const buttons = [];
        if (data.status === 'Pending') {
            buttons.push(`<button type="button" class="apt-btn apt-btn--sm" id="aptApproveBtn">Approve</button>`);
            buttons.push(`<button type="button" class="apt-btn apt-btn--danger apt-btn--sm" id="aptRejectBtn">Reject</button>`);
        }
        if (data.status === 'Pending' || data.status === 'Approved') {
            buttons.push(`<button type="button" class="apt-btn apt-btn--ghost apt-btn--sm" id="aptRescheduleBtn">Reschedule</button>`);
        }
        if (data.status === 'Approved') {
            buttons.push(`<button type="button" class="apt-btn apt-btn--sm" id="aptCompleteBtn">Mark Completed</button>`);
            buttons.push(`<button type="button" class="apt-btn apt-btn--danger apt-btn--sm" id="aptNoShowBtn">Mark No Show</button>`);
        }
        els.actionButtons.innerHTML = buttons.join('');

        document.getElementById('aptApproveBtn')?.addEventListener('click', () => runStatusAction('approve'));
        document.getElementById('aptCompleteBtn')?.addEventListener('click', () => runStatusAction('complete'));
        document.getElementById('aptNoShowBtn')?.addEventListener('click', () => runStatusAction('no_show'));
        document.getElementById('aptRescheduleBtn')?.addEventListener('click', openRescheduleModal);
        document.getElementById('aptRejectBtn')?.addEventListener('click', openRejectModal);
    }

    async function runStatusAction(action) {
        if (!activeAppointmentId) return;
        try {
            const res = await fetch(`${API_URL}?action=${action}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ appointment_id: activeAppointmentId }),
            });
            const payload = await res.json();
            if (!payload.success) throw new Error(payload.message || 'Request failed');

            closeDetailModal();
            loadAppointments();
        } catch (err) {
            console.error('Failed to update appointment:', err);
            alert(err.message || 'Could not update the appointment. Please try again.');
        }
    }

    /* ---------------------------------------------------------
       Reschedule modal
    --------------------------------------------------------- */
    function bindRescheduleModal() {
        els.rescheduleCloseBtn?.addEventListener('click', closeRescheduleModal);
        els.rescheduleCancelBtn?.addEventListener('click', closeRescheduleModal);

        els.rescheduleForm?.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (!activeAppointmentId) return;

            const formData = new FormData(els.rescheduleForm);
            const submitBtn = els.rescheduleForm.querySelector('button[type="submit"]');
            toggleButtonLoading(submitBtn, true, 'Saving...');

            try {
                const res = await fetch(`${API_URL}?action=reschedule`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        appointment_id: activeAppointmentId,
                        appointment_date: formData.get('appointment_date'),
                    }),
                });
                const payload = await res.json();
                if (!payload.success) throw new Error(payload.message || 'Request failed');

                closeRescheduleModal();
                closeDetailModal();
                loadAppointments();
            } catch (err) {
                console.error('Failed to reschedule:', err);
                alert(err.message || 'Could not reschedule. Please try again.');
            } finally {
                toggleButtonLoading(submitBtn, false, 'Confirm Reschedule');
            }
        });
    }

    function openRescheduleModal() {
        els.rescheduleForm?.reset();
        els.rescheduleModal?.classList.add('apt-modal-overlay--open');
    }
    function closeRescheduleModal() {
        els.rescheduleModal?.classList.remove('apt-modal-overlay--open');
    }

    /* ---------------------------------------------------------
       Reject modal
    --------------------------------------------------------- */
    function bindRejectModal() {
        els.rejectCloseBtn?.addEventListener('click', closeRejectModal);
        els.rejectCancelBtn?.addEventListener('click', closeRejectModal);

        els.rejectForm?.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (!activeAppointmentId) return;

            const formData = new FormData(els.rejectForm);
            const submitBtn = els.rejectForm.querySelector('button[type="submit"]');
            toggleButtonLoading(submitBtn, true, 'Saving...');

            try {
                const res = await fetch(`${API_URL}?action=reject`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        appointment_id: activeAppointmentId,
                        reason: formData.get('reason'),
                    }),
                });
                const payload = await res.json();
                if (!payload.success) throw new Error(payload.message || 'Request failed');

                closeRejectModal();
                closeDetailModal();
                loadAppointments();
            } catch (err) {
                console.error('Failed to reject appointment:', err);
                alert(err.message || 'Could not reject the appointment. Please try again.');
            } finally {
                toggleButtonLoading(submitBtn, false, 'Reject');
            }
        });
    }

    function openRejectModal() {
        els.rejectForm?.reset();
        els.rejectModal?.classList.add('apt-modal-overlay--open');
    }
    function closeRejectModal() {
        els.rejectModal?.classList.remove('apt-modal-overlay--open');
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
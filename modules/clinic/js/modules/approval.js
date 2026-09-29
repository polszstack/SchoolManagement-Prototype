const ApprovalController = '/sms/modules/school-directress/controllers/ApprovalController.php';

// ── INIT ──────────────────────────────────────────────────────────────────────
function initApprovalModule() {

    loadApprovalQueue();

    // --- Submit Approval ---
        const uploadForm = document.getElementById('approval-upload-form');
        if (uploadForm) {
            uploadForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                const fileError = document.getElementById('file-error');
                const submitBtn = document.getElementById('submit-btn');

                if (fileError) fileError.style.display = 'none';  // ← guard
                if (submitBtn) {
                    submitBtn.disabled    = true;
                    submitBtn.textContent = 'Submitting…';
                }

                const formData = new FormData(uploadForm);
                formData.append('action', 'submit');

                try {
                    const res  = await fetch(ApprovalController, { method: 'POST', body: formData });
                    const json = await res.json();

                    if (!json.success && json.message?.toLowerCase().includes('session expired')) {
                        setTimeout(() => window.location.href = '/sms/index.php', 1500);
                        return;
                    }

                    if (json.success) {
                        uploadForm.reset();
                        showToast('Approval submitted successfully!', 'success');
                        loadApprovalQueue();
                    } else {
                        if (fileError) {
                            fileError.textContent   = json.message;
                            fileError.style.display = 'block';
                        } else {
                            showToast(json.message, 'error');
                        }
                    }

                } catch (err) {
                    console.error(err);
                    if (fileError) {
                        fileError.textContent   = 'A network error occurred. Please try again.';
                        fileError.style.display = 'block';
                    } else {
                        showToast('A network error occurred. Please try again.', 'error');
                    }
                } finally {
                    if (submitBtn) {
                        submitBtn.disabled    = false;
                        submitBtn.textContent = 'Submit Approval';
                    }
                }
            });
        }

    // --- Status filter ---
    const deptFilter = document.getElementById('department-filter');
    if (deptFilter) {
        deptFilter.addEventListener('change', () => loadApprovalQueue());
    }

    // --- Search ---
    const search = document.getElementById('approval-search');
    if (search) {
        search.addEventListener('input', applyApprovalSearchFilter);
    }

    // --- Approve / Reject (delegated) ---
    document.addEventListener('click', async (e) => {
        const row        = e.target.closest('tr');
        const approvalId = row?.dataset.approvalId;

        if (e.target.classList.contains('btn-approve')) {
            if (!approvalId) return;
            await sendDecision('approve', approvalId);
        }

        if (e.target.classList.contains('btn-reject')) {
            if (!approvalId) return;
            await sendDecision('reject', approvalId);
        }
    });
}

// Handles both hard refresh and sidebar navigation
window.addEventListener('page:loaded', initApprovalModule);
document.addEventListener('DOMContentLoaded', initApprovalModule);

// ── LOAD APPROVAL QUEUE ───────────────────────────────────────────────────────
async function loadApprovalQueue() {
    const deptFilter = document.getElementById('department-filter');
    const filter     = deptFilter?.value ?? 'all';
    const tbody      = document.querySelector('.ads-table tbody');
    if (!tbody) return;

    tbody.innerHTML = `<tr><td colspan="8" class="muted">Loading…</td></tr>`;

    try {
        const res  = await fetch(`${ApprovalController}?action=get&filter=${encodeURIComponent(filter)}`);
        const json = await res.json();

        if (!json.success) {
            tbody.innerHTML = `<tr><td colspan="8" class="muted">Failed to load approvals.</td></tr>`;
            return;
        }

        if (!json.data?.length) {
            tbody.innerHTML = `<tr><td colspan="8" class="muted">No approvals found.</td></tr>`;
            return;
        }

        tbody.innerHTML = json.data.map(row => `
            <tr data-approval-id="${esc(row.approval_id)}">
                <td>${esc(row.approval_id)}</td>
                <td>${esc(row.title)}</td>
                <td>${esc(row.submit_by)}</td>
                <td>${esc(row.department_name)}</td>
                <td>${esc(row.approver_id ?? 'N/A')}</td>
                <td>
                    <span class="badge badge-${esc(row.decision ?? 'pending')}">
                        ${esc(row.decision ? row.decision.charAt(0).toUpperCase() + row.decision.slice(1) : 'Pending')}
                    </span>
                </td>
                <td>
                    ${row.file_path
                        ? `<a style="color:var(--color2);text-decoration:none;border:1px solid var(--color4);padding:4px 8px;border-radius:4px;" href="/sms/${esc(row.file_path)}" target="_blank">View</a>`
                        : '<span class="muted">No file</span>'
                    }
                </td>
            </tr>
        `).join('');

        applyApprovalSearchFilter();

    } catch (err) {
        console.error('loadApprovalQueue error:', err);
        tbody.innerHTML = `<tr><td colspan="8" class="muted">Failed to load approvals.</td></tr>`;
    }
}

// ── SEND APPROVE / REJECT ─────────────────────────────────────────────────────
async function sendDecision(action, approvalId = '') {
    const formData = new FormData();
    formData.append('action',      action);
    formData.append('approval_id', approvalId);

    try {
        const res  = await fetch(ApprovalController, { method: 'POST', body: formData });
        const json = await res.json();

        if (!json.success && json.message?.toLowerCase().includes('session expired')) {
            setTimeout(() => window.location.href = '/sms/index.php', 1500);
            return;
        }

        if (json.success) {
            showToast(action === 'approve' ? 'Approval accepted.' : 'Approval rejected.', 'success');
            loadApprovalQueue();
        } else {
            showToast(json.message, 'error');
        }

    } catch (err) {
        console.error(err);
        showToast('A network error occurred. Please try again.', 'error');
    }
}

// ── SEARCH ────────────────────────────────────────────────────────────────────
function applyApprovalSearchFilter() {
    const search = document.getElementById('approval-search');
    const term   = search?.value.toLowerCase().trim() ?? '';
    const rows   = document.querySelectorAll('.ads-table tbody tr');

    rows.forEach(row => {
        if (row.classList.contains('no-data')) return;
        const title     = row.cells[1]?.textContent.toLowerCase() ?? '';
        const submitter = row.cells[2]?.textContent.toLowerCase() ?? '';
        row.style.display = (title.includes(term) || submitter.includes(term)) ? '' : 'none';
    });
}

// ── TOAST ─────────────────────────────────────────────────────────────────────
function showToast(message, type = 'info') {
    const bg = type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#17a2b8';

    const toast = document.createElement('div');
    toast.textContent = message;
    toast.style.cssText = `
        position:fixed; bottom:24px; right:24px; z-index:9999;
        background:${bg}; color:#fff; padding:12px 20px;
        border-radius:6px; box-shadow:0 4px 12px rgba(0,0,0,.2);
        font-size:.9rem; max-width:320px;
    `;

    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3500);
}

// ── HELPERS ───────────────────────────────────────────────────────────────────
function esc(str) {
    const d = document.createElement('div');
    d.textContent = String(str ?? '');
    return d.innerHTML;
}
const ApprovalController = '/sms/modules/school-directress/controllers/ApprovalController.php';

// ── INIT ──────────────────────────────────────────────────────────────────────
window.addEventListener('page:loaded', () => {

    loadApprovalQueue();

    const uploadForm = document.getElementById('approval-upload-form');

    // --- Modal wiring ---
    const modalOverlay  = document.getElementById('approval-modal-overlay');
    const openModalBtn  = document.getElementById('approval-open-modal');
    const closeModalBtn = document.getElementById('approval-modal-close');

    const openApprovalModal = () => modalOverlay?.classList.add('active');
    const closeApprovalModal = () => {
        modalOverlay?.classList.remove('active');
        uploadForm?.reset();
    };

    openModalBtn?.addEventListener('click', openApprovalModal);
    closeModalBtn?.addEventListener('click', closeApprovalModal);
    modalOverlay?.addEventListener('click', (e) => {
        if (e.target === modalOverlay) closeApprovalModal();
    });

    if (!window.approvalEscBound) {
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeApprovalModal();
        });
        window.approvalEscBound = true;
    }

    if (uploadForm) {
        uploadForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(uploadForm);
            formData.append('action', 'submit');

            try {
                const res  = await fetch(ApprovalController, { method: 'POST', body: formData });
                const text = await res.text();
                const json = JSON.parse(text);

                if (!json.success && json.message?.toLowerCase().includes('session expired')) {
                    setTimeout(() => window.location.href = '/sms/index.php', 1500);
                    return;
                }

                if (json.success) {
                    closeApprovalModal();
                    loadApprovalQueue();
                    showToast('Approval request submitted successfully!', 'success');
                }

            } catch (err) {
                console.error(err);
            }
        });
    }

    // --- Department filter ---
    const deptFilter = document.getElementById('department-filter');
    if (deptFilter) {
        deptFilter.addEventListener('change', () => loadApprovalQueue());
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
});

// ── LOAD APPROVAL QUEUE ───────────────────────────────────────────────────────
async function loadApprovalQueue() {
    const deptFilter = document.getElementById('department-filter');
    const filter     = deptFilter?.value ?? 'all';
    const tbody      = document.querySelector('.ads-table tbody');
    if (!tbody) return;

    try {
        const res  = await fetch(`${ApprovalController}?action=get&filter=${encodeURIComponent(filter)}`);
        const json = await res.json();

        if (!json.success) {
            tbody.innerHTML = `<tr><td colspan="8">Failed to load approvals.</td></tr>`;
            return;
        }

        if (!json.data?.length) {
            tbody.innerHTML = `<tr><td colspan="8">No approvals found.</td></tr>`;
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
                        ${esc(row.decision ?? 'pending')}
                    </span>
                </td>
                <td>
                    <div class="actions">
                        <button class="btn-approve">Approve</button>
                        <button class="btn-reject">Reject</button>
                    </div>
                </td>
                <td>
                    ${row.file_path
                        ? `<a style="color:var(--color2);text-decoration:none;border:1px solid var(--color4);padding:4px 8px;border-radius:4px;" href="/sms/${esc(row.file_path)}" target="_blank">View</a>`
                        : '<span class="muted">No file</span>'
                    }
                </td>
            </tr>
        `).join('');

    } catch (err) {
        console.error('loadApprovalQueue error:', err);
    }
}

// ── SEND APPROVE / REJECT ────────────────────────────────────────────────────
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

        if (json.success) loadApprovalQueue();

    } catch (err) {
        console.error(err);
    }
}

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
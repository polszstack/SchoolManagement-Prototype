const IssuesController = '/sms/modules/school-directress/controllers/IssueController.php';

// ── INIT ──────────────────────────────────────────────────────────────────────
window.addEventListener('page:loaded', () => {

    loadIssues();

    // --- Modal wiring ---
    const modalOverlay  = document.getElementById('concern-modal-overlay');
    const openModalBtn  = document.getElementById('concern-open-modal');
    const closeModalBtn = document.getElementById('concern-modal-close');
    const logForm       = document.getElementById('concern-log-form');

    const openConcernModal = () => modalOverlay?.classList.add('active');
    const closeConcernModal = () => {
        modalOverlay?.classList.remove('active');
        logForm?.reset();
        const fileError = document.getElementById('file-error');
        if (fileError) fileError.style.display = 'none';
    };

    openModalBtn?.addEventListener('click', openConcernModal);
    closeModalBtn?.addEventListener('click', closeConcernModal);
    modalOverlay?.addEventListener('click', (e) => {
        if (e.target === modalOverlay) closeConcernModal();
    });

    if (!window.concernEscBound) {
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeConcernModal();
        });
        window.concernEscBound = true;
    }

    // --- Submit concern ---
    if (logForm) {
        logForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const fileError = document.getElementById('file-error');
            const submitBtn = logForm.querySelector('.btn-log');
            fileError.style.display = 'none';

            const formData = new FormData(logForm);
            formData.append('action', 'submit');

            submitBtn.disabled    = true;
            submitBtn.textContent = 'Submitting…';

            try {
                const res = await fetch(IssuesController, { method: 'POST', body: formData });
                const raw = await res.text();

                let json;
                try {
                    json = JSON.parse(raw);
                } catch {
                    fileError.textContent   = 'Server error — check console for details.';
                    fileError.style.display = 'block';
                    return;
                }

                if (!json.success && json.message?.toLowerCase().includes('session expired')) {
                    setTimeout(() => window.location.href = '/sms/index.php', 1500);
                    return;
                }

                if (json.success) {
                    closeConcernModal();
                    showToast('Concern logged successfully!', 'success');
                    loadIssues();
                } else {
                    fileError.textContent   = json.message;
                    fileError.style.display = 'block';
                }

            } catch (err) {
                console.error(err);
                fileError.textContent   = 'A network error occurred. Please try again.';
                fileError.style.display = 'block';
            } finally {
                submitBtn.disabled    = false;
                submitBtn.textContent = 'Log Concern';
            }
        });
    }

    // --- Status filter ---
    const statusFilter = document.getElementById('concern-filter');
    if (statusFilter) {
        statusFilter.addEventListener('change', () => loadIssues());
    }

    // --- Search ---
    const search = document.getElementById('concern-search');
    if (search) {
        search.addEventListener('input', () => loadIssues());
    }

    // --- Resolve / Reopen (delegated) ---
    document.addEventListener('click', async (e) => {
        const row     = e.target.closest('tr');
        const issueId = row?.dataset.issueId;

        if (e.target.classList.contains('btn-resolve')) {
            if (!issueId) return;
            await sendStatusUpdate(issueId, 'resolved');
        }

        if (e.target.classList.contains('btn-reopen')) {
            if (!issueId) return;
            await sendStatusUpdate(issueId, 'open');
        }
    });
});

// ── LOAD ISSUES ───────────────────────────────────────────────────────────────
async function loadIssues() {
    const status = document.getElementById('concern-filter')?.value       ?? 'all';
    const search = document.getElementById('concern-search')?.value.trim() ?? '';
    const tbody  = document.querySelector('.concern-table tbody');
    if (!tbody) return;

    tbody.innerHTML = `<tr><td colspan="8" class="muted">Loading…</td></tr>`;

    try {
        const res  = await fetch(`${IssuesController}?action=list&status=${encodeURIComponent(status)}&search=${encodeURIComponent(search)}`);
        const json = await res.json();

        if (!json.success) {
            tbody.innerHTML = `<tr><td colspan="8" class="muted">Failed to load issues.</td></tr>`;
            return;
        }

        if (!json.data?.length) {
            tbody.innerHTML = `<tr><td colspan="8" class="muted">No issues found.</td></tr>`;
            return;
        }

        tbody.innerHTML = json.data.map(row => `
            <tr data-issue-id="${esc(row.issue_id)}">
                <td>${esc(row.issue_id)}</td>
                <td>${esc(row.title)}</td>
                <td>${esc(row.department_name)}</td>
                <td>${esc(row.submitted_by)}</td>
                <td>${esc(row.submitted_on)}</td>
                <td>
                    <span class="badge badge-${esc(row.status ?? 'open')}">
                        ${esc(row.status ? row.status.charAt(0).toUpperCase() + row.status.slice(1) : '—')}
                    </span>
                </td>
                <td>
                    ${row.file_path
                        ? `<a style="color:var(--color2);text-decoration:none;border:1px solid var(--color4);padding:4px 8px;border-radius:4px;" href="/sms/${esc(row.file_path)}" target="_blank">View</a>`
                        : '<span class="muted">—</span>'
                    }
                </td>
                <td>
                    <div class="actions">
                        ${row.status !== 'resolved'
                            ? `<button class="btn-resolve">Resolve</button>`
                            : `<button class="btn-reopen">Reopen</button>`
                        }
                    </div>
                </td>
            </tr>
        `).join('');

    } catch (err) {
        console.error('loadIssues error:', err);
        tbody.innerHTML = `<tr><td colspan="8" class="muted">Failed to load issues.</td></tr>`;
    }
}

// ── SEND STATUS UPDATE ────────────────────────────────────────────────────────
async function sendStatusUpdate(issueId, status) {
    const formData = new FormData();
    formData.append('action',   'update_status');
    formData.append('issue_id', issueId);
    formData.append('status',   status);

    try {
        const res  = await fetch(IssuesController, { method: 'POST', body: formData });
        const json = await res.json();

        if (!json.success && json.message?.toLowerCase().includes('session expired')) {
            setTimeout(() => window.location.href = '/sms/index.php', 1500);
            return;
        }

        if (json.success) loadIssues();
        else showToast(json.message, 'error');

    } catch (err) {
        console.error(err);
    }
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
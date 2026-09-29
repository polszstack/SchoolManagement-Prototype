const ReportController = '/sms/modules/school-directress/controllers/ReportController.php';

// ── INIT ──────────────────────────────────────────────────────────────────────
function initReportModule() {

    loadReports();

    // --- Submit Report ---
    const uploadForm  = document.getElementById('report-upload-form');
    if (uploadForm) {
        uploadForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const fileError    = document.getElementById('file-error');
            const submitBtn    = document.getElementById('submit-btn');
            const progressWrap = document.getElementById('upload-progress');
            const progressBar  = document.getElementById('progress-bar');
            const progressText = document.getElementById('progress-text');

            fileError.style.display    = 'none';
            submitBtn.disabled         = true;
            submitBtn.textContent      = 'Uploading…';
            progressWrap.style.display = 'block';

            const formData = new FormData(uploadForm);
            formData.append('action', 'submit');

            try {
                const res  = await fetch(ReportController, { method: 'POST', body: formData });
                const json = await res.json();

                if (!json.success && json.message?.toLowerCase().includes('session expired')) {
                    setTimeout(() => window.location.href = '/sms/index.php', 1500);
                    return;
                }

                if (json.success) {
                    uploadForm.reset();
                    showToast('Report submitted successfully!', 'success');
                    loadReports();
                } else {
                    fileError.textContent   = json.message;
                    fileError.style.display = 'block';
                }

            } catch (err) {
                console.error(err);
                fileError.textContent   = 'A network error occurred. Please try again.';
                fileError.style.display = 'block';
            } finally {
                submitBtn.disabled         = false;
                submitBtn.textContent      = 'Submit Report';
                progressWrap.style.display = 'none';
                progressBar.style.width    = '0%';
                progressText.textContent   = '0%';
            }
        });
    }

    // --- Department filter ---
    const deptFilter = document.getElementById('rsm-filter');
    if (deptFilter) {
        deptFilter.addEventListener('change', () => loadReports());
    }

    // --- Search ---
    const search = document.getElementById('rsm-search');
    if (search) {
        search.addEventListener('input', applySearchFilter);
    }
}

// Handles both hard refresh and sidebar navigation
window.addEventListener('page:loaded', initReportModule);
document.addEventListener('DOMContentLoaded', initReportModule);

// ── LOAD REPORTS ──────────────────────────────────────────────────────────────
async function loadReports() {
    const deptFilter    = document.getElementById('rsm-filter');
    const department_id = deptFilter?.value ?? '';
    const tbody         = document.querySelector('.rsm-table tbody');
    if (!tbody) return;

    tbody.innerHTML = `<tr><td colspan="7" class="muted">Loading…</td></tr>`;

    try {
        const res  = await fetch(`${ReportController}?action=list&department_id=${encodeURIComponent(department_id)}`);
        const json = await res.json();

        if (!json.success) {
            tbody.innerHTML = `<tr><td colspan="7" class="muted">Failed to load reports.</td></tr>`;
            return;
        }

        if (!json.data?.length) {
            tbody.innerHTML = `<tr><td colspan="7" class="muted">No reports found.</td></tr>`;
            return;
        }

        tbody.innerHTML = json.data.map(row => `
            <tr>
                <td>${esc(row.report_id)}</td>
                <td>${esc(row.title)}</td>
                <td>${esc(row.report_type ?? 'N/A')}</td>
                <td>${esc(row.department_name)}</td>
                <td>${esc(row.submitted_by)}</td>
                <td>${esc(row.submitted_at)}</td>
                <td>
                    ${row.file_path
                        ? `<a style="color:var(--color2);text-decoration:none; border:1px solid var(--color2); padding:5px 10px; border-radius:4px;" href="/sms/${esc(row.file_path)}" target="_blank">View</a>`
                        : '<span class="muted">No file</span>'
                    }
                </td>
            </tr>
        `).join('');

        applySearchFilter();

    } catch (err) {
        console.error('loadReports error:', err);
        tbody.innerHTML = `<tr><td colspan="7" class="muted">Failed to load reports.</td></tr>`;
    }
}

// ── SEARCH ────────────────────────────────────────────────────────────────────
function applySearchFilter() {
    const search = document.getElementById('rsm-search');
    const term   = search?.value.toLowerCase().trim() ?? '';
    const rows   = document.querySelectorAll('.rsm-table tbody tr');

    rows.forEach(row => {
        if (row.classList.contains('no-data')) return;
        const title     = row.cells[1]?.textContent.toLowerCase() ?? '';
        const submitter = row.cells[4]?.textContent.toLowerCase() ?? '';
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
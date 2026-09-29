const PAGE_SIZE = 10;

let smEls         = {};
let smCurrentPage = 1;
let smLastMatched = [];

// ── INIT ──────────────────────────────────────────────────────────────────────
function initStudentMonitoringModule() {
    smQueryElements();
    if (!smEls.body) return; // this page fragment isn't the one currently loaded

    smBindEvents();
    smRunFilter();
}

// Handles both hard refresh and sidebar navigation
window.addEventListener('page:loaded', initStudentMonitoringModule);
document.addEventListener('DOMContentLoaded', initStudentMonitoringModule);

function smQueryElements() {
    smEls = {
        search:    document.getElementById('smSearch'),
        status:    document.getElementById('smStatus'),
        course:    document.getElementById('smCourse'),
        year:      document.getElementById('smYear'),
        body:      document.getElementById('smBody'),
        showing:   document.getElementById('smShowing'),
        pageNav:   document.getElementById('smPageNav'),
        exportBtn: document.getElementById('smExportBtn'),
    };
}

// ── ROW EXPAND / COLLAPSE ────────────────────────────────────────────────────
function smGetDetailRow(row) {
    const m = row.getAttribute('onclick')?.match(/'(smd-[^']+)'/);
    return m ? document.getElementById(m[1]) : null;
}

function smCollapseRow(row) {
    const dr = smGetDetailRow(row);
    if (dr) dr.classList.remove('sm--open');
    row.classList.remove('sm-row--expanded');
}

// Called directly from the inline onclick="" in the markup
function smToggle(tr, rid) {
    const detail = document.getElementById(rid);
    if (!detail) return;

    const isOpen = detail.classList.contains('sm--open');

    document.querySelectorAll('.sm-detail-row.sm--open').forEach(el => el.classList.remove('sm--open'));
    document.querySelectorAll('.sm-row.sm-row--expanded').forEach(el => el.classList.remove('sm-row--expanded'));

    if (!isOpen) {
        detail.classList.add('sm--open');
        tr.classList.add('sm-row--expanded');
    }
}

// ── FILTER MATCHING (shared by pagination + CSV export) ─────────────────────
function smCurrentFilters() {
    return {
        q:  smEls.search ? smEls.search.value.toLowerCase().trim() : '',
        st: smEls.status ? smEls.status.value : '',
        co: smEls.course ? smEls.course.value : '',
        yr: smEls.year   ? smEls.year.value   : '',
    };
}

function smRowMatches(row, f) {
    return (!f.q  || row.dataset.name.includes(f.q) || row.dataset.snum.includes(f.q) || row.dataset.email.includes(f.q)) &&
           (!f.st || row.dataset.status === f.st) &&
           (!f.co || row.dataset.course === f.co) &&
           (!f.yr || row.dataset.year   === f.yr);
}

// ── FILTER + PAGINATE ────────────────────────────────────────────────────────
function smRunFilter() {
    if (!smEls.body) return;

    const f       = smCurrentFilters();
    const allRows = Array.from(smEls.body.querySelectorAll('.sm-row'));

    smLastMatched = allRows.filter(row => {
        const ok = smRowMatches(row, f);
        if (!ok) {
            row.style.display = 'none';
            smCollapseRow(row);
        }
        return ok;
    });

    smCurrentPage = 1; // reset to page 1 whenever the filter changes
    smPaginate();
}

function smPaginate() {
    const total      = smLastMatched.length;
    const totalPages = Math.max(1, Math.ceil(total / PAGE_SIZE));
    if (smCurrentPage > totalPages) smCurrentPage = totalPages;

    const start = (smCurrentPage - 1) * PAGE_SIZE;
    const end   = start + PAGE_SIZE;

    smLastMatched.forEach((row, idx) => {
        const onPage = idx >= start && idx < end;
        row.style.display = onPage ? '' : 'none';
        if (!onPage) smCollapseRow(row);
    });

    smUpdateShowingText(total, start, end);
    smRenderPageButtons(totalPages);
}

function smUpdateShowingText(total, start, end) {
    if (!smEls.showing) return;

    if (total === 0) {
        smEls.showing.innerHTML = 'No students found';
        return;
    }

    const from = start + 1;
    const to   = Math.min(end, total);
    smEls.showing.innerHTML = `Showing <strong>${from}-${to}</strong> of <strong>${total}</strong> students`;
}

function smRenderPageButtons(totalPages) {
    if (!smEls.pageNav) return;

    smEls.pageNav.innerHTML = '';

    for (let p = 1; p <= totalPages; p++) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'sm-page-btn' + (p === smCurrentPage ? ' sm-page-btn--active' : '');
        btn.textContent = p;
        btn.addEventListener('click', () => {
            smCurrentPage = p;
            smPaginate();
        });
        smEls.pageNav.appendChild(btn);
    }
}

// ── CSV EXPORT (exports ALL filtered rows, not just the visible page) ───────
function smExportCSV() {
    if (!smEls.body) return;

    const f     = smCurrentFilters();
    const rows  = Array.from(smEls.body.querySelectorAll('.sm-row')).filter(row => smRowMatches(row, f));
    const lines = [['Name', 'Student Number', 'Course', 'Status', 'Year Level', 'Sex', 'Email']];

    rows.forEach(r => {
        lines.push([
            r.querySelector('.sm-name')?.textContent.trim()  || '',
            r.querySelector('.sm-mono')?.textContent.trim()  || '',
            r.dataset.course || '',
            r.querySelector('.sm-badge')?.textContent.trim() || '',
            r.querySelector('.sm-year')?.textContent.trim()  || '',
            r.dataset.sex   || '',
            r.dataset.email || '',
        ]);
    });

    const csv  = lines.map(row => row.map(v => `"${String(v).replace(/"/g, '""')}"`).join(',')).join('\n');
    const blob = new Blob([csv], { type: 'text/csv' });
    const a    = document.createElement('a');

    a.href     = URL.createObjectURL(blob);
    a.download = `enrolled_students_${new Date().toISOString().slice(0, 10)}.csv`;
    a.click();
    URL.revokeObjectURL(a.href);
}

// ── BIND ──────────────────────────────────────────────────────────────────────
function smBindEvents() {
    [smEls.search, smEls.status, smEls.course, smEls.year].forEach(el => {
        if (!el) return;
        el.addEventListener(el.tagName === 'INPUT' ? 'input' : 'change', smRunFilter);
    });

    if (smEls.exportBtn) smEls.exportBtn.addEventListener('click', smExportCSV);
}
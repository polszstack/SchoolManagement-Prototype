'use strict';
// ============================================================
// TransactionsController — Enhanced with search, days-left, export
// ============================================================
class TransactionsController {
    #api; #ui;
    #pager      = new Paginator(10);
    #searchTerm = '';
    #debounceId = null;

    constructor(api, ui) { this.#api = api; this.#ui = ui; }

    async load() {
        const status = document.getElementById('transStatusFilter')?.value || '';
        const search = this.#searchTerm;
        const data   = await this.#api.get({ action: 'get_transactions', status, search, page: this.#pager.page, limit: this.#pager.pageSize });

        this.#pager.total = data.total || 0;

        // Update badge counts
        this.#updateBadges();

        const tbody = document.getElementById('transTableBody');
        if (!data.transactions?.length) {
            tbody.innerHTML = '<tr><td colspan="8" class="empty-state"><p>No transactions found</p></td></tr>';
        } else {
            tbody.innerHTML = data.transactions.map(t => {
                const daysLeft = t.return_date ? null : this.#daysLeft(t.due_date);
                let daysHtml = '—';
                if (daysLeft !== null) {
                    if (daysLeft < 0)       daysHtml = `<span class="badge badge-danger">${Math.abs(daysLeft)}d late</span>`;
                    else if (daysLeft === 0) daysHtml = `<span class="badge badge-warning">Today</span>`;
                    else if (daysLeft <= 3)  daysHtml = `<span class="badge badge-warning">${daysLeft}d</span>`;
                    else                     daysHtml = `<span class="badge badge-info">${daysLeft}d</span>`;
                }

                return `
                <tr class="${t.status === 'overdue' ? 'row-overdue' : ''}">
                    <td><div class="d-flex gap-1 align-center">${this.#ui.bookCoverHTML(t, 24)}<span class="fw-600 small">${this.#ui.escH(t.book_title)}</span></div></td>
                    <td>${this.#ui.escH(t.borrower_name)}<br><small class="text-muted">${this.#ui.escH(t.member_id)}</small></td>
                    <td class="small">${t.borrow_date}</td>
                    <td class="small">${t.due_date}</td>
                    <td>${daysHtml}</td>
                    <td class="small">${t.return_date || '—'}</td>
                    <td><span class="badge ${this.#ui.badgeClass(t.status)}">${t.status}</span></td>
                    <td>${t.fine > 0 ? `<span class="text-danger fw-600">₱${t.fine}</span>` : '<span class="text-muted">₱0.00</span>'}</td>
                </tr>`;
            }).join('');
        }
        document.getElementById('transPaginationInfo').textContent = this.#pager.infoText('transactions');
    }

    search(value) {
        this.#searchTerm = (value ?? '').trim();
        this.#pager.reset();
        clearTimeout(this.#debounceId);
        this.#debounceId = setTimeout(() => this.load(), 300);
    }

    changePage(dir) { this.#pager.move(dir); this.load(); }

    async #updateBadges() {
        try {
            const [active, overdue] = await Promise.all([
                this.#api.get({ action: 'get_transactions', status: 'active',  limit: 1 }),
                this.#api.get({ action: 'get_transactions', status: 'overdue', limit: 1 }),
            ]);
            const ba = document.getElementById('txnBadgeActive');
            const bo = document.getElementById('txnBadgeOverdue');
            if (ba) ba.textContent = `Active: ${active.total ?? 0}`;
            if (bo) bo.textContent = `Overdue: ${overdue.total ?? 0}`;
        } catch(e) { /* non-fatal */ }
    }

    #daysLeft(dueDateStr) {
        const due  = new Date(dueDateStr);
        const now  = new Date();
        now.setHours(0,0,0,0);
        due.setHours(0,0,0,0);
        return Math.round((due - now) / 86400000);
    }

    async exportCSV() {
        const status = document.getElementById('transStatusFilter')?.value || '';
        const data   = await this.#api.get({ action: 'get_transactions', status, limit: 9999 });
        if (!data.transactions?.length) { alert('No data to export.'); return; }

        const headers = ['Book', 'Member', 'Member ID', 'Borrow Date', 'Due Date', 'Return Date', 'Status', 'Fine'];
        const rows    = data.transactions.map(t => [
            `"${(t.book_title   || '').replace(/"/g,'""')}"`,
            `"${(t.borrower_name || '').replace(/"/g,'""')}"`,
            `"${(t.member_id    || '').replace(/"/g,'""')}"`,
            t.borrow_date, t.due_date, t.return_date || '',
            t.status,
            t.fine ?? 0,
        ]);

        const csv  = [headers.join(','), ...rows.map(r => r.join(','))].join('\n');
        const blob = new Blob([csv], { type: 'text/csv' });
        const url  = URL.createObjectURL(blob);
        const a    = document.createElement('a');
        a.href     = url;
        a.download = `transactions_${new Date().toISOString().slice(0,10)}.csv`;
        a.click();
        URL.revokeObjectURL(url);
    }
}

// ============================================================
// ReportsController
// ============================================================
class ReportsController {
    #api; #ui;

    constructor(api, ui) { this.#api = api; this.#ui = ui; }

    async load() {
        const data = await this.#api.get({ action: 'get_report' });
        const { stats, genreBreakdown, overdueList, topBorrowed } = data;

        document.getElementById('reportsContent').innerHTML = `
        <div class="grid-3 mb-2">
            ${this.#statBox(stats.total_books,       'Total Books',    'var(--primary)')}
            ${this.#statBox(stats.available,         'Available',      'var(--success)')}
            ${this.#statBox(stats.borrowed,          'Borrowed',       'var(--warning)')}
            ${this.#statBox(stats.overdue,           'Overdue',        'var(--danger)')}
            ${this.#statBox(stats.total_members,     'Members',        'var(--info)')}
            ${this.#statBox('₱' + stats.total_fines, 'Pending Fines',  'var(--danger)')}
        </div>
        <div class="grid-2">
            <div>
                <h4 class="mb-1"><i class="fas fa-tag" style="color:var(--primary)"></i> Books by Genre</h4>
                ${genreBreakdown.map(g => `
                <div style="margin-bottom:.6rem">
                    <div class="d-flex gap-1 align-center" style="justify-content:space-between;margin-bottom:.2rem">
                        <span class="fw-600 small">${this.#ui.escH(g.genre)}</span>
                        <span class="text-muted small">${g.count} books</span>
                    </div>
                    <div class="progress-bar"><div class="progress-fill" style="width:${Math.round(g.count/stats.total_books*100)}%"></div></div>
                </div>`).join('')}
            </div>
            <div>
                <h4 class="mb-1"><i class="fas fa-star" style="color:var(--warning)"></i> Most Borrowed</h4>
                ${topBorrowed.length ? topBorrowed.map((b,i) => `
                <div class="d-flex gap-1 align-center mb-1" style="padding:.45rem 0;border-bottom:1px solid var(--border-color)">
                    <span style="font-size:1.1rem;font-weight:700;color:var(--gray-light);width:22px">${i+1}</span>
                    <div style="flex:1"><div class="fw-600 small">${this.#ui.escH(b.title)}</div><div class="text-muted small">${this.#ui.escH(b.author)}</div></div>
                    <span class="badge badge-info">${b.borrow_count}×</span>
                </div>`).join('') : '<p class="text-muted">No data yet</p>'}

                <h4 class="mt-2 mb-1"><i class="fas fa-clock" style="color:var(--danger)"></i> Overdue Books</h4>
                ${overdueList.length ? overdueList.map(o => `
                <div style="padding:.45rem 0;border-bottom:1px solid var(--border-color)">
                    <div class="fw-600 small">${this.#ui.escH(o.book_title)}</div>
                    <div class="text-muted small">${this.#ui.escH(o.borrower_name)} · Due: ${o.due_date} · <span class="text-danger fw-600">₱${o.fine}</span></div>
                </div>`).join('') : '<p class="text-muted">No overdue books 🎉</p>'}
            </div>
        </div>`;
    }

    #statBox(val, label, color) {
        return `<div style="background:var(--light);padding:1.1rem;border-radius:var(--border-radius);text-align:center">
            <div style="font-size:1.75rem;font-weight:700;color:${color}">${val}</div>
            <div class="text-muted small">${label}</div>
        </div>`;
    }
}

// ============================================================
// SettingsController
// ============================================================
class SettingsController {
    #api; #ui;

    constructor(api, ui) { this.#api = api; this.#ui = ui; }

    async load() {
        const s = await this.#api.get({ action: 'get_settings' });
        if (s.library_name)         document.getElementById('settingLibraryName').value   = s.library_name;
        if (s.max_borrow_days)      document.getElementById('settingMaxBorrowDays').value  = s.max_borrow_days;
        if (s.max_books_per_member) document.getElementById('settingMaxBooks').value       = s.max_books_per_member;
        if (s.daily_fine_rate)      document.getElementById('settingFineRate').value       = s.daily_fine_rate;
    }

    async save() {
        const payload = {
            library_name:         document.getElementById('settingLibraryName').value.trim(),
            max_borrow_days:      document.getElementById('settingMaxBorrowDays').value,
            max_books_per_member: document.getElementById('settingMaxBooks').value,
            daily_fine_rate:      document.getElementById('settingFineRate').value,
        };
        const res = await this.#api.post('save_settings', payload);
        if (res.error) { this.#ui.showNotif(res.error, 'error'); return; }
        this.#ui.showNotif(res.message, 'success');
    }
}

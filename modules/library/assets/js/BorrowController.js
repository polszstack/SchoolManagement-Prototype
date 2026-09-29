'use strict';
// ============================================================
// BorrowController — issue and return books
// ============================================================
class BorrowController {
    #api; #ui;
    #transactionTitles = {};

    constructor(api, ui) { this.#api = api; this.#ui = ui; }

    async loadDropdowns() {
        const [booksData, membersData] = await Promise.all([
            this.#api.get({ action: 'get_books', status: 'available', limit: 500 }),
            this.#api.get({ action: 'get_borrowers', limit: 500 }),
        ]);
        const bSel = document.getElementById('borrowBookId');
        const mSel = document.getElementById('borrowMemberId');
        bSel.innerHTML = '<option value="">— Select Available Book —</option>'
            + (booksData.books || []).map(b => `<option value="${b.id}">${this.#ui.escH(b.title)} — ${this.#ui.escH(b.author)}</option>`).join('');
        mSel.innerHTML = '<option value="">— Select Member —</option>'
            + (membersData.borrowers || []).map(m => `<option value="${m.id}">${this.#ui.escH(m.name)} (${this.#ui.escH(m.borrower_id)})</option>`).join('');
    }

    async loadActive() {
        const data  = await this.#api.get({ action: 'get_transactions', status: 'active', limit: 20 });
        const panel = document.getElementById('activeBorrowingsPanel');
        if (!data.transactions?.length) {
            panel.innerHTML = '<div class="empty-state"><i class="fas fa-book-reader"></i><p>No active borrowings</p></div>';
            return;
        }
        panel.innerHTML = data.transactions.map(t => `
            <div class="d-flex gap-1 align-center mb-1" style="padding:.75rem 0;border-bottom:1px solid var(--border-color)">
                ${this.#ui.bookCoverHTML(t, 28)}
                <div style="flex:1;min-width:0">
                    <div class="fw-600 small" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${this.#ui.escH(t.book_title)}</div>
                    <div class="text-muted small">${this.#ui.escH(t.borrower_name)} &nbsp;·&nbsp; Due: ${t.due_date}</div>
                </div>
            </div>`).join('');
    }

    async issue() {
        const book_id     = document.getElementById('borrowBookId').value;
        const borrower_id = document.getElementById('borrowMemberId').value;
        const borrow_days = document.getElementById('borrowDays').value;
        const notes       = document.getElementById('borrowNotes').value.trim();

        if (!book_id || !borrower_id) { this.#ui.showNotif('Please select both book and member.', 'error'); return; }

        const res = await this.#api.post('borrow_book', { book_id: +book_id, borrower_id: +borrower_id, borrow_days: +borrow_days, notes });
        if (res.error) { this.#ui.showNotif(res.error, 'error'); return; }
        this.#ui.showNotif(res.message, 'success');

        document.getElementById('borrowBookId').value   = '';
        document.getElementById('borrowMemberId').value = '';
        document.getElementById('borrowNotes').value    = '';
        this.loadDropdowns();
        this.loadActive();
        app.dashboard.load();
    }

    async loadReturnList() {
        const data  = await this.#api.get({ action: 'get_transactions', status: 'active', limit: 100 });
        const panel = document.getElementById('returnableList');
        if (!data.transactions?.length) {
            panel.innerHTML = '<div class="empty-state"><i class="fas fa-check-circle"></i><p>No books to return</p></div>';
            return;
        }

        this.#transactionTitles = {};
        data.transactions.forEach(t => { this.#transactionTitles[t.id] = t.book_title; });

        panel.innerHTML = `
        <div class="table-responsive">
        <table>
            <thead><tr><th>Cover</th><th>Book</th><th>Member</th><th>Due Date</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
            ${data.transactions.map(t => `
            <tr class="${t.status === 'overdue' ? 'row-overdue' : ''}">
                <td>${this.#ui.bookCoverHTML(t, 28)}</td>
                <td><strong>${this.#ui.escH(t.book_title)}</strong></td>
                <td>${this.#ui.escH(t.borrower_name)}<br><small class="text-muted">${this.#ui.escH(t.member_id)}</small></td>
                <td>${t.due_date}${t.status === 'overdue' ? '<br><span class="badge badge-danger">OVERDUE</span>' : ''}</td>
                <td><span class="badge ${this.#ui.badgeClass(t.status)}">${t.status}</span></td>
                <td><button class="btn btn-sm btn-success" onclick="app.borrow.openReturnModal(${t.id}, ${t.fine || 0})"><i class="fas fa-undo"></i> Return</button></td>
            </tr>`).join('')}
            </tbody>
        </table>
        </div>`;
    }

    openReturnModal(transId, currentFine) {
        const title = this.#transactionTitles[transId] || 'Unknown Book';
        document.getElementById('returnModalContent').innerHTML = `
            <p class="mb-2">Returning: <strong>${this.#ui.escH(title)}</strong></p>
            <div class="form-group"><label>Book Condition</label>
                <select id="returnCondition">
                    <option value="Excellent">Excellent</option>
                    <option value="Good" selected>Good</option>
                    <option value="Fair">Fair</option>
                    <option value="Poor">Poor</option>
                </select>
            </div>
            <div class="form-group"><label>Fine to Collect (₱)</label>
                <input type="number" id="returnFine" value="${currentFine || 0}" min="0" step="0.50">
            </div>
            <button class="btn btn-success w-100" onclick="app.borrow.confirmReturn(${transId})">
                <i class="fas fa-check"></i> Confirm Return
            </button>`;
        this.#ui.openModal('returnModal');
    }

    async confirmReturn(transId) {
        const condition = document.getElementById('returnCondition').value;
        const fine      = parseFloat(document.getElementById('returnFine').value) || 0;
        const res = await this.#api.post('return_book', { transaction_id: transId, condition, fine });
        if (res.error) { this.#ui.showNotif(res.error, 'error'); return; }
        this.#ui.showNotif(res.message, 'success');
        this.#ui.closeModal('returnModal');
        this.loadReturnList();
        app.dashboard.load();
        app.transactions.load();
    }
}

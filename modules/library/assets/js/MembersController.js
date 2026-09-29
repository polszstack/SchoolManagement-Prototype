'use strict';
// ============================================================
// MembersController
// ============================================================
class MembersController {
    #api; #ui;
    #pager          = new Paginator(10);
    #searchTerm     = '';
    #debounceId     = null;
    #studentDebounce = null;

    constructor(api, ui) { this.#api = api; this.#ui = ui; }

    async load() {
        const search = this.#searchTerm;
        const type   = document.getElementById('memberTypeFilter')?.value || '';
        const data   = await this.#api.get({ action: 'get_borrowers', search, type, page: this.#pager.page, limit: this.#pager.pageSize });

        this.#pager.total = data.total || 0;
        const tbody = document.getElementById('membersTableBody');

        if (!data.borrowers?.length) {
            tbody.innerHTML = `<tr><td colspan="4" class="empty-state">
                <i class="fas fa-users" style="display:block;font-size:2rem;color:var(--gray-light);margin-bottom:.5rem"></i>
                <p>${search ? `No members matching "<strong>${this.#ui.escH(search)}</strong>"` : 'No members found'}</p>
            </td></tr>`;
        } else {
            tbody.innerHTML = data.borrowers.map(m => {
                const typeColors = { Student: '#2563eb', Teacher: '#059669', Staff: '#d97706' };
                const tc = typeColors[m.type] || '#64748b';
                const overdueHTML = m.overdue_count > 0
                    ? `<span class="badge badge-danger" style="font-size:.7rem">${m.overdue_count} overdue</span>`
                    : '';
                return `<tr>
                    <td>
                        <strong>${this.#ui.escH(m.name)}</strong><br>
                        <small class="text-muted">${this.#ui.escH(m.borrower_id)}</small>
                    </td>
                    <td><span style="color:${tc};font-weight:600">${m.type}</span>${m.grade ? `<br><small class="text-muted">${this.#ui.escH(m.grade)}</small>` : ''}</td>
                    <td>
                        <span class="badge badge-info">${m.currently_borrowed || 0} active</span>
                        ${overdueHTML}
                    </td>
                    <td><div class="d-flex gap-1">
                        <button class="btn btn-sm btn-outline" onclick="app.members.view(${m.id})" title="View"><i class="fas fa-eye"></i></button>
                        <button class="btn btn-sm btn-outline" onclick="app.members.edit(${m.id})" title="Edit"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-outline btn-danger" onclick="app.members.delete(${m.id})" title="Delete"><i class="fas fa-trash"></i></button>
                    </div></td>
                </tr>`;
            }).join('');
        }
        document.getElementById('membersPaginationInfo').textContent = this.#pager.infoText('members');
    }

    search(value) {
        this.#searchTerm = (value ?? '').trim();
        this.#pager.reset();
        clearTimeout(this.#debounceId);
        this.#debounceId = setTimeout(() => this.load(), 300);
    }

    filter()        { this.#pager.reset(); this.load(); }
    changePage(dir) { this.#pager.move(dir); this.load(); }

    async submit() {
        const id         = document.getElementById('editMemberId').value;
        const name       = document.getElementById('memberName').value.trim();
        const borrowerId = document.getElementById('memberBorrowerId').value.trim();
        const email      = document.getElementById('memberEmail').value.trim();
        const phone      = document.getElementById('memberPhone').value.trim();
        const type       = document.getElementById('memberType').value;
        const grade      = document.getElementById('memberGrade').value.trim();
        const address    = document.getElementById('memberAddress').value.trim();

        if (!name) { this.#ui.showNotif('Name is required.', 'error'); return; }
        if (!id && !borrowerId) { this.#ui.showNotif('Member ID is required.', 'error'); return; }

        const action  = id ? 'update_borrower' : 'add_borrower';
        const payload = { name, email, phone, type, grade, address };
        if (id) payload.id = parseInt(id);
        else    payload.borrower_id = borrowerId;

        const btn = document.getElementById('memberSubmitBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

        const res = await this.#api.post(action, payload);
        btn.disabled = false;
        btn.innerHTML = id ? '<i class="fas fa-save"></i> Update Member' : '<i class="fas fa-user-plus"></i> Add Member';

        if (res.error) { this.#ui.showNotif(res.error, 'error'); return; }
        this.#ui.showNotif(res.message, 'success');
        this.cancelEdit();
        this.load();
    }

    async edit(id) {
        const m = await this.#api.get({ action: 'get_borrower', id });
        if (m.error) { this.#ui.showNotif(m.error, 'error'); return; }

        document.getElementById('editMemberId').value        = m.id;
        document.getElementById('memberName').value          = m.name;
        document.getElementById('memberBorrowerId').value    = m.borrower_id;
        document.getElementById('memberBorrowerId').disabled = true;
        document.getElementById('memberEmail').value         = m.email   || '';
        document.getElementById('memberPhone').value         = m.phone   || '';
        document.getElementById('memberType').value          = m.type;
        document.getElementById('memberGrade').value         = m.grade   || '';
        document.getElementById('memberAddress').value       = m.address || '';

        document.getElementById('memberFormTitle').textContent   = 'Edit Member';
        document.getElementById('memberSubmitBtn').innerHTML     = '<i class="fas fa-save"></i> Update Member';
        document.getElementById('memberCancelBtn').style.display = 'inline-flex';
        document.getElementById('memberName').scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    cancelEdit() {
        document.getElementById('editMemberId').value = '';
        ['memberName','memberBorrowerId','memberEmail','memberPhone','memberGrade','memberAddress'].forEach(id => {
            const el = document.getElementById(id);
            el.value    = '';
            el.disabled = false;
        });
        document.getElementById('memberType').value              = 'Student';
        document.getElementById('memberFormTitle').textContent   = 'Add New Member';
        document.getElementById('memberSubmitBtn').innerHTML     = '<i class="fas fa-user-plus"></i> Add Member';
        document.getElementById('memberCancelBtn').style.display = 'none';
    }

    async delete(id) {
        if (!confirm('Delete this member? This cannot be undone.')) return;
        const res = await this.#api.post('delete_borrower', { id });
        if (res.error) { this.#ui.showNotif(res.error, 'error'); return; }
        this.#ui.showNotif(res.message, 'success');
        this.load();
    }

    async view(id) {
        const m = await this.#api.get({ action: 'get_borrower', id });
        if (m.error) { this.#ui.showNotif(m.error, 'error'); return; }

        const typeColors = { Student: '#2563eb', Teacher: '#059669', Staff: '#d97706' };
        const tc = typeColors[m.type] || '#64748b';

        document.getElementById('modalTitle').textContent = m.name;
        document.getElementById('modalContent').innerHTML = `
            <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem">
                <div style="width:60px;height:60px;border-radius:50%;background:${tc};display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.4rem;flex-shrink:0">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    <h3 style="margin:0">${this.#ui.escH(m.name)}</h3>
                    <div class="text-muted">${this.#ui.escH(m.borrower_id)} &nbsp;·&nbsp; <span style="color:${tc};font-weight:600">${m.type}</span></div>
                </div>
            </div>
            <div class="grid-2 mb-2">
                <div>
                    <p><strong>Course/Grade:</strong> ${this.#ui.escH(m.grade || 'N/A')}</p>
                    <p><strong>Email:</strong> ${this.#ui.escH(m.email || 'N/A')}</p>
                    <p><strong>Phone:</strong> ${this.#ui.escH(m.phone || 'N/A')}</p>
                    <p><strong>Joined:</strong> ${m.join_date}</p>
                </div>
                <div><p><strong>Address:</strong> ${this.#ui.escH(m.address || 'N/A')}</p></div>
            </div>
            <h4 class="mb-1">Borrowing History</h4>
            ${m.history?.length ? m.history.map(h => `
            <div style="border-left:3px solid var(--primary);padding-left:1rem;margin-bottom:.75rem">
                <strong>${this.#ui.escH(h.book_title)}</strong><br>
                <small>Borrowed: ${h.borrow_date} &nbsp;·&nbsp; Due: ${h.due_date} &nbsp;·&nbsp; <span class="badge ${this.#ui.badgeClass(h.status)}">${h.status}</span>${h.fine > 0 ? ` &nbsp; Fine: ₱${h.fine}` : ''}</small>
            </div>`).join('') : '<p class="text-muted">No borrowing history.</p>'}
            <div class="d-flex gap-1 mt-2">
                <button class="btn btn-sm" onclick="app.ui.closeModal('detailModal');app.members.edit(${m.id})"><i class="fas fa-edit"></i> Edit</button>
            </div>`;
        this.#ui.openModal('detailModal');
    }

    // ── Search rgr_students ──────────────────────────────────
    searchStudents(value) {
        const query   = (value ?? '').trim();
        const results = document.getElementById('studentSearchResults');
        const empty   = document.getElementById('studentSearchEmpty');
        const spinner = document.getElementById('studentSearchSpinner');

        if (query.length < 2) {
            results.style.display = 'none';
            empty.style.display   = 'none';
            spinner.style.display = 'none';
            return;
        }

        clearTimeout(this.#studentDebounce);
        this.#studentDebounce = setTimeout(async () => {
            spinner.style.display = 'block';
            results.style.display = 'none';
            empty.style.display   = 'none';

            try {
                const data     = await this.#api.get({ action: 'search_students', search: query, limit: 15 });
                const students = data.students ?? [];

                spinner.style.display = 'none';

                if (!students.length) {
                    empty.style.display = 'block';
                    return;
                }

                document.getElementById('studentSearchBody').innerHTML = students.map(s => `
                    <tr>
                        <td><strong>${this.#ui.escH(String(s.student_number))}</strong></td>
                        <td>${this.#ui.escH(s.full_name)}</td>
                        <td><span class="genre-pill">${this.#ui.escH(s.course ?? '—')}</span></td>
                        <td>${s.year_level ?? '—'}</td>
                        <td class="small text-muted">${this.#ui.escH(s.email ?? '—')}</td>
                        <td>
                            <span class="badge ${s.academic_status === 'active' ? 'badge-success' : 'badge-warning'}">
                                ${s.academic_status}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-success"
                                onclick="app.members.importStudent(${s.student_number}, '${this.#ui.escH(s.full_name).replace(/'/g,"\\'")}')">
                                <i class="fas fa-plus"></i> Import
                            </button>
                        </td>
                    </tr>
                `).join('');

                results.style.display = '';

            } catch(e) {
                spinner.style.display = 'none';
                empty.style.display   = 'block';
            }
        }, 350);
    }

    // ── Import student from rgr_students ────────────────────
    async importStudent(studentNumber, name) {
        if (!confirm(`Import "${name}" (Student #${studentNumber}) as a library member?`)) return;

        const res = await this.#api.post('import_student', { student_number: studentNumber });
        if (res.error) { this.#ui.showNotif(res.error, 'error'); return; }

        this.#ui.showNotif(res.message, 'success');

        // Clear search
        const searchEl = document.getElementById('studentSearch');
        if (searchEl) searchEl.value = '';
        document.getElementById('studentSearchResults').style.display = 'none';
        document.getElementById('studentSearchEmpty').style.display   = 'none';

        this.load();
        app.dashboard.load();
    }
}

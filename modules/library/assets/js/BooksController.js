'use strict';
// ============================================================
// BooksController
// ============================================================
class BooksController {
    #api; #ui;
    #pager      = new Paginator(10);
    #searchTerm = '';
    #debounceId = null;

    constructor(api, ui) { this.#api = api; this.#ui = ui; }

    async load() {
        const search = this.#searchTerm;
        const status = document.getElementById('bookStatusFilter')?.value || '';
        const data   = await this.#api.get({ action: 'get_books', search, status, page: this.#pager.page, limit: this.#pager.pageSize });
        this.#pager.total = data.total || 0;

        const tbody = document.getElementById('booksTableBody');
        if (!data.books?.length) {
            tbody.innerHTML = `<tr><td colspan="5" class="empty-state">
                <i class="fas fa-book" style="display:block;font-size:2rem;color:var(--gray-light);margin-bottom:.5rem"></i>
                <p>${search ? `No books matching "<strong>${this.#ui.escH(search)}</strong>"` : 'No books found'}</p>
            </td></tr>`;
        } else {
            tbody.innerHTML = data.books.map(b => `
                <tr>
                    <td>${this.#ui.bookCoverHTML(b, 32)}</td>
                    <td><strong>${this.#ui.escH(b.title)}</strong><br><small class="text-muted">${this.#ui.escH(b.author)}${b.isbn ? ` &nbsp;·&nbsp; ISBN: ${this.#ui.escH(b.isbn)}` : ''}</small></td>
                    <td><span class="genre-pill">${this.#ui.escH(b.genre)}</span></td>
                    <td><span class="badge ${this.#ui.badgeClass(b.status)}">${b.status}</span></td>
                    <td><div class="d-flex gap-1">
                        <button class="btn btn-sm btn-outline" onclick="app.books.view(${b.id})" title="View"><i class="fas fa-eye"></i></button>
                        <button class="btn btn-sm btn-outline" onclick="app.books.edit(${b.id})" title="Edit"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-outline btn-danger" onclick="app.books.delete(${b.id})" title="Delete"><i class="fas fa-trash"></i></button>
                    </div></td>
                </tr>`).join('');
        }
        document.getElementById('booksPaginationInfo').textContent = this.#pager.infoText('books');
    }

    search(value) {
        this.#searchTerm = (value ?? '').trim();
        this.#pager.reset();
        clearTimeout(this.#debounceId);
        this.#debounceId = setTimeout(() => this.load(), 300);
    }

    filter()          { this.#pager.reset(); this.load(); }
    changePage(dir)   { this.#pager.move(dir); this.load(); }

    async submit() {
        const id        = document.getElementById('editBookId').value;
        const title     = document.getElementById('bookTitle').value.trim();
        const author    = document.getElementById('bookAuthor').value.trim();
        const genre     = document.getElementById('bookGenre').value;
        const year      = document.getElementById('bookYear').value;
        const isbn      = document.getElementById('bookISBN').value.trim();
        const desc      = document.getElementById('bookDescription').value.trim();
        const cover_url = document.getElementById('bookCoverUrl').value.trim();

        if (!title || !author) { this.#ui.showNotif('Title and Author are required.', 'error'); return; }

        const action  = id ? 'update_book' : 'add_book';
        const payload = { title, author, genre, year, isbn, description: desc, cover_url };
        if (id) payload.id = parseInt(id);

        const btn = document.getElementById('bookSubmitBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

        const res = await this.#api.post(action, payload);
        btn.disabled = false;
        btn.innerHTML = id ? '<i class="fas fa-save"></i> Update Book' : '<i class="fas fa-plus-circle"></i> Add Book';

        if (res.error) { this.#ui.showNotif(res.error, 'error'); return; }
        this.#ui.showNotif(res.message, 'success');
        this.cancelEdit();
        this.load();
        app.dashboard.load();
    }

    async edit(id) {
        const book = await this.#api.get({ action: 'get_book', id });
        if (book.error) { this.#ui.showNotif(book.error, 'error'); return; }

        document.getElementById('editBookId').value      = book.id;
        document.getElementById('bookTitle').value       = book.title;
        document.getElementById('bookAuthor').value      = book.author;
        document.getElementById('bookGenre').value       = book.genre;
        document.getElementById('bookYear').value        = book.year || '';
        document.getElementById('bookISBN').value        = book.isbn || '';
        document.getElementById('bookDescription').value = book.description || '';
        document.getElementById('bookCoverUrl').value    = book.cover_url || '';

        const src = this.#ui.coverSrc(book);
        if (src) {
            document.getElementById('coverPreviewImg').src           = src;
            document.getElementById('coverPreviewImg').style.display = 'block';
            document.getElementById('uploadIcon').style.display      = 'none';
        }
        document.getElementById('bookFormTitle').textContent   = 'Edit Book';
        document.getElementById('bookSubmitBtn').innerHTML     = '<i class="fas fa-save"></i> Update Book';
        document.getElementById('bookCancelBtn').style.display = 'inline-flex';
        document.getElementById('bookTitle').scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    cancelEdit() {
        document.getElementById('editBookId').value = '';
        ['bookTitle','bookAuthor','bookYear','bookISBN','bookDescription','bookCoverUrl'].forEach(id => {
            document.getElementById(id).value = '';
        });
        document.getElementById('bookYear').value  = new Date().getFullYear();
        document.getElementById('bookGenre').value = 'Fiction';
        document.getElementById('bookFormTitle').textContent   = 'Add New Book';
        document.getElementById('bookSubmitBtn').innerHTML     = '<i class="fas fa-plus-circle"></i> Add Book';
        document.getElementById('bookCancelBtn').style.display = 'none';
        document.getElementById('coverPreviewImg').style.display = 'none';
        document.getElementById('uploadIcon').style.display    = 'block';
    }

    async delete(id) {
        if (!confirm('Delete this book? This cannot be undone.')) return;
        const res = await this.#api.post('delete_book', { id });
        if (res.error) { this.#ui.showNotif(res.error, 'error'); return; }
        this.#ui.showNotif(res.message, 'success');
        this.load();
        app.dashboard.load();
    }

    async view(id) {
        const book = await this.#api.get({ action: 'get_book', id });
        if (book.error) { this.#ui.showNotif(book.error, 'error'); return; }
        const src = this.#ui.coverSrc(book);
        document.getElementById('modalTitle').textContent = book.title;
        document.getElementById('modalContent').innerHTML = `
            <div class="d-flex gap-1 mb-2 align-center">
                ${src
                    ? `<img src="${src}" alt="" style="width:80px;height:112px;object-fit:cover;border-radius:8px;flex-shrink:0">`
                    : `<div style="width:80px;height:112px;background:var(--gradient-primary);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:2rem;flex-shrink:0"><i class="fas fa-book"></i></div>`}
                <div>
                    <h3 style="margin-bottom:.25rem">${this.#ui.escH(book.title)}</h3>
                    <p style="margin-bottom:.25rem;color:var(--gray)">by ${this.#ui.escH(book.author)}</p>
                    <span class="badge ${this.#ui.badgeClass(book.status)}">${book.status}</span>
                </div>
            </div>
            <div class="grid-2">
                <div>
                    <p><strong>Genre:</strong> ${this.#ui.escH(book.genre)}</p>
                    <p><strong>Year:</strong> ${book.year || 'N/A'}</p>
                    <p><strong>ISBN:</strong> ${book.isbn || 'N/A'}</p>
                    <p><strong>Condition:</strong> ${book.condition || 'N/A'}</p>
                    <p><strong>Added:</strong> ${book.added_date}</p>
                </div>
                <div>
                    <h4 class="mb-1">Description</h4>
                    <p>${this.#ui.escH(book.description || 'No description available.')}</p>
                </div>
            </div>
            ${book.history?.length ? `<h4 class="mt-1 mb-1">Borrowing History</h4>${book.history.map(h => `
            <div style="border-left:3px solid var(--primary);padding-left:1rem;margin-bottom:.75rem">
                <strong>${this.#ui.escH(h.borrower_name)}</strong> <span class="text-muted small">(${h.member_id})</span><br>
                <small>Borrowed: ${h.borrow_date} &nbsp;·&nbsp; Due: ${h.due_date} &nbsp;·&nbsp; <span class="badge ${this.#ui.badgeClass(h.status)}">${h.status}</span></small>
            </div>`).join('')}` : '<p class="text-muted mt-1">No borrowing history.</p>'}
            <div class="d-flex gap-1 mt-2">
                <button class="btn btn-sm" onclick="app.ui.closeModal('detailModal');app.books.edit(${book.id})"><i class="fas fa-edit"></i> Edit</button>
            </div>`;
        this.#ui.openModal('detailModal');
    }

    previewISBNCover() {
        const isbn = document.getElementById('bookISBN').value.trim().replace(/-/g, '');
        if (isbn.length >= 10) {
            const url = `https://covers.openlibrary.org/b/isbn/${isbn}-L.jpg`;
            document.getElementById('bookCoverUrl').value          = url;
            document.getElementById('coverPreviewImg').src         = url;
            document.getElementById('coverPreviewImg').style.display = 'block';
            document.getElementById('uploadIcon').style.display    = 'none';
        }
    }

    handleDragOver(e) { e.preventDefault(); document.getElementById('coverUploadArea').classList.add('dragover'); }
    handleDrop(e)     { e.preventDefault(); document.getElementById('coverUploadArea').classList.remove('dragover'); if (e.dataTransfer.files[0]) this.uploadCoverFile(e.dataTransfer.files[0]); }
    handleCoverUpload(e) { const file = e.target.files[0]; if (file) this.uploadCoverFile(file); }

    async uploadCoverFile(file) {
        const bookId = document.getElementById('editBookId').value;
        const fd = new FormData();
        fd.append('cover', file);
        fd.append('action', 'upload_cover');
        if (bookId) fd.append('book_id', bookId);

        const data = await this.#api.upload(fd);
        if (data.error) { this.#ui.showNotif(data.error, 'error'); return; }

        document.getElementById('bookCoverUrl').value              = '';
        document.getElementById('coverPreviewImg').src             = data.url;
        document.getElementById('coverPreviewImg').style.display   = 'block';
        document.getElementById('uploadIcon').style.display        = 'none';
        this.#ui.showNotif('Cover uploaded!', 'success');
        if (bookId) { this.load(); app.gallery.load(); }
    }
}

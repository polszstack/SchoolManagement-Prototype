'use strict';
// ============================================================
// GalleryController
// ============================================================
class GalleryController {
    #api; #ui;
    #pager      = new Paginator(12);
    #searchTerm = '';
    #debounceId = null;
    static #COLORS = ['#2563eb','#7c3aed','#059669','#d97706','#dc2626','#0891b2','#9333ea','#0f766e'];

    constructor(api, ui) { this.#api = api; this.#ui = ui; }

    search(value) {
        this.#searchTerm = (value ?? '').trim();
        this.#pager.reset();
        clearTimeout(this.#debounceId);
        this.#debounceId = setTimeout(() => this.load(), 300);
    }

    async load() {
        const search = this.#searchTerm;
        const genre  = document.getElementById('galleryGenreFilter')?.value  || '';
        const status = document.getElementById('galleryStatusFilter')?.value || '';
        const data   = await this.#api.get({ action: 'get_books', search, genre, status, page: this.#pager.page, limit: this.#pager.pageSize });

        this.#pager.total = data.total || 0;
        const grid = document.getElementById('galleryGrid');

        if (!data.books?.length) {
            grid.innerHTML = '<div class="empty-state" style="grid-column:1/-1"><i class="fas fa-th-large"></i><p>No books found</p></div>';
        } else {
            grid.innerHTML = data.books.map(b => this.#renderCard(b)).join('');
        }
        document.getElementById('galleryPaginationInfo').textContent = this.#pager.infoText('books');
    }

    #renderCard(b) {
        const src   = this.#ui.coverSrc(b);
        const color = GalleryController.#COLORS[b.id % GalleryController.#COLORS.length];
        return `
        <div class="book-card">
            <div class="book-cover-wrap">
                ${src ? `<img src="${this.#ui.escH(src)}" alt="${this.#ui.escH(b.title)}" onerror="this.style.display='none';this.nextSibling.style.display='flex'">` : ''}
                <div class="book-cover-placeholder" style="background:linear-gradient(135deg,${color},${color}cc);${src ? 'display:none' : ''}">
                    <i class="fas fa-book"></i>
                </div>
            </div>
            <div class="book-card-actions">
                <button class="btn-view" onclick="app.books.view(${b.id})" title="View"><i class="fas fa-eye"></i></button>
                <button class="btn-edit" onclick="app.ui.switchTab('books');app.books.edit(${b.id})" title="Edit"><i class="fas fa-edit"></i></button>
                <button class="btn-del"  onclick="app.books.delete(${b.id})" title="Delete"><i class="fas fa-trash"></i></button>
            </div>
            <div class="book-info">
                <div class="book-title-card">${this.#ui.escH(b.title)}</div>
                <div class="book-author-card">${this.#ui.escH(b.author)}</div>
                <span class="badge ${this.#ui.badgeClass(b.status)}" style="font-size:.7rem">${b.status}</span>
            </div>
        </div>`;
    }

    changePage(dir) { this.#pager.move(dir); this.load(); }

    async loadGenreOptions() {
        const data = await this.#api.get({ action: 'get_genres' });
        const sel  = document.getElementById('galleryGenreFilter');
        const cur  = sel.value;
        sel.innerHTML = '<option value="">All Genres</option>'
            + (data.genres || []).map(g => `<option value="${this.#ui.escH(g)}" ${g === cur ? 'selected' : ''}>${this.#ui.escH(g)}</option>`).join('');
    }
}

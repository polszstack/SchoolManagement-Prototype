'use strict';
// ============================================================
// Paginator — shared pagination state helper
// ============================================================
class Paginator {
    page     = 1;
    total    = 0;
    pageSize = 10;

    constructor(pageSize = 10) { this.pageSize = pageSize; }

    reset() { this.page = 1; }

    move(dir) {
        const maxPage = Math.max(1, Math.ceil(this.total / this.pageSize));
        this.page = Math.min(maxPage, Math.max(1, this.page + dir));
    }

    infoText(noun = 'items') {
        if (!this.total) return `No ${noun} found`;
        const from = (this.page - 1) * this.pageSize + 1;
        const to   = Math.min(this.page * this.pageSize, this.total);
        return `Showing ${from}–${to} of ${this.total} ${noun}`;
    }
}

// ============================================================
// UIService — notifications, modals, sidebar, tab switching
// ============================================================
class UIService {
    #notifTimer = null;

    showNotif(msg, type = 'info') {
        const n      = document.getElementById('notification');
        const icons  = { success: 'fa-check-circle', error: 'fa-exclamation-circle', info: 'fa-info-circle', warning: 'fa-exclamation-triangle' };
        document.getElementById('notifIcon').className = 'fas ' + (icons[type] ?? icons.info);
        document.getElementById('notifText').textContent = msg;
        n.className = `notification show ${type}`;
        clearTimeout(this.#notifTimer);
        this.#notifTimer = setTimeout(() => n.classList.remove('show'), 5000);
    }

    hideNotification() { document.getElementById('notification').classList.remove('show'); }

    openModal(id)  { document.getElementById(id).classList.add('active');    document.body.style.overflow = 'hidden'; }
    closeModal(id) { document.getElementById(id).classList.remove('active'); document.body.style.overflow = ''; }

    toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('active');
        document.getElementById('sidebarOverlay').classList.toggle('active');
    }
    closeSidebar() {
        document.getElementById('sidebar').classList.remove('active');
        document.getElementById('sidebarOverlay').classList.remove('active');
    }

    switchTab(id) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.sidebar-nav .tab').forEach(el => el.classList.remove('active'));
        document.getElementById('tab-' + id)?.classList.add('active');
        document.querySelectorAll('.sidebar-nav .tab').forEach(el => {
            if (el.getAttribute('onclick')?.includes(`'${id}'`)) el.classList.add('active');
        });
        this.closeSidebar();

        const map = {
            dashboard:    () => app.dashboard.load(),
            books:        () => app.books.load(),
            gallery:      () => { app.gallery.load(); app.gallery.loadGenreOptions(); },
            borrowers:    () => app.members.load(),
            borrow:       () => { app.borrow.loadDropdowns(); app.borrow.loadActive(); },
            return:       () => app.borrow.loadReturnList(),
            transactions: () => app.transactions.load(),
            reports:      () => app.reports.load(),
            settings:     () => app.settings.load(),
        };
        map[id]?.();
    }

    /** Escape HTML to prevent XSS */
    escH(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    coverSrc(book) {
        if (book.cover_local) return 'uploads/covers/' + book.cover_local;
        if (book.cover_url)   return book.cover_url;
        return null;
    }

    bookCoverHTML(book, size = 40) {
        const src    = this.coverSrc(book);
        const colors = ['#2563eb','#7c3aed','#059669','#d97706','#dc2626','#0891b2'];
        const color  = colors[(book.id || 0) % colors.length];
        const fallback = `<span style="display:inline-flex;width:${size}px;height:${size * 1.4}px;background:${color};border-radius:4px;align-items:center;justify-content:center;color:#fff;font-size:${size * 0.4}px"><i class='fas fa-book'></i></span>`;
        if (!src) return fallback;
        return `<img src="${src}" alt="" style="width:${size}px;height:${size * 1.4}px;object-fit:cover;border-radius:4px;vertical-align:middle" onerror="this.outerHTML='${fallback.replace(/'/g, "\\'")}'">`; 
    }

    badgeClass(status) {
        return { available: 'badge-success', borrowed: 'badge-warning', overdue: 'badge-danger', returned: 'badge-info', active: 'badge-warning' }[status] ?? 'badge-info';
    }
}

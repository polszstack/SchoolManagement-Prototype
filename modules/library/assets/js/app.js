'use strict';
// ============================================================
// LibraryApp — root application, wires all controllers together
// ============================================================
class LibraryApp {
    api;
    ui;
    dashboard;
    books;
    gallery;
    members;
    borrow;
    transactions;
    reports;
    settings;

    constructor() {
        this.api          = new ApiService();
        this.ui           = new UIService();
        this.dashboard    = new DashboardController(this.api, this.ui);
        this.books        = new BooksController(this.api, this.ui);
        this.gallery      = new GalleryController(this.api, this.ui);
        this.members      = new MembersController(this.api, this.ui);
        this.borrow       = new BorrowController(this.api, this.ui);
        this.transactions = new TransactionsController(this.api, this.ui);
        this.reports      = new ReportsController(this.api, this.ui);
        this.settings     = new SettingsController(this.api, this.ui);

        this.#bindModals();
    }

    init() {
        this.dashboard.load();
        setTimeout(() => this.gallery.loadGenreOptions(), 500);
    }

    #bindModals() {
        document.getElementById('detailModal').addEventListener('click', e => {
            if (e.target === e.currentTarget) this.ui.closeModal('detailModal');
        });
        document.getElementById('returnModal').addEventListener('click', e => {
            if (e.target === e.currentTarget) this.ui.closeModal('returnModal');
        });
    }
}

// ============================================================
// Bootstrap
// ============================================================
const app = new LibraryApp();
document.addEventListener('DOMContentLoaded', () => app.init());

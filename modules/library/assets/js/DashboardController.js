'use strict';
// ============================================================
// DashboardController — Enhanced with borrow tracking & inventory
// ============================================================
class DashboardController {
    #api; #ui;

    constructor(api, ui) { this.#api = api; this.#ui = ui; }

    async load() {
        const data = await this.#api.get({ action: 'get_stats' });

        // ── Stat cards ──────────────────────────────────────
        document.getElementById('statTotalBooks').textContent    = data.total_books    ?? '—';
        document.getElementById('statTotalMembers').textContent  = data.total_members  ?? '—';
        document.getElementById('statBorrowedBooks').textContent = data.borrowed_books ?? '—';
        document.getElementById('statOverdueBooks').textContent  = data.overdue_books  ?? '—';
        document.getElementById('statFineText').innerHTML =
            `<i class="fas fa-peso-sign"></i> ₱${data.total_fines ?? '0.00'} fines pending`;

        // ── Alert badges ────────────────────────────────────
        const dueTodayEl = document.getElementById('statDueToday');
        const dueSoonEl  = document.getElementById('statDueSoon');
        if (dueTodayEl) dueTodayEl.textContent = data.due_today ?? 0;
        if (dueSoonEl)  dueSoonEl.textContent  = data.due_soon  ?? 0;

        // ── Availability bar ────────────────────────────────
        this.#renderAvailabilityBar(data);

        // ── Active borrowings tracking panel ────────────────
        this.#renderBorrowTracker(data.active_borrowings ?? []);

        // ── Inventory health ────────────────────────────────
        this.#renderInventoryHealth(data.inventory_health ?? []);

        // ── Genre inventory ─────────────────────────────────
        this.#renderGenreInventory(data.genre_inventory ?? [], data.total_books ?? 0);

        // ── Recent books & activity ─────────────────────────
        this.#renderRecentBooks(data.recent_books  ?? []);
        this.#renderActivity(data.recent_activity ?? []);
    }

    // ── Availability bar ────────────────────────────────────
    #renderAvailabilityBar(data) {
        const el = document.getElementById('availabilityBar');
        if (!el) return;
        const total     = data.total_books    || 1;
        const available = data.available_books || 0;
        const borrowed  = data.borrowed_books  || 0;
        const overdue   = data.overdue_books   || 0;
        const availPct  = Math.round(available / total * 100);
        const borrowPct = Math.round(borrowed  / total * 100);
        const overduePct= Math.round(overdue   / total * 100);

        el.innerHTML = `
            <div style="display:flex;height:14px;border-radius:8px;overflow:hidden;gap:2px;margin:.5rem 0">
                <div style="width:${availPct}%;background:var(--success);transition:width .6s ease" title="Available: ${available}"></div>
                <div style="width:${borrowPct}%;background:var(--warning);transition:width .6s ease" title="Borrowed: ${borrowed}"></div>
                <div style="width:${overduePct}%;background:var(--danger);transition:width .6s ease" title="Overdue: ${overdue}"></div>
            </div>
            <div style="display:flex;gap:1.25rem;flex-wrap:wrap;font-size:.78rem">
                <span><span style="display:inline-block;width:10px;height:10px;background:var(--success);border-radius:2px;margin-right:4px"></span>Available: <strong>${available}</strong> (${availPct}%)</span>
                <span><span style="display:inline-block;width:10px;height:10px;background:var(--warning);border-radius:2px;margin-right:4px"></span>Borrowed: <strong>${borrowed}</strong> (${borrowPct}%)</span>
                <span><span style="display:inline-block;width:10px;height:10px;background:var(--danger);border-radius:2px;margin-right:4px"></span>Overdue: <strong>${overdue}</strong> (${overduePct}%)</span>
            </div>`;
    }

    // ── Borrow tracker ──────────────────────────────────────
    #renderBorrowTracker(borrowings) {
        const el = document.getElementById('borrowTracker');
        if (!el) return;

        if (!borrowings.length) {
            el.innerHTML = `<div class="empty-state"><i class="fas fa-check-circle" style="color:var(--success)"></i><p>No active borrowings — all clear!</p></div>`;
            return;
        }

        el.innerHTML = borrowings.map(t => {
            const days    = parseInt(t.days_until_due);
            const isOver  = t.status === 'overdue' || days < 0;
            const isToday = days === 0;
            const isSoon  = days > 0 && days <= 3;

            let dueLabel, dueColor, rowBg;
            if (isOver) {
                const daysLate = Math.abs(days);
                dueLabel = `<span style="color:var(--danger);font-weight:700">${daysLate}d overdue</span>`;
                dueColor = 'var(--danger)';
                rowBg    = 'rgba(239,68,68,.04)';
            } else if (isToday) {
                dueLabel = `<span style="color:var(--warning);font-weight:700">Due today!</span>`;
                dueColor = 'var(--warning)';
                rowBg    = 'rgba(245,158,11,.04)';
            } else if (isSoon) {
                dueLabel = `<span style="color:var(--warning)">In ${days}d</span>`;
                dueColor = 'var(--warning)';
                rowBg    = 'rgba(245,158,11,.03)';
            } else {
                dueLabel = `<span style="color:var(--gray)">In ${days}d</span>`;
                dueColor = 'var(--gray)';
                rowBg    = 'transparent';
            }

            const urgencyDot = isOver  ? `<span style="width:8px;height:8px;border-radius:50%;background:var(--danger);display:inline-block;margin-right:6px;flex-shrink:0"></span>`
                             : isToday ? `<span style="width:8px;height:8px;border-radius:50%;background:var(--warning);display:inline-block;margin-right:6px;flex-shrink:0"></span>`
                             : isSoon  ? `<span style="width:8px;height:8px;border-radius:50%;background:var(--info);display:inline-block;margin-right:6px;flex-shrink:0"></span>`
                             : `<span style="width:8px;height:8px;border-radius:50%;background:var(--success);display:inline-block;margin-right:6px;flex-shrink:0"></span>`;

            const typeColors = { Student:'#2563eb', Teacher:'#059669', Staff:'#d97706' };
            const tc = typeColors[t.borrower_type] || '#64748b';

            return `
            <div style="display:flex;align-items:center;gap:.875rem;padding:.75rem .5rem;border-bottom:1px solid var(--border-color);background:${rowBg};transition:background .2s">
                ${this.#ui.bookCoverHTML(t, 34)}
                <div style="flex:1;min-width:0">
                    <div class="fw-600 small" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${this.#ui.escH(t.book_title)}</div>
                    <div class="text-muted small" style="display:flex;align-items:center;gap:.3rem;flex-wrap:wrap">
                        ${urgencyDot}
                        <span style="color:${tc};font-weight:600">${this.#ui.escH(t.borrower_name)}</span>
                        <span style="color:var(--gray-light)">·</span>
                        <span class="small" style="color:var(--gray)">${this.#ui.escH(t.borrower_type)}</span>
                    </div>
                </div>
                <div style="text-align:right;flex-shrink:0">
                    <div class="small text-muted">${t.due_date}</div>
                    <div class="small">${dueLabel}</div>
                </div>
            </div>`;
        }).join('');
    }

    // ── Inventory health by condition ───────────────────────
    #renderInventoryHealth(health) {
        const el = document.getElementById('inventoryHealth');
        if (!el) return;
        const colors = { Excellent:'var(--success)', Good:'var(--info)', Fair:'var(--warning)', Poor:'var(--danger)' };
        const total  = health.reduce((s, r) => s + parseInt(r.count), 0) || 1;

        el.innerHTML = health.map(r => {
            const pct   = Math.round(r.count / total * 100);
            const color = colors[r.condition] || 'var(--gray)';
            return `
            <div style="margin-bottom:.6rem">
                <div style="display:flex;justify-content:space-between;margin-bottom:.25rem">
                    <span class="small fw-600" style="color:${color}">${this.#ui.escH(r.condition)}</span>
                    <span class="small text-muted">${r.count} books (${pct}%)</span>
                </div>
                <div style="height:7px;background:var(--border-color);border-radius:4px;overflow:hidden">
                    <div style="width:${pct}%;height:100%;background:${color};border-radius:4px;transition:width .6s ease"></div>
                </div>
            </div>`;
        }).join('') || '<p class="text-muted small">No condition data yet.</p>';
    }

    // ── Genre inventory breakdown ───────────────────────────
    #renderGenreInventory(genres, totalBooks) {
        const el = document.getElementById('genreInventory');
        if (!el) return;
        if (!genres.length) { el.innerHTML = '<p class="text-muted small">No books yet.</p>'; return; }

        el.innerHTML = genres.map(g => {
            const pct  = Math.round(parseInt(g.total) / (totalBooks || 1) * 100);
            const aPct = Math.round(parseInt(g.available) / parseInt(g.total) * 100);
            return `
            <div style="display:flex;align-items:center;gap:.75rem;padding:.45rem 0;border-bottom:1px solid var(--border-color)">
                <span class="genre-pill" style="flex-shrink:0;min-width:90px;text-align:center">${this.#ui.escH(g.genre)}</span>
                <div style="flex:1">
                    <div style="height:7px;background:var(--border-color);border-radius:4px;overflow:hidden">
                        <div style="width:${pct}%;height:100%;background:var(--gradient-primary);border-radius:4px;transition:width .6s ease"></div>
                    </div>
                </div>
                <div style="text-align:right;flex-shrink:0;min-width:80px">
                    <span class="small fw-600">${g.total}</span>
                    <span class="small text-muted"> · </span>
                    <span class="small" style="color:var(--success)">${g.available} avail</span>
                </div>
            </div>`;
        }).join('');
    }

    // ── Recent books ────────────────────────────────────────
    #renderRecentBooks(books) {
        const el = document.getElementById('dashRecentBooks');
        if (!books.length) {
            el.innerHTML = '<div class="empty-state"><i class="fas fa-book"></i><p>No books yet</p></div>';
            return;
        }
        el.innerHTML = books.map(b => `
            <div class="d-flex gap-1 align-center mb-1" style="padding:.5rem 0;border-bottom:1px solid var(--border-color)">
                ${this.#ui.bookCoverHTML(b, 32)}
                <div style="flex:1;min-width:0">
                    <div class="fw-600 small" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${this.#ui.escH(b.title)}</div>
                    <div class="text-muted small">${this.#ui.escH(b.author)}</div>
                </div>
                <span class="badge ${this.#ui.badgeClass(b.status)}" style="font-size:.7rem">${b.status}</span>
            </div>`).join('');
    }

    // ── Activity ────────────────────────────────────────────
    #renderActivity(activity) {
        const el = document.getElementById('dashActivity');
        if (!activity.length) {
            el.innerHTML = '<div class="empty-state"><i class="fas fa-history"></i><p>No activity yet</p></div>';
            return;
        }
        el.innerHTML = activity.slice(0, 8).map(a => {
            const isRegistrar = a.source === 'registrar';
            const iconBg  = isRegistrar ? 'rgba(59,130,246,.1)'  : 'rgba(32,0,130,.1)';
            const iconCol = isRegistrar ? 'var(--info)'           : 'var(--primary)';
            const srcBadge = isRegistrar
                ? `<span style="font-size:.65rem;background:rgba(59,130,246,.12);color:var(--info);padding:.15rem .45rem;border-radius:4px;margin-left:.35rem">Registrar</span>`
                : `<span style="font-size:.65rem;background:rgba(32,0,130,.1);color:var(--primary);padding:.15rem .45rem;border-radius:4px;margin-left:.35rem">Library</span>`;
            return `
            <div class="activity-item">
                <div class="activity-icon" style="background:${iconBg};color:${iconCol}">
                    <i class="fas ${isRegistrar ? 'fa-graduation-cap' : 'fa-circle-dot'}"></i>
                </div>
                <div class="activity-text">
                    <div class="fw-600 small">${this.#ui.escH(a.action)}${srcBadge}</div>
                    <div class="text-muted small">${this.#ui.escH(a.details ?? '')}</div>
                    <div class="activity-time">${a.created_at}</div>
                </div>
            </div>`;
        }).join('');
    }
}

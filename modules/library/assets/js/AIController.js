// ============================================================
// assets/js/AIController.js
// ============================================================

const API_URL = 'api.php';

const AI = (() => {

    async function refreshRiskReport() {
        const spinner = document.getElementById('riskSpinner');
        const table   = document.getElementById('riskTable');
        const empty   = document.getElementById('riskEmpty');
        const tbody   = document.getElementById('riskTableBody');

        spinner.style.display = 'block';
        table.style.display   = 'none';
        empty.style.display   = 'none';

        try {
            const res  = await fetch(API_URL + '?action=ai_risk_report');
            const json = await res.json();
            const data = json.data ?? [];

            spinner.style.display = 'none';

            if (!data.length) {
                empty.style.display = 'block';
                return;
            }

            tbody.innerHTML = data.map(txn => {
                const risk   = parseFloat(txn.risk_score);
                const color  = risk >= 75 ? 'var(--danger)'
                             : risk >= 50 ? 'var(--warning)'
                             : 'var(--success)';
                const badge  = risk >= 75 ? 'badge-danger'
                             : risk >= 50 ? 'badge-warning'
                             : 'badge-success';
                const label  = risk >= 75 ? 'High' : risk >= 50 ? 'Medium' : 'Low';
                const daysLeft = parseInt(txn.days_until_due);
                const dueLabel = daysLeft < 0
                    ? `<span class="text-danger fw-600">${Math.abs(daysLeft)}d overdue</span>`
                    : daysLeft === 0 ? '<span class="text-warning fw-600">Due today</span>'
                    : `In ${daysLeft}d`;

                return `
                    <tr class="${risk >= 75 ? 'row-overdue' : ''}">
                        <td>
                            <div class="fw-600">${escHtml(txn.borrower_name)}</div>
                            <div class="small text-muted">${escHtml(txn.borrower_type)}</div>
                        </td>
                        <td>
                            <div class="fw-600">${escHtml(txn.book_title)}</div>
                            <div class="small text-muted">${escHtml(txn.genre ?? '')}</div>
                        </td>
                        <td>${escHtml(txn.due_date)}<br><span class="small">${dueLabel}</span></td>
                        <td><span class="badge ${txn.status === 'overdue' ? 'badge-danger' : 'badge-info'}">${txn.status}</span></td>
                        <td>
                            <div style="display:flex;align-items:center;gap:.5rem">
                                <div style="flex:1;background:var(--border-color);border-radius:4px;height:8px;overflow:hidden">
                                    <div style="width:${risk}%;height:100%;background:${color};border-radius:4px;transition:width .5s"></div>
                                </div>
                                <span class="badge ${badge}" style="min-width:60px;justify-content:center">${risk}%</span>
                            </div>
                        </td>
                        <td><span class="small text-muted">${label}</span></td>
                    </tr>
                `;
            }).join('');

            table.style.display = '';

        } catch (err) {
            spinner.style.display = 'none';
            console.error('Risk report error:', err);
        }
    }

    async function runReminderCheck() {
        if (!confirm('Run AI reminder check now? This will send reminders to at-risk borrowers.')) return;

        try {
            const res   = await fetch(API_URL + '?action=ai_run_reminders', { method: 'POST' });
            const json  = await res.json();
            const count = (json.data ?? []).length;
            alert('AI check complete - ' + count + ' reminder(s) sent.');
            refreshRiskReport();
            loadReminderLog();
        } catch (err) {
            alert('Reminder check failed.');
            console.error('Reminder error:', err);
        }
    }

    async function loadReminderLog() {
        const wrap    = document.getElementById('reminderLogList');
        const spinner = document.getElementById('reminderSpinner');

        spinner.style.display = 'block';
        wrap.innerHTML = '';

        try {
            const res  = await fetch(API_URL + '?action=ai_reminder_log');
            const json = await res.json();
            const data = json.data ?? [];

            spinner.style.display = 'none';

            if (!data.length) {
                wrap.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-bell-slash"></i>
                        <p>No reminders have been sent yet.</p>
                    </div>`;
                return;
            }

            wrap.innerHTML = data.map(r => {
                const stageColors = {
                    '3_day'  : 'badge-info',
                    '1_day'  : 'badge-warning',
                    'overdue': 'badge-danger',
                    'final'  : 'badge-danger',
                };
                const typeIcon = r.reminder_type === 'sms'
                    ? '<i class="fas fa-sms"></i>'
                    : r.reminder_type === 'both'
                    ? '<i class="fas fa-envelope"></i><i class="fas fa-sms"></i>'
                    : '<i class="fas fa-envelope"></i>';

                return `
                    <div class="activity-item">
                        <div class="activity-icon" style="background:rgba(239,68,68,.1);color:var(--danger)">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div class="activity-text">
                            <div class="fw-600">${escHtml(r.borrower_name)} - <em>${escHtml(r.book_title)}</em></div>
                            <div class="small text-muted">
                                ${typeIcon}
                                <span class="badge ${stageColors[r.reminder_stage] ?? 'badge-info'}" style="font-size:.65rem">${r.reminder_stage.replace('_', ' ')}</span>
                                Risk: <strong>${r.risk_score}%</strong>
                                &bull; ${escHtml(r.status)}
                            </div>
                            ${r.message_preview ? `<div class="small text-muted mt-1" style="font-style:italic">"${escHtml(r.message_preview.substring(0, 100))}"</div>` : ''}
                        </div>
                        <div class="activity-time">${formatTime(r.sent_at)}</div>
                    </div>
                `;
            }).join('');

        } catch (err) {
            spinner.style.display = 'none';
            wrap.innerHTML = `<p class="text-danger">Error loading reminders.</p>`;
            console.error('Reminder log error:', err);
        }
    }

    async function populateMemberDropdown() {
        const select = document.getElementById('aiMemberSelect');
        if (!select) return;

        try {
            const res     = await fetch(API_URL + '?action=get_borrowers');
            const json    = await res.json();

            const members = Array.isArray(json.borrowers) ? json.borrowers
                          : Array.isArray(json.data)      ? json.data
                          : [];

            if (!members.length) {
                select.innerHTML = '<option value="">-- No members found --</option>';
                return;
            }

            select.innerHTML = '<option value="">-- Choose a member --</option>'
                + members.map(m =>
                    `<option value="${m.id}">${escHtml(m.name)} (${escHtml(m.borrower_id)})</option>`
                ).join('');

        } catch (e) {
            select.innerHTML = '<option value="">-- Error loading members --</option>';
            console.error('AI dropdown error:', e);
        }
    }

    async function loadRecommendations() {
        const borrowerId  = document.getElementById('aiMemberSelect').value;
        const limit       = parseInt(document.getElementById('aiRecCount').value) || 5;
        const grid        = document.getElementById('recGrid');
        const empty       = document.getElementById('recEmpty');
        const spinner     = document.getElementById('recSpinner');
        const profileWrap = document.getElementById('readingProfileWrap');

        if (!borrowerId) {
            grid.innerHTML            = '';
            empty.style.display       = 'block';
            profileWrap.style.display = 'none';
            return;
        }

        empty.style.display   = 'none';
        grid.innerHTML        = '';
        spinner.style.display = 'block';

        try {
            const res  = await fetch(API_URL + '?action=ai_recommendations', {
                method : 'POST',
                headers: { 'Content-Type': 'application/json' },
                body   : JSON.stringify({ borrower_id: borrowerId, limit }),
            });
            const json = await res.json();
            const recs = json.data?.recommendations ?? [];
            const prof = json.data?.reading_profile  ?? [];

            spinner.style.display = 'none';

            if (prof.length) {
                profileWrap.style.display = '';
                document.getElementById('readingProfilePills').innerHTML = prof.map(p => `
                    <span class="genre-pill" style="padding:.35rem .9rem">
                        ${escHtml(p.genre)} <strong>${p.percentage}%</strong>
                    </span>
                `).join('');
            } else {
                profileWrap.style.display = 'none';
            }

            if (!recs.length) {
                empty.style.display = 'block';
                empty.innerHTML = `
                    <i class="fas fa-book-open"></i>
                    <p>No recommendations yet - try borrowing some books first!</p>
                `;
                return;
            }

            const recTypeLabels = {
                genre_based  : { label: 'Genre Match',      cls: 'badge-info'    },
                collaborative: { label: 'Others Also Read', cls: 'badge-success' },
                trending     : { label: 'Trending',         cls: 'badge-warning' },
            };

            grid.innerHTML = recs.map(book => {
                const typeInfo  = recTypeLabels[book.rec_type] ?? { label: 'Suggested', cls: 'badge-info' };
                const score     = Math.round((parseFloat(book.similarity_score) || 0) * 100);
                const coverHtml = book.cover_url
                    ? `<img src="${escHtml(book.cover_url)}" alt="${escHtml(book.title)}" loading="lazy">`
                    : `<div class="book-cover-placeholder" style="background:var(--gradient-primary)"><i class="fas fa-book"></i></div>`;

                return `
                    <div class="book-card ai-rec-card">
                        <div class="book-cover-wrap">${coverHtml}</div>
                        <div class="book-info">
                            <div class="book-title-card fw-600">${escHtml(book.title)}</div>
                            <div class="book-author-card">${escHtml(book.author)}</div>
                            <span class="genre-pill">${escHtml(book.genre ?? 'General')}</span>
                            <div style="margin-top:.6rem">
                                <span class="badge ${typeInfo.cls}" style="font-size:.65rem;margin-bottom:.3rem">${typeInfo.label}</span>
                                ${score > 0 ? `
                                <div class="progress-bar" style="margin:.4rem 0">
                                    <div class="progress-fill" style="width:${Math.min(score,100)}%"></div>
                                </div>
                                <div class="small text-muted">Match: ${score}%</div>` : ''}
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

        } catch (err) {
            spinner.style.display = 'none';
            console.error('Recommendations error:', err);
            empty.style.display = 'block';
            empty.innerHTML = `
                <i class="fas fa-exclamation-circle" style="color:var(--danger)"></i>
                <p class="text-danger">Failed to load recommendations.</p>
            `;
        }
    }

    function init() {
        refreshRiskReport();
        loadReminderLog();
        populateMemberDropdown();

        const empty = document.getElementById('recEmpty');
        if (empty) {
            empty.style.display = 'block';
            empty.innerHTML = `
                <i class="fas fa-book-open"></i>
                <p>Select a member above to see personalized recommendations.</p>
            `;
        }
    }

    function escHtml(str) {
        if (str == null) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function formatTime(ts) {
        if (!ts) return '';
        const d = new Date(ts.replace(' ', 'T'));
        if (isNaN(d)) return ts;
        const diff = Math.floor((Date.now() - d.getTime()) / 1000);
        if (diff < 60)    return 'Just now';
        if (diff < 3600)  return Math.floor(diff / 60) + 'm ago';
        if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
        return d.toLocaleDateString();
    }

    return { init, refreshRiskReport, runReminderCheck, loadReminderLog, loadRecommendations };
})();

document.addEventListener('DOMContentLoaded', () => {
    const checkApp = setInterval(() => {
        if (window.app && window.app.ui && window.app.ui.switchTab) {
            clearInterval(checkApp);
            const _orig = app.ui.switchTab.bind(app.ui);
            app.ui.switchTab = function(tab) {
                _orig(tab);
                if (tab === 'ai') AI.init();
            };
        }
    }, 100);
});
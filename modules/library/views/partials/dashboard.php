<!-- ====== DASHBOARD ====== -->
<div class="tab-content active" id="tab-dashboard">

    <!-- ── Stat Cards ─────────────────────────────────────── -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-card-header">
                <div>
                    <div class="stat-card-title">Total Books</div>
                    <div class="stat-card-value" id="statTotalBooks">
                        <div class="spinner" style="width:22px;height:22px;margin:0;border-width:3px"></div>
                    </div>
                    <div class="stat-card-change"><i class="fas fa-book"></i> In collection</div>
                </div>
                <div class="stat-card-icon"><i class="fas fa-book"></i></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-header">
                <div>
                    <div class="stat-card-title">Total Members</div>
                    <div class="stat-card-value" id="statTotalMembers">—</div>
                    <div class="stat-card-change"><i class="fas fa-users"></i> Registered</div>
                </div>
                <div class="stat-card-icon"><i class="fas fa-users"></i></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-header">
                <div>
                    <div class="stat-card-title">Borrowed Books</div>
                    <div class="stat-card-value" id="statBorrowedBooks">—</div>
                    <div class="stat-card-change"><i class="fas fa-book-reader"></i> Active loans</div>
                </div>
                <div class="stat-card-icon"><i class="fas fa-book-reader"></i></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-header">
                <div>
                    <div class="stat-card-title">Overdue Returns</div>
                    <div class="stat-card-value" id="statOverdueBooks">—</div>
                    <div class="stat-card-change" id="statFineText"><i class="fas fa-peso-sign"></i> Fines pending</div>
                </div>
                <div class="stat-card-icon"><i class="fas fa-clock"></i></div>
            </div>
        </div>
    </div>

    <!-- ── Alert strip ────────────────────────────────────── -->
    <div style="display:flex;gap:1rem;margin-bottom:1.5rem;flex-wrap:wrap">
        <div class="alert-chip alert-chip-warning">
            <i class="fas fa-exclamation-circle"></i>
            <span><strong id="statDueToday">—</strong> book(s) due today</span>
            <button class="btn btn-sm btn-warning" style="padding:.3rem .75rem;font-size:.78rem" onclick="app.ui.switchTab('return')">Return Now</button>
        </div>
        <div class="alert-chip alert-chip-info">
            <i class="fas fa-bell"></i>
            <span><strong id="statDueSoon">—</strong> due within 3 days</span>
            <button class="btn btn-sm btn-info" style="padding:.3rem .75rem;font-size:.78rem" onclick="app.ui.switchTab('transactions')">View All</button>
        </div>
        <div class="alert-chip alert-chip-primary">
            <i class="fas fa-robot"></i>
            <span>AI reminders available</span>
            <button class="btn btn-sm" style="padding:.3rem .75rem;font-size:.78rem;background:var(--gradient-primary)" onclick="app.ui.switchTab('ai')">AI Insights</button>
        </div>
    </div>

    <!-- ── Collection availability bar ───────────────────── -->
    <div class="card" style="margin-bottom:1.5rem">
        <div class="card-header" style="margin-bottom:.75rem;padding-bottom:.75rem">
            <h3 class="card-title"><i class="fas fa-chart-bar"></i> Collection Availability</h3>
            <button class="btn btn-sm btn-outline" onclick="app.dashboard.load()"><i class="fas fa-sync-alt"></i> Refresh</button>
        </div>
        <div id="availabilityBar"><div class="spinner" style="width:22px;height:22px;border-width:2px;margin:.5rem 0"></div></div>
    </div>

    <!-- ── Quick Actions ──────────────────────────────────── -->
    <div class="card" style="margin-bottom:1.5rem">
        <div class="card-header" style="margin-bottom:.75rem;padding-bottom:.75rem">
            <h3 class="card-title"><i class="fas fa-bolt"></i> Quick Actions</h3>
        </div>
        <div class="d-flex flex-wrap gap-1">
            <button class="btn"             onclick="app.ui.switchTab('books')">      <i class="fas fa-plus"></i>       Add Book</button>
            <button class="btn btn-success" onclick="app.ui.switchTab('borrowers')">  <i class="fas fa-user-plus"></i>  Add Member</button>
            <button class="btn btn-warning" onclick="app.ui.switchTab('borrow')">     <i class="fas fa-handshake"></i>  Borrow Book</button>
            <button class="btn btn-info"    onclick="app.ui.switchTab('return')">     <i class="fas fa-undo"></i>        Return Book</button>
            <button class="btn btn-outline" onclick="app.ui.switchTab('reports')">    <i class="fas fa-chart-bar"></i>  Reports</button>
        </div>
    </div>

    <!-- ── Two column: borrow tracker + inventory ─────────── -->
    <div class="grid-2" style="margin-bottom:1.5rem">

        <!-- Active Borrow Tracker -->
        <div class="card" style="margin-bottom:0">
            <div class="card-header" style="margin-bottom:.75rem;padding-bottom:.75rem">
                <h3 class="card-title"><i class="fas fa-map-marker-alt"></i> Active Borrow Tracker</h3>
                <button class="btn btn-sm btn-outline" onclick="app.ui.switchTab('transactions')">See All</button>
            </div>
            <div style="font-size:.8rem;margin-bottom:.5rem;display:flex;gap:1rem;flex-wrap:wrap">
                <span><span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:var(--danger);margin-right:4px"></span>Overdue</span>
                <span><span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:var(--warning);margin-right:4px"></span>Due soon</span>
                <span><span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:var(--success);margin-right:4px"></span>On track</span>
            </div>
            <div id="borrowTracker"><div class="spinner"></div></div>
        </div>

        <!-- Inventory Management Panel -->
        <div style="display:flex;flex-direction:column;gap:1.25rem">

            <!-- Condition Health -->
            <div class="card" style="margin-bottom:0;flex:1">
                <div class="card-header" style="margin-bottom:.75rem;padding-bottom:.75rem">
                    <h3 class="card-title"><i class="fas fa-heartbeat"></i> Inventory Health</h3>
                    <button class="btn btn-sm btn-outline" onclick="app.ui.switchTab('books')">Manage</button>
                </div>
                <div id="inventoryHealth"><div class="spinner" style="width:20px;height:20px;border-width:2px;margin:.5rem 0"></div></div>
            </div>

            <!-- Genre Inventory -->
            <div class="card" style="margin-bottom:0;flex:1">
                <div class="card-header" style="margin-bottom:.75rem;padding-bottom:.75rem">
                    <h3 class="card-title"><i class="fas fa-layer-group"></i> Books by Genre</h3>
                </div>
                <div id="genreInventory"><div class="spinner" style="width:20px;height:20px;border-width:2px;margin:.5rem 0"></div></div>
            </div>
        </div>
    </div>

    <!-- ── Recent books + activity ────────────────────────── -->
    <div class="grid-2">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-star"></i> Recently Added</h3>
                <button class="btn btn-sm btn-outline" onclick="app.ui.switchTab('gallery')">Gallery</button>
            </div>
            <div id="dashRecentBooks"><div class="spinner"></div></div>
        </div>
        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-history"></i> Recent Activity</h3></div>
            <div id="dashActivity"><div class="spinner"></div></div>
        </div>
    </div>

</div>

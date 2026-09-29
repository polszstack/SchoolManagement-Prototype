<!-- ====== TRANSACTIONS ====== -->
<div class="tab-content" id="tab-transactions">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-list-alt"></i> All Transactions</h3>
            <div class="d-flex gap-1 align-center flex-wrap">
                <!-- Quick summary badges -->
                <span class="badge badge-warning" id="txnBadgeActive" style="cursor:pointer" onclick="document.getElementById('transStatusFilter').value='active';app.transactions.load()">Active: —</span>
                <span class="badge badge-danger"  id="txnBadgeOverdue" style="cursor:pointer" onclick="document.getElementById('transStatusFilter').value='overdue';app.transactions.load()">Overdue: —</span>
            </div>
        </div>

        <!-- Filters row -->
        <div class="d-flex gap-1 mb-2 align-center flex-wrap">
            <div class="search-box" style="flex:1;min-width:180px;margin:0">
                <i class="fas fa-search"></i>
                <input type="text" id="transSearch" placeholder="Search book or member..." oninput="app.transactions.search(this.value)">
            </div>
            <select id="transStatusFilter" onchange="app.transactions.load()" style="width:auto;padding:.625rem .875rem">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="returned">Returned</option>
                <option value="overdue">Overdue</option>
            </select>
            <button class="btn btn-sm btn-outline" onclick="app.transactions.exportCSV()"><i class="fas fa-download"></i> Export</button>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Book</th>
                        <th>Member</th>
                        <th>Borrowed</th>
                        <th>Due Date</th>
                        <th>Days Left</th>
                        <th>Return Date</th>
                        <th>Status</th>
                        <th>Fine</th>
                    </tr>
                </thead>
                <tbody id="transTableBody">
                    <tr><td colspan="8" class="empty-state"><div class="spinner"></div></td></tr>
                </tbody>
            </table>
        </div>

        <div class="pagination">
            <span class="pagination-info" id="transPaginationInfo">Loading...</span>
            <div class="d-flex gap-1">
                <button class="btn btn-sm btn-outline" onclick="app.transactions.changePage(-1)"><i class="fas fa-chevron-left"></i></button>
                <button class="btn btn-sm btn-outline" onclick="app.transactions.changePage(1)"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
    </div>
</div>

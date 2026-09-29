<!-- ====== BORROW ====== -->
<div class="tab-content" id="tab-borrow">
    <div class="card">
        <div class="card-header"><h3 class="card-title"><i class="fas fa-handshake"></i> Borrow a Book</h3></div>
        <div class="grid-2">
            <div>
                <div class="form-group">
                    <label>Select Book *</label>
                    <select id="borrowBookId"><option value="">— Select Available Book —</option></select>
                </div>
                <div class="form-group">
                    <label>Select Member *</label>
                    <select id="borrowMemberId"><option value="">— Select Member —</option></select>
                </div>
                <div class="form-group">
                    <label>Borrow Duration (days)</label>
                    <input type="number" id="borrowDays" value="14" min="1" max="90">
                </div>
                <div class="form-group">
                    <label>Notes</label>
                    <textarea id="borrowNotes" placeholder="Optional notes..." style="min-height:80px"></textarea>
                </div>
                <button class="btn btn-success w-100" onclick="app.borrow.issue()">
                    <i class="fas fa-handshake"></i> Issue Book
                </button>
            </div>
            <div>
                <h4 class="mb-1">Active Borrowings</h4>
                <div id="activeBorrowingsPanel"><div class="spinner"></div></div>
            </div>
        </div>
    </div>
</div>

<!-- ====== RETURN ====== -->
<div class="tab-content" id="tab-return">
    <div class="card">
        <div class="card-header"><h3 class="card-title"><i class="fas fa-undo"></i> Return a Book</h3></div>
        <div id="returnableList"><div class="spinner"></div></div>
    </div>
</div>

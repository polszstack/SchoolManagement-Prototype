<!-- ====== MEMBERS ====== -->
<div class="tab-content" id="tab-borrowers">

    <!-- ── Import from Registrar ──────────────────────────── -->
    <div class="card" style="margin-bottom:1.5rem;border-left:4px solid var(--primary)">
        <div class="card-header" style="margin-bottom:.875rem;padding-bottom:.875rem">
            <h3 class="card-title">
                <i class="fas fa-user-graduate"></i> Import Student from Registrar
                <span class="badge badge-info" style="font-size:.7rem;margin-left:.5rem">rgr_students</span>
            </h3>
            <span class="text-muted small">Search and import students directly from the registrar database</span>
        </div>

        <div class="d-flex gap-1 align-center" style="margin-bottom:.875rem;flex-wrap:wrap">
            <div class="search-box" style="flex:1;min-width:220px;margin:0">
                <i class="fas fa-search"></i>
                <input type="text" id="studentSearch"
                    placeholder="Search by name, student no., or course..."
                    oninput="app.members.searchStudents(this.value)">
            </div>
            <span class="text-muted small">Type at least 2 characters</span>
        </div>

        <!-- Student search results -->
        <div id="studentSearchResults" style="display:none">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Student No.</th>
                            <th>Full Name</th>
                            <th>Course</th>
                            <th>Year</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="studentSearchBody"></tbody>
                </table>
            </div>
        </div>
        <div id="studentSearchEmpty" style="display:none" class="text-muted small" style="padding:.5rem">
            <i class="fas fa-info-circle"></i> No students found or all already imported.
        </div>
        <div id="studentSearchSpinner" style="display:none"><div class="spinner" style="width:24px;height:24px;border-width:3px;margin:.5rem 0"></div></div>
    </div>

    <!-- ── Members list + manual add ─────────────────────── -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-users"></i> Library Members</h3>
        </div>
        <div class="grid-2">

            <!-- ADD / EDIT FORM -->
            <div>
                <h4 class="mb-1" id="memberFormTitle">Add New Member</h4>
                <input type="hidden" id="editMemberId" value="">
                <div class="grid-2">
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" id="memberName" placeholder="Full name">
                    </div>
                    <div class="form-group">
                        <label>Member ID *</label>
                        <input type="text" id="memberBorrowerId" placeholder="e.g. 1001 or STU-001">
                    </div>
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" id="memberEmail" placeholder="email@school.edu">
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" id="memberPhone" placeholder="555-0000">
                    </div>
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label>Type</label>
                        <select id="memberType">
                            <option value="Student">Student</option>
                            <option value="Teacher">Teacher</option>
                            <option value="Staff">Staff</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Course / Grade / Dept</label>
                        <input type="text" id="memberGrade" placeholder="BSCrim Year 2 / Grade 10">
                    </div>
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <textarea id="memberAddress" placeholder="Street address..." style="min-height:70px"></textarea>
                </div>
                <div class="d-flex gap-1">
                    <button class="btn btn-success w-100" id="memberSubmitBtn" onclick="app.members.submit()">
                        <i class="fas fa-user-plus"></i> Add Member
                    </button>
                    <button class="btn btn-outline" id="memberCancelBtn" style="display:none" onclick="app.members.cancelEdit()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </div>

            <!-- TABLE -->
            <div>
                <div class="d-flex gap-1 mb-1 align-center">
                    <div class="search-box" style="flex:1;margin:0">
                        <i class="fas fa-search"></i>
                        <input type="text" id="memberSearch" placeholder="Search members..."
                            oninput="app.members.search(this.value)">
                    </div>
                    <select id="memberTypeFilter" onchange="app.members.filter()"
                        style="width:auto;padding:.875rem">
                        <option value="">All Types</option>
                        <option value="Student">Students</option>
                        <option value="Teacher">Teachers</option>
                        <option value="Staff">Staff</option>
                    </select>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Name / ID</th>
                                <th>Type</th>
                                <th>Borrowed</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="membersTableBody">
                            <tr><td colspan="4" class="empty-state"><div class="spinner"></div></td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="pagination">
                    <span class="pagination-info" id="membersPaginationInfo">Loading...</span>
                    <div class="d-flex gap-1">
                        <button class="btn btn-sm btn-outline" onclick="app.members.changePage(-1)">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="btn btn-sm btn-outline" onclick="app.members.changePage(1)">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

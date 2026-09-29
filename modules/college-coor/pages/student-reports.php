<div class="reports-wrapper">
    <!-- Top Navigation Bar -->
    <div class="reports-top-bar">
        <h1 class="page-title">Academic Reports</h1>
        <div class="breadcrumb">
            <span>Dashboard</span> / <span class="active">Reports</span>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast" id="toastNotification"></div>

    <div class="reports-container">
        <!-- Report Categories Section -->
        <section class="report-categories-section">
            <div class="section-header">
                <h2>Select Report Type</h2>
            </div>

            <div class="report-categories-grid">
                <div class="report-category-card" data-report="programs">
                    <div class="report-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="report-info">
                        <h3>Programs Report</h3>
                        <p>View all academic programs and curriculum details</p>
                    </div>
                    <div class="report-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </div>

                <div class="report-category-card" data-report="subjects">
                    <div class="report-icon">
                        <i class="fas fa-list-ul"></i>
                    </div>
                    <div class="report-info">
                        <h3>Subjects Report</h3>
                        <p>Detailed view of all subjects and course information</p>
                    </div>
                    <div class="report-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </div>

                <div class="report-category-card" data-report="sections">
                    <div class="report-icon">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div class="report-info">
                        <h3>Sections Report</h3>
                        <p>Section assignments and enrollment information</p>
                    </div>
                    <div class="report-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </div>

                <div class="report-category-card" data-report="class-schedule">
                    <div class="report-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="report-info">
                        <h3>Class Schedule Report</h3>
                        <p>Complete timetable and schedule information</p>
                    </div>
                    <div class="report-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </div>

                <div class="report-category-card" data-report="faculty-load">
                    <div class="report-icon">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <div class="report-info">
                        <h3>Faculty Load Report</h3>
                        <p>Faculty workload distribution and course assignments</p>
                    </div>
                    <div class="report-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </div>

                <div class="report-category-card" data-report="student-academic">
                    <div class="report-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="report-info">
                        <h3>Student Academic Report</h3>
                        <p>Student grades, GPA, and academic standing</p>
                    </div>
                    <div class="report-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </div>
            </div>
        </section>

        <!-- Report Filter Panel -->
        <section class="report-filter-section" id="filterSection" style="display: none;">
            <div class="section-header">
                <h2>Report Filters</h2>
                <button class="btn btn-secondary btn-sm" onclick="resetFilters()">
                    <i class="fas fa-redo"></i> Reset
                </button>
            </div>

            <div class="filter-panel">
                <div class="filters-grid">
                    <div class="filter-group">
                        <label for="reportAcademicYear">Academic Year</label>
                        <select id="reportAcademicYear" class="filter-select">
                            <option value="2025-2026" selected>2025-2026</option>
                            <option value="2024-2025">2024-2025</option>
                            <option value="2026-2027">2026-2027</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="reportSemester">Semester</label>
                        <select id="reportSemester" class="filter-select">
                            <option value="1st Semester" selected>1st Semester</option>
                            <option value="2nd Semester">2nd Semester</option>
                            <option value="Summer">Summer</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="reportProgram">Program</label>
                        <select id="reportProgram" class="filter-select">
                            <option value="">All Programs</option>
                            <option value="BS-IT">BS-IT</option>
                            <option value="BS-ED">BS-ED</option>
                            <option value="BS-ACCT">BS-ACCT</option>
                            <option value="BS-CIS">BS-CIS</option>
                            <option value="BS-BIOLOGY">BS-BIOLOGY</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="reportSection">Section</label>
                        <select id="reportSection" class="filter-select">
                            <option value="">All Sections</option>
                            <option value="IT-1A">IT-1A</option>
                            <option value="IT-1B">IT-1B</option>
                            <option value="IT-2A">IT-2A</option>
                            <option value="IT-3A">IT-3A</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="reportFaculty">Faculty</label>
                        <select id="reportFaculty" class="filter-select">
                            <option value="">All Faculty</option>
                            <option value="Dr. Maria Santos">Dr. Maria Santos</option>
                            <option value="Mr. John Dela Cruz">Mr. John Dela Cruz</option>
                            <option value="Dr. Elizabeth Reyes">Dr. Elizabeth Reyes</option>
                            <option value="Mr. Ramon Garcia">Mr. Ramon Garcia</option>
                            <option value="Ms. Anna Rodriguez">Ms. Anna Rodriguez</option>
                        </select>
                    </div>
                </div>

                <div class="action-buttons">
                    <button class="btn btn-primary" onclick="generateReport()">
                        <i class="fas fa-sync-alt"></i> Generate Report
                    </button>
                    <button class="btn btn-info" onclick="exportPDF()">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                    <button class="btn btn-success" onclick="exportExcel()">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </button>
                    <button class="btn btn-warning" onclick="printReport()">
                        <i class="fas fa-print"></i> Print
                    </button>
                </div>
            </div>
        </section>

        <!-- Generated Report Section -->
        <section class="report-results-section" id="resultsSection" style="display: none;">
            <div class="section-header">
                <h2>Report Results</h2>
                <span class="report-info-badge" id="reportInfoBadge"></span>
            </div>

            <div class="table-wrapper">
                <table class="report-table" id="reportTable">
                    <thead>
                        <tr id="reportTableHead">
                            <!-- Headers populated by JavaScript -->
                        </tr>
                    </thead>
                    <tbody id="reportTableBody">
                        <!-- Data populated by JavaScript -->
                    </tbody>
                </table>
            </div>

            <div class="report-summary">
                <div class="summary-stats">
                    <div class="summary-stat">
                        <span class="stat-label">Total Records</span>
                        <span class="stat-value" id="totalRecords">0</span>
                    </div>
                    <div class="summary-stat">
                        <span class="stat-label">Generated</span>
                        <span class="stat-value" id="generatedTime">-</span>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

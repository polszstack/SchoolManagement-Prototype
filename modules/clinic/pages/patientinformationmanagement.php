<?php
include_once __DIR__ . '/../../../database/db.php';
include_once __DIR__ . '/../classes/student.php';

$studentClass = new Student();
$allStudents  = $studentClass->getAllStudents();
if (!$allStudents) $allStudents = [];

// ── Server-side search + pagination (initial load / fallback) ────────────────
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

if (!empty($searchTerm)) {
    $filteredStudents = array_filter($allStudents, function ($student) use ($searchTerm) {
        $q = strtolower($searchTerm);
        return strpos(strtolower($student['student_number']),    $q) !== false
            || strpos(strtolower($student['first_name']),        $q) !== false
            || strpos(strtolower($student['last_name']),         $q) !== false
            || strpos(strtolower($student['course']   ?? ''),    $q) !== false
            || strpos(strtolower($student['email']    ?? ''),    $q) !== false;
    });
    $students = array_values($filteredStudents);
} else {
    $students = $allStudents;
}

$rowsPerPage   = 10;
$totalStudents = count($students);
$totalPages    = max(1, (int) ceil($totalStudents / $rowsPerPage));
$currentPage   = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$currentPage   = max(1, min($currentPage, $totalPages));
$offset        = ($currentPage - 1) * $rowsPerPage;
$studentsOnPage = array_slice($students, $offset, $rowsPerPage);
?>

<div class="module-header">
    <h1>Patient Information</h1>
</div>

<div class="module-content">

    <!-- ── Student Search ──────────────────────────────────────────────────── -->
    <div class="form-group">
        <label for="studentSearch">Search by Student Number, Name, or Course:</label>
        <div class="search-container">
            <input type="text"
                   id="studentSearch"
                   class="search-input"
                   placeholder="Enter search term..."
                   value="<?= htmlspecialchars($searchTerm) ?>">
            <button type="button" id="searchBtn" class="btn-primary">Search</button>
            <button type="button" id="clearBtn"  class="btn-secondary">Clear</button>
        </div>
    </div>

    <!-- ── Student Select Dropdown ─────────────────────────────────────────── -->
    <div class="form-group">
        <label for="studentID">Or Select Student from List:</label>
        <div class="search-container">
            <select id="studentID" class="search-select">
                <option value="">-- Select Student --</option>
                <?php foreach ($allStudents as $student) : ?>
                    <option value="<?= htmlspecialchars($student['student_number']) ?>">
                        <?= htmlspecialchars($student['student_number']) ?> -
                        <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="button" id="selectSearchBtn" class="btn-primary">Load Student</button>
        </div>
    </div>

    <!-- ── Edit Form (hidden until a student is loaded) ────────────────────── -->
    <div id="studentInfo" class="student-info" style="display:none;">
        <h2>Edit Student Information</h2>
        <form id="studentForm" novalidate>
            <input type="hidden" id="hiddenNumber"   name="student_number">
            <input type="hidden" id="originalNumber" name="original_number">

            <div class="form-row">
                <div class="form-col">
                    <label for="firstName">First Name: <span class="required">*</span></label>
                    <input type="text" id="firstName" name="first_name" required>
                </div>
                <div class="form-col">
                    <label for="lastName">Last Name: <span class="required">*</span></label>
                    <input type="text" id="lastName" name="last_name" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col">
                    <label for="gender">Gender:</label>
                    <select id="gender" name="gender">
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-col">
                    <label for="dob">Date of Birth:</label>
                    <input type="date" id="dob" name="date_of_birth">
                </div>
            </div>

            <div class="form-row">
                <div class="form-col">
                    <label for="phone">Phone:</label>
                    <input type="tel" id="phone" name="phone" placeholder="e.g., 09123456789">
                </div>
                <div class="form-col">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" placeholder="student@example.com">
                </div>
            </div>

            <div class="form-row">
                <div class="form-col">
                    <label for="course">Course:</label>
                    <input type="text" id="course" name="course" placeholder="e.g., BSIT">
                </div>
                <div class="form-col">
                    <label for="yearLevel">Year Level:</label>
                    <select id="yearLevel" name="year_level">
                        <option value="">Select Year</option>
                        <option value="1">1st Year</option>
                        <option value="2">2nd Year</option>
                        <option value="3">3rd Year</option>
                        <option value="4">4th Year</option>
                        <option value="5">5th Year</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col-full">
                    <label for="address">Address:</label>
                    <textarea id="address" name="address" rows="3" placeholder="Enter complete address"></textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit"  class="btn-primary">Update Student</button>
                <button type="button" id="cancelEditBtn" class="btn-secondary">Cancel</button>
            </div>
        </form>
        <div id="updateMessage" class="message" style="display:none;"></div>
    </div>

    <!-- ── Student Records Table ───────────────────────────────────────────── -->
    <h2>Student Records <span id="searchTitle"><?= !empty($searchTerm)
        ? '- Search Results for "' . htmlspecialchars($searchTerm) . '"'
        : '' ?></span></h2>

    <div class="table-info" id="tableInfo"
         <?= $totalStudents === 0 ? 'style="display:none;"' : '' ?>>
        <p>Showing
            <span id="startResult"><?= $totalStudents > 0 ? $offset + 1 : 0 ?></span> to
            <span id="endResult"><?= min($offset + $rowsPerPage, $totalStudents) ?></span> of
            <span id="totalResults"><?= $totalStudents ?></span> students
        </p>
        <div class="table-actions">
            <button type="button" id="exportBtn" class="btn-secondary">Export to CSV</button>
        </div>
    </div>

    <div class="table-container">
        <table id="studentRecords" class="data-table">
            <thead>
                <tr>
                    <th>Student Number</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Gender</th>
                    <th>Date of Birth</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Course</th>
                    <th>Year</th>
                    <th>Address</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <?php if (empty($studentsOnPage)) : ?>
                    <tr><td colspan="11" class="no-data">No students found.</td></tr>
                <?php else : ?>
                    <?php foreach ($studentsOnPage as $s) : ?>
                        <tr>
                            <td><?= htmlspecialchars($s['student_number']) ?></td>
                            <td><?= htmlspecialchars($s['first_name']) ?></td>
                            <td><?= htmlspecialchars($s['last_name']) ?></td>
                            <td><?= htmlspecialchars($s['gender']        ?? '') ?></td>
                            <td><?= htmlspecialchars($s['date_of_birth'] ?? '') ?></td>
                            <td><?= htmlspecialchars($s['phone']         ?? '') ?></td>
                            <td><?= htmlspecialchars($s['email']         ?? '') ?></td>
                            <td><?= htmlspecialchars($s['course']        ?? '') ?></td>
                            <td><?= htmlspecialchars($s['year_level']    ?? '') ?></td>
                            <td><?= htmlspecialchars($s['address']       ?? '') ?></td>
                            <td>
                                <!-- data-student keeps the student number safely out of JS string literals -->
                                <button type="button"
                                        class="btn-edit"
                                        data-student="<?= htmlspecialchars($s['student_number']) ?>">
                                    Edit
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- ── Pagination ──────────────────────────────────────────────────────── -->
    <div id="paginationContainer">
        <?php if ($totalPages > 1) : ?>
        <div class="pagination">
            <div class="pagination-info">
                Page <span id="currentPageDisplay"><?= $currentPage ?></span>
                of   <span id="totalPagesDisplay"><?= $totalPages ?></span>
            </div>
            <div class="pagination-controls">
                <?php if ($currentPage > 1) : ?>
                    <a href="#" class="page-link" data-page="1" title="First Page">««</a>
                    <a href="#" class="page-link" data-page="<?= $currentPage - 1 ?>" title="Previous Page">«</a>
                <?php endif; ?>

                <?php
                    $win   = 2;
                    $start = max(1, $currentPage - $win);
                    $end   = min($totalPages, $currentPage + $win);
                    if ($start > 1) echo '<span class="page-ellipsis">…</span>';
                ?>
                <?php for ($i = $start; $i <= $end; $i++) : ?>
                    <?php if ($i === $currentPage) : ?>
                        <span class="page-current"><?= $i ?></span>
                    <?php else : ?>
                        <a href="#" class="page-link" data-page="<?= $i ?>"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                <?php if ($end < $totalPages) : ?>
                    <span class="page-ellipsis">…</span>
                <?php endif; ?>

                <?php if ($currentPage < $totalPages) : ?>
                    <a href="#" class="page-link" data-page="<?= $currentPage + 1 ?>" title="Next Page">»</a>
                    <a href="#" class="page-link" data-page="<?= $totalPages ?>" title="Last Page">»»</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

</div><!-- /module-content -->

<script>
(function () {
'use strict';

// ── Module state ─────────────────────────────────────────────────────────────
let currentSearchTerm = '<?= addslashes($searchTerm) ?>';
let currentPage       = <?= $currentPage ?>;

// ── Utilities ─────────────────────────────────────────────────────────────────
function escapeHtml(text) {
    if (text === null || text === undefined) return '';
    const d = document.createElement('div');
    d.textContent = String(text);
    return d.innerHTML;
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function isValidPhone(phone) {
    return /^[\d\s+\-]{10,15}$/.test(phone);
}

function showMsg(cls, text) {
    const el = document.getElementById('updateMessage');
    el.className    = 'message ' + cls;
    el.textContent  = text;
    el.style.display = 'block';
}

function hideMsg() {
    const el = document.getElementById('updateMessage');
    el.style.display = 'none';
    el.textContent   = '';
}

// ── Load a student into the edit form via AJAX ────────────────────────────────
function loadStudent(studentNumber) {
    if (!studentNumber) {
        alert('Please select a student.');
        return;
    }

    showMsg('info', 'Loading student data…');

    fetch('../handlers/get_student.php?student_number=' + encodeURIComponent(studentNumber))
        .then(res => {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        })
        .then(student => {
            if (!student || student.error) {
                alert('Student not found.');
                document.getElementById('studentInfo').style.display = 'none';
                hideMsg();
                return;
            }

            // Populate form fields
            document.getElementById('hiddenNumber').value   = student.student_number  || '';
            document.getElementById('originalNumber').value = student.student_number  || '';
            document.getElementById('firstName').value      = student.first_name       || '';
            document.getElementById('lastName').value       = student.last_name        || '';
            document.getElementById('gender').value         = student.gender           || '';
            document.getElementById('dob').value            = student.date_of_birth    || '';
            document.getElementById('phone').value          = student.phone            || '';
            document.getElementById('email').value          = student.email            || '';
            document.getElementById('course').value         = student.course           || '';
            document.getElementById('yearLevel').value      = student.year_level       || '';
            document.getElementById('address').value        = student.address          || '';

            document.getElementById('studentInfo').style.display = 'block';
            hideMsg();
            document.getElementById('studentInfo').scrollIntoView({ behavior: 'smooth' });
        })
        .catch(err => {
            console.error('loadStudent:', err);
            alert('Error loading student data. Please try again.');
            document.getElementById('studentInfo').style.display = 'none';
            hideMsg();
        });
}

// ── AJAX table search + pagination ────────────────────────────────────────────
function performSearch(searchTerm, page) {
    page = page || 1;
    currentSearchTerm = searchTerm;
    currentPage       = page;

    document.getElementById('tableBody').innerHTML =
        '<tr><td colspan="11" class="no-data">Searching…</td></tr>';

    fetch('../handlers/search_students.php?search=' + encodeURIComponent(searchTerm) + '&page=' + page)
        .then(res => {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        })
        .then(data => {
            renderTableBody(data.students);
            renderPagination(data.currentPage, data.totalPages);
            renderResultsInfo(data.start, data.end, data.totalStudents);

            // Update search title
            document.getElementById('searchTitle').textContent = searchTerm
                ? '- Search Results for "' + searchTerm + '"' : '';

            // Show / hide table info bar
            document.getElementById('tableInfo').style.display =
                data.totalStudents > 0 ? '' : 'none';

            // Update browser URL without a reload
            const url = new URL(window.location.href);
            searchTerm
                ? url.searchParams.set('search', searchTerm)
                : url.searchParams.delete('search');
            data.currentPage > 1
                ? url.searchParams.set('page', data.currentPage)
                : url.searchParams.delete('page');
            window.history.replaceState({}, '', url);
        })
        .catch(err => {
            console.error('performSearch:', err);
            document.getElementById('tableBody').innerHTML =
                '<tr><td colspan="11" class="no-data">Error searching. Please try again.</td></tr>';
        });
}

// ── Render helpers ────────────────────────────────────────────────────────────

function renderTableBody(students) {
    const tbody = document.getElementById('tableBody');

    if (!students || students.length === 0) {
        tbody.innerHTML = '<tr><td colspan="11" class="no-data">No students found.</td></tr>';
        return;
    }

    let html = '';
    students.forEach(s => {
        // IMPORTANT: data-student attribute instead of inline onclick —
        // avoids the escapeHtml-breaks-JS-string-literal bug completely.
        html +=
            '<tr>' +
            '<td>' + escapeHtml(s.student_number)    + '</td>' +
            '<td>' + escapeHtml(s.first_name)         + '</td>' +
            '<td>' + escapeHtml(s.last_name)          + '</td>' +
            '<td>' + escapeHtml(s.gender        || '') + '</td>' +
            '<td>' + escapeHtml(s.date_of_birth || '') + '</td>' +
            '<td>' + escapeHtml(s.phone         || '') + '</td>' +
            '<td>' + escapeHtml(s.email         || '') + '</td>' +
            '<td>' + escapeHtml(s.course        || '') + '</td>' +
            '<td>' + escapeHtml(s.year_level    || '') + '</td>' +
            '<td>' + escapeHtml(s.address       || '') + '</td>' +
            '<td><button type="button" class="btn-edit" data-student="' +
                escapeHtml(s.student_number) + '">Edit</button></td>' +
            '</tr>';
    });

    tbody.innerHTML = html;
    bindEditButtons(tbody);   // re-bind after innerHTML wipe
}

function renderPagination(page, totalPages) {
    const container = document.getElementById('paginationContainer');

    if (totalPages <= 1) {
        container.innerHTML = '';
        return;
    }

    const win   = 2;
    const start = Math.max(1, page - win);
    const end   = Math.min(totalPages, page + win);

    let html =
        '<div class="pagination">' +
        '<div class="pagination-info">Page ' + page + ' of ' + totalPages + '</div>' +
        '<div class="pagination-controls">';

    if (page > 1) {
        html += '<a href="#" class="page-link" data-page="1" title="First Page">««</a>';
        html += '<a href="#" class="page-link" data-page="' + (page - 1) + '" title="Previous Page">«</a>';
    }

    if (start > 1) html += '<span class="page-ellipsis">…</span>';

    for (let i = start; i <= end; i++) {
        if (i === page) {
            html += '<span class="page-current">' + i + '</span>';
        } else {
            html += '<a href="#" class="page-link" data-page="' + i + '">' + i + '</a>';
        }
    }

    if (end < totalPages) html += '<span class="page-ellipsis">…</span>';

    if (page < totalPages) {
        html += '<a href="#" class="page-link" data-page="' + (page + 1) + '" title="Next Page">»</a>';
        html += '<a href="#" class="page-link" data-page="' + totalPages  + '" title="Last Page">»»</a>';
    }

    html += '</div></div>';
    container.innerHTML = html;
    bindPaginationLinks(container);
}

function renderResultsInfo(start, end, total) {
    const s = document.getElementById('startResult');
    const e = document.getElementById('endResult');
    const t = document.getElementById('totalResults');
    if (s) s.textContent = start;
    if (e) e.textContent = end;
    if (t) t.textContent = total;
}

// ── Safe event-binding (data-attribute approach, no inline JS) ────────────────

function bindEditButtons(scope) {
    scope = scope || document;
    scope.querySelectorAll('button.btn-edit[data-student]').forEach(btn => {
        // Clone removes stale listeners added by previous renders
        const fresh = btn.cloneNode(true);
        btn.parentNode.replaceChild(fresh, btn);
        fresh.addEventListener('click', function () {
            const num    = this.getAttribute('data-student');
            const select = document.getElementById('studentID');
            // Sync the dropdown if this student exists in it
            if (select) {
                const found = Array.from(select.options).some(o => o.value === num);
                if (found) select.value = num;
            }
            loadStudent(num);
        });
    });
}

function bindPaginationLinks(scope) {
    scope = scope || document;
    scope.querySelectorAll('a.page-link[data-page]').forEach(link => {
        const fresh = link.cloneNode(true);
        link.parentNode.replaceChild(fresh, link);
        fresh.addEventListener('click', function (e) {
            e.preventDefault();
            performSearch(currentSearchTerm, parseInt(this.getAttribute('data-page'), 10));
        });
    });
}

// ── Wire everything up after DOM is ready ─────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {

    // Bind PHP-rendered Edit buttons and pagination links on first load
    bindEditButtons(document.getElementById('tableBody'));
    bindPaginationLinks(document.getElementById('paginationContainer'));

    // Search button
    document.getElementById('searchBtn').addEventListener('click', function () {
        performSearch(document.getElementById('studentSearch').value.trim(), 1);
    });

    // Enter key in the search input
    document.getElementById('studentSearch').addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            performSearch(this.value.trim(), 1);
        }
    });

    // Load Student button (dropdown)
    document.getElementById('selectSearchBtn').addEventListener('click', function () {
        const num = document.getElementById('studentID').value.trim();
        if (!num) {
            alert('Please select a student from the list.');
            return;
        }
        loadStudent(num);
    });

    // Clear button — resets everything and reloads all students
    document.getElementById('clearBtn').addEventListener('click', function () {
        document.getElementById('studentSearch').value       = '';
        document.getElementById('studentID').value           = '';
        document.getElementById('studentInfo').style.display = 'none';
        hideMsg();
        performSearch('', 1);
    });

    // Cancel edit button
    document.getElementById('cancelEditBtn').addEventListener('click', function () {
        document.getElementById('studentInfo').style.display = 'none';
        document.getElementById('studentID').value           = '';
        hideMsg();
    });

    // Update student form
    document.getElementById('studentForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const firstName = document.getElementById('firstName').value.trim();
        const lastName  = document.getElementById('lastName').value.trim();

        if (!firstName || !lastName) {
            alert('First name and last name are required.');
            return;
        }

        const email = document.getElementById('email').value.trim();
        if (email && !isValidEmail(email)) {
            alert('Please enter a valid email address.');
            return;
        }

        const phone = document.getElementById('phone').value.trim();
        if (phone && !isValidPhone(phone)) {
            alert('Please enter a valid phone number (10–15 digits).');
            return;
        }

        const payload = {
            original_number: document.getElementById('originalNumber').value,
            student_number:  document.getElementById('hiddenNumber').value,
            first_name:      firstName,
            last_name:       lastName,
            gender:          document.getElementById('gender').value,
            date_of_birth:   document.getElementById('dob').value,
            phone,
            email,
            course:          document.getElementById('course').value.trim(),
            year_level:      document.getElementById('yearLevel').value,
            address:         document.getElementById('address').value.trim()
        };

        showMsg('info', 'Updating student record…');

        fetch('../handlers/update_student.php', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify(payload)
        })
        .then(res => {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        })
        .then(result => {
            if (result.success) {
                showMsg('success', 'Student updated successfully!');
                setTimeout(() => {
                    document.getElementById('studentInfo').style.display = 'none';
                    hideMsg();
                    performSearch(currentSearchTerm, currentPage);
                }, 1500);
            } else {
                showMsg('error', result.message || 'Update failed. Please try again.');
            }
        })
        .catch(err => {
            console.error('update:', err);
            showMsg('error', 'Server error. Please try again.');
        });
    });

    // Export to CSV
    const exportBtn = document.getElementById('exportBtn');
    if (exportBtn) {
        exportBtn.addEventListener('click', function () {
            const rows = document.querySelectorAll('#tableBody tr');

            if (!rows.length || (rows.length === 1 && rows[0].querySelector('.no-data'))) {
                alert('No data to export.');
                return;
            }

            const csv     = [];
            const headers = [];
            document.querySelectorAll('#studentRecords thead th').forEach(th => {
                if (th.textContent.trim() !== 'Actions') {
                    headers.push('"' + th.textContent.trim().replace(/"/g, '""') + '"');
                }
            });
            csv.push(headers.join(','));

            rows.forEach(row => {
                if (row.querySelector('.no-data')) return;
                const cells   = row.querySelectorAll('td');
                const rowData = [];
                for (let i = 0; i < cells.length - 1; i++) {   // skip Actions
                    let txt = cells[i].textContent.trim().replace(/"/g, '""');
                    if (txt.includes(',') || txt.includes('\n') || txt.includes('"')) {
                        txt = '"' + txt + '"';
                    }
                    rowData.push(txt);
                }
                csv.push(rowData.join(','));
            });

            const blob = new Blob([csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href     = URL.createObjectURL(blob);
            link.download = 'student_records_' + new Date().toISOString().split('T')[0] + '.csv';
            link.style.display = 'none';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(link.href);
        });
    }

}); // end DOMContentLoaded

})(); // IIFE — no globals leaked
</script>
// JS for fetching and displaying student records table in Academics Module
// To use: import this file in academics.php

document.addEventListener('DOMContentLoaded', function() {
    const studentsTab = document.getElementById('students-tab');
    if (studentsTab) {
        studentsTab.addEventListener('shown.bs.tab', function() {
            loadStudentRecordsTable();
        });
    }
    // Optionally, load on page load if tab is already active
    if (studentsTab && studentsTab.classList.contains('active')) {
        loadStudentRecordsTable();
    }
});

function loadStudentRecordsTable() {
    const tbody = document.querySelector('#studentRecordsTable tbody');
    if (!tbody) return;
    tbody.innerHTML = '<tr><td colspan="9" class="text-center">Loading...</td></tr>';
    fetch('../api/get_student_records.php')
        .then(res => res.json())
        .then(records => {
            if (!records || records.length === 0) {
                tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted">No student records found</td></tr>';
                return;
            }
            tbody.innerHTML = records.map(row => `
                <tr>
                    <td>${escapeHTML(row.student_id)}</td>
                    <td>${escapeHTML(row.full_name)}</td>
                    <td>${escapeHTML(row.email)}</td>
                    <td>${escapeHTML(row.contact_no)}</td>
                    <td>${escapeHTML(row.gender)}</td>
                    <td>${escapeHTML(row.program)}</td>
                    <td>${escapeHTML(row.grade_level)}</td>
                    <td>${escapeHTML(row.section_name)}</td>
                    <td>${escapeHTML(row.status)}</td>
                </tr>
            `).join('');
        })
        .catch(() => {
            tbody.innerHTML = '<tr><td colspan="9" class="text-center text-danger">Failed to load student records</td></tr>';
        });
}

function escapeHTML(str) {
    return String(str || '').replace(/[&<>"]/g, function(c) {
        return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c];
    });
}

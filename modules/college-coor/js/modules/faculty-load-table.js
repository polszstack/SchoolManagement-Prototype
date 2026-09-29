// JS for fetching and displaying faculty load table in Academics Module
// To use: import this file in academics.php

document.addEventListener('DOMContentLoaded', function() {
    const facultyTab = document.getElementById('faculty-tab');
    if (facultyTab) {
        facultyTab.addEventListener('shown.bs.tab', function() {
            loadFacultyLoadTable();
        });
    }
    // Optionally, load on page load if tab is already active
    if (facultyTab && facultyTab.classList.contains('active')) {
        loadFacultyLoadTable();
    }
});

function loadFacultyLoadTable() {
    const tbody = document.querySelector('#facultyLoadTable tbody');
    if (!tbody) return;
    tbody.innerHTML = '<tr><td colspan="6" class="text-center">Loading...</td></tr>';
    fetch('../api/get_faculty_load.php')
        .then(res => res.json())
        .then(loads => {
            if (!loads || loads.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No faculty load found</td></tr>';
                return;
            }
            tbody.innerHTML = loads.map(row => `
                <tr>
                    <td>${escapeHTML(row.faculty_name)}</td>
                    <td>${escapeHTML(row.subject_code)}</td>
                    <td>${escapeHTML(row.subject_name)}</td>
                    <td>${escapeHTML(row.section_name)}</td>
                    <td>${escapeHTML(row.total_units)}</td>
                    <td>${escapeHTML(row.total_classes)}</td>
                </tr>
            `).join('');
        })
        .catch(() => {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Failed to load faculty load</td></tr>';
        });
}

function escapeHTML(str) {
    return String(str || '').replace(/[&<>"]/g, function(c) {
        return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c];
    });
}

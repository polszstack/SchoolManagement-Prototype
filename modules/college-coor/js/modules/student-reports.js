// =====================================================
// REPORTS MODULE - JAVASCRIPT
// =====================================================

let currentReport = null;
let reportData = {
    programs: [
        { code: 'BS-IT', name: 'Bachelor of Science in Information Technology', director: 'Dr. Maria Santos', students: 245, units: 132, status: 'Active' },
        { code: 'BS-ED', name: 'Bachelor of Science in Education', director: 'Mr. John Dela Cruz', students: 180, units: 120, status: 'Active' },
        { code: 'BS-ACCT', name: 'Bachelor of Science in Accountancy', director: 'Dr. Elizabeth Reyes', students: 156, units: 124, status: 'Active' }
    ],
    subjects: [
        { code: 'IT-101', name: 'Introduction to Programming', units: 3, program: 'BS-IT', faculty: 'Dr. Maria Santos', sections: 3 },
        { code: 'IT-102', name: 'Data Structures', units: 3, program: 'BS-IT', faculty: 'Mr. John Dela Cruz', sections: 2 },
        { code: 'IT-103', name: 'Web Development', units: 3, program: 'BS-IT', faculty: 'Dr. Elizabeth Reyes', sections: 2 },
        { code: 'MATH-101', name: 'Calculus I', units: 4, program: 'General', faculty: 'Mr. Ramon Garcia', sections: 4 }
    ],
    sections: [
        { name: 'IT-1A', program: 'BS-IT', level: '1st Year', year: 'Academic Year 2025-2026', students: 45, capacity: 50, adviser: 'Dr. Maria Santos' },
        { name: 'IT-1B', program: 'BS-IT', level: '1st Year', year: 'Academic Year 2025-2026', students: 42, capacity: 50, adviser: 'Mr. John Dela Cruz' },
        { name: 'IT-2A', program: 'BS-IT', level: '2nd Year', year: 'Academic Year 2025-2026', students: 40, capacity: 50, adviser: 'Dr. Elizabeth Reyes' }
    ],
    'class-schedule': [
        { subject: 'IT-101', section: 'IT-1A', faculty: 'Dr. Maria Santos', day: 'Monday', time: '08:00-09:30', room: 'CCS-101', students: 45 },
        { subject: 'IT-102', section: 'IT-1A', faculty: 'Mr. John Dela Cruz', day: 'Tuesday', time: '10:00-11:30', room: 'CCS-102', students: 45 },
        { subject: 'IT-103', section: 'IT-1A', faculty: 'Dr. Elizabeth Reyes', day: 'Wednesday', time: '14:00-15:30', room: 'CCS-103', students: 45 }
    ],
    'faculty-load': [
        { faculty: 'Dr. Maria Santos', department: 'IT', subjects: 4, sections: 6, units: 24, maxUnits: 28, status: 'Full' },
        { faculty: 'Mr. John Dela Cruz', department: 'IT', subjects: 3, sections: 5, units: 18, maxUnits: 28, status: 'Normal' },
        { faculty: 'Dr. Elizabeth Reyes', department: 'IT', subjects: 4, sections: 5, units: 21, maxUnits: 28, status: 'Normal' }
    ],
    'student-academic': [
        { id: 'IT-001', name: 'Maria Santos', program: 'BS-IT', level: '1st Year', gpa: 3.85, status: 'Regular', subjects: 4 },
        { id: 'IT-002', name: 'John Dela Cruz', program: 'BS-IT', level: '1st Year', gpa: 2.45, status: 'Probation', subjects: 4 },
        { id: 'IT-003', name: 'Elizabeth Reyes', program: 'BS-IT', level: '1st Year', gpa: 3.65, status: 'Regular', subjects: 4 }
    ]
};

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    initializeReports();
});

function initializeReports() {
    // Add click listeners to report category cards
    const cards = document.querySelectorAll('.report-category-card');
    cards.forEach(card => {
        card.addEventListener('click', function() {
            selectReport(this.getAttribute('data-report'));
        });
    });
}

function selectReport(reportType) {
    currentReport = reportType;
    
    // Hide categories, show filters and results
    document.getElementById('filterSection').style.display = 'block';
    document.getElementById('resultsSection').style.display = 'block';
    
    // Scroll to filter section
    setTimeout(() => {
        document.getElementById('filterSection').scrollIntoView({ behavior: 'smooth' });
    }, 100);
    
    // Auto-generate report
    generateReport();
    
    showToast(`${reportType.replace('-', ' ').toUpperCase()} selected`, 'info');
}

function generateReport() {
    if (!currentReport) {
        showToast('Please select a report type', 'warning');
        return;
    }

    const data = reportData[currentReport];
    
    if (!data) {
        showToast('Report data not found', 'error');
        return;
    }

    // Generate headers based on report type
    const headers = getReportHeaders(currentReport);
    
    // Populate table header
    const thead = document.getElementById('reportTableHead');
    thead.innerHTML = '';
    headers.forEach(header => {
        const th = document.createElement('th');
        th.textContent = header;
        thead.appendChild(th);
    });

    // Populate table body
    const tbody = document.getElementById('reportTableBody');
    tbody.innerHTML = '';
    
    data.forEach(row => {
        const tr = document.createElement('tr');
        headers.forEach(header => {
            const td = document.createElement('td');
            const key = header.toLowerCase().replace(/ /g, '');
            const value = getRowValue(row, header, currentReport);
            td.textContent = value;
            tr.appendChild(td);
        });
        tbody.appendChild(tr);
    });

    // Update info badge and summary
    document.getElementById('reportInfoBadge').textContent = `${currentReport.replace('-', ' ').toUpperCase()} - ${data.length} records`;
    document.getElementById('totalRecords').textContent = data.length;
    document.getElementById('generatedTime').textContent = new Date().toLocaleString();

    showToast('Report generated successfully', 'success');
}

function getReportHeaders(reportType) {
    switch(reportType) {
        case 'programs':
            return ['Program Code', 'Program Name', 'Director', 'Students', 'Total Units', 'Status'];
        case 'subjects':
            return ['Subject Code', 'Subject Name', 'Units', 'Program', 'Faculty', 'Sections'];
        case 'sections':
            return ['Section Name', 'Program', 'Year Level', 'Academic Year', 'Students', 'Capacity', 'Adviser'];
        case 'class-schedule':
            return ['Subject', 'Section', 'Faculty', 'Day', 'Time', 'Room', 'Students'];
        case 'faculty-load':
            return ['Faculty', 'Department', 'Subjects', 'Sections', 'Units', 'Max Units', 'Status'];
        case 'student-academic':
            return ['Student ID', 'Student Name', 'Program', 'Year Level', 'GPA', 'Academic Status', 'Subjects'];
        default:
            return [];
    }
}

function getRowValue(row, header, reportType) {
    const headerKey = header.toLowerCase().replace(/ /g, '');
    
    // Map header names to row properties
    const keyMappings = {
        programcode: 'code',
        programname: 'name',
        // ... add more mappings as needed
    };
    
    let key = keyMappings[headerKey] || headerKey;
    
    // Handle special cases
    if (header === 'Program Code') return row.code;
    if (header === 'Program Name') return row.name;
    if (header === 'Director') return row.director;
    if (header === 'Students') return row.students;
    if (header === 'Total Units') return row.units;
    if (header === 'Status') return row.status;
    if (header === 'Subject Code') return row.code;
    if (header === 'Subject Name') return row.name;
    if (header === 'Units') return row.units;
    if (header === 'Program') return row.program;
    if (header === 'Faculty') return row.faculty;
    if (header === 'Sections') return row.sections;
    if (header === 'Section Name') return row.name;
    if (header === 'Year Level') return row.level;
    if (header === 'Academic Year') return row.year;
    if (header === 'Capacity') return row.capacity;
    if (header === 'Adviser') return row.adviser;
    if (header === 'Subject') return row.subject;
    if (header === 'Section') return row.section;
    if (header === 'Day') return row.day;
    if (header === 'Time') return row.time;
    if (header === 'Room') return row.room;
    if (header === 'Department') return row.department;
    if (header === 'Max Units') return row.maxUnits;
    if (header === 'Student ID') return row.id;
    if (header === 'Student Name') return row.name;
    if (header === 'GPA') return row.gpa ? row.gpa.toFixed(2) : '-';
    if (header === 'Academic Status') return row.status;
    if (header === 'Subjects') return row.subjects;
    
    return row[key] || '-';
}

function resetFilters() {
    document.getElementById('reportAcademicYear').value = '2025-2026';
    document.getElementById('reportSemester').value = '1st Semester';
    document.getElementById('reportProgram').value = '';
    document.getElementById('reportSection').value = '';
    document.getElementById('reportFaculty').value = '';
    
    showToast('Filters reset', 'info');
}

function exportPDF() {
    if (!currentReport) {
        showToast('Please generate a report first', 'warning');
        return;
    }
    
    // Simple PDF export simulation
    const table = document.getElementById('reportTable');
    const heading = currentReport.replace('-', ' ').toUpperCase();
    const timestamp = new Date().toLocaleString();
    
    let pdfContent = `${heading} Report\n`;
    pdfContent += `Generated: ${timestamp}\n\n`;
    
    // Add table content
    const rows = table.querySelectorAll('tr');
    rows.forEach(row => {
        const cells = row.querySelectorAll('td, th');
        let rowContent = '';
        cells.forEach(cell => {
            rowContent += cell.textContent.padEnd(20);
        });
        pdfContent += rowContent + '\n';
    });
    
    showToast('PDF export functionality ready for backend integration', 'success');
}

function exportExcel() {
    if (!currentReport) {
        showToast('Please generate a report first', 'warning');
        return;
    }
    
    // Simple Excel export simulation
    const table = document.getElementById('reportTable');
    let csvContent = 'data:text/csv;charset=utf-8,';
    
    const rows = table.querySelectorAll('tr');
    rows.forEach(row => {
        const cells = row.querySelectorAll('td, th');
        const rowContent = Array.from(cells)
            .map(cell => '"' + cell.textContent.replace(/"/g, '""') + '"')
            .join(',');
        csvContent += rowContent + '\n';
    });
    
    const link = document.createElement('a');
    link.setAttribute('href', encodeURI(csvContent));
    link.setAttribute('download', `${currentReport}-report-${new Date().getTime()}.csv`);
    link.click();
    
    showToast('Excel file downloaded successfully', 'success');
}

function printReport() {
    if (!currentReport) {
        showToast('Please generate a report first', 'warning');
        return;
    }
    
    const table = document.getElementById('reportTable');
    const heading = currentReport.replace('-', ' ').toUpperCase();
    
    let printWindow = window.open('', '', 'height=600,width=800');
    printWindow.document.write('<html><head><title>Print Report</title>');
    printWindow.document.write('<style>');
    printWindow.document.write('body { font-family: Arial, sans-serif; padding: 20px; }');
    printWindow.document.write('h2 { color: #1e3a8a; }');
    printWindow.document.write('table { width: 100%; border-collapse: collapse; margin-top: 20px; }');
    printWindow.document.write('th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }');
    printWindow.document.write('th { background-color: #1e3a8a; color: white; font-weight: bold; }');
    printWindow.document.write('tr:nth-child(even) { background-color: #f9fafb; }');
    printWindow.document.write('</style></head><body>');
    
    printWindow.document.write(`<h2>${heading} Report</h2>`);
    printWindow.document.write(`<p>Generated: ${new Date().toLocaleString()}</p>`);
    printWindow.document.write(table.outerHTML);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    
    setTimeout(() => {
        printWindow.print();
    }, 250);
    
    showToast('Print preview opened', 'success');
}

function showToast(message, type = 'info') {
    const toast = document.getElementById('toastNotification');
    toast.innerHTML = `
        <div class="toast-message">${message}</div>
    `;
    toast.className = `toast ${type} active`;
    
    // Auto-hide after 4 seconds
    setTimeout(() => {
        toast.classList.remove('active');
    }, 4000);
}

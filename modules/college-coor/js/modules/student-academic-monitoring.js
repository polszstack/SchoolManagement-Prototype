// =====================================================
// STUDENT ACADEMIC MONITORING MODULE - JAVASCRIPT
// =====================================================

// Sample student academic data
let studentAcademicData = [
    {
        id: 'IT-001',
        name: 'Maria Santos',
        program: 'BS-IT',
        yearLevel: '1st Year',
        section: 'IT-1A',
        gpa: 3.85,
        status: 'Regular',
        subjects: [
            { code: 'IT-101', name: 'Introduction to Programming', units: 3, midterm: 92, final: 90, remarks: 'Passed' },
            { code: 'IT-102', name: 'Data Structures', units: 3, midterm: 88, final: 86, remarks: 'Passed' },
            { code: 'GEN-101', name: 'General Education 1', units: 3, midterm: 85, final: 88, remarks: 'Passed' },
            { code: 'MATH-101', name: 'Calculus I', units: 4, midterm: 90, final: 92, remarks: 'Passed' }
        ]
    },
    {
        id: 'IT-002',
        name: 'John Dela Cruz',
        program: 'BS-IT',
        yearLevel: '1st Year',
        section: 'IT-1A',
        gpa: 2.45,
        status: 'Probation',
        subjects: [
            { code: 'IT-101', name: 'Introduction to Programming', units: 3, midterm: 65, final: 70, remarks: 'Passed' },
            { code: 'IT-102', name: 'Data Structures', units: 3, midterm: 58, final: 62, remarks: 'Passed' },
            { code: 'GEN-101', name: 'General Education 1', units: 3, midterm: 72, final: 75, remarks: 'Passed' },
            { code: 'MATH-101', name: 'Calculus I', units: 4, midterm: 50, final: 55, remarks: 'Failed' }
        ]
    },
    {
        id: 'IT-003',
        name: 'Elizabeth Reyes',
        program: 'BS-IT',
        yearLevel: '1st Year',
        section: 'IT-1A',
        gpa: 3.65,
        status: 'Regular',
        subjects: [
            { code: 'IT-101', name: 'Introduction to Programming', units: 3, midterm: 88, final: 90, remarks: 'Passed' },
            { code: 'IT-102', name: 'Data Structures', units: 3, midterm: 85, final: 87, remarks: 'Passed' },
            { code: 'GEN-101', name: 'General Education 1', units: 3, midterm: 82, final: 84, remarks: 'Passed' },
            { code: 'MATH-101', name: 'Calculus I', units: 4, midterm: 86, final: 89, remarks: 'Passed' }
        ]
    },
    {
        id: 'IT-004',
        name: 'Ramon Garcia',
        program: 'BS-IT',
        yearLevel: '1st Year',
        section: 'IT-1A',
        gpa: 1.85,
        status: 'At Risk',
        subjects: [
            { code: 'IT-101', name: 'Introduction to Programming', units: 3, midterm: 45, final: 48, remarks: 'Failed' },
            { code: 'IT-102', name: 'Data Structures', units: 3, midterm: 52, final: 55, remarks: 'Failed' },
            { code: 'GEN-101', name: 'General Education 1', units: 3, midterm: 60, final: 62, remarks: 'Passed' },
            { code: 'MATH-101', name: 'Calculus I', units: 4, midterm: 48, final: 50, remarks: 'Failed' }
        ]
    },
    {
        id: 'IT-005',
        name: 'Anna Rodriguez',
        program: 'BS-IT',
        yearLevel: '1st Year',
        section: 'IT-1A',
        gpa: 3.92,
        status: 'Regular',
        subjects: [
            { code: 'IT-101', name: 'Introduction to Programming', units: 3, midterm: 95, final: 94, remarks: 'Passed' },
            { code: 'IT-102', name: 'Data Structures', units: 3, midterm: 92, final: 91, remarks: 'Passed' },
            { code: 'GEN-101', name: 'General Education 1', units: 3, midterm: 90, final: 92, remarks: 'Passed' },
            { code: 'MATH-101', name: 'Calculus I', units: 4, midterm: 94, final: 96, remarks: 'Passed' }
        ]
    },
    {
        id: 'IT-006',
        name: 'Miguel Fernandez',
        program: 'BS-IT',
        yearLevel: '1st Year',
        section: 'IT-1B',
        gpa: 3.25,
        status: 'Regular',
        subjects: [
            { code: 'IT-101', name: 'Introduction to Programming', units: 3, midterm: 82, final: 84, remarks: 'Passed' },
            { code: 'IT-102', name: 'Data Structures', units: 3, midterm: 79, final: 81, remarks: 'Passed' },
            { code: 'GEN-101', name: 'General Education 1', units: 3, midterm: 78, final: 80, remarks: 'Passed' },
            { code: 'MATH-101', name: 'Calculus I', units: 4, midterm: 76, final: 78, remarks: 'Passed' }
        ]
    },
    {
        id: 'IT-007',
        name: 'Rosa Mercado',
        program: 'BS-IT',
        yearLevel: '1st Year',
        section: 'IT-1B',
        gpa: 2.15,
        status: 'Probation',
        subjects: [
            { code: 'IT-101', name: 'Introduction to Programming', units: 3, midterm: 68, final: 70, remarks: 'Passed' },
            { code: 'IT-102', name: 'Data Structures', units: 3, midterm: 60, final: 65, remarks: 'Passed' },
            { code: 'GEN-101', name: 'General Education 1', units: 3, midterm: 70, final: 72, remarks: 'Passed' },
            { code: 'MATH-101', name: 'Calculus I', units: 4, midterm: 55, final: 58, remarks: 'Failed' }
        ]
    },
    {
        id: 'IT-008',
        name: 'Carlos Lopez',
        program: 'BS-IT',
        yearLevel: '1st Year',
        section: 'IT-1B',
        gpa: 1.95,
        status: 'At Risk',
        subjects: [
            { code: 'IT-101', name: 'Introduction to Programming', units: 3, midterm: 50, final: 52, remarks: 'Failed' },
            { code: 'IT-102', name: 'Data Structures', units: 3, midterm: 55, final: 58, remarks: 'Failed' },
            { code: 'GEN-101', name: 'General Education 1', units: 3, midterm: 65, final: 67, remarks: 'Passed' },
            { code: 'MATH-101', name: 'Calculus I', units: 4, midterm: 52, final: 54, remarks: 'Failed' }
        ]
    }
];

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    initializeStudentMonitoring();
});

function initializeStudentMonitoring() {
    // Populate monitoring table
    populateMonitoringTable();
    
    // Add event listeners for filters
    document.getElementById('academicYearFilter').addEventListener('change', handleFilterChange);
    document.getElementById('semesterFilter').addEventListener('change', handleFilterChange);
    document.getElementById('programFilter').addEventListener('change', handleFilterChange);
    document.getElementById('yearLevelFilter').addEventListener('change', handleFilterChange);
    document.getElementById('sectionFilter').addEventListener('change', handleFilterChange);
    document.getElementById('studentSearch').addEventListener('input', handleFilterChange);
    
    // Modal close button
    const modal = document.getElementById('studentDetailsModal');
    const closeBtn = modal.querySelector('.modal-close');
    closeBtn.addEventListener('click', () => {
        modal.classList.remove('active');
    });
    
    // Close modal on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            modal.classList.remove('active');
        }
    });
    
    // Close modal on backdrop click
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('active');
        }
    });
    
    // Update statistics
    updateStatistics();
}

function handleFilterChange() {
    populateMonitoringTable();
    updateStatistics();
}

function getFilteredStudents() {
    const program = document.getElementById('programFilter').value;
    const yearLevel = document.getElementById('yearLevelFilter').value;
    const section = document.getElementById('sectionFilter').value;
    const searchTerm = document.getElementById('studentSearch').value.toLowerCase();
    
    return studentAcademicData.filter(student => {
        const matchProgram = !program || student.program === program;
        const matchYear = !yearLevel || student.yearLevel === yearLevel;
        const matchSection = !section || student.section === section;
        const matchSearch = !searchTerm || 
                          student.id.toLowerCase().includes(searchTerm) ||
                          student.name.toLowerCase().includes(searchTerm);
        
        return matchProgram && matchYear && matchSection && matchSearch;
    });
}

function populateMonitoringTable() {
    const tbody = document.getElementById('monitoringTableBody');
    const filteredStudents = getFilteredStudents();
    
    tbody.innerHTML = '';
    
    filteredStudents.forEach(student => {
        const row = document.createElement('tr');
        
        const gpaClass = student.gpa >= 3.5 ? 'high' : student.gpa >= 2.5 ? 'medium' : 'low';
        const statusClass = student.status.toLowerCase().replace(' ', '-');
        
        row.innerHTML = `
            <td><strong>${student.id}</strong></td>
            <td>${student.name}</td>
            <td>${student.program}</td>
            <td>${student.yearLevel}</td>
            <td>${student.section}</td>
            <td>
                <div class="gpa-value ${gpaClass}">${student.gpa.toFixed(2)}</div>
            </td>
            <td>
                <span class="status-badge ${statusClass}">${student.status}</span>
            </td>
            <td>
                <button class="action-btn" onclick="viewStudentDetails('${student.id}')">View Details</button>
            </td>
        `;
        
        tbody.appendChild(row);
    });
    
    // Update total records count
    document.getElementById('totalRecordsCount').textContent = filteredStudents.length;
}

function viewStudentDetails(studentId) {
    const student = studentAcademicData.find(s => s.id === studentId);
    if (!student) return;
    
    // Populate student information
    document.getElementById('detailStudentId').textContent = student.id;
    document.getElementById('detailStudentName').textContent = student.name;
    document.getElementById('detailProgram').textContent = student.program;
    document.getElementById('detailYearLevel').textContent = student.yearLevel;
    document.getElementById('detailSection').textContent = student.section;
    
    // Set academic status with color
    const statusElement = document.getElementById('detailAcademicStatus');
    statusElement.textContent = student.status;
    statusElement.className = `status-badge ${student.status.toLowerCase().replace(' ', '-')}`;
    
    // Populate academic summary
    document.getElementById('detailCurrentGPA').textContent = student.gpa.toFixed(2);
    
    const totalUnits = student.subjects.reduce((sum, subject) => sum + subject.units, 0);
    document.getElementById('detailTotalUnits').textContent = totalUnits;
    
    const unitsPassed = student.subjects
        .filter(subject => subject.remarks === 'Passed')
        .reduce((sum, subject) => sum + subject.units, 0);
    document.getElementById('detailUnitsPassed').textContent = unitsPassed;
    
    const subjectsFailed = student.subjects.filter(subject => subject.remarks === 'Failed').length;
    document.getElementById('detailSubjectsFailed').textContent = subjectsFailed;
    
    // Populate grades table
    const gradesBody = document.getElementById('gradesTableBody');
    gradesBody.innerHTML = '';
    
    student.subjects.forEach(subject => {
        const row = document.createElement('tr');
        const remarksClass = subject.remarks === 'Passed' ? 'remarks-passed' : 'remarks-failed';
        const gradeClass = subject.remarks === 'Passed' ? 'passed' : 'failed';
        
        row.innerHTML = `
            <td><strong>${subject.code}</strong></td>
            <td>${subject.name}</td>
            <td><span class="gpa-value">${subject.units}</span></td>
            <td><span class="grade-value ${gradeClass}">${subject.midterm}</span></td>
            <td><span class="grade-value ${gradeClass}">${subject.final}</span></td>
            <td><span class="remarks-cell ${remarksClass}">${subject.remarks}</span></td>
        `;
        
        gradesBody.appendChild(row);
    });
    
    // Populate status information
    const statusInfoBox = document.getElementById('statusInfoBox');
    const statusClass = student.status.toLowerCase().replace(' ', '-');
    statusInfoBox.className = `status-info-box ${statusClass}`;
    
    let statusMessage = '';
    
    if (student.status === 'Regular') {
        statusMessage = `
            <div class="status-info-text">
                <strong>✓ Good Academic Standing</strong>
                This student is in good academic standing with a GPA of ${student.gpa.toFixed(2)} and no failed subjects. 
                Continue to maintain academic excellence and following the degree requirements.
            </div>
        `;
    } else if (student.status === 'Probation') {
        statusMessage = `
            <div class="status-info-text">
                <strong>⚠ Academic Probation</strong>
                This student's GPA (${student.gpa.toFixed(2)}) is below the minimum required. They are on academic probation 
                and must improve their grades next semester. Academic advising is recommended.
            </div>
        `;
    } else if (student.status === 'At Risk') {
        statusMessage = `
            <div class="status-info-text">
                <strong>✕ At Risk - Academic Intervention Required</strong>
                This student's academic performance is critically low (GPA: ${student.gpa.toFixed(2)}, ${subjectsFailed} failed subjects). 
                Immediate intervention, tutoring, and counseling are strongly recommended to prevent academic dismissal.
            </div>
        `;
    }
    
    statusInfoBox.innerHTML = statusMessage;
    
    // Open modal
    document.getElementById('studentDetailsModal').classList.add('active');
}

function updateStatistics() {
    const filteredStudents = getFilteredStudents();
    
    const regularCount = filteredStudents.filter(s => s.status === 'Regular').length;
    const probationCount = filteredStudents.filter(s => s.status === 'Probation').length;
    const atRiskCount = filteredStudents.filter(s => s.status === 'At Risk').length;
    
    document.getElementById('totalStudentsCount').textContent = filteredStudents.length;
    document.getElementById('regularCount').textContent = regularCount;
    document.getElementById('probationCount').textContent = probationCount;
    document.getElementById('atRiskCount').textContent = atRiskCount;
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

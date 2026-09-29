// Faculty Load Monitoring Dashboard JavaScript

let facultyLoadData = [
    {
        name: 'Dr. Maria Santos',
        department: 'College of Engineering',
        subjectsCount: 3,
        sections: 5,
        totalUnits: 18,
        maxUnits: 24,
        status: 'Normal',
        courses: [
            { code: 'IT-101', name: 'Introduction to Programming', section: 'IT-1A', units: 3, schedule: 'MWF 8:00-9:00 AM' },
            { code: 'IT-102', name: 'Data Structures', section: 'IT-1B', units: 3, schedule: 'TTh 8:00-9:30 AM' },
            { code: 'IT-201', name: 'Database Management', section: 'IT-3A, IT-3B, IT-3C', units: 3, schedule: 'MWF 2:00-3:00 PM' }
        ]
    },
    {
        name: 'Mr. John Dela Cruz',
        department: 'College of Engineering',
        subjectsCount: 4,
        sections: 6,
        totalUnits: 23,
        maxUnits: 24,
        status: 'Full',
        courses: [
            { code: 'IT-103', name: 'Web Development', section: 'IT-1A, IT-1B', units: 3, schedule: 'MWF 9:00-10:00 AM' },
            { code: 'IT-104', name: 'Software Engineering', section: 'IT-2A', units: 4, schedule: 'TTh 9:00-10:30 AM' },
            { code: 'IT-301', name: 'Mobile Development', section: 'IT-3A, IT-3B', units: 4, schedule: 'MWF 1:00-2:00 PM' },
            { code: 'IT-401', name: 'Capstone Project', section: 'IT-4A', units: 6, schedule: 'W 3:00-5:00 PM' }
        ]
    },
    {
        name: 'Dr. Elizabeth Reyes',
        department: 'College of Education',
        subjectsCount: 2,
        sections: 3,
        totalUnits: 12,
        maxUnits: 24,
        status: 'Underload',
        courses: [
            { code: 'ED-101', name: 'Educational Psychology', section: 'ED-2A', units: 4, schedule: 'MWF 9:00-10:00 AM' },
            { code: 'ED-102', name: 'Curriculum Development', section: 'ED-2A, ED-2B', units: 4, schedule: 'TTh 10:00-11:30 AM' }
        ]
    },
    {
        name: 'Mr. Ramon Garcia',
        department: 'College of Business',
        subjectsCount: 5,
        sections: 8,
        totalUnits: 28,
        maxUnits: 24,
        status: 'Overload',
        courses: [
            { code: 'ACCT-101', name: 'Accounting Fundamentals', section: 'ACCT-1A, ACCT-1B', units: 3, schedule: 'MWF 10:00-11:00 AM' },
            { code: 'ACCT-102', name: 'Financial Accounting', section: 'ACCT-1A', units: 3, schedule: 'TTh 10:00-11:30 AM' },
            { code: 'ACCT-201', name: 'Managerial Accounting', section: 'ACCT-2A, ACCT-2B', units: 4, schedule: 'MWF 1:00-2:00 PM' },
            { code: 'BUS-101', name: 'Business Management', section: 'BUS-1A', units: 4, schedule: 'TTh 1:00-2:30 PM' },
            { code: 'BUS-301', name: 'Strategic Planning', section: 'BUS-3A, BUS-3B', units: 6, schedule: 'W 3:00-5:00 PM' }
        ]
    },
    {
        name: 'Ms. Anna Rodriguez',
        department: 'College of Science',
        subjectsCount: 3,
        sections: 4,
        totalUnits: 19,
        maxUnits: 24,
        status: 'Normal',
        courses: [
            { code: 'BIO-101', name: 'General Biology', section: 'BIO-1A', units: 4, schedule: 'MWF 8:00-9:00 AM' },
            { code: 'BIO-102', name: 'Biology Laboratory', section: 'BIO-1A', units: 2, schedule: 'M 1:00-3:00 PM' },
            { code: 'BIO-201', name: 'Cellular Biology', section: 'BIO-2A, BIO-2B', units: 4, schedule: 'MWF 10:00-11:00 AM' }
        ]
    },
    {
        name: 'Dr. Michael Thompson',
        department: 'College of Engineering',
        subjectsCount: 4,
        sections: 7,
        totalUnits: 25,
        maxUnits: 24,
        status: 'Overload',
        courses: [
            { code: 'ENG-101', name: 'Fundamentals of Engineering', section: 'ENG-1A', units: 3, schedule: 'MWF 9:00-10:00 AM' },
            { code: 'ENG-201', name: 'Engineering Design', section: 'ENG-2A, ENG-2B', units: 4, schedule: 'TTh 9:00-10:30 AM' },
            { code: 'ENG-301', name: 'Advanced Engineering', section: 'ENG-3A, ENG-3B', units: 4, schedule: 'MWF 2:00-3:00 PM' },
            { code: 'ENG-401', name: 'Capstone Design', section: 'ENG-4A', units: 6, schedule: 'F 1:00-5:00 PM' }
        ]
    }
];

// Toast Notification Function
function showToast(message, type = 'success') {
    const toast = document.getElementById('toastNotification');
    toast.textContent = message;
    toast.className = `toast ${type}`;
    toast.style.display = 'block';
    setTimeout(() => {
        toast.classList.add('show');
    }, 10);
    setTimeout(() => {
        toast.style.display = 'none';
        toast.classList.remove('show');
    }, 3000);
}

// Update Total Faculty Count
function updateFacultyCount() {
    const visibleRows = document.querySelectorAll('#facultyLoadTableBody tr:not([style*="display: none"])');
    document.getElementById('totalFacultyCount').textContent = visibleRows.length;
}

// Initialize Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    // Filter by Academic Year
    const academicYearFilter = document.getElementById('academicYearFilter');
    if (academicYearFilter) {
        academicYearFilter.addEventListener('change', filterTable);
    }

    // Filter by Semester
    const semesterFilter = document.getElementById('semesterFilter');
    if (semesterFilter) {
        semesterFilter.addEventListener('change', filterTable);
    }

    // Filter by Department
    const departmentFilter = document.getElementById('departmentFilter');
    if (departmentFilter) {
        departmentFilter.addEventListener('change', filterTable);
    }

    // Search Faculty
    const facultySearch = document.getElementById('facultySearch');
    if (facultySearch) {
        facultySearch.addEventListener('keyup', filterTable);
    }

    // Modal Close Buttons
    document.querySelectorAll('.modal-close').forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.modal').classList.remove('active');
        });
    });

    // Close modal when clicking outside
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });
    });

    // ESC key to close modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal.active').forEach(modal => {
                modal.classList.remove('active');
            });
        }
    });

    // Initialize table
    updateFacultyCount();
});

// Filter Table Function
function filterTable() {
    const searchTerm = document.getElementById('facultySearch').value.toLowerCase();
    const departmentFilter = document.getElementById('departmentFilter').value.toLowerCase();
    
    const rows = document.querySelectorAll('#facultyLoadTableBody tr');
    
    rows.forEach(row => {
        const name = row.cells[0].textContent.toLowerCase();
        const department = row.cells[1].textContent.toLowerCase();
        
        const matchesSearch = name.includes(searchTerm);
        const matchesDepartment = departmentFilter === '' || department.includes(departmentFilter);
        
        row.style.display = (matchesSearch && matchesDepartment) ? '' : 'none';
    });

    updateFacultyCount();
}

// View Faculty Load Details
function viewFacultyDetails(facultyName) {
    const faculty = facultyLoadData.find(f => f.name === facultyName);
    
    if (!faculty) {
        showToast('Faculty not found', 'error');
        return;
    }

    // Set title
    document.getElementById('facultyDetailsTitle').textContent = `${faculty.name} - Load Details`;

    // Calculate load status color
    const loadPercentage = (faculty.totalUnits / faculty.maxUnits) * 100;
    let loadColor = '#10b981'; // Normal
    if (loadPercentage < 50) {
        loadColor = '#0ea5e9'; // Underload
    } else if (loadPercentage > 100) {
        loadColor = '#ef4444'; // Overload
    } else if (loadPercentage >= 87.5) {
        loadColor = '#f59e0b'; // Full
    }

    // Build content
    let content = `
        <div class="faculty-load-summary">
            <div class="summary-item">
                <span class="summary-label">Total Units</span>
                <span class="summary-value">${faculty.totalUnits}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Maximum Units</span>
                <span class="summary-value">${faculty.maxUnits}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Remaining Units</span>
                <span class="summary-value">${faculty.maxUnits - faculty.totalUnits}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Load Status</span>
                <span class="badge badge-${faculty.status.toLowerCase()}">${faculty.status}</span>
            </div>
        </div>

        <div class="summary-item" style="margin-bottom: 1.5rem;">
            <span class="summary-label">Load Percentage</span>
            <div class="load-progress">
                <div class="load-progress-bar" style="width: ${Math.min(loadPercentage, 100)}%; background: ${loadColor};">
                    ${Math.round(loadPercentage)}%
                </div>
            </div>
        </div>

        <h4 style="font-size: 1.1rem; font-weight: 700; color: #1f2937; margin: 1.5rem 0 1rem 0; border-bottom: 2px solid #e5e7eb; padding-bottom: 0.5rem;">Courses and Subjects</h4>
        
        <table class="faculty-details-table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Subject Name</th>
                    <th>Section</th>
                    <th>Units</th>
                    <th>Schedule</th>
                </tr>
            </thead>
            <tbody>
    `;

    faculty.courses.forEach(course => {
        content += `
                <tr>
                    <td><strong>${course.code}</strong></td>
                    <td>${course.name}</td>
                    <td>${course.section}</td>
                    <td><span class="badge badge-normal">${course.units}</span></td>
                    <td>${course.schedule}</td>
                </tr>
        `;
    });

    content += `
            </tbody>
        </table>

        <div style="background: #f0f7ff; padding: 1rem; border-radius: 8px; border-left: 4px solid #1e3a8a; margin-top: 1.5rem;">
            <h4 style="margin: 0 0 0.5rem 0; color: #1e3a8a;">Load Information</h4>
            <p style="margin: 0.25rem 0; font-size: 0.9rem; color: #6b7280;">
                <strong>Teaching Workload:</strong> ${faculty.subjectsCount} subject(s) across ${faculty.sections} section(s)
            </p>
            <p style="margin: 0.25rem 0; font-size: 0.9rem; color: #6b7280;">
                <strong>Department:</strong> ${faculty.department}
            </p>
            ${faculty.status === 'Overload' ? `
            <p style="margin: 0.25rem 0; font-size: 0.9rem; color: #ef4444; font-weight: 600;">
                ⚠️ Faculty is currently carrying more than the maximum workload.
            </p>
            ` : ''}
            ${faculty.status === 'Underload' ? `
            <p style="margin: 0.25rem 0; font-size: 0.9rem; color: #0ea5e9; font-weight: 600;">
                ℹ️ Faculty has capacity for additional courses.
            </p>
            ` : ''}
        </div>
    `;

    document.getElementById('facultyDetailsContent').innerHTML = content;
    document.getElementById('facultyDetailsModal').classList.add('active');
}

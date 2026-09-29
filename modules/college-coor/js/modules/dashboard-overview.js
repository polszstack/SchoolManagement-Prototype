// Dashboard Overview - initializes charts and populates data

// Sample data for dashboard
const dashboardData = {
    summary: {
        programs: 8,
        students: 1250,
        faculty: 62,
        sections: 48,
        subjects: 156,
        pendingAppointments: 23
    },
    
    studentsPerProgram: {
        labels: ['BS-IT', 'BS-CS', 'BS-EC', 'BS-EN', 'BS-BIO', 'BS-CHEM', 'BS-COM', 'AS-GED'],
        data: [185, 156, 142, 168, 124, 98, 145, 232]
    },
    
    facultyLoad: {
        labels: ['Fully Loaded', 'Underloaded', 'Overloaded'],
        data: [42, 12, 8],
        colors: ['#10b981', '#f59e0b', '#ef4444']
    },
    
    studentStatus: {
        labels: ['Good Standing', 'At Risk', 'Probation'],
        data: [945, 215, 90],
        colors: ['#10b981', '#f59e0b', '#ef4444']
    },
    
    upcomingAppointments: [
        {
            id: 'APT-001',
            studentName: 'Maria Santos Garcia',
            adviser: 'Dr. Maria Santos',
            date: '2026-03-16',
            time: '10:00 AM',
            purpose: 'Course Planning'
        },
        {
            id: 'APT-002',
            studentName: 'Juan Carlos Reyes',
            adviser: 'Prof. Juan Reyes',
            date: '2026-03-16',
            time: '2:00 PM',
            purpose: 'Academic Progress Review'
        },
        {
            id: 'APT-003',
            studentName: 'Ana Maria Cruz',
            adviser: 'Dr. Maria Santos',
            date: '2026-03-17',
            time: '9:30 AM',
            purpose: 'Major Declaration'
        },
        {
            id: 'APT-004',
            studentName: 'Miguel Fernando Lopez',
            adviser: 'Prof. Ramon Miguel',
            date: '2026-03-17',
            time: '3:00 PM',
            purpose: 'GPA Improvement Plan'
        },
        {
            id: 'APT-005',
            studentName: 'Rosa Isabel Ocampo',
            adviser: 'Dr. Rita Ocampo',
            date: '2026-03-18',
            time: '11:00 AM',
            purpose: 'Thesis Work Discussion'
        }
    ],
    
    upcomingEvents: [
        {
            id: 'EVT-001',
            title: 'Enrollment Period Starts',
            date: '2026-03-16',
            description: 'Spring semester enrollment period begins for all students'
        },
        {
            id: 'EVT-002',
            title: 'Midterm Exams',
            date: '2026-03-30',
            description: 'Midterm examinations for all courses commence'
        },
        {
            id: 'EVT-003',
            title: 'Faculty Meeting',
            date: '2026-04-05',
            description: 'Monthly faculty meeting at Conference Room A'
        },
        {
            id: 'EVT-004',
            title: 'Final Exams',
            date: '2026-05-11',
            description: 'Final examination period begins for spring semester'
        },
        {
            id: 'EVT-005',
            title: 'Graduation Ceremony',
            date: '2026-05-25',
            description: 'Spring semester graduation and commencement exercises'
        }
    ],
    
    recentActivities: [
        {
            type: 'student',
            icon: '👤',
            title: 'New Student Added',
            description: 'Carlos Manuel Santos enrolled in BS-IT',
            timestamp: '2 hours ago'
        },
        {
            type: 'section',
            icon: '📍',
            title: 'Section Updated',
            description: 'Section IT-3A capacity increased to 45 students',
            timestamp: '4 hours ago'
        },
        {
            type: 'faculty',
            icon: '👨‍🏫',
            title: 'Faculty Load Assigned',
            description: 'Dr. Maria Santos assigned to 4 new classes',
            timestamp: '6 hours ago'
        },
        {
            type: 'schedule',
            icon: '⏱️',
            title: 'Schedule Updated',
            description: 'Math 101 class moved from MWF to TTh schedule',
            timestamp: '8 hours ago'
        },
        {
            type: 'section',
            icon: '📍',
            title: 'Section Created',
            description: 'New section CS-2B created for Spring semester',
            timestamp: '1 day ago'
        },
        {
            type: 'student',
            icon: '👤',
            title: 'Student Status Updated',
            description: 'Maria Santos promoted to Dean\'s List',
            timestamp: '2 days ago'
        }
    ]
};

// Store for Chart instances
const chartInstances = {};

// Initialize dashboard on page load
document.addEventListener('DOMContentLoaded', function() {
    populateSummaryCards();
    initializeCharts();
    populateUpcomingAppointments();
    populateUpcomingEvents();
    populateRecentActivities();
    attachEventListeners();
});

/**
 * Populate the top summary cards with data
 */
function populateSummaryCards() {
    document.getElementById('totalPrograms').textContent = dashboardData.summary.programs;
    document.getElementById('totalStudents').textContent = dashboardData.summary.students.toLocaleString();
    document.getElementById('totalFaculty').textContent = dashboardData.summary.faculty;
    document.getElementById('totalSections').textContent = dashboardData.summary.sections;
    document.getElementById('totalSubjects').textContent = dashboardData.summary.subjects;
    document.getElementById('pendingAppointments').textContent = dashboardData.summary.pendingAppointments;
}

/**
 * Initialize all three charts
 */
function initializeCharts() {
    // Wait for Chart.js to be available
    if (typeof Chart === 'undefined') {
        setTimeout(initializeCharts, 100);
        return;
    }
    
    initStudentsPerProgramChart();
    initFacultyLoadChart();
    initStudentStatusChart();
}

/**
 * Chart 1: Students per Program (Bar Chart)
 */
function initStudentsPerProgramChart() {
    const ctx = document.getElementById('studentsPerProgramChart');
    if (!ctx) return;
    
    const ctxElement = ctx.getContext('2d');
    
    // Create gradient for bars
    const gradient = ctxElement.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, '#3b82f6');
    gradient.addColorStop(1, '#1e40af');
    
    chartInstances.studentsPerProgram = new Chart(ctxElement, {
        type: 'bar',
        data: {
            labels: dashboardData.studentsPerProgram.labels,
            datasets: [{
                label: 'Number of Students',
                data: dashboardData.studentsPerProgram.data,
                backgroundColor: gradient,
                borderColor: '#1e3a8a',
                borderWidth: 1,
                borderRadius: 6,
                hoverBackgroundColor: '#1e3a8a',
                tension: 0.4
            }]
        },
        options: {
            indexAxis: 'x',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 10,
                    borderRadius: 6,
                    titleFont: { size: 13, weight: 'bold' },
                    bodyFont: { size: 12 },
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + ' students';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#6b7280',
                        font: { size: 12 }
                    }
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        color: '#6b7280',
                        font: { size: 11 }
                    }
                }
            }
        }
    });
}

/**
 * Chart 2: Faculty Load Distribution (Doughnut Chart)
 */
function initFacultyLoadChart() {
    const ctx = document.getElementById('facultyLoadChart');
    if (!ctx) return;
    
    const ctxElement = ctx.getContext('2d');
    
    chartInstances.facultyLoad = new Chart(ctxElement, {
        type: 'doughnut',
        data: {
            labels: dashboardData.facultyLoad.labels,
            datasets: [{
                data: dashboardData.facultyLoad.data,
                backgroundColor: dashboardData.facultyLoad.colors,
                borderColor: 'white',
                borderWidth: 3,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        padding: 15,
                        font: { size: 12 },
                        color: '#6b7280',
                        boxWidth: 15,
                        boxHeight: 15,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    borderRadius: 6,
                    titleFont: { size: 13, weight: 'bold' },
                    bodyFont: { size: 12 },
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
}

/**
 * Chart 3: Student Academic Status (Pie Chart)
 */
function initStudentStatusChart() {
    const ctx = document.getElementById('studentStatusChart');
    if (!ctx) return;
    
    const ctxElement = ctx.getContext('2d');
    
    chartInstances.studentStatus = new Chart(ctxElement, {
        type: 'pie',
        data: {
            labels: dashboardData.studentStatus.labels,
            datasets: [{
                data: dashboardData.studentStatus.data,
                backgroundColor: dashboardData.studentStatus.colors,
                borderColor: 'white',
                borderWidth: 3,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: { size: 12 },
                        color: '#6b7280',
                        boxWidth: 15,
                        boxHeight: 15,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    borderRadius: 6,
                    titleFont: { size: 13, weight: 'bold' },
                    bodyFont: { size: 12 },
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
}

/**
 * Populate upcoming appointments table
 */
function populateUpcomingAppointments() {
    const tbody = document.getElementById('upcomingAppointmentsBody');
    const emptyState = document.getElementById('appointmentsEmptyState');
    
    if (!dashboardData.upcomingAppointments || dashboardData.upcomingAppointments.length === 0) {
        emptyState.style.display = 'block';
        tbody.innerHTML = '';
        return;
    }
    
    tbody.innerHTML = dashboardData.upcomingAppointments.map(apt => `
        <tr>
            <td>${escapeHtml(apt.studentName)}</td>
            <td>${escapeHtml(apt.adviser)}</td>
            <td>${formatDate(apt.date)}</td>
            <td>${apt.time}</td>
            <td>${escapeHtml(apt.purpose)}</td>
        </tr>
    `).join('');
    
    emptyState.style.display = 'none';
}

/**
 * Populate upcoming events table
 */
function populateUpcomingEvents() {
    const tbody = document.getElementById('upcomingEventsBody');
    const emptyState = document.getElementById('eventsEmptyState');
    
    if (!dashboardData.upcomingEvents || dashboardData.upcomingEvents.length === 0) {
        emptyState.style.display = 'block';
        tbody.innerHTML = '';
        return;
    }
    
    tbody.innerHTML = dashboardData.upcomingEvents.map(event => `
        <tr>
            <td>${escapeHtml(event.title)}</td>
            <td>${formatDate(event.date)}</td>
            <td>${escapeHtml(event.description)}</td>
        </tr>
    `).join('');
    
    emptyState.style.display = 'none';
}

/**
 * Populate recent activities list
 */
function populateRecentActivities() {
    const activitiesList = document.getElementById('activitiesList');
    
    activitiesList.innerHTML = dashboardData.recentActivities.map(activity => `
        <div class="activity-item activity-type-${activity.type}">
            <div class="activity-icon">${activity.icon}</div>
            <div class="activity-content">
                <p class="activity-title">${escapeHtml(activity.title)}</p>
                <p class="activity-meta">${escapeHtml(activity.description)} • ${activity.timestamp}</p>
            </div>
        </div>
    `).join('');
}

/**
 * Attach event listeners for interactive features
 */
function attachEventListeners() {
    // Highlight cards on hover
    const cards = document.querySelectorAll('.summary-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-4px)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
}

/**
 * Utility: Format date from YYYY-MM-DD to readable format
 */
function formatDate(dateString) {
    const date = new Date(dateString + 'T00:00:00');
    const options = { month: 'short', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}

/**
 * Utility: Escape HTML to prevent XSS
 */
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

/**
 * Utility: Show toast notification
 */
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        padding: 12px 20px;
        background-color: #3b82f6;
        color: white;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 1000;
        animation: slideIn 0.3s ease;
        font-size: 14px;
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Add CSS animations for toast
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Export for testing
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        dashboardData,
        populateSummaryCards,
        initializeCharts,
        formatDate,
        escapeHtml
    };
}

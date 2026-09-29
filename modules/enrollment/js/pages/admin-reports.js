/**
 * Admin Reports Page JavaScript
 * Handles all charts and data visualization for the reports page
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Reports page initialized');
    
    // Small delay to ensure Chart.js is fully loaded
    setTimeout(function() {
        initMonthlyChart();
        initStatusChart();
        initGenderChart();
        initCivilStatusChart();
        initExportButtons();
        initDateRangePicker();
    }, 100);
});

/**
 * Monthly Applications Line Chart - FIXED VERSION
 */
function initMonthlyChart() {
    const canvas = document.getElementById('monthlyChart');
    if (!canvas) {
        console.log('Monthly chart canvas not found');
        return;
    }

    const ctx = canvas.getContext('2d');
    
    // Get monthly data from data attribute
    const monthlyDataAttr = canvas.dataset.monthly;
    const year = canvas.dataset.year || new Date().getFullYear();
    
    console.log('Monthly data attribute:', monthlyDataAttr);
    
    if (!monthlyDataAttr || monthlyDataAttr === '[]' || monthlyDataAttr === 'null') {
        showNoDataMessage(canvas, 'No monthly application data available');
        return;
    }
    
    try {
        const monthlyApplications = JSON.parse(monthlyDataAttr);
        
        // Create arrays for labels and data
        const monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 
                             'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const monthlyData = new Array(12).fill(0);
        
        // Fill in the actual data
        if (Array.isArray(monthlyApplications) && monthlyApplications.length > 0) {
            monthlyApplications.forEach(item => {
                if (item && item.month && item.count !== undefined) {
                    const monthIndex = parseInt(item.month) - 1;
                    if (monthIndex >= 0 && monthIndex < 12) {
                        monthlyData[monthIndex] = parseInt(item.count);
                    }
                }
            });
        }
        
        console.log('Processed monthly data:', monthlyData);
        
        // Check if all values are zero
        if (monthlyData.every(val => val === 0)) {
            showNoDataMessage(canvas, 'No application data for this year');
            return;
        }
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: monthLabels,
                datasets: [{
                    label: 'Applications',
                    data: monthlyData,
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    borderWidth: 3,
                    pointBackgroundColor: '#3498db',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 8,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: { size: 12, weight: 'bold' },
                            color: '#2c3e50'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleFont: { size: 13, weight: 'bold' },
                        bodyFont: { size: 12 },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return `Applications: ${context.raw}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0, 0, 0, 0.05)' },
                        ticks: { 
                            stepSize: 1,
                            font: { size: 11 },
                            callback: function(value) {
                                if (Math.floor(value) === value) {
                                    return value;
                                }
                            }
                        },
                        title: {
                            display: true,
                            text: 'Number of Applications',
                            font: { size: 12, weight: 'bold' }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    }
                },
                animation: {
                    duration: 1500,
                    easing: 'easeInOutQuart'
                }
            }
        });
        console.log('Monthly chart created successfully');
    } catch (e) {
        console.error('Error parsing monthly data:', e);
        showNoDataMessage(canvas, 'Error loading chart data');
    }
}

/**
 * Application Status Doughnut Chart
 */
function initStatusChart() {
    const canvas = document.getElementById('statusChart');
    if (!canvas) {
        console.log('Status chart canvas not found');
        return;
    }

    const ctx = canvas.getContext('2d');
    
    // Get status data from data attributes
    const pending = parseInt(canvas.dataset.pending) || 0;
    const verified = parseInt(canvas.dataset.verified) || 0;
    const converted = parseInt(canvas.dataset.converted) || 0;
    const rejected = parseInt(canvas.dataset.rejected) || 0;
    
    console.log('Status data:', { pending, verified, converted, rejected });
    
    // Only create chart if there's data
    if (pending + verified + converted + rejected === 0) {
        showNoDataMessage(canvas, 'No status data available');
        return;
    }
    
    const total = pending + verified + converted + rejected;
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Verified', 'Converted', 'Rejected'],
            datasets: [{
                data: [pending, verified, converted, rejected],
                backgroundColor: ['#f39c12', '#3498db', '#2ecc71', '#e74c3c'],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        boxHeight: 12,
                        padding: 15,
                        font: { size: 12 },
                        generateLabels: function(chart) {
                            const data = chart.data;
                            if (data.labels.length && data.datasets.length) {
                                return data.labels.map((label, i) => {
                                    const value = data.datasets[0].data[i];
                                    const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                    return {
                                        text: `${label}: ${value} (${percentage}%)`,
                                        fillStyle: data.datasets[0].backgroundColor[i],
                                        hidden: false,
                                        index: i
                                    };
                                });
                            }
                            return [];
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            },
            animation: {
                animateScale: true,
                animateRotate: true,
                duration: 1500
            }
        }
    });
    console.log('Status chart created successfully');
}

/**
 * Gender Distribution Pie Chart
 */
function initGenderChart() {
    const canvas = document.getElementById('genderChart');
    if (!canvas) {
        console.log('Gender chart canvas not found');
        return;
    }

    const ctx = canvas.getContext('2d');
    
    // Get gender data from data attributes
    const male = parseInt(canvas.dataset.male) || 0;
    const female = parseInt(canvas.dataset.female) || 0;
    const other = parseInt(canvas.dataset.other) || 0;
    
    console.log('Gender data:', { male, female, other });
    
    // Only create chart if there's data
    if (male + female + other === 0) {
        showNoDataMessage(canvas, 'No gender data available');
        return;
    }
    
    const total = male + female + other;
    
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Male', 'Female', 'Other'],
            datasets: [{
                data: [male, female, other],
                backgroundColor: ['#3498db', '#e74c3c', '#95a5a6'],
                borderWidth: 0,
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
                        boxWidth: 12,
                        boxHeight: 12,
                        padding: 15,
                        font: { size: 12 },
                        generateLabels: function(chart) {
                            const data = chart.data;
                            if (data.labels.length && data.datasets.length) {
                                return data.labels.map((label, i) => {
                                    const value = data.datasets[0].data[i];
                                    const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                    return {
                                        text: `${label}: ${value} (${percentage}%)`,
                                        fillStyle: data.datasets[0].backgroundColor[i],
                                        hidden: false,
                                        index: i
                                    };
                                });
                            }
                            return [];
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            },
            animation: {
                animateScale: true,
                animateRotate: true,
                duration: 1500
            }
        }
    });
    console.log('Gender chart created successfully');
}

/**
 * Civil Status Distribution Pie Chart
 */
function initCivilStatusChart() {
    const canvas = document.getElementById('civilStatusChart');
    if (!canvas) {
        console.log('Civil status chart canvas not found');
        return;
    }

    const ctx = canvas.getContext('2d');
    
    // Get civil status data from data attributes
    const single = parseInt(canvas.dataset.single) || 0;
    const married = parseInt(canvas.dataset.married) || 0;
    const widowed = parseInt(canvas.dataset.widowed) || 0;
    const separated = parseInt(canvas.dataset.separated) || 0;
    const others = parseInt(canvas.dataset.others) || 0;
    
    console.log('Civil status data:', { single, married, widowed, separated, others });
    
    // Filter out zero values
    const labels = [];
    const data = [];
    const colors = [];
    
    if (single > 0) {
        labels.push('Single');
        data.push(single);
        colors.push('#3498db');
    }
    if (married > 0) {
        labels.push('Married');
        data.push(married);
        colors.push('#2ecc71');
    }
    if (widowed > 0) {
        labels.push('Widowed');
        data.push(widowed);
        colors.push('#9b59b6');
    }
    if (separated > 0) {
        labels.push('Separated');
        data.push(separated);
        colors.push('#e67e22');
    }
    if (others > 0) {
        labels.push('Others');
        data.push(others);
        colors.push('#95a5a6');
    }
    
    if (labels.length === 0) {
        showNoDataMessage(canvas, 'No civil status data available');
        return;
    }
    
    const total = data.reduce((a, b) => a + b, 0);
    
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors,
                borderWidth: 0,
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
                        boxWidth: 12,
                        boxHeight: 12,
                        padding: 15,
                        font: { size: 12 },
                        generateLabels: function(chart) {
                            const data = chart.data;
                            if (data.labels.length && data.datasets.length) {
                                return data.labels.map((label, i) => {
                                    const value = data.datasets[0].data[i];
                                    const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                    return {
                                        text: `${label}: ${value} (${percentage}%)`,
                                        fillStyle: data.datasets[0].backgroundColor[i],
                                        hidden: false,
                                        index: i
                                    };
                                });
                            }
                            return [];
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            },
            animation: {
                animateScale: true,
                animateRotate: true,
                duration: 1500
            }
        }
    });
    console.log('Civil status chart created successfully');
}

/**
 * Initialize export buttons
 */
function initExportButtons() {
    // Add export dropdown menu
    const exportBtn = document.querySelector('.btn-success');
    if (exportBtn && exportBtn.textContent.includes('Print')) {
        const exportDropdown = document.createElement('div');
        exportDropdown.className = 'btn-group ms-2';
        exportDropdown.innerHTML = `
            <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fas fa-download"></i> Export
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#" onclick="exportAsPDF()"><i class="fas fa-file-pdf"></i> Export as PDF</a></li>
                <li><a class="dropdown-item" href="#" onclick="exportAsExcel()"><i class="fas fa-file-excel"></i> Export as Excel</a></li>
                <li><a class="dropdown-item" href="#" onclick="exportCharts()"><i class="fas fa-chart-line"></i> Export Charts</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#" onclick="exportTableToCSV()"><i class="fas fa-table"></i> Export Table Data</a></li>
            </ul>
        `;
        
        exportBtn.parentNode.appendChild(exportDropdown);
    }
}

/**
 * Initialize date range picker
 */
function initDateRangePicker() {
    // Add date range picker next to year select
    const yearSelect = document.querySelector('select[onchange*="year"]');
    if (yearSelect) {
        const dateRangeBtn = document.createElement('button');
        dateRangeBtn.className = 'btn btn-outline-primary ms-2';
        dateRangeBtn.innerHTML = '<i class="fas fa-calendar-alt"></i> Custom Range';
        dateRangeBtn.onclick = function() {
            showDateRangeModal();
        };
        
        yearSelect.parentNode.insertBefore(dateRangeBtn, yearSelect.nextSibling);
    }
}

/**
 * Show date range modal
 */
function showDateRangeModal() {
    const modalHtml = `
        <div class="modal fade" id="dateRangeModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-calendar-alt"></i> Select Date Range
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" id="startDate" class="form-control" value="${new Date().toISOString().slice(0,10)}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">End Date</label>
                            <input type="date" id="endDate" class="form-control" value="${new Date().toISOString().slice(0,10)}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="applyDateRange()">Apply Range</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal
    const existingModal = document.getElementById('dateRangeModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Add new modal
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('dateRangeModal'));
    modal.show();
}

/**
 * Apply date range filter
 */
window.applyDateRange = function() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    if (startDate && endDate) {
        window.location.href = `?start=${startDate}&end=${endDate}`;
    }
};

/**
 * Export as PDF
 */
window.exportAsPDF = function() {
    window.print();
};

/**
 * Export as Excel (CSV format)
 */
window.exportAsExcel = function() {
    const tables = document.querySelectorAll('table');
    let csv = '';
    
    tables.forEach((table, index) => {
        const rows = table.querySelectorAll('tr');
        rows.forEach(row => {
            const cols = row.querySelectorAll('td, th');
            const rowData = [];
            cols.forEach(col => {
                rowData.push('"' + col.innerText.replace(/"/g, '""') + '"');
            });
            csv += rowData.join(',') + '\n';
        });
        if (index < tables.length - 1) csv += '\n';
    });
    
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'report-data.csv';
    a.click();
    window.URL.revokeObjectURL(url);
};

/**
 * Export all charts as images
 */
window.exportCharts = function() {
    const charts = ['monthlyChart', 'statusChart', 'genderChart', 'civilStatusChart'];
    
    charts.forEach(chartId => {
        const canvas = document.getElementById(chartId);
        if (canvas && canvas.style.display !== 'none') {
            const link = document.createElement('a');
            link.download = `${chartId}.png`;
            link.href = canvas.toDataURL('image/png');
            link.click();
        }
    });
};

/**
 * Export table data to CSV
 */
window.exportTableToCSV = function() {
    const table = document.querySelector('.table');
    if (!table) return;
    
    const rows = table.querySelectorAll('tr');
    const csv = [];
    
    rows.forEach(row => {
        const cols = row.querySelectorAll('td, th');
        const rowData = [];
        cols.forEach(col => {
            rowData.push('"' + col.innerText.replace(/"/g, '""') + '"');
        });
        csv.push(rowData.join(','));
    });
    
    const blob = new Blob([csv.join('\n')], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'course-enrollment.csv';
    a.click();
    window.URL.revokeObjectURL(url);
};

/**
 * Helper function to show no data message
 */
function showNoDataMessage(canvas, message) {
    const parent = canvas.parentElement;
    canvas.style.display = 'none';
    
    // Check if message already exists
    if (parent.querySelector('.no-data-message')) {
        return;
    }
    
    const messageDiv = document.createElement('div');
    messageDiv.className = 'no-data-message text-muted text-center py-5';
    messageDiv.innerHTML = `<i class="fas fa-chart-pie fa-2x mb-2"></i><br>${message}`;
    parent.appendChild(messageDiv);
}

/**
 * Refresh reports data
 */
window.refreshReports = function() {
    location.reload();
};
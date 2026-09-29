/**
 * Admin Courses Management JavaScript
 * Handles course operations, form validation, and UI interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Courses page initialized');
    
    initCourseForm();
    initSearchFilter();
    initTableSorting();
    initTooltips();
    initStatsCounter();
});

/**
 * Initialize course form validation
 */
function initCourseForm() {
    const form = document.querySelector('form[method="POST"]');
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        const courseCode = document.querySelector('input[name="course_code"]');
        const courseName = document.querySelector('input[name="course_name"]');
        const totalUnits = document.querySelector('input[name="total_units"]');
        
        // Validate course code format
        if (courseCode && !validateCourseCode(courseCode.value)) {
            e.preventDefault();
            showNotification('Course code should be uppercase letters only (e.g., BSCS, BSIT)', 'error');
            return;
        }
        
        // Validate course name
        if (courseName && courseName.value.trim().length < 5) {
            e.preventDefault();
            showNotification('Course name must be at least 5 characters', 'error');
            return;
        }
        
        // Validate total units
        if (totalUnits && (totalUnits.value < 50 || totalUnits.value > 300)) {
            e.preventDefault();
            showNotification('Total units should be between 50 and 300', 'error');
            return;
        }
    });
    
    // Auto uppercase course code
    const courseCodeInput = document.querySelector('input[name="course_code"]');
    if (courseCodeInput) {
        courseCodeInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    }
}

/**
 * Validate course code format
 */
function validateCourseCode(code) {
    return /^[A-Z]{2,10}$/.test(code);
}

/**
 * Initialize search filter for courses table
 */
function initSearchFilter() {
    // Add search input to card header
    const cardHeader = document.querySelector('.card-header');
    if (!cardHeader) return;
    
    const searchHtml = `
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="m-0">Active Courses</h5>
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="courseSearch" class="form-control form-control-sm" 
                       placeholder="Search courses..." style="width: 250px;">
            </div>
        </div>
    `;
    
    cardHeader.innerHTML = searchHtml;
    
    // Add search functionality
    const searchInput = document.getElementById('courseSearch');
    const table = document.querySelector('.table');
    
    if (!searchInput || !table) return;
    
    searchInput.addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = table.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
        
        // Show/hide no results message
        updateNoResultsMessage(table);
    });
}

/**
 * Initialize table sorting
 */
function initTableSorting() {
    const table = document.querySelector('.table');
    if (!table) return;
    
    const headers = table.querySelectorAll('thead th');
    
    headers.forEach((header, index) => {
        // Skip Actions column
        if (index === headers.length - 1) return;
        
        header.style.cursor = 'pointer';
        header.addEventListener('click', function() {
            sortTable(table, index);
        });
    });
}

/**
 * Sort table by column
 */
function sortTable(table, column) {
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    // Toggle sort direction
    const currentDirection = table.dataset.sortDirection === 'asc' ? 'desc' : 'asc';
    table.dataset.sortDirection = currentDirection;
    
    rows.sort((a, b) => {
        let aValue = a.cells[column].textContent.trim();
        let bValue = b.cells[column].textContent.trim();
        
        // Check if values are numbers
        if (!isNaN(aValue) && !isNaN(bValue)) {
            aValue = parseInt(aValue);
            bValue = parseInt(bValue);
            return currentDirection === 'asc' ? aValue - bValue : bValue - aValue;
        }
        
        // String comparison
        return currentDirection === 'asc' 
            ? aValue.localeCompare(bValue)
            : bValue.localeCompare(aValue);
    });
    
    // Reorder rows
    rows.forEach(row => tbody.appendChild(row));
    
    // Update header indicators
    updateSortIndicators(table, column, currentDirection);
}

/**
 * Update sort indicators on headers
 */
function updateSortIndicators(table, column, direction) {
    const headers = table.querySelectorAll('thead th');
    
    headers.forEach((header, index) => {
        header.classList.remove('sort-asc', 'sort-desc');
        
        if (index === column) {
            header.classList.add(direction === 'asc' ? 'sort-asc' : 'sort-desc');
        }
    });
}

/**
 * Update no results message
 */
function updateNoResultsMessage(table) {
    const tbody = table.querySelector('tbody');
    const rows = tbody.querySelectorAll('tr');
    let visibleCount = 0;
    
    rows.forEach(row => {
        if (row.style.display !== 'none') {
            visibleCount++;
        }
    });
    
    // Remove existing no results message
    const existingMessage = tbody.querySelector('.no-results-message');
    if (existingMessage) {
        existingMessage.remove();
    }
    
    // Show no results message if no visible rows
    if (visibleCount === 0) {
        const messageRow = document.createElement('tr');
        messageRow.className = 'no-results-message';
        messageRow.innerHTML = `
            <td colspan="6" class="text-center py-4">
                <i class="fas fa-search fa-2x mb-3 text-muted"></i>
                <p class="text-muted mb-0">No courses match your search</p>
            </td>
        `;
        tbody.appendChild(messageRow);
    }
}

/**
 * Initialize tooltips
 */
function initTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

/**
 * Initialize statistics counter
 */
function initStatsCounter() {
    const totalCourses = document.querySelectorAll('tbody tr').length;
    const activeCount = document.querySelector('.active-count');
    
    if (activeCount) {
        animateValue(activeCount, 0, totalCourses, 1000);
    }
}

/**
 * Animate number counting
 */
function animateValue(element, start, end, duration) {
    if (start === end) return;
    
    const range = end - start;
    const increment = range / (duration / 16);
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        
        if (current >= end) {
            element.innerText = end;
            clearInterval(timer);
        } else {
            element.innerText = Math.floor(current);
        }
    }, 16);
}

/**
 * Show notification message
 */
function showNotification(message, type = 'info') {
    // Check if notification container exists
    let container = document.querySelector('.notification-container');
    
    if (!container) {
        container = document.createElement('div');
        container.className = 'notification-container';
        document.body.appendChild(container);
    }
    
    // Create notification
    const notification = document.createElement('div');
    notification.className = `notification notification-${type} fade-in`;
    
    const icon = type === 'success' ? 'check-circle' : (type === 'error' ? 'exclamation-circle' : 'info-circle');
    notification.innerHTML = `
        <i class="fas fa-${icon}"></i>
        <span>${message}</span>
    `;
    
    container.appendChild(notification);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.classList.add('fade-out');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

/**
 * Delete course function
 */
window.deleteCourse = function(id) {
    if (confirm('Are you sure you want to delete this course? This action cannot be undone.')) {
        showNotification('Deleting course...', 'info');
        
        // Use fetch for AJAX delete
        fetch('delete_course.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: id })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Course deleted successfully!', 'success');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                showNotification(data.message || 'Error deleting course', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred while deleting', 'error');
        });
    }
};

/**
 * Edit course function
 */
window.editCourse = function(id) {
    // Show loading
    showNotification('Loading course details...', 'info');
    
    // Fetch course details
    fetch(`get-course.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Populate and show edit modal
                showEditModal(data.course);
            } else {
                showNotification('Error loading course details', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred', 'error');
        });
};

/**
 * Show edit course modal
 */
function showEditModal(course) {
    // Check if modal exists, if not create it
    let modal = document.getElementById('editCourseModal');
    
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'editCourseModal';
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form id="editCourseForm" method="POST">
                        <div class="modal-header bg-warning">
                            <h5 class="modal-title">
                                <i class="fas fa-edit"></i> Edit Course
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="course_id" id="edit_course_id">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Course Code</label>
                                    <input type="text" name="course_code" id="edit_course_code" 
                                           class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Course Name</label>
                                    <input type="text" name="course_name" id="edit_course_name" 
                                           class="form-control" required>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" id="edit_description" 
                                              class="form-control" rows="3"></textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Duration (Years)</label>
                                    <input type="number" name="duration_years" id="edit_duration" 
                                           class="form-control" min="1" max="6" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Total Units</label>
                                    <input type="number" name="total_units" id="edit_units" 
                                           class="form-control" required>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="is_active" id="edit_is_active" 
                                               class="form-check-input" value="1">
                                        <label class="form-check-label">Active Course</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="update_course" class="btn btn-warning">Update Course</button>
                        </div>
                    </form>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }
    
    // Populate form with course data
    document.getElementById('edit_course_id').value = course.id;
    document.getElementById('edit_course_code').value = course.course_code;
    document.getElementById('edit_course_name').value = course.course_name;
    document.getElementById('edit_description').value = course.description || '';
    document.getElementById('edit_duration').value = course.duration_years;
    document.getElementById('edit_units').value = course.total_units;
    document.getElementById('edit_is_active').checked = course.is_active == 1;
    
    // Show modal
    const editModal = new bootstrap.Modal(modal);
    editModal.show();
}

/**
 * Export courses to CSV
 */
function exportCourses() {
    const courses = [];
    const rows = document.querySelectorAll('tbody tr:not(.no-results-message)');
    
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        courses.push({
            code: cells[0].textContent.trim(),
            name: cells[1].textContent.trim(),
            duration: cells[2].textContent.trim(),
            units: cells[3].textContent.trim(),
            enrolled: cells[4].textContent.trim()
        });
    });
    
    // Create CSV
    let csv = 'Course Code,Course Name,Duration,Total Units,Students Enrolled\n';
    courses.forEach(c => {
        csv += `"${c.code}","${c.name}",${c.duration},${c.units},${c.enrolled}\n`;
    });
    
    // Download
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'courses-list.csv';
    a.click();
    window.URL.revokeObjectURL(url);
    
    showNotification('Courses exported successfully!', 'success');
}
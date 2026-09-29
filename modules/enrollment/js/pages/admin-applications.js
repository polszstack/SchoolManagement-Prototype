/**
 * Admin Applications Page JavaScript
 * Handles filtering, searching, and table interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Applications page initialized');
    
    initSearchFilter();
    initTableSorting();
    initBulkActions();
    initTooltips();
});

/**
 * Initialize search filter functionality
 */
function initSearchFilter() {
    const searchInput = document.getElementById('searchInput');
    const table = document.getElementById('applicationsTable');
    
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
 * Initialize table column sorting
 */
function initTableSorting() {
    const table = document.getElementById('applicationsTable');
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
        const aValue = a.cells[column].textContent.trim();
        const bValue = b.cells[column].textContent.trim();
        
        // Check if values are dates
        if (isDate(aValue) && isDate(bValue)) {
            const dateA = new Date(aValue);
            const dateB = new Date(bValue);
            return currentDirection === 'asc' ? dateA - dateB : dateB - dateA;
        }
        
        // Check if values are numbers
        if (!isNaN(aValue) && !isNaN(bValue)) {
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
 * Check if string is a date
 */
function isDate(value) {
    return !isNaN(Date.parse(value));
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
            <td colspan="${table.querySelectorAll('thead th').length}" class="text-center py-4">
                <i class="fas fa-search fa-2x mb-3 text-muted"></i>
                <p class="text-muted mb-0">No applications match your search</p>
            </td>
        `;
        tbody.appendChild(messageRow);
    }
}

/**
 * Initialize bulk actions
 */
function initBulkActions() {
    const selectAllCheckbox = document.getElementById('selectAll');
    if (!selectAllCheckbox) return;
    
    selectAllCheckbox.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.application-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkActionsBar();
    });
    
    // Individual checkbox changes
    document.querySelectorAll('.application-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActionsBar);
    });
    
    // Bulk action select
    const bulkActionSelect = document.getElementById('bulkAction');
    if (bulkActionSelect) {
        bulkActionSelect.addEventListener('change', function() {
            if (this.value) {
                executeBulkAction(this.value);
                this.value = '';
            }
        });
    }
}

/**
 * Update bulk actions bar visibility
 */
function updateBulkActionsBar() {
    const selectedCount = document.querySelectorAll('.application-checkbox:checked').length;
    const bulkBar = document.getElementById('bulkActionsBar');
    
    if (!bulkBar) return;
    
    if (selectedCount > 0) {
        bulkBar.style.display = 'block';
        bulkBar.querySelector('.selected-count').textContent = selectedCount;
    } else {
        bulkBar.style.display = 'none';
    }
}

/**
 * Execute bulk action
 */
function executeBulkAction(action) {
    const selectedIds = [];
    document.querySelectorAll('.application-checkbox:checked').forEach(checkbox => {
        selectedIds.push(checkbox.value);
    });
    
    if (selectedIds.length === 0) {
        alert('Please select at least one application');
        return;
    }
    
    // Confirm action
    if (!confirm(`Are you sure you want to ${action} ${selectedIds.length} application(s)?`)) {
        return;
    }
    
    // Create form and submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'bulk-actions.php';
    
    const idsInput = document.createElement('input');
    idsInput.type = 'hidden';
    idsInput.name = 'application_ids';
    idsInput.value = JSON.stringify(selectedIds);
    
    const actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'action';
    actionInput.value = action;
    
    form.appendChild(idsInput);
    form.appendChild(actionInput);
    document.body.appendChild(form);
    form.submit();
}

/**
 * Initialize tooltips
 */
function initTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

/**
 * Export table to CSV
 */
function exportToCSV() {
    const table = document.getElementById('applicationsTable');
    if (!table) return;
    
    const rows = [];
    
    // Get headers
    const headers = [];
    table.querySelectorAll('thead th').forEach(header => {
        // Skip Actions column
        if (!header.textContent.includes('Actions')) {
            headers.push(header.textContent.trim());
        }
    });
    rows.push(headers.join(','));
    
    // Get data rows
    table.querySelectorAll('tbody tr').forEach(row => {
        // Skip no results message
        if (row.classList.contains('no-results-message')) return;
        
        const rowData = [];
        row.querySelectorAll('td').forEach((cell, index) => {
            // Skip Actions column
            if (index < headers.length) {
                let cellText = cell.textContent.trim().replace(/,/g, ';'); // Replace commas
                rowData.push(`"${cellText}"`); // Wrap in quotes
            }
        });
        rows.push(rowData.join(','));
    });
    
    // Create and download CSV
    const csvContent = rows.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'applications-export.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}

/**
 * Quick status update
 */
function updateStatus(applicantId, newStatus) {
    if (!confirm(`Are you sure you want to change status to ${newStatus}?`)) {
        return;
    }
    
    fetch('update-status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            applicant_id: applicantId,
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error updating status: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating status');
    });
}
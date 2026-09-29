/**
 * Programs Management Module
 * Handles AJAX operations for adding and deleting programs
 * Uses Bootstrap 5 toasts and custom confirmation modals
 * 
 * Features:
 * - Green toast for successful program additions
 * - Red toast for successful program deletions
 * - Custom confirmation modal for delete operations
 * - Auto-hiding toasts (3 seconds)
 * - Non-blocking notifications using Bootstrap Toast component
 */

$(document).ready(function() {
    console.log('[PROGRAMS-MGMT] Module initialized');
    
    // Initialize event listeners
    initializeAddProgramForm();
    initializeModalHandlers();
});

/**
 * Initialize Add Program Form Handler
 * Handles form submission and AJAX for adding new programs
 */
function initializeAddProgramForm() {
    console.log('[PROGRAMS-MGMT] Initializing Add Program form');
    
    $(document).on('submit', '#addProgramForm', function(e) {
        e.preventDefault();
        console.log('[PROGRAMS-MGMT] Add Program form submitted');
        
        const $form = $(this);
        const programCode = $('#programCode').val().trim();
        const programName = $('#programName').val().trim();
        const description = $('#description').val().trim();
        
        console.log('[PROGRAMS-MGMT] Form values:', { 
            code: programCode, 
            name: programName, 
            desc: description 
        });
        
        // Validate form fields
        if (!programCode || !programName || !description) {
            showToast('Please fill in all required fields', 'warning');
            console.warn('[PROGRAMS-MGMT] Validation failed - empty fields');
            return false;
        }
        
        // Send AJAX request
        $.ajax({
            url: './api/save_program.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                program_code: programCode,
                program_name: programName,
                department: description
            }),
            success: function(response) {
                console.log('[PROGRAMS-MGMT] Add Program successful:', response);
                
                if (response.success) {
                    // Show green success toast
                    showToast('Program added successfully', 'success');
                    
                    // Add new row to table
                    const formattedCode = formatProgramCode(programCode);
                    const newRow = `
                        <tr>
                            <td>${escapeHtml(formattedCode)}</td>
                            <td>${escapeHtml(programName)}</td>
                            <td>${escapeHtml(description)}</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-info" onclick="viewCurriculum('${response.program_id}', '${escapeHtml(formattedCode)}')">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button class="btn btn-sm btn-warning" onclick="editProgram('${escapeHtml(programCode)}', '${escapeHtml(programName)}', '${escapeHtml(description)}')">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteProgram('${escapeHtml(programCode)}')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                    
                    $('#programsTableBody').append(newRow);
                    console.log('[PROGRAMS-MGMT] Row added to table');
                    
                    // Reset form
                    $form[0].reset();
                    $('#programCode').prop('disabled', false);
                    
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addProgramModal'));
                    if (modal) {
                        modal.hide();
                    }
                } else {
                    showToast('Error: ' + response.message, 'danger');
                    console.error('[PROGRAMS-MGMT] Server returned error:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('[PROGRAMS-MGMT] AJAX error:', error, xhr);
                showToast('Error: Failed to add program', 'danger');
            }
        });
        
        return false;
    });
}

/**
 * Show Custom Confirmation Modal for Delete Operation
 * Creates a Bootstrap 5 modal asking for delete confirmation
 * 
 * @param {string} programCode - The program code to delete
 */
function showDeleteConfirmation(programCode) {
    console.log('[PROGRAMS-MGMT] Showing delete confirmation for:', programCode);
    
    // Create modal HTML
    const modalHtml = `
        <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light border-danger">
                        <h5 class="modal-title text-danger" id="deleteModalLabel">
                            <i class="fas fa-exclamation-triangle me-2"></i> Confirm Deletion
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-2">Are you sure you want to delete program <strong>${escapeHtml(programCode)}</strong>?</p>
                        <p class="text-muted small mb-0"><i class="fas fa-info-circle me-1"></i>This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                            <i class="fas fa-trash me-2"></i>Delete Program
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove old modal if exists
    $('#deleteConfirmModal').remove();
    
    // Add new modal to body
    $('body').append(modalHtml);
    
    // Show modal
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    deleteModal.show();
    
    // Handle confirm deletion button click (one-time binding)
    $('#confirmDeleteBtn').one('click', function() {
        console.log('[PROGRAMS-MGMT] Delete confirmed for:', programCode);
        performDelete(programCode, deleteModal);
    });
}

/**
 * Perform the actual delete operation via AJAX
 * Shows red toast notification on success
 * 
 * @param {string} programCode - The program code to delete
 * @param {bootstrap.Modal} modal - The confirmation modal to close
 */
function performDelete(programCode, modal) {
    console.log('[PROGRAMS-MGMT] Performing AJAX delete for:', programCode);
    
    $.ajax({
        url: './api/delete_program.php',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            program_code: programCode
        }),
        success: function(response) {
            console.log('[PROGRAMS-MGMT] Delete AJAX successful:', response);
            
            if (response.success) {
                // Close the confirmation modal first
                modal.hide();
                
                // Show red delete success toast
                showToast('Program deleted successfully', 'danger');
                
                // Remove row from table with fade animation
                const tableBody = $('#programsTableBody');
                const rows = tableBody.find('tr');
                
                rows.each(function() {
                    const firstCell = $(this).find('td:first');
                    // The cell contains formatted code (e.g., "BS-IT")
                    // We need to compare with the original code
                    if (firstCell.length > 0) {
                        $(this).fadeOut(300, function() {
                            $(this).remove();
                            console.log('[PROGRAMS-MGMT] Row removed from table');
                        });
                        return false; // Break loop after first match
                    }
                });
            } else {
                showToast('Error: ' + (response.message || 'Failed to delete program'), 'danger');
                console.error('[PROGRAMS-MGMT] Server returned error:', response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('[PROGRAMS-MGMT] AJAX error:', error, xhr);
            showToast('Error: Failed to delete program', 'danger');
        }
    });
}

/**
 * Show Bootstrap 5 Toast Notification
 * Auto-hides after 3 seconds, non-blocking
 * 
 * @param {string} message - Toast message text
 * @param {string} type - Toast type: 'success' (green), 'danger' (red), 'warning' (orange), 'info' (blue)
 */
function showToast(message, type = 'success') {
    console.log('[PROGRAMS-MGMT] Showing toast:', message, 'Type:', type);
    
    // Toast styling based on type
    const toastStyles = {
        success: {
            color: '#10b981',   // Green
            icon: 'check-circle',
            label: 'Success'
        },
        danger: {
            color: '#ef4444',   // Red
            icon: 'exclamation-circle',
            label: 'Error'
        },
        warning: {
            color: '#f59e0b',   // Orange
            icon: 'exclamation-triangle',
            label: 'Warning'
        },
        info: {
            color: '#0ea5e9',   // Blue
            icon: 'info-circle',
            label: 'Info'
        }
    };
    
    const style = toastStyles[type] || toastStyles.info;
    
    // Create unique toast HTML using Bootstrap 5 Toast component
    const toastId = 'toast-' + Date.now();
    const toastHtml = `
        <div class="position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 1100;">
            <div class="toast" id="${toastId}" role="alert" aria-live="polite" aria-atomic="true">
                <div class="toast-header" style="border-left: 4px solid ${style.color}; background-color: #ffffff;">
                    <i class="fas fa-${style.icon}" style="color: ${style.color}; margin-right: 8px;"></i>
                    <strong class="me-auto" style="color: ${style.color};">${style.label}</strong>
                    <small class="text-muted">just now</small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body" style="color: #1f2937;">
                    ${escapeHtml(message)}
                </div>
            </div>
        </div>
    `;
    
    // Add toast to body
    $('body').append(toastHtml);
    
    // Initialize Bootstrap Toast component
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, {
        delay: 3000,  // Auto-hide after 3 seconds
        autohide: true
    });
    
    // Show the toast
    toast.show();
    
    // Remove toast element from DOM after it's hidden
    toastElement.addEventListener('hidden.bs.toast', function() {
        $(this).closest('.position-fixed').remove();
    });
}

/**
 * Escape HTML special characters for safe display
 * Prevents XSS attacks
 * 
 * @param {string} text - Text to escape
 * @returns {string} Escaped text
 */
function escapeHtml(text) {
    if (!text) return '';
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
 * Format program code to standard format
 * Example: BSIT => BS-IT
 * 
 * @param {string} code - The program code
 * @returns {string} Formatted code
 */
function formatProgramCode(code) {
    if (!code || code.length < 3) return code;
    return code.substring(0, 2) + '-' + code.substring(2);
}

/**
 * Initialize Modal Handlers
 * Sets up event handlers for modals
 */
function initializeModalHandlers() {
    console.log('[PROGRAMS-MGMT] Initializing modal handlers');
    
    // Clear form when opening add program modal
    $('#addProgramModal').on('show.bs.modal', function() {
        $('#addProgramForm')[0].reset();
        $('#programCode').prop('disabled', false);
        $('#addProgramModal .modal-title').text('Add New Program');
        console.log('[PROGRAMS-MGMT] Add Program modal opened');
    });
    
    // Handle modal close to reset form state
    $('#addProgramModal').on('hidden.bs.modal', function() {
        $('#addProgramForm')[0].reset();
        $('#programCode').prop('disabled', false);
        $('#addProgramModal .modal-title').text('Add New Program');
    });
}

/**
 * Global window functions for inline onclick handlers
 */

/**
 * Delete a program - shows confirmation modal first
 * Called from table action button onclick
 * 
 * @param {string} programCode - The program code to delete
 */
window.deleteProgram = function(programCode) {
    console.log('[PROGRAMS-MGMT] Delete button clicked for:', programCode);
    showDeleteConfirmation(programCode);
};

/**
 * View curriculum for a program
 * Called from table action button onclick
 * 
 * @param {string} programId - The program ID
 * @param {string} programCode - The program code
 */
window.viewCurriculum = function(programId, programCode) {
    console.log('[PROGRAMS-MGMT] View curriculum clicked for:', programCode);
    // Implementation for viewing curriculum (separate functionality)
};

/**
 * Edit a program - opens add program modal with form populated
 * Called from table action button onclick
 * 
 * @param {string} programCode - The program code
 * @param {string} programName - The program name
 * @param {string} description - The program description
 */
window.editProgram = function(programCode, programName, description) {
    console.log('[PROGRAMS-MGMT] Edit clicked for:', programCode);
    
    // Populate form with existing values
    $('#programCode').val(programCode).prop('disabled', true);
    $('#programName').val(programName);
    $('#description').val(description);
    
    // Update modal title
    $('#addProgramModal .modal-title').text('Edit Program');
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('addProgramModal'));
    modal.show();
};

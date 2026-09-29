<!-- Events Management Page - Display and manage events -->
<?php
require_once dirname(dirname(dirname(__DIR__))) . '/database/db.php';
require_once dirname(__DIR__) . '/classes/EventManager.php';

// Create database connection and manager
$database = new Database();
$conn = $database->getConnection();
$eventManager = new EventManager($conn);

// Fetch all CC events from database
try {
    $events = $eventManager->getAllEvents();
    $eventTypes = $eventManager->getEventTypeEnums();
    $statuses = $eventManager->getStatusEnums();
} catch (Exception $e) {
    $events = [];
    $eventTypes = ['Academic', 'Meeting','Institutional Event', 'Sports', 'Cultural', 'Social', 'Workshop', 'Seminar'];
  $statuses = ['upcoming', 'ongoing', 'completed', 'cancelled'];
    $error = $e->getMessage();
}
?>

<style>
    /* Custom CSS - Purely HTML/CSS/JS, no Bootstrap */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    }

    body {
        background-color: #f8f9fa;
        padding: 20px;
    }

    .container {
        max-width: 1400px;
        margin: 0 auto;
    }

    /* Header Styles */
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .header h1 {
        font-size: 24px;
        color: #333;
        margin-bottom: 5px;
    }

    .header p {
        color: #6c757d;
        font-size: 14px;
    }

    /* Button Styles */
    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s;
    }

    .btn-primary {
        background-color: #0d6efd;
        color: white;
    }

    .btn-primary:hover {
        background-color: #0b5ed7;
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #5c636a;
    }

    .btn-info {
        background-color: #0dcaf0;
        color: white;
        padding: 5px 10px;
        font-size: 13px;
    }

    .btn-info:hover {
        background-color: #31d2f2;
    }

    .btn-danger {
        background-color: #dc3545;
        color: white;
        padding: 5px 10px;
        font-size: 13px;
    }

    .btn-danger:hover {
        background-color: #bb2d3b;
    }

    .btn-sm {
        padding: 5px 10px;
        font-size: 13px;
    }

    /* Cards */
    .card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }

    .card-body {
        padding: 20px;
    }

    .card-header {
        padding: 15px 20px;
        border-bottom: 1px solid #dee2e6;
        background: white;
        font-weight: 500;
    }

    /* Statistics Cards */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 15px;
        margin-bottom: 20px;
    }

    .stat-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    .stat-icon.total { background: #e7f5ff; color: #0d6efd; }
    .stat-icon.upcoming { background: #fff3cd; color: #ffc107; }
    .stat-icon.ongoing { background: #cff4fc; color: #0dcaf0; }
    .stat-icon.completed { background: #d1e7dd; color: #198754; }

    .stat-info h6 {
        font-size: 14px;
        color: #6c757d;
        margin-bottom: 5px;
    }

    .stat-info h3 {
        font-size: 24px;
        color: #333;
    }

    /* Filters */
    .filters {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr;
        gap: 15px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-label {
        display: block;
        margin-bottom: 5px;
        font-weight: 500;
        color: #333;
    }

    .form-control {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        font-size: 14px;
    }

    .form-control:focus {
        outline: none;
        border-color: #86b7fe;
        box-shadow: 0 0 0 3px rgba(13,110,253,0.25);
    }

    .form-select {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        font-size: 14px;
        background: white;
    }

    /* Table */
    .table-responsive {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th {
        text-align: left;
        padding: 12px;
        background: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        color: #333;
    }

    td {
        padding: 12px;
        border-bottom: 1px solid #dee2e6;
    }

    tr:hover {
        background: #f8f9fa;
    }

    /* Badges */
    .badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
    }

    .bg-secondary { background: #6c757d; color: white; }
    .bg-primary { background: #0d6efd; color: white; }
    .bg-success { background: #198754; color: white; }
    .bg-danger { background: #dc3545; color: white; }

    /* Modal */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
    }

    .modal.show {
        display: block;
    }

    .modal-dialog {
        max-width: 500px;
        margin: 50px auto;
    }

    .modal-content {
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .modal-header {
        padding: 15px 20px;
        border-bottom: 1px solid #dee2e6;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-title {
        font-size: 18px;
        font-weight: 600;
    }

    .btn-close {
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
        color: #6c757d;
    }

    .modal-body {
        padding: 20px;
    }

    .modal-footer {
        padding: 15px 20px;
        border-top: 1px solid #dee2e6;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    /* Toast */
    .toast-container {
        position: fixed;
        top: 80px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 9999;
        width: auto;
        max-width: 600px;
        padding: 0 20px;
    }

    .toast {
        background: white;
        border-radius: 8px;
        padding: 16px 28px;
        margin-bottom: 10px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        animation: slideInTop 0.4s ease-out forwards;
        font-size: 15px;
        font-weight: 600;
        text-align: center;
        letter-spacing: 0.3px;
        min-height: 50px;
    }

    .toast.success { 
        background: #28a745;
        color: white;
        box-shadow: 0 3px 10px rgba(40, 167, 69, 0.4);
    }
    .toast.danger { 
        background: #dc3545;
        color: white;
        box-shadow: 0 3px 10px rgba(220, 53, 69, 0.4);
    }
    .toast.warning { 
        background: #ffc107;
        color: #333;
        box-shadow: 0 3px 10px rgba(255, 193, 7, 0.4);
    }
    .toast.info { 
        background: #17a2b8;
        color: white;
        box-shadow: 0 3px 10px rgba(23, 162, 184, 0.4);
    }

    .toast-close {
        display: none;
    }

    @keyframes slideInTop {
        from { 
            transform: translateX(-50%) translateY(-50px);
            opacity: 0;
        }
        to { 
            transform: translateX(-50%) translateY(0);
            opacity: 1;
        }
    }

    @keyframes slideOutTop {
        from { 
            transform: translateX(-50%) translateY(0);
            opacity: 1;
        }
        to { 
            transform: translateX(-50%) translateY(-50px);
            opacity: 0;
        }
    }

    /* Grid */
    .row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }

    .text-muted { color: #6c757d; }
    .text-center { text-align: center; }
    .py-4 { padding: 40px 0; }
    .mb-0 { margin-bottom: 0; }
    .mb-3 { margin-bottom: 15px; }
    .mb-4 { margin-bottom: 20px; }
    .me-2 { margin-right: 8px; }
    .mt-3 { margin-top: 15px; }

    .spinner-border {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid currentColor;
        border-right-color: transparent;
        border-radius: 50%;
        animation: spin 0.75s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .text-danger { color: #dc3545; }
    small { font-size: 12px; }
</style>

<div class="container">
    <!-- Page Header -->
    <div class="header">
        <div>
            <h1><i class="fas fa-calendar-alt me-2"></i> Events Management</h1>
            <p class="text-muted small">Manage college events, activities, and celebrations</p>
        </div>
        <button class="btn btn-primary" id="addEventBtn" type="button">
            <i class="fas fa-plus me-2"></i> Add Event
        </button>
    </div>

    <!-- Event Statistics Cards -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon total">
                <i class="fas fa-calendar"></i>
            </div>
            <div class="stat-info">
                <h6>Total Events</h6>
                <h3 id="totalEvents">0</h3>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon upcoming">
                <i class="fas fa-arrow-right"></i>
            </div>
            <div class="stat-info">
                <h6>Upcoming</h6>
                <h3 id="upcomingEvents">0</h3>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon ongoing">
                <i class="fas fa-circle"></i>
            </div>
            <div class="stat-info">
                <h6>Ongoing</h6>
                <h3 id="ongoingEvents">0</h3>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon completed">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h6>Completed</h6>
                <h3 id="completedEvents">0</h3>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="filters">
                <div class="form-group">
                    <label class="form-label">Search by Title</label>
                    <input type="text" class="form-control" id="searchInput" placeholder="Search events...">
                </div>
                <div class="form-group">
                    <label class="form-label">Event Type</label>
                    <select class="form-select" id="filterType">
                        <option value="">All Types</option>
                        <option value="Academic">Academic</option>
                        <option value="Sports">Sports</option>
                        <option value="Cultural">Cultural</option>
                        <option value="Social">Social</option>
                        <option value="Workshop">Workshop</option>
                        <option value="Seminar">Seminar</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                   <select class="form-select" id="filterStatus">
    <option value="">All Status</option>
    <option value="upcoming">Upcoming</option>
    <option value="ongoing">Ongoing</option>
    <option value="completed">Completed</option>
    <option value="cancelled">Cancelled</option>
</select>
                </div>
            </div>
        </div>
    </div>

    <!-- Events Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Events List</h5>
        </div>
        <div class="table-responsive">
            <table id="eventsTable">
                <thead>
                    <tr>
                        <th>Event Title</th>
                        <th>Type</th>
                        <th>Date & Time</th>
                        <th>Location</th>
                        <th>Target Audience</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="eventsTableBody">
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fas fa-spinner fa-spin me-2"></i> Loading events...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Event Modal -->
<div class="modal" id="addEventModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalTitle">Add New Event</h5>
                <button type="button" class="btn-close" onclick="closeModal()">&times;</button>
            </div>
            <form id="eventForm" data-custom-submit="true">
                <div class="modal-body">
                    <input type="hidden" id="eventId">
                    
                    <div class="form-group">
                        <label class="form-label">Event Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="eventTitle" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Event Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="eventType" required>
                            <option value="">Select Type</option>
                            <?php foreach ($eventTypes as $type): ?>
                                <option value="<?php echo htmlspecialchars($type); ?>"><?php echo htmlspecialchars($type); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="eventDate" required>
                    </div>

                    <div class="row">
                        <div class="form-group">
                            <label class="form-label">Start Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="startTime" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">End Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="endTime" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Location <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="location" placeholder="e.g., Gymnasium, Auditorium" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="description" rows="3" placeholder="Event details..."></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Target Audience</label>
                        <input type="text" class="form-control" id="targetAudience" placeholder="e.g., Grade 10-12, Faculty, All Students">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="eventStatus" required>
                            <option value="">Select Status</option>
                            <?php foreach ($statuses as $status): ?>
                                <option value="<?php echo htmlspecialchars($status); ?>"><?php echo htmlspecialchars($status); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Save Event
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toastContainer" class="toast-container"></div>

<script>
// ===== GLOBAL STATE =====
const BASE_URL = '/sms/modules/college-coor/api';
const eventData = <?php echo json_encode($events ?? []); ?>;

// Modal functions
function openModal() {
    document.getElementById('addEventModal').classList.add('show');
}

function closeModal() {
    document.getElementById('addEventModal').classList.remove('show');
    resetEventForm();
}

// Initialize event manager (called both on page load and after dynamic page load)
function initializeEventManager() {
    // Get the form and reset its listener flag since it's a new element
    const form = document.getElementById('eventForm');
    if (form) {
        form.dataset.listenerAttached = 'false';
    }

    // Setup Add Event button
    const addBtn = document.getElementById('addEventBtn');
    if (addBtn) {
        addBtn.addEventListener('click', function() {
            openAddEventModal();
        });
    }

    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
        const modal = document.getElementById('addEventModal');
        if (e.target === modal) {
            closeModal();
        }
    });

    attachFormListener();
    attachFilters();
    
    // Always fetch fresh event data from API (important for dynamic page loads)
    refreshEventsData();
}

// Run on initial page load
document.addEventListener('DOMContentLoaded', initializeEventManager);

// Re-run when page is loaded dynamically via page-switcher
window.addEventListener('page:loaded', function(e) {
    if (e.detail && e.detail.page === 'events-management') {
        initializeEventManager();
    }
});

// ===============================
// MODAL CONTROL
// ===============================

// OPEN ADD MODAL (Clean State)
function openAddEventModal() {
    const form = document.getElementById('eventForm');

    if (!form) return;

    // Reset form
    form.reset();
    
    // Clear hidden ID
    document.getElementById('eventId').value = '';
    
    // Set title
    document.getElementById('eventModalTitle').textContent = 'Add New Event';

    // Reset submit button
    const btn = form.querySelector('button[type="submit"]');
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-save me-2"></i> Save Event';

    // Show modal
    openModal();
}

// RESET FORM (on modal close)
function resetEventForm() {
    const form = document.getElementById('eventForm');
    if (!form) return;

    form.reset();
    document.getElementById('eventId').value = '';
    document.getElementById('eventModalTitle').textContent = 'Add New Event';

    const btn = form.querySelector('button[type="submit"]');
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-save me-2"></i> Save Event';
}

// ===============================
// FORM SUBMIT (ADD / UPDATE)
// ===============================
function attachFormListener() {
    const form = document.getElementById('eventForm');
    if (!form) {
        console.error('ERROR: eventForm not found!');
        return;
    }

    // Prevent duplicate listener attachment
    if (form.dataset.listenerAttached === 'true') {
        return;
    }

    form.dataset.listenerAttached = 'true';

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const btn = form.querySelector('button[type="submit"]');
        if (btn.disabled) return;

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

        // Get event ID - check it exists and is a valid number
        const eventIdElement = document.getElementById('eventId');
        
        if (!eventIdElement) {
            console.error('ERROR: eventId element not found!');
            throw new Error('Form element missing');
        }
        
        const eventIdValue = (eventIdElement.value || '').trim();
        const eventId = eventIdValue && eventIdValue !== '' ? parseInt(eventIdValue, 10) : 0;
        const isEdit = !isNaN(eventId) && eventId > 0;

        const payload = {
            event_title: document.getElementById('eventTitle').value,
            event_type: document.getElementById('eventType').value,
            event_date: document.getElementById('eventDate').value,
            start_time: document.getElementById('startTime').value,
            end_time: document.getElementById('endTime').value,
            location: document.getElementById('location').value,
            description: document.getElementById('description').value,
            target_audience: document.getElementById('targetAudience').value,
            status: document.getElementById('eventStatus').value
        };

        // Add event_id only for edits
        if (isEdit) {
            payload.event_id = eventId;
        }

        const url = isEdit
            ? `${BASE_URL}/update_event.php`
            : `${BASE_URL}/add_event.php`;

        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                showToast('Event saved successfully!', 'success');
                closeModal();
                refreshEventsData();
            } else {
                throw new Error(res.message || 'Unknown error occurred');
            }
        })
        .catch(err => {
            showToast('Error saving event: ' + err.message, 'danger');

            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-2"></i> Save Event';
        });
    });
}

// ===============================
// DISPLAY TABLE
// ===============================
function displayEventsData(data = eventData) {
    const tbody = document.getElementById('eventsTableBody');
    if (!tbody) return;

    tbody.innerHTML = '';

    if (!data || !data.length) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-4 text-muted">
                    <i class="fas fa-calendar-times me-2"></i> No events found
                </td>
            </tr>
        `;
        return;
    }

    data.forEach(e => {
        const row = document.createElement('tr');

        row.innerHTML = `
            <td><strong>${escapeHtml(e.event_title || '')}</strong></td>
            <td>${escapeHtml(e.event_type || '-')}</td>
            <td>${formatDate(e.event_date)}<br><small class="text-muted">${e.start_time || ''} - ${e.end_time || ''}</small></td>
            <td>${escapeHtml(e.location || '-')}</td>
            <td>${escapeHtml(e.target_audience || '-')}</td>
            <td>${renderStatusBadge(e.status)}</td>
            <td>
                <button class="btn btn-info btn-sm" onclick="editEvent(${e.event_id})">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-danger btn-sm" onclick="deleteEvent(${e.event_id})">
                    <i class="fas fa-trash"></i> Delete
                </button>
                <button class="btn btn-warning btn-sm" onclick="downloadEventPDF(${e.event_id})">
                    <i class="fas fa-file-pdf"></i> PDF
                </button>
            </td>
        `;

        tbody.appendChild(row);
    });
}

// Helper function to escape HTML
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// EDIT EVENT
function editEvent(id) {
    const event = eventData.find(e => e.event_id == id);
    
    if (!event) {
        console.error('Event not found in eventData for ID:', id);
        return;
    }

    const eventIdInput = document.getElementById('eventId');
    
    if (!eventIdInput) {
        console.error('ERROR: eventId input not found!');
        return;
    }

    eventIdInput.value = event.event_id;
    
    document.getElementById('eventTitle').value = event.event_title || '';
    document.getElementById('eventType').value = event.event_type || '';
    document.getElementById('eventDate').value = event.event_date || '';
    document.getElementById('startTime').value = event.start_time || '';
    document.getElementById('endTime').value = event.end_time || '';
    document.getElementById('location').value = event.location || '';
    document.getElementById('description').value = event.description || '';
    document.getElementById('targetAudience').value = event.target_audience || '';
    document.getElementById('eventStatus').value = event.status || '';

    document.getElementById('eventModalTitle').textContent = 'Edit Event';

    openModal();
}

// DELETE EVENT
function deleteEvent(id) {
    if (!confirm('Are you sure you want to delete this event?')) return;

    fetch(`${BASE_URL}/delete_event.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ event_id: id })
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            showToast('Event deleted successfully!', 'success');
            refreshEventsData();
        } else {
            throw new Error(res.message || 'Delete failed');
        }
    })
    .catch(err => {
        console.error(err);
        showToast('Error deleting event', 'danger');
    });
}

// ===============================
// FILTERS
// ===============================
function attachFilters() {
    const searchInput = document.getElementById('searchInput');
    const filterType = document.getElementById('filterType');
    const filterStatus = document.getElementById('filterStatus');
    
    if (searchInput) searchInput.addEventListener('keyup', filterEvents);
    if (filterType) filterType.addEventListener('change', filterEvents);
    if (filterStatus) filterStatus.addEventListener('change', filterEvents);
}

function filterEvents() {
    const searchInput = document.getElementById('searchInput');
    const filterType = document.getElementById('filterType');
    const filterStatus = document.getElementById('filterStatus');
    
    const search = searchInput ? searchInput.value.toLowerCase() : '';
    const type = filterType ? filterType.value : '';
    const status = filterStatus ? filterStatus.value : '';

    const filtered = eventData.filter(e =>
        (!search || (e.event_title && e.event_title.toLowerCase().includes(search))) &&
        (!type || e.event_type === type) &&
        (!status || e.status === status)
    );

    displayEventsData(filtered);
}

// ===============================
// REFRESH DATA
// ===============================
function refreshEventsData() {
    fetch(`${BASE_URL}/get_events.php`)
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                eventData.length = 0;
                eventData.push(...(res.events || []));
                displayEventsData();
                updateEventStatistics();
            }
        })
        .catch(err => {
            showToast('Failed to refresh events', 'danger');
        });
}

// ===============================
// STATISTICS
// ===============================
function updateEventStatistics() {
    const totalEl = document.getElementById('totalEvents');
    const upcomingEl = document.getElementById('upcomingEvents');
    const ongoingEl = document.getElementById('ongoingEvents');
    const completedEl = document.getElementById('completedEvents');
    
    if (!eventData) return;
    
    const total = eventData.length;
    const scheduled = eventData.filter(e => e.status === 'upcoming').length;
const ongoing = eventData.filter(e => e.status === 'ongoing').length;
const completed = eventData.filter(e => e.status === 'completed').length;

    if (totalEl) totalEl.textContent = total;
    if (upcomingEl) upcomingEl.textContent = scheduled;
    if (ongoingEl) ongoingEl.textContent = ongoing;
    if (completedEl) completedEl.textContent = completed;
}

// ===============================
// HELPERS
// ===============================
function renderStatusBadge(status) {
    const map = {
    'upcoming': 'secondary',
    'ongoing': 'primary',
    'completed': 'success',
    'cancelled': 'danger'
};

    const color = map[status] || 'secondary';
    return `<span class="badge bg-${color}">${escapeHtml(status || 'Unknown')}</span>`;
}

function formatDate(dateStr) {
    if (!dateStr) return '-';
    try {
        return new Date(dateStr).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    } catch (e) {
        return dateStr;
    }
}

// ===============================
// TOAST NOTIFICATION
// ===============================
function showToast(message, type = 'info', duration = 4000) {
    const container = document.getElementById('toastContainer');
    if (!container) {
        return;
    }
    
    const toastId = 'toast-' + Date.now();
    
    const html = `<div id="${toastId}" class="toast ${type}">${message}</div>`;
    
    container.insertAdjacentHTML('beforeend', html);
    
    // Auto remove after specified duration
    setTimeout(() => {
        const toast = document.getElementById(toastId);
        if (toast) {
            toast.style.animation = 'slideOutTop 0.3s ease-out';
            setTimeout(() => {
                if (toast && toast.parentElement) {
                    toast.remove();
                }
            }, 300);
        }
    }, duration);
}

// ===============================
// PDF DOWNLOAD
// ===============================
function downloadEventPDF(id) {
    const event = eventData.find(e => e.event_id == id);
    
    if (!event) {
        showToast('Event not found', 'danger');
        return;
    }

    // Create form and submit to download endpoint
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `${BASE_URL}/download_event_pdf.php`;
    
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'event_id';
    input.value = id;
    
    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);

    showToast('Generating PDF...', 'info', 2000);
}
</script>
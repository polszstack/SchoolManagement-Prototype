<?php
// Dashboard Overview Page - Main analytics and summary for College Coordinator
require_once dirname(__DIR__) . '/classes/DatabaseHelper.php';

$helper = new DatabaseHelper();

// Fetch upcoming appointments and events
$upcomingEvents = $helper->getUpcomingEvents(5);

// Fetch calendar events with "upcoming" status
$calendarEventsData = $helper->getCalendarEvents();

// Format calendar events by date for JavaScript
$eventsByDate = [];
foreach ($calendarEventsData as $event) {
    $eventDate = $event['event_date'];
    if (!isset($eventsByDate[$eventDate])) {
        $eventsByDate[$eventDate] = [];
    }
    
    $eventTitle = $event['event_title'];
    if (!empty($event['start_time'])) {
        $eventTitle .= ' - ' . $event['start_time'];
    }
    
    $eventsByDate[$eventDate][] = $eventTitle;
}

// Fetch recent activities from all modules
$recentActivities = $helper->getRecentActivities(15);

// ===== Chart Data =====

// 1. Students per Program
$studentsPerProgram = $helper->getStudentsPerProgram();
$programLabels = array_map(function($item) { return $item['program_code']; }, $studentsPerProgram);
$programData = array_map(function($item) { return (int)$item['student_count']; }, $studentsPerProgram);

// 2. Faculty Load Distribution
$facultyLoad = $helper->getFacultyLoadDistribution();
$facultyLabels = array_map(function($item) { return substr($item['faculty_name'], 0, 15); }, $facultyLoad);
$facultyUnits = array_map(function($item) { return (int)$item['total_units']; }, $facultyLoad);

// 3. Student Academic Status
$studentStatusData = $helper->getStudentAcademicStatus();
$statusLabels = array_map(function($item) { return $item['status']; }, $studentStatusData);
$statusCounts = array_map(function($item) { return (int)$item['count']; }, $studentStatusData);

// Convert to JSON for JavaScript
$chartDataJson = json_encode([
    'programLabels' => $programLabels,
    'programData' => $programData,
    'facultyLabels' => $facultyLabels,
    'facultyUnits' => $facultyUnits,
    'statusLabels' => $statusLabels,
    'statusCounts' => $statusCounts
]);
?>

<div class="dashboard-container">
    <!-- Analytics Charts Section -->
    <section class="analytics-section">
        <h2 class="section-title">Academic Analytics</h2>
        
        <div class="charts-grid">
            <!-- Students per Program Chart -->
            <div class="chart-container">
                <h3 class="chart-title">Students per Program</h3>
                <div class="chart-wrapper">
                    <canvas id="studentsPerProgramChart"></canvas>
                </div>
            </div>

            <!-- Faculty Load Distribution Chart -->
            <div class="chart-container">
                <h3 class="chart-title">Faculty Load Distribution</h3>
                <div id="faultyLoadSummary" class="chart-subtitle" style="font-size: 13px; color: #6b7280; margin: 4px 0 10px;">Loading faulty load count…</div>
                <div class="chart-wrapper">
                    <canvas id="facultyLoadChart"></canvas>
                </div>
            </div>

            <!-- Student Academic Status Chart -->
            <div class="chart-container">
                <h3 class="chart-title">Student Academic Status</h3>
                <div class="chart-wrapper">
                    <canvas id="studentStatusChart"></canvas>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content Grid -->
    <div class="dashboard-grid">
        <!-- Upcoming Events / Academic Calendar -->
        <section class="upcoming-events-section">
            <div class="section-header">
                <h2 class="section-title">Academic Events Calendar</h2>
            </div>
            
            <div class="calendar-container">
                <div class="calendar-header">
                    <button id="prevMonth" class="btn-nav" onclick="previousMonth()">← Previous</button>
                    <h3 id="monthYear">March 2026</h3>
                    <button id="nextMonth" class="btn-nav" onclick="nextMonth()">Next →</button>
                </div>
                
                <div class="calendar-grid">
                    <div class="calendar-day-header">Sun</div>
                    <div class="calendar-day-header">Mon</div>
                    <div class="calendar-day-header">Tue</div>
                    <div class="calendar-day-header">Wed</div>
                    <div class="calendar-day-header">Thu</div>
                    <div class="calendar-day-header">Fri</div>
                    <div class="calendar-day-header">Sat</div>
                    
                    <div id="calendarDays" class="calendar-days">
                        <!-- Days will be populated by JavaScript -->
                    </div>
                </div>
            </div>
        </section>

        <!-- Events Modal -->
        <div id="eventsModal" class="events-modal" style="display: none;">
            <div class="modal-content">
                <button class="modal-close" onclick="closeEventsModal()">&times;</button>
                <h2 id="modalDateTitle">Events</h2>
                <div id="modalEventsList" class="modal-events-list">
                    <!-- Events will be populated by JavaScript -->
                </div>
            </div>
        </div>

        <style>
            .calendar-container {
                background: white;
                border-radius: 8px;
                padding: 20px;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            }

            .calendar-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 20px;
            }

            .calendar-header h3 {
                font-size: 18px;
                font-weight: 600;
                color: #1f2937;
                margin: 0;
            }

            .btn-nav {
                background: #3b82f6;
                color: white;
                border: none;
                padding: 8px 16px;
                border-radius: 5px;
                cursor: pointer;
                font-size: 14px;
                transition: background 0.3s;
            }

            .btn-nav:hover {
                background: #2563eb;
            }

            .calendar-grid {
                display: grid;
                grid-template-columns: repeat(7, 1fr);
                gap: 1px;
                background: #e5e7eb;
                padding: 1px;
                border-radius: 8px;
                overflow: hidden;
            }

            .calendar-day-header {
                background: #f3f4f6;
                padding: 10px;
                text-align: center;
                font-weight: 600;
                font-size: 12px;
                color: #6b7280;
                text-transform: uppercase;
            }

            .calendar-days {
                display: contents;
            }

            .calendar-day {
                background: white;
                padding: 12px 8px;
                min-height: 80px;
                cursor: pointer;
                transition: background 0.3s;
                position: relative;
            }

            .calendar-day:hover {
                background: #f0f9ff;
            }

            .calendar-day.other-month {
                background: #f9fafb;
                color: #d1d5db;
            }

            .calendar-day.today {
                background: #dbeafe;
                border: 2px solid #3b82f6;
            }

            .calendar-day-number {
                font-weight: 600;
                font-size: 14px;
                color: #1f2937;
                margin-bottom: 5px;
            }

            .calendar-day.other-month .calendar-day-number {
                color: #d1d5db;
            }

            .calendar-events {
                font-size: 11px;
                color: #3b82f6;
                overflow: hidden;
            }

            .calendar-event-dot {
                display: inline-block;
                width: 5px;
                height: 5px;
                background: #3b82f6;
                border-radius: 50%;
                margin-right: 3px;
            }

            .events-modal {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1000;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .modal-content {
                background: white;
                border-radius: 8px;
                padding: 30px;
                max-width: 500px;
                width: 90%;
                max-height: 80vh;
                overflow-y: auto;
                position: relative;
            }

            .modal-close {
                position: absolute;
                top: 15px;
                right: 15px;
                background: none;
                border: none;
                font-size: 24px;
                cursor: pointer;
                color: #6b7280;
            }

            .modal-close:hover {
                color: #1f2937;
            }

            #modalDateTitle {
                margin-top: 0;
                margin-bottom: 20px;
                color: #1f2937;
                font-size: 20px;
            }

            .modal-events-list {
                display: flex;
                flex-direction: column;
                gap: 15px;
            }

            .modal-event-item {
                padding: 15px;
                background: #f3f4f6;
                border-left: 4px solid #3b82f6;
                border-radius: 5px;
            }

            .modal-event-title {
                font-weight: 600;
                color: #1f2937;
                margin-bottom: 5px;
            }

            .modal-event-desc {
                font-size: 13px;
                color: #6b7280;
            }

            .modal-event-empty {
                text-align: center;
                color: #6b7280;
                padding: 20px;
            }
        </style>

        <script>
            const eventsByDate = <?php echo json_encode($eventsByDate); ?>;

            let currentDate = new Date(2026, 2, 18); // March 18, 2026

            function renderCalendar() {
                const year = currentDate.getFullYear();
                const month = currentDate.getMonth();
                
                // Update header
                const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'];
                document.getElementById('monthYear').textContent = `${monthNames[month]} ${year}`;
                
                // Get first day of month and number of days
                const firstDay = new Date(year, month, 1).getDay();
                const daysInMonth = new Date(year, month + 1, 0).getDate();
                const daysInPrevMonth = new Date(year, month, 0).getDate();
                
                const calendarDays = document.getElementById('calendarDays');
                calendarDays.innerHTML = '';
                
                // Previous month days
                for (let i = firstDay - 1; i >= 0; i--) {
                    const dayDiv = document.createElement('div');
                    dayDiv.className = 'calendar-day other-month';
                    dayDiv.innerHTML = `<div class="calendar-day-number">${daysInPrevMonth - i}</div>`;
                    calendarDays.appendChild(dayDiv);
                }
                
                // Current month days
                for (let day = 1; day <= daysInMonth; day++) {
                    const dayDiv = document.createElement('div');
                    dayDiv.className = 'calendar-day';
                    const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                    
                    // Check if today
                    const today = new Date();
                    if (day === today.getDate() && month === today.getMonth() && year === today.getFullYear()) {
                        dayDiv.classList.add('today');
                    }
                    
                    let html = `<div class="calendar-day-number">${day}</div>`;
                    
                    if (eventsByDate[dateStr]) {
                        html += '<div class="calendar-events"><span class="calendar-event-dot"></span> Events</div>';
                    }
                    
                    dayDiv.innerHTML = html;
                    dayDiv.onclick = () => showEventsModal(dateStr, day);
                    calendarDays.appendChild(dayDiv);
                }
                
                // Next month days
                const totalCells = calendarDays.children.length + firstDay;
                const remainingCells = 42 - totalCells;
                for (let day = 1; day <= remainingCells; day++) {
                    const dayDiv = document.createElement('div');
                    dayDiv.className = 'calendar-day other-month';
                    dayDiv.innerHTML = `<div class="calendar-day-number">${day}</div>`;
                    calendarDays.appendChild(dayDiv);
                }
            }

            function previousMonth() {
                currentDate.setMonth(currentDate.getMonth() - 1);
                renderCalendar();
            }

            function nextMonth() {
                currentDate.setMonth(currentDate.getMonth() + 1);
                renderCalendar();
            }

            function showEventsModal(dateStr, day) {
                const modal = document.getElementById('eventsModal');
                const title = document.getElementById('modalDateTitle');
                const eventsList = document.getElementById('modalEventsList');
                
                const date = new Date(dateStr + 'T00:00:00');
                const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'];
                title.textContent = `${monthNames[date.getMonth()]} ${date.getDate()}, ${date.getFullYear()}`;
                
                const events = eventsByDate[dateStr] || [];
                if (events.length === 0) {
                    eventsList.innerHTML = '<div class="modal-event-empty">No events scheduled for this day</div>';
                } else {
                    eventsList.innerHTML = events.map((event, idx) => {
                        const [title, desc] = event.split(' - ');
                        return `
                            <div class="modal-event-item">
                                <div class="modal-event-title">${title}</div>
                                <div class="modal-event-desc">${desc || 'Academic event'}</div>
                            </div>
                        `;
                    }).join('');
                }
                
                modal.style.display = 'flex';
            }

            function closeEventsModal() {
                document.getElementById('eventsModal').style.display = 'none';
            }

            window.onclick = function(event) {
                const modal = document.getElementById('eventsModal');
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            };

            // Initial render
            renderCalendar();
        </script>

        <!-- Recent Activities -->
        <section class="recent-activities-section">
            <div class="section-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h2 class="section-title">Recent Activities</h2>
                <button onclick="openActivitiesModal()" style="padding: 6px 12px; background-color: #3b82f6; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 13px;">View All</button>
            </div>
            
            <div class="activities-list" id="activitiesList">
                <?php if (empty($recentActivities)): ?>
                    <div class="activity-item" style="padding: 15px; text-align: center; color: #999;">
                        No recent activities yet
                    </div>
                <?php else: ?>
                    <?php foreach (array_slice($recentActivities, 0, 6) as $activity): ?>
                        <div class="activity-item" style="padding: 12px; border-radius: 5px; background-color: #f0f9ff; border-left: 4px solid #3b82f6; margin-bottom: 8px;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 10px;">
                                <div style="flex: 1;">
                                    <div style="font-weight: 600; color: #1f2937; font-size: 14px;">
                                        <?php echo htmlspecialchars($activity['type']); ?>
                                    </div>
                                    <div style="color: #4b5563; font-size: 13px; margin: 4px 0;">
                                        <?php echo htmlspecialchars($activity['description']); ?>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <span style="font-size: 11px; color: #6b7280;"><?php echo htmlspecialchars($activity['module']); ?></span>
                                        <span style="font-size: 11px; color: #9ca3af;">
                                            <?php 
                                                $timestamp = strtotime($activity['timestamp']);
                                                $diff = time() - $timestamp;
                                                if ($diff < 60) echo 'just now';
                                                elseif ($diff < 3600) echo floor($diff / 60) . 'm ago';
                                                elseif ($diff < 86400) echo floor($diff / 3600) . 'h ago';
                                                else echo date('M d, Y', $timestamp);
                                            ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <!-- Activities Modal -->
        <div id="activitiesModal" class="activities-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000; align-items: center; justify-content: center;">
            <div style="background: white; border-radius: 8px; padding: 30px; max-width: 600px; width: 90%; max-height: 80vh; overflow-y: auto; position: relative;">
                <button onclick="closeActivitiesModal()" style="position: absolute; top: 15px; right: 15px; background: none; border: none; font-size: 24px; cursor: pointer; color: #6b7280;">&times;</button>
                
                <h2 style="margin-top: 0; margin-bottom: 20px; color: #1f2937; font-size: 22px;">All Recent Activities</h2>
                
                <div id="allActivitiesList" style="display: flex; flex-direction: column; gap: 10px;">
                    <!-- Activities will be populated by JavaScript -->
                </div>
            </div>
        </div>

        <script>
            // All activities data for the modal
            const allActivities = <?php echo json_encode($recentActivities); ?>;

            function openActivitiesModal() {
                const modal = document.getElementById('activitiesModal');
                const list = document.getElementById('allActivitiesList');
                
                // Populate modal with all activities
                if (allActivities.length === 0) {
                    list.innerHTML = '<div style="padding: 20px; text-align: center; color: #999;">No recent activities yet</div>';
                } else {
                    list.innerHTML = allActivities.map(activity => {
                        const timestamp = new Date(activity.timestamp);
                        const diff = Math.floor((Date.now() - timestamp.getTime()) / 1000);
                        let timeStr;
                        if (diff < 60) timeStr = 'just now';
                        else if (diff < 3600) timeStr = Math.floor(diff / 60) + 'm ago';
                        else if (diff < 86400) timeStr = Math.floor(diff / 3600) + 'h ago';
                        else timeStr = timestamp.toLocaleDateString();

                        return `
                            <div style="padding: 12px; border-radius: 5px; background-color: #f0f9ff; border-left: 4px solid #3b82f6; margin-bottom: 8px;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 10px;">
                                    <div style="flex: 1;">
                                        <div style="font-weight: 600; color: #1f2937; font-size: 14px;">
                                            ${activity.type}
                                        </div>
                                        <div style="color: #4b5563; font-size: 13px; margin: 4px 0;">
                                            ${activity.description}
                                        </div>
                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                            <span style="font-size: 11px; color: #6b7280;">${activity.module}</span>
                                            <span style="font-size: 11px; color: #9ca3af;">${timeStr}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    }).join('');
                }
                
                modal.style.display = 'flex';
            }

            function closeActivitiesModal() {
                document.getElementById('activitiesModal').style.display = 'none';
            }

            // Close modal when clicking outside
            document.getElementById('activitiesModal').addEventListener('click', function(event) {
                if (event.target === this) {
                    closeActivitiesModal();
                }
            });
        </script>
    </div>

    

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>

<script>
    // Chart data from PHP
    const chartData = <?php echo $chartDataJson; ?>;

    // Keep a reference so we can update later
    let facultyLoadChart = null;

    function initStudentsPerProgramChart() {
        const ctx1 = document.getElementById('studentsPerProgramChart').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: chartData.programLabels,
                datasets: [{
                    label: 'Number of Students',
                    data: chartData.programData,
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#3498db',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    const facultyLoadLabels = ['Underloaded', 'Fully Loaded', 'Overloaded'];

    function computeLoadCounts(unitsArray, maxLoad = 15) {
        const counts = { under: 0, full: 0, over: 0 };
        unitsArray.forEach(u => {
            const total = Number(u) || 0;
            if (total < maxLoad) counts.under += 1;
            else if (total === maxLoad) counts.full += 1;
            else counts.over += 1;
        });
        return counts;
    }

    function updateFaultyLoadSummary(counts) {
        const el = document.getElementById('faultyLoadSummary');
        if (!el) return;
        const faulty = (counts.under || 0) + (counts.over || 0);
        el.textContent = `Faulty load count: ${faulty}`;
    }

    function initFacultyLoadChart(initialUnits, maxLoad = 15) {
        const ctx2 = document.getElementById('facultyLoadChart').getContext('2d');
        const loadCounts = computeLoadCounts(initialUnits, maxLoad);
        updateFaultyLoadSummary(loadCounts);

        facultyLoadChart = new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: facultyLoadLabels,
                datasets: [{
                    label: 'Faculty Count',
                    data: [loadCounts.under, loadCounts.full, loadCounts.over],
                    backgroundColor: ['#f59e0b', '#10b981', '#ef4444'],
                    borderColor: ['#d97706', '#059669', '#dc2626'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    async function refreshFacultyLoadChart() {
        try {
            const res = await fetch('/sms/modules/college-coor/api/get_faculty_load.php', { credentials: 'same-origin' });
            if (!res.ok) throw new Error('Failed to load faculty load data');
            const data = await res.json();

            const units = data.map(item => Number(item.total_units) || 0);
            const maxLoad = data.length ? (Number(data[0].max_load) || 15) : 15;

            if (!facultyLoadChart) {
                initFacultyLoadChart(units, maxLoad);
                return;
            }

            const counts = computeLoadCounts(units, maxLoad);
            updateFaultyLoadSummary(counts);
            facultyLoadChart.data.datasets[0].data = [counts.under, counts.full, counts.over];
            facultyLoadChart.update();
        } catch (error) {
            console.error('Error refreshing faculty load chart:', error);
        }
    }

    // Ensure other modules can trigger an update
    window.refreshFacultyLoadChart = refreshFacultyLoadChart;

    function initDashboardCharts() {
        initStudentsPerProgramChart();
        initFacultyLoadChart(chartData.facultyUnits);
    }

    document.addEventListener('DOMContentLoaded', function() {
        initDashboardCharts();

        if (sessionStorage.getItem('refreshFacultyLoad') === 'true') {
            sessionStorage.removeItem('refreshFacultyLoad');
            refreshFacultyLoadChart();
        }
    });

    window.addEventListener('page:loaded', function(e) {
        if (e.detail && e.detail.page === 'dashboard-overview') {
            if (sessionStorage.getItem('refreshFacultyLoad') === 'true') {
                sessionStorage.removeItem('refreshFacultyLoad');
                refreshFacultyLoadChart();
            }
        }
    });

    // If another tab updated faculty load, refresh this chart as well
    window.addEventListener('storage', function (e) {
        if (e.key === 'refreshFacultyLoad' && e.newValue === 'true') {
            refreshFacultyLoadChart();
        }
    });

    // 3. Student Academic Status Chart
    const ctx3 = document.getElementById('studentStatusChart').getContext('2d');
    new Chart(ctx3, {
        type: 'pie',
        data: {
            labels: chartData.statusLabels,
            datasets: [{
                data: chartData.statusCounts,
                backgroundColor: [
                    '#2ecc71', '#e74c3c', '#95a5a6'
                ],
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                }
            }
        }
    });
</script>

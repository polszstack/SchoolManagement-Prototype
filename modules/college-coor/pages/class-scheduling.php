<?php
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once dirname(__DIR__, 3) . '/database/db.php';
require_once dirname(__DIR__) . '/classes/Schedule.php';
require_once dirname(__DIR__) . '/classes/Faculty.php';
require_once dirname(__DIR__) . '/classes/SectionManager.php';

$database = new Database();
$db = $database->getConnection();

// Check database connection
if (!$db) {
    die("Database connection failed");
}

$schedule = new Schedule($db);
$faculty = new Faculty($db);
$section = new SectionManager($db);

// Default filter values
$semester = isset($_GET['semester']) ? $_GET['semester'] : '2nd Sem';
$school_year = isset($_GET['school_year']) ? $_GET['school_year'] : '2025-2026';
$day_of_week = isset($_GET['day_of_week']) ? $_GET['day_of_week'] : '';
$room = isset($_GET['room']) ? $_GET['room'] : '';

// Handle form submissions - FIXED HERE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if this is an add schedule submission (either by button or by having room field)
    if (isset($_POST['add_schedule']) || isset($_POST['room'])) {
        try {
            // Convert time format from HH:MM to HH:MM:SS
            $start_time = $_POST['start_time'] . ':00';
            $end_time = $_POST['end_time'] . ':00';
            
            // Add new schedule using Schedule class
            $schedule->room = trim($_POST['room']);
            $schedule->official_time = trim($_POST['official_time']);
            $schedule->start_time = $start_time;
            $schedule->end_time = $end_time;
            $schedule->day_of_week = trim($_POST['day_of_week']);
            $schedule->subject_code = trim($_POST['subject_code']);
            $schedule->grade_section_id = (int)$_POST['grade_section_id'];
            $schedule->faculty_id = (int)$_POST['faculty_id'];
            $schedule->semester = trim($_POST['semester']);
            $schedule->school_year = trim($_POST['school_year']);
            
            if ($schedule->create()) {
                echo "<script>alert('Schedule added successfully!'); sessionStorage.setItem('refreshFacultyLoad', 'true'); window.location.href=window.location.href;</script>";
                exit;
            } else {
                $errorInfo = $db->errorInfo();
                throw new Exception("Failed to add schedule. Error: " . ($errorInfo[2] ?? 'Unknown error'));
            }
        } catch (Exception $e) {
            echo "<script>alert('Error: " . addslashes($e->getMessage()) . "');</script>";
        }
    }
    
    if (isset($_POST['update_schedule'])) {
        try {
            // Convert time format from HH:MM to HH:MM:SS
            $start_time = $_POST['start_time'] . ':00';
            $end_time = $_POST['end_time'] . ':00';
            
            // Update schedule using Schedule class
            $schedule->id = (int)$_POST['schedule_id'];
            $schedule->room = trim($_POST['room']);
            $schedule->official_time = trim($_POST['official_time']);
            $schedule->start_time = $start_time;
            $schedule->end_time = $end_time;
            $schedule->day_of_week = trim($_POST['day_of_week']);
            $schedule->subject_code = trim($_POST['subject_code']);
            $schedule->grade_section_id = (int)$_POST['grade_section_id'];
            $schedule->faculty_id = (int)$_POST['faculty_id'];
            $schedule->semester = trim($_POST['semester']);
            $schedule->school_year = trim($_POST['school_year']);
            
            if ($schedule->update()) {
                echo "<script>alert('Schedule updated successfully!'); sessionStorage.setItem('refreshFacultyLoad', 'true'); window.location.href=window.location.href;</script>";
                exit;
            } else {
                $errorInfo = $db->errorInfo();
                throw new Exception("Failed to update schedule. Error: " . ($errorInfo[2] ?? 'Unknown error'));
            }
        } catch (Exception $e) {
            echo "<script>alert('Error: " . addslashes($e->getMessage()) . "');</script>";
        }
    }
    
    if (isset($_POST['delete_schedule'])) {
        try {
            $schedule->id = (int)$_POST['schedule_id'];
            if ($schedule->delete()) {
                echo "<script>alert('Schedule deleted successfully!'); sessionStorage.setItem('refreshFacultyLoad', 'true'); window.location.href=window.location.href;</script>";
                exit;
            } else {
                throw new Exception("Failed to delete schedule.");
            }
        } catch (Exception $e) {
            echo "<script>alert('Error: " . addslashes($e->getMessage()) . "');</script>";
        }
    }
}

// Get all schedules using the new method
$schedules_result = $schedule->getAllWithFilters($semester, $school_year, $day_of_week, $room);
$schedules = $schedules_result ? $schedules_result->fetchAll(PDO::FETCH_ASSOC) : [];

// Get distinct values for filters using the new methods
$distinct_days_result = $schedule->getDistinctDays($semester, $school_year);
$distinct_days = $distinct_days_result ? $distinct_days_result->fetchAll() : [];

$distinct_rooms_result = $schedule->getDistinctRooms($semester, $school_year);
$distinct_rooms = $distinct_rooms_result ? $distinct_rooms_result->fetchAll() : [];

// Get data for dropdowns
$all_faculty = $db->query("SELECT id, faculty_code, first_name, last_name FROM cc_faculty ORDER BY last_name")->fetchAll();
$all_sections = $db->query("SELECT id, section_code, grade_level, program FROM cc_sections ORDER BY section_code")->fetchAll();

// Calculate statistics using the new method
$stats = $schedule->getStatistics($semester, $school_year);
$total_schedules = $stats['total_schedules'] ?? 0;
$rooms_count = $stats['total_rooms'] ?? 0;
$faculty_count = $stats['total_faculty'] ?? 0;
$sections_count = $stats['total_sections'] ?? 0;

// Get weekly summary
$weekly_summary_result = $schedule->getWeeklySummary($semester, $school_year);
$weekly_summary = $weekly_summary_result ? $weekly_summary_result->fetchAll(PDO::FETCH_ASSOC) : [];

// Create daily summary from weekly_summary
$daily_summary = [];
foreach ($weekly_summary as $item) {
    $daily_summary[$item['day_of_week']] = $item['total_classes'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3a0ca3;
            --success: #4cc9f0;
            --warning: #f72585;
            --light: #f8f9fa;
            --dark: #212529;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #f5f7ff;
            color: var(--dark);
            padding: 20px;
            min-height: 100vh;
        }

        .container { max-width: 1600px; margin: 0 auto; }

        .header {
            background: white;
            border-radius: var(--border-radius);
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: var(--box-shadow);
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .header h1 { color: var(--primary); margin-bottom: 10px; font-size: 2.2rem; }
        .header p { color: #666; font-size: 1.1rem; }

        /* Stats */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .stat-card:hover { transform: translateY(-5px); }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary);
            margin: 10px 0;
        }

        .stat-label { color: #666; font-size: 0.9rem; }

        /* Tabs */
        .tabs {
            display: flex;
            background: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            margin-bottom: 25px;
            box-shadow: var(--box-shadow);
        }

        .tab {
            flex: 1;
            padding: 15px 20px;
            text-align: center;
            cursor: pointer;
            border: none;
            background: none;
            font-size: 1rem;
            font-weight: 600;
            color: #666;
            transition: all 0.3s ease;
        }

        .tab:hover { background: #f8f9fa; }
        .tab.active { background: var(--primary); color: white; }

        /* Tab Content */
        .tab-content {
            display: none;
            background: white;
            padding: 25px;
            border-radius: var(--border-radius);
            margin-bottom: 25px;
            box-shadow: var(--box-shadow);
        }

        .tab-content.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Filters */
        .filters {
            background: #f8f9fa;
            padding: 20px;
            border-radius: var(--border-radius);
            margin-bottom: 25px;
        }

        .filter-group {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: flex-end;
        }

        .filter-item { flex: 1; min-width: 200px; }

        label { display: block; margin-bottom: 5px; font-weight: 600; color: #555; }

        select, input, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        .btn {
            padding: 10px 25px;
            border: none;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: var(--secondary); transform: translateY(-2px); }

        .btn-success { background: #2ec4b6; color: white; }
        .btn-success:hover { background: #25a195; transform: translateY(-2px); }

        .btn-warning { background: #ff9f1c; color: white; }
        .btn-warning:hover { background: #e68a00; transform: translateY(-2px); }

        .btn-danger { background: #e71d36; color: white; }
        .btn-danger:hover { background: #c81d36; transform: translateY(-2px); }

        /* Table */
        .table-container {
            overflow-x: auto;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            max-height: 600px;
            overflow-y: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1200px;
        }

        .data-table th {
            background: var(--primary);
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .data-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }

        .data-table tr:hover { background: #f8f9fa; }

        .data-table tr:nth-child(even) { background: #fafafa; }

        /* Badges */
        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
        }

        .badge-primary { background: #e3f2fd; color: #1976d2; }
        .badge-success { background: #e8f5e9; color: #388e3c; }
        .badge-warning { background: #fff3cd; color: #856404; }

        /* Forms */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .form-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: var(--border-radius);
            border-left: 4px solid var(--primary);
        }

        .form-card h3 { margin-bottom: 15px; color: var(--primary); }

        .form-group { margin-bottom: 15px; }

        /* Import Section */
        .import-box {
            background: #f0f7ff;
            border: 2px dashed #4cc9f0;
            border-radius: var(--border-radius);
            padding: 30px;
            text-align: center;
            margin-bottom: 25px;
        }

        .csv-template {
            background: #f8f9fa;
            padding: 15px;
            border-radius: var(--border-radius);
            margin-top: 15px;
            font-family: monospace;
            font-size: 0.9rem;
            overflow-x: auto;
        }

        /* Summary */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
            margin: 20px 0;
        }

        .summary-item {
            background: white;
            padding: 15px;
            border-radius: var(--border-radius);
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .summary-day { font-weight: 600; color: var(--primary); }
        .summary-count { font-size: 1.5rem; font-weight: 700; margin-top: 5px; }

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
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: var(--border-radius);
            max-width: 800px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #666;
        }

        .close-modal:hover { color: var(--danger); }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 40px;
            padding: 20px;
            color: #666;
            border-top: 1px solid #eee;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .filter-group { flex-direction: column; }
            .filter-item { min-width: 100%; }
            .tabs { flex-direction: column; }
        }

        .video-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
            opacity: 0.15;
        }
        
        .video-background video {
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            object-fit: cover;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <div class="video-background">
        <video autoplay muted loop playsinline>
            <source src="https://assets.mixkit.co/videos/preview/mixkit-college-students-walking-on-campus-47870-large.mp4" type="video/mp4">
        </video>
    </div>
    
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1><i class="fas fa-calendar-alt"></i> Schedule Management System</h1>
            <p>Office of the Safety and Security | Class Schedule Management</p>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-calendar-check fa-2x" style="color: #4361ee;"></i>
                <div class="stat-value"><?= $total_schedules ?></div>
                <div class="stat-label">Total Classes</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-door-open fa-2x" style="color: #3a0ca3;"></i>
                <div class="stat-value"><?= $rooms_count ?></div>
                <div class="stat-label">Rooms Used</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-chalkboard-teacher fa-2x" style="color: #4cc9f0;"></i>
                <div class="stat-value"><?= $faculty_count ?></div>
                <div class="stat-label">Faculty Scheduled</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-users fa-2x" style="color: #f72585;"></i>
                <div class="stat-value"><?= $sections_count ?></div>
                <div class="stat-label">Sections</div>
            </div>
        </div>

        <!-- Daily Summary -->
        <?php if (!empty($daily_summary)): ?>
        <div class="form-card">
            <h3><i class="fas fa-chart-bar"></i> Weekly Schedule Distribution</h3>
            <div class="summary-grid">
                <?php 
                $days_order = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                foreach ($days_order as $day): 
                    $count = $daily_summary[$day] ?? 0;
                ?>
                <div class="summary-item">
                    <div class="summary-day"><?= $day ?></div>
                    <div class="summary-count"><?= $count ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Tabs -->
        <div class="tabs">
            <button class="tab <?= !isset($_GET['action']) || $_GET['action'] == 'view' ? 'active' : '' ?>" onclick="showTab('view')">
                <i class="fas fa-eye"></i> View Schedules
            </button>
            <button class="tab <?= isset($_GET['action']) && $_GET['action'] == 'add' ? 'active' : '' ?>" onclick="showTab('add')">
                <i class="fas fa-plus-circle"></i> Add Schedule
            </button>
            <button class="tab <?= isset($_GET['action']) && $_GET['action'] == 'import' ? 'active' : '' ?>" onclick="showTab('import')">
                <i class="fas fa-file-import"></i> Import CSV
            </button>
        </div>

        <!-- Filters -->
        <div class="filters">
            <form method="GET" class="filter-group">
                <div class="filter-item">
                    <label><i class="fas fa-graduation-cap"></i> Semester</label>
                    <select name="semester">
                        <option value="1st Sem" <?= $semester == '1st Sem' ? 'selected' : '' ?>>1st Semester</option>
                        <option value="2nd Sem" <?= $semester == '2nd Sem' ? 'selected' : '' ?>>2nd Semester</option>
                        <option value="Summer" <?= $semester == 'Summer' ? 'selected' : '' ?>>Summer</option>
                    </select>
                </div>
                <div class="filter-item">
                    <label><i class="fas fa-calendar-alt"></i> School Year</label>
                    <input type="text" name="school_year" value="<?= htmlspecialchars($school_year) ?>" placeholder="YYYY-YYYY">
                </div>
                <div class="filter-item">
                    <label><i class="fas fa-calendar-day"></i> Day of Week</label>
                    <select name="day_of_week">
                        <option value="">All Days</option>
                        <?php foreach ($distinct_days as $day): ?>
                            <option value="<?= htmlspecialchars($day['day_of_week']) ?>" <?= $day_of_week == $day['day_of_week'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($day['day_of_week']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-item">
                    <label><i class="fas fa-door-open"></i> Room</label>
                    <select name="room">
                        <option value="">All Rooms</option>
                        <?php foreach ($distinct_rooms as $room_item): ?>
                            <option value="<?= htmlspecialchars($room_item['room']) ?>" <?= $room == $room_item['room'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($room_item['room']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-item">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                    <a href="schedule_manager.php" class="btn btn-warning">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- View Schedules Tab -->
        <div id="view-tab" class="tab-content <?= !isset($_GET['action']) || $_GET['action'] == 'view' ? 'active' : '' ?>">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2><i class="fas fa-list"></i> All Schedules (<?= $total_schedules ?>)</h2>
                <div>
                    <button onclick="exportToCSV()" class="btn btn-success">
                        <i class="fas fa-file-csv"></i> Export CSV
                    </button>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <label for="facultySelector" style="margin: 0; font-weight: 600;">Select Faculty to Print:</label>
                        <select id="facultySelector" class="form-control" style="width: 250px; flex: 0;">
                            <option value="">-- Select Faculty --</option>
                            <?php 
                                $faculty_list = [];
                                foreach ($schedules as $schedule) {
                                    $faculty_key = $schedule['faculty_code'] ?? 'Unknown';
                                    if (!in_array($faculty_key, $faculty_list)) {
                                        $faculty_list[] = $faculty_key;
                                    }
                                }
                                foreach ($faculty_list as $fac_code) {
                                    echo '<option value="' . htmlspecialchars($fac_code) . '">' . htmlspecialchars($fac_code) . '</option>';
                                }
                            ?>
                        </select>
                        <button id="printScheduleBtn" onclick="printSchedule()" class="btn btn-primary">
                            <i class="fas fa-print"></i> Print
                        </button>
                    </div>
                </div>
            </div>
            
            <?php if ($total_schedules > 0): ?>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>Time Slot</th>
                                <th>Room</th>
                                <th>Subject</th>
                                <th>Section</th>
                                <th>Faculty</th>
                                <th>Semester</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($schedules as $row): ?>
                            <tr>
                                <td>
                                    <span class="badge badge-primary"><?= htmlspecialchars($row['day_of_week'] ?? '') ?></span>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($row['official_time'] ?? '') ?></strong><br>
                                    <small><?= date('h:i A', strtotime($row['start_time'])) ?> - <?= date('h:i A', strtotime($row['end_time'])) ?></small>
                                </td>
                                <td><span class="badge badge-warning"><?= htmlspecialchars($row['room'] ?? '') ?></span></td>
                                <td><strong><?= htmlspecialchars($row['subject_code'] ?? '') ?></strong></td>
                                <td>
                                    <?= htmlspecialchars($row['section_code'] ?? '') ?><br>
                                    <small><?= htmlspecialchars($row['grade_level'] ?? '') ?> - <?= htmlspecialchars($row['program'] ?? '') ?></small>
                                </td>
                                <td>
                                    <?= htmlspecialchars($row['last_name'] ?? '') ?>, <?= htmlspecialchars($row['first_name'] ?? '') ?><br>
                                    <small><?= htmlspecialchars($row['faculty_code'] ?? '') ?></small>
                                </td>
                                <td>
                                    <?= htmlspecialchars($row['semester'] ?? '') ?><br>
                                    <small><?= htmlspecialchars($row['school_year'] ?? '') ?></small>
                                </td>
                                <td>
                                    <button class="btn btn-primary btn-sm" onclick='editSchedule(<?= json_encode($row) ?>)'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" 
                                            onclick="deleteSchedule(<?= $row['id'] ?>)"
                                            title="Delete Schedule">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 40px; color: #666;">
                    <i class="fas fa-calendar-times fa-3x" style="margin-bottom: 20px; color: #ddd;"></i>
                    <h3>No schedules found</h3>
                    <p>Try different filters or add new schedules</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Add Schedule Tab -->
        <div id="add-tab" class="tab-content <?= isset($_GET['action']) && $_GET['action'] == 'add' ? 'active' : '' ?>">
            <h2><i class="fas fa-plus-circle"></i> Add New Schedule</h2>
            
            <form method="POST" class="form-grid" onsubmit="return validateForm()">
                <div class="form-card">
                    <h3>Basic Information</h3>
                    <div class="form-group">
                        <label for="room">Room</label>
                        <input type="text" id="room" name="room" required placeholder="e.g., G15, A10" maxlength="20">
                    </div>
                    
                    <div class="form-group">
                        <label for="official_time">Official Time Slot</label>
                        <select id="official_time" name="official_time" required>
                            <option value="">Select Time Slot</option>
                            <option value="7:30-10:00">7:30-10:00 AM</option>
                            <option value="10:00-12:30">10:00-12:30 PM</option>
                            <option value="12:30-3:00">12:30-3:00 PM</option>
                            <option value="3:00-5:30">3:00-5:30 PM</option>
                            <option value="5:30-8:00">5:30-8:00 PM</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="start_time">Start Time</label>
                        <input type="time" id="start_time" name="start_time" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="end_time">End Time</label>
                        <input type="time" id="end_time" name="end_time" required>
                    </div>
                </div>

                <div class="form-card">
                    <h3>Course Information</h3>
                    <div class="form-group">
                        <label for="day_of_week">Day of Week</label>
                        <select id="day_of_week" name="day_of_week" required>
                            <option value="">Select Day</option>
                            <option value="Monday">Monday</option>
                            <option value="Tuesday">Tuesday</option>
                            <option value="Wednesday">Wednesday</option>
                            <option value="Thursday">Thursday</option>
                            <option value="Friday">Friday</option>
                            <option value="Saturday">Saturday</option>
                            <option value="Sunday">Sunday</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject_code">Subject Code</label>
                        <input type="text" id="subject_code" name="subject_code" required placeholder="e.g., OJT, PRE4, TPC5" maxlength="20">
                    </div>
                    
                    <div class="form-group">
                        <label for="grade_section_id">Section</label>
                        <select id="grade_section_id" name="grade_section_id" required>
                            <option value="">Select Section</option>
                            <?php foreach ($all_sections as $sec): ?>
                                <option value="<?= $sec['id'] ?>">
                                    <?= htmlspecialchars($sec['section_code']) ?> (<?= htmlspecialchars($sec['grade_level']) ?> - <?= htmlspecialchars($sec['program']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-card">
                    <h3>Faculty & Semester</h3>
                    <div class="form-group">
                        <label for="faculty_id">Assigned Faculty</label>
                        <select id="faculty_id" name="faculty_id" required>
                            <option value="">Select Faculty</option>
                            <?php foreach ($all_faculty as $fac): ?>
                                <option value="<?= $fac['id'] ?>">
                                    <?= htmlspecialchars($fac['last_name']) ?>, <?= htmlspecialchars($fac['first_name']) ?> (<?= htmlspecialchars($fac['faculty_code']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="semester">Semester</label>
                        <select id="semester" name="semester" required>
                            <option value="1st Sem">1st Semester</option>
                            <option value="2nd Sem" selected>2nd Semester</option>
                            <option value="Summer">Summer</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="school_year">School Year</label>
                        <input type="text" id="school_year" name="school_year" 
                               value="<?= htmlspecialchars($school_year) ?>" placeholder="YYYY-YYYY" required 
                               pattern="\d{4}-\d{4}" title="Please enter in format YYYY-YYYY">
                    </div>
                </div>

                <div style="grid-column: 1 / -1; text-align: center;">
                    <input type="hidden" name="semester_filter" value="<?= htmlspecialchars($semester) ?>">
                    <input type="hidden" name="school_year_filter" value="<?= htmlspecialchars($school_year) ?>">
                    <button type="submit" name="add_schedule" value="1" class="btn btn-success">
                        <i class="fas fa-save"></i> Save Schedule
                    </button>
                    <button type="reset" class="btn btn-warning">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                </div>
            </form>
        </div>

        <!-- Import CSV Tab -->
        <div id="import-tab" class="tab-content <?= isset($_GET['action']) && $_GET['action'] == 'import' ? 'active' : '' ?>">
            <h2><i class="fas fa-file-import"></i> Import Schedules from CSV</h2>
            
            <div class="import-box">
                <h3><i class="fas fa-cloud-upload-alt"></i> Upload CSV File</h3>
                <p>Upload a CSV file containing schedule data</p>
                
                <form method="POST" enctype="multipart/form-data" style="margin: 20px 0;">
                    <div style="margin-bottom: 15px;">
                        <input type="file" name="csv_file" accept=".csv" required>
                    </div>
                    <button type="submit" name="import_schedule" value="1" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Import CSV
                    </button>
                </form>
                
                <div class="csv-template">
                    <h4>CSV Format (First row should be headers):</h4>
                    <p>room,official_time,start_time,end_time,day_of_week,subject_code,section_code,faculty_code,semester,school_year</p>
                    <h4>Example:</h4>
                    <p>G15,12:30-3:00,12:30:00,15:00:00,Monday,TPC5,BSCRIM 1201,DJAURIGUE,2nd Sem,2025-2026</p>
                    <p>A10,7:30-10:00,07:30:00,10:00:00,Tuesday,OJT,BSCRIM 3201,MCPANGILINAN,2nd Sem,2025-2026</p>
                </div>
                
                <div style="margin-top: 20px; color: #666;">
                    <p><i class="fas fa-info-circle"></i> Notes:</p>
                    <ul style="text-align: left; max-width: 600px; margin: 10px auto;">
                        <li>section_code must exist in the sections table</li>
                        <li>faculty_code must exist in the faculty table</li>
                        <li>Time format must be HH:MM:SS (24-hour format)</li>
                        <li>Days must be spelled correctly (Monday, Tuesday, etc.)</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div style="margin-bottom: 10px;">
                <a href="index.php" class="btn btn-primary">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="attendance_form.php" class="btn btn-success">
                    <i class="fas fa-clipboard-check"></i> Mark Attendance
                </a>
                <a href="view_enrollees.php" class="btn btn-primary">
                    <i class="fas fa-users"></i> View Enrollees
                </a>
            </div>
            <p>Schedule Management System &copy; <?= date('Y') ?> | Office of the Safety and Security</p>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-edit"></i> Edit Schedule</h2>
                <button class="close-modal" onclick="closeEditModal()">&times;</button>
            </div>
            <form method="POST" id="editForm" class="form-grid" onsubmit="return validateEditForm()">
                <input type="hidden" name="schedule_id" id="edit_schedule_id">
                <input type="hidden" name="semester" id="edit_semester_hidden">
                <input type="hidden" name="school_year" id="edit_school_year_hidden">
                
                <div class="form-card">
                    <h3>Basic Information</h3>
                    <div class="form-group">
                        <label>Room</label>
                        <input type="text" name="room" id="edit_room" required maxlength="20">
                    </div>
                    
                    <div class="form-group">
                        <label>Official Time Slot</label>
                        <select name="official_time" id="edit_official_time" required>
                            <option value="7:30-10:00">7:30-10:00 AM</option>
                            <option value="10:00-12:30">10:00-12:30 PM</option>
                            <option value="12:30-3:00">12:30-3:00 PM</option>
                            <option value="3:00-5:30">3:00-5:30 PM</option>
                            <option value="5:30-8:00">5:30-8:00 PM</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Start Time</label>
                        <input type="time" name="start_time" id="edit_start_time" required>
                    </div>
                    
                    <div class="form-group">
                        <label>End Time</label>
                        <input type="time" name="end_time" id="edit_end_time" required>
                    </div>
                </div>

                <div class="form-card">
                    <h3>Course Information</h3>
                    <div class="form-group">
                        <label>Day of Week</label>
                        <select name="day_of_week" id="edit_day_of_week" required>
                            <option value="Monday">Monday</option>
                            <option value="Tuesday">Tuesday</option>
                            <option value="Wednesday">Wednesday</option>
                            <option value="Thursday">Thursday</option>
                            <option value="Friday">Friday</option>
                            <option value="Saturday">Saturday</option>
                            <option value="Sunday">Sunday</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Subject Code</label>
                        <input type="text" name="subject_code" id="edit_subject_code" required maxlength="20">
                    </div>
                    
                    <div class="form-group">
                        <label>Section</label>
                        <select name="grade_section_id" id="edit_grade_section_id" required>
                            <?php foreach ($all_sections as $sec): ?>
                                <option value="<?= $sec['id'] ?>">
                                    <?= htmlspecialchars($sec['section_code']) ?> (<?= htmlspecialchars($sec['grade_level']) ?> - <?= htmlspecialchars($sec['program']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-card">
                    <h3>Faculty & Semester</h3>
                    <div class="form-group">
                        <label>Assigned Faculty</label>
                        <select name="faculty_id" id="edit_faculty_id" required>
                            <?php foreach ($all_faculty as $fac): ?>
                                <option value="<?= $fac['id'] ?>">
                                    <?= htmlspecialchars($fac['last_name']) ?>, <?= htmlspecialchars($fac['first_name']) ?> (<?= htmlspecialchars($fac['faculty_code']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Semester</label>
                        <select name="semester_display" id="edit_semester" required onchange="document.getElementById('edit_semester_hidden').value = this.value">
                            <option value="1st Sem">1st Semester</option>
                            <option value="2nd Sem">2nd Semester</option>
                            <option value="Summer">Summer</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>School Year</label>
                        <input type="text" name="school_year_display" id="edit_school_year" required 
                               pattern="\d{4}-\d{4}" title="Please enter in format YYYY-YYYY"
                               onchange="document.getElementById('edit_school_year_hidden').value = this.value">
                    </div>
                </div>

                <div style="grid-column: 1 / -1; text-align: center; margin-top: 20px;">
                    <button type="submit" name="update_schedule" value="1" class="btn btn-success">
                        <i class="fas fa-save"></i> Update Schedule
                    </button>
                    <button type="button" class="btn btn-warning" onclick="closeEditModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Tab functionality
        function showTab(tabName) {
            // Hide all tab content
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show selected tab content
            document.getElementById(tabName + '-tab').classList.add('active');
            
            // Add active class to clicked tab
            event.target.classList.add('active');
            
            // Update URL parameter without reload
            const url = new URL(window.location);
            url.searchParams.set('action', tabName);
            window.history.pushState({}, '', url);
        }

        // Set active tab based on URL
        const urlParams = new URLSearchParams(window.location.search);
        const actionParam = urlParams.get('action');
        if (actionParam && ['view', 'add', 'import'].includes(actionParam)) {
            // Don't reload, just update active states
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            document.getElementById(actionParam + '-tab').classList.add('active');
            document.querySelectorAll('.tab').forEach((tab, index) => {
                if ((actionParam === 'view' && index === 0) ||
                    (actionParam === 'add' && index === 1) ||
                    (actionParam === 'import' && index === 2)) {
                    tab.classList.add('active');
                }
            });
        }

        // Edit schedule modal
        function editSchedule(schedule) {
            // Fill form with schedule data
            document.getElementById('edit_schedule_id').value = schedule.id;
            document.getElementById('edit_room').value = schedule.room;
            document.getElementById('edit_official_time').value = schedule.official_time;
            
            // Convert time from HH:MM:SS to HH:MM for time input
            if (schedule.start_time) {
                document.getElementById('edit_start_time').value = schedule.start_time.substring(0, 5);
            }
            if (schedule.end_time) {
                document.getElementById('edit_end_time').value = schedule.end_time.substring(0, 5);
            }
            
            document.getElementById('edit_day_of_week').value = schedule.day_of_week;
            document.getElementById('edit_subject_code').value = schedule.subject_code;
            document.getElementById('edit_grade_section_id').value = schedule.grade_section_id;
            document.getElementById('edit_faculty_id').value = schedule.faculty_id;
            
            // Set semester and school year in both display and hidden fields
            document.getElementById('edit_semester').value = schedule.semester;
            document.getElementById('edit_semester_hidden').value = schedule.semester;
            document.getElementById('edit_school_year').value = schedule.school_year;
            document.getElementById('edit_school_year_hidden').value = schedule.school_year;
            
            // Show modal
            document.getElementById('editModal').style.display = 'flex';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target === modal) {
                closeEditModal();
            }
        }

        // Form validation
        function validateForm() {
            const startTime = document.getElementById('start_time').value;
            const endTime = document.getElementById('end_time').value;
            const dayOfWeek = document.getElementById('day_of_week').value;
            const schoolYear = document.getElementById('school_year').value;
            
            // Validate school year format
            const schoolYearPattern = /^\d{4}-\d{4}$/;
            if (!schoolYearPattern.test(schoolYear)) {
                alert('Please enter school year in format YYYY-YYYY (e.g., 2025-2026)');
                return false;
            }
            
            // Validate time order
            if (startTime >= endTime) {
                alert('End time must be after start time');
                return false;
            }
            
            return true;
        }

        function validateEditForm() {
            const startTime = document.getElementById('edit_start_time').value;
            const endTime = document.getElementById('edit_end_time').value;
            const schoolYear = document.getElementById('edit_school_year').value;
            
            // Validate school year format
            const schoolYearPattern = /^\d{4}-\d{4}$/;
            if (!schoolYearPattern.test(schoolYear)) {
                alert('Please enter school year in format YYYY-YYYY (e.g., 2025-2026)');
                return false;
            }
            
            // Validate time order
            if (startTime >= endTime) {
                alert('End time must be after start time');
                return false;
            }
            
            return true;
        }

        // Set default time in add form
        document.addEventListener('DOMContentLoaded', function() {
            const now = new Date();
            const currentTime = now.toTimeString().slice(0,5);
            
            // Set default times if fields are empty
            if (!document.getElementById('start_time').value) {
                document.getElementById('start_time').value = currentTime;
            }
            if (!document.getElementById('end_time').value) {
                const endTime = new Date(now.getTime() + 2.5 * 60 * 60 * 1000).toTimeString().slice(0,5);
                document.getElementById('end_time').value = endTime;
            }
            
            // Set default time slot based on current time
            if (!document.getElementById('official_time').value) {
                const hour = now.getHours();
                let timeSlot = '';
                if (hour >= 7 && hour < 10) timeSlot = '7:30-10:00';
                else if (hour >= 10 && hour < 12) timeSlot = '10:00-12:30';
                else if (hour >= 12 && hour < 15) timeSlot = '12:30-3:00';
                else if (hour >= 15 && hour < 18) timeSlot = '3:00-5:30';
                else timeSlot = '5:30-8:00';
                
                document.getElementById('official_time').value = timeSlot;
            }
            
            // Set current day
            if (!document.getElementById('day_of_week').value) {
                const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                document.getElementById('day_of_week').value = days[now.getDay()];
            }
        });

        // Export to CSV
        function exportToCSV() {
            const rows = document.querySelectorAll('.data-table tbody tr');
            if (rows.length === 0) {
                alert('No data to export');
                return;
            }
            
            let csv = 'Room,Time Slot,Start Time,End Time,Day,Subject,Section,Faculty,Semester,School Year\n';
            
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length >= 7) {
                    const room = cells[2].querySelector('.badge')?.textContent || '';
                    const timeSlot = cells[1].querySelector('strong')?.textContent || '';
                    const times = cells[1].querySelector('small')?.textContent.split(' - ') || [];
                    const startTime = times[0]?.trim() || '';
                    const endTime = times[1]?.trim() || '';
                    const day = cells[0].querySelector('.badge')?.textContent || '';
                    const subject = cells[3]?.textContent?.trim() || '';
                    const section = cells[4]?.textContent?.split('\n')[0]?.trim() || '';
                    const faculty = cells[5]?.textContent?.split('\n')[0]?.trim() || '';
                    const semester = cells[6]?.textContent?.split('\n')[0]?.trim() || '';
                    const schoolYear = cells[6]?.querySelector('small')?.textContent || '';
                    
                    csv += `"${room}","${timeSlot}","${startTime}","${endTime}","${day}","${subject}","${section}","${faculty}","${semester}","${schoolYear}"\n`;
                }
            });
            
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `schedules_${new Date().toISOString().slice(0,10)}.csv`;
            a.click();
            window.URL.revokeObjectURL(url);
        }

        // Print schedule
        function printSchedule() {
            // Get selected faculty
            const selectedFaculty = document.getElementById('facultySelector').value;
            
            if (!selectedFaculty) {
                alert('Please select a faculty to print');
                return;
            }

            // Get all rows from table
            const rows = document.querySelectorAll('.table-container tbody tr');
            
            if (rows.length === 0) {
                alert('No schedules to print');
                return;
            }

            // Filter schedules for selected faculty only
            const schedules = [];
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length > 0) {
                    // Faculty code is in the 6th cell (index 5)
                    const facultyInfo = cells[5].textContent.trim();
                    const facultyCode = facultyInfo.split('\n')[1]?.trim() || '';
                    
                    // Only add if matches selected faculty
                    if (facultyCode === selectedFaculty) {
                        schedules.push({
                            day: cells[0].textContent.trim(),
                            time: cells[1].textContent.trim(),
                            room: cells[2].textContent.trim(),
                            subject_code: cells[3].textContent.trim(),
                            section: cells[4].textContent.trim(),
                            faculty_full: cells[5].textContent.trim(),
                            faculty_code: facultyCode
                        });
                    }
                }
            });

            if (schedules.length === 0) {
                alert('No schedules found for selected faculty');
                return;
            }

            // Get filter values
            const semester = document.querySelector('select[name="semester"]')?.value || '<?= htmlspecialchars($semester) ?>';
            const school_year = document.querySelector('select[name="school_year"]')?.value || '<?= htmlspecialchars($school_year) ?>';
            
            // Extract faculty name from full info
            let faculty_name = '';
            let faculty_code = selectedFaculty;
            if (schedules.length > 0) {
                const facultyText = schedules[0].faculty_full;
                const lines = facultyText.split('\n');
                faculty_name = lines[0]?.trim() || '';
            }

            // Build table rows HTML
            let tableRows = '';
            schedules.forEach(sch => {
                tableRows += '<tr>' +
                    '<td style="font-weight: bold;">' + sch.day + '</td>' +
                    '<td>' + sch.time + '</td>' +
                    '<td style="font-weight: bold;">' + sch.subject_code + '</td>' +
                    '<td>' + sch.section + '</td>' +
                    '<td style="font-weight: bold; text-align: center;">' + sch.room + '</td>' +
                    '</tr>';
            });

            // Create print window
            const printWindow = window.open('', '', 'width=1200,height=800');
            const htmlContent = `
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <title>Faculty Class Schedule - ${faculty_name || 'Schedule'}</title>
                    <style>
                        * {
                            margin: 0;
                            padding: 0;
                            box-sizing: border-box;
                        }

                        body {
                            font-family: 'Calibri', 'Arial', sans-serif;
                            padding: 30px;
                            background: white;
                            color: #333;
                            line-height: 1.4;
                        }

                        .print-container {
                            max-width: 900px;
                            margin: 0 auto;
                            background: white;
                            padding: 40px;
                            border-radius: 4px;
                        }

                        /* HEADER SECTION */
                        .header {
                            text-align: center;
                            border-bottom: 3px solid #333;
                            padding-bottom: 15px;
                            margin-bottom: 20px;
                        }

                        .college-name {
                            font-size: 18px;
                            font-weight: bold;
                            color: #000;
                            margin-bottom: 5px;
                            letter-spacing: 0.5px;
                        }

                        .college-address {
                            font-size: 11px;
                            color: #333;
                            line-height: 1.3;
                            margin-bottom: 3px;
                        }

                        .college-office {
                            font-size: 12px;
                            font-weight: bold;
                            color: #000;
                            margin-top: 8px;
                        }

                        .document-title {
                            font-size: 16px;
                            font-weight: bold;
                            color: #000;
                            margin-top: 12px;
                            text-transform: uppercase;
                            letter-spacing: 0.5px;
                        }

                        /* FACULTY INFO SECTION */
                        .faculty-info {
                            background: #f5f5f5;
                            padding: 15px;
                            margin-bottom: 20px;
                            border: 1px solid #ccc;
                            border-radius: 3px;
                        }

                        .info-row {
                            display: flex;
                            margin-bottom: 8px;
                            font-size: 12px;
                        }

                        .info-label {
                            font-weight: bold;
                            width: 120px;
                            color: #333;
                        }

                        .info-value {
                            flex: 1;
                            color: #555;
                        }

                        /* TABLE SECTION */
                        .schedule-table {
                            width: 100%;
                            border-collapse: collapse;
                            margin: 20px 0;
                            font-size: 11px;
                        }

                        .schedule-table thead {
                            background: #333;
                            color: white;
                        }

                        .schedule-table th {
                            padding: 10px 8px;
                            text-align: left;
                            font-weight: bold;
                            border: 1px solid #333;
                            font-size: 11px;
                        }

                        .schedule-table td {
                            padding: 8px;
                            border: 1px solid #ccc;
                        }

                        .schedule-table tbody tr {
                            background: white;
                        }

                        .schedule-table tbody tr:nth-child(even) {
                            background: #f9f9f9;
                        }

                        /* FOOTER SECTION */
                        .footer {
                            margin-top: 40px;
                            padding-top: 20px;
                            border-top: 1px solid #ddd;
                        }

                        .footer-row {
                            display: flex;
                            justify-content: space-between;
                            margin-top: 40px;
                        }

                        .signature-block {
                            width: 30%;
                            text-align: center;
                            font-size: 11px;
                        }

                        .signature-line {
                            border-top: 1px solid #000;
                            padding-top: 5px;
                            margin-top: 35px;
                            font-weight: bold;
                        }

                        .signature-title {
                            font-size: 10px;
                            color: #666;
                            margin-top: 3px;
                        }

                        .date-prepared {
                            font-size: 10px;
                            color: #666;
                            margin-top: 15px;
                        }

                        @media print {
                            body {
                                margin: 0;
                                padding: 0;
                                background: white;
                            }
                            .print-container {
                                padding: 20px;
                                border-radius: 0;
                                box-shadow: none;
                            }
                            @page {
                                size: A4 landscape;
                                margin: 10mm;
                            }
                        }
                    </style>
                </head>
                <body>
                    <div class="print-container">
                        <!-- HEADER -->
                        <div class="header">
                            <div class="college-name">BESTLINK COLLEGE OF THE PHILIPPINES</div>
                            <div class="college-address">
                                1071 Brgy. Kaligayahan, Quirino Highway, Novaliches<br>
                                Quezon City, Philippines 1116
                            </div>
                            <div class="college-office">College Coordinator Office</div>
                            <div class="document-title">Faculty Class Schedule</div>
                        </div>

                        <!-- FACULTY INFORMATION -->
                        <div class="faculty-info">
                            <div class="info-row">
                                <div class="info-label">Faculty Name:</div>
                                <div class="info-value">${faculty_name || 'N/A'}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Faculty Code:</div>
                                <div class="info-value">${faculty_code || 'N/A'}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Semester:</div>
                                <div class="info-value">${semester} ${school_year}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Academic Year:</div>
                                <div class="info-value">${school_year}</div>
                            </div>
                        </div>

                        <!-- SCHEDULE TABLE -->
                        <table class="schedule-table">
                            <thead>
                                <tr>
                                    <th>Day</th>
                                    <th>Time</th>
                                    <th>Subject Code</th>
                                    <th>Section</th>
                                    <th>Room</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${tableRows}
                            </tbody>
                        </table>

                        <!-- FOOTER -->
                        <div class="footer">
                            <div class="footer-row">
                                <div class="signature-block">
                                    <div class="date-prepared">Prepared by:</div>
                                    <div class="signature-line"></div>
                                    <div class="signature-title">College Coordinator</div>
                                </div>

                                <div class="signature-block">
                                    <div class="date-prepared">Noted by:</div>
                                    <div class="signature-line"></div>
                                    <div class="signature-title">Dean / Director</div>
                                </div>

                                <div class="signature-block">
                                    <div class="date-prepared">Date:</div>
                                    <div class="signature-line">${new Date().toLocaleDateString()}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </body>
                </html>
            `;
            
            printWindow.document.write(htmlContent);
            printWindow.document.close();
            printWindow.focus();
            
            // Trigger print after content is loaded
            setTimeout(() => {
                printWindow.print();
            }, 250);
        }

        // Delete schedule via AJAX
        function deleteSchedule(scheduleId) {
            if (!confirm('Delete this schedule? This will also delete attendance records.')) {
                return;
            }

            const formData = new FormData();
            formData.append('schedule_id', scheduleId);

            fetch('/sms/modules/college-coor/api/delete_schedule.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting schedule: ' + error);
            });
        }
    </script>
</body>
</html>
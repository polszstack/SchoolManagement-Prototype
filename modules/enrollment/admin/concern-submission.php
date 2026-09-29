<?php
include_once __DIR__ . '/../../../auth/session.php';
include_once __DIR__ . '/../classes/Issues.php';
$admin_name = $admin_name ?? 'Admin';
$user_role = $user_role ?? 'Administrator';
$issues      = new Issues();
$departments = $issues->getDepartments();
include '../includes/header.php';
include '../includes/sidebar_admin.php';
?>

<!-- Add wrapper div with margin for sidebar -->
 <link rel="stylesheet" href="../css/pages/concerns-issue-tracking.css">
 <div class="module-header" style="padding-left: 0;">
        <h1>Concern Submission</h1>
    </div>

    <div class="module-content" style="padding: 0;">
        <div class="concerns-controls">
            <div class="concern-form">
                <h3>Log Concern</h3>
                <form id="concern-log-form" data-skip>
                    <div class="form-group">
                        <label for="concern-title">Title</label>
                        <input id="concern-title" name="title" type="text" placeholder="Short summary of the concern">
                    </div>

                    <div class="form-group">
                        <label for="concern-file">Attachment <span style="font-weight:400; color:var(--muted);">(optional)</span></label>
                        <input class="file-btn" id="concern-file" name="file" type="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg">
                        <span id="file-error" style="display:none; color:red; font-size:0.85rem;"></span>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-log">Log Concern</button>
                        <button type="reset" class="btn-cancel">Clear</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="concerns-list table-responsive">
            <div class="concerns-list-head">
                <h3>Issue List</h3>
                <div class="concerns-filters">
                    <select id="concern-filter">
                        <option value="all">All</option>
                        <option value="open">Open</option>
                        <option value="resolved">Resolved</option>
                    </select>
                    <input class="concern-search" id="concern-search" type="search" placeholder="Search by title or submitter">
                </div>
            </div>

            <table class="concern-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Department</th>
                        <th>Submitted By</th>
                        <th>Submitted On</th>
                        <th>Status</th>
                        <th>Attachment</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="7" class="muted">Loading…</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
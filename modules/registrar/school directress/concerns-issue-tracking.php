<?php
include_once __DIR__ . '/../../../auth/session.php';
include_once __DIR__ . '/../classes/Issues.php';

$issues      = new Issues();
$departments = $issues->getDepartments();
?>

<div class="module-header">
    <h1>Concerns & Issue Tracking</h1>
    <p>Log, assign, and track concerns. Simple status updates only (Open → Resolved).</p>
</div>

<div class="module-content">
    <div class="concerns-controls">
        <div class="concern-form">
            <h3>Log Concern</h3>
            <form id="concern-log-form" data-skip>
                <div class="form-group">
                    <label for="concern-title">Title</label>
                    <input id="concern-title" name="title" type="text" placeholder="Short summary of the concern">
                </div>

                <div class="form-group">
                    <label for="concern-desc">Description</label>
                    <textarea id="concern-desc" name="description" rows="4" placeholder="Detailed description"></textarea>
                </div>

                <div class="form-group">
                    <label for="concern-dept">Assign Department</label>
                    <select id="concern-dept" name="department">
                        <option value="">Select Department</option>
                        <?php foreach ($departments as $dept) : ?>
                            <option value="<?= $dept['department_id'] ?>">
                                <?= htmlspecialchars($dept['department_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
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
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr><td colspan="8" class="muted">Loading…</td></tr>
            </tbody>
        </table>
    </div>
</div>
<?php
// Concerns & Issue Tracking Dashboard
// College Coordinator

// Include necessary classes and DB connection
require_once(__DIR__ . '/../classes/IssueManager.php');

require_once(__DIR__ . '/../../../database/db.php');

$database = new Database();
$conn = $database->getConnection();

$issueManager = new IssueManager($conn);
$issues = $issueManager->getAllIssues();
$statusCounts = $issueManager->getStatusCounts();
$priorityList = $issueManager->getPriorityList();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concerns & Issue Tracking</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/concerns-issues.css">
</head>
<body>
<div class="container-fluid mt-4">
    <h2 class="mb-4">Concerns & Issue Tracking</h2>
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h6>Open Issues</h6>
                    <span class="badge bg-warning text-dark fs-5"><?= $statusCounts['open'] ?? 0 ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h6>In Progress</h6>
                    <span class="badge bg-primary fs-5"><?= $statusCounts['in_progress'] ?? 0 ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h6>Resolved</h6>
                    <span class="badge bg-success fs-5"><?= $statusCounts['resolved'] ?? 0 ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h6>Closed</h6>
                    <span class="badge bg-secondary fs-5"><?= $statusCounts['closed'] ?? 0 ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h6>Total Issues</h6>
                    <span class="badge bg-dark fs-5"><?= $statusCounts['total'] ?? 0 ?></span>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-3">
            <select id="filterStatus" class="form-select">
                <option value="">All Status</option>
                <option value="open">Open</option>
                <option value="in_progress">In Progress</option>
                <option value="resolved">Resolved</option>
                <option value="closed">Closed</option>
            </select>
        </div>
        <div class="col-md-3">
            <select id="filterPriority" class="form-select">
                <option value="">All Priority</option>
                <?php foreach ($priorityList as $priority): ?>
                    <option value="<?= $priority ?>"><?= ucfirst($priority) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <input type="text" id="searchBar" class="form-control" placeholder="Search issues...">
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-hover" id="issuesTable">
            <thead class="table-light">
                <tr>
                    <th style="width: 80px;">Issue ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Department</th>
                    <th>Submitted by</th>
                    <th>Submitted on</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($issues)): ?>
                <?php foreach ($issues as $issue): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($issue['issue_id'] ?? $issue['id'] ?? '') ?></strong></td>
                        <td><?= htmlspecialchars($issue['title'] ?? '') ?></td>
                        <td><?= htmlspecialchars($issue['description'] ?? '') ?></td>
                        <td><?= htmlspecialchars($issue['department'] ?? '') ?></td>
                        <td><?= htmlspecialchars($issue['submitted_by'] ?? '') ?></td>
                        <td><?= isset($issue['submitted_on']) ? date('Y-m-d', strtotime($issue['submitted_on'])) : '' ?></td>
                        <td><span class="badge status-badge status-<?= $issue['status'] ?? '' ?>"><?= $issue['status'] ?? '' ?></span></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary view-issue-btn" data-id="<?= $issue['id'] ?? '' ?>">View</button>
                            <button class="btn btn-sm btn-outline-warning edit-issue-btn" data-id="<?= $issue['id'] ?? '' ?>">Edit</button>
                            <button class="btn btn-sm btn-outline-danger delete-issue-btn" data-id="<?= $issue['id'] ?? '' ?>">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center text-muted">No issues found</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Issue Details Modal -->
<div class="modal fade" id="issueModal" tabindex="-1" aria-labelledby="issueModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="issueModalLabel">Issue Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="issueModalBody">
        <!-- Issue details will be loaded here -->
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/concerns-issues.js"></script>
</body>
</html>

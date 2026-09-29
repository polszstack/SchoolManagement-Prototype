<?php
// Approval & Decision Support UI
require_once(__DIR__ . '/../classes/ApprovalDecisionSupport.php');
require_once(__DIR__ . '/../../../database/db.php');

$database = new Database();
$conn = $database->getConnection();

$approvalSupport = new ApprovalDecisionSupport($conn);
$allApprovals = $approvalSupport->getRequests('all');
$pendingCount = count(array_filter($allApprovals, fn($r) => isset($r['decision']) && $r['decision'] === 'Pending'));
$reviewCount = count(array_filter($allApprovals, fn($r) => isset($r['decision']) && $r['decision'] === 'Under Review'));
$approvedCount = count(array_filter($allApprovals, fn($r) => isset($r['decision']) && $r['decision'] === 'Approved'));
$rejectedCount = count(array_filter($allApprovals, fn($r) => isset($r['decision']) && $r['decision'] === 'Rejected'));
$totalCount = count($allApprovals);
?>
<div class="container mt-4">
  <h2 class="mb-4">Approval & Decision Support</h2>
  <div class="row mb-4">
    <div class="col-md-2">
      <div class="card text-center shadow-sm">
        <div class="card-body">
          <h6 class="card-title">Pending Requests</h6>
          <span class="display-6 text-warning" id="pending-count"><?= $pendingCount ?></span>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card text-center shadow-sm">
        <div class="card-body">
          <h6 class="card-title">Under Review</h6>
          <span class="display-6 text-info" id="review-count"><?= $reviewCount ?></span>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card text-center shadow-sm">
        <div class="card-body">
          <h6 class="card-title">Approved</h6>
          <span class="display-6 text-success" id="approved-count"><?= $approvedCount ?></span>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card text-center shadow-sm">
        <div class="card-body">
          <h6 class="card-title">Rejected</h6>
          <span class="display-6 text-danger" id="rejected-count"><?= $rejectedCount ?></span>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card text-center shadow-sm">
        <div class="card-body">
          <h6 class="card-title">Total Requests</h6>
          <span class="display-6 text-primary" id="total-count"><?= $totalCount ?></span>
        </div>
      </div>
    </div>
  </div>

  <ul class="nav nav-tabs mb-3" id="statusTabs">
    <li class="nav-item"><a class="nav-link active" data-status="all" href="#">All</a></li>
    <li class="nav-item"><a class="nav-link" data-status="pending" href="#">Pending</a></li>
    <li class="nav-item"><a class="nav-link" data-status="review" href="#">Under Review</a></li>
    <li class="nav-item"><a class="nav-link" data-status="approved" href="#">Approved</a></li>
    <li class="nav-item"><a class="nav-link" data-status="rejected" href="#">Rejected</a></li>
  </ul>

  <div class="table-responsive">
    <table class="table table-bordered table-hover" id="approvalTable">
      <thead class="table-light">
        <tr>
          <th>Approval ID</th>
          <th>Title</th>
          <th>Submitted By</th>
          <th>Department</th>
          <th>Submitted On</th>
          <th>Decision</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($allApprovals)): ?>
          <?php foreach ($allApprovals as $approval): ?>
            <tr>
              <td><?= htmlspecialchars($approval['approval_id'] ?? '') ?></td>
              <td><?= htmlspecialchars($approval['title'] ?? '') ?></td>
              <td><?= htmlspecialchars($approval['submit_by'] ?? '') ?></td>
              <td><?= htmlspecialchars($approval['department'] ?? '') ?></td>
              <td><?= isset($approval['submitted_on']) ? date('Y-m-d H:i', strtotime($approval['submitted_on'])) : '' ?></td>
              <td><span class="badge status-badge status-<?= strtolower($approval['decision'] ?? '') ?>"><?= ucfirst($approval['decision'] ?? '') ?></span></td>
              <td>
                <button class="btn btn-sm btn-outline-primary view-request-btn" data-id="<?= $approval['approval_id'] ?? '' ?>">View</button>
                <button class="btn btn-sm btn-outline-warning review-request-btn" data-id="<?= $approval['approval_id'] ?? '' ?>">Review</button>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="7" class="text-center text-muted">No approval requests found</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="modal fade" id="requestModal" tabindex="-1" aria-labelledby="requestModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="requestModalLabel">Request Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="modalBody">
        <!-- Details loaded via JS -->
      </div>
    </div>
  </div>
</div>

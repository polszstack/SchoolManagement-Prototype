<?php
// views/partials/ai_features.php
?>

<div class="tab-content" id="tab-ai">

    <div class="card ai-card" id="ai-risk-card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-brain"></i>
                Smart Late Return Detection
                <span class="badge badge-info" style="font-size:.7rem;padding:.2rem .6rem;margin-left:.5rem">AI</span>
            </h3>
            <div class="d-flex gap-1 flex-wrap">
                <button class="btn btn-sm btn-outline" onclick="AI.refreshRiskReport()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
                <button class="btn btn-sm btn-warning" onclick="AI.runReminderCheck()">
                    <i class="fas fa-bell"></i> Send Reminders Now
                </button>
            </div>
        </div>
        <p class="text-muted mb-2">
            <i class="fas fa-info-circle"></i>
            The AI scores each active borrower (0-100) based on their late return history,
            days until due, genre trends, and borrower type.
        </p>
        <div class="ai-threshold-bar mb-2">
            <div class="d-flex align-center gap-1 mb-1">
                <span class="text-muted small">Risk Threshold:</span>
                <span class="fw-600" id="aiThresholdLabel">60%</span>
                <span class="text-muted small">— borrowers above this get reminders</span>
            </div>
            <div class="progress-bar"><div class="progress-fill" id="aiThresholdFill" style="width:60%"></div></div>
        </div>
        <div class="table-responsive" id="riskTableWrap">
            <div class="spinner" id="riskSpinner"></div>
            <table id="riskTable" style="display:none">
                <thead>
                    <tr>
                        <th>Borrower</th>
                        <th>Book</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Risk Score</th>
                        <th>Level</th>
                    </tr>
                </thead>
                <tbody id="riskTableBody"></tbody>
            </table>
            <div class="empty-state" id="riskEmpty" style="display:none">
                <i class="fas fa-check-circle" style="color:var(--success)"></i>
                <p>No active transactions — nothing to monitor!</p>
            </div>
        </div>
    </div>

    <div class="card ai-card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-history"></i> Recent Reminders Sent</h3>
            <button class="btn btn-sm btn-outline" onclick="AI.loadReminderLog()">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>
        <div id="reminderLogWrap">
            <div class="spinner" id="reminderSpinner"></div>
            <div id="reminderLogList"></div>
        </div>
    </div>

    <div class="card ai-card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-magic"></i>
                AI Book Recommendations
                <span class="badge badge-success" style="font-size:.7rem;padding:.2rem .6rem;margin-left:.5rem">AI</span>
            </h3>
        </div>
        <p class="text-muted mb-2">
            <i class="fas fa-info-circle"></i>
            Select a member to see personalized book recommendations.
        </p>
        <div class="grid-2" style="margin-bottom:1.5rem">
            <div class="form-group" style="margin:0">
                <label>Select Member</label>
                <select id="aiMemberSelect" onchange="AI.loadRecommendations()">
                    <option value="">-- Choose a member --</option>
                </select>
            </div>
            <div class="form-group" style="margin:0">
                <label>Number of Recommendations</label>
                <select id="aiRecCount">
                    <option value="3">3 books</option>
                    <option value="5" selected>5 books</option>
                    <option value="8">8 books</option>
                </select>
            </div>
        </div>
        <div id="readingProfileWrap" style="display:none;margin-bottom:1.5rem">
            <p class="fw-600 mb-1"><i class="fas fa-chart-pie"></i> Reading Profile</p>
            <div id="readingProfilePills" class="d-flex gap-1 flex-wrap"></div>
        </div>
        <div id="recGrid" class="books-grid"></div>
        <div class="empty-state" id="recEmpty">
            <i class="fas fa-book-open"></i>
            <p>Select a member above to see personalized recommendations.</p>
        </div>
        <div class="spinner" id="recSpinner" style="display:none"></div>
    </div>

</div>
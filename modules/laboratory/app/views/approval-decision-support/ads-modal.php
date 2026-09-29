<div class="modal fade" id="approvalDecisionModal" tabindex="-1" aria-labelledby="approvalDecisionLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="approvalDecisionForm" method="POST" action="<?= BASE_URL ?>/approval-decision-support/update-decision">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="approvalDecisionLabel">Decision Remarks</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="approval_id" id="modalApprovalId">
                    <input type="hidden" name="action" id="modalAction">
                    <div class="mb-3">
                        <label for="modalRemarks" class="form-label">Remarks (optional)</label>
                        <textarea class="form-control" id="modalRemarks" name="remarks" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Submit Decision</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
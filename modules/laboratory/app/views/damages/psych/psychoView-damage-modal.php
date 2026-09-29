<div class="modal fade" id="psychoViewDamageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>Damage Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="text-center mb-4">
                    <img src="projector.png" class="img-fluid rounded border p-2" style="max-width:160px;">
                </div>

                <div class="row g-3">

                    <div class="col-12">
                        <small class="text-muted">Code</small>
                        <div class="fw-semibold" id="damage_code"></div>
                    </div>

                    <div class="col-12">
                        <small class="text-muted">Category</small>
                        <div class="fw-semibold" id="damage_category">Projector</div>
                    </div>

                    <div class="col-12">
                        <small class="text-muted">Item Code</small>
                        <div class="fw-semibold" id="damage_code">PRJ-00012</div>
                    </div>

                    <div class="col-12">
                        <small class="text-muted">Description</small>
                        <div class="fw-semibold" id="damage_description">
                            Lens cracked and projector not turning on.
                        </div>
                    </div>

                    <div class="col-12">
                        <small class="text-muted">Status</small><br>
                        <span class="badge bg-danger px-3 py-2" id="damage_status"></span>
                        
                    </div>

                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Close
                </button>
            </div>

        </div>
    </div>
</div>
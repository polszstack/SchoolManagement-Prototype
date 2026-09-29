<div class="modal fade" id="psychoAddDamageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus-square me-2"></i> Add New Damage
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form action="<?= BASE_URL ?>/psycho-damage/create" method="POST">

                    <div class="mb-3">
                        <label class="form-label">Item</label>
                        <select class="form-select" name="item" required>
                            <option value="">Please select item here</option>
                            <option value="Microscope">Microscope</option>
                            <option value="Beaker">Beaker</option>
                            <option value="Test Tube">Test Tube</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="4" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Code</label>
                        <input type="text" class="form-control" name="code" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="Unfixed" selected>Unfixed</option>
                            <option value="Fixed">Fixed</option>
                        </select>
                    </div>

            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>

            </form>

        </div>
    </div>
</div>
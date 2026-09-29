<div class="modal fade" id="editDamageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i> Edit Damage
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="editDamageForm">
                    <input type="hidden" name="id" id="damage_id">

                    <div class="mb-3">
                        <label class="form-label">Item</label>
                        <select class="form-select" name="edit_item" id="item" required>
                            <option value="">Please select item here</option>
                            <option value="Microscope">Microscope</option>
                            <option value="Beaker">Beaker</option>
                            <option value="Test Tube">Test Tube</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="edit_description" rows="4" id="description" required></textarea>
                    </div>

                     <div class="mb-3">
                        <label class="form-label">Description</label>
                        <input class="form-control" type="text" name="edit_code" id="code">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="edit_status" id="status">
                            <option value="Unfixed">Unfixed</option>
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
<div class="modal fade" id="psychEditModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Edit Equipment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <form id="editPsychForm">

                   <input type="hidden" name="id" id="psych_edit_id">

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Category</label>
                            <input type="text" class="form-control" name="edit_category" id="psych_edit_category">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Total</label>
                            <input type="number" class="form-control" name="edit_total" id="psych_edit_total" >
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Available</label>
                            <input type="number" class="form-control" name="edit_available" id="psych_edit_available" >
                        </div>

                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="itEditModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form id="itEditForm" enctype="multipart/form-data">

                <input type="hidden" name="id" id="edit_id">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Inventory</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <input type="text" name="category"
                                id="edit_category"
                                class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Laboratory</label>
                            <input type="text"
                                name="lab"
                                id="edit_lab"
                                class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Total</label>
                            <input type="number"
                                name="total"
                                id="edit_total"
                                class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Available</label>
                            <input type="number"
                                name="available"
                                id="edit_available"
                                class="form-control" required>
                        </div>

                        <div class="col-md-12">

                            <label class="form-label">Current Photo</label>

                            <div class="mb-2">
                                <img id="edit_preview"
                                     class="img-fluid rounded"
                                     style="max-width:150px;">
                            </div>

                            <input type="file"
                                   name="item_img"
                                   class="form-control"
                                   accept="image/*">

                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="submit" class="btn btn-success">
                        Update
                    </button>

                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Cancel
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>
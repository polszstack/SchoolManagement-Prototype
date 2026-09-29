<div class="modal fade" id="itAddModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form id="itAddForm" enctype="multipart/form-data">

                <div class="modal-header">
                    <h5 class="modal-title">Add Inventory</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <input type="text" name="category" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Laboratory</label>
                            <input type="text" name="lab" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Total</label>
                            <input type="number" name="total" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Available</label>
                            <input type="number" name="available" class="form-control" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Photo</label>
                            <input type="file" name="item_img" class="form-control" accept="image/*">
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        Save
                    </button>

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
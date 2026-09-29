<div class="modal fade" id="crimAddModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Create CRIM Equipment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <form action="<?= BASE_URL ?>/crim-inventory/create" method="POST" enctype="multipart/form-data">

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Category</label>
                            <input type="text" class="form-control" name="category">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Total</label>
                            <input type="number" class="form-control" name="total">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Available</label>
                            <input type="number" class="form-control" name="available">
                        </div>

                        <div class="col-md-6 mb-3" >
                            <label class="form-label">Photo</label>
                            <input type="file" class="form-control" name="item_img">
                        </div>

                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>
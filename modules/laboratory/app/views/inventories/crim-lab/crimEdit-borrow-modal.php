<!-- Edit Borrow Modal -->
<div class="modal fade" id="crimEditBorrowModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form action="actions/update-borrow.php" method="POST">

                <div class="modal-header bg-warning">
                    <h5 class="modal-title">
                        <i class="fas fa-edit"></i> Edit Borrow
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <input type="hidden" name="borrow_id" value="1">

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Borrower Name</label>
                            <input type="text" name="borrower_name" class="form-control" value="Juan">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Laboratory</label>
                            <select name="lab_type" class="form-control">
                                <option>Fingerprint Lab</option>
                                <option>Ballistics Lab</option>
                                <option>Drug Identification Lab</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="quantity" class="form-control" value="5">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Borrowed Date</label>
                            <input type="date" name="borrowed_date" class="form-control" value="2024-03-27">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Return Date</label>
                            <input type="date" name="return_date" class="form-control" value="2024-03-27">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control">
                                <option value="Borrowed">Borrowed</option>
                                <option value="Returned">Returned</option>
                            </select>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save"></i> Update
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
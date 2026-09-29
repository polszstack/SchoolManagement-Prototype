<div class="modal fade" id="createBorrowModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Borrow Equipment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="store.php" method="POST">

                <div class="modal-body">

                    <div class="row">

                        <!-- Borrower Type -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Borrower</label>
                            <select class="form-select" name="borrower" required>
                                <option value="">Select Borrower Type</option>
                                <option value="Student">Student</option>
                                <option value="Staff">Staff/Faculty</option>
                            </select>
                        </div>

                        <!-- Borrower Name -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Borrower Name</label>
                            <input type="text" name="borrower_name" class="form-control" required>
                        </div>

                        <!-- Items Section -->
                        <div class="mb-3">
                            <label class="form-label">Items</label>

                            <div id="itemsWrapper">

                                <div class="row g-2 mb-2 item-row">

                                    <div class="col-md-6">
                                        <input type="text" name="item[]" class="form-control" placeholder="Item Name" required>
                                    </div>

                                    <div class="col-md-3">
                                        <input type="number" name="quantity[]" class="form-control" placeholder="Quantity" required>
                                    </div>

                                    <div class="col-md-3 d-flex">

                                        <button type="button" class="btn btn-success addItem me-2">
                                            <i class="fas fa-plus"></i>
                                        </button>

                                        <button type="button" class="btn btn-danger removeItem">
                                            <i class="fas fa-minus"></i>
                                        </button>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <!-- Borrow Date -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Borrowed Date</label>
                            <input type="date" name="borrow_date" class="form-control" required>
                        </div>

                        <!-- Return Date -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Returned Date</label>
                            <input type="date" name="return_date" class="form-control">
                        </div>

                        <!-- Status -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="Borrowed">Borrowed</option>
                                <option value="Returned">Returned</option>
                            </select>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit" class="btn btn-primary">
                        Save Borrow
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>

<script>

document.addEventListener("click", function(e){

    // ADD ITEM
    if(e.target.closest(".addItem")){

        let wrapper = document.getElementById("itemsWrapper");

        let newRow = document.createElement("div");
        newRow.classList.add("row","g-2","mb-2","item-row");

        newRow.innerHTML = `
            <div class="col-md-6">
                <input type="text" name="item[]" class="form-control" placeholder="Item Name" required>
            </div>

            <div class="col-md-3">
                <input type="number" name="quantity[]" class="form-control" placeholder="Quantity" required>
            </div>

            <div class="col-md-3 d-flex">
                <button type="button" class="btn btn-success addItem me-2">
                    <i class="fas fa-plus"></i>
                </button>
                <button type="button" class="btn btn-danger removeItem">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        `;

        wrapper.appendChild(newRow);
    }


    // REMOVE ITEM
    if(e.target.closest(".removeItem")){

        let rows = document.querySelectorAll(".item-row");

        if(rows.length > 1){
            e.target.closest(".item-row").remove();
        }

    }

});

</script>
<?php include '../includes/sidebar.php'; ?>
<?php include '../includes/header.php'; ?>
<link rel="stylesheet" href="/SchoolManagementSystem/assets/css/style.css">

<main class="main-content">
    <div class="container-fluid px-4">
        <div class="container mt-4">
            <div class="card shadow-lg card shadow-sm border-0 border-top border-4 border-secondary p-3">
                <div class="card-body">

                    <h4 class="card-header-title mb-4">Add New Borrow Record</h4>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Borrower</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="borrowerType" id="faculty" checked>
                                <label class="form-check-label" for="faculty">Faculty</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="borrowerType" id="student">
                                <label class="form-check-label" for="student">Student</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="borrowerType" id="staff">
                                <label class="form-check-label" for="staff">Staff</label>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Borrower Name</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Department</label>
                            <input type="text" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3 col-md-4 ps-0">
                        <label class="form-label fw-semibold">Borrowed Date</label>
                        <input type="date" class="form-control" value="2026-02-23">
                    </div>
                    <hr>
                    <div class="row align-items-end mb-2">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Item:</label>
                            <select class="form-select">
                                <option selected>Please select category here</option>
                                <option>Projector</option>
                                <option>Laptop</option>
                                <option>Microphone</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-primary mt-3">
                                + Add to List
                            </button>
                        </div>
                    </div>

                    <h5 class="fw-bold mt-3">Items</h5>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Remarks</label>
                        <textarea class="form-control" rows="4"></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button class="btn btn-secondary">Cancel</button>
                        <button class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
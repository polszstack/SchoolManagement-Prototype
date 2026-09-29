<?php include '../includes/sidebar.php'; ?>
<?php include '../includes/header.php'; ?>
<link rel="stylesheet" href="/SchoolManagementSystem/assets/css/style.css">


<main class="main-content">
    <div class="container-fluid px-4">
        <div class="container mt-4 card shadow-sm border-0 border-top border-4 border-secondary">
            <h3 class="mb-3">Borrow Record</h3>
            <div class="card shadow-sm">

                <div class="card-body">

                    <!-- Borrower Info -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><span class="info-label">Borrower:</span> Student</p>
                            <p><span class="info-label">Borrower Name:</span> John Smith</p>
                            <p><span class="info-label">Borrowed Date:</span> 2024-03-27</p>
                            <p>
                                <span class="info-label">Status:</span>
                                <span class="badge bg-primary">Returned</span>
                            </p>
                        </div>

                        <div class="col-md-6">
                            <p><span class="info-label">Course/Year/Section:</span> BSIS 4A</p>
                            <p><span class="info-label">Returned Date:</span> 2024-03-27</p>
                        </div>
                    </div>

                    <hr>

                    <!-- Borrowed Items -->
                    <h5 class="fw-bold mb-3">Borrowed Items</h5>

                    <div class="items-box mb-3">
                        Mouse | M-1001
                    </div>

                    <p class="info-label">Remarks</p>
                    <p>test</p>

                    <div class="card-footer-custom d-flex justify-content-end gap-2">
                        <button class="btn btn-primary">Edit</button>
                        <button class="btn btn-secondary">Back to List</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
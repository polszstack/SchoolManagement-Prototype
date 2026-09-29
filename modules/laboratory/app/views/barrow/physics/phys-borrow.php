<?php include  __DIR__ .'/../../includes/sidebar.php'; ?>
<?php include  __DIR__ .'/../../includes/header.php'; ?>

<link rel="stylesheet" href="/SchoolManagementSystem/assets/css/style.css">


<main class="main-content">
    <div class="container-fluid px-4">
        <h1 class="h3 mb-2 text-gray-800">Borrows</h1>
        <p class="mb-4">Physics Laboratory</p>

        <div class="card mb-4 card shadow-sm border-0 border-top border-4 border-secondary shadow-lg p-3" id="#createBorrowModal">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-table me-1"></i>
                    Barrow Equipment
                </div>

                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createBorrowModal">
                    <i class="fas fa-plus me-1"></i> Create New
                </button>

            </div>
            <div class="card-body">
                <table id="physBorrowTable" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Borrower</th>
                            <th>Borrower Name</th>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Barrowed Date</th>
                            <th>Returned Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Student</td>
                            <td>Juan</td>
                            <td>Microscope</td>
                            <td>5</td>
                            <td>2024-03-27</td>
                            <td>2024-03-27</td>
                            <td>
                                <span class="badge bg-success">Returned</span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        Action
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item" href="view.php?id=1">
                                                <i class="fas fa-eye me-2"></i> View
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="edit.php?id=1">
                                                <i class="fas fa-edit me-2"></i> Edit
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item text-danger" href="delete.php?id=1"
                                                onclick="return confirm('Are you sure you want to delete this record?')">
                                                <i class="fas fa-trash me-2"></i> Delete
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</main>



<script>
    $(document).ready(function() {
        $('#physBorrowTable').DataTable({
            pageLength: 10,
            lengthMenu: [10, 20, 30, 40],
        });
    });
</script>

<?php require __DIR__ . '/physAdd-borrow-modal.php'; ?>

<?php include  __DIR__ .'/../../includes/footer.php'; ?>
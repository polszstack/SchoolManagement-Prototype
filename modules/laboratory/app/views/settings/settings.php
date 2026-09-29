<?php include __DIR__ . '/../includes/sidebar.php'; ?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<main class="main-content">
    <div class="container">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div id="dashboard-title">
                <h1 class="h3 fw-bold mb-0">Activity Logs</h1>
                <p class="text-muted small">Track and monitor system activities performed by users.</p>
            </div>
        </div>

        <div class="card shadow-sm">

            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="fs-6 mb-0 text-primary">Activity Logs</h3>

                <div class="d-flex gap-2">

                    <button type="button" class="btn btn-outline-danger btn-sm" id="pdf">
                        <i class="bi bi-file-earmark-pdf-fill"></i> PDF
                    </button>

                    <button type="button" class="btn btn-outline-success btn-sm" id="excel">
                        <i class="bi bi-file-earmark-excel-fill"></i> Excel
                    </button>

                    <button type="button" class="btn btn-outline-primary btn-sm" id="csv">
                        <i class="bi bi-filetype-csv"></i> CSV
                    </button>

                </div>
            </div>

            <div class="card-body">
                <div class="row g-2 mb-3">

                    <div class="col-md-2">
                        <label class="form-label small">Per Page</label>
                        <select class="form-select form-select-sm" id="limit">
                            <option value="10">10</option>
                            <option value="15">15</option>
                            <option value="20">20</option>
                            <option value="40">40</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small">Order</label>
                        <select class="form-select form-select-sm" id="range">
                            <option value="today">Today</option>
                            <option value="7days">7 Days</option>
                            <option value="this_week">This Week</option>
                            <option value="last_week">Last Week</option>
                            <option value="this_month">This Month</option>
                            <option value="last_month">Last Month</option>
                        </select>
                    </div>

                    <div class="col-md-5">
                        <label class="form-label small">Search</label>
                        <input type="text"
                            class="form-control form-control-sm"
                            placeholder="Search activity..."
                            id="search">
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-danger btn-sm d-none w-100" id="delete-btn">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </div>

                </div>

                <div class="table-responsive">

                    <table class="table table-hover table-striped align-middle">

                        <thead class="table-light">
                            <tr>
                                <th width="40">
                                    <input type="checkbox" id="select-all">
                                </th>
                                <th>User</th>
                                <th>Action</th>
                                <th>Ip Address</th>
                                <th>Date</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>

                        <tbody id="studentsTableBody">
                        </tbody>

                    </table>

                </div>



                <div class="d-flex align-items-center justify-content-between">
                    <div class="fw-bold" id="pageInfo"></div>
                    <div id="pagination" class="d-flex gap-2"></div>
                </div>


            </div>


        </div>


    </div>
    </div>
    </div>
</main>


<div class="modal fade" id="viewModal" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modalTitle"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="closeBtn"></button>
            </div>
            <div class="modal-body">

                <div class="row g-3">

                    <div class="col-md-6">
                        <label for="inputEmail4" class="form-label">User</label>
                        <input type="text" class="form-control" id="user" readonly>
                    </div>

                    <div class="col-md-6">
                        <label for="inputEmail4" class="form-label">Time</label>
                        <input type="text" class="form-control" id="time" readonly>
                    </div>

                    <div class="col-md-6">
                        <label for="inputEmail4" class="form-label">Ip Address</label>
                        <input type="text" class="form-control" id="ip_address" readonly>
                    </div>



                    <div class="col-md-12">
                        <label for="inputEmail4" class="form-label">Description</label>
                        <textarea type="text" class="form-control" id="desc" readonly></textarea>
                    </div>



                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/gsap@3.14.1/dist/gsap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
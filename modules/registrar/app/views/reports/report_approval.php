<?php include __DIR__ .'/../partials/sidebar.php'; ?>
<?php include  __DIR__ .'/../partials/header.php'; ?>


<main class="main-content">
    <div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">
    
        <div id="dashboard-title">
            <h1 class="h3 fw-bold mb-0">Approval & Decision Support</h1>
             <p class="text-muted small">Simple approval queue for submitted requests and reports — approve or reject with optional remarks.</p>
        </div>

          <button class="btn btn-primary btn-sm" id="addReportApproval">
                <i class="bi bi-plus-lg"></i> Submit a Report
            </button>

    </div>
  
    <div class="card shadow-sm">


    <div class="card-header d-flex justify-content-between align-items-center">

            <h3 class="fs-6 mb-0 text-primary">Approval Queue</h3>

    </div>

        <!-- test -->

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
                    <select class="form-select form-select-sm" id="order">
                        <option value="desc">Descending (Z-A)</option>
                        <option value="asc">Ascending (A-Z)</option>
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
                            <th>Approval ID</th>
                            <th>Title</th>
                            <th>Submitted By</th>
                            <th>Department</th>
                            <th>Decided By</th>
                            <th>Decision</th>
                            <th>Attachment</th>
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

<div class="modal fade" id="addApprovalModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content shadow-sm">

            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title fw-semibold">
                    Add Approval Report
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" id="closeBtn"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">

                <form class="row g-3" id="addReportApprovalForm" action="<?php echo BASE_URL ?>/reports-approval/store" method="POST" enctype="multipart/form-data">

                    <!-- AI Auto Correct -->
                    <div class="col-12 d-flex justify-content-between align-items-center border rounded p-2 px-3 bg-light">

                        <div>
                            <span class="fw-semibold">AI Auto-Correct</span>
                            <i class="bi bi-info-circle text-muted"
                               data-bs-toggle="tooltip"
                               data-bs-placement="right"
                               title="When enabled, AI automatically corrects grammar, spelling, and capitalization.">
                            </i>
                            <div class="small text-muted">
                                Improve text automatically before saving.
                            </div>
                        </div>

                        <div class="form-check form-switch m-0">
                            <input type="hidden" name="ai_auto_correct" value="0">

                            <input class="form-check-input"
                                   type="checkbox"
                                   id="aiAutoCorrect"
                                   name="ai_auto_correct"
                                   value="1">
                        </div>

                    </div>
                 
        
                    <!-- Course Code -->
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">
                            Title
                        </label>

                        <input
                            type="text"
                            class="form-control ai-clean"
                            name="report_title"
                            id="report_title"
                            placeholder="Title" required>
              
                    </div>

                    <!-- Course Years -->
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">
                            Description
                        </label>

                        <textarea class="form-control ai-clean" name="report_description" id="report_description" placeholder="Description" required></textarea>
                        
                    </div>

                    <!-- Course Name -->
                    <div class="col-12">
                        <label class="form-label fw-semibold">
                           Attachment (optional)
                        </label>                    

                        <input class="form-control" type="file" id="approval-attachment" name="attachment">
                    </div>


                     <div class="col-md-12">
                        <label class="form-label">Approval Queue</label>
                        <select name="department" class="form-control" required>
                        <option value="">All Departments</option>

                            <?php foreach ($departments as $department): ?>
                                <option value="<?= $department['department_id'] ?>">
                                    <?= htmlspecialchars($department['department_name']) ?>
                                </option>
                            <?php endforeach; ?>

                        </select>

                        </div>
                            



                </form>

            </div>

            <!-- Footer -->
            <div class="modal-footer">

                <button type="button"
                        class="btn btn-primary px-4"
                        id="addReportApprovalSubmit">
                    Submit
                </button>

            </div>

        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/gsap@3.14.1/dist/gsap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>   const BASE_URL = "<?php echo BASE_URL ?>" </script>
<script src="<?php echo BASE_URL ?>/js/report_approval.js"></script>

<?php include  __DIR__ .'/../partials/footer.php'; ?>

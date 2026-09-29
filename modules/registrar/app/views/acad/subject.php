<?php include __DIR__ .'/../partials/sidebar.php'; ?>
<?php include  __DIR__ .'/../partials/header.php'; ?>

<main class="main-content">
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">
    
        <div id="dashboard-title">
            <h1 class="h3 fw-bold mb-0">Subjects Management</h1>
             <p class="text-muted small">Manage and organize academic programs for the registrar.</p>
        </div>
        

        <button class="btn btn-primary btn-sm" id="addSubject">
            <i class="bi bi-plus-lg"></i> Add Subjects
        </button>

    </div>


    <div class="card shadow-sm">

        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="fs-6 mb-0 text-primary">Strand List</h3>

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
                    <select class="form-select form-select-sm" id="order">
                        <option value="desc">Descending (Z-A)</option>
                        <option value="asc">Ascending (A-Z)</option>
                    </select>
                </div>

                <div class="col-md-5">
                    <label class="form-label small">Search</label>
                    <input type="text"
                        class="form-control form-control-sm"
                        placeholder="Search subjects..."
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
                            <th>No.</th>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Units</th>
                            <th>Lecture Hours</th>
                            <th>Lab Hours</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody id="studentsTableBody">
                    </tbody>

                </table>

            </div>


            
            <div class="d-flex align-items-center justify-content-between mt-3">

                <div class="fw-semibold small" id="pageInfo"></div>

                <div id="pagination" class="d-flex gap-1"></div>

            </div>

        </div>

    </div>

</div>
</main>

 <!-- add course modal -->

<div class="modal fade" id="addSubjectModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content shadow-sm">

            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title fw-semibold">
                    Add Subject
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" id="closeBtn"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">

                <form class="row g-3" id="subjectForm" action="<?php echo BASE_URL ?>/subject/store" method="POST">

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

                    <!-- Subject Code -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Strand Code
                        </label>

                        <input
                            type="text"
                            class="form-control ai-clean"
                            name="subject_code"
                            id="subject_code"
                            placeholder="e.g. IS001, ENG1">

                        <div class="invalid-feedback" id="error-subject_code"></div>
                    </div>

                    <!-- Subject Name -->
                    <div class="col-12">
                        <label class="form-label fw-semibold">
                            Subject Name
                        </label>

                        <input
                            type="text"
                            class="form-control ai-clean"
                            name="subject_name"
                            id="subject_name"
                            placeholder="e.g. Introduction to Information System...">

                        <div class="invalid-feedback" id="error-subject_code"></div>
                    </div>

                    <!-- subject unit -->

                    <div class="col-4">
                        <label class="form-label fw-semibold">
                            Subject Unit
                        </label>

                        <input
                            type="number"
                            class="form-control ai-clean"
                            name="subject_unit"
                            id="subject_unit"
                            placeholder="e.g. 1">

                        <div class="invalid-feedback" id="error-subject_unit"></div>
                    </div>
                     <!-- subject lecture hours -->
                    <div class="col-4">
                        <label class="form-label fw-semibold">
                            Lecture Hours
                        </label>

                        <input
                            type="number"
                            class="form-control ai-clean"
                            name="subject_lecture"
                            id="subject_lecture"
                             placeholder="e.g. 3">

                        <div class="invalid-feedback" id="error-subject_lecture"></div>
                    </div>

                    <!-- subject lab hours -->

                    <div class="col-4">
                        <label class="form-label fw-semibold">
                            Lab Hours 
                        <i class="bi bi-info-circle text-muted"
                               data-bs-toggle="tooltip"
                               data-bs-placement="right"
                               title="When enabled, AI automatically corrects grammar, spelling, and capitalization.">
                            </i>
                        </label>

                        <input
                            type="number"
                            class="form-control ai-clean"
                            name="subject_lab"
                            id="subject_lab"
                             placeholder="e.g. 2">

                        <div class="invalid-feedback" id="error-subject_lab"></div>
                    </div>

                </form>

            </div>

            <!-- Footer -->
            <div class="modal-footer">
                <button type="button"
                        class="btn btn-primary px-4"
                        id="addSubjectSubmit">
                    Submit
                </button>

            </div>

        </div>
    </div>
</div>

 <!-- edit course modal -->

<div class="modal fade" id="editStrandModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
     <div class="modal-dialog modal-md">
        <div class="modal-content shadow-sm">

        <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title fw-semibold">
                    Edit Strand
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" id="editCloseBtn"></button>
            </div>

        
        <div class="modal-body">
            
     <form class="row g-3" id="editStrandForm" method="POST">

        <div class="col-md-6">
            <label for="inputEmail4" class="form-label">Strand Code</label>
            <input type="text" class="form-control" name="edit_strand_code" id="edit_strand_code" >
            <div class="invalid-feedback" id="error-edit_strand_code"></div>
        </div>

         <div class="col-md-12">
            <label for="inputPassword4" class="form-label">Strand Name</label>
            <input type="text" class="form-control" name="edit_strand_name" id="edit_strand_name">
            <div class="invalid-feedback" id="error-edit_strand_name"></div>
        </div>
        
        </form>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="editStrandSubmit">Submit</button>
        </div>
        </div>
    </div>
</div>

 <!-- show course modal -->

<!-- <div class="modal fade" id="showCourseModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h1 class="modal-title fs-5" id="showModalTitle">Add Course</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="closeBtn"></button>
        </div>
        <div class="modal-body">
            
     <form class="row g-3" id="courseForm">

        <div class="col-md-6">
            <label for="inputEmail4" class="form-label">Strand Code</label>
            <input type="text" class="form-control"  id="show_strand_code" readonly>
        </div>

         <div class="col-md-12">
            <label for="inputPassword4" class="form-label">Course Name</label>
            <textarea type="text" class="form-control" id="show_strand_name" readonly></textarea>
        </div>
        
        </form>

        </div>
        </div>
    </div>
</div> -->

<script src="https://cdn.jsdelivr.net/npm/gsap@3.14.1/dist/gsap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script> const BASE_URL = "<?php echo BASE_URL ?>" </script>
<script src="<?= BASE_URL ?>/js/subject.js"></script>

<?php include  __DIR__ .'/../partials/footer.php'; ?>
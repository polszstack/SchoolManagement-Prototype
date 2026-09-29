<?php include __DIR__ .'/../partials/sidebar.php'; ?>
<?php include  __DIR__ .'/../partials/header.php'; ?>


<main class="main-content">
    <div class="container">
            <div id="title" class="mb-5">
                <h1 class="text-bold" id="dashboard-title">Students</h1>
            </div>
        
  
    <div class="card shadow-sm">

     <div class="card-header">
        <h3 class="card-title fs-5">Student Lists</h3>
    </div>

      <div class="card-body">

       <div class="d-flex justify-content-between align-items-center">

            
        <div class="input-group mb-4" >
            <label class="input-group-text" for="perPageSelect">Per Page:</label>
            <select class="form-select col-2" id="limit">
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="20">20</option>
                    <option value="40">40</option>
                </select>
        </div>

        <div class="mb-4 p-2 d-flex align-items-center gap-2">

              <button type="button" class="btn btn-danger btn-sm" id="pdf">
                 <i class="fas bi-file-earmark-pdf-fill"></i>
                  PDF
              </button>

            
             <button type="button" class="btn btn-success btn-sm" id="excel">
                 <i class="fas bi-file-earmark-excel-fill"></i>
                 Excel
             </button>

            
              <button type="button" class="btn btn-primary btn-sm" id="csv">
                  <i class="fas bi-filetype-csv"></i>
                  CSV
              </button>
        </div>
        </div>

        <div class="input-group mb-3" >
            <select class="form-select col-2" id="status">
                <option value="all">All</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="graduated">Graduated</option>
            </select>

            <input type="text"
                class="form-control"
                placeholder="Search..." id="search">

        </div>

  
      
        <table class="table table-hover table-striped">
            <thead>
                <tr>    
                <th scope="col">Student No.</th>
                <th scope="col">Name</th>
                <th scope="col">Course</th>
                <th scope="col">Status</th>
                <th scope="col" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody id="studentsTableBody">

        
             
            </tbody>
         </table>

         

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

<script src="https://cdn.jsdelivr.net/npm/gsap@3.14.1/dist/gsap.min.js"></script>

<script>   const BASE_URL = "<?php echo BASE_URL ?>" </script>
<script src="<?php echo BASE_URL ?>/js/student.js"></script>

<?php include  __DIR__ .'/../partials/footer.php'; ?>
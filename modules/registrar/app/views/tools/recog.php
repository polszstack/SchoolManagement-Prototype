<?php include __DIR__ .'/../partials/sidebar.php'; ?>
<?php include  __DIR__ .'/../partials/header.php'; ?>

<main class="main-content">

    <div class="container">
            <div id="title" class="mb-5" >
                <h1 class="text-bold" id="dashboard-title">Optical Character Recognition</h1>
            </div>
        
  
    <div class="card shadow-sm mt-3">

     <div class="card-header">
        <h3 class="card-title fs-5">File Upload</h3>
    </div>

      <div class="card-body">


      <div class="mb-3 p-4">
            <form method="POST" action="<?= BASE_URL ?>/scan" enctype="multipart/form-data"'>
             <input type="file" name="file" required>
            <button type="submit" class="btn btn-primary mt-2 w-100" name="scan">Scan</button>
            </form>
     </div>



    </form>

      </div>

    </div>


  <?php if (!empty($result)): ?>

    <div class="card shadow-sm mt-5">

     <div class="card-header d-flex align-items-center justify-content-between">
        <h3 class="card-title fs-5">Result</h3>
        <div class="card-tools ">
                <button type="button" class="btn btn-sm btn-default" id='copyText'">
                    <i class="fas fa-copy"></i> Copy Text
                </button>
        </div>
    </div>

      <div class="card-body">

    
            <pre><?= htmlspecialchars($result) ?></pre>
      
      </div>

    </div>
    <?php endif; ?>
        

        
    </div>
</main>


<script src="https://cdn.jsdelivr.net/npm/gsap@3.14.1/dist/gsap.min.js"></script>

<?php include  __DIR__ .'/../partials/footer.php'; ?>
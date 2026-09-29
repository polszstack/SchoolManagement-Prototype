<?php include __DIR__ .'/../partials/sidebar.php'; ?>
<?php include  __DIR__ .'/../partials/header.php'; ?>


<main class="main-content">
    <div class="container">
            <div id="title" class="mb-5">
                <h1 class="text-bold" id="dashboard-title">Activity Calendar</h1>
            </div>
    
  

    <div class="card shadow-sm">

     <div class="card-header">
        <h3 class="card-title fs-5">Calendar</h3>
    </div>

      <div class="card-body">

          <div id='calendarDiv'></div>
    
      </div>
    </div>        
    </div>
</main>


<div class="modal fade"  tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" id="calendarModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" id="header" >
        <h1 class="modal-title fs-5" id="modalTitle">Modal title</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="d-flex gap-2">
         <div class="mb-2" id="start-container">
            <label for="recipient-name" class="col-form-label fw-bold">Start: </label>
            <input type="text" id="start" class="form-control" id="recipient-name" readonly>
          </div>

          <div class="mb-2" id="end-container">
            <label for="recipient-name" class="col-form-label fw-bold">End: </label>
            <input type="text" id="end" class="form-control" id="recipient-name" readonly >
          </div>
        </div>

         <div class="mb-3">
            <label for="message-text" class="col-form-label fw-bold">Description:</label>
            <textarea class="form-control" id="description" readonly></textarea>
          </div>



      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/gsap@3.14.1/dist/gsap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.4/dist/confetti.browser.min.js"></script>
   

<script> const BASE_URL = "<?php echo BASE_URL ?>"</script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.20/index.global.min.js'></script>
<script src="<?php echo BASE_URL ?>/js/calendar.js"></script>

<?php include  __DIR__ .'/../partials/footer.php'; ?>
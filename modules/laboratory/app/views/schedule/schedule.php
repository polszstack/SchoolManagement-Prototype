<?php include  __DIR__ .'/../includes/sidebar.php'; ?>
<?php include  __DIR__ .'/../includes/header.php'; ?>

<link rel="stylesheet" href="/SchoolManagementSystem/assets/css/style.css">


<main class="main-content">
    <div class="container-fluid px-4">
        <h1 class="h3 mb-2 text-gray-800">Schedule</h1>
        <p class="mb-4">Calendar</p>

        <div class="card mb-4 card shadow-sm border-0 border-top border-4 border-secondary shadow-lg p-3">

           <div class="card-body">
                <div id='homeCalendar' style="height: 100%; width: 100%;"></div>
            </div>
            
        </div>
    </div>
</main>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.20/index.global.min.js'></script>

<script> const BASE_URL = "<?= BASE_URL ?>"; </script>
<script src="<?= BASE_URL ?>/js/calendar.js"></script>

<?php include  __DIR__ .'/../includes/footer.php'; ?>
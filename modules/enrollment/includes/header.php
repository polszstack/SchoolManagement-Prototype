html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Enrollment System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title><?php echo $pageTitle ?? 'School Management System'; ?></title>
    
    <!-- Add your main script.js file -->
    <script src="../js/script.js" type="module"></script>
    <style>
        .content { margin-left: 250px; padding: 20px; margin-top: 45px; }
    </style>
</head>
<header>
    <div class="hamburger">
      <span></span>
      <span></span>
      <span></span>
    </div>
    <div class="realtime" id="realtimeClock" aria-live="polite">--:-- </div>
</header>
<body></body>
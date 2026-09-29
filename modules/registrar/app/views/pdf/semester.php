<?php
    $logo = dirname(__DIR__, 2) . '/assets/images/bestlink.png';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
    @page {
        margin: 120px 50px 80px 50px;
    }

    .pagenum:before { content: counter(page); }

    body { font-family: DejaVu Sans, sans-serif; }

    header {
        position: fixed;
        top: -100px;  
        left: 0;
        right: 0;
        height: 100px;
        text-align: center;
    }

    footer {
        position: fixed;
        bottom: -50px;
        left: 0;
        right: 0;
        height: 50px;
        text-align: center;
        font-size: 12px;
    }

    table { width: 100%; border-collapse: collapse;}
    th, td { padding: 6px; border:  none; }

    .content { 
        margin-top: 40px;
        margin-bottom: 50px;
    }
</style>

</head>
<body>

<header>
    <table width="100%">
        <tr>
            <td width="15%">
                <img src="<?php echo $school_image?>" height="60" alt="school Image">
            </td>
            <td width="70%" align="center">
                <strong>Bestlink College of the Philippines - Bulacan Inc.</strong>
                <span style="display: block; font-size:12px;">Lot 1 Ipo Road, Minuyan Proper, 
                    City of San Jose Del Monte, Bulacan
                </span>
                <span style="display: block; font-size:11px;">(044) 797-2949</span>
            </td>
            <td width="15%" align="right">
                <img src="<?php echo $ched_image ?>" height="60" alt="ched Image">
            </td>
        </tr>
    </table>
</header>



<div class="content">
    <h3 style="text-align:center;"> Semester List </h3>

    <table width="100%">
        <thead>
            <tr>
                <th>No.</th>
                <th>Semester</th>
                <th>School Year</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
      
            <?php foreach ($semesters as $key => $semester): ?>
            <tr>
                <td><?php echo $key + 1; ?></td>
                <td><?php echo $semester['semester']; ?></td>
                <td><?php echo $semester['school_year'] ?></td>
                <td><?php echo $semester['status'] ? 'Active' : 'Inactive'; ?></td>
            </tr>
        <?php endforeach; ?>


        </tbody>
    </table>
</div>


<footer>
    Page <span class="pagenum"></span>
</footer>

</body>


</html>
<aside class="sidebar">
        <div class="school-logo">
            <img src="<?php echo BASE_URL ?>/assets/images/bcp-logo.png" alt="School Logo">


           <div class="sidebar-icons d-flex align-items-center gap-3">

                <button type="button"  id="notif-btn" class="btn position-relative p-0 border-0 bg-transparent">
                    <i class="fa-regular fa-bell fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        5
                    </span>
                </button>

                <button type="button" class="btn p-0 border-0 bg-transparent">
                    <i class="fa-regular fa-circle-user fs-5"></i>
                </button>

            </div>
       

        </div>
        <div class="sidebar-header">
            <div class="user-avatar">UA</div>
            <h1><?=  $user['first_name']." ".$user['last_name'] ?? 'Username' ?> </h1>
            <p class="user-id"><?=  $user['employee_id'] ?? 'SAMPLEID123456789' ?> </p>
        </div>


        <div class="accordion" id="sidebarMenu">

        <!-- dashboard -->

        <h2>Dashboard</h2>
        <ul>
            <li><a href="<?php echo BASE_URL ?>" class="menu-link <?php echo CURRENT_URI  === "template2" ? 'active' : '' ?> " id="dashboard">Dashboard</a></li>
        </ul>

        <!-- student management -->

        <hr>
        <h2>
            <a data-bs-toggle="collapse" href="#studentMenu" class="menu-toggle d-flex justify-content-between align-items-center text-decoration-none text-white" id="StudentDriver">
            Student Management
            <i class="fa-solid fa-chevron-down"></i>
            </a>
        </h2>

        <div id="studentMenu" class="collapse <?php echo in_array(CURRENT_URI, ['students','enrollees']) ? 'show' : '' ?>"  data-bs-parent="#sidebarMenu">
        <ul>
             <li><a href="<?php echo BASE_URL?>/students" class="menu-link <?php echo CURRENT_URI  === "students" ? 'active' : '' ?>" id="student">Students</a></li>
             <li><a href="<?php echo BASE_URL?>/enrollee" class="menu-link <?php echo CURRENT_URI  === "enrollee" ? 'active' : '' ?>" id="enrollee">Enrollees</a></li>
             
        </ul>
        </div>

        <!-- Academic Management -->

        <hr>
        <h2>
            <a data-bs-toggle="collapse" href="#schoolMenu" class="menu-toggle d-flex justify-content-between align-items-center text-decoration-none text-white" id="AcademicDriver">
             Academic Management
             <i class="fa-solid fa-chevron-down"></i>
            </a>
        </h2>
         <div id="schoolMenu" class="collapse <?php echo in_array(CURRENT_URI, ['course','school-year','semester','strand','subject']) ? 'show' : '' ?>"  data-bs-parent="#sidebarMenu">
        <ul>
            <li><a href="<?php echo BASE_URL?>/course" class="menu-link <?php echo CURRENT_URI  === "course" ? 'active' : '' ?>" id="course">Courses</a></li>
            <li><a href="<?php echo BASE_URL?>/school-year" class="menu-link <?php echo CURRENT_URI  === "school-year" ? 'active' : '' ?>" id="course">School Year</a></li>
            <li><a href="<?php echo BASE_URL?>/semester" class="menu-link <?php echo CURRENT_URI  === "semester" ? 'active' : '' ?>" id="semester">Semesters</a></li>
            <li><a href="<?php echo BASE_URL?>/strand" class="menu-link <?php echo CURRENT_URI  === "strand" ? 'active' : '' ?>" id="strand">Strands</a></li>
            <li><a href="<?php echo BASE_URL?>/subject" class="menu-link <?php echo CURRENT_URI  === "subject" ? 'active' : '' ?>" id="course">Subjects</a></li>
            <li><a href="<?php echo BASE_URL?>/room" class="menu-link <?php echo CURRENT_URI  === "room" ? 'active' : '' ?>" id="course">Rooms</a></li>
              
        </ul>
        </div>

        <!-- reports and request -->

        <hr>
        <h2>
             <a data-bs-toggle="collapse" href="#schoolReports" class="menu-toggle d-flex justify-content-between align-items-center text-decoration-none text-white" id="ReportDriver">
             Reports and Requests
             <i class="fa-solid fa-chevron-down"></i>
            </a>
        </h2>
         <div id="schoolReports" class="collapse <?php echo in_array(CURRENT_URI, ['reports','file','reports-approval','reports-submit']) ? 'show' : '' ?>"  data-bs-parent="#sidebarMenu">
        <ul>
             <li><a href="<?php echo BASE_URL?>/reports" class="menu-link <?php echo CURRENT_URI  === "reports" ? 'active' : '' ?>" id="reports">Reports</a></li>
              <li><a href="<?php echo BASE_URL?>/file" class="menu-link <?php echo CURRENT_URI  === "file" ? 'active' : '' ?>" id="file">File Requests</a></li> 
              <li><a href="<?php echo BASE_URL?>/reports-approval" class="menu-link <?php echo CURRENT_URI  === "reports-approval" ? 'active' : '' ?>" id="reports-approval">Approval & Decision Support</a></li>
               <li><a href="<?php echo BASE_URL?>/reports-submit" class="menu-link <?php echo CURRENT_URI  === "reports-submit" ? 'active' : '' ?>" id="reports-submit">Report Submission Management</a></li>
        </ul>
        </div>

         <!-- tools  -->

        <hr>
        <h2>
             <a data-bs-toggle="collapse" href="#schoolTools" class="menu-toggle d-flex justify-content-between align-items-center text-decoration-none text-white" id="ToolDriver"> 
             Tools 
             <i class="fa-solid fa-chevron-down"></i>
            </a>
        </h2>
           <div id="schoolTools" class="collapse <?php echo in_array(CURRENT_URI, ['recog','calendar']) ? 'show' : '' ?>"  data-bs-parent="#sidebarMenu">
        <ul>
            <li><a href="<?php echo BASE_URL?>/tools/recog" class="menu-link <?php echo CURRENT_URI  === "recog" ? 'active' : '' ?> " id="ocr">Image-to-Text</a></li>
            <li><a href="<?php echo BASE_URL?>students" class="menu-link">Notes</a></li>
            <li><a href="<?php echo BASE_URL?>/calendar" class="menu-link <?php echo CURRENT_URI  === "calendar" ? 'active' : '' ?>" id="calendar">Calendar</a></li>
        </ul>
            </div>

             <!-- settings  -->
        <hr>
        <h2>
          <a data-bs-toggle="collapse" href="#schoolSettings" class="menu-toggle d-flex justify-content-between align-items-center text-decoration-none text-white" id="SettingDriver">
             Settings
             <i class="fa-solid fa-chevron-down"></i>
            </a>
        </h2>
        <div id="schoolSettings" class="collapse <?php echo in_array(CURRENT_URI, ['activity','profile','']) ? 'show' : '' ?>"  data-bs-parent="#sidebarMenu">
            <ul>
                 <li><a href="<?php echo BASE_URL?>/settings/concerns" class="menu-link <?php echo CURRENT_URI  === "concerns" ? 'active' : '' ?>"" id="concerns">Concerns & Issue Tracking</a></li>    
                <li><a href="<?php echo BASE_URL?>students" class="menu-link" id="profile">Profile</a></li>      
                <li><a href="<?php echo BASE_URL?>/settings/activity" class="menu-link <?php echo CURRENT_URI  === "activity" ? 'active' : '' ?>"" id="profile">Activity Logs</a></li>      
            </ul>
        </div>

 </div>
       
</aside>




 
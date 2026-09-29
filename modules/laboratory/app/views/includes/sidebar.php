<aside class="sidebar">
    <div class="school-logo">
        <img src="<?= BASE_URL ?>/assets/bcp-logo.png" alt="School Logo">
        <div class="sidebar-icons">
            <i class="fa-regular fa-bell"></i>
            <i class="fa-regular fa-circle-user"></i>
        </div>
    </div>
    <div class="sidebar-header">
        <div class="user-avatar">UA</div>
        <h1>Username</h1>
        <p class="user-id">SAMPLEID123456789</p>
    </div>
    <h2>Laboratory</h2>
    <ul id="accordionSidebar">
        <li><a href="<?= BASE_URL ?>" class="menu-link <?php echo CURRENT_URI == 'laboratory'? "active" :  "" ?>">Dashboard</a></li>
        <!-- Laboratories -->
        <li class="nav-item">
            <a class="menu-link collapsed d-flex justify-content-between align-items-center"
                href="#"
                data-bs-toggle="collapse"
                data-bs-target="#collapseUtilities"
                aria-expanded="false"
                aria-controls="collapseUtilities">
                <span>Laboratories</span>
                <i class="bi bi-chevron-right dropdown-icon fs-6"></i>
            </a>

            <div id="collapseUtilities"
                class="collapse"
                data-bs-parent="#accordionSidebar">

                <div class="py-2 px-3 rounded shadow-sm w-100 accordionSidebar-inner">

                    <a class="dropdown-item menu-link text-white d-flex justify-content-between align-items-center collapsed"
                        data-bs-toggle="collapse"
                        href="#collapsePhysics"
                        role="button"
                        aria-expanded="false"
                        aria-controls="collapsePhysics">
                        <span>Physics Lab</span>
                        <i class="bi bi-chevron-right dropdown-icon fs-6"></i>
                    </a>

                    <div class="collapse ps-3" id="collapsePhysics">
                        <a href="<?= BASE_URL ?>/inventory" class="dropdown-item menu-link small text-white <?php echo CURRENT_URI == 'inventory'? "active" :  "" ?>">Inventory</a>
                        <a href="<?= BASE_URL ?>/damages" class="dropdown-item menu-link small text-white <?php echo CURRENT_URI == 'damages'? "active" :  "" ?>">Damages</a>
                        <a href="<?= BASE_URL ?>/borrow" class="dropdown-item menu-link small text-white <?php echo CURRENT_URI == 'borrow'? "active" :  "" ?>">Borrows</a>
                    </div>
                    
                    <a class="dropdown-item menu-link text-white d-flex justify-content-between align-items-center collapsed"
                        data-bs-toggle="collapse"
                        href="#collapsePsychology"
                        role="button"
                        aria-expanded="false"
                        aria-controls="collapsePsychology">
                        <span>Psychology Lab</span>
                        <i class="bi bi-chevron-right dropdown-icon fs-6"></i>
                    </a>

                    <div class="collapse ps-3" id="collapsePsychology">
                        <a href="<?= BASE_URL ?>/psycho-inventory" class="dropdown-item menu-link small text-white <?php echo CURRENT_URI == 'psycho-inventory'? "active" :  "" ?>">Inventory</a>
                        <a href="<?= BASE_URL ?>/psycho-damage" class="dropdown-item menu-link small text-white <?php echo CURRENT_URI == 'psycho-damage'? "active" :  "" ?>">Damages</a>
                        <a href="#" class="dropdown-item menu-link small text-white">Borrows</a>
                    </div>

                    <a class="dropdown-item menu-link text-white d-flex justify-content-between align-items-center collapsed"
                        data-bs-toggle="collapse"
                        href="#collapseHE"
                        role="button"
                        aria-expanded="false"
                        aria-controls="collapseHE">
                        <span>HE Lab</span>
                        <i class="bi bi-chevron-right dropdown-icon fs-6"></i>
                    </a>

                    <div class="collapse ps-3" id="collapseHE">
                        <a href="<?= BASE_URL ?>/he-inventory" class="dropdown-item menu-link small text-white <?php echo CURRENT_URI == 'he-inventory'? "active" :  "" ?>">Inventory</a>
                        <a href="#" class="dropdown-item menu-link small text-white">Damages</a>
                        <a href="#" class="dropdown-item menu-link small text-white">Borrows</a>
                    </div>

                    <!-- Crim Lab with Submenu -->
                    <a class="dropdown-item menu-link text-white d-flex justify-content-between align-items-center collapsed"
                        data-bs-toggle="collapse"
                        href="#collapseCrim"
                        role="button"
                        aria-expanded="false"
                        aria-controls="collapseCrim">
                        <span>Crim Lab</span>
                        <i class="bi bi-chevron-right dropdown-icon fs-6"></i>
                    </a>

                    <div class="collapse ps-2 text-white" id="collapseCrim">
                        <a href="<?= BASE_URL ?>/crim-inventory" class="dropdown-item menu-link small text-white <?php echo CURRENT_URI == 'crim-inventory'? "active" :  "" ?>">Inventory</a>
                        <a href="<?= BASE_URL ?>/crim-borrow" class="dropdown-item menu-link small fw-light <?php echo CURRENT_URI == 'crim-borrow'? "active" :  "" ?>">Borrows</a>
                        <a href="<?= BASE_URL ?>/crim-damage" class="dropdown-item menu-link small fw-light <?php echo CURRENT_URI == 'crim-damage'? "active" :  "" ?>">Damages</a>
                    </div>

                    <!-- IT Lab with Submenu -->
                    <a class="dropdown-item menu-link text-white d-flex justify-content-between align-items-center collapsed"
                        data-bs-toggle="collapse"
                        href="#collapseIT"
                        role="button"
                        aria-expanded="false"
                        aria-controls="collapseIT">
                        <span>IT Lab</span>
                        <i class="bi bi-chevron-right dropdown-icon fs-6"></i>
                    </a>

                    <div class="collapse ps-2" id="collapseIT">
                         <a href="<?= BASE_URL ?>/it-inventory" class="dropdown-item menu-link small text-white <?php echo CURRENT_URI == 'it-inventory'? "active" :  "" ?>">Inventory</a>
                        <a href="#" class="dropdown-item menu-link small fw-light text-white">Damages</a>
                        <a href="#" class="dropdown-item menu-link small fw-light text-white">Borrows</a>
                    </div>

                </div>
            </div>
        </li>


        <!-- Reports -->
        <li class="nav-item">
            <a class="menu-link collapsed d-flex justify-content-between align-items-center"
                href="#"
                data-bs-toggle="collapse"
                data-bs-target="#collapseReports"
                aria-expanded="false"
                aria-controls="collapseReporta">
                <span>Reports</span>
                <i class="bi bi-chevron-right dropdown-icon fs-6"></i>
            </a>

            <div id="collapsereports"
                class="collapse"
                data-bs-parent="#accordionSidebar">

                <div class="text-white py-2 px-3 rounded shadow-sm w-100">
                    <a class="dropdown-item menu-link" href="#">Inventory Reports</a>
                    <a class="dropdown-item menu-link" href="#">Damaged Equipment</a>
                    <a class="dropdown-item menu-link" href="#">Laboratory Usage</a>
                    <a class="dropdown-item menu-link" href="#">Monthly Schedule</a>
                </div>
            </div>
        </li>

        <li><a href="" class="menu-link">Users</a></li>
         <a href="<?= BASE_URL ?>/schedule" class="dropdown-item menu-link small fw-light <?php echo CURRENT_URI == 'schedule'? "active" :  "" ?>">Schedule</a>
        <li><a href="" class="menu-link">Reports</a></li>
        <li><a href="<?= BASE_URL ?>/approval-decision-support" class="menu-link">Approval Decision Support</a></li>
        <li><a href="<?= BASE_URL ?>/concern-issue-tracking" class="menu-link">Concerns Issue Tracking</a></li>
        <li><a href="<?= BASE_URL ?>/report-submission-management" class="menu-link">Report Submmision Management</a></li>

    </ul>


    <div class="sidebar-header">
    </div>

    <ul>
        <li><a href="#" class="menu-link">Settings</a></li>
        <li><a href="#" class="menu-link">Lagout</a></li>
    </ul>
</aside>
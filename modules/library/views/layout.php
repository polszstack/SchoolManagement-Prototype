<?php
/**
 * views/layout.php
 * Master layout — BCP design + Library Management System
 */
$userInitial = strtoupper(substr($employeeName, 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= htmlspecialchars($libraryName) ?> | BCP</title>
    <link rel="icon" type="image/png" href="assets/images/bcp-logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/components.css">
    <link rel="stylesheet" href="assets/css/ai_features.css">
    <link rel="stylesheet" href="assets/css/dropdown.css">
</head>
<body>

<!-- ====== PAGE LOADER ====== -->
<div class="page-loader" id="pageLoader">
    <div class="loader-logo">
        <img src="assets/images/bcp-logo.png" alt="BCP Logo">
    </div>
    <div class="loader-spinner"></div>
    <div class="loader-text">Loading...</div>
</div>

<!-- Sidebar overlay for mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="app-container">

<!-- ====== SIDEBAR (BCP Style) ====== -->
<aside class="sidebar" id="sidebar">

    <!-- Logo row + icons -->
    <div class="school-logo">
        <img src="assets/images/bcp-logo.png" alt="BCP Logo">
        <div class="sidebar-icons">
            <!-- Bell Icon + Notification Dropdown -->
            <div class="icon-wrapper" id="bellWrapper">
                <i class="fa-regular fa-bell" id="bellBtn"></i>
                <div class="icon-dropdown" id="bellDropdown">
                    <div class="dropdown-header">
                        <span>Notifications</span>
                        <button class="mark-all-read">Mark all as read</button>
                    </div>
                    <ul class="notif-list">
                        <li class="notif-item">
                            <div class="notif-icon"><i class="fas fa-book"></i></div>
                            <div class="notif-content">
                                <p>Library system ready</p>
                                <span>Just now</span>
                            </div>
                        </li>
                    </ul>
                    <div class="dropdown-footer">
                        <a href="#">View all notifications</a>
                    </div>
                </div>
            </div>

            <!-- User Icon + Profile Dropdown -->
            <div class="icon-wrapper" id="userWrapper">
                <i class="fa-regular fa-circle-user" id="userBtn"></i>
                <div class="icon-dropdown" id="userDropdown">
                    <div class="dropdown-header">
                        <div class="dropdown-user-info">
                            <div class="dropdown-avatar"><?= $userInitial ?></div>
                            <div>
                                <strong><?= htmlspecialchars($employeeName ?? 'Librarian') ?></strong>
                                <span><?= htmlspecialchars($userRole ?? 'Staff') ?></span>
                            </div>
                        </div>
                    </div>
                    <ul class="user-menu">
                        <li><a href="#"><i class="fa-regular fa-user"></i> Profile Settings</a></li>
                        <li><a href="#"><i class="fa-solid fa-lock"></i> Change Password</a></li>
                        <li class="divider"></li>
                        <li>
                            <a href="auth/logout.php" class="signout-link">
                                <i class="fa-solid fa-right-from-bracket"></i> Sign Out
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- User info -->
    <div class="sidebar-header">
        <div class="user_avatar"><?= $userInitial ?></div>
        <h1 class="employee_name"><?= htmlspecialchars($employeeName ?? 'Librarian') ?></h1>
        <p class="employee_position"><?= htmlspecialchars($userPosition ?? $userRole ?? 'Staff') ?></p>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav">
        <p class="sidebar-nav-section-title">Library</p>
        <ul>
            <li><button class="tab active" onclick="app.ui.switchTab('dashboard')"><i class="fas fa-tachometer-alt"></i> Dashboard</button></li>
            <li><button class="tab" onclick="app.ui.switchTab('books')"><i class="fas fa-book"></i> Books</button></li>
            <li><button class="tab" onclick="app.ui.switchTab('gallery')"><i class="fas fa-th-large"></i> Book Gallery</button></li>
        </ul>
        <div class="separator"></div>
        <p class="sidebar-nav-section-title">Circulation</p>
        <ul>
            <li><button class="tab" onclick="app.ui.switchTab('borrowers')"><i class="fas fa-users"></i> Members</button></li>
            <li><button class="tab" onclick="app.ui.switchTab('borrow')"><i class="fas fa-handshake"></i> Borrow Book</button></li>
            <li><button class="tab" onclick="app.ui.switchTab('return')"><i class="fas fa-undo"></i> Return Book</button></li>
            <li><button class="tab" onclick="app.ui.switchTab('transactions')"><i class="fas fa-list-alt"></i> Transactions</button></li>
        </ul>
        <div class="separator"></div>
        <p class="sidebar-nav-section-title">Analytics</p>
        <ul>
            <li><button class="tab" onclick="app.ui.switchTab('reports')"><i class="fas fa-chart-bar"></i> Reports</button></li>
            <li><button class="tab" onclick="app.ui.switchTab('ai')"><i class="fas fa-robot"></i> AI Insights</button></li>
            <li><button class="tab" onclick="app.ui.switchTab('settings')"><i class="fas fa-cog"></i> Settings</button></li>
        </ul>
    </nav>

    <div class="sidebar-footer">
        <i class="fas fa-clock"></i> <?= htmlspecialchars($currentDate) ?>
    </div>
</aside>

<!-- ====== HEADER (BCP Style) ====== -->
<header id="mainHeader">
    <div class="hamburger" id="hamburger">
        <span></span><span></span><span></span>
    </div>
    <div class="header-right">
        <div class="realtime" id="realtimeClock" aria-live="polite">--:--:--</div>
        <div class="header-actions-btns">
            <button class="btn btn-outline btn-sm" onclick="app.dashboard.load()"><i class="fas fa-sync-alt"></i> Refresh</button>
            <button class="btn btn-sm" onclick="app.ui.switchTab('borrow')"><i class="fas fa-handshake"></i> Quick Borrow</button>
        </div>
    </div>
</header>

<!-- ====== MAIN CONTENT ====== -->
<main class="main-content" id="mainContent">

    <!-- Notification Bar -->
    <div class="notification" id="notification">
        <i class="fas fa-info-circle" id="notifIcon"></i>
        <span id="notifText"></span>
        <button class="notification-close" onclick="app.ui.hideNotification()"><i class="fas fa-times"></i></button>
    </div>

    <!-- Page Banner -->
    <div class="page-banner">
        <img src="assets/images/bcp-logo.png" alt="BCP">
        <div>
            <div class="page-banner-title"><?= htmlspecialchars($libraryName) ?></div>
            <div class="page-banner-sub">Bestlink College of the Philippines — Library Management System</div>
        </div>
    </div>

    <!-- Tab Views -->
    <?php require __DIR__ . '/partials/dashboard.php'; ?>
    <?php require __DIR__ . '/partials/books.php';        ?>
    <?php require __DIR__ . '/partials/gallery.php';      ?>
    <?php require __DIR__ . '/partials/members.php';      ?>
    <?php require __DIR__ . '/partials/borrow.php';       ?>
    <?php require __DIR__ . '/partials/transactions.php'; ?>
    <?php require __DIR__ . '/partials/reports.php';      ?>
    <?php require __DIR__ . '/partials/settings.php';     ?>
    <?php require __DIR__ . '/partials/ai_features.php';  ?>

</main>

<!-- ====== FOOTER ====== -->
<footer id="mainFooter">
    <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($libraryName) ?> — Bestlink College of the Philippines. All rights reserved.</p>
</footer>

</div><!-- /.app-container -->

<!-- ====== DETAIL MODAL ====== -->
<div class="modal" id="detailModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Details</h3>
            <button class="modal-close" onclick="app.ui.closeModal('detailModal')"><i class="fas fa-times"></i></button>
        </div>
        <div id="modalContent"></div>
    </div>
</div>

<!-- ====== RETURN MODAL ====== -->
<div class="modal" id="returnModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Return Book</h3>
            <button class="modal-close" onclick="app.ui.closeModal('returnModal')"><i class="fas fa-times"></i></button>
        </div>
        <div id="returnModalContent"></div>
    </div>
</div>

<!-- ====== SCRIPTS ====== -->
<script src="assets/js/ApiService.js"></script>
<script src="assets/js/UIService.js"></script>
<script src="assets/js/DashboardController.js"></script>
<script src="assets/js/BooksController.js"></script>
<script src="assets/js/GalleryController.js"></script>
<script src="assets/js/MembersController.js"></script>
<script src="assets/js/BorrowController.js"></script>
<script src="assets/js/controllers.js"></script>
<script src="assets/js/app.js"></script>
<script src="assets/js/AIController.js"></script>

<script>
// ── Page Loader ────────────────────────────────────────────
window.addEventListener('load', () => {
    setTimeout(() => {
        document.getElementById('pageLoader').classList.add('hidden');
    }, 800);
});

// ── Realtime Clock ─────────────────────────────────────────
(() => {
    const el = document.getElementById('realtimeClock');
    if (!el) return;
    const update = () => el.textContent = new Date().toLocaleTimeString([], {
        hour: '2-digit', minute: '2-digit', second: '2-digit'
    });
    update();
    setInterval(update, 1000);
})();

// ── Hamburger ──────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    const hamburger   = document.getElementById('hamburger');
    const sidebar     = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const mainFooter  = document.getElementById('mainFooter');
    const mainHeader  = document.getElementById('mainHeader');
    const overlay     = document.getElementById('sidebarOverlay');
    const SW          = 252;

    function isMobile() { return window.innerWidth <= 768; }

    hamburger.addEventListener('click', function () {
        if (isMobile()) {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        } else {
            sidebar.classList.toggle('hidden');
            const hidden = sidebar.classList.contains('hidden');
            mainContent.style.marginLeft = hidden ? '0' : SW + 'px';
            mainFooter.style.marginLeft  = hidden ? '0' : SW + 'px';
            mainHeader.style.left        = hidden ? '0' : SW + 'px';
            mainHeader.style.width       = hidden ? '100%' : `calc(100% - ${SW}px)`;
        }
    });

    overlay.addEventListener('click', () => {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
    });

    window.addEventListener('resize', () => {
        if (isMobile()) {
            mainContent.style.marginLeft = '';
            mainFooter.style.marginLeft  = '';
            mainHeader.style.left        = '';
            mainHeader.style.width       = '';
            sidebar.classList.remove('hidden');
        }
    });
});

// ── Dropdowns ──────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    const bellBtn      = document.getElementById('bellBtn');
    const bellDropdown = document.getElementById('bellDropdown');
    const bellWrapper  = document.getElementById('bellWrapper');
    const userBtn      = document.getElementById('userBtn');
    const userDropdown = document.getElementById('userDropdown');
    const userWrapper  = document.getElementById('userWrapper');

    function closeAll() {
        bellDropdown.classList.remove('open');
        userDropdown.classList.remove('open');
    }

    function openDropdown(dropdown, triggerEl) {
        const sidebar     = document.querySelector('.sidebar');
        const rect        = triggerEl.getBoundingClientRect();
        const sidebarRect = sidebar.getBoundingClientRect();
        dropdown.style.top  = rect.top + 'px';
        dropdown.style.left = (sidebarRect.right + 8) + 'px';
        dropdown.classList.add('open');
    }

    bellBtn.addEventListener('click', e => {
        e.stopPropagation();
        const open = bellDropdown.classList.contains('open');
        closeAll();
        if (!open) openDropdown(bellDropdown, bellBtn);
    });

    userBtn.addEventListener('click', e => {
        e.stopPropagation();
        const open = userDropdown.classList.contains('open');
        closeAll();
        if (!open) openDropdown(userDropdown, userBtn);
    });

    document.addEventListener('click', e => {
        if (!bellWrapper.contains(e.target) && !userWrapper.contains(e.target)) closeAll();
    });

    const markAllRead = document.querySelector('.mark-all-read');
    if (markAllRead) {
        markAllRead.addEventListener('click', () => {
            document.querySelectorAll('.notif-item.unread').forEach(i => i.classList.remove('unread'));
        });
    }
});

// ── AI Tab Init ────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    setTimeout(function () {
        if (typeof AI !== 'undefined') AI.init();
    }, 1500);

    // Override switchTab to init AI when switching to AI tab
    const checkApp = setInterval(() => {
        if (window.app && window.app.ui && window.app.ui.switchTab) {
            clearInterval(checkApp);
            const _orig = app.ui.switchTab.bind(app.ui);
            app.ui.switchTab = function (tab) {
                _orig(tab);
                if (tab === 'ai' && typeof AI !== 'undefined') AI.init();
            };
        }
    }, 100);
});
</script>

</body>
</html>

<!-- Sidebar -->
<aside class="admin-sidebar">
    <div class="sidebar-header">
        <img src="../assets/images/ns.jpg" alt="NSCT Logo" class="logo">
        <h2>NSCT Admin</h2>
    </div>
    <nav class="sidebar-nav">
        <ul class="nav-menu">
            <li class="<?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                <a href="../dashboard.php">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="<?= $currentPage === 'members' ? 'active' : '' ?>">
                <a href="../modules/members/list.php">
                    <i class="fas fa-users"></i>
                    <span>Members</span>
                </a>
            </li>
            <li class="<?= $currentPage === 'sahyog' ? 'active' : '' ?>">
                <a href="../modules/sahyog/list.php">
                    <i class="fas fa-hands-helping"></i>
                    <span>Sahyog</span>
                </a>
            </li>
            <li class="<?= $currentPage === 'payments' ? 'active' : '' ?>">
                <a href="../modules/payments/list.php">
                    <i class="fas fa-rupee-sign"></i>
                    <span>Payments</span>
                </a>
            </li>
            <li class="<?= $currentPage === 'reports' ? 'active' : '' ?>">
                <a href="../modules/reports/index.php">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reports</span>
                </a>
            </li>
            <li class="<?= $currentPage === 'settings' ? 'active' : '' ?>">
                <a href="../modules/settings/general.php">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </li>
            <li>
                <a href="../logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>

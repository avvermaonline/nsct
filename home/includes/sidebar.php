<nav class="admin-sidebar">
    <ul class="nav-menu">
        <li class="<?= $currentPage === 'dashboard' ? 'active' : '' ?>">
            <a href="/admin/dashboard.php">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="<?= $currentPage === 'members' ? 'active' : '' ?>">
            <a href="/admin/modules/members/list.php">
                <i class="fas fa-users"></i>
                <span>Members</span>
            </a>
        </li>
        <li class="<?= $currentPage === 'payments' ? 'active' : '' ?>">
            <a href="/admin/modules/payments/list.php">
                <i class="fas fa-credit-card"></i>
                <span>Payments</span>
            </a>
        </li>
        <li class="<?= $currentPage === 'reports' ? 'active' : '' ?>">
            <a href="/admin/modules/reports/index.php">
                <i class="fas fa-chart-bar"></i>
                <span>Reports</span>
            </a>
        </li>
        <li class="<?= $currentPage === 'settings' ? 'active' : '' ?>">
            <a href="/admin/modules/settings/general.php">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
        </li>
    </ul>
</nav>

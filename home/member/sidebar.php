<?php
// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get current page name
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">
    <div class="sidebar-header">
        <h3>Member Menu</h3>
    </div>
    <ul class="sidebar-menu">
        <li class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>">
            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        </li>
        <li class="<?= $current_page == 'profile.php' ? 'active' : '' ?>">
            <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
        </li>
        <li class="<?= $current_page == 'id_card.php' ? 'active' : '' ?>">
            <a href="id_card.php"><i class="fas fa-id-card"></i> ID Card</a>
        </li>
        <li class="<?= $current_page == 'upload_receipt.php' ? 'active' : '' ?>">
            <a href="upload_receipt.php"><i class="fas fa-file-upload"></i> Upload Receipt</a>
        </li>
        <li class="<?= $current_page == 'running_sahyog.php' ? 'active' : '' ?>">
            <a href="running_sahyog.php"><i class="fas fa-hands-helping"></i> Running Sahyog List</a>
        </li>
        <li class="<?= $current_page == 'all_sahyog.php' ? 'active' : '' ?>">
            <a href="all_sahyog.php"><i class="fas fa-list"></i> View All Sahyog List</a>
        </li>
        <li class="<?= $current_page == 'upload_vywastha.php' ? 'active' : '' ?>">
            <a href="upload_vywastha.php"><i class="fas fa-rupee-sign"></i> Upload Vywastha Shulk</a>
        </li>
        <li class="<?= $current_page == 'all_vywastha.php' ? 'active' : '' ?>">
            <a href="all_vywastha.php"><i class="fas fa-history"></i> View All Vywastha Shulk</a>
        </li>
        <li class="<?= $current_page == 'change_password.php' ? 'active' : '' ?>">
            <a href="change_password.php"><i class="fas fa-key"></i> Update Password</a>
        </li>
        <li class="<?= $current_page == 'self_declaration.php' ? 'active' : '' ?>">
            <a href="self_declaration.php"><i class="fas fa-file-signature"></i> Self Declaration</a>
        </li>
        <li>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </li>
    </ul>
</div>

<style>
.sidebar {
    background: #f8f9fa;
    border-right: 1px solid #e0e0e0;
    width: 250px;
    height: 100%;
    position: fixed;
    left: 0;
    top: 60px;
    overflow-y: auto;
    z-index: 100;
    box-shadow: 2px 0 5px rgba(0,0,0,0.05);
}

.sidebar-header {
    padding: 15px;
    background: #003366;
    color: #fff;
}

.sidebar-header h3 {
    margin: 0;
    font-size: 1.2rem;
}

.sidebar-menu {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sidebar-menu li {
    border-bottom: 1px solid #e0e0e0;
}

.sidebar-menu li a {
    display: block;
    padding: 12px 15px;
    color: #333;
    text-decoration: none;
    transition: all 0.3s;
}

.sidebar-menu li a i {
    margin-right: 10px;
    width: 20px;
    text-align: center;
}

.sidebar-menu li a:hover {
    background: #e9ecef;
    color: #ed620c;
}

.sidebar-menu li.active a {
    background: #e9ecef;
    color: #ed620c;
    font-weight: bold;
    border-left: 4px solid #ed620c;
}

@media (max-width: 768px) {
    .sidebar {
        width: 100%;
        position: relative;
        height: auto;
        top: 0;
    }
    
    .content-wrapper {
        margin-left: 0 !important;
    }
}
</style>
<?php
session_start();
require_once "../includes/config.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Get all sahyog campaigns
$stmt = $pdo->prepare("SELECT * FROM sahyog ORDER BY created_at DESC");
$stmt->execute();
$sahyog_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get user's contributions
$stmt = $pdo->prepare("SELECT sc.*, s.title FROM sahyog_contributions sc 
                      JOIN sahyog s ON sc.sahyog_id = s.id 
                      WHERE sc.member_id = ? 
                      ORDER BY sc.contribution_date DESC");
$stmt->execute([$user_id]);
$contributions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Sahyog List - NSCT</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background: #f4f6f9;
            font-family: 'Roboto', Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background: #003366;
            color: #fff;
            padding: 0.7rem 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        .navbar .logo {
            display: flex;
            align-items: center;
            margin-left: 2rem;
        }
        .navbar h1 {
            margin: 0;
            font-size: 1.7rem;
            letter-spacing: 1px;
        }
        .navbar nav {
            margin-right: 2rem;
        }
        .navbar nav a {
            color: #fff;
            text-decoration: none;
            margin-left: 1.2rem;
            font-weight: 500;
            transition: color 0.2s;
        }
        .navbar nav a:hover {
            color: #ffd700;
        }
        .content-wrapper {
            margin-left: 250px;
            padding: 20px;
        }
        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .card-title {
            color: #003366;
            font-size: 1.5rem;
            margin-top: 0;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .tab-container {
            margin-bottom: 20px;
        }
        .tab-buttons {
            display: flex;
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
        }
        .tab-button {
            padding: 10px 20px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            color: #666;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
        }
        .tab-button.active {
            color: #ed620c;
            border-bottom-color: #ed620c;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .sahyog-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .sahyog-title {
            font-size: 1.2rem;
            color: #003366;
            margin-top: 0;
            margin-bottom: 10px;
        }
        .sahyog-details {
            margin-bottom: 15px;
        }
        .sahyog-details p {
            margin: 5px 0;
        }
        .sahyog-progress {
            height: 20px;
            background-color: #e9ecef;
            border-radius: 10px;
            margin-bottom: 10px;
            overflow: hidden;
        }
        .sahyog-progress-bar {
            height: 100%;
            border-radius: 10px;
            transition: width 0.3s;
        }
        .progress-active {
            background-color: #ed620c;
        }
        .progress-completed {
            background-color: #28a745;
        }
        .progress-cancelled {
            background-color: #dc3545;
        }
        .sahyog-stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 0.9rem;
            color: #666;
        }
        .sahyog-status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .status-active {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .table th, .table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .table th {
            background-color: #f8f9fa;
            color: #333;
            font-weight: 600;
        }
        .table tr:hover {
            background-color: #f5f5f5;
        }
        @media (max-width: 768px) {
            .content-wrapper {
                margin-left: 0;
            }
            .table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo">
            <h1>NSCT Dashboard</h1>
        </div>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="profile.php">Profile</a>
            <a href="logout.php">Logout</a>
        </nav>
    </div>
    
    <?php include 'sidebar.php'; ?>
    
    <div class="content-wrapper">
        <div class="card">
            <h2 class="card-title">All Sahyog List</h2>
            
            <div class="tab-container">
                <div class="tab-buttons">
                    <button class="tab-button active" data-tab="all-sahyog">All Sahyog Campaigns</button>
                    <button class="tab-button" data-tab="my-contributions">My Contributions</button>
                </div>
                
                <div id="all-sahyog" class="tab-content active">
                    <?php if (empty($sahyog_list)): ?>
                        <p>No Sahyog campaigns found.</p>
                    <?php else: ?>
                        <?php foreach ($sahyog_list as $sahyog): ?>
                            <?php 
                                $progress = ($sahyog['amount_needed'] > 0) ? 
                                    min(100, ($sahyog['amount_collected'] / $sahyog['amount_needed']) * 100) : 0;
                                
                                $statusClass = '';
                                $progressClass = '';
                                
                                if ($sahyog['status'] === 'active') {
                                    $statusClass = 'status-active';
                                    $progressClass = 'progress-active';
                                } elseif ($sahyog['status'] === 'completed') {
                                    $statusClass = 'status-completed';
                                    $progressClass = 'progress-completed';
                                } else {
                                    $statusClass = 'status-cancelled';
                                    $progressClass = 'progress-cancelled';
                                }
                            ?>
                            <div class="sahyog-card">
                                <span class="sahyog-status <?= $statusClass ?>"><?= ucfirst(htmlspecialchars($sahyog['status'])) ?></span>
                                <h3 class="sahyog-title"><?= htmlspecialchars($sahyog['title']) ?></h3>
                                
                                <div class="sahyog-details">
                                    <p><strong>Beneficiary:</strong> <?= htmlspecialchars($sahyog['beneficiary_name']) ?></p>
                                    <p><?= nl2br(htmlspecialchars($sahyog['description'])) ?></p>
                                    <p><strong>Created:</strong> <?= date('d M Y', strtotime($sahyog['created_at'])) ?></p>
                                </div>
                                
                                <div class="sahyog-progress">
                                    <div class="sahyog-progress-bar <?= $progressClass ?>" style="width: <?= $progress ?>%"></div>
                                </div>
                                
                                <div class="sahyog-stats">
                                    <span>Collected: ₹<?= number_format($sahyog['amount_collected'], 2) ?></span>
                                    <span>Target: ₹<?= number_format($sahyog['amount_needed'], 2) ?></span>
                                    <span><?= round($progress) ?>% Complete</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <div id="my-contributions" class="tab-content">
                    <?php if (empty($contributions)): ?>
                        <p>You haven't made any contributions yet.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Sahyog Campaign</th>
                                        <th>Amount</th>
                                        <th>Payment Method</th>
                                        <th>Transaction ID</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($contributions as $contribution): ?>
                                        <tr>
                                            <td><?= date('d M Y', strtotime($contribution['contribution_date'])) ?></td>
                                            <td><?= htmlspecialchars($contribution['title']) ?></td>
                                            <td>₹<?= number_format($contribution['amount'], 2) ?></td>
                                            <td><?= htmlspecialchars($contribution['payment_method']) ?></td>
                                            <td><?= htmlspecialchars($contribution['transaction_id'] ?: 'N/A') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Tab functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const tabId = this.getAttribute('data-tab');
                    
                    // Remove active class from all buttons and contents
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    tabContents.forEach(content => content.classList.remove('active'));
                    
                    // Add active class to current button and content
                    this.classList.add('active');
                    document.getElementById(tabId).classList.add('active');
                });
            });
        });
    </script>
</body>
</html>
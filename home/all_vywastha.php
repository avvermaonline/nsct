<?php
session_start();
require_once "../includes/config.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Get all vywastha shulk payments
$stmt = $pdo->prepare("SELECT * FROM vywastha_shulk WHERE member_id = ? ORDER BY payment_date DESC");
$stmt->execute([$user_id]);
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate statistics
$total_paid = 0;
$approved_count = 0;
$pending_count = 0;
$rejected_count = 0;

foreach ($payments as $payment) {
    if ($payment['status'] === 'approved') {
        $total_paid += $payment['amount'];
        $approved_count++;
    } elseif ($payment['status'] === 'pending') {
        $pending_count++;
    } else {
        $rejected_count++;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Vywastha Shulk - NSCT</title>
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
        .stats-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            flex: 1;
            min-width: 200px;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            border-left: 4px solid #003366;
        }
        .stat-card.total {
            border-left-color: #28a745;
        }
        .stat-card.approved {
            border-left-color: #17a2b8;
        }
        .stat-card.pending {
            border-left-color: #ffc107;
        }
        .stat-card.rejected {
            border-left-color: #dc3545;
        }
        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: #333;
            margin: 10px 0;
        }
        .stat-label {
            font-size: 0.9rem;
            color: #666;
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
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .badge-pending {
            background-color: #ffeeba;
            color: #856404;
        }
        .badge-approved {
            background-color: #c3e6cb;
            color: #155724;
        }
        .badge-rejected {
            background-color: #f5c6cb;
            color: #721c24;
        }
        .receipt-link {
            color: #007bff;
            text-decoration: none;
        }
        .receipt-link:hover {
            text-decoration: underline;
        }
        .filter-container {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .filter-container label {
            margin-right: 5px;
            font-weight: 500;
        }
        .filter-select {
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        .empty-state {
            text-align: center;
            padding: 30px;
            color: #666;
        }
        .empty-state i {
            font-size: 3rem;
            color: #ddd;
            margin-bottom: 15px;
        }
        @media (max-width: 768px) {
            .content-wrapper {
                margin-left: 0;
            }
            .stats-container {
                flex-direction: column;
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
            <h2 class="card-title">All Vywastha Shulk Payments</h2>
            
            <div class="stats-container">
                <div class="stat-card total">
                    <div class="stat-label">Total Paid (Approved)</div>
                    <div class="stat-value">₹<?= number_format($total_paid, 2) ?></div>
                </div>
                <div class="stat-card approved">
                    <div class="stat-label">Approved Payments</div>
                    <div class="stat-value"><?= $approved_count ?></div>
                </div>
                <div class="stat-card pending">
                    <div class="stat-label">Pending Payments</div>
                    <div class="stat-value"><?= $pending_count ?></div>
                </div>
                <div class="stat-card rejected">
                    <div class="stat-label">Rejected Payments</div>
                    <div class="stat-value"><?= $rejected_count ?></div>
                </div>
            </div>
            
            <div class="filter-container">
                <label for="status-filter">Filter by Status:</label>
                <select id="status-filter" class="filter-select">
                    <option value="all">All</option>
                    <option value="approved">Approved</option>
                    <option value="pending">Pending</option>
                    <option value="rejected">Rejected</option>
                </select>
                
                <label for="year-filter" style="margin-left: 20px;">Filter by Year:</label>
                <select id="year-filter" class="filter-select">
                    <option value="all">All Years</option>
                    <?php
                        $years = [];
                        foreach ($payments as $payment) {
                            $year = date('Y', strtotime($payment['payment_date']));
                            if (!in_array($year, $years)) {
                                $years[] = $year;
                                echo "<option value=\"$year\">$year</option>";
                            }
                        }
                    ?>
                </select>
            </div>
            
            <?php if (empty($payments)): ?>
                <div class="empty-state">
                    <i class="fas fa-file-invoice"></i>
                    <p>No Vywastha Shulk payment records found.</p>
                    <p>Start by uploading your first payment in the "Upload Vywastha Shulk" section.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table" id="payments-table">
                        <thead>
                            <tr>
                                <th>Payment Date</th>
                                <th>Amount</th>
                                <th>Period</th>
                                <th>Method</th>
                                <th>Transaction ID</th>
                                <th>Status</th>
                                <th>Receipt</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($payments as $payment): ?>
                                <tr data-status="<?= $payment['status'] ?>" data-year="<?= date('Y', strtotime($payment['payment_date'])) ?>">
                                    <td><?= date('d M Y', strtotime($payment['payment_date'])) ?></td>
                                    <td>₹<?= number_format($payment['amount'], 2) ?></td>
                                    <td><?= date('d M Y', strtotime($payment['period_start'])) ?> to <?= date('d M Y', strtotime($payment['period_end'])) ?></td>
                                    <td><?= htmlspecialchars($payment['payment_method']) ?></td>
                                    <td><?= htmlspecialchars($payment['transaction_id'] ?: 'N/A') ?></td>
                                    <td>
                                        <?php if ($payment['status'] === 'pending'): ?>
                                            <span class="badge badge-pending">Pending</span>
                                        <?php elseif ($payment['status'] === 'approved'): ?>
                                            <span class="badge badge-approved">Approved</span>
                                        <?php else: ?>
                                            <span class="badge badge-rejected">Rejected</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="../uploads/receipts/<?= htmlspecialchars($payment['receipt_image']) ?>" target="_blank" class="receipt-link">View Receipt</a>
                                    </td>
                                    <td><?= htmlspecialchars($payment['remarks'] ?: 'N/A') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusFilter = document.getElementById('status-filter');
            const yearFilter = document.getElementById('year-filter');
            const table = document.getElementById('payments-table');
            
            if (table) {
                const rows = table.querySelectorAll('tbody tr');
                
                function applyFilters() {
                    const selectedStatus = statusFilter.value;
                    const selectedYear = yearFilter.value;
                    
                    rows.forEach(row => {
                        const status = row.getAttribute('data-status');
                        const year = row.getAttribute('data-year');
                        
                        const statusMatch = selectedStatus === 'all' || status === selectedStatus;
                        const yearMatch = selectedYear === 'all' || year === selectedYear;
                        
                        if (statusMatch && yearMatch) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                }
                
                statusFilter.addEventListener('change', applyFilters);
                yearFilter.addEventListener('change', applyFilters);
            }
        });
    </script>
</body>
</html>
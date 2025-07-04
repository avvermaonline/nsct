<?php
session_start();
require_once "../includes/config.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = $_POST['amount'] ?? '';
    $payment_date = $_POST['payment_date'] ?? '';
    $payment_method = $_POST['payment_method'] ?? '';
    $transaction_id = $_POST['transaction_id'] ?? '';
    $period_start = $_POST['period_start'] ?? '';
    $period_end = $_POST['period_end'] ?? '';
    
    // Validate input
    if (empty($amount) || empty($payment_date) || empty($payment_method) || empty($period_start) || empty($period_end)) {
        $error = 'Please fill all required fields';
    } elseif (!is_numeric($amount) || $amount <= 0) {
        $error = 'Please enter a valid amount';
    } elseif (strtotime($period_end) < strtotime($period_start)) {
        $error = 'End period cannot be before start period';
    } elseif (!isset($_FILES['receipt_image']) || $_FILES['receipt_image']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Please upload a receipt image';
    } else {
        // Handle file upload
        $uploads_dir = "../uploads/receipts";
        if (!is_dir($uploads_dir)) {
            mkdir($uploads_dir, 0777, true);
        }
        
        $file_ext = strtolower(pathinfo($_FILES['receipt_image']['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'pdf'];
        
        if (!in_array($file_ext, $allowed_ext)) {
            $error = 'Only JPG, JPEG, PNG, and PDF files are allowed';
        } else {
            $receipt_name = 'vywastha_' . uniqid() . '.' . $file_ext;
            $receipt_path = $uploads_dir . '/' . $receipt_name;
            
            if (move_uploaded_file($_FILES['receipt_image']['tmp_name'], $receipt_path)) {
                // Save to database
                $stmt = $pdo->prepare("INSERT INTO vywastha_shulk (member_id, amount, payment_date, payment_method, transaction_id, receipt_image, period_start, period_end, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
                $result = $stmt->execute([
                    $user_id, 
                    $amount, 
                    $payment_date, 
                    $payment_method, 
                    $transaction_id, 
                    $receipt_name, 
                    $period_start, 
                    $period_end
                ]);
                
                if ($result) {
                    $message = 'Vywastha Shulk payment uploaded successfully. It will be reviewed by an administrator.';
                } else {
                    $error = 'Failed to save payment information';
                }
            } else {
                $error = 'Failed to upload receipt image';
            }
        }
    }
}

// Get recent vywastha shulk payments
$stmt = $pdo->prepare("SELECT * FROM vywastha_shulk WHERE member_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$user_id]);
$recent_payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Vywastha Shulk - NSCT</title>
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
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #333;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        .btn {
            display: inline-block;
            background: #003366;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            border: none;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #00254d;
        }
        .btn-primary {
            background: #ed620c;
        }
        .btn-primary:hover {
            background: #c85000;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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
        .info-box {
            background-color: #e2f0fb;
            border: 1px solid #b8daff;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            color: #004085;
        }
        .info-box h3 {
            margin-top: 0;
            font-size: 1.1rem;
        }
        .info-box p {
            margin-bottom: 0;
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
            <h2 class="card-title">Upload Vywastha Shulk</h2>
            
            <div class="info-box">
                <h3>What is Vywastha Shulk?</h3>
                <p>Vywastha Shulk is the management fee that helps NSCT maintain its operations and provide services to members. Regular payment of Vywastha Shulk ensures the smooth functioning of the organization.</p>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="amount">Amount (₹) *</label>
                    <input type="number" id="amount" name="amount" class="form-control" min="1" step="0.01" required>
                </div>
                
                <div class="form-group">
                    <label for="payment_date">Payment Date *</label>
                    <input type="date" id="payment_date" name="payment_date" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="payment_method">Payment Method *</label>
                    <select id="payment_method" name="payment_method" class="form-control" required>
                        <option value="">Select Payment Method</option>
                        <option value="UPI">UPI</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="Credit/Debit Card">Credit/Debit Card</option>
                        <option value="Cash">Cash</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="transaction_id">Transaction ID</label>
                    <input type="text" id="transaction_id" name="transaction_id" class="form-control">
                    <small>Leave blank if paying by cash</small>
                </div>
                
                <div class="form-group">
                    <label for="period_start">Period Start Date *</label>
                    <input type="date" id="period_start" name="period_start" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="period_end">Period End Date *</label>
                    <input type="date" id="period_end" name="period_end" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="receipt_image">Receipt Image *</label>
                    <input type="file" id="receipt_image" name="receipt_image" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                    <small>Upload a clear image of your payment receipt (JPG, PNG, or PDF)</small>
                </div>
                
                <button type="submit" class="btn btn-primary">Upload Payment</button>
            </form>
        </div>
        
        <div class="card">
            <h2 class="card-title">Recent Vywastha Shulk Payments</h2>
            
            <?php if (empty($recent_payments)): ?>
                <p>No payment records found.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Payment Date</th>
                                <th>Amount</th>
                                <th>Period</th>
                                <th>Method</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_payments as $payment): ?>
                                <tr>
                                    <td><?= date('d M Y', strtotime($payment['payment_date'])) ?></td>
                                    <td>₹<?= number_format($payment['amount'], 2) ?></td>
                                    <td><?= date('d M Y', strtotime($payment['period_start'])) ?> to <?= date('d M Y', strtotime($payment['period_end'])) ?></td>
                                    <td><?= htmlspecialchars($payment['payment_method']) ?></td>
                                    <td>
                                        <?php if ($payment['status'] === 'pending'): ?>
                                            <span class="badge badge-pending">Pending</span>
                                        <?php elseif ($payment['status'] === 'approved'): ?>
                                            <span class="badge badge-approved">Approved</span>
                                        <?php else: ?>
                                            <span class="badge badge-rejected">Rejected</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        // Set default date values
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('payment_date').value = today;
            document.getElementById('payment_date').max = today;
        });
    </script>
</body>
</html>
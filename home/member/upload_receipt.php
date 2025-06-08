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
    $payment_type = $_POST['payment_type'] ?? '';
    $amount = $_POST['amount'] ?? '';
    $payment_method = $_POST['payment_method'] ?? '';
    $transaction_id = $_POST['transaction_id'] ?? '';
    
    // Validate input
    if (empty($payment_type) || empty($amount) || empty($payment_method)) {
        $error = 'Please fill all required fields';
    } elseif (!is_numeric($amount) || $amount <= 0) {
        $error = 'Please enter a valid amount';
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
            $receipt_name = 'receipt_' . uniqid() . '.' . $file_ext;
            $receipt_path = $uploads_dir . '/' . $receipt_name;
            
            if (move_uploaded_file($_FILES['receipt_image']['tmp_name'], $receipt_path)) {
                // Save to database
                $stmt = $pdo->prepare("INSERT INTO payments (member_id, amount, payment_type, payment_method, transaction_id, receipt_image, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
                $result = $stmt->execute([$user_id, $amount, $payment_type, $payment_method, $transaction_id, $receipt_name]);
                
                if ($result) {
                    $message = 'Receipt uploaded successfully. It will be reviewed by an administrator.';
                } else {
                    $error = 'Failed to save payment information';
                }
            } else {
                $error = 'Failed to upload receipt image';
            }
        }
    }
}

// Get recent payments
$stmt = $pdo->prepare("SELECT * FROM payments WHERE member_id = ? ORDER BY payment_date DESC LIMIT 5");
$stmt->execute([$user_id]);
$recent_payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Receipt - NSCT</title>
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
        .badge-completed {
            background-color: #c3e6cb;
            color: #155724;
        }
        .badge-failed {
            background-color: #f5c6cb;
            color: #721c24;
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
            <h2 class="card-title">Upload Payment Receipt</h2>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="payment_type">Payment Type *</label>
                    <select id="payment_type" name="payment_type" class="form-control" required>
                        <option value="">Select Payment Type</option>
                        <option value="registration">Registration Fee</option>
                        <option value="donation">Donation</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="amount">Amount (₹) *</label>
                    <input type="number" id="amount" name="amount" class="form-control" min="1" step="0.01" required>
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
                    <label for="receipt_image">Receipt Image *</label>
                    <input type="file" id="receipt_image" name="receipt_image" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                    <small>Upload a clear image of your payment receipt (JPG, PNG, or PDF)</small>
                </div>
                
                <button type="submit" class="btn btn-primary">Upload Receipt</button>
            </form>
        </div>
        
        <div class="card">
            <h2 class="card-title">Recent Payments</h2>
            
            <?php if (empty($recent_payments)): ?>
                <p>No payment records found.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_payments as $payment): ?>
                                <tr>
                                    <td><?= date('d M Y', strtotime($payment['payment_date'])) ?></td>
                                    <td><?= ucfirst(htmlspecialchars($payment['payment_type'])) ?></td>
                                    <td>₹<?= number_format($payment['amount'], 2) ?></td>
                                    <td><?= htmlspecialchars($payment['payment_method']) ?></td>
                                    <td>
                                        <?php if ($payment['status'] === 'pending'): ?>
                                            <span class="badge badge-pending">Pending</span>
                                        <?php elseif ($payment['status'] === 'completed'): ?>
                                            <span class="badge badge-completed">Completed</span>
                                        <?php else: ?>
                                            <span class="badge badge-failed">Failed</span>
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
</body>
</html>
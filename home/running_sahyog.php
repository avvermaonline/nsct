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

// Get active sahyog campaigns
$stmt = $pdo->prepare("SELECT * FROM sahyog WHERE status = 'active' ORDER BY created_at DESC");
$stmt->execute();
$sahyog_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle contribution form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contribute'])) {
    $sahyog_id = $_POST['sahyog_id'] ?? '';
    $amount = $_POST['amount'] ?? '';
    $payment_method = $_POST['payment_method'] ?? '';
    $transaction_id = $_POST['transaction_id'] ?? '';
    
    // Validate input
    if (empty($sahyog_id) || empty($amount) || empty($payment_method)) {
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
            $receipt_name = 'sahyog_receipt_' . uniqid() . '.' . $file_ext;
            $receipt_path = $uploads_dir . '/' . $receipt_name;
            
            if (move_uploaded_file($_FILES['receipt_image']['tmp_name'], $receipt_path)) {
                // Save to database
                $stmt = $pdo->prepare("INSERT INTO sahyog_contributions (sahyog_id, member_id, amount, payment_method, transaction_id, receipt_image) VALUES (?, ?, ?, ?, ?, ?)");
                $result = $stmt->execute([$sahyog_id, $user_id, $amount, $payment_method, $transaction_id, $receipt_name]);
                
                if ($result) {
                    // Update sahyog amount_collected
                    $updateStmt = $pdo->prepare("UPDATE sahyog SET amount_collected = amount_collected + ? WHERE id = ?");
                    $updateStmt->execute([$amount, $sahyog_id]);
                    
                    $message = 'Thank you for your contribution! Your receipt has been uploaded successfully.';
                } else {
                    $error = 'Failed to save contribution information';
                }
            } else {
                $error = 'Failed to upload receipt image';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Running Sahyog List - NSCT</title>
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
            background-color: #ed620c;
            border-radius: 10px;
            transition: width 0.3s;
        }
        .sahyog-stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 0.9rem;
            color: #666;
        }
        .sahyog-actions {
            text-align: right;
        }
        .btn {
            display: inline-block;
            background: #003366;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            border: none;
            cursor: pointer;
            transition: background 0.3s;
            font-size: 0.9rem;
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
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 8px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover {
            color: black;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        .form-control {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
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
        @media (max-width: 768px) {
            .content-wrapper {
                margin-left: 0;
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
            <h2 class="card-title">Running Sahyog List</h2>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if (empty($sahyog_list)): ?>
                <p>No active Sahyog campaigns at the moment.</p>
            <?php else: ?>
                <?php foreach ($sahyog_list as $sahyog): ?>
                    <?php 
                        $progress = ($sahyog['amount_needed'] > 0) ? 
                            min(100, ($sahyog['amount_collected'] / $sahyog['amount_needed']) * 100) : 0;
                    ?>
                    <div class="sahyog-card">
                        <h3 class="sahyog-title"><?= htmlspecialchars($sahyog['title']) ?></h3>
                        
                        <div class="sahyog-details">
                            <p><strong>Beneficiary:</strong> <?= htmlspecialchars($sahyog['beneficiary_name']) ?></p>
                            <p><?= nl2br(htmlspecialchars($sahyog['description'])) ?></p>
                        </div>
                        
                        <div class="sahyog-progress">
                            <div class="sahyog-progress-bar" style="width: <?= $progress ?>%"></div>
                        </div>
                        
                        <div class="sahyog-stats">
                            <span>Collected: ₹<?= number_format($sahyog['amount_collected'], 2) ?></span>
                            <span>Target: ₹<?= number_format($sahyog['amount_needed'], 2) ?></span>
                            <span><?= round($progress) ?>% Complete</span>
                        </div>
                        
                        <div class="sahyog-actions">
                            <button class="btn btn-primary contribute-btn" data-sahyog-id="<?= $sahyog['id'] ?>" data-sahyog-title="<?= htmlspecialchars($sahyog['title']) ?>">Contribute</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Contribution Modal -->
    <div id="contributionModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Contribute to <span id="sahyogTitle"></span></h2>
            
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" id="sahyog_id" name="sahyog_id">
                
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
                
                <button type="submit" name="contribute" class="btn btn-primary">Submit Contribution</button>
            </form>
        </div>
    </div>
    
    <script>
        // Get the modal
        var modal = document.getElementById("contributionModal");
        
        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];
        
        // Get all contribute buttons
        var btns = document.getElementsByClassName("contribute-btn");
        
        // Add click event to all contribute buttons
        for (var i = 0; i < btns.length; i++) {
            btns[i].onclick = function() {
                var sahyogId = this.getAttribute("data-sahyog-id");
                var sahyogTitle = this.getAttribute("data-sahyog-title");
                
                document.getElementById("sahyog_id").value = sahyogId;
                document.getElementById("sahyogTitle").textContent = sahyogTitle;
                
                modal.style.display = "block";
            }
        }
        
        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }
        
        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>
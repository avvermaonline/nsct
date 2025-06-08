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

// Get user data
$stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if user already has an active declaration
$stmt = $pdo->prepare("SELECT * FROM self_declarations WHERE member_id = ? AND status = 'active' ORDER BY declaration_date DESC LIMIT 1");
$stmt->execute([$user_id]);
$active_declaration = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_declaration'])) {
    $declaration_text = $_POST['declaration_text'] ?? '';
    
    if (empty($declaration_text)) {
        $error = 'Please provide the declaration text';
    } else {
        // Get client IP address
        $ip_address = $_SERVER['REMOTE_ADDR'];
        
        // Insert into database
        $stmt = $pdo->prepare("INSERT INTO self_declarations (member_id, declaration_text, declaration_date, ip_address, status) VALUES (?, ?, CURDATE(), ?, 'active')");
        $result = $stmt->execute([$user_id, $declaration_text, $ip_address]);
        
        if ($result) {
            $message = 'Self declaration submitted successfully';
            
            // Refresh the active declaration
            $stmt = $pdo->prepare("SELECT * FROM self_declarations WHERE member_id = ? AND status = 'active' ORDER BY declaration_date DESC LIMIT 1");
            $stmt->execute([$user_id]);
            $active_declaration = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $error = 'Failed to submit declaration';
        }
    }
}

// Get declaration history
$stmt = $pdo->prepare("SELECT * FROM self_declarations WHERE member_id = ? ORDER BY declaration_date DESC");
$stmt->execute([$user_id]);
$declaration_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Self Declaration - NSCT</title>
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
        textarea.form-control {
            min-height: 150px;
            resize: vertical;
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
        .declaration-box {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .declaration-text {
            font-style: italic;
            margin-bottom: 10px;
            line-height: 1.6;
        }
        .declaration-meta {
            font-size: 0.9rem;
            color: #666;
            text-align: right;
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
        .badge-active {
            background-color: #c3e6cb;
            color: #155724;
        }
        .badge-revoked {
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
            <h2 class="card-title">Self Declaration</h2>
            
            <div class="info-box">
                <h3>What is Self Declaration?</h3>
                <p>A self declaration is your formal commitment to abide by the rules and regulations of NSCT. By submitting this declaration, you confirm your understanding of the organization's principles and your willingness to participate in its activities.</p>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if ($active_declaration): ?>
                <h3>Your Current Active Declaration</h3>
                <div class="declaration-box">
                    <div class="declaration-text">
                        <?= nl2br(htmlspecialchars($active_declaration['declaration_text'])) ?>
                    </div>
                    <div class="declaration-meta">
                        Submitted on: <?= date('d M Y', strtotime($active_declaration['declaration_date'])) ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="declaration_text">Declaration Text *</label>
                    <textarea id="declaration_text" name="declaration_text" class="form-control" required><?= $active_declaration ? '' : "I, " . htmlspecialchars($user['name']) . ", hereby declare that I will abide by all the rules and regulations of NSCT (नन्दवंशी सेल्फ केयर टीम). I understand that my membership is subject to regular participation and contribution to the organization's activities. I confirm that all information provided by me is true and accurate to the best of my knowledge." ?></textarea>
                </div>
                
                <button type="submit" name="submit_declaration" class="btn btn-primary">Submit Declaration</button>
            </form>
        </div>
        
        <?php if (!empty($declaration_history)): ?>
        <div class="card">
            <h2 class="card-title">Declaration History</h2>
            
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Declaration</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($declaration_history as $declaration): ?>
                            <tr>
                                <td><?= date('d M Y', strtotime($declaration['declaration_date'])) ?></td>
                                <td><?= nl2br(htmlspecialchars(substr($declaration['declaration_text'], 0, 100) . (strlen($declaration['declaration_text']) > 100 ? '...' : ''))) ?></td>
                                <td>
                                    <?php if ($declaration['status'] === 'active'): ?>
                                        <span class="badge badge-active">Active</span>
                                    <?php else: ?>
                                        <span class="badge badge-revoked">Revoked</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
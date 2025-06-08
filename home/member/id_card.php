<?php
session_start();
require_once "../includes/config_nosession.php";

// Create database connection
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get user data
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);


// Check if user has an ID card
$stmt = $pdo->prepare("SELECT * FROM id_cards WHERE member_id = ? AND status = 'active'");
$stmt->execute([$user_id]);
$id_card = $stmt->fetch(PDO::FETCH_ASSOC);

$has_id_card = !empty($id_card);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ID Card - NSCT</title>
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
        .id-card-container {
            max-width: 500px;
            margin: 0 auto;
        }
        .id-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .id-card-header {
            background: #003366;
            color: white;
            padding: 15px;
            text-align: center;
        }
        .id-card-header h2 {
            margin: 0;
            font-size: 1.5rem;
        }
        .id-card-body {
            padding: 20px;
            background: #fff;
        }
        .id-card-photo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #ed620c;
            margin: 0 auto 15px auto;
            display: block;
        }
        .id-card-details {
            text-align: center;
        }
        .id-card-name {
            font-size: 1.3rem;
            font-weight: bold;
            color: #003366;
            margin-bottom: 5px;
        }
        .id-card-number {
            font-size: 1.1rem;
            color: #ed620c;
            margin-bottom: 15px;
        }
        .id-card-info {
            margin-top: 15px;
            border-top: 1px dashed #ddd;
            padding-top: 15px;
        }
        .id-card-info p {
            margin: 5px 0;
            display: flex;
            justify-content: space-between;
        }
        .id-card-info strong {
            color: #003366;
        }
        .id-card-footer {
            background: #f8f9fa;
            padding: 15px;
            text-align: center;
            border-top: 1px solid #ddd;
        }
        .id-card-validity {
            font-size: 0.9rem;
            color: #666;
        }
        .id-card-signature {
            margin-top: 15px;
            text-align: right;
        }
        .id-card-signature img {
            max-height: 40px;
            margin-bottom: 5px;
        }
        .id-card-signature p {
            margin: 0;
            font-size: 0.8rem;
            color: #666;
        }
        .id-card-actions {
            margin-top: 20px;
            text-align: center;
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
        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        @media print {
            .navbar, .sidebar, .id-card-actions, .no-print {
                display: none;
            }
            .content-wrapper {
                margin-left: 0;
                padding: 0;
            }
            .card {
                box-shadow: none;
                border: none;
            }
            .id-card {
                border: 1px solid #000;
            }
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
            <h2 class="card-title">Member ID Card</h2>
            
            <?php if (!$has_id_card): ?>
                <div class="alert alert-info">
                    <p>Your ID card has not been generated yet. Please complete your profile and make the registration payment. An administrator will review and generate your ID card.</p>
                </div>
            <?php else: ?>
                <div class="id-card-container">
                    <div class="id-card">
                        <div class="id-card-header">
                            <h2>NSCT - नन्दवंशी सेल्फ केयर टीम</h2>
                        </div>
                        <div class="id-card-body">
                            <img src="<?= !empty($user['photo']) ? '../uploads/' . htmlspecialchars($user['photo']) : '../assets/default-user.png' ?>" alt="Member Photo" class="id-card-photo">
                            
                            <div class="id-card-details">
                                <div class="id-card-name"><?= htmlspecialchars($user['name']) ?></div>
                                <div class="id-card-number">ID: <?= htmlspecialchars($id_card['card_number']) ?></div>
                                
                                <div class="id-card-info">
                                    <p>
                                        <strong>Mobile:</strong>
                                        <span><?= htmlspecialchars($user['mobile']) ?></span>
                                    </p>
                                    <p>
                                        <strong>State:</strong>
                                        <span><?= htmlspecialchars($user['state'] ?? 'N/A') ?></span>
                                    </p>
                                    <p>
                                        <strong>District:</strong>
                                        <span><?= htmlspecialchars($user['district'] ?? 'N/A') ?></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="id-card-footer">
                            <div class="id-card-validity">
                                Valid From: <?= date('d M Y', strtotime($id_card['issue_date'])) ?> to <?= date('d M Y', strtotime($id_card['expiry_date'])) ?>
                            </div>
                            <div class="id-card-signature">
                                <img src="../assets/signature.png" alt="Authorized Signature">
                                <p>Authorized Signature</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="id-card-actions">
                        <button onclick="window.print()" class="btn">Print ID Card</button>
                        <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
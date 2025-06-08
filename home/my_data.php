<?php
session_start();
require_once "../includes/config_nosession.php";

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Get user data
$stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Example: Show all user data in a table
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My NSCT Data</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body { background: #f4f6f9; font-family: 'Roboto', Arial, sans-serif; margin: 0; padding: 0; }
        .navbar { background: #003366; color: #fff; padding: 0.7rem 0; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; }
        .navbar .logo { display: flex; align-items: center; margin-left: 2rem; }
        .navbar h1 { margin: 0; font-size: 1.7rem; letter-spacing: 1px; }
        .navbar nav { margin-right: 2rem; }
        .navbar nav a { color: #fff; text-decoration: none; margin-left: 1.2rem; font-weight: 500; transition: color 0.2s; }
        .navbar nav a:hover { color: #ffd700; }
        .content-wrapper { max-width: 800px; margin: 40px auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 30px; }
        h2 { color: #003366; margin-top: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f8f9fa; color: #333; font-weight: 600; }
        tr:last-child td { border-bottom: none; }
        .footer { background: #003366; color: #fff; text-align: center; padding: 1.2rem 0 0.7rem 0; margin-top: 2rem; }
        .footer .contact { margin-bottom: 0.5rem; }
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
    <div class="content-wrapper">
        <h2>My NSCT Data</h2>
        <table>
            <tr><th>Field</th><th>Value</th></tr>
            <?php foreach ($user as $key => $value): ?>
                <tr>
                    <td><?= htmlspecialchars(ucwords(str_replace('_', ' ', $key))) ?></td>
                    <td><?= htmlspecialchars($value) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <div class="footer">
        <div class="contact">
            <strong>Contact:</strong> info@nsct.com | +91-12345-67890
        </div>
        &copy; <?= date('Y'); ?> NSCT. All rights reserved.
    </div>
</body>
</html>

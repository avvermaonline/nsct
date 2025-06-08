<?php
session_start();
require_once "../includes/config_nosession.php";

// Create a direct database connection
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

// Ensure user is logged in
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

if (!$user) {
    // User not found in database
    session_destroy();
    header('Location: login.php');
    exit;
}

// Check if profile needs completion
$showProfileModal = empty($user['aadhar_number']) || empty($user['father_name']) || 
                   empty($user['nominee_name']) || empty($user['family_members']);

// Handle profile update from modal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complete_profile'])) {
    $aadhar_number = trim($_POST['aadhar_number'] ?? '');
    $father_name = trim($_POST['father_name'] ?? '');
    $nominee_name = trim($_POST['nominee_name'] ?? '');
    $family_members = trim($_POST['family_members'] ?? '');
    $medical_condition = trim($_POST['medical_condition'] ?? '');
    
    // Validate input
    if (empty($aadhar_number) || empty($father_name) || empty($nominee_name) || empty($family_members)) {
        $error = 'All fields are required except medical condition';
    } elseif (!preg_match('/^[0-9]{12}$/', $aadhar_number)) {
        $error = 'Aadhar number must be 12 digits';
    } else {
        // Update user data
        $stmt = $pdo->prepare("UPDATE members SET aadhar_number = ?, father_name = ?, nominee_name = ?, family_members = ?, medical_condition = ? WHERE id = ?");
        $result = $stmt->execute([$aadhar_number, $father_name, $nominee_name, $family_members, $medical_condition, $user_id]);
        
        if ($result) {
            $message = 'Profile updated successfully';
            $showProfileModal = false;
            
            // Refresh user data
            $stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $error = 'Failed to update profile';
        }
    }
}

// Get recent sahyog contributions
try {
    // Check if contributions table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'contributions'");
    $tableExists = $stmt->rowCount() > 0;
    
    if ($tableExists) {
        $stmt = $pdo->prepare("
            SELECT s.title, c.amount, c.created_at 
            FROM contributions c
            JOIN sahyog s ON c.sahyog_id = s.id
            WHERE c.member_id = ?
            ORDER BY c.created_at DESC LIMIT 5
        ");
        $stmt->execute([$user_id]);
        $recentContributions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Table doesn't exist, set empty array
        $recentContributions = [];
    }
} catch (PDOException $e) {
    // Handle error gracefully
    $recentContributions = [];
}

// Helper functions
function getMembershipStatus($user) {
    return $user['status'] == 'active' ? 'Active' : 'Pending';
}

function getNextPaymentDate() {
    // Placeholder - implement your logic here
    return date('Y-m-d', strtotime('+30 days'));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - NSCT</title>
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
        .dashboard-container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .welcome-section {
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }
        .welcome-section h2 {
            color: #003366;
            margin-top: 0;
        }
        .quick-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-box {
            flex: 1;
            min-width: 200px;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            border-left: 4px solid #003366;
        }
        .stat-box h3 {
            margin-top: 0;
            color: #666;
            font-size: 1rem;
        }
        .stat-box p {
            margin-bottom: 0;
            font-size: 1.5rem;
            font-weight: 700;
            color: #003366;
        }
        .recent-activities, .payment-section {
            margin-bottom: 30px;
        }
        .recent-activities h3, .payment-section h3 {
            color: #003366;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .payment-table {
            width: 100%;
            border-collapse: collapse;
        }
        .payment-table th, .payment-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .payment-table th {
            background-color: #f8f9fa;
            color: #333;
            font-weight: 600;
        }
        /* Modal styles */
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
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .modal-header {
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
            margin-bottom: 20px;
        }
        .modal-header h2 {
            margin: 0;
            color: #003366;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            margin-top: -10px;
        }
        .close:hover {
            color: #000;
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
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .btn-submit {
            background: #ed620c;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-submit:hover {
            background: #c85000;
        }
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
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
            .quick-stats {
                flex-direction: column;
            }
            .modal-content {
                width: 95%;
                margin: 10% auto;
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
        <div class="dashboard-container">
            <div class="welcome-section">
                <h2>स्वागत है, <?php echo htmlspecialchars($user['name']); ?></h2>
                <p>सदस्य ID: <?php echo htmlspecialchars($user['id']); ?></p>
            </div>

            <div class="quick-stats">
                <div class="stat-box">
                    <h3>स्थिति</h3>
                    <p><?php echo getMembershipStatus($user); ?></p>
                </div>
                <div class="stat-box">
                    <h3>अगला भुगतान</h3>
                    <p><?php echo getNextPaymentDate(); ?></p>
                </div>
            </div>

            <div class="recent-activities">
                <h3>हाल की गतिविधियां</h3>
                <p>No recent activities found.</p>
            </div>

            <div class="payment-section">
                <h3>भुगतान इतिहास</h3>
                <?php if (empty($recentPayments)): ?>
                    <p>No payment history found.</p>
                <?php else: ?>
                    <table class="payment-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Type</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentPayments as $payment): ?>
                                <tr>
                                    <td><?= date('d M Y', strtotime($payment['payment_date'])) ?></td>
                                    <td>₹<?= number_format($payment['amount'], 2) ?></td>
                                    <td><?= ucfirst($payment['payment_type']) ?></td>
                                    <td><?= ucfirst($payment['status']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Profile Completion Modal -->
    <div id="profileModal" class="modal" style="<?= $showProfileModal ? 'display:block;' : 'display:none;' ?>">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close">&times;</span>
                <h2>Complete Your Profile</h2>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            
            <p>Please complete your profile by providing the following information:</p>
            
            <form method="POST">
                <div class="form-group">
                    <label for="aadhar_number">Aadhar Number *</label>
                    <input type="text" id="aadhar_number" name="aadhar_number" class="form-control" value="<?= htmlspecialchars($user['aadhar_number'] ?? '') ?>" pattern="[0-9]{12}" maxlength="12" required>
                </div>
                
                <div class="form-group">
                    <label for="father_name">Father's Name *</label>
                    <input type="text" id="father_name" name="father_name" class="form-control" value="<?= htmlspecialchars($user['father_name'] ?? '') ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="nominee_name">Nominee Name *</label>
                    <input type="text" id="nominee_name" name="nominee_name" class="form-control" value="<?= htmlspecialchars($user['nominee_name'] ?? '') ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="family_members">Total Family Members *</label>
                    <input type="number" id="family_members" name="family_members" class="form-control" value="<?= htmlspecialchars($user['family_members'] ?? '') ?>" min="1" required>
                </div>
                
                <div class="form-group">
                    <label for="medical_condition">Any Medical Condition</label>
                    <textarea id="medical_condition" name="medical_condition" class="form-control" rows="3"><?= htmlspecialchars($user['medical_condition'] ?? '') ?></textarea>
                    <small>Leave blank if none</small>
                </div>
                
                <button type="submit" name="complete_profile" class="btn-submit">Save Information</button>
            </form>
        </div>
    </div>
    
    <script>
        // Get the modal
        var modal = document.getElementById("profileModal");
        
        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];
        
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

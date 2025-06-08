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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $aadhar_number = trim($_POST['aadhar_number'] ?? '');
    $father_name = trim($_POST['father_name'] ?? '');
    $nominee_name = trim($_POST['nominee_name'] ?? '');
    $family_members = trim($_POST['family_members'] ?? '');
    $medical_condition = trim($_POST['medical_condition'] ?? '');
    
    // Validate input
    if (empty($name) || empty($email) || empty($aadhar_number) || empty($father_name) || empty($nominee_name) || empty($family_members)) {
        $error = 'All required fields must be filled';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } elseif (!preg_match('/^[0-9]{12}$/', $aadhar_number)) {
        $error = 'Aadhar number must be 12 digits';
    } else {
        // Handle photo upload if provided
        $photo_name = $user['photo'];
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png'];
            if (in_array($ext, $allowed)) {
                $photo_name = uniqid("photo_") . "." . $ext;
                $upload_dir = "../uploads/";
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                move_uploaded_file($_FILES['photo']['tmp_name'], $upload_dir . $photo_name);
            } else {
                $error = 'Only JPG, JPEG and PNG files are allowed for photo';
            }
        }
        
        if (empty($error)) {
            // Update user data
            $stmt = $pdo->prepare("UPDATE members SET name = ?, email = ?, address = ?, photo = ?, 
                                  aadhar_number = ?, father_name = ?, nominee_name = ?, 
                                  family_members = ?, medical_condition = ? WHERE id = ?");
            $result = $stmt->execute([
                $name, $email, $address, $photo_name, 
                $aadhar_number, $father_name, $nominee_name, 
                $family_members, $medical_condition, $user_id
            ]);
            
            if ($result) {
                $message = 'Profile updated successfully';
                // Refresh user data
                $stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $error = 'Failed to update profile';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile - NSCT</title>
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
        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .profile-photo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #ed620c;
            margin-right: 20px;
        }
        .profile-name {
            flex: 1;
        }
        .profile-name h2 {
            margin: 0 0 5px 0;
            color: #003366;
        }
        .profile-name p {
            margin: 0;
            color: #666;
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
            box-sizing: border-box;
        }
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 0;
        }
        .form-row .form-group {
            flex: 1;
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
        .section-title {
            color: #003366;
            font-size: 1.2rem;
            margin-top: 30px;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid #eee;
        }
        @media (max-width: 768px) {
            .content-wrapper {
                margin-left: 0;
            }
            .profile-header {
                flex-direction: column;
                text-align: center;
            }
            .profile-photo {
                margin-right: 0;
                margin-bottom: 15px;
            }
            .form-row {
                flex-direction: column;
                gap: 0;
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
            <h2 class="card-title">My Profile</h2>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <div class="profile-header">
                <img src="<?= !empty($user['photo']) ? '../uploads/' . htmlspecialchars($user['photo']) : '../assets/default-user.png' ?>" alt="Profile Photo" class="profile-photo">
                <div class="profile-name">
                    <h2><?= htmlspecialchars($user['name']) ?></h2>
                    <p>Member ID: <?= htmlspecialchars($user['id']) ?></p>
                    <p>Status: <?= htmlspecialchars(ucfirst($user['status'])) ?></p>
                </div>
            </div>
            
            <form method="POST" enctype="multipart/form-data">
                <h3 class="section-title">Basic Information</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Full Name *</label>
                        <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="mobile">Mobile Number</label>
                        <input type="text" id="mobile" class="form-control" value="<?= htmlspecialchars($user['mobile']) ?>" readonly>
                        <small>Mobile number cannot be changed</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="dob">Date of Birth</label>
                        <input type="date" id="dob" name="dob" class="form-control" value="<?= htmlspecialchars($user['dob'] ?? '') ?>" readonly>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <input type="text" id="gender" class="form-control" value="<?= htmlspecialchars($user['gender'] ?? '') ?>" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label for="pan">PAN Number</label>
                        <input type="text" id="pan" class="form-control" value="<?= htmlspecialchars($user['pan'] ?? '') ?>" readonly>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" class="form-control" rows="3"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                </div>
                
                <h3 class="section-title">Additional Information</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="aadhar_number">Aadhar Number *</label>
                        <input type="text" id="aadhar_number" name="aadhar_number" class="form-control" value="<?= htmlspecialchars($user['aadhar_number'] ?? '') ?>" pattern="[0-9]{12}" maxlength="12" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="father_name">Father's Name *</label>
                        <input type="text" id="father_name" name="father_name" class="form-control" value="<?= htmlspecialchars($user['father_name'] ?? '') ?>" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="nominee_name">Nominee Name *</label>
                        <input type="text" id="nominee_name" name="nominee_name" class="form-control" value="<?= htmlspecialchars($user['nominee_name'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="family_members">Total Family Members *</label>
                        <input type="number" id="family_members" name="family_members" class="form-control" value="<?= htmlspecialchars($user['family_members'] ?? '') ?>" min="1" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="medical_condition">Any Medical Condition</label>
                    <textarea id="medical_condition" name="medical_condition" class="form-control" rows="3"><?= htmlspecialchars($user['medical_condition'] ?? '') ?></textarea>
                    <small>Leave blank if none</small>
                </div>
                
                <h3 class="section-title">Profile Photo</h3>
                <div class="form-group">
                    <label for="photo">Update Profile Photo</label>
                    <input type="file" id="photo" name="photo" class="form-control" accept=".jpg,.jpeg,.png">
                    <small>Leave empty to keep current photo</small>
                </div>
                
                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
    </div>
</body>
</html>

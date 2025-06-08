<?php
// Include configuration without starting session
require_once "../includes/config_nosession.php";

// Now start the session
session_start();

$error = '';
$success = '';

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mobile = $_POST['mobile'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($mobile) || empty($password)) {
        $error = 'Please enter both mobile number and password';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM members WHERE mobile = ?");
            $stmt->execute([$mobile]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_mobile'] = $user['mobile'];
                
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Invalid mobile number or password';
            }
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            $error = 'An error occurred during login';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSCT - Login</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background: url('../assets/images/ns.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Roboto', Arial, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar {
            background: #003366cc;
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
        .login-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 30px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            border-top: 6px solid #ed620c;
        }
        .login-title {
            text-align: center;
            color: #003366;
            font-size: 1.8rem;
            margin-bottom: 25px;
            font-weight: 700;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #003366;
            font-weight: 500;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border 0.3s;
            box-sizing: border-box;
        }
        .form-group input:focus {
            border-color: #003366;
            outline: none;
        }
        .error {
            color: #dc3545;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        .success {
            color: #28a745;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        .submit-btn {
            width: 100%;
            background-color: #ed620c;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: bold;
            transition: background 0.3s;
            margin-top: 10px;
        }
        .submit-btn:hover {
            background-color: #c85000;
        }
        .links-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            font-size: 0.95rem;
        }
        .forgot-link, .register-link {
            color: #003366;
            text-decoration: none;
            transition: color 0.2s;
        }
        .forgot-link:hover, .register-link:hover {
            color: #ed620c;
            text-decoration: underline;
        }
        .footer {
            background: #003366cc;
            color: #fff;
            text-align: center;
            padding: 1rem 0;
            margin-top: auto;
        }
        .footer .contact {
            margin-bottom: 0.5rem;
        }
        @media (max-width: 768px) {
            .login-container {
                margin: 30px 20px;
                padding: 20px;
            }
            .navbar {
                flex-direction: column;
                padding: 0.5rem 0;
            }
            .navbar .logo {
                margin: 0 0 10px 0;
                justify-content: center;
            }
            .navbar nav {
                margin: 10px 0;
                text-align: center;
                width: 100%;
            }
            .navbar nav a {
                margin: 0 10px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo">
            <h1>NSCT - नन्दवंशी सेल्फ केयर टीम</h1>
        </div>
        <nav>
            <a href="index.php">Home</a>
            <a href="about.php">About</a>
            <a href="SadasyaSuchi.php">Sadasya Suchi</a>
            <a href="SahyogList.php">Sahyog Suchi</a>
            <a href="VywasthaSuchi.php">Vywastha Suchi</a>
            <a href="Niyamawali.php">Niyamawali</a>
            <a href="contact.php">Contact</a>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        </nav>
    </div>

    <div class="login-container">
        <h2 class="login-title">Member Login</h2>
        
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="mobile"><i class="fas fa-mobile-alt"></i> Mobile Number:</label>
                <input type="text" id="mobile" name="mobile" pattern="[0-9]{10}" maxlength="10" required placeholder="Enter 10 digit mobile number" value="<?= htmlspecialchars($_POST['mobile'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Password:</label>
                <input type="password" id="password" name="password" required placeholder="Enter your password">
            </div>

            <button type="submit" class="submit-btn">Login</button>
            
            <div class="links-container">
                <a href="forgot_password.php" class="forgot-link">Forgot Password?</a>
                <a href="register.php" class="register-link">New User? Register</a>
            </div>
        </form>
    </div>
    
    <div class="footer">
        <div class="contact">
            <strong>Contact:</strong> info@nsct.com | +91-12345-67890
        </div>
        &copy; <?php echo date('Y'); ?> NSCT. All rights reserved.
    </div>
</body>
</html>

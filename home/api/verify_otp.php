<?php
// api/verify_otp.php
header('Content-Type: application/json');
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
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

// Get phone and OTP from request
$data = json_decode(file_get_contents('php://input'), true);
$phone = isset($data['phone']) ? filter_var($data['phone'], FILTER_SANITIZE_STRING) : '';
$otp = isset($data['otp']) ? filter_var($data['otp'], FILTER_SANITIZE_STRING) : '';

if (empty($phone) || empty($otp)) {
    echo json_encode(['status' => 'error', 'message' => 'Phone number and OTP are required']);
    exit;
}

// Verify OTP
try {
    $stmt = $pdo->prepare("SELECT * FROM otp_verification WHERE phone = ? AND otp = ? AND expires_at > NOW()");
    $stmt->execute([$phone, $otp]);
    $result = $stmt->fetch();
    
    if (!$result) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid or expired OTP']);
        exit;
    }
    
    // Mark OTP as verified
    $stmt = $pdo->prepare("UPDATE otp_verification SET verified = 1 WHERE phone = ?");
    $stmt->execute([$phone]);
    
    echo json_encode(['status' => 'success', 'message' => 'OTP verified successfully']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Verification failed: ' . $e->getMessage()]);
}
?>

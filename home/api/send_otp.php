<?php
// api/send_otp.php
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

// Get phone number from request
$data = json_decode(file_get_contents('php://input'), true);
$phone = isset($data['phone']) ? filter_var($data['phone'], FILTER_SANITIZE_STRING) : '';

if (empty($phone)) {
    echo json_encode(['status' => 'error', 'message' => 'Phone number is required']);
    exit;
}

// Check if phone already exists
$stmt = $pdo->prepare("SELECT id FROM members WHERE phone = ?");
$stmt->execute([$phone]);
if ($stmt->rowCount() > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Phone number already registered']);
    exit;
}

// Generate OTP
$otp = rand(100000, 999999);

// Check if table exists, create if not
try {
    $pdo->query("SELECT 1 FROM otp_verification LIMIT 1");
} catch (PDOException $e) {
    // Table doesn't exist, create it
    $pdo->exec("CREATE TABLE `otp_verification` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `phone` varchar(15) NOT NULL,
        `otp` varchar(6) NOT NULL,
        `verified` tinyint(1) DEFAULT 0,
        `created_at` datetime NOT NULL,
        `expires_at` datetime NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `phone` (`phone`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

// Store OTP in database
try {
    $stmt = $pdo->prepare("INSERT INTO otp_verification (phone, otp, created_at, expires_at) 
                          VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 10 MINUTE))
                          ON DUPLICATE KEY UPDATE otp = ?, created_at = NOW(), expires_at = DATE_ADD(NOW(), INTERVAL 10 MINUTE)");
    $stmt->execute([$phone, $otp, $otp]);
    
    // In a real application, you would send the OTP via SMS using a service like Twilio
    // For now, we'll just return it in the response for testing
    echo json_encode([
        'status' => 'success', 
        'message' => 'OTP sent successfully',
        'otp' => $otp // Remove this in production
    ]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to store OTP: ' . $e->getMessage()]);
}
?>

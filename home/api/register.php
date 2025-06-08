<?php
// api/register.php
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

// Get form data
$phone = filter_input(INPUT_POST, 'verifiedPhone', FILTER_SANITIZE_STRING);
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$dob = filter_input(INPUT_POST, 'dob', FILTER_SANITIZE_STRING);
$state = filter_input(INPUT_POST, 'state', FILTER_SANITIZE_STRING);
$district = filter_input(INPUT_POST, 'district', FILTER_SANITIZE_STRING);
$address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
$aadhar = filter_input(INPUT_POST, 'aadhar', FILTER_SANITIZE_STRING);
$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

// Validate required fields
if (empty($phone) || empty($name) || empty($dob) || empty($state) || empty($district) || empty($address) || empty($aadhar) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'All required fields must be filled']);
    exit;
}

// Verify if OTP was verified for this phone
try {
    $stmt = $pdo->prepare("SELECT * FROM otp_verification WHERE phone = ? AND verified = 1");
    $stmt->execute([$phone]);
    if ($stmt->rowCount() === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Phone number not verified']);
        exit;
    }
    
    // Check if phone already exists
    $stmt = $pdo->prepare("SELECT id FROM members WHERE phone = ?");
    $stmt->execute([$phone]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Phone number already registered']);
        exit;
    }
    
    // Check if aadhar already exists
    $stmt = $pdo->prepare("SELECT id FROM members WHERE aadhar = ?");
    $stmt->execute([$aadhar]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Aadhar number already registered']);
        exit;
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Generate member ID
    $memberPrefix = 'NSCT';
    $stmt = $pdo->query("SELECT MAX(CAST(SUBSTRING(member_id, 5) AS UNSIGNED)) as max_id FROM members WHERE member_id LIKE 'NSCT%'");
    $result = $stmt->fetch();
    $nextId = ($result['max_id'] ?? 0) + 1;
    $memberId = $memberPrefix . str_pad($nextId, 6, '0', STR_PAD_LEFT);
    
    // Begin transaction
    $pdo->beginTransaction();
    
    // Insert member
    $stmt = $pdo->prepare("INSERT INTO members (member_id, name, email, phone, dob, state, district, address, aadhar, password, status, created_at) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', NOW())");
    $stmt->execute([$memberId, $name, $email, $phone, $dob, $state, $district, $address, $aadhar, $hashedPassword]);
    
    // Clean up OTP verification
    $stmt = $pdo->prepare("DELETE FROM otp_verification WHERE phone = ?");
    $stmt->execute([$phone]);
    
    // Commit transaction
    $pdo->commit();
    
    echo json_encode(['status' => 'success', 'message' => 'Registration successful', 'member_id' => $memberId]);
} catch (PDOException $e) {
    // Rollback transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['status' => 'error', 'message' => 'Registration failed: ' . $e->getMessage()]);
}
?>

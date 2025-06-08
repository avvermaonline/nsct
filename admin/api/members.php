<?php
// admin/api/get_member.php
header('Content-Type: application/json');
require_once "../../includes/config_nosession.php";
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

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

// Get member ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid member ID']);
    exit;
}

// Get member details
$stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
$stmt->execute([$id]);
$member = $stmt->fetch();

if (!$member) {
    echo json_encode(['status' => 'error', 'message' => 'Member not found']);
    exit;
}

// Get member contributions
$stmt = $pdo->prepare("
    SELECT s.title, c.amount, c.created_at as date
    FROM contributions c
    JOIN sahyog s ON c.sahyog_id = s.id
    WHERE c.member_id = ?
    ORDER BY c.created_at DESC
");
$stmt->execute([$id]);
$contributions = $stmt->fetchAll();

$member['contributions'] = $contributions;

echo json_encode(['status' => 'success', 'data' => $member]);
?>

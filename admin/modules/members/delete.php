<?php
define('ADMIN_ACCESS', true);
require_once '../../../includes/config.php';
require_once '../../includes/admin-auth.php';

$id = $_GET['id'] ?? 0;

try {
    // Check if member exists
    $stmt = $db->prepare("SELECT id FROM members WHERE id = ?");
    $stmt->execute([$id]);
    if (!$stmt->fetch()) {
        throw new Exception('Member not found');
    }

    // Delete member
    $stmt = $db->prepare("DELETE FROM members WHERE id = ?");
    $stmt->execute([$id]);

    $_SESSION['success_message'] = 'Member deleted successfully';
} catch (Exception $e) {
    $_SESSION['error_message'] = $e->getMessage();
}

header('Location: list.php');
exit;

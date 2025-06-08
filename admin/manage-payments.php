<?php
require_once "../includes/config.php";
require_once "../includes/admin-auth.php";

class PaymentManager {
    private $db;
    
    public function __construct() {
        global $pdo;
        $this->db = $pdo;
    }

    public function getPayments($page = 1, $limit = 20) {
        $offset = ($page - 1) * $limit;
        $stmt = $this->db->prepare("
            SELECT 
                p.*, 
                m.username as member_name
            FROM payments p
            JOIN members m ON p.member_id = m.id
            ORDER BY payment_date DESC 
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updatePaymentStatus($paymentId, $status) {
        $stmt = $this->db->prepare("
            UPDATE payments 
            SET status = :status 
            WHERE id = :id
        ");
        return $stmt->execute([
            ':status' => $status,
            ':id' => $paymentId
        ]);
    }
}

$paymentManager = new PaymentManager();
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$payments = $paymentManager->getPayments($page);
?>

<!-- HTML Template -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Payments</title>
</head>
<body>
    <div class="payment-management">
        <h1>Manage Payments</h1>
        
        <table class="payments-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Member</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Payment Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $payment): ?>
                <tr>
                    <td><?= htmlspecialchars($payment['id']) ?></td>
                    <td><?= htmlspecialchars($payment['member_name']) ?></td>
                    <td><?= htmlspecialchars($payment['amount']) ?></td>
                    <td><?= htmlspecialchars($payment['status']) ?></td>
                    <td><?= htmlspecialchars($payment['payment_date']) ?></td>
                    <td>
                        <button onclick="updatePaymentStatus(<?= $payment['id'] ?>)">Update Status</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

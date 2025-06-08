<?php
require_once "../includes/config.php";
require_once "../includes/admin-auth.php";

class ReportGenerator {
    private $db;
    
    public function __construct() {
        global $pdo;
        $this->db = $pdo;
    }

    public function generateMembershipReport($startDate, $endDate) {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_registrations,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_members,
                DATE_FORMAT(registration_date, '%Y-%m') as month
            FROM members
            WHERE registration_date BETWEEN :start_date AND :end_date
            GROUP BY DATE_FORMAT(registration_date, '%Y-%m')
            ORDER BY month
        ");
        $stmt->execute([
            ':start_date' => $startDate,
            ':end_date' => $endDate
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function generateRevenueReport($startDate, $endDate) {
        $stmt = $this->db->prepare("
            SELECT 
                SUM(amount) as total_revenue,
                COUNT(*) as transaction_count,
                DATE_FORMAT(payment_date, '%Y-%m') as month
            FROM payments
            WHERE payment_date BETWEEN :start_date AND :end_date
                AND status = 'completed'
            GROUP BY DATE_FORMAT(payment_date, '%Y-%m')
            ORDER BY month
        ");
        $stmt->execute([
            ':start_date' => $startDate,
            ':end_date' => $endDate
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

$reportGenerator = new ReportGenerator();
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-1 year'));
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

$membershipReport = $reportGenerator->generateMembershipReport($startDate, $endDate);
$revenueReport = $reportGenerator->generateRevenueReport($startDate, $endDate);
?>

<!-- HTML Template -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Reports</title>
</head>
<body>
    <div class="reports">
        <h1>Admin Reports</h1>
        
        <div class="report-filters">
            <form method="GET">
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($startDate) ?>">
                
                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($endDate) ?>">
                
                <button type="submit">Generate Reports</button>
            </form>
        </div>

        <div class="membership-report">
            <h2>Membership Report</h2>
            <table>
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Total Registrations</th>
                        <th>Active Members</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($membershipReport as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['month']) ?></td>
                        <td><?= htmlspecialchars($row['total_registrations']) ?></td>
                        <td><?= htmlspecialchars($row['active_members']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="revenue-report">
            <h2>Revenue Report</h2>
            <table>
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Total Revenue</th>
                        <th>Transaction Count</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($revenueReport as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['month']) ?></td>
                        <td><?= htmlspecialchars($row['total_revenue']) ?></td>
                        <td><?= htmlspecialchars($row['transaction_count']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

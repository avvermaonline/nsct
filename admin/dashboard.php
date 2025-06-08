<?php
define('ADMIN_ACCESS', true);
require_once '../includes/config.php';
require_once 'includes/admin-auth.php';

$pageTitle = 'Dashboard';
$currentPage = 'dashboard';

// Get dashboard stats
try {
    // Total members
    $stmt = $pdo->query("SELECT COUNT(*) FROM members");
    $totalMembers = $stmt->fetchColumn();
    
    // Active members
    $stmt = $pdo->query("SELECT COUNT(*) FROM members WHERE status = 'active'");
    $activeMembers = $stmt->fetchColumn();
    
    // Total sahyog campaigns
    $stmt = $pdo->query("SELECT COUNT(*) FROM sahyog");
    $totalSahyog = $stmt->fetchColumn() ?? 0;
    
    // Total amount collected
    $stmt = $pdo->query("SELECT SUM(amount_collected) FROM sahyog");
    $totalAmount = $stmt->fetchColumn() ?? 0;
    
    // Recent members
    $stmt = $pdo->query("SELECT * FROM members ORDER BY created_at DESC LIMIT 5");
    $recentMembers = $stmt->fetchAll();
    
    // Recent sahyog
    $stmt = $pdo->query("SELECT * FROM sahyog ORDER BY created_at DESC LIMIT 5");
    $recentSahyog = $stmt->fetchAll();
    
    // Monthly registrations for chart
    $stmt = $pdo->query("
        SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count 
        FROM members 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH) 
        GROUP BY DATE_FORMAT(created_at, '%Y-%m') 
        ORDER BY month ASC
    ");
    $registrationData = $stmt->fetchAll();
    
    $months = [];
    $counts = [];
    
    foreach ($registrationData as $data) {
        $months[] = date('M Y', strtotime($data['month'] . '-01'));
        $counts[] = $data['count'];
    }
    
    // Member distribution by state
    $stmt = $pdo->query("
        SELECT state, COUNT(*) as count 
        FROM members 
        GROUP BY state 
        ORDER BY count DESC 
        LIMIT 5
    ");
    $stateData = $stmt->fetchAll();
    
    $states = [];
    $stateCounts = [];
    
    foreach ($stateData as $data) {
        $states[] = $data['state'] ?: 'Unknown';
        $stateCounts[] = $data['count'];
    }
} catch (PDOException $e) {
    // Handle database errors
    $error = "Database error: " . $e->getMessage();
}

require_once 'includes/header.php';
require_once 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card h-100" style="border-left-color: #4e73df;">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs text-uppercase mb-1 text-primary fw-bold">Total Members</div>
                                <div class="h5 mb-0 fw-bold"><?= number_format($totalMembers) ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300 stat-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card h-100" style="border-left-color: #1cc88a;">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs text-uppercase mb-1 text-success fw-bold">Active Members</div>
                                <div class="h5 mb-0 fw-bold"><?= number_format($activeMembers) ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-check fa-2x text-gray-300 stat-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card h-100" style="border-left-color: #36b9cc;">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs text-uppercase mb-1 text-info fw-bold">Total Sahyog</div>
                                <div class="h5 mb-0 fw-bold"><?= number_format($totalSahyog) ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-hands-helping fa-2x text-gray-300 stat-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card h-100" style="border-left-color: #f6c23e;">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs text-uppercase mb-1 text-warning fw-bold">Total Amount</div>
                                <div class="h5 mb-0 fw-bold">₹<?= number_format($totalAmount, 2) ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-rupee-sign fa-2x text-gray-300 stat-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="m-0 fw-bold">Monthly Registrations</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="registrationsChart" height="300"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="m-0 fw-bold">Member Distribution</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="memberDistributionChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Data -->
        <div class="row">
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="m-0 fw-bold">Recent Members</h6>
                        <a href="modules/members/list.php" class="btn btn-sm btn-primary">View All</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>District</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentMembers as $member): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($member['member_id']) ?></td>
                                            <td><?= htmlspecialchars($member['name']) ?></td>
                                            <td><?= htmlspecialchars($member['phone']) ?></td>
                                            <td><?= htmlspecialchars($member['district']) ?></td>
                                            <td><?= date('d M Y', strtotime($member['created_at'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                          <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="m-0 fw-bold">Recent Sahyog</h6>
                        <a href="modules/sahyog/list.php" class="btn btn-sm btn-primary">View All</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Beneficiary</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentSahyog as $sahyog): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($sahyog['title']) ?></td>
                                            <td><?= htmlspecialchars($sahyog['beneficiary_name']) ?></td>
                                            <td>₹<?= number_format($sahyog['amount_collected'], 2) ?></td>
                                            <td>
                                                <?php if ($sahyog['status'] === 'active'): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php elseif ($sahyog['status'] === 'completed'): ?>
                                                    <span class="badge bg-primary">Completed</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$pageScripts = <<<EOT
<script>
    // Monthly Registrations Chart
    const registrationsCtx = document.getElementById('registrationsChart').getContext('2d');
    const registrationsChart = new Chart(registrationsCtx, {
        type: 'line',
        data: {
            labels: JSON.parse('${json_encode($months)}'),
            datasets: [{
                label: 'New Registrations',
                data: JSON.parse('${json_encode($counts)}'),
                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                borderColor: 'rgba(78, 115, 223, 1)',
                borderWidth: 2,
                pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                pointBorderColor: '#fff',
                pointRadius: 3,
                pointHoverRadius: 5,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
    
    // Member Distribution Chart
    const distributionCtx = document.getElementById('memberDistributionChart').getContext('2d');
    const distributionChart = new Chart(distributionCtx, {
        type: 'doughnut',
        data: {
            labels: JSON.parse('${json_encode($states)}'),
            datasets: [{
                data: JSON.parse('${json_encode($stateCounts)}'),
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'
                ],
                hoverBackgroundColor: [
                    '#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617'
                ],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            cutout: '60%'
        }
    });
</script>
EOT;

require_once 'includes/footer.php';
?>


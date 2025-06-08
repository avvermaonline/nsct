<?php
session_start();
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
    die("Connection failed: " . $e->getMessage());
}

// Get active sahyog campaigns
$stmt = $pdo->query("SELECT * FROM sahyog WHERE status = 'active' ORDER BY created_at DESC");
$sahyog_list = $stmt->fetchAll();

// Set current page for navbar highlighting
$current_page = 'sahyog';
$page_title = 'Sahyog List - NSCT';

// Include header
include 'header.php';
// Include navbar
include 'navbar.php';
?>

<!-- Page Header -->
<div class="bg-primary text-white py-4 mb-4">
    <div class="container">
        <h1 class="fw-bold">सहयोग सूची</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php" class="text-white-50">Home</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page">Sahyog Suchi</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-4">
    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-body p-4">
            <h2 class="text-primary border-bottom border-danger pb-2 mb-4">सहयोग सूची</h2>
            <p class="lead text-center mb-5">वर्तमान में चल रहे सहयोग अभियानों की सूची। आप भी इन अभियानों में अपना योगदान देकर मदद कर सकते हैं।</p>
            
            <?php if (empty($sahyog_list)): ?>
                <div class="alert alert-info text-center p-5">
                    <i class="fas fa-info-circle fa-3x mb-3"></i>
                    <h4>वर्तमान में कोई सक्रिय सहयोग अभियान नहीं है।</h4>
                    <p class="mb-0">कृपया बाद में पुनः जांचें या हमसे संपर्क करें।</p>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($sahyog_list as $sahyog): ?>
                        <?php 
                            $progress = ($sahyog['amount_needed'] > 0) ? 
                                min(100, ($sahyog['amount_collected'] / $sahyog['amount_needed']) * 100) : 0;
                        ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card h-100">
                                <img src="<?= !empty($sahyog['image']) ? '../uploads/sahyog/' . htmlspecialchars($sahyog['image']) : '../assets/default-sahyog.jpg' ?>" alt="<?= htmlspecialchars($sahyog['title']) ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                                <div class="card-body">
                                    <h5 class="card-title text-primary"><?= htmlspecialchars($sahyog['title']) ?></h5>
                                    <p class="card-text"><strong>लाभार्थी:</strong> <?= htmlspecialchars($sahyog['beneficiary_name']) ?></p>
                                    <p class="card-text"><?= nl2br(htmlspecialchars(substr($sahyog['description'], 0, 100))) ?>...</p>
                                    
                                    <div class="progress mb-3" style="height: 10px;">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: <?= $progress ?>%;" aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between mb-3">
                                        <span>एकत्रित: ₹<?= number_format($sahyog['amount_collected'], 0) ?></span>
                                        <span>लक्ष्य: ₹<?= number_format($sahyog['amount_needed'], 0) ?></span>
                                        <span class="badge bg-danger"><?= round($progress) ?>%</span>
                                    </div>
                                    
                                    <a href="sahyog-details.php?id=<?= $sahyog['id'] ?>" class="btn btn-primary w-100">विवरण देखें</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
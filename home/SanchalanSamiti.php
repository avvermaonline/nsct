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

// Use hardcoded team members since the table doesn't exist yet
$team_members = [
    [
        'name' => 'जितेंद्र कुमार शर्मा',
        'position' => 'अध्यक्ष',
        'category' => 'core',
        'district' => 'लखनऊ',
        'state' => 'उत्तर प्रदेश',
        'phone' => '9876543210',
        'email' => 'jitendra@nsct.com',
        'whatsapp' => '919876543210',
        'photo' => ''
    ],
    [
        'name' => 'प्रदीप कुमार वर्मा',
        'position' => 'सचिव',
        'category' => 'core',
        'district' => 'लखनऊ',
        'state' => 'उत्तर प्रदेश',
        'phone' => '9876543211',
        'email' => 'pradeep@nsct.com',
        'whatsapp' => '919876543211',
        'photo' => ''
    ],
    [
        'name' => 'अमित सिंह',
        'position' => 'कोषाध्यक्ष',
        'category' => 'core',
        'district' => 'लखनऊ',
        'state' => 'उत्तर प्रदेश',
        'phone' => '9876543212',
        'email' => 'amit@nsct.com',
        'whatsapp' => '919876543212',
        'photo' => ''
    ],
    [
        'name' => 'राजेश कुमार',
        'position' => 'क्षेत्रीय समन्वयक (पूर्वी उत्तर प्रदेश)',
        'category' => 'regional',
        'district' => 'वाराणसी',
        'state' => 'उत्तर प्रदेश',
        'phone' => '9876543213',
        'email' => 'rajesh@nsct.com',
        'whatsapp' => '919876543213',
        'photo' => ''
    ],
    [
        'name' => 'सुनील वर्मा',
        'position' => 'क्षेत्रीय समन्वयक (पश्चिमी उत्तर प्रदेश)',
        'category' => 'regional',
        'district' => 'मेरठ',
        'state' => 'उत्तर प्रदेश',
        'phone' => '9876543214',
        'email' => 'sunil@nsct.com',
        'whatsapp' => '919876543214',
        'photo' => ''
    ],
    [
        'name' => 'अनिल शर्मा',
        'position' => 'जिला समन्वयक',
        'category' => 'district',
        'district' => 'कानपुर',
        'state' => 'उत्तर प्रदेश',
        'phone' => '9876543215',
        'email' => 'anil@nsct.com',
        'whatsapp' => '919876543215',
        'photo' => ''
    ]
];

// Set current page for navbar highlighting
$current_page = 'sanchalan';
$page_title = 'Sanchalan Samiti - NSCT';

// Include header
include 'header.php';
// Include navbar
include 'navbar.php';
?>

<!-- Page Header -->
<div class="bg-primary text-white py-4 mb-4">
    <div class="container">
        <h1 class="fw-bold">संचालन समिति</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php" class="text-white-50">Home</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page">Sanchalan Samiti</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-4">
    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-body p-4">
            <h2 class="text-primary border-bottom border-danger pb-2 mb-4">संचालन समिति</h2>
            <p class="lead text-center mb-5">नन्दवंशी सेल्फ केयर टीम के प्रबंधन और संचालन समिति के सदस्य। ये लोग संगठन के सुचारू संचालन के लिए जिम्मेदार हैं।</p>
            
            <?php if (empty($team_members)): ?>
                <div class="alert alert-info text-center p-5">
                    <i class="fas fa-info-circle fa-3x mb-3"></i>
                    <h4>संचालन समिति के सदस्यों की जानकारी अभी उपलब्ध नहीं है।</h4>
                </div>
            <?php else: ?>
                <!-- Core Management Team -->
                <h3 class="h4 text-center position-relative mb-5">
                    <span class="bg-light px-4 position-relative" style="z-index: 1;">मुख्य प्रबंधन समिति</span>
                    <hr class="position-absolute top-50 start-0 end-0 border-danger" style="z-index: 0;">
                </h3>
                
                <div class="row mb-5">
                    <?php 
                    $core_members = array_filter($team_members, function($member) {
                        return $member['category'] === 'core';
                    });
                    
                    foreach ($core_members as $member): 
                    ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card h-100 text-center">
                                <div class="card-body p-4">
                                    <div class="member-img mb-3">
                                        <img src="<?= !empty($member['photo']) ? '../uploads/team/' . htmlspecialchars($member['photo']) : '../assets/default-user.png' ?>" alt="<?= htmlspecialchars($member['name']) ?>" class="img-fluid">
                                    </div>
                                    <h4 class="card-title text-primary"><?= htmlspecialchars($member['name']) ?></h4>
                                    <p class="card-text text-danger fw-medium"><?= htmlspecialchars($member['position']) ?></p>
                                    
                                    <?php if (!empty($member['district'])): ?>
                                        <p class="text-muted"><?= htmlspecialchars($member['district']) ?>, <?= htmlspecialchars($member['state']) ?></p>
                                    <?php endif; ?>
                                    
                                    <div class="mt-3">
                                        <?php if (!empty($member['phone'])): ?>
                                            <a href="tel:<?= htmlspecialchars($member['phone']) ?>" class="btn btn-sm btn-outline-primary me-2" title="Call"><i class="fas fa-phone-alt"></i></a>
                                        <?php endif; ?>
                                        <?php if (!empty($member['email'])): ?>
                                            <a href="mailto:<?= htmlspecialchars($member['email']) ?>" class="btn btn-sm btn-outline-primary me-2" title="Email"><i class="fas fa-envelope"></i></a>
                                        <?php endif; ?>
                                        <?php if (!empty($member['whatsapp'])): ?>
                                            <a href="https://wa.me/<?= htmlspecialchars($member['whatsapp']) ?>" class="btn btn-sm btn-outline-success" title="WhatsApp" target="_blank"><i class="fab fa-whatsapp"></i></a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Regional Coordinators -->
                <?php 
                $regional_members = array_filter($team_members, function($member) {
                    return $member['category'] === 'regional';
                });
                
                if (!empty($regional_members)): 
                ?>
                    <h3 class="h4 text-center position-relative mb-5">
                        <span class="bg-light px-4 position-relative" style="z-index: 1;">क्षेत्रीय समन्वयक</span>
                        <hr class="position-absolute top-50 start-0 end-0 border-danger" style="z-index: 0;">
                    </h3>
                    
                    <div class="row mb-5">
                        <?php foreach ($regional_members as $member): ?>
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card h-100 text-center">
                                    <div class="card-body p-4">
                                        <div class="member-img mb-3">
                                            <img src="<?= !empty($member['photo']) ? '../uploads/team/' . htmlspecialchars($member['photo']) : '../assets/default-user.png' ?>" alt="<?= htmlspecialchars($member['name']) ?>" class="img-fluid">
                                        </div>
                                        <h4 class="card-title text-primary"><?= htmlspecialchars($member['name']) ?></h4>
                                        <p class="card-text text-danger fw-medium"><?= htmlspecialchars($member['position']) ?></p>
                                        
                                        <?php if (!empty($member['district'])): ?>
                                            <p class="text-muted"><?= htmlspecialchars($member['district']) ?>, <?= htmlspecialchars($member['state']) ?></p>
                                        <?php endif; ?>
                                        
                                        <div class="mt-3">
                                            <?php if (!empty($member['phone'])): ?>
                                                <a href="tel:<?= htmlspecialchars($member['phone']) ?>" class="btn btn-sm btn-outline-primary me-2" title="Call"><i class="fas fa-phone-alt"></i></a>
                                            <?php endif; ?>
                                            <?php if (!empty($member['email'])): ?>
                                                <a href="mailto:<?= htmlspecialchars($member['email']) ?>" class="btn btn-sm btn-outline-primary me-2" title="Email"><i class="fas fa-envelope"></i></a>
                                            <?php endif; ?>
                                            <?php if (!empty($member['whatsapp'])): ?>
                                                <a href="https://wa.me/<?= htmlspecialchars($member['whatsapp']) ?>" class="btn btn-sm btn-outline-success" title="WhatsApp" target="_blank"><i class="fab fa-whatsapp"></i></a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <!-- District Coordinators -->
                <?php 
                $district_members = array_filter($team_members, function($member) {
                    return $member['category'] === 'district';
                });
                
                if (!empty($district_members)): 
                ?>
                    <h3 class="h4 text-center position-relative mb-5">
                        <span class="bg-light px-4 position-relative" style="z-index: 1;">जिला समन्वयक</span>
                        <hr class="position-absolute top-50 start-0 end-0 border-danger" style="z-index: 0;">
                    </h3>
                    
                    <div class="row">
                        <?php foreach ($district_members as $member): ?>
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card h-100 text-center">
                                    <div class="card-body p-4">
                                        <div class="member-img mb-3">
                                            <img src="<?= !empty($member['photo']) ? '../uploads/team/' . htmlspecialchars($member['photo']) : '../assets/default-user.png' ?>" alt="<?= htmlspecialchars($member['name']) ?>" class="img-fluid">
                                        </div>
                                        <h4 class="card-title text-primary"><?= htmlspecialchars($member['name']) ?></h4>
                                        <p class="card-text text-danger fw-medium"><?= htmlspecialchars($member['position']) ?></p>
                                        
                                        <?php if (!empty($member['district'])): ?>
                                            <p class="text-muted"><?= htmlspecialchars($member['district']) ?>, <?= htmlspecialchars($member['state']) ?></p>
                                        <?php endif; ?>
                                        
                                        <div class="mt-3">
                                            <?php if (!empty($member['phone'])): ?>
                                                <a href="tel:<?= htmlspecialchars($member['phone']) ?>" class="btn btn-sm btn-outline-primary me-2" title="Call"><i class="fas fa-phone-alt"></i></a>
                                            <?php endif; ?>
                                            <?php if (!empty($member['email'])): ?>
                                                <a href="mailto:<?= htmlspecialchars($member['email']) ?>" class="btn btn-sm btn-outline-primary me-2" title="Email"><i class="fas fa-envelope"></i></a>
                                            <?php endif; ?>
                                            <?php if (!empty($member['whatsapp'])): ?>
                                                <a href="https://wa.me/<?= htmlspecialchars($member['whatsapp']) ?>" class="btn btn-sm btn-outline-success" title="WhatsApp" target="_blank"><i class="fab fa-whatsapp"></i></a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .member-img {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        overflow: hidden;
        margin: 0 auto;
        border: 5px solid #e74c3c;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        transition: all 0.3s;
    }
    
    .member-img:hover {
        transform: scale(1.05);
        box-shadow: 0 15px 35px rgba(0,0,0,0.2);
    }
    
    .member-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: all 0.5s;
    }
    
    .member-img:hover img {
        transform: scale(1.1);
    }
</style>

<?php include 'footer.php'; ?>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
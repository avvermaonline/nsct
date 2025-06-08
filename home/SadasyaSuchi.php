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

// Get members list with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

$stmt = $pdo->prepare("SELECT id, name, state, district, status, created_at FROM members WHERE status = 'active' ORDER BY name ASC LIMIT :offset, :perPage");
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':perPage', $perPage, PDO::PARAM_INT);
$stmt->execute();
$members = $stmt->fetchAll();

// Get total count for pagination
$stmt = $pdo->query("SELECT COUNT(*) FROM members WHERE status = 'active'");
$totalMembers = $stmt->fetchColumn();
$totalPages = ceil($totalMembers / $perPage);

// Set current page for navbar highlighting
$current_page = 'sadasya';
$page_title = 'Sadasya Suchi - NSCT';

// Include header
include 'header.php';
// Include navbar
include 'navbar.php';
?>

<!-- Page Header -->
<div class="bg-primary text-white py-4 mb-4">
    <div class="container">
        <h1 class="fw-bold">सदस्य सूची</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php" class="text-white-50">Home</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page">Sadasya Suchi</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-4">
    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center mb-4">
                <div class="col-md-6">
                    <h2 class="text-primary border-bottom border-danger pb-2 mb-0">सदस्य सूची</h2>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" id="searchInput" class="form-control" placeholder="नाम या जिले से खोजें...">
                        <button class="btn btn-danger" type="button" onclick="searchMembers()">
                            <i class="fas fa-search"></i> खोजें
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>क्र.सं.</th>
                            <th>नाम</th>
                            <th>राज्य</th>
                            <th>जिला</th>
                            <th>सदस्यता तिथि</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($members)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4">कोई सदस्य नहीं मिला</td>
                            </tr>
                        <?php else: ?>
                            <?php $counter = $offset + 1; ?>
                            <?php foreach ($members as $member): ?>
                                <tr>
                                    <td><?= $counter++ ?></td>
                                    <td><?= htmlspecialchars($member['name']) ?></td>
                                    <td><?= htmlspecialchars($member['state'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($member['district'] ?? 'N/A') ?></td>
                                    <td><?= date('d M Y', strtotime($member['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if ($totalPages > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=1" aria-label="First">
                                    <span aria-hidden="true">&laquo;&laquo;</span>
                                </a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $page - 1 ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="page-item disabled">
                                <span class="page-link">&laquo;&laquo;</span>
                            </li>
                            <li class="page-item disabled">
                                <span class="page-link">&laquo;</span>
                            </li>
                        <?php endif; ?>
                        
                        <?php
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $page + 2);
                        
                        for ($i = $startPage; $i <= $endPage; $i++): ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $page + 1 ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $totalPages ?>" aria-label="Last">
                                    <span aria-hidden="true">&raquo;&raquo;</span>
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="page-item disabled">
                                <span class="page-link">&raquo;</span>
                            </li>
                            <li class="page-item disabled">
                                <span class="page-link">&raquo;&raquo;</span>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function searchMembers() {
        const input = document.getElementById('searchInput').value.toLowerCase();
        const table = document.querySelector('table');
        const rows = table.getElementsByTagName('tr');
        
        for (let i = 1; i < rows.length; i++) {
            const name = rows[i].getElementsByTagName('td')[1]?.textContent.toLowerCase() || '';
            const district = rows[i].getElementsByTagName('td')[3]?.textContent.toLowerCase() || '';
            
            if (name.includes(input) || district.includes(input)) {
                rows[i].style.display = '';
            } else {
                rows[i].style.display = 'none';
            }
        }
    }
    
    // Enable search on Enter key press
    document.getElementById('searchInput').addEventListener('keyup', function(event) {
        if (event.key === 'Enter') {
            searchMembers();
        }
    });
</script>
</body>
</html>
<?php
define('ADMIN_ACCESS', true);
require_once '../../../includes/config.php';
require_once '../../includes/admin-auth.php';

$pageTitle = 'Manage Members';
$currentPage = 'members';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Get total members
$totalMembers = $db->query("SELECT COUNT(*) FROM members")->fetchColumn();
$totalPages = ceil($totalMembers / $limit);

// Get members
$stmt = $db->prepare("
    SELECT * FROM members 
    ORDER BY created_at DESC 
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<main class="main-content">
    <div class="page-header">
        <h1>Manage Members</h1>
        <a href="add.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Member
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Joined Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($members as $member): ?>
                        <tr>
                            <td><?= htmlspecialchars($member['id']) ?></td>
                            <td><?= htmlspecialchars($member['username']) ?></td>
                            <td><?= htmlspecialchars($member['email']) ?></td>
                            <td>
                                <span class="badge badge-<?= $member['status'] === 'active' ? 'success' : 'danger' ?>">
                                    <?= htmlspecialchars($member['status']) ?>
                                </span>
                            </td>
                            <td><?= date('M j, Y', strtotime($member['created_at'])) ?></td>
                            <td>
                                <a href="edit.php?id=<?= $member['id'] ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="delete.php?id=<?= $member['id'] ?>" 
                                   class="btn btn-sm btn-danger"
                                   data-confirm="Are you sure you want to delete this member?">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?= $i ?>" 
                           class="page-link <?= $i === $page ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php require_once '../../includes/footer.php'; ?>

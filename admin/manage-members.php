<?php
// admin/modules/members/list.php
define('ADMIN_ACCESS', true);
require_once '../../../includes/config.php';
require_once '../../includes/admin-auth.php';

$pageTitle = 'Member Management';
$currentPage = 'members';

// Handle status change
if (isset($_GET['action']) && $_GET['action'] == 'status' && isset($_GET['id']) && isset($_GET['status'])) {
    $id = $_GET['id'];
    $status = $_GET['status'];
    
    if (in_array($status, ['active', 'inactive', 'pending'])) {
        $stmt = $pdo->prepare("UPDATE members SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
        
        $statusMessage = "Member status updated successfully";
    }
}

// Handle delete
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $stmt = $pdo->prepare("DELETE FROM members WHERE id = ?");
    $stmt->execute([$id]);
    
    $statusMessage = "Member deleted successfully";
}

// Get members with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

$search = isset($_GET['search']) ? $_GET['search'] : '';
$status = isset($_GET['filter_status']) ? $_GET['filter_status'] : '';

$params = [];
$whereClause = [];

if (!empty($search)) {
    $whereClause[] = "(name LIKE ? OR member_id LIKE ? OR phone LIKE ? OR email LIKE ? OR district LIKE ?)";
    $searchParam = "%$search%";
    $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam, $searchParam]);
}

if (!empty($status)) {
    $whereClause[] = "status = ?";
    $params[] = $status;
}

$where = !empty($whereClause) ? "WHERE " . implode(" AND ", $whereClause) : "";

$stmt = $pdo->prepare("SELECT COUNT(*) FROM members $where");
$stmt->execute($params);
$totalMembers = $stmt->fetchColumn();

$totalPages = ceil($totalMembers / $perPage);

$stmt = $pdo->prepare("SELECT * FROM members $where ORDER BY created_at DESC LIMIT ?, ?");
$params[] = $offset;
$params[] = $perPage;
$stmt->execute($params);
$members = $stmt->fetchAll();

require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <?php if (isset($statusMessage)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($statusMessage) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">Member Management</h5>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <a href="add.php" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> Add Member
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-8">
                        <form action="" method="GET" class="d-flex">
                            <input type="text" name="search" class="form-control me-2" placeholder="Search members..." value="<?= htmlspecialchars($search) ?>">
                            <select name="filter_status" class="form-select me-2" style="width: 150px;">
                                <option value="">All Status</option>
                                <option value="active" <?= $status == 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= $status == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                <option value="pending" <?= $status == 'pending' ? 'selected' : '' ?>>Pending</option>
                            </select>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <a href="export.php" class="btn btn-success">
                            <i class="fas fa-file-excel me-1"></i> Export
                        </a>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Member ID</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>District</th>
                                <th>Status</th>
                                <th>Registered On</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($members)): ?>
                                <tr>
                                    <td colspan="7" class="text-center">No members found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($members as $member): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($member['member_id']) ?></td>
                                        <td><?= htmlspecialchars($member['name']) ?></td>
                                        <td><?= htmlspecialchars($member['phone']) ?></td>
                                        <td><?= htmlspecialchars($member['district']) ?></td>
                                        <td>
                                            <?php if ($member['status'] === 'active'): ?>
                                                <span class="badge bg-success">Active</span>
                                            <?php elseif ($member['status'] === 'inactive'): ?>
                                                <span class="badge bg-danger">Inactive</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">Pending</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('d M Y', strtotime($member['created_at'])) ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="view.php?id=<?= $member['id'] ?>" class="btn btn-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="edit.php?id=<?= $member['id'] ?>" class="btn btn-primary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger" title="Delete" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $member['id'] ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <div class="dropdown d-inline-block">
                                                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                        <i class="fas fa-cog"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <?php if ($member['status'] !== 'active'): ?>
                                                            <li><a class="dropdown-item" href="?action=status&id=<?= $member['id'] ?>&status=active">Mark Active</a></li>
                                                        <?php endif; ?>
                                                        <?php if ($member['status'] !== 'inactive'): ?>
                                                            <li><a class="dropdown-item" href="?action=status&id=<?= $member['id'] ?>&status=inactive">Mark Inactive</a></li>
                                                        <?php endif; ?>
                                                        <?php if ($member['status'] !== 'pending'): ?>
                                                            <li><a class="dropdown-item" href="?action=status&id=<?= $member['id'] ?>&status=pending">Mark Pending</a></li>
                                                        <?php endif; ?>
                                                    </ul>
                                                </div>
                                            </div>
                                            
                                            <!-- Delete Modal -->
                                            <div class="modal fade" id="deleteModal<?= $member['id'] ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Confirm Delete</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Are you sure you want to delete member <strong><?= htmlspecialchars($member['name']) ?></strong>?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <a href="?action=delete&id=<?= $member['id'] ?>" class="btn btn-danger">Delete</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&filter_status=<?= urlencode($status) ?>">Previous</a>
                            </li>
                            
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&filter_status=<?= urlencode($status) ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&filter_status=<?= urlencode($status) ?>">Next</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>

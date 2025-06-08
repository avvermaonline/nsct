<?php
define('ADMIN_ACCESS', true);
require_once '../../../includes/config.php';
require_once '../../includes/admin-auth.php';

$pageTitle = 'Edit Member';
$currentPage = 'members';
$error = '';
$success = '';

$id = $_GET['id'] ?? 0;

// Get member data
$stmt = $db->prepare("SELECT * FROM members WHERE id = ?");
$stmt->execute([$id]);
$member = $stmt->fetch();

if (!$member) {
    header('Location: list.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $status = $_POST['status'] ?? 'pending';
    $password = $_POST['password'] ?? '';

    try {
        // Validate input
        if (empty($username) || empty($email)) {
            throw new Exception('Username and email are required');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format');
        }

        // Check if username or email already exists for other users
        $stmt = $db->prepare("
            SELECT COUNT(*) FROM members 
            WHERE (username = ? OR email = ?) 
            AND id != ?
        ");
        $stmt->execute([$username, $email, $id]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception('Username or email already exists');
        }

        // Update member
        if (!empty($password)) {
            $stmt = $db->prepare("
                UPDATE members 
                SET username = ?, email = ?, status = ?, password_hash = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $username,
                $email,
                $status,
                password_hash($password, PASSWORD_DEFAULT),
                $id
            ]);
        } else {
            $stmt = $db->prepare("
                UPDATE members 
                SET username = ?, email = ?, status = ?
                WHERE id = ?
            ");
            $stmt->execute([$username, $email, $status, $id]);
        }

        $success = 'Member updated successfully';
        
        // Refresh member data
        $stmt = $db->prepare("SELECT * FROM members WHERE id = ?");
        $stmt->execute([$id]);
        $member = $stmt->fetch();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<main class="main-content">
    <div class="page-header">
        <h1>Edit Member</h1>
        <a href="list.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="" class="form">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" 
                           class="form-control" required 
                           value="<?= htmlspecialchars($member['username']) ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" 
                           class="form-control" required 
                           value="<?= htmlspecialchars($member['email']) ?>">
                </div>

                <div class="form-group">
                    <label for="password">Password (leave blank to keep current)</label>
                    <input type="password" id="password" name="password" class="form-control">
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="active" <?= $member['status'] === 'active' ? 'selected' : '' ?>>
                            Active
                        </option>
                        <option value="pending" <?= $member['status'] === 'pending' ? 'selected' : '' ?>>
                            Pending
                        </option>
                        <option value="inactive" <?= $member['status'] === 'inactive' ? 'selected' : '' ?>>
                            Inactive
                        </option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Update Member</button>
            </form>
        </div>
    </div>
</main>

<?php require_once '../../includes/footer.php'; ?>

<?php
session_start();

class AdminAuth {
    private $db;
    private $maxLoginAttempts = 5;
    private $lockoutDuration = 1800; // 30 minutes in seconds

    public function __construct() {
        global $pdo;
        $this->db = $pdo;
    }

    public function login($username, $password) {
        try {
            // Check if the account is locked
            if ($this->isAccountLocked($username)) {
                return [
                    'success' => false,
                    'message' => 'Account is temporarily locked. Please try again later.'
                ];
            }

            $stmt = $this->db->prepare("
                SELECT id, username, password_hash, status, login_attempts, last_login_attempt 
                FROM admin_users 
                WHERE username = :username
            ");
            $stmt->execute([':username' => $username]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$admin) {
                $this->logFailedAttempt($username);
                return [
                    'success' => false,
                    'message' => 'Invalid credentials'
                ];
            }

            if ($admin['status'] !== 'active') {
                return [
                    'success' => false,
                    'message' => 'Account is not active'
                ];
            }

            if (password_verify($password, $admin['password_hash'])) {
                // Reset login attempts on successful login
                $this->resetLoginAttempts($username);

                // Set session
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_last_activity'] = time();

                // Update last login
                $this->updateLastLogin($admin['id']);

                return [
                    'success' => true,
                    'message' => 'Login successful'
                ];
            } else {
                $this->logFailedAttempt($username);
                return [
                    'success' => false,
                    'message' => 'Invalid credentials'
                ];
            }
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred during login'
            ];
        }
    }

    private function isAccountLocked($username) {
        $stmt = $this->db->prepare("
            SELECT login_attempts, last_login_attempt 
            FROM admin_users 
            WHERE username = :username
        ");
        $stmt->execute([':username' => $username]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            if ($result['login_attempts'] >= $this->maxLoginAttempts) {
                $lockoutTime = strtotime($result['last_login_attempt']) + $this->lockoutDuration;
                if (time() < $lockoutTime) {
                    return true;
                } else {
                    // Reset attempts if lockout period has expired
                    $this->resetLoginAttempts($username);
                }
            }
        }
        return false;
    }

    private function logFailedAttempt($username) {
        $stmt = $this->db->prepare("
            UPDATE admin_users 
            SET login_attempts = login_attempts + 1,
                last_login_attempt = CURRENT_TIMESTAMP
            WHERE username = :username
        ");
        $stmt->execute([':username' => $username]);
    }

    private function resetLoginAttempts($username) {
        $stmt = $this->db->prepare("
            UPDATE admin_users 
            SET login_attempts = 0,
                last_login_attempt = NULL
            WHERE username = :username
        ");
        $stmt->execute([':username' => $username]);
    }

    private function updateLastLogin($adminId) {
        $stmt = $this->db->prepare("
            UPDATE admin_users 
            SET last_login = CURRENT_TIMESTAMP
            WHERE id = :id
        ");
        $stmt->execute([':id' => $adminId]);
    }

    public function isLoggedIn() {
        if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_last_activity'])) {
            return false;
        }

        // Check for session timeout (30 minutes)
        if (time() - $_SESSION['admin_last_activity'] > 1800) {
            $this->logout();
            return false;
        }

        // Update last activity
        $_SESSION['admin_last_activity'] = time();
        return true;
    }

    public function logout() {
        session_unset();
        session_destroy();
        session_start();
    }

    public function requireAuth() {
        if (!$this->isLoggedIn()) {
            header('Location: /admin/login.php');
            exit;
        }
    }
}

// Create auth instance
$adminAuth = new AdminAuth();

// Check authentication for all admin pages except login
$currentPage = basename($_SERVER['PHP_SELF']);
if ($currentPage !== 'login.php') {
    $adminAuth->requireAuth();
}

<?php
// Authentication class for NSCT project

class Auth {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // Login with mobile number
    public function loginWithMobile($mobile, $password) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM members WHERE mobile = ?");
            $stmt->execute([$mobile]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                // Check if user is active
                if ($user['status'] !== 'active') {
                    return [
                        'success' => false,
                        'message' => 'Your account is not active. Please contact administrator.'
                    ];
                }
                
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_mobile'] = $user['mobile'];
                
                return [
                    'success' => true,
                    'message' => 'Login successful'
                ];
            }
            
            return [
                'success' => false,
                'message' => 'Invalid mobile number or password'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }
    
    // Admin login
    public function adminLogin($username, $password) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM admins WHERE username = ?");
            $stmt->execute([$username]);
            $admin = $stmt->fetch();
            
            if ($admin && password_verify($password, $admin['password'])) {
                // Set session variables
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['name'];
                $_SESSION['admin_role'] = $admin['role'];
                
                // Update last login time
                $updateStmt = $this->pdo->prepare("UPDATE admins SET last_login = NOW() WHERE id = ?");
                $updateStmt->execute([$admin['id']]);
                
                return [
                    'success' => true,
                    'message' => 'Login successful'
                ];
            }
            
            return [
                'success' => false,
                'message' => 'Invalid username or password'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }
    
    // Logout
    public function logout() {
        // Unset all session variables
        $_SESSION = [];
        
        // If it's desired to kill the session, also delete the session cookie.
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Finally, destroy the session.
        session_destroy();
        
        return [
            'success' => true,
            'message' => 'Logout successful'
        ];
    }
    
    // Check if user is logged in
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    // Check if admin is logged in
    public function isAdminLoggedIn() {
        return isset($_SESSION['admin_id']);
    }
    
    // Register new user
    public function register($userData) {
        try {
            // Check if mobile or email already exists
            $checkStmt = $this->pdo->prepare("SELECT id FROM members WHERE mobile = ? OR email = ?");
            $checkStmt->execute([$userData['mobile'], $userData['email']]);
            if ($checkStmt->fetch()) {
                return [
                    'success' => false,
                    'message' => 'Mobile number or email already registered'
                ];
            }
            
            // Hash password
            $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
            
            // Insert user data
            $sql = "INSERT INTO members (name, mobile, email, password, gender, dob, pan, photo, id_proof, state, district, country, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $userData['name'],
                $userData['mobile'],
                $userData['email'],
                $userData['password'],
                $userData['gender'] ?? null,
                $userData['dob'] ?? null,
                $userData['pan'] ?? null,
                $userData['photo'] ?? null,
                $userData['id_proof'] ?? null,
                $userData['state'] ?? null,
                $userData['district'] ?? null,
                $userData['country'] ?? 'India'
            ]);
            
            return [
                'success' => true,
                'message' => 'Registration successful',
                'user_id' => $this->pdo->lastInsertId()
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }
    
    // Request password reset
    public function requestPasswordReset($email) {
        try {
            // Check if email exists
            $stmt = $this->pdo->prepare("SELECT id FROM members WHERE email = ?");
            $stmt->execute([$email]);
            if (!$stmt->fetch()) {
                return [
                    'success' => false,
                    'message' => 'Email not found'
                ];
            }
            
            // Generate token
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hour
            
            // Delete any existing tokens for this email
            $deleteStmt = $this->pdo->prepare("DELETE FROM password_reset WHERE email = ?");
            $deleteStmt->execute([$email]);
            
            // Insert new token
            $insertStmt = $this->pdo->prepare("INSERT INTO password_reset (email, token, expires_at) VALUES (?, ?, ?)");
            $insertStmt->execute([$email, $token, $expires]);
            
            return [
                'success' => true,
                'message' => 'Password reset link sent',
                'token' => $token
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }
    
    // Reset password
    public function resetPassword($token, $password) {
        try {
            // Check if token exists and is valid
            $stmt = $this->pdo->prepare("SELECT email, expires_at FROM password_reset WHERE token = ?");
            $stmt->execute([$token]);
            $reset = $stmt->fetch();
            
            if (!$reset) {
                return [
                    'success' => false,
                    'message' => 'Invalid token'
                ];
            }
            
            // Check if token is expired
            if (strtotime($reset['expires_at']) < time()) {
                return [
                    'success' => false,
                    'message' => 'Token expired'
                ];
            }
            
            // Update password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $updateStmt = $this->pdo->prepare("UPDATE members SET password = ? WHERE email = ?");
            $updateStmt->execute([$hashedPassword, $reset['email']]);
            
            // Delete token
            $deleteStmt = $this->pdo->prepare("DELETE FROM password_reset WHERE token = ?");
            $deleteStmt->execute([$token]);
            
            return [
                'success' => true,
                'message' => 'Password reset successful'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }
}

// Create auth instance
$auth = new Auth($pdo);

// Create admin auth instance
$adminAuth = new Auth($pdo);
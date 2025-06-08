<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Session security checks
function checkSessionSecurity() {
    if (isset($_SESSION['admin_last_activity'])) {
        $timeout = 1800; // 30 minutes
        if (time() - $_SESSION['admin_last_activity'] > $timeout) {
            session_unset();
            session_destroy();
            header('Location: /admin/login.php?timeout=1');
            exit;
        }
        $_SESSION['admin_last_activity'] = time();
    }
}

// Regenerate session ID periodically
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} else if (time() - $_SESSION['created'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}

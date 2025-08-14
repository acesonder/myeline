<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    // Log the logout activity
    logActivity('logout', 'User logged out');
    
    // Remove session from database
    try {
        $db = getDB();
        $db->query("DELETE FROM user_sessions WHERE id = ?", [session_id()]);
    } catch (Exception $e) {
        error_log("Error removing session: " . $e->getMessage());
    }
}

// Destroy session
session_destroy();

// Clear session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirect to landing page
header('Location: index.php');
exit();
?>
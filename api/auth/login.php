<?php
/**
 * Login API endpoint
 */

require_once '../../config/database.php';
require_once '../../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Method not allowed', 405);
}

try {
    // Get input data
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']) && $_POST['remember'];
    
    // Validate input
    if (empty($email) || empty($password)) {
        errorResponse('Email and password are required');
    }
    
    if (!validateEmail($email)) {
        errorResponse('Invalid email format');
    }
    
    // Get user from database
    $db = getDB();
    $stmt = $db->query("
        SELECT id, username, email, password_hash, first_name, last_name, user_type, 
               email_verified, login_attempts, locked_until
        FROM users 
        WHERE email = ? AND deleted_at IS NULL
    ", [$email]);
    
    $user = $stmt->fetch();
    
    if (!$user) {
        // Log failed attempt
        logActivity('login_failed', "Failed login attempt for email: $email");
        errorResponse('Invalid email or password');
    }
    
    // Check if account is locked
    if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
        $lockExpiry = date('g:i A', strtotime($user['locked_until']));
        errorResponse("Account is locked until $lockExpiry due to too many failed login attempts");
    }
    
    // Check if email is verified
    if (!$user['email_verified']) {
        errorResponse('Please verify your email address before logging in');
    }
    
    // Verify password
    if (!verifyPassword($password, $user['password_hash'])) {
        // Increment login attempts
        $attempts = $user['login_attempts'] + 1;
        $lockUntil = null;
        
        // Lock account after 5 failed attempts for 30 minutes
        if ($attempts >= 5) {
            $lockUntil = date('Y-m-d H:i:s', strtotime('+30 minutes'));
        }
        
        $db->query("
            UPDATE users 
            SET login_attempts = ?, locked_until = ?
            WHERE id = ?
        ", [$attempts, $lockUntil, $user['id']]);
        
        logActivity('login_failed', "Failed login attempt for user ID: {$user['id']}", [
            'attempts' => $attempts,
            'locked' => $lockUntil !== null
        ]);
        
        errorResponse('Invalid email or password');
    }
    
    // Successful login - reset login attempts and update last login
    $db->query("
        UPDATE users 
        SET login_attempts = 0, locked_until = NULL, last_login = NOW()
        WHERE id = ?
    ", [$user['id']]);
    
    // Create session
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['user_type'] = $user['user_type'];
    $_SESSION['login_time'] = time();
    
    // Set session cookie expiry
    if ($remember) {
        // Remember for 30 days
        $expire = time() + (30 * 24 * 60 * 60);
        setcookie(session_name(), session_id(), $expire, '/');
    }
    
    // Create session record
    $sessionData = [
        'id' => session_id(),
        'user_id' => $user['id'],
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
    ];
    
    $db->query("
        INSERT INTO user_sessions (id, user_id, ip_address, user_agent)
        VALUES (:id, :user_id, :ip_address, :user_agent)
        ON DUPLICATE KEY UPDATE 
        last_activity = CURRENT_TIMESTAMP,
        ip_address = VALUES(ip_address),
        user_agent = VALUES(user_agent)
    ", $sessionData);
    
    // Log successful login
    logActivity('login_success', "User logged in successfully");
    
    // Determine redirect URL based on user type
    $redirectUrl = match($user['user_type']) {
        'patient' => 'dashboard.php',
        'caregiver' => 'caregiver-dashboard.php',
        'admin' => 'admin-dashboard.php',
        default => 'dashboard.php'
    };
    
    successResponse('Login successful', [
        'redirect' => $redirectUrl,
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'user_type' => $user['user_type']
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    errorResponse('An error occurred during login. Please try again.');
}
?>
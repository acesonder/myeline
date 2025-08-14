<?php
/**
 * Registration API endpoint
 */

require_once '../../config/database.php';
require_once '../../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Method not allowed', 405);
}

try {
    // Get input data
    $firstName = sanitizeInput($_POST['firstName'] ?? '');
    $lastName = sanitizeInput($_POST['lastName'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $userType = sanitizeInput($_POST['userType'] ?? '');
    $agreeTerms = isset($_POST['agreeTerms']) && $_POST['agreeTerms'];
    
    // Validate input
    $errors = [];
    
    if (empty($firstName)) {
        $errors['firstName'] = 'First name is required';
    }
    
    if (empty($lastName)) {
        $errors['lastName'] = 'Last name is required';
    }
    
    if (empty($email)) {
        $errors['registerEmail'] = 'Email is required';
    } elseif (!validateEmail($email)) {
        $errors['registerEmail'] = 'Invalid email format';
    }
    
    if (empty($username)) {
        $errors['username'] = 'Username is required';
    } elseif (strlen($username) < 3) {
        $errors['username'] = 'Username must be at least 3 characters';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors['username'] = 'Username can only contain letters, numbers, and underscores';
    }
    
    if (empty($password)) {
        $errors['registerPassword'] = 'Password is required';
    } else {
        $passwordErrors = validatePasswordStrength($password);
        if (!empty($passwordErrors)) {
            $errors['registerPassword'] = implode('. ', $passwordErrors);
        }
    }
    
    if (empty($userType) || !in_array($userType, ['patient', 'caregiver'])) {
        $errors['userType'] = 'Please select a valid user type';
    }
    
    if (!$agreeTerms) {
        $errors['agreeTerms'] = 'You must agree to the terms and conditions';
    }
    
    if (!empty($errors)) {
        errorResponse('Please correct the following errors:', 400, $errors);
    }
    
    $db = getDB();
    
    // Check if email already exists
    $stmt = $db->query("SELECT id FROM users WHERE email = ?", [$email]);
    if ($stmt->fetch()) {
        errorResponse('An account with this email already exists', 400, [
            'registerEmail' => 'Email already registered'
        ]);
    }
    
    // Check if username already exists
    $stmt = $db->query("SELECT id FROM users WHERE username = ?", [$username]);
    if ($stmt->fetch()) {
        errorResponse('This username is already taken', 400, [
            'username' => 'Username already taken'
        ]);
    }
    
    // Create new user
    $userData = [
        'username' => $username,
        'email' => $email,
        'password_hash' => hashPassword($password),
        'first_name' => $firstName,
        'last_name' => $lastName,
        'user_type' => $userType,
        'email_verified' => 0, // Will be verified via email
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    $db->beginTransaction();
    
    try {
        // Insert user
        $stmt = $db->query("
            INSERT INTO users (username, email, password_hash, first_name, last_name, user_type, email_verified, created_at)
            VALUES (:username, :email, :password_hash, :first_name, :last_name, :user_type, :email_verified, :created_at)
        ", $userData);
        
        $userId = $db->lastInsertId();
        
        // Generate email verification token
        $verificationToken = generateToken(32);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        $db->query("
            INSERT INTO password_resets (user_id, token, expires_at, used)
            VALUES (?, ?, ?, 0)
        ", [$userId, $verificationToken, $expiresAt]);
        
        // Log registration
        logActivity('user_registered', "New user registered: $username ($userType)", [
            'user_id' => $userId,
            'email' => $email,
            'user_type' => $userType
        ]);
        
        $db->commit();
        
        // Send verification email (in a real implementation)
        // For now, we'll just return success
        $verificationLink = "http://{$_SERVER['HTTP_HOST']}/verify-email.php?token=$verificationToken";
        
        // In production, you would send an actual email here
        // sendEmail($email, 'Verify Your Myeline Account', $emailBody);
        
        successResponse('Registration successful! Please check your email to verify your account.', [
            'user_id' => $userId,
            'verification_required' => true,
            'verification_link' => $verificationLink // Only for demo purposes
        ]);
        
    } catch (Exception $e) {
        $db->rollback();
        throw $e;
    }
    
} catch (Exception $e) {
    error_log("Registration error: " . $e->getMessage());
    errorResponse('An error occurred during registration. Please try again.');
}
?>
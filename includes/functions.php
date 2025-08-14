<?php
/**
 * Common functions for Myeline Cancer Care Hub
 */

/**
 * Sanitize input data
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email address
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Generate secure random token
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Hash password securely
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify password against hash
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Require user to be logged in
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: index.php');
        exit();
    }
}

/**
 * Get current user information
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    try {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM users WHERE id = ? AND deleted_at IS NULL", [$_SESSION['user_id']]);
        return $stmt->fetch();
    } catch (Exception $e) {
        error_log("Error fetching current user: " . $e->getMessage());
        return null;
    }
}

/**
 * Check if user has specific permission
 */
function hasPermission($permission) {
    $user = getCurrentUser();
    if (!$user) return false;
    
    // Admin users have all permissions
    if ($user['user_type'] === 'admin') {
        return true;
    }
    
    // Add specific permission checks here
    return false;
}

/**
 * Log user activity
 */
function logActivity($action, $description = null, $additionalData = null) {
    try {
        $db = getDB();
        $userId = $_SESSION['user_id'] ?? null;
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        
        $data = [
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'additional_data' => $additionalData ? json_encode($additionalData) : null
        ];
        
        $db->query("
            INSERT INTO activity_log (user_id, action, description, ip_address, user_agent, additional_data)
            VALUES (:user_id, :action, :description, :ip_address, :user_agent, :additional_data)
        ", $data);
        
    } catch (Exception $e) {
        error_log("Error logging activity: " . $e->getMessage());
    }
}

/**
 * Send JSON response
 */
function jsonResponse($data, $httpCode = 200) {
    http_response_code($httpCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

/**
 * Send error response
 */
function errorResponse($message, $httpCode = 400, $errors = null) {
    $response = [
        'success' => false,
        'message' => $message
    ];
    
    if ($errors) {
        $response['errors'] = $errors;
    }
    
    jsonResponse($response, $httpCode);
}

/**
 * Send success response
 */
function successResponse($message, $data = null) {
    $response = [
        'success' => true,
        'message' => $message
    ];
    
    if ($data) {
        $response['data'] = $data;
    }
    
    jsonResponse($response);
}

/**
 * Validate CSRF token
 */
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = generateToken(32);
    }
    return $_SESSION['csrf_token'];
}

/**
 * Format date for display
 */
function formatDate($date, $format = 'M j, Y') {
    if (!$date) return '';
    return date($format, strtotime($date));
}

/**
 * Format datetime for display
 */
function formatDateTime($datetime, $format = 'M j, Y g:i A') {
    if (!$datetime) return '';
    return date($format, strtotime($datetime));
}

/**
 * Calculate time ago
 */
function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'just now';
    if ($time < 3600) return floor($time/60) . ' min ago';
    if ($time < 86400) return floor($time/3600) . ' hr ago';
    if ($time < 2592000) return floor($time/86400) . ' days ago';
    if ($time < 31536000) return floor($time/2592000) . ' months ago';
    
    return floor($time/31536000) . ' years ago';
}

/**
 * Get user's timezone
 */
function getUserTimezone() {
    $user = getCurrentUser();
    return $user['timezone'] ?? 'America/Regina';
}

/**
 * Convert UTC time to user timezone
 */
function toUserTime($utcTime) {
    $userTz = new DateTimeZone(getUserTimezone());
    $utc = new DateTimeZone('UTC');
    
    $date = new DateTime($utcTime, $utc);
    $date->setTimezone($userTz);
    
    return $date->format('Y-m-d H:i:s');
}

/**
 * Convert user time to UTC
 */
function toUTCTime($userTime) {
    $userTz = new DateTimeZone(getUserTimezone());
    $utc = new DateTimeZone('UTC');
    
    $date = new DateTime($userTime, $userTz);
    $date->setTimezone($utc);
    
    return $date->format('Y-m-d H:i:s');
}

/**
 * Upload file securely
 */
function uploadFile($file, $allowedTypes = [], $maxSize = null) {
    if (!$maxSize) {
        $maxSize = UPLOAD_MAX_SIZE;
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('File upload error: ' . $file['error']);
    }
    
    if ($file['size'] > $maxSize) {
        throw new Exception('File too large. Maximum size: ' . formatBytes($maxSize));
    }
    
    $pathInfo = pathinfo($file['name']);
    $extension = strtolower($pathInfo['extension']);
    
    if (!empty($allowedTypes) && !in_array($extension, $allowedTypes)) {
        throw new Exception('File type not allowed. Allowed types: ' . implode(', ', $allowedTypes));
    }
    
    // Generate unique filename
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $uploadPath = UPLOAD_PATH . $filename;
    
    // Create upload directory if it doesn't exist
    if (!file_exists(UPLOAD_PATH)) {
        mkdir(UPLOAD_PATH, 0755, true);
    }
    
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        throw new Exception('Failed to move uploaded file');
    }
    
    return [
        'filename' => $filename,
        'original_name' => $file['name'],
        'size' => $file['size'],
        'type' => $file['type'],
        'path' => $uploadPath
    ];
}

/**
 * Format bytes to human readable format
 */
function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}

/**
 * Validate password strength
 */
function validatePasswordStrength($password) {
    $errors = [];
    
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long';
    }
    
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = 'Password must contain at least one lowercase letter';
    }
    
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = 'Password must contain at least one uppercase letter';
    }
    
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = 'Password must contain at least one number';
    }
    
    if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
        $errors[] = 'Password must contain at least one special character';
    }
    
    return $errors;
}

/**
 * Send email notification
 */
function sendEmail($to, $subject, $body, $isHTML = true) {
    // This is a basic implementation
    // In production, you would use a proper email library like PHPMailer or SwiftMailer
    
    $headers = [
        'From: ' . SMTP_FROM_NAME . ' <' . SMTP_FROM_EMAIL . '>',
        'Reply-To: ' . SMTP_FROM_EMAIL,
        'X-Mailer: PHP/' . phpversion()
    ];
    
    if ($isHTML) {
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=UTF-8';
    }
    
    return mail($to, $subject, $body, implode("\r\n", $headers));
}

/**
 * Get weather data for Melfort, SK
 */
function getWeatherData() {
    if (!defined('WEATHER_API_KEY') || empty(WEATHER_API_KEY)) {
        return null;
    }
    
    $city = 'Melfort,SK,CA';
    $url = WEATHER_API_URL . "/weather?q={$city}&appid=" . WEATHER_API_KEY . "&units=metric";
    
    $context = stream_context_create([
        'http' => [
            'timeout' => 10
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    
    if ($response === false) {
        return null;
    }
    
    return json_decode($response, true);
}

/**
 * Check if user is patient or caregiver of patient
 */
function canAccessPatientData($patientId) {
    $user = getCurrentUser();
    if (!$user) return false;
    
    // Patient can access their own data
    if ($user['id'] == $patientId) {
        return true;
    }
    
    // Check if user is caregiver of this patient
    if ($user['user_type'] === 'caregiver') {
        try {
            $db = getDB();
            $stmt = $db->query("
                SELECT id FROM caregiver_relationships 
                WHERE patient_id = ? AND caregiver_id = ? AND status = 'active'
            ", [$patientId, $user['id']]);
            
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            error_log("Error checking caregiver access: " . $e->getMessage());
            return false;
        }
    }
    
    // Admin can access all data
    return $user['user_type'] === 'admin';
}

/**
 * Get daily quote or affirmation
 */
function getDailyContent($type = 'quote') {
    try {
        $db = getDB();
        $stmt = $db->query("
            SELECT content, author FROM daily_content 
            WHERE content_type = ? AND is_active = 1 
            ORDER BY RAND() LIMIT 1
        ", [$type]);
        
        return $stmt->fetch();
    } catch (Exception $e) {
        error_log("Error fetching daily content: " . $e->getMessage());
        return null;
    }
}

/**
 * Calculate medication adherence percentage
 */
function calculateMedicationAdherence($userId, $days = 30) {
    try {
        $db = getDB();
        $since = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        $stmt = $db->query("
            SELECT 
                COUNT(*) as total,
                COUNT(CASE WHEN status = 'taken' THEN 1 END) as taken
            FROM medication_logs 
            WHERE user_id = ? AND scheduled_time >= ?
        ", [$userId, $since]);
        
        $result = $stmt->fetch();
        
        if ($result['total'] == 0) {
            return 100; // No medications scheduled
        }
        
        return round(($result['taken'] / $result['total']) * 100, 1);
        
    } catch (Exception $e) {
        error_log("Error calculating medication adherence: " . $e->getMessage());
        return 0;
    }
}

/**
 * Get unread message count
 */
function getUnreadMessageCount($userId) {
    try {
        $db = getDB();
        $stmt = $db->query("
            SELECT COUNT(*) as count
            FROM messages m
            JOIN conversations c ON m.conversation_id = c.id
            WHERE JSON_CONTAINS(c.participants, ?) 
            AND m.sender_id != ?
            AND (m.read_by IS NULL OR NOT JSON_CONTAINS(m.read_by, ?))
        ", [json_encode($userId), $userId, json_encode($userId)]);
        
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
        
    } catch (Exception $e) {
        error_log("Error getting unread message count: " . $e->getMessage());
        return 0;
    }
}

/**
 * Generate AI insight (placeholder for future AI integration)
 */
function generateAIInsight($userId, $data) {
    // This is a placeholder for AI integration
    // In a real implementation, this would connect to an AI service
    
    $insights = [
        "Your pain levels have been consistently lower on days when you drink more water.",
        "Your mood tends to improve after morning walks. Consider adding more light exercise to your routine.",
        "You've been very consistent with your medication schedule this week. Great job!",
        "Your sleep quality correlates with your afternoon activity levels.",
        "Consider discussing your recent symptom patterns with your healthcare provider."
    ];
    
    return $insights[array_rand($insights)];
}

/**
 * Check if it's time for medication reminder
 */
function checkMedicationReminders($userId) {
    try {
        $db = getDB();
        $now = date('Y-m-d H:i:s');
        $soon = date('Y-m-d H:i:s', strtotime('+30 minutes'));
        
        $stmt = $db->query("
            SELECT m.name, m.dosage, ml.scheduled_time
            FROM medication_logs ml
            JOIN medications m ON ml.medication_id = m.id
            WHERE ml.user_id = ? 
            AND ml.status = 'scheduled'
            AND ml.scheduled_time BETWEEN ? AND ?
            ORDER BY ml.scheduled_time
        ", [$userId, $now, $soon]);
        
        return $stmt->fetchAll();
        
    } catch (Exception $e) {
        error_log("Error checking medication reminders: " . $e->getMessage());
        return [];
    }
}

/**
 * Create notification
 */
function createNotification($userId, $type, $title, $content, $actionUrl = null) {
    try {
        $db = getDB();
        $data = [
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'content' => $content,
            'action_url' => $actionUrl
        ];
        
        $db->query("
            INSERT INTO notifications (user_id, type, title, content, action_url)
            VALUES (:user_id, :type, :title, :content, :action_url)
        ", $data);
        
        return true;
        
    } catch (Exception $e) {
        error_log("Error creating notification: " . $e->getMessage());
        return false;
    }
}

/**
 * Get system setting
 */
function getSetting($key, $default = null) {
    try {
        $db = getDB();
        $stmt = $db->query("SELECT value FROM settings WHERE `key` = ?", [$key]);
        $result = $stmt->fetch();
        
        return $result ? $result['value'] : $default;
        
    } catch (Exception $e) {
        error_log("Error getting setting: " . $e->getMessage());
        return $default;
    }
}

/**
 * Set system setting
 */
function setSetting($key, $value, $description = null) {
    try {
        $db = getDB();
        $data = [
            'key' => $key,
            'value' => $value,
            'description' => $description
        ];
        
        $db->query("
            INSERT INTO settings (`key`, `value`, `description`) 
            VALUES (:key, :value, :description)
            ON DUPLICATE KEY UPDATE 
            value = VALUES(value), 
            description = VALUES(description)
        ", $data);
        
        return true;
        
    } catch (Exception $e) {
        error_log("Error setting value: " . $e->getMessage());
        return false;
    }
}
?>
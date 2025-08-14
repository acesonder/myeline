<?php
/**
 * Myeline Maintenance and Troubleshooting Tool
 * 
 * This script provides utilities for common maintenance tasks
 * and troubleshooting issues that might occur with the web application.
 */

// Security check - only allow access from localhost or if maintenance mode is enabled
$allowedIPs = ['127.0.0.1', '::1'];
$currentIP = $_SERVER['REMOTE_ADDR'] ?? '';

if (!in_array($currentIP, $allowedIPs) && !isset($_GET['maintenance_key'])) {
    http_response_code(403);
    die('Access denied. This tool is only accessible from localhost or with maintenance key.');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Myeline Maintenance Tool</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .maintenance-header {
            background: linear-gradient(135deg, #dc3545, #fd7e14);
            color: white;
            padding: 2rem 0;
        }
        .tool-card {
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .tool-card:hover {
            transform: translateY(-2px);
        }
        .output-section {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1rem;
            max-height: 400px;
            overflow-y: auto;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="maintenance-header">
        <div class="container">
            <h1><i class="fas fa-tools me-2"></i>Myeline Maintenance Tool</h1>
            <p class="mb-0">System diagnostics and troubleshooting utilities</p>
        </div>
    </div>

    <div class="container my-5">
        <?php
        if (isset($_POST['action'])) {
            echo '<div class="row mb-4">';
            echo '<div class="col-12">';
            echo '<div class="card">';
            echo '<div class="card-header"><h5>Operation Results</h5></div>';
            echo '<div class="card-body">';
            
            $action = $_POST['action'];
            
            try {
                switch ($action) {
                    case 'check_php':
                        checkPHPConfiguration();
                        break;
                    case 'check_database':
                        checkDatabaseConnection();
                        break;
                    case 'check_permissions':
                        checkFilePermissions();
                        break;
                    case 'check_config':
                        checkConfiguration();
                        break;
                    case 'clear_sessions':
                        clearSessions();
                        break;
                    case 'check_logs':
                        checkErrorLogs();
                        break;
                    case 'system_info':
                        showSystemInfo();
                        break;
                    default:
                        echo '<div class="alert alert-warning">Unknown action: ' . htmlspecialchars($action) . '</div>';
                }
            } catch (Exception $e) {
                echo '<div class="alert alert-danger">';
                echo '<h6>Error occurred:</h6>';
                echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
                echo '</div>';
            }
            
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
        ?>

        <div class="row g-4">
            <!-- PHP Configuration Check -->
            <div class="col-md-6 col-lg-4">
                <div class="card tool-card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-code text-primary me-2"></i>
                            PHP Configuration
                        </h5>
                        <p class="card-text">Check PHP version, extensions, and configuration settings.</p>
                        <form method="post">
                            <input type="hidden" name="action" value="check_php">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>Check PHP
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Database Connection Check -->
            <div class="col-md-6 col-lg-4">
                <div class="card tool-card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-database text-success me-2"></i>
                            Database Connection
                        </h5>
                        <p class="card-text">Test database connectivity and check table structure.</p>
                        <form method="post">
                            <input type="hidden" name="action" value="check_database">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-search me-1"></i>Check Database
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- File Permissions Check -->
            <div class="col-md-6 col-lg-4">
                <div class="card tool-card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-lock text-warning me-2"></i>
                            File Permissions
                        </h5>
                        <p class="card-text">Check file and directory permissions for uploads and cache.</p>
                        <form method="post">
                            <input type="hidden" name="action" value="check_permissions">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-search me-1"></i>Check Permissions
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Configuration Check -->
            <div class="col-md-6 col-lg-4">
                <div class="card tool-card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-cog text-info me-2"></i>
                            Configuration
                        </h5>
                        <p class="card-text">Verify application configuration and settings.</p>
                        <form method="post">
                            <input type="hidden" name="action" value="check_config">
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-search me-1"></i>Check Config
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Clear Sessions -->
            <div class="col-md-6 col-lg-4">
                <div class="card tool-card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-broom text-danger me-2"></i>
                            Clear Sessions
                        </h5>
                        <p class="card-text">Remove all active user sessions and clean session data.</p>
                        <form method="post" onsubmit="return confirm('Are you sure? This will log out all users.')">
                            <input type="hidden" name="action" value="clear_sessions">
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-broom me-1"></i>Clear Sessions
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Error Logs -->
            <div class="col-md-6 col-lg-4">
                <div class="card tool-card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-exclamation-triangle text-secondary me-2"></i>
                            Error Logs
                        </h5>
                        <p class="card-text">View recent error logs and system messages.</p>
                        <form method="post">
                            <input type="hidden" name="action" value="check_logs">
                            <button type="submit" class="btn btn-secondary">
                                <i class="fas fa-file-alt me-1"></i>View Logs
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            System Information
                        </h5>
                        <p class="card-text">Display comprehensive system information and diagnostics.</p>
                        <form method="post">
                            <input type="hidden" name="action" value="system_info">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-info me-1"></i>Show System Info
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
function checkPHPConfiguration() {
    echo '<div class="output-section">';
    
    echo '<h6>PHP Information:</h6>';
    echo 'PHP Version: ' . PHP_VERSION . '<br>';
    echo 'PHP SAPI: ' . php_sapi_name() . '<br><br>';
    
    echo '<h6>Required Extensions:</h6>';
    $required_extensions = ['pdo', 'pdo_mysql', 'json', 'mbstring', 'openssl', 'curl'];
    
    foreach ($required_extensions as $ext) {
        $status = extension_loaded($ext) ? '✅' : '❌';
        echo $status . ' ' . $ext . '<br>';
    }
    
    echo '<br><h6>Important Settings:</h6>';
    $settings = [
        'max_execution_time' => ini_get('max_execution_time'),
        'memory_limit' => ini_get('memory_limit'),
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'post_max_size' => ini_get('post_max_size'),
        'session.save_path' => ini_get('session.save_path')
    ];
    
    foreach ($settings as $setting => $value) {
        echo $setting . ': ' . $value . '<br>';
    }
    
    echo '</div>';
}

function checkDatabaseConnection() {
    echo '<div class="output-section">';
    
    try {
        require_once 'config/database.php';
        
        echo '<h6>Database Connection Test:</h6>';
        
        $db = getDB();
        echo '✅ Database connection successful<br>';
        echo 'Host: ' . DB_HOST . '<br>';
        echo 'Database: ' . DB_NAME . '<br><br>';
        
        // Check tables
        echo '<h6>Table Check:</h6>';
        $tables = $db->fetchAll("SHOW TABLES");
        echo 'Found ' . count($tables) . ' tables:<br>';
        
        foreach ($tables as $table) {
            $tableName = array_values($table)[0];
            $count = $db->fetch("SELECT COUNT(*) as count FROM `$tableName`");
            echo '• ' . $tableName . ' (' . $count['count'] . ' records)<br>';
        }
        
    } catch (Exception $e) {
        echo '❌ Database connection failed:<br>';
        echo htmlspecialchars($e->getMessage());
    }
    
    echo '</div>';
}

function checkFilePermissions() {
    echo '<div class="output-section">';
    
    echo '<h6>File Permissions Check:</h6>';
    
    $paths = [
        'uploads/' => 'Upload directory',
        'config/' => 'Configuration directory',
        'assets/' => 'Assets directory'
    ];
    
    foreach ($paths as $path => $description) {
        if (file_exists($path)) {
            $writable = is_writable($path) ? '✅ Writable' : '❌ Not writable';
            $readable = is_readable($path) ? '✅ Readable' : '❌ Not readable';
            
            echo $description . ' (' . $path . '):<br>';
            echo '  ' . $readable . '<br>';
            echo '  ' . $writable . '<br>';
            echo '  Permissions: ' . substr(sprintf('%o', fileperms($path)), -4) . '<br><br>';
        } else {
            echo '❌ ' . $description . ' (' . $path . ') does not exist<br><br>';
        }
    }
    
    echo '</div>';
}

function checkConfiguration() {
    echo '<div class="output-section">';
    
    echo '<h6>Configuration Check:</h6>';
    
    // Check if config file exists
    if (file_exists('config/database.php')) {
        echo '✅ Database config file exists<br>';
        
        // Check constants
        require_once 'config/database.php';
        
        $constants = ['DB_HOST', 'DB_USERNAME', 'DB_NAME', 'SECRET_KEY'];
        foreach ($constants as $const) {
            if (defined($const)) {
                $value = constant($const);
                if ($const === 'SECRET_KEY' || $const === 'DB_PASSWORD') {
                    $value = str_repeat('*', strlen($value));
                }
                echo '✅ ' . $const . ': ' . $value . '<br>';
            } else {
                echo '❌ ' . $const . ' not defined<br>';
            }
        }
    } else {
        echo '❌ Database config file missing<br>';
    }
    
    echo '<br><h6>Environment:</h6>';
    echo 'APP_ENV: ' . (defined('APP_ENV') ? APP_ENV : 'not set') . '<br>';
    echo 'Error Reporting: ' . (error_reporting() ? 'On' : 'Off') . '<br>';
    echo 'Display Errors: ' . (ini_get('display_errors') ? 'On' : 'Off') . '<br>';
    
    echo '</div>';
}

function clearSessions() {
    echo '<div class="output-section">';
    
    try {
        require_once 'config/database.php';
        $db = getDB();
        
        // Clear database sessions
        $result = $db->execute("DELETE FROM user_sessions");
        echo '✅ Cleared ' . $result . ' database sessions<br>';
        
        // Clear PHP session files
        $sessionPath = session_save_path();
        if ($sessionPath && is_dir($sessionPath)) {
            $files = glob($sessionPath . '/sess_*');
            $count = 0;
            foreach ($files as $file) {
                if (unlink($file)) {
                    $count++;
                }
            }
            echo '✅ Cleared ' . $count . ' session files<br>';
        }
        
        echo '<br>All sessions have been cleared. Users will need to log in again.';
        
    } catch (Exception $e) {
        echo '❌ Error clearing sessions:<br>';
        echo htmlspecialchars($e->getMessage());
    }
    
    echo '</div>';
}

function checkErrorLogs() {
    echo '<div class="output-section">';
    
    echo '<h6>PHP Error Log:</h6>';
    
    $logFile = ini_get('error_log');
    if ($logFile && file_exists($logFile)) {
        $lines = file($logFile);
        $recentLines = array_slice($lines, -20); // Last 20 lines
        
        foreach ($recentLines as $line) {
            echo htmlspecialchars($line) . '<br>';
        }
    } else {
        echo 'No error log file found or accessible.<br>';
    }
    
    echo '<br><h6>Web Server Error Log:</h6>';
    $possibleLogs = [
        '/var/log/apache2/error.log',
        '/var/log/nginx/error.log',
        '/usr/local/var/log/nginx/error.log'
    ];
    
    $found = false;
    foreach ($possibleLogs as $log) {
        if (file_exists($log) && is_readable($log)) {
            $lines = file($log);
            $recentLines = array_slice($lines, -10);
            echo 'From ' . $log . ':<br>';
            foreach ($recentLines as $line) {
                echo htmlspecialchars($line) . '<br>';
            }
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        echo 'No accessible web server error logs found.<br>';
    }
    
    echo '</div>';
}

function showSystemInfo() {
    echo '<div class="output-section">';
    
    echo '<h6>System Information:</h6>';
    echo 'Operating System: ' . PHP_OS . '<br>';
    echo 'Server Software: ' . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . '<br>';
    echo 'Document Root: ' . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . '<br>';
    echo 'Current Time: ' . date('Y-m-d H:i:s T') . '<br>';
    echo 'Timezone: ' . date_default_timezone_get() . '<br><br>';
    
    echo '<h6>Memory Usage:</h6>';
    echo 'Current: ' . formatBytes(memory_get_usage()) . '<br>';
    echo 'Peak: ' . formatBytes(memory_get_peak_usage()) . '<br>';
    echo 'Limit: ' . ini_get('memory_limit') . '<br><br>';
    
    echo '<h6>Disk Usage:</h6>';
    $diskFree = disk_free_space('./');
    $diskTotal = disk_total_space('./');
    if ($diskFree && $diskTotal) {
        echo 'Free space: ' . formatBytes($diskFree) . '<br>';
        echo 'Total space: ' . formatBytes($diskTotal) . '<br>';
        echo 'Used: ' . round((($diskTotal - $diskFree) / $diskTotal) * 100, 2) . '%<br>';
    }
    
    echo '</div>';
}

function formatBytes($size, $precision = 2) {
    $base = log($size, 1024);
    $suffixes = ['B', 'KB', 'MB', 'GB', 'TB'];
    return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
}
?>
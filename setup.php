<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Myeline Setup - Database Installation</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .setup-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }
        
        .setup-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 800px;
            width: 100%;
        }
        
        .setup-header {
            background: linear-gradient(135deg, #4f46e5, #06b6d4);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .setup-body {
            padding: 2rem;
        }
        
        .status-item {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 8px;
            border-left: 4px solid #dee2e6;
        }
        
        .status-item.success {
            background: #d1e7dd;
            border-left-color: #198754;
        }
        
        .status-item.error {
            background: #f8d7da;
            border-left-color: #dc3545;
        }
        
        .status-item.warning {
            background: #fff3cd;
            border-left-color: #ffc107;
        }
        
        .status-item.info {
            background: #cff4fc;
            border-left-color: #0dcaf0;
        }
        
        .output-log {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1rem;
            max-height: 400px;
            overflow-y: auto;
            font-family: 'Courier New', monospace;
            font-size: 0.875rem;
        }
        
        .log-entry {
            margin-bottom: 0.5rem;
        }
        
        .log-timestamp {
            color: #6c757d;
            margin-right: 0.5rem;
        }
        
        .log-type {
            font-weight: bold;
            margin-right: 0.5rem;
        }
        
        .log-type.info { color: #0dcaf0; }
        .log-type.success { color: #198754; }
        .log-type.warning { color: #ffc107; }
        .log-type.error { color: #dc3545; }
    </style>
</head>
<body>
    <div class="setup-container">
        <div class="setup-card">
            <div class="setup-header">
                <h1 class="mb-2">
                    <i class="fas fa-heart me-2"></i>
                    Myeline Setup
                </h1>
                <p class="mb-0">Cancer Care & Comfort Hub</p>
            </div>
            
            <div class="setup-body">
                <?php
                if (isset($_POST['action'])) {
                    require_once 'database/install.php';
                    
                    $installer = new DatabaseInstaller();
                    $action = $_POST['action'];
                    $includeDemoData = isset($_POST['demo_data']);
                    
                    echo '<div class="alert alert-info">';
                    echo '<i class="fas fa-cog fa-spin me-2"></i>';
                    echo 'Processing request...';
                    echo '</div>';
                    
                    echo '<div class="output-log mb-4">';
                    
                    try {
                        switch ($action) {
                            case 'install':
                                $success = $installer->install($includeDemoData);
                                break;
                            case 'reset':
                                $success = $installer->resetDatabase();
                                break;
                            case 'health':
                                $checks = $installer->checkSystemHealth();
                                $success = true;
                                break;
                        }
                        
                        $output = $installer->getOutput();
                        foreach ($output as $entry) {
                            echo '<div class="log-entry">';
                            echo '<span class="log-timestamp">[' . $entry['timestamp'] . ']</span>';
                            echo '<span class="log-type ' . $entry['type'] . '">' . strtoupper($entry['type']) . ':</span>';
                            echo htmlspecialchars($entry['message']);
                            echo '</div>';
                        }
                        
                        if ($action === 'health' && isset($checks)) {
                            echo '<div class="mt-3">';
                            echo '<strong>System Health Check Results:</strong><br>';
                            foreach ($checks as $check => $result) {
                                $statusClass = $result['status'] === 'OK' ? 'success' : ($result['status'] === 'WARNING' ? 'warning' : 'error');
                                echo '<div class="log-entry">';
                                echo '<span class="log-type ' . $statusClass . '">[' . $check . '] ' . $result['status'] . ':</span>';
                                echo htmlspecialchars($result['message']);
                                echo '</div>';
                            }
                            echo '</div>';
                        }
                        
                    } catch (Exception $e) {
                        echo '<div class="log-entry">';
                        echo '<span class="log-type error">ERROR:</span>';
                        echo htmlspecialchars($e->getMessage());
                        echo '</div>';
                        $success = false;
                    }
                    
                    echo '</div>';
                    
                    if ($success && $action === 'install') {
                        echo '<div class="alert alert-success">';
                        echo '<h5><i class="fas fa-check-circle me-2"></i>Installation Complete!</h5>';
                        echo '<p class="mb-0">Your Myeline installation is ready. You can now start using the application.</p>';
                        echo '<div class="mt-3">';
                        echo '<a href="index.php" class="btn btn-success me-2">Go to Application</a>';
                        if ($includeDemoData) {
                            echo '<div class="mt-2"><small class="text-muted">';
                            echo '<strong>Demo Accounts:</strong><br>';
                            echo 'Admin: admin / Admin123!<br>';
                            echo 'Patient: patient_demo / Patient123!<br>';
                            echo 'Caregiver: caregiver_demo / Caregiver123!';
                            echo '</small></div>';
                        }
                        echo '</div>';
                        echo '</div>';
                    } elseif ($success && $action === 'reset') {
                        echo '<div class="alert alert-warning">';
                        echo '<h5><i class="fas fa-exclamation-triangle me-2"></i>Database Reset Complete</h5>';
                        echo '<p class="mb-0">All data has been removed. You can now reinstall the database.</p>';
                        echo '</div>';
                    } elseif (!$success) {
                        echo '<div class="alert alert-danger">';
                        echo '<h5><i class="fas fa-times-circle me-2"></i>Operation Failed</h5>';
                        echo '<p class="mb-0">There was an error during the operation. Please check the logs above for details.</p>';
                        echo '</div>';
                    }
                } else {
                    // Show setup options
                ?>
                
                <div class="mb-4">
                    <h3><i class="fas fa-database me-2"></i>Database Setup</h3>
                    <p class="text-muted">
                        Welcome to the Myeline setup wizard. This tool will help you install and configure 
                        your Cancer Care & Comfort Hub database and system.
                    </p>
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-download text-primary me-2"></i>
                                    Install Database
                                </h5>
                                <p class="card-text">
                                    Install the complete database schema, create the admin user, 
                                    and set up the basic system configuration.
                                </p>
                                <form method="post" class="mt-3">
                                    <input type="hidden" name="action" value="install">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="demoData" name="demo_data">
                                        <label class="form-check-label" for="demoData">
                                            Include demo data for testing
                                        </label>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-download me-2"></i>Install Database
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-stethoscope text-success me-2"></i>
                                    System Health Check
                                </h5>
                                <p class="card-text">
                                    Check the current system health, database connectivity, 
                                    and verify that all components are working correctly.
                                </p>
                                <form method="post" class="mt-3">
                                    <input type="hidden" name="action" value="health">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-stethoscope me-2"></i>Check System Health
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-redo text-warning me-2"></i>
                                    Reset Database
                                </h5>
                                <p class="card-text">
                                    <strong class="text-danger">Warning:</strong> This will completely 
                                    remove all data and reset the database. Use with caution.
                                </p>
                                <form method="post" class="mt-3" onsubmit="return confirm('Are you sure you want to reset the database? This will delete ALL data and cannot be undone.')">
                                    <input type="hidden" name="action" value="reset">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-redo me-2"></i>Reset Database
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-book text-info me-2"></i>
                                    Documentation
                                </h5>
                                <p class="card-text">
                                    Need help? Check the documentation for detailed setup instructions,
                                    troubleshooting guides, and feature explanations.
                                </p>
                                <div class="mt-3">
                                    <a href="README.md" class="btn btn-info me-2" target="_blank">
                                        <i class="fas fa-book me-2"></i>View README
                                    </a>
                                    <a href="#" class="btn btn-outline-info">
                                        <i class="fas fa-question-circle me-2"></i>Help
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Before You Start</h6>
                        <ul class="mb-0">
                            <li>Ensure MySQL/MariaDB is running and accessible</li>
                            <li>Verify database credentials in <code>config/database.php</code></li>
                            <li>Make sure PHP has the required extensions (PDO, MySQLi, JSON)</li>
                            <li>Ensure the web server has write permissions for uploads</li>
                        </ul>
                    </div>
                </div>

                <?php } ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
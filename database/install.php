<?php
/**
 * Myeline Database Installation and Setup Script
 * 
 * This script handles:
 * - Database creation and schema installation
 * - Demo content population
 * - System verification and health checks
 * - Troubleshooting utilities
 */

require_once __DIR__ . '/../config/database.php';

class DatabaseInstaller {
    private $host;
    private $username;
    private $password;
    private $dbname;
    private $connection;
    private $output = [];
    
    public function __construct() {
        $this->host = DB_HOST;
        $this->username = DB_USERNAME;
        $this->password = DB_PASSWORD;
        $this->dbname = DB_NAME;
    }
    
    public function install($includeDemoData = false) {
        try {
            $this->log("Starting Myeline Database Installation...", 'info');
            
            // Step 1: Connect to MySQL server (without database)
            $this->connectToServer();
            
            // Step 2: Create database if it doesn't exist
            $this->createDatabase();
            
            // Step 3: Connect to the new database
            $this->connectToDatabase();
            
            // Step 4: Install schema
            $this->installSchema();
            
            // Step 5: Insert system settings
            $this->insertSystemSettings();
            
            // Step 6: Create default admin user
            $this->createDefaultAdmin();
            
            // Step 7: Insert demo data if requested
            if ($includeDemoData) {
                $this->insertDemoData();
            }
            
            // Step 8: Verify installation
            $this->verifyInstallation();
            
            $this->log("Database installation completed successfully!", 'success');
            return true;
            
        } catch (Exception $e) {
            $this->log("Installation failed: " . $e->getMessage(), 'error');
            return false;
        }
    }
    
    private function connectToServer() {
        try {
            $dsn = "mysql:host={$this->host};charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
            $this->log("Connected to MySQL server successfully", 'success');
            
        } catch (PDOException $e) {
            throw new Exception("Failed to connect to MySQL server: " . $e->getMessage());
        }
    }
    
    private function createDatabase() {
        try {
            $this->connection->exec("CREATE DATABASE IF NOT EXISTS `{$this->dbname}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $this->log("Database '{$this->dbname}' created successfully", 'success');
        } catch (PDOException $e) {
            throw new Exception("Failed to create database: " . $e->getMessage());
        }
    }
    
    private function connectToDatabase() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
            $this->log("Connected to database '{$this->dbname}' successfully", 'success');
            
        } catch (PDOException $e) {
            throw new Exception("Failed to connect to database: " . $e->getMessage());
        }
    }
    
    private function installSchema() {
        try {
            $schemaFile = __DIR__ . '/schema.sql';
            if (!file_exists($schemaFile)) {
                throw new Exception("Schema file not found: $schemaFile");
            }
            
            $sql = file_get_contents($schemaFile);
            
            // Split the SQL file by semicolons and execute each statement
            $statements = array_filter(array_map('trim', explode(';', $sql)));
            
            foreach ($statements as $statement) {
                if (!empty($statement) && !preg_match('/^--/', $statement)) {
                    $this->connection->exec($statement);
                }
            }
            
            $this->log("Database schema installed successfully", 'success');
            
        } catch (PDOException $e) {
            throw new Exception("Failed to install schema: " . $e->getMessage());
        }
    }
    
    private function insertSystemSettings() {
        try {
            $settings = [
                ['app_name', 'Myeline', 'Application name'],
                ['app_version', '1.0.0', 'Application version'],
                ['app_timezone', 'America/Regina', 'Default application timezone'],
                ['maintenance_mode', '0', 'Maintenance mode flag'],
                ['max_file_upload_size', '5242880', 'Maximum file upload size in bytes (5MB)'],
                ['session_timeout', '3600', 'Session timeout in seconds'],
                ['password_min_length', '8', 'Minimum password length'],
                ['enable_2fa', '1', 'Enable two-factor authentication'],
                ['privacy_policy_url', '', 'Privacy policy URL'],
                ['terms_of_service_url', '', 'Terms of service URL'],
                ['support_email', 'support@myeline.com', 'Support email address'],
                ['smtp_enabled', '0', 'Enable SMTP email sending'],
                ['weather_api_enabled', '0', 'Enable weather API integration'],
                ['ai_assistant_enabled', '1', 'Enable AI assistant features'],
                ['data_retention_days', '2555', 'Data retention period in days (7 years)'],
                ['backup_enabled', '1', 'Enable automatic backups'],
                ['audit_log_enabled', '1', 'Enable audit logging'],
                ['emergency_contact_required', '1', 'Require emergency contact information'],
                ['caregiver_approval_required', '1', 'Require approval for caregiver relationships'],
                ['medication_reminder_enabled', '1', 'Enable medication reminders'],
                ['symptom_alert_threshold', '8', 'Symptom severity threshold for alerts'],
                ['pain_alert_threshold', '8', 'Pain level threshold for alerts'],
                ['hydration_daily_goal', '2000', 'Daily hydration goal in ml'],
                ['demo_mode', '0', 'Demo mode flag for testing']
            ];
            
            $stmt = $this->connection->prepare("
                INSERT IGNORE INTO settings (`key`, `value`, `description`) 
                VALUES (?, ?, ?)
            ");
            
            foreach ($settings as $setting) {
                $stmt->execute($setting);
            }
            
            $this->log("System settings inserted successfully", 'success');
            
        } catch (PDOException $e) {
            throw new Exception("Failed to insert system settings: " . $e->getMessage());
        }
    }
    
    private function createDefaultAdmin() {
        try {
            // Check if admin user already exists
            $stmt = $this->connection->prepare("SELECT id FROM users WHERE username = 'admin' OR user_type = 'admin'");
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $this->log("Admin user already exists, skipping creation", 'info');
                return;
            }
            
            // Create default admin user
            $adminData = [
                'username' => 'admin',
                'email' => 'admin@myeline.com',
                'password_hash' => password_hash('Admin123!', PASSWORD_DEFAULT),
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'user_type' => 'admin',
                'email_verified' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $stmt = $this->connection->prepare("
                INSERT INTO users (username, email, password_hash, first_name, last_name, user_type, email_verified, created_at)
                VALUES (:username, :email, :password_hash, :first_name, :last_name, :user_type, :email_verified, :created_at)
            ");
            
            $stmt->execute($adminData);
            
            $this->log("Default admin user created (Username: admin, Password: Admin123!)", 'success');
            $this->log("IMPORTANT: Please change the default admin password after first login!", 'warning');
            
        } catch (PDOException $e) {
            throw new Exception("Failed to create admin user: " . $e->getMessage());
        }
    }
    
    private function insertDemoData() {
        try {
            $this->log("Inserting demo data...", 'info');
            
            // Create demo patient
            $patientData = [
                'username' => 'patient_demo',
                'email' => 'patient@demo.myeline.com',
                'password_hash' => password_hash('Patient123!', PASSWORD_DEFAULT),
                'first_name' => 'Jane',
                'last_name' => 'Doe',
                'user_type' => 'patient',
                'phone' => '306-555-0123',
                'date_of_birth' => '1965-03-15',
                'emergency_contact' => 'John Doe (Husband)',
                'emergency_phone' => '306-555-0124',
                'email_verified' => 1
            ];
            
            $stmt = $this->connection->prepare("
                INSERT INTO users (username, email, password_hash, first_name, last_name, user_type, phone, date_of_birth, emergency_contact, emergency_phone, email_verified)
                VALUES (:username, :email, :password_hash, :first_name, :last_name, :user_type, :phone, :date_of_birth, :emergency_contact, :emergency_phone, :email_verified)
            ");
            $stmt->execute($patientData);
            $patientId = $this->connection->lastInsertId();
            
            // Create demo caregiver
            $caregiverData = [
                'username' => 'caregiver_demo',
                'email' => 'caregiver@demo.myeline.com',
                'password_hash' => password_hash('Caregiver123!', PASSWORD_DEFAULT),
                'first_name' => 'John',
                'last_name' => 'Doe',
                'user_type' => 'caregiver',
                'phone' => '306-555-0124',
                'email_verified' => 1
            ];
            
            $stmt->execute($caregiverData);
            $caregiverId = $this->connection->lastInsertId();
            
            // Create caregiver relationship
            $relationshipData = [
                'patient_id' => $patientId,
                'caregiver_id' => $caregiverId,
                'relationship_type' => 'family',
                'access_level' => 'high',
                'status' => 'active',
                'accepted_at' => date('Y-m-d H:i:s')
            ];
            
            $stmt = $this->connection->prepare("
                INSERT INTO caregiver_relationships (patient_id, caregiver_id, relationship_type, access_level, status, accepted_at)
                VALUES (:patient_id, :caregiver_id, :relationship_type, :access_level, :status, :accepted_at)
            ");
            $stmt->execute($relationshipData);
            
            // Insert sample daily content
            $this->insertSampleDailyContent();
            
            // Insert sample medications
            $this->insertSampleMedications($patientId);
            
            // Insert sample health data
            $this->insertSampleHealthData($patientId);
            
            $this->log("Demo data inserted successfully", 'success');
            $this->log("Demo Patient - Username: patient_demo, Password: Patient123!", 'info');
            $this->log("Demo Caregiver - Username: caregiver_demo, Password: Caregiver123!", 'info');
            
        } catch (PDOException $e) {
            throw new Exception("Failed to insert demo data: " . $e->getMessage());
        }
    }
    
    private function insertSampleDailyContent() {
        $content = [
            ['quote', 'Courage doesn\'t always roar. Sometimes courage is the quiet voice at the end of the day saying, "I will try again tomorrow."', 'Mary Anne Radmacher'],
            ['quote', 'You are braver than you believe, stronger than you seem, and smarter than you think.', 'A.A. Milne'],
            ['affirmation', 'I am strong, I am resilient, and I am healing every day.', null],
            ['affirmation', 'Today I choose to focus on what I can control and let go of what I cannot.', null],
            ['tip', 'Take a few minutes today to practice deep breathing. It can help reduce stress and improve your mood.', null],
            ['tip', 'Remember to stay hydrated! Aim for 8 glasses of water throughout the day.', null],
            ['joke', 'Why don\'t scientists trust atoms? Because they make up everything!', null],
            ['joke', 'What do you call a bear with no teeth? A gummy bear!', null]
        ];
        
        $stmt = $this->connection->prepare("
            INSERT INTO daily_content (content_type, content, author)
            VALUES (?, ?, ?)
        ");
        
        foreach ($content as $item) {
            $stmt->execute($item);
        }
    }
    
    private function insertSampleMedications($patientId) {
        $medications = [
            [
                'user_id' => $patientId,
                'name' => 'Ondansetron',
                'generic_name' => 'Zofran',
                'dosage' => '8mg',
                'form' => 'tablet',
                'frequency' => '3 times daily',
                'schedule_times' => json_encode(['08:00', '14:00', '20:00']),
                'start_date' => date('Y-m-d'),
                'prescriber' => 'Dr. Smith - Oncology',
                'instructions' => 'Take with or without food. For nausea prevention.'
            ],
            [
                'user_id' => $patientId,
                'name' => 'Morphine SR',
                'generic_name' => 'Morphine Sulfate',
                'dosage' => '15mg',
                'form' => 'tablet',
                'frequency' => '2 times daily',
                'schedule_times' => json_encode(['08:00', '20:00']),
                'start_date' => date('Y-m-d'),
                'prescriber' => 'Dr. Smith - Oncology',
                'instructions' => 'Take with food. Do not crush or chew.'
            ]
        ];
        
        $stmt = $this->connection->prepare("
            INSERT INTO medications (user_id, name, generic_name, dosage, form, frequency, schedule_times, start_date, prescriber, instructions)
            VALUES (:user_id, :name, :generic_name, :dosage, :form, :frequency, :schedule_times, :start_date, :prescriber, :instructions)
        ");
        
        foreach ($medications as $med) {
            $stmt->execute($med);
        }
    }
    
    private function insertSampleHealthData($patientId) {
        // Insert sample pain logs
        for ($i = 0; $i < 7; $i++) {
            $date = date('Y-m-d H:i:s', strtotime("-$i days"));
            $painLevel = rand(3, 7);
            
            $this->connection->prepare("
                INSERT INTO pain_logs (user_id, pain_level, pain_type, description, logged_at)
                VALUES (?, ?, 'dull', 'General discomfort', ?)
            ")->execute([$patientId, $painLevel, $date]);
        }
        
        // Insert sample mood logs
        for ($i = 0; $i < 7; $i++) {
            $date = date('Y-m-d H:i:s', strtotime("-$i days"));
            $moodScore = rand(5, 8);
            $energyLevel = rand(4, 7);
            
            $this->connection->prepare("
                INSERT INTO mood_logs (user_id, mood_score, energy_level, logged_at)
                VALUES (?, ?, ?, ?)
            ")->execute([$patientId, $moodScore, $energyLevel, $date]);
        }
        
        // Insert sample hydration logs
        $today = date('Y-m-d');
        $amounts = [250, 500, 250, 300, 200, 250, 400, 300];
        
        foreach ($amounts as $i => $amount) {
            $time = sprintf('%02d:00:00', 8 + ($i * 2));
            $datetime = $today . ' ' . $time;
            
            $this->connection->prepare("
                INSERT INTO hydration_logs (user_id, amount_ml, logged_at)
                VALUES (?, ?, ?)
            ")->execute([$patientId, $amount, $datetime]);
        }
    }
    
    private function verifyInstallation() {
        try {
            // Check if tables exist
            $tables = [
                'users', 'user_sessions', 'caregiver_relationships', 'symptoms', 
                'pain_logs', 'mood_logs', 'vitals', 'hydration_logs', 'medications',
                'medication_logs', 'appointments', 'conversations', 'messages',
                'daily_content', 'photos', 'goals', 'ai_insights', 'notifications', 'settings'
            ];
            
            $missingTables = [];
            foreach ($tables as $table) {
                $stmt = $this->connection->prepare("SHOW TABLES LIKE ?");
                $stmt->execute([$table]);
                
                if ($stmt->rowCount() === 0) {
                    $missingTables[] = $table;
                }
            }
            
            if (!empty($missingTables)) {
                throw new Exception("Missing tables: " . implode(', ', $missingTables));
            }
            
            // Check if admin user exists
            $stmt = $this->connection->prepare("SELECT COUNT(*) as count FROM users WHERE user_type = 'admin'");
            $stmt->execute();
            $result = $stmt->fetch();
            
            if ($result['count'] == 0) {
                throw new Exception("No admin user found");
            }
            
            // Check if settings exist
            $stmt = $this->connection->prepare("SELECT COUNT(*) as count FROM settings");
            $stmt->execute();
            $result = $stmt->fetch();
            
            if ($result['count'] == 0) {
                throw new Exception("No system settings found");
            }
            
            $this->log("Installation verification passed", 'success');
            
        } catch (PDOException $e) {
            throw new Exception("Verification failed: " . $e->getMessage());
        }
    }
    
    public function checkSystemHealth() {
        $checks = [];
        
        try {
            // Database connection
            $this->connectToDatabase();
            $checks['database_connection'] = ['status' => 'OK', 'message' => 'Database connection successful'];
            
            // Check table structure
            $stmt = $this->connection->query("SHOW TABLES");
            $tableCount = $stmt->rowCount();
            $checks['table_count'] = ['status' => 'OK', 'message' => "$tableCount tables found"];
            
            // Check admin user
            $stmt = $this->connection->prepare("SELECT COUNT(*) as count FROM users WHERE user_type = 'admin'");
            $stmt->execute();
            $adminCount = $stmt->fetch()['count'];
            $checks['admin_user'] = [
                'status' => $adminCount > 0 ? 'OK' : 'ERROR', 
                'message' => "$adminCount admin user(s) found"
            ];
            
            // Check file permissions
            $uploadDir = __DIR__ . '/../uploads/';
            $checks['upload_directory'] = [
                'status' => is_writable($uploadDir) ? 'OK' : 'WARNING',
                'message' => is_writable($uploadDir) ? 'Upload directory is writable' : 'Upload directory not writable'
            ];
            
            // Check PHP extensions
            $requiredExtensions = ['pdo', 'pdo_mysql', 'json', 'mbstring', 'openssl'];
            $missingExtensions = [];
            
            foreach ($requiredExtensions as $ext) {
                if (!extension_loaded($ext)) {
                    $missingExtensions[] = $ext;
                }
            }
            
            $checks['php_extensions'] = [
                'status' => empty($missingExtensions) ? 'OK' : 'ERROR',
                'message' => empty($missingExtensions) ? 'All required PHP extensions loaded' : 'Missing extensions: ' . implode(', ', $missingExtensions)
            ];
            
        } catch (Exception $e) {
            $checks['database_connection'] = ['status' => 'ERROR', 'message' => $e->getMessage()];
        }
        
        return $checks;
    }
    
    public function resetDatabase() {
        try {
            $this->log("Resetting database...", 'warning');
            
            $this->connectToDatabase();
            
            // Drop all tables
            $this->connection->exec("SET FOREIGN_KEY_CHECKS = 0");
            
            $stmt = $this->connection->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            foreach ($tables as $table) {
                $this->connection->exec("DROP TABLE IF EXISTS `$table`");
            }
            
            $this->connection->exec("SET FOREIGN_KEY_CHECKS = 1");
            
            $this->log("Database reset complete", 'success');
            return true;
            
        } catch (Exception $e) {
            $this->log("Database reset failed: " . $e->getMessage(), 'error');
            return false;
        }
    }
    
    private function log($message, $type = 'info') {
        $timestamp = date('Y-m-d H:i:s');
        $this->output[] = [
            'timestamp' => $timestamp,
            'type' => $type,
            'message' => $message
        ];
        
        // Also output to console if running from CLI
        if (php_sapi_name() === 'cli') {
            $colors = [
                'info' => "\033[0;36m",    // Cyan
                'success' => "\033[0;32m", // Green
                'warning' => "\033[1;33m", // Yellow
                'error' => "\033[0;31m",   // Red
                'reset' => "\033[0m"       // Reset
            ];
            
            $color = $colors[$type] ?? $colors['info'];
            echo $color . "[$timestamp] " . strtoupper($type) . ": $message" . $colors['reset'] . "\n";
        }
    }
    
    public function getOutput() {
        return $this->output;
    }
}

// If running from command line
if (php_sapi_name() === 'cli') {
    $installer = new DatabaseInstaller();
    
    $command = $argv[1] ?? 'install';
    $includeDemoData = in_array('--demo', $argv);
    
    switch ($command) {
        case 'install':
            $installer->install($includeDemoData);
            break;
        
        case 'reset':
            $installer->resetDatabase();
            break;
        
        case 'health':
            $checks = $installer->checkSystemHealth();
            foreach ($checks as $check => $result) {
                echo "[$check] {$result['status']}: {$result['message']}\n";
            }
            break;
        
        default:
            echo "Usage: php install.php [install|reset|health] [--demo]\n";
            echo "  install   - Install database schema and default data\n";
            echo "  reset     - Reset database (WARNING: Deletes all data)\n";
            echo "  health    - Check system health\n";
            echo "  --demo    - Include demo data when installing\n";
            break;
    }
}
?>
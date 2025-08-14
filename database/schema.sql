-- Myeline Cancer Care Hub Database Schema
-- Compatible with MySQL 5.7+ and MariaDB 10.2+

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS `myeline_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `myeline_db`;

-- =====================================================
-- USERS AND AUTHENTICATION TABLES
-- =====================================================

-- Users table
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `user_type` enum('patient','caregiver','admin') NOT NULL DEFAULT 'patient',
  `phone` varchar(20) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `emergency_contact` varchar(100) DEFAULT NULL,
  `emergency_phone` varchar(20) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `timezone` varchar(50) DEFAULT 'America/Regina',
  `language` varchar(10) DEFAULT 'en',
  `theme_preference` enum('light','dark','high-contrast') DEFAULT 'light',
  `font_preference` enum('default','dyslexia') DEFAULT 'default',
  `email_verified` tinyint(1) DEFAULT 0,
  `two_factor_enabled` tinyint(1) DEFAULT 0,
  `two_factor_secret` varchar(255) DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `login_attempts` int(11) DEFAULT 0,
  `locked_until` timestamp NULL DEFAULT NULL,
  `privacy_level` enum('low','medium','high') DEFAULT 'medium',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `user_type` (`user_type`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User sessions table
CREATE TABLE `user_sessions` (
  `id` varchar(128) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text,
  `last_activity` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `last_activity` (`last_activity`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Password reset tokens
CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` timestamp NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `token` (`token`),
  KEY `expires_at` (`expires_at`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- PATIENT-CAREGIVER RELATIONSHIPS
-- =====================================================

-- Caregiver relationships
CREATE TABLE `caregiver_relationships` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `caregiver_id` int(11) NOT NULL,
  `relationship_type` enum('family','friend','professional','other') NOT NULL,
  `access_level` enum('low','medium','high') NOT NULL DEFAULT 'medium',
  `permissions` json DEFAULT NULL,
  `status` enum('pending','active','inactive','revoked') DEFAULT 'pending',
  `invited_by` int(11) DEFAULT NULL,
  `accepted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `patient_caregiver` (`patient_id`,`caregiver_id`),
  KEY `caregiver_id` (`caregiver_id`),
  KEY `status` (`status`),
  FOREIGN KEY (`patient_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`caregiver_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- HEALTH DATA TABLES
-- =====================================================

-- Symptoms tracking
CREATE TABLE `symptoms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `symptom_type` varchar(100) NOT NULL,
  `severity` int(11) NOT NULL CHECK (`severity` >= 1 AND `severity` <= 10),
  `description` text DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `duration_minutes` int(11) DEFAULT NULL,
  `triggers` json DEFAULT NULL,
  `medications_taken` json DEFAULT NULL,
  `logged_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `symptom_type` (`symptom_type`),
  KEY `logged_at` (`logged_at`),
  KEY `severity` (`severity`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pain tracking with body map
CREATE TABLE `pain_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `pain_level` int(11) NOT NULL CHECK (`pain_level` >= 0 AND `pain_level` <= 10),
  `pain_type` enum('sharp','dull','burning','throbbing','cramping','shooting','other') DEFAULT NULL,
  `body_locations` json DEFAULT NULL, -- Store body map coordinates and regions
  `description` text DEFAULT NULL,
  `triggers` text DEFAULT NULL,
  `relief_methods` text DEFAULT NULL,
  `medication_effectiveness` int(11) DEFAULT NULL CHECK (`medication_effectiveness` >= 1 AND `medication_effectiveness` <= 10),
  `logged_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `pain_level` (`pain_level`),
  KEY `logged_at` (`logged_at`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Mood tracking
CREATE TABLE `mood_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `mood_score` int(11) NOT NULL CHECK (`mood_score` >= 1 AND `mood_score` <= 10),
  `mood_type` enum('happy','sad','anxious','angry','frustrated','hopeful','peaceful','energetic','tired','other') DEFAULT NULL,
  `energy_level` int(11) DEFAULT NULL CHECK (`energy_level` >= 1 AND `energy_level` <= 10),
  `anxiety_level` int(11) DEFAULT NULL CHECK (`anxiety_level` >= 1 AND `anxiety_level` <= 10),
  `sleep_quality` int(11) DEFAULT NULL CHECK (`sleep_quality` >= 1 AND `sleep_quality` <= 10),
  `notes` text DEFAULT NULL,
  `activities` json DEFAULT NULL,
  `logged_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `mood_score` (`mood_score`),
  KEY `logged_at` (`logged_at`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Vital signs
CREATE TABLE `vitals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `temperature` decimal(4,1) DEFAULT NULL, -- Celsius
  `blood_pressure_systolic` int(11) DEFAULT NULL,
  `blood_pressure_diastolic` int(11) DEFAULT NULL,
  `heart_rate` int(11) DEFAULT NULL,
  `respiratory_rate` int(11) DEFAULT NULL,
  `oxygen_saturation` decimal(5,2) DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL, -- kg
  `height` decimal(5,2) DEFAULT NULL, -- cm
  `blood_glucose` decimal(5,1) DEFAULT NULL, -- mmol/L
  `notes` text DEFAULT NULL,
  `measured_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `measured_at` (`measured_at`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Hydration tracking
CREATE TABLE `hydration_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `amount_ml` int(11) NOT NULL,
  `liquid_type` enum('water','juice','tea','coffee','soup','other') DEFAULT 'water',
  `logged_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `logged_at` (`logged_at`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- MEDICATION MANAGEMENT
-- =====================================================

-- Medications
CREATE TABLE `medications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `generic_name` varchar(100) DEFAULT NULL,
  `dosage` varchar(50) NOT NULL,
  `form` enum('tablet','capsule','liquid','injection','cream','patch','inhaler','other') DEFAULT 'tablet',
  `frequency` varchar(100) NOT NULL, -- e.g., "2 times daily", "every 6 hours"
  `schedule_times` json DEFAULT NULL, -- Array of time strings
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `prescriber` varchar(100) DEFAULT NULL,
  `pharmacy` varchar(100) DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `side_effects` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `is_active` (`is_active`),
  KEY `start_date` (`start_date`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Medication adherence log
CREATE TABLE `medication_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `medication_id` int(11) NOT NULL,
  `scheduled_time` timestamp NOT NULL,
  `taken_time` timestamp NULL DEFAULT NULL,
  `status` enum('scheduled','taken','skipped','late') DEFAULT 'scheduled',
  `dosage_taken` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `side_effects` text DEFAULT NULL,
  `effectiveness` int(11) DEFAULT NULL CHECK (`effectiveness` >= 1 AND `effectiveness` <= 10),
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `medication_id` (`medication_id`),
  KEY `scheduled_time` (`scheduled_time`),
  KEY `status` (`status`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`medication_id`) REFERENCES `medications`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- APPOINTMENTS AND HEALTHCARE TEAM
-- =====================================================

-- Healthcare providers
CREATE TABLE `healthcare_providers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `speciality` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Appointments
CREATE TABLE `appointments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `provider_id` int(11) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `appointment_date` datetime NOT NULL,
  `duration_minutes` int(11) DEFAULT 60,
  `location` varchar(200) DEFAULT NULL,
  `appointment_type` enum('in-person','telehealth','phone','other') DEFAULT 'in-person',
  `telehealth_link` varchar(500) DEFAULT NULL,
  `status` enum('scheduled','confirmed','completed','cancelled','no-show') DEFAULT 'scheduled',
  `preparation_notes` text DEFAULT NULL,
  `follow_up_notes` text DEFAULT NULL,
  `reminder_sent` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `provider_id` (`provider_id`),
  KEY `appointment_date` (`appointment_date`),
  KEY `status` (`status`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`provider_id`) REFERENCES `healthcare_providers`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- MESSAGING AND COMMUNICATION
-- =====================================================

-- Conversations
CREATE TABLE `conversations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('patient-caregiver','support','emergency') DEFAULT 'patient-caregiver',
  `title` varchar(200) DEFAULT NULL,
  `participants` json NOT NULL, -- Array of user IDs
  `last_message_at` timestamp NULL DEFAULT NULL,
  `is_archived` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `last_message_at` (`last_message_at`),
  KEY `is_archived` (`is_archived`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Messages
CREATE TABLE `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `conversation_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `message_type` enum('text','image','file','system','emergency') DEFAULT 'text',
  `content` text NOT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `is_urgent` tinyint(1) DEFAULT 0,
  `read_by` json DEFAULT NULL, -- Array of user IDs who have read the message
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `conversation_id` (`conversation_id`),
  KEY `sender_id` (`sender_id`),
  KEY `created_at` (`created_at`),
  KEY `is_urgent` (`is_urgent`),
  FOREIGN KEY (`conversation_id`) REFERENCES `conversations`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`sender_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- COMFORT AND WELLNESS FEATURES
-- =====================================================

-- Daily quotes and affirmations
CREATE TABLE `daily_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_type` enum('quote','affirmation','tip','joke') NOT NULL,
  `content` text NOT NULL,
  `author` varchar(100) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `content_type` (`content_type`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Photo sharing and memory vault
CREATE TABLE `photos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `shared_with` json DEFAULT NULL, -- Array of user IDs
  `title` varchar(200) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_size` int(11) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `taken_at` timestamp NULL DEFAULT NULL,
  `is_private` tinyint(1) DEFAULT 0,
  `tags` json DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `taken_at` (`taken_at`),
  KEY `is_private` (`is_private`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Goals and milestones
CREATE TABLE `goals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `goal_type` enum('daily','weekly','monthly','milestone') DEFAULT 'daily',
  `target_value` decimal(10,2) DEFAULT NULL,
  `current_value` decimal(10,2) DEFAULT 0,
  `unit` varchar(50) DEFAULT NULL,
  `target_date` date DEFAULT NULL,
  `status` enum('active','completed','paused','cancelled') DEFAULT 'active',
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `goal_type` (`goal_type`),
  KEY `status` (`status`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- AI ASSISTANT AND INSIGHTS
-- =====================================================

-- AI insights and recommendations
CREATE TABLE `ai_insights` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `insight_type` enum('trend','recommendation','alert','correlation') NOT NULL,
  `title` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `data_sources` json DEFAULT NULL, -- What data was used to generate this insight
  `confidence_score` decimal(3,2) DEFAULT NULL, -- 0.00 to 1.00
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `is_read` tinyint(1) DEFAULT 0,
  `is_dismissed` tinyint(1) DEFAULT 0,
  `action_taken` text DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `insight_type` (`insight_type`),
  KEY `priority` (`priority`),
  KEY `is_read` (`is_read`),
  KEY `expires_at` (`expires_at`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- AI conversation history
CREATE TABLE `ai_conversations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `session_id` varchar(100) NOT NULL,
  `user_message` text NOT NULL,
  `ai_response` text NOT NULL,
  `context` json DEFAULT NULL, -- Additional context used for the response
  `feedback` enum('helpful','not-helpful','incorrect') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `session_id` (`session_id`),
  KEY `created_at` (`created_at`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- SYSTEM AND AUDIT TABLES
-- =====================================================

-- Activity log for audit trail
CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `additional_data` json DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `action` (`action`),
  KEY `created_at` (`created_at`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- System notifications
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` enum('medication','appointment','message','insight','emergency','system') NOT NULL,
  `title` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `action_url` varchar(500) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `type` (`type`),
  KEY `is_read` (`is_read`),
  KEY `sent_at` (`sent_at`),
  KEY `expires_at` (`expires_at`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Application settings
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_system` tinyint(1) DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- INDEXES FOR PERFORMANCE
-- =====================================================

-- Composite indexes for common queries
ALTER TABLE `symptoms` ADD INDEX `user_symptom_date` (`user_id`, `symptom_type`, `logged_at`);
ALTER TABLE `pain_logs` ADD INDEX `user_pain_date` (`user_id`, `pain_level`, `logged_at`);
ALTER TABLE `mood_logs` ADD INDEX `user_mood_date` (`user_id`, `mood_score`, `logged_at`);
ALTER TABLE `medication_logs` ADD INDEX `user_med_schedule` (`user_id`, `medication_id`, `scheduled_time`);
ALTER TABLE `appointments` ADD INDEX `user_date_status` (`user_id`, `appointment_date`, `status`);

-- =====================================================
-- VIEWS FOR COMMON QUERIES
-- =====================================================

-- Recent health summary view
CREATE VIEW `recent_health_summary` AS
SELECT 
    u.id as user_id,
    u.first_name,
    u.last_name,
    (SELECT AVG(pain_level) FROM pain_logs WHERE user_id = u.id AND logged_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)) as avg_pain_7d,
    (SELECT AVG(mood_score) FROM mood_logs WHERE user_id = u.id AND logged_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)) as avg_mood_7d,
    (SELECT COUNT(*) FROM symptoms WHERE user_id = u.id AND logged_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)) as symptom_count_7d,
    (SELECT SUM(amount_ml) FROM hydration_logs WHERE user_id = u.id AND DATE(logged_at) = CURDATE()) as hydration_today
FROM users u 
WHERE u.user_type = 'patient' AND u.deleted_at IS NULL;

-- Medication adherence view
CREATE VIEW `medication_adherence` AS
SELECT 
    m.user_id,
    m.id as medication_id,
    m.name as medication_name,
    COUNT(ml.id) as total_scheduled,
    COUNT(CASE WHEN ml.status = 'taken' THEN 1 END) as taken,
    COUNT(CASE WHEN ml.status = 'skipped' THEN 1 END) as skipped,
    ROUND((COUNT(CASE WHEN ml.status = 'taken' THEN 1 END) / COUNT(ml.id)) * 100, 2) as adherence_percentage
FROM medications m
LEFT JOIN medication_logs ml ON m.id = ml.medication_id 
    AND ml.scheduled_time >= DATE_SUB(NOW(), INTERVAL 30 DAY)
WHERE m.is_active = 1
GROUP BY m.user_id, m.id;

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;
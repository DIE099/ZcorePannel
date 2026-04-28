-- =====================================================
-- ZCore Panel - MySQL Database Schema
-- Version: 2.0
-- =====================================================

-- Create Database
CREATE DATABASE IF NOT EXISTS `zcore_panel`;
USE `zcore_panel`;

-- =====================================================
-- Table: users (Admin & Users)
-- =====================================================
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `email` VARCHAR(100) DEFAULT NULL,
    `role` ENUM('admin', 'user', 'viewer') DEFAULT 'user',
    `status` ENUM('active', 'banned', 'pending') DEFAULT 'active',
    `created_by` VARCHAR(50) DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `last_login` DATETIME DEFAULT NULL,
    `last_ip` VARCHAR(45) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: licenses (License Keys)
-- =====================================================
CREATE TABLE IF NOT EXISTS `licenses` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `license_key` VARCHAR(50) NOT NULL UNIQUE,
    `package_name` VARCHAR(255) NOT NULL,
    `created_by` VARCHAR(50) NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `expiry_days` INT(11) DEFAULT 30,
    `expires_at` DATETIME DEFAULT NULL,
    `status` ENUM('active', 'expired', 'revoked', 'suspended') DEFAULT 'active',
    `note` TEXT DEFAULT NULL,
    `max_devices` INT(11) DEFAULT 3,
    `last_used` DATETIME DEFAULT NULL,
    `last_ip` VARCHAR(45) DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_license_key` (`license_key`),
    INDEX `idx_status` (`status`),
    INDEX `idx_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: devices (Registered Devices)
-- =====================================================
CREATE TABLE IF NOT EXISTS `devices` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `license_id` INT(11) NOT NULL,
    `device_id` VARCHAR(255) NOT NULL,
    `device_name` VARCHAR(255) DEFAULT NULL,
    `device_model` VARCHAR(255) DEFAULT NULL,
    `android_version` VARCHAR(50) DEFAULT NULL,
    `first_seen` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `last_seen` DATETIME DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `status` ENUM('active', 'blocked') DEFAULT 'active',
    PRIMARY KEY (`id`),
    FOREIGN KEY (`license_id`) REFERENCES `licenses`(`id`) ON DELETE CASCADE,
    INDEX `idx_device_id` (`device_id`),
    UNIQUE KEY `unique_device_license` (`license_id`, `device_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: logs (Activity Logs)
-- =====================================================
CREATE TABLE IF NOT EXISTS `logs` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `action` VARCHAR(100) NOT NULL,
    `username` VARCHAR(50) DEFAULT NULL,
    `license_key` VARCHAR(50) DEFAULT NULL,
    `details` TEXT DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` TEXT DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_action` (`action`),
    INDEX `idx_username` (`username`),
    INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: settings (Panel Settings)
-- =====================================================
CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `setting_key` VARCHAR(100) NOT NULL UNIQUE,
    `setting_value` TEXT DEFAULT NULL,
    `setting_type` ENUM('text', 'number', 'boolean', 'json') DEFAULT 'text',
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: registration_codes (Referral/Registration Codes)
-- =====================================================
CREATE TABLE IF NOT EXISTS `registration_codes` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(50) NOT NULL UNIQUE,
    `created_by` VARCHAR(50) DEFAULT NULL,
    `max_uses` INT(11) DEFAULT 1,
    `used_count` INT(11) DEFAULT 0,
    `expires_at` DATETIME DEFAULT NULL,
    `status` ENUM('active', 'used', 'expired') DEFAULT 'active',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: blacklist (Blocked IPs or Devices)
-- =====================================================
CREATE TABLE IF NOT EXISTS `blacklist` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `type` ENUM('ip', 'device', 'user') DEFAULT 'ip',
    `value` VARCHAR(255) NOT NULL,
    `reason` TEXT DEFAULT NULL,
    `created_by` VARCHAR(50) DEFAULT NULL,
    `expires_at` DATETIME DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_type_value` (`type`, `value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- INSERT DEFAULT DATA
-- =====================================================

-- Default Admin User (password: admin123)
INSERT INTO `users` (`username`, `password`, `email`, `role`, `status`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@zcore.com', 'admin', 'active');

-- Default Registration Codes
INSERT INTO `registration_codes` (`code`, `created_by`, `max_uses`) VALUES
('ZENIN2024', 'admin', 100),
('BLACKBOX', 'admin', 50),
('ZCORE', 'admin', 10);

-- Default Settings
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_type`) VALUES
('panel_name', 'ZCore Panel', 'text'),
('panel_version', '2.0', 'text'),
('default_expiry_days', '30', 'number'),
('max_devices_per_license', '3', 'number'),
('enable_registration', 'true', 'boolean'),
('smtp_host', '', 'text'),
('smtp_port', '587', 'number'),
('smtp_user', '', 'text'),
('smtp_pass', '', 'text');

-- =====================================================
-- CREATE VIEWS
-- =====================================================

-- View: Active Licenses Summary
CREATE OR REPLACE VIEW `view_active_licenses` AS
SELECT 
    COUNT(*) as total_active,
    SUM(CASE WHEN status = 'active' AND expires_at > NOW() THEN 1 ELSE 0 END) as valid_active,
    SUM(CASE WHEN status = 'active' AND expires_at <= NOW() THEN 1 ELSE 0 END) as expired_active
FROM licenses;

-- View: License Usage Stats
CREATE OR REPLACE VIEW `view_license_stats` AS
SELECT 
    l.license_key,
    l.package_name,
    l.status,
    l.expires_at,
    COUNT(d.id) as device_count,
    MAX(d.last_seen) as last_activity
FROM licenses l
LEFT JOIN devices d ON l.id = d.license_id
GROUP BY l.id;

-- =====================================================
-- CREATE PROCEDURES
-- =====================================================

-- Procedure: Clean Expired Logs (Run monthly)
DELIMITER $$
CREATE PROCEDURE `clean_expired_logs`()
BEGIN
    DELETE FROM logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY);
    DELETE FROM devices WHERE last_seen < DATE_SUB(NOW(), INTERVAL 90 DAY);
END$$
DELIMITER ;

-- Procedure: Auto Expire Licenses
DELIMITER $$
CREATE PROCEDURE `auto_expire_licenses`()
BEGIN
    UPDATE licenses SET status = 'expired' 
    WHERE status = 'active' AND expires_at < NOW();
END$$
DELIMITER ;

-- =====================================================
-- CREATE TRIGGERS
-- =====================================================

-- Trigger: Log License Creation
DELIMITER $$
CREATE TRIGGER `log_license_creation`
AFTER INSERT ON `licenses`
FOR EACH ROW
BEGIN
    INSERT INTO logs (action, username, license_key, details)
    VALUES ('CREATE_LICENSE', NEW.created_by, NEW.license_key, CONCAT('Package: ', NEW.package_name));
END$$
DELIMITER ;

-- Trigger: Log License Status Change
DELIMITER $$
CREATE TRIGGER `log_license_status_change`
AFTER UPDATE ON `licenses`
FOR EACH ROW
BEGIN
    IF OLD.status != NEW.status THEN
        INSERT INTO logs (action, username, license_key, details)
        VALUES ('CHANGE_STATUS', NEW.created_by, NEW.license_key, CONCAT('Status changed from ', OLD.status, ' to ', NEW.status));
    END IF;
END$$
DELIMITER ;

-- =====================================================
-- INDEXES FOR PERFORMANCE
-- =====================================================

CREATE INDEX idx_license_key_status ON licenses(license_key, status);
CREATE INDEX idx_devices_license ON devices(license_id);
CREATE INDEX idx_logs_created ON logs(created_at DESC);
CREATE INDEX idx_users_role ON users(role);

-- =====================================================
-- DONE
-- =====================================================
SELECT '✅ ZCore Panel Database Created Successfully!' as message;
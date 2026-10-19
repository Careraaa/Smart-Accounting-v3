-- ============================================================================
--  Make test1 match the target table structure.
--
--  Database: if0_42738871_smart_accounting_v3_test
--  Target:   the structure test2 has, which is what the code expects.
--
--  KEEPS EVERY ROW. It can create a missing table, add a missing column,
--  add a missing primary key, and add a missing foreign key.
--  It never drops anything.
--
-- WARNING: BACK UP FIRST. Export test1 from phpMyAdmin and keep the file.
--
-- NOTE: columns test1 has that the target does not are left in place.
--      Keeping them is safer than dropping them and losing their data.
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Create any table that is missing (foreign keys come later) ----------
--
SET @has_tbl_account_mappings = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'account_mappings');

SET @sql = IF(@has_tbl_account_mappings = 0,
  'CREATE TABLE IF NOT EXISTS `account_mappings` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `mapping_key` varchar(50) NOT NULL,
    `label` varchar(255) NOT NULL,
    `account_id` bigint(20) unsigned NOT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `account_mappings_mapping_key_unique` (`mapping_key`),
    KEY `account_mappings_account_id_foreign` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_accounting_audit_logs = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'accounting_audit_logs');

SET @sql = IF(@has_tbl_accounting_audit_logs = 0,
  'CREATE TABLE IF NOT EXISTS `accounting_audit_logs` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` bigint(20) unsigned NOT NULL,
    `action` varchar(50) NOT NULL,
    `module` varchar(50) NOT NULL,
    `record_id` bigint(20) unsigned DEFAULT NULL,
    `description` text DEFAULT NULL,
    `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
    `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
    `ip_address` varchar(45) DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `accounting_audit_logs_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_allowances = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'allowances');

SET @sql = IF(@has_tbl_allowances = 0,
  'CREATE TABLE IF NOT EXISTS `allowances` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` bigint(20) unsigned NOT NULL,
    `payroll_id` bigint(20) unsigned DEFAULT NULL,
    `allowance_type` varchar(255) NOT NULL,
    `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
    `effective_date` date DEFAULT NULL,
    `status` varchar(255) NOT NULL DEFAULT \'active\',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `allowances_user_id_foreign` (`user_id`),
    KEY `allowances_payroll_id_foreign` (`payroll_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_attendance = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'attendance');

SET @sql = IF(@has_tbl_attendance = 0,
  'CREATE TABLE IF NOT EXISTS `attendance` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` bigint(20) unsigned NOT NULL,
    `date` date NOT NULL,
    `time_in` time DEFAULT NULL,
    `time_out` time DEFAULT NULL,
    `status` enum(\'present\',\'late\',\'absent\',\'on leave\') DEFAULT NULL,
    `is_manual` tinyint(1) NOT NULL DEFAULT 0,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `attendance_user_id_date_unique` (`user_id`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_attendance_logs = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'attendance_logs');

SET @sql = IF(@has_tbl_attendance_logs = 0,
  'CREATE TABLE IF NOT EXISTS `attendance_logs` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` bigint(20) unsigned NOT NULL,
    `type` enum(\'time_in\',\'time_out\') NOT NULL,
    `logged_at` datetime NOT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `attendance_logs_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_attendance_tokens = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'attendance_tokens');

SET @sql = IF(@has_tbl_attendance_tokens = 0,
  'CREATE TABLE IF NOT EXISTS `attendance_tokens` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `token` varchar(255) NOT NULL,
    `used` tinyint(1) NOT NULL DEFAULT 0,
    `user_id` bigint(20) unsigned DEFAULT NULL,
    `expires_at` timestamp NULL DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `attendance_tokens_token_unique` (`token`),
    KEY `attendance_tokens_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_bonus_employee = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bonus_employee');

SET @sql = IF(@has_tbl_bonus_employee = 0,
  'CREATE TABLE IF NOT EXISTS `bonus_employee` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `bonus_id` bigint(20) unsigned NOT NULL,
    `user_id` bigint(20) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `bonus_employee_bonus_id_user_id_unique` (`bonus_id`,`user_id`),
    KEY `bonus_employee_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_bonuses = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bonuses');

SET @sql = IF(@has_tbl_bonuses = 0,
  'CREATE TABLE IF NOT EXISTS `bonuses` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `description` text DEFAULT NULL,
    `type` varchar(255) NOT NULL,
    `computation_method` varchar(255) NOT NULL,
    `formula` text DEFAULT NULL,
    `fixed_amount` decimal(12,2) DEFAULT NULL,
    `percentage_value` decimal(8,4) DEFAULT NULL,
    `is_mandatory` tinyint(1) NOT NULL DEFAULT 0,
    `is_system_generated` tinyint(1) NOT NULL DEFAULT 0,
    `status` varchar(255) NOT NULL DEFAULT \'active\',
    `year` smallint(5) unsigned DEFAULT NULL,
    `payroll_period` varchar(255) DEFAULT NULL,
    `code` varchar(255) DEFAULT NULL,
    `created_by` bigint(20) unsigned DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `bonuses_code_unique` (`code`),
    KEY `bonuses_created_by_foreign` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_boundary_payments = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'boundary_payments');

SET @sql = IF(@has_tbl_boundary_payments = 0,
  'CREATE TABLE IF NOT EXISTS `boundary_payments` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `daily_remittance_id` bigint(20) unsigned NOT NULL,
    `source_payment_key` varchar(64) NOT NULL,
    `initial_payment_remittance_id` bigint(20) unsigned DEFAULT NULL,
    `payment_date` date NOT NULL,
    `amount` decimal(15,2) NOT NULL,
    `paid_by` varchar(16) DEFAULT NULL,
    `payment_kind` varchar(16) NOT NULL,
    `status` varchar(16) NOT NULL DEFAULT \'Posted\',
    `recorded_by` bigint(20) unsigned NOT NULL,
    `approved_by` bigint(20) unsigned DEFAULT NULL,
    `journal_entry_id` bigint(20) unsigned DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `boundary_payments_source_payment_key_unique` (`source_payment_key`),
    UNIQUE KEY `boundary_payments_initial_payment_remittance_id_unique` (`initial_payment_remittance_id`),
    UNIQUE KEY `boundary_payments_journal_entry_id_unique` (`journal_entry_id`),
    KEY `boundary_payments_recorded_by_foreign` (`recorded_by`),
    KEY `boundary_payments_approved_by_foreign` (`approved_by`),
    KEY `boundary_payments_daily_remittance_id_payment_date_index` (`daily_remittance_id`,`payment_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_cache = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cache');

SET @sql = IF(@has_tbl_cache = 0,
  'CREATE TABLE IF NOT EXISTS `cache` (
    `key` varchar(255) NOT NULL,
    `value` mediumtext NOT NULL,
    `expiration` int(11) NOT NULL,
    PRIMARY KEY (`key`),
    KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_cache_locks = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cache_locks');

SET @sql = IF(@has_tbl_cache_locks = 0,
  'CREATE TABLE IF NOT EXISTS `cache_locks` (
    `key` varchar(255) NOT NULL,
    `owner` varchar(255) NOT NULL,
    `expiration` int(11) NOT NULL,
    PRIMARY KEY (`key`),
    KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_cash_advances = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cash_advances');

SET @sql = IF(@has_tbl_cash_advances = 0,
  'CREATE TABLE IF NOT EXISTS `cash_advances` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` bigint(20) unsigned NOT NULL,
    `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
    `repayment_months` int(11) NOT NULL DEFAULT 1,
    `monthly_deduction` decimal(12,2) NOT NULL DEFAULT 0.00,
    `amount_deducted` decimal(12,2) NOT NULL DEFAULT 0.00,
    `request_date` date DEFAULT NULL,
    `approval_date` date DEFAULT NULL,
    `status` varchar(255) NOT NULL DEFAULT \'pending\',
    `approved_by` bigint(20) unsigned DEFAULT NULL,
    `approved_at` timestamp NULL DEFAULT NULL,
    `rejection_reason` text DEFAULT NULL,
    `deducted_payroll_id` bigint(20) unsigned DEFAULT NULL,
    `notes` text DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `cash_advances_user_id_foreign` (`user_id`),
    KEY `cash_advances_approved_by_foreign` (`approved_by`),
    KEY `cash_advances_deducted_payroll_id_foreign` (`deducted_payroll_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_chart_of_accounts = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'chart_of_accounts');

SET @sql = IF(@has_tbl_chart_of_accounts = 0,
  'CREATE TABLE IF NOT EXISTS `chart_of_accounts` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `account_code` varchar(20) NOT NULL,
    `account_name` varchar(255) NOT NULL,
    `account_type` enum(\'Asset\',\'Liability\',\'Equity\',\'Revenue\',\'Expense\') NOT NULL,
    `description` text DEFAULT NULL,
    `is_active` tinyint(1) NOT NULL DEFAULT 1,
    `created_by` bigint(20) unsigned DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    `is_contra` tinyint(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `chart_of_accounts_account_code_unique` (`account_code`),
    KEY `chart_of_accounts_created_by_foreign` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_daily_remittances = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'daily_remittances');

SET @sql = IF(@has_tbl_daily_remittances = 0,
  'CREATE TABLE IF NOT EXISTS `daily_remittances` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `driver_id` bigint(20) unsigned NOT NULL,
    `pao_id` bigint(20) unsigned NOT NULL,
    `route_id` bigint(20) unsigned NOT NULL,
    `vehicle_id` bigint(20) unsigned NOT NULL,
    `remittance_date` date NOT NULL,
    `total_collection` decimal(10,2) NOT NULL DEFAULT 0.00,
    `diesel` decimal(10,2) DEFAULT NULL,
    `parking` decimal(10,2) DEFAULT NULL,
    `dispatcher` decimal(10,2) DEFAULT NULL,
    `food_allowance` decimal(10,2) DEFAULT NULL,
    `barker` decimal(10,2) DEFAULT NULL,
    `others` decimal(10,2) DEFAULT NULL,
    `total_expenses` decimal(10,2) NOT NULL DEFAULT 0.00,
    `boundary` decimal(10,2) DEFAULT NULL,
    `net_remittance` decimal(10,2) NOT NULL DEFAULT 0.00,
    `is_short_remittance` tinyint(1) NOT NULL DEFAULT 0,
    `short_amount` decimal(10,2) DEFAULT NULL,
    `driver_share` decimal(10,2) DEFAULT NULL,
    `driver_amount_paid` decimal(10,2) DEFAULT 0.00,
    `driver_status` enum(\'pending\',\'partial\',\'paid\') DEFAULT \'pending\',
    `pao_share` decimal(10,2) DEFAULT NULL,
    `pao_amount_paid` decimal(10,2) DEFAULT 0.00,
    `pao_status` enum(\'pending\',\'partial\',\'paid\') DEFAULT \'pending\',
    `resolution_notes` text DEFAULT NULL,
    `resolved_at` timestamp NULL DEFAULT NULL,
    `status` varchar(255) NOT NULL DEFAULT \'pending\',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `daily_remittances_driver_id_foreign` (`driver_id`),
    KEY `daily_remittances_pao_id_foreign` (`pao_id`),
    KEY `daily_remittances_route_id_foreign` (`route_id`),
    KEY `daily_remittances_vehicle_id_foreign` (`vehicle_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_deductions = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'deductions');

SET @sql = IF(@has_tbl_deductions = 0,
  'CREATE TABLE IF NOT EXISTS `deductions` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` bigint(20) unsigned NOT NULL,
    `payroll_id` bigint(20) unsigned DEFAULT NULL,
    `deduction_type` varchar(255) NOT NULL,
    `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
    `effective_date` date DEFAULT NULL,
    `status` varchar(255) NOT NULL DEFAULT \'active\',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `deductions_user_id_foreign` (`user_id`),
    KEY `deductions_payroll_id_foreign` (`payroll_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_departments = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'departments');

SET @sql = IF(@has_tbl_departments = 0,
  'CREATE TABLE IF NOT EXISTS `departments` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `is_active` tinyint(1) NOT NULL DEFAULT 1,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `departments_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_drivers = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'drivers');

SET @sql = IF(@has_tbl_drivers = 0,
  'CREATE TABLE IF NOT EXISTS `drivers` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `license_number` varchar(255) NOT NULL,
    `contact_number` varchar(255) NOT NULL,
    `gender` enum(\'male\',\'female\',\'prefer_not_to_say\') DEFAULT NULL,
    `email` varchar(255) DEFAULT NULL,
    `address` text DEFAULT NULL,
    `date_of_hire` date DEFAULT NULL,
    `status` varchar(255) NOT NULL DEFAULT \'active\',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `drivers_license_number_unique` (`license_number`),
    UNIQUE KEY `drivers_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_employee_attachments = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_attachments');

SET @sql = IF(@has_tbl_employee_attachments = 0,
  'CREATE TABLE IF NOT EXISTS `employee_attachments` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` bigint(20) unsigned NOT NULL,
    `attachment_key` varchar(255) NOT NULL,
    `label` varchar(255) NOT NULL,
    `file_path` varchar(255) NOT NULL,
    `original_name` varchar(255) NOT NULL,
    `mime_type` varchar(255) DEFAULT NULL,
    `file_size` bigint(20) unsigned DEFAULT NULL,
    `status` enum(\'pending\',\'approved\',\'rejected\') NOT NULL DEFAULT \'pending\',
    `rejection_reason` text DEFAULT NULL,
    `reviewed_by` bigint(20) unsigned DEFAULT NULL,
    `reviewed_at` timestamp NULL DEFAULT NULL,
    `uploaded_by_role` enum(\'hr\',\'employee\') NOT NULL DEFAULT \'hr\',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `employee_attachments_reviewed_by_foreign` (`reviewed_by`),
    KEY `employee_attachments_user_id_attachment_key_index` (`user_id`,`attachment_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_employee_beneficiaries = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_beneficiaries');

SET @sql = IF(@has_tbl_employee_beneficiaries = 0,
  'CREATE TABLE IF NOT EXISTS `employee_beneficiaries` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` bigint(20) unsigned NOT NULL,
    `name` varchar(255) NOT NULL,
    `date_of_birth` date DEFAULT NULL,
    `relationship` varchar(255) DEFAULT NULL,
    `sequence` int(11) NOT NULL DEFAULT 1,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `employee_beneficiaries_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_employee_experiences = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_experiences');

SET @sql = IF(@has_tbl_employee_experiences = 0,
  'CREATE TABLE IF NOT EXISTS `employee_experiences` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` bigint(20) unsigned NOT NULL,
    `company_name` varchar(255) NOT NULL,
    `position` varchar(255) DEFAULT NULL,
    `duration` varchar(255) DEFAULT NULL,
    `responsibilities` text DEFAULT NULL,
    `sequence` int(11) NOT NULL DEFAULT 1,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `employee_experiences_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_employee_leave_balances = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_leave_balances');

SET @sql = IF(@has_tbl_employee_leave_balances = 0,
  'CREATE TABLE IF NOT EXISTS `employee_leave_balances` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` bigint(20) unsigned NOT NULL,
    `leave_type_id` bigint(20) unsigned NOT NULL,
    `total_days` int(11) NOT NULL,
    `used_days` int(11) NOT NULL DEFAULT 0,
    `remaining_days` int(11) NOT NULL,
    `year` year(4) NOT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `employee_leave_balances_user_id_leave_type_id_year_unique` (`user_id`,`leave_type_id`,`year`),
    KEY `employee_leave_balances_leave_type_id_foreign` (`leave_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_employee_references = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_references');

SET @sql = IF(@has_tbl_employee_references = 0,
  'CREATE TABLE IF NOT EXISTS `employee_references` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` bigint(20) unsigned NOT NULL,
    `name` varchar(255) NOT NULL,
    `address` varchar(255) DEFAULT NULL,
    `contact_number` varchar(255) DEFAULT NULL,
    `sequence` int(11) NOT NULL DEFAULT 1,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `employee_references_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_employee_skills = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_skills');

SET @sql = IF(@has_tbl_employee_skills = 0,
  'CREATE TABLE IF NOT EXISTS `employee_skills` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` bigint(20) unsigned NOT NULL,
    `skill_name` varchar(255) NOT NULL,
    `proficiency` varchar(255) DEFAULT NULL,
    `sequence` int(11) NOT NULL DEFAULT 1,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `employee_skills_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_holidays = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'holidays');

SET @sql = IF(@has_tbl_holidays = 0,
  'CREATE TABLE IF NOT EXISTS `holidays` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `date` date NOT NULL,
    `type` varchar(255) DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_journal_entries = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'journal_entries');

SET @sql = IF(@has_tbl_journal_entries = 0,
  'CREATE TABLE IF NOT EXISTS `journal_entries` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `journal_number` varchar(30) NOT NULL,
    `transaction_date` date NOT NULL,
    `description` varchar(255) NOT NULL,
    `reference_type` varchar(50) DEFAULT NULL,
    `reference_id` bigint(20) unsigned DEFAULT NULL,
    `status` enum(\'Draft\',\'Posted\',\'Void\') NOT NULL DEFAULT \'Draft\',
    `created_by` bigint(20) unsigned DEFAULT NULL,
    `posted_by` bigint(20) unsigned DEFAULT NULL,
    `posted_at` timestamp NULL DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `journal_entries_journal_number_unique` (`journal_number`),
    KEY `journal_entries_created_by_foreign` (`created_by`),
    KEY `journal_entries_posted_by_foreign` (`posted_by`),
    KEY `journal_entries_reference_type_reference_id_index` (`reference_type`,`reference_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_journal_entry_lines = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'journal_entry_lines');

SET @sql = IF(@has_tbl_journal_entry_lines = 0,
  'CREATE TABLE IF NOT EXISTS `journal_entry_lines` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `journal_entry_id` bigint(20) unsigned NOT NULL,
    `account_id` bigint(20) unsigned NOT NULL,
    `description` varchar(255) DEFAULT NULL,
    `debit` decimal(15,2) NOT NULL DEFAULT 0.00,
    `credit` decimal(15,2) NOT NULL DEFAULT 0.00,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `journal_entry_lines_journal_entry_id_foreign` (`journal_entry_id`),
    KEY `journal_entry_lines_account_id_foreign` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_leave_types = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'leave_types');

SET @sql = IF(@has_tbl_leave_types = 0,
  'CREATE TABLE IF NOT EXISTS `leave_types` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `abbreviation` varchar(255) DEFAULT NULL,
    `days_allowed` int(11) NOT NULL DEFAULT 0,
    `carry_over` tinyint(1) NOT NULL DEFAULT 0,
    `description` text DEFAULT NULL,
    `status` enum(\'active\',\'inactive\') NOT NULL DEFAULT \'active\',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_leaves = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'leaves');

SET @sql = IF(@has_tbl_leaves = 0,
  'CREATE TABLE IF NOT EXISTS `leaves` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` bigint(20) unsigned NOT NULL,
    `leave_type_id` bigint(20) unsigned DEFAULT NULL,
    `leave_type` varchar(255) NOT NULL,
    `start_date` date NOT NULL,
    `end_date` date NOT NULL,
    `reason` text DEFAULT NULL,
    `status` enum(\'pending\',\'approved\',\'rejected\',\'paid\') NOT NULL DEFAULT \'pending\',
    `paid_days` int(11) NOT NULL DEFAULT 0,
    `unpaid_days` int(11) NOT NULL DEFAULT 0,
    `rejection_reason` text DEFAULT NULL,
    `approved_by` bigint(20) unsigned DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `leaves_user_id_foreign` (`user_id`),
    KEY `leaves_approved_by_foreign` (`approved_by`),
    KEY `leaves_leave_type_id_foreign` (`leave_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_migrations = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'migrations');

SET @sql = IF(@has_tbl_migrations = 0,
  'CREATE TABLE IF NOT EXISTS `migrations` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `migration` varchar(255) NOT NULL,
    `batch` int(11) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_notifications = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'notifications');

SET @sql = IF(@has_tbl_notifications = 0,
  'CREATE TABLE IF NOT EXISTS `notifications` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` bigint(20) unsigned NOT NULL,
    `type` varchar(255) NOT NULL,
    `title` varchar(255) NOT NULL,
    `message` text NOT NULL,
    `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
    `read_at` timestamp NULL DEFAULT NULL,
    `deleted_at` timestamp NULL DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `notifications_user_id_created_at_index` (`user_id`,`created_at`),
    KEY `notifications_user_id_read_at_index` (`user_id`,`read_at`),
    KEY `notifications_deleted_at_index` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_overtime_undertimes = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'overtime_undertimes');

SET @sql = IF(@has_tbl_overtime_undertimes = 0,
  'CREATE TABLE IF NOT EXISTS `overtime_undertimes` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` bigint(20) unsigned NOT NULL,
    `date` date NOT NULL,
    `type` varchar(255) NOT NULL,
    `hours` decimal(5,2) NOT NULL,
    `amount` decimal(15,2) DEFAULT NULL,
    `hourly_rate_used` decimal(15,2) DEFAULT NULL,
    `reason` text DEFAULT NULL,
    `status` varchar(255) NOT NULL DEFAULT \'pending\',
    `rejection_reason` text DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    `approved_by` bigint(20) unsigned DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `overtime_undertimes_user_id_foreign` (`user_id`),
    KEY `overtime_undertimes_approved_by_foreign` (`approved_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_paos = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'paos');

SET @sql = IF(@has_tbl_paos = 0,
  'CREATE TABLE IF NOT EXISTS `paos` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `contact_number` varchar(255) NOT NULL,
    `gender` enum(\'male\',\'female\',\'prefer_not_to_say\') DEFAULT NULL,
    `email` varchar(255) DEFAULT NULL,
    `address` text DEFAULT NULL,
    `date_of_hire` date DEFAULT NULL,
    `status` varchar(255) NOT NULL DEFAULT \'active\',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `paos_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_payroll_allowances = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_allowances');

SET @sql = IF(@has_tbl_payroll_allowances = 0,
  'CREATE TABLE IF NOT EXISTS `payroll_allowances` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `payroll_id` bigint(20) unsigned NOT NULL,
    `allowance_type` varchar(255) NOT NULL,
    `hours` decimal(8,2) DEFAULT NULL,
    `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `payroll_allowances_payroll_id_foreign` (`payroll_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_payroll_batches = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_batches');

SET @sql = IF(@has_tbl_payroll_batches = 0,
  'CREATE TABLE IF NOT EXISTS `payroll_batches` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `type` varchar(50) NOT NULL DEFAULT \'regular\',
    `period_start` date NOT NULL,
    `period_end` date NOT NULL,
    `status` enum(\'draft\',\'submitted\',\'approved\',\'rejected\') NOT NULL DEFAULT \'draft\',
    `generated_by` bigint(20) unsigned DEFAULT NULL,
    `finalized_by` bigint(20) unsigned DEFAULT NULL,
    `finalized_at` timestamp NULL DEFAULT NULL,
    `approved_by` bigint(20) unsigned DEFAULT NULL,
    `approved_at` timestamp NULL DEFAULT NULL,
    `rejected_by` bigint(20) unsigned DEFAULT NULL,
    `rejected_at` timestamp NULL DEFAULT NULL,
    `rejection_note` text DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `payroll_batches_generated_by_foreign` (`generated_by`),
    KEY `payroll_batches_finalized_by_foreign` (`finalized_by`),
    KEY `payroll_batches_approved_by_foreign` (`approved_by`),
    KEY `payroll_batches_rejected_by_foreign` (`rejected_by`),
    KEY `payroll_batches_type_index` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_payroll_bonuses = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_bonuses');

SET @sql = IF(@has_tbl_payroll_bonuses = 0,
  'CREATE TABLE IF NOT EXISTS `payroll_bonuses` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `payroll_id` bigint(20) unsigned NOT NULL,
    `bonus_type` enum(\'performance\',\'holiday\',\'attendance\',\'special\') NOT NULL,
    `description` varchar(255) DEFAULT NULL,
    `amount` decimal(12,2) NOT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `payroll_bonuses_payroll_id_foreign` (`payroll_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_payroll_cutoff_schedules = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_cutoff_schedules');

SET @sql = IF(@has_tbl_payroll_cutoff_schedules = 0,
  'CREATE TABLE IF NOT EXISTS `payroll_cutoff_schedules` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `cutoff_day` int(11) NOT NULL,
    `label` varchar(255) NOT NULL,
    `payroll_period_start` date DEFAULT NULL,
    `payroll_period_end` date DEFAULT NULL,
    `is_active` tinyint(1) NOT NULL DEFAULT 1,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `payroll_cutoff_schedules_cutoff_day_unique` (`cutoff_day`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_payroll_deductions = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_deductions');

SET @sql = IF(@has_tbl_payroll_deductions = 0,
  'CREATE TABLE IF NOT EXISTS `payroll_deductions` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `payroll_id` bigint(20) unsigned NOT NULL,
    `deduction_type` varchar(255) NOT NULL,
    `hours` decimal(8,2) DEFAULT NULL,
    `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
    `description` text DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    `accounting_treatment` varchar(30) DEFAULT NULL,
    `deduction_source` varchar(20) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `payroll_deductions_payroll_id_foreign` (`payroll_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_payrolls = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls');

SET @sql = IF(@has_tbl_payrolls = 0,
  'CREATE TABLE IF NOT EXISTS `payrolls` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `batch_id` bigint(20) unsigned DEFAULT NULL,
    `user_id` bigint(20) unsigned NOT NULL,
    `payroll_period_start` date NOT NULL,
    `payroll_period_end` date NOT NULL,
    `total_allowances` decimal(10,2) NOT NULL DEFAULT 0.00,
    `total_bonuses` decimal(12,2) NOT NULL DEFAULT 0.00,
    `total_deductions` decimal(10,2) NOT NULL DEFAULT 0.00,
    `cash_advance_deduction` decimal(12,2) NOT NULL DEFAULT 0.00,
    `salary_loan_deduction` decimal(12,2) NOT NULL DEFAULT 0.00,
    `loan_deduction_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`loan_deduction_data`)),
    `status` varchar(255) NOT NULL DEFAULT \'pending\',
    `basic_salary` decimal(12,2) NOT NULL DEFAULT 0.00,
    `gross_pay` decimal(12,2) NOT NULL DEFAULT 0.00,
    `holiday_pay` decimal(12,2) NOT NULL DEFAULT 0.00,
    `holiday_ot_pay` decimal(12,2) NOT NULL DEFAULT 0.00,
    `holiday_ot_hours` decimal(8,2) NOT NULL DEFAULT 0.00,
    `holiday_breakdown` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`holiday_breakdown`)),
    `net_pay` decimal(12,2) NOT NULL DEFAULT 0.00,
    `days_worked` decimal(8,2) NOT NULL DEFAULT 0.00,
    `hours_worked` decimal(8,2) NOT NULL DEFAULT 0.00,
    `payment_date` date DEFAULT NULL,
    `approved_by` bigint(20) unsigned DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    `present_days` int(10) unsigned NOT NULL DEFAULT 0,
    `late_days` int(10) unsigned NOT NULL DEFAULT 0,
    `absent_days` int(10) unsigned NOT NULL DEFAULT 0,
    `daily_rate_snapshot` decimal(10,2) NOT NULL DEFAULT 0.00,
    `sss` decimal(10,2) NOT NULL DEFAULT 0.00,
    `pagibig` decimal(10,2) NOT NULL DEFAULT 0.00,
    `philhealth` decimal(10,2) NOT NULL DEFAULT 0.00,
    `withholding_tax` decimal(12,2) NOT NULL DEFAULT 0.00,
    `employer_sss` decimal(12,2) DEFAULT NULL,
    `employer_pagibig` decimal(12,2) DEFAULT NULL,
    `employer_philhealth` decimal(12,2) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `payrolls_user_id_foreign` (`user_id`),
    KEY `payrolls_approved_by_foreign` (`approved_by`),
    KEY `payrolls_batch_id_foreign` (`batch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_pinned_items = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'pinned_items');

SET @sql = IF(@has_tbl_pinned_items = 0,
  'CREATE TABLE IF NOT EXISTS `pinned_items` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` bigint(20) unsigned NOT NULL,
    `label` varchar(255) NOT NULL,
    `url` varchar(500) NOT NULL,
    `sort_order` smallint(5) unsigned NOT NULL DEFAULT 0,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `pinned_items_user_id_url_unique` (`user_id`,`url`),
    KEY `pinned_items_user_id_sort_order_index` (`user_id`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_position_rates = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'position_rates');

SET @sql = IF(@has_tbl_position_rates = 0,
  'CREATE TABLE IF NOT EXISTS `position_rates` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `department_id` bigint(20) unsigned DEFAULT NULL,
    `name` varchar(255) NOT NULL,
    `daily_rate` decimal(10,2) NOT NULL DEFAULT 0.00,
    `is_active` tinyint(1) NOT NULL DEFAULT 1,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `position_rates_name_unique` (`name`),
    KEY `position_rates_department_id_foreign` (`department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_routes = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'routes');

SET @sql = IF(@has_tbl_routes = 0,
  'CREATE TABLE IF NOT EXISTS `routes` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `route_name` varchar(255) NOT NULL,
    `weekday_boundary` decimal(10,2) NOT NULL DEFAULT 0.00,
    `weekend_boundary` decimal(10,2) NOT NULL DEFAULT 0.00,
    `origin` varchar(255) NOT NULL,
    `destination` varchar(255) NOT NULL,
    `status` varchar(255) NOT NULL DEFAULT \'active\',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_sessions = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sessions');

SET @sql = IF(@has_tbl_sessions = 0,
  'CREATE TABLE IF NOT EXISTS `sessions` (
    `id` varchar(255) NOT NULL,
    `user_id` bigint(20) unsigned DEFAULT NULL,
    `ip_address` varchar(45) DEFAULT NULL,
    `user_agent` text DEFAULT NULL,
    `payload` longtext NOT NULL,
    `last_activity` int(11) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `sessions_user_id_index` (`user_id`),
    KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_settings = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'settings');

SET @sql = IF(@has_tbl_settings = 0,
  'CREATE TABLE IF NOT EXISTS `settings` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `key` varchar(255) NOT NULL,
    `value` text DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_shifts = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'shifts');

SET @sql = IF(@has_tbl_shifts = 0,
  'CREATE TABLE IF NOT EXISTS `shifts` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `start_time` time NOT NULL,
    `end_time` time NOT NULL,
    `break_start` time DEFAULT NULL,
    `break_end` time DEFAULT NULL,
    `is_active` tinyint(1) NOT NULL DEFAULT 1,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `shifts_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_statutory_deductions = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'statutory_deductions');

SET @sql = IF(@has_tbl_statutory_deductions = 0,
  'CREATE TABLE IF NOT EXISTS `statutory_deductions` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `min_salary` decimal(12,2) DEFAULT NULL,
    `max_salary` decimal(12,2) DEFAULT NULL,
    `employee_share` decimal(12,2) DEFAULT NULL,
    `employer_share` decimal(12,2) DEFAULT NULL,
    `percentage_employee` decimal(5,2) DEFAULT NULL,
    `percentage_employer` decimal(5,2) DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_thirteenth_month_pays = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'thirteenth_month_pays');

SET @sql = IF(@has_tbl_thirteenth_month_pays = 0,
  'CREATE TABLE IF NOT EXISTS `thirteenth_month_pays` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `batch_id` bigint(20) unsigned DEFAULT NULL,
    `user_id` bigint(20) unsigned NOT NULL,
    `calendar_year` smallint(5) unsigned NOT NULL,
    `total_basic_salary_earned` decimal(14,2) NOT NULL DEFAULT 0.00,
    `thirteenth_month_pay` decimal(14,2) NOT NULL DEFAULT 0.00,
    `months_worked` decimal(5,2) NOT NULL DEFAULT 0.00,
    `is_eligible` tinyint(1) NOT NULL DEFAULT 1,
    `amount_paid` decimal(14,2) NOT NULL DEFAULT 0.00,
    `amount_remaining` decimal(14,2) NOT NULL DEFAULT 0.00,
    `status` varchar(255) NOT NULL DEFAULT \'pending\',
    `payment_date` date DEFAULT NULL,
    `paid_by` bigint(20) unsigned DEFAULT NULL,
    `notes` text DEFAULT NULL,
    `computed_by` bigint(20) unsigned DEFAULT NULL,
    `computed_at` timestamp NULL DEFAULT NULL,
    `computation_breakdown` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`computation_breakdown`)),
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `thirteenth_month_pays_user_id_calendar_year_unique` (`user_id`,`calendar_year`),
    KEY `thirteenth_month_pays_paid_by_foreign` (`paid_by`),
    KEY `thirteenth_month_pays_computed_by_foreign` (`computed_by`),
    KEY `thirteenth_month_pays_calendar_year_status_index` (`calendar_year`,`status`),
    KEY `thirteenth_month_pays_batch_id_foreign` (`batch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_user_activities = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'user_activities');

SET @sql = IF(@has_tbl_user_activities = 0,
  'CREATE TABLE IF NOT EXISTS `user_activities` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` bigint(20) unsigned NOT NULL,
    `action` varchar(255) NOT NULL,
    `subject_type` varchar(255) DEFAULT NULL,
    `subject_id` bigint(20) unsigned DEFAULT NULL,
    `subject_label` varchar(255) NOT NULL,
    `url` varchar(255) DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `user_activities_user_id_created_at_index` (`user_id`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_users = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users');

SET @sql = IF(@has_tbl_users = 0,
  'CREATE TABLE IF NOT EXISTS `users` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `username` varchar(255) NOT NULL,
    `password` varchar(255) NOT NULL,
    `password_changed` tinyint(1) NOT NULL DEFAULT 0,
    `role` varchar(255) NOT NULL DEFAULT \'employee\',
    `profile_picture` varchar(255) DEFAULT NULL,
    `remember_token` varchar(100) DEFAULT NULL,
    `is_logged_in` tinyint(1) NOT NULL DEFAULT 0,
    `first_name` varchar(255) DEFAULT NULL,
    `middle_name` varchar(255) DEFAULT NULL,
    `last_name` varchar(255) DEFAULT NULL,
    `email` varchar(255) DEFAULT NULL,
    `phone` varchar(255) DEFAULT NULL,
    `address` text DEFAULT NULL,
    `civil_status` varchar(255) DEFAULT NULL,
    `gender` enum(\'male\',\'female\',\'prefer_not_to_say\') DEFAULT NULL,
    `spouse_name` varchar(255) DEFAULT NULL,
    `date_of_birth` date DEFAULT NULL,
    `place_of_birth` varchar(255) DEFAULT NULL,
    `educational_attainment` varchar(255) DEFAULT NULL,
    `driver_license_number` varchar(255) DEFAULT NULL,
    `driver_license_validity` date DEFAULT NULL,
    `date_of_hire` date DEFAULT NULL,
    `position` varchar(255) DEFAULT NULL,
    `shift_id` bigint(20) unsigned DEFAULT NULL,
    `department` varchar(255) DEFAULT NULL,
    `status` varchar(255) NOT NULL DEFAULT \'active\',
    `salary_rate` decimal(10,2) NOT NULL DEFAULT 0.00,
    `work_days_per_week` tinyint(4) NOT NULL DEFAULT 5,
    `bank_name` varchar(255) DEFAULT NULL,
    `bank_account_number` varchar(255) DEFAULT NULL,
    `schedule_type` varchar(255) DEFAULT NULL,
    `has_sss` tinyint(1) NOT NULL DEFAULT 0,
    `sss_number` varchar(255) DEFAULT NULL,
    `tin_number` varchar(255) DEFAULT NULL,
    `has_tin` tinyint(1) NOT NULL DEFAULT 0,
    `pagibig_number` varchar(255) DEFAULT NULL,
    `philhealth_number` varchar(255) DEFAULT NULL,
    `has_pagibig` tinyint(1) NOT NULL DEFAULT 0,
    `has_philhealth` tinyint(1) NOT NULL DEFAULT 0,
    `signature_path` varchar(255) DEFAULT NULL,
    `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attachments`)),
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    `last_login_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_username_unique` (`username`),
    UNIQUE KEY `users_email_unique` (`email`),
    KEY `users_shift_id_foreign` (`shift_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_vehicles = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'vehicles');

SET @sql = IF(@has_tbl_vehicles = 0,
  'CREATE TABLE IF NOT EXISTS `vehicles` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `plate_number` varchar(255) NOT NULL,
    `route_id` bigint(20) unsigned NOT NULL,
    `operator` varchar(255) NOT NULL,
    `status` varchar(255) NOT NULL DEFAULT \'active\',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `vehicles_plate_number_unique` (`plate_number`),
    KEY `vehicles_route_id_foreign` (`route_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_tbl_withholding_taxes = (SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'withholding_taxes');

SET @sql = IF(@has_tbl_withholding_taxes = 0,
  'CREATE TABLE IF NOT EXISTS `withholding_taxes` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `min_salary` decimal(12,2) DEFAULT NULL,
    `max_salary` decimal(12,2) DEFAULT NULL,
    `employee_share` decimal(12,2) DEFAULT NULL,
    `percentage_employee` decimal(5,2) DEFAULT NULL,
    `description` text DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 2. Add any column that is missing --------------------------------------
--
SET @has_account_mappings_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'account_mappings' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_account_mappings_id = 0,
  'ALTER TABLE `account_mappings` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_account_mappings_mapping_key = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'account_mappings' AND COLUMN_NAME = 'mapping_key');

SET @sql = IF(@has_account_mappings_mapping_key = 0,
  'ALTER TABLE `account_mappings` ADD COLUMN `mapping_key` varchar(50) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_account_mappings_label = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'account_mappings' AND COLUMN_NAME = 'label');

SET @sql = IF(@has_account_mappings_label = 0,
  'ALTER TABLE `account_mappings` ADD COLUMN `label` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_account_mappings_account_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'account_mappings' AND COLUMN_NAME = 'account_id');

SET @sql = IF(@has_account_mappings_account_id = 0,
  'ALTER TABLE `account_mappings` ADD COLUMN `account_id` bigint(20) unsigned NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_account_mappings_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'account_mappings' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_account_mappings_created_at = 0,
  'ALTER TABLE `account_mappings` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_account_mappings_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'account_mappings' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_account_mappings_updated_at = 0,
  'ALTER TABLE `account_mappings` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_accounting_audit_logs_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'accounting_audit_logs' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_accounting_audit_logs_id = 0,
  'ALTER TABLE `accounting_audit_logs` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_accounting_audit_logs_user_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'accounting_audit_logs' AND COLUMN_NAME = 'user_id');

SET @sql = IF(@has_accounting_audit_logs_user_id = 0,
  'ALTER TABLE `accounting_audit_logs` ADD COLUMN `user_id` bigint(20) unsigned NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_accounting_audit_logs_action = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'accounting_audit_logs' AND COLUMN_NAME = 'action');

SET @sql = IF(@has_accounting_audit_logs_action = 0,
  'ALTER TABLE `accounting_audit_logs` ADD COLUMN `action` varchar(50) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_accounting_audit_logs_module = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'accounting_audit_logs' AND COLUMN_NAME = 'module');

SET @sql = IF(@has_accounting_audit_logs_module = 0,
  'ALTER TABLE `accounting_audit_logs` ADD COLUMN `module` varchar(50) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_accounting_audit_logs_record_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'accounting_audit_logs' AND COLUMN_NAME = 'record_id');

SET @sql = IF(@has_accounting_audit_logs_record_id = 0,
  'ALTER TABLE `accounting_audit_logs` ADD COLUMN `record_id` bigint(20) unsigned DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_accounting_audit_logs_description = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'accounting_audit_logs' AND COLUMN_NAME = 'description');

SET @sql = IF(@has_accounting_audit_logs_description = 0,
  'ALTER TABLE `accounting_audit_logs` ADD COLUMN `description` text DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_accounting_audit_logs_old_values = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'accounting_audit_logs' AND COLUMN_NAME = 'old_values');

SET @sql = IF(@has_accounting_audit_logs_old_values = 0,
  'ALTER TABLE `accounting_audit_logs` ADD COLUMN `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`))',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_accounting_audit_logs_new_values = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'accounting_audit_logs' AND COLUMN_NAME = 'new_values');

SET @sql = IF(@has_accounting_audit_logs_new_values = 0,
  'ALTER TABLE `accounting_audit_logs` ADD COLUMN `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`))',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_accounting_audit_logs_ip_address = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'accounting_audit_logs' AND COLUMN_NAME = 'ip_address');

SET @sql = IF(@has_accounting_audit_logs_ip_address = 0,
  'ALTER TABLE `accounting_audit_logs` ADD COLUMN `ip_address` varchar(45) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_accounting_audit_logs_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'accounting_audit_logs' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_accounting_audit_logs_created_at = 0,
  'ALTER TABLE `accounting_audit_logs` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_accounting_audit_logs_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'accounting_audit_logs' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_accounting_audit_logs_updated_at = 0,
  'ALTER TABLE `accounting_audit_logs` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_allowances_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'allowances' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_allowances_id = 0,
  'ALTER TABLE `allowances` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_allowances_user_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'allowances' AND COLUMN_NAME = 'user_id');

SET @sql = IF(@has_allowances_user_id = 0,
  'ALTER TABLE `allowances` ADD COLUMN `user_id` bigint(20) unsigned NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_allowances_payroll_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'allowances' AND COLUMN_NAME = 'payroll_id');

SET @sql = IF(@has_allowances_payroll_id = 0,
  'ALTER TABLE `allowances` ADD COLUMN `payroll_id` bigint(20) unsigned DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_allowances_allowance_type = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'allowances' AND COLUMN_NAME = 'allowance_type');

SET @sql = IF(@has_allowances_allowance_type = 0,
  'ALTER TABLE `allowances` ADD COLUMN `allowance_type` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_allowances_amount = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'allowances' AND COLUMN_NAME = 'amount');

SET @sql = IF(@has_allowances_amount = 0,
  'ALTER TABLE `allowances` ADD COLUMN `amount` decimal(10,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_allowances_effective_date = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'allowances' AND COLUMN_NAME = 'effective_date');

SET @sql = IF(@has_allowances_effective_date = 0,
  'ALTER TABLE `allowances` ADD COLUMN `effective_date` date DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_allowances_status = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'allowances' AND COLUMN_NAME = 'status');

SET @sql = IF(@has_allowances_status = 0,
  'ALTER TABLE `allowances` ADD COLUMN `status` varchar(255) NOT NULL DEFAULT \'active\'',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_allowances_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'allowances' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_allowances_created_at = 0,
  'ALTER TABLE `allowances` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_allowances_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'allowances' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_allowances_updated_at = 0,
  'ALTER TABLE `allowances` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_attendance_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'attendance' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_attendance_id = 0,
  'ALTER TABLE `attendance` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_attendance_user_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'attendance' AND COLUMN_NAME = 'user_id');

SET @sql = IF(@has_attendance_user_id = 0,
  'ALTER TABLE `attendance` ADD COLUMN `user_id` bigint(20) unsigned NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_attendance_date = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'attendance' AND COLUMN_NAME = 'date');

SET @sql = IF(@has_attendance_date = 0,
  'ALTER TABLE `attendance` ADD COLUMN `date` date NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_attendance_time_in = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'attendance' AND COLUMN_NAME = 'time_in');

SET @sql = IF(@has_attendance_time_in = 0,
  'ALTER TABLE `attendance` ADD COLUMN `time_in` time DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_attendance_time_out = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'attendance' AND COLUMN_NAME = 'time_out');

SET @sql = IF(@has_attendance_time_out = 0,
  'ALTER TABLE `attendance` ADD COLUMN `time_out` time DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_attendance_status = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'attendance' AND COLUMN_NAME = 'status');

SET @sql = IF(@has_attendance_status = 0,
  'ALTER TABLE `attendance` ADD COLUMN `status` enum(\'present\',\'late\',\'absent\',\'on leave\') DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_attendance_is_manual = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'attendance' AND COLUMN_NAME = 'is_manual');

SET @sql = IF(@has_attendance_is_manual = 0,
  'ALTER TABLE `attendance` ADD COLUMN `is_manual` tinyint(1) NOT NULL DEFAULT 0',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_attendance_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'attendance' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_attendance_created_at = 0,
  'ALTER TABLE `attendance` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_attendance_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'attendance' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_attendance_updated_at = 0,
  'ALTER TABLE `attendance` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_attendance_logs_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'attendance_logs' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_attendance_logs_id = 0,
  'ALTER TABLE `attendance_logs` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_attendance_logs_user_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'attendance_logs' AND COLUMN_NAME = 'user_id');

SET @sql = IF(@has_attendance_logs_user_id = 0,
  'ALTER TABLE `attendance_logs` ADD COLUMN `user_id` bigint(20) unsigned NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_attendance_logs_type = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'attendance_logs' AND COLUMN_NAME = 'type');

SET @sql = IF(@has_attendance_logs_type = 0,
  'ALTER TABLE `attendance_logs` ADD COLUMN `type` enum(\'time_in\',\'time_out\') NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_attendance_logs_logged_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'attendance_logs' AND COLUMN_NAME = 'logged_at');

SET @sql = IF(@has_attendance_logs_logged_at = 0,
  'ALTER TABLE `attendance_logs` ADD COLUMN `logged_at` datetime NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_attendance_logs_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'attendance_logs' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_attendance_logs_created_at = 0,
  'ALTER TABLE `attendance_logs` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_attendance_logs_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'attendance_logs' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_attendance_logs_updated_at = 0,
  'ALTER TABLE `attendance_logs` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_attendance_tokens_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'attendance_tokens' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_attendance_tokens_id = 0,
  'ALTER TABLE `attendance_tokens` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_attendance_tokens_token = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'attendance_tokens' AND COLUMN_NAME = 'token');

SET @sql = IF(@has_attendance_tokens_token = 0,
  'ALTER TABLE `attendance_tokens` ADD COLUMN `token` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_attendance_tokens_used = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'attendance_tokens' AND COLUMN_NAME = 'used');

SET @sql = IF(@has_attendance_tokens_used = 0,
  'ALTER TABLE `attendance_tokens` ADD COLUMN `used` tinyint(1) NOT NULL DEFAULT 0',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_attendance_tokens_user_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'attendance_tokens' AND COLUMN_NAME = 'user_id');

SET @sql = IF(@has_attendance_tokens_user_id = 0,
  'ALTER TABLE `attendance_tokens` ADD COLUMN `user_id` bigint(20) unsigned DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_attendance_tokens_expires_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'attendance_tokens' AND COLUMN_NAME = 'expires_at');

SET @sql = IF(@has_attendance_tokens_expires_at = 0,
  'ALTER TABLE `attendance_tokens` ADD COLUMN `expires_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_attendance_tokens_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'attendance_tokens' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_attendance_tokens_created_at = 0,
  'ALTER TABLE `attendance_tokens` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_attendance_tokens_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'attendance_tokens' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_attendance_tokens_updated_at = 0,
  'ALTER TABLE `attendance_tokens` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_bonus_employee_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bonus_employee' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_bonus_employee_id = 0,
  'ALTER TABLE `bonus_employee` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_bonus_employee_bonus_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bonus_employee' AND COLUMN_NAME = 'bonus_id');

SET @sql = IF(@has_bonus_employee_bonus_id = 0,
  'ALTER TABLE `bonus_employee` ADD COLUMN `bonus_id` bigint(20) unsigned NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_bonus_employee_user_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bonus_employee' AND COLUMN_NAME = 'user_id');

SET @sql = IF(@has_bonus_employee_user_id = 0,
  'ALTER TABLE `bonus_employee` ADD COLUMN `user_id` bigint(20) unsigned NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_bonuses_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bonuses' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_bonuses_id = 0,
  'ALTER TABLE `bonuses` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_bonuses_name = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bonuses' AND COLUMN_NAME = 'name');

SET @sql = IF(@has_bonuses_name = 0,
  'ALTER TABLE `bonuses` ADD COLUMN `name` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_bonuses_description = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bonuses' AND COLUMN_NAME = 'description');

SET @sql = IF(@has_bonuses_description = 0,
  'ALTER TABLE `bonuses` ADD COLUMN `description` text DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_bonuses_type = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bonuses' AND COLUMN_NAME = 'type');

SET @sql = IF(@has_bonuses_type = 0,
  'ALTER TABLE `bonuses` ADD COLUMN `type` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_bonuses_computation_method = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bonuses' AND COLUMN_NAME = 'computation_method');

SET @sql = IF(@has_bonuses_computation_method = 0,
  'ALTER TABLE `bonuses` ADD COLUMN `computation_method` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_bonuses_formula = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bonuses' AND COLUMN_NAME = 'formula');

SET @sql = IF(@has_bonuses_formula = 0,
  'ALTER TABLE `bonuses` ADD COLUMN `formula` text DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_bonuses_fixed_amount = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bonuses' AND COLUMN_NAME = 'fixed_amount');

SET @sql = IF(@has_bonuses_fixed_amount = 0,
  'ALTER TABLE `bonuses` ADD COLUMN `fixed_amount` decimal(12,2) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_bonuses_percentage_value = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bonuses' AND COLUMN_NAME = 'percentage_value');

SET @sql = IF(@has_bonuses_percentage_value = 0,
  'ALTER TABLE `bonuses` ADD COLUMN `percentage_value` decimal(8,4) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_bonuses_is_mandatory = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bonuses' AND COLUMN_NAME = 'is_mandatory');

SET @sql = IF(@has_bonuses_is_mandatory = 0,
  'ALTER TABLE `bonuses` ADD COLUMN `is_mandatory` tinyint(1) NOT NULL DEFAULT 0',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_bonuses_is_system_generated = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bonuses' AND COLUMN_NAME = 'is_system_generated');

SET @sql = IF(@has_bonuses_is_system_generated = 0,
  'ALTER TABLE `bonuses` ADD COLUMN `is_system_generated` tinyint(1) NOT NULL DEFAULT 0',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_bonuses_status = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bonuses' AND COLUMN_NAME = 'status');

SET @sql = IF(@has_bonuses_status = 0,
  'ALTER TABLE `bonuses` ADD COLUMN `status` varchar(255) NOT NULL DEFAULT \'active\'',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_bonuses_year = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bonuses' AND COLUMN_NAME = 'year');

SET @sql = IF(@has_bonuses_year = 0,
  'ALTER TABLE `bonuses` ADD COLUMN `year` smallint(5) unsigned DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_bonuses_payroll_period = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bonuses' AND COLUMN_NAME = 'payroll_period');

SET @sql = IF(@has_bonuses_payroll_period = 0,
  'ALTER TABLE `bonuses` ADD COLUMN `payroll_period` varchar(255) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_bonuses_code = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bonuses' AND COLUMN_NAME = 'code');

SET @sql = IF(@has_bonuses_code = 0,
  'ALTER TABLE `bonuses` ADD COLUMN `code` varchar(255) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_bonuses_created_by = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bonuses' AND COLUMN_NAME = 'created_by');

SET @sql = IF(@has_bonuses_created_by = 0,
  'ALTER TABLE `bonuses` ADD COLUMN `created_by` bigint(20) unsigned DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_bonuses_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bonuses' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_bonuses_created_at = 0,
  'ALTER TABLE `bonuses` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_bonuses_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bonuses' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_bonuses_updated_at = 0,
  'ALTER TABLE `bonuses` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_boundary_payments_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'boundary_payments' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_boundary_payments_id = 0,
  'ALTER TABLE `boundary_payments` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_boundary_payments_daily_remittance_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'boundary_payments' AND COLUMN_NAME = 'daily_remittance_id');

SET @sql = IF(@has_boundary_payments_daily_remittance_id = 0,
  'ALTER TABLE `boundary_payments` ADD COLUMN `daily_remittance_id` bigint(20) unsigned NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_boundary_payments_source_payment_key = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'boundary_payments' AND COLUMN_NAME = 'source_payment_key');

SET @sql = IF(@has_boundary_payments_source_payment_key = 0,
  'ALTER TABLE `boundary_payments` ADD COLUMN `source_payment_key` varchar(64) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_boundary_payments_initial_payment_remittance_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'boundary_payments' AND COLUMN_NAME = 'initial_payment_remittance_id');

SET @sql = IF(@has_boundary_payments_initial_payment_remittance_id = 0,
  'ALTER TABLE `boundary_payments` ADD COLUMN `initial_payment_remittance_id` bigint(20) unsigned DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_boundary_payments_payment_date = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'boundary_payments' AND COLUMN_NAME = 'payment_date');

SET @sql = IF(@has_boundary_payments_payment_date = 0,
  'ALTER TABLE `boundary_payments` ADD COLUMN `payment_date` date NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_boundary_payments_amount = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'boundary_payments' AND COLUMN_NAME = 'amount');

SET @sql = IF(@has_boundary_payments_amount = 0,
  'ALTER TABLE `boundary_payments` ADD COLUMN `amount` decimal(15,2) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_boundary_payments_paid_by = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'boundary_payments' AND COLUMN_NAME = 'paid_by');

SET @sql = IF(@has_boundary_payments_paid_by = 0,
  'ALTER TABLE `boundary_payments` ADD COLUMN `paid_by` varchar(16) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_boundary_payments_payment_kind = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'boundary_payments' AND COLUMN_NAME = 'payment_kind');

SET @sql = IF(@has_boundary_payments_payment_kind = 0,
  'ALTER TABLE `boundary_payments` ADD COLUMN `payment_kind` varchar(16) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_boundary_payments_status = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'boundary_payments' AND COLUMN_NAME = 'status');

SET @sql = IF(@has_boundary_payments_status = 0,
  'ALTER TABLE `boundary_payments` ADD COLUMN `status` varchar(16) NOT NULL DEFAULT \'Posted\'',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_boundary_payments_recorded_by = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'boundary_payments' AND COLUMN_NAME = 'recorded_by');

SET @sql = IF(@has_boundary_payments_recorded_by = 0,
  'ALTER TABLE `boundary_payments` ADD COLUMN `recorded_by` bigint(20) unsigned NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_boundary_payments_approved_by = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'boundary_payments' AND COLUMN_NAME = 'approved_by');

SET @sql = IF(@has_boundary_payments_approved_by = 0,
  'ALTER TABLE `boundary_payments` ADD COLUMN `approved_by` bigint(20) unsigned DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_boundary_payments_journal_entry_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'boundary_payments' AND COLUMN_NAME = 'journal_entry_id');

SET @sql = IF(@has_boundary_payments_journal_entry_id = 0,
  'ALTER TABLE `boundary_payments` ADD COLUMN `journal_entry_id` bigint(20) unsigned DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_boundary_payments_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'boundary_payments' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_boundary_payments_created_at = 0,
  'ALTER TABLE `boundary_payments` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_boundary_payments_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'boundary_payments' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_boundary_payments_updated_at = 0,
  'ALTER TABLE `boundary_payments` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_cache_key = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cache' AND COLUMN_NAME = 'key');

SET @sql = IF(@has_cache_key = 0,
  'ALTER TABLE `cache` ADD COLUMN `key` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_cache_value = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cache' AND COLUMN_NAME = 'value');

SET @sql = IF(@has_cache_value = 0,
  'ALTER TABLE `cache` ADD COLUMN `value` mediumtext NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_cache_expiration = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cache' AND COLUMN_NAME = 'expiration');

SET @sql = IF(@has_cache_expiration = 0,
  'ALTER TABLE `cache` ADD COLUMN `expiration` int(11) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_cache_locks_key = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cache_locks' AND COLUMN_NAME = 'key');

SET @sql = IF(@has_cache_locks_key = 0,
  'ALTER TABLE `cache_locks` ADD COLUMN `key` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_cache_locks_owner = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cache_locks' AND COLUMN_NAME = 'owner');

SET @sql = IF(@has_cache_locks_owner = 0,
  'ALTER TABLE `cache_locks` ADD COLUMN `owner` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_cache_locks_expiration = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cache_locks' AND COLUMN_NAME = 'expiration');

SET @sql = IF(@has_cache_locks_expiration = 0,
  'ALTER TABLE `cache_locks` ADD COLUMN `expiration` int(11) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_cash_advances_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cash_advances' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_cash_advances_id = 0,
  'ALTER TABLE `cash_advances` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_cash_advances_user_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cash_advances' AND COLUMN_NAME = 'user_id');

SET @sql = IF(@has_cash_advances_user_id = 0,
  'ALTER TABLE `cash_advances` ADD COLUMN `user_id` bigint(20) unsigned NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_cash_advances_amount = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cash_advances' AND COLUMN_NAME = 'amount');

SET @sql = IF(@has_cash_advances_amount = 0,
  'ALTER TABLE `cash_advances` ADD COLUMN `amount` decimal(10,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_cash_advances_repayment_months = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cash_advances' AND COLUMN_NAME = 'repayment_months');

SET @sql = IF(@has_cash_advances_repayment_months = 0,
  'ALTER TABLE `cash_advances` ADD COLUMN `repayment_months` int(11) NOT NULL DEFAULT 1',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_cash_advances_monthly_deduction = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cash_advances' AND COLUMN_NAME = 'monthly_deduction');

SET @sql = IF(@has_cash_advances_monthly_deduction = 0,
  'ALTER TABLE `cash_advances` ADD COLUMN `monthly_deduction` decimal(12,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_cash_advances_amount_deducted = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cash_advances' AND COLUMN_NAME = 'amount_deducted');

SET @sql = IF(@has_cash_advances_amount_deducted = 0,
  'ALTER TABLE `cash_advances` ADD COLUMN `amount_deducted` decimal(12,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_cash_advances_request_date = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cash_advances' AND COLUMN_NAME = 'request_date');

SET @sql = IF(@has_cash_advances_request_date = 0,
  'ALTER TABLE `cash_advances` ADD COLUMN `request_date` date DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_cash_advances_approval_date = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cash_advances' AND COLUMN_NAME = 'approval_date');

SET @sql = IF(@has_cash_advances_approval_date = 0,
  'ALTER TABLE `cash_advances` ADD COLUMN `approval_date` date DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_cash_advances_status = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cash_advances' AND COLUMN_NAME = 'status');

SET @sql = IF(@has_cash_advances_status = 0,
  'ALTER TABLE `cash_advances` ADD COLUMN `status` varchar(255) NOT NULL DEFAULT \'pending\'',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_cash_advances_approved_by = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cash_advances' AND COLUMN_NAME = 'approved_by');

SET @sql = IF(@has_cash_advances_approved_by = 0,
  'ALTER TABLE `cash_advances` ADD COLUMN `approved_by` bigint(20) unsigned DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_cash_advances_approved_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cash_advances' AND COLUMN_NAME = 'approved_at');

SET @sql = IF(@has_cash_advances_approved_at = 0,
  'ALTER TABLE `cash_advances` ADD COLUMN `approved_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_cash_advances_rejection_reason = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cash_advances' AND COLUMN_NAME = 'rejection_reason');

SET @sql = IF(@has_cash_advances_rejection_reason = 0,
  'ALTER TABLE `cash_advances` ADD COLUMN `rejection_reason` text DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_cash_advances_deducted_payroll_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cash_advances' AND COLUMN_NAME = 'deducted_payroll_id');

SET @sql = IF(@has_cash_advances_deducted_payroll_id = 0,
  'ALTER TABLE `cash_advances` ADD COLUMN `deducted_payroll_id` bigint(20) unsigned DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_cash_advances_notes = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cash_advances' AND COLUMN_NAME = 'notes');

SET @sql = IF(@has_cash_advances_notes = 0,
  'ALTER TABLE `cash_advances` ADD COLUMN `notes` text DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_cash_advances_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cash_advances' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_cash_advances_created_at = 0,
  'ALTER TABLE `cash_advances` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_cash_advances_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cash_advances' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_cash_advances_updated_at = 0,
  'ALTER TABLE `cash_advances` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_chart_of_accounts_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'chart_of_accounts' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_chart_of_accounts_id = 0,
  'ALTER TABLE `chart_of_accounts` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_chart_of_accounts_account_code = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'chart_of_accounts' AND COLUMN_NAME = 'account_code');

SET @sql = IF(@has_chart_of_accounts_account_code = 0,
  'ALTER TABLE `chart_of_accounts` ADD COLUMN `account_code` varchar(20) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_chart_of_accounts_account_name = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'chart_of_accounts' AND COLUMN_NAME = 'account_name');

SET @sql = IF(@has_chart_of_accounts_account_name = 0,
  'ALTER TABLE `chart_of_accounts` ADD COLUMN `account_name` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_chart_of_accounts_account_type = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'chart_of_accounts' AND COLUMN_NAME = 'account_type');

SET @sql = IF(@has_chart_of_accounts_account_type = 0,
  'ALTER TABLE `chart_of_accounts` ADD COLUMN `account_type` enum(\'Asset\',\'Liability\',\'Equity\',\'Revenue\',\'Expense\') NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_chart_of_accounts_description = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'chart_of_accounts' AND COLUMN_NAME = 'description');

SET @sql = IF(@has_chart_of_accounts_description = 0,
  'ALTER TABLE `chart_of_accounts` ADD COLUMN `description` text DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_chart_of_accounts_is_active = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'chart_of_accounts' AND COLUMN_NAME = 'is_active');

SET @sql = IF(@has_chart_of_accounts_is_active = 0,
  'ALTER TABLE `chart_of_accounts` ADD COLUMN `is_active` tinyint(1) NOT NULL DEFAULT 1',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_chart_of_accounts_created_by = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'chart_of_accounts' AND COLUMN_NAME = 'created_by');

SET @sql = IF(@has_chart_of_accounts_created_by = 0,
  'ALTER TABLE `chart_of_accounts` ADD COLUMN `created_by` bigint(20) unsigned DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_chart_of_accounts_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'chart_of_accounts' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_chart_of_accounts_created_at = 0,
  'ALTER TABLE `chart_of_accounts` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_chart_of_accounts_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'chart_of_accounts' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_chart_of_accounts_updated_at = 0,
  'ALTER TABLE `chart_of_accounts` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_chart_of_accounts_is_contra = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'chart_of_accounts' AND COLUMN_NAME = 'is_contra');

SET @sql = IF(@has_chart_of_accounts_is_contra = 0,
  'ALTER TABLE `chart_of_accounts` ADD COLUMN `is_contra` tinyint(1) NOT NULL DEFAULT 0',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_daily_remittances_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'daily_remittances' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_daily_remittances_id = 0,
  'ALTER TABLE `daily_remittances` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_daily_remittances_driver_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'daily_remittances' AND COLUMN_NAME = 'driver_id');

SET @sql = IF(@has_daily_remittances_driver_id = 0,
  'ALTER TABLE `daily_remittances` ADD COLUMN `driver_id` bigint(20) unsigned NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_daily_remittances_pao_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'daily_remittances' AND COLUMN_NAME = 'pao_id');

SET @sql = IF(@has_daily_remittances_pao_id = 0,
  'ALTER TABLE `daily_remittances` ADD COLUMN `pao_id` bigint(20) unsigned NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_daily_remittances_route_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'daily_remittances' AND COLUMN_NAME = 'route_id');

SET @sql = IF(@has_daily_remittances_route_id = 0,
  'ALTER TABLE `daily_remittances` ADD COLUMN `route_id` bigint(20) unsigned NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_daily_remittances_vehicle_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'daily_remittances' AND COLUMN_NAME = 'vehicle_id');

SET @sql = IF(@has_daily_remittances_vehicle_id = 0,
  'ALTER TABLE `daily_remittances` ADD COLUMN `vehicle_id` bigint(20) unsigned NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_daily_remittances_remittance_date = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'daily_remittances' AND COLUMN_NAME = 'remittance_date');

SET @sql = IF(@has_daily_remittances_remittance_date = 0,
  'ALTER TABLE `daily_remittances` ADD COLUMN `remittance_date` date NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_daily_remittances_total_collection = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'daily_remittances' AND COLUMN_NAME = 'total_collection');

SET @sql = IF(@has_daily_remittances_total_collection = 0,
  'ALTER TABLE `daily_remittances` ADD COLUMN `total_collection` decimal(10,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_daily_remittances_diesel = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'daily_remittances' AND COLUMN_NAME = 'diesel');

SET @sql = IF(@has_daily_remittances_diesel = 0,
  'ALTER TABLE `daily_remittances` ADD COLUMN `diesel` decimal(10,2) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_daily_remittances_parking = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'daily_remittances' AND COLUMN_NAME = 'parking');

SET @sql = IF(@has_daily_remittances_parking = 0,
  'ALTER TABLE `daily_remittances` ADD COLUMN `parking` decimal(10,2) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_daily_remittances_dispatcher = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'daily_remittances' AND COLUMN_NAME = 'dispatcher');

SET @sql = IF(@has_daily_remittances_dispatcher = 0,
  'ALTER TABLE `daily_remittances` ADD COLUMN `dispatcher` decimal(10,2) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_daily_remittances_food_allowance = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'daily_remittances' AND COLUMN_NAME = 'food_allowance');

SET @sql = IF(@has_daily_remittances_food_allowance = 0,
  'ALTER TABLE `daily_remittances` ADD COLUMN `food_allowance` decimal(10,2) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_daily_remittances_barker = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'daily_remittances' AND COLUMN_NAME = 'barker');

SET @sql = IF(@has_daily_remittances_barker = 0,
  'ALTER TABLE `daily_remittances` ADD COLUMN `barker` decimal(10,2) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_daily_remittances_others = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'daily_remittances' AND COLUMN_NAME = 'others');

SET @sql = IF(@has_daily_remittances_others = 0,
  'ALTER TABLE `daily_remittances` ADD COLUMN `others` decimal(10,2) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_daily_remittances_total_expenses = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'daily_remittances' AND COLUMN_NAME = 'total_expenses');

SET @sql = IF(@has_daily_remittances_total_expenses = 0,
  'ALTER TABLE `daily_remittances` ADD COLUMN `total_expenses` decimal(10,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_daily_remittances_boundary = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'daily_remittances' AND COLUMN_NAME = 'boundary');

SET @sql = IF(@has_daily_remittances_boundary = 0,
  'ALTER TABLE `daily_remittances` ADD COLUMN `boundary` decimal(10,2) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_daily_remittances_net_remittance = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'daily_remittances' AND COLUMN_NAME = 'net_remittance');

SET @sql = IF(@has_daily_remittances_net_remittance = 0,
  'ALTER TABLE `daily_remittances` ADD COLUMN `net_remittance` decimal(10,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_daily_remittances_is_short_remittance = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'daily_remittances' AND COLUMN_NAME = 'is_short_remittance');

SET @sql = IF(@has_daily_remittances_is_short_remittance = 0,
  'ALTER TABLE `daily_remittances` ADD COLUMN `is_short_remittance` tinyint(1) NOT NULL DEFAULT 0',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_daily_remittances_short_amount = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'daily_remittances' AND COLUMN_NAME = 'short_amount');

SET @sql = IF(@has_daily_remittances_short_amount = 0,
  'ALTER TABLE `daily_remittances` ADD COLUMN `short_amount` decimal(10,2) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_daily_remittances_driver_share = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'daily_remittances' AND COLUMN_NAME = 'driver_share');

SET @sql = IF(@has_daily_remittances_driver_share = 0,
  'ALTER TABLE `daily_remittances` ADD COLUMN `driver_share` decimal(10,2) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_daily_remittances_driver_amount_paid = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'daily_remittances' AND COLUMN_NAME = 'driver_amount_paid');

SET @sql = IF(@has_daily_remittances_driver_amount_paid = 0,
  'ALTER TABLE `daily_remittances` ADD COLUMN `driver_amount_paid` decimal(10,2) DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_daily_remittances_driver_status = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'daily_remittances' AND COLUMN_NAME = 'driver_status');

SET @sql = IF(@has_daily_remittances_driver_status = 0,
  'ALTER TABLE `daily_remittances` ADD COLUMN `driver_status` enum(\'pending\',\'partial\',\'paid\') DEFAULT \'pending\'',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_daily_remittances_pao_share = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'daily_remittances' AND COLUMN_NAME = 'pao_share');

SET @sql = IF(@has_daily_remittances_pao_share = 0,
  'ALTER TABLE `daily_remittances` ADD COLUMN `pao_share` decimal(10,2) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_daily_remittances_pao_amount_paid = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'daily_remittances' AND COLUMN_NAME = 'pao_amount_paid');

SET @sql = IF(@has_daily_remittances_pao_amount_paid = 0,
  'ALTER TABLE `daily_remittances` ADD COLUMN `pao_amount_paid` decimal(10,2) DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_daily_remittances_pao_status = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'daily_remittances' AND COLUMN_NAME = 'pao_status');

SET @sql = IF(@has_daily_remittances_pao_status = 0,
  'ALTER TABLE `daily_remittances` ADD COLUMN `pao_status` enum(\'pending\',\'partial\',\'paid\') DEFAULT \'pending\'',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_daily_remittances_resolution_notes = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'daily_remittances' AND COLUMN_NAME = 'resolution_notes');

SET @sql = IF(@has_daily_remittances_resolution_notes = 0,
  'ALTER TABLE `daily_remittances` ADD COLUMN `resolution_notes` text DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_daily_remittances_resolved_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'daily_remittances' AND COLUMN_NAME = 'resolved_at');

SET @sql = IF(@has_daily_remittances_resolved_at = 0,
  'ALTER TABLE `daily_remittances` ADD COLUMN `resolved_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_daily_remittances_status = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'daily_remittances' AND COLUMN_NAME = 'status');

SET @sql = IF(@has_daily_remittances_status = 0,
  'ALTER TABLE `daily_remittances` ADD COLUMN `status` varchar(255) NOT NULL DEFAULT \'pending\'',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_daily_remittances_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'daily_remittances' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_daily_remittances_created_at = 0,
  'ALTER TABLE `daily_remittances` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_daily_remittances_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'daily_remittances' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_daily_remittances_updated_at = 0,
  'ALTER TABLE `daily_remittances` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_deductions_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'deductions' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_deductions_id = 0,
  'ALTER TABLE `deductions` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_deductions_user_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'deductions' AND COLUMN_NAME = 'user_id');

SET @sql = IF(@has_deductions_user_id = 0,
  'ALTER TABLE `deductions` ADD COLUMN `user_id` bigint(20) unsigned NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_deductions_payroll_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'deductions' AND COLUMN_NAME = 'payroll_id');

SET @sql = IF(@has_deductions_payroll_id = 0,
  'ALTER TABLE `deductions` ADD COLUMN `payroll_id` bigint(20) unsigned DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_deductions_deduction_type = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'deductions' AND COLUMN_NAME = 'deduction_type');

SET @sql = IF(@has_deductions_deduction_type = 0,
  'ALTER TABLE `deductions` ADD COLUMN `deduction_type` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_deductions_amount = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'deductions' AND COLUMN_NAME = 'amount');

SET @sql = IF(@has_deductions_amount = 0,
  'ALTER TABLE `deductions` ADD COLUMN `amount` decimal(10,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_deductions_effective_date = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'deductions' AND COLUMN_NAME = 'effective_date');

SET @sql = IF(@has_deductions_effective_date = 0,
  'ALTER TABLE `deductions` ADD COLUMN `effective_date` date DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_deductions_status = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'deductions' AND COLUMN_NAME = 'status');

SET @sql = IF(@has_deductions_status = 0,
  'ALTER TABLE `deductions` ADD COLUMN `status` varchar(255) NOT NULL DEFAULT \'active\'',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_deductions_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'deductions' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_deductions_created_at = 0,
  'ALTER TABLE `deductions` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_deductions_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'deductions' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_deductions_updated_at = 0,
  'ALTER TABLE `deductions` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_departments_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'departments' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_departments_id = 0,
  'ALTER TABLE `departments` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_departments_name = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'departments' AND COLUMN_NAME = 'name');

SET @sql = IF(@has_departments_name = 0,
  'ALTER TABLE `departments` ADD COLUMN `name` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_departments_is_active = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'departments' AND COLUMN_NAME = 'is_active');

SET @sql = IF(@has_departments_is_active = 0,
  'ALTER TABLE `departments` ADD COLUMN `is_active` tinyint(1) NOT NULL DEFAULT 1',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_departments_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'departments' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_departments_created_at = 0,
  'ALTER TABLE `departments` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_departments_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'departments' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_departments_updated_at = 0,
  'ALTER TABLE `departments` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_drivers_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'drivers' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_drivers_id = 0,
  'ALTER TABLE `drivers` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_drivers_name = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'drivers' AND COLUMN_NAME = 'name');

SET @sql = IF(@has_drivers_name = 0,
  'ALTER TABLE `drivers` ADD COLUMN `name` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_drivers_license_number = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'drivers' AND COLUMN_NAME = 'license_number');

SET @sql = IF(@has_drivers_license_number = 0,
  'ALTER TABLE `drivers` ADD COLUMN `license_number` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_drivers_contact_number = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'drivers' AND COLUMN_NAME = 'contact_number');

SET @sql = IF(@has_drivers_contact_number = 0,
  'ALTER TABLE `drivers` ADD COLUMN `contact_number` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_drivers_gender = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'drivers' AND COLUMN_NAME = 'gender');

SET @sql = IF(@has_drivers_gender = 0,
  'ALTER TABLE `drivers` ADD COLUMN `gender` enum(\'male\',\'female\',\'prefer_not_to_say\') DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_drivers_email = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'drivers' AND COLUMN_NAME = 'email');

SET @sql = IF(@has_drivers_email = 0,
  'ALTER TABLE `drivers` ADD COLUMN `email` varchar(255) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_drivers_address = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'drivers' AND COLUMN_NAME = 'address');

SET @sql = IF(@has_drivers_address = 0,
  'ALTER TABLE `drivers` ADD COLUMN `address` text DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_drivers_date_of_hire = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'drivers' AND COLUMN_NAME = 'date_of_hire');

SET @sql = IF(@has_drivers_date_of_hire = 0,
  'ALTER TABLE `drivers` ADD COLUMN `date_of_hire` date DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_drivers_status = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'drivers' AND COLUMN_NAME = 'status');

SET @sql = IF(@has_drivers_status = 0,
  'ALTER TABLE `drivers` ADD COLUMN `status` varchar(255) NOT NULL DEFAULT \'active\'',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_drivers_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'drivers' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_drivers_created_at = 0,
  'ALTER TABLE `drivers` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_drivers_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'drivers' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_drivers_updated_at = 0,
  'ALTER TABLE `drivers` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_attachments_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_attachments' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_employee_attachments_id = 0,
  'ALTER TABLE `employee_attachments` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_attachments_user_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_attachments' AND COLUMN_NAME = 'user_id');

SET @sql = IF(@has_employee_attachments_user_id = 0,
  'ALTER TABLE `employee_attachments` ADD COLUMN `user_id` bigint(20) unsigned NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_attachments_attachment_key = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_attachments' AND COLUMN_NAME = 'attachment_key');

SET @sql = IF(@has_employee_attachments_attachment_key = 0,
  'ALTER TABLE `employee_attachments` ADD COLUMN `attachment_key` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_attachments_label = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_attachments' AND COLUMN_NAME = 'label');

SET @sql = IF(@has_employee_attachments_label = 0,
  'ALTER TABLE `employee_attachments` ADD COLUMN `label` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_attachments_file_path = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_attachments' AND COLUMN_NAME = 'file_path');

SET @sql = IF(@has_employee_attachments_file_path = 0,
  'ALTER TABLE `employee_attachments` ADD COLUMN `file_path` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_attachments_original_name = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_attachments' AND COLUMN_NAME = 'original_name');

SET @sql = IF(@has_employee_attachments_original_name = 0,
  'ALTER TABLE `employee_attachments` ADD COLUMN `original_name` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_attachments_mime_type = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_attachments' AND COLUMN_NAME = 'mime_type');

SET @sql = IF(@has_employee_attachments_mime_type = 0,
  'ALTER TABLE `employee_attachments` ADD COLUMN `mime_type` varchar(255) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_attachments_file_size = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_attachments' AND COLUMN_NAME = 'file_size');

SET @sql = IF(@has_employee_attachments_file_size = 0,
  'ALTER TABLE `employee_attachments` ADD COLUMN `file_size` bigint(20) unsigned DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_attachments_status = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_attachments' AND COLUMN_NAME = 'status');

SET @sql = IF(@has_employee_attachments_status = 0,
  'ALTER TABLE `employee_attachments` ADD COLUMN `status` enum(\'pending\',\'approved\',\'rejected\') NOT NULL DEFAULT \'pending\'',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_attachments_rejection_reason = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_attachments' AND COLUMN_NAME = 'rejection_reason');

SET @sql = IF(@has_employee_attachments_rejection_reason = 0,
  'ALTER TABLE `employee_attachments` ADD COLUMN `rejection_reason` text DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_attachments_reviewed_by = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_attachments' AND COLUMN_NAME = 'reviewed_by');

SET @sql = IF(@has_employee_attachments_reviewed_by = 0,
  'ALTER TABLE `employee_attachments` ADD COLUMN `reviewed_by` bigint(20) unsigned DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_attachments_reviewed_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_attachments' AND COLUMN_NAME = 'reviewed_at');

SET @sql = IF(@has_employee_attachments_reviewed_at = 0,
  'ALTER TABLE `employee_attachments` ADD COLUMN `reviewed_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_attachments_uploaded_by_role = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_attachments' AND COLUMN_NAME = 'uploaded_by_role');

SET @sql = IF(@has_employee_attachments_uploaded_by_role = 0,
  'ALTER TABLE `employee_attachments` ADD COLUMN `uploaded_by_role` enum(\'hr\',\'employee\') NOT NULL DEFAULT \'hr\'',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_attachments_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_attachments' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_employee_attachments_created_at = 0,
  'ALTER TABLE `employee_attachments` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_attachments_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_attachments' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_employee_attachments_updated_at = 0,
  'ALTER TABLE `employee_attachments` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_beneficiaries_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_beneficiaries' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_employee_beneficiaries_id = 0,
  'ALTER TABLE `employee_beneficiaries` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_beneficiaries_user_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_beneficiaries' AND COLUMN_NAME = 'user_id');

SET @sql = IF(@has_employee_beneficiaries_user_id = 0,
  'ALTER TABLE `employee_beneficiaries` ADD COLUMN `user_id` bigint(20) unsigned NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_beneficiaries_name = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_beneficiaries' AND COLUMN_NAME = 'name');

SET @sql = IF(@has_employee_beneficiaries_name = 0,
  'ALTER TABLE `employee_beneficiaries` ADD COLUMN `name` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_beneficiaries_date_of_birth = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_beneficiaries' AND COLUMN_NAME = 'date_of_birth');

SET @sql = IF(@has_employee_beneficiaries_date_of_birth = 0,
  'ALTER TABLE `employee_beneficiaries` ADD COLUMN `date_of_birth` date DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_beneficiaries_relationship = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_beneficiaries' AND COLUMN_NAME = 'relationship');

SET @sql = IF(@has_employee_beneficiaries_relationship = 0,
  'ALTER TABLE `employee_beneficiaries` ADD COLUMN `relationship` varchar(255) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_beneficiaries_sequence = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_beneficiaries' AND COLUMN_NAME = 'sequence');

SET @sql = IF(@has_employee_beneficiaries_sequence = 0,
  'ALTER TABLE `employee_beneficiaries` ADD COLUMN `sequence` int(11) NOT NULL DEFAULT 1',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_beneficiaries_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_beneficiaries' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_employee_beneficiaries_created_at = 0,
  'ALTER TABLE `employee_beneficiaries` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_beneficiaries_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_beneficiaries' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_employee_beneficiaries_updated_at = 0,
  'ALTER TABLE `employee_beneficiaries` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_experiences_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_experiences' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_employee_experiences_id = 0,
  'ALTER TABLE `employee_experiences` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_experiences_user_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_experiences' AND COLUMN_NAME = 'user_id');

SET @sql = IF(@has_employee_experiences_user_id = 0,
  'ALTER TABLE `employee_experiences` ADD COLUMN `user_id` bigint(20) unsigned NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_experiences_company_name = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_experiences' AND COLUMN_NAME = 'company_name');

SET @sql = IF(@has_employee_experiences_company_name = 0,
  'ALTER TABLE `employee_experiences` ADD COLUMN `company_name` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_experiences_position = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_experiences' AND COLUMN_NAME = 'position');

SET @sql = IF(@has_employee_experiences_position = 0,
  'ALTER TABLE `employee_experiences` ADD COLUMN `position` varchar(255) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_experiences_duration = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_experiences' AND COLUMN_NAME = 'duration');

SET @sql = IF(@has_employee_experiences_duration = 0,
  'ALTER TABLE `employee_experiences` ADD COLUMN `duration` varchar(255) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_experiences_responsibilities = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_experiences' AND COLUMN_NAME = 'responsibilities');

SET @sql = IF(@has_employee_experiences_responsibilities = 0,
  'ALTER TABLE `employee_experiences` ADD COLUMN `responsibilities` text DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_experiences_sequence = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_experiences' AND COLUMN_NAME = 'sequence');

SET @sql = IF(@has_employee_experiences_sequence = 0,
  'ALTER TABLE `employee_experiences` ADD COLUMN `sequence` int(11) NOT NULL DEFAULT 1',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_experiences_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_experiences' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_employee_experiences_created_at = 0,
  'ALTER TABLE `employee_experiences` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_experiences_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_experiences' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_employee_experiences_updated_at = 0,
  'ALTER TABLE `employee_experiences` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_leave_balances_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_leave_balances' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_employee_leave_balances_id = 0,
  'ALTER TABLE `employee_leave_balances` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_leave_balances_user_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_leave_balances' AND COLUMN_NAME = 'user_id');

SET @sql = IF(@has_employee_leave_balances_user_id = 0,
  'ALTER TABLE `employee_leave_balances` ADD COLUMN `user_id` bigint(20) unsigned NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_leave_balances_leave_type_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_leave_balances' AND COLUMN_NAME = 'leave_type_id');

SET @sql = IF(@has_employee_leave_balances_leave_type_id = 0,
  'ALTER TABLE `employee_leave_balances` ADD COLUMN `leave_type_id` bigint(20) unsigned NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_leave_balances_total_days = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_leave_balances' AND COLUMN_NAME = 'total_days');

SET @sql = IF(@has_employee_leave_balances_total_days = 0,
  'ALTER TABLE `employee_leave_balances` ADD COLUMN `total_days` int(11) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_leave_balances_used_days = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_leave_balances' AND COLUMN_NAME = 'used_days');

SET @sql = IF(@has_employee_leave_balances_used_days = 0,
  'ALTER TABLE `employee_leave_balances` ADD COLUMN `used_days` int(11) NOT NULL DEFAULT 0',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_leave_balances_remaining_days = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_leave_balances' AND COLUMN_NAME = 'remaining_days');

SET @sql = IF(@has_employee_leave_balances_remaining_days = 0,
  'ALTER TABLE `employee_leave_balances` ADD COLUMN `remaining_days` int(11) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_leave_balances_year = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_leave_balances' AND COLUMN_NAME = 'year');

SET @sql = IF(@has_employee_leave_balances_year = 0,
  'ALTER TABLE `employee_leave_balances` ADD COLUMN `year` year(4) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_leave_balances_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_leave_balances' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_employee_leave_balances_created_at = 0,
  'ALTER TABLE `employee_leave_balances` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_leave_balances_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_leave_balances' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_employee_leave_balances_updated_at = 0,
  'ALTER TABLE `employee_leave_balances` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_references_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_references' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_employee_references_id = 0,
  'ALTER TABLE `employee_references` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_references_user_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_references' AND COLUMN_NAME = 'user_id');

SET @sql = IF(@has_employee_references_user_id = 0,
  'ALTER TABLE `employee_references` ADD COLUMN `user_id` bigint(20) unsigned NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_references_name = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_references' AND COLUMN_NAME = 'name');

SET @sql = IF(@has_employee_references_name = 0,
  'ALTER TABLE `employee_references` ADD COLUMN `name` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_references_address = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_references' AND COLUMN_NAME = 'address');

SET @sql = IF(@has_employee_references_address = 0,
  'ALTER TABLE `employee_references` ADD COLUMN `address` varchar(255) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_references_contact_number = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_references' AND COLUMN_NAME = 'contact_number');

SET @sql = IF(@has_employee_references_contact_number = 0,
  'ALTER TABLE `employee_references` ADD COLUMN `contact_number` varchar(255) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_references_sequence = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_references' AND COLUMN_NAME = 'sequence');

SET @sql = IF(@has_employee_references_sequence = 0,
  'ALTER TABLE `employee_references` ADD COLUMN `sequence` int(11) NOT NULL DEFAULT 1',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_references_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_references' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_employee_references_created_at = 0,
  'ALTER TABLE `employee_references` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_references_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_references' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_employee_references_updated_at = 0,
  'ALTER TABLE `employee_references` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_skills_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_skills' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_employee_skills_id = 0,
  'ALTER TABLE `employee_skills` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_skills_user_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_skills' AND COLUMN_NAME = 'user_id');

SET @sql = IF(@has_employee_skills_user_id = 0,
  'ALTER TABLE `employee_skills` ADD COLUMN `user_id` bigint(20) unsigned NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_skills_skill_name = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_skills' AND COLUMN_NAME = 'skill_name');

SET @sql = IF(@has_employee_skills_skill_name = 0,
  'ALTER TABLE `employee_skills` ADD COLUMN `skill_name` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_skills_proficiency = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_skills' AND COLUMN_NAME = 'proficiency');

SET @sql = IF(@has_employee_skills_proficiency = 0,
  'ALTER TABLE `employee_skills` ADD COLUMN `proficiency` varchar(255) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_skills_sequence = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_skills' AND COLUMN_NAME = 'sequence');

SET @sql = IF(@has_employee_skills_sequence = 0,
  'ALTER TABLE `employee_skills` ADD COLUMN `sequence` int(11) NOT NULL DEFAULT 1',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_skills_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_skills' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_employee_skills_created_at = 0,
  'ALTER TABLE `employee_skills` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employee_skills_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_skills' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_employee_skills_updated_at = 0,
  'ALTER TABLE `employee_skills` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_holidays_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'holidays' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_holidays_id = 0,
  'ALTER TABLE `holidays` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_holidays_name = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'holidays' AND COLUMN_NAME = 'name');

SET @sql = IF(@has_holidays_name = 0,
  'ALTER TABLE `holidays` ADD COLUMN `name` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_holidays_date = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'holidays' AND COLUMN_NAME = 'date');

SET @sql = IF(@has_holidays_date = 0,
  'ALTER TABLE `holidays` ADD COLUMN `date` date NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_holidays_type = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'holidays' AND COLUMN_NAME = 'type');

SET @sql = IF(@has_holidays_type = 0,
  'ALTER TABLE `holidays` ADD COLUMN `type` varchar(255) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_holidays_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'holidays' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_holidays_created_at = 0,
  'ALTER TABLE `holidays` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_holidays_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'holidays' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_holidays_updated_at = 0,
  'ALTER TABLE `holidays` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_journal_entries_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'journal_entries' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_journal_entries_id = 0,
  'ALTER TABLE `journal_entries` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_journal_entries_journal_number = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'journal_entries' AND COLUMN_NAME = 'journal_number');

SET @sql = IF(@has_journal_entries_journal_number = 0,
  'ALTER TABLE `journal_entries` ADD COLUMN `journal_number` varchar(30) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_journal_entries_transaction_date = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'journal_entries' AND COLUMN_NAME = 'transaction_date');

SET @sql = IF(@has_journal_entries_transaction_date = 0,
  'ALTER TABLE `journal_entries` ADD COLUMN `transaction_date` date NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_journal_entries_description = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'journal_entries' AND COLUMN_NAME = 'description');

SET @sql = IF(@has_journal_entries_description = 0,
  'ALTER TABLE `journal_entries` ADD COLUMN `description` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_journal_entries_reference_type = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'journal_entries' AND COLUMN_NAME = 'reference_type');

SET @sql = IF(@has_journal_entries_reference_type = 0,
  'ALTER TABLE `journal_entries` ADD COLUMN `reference_type` varchar(50) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_journal_entries_reference_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'journal_entries' AND COLUMN_NAME = 'reference_id');

SET @sql = IF(@has_journal_entries_reference_id = 0,
  'ALTER TABLE `journal_entries` ADD COLUMN `reference_id` bigint(20) unsigned DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_journal_entries_status = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'journal_entries' AND COLUMN_NAME = 'status');

SET @sql = IF(@has_journal_entries_status = 0,
  'ALTER TABLE `journal_entries` ADD COLUMN `status` enum(\'Draft\',\'Posted\',\'Void\') NOT NULL DEFAULT \'Draft\'',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_journal_entries_created_by = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'journal_entries' AND COLUMN_NAME = 'created_by');

SET @sql = IF(@has_journal_entries_created_by = 0,
  'ALTER TABLE `journal_entries` ADD COLUMN `created_by` bigint(20) unsigned DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_journal_entries_posted_by = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'journal_entries' AND COLUMN_NAME = 'posted_by');

SET @sql = IF(@has_journal_entries_posted_by = 0,
  'ALTER TABLE `journal_entries` ADD COLUMN `posted_by` bigint(20) unsigned DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_journal_entries_posted_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'journal_entries' AND COLUMN_NAME = 'posted_at');

SET @sql = IF(@has_journal_entries_posted_at = 0,
  'ALTER TABLE `journal_entries` ADD COLUMN `posted_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_journal_entries_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'journal_entries' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_journal_entries_created_at = 0,
  'ALTER TABLE `journal_entries` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_journal_entries_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'journal_entries' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_journal_entries_updated_at = 0,
  'ALTER TABLE `journal_entries` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_journal_entry_lines_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'journal_entry_lines' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_journal_entry_lines_id = 0,
  'ALTER TABLE `journal_entry_lines` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_journal_entry_lines_journal_entry_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'journal_entry_lines' AND COLUMN_NAME = 'journal_entry_id');

SET @sql = IF(@has_journal_entry_lines_journal_entry_id = 0,
  'ALTER TABLE `journal_entry_lines` ADD COLUMN `journal_entry_id` bigint(20) unsigned NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_journal_entry_lines_account_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'journal_entry_lines' AND COLUMN_NAME = 'account_id');

SET @sql = IF(@has_journal_entry_lines_account_id = 0,
  'ALTER TABLE `journal_entry_lines` ADD COLUMN `account_id` bigint(20) unsigned NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_journal_entry_lines_description = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'journal_entry_lines' AND COLUMN_NAME = 'description');

SET @sql = IF(@has_journal_entry_lines_description = 0,
  'ALTER TABLE `journal_entry_lines` ADD COLUMN `description` varchar(255) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_journal_entry_lines_debit = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'journal_entry_lines' AND COLUMN_NAME = 'debit');

SET @sql = IF(@has_journal_entry_lines_debit = 0,
  'ALTER TABLE `journal_entry_lines` ADD COLUMN `debit` decimal(15,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_journal_entry_lines_credit = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'journal_entry_lines' AND COLUMN_NAME = 'credit');

SET @sql = IF(@has_journal_entry_lines_credit = 0,
  'ALTER TABLE `journal_entry_lines` ADD COLUMN `credit` decimal(15,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_journal_entry_lines_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'journal_entry_lines' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_journal_entry_lines_created_at = 0,
  'ALTER TABLE `journal_entry_lines` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_journal_entry_lines_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'journal_entry_lines' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_journal_entry_lines_updated_at = 0,
  'ALTER TABLE `journal_entry_lines` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_leave_types_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'leave_types' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_leave_types_id = 0,
  'ALTER TABLE `leave_types` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_leave_types_name = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'leave_types' AND COLUMN_NAME = 'name');

SET @sql = IF(@has_leave_types_name = 0,
  'ALTER TABLE `leave_types` ADD COLUMN `name` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_leave_types_abbreviation = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'leave_types' AND COLUMN_NAME = 'abbreviation');

SET @sql = IF(@has_leave_types_abbreviation = 0,
  'ALTER TABLE `leave_types` ADD COLUMN `abbreviation` varchar(255) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_leave_types_days_allowed = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'leave_types' AND COLUMN_NAME = 'days_allowed');

SET @sql = IF(@has_leave_types_days_allowed = 0,
  'ALTER TABLE `leave_types` ADD COLUMN `days_allowed` int(11) NOT NULL DEFAULT 0',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_leave_types_carry_over = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'leave_types' AND COLUMN_NAME = 'carry_over');

SET @sql = IF(@has_leave_types_carry_over = 0,
  'ALTER TABLE `leave_types` ADD COLUMN `carry_over` tinyint(1) NOT NULL DEFAULT 0',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_leave_types_description = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'leave_types' AND COLUMN_NAME = 'description');

SET @sql = IF(@has_leave_types_description = 0,
  'ALTER TABLE `leave_types` ADD COLUMN `description` text DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_leave_types_status = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'leave_types' AND COLUMN_NAME = 'status');

SET @sql = IF(@has_leave_types_status = 0,
  'ALTER TABLE `leave_types` ADD COLUMN `status` enum(\'active\',\'inactive\') NOT NULL DEFAULT \'active\'',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_leave_types_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'leave_types' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_leave_types_created_at = 0,
  'ALTER TABLE `leave_types` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_leave_types_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'leave_types' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_leave_types_updated_at = 0,
  'ALTER TABLE `leave_types` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_leaves_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'leaves' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_leaves_id = 0,
  'ALTER TABLE `leaves` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_leaves_user_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'leaves' AND COLUMN_NAME = 'user_id');

SET @sql = IF(@has_leaves_user_id = 0,
  'ALTER TABLE `leaves` ADD COLUMN `user_id` bigint(20) unsigned NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_leaves_leave_type_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'leaves' AND COLUMN_NAME = 'leave_type_id');

SET @sql = IF(@has_leaves_leave_type_id = 0,
  'ALTER TABLE `leaves` ADD COLUMN `leave_type_id` bigint(20) unsigned DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_leaves_leave_type = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'leaves' AND COLUMN_NAME = 'leave_type');

SET @sql = IF(@has_leaves_leave_type = 0,
  'ALTER TABLE `leaves` ADD COLUMN `leave_type` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_leaves_start_date = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'leaves' AND COLUMN_NAME = 'start_date');

SET @sql = IF(@has_leaves_start_date = 0,
  'ALTER TABLE `leaves` ADD COLUMN `start_date` date NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_leaves_end_date = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'leaves' AND COLUMN_NAME = 'end_date');

SET @sql = IF(@has_leaves_end_date = 0,
  'ALTER TABLE `leaves` ADD COLUMN `end_date` date NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_leaves_reason = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'leaves' AND COLUMN_NAME = 'reason');

SET @sql = IF(@has_leaves_reason = 0,
  'ALTER TABLE `leaves` ADD COLUMN `reason` text DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_leaves_status = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'leaves' AND COLUMN_NAME = 'status');

SET @sql = IF(@has_leaves_status = 0,
  'ALTER TABLE `leaves` ADD COLUMN `status` enum(\'pending\',\'approved\',\'rejected\',\'paid\') NOT NULL DEFAULT \'pending\'',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_leaves_paid_days = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'leaves' AND COLUMN_NAME = 'paid_days');

SET @sql = IF(@has_leaves_paid_days = 0,
  'ALTER TABLE `leaves` ADD COLUMN `paid_days` int(11) NOT NULL DEFAULT 0',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_leaves_unpaid_days = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'leaves' AND COLUMN_NAME = 'unpaid_days');

SET @sql = IF(@has_leaves_unpaid_days = 0,
  'ALTER TABLE `leaves` ADD COLUMN `unpaid_days` int(11) NOT NULL DEFAULT 0',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_leaves_rejection_reason = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'leaves' AND COLUMN_NAME = 'rejection_reason');

SET @sql = IF(@has_leaves_rejection_reason = 0,
  'ALTER TABLE `leaves` ADD COLUMN `rejection_reason` text DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_leaves_approved_by = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'leaves' AND COLUMN_NAME = 'approved_by');

SET @sql = IF(@has_leaves_approved_by = 0,
  'ALTER TABLE `leaves` ADD COLUMN `approved_by` bigint(20) unsigned DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_leaves_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'leaves' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_leaves_created_at = 0,
  'ALTER TABLE `leaves` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_leaves_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'leaves' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_leaves_updated_at = 0,
  'ALTER TABLE `leaves` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_migrations_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'migrations' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_migrations_id = 0,
  'ALTER TABLE `migrations` ADD COLUMN `id` int(10) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_migrations_migration = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'migrations' AND COLUMN_NAME = 'migration');

SET @sql = IF(@has_migrations_migration = 0,
  'ALTER TABLE `migrations` ADD COLUMN `migration` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_migrations_batch = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'migrations' AND COLUMN_NAME = 'batch');

SET @sql = IF(@has_migrations_batch = 0,
  'ALTER TABLE `migrations` ADD COLUMN `batch` int(11) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_notifications_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'notifications' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_notifications_id = 0,
  'ALTER TABLE `notifications` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_notifications_user_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'notifications' AND COLUMN_NAME = 'user_id');

SET @sql = IF(@has_notifications_user_id = 0,
  'ALTER TABLE `notifications` ADD COLUMN `user_id` bigint(20) unsigned NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_notifications_type = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'notifications' AND COLUMN_NAME = 'type');

SET @sql = IF(@has_notifications_type = 0,
  'ALTER TABLE `notifications` ADD COLUMN `type` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_notifications_title = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'notifications' AND COLUMN_NAME = 'title');

SET @sql = IF(@has_notifications_title = 0,
  'ALTER TABLE `notifications` ADD COLUMN `title` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_notifications_message = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'notifications' AND COLUMN_NAME = 'message');

SET @sql = IF(@has_notifications_message = 0,
  'ALTER TABLE `notifications` ADD COLUMN `message` text NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_notifications_data = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'notifications' AND COLUMN_NAME = 'data');

SET @sql = IF(@has_notifications_data = 0,
  'ALTER TABLE `notifications` ADD COLUMN `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`))',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_notifications_read_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'notifications' AND COLUMN_NAME = 'read_at');

SET @sql = IF(@has_notifications_read_at = 0,
  'ALTER TABLE `notifications` ADD COLUMN `read_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_notifications_deleted_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'notifications' AND COLUMN_NAME = 'deleted_at');

SET @sql = IF(@has_notifications_deleted_at = 0,
  'ALTER TABLE `notifications` ADD COLUMN `deleted_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_notifications_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'notifications' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_notifications_created_at = 0,
  'ALTER TABLE `notifications` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_notifications_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'notifications' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_notifications_updated_at = 0,
  'ALTER TABLE `notifications` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_overtime_undertimes_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'overtime_undertimes' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_overtime_undertimes_id = 0,
  'ALTER TABLE `overtime_undertimes` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_overtime_undertimes_user_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'overtime_undertimes' AND COLUMN_NAME = 'user_id');

SET @sql = IF(@has_overtime_undertimes_user_id = 0,
  'ALTER TABLE `overtime_undertimes` ADD COLUMN `user_id` bigint(20) unsigned NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_overtime_undertimes_date = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'overtime_undertimes' AND COLUMN_NAME = 'date');

SET @sql = IF(@has_overtime_undertimes_date = 0,
  'ALTER TABLE `overtime_undertimes` ADD COLUMN `date` date NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_overtime_undertimes_type = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'overtime_undertimes' AND COLUMN_NAME = 'type');

SET @sql = IF(@has_overtime_undertimes_type = 0,
  'ALTER TABLE `overtime_undertimes` ADD COLUMN `type` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_overtime_undertimes_hours = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'overtime_undertimes' AND COLUMN_NAME = 'hours');

SET @sql = IF(@has_overtime_undertimes_hours = 0,
  'ALTER TABLE `overtime_undertimes` ADD COLUMN `hours` decimal(5,2) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_overtime_undertimes_amount = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'overtime_undertimes' AND COLUMN_NAME = 'amount');

SET @sql = IF(@has_overtime_undertimes_amount = 0,
  'ALTER TABLE `overtime_undertimes` ADD COLUMN `amount` decimal(15,2) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_overtime_undertimes_hourly_rate_used = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'overtime_undertimes' AND COLUMN_NAME = 'hourly_rate_used');

SET @sql = IF(@has_overtime_undertimes_hourly_rate_used = 0,
  'ALTER TABLE `overtime_undertimes` ADD COLUMN `hourly_rate_used` decimal(15,2) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_overtime_undertimes_reason = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'overtime_undertimes' AND COLUMN_NAME = 'reason');

SET @sql = IF(@has_overtime_undertimes_reason = 0,
  'ALTER TABLE `overtime_undertimes` ADD COLUMN `reason` text DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_overtime_undertimes_status = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'overtime_undertimes' AND COLUMN_NAME = 'status');

SET @sql = IF(@has_overtime_undertimes_status = 0,
  'ALTER TABLE `overtime_undertimes` ADD COLUMN `status` varchar(255) NOT NULL DEFAULT \'pending\'',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_overtime_undertimes_rejection_reason = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'overtime_undertimes' AND COLUMN_NAME = 'rejection_reason');

SET @sql = IF(@has_overtime_undertimes_rejection_reason = 0,
  'ALTER TABLE `overtime_undertimes` ADD COLUMN `rejection_reason` text DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_overtime_undertimes_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'overtime_undertimes' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_overtime_undertimes_created_at = 0,
  'ALTER TABLE `overtime_undertimes` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_overtime_undertimes_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'overtime_undertimes' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_overtime_undertimes_updated_at = 0,
  'ALTER TABLE `overtime_undertimes` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_overtime_undertimes_approved_by = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'overtime_undertimes' AND COLUMN_NAME = 'approved_by');

SET @sql = IF(@has_overtime_undertimes_approved_by = 0,
  'ALTER TABLE `overtime_undertimes` ADD COLUMN `approved_by` bigint(20) unsigned DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_paos_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'paos' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_paos_id = 0,
  'ALTER TABLE `paos` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_paos_name = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'paos' AND COLUMN_NAME = 'name');

SET @sql = IF(@has_paos_name = 0,
  'ALTER TABLE `paos` ADD COLUMN `name` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_paos_contact_number = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'paos' AND COLUMN_NAME = 'contact_number');

SET @sql = IF(@has_paos_contact_number = 0,
  'ALTER TABLE `paos` ADD COLUMN `contact_number` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_paos_gender = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'paos' AND COLUMN_NAME = 'gender');

SET @sql = IF(@has_paos_gender = 0,
  'ALTER TABLE `paos` ADD COLUMN `gender` enum(\'male\',\'female\',\'prefer_not_to_say\') DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_paos_email = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'paos' AND COLUMN_NAME = 'email');

SET @sql = IF(@has_paos_email = 0,
  'ALTER TABLE `paos` ADD COLUMN `email` varchar(255) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_paos_address = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'paos' AND COLUMN_NAME = 'address');

SET @sql = IF(@has_paos_address = 0,
  'ALTER TABLE `paos` ADD COLUMN `address` text DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_paos_date_of_hire = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'paos' AND COLUMN_NAME = 'date_of_hire');

SET @sql = IF(@has_paos_date_of_hire = 0,
  'ALTER TABLE `paos` ADD COLUMN `date_of_hire` date DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_paos_status = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'paos' AND COLUMN_NAME = 'status');

SET @sql = IF(@has_paos_status = 0,
  'ALTER TABLE `paos` ADD COLUMN `status` varchar(255) NOT NULL DEFAULT \'active\'',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_paos_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'paos' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_paos_created_at = 0,
  'ALTER TABLE `paos` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_paos_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'paos' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_paos_updated_at = 0,
  'ALTER TABLE `paos` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_allowances_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_allowances' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_payroll_allowances_id = 0,
  'ALTER TABLE `payroll_allowances` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_allowances_payroll_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_allowances' AND COLUMN_NAME = 'payroll_id');

SET @sql = IF(@has_payroll_allowances_payroll_id = 0,
  'ALTER TABLE `payroll_allowances` ADD COLUMN `payroll_id` bigint(20) unsigned NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_allowances_allowance_type = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_allowances' AND COLUMN_NAME = 'allowance_type');

SET @sql = IF(@has_payroll_allowances_allowance_type = 0,
  'ALTER TABLE `payroll_allowances` ADD COLUMN `allowance_type` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_allowances_hours = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_allowances' AND COLUMN_NAME = 'hours');

SET @sql = IF(@has_payroll_allowances_hours = 0,
  'ALTER TABLE `payroll_allowances` ADD COLUMN `hours` decimal(8,2) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_allowances_amount = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_allowances' AND COLUMN_NAME = 'amount');

SET @sql = IF(@has_payroll_allowances_amount = 0,
  'ALTER TABLE `payroll_allowances` ADD COLUMN `amount` decimal(12,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_allowances_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_allowances' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_payroll_allowances_created_at = 0,
  'ALTER TABLE `payroll_allowances` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_allowances_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_allowances' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_payroll_allowances_updated_at = 0,
  'ALTER TABLE `payroll_allowances` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_batches_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_batches' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_payroll_batches_id = 0,
  'ALTER TABLE `payroll_batches` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_batches_type = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_batches' AND COLUMN_NAME = 'type');

SET @sql = IF(@has_payroll_batches_type = 0,
  'ALTER TABLE `payroll_batches` ADD COLUMN `type` varchar(50) NOT NULL DEFAULT \'regular\'',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_batches_period_start = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_batches' AND COLUMN_NAME = 'period_start');

SET @sql = IF(@has_payroll_batches_period_start = 0,
  'ALTER TABLE `payroll_batches` ADD COLUMN `period_start` date NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_batches_period_end = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_batches' AND COLUMN_NAME = 'period_end');

SET @sql = IF(@has_payroll_batches_period_end = 0,
  'ALTER TABLE `payroll_batches` ADD COLUMN `period_end` date NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_batches_status = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_batches' AND COLUMN_NAME = 'status');

SET @sql = IF(@has_payroll_batches_status = 0,
  'ALTER TABLE `payroll_batches` ADD COLUMN `status` enum(\'draft\',\'submitted\',\'approved\',\'rejected\') NOT NULL DEFAULT \'draft\'',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_batches_generated_by = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_batches' AND COLUMN_NAME = 'generated_by');

SET @sql = IF(@has_payroll_batches_generated_by = 0,
  'ALTER TABLE `payroll_batches` ADD COLUMN `generated_by` bigint(20) unsigned DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_batches_finalized_by = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_batches' AND COLUMN_NAME = 'finalized_by');

SET @sql = IF(@has_payroll_batches_finalized_by = 0,
  'ALTER TABLE `payroll_batches` ADD COLUMN `finalized_by` bigint(20) unsigned DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_batches_finalized_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_batches' AND COLUMN_NAME = 'finalized_at');

SET @sql = IF(@has_payroll_batches_finalized_at = 0,
  'ALTER TABLE `payroll_batches` ADD COLUMN `finalized_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_batches_approved_by = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_batches' AND COLUMN_NAME = 'approved_by');

SET @sql = IF(@has_payroll_batches_approved_by = 0,
  'ALTER TABLE `payroll_batches` ADD COLUMN `approved_by` bigint(20) unsigned DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_batches_approved_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_batches' AND COLUMN_NAME = 'approved_at');

SET @sql = IF(@has_payroll_batches_approved_at = 0,
  'ALTER TABLE `payroll_batches` ADD COLUMN `approved_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_batches_rejected_by = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_batches' AND COLUMN_NAME = 'rejected_by');

SET @sql = IF(@has_payroll_batches_rejected_by = 0,
  'ALTER TABLE `payroll_batches` ADD COLUMN `rejected_by` bigint(20) unsigned DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_batches_rejected_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_batches' AND COLUMN_NAME = 'rejected_at');

SET @sql = IF(@has_payroll_batches_rejected_at = 0,
  'ALTER TABLE `payroll_batches` ADD COLUMN `rejected_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_batches_rejection_note = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_batches' AND COLUMN_NAME = 'rejection_note');

SET @sql = IF(@has_payroll_batches_rejection_note = 0,
  'ALTER TABLE `payroll_batches` ADD COLUMN `rejection_note` text DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_batches_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_batches' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_payroll_batches_created_at = 0,
  'ALTER TABLE `payroll_batches` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_batches_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_batches' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_payroll_batches_updated_at = 0,
  'ALTER TABLE `payroll_batches` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_bonuses_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_bonuses' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_payroll_bonuses_id = 0,
  'ALTER TABLE `payroll_bonuses` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_bonuses_payroll_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_bonuses' AND COLUMN_NAME = 'payroll_id');

SET @sql = IF(@has_payroll_bonuses_payroll_id = 0,
  'ALTER TABLE `payroll_bonuses` ADD COLUMN `payroll_id` bigint(20) unsigned NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_bonuses_bonus_type = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_bonuses' AND COLUMN_NAME = 'bonus_type');

SET @sql = IF(@has_payroll_bonuses_bonus_type = 0,
  'ALTER TABLE `payroll_bonuses` ADD COLUMN `bonus_type` enum(\'performance\',\'holiday\',\'attendance\',\'special\') NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_bonuses_description = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_bonuses' AND COLUMN_NAME = 'description');

SET @sql = IF(@has_payroll_bonuses_description = 0,
  'ALTER TABLE `payroll_bonuses` ADD COLUMN `description` varchar(255) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_bonuses_amount = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_bonuses' AND COLUMN_NAME = 'amount');

SET @sql = IF(@has_payroll_bonuses_amount = 0,
  'ALTER TABLE `payroll_bonuses` ADD COLUMN `amount` decimal(12,2) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_bonuses_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_bonuses' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_payroll_bonuses_created_at = 0,
  'ALTER TABLE `payroll_bonuses` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_bonuses_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_bonuses' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_payroll_bonuses_updated_at = 0,
  'ALTER TABLE `payroll_bonuses` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_cutoff_schedules_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_cutoff_schedules' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_payroll_cutoff_schedules_id = 0,
  'ALTER TABLE `payroll_cutoff_schedules` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_cutoff_schedules_cutoff_day = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_cutoff_schedules' AND COLUMN_NAME = 'cutoff_day');

SET @sql = IF(@has_payroll_cutoff_schedules_cutoff_day = 0,
  'ALTER TABLE `payroll_cutoff_schedules` ADD COLUMN `cutoff_day` int(11) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_cutoff_schedules_label = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_cutoff_schedules' AND COLUMN_NAME = 'label');

SET @sql = IF(@has_payroll_cutoff_schedules_label = 0,
  'ALTER TABLE `payroll_cutoff_schedules` ADD COLUMN `label` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_cutoff_schedules_payroll_period_start = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_cutoff_schedules' AND COLUMN_NAME = 'payroll_period_start');

SET @sql = IF(@has_payroll_cutoff_schedules_payroll_period_start = 0,
  'ALTER TABLE `payroll_cutoff_schedules` ADD COLUMN `payroll_period_start` date DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_cutoff_schedules_payroll_period_end = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_cutoff_schedules' AND COLUMN_NAME = 'payroll_period_end');

SET @sql = IF(@has_payroll_cutoff_schedules_payroll_period_end = 0,
  'ALTER TABLE `payroll_cutoff_schedules` ADD COLUMN `payroll_period_end` date DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_cutoff_schedules_is_active = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_cutoff_schedules' AND COLUMN_NAME = 'is_active');

SET @sql = IF(@has_payroll_cutoff_schedules_is_active = 0,
  'ALTER TABLE `payroll_cutoff_schedules` ADD COLUMN `is_active` tinyint(1) NOT NULL DEFAULT 1',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_cutoff_schedules_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_cutoff_schedules' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_payroll_cutoff_schedules_created_at = 0,
  'ALTER TABLE `payroll_cutoff_schedules` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_cutoff_schedules_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_cutoff_schedules' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_payroll_cutoff_schedules_updated_at = 0,
  'ALTER TABLE `payroll_cutoff_schedules` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_deductions_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_deductions' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_payroll_deductions_id = 0,
  'ALTER TABLE `payroll_deductions` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_deductions_payroll_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_deductions' AND COLUMN_NAME = 'payroll_id');

SET @sql = IF(@has_payroll_deductions_payroll_id = 0,
  'ALTER TABLE `payroll_deductions` ADD COLUMN `payroll_id` bigint(20) unsigned NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_deductions_deduction_type = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_deductions' AND COLUMN_NAME = 'deduction_type');

SET @sql = IF(@has_payroll_deductions_deduction_type = 0,
  'ALTER TABLE `payroll_deductions` ADD COLUMN `deduction_type` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_deductions_hours = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_deductions' AND COLUMN_NAME = 'hours');

SET @sql = IF(@has_payroll_deductions_hours = 0,
  'ALTER TABLE `payroll_deductions` ADD COLUMN `hours` decimal(8,2) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_deductions_amount = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_deductions' AND COLUMN_NAME = 'amount');

SET @sql = IF(@has_payroll_deductions_amount = 0,
  'ALTER TABLE `payroll_deductions` ADD COLUMN `amount` decimal(10,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_deductions_description = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_deductions' AND COLUMN_NAME = 'description');

SET @sql = IF(@has_payroll_deductions_description = 0,
  'ALTER TABLE `payroll_deductions` ADD COLUMN `description` text DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_deductions_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_deductions' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_payroll_deductions_created_at = 0,
  'ALTER TABLE `payroll_deductions` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_deductions_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_deductions' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_payroll_deductions_updated_at = 0,
  'ALTER TABLE `payroll_deductions` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_deductions_accounting_treatment = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_deductions' AND COLUMN_NAME = 'accounting_treatment');

SET @sql = IF(@has_payroll_deductions_accounting_treatment = 0,
  'ALTER TABLE `payroll_deductions` ADD COLUMN `accounting_treatment` varchar(30) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payroll_deductions_deduction_source = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_deductions' AND COLUMN_NAME = 'deduction_source');

SET @sql = IF(@has_payroll_deductions_deduction_source = 0,
  'ALTER TABLE `payroll_deductions` ADD COLUMN `deduction_source` varchar(20) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payrolls_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_payrolls_id = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payrolls_batch_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'batch_id');

SET @sql = IF(@has_payrolls_batch_id = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `batch_id` bigint(20) unsigned DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payrolls_user_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'user_id');

SET @sql = IF(@has_payrolls_user_id = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `user_id` bigint(20) unsigned NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payrolls_payroll_period_start = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'payroll_period_start');

SET @sql = IF(@has_payrolls_payroll_period_start = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `payroll_period_start` date NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payrolls_payroll_period_end = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'payroll_period_end');

SET @sql = IF(@has_payrolls_payroll_period_end = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `payroll_period_end` date NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payrolls_total_allowances = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'total_allowances');

SET @sql = IF(@has_payrolls_total_allowances = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `total_allowances` decimal(10,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payrolls_total_bonuses = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'total_bonuses');

SET @sql = IF(@has_payrolls_total_bonuses = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `total_bonuses` decimal(12,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payrolls_total_deductions = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'total_deductions');

SET @sql = IF(@has_payrolls_total_deductions = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `total_deductions` decimal(10,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payrolls_cash_advance_deduction = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'cash_advance_deduction');

SET @sql = IF(@has_payrolls_cash_advance_deduction = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `cash_advance_deduction` decimal(12,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payrolls_salary_loan_deduction = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'salary_loan_deduction');

SET @sql = IF(@has_payrolls_salary_loan_deduction = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `salary_loan_deduction` decimal(12,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payrolls_loan_deduction_data = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'loan_deduction_data');

SET @sql = IF(@has_payrolls_loan_deduction_data = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `loan_deduction_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`loan_deduction_data`))',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payrolls_status = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'status');

SET @sql = IF(@has_payrolls_status = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `status` varchar(255) NOT NULL DEFAULT \'pending\'',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payrolls_basic_salary = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'basic_salary');

SET @sql = IF(@has_payrolls_basic_salary = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `basic_salary` decimal(12,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payrolls_gross_pay = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'gross_pay');

SET @sql = IF(@has_payrolls_gross_pay = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `gross_pay` decimal(12,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payrolls_holiday_pay = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'holiday_pay');

SET @sql = IF(@has_payrolls_holiday_pay = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `holiday_pay` decimal(12,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payrolls_holiday_ot_pay = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'holiday_ot_pay');

SET @sql = IF(@has_payrolls_holiday_ot_pay = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `holiday_ot_pay` decimal(12,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payrolls_holiday_ot_hours = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'holiday_ot_hours');

SET @sql = IF(@has_payrolls_holiday_ot_hours = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `holiday_ot_hours` decimal(8,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payrolls_holiday_breakdown = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'holiday_breakdown');

SET @sql = IF(@has_payrolls_holiday_breakdown = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `holiday_breakdown` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`holiday_breakdown`))',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payrolls_net_pay = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'net_pay');

SET @sql = IF(@has_payrolls_net_pay = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `net_pay` decimal(12,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payrolls_days_worked = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'days_worked');

SET @sql = IF(@has_payrolls_days_worked = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `days_worked` decimal(8,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payrolls_hours_worked = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'hours_worked');

SET @sql = IF(@has_payrolls_hours_worked = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `hours_worked` decimal(8,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payrolls_payment_date = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'payment_date');

SET @sql = IF(@has_payrolls_payment_date = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `payment_date` date DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payrolls_approved_by = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'approved_by');

SET @sql = IF(@has_payrolls_approved_by = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `approved_by` bigint(20) unsigned DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payrolls_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_payrolls_created_at = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payrolls_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_payrolls_updated_at = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payrolls_present_days = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'present_days');

SET @sql = IF(@has_payrolls_present_days = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `present_days` int(10) unsigned NOT NULL DEFAULT 0',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payrolls_late_days = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'late_days');

SET @sql = IF(@has_payrolls_late_days = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `late_days` int(10) unsigned NOT NULL DEFAULT 0',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payrolls_absent_days = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'absent_days');

SET @sql = IF(@has_payrolls_absent_days = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `absent_days` int(10) unsigned NOT NULL DEFAULT 0',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payrolls_daily_rate_snapshot = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'daily_rate_snapshot');

SET @sql = IF(@has_payrolls_daily_rate_snapshot = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `daily_rate_snapshot` decimal(10,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payrolls_sss = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'sss');

SET @sql = IF(@has_payrolls_sss = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `sss` decimal(10,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payrolls_pagibig = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'pagibig');

SET @sql = IF(@has_payrolls_pagibig = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `pagibig` decimal(10,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payrolls_philhealth = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'philhealth');

SET @sql = IF(@has_payrolls_philhealth = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `philhealth` decimal(10,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payrolls_withholding_tax = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'withholding_tax');

SET @sql = IF(@has_payrolls_withholding_tax = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `withholding_tax` decimal(12,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payrolls_employer_sss = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'employer_sss');

SET @sql = IF(@has_payrolls_employer_sss = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `employer_sss` decimal(12,2) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payrolls_employer_pagibig = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'employer_pagibig');

SET @sql = IF(@has_payrolls_employer_pagibig = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `employer_pagibig` decimal(12,2) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payrolls_employer_philhealth = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'employer_philhealth');

SET @sql = IF(@has_payrolls_employer_philhealth = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `employer_philhealth` decimal(12,2) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_pinned_items_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'pinned_items' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_pinned_items_id = 0,
  'ALTER TABLE `pinned_items` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_pinned_items_user_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'pinned_items' AND COLUMN_NAME = 'user_id');

SET @sql = IF(@has_pinned_items_user_id = 0,
  'ALTER TABLE `pinned_items` ADD COLUMN `user_id` bigint(20) unsigned NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_pinned_items_label = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'pinned_items' AND COLUMN_NAME = 'label');

SET @sql = IF(@has_pinned_items_label = 0,
  'ALTER TABLE `pinned_items` ADD COLUMN `label` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_pinned_items_url = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'pinned_items' AND COLUMN_NAME = 'url');

SET @sql = IF(@has_pinned_items_url = 0,
  'ALTER TABLE `pinned_items` ADD COLUMN `url` varchar(500) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_pinned_items_sort_order = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'pinned_items' AND COLUMN_NAME = 'sort_order');

SET @sql = IF(@has_pinned_items_sort_order = 0,
  'ALTER TABLE `pinned_items` ADD COLUMN `sort_order` smallint(5) unsigned NOT NULL DEFAULT 0',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_pinned_items_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'pinned_items' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_pinned_items_created_at = 0,
  'ALTER TABLE `pinned_items` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_pinned_items_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'pinned_items' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_pinned_items_updated_at = 0,
  'ALTER TABLE `pinned_items` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_position_rates_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'position_rates' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_position_rates_id = 0,
  'ALTER TABLE `position_rates` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_position_rates_department_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'position_rates' AND COLUMN_NAME = 'department_id');

SET @sql = IF(@has_position_rates_department_id = 0,
  'ALTER TABLE `position_rates` ADD COLUMN `department_id` bigint(20) unsigned DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_position_rates_name = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'position_rates' AND COLUMN_NAME = 'name');

SET @sql = IF(@has_position_rates_name = 0,
  'ALTER TABLE `position_rates` ADD COLUMN `name` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_position_rates_daily_rate = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'position_rates' AND COLUMN_NAME = 'daily_rate');

SET @sql = IF(@has_position_rates_daily_rate = 0,
  'ALTER TABLE `position_rates` ADD COLUMN `daily_rate` decimal(10,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_position_rates_is_active = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'position_rates' AND COLUMN_NAME = 'is_active');

SET @sql = IF(@has_position_rates_is_active = 0,
  'ALTER TABLE `position_rates` ADD COLUMN `is_active` tinyint(1) NOT NULL DEFAULT 1',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_position_rates_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'position_rates' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_position_rates_created_at = 0,
  'ALTER TABLE `position_rates` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_position_rates_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'position_rates' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_position_rates_updated_at = 0,
  'ALTER TABLE `position_rates` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_routes_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'routes' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_routes_id = 0,
  'ALTER TABLE `routes` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_routes_route_name = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'routes' AND COLUMN_NAME = 'route_name');

SET @sql = IF(@has_routes_route_name = 0,
  'ALTER TABLE `routes` ADD COLUMN `route_name` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_routes_weekday_boundary = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'routes' AND COLUMN_NAME = 'weekday_boundary');

SET @sql = IF(@has_routes_weekday_boundary = 0,
  'ALTER TABLE `routes` ADD COLUMN `weekday_boundary` decimal(10,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_routes_weekend_boundary = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'routes' AND COLUMN_NAME = 'weekend_boundary');

SET @sql = IF(@has_routes_weekend_boundary = 0,
  'ALTER TABLE `routes` ADD COLUMN `weekend_boundary` decimal(10,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_routes_origin = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'routes' AND COLUMN_NAME = 'origin');

SET @sql = IF(@has_routes_origin = 0,
  'ALTER TABLE `routes` ADD COLUMN `origin` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_routes_destination = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'routes' AND COLUMN_NAME = 'destination');

SET @sql = IF(@has_routes_destination = 0,
  'ALTER TABLE `routes` ADD COLUMN `destination` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_routes_status = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'routes' AND COLUMN_NAME = 'status');

SET @sql = IF(@has_routes_status = 0,
  'ALTER TABLE `routes` ADD COLUMN `status` varchar(255) NOT NULL DEFAULT \'active\'',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_routes_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'routes' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_routes_created_at = 0,
  'ALTER TABLE `routes` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_routes_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'routes' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_routes_updated_at = 0,
  'ALTER TABLE `routes` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_sessions_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sessions' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_sessions_id = 0,
  'ALTER TABLE `sessions` ADD COLUMN `id` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_sessions_user_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sessions' AND COLUMN_NAME = 'user_id');

SET @sql = IF(@has_sessions_user_id = 0,
  'ALTER TABLE `sessions` ADD COLUMN `user_id` bigint(20) unsigned DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_sessions_ip_address = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sessions' AND COLUMN_NAME = 'ip_address');

SET @sql = IF(@has_sessions_ip_address = 0,
  'ALTER TABLE `sessions` ADD COLUMN `ip_address` varchar(45) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_sessions_user_agent = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sessions' AND COLUMN_NAME = 'user_agent');

SET @sql = IF(@has_sessions_user_agent = 0,
  'ALTER TABLE `sessions` ADD COLUMN `user_agent` text DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_sessions_payload = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sessions' AND COLUMN_NAME = 'payload');

SET @sql = IF(@has_sessions_payload = 0,
  'ALTER TABLE `sessions` ADD COLUMN `payload` longtext NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_sessions_last_activity = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sessions' AND COLUMN_NAME = 'last_activity');

SET @sql = IF(@has_sessions_last_activity = 0,
  'ALTER TABLE `sessions` ADD COLUMN `last_activity` int(11) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_settings_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'settings' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_settings_id = 0,
  'ALTER TABLE `settings` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_settings_key = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'settings' AND COLUMN_NAME = 'key');

SET @sql = IF(@has_settings_key = 0,
  'ALTER TABLE `settings` ADD COLUMN `key` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_settings_value = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'settings' AND COLUMN_NAME = 'value');

SET @sql = IF(@has_settings_value = 0,
  'ALTER TABLE `settings` ADD COLUMN `value` text DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_settings_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'settings' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_settings_created_at = 0,
  'ALTER TABLE `settings` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_settings_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'settings' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_settings_updated_at = 0,
  'ALTER TABLE `settings` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_shifts_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'shifts' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_shifts_id = 0,
  'ALTER TABLE `shifts` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_shifts_name = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'shifts' AND COLUMN_NAME = 'name');

SET @sql = IF(@has_shifts_name = 0,
  'ALTER TABLE `shifts` ADD COLUMN `name` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_shifts_start_time = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'shifts' AND COLUMN_NAME = 'start_time');

SET @sql = IF(@has_shifts_start_time = 0,
  'ALTER TABLE `shifts` ADD COLUMN `start_time` time NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_shifts_end_time = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'shifts' AND COLUMN_NAME = 'end_time');

SET @sql = IF(@has_shifts_end_time = 0,
  'ALTER TABLE `shifts` ADD COLUMN `end_time` time NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_shifts_break_start = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'shifts' AND COLUMN_NAME = 'break_start');

SET @sql = IF(@has_shifts_break_start = 0,
  'ALTER TABLE `shifts` ADD COLUMN `break_start` time DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_shifts_break_end = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'shifts' AND COLUMN_NAME = 'break_end');

SET @sql = IF(@has_shifts_break_end = 0,
  'ALTER TABLE `shifts` ADD COLUMN `break_end` time DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_shifts_is_active = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'shifts' AND COLUMN_NAME = 'is_active');

SET @sql = IF(@has_shifts_is_active = 0,
  'ALTER TABLE `shifts` ADD COLUMN `is_active` tinyint(1) NOT NULL DEFAULT 1',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_shifts_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'shifts' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_shifts_created_at = 0,
  'ALTER TABLE `shifts` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_shifts_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'shifts' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_shifts_updated_at = 0,
  'ALTER TABLE `shifts` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_statutory_deductions_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'statutory_deductions' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_statutory_deductions_id = 0,
  'ALTER TABLE `statutory_deductions` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_statutory_deductions_name = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'statutory_deductions' AND COLUMN_NAME = 'name');

SET @sql = IF(@has_statutory_deductions_name = 0,
  'ALTER TABLE `statutory_deductions` ADD COLUMN `name` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_statutory_deductions_min_salary = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'statutory_deductions' AND COLUMN_NAME = 'min_salary');

SET @sql = IF(@has_statutory_deductions_min_salary = 0,
  'ALTER TABLE `statutory_deductions` ADD COLUMN `min_salary` decimal(12,2) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_statutory_deductions_max_salary = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'statutory_deductions' AND COLUMN_NAME = 'max_salary');

SET @sql = IF(@has_statutory_deductions_max_salary = 0,
  'ALTER TABLE `statutory_deductions` ADD COLUMN `max_salary` decimal(12,2) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_statutory_deductions_employee_share = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'statutory_deductions' AND COLUMN_NAME = 'employee_share');

SET @sql = IF(@has_statutory_deductions_employee_share = 0,
  'ALTER TABLE `statutory_deductions` ADD COLUMN `employee_share` decimal(12,2) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_statutory_deductions_employer_share = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'statutory_deductions' AND COLUMN_NAME = 'employer_share');

SET @sql = IF(@has_statutory_deductions_employer_share = 0,
  'ALTER TABLE `statutory_deductions` ADD COLUMN `employer_share` decimal(12,2) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_statutory_deductions_percentage_employee = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'statutory_deductions' AND COLUMN_NAME = 'percentage_employee');

SET @sql = IF(@has_statutory_deductions_percentage_employee = 0,
  'ALTER TABLE `statutory_deductions` ADD COLUMN `percentage_employee` decimal(5,2) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_statutory_deductions_percentage_employer = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'statutory_deductions' AND COLUMN_NAME = 'percentage_employer');

SET @sql = IF(@has_statutory_deductions_percentage_employer = 0,
  'ALTER TABLE `statutory_deductions` ADD COLUMN `percentage_employer` decimal(5,2) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_statutory_deductions_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'statutory_deductions' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_statutory_deductions_created_at = 0,
  'ALTER TABLE `statutory_deductions` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_statutory_deductions_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'statutory_deductions' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_statutory_deductions_updated_at = 0,
  'ALTER TABLE `statutory_deductions` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_thirteenth_month_pays_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'thirteenth_month_pays' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_thirteenth_month_pays_id = 0,
  'ALTER TABLE `thirteenth_month_pays` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_thirteenth_month_pays_batch_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'thirteenth_month_pays' AND COLUMN_NAME = 'batch_id');

SET @sql = IF(@has_thirteenth_month_pays_batch_id = 0,
  'ALTER TABLE `thirteenth_month_pays` ADD COLUMN `batch_id` bigint(20) unsigned DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_thirteenth_month_pays_user_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'thirteenth_month_pays' AND COLUMN_NAME = 'user_id');

SET @sql = IF(@has_thirteenth_month_pays_user_id = 0,
  'ALTER TABLE `thirteenth_month_pays` ADD COLUMN `user_id` bigint(20) unsigned NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_thirteenth_month_pays_calendar_year = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'thirteenth_month_pays' AND COLUMN_NAME = 'calendar_year');

SET @sql = IF(@has_thirteenth_month_pays_calendar_year = 0,
  'ALTER TABLE `thirteenth_month_pays` ADD COLUMN `calendar_year` smallint(5) unsigned NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_thirteenth_month_pays_total_basic_salary_earned = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'thirteenth_month_pays' AND COLUMN_NAME = 'total_basic_salary_earned');

SET @sql = IF(@has_thirteenth_month_pays_total_basic_salary_earned = 0,
  'ALTER TABLE `thirteenth_month_pays` ADD COLUMN `total_basic_salary_earned` decimal(14,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_thirteenth_month_pays_thirteenth_month_pay = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'thirteenth_month_pays' AND COLUMN_NAME = 'thirteenth_month_pay');

SET @sql = IF(@has_thirteenth_month_pays_thirteenth_month_pay = 0,
  'ALTER TABLE `thirteenth_month_pays` ADD COLUMN `thirteenth_month_pay` decimal(14,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_thirteenth_month_pays_months_worked = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'thirteenth_month_pays' AND COLUMN_NAME = 'months_worked');

SET @sql = IF(@has_thirteenth_month_pays_months_worked = 0,
  'ALTER TABLE `thirteenth_month_pays` ADD COLUMN `months_worked` decimal(5,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_thirteenth_month_pays_is_eligible = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'thirteenth_month_pays' AND COLUMN_NAME = 'is_eligible');

SET @sql = IF(@has_thirteenth_month_pays_is_eligible = 0,
  'ALTER TABLE `thirteenth_month_pays` ADD COLUMN `is_eligible` tinyint(1) NOT NULL DEFAULT 1',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_thirteenth_month_pays_amount_paid = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'thirteenth_month_pays' AND COLUMN_NAME = 'amount_paid');

SET @sql = IF(@has_thirteenth_month_pays_amount_paid = 0,
  'ALTER TABLE `thirteenth_month_pays` ADD COLUMN `amount_paid` decimal(14,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_thirteenth_month_pays_amount_remaining = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'thirteenth_month_pays' AND COLUMN_NAME = 'amount_remaining');

SET @sql = IF(@has_thirteenth_month_pays_amount_remaining = 0,
  'ALTER TABLE `thirteenth_month_pays` ADD COLUMN `amount_remaining` decimal(14,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_thirteenth_month_pays_status = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'thirteenth_month_pays' AND COLUMN_NAME = 'status');

SET @sql = IF(@has_thirteenth_month_pays_status = 0,
  'ALTER TABLE `thirteenth_month_pays` ADD COLUMN `status` varchar(255) NOT NULL DEFAULT \'pending\'',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_thirteenth_month_pays_payment_date = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'thirteenth_month_pays' AND COLUMN_NAME = 'payment_date');

SET @sql = IF(@has_thirteenth_month_pays_payment_date = 0,
  'ALTER TABLE `thirteenth_month_pays` ADD COLUMN `payment_date` date DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_thirteenth_month_pays_paid_by = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'thirteenth_month_pays' AND COLUMN_NAME = 'paid_by');

SET @sql = IF(@has_thirteenth_month_pays_paid_by = 0,
  'ALTER TABLE `thirteenth_month_pays` ADD COLUMN `paid_by` bigint(20) unsigned DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_thirteenth_month_pays_notes = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'thirteenth_month_pays' AND COLUMN_NAME = 'notes');

SET @sql = IF(@has_thirteenth_month_pays_notes = 0,
  'ALTER TABLE `thirteenth_month_pays` ADD COLUMN `notes` text DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_thirteenth_month_pays_computed_by = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'thirteenth_month_pays' AND COLUMN_NAME = 'computed_by');

SET @sql = IF(@has_thirteenth_month_pays_computed_by = 0,
  'ALTER TABLE `thirteenth_month_pays` ADD COLUMN `computed_by` bigint(20) unsigned DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_thirteenth_month_pays_computed_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'thirteenth_month_pays' AND COLUMN_NAME = 'computed_at');

SET @sql = IF(@has_thirteenth_month_pays_computed_at = 0,
  'ALTER TABLE `thirteenth_month_pays` ADD COLUMN `computed_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_thirteenth_month_pays_computation_breakdown = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'thirteenth_month_pays' AND COLUMN_NAME = 'computation_breakdown');

SET @sql = IF(@has_thirteenth_month_pays_computation_breakdown = 0,
  'ALTER TABLE `thirteenth_month_pays` ADD COLUMN `computation_breakdown` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`computation_breakdown`))',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_thirteenth_month_pays_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'thirteenth_month_pays' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_thirteenth_month_pays_created_at = 0,
  'ALTER TABLE `thirteenth_month_pays` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_thirteenth_month_pays_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'thirteenth_month_pays' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_thirteenth_month_pays_updated_at = 0,
  'ALTER TABLE `thirteenth_month_pays` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_thirteenth_month_pays_deleted_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'thirteenth_month_pays' AND COLUMN_NAME = 'deleted_at');

SET @sql = IF(@has_thirteenth_month_pays_deleted_at = 0,
  'ALTER TABLE `thirteenth_month_pays` ADD COLUMN `deleted_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_user_activities_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'user_activities' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_user_activities_id = 0,
  'ALTER TABLE `user_activities` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_user_activities_user_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'user_activities' AND COLUMN_NAME = 'user_id');

SET @sql = IF(@has_user_activities_user_id = 0,
  'ALTER TABLE `user_activities` ADD COLUMN `user_id` bigint(20) unsigned NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_user_activities_action = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'user_activities' AND COLUMN_NAME = 'action');

SET @sql = IF(@has_user_activities_action = 0,
  'ALTER TABLE `user_activities` ADD COLUMN `action` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_user_activities_subject_type = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'user_activities' AND COLUMN_NAME = 'subject_type');

SET @sql = IF(@has_user_activities_subject_type = 0,
  'ALTER TABLE `user_activities` ADD COLUMN `subject_type` varchar(255) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_user_activities_subject_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'user_activities' AND COLUMN_NAME = 'subject_id');

SET @sql = IF(@has_user_activities_subject_id = 0,
  'ALTER TABLE `user_activities` ADD COLUMN `subject_id` bigint(20) unsigned DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_user_activities_subject_label = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'user_activities' AND COLUMN_NAME = 'subject_label');

SET @sql = IF(@has_user_activities_subject_label = 0,
  'ALTER TABLE `user_activities` ADD COLUMN `subject_label` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_user_activities_url = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'user_activities' AND COLUMN_NAME = 'url');

SET @sql = IF(@has_user_activities_url = 0,
  'ALTER TABLE `user_activities` ADD COLUMN `url` varchar(255) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_user_activities_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'user_activities' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_user_activities_created_at = 0,
  'ALTER TABLE `user_activities` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_user_activities_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'user_activities' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_user_activities_updated_at = 0,
  'ALTER TABLE `user_activities` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_users_id = 0,
  'ALTER TABLE `users` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_name = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'name');

SET @sql = IF(@has_users_name = 0,
  'ALTER TABLE `users` ADD COLUMN `name` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_username = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'username');

SET @sql = IF(@has_users_username = 0,
  'ALTER TABLE `users` ADD COLUMN `username` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_password = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'password');

SET @sql = IF(@has_users_password = 0,
  'ALTER TABLE `users` ADD COLUMN `password` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_password_changed = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'password_changed');

SET @sql = IF(@has_users_password_changed = 0,
  'ALTER TABLE `users` ADD COLUMN `password_changed` tinyint(1) NOT NULL DEFAULT 0',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_role = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'role');

SET @sql = IF(@has_users_role = 0,
  'ALTER TABLE `users` ADD COLUMN `role` varchar(255) NOT NULL DEFAULT \'employee\'',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_profile_picture = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'profile_picture');

SET @sql = IF(@has_users_profile_picture = 0,
  'ALTER TABLE `users` ADD COLUMN `profile_picture` varchar(255) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_remember_token = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'remember_token');

SET @sql = IF(@has_users_remember_token = 0,
  'ALTER TABLE `users` ADD COLUMN `remember_token` varchar(100) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_is_logged_in = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'is_logged_in');

SET @sql = IF(@has_users_is_logged_in = 0,
  'ALTER TABLE `users` ADD COLUMN `is_logged_in` tinyint(1) NOT NULL DEFAULT 0',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_first_name = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'first_name');

SET @sql = IF(@has_users_first_name = 0,
  'ALTER TABLE `users` ADD COLUMN `first_name` varchar(255) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_middle_name = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'middle_name');

SET @sql = IF(@has_users_middle_name = 0,
  'ALTER TABLE `users` ADD COLUMN `middle_name` varchar(255) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_last_name = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'last_name');

SET @sql = IF(@has_users_last_name = 0,
  'ALTER TABLE `users` ADD COLUMN `last_name` varchar(255) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_email = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'email');

SET @sql = IF(@has_users_email = 0,
  'ALTER TABLE `users` ADD COLUMN `email` varchar(255) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_phone = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'phone');

SET @sql = IF(@has_users_phone = 0,
  'ALTER TABLE `users` ADD COLUMN `phone` varchar(255) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_address = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'address');

SET @sql = IF(@has_users_address = 0,
  'ALTER TABLE `users` ADD COLUMN `address` text DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_civil_status = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'civil_status');

SET @sql = IF(@has_users_civil_status = 0,
  'ALTER TABLE `users` ADD COLUMN `civil_status` varchar(255) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_gender = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'gender');

SET @sql = IF(@has_users_gender = 0,
  'ALTER TABLE `users` ADD COLUMN `gender` enum(\'male\',\'female\',\'prefer_not_to_say\') DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_spouse_name = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'spouse_name');

SET @sql = IF(@has_users_spouse_name = 0,
  'ALTER TABLE `users` ADD COLUMN `spouse_name` varchar(255) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_date_of_birth = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'date_of_birth');

SET @sql = IF(@has_users_date_of_birth = 0,
  'ALTER TABLE `users` ADD COLUMN `date_of_birth` date DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_place_of_birth = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'place_of_birth');

SET @sql = IF(@has_users_place_of_birth = 0,
  'ALTER TABLE `users` ADD COLUMN `place_of_birth` varchar(255) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_educational_attainment = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'educational_attainment');

SET @sql = IF(@has_users_educational_attainment = 0,
  'ALTER TABLE `users` ADD COLUMN `educational_attainment` varchar(255) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_driver_license_number = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'driver_license_number');

SET @sql = IF(@has_users_driver_license_number = 0,
  'ALTER TABLE `users` ADD COLUMN `driver_license_number` varchar(255) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_driver_license_validity = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'driver_license_validity');

SET @sql = IF(@has_users_driver_license_validity = 0,
  'ALTER TABLE `users` ADD COLUMN `driver_license_validity` date DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_date_of_hire = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'date_of_hire');

SET @sql = IF(@has_users_date_of_hire = 0,
  'ALTER TABLE `users` ADD COLUMN `date_of_hire` date DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_position = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'position');

SET @sql = IF(@has_users_position = 0,
  'ALTER TABLE `users` ADD COLUMN `position` varchar(255) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_shift_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'shift_id');

SET @sql = IF(@has_users_shift_id = 0,
  'ALTER TABLE `users` ADD COLUMN `shift_id` bigint(20) unsigned DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_department = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'department');

SET @sql = IF(@has_users_department = 0,
  'ALTER TABLE `users` ADD COLUMN `department` varchar(255) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_status = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'status');

SET @sql = IF(@has_users_status = 0,
  'ALTER TABLE `users` ADD COLUMN `status` varchar(255) NOT NULL DEFAULT \'active\'',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_salary_rate = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'salary_rate');

SET @sql = IF(@has_users_salary_rate = 0,
  'ALTER TABLE `users` ADD COLUMN `salary_rate` decimal(10,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_work_days_per_week = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'work_days_per_week');

SET @sql = IF(@has_users_work_days_per_week = 0,
  'ALTER TABLE `users` ADD COLUMN `work_days_per_week` tinyint(4) NOT NULL DEFAULT 5',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_bank_name = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'bank_name');

SET @sql = IF(@has_users_bank_name = 0,
  'ALTER TABLE `users` ADD COLUMN `bank_name` varchar(255) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_bank_account_number = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'bank_account_number');

SET @sql = IF(@has_users_bank_account_number = 0,
  'ALTER TABLE `users` ADD COLUMN `bank_account_number` varchar(255) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_schedule_type = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'schedule_type');

SET @sql = IF(@has_users_schedule_type = 0,
  'ALTER TABLE `users` ADD COLUMN `schedule_type` varchar(255) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_has_sss = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'has_sss');

SET @sql = IF(@has_users_has_sss = 0,
  'ALTER TABLE `users` ADD COLUMN `has_sss` tinyint(1) NOT NULL DEFAULT 0',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_sss_number = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'sss_number');

SET @sql = IF(@has_users_sss_number = 0,
  'ALTER TABLE `users` ADD COLUMN `sss_number` varchar(255) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_tin_number = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'tin_number');

SET @sql = IF(@has_users_tin_number = 0,
  'ALTER TABLE `users` ADD COLUMN `tin_number` varchar(255) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_has_tin = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'has_tin');

SET @sql = IF(@has_users_has_tin = 0,
  'ALTER TABLE `users` ADD COLUMN `has_tin` tinyint(1) NOT NULL DEFAULT 0',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_pagibig_number = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'pagibig_number');

SET @sql = IF(@has_users_pagibig_number = 0,
  'ALTER TABLE `users` ADD COLUMN `pagibig_number` varchar(255) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_philhealth_number = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'philhealth_number');

SET @sql = IF(@has_users_philhealth_number = 0,
  'ALTER TABLE `users` ADD COLUMN `philhealth_number` varchar(255) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_has_pagibig = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'has_pagibig');

SET @sql = IF(@has_users_has_pagibig = 0,
  'ALTER TABLE `users` ADD COLUMN `has_pagibig` tinyint(1) NOT NULL DEFAULT 0',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_has_philhealth = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'has_philhealth');

SET @sql = IF(@has_users_has_philhealth = 0,
  'ALTER TABLE `users` ADD COLUMN `has_philhealth` tinyint(1) NOT NULL DEFAULT 0',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_signature_path = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'signature_path');

SET @sql = IF(@has_users_signature_path = 0,
  'ALTER TABLE `users` ADD COLUMN `signature_path` varchar(255) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_attachments = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'attachments');

SET @sql = IF(@has_users_attachments = 0,
  'ALTER TABLE `users` ADD COLUMN `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attachments`))',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_users_created_at = 0,
  'ALTER TABLE `users` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_users_updated_at = 0,
  'ALTER TABLE `users` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_users_last_login_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'last_login_at');

SET @sql = IF(@has_users_last_login_at = 0,
  'ALTER TABLE `users` ADD COLUMN `last_login_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_vehicles_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'vehicles' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_vehicles_id = 0,
  'ALTER TABLE `vehicles` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_vehicles_plate_number = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'vehicles' AND COLUMN_NAME = 'plate_number');

SET @sql = IF(@has_vehicles_plate_number = 0,
  'ALTER TABLE `vehicles` ADD COLUMN `plate_number` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_vehicles_route_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'vehicles' AND COLUMN_NAME = 'route_id');

SET @sql = IF(@has_vehicles_route_id = 0,
  'ALTER TABLE `vehicles` ADD COLUMN `route_id` bigint(20) unsigned NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_vehicles_operator = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'vehicles' AND COLUMN_NAME = 'operator');

SET @sql = IF(@has_vehicles_operator = 0,
  'ALTER TABLE `vehicles` ADD COLUMN `operator` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_vehicles_status = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'vehicles' AND COLUMN_NAME = 'status');

SET @sql = IF(@has_vehicles_status = 0,
  'ALTER TABLE `vehicles` ADD COLUMN `status` varchar(255) NOT NULL DEFAULT \'active\'',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_vehicles_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'vehicles' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_vehicles_created_at = 0,
  'ALTER TABLE `vehicles` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_vehicles_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'vehicles' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_vehicles_updated_at = 0,
  'ALTER TABLE `vehicles` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_withholding_taxes_id = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'withholding_taxes' AND COLUMN_NAME = 'id');

SET @sql = IF(@has_withholding_taxes_id = 0,
  'ALTER TABLE `withholding_taxes` ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_withholding_taxes_name = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'withholding_taxes' AND COLUMN_NAME = 'name');

SET @sql = IF(@has_withholding_taxes_name = 0,
  'ALTER TABLE `withholding_taxes` ADD COLUMN `name` varchar(255) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_withholding_taxes_min_salary = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'withholding_taxes' AND COLUMN_NAME = 'min_salary');

SET @sql = IF(@has_withholding_taxes_min_salary = 0,
  'ALTER TABLE `withholding_taxes` ADD COLUMN `min_salary` decimal(12,2) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_withholding_taxes_max_salary = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'withholding_taxes' AND COLUMN_NAME = 'max_salary');

SET @sql = IF(@has_withholding_taxes_max_salary = 0,
  'ALTER TABLE `withholding_taxes` ADD COLUMN `max_salary` decimal(12,2) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_withholding_taxes_employee_share = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'withholding_taxes' AND COLUMN_NAME = 'employee_share');

SET @sql = IF(@has_withholding_taxes_employee_share = 0,
  'ALTER TABLE `withholding_taxes` ADD COLUMN `employee_share` decimal(12,2) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_withholding_taxes_percentage_employee = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'withholding_taxes' AND COLUMN_NAME = 'percentage_employee');

SET @sql = IF(@has_withholding_taxes_percentage_employee = 0,
  'ALTER TABLE `withholding_taxes` ADD COLUMN `percentage_employee` decimal(5,2) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_withholding_taxes_description = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'withholding_taxes' AND COLUMN_NAME = 'description');

SET @sql = IF(@has_withholding_taxes_description = 0,
  'ALTER TABLE `withholding_taxes` ADD COLUMN `description` text DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_withholding_taxes_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'withholding_taxes' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_withholding_taxes_created_at = 0,
  'ALTER TABLE `withholding_taxes` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_withholding_taxes_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'withholding_taxes' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_withholding_taxes_updated_at = 0,
  'ALTER TABLE `withholding_taxes` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 536 columns checked.
-- 3. Add any foreign key that is missing ---------------------------------
-- Done last, because a foreign key needs both tables and the primary key
-- on the table it points at to already exist.
--
SET @has_fk_account_mappings_account_mappings_account_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'account_mappings' AND CONSTRAINT_NAME = 'account_mappings_account_id_foreign');

SET @sql = IF(@has_fk_account_mappings_account_mappings_account_id_foreign = 0,
  'ALTER TABLE `account_mappings` ADD CONSTRAINT `account_mappings_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `chart_of_accounts` (`id`)',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_accounting_audit_logs_accounting_audit_logs_user_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'accounting_audit_logs' AND CONSTRAINT_NAME = 'accounting_audit_logs_user_id_foreign');

SET @sql = IF(@has_fk_accounting_audit_logs_accounting_audit_logs_user_id_foreign = 0,
  'ALTER TABLE `accounting_audit_logs` ADD CONSTRAINT `accounting_audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_allowances_allowances_payroll_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'allowances' AND CONSTRAINT_NAME = 'allowances_payroll_id_foreign');

SET @sql = IF(@has_fk_allowances_allowances_payroll_id_foreign = 0,
  'ALTER TABLE `allowances` ADD CONSTRAINT `allowances_payroll_id_foreign` FOREIGN KEY (`payroll_id`) REFERENCES `payrolls` (`id`) ON DELETE SET NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_allowances_allowances_user_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'allowances' AND CONSTRAINT_NAME = 'allowances_user_id_foreign');

SET @sql = IF(@has_fk_allowances_allowances_user_id_foreign = 0,
  'ALTER TABLE `allowances` ADD CONSTRAINT `allowances_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_attendance_attendance_user_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'attendance' AND CONSTRAINT_NAME = 'attendance_user_id_foreign');

SET @sql = IF(@has_fk_attendance_attendance_user_id_foreign = 0,
  'ALTER TABLE `attendance` ADD CONSTRAINT `attendance_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_attendance_logs_attendance_logs_user_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'attendance_logs' AND CONSTRAINT_NAME = 'attendance_logs_user_id_foreign');

SET @sql = IF(@has_fk_attendance_logs_attendance_logs_user_id_foreign = 0,
  'ALTER TABLE `attendance_logs` ADD CONSTRAINT `attendance_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_attendance_tokens_attendance_tokens_user_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'attendance_tokens' AND CONSTRAINT_NAME = 'attendance_tokens_user_id_foreign');

SET @sql = IF(@has_fk_attendance_tokens_attendance_tokens_user_id_foreign = 0,
  'ALTER TABLE `attendance_tokens` ADD CONSTRAINT `attendance_tokens_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_bonus_employee_bonus_employee_bonus_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'bonus_employee' AND CONSTRAINT_NAME = 'bonus_employee_bonus_id_foreign');

SET @sql = IF(@has_fk_bonus_employee_bonus_employee_bonus_id_foreign = 0,
  'ALTER TABLE `bonus_employee` ADD CONSTRAINT `bonus_employee_bonus_id_foreign` FOREIGN KEY (`bonus_id`) REFERENCES `bonuses` (`id`) ON DELETE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_bonus_employee_bonus_employee_user_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'bonus_employee' AND CONSTRAINT_NAME = 'bonus_employee_user_id_foreign');

SET @sql = IF(@has_fk_bonus_employee_bonus_employee_user_id_foreign = 0,
  'ALTER TABLE `bonus_employee` ADD CONSTRAINT `bonus_employee_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_bonuses_bonuses_created_by_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'bonuses' AND CONSTRAINT_NAME = 'bonuses_created_by_foreign');

SET @sql = IF(@has_fk_bonuses_bonuses_created_by_foreign = 0,
  'ALTER TABLE `bonuses` ADD CONSTRAINT `bonuses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_boundary_payments_boundary_payments_approved_by_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'boundary_payments' AND CONSTRAINT_NAME = 'boundary_payments_approved_by_foreign');

SET @sql = IF(@has_fk_boundary_payments_boundary_payments_approved_by_foreign = 0,
  'ALTER TABLE `boundary_payments` ADD CONSTRAINT `boundary_payments_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`)',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_boundary_payments_boundary_payments_daily_remittance_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'boundary_payments' AND CONSTRAINT_NAME = 'boundary_payments_daily_remittance_id_foreign');

SET @sql = IF(@has_fk_boundary_payments_boundary_payments_daily_remittance_id_foreign = 0,
  'ALTER TABLE `boundary_payments` ADD CONSTRAINT `boundary_payments_daily_remittance_id_foreign` FOREIGN KEY (`daily_remittance_id`) REFERENCES `daily_remittances` (`id`)',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_boundary_payments_boundary_payments_initial_payment_remittance_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'boundary_payments' AND CONSTRAINT_NAME = 'boundary_payments_initial_payment_remittance_id_foreign');

SET @sql = IF(@has_fk_boundary_payments_boundary_payments_initial_payment_remittance_id_foreign = 0,
  'ALTER TABLE `boundary_payments` ADD CONSTRAINT `boundary_payments_initial_payment_remittance_id_foreign` FOREIGN KEY (`initial_payment_remittance_id`) REFERENCES `daily_remittances` (`id`)',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_boundary_payments_boundary_payments_journal_entry_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'boundary_payments' AND CONSTRAINT_NAME = 'boundary_payments_journal_entry_id_foreign');

SET @sql = IF(@has_fk_boundary_payments_boundary_payments_journal_entry_id_foreign = 0,
  'ALTER TABLE `boundary_payments` ADD CONSTRAINT `boundary_payments_journal_entry_id_foreign` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`)',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_boundary_payments_boundary_payments_recorded_by_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'boundary_payments' AND CONSTRAINT_NAME = 'boundary_payments_recorded_by_foreign');

SET @sql = IF(@has_fk_boundary_payments_boundary_payments_recorded_by_foreign = 0,
  'ALTER TABLE `boundary_payments` ADD CONSTRAINT `boundary_payments_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`)',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_cash_advances_cash_advances_approved_by_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'cash_advances' AND CONSTRAINT_NAME = 'cash_advances_approved_by_foreign');

SET @sql = IF(@has_fk_cash_advances_cash_advances_approved_by_foreign = 0,
  'ALTER TABLE `cash_advances` ADD CONSTRAINT `cash_advances_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_cash_advances_cash_advances_deducted_payroll_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'cash_advances' AND CONSTRAINT_NAME = 'cash_advances_deducted_payroll_id_foreign');

SET @sql = IF(@has_fk_cash_advances_cash_advances_deducted_payroll_id_foreign = 0,
  'ALTER TABLE `cash_advances` ADD CONSTRAINT `cash_advances_deducted_payroll_id_foreign` FOREIGN KEY (`deducted_payroll_id`) REFERENCES `payrolls` (`id`) ON DELETE SET NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_cash_advances_cash_advances_user_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'cash_advances' AND CONSTRAINT_NAME = 'cash_advances_user_id_foreign');

SET @sql = IF(@has_fk_cash_advances_cash_advances_user_id_foreign = 0,
  'ALTER TABLE `cash_advances` ADD CONSTRAINT `cash_advances_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_chart_of_accounts_chart_of_accounts_created_by_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'chart_of_accounts' AND CONSTRAINT_NAME = 'chart_of_accounts_created_by_foreign');

SET @sql = IF(@has_fk_chart_of_accounts_chart_of_accounts_created_by_foreign = 0,
  'ALTER TABLE `chart_of_accounts` ADD CONSTRAINT `chart_of_accounts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_daily_remittances_daily_remittances_driver_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'daily_remittances' AND CONSTRAINT_NAME = 'daily_remittances_driver_id_foreign');

SET @sql = IF(@has_fk_daily_remittances_daily_remittances_driver_id_foreign = 0,
  'ALTER TABLE `daily_remittances` ADD CONSTRAINT `daily_remittances_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_daily_remittances_daily_remittances_pao_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'daily_remittances' AND CONSTRAINT_NAME = 'daily_remittances_pao_id_foreign');

SET @sql = IF(@has_fk_daily_remittances_daily_remittances_pao_id_foreign = 0,
  'ALTER TABLE `daily_remittances` ADD CONSTRAINT `daily_remittances_pao_id_foreign` FOREIGN KEY (`pao_id`) REFERENCES `paos` (`id`) ON DELETE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_daily_remittances_daily_remittances_route_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'daily_remittances' AND CONSTRAINT_NAME = 'daily_remittances_route_id_foreign');

SET @sql = IF(@has_fk_daily_remittances_daily_remittances_route_id_foreign = 0,
  'ALTER TABLE `daily_remittances` ADD CONSTRAINT `daily_remittances_route_id_foreign` FOREIGN KEY (`route_id`) REFERENCES `routes` (`id`) ON DELETE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_daily_remittances_daily_remittances_vehicle_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'daily_remittances' AND CONSTRAINT_NAME = 'daily_remittances_vehicle_id_foreign');

SET @sql = IF(@has_fk_daily_remittances_daily_remittances_vehicle_id_foreign = 0,
  'ALTER TABLE `daily_remittances` ADD CONSTRAINT `daily_remittances_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_deductions_deductions_payroll_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'deductions' AND CONSTRAINT_NAME = 'deductions_payroll_id_foreign');

SET @sql = IF(@has_fk_deductions_deductions_payroll_id_foreign = 0,
  'ALTER TABLE `deductions` ADD CONSTRAINT `deductions_payroll_id_foreign` FOREIGN KEY (`payroll_id`) REFERENCES `payrolls` (`id`) ON DELETE SET NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_deductions_deductions_user_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'deductions' AND CONSTRAINT_NAME = 'deductions_user_id_foreign');

SET @sql = IF(@has_fk_deductions_deductions_user_id_foreign = 0,
  'ALTER TABLE `deductions` ADD CONSTRAINT `deductions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_employee_attachments_employee_attachments_reviewed_by_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_attachments' AND CONSTRAINT_NAME = 'employee_attachments_reviewed_by_foreign');

SET @sql = IF(@has_fk_employee_attachments_employee_attachments_reviewed_by_foreign = 0,
  'ALTER TABLE `employee_attachments` ADD CONSTRAINT `employee_attachments_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_employee_attachments_employee_attachments_user_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_attachments' AND CONSTRAINT_NAME = 'employee_attachments_user_id_foreign');

SET @sql = IF(@has_fk_employee_attachments_employee_attachments_user_id_foreign = 0,
  'ALTER TABLE `employee_attachments` ADD CONSTRAINT `employee_attachments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_employee_beneficiaries_employee_beneficiaries_user_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_beneficiaries' AND CONSTRAINT_NAME = 'employee_beneficiaries_user_id_foreign');

SET @sql = IF(@has_fk_employee_beneficiaries_employee_beneficiaries_user_id_foreign = 0,
  'ALTER TABLE `employee_beneficiaries` ADD CONSTRAINT `employee_beneficiaries_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_employee_experiences_employee_experiences_user_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_experiences' AND CONSTRAINT_NAME = 'employee_experiences_user_id_foreign');

SET @sql = IF(@has_fk_employee_experiences_employee_experiences_user_id_foreign = 0,
  'ALTER TABLE `employee_experiences` ADD CONSTRAINT `employee_experiences_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_employee_leave_balances_employee_leave_balances_leave_type_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_leave_balances' AND CONSTRAINT_NAME = 'employee_leave_balances_leave_type_id_foreign');

SET @sql = IF(@has_fk_employee_leave_balances_employee_leave_balances_leave_type_id_foreign = 0,
  'ALTER TABLE `employee_leave_balances` ADD CONSTRAINT `employee_leave_balances_leave_type_id_foreign` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_employee_leave_balances_employee_leave_balances_user_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_leave_balances' AND CONSTRAINT_NAME = 'employee_leave_balances_user_id_foreign');

SET @sql = IF(@has_fk_employee_leave_balances_employee_leave_balances_user_id_foreign = 0,
  'ALTER TABLE `employee_leave_balances` ADD CONSTRAINT `employee_leave_balances_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_employee_references_employee_references_user_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_references' AND CONSTRAINT_NAME = 'employee_references_user_id_foreign');

SET @sql = IF(@has_fk_employee_references_employee_references_user_id_foreign = 0,
  'ALTER TABLE `employee_references` ADD CONSTRAINT `employee_references_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_employee_skills_employee_skills_user_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'employee_skills' AND CONSTRAINT_NAME = 'employee_skills_user_id_foreign');

SET @sql = IF(@has_fk_employee_skills_employee_skills_user_id_foreign = 0,
  'ALTER TABLE `employee_skills` ADD CONSTRAINT `employee_skills_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_journal_entries_journal_entries_created_by_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'journal_entries' AND CONSTRAINT_NAME = 'journal_entries_created_by_foreign');

SET @sql = IF(@has_fk_journal_entries_journal_entries_created_by_foreign = 0,
  'ALTER TABLE `journal_entries` ADD CONSTRAINT `journal_entries_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_journal_entries_journal_entries_posted_by_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'journal_entries' AND CONSTRAINT_NAME = 'journal_entries_posted_by_foreign');

SET @sql = IF(@has_fk_journal_entries_journal_entries_posted_by_foreign = 0,
  'ALTER TABLE `journal_entries` ADD CONSTRAINT `journal_entries_posted_by_foreign` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_journal_entry_lines_journal_entry_lines_account_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'journal_entry_lines' AND CONSTRAINT_NAME = 'journal_entry_lines_account_id_foreign');

SET @sql = IF(@has_fk_journal_entry_lines_journal_entry_lines_account_id_foreign = 0,
  'ALTER TABLE `journal_entry_lines` ADD CONSTRAINT `journal_entry_lines_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `chart_of_accounts` (`id`)',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_journal_entry_lines_journal_entry_lines_journal_entry_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'journal_entry_lines' AND CONSTRAINT_NAME = 'journal_entry_lines_journal_entry_id_foreign');

SET @sql = IF(@has_fk_journal_entry_lines_journal_entry_lines_journal_entry_id_foreign = 0,
  'ALTER TABLE `journal_entry_lines` ADD CONSTRAINT `journal_entry_lines_journal_entry_id_foreign` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`) ON DELETE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_leaves_leaves_approved_by_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'leaves' AND CONSTRAINT_NAME = 'leaves_approved_by_foreign');

SET @sql = IF(@has_fk_leaves_leaves_approved_by_foreign = 0,
  'ALTER TABLE `leaves` ADD CONSTRAINT `leaves_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_leaves_leaves_leave_type_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'leaves' AND CONSTRAINT_NAME = 'leaves_leave_type_id_foreign');

SET @sql = IF(@has_fk_leaves_leaves_leave_type_id_foreign = 0,
  'ALTER TABLE `leaves` ADD CONSTRAINT `leaves_leave_type_id_foreign` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE SET NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_leaves_leaves_user_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'leaves' AND CONSTRAINT_NAME = 'leaves_user_id_foreign');

SET @sql = IF(@has_fk_leaves_leaves_user_id_foreign = 0,
  'ALTER TABLE `leaves` ADD CONSTRAINT `leaves_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_notifications_notifications_user_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'notifications' AND CONSTRAINT_NAME = 'notifications_user_id_foreign');

SET @sql = IF(@has_fk_notifications_notifications_user_id_foreign = 0,
  'ALTER TABLE `notifications` ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_overtime_undertimes_overtime_undertimes_approved_by_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'overtime_undertimes' AND CONSTRAINT_NAME = 'overtime_undertimes_approved_by_foreign');

SET @sql = IF(@has_fk_overtime_undertimes_overtime_undertimes_approved_by_foreign = 0,
  'ALTER TABLE `overtime_undertimes` ADD CONSTRAINT `overtime_undertimes_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_overtime_undertimes_overtime_undertimes_user_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'overtime_undertimes' AND CONSTRAINT_NAME = 'overtime_undertimes_user_id_foreign');

SET @sql = IF(@has_fk_overtime_undertimes_overtime_undertimes_user_id_foreign = 0,
  'ALTER TABLE `overtime_undertimes` ADD CONSTRAINT `overtime_undertimes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_payroll_allowances_payroll_allowances_payroll_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_allowances' AND CONSTRAINT_NAME = 'payroll_allowances_payroll_id_foreign');

SET @sql = IF(@has_fk_payroll_allowances_payroll_allowances_payroll_id_foreign = 0,
  'ALTER TABLE `payroll_allowances` ADD CONSTRAINT `payroll_allowances_payroll_id_foreign` FOREIGN KEY (`payroll_id`) REFERENCES `payrolls` (`id`) ON DELETE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_payroll_batches_payroll_batches_approved_by_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_batches' AND CONSTRAINT_NAME = 'payroll_batches_approved_by_foreign');

SET @sql = IF(@has_fk_payroll_batches_payroll_batches_approved_by_foreign = 0,
  'ALTER TABLE `payroll_batches` ADD CONSTRAINT `payroll_batches_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_payroll_batches_payroll_batches_finalized_by_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_batches' AND CONSTRAINT_NAME = 'payroll_batches_finalized_by_foreign');

SET @sql = IF(@has_fk_payroll_batches_payroll_batches_finalized_by_foreign = 0,
  'ALTER TABLE `payroll_batches` ADD CONSTRAINT `payroll_batches_finalized_by_foreign` FOREIGN KEY (`finalized_by`) REFERENCES `users` (`id`) ON DELETE SET NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_payroll_batches_payroll_batches_generated_by_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_batches' AND CONSTRAINT_NAME = 'payroll_batches_generated_by_foreign');

SET @sql = IF(@has_fk_payroll_batches_payroll_batches_generated_by_foreign = 0,
  'ALTER TABLE `payroll_batches` ADD CONSTRAINT `payroll_batches_generated_by_foreign` FOREIGN KEY (`generated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_payroll_batches_payroll_batches_rejected_by_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_batches' AND CONSTRAINT_NAME = 'payroll_batches_rejected_by_foreign');

SET @sql = IF(@has_fk_payroll_batches_payroll_batches_rejected_by_foreign = 0,
  'ALTER TABLE `payroll_batches` ADD CONSTRAINT `payroll_batches_rejected_by_foreign` FOREIGN KEY (`rejected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_payroll_bonuses_payroll_bonuses_payroll_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_bonuses' AND CONSTRAINT_NAME = 'payroll_bonuses_payroll_id_foreign');

SET @sql = IF(@has_fk_payroll_bonuses_payroll_bonuses_payroll_id_foreign = 0,
  'ALTER TABLE `payroll_bonuses` ADD CONSTRAINT `payroll_bonuses_payroll_id_foreign` FOREIGN KEY (`payroll_id`) REFERENCES `payrolls` (`id`) ON DELETE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_payroll_deductions_payroll_deductions_payroll_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_deductions' AND CONSTRAINT_NAME = 'payroll_deductions_payroll_id_foreign');

SET @sql = IF(@has_fk_payroll_deductions_payroll_deductions_payroll_id_foreign = 0,
  'ALTER TABLE `payroll_deductions` ADD CONSTRAINT `payroll_deductions_payroll_id_foreign` FOREIGN KEY (`payroll_id`) REFERENCES `payrolls` (`id`) ON DELETE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_payrolls_payrolls_approved_by_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND CONSTRAINT_NAME = 'payrolls_approved_by_foreign');

SET @sql = IF(@has_fk_payrolls_payrolls_approved_by_foreign = 0,
  'ALTER TABLE `payrolls` ADD CONSTRAINT `payrolls_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_payrolls_payrolls_batch_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND CONSTRAINT_NAME = 'payrolls_batch_id_foreign');

SET @sql = IF(@has_fk_payrolls_payrolls_batch_id_foreign = 0,
  'ALTER TABLE `payrolls` ADD CONSTRAINT `payrolls_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `payroll_batches` (`id`) ON DELETE SET NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_payrolls_payrolls_user_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND CONSTRAINT_NAME = 'payrolls_user_id_foreign');

SET @sql = IF(@has_fk_payrolls_payrolls_user_id_foreign = 0,
  'ALTER TABLE `payrolls` ADD CONSTRAINT `payrolls_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_pinned_items_pinned_items_user_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'pinned_items' AND CONSTRAINT_NAME = 'pinned_items_user_id_foreign');

SET @sql = IF(@has_fk_pinned_items_pinned_items_user_id_foreign = 0,
  'ALTER TABLE `pinned_items` ADD CONSTRAINT `pinned_items_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_position_rates_position_rates_department_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'position_rates' AND CONSTRAINT_NAME = 'position_rates_department_id_foreign');

SET @sql = IF(@has_fk_position_rates_position_rates_department_id_foreign = 0,
  'ALTER TABLE `position_rates` ADD CONSTRAINT `position_rates_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_thirteenth_month_pays_thirteenth_month_pays_batch_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'thirteenth_month_pays' AND CONSTRAINT_NAME = 'thirteenth_month_pays_batch_id_foreign');

SET @sql = IF(@has_fk_thirteenth_month_pays_thirteenth_month_pays_batch_id_foreign = 0,
  'ALTER TABLE `thirteenth_month_pays` ADD CONSTRAINT `thirteenth_month_pays_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `payroll_batches` (`id`) ON DELETE SET NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_thirteenth_month_pays_thirteenth_month_pays_computed_by_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'thirteenth_month_pays' AND CONSTRAINT_NAME = 'thirteenth_month_pays_computed_by_foreign');

SET @sql = IF(@has_fk_thirteenth_month_pays_thirteenth_month_pays_computed_by_foreign = 0,
  'ALTER TABLE `thirteenth_month_pays` ADD CONSTRAINT `thirteenth_month_pays_computed_by_foreign` FOREIGN KEY (`computed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_thirteenth_month_pays_thirteenth_month_pays_paid_by_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'thirteenth_month_pays' AND CONSTRAINT_NAME = 'thirteenth_month_pays_paid_by_foreign');

SET @sql = IF(@has_fk_thirteenth_month_pays_thirteenth_month_pays_paid_by_foreign = 0,
  'ALTER TABLE `thirteenth_month_pays` ADD CONSTRAINT `thirteenth_month_pays_paid_by_foreign` FOREIGN KEY (`paid_by`) REFERENCES `users` (`id`) ON DELETE SET NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_thirteenth_month_pays_thirteenth_month_pays_user_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'thirteenth_month_pays' AND CONSTRAINT_NAME = 'thirteenth_month_pays_user_id_foreign');

SET @sql = IF(@has_fk_thirteenth_month_pays_thirteenth_month_pays_user_id_foreign = 0,
  'ALTER TABLE `thirteenth_month_pays` ADD CONSTRAINT `thirteenth_month_pays_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_user_activities_user_activities_user_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'user_activities' AND CONSTRAINT_NAME = 'user_activities_user_id_foreign');

SET @sql = IF(@has_fk_user_activities_user_activities_user_id_foreign = 0,
  'ALTER TABLE `user_activities` ADD CONSTRAINT `user_activities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_users_users_shift_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND CONSTRAINT_NAME = 'users_shift_id_foreign');

SET @sql = IF(@has_fk_users_users_shift_id_foreign = 0,
  'ALTER TABLE `users` ADD CONSTRAINT `users_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE SET NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk_vehicles_vehicles_route_id_foreign = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'vehicles' AND CONSTRAINT_NAME = 'vehicles_route_id_foreign');

SET @sql = IF(@has_fk_vehicles_vehicles_route_id_foreign = 0,
  'ALTER TABLE `vehicles` ADD CONSTRAINT `vehicles_route_id_foreign` FOREIGN KEY (`route_id`) REFERENCES `routes` (`id`) ON DELETE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 62 foreign keys checked.
SET FOREIGN_KEY_CHECKS = 1;

-- 4. Payroll accounting fix ----------------------------------------------
UPDATE `chart_of_accounts`
SET `is_contra` = 1,
    `description` = 'Contra-expense for valid employee wage reductions; do not use for statutory withholdings or employee receivable recoveries.',
    `updated_at` = NOW()
WHERE `account_code` = '5060';

INSERT INTO `chart_of_accounts`
    (`account_code`, `account_name`, `account_type`, `description`, `is_active`, `created_at`, `updated_at`)
SELECT '2070', 'Other Payroll Deductions Payable', 'Liability',
       'Employee payroll deductions withheld for remittance to third parties',
       1, NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `chart_of_accounts` WHERE `account_code` = '2070');

INSERT IGNORE INTO `account_mappings` (`mapping_key`, `label`, `account_id`, `created_at`, `updated_at`)
SELECT 'payroll_deductions', 'Payroll Deductions', `id`, NOW(), NOW()
FROM `chart_of_accounts`
WHERE `account_code` = '5060';

INSERT IGNORE INTO `account_mappings` (`mapping_key`, `label`, `account_id`, `created_at`, `updated_at`)
SELECT 'employee_receivable', 'Employee Receivable', `id`, NOW(), NOW()
FROM `chart_of_accounts`
WHERE `account_code` = '1030';

INSERT IGNORE INTO `account_mappings` (`mapping_key`, `label`, `account_id`, `created_at`, `updated_at`)
SELECT 'other_payroll_deductions_payable', 'Other Payroll Deductions Payable', `id`, NOW(), NOW()
FROM `chart_of_accounts`
WHERE `account_code` = '2070';

INSERT IGNORE INTO `account_mappings` (`mapping_key`, `label`, `account_id`, `created_at`, `updated_at`)
SELECT 'employer_contributions_expense', 'Employer Government Contributions Expense', `id`, NOW(), NOW()
FROM `chart_of_accounts`
WHERE `account_code` = '5050';

-- 5. Verify --------------------------------------------------------------
-- Every primary_key value here must say has PK.

SELECT t.table_name,
       IF(EXISTS (
           SELECT 1 FROM information_schema.TABLE_CONSTRAINTS c
           WHERE c.CONSTRAINT_SCHEMA = DATABASE()
             AND c.TABLE_NAME = t.table_name
             AND c.CONSTRAINT_TYPE = 'PRIMARY KEY'
       ), 'has PK', 'STILL BROKEN') AS primary_key
FROM information_schema.TABLES t
WHERE t.TABLE_SCHEMA = DATABASE()
  AND t.table_name IN ('users','payrolls','notifications','accounting_audit_logs','thirteenth_month_pays')
ORDER BY t.table_name;

-- Row counts. Everything must match what you had before.
SELECT 'users' AS tbl, COUNT(*) AS rows_now FROM `users`
UNION ALL SELECT 'drivers',           COUNT(*) FROM `drivers`
UNION ALL SELECT 'paos',              COUNT(*) FROM `paos`
UNION ALL SELECT 'routes',            COUNT(*) FROM `routes`
UNION ALL SELECT 'vehicles',          COUNT(*) FROM `vehicles`
UNION ALL SELECT 'payrolls',          COUNT(*) FROM `payrolls`
UNION ALL SELECT 'attendance',        COUNT(*) FROM `attendance`
UNION ALL SELECT 'daily_remittances', COUNT(*) FROM `daily_remittances`
UNION ALL SELECT 'notifications',     COUNT(*) FROM `notifications`
UNION ALL SELECT 'chart_of_accounts', COUNT(*) FROM `chart_of_accounts`;

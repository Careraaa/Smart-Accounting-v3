-- ===========================================
-- SQA Deploy Dump (sqa-smartacc-v3)
-- Generated: 2026-10-20 09:18:05
-- Structure + default staff accounts only
-- ===========================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: bld
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `account_mappings`
--

DROP TABLE IF EXISTS `account_mappings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `account_mappings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `mapping_key` varchar(50) NOT NULL,
  `label` varchar(255) NOT NULL,
  `account_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_mappings_mapping_key_unique` (`mapping_key`),
  KEY `account_mappings_account_id_foreign` (`account_id`),
  CONSTRAINT `account_mappings_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `chart_of_accounts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `account_mappings`
--

LOCK TABLES `account_mappings` WRITE;
/*!40000 ALTER TABLE `account_mappings` DISABLE KEYS */;
/*!40000 ALTER TABLE `account_mappings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `accounting_audit_logs`
--

DROP TABLE IF EXISTS `accounting_audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accounting_audit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `action` varchar(50) NOT NULL,
  `module` varchar(50) NOT NULL,
  `record_id` bigint(20) unsigned DEFAULT NULL,
  `description` text DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accounting_audit_logs`
--

LOCK TABLES `accounting_audit_logs` WRITE;
/*!40000 ALTER TABLE `accounting_audit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `accounting_audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `allowances`
--

DROP TABLE IF EXISTS `allowances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `allowances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `payroll_id` bigint(20) unsigned DEFAULT NULL,
  `allowance_type` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `effective_date` date DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `allowances_user_id_foreign` (`user_id`),
  KEY `allowances_payroll_id_foreign` (`payroll_id`),
  CONSTRAINT `allowances_payroll_id_foreign` FOREIGN KEY (`payroll_id`) REFERENCES `payrolls` (`id`) ON DELETE SET NULL,
  CONSTRAINT `allowances_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `allowances`
--

LOCK TABLES `allowances` WRITE;
/*!40000 ALTER TABLE `allowances` DISABLE KEYS */;
/*!40000 ALTER TABLE `allowances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendance`
--

DROP TABLE IF EXISTS `attendance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attendance` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `date` date NOT NULL,
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `status` enum('present','late','absent','on leave') DEFAULT NULL,
  `is_manual` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attendance_user_id_date_unique` (`user_id`,`date`),
  CONSTRAINT `attendance_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance`
--

LOCK TABLES `attendance` WRITE;
/*!40000 ALTER TABLE `attendance` DISABLE KEYS */;
/*!40000 ALTER TABLE `attendance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendance_logs`
--

DROP TABLE IF EXISTS `attendance_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attendance_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `type` enum('time_in','time_out') NOT NULL,
  `logged_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attendance_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `attendance_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance_logs`
--

LOCK TABLES `attendance_logs` WRITE;
/*!40000 ALTER TABLE `attendance_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `attendance_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendance_tokens`
--

DROP TABLE IF EXISTS `attendance_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attendance_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `token` varchar(255) NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attendance_tokens_token_unique` (`token`),
  KEY `attendance_tokens_user_id_foreign` (`user_id`),
  CONSTRAINT `attendance_tokens_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance_tokens`
--

LOCK TABLES `attendance_tokens` WRITE;
/*!40000 ALTER TABLE `attendance_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `attendance_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bonus_employee`
--

DROP TABLE IF EXISTS `bonus_employee`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bonus_employee` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `bonus_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bonus_employee_bonus_id_user_id_unique` (`bonus_id`,`user_id`),
  KEY `bonus_employee_user_id_foreign` (`user_id`),
  CONSTRAINT `bonus_employee_bonus_id_foreign` FOREIGN KEY (`bonus_id`) REFERENCES `bonuses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bonus_employee_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bonus_employee`
--

LOCK TABLES `bonus_employee` WRITE;
/*!40000 ALTER TABLE `bonus_employee` DISABLE KEYS */;
/*!40000 ALTER TABLE `bonus_employee` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bonuses`
--

DROP TABLE IF EXISTS `bonuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bonuses` (
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
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `year` smallint(5) unsigned DEFAULT NULL,
  `payroll_period` varchar(255) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bonuses_code_unique` (`code`),
  KEY `bonuses_created_by_foreign` (`created_by`),
  CONSTRAINT `bonuses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bonuses`
--

LOCK TABLES `bonuses` WRITE;
/*!40000 ALTER TABLE `bonuses` DISABLE KEYS */;
/*!40000 ALTER TABLE `bonuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `boundary_payments`
--

DROP TABLE IF EXISTS `boundary_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `boundary_payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `daily_remittance_id` bigint(20) unsigned NOT NULL,
  `source_payment_key` varchar(64) NOT NULL,
  `initial_payment_remittance_id` bigint(20) unsigned DEFAULT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `paid_by` varchar(16) DEFAULT NULL,
  `payment_kind` varchar(16) NOT NULL,
  `status` varchar(16) NOT NULL DEFAULT 'Posted',
  `recorded_by` bigint(20) unsigned NOT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `journal_entry_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `boundary_payments_source_payment_key_unique` (`source_payment_key`),
  UNIQUE KEY `boundary_payments_initial_payment_remittance_id_unique` (`initial_payment_remittance_id`),
  UNIQUE KEY `boundary_payments_journal_entry_id_unique` (`journal_entry_id`),
  KEY `boundary_payments_daily_remittance_id_payment_date_index` (`daily_remittance_id`,`payment_date`),
  KEY `boundary_payments_recorded_by_foreign` (`recorded_by`),
  KEY `boundary_payments_approved_by_foreign` (`approved_by`),
  CONSTRAINT `boundary_payments_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`),
  CONSTRAINT `boundary_payments_daily_remittance_id_foreign` FOREIGN KEY (`daily_remittance_id`) REFERENCES `daily_remittances` (`id`),
  CONSTRAINT `boundary_payments_initial_payment_remittance_id_foreign` FOREIGN KEY (`initial_payment_remittance_id`) REFERENCES `daily_remittances` (`id`),
  CONSTRAINT `boundary_payments_journal_entry_id_foreign` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`),
  CONSTRAINT `boundary_payments_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `boundary_payments`
--

LOCK TABLES `boundary_payments` WRITE;
/*!40000 ALTER TABLE `boundary_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `boundary_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cash_advances`
--

DROP TABLE IF EXISTS `cash_advances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cash_advances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `repayment_months` int(11) NOT NULL DEFAULT 1,
  `monthly_deduction` decimal(12,2) NOT NULL DEFAULT 0.00,
  `amount_deducted` decimal(12,2) NOT NULL DEFAULT 0.00,
  `request_date` date DEFAULT NULL,
  `approval_date` date DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
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
  KEY `cash_advances_deducted_payroll_id_foreign` (`deducted_payroll_id`),
  CONSTRAINT `cash_advances_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cash_advances_deducted_payroll_id_foreign` FOREIGN KEY (`deducted_payroll_id`) REFERENCES `payrolls` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cash_advances_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cash_advances`
--

LOCK TABLES `cash_advances` WRITE;
/*!40000 ALTER TABLE `cash_advances` DISABLE KEYS */;
/*!40000 ALTER TABLE `cash_advances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chart_of_accounts`
--

DROP TABLE IF EXISTS `chart_of_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chart_of_accounts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `account_code` varchar(20) NOT NULL,
  `account_name` varchar(255) NOT NULL,
  `account_type` enum('Asset','Liability','Equity','Revenue','Expense') NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `chart_of_accounts_account_code_unique` (`account_code`),
  KEY `chart_of_accounts_created_by_foreign` (`created_by`),
  CONSTRAINT `chart_of_accounts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chart_of_accounts`
--

LOCK TABLES `chart_of_accounts` WRITE;
/*!40000 ALTER TABLE `chart_of_accounts` DISABLE KEYS */;
INSERT INTO `chart_of_accounts` VALUES (1,'101','rent expense','Expense','rental for the month of july',1,NULL,'2026-09-03 23:19:22','2026-09-03 23:19:22');
/*!40000 ALTER TABLE `chart_of_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `daily_remittances`
--

DROP TABLE IF EXISTS `daily_remittances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `daily_remittances` (
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
  `driver_status` enum('pending','partial','paid') DEFAULT 'pending',
  `pao_share` decimal(10,2) DEFAULT NULL,
  `pao_amount_paid` decimal(10,2) DEFAULT 0.00,
  `pao_status` enum('pending','partial','paid') DEFAULT 'pending',
  `resolution_notes` text DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `daily_remittances_driver_id_foreign` (`driver_id`),
  KEY `daily_remittances_pao_id_foreign` (`pao_id`),
  KEY `daily_remittances_route_id_foreign` (`route_id`),
  KEY `daily_remittances_vehicle_id_foreign` (`vehicle_id`),
  CONSTRAINT `daily_remittances_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `daily_remittances_pao_id_foreign` FOREIGN KEY (`pao_id`) REFERENCES `paos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `daily_remittances_route_id_foreign` FOREIGN KEY (`route_id`) REFERENCES `routes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `daily_remittances_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=123 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `daily_remittances`
--

LOCK TABLES `daily_remittances` WRITE;
/*!40000 ALTER TABLE `daily_remittances` DISABLE KEYS */;
/*!40000 ALTER TABLE `daily_remittances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `deductions`
--

DROP TABLE IF EXISTS `deductions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deductions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `payroll_id` bigint(20) unsigned DEFAULT NULL,
  `deduction_type` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `effective_date` date DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `deductions_user_id_foreign` (`user_id`),
  KEY `deductions_payroll_id_foreign` (`payroll_id`),
  CONSTRAINT `deductions_payroll_id_foreign` FOREIGN KEY (`payroll_id`) REFERENCES `payrolls` (`id`) ON DELETE SET NULL,
  CONSTRAINT `deductions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `deductions`
--

LOCK TABLES `deductions` WRITE;
/*!40000 ALTER TABLE `deductions` DISABLE KEYS */;
/*!40000 ALTER TABLE `deductions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `departments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `departments_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
INSERT INTO `departments` VALUES (1,'HR',1,'2026-09-03 22:18:22','2026-09-03 22:18:22'),(2,'Admin',1,'2026-09-03 22:18:39','2026-09-03 22:18:39'),(3,'Accounting',1,'2026-09-03 22:18:52','2026-09-03 22:18:52'),(6,'Employee',1,'2026-09-10 17:48:43','2026-09-10 17:48:43');
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `drivers`
--

DROP TABLE IF EXISTS `drivers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `drivers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `license_number` varchar(255) NOT NULL,
  `contact_number` varchar(255) NOT NULL,
  `gender` enum('male','female','prefer_not_to_say') DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `date_of_hire` date DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `drivers_license_number_unique` (`license_number`),
  UNIQUE KEY `drivers_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `drivers`
--

LOCK TABLES `drivers` WRITE;
/*!40000 ALTER TABLE `drivers` DISABLE KEYS */;
/*!40000 ALTER TABLE `drivers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_attachments`
--

DROP TABLE IF EXISTS `employee_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_attachments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `attachment_key` varchar(255) NOT NULL,
  `label` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `file_size` bigint(20) unsigned DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `rejection_reason` text DEFAULT NULL,
  `reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `uploaded_by_role` enum('hr','employee') NOT NULL DEFAULT 'hr',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_attachments_reviewed_by_foreign` (`reviewed_by`),
  KEY `employee_attachments_user_id_attachment_key_index` (`user_id`,`attachment_key`),
  CONSTRAINT `employee_attachments_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employee_attachments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_attachments`
--

LOCK TABLES `employee_attachments` WRITE;
/*!40000 ALTER TABLE `employee_attachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_attachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_beneficiaries`
--

DROP TABLE IF EXISTS `employee_beneficiaries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_beneficiaries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `relationship` varchar(255) DEFAULT NULL,
  `sequence` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_beneficiaries_user_id_foreign` (`user_id`),
  CONSTRAINT `employee_beneficiaries_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_beneficiaries`
--

LOCK TABLES `employee_beneficiaries` WRITE;
/*!40000 ALTER TABLE `employee_beneficiaries` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_beneficiaries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_experiences`
--

DROP TABLE IF EXISTS `employee_experiences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_experiences` (
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
  KEY `employee_experiences_user_id_foreign` (`user_id`),
  CONSTRAINT `employee_experiences_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_experiences`
--

LOCK TABLES `employee_experiences` WRITE;
/*!40000 ALTER TABLE `employee_experiences` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_experiences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_leave_balances`
--

DROP TABLE IF EXISTS `employee_leave_balances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_leave_balances` (
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
  KEY `employee_leave_balances_leave_type_id_foreign` (`leave_type_id`),
  CONSTRAINT `employee_leave_balances_leave_type_id_foreign` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_leave_balances_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_leave_balances`
--

LOCK TABLES `employee_leave_balances` WRITE;
/*!40000 ALTER TABLE `employee_leave_balances` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_leave_balances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_references`
--

DROP TABLE IF EXISTS `employee_references`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_references` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact_number` varchar(255) DEFAULT NULL,
  `sequence` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_references_user_id_foreign` (`user_id`),
  CONSTRAINT `employee_references_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_references`
--

LOCK TABLES `employee_references` WRITE;
/*!40000 ALTER TABLE `employee_references` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_references` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_skills`
--

DROP TABLE IF EXISTS `employee_skills`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_skills` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `skill_name` varchar(255) NOT NULL,
  `proficiency` varchar(255) DEFAULT NULL,
  `sequence` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_skills_user_id_foreign` (`user_id`),
  CONSTRAINT `employee_skills_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_skills`
--

LOCK TABLES `employee_skills` WRITE;
/*!40000 ALTER TABLE `employee_skills` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_skills` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `holidays`
--

DROP TABLE IF EXISTS `holidays`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `holidays` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `holidays`
--

LOCK TABLES `holidays` WRITE;
/*!40000 ALTER TABLE `holidays` DISABLE KEYS */;
INSERT INTO `holidays` VALUES (1,'New Year\'s Day','2026-01-01','regular',NULL,NULL),(2,'Araw ng Kagitingan','2026-04-09','regular',NULL,NULL),(3,'Labor Day','2026-05-01','regular',NULL,NULL),(4,'Independence Day','2026-06-12','regular',NULL,NULL),(5,'National Heroes Day','2026-08-31','regular',NULL,NULL),(6,'Bonifacio Day','2026-11-30','regular',NULL,NULL),(7,'Christmas Day','2026-12-25','regular',NULL,NULL),(8,'Rizal Day','2026-12-30','regular',NULL,NULL),(9,'EDSA People Power Revolution','2026-02-25','special',NULL,NULL),(10,'Ninoy Aquino Day','2026-08-21','special',NULL,NULL),(11,'All Saints\' Day','2026-11-01','special',NULL,NULL),(12,'Christmas Eve','2026-12-24','special',NULL,NULL),(13,'New Year\'s Eve','2026-12-31','special',NULL,NULL),(14,'New Year\'s Day','2027-01-01','regular',NULL,NULL),(15,'Araw ng Kagitingan','2027-04-09','regular',NULL,NULL),(16,'Labor Day','2027-05-01','regular',NULL,NULL),(17,'Independence Day','2027-06-12','regular',NULL,NULL),(18,'National Heroes Day','2027-08-31','regular',NULL,NULL),(19,'Bonifacio Day','2027-11-30','regular',NULL,NULL),(20,'Christmas Day','2027-12-25','regular',NULL,NULL),(21,'Rizal Day','2027-12-30','regular',NULL,NULL),(22,'EDSA People Power Revolution','2027-02-25','special',NULL,NULL),(23,'Ninoy Aquino Day','2027-08-21','special',NULL,NULL),(24,'All Saints\' Day','2027-11-01','special',NULL,NULL),(25,'Christmas Eve','2027-12-24','special',NULL,NULL),(26,'New Year\'s Eve','2027-12-31','special',NULL,NULL),(27,'New Year\'s Day','2028-01-01','regular',NULL,NULL),(28,'Araw ng Kagitingan','2028-04-09','regular',NULL,NULL),(29,'Labor Day','2028-05-01','regular',NULL,NULL),(30,'Independence Day','2028-06-12','regular',NULL,NULL),(31,'National Heroes Day','2028-08-31','regular',NULL,NULL),(32,'Bonifacio Day','2028-11-30','regular',NULL,NULL),(33,'Christmas Day','2028-12-25','regular',NULL,NULL),(34,'Rizal Day','2028-12-30','regular',NULL,NULL),(35,'EDSA People Power Revolution','2028-02-25','special',NULL,NULL),(36,'Ninoy Aquino Day','2028-08-21','special',NULL,NULL),(37,'All Saints\' Day','2028-11-01','special',NULL,NULL),(38,'Christmas Eve','2028-12-24','special',NULL,NULL),(39,'New Year\'s Eve','2028-12-31','special',NULL,NULL),(40,'New Year\'s Day','2029-01-01','regular',NULL,NULL),(41,'Araw ng Kagitingan','2029-04-09','regular',NULL,NULL),(42,'Labor Day','2029-05-01','regular',NULL,NULL),(43,'Independence Day','2029-06-12','regular',NULL,NULL),(44,'National Heroes Day','2029-08-31','regular',NULL,NULL),(45,'Bonifacio Day','2029-11-30','regular',NULL,NULL),(46,'Christmas Day','2029-12-25','regular',NULL,NULL),(47,'Rizal Day','2029-12-30','regular',NULL,NULL),(48,'EDSA People Power Revolution','2029-02-25','special',NULL,NULL),(49,'Ninoy Aquino Day','2029-08-21','special',NULL,NULL),(50,'All Saints\' Day','2029-11-01','special',NULL,NULL),(51,'Christmas Eve','2029-12-24','special',NULL,NULL),(52,'New Year\'s Eve','2029-12-31','special',NULL,NULL),(53,'New Year\'s Day','2030-01-01','regular',NULL,NULL),(54,'Araw ng Kagitingan','2030-04-09','regular',NULL,NULL),(55,'Labor Day','2030-05-01','regular',NULL,NULL),(56,'Independence Day','2030-06-12','regular',NULL,NULL),(57,'National Heroes Day','2030-08-31','regular',NULL,NULL),(58,'Bonifacio Day','2030-11-30','regular',NULL,NULL),(59,'Christmas Day','2030-12-25','regular',NULL,NULL),(60,'Rizal Day','2030-12-30','regular',NULL,NULL),(61,'EDSA People Power Revolution','2030-02-25','special',NULL,NULL),(62,'Ninoy Aquino Day','2030-08-21','special',NULL,NULL),(63,'All Saints\' Day','2030-11-01','special',NULL,NULL),(64,'Christmas Eve','2030-12-24','special',NULL,NULL),(65,'New Year\'s Eve','2030-12-31','special',NULL,NULL),(66,'New Year\'s Day','2031-01-01','regular',NULL,NULL),(67,'Araw ng Kagitingan','2031-04-09','regular',NULL,NULL),(68,'Labor Day','2031-05-01','regular',NULL,NULL),(69,'Independence Day','2031-06-12','regular',NULL,NULL),(70,'National Heroes Day','2031-08-31','regular',NULL,NULL),(71,'Bonifacio Day','2031-11-30','regular',NULL,NULL),(72,'Christmas Day','2031-12-25','regular',NULL,NULL),(73,'Rizal Day','2031-12-30','regular',NULL,NULL),(74,'EDSA People Power Revolution','2031-02-25','special',NULL,NULL),(75,'Ninoy Aquino Day','2031-08-21','special',NULL,NULL),(76,'All Saints\' Day','2031-11-01','special',NULL,NULL),(77,'Christmas Eve','2031-12-24','special',NULL,NULL),(78,'New Year\'s Eve','2031-12-31','special',NULL,NULL);
/*!40000 ALTER TABLE `holidays` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `journal_entries`
--

DROP TABLE IF EXISTS `journal_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `journal_entries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `journal_number` varchar(30) NOT NULL,
  `transaction_date` date NOT NULL,
  `description` varchar(255) NOT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `reference_id` bigint(20) unsigned DEFAULT NULL,
  `status` enum('Draft','Posted','Void') NOT NULL DEFAULT 'Draft',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `posted_by` bigint(20) unsigned DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `journal_entries_journal_number_unique` (`journal_number`),
  KEY `journal_entries_created_by_foreign` (`created_by`),
  KEY `journal_entries_posted_by_foreign` (`posted_by`),
  KEY `journal_entries_reference_type_reference_id_index` (`reference_type`,`reference_id`),
  CONSTRAINT `journal_entries_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `journal_entries_posted_by_foreign` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `journal_entries`
--

LOCK TABLES `journal_entries` WRITE;
/*!40000 ALTER TABLE `journal_entries` DISABLE KEYS */;
/*!40000 ALTER TABLE `journal_entries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `journal_entry_lines`
--

DROP TABLE IF EXISTS `journal_entry_lines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `journal_entry_lines` (
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
  KEY `journal_entry_lines_account_id_foreign` (`account_id`),
  CONSTRAINT `journal_entry_lines_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `chart_of_accounts` (`id`),
  CONSTRAINT `journal_entry_lines_journal_entry_id_foreign` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `journal_entry_lines`
--

LOCK TABLES `journal_entry_lines` WRITE;
/*!40000 ALTER TABLE `journal_entry_lines` DISABLE KEYS */;
/*!40000 ALTER TABLE `journal_entry_lines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leave_types`
--

DROP TABLE IF EXISTS `leave_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leave_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `abbreviation` varchar(255) DEFAULT NULL,
  `days_allowed` int(11) NOT NULL DEFAULT 0,
  `carry_over` tinyint(1) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_types`
--

LOCK TABLES `leave_types` WRITE;
/*!40000 ALTER TABLE `leave_types` DISABLE KEYS */;
INSERT INTO `leave_types` VALUES (1,'Vacation Leave','VL',15,1,'Annual vacation leave for rest and recreation','active','2026-09-03 06:01:03','2026-09-03 06:01:03'),(2,'Sick Leave','SL',15,1,'Leave for illness or medical appointments','active','2026-09-03 06:01:03','2026-09-03 06:01:03'),(3,'Maternity Leave','ML',105,0,'Maternity leave for female employees (105 days)','active','2026-09-03 06:01:03','2026-09-03 06:01:03'),(4,'Paternity Leave','PL',7,0,'Paternity leave for male employees','active','2026-09-03 06:01:03','2026-09-03 06:01:03'),(5,'Emergency Leave','EL',5,0,'Leave for emergency situations or family emergencies','active','2026-09-03 06:01:03','2026-09-03 06:01:03'),(6,'Bereavement Leave','BL',5,0,'Leave for the death of an immediate family member','active','2026-09-03 06:01:03','2026-09-03 06:01:03'),(7,'Solo Parent','SoP',7,0,'Under RA 8972','inactive','2026-09-18 18:39:34','2026-09-18 18:39:34');
/*!40000 ALTER TABLE `leave_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leaves`
--

DROP TABLE IF EXISTS `leaves`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leaves` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `leave_type_id` bigint(20) unsigned DEFAULT NULL,
  `leave_type` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('pending','approved','rejected','paid') NOT NULL DEFAULT 'pending',
  `paid_days` int(11) NOT NULL DEFAULT 0,
  `unpaid_days` int(11) NOT NULL DEFAULT 0,
  `rejection_reason` text DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leaves_user_id_foreign` (`user_id`),
  KEY `leaves_approved_by_foreign` (`approved_by`),
  KEY `leaves_leave_type_id_foreign` (`leave_type_id`),
  CONSTRAINT `leaves_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leaves_leave_type_id_foreign` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leaves_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leaves`
--

LOCK TABLES `leaves` WRITE;
/*!40000 ALTER TABLE `leaves` DISABLE KEYS */;
/*!40000 ALTER TABLE `leaves` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `type` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=199 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `overtime_undertimes`
--

DROP TABLE IF EXISTS `overtime_undertimes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `overtime_undertimes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `date` date NOT NULL,
  `type` varchar(255) NOT NULL,
  `hours` decimal(5,2) NOT NULL,
  `amount` decimal(15,2) DEFAULT NULL,
  `hourly_rate_used` decimal(15,2) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `overtime_undertimes_user_id_foreign` (`user_id`),
  KEY `overtime_undertimes_approved_by_foreign` (`approved_by`),
  CONSTRAINT `overtime_undertimes_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `overtime_undertimes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `overtime_undertimes`
--

LOCK TABLES `overtime_undertimes` WRITE;
/*!40000 ALTER TABLE `overtime_undertimes` DISABLE KEYS */;
/*!40000 ALTER TABLE `overtime_undertimes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paos`
--

DROP TABLE IF EXISTS `paos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `contact_number` varchar(255) NOT NULL,
  `gender` enum('male','female','prefer_not_to_say') DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `date_of_hire` date DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `paos_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paos`
--

LOCK TABLES `paos` WRITE;
/*!40000 ALTER TABLE `paos` DISABLE KEYS */;
INSERT INTO `paos` VALUES (1,'Juan Bo','09318973202','male','juanbo@gmail.com','Concepcion Marikina City','2025-09-01','active','2026-09-17 01:13:08','2026-09-30 19:58:05'),(2,'Raymart Dizon','09182468761',NULL,NULL,NULL,NULL,'active','2026-09-30 19:56:19','2026-09-30 19:56:19'),(3,'Elco Monforte','09654813546',NULL,NULL,NULL,NULL,'active','2026-09-30 19:57:47','2026-09-30 19:57:47'),(4,'Bertmon Reyes','09256484354',NULL,NULL,NULL,NULL,'active','2026-09-30 19:58:39','2026-09-30 19:58:39'),(5,'Noel Ragua','09111111111',NULL,NULL,NULL,NULL,'active','2026-09-30 20:00:15','2026-09-30 20:00:15'),(6,'John Joseph Barcelon','09185623547',NULL,NULL,NULL,NULL,'active','2026-09-30 20:01:07','2026-09-30 20:01:07'),(7,'Samue Alcantara','09584321654',NULL,NULL,NULL,NULL,'active','2026-09-30 20:02:20','2026-09-30 20:02:20'),(8,'Kris Aldin Gayola','09845621564',NULL,NULL,NULL,NULL,'active','2026-09-30 20:02:37','2026-09-30 20:02:37'),(9,'Ediever Guilaran','09548976213',NULL,NULL,NULL,NULL,'active','2026-09-30 20:03:56','2026-09-30 20:03:56'),(10,'Kristian Bindoy','09256498731',NULL,NULL,NULL,NULL,'active','2026-09-30 20:04:33','2026-09-30 20:04:33'),(11,'Marina Sarno','09321654898',NULL,NULL,NULL,NULL,'active','2026-10-01 00:54:29','2026-10-01 00:54:29'),(12,'Myrna Mayuga','09321654789',NULL,NULL,NULL,NULL,'active','2026-10-01 18:15:09','2026-10-01 18:15:09'),(13,'Michael Tiamson','09366589757',NULL,NULL,NULL,NULL,'active','2026-10-01 18:29:41','2026-10-01 18:29:41'),(14,'Carl James Obeña','09513565749',NULL,NULL,NULL,NULL,'active','2026-10-01 18:33:48','2026-10-01 18:33:48'),(15,'Mary Grace Atoli','09456897651',NULL,NULL,NULL,NULL,'active','2026-10-01 18:42:49','2026-10-01 18:42:49'),(16,'Darren Lloyd Kho','09189876532',NULL,NULL,NULL,NULL,'active','2026-10-01 19:01:12','2026-10-01 19:01:12'),(17,'Lenie Nunez','09316879463',NULL,NULL,NULL,NULL,'active','2026-10-01 19:07:22','2026-10-01 19:07:22'),(18,'Jorick Batiancila','09598987416',NULL,NULL,NULL,NULL,'active','2026-10-01 19:08:55','2026-10-01 19:08:55'),(19,'Eloisa Meterio','09569879543',NULL,NULL,NULL,NULL,'active','2026-10-01 19:09:58','2026-10-01 19:09:58'),(20,'Joel Divina','09565468971',NULL,NULL,NULL,NULL,'active','2026-10-13 17:57:02','2026-10-13 17:57:02'),(21,'Ricky Carcueva','09088796531',NULL,NULL,NULL,NULL,'active','2026-10-13 18:00:21','2026-10-13 18:00:21'),(22,'Jhunelle Sarte','09564987651',NULL,NULL,NULL,NULL,'active','2026-10-13 18:43:13','2026-10-13 18:43:13');
/*!40000 ALTER TABLE `paos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_allowances`
--

DROP TABLE IF EXISTS `payroll_allowances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_allowances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payroll_id` bigint(20) unsigned NOT NULL,
  `allowance_type` varchar(255) NOT NULL,
  `hours` decimal(8,2) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payroll_allowances_payroll_id_foreign` (`payroll_id`),
  CONSTRAINT `payroll_allowances_payroll_id_foreign` FOREIGN KEY (`payroll_id`) REFERENCES `payrolls` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_allowances`
--

LOCK TABLES `payroll_allowances` WRITE;
/*!40000 ALTER TABLE `payroll_allowances` DISABLE KEYS */;
/*!40000 ALTER TABLE `payroll_allowances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_batches`
--

DROP TABLE IF EXISTS `payroll_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_batches` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL DEFAULT 'regular',
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `status` enum('draft','submitted','approved','rejected') NOT NULL DEFAULT 'draft',
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
  KEY `payroll_batches_type_index` (`type`),
  CONSTRAINT `payroll_batches_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payroll_batches_finalized_by_foreign` FOREIGN KEY (`finalized_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payroll_batches_generated_by_foreign` FOREIGN KEY (`generated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payroll_batches_rejected_by_foreign` FOREIGN KEY (`rejected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_batches`
--

LOCK TABLES `payroll_batches` WRITE;
/*!40000 ALTER TABLE `payroll_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `payroll_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_bonuses`
--

DROP TABLE IF EXISTS `payroll_bonuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_bonuses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payroll_id` bigint(20) unsigned NOT NULL,
  `bonus_type` enum('performance','holiday','attendance','special') NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payroll_bonuses_payroll_id_foreign` (`payroll_id`),
  CONSTRAINT `payroll_bonuses_payroll_id_foreign` FOREIGN KEY (`payroll_id`) REFERENCES `payrolls` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_bonuses`
--

LOCK TABLES `payroll_bonuses` WRITE;
/*!40000 ALTER TABLE `payroll_bonuses` DISABLE KEYS */;
/*!40000 ALTER TABLE `payroll_bonuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_cutoff_schedules`
--

DROP TABLE IF EXISTS `payroll_cutoff_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_cutoff_schedules` (
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_cutoff_schedules`
--

LOCK TABLES `payroll_cutoff_schedules` WRITE;
/*!40000 ALTER TABLE `payroll_cutoff_schedules` DISABLE KEYS */;
INSERT INTO `payroll_cutoff_schedules` VALUES (1,15,'1st Half',NULL,NULL,1,'2026-09-03 06:01:03','2026-09-29 00:27:03'),(2,31,'2nd Half',NULL,NULL,0,'2026-09-03 06:01:03','2026-09-29 00:27:03');
/*!40000 ALTER TABLE `payroll_cutoff_schedules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_deductions`
--

DROP TABLE IF EXISTS `payroll_deductions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_deductions` (
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
  KEY `payroll_deductions_payroll_id_foreign` (`payroll_id`),
  CONSTRAINT `payroll_deductions_payroll_id_foreign` FOREIGN KEY (`payroll_id`) REFERENCES `payrolls` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_deductions`
--

LOCK TABLES `payroll_deductions` WRITE;
/*!40000 ALTER TABLE `payroll_deductions` DISABLE KEYS */;
/*!40000 ALTER TABLE `payroll_deductions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payrolls`
--

DROP TABLE IF EXISTS `payrolls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payrolls` (
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
  `loan_deduction_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `basic_salary` decimal(12,2) NOT NULL DEFAULT 0.00,
  `gross_pay` decimal(12,2) NOT NULL DEFAULT 0.00,
  `holiday_pay` decimal(12,2) NOT NULL DEFAULT 0.00,
  `holiday_ot_pay` decimal(12,2) NOT NULL DEFAULT 0.00,
  `holiday_ot_hours` decimal(8,2) NOT NULL DEFAULT 0.00,
  `holiday_breakdown` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payrolls`
--

LOCK TABLES `payrolls` WRITE;
/*!40000 ALTER TABLE `payrolls` DISABLE KEYS */;
/*!40000 ALTER TABLE `payrolls` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pinned_items`
--

DROP TABLE IF EXISTS `pinned_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pinned_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `label` varchar(255) NOT NULL,
  `url` varchar(500) NOT NULL,
  `sort_order` smallint(5) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pinned_items_user_id_url_unique` (`user_id`,`url`),
  KEY `pinned_items_user_id_sort_order_index` (`user_id`,`sort_order`),
  CONSTRAINT `pinned_items_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pinned_items`
--

LOCK TABLES `pinned_items` WRITE;
/*!40000 ALTER TABLE `pinned_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `pinned_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `position_rates`
--

DROP TABLE IF EXISTS `position_rates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `position_rates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `daily_rate` decimal(10,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `position_rates_name_unique` (`name`),
  KEY `position_rates_department_id_foreign` (`department_id`),
  CONSTRAINT `position_rates_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `position_rates`
--

LOCK TABLES `position_rates` WRITE;
/*!40000 ALTER TABLE `position_rates` DISABLE KEYS */;
INSERT INTO `position_rates` VALUES (1,1,'HR',5000.00,1,'2026-09-03 22:20:10','2026-09-03 22:20:10'),(2,3,'Accounting',1000.00,1,'2026-09-03 22:28:25','2026-09-03 22:28:25'),(3,2,'Admin',800.00,1,'2026-09-03 22:28:40','2026-09-03 22:28:40'),(4,NULL,'Driver',750.00,1,'2026-09-03 22:29:00','2026-09-03 22:29:00'),(5,NULL,'Pao',650.00,1,'2026-09-03 22:29:11','2026-09-03 22:29:11');
/*!40000 ALTER TABLE `position_rates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `routes`
--

DROP TABLE IF EXISTS `routes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `routes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `route_name` varchar(255) NOT NULL,
  `weekday_boundary` decimal(10,2) NOT NULL DEFAULT 0.00,
  `weekend_boundary` decimal(10,2) NOT NULL DEFAULT 0.00,
  `origin` varchar(255) NOT NULL,
  `destination` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `routes`
--

LOCK TABLES `routes` WRITE;
/*!40000 ALTER TABLE `routes` DISABLE KEYS */;
/*!40000 ALTER TABLE `routes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shifts`
--

DROP TABLE IF EXISTS `shifts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shifts` (
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shifts`
--

LOCK TABLES `shifts` WRITE;
/*!40000 ALTER TABLE `shifts` DISABLE KEYS */;
INSERT INTO `shifts` VALUES (1,'Office Hours','08:00:00','17:00:00','12:00:00','13:00:00',1,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(2,'Operation','05:00:00','15:00:00','11:00:00','12:00:00',1,'2026-09-03 22:34:51','2026-09-18 17:24:22'),(3,'Evening Dispatch','13:00:00','22:00:00','18:00:00','19:00:00',1,'2026-09-03 22:36:19','2026-09-18 19:27:09'),(4,'Evening Shift','14:00:00','23:00:00','18:00:00','19:00:00',1,'2026-09-18 17:22:45','2026-09-18 17:22:45'),(5,'Morning Dispatch','04:00:00','13:00:00','12:00:00','13:00:00',1,'2026-09-18 17:26:40','2026-09-18 17:26:40'),(6,'Office Shift','09:00:00','18:00:00','12:00:00','13:00:00',1,'2026-09-18 20:58:30','2026-09-18 20:58:30');
/*!40000 ALTER TABLE `shifts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `statutory_deductions`
--

DROP TABLE IF EXISTS `statutory_deductions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `statutory_deductions` (
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
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `statutory_deductions`
--

LOCK TABLES `statutory_deductions` WRITE;
/*!40000 ALTER TABLE `statutory_deductions` DISABLE KEYS */;
INSERT INTO `statutory_deductions` VALUES (1,'SSS',0.00,5249.99,250.00,510.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(2,'SSS',5250.00,5749.99,275.00,560.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(3,'SSS',5750.00,6249.99,300.00,610.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(4,'SSS',6250.00,6749.99,325.00,660.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(5,'SSS',6750.00,7249.99,350.00,710.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(6,'SSS',7250.00,7749.99,375.00,760.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(7,'SSS',7750.00,8249.99,400.00,810.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(8,'SSS',8250.00,8749.99,425.00,860.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(9,'SSS',8750.00,9249.99,450.00,910.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(10,'SSS',9250.00,9749.99,475.00,960.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(11,'SSS',9750.00,10249.99,500.00,1010.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(12,'SSS',10250.00,10749.99,525.00,1060.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(13,'SSS',10750.00,11249.99,550.00,1110.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(14,'SSS',11250.00,11749.99,575.00,1160.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(15,'SSS',11750.00,12249.99,600.00,1210.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(16,'SSS',12250.00,12749.99,625.00,1260.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(17,'SSS',12750.00,13249.99,650.00,1310.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(18,'SSS',13250.00,13749.99,675.00,1360.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(19,'SSS',13750.00,14249.99,700.00,1410.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(20,'SSS',14250.00,14749.99,725.00,1460.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(21,'SSS',14750.00,15249.99,750.00,1530.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(22,'SSS',15250.00,15749.99,775.00,1580.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(23,'SSS',15750.00,16249.99,800.00,1630.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(24,'SSS',16250.00,16749.99,825.00,1680.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(25,'SSS',16750.00,17249.99,850.00,1730.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(26,'SSS',17250.00,17749.99,875.00,1780.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(27,'SSS',17750.00,18249.99,900.00,1830.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(28,'SSS',18250.00,18749.99,925.00,1880.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(29,'SSS',18750.00,19249.99,950.00,1930.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(30,'SSS',19250.00,19749.99,975.00,1980.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(31,'SSS',19750.00,20249.99,1000.00,2030.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(32,'SSS',20250.00,20749.99,1025.00,2080.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(33,'SSS',20750.00,21249.99,1050.00,2130.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(34,'SSS',21250.00,21749.99,1075.00,2180.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(35,'SSS',21750.00,22249.99,1100.00,2230.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(36,'SSS',22250.00,22749.99,1125.00,2280.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(37,'SSS',22750.00,23249.99,1150.00,2330.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(38,'SSS',23250.00,23749.99,1175.00,2380.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(39,'SSS',23750.00,24249.99,1200.00,2430.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(40,'SSS',24250.00,24749.99,1225.00,2480.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(41,'SSS',24750.00,25249.99,1250.00,2530.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(42,'SSS',25250.00,25749.99,1275.00,2580.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(43,'SSS',25750.00,26249.99,1300.00,2630.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(44,'SSS',26250.00,26749.99,1325.00,2680.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(45,'SSS',26750.00,27249.99,1350.00,2730.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(46,'SSS',27250.00,27749.99,1375.00,2780.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(47,'SSS',27750.00,28249.99,1400.00,2830.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(48,'SSS',28250.00,28749.99,1425.00,2880.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(49,'SSS',28750.00,29249.99,1450.00,2930.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(50,'SSS',29250.00,29749.99,1475.00,2980.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(51,'SSS',29750.00,30249.99,1500.00,3030.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(52,'SSS',30250.00,30749.99,1525.00,3080.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(53,'SSS',30750.00,31249.99,1550.00,3130.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(54,'SSS',31250.00,31749.99,1575.00,3180.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(55,'SSS',31750.00,32249.99,1600.00,3230.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(56,'SSS',32250.00,32749.99,1625.00,3280.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(57,'SSS',32750.00,33249.99,1650.00,3330.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(58,'SSS',33250.00,33749.99,1675.00,3380.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(59,'SSS',33750.00,34249.99,1700.00,3430.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(60,'SSS',34250.00,34749.99,1725.00,3480.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(61,'SSS',34750.00,999999999.00,1750.00,3530.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(62,'Pag-IBIG',0.00,1500.00,NULL,NULL,1.00,2.00,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(63,'Pag-IBIG',1500.01,999999999.00,NULL,NULL,2.00,2.00,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(64,'PhilHealth',0.00,10000.00,250.00,250.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(65,'PhilHealth',10000.01,99999.99,NULL,NULL,2.50,2.50,'2026-09-03 06:01:03','2026-09-03 06:01:03'),(66,'PhilHealth',100000.00,999999999.00,2500.00,2500.00,NULL,NULL,'2026-09-03 06:01:03','2026-09-03 06:01:03');
/*!40000 ALTER TABLE `statutory_deductions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `thirteenth_month_pays`
--

DROP TABLE IF EXISTS `thirteenth_month_pays`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `thirteenth_month_pays` (
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
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `payment_date` date DEFAULT NULL,
  `paid_by` bigint(20) unsigned DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `computed_by` bigint(20) unsigned DEFAULT NULL,
  `computed_at` timestamp NULL DEFAULT NULL,
  `computation_breakdown` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `thirteenth_month_pays`
--

LOCK TABLES `thirteenth_month_pays` WRITE;
/*!40000 ALTER TABLE `thirteenth_month_pays` DISABLE KEYS */;
/*!40000 ALTER TABLE `thirteenth_month_pays` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_activities`
--

DROP TABLE IF EXISTS `user_activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_activities` (
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
  KEY `user_activities_user_id_created_at_index` (`user_id`,`created_at`),
  CONSTRAINT `user_activities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2595 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_activities`
--

LOCK TABLES `user_activities` WRITE;
/*!40000 ALTER TABLE `user_activities` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_activities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `password_changed` tinyint(1) NOT NULL DEFAULT 0,
  `role` varchar(255) NOT NULL DEFAULT 'employee',
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
  `gender` enum('male','female','prefer_not_to_say') DEFAULT NULL,
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
  `status` varchar(255) NOT NULL DEFAULT 'active',
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
  `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Super Admin','super_admin','$2y$12$YhTTF5BRZfCgAA9.G1RFyObHHru/l1suzFQ2cXZvWvYYze66mQ/hS',0,'superadmin',NULL,'kGJJIedNYMXz7nGhWkPdQEOSuai3FNzSX01mr5WBaTNQh7mxQn0d8Ja7BSae',1,'Super',NULL,'Admin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',0.00,5,NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,0,0,NULL,NULL,'2026-09-03 06:01:03','2026-10-20 06:39:14','2026-10-20 06:39:14'),(2,'QR Attendance Admin','qr_admin','$2y$12$YhTTF5BRZfCgAA9.G1RFyObHHru/l1suzFQ2cXZvWvYYze66mQ/hS',0,'qr_admin',NULL,'xaJY2iNiK6cNR2klK1Q3FZ60SnvLDu4BcS4L72hlxIDvb4sS7Mh6IJqt0Spy',1,'QR',NULL,'Admin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',0.00,5,NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,0,0,NULL,NULL,'2026-09-03 06:01:03','2026-09-11 16:39:40','2026-09-11 16:39:40'),(3,'Maria Carmel Galino Wacay','melwacay','$2y$12$Hl/oT6oNl4sHMccJeHkOR.4cXfgXumCUOr15ecsjwgSHSWeAnEIgW',0,'hr',NULL,'PR0s7LPINDEXQiHJWJEym6NwKNvqLuYdGU0xXJkUbieZHfsS2tEqaVwFB5zq',1,'Maria Carmel','Galino','Wacay','maria.wacay@gmail.com','09959221488','{\"street\":\"549-C Dir A Bunye Street Lakeside III Purok 3\",\"barangay\":\"Sucat\",\"city\":\"Muntinlupa City\",\"province\":\"National Capital Region\"}','Single','female',NULL,'1970-11-10','Anini-y Antique','College (Bachelor\'s)',NULL,NULL,'2022-10-04','HR',1,'HR','active',5000.00,6,'BPI',NULL,NULL,1,'07-3105391-5','216-204-486',1,'1010-0108-2645',NULL,1,1,NULL,NULL,'2026-09-03 22:22:13','2026-09-18 20:52:22','2026-09-18 18:21:27'),(4,'beth boongaling','beth.boongaling','$2y$12$zTkN4TdQhMw5bR9y1sDhMeSNiUAtBSWNqvcTQcI9jKuThIxetS7K2',0,'accountant',NULL,NULL,1,'beth',NULL,'boongaling','elizabethboongaling04@gmail.com','09201213683','{\"street\":\"blk 6 lot 6 Millenium homes\",\"barangay\":\"burgos\",\"city\":\"rodriguez\",\"province\":\"rizal\"}','Married','female',NULL,'1986-04-04','San Mateo, Rizal','College (Bachelor\'s)',NULL,NULL,'2019-09-23','Accounting',1,'Accounting','active',1000.00,5,NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,0,0,NULL,NULL,'2026-09-03 22:33:32','2026-10-01 18:23:23','2026-10-01 18:23:23'),(7,'Mary Grace Manduriao','marygrace.mandurioa','$2y$12$rGs5XGdVUa6CnOVSQnBaFuhTDcSSsvSqRQPAmQckKtbAd77P7HZ86',0,'employee',NULL,NULL,1,'Mary Grace',NULL,'Manduriao','marygracemanduriao@gmail.com','09368766406','{\"street\":\"Briget, Torres Comp\",\"barangay\":\"Ampid 1\",\"city\":\"San Mateo\",\"province\":\"Rizal\"}','Married','female',NULL,'1986-04-17','San Mateo, Rizal','College (Bachelor\'s)',NULL,NULL,'2023-09-01','Admin',NULL,'Admin','active',800.00,5,NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,0,0,NULL,NULL,'2026-09-10 16:57:20','2026-09-10 17:07:54','2026-09-10 17:07:54'),(8,'Joshua  Boongaling','joshua.boongaling','$2y$12$.dYgOQmjLiistQRmB87ZburlUWXwD5sfYdo79deYCUDFO4P6T3nSC',0,'employee',NULL,NULL,1,'Joshua',NULL,'Boongaling',NULL,'09054857450','{\"street\":\"blk 6 lot 6 Millenium homes\",\"barangay\":\"burgos\",\"city\":\"rodriguez\",\"province\":\"Rizal\"}','Single','male',NULL,'1999-06-22','Quezon City','College (Bachelor\'s)',NULL,NULL,'2021-07-11','Admin',NULL,'Admin','active',800.00,5,NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,0,0,NULL,NULL,'2026-09-10 17:20:22','2026-09-17 19:20:20','2026-09-17 19:20:20'),(9,'Manny  Dela Cruz','manny.delacruz','$2y$12$EphI47jSJ7wE7QcQ3yueYeiBKUuEouqy1Nz6k4cj3hrPoKv2SDr/a',0,'employee',NULL,NULL,1,'Manny',NULL,'Dela Cruz',NULL,'09617131703','{\"street\":\"blk 1 lot 16 Millenium homes\",\"barangay\":\"Burgos\",\"city\":\"Rodriguez\",\"province\":\"Rizal\"}','Married','male',NULL,'1978-06-12','Pangasinan','College (Bachelor\'s)',NULL,NULL,'2024-01-01','Admin',NULL,'Admin','active',800.00,5,NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,0,0,NULL,NULL,'2026-09-10 17:42:18','2026-09-10 20:49:33','2026-09-10 20:49:33'),(10,'Ronolfo  Alcanse','ronolfo.alcanse','$2y$12$ZGHVYMJF2m.v7cA5eKVBjOXJiVbPb95TfwFXtXwiyOryIkMy4s2zy',0,'employee',NULL,NULL,1,'Ronolfo',NULL,'Alcanse','ronniealcanse1977@gmail.com','09958630020','{\"street\":\"Blk 2 Lot 1 Millenium Homes\",\"barangay\":\"Burgos\",\"city\":\"Rodriguez\",\"province\":\"Rizal\"}','Married','male',NULL,'1978-08-10','Cavite','College (Bachelor\'s)',NULL,NULL,'2024-01-01','Admin',3,'Admin','active',800.00,5,NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,0,0,NULL,NULL,'2026-09-10 17:57:04','2026-09-18 18:52:24','2026-09-10 21:09:15'),(11,'Kimberly Magayones Ramos','kimberly.ramos','$2y$12$.oX9TY3OAVPPNDm4MmtSpueento.rir/CmaeznK2THZkRpDRuJgt2',0,'employee',NULL,NULL,0,'Kimberly','Magayones','Ramos','khimr@gmail.com','09668025877','{\"street\":\"0490 Area 6\",\"barangay\":\"Botocan\",\"city\":\"Quezon City\",\"province\":\"National Capital Region\"}','Single','female',NULL,'1996-04-30','Quezon City','Vocational / TESDA',NULL,NULL,'2026-09-15','Admin',4,'Admin','active',800.00,5,NULL,NULL,NULL,1,'35-1100417-8','611-600-080',1,'1212-8870-1922',NULL,1,0,NULL,NULL,'2026-09-18 19:22:51','2026-10-13 17:22:09',NULL);
(1,'Super Admin','super_admin','$2y$12$YhTTF5BRZfCgAA9.G1RFyObHHru/l1suzFQ2cXZvWvYYze66mQ/hS',0,'superadmin',NULL,'kGJJIedNYMXz7nGhWkPdQEOSuai3FNzSX01mr5WBaTNQh7mxQn0d8Ja7BSae',1,'Super',NULL,'Admin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',0.00,5,NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,0,0,NULL,NULL,'2026-09-03 06:01:03','2026-10-20 06:39:14','2026-10-20 06:39:14'),
(2,'QR Attendance Admin','qr_admin','$2y$12$YhTTF5BRZfCgAA9.G1RFyObHHru/l1suzFQ2cXZvWvYYze66mQ/hS',0,'qr_admin',NULL,'xaJY2iNiK6cNR2klK1Q3FZ60SnvLDu4BcS4L72hlxIDvb4sS7Mh6IJqt0Spy',1,'QR',NULL,'Admin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',0.00,5,NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,0,0,NULL,NULL,'2026-09-03 06:01:03','2026-09-11 16:39:40','2026-09-11 16:39:40'),
(3,'Maria Carmel Galino Wacay','melwacay','$2y$12$Hl/oT6oNl4sHMccJeHkOR.4cXfgXumCUOr15ecsjwgSHSWeAnEIgW',0,'hr',NULL,'PR0s7LPINDEXQiHJWJEym6NwKNvqLuYdGU0xXJkUbieZHfsS2tEqaVwFB5zq',1,'Maria Carmel','Galino','Wacay','maria.wacay@gmail.com','09959221488','{\"street\":\"549-C Dir A Bunye Street Lakeside III Purok 3\",\"barangay\":\"Sucat\",\"city\":\"Muntinlupa City\",\"province\":\"National Capital Region\"}','Single','female',NULL,'1970-11-10','Anini-y Antique','College (Bachelor\'s),
(4,'beth boongaling','beth.boongaling','$2y$12$zTkN4TdQhMw5bR9y1sDhMeSNiUAtBSWNqvcTQcI9jKuThIxetS7K2',0,'accountant',NULL,NULL,1,'beth',NULL,'boongaling','elizabethboongaling04@gmail.com','09201213683','{\"street\":\"blk 6 lot 6 Millenium homes\",\"barangay\":\"burgos\",\"city\":\"rodriguez\",\"province\":\"rizal\"}','Married','female',NULL,'1986-04-04','San Mateo, Rizal','College (Bachelor\'s),
(7,'Mary Grace Manduriao','marygrace.mandurioa','$2y$12$rGs5XGdVUa6CnOVSQnBaFuhTDcSSsvSqRQPAmQckKtbAd77P7HZ86',0,'employee',NULL,NULL,1,'Mary Grace',NULL,'Manduriao','marygracemanduriao@gmail.com','09368766406','{\"street\":\"Briget, Torres Comp\",\"barangay\":\"Ampid 1\",\"city\":\"San Mateo\",\"province\":\"Rizal\"}','Married','female',NULL,'1986-04-17','San Mateo, Rizal','College (Bachelor\'s);
UNLOCK TABLES;

--
-- Table structure for table `vehicles`
--

DROP TABLE IF EXISTS `vehicles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vehicles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `plate_number` varchar(255) NOT NULL,
  `route_id` bigint(20) unsigned NOT NULL,
  `operator` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vehicles_plate_number_unique` (`plate_number`),
  KEY `vehicles_route_id_foreign` (`route_id`),
  CONSTRAINT `vehicles_route_id_foreign` FOREIGN KEY (`route_id`) REFERENCES `routes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicles`
--

LOCK TABLES `vehicles` WRITE;
/*!40000 ALTER TABLE `vehicles` DISABLE KEYS */;
/*!40000 ALTER TABLE `vehicles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `withholding_taxes`
--

DROP TABLE IF EXISTS `withholding_taxes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `withholding_taxes` (
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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `withholding_taxes`
--

LOCK TABLES `withholding_taxes` WRITE;
/*!40000 ALTER TABLE `withholding_taxes` DISABLE KEYS */;
INSERT INTO `withholding_taxes` VALUES (1,'Daily - Bracket 1',0.00,685.00,0.00,0.00,'Daily','2026-09-03 06:01:03','2026-09-03 06:01:03'),(2,'Daily - Bracket 2',685.01,1095.00,0.00,15.00,'Daily (₱0 + 15% over ₱685)','2026-09-03 06:01:03','2026-09-03 06:01:03'),(3,'Daily - Bracket 3',1096.00,2191.00,61.65,20.00,'Daily (₱61.65 + 20% over ₱1,096)','2026-09-03 06:01:03','2026-09-03 06:01:03'),(4,'Daily - Bracket 4',2192.00,5478.00,280.85,25.00,'Daily (₱280.85 + 25% over ₱2,192)','2026-09-03 06:01:03','2026-09-03 06:01:03'),(5,'Daily - Bracket 5',5479.00,21917.00,1102.60,30.00,'Daily (₱1,102.60 + 30% over ₱5,479)','2026-09-03 06:01:03','2026-09-03 06:01:03'),(6,'Daily - Bracket 6',21918.00,999999.00,6036.30,35.00,'Daily (₱6,036.30 + 35% over ₱21,918)','2026-09-03 06:01:03','2026-09-03 06:01:03'),(7,'Weekly - Bracket 1',0.00,4808.00,0.00,0.00,'Weekly','2026-09-03 06:01:03','2026-09-03 06:01:03'),(8,'Weekly - Bracket 2',4808.01,7691.00,0.00,15.00,'Weekly (₱0 + 15% over ₱4,808)','2026-09-03 06:01:03','2026-09-03 06:01:03'),(9,'Weekly - Bracket 3',7692.00,15384.00,432.60,20.00,'Weekly (₱432.60 + 20% over ₱7,692)','2026-09-03 06:01:03','2026-09-03 06:01:03'),(10,'Weekly - Bracket 4',15385.00,39461.00,1971.20,25.00,'Weekly (₱1,971.20 + 25% over ₱15,385)','2026-09-03 06:01:03','2026-09-03 06:01:03'),(11,'Weekly - Bracket 5',39462.00,153845.00,7760.45,30.00,'Weekly (₱7,760.45 + 30% over ₱39,462)','2026-09-03 06:01:03','2026-09-03 06:01:03'),(12,'Weekly - Bracket 6',153846.00,999999.00,42355.65,35.00,'Weekly (₱42,355.65 + 35% over ₱153,846)','2026-09-03 06:01:03','2026-09-03 06:01:03'),(13,'Semi-monthly - Bracket 1',0.00,10417.00,0.00,0.00,'Semi-monthly','2026-09-03 06:01:03','2026-09-03 06:01:03'),(14,'Semi-monthly - Bracket 2',10417.01,16666.00,0.00,15.00,'Semi-monthly (₱0 + 15% over ₱10,417)','2026-09-03 06:01:03','2026-09-03 06:01:03'),(15,'Semi-monthly - Bracket 3',16667.00,33332.00,937.50,20.00,'Semi-monthly (₱937.50 + 20% over ₱16,667)','2026-09-03 06:01:03','2026-09-03 06:01:03'),(16,'Semi-monthly - Bracket 4',33333.00,83332.00,4270.70,25.00,'Semi-monthly (₱4,270.70 + 25% over ₱33,333)','2026-09-03 06:01:03','2026-09-03 06:01:03'),(17,'Semi-monthly - Bracket 5',83333.00,333332.00,16770.70,30.00,'Semi-monthly (₱16,770.70 + 30% over ₱83,333)','2026-09-03 06:01:03','2026-09-03 06:01:03'),(18,'Semi-monthly - Bracket 6',333333.00,999999.00,91770.70,35.00,'Semi-monthly (₱91,770.70 + 35% over ₱333,333)','2026-09-03 06:01:03','2026-09-03 06:01:03'),(19,'Monthly - Bracket 1',0.00,20833.00,0.00,0.00,'Monthly','2026-09-03 06:01:03','2026-09-03 06:01:03'),(20,'Monthly - Bracket 2',20833.01,33332.00,0.00,15.00,'Monthly (₱0 + 15% over ₱20,833)','2026-09-03 06:01:04','2026-09-03 06:01:04'),(21,'Monthly - Bracket 3',33333.00,66666.00,1875.00,20.00,'Monthly (₱1,875.00 + 20% over ₱33,333)','2026-09-03 06:01:04','2026-09-03 06:01:04'),(22,'Monthly - Bracket 4',66667.00,166666.00,8541.80,25.00,'Monthly (₱8,541.80 + 25% over ₱66,667)','2026-09-03 06:01:04','2026-09-03 06:01:04'),(23,'Monthly - Bracket 5',166667.00,666666.00,33541.80,30.00,'Monthly (₱33,541.80 + 30% over ₱166,667)','2026-09-03 06:01:04','2026-09-03 06:01:04'),(24,'Monthly - Bracket 6',666667.00,999999.00,183541.80,35.00,'Monthly (₱183,541.80 + 35% over ₱666,667)','2026-09-03 06:01:04','2026-09-03 06:01:04');
/*!40000 ALTER TABLE `withholding_taxes` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed

-- ===========================================
-- Foreign key constraints
-- ===========================================


SET FOREIGN_KEY_CHECKS = 1;

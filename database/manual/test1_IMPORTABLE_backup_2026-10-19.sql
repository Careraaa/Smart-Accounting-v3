SET FOREIGN_KEY_CHECKS=0;
-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql104.infinityfree.com
-- Generation Time: Oct 19, 2026 at 12:33 PM
-- Server version: 11.4.13-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_42738871_smart_accounting_v3_test`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounting_audit_logs`
--

CREATE TABLE `accounting_audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `action` varchar(50) NOT NULL,
  `module` varchar(50) NOT NULL,
  `record_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `account_mappings`
--

CREATE TABLE `account_mappings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `mapping_key` varchar(50) NOT NULL,
  `label` varchar(255) NOT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `allowances`
--

CREATE TABLE `allowances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `payroll_id` bigint(20) UNSIGNED DEFAULT NULL,
  `allowance_type` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `effective_date` date DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `status` enum('present','late','absent','on leave') DEFAULT NULL,
  `is_manual` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `user_id`, `date`, `time_in`, `time_out`, `status`, `is_manual`, `created_at`, `updated_at`) VALUES
(1, 7, '2026-09-10', '10:09:59', '10:34:18', 'present', 0, '2026-09-10 17:09:59', '2026-09-10 17:34:18'),
(2, 8, '2026-09-10', '10:22:27', '14:14:40', 'present', 0, '2026-09-10 17:22:27', '2026-09-10 21:14:40'),
(3, 9, '2026-09-10', '13:52:21', NULL, 'present', 0, '2026-09-10 20:52:21', '2026-09-10 20:52:21'),
(4, 8, '2026-09-17', '12:20:20', NULL, 'present', 0, '2026-09-17 19:20:20', '2026-09-17 19:20:20'),
(5, 3, '2026-09-01', '08:00:00', '17:00:00', 'present', 1, '2026-09-18 19:03:36', '2026-09-18 19:03:36'),
(6, 10, '2026-09-17', '10:00:00', '23:55:00', 'present', 1, '2026-09-18 19:25:18', '2026-09-29 00:20:51'),
(7, 10, '2026-09-01', '10:15:00', '22:10:00', 'present', 1, '2026-09-28 23:09:02', '2026-09-28 23:09:02'),
(8, 10, '2026-09-02', '13:55:00', '23:30:00', 'late', 1, '2026-09-28 23:09:46', '2026-09-28 23:09:46'),
(9, 10, '2026-09-03', '13:15:00', '12:30:00', 'late', 1, '2026-09-28 23:10:44', '2026-09-28 23:10:44'),
(10, 10, '2026-09-04', '09:15:00', '22:45:00', 'present', 1, '2026-09-28 23:13:51', '2026-09-28 23:13:51'),
(11, 10, '2026-09-05', '14:35:00', '22:45:00', 'late', 1, '2026-09-28 23:16:29', '2026-09-28 23:16:29'),
(12, 10, '2026-09-06', '09:05:00', '19:00:00', 'present', 1, '2026-09-28 23:17:11', '2026-09-28 23:17:11'),
(13, 10, '2026-09-07', '15:01:00', '00:45:00', 'late', 1, '2026-09-28 23:18:04', '2026-09-28 23:18:04'),
(14, 10, '2026-09-08', '13:55:00', '00:40:00', 'late', 1, '2026-09-28 23:19:38', '2026-09-28 23:19:38'),
(15, 10, '2026-09-09', '13:00:00', '00:45:00', 'present', 1, '2026-09-28 23:21:00', '2026-09-28 23:21:00'),
(16, 10, '2026-09-10', '04:35:00', '00:55:00', 'present', 1, '2026-09-28 23:26:19', '2026-09-28 23:26:19'),
(17, 10, '2026-09-18', '11:45:00', '22:55:00', 'present', 1, '2026-09-29 00:21:41', '2026-09-29 00:21:41'),
(18, 10, '2026-09-21', '13:00:00', '23:55:00', 'present', 1, '2026-09-29 00:22:33', '2026-09-29 00:22:33'),
(19, 10, '2026-09-23', '08:05:00', '23:45:00', 'present', 1, '2026-09-29 00:23:11', '2026-09-29 00:23:11'),
(20, 10, '2026-08-18', '13:01:00', '23:55:00', 'present', 1, '2026-09-29 00:29:52', '2026-09-29 00:29:52'),
(21, 10, '2026-08-20', '08:01:00', '23:45:00', 'present', 1, '2026-09-29 00:30:28', '2026-09-29 00:30:28'),
(22, 10, '2026-08-27', '11:00:00', '23:59:00', 'present', 1, '2026-09-29 00:31:06', '2026-09-29 00:31:06'),
(23, 3, '2026-09-16', '08:00:00', '18:00:00', 'present', 1, '2026-10-01 23:32:30', '2026-10-01 23:32:30'),
(24, 3, '2026-09-18', '08:00:00', '17:45:00', 'present', 1, '2026-10-01 23:33:18', '2026-10-01 23:33:18'),
(25, 3, '2026-10-19', '09:15:00', '18:45:00', 'late', 1, '2026-10-01 23:33:52', '2026-10-01 23:33:52'),
(26, 3, '2026-09-30', '07:49:00', '19:49:00', 'present', 1, '2026-10-01 23:34:44', '2026-10-01 23:34:44');

-- --------------------------------------------------------

--
-- Table structure for table `attendance_logs`
--

CREATE TABLE `attendance_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('time_in','time_out') NOT NULL,
  `logged_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendance_logs`
--

INSERT INTO `attendance_logs` (`id`, `user_id`, `type`, `logged_at`, `created_at`, `updated_at`) VALUES
(1, 7, 'time_in', '2026-09-10 10:09:59', '2026-09-10 17:09:59', '2026-09-10 17:09:59'),
(2, 8, 'time_in', '2026-09-10 10:22:27', '2026-09-10 17:22:27', '2026-09-10 17:22:27'),
(3, 8, 'time_out', '2026-09-10 10:22:54', '2026-09-10 17:22:54', '2026-09-10 17:22:54'),
(4, 7, 'time_out', '2026-09-10 10:34:18', '2026-09-10 17:34:18', '2026-09-10 17:34:18'),
(5, 9, 'time_in', '2026-09-10 13:52:21', '2026-09-10 20:52:21', '2026-09-10 20:52:21'),
(6, 8, 'time_out', '2026-09-10 14:14:40', '2026-09-10 21:14:40', '2026-09-10 21:14:40'),
(7, 8, 'time_in', '2026-09-17 12:20:20', '2026-09-17 19:20:20', '2026-09-17 19:20:20');

-- --------------------------------------------------------

--
-- Table structure for table `attendance_tokens`
--

CREATE TABLE `attendance_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `token` varchar(255) NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendance_tokens`
--

INSERT INTO `attendance_tokens` (`id`, `token`, `used`, `user_id`, `expires_at`, `created_at`, `updated_at`) VALUES
(50, 'YS4UU5NT', 0, NULL, '2026-10-15 03:50:28', '2026-10-15 03:49:28', '2026-10-15 03:49:28');

-- --------------------------------------------------------

--
-- Table structure for table `bonuses`
--

CREATE TABLE `bonuses` (
  `id` bigint(20) UNSIGNED NOT NULL,
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
  `year` smallint(5) UNSIGNED DEFAULT NULL,
  `payroll_period` varchar(255) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bonus_employee`
--

CREATE TABLE `bonus_employee` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bonus_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `boundary_payments`
--

CREATE TABLE `boundary_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `daily_remittance_id` bigint(20) UNSIGNED NOT NULL,
  `source_payment_key` varchar(64) NOT NULL,
  `initial_payment_remittance_id` bigint(20) UNSIGNED DEFAULT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `paid_by` varchar(16) DEFAULT NULL,
  `payment_kind` varchar(16) NOT NULL,
  `status` varchar(16) NOT NULL DEFAULT 'Posted',
  `recorded_by` bigint(20) UNSIGNED NOT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `journal_entry_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('knights-transport-services-corporation-smart-accounting-cache-notif.1', 'a:2:{s:5:\"count\";i:0;s:6:\"recent\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}', 1788415318),
('smart-accounting-cache-current_qr_token', 's:8:\"YS4UU5NT\";', 1791982233),
('smart-accounting-cache-mary grace.madurioa|103.249.203.124', 'i:2;', 1789005875),
('smart-accounting-cache-mary grace.madurioa|103.249.203.124:timer', 'i:1789005875;', 1789005875),
('smart-accounting-cache-marygrace.madurioa|103.249.203.124', 'i:2;', 1789005966),
('smart-accounting-cache-marygrace.madurioa|103.249.203.124:timer', 'i:1789005966;', 1789005966),
('smart-accounting-cache-mel|103.249.203.124', 'i:1;', 1789701682),
('smart-accounting-cache-mel|103.249.203.124:timer', 'i:1789701682;', 1789701682),
('smart-accounting-cache-notif.1', 'a:2:{s:5:\"count\";i:0;s:6:\"recent\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}', 1792425338),
('smart-accounting-cache-notif.10', 'a:2:{s:5:\"count\";i:0;s:6:\"recent\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}', 1789020765),
('smart-accounting-cache-notif.3', 'a:2:{s:5:\"count\";i:13;s:6:\"recent\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:3:{i:0;O:23:\"App\\Models\\Notification\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:13:\"notifications\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:10:{s:2:\"id\";i:112;s:7:\"user_id\";i:3;s:4:\"type\";s:17:\"overtime_approved\";s:5:\"title\";s:24:\"Overtime Record Approved\";s:7:\"message\";s:69:\"Your Overtime record for Sep 16, 2026 (1.00 hours) has been approved.\";s:4:\"data\";s:54:\"{\"record_id\":17,\"type\":\"overtime\",\"status\":\"approved\"}\";s:7:\"read_at\";N;s:10:\"deleted_at\";N;s:10:\"created_at\";s:19:\"2026-10-01 16:35:31\";s:10:\"updated_at\";s:19:\"2026-10-01 16:35:31\";}s:11:\"\0*\0original\";a:10:{s:2:\"id\";i:112;s:7:\"user_id\";i:3;s:4:\"type\";s:17:\"overtime_approved\";s:5:\"title\";s:24:\"Overtime Record Approved\";s:7:\"message\";s:69:\"Your Overtime record for Sep 16, 2026 (1.00 hours) has been approved.\";s:4:\"data\";s:54:\"{\"record_id\":17,\"type\":\"overtime\",\"status\":\"approved\"}\";s:7:\"read_at\";N;s:10:\"deleted_at\";N;s:10:\"created_at\";s:19:\"2026-10-01 16:35:31\";s:10:\"updated_at\";s:19:\"2026-10-01 16:35:31\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:3:{s:4:\"data\";s:5:\"array\";s:7:\"read_at\";s:8:\"datetime\";s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:7:\"user_id\";i:1;s:4:\"type\";i:2;s:5:\"title\";i:3;s:7:\"message\";i:4;s:4:\"data\";i:5;s:7:\"read_at\";i:6;s:10:\"deleted_at\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:1;O:23:\"App\\Models\\Notification\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:13:\"notifications\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:10:{s:2:\"id\";i:111;s:7:\"user_id\";i:3;s:4:\"type\";s:17:\"overtime_approved\";s:5:\"title\";s:24:\"Overtime Record Approved\";s:7:\"message\";s:69:\"Your Overtime record for Sep 18, 2026 (1.00 hours) has been approved.\";s:4:\"data\";s:54:\"{\"record_id\":18,\"type\":\"overtime\",\"status\":\"approved\"}\";s:7:\"read_at\";N;s:10:\"deleted_at\";N;s:10:\"created_at\";s:19:\"2026-10-01 16:35:25\";s:10:\"updated_at\";s:19:\"2026-10-01 16:35:25\";}s:11:\"\0*\0original\";a:10:{s:2:\"id\";i:111;s:7:\"user_id\";i:3;s:4:\"type\";s:17:\"overtime_approved\";s:5:\"title\";s:24:\"Overtime Record Approved\";s:7:\"message\";s:69:\"Your Overtime record for Sep 18, 2026 (1.00 hours) has been approved.\";s:4:\"data\";s:54:\"{\"record_id\":18,\"type\":\"overtime\",\"status\":\"approved\"}\";s:7:\"read_at\";N;s:10:\"deleted_at\";N;s:10:\"created_at\";s:19:\"2026-10-01 16:35:25\";s:10:\"updated_at\";s:19:\"2026-10-01 16:35:25\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:3:{s:4:\"data\";s:5:\"array\";s:7:\"read_at\";s:8:\"datetime\";s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:7:\"user_id\";i:1;s:4:\"type\";i:2;s:5:\"title\";i:3;s:7:\"message\";i:4;s:4:\"data\";i:5;s:7:\"read_at\";i:6;s:10:\"deleted_at\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:2;O:23:\"App\\Models\\Notification\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:13:\"notifications\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:10:{s:2:\"id\";i:110;s:7:\"user_id\";i:3;s:4:\"type\";s:17:\"overtime_approved\";s:5:\"title\";s:24:\"Overtime Record Approved\";s:7:\"message\";s:69:\"Your Overtime record for Oct 19, 2026 (2.00 hours) has been approved.\";s:4:\"data\";s:54:\"{\"record_id\":19,\"type\":\"overtime\",\"status\":\"approved\"}\";s:7:\"read_at\";N;s:10:\"deleted_at\";N;s:10:\"created_at\";s:19:\"2026-10-01 16:35:15\";s:10:\"updated_at\";s:19:\"2026-10-01 16:35:15\";}s:11:\"\0*\0original\";a:10:{s:2:\"id\";i:110;s:7:\"user_id\";i:3;s:4:\"type\";s:17:\"overtime_approved\";s:5:\"title\";s:24:\"Overtime Record Approved\";s:7:\"message\";s:69:\"Your Overtime record for Oct 19, 2026 (2.00 hours) has been approved.\";s:4:\"data\";s:54:\"{\"record_id\":19,\"type\":\"overtime\",\"status\":\"approved\"}\";s:7:\"read_at\";N;s:10:\"deleted_at\";N;s:10:\"created_at\";s:19:\"2026-10-01 16:35:15\";s:10:\"updated_at\";s:19:\"2026-10-01 16:35:15\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:3:{s:4:\"data\";s:5:\"array\";s:7:\"read_at\";s:8:\"datetime\";s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:7:\"user_id\";i:1;s:4:\"type\";i:2;s:5:\"title\";i:3;s:7:\"message\";i:4;s:4:\"data\";i:5;s:7:\"read_at\";i:6;s:10:\"deleted_at\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}', 1790844102),
('smart-accounting-cache-notif.4', 'a:2:{s:5:\"count\";i:37;s:6:\"recent\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:3:{i:0;O:23:\"App\\Models\\Notification\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:13:\"notifications\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:10:{s:2:\"id\";i:100;s:7:\"user_id\";i:4;s:4:\"type\";s:18:\"remittance_created\";s:5:\"title\";s:22:\"New Remittance Created\";s:7:\"message\";s:82:\"Rolando Bernal created a new remittance for Sep 30, 2026 amounting to ₱3,300.00.\";s:4:\"data\";s:256:\"{\"remittance_id\":38,\"driver_id\":\"21\",\"driver_name\":\"Rolando Bernal\",\"total_collection\":\"6738.00\",\"total_expenses\":\"3438.00\",\"net_remittance\":\"3300.00\",\"pao_name\":\"Eloisa Meterio\",\"vehicle_name\":\"NKI5317\",\"remittance_date\":\"Sep 30, 2026\",\"status\":\"pending\"}\";s:7:\"read_at\";N;s:10:\"deleted_at\";N;s:10:\"created_at\";s:19:\"2026-10-01 12:11:03\";s:10:\"updated_at\";s:19:\"2026-10-01 12:11:03\";}s:11:\"\0*\0original\";a:10:{s:2:\"id\";i:100;s:7:\"user_id\";i:4;s:4:\"type\";s:18:\"remittance_created\";s:5:\"title\";s:22:\"New Remittance Created\";s:7:\"message\";s:82:\"Rolando Bernal created a new remittance for Sep 30, 2026 amounting to ₱3,300.00.\";s:4:\"data\";s:256:\"{\"remittance_id\":38,\"driver_id\":\"21\",\"driver_name\":\"Rolando Bernal\",\"total_collection\":\"6738.00\",\"total_expenses\":\"3438.00\",\"net_remittance\":\"3300.00\",\"pao_name\":\"Eloisa Meterio\",\"vehicle_name\":\"NKI5317\",\"remittance_date\":\"Sep 30, 2026\",\"status\":\"pending\"}\";s:7:\"read_at\";N;s:10:\"deleted_at\";N;s:10:\"created_at\";s:19:\"2026-10-01 12:11:03\";s:10:\"updated_at\";s:19:\"2026-10-01 12:11:03\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:3:{s:4:\"data\";s:5:\"array\";s:7:\"read_at\";s:8:\"datetime\";s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:7:\"user_id\";i:1;s:4:\"type\";i:2;s:5:\"title\";i:3;s:7:\"message\";i:4;s:4:\"data\";i:5;s:7:\"read_at\";i:6;s:10:\"deleted_at\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:1;O:23:\"App\\Models\\Notification\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:13:\"notifications\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:10:{s:2:\"id\";i:99;s:7:\"user_id\";i:4;s:4:\"type\";s:18:\"remittance_created\";s:5:\"title\";s:22:\"New Remittance Created\";s:7:\"message\";s:83:\"Kyowanie Martin created a new remittance for Sep 30, 2026 amounting to ₱3,100.00.\";s:4:\"data\";s:260:\"{\"remittance_id\":37,\"driver_id\":\"20\",\"driver_name\":\"Kyowanie Martin\",\"total_collection\":\"7110.00\",\"total_expenses\":\"4723.00\",\"net_remittance\":\"3100.00\",\"pao_name\":\"Jorick Batiancila\",\"vehicle_name\":\"NKI5264\",\"remittance_date\":\"Sep 30, 2026\",\"status\":\"pending\"}\";s:7:\"read_at\";N;s:10:\"deleted_at\";N;s:10:\"created_at\";s:19:\"2026-10-01 12:09:31\";s:10:\"updated_at\";s:19:\"2026-10-01 12:09:31\";}s:11:\"\0*\0original\";a:10:{s:2:\"id\";i:99;s:7:\"user_id\";i:4;s:4:\"type\";s:18:\"remittance_created\";s:5:\"title\";s:22:\"New Remittance Created\";s:7:\"message\";s:83:\"Kyowanie Martin created a new remittance for Sep 30, 2026 amounting to ₱3,100.00.\";s:4:\"data\";s:260:\"{\"remittance_id\":37,\"driver_id\":\"20\",\"driver_name\":\"Kyowanie Martin\",\"total_collection\":\"7110.00\",\"total_expenses\":\"4723.00\",\"net_remittance\":\"3100.00\",\"pao_name\":\"Jorick Batiancila\",\"vehicle_name\":\"NKI5264\",\"remittance_date\":\"Sep 30, 2026\",\"status\":\"pending\"}\";s:7:\"read_at\";N;s:10:\"deleted_at\";N;s:10:\"created_at\";s:19:\"2026-10-01 12:09:31\";s:10:\"updated_at\";s:19:\"2026-10-01 12:09:31\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:3:{s:4:\"data\";s:5:\"array\";s:7:\"read_at\";s:8:\"datetime\";s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:7:\"user_id\";i:1;s:4:\"type\";i:2;s:5:\"title\";i:3;s:7:\"message\";i:4;s:4:\"data\";i:5;s:7:\"read_at\";i:6;s:10:\"deleted_at\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:2;O:23:\"App\\Models\\Notification\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:13:\"notifications\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:10:{s:2:\"id\";i:98;s:7:\"user_id\";i:4;s:4:\"type\";s:18:\"remittance_created\";s:5:\"title\";s:22:\"New Remittance Created\";s:7:\"message\";s:81:\"Romnick Viana created a new remittance for Sep 30, 2026 amounting to ₱1,600.00.\";s:4:\"data\";s:252:\"{\"remittance_id\":36,\"driver_id\":\"19\",\"driver_name\":\"Romnick Viana\",\"total_collection\":\"4563.00\",\"total_expenses\":\"2880.00\",\"net_remittance\":\"1600.00\",\"pao_name\":\"Lenie Nunez\",\"vehicle_name\":\"NKI5305\",\"remittance_date\":\"Sep 30, 2026\",\"status\":\"pending\"}\";s:7:\"read_at\";N;s:10:\"deleted_at\";N;s:10:\"created_at\";s:19:\"2026-10-01 12:08:24\";s:10:\"updated_at\";s:19:\"2026-10-01 12:08:24\";}s:11:\"\0*\0original\";a:10:{s:2:\"id\";i:98;s:7:\"user_id\";i:4;s:4:\"type\";s:18:\"remittance_created\";s:5:\"title\";s:22:\"New Remittance Created\";s:7:\"message\";s:81:\"Romnick Viana created a new remittance for Sep 30, 2026 amounting to ₱1,600.00.\";s:4:\"data\";s:252:\"{\"remittance_id\":36,\"driver_id\":\"19\",\"driver_name\":\"Romnick Viana\",\"total_collection\":\"4563.00\",\"total_expenses\":\"2880.00\",\"net_remittance\":\"1600.00\",\"pao_name\":\"Lenie Nunez\",\"vehicle_name\":\"NKI5305\",\"remittance_date\":\"Sep 30, 2026\",\"status\":\"pending\"}\";s:7:\"read_at\";N;s:10:\"deleted_at\";N;s:10:\"created_at\";s:19:\"2026-10-01 12:08:24\";s:10:\"updated_at\";s:19:\"2026-10-01 12:08:24\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:3:{s:4:\"data\";s:5:\"array\";s:7:\"read_at\";s:8:\"datetime\";s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:7:\"user_id\";i:1;s:4:\"type\";i:2;s:5:\"title\";i:3;s:7:\"message\";i:4;s:4:\"data\";i:5;s:7:\"read_at\";i:6;s:10:\"deleted_at\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}', 1790843222),
('smart-accounting-cache-notif.6', 'a:2:{s:5:\"count\";i:0;s:6:\"recent\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}', 1788424460);
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('smart-accounting-cache-notif.7', 'a:2:{s:5:\"count\";i:3;s:6:\"recent\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:3:{i:0;O:23:\"App\\Models\\Notification\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:13:\"notifications\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:10:{s:2:\"id\";i:6;s:7:\"user_id\";i:7;s:4:\"type\";s:18:\"overtime_submitted\";s:5:\"title\";s:26:\"Undertime Record Submitted\";s:7:\"message\";s:71:\"Your Undertime record for Sep 10, 2026 (5.50 hours) has been submitted.\";s:4:\"data\";s:86:\"{\"record_id\":2,\"type\":\"undertime\",\"hours\":\"5.50\",\"date\":\"2026-09-09T16:00:00.000000Z\"}\";s:7:\"read_at\";N;s:10:\"deleted_at\";N;s:10:\"created_at\";s:19:\"2026-09-10 10:34:18\";s:10:\"updated_at\";s:19:\"2026-09-10 10:34:18\";}s:11:\"\0*\0original\";a:10:{s:2:\"id\";i:6;s:7:\"user_id\";i:7;s:4:\"type\";s:18:\"overtime_submitted\";s:5:\"title\";s:26:\"Undertime Record Submitted\";s:7:\"message\";s:71:\"Your Undertime record for Sep 10, 2026 (5.50 hours) has been submitted.\";s:4:\"data\";s:86:\"{\"record_id\":2,\"type\":\"undertime\",\"hours\":\"5.50\",\"date\":\"2026-09-09T16:00:00.000000Z\"}\";s:7:\"read_at\";N;s:10:\"deleted_at\";N;s:10:\"created_at\";s:19:\"2026-09-10 10:34:18\";s:10:\"updated_at\";s:19:\"2026-09-10 10:34:18\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:3:{s:4:\"data\";s:5:\"array\";s:7:\"read_at\";s:8:\"datetime\";s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:7:\"user_id\";i:1;s:4:\"type\";i:2;s:5:\"title\";i:3;s:7:\"message\";i:4;s:4:\"data\";i:5;s:7:\"read_at\";i:6;s:10:\"deleted_at\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:1;O:23:\"App\\Models\\Notification\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:13:\"notifications\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:10:{s:2:\"id\";i:5;s:7:\"user_id\";i:7;s:4:\"type\";s:19:\"attendance_recorded\";s:5:\"title\";s:17:\"Time Out Recorded\";s:7:\"message\";s:60:\"Your Time Out has been recorded for Sep 10, 2026 at 10:34 AM\";s:4:\"data\";s:72:\"{\"employee_id\":7,\"type\":\"time_out\",\"date\":\"2026-09-09T16:00:00.000000Z\"}\";s:7:\"read_at\";N;s:10:\"deleted_at\";N;s:10:\"created_at\";s:19:\"2026-09-10 10:34:18\";s:10:\"updated_at\";s:19:\"2026-09-10 10:34:18\";}s:11:\"\0*\0original\";a:10:{s:2:\"id\";i:5;s:7:\"user_id\";i:7;s:4:\"type\";s:19:\"attendance_recorded\";s:5:\"title\";s:17:\"Time Out Recorded\";s:7:\"message\";s:60:\"Your Time Out has been recorded for Sep 10, 2026 at 10:34 AM\";s:4:\"data\";s:72:\"{\"employee_id\":7,\"type\":\"time_out\",\"date\":\"2026-09-09T16:00:00.000000Z\"}\";s:7:\"read_at\";N;s:10:\"deleted_at\";N;s:10:\"created_at\";s:19:\"2026-09-10 10:34:18\";s:10:\"updated_at\";s:19:\"2026-09-10 10:34:18\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:3:{s:4:\"data\";s:5:\"array\";s:7:\"read_at\";s:8:\"datetime\";s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:7:\"user_id\";i:1;s:4:\"type\";i:2;s:5:\"title\";i:3;s:7:\"message\";i:4;s:4:\"data\";i:5;s:7:\"read_at\";i:6;s:10:\"deleted_at\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:2;O:23:\"App\\Models\\Notification\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:13:\"notifications\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:10:{s:2:\"id\";i:1;s:7:\"user_id\";i:7;s:4:\"type\";s:19:\"attendance_recorded\";s:5:\"title\";s:16:\"Time In Recorded\";s:7:\"message\";s:59:\"Your Time In has been recorded for Sep 10, 2026 at 10:09 AM\";s:4:\"data\";s:71:\"{\"employee_id\":7,\"type\":\"time_in\",\"date\":\"2026-09-09T16:00:00.000000Z\"}\";s:7:\"read_at\";N;s:10:\"deleted_at\";N;s:10:\"created_at\";s:19:\"2026-09-10 10:09:59\";s:10:\"updated_at\";s:19:\"2026-09-10 10:09:59\";}s:11:\"\0*\0original\";a:10:{s:2:\"id\";i:1;s:7:\"user_id\";i:7;s:4:\"type\";s:19:\"attendance_recorded\";s:5:\"title\";s:16:\"Time In Recorded\";s:7:\"message\";s:59:\"Your Time In has been recorded for Sep 10, 2026 at 10:09 AM\";s:4:\"data\";s:71:\"{\"employee_id\":7,\"type\":\"time_in\",\"date\":\"2026-09-09T16:00:00.000000Z\"}\";s:7:\"read_at\";N;s:10:\"deleted_at\";N;s:10:\"created_at\";s:19:\"2026-09-10 10:09:59\";s:10:\"updated_at\";s:19:\"2026-09-10 10:09:59\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:3:{s:4:\"data\";s:5:\"array\";s:7:\"read_at\";s:8:\"datetime\";s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:7:\"user_id\";i:1;s:4:\"type\";i:2;s:5:\"title\";i:3;s:7:\"message\";i:4;s:4:\"data\";i:5;s:7:\"read_at\";i:6;s:10:\"deleted_at\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}', 1789007892),
('smart-accounting-cache-notif.8', 'a:2:{s:5:\"count\";i:5;s:6:\"recent\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:3:{i:0;O:23:\"App\\Models\\Notification\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:13:\"notifications\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:10:{s:2:\"id\";i:9;s:7:\"user_id\";i:8;s:4:\"type\";s:18:\"overtime_submitted\";s:5:\"title\";s:26:\"Undertime Record Submitted\";s:7:\"message\";s:71:\"Your Undertime record for Sep 10, 2026 (3.00 hours) has been submitted.\";s:4:\"data\";s:86:\"{\"record_id\":3,\"type\":\"undertime\",\"hours\":\"3.00\",\"date\":\"2026-09-09T16:00:00.000000Z\"}\";s:7:\"read_at\";N;s:10:\"deleted_at\";N;s:10:\"created_at\";s:19:\"2026-09-10 14:14:40\";s:10:\"updated_at\";s:19:\"2026-09-10 14:14:40\";}s:11:\"\0*\0original\";a:10:{s:2:\"id\";i:9;s:7:\"user_id\";i:8;s:4:\"type\";s:18:\"overtime_submitted\";s:5:\"title\";s:26:\"Undertime Record Submitted\";s:7:\"message\";s:71:\"Your Undertime record for Sep 10, 2026 (3.00 hours) has been submitted.\";s:4:\"data\";s:86:\"{\"record_id\":3,\"type\":\"undertime\",\"hours\":\"3.00\",\"date\":\"2026-09-09T16:00:00.000000Z\"}\";s:7:\"read_at\";N;s:10:\"deleted_at\";N;s:10:\"created_at\";s:19:\"2026-09-10 14:14:40\";s:10:\"updated_at\";s:19:\"2026-09-10 14:14:40\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:3:{s:4:\"data\";s:5:\"array\";s:7:\"read_at\";s:8:\"datetime\";s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:7:\"user_id\";i:1;s:4:\"type\";i:2;s:5:\"title\";i:3;s:7:\"message\";i:4;s:4:\"data\";i:5;s:7:\"read_at\";i:6;s:10:\"deleted_at\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:1;O:23:\"App\\Models\\Notification\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:13:\"notifications\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:10:{s:2:\"id\";i:8;s:7:\"user_id\";i:8;s:4:\"type\";s:19:\"attendance_recorded\";s:5:\"title\";s:17:\"Time Out Recorded\";s:7:\"message\";s:60:\"Your Time Out has been recorded for Sep 10, 2026 at 14:14 PM\";s:4:\"data\";s:72:\"{\"employee_id\":8,\"type\":\"time_out\",\"date\":\"2026-09-09T16:00:00.000000Z\"}\";s:7:\"read_at\";N;s:10:\"deleted_at\";N;s:10:\"created_at\";s:19:\"2026-09-10 14:14:40\";s:10:\"updated_at\";s:19:\"2026-09-10 14:14:40\";}s:11:\"\0*\0original\";a:10:{s:2:\"id\";i:8;s:7:\"user_id\";i:8;s:4:\"type\";s:19:\"attendance_recorded\";s:5:\"title\";s:17:\"Time Out Recorded\";s:7:\"message\";s:60:\"Your Time Out has been recorded for Sep 10, 2026 at 14:14 PM\";s:4:\"data\";s:72:\"{\"employee_id\":8,\"type\":\"time_out\",\"date\":\"2026-09-09T16:00:00.000000Z\"}\";s:7:\"read_at\";N;s:10:\"deleted_at\";N;s:10:\"created_at\";s:19:\"2026-09-10 14:14:40\";s:10:\"updated_at\";s:19:\"2026-09-10 14:14:40\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:3:{s:4:\"data\";s:5:\"array\";s:7:\"read_at\";s:8:\"datetime\";s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:7:\"user_id\";i:1;s:4:\"type\";i:2;s:5:\"title\";i:3;s:7:\"message\";i:4;s:4:\"data\";i:5;s:7:\"read_at\";i:6;s:10:\"deleted_at\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:2;O:23:\"App\\Models\\Notification\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:13:\"notifications\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:10:{s:2:\"id\";i:4;s:7:\"user_id\";i:8;s:4:\"type\";s:18:\"overtime_submitted\";s:5:\"title\";s:26:\"Undertime Record Submitted\";s:7:\"message\";s:71:\"Your Undertime record for Sep 10, 2026 (5.50 hours) has been submitted.\";s:4:\"data\";s:86:\"{\"record_id\":1,\"type\":\"undertime\",\"hours\":\"5.50\",\"date\":\"2026-09-09T16:00:00.000000Z\"}\";s:7:\"read_at\";N;s:10:\"deleted_at\";N;s:10:\"created_at\";s:19:\"2026-09-10 10:22:54\";s:10:\"updated_at\";s:19:\"2026-09-10 10:22:54\";}s:11:\"\0*\0original\";a:10:{s:2:\"id\";i:4;s:7:\"user_id\";i:8;s:4:\"type\";s:18:\"overtime_submitted\";s:5:\"title\";s:26:\"Undertime Record Submitted\";s:7:\"message\";s:71:\"Your Undertime record for Sep 10, 2026 (5.50 hours) has been submitted.\";s:4:\"data\";s:86:\"{\"record_id\":1,\"type\":\"undertime\",\"hours\":\"5.50\",\"date\":\"2026-09-09T16:00:00.000000Z\"}\";s:7:\"read_at\";N;s:10:\"deleted_at\";N;s:10:\"created_at\";s:19:\"2026-09-10 10:22:54\";s:10:\"updated_at\";s:19:\"2026-09-10 10:22:54\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:3:{s:4:\"data\";s:5:\"array\";s:7:\"read_at\";s:8:\"datetime\";s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:7:\"user_id\";i:1;s:4:\"type\";i:2;s:5:\"title\";i:3;s:7:\"message\";i:4;s:4:\"data\";i:5;s:7:\"read_at\";i:6;s:10:\"deleted_at\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}', 1789020914),
('smart-accounting-cache-notif.9', 'a:2:{s:5:\"count\";i:1;s:6:\"recent\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:1:{i:0;O:23:\"App\\Models\\Notification\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:13:\"notifications\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:10:{s:2:\"id\";i:7;s:7:\"user_id\";i:9;s:4:\"type\";s:19:\"attendance_recorded\";s:5:\"title\";s:16:\"Time In Recorded\";s:7:\"message\";s:59:\"Your Time In has been recorded for Sep 10, 2026 at 13:52 PM\";s:4:\"data\";s:71:\"{\"employee_id\":9,\"type\":\"time_in\",\"date\":\"2026-09-09T16:00:00.000000Z\"}\";s:7:\"read_at\";N;s:10:\"deleted_at\";N;s:10:\"created_at\";s:19:\"2026-09-10 13:52:21\";s:10:\"updated_at\";s:19:\"2026-09-10 13:52:21\";}s:11:\"\0*\0original\";a:10:{s:2:\"id\";i:7;s:7:\"user_id\";i:9;s:4:\"type\";s:19:\"attendance_recorded\";s:5:\"title\";s:16:\"Time In Recorded\";s:7:\"message\";s:59:\"Your Time In has been recorded for Sep 10, 2026 at 13:52 PM\";s:4:\"data\";s:71:\"{\"employee_id\":9,\"type\":\"time_in\",\"date\":\"2026-09-09T16:00:00.000000Z\"}\";s:7:\"read_at\";N;s:10:\"deleted_at\";N;s:10:\"created_at\";s:19:\"2026-09-10 13:52:21\";s:10:\"updated_at\";s:19:\"2026-09-10 13:52:21\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:3:{s:4:\"data\";s:5:\"array\";s:7:\"read_at\";s:8:\"datetime\";s:10:\"deleted_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:7:\"user_id\";i:1;s:4:\"type\";i:2;s:5:\"title\";i:3;s:7:\"message\";i:4;s:4:\"data\";i:5;s:7:\"read_at\";i:6;s:10:\"deleted_at\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}', 1789019650),
('smart-accounting-cache-ronolfo alcanse|103.249.203.124', 'i:4;', 1789020526),
('smart-accounting-cache-ronolfo alcanse|103.249.203.124:timer', 'i:1789020526;', 1789020526),
('smart-accounting-cache-su|103.249.203.124', 'i:1;', 1789618030),
('smart-accounting-cache-su|103.249.203.124:timer', 'i:1789618030;', 1789618030);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cash_advances`
--

CREATE TABLE `cash_advances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `repayment_months` int(11) NOT NULL DEFAULT 1,
  `monthly_deduction` decimal(12,2) NOT NULL DEFAULT 0.00,
  `amount_deducted` decimal(12,2) NOT NULL DEFAULT 0.00,
  `request_date` date DEFAULT NULL,
  `approval_date` date DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `deducted_payroll_id` bigint(20) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chart_of_accounts`
--

CREATE TABLE `chart_of_accounts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `account_code` varchar(20) NOT NULL,
  `account_name` varchar(255) NOT NULL,
  `account_type` enum('Asset','Liability','Equity','Revenue','Expense') NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chart_of_accounts`
--

INSERT INTO `chart_of_accounts` (`id`, `account_code`, `account_name`, `account_type`, `description`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(1, '101', 'rent expense', 'Expense', 'rental for the month of july', 1, NULL, '2026-09-03 23:19:22', '2026-09-03 23:19:22');

-- --------------------------------------------------------

--
-- Table structure for table `daily_remittances`
--

CREATE TABLE `daily_remittances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `driver_id` bigint(20) UNSIGNED NOT NULL,
  `pao_id` bigint(20) UNSIGNED NOT NULL,
  `route_id` bigint(20) UNSIGNED NOT NULL,
  `vehicle_id` bigint(20) UNSIGNED NOT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `daily_remittances`
--

INSERT INTO `daily_remittances` (`id`, `driver_id`, `pao_id`, `route_id`, `vehicle_id`, `remittance_date`, `total_collection`, `diesel`, `parking`, `dispatcher`, `food_allowance`, `barker`, `others`, `total_expenses`, `boundary`, `net_remittance`, `is_short_remittance`, `short_amount`, `driver_share`, `driver_amount_paid`, `driver_status`, `pao_share`, `pao_amount_paid`, `pao_status`, `resolution_notes`, `resolved_at`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 2, 1, '2026-09-15', '5089.00', NULL, NULL, NULL, NULL, NULL, NULL, '3356.00', '2100.00', '1733.00', 1, '367.00', '183.50', '183.50', 'paid', '183.50', '183.50', 'paid', '9/17/2026', '2026-09-17 19:24:40', 'rejected', '2026-09-17 01:14:40', '2026-09-17 19:24:40'),
(3, 10, 10, 4, 16, '2026-09-28', '5310.00', '2703.00', '50.00', '20.00', NULL, NULL, NULL, '2773.00', '3000.00', '3100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'approved', '2026-09-30 20:49:55', '2026-09-30 22:33:33'),
(4, 9, 9, 1, 7, '2026-09-28', '7077.00', '3647.00', '150.00', '80.00', NULL, NULL, NULL, '3877.00', '3200.00', '3300.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'approved', '2026-09-30 20:51:56', '2026-09-30 22:34:23'),
(5, 8, 8, 1, 8, '2026-09-28', '7077.00', '3905.00', '150.00', '80.00', NULL, NULL, NULL, '4135.00', '3300.00', '3000.00', 1, '300.00', '150.00', '0.00', 'pending', '150.00', '0.00', 'pending', NULL, NULL, 'approved', '2026-09-30 20:56:42', '2026-09-30 22:35:35'),
(6, 7, 7, 2, 5, '2026-09-28', '7427.00', '2958.00', '125.00', '80.00', NULL, NULL, NULL, '3163.00', '2100.00', '2100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'approved', '2026-09-30 20:57:57', '2026-09-30 22:36:22'),
(7, 6, 6, 4, 14, '2026-09-28', '8466.00', '4349.00', '100.00', '40.00', NULL, NULL, NULL, '4489.00', '3100.00', '3100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'approved', '2026-09-30 20:58:49', '2026-09-30 22:36:58'),
(8, 5, 5, 6, 13, '2026-09-28', '2614.00', '3480.00', NULL, NULL, NULL, NULL, NULL, '3480.00', '2100.00', '1100.00', 1, '1000.00', '500.00', '0.00', 'pending', '500.00', '0.00', 'pending', NULL, NULL, 'approved', '2026-09-30 22:18:20', '2026-09-30 22:52:15'),
(9, 4, 4, 6, 12, '2026-09-28', '3582.00', '3153.00', NULL, NULL, NULL, NULL, NULL, '3153.00', '2100.00', '2100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'approved', '2026-09-30 22:19:03', '2026-09-30 22:55:47'),
(10, 3, 3, 3, 9, '2026-09-28', '7550.00', '4000.00', '150.00', '100.00', NULL, NULL, NULL, '4250.00', '3300.00', '3300.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'approved', '2026-09-30 22:19:44', '2026-09-30 22:56:13'),
(11, 2, 2, 4, 15, '2026-09-28', '235.00', '3000.00', '50.00', '20.00', NULL, NULL, NULL, '3070.00', '3100.00', '2500.00', 1, '600.00', '300.00', '0.00', 'pending', '300.00', '0.00', 'pending', NULL, NULL, 'approved', '2026-09-30 22:20:41', '2026-09-30 22:56:46'),
(12, 1, 1, 2, 1, '2026-09-28', '5313.00', '3207.00', '125.00', '60.00', '200.00', '100.00', NULL, '3692.00', '2100.00', '2300.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'approved', '2026-09-30 22:21:34', '2026-09-30 22:57:28'),
(13, 1, 1, 7, 1, '2026-09-29', '4569.00', '2768.00', '125.00', '60.00', '200.00', '100.00', NULL, '3253.00', '1.00', '1850.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-01 00:47:34', '2026-10-01 00:47:34'),
(14, 7, 7, 2, 5, '2026-09-29', '6601.00', '2876.00', '125.00', '80.00', NULL, NULL, NULL, '3081.00', '2100.00', '2100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-01 00:49:25', '2026-10-01 00:49:25'),
(15, 11, 11, 2, 4, '2026-09-29', '6830.00', '4789.00', '150.00', '100.00', NULL, NULL, NULL, '5039.00', '2100.00', '2100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-01 18:12:03', '2026-10-01 18:12:03'),
(16, 3, 3, 3, 9, '2026-09-29', '7750.00', '4300.00', '150.00', '100.00', NULL, NULL, NULL, '4550.00', '3300.00', '3300.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-01 18:12:45', '2026-10-01 18:12:45'),
(17, 8, 8, 1, 8, '2026-09-29', '7177.00', '3747.00', '150.00', '80.00', NULL, NULL, NULL, '3977.00', '3300.00', '3300.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-01 18:13:38', '2026-10-01 18:13:38'),
(18, 12, 12, 6, 13, '2026-09-29', '9494.00', '2979.00', NULL, NULL, NULL, NULL, NULL, '2979.00', '2100.00', '2100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-01 18:16:01', '2026-10-01 18:16:01'),
(19, 2, 2, 4, 15, '2026-09-29', '1635.00', '3290.00', '50.00', NULL, NULL, NULL, NULL, '3340.00', '3100.00', '3100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-01 18:16:47', '2026-10-01 18:16:47'),
(20, 13, 13, 4, 14, '2026-09-29', '4383.00', '3509.00', '150.00', '60.00', NULL, NULL, NULL, '3719.00', '3100.00', '2100.00', 1, '1000.00', '500.00', '0.00', 'pending', '500.00', '0.00', 'pending', NULL, NULL, 'pending', '2026-10-01 18:31:34', '2026-10-01 18:31:34'),
(21, 5, 5, 6, 12, '2026-09-29', '2747.00', '3257.00', NULL, NULL, NULL, NULL, NULL, '3257.00', '2100.00', '1600.00', 1, '500.00', '250.00', '0.00', 'pending', '250.00', '0.00', 'pending', NULL, NULL, 'pending', '2026-10-01 18:32:47', '2026-10-01 18:32:47'),
(22, 14, 14, 5, 10, '2026-09-29', '4897.00', '3715.00', NULL, NULL, NULL, NULL, NULL, '3715.00', '2100.00', '1600.00', 1, '500.00', '250.00', '0.00', 'pending', '250.00', '0.00', 'pending', NULL, NULL, 'pending', '2026-10-01 18:35:13', '2026-10-01 18:35:13'),
(23, 9, 9, 1, 7, '2026-09-29', '6817.00', '3387.00', '150.00', '80.00', NULL, NULL, NULL, '3617.00', '3300.00', '3300.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-01 18:40:15', '2026-10-01 18:40:15'),
(24, 15, 15, 5, 11, '2026-09-29', '5910.00', '3247.00', NULL, NULL, NULL, NULL, NULL, '3247.00', '2100.00', '1650.00', 1, '450.00', '225.00', '0.00', 'pending', '225.00', '0.00', 'pending', NULL, NULL, 'pending', '2026-10-01 18:43:48', '2026-10-01 18:43:48'),
(25, 10, 10, 4, 16, '2026-09-29', '5133.00', '3313.00', '100.00', '20.00', NULL, NULL, NULL, '3433.00', '3100.00', '2200.00', 1, '900.00', '450.00', '0.00', 'pending', '450.00', '0.00', 'pending', NULL, NULL, 'pending', '2026-10-01 18:44:46', '2026-10-01 18:44:46'),
(26, 12, 12, 8, 18, '2026-09-30', '2145.00', '1000.00', NULL, NULL, NULL, NULL, NULL, '1000.00', '0.00', '658.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-01 18:57:58', '2026-10-01 18:57:58'),
(27, 2, 2, 4, 15, '2026-09-30', '6203.00', '3033.00', '50.00', '20.00', NULL, NULL, NULL, '3103.00', '3100.00', '3100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-01 18:59:33', '2026-10-01 18:59:33'),
(28, 4, 4, 6, 12, '2026-09-30', '5741.00', '3000.00', NULL, NULL, NULL, NULL, NULL, '3000.00', '2100.00', '2100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-01 19:00:23', '2026-10-01 19:00:23'),
(29, 14, 16, 5, 10, '2026-09-30', '4655.00', '3743.00', NULL, NULL, NULL, NULL, NULL, '3743.00', '2100.00', '1600.00', 1, '500.00', '250.00', '0.00', 'pending', '250.00', '0.00', 'pending', NULL, NULL, 'pending', '2026-10-01 19:01:55', '2026-10-01 19:01:55'),
(30, 16, 15, 5, 11, '2026-09-30', '4720.00', '3120.00', NULL, NULL, NULL, NULL, NULL, '3120.00', '2100.00', '1600.00', 1, '500.00', '250.00', '0.00', 'pending', '250.00', '0.00', 'pending', NULL, NULL, 'pending', '2026-10-01 19:02:43', '2026-10-01 19:02:43'),
(31, 1, 1, 7, 1, '2026-09-30', '5744.00', '2759.00', '125.00', '60.00', '300.00', '100.00', NULL, '3344.00', '1.00', '2400.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-01 19:03:40', '2026-10-01 19:03:40'),
(32, 6, 6, 4, 14, '2026-09-30', '6974.00', '3890.00', '50.00', '20.00', NULL, NULL, NULL, '3960.00', '3100.00', '2100.00', 1, '1000.00', '500.00', '0.00', 'pending', '500.00', '0.00', 'pending', NULL, NULL, 'pending', '2026-10-01 19:04:23', '2026-10-01 19:04:23'),
(33, 11, 11, 2, 4, '2026-09-30', '8155.00', '3338.00', '150.00', '100.00', NULL, NULL, NULL, '3588.00', '2100.00', '2100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-01 19:05:24', '2026-10-01 19:05:24'),
(34, 7, 7, 2, 5, '2026-09-30', '9150.00', '2918.00', '125.00', '80.00', NULL, NULL, NULL, '3123.00', '2100.00', '2100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-01 19:06:06', '2026-10-01 19:06:06'),
(35, 9, 9, 1, 7, '2026-09-30', '6914.00', '3384.00', '150.00', '80.00', NULL, NULL, NULL, '3614.00', '3300.00', '3300.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-01 19:06:50', '2026-10-01 19:06:50'),
(36, 19, 17, 2, 2, '2026-09-30', '4563.00', '2700.00', '100.00', '80.00', NULL, NULL, NULL, '2880.00', '2100.00', '1600.00', 1, '500.00', '250.00', '0.00', 'pending', '250.00', '0.00', 'pending', NULL, NULL, 'pending', '2026-10-01 19:08:24', '2026-10-01 19:08:24'),
(37, 20, 18, 4, 17, '2026-09-30', '7110.00', '4603.00', '100.00', '20.00', NULL, NULL, NULL, '4723.00', '3100.00', '3100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-01 19:09:31', '2026-10-01 19:09:31'),
(38, 21, 19, 3, 9, '2026-09-30', '6738.00', '3208.00', '150.00', '80.00', NULL, NULL, NULL, '3438.00', '3300.00', '3300.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-01 19:11:03', '2026-10-01 19:11:03'),
(39, 20, 18, 4, 17, '2026-10-01', '5300.00', '2516.00', '100.00', '20.00', NULL, NULL, NULL, '2636.00', '3100.00', '3100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-05 21:21:08', '2026-10-05 21:21:08'),
(40, 10, 10, 4, 16, '2026-10-01', '5858.00', '2596.00', '100.00', '20.00', NULL, NULL, NULL, '2716.00', '3100.00', '3100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-05 21:21:58', '2026-10-05 21:21:58'),
(41, 16, 15, 5, 11, '2026-10-01', '6460.00', '4000.00', NULL, NULL, NULL, NULL, NULL, '4000.00', '2100.00', '2100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-05 21:23:01', '2026-10-05 21:23:01'),
(42, 7, 7, 2, 5, '2026-10-01', '6168.00', '2920.00', '125.00', '80.00', NULL, NULL, NULL, '3125.00', '2100.00', '2100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-07 19:35:35', '2026-10-07 19:35:35'),
(43, 3, 3, 3, 9, '2026-10-01', '7622.00', '4072.00', '150.00', '100.00', NULL, NULL, NULL, '4322.00', '3300.00', '3300.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-07 19:36:40', '2026-10-07 19:36:40'),
(44, 8, 8, 1, 8, '2026-10-01', '7122.00', '3592.00', '150.00', '80.00', NULL, NULL, NULL, '3822.00', '3300.00', '3300.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-07 20:00:36', '2026-10-07 20:00:36'),
(45, 21, 20, 2, 2, '2026-10-01', '5910.00', '3440.00', '150.00', '100.00', NULL, NULL, NULL, '3690.00', '2100.00', '2100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 17:58:21', '2026-10-13 17:58:21'),
(46, 11, 11, 2, 4, '2026-10-01', '6048.00', '3509.00', '125.00', '80.00', NULL, NULL, NULL, '3714.00', '2100.00', '2100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 17:59:26', '2026-10-13 17:59:26'),
(47, 6, 21, 4, 14, '2026-10-01', '5457.00', '3772.00', '100.00', '40.00', NULL, NULL, NULL, '3912.00', '3100.00', '3100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 18:01:08', '2026-10-13 18:01:08'),
(48, 1, 1, 7, 1, '2026-10-01', '6764.00', '3229.00', '125.00', '60.00', '200.00', '100.00', NULL, '3714.00', '1.00', '3050.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 18:02:18', '2026-10-13 18:02:18'),
(49, 14, 16, 5, 10, '2026-10-01', '4560.00', '1743.00', NULL, NULL, NULL, NULL, NULL, '1743.00', '2100.00', '1800.00', 1, '300.00', '150.00', '0.00', 'pending', '150.00', '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 18:03:20', '2026-10-13 18:03:20'),
(50, 22, 5, 1, 7, '2026-10-01', '6405.00', '2900.00', '125.00', '80.00', NULL, NULL, NULL, '3105.00', '3300.00', '3300.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 18:05:40', '2026-10-13 18:05:40'),
(51, 5, 13, 4, 15, '2026-10-01', '6545.00', '3255.00', '150.00', '40.00', NULL, NULL, NULL, '3445.00', '3100.00', '3100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 18:07:13', '2026-10-13 18:07:13'),
(52, 18, 22, 2, 4, '2026-10-02', '725.00', '800.00', '25.00', NULL, NULL, NULL, NULL, '825.00', '2100.00', '0.00', 1, '2100.00', '1050.00', '0.00', 'pending', '1050.00', '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 18:44:16', '2026-10-13 18:44:16'),
(53, 9, 9, 1, 7, '2026-10-02', '7008.00', '3478.00', '150.00', '80.00', NULL, NULL, NULL, '3708.00', '3300.00', '3300.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 18:46:36', '2026-10-13 18:46:36'),
(54, 1, 1, 7, 1, '2026-10-02', '7410.00', '3375.00', '125.00', '60.00', '200.00', '100.00', NULL, '3860.00', '1.00', '3550.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 18:47:46', '2026-10-13 18:47:46'),
(55, 15, 21, 2, 2, '2026-10-02', '3110.00', '2875.00', '100.00', '60.00', NULL, NULL, NULL, '3035.00', '2100.00', '2100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 18:49:44', '2026-10-13 18:49:44'),
(56, 7, 7, 2, 5, '2026-10-02', '4163.00', '3351.00', '125.00', '80.00', NULL, NULL, NULL, '3556.00', '2100.00', '2100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 18:50:50', '2026-10-13 18:50:50'),
(57, 6, 6, 4, 14, '2026-10-02', '7265.00', '3331.00', '50.00', '20.00', NULL, NULL, NULL, '3401.00', '3100.00', '3100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 18:52:39', '2026-10-13 18:52:39'),
(58, 22, 5, 5, 11, '2026-10-02', '262.00', '2437.00', NULL, NULL, NULL, NULL, NULL, '2437.00', '2100.00', '2100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 18:54:01', '2026-10-13 18:54:01'),
(59, 8, 8, 1, 8, '2026-10-02', '6092.00', '2887.00', '125.00', '80.00', NULL, NULL, NULL, '3092.00', '3300.00', '3000.00', 1, '300.00', '150.00', '0.00', 'pending', '150.00', '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 18:56:17', '2026-10-13 18:56:17'),
(60, 10, 10, 4, 16, '2026-10-02', '5942.00', '2427.00', '100.00', '20.00', NULL, NULL, '1000.00', '3547.00', '3100.00', '3100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 18:59:59', '2026-10-13 18:59:59'),
(61, 5, 13, 4, 15, '2026-10-02', '6162.00', '3872.00', '150.00', '40.00', NULL, NULL, NULL, '4062.00', '3100.00', '2100.00', 1, '1000.00', '500.00', '0.00', 'pending', '500.00', '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 19:04:01', '2026-10-13 19:04:20'),
(62, 14, 16, 5, 10, '2026-10-02', '4296.00', '2384.00', NULL, NULL, NULL, NULL, NULL, '2384.00', '2100.00', '2100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 19:05:46', '2026-10-13 19:05:46'),
(63, 3, 3, 3, 9, '2026-10-02', '7730.00', '4200.00', '150.00', '80.00', NULL, NULL, NULL, '4430.00', '3300.00', '3300.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 19:07:22', '2026-10-13 19:07:22'),
(64, 20, 18, 4, 17, '2026-10-02', '3981.00', '3979.00', '150.00', '60.00', NULL, NULL, NULL, '4189.00', '3100.00', '3100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 19:09:59', '2026-10-13 19:09:59'),
(65, 19, 17, 6, 12, '2026-10-02', '1234.00', '3534.00', NULL, NULL, NULL, NULL, NULL, '3534.00', '2100.00', '1600.00', 1, '500.00', '250.00', '0.00', 'pending', '250.00', '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 19:11:24', '2026-10-13 19:11:24'),
(66, 11, 11, 3, 9, '2026-10-05', '7002.00', '3577.00', '125.00', NULL, NULL, NULL, NULL, '3702.00', '3300.00', '3300.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 20:23:19', '2026-10-13 20:23:19'),
(67, 5, 18, 4, 17, '2026-10-05', '3715.00', '4000.00', '50.00', NULL, NULL, NULL, '200.00', '4250.00', '3100.00', '3100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 20:26:21', '2026-10-13 20:26:21'),
(68, 14, 16, 5, 10, '2026-10-05', '3830.00', '2743.00', NULL, NULL, NULL, NULL, NULL, '2743.00', '2100.00', '1700.00', 1, '400.00', '200.00', '0.00', 'pending', '200.00', '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 20:28:19', '2026-10-13 20:28:19'),
(69, 4, 4, 6, 12, '2026-10-05', '4655.00', '3026.00', NULL, NULL, NULL, NULL, NULL, '3026.00', '2100.00', '2100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 20:30:15', '2026-10-13 20:30:15'),
(70, 15, 21, 2, 2, '2026-10-05', '5054.00', '2854.00', '100.00', NULL, NULL, NULL, NULL, '2954.00', '2100.00', '2100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 20:32:17', '2026-10-13 20:32:17'),
(71, 22, 5, 2, 6, '2026-10-05', '4526.00', '2801.00', '125.00', NULL, NULL, NULL, NULL, '2926.00', '2100.00', '1600.00', 1, '500.00', '250.00', '0.00', 'pending', '250.00', '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 20:37:54', '2026-10-13 20:37:54'),
(72, 16, 15, 5, 11, '2026-10-05', '6905.00', '3808.00', NULL, NULL, NULL, NULL, NULL, '3808.00', '2100.00', '2100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 20:38:47', '2026-10-13 20:38:47'),
(73, 10, 10, 4, 16, '2026-10-05', '6130.00', '2599.00', '100.00', NULL, NULL, NULL, NULL, '2699.00', '3100.00', '3100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 20:39:45', '2026-10-13 20:39:45'),
(74, 6, 6, 4, 14, '2026-10-05', '9929.00', '4144.00', '100.00', NULL, NULL, NULL, NULL, '4244.00', '3100.00', '3100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 20:40:22', '2026-10-13 20:40:22'),
(75, 7, 7, 2, 5, '2026-10-05', '5145.00', '2920.00', '125.00', NULL, NULL, NULL, NULL, '3045.00', '2100.00', '2100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 20:41:05', '2026-10-13 20:41:05'),
(76, 8, 8, 1, 8, '2026-10-05', '7150.00', '4000.00', '150.00', NULL, NULL, NULL, NULL, '4150.00', '3300.00', '3300.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 20:41:52', '2026-10-13 20:41:52'),
(77, 9, 9, 1, 7, '2026-10-05', '7421.00', '3971.00', '150.00', NULL, NULL, NULL, NULL, '4121.00', '3300.00', '3300.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 20:42:41', '2026-10-13 20:42:41'),
(78, 20, 18, 4, 17, '2026-10-06', '5565.00', '2900.00', NULL, NULL, NULL, NULL, NULL, '2900.00', '3100.00', '3100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 20:50:01', '2026-10-13 20:50:01');
INSERT INTO `daily_remittances` (`id`, `driver_id`, `pao_id`, `route_id`, `vehicle_id`, `remittance_date`, `total_collection`, `diesel`, `parking`, `dispatcher`, `food_allowance`, `barker`, `others`, `total_expenses`, `boundary`, `net_remittance`, `is_short_remittance`, `short_amount`, `driver_share`, `driver_amount_paid`, `driver_status`, `pao_share`, `pao_amount_paid`, `pao_status`, `resolution_notes`, `resolved_at`, `status`, `created_at`, `updated_at`) VALUES
(79, 22, 5, 2, 6, '2026-10-06', '5617.00', '3417.00', '100.00', NULL, NULL, NULL, NULL, '3517.00', '2100.00', '2100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 20:51:49', '2026-10-13 20:51:49'),
(80, 10, 10, 4, 16, '2026-10-06', '6654.00', '2950.00', '50.00', '20.00', NULL, NULL, NULL, '3020.00', '3100.00', '3100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 20:52:37', '2026-10-13 20:52:37'),
(81, 5, 13, 4, 14, '2026-10-06', '6920.00', '4358.00', '150.00', '60.00', NULL, NULL, NULL, '4568.00', '3100.00', '3100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 20:57:10', '2026-10-13 20:57:10'),
(82, 1, 1, 7, 1, '2026-10-06', '7243.00', '2858.00', '125.00', '60.00', '200.00', '100.00', NULL, '3343.00', '1.00', '3800.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 21:25:56', '2026-10-13 21:25:56'),
(83, 16, 15, 5, 11, '2026-10-06', '5342.00', '3500.00', NULL, NULL, NULL, NULL, NULL, '3500.00', '2100.00', '1400.00', 1, '700.00', '350.00', '0.00', 'pending', '350.00', '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 21:26:57', '2026-10-13 21:26:57'),
(84, 9, 9, 1, 7, '2026-10-06', '6992.00', '3442.00', '150.00', '100.00', NULL, NULL, NULL, '3692.00', '3300.00', '3300.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 21:28:02', '2026-10-13 21:28:02'),
(85, 14, 16, 5, 10, '2026-10-06', '5839.00', '3739.00', NULL, NULL, NULL, NULL, NULL, '3739.00', '2100.00', '2100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 21:29:16', '2026-10-13 21:29:16'),
(86, 7, 7, 2, 5, '2026-10-06', '5213.00', '2908.00', '125.00', '80.00', NULL, NULL, NULL, '3113.00', '2100.00', '2100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 21:30:17', '2026-10-13 21:30:17'),
(87, 3, 3, 3, 9, '2026-10-06', '7022.00', '3472.00', '150.00', '100.00', NULL, NULL, NULL, '3722.00', '3300.00', '3300.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 21:31:03', '2026-10-13 21:31:03'),
(88, 8, 19, 1, 8, '2026-10-06', '7022.00', '3472.00', '150.00', '100.00', NULL, NULL, NULL, '3722.00', '3300.00', '3300.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 21:31:55', '2026-10-13 21:31:55'),
(89, 4, 4, 6, 12, '2026-10-07', '1287.00', '1287.00', NULL, NULL, NULL, NULL, NULL, '1287.00', '2100.00', '0.00', 1, '2100.00', '0.00', '0.00', 'pending', '2100.00', '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 21:58:21', '2026-10-13 21:58:21'),
(90, 22, 5, 2, 6, '2026-10-07', '5588.00', '3303.00', '125.00', '60.00', NULL, NULL, NULL, '3488.00', '2100.00', '2100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-13 21:59:16', '2026-10-13 21:59:16'),
(91, 3, 19, 3, 9, '2026-10-07', '7103.00', '3553.00', '150.00', '100.00', NULL, NULL, NULL, '3803.00', '3300.00', '3300.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-16 19:41:49', '2026-10-16 19:41:49'),
(92, 6, 6, 4, 14, '2026-10-07', '10681.00', '4142.00', '100.00', '20.00', NULL, NULL, NULL, '4262.00', '3100.00', '3100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-16 19:42:57', '2026-10-16 19:42:57'),
(93, 11, 11, 1, 8, '2026-10-07', '7121.00', '3571.00', '150.00', '100.00', NULL, NULL, NULL, '3821.00', '3300.00', '3300.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-16 19:44:09', '2026-10-16 19:44:09'),
(94, 7, 7, 2, 5, '2026-10-07', '5302.00', '2997.00', '125.00', '80.00', NULL, NULL, NULL, '3202.00', '2100.00', '2100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-16 19:45:09', '2026-10-16 19:45:09'),
(95, 16, 15, 5, 11, '2026-10-07', '6818.00', '4000.00', NULL, NULL, NULL, NULL, NULL, '4000.00', '2100.00', '2100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-16 19:46:01', '2026-10-16 19:46:01'),
(96, 15, 13, 4, 16, '2026-10-07', '4400.00', '2613.00', '150.00', '60.00', NULL, NULL, NULL, '2823.00', '3100.00', '3100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-16 19:52:37', '2026-10-16 19:52:37'),
(97, 9, 9, 1, 7, '2026-10-07', '6998.00', '3468.00', '150.00', '80.00', NULL, NULL, NULL, '3698.00', '3300.00', '3300.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-16 19:53:35', '2026-10-16 19:53:35'),
(98, 14, 14, 5, 10, '2026-10-07', '5260.00', '3346.00', NULL, NULL, NULL, NULL, NULL, '3346.00', '2100.00', '2100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-16 19:55:08', '2026-10-16 19:55:08'),
(99, 20, 18, 4, 17, '2026-10-07', '2845.00', '2932.00', '100.00', '20.00', NULL, NULL, NULL, '3052.00', '3100.00', '3100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-16 19:55:57', '2026-10-16 19:55:57'),
(100, 1, 1, 7, 1, '2026-10-07', '6269.00', '2784.00', '125.00', '60.00', '200.00', '100.00', NULL, '3269.00', '1.00', '3000.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-16 19:56:57', '2026-10-16 19:56:57'),
(101, 21, 20, 2, 2, '2026-10-07', '5302.00', '3072.00', '150.00', '80.00', NULL, NULL, NULL, '3302.00', '2100.00', '2100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-16 19:57:58', '2026-10-16 19:57:58'),
(102, 1, 1, 7, 1, '2026-10-08', '5823.00', '2738.00', '125.00', '60.00', '200.00', '100.00', NULL, '3223.00', '1.00', '2600.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-16 20:34:59', '2026-10-16 20:34:59'),
(103, 7, 7, 2, 5, '2026-10-08', '4359.00', '2099.00', '100.00', '60.00', NULL, NULL, NULL, '2259.00', '2100.00', '2100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-16 20:36:45', '2026-10-16 20:36:45'),
(104, 6, 6, 4, 14, '2026-10-08', '7525.00', '3002.00', '50.00', NULL, NULL, NULL, NULL, '3052.00', '3100.00', '3100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-16 20:37:37', '2026-10-16 20:37:37'),
(105, 17, 19, 1, 7, '2026-10-08', '8687.00', '3787.00', '150.00', '100.00', '1500.00', NULL, NULL, '5537.00', '3300.00', '3300.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-16 20:42:00', '2026-10-16 20:42:00'),
(106, 11, 11, 3, 9, '2026-10-08', '6897.00', '3392.00', '125.00', '80.00', NULL, NULL, NULL, '3597.00', '3300.00', '3300.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-16 20:43:23', '2026-10-16 20:43:23'),
(107, 21, 20, 2, 2, '2026-10-08', '5776.00', '3446.00', '150.00', '80.00', NULL, NULL, NULL, '3676.00', '2100.00', '2100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-16 20:45:58', '2026-10-16 20:45:58'),
(108, 14, 16, 5, 10, '2026-10-08', '4033.00', '3439.00', NULL, NULL, NULL, NULL, NULL, '3439.00', '2100.00', '1600.00', 1, '500.00', '250.00', '0.00', 'pending', '250.00', '0.00', 'pending', NULL, NULL, 'pending', '2026-10-16 20:49:25', '2026-10-16 20:49:25'),
(109, 16, 15, 5, 11, '2026-10-08', '5027.00', '3700.00', NULL, NULL, NULL, NULL, NULL, '3700.00', '2100.00', '1300.00', 1, '800.00', '400.00', '0.00', 'pending', '400.00', '0.00', 'pending', NULL, NULL, 'pending', '2026-10-16 20:50:19', '2026-10-16 20:50:19'),
(110, 8, 8, 1, 8, '2026-10-08', '7154.00', '3624.00', '150.00', '80.00', NULL, NULL, NULL, '3854.00', '3300.00', '3300.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-16 20:51:43', '2026-10-16 20:51:43'),
(111, 5, 10, 4, 17, '2026-10-08', '5617.00', '3643.00', '150.00', '60.00', NULL, NULL, '100.00', '3953.00', '3100.00', '3100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-16 20:52:41', '2026-10-16 20:52:41'),
(112, 22, 5, 5, 11, '2026-10-09', '5100.00', '3000.00', NULL, NULL, NULL, NULL, NULL, '3000.00', '2100.00', '2100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-16 20:55:59', '2026-10-16 20:55:59'),
(113, 20, 18, 4, 17, '2026-10-09', '1695.00', '3409.00', '100.00', '20.00', NULL, NULL, NULL, '3529.00', '3100.00', '2600.00', 1, '500.00', '250.00', '0.00', 'pending', '250.00', '0.00', 'pending', NULL, NULL, 'pending', '2026-10-16 20:59:20', '2026-10-16 21:00:14'),
(114, 1, 1, 7, 1, '2026-10-09', '6906.00', '2661.00', '125.00', '20.00', '200.00', '100.00', NULL, '3106.00', '1.00', '3800.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-16 21:01:36', '2026-10-16 21:01:36'),
(115, 11, 11, 3, 9, '2026-10-09', '5850.00', '2450.00', '100.00', NULL, NULL, NULL, NULL, '2550.00', '3300.00', '3300.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-16 21:04:24', '2026-10-16 21:04:24'),
(116, 14, 16, 5, 10, '2026-10-09', '3080.00', '2000.00', NULL, NULL, '150.00', NULL, '500.00', '2650.00', '0.00', '430.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-16 21:05:59', '2026-10-16 21:05:59'),
(117, 10, 10, 4, 14, '2026-10-09', '6072.00', '2902.00', '50.00', '20.00', NULL, NULL, NULL, '2972.00', '3100.00', '3100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-16 21:06:51', '2026-10-16 21:06:51'),
(118, 4, 22, 2, 4, '2026-10-09', '6011.00', '3706.00', '125.00', '80.00', NULL, NULL, NULL, '3911.00', '2100.00', '2100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-16 21:07:39', '2026-10-16 21:07:39'),
(119, 9, 9, 1, 7, '2026-10-09', '7083.00', '3553.00', '150.00', '80.00', NULL, NULL, NULL, '3783.00', '3300.00', '3300.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-16 21:09:23', '2026-10-16 21:09:23'),
(120, 2, 21, 2, 2, '2026-10-09', '5574.00', '3294.00', '100.00', '80.00', NULL, NULL, NULL, '3474.00', '2100.00', '2100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-16 21:10:40', '2026-10-16 21:10:40'),
(121, 8, 8, 1, 8, '2026-10-09', '4810.00', '2800.00', '100.00', '60.00', NULL, NULL, NULL, '2960.00', '0.00', '1850.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-16 21:12:09', '2026-10-16 21:12:09'),
(122, 7, 7, 2, 5, '2026-10-09', '6132.00', '3677.00', '125.00', '80.00', '150.00', NULL, NULL, '4032.00', '2100.00', '2100.00', 0, NULL, NULL, '0.00', 'pending', NULL, '0.00', 'pending', NULL, NULL, 'pending', '2026-10-16 21:13:16', '2026-10-16 21:13:16');

-- --------------------------------------------------------

--
-- Table structure for table `deductions`
--

CREATE TABLE `deductions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `payroll_id` bigint(20) UNSIGNED DEFAULT NULL,
  `deduction_type` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `effective_date` date DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'HR', 1, '2026-09-03 22:18:22', '2026-09-03 22:18:22'),
(2, 'Admin', 1, '2026-09-03 22:18:39', '2026-09-03 22:18:39'),
(3, 'Accounting', 1, '2026-09-03 22:18:52', '2026-09-03 22:18:52'),
(6, 'Employee', 1, '2026-09-10 17:48:43', '2026-09-10 17:48:43');

-- --------------------------------------------------------

--
-- Table structure for table `drivers`
--

CREATE TABLE `drivers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `license_number` varchar(255) NOT NULL,
  `contact_number` varchar(255) NOT NULL,
  `gender` enum('male','female','prefer_not_to_say') DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `date_of_hire` date DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `drivers`
--

INSERT INTO `drivers` (`id`, `name`, `license_number`, `contact_number`, `gender`, `email`, `address`, `date_of_hire`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Ireneo Felipe', 'C62-36-523666', '09106895827', 'male', 'ireneofelipe@gmail.com', 'Cupang Antipolo City Rizal', '2024-08-01', 'active', '2026-09-17 01:11:41', '2026-10-01 18:30:08'),
(2, 'Florencio Mondigo', 'A12-58-431456', '09674728223', NULL, NULL, NULL, NULL, 'active', '2026-09-30 00:28:43', '2026-09-30 00:28:43'),
(3, 'Joselito Basa', 'C16-45-812674', '09856578781', NULL, NULL, NULL, NULL, 'active', '2026-09-30 19:56:55', '2026-09-30 19:57:09'),
(4, 'Delio Urbino', 'A58-43-549813', '09251354835', NULL, NULL, NULL, NULL, 'active', '2026-09-30 19:59:08', '2026-09-30 19:59:08'),
(5, 'Jhazffer Gonzales', 'N15-32-554612', '09265843215', NULL, NULL, NULL, NULL, 'active', '2026-09-30 19:59:49', '2026-09-30 19:59:49'),
(6, 'Roberto Parilla', 'C16-58-455136', '09366598984', NULL, NULL, NULL, NULL, 'active', '2026-09-30 20:01:33', '2026-09-30 20:01:33'),
(7, 'Alex Estinopo', 'N58-43-215879', '09458135648', NULL, NULL, NULL, NULL, 'active', '2026-09-30 20:01:57', '2026-09-30 20:01:57'),
(8, 'Albert Juanillo', 'D54-76-213548', '09258654989', NULL, NULL, NULL, NULL, 'active', '2026-09-30 20:03:05', '2026-09-30 20:03:05'),
(9, 'Jay Reducto', 'C65-13-879532', '09981655289', NULL, NULL, NULL, NULL, 'active', '2026-09-30 20:03:30', '2026-09-30 20:03:30'),
(10, 'Raffy Lopez', 'C98-41-653216', '09556598716', NULL, NULL, NULL, NULL, 'active', '2026-09-30 20:05:09', '2026-09-30 20:05:09'),
(11, 'Joseph Confesio', 'N14-95-236548', '09563188979', NULL, NULL, NULL, NULL, 'active', '2026-10-01 00:54:06', '2026-10-01 00:54:06'),
(12, 'Romeo Tangjal', 'C54-98-765132', '09565189736', NULL, NULL, NULL, NULL, 'active', '2026-10-01 18:14:38', '2026-10-01 18:14:38'),
(13, 'Bernardo Godalla', 'N16-89-856111', '09876546313', NULL, NULL, NULL, NULL, 'active', '2026-10-01 18:17:52', '2026-10-01 18:17:52'),
(14, 'Daniel Rustin', 'A16-58-976321', '09598984732', NULL, NULL, NULL, NULL, 'active', '2026-10-01 18:33:24', '2026-10-01 18:33:24'),
(15, 'Relly Pamor', 'C65-49-879765', '09056857983', NULL, NULL, NULL, NULL, 'active', '2026-10-01 18:41:06', '2026-10-01 18:41:06'),
(16, 'Romulo Rimando', 'C16-54-897463', '09987645132', NULL, NULL, NULL, NULL, 'active', '2026-10-01 18:45:16', '2026-10-01 18:45:16'),
(17, 'Christian Datoon', 'N87-98-764131', '09589876512', NULL, NULL, NULL, NULL, 'active', '2026-10-01 18:45:51', '2026-10-01 18:45:51'),
(18, 'Joshua Matamis', 'A98-78-654132', '09567897413', NULL, NULL, NULL, NULL, 'active', '2026-10-01 18:46:19', '2026-10-01 18:46:19'),
(19, 'Romnick Viana', 'N89-76-454531', '09465987132', NULL, NULL, NULL, NULL, 'active', '2026-10-01 18:47:16', '2026-10-01 18:47:16'),
(20, 'Kyowanie Martin', 'C98-75-646532', '09296597832', NULL, NULL, NULL, NULL, 'active', '2026-10-01 18:47:54', '2026-10-01 18:47:54'),
(21, 'Rolando Bernal', 'N65-87-987656', '09254986351', NULL, NULL, NULL, NULL, 'active', '2026-10-01 18:49:08', '2026-10-01 18:49:08'),
(22, 'Noel Jesus Nobleza', 'C65-41-798765', '09280955651', NULL, NULL, NULL, NULL, 'active', '2026-10-13 18:04:15', '2026-10-13 18:04:15');

-- --------------------------------------------------------

--
-- Table structure for table `employee_attachments`
--

CREATE TABLE `employee_attachments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `attachment_key` varchar(255) NOT NULL,
  `label` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `file_size` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `rejection_reason` text DEFAULT NULL,
  `reviewed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `uploaded_by_role` enum('hr','employee') NOT NULL DEFAULT 'hr',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_beneficiaries`
--

CREATE TABLE `employee_beneficiaries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `relationship` varchar(255) DEFAULT NULL,
  `sequence` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee_beneficiaries`
--

INSERT INTO `employee_beneficiaries` (`id`, `user_id`, `name`, `date_of_birth`, `relationship`, `sequence`, `created_at`, `updated_at`) VALUES
(3, 11, 'Naomi Lorraine Ramos', NULL, 'Child', 1, '2026-09-18 19:22:51', '2026-09-18 19:22:51'),
(4, 3, 'Divine Carmela Wacay', NULL, 'Spouse', 1, '2026-09-18 20:52:22', '2026-09-18 20:52:22');

-- --------------------------------------------------------

--
-- Table structure for table `employee_experiences`
--

CREATE TABLE `employee_experiences` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `position` varchar(255) DEFAULT NULL,
  `duration` varchar(255) DEFAULT NULL,
  `responsibilities` text DEFAULT NULL,
  `sequence` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_leave_balances`
--

CREATE TABLE `employee_leave_balances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `leave_type_id` bigint(20) UNSIGNED NOT NULL,
  `total_days` int(11) NOT NULL,
  `used_days` int(11) NOT NULL DEFAULT 0,
  `remaining_days` int(11) NOT NULL,
  `year` year(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee_leave_balances`
--

INSERT INTO `employee_leave_balances` (`id`, `user_id`, `leave_type_id`, `total_days`, `used_days`, `remaining_days`, `year`, `created_at`, `updated_at`) VALUES
(1, 3, 1, 15, 0, 15, 2026, '2026-09-03 22:22:13', '2026-09-03 22:22:13'),
(2, 3, 2, 15, 0, 15, 2026, '2026-09-03 22:22:13', '2026-09-03 22:22:13'),
(3, 3, 3, 105, 0, 105, 2026, '2026-09-03 22:22:13', '2026-09-03 22:22:13'),
(4, 3, 4, 7, 0, 7, 2026, '2026-09-03 22:22:13', '2026-09-03 22:22:13'),
(5, 3, 5, 5, 0, 5, 2026, '2026-09-03 22:22:13', '2026-09-03 22:22:13'),
(6, 3, 6, 5, 0, 5, 2026, '2026-09-03 22:22:13', '2026-09-03 22:22:13'),
(7, 4, 1, 15, 0, 15, 2026, '2026-09-03 22:33:32', '2026-09-03 22:33:32'),
(8, 4, 2, 15, 0, 15, 2026, '2026-09-03 22:33:32', '2026-09-03 22:33:32'),
(9, 4, 3, 105, 0, 105, 2026, '2026-09-03 22:33:32', '2026-09-03 22:33:32'),
(10, 4, 4, 7, 0, 7, 2026, '2026-09-03 22:33:32', '2026-09-03 22:33:32'),
(11, 4, 5, 5, 0, 5, 2026, '2026-09-03 22:33:32', '2026-09-03 22:33:32'),
(12, 4, 6, 5, 0, 5, 2026, '2026-09-03 22:33:32', '2026-09-03 22:33:32'),
(25, 7, 1, 15, 0, 15, 2026, '2026-09-10 16:57:20', '2026-09-10 16:57:20'),
(26, 7, 2, 15, 0, 15, 2026, '2026-09-10 16:57:20', '2026-09-10 16:57:20'),
(27, 7, 3, 105, 0, 105, 2026, '2026-09-10 16:57:20', '2026-09-10 16:57:20'),
(28, 7, 4, 7, 0, 7, 2026, '2026-09-10 16:57:20', '2026-09-10 16:57:20'),
(29, 7, 5, 5, 0, 5, 2026, '2026-09-10 16:57:20', '2026-09-10 16:57:20'),
(30, 7, 6, 5, 0, 5, 2026, '2026-09-10 16:57:20', '2026-09-10 16:57:20'),
(31, 8, 1, 15, 0, 15, 2026, '2026-09-10 17:20:22', '2026-09-10 17:20:22'),
(32, 8, 2, 15, 0, 15, 2026, '2026-09-10 17:20:22', '2026-09-10 17:20:22'),
(33, 8, 3, 105, 0, 105, 2026, '2026-09-10 17:20:22', '2026-09-10 17:20:22'),
(34, 8, 4, 7, 0, 7, 2026, '2026-09-10 17:20:22', '2026-09-10 17:20:22'),
(35, 8, 5, 5, 0, 5, 2026, '2026-09-10 17:20:22', '2026-09-10 17:20:22'),
(36, 8, 6, 5, 0, 5, 2026, '2026-09-10 17:20:22', '2026-09-10 17:20:22'),
(37, 9, 1, 15, 0, 15, 2026, '2026-09-10 17:42:18', '2026-09-10 17:42:18'),
(38, 9, 2, 15, 0, 15, 2026, '2026-09-10 17:42:18', '2026-09-10 17:42:18'),
(39, 9, 3, 105, 0, 105, 2026, '2026-09-10 17:42:18', '2026-09-10 17:42:18'),
(40, 9, 4, 7, 0, 7, 2026, '2026-09-10 17:42:18', '2026-09-10 17:42:18'),
(41, 9, 5, 5, 0, 5, 2026, '2026-09-10 17:42:18', '2026-09-10 17:42:18'),
(42, 9, 6, 5, 0, 5, 2026, '2026-09-10 17:42:18', '2026-09-10 17:42:18'),
(43, 10, 1, 15, 0, 15, 2026, '2026-09-10 17:57:04', '2026-09-10 17:57:04'),
(44, 10, 2, 15, 0, 15, 2026, '2026-09-10 17:57:04', '2026-09-10 17:57:04'),
(45, 10, 3, 105, 0, 105, 2026, '2026-09-10 17:57:04', '2026-09-10 17:57:04'),
(46, 10, 4, 7, 0, 7, 2026, '2026-09-10 17:57:04', '2026-09-10 17:57:04'),
(47, 10, 5, 5, 0, 5, 2026, '2026-09-10 17:57:04', '2026-09-10 17:57:04'),
(48, 10, 6, 5, 0, 5, 2026, '2026-09-10 17:57:04', '2026-09-10 17:57:04'),
(49, 11, 1, 15, 0, 15, 2026, '2026-09-18 19:22:51', '2026-09-18 19:22:51'),
(50, 11, 2, 15, 0, 15, 2026, '2026-09-18 19:22:51', '2026-09-18 19:22:51'),
(51, 11, 3, 105, 0, 105, 2026, '2026-09-18 19:22:51', '2026-09-18 19:22:51'),
(52, 11, 4, 7, 0, 7, 2026, '2026-09-18 19:22:51', '2026-09-18 19:22:51'),
(53, 11, 5, 5, 0, 5, 2026, '2026-09-18 19:22:51', '2026-09-18 19:22:51'),
(54, 11, 6, 5, 0, 5, 2026, '2026-09-18 19:22:51', '2026-09-18 19:22:51'),
(55, 1, 1, 15, 0, 15, 2026, '2026-10-15 03:04:20', '2026-10-15 03:04:20'),
(56, 1, 2, 15, 0, 15, 2026, '2026-10-15 03:04:20', '2026-10-15 03:04:20'),
(57, 1, 3, 105, 0, 105, 2026, '2026-10-15 03:04:20', '2026-10-15 03:04:20'),
(58, 1, 4, 7, 0, 7, 2026, '2026-10-15 03:04:20', '2026-10-15 03:04:20'),
(59, 1, 5, 5, 0, 5, 2026, '2026-10-15 03:04:20', '2026-10-15 03:04:20'),
(60, 1, 6, 5, 0, 5, 2026, '2026-10-15 03:04:20', '2026-10-15 03:04:20');

-- --------------------------------------------------------

--
-- Table structure for table `employee_references`
--

CREATE TABLE `employee_references` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact_number` varchar(255) DEFAULT NULL,
  `sequence` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_skills`
--

CREATE TABLE `employee_skills` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `skill_name` varchar(255) NOT NULL,
  `proficiency` varchar(255) DEFAULT NULL,
  `sequence` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `holidays`
--

CREATE TABLE `holidays` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `holidays`
--

INSERT INTO `holidays` (`id`, `name`, `date`, `type`, `created_at`, `updated_at`) VALUES
(1, 'New Year\'s Day', '2026-01-01', 'regular', NULL, NULL),
(2, 'Araw ng Kagitingan', '2026-04-09', 'regular', NULL, NULL),
(3, 'Labor Day', '2026-05-01', 'regular', NULL, NULL),
(4, 'Independence Day', '2026-06-12', 'regular', NULL, NULL),
(5, 'National Heroes Day', '2026-08-31', 'regular', NULL, NULL),
(6, 'Bonifacio Day', '2026-11-30', 'regular', NULL, NULL),
(7, 'Christmas Day', '2026-12-25', 'regular', NULL, NULL),
(8, 'Rizal Day', '2026-12-30', 'regular', NULL, NULL),
(9, 'EDSA People Power Revolution', '2026-02-25', 'special', NULL, NULL),
(10, 'Ninoy Aquino Day', '2026-08-21', 'special', NULL, NULL),
(11, 'All Saints\' Day', '2026-11-01', 'special', NULL, NULL),
(12, 'Christmas Eve', '2026-12-24', 'special', NULL, NULL),
(13, 'New Year\'s Eve', '2026-12-31', 'special', NULL, NULL),
(14, 'New Year\'s Day', '2027-01-01', 'regular', NULL, NULL),
(15, 'Araw ng Kagitingan', '2027-04-09', 'regular', NULL, NULL),
(16, 'Labor Day', '2027-05-01', 'regular', NULL, NULL),
(17, 'Independence Day', '2027-06-12', 'regular', NULL, NULL),
(18, 'National Heroes Day', '2027-08-31', 'regular', NULL, NULL),
(19, 'Bonifacio Day', '2027-11-30', 'regular', NULL, NULL),
(20, 'Christmas Day', '2027-12-25', 'regular', NULL, NULL),
(21, 'Rizal Day', '2027-12-30', 'regular', NULL, NULL),
(22, 'EDSA People Power Revolution', '2027-02-25', 'special', NULL, NULL),
(23, 'Ninoy Aquino Day', '2027-08-21', 'special', NULL, NULL),
(24, 'All Saints\' Day', '2027-11-01', 'special', NULL, NULL),
(25, 'Christmas Eve', '2027-12-24', 'special', NULL, NULL),
(26, 'New Year\'s Eve', '2027-12-31', 'special', NULL, NULL),
(27, 'New Year\'s Day', '2028-01-01', 'regular', NULL, NULL),
(28, 'Araw ng Kagitingan', '2028-04-09', 'regular', NULL, NULL),
(29, 'Labor Day', '2028-05-01', 'regular', NULL, NULL),
(30, 'Independence Day', '2028-06-12', 'regular', NULL, NULL),
(31, 'National Heroes Day', '2028-08-31', 'regular', NULL, NULL),
(32, 'Bonifacio Day', '2028-11-30', 'regular', NULL, NULL),
(33, 'Christmas Day', '2028-12-25', 'regular', NULL, NULL),
(34, 'Rizal Day', '2028-12-30', 'regular', NULL, NULL),
(35, 'EDSA People Power Revolution', '2028-02-25', 'special', NULL, NULL),
(36, 'Ninoy Aquino Day', '2028-08-21', 'special', NULL, NULL),
(37, 'All Saints\' Day', '2028-11-01', 'special', NULL, NULL),
(38, 'Christmas Eve', '2028-12-24', 'special', NULL, NULL),
(39, 'New Year\'s Eve', '2028-12-31', 'special', NULL, NULL),
(40, 'New Year\'s Day', '2029-01-01', 'regular', NULL, NULL),
(41, 'Araw ng Kagitingan', '2029-04-09', 'regular', NULL, NULL),
(42, 'Labor Day', '2029-05-01', 'regular', NULL, NULL),
(43, 'Independence Day', '2029-06-12', 'regular', NULL, NULL),
(44, 'National Heroes Day', '2029-08-31', 'regular', NULL, NULL),
(45, 'Bonifacio Day', '2029-11-30', 'regular', NULL, NULL),
(46, 'Christmas Day', '2029-12-25', 'regular', NULL, NULL),
(47, 'Rizal Day', '2029-12-30', 'regular', NULL, NULL),
(48, 'EDSA People Power Revolution', '2029-02-25', 'special', NULL, NULL),
(49, 'Ninoy Aquino Day', '2029-08-21', 'special', NULL, NULL),
(50, 'All Saints\' Day', '2029-11-01', 'special', NULL, NULL),
(51, 'Christmas Eve', '2029-12-24', 'special', NULL, NULL),
(52, 'New Year\'s Eve', '2029-12-31', 'special', NULL, NULL),
(53, 'New Year\'s Day', '2030-01-01', 'regular', NULL, NULL),
(54, 'Araw ng Kagitingan', '2030-04-09', 'regular', NULL, NULL),
(55, 'Labor Day', '2030-05-01', 'regular', NULL, NULL),
(56, 'Independence Day', '2030-06-12', 'regular', NULL, NULL),
(57, 'National Heroes Day', '2030-08-31', 'regular', NULL, NULL),
(58, 'Bonifacio Day', '2030-11-30', 'regular', NULL, NULL),
(59, 'Christmas Day', '2030-12-25', 'regular', NULL, NULL),
(60, 'Rizal Day', '2030-12-30', 'regular', NULL, NULL),
(61, 'EDSA People Power Revolution', '2030-02-25', 'special', NULL, NULL),
(62, 'Ninoy Aquino Day', '2030-08-21', 'special', NULL, NULL),
(63, 'All Saints\' Day', '2030-11-01', 'special', NULL, NULL),
(64, 'Christmas Eve', '2030-12-24', 'special', NULL, NULL),
(65, 'New Year\'s Eve', '2030-12-31', 'special', NULL, NULL),
(66, 'New Year\'s Day', '2031-01-01', 'regular', NULL, NULL),
(67, 'Araw ng Kagitingan', '2031-04-09', 'regular', NULL, NULL),
(68, 'Labor Day', '2031-05-01', 'regular', NULL, NULL),
(69, 'Independence Day', '2031-06-12', 'regular', NULL, NULL),
(70, 'National Heroes Day', '2031-08-31', 'regular', NULL, NULL),
(71, 'Bonifacio Day', '2031-11-30', 'regular', NULL, NULL),
(72, 'Christmas Day', '2031-12-25', 'regular', NULL, NULL),
(73, 'Rizal Day', '2031-12-30', 'regular', NULL, NULL),
(74, 'EDSA People Power Revolution', '2031-02-25', 'special', NULL, NULL),
(75, 'Ninoy Aquino Day', '2031-08-21', 'special', NULL, NULL),
(76, 'All Saints\' Day', '2031-11-01', 'special', NULL, NULL),
(77, 'Christmas Eve', '2031-12-24', 'special', NULL, NULL),
(78, 'New Year\'s Eve', '2031-12-31', 'special', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `journal_entries`
--

CREATE TABLE `journal_entries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `journal_number` varchar(30) NOT NULL,
  `transaction_date` date NOT NULL,
  `description` varchar(255) NOT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `reference_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('Draft','Posted','Void') NOT NULL DEFAULT 'Draft',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `posted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `journal_entry_lines`
--

CREATE TABLE `journal_entry_lines` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `journal_entry_id` bigint(20) UNSIGNED NOT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `debit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `credit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leaves`
--

CREATE TABLE `leaves` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `leave_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `leave_type` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('pending','approved','rejected','paid') NOT NULL DEFAULT 'pending',
  `paid_days` int(11) NOT NULL DEFAULT 0,
  `unpaid_days` int(11) NOT NULL DEFAULT 0,
  `rejection_reason` text DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_types`
--

CREATE TABLE `leave_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `abbreviation` varchar(255) DEFAULT NULL,
  `days_allowed` int(11) NOT NULL DEFAULT 0,
  `carry_over` tinyint(1) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leave_types`
--

INSERT INTO `leave_types` (`id`, `name`, `abbreviation`, `days_allowed`, `carry_over`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Vacation Leave', 'VL', 15, 1, 'Annual vacation leave for rest and recreation', 'active', '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(2, 'Sick Leave', 'SL', 15, 1, 'Leave for illness or medical appointments', 'active', '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(3, 'Maternity Leave', 'ML', 105, 0, 'Maternity leave for female employees (105 days)', 'active', '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(4, 'Paternity Leave', 'PL', 7, 0, 'Paternity leave for male employees', 'active', '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(5, 'Emergency Leave', 'EL', 5, 0, 'Leave for emergency situations or family emergencies', 'active', '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(6, 'Bereavement Leave', 'BL', 5, 0, 'Leave for the death of an immediate family member', 'active', '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(7, 'Solo Parent', 'SoP', 7, 0, 'Under RA 8972', 'inactive', '2026-09-18 18:39:34', '2026-09-18 18:39:34');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2026_02_26_050700_create_full_smart_accounting_tables', 1),
(2, '2026_06_01_100000_create_additional_tables', 1),
(3, '2026_06_01_200000_add_columns_to_users_drivers_paos', 1),
(4, '2026_06_01_300000_add_columns_to_payroll_tables', 1),
(5, '2026_06_01_400000_add_columns_to_leaves_loans_attendance', 1),
(6, '2026_06_01_500000_add_columns_to_remittances_routes', 1),
(7, '2026_06_03_000001_add_work_days_per_week_to_users', 1),
(8, '2026_06_04_000001_add_type_to_payroll_batches_and_batch_id_to_thirteenth_month_pays', 1),
(9, '2026_07_20_000001_create_position_rates_table', 1),
(10, '2026_07_20_000002_create_departments_table', 1),
(11, '2026_07_20_000003_add_department_id_to_position_rates_table', 1),
(12, '2026_08_25_000001_create_accounting_tables', 1),
(13, '2026_08_30_000001_remap_legacy_attachment_keys', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `title`, `message`, `data`, `read_at`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 7, 'attendance_recorded', 'Time In Recorded', 'Your Time In has been recorded for Sep 10, 2026 at 10:09 AM', '{\"employee_id\":7,\"type\":\"time_in\",\"date\":\"2026-09-09T16:00:00.000000Z\"}', NULL, NULL, '2026-09-10 17:09:59', '2026-09-10 17:09:59'),
(2, 8, 'attendance_recorded', 'Time In Recorded', 'Your Time In has been recorded for Sep 10, 2026 at 10:22 AM', '{\"employee_id\":8,\"type\":\"time_in\",\"date\":\"2026-09-09T16:00:00.000000Z\"}', NULL, NULL, '2026-09-10 17:22:27', '2026-09-10 17:22:27'),
(3, 8, 'attendance_recorded', 'Time Out Recorded', 'Your Time Out has been recorded for Sep 10, 2026 at 10:22 AM', '{\"employee_id\":8,\"type\":\"time_out\",\"date\":\"2026-09-09T16:00:00.000000Z\"}', NULL, NULL, '2026-09-10 17:22:54', '2026-09-10 17:22:54'),
(4, 8, 'overtime_submitted', 'Undertime Record Submitted', 'Your Undertime record for Sep 10, 2026 (5.50 hours) has been submitted.', '{\"record_id\":1,\"type\":\"undertime\",\"hours\":\"5.50\",\"date\":\"2026-09-09T16:00:00.000000Z\"}', NULL, NULL, '2026-09-10 17:22:54', '2026-09-10 17:22:54'),
(5, 7, 'attendance_recorded', 'Time Out Recorded', 'Your Time Out has been recorded for Sep 10, 2026 at 10:34 AM', '{\"employee_id\":7,\"type\":\"time_out\",\"date\":\"2026-09-09T16:00:00.000000Z\"}', NULL, NULL, '2026-09-10 17:34:18', '2026-09-10 17:34:18'),
(6, 7, 'overtime_submitted', 'Undertime Record Submitted', 'Your Undertime record for Sep 10, 2026 (5.50 hours) has been submitted.', '{\"record_id\":2,\"type\":\"undertime\",\"hours\":\"5.50\",\"date\":\"2026-09-09T16:00:00.000000Z\"}', NULL, NULL, '2026-09-10 17:34:18', '2026-09-10 17:34:18'),
(7, 9, 'attendance_recorded', 'Time In Recorded', 'Your Time In has been recorded for Sep 10, 2026 at 13:52 PM', '{\"employee_id\":9,\"type\":\"time_in\",\"date\":\"2026-09-09T16:00:00.000000Z\"}', NULL, NULL, '2026-09-10 20:52:21', '2026-09-10 20:52:21'),
(8, 8, 'attendance_recorded', 'Time Out Recorded', 'Your Time Out has been recorded for Sep 10, 2026 at 14:14 PM', '{\"employee_id\":8,\"type\":\"time_out\",\"date\":\"2026-09-09T16:00:00.000000Z\"}', NULL, NULL, '2026-09-10 21:14:40', '2026-09-10 21:14:40'),
(9, 8, 'overtime_submitted', 'Undertime Record Submitted', 'Your Undertime record for Sep 10, 2026 (3.00 hours) has been submitted.', '{\"record_id\":3,\"type\":\"undertime\",\"hours\":\"3.00\",\"date\":\"2026-09-09T16:00:00.000000Z\"}', NULL, NULL, '2026-09-10 21:14:40', '2026-09-10 21:14:40'),
(10, 8, 'overtime_approved', 'Undertime Record Approved', 'Your Undertime record for Sep 10, 2026 (3.00 hours) has been approved.', '{\"record_id\":3,\"type\":\"undertime\",\"status\":\"approved\"}', NULL, NULL, '2026-09-11 17:14:14', '2026-09-11 17:14:14'),
(11, 7, 'overtime_approved', 'Undertime Record Approved', 'Your Undertime record for Sep 10, 2026 (5.50 hours) has been approved.', '{\"record_id\":2,\"type\":\"undertime\",\"status\":\"approved\"}', NULL, NULL, '2026-09-11 17:14:20', '2026-09-11 17:14:20'),
(14, 8, 'attendance_recorded', 'Time In Recorded', 'Your Time In has been recorded for Sep 17, 2026 at 12:20 PM', '{\"employee_id\":8,\"type\":\"time_in\",\"date\":\"2026-09-16T16:00:00.000000Z\"}', NULL, NULL, '2026-09-17 19:20:20', '2026-09-17 19:20:20'),
(15, 3, 'attendance_recorded', 'Time Out Recorded', 'Your Time Out has been recorded for Sep 01, 2026 at 12:03 PM', '{\"employee_id\":3,\"type\":\"manual_entry\",\"date\":\"2026-08-31T16:00:00.000000Z\"}', NULL, NULL, '2026-09-18 19:03:36', '2026-09-18 19:03:36'),
(16, 10, 'attendance_recorded', 'Time Out Recorded', 'Your Time Out has been recorded for Sep 17, 2026 at 12:25 PM', '{\"employee_id\":10,\"type\":\"manual_entry\",\"date\":\"2026-09-16T16:00:00.000000Z\"}', NULL, NULL, '2026-09-18 19:25:18', '2026-09-18 19:25:18'),
(17, 10, 'overtime_submitted', 'Undertime Record Submitted', 'Your Undertime record for Sep 17, 2026 (1.00 hours) has been submitted.', '{\"record_id\":4,\"type\":\"undertime\",\"hours\":\"1.00\",\"date\":\"2026-09-16T16:00:00.000000Z\"}', NULL, NULL, '2026-09-18 19:25:18', '2026-09-18 19:25:18'),
(18, 10, 'attendance_recorded', 'Time Out Recorded', 'Your Time Out has been recorded for Sep 17, 2026 at 12:34 PM', '{\"employee_id\":10,\"type\":\"manual_entry\",\"date\":\"2026-09-16T16:00:00.000000Z\"}', NULL, NULL, '2026-09-18 19:34:22', '2026-09-18 19:34:22'),
(19, 10, 'attendance_recorded', 'Time Out Recorded', 'Your Time Out has been recorded for Sep 17, 2026 at 12:34 PM', '{\"employee_id\":10,\"type\":\"manual_entry\",\"date\":\"2026-09-16T16:00:00.000000Z\"}', NULL, NULL, '2026-09-18 19:34:55', '2026-09-18 19:34:55'),
(20, 10, 'attendance_recorded', 'Time Out Recorded', 'Your Time Out has been recorded for Sep 17, 2026 at 13:42 PM', '{\"employee_id\":10,\"type\":\"manual_entry\",\"date\":\"2026-09-16T16:00:00.000000Z\"}', NULL, NULL, '2026-09-18 20:42:49', '2026-09-18 20:42:49'),
(21, 10, 'attendance_recorded', 'Time Out Recorded', 'Your Time Out has been recorded for Sep 17, 2026 at 13:43 PM', '{\"employee_id\":10,\"type\":\"manual_entry\",\"date\":\"2026-09-16T16:00:00.000000Z\"}', NULL, NULL, '2026-09-18 20:43:33', '2026-09-18 20:43:33'),
(22, 10, 'overtime_rejected', 'Undertime Record Rejected', 'Your Undertime record for Sep 17, 2026 (1.00 hours) has been rejected.', '{\"record_id\":4,\"type\":\"undertime\",\"status\":\"rejected\",\"reason\":null}', NULL, NULL, '2026-09-18 20:44:12', '2026-09-18 20:44:12'),
(23, 10, 'attendance_recorded', 'Time Out Recorded', 'Your Time Out has been recorded for Sep 01, 2026 at 16:09 PM', '{\"employee_id\":10,\"type\":\"manual_entry\",\"date\":\"2026-08-31T16:00:00.000000Z\"}', NULL, NULL, '2026-09-28 23:09:02', '2026-09-28 23:09:02'),
(24, 10, 'attendance_recorded', 'Time Out Recorded', 'Your Time Out has been recorded for Sep 02, 2026 at 16:09 PM', '{\"employee_id\":10,\"type\":\"manual_entry\",\"date\":\"2026-09-01T16:00:00.000000Z\"}', NULL, NULL, '2026-09-28 23:09:46', '2026-09-28 23:09:46'),
(25, 10, 'overtime_submitted', 'Overtime Record Submitted', 'Your Overtime record for Sep 02, 2026 (1.50 hours) has been submitted.', '{\"record_id\":5,\"type\":\"overtime\",\"hours\":\"1.50\",\"date\":\"2026-09-01T16:00:00.000000Z\"}', NULL, NULL, '2026-09-28 23:09:46', '2026-09-28 23:09:46'),
(26, 10, 'attendance_recorded', 'Time Out Recorded', 'Your Time Out has been recorded for Sep 03, 2026 at 16:10 PM', '{\"employee_id\":10,\"type\":\"manual_entry\",\"date\":\"2026-09-02T16:00:00.000000Z\"}', NULL, NULL, '2026-09-28 23:10:44', '2026-09-28 23:10:44'),
(27, 10, 'overtime_submitted', 'Undertime Record Submitted', 'Your Undertime record for Sep 03, 2026 (8.50 hours) has been submitted.', '{\"record_id\":6,\"type\":\"undertime\",\"hours\":\"8.50\",\"date\":\"2026-09-02T16:00:00.000000Z\"}', NULL, NULL, '2026-09-28 23:10:44', '2026-09-28 23:10:44'),
(28, 10, 'overtime_approved', 'Undertime Record Approved', 'Your Undertime record for Sep 03, 2026 (8.50 hours) has been approved.', '{\"record_id\":6,\"type\":\"undertime\",\"status\":\"approved\"}', NULL, NULL, '2026-09-28 23:11:53', '2026-09-28 23:11:53'),
(29, 10, 'overtime_approved', 'Overtime Record Approved', 'Your Overtime record for Sep 02, 2026 (1.50 hours) has been approved.', '{\"record_id\":5,\"type\":\"overtime\",\"status\":\"approved\"}', NULL, NULL, '2026-09-28 23:12:12', '2026-09-28 23:12:12'),
(30, 10, 'attendance_recorded', 'Time Out Recorded', 'Your Time Out has been recorded for Sep 04, 2026 at 16:13 PM', '{\"employee_id\":10,\"type\":\"manual_entry\",\"date\":\"2026-09-03T16:00:00.000000Z\"}', NULL, NULL, '2026-09-28 23:13:51', '2026-09-28 23:13:51'),
(31, 10, 'overtime_submitted', 'Overtime Record Submitted', 'Your Overtime record for Sep 04, 2026 (1.00 hours) has been submitted.', '{\"record_id\":7,\"type\":\"overtime\",\"hours\":\"1.00\",\"date\":\"2026-09-03T16:00:00.000000Z\"}', NULL, NULL, '2026-09-28 23:13:51', '2026-09-28 23:13:51'),
(32, 10, 'attendance_recorded', 'Time Out Recorded', 'Your Time Out has been recorded for Sep 05, 2026 at 16:16 PM', '{\"employee_id\":10,\"type\":\"manual_entry\",\"date\":\"2026-09-04T16:00:00.000000Z\"}', NULL, NULL, '2026-09-28 23:16:29', '2026-09-28 23:16:29'),
(33, 10, 'overtime_submitted', 'Overtime Record Submitted', 'Your Overtime record for Sep 05, 2026 (1.00 hours) has been submitted.', '{\"record_id\":8,\"type\":\"overtime\",\"hours\":\"1.00\",\"date\":\"2026-09-04T16:00:00.000000Z\"}', NULL, NULL, '2026-09-28 23:16:29', '2026-09-28 23:16:29'),
(34, 10, 'attendance_recorded', 'Time Out Recorded', 'Your Time Out has been recorded for Sep 06, 2026 at 16:17 PM', '{\"employee_id\":10,\"type\":\"manual_entry\",\"date\":\"2026-09-05T16:00:00.000000Z\"}', NULL, NULL, '2026-09-28 23:17:11', '2026-09-28 23:17:11'),
(35, 10, 'overtime_submitted', 'Undertime Record Submitted', 'Your Undertime record for Sep 06, 2026 (3.00 hours) has been submitted.', '{\"record_id\":9,\"type\":\"undertime\",\"hours\":\"3.00\",\"date\":\"2026-09-05T16:00:00.000000Z\"}', NULL, NULL, '2026-09-28 23:17:11', '2026-09-28 23:17:11'),
(36, 10, 'attendance_recorded', 'Time Out Recorded', 'Your Time Out has been recorded for Sep 07, 2026 at 16:18 PM', '{\"employee_id\":10,\"type\":\"manual_entry\",\"date\":\"2026-09-06T16:00:00.000000Z\"}', NULL, NULL, '2026-09-28 23:18:04', '2026-09-28 23:18:04'),
(37, 10, 'attendance_recorded', 'Time Out Recorded', 'Your Time Out has been recorded for Sep 08, 2026 at 16:19 PM', '{\"employee_id\":10,\"type\":\"manual_entry\",\"date\":\"2026-09-07T16:00:00.000000Z\"}', NULL, NULL, '2026-09-28 23:19:38', '2026-09-28 23:19:38'),
(38, 10, 'attendance_recorded', 'Time Out Recorded', 'Your Time Out has been recorded for Sep 09, 2026 at 16:21 PM', '{\"employee_id\":10,\"type\":\"manual_entry\",\"date\":\"2026-09-08T16:00:00.000000Z\"}', NULL, NULL, '2026-09-28 23:21:00', '2026-09-28 23:21:00'),
(39, 10, 'overtime_approved', 'Overtime Record Approved', 'Your Overtime record for Sep 04, 2026 (1.00 hours) has been approved.', '{\"record_id\":7,\"type\":\"overtime\",\"status\":\"approved\"}', NULL, NULL, '2026-09-28 23:23:32', '2026-09-28 23:23:32'),
(40, 10, 'overtime_approved', 'Undertime Record Approved', 'Your Undertime record for Sep 06, 2026 (3.00 hours) has been approved.', '{\"record_id\":9,\"type\":\"undertime\",\"status\":\"approved\"}', NULL, NULL, '2026-09-28 23:23:38', '2026-09-28 23:23:38'),
(41, 10, 'overtime_approved', 'Overtime Record Approved', 'Your Overtime record for Sep 05, 2026 (1.00 hours) has been approved.', '{\"record_id\":8,\"type\":\"overtime\",\"status\":\"approved\"}', NULL, NULL, '2026-09-28 23:23:48', '2026-09-28 23:23:48'),
(42, 10, 'attendance_recorded', 'Time Out Recorded', 'Your Time Out has been recorded for Sep 10, 2026 at 16:26 PM', '{\"employee_id\":10,\"type\":\"manual_entry\",\"date\":\"2026-09-09T16:00:00.000000Z\"}', NULL, NULL, '2026-09-28 23:26:19', '2026-09-28 23:26:19'),
(43, 10, 'attendance_recorded', 'Time Out Recorded', 'Your Time Out has been recorded for Sep 17, 2026 at 17:20 PM', '{\"employee_id\":10,\"type\":\"manual_entry\",\"date\":\"2026-09-16T16:00:00.000000Z\"}', NULL, NULL, '2026-09-29 00:20:51', '2026-09-29 00:20:51'),
(44, 10, 'overtime_submitted', 'Overtime Record Submitted', 'Your Overtime record for Sep 17, 2026 (2.00 hours) has been submitted.', '{\"record_id\":10,\"type\":\"overtime\",\"hours\":\"2.00\",\"date\":\"2026-09-16T16:00:00.000000Z\"}', NULL, NULL, '2026-09-29 00:20:51', '2026-09-29 00:20:51'),
(45, 10, 'attendance_recorded', 'Time Out Recorded', 'Your Time Out has been recorded for Sep 18, 2026 at 17:21 PM', '{\"employee_id\":10,\"type\":\"manual_entry\",\"date\":\"2026-09-17T16:00:00.000000Z\"}', NULL, NULL, '2026-09-29 00:21:41', '2026-09-29 00:21:41'),
(46, 10, 'overtime_submitted', 'Overtime Record Submitted', 'Your Overtime record for Sep 18, 2026 (1.00 hours) has been submitted.', '{\"record_id\":11,\"type\":\"overtime\",\"hours\":\"1.00\",\"date\":\"2026-09-17T16:00:00.000000Z\"}', NULL, NULL, '2026-09-29 00:21:41', '2026-09-29 00:21:41'),
(47, 10, 'attendance_recorded', 'Time Out Recorded', 'Your Time Out has been recorded for Sep 21, 2026 at 17:22 PM', '{\"employee_id\":10,\"type\":\"manual_entry\",\"date\":\"2026-09-20T16:00:00.000000Z\"}', NULL, NULL, '2026-09-29 00:22:33', '2026-09-29 00:22:33'),
(48, 10, 'overtime_submitted', 'Overtime Record Submitted', 'Your Overtime record for Sep 21, 2026 (2.00 hours) has been submitted.', '{\"record_id\":12,\"type\":\"overtime\",\"hours\":\"2.00\",\"date\":\"2026-09-20T16:00:00.000000Z\"}', NULL, NULL, '2026-09-29 00:22:33', '2026-09-29 00:22:33'),
(49, 10, 'attendance_recorded', 'Time Out Recorded', 'Your Time Out has been recorded for Sep 23, 2026 at 17:23 PM', '{\"employee_id\":10,\"type\":\"manual_entry\",\"date\":\"2026-09-22T16:00:00.000000Z\"}', NULL, NULL, '2026-09-29 00:23:11', '2026-09-29 00:23:11'),
(50, 10, 'overtime_submitted', 'Overtime Record Submitted', 'Your Overtime record for Sep 23, 2026 (2.00 hours) has been submitted.', '{\"record_id\":13,\"type\":\"overtime\",\"hours\":\"2.00\",\"date\":\"2026-09-22T16:00:00.000000Z\"}', NULL, NULL, '2026-09-29 00:23:11', '2026-09-29 00:23:11'),
(51, 10, 'overtime_approved', 'Overtime Record Approved', 'Your Overtime record for Sep 23, 2026 (2.00 hours) has been approved.', '{\"record_id\":13,\"type\":\"overtime\",\"status\":\"approved\"}', NULL, NULL, '2026-09-29 00:23:24', '2026-09-29 00:23:24'),
(52, 10, 'overtime_approved', 'Overtime Record Approved', 'Your Overtime record for Sep 21, 2026 (2.00 hours) has been approved.', '{\"record_id\":12,\"type\":\"overtime\",\"status\":\"approved\"}', NULL, NULL, '2026-09-29 00:23:30', '2026-09-29 00:23:30'),
(53, 10, 'overtime_approved', 'Overtime Record Approved', 'Your Overtime record for Sep 18, 2026 (1.00 hours) has been approved.', '{\"record_id\":11,\"type\":\"overtime\",\"status\":\"approved\"}', NULL, NULL, '2026-09-29 00:23:35', '2026-09-29 00:23:35'),
(54, 10, 'overtime_approved', 'Overtime Record Approved', 'Your Overtime record for Sep 17, 2026 (2.00 hours) has been approved.', '{\"record_id\":10,\"type\":\"overtime\",\"status\":\"approved\"}', NULL, NULL, '2026-09-29 00:23:41', '2026-09-29 00:23:41'),
(55, 10, 'attendance_recorded', 'Time Out Recorded', 'Your Time Out has been recorded for Aug 18, 2026 at 17:29 PM', '{\"employee_id\":10,\"type\":\"manual_entry\",\"date\":\"2026-08-17T16:00:00.000000Z\"}', NULL, NULL, '2026-09-29 00:29:52', '2026-09-29 00:29:52'),
(56, 10, 'overtime_submitted', 'Overtime Record Submitted', 'Your Overtime record for Aug 18, 2026 (2.00 hours) has been submitted.', '{\"record_id\":14,\"type\":\"overtime\",\"hours\":\"2.00\",\"date\":\"2026-08-17T16:00:00.000000Z\"}', NULL, NULL, '2026-09-29 00:29:52', '2026-09-29 00:29:52'),
(57, 10, 'attendance_recorded', 'Time Out Recorded', 'Your Time Out has been recorded for Aug 20, 2026 at 17:30 PM', '{\"employee_id\":10,\"type\":\"manual_entry\",\"date\":\"2026-08-19T16:00:00.000000Z\"}', NULL, NULL, '2026-09-29 00:30:28', '2026-09-29 00:30:28'),
(58, 10, 'overtime_submitted', 'Overtime Record Submitted', 'Your Overtime record for Aug 20, 2026 (2.00 hours) has been submitted.', '{\"record_id\":15,\"type\":\"overtime\",\"hours\":\"2.00\",\"date\":\"2026-08-19T16:00:00.000000Z\"}', NULL, NULL, '2026-09-29 00:30:28', '2026-09-29 00:30:28'),
(59, 10, 'attendance_recorded', 'Time Out Recorded', 'Your Time Out has been recorded for Aug 27, 2026 at 17:31 PM', '{\"employee_id\":10,\"type\":\"manual_entry\",\"date\":\"2026-08-26T16:00:00.000000Z\"}', NULL, NULL, '2026-09-29 00:31:06', '2026-09-29 00:31:06'),
(60, 10, 'overtime_submitted', 'Overtime Record Submitted', 'Your Overtime record for Aug 27, 2026 (2.00 hours) has been submitted.', '{\"record_id\":16,\"type\":\"overtime\",\"hours\":\"2.00\",\"date\":\"2026-08-26T16:00:00.000000Z\"}', NULL, NULL, '2026-09-29 00:31:06', '2026-09-29 00:31:06'),
(61, 10, 'overtime_approved', 'Overtime Record Approved', 'Your Overtime record for Aug 27, 2026 (2.00 hours) has been approved.', '{\"record_id\":16,\"type\":\"overtime\",\"status\":\"approved\"}', NULL, NULL, '2026-09-29 00:31:22', '2026-09-29 00:31:22'),
(62, 10, 'overtime_approved', 'Overtime Record Approved', 'Your Overtime record for Aug 20, 2026 (2.00 hours) has been approved.', '{\"record_id\":15,\"type\":\"overtime\",\"status\":\"approved\"}', NULL, NULL, '2026-09-29 00:31:27', '2026-09-29 00:31:27'),
(63, 10, 'overtime_approved', 'Overtime Record Approved', 'Your Overtime record for Aug 18, 2026 (2.00 hours) has been approved.', '{\"record_id\":14,\"type\":\"overtime\",\"status\":\"approved\"}', NULL, NULL, '2026-09-29 00:31:38', '2026-09-29 00:31:38'),
(64, 4, 'remittance_deleted', 'Remittance Deleted', 'IRENEO FELIPE\'s remittance for Sep 15, 2026 amounting to ₱2,000.00 has been deleted.', '{\"remittance_id\":2,\"driver_id\":1,\"driver_name\":\"IRENEO FELIPE\",\"total_collection\":\"5089.00\",\"total_expenses\":\"3356.00\",\"net_remittance\":\"2000.00\",\"pao_name\":\"Juan Bo\",\"vehicle_name\":\"NKI5314\",\"remittance_date\":\"Sep 15, 2026\",\"status\":\"approved\"}', NULL, NULL, '2026-09-30 20:05:44', '2026-09-30 20:05:44'),
(65, 4, 'remittance_created', 'New Remittance Created', 'Raffy Lopez created a new remittance for Sep 28, 2026 amounting to ₱3,100.00.', '{\"remittance_id\":3,\"driver_id\":\"10\",\"driver_name\":\"Raffy Lopez\",\"total_collection\":\"5310.00\",\"total_expenses\":\"2773.00\",\"net_remittance\":\"3100.00\",\"pao_name\":\"Kristian Bindoy\",\"vehicle_name\":\"NKI5265\",\"remittance_date\":\"Sep 28, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-09-30 20:49:55', '2026-09-30 20:49:55'),
(66, 4, 'remittance_created', 'New Remittance Created', 'Jay Reducto created a new remittance for Sep 28, 2026 amounting to ₱3,300.00.', '{\"remittance_id\":4,\"driver_id\":\"9\",\"driver_name\":\"Jay Reducto\",\"total_collection\":\"7077.00\",\"total_expenses\":\"3877.00\",\"net_remittance\":\"3300.00\",\"pao_name\":\"Ediever Guilaran\",\"vehicle_name\":\"NKI5315\",\"remittance_date\":\"Sep 28, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-09-30 20:51:56', '2026-09-30 20:51:56'),
(67, 4, 'remittance_created', 'New Remittance Created', 'Albert Juanillo created a new remittance for Sep 28, 2026 amounting to ₱3,000.00.', '{\"remittance_id\":5,\"driver_id\":\"8\",\"driver_name\":\"Albert Juanillo\",\"total_collection\":\"7077.00\",\"total_expenses\":\"4135.00\",\"net_remittance\":\"3000.00\",\"pao_name\":\"Kris Aldin Gayola\",\"vehicle_name\":\"NKI5316\",\"remittance_date\":\"Sep 28, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-09-30 20:56:42', '2026-09-30 20:56:42'),
(68, 4, 'remittance_created', 'New Remittance Created', 'Alex Estinopo created a new remittance for Sep 28, 2026 amounting to ₱2,100.00.', '{\"remittance_id\":6,\"driver_id\":\"7\",\"driver_name\":\"Alex Estinopo\",\"total_collection\":\"7427.00\",\"total_expenses\":\"3163.00\",\"net_remittance\":\"2100.00\",\"pao_name\":\"Samue Alcantara\",\"vehicle_name\":\"NKI5324\",\"remittance_date\":\"Sep 28, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-09-30 20:57:57', '2026-09-30 20:57:57'),
(69, 4, 'remittance_created', 'New Remittance Created', 'Roberto Parilla created a new remittance for Sep 28, 2026 amounting to ₱3,100.00.', '{\"remittance_id\":7,\"driver_id\":\"6\",\"driver_name\":\"Roberto Parilla\",\"total_collection\":\"8466.00\",\"total_expenses\":\"4489.00\",\"net_remittance\":\"3100.00\",\"pao_name\":\"John Joseph Barcelon\",\"vehicle_name\":\"NKI5255\",\"remittance_date\":\"Sep 28, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-09-30 20:58:49', '2026-09-30 20:58:49');
INSERT INTO `notifications` (`id`, `user_id`, `type`, `title`, `message`, `data`, `read_at`, `deleted_at`, `created_at`, `updated_at`) VALUES
(70, 4, 'remittance_created', 'New Remittance Created', 'Jhazffer Gonzales created a new remittance for Sep 28, 2026 amounting to ₱1,100.00.', '{\"remittance_id\":8,\"driver_id\":\"5\",\"driver_name\":\"Jhazffer Gonzales\",\"total_collection\":\"2614.00\",\"total_expenses\":\"3480.00\",\"net_remittance\":\"1100.00\",\"pao_name\":\"Noel Ragua\",\"vehicle_name\":\"NKI5274\",\"remittance_date\":\"Sep 28, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-09-30 22:18:20', '2026-09-30 22:18:20'),
(71, 4, 'remittance_created', 'New Remittance Created', 'Delio Urbino created a new remittance for Sep 28, 2026 amounting to ₱2,100.00.', '{\"remittance_id\":9,\"driver_id\":\"4\",\"driver_name\":\"Delio Urbino\",\"total_collection\":\"3582.00\",\"total_expenses\":\"3153.00\",\"net_remittance\":\"2100.00\",\"pao_name\":\"Bertmon Reyes\",\"vehicle_name\":\"NKI5256\",\"remittance_date\":\"Sep 28, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-09-30 22:19:03', '2026-09-30 22:19:03'),
(72, 4, 'remittance_created', 'New Remittance Created', 'Joselito Basa created a new remittance for Sep 28, 2026 amounting to ₱3,300.00.', '{\"remittance_id\":10,\"driver_id\":\"3\",\"driver_name\":\"Joselito Basa\",\"total_collection\":\"7550.00\",\"total_expenses\":\"4250.00\",\"net_remittance\":\"3300.00\",\"pao_name\":\"Elco Monforte\",\"vehicle_name\":\"NKI5317\",\"remittance_date\":\"Sep 28, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-09-30 22:19:44', '2026-09-30 22:19:44'),
(73, 4, 'remittance_created', 'New Remittance Created', 'Florencio Mondigo created a new remittance for Sep 28, 2026 amounting to ₱2,500.00.', '{\"remittance_id\":11,\"driver_id\":\"2\",\"driver_name\":\"Florencio Mondigo\",\"total_collection\":\"235.00\",\"total_expenses\":\"3070.00\",\"net_remittance\":\"2500.00\",\"pao_name\":\"Raymart Dizon\",\"vehicle_name\":\"NKI5266\",\"remittance_date\":\"Sep 28, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-09-30 22:20:41', '2026-09-30 22:20:41'),
(74, 4, 'remittance_created', 'New Remittance Created', 'IRENEO FELIPE created a new remittance for Sep 28, 2026 amounting to ₱2,300.00.', '{\"remittance_id\":12,\"driver_id\":\"1\",\"driver_name\":\"IRENEO FELIPE\",\"total_collection\":\"5313.00\",\"total_expenses\":\"3692.00\",\"net_remittance\":\"2300.00\",\"pao_name\":\"Juan Bo\",\"vehicle_name\":\"NKI5314\",\"remittance_date\":\"Sep 28, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-09-30 22:21:34', '2026-09-30 22:21:34'),
(75, 4, 'remittance_created', 'New Remittance Created', 'IRENEO FELIPE created a new remittance for Sep 29, 2026 amounting to ₱1,850.00.', '{\"remittance_id\":13,\"driver_id\":\"1\",\"driver_name\":\"IRENEO FELIPE\",\"total_collection\":\"4569.00\",\"total_expenses\":\"3253.00\",\"net_remittance\":\"1850.00\",\"pao_name\":\"Juan Bo\",\"vehicle_name\":\"NKI5314\",\"remittance_date\":\"Sep 29, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-01 00:47:34', '2026-10-01 00:47:34'),
(76, 4, 'remittance_created', 'New Remittance Created', 'Alex Estinopo created a new remittance for Sep 29, 2026 amounting to ₱2,100.00.', '{\"remittance_id\":14,\"driver_id\":\"7\",\"driver_name\":\"Alex Estinopo\",\"total_collection\":\"6601.00\",\"total_expenses\":\"3081.00\",\"net_remittance\":\"2100.00\",\"pao_name\":\"Samue Alcantara\",\"vehicle_name\":\"NKI5324\",\"remittance_date\":\"Sep 29, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-01 00:49:25', '2026-10-01 00:49:25'),
(77, 4, 'remittance_created', 'New Remittance Created', 'Joseph Confesio created a new remittance for Sep 29, 2026 amounting to ₱2,100.00.', '{\"remittance_id\":15,\"driver_id\":\"11\",\"driver_name\":\"Joseph Confesio\",\"total_collection\":\"6830.00\",\"total_expenses\":\"5039.00\",\"net_remittance\":\"2100.00\",\"pao_name\":\"Marina Sarno\",\"vehicle_name\":\"NKI5307\",\"remittance_date\":\"Sep 29, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-01 18:12:03', '2026-10-01 18:12:03'),
(78, 4, 'remittance_created', 'New Remittance Created', 'Joselito Basa created a new remittance for Sep 29, 2026 amounting to ₱3,300.00.', '{\"remittance_id\":16,\"driver_id\":\"3\",\"driver_name\":\"Joselito Basa\",\"total_collection\":\"7750.00\",\"total_expenses\":\"4550.00\",\"net_remittance\":\"3300.00\",\"pao_name\":\"Elco Monforte\",\"vehicle_name\":\"NKI5317\",\"remittance_date\":\"Sep 29, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-01 18:12:46', '2026-10-01 18:12:46'),
(79, 4, 'remittance_created', 'New Remittance Created', 'Albert Juanillo created a new remittance for Sep 29, 2026 amounting to ₱3,300.00.', '{\"remittance_id\":17,\"driver_id\":\"8\",\"driver_name\":\"Albert Juanillo\",\"total_collection\":\"7177.00\",\"total_expenses\":\"3977.00\",\"net_remittance\":\"3300.00\",\"pao_name\":\"Kris Aldin Gayola\",\"vehicle_name\":\"NKI5316\",\"remittance_date\":\"Sep 29, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-01 18:13:38', '2026-10-01 18:13:38'),
(80, 4, 'remittance_created', 'New Remittance Created', 'Romeo Tangjal created a new remittance for Sep 29, 2026 amounting to ₱2,100.00.', '{\"remittance_id\":18,\"driver_id\":\"12\",\"driver_name\":\"Romeo Tangjal\",\"total_collection\":\"9494.00\",\"total_expenses\":\"2979.00\",\"net_remittance\":\"2100.00\",\"pao_name\":\"Myrna Mayuga\",\"vehicle_name\":\"NKI5274\",\"remittance_date\":\"Sep 29, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-01 18:16:01', '2026-10-01 18:16:01'),
(81, 4, 'remittance_created', 'New Remittance Created', 'Florencio Mondigo created a new remittance for Sep 29, 2026 amounting to ₱3,100.00.', '{\"remittance_id\":19,\"driver_id\":\"2\",\"driver_name\":\"Florencio Mondigo\",\"total_collection\":\"1635.00\",\"total_expenses\":\"3340.00\",\"net_remittance\":\"3100.00\",\"pao_name\":\"Raymart Dizon\",\"vehicle_name\":\"NKI5266\",\"remittance_date\":\"Sep 29, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-01 18:16:47', '2026-10-01 18:16:47'),
(82, 4, 'remittance_created', 'New Remittance Created', 'Bernardo Godalla created a new remittance for Sep 29, 2026 amounting to ₱2,100.00.', '{\"remittance_id\":20,\"driver_id\":\"13\",\"driver_name\":\"Bernardo Godalla\",\"total_collection\":\"4383.00\",\"total_expenses\":\"3719.00\",\"net_remittance\":\"2100.00\",\"pao_name\":\"Michael Tiamson\",\"vehicle_name\":\"NKI5255\",\"remittance_date\":\"Sep 29, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-01 18:31:34', '2026-10-01 18:31:34'),
(83, 4, 'remittance_created', 'New Remittance Created', 'Jhazffer Gonzales created a new remittance for Sep 29, 2026 amounting to ₱1,600.00.', '{\"remittance_id\":21,\"driver_id\":\"5\",\"driver_name\":\"Jhazffer Gonzales\",\"total_collection\":\"2747.00\",\"total_expenses\":\"3257.00\",\"net_remittance\":\"1600.00\",\"pao_name\":\"Noel Ragua\",\"vehicle_name\":\"NKI5256\",\"remittance_date\":\"Sep 29, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-01 18:32:47', '2026-10-01 18:32:47'),
(84, 4, 'remittance_created', 'New Remittance Created', 'Daniel Rustin created a new remittance for Sep 29, 2026 amounting to ₱1,600.00.', '{\"remittance_id\":22,\"driver_id\":\"14\",\"driver_name\":\"Daniel Rustin\",\"total_collection\":\"4897.00\",\"total_expenses\":\"3715.00\",\"net_remittance\":\"1600.00\",\"pao_name\":\"Carl James Obe\\u00f1a\",\"vehicle_name\":\"NKI5267\",\"remittance_date\":\"Sep 29, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-01 18:35:13', '2026-10-01 18:35:13'),
(85, 4, 'remittance_created', 'New Remittance Created', 'Jay Reducto created a new remittance for Sep 29, 2026 amounting to ₱3,300.00.', '{\"remittance_id\":23,\"driver_id\":\"9\",\"driver_name\":\"Jay Reducto\",\"total_collection\":\"6817.00\",\"total_expenses\":\"3617.00\",\"net_remittance\":\"3300.00\",\"pao_name\":\"Ediever Guilaran\",\"vehicle_name\":\"NKI5315\",\"remittance_date\":\"Sep 29, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-01 18:40:16', '2026-10-01 18:40:16'),
(86, 4, 'remittance_created', 'New Remittance Created', 'Relly Pamor created a new remittance for Sep 29, 2026 amounting to ₱1,650.00.', '{\"remittance_id\":24,\"driver_id\":\"15\",\"driver_name\":\"Relly Pamor\",\"total_collection\":\"5910.00\",\"total_expenses\":\"3247.00\",\"net_remittance\":\"1650.00\",\"pao_name\":\"Mary Grace Atoli\",\"vehicle_name\":\"NKI5275\",\"remittance_date\":\"Sep 29, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-01 18:43:48', '2026-10-01 18:43:48'),
(87, 4, 'remittance_created', 'New Remittance Created', 'Raffy Lopez created a new remittance for Sep 29, 2026 amounting to ₱2,200.00.', '{\"remittance_id\":25,\"driver_id\":\"10\",\"driver_name\":\"Raffy Lopez\",\"total_collection\":\"5133.00\",\"total_expenses\":\"3433.00\",\"net_remittance\":\"2200.00\",\"pao_name\":\"Kristian Bindoy\",\"vehicle_name\":\"NKI5265\",\"remittance_date\":\"Sep 29, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-01 18:44:46', '2026-10-01 18:44:46'),
(88, 4, 'remittance_created', 'New Remittance Created', 'Romeo Tangjal created a new remittance for Sep 30, 2026 amounting to ₱658.00.', '{\"remittance_id\":26,\"driver_id\":\"12\",\"driver_name\":\"Romeo Tangjal\",\"total_collection\":\"2145.00\",\"total_expenses\":\"1000.00\",\"net_remittance\":\"658.00\",\"pao_name\":\"Myrna Mayuga\",\"vehicle_name\":\"UNI1234\",\"remittance_date\":\"Sep 30, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-01 18:57:58', '2026-10-01 18:57:58'),
(89, 4, 'remittance_created', 'New Remittance Created', 'Florencio Mondigo created a new remittance for Sep 30, 2026 amounting to ₱3,100.00.', '{\"remittance_id\":27,\"driver_id\":\"2\",\"driver_name\":\"Florencio Mondigo\",\"total_collection\":\"6203.00\",\"total_expenses\":\"3103.00\",\"net_remittance\":\"3100.00\",\"pao_name\":\"Raymart Dizon\",\"vehicle_name\":\"NKI5266\",\"remittance_date\":\"Sep 30, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-01 18:59:33', '2026-10-01 18:59:33'),
(90, 4, 'remittance_created', 'New Remittance Created', 'Delio Urbino created a new remittance for Sep 30, 2026 amounting to ₱2,100.00.', '{\"remittance_id\":28,\"driver_id\":\"4\",\"driver_name\":\"Delio Urbino\",\"total_collection\":\"5741.00\",\"total_expenses\":\"3000.00\",\"net_remittance\":\"2100.00\",\"pao_name\":\"Bertmon Reyes\",\"vehicle_name\":\"NKI5256\",\"remittance_date\":\"Sep 30, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-01 19:00:23', '2026-10-01 19:00:23'),
(91, 4, 'remittance_created', 'New Remittance Created', 'Daniel Rustin created a new remittance for Sep 30, 2026 amounting to ₱1,600.00.', '{\"remittance_id\":29,\"driver_id\":\"14\",\"driver_name\":\"Daniel Rustin\",\"total_collection\":\"4655.00\",\"total_expenses\":\"3743.00\",\"net_remittance\":\"1600.00\",\"pao_name\":\"Darren Lloyd Kho\",\"vehicle_name\":\"NKI5267\",\"remittance_date\":\"Sep 30, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-01 19:01:55', '2026-10-01 19:01:55'),
(92, 4, 'remittance_created', 'New Remittance Created', 'Romulo Rimando created a new remittance for Sep 30, 2026 amounting to ₱1,600.00.', '{\"remittance_id\":30,\"driver_id\":\"16\",\"driver_name\":\"Romulo Rimando\",\"total_collection\":\"4720.00\",\"total_expenses\":\"3120.00\",\"net_remittance\":\"1600.00\",\"pao_name\":\"Mary Grace Atoli\",\"vehicle_name\":\"NKI5275\",\"remittance_date\":\"Sep 30, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-01 19:02:43', '2026-10-01 19:02:43'),
(93, 4, 'remittance_created', 'New Remittance Created', 'Ireneo Felipe created a new remittance for Sep 30, 2026 amounting to ₱2,400.00.', '{\"remittance_id\":31,\"driver_id\":\"1\",\"driver_name\":\"Ireneo Felipe\",\"total_collection\":\"5744.00\",\"total_expenses\":\"3344.00\",\"net_remittance\":\"2400.00\",\"pao_name\":\"Juan Bo\",\"vehicle_name\":\"NKI5314\",\"remittance_date\":\"Sep 30, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-01 19:03:40', '2026-10-01 19:03:40'),
(94, 4, 'remittance_created', 'New Remittance Created', 'Roberto Parilla created a new remittance for Sep 30, 2026 amounting to ₱2,100.00.', '{\"remittance_id\":32,\"driver_id\":\"6\",\"driver_name\":\"Roberto Parilla\",\"total_collection\":\"6974.00\",\"total_expenses\":\"3960.00\",\"net_remittance\":\"2100.00\",\"pao_name\":\"John Joseph Barcelon\",\"vehicle_name\":\"NKI5255\",\"remittance_date\":\"Sep 30, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-01 19:04:23', '2026-10-01 19:04:23'),
(95, 4, 'remittance_created', 'New Remittance Created', 'Joseph Confesio created a new remittance for Sep 30, 2026 amounting to ₱2,100.00.', '{\"remittance_id\":33,\"driver_id\":\"11\",\"driver_name\":\"Joseph Confesio\",\"total_collection\":\"8155.00\",\"total_expenses\":\"3588.00\",\"net_remittance\":\"2100.00\",\"pao_name\":\"Marina Sarno\",\"vehicle_name\":\"NKI5307\",\"remittance_date\":\"Sep 30, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-01 19:05:24', '2026-10-01 19:05:24'),
(96, 4, 'remittance_created', 'New Remittance Created', 'Alex Estinopo created a new remittance for Sep 30, 2026 amounting to ₱2,100.00.', '{\"remittance_id\":34,\"driver_id\":\"7\",\"driver_name\":\"Alex Estinopo\",\"total_collection\":\"9150.00\",\"total_expenses\":\"3123.00\",\"net_remittance\":\"2100.00\",\"pao_name\":\"Samue Alcantara\",\"vehicle_name\":\"NKI5324\",\"remittance_date\":\"Sep 30, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-01 19:06:06', '2026-10-01 19:06:06'),
(97, 4, 'remittance_created', 'New Remittance Created', 'Jay Reducto created a new remittance for Sep 30, 2026 amounting to ₱3,300.00.', '{\"remittance_id\":35,\"driver_id\":\"9\",\"driver_name\":\"Jay Reducto\",\"total_collection\":\"6914.00\",\"total_expenses\":\"3614.00\",\"net_remittance\":\"3300.00\",\"pao_name\":\"Ediever Guilaran\",\"vehicle_name\":\"NKI5315\",\"remittance_date\":\"Sep 30, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-01 19:06:50', '2026-10-01 19:06:50'),
(98, 4, 'remittance_created', 'New Remittance Created', 'Romnick Viana created a new remittance for Sep 30, 2026 amounting to ₱1,600.00.', '{\"remittance_id\":36,\"driver_id\":\"19\",\"driver_name\":\"Romnick Viana\",\"total_collection\":\"4563.00\",\"total_expenses\":\"2880.00\",\"net_remittance\":\"1600.00\",\"pao_name\":\"Lenie Nunez\",\"vehicle_name\":\"NKI5305\",\"remittance_date\":\"Sep 30, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-01 19:08:24', '2026-10-01 19:08:24'),
(99, 4, 'remittance_created', 'New Remittance Created', 'Kyowanie Martin created a new remittance for Sep 30, 2026 amounting to ₱3,100.00.', '{\"remittance_id\":37,\"driver_id\":\"20\",\"driver_name\":\"Kyowanie Martin\",\"total_collection\":\"7110.00\",\"total_expenses\":\"4723.00\",\"net_remittance\":\"3100.00\",\"pao_name\":\"Jorick Batiancila\",\"vehicle_name\":\"NKI5264\",\"remittance_date\":\"Sep 30, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-01 19:09:31', '2026-10-01 19:09:31'),
(100, 4, 'remittance_created', 'New Remittance Created', 'Rolando Bernal created a new remittance for Sep 30, 2026 amounting to ₱3,300.00.', '{\"remittance_id\":38,\"driver_id\":\"21\",\"driver_name\":\"Rolando Bernal\",\"total_collection\":\"6738.00\",\"total_expenses\":\"3438.00\",\"net_remittance\":\"3300.00\",\"pao_name\":\"Eloisa Meterio\",\"vehicle_name\":\"NKI5317\",\"remittance_date\":\"Sep 30, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-01 19:11:03', '2026-10-01 19:11:03'),
(101, 3, 'attendance_recorded', 'Time Out Recorded', 'Your Time Out has been recorded for Sep 16, 2026 at 16:32 PM', '{\"employee_id\":3,\"type\":\"manual_entry\",\"date\":\"2026-09-15T16:00:00.000000Z\"}', NULL, NULL, '2026-10-01 23:32:30', '2026-10-01 23:32:30'),
(102, 3, 'overtime_submitted', 'Overtime Record Submitted', 'Your Overtime record for Sep 16, 2026 (1.00 hours) has been submitted.', '{\"record_id\":17,\"type\":\"overtime\",\"hours\":\"1.00\",\"date\":\"2026-09-15T16:00:00.000000Z\"}', NULL, NULL, '2026-10-01 23:32:30', '2026-10-01 23:32:30'),
(103, 3, 'attendance_recorded', 'Time Out Recorded', 'Your Time Out has been recorded for Sep 18, 2026 at 16:33 PM', '{\"employee_id\":3,\"type\":\"manual_entry\",\"date\":\"2026-09-17T16:00:00.000000Z\"}', NULL, NULL, '2026-10-01 23:33:18', '2026-10-01 23:33:18'),
(104, 3, 'overtime_submitted', 'Overtime Record Submitted', 'Your Overtime record for Sep 18, 2026 (1.00 hours) has been submitted.', '{\"record_id\":18,\"type\":\"overtime\",\"hours\":\"1.00\",\"date\":\"2026-09-17T16:00:00.000000Z\"}', NULL, NULL, '2026-10-01 23:33:18', '2026-10-01 23:33:18'),
(105, 3, 'attendance_recorded', 'Time Out Recorded', 'Your Time Out has been recorded for Oct 19, 2026 at 16:33 PM', '{\"employee_id\":3,\"type\":\"manual_entry\",\"date\":\"2026-10-18T16:00:00.000000Z\"}', NULL, NULL, '2026-10-01 23:33:52', '2026-10-01 23:33:52'),
(106, 3, 'overtime_submitted', 'Overtime Record Submitted', 'Your Overtime record for Oct 19, 2026 (2.00 hours) has been submitted.', '{\"record_id\":19,\"type\":\"overtime\",\"hours\":\"2.00\",\"date\":\"2026-10-18T16:00:00.000000Z\"}', NULL, NULL, '2026-10-01 23:33:53', '2026-10-01 23:33:53'),
(107, 3, 'attendance_recorded', 'Time Out Recorded', 'Your Time Out has been recorded for Sep 30, 2026 at 16:34 PM', '{\"employee_id\":3,\"type\":\"manual_entry\",\"date\":\"2026-09-29T16:00:00.000000Z\"}', NULL, NULL, '2026-10-01 23:34:44', '2026-10-01 23:34:44'),
(108, 3, 'overtime_submitted', 'Overtime Record Submitted', 'Your Overtime record for Sep 30, 2026 (3.00 hours) has been submitted.', '{\"record_id\":20,\"type\":\"overtime\",\"hours\":\"3.00\",\"date\":\"2026-09-29T16:00:00.000000Z\"}', NULL, NULL, '2026-10-01 23:34:44', '2026-10-01 23:34:44'),
(109, 3, 'overtime_approved', 'Overtime Record Approved', 'Your Overtime record for Sep 30, 2026 (3.00 hours) has been approved.', '{\"record_id\":20,\"type\":\"overtime\",\"status\":\"approved\"}', NULL, NULL, '2026-10-01 23:35:10', '2026-10-01 23:35:10'),
(110, 3, 'overtime_approved', 'Overtime Record Approved', 'Your Overtime record for Oct 19, 2026 (2.00 hours) has been approved.', '{\"record_id\":19,\"type\":\"overtime\",\"status\":\"approved\"}', NULL, NULL, '2026-10-01 23:35:15', '2026-10-01 23:35:15'),
(111, 3, 'overtime_approved', 'Overtime Record Approved', 'Your Overtime record for Sep 18, 2026 (1.00 hours) has been approved.', '{\"record_id\":18,\"type\":\"overtime\",\"status\":\"approved\"}', NULL, NULL, '2026-10-01 23:35:25', '2026-10-01 23:35:25'),
(112, 3, 'overtime_approved', 'Overtime Record Approved', 'Your Overtime record for Sep 16, 2026 (1.00 hours) has been approved.', '{\"record_id\":17,\"type\":\"overtime\",\"status\":\"approved\"}', NULL, NULL, '2026-10-01 23:35:31', '2026-10-01 23:35:31'),
(113, 4, 'remittance_created', 'New Remittance Created', 'Kyowanie Martin created a new remittance for Oct 01, 2026 amounting to ₱3,100.00.', '{\"remittance_id\":39,\"driver_id\":\"20\",\"driver_name\":\"Kyowanie Martin\",\"total_collection\":\"5300.00\",\"total_expenses\":\"2636.00\",\"net_remittance\":\"3100.00\",\"pao_name\":\"Jorick Batiancila\",\"vehicle_name\":\"NKI5264\",\"remittance_date\":\"Oct 01, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-05 21:21:08', '2026-10-05 21:21:08'),
(114, 4, 'remittance_created', 'New Remittance Created', 'Raffy Lopez created a new remittance for Oct 01, 2026 amounting to ₱3,100.00.', '{\"remittance_id\":40,\"driver_id\":\"10\",\"driver_name\":\"Raffy Lopez\",\"total_collection\":\"5858.00\",\"total_expenses\":\"2716.00\",\"net_remittance\":\"3100.00\",\"pao_name\":\"Kristian Bindoy\",\"vehicle_name\":\"NKI5265\",\"remittance_date\":\"Oct 01, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-05 21:21:58', '2026-10-05 21:21:58');
INSERT INTO `notifications` (`id`, `user_id`, `type`, `title`, `message`, `data`, `read_at`, `deleted_at`, `created_at`, `updated_at`) VALUES
(115, 4, 'remittance_created', 'New Remittance Created', 'Romulo Rimando created a new remittance for Oct 01, 2026 amounting to ₱2,100.00.', '{\"remittance_id\":41,\"driver_id\":\"16\",\"driver_name\":\"Romulo Rimando\",\"total_collection\":\"6460.00\",\"total_expenses\":\"4000.00\",\"net_remittance\":\"2100.00\",\"pao_name\":\"Mary Grace Atoli\",\"vehicle_name\":\"NKI5275\",\"remittance_date\":\"Oct 01, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-05 21:23:01', '2026-10-05 21:23:01'),
(116, 4, 'remittance_created', 'New Remittance Created', 'Alex Estinopo created a new remittance for Oct 01, 2026 amounting to ₱2,100.00.', '{\"remittance_id\":42,\"driver_id\":\"7\",\"driver_name\":\"Alex Estinopo\",\"total_collection\":\"6168.00\",\"total_expenses\":\"3125.00\",\"net_remittance\":\"2100.00\",\"pao_name\":\"Samue Alcantara\",\"vehicle_name\":\"NKI5324\",\"remittance_date\":\"Oct 01, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-07 19:35:35', '2026-10-07 19:35:35'),
(117, 4, 'remittance_created', 'New Remittance Created', 'Joselito Basa created a new remittance for Oct 01, 2026 amounting to ₱3,300.00.', '{\"remittance_id\":43,\"driver_id\":\"3\",\"driver_name\":\"Joselito Basa\",\"total_collection\":\"7622.00\",\"total_expenses\":\"4322.00\",\"net_remittance\":\"3300.00\",\"pao_name\":\"Elco Monforte\",\"vehicle_name\":\"NKI5317\",\"remittance_date\":\"Oct 01, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-07 19:36:40', '2026-10-07 19:36:40'),
(118, 4, 'remittance_created', 'New Remittance Created', 'Albert Juanillo created a new remittance for Oct 01, 2026 amounting to ₱3,300.00.', '{\"remittance_id\":44,\"driver_id\":\"8\",\"driver_name\":\"Albert Juanillo\",\"total_collection\":\"7122.00\",\"total_expenses\":\"3822.00\",\"net_remittance\":\"3300.00\",\"pao_name\":\"Kris Aldin Gayola\",\"vehicle_name\":\"NKI5316\",\"remittance_date\":\"Oct 01, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-07 20:00:36', '2026-10-07 20:00:36'),
(119, 4, 'remittance_created', 'New Remittance Created', 'Rolando Bernal created a new remittance for Oct 01, 2026 amounting to ₱2,100.00.', '{\"remittance_id\":45,\"driver_id\":\"21\",\"driver_name\":\"Rolando Bernal\",\"total_collection\":\"5910.00\",\"total_expenses\":\"3690.00\",\"net_remittance\":\"2100.00\",\"pao_name\":\"Joel Divina\",\"vehicle_name\":\"NKI5305\",\"remittance_date\":\"Oct 01, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 17:58:21', '2026-10-13 17:58:21'),
(120, 4, 'remittance_created', 'New Remittance Created', 'Joseph Confesio created a new remittance for Oct 01, 2026 amounting to ₱2,100.00.', '{\"remittance_id\":46,\"driver_id\":\"11\",\"driver_name\":\"Joseph Confesio\",\"total_collection\":\"6048.00\",\"total_expenses\":\"3714.00\",\"net_remittance\":\"2100.00\",\"pao_name\":\"Marina Sarno\",\"vehicle_name\":\"NKI5307\",\"remittance_date\":\"Oct 01, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 17:59:26', '2026-10-13 17:59:26'),
(121, 4, 'remittance_created', 'New Remittance Created', 'Roberto Parilla created a new remittance for Oct 01, 2026 amounting to ₱3,100.00.', '{\"remittance_id\":47,\"driver_id\":\"6\",\"driver_name\":\"Roberto Parilla\",\"total_collection\":\"5457.00\",\"total_expenses\":\"3912.00\",\"net_remittance\":\"3100.00\",\"pao_name\":\"Ricky Carcueva\",\"vehicle_name\":\"NKI5255\",\"remittance_date\":\"Oct 01, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 18:01:08', '2026-10-13 18:01:08'),
(122, 4, 'remittance_created', 'New Remittance Created', 'Ireneo Felipe created a new remittance for Oct 01, 2026 amounting to ₱3,050.00.', '{\"remittance_id\":48,\"driver_id\":\"1\",\"driver_name\":\"Ireneo Felipe\",\"total_collection\":\"6764.00\",\"total_expenses\":\"3714.00\",\"net_remittance\":\"3050.00\",\"pao_name\":\"Juan Bo\",\"vehicle_name\":\"NKI5314\",\"remittance_date\":\"Oct 01, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 18:02:18', '2026-10-13 18:02:18'),
(123, 4, 'remittance_created', 'New Remittance Created', 'Daniel Rustin created a new remittance for Oct 01, 2026 amounting to ₱1,800.00.', '{\"remittance_id\":49,\"driver_id\":\"14\",\"driver_name\":\"Daniel Rustin\",\"total_collection\":\"4560.00\",\"total_expenses\":\"1743.00\",\"net_remittance\":\"1800.00\",\"pao_name\":\"Darren Lloyd Kho\",\"vehicle_name\":\"NKI5267\",\"remittance_date\":\"Oct 01, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 18:03:20', '2026-10-13 18:03:20'),
(124, 4, 'remittance_created', 'New Remittance Created', 'Noel Jesus Nobleza created a new remittance for Oct 01, 2026 amounting to ₱3,300.00.', '{\"remittance_id\":50,\"driver_id\":\"22\",\"driver_name\":\"Noel Jesus Nobleza\",\"total_collection\":\"6405.00\",\"total_expenses\":\"3105.00\",\"net_remittance\":\"3300.00\",\"pao_name\":\"Noel Ragua\",\"vehicle_name\":\"NKI5315\",\"remittance_date\":\"Oct 01, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 18:05:40', '2026-10-13 18:05:40'),
(125, 4, 'remittance_created', 'New Remittance Created', 'Jhazffer Gonzales created a new remittance for Oct 01, 2026 amounting to ₱3,100.00.', '{\"remittance_id\":51,\"driver_id\":\"5\",\"driver_name\":\"Jhazffer Gonzales\",\"total_collection\":\"6545\",\"total_expenses\":\"3445.00\",\"net_remittance\":\"3100.00\",\"pao_name\":\"Michael Tiamson\",\"vehicle_name\":\"NKI5266\",\"remittance_date\":\"Oct 01, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 18:07:13', '2026-10-13 18:07:13'),
(126, 4, 'remittance_created', 'New Remittance Created', 'Joshua Matamis created a new remittance for Oct 02, 2026 amounting to ₱0.00.', '{\"remittance_id\":52,\"driver_id\":\"18\",\"driver_name\":\"Joshua Matamis\",\"total_collection\":\"725.00\",\"total_expenses\":\"825.00\",\"net_remittance\":\"0.00\",\"pao_name\":\"Jhunelle Sarte\",\"vehicle_name\":\"NKI5307\",\"remittance_date\":\"Oct 02, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 18:44:16', '2026-10-13 18:44:16'),
(127, 4, 'remittance_created', 'New Remittance Created', 'Jay Reducto created a new remittance for Oct 02, 2026 amounting to ₱3,300.00.', '{\"remittance_id\":53,\"driver_id\":\"9\",\"driver_name\":\"Jay Reducto\",\"total_collection\":\"7008.00\",\"total_expenses\":\"3708.00\",\"net_remittance\":\"3300.00\",\"pao_name\":\"Ediever Guilaran\",\"vehicle_name\":\"NKI5315\",\"remittance_date\":\"Oct 02, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 18:46:36', '2026-10-13 18:46:36'),
(128, 4, 'remittance_created', 'New Remittance Created', 'Ireneo Felipe created a new remittance for Oct 02, 2026 amounting to ₱3,550.00.', '{\"remittance_id\":54,\"driver_id\":\"1\",\"driver_name\":\"Ireneo Felipe\",\"total_collection\":\"7410.00\",\"total_expenses\":\"3860.00\",\"net_remittance\":\"3550.00\",\"pao_name\":\"Juan Bo\",\"vehicle_name\":\"NKI5314\",\"remittance_date\":\"Oct 02, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 18:47:46', '2026-10-13 18:47:46'),
(129, 4, 'remittance_created', 'New Remittance Created', 'Relly Pamor created a new remittance for Oct 02, 2026 amounting to ₱2,100.00.', '{\"remittance_id\":55,\"driver_id\":\"15\",\"driver_name\":\"Relly Pamor\",\"total_collection\":\"3110.00\",\"total_expenses\":\"3035.00\",\"net_remittance\":\"2100.00\",\"pao_name\":\"Ricky Carcueva\",\"vehicle_name\":\"NKI5305\",\"remittance_date\":\"Oct 02, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 18:49:44', '2026-10-13 18:49:44'),
(130, 4, 'remittance_created', 'New Remittance Created', 'Alex Estinopo created a new remittance for Oct 02, 2026 amounting to ₱2,100.00.', '{\"remittance_id\":56,\"driver_id\":\"7\",\"driver_name\":\"Alex Estinopo\",\"total_collection\":\"4163.00\",\"total_expenses\":\"3556.00\",\"net_remittance\":\"2100.00\",\"pao_name\":\"Samue Alcantara\",\"vehicle_name\":\"NKI5324\",\"remittance_date\":\"Oct 02, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 18:50:50', '2026-10-13 18:50:50'),
(131, 4, 'remittance_created', 'New Remittance Created', 'Roberto Parilla created a new remittance for Oct 02, 2026 amounting to ₱3,100.00.', '{\"remittance_id\":57,\"driver_id\":\"6\",\"driver_name\":\"Roberto Parilla\",\"total_collection\":\"7265.00\",\"total_expenses\":\"3401.00\",\"net_remittance\":\"3100.00\",\"pao_name\":\"John Joseph Barcelon\",\"vehicle_name\":\"NKI5255\",\"remittance_date\":\"Oct 02, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 18:52:39', '2026-10-13 18:52:39'),
(132, 4, 'remittance_created', 'New Remittance Created', 'Noel Jesus Nobleza created a new remittance for Oct 02, 2026 amounting to ₱2,100.00.', '{\"remittance_id\":58,\"driver_id\":\"22\",\"driver_name\":\"Noel Jesus Nobleza\",\"total_collection\":\"262.00\",\"total_expenses\":\"2437.00\",\"net_remittance\":\"2100.00\",\"pao_name\":\"Noel Ragua\",\"vehicle_name\":\"NKI5275\",\"remittance_date\":\"Oct 02, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 18:54:01', '2026-10-13 18:54:01'),
(133, 4, 'remittance_created', 'New Remittance Created', 'Albert Juanillo created a new remittance for Oct 02, 2026 amounting to ₱3,000.00.', '{\"remittance_id\":59,\"driver_id\":\"8\",\"driver_name\":\"Albert Juanillo\",\"total_collection\":\"6092.00\",\"total_expenses\":\"3092.00\",\"net_remittance\":\"3000.00\",\"pao_name\":\"Kris Aldin Gayola\",\"vehicle_name\":\"NKI5316\",\"remittance_date\":\"Oct 02, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 18:56:17', '2026-10-13 18:56:17'),
(134, 4, 'remittance_created', 'New Remittance Created', 'Raffy Lopez created a new remittance for Oct 02, 2026 amounting to ₱3,100.00.', '{\"remittance_id\":60,\"driver_id\":\"10\",\"driver_name\":\"Raffy Lopez\",\"total_collection\":\"5942.00\",\"total_expenses\":\"3547.00\",\"net_remittance\":\"3100.00\",\"pao_name\":\"Kristian Bindoy\",\"vehicle_name\":\"NKI5265\",\"remittance_date\":\"Oct 02, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 18:59:59', '2026-10-13 18:59:59'),
(135, 4, 'remittance_created', 'New Remittance Created', 'Jhazffer Gonzales created a new remittance for Oct 02, 2026 amounting to ₱2,100.00.', '{\"remittance_id\":61,\"driver_id\":\"5\",\"driver_name\":\"Jhazffer Gonzales\",\"total_collection\":\"6162.00\",\"total_expenses\":\"4062.00\",\"net_remittance\":\"2100\",\"pao_name\":\"Michael Tiamson\",\"vehicle_name\":\"NKI5266\",\"remittance_date\":\"Oct 02, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 19:04:01', '2026-10-13 19:04:01'),
(136, 4, 'remittance_updated', 'Remittance Updated', 'Jhazffer Gonzales\'s remittance has been updated. Net amount: ₱2,100.00.', '{\"remittance_id\":61,\"driver_id\":\"5\",\"driver_name\":\"Jhazffer Gonzales\",\"total_collection\":\"6162.00\",\"total_expenses\":\"4062.00\",\"net_remittance\":\"2100.00\",\"pao_name\":\"Michael Tiamson\",\"vehicle_name\":\"NKI5266\",\"remittance_date\":\"Oct 02, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 19:04:20', '2026-10-13 19:04:20'),
(137, 4, 'remittance_created', 'New Remittance Created', 'Daniel Rustin created a new remittance for Oct 02, 2026 amounting to ₱2,100.00.', '{\"remittance_id\":62,\"driver_id\":\"14\",\"driver_name\":\"Daniel Rustin\",\"total_collection\":\"4296.00\",\"total_expenses\":\"2384.00\",\"net_remittance\":\"2100.00\",\"pao_name\":\"Darren Lloyd Kho\",\"vehicle_name\":\"NKI5267\",\"remittance_date\":\"Oct 02, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 19:05:46', '2026-10-13 19:05:46'),
(138, 4, 'remittance_created', 'New Remittance Created', 'Joselito Basa created a new remittance for Oct 02, 2026 amounting to ₱3,300.00.', '{\"remittance_id\":63,\"driver_id\":\"3\",\"driver_name\":\"Joselito Basa\",\"total_collection\":\"7730.00\",\"total_expenses\":\"4430.00\",\"net_remittance\":\"3300.00\",\"pao_name\":\"Elco Monforte\",\"vehicle_name\":\"NKI5317\",\"remittance_date\":\"Oct 02, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 19:07:22', '2026-10-13 19:07:22'),
(139, 4, 'remittance_created', 'New Remittance Created', 'Kyowanie Martin created a new remittance for Oct 02, 2026 amounting to ₱3,100.00.', '{\"remittance_id\":64,\"driver_id\":\"20\",\"driver_name\":\"Kyowanie Martin\",\"total_collection\":\"3981.00\",\"total_expenses\":\"4189.00\",\"net_remittance\":\"3100.00\",\"pao_name\":\"Jorick Batiancila\",\"vehicle_name\":\"NKI5264\",\"remittance_date\":\"Oct 02, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 19:09:59', '2026-10-13 19:09:59'),
(140, 4, 'remittance_created', 'New Remittance Created', 'Romnick Viana created a new remittance for Oct 02, 2026 amounting to ₱1,600.00.', '{\"remittance_id\":65,\"driver_id\":\"19\",\"driver_name\":\"Romnick Viana\",\"total_collection\":\"1234.00\",\"total_expenses\":\"3534.00\",\"net_remittance\":\"1600.00\",\"pao_name\":\"Lenie Nunez\",\"vehicle_name\":\"NKI5256\",\"remittance_date\":\"Oct 02, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 19:11:25', '2026-10-13 19:11:25'),
(141, 4, 'remittance_created', 'New Remittance Created', 'Joseph Confesio created a new remittance for Oct 05, 2026 amounting to ₱3,300.00.', '{\"remittance_id\":66,\"driver_id\":\"11\",\"driver_name\":\"Joseph Confesio\",\"total_collection\":\"7002.00\",\"total_expenses\":\"3702.00\",\"net_remittance\":\"3300.00\",\"pao_name\":\"Marina Sarno\",\"vehicle_name\":\"NKI5317\",\"remittance_date\":\"Oct 05, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 20:23:19', '2026-10-13 20:23:19'),
(142, 4, 'remittance_created', 'New Remittance Created', 'Jhazffer Gonzales created a new remittance for Oct 05, 2026 amounting to ₱3,100.00.', '{\"remittance_id\":67,\"driver_id\":\"5\",\"driver_name\":\"Jhazffer Gonzales\",\"total_collection\":\"3715.00\",\"total_expenses\":\"4250.00\",\"net_remittance\":\"3100.00\",\"pao_name\":\"Jorick Batiancila\",\"vehicle_name\":\"NKI5264\",\"remittance_date\":\"Oct 05, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 20:26:21', '2026-10-13 20:26:21'),
(143, 4, 'remittance_created', 'New Remittance Created', 'Daniel Rustin created a new remittance for Oct 05, 2026 amounting to ₱1,700.00.', '{\"remittance_id\":68,\"driver_id\":\"14\",\"driver_name\":\"Daniel Rustin\",\"total_collection\":\"3830.00\",\"total_expenses\":\"2743.00\",\"net_remittance\":\"1700.00\",\"pao_name\":\"Darren Lloyd Kho\",\"vehicle_name\":\"NKI5267\",\"remittance_date\":\"Oct 05, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 20:28:19', '2026-10-13 20:28:19'),
(144, 4, 'remittance_created', 'New Remittance Created', 'Delio Urbino created a new remittance for Oct 05, 2026 amounting to ₱2,100.00.', '{\"remittance_id\":69,\"driver_id\":\"4\",\"driver_name\":\"Delio Urbino\",\"total_collection\":\"4655.00\",\"total_expenses\":\"3026.00\",\"net_remittance\":\"2100.00\",\"pao_name\":\"Bertmon Reyes\",\"vehicle_name\":\"NKI5256\",\"remittance_date\":\"Oct 05, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 20:30:15', '2026-10-13 20:30:15'),
(145, 4, 'remittance_created', 'New Remittance Created', 'Relly Pamor created a new remittance for Oct 05, 2026 amounting to ₱2,100.00.', '{\"remittance_id\":70,\"driver_id\":\"15\",\"driver_name\":\"Relly Pamor\",\"total_collection\":\"5054.00\",\"total_expenses\":\"2954.00\",\"net_remittance\":\"2100.00\",\"pao_name\":\"Ricky Carcueva\",\"vehicle_name\":\"NKI5305\",\"remittance_date\":\"Oct 05, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 20:32:17', '2026-10-13 20:32:17'),
(146, 4, 'remittance_created', 'New Remittance Created', 'Noel Jesus Nobleza created a new remittance for Oct 05, 2026 amounting to ₱1,600.00.', '{\"remittance_id\":71,\"driver_id\":\"22\",\"driver_name\":\"Noel Jesus Nobleza\",\"total_collection\":\"4526\",\"total_expenses\":\"2926.00\",\"net_remittance\":\"1600.00\",\"pao_name\":\"Noel Ragua\",\"vehicle_name\":\"NKI5325\",\"remittance_date\":\"Oct 05, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 20:37:54', '2026-10-13 20:37:54'),
(147, 4, 'remittance_created', 'New Remittance Created', 'Romulo Rimando created a new remittance for Oct 05, 2026 amounting to ₱2,100.00.', '{\"remittance_id\":72,\"driver_id\":\"16\",\"driver_name\":\"Romulo Rimando\",\"total_collection\":\"6905.00\",\"total_expenses\":\"3808.00\",\"net_remittance\":\"2100.00\",\"pao_name\":\"Mary Grace Atoli\",\"vehicle_name\":\"NKI5275\",\"remittance_date\":\"Oct 05, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 20:38:47', '2026-10-13 20:38:47'),
(148, 4, 'remittance_created', 'New Remittance Created', 'Raffy Lopez created a new remittance for Oct 05, 2026 amounting to ₱3,100.00.', '{\"remittance_id\":73,\"driver_id\":\"10\",\"driver_name\":\"Raffy Lopez\",\"total_collection\":\"6130.00\",\"total_expenses\":\"2699.00\",\"net_remittance\":\"3100.00\",\"pao_name\":\"Kristian Bindoy\",\"vehicle_name\":\"NKI5265\",\"remittance_date\":\"Oct 05, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 20:39:45', '2026-10-13 20:39:45'),
(149, 4, 'remittance_created', 'New Remittance Created', 'Roberto Parilla created a new remittance for Oct 05, 2026 amounting to ₱3,100.00.', '{\"remittance_id\":74,\"driver_id\":\"6\",\"driver_name\":\"Roberto Parilla\",\"total_collection\":\"9929.00\",\"total_expenses\":\"4244.00\",\"net_remittance\":\"3100.00\",\"pao_name\":\"John Joseph Barcelon\",\"vehicle_name\":\"NKI5255\",\"remittance_date\":\"Oct 05, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 20:40:22', '2026-10-13 20:40:22'),
(150, 4, 'remittance_created', 'New Remittance Created', 'Alex Estinopo created a new remittance for Oct 05, 2026 amounting to ₱2,100.00.', '{\"remittance_id\":75,\"driver_id\":\"7\",\"driver_name\":\"Alex Estinopo\",\"total_collection\":\"5145.00\",\"total_expenses\":\"3045.00\",\"net_remittance\":\"2100.00\",\"pao_name\":\"Samue Alcantara\",\"vehicle_name\":\"NKI5324\",\"remittance_date\":\"Oct 05, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 20:41:05', '2026-10-13 20:41:05'),
(151, 4, 'remittance_created', 'New Remittance Created', 'Albert Juanillo created a new remittance for Oct 05, 2026 amounting to ₱3,300.00.', '{\"remittance_id\":76,\"driver_id\":\"8\",\"driver_name\":\"Albert Juanillo\",\"total_collection\":\"7150.00\",\"total_expenses\":\"4150.00\",\"net_remittance\":\"3300.00\",\"pao_name\":\"Kris Aldin Gayola\",\"vehicle_name\":\"NKI5316\",\"remittance_date\":\"Oct 05, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 20:41:52', '2026-10-13 20:41:52'),
(152, 4, 'remittance_created', 'New Remittance Created', 'Jay Reducto created a new remittance for Oct 05, 2026 amounting to ₱3,300.00.', '{\"remittance_id\":77,\"driver_id\":\"9\",\"driver_name\":\"Jay Reducto\",\"total_collection\":\"7421\",\"total_expenses\":\"4121.00\",\"net_remittance\":\"3300.00\",\"pao_name\":\"Ediever Guilaran\",\"vehicle_name\":\"NKI5315\",\"remittance_date\":\"Oct 05, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 20:42:41', '2026-10-13 20:42:41'),
(153, 4, 'remittance_created', 'New Remittance Created', 'Kyowanie Martin created a new remittance for Oct 06, 2026 amounting to ₱3,100.00.', '{\"remittance_id\":78,\"driver_id\":\"20\",\"driver_name\":\"Kyowanie Martin\",\"total_collection\":\"5565.00\",\"total_expenses\":\"2900.00\",\"net_remittance\":\"3100.00\",\"pao_name\":\"Jorick Batiancila\",\"vehicle_name\":\"NKI5264\",\"remittance_date\":\"Oct 06, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 20:50:01', '2026-10-13 20:50:01'),
(154, 4, 'remittance_created', 'New Remittance Created', 'Noel Jesus Nobleza created a new remittance for Oct 06, 2026 amounting to ₱2,100.00.', '{\"remittance_id\":79,\"driver_id\":\"22\",\"driver_name\":\"Noel Jesus Nobleza\",\"total_collection\":\"5617.00\",\"total_expenses\":\"3517.00\",\"net_remittance\":\"2100.00\",\"pao_name\":\"Noel Ragua\",\"vehicle_name\":\"NKI5325\",\"remittance_date\":\"Oct 06, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 20:51:49', '2026-10-13 20:51:49');
INSERT INTO `notifications` (`id`, `user_id`, `type`, `title`, `message`, `data`, `read_at`, `deleted_at`, `created_at`, `updated_at`) VALUES
(155, 4, 'remittance_created', 'New Remittance Created', 'Raffy Lopez created a new remittance for Oct 06, 2026 amounting to ₱3,100.00.', '{\"remittance_id\":80,\"driver_id\":\"10\",\"driver_name\":\"Raffy Lopez\",\"total_collection\":\"6654.00\",\"total_expenses\":\"3020.00\",\"net_remittance\":\"3100.00\",\"pao_name\":\"Kristian Bindoy\",\"vehicle_name\":\"NKI5265\",\"remittance_date\":\"Oct 06, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 20:52:37', '2026-10-13 20:52:37'),
(156, 4, 'remittance_created', 'New Remittance Created', 'Jhazffer Gonzales created a new remittance for Oct 06, 2026 amounting to ₱3,100.00.', '{\"remittance_id\":81,\"driver_id\":\"5\",\"driver_name\":\"Jhazffer Gonzales\",\"total_collection\":\"6920.00\",\"total_expenses\":\"4568.00\",\"net_remittance\":\"3100\",\"pao_name\":\"Michael Tiamson\",\"vehicle_name\":\"NKI5255\",\"remittance_date\":\"Oct 06, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 20:57:10', '2026-10-13 20:57:10'),
(157, 4, 'remittance_created', 'New Remittance Created', 'Ireneo Felipe created a new remittance for Oct 06, 2026 amounting to ₱3,800.00.', '{\"remittance_id\":82,\"driver_id\":\"1\",\"driver_name\":\"Ireneo Felipe\",\"total_collection\":\"7243\",\"total_expenses\":\"3343.00\",\"net_remittance\":\"3800.00\",\"pao_name\":\"Juan Bo\",\"vehicle_name\":\"NKI5314\",\"remittance_date\":\"Oct 06, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 21:25:56', '2026-10-13 21:25:56'),
(158, 4, 'remittance_created', 'New Remittance Created', 'Romulo Rimando created a new remittance for Oct 06, 2026 amounting to ₱1,400.00.', '{\"remittance_id\":83,\"driver_id\":\"16\",\"driver_name\":\"Romulo Rimando\",\"total_collection\":\"5342.00\",\"total_expenses\":\"3500.00\",\"net_remittance\":\"1400.00\",\"pao_name\":\"Mary Grace Atoli\",\"vehicle_name\":\"NKI5275\",\"remittance_date\":\"Oct 06, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 21:26:57', '2026-10-13 21:26:57'),
(159, 4, 'remittance_created', 'New Remittance Created', 'Jay Reducto created a new remittance for Oct 06, 2026 amounting to ₱3,300.00.', '{\"remittance_id\":84,\"driver_id\":\"9\",\"driver_name\":\"Jay Reducto\",\"total_collection\":\"6992.00\",\"total_expenses\":\"3692.00\",\"net_remittance\":\"3300.00\",\"pao_name\":\"Ediever Guilaran\",\"vehicle_name\":\"NKI5315\",\"remittance_date\":\"Oct 06, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 21:28:02', '2026-10-13 21:28:02'),
(160, 4, 'remittance_created', 'New Remittance Created', 'Daniel Rustin created a new remittance for Oct 06, 2026 amounting to ₱2,100.00.', '{\"remittance_id\":85,\"driver_id\":\"14\",\"driver_name\":\"Daniel Rustin\",\"total_collection\":\"5839.00\",\"total_expenses\":\"3739.00\",\"net_remittance\":\"2100.00\",\"pao_name\":\"Darren Lloyd Kho\",\"vehicle_name\":\"NKI5267\",\"remittance_date\":\"Oct 06, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 21:29:17', '2026-10-13 21:29:17'),
(161, 4, 'remittance_created', 'New Remittance Created', 'Alex Estinopo created a new remittance for Oct 06, 2026 amounting to ₱2,100.00.', '{\"remittance_id\":86,\"driver_id\":\"7\",\"driver_name\":\"Alex Estinopo\",\"total_collection\":\"5213.00\",\"total_expenses\":\"3113.00\",\"net_remittance\":\"2100.00\",\"pao_name\":\"Samue Alcantara\",\"vehicle_name\":\"NKI5324\",\"remittance_date\":\"Oct 06, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 21:30:17', '2026-10-13 21:30:17'),
(162, 4, 'remittance_created', 'New Remittance Created', 'Joselito Basa created a new remittance for Oct 06, 2026 amounting to ₱3,300.00.', '{\"remittance_id\":87,\"driver_id\":\"3\",\"driver_name\":\"Joselito Basa\",\"total_collection\":\"7022.00\",\"total_expenses\":\"3722.00\",\"net_remittance\":\"3300.00\",\"pao_name\":\"Elco Monforte\",\"vehicle_name\":\"NKI5317\",\"remittance_date\":\"Oct 06, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 21:31:03', '2026-10-13 21:31:03'),
(163, 4, 'remittance_created', 'New Remittance Created', 'Albert Juanillo created a new remittance for Oct 06, 2026 amounting to ₱3,300.00.', '{\"remittance_id\":88,\"driver_id\":\"8\",\"driver_name\":\"Albert Juanillo\",\"total_collection\":\"7022.00\",\"total_expenses\":\"3722.00\",\"net_remittance\":\"3300.00\",\"pao_name\":\"Eloisa Meterio\",\"vehicle_name\":\"NKI5316\",\"remittance_date\":\"Oct 06, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 21:31:55', '2026-10-13 21:31:55'),
(164, 4, 'remittance_created', 'New Remittance Created', 'Delio Urbino created a new remittance for Oct 07, 2026 amounting to ₱0.00.', '{\"remittance_id\":89,\"driver_id\":\"4\",\"driver_name\":\"Delio Urbino\",\"total_collection\":\"1287.00\",\"total_expenses\":\"1287.00\",\"net_remittance\":\"0.00\",\"pao_name\":\"Bertmon Reyes\",\"vehicle_name\":\"NKI5256\",\"remittance_date\":\"Oct 07, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 21:58:22', '2026-10-13 21:58:22'),
(165, 4, 'remittance_created', 'New Remittance Created', 'Noel Jesus Nobleza created a new remittance for Oct 07, 2026 amounting to ₱2,100.00.', '{\"remittance_id\":90,\"driver_id\":\"22\",\"driver_name\":\"Noel Jesus Nobleza\",\"total_collection\":\"5588.00\",\"total_expenses\":\"3488.00\",\"net_remittance\":\"2100.00\",\"pao_name\":\"Noel Ragua\",\"vehicle_name\":\"NKI5325\",\"remittance_date\":\"Oct 07, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-13 21:59:16', '2026-10-13 21:59:16'),
(166, 4, 'remittance_created', 'New Remittance Created', 'Joselito Basa created a new remittance for Oct 07, 2026 amounting to ₱3,300.00.', '{\"remittance_id\":91,\"driver_id\":\"3\",\"driver_name\":\"Joselito Basa\",\"total_collection\":\"7103.00\",\"total_expenses\":\"3803.00\",\"net_remittance\":\"3300.00\",\"pao_name\":\"Eloisa Meterio\",\"vehicle_name\":\"NKI5317\",\"remittance_date\":\"Oct 07, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-16 19:41:49', '2026-10-16 19:41:49'),
(167, 4, 'remittance_created', 'New Remittance Created', 'Roberto Parilla created a new remittance for Oct 07, 2026 amounting to ₱3,100.00.', '{\"remittance_id\":92,\"driver_id\":\"6\",\"driver_name\":\"Roberto Parilla\",\"total_collection\":\"10681.00\",\"total_expenses\":\"4262.00\",\"net_remittance\":\"3100.00\",\"pao_name\":\"John Joseph Barcelon\",\"vehicle_name\":\"NKI5255\",\"remittance_date\":\"Oct 07, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-16 19:42:57', '2026-10-16 19:42:57'),
(168, 4, 'remittance_created', 'New Remittance Created', 'Joseph Confesio created a new remittance for Oct 07, 2026 amounting to ₱3,300.00.', '{\"remittance_id\":93,\"driver_id\":\"11\",\"driver_name\":\"Joseph Confesio\",\"total_collection\":\"7121.00\",\"total_expenses\":\"3821.00\",\"net_remittance\":\"3300\",\"pao_name\":\"Marina Sarno\",\"vehicle_name\":\"NKI5316\",\"remittance_date\":\"Oct 07, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-16 19:44:09', '2026-10-16 19:44:09'),
(169, 4, 'remittance_created', 'New Remittance Created', 'Alex Estinopo created a new remittance for Oct 07, 2026 amounting to ₱2,100.00.', '{\"remittance_id\":94,\"driver_id\":\"7\",\"driver_name\":\"Alex Estinopo\",\"total_collection\":\"5302.00\",\"total_expenses\":\"3202.00\",\"net_remittance\":\"2100\",\"pao_name\":\"Samue Alcantara\",\"vehicle_name\":\"NKI5324\",\"remittance_date\":\"Oct 07, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-16 19:45:09', '2026-10-16 19:45:09'),
(170, 4, 'remittance_created', 'New Remittance Created', 'Romulo Rimando created a new remittance for Oct 07, 2026 amounting to ₱2,100.00.', '{\"remittance_id\":95,\"driver_id\":\"16\",\"driver_name\":\"Romulo Rimando\",\"total_collection\":\"6818.00\",\"total_expenses\":\"4000.00\",\"net_remittance\":\"2100\",\"pao_name\":\"Mary Grace Atoli\",\"vehicle_name\":\"NKI5275\",\"remittance_date\":\"Oct 07, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-16 19:46:01', '2026-10-16 19:46:01'),
(171, 4, 'remittance_created', 'New Remittance Created', 'Relly Pamor created a new remittance for Oct 07, 2026 amounting to ₱3,100.00.', '{\"remittance_id\":96,\"driver_id\":\"15\",\"driver_name\":\"Relly Pamor\",\"total_collection\":\"4400.00\",\"total_expenses\":\"2823.00\",\"net_remittance\":\"3100\",\"pao_name\":\"Michael Tiamson\",\"vehicle_name\":\"NKI5265\",\"remittance_date\":\"Oct 07, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-16 19:52:37', '2026-10-16 19:52:37'),
(172, 4, 'remittance_created', 'New Remittance Created', 'Jay Reducto created a new remittance for Oct 07, 2026 amounting to ₱3,300.00.', '{\"remittance_id\":97,\"driver_id\":\"9\",\"driver_name\":\"Jay Reducto\",\"total_collection\":\"6998\",\"total_expenses\":\"3698.00\",\"net_remittance\":\"3300.00\",\"pao_name\":\"Ediever Guilaran\",\"vehicle_name\":\"NKI5315\",\"remittance_date\":\"Oct 07, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-16 19:53:35', '2026-10-16 19:53:35'),
(173, 4, 'remittance_created', 'New Remittance Created', 'Daniel Rustin created a new remittance for Oct 07, 2026 amounting to ₱2,100.00.', '{\"remittance_id\":98,\"driver_id\":\"14\",\"driver_name\":\"Daniel Rustin\",\"total_collection\":\"5260.00\",\"total_expenses\":\"3346.00\",\"net_remittance\":\"2100\",\"pao_name\":\"Carl James Obe\\u00f1a\",\"vehicle_name\":\"NKI5267\",\"remittance_date\":\"Oct 07, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-16 19:55:08', '2026-10-16 19:55:08'),
(174, 4, 'remittance_created', 'New Remittance Created', 'Kyowanie Martin created a new remittance for Oct 07, 2026 amounting to ₱3,100.00.', '{\"remittance_id\":99,\"driver_id\":\"20\",\"driver_name\":\"Kyowanie Martin\",\"total_collection\":\"2845.00\",\"total_expenses\":\"3052.00\",\"net_remittance\":\"3100\",\"pao_name\":\"Jorick Batiancila\",\"vehicle_name\":\"NKI5264\",\"remittance_date\":\"Oct 07, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-16 19:55:57', '2026-10-16 19:55:57'),
(175, 4, 'remittance_created', 'New Remittance Created', 'Ireneo Felipe created a new remittance for Oct 07, 2026 amounting to ₱3,000.00.', '{\"remittance_id\":100,\"driver_id\":\"1\",\"driver_name\":\"Ireneo Felipe\",\"total_collection\":\"6269\",\"total_expenses\":\"3269.00\",\"net_remittance\":\"3000.00\",\"pao_name\":\"Juan Bo\",\"vehicle_name\":\"NKI5314\",\"remittance_date\":\"Oct 07, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-16 19:56:57', '2026-10-16 19:56:57'),
(176, 4, 'remittance_created', 'New Remittance Created', 'Rolando Bernal created a new remittance for Oct 07, 2026 amounting to ₱2,100.00.', '{\"remittance_id\":101,\"driver_id\":\"21\",\"driver_name\":\"Rolando Bernal\",\"total_collection\":\"5302.00\",\"total_expenses\":\"3302.00\",\"net_remittance\":\"2100.00\",\"pao_name\":\"Joel Divina\",\"vehicle_name\":\"NKI5305\",\"remittance_date\":\"Oct 07, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-16 19:57:58', '2026-10-16 19:57:58'),
(177, 4, 'remittance_created', 'New Remittance Created', 'Ireneo Felipe created a new remittance for Oct 08, 2026 amounting to ₱2,600.00.', '{\"remittance_id\":102,\"driver_id\":\"1\",\"driver_name\":\"Ireneo Felipe\",\"total_collection\":\"5823.00\",\"total_expenses\":\"3223.00\",\"net_remittance\":\"2600.00\",\"pao_name\":\"Juan Bo\",\"vehicle_name\":\"NKI5314\",\"remittance_date\":\"Oct 08, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-16 20:34:59', '2026-10-16 20:34:59'),
(178, 4, 'remittance_created', 'New Remittance Created', 'Alex Estinopo created a new remittance for Oct 08, 2026 amounting to ₱2,100.00.', '{\"remittance_id\":103,\"driver_id\":\"7\",\"driver_name\":\"Alex Estinopo\",\"total_collection\":\"4359.00\",\"total_expenses\":\"2259.00\",\"net_remittance\":\"2100.00\",\"pao_name\":\"Samue Alcantara\",\"vehicle_name\":\"NKI5324\",\"remittance_date\":\"Oct 08, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-16 20:36:45', '2026-10-16 20:36:45'),
(179, 4, 'remittance_created', 'New Remittance Created', 'Roberto Parilla created a new remittance for Oct 08, 2026 amounting to ₱3,100.00.', '{\"remittance_id\":104,\"driver_id\":\"6\",\"driver_name\":\"Roberto Parilla\",\"total_collection\":\"7525.00\",\"total_expenses\":\"3052.00\",\"net_remittance\":\"3100.00\",\"pao_name\":\"John Joseph Barcelon\",\"vehicle_name\":\"NKI5255\",\"remittance_date\":\"Oct 08, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-16 20:37:37', '2026-10-16 20:37:37'),
(180, 4, 'remittance_created', 'New Remittance Created', 'Christian Datoon created a new remittance for Oct 08, 2026 amounting to ₱3,300.00.', '{\"remittance_id\":105,\"driver_id\":\"17\",\"driver_name\":\"Christian Datoon\",\"total_collection\":\"8687.00\",\"total_expenses\":\"5537.00\",\"net_remittance\":\"3300.00\",\"pao_name\":\"Eloisa Meterio\",\"vehicle_name\":\"NKI5315\",\"remittance_date\":\"Oct 08, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-16 20:42:00', '2026-10-16 20:42:00'),
(181, 4, 'remittance_created', 'New Remittance Created', 'Joseph Confesio created a new remittance for Oct 08, 2026 amounting to ₱3,300.00.', '{\"remittance_id\":106,\"driver_id\":\"11\",\"driver_name\":\"Joseph Confesio\",\"total_collection\":\"6897.00\",\"total_expenses\":\"3597.00\",\"net_remittance\":\"3300.00\",\"pao_name\":\"Marina Sarno\",\"vehicle_name\":\"NKI5317\",\"remittance_date\":\"Oct 08, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-16 20:43:23', '2026-10-16 20:43:23'),
(182, 4, 'remittance_created', 'New Remittance Created', 'Rolando Bernal created a new remittance for Oct 08, 2026 amounting to ₱2,100.00.', '{\"remittance_id\":107,\"driver_id\":\"21\",\"driver_name\":\"Rolando Bernal\",\"total_collection\":\"5776.00\",\"total_expenses\":\"3676.00\",\"net_remittance\":\"2100.00\",\"pao_name\":\"Joel Divina\",\"vehicle_name\":\"NKI5305\",\"remittance_date\":\"Oct 08, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-16 20:45:58', '2026-10-16 20:45:58'),
(183, 4, 'remittance_created', 'New Remittance Created', 'Daniel Rustin created a new remittance for Oct 08, 2026 amounting to ₱1,600.00.', '{\"remittance_id\":108,\"driver_id\":\"14\",\"driver_name\":\"Daniel Rustin\",\"total_collection\":\"4033.00\",\"total_expenses\":\"3439.00\",\"net_remittance\":\"1600.00\",\"pao_name\":\"Darren Lloyd Kho\",\"vehicle_name\":\"NKI5267\",\"remittance_date\":\"Oct 08, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-16 20:49:25', '2026-10-16 20:49:25'),
(184, 4, 'remittance_created', 'New Remittance Created', 'Romulo Rimando created a new remittance for Oct 08, 2026 amounting to ₱1,300.00.', '{\"remittance_id\":109,\"driver_id\":\"16\",\"driver_name\":\"Romulo Rimando\",\"total_collection\":\"5027.00\",\"total_expenses\":\"3700.00\",\"net_remittance\":\"1300.00\",\"pao_name\":\"Mary Grace Atoli\",\"vehicle_name\":\"NKI5275\",\"remittance_date\":\"Oct 08, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-16 20:50:19', '2026-10-16 20:50:19'),
(185, 4, 'remittance_created', 'New Remittance Created', 'Albert Juanillo created a new remittance for Oct 08, 2026 amounting to ₱3,300.00.', '{\"remittance_id\":110,\"driver_id\":\"8\",\"driver_name\":\"Albert Juanillo\",\"total_collection\":\"7154\",\"total_expenses\":\"3854.00\",\"net_remittance\":\"3300.00\",\"pao_name\":\"Kris Aldin Gayola\",\"vehicle_name\":\"NKI5316\",\"remittance_date\":\"Oct 08, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-16 20:51:43', '2026-10-16 20:51:43'),
(186, 4, 'remittance_created', 'New Remittance Created', 'Jhazffer Gonzales created a new remittance for Oct 08, 2026 amounting to ₱3,100.00.', '{\"remittance_id\":111,\"driver_id\":\"5\",\"driver_name\":\"Jhazffer Gonzales\",\"total_collection\":\"5617.00\",\"total_expenses\":\"3953.00\",\"net_remittance\":\"3100.00\",\"pao_name\":\"Kristian Bindoy\",\"vehicle_name\":\"NKI5264\",\"remittance_date\":\"Oct 08, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-16 20:52:41', '2026-10-16 20:52:41'),
(187, 4, 'remittance_created', 'New Remittance Created', 'Noel Jesus Nobleza created a new remittance for Oct 09, 2026 amounting to ₱2,100.00.', '{\"remittance_id\":112,\"driver_id\":\"22\",\"driver_name\":\"Noel Jesus Nobleza\",\"total_collection\":\"5100\",\"total_expenses\":\"3000.00\",\"net_remittance\":\"2100.00\",\"pao_name\":\"Noel Ragua\",\"vehicle_name\":\"NKI5275\",\"remittance_date\":\"Oct 09, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-16 20:55:59', '2026-10-16 20:55:59'),
(188, 4, 'remittance_created', 'New Remittance Created', 'Kyowanie Martin created a new remittance for Oct 08, 2026 amounting to ₱2,600.00.', '{\"remittance_id\":113,\"driver_id\":\"20\",\"driver_name\":\"Kyowanie Martin\",\"total_collection\":\"1695.00\",\"total_expenses\":\"3529.00\",\"net_remittance\":\"2600.00\",\"pao_name\":\"Jorick Batiancila\",\"vehicle_name\":\"NKI5264\",\"remittance_date\":\"Oct 08, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-16 20:59:20', '2026-10-16 20:59:20'),
(189, 4, 'remittance_updated', 'Remittance Updated', 'Kyowanie Martin\'s remittance has been updated. Net amount: ₱2,600.00.', '{\"remittance_id\":113,\"driver_id\":\"20\",\"driver_name\":\"Kyowanie Martin\",\"total_collection\":\"1695.00\",\"total_expenses\":\"3529.00\",\"net_remittance\":\"2600.00\",\"pao_name\":\"Jorick Batiancila\",\"vehicle_name\":\"NKI5264\",\"remittance_date\":\"Oct 09, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-16 21:00:14', '2026-10-16 21:00:14'),
(190, 4, 'remittance_created', 'New Remittance Created', 'Ireneo Felipe created a new remittance for Oct 09, 2026 amounting to ₱3,800.00.', '{\"remittance_id\":114,\"driver_id\":\"1\",\"driver_name\":\"Ireneo Felipe\",\"total_collection\":\"6906.00\",\"total_expenses\":\"3106.00\",\"net_remittance\":\"3800.00\",\"pao_name\":\"Juan Bo\",\"vehicle_name\":\"NKI5314\",\"remittance_date\":\"Oct 09, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-16 21:01:36', '2026-10-16 21:01:36'),
(191, 4, 'remittance_created', 'New Remittance Created', 'Joseph Confesio created a new remittance for Oct 09, 2026 amounting to ₱3,300.00.', '{\"remittance_id\":115,\"driver_id\":\"11\",\"driver_name\":\"Joseph Confesio\",\"total_collection\":\"5850\",\"total_expenses\":\"2550.00\",\"net_remittance\":\"3300.00\",\"pao_name\":\"Marina Sarno\",\"vehicle_name\":\"NKI5317\",\"remittance_date\":\"Oct 09, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-16 21:04:24', '2026-10-16 21:04:24'),
(192, 4, 'remittance_created', 'New Remittance Created', 'Daniel Rustin created a new remittance for Oct 09, 2026 amounting to ₱430.00.', '{\"remittance_id\":116,\"driver_id\":\"14\",\"driver_name\":\"Daniel Rustin\",\"total_collection\":\"3080.00\",\"total_expenses\":\"2650.00\",\"net_remittance\":\"430.00\",\"pao_name\":\"Darren Lloyd Kho\",\"vehicle_name\":\"NKI5267\",\"remittance_date\":\"Oct 09, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-16 21:05:59', '2026-10-16 21:05:59'),
(193, 4, 'remittance_created', 'New Remittance Created', 'Raffy Lopez created a new remittance for Oct 09, 2026 amounting to ₱3,100.00.', '{\"remittance_id\":117,\"driver_id\":\"10\",\"driver_name\":\"Raffy Lopez\",\"total_collection\":\"6072\",\"total_expenses\":\"2972.00\",\"net_remittance\":\"3100.00\",\"pao_name\":\"Kristian Bindoy\",\"vehicle_name\":\"NKI5255\",\"remittance_date\":\"Oct 09, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-16 21:06:51', '2026-10-16 21:06:51'),
(194, 4, 'remittance_created', 'New Remittance Created', 'Delio Urbino created a new remittance for Oct 09, 2026 amounting to ₱2,100.00.', '{\"remittance_id\":118,\"driver_id\":\"4\",\"driver_name\":\"Delio Urbino\",\"total_collection\":\"6011.00\",\"total_expenses\":\"3911.00\",\"net_remittance\":\"2100\",\"pao_name\":\"Jhunelle Sarte\",\"vehicle_name\":\"NKI5307\",\"remittance_date\":\"Oct 09, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-16 21:07:39', '2026-10-16 21:07:39');
INSERT INTO `notifications` (`id`, `user_id`, `type`, `title`, `message`, `data`, `read_at`, `deleted_at`, `created_at`, `updated_at`) VALUES
(195, 4, 'remittance_created', 'New Remittance Created', 'Jay Reducto created a new remittance for Oct 09, 2026 amounting to ₱3,300.00.', '{\"remittance_id\":119,\"driver_id\":\"9\",\"driver_name\":\"Jay Reducto\",\"total_collection\":\"7083\",\"total_expenses\":\"3783.00\",\"net_remittance\":\"3300.00\",\"pao_name\":\"Ediever Guilaran\",\"vehicle_name\":\"NKI5315\",\"remittance_date\":\"Oct 09, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-16 21:09:23', '2026-10-16 21:09:23'),
(196, 4, 'remittance_created', 'New Remittance Created', 'Florencio Mondigo created a new remittance for Oct 09, 2026 amounting to ₱2,100.00.', '{\"remittance_id\":120,\"driver_id\":\"2\",\"driver_name\":\"Florencio Mondigo\",\"total_collection\":\"5574\",\"total_expenses\":\"3474.00\",\"net_remittance\":\"2100.00\",\"pao_name\":\"Ricky Carcueva\",\"vehicle_name\":\"NKI5305\",\"remittance_date\":\"Oct 09, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-16 21:10:40', '2026-10-16 21:10:40'),
(197, 4, 'remittance_created', 'New Remittance Created', 'Albert Juanillo created a new remittance for Oct 09, 2026 amounting to ₱1,850.00.', '{\"remittance_id\":121,\"driver_id\":\"8\",\"driver_name\":\"Albert Juanillo\",\"total_collection\":\"4810.00\",\"total_expenses\":\"2960.00\",\"net_remittance\":\"1850.00\",\"pao_name\":\"Kris Aldin Gayola\",\"vehicle_name\":\"NKI5316\",\"remittance_date\":\"Oct 09, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-16 21:12:09', '2026-10-16 21:12:09'),
(198, 4, 'remittance_created', 'New Remittance Created', 'Alex Estinopo created a new remittance for Oct 09, 2026 amounting to ₱2,100.00.', '{\"remittance_id\":122,\"driver_id\":\"7\",\"driver_name\":\"Alex Estinopo\",\"total_collection\":\"6132.00\",\"total_expenses\":\"4032.00\",\"net_remittance\":\"2100.00\",\"pao_name\":\"Samue Alcantara\",\"vehicle_name\":\"NKI5324\",\"remittance_date\":\"Oct 09, 2026\",\"status\":\"pending\"}', NULL, NULL, '2026-10-16 21:13:16', '2026-10-16 21:13:16');

-- --------------------------------------------------------

--
-- Table structure for table `overtime_undertimes`
--

CREATE TABLE `overtime_undertimes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
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
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `overtime_undertimes`
--

INSERT INTO `overtime_undertimes` (`id`, `user_id`, `date`, `type`, `hours`, `amount`, `hourly_rate_used`, `reason`, `status`, `rejection_reason`, `created_at`, `updated_at`, `approved_by`) VALUES
(2, 7, '2026-09-10', 'undertime', '5.50', '-550.00', '100.00', 'Auto-detected from QR scan (time in: 10:09 AM)', 'approved', NULL, '2026-09-10 17:34:18', '2026-09-11 17:14:20', 1),
(3, 8, '2026-09-10', 'undertime', '3.00', '-300.00', '100.00', 'Auto-detected from QR scan (time in: 10:22 AM)', 'approved', NULL, '2026-09-10 21:14:40', '2026-09-11 17:14:14', 1),
(4, 10, '2026-09-17', 'undertime', '1.00', '-100.00', '100.00', 'Auto-detected from manual attendance log (time in: 1:00 PM)', 'rejected', 'Not Allowed.', '2026-09-18 19:25:18', '2026-09-18 20:44:12', 3),
(5, 10, '2026-09-02', 'overtime', '1.50', '187.50', '100.00', 'Auto-detected from manual attendance log (time out: 11:30 PM)', 'approved', NULL, '2026-09-28 23:09:46', '2026-09-28 23:12:12', 3),
(6, 10, '2026-09-03', 'undertime', '8.50', '-850.00', '100.00', 'Auto-detected from manual attendance log (time in: 1:15 PM)', 'approved', NULL, '2026-09-28 23:10:44', '2026-09-28 23:11:53', 3),
(7, 10, '2026-09-04', 'overtime', '1.00', '125.00', '100.00', 'Auto-detected from manual attendance log (time out: 10:45 PM)', 'approved', NULL, '2026-09-28 23:13:51', '2026-09-28 23:23:32', 3),
(8, 10, '2026-09-05', 'overtime', '1.00', '125.00', '100.00', 'Auto-detected from manual attendance log (time out: 10:45 PM)', 'approved', NULL, '2026-09-28 23:16:29', '2026-09-28 23:23:48', 3),
(9, 10, '2026-09-06', 'undertime', '3.00', '-300.00', '100.00', 'Auto-detected from manual attendance log (time in: 9:05 AM)', 'approved', NULL, '2026-09-28 23:17:11', '2026-09-28 23:23:38', 3),
(10, 10, '2026-09-17', 'overtime', '2.00', '250.00', '100.00', 'Auto-detected from manual attendance log (time out: 11:55 PM)', 'approved', NULL, '2026-09-29 00:20:51', '2026-09-29 00:23:41', 3),
(11, 10, '2026-09-18', 'overtime', '1.00', '125.00', '100.00', 'Auto-detected from manual attendance log (time out: 10:55 PM)', 'approved', NULL, '2026-09-29 00:21:41', '2026-09-29 00:23:35', 3),
(12, 10, '2026-09-21', 'overtime', '2.00', '250.00', '100.00', 'Auto-detected from manual attendance log (time out: 11:55 PM)', 'approved', NULL, '2026-09-29 00:22:33', '2026-09-29 00:23:30', 3),
(13, 10, '2026-09-23', 'overtime', '2.00', '250.00', '100.00', 'Auto-detected from manual attendance log (time out: 11:45 PM)', 'approved', NULL, '2026-09-29 00:23:11', '2026-09-29 00:23:24', 3),
(14, 10, '2026-08-18', 'overtime', '2.00', '250.00', '100.00', 'Auto-detected from manual attendance log (time out: 11:55 PM)', 'approved', NULL, '2026-09-29 00:29:52', '2026-09-29 00:31:38', 3),
(15, 10, '2026-08-20', 'overtime', '2.00', '250.00', '100.00', 'Auto-detected from manual attendance log (time out: 11:45 PM)', 'approved', NULL, '2026-09-29 00:30:28', '2026-09-29 00:31:27', 3),
(16, 10, '2026-08-27', 'overtime', '2.00', '250.00', '100.00', 'Auto-detected from manual attendance log (time out: 11:59 PM)', 'approved', NULL, '2026-09-29 00:31:06', '2026-09-29 00:31:22', 3),
(17, 3, '2026-09-16', 'overtime', '1.00', '781.25', '625.00', 'Auto-detected from manual attendance log (time out: 6:00 PM)', 'approved', NULL, '2026-10-01 23:32:30', '2026-10-01 23:35:31', 3),
(18, 3, '2026-09-18', 'overtime', '1.00', '781.25', '625.00', 'Auto-detected from manual attendance log (time out: 5:45 PM)', 'approved', NULL, '2026-10-01 23:33:18', '2026-10-01 23:35:25', 3),
(19, 3, '2026-10-19', 'overtime', '2.00', '1562.50', '625.00', 'Auto-detected from manual attendance log (time out: 6:45 PM)', 'approved', NULL, '2026-10-01 23:33:53', '2026-10-01 23:35:15', 3),
(20, 3, '2026-09-30', 'overtime', '3.00', '2343.75', '625.00', 'Auto-detected from manual attendance log (time out: 7:49 PM)', 'approved', NULL, '2026-10-01 23:34:44', '2026-10-01 23:35:10', 3);

-- --------------------------------------------------------

--
-- Table structure for table `paos`
--

CREATE TABLE `paos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `contact_number` varchar(255) NOT NULL,
  `gender` enum('male','female','prefer_not_to_say') DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `date_of_hire` date DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `paos`
--

INSERT INTO `paos` (`id`, `name`, `contact_number`, `gender`, `email`, `address`, `date_of_hire`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Juan Bo', '09318973202', 'male', 'juanbo@gmail.com', 'Concepcion Marikina City', '2025-09-01', 'active', '2026-09-17 01:13:08', '2026-09-30 19:58:05'),
(2, 'Raymart Dizon', '09182468761', NULL, NULL, NULL, NULL, 'active', '2026-09-30 19:56:19', '2026-09-30 19:56:19'),
(3, 'Elco Monforte', '09654813546', NULL, NULL, NULL, NULL, 'active', '2026-09-30 19:57:47', '2026-09-30 19:57:47'),
(4, 'Bertmon Reyes', '09256484354', NULL, NULL, NULL, NULL, 'active', '2026-09-30 19:58:39', '2026-09-30 19:58:39'),
(5, 'Noel Ragua', '09111111111', NULL, NULL, NULL, NULL, 'active', '2026-09-30 20:00:15', '2026-09-30 20:00:15'),
(6, 'John Joseph Barcelon', '09185623547', NULL, NULL, NULL, NULL, 'active', '2026-09-30 20:01:07', '2026-09-30 20:01:07'),
(7, 'Samue Alcantara', '09584321654', NULL, NULL, NULL, NULL, 'active', '2026-09-30 20:02:20', '2026-09-30 20:02:20'),
(8, 'Kris Aldin Gayola', '09845621564', NULL, NULL, NULL, NULL, 'active', '2026-09-30 20:02:37', '2026-09-30 20:02:37'),
(9, 'Ediever Guilaran', '09548976213', NULL, NULL, NULL, NULL, 'active', '2026-09-30 20:03:56', '2026-09-30 20:03:56'),
(10, 'Kristian Bindoy', '09256498731', NULL, NULL, NULL, NULL, 'active', '2026-09-30 20:04:33', '2026-09-30 20:04:33'),
(11, 'Marina Sarno', '09321654898', NULL, NULL, NULL, NULL, 'active', '2026-10-01 00:54:29', '2026-10-01 00:54:29'),
(12, 'Myrna Mayuga', '09321654789', NULL, NULL, NULL, NULL, 'active', '2026-10-01 18:15:09', '2026-10-01 18:15:09'),
(13, 'Michael Tiamson', '09366589757', NULL, NULL, NULL, NULL, 'active', '2026-10-01 18:29:41', '2026-10-01 18:29:41'),
(14, 'Carl James Obeña', '09513565749', NULL, NULL, NULL, NULL, 'active', '2026-10-01 18:33:48', '2026-10-01 18:33:48'),
(15, 'Mary Grace Atoli', '09456897651', NULL, NULL, NULL, NULL, 'active', '2026-10-01 18:42:49', '2026-10-01 18:42:49'),
(16, 'Darren Lloyd Kho', '09189876532', NULL, NULL, NULL, NULL, 'active', '2026-10-01 19:01:12', '2026-10-01 19:01:12'),
(17, 'Lenie Nunez', '09316879463', NULL, NULL, NULL, NULL, 'active', '2026-10-01 19:07:22', '2026-10-01 19:07:22'),
(18, 'Jorick Batiancila', '09598987416', NULL, NULL, NULL, NULL, 'active', '2026-10-01 19:08:55', '2026-10-01 19:08:55'),
(19, 'Eloisa Meterio', '09569879543', NULL, NULL, NULL, NULL, 'active', '2026-10-01 19:09:58', '2026-10-01 19:09:58'),
(20, 'Joel Divina', '09565468971', NULL, NULL, NULL, NULL, 'active', '2026-10-13 17:57:02', '2026-10-13 17:57:02'),
(21, 'Ricky Carcueva', '09088796531', NULL, NULL, NULL, NULL, 'active', '2026-10-13 18:00:21', '2026-10-13 18:00:21'),
(22, 'Jhunelle Sarte', '09564987651', NULL, NULL, NULL, NULL, 'active', '2026-10-13 18:43:13', '2026-10-13 18:43:13');

-- --------------------------------------------------------

--
-- Table structure for table `payrolls`
--

CREATE TABLE `payrolls` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `batch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
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
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
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
  `employer_philhealth` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payrolls`
--

INSERT INTO `payrolls` (`id`, `batch_id`, `user_id`, `payroll_period_start`, `payroll_period_end`, `total_allowances`, `total_bonuses`, `total_deductions`, `cash_advance_deduction`, `salary_loan_deduction`, `loan_deduction_data`, `status`, `basic_salary`, `gross_pay`, `holiday_pay`, `holiday_ot_pay`, `holiday_ot_hours`, `holiday_breakdown`, `net_pay`, `days_worked`, `hours_worked`, `payment_date`, `approved_by`, `created_at`, `updated_at`, `present_days`, `late_days`, `absent_days`, `daily_rate_snapshot`, `sss`, `pagibig`, `philhealth`, `withholding_tax`) VALUES
(1, 1, 7, '2026-09-01', '2026-09-15', '0.00', '0.00', '550.00', '0.00', '0.00', '{\"cash_advances\":[]}', 'pending', '800.00', '800.00', '0.00', '0.00', '0.00', '[]', '250.00', '1.00', '0.41', NULL, NULL, '2026-09-18 17:18:50', '2026-09-18 17:18:50', 0, 0, 0, '0.00', '0.00', '0.00', '0.00', '0.00'),
(2, 1, 8, '2026-09-01', '2026-09-15', '0.00', '0.00', '300.00', '0.00', '0.00', '{\"cash_advances\":[]}', 'pending', '800.00', '800.00', '0.00', '0.00', '0.00', '[]', '500.00', '1.00', '2.87', NULL, NULL, '2026-09-18 17:18:50', '2026-09-18 17:18:50', 0, 0, 0, '0.00', '0.00', '0.00', '0.00', '0.00'),
(3, 1, 9, '2026-09-01', '2026-09-15', '0.00', '0.00', '0.00', '0.00', '0.00', '{\"cash_advances\":[]}', 'pending', '800.00', '800.00', '0.00', '0.00', '0.00', '[]', '800.00', '1.00', '0.00', NULL, NULL, '2026-09-18 17:18:50', '2026-09-18 17:18:50', 0, 0, 0, '0.00', '0.00', '0.00', '0.00', '0.00'),
(5, 3, 10, '2026-08-16', '2026-08-31', '750.00', '0.00', '0.00', '0.00', '0.00', '{\"cash_advances\":[]}', 'pending', '2400.00', '3950.00', '800.00', '0.00', '0.00', '[{\"label\":\"(Regular) Holiday Pay - unworked\",\"amount\":800}]', '3950.00', '3.00', '36.62', NULL, NULL, '2026-10-01 23:28:28', '2026-10-01 23:28:28', 0, 0, 0, '0.00', '0.00', '0.00', '0.00', '0.00'),
(6, 2, 3, '2026-09-16', '2026-09-30', '3906.25', '0.00', '2225.00', '0.00', '0.00', '{\"cash_advances\":[]}', 'pending', '15000.00', '18906.25', '0.00', '0.00', '0.00', '[]', '16681.25', '3.00', '28.75', NULL, NULL, '2026-10-01 23:37:04', '2026-10-01 23:37:04', 0, 0, 0, '0.00', '875.00', '100.00', '1250.00', '0.00'),
(7, 1, 10, '2026-09-01', '2026-09-15', '437.50', '0.00', '1718.34', '0.00', '0.00', '{\"cash_advances\":[]}', 'pending', '8000.00', '8437.50', '0.00', '0.00', '0.00', '[]', '6719.16', '10.00', '92.27', NULL, NULL, '2026-10-10 23:23:53', '2026-10-10 23:23:53', 0, 0, 0, '0.00', '0.00', '0.00', '0.00', '0.00');

-- --------------------------------------------------------

--
-- Table structure for table `payroll_allowances`
--

CREATE TABLE `payroll_allowances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payroll_id` bigint(20) UNSIGNED NOT NULL,
  `allowance_type` varchar(255) NOT NULL,
  `hours` decimal(8,2) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payroll_allowances`
--

INSERT INTO `payroll_allowances` (`id`, `payroll_id`, `allowance_type`, `hours`, `amount`, `created_at`, `updated_at`) VALUES
(1, 5, 'Overtime Pay', '6.00', '750.00', '2026-10-01 23:28:28', '2026-10-01 23:28:28'),
(2, 6, 'Overtime Pay', '5.00', '3906.25', '2026-10-01 23:37:04', '2026-10-01 23:37:04'),
(3, 7, 'Overtime Pay', '3.50', '437.50', '2026-10-10 23:23:53', '2026-10-10 23:23:53');

-- --------------------------------------------------------

--
-- Table structure for table `payroll_batches`
--

CREATE TABLE `payroll_batches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'regular',
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `status` enum('draft','submitted','approved','rejected') NOT NULL DEFAULT 'draft',
  `generated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `finalized_by` bigint(20) UNSIGNED DEFAULT NULL,
  `finalized_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejected_by` bigint(20) UNSIGNED DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `rejection_note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payroll_batches`
--

INSERT INTO `payroll_batches` (`id`, `type`, `period_start`, `period_end`, `status`, `generated_by`, `finalized_by`, `finalized_at`, `approved_by`, `approved_at`, `rejected_by`, `rejected_at`, `rejection_note`, `created_at`, `updated_at`) VALUES
(1, 'regular', '2026-09-01', '2026-09-15', 'draft', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-18 17:18:21', '2026-09-18 17:18:21'),
(2, 'regular', '2026-09-16', '2026-09-30', 'draft', 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-29 00:27:36', '2026-09-29 00:27:36'),
(3, 'regular', '2026-08-16', '2026-08-31', 'draft', 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-29 00:32:29', '2026-09-29 00:32:29');

-- --------------------------------------------------------

--
-- Table structure for table `payroll_bonuses`
--

CREATE TABLE `payroll_bonuses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payroll_id` bigint(20) UNSIGNED NOT NULL,
  `bonus_type` enum('performance','holiday','attendance','special') NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payroll_cutoff_schedules`
--

CREATE TABLE `payroll_cutoff_schedules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cutoff_day` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `payroll_period_start` date DEFAULT NULL,
  `payroll_period_end` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payroll_cutoff_schedules`
--

INSERT INTO `payroll_cutoff_schedules` (`id`, `cutoff_day`, `label`, `payroll_period_start`, `payroll_period_end`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 15, '1st Half', NULL, NULL, 1, '2026-09-03 06:01:03', '2026-09-29 00:27:03'),
(2, 31, '2nd Half', NULL, NULL, 0, '2026-09-03 06:01:03', '2026-09-29 00:27:03');

-- --------------------------------------------------------

--
-- Table structure for table `payroll_deductions`
--

CREATE TABLE `payroll_deductions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payroll_id` bigint(20) UNSIGNED NOT NULL,
  `deduction_type` varchar(255) NOT NULL,
  `hours` decimal(8,2) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `accounting_treatment` varchar(30) DEFAULT NULL,
  `deduction_source` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payroll_deductions`
--

INSERT INTO `payroll_deductions` (`id`, `payroll_id`, `deduction_type`, `hours`, `amount`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 'Undertime Deduction', '5.50', '550.00', NULL, '2026-09-18 17:18:50', '2026-09-18 17:18:50'),
(2, 2, 'Undertime Deduction', '3.00', '300.00', NULL, '2026-09-18 17:18:50', '2026-09-18 17:18:50'),
(3, 6, 'SSS', NULL, '875.00', NULL, '2026-10-01 23:37:04', '2026-10-01 23:37:04'),
(4, 6, 'Pag-IBIG', NULL, '100.00', NULL, '2026-10-01 23:37:04', '2026-10-01 23:37:04'),
(5, 6, 'PhilHealth', NULL, '1250.00', NULL, '2026-10-01 23:37:04', '2026-10-01 23:37:04'),
(6, 7, 'Late Deduction', '5.68', '568.34', '341 minutes late', '2026-10-10 23:23:53', '2026-10-10 23:23:53'),
(7, 7, 'Undertime Deduction', '11.50', '1150.00', NULL, '2026-10-10 23:23:53', '2026-10-10 23:23:53');

-- --------------------------------------------------------

--
-- Table structure for table `pinned_items`
--

CREATE TABLE `pinned_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `label` varchar(255) NOT NULL,
  `url` varchar(500) NOT NULL,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `position_rates`
--

CREATE TABLE `position_rates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `daily_rate` decimal(10,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `position_rates`
--

INSERT INTO `position_rates` (`id`, `department_id`, `name`, `daily_rate`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'HR', '5000.00', 1, '2026-09-03 22:20:10', '2026-09-03 22:20:10'),
(2, 3, 'Accounting', '1000.00', 1, '2026-09-03 22:28:25', '2026-09-03 22:28:25'),
(3, 2, 'Admin', '800.00', 1, '2026-09-03 22:28:40', '2026-09-03 22:28:40'),
(4, NULL, 'Driver', '750.00', 1, '2026-09-03 22:29:00', '2026-09-03 22:29:00'),
(5, NULL, 'Pao', '650.00', 1, '2026-09-03 22:29:11', '2026-09-03 22:29:11');

-- --------------------------------------------------------

--
-- Table structure for table `routes`
--

CREATE TABLE `routes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `route_name` varchar(255) NOT NULL,
  `weekday_boundary` decimal(10,2) NOT NULL DEFAULT 0.00,
  `weekend_boundary` decimal(10,2) NOT NULL DEFAULT 0.00,
  `origin` varchar(255) NOT NULL,
  `destination` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `routes`
--

INSERT INTO `routes` (`id`, `route_name`, `weekday_boundary`, `weekend_boundary`, `origin`, `destination`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Marikina - Cubao', '3300.00', '3300.00', 'Marikina', 'Cubao', 'active', '2026-09-10 18:07:56', '2026-09-30 20:53:28'),
(2, 'SSS Village - Cubao', '2100.00', '2100.00', 'SSS Village', 'Cubao', 'active', '2026-09-10 18:08:19', '2026-09-30 20:53:43'),
(3, 'Parang - Cubao', '3300.00', '3300.00', 'Parang', 'Cubao', 'active', '2026-09-10 18:08:56', '2026-09-30 20:53:57'),
(4, 'Montalban - Cubao', '3100.00', '3100.00', 'Montalban', 'Cubao', 'active', '2026-09-10 18:09:27', '2026-09-30 20:54:11'),
(5, 'Pasig - Marikina', '2100.00', '2100.00', 'Pasig', 'Marikina', 'active', '2026-09-10 18:09:54', '2026-09-30 20:54:23'),
(6, 'Marikina - Montalban', '2100.00', '2100.00', 'Marikina', 'Montalban', 'active', '2026-09-10 18:10:48', '2026-09-30 20:54:41'),
(7, 'SSS Village - Cubao (Daily Rate)', '1.00', '1.00', 'SSS Village', 'Cubao (Daily Rate)', 'active', '2026-10-01 00:45:28', '2026-10-01 00:45:28'),
(8, 'Trouble - Unit', '0.00', '0.00', 'Trouble', 'Unit', 'active', '2026-10-01 18:54:01', '2026-10-01 18:54:01'),
(9, 'Marikina - Montalban (Weekend)', '1600.00', '1600.00', 'Marikina', 'Montalban (Weekend)', 'active', '2026-10-13 19:14:25', '2026-10-13 19:14:41');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('BVszOvryYtwbxhfi8P3HaqQ4PUWgO14TlBZ3QCEF', 1, '112.210.227.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiTmw3RDFLenFhVVhHMTZrblp5aEVCSXlnMzlrVm5aOTlXZXhwV0NlZSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTY6Imh0dHBzOi8vdGVzdC1zbWFydGFjYy12My5mcmVlZGV2LmFwcC9ub3RpZmljYXRpb25zL2NvdW50IjtzOjU6InJvdXRlIjtzOjE5OiJub3RpZmljYXRpb25zLmNvdW50Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjEyOiJ2aWV3X2FzX3JvbGUiO3M6MTY6InJlbWl0dGFuY2VfY2xlcmsiO30=', 1792427568);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shifts`
--

CREATE TABLE `shifts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `break_start` time DEFAULT NULL,
  `break_end` time DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shifts`
--

INSERT INTO `shifts` (`id`, `name`, `start_time`, `end_time`, `break_start`, `break_end`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Office Hours', '08:00:00', '17:00:00', '12:00:00', '13:00:00', 1, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(2, 'Operation', '05:00:00', '15:00:00', '11:00:00', '12:00:00', 1, '2026-09-03 22:34:51', '2026-09-18 17:24:22'),
(3, 'Evening Dispatch', '13:00:00', '22:00:00', '18:00:00', '19:00:00', 1, '2026-09-03 22:36:19', '2026-09-18 19:27:09'),
(4, 'Evening Shift', '14:00:00', '23:00:00', '18:00:00', '19:00:00', 1, '2026-09-18 17:22:45', '2026-09-18 17:22:45'),
(5, 'Morning Dispatch', '04:00:00', '13:00:00', '12:00:00', '13:00:00', 1, '2026-09-18 17:26:40', '2026-09-18 17:26:40'),
(6, 'Office Shift', '09:00:00', '18:00:00', '12:00:00', '13:00:00', 1, '2026-09-18 20:58:30', '2026-09-18 20:58:30');

-- --------------------------------------------------------

--
-- Table structure for table `statutory_deductions`
--

CREATE TABLE `statutory_deductions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `min_salary` decimal(12,2) DEFAULT NULL,
  `max_salary` decimal(12,2) DEFAULT NULL,
  `employee_share` decimal(12,2) DEFAULT NULL,
  `employer_share` decimal(12,2) DEFAULT NULL,
  `percentage_employee` decimal(5,2) DEFAULT NULL,
  `percentage_employer` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `statutory_deductions`
--

INSERT INTO `statutory_deductions` (`id`, `name`, `min_salary`, `max_salary`, `employee_share`, `employer_share`, `percentage_employee`, `percentage_employer`, `created_at`, `updated_at`) VALUES
(1, 'SSS', '0.00', '5249.99', '250.00', '510.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(2, 'SSS', '5250.00', '5749.99', '275.00', '560.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(3, 'SSS', '5750.00', '6249.99', '300.00', '610.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(4, 'SSS', '6250.00', '6749.99', '325.00', '660.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(5, 'SSS', '6750.00', '7249.99', '350.00', '710.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(6, 'SSS', '7250.00', '7749.99', '375.00', '760.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(7, 'SSS', '7750.00', '8249.99', '400.00', '810.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(8, 'SSS', '8250.00', '8749.99', '425.00', '860.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(9, 'SSS', '8750.00', '9249.99', '450.00', '910.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(10, 'SSS', '9250.00', '9749.99', '475.00', '960.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(11, 'SSS', '9750.00', '10249.99', '500.00', '1010.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(12, 'SSS', '10250.00', '10749.99', '525.00', '1060.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(13, 'SSS', '10750.00', '11249.99', '550.00', '1110.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(14, 'SSS', '11250.00', '11749.99', '575.00', '1160.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(15, 'SSS', '11750.00', '12249.99', '600.00', '1210.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(16, 'SSS', '12250.00', '12749.99', '625.00', '1260.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(17, 'SSS', '12750.00', '13249.99', '650.00', '1310.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(18, 'SSS', '13250.00', '13749.99', '675.00', '1360.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(19, 'SSS', '13750.00', '14249.99', '700.00', '1410.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(20, 'SSS', '14250.00', '14749.99', '725.00', '1460.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(21, 'SSS', '14750.00', '15249.99', '750.00', '1530.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(22, 'SSS', '15250.00', '15749.99', '775.00', '1580.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(23, 'SSS', '15750.00', '16249.99', '800.00', '1630.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(24, 'SSS', '16250.00', '16749.99', '825.00', '1680.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(25, 'SSS', '16750.00', '17249.99', '850.00', '1730.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(26, 'SSS', '17250.00', '17749.99', '875.00', '1780.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(27, 'SSS', '17750.00', '18249.99', '900.00', '1830.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(28, 'SSS', '18250.00', '18749.99', '925.00', '1880.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(29, 'SSS', '18750.00', '19249.99', '950.00', '1930.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(30, 'SSS', '19250.00', '19749.99', '975.00', '1980.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(31, 'SSS', '19750.00', '20249.99', '1000.00', '2030.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(32, 'SSS', '20250.00', '20749.99', '1025.00', '2080.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(33, 'SSS', '20750.00', '21249.99', '1050.00', '2130.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(34, 'SSS', '21250.00', '21749.99', '1075.00', '2180.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(35, 'SSS', '21750.00', '22249.99', '1100.00', '2230.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(36, 'SSS', '22250.00', '22749.99', '1125.00', '2280.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(37, 'SSS', '22750.00', '23249.99', '1150.00', '2330.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(38, 'SSS', '23250.00', '23749.99', '1175.00', '2380.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(39, 'SSS', '23750.00', '24249.99', '1200.00', '2430.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(40, 'SSS', '24250.00', '24749.99', '1225.00', '2480.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(41, 'SSS', '24750.00', '25249.99', '1250.00', '2530.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(42, 'SSS', '25250.00', '25749.99', '1275.00', '2580.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(43, 'SSS', '25750.00', '26249.99', '1300.00', '2630.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(44, 'SSS', '26250.00', '26749.99', '1325.00', '2680.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(45, 'SSS', '26750.00', '27249.99', '1350.00', '2730.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(46, 'SSS', '27250.00', '27749.99', '1375.00', '2780.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(47, 'SSS', '27750.00', '28249.99', '1400.00', '2830.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(48, 'SSS', '28250.00', '28749.99', '1425.00', '2880.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(49, 'SSS', '28750.00', '29249.99', '1450.00', '2930.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(50, 'SSS', '29250.00', '29749.99', '1475.00', '2980.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(51, 'SSS', '29750.00', '30249.99', '1500.00', '3030.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(52, 'SSS', '30250.00', '30749.99', '1525.00', '3080.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(53, 'SSS', '30750.00', '31249.99', '1550.00', '3130.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(54, 'SSS', '31250.00', '31749.99', '1575.00', '3180.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(55, 'SSS', '31750.00', '32249.99', '1600.00', '3230.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(56, 'SSS', '32250.00', '32749.99', '1625.00', '3280.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(57, 'SSS', '32750.00', '33249.99', '1650.00', '3330.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(58, 'SSS', '33250.00', '33749.99', '1675.00', '3380.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(59, 'SSS', '33750.00', '34249.99', '1700.00', '3430.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(60, 'SSS', '34250.00', '34749.99', '1725.00', '3480.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(61, 'SSS', '34750.00', '999999999.00', '1750.00', '3530.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(62, 'Pag-IBIG', '0.00', '1500.00', NULL, NULL, '1.00', '2.00', '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(63, 'Pag-IBIG', '1500.01', '999999999.00', NULL, NULL, '2.00', '2.00', '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(64, 'PhilHealth', '0.00', '10000.00', '250.00', '250.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(65, 'PhilHealth', '10000.01', '99999.99', NULL, NULL, '2.50', '2.50', '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(66, 'PhilHealth', '100000.00', '999999999.00', '2500.00', '2500.00', NULL, NULL, '2026-09-03 06:01:03', '2026-09-03 06:01:03');

-- --------------------------------------------------------

--
-- Table structure for table `thirteenth_month_pays`
--

CREATE TABLE `thirteenth_month_pays` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `batch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `calendar_year` smallint(5) UNSIGNED NOT NULL,
  `total_basic_salary_earned` decimal(14,2) NOT NULL DEFAULT 0.00,
  `thirteenth_month_pay` decimal(14,2) NOT NULL DEFAULT 0.00,
  `months_worked` decimal(5,2) NOT NULL DEFAULT 0.00,
  `is_eligible` tinyint(1) NOT NULL DEFAULT 1,
  `amount_paid` decimal(14,2) NOT NULL DEFAULT 0.00,
  `amount_remaining` decimal(14,2) NOT NULL DEFAULT 0.00,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `payment_date` date DEFAULT NULL,
  `paid_by` bigint(20) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `computed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `computed_at` timestamp NULL DEFAULT NULL,
  `computation_breakdown` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
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
  `shift_id` bigint(20) UNSIGNED DEFAULT NULL,
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
  `last_login_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `password`, `password_changed`, `role`, `profile_picture`, `remember_token`, `is_logged_in`, `first_name`, `middle_name`, `last_name`, `email`, `phone`, `address`, `civil_status`, `gender`, `spouse_name`, `date_of_birth`, `place_of_birth`, `educational_attainment`, `driver_license_number`, `driver_license_validity`, `date_of_hire`, `position`, `shift_id`, `department`, `status`, `salary_rate`, `work_days_per_week`, `bank_name`, `bank_account_number`, `schedule_type`, `has_sss`, `sss_number`, `tin_number`, `has_tin`, `pagibig_number`, `philhealth_number`, `has_pagibig`, `has_philhealth`, `signature_path`, `attachments`, `created_at`, `updated_at`, `last_login_at`) VALUES
(1, 'Super Admin', 'super_admin', '$2y$12$YhTTF5BRZfCgAA9.G1RFyObHHru/l1suzFQ2cXZvWvYYze66mQ/hS', 0, 'superadmin', NULL, 'kGJJIedNYMXz7nGhWkPdQEOSuai3FNzSX01mr5WBaTNQh7mxQn0d8Ja7BSae', 1, 'Super', NULL, 'Admin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active', '0.00', 5, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, 0, 0, NULL, NULL, '2026-09-03 06:01:03', '2026-10-20 06:39:14', '2026-10-20 06:39:14'),
(2, 'QR Attendance Admin', 'qr_admin', '$2y$12$YhTTF5BRZfCgAA9.G1RFyObHHru/l1suzFQ2cXZvWvYYze66mQ/hS', 0, 'qr_admin', NULL, 'xaJY2iNiK6cNR2klK1Q3FZ60SnvLDu4BcS4L72hlxIDvb4sS7Mh6IJqt0Spy', 1, 'QR', NULL, 'Admin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active', '0.00', 5, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, 0, 0, NULL, NULL, '2026-09-03 06:01:03', '2026-09-11 16:39:40', '2026-09-11 16:39:40'),
(3, 'Maria Carmel Galino Wacay', 'melwacay', '$2y$12$Hl/oT6oNl4sHMccJeHkOR.4cXfgXumCUOr15ecsjwgSHSWeAnEIgW', 0, 'hr', NULL, 'PR0s7LPINDEXQiHJWJEym6NwKNvqLuYdGU0xXJkUbieZHfsS2tEqaVwFB5zq', 1, 'Maria Carmel', 'Galino', 'Wacay', 'maria.wacay@gmail.com', '09959221488', '{\"street\":\"549-C Dir A Bunye Street Lakeside III Purok 3\",\"barangay\":\"Sucat\",\"city\":\"Muntinlupa City\",\"province\":\"National Capital Region\"}', 'Single', 'female', NULL, '1970-11-10', 'Anini-y Antique', 'College (Bachelor\'s)', NULL, NULL, '2022-10-04', 'HR', 1, 'HR', 'active', '5000.00', 6, 'BPI', NULL, NULL, 1, '07-3105391-5', '216-204-486', 1, '1010-0108-2645', NULL, 1, 1, NULL, NULL, '2026-09-03 22:22:13', '2026-09-18 20:52:22', '2026-09-18 18:21:27'),
(4, 'beth boongaling', 'beth.boongaling', '$2y$12$zTkN4TdQhMw5bR9y1sDhMeSNiUAtBSWNqvcTQcI9jKuThIxetS7K2', 0, 'accountant', NULL, NULL, 1, 'beth', NULL, 'boongaling', 'elizabethboongaling04@gmail.com', '09201213683', '{\"street\":\"blk 6 lot 6 Millenium homes\",\"barangay\":\"burgos\",\"city\":\"rodriguez\",\"province\":\"rizal\"}', 'Married', 'female', NULL, '1986-04-04', 'San Mateo, Rizal', 'College (Bachelor\'s)', NULL, NULL, '2019-09-23', 'Accounting', 1, 'Accounting', 'active', '1000.00', 5, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, 0, 0, NULL, NULL, '2026-09-03 22:33:32', '2026-10-01 18:23:23', '2026-10-01 18:23:23'),
(7, 'Mary Grace Manduriao', 'marygrace.mandurioa', '$2y$12$rGs5XGdVUa6CnOVSQnBaFuhTDcSSsvSqRQPAmQckKtbAd77P7HZ86', 0, 'employee', NULL, NULL, 1, 'Mary Grace', NULL, 'Manduriao', 'marygracemanduriao@gmail.com', '09368766406', '{\"street\":\"Briget, Torres Comp\",\"barangay\":\"Ampid 1\",\"city\":\"San Mateo\",\"province\":\"Rizal\"}', 'Married', 'female', NULL, '1986-04-17', 'San Mateo, Rizal', 'College (Bachelor\'s)', NULL, NULL, '2023-09-01', 'Admin', NULL, 'Admin', 'active', '800.00', 5, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, 0, 0, NULL, NULL, '2026-09-10 16:57:20', '2026-09-10 17:07:54', '2026-09-10 17:07:54'),
(8, 'Joshua  Boongaling', 'joshua.boongaling', '$2y$12$.dYgOQmjLiistQRmB87ZburlUWXwD5sfYdo79deYCUDFO4P6T3nSC', 0, 'employee', NULL, NULL, 1, 'Joshua', NULL, 'Boongaling', NULL, '09054857450', '{\"street\":\"blk 6 lot 6 Millenium homes\",\"barangay\":\"burgos\",\"city\":\"rodriguez\",\"province\":\"Rizal\"}', 'Single', 'male', NULL, '1999-06-22', 'Quezon City', 'College (Bachelor\'s)', NULL, NULL, '2021-07-11', 'Admin', NULL, 'Admin', 'active', '800.00', 5, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, 0, 0, NULL, NULL, '2026-09-10 17:20:22', '2026-09-17 19:20:20', '2026-09-17 19:20:20'),
(9, 'Manny  Dela Cruz', 'manny.delacruz', '$2y$12$EphI47jSJ7wE7QcQ3yueYeiBKUuEouqy1Nz6k4cj3hrPoKv2SDr/a', 0, 'employee', NULL, NULL, 1, 'Manny', NULL, 'Dela Cruz', NULL, '09617131703', '{\"street\":\"blk 1 lot 16 Millenium homes\",\"barangay\":\"Burgos\",\"city\":\"Rodriguez\",\"province\":\"Rizal\"}', 'Married', 'male', NULL, '1978-06-12', 'Pangasinan', 'College (Bachelor\'s)', NULL, NULL, '2024-01-01', 'Admin', NULL, 'Admin', 'active', '800.00', 5, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, 0, 0, NULL, NULL, '2026-09-10 17:42:18', '2026-09-10 20:49:33', '2026-09-10 20:49:33'),
(10, 'Ronolfo  Alcanse', 'ronolfo.alcanse', '$2y$12$ZGHVYMJF2m.v7cA5eKVBjOXJiVbPb95TfwFXtXwiyOryIkMy4s2zy', 0, 'employee', NULL, NULL, 1, 'Ronolfo', NULL, 'Alcanse', 'ronniealcanse1977@gmail.com', '09958630020', '{\"street\":\"Blk 2 Lot 1 Millenium Homes\",\"barangay\":\"Burgos\",\"city\":\"Rodriguez\",\"province\":\"Rizal\"}', 'Married', 'male', NULL, '1978-08-10', 'Cavite', 'College (Bachelor\'s)', NULL, NULL, '2024-01-01', 'Admin', 3, 'Admin', 'active', '800.00', 5, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, 0, 0, NULL, NULL, '2026-09-10 17:57:04', '2026-09-18 18:52:24', '2026-09-10 21:09:15'),
(11, 'Kimberly Magayones Ramos', 'kimberly.ramos', '$2y$12$.oX9TY3OAVPPNDm4MmtSpueento.rir/CmaeznK2THZkRpDRuJgt2', 0, 'employee', NULL, NULL, 0, 'Kimberly', 'Magayones', 'Ramos', 'khimr@gmail.com', '09668025877', '{\"street\":\"0490 Area 6\",\"barangay\":\"Botocan\",\"city\":\"Quezon City\",\"province\":\"National Capital Region\"}', 'Single', 'female', NULL, '1996-04-30', 'Quezon City', 'Vocational / TESDA', NULL, NULL, '2026-09-15', 'Admin', 4, 'Admin', 'active', '800.00', 5, NULL, NULL, NULL, 1, '35-1100417-8', '611-600-080', 1, '1212-8870-1922', NULL, 1, 0, NULL, NULL, '2026-09-18 19:22:51', '2026-10-13 17:22:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_activities`
--

CREATE TABLE `user_activities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `action` varchar(255) NOT NULL,
  `subject_type` varchar(255) DEFAULT NULL,
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL,
  `subject_label` varchar(255) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_activities`
--

INSERT INTO `user_activities` (`id`, `user_id`, `action`, `subject_type`, `subject_id`, `subject_label`, `url`, `created_at`, `updated_at`) VALUES
(146, 2, 'viewed', NULL, NULL, 'dashboard', 'https://test-smartacc-v3.freedev.app/admin/qr-monitor', '2026-09-03 23:00:45', '2026-09-03 23:00:45'),
(147, 2, 'viewed', NULL, NULL, 'dashboard', 'https://test-smartacc-v3.freedev.app/admin/qr-monitor', '2026-09-03 23:00:46', '2026-09-03 23:00:46'),
(272, 8, 'viewed', NULL, NULL, 'Attendance', 'https://test-smartacc-v3.freedev.app/my/attendance', '2026-09-10 17:24:13', '2026-09-10 17:24:13'),
(273, 8, 'viewed', NULL, NULL, 'Overtime/Undertime', 'https://test-smartacc-v3.freedev.app/my/overtime-undertime', '2026-09-10 17:25:11', '2026-09-10 17:25:11'),
(274, 8, 'viewed', NULL, NULL, 'Overtime/Undertime', 'https://test-smartacc-v3.freedev.app/my/overtime-undertime', '2026-09-10 17:25:14', '2026-09-10 17:25:14'),
(275, 8, 'viewed', NULL, NULL, 'Attendance', 'https://test-smartacc-v3.freedev.app/my/attendance', '2026-09-10 17:25:16', '2026-09-10 17:25:16'),
(276, 8, 'viewed', NULL, NULL, 'Attendance', 'https://test-smartacc-v3.freedev.app/my/attendance', '2026-09-10 17:25:19', '2026-09-10 17:25:19'),
(277, 8, 'viewed', NULL, NULL, 'Overtime/Undertime', 'https://test-smartacc-v3.freedev.app/my/overtime-undertime', '2026-09-10 17:25:22', '2026-09-10 17:25:22'),
(278, 8, 'viewed', NULL, NULL, 'Overtime/Undertime', 'https://test-smartacc-v3.freedev.app/my/overtime-undertime', '2026-09-10 17:25:24', '2026-09-10 17:25:24'),
(283, 7, 'viewed', NULL, NULL, 'Attendance', 'https://test-smartacc-v3.freedev.app/my/attendance', '2026-09-10 17:35:36', '2026-09-10 17:35:36'),
(284, 7, 'viewed', NULL, NULL, 'Attendance', 'https://test-smartacc-v3.freedev.app/my/attendance', '2026-09-10 17:35:55', '2026-09-10 17:35:55'),
(285, 7, 'viewed', NULL, NULL, 'Attendance', 'https://test-smartacc-v3.freedev.app/my/attendance', '2026-09-10 17:36:09', '2026-09-10 17:36:09'),
(381, 2, 'viewed', NULL, NULL, 'dashboard', 'https://test-smartacc-v3.freedev.app/admin/qr-monitor', '2026-09-10 20:51:59', '2026-09-10 20:51:59'),
(382, 9, 'viewed', NULL, NULL, 'Attendance', 'https://test-smartacc-v3.freedev.app/my/attendance', '2026-09-10 20:53:02', '2026-09-10 20:53:02'),
(383, 9, 'viewed', NULL, NULL, 'Cash Advance', 'https://test-smartacc-v3.freedev.app/my/cash-advances', '2026-09-10 20:54:08', '2026-09-10 20:54:08'),
(384, 2, 'viewed', NULL, NULL, 'dashboard', 'https://test-smartacc-v3.freedev.app/admin/qr-monitor', '2026-09-10 21:06:16', '2026-09-10 21:06:16'),
(385, 2, 'viewed', NULL, NULL, 'dashboard', 'https://test-smartacc-v3.freedev.app/admin/qr-monitor', '2026-09-10 21:06:45', '2026-09-10 21:06:45'),
(386, 8, 'viewed', NULL, NULL, 'Overtime/Undertime', 'https://test-smartacc-v3.freedev.app/my/overtime-undertime', '2026-09-10 21:13:11', '2026-09-10 21:13:11'),
(387, 8, 'viewed', NULL, NULL, 'Attendance', 'https://test-smartacc-v3.freedev.app/my/attendance', '2026-09-10 21:13:29', '2026-09-10 21:13:29'),
(388, 8, 'viewed', NULL, NULL, 'Attendance', 'https://test-smartacc-v3.freedev.app/my/attendance', '2026-09-10 21:14:55', '2026-09-10 21:14:55'),
(403, 2, 'viewed', NULL, NULL, 'dashboard', 'https://test-smartacc-v3.freedev.app/admin/qr-monitor', '2026-09-11 16:24:39', '2026-09-11 16:24:39'),
(404, 2, 'viewed', NULL, NULL, 'dashboard', 'https://test-smartacc-v3.freedev.app/admin/qr-monitor', '2026-09-11 16:39:02', '2026-09-11 16:39:02'),
(405, 2, 'viewed', NULL, NULL, 'dashboard', 'https://test-smartacc-v3.freedev.app/admin/qr-monitor', '2026-09-11 16:39:40', '2026-09-11 16:39:40'),
(546, 2, 'viewed', NULL, NULL, 'dashboard', 'https://test-smartacc-v3.freedev.app/admin/qr-monitor', '2026-09-14 22:48:19', '2026-09-14 22:48:19'),
(547, 2, 'viewed', NULL, NULL, 'dashboard', 'https://test-smartacc-v3.freedev.app/admin/qr-monitor', '2026-09-14 22:48:39', '2026-09-14 22:48:39'),
(555, 2, 'viewed', NULL, NULL, 'dashboard', 'https://test-smartacc-v3.freedev.app/admin/qr-monitor', '2026-09-17 00:13:23', '2026-09-17 00:13:23'),
(631, 2, 'viewed', NULL, NULL, 'dashboard', 'https://test-smartacc-v3.freedev.app/admin/qr-monitor', '2026-09-17 19:19:31', '2026-09-17 19:19:31'),
(632, 2, 'viewed', NULL, NULL, 'dashboard', 'https://test-smartacc-v3.freedev.app/admin/qr-monitor', '2026-09-17 19:19:33', '2026-09-17 19:19:33'),
(1029, 2, 'viewed', NULL, NULL, 'dashboard', 'https://test-smartacc-v3.freedev.app/admin/qr-monitor', '2026-09-28 22:46:56', '2026-09-28 22:46:56'),
(1030, 2, 'viewed', NULL, NULL, 'dashboard', 'https://test-smartacc-v3.freedev.app/admin/qr-monitor', '2026-09-28 22:46:57', '2026-09-28 22:46:57'),
(1503, 4, 'viewed', NULL, NULL, 'Remittance Approval List', 'https://test-smartacc-v3.freedev.app/remittance-approval', '2026-09-30 22:56:54', '2026-09-30 22:56:54'),
(1504, 4, 'viewed', 'daily_remittance', 12, 'Remittance #12', 'https://test-smartacc-v3.freedev.app/remittance-approval/12', '2026-09-30 22:57:22', '2026-09-30 22:57:22'),
(1505, 4, 'approved', 'daily_remittance', 12, 'Remittance #12', 'https://test-smartacc-v3.freedev.app/remittance-approval/12/approve', '2026-09-30 22:57:28', '2026-09-30 22:57:28'),
(1506, 4, 'viewed', 'daily_remittance', 12, 'Remittance #12', 'https://test-smartacc-v3.freedev.app/remittance-approval/12', '2026-09-30 22:57:29', '2026-09-30 22:57:29'),
(1507, 4, 'viewed', NULL, NULL, 'Remittance Approval List', 'https://test-smartacc-v3.freedev.app/remittance-approval', '2026-09-30 22:57:38', '2026-09-30 22:57:38'),
(1508, 4, 'viewed', NULL, NULL, 'Remittance Report', 'https://test-smartacc-v3.freedev.app/reports/remittance', '2026-09-30 22:58:33', '2026-09-30 22:58:33'),
(1559, 4, 'viewed', NULL, NULL, 'Remittance Approval List', 'https://test-smartacc-v3.freedev.app/remittance-approval', '2026-10-01 00:50:01', '2026-10-01 00:50:01'),
(1652, 4, 'viewed', NULL, NULL, 'Accountant Dashboard', 'https://test-smartacc-v3.freedev.app/accountant', '2026-10-01 18:23:23', '2026-10-01 18:23:23'),
(1848, 4, 'viewed', NULL, NULL, 'Remittance Approval List', 'https://test-smartacc-v3.freedev.app/remittance-approval', '2026-10-01 23:15:41', '2026-10-01 23:15:41'),
(1872, 4, 'viewed', NULL, NULL, 'Remittance Report', 'https://test-smartacc-v3.freedev.app/reports/remittance', '2026-10-01 23:26:32', '2026-10-01 23:26:32'),
(1914, 3, 'viewed', 'attendance', NULL, 'Attendance dashboard', 'https://test-smartacc-v3.freedev.app/attendance', '2026-10-01 23:35:16', '2026-10-01 23:35:16'),
(1915, 3, 'approved', 'overtime', 18, 'OT/UT record #18', 'https://test-smartacc-v3.freedev.app/overtime/18/approve', '2026-10-01 23:35:25', '2026-10-01 23:35:25'),
(1916, 3, 'viewed', 'attendance', NULL, 'Attendance dashboard', 'https://test-smartacc-v3.freedev.app/attendance', '2026-10-01 23:35:26', '2026-10-01 23:35:26'),
(1917, 3, 'approved', 'overtime', 17, 'OT/UT record #17', 'https://test-smartacc-v3.freedev.app/overtime/17/approve', '2026-10-01 23:35:31', '2026-10-01 23:35:31'),
(1918, 3, 'viewed', 'attendance', NULL, 'Attendance dashboard', 'https://test-smartacc-v3.freedev.app/attendance', '2026-10-01 23:35:31', '2026-10-01 23:35:31'),
(1919, 3, 'viewed', 'payroll', NULL, 'Payroll dashboard', 'https://test-smartacc-v3.freedev.app/payroll/salary-computation', '2026-10-01 23:35:49', '2026-10-01 23:35:49'),
(1920, 3, 'viewed', 'payroll', NULL, 'Payroll dashboard', 'https://test-smartacc-v3.freedev.app/payroll/salary-computation', '2026-10-01 23:36:29', '2026-10-01 23:36:29'),
(1921, 3, 'created', 'payroll', 6, 'Payroll for Maria Carmel Wacay in batch #2', 'https://test-smartacc-v3.freedev.app/payroll/batch/2/employee', '2026-10-01 23:37:04', '2026-10-01 23:37:04'),
(1922, 3, 'viewed', 'report', NULL, 'Government contribution report', 'https://test-smartacc-v3.freedev.app/reports/government-contribution', '2026-10-01 23:37:57', '2026-10-01 23:37:57'),
(1923, 3, 'viewed', 'report', NULL, 'Government contribution report', 'https://test-smartacc-v3.freedev.app/reports/government-contribution', '2026-10-01 23:41:12', '2026-10-01 23:41:12'),
(2585, 1, 'viewed', 'attendance', NULL, 'Attendance dashboard', 'https://test-smartacc-v3.freedev.app/attendance', '2026-10-20 03:14:27', '2026-10-20 03:14:27'),
(2586, 1, 'viewed', NULL, NULL, 'account', 'https://test-smartacc-v3.freedev.app/superadmin/accounts', '2026-10-20 03:14:41', '2026-10-20 03:14:41'),
(2587, 1, 'viewed', 'payroll', NULL, 'Payroll dashboard', 'https://test-smartacc-v3.freedev.app/payroll/salary-computation', '2026-10-20 03:15:00', '2026-10-20 03:15:00'),
(2588, 1, 'viewed', NULL, NULL, 'dashboard', 'https://test-smartacc-v3.freedev.app/superadmin/dashboard', '2026-10-20 03:15:16', '2026-10-20 03:15:16'),
(2589, 1, 'viewed', 'driver', NULL, 'Drivers list', 'https://test-smartacc-v3.freedev.app/drivers', '2026-10-20 03:15:26', '2026-10-20 03:15:26'),
(2590, 1, 'viewed', 'daily_remittance', NULL, 'Daily Remittances list', 'https://test-smartacc-v3.freedev.app/remittances', '2026-10-20 03:15:31', '2026-10-20 03:15:31'),
(2591, 1, 'viewed', NULL, NULL, 'dashboard', 'https://test-smartacc-v3.freedev.app/superadmin/dashboard', '2026-10-20 03:15:44', '2026-10-20 03:15:44'),
(2592, 1, 'viewed', NULL, NULL, 'dashboard', 'https://test-smartacc-v3.freedev.app/superadmin/dashboard', '2026-10-20 06:39:15', '2026-10-20 06:39:15'),
(2593, 1, 'viewed', NULL, NULL, 'dashboard', 'https://test-smartacc-v3.freedev.app/superadmin/dashboard', '2026-10-20 06:55:08', '2026-10-20 06:55:08'),
(2594, 1, 'viewed', NULL, NULL, 'dashboard', 'https://test-smartacc-v3.freedev.app/remittance-clerk', '2026-10-20 06:55:26', '2026-10-20 06:55:26');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `plate_number` varchar(255) NOT NULL,
  `route_id` bigint(20) UNSIGNED NOT NULL,
  `operator` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `plate_number`, `route_id`, `operator`, `status`, `created_at`, `updated_at`) VALUES
(1, 'NKI5314', 7, 'IRENEO FELIPE', 'active', '2026-09-10 18:18:30', '2026-10-01 00:46:31'),
(2, 'NKI5305', 2, 'ROLAND JOSEPH P. ROSELL', 'active', '2026-09-30 20:08:10', '2026-09-30 20:08:10'),
(3, 'NKI5306', 2, 'MARYCIL F. LABASAN', 'under_maintenance', '2026-09-30 20:09:11', '2026-09-30 20:10:54'),
(4, 'NKI5307', 2, 'RENATO G. CELARIO', 'active', '2026-09-30 20:09:41', '2026-09-30 20:09:41'),
(5, 'NKI5324', 2, 'GERRY Q. OLAYA', 'active', '2026-09-30 20:10:13', '2026-09-30 20:10:13'),
(6, 'NKI5325', 2, 'ELMER ABO', 'active', '2026-09-30 20:10:34', '2026-09-30 20:10:34'),
(7, 'NKI5315', 1, 'JIMMY R. DE LARA', 'active', '2026-09-30 20:11:31', '2026-09-30 20:11:31'),
(8, 'NKI5316', 1, 'PEPITO O. SUPETRAN', 'under_maintenance', '2026-09-30 20:11:54', '2026-10-16 21:12:09'),
(9, 'NKI5317', 3, 'KNIGHTS TRANSPORT', 'active', '2026-09-30 20:12:16', '2026-09-30 20:12:16'),
(10, 'NKI5267', 5, 'BERT R. VILLARAZA', 'under_maintenance', '2026-09-30 20:13:13', '2026-10-16 21:05:59'),
(11, 'NKI5275', 5, 'GEMMA CUYOS JUBAN', 'active', '2026-09-30 20:13:46', '2026-09-30 20:13:46'),
(12, 'NKI5256', 6, 'ROBERTO VILLAMIDEZ MARCOS', 'active', '2026-09-30 20:14:14', '2026-09-30 20:14:14'),
(13, 'NKI5274', 6, 'MARIA CRISTINA M. MANILA', 'active', '2026-09-30 20:14:46', '2026-09-30 20:14:46'),
(14, 'NKI5255', 4, 'ROBERTO VILLAMIDEZ MARCOS', 'active', '2026-09-30 20:15:37', '2026-09-30 20:17:29'),
(15, 'NKI5266', 4, 'JOY CRUZ/MA. CLARA CRUZ', 'active', '2026-09-30 20:16:02', '2026-09-30 20:16:02'),
(16, 'NKI5265', 4, 'LEIZL INSO RARO', 'active', '2026-09-30 20:16:28', '2026-09-30 20:16:28'),
(17, 'NKI5264', 4, 'ROGELIO A. AZURIN JR.', 'active', '2026-09-30 20:16:58', '2026-09-30 20:16:58'),
(18, 'UNI1234', 8, 'KNIGHTS TRANSPORT', 'active', '2026-10-01 18:56:36', '2026-10-01 18:56:36');

-- --------------------------------------------------------

--
-- Table structure for table `withholding_taxes`
--

CREATE TABLE `withholding_taxes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `min_salary` decimal(12,2) DEFAULT NULL,
  `max_salary` decimal(12,2) DEFAULT NULL,
  `employee_share` decimal(12,2) DEFAULT NULL,
  `percentage_employee` decimal(5,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `withholding_taxes`
--

INSERT INTO `withholding_taxes` (`id`, `name`, `min_salary`, `max_salary`, `employee_share`, `percentage_employee`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Daily - Bracket 1', '0.00', '685.00', '0.00', '0.00', 'Daily', '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(2, 'Daily - Bracket 2', '685.01', '1095.00', '0.00', '15.00', 'Daily (₱0 + 15% over ₱685)', '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(3, 'Daily - Bracket 3', '1096.00', '2191.00', '61.65', '20.00', 'Daily (₱61.65 + 20% over ₱1,096)', '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(4, 'Daily - Bracket 4', '2192.00', '5478.00', '280.85', '25.00', 'Daily (₱280.85 + 25% over ₱2,192)', '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(5, 'Daily - Bracket 5', '5479.00', '21917.00', '1102.60', '30.00', 'Daily (₱1,102.60 + 30% over ₱5,479)', '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(6, 'Daily - Bracket 6', '21918.00', '999999.00', '6036.30', '35.00', 'Daily (₱6,036.30 + 35% over ₱21,918)', '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(7, 'Weekly - Bracket 1', '0.00', '4808.00', '0.00', '0.00', 'Weekly', '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(8, 'Weekly - Bracket 2', '4808.01', '7691.00', '0.00', '15.00', 'Weekly (₱0 + 15% over ₱4,808)', '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(9, 'Weekly - Bracket 3', '7692.00', '15384.00', '432.60', '20.00', 'Weekly (₱432.60 + 20% over ₱7,692)', '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(10, 'Weekly - Bracket 4', '15385.00', '39461.00', '1971.20', '25.00', 'Weekly (₱1,971.20 + 25% over ₱15,385)', '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(11, 'Weekly - Bracket 5', '39462.00', '153845.00', '7760.45', '30.00', 'Weekly (₱7,760.45 + 30% over ₱39,462)', '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(12, 'Weekly - Bracket 6', '153846.00', '999999.00', '42355.65', '35.00', 'Weekly (₱42,355.65 + 35% over ₱153,846)', '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(13, 'Semi-monthly - Bracket 1', '0.00', '10417.00', '0.00', '0.00', 'Semi-monthly', '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(14, 'Semi-monthly - Bracket 2', '10417.01', '16666.00', '0.00', '15.00', 'Semi-monthly (₱0 + 15% over ₱10,417)', '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(15, 'Semi-monthly - Bracket 3', '16667.00', '33332.00', '937.50', '20.00', 'Semi-monthly (₱937.50 + 20% over ₱16,667)', '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(16, 'Semi-monthly - Bracket 4', '33333.00', '83332.00', '4270.70', '25.00', 'Semi-monthly (₱4,270.70 + 25% over ₱33,333)', '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(17, 'Semi-monthly - Bracket 5', '83333.00', '333332.00', '16770.70', '30.00', 'Semi-monthly (₱16,770.70 + 30% over ₱83,333)', '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(18, 'Semi-monthly - Bracket 6', '333333.00', '999999.00', '91770.70', '35.00', 'Semi-monthly (₱91,770.70 + 35% over ₱333,333)', '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(19, 'Monthly - Bracket 1', '0.00', '20833.00', '0.00', '0.00', 'Monthly', '2026-09-03 06:01:03', '2026-09-03 06:01:03'),
(20, 'Monthly - Bracket 2', '20833.01', '33332.00', '0.00', '15.00', 'Monthly (₱0 + 15% over ₱20,833)', '2026-09-03 06:01:04', '2026-09-03 06:01:04'),
(21, 'Monthly - Bracket 3', '33333.00', '66666.00', '1875.00', '20.00', 'Monthly (₱1,875.00 + 20% over ₱33,333)', '2026-09-03 06:01:04', '2026-09-03 06:01:04'),
(22, 'Monthly - Bracket 4', '66667.00', '166666.00', '8541.80', '25.00', 'Monthly (₱8,541.80 + 25% over ₱66,667)', '2026-09-03 06:01:04', '2026-09-03 06:01:04'),
(23, 'Monthly - Bracket 5', '166667.00', '666666.00', '33541.80', '30.00', 'Monthly (₱33,541.80 + 30% over ₱166,667)', '2026-09-03 06:01:04', '2026-09-03 06:01:04'),
(24, 'Monthly - Bracket 6', '666667.00', '999999.00', '183541.80', '35.00', 'Monthly (₱183,541.80 + 35% over ₱666,667)', '2026-09-03 06:01:04', '2026-09-03 06:01:04');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account_mappings`
--
ALTER TABLE `account_mappings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `account_mappings_mapping_key_unique` (`mapping_key`),
  ADD KEY `account_mappings_account_id_foreign` (`account_id`);

--
-- Indexes for table `allowances`
--
ALTER TABLE `allowances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `allowances_user_id_foreign` (`user_id`),
  ADD KEY `allowances_payroll_id_foreign` (`payroll_id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `attendance_user_id_date_unique` (`user_id`,`date`);

--
-- Indexes for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attendance_logs_user_id_foreign` (`user_id`);

--
-- Indexes for table `attendance_tokens`
--
ALTER TABLE `attendance_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `attendance_tokens_token_unique` (`token`),
  ADD KEY `attendance_tokens_user_id_foreign` (`user_id`);

--
-- Indexes for table `bonuses`
--
ALTER TABLE `bonuses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bonuses_code_unique` (`code`),
  ADD KEY `bonuses_created_by_foreign` (`created_by`);

--
-- Indexes for table `bonus_employee`
--
ALTER TABLE `bonus_employee`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bonus_employee_bonus_id_user_id_unique` (`bonus_id`,`user_id`),
  ADD KEY `bonus_employee_user_id_foreign` (`user_id`);

--
-- Indexes for table `boundary_payments`
--
ALTER TABLE `boundary_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `boundary_payments_source_payment_key_unique` (`source_payment_key`),
  ADD UNIQUE KEY `boundary_payments_initial_payment_remittance_id_unique` (`initial_payment_remittance_id`),
  ADD UNIQUE KEY `boundary_payments_journal_entry_id_unique` (`journal_entry_id`),
  ADD KEY `boundary_payments_daily_remittance_id_payment_date_index` (`daily_remittance_id`,`payment_date`),
  ADD KEY `boundary_payments_recorded_by_foreign` (`recorded_by`),
  ADD KEY `boundary_payments_approved_by_foreign` (`approved_by`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `cash_advances`
--
ALTER TABLE `cash_advances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cash_advances_user_id_foreign` (`user_id`),
  ADD KEY `cash_advances_approved_by_foreign` (`approved_by`),
  ADD KEY `cash_advances_deducted_payroll_id_foreign` (`deducted_payroll_id`);

--
-- Indexes for table `chart_of_accounts`
--
ALTER TABLE `chart_of_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `chart_of_accounts_account_code_unique` (`account_code`),
  ADD KEY `chart_of_accounts_created_by_foreign` (`created_by`);

--
-- Indexes for table `daily_remittances`
--
ALTER TABLE `daily_remittances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `daily_remittances_driver_id_foreign` (`driver_id`),
  ADD KEY `daily_remittances_pao_id_foreign` (`pao_id`),
  ADD KEY `daily_remittances_route_id_foreign` (`route_id`),
  ADD KEY `daily_remittances_vehicle_id_foreign` (`vehicle_id`);

--
-- Indexes for table `deductions`
--
ALTER TABLE `deductions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `deductions_user_id_foreign` (`user_id`),
  ADD KEY `deductions_payroll_id_foreign` (`payroll_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `departments_name_unique` (`name`);

--
-- Indexes for table `drivers`
--
ALTER TABLE `drivers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `drivers_license_number_unique` (`license_number`),
  ADD UNIQUE KEY `drivers_email_unique` (`email`);

--
-- Indexes for table `employee_attachments`
--
ALTER TABLE `employee_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_attachments_reviewed_by_foreign` (`reviewed_by`),
  ADD KEY `employee_attachments_user_id_attachment_key_index` (`user_id`,`attachment_key`);

--
-- Indexes for table `employee_beneficiaries`
--
ALTER TABLE `employee_beneficiaries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_beneficiaries_user_id_foreign` (`user_id`);

--
-- Indexes for table `employee_experiences`
--
ALTER TABLE `employee_experiences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_experiences_user_id_foreign` (`user_id`);

--
-- Indexes for table `employee_leave_balances`
--
ALTER TABLE `employee_leave_balances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_leave_balances_user_id_leave_type_id_year_unique` (`user_id`,`leave_type_id`,`year`),
  ADD KEY `employee_leave_balances_leave_type_id_foreign` (`leave_type_id`);

--
-- Indexes for table `employee_references`
--
ALTER TABLE `employee_references`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_references_user_id_foreign` (`user_id`);

--
-- Indexes for table `employee_skills`
--
ALTER TABLE `employee_skills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_skills_user_id_foreign` (`user_id`);

--
-- Indexes for table `holidays`
--
ALTER TABLE `holidays`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `journal_entries`
--
ALTER TABLE `journal_entries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `journal_entries_journal_number_unique` (`journal_number`),
  ADD KEY `journal_entries_created_by_foreign` (`created_by`),
  ADD KEY `journal_entries_posted_by_foreign` (`posted_by`),
  ADD KEY `journal_entries_reference_type_reference_id_index` (`reference_type`,`reference_id`);

--
-- Indexes for table `journal_entry_lines`
--
ALTER TABLE `journal_entry_lines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `journal_entry_lines_journal_entry_id_foreign` (`journal_entry_id`),
  ADD KEY `journal_entry_lines_account_id_foreign` (`account_id`);

--
-- Indexes for table `leaves`
--
ALTER TABLE `leaves`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leaves_user_id_foreign` (`user_id`),
  ADD KEY `leaves_approved_by_foreign` (`approved_by`),
  ADD KEY `leaves_leave_type_id_foreign` (`leave_type_id`);

--
-- Indexes for table `leave_types`
--
ALTER TABLE `leave_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `overtime_undertimes`
--
ALTER TABLE `overtime_undertimes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `overtime_undertimes_user_id_foreign` (`user_id`),
  ADD KEY `overtime_undertimes_approved_by_foreign` (`approved_by`);

--
-- Indexes for table `paos`
--
ALTER TABLE `paos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `paos_email_unique` (`email`);

--
-- Indexes for table `payroll_allowances`
--
ALTER TABLE `payroll_allowances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payroll_allowances_payroll_id_foreign` (`payroll_id`);

--
-- Indexes for table `payroll_batches`
--
ALTER TABLE `payroll_batches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payroll_batches_generated_by_foreign` (`generated_by`),
  ADD KEY `payroll_batches_finalized_by_foreign` (`finalized_by`),
  ADD KEY `payroll_batches_approved_by_foreign` (`approved_by`),
  ADD KEY `payroll_batches_rejected_by_foreign` (`rejected_by`),
  ADD KEY `payroll_batches_type_index` (`type`);

--
-- Indexes for table `payroll_bonuses`
--
ALTER TABLE `payroll_bonuses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payroll_bonuses_payroll_id_foreign` (`payroll_id`);

--
-- Indexes for table `payroll_cutoff_schedules`
--
ALTER TABLE `payroll_cutoff_schedules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payroll_cutoff_schedules_cutoff_day_unique` (`cutoff_day`);

--
-- Indexes for table `payroll_deductions`
--
ALTER TABLE `payroll_deductions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payroll_deductions_payroll_id_foreign` (`payroll_id`);

--
-- Indexes for table `pinned_items`
--
ALTER TABLE `pinned_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pinned_items_user_id_url_unique` (`user_id`,`url`),
  ADD KEY `pinned_items_user_id_sort_order_index` (`user_id`,`sort_order`);

--
-- Indexes for table `position_rates`
--
ALTER TABLE `position_rates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `position_rates_name_unique` (`name`),
  ADD KEY `position_rates_department_id_foreign` (`department_id`);

--
-- Indexes for table `routes`
--
ALTER TABLE `routes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Indexes for table `shifts`
--
ALTER TABLE `shifts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `shifts_name_unique` (`name`);

--
-- Indexes for table `statutory_deductions`
--
ALTER TABLE `statutory_deductions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_activities`
--
ALTER TABLE `user_activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_activities_user_id_created_at_index` (`user_id`,`created_at`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vehicles_plate_number_unique` (`plate_number`),
  ADD KEY `vehicles_route_id_foreign` (`route_id`);

--
-- Indexes for table `withholding_taxes`
--
ALTER TABLE `withholding_taxes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounting_audit_logs`
--
ALTER TABLE `accounting_audit_logs`
  ADD PRIMARY KEY (`id`),
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `account_mappings`
--
ALTER TABLE `account_mappings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `allowances`
--
ALTER TABLE `allowances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `attendance_tokens`
--
ALTER TABLE `attendance_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `bonuses`
--
ALTER TABLE `bonuses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bonus_employee`
--
ALTER TABLE `bonus_employee`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `boundary_payments`
--
ALTER TABLE `boundary_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cash_advances`
--
ALTER TABLE `cash_advances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chart_of_accounts`
--
ALTER TABLE `chart_of_accounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `daily_remittances`
--
ALTER TABLE `daily_remittances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

--
-- AUTO_INCREMENT for table `deductions`
--
ALTER TABLE `deductions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `drivers`
--
ALTER TABLE `drivers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `employee_attachments`
--
ALTER TABLE `employee_attachments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_beneficiaries`
--
ALTER TABLE `employee_beneficiaries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `employee_experiences`
--
ALTER TABLE `employee_experiences`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_leave_balances`
--
ALTER TABLE `employee_leave_balances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `employee_references`
--
ALTER TABLE `employee_references`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_skills`
--
ALTER TABLE `employee_skills`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `holidays`
--
ALTER TABLE `holidays`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `journal_entries`
--
ALTER TABLE `journal_entries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `journal_entry_lines`
--
ALTER TABLE `journal_entry_lines`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leaves`
--
ALTER TABLE `leaves`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_types`
--
ALTER TABLE `leave_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `overtime_undertimes`
--
ALTER TABLE `overtime_undertimes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `paos`
--
ALTER TABLE `paos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `payrolls`
--
ALTER TABLE `payrolls`
  ADD PRIMARY KEY (`id`),
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payroll_allowances`
--
ALTER TABLE `payroll_allowances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payroll_batches`
--
ALTER TABLE `payroll_batches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payroll_bonuses`
--
ALTER TABLE `payroll_bonuses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payroll_cutoff_schedules`
--
ALTER TABLE `payroll_cutoff_schedules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payroll_deductions`
--
ALTER TABLE `payroll_deductions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pinned_items`
--
ALTER TABLE `pinned_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `position_rates`
--
ALTER TABLE `position_rates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `routes`
--
ALTER TABLE `routes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shifts`
--
ALTER TABLE `shifts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `statutory_deductions`
--
ALTER TABLE `statutory_deductions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `thirteenth_month_pays`
--
ALTER TABLE `thirteenth_month_pays`
  ADD PRIMARY KEY (`id`),
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_activities`
--
ALTER TABLE `user_activities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2595;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `withholding_taxes`
--
ALTER TABLE `withholding_taxes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `account_mappings`
--
ALTER TABLE `account_mappings`
  ADD CONSTRAINT `account_mappings_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `chart_of_accounts` (`id`);

--
-- Constraints for table `allowances`
--
ALTER TABLE `allowances`
  ADD CONSTRAINT `allowances_payroll_id_foreign` FOREIGN KEY (`payroll_id`) REFERENCES `payrolls` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `allowances_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  ADD CONSTRAINT `attendance_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attendance_tokens`
--
ALTER TABLE `attendance_tokens`
  ADD CONSTRAINT `attendance_tokens_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `bonuses`
--
ALTER TABLE `bonuses`
  ADD CONSTRAINT `bonuses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `bonus_employee`
--
ALTER TABLE `bonus_employee`
  ADD CONSTRAINT `bonus_employee_bonus_id_foreign` FOREIGN KEY (`bonus_id`) REFERENCES `bonuses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bonus_employee_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `boundary_payments`
--
ALTER TABLE `boundary_payments`
  ADD CONSTRAINT `boundary_payments_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `boundary_payments_daily_remittance_id_foreign` FOREIGN KEY (`daily_remittance_id`) REFERENCES `daily_remittances` (`id`),
  ADD CONSTRAINT `boundary_payments_initial_payment_remittance_id_foreign` FOREIGN KEY (`initial_payment_remittance_id`) REFERENCES `daily_remittances` (`id`),
  ADD CONSTRAINT `boundary_payments_journal_entry_id_foreign` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`),
  ADD CONSTRAINT `boundary_payments_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `cash_advances`
--
ALTER TABLE `cash_advances`
  ADD CONSTRAINT `cash_advances_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `cash_advances_deducted_payroll_id_foreign` FOREIGN KEY (`deducted_payroll_id`) REFERENCES `payrolls` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `cash_advances_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chart_of_accounts`
--
ALTER TABLE `chart_of_accounts`
  ADD CONSTRAINT `chart_of_accounts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `daily_remittances`
--
ALTER TABLE `daily_remittances`
  ADD CONSTRAINT `daily_remittances_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `daily_remittances_pao_id_foreign` FOREIGN KEY (`pao_id`) REFERENCES `paos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `daily_remittances_route_id_foreign` FOREIGN KEY (`route_id`) REFERENCES `routes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `daily_remittances_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `deductions`
--
ALTER TABLE `deductions`
  ADD CONSTRAINT `deductions_payroll_id_foreign` FOREIGN KEY (`payroll_id`) REFERENCES `payrolls` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `deductions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_attachments`
--
ALTER TABLE `employee_attachments`
  ADD CONSTRAINT `employee_attachments_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_attachments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_beneficiaries`
--
ALTER TABLE `employee_beneficiaries`
  ADD CONSTRAINT `employee_beneficiaries_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_experiences`
--
ALTER TABLE `employee_experiences`
  ADD CONSTRAINT `employee_experiences_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_leave_balances`
--
ALTER TABLE `employee_leave_balances`
  ADD CONSTRAINT `employee_leave_balances_leave_type_id_foreign` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_leave_balances_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_references`
--
ALTER TABLE `employee_references`
  ADD CONSTRAINT `employee_references_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_skills`
--
ALTER TABLE `employee_skills`
  ADD CONSTRAINT `employee_skills_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `journal_entries`
--
ALTER TABLE `journal_entries`
  ADD CONSTRAINT `journal_entries_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `journal_entries_posted_by_foreign` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `journal_entry_lines`
--
ALTER TABLE `journal_entry_lines`
  ADD CONSTRAINT `journal_entry_lines_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `chart_of_accounts` (`id`),
  ADD CONSTRAINT `journal_entry_lines_journal_entry_id_foreign` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leaves`
--
ALTER TABLE `leaves`
  ADD CONSTRAINT `leaves_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `leaves_leave_type_id_foreign` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `leaves_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `overtime_undertimes`
--
ALTER TABLE `overtime_undertimes`
  ADD CONSTRAINT `overtime_undertimes_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `overtime_undertimes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payroll_allowances`
--
ALTER TABLE `payroll_allowances`
  ADD CONSTRAINT `payroll_allowances_payroll_id_foreign` FOREIGN KEY (`payroll_id`) REFERENCES `payrolls` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payroll_batches`
--
ALTER TABLE `payroll_batches`
  ADD CONSTRAINT `payroll_batches_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payroll_batches_finalized_by_foreign` FOREIGN KEY (`finalized_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payroll_batches_generated_by_foreign` FOREIGN KEY (`generated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payroll_batches_rejected_by_foreign` FOREIGN KEY (`rejected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `payroll_bonuses`
--
ALTER TABLE `payroll_bonuses`
  ADD CONSTRAINT `payroll_bonuses_payroll_id_foreign` FOREIGN KEY (`payroll_id`) REFERENCES `payrolls` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payroll_deductions`
--
ALTER TABLE `payroll_deductions`
  ADD CONSTRAINT `payroll_deductions_payroll_id_foreign` FOREIGN KEY (`payroll_id`) REFERENCES `payrolls` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pinned_items`
--
ALTER TABLE `pinned_items`
  ADD CONSTRAINT `pinned_items_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `position_rates`
--
ALTER TABLE `position_rates`
  ADD CONSTRAINT `position_rates_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_activities`
--
ALTER TABLE `user_activities`
  ADD CONSTRAINT `user_activities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD CONSTRAINT `vehicles_route_id_foreign` FOREIGN KEY (`route_id`) REFERENCES `routes` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

SET FOREIGN_KEY_CHECKS=1;

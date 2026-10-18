-- Build script: assembles the finished test2 database.
-- Runs against the "final" database. Source tables come from t1 and t2.

SET FOREIGN_KEY_CHECKS = 0;

-- ── 1. Throw away test1's transaction history ───────────────────────────────
TRUNCATE TABLE `payroll_batches`;
TRUNCATE TABLE `payrolls`;
TRUNCATE TABLE `payroll_allowances`;
TRUNCATE TABLE `payroll_deductions`;
TRUNCATE TABLE `payroll_bonuses`;
TRUNCATE TABLE `thirteenth_month_pays`;
TRUNCATE TABLE `cash_advances`;
TRUNCATE TABLE `allowances`;
TRUNCATE TABLE `deductions`;
TRUNCATE TABLE `bonuses`;
TRUNCATE TABLE `bonus_employee`;
TRUNCATE TABLE `attendance`;
TRUNCATE TABLE `attendance_logs`;
TRUNCATE TABLE `attendance_tokens`;
TRUNCATE TABLE `overtime_undertimes`;
TRUNCATE TABLE `leaves`;
TRUNCATE TABLE `employee_leave_balances`;
TRUNCATE TABLE `daily_remittances`;
TRUNCATE TABLE `journal_entries`;
TRUNCATE TABLE `journal_entry_lines`;
TRUNCATE TABLE `accounting_audit_logs`;
TRUNCATE TABLE `notifications`;
TRUNCATE TABLE `user_activities`;
TRUNCATE TABLE `pinned_items`;
TRUNCATE TABLE `sessions`;
TRUNCATE TABLE `cache`;
TRUNCATE TABLE `cache_locks`;

-- ── 2. Swap the old routes.boundary for weekday/weekend boundary ────────────
ALTER TABLE `routes`
  ADD COLUMN `weekday_boundary` DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER `route_name`,
  ADD COLUMN `weekend_boundary` DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER `weekday_boundary`;

UPDATE `routes`
SET `weekday_boundary` = COALESCE(`boundary`, 0),
    `weekend_boundary` = COALESCE(`boundary`, 0);

ALTER TABLE `routes` DROP COLUMN `boundary`;

-- ── 3. Use test2's chart of accounts and mappings ───────────────────────────
-- test1 only has 1 account. test2 has 19 accounts plus 12 mappings, which the
-- payroll journal needs. Copy them over.
DROP TABLE IF EXISTS `chart_of_accounts`;
CREATE TABLE `chart_of_accounts` LIKE `t2`.`chart_of_accounts`;
INSERT INTO `chart_of_accounts` SELECT * FROM `t2`.`chart_of_accounts`;

DROP TABLE IF EXISTS `account_mappings`;
CREATE TABLE `account_mappings` LIKE `t2`.`account_mappings`;
INSERT INTO `account_mappings` SELECT * FROM `t2`.`account_mappings`;

-- ── 4. Payroll accounting fix ──────────────────────────────────────────────
-- thirteenth_month_pays was empty in both exports, so no rows revealed that
-- it was missing these three columns. The model uses SoftDeletes, so without
-- deleted_at every payroll page throws a 500.
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS
               WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'thirteenth_month_pays'
                 AND COLUMN_NAME = 'created_at') > 0,
    'SELECT 1',
    'ALTER TABLE `thirteenth_month_pays`
       ADD COLUMN `created_at` timestamp NULL DEFAULT NULL,
       ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL,
       ADD COLUMN `deleted_at` timestamp NULL DEFAULT NULL');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- test2 may already have is_contra (someone added it by hand). Only add it
-- when it is missing.
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS
               WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'chart_of_accounts'
                 AND COLUMN_NAME = 'is_contra') > 0,
    'SELECT 1',
    'ALTER TABLE `chart_of_accounts` ADD COLUMN `is_contra` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_active`');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

UPDATE `chart_of_accounts` SET `is_contra` = 1 WHERE `account_code` = '5060';

INSERT INTO `chart_of_accounts`
    (`account_code`, `account_name`, `account_type`, `description`, `is_active`, `created_at`, `updated_at`)
SELECT '2070', 'Other Payroll Deductions Payable', 'Liability',
       'Employee payroll deductions withheld for remittance to third parties',
       1, NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `chart_of_accounts` WHERE `account_code` = '2070');

INSERT IGNORE INTO `account_mappings` (`mapping_key`, `label`, `account_id`, `created_at`, `updated_at`)
SELECT 'payroll_deductions', 'Payroll Deductions', `id`, NOW(), NOW()
FROM `chart_of_accounts` WHERE `account_code` = '5060';

INSERT IGNORE INTO `account_mappings` (`mapping_key`, `label`, `account_id`, `created_at`, `updated_at`)
SELECT 'employee_receivable', 'Employee Receivable', `id`, NOW(), NOW()
FROM `chart_of_accounts` WHERE `account_code` = '1030';

INSERT IGNORE INTO `account_mappings` (`mapping_key`, `label`, `account_id`, `created_at`, `updated_at`)
SELECT 'other_payroll_deductions_payable', 'Other Payroll Deductions Payable', `id`, NOW(), NOW()
FROM `chart_of_accounts` WHERE `account_code` = '2070';

INSERT IGNORE INTO `account_mappings` (`mapping_key`, `label`, `account_id`, `created_at`, `updated_at`)
SELECT 'employer_contributions_expense', 'Employer Government Contributions Expense', `id`, NOW(), NOW()
FROM `chart_of_accounts` WHERE `account_code` = '5050';

SET FOREIGN_KEY_CHECKS = 1;
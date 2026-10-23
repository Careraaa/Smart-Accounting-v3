-- ============================================================================
--  Repair the TEST1 database (the client sandbox).
--
--  Database: if0_42738871_smart_accounting_v3_test
--
--  KEEPS EVERY ROW. This only adds missing columns and primary keys, then
--  applies the payroll accounting fix.
--
--  Plain SQL only, no stored procedures, so it runs in phpMyAdmin.
--  Every change checks first, so running it twice is safe.
--
-- WARNING: BACK UP FIRST. Export test1 from phpMyAdmin and keep the file.
--
-- WARNING: This does NOT touch the routes table. test1 still uses the old single
--         boundary column and its code expects it. Leave it alone.
-- ============================================================================


-- 1. Columns ---------------------------------------------------------------

SET @has_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_created_at = 0,
  'ALTER TABLE `users` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_updated_at = 0,
  'ALTER TABLE `users` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_last_login_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'last_login_at');

SET @sql = IF(@has_last_login_at = 0,
  'ALTER TABLE `users` ADD COLUMN `last_login_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_read_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'notifications' AND COLUMN_NAME = 'read_at');

SET @sql = IF(@has_read_at = 0,
  'ALTER TABLE `notifications` ADD COLUMN `read_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_deleted_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'notifications' AND COLUMN_NAME = 'deleted_at');

SET @sql = IF(@has_deleted_at = 0,
  'ALTER TABLE `notifications` ADD COLUMN `deleted_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'notifications' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_created_at = 0,
  'ALTER TABLE `notifications` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'notifications' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_updated_at = 0,
  'ALTER TABLE `notifications` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_new_values = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'accounting_audit_logs' AND COLUMN_NAME = 'new_values');

SET @sql = IF(@has_new_values = 0,
  'ALTER TABLE `accounting_audit_logs` ADD COLUMN `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_ip_address = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'accounting_audit_logs' AND COLUMN_NAME = 'ip_address');

SET @sql = IF(@has_ip_address = 0,
  'ALTER TABLE `accounting_audit_logs` ADD COLUMN `ip_address` varchar(45) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'accounting_audit_logs' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_created_at = 0,
  'ALTER TABLE `accounting_audit_logs` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'accounting_audit_logs' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_updated_at = 0,
  'ALTER TABLE `accounting_audit_logs` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'thirteenth_month_pays' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_created_at = 0,
  'ALTER TABLE `thirteenth_month_pays` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'thirteenth_month_pays' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_updated_at = 0,
  'ALTER TABLE `thirteenth_month_pays` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_deleted_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'thirteenth_month_pays' AND COLUMN_NAME = 'deleted_at');

SET @sql = IF(@has_deleted_at = 0,
  'ALTER TABLE `thirteenth_month_pays` ADD COLUMN `deleted_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_is_contra = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'chart_of_accounts' AND COLUMN_NAME = 'is_contra');

SET @sql = IF(@has_is_contra = 0,
  'ALTER TABLE `chart_of_accounts` ADD COLUMN `is_contra` tinyint(1) NOT NULL DEFAULT 0',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_accounting_treatment = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_deductions' AND COLUMN_NAME = 'accounting_treatment');

SET @sql = IF(@has_accounting_treatment = 0,
  'ALTER TABLE `payroll_deductions` ADD COLUMN `accounting_treatment` varchar(30) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_deduction_source = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_deductions' AND COLUMN_NAME = 'deduction_source');

SET @sql = IF(@has_deduction_source = 0,
  'ALTER TABLE `payroll_deductions` ADD COLUMN `deduction_source` varchar(20) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_status = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'status');

SET @sql = IF(@has_status = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `status` varchar(255) NOT NULL DEFAULT \'pending\'',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_basic_salary = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'basic_salary');

SET @sql = IF(@has_basic_salary = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `basic_salary` decimal(12,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_gross_pay = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'gross_pay');

SET @sql = IF(@has_gross_pay = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `gross_pay` decimal(12,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_holiday_pay = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'holiday_pay');

SET @sql = IF(@has_holiday_pay = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `holiday_pay` decimal(12,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_holiday_ot_pay = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'holiday_ot_pay');

SET @sql = IF(@has_holiday_ot_pay = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `holiday_ot_pay` decimal(12,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_holiday_ot_hours = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'holiday_ot_hours');

SET @sql = IF(@has_holiday_ot_hours = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `holiday_ot_hours` decimal(8,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_holiday_breakdown = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'holiday_breakdown');

SET @sql = IF(@has_holiday_breakdown = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `holiday_breakdown` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_net_pay = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'net_pay');

SET @sql = IF(@has_net_pay = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `net_pay` decimal(12,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_days_worked = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'days_worked');

SET @sql = IF(@has_days_worked = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `days_worked` decimal(8,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_hours_worked = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'hours_worked');

SET @sql = IF(@has_hours_worked = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `hours_worked` decimal(8,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_payment_date = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'payment_date');

SET @sql = IF(@has_payment_date = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `payment_date` date DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_approved_by = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'approved_by');

SET @sql = IF(@has_approved_by = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `approved_by` bigint(20) unsigned DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_created_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'created_at');

SET @sql = IF(@has_created_at = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_updated_at = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'updated_at');

SET @sql = IF(@has_updated_at = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_present_days = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'present_days');

SET @sql = IF(@has_present_days = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `present_days` int(10) unsigned NOT NULL DEFAULT 0',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_late_days = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'late_days');

SET @sql = IF(@has_late_days = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `late_days` int(10) unsigned NOT NULL DEFAULT 0',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_absent_days = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'absent_days');

SET @sql = IF(@has_absent_days = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `absent_days` int(10) unsigned NOT NULL DEFAULT 0',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_daily_rate_snapshot = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'daily_rate_snapshot');

SET @sql = IF(@has_daily_rate_snapshot = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `daily_rate_snapshot` decimal(10,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_sss = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'sss');

SET @sql = IF(@has_sss = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `sss` decimal(10,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_pagibig = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'pagibig');

SET @sql = IF(@has_pagibig = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `pagibig` decimal(10,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_philhealth = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'philhealth');

SET @sql = IF(@has_philhealth = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `philhealth` decimal(10,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_withholding_tax = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'withholding_tax');

SET @sql = IF(@has_withholding_tax = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `withholding_tax` decimal(12,2) NOT NULL DEFAULT 0.00',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employer_sss = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'employer_sss');

SET @sql = IF(@has_employer_sss = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `employer_sss` decimal(12,2) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employer_pagibig = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'employer_pagibig');

SET @sql = IF(@has_employer_pagibig = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `employer_pagibig` decimal(12,2) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_employer_philhealth = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND COLUMN_NAME = 'employer_philhealth');

SET @sql = IF(@has_employer_philhealth = 0,
  'ALTER TABLE `payrolls` ADD COLUMN `employer_philhealth` decimal(12,2) DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 2. Primary keys --------------------------------------------------------
-- Without these the database cannot tell rows apart.

SET @has_pk_users = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND CONSTRAINT_TYPE = 'PRIMARY KEY');

SET @sql = IF(@has_pk_users = 0,
  'ALTER TABLE `users` ADD PRIMARY KEY (`id`)',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_pk_payrolls = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls' AND CONSTRAINT_TYPE = 'PRIMARY KEY');

SET @sql = IF(@has_pk_payrolls = 0,
  'ALTER TABLE `payrolls` ADD PRIMARY KEY (`id`)',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_pk_notifications = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'notifications' AND CONSTRAINT_TYPE = 'PRIMARY KEY');

SET @sql = IF(@has_pk_notifications = 0,
  'ALTER TABLE `notifications` ADD PRIMARY KEY (`id`)',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_pk_accounting_audit_logs = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'accounting_audit_logs' AND CONSTRAINT_TYPE = 'PRIMARY KEY');

SET @sql = IF(@has_pk_accounting_audit_logs = 0,
  'ALTER TABLE `accounting_audit_logs` ADD PRIMARY KEY (`id`)',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_pk_thirteenth_month_pays = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'thirteenth_month_pays' AND CONSTRAINT_TYPE = 'PRIMARY KEY');

SET @sql = IF(@has_pk_thirteenth_month_pays = 0,
  'ALTER TABLE `thirteenth_month_pays` ADD PRIMARY KEY (`id`)',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 3. Payroll accounting fix ----------------------------------------------

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

-- Rows written before this fix have no deduction_source. Give the ones the system
-- creates a value so old rows still make sense.
UPDATE `payroll_deductions` SET `deduction_source` = 'system'
WHERE `deduction_source` IS NULL
  AND (`deduction_type` IN ('SSS', 'Pag-IBIG', 'PhilHealth', 'Withholding Tax', 'Late Deduction')
       OR `deduction_type` LIKE 'Undertime Deduction%');


-- 4. Verify --------------------------------------------------------------
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

-- The 500 fix. All three must say 1.
SELECT
    (SELECT COUNT(*) FROM information_schema.COLUMNS
      WHERE table_schema = DATABASE() AND table_name = 'payroll_deductions'
        AND column_name = 'accounting_treatment') AS accounting_treatment,
    (SELECT COUNT(*) FROM information_schema.COLUMNS
      WHERE table_schema = DATABASE() AND table_name = 'payroll_deductions'
        AND column_name = 'deduction_source')     AS deduction_source,
    (SELECT COUNT(*) FROM information_schema.COLUMNS
      WHERE table_schema = DATABASE() AND table_name = 'thirteenth_month_pays'
        AND column_name = 'deleted_at')           AS thirteenth_month_pays_deleted_at;

-- The payroll page needs these four mappings.
SELECT m.`mapping_key`, c.`account_code`, c.`account_name`
FROM `account_mappings` m
JOIN `chart_of_accounts` c ON c.`id` = m.`account_id`
WHERE m.`mapping_key` IN ('payroll_deductions', 'employee_receivable',
                          'other_payroll_deductions_payable',
                          'employer_contributions_expense')
ORDER BY m.`mapping_key`;

-- Row counts. Write these down BEFORE running the script, then compare.
-- Everything must match, except chart_of_accounts which gains one row (2070).
SELECT 'users' AS tbl, COUNT(*) AS rows_now FROM `users`
UNION ALL SELECT 'drivers',           COUNT(*) FROM `drivers`
UNION ALL SELECT 'paos',              COUNT(*) FROM `paos`
UNION ALL SELECT 'routes',            COUNT(*) FROM `routes`
UNION ALL SELECT 'vehicles',          COUNT(*) FROM `vehicles`
UNION ALL SELECT 'payrolls',          COUNT(*) FROM `payrolls`
UNION ALL SELECT 'attendance',        COUNT(*) FROM `attendance`
UNION ALL SELECT 'daily_remittances', COUNT(*) FROM `daily_remittances`
UNION ALL SELECT 'payroll_deductions',COUNT(*) FROM `payroll_deductions`
UNION ALL SELECT 'chart_of_accounts', COUNT(*) FROM `chart_of_accounts`;

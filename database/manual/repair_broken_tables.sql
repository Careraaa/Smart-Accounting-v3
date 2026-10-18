-- ============================================================================
--  Repair the 5 broken tables in the Smart Accounting databases.
--
--  In both databases these tables have a half-finished CREATE TABLE:
--  missing trailing columns, no PRIMARY KEY. Exporting and importing them
--  fails, and the app breaks when it tries to write to them.
--
--    users                    missing created_at, updated_at, last_login_at
--    payrolls                 missing 25 columns (status, gross_pay, net_pay...)
--    notifications            missing read_at, deleted_at, created_at, updated_at
--    accounting_audit_logs    missing new_values, ip_address, timestamps
--    thirteenth_month_pays    columns are fine, only the primary key is missing
--
--  This only ADDS things. It never drops or changes existing data.
--  SAFE TO RUN MORE THAN ONCE.
--
--  Run it on test1, test2 and main.
--
--  HOW TO USE: phpMyAdmin -> select the database -> SQL tab -> paste -> Go.
-- ============================================================================


-- ── 1. Columns ──────────────────────────────────────────────────────────────
-- MySQL has no "ADD COLUMN IF NOT EXISTS", so use a helper procedure.

DROP PROCEDURE IF EXISTS sa_add_column_if_missing;

DELIMITER $$

CREATE PROCEDURE sa_add_column_if_missing(
    IN p_table      VARCHAR(64),
    IN p_column     VARCHAR(64),
    IN p_definition VARCHAR(255)
)
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME  = p_table
          AND COLUMN_NAME = p_column
    ) THEN
        SET @ddl = CONCAT(
            'ALTER TABLE `', p_table, '` ADD COLUMN `', p_column, '` ', p_definition
        );
        PREPARE stmt FROM @ddl;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END$$

-- Same trick for the primary key.
CREATE PROCEDURE sa_add_pk_if_missing(IN p_table VARCHAR(64))
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.TABLE_CONSTRAINTS
        WHERE CONSTRAINT_SCHEMA = DATABASE()
          AND TABLE_NAME      = p_table
          AND CONSTRAINT_TYPE = 'PRIMARY KEY'
    ) THEN
        SET @ddl = CONCAT('ALTER TABLE `', p_table, '` ADD PRIMARY KEY (`id`)');
        PREPARE stmt FROM @ddl;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END$$

DELIMITER ;


-- ── users ───────────────────────────────────────────────────────────────────
CALL sa_add_column_if_missing('users', 'created_at',    'timestamp NULL DEFAULT NULL');
CALL sa_add_column_if_missing('users', 'updated_at',    'timestamp NULL DEFAULT NULL');
CALL sa_add_column_if_missing('users', 'last_login_at', 'timestamp NULL DEFAULT NULL');

-- ── notifications ───────────────────────────────────────────────────────────
CALL sa_add_column_if_missing('notifications', 'read_at',    'timestamp NULL DEFAULT NULL');
CALL sa_add_column_if_missing('notifications', 'deleted_at', 'timestamp NULL DEFAULT NULL');
CALL sa_add_column_if_missing('notifications', 'created_at', 'timestamp NULL DEFAULT NULL');
CALL sa_add_column_if_missing('notifications', 'updated_at', 'timestamp NULL DEFAULT NULL');

-- ── accounting_audit_logs ───────────────────────────────────────────────────
CALL sa_add_column_if_missing('accounting_audit_logs', 'new_values',  'longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL');
CALL sa_add_column_if_missing('accounting_audit_logs', 'ip_address',  'varchar(45) DEFAULT NULL');
CALL sa_add_column_if_missing('accounting_audit_logs', 'created_at',  'timestamp NULL DEFAULT NULL');
CALL sa_add_column_if_missing('accounting_audit_logs', 'updated_at',  'timestamp NULL DEFAULT NULL');

-- ── payrolls ────────────────────────────────────────────────────────────────
CALL sa_add_column_if_missing('payrolls', 'status',             'varchar(255) NOT NULL DEFAULT ''pending''');
CALL sa_add_column_if_missing('payrolls', 'basic_salary',       'decimal(12,2) NOT NULL DEFAULT 0.00');
CALL sa_add_column_if_missing('payrolls', 'gross_pay',          'decimal(12,2) NOT NULL DEFAULT 0.00');
CALL sa_add_column_if_missing('payrolls', 'holiday_pay',        'decimal(12,2) NOT NULL DEFAULT 0.00');
CALL sa_add_column_if_missing('payrolls', 'holiday_ot_pay',     'decimal(12,2) NOT NULL DEFAULT 0.00');
CALL sa_add_column_if_missing('payrolls', 'holiday_ot_hours',   'decimal(8,2) NOT NULL DEFAULT 0.00');
CALL sa_add_column_if_missing('payrolls', 'holiday_breakdown',  'longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL');
CALL sa_add_column_if_missing('payrolls', 'net_pay',            'decimal(12,2) NOT NULL DEFAULT 0.00');
CALL sa_add_column_if_missing('payrolls', 'days_worked',        'decimal(8,2) NOT NULL DEFAULT 0.00');
CALL sa_add_column_if_missing('payrolls', 'hours_worked',       'decimal(8,2) NOT NULL DEFAULT 0.00');
CALL sa_add_column_if_missing('payrolls', 'payment_date',       'date DEFAULT NULL');
CALL sa_add_column_if_missing('payrolls', 'approved_by',        'bigint(20) unsigned DEFAULT NULL');
CALL sa_add_column_if_missing('payrolls', 'created_at',         'timestamp NULL DEFAULT NULL');
CALL sa_add_column_if_missing('payrolls', 'updated_at',         'timestamp NULL DEFAULT NULL');
CALL sa_add_column_if_missing('payrolls', 'present_days',       'int(10) unsigned NOT NULL DEFAULT 0');
CALL sa_add_column_if_missing('payrolls', 'late_days',          'int(10) unsigned NOT NULL DEFAULT 0');
CALL sa_add_column_if_missing('payrolls', 'absent_days',        'int(10) unsigned NOT NULL DEFAULT 0');
CALL sa_add_column_if_missing('payrolls', 'daily_rate_snapshot','decimal(10,2) NOT NULL DEFAULT 0.00');
CALL sa_add_column_if_missing('payrolls', 'sss',                'decimal(10,2) NOT NULL DEFAULT 0.00');
CALL sa_add_column_if_missing('payrolls', 'pagibig',            'decimal(10,2) NOT NULL DEFAULT 0.00');
CALL sa_add_column_if_missing('payrolls', 'philhealth',         'decimal(10,2) NOT NULL DEFAULT 0.00');
CALL sa_add_column_if_missing('payrolls', 'withholding_tax',    'decimal(12,2) NOT NULL DEFAULT 0.00');
CALL sa_add_column_if_missing('payrolls', 'employer_sss',       'decimal(12,2) DEFAULT NULL');
CALL sa_add_column_if_missing('payrolls', 'employer_pagibig',   'decimal(12,2) DEFAULT NULL');
CALL sa_add_column_if_missing('payrolls', 'employer_philhealth','decimal(12,2) DEFAULT NULL');

DROP PROCEDURE sa_add_column_if_missing;


-- ── 2. Primary keys ─────────────────────────────────────────────────────────
-- These 5 tables have no primary key, so the database cannot tell rows apart.
-- This fails if any table already has duplicate id values, which would mean
-- the table is genuinely damaged and needs a human to look at it.

CALL sa_add_pk_if_missing('users');
CALL sa_add_pk_if_missing('payrolls');
CALL sa_add_pk_if_missing('notifications');
CALL sa_add_pk_if_missing('accounting_audit_logs');
CALL sa_add_pk_if_missing('thirteenth_month_pays');

DROP PROCEDURE sa_add_pk_if_missing;


-- ── 3. Verify ───────────────────────────────────────────────────────────────
-- Both queries must return 5 rows.
SELECT t.table_name,
       IF(EXISTS (
           SELECT 1 FROM information_schema.TABLE_CONSTRAINTS c
           WHERE c.CONSTRAINT_SCHEMA = DATABASE()
             AND c.TABLE_NAME = t.table_name
             AND c.CONSTRAINT_TYPE = 'PRIMARY KEY'
       ), 'has PK', 'STILL BROKEN') AS primary_key
FROM information_schema.TABLES t
WHERE t.TABLE_SCHEMA = DATABASE()
  AND t.TABLE_NAME IN ('users','payrolls','notifications',
                       'accounting_audit_logs','thirteenth_month_pays')
ORDER BY t.table_name;

-- These counts must match what the file repair produced:
--   users 46, payrolls 36, notifications 10,
--   accounting_audit_logs 11, thirteenth_month_pays 17
SELECT table_name, COUNT(*) AS column_count
FROM information_schema.COLUMNS
WHERE table_schema = DATABASE()
  AND table_name IN ('users','payrolls','notifications',
                     'accounting_audit_logs','thirteenth_month_pays')
GROUP BY table_name
ORDER BY table_name;
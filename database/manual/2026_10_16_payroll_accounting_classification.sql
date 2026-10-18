-- ============================================================================
--  Manual SQL for InfinityFree (phpMyAdmin) — there is no SSH on free hosting
--  and the deploy workflow cannot reach the DB, so `php artisan migrate`
--  never runs on the server. See .github/workflows/deploy.yml (the NOTE at
--  the composer step) and DEPLOYMENT.md.
--
--  This mirrors, for the test database:
--    database/migrations/2026_10_16_000001_add_payroll_deductions_account_mapping.php
--    database/migrations/2026_10_16_000002_add_payroll_accounting_classification.php
--    database/migrations/2026_10_16_000003_backfill_payroll_account_mappings.php
--
--  SAFE TO RUN MORE THAN ONCE. Every change is guarded, so re-running does
--  nothing. Run it against BOTH the production and the sandbox database.
--
--  HOW TO USE: phpMyAdmin -> select the database -> SQL tab -> paste -> Go.
-- ============================================================================


-- ── 1. Columns ──────────────────────────────────────────────────────────────
-- MySQL has no "ADD COLUMN IF NOT EXISTS", so use a tiny helper procedure.
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

DELIMITER ;

-- The two that caused the 500 on /payroll/batch/{id}/confirm:
CALL sa_add_column_if_missing('payroll_deductions', 'accounting_treatment', 'VARCHAR(30) NULL');
CALL sa_add_column_if_missing('payroll_deductions', 'deduction_source',     'VARCHAR(20) NULL');

-- Employer contribution share (paid by the company, not withheld).
CALL sa_add_column_if_missing('payrolls', 'employer_sss',        'DECIMAL(12,2) NULL');
CALL sa_add_column_if_missing('payrolls', 'employer_pagibig',    'DECIMAL(12,2) NULL');
CALL sa_add_column_if_missing('payrolls', 'employer_philhealth', 'DECIMAL(12,2) NULL');

-- Marks an account as a contra account (used for wage reductions).
CALL sa_add_column_if_missing('chart_of_accounts', 'is_contra', 'TINYINT(1) NOT NULL DEFAULT 0');

DROP PROCEDURE sa_add_column_if_missing;


-- ── 2. 5060 becomes a contra-expense account ────────────────────────────────
UPDATE chart_of_accounts
SET is_contra   = 1,
    description = 'Contra-expense for valid employee wage reductions; do not use for statutory withholdings or employee receivable recoveries.',
    updated_at  = NOW()
WHERE account_code = '5060';


-- ── 3. 2070 Other Payroll Deductions Payable ─────────────────────────────────
-- WARNING: if account code 2070 is already used for a DIFFERENT account, the
-- Laravel migration refuses to run. Check this before applying:
--
--   SELECT id, account_code, account_name, account_type
--   FROM chart_of_accounts WHERE account_code IN ('2070', '5060', '5050', '1030');
--
INSERT INTO chart_of_accounts
    (account_code, account_name, account_type, description, is_active, created_at, updated_at)
SELECT '2070', 'Other Payroll Deductions Payable', 'Liability',
       'Employee payroll deductions withheld for remittance to third parties',
       1, NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM chart_of_accounts WHERE account_code = '2070'
);


-- ── 4. Account mappings the payroll journal needs ────────────────────────────
-- account_mappings.mapping_key is UNIQUE, so IGNORE makes this safe to re-run.
-- Existing rows are left alone; fix those by hand in the app if needed.

INSERT IGNORE INTO account_mappings (mapping_key, label, account_id, created_at, updated_at)
SELECT 'payroll_deductions', 'Payroll Deductions', id, NOW(), NOW()
FROM chart_of_accounts WHERE account_code = '5060';

INSERT IGNORE INTO account_mappings (mapping_key, label, account_id, created_at, updated_at)
SELECT 'employee_receivable', 'Employee Receivable', id, NOW(), NOW()
FROM chart_of_accounts WHERE account_code = '1030';

INSERT IGNORE INTO account_mappings (mapping_key, label, account_id, created_at, updated_at)
SELECT 'other_payroll_deductions_payable', 'Other Payroll Deductions Payable', id, NOW(), NOW()
FROM chart_of_accounts WHERE account_code = '2070';

INSERT IGNORE INTO account_mappings (mapping_key, label, account_id, created_at, updated_at)
SELECT 'employer_contributions_expense', 'Employer Government Contributions Expense', id, NOW(), NOW()
FROM chart_of_accounts WHERE account_code = '5050';


-- ── 5. Backfill old rows ────────────────────────────────────────────────────
-- Rows written before this fix have no accounting_treatment. Give them a
-- sensible default so the payroll journal can still build.
UPDATE payroll_deductions SET deduction_source = 'system'
WHERE deduction_source IS NULL
  AND (deduction_type IN ('SSS', 'Pag-IBIG', 'PhilHealth', 'Withholding Tax', 'Late Deduction')
       OR deduction_type LIKE 'Undertime Deduction%');


-- ── 6. Verify ───────────────────────────────────────────────────────────────
-- Every row below must return the expected column.
SELECT
    (SELECT COUNT(*) FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_deductions'
        AND COLUMN_NAME = 'accounting_treatment')       AS accounting_treatment_ok,
    (SELECT COUNT(*) FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_deductions'
        AND COLUMN_NAME = 'deduction_source')           AS deduction_source_ok,
    (SELECT COUNT(*) FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payrolls'
        AND COLUMN_NAME = 'employer_sss')               AS employer_sss_ok,
    (SELECT COUNT(*) FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'chart_of_accounts'
        AND COLUMN_NAME = 'is_contra')                 AS is_contra_ok;

SELECT mapping_key, label, c.account_code, c.account_name
FROM account_mappings m
JOIN chart_of_accounts c ON c.id = m.account_id
WHERE m.mapping_key IN ('payroll_deductions', 'employee_receivable',
                        'other_payroll_deductions_payable', 'employer_contributions_expense')
ORDER BY m.mapping_key;

-- The three account codes must line up like this. If 2070 points at some
-- unrelated account, stop and rename it before generating payroll journals.
SELECT account_code, account_name, account_type, is_contra, is_active
FROM chart_of_accounts
WHERE account_code IN ('1030', '2070', '5050', '5060')
ORDER BY account_code;
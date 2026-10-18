-- ============================================================================
-- Manual SQL for MySQL/phpMyAdmin deployments that do not run Laravel
-- migrations. This mirrors:
--   database/migrations/2026_10_18_000001_create_boundary_payments_table.php
--   database/migrations/2026_10_18_000002_backfill_boundary_account_mappings.php
--
-- Safe to re-run when the boundary_payments table is absent or already
-- complete. CREATE TABLE IF NOT EXISTS does not repair a partially-created
-- table; inspect its columns and constraints before rerunning in that case.
--
-- Before running, check that accounts 1010 and 4010 are active and have the
-- expected account types. Existing mapping rows are left unchanged.
-- No receipts or receipt identifiers are used.
--
-- How to use: phpMyAdmin -> select the target database -> SQL tab -> paste ->
-- Go. Run against the production database and any sandbox separately.
-- ============================================================================

-- Preflight: account 1010 must be an active Asset; 4010 an active Revenue.
SELECT account_code, account_name, account_type, is_active
FROM chart_of_accounts
WHERE account_code IN ('1010', '4010')
ORDER BY account_code;

-- Each actual received payment is stored separately and linked to its
-- remittance and its own journal entry. Unique keys protect retries.
CREATE TABLE IF NOT EXISTS `boundary_payments` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `daily_remittance_id` BIGINT UNSIGNED NOT NULL,
    `source_payment_key` VARCHAR(64) NOT NULL,
    `initial_payment_remittance_id` BIGINT UNSIGNED NULL,
    `payment_date` DATE NOT NULL,
    `amount` DECIMAL(15, 2) NOT NULL,
    `paid_by` VARCHAR(16) NOT NULL,
    `payment_kind` VARCHAR(16) NOT NULL,
    `status` VARCHAR(16) NOT NULL DEFAULT 'Posted',
    `recorded_by` BIGINT UNSIGNED NOT NULL,
    `approved_by` BIGINT UNSIGNED NOT NULL,
    `journal_entry_id` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `boundary_payments_source_payment_key_unique` (`source_payment_key`),
    UNIQUE KEY `boundary_payments_initial_payment_remittance_id_unique` (`initial_payment_remittance_id`),
    UNIQUE KEY `boundary_payments_journal_entry_id_unique` (`journal_entry_id`),
    KEY `boundary_payments_daily_remittance_id_payment_date_index` (`daily_remittance_id`, `payment_date`),
    CONSTRAINT `boundary_payments_daily_remittance_id_foreign`
        FOREIGN KEY (`daily_remittance_id`) REFERENCES `daily_remittances` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `boundary_payments_initial_payment_remittance_id_foreign`
        FOREIGN KEY (`initial_payment_remittance_id`) REFERENCES `daily_remittances` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `boundary_payments_recorded_by_foreign`
        FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `boundary_payments_approved_by_foreign`
        FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `boundary_payments_journal_entry_id_foreign`
        FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Match the Laravel mapping backfill: only insert defaults when the account
-- exists with the expected active type. Existing mappings are not overwritten.
INSERT IGNORE INTO `account_mappings`
    (`mapping_key`, `label`, `account_id`, `created_at`, `updated_at`)
SELECT 'boundary_cash', 'Boundary Payment Cash / Bank', `id`, NOW(), NOW()
FROM `chart_of_accounts`
WHERE `account_code` = '1010'
  AND `account_type` = 'Asset'
  AND `is_active` = 1;

INSERT IGNORE INTO `account_mappings`
    (`mapping_key`, `label`, `account_id`, `created_at`, `updated_at`)
SELECT 'boundary_revenue', 'Boundary Revenue', `id`, NOW(), NOW()
FROM `chart_of_accounts`
WHERE `account_code` = '4010'
  AND `account_type` = 'Revenue'
  AND `is_active` = 1;

-- Verify the table was created and that both mappings point to active
-- accounts with the required types. Configure/fix mappings in the application
-- if either row is missing or incorrect.
SHOW CREATE TABLE `boundary_payments`;

SELECT m.mapping_key, m.label, a.account_code, a.account_name,
       a.account_type, a.is_active
FROM `account_mappings` AS m
JOIN `chart_of_accounts` AS a ON a.id = m.account_id
WHERE m.mapping_key IN ('boundary_cash', 'boundary_revenue')
ORDER BY m.mapping_key;

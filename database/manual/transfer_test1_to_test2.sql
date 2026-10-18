-- ============================================================================
--  Copy the CLIENT'S master data from TEST1 into TEST2.
--
--  Test1 (source)  : if0_42738871_smart_accounting_v3_test
--  Test2 (target)  : if0_42738871_smart_accounting_v3_test2
--
--  Transfers: people (users), PAOs, drivers, routes, vehicles, and the
--             reference data they depend on (departments, shifts, rates,
--             holidays, statutory tables, cutoffs).
--  Does NOT transfer: payroll, cash advances, attendance, leaves, OT/UT,
--             daily remittances, journal entries, notifications, logs.
--
--  Original IDs are preserved, so every foreign key stays valid.
--
--  ⚠ RUN THE BACKUP FIRST. Export test2 from phpMyAdmin before running this.
--     test2.zip is a valid backup ONLY if you have not changed test2 since
--     it was exported.
--
--  SAFE TO RUN TWICE?  No. It truncates the target tables every time, so the
--  result is the same each run (idempotent in practice) but anything created
--  in test2 since the last run is lost. Back up first.
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ── 0. Pre-flight check — RUN THIS FIRST, ON ITS OWN ───────────────────────
-- Tables copied with SELECT * must have identical columns in both databases.
-- `routes` is handled explicitly further down and is not listed here.
--
-- This MUST return 0 rows. If it returns rows, STOP: apply the missing
-- migrations to test1 first, then run this script again.
--
-- SELECT a.table_name
-- FROM information_schema.columns a
-- JOIN information_schema.columns b
--   ON b.table_schema = 'if0_42738871_smart_accounting_v3_test'
--  AND b.table_name    = a.table_name
--  AND b.column_name   = a.column_name
-- WHERE a.table_schema = 'if0_42738871_smart_accounting_v3_test2'
--   AND a.table_name IN ('users','drivers','paos','vehicles','departments',
--                        'shifts','position_rates','holidays','leave_types',
--                        'statutory_deductions','withholding_taxes',
--                        'payroll_cutoff_schedules','employee_beneficiaries',
--                        'employee_experiences','employee_references',
--                        'employee_skills','employee_attachments')
-- GROUP BY a.table_name
-- HAVING COUNT(*) <> (
--     SELECT COUNT(*) FROM information_schema.columns c
--     WHERE c.table_schema = 'if0_42738871_smart_accounting_v3_test'
--       AND c.table_name    = a.table_name
-- );

-- ── 1. Clear test2 ──────────────────────────────────────────────────────────
-- Configuration we deliberately keep from test2 (test1 has almost nothing):
--   chart_of_accounts (test1 = 1 row, test2 = 19), account_mappings
--   (test1 = 0 rows, test2 = 12), migrations, cache, cache_locks, settings

-- Transactional — cleared, nothing comes back from test1.
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
TRUNCATE TABLE `leaves`;
TRUNCATE TABLE `overtime_undertimes`;
TRUNCATE TABLE `employee_leave_balances`;
TRUNCATE TABLE `daily_remittances`;

TRUNCATE TABLE `journal_entries`;
TRUNCATE TABLE `journal_entry_lines`;
TRUNCATE TABLE `accounting_audit_logs`;

TRUNCATE TABLE `notifications`;
TRUNCATE TABLE `user_activities`;
TRUNCATE TABLE `pinned_items`;
TRUNCATE TABLE `sessions`;

-- Master data — cleared, then refilled from test1 below.
TRUNCATE TABLE `employee_attachments`;
TRUNCATE TABLE `employee_beneficiaries`;
TRUNCATE TABLE `employee_experiences`;
TRUNCATE TABLE `employee_references`;
TRUNCATE TABLE `employee_skills`;
TRUNCATE TABLE `vehicles`;
TRUNCATE TABLE `routes`;
TRUNCATE TABLE `drivers`;
TRUNCATE TABLE `paos`;
TRUNCATE TABLE `users`;
TRUNCATE TABLE `position_rates`;
TRUNCATE TABLE `departments`;
TRUNCATE TABLE `shifts`;
TRUNCATE TABLE `holidays`;
TRUNCATE TABLE `leave_types`;
TRUNCATE TABLE `statutory_deductions`;
TRUNCATE TABLE `withholding_taxes`;
TRUNCATE TABLE `payroll_cutoff_schedules`;


-- ── 2. Bring the people and their reference data across ────────────────────
-- Parent tables first. IDs are copied as-is.

INSERT INTO `departments` SELECT * FROM `if0_42738871_smart_accounting_v3_test`.`departments`;
INSERT INTO `shifts`      SELECT * FROM `if0_42738871_smart_accounting_v3_test`.`shifts`;

INSERT INTO `users`      SELECT * FROM `if0_42738871_smart_accounting_v3_test`.`users`;
INSERT INTO `drivers`    SELECT * FROM `if0_42738871_smart_accounting_v3_test`.`drivers`;
INSERT INTO `paos`       SELECT * FROM `if0_42738871_smart_accounting_v3_test`.`paos`;

-- routes needs an explicit column list. Test1 still has the old single
-- `boundary` column; test2 has the migration
-- 2026_10_16_000001_replace_route_boundary_with_weekday_and_weekend_rates
-- applied, which replaced it with weekday_boundary + weekend_boundary.
-- This copies the value into both, the same way the migration does.
INSERT INTO `routes` (`id`, `route_name`, `weekday_boundary`, `weekend_boundary`, `origin`, `destination`, `status`, `created_at`, `updated_at`)
SELECT `id`, `route_name`, COALESCE(`boundary`, 0), COALESCE(`boundary`, 0), `origin`, `destination`, `status`, `created_at`, `updated_at`
FROM `if0_42738871_smart_accounting_v3_test`.`routes`;

INSERT INTO `vehicles`   SELECT * FROM `if0_42738871_smart_accounting_v3_test`.`vehicles`;

INSERT INTO `employee_beneficiaries` SELECT * FROM `if0_42738871_smart_accounting_v3_test`.`employee_beneficiaries`;
INSERT INTO `employee_experiences`  SELECT * FROM `if0_42738871_smart_accounting_v3_test`.`employee_experiences`;
INSERT INTO `employee_references`   SELECT * FROM `if0_42738871_smart_accounting_v3_test`.`employee_references`;
INSERT INTO `employee_skills`       SELECT * FROM `if0_42738871_smart_accounting_v3_test`.`employee_skills`;
INSERT INTO `employee_attachments`  SELECT * FROM `if0_42738871_smart_accounting_v3_test`.`employee_attachments`;

INSERT INTO `position_rates`          SELECT * FROM `if0_42738871_smart_accounting_v3_test`.`position_rates`;
INSERT INTO `holidays`                SELECT * FROM `if0_42738871_smart_accounting_v3_test`.`holidays`;
INSERT INTO `leave_types`             SELECT * FROM `if0_42738871_smart_accounting_v3_test`.`leave_types`;
INSERT INTO `statutory_deductions`    SELECT * FROM `if0_42738871_smart_accounting_v3_test`.`statutory_deductions`;
INSERT INTO `withholding_taxes`       SELECT * FROM `if0_42738871_smart_accounting_v3_test`.`withholding_taxes`;
INSERT INTO `payroll_cutoff_schedules` SELECT * FROM `if0_42738871_smart_accounting_v3_test`.`payroll_cutoff_schedules`;

SET FOREIGN_KEY_CHECKS = 1;


-- ── 3. Verify ───────────────────────────────────────────────────────────────
SELECT 'users' t, COUNT(*) n FROM `users`
UNION ALL SELECT 'drivers',    COUNT(*) FROM `drivers`
UNION ALL SELECT 'paos',       COUNT(*) FROM `paos`
UNION ALL SELECT 'routes',     COUNT(*) FROM `routes`
UNION ALL SELECT 'vehicles',   COUNT(*) FROM `vehicles`
UNION ALL SELECT 'departments',COUNT(*) FROM `departments`
UNION ALL SELECT 'shifts',     COUNT(*) FROM `shifts`
UNION ALL SELECT 'payrolls (must be 0)',  COUNT(*) FROM `payrolls`
UNION ALL SELECT 'cash_advances (must be 0)', COUNT(*) FROM `cash_advances`
UNION ALL SELECT 'chart_of_accounts (kept from test2)', COUNT(*) FROM `chart_of_accounts`
UNION ALL SELECT 'account_mappings (kept from test2)', COUNT(*) FROM `account_mappings`;

-- Every user must still be reachable. This must return 0 rows.
SELECT v.id AS orphan_vehicle, v.plate_number
FROM `vehicles` v LEFT JOIN `routes` r ON r.id = v.route_id
WHERE r.id IS NULL;

SELECT u.id AS orphan_user, u.username
FROM `users` u LEFT JOIN `shifts` s ON s.id = u.shift_id
WHERE u.shift_id IS NOT NULL AND s.id IS NULL;

-- Optional: record that these migrations were applied by hand.
-- INSERT INTO `migrations` (`migration`, `batch`) VALUES
--   ('2026_10_16_000002_add_payroll_accounting_classification', 1),
--   ('2026_10_16_000003_backfill_payroll_account_mappings', 1);
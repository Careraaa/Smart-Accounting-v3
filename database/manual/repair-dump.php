<?php
/**
 * Repairs the phpMyAdmin exports of the Smart Accounting databases.
 *
 * Problem found in both exports: 5 tables have a truncated CREATE TABLE
 * (no trailing columns, no ENGINE clause, no PRIMARY KEY) while their
 * INSERT statements reference the full column list. Importing either file
 * as-is throws "Unknown column ... in 'field list'" and leaves a broken
 * schema behind.
 *
 * This script only ADDS the missing pieces. It never drops or edits data.
 *
 * Usage: php repair-dump.php <input.sql> <output.sql>
 */

$in  = $argv[1] ?? null;
$out = $argv[2] ?? null;
if (!$in || !$out || !is_file($in)) {
    fwrite(STDERR, "Usage: php repair-dump.php <input.sql> <output.sql>\n");
    exit(1);
}

$sql = file_get_contents($in);

// Columns missing from the truncated CREATE TABLE statements.
// Types come from database/migrations/* in the repo.
$missing = [
    'accounting_audit_logs' => [
        '`new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL',
        '`ip_address` varchar(45) DEFAULT NULL',
        '`created_at` timestamp NULL DEFAULT NULL',
        '`updated_at` timestamp NULL DEFAULT NULL',
    ],
    'notifications' => [
        '`read_at` timestamp NULL DEFAULT NULL',
        '`deleted_at` timestamp NULL DEFAULT NULL',
        '`created_at` timestamp NULL DEFAULT NULL',
        '`updated_at` timestamp NULL DEFAULT NULL',
    ],
    'payrolls' => [
        '`status` varchar(255) NOT NULL DEFAULT \'pending\'',
        '`basic_salary` decimal(12,2) NOT NULL DEFAULT 0.00',
        '`gross_pay` decimal(12,2) NOT NULL DEFAULT 0.00',
        '`holiday_pay` decimal(12,2) NOT NULL DEFAULT 0.00',
        '`holiday_ot_pay` decimal(12,2) NOT NULL DEFAULT 0.00',
        '`holiday_ot_hours` decimal(8,2) NOT NULL DEFAULT 0.00',
        '`holiday_breakdown` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL',
        '`net_pay` decimal(12,2) NOT NULL DEFAULT 0.00',
        '`days_worked` decimal(8,2) NOT NULL DEFAULT 0.00',
        '`hours_worked` decimal(8,2) NOT NULL DEFAULT 0.00',
        '`payment_date` date DEFAULT NULL',
        '`approved_by` bigint(20) UNSIGNED DEFAULT NULL',
        '`created_at` timestamp NULL DEFAULT NULL',
        '`updated_at` timestamp NULL DEFAULT NULL',
        '`present_days` int(10) unsigned NOT NULL DEFAULT 0',
        '`late_days` int(10) unsigned NOT NULL DEFAULT 0',
        '`absent_days` int(10) unsigned NOT NULL DEFAULT 0',
        '`daily_rate_snapshot` decimal(10,2) NOT NULL DEFAULT 0.00',
        '`sss` decimal(10,2) NOT NULL DEFAULT 0.00',
        '`pagibig` decimal(10,2) NOT NULL DEFAULT 0.00',
        '`philhealth` decimal(10,2) NOT NULL DEFAULT 0.00',
        '`withholding_tax` decimal(12,2) NOT NULL DEFAULT 0.00',
        '`employer_sss` decimal(12,2) DEFAULT NULL',
        '`employer_pagibig` decimal(12,2) DEFAULT NULL',
        '`employer_philhealth` decimal(12,2) DEFAULT NULL',
    ],
    'thirteenth_month_pays' => [
        '`created_at` timestamp NULL DEFAULT NULL',
        '`updated_at` timestamp NULL DEFAULT NULL',
        '`deleted_at` timestamp NULL DEFAULT NULL',
    ],
    'users' => [
        '`created_at` timestamp NULL DEFAULT NULL',
        '`updated_at` timestamp NULL DEFAULT NULL',
        '`last_login_at` timestamp NULL DEFAULT NULL',
    ],
];

// payroll_deductions already has a valid CREATE, but the payroll
// accounting-classification migration was never applied to these
// databases. That missing column is what caused the 500 on
// /payroll/batch/{id}/confirm.
$extraColumns = [
    'payroll_deductions' => [
        '`accounting_treatment` varchar(30) DEFAULT NULL',
        '`deduction_source` varchar(20) DEFAULT NULL',
    ],
];

$log = [];

// ── 1. Repair the truncated CREATE TABLE statements ────────────────────────
foreach ($missing as $table => $cols) {
    $pattern = '/(CREATE TABLE `' . preg_quote($table, '/') . '` \(\n)(.*?)(\n)\) ;/s';

    if (!preg_match($pattern, $sql, $m)) {
        $log[] = "SKIP  $table — truncated CREATE not found";
        continue;
    }

    $body  = $m[2];
    $added = [];

    foreach ($cols as $col) {
        $name = strtok($col, ' ');
        if (preg_match('/^\s*' . preg_quote($name, '/') . '\s/m', $body)) {
            continue; // already present
        }
        $added[] = $col;
    }

    if ($added) {
        $body .= ",\n" . implode(",\n", array_map(fn ($c) => '  ' . $c, $added));
    }

    $engine = "\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    $sql = preg_replace($pattern, '$1' . str_replace('$', '\\$', $body) . $engine, $sql, 1);

    $log[] = sprintf(
        'FIX   %-22s +%d column(s), added ENGINE',
        $table,
        count($added)
    );
}

// ── 2. Append extra columns to tables with a valid CREATE ──────────────────
foreach ($extraColumns as $table => $cols) {
    $pattern = '/(CREATE TABLE `' . preg_quote($table, '/') . '` \(\n)(.*?)(\n\) ENGINE=)/s';

    if (!preg_match($pattern, $sql, $m)) {
        $log[] = "SKIP  $table — CREATE not found";
        continue;
    }

    $body = $m[2];
    $added = [];
    foreach ($cols as $col) {
        $name = strtok($col, ' ');
        if (preg_match('/^\s*' . preg_quote($name, '/') . '\s/m', $body)) {
            continue;
        }
        $added[] = $col;
    }

    if ($added) {
        $body .= ",\n" . implode(",\n", array_map(fn ($c) => '  ' . $c, $added));
    }
    $sql = preg_replace($pattern, '$1' . str_replace('$', '\\$', $body) . '$3', $sql, 1);

    $log[] = sprintf('FIX   %-22s +%d column(s)', $table, count($added));
}

// ── 3. Give the 5 tables a PRIMARY KEY ─────────────────────────────────────
// Their ALTER block only sets AUTO_INCREMENT.
foreach (array_keys($missing) as $table) {
    $pattern = '/ALTER TABLE `' . preg_quote($table, '/') . '`\n  MODIFY `id` bigint\(20\) UNSIGNED NOT NULL AUTO_INCREMENT;/';

    if (!preg_match($pattern, $sql)) {
        $log[] = "SKIP  $table — AUTO_INCREMENT ALTER not found";
        continue;
    }

    $sql = preg_replace(
        $pattern,
        "ALTER TABLE `$table`\n  ADD PRIMARY KEY (`id`),\n  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;",
        $sql,
        1
    );

    $log[] = sprintf('FIX   %-22s +PRIMARY KEY (id)', $table);
}

// ── 4. Wrap in FK checks off so alphabetical order does not break the import
if (!preg_match('/^\s*SET FOREIGN_KEY_CHECKS=0;/m', $sql)) {
    $sql = "SET FOREIGN_KEY_CHECKS=0;\n" . $sql;
}
if (!preg_match('/SET FOREIGN_KEY_CHECKS=1;\s*$/', $sql)) {
    $sql = rtrim($sql) . "\n\nSET FOREIGN_KEY_CHECKS=1;\n";
}

file_put_contents($out, $sql);

echo implode("\n", $log), "\n";
echo "\nwrote: $out\n";
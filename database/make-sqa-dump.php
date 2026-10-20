<?php
/**
 * Build a clean SQA deploy SQL dump.
 * Keeps: all table structures + 5 staff users + reference/lookup data.
 * Strips: all transactional/demo data (payroll, attendance, employees, etc.)
 */

$src = __DIR__ . '/manual/test1_IMPORTABLE_backup_2026-10-19.sql';
$dst = __DIR__ . '/sqa-deploy-structure.sql';

$lines = file($src, FILE_IGNORE_NEW_LINES);
if ($lines === false) {
    die("Cannot read source file\n");
}

$out = [];
$out[] = "-- ===========================================";
$out[] = "-- SQA Deploy Dump (sqa-smartacc-v3)";
$out[] = "-- Generated: " . date('Y-m-d H:i:s');
$out[] = "-- Structure + default staff accounts only";
$out[] = "-- ===========================================";
$out[] = "";
$out[] = "SET NAMES utf8mb4;";
$out[] = "SET FOREIGN_KEY_CHECKS = 0;";
$out[] = "";

// Tables where we keep INSERT data (reference + lookup tables)
$keepDataTables = [
    'users',               // 5 staff accounts only
    'departments',
    'leave_types',
    'shifts',
    'holidays',
    'statutory_deductions',
    'withholding_taxes',
    'payroll_cutoff_schedules',
    'chart_of_accounts',
    'position_rates',
    'account_mappings',
    'paos',
    'settings',
    'pinned_items',
];

$currentTable = '';
$inInsert = false;
$insertBuffer = [];

foreach ($lines as $line) {
    // Detect CREATE TABLE
    if (preg_match('/^CREATE TABLE `([^`]+)`/', $line, $m)) {
        $currentTable = $m[1];
        $inInsert = false;
        $out[] = $line;
        continue;
    }

    // Collect INSERT lines
    if (preg_match('/^INSERT INTO `([^`]+)`/', $line, $m)) {
        $tbl = $m[1];
        if (in_array($tbl, $keepDataTables, true)) {
            $inInsert = true;
            $insertBuffer[] = $line;
        } else {
            $inInsert = false;
        }
        continue;
    }

    // Inside an INSERT we want to keep
    if ($inInsert) {
        // Multi-row INSERT: lines end with ); or continue with values
        $insertBuffer[] = $line;
        // Check if this line ends the INSERT statement
        if (preg_match('/;\s*$/', $line)) {
            // Filter users table: only keep first 5 (staff accounts)
            if ($currentTable === 'users') {
                // Reconstruct and filter
                $full = implode("\n", $insertBuffer);
                // Extract individual value sets
                if (preg_match_all('/\(([^)]*(?:\([^)]*\)[^)]*)*)\)/', $full, $matches)) {
                    $header = preg_replace('/\)$/', '', $insertBuffer[0]); // INSERT INTO ... VALUES
                    // Get just the VALUES part
                    $valuesPart = [];
                    foreach ($matches[1] as $idx => $val) {
                        if ($idx < 5) { // first 5 = staff (IDs 1-5)
                            $valuesPart[] = '(' . $val . ')';
                        }
                    }
                    if (!empty($valuesPart)) {
                        $out[] = $insertBuffer[0];
                        // Split into chunks for readability
                        $chunkSize = 1;
                        $chunks = array_chunk($valuesPart, $chunkSize);
                        foreach ($chunks as $ci => $chunk) {
                            $out[] = implode(",\n", $chunk) . ($ci < count($chunks) - 1 ? ',' : ';');
                        }
                    }
                }
            } else {
                // Keep full INSERT for reference tables
                foreach ($insertBuffer as $bline) {
                    $out[] = $bline;
                }
            }
            $insertBuffer = [];
            $inInsert = false;
        }
        continue;
    }

    // Skip other INSERT lines (data we don't want)
    if (preg_match('/^INSERT INTO/', $line)) {
        continue;
    }

    // Skip ALTER TABLE (we add them at the end)
    if (preg_match('/^ALTER TABLE/', $line)) {
        continue;
    }

    $out[] = $line;
}

// Now append ALTER TABLE statements (foreign keys) at the end
$out[] = "";
$out[] = "-- ===========================================";
$out[] = "-- Foreign key constraints";
$out[] = "-- ===========================================";
$out[] = "";

$alterLines = [];
$collecting = false;
foreach ($lines as $line) {
    if (preg_match('/^ALTER TABLE/', $line)) {
        $collecting = true;
    }
    if ($collecting) {
        $alterLines[] = $line;
        if (preg_match('/;\s*$/', $line)) {
            $collecting = false;
        }
    }
}

foreach ($alterLines as $aline) {
    $out[] = $aline;
}

$out[] = "";
$out[] = "SET FOREIGN_KEY_CHECKS = 1;";
$out[] = "";

file_put_contents($dst, implode("\n", $out));
echo "Created: $dst (" . count($out) . " lines)\n";

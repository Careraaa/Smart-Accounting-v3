<?php
/**
 * Build a clean SQA deploy SQL dump.
 *
 * Source: the latest full dump (manual/test1_READY.sql = newest export with
 * the current structure). Re-run this whenever the source dump changes.
 *
 * Keeps:   every table structure + the 5 staff accounts + reference/lookup data.
 * Strips:  test1 people/operators (paos), the extra employee users, and all
 *          transactional/demo data (payroll, attendance, drivers, ...).
 *
 * The users table is written by hand below because a plain INSERT rewrite
 * breaks on the JSON addresses and escaped quotes inside the value list.
 */

$src = __DIR__ . '/manual/test1_READY.sql';
$dst = __DIR__ . '/sqa-deploy-structure.sql';

if (!is_file($src)) {
    die("Cannot read source file: $src\n");
}

$lines = file($src, FILE_IGNORE_NEW_LINES);
if ($lines === false) {
    die("Cannot read source file\n");
}

// Tables where we keep INSERT data (reference + lookup tables only).
$keepDataTables = [
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
    'settings',
    'pinned_items',
];

/**
 * Split a multi-row INSERT statement into its top-level value tuples.
 * Respects single-quoted strings, backslash escapes and nested parentheses
 * (e.g. the JSON address column and "College (Bachelor's)").
 *
 * @return list<string> tuple contents, without the surrounding parentheses
 */
function extractValueTuples(string $sql): array
{
    $pos = stripos($sql, 'VALUES');
    if ($pos === false) {
        return [];
    }

    $body = rtrim(substr($sql, $pos + 6));
    if (substr($body, -1) === ';') {
        $body = substr($body, 0, -1);
    }

    $tuples = [];
    $cur = '';
    $inString = false;
    $escaped = false;
    $depth = 0;

    for ($i = 0; $i < strlen($body); $i++) {
        $c = $body[$i];

        if ($inString) {
            $cur .= $c;
            if ($escaped) {
                $escaped = false;
            } elseif ($c === '\\') {
                $escaped = true;
            } elseif ($c === "'") {
                $inString = false;
            }
            continue;
        }

        if ($c === "'") {
            $inString = true;
            $cur .= $c;
            continue;
        }
        if ($c === '(') {
            if ($depth === 0) {
                $cur = '';
            } else {
                $cur .= $c;
            }
            $depth++;
            continue;
        }
        if ($c === ')') {
            $depth--;
            if ($depth === 0) {
                $tuples[] = $cur;
                continue;
            }
            $cur .= $c;
            continue;
        }
        if ($depth > 0) {
            $cur .= $c;
        }
    }

    return $tuples;
}

$out = [];
$out[] = "-- ===========================================";
$out[] = "-- SQA Deploy Dump (sqa-smartacc-v3)";
$out[] = "-- Generated: " . date('Y-m-d H:i:s');
$out[] = "-- Source: manual/test1_READY.sql";
$out[] = "-- Structure + 5 staff accounts + reference data only";
$out[] = "-- ===========================================";
$out[] = "";
$out[] = "SET NAMES utf8mb4;";
$out[] = "SET FOREIGN_KEY_CHECKS = 0;";
$out[] = "";

$currentTable = '';
$inInsert = false;
$insertBuffer = [];

foreach ($lines as $line) {
    // Track the table we are inside so we can special-case `users`.
    if (preg_match('/^CREATE TABLE `([^`]+)`/', $line, $m)) {
        $currentTable = $m[1];
        $inInsert = false;
        $out[] = $line;
        continue;
    }

    // Collect INSERT lines (single- or multi-row).
    if (preg_match('/^INSERT INTO `([^`]+)`/', $line, $m)) {
        $tbl = $m[1];
        if ($tbl === 'users' || in_array($tbl, $keepDataTables, true)) {
            $inInsert = true;
            $insertBuffer[] = $line;
        } else {
            $inInsert = false;
        }
        continue;
    }

    if ($inInsert) {
        $insertBuffer[] = $line;
        if (preg_match('/;\s*$/', $line)) {
            if ($currentTable === 'users') {
                $full = implode("\n", $insertBuffer);
                $pos = stripos($full, 'VALUES');
                $header = $pos === false ? '' : substr($full, 0, $pos + 6);
                $tuples = extractValueTuples($full);
                $staff = array_slice($tuples, 0, 5); // 5 system/staff accounts
                if ($header !== '' && $staff !== []) {
                    $out[] = $header;
                    $last = count($staff) - 1;
                    foreach ($staff as $i => $tuple) {
                        $out[] = '(' . $tuple . ')' . ($i < $last ? ',' : ';');
                    }
                }
            } else {
                foreach ($insertBuffer as $bline) {
                    $out[] = $bline;
                }
            }
            $insertBuffer = [];
            $inInsert = false;
        }
        continue;
    }

    // Skip data we do not want.
    if (preg_match('/^INSERT INTO/', $line)) {
        continue;
    }

    // Skip ALTER TABLE (re-appended at the end, if the source has any).
    if (preg_match('/^ALTER TABLE/', $line)) {
        continue;
    }

    $out[] = $line;
}

// Append any standalone ALTER TABLE statements from the source at the end.
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

if ($alterLines !== []) {
    $out[] = "";
    $out[] = "-- ===========================================";
    $out[] = "-- Foreign key constraints";
    $out[] = "-- ===========================================";
    $out[] = "";
    foreach ($alterLines as $aline) {
        $out[] = $aline;
    }
}

$out[] = "";
$out[] = "SET FOREIGN_KEY_CHECKS = 1;";
$out[] = "";

file_put_contents($dst, implode("\n", $out));

// Quick self-check.
$written = file_get_contents($dst);
preg_match_all('/^INSERT INTO `([^`]+)`/m', $written, $m);
$kept = array_values(array_unique($m[1]));
echo "Created: $dst (" . count($out) . " lines)\n";
echo "INSERT tables: " . implode(', ', $kept) . "\n";

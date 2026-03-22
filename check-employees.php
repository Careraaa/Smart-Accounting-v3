<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Employee;

// Exclude superadmin and qr_admin from employee counts
$total = Employee::whereNotIn('role', ['superadmin', 'qr_admin'])->count();
$active = Employee::whereNotIn('role', ['superadmin', 'qr_admin'])->where('status', 'active')->count();
$inactive = Employee::whereNotIn('role', ['superadmin', 'qr_admin'])->where('status', '!=', 'active')->count();

echo "Total employees: " . $total . "\n";
echo "Active employees: " . $active . "\n";
echo "Inactive employees: " . $inactive . "\n";
echo "\nAll employees:\n";
Employee::whereNotIn('role', ['superadmin', 'qr_admin'])->get()->each(function($e) {
    echo $e->id . ': ' . $e->first_name . ' ' . $e->last_name . ' (status: ' . $e->status . ")\n";
});

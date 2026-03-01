<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Employee;

// Exclude superadmin from employee counts
$total = Employee::where('role', '!=', 'superadmin')->count();
$active = Employee::where('role', '!=', 'superadmin')->where('status', 'active')->count();
$inactive = Employee::where('role', '!=', 'superadmin')->where('status', '!=', 'active')->count();

echo "Total employees: " . $total . "\n";
echo "Active employees: " . $active . "\n";
echo "Inactive employees: " . $inactive . "\n";
echo "\nAll employees:\n";
Employee::where('role', '!=', 'superadmin')->get()->each(function($e) {
    echo $e->id . ': ' . $e->first_name . ' ' . $e->last_name . ' (status: ' . $e->status . ")\n";
});

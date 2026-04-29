<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('maintenance:unlock', function () {
    $maintenanceFile = storage_path('maintenance.json');
    $disabledCustom = false;
    $disabledLaravel = false;

    if (file_exists($maintenanceFile)) {
        unlink($maintenanceFile);
        $disabledCustom = true;
    }

    if (app()->isDownForMaintenance()) {
        Artisan::call('up');
        $disabledLaravel = true;
    }

    if (!$disabledCustom && !$disabledLaravel) {
        $this->info('Maintenance mode is already disabled.');
        return;
    }

    $this->info('Maintenance mode unlocked successfully.');
    if ($disabledCustom) {
        $this->line('- Custom maintenance file removed (storage/maintenance.json).');
    }
    if ($disabledLaravel) {
        $this->line('- Laravel native maintenance mode disabled.');
    }
})->purpose('Disable custom and Laravel maintenance mode safely');

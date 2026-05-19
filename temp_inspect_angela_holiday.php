<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('username', 'angela.fernandez')->first();
$batchStart = '2026-05-01';
$batchEnd = '2026-05-15';

$holidays = App\Models\Holiday::whereBetween('date', [$batchStart, $batchEnd])->get();
echo "Holidays: " . count($holidays) . "\n";
foreach ($holidays as $h) {
    echo "  {$h->name} ({$h->type}) {$h->date}\n";
}

$attendance = App\Models\Attendance::where('user_id', $user->id)->whereBetween('date', [$batchStart, $batchEnd])->get();
echo "Attendance records: " . count($attendance) . "\n";
foreach ($attendance as $a) {
    $hours = $a->hours_worked === null ? 'null' : number_format($a->hours_worked,2);
    echo "  {$a->date} status={$a->status} hours={$hours} time_in={$a->time_in} time_out={$a->time_out}\n";
}

$svc = new App\Services\HolidayWageService();
$res = $svc->calculateHolidayWages($user, new Carbon\Carbon($batchStart), new Carbon\Carbon($batchEnd));
var_export($res);
echo "\n";

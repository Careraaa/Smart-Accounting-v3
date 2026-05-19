<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('username', 'angela.fernandez')->first();
if (!$user) {
    echo "Angela not found\n";
    exit(1);
}

echo "User: {$user->id} {$user->first_name} {$user->last_name}\n";
$payrolls = App\Models\Payroll::where('user_id', $user->id)->get();
foreach ($payrolls as $p) {
    echo "Payroll {$p->id} period {$p->payroll_period_start} to {$p->payroll_period_end}\n";
    echo "  basic=".number_format($p->basic_salary,2)." gross=".number_format($p->gross_pay,2)." allow=".number_format($p->total_allowances,2)." deduct=".number_format($p->total_deductions,2)." net=".number_format($p->net_pay,2)."\n";
    $ots = App\Models\OvertimeUndertime::where('user_id',$user->id)->approved()->overtime()->get();
    $uts = App\Models\OvertimeUndertime::where('user_id',$user->id)->approved()->undertime()->get();
    echo "  OT records: ".count($ots)."\n";
    foreach ($ots as $ot) {
        echo "   OT {$ot->date} hrs ".number_format($ot->hours,2)." amt ".number_format($ot->amount,2)."\n";
    }
    echo "  UT records: ".count($uts)."\n";
    foreach ($uts as $ut) {
        echo "   UT {$ut->date} hrs ".number_format($ut->hours,2)." amt ".number_format($ut->amount,2)."\n";
    }
}

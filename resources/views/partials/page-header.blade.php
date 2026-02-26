@php
    $currentRoute = request()->route()->getName() ?? '';
    $menuLabels = [
        'payroll.salary-computation' => ['Payroll Processing', 'Salary Computation'],
        'payroll.statutory-deductions' => ['Payroll Processing', 'Statutory Deductions'],
        'payroll.receivables' => ['Payroll Processing', 'Payroll Receivables'],
        'payroll.generate-payslip' => ['Payroll Processing', 'Generate Payslip'],
        'employees' => ['HR Management', 'Employee Management'],
        'attendance' => ['HR Management', 'Attendance'],
        'reports' => ['Reports'],
        'dashboard' => ['Dashboard'],
    ];
    $breadcrumbs = ['Dashboard'];
    foreach ($menuLabels as $prefix => $labels) {
        if (str_starts_with($currentRoute, $prefix)) {
            $breadcrumbs = $labels;
            break;
        }
    }
    $title = end($breadcrumbs);
@endphp

<div class="kt-page-header">
    <div class="kt-page-header-left">
        <div>
            <h5 class="kt-page-title">{{ $title }}</h5>
            @if (count($breadcrumbs) > 1)
                <nav class="kt-breadcrumb">
                    @foreach ($breadcrumbs as $crumb)
                        @if (!$loop->last)
                            <span class="kt-breadcrumb-item">{{ $crumb }}</span>
                            <span class="kt-breadcrumb-sep">/</span>
                        @else
                            <span class="kt-breadcrumb-item active">{{ $crumb }}</span>
                        @endif
                    @endforeach
                </nav>
            @endif
        </div>
    </div>
</div>
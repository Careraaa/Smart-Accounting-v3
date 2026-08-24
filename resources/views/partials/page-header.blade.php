@php
    // Allow pages to explicitly set a header without editing this partial:
    //   @section('page_title', 'Some Title')
    //   @section('page_breadcrumbs', ['Section', 'Page'])
    //   @section('page_hide_header', true)
    $hideHeader = (bool) trim((string)($__env->yieldContent('page_hide_header')));
    if ($hideHeader) {
        return;
    }

    $explicitTitle = trim((string) $__env->yieldContent('page_title'));
    $explicitCrumbs = $__env->yieldContent('page_breadcrumbs');

    $currentRoute = request()->route()?->getName() ?? '';
    $path = trim((string) request()->path(), '/');

    $startCase = function (string $s): string {
        $s = str_replace(['-', '_', '.'], ' ', $s);
        $s = preg_replace('/\s+/', ' ', $s);
        return trim(ucwords($s));
    };

    $titleFromRoute = function (string $routeName) use ($startCase): array {
        // Returns [breadcrumbsArray, titleString]
        // Examples:
        // - hr.index => ["Dashboard","HR Management","Dashboard"], "Dashboard"
        // - employees.index => ["HR Management","Employee Management"], "Employee Management"
        // - attendance.scan => ["HR Management","Scan QR Attendance"], "Scan QR Attendance"
        $parts = explode('.', $routeName);
        $action = count($parts) ? end($parts) : '';
        $scope = count($parts) > 1 ? $parts[count($parts) - 2] : ($parts[0] ?? '');

        // Hard overrides for key routes (highest signal)
        $hard = [
            'dashboard' => ['Dashboard'],
            'hr.index' => ['HR Management', 'Dashboard'],
            'employee.index' => ['Dashboard'],
            'employee.dashboard' => ['Dashboard'],
            'attendance.scan' => ['Attendance', 'Scan QR Attendance'],
            'attendance.index' => ['HR Management', 'Attendance'],
            'notifications.index' => ['Notifications'],
            'profile.details' => ['Profile'],
            'profile.edit' => ['Profile', 'Edit Profile'],
            'settings.account' => ['Account Settings'],
        ];
        if (isset($hard[$routeName])) {
            $crumbs = $hard[$routeName];
            return [$crumbs, (string) end($crumbs)];
        }

        // Resource-ish titles
        $resourceMap = [
            'employees' => 'Employee Management',
            'attendance' => 'Attendance',
            'leave' => 'Leave Requests',
            'overtime' => 'Overtime / Undertime',
            'payroll' => 'Payroll',
            'reports' => 'Reports',
        ];

        // Common action naming
        $actionLabel = match ($action) {
            'index' => '',
            'create' => 'Create',
            'edit' => 'Edit',
            'show' => 'Details',
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            default => $startCase($action),
        };

        $base = $resourceMap[$scope] ?? $resourceMap[$parts[0] ?? ''] ?? $startCase($scope ?: ($parts[0] ?? ''));
        $title = trim(($actionLabel ? ($actionLabel . ' ') : '') . $base);
        if ($title === '' || strtolower($title) === 'index') {
            $title = $base ?: 'Dashboard';
        }

        // Breadcrumb grouping: try to infer a section
        $section = 'Dashboard';
        if (in_array($scope, ['employees', 'attendance', 'leave', 'overtime'], true)) {
            $section = 'HR Management';
        } elseif (str_starts_with($routeName, 'payroll.')) {
            $section = 'Payroll Processing';
        } elseif (str_starts_with($routeName, 'notifications.')) {
            $section = 'Notifications';
        } elseif (str_starts_with($routeName, 'settings.')) {
            $section = 'Settings';
        } elseif (str_starts_with($routeName, 'profile.')) {
            $section = 'Profile';
        }

        $crumbs = $section === $title ? [$title] : [$section, $title];
        return [$crumbs, $title];
    };

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

    // 1) Page-provided title/breadcrumbs (highest priority)
    if (!empty($explicitTitle)) {
        $title = $explicitTitle;
        $breadcrumbs = is_array($explicitCrumbs) && count($explicitCrumbs)
            ? $explicitCrumbs
            : ['Dashboard', $explicitTitle];
    } else {
        // 2) Route-prefix mapping (current behavior)
        $breadcrumbs = ['Dashboard'];
        foreach ($menuLabels as $prefix => $labels) {
            if ($currentRoute && str_starts_with($currentRoute, $prefix)) {
                $breadcrumbs = $labels;
                break;
            }
        }

        // 3) Route-aware title (prevents "index" titles)
        if ($currentRoute) {
            [$crumbs, $t] = $titleFromRoute($currentRoute);
            // Prefer this smarter title if the old mapping would just say Dashboard
            if ($breadcrumbs === ['Dashboard']) {
                $breadcrumbs = $crumbs;
            }
            $title = $t;
        } else {
            // 4) Last resort: URL path
            $fallbackKey = $path ?: 'dashboard';
            $parts = preg_split('/[\/]+/', $fallbackKey);
            $last = $parts ? end($parts) : $fallbackKey;
            $title = $startCase((string) $last);
            $breadcrumbs = ['Dashboard', $title];
        }
    }
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
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class SearchController extends Controller
{
    /**
     * Pages available per role.
     * Each entry: ['label' => '...', 'route' => '...', 'icon' => 'feather-...']
     * 'superadmin' gets every page from every role.
     */
    private static function pagesForRole(string $role): array
    {
        $shared = [
            ['label' => 'Dashboard',          'route' => 'dashboard',        'icon' => 'feather-airplay'],
            ['label' => 'Scan QR Attendance', 'route' => 'attendance.scan',  'icon' => 'feather-camera'],
            ['label' => 'My Profile',         'route' => 'profile.details',  'icon' => 'feather-user'],
            ['label' => 'My Documents',       'route' => 'employee.attachments.index', 'icon' => 'feather-folder'],
            ['label' => 'Notifications',      'route' => 'notifications.index', 'icon' => 'feather-bell'],
        ];

        $hr = [
            ['label' => 'Employee Management',          'route' => 'employees.index',                    'icon' => 'feather-user-plus'],
            ['label' => 'Leave Types',                  'route' => 'leave-type.index',                   'icon' => 'feather-tag'],
            ['label' => 'Pending Leaves',               'route' => 'leave.pending',                      'icon' => 'feather-calendar'],
            ['label' => 'Approved Leaves',              'route' => 'leave.approved',                     'icon' => 'feather-calendar'],
            ['label' => 'Rejected Leaves',              'route' => 'leave.rejected',                     'icon' => 'feather-calendar'],
            ['label' => 'QR Time IN / OUT Records',     'route' => 'attendance.index',                   'icon' => 'feather-clock'],
            ['label' => 'Overtime / Undertime',         'route' => 'overtime.index',                     'icon' => 'feather-clock'],
            ['label' => 'Payroll Management',           'route' => 'payroll.salary-computation.index',   'icon' => 'feather-dollar-sign'],
            ['label' => 'Statutory Deductions',         'route' => 'payroll.statutory-deductions.index', 'icon' => 'feather-dollar-sign'],
            ['label' => 'Payroll Receivables',          'route' => 'payroll.receivables.index',          'icon' => 'feather-inbox'],
            ['label' => 'Pay Slips',                    'route' => 'payroll.generate-payslip.index',     'icon' => 'feather-file-text'],
            ['label' => 'Payroll Summary',              'route' => 'payroll.history.index',              'icon' => 'feather-bar-chart-2'],
            ['label' => 'Government Contribution Summary', 'route' => 'reports.government-contribution', 'icon' => 'feather-bar-chart-2'],
        ];

        $remittance = [
            ['label' => 'List of Drivers',       'route' => 'drivers.index',           'icon' => 'feather-users'],
            ['label' => 'List of PAO / Conductors', 'route' => 'paos.index',           'icon' => 'feather-users'],
            ['label' => 'Manage Routes',         'route' => 'routes.index',            'icon' => 'feather-map'],
            ['label' => 'Manage Vehicles',       'route' => 'vehicles.index',          'icon' => 'feather-truck'],
            ['label' => 'Record Remittance',     'route' => 'remittances.index',       'icon' => 'feather-activity'],
            ['label' => 'Short Remittance',      'route' => 'short-remittances.index', 'icon' => 'feather-activity'],
            ['label' => 'Remittance Report',     'route' => 'reports.index',           'icon' => 'feather-file-text'],
        ];

        $accountant = [
            ['label' => 'Payroll Release Approval', 'route' => 'payroll-approval.index',    'icon' => 'feather-check-circle'],
            ['label' => 'Remittance Approval',      'route' => 'remittance-approval.index', 'icon' => 'feather-check-circle'],
            ['label' => 'Receivables & Loans',      'route' => 'payroll.receivables.index', 'icon' => 'feather-inbox'],
            ['label' => 'Remittance Reports',       'route' => 'reports.remittance',        'icon' => 'feather-file-text'],
            ['label' => 'Payroll Reports',          'route' => 'reports.payroll',           'icon' => 'feather-file-text'],
        ];

        $employee = [
            ['label' => 'Cash Advances',    'route' => 'employee.cash-advances.index',      'icon' => 'feather-credit-card'],
            ['label' => 'Salary Loans',     'route' => 'employee.salary-loans.index',       'icon' => 'feather-briefcase'],
            ['label' => 'My Leaves',        'route' => 'employee.leaves.index',             'icon' => 'feather-calendar'],
            ['label' => 'OT / UT Requests', 'route' => 'employee.overtime-undertime.index', 'icon' => 'feather-clock'],
        ];

        $superadmin_only = [
            ['label' => 'Accounts',       'route' => 'superadmin.accounts.index', 'icon' => 'feather-briefcase'],
            ['label' => 'Configuration',  'route' => 'configuration.index',       'icon' => 'feather-settings'],
            ['label' => 'QR Monitor',     'route' => 'admin.dashboard',           'icon' => 'feather-camera'],
        ];

        $qr_admin = [
            ['label' => 'QR Monitor', 'route' => 'admin.dashboard', 'icon' => 'feather-camera'],
        ];

        switch ($role) {
            case 'superadmin':
                return array_merge($shared, $superadmin_only, $hr, $remittance, $accountant, $employee, $qr_admin);
            case 'hr':
                return array_merge($shared, $hr);
            case 'remittance_clerk':
                return array_merge($shared, $remittance);
            case 'accountant':
                return array_merge($shared, $accountant);
            case 'employee':
                return array_merge($shared, $employee);
            case 'qr_admin':
                return array_merge($shared, $qr_admin);
            default:
                return $shared;
        }
    }

    public function search(Request $request)
    {
        $query = trim($request->get('q', ''));

        if (strlen($query) < 1) {
            return response()->json(['results' => []]);
        }

        $user = auth()->user();
        $role = $user->role;
        $term = mb_strtolower($query);

        $results = [];

        // ── 1. Page / module suggestions ──────────────────────────────────
        $pages = self::pagesForRole($role);
        foreach ($pages as $page) {
            if (str_contains(mb_strtolower($page['label']), $term)) {
                try {
                    $url = route($page['route']);
                } catch (\Exception $e) {
                    continue;
                }
                $results[] = [
                    'type'     => 'page',
                    'label'    => $page['label'],
                    'icon'     => $page['icon'],
                    'url'      => $url,
                    'subtitle' => 'Page',
                ];
            }
        }

        // ── 2. Employee suggestions (role-gated) ──────────────────────────
        $canSearchEmployees = in_array($role, ['hr', 'superadmin', 'accountant', 'qr_admin', 'employee']);

        if ($canSearchEmployees) {
            $empQuery = User::whereIn('role', ['employee', 'hr', 'accountant', 'remittance_clerk', 'superadmin', 'qr_admin'])
                ->where('status', 'active');

            // Employees can only find themselves
            if ($role === 'employee') {
                $empQuery->where('id', $user->id);
            }

            $like = '%' . $query . '%';
            $users = $empQuery
                ->where(function ($q) use ($like) {
                    $q->where('first_name', 'like', $like)
                      ->orWhere('last_name', 'like', $like)
                      ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", [$like])
                      ->orWhere('email', 'like', $like)
                      ->orWhere('position', 'like', $like)
                      ->orWhere('department', 'like', $like)
                      ->orWhere('username', 'like', $like);
                })
                ->orderBy('first_name')
                ->limit(6)
                ->get(['id', 'first_name', 'last_name', 'email', 'position', 'department', 'role']);

            foreach ($users as $u) {
                $url = '#';
                if (in_array($role, ['hr', 'superadmin', 'accountant', 'qr_admin'])) {
                    try { $url = route('employees.show', $u->id); } catch (\Exception $e) {}
                } elseif ($role === 'employee') {
                    try { $url = route('employee.profile.show'); } catch (\Exception $e) {}
                }

                $initials = strtoupper(
                    substr($u->first_name ?? '', 0, 1) .
                    substr($u->last_name  ?? '', 0, 1)
                );
                $name = trim(($u->first_name ?? '') . ' ' . ($u->last_name ?? ''));

                $results[] = [
                    'type'     => 'employee',
                    'label'    => $name ?: 'Unknown',
                    'initials' => $initials ?: '?',
                    'subtitle' => implode(' · ', array_filter([$u->position, $u->department])),
                    'url'      => $url,
                ];
            }
        }

        // Cap total at 10
        return response()->json(['results' => array_slice($results, 0, 10)]);
    }
}

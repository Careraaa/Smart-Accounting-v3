{{-- Right sidebar (desktop only) --}}
@php
$user = auth()->user();
$pinnedItems = $user ? \App\Models\PinnedItem::where('user_id', $user->id)->orderBy('sort_order')->get() : collect();
$activities = $user ? \App\Models\UserActivity::where('user_id', $user->id)->latest()->take(10)->get() : collect();

// Prune anything beyond the 10 most recent
if ($user) {
    $keepIds = $activities->pluck('id');
    \App\Models\UserActivity::where('user_id', $user->id)->whereNotIn('id', $keepIds)->delete();
}

$role = $user?->role ?? '';
$modules = [
    'hr' => [
        ['label' => 'HR Dashboard', 'url' => route('hr.index')],
        ['label' => 'Employees', 'url' => route('employees.index')],
        ['label' => 'Leave Management', 'url' => route('leave.index')],
        ['label' => 'Holiday Management', 'url' => route('holiday.index')],
        ['label' => 'Attendance', 'url' => route('attendance.index')],
        ['label' => 'Payroll Management', 'url' => route('payroll.salary-computation.index')],
        ['label' => 'Statutory Deductions', 'url' => route('payroll.statutory-deductions.index')],
        ['label' => 'Employee Receivables', 'url' => route('payroll.receivables.index')],
        ['label' => 'Payslips', 'url' => route('payroll.generate-payslip.index')],
        ['label' => 'Payroll Summary', 'url' => route('payroll.history.index')],
        ['label' => 'Bonuses', 'url' => route('bonuses.index')],
        ['label' => 'Government Contribution', 'url' => route('reports.government-contribution')],
        ['label' => 'Settings', 'url' => route('settings.index')],
    ],
    'remittance_clerk' => [
        ['label' => 'RC Dashboard', 'url' => route('remittance-clerk.index')],
        ['label' => 'Drivers', 'url' => route('drivers.index')],
        ['label' => 'PAOs/Conductors', 'url' => route('paos.index')],
        ['label' => 'Routes', 'url' => route('routes.index')],
        ['label' => 'Vehicles', 'url' => route('vehicles.index')],
        ['label' => 'Daily Remittances', 'url' => route('remittances.index')],
        ['label' => 'Short Remittances', 'url' => route('short-remittances.index')],
        ['label' => 'Remittance Report', 'url' => route('reports.index')],
        ['label' => 'Cash Advances', 'url' => route('employee.cash-advances.index')],
        ['label' => 'Salary Loans', 'url' => route('employee.salary-loans.index')],
        ['label' => 'My Leaves', 'url' => route('employee.leaves.index')],
        ['label' => 'My Attendance', 'url' => route('employee.attendance.index')],
        ['label' => 'My Profile', 'url' => route('profile.details')],
        ['label' => 'My Documents', 'url' => route('employee.attachments.index')],
    ],
    'accountant' => [
        ['label' => 'Accountant Dashboard', 'url' => route('accountant.index')],
        ['label' => 'Payroll Approval', 'url' => route('payroll-approval.index')],
        ['label' => 'Remittance Approval', 'url' => route('remittance-approval.index')],
        ['label' => 'Remittance Reports', 'url' => route('reports.remittance')],
        ['label' => 'Payroll Reports', 'url' => route('reports.payroll')],
        ['label' => 'Cash Advances', 'url' => route('employee.cash-advances.index')],
        ['label' => 'Salary Loans', 'url' => route('employee.salary-loans.index')],
        ['label' => 'My Leaves', 'url' => route('employee.leaves.index')],
        ['label' => 'My Attendance', 'url' => route('employee.attendance.index')],
        ['label' => 'My Profile', 'url' => route('profile.details')],
    ],
    'employee' => [
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Scan QR', 'url' => route('attendance.scan')],
        ['label' => 'Cash Advances', 'url' => route('employee.cash-advances.index')],
        ['label' => 'Salary Loans', 'url' => route('employee.salary-loans.index')],
        ['label' => 'My Leaves', 'url' => route('employee.leaves.index')],
        ['label' => 'My OT/UT', 'url' => route('employee.overtime-undertime.index')],
        ['label' => 'My Attendance', 'url' => route('employee.attendance.index')],
        ['label' => 'My Documents', 'url' => route('employee.attachments.index')],
        ['label' => 'My Profile', 'url' => route('profile.details')],
    ],
    'superadmin' => [
        ['label' => 'Super Admin Dashboard', 'url' => route('superadmin.dashboard')],
        ['label' => 'Manage Accounts', 'url' => route('superadmin.accounts.index')],
        ['label' => 'System Config', 'url' => route('configuration.index')],
        ['label' => 'QR Monitor', 'url' => route('admin.dashboard')],
        ['label' => 'Employee Management', 'url' => route('employees.index')],
        ['label' => 'Leave Management', 'url' => route('leave.index')],
        ['label' => 'Holiday Management', 'url' => route('holiday.index')],
        ['label' => 'Attendance', 'url' => route('attendance.index')],
        ['label' => 'Payroll Management', 'url' => route('payroll.salary-computation.index')],
        ['label' => 'Bonuses', 'url' => route('bonuses.index')],
        ['label' => 'Drivers', 'url' => route('drivers.index')],
        ['label' => 'PAOs/Conductors', 'url' => route('paos.index')],
        ['label' => 'Routes', 'url' => route('routes.index')],
        ['label' => 'Vehicles', 'url' => route('vehicles.index')],
        ['label' => 'Daily Remittances', 'url' => route('remittances.index')],
        ['label' => 'Payroll Approval', 'url' => route('payroll-approval.index')],
        ['label' => 'Remittance Approval', 'url' => route('remittance-approval.index')],
        ['label' => 'Remittance Reports', 'url' => route('reports.remittance')],
        ['label' => 'Payroll Reports', 'url' => route('reports.payroll')],
    ],
];
$allModules = $modules[$role] ?? [];
$modulesJson = json_encode($allModules);
// Feather icons matching left sidebar — label keyword => [feather-class, tailwind-color]
$pinIcons = [
    'employee'          => ['feather-user-plus', 'text-emerald-500'],
    'my profile'        => ['feather-user', 'text-indigo-500'],
    'profile'           => ['feather-user', 'text-indigo-500'],
    'leave'             => ['feather-calendar', 'text-violet-500'],
    'holiday'           => ['feather-gift', 'text-rose-500'],
    'attendance'        => ['feather-clock', 'text-cyan-500'],
    'dashboard'         => ['feather-airplay', 'text-blue-500'],
    'payroll'           => ['feather-dollar-sign', 'text-green-600'],
    'payslip'           => ['feather-file', 'text-sky-500'],
    'bonus'             => ['feather-award', 'text-yellow-500'],
    'remittance'        => ['feather-edit', 'text-teal-500'],
    'approval'          => ['feather-check-circle', 'text-indigo-500'],
    'report'            => ['feather-file-text', 'text-sky-500'],
    'setting'           => ['feather-settings', 'text-slate-500'],
    'config'            => ['feather-settings', 'text-slate-500'],
    'driver'            => ['feather-user', 'text-orange-500'],
    'route'             => ['feather-map-pin', 'text-orange-500'],
    'vehicle'           => ['feather-truck', 'text-orange-500'],
    'cash advance'      => ['feather-credit-card', 'text-emerald-500'],
    'salary loan'       => ['feather-briefcase', 'text-amber-500'],
    'my document'       => ['feather-folder', 'text-sky-500'],
    'document'          => ['feather-folder', 'text-sky-500'],
    'scan'              => ['feather-camera', 'text-cyan-500'],
    'overtime'          => ['feather-clock', 'text-cyan-500'],
    'undertime'         => ['feather-clock', 'text-cyan-500'],
    'pao'               => ['feather-users', 'text-orange-500'],
    'super admin'       => ['feather-shield', 'text-pink-500'],
    'account'           => ['feather-shield', 'text-pink-500'],
    'qr'                => ['feather-monitor', 'text-rose-500'],
    'deduction'         => ['feather-shield', 'text-green-600'],
    'receivable'        => ['feather-inbox', 'text-emerald-500'],
    'summary'           => ['feather-bar-chart-2', 'text-green-600'],
    'contribution'      => ['feather-file-text', 'text-sky-500'],
    'my ot'             => ['feather-clock', 'text-cyan-500'],
    'government'        => ['feather-file-text', 'text-sky-500'],
];
$matchIcon = function($item) use ($pinIcons) {
    $label = mb_strtolower($item->label);
    foreach ($pinIcons as $kw => $icon) {
        if (str_contains($label, $kw)) return $icon;
    }
    return ['feather-bookmark', 'text-gray-400'];
};
$pinnedItemsWithIcon = $pinnedItems->map(fn($i) => [
    'url' => $i->url, 'label' => $i->label,
    'icon' => $matchIcon($i)[0], 'color' => $matchIcon($i)[1],
]);
$pinnedItemsJson = json_encode($pinnedItemsWithIcon);
$pinIconsJson = json_encode($pinIcons);
@endphp

<div id="right-sidebar" class="fixed top-0 right-0 z-40 h-dvh w-[280px] bg-white/95 backdrop-blur-xl border-l border-gray-100 flex flex-col shadow-2xl overflow-x-hidden" style="box-shadow:-8px 0 40px rgba(0,0,0,.06)">
    {{-- Header --}}
    <div class="flex items-center justify-between px-4 h-12 border-b border-gray-50 shrink-0">
        <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded-lg bg-gradient-to-br from-amber-400 to-amber-500 flex items-center justify-center shadow-sm">
                <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
            </div>
            <span class="text-xs font-bold text-gray-800 tracking-widest uppercase">Quick Access</span>
        </div>

    </div>

    {{-- Pinned section --}}
    <div id="rs-pinned-section" class="border-b border-gray-50 overflow-hidden flex flex-col" style="flex: 0 0 auto;">
        <div class="px-3 pt-3 pb-2 flex flex-col min-h-0 flex-1">
            <div class="flex items-center justify-between mb-2.5 px-1 shrink-0">
            <div class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Pinned</span>
            </div>
            <span id="rs-pinned-count" class="text-[10px] text-gray-300 font-medium">{{ $pinnedItems->count() }}</span>
            </div>
            {{-- Search modules --}}
            <div class="relative mb-2 shrink-0">
                <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-300 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" id="rs-pin-search" class="w-full h-9 pl-8 pr-3 text-xs bg-gray-50 border border-gray-100 rounded-lg text-gray-600 placeholder-gray-300 focus:outline-none focus:border-gray-200 focus:bg-white focus:shadow-sm transition-all" placeholder="Search modules to pin..." autocomplete="off">
            </div>
            <div id="rs-pin-search-results" class="mb-1 max-h-[160px] overflow-y-auto overflow-x-hidden space-y-0.5 rounded-lg bg-gray-50 p-1.5 shadow-sm border border-gray-100" style="display:none"></div>
            {{-- Pinned list --}}
            <div id="rs-pinned-list" class="space-y-0.5 overflow-y-auto overflow-x-hidden flex-1 min-h-0">
                @forelse($pinnedItemsWithIcon as $item)
                <div class="rs-pinned-item group flex items-center gap-2 px-2.5 py-2 rounded-lg hover:bg-gray-50/80 hover:scale-[1.02] transition-all">
                    <a href="{{ $item['url'] }}" class="flex-1 flex items-center gap-2.5 min-w-0 no-underline">
                        <span class="rs-pin-icon shrink-0 flex items-center justify-center w-6 h-6 {{ $item['color'] }}">
                            <i class="{{ $item['icon'] }}" style="font-size:21px;line-height:1"></i>
                        </span>
                        <span class="text-sm font-medium text-gray-700 truncate leading-tight">{{ $item['label'] }}</span>
                    </a>
                    <a href="#" data-pin-url="{{ $item['url'] }}" data-pin-label="{{ $item['label'] }}" class="shrink-0 flex items-center justify-center w-7 h-7 rounded-lg hover:bg-amber-50 transition-all duration-300 hover:scale-125 active:scale-90 no-underline" title="Unpin">
                        <svg class="w-5 h-5 text-amber-400 transition-all duration-300 group-hover:scale-110" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    </a>
                </div>
                @empty
                <p class="text-[11px] text-gray-300 italic px-1 py-1">No pinned pages yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Drag handle --}}
    <div id="rs-resize-handle" class="shrink-0 h-[16px] cursor-row-resize flex items-center justify-center hover:bg-gray-50/80 active:bg-gray-100 transition-colors relative select-none group" style="margin: -1px 0;">
        <div class="rs-grip flex items-center gap-[5px]">
            <span class="w-[3px] h-[3px] rounded-full bg-gray-300 group-hover:bg-gray-400 transition-colors"></span>
            <span class="w-[3px] h-[3px] rounded-full bg-gray-300 group-hover:bg-gray-400 transition-colors"></span>
            <span class="w-[3px] h-[3px] rounded-full bg-gray-300 group-hover:bg-gray-400 transition-colors"></span>
        </div>
    </div>

    {{-- Activity section --}}
    <div class="flex flex-col min-h-0" id="rs-activity-section" style="flex: 1 1 0%; min-height: 80px;">
        <div class="flex items-center justify-between px-4 pt-3 pb-1.5 shrink-0" id="rs-activity-toggle">
            <div class="flex items-center gap-1.5">
                <svg class="rs-activity-arrow w-2.5 h-2.5 text-gray-300" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5l7 7-7 7"/></svg>
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Recent</span>
            </div>
            @if($activities->isNotEmpty())
            <span class="text-[10px] text-gray-300 font-medium">{{ $activities->count() }}</span>
            @endif
        </div>
        <div id="rs-activity-body">
            <div class="px-3 pb-3">
            @if($activities->isNotEmpty())
                <div class="space-y-1">
                    @foreach($activities as $act)
                    @php
                        $dotColors = [
                            'viewed'   => 'bg-blue-500',
                            'created'  => 'bg-emerald-500',
                            'submitted' => 'bg-emerald-500',
                            'updated'  => 'bg-amber-500',
                            'approved' => 'bg-emerald-500',
                            'rejected' => 'bg-red-500',
                            'deleted'  => 'bg-gray-500',
                        ];
                        $badgeColors = [
                            'viewed'   => 'bg-blue-50 text-blue-600',
                            'created'  => 'bg-emerald-50 text-emerald-600',
                            'submitted' => 'bg-emerald-50 text-emerald-600',
                            'updated'  => 'bg-amber-50 text-amber-600',
                            'approved' => 'bg-emerald-50 text-emerald-600',
                            'rejected' => 'bg-red-50 text-red-500',
                            'deleted'  => 'bg-gray-100 text-gray-500',
                        ];
                        $dot = $dotColors[$act->action] ?? 'bg-gray-400';
                        $badge = $badgeColors[$act->action] ?? 'bg-gray-50 text-gray-400';
                        $isPinned = $pinnedItems->contains('url', $act->url);
                        $actionLabel = $act->action === 'viewed' ? 'visited' : $act->action;
                    @endphp
                    <div class="rs-activity-item group flex items-start gap-2.5 px-2.5 py-2.5 rounded-xl hover:bg-gray-50/80 transition-all border border-transparent hover:border-gray-100" style="transition-delay: {{ min($loop->index * 20, 400) }}ms">
                        <div class="relative mt-1.5 shrink-0">
                            <div class="w-2.5 h-2.5 rounded-full {{ $dot }} ring-2 ring-white shadow-sm"></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1.5">
                                <p class="text-xs font-semibold text-gray-700 leading-tight truncate">{{ $act->subject_label }}</p>
                                <span class="shrink-0 text-[9px] font-semibold uppercase px-1.5 py-0.5 rounded-full {{ $badge }}">{{ $actionLabel }}</span>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-1">{{ $act->created_at->diffForHumans() }}</p>
                        </div>
                        @if($act->action === 'viewed' && $act->url)
                        <a href="#" data-pin-url="{{ $act->url }}" data-pin-label="{{ $act->subject_label }}" class="rs-pin-toggle shrink-0 w-6 h-6 rounded-lg hover:bg-gray-100 flex items-center justify-center transition-all no-underline mt-0.5 {{ $isPinned ? 'text-amber-400' : 'text-gray-300' }}" title="{{ $isPinned ? 'Unpin' : 'Pin' }}">
                            <svg class="w-4 h-4" fill="{{ $isPinned ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        </a>
                        @endif
                    </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center h-full text-center px-6">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center mb-4 shadow-sm">
                        <svg class="w-6 h-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="text-xs text-gray-400 font-semibold">No activity yet</p>
                    <p class="text-[11px] text-gray-300 mt-1 leading-relaxed">Your recent actions will<br>appear here.</p>
                </div>
            @endif
        </div>
    </div>
</div>
</div>

{{-- Toggle arrow --}}
<button id="right-sidebar-toggle" class="fixed top-1/2 -translate-y-1/2 right-0 z-50 w-7 h-14 bg-white/90 backdrop-blur-sm border border-gray-100 border-r-0 rounded-l-xl shadow-sm text-gray-300 hover:text-gray-500 hover:border-gray-200 hover:shadow-md flex items-center justify-center cursor-pointer transition-all duration-200 active:scale-95" type="button" title="Open sidebar">
    <svg class="w-3.5 h-3.5 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.5 5l7 7-7 7"/>
    </svg>
</button>

<script>
var modules = {!! $modulesJson !!};
var pinnedItems = {!! $pinnedItemsJson !!};
var toggleUrl = "{{ route('pinned-items.toggle') }}";
var csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
var pinIcons = {!! $pinIconsJson !!};

function getPinIcon(label, url) {
    var l = (label || '').toLowerCase();
    for (var kw in pinIcons) {
        if (l.indexOf(kw) !== -1) return pinIcons[kw];
    }
    return ['feather-bookmark', 'text-gray-400'];
}

function renderPinnedList() {
    var container = document.getElementById('rs-pinned-list');
    if (!container) return;
    if (pinnedItems.length === 0) {
        container.innerHTML = '<p class="text-[11px] text-gray-300 italic px-1 py-1">No pinned pages yet.</p>';
    } else {
        container.innerHTML = pinnedItems.map(function(p, idx) {
            var icon = (p.icon && p.color) ? {icon: p.icon, color: p.color} : (function(){
                var arr = getPinIcon(p.label, p.url);
                return {icon: arr[0], color: arr[1]};
            })();
            return '<div class="rs-pinned-item group flex items-center gap-2 px-2.5 py-2 rounded-lg hover:bg-gray-50/80 hover:scale-[1.02] transition-all" style="animation:rsSlideIn .35s ease-out both;animation-delay:' + (idx * 40) + 'ms">' +
                '<a href="' + p.url.replace(/"/g, '&quot;') + '" class="flex-1 flex items-center gap-2.5 min-w-0 no-underline">' +
                '<span class="rs-pin-icon shrink-0 flex items-center justify-center w-6 h-6 ' + icon.color + '"><i class="' + icon.icon + '" style="font-size:21px;line-height:1"></i></span>' +
                '<span class="text-sm font-medium text-gray-700 truncate leading-tight">' + p.label.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</span></a>' +
                '<a href="#" data-pin-url="' + p.url.replace(/"/g, '&quot;') + '" data-pin-label="' + p.label.replace(/"/g, '&quot;') + '" class="shrink-0 flex items-center justify-center w-7 h-7 rounded-lg hover:bg-amber-50 transition-all duration-300 hover:scale-125 active:scale-90 no-underline" title="Unpin">' +
                '<svg class="w-5 h-5 text-amber-400 transition-all duration-300 group-hover:scale-110" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg></a></div>';
        }).join('');
    }
    var countEl = document.getElementById('rs-pinned-count');
    if (countEl) countEl.textContent = pinnedItems.length;
}

function updateStarStates(url, pinned) {
    document.querySelectorAll('[data-pin-url="' + url.replace(/"/g, '&quot;') + '"]').forEach(function(el) {
        if (pinned) {
            el.classList.remove('text-gray-300');
            el.classList.add('text-amber-400');
            var svg = el.querySelector('svg');
            if (svg) svg.setAttribute('fill', 'currentColor');
            // Pop animation on the star wrapper
            el.classList.remove('rs-star-pop');
            void el.offsetWidth;
            el.classList.add('rs-star-pop');
            el.addEventListener('animationend', function handler() {
                el.classList.remove('rs-star-pop');
                el.removeEventListener('animationend', handler);
            }, { once: true });
            // Flash animation on search result row
            if (el.classList.contains('rs-search-result')) {
                el.classList.remove('rs-pin-flash');
                void el.offsetWidth;
                el.classList.add('rs-pin-flash');
                el.addEventListener('animationend', function handler() {
                    el.classList.remove('rs-pin-flash');
                    el.removeEventListener('animationend', handler);
                }, { once: true });
            }
        } else {
            el.classList.remove('text-amber-400');
            el.classList.add('text-gray-300');
            var svg = el.querySelector('svg');
            if (svg) svg.setAttribute('fill', 'none');
        }
    });
}

window.togglePin = function(url, label) {
    fetch(toggleUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ url: url, label: label })
    }).then(function(r) { return r.json(); }).then(function(data) {
        var existingIdx = -1;
        for (var i = 0; i < pinnedItems.length; i++) {
            if (pinnedItems[i].url === url) { existingIdx = i; break; }
        }
        if (data.pinned && existingIdx === -1) {
            var icon = getPinIcon(label, url);
            pinnedItems.push({ url: url, label: label, icon: icon[0], color: icon[1] });
        } else if (!data.pinned && existingIdx !== -1) {
            pinnedItems.splice(existingIdx, 1);
        }
        updateStarStates(url, data.pinned);
        renderPinnedList();
    }).catch(function(err) { console.error('Pin toggle failed:', err); });
};

document.addEventListener('DOMContentLoaded', function() {
    var btn = document.getElementById('right-sidebar-toggle');
    var icon = btn ? btn.querySelector('svg') : null;
    var searchInput = document.getElementById('rs-pin-search');
    var searchResults = document.getElementById('rs-pin-search-results');

    function toggleSidebar() {
        var isOpen = document.body.classList.toggle('right-sidebar-open');
        if (icon) icon.style.transform = isOpen ? 'rotate(180deg)' : '';
    }

    if (btn) btn.addEventListener('click', toggleSidebar);

    // Collapsible activity section
    var activityToggle = document.getElementById('rs-activity-toggle');
    var activitySection = document.getElementById('rs-activity-section');
    var activityBody = document.getElementById('rs-activity-body');
    if (activityToggle && activityBody) {
        activityToggle.addEventListener('click', function() {
            var isCollapsed = activitySection.classList.contains('rs-activity-collapsed');
            if (isCollapsed) {
                activityBody.style.maxHeight = activityBody.scrollHeight + 'px';
                activitySection.classList.remove('rs-activity-collapsed');
                activityBody.addEventListener('transitionend', function handler() {
                    activityBody.style.maxHeight = '';
                    activityBody.removeEventListener('transitionend', handler);
                }, { once: true });
            } else {
                activityBody.style.maxHeight = activityBody.scrollHeight + 'px';
                requestAnimationFrame(function() {
                    activityBody.style.maxHeight = '0px';
                    activitySection.classList.add('rs-activity-collapsed');
                });
            }
        });
    }

    // Event delegation for pin toggles (activity stars + unpin buttons)
    document.addEventListener('click', function(e) {
        var el = e.target.closest('[data-pin-url]');
        if (!el) return;
        e.preventDefault();
        window.togglePin(el.dataset.pinUrl, el.dataset.pinLabel || '');
    });

    // Search modules to pin
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            var q = this.value.toLowerCase().trim();
            if (!q) { searchResults.style.display = 'none'; searchResults.innerHTML = ''; return; }

            var matches = modules.filter(function(m) {
                return m.label.toLowerCase().indexOf(q) !== -1;
            });

            if (matches.length === 0) { searchResults.style.display = 'none'; searchResults.innerHTML = ''; return; }

            var pinnedUrls = pinnedItems.map(function(p) { return p.url; });
            searchResults.style.display = 'block';
            searchResults.innerHTML = matches.map(function(m) {
                var pinned = pinnedUrls.indexOf(m.url) !== -1;
                return '<a href="#" data-pin-url="' + m.url.replace(/"/g, '&quot;') + '" data-pin-label="' + m.label.replace(/"/g, '&quot;') + '" class="rs-search-result flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-gray-100 transition-all no-underline cursor-pointer">' +
                    '<span class="flex-1 text-xs text-gray-600 truncate">' + m.label + '</span>' +
                    '<svg class="w-3.5 h-3.5 shrink-0 ' + (pinned ? 'text-amber-400' : 'text-gray-200') + '" fill="' + (pinned ? 'currentColor' : 'none') + '" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>' +
                    '</a>';
            }).join('');
        });
    }
});

// ── Drag-to-resize between pinned & activity sections ──────────────────────
(function() {
    var sidebar = document.getElementById('right-sidebar');
    var pinned = document.getElementById('rs-pinned-section');
    var handle = document.getElementById('rs-resize-handle');
    if (!sidebar || !pinned || !handle) return;

    // Restore saved height or let content determine natural height on first render
    var saved = localStorage.getItem('rs-pinned-height');
    if (saved) {
        pinned.style.height = saved + 'px';
    } else {
        // Let content size naturally, then lock height after next frame
        requestAnimationFrame(function() {
            pinned.style.height = pinned.offsetHeight + 'px';
        });
    }

    var startY, startH;

    function onMove(e) {
        var diff = e.clientY - startY;
        var minH = 80;
        var maxH = sidebar.offsetHeight - 80;
        var newH = Math.max(minH, Math.min(maxH, startH + diff));
        pinned.style.height = newH + 'px';
    }

    function onUp() {
        document.body.classList.remove('select-none', 'cursor-row-resize');
        document.removeEventListener('mousemove', onMove);
        document.removeEventListener('mouseup', onUp);
        localStorage.setItem('rs-pinned-height', pinned.offsetHeight);
    }

    handle.addEventListener('mousedown', function(e) {
        e.preventDefault();
        startY = e.clientY;
        startH = pinned.offsetHeight;
        document.body.classList.add('select-none', 'cursor-row-resize');
        document.addEventListener('mousemove', onMove);
        document.addEventListener('mouseup', onUp);
    });
})();
</script>
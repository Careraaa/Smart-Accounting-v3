@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeSlideUp {
    0%  { opacity:0; transform:translateY(14px); }
    100%{ opacity:1; transform:translateY(0); }
}
@keyframes scaleIn {
    0%  { opacity:0; transform:scale(0.93); }
    100%{ opacity:1; transform:scale(1); }
}
@keyframes slideInLeft {
    0%  { opacity:0; transform:translateX(-10px); }
    100%{ opacity:1; transform:translateX(0); }
}
.ac-page-in  { animation:fadeSlideUp 0.42s cubic-bezier(0.16,1,0.3,1) both; }
.ac-stats-in { animation:scaleIn   0.38s cubic-bezier(0.16,1,0.3,1) both; }
.ac-stats-in:nth-child(1){ animation-delay:.04s; }
.ac-stats-in:nth-child(2){ animation-delay:.09s; }
.ac-stats-in:nth-child(3){ animation-delay:.14s; }
.ac-stats-in:nth-child(4){ animation-delay:.19s; }
.ac-filter-in{ animation:slideInLeft 0.38s cubic-bezier(0.16,1,0.3,1) .1s both; }
.ac-table-in { animation:fadeSlideUp 0.48s cubic-bezier(0.16,1,0.3,1) .15s both; }
.ac-row-hover:hover { background:rgba(99,102,241,0.03); }
[data-ac] input:focus-visible,
[data-ac] select:focus-visible,
[data-ac] button:focus-visible { outline:none !important; }
</style>
@endpush

@section('content')
<div class="max-w-full" data-ac>

    {{-- Flash --}}
    @foreach(['success','error','info'] as $ft)
        @if(session($ft))
        <div class="flex items-center gap-2.5 px-4 py-3 mb-5 rounded-xl text-sm font-medium border
            {{ $ft==='success' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : ($ft==='error' ? 'bg-red-50 border-red-200 text-red-700' : 'bg-blue-50 border-blue-200 text-blue-700') }}"
            style="animation:fadeSlideUp .35s ease both;">
            @if($ft==='success')
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            @else
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
            @endif
            {{ session($ft) }}
        </div>
        @endif
    @endforeach

    {{-- Page header --}}
    <div class="flex items-start justify-between mb-7 flex-wrap gap-4 ac-page-in">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Accounts</h1>
            <p class="text-sm text-gray-400 mt-0.5">Manage user accounts and access control</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('superadmin.accounts.create') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded-xl hover:bg-gray-800 hover:shadow-lg hover:shadow-gray-900/20 active:scale-[0.97] transition-all duration-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Add Account
            </a>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="ac-stats-in bg-white border border-gray-200 rounded-xl px-5 py-4 hover:shadow-md hover:border-indigo-200 transition-all duration-300">
            <div>
                <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">Total</div>
                <div class="text-2xl font-bold text-gray-900 tabular-nums mt-0.5" id="statTotal">{{ $allUsers->count() }}</div>
            </div>
        </div>
        <div class="ac-stats-in bg-white border border-gray-200 rounded-xl px-5 py-4 hover:shadow-md hover:border-emerald-200 transition-all duration-300">
            <div>
                <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">Active</div>
                <div class="text-2xl font-bold text-gray-900 tabular-nums mt-0.5" id="statActive">{{ $allUsers->where('status','active')->count() }}</div>
            </div>
        </div>
        <div class="ac-stats-in bg-white border border-gray-200 rounded-xl px-5 py-4 hover:shadow-md hover:border-gray-300 transition-all duration-300">
            <div>
                <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">Inactive</div>
                <div class="text-2xl font-bold text-gray-900 tabular-nums mt-0.5" id="statInactive">{{ $allUsers->where('status','inactive')->count() }}</div>
            </div>
        </div>
        <div class="ac-stats-in bg-white border border-gray-200 rounded-xl px-5 py-4 hover:shadow-md hover:border-violet-200 transition-all duration-300">
            <div>
                <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">Roles</div>
                <div class="text-2xl font-bold text-gray-900 tabular-nums mt-0.5">{{ $allUsers->pluck('role')->unique()->count() }}</div>
            </div>
        </div>
    </div>

    {{-- Filter bar --}}
    <div class="ac-filter-in flex items-center gap-2.5 mb-4 flex-wrap">
        <div class="relative flex-1 min-w-[200px]">
            <input type="text" id="accSearch" placeholder="Search by name or email&hellip;"
                class="w-full pl-4 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-white text-gray-900 placeholder-gray-400 outline-none transition-all duration-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200/50 hover:border-gray-300">
        </div>
        <select id="accRoleFilter"
            class="px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl bg-white text-gray-700 outline-none transition-all duration-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200/50 hover:border-gray-300 cursor-pointer">
            <option value="">All Roles</option>
            <option value="superadmin">Superadmin</option>
            <option value="hr">HR</option>
            <option value="accountant">Accountant</option>
            <option value="remittance_clerk">Remittance Clerk</option>
            <option value="employee">Employee</option>
            <option value="qr_admin">QR Admin</option>
        </select>
        <select id="accStatusFilter"
            class="px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl bg-white text-gray-700 outline-none transition-all duration-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200/50 hover:border-gray-300 cursor-pointer">
            <option value="">All Statuses</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
    </div>

    {{-- Table --}}
    <div class="ac-table-in bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition-shadow duration-300">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">User</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Email</th>
                        <th class="text-center px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Role</th>
                        <th class="text-center px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="text-center px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Last Login</th>
                        <th class="text-center px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody id="accTbody">
                    @forelse($users as $user)
                        @php
                            $initials = strtoupper(
                                substr($user->first_name ?? 'U', 0, 1) .
                                substr($user->last_name ?? '', 0, 1)
                            );
                            $roleBadge = match($user->role) {
                                'superadmin'      => 'bg-amber-100 text-amber-800 border border-amber-200',
                                'hr'              => 'bg-blue-100 text-blue-800 border border-blue-200',
                                'accountant'      => 'bg-purple-100 text-purple-800 border border-purple-200',
                                'remittance_clerk'=> 'bg-pink-100 text-pink-800 border border-pink-200',
                                'employee'        => 'bg-green-100 text-green-800 border border-green-200',
                                'qr_admin'        => 'bg-indigo-100 text-indigo-800 border border-indigo-200',
                                default           => 'bg-gray-100 text-gray-700 border border-gray-200',
                            };
                            $statusBadge = $user->status === 'active'
                                ? 'bg-emerald-100 text-emerald-800 border border-emerald-200'
                                : 'bg-red-100 text-red-800 border border-red-200';
                            $statusLabel = ucfirst($user->status ?? 'inactive');
                        @endphp
                        <tr class="ac-row-hover border-b border-gray-100 transition-colors duration-150"
                            data-name="{{ strtolower(($user->first_name ?? '') . ' ' . ($user->last_name ?? '') . ' ' . ($user->email ?? '')) }}"
                            data-role="{{ $user->role }}"
                            data-status="{{ $user->status === 'active' ? 'active' : 'inactive' }}">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-500 border border-gray-200 flex-shrink-0 uppercase">{{ $initials }}</div>
                                    <div class="font-semibold text-gray-900">{{ $user->first_name }} {{ $user->last_name }}</div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="font-mono text-sm text-gray-600 tabular-nums">{{ $user->email }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="inline-flex items-center justify-center px-2.5 py-1 text-[0.68rem] font-bold uppercase tracking-wider rounded-full min-w-[120px] whitespace-nowrap {{ $roleBadge }}">
                                    {{ str_replace('_', ' ', $user->role) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[0.68rem] font-bold uppercase tracking-wider rounded-full whitespace-nowrap {{ $statusBadge }}"
                                      data-status-badge>
                                    <span class="w-1.5 h-1.5 rounded-full {{ $user->status === 'active' ? 'bg-emerald-600' : 'bg-red-500' }}"></span>
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="font-mono text-sm text-gray-600 tabular-nums">
                                    @if(isset($user->last_login_at) && $user->last_login_at)
                                        {{ $user->last_login_at->format('M d, Y H:i') }}
                                    @else
                                        <span style="color:#9ca3af;">&mdash;</span>
                                    @endif
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <div class="flex items-center gap-1.5 justify-center">
                                    <a href="{{ route('superadmin.accounts.edit', $user->id) }}" class="w-7 h-7 rounded-lg border border-gray-200 bg-white inline-flex items-center justify-center text-gray-500 hover:bg-gray-100 hover:text-gray-900 hover:border-gray-300 transition-all duration-150 no-underline" title="Edit User">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <a href="{{ route('superadmin.accounts.reset-password', $user->id) }}" class="w-7 h-7 rounded-lg border border-gray-200 bg-white inline-flex items-center justify-center text-gray-500 hover:bg-gray-100 hover:text-gray-900 hover:border-gray-300 transition-all duration-150 no-underline" title="Reset Password">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                    </a>
                                    <label class="relative inline-block w-10 h-5.5 cursor-pointer">
                                        <input type="checkbox" class="sr-only peer" {{ $user->status === 'active' ? 'checked' : '' }} onchange="toggleStatus({{ $user->id }}, this)">
                                        <span class="absolute inset-0 bg-gray-300 rounded-full transition-colors duration-300 peer-checked:bg-emerald-500"></span>
                                        <span class="absolute left-0.5 top-0.5 w-4.5 h-4.5 bg-white rounded-full shadow transition-transform duration-300 peer-checked:translate-x-4.5"></span>
                                    </label>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="flex flex-col items-center justify-center py-14 text-center">
                                    <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mb-3.5 text-gray-300">
                                        <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    </div>
                                    <p class="text-sm font-bold text-gray-600 mb-1.5">No users found</p>
                                    <p class="text-xs text-gray-400">Try adjusting your search or filter criteria</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100 bg-gray-50/50 flex-wrap gap-3">
            <div class="text-xs text-gray-400" id="accPaginationInfo">
                Showing <strong class="text-gray-700">0</strong> users
            </div>
            <nav id="accPaginationNav" class="flex items-center gap-1"></nav>
        </div>
    </div>

</div>

{{-- Confirmation Modal --}}
<div id="saConfirmModal" class="fixed inset-0 hidden items-center justify-center z-[9999]">
    <div id="saConfirmBackdrop" class="absolute inset-0 bg-gray-900/55"></div>
    <div role="dialog" aria-modal="true" aria-labelledby="saConfirmTitle"
         class="relative w-[min(520px,92vw)] bg-white border border-gray-200 rounded-xl shadow-2xl overflow-hidden">
        <div class="px-4 py-4 border-b border-gray-100 flex gap-3 items-start">
            <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86l-8.3 14.4A2 2 0 003.7 21h16.6a2 2 0 001.71-2.74l-8.3-14.4a2 2 0 00-3.42 0z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p id="saConfirmTitle" class="text-sm font-extrabold text-gray-900 -tracking-[0.01em] mb-1">Confirm action</p>
                <p id="saConfirmMessage" class="text-xs text-gray-500 leading-relaxed">Are you sure?</p>
            </div>
        </div>
        <div class="px-4 py-3.5 flex justify-end gap-2.5 flex-wrap">
            <button type="button" id="saConfirmCancel" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-gray-700 border border-gray-200 rounded-xl text-sm font-semibold hover:border-red-500 hover:text-red-500 hover:bg-red-50 transition-all duration-150">Cancel</button>
            <button type="button" id="saConfirmOk" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#c8292a] text-white rounded-xl text-sm font-bold hover:bg-[#a81f20] transition-all duration-150 shadow-lg shadow-red-700/30">Confirm</button>
        </div>
    </div>
</div>

<script>
window.allAccountsData = {!! json_encode($allUsers->map(fn($u) => [
    'id'         => $u->id,
    'firstName'  => $u->first_name,
    'lastName'   => $u->last_name,
    'name'       => strtolower(($u->first_name??'').' '.($u->last_name??'').' '.($u->email??'')),
    'email'      => $u->email,
    'role'       => $u->role,
    'status'     => $u->status,
    'lastLogin'  => $u->last_login_at ? $u->last_login_at->format('M d, Y H:i') : null,
    'initials'   => strtoupper(substr($u->first_name ?? 'U', 0, 1) . substr($u->last_name ?? '', 0, 1)),
])) !!};

const ROLE_BADGE_MAP = {
    superadmin: 'bg-amber-100 text-amber-800 border border-amber-200',
    hr: 'bg-blue-100 text-blue-800 border border-blue-200',
    accountant: 'bg-purple-100 text-purple-800 border border-purple-200',
    remittance_clerk: 'bg-pink-100 text-pink-800 border border-pink-200',
    employee: 'bg-green-100 text-green-800 border border-green-200',
    qr_admin: 'bg-indigo-100 text-indigo-800 border border-indigo-200',
};

(function(){
    const search  = document.getElementById('accSearch');
    const roleF   = document.getElementById('accRoleFilter');
    const statusF = document.getElementById('accStatusFilter');
    const tbody   = document.getElementById('accTbody');
    const PER     = 10;
    let page = 1, filtered = [];

    function applyFilters(){
        const q  = search.value.toLowerCase().trim();
        const rl = roleF.value;
        const st = statusF.value;
        filtered = window.allAccountsData.filter(u =>
            (!q  || u.name.includes(q)) &&
            (!rl || u.role === rl) &&
            (!st || u.status === st)
        );
        page = 1;
        render();
    }

    function render(){
        const start   = (page-1)*PER;
        const pageData= filtered.slice(start, start+PER);
        tbody.innerHTML = '';

        if(!pageData.length){
            if(filtered.length === 0 && window.allAccountsData.length > 0){
                tbody.innerHTML = `<tr><td colspan="6"><div class="flex flex-col items-center justify-center py-14 text-center"><div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mb-3.5 text-gray-300"><svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg></div><p class="text-sm font-bold text-gray-600 mb-1.5">No results found</p><p class="text-xs text-gray-400">Try adjusting your search or filter criteria</p></div></td></tr>`;
            } else if(window.allAccountsData.length === 0){
                tbody.innerHTML = `<tr><td colspan="6"><div class="flex flex-col items-center justify-center py-14 text-center"><div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mb-3.5 text-gray-300"><svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg></div><p class="text-sm font-bold text-gray-600 mb-1.5">No users found</p><p class="text-xs text-gray-400">Try adjusting your search or filter criteria</p></div></td></tr>`;
            }
        } else {
            const editRoute  = '{{ route("superadmin.accounts.edit", ["account"=>"__ID__"]) }}';
            const resetRoute = '{{ route("superadmin.accounts.reset-password", ["account"=>"__ID__"]) }}';
            pageData.forEach(u => {
                const editHref   = editRoute.replace('__ID__', u.id);
                const resetHref  = resetRoute.replace('__ID__', u.id);
                const row = document.createElement('tr');
                row.className = 'ac-row-hover border-b border-gray-100 transition-colors duration-150';
                row.dataset.name   = u.name;
                row.dataset.role   = u.role;
                row.dataset.status = u.status;

                const roleLabel  = u.role.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
                const roleBadge  = ROLE_BADGE_MAP[u.role] || 'bg-gray-100 text-gray-700 border border-gray-200';
                const isActive   = u.status === 'active';
                const statusBadge= isActive ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-red-100 text-red-800 border border-red-200';
                const statusDot  = isActive ? 'bg-emerald-600' : 'bg-red-500';
                const statusLabel= u.status.charAt(0).toUpperCase() + u.status.slice(1);
                const lastLoginHtml = u.lastLogin || '<span style="color:#9ca3af;">&mdash;</span>';

                row.innerHTML = `
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-500 border border-gray-200 flex-shrink-0 uppercase">${u.initials}</div>
                            <div class="font-semibold text-gray-900">${u.firstName} ${u.lastName}</div>
                        </div>
                    </td>
                    <td class="px-5 py-3.5"><span class="font-mono text-sm text-gray-600 tabular-nums">${u.email}</span></td>
                    <td class="px-5 py-3.5 text-center"><span class="inline-flex items-center justify-center px-2.5 py-1 text-[0.68rem] font-bold uppercase tracking-wider rounded-full min-w-[120px] whitespace-nowrap ${roleBadge}">${roleLabel}</span></td>
                    <td class="px-5 py-3.5 text-center"><span class="inline-flex items-center gap-1 px-2.5 py-1 text-[0.68rem] font-bold uppercase tracking-wider rounded-full whitespace-nowrap ${statusBadge}" data-status-badge><span class="w-1.5 h-1.5 rounded-full ${statusDot}"></span>${statusLabel}</span></td>
                    <td class="px-5 py-3.5 text-center"><span class="font-mono text-sm text-gray-600 tabular-nums">${lastLoginHtml}</span></td>
                    <td class="px-5 py-3.5 text-center">
                        <div class="flex items-center gap-1.5 justify-center">
                            <a href="${editHref}" class="w-7 h-7 rounded-lg border border-gray-200 bg-white inline-flex items-center justify-center text-gray-500 hover:bg-gray-100 hover:text-gray-900 hover:border-gray-300 transition-all duration-150 no-underline" title="Edit User">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <a href="${resetHref}" class="w-7 h-7 rounded-lg border border-gray-200 bg-white inline-flex items-center justify-center text-gray-500 hover:bg-gray-100 hover:text-gray-900 hover:border-gray-300 transition-all duration-150 no-underline" title="Reset Password">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                            </a>
                            <label class="relative inline-block w-10 h-5.5 cursor-pointer">
                                <input type="checkbox" class="sr-only peer" ${u.status === 'active' ? 'checked' : ''} onchange="toggleStatus(${u.id}, this)">
                                <span class="absolute inset-0 bg-gray-300 rounded-full transition-colors duration-300 peer-checked:bg-emerald-500"></span>
                                <span class="absolute left-0.5 top-0.5 w-4.5 h-4.5 bg-white rounded-full shadow transition-transform duration-300 peer-checked:translate-x-4.5"></span>
                            </label>
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }
        updatePagination();
    }

    function updatePagination(){
        const total = filtered.length;
        const pages = Math.ceil(total/PER);
        const info  = document.getElementById('accPaginationInfo');
        const nav   = document.getElementById('accPaginationNav');
        if(!info||!nav) return;
        if(total === 0){ info.innerHTML='No accounts to display'; nav.innerHTML=''; return; }
        const s = (page-1)*PER+1, e = Math.min(page*PER, total);
        info.innerHTML = `Showing <strong class="text-gray-700">${s}</strong>&ndash;<strong class="text-gray-700">${e}</strong> of <strong class="text-gray-700">${total}</strong> users`;
        if(pages<=1){ nav.innerHTML=''; return; }

        const btnClass = `flex items-center justify-center w-7 h-7 rounded-lg text-xs font-semibold border transition-all duration-150`;
        const activeClass = `${btnClass} bg-gray-900 text-white border-gray-900`;
        const defClass    = `${btnClass} bg-white text-gray-600 border-gray-200 hover:bg-gray-50 hover:text-gray-900 hover:border-gray-300`;
        const disClass    = `${btnClass} bg-gray-50 text-gray-300 border-gray-100 cursor-not-allowed pointer-events-none`;

        let html = '';
        html += `<button data-p="${page-1}" class="${page===1?disClass:defClass}">&lsaquo;</button>`;
        for(let i=1;i<=pages;i++){
            html += `<button data-p="${i}" class="${i===page?activeClass:defClass}">${i}</button>`;
        }
        html += `<button data-p="${page+1}" class="${page===pages?disClass:defClass}">&rsaquo;</button>`;
        nav.innerHTML = html;
        nav.querySelectorAll('button[data-p]').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                const p = parseInt(btn.dataset.p);
                if(p<1||p>pages) return;
                page = p; render();
            });
        });
    }

    search.addEventListener('input', applyFilters);
    roleF.addEventListener('change', applyFilters);
    statusF.addEventListener('change', applyFilters);
    applyFilters();
})();

const SA_TOGGLE_STATUS_URL = @json(route('superadmin.accounts.toggle-status', ['account' => '__ACCOUNT__']));
const SA_RESET_PASSWORD_URL = @json(route('superadmin.accounts.perform-reset-password', ['account' => '__ACCOUNT__']));

function saUrlFromTemplate(template, accountId) {
    return String(template).replace('__ACCOUNT__', String(accountId));
}

function saConfirm({ title = 'Confirm action', message = 'Are you sure?', confirmText = 'Confirm', cancelText = 'Cancel' } = {}) {
    const modal = document.getElementById('saConfirmModal');
    const backdrop = document.getElementById('saConfirmBackdrop');
    const titleEl = document.getElementById('saConfirmTitle');
    const msgEl = document.getElementById('saConfirmMessage');
    const btnCancel = document.getElementById('saConfirmCancel');
    const btnOk = document.getElementById('saConfirmOk');

    titleEl.textContent = title;
    msgEl.textContent = message;
    btnOk.textContent = confirmText;
    btnCancel.textContent = cancelText;

    modal.style.display = 'flex';

    return new Promise((resolve) => {
        const cleanup = () => {
            modal.style.display = 'none';
            btnCancel.removeEventListener('click', onCancel);
            btnOk.removeEventListener('click', onOk);
            backdrop.removeEventListener('click', onCancel);
            document.removeEventListener('keydown', onKeydown);
        };

        const onCancel = () => { cleanup(); resolve(false); };
        const onOk = () => { cleanup(); resolve(true); };
        const onKeydown = (e) => { if (e.key === 'Escape') onCancel(); };

        btnCancel.addEventListener('click', onCancel);
        btnOk.addEventListener('click', onOk);
        backdrop.addEventListener('click', onCancel);
        document.addEventListener('keydown', onKeydown);
    });
}

function resetPassword(userId, userName) {
    saConfirm({
        title: 'Reset password?',
        message: `Reset password for ${userName}? A temporary password will be generated.`,
        confirmText: 'Reset password',
        cancelText: 'Cancel',
    }).then((ok) => {
        if (!ok) return;

        fetch(saUrlFromTemplate(SA_RESET_PASSWORD_URL, userId), {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`Password reset successful!\nNew password: ${data.password}\n\nPlease share this with the user.`);
                location.reload();
            } else {
                alert('Failed to reset password: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            alert('Error: ' + error.message);
        });
    });
}

function toggleStatus(userId, checkbox) {
    const newStatus = checkbox.checked ? 'active' : 'inactive';
    const row = checkbox.closest('tr');
    const badge = row ? row.querySelector('[data-status-badge]') : null;

    checkbox.disabled = true;

    fetch(saUrlFromTemplate(SA_TOGGLE_STATUS_URL, userId), {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ status: newStatus }),
    })
    .then(async (response) => {
        const contentType = response.headers.get('content-type') || '';
        const raw = await response.text();

        let data = null;
        if (contentType.includes('application/json')) {
            try { data = JSON.parse(raw); } catch (_) { /* fallthrough */ }
        }

        if (!response.ok) {
            const msg =
                (data && (data.message || (data.errors && JSON.stringify(data.errors)))) ||
                raw?.slice(0, 300) ||
                `Request failed (${response.status})`;
            throw new Error(msg);
        }

        if (!data) {
            throw new Error('Unexpected server response. Please try again.');
        }

        if (!data.success) throw new Error(data.message || 'Unknown error');

        const effectiveStatus = (data.status === 'active') ? 'active' : 'inactive';
        if (row) row.setAttribute('data-status', effectiveStatus);

        if (badge) {
            const isActive = effectiveStatus === 'active';
            badge.className = `inline-flex items-center gap-1 px-2.5 py-1 text-[0.68rem] font-bold uppercase tracking-wider rounded-full whitespace-nowrap ${isActive ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-red-100 text-red-800 border border-red-200'}`;
            badge.innerHTML = `<span class="w-1.5 h-1.5 rounded-full ${isActive ? 'bg-emerald-600' : 'bg-red-500'}"></span>${effectiveStatus.charAt(0).toUpperCase() + effectiveStatus.slice(1)}`;
        }

        const userData = window.allAccountsData.find(u => u.id === userId);
        if (userData) userData.status = effectiveStatus;

        document.getElementById('accRoleFilter')?.dispatchEvent(new Event('change'));
        document.getElementById('accStatusFilter')?.dispatchEvent(new Event('change'));
    })
    .catch(error => {
        alert('Error: ' + error.message);
        checkbox.checked = !checkbox.checked;
    })
    .finally(() => {
        checkbox.disabled = false;
    });
}
</script>
@endsection

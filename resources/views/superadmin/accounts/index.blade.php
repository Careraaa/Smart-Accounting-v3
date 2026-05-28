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

/* Role badges */
.sa-role {
    display:inline-flex;align-items:center;justify-content:center;
    padding:3px 10px;min-width:128px;border-radius:20px;
    font-size:0.68rem;font-weight:700;text-transform:capitalize;
    letter-spacing:0.04em;white-space:nowrap;text-align:center;
}
.sa-role.r-superadmin { background:#fef3c7;color:#b45309;border:1px solid #fde68a; }
.sa-role.r-hr { background:#dbeafe;color:#0369a1;border:1px solid #bae6fd; }
.sa-role.r-accountant { background:#f3e8ff;color:#7c3aed;border:1px solid #ddd6fe; }
.sa-role.r-remittance_clerk { background:#fce7f3;color:#be185d;border:1px solid #fbcfe8; }
.sa-role.r-employee { background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0; }
.sa-role.r-qr_admin { background:#f5f3ff;color:#6d28d9;border:1px solid #ddd6fe; }

/* Status badges */
.sa-status { display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;white-space:nowrap; }
.sa-status::before { content:'';width:5px;height:5px;border-radius:50%; }
.sa-status.s-active   { background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0; }
.sa-status.s-active::before { background:#16a34a; }
.sa-status.s-inactive { background:#fff0f0;color:#c8292a;border:1px solid #fecaca; }
.sa-status.s-inactive::before { background:#ef4444; }

/* Toggle switch */
.sa-toggle { position:relative;display:inline-block;width:44px;height:24px; }
.sa-toggle input { opacity:0;width:0;height:0; }
.sa-toggle-slider { position:absolute;cursor:pointer;top:0;left:0;right:0;bottom:0;background-color:#d1d5db;transition:0.3s;border-radius:24px; }
.sa-toggle-slider:before { position:absolute;content:"";height:18px;width:18px;left:3px;bottom:3px;background-color:#fff;transition:0.3s;border-radius:50%; }
.sa-toggle input:checked + .sa-toggle-slider { background-color:#16a34a; }
.sa-toggle input:checked + .sa-toggle-slider:before { transform:translateX(20px); }

/* Action buttons */
.prl-actions { display:flex;gap:6px;align-items:center; }
.prl-action-btn { width:30px;height:30px;border-radius:7px;border:1px solid #e5e7eb;background:#fff;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;text-decoration:none;color:#6b7280;transition:all 0.13s;padding:0; }
.prl-action-btn:hover { background:#f4f5f7;color:#111827;border-color:#d1d5db; }

/* Employee cell */
.prl-emp-cell { display:flex;align-items:center;gap:10px; }
.prl-emp-avatar { width:32px;height:32px;border-radius:50%;background:#f3f4f6;display:flex;align-items:center;justify-content:center;font-size:0.72rem;font-weight:700;color:#6b7280;flex-shrink:0;border:1.5px solid #e5e7eb;text-transform:uppercase; }
.prl-emp-name { font-weight:600;color:#111827;font-size:0.845rem; }
.prl-mono { font-family:'DM Mono',monospace;font-size:0.82rem;font-variant-numeric:tabular-nums; }

/* Empty state */
.ac-empty { display:flex;flex-direction:column;align-items:center;justify-content:center;padding:56px 24px;text-align:center; }
.ac-empty-icon { width:56px;height:56px;background:#f3f4f6;border-radius:16px;display:flex;align-items:center;justify-content:center;margin-bottom:14px;color:#d1d5db; }
.ac-empty-title { font-size:0.9rem;font-weight:700;color:#374151;margin:0 0 6px; }
.ac-empty-sub   { font-size:0.78rem;color:#9ca3af;margin:0; }

/* Modal buttons */
.prl-btn-generate {
    display:inline-flex;align-items:center;gap:10px;padding:11px 22px;background:#c8292a;color:#fff;
    border:none;border-radius:12px;font-family:'Sora',sans-serif;font-size:0.86rem;font-weight:700;
    cursor:pointer;transition:background 0.15s,box-shadow 0.15s;
    box-shadow:0 4px 20px rgba(200,41,42,0.45);white-space:nowrap;text-decoration:none;
}
.prl-btn-generate:hover { background:#a81f20;color:#fff;box-shadow:0 8px 28px rgba(200,41,42,0.55); }
.prl-btn-cancel {
    display:inline-flex;align-items:center;gap:6px;padding:10px 18px;background:#fff;color:#374151;border:1px solid #e5e7eb;border-radius:10px;
    font-family:'Sora',sans-serif;font-size:0.82rem;font-weight:600;text-decoration:none;cursor:pointer;transition:all 0.15s;
}
.prl-btn-cancel:hover { border-color:#c8292a;color:#c8292a;background:#fff5f5; }
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
            <input type="text" id="accSearch" placeholder="Search by name or email…"
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
                            $roleClass = 'r-' . $user->role;
                            $statusClass = $user->status === 'active' ? 's-active' : 's-inactive';
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
                                <span class="sa-role {{ $roleClass }}">
                                    {{ str_replace('_', ' ', $user->role) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="sa-status {{ $statusClass }}" data-status-badge>
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="font-mono text-sm text-gray-600 tabular-nums">
                                    @if(isset($user->last_login_at) && $user->last_login_at)
                                        {{ $user->last_login_at->format('M d, Y H:i') }}
                                    @else
                                        <span style="color:#9ca3af;">—</span>
                                    @endif
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <div class="prl-actions" style="justify-content:center;">
                                    <a href="{{ route('superadmin.accounts.edit', $user->id) }}" class="prl-action-btn" title="Edit User">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <a href="{{ route('superadmin.accounts.reset-password', $user->id) }}" class="prl-action-btn" title="Reset Password">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                    </a>
                                    <label class="sa-toggle">
                                        <input type="checkbox" {{ $user->status === 'active' ? 'checked' : '' }} onchange="toggleStatus({{ $user->id }}, this)">
                                        <span class="sa-toggle-slider"></span>
                                    </label>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="ac-empty">
                                    <div class="ac-empty-icon">
                                        <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    </div>
                                    <p class="ac-empty-title">No users found</p>
                                    <p class="ac-empty-sub">Try adjusting your search or filter criteria</p>
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

{{-- Confirmation Modal (custom, replaces browser confirm) --}}
<div id="saConfirmModal" style="position:fixed;inset:0;display:none;align-items:center;justify-content:center;z-index:9999;">
    <div id="saConfirmBackdrop" style="position:absolute;inset:0;background:rgba(17,24,39,0.55);"></div>
    <div role="dialog" aria-modal="true" aria-labelledby="saConfirmTitle"
         style="position:relative;width:min(520px,92vw);background:#fff;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 20px 60px rgba(0,0,0,0.25);overflow:hidden;">
        <div style="padding:16px 18px;border-bottom:1px solid #f3f4f6;display:flex;gap:12px;align-items:flex-start;">
            <div style="width:36px;height:36px;border-radius:10px;background:#fffbeb;color:#d97706;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86l-8.3 14.4A2 2 0 003.7 21h16.6a2 2 0 001.71-2.74l-8.3-14.4a2 2 0 00-3.42 0z"/>
                </svg>
            </div>
            <div style="flex:1;min-width:0;">
                <p id="saConfirmTitle" style="margin:0 0 4px;font-size:0.92rem;font-weight:800;color:#111827;letter-spacing:-0.01em;">Confirm action</p>
                <p id="saConfirmMessage" style="margin:0;font-size:0.82rem;color:#6b7280;line-height:1.45;">Are you sure?</p>
            </div>
        </div>
        <div style="padding:14px 18px;display:flex;justify-content:flex-end;gap:10px;flex-wrap:wrap;">
            <button type="button" id="saConfirmCancel" class="prl-btn-cancel">Cancel</button>
            <button type="button" id="saConfirmOk" class="prl-btn-generate" style="box-shadow:none;">Confirm</button>
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
                tbody.innerHTML = `<tr><td colspan="6"><div class="ac-empty"><div class="ac-empty-icon"><svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg></div><p class="ac-empty-title">No results found</p><p class="ac-empty-sub">Try adjusting your search or filter criteria</p></div></td></tr>`;
            } else if(window.allAccountsData.length === 0){
                tbody.innerHTML = `<tr><td colspan="6"><div class="ac-empty"><div class="ac-empty-icon"><svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg></div><p class="ac-empty-title">No users found</p><p class="ac-empty-sub">Try adjusting your search or filter criteria</p></div></td></tr>`;
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
                const roleClass  = 'r-' + u.role;
                const statusClass= u.status === 'active' ? 's-active' : 's-inactive';
                const statusLabel= u.status.charAt(0).toUpperCase() + u.status.slice(1);
                const lastLoginHtml = u.lastLogin || '<span style="color:#9ca3af;">—</span>';

                row.innerHTML = `
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-500 border border-gray-200 flex-shrink-0 uppercase">${u.initials}</div>
                            <div class="font-semibold text-gray-900">${u.firstName} ${u.lastName}</div>
                        </div>
                    </td>
                    <td class="px-5 py-3.5"><span class="font-mono text-sm text-gray-600 tabular-nums">${u.email}</span></td>
                    <td class="px-5 py-3.5 text-center"><span class="sa-role ${roleClass}">${roleLabel}</span></td>
                    <td class="px-5 py-3.5 text-center"><span class="sa-status ${statusClass}" data-status-badge>${statusLabel}</span></td>
                    <td class="px-5 py-3.5 text-center"><span class="font-mono text-sm text-gray-600 tabular-nums">${lastLoginHtml}</span></td>
                    <td class="px-5 py-3.5 text-center">
                        <div class="prl-actions" style="justify-content:center;">
                            <a href="${editHref}" class="prl-action-btn" title="Edit User">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <a href="${resetHref}" class="prl-action-btn" title="Reset Password">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                            </a>
                            <label class="sa-toggle">
                                <input type="checkbox" ${u.status === 'active' ? 'checked' : ''} onchange="toggleStatus(${u.id}, this)">
                                <span class="sa-toggle-slider"></span>
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
        info.innerHTML = `Showing <strong class="text-gray-700">${s}</strong>–<strong class="text-gray-700">${e}</strong> of <strong class="text-gray-700">${total}</strong> users`;
        if(pages<=1){ nav.innerHTML=''; return; }

        const btnClass = `flex items-center justify-center w-7 h-7 rounded-lg text-xs font-semibold border transition-all duration-150`;
        const activeClass = `${btnClass} bg-gray-900 text-white border-gray-900`;
        const defClass    = `${btnClass} bg-white text-gray-600 border-gray-200 hover:bg-gray-50 hover:text-gray-900 hover:border-gray-300`;
        const disClass    = `${btnClass} bg-gray-50 text-gray-300 border-gray-100 cursor-not-allowed pointer-events-none`;

        let html = '';
        html += `<button data-p="${page-1}" class="${page===1?disClass:defClass}">‹</button>`;
        for(let i=1;i<=pages;i++){
            html += `<button data-p="${i}" class="${i===page?activeClass:defClass}">${i}</button>`;
        }
        html += `<button data-p="${page+1}" class="${page===pages?disClass:defClass}">›</button>`;
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

// Route templates (avoid hardcoded paths; fixes subfolder / tunnel deployments)
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
            badge.textContent = effectiveStatus.charAt(0).toUpperCase() + effectiveStatus.slice(1);
            badge.classList.remove('s-active', 's-inactive');
            badge.classList.add(effectiveStatus === 'active' ? 's-active' : 's-inactive');
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

@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
.acc-page { font-family: 'Sora', sans-serif; }

/* ── Topbar ─────────────────────────────────────────────────── */
.acc-topbar { display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;flex-wrap:wrap; }
.acc-topbar-title { font-size:1.35rem;font-weight:800;color:#111827;letter-spacing:-0.02em;margin:0 0 2px; }
.acc-topbar-sub   { font-size:0.78rem;color:#9ca3af;margin:0; }
.acc-topbar-actions { display:flex;gap:8px;flex-wrap:wrap;align-items:center; }

.acc-btn-primary {
    display:inline-flex;align-items:center;gap:7px;padding:9px 18px;
    background:#111827;color:#fff;border:none;border-radius:10px;
    font-family:'Sora',sans-serif;font-size:0.82rem;font-weight:600;
    text-decoration:none;cursor:pointer;transition:background 0.15s;white-space:nowrap;
}
.acc-btn-primary:hover { background:#000;color:#fff; }

/* ── Flash messages ─────────────────────────────────────────── */
.acc-flash { display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:10px;font-size:0.82rem;font-weight:500;margin-bottom:20px;animation:flashIn 0.3s ease; }
.acc-flash.success { background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d; }
.acc-flash.error   { background:#fff0f0;border:1px solid #fecaca;color:#c8292a; }
.acc-flash.info    { background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8; }
@keyframes flashIn { from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)} }

/* ── Filter bar ─────────────────────────────────────────────── */
.acc-filter-bar { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:12px 16px;display:flex;align-items:center;gap:10px;margin-bottom:14px;flex-wrap:wrap; }
.acc-search-wrap { position:relative;flex:1;min-width:180px; }
.acc-search-wrap svg { position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#9ca3af;pointer-events:none; }
.acc-search-input { width:100%;border:1px solid #e5e7eb;border-radius:8px;padding:8px 12px 8px 34px;font-size:0.82rem;font-family:'Sora',sans-serif;color:#111827;background:#f9fafb;outline:none;transition:border-color 0.15s,background 0.15s; }
.acc-search-input:focus { border-color:#c8292a;background:#fff;box-shadow:0 0 0 3px rgba(200,41,42,0.08); }
.acc-filter-select { border:1px solid #e5e7eb;border-radius:8px;padding:8px 12px;font-size:0.82rem;font-family:'Sora',sans-serif;color:#374151;background:#f9fafb;outline:none;cursor:pointer;transition:border-color 0.15s; }
.acc-filter-select:focus { border-color:#c8292a; }

/* ── Table card ─────────────────────────────────────────────── */
.acc-section-head  { display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;flex-wrap:wrap;gap:10px; }
.acc-section-title { font-size:0.88rem;font-weight:700;color:#111827;margin:0;display:flex;align-items:center;gap:8px; }
.acc-dot { width:8px;height:8px;border-radius:50%;background:#c8292a;display:inline-block; }

.acc-table-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden; }
.acc-table-scroll { overflow-x:auto; }
.acc-table { width:100%;border-collapse:collapse;font-size:0.835rem; }
.acc-table thead tr { background:#f8f9fb;border-bottom:1px solid #e5e7eb; }
.acc-table thead th { padding:11px 16px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#6b7280;white-space:nowrap;font-family:'Sora',sans-serif; }
.acc-table tbody tr { border-bottom:1px solid #f3f4f6;transition:background 0.1s; }
.acc-table tbody tr:last-child { border-bottom:none; }
.acc-table tbody tr:hover { background:#fafafa; }
.acc-table tbody td { padding:12px 16px;color:#374151;vertical-align:middle; }

/* User cell */
.acc-user-cell   { display:flex;align-items:center;gap:10px; }
.acc-user-avatar { width:32px;height:32px;border-radius:50%;background:#f3f4f6;display:flex;align-items:center;justify-content:center;font-size:0.72rem;font-weight:700;color:#6b7280;flex-shrink:0;border:1.5px solid #e5e7eb;text-transform:uppercase; }
.acc-user-name   { font-weight:600;color:#111827;font-size:0.845rem; }

/* Mono values */
.acc-mono { font-family:'DM Mono',monospace;font-size:0.82rem;font-variant-numeric:tabular-nums; }

/* Status badges */
.acc-status { display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;white-space:nowrap; }
.acc-status::before { content:'';width:5px;height:5px;border-radius:50%; }
.acc-status.s-active     { background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0; }
.acc-status.s-active::before { background:#16a34a; }
.acc-status.s-inactive   { background:#fff0f0;color:#c8292a;border:1px solid #fecaca; }
.acc-status.s-inactive::before { background:#ef4444; }

/* Role badges */
.acc-role { display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:700;text-transform:capitalize;letter-spacing:0.06em;white-space:nowrap; }
.acc-role.r-superadmin { background:#fef3c7;color:#d97706;border:1px solid #fde68a; }
.acc-role.r-hr { background:#dbeafe;color:#0284c7;border:1px solid #bae6fd; }
.acc-role.r-accountant { background:#f3e8ff;color:#7c3aed;border:1px solid #ddd6fe; }
.acc-role.r-remittance_clerk { background:#fce7f3;color:#db2777;border:1px solid #fbcfe8; }
.acc-role.r-employee { background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0; }
.acc-role.r-qr_admin { background:#f5f3ff;color:#7c3aed;border:1px solid #ddd6fe; }

/* Action buttons */
.acc-actions { display:flex;gap:6px;align-items:center; }
.acc-btn-sm { display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:6px;border:1px solid #e5e7eb;background:#fff;cursor:pointer;transition:all 0.15s;color:#6b7280; }
.acc-btn-sm:hover { background:#f3f4f6;color:#111827;border-color:#d1d5db; }
.acc-btn-sm svg { width:14px;height:14px; }

/* Toggle switch */
.acc-toggle { position:relative;display:inline-block;width:44px;height:24px; }
.acc-toggle input { opacity:0;width:0;height:0; }
.acc-toggle-slider { position:absolute;cursor:pointer;top:0;left:0;right:0;bottom:0;background-color:#ccc;transition:0.3s;border-radius:24px; }
.acc-toggle-slider:before { position:absolute;content:"";height:18px;width:18px;left:3px;bottom:3px;background-color:#fff;transition:0.3s;border-radius:50%; }
input:checked + .acc-toggle-slider { background-color:#16a34a; }
input:checked + .acc-toggle-slider:before { transform:translateX(20px); }

/* Empty state */
.acc-empty { display:flex;flex-direction:column;align-items:center;justify-content:center;padding:56px 24px;text-align:center; }
.acc-empty-icon  { width:56px;height:56px;background:#f3f4f6;border-radius:16px;display:flex;align-items:center;justify-content:center;margin-bottom:14px;color:#d1d5db; }
.acc-empty-title { font-size:0.9rem;font-weight:700;color:#374151;margin:0 0 6px; }
.acc-empty-sub   { font-size:0.78rem;color:#9ca3af;margin:0; }

/* Pagination strip */
.acc-pagination-strip { display:flex;justify-content:space-between;align-items:center;padding:12px 16px;border-top:1px solid #f3f4f6;background:#fafafa; }
.acc-pagination-info  { font-size:0.75rem;color:#9ca3af; }
.acc-pagination-info strong { color:#374151; }
</style>
@endpush

@section('content')
<div class="acc-page">

    {{-- Flash messages --}}
    @foreach(['success','error','info'] as $t)
        @if(session($t))
        <div class="acc-flash {{ $t }}">
            @if($t==='success')
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            @else
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
            @endif
            {{ session($t) }}
        </div>
        @endif
    @endforeach

    {{-- Topbar --}}
    <div class="acc-topbar">
        <div>
            <h1 class="acc-topbar-title">Accounts</h1>
            <p class="acc-topbar-sub">Manage user accounts and access control</p>
        </div>
        <div class="acc-topbar-actions">
            <a href="{{ route('superadmin.accounts.create') }}" class="acc-btn-primary">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Add Account
            </a>
        </div>
    </div>

    {{-- Section header + filter --}}
    <div class="acc-section-head">
        <h2 class="acc-section-title"><span class="acc-dot"></span> All Users</h2>
    </div>

    <div class="acc-filter-bar">
        <div class="acc-search-wrap">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
            <input type="text" class="acc-search-input" id="accSearch" placeholder="Search user…">
        </div>
        <select class="acc-filter-select" id="accRoleFilter">
            <option value="">All Roles</option>
            <option value="superadmin">Superadmin</option>
            <option value="hr">HR</option>
            <option value="accountant">Accountant</option>
            <option value="remittance_clerk">Remittance Clerk</option>
            <option value="employee">Employee</option>
            <option value="qr_admin">QR Admin</option>
        </select>
        <select class="acc-filter-select" id="accStatusFilter">
            <option value="">All Statuses</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
    </div>

    {{-- Table --}}
    <div class="acc-table-card">
        <div class="acc-table-scroll">
            <table class="acc-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="accTbody">
                    @forelse($users as $user)
                        @php
                            $initials = strtoupper(
                                substr($user->first_name ?? 'U', 0, 1) .
                                substr($user->last_name ?? '', 0, 1)
                            );
                            $roleClass = 'r-' . str_replace('_', '-', $user->role);
                            $statusClass = $user->status === 'active' ? 's-active' : 's-inactive';
                            $statusLabel = ucfirst($user->status ?? 'inactive');
                        @endphp
                        <tr data-name="{{ strtolower(($user->first_name ?? '') . ' ' . ($user->last_name ?? '') . ' ' . ($user->email ?? '')) }}"
                            data-role="{{ $user->role }}"
                            data-status="{{ $user->status === 'active' ? 'active' : 'inactive' }}">
                            <td>
                                <div class="acc-user-cell">
                                    <div class="acc-user-avatar">{{ $initials }}</div>
                                    <div class="acc-user-name">
                                        {{ $user->first_name }} {{ $user->last_name }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="acc-mono">{{ $user->email }}</span>
                            </td>
                            <td>
                                <span class="acc-role {{ $roleClass }}">
                                    {{ str_replace('_', ' ', $user->role) }}
                                </span>
                            </td>
                            <td>
                                <span class="acc-status {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td>
                                <span class="acc-mono">
                                    @if(isset($user->last_login_at) && $user->last_login_at)
                                        {{ $user->last_login_at->format('M d, Y H:i') }}
                                    @else
                                        <span style="color:#9ca3af;">—</span>
                                    @endif
                                </span>
                            </td>
                            <td>
                                <div class="acc-actions text-center" style="justify-content:center;">
                                    <a href="{{ route('superadmin.accounts.edit', $user->id) }}" class="acc-btn-sm" title="Edit User">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <a href="{{ route('superadmin.accounts.reset-password', $user->id) }}" class="acc-btn-sm" title="Reset Password">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                    </a>
                                    <label class="acc-toggle">
                                        <input type="checkbox" {{ $user->status === 'active' ? 'checked' : '' }} onchange="toggleStatus({{ $user->id }}, this)">
                                        <span class="acc-toggle-slider"></span>
                                    </label>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="acc-empty">
                                    <div class="acc-empty-icon">
                                        <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    </div>
                                    <p class="acc-empty-title">No users found</p>
                                    <p class="acc-empty-sub">Try adjusting your search or filter criteria</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->count() > 0)
        <div class="acc-pagination-strip">
            <p class="acc-pagination-info">
                Showing <strong>{{ $users->firstItem() }}</strong> to <strong>{{ $users->lastItem() }}</strong>
                of <strong>{{ $users->total() }}</strong> users
            </p>
            <div>
                {{ $users->links() }}
            </div>
        </div>
        @endif
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('accSearch');
    const roleFilter = document.getElementById('accRoleFilter');
    const statusFilter = document.getElementById('accStatusFilter');
    const tbody = document.getElementById('accTbody');

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const roleValue = roleFilter.value.toLowerCase();
        const statusValue = statusFilter.value.toLowerCase();

        Array.from(tbody.querySelectorAll('tr')).forEach(row => {
            const dataName = row.getAttribute('data-name');
            const dataRole = row.getAttribute('data-role');
            const dataStatus = row.getAttribute('data-status');

            const matchesSearch = dataName.includes(searchTerm);
            const matchesRole = !roleValue || dataRole === roleValue;
            const matchesStatus = !statusValue || dataStatus === statusValue;

            row.style.display = matchesSearch && matchesRole && matchesStatus ? '' : 'none';
        });
    }

    searchInput.addEventListener('keyup', filterTable);
    roleFilter.addEventListener('change', filterTable);
    statusFilter.addEventListener('change', filterTable);
});

function resetPassword(userId, userName) {
    if (!confirm(`Reset password for ${userName}? They will receive a temporary password.`)) {
        return;
    }

    fetch(`/superadmin/accounts/${userId}/reset-password`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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
}

function toggleStatus(userId, checkbox) {
    const newStatus = checkbox.checked ? 'active' : 'inactive';

    fetch(`/superadmin/accounts/${userId}/toggle-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ status: newStatus }),
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            alert('Failed to update status: ' + (data.message || 'Unknown error'));
            checkbox.checked = !checkbox.checked;
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
        checkbox.checked = !checkbox.checked;
    });
}
</script>
@endsection

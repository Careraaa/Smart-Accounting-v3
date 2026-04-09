@extends('layouts.layout')

@push('styles')
@include('superadmin.partials.prl-theme')
@endpush

@section('content')
<div class="prl-page">

    {{-- Flash messages --}}
    @foreach(['success','error','info'] as $t)
        @if(session($t))
        <div class="prl-flash {{ $t }}">
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
    <div class="prl-topbar">
        <div>
            <h1 class="prl-topbar-title">Accounts</h1>
            <p class="prl-topbar-sub">Manage user accounts and access control</p>
        </div>
        <div class="prl-topbar-actions">
            <a href="{{ route('superadmin.accounts.create') }}" class="prl-btn-generate">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Add Account
            </a>
        </div>
    </div>

    {{-- Section header + filter --}}
    <div class="prl-section-head">
        <h2 class="prl-section-title"><span class="prl-dot"></span> All Users</h2>
    </div>

    <div class="prl-filter-bar">
        <div class="prl-search-wrap">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
            <input type="text" class="prl-search-input" id="accSearch" placeholder="Search user…">
        </div>
        <select class="prl-filter-select" id="accRoleFilter">
            <option value="">All Roles</option>
            <option value="superadmin">Superadmin</option>
            <option value="hr">HR</option>
            <option value="accountant">Accountant</option>
            <option value="remittance_clerk">Remittance Clerk</option>
            <option value="employee">Employee</option>
            <option value="qr_admin">QR Admin</option>
        </select>
        <select class="prl-filter-select" id="accStatusFilter">
            <option value="">All Statuses</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
    </div>

    {{-- Table --}}
    <div class="prl-table-card">
        <div class="prl-table-scroll">
            <table class="prl-table">
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
                            $roleClass = 'r-' . $user->role;
                            $statusClass = $user->status === 'active' ? 's-active' : 's-inactive';
                            $statusLabel = ucfirst($user->status ?? 'inactive');
                        @endphp
                        <tr data-name="{{ strtolower(($user->first_name ?? '') . ' ' . ($user->last_name ?? '') . ' ' . ($user->email ?? '')) }}"
                            data-role="{{ $user->role }}"
                            data-status="{{ $user->status === 'active' ? 'active' : 'inactive' }}">
                            <td>
                                <div class="prl-emp-cell">
                                    <div class="prl-emp-avatar">{{ $initials }}</div>
                                    <div class="prl-emp-name">
                                        {{ $user->first_name }} {{ $user->last_name }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="prl-mono">{{ $user->email }}</span>
                            </td>
                            <td>
                                <span class="sa-role {{ $roleClass }}">
                                    {{ str_replace('_', ' ', $user->role) }}
                                </span>
                            </td>
                            <td>
                                <span class="sa-status {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td>
                                <span class="prl-mono">
                                    @if(isset($user->last_login_at) && $user->last_login_at)
                                        {{ $user->last_login_at->format('M d, Y H:i') }}
                                    @else
                                        <span style="color:#9ca3af;">—</span>
                                    @endif
                                </span>
                            </td>
                            <td>
                                <div class="prl-actions text-center" style="justify-content:center;">
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
                                <div class="prl-empty">
                                    <div class="prl-empty-icon">
                                        <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    </div>
                                    <p class="prl-empty-title">No users found</p>
                                    <p class="prl-empty-sub">Try adjusting your search or filter criteria</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->count() > 0)
        <div class="prl-pagination-strip">
            <p class="prl-pagination-info">
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

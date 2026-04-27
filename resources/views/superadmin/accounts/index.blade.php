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
            <table class="prl-table sa-accounts-table">
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
                                <span class="sa-status {{ $statusClass }}" data-status-badge>
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
            // Common Laravel cases:
            // - 419: CSRF token mismatch (HTML)
            // - 302: redirect to login (HTML)
            // - 422: validation error (JSON when Accept header is set)
            const msg =
                (data && (data.message || (data.errors && JSON.stringify(data.errors)))) ||
                raw?.slice(0, 300) ||
                `Request failed (${response.status})`;
            throw new Error(msg);
        }

        if (!data) {
            // Successful response should be JSON from toggleStatus()
            throw new Error('Unexpected server response. Please try again.');
        }

        if (!data.success) throw new Error(data.message || 'Unknown error');

        // Update row + badge immediately (keeps filters accurate)
        const effectiveStatus = (data.status === 'active') ? 'active' : 'inactive';
        if (row) row.setAttribute('data-status', effectiveStatus);

        if (badge) {
            badge.textContent = effectiveStatus.charAt(0).toUpperCase() + effectiveStatus.slice(1);
            badge.classList.remove('s-active', 's-inactive');
            badge.classList.add(effectiveStatus === 'active' ? 's-active' : 's-inactive');
        }

        // Re-apply filters without full reload
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

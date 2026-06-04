@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');

@keyframes fadeSlideUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { 0%{opacity:0;transform:scale(0.92)} 100%{opacity:1;transform:scale(1)} }
@keyframes flashIn { from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)} }

.otp-page { font-family: 'Sora', sans-serif; }

.otp-flash { display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:10px;font-size:0.82rem;font-weight:500;margin-bottom:20px;animation:flashIn 0.3s ease; }
.otp-flash.success { background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d; }
.otp-flash.error   { background:#fff0f0;border:1px solid #fecaca;color:#c8292a; }

.otp-topbar { display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;flex-wrap:wrap; }
.otp-topbar-title { font-size:1.35rem;font-weight:800;color:#111827;letter-spacing:-0.02em;margin:0 0 2px; }
.otp-topbar-sub   { font-size:0.78rem;color:#9ca3af;margin:0; }

.otp-btn-primary {
    display:inline-flex;align-items:center;gap:7px;padding:9px 18px;
    background:#111827;color:#fff;border:none;border-radius:10px;
    font-family:'Sora',sans-serif;font-size:0.82rem;font-weight:600;
    text-decoration:none;cursor:pointer;transition:background 0.15s;white-space:nowrap;
}
.otp-btn-primary:hover { background:#000;color:#fff; }

.otp-btn-sec {
    display:inline-flex;align-items:center;gap:7px;padding:9px 16px;background:#fff;color:#374151;
    border:1px solid #e5e7eb;border-radius:10px;font-family:'Sora',sans-serif;font-size:0.82rem;
    font-weight:600;text-decoration:none;cursor:pointer;transition:all 0.15s;white-space:nowrap;
}
.otp-btn-sec:hover { border-color:#c8292a;color:#c8292a;background:#fff5f5; }

.stat-card { animation:scaleIn 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.stat-card:nth-child(1) { animation-delay:0.05s; }
.stat-card:nth-child(2) { animation-delay:0.1s; }
.stat-card:nth-child(3) { animation-delay:0.15s; }
.fade-up { animation:fadeSlideUp 0.45s cubic-bezier(0.16,1,0.3,1) both; }

.otp-tab-bar { display:flex;gap:8px;margin-bottom:24px;flex-wrap:wrap;animation:fadeSlideUp 0.4s cubic-bezier(0.16,1,0.3,1) 0.05s both; }
.otp-tab {
    display:inline-flex;align-items:center;gap:8px;padding:10px 18px;border-radius:10px;font-size:0.78rem;
    font-weight:700;border:1px solid #e5e7eb;cursor:pointer;transition:all 0.15s;font-family:'Sora',sans-serif;
    background:#fff;color:#374151;
}
.otp-tab:hover { border-color:#c8292a;color:#c8292a;background:#fff5f5; }
.otp-tab.active { background:#111827;color:#fff;border-color:#111827; }
.otp-tab.active:hover { background:#000;color:#fff; }
.otp-tab-count {
    display:inline-flex;align-items:center;justify-content:center;min-width:20px;height:18px;
    padding:0 5px;border-radius:9px;font-size:0.65rem;font-weight:700;
}
.otp-tab.active .otp-tab-count { background:rgba(255,255,255,0.2);color:#fff; }
.otp-tab .otp-tab-count { background:#f3f4f6;color:#374151; }

.otp-filter-bar {
    background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:10px 14px;
    display:flex;align-items:center;gap:10px;margin-bottom:14px;flex-wrap:wrap;
    animation:fadeSlideUp 0.4s cubic-bezier(0.16,1,0.3,1) 0.1s both;
}
.otp-search-wrap { position:relative;flex:1;min-width:160px; }
.otp-search-wrap svg { position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#9ca3af;pointer-events:none; }
.otp-search-input { width:100%;border:1px solid #e5e7eb;border-radius:8px;padding:7px 12px 7px 32px;font-size:0.82rem;font-family:'Sora',sans-serif;color:#111827;background:#f9fafb;outline:none;transition:border-color 0.15s; }
.otp-search-input:focus { border-color:#c8292a;background:#fff;box-shadow:0 0 0 3px rgba(200,41,42,0.08); }
.otp-filter-select { border:1px solid #e5e7eb;border-radius:8px;padding:7px 12px;font-size:0.82rem;font-family:'Sora',sans-serif;color:#374151;background:#f9fafb;outline:none;cursor:pointer; }
.otp-filter-select:focus { border-color:#c8292a; }
.otp-result-count { font-size:0.72rem;color:#9ca3af;font-family:'DM Mono',monospace;font-variant-numeric:tabular-nums;white-space:nowrap; }

.otp-table-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;animation:fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) 0.15s both; }
.otp-table-scroll { overflow-x:auto; }
.otp-table { width:100%;border-collapse:collapse;font-size:0.835rem; }
.otp-table thead tr { background:#f8f9fb;border-bottom:1px solid #e5e7eb; }
.otp-table thead th { padding:11px 16px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#6b7280;white-space:nowrap; }
.otp-table tbody tr { border-bottom:1px solid #f3f4f6;transition:background 0.12s;cursor:pointer; }
.otp-table tbody tr:last-child { border-bottom:none; }
.otp-table tbody tr:hover { background:#fdf4f4; }
.otp-table tbody td { padding:12px 16px;color:#374151;vertical-align:middle; }

.otp-emp-cell   { display:flex;align-items:center;gap:10px; }
.otp-emp-avatar { width:34px;height:34px;border-radius:50%;background:#f3f4f6;display:flex;align-items:center;justify-content:center;font-size:0.72rem;font-weight:700;color:#6b7280;flex-shrink:0;border:1.5px solid #e5e7eb;text-transform:uppercase; }
.otp-emp-name   { font-weight:600;color:#111827;font-size:0.845rem;line-height:1.2; }
.otp-emp-pos    { font-size:0.72rem;color:#9ca3af;margin-top:1px; }

.otp-type-badge { display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:700;border:1px solid; }
.otp-type-badge.ot { background:#f0f9ff;color:#0369a1;border-color:#bae6fd; }
.otp-type-badge.ut { background:#fffbeb;color:#d97706;border-color:#fde68a; }

.otp-status { display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;white-space:nowrap; }
.otp-status::before { content:'';width:5px;height:5px;border-radius:50%; }
.otp-status.s-approved { background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0; }
.otp-status.s-approved::before { background:#16a34a; }
.otp-status.s-rejected { background:#fff0f0;color:#c8292a;border:1px solid #fecaca; }
.otp-status.s-rejected::before { background:#ef4444; }
.otp-status.s-pending { background:#fffbeb;color:#d97706;border:1px solid #fde68a; }
.otp-status.s-pending::before { background:#d97706; }

.otp-mono { font-family:'DM Mono',monospace;font-variant-numeric:tabular-nums; }
.otp-empty { display:flex;flex-direction:column;align-items:center;justify-content:center;padding:48px 24px;text-align:center; }
.otp-empty-icon { width:48px;height:48px;border-radius:12px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;color:#d1d5db;margin-bottom:16px; }
.otp-empty-title { font-size:0.85rem;font-weight:600;color:#6b7280;margin:0 0 4px; }
.otp-empty-sub { font-size:0.78rem;color:#9ca3af;margin:0; }

.otp-pagination-strip { display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-top:1px solid #f3f4f6;background:#fafafa;flex-wrap:wrap;gap:8px; }
.otp-pagination-info { font-size:0.72rem;color:#9ca3af; }
.otp-pagination-info strong { color:#374151; }
.otp-pagination-nav { display:flex;align-items:center;gap:4px; }
.otp-page-btn {
    display:flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:7px;
    font-size:0.72rem;font-weight:600;border:1px solid #e5e7eb;background:#fff;color:#6b7280;
    cursor:pointer;transition:all 0.12s;font-family:'Sora',sans-serif;
}
.otp-page-btn:hover { background:#f3f4f6;border-color:#d1d5db;color:#374151; }
.otp-page-btn.active { background:#111827;color:#fff;border-color:#111827; }
.otp-page-btn.active:hover { background:#000; }
.otp-page-btn:disabled { background:#f9fafb;color:#d1d5db;border-color:#f3f4f6;cursor:default;pointer-events:none; }
</style>
@endpush

@section('content')
<div class="otp-page">

    {{-- Flash --}}
    @foreach(['success','error'] as $t)
        @if(session($t))
        <div class="otp-flash {{ $t === 'success' ? 'success' : 'error' }}">
            @if($t === 'success')
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            @else
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
            @endif
            {{ session($t) }}
        </div>
        @endif
    @endforeach

    {{-- Topbar --}}
    <div class="otp-topbar fade-up">
        <div>
            <h1 class="otp-topbar-title">OT / UT Requests</h1>
            <p class="otp-topbar-sub">Review and act on overtime &amp; undertime requests submitted by employees.</p>
        </div>
        <div class="flex gap-2 flex-wrap">
            <a href="{{ url()->previous() }}" class="otp-btn-sec">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back
            </a>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="stat-card bg-white border border-gray-200 rounded-xl px-5 py-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 7v5l3 3"/></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Pending</p>
                    <p class="text-xl font-bold text-gray-900 tabular-nums mt-0.5">{{ $pendingCount }}</p>
                    <p class="text-[0.6rem] text-gray-400">awaiting review</p>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white border border-gray-200 rounded-xl px-5 py-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Approved</p>
                    <p class="text-xl font-bold text-gray-900 tabular-nums mt-0.5">{{ $approvedCount }}</p>
                    <p class="text-[0.6rem] text-gray-400">approved requests</p>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white border border-gray-200 rounded-xl px-5 py-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Rejected</p>
                    <p class="text-xl font-bold text-gray-900 tabular-nums mt-0.5">{{ $rejectedCount }}</p>
                    <p class="text-[0.6rem] text-gray-400">rejected requests</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Status tabs --}}
    <div class="otp-tab-bar" id="otpTabBar">
        <button data-status="pending" class="otp-tab {{ $status === 'pending' ? 'active' : '' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 7v5l3 3"/></svg>
            Pending
            <span class="otp-tab-count">{{ $pendingCount }}</span>
        </button>
        <button data-status="approved" class="otp-tab {{ $status === 'approved' ? 'active' : '' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Approved
            <span class="otp-tab-count">{{ $approvedCount }}</span>
        </button>
        <button data-status="rejected" class="otp-tab {{ $status === 'rejected' ? 'active' : '' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            Rejected
            <span class="otp-tab-count">{{ $rejectedCount }}</span>
        </button>
    </div>

    {{-- Filter bar --}}
    <div class="otp-filter-bar">
        <div class="otp-search-wrap">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
            <input type="text" id="otpSearch" placeholder="Search employee…" class="otp-search-input">
        </div>
        <select id="otpTypeFilter" class="otp-filter-select">
            <option value="all">All Types</option>
            <option value="overtime">Overtime</option>
            <option value="undertime">Undertime</option>
        </select>
        <span class="otp-result-count" id="otpResultCount">0 requests</span>
    </div>

    {{-- Table --}}
    <div class="otp-table-card">
        <div class="otp-table-scroll">
            <table class="otp-table">
                <thead>
                    <tr>
                        <th class="text-left">Employee</th>
                        <th class="text-left">Type</th>
                        <th class="text-left">Date</th>
                        <th class="text-center">Hours</th>
                        <th class="text-right">Amount</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody id="otpTbody"></tbody>
            </table>
        </div>

        <div id="otpNoResults" class="hidden">
            <div class="otp-empty">
                <div class="otp-empty-icon">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="otp-empty-title" id="otpEmptyTitle">No requests found</p>
                <p class="otp-empty-sub" id="otpEmptySub">Try adjusting your filters.</p>
            </div>
        </div>

        <div class="otp-pagination-strip">
            <div class="otp-pagination-info" id="otpPaginationInfo">Showing <strong>0</strong> requests</div>
            <div class="otp-pagination-nav" id="otpPaginationNav"></div>
        </div>
    </div>
</div>

{{-- Reject modal --}}
<div class="fixed inset-0 bg-black/50 hidden items-center justify-center z-[9999] p-5" id="rejectModal">
    <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full p-7">
        <div class="mb-5">
            <h3 class="text-lg font-bold text-gray-900 m-0 mb-1">Reject Request</h3>
            <p class="text-xs text-gray-400 m-0" id="rejectModalSub">Provide a reason for rejecting this request.</p>
        </div>
        <form id="rejectForm" method="POST">
            @csrf
            <div class="mb-6">
                <label for="modal_rejection_reason" class="block text-xs font-bold uppercase tracking-wide text-gray-500 mb-1.5">Rejection Reason <span class="text-amber-600">*</span></label>
                <textarea name="rejection_reason" id="modal_rejection_reason"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-xs text-gray-900 bg-gray-50/50 outline-none resize-y min-h-[80px] focus:border-gray-400 focus:ring-2 focus:ring-gray-200/50 focus:bg-white transition-colors"
                    placeholder="Enter reason for rejection…" required></textarea>
            </div>
            <div class="flex gap-2.5 justify-end">
                <button type="button" id="otpCancelBtn" class="px-4 py-2.5 rounded-xl text-xs font-bold cursor-pointer transition-colors bg-gray-100 text-gray-600 hover:bg-gray-200 border-none">Cancel</button>
                <button type="submit" class="px-4 py-2.5 rounded-xl text-xs font-bold cursor-pointer transition-colors bg-red-600/80 text-white hover:bg-red-700/80 border-none">Confirm Rejection</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.allOtRequests = {!! json_encode($allRequests->map(fn($r) => [
    'id' => $r->id,
    'firstName' => $r->employee?->first_name ?? 'Unknown',
    'lastName'  => $r->employee?->last_name ?? '',
    'name' => strtolower(trim(($r->employee?->first_name ?? '') . ' ' . ($r->employee?->last_name ?? ''))),
    'department' => $r->employee?->department ?? '',
    'position' => $r->employee?->position ?? '',
    'type' => $r->type,
    'date' => $r->date->format('M d, Y'),
    'hours' => (float) $r->hours,
    'amount' => (float) abs($r->amount),
    'status' => $r->status,
])) !!};

(function () {
    var search   = document.getElementById('otpSearch');
    var typeF    = document.getElementById('otpTypeFilter');
    var tbody    = document.getElementById('otpTbody');
    var noRes    = document.getElementById('otpNoResults');
    var emptyT   = document.getElementById('otpEmptyTitle');
    var emptyS   = document.getElementById('otpEmptySub');
    var countEl  = document.getElementById('otpResultCount');
    var PER      = 15;
    var page     = 1;
    var filtered = [];
    var curStatus = '{{ $status }}';

    var counts = { pending: {{ $pendingCount }}, approved: {{ $approvedCount }}, rejected: {{ $rejectedCount }} };

    function switchTab(status) {
        curStatus = status;
        document.querySelectorAll('.otp-tab').forEach(function (btn) {
            var isActive = btn.dataset.status === status;
            btn.classList.toggle('active', isActive);
            var badge = btn.querySelector('.otp-tab-count');
            if (badge) badge.textContent = counts[status];
        });
        page = 1;
        applyFilters();
    }

    function applyFilters() {
        var q = search.value.toLowerCase().trim();
        var t = typeF.value;
        filtered = window.allOtRequests.filter(function (r) {
            if (r.status !== curStatus) return false;
            if (t !== 'all' && r.type !== t) return false;
            if (q && !r.name.includes(q)) return false;
            return true;
        });
        page = 1;
        render();
    }

    function render() {
        var start = (page - 1) * PER;
        var end = Math.min(start + PER, filtered.length);
        var pageData = filtered.slice(start, end);
        tbody.innerHTML = '';
        countEl.textContent = filtered.length + ' ' + (filtered.length === 1 ? 'request' : 'requests');

        if (pageData.length === 0) {
            noRes.classList.remove('hidden');
            var labels = { pending: ['No pending requests', 'All caught up — nothing awaiting review.'], approved: ['No approved requests', 'No approved requests yet.'], rejected: ['No rejected requests', 'No rejected requests yet.'] };
            var l = labels[curStatus] || ['No requests found', 'Try adjusting your filters.'];
            emptyT.textContent = filtered.length === 0 ? l[0] : 'No results found';
            emptyS.textContent = filtered.length === 0 ? l[1] : 'Try adjusting your search or filters.';
        } else {
            noRes.classList.add('hidden');
            var showRoute = '{{ route("overtime.show", ["overtime" => "__ID__"]) }}';
            pageData.forEach(function (r) {
                var url = showRoute.replace('__ID__', r.id);
                var initials = (r.firstName.charAt(0) + r.lastName.charAt(0)).toUpperCase() || '?';
                var isOt = r.type === 'overtime';
                var typeCls = isOt ? 'ot' : 'ut';
                var typeLabel = isOt ? 'Overtime' : 'Undertime';
                var statusCls = r.status === 'approved' ? 's-approved' : r.status === 'rejected' ? 's-rejected' : 's-pending';
                var statusLabel = r.status.charAt(0).toUpperCase() + r.status.slice(1);
                var amtCls = r.amount >= 0 ? 'text-emerald-600' : 'text-red-600';

                var row = document.createElement('tr');
                row.onclick = function () { window.location = url; };
                row.innerHTML =
                    '<td><div class="otp-emp-cell"><div class="otp-emp-avatar">' + initials + '</div><div><div class="otp-emp-name">' + r.firstName + ' ' + r.lastName + '</div><div class="otp-emp-pos">' + (r.position || '—') + '</div></div></div></td>' +
                    '<td><span class="otp-type-badge ' + typeCls + '">' + typeLabel + '</span></td>' +
                    '<td class="otp-mono" style="font-size:0.85rem">' + r.date + '</td>' +
                    '<td class="text-center otp-mono" style="font-size:0.85rem;font-weight:600">' + r.hours.toFixed(1) + 'h</td>' +
                    '<td class="text-right otp-mono" style="font-size:0.85rem;font-weight:700;' + amtCls + '">₱' + r.amount.toFixed(2) + '</td>' +
                    '<td class="text-center"><span class="otp-status ' + statusCls + '">' + statusLabel + '</span></td>';
                tbody.appendChild(row);
            });
        }
        updatePagination();
    }

    function updatePagination() {
        var total = filtered.length;
        var pages = Math.ceil(total / PER);
        var info  = document.getElementById('otpPaginationInfo');
        var nav   = document.getElementById('otpPaginationNav');
        if (!info || !nav) return;
        if (total === 0) { info.innerHTML = 'No requests to display'; nav.innerHTML = ''; return; }
        var s = (page - 1) * PER + 1, e = Math.min(page * PER, total);
        info.innerHTML = 'Showing <strong>' + s + '</strong>&ndash;<strong>' + e + '</strong> of <strong>' + total + '</strong>';
        if (pages <= 1) { nav.innerHTML = ''; return; }

        var html = '<button class="otp-page-btn" data-p="' + (page - 1) + '"' + (page === 1 ? ' disabled' : '') + '>&lsaquo;</button>';
        var half = 2;
        var winStart = Math.max(1, page - half);
        var winEnd = Math.min(pages, winStart + 4);
        if (winEnd - winStart + 1 < 5) { winStart = Math.max(winEnd - 4, 1); }
        for (var i = winStart; i <= winEnd; i++) {
            html += '<button class="otp-page-btn' + (i === page ? ' active' : '') + '" data-p="' + i + '">' + i + '</button>';
        }
        html += '<button class="otp-page-btn" data-p="' + (page + 1) + '"' + (page === pages ? ' disabled' : '') + '>&rsaquo;</button>';
        nav.innerHTML = html;
        nav.querySelectorAll('.otp-page-btn:not([disabled])').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                var p = parseInt(this.dataset.p);
                if (p < 1 || p > pages) return;
                page = p;
                render();
            });
        });
    }

    search.addEventListener('input', applyFilters);
    typeF.addEventListener('change', applyFilters);

    document.querySelectorAll('.otp-tab').forEach(function (btn) {
        btn.addEventListener('click', function () { switchTab(this.dataset.status); });
    });

    applyFilters();

    // Reject modal
    var rejectModal = document.getElementById('rejectModal');
    var otpCancelBtn = document.getElementById('otpCancelBtn');

    window.openRejectModal = function (id, name) {
        document.getElementById('rejectModalSub').textContent = 'Provide a reason for rejecting ' + name + '\'s request.';
        document.getElementById('rejectForm').action = '/overtime/' + id + '/reject';
        document.getElementById('modal_rejection_reason').value = '';
        rejectModal.classList.remove('hidden');
        rejectModal.classList.add('flex');
        setTimeout(function () { document.getElementById('modal_rejection_reason').focus(); }, 100);
    };

    if (otpCancelBtn) {
        otpCancelBtn.addEventListener('click', function () {
            rejectModal.classList.add('hidden');
            rejectModal.classList.remove('flex');
        });
    }

    rejectModal.addEventListener('click', function (e) {
        if (e.target === this) {
            rejectModal.classList.add('hidden');
            rejectModal.classList.remove('flex');
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            rejectModal.classList.add('hidden');
            rejectModal.classList.remove('flex');
        }
    });
})();
</script>
@endpush
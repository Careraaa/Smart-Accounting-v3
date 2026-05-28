@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { 0%{opacity:0;transform:scale(0.93)} 100%{opacity:1;transform:scale(1)} }
.fade-up { animation:fadeUp 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.scale-in { animation:scaleIn 0.35s cubic-bezier(0.16,1,0.3,1) both; }
.stat-card:nth-child(1) { animation-delay:0.05s; }
.stat-card:nth-child(2) { animation-delay:0.1s; }
.stat-card:nth-child(3) { animation-delay:0.15s; }
.stat-card:nth-child(4) { animation-delay:0.2s; }
</style>
@endpush

@section('content')
{{-- Flash --}}
    @foreach(['success','error','info'] as $t)
        @if(session($t))
        <div class="fade-up inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-xs font-semibold mb-5
            {{ $t === 'success' ? 'bg-emerald-500/15 border border-emerald-500/25 text-emerald-700' : '' }}
            {{ $t === 'error' ? 'bg-red-500/15 border border-red-500/25 text-red-700' : '' }}
            {{ $t === 'info' ? 'bg-blue-500/15 border border-blue-500/25 text-blue-700' : '' }}">
            @if($t==='success')
            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            @else
            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
            @endif
            {{ session($t) }}
        </div>
        @endif
    @endforeach

    {{-- Header --}}
    <div class="flex items-start justify-between mb-6 flex-wrap gap-4 fade-up">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Remittances</h1>
            <p class="text-sm text-gray-500 mt-0.5">Pending and approved remittances for review and tracking</p>
        </div>
        <a href="{{ route('remittances.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded-xl hover:bg-gray-800 hover:shadow-lg hover:shadow-gray-900/20 active:scale-[0.97] transition-all duration-200 no-underline">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Add Remittance
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <div class="scale-in stat-card bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm">
            <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Total</p>
            <p class="text-xl font-bold text-gray-900 tabular-nums mt-1">{{ $totalRemittances }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">all remittances</p>
        </div>
        <div class="scale-in stat-card bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm">
            <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Approved</p>
            <p class="text-xl font-bold text-emerald-600 tabular-nums mt-1">{{ $approvedCount }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">approved</p>
        </div>
        <div class="scale-in stat-card bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm">
            <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Pending</p>
            <p class="text-xl font-bold text-amber-600 tabular-nums mt-1">{{ $pendingCount }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">awaiting review</p>
        </div>
        <div class="scale-in stat-card bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm">
            <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Rejected</p>
            <p class="text-xl font-bold text-red-600 tabular-nums mt-1">{{ $rejectedRemittances }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">rejected</p>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="border-b border-gray-200 mb-6 fade-up">
        <nav class="flex gap-1 -mb-px" role="tablist">
            <button role="tab" data-tab="pending"
                class="tab-btn relative px-5 py-3 text-sm font-medium border-b-2 transition-all duration-200 group
                {{ ($tab ?? 'pending') === 'pending'
                    ? 'border-amber-500 text-amber-700 bg-amber-50/60'
                    : 'border-transparent text-gray-400 hover:text-gray-600 hover:border-gray-300 hover:bg-gray-50/50' }}">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 {{ ($tab ?? 'pending') === 'pending' ? 'text-amber-500' : 'text-gray-400 group-hover:text-gray-500' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" d="M12 8v4m0 4h.01"/></svg>
                    Pending
                    @if($pendingCount > 0)
                    <span class="ml-1 inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-[11px] font-bold
                        {{ ($tab ?? 'pending') === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-gray-200 text-gray-600' }}
                        transition-colors duration-200">{{ $pendingCount }}</span>
                    @endif
                </span>
            </button>
            <button role="tab" data-tab="approved"
                class="tab-btn relative px-5 py-3 text-sm font-medium border-b-2 transition-all duration-200 group
                {{ ($tab ?? 'pending') === 'approved'
                    ? 'border-emerald-500 text-emerald-700 bg-emerald-50/60'
                    : 'border-transparent text-gray-400 hover:text-gray-600 hover:border-gray-300 hover:bg-gray-50/50' }}">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 {{ ($tab ?? 'pending') === 'approved' ? 'text-emerald-500' : 'text-gray-400 group-hover:text-gray-500' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Approved
                    @if($approvedCount > 0)
                    <span class="ml-1 inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-[11px] font-bold
                        {{ ($tab ?? 'pending') === 'approved' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-200 text-gray-600' }}
                        transition-colors duration-200">{{ $approvedCount }}</span>
                    @endif
                </span>
            </button>
            <button role="tab" data-tab="rejected"
                class="tab-btn relative px-5 py-3 text-sm font-medium border-b-2 transition-all duration-200 group
                {{ ($tab ?? 'pending') === 'rejected'
                    ? 'border-red-500 text-red-700 bg-red-50/60'
                    : 'border-transparent text-gray-400 hover:text-gray-600 hover:border-gray-300 hover:bg-gray-50/50' }}">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 {{ ($tab ?? 'pending') === 'rejected' ? 'text-red-500' : 'text-gray-400 group-hover:text-gray-500' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 9l-6 6M9 9l6 6"/></svg>
                    Rejected
                </span>
            </button>
        </nav>
    </div>

    {{-- Pending tab panel --}}
    <div id="tab-pending" class="tab-panel {{ ($tab ?? 'pending') !== 'pending' ? 'hidden' : '' }}">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden mb-6 fade-up">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-50 bg-gray-50/50">
                        <th class="text-left px-5 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">
                            <a href="{{ route('remittances.index', ['sort_by' => 'remittance_date', 'sort_order' => ($sortBy === 'remittance_date' && $sortOrder === 'asc') ? 'desc' : 'asc']) }}" class="inline-flex items-center gap-1 no-underline text-gray-400 hover:text-gray-700">
                                Date
                                @if($sortBy === 'remittance_date')
                                <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $sortOrder === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>
                                @endif
                            </a>
                        </th>
                        <th class="text-left px-4 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Route</th>
                        <th class="text-left px-4 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Vehicle</th>
                        <th class="text-right px-3 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Net Remittance</th>
                        <th class="text-center px-3 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                @forelse($pendingRemittances as $remittance)
                    <tr class="transition-colors hover:bg-gray-50/50 cursor-pointer" onclick="window.location='{{ route('remittances.show', $remittance) }}'">
                        <td class="px-5 py-3.5">
                            <p class="text-xs font-semibold text-gray-700 font-mono">{{ $remittance->remittance_date?->format('M d, Y') }}</p>
                        </td>
                        <td class="px-4 py-3.5">
                            <p class="text-xs font-bold text-gray-900">{{ $remittance->route->route_name }}</p>
                        </td>
                        <td class="px-4 py-3.5">
                            <p class="text-xs font-mono text-gray-700">{{ $remittance->vehicle->plate_number }}</p>
                        </td>
                        <td class="px-3 py-3.5 text-right">
                            <span class="text-xs font-mono font-bold {{ $remittance->is_short_remittance ? 'text-red-600' : 'text-emerald-600' }}">
                                ₱{{ number_format($remittance->net_remittance, 2) }}
                            </span>
                        </td>
                        <td class="px-3 py-3.5 text-center">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[0.55rem] font-bold border bg-amber-50 text-amber-700 border-amber-200">Pending</span>
                        </td>
                        <form id="delete-form-{{ $remittance->id }}" action="{{ route('remittances.destroy', $remittance) }}" method="POST" style="display:none;">
                            @csrf @method('DELETE')
                        </form>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="flex flex-col items-center justify-center py-12 text-center">
                                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mb-3">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <p class="text-sm font-semibold text-gray-500">No pending remittances</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($pendingRemittances, 'hasPages') && $pendingRemittances->hasPages())
        <div class="flex items-center justify-between px-5 py-3 border-t border-gray-50 bg-gray-50/30">
            <p class="text-[0.65rem] text-gray-400">
                Showing <strong class="text-gray-600">{{ $pendingRemittances->firstItem() }}</strong>–<strong class="text-gray-600">{{ $pendingRemittances->lastItem() }}</strong>
                of <strong class="text-gray-600">{{ $pendingRemittances->total() }}</strong> pending remittances
            </p>
            {{ $pendingRemittances->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

{{-- Approved tab panel --}}
<div id="tab-approved" class="tab-panel {{ ($tab ?? 'pending') !== 'approved' ? 'hidden' : '' }}">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden fade-up">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-50 bg-gray-50/50">
                        <th class="text-left px-5 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Date</th>
                        <th class="text-left px-4 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Route</th>
                        <th class="text-left px-4 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Vehicle</th>
                        <th class="text-right px-3 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Net Remittance</th>
                        <th class="text-center px-3 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                @forelse($approvedRemittances as $remittance)
                    <tr class="transition-colors hover:bg-gray-50/50 cursor-pointer" onclick="window.location='{{ route('remittances.show', $remittance) }}'">
                        <td class="px-5 py-3.5">
                            <p class="text-xs font-semibold text-gray-700 font-mono">{{ $remittance->remittance_date?->format('M d, Y') }}</p>
                        </td>
                        <td class="px-4 py-3.5">
                            <p class="text-xs font-bold text-gray-900">{{ $remittance->route->route_name }}</p>
                        </td>
                        <td class="px-4 py-3.5">
                            <p class="text-xs font-mono text-gray-700">{{ $remittance->vehicle->plate_number }}</p>
                        </td>
                        <td class="px-3 py-3.5 text-right">
                            <span class="text-xs font-mono font-bold {{ $remittance->is_short_remittance ? 'text-red-600' : 'text-emerald-600' }}">
                                ₱{{ number_format($remittance->net_remittance, 2) }}
                            </span>
                        </td>
                        <td class="px-3 py-3.5 text-center">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[0.55rem] font-bold border bg-emerald-50 text-emerald-700 border-emerald-200">Approved</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="flex flex-col items-center justify-center py-12 text-center">
                                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mb-3">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <p class="text-sm font-semibold text-gray-500">No approved remittances</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($approvedRemittances, 'hasPages') && $approvedRemittances->hasPages())
        <div class="flex items-center justify-between px-5 py-3 border-t border-gray-50 bg-gray-50/30">
            <p class="text-[0.65rem] text-gray-400">
                Showing <strong class="text-gray-600">{{ $approvedRemittances->firstItem() }}</strong>–<strong class="text-gray-600">{{ $approvedRemittances->lastItem() }}</strong>
                of <strong class="text-gray-600">{{ $approvedRemittances->total() }}</strong> approved remittances
            </p>
            {{ $approvedRemittances->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

{{-- Rejected tab panel --}}
<div id="tab-rejected" class="tab-panel {{ ($tab ?? 'pending') !== 'rejected' ? 'hidden' : '' }}">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden fade-up">
        <div class="flex flex-col items-center justify-center py-14 text-center">
            <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center mb-3">
                <svg class="w-6 h-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 9l-6 6M9 9l6 6"/></svg>
            </div>
            <p class="text-sm font-semibold text-gray-500">No rejected remittances</p>
            <p class="text-xs text-gray-400 mt-1">Rejected remittances will appear here.</p>
        </div>
    </div>
</div>

</div>

{{-- Delete Modal --}}
<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden" id="deleteModal">
    <div class="bg-white rounded-2xl shadow-2xl p-6 max-w-sm w-full mx-4 scale-in">
        <div class="flex flex-col items-center text-center">
            <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">Delete Remittance?</h3>
            <p class="text-sm text-gray-500 mb-6" id="deleteModalBody">This action cannot be undone.</p>
            <div class="flex gap-3 w-full">
                <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2.5 rounded-xl text-xs font-bold bg-gray-100 text-gray-600 transition-all hover:bg-gray-200 cursor-pointer border-none">Cancel</button>
                <button type="button" id="deleteConfirmBtn" class="flex-1 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-red-600 transition-all hover:bg-red-700 cursor-pointer border-none">Yes, Delete</button>
            </div>
        </div>
    </div>@endsection

@push('scripts')
<script>
(function () {
    var activeTab = '{{ $tab ?? 'pending' }}';
    var tabBtns = document.querySelectorAll('.tab-btn');
    var tabStyles = {
        pending:  { border: 'border-amber-500',  text: 'text-amber-700',  bg: 'bg-amber-50/60', icon: 'text-amber-500',  badge: { bg: 'bg-amber-100',   text: 'text-amber-800' } },
        approved: { border: 'border-emerald-500',text: 'text-emerald-700',bg: 'bg-emerald-50/60',icon: 'text-emerald-500',badge: { bg: 'bg-emerald-100', text: 'text-emerald-800' } },
        rejected: { border: 'border-red-500',    text: 'text-red-700',   bg: 'bg-red-50/60',   icon: 'text-red-500',   badge: { bg: '',               text: '' } }
    };
    var inactiveBtn = ['border-transparent', 'text-gray-400', 'hover:text-gray-600', 'hover:border-gray-300', 'hover:bg-gray-50/50'];
    var inactiveSvg = ['text-gray-400', 'group-hover:text-gray-500'];
    var inactiveBadge = ['bg-gray-200', 'text-gray-600'];

    tabBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var tab = this.dataset.tab;
            if (tab === activeTab) return;
            var prev = document.getElementById('tab-' + activeTab);
            if (prev) { prev.classList.add('hidden'); prev.classList.remove('active-panel'); }
            var next = document.getElementById('tab-' + tab);
            if (next) { next.classList.remove('hidden'); next.classList.add('active-panel'); }
            tabBtns.forEach(function (b) {
                b.classList.remove('border-amber-500','text-amber-700','bg-amber-50/60','border-emerald-500','text-emerald-700','bg-emerald-50/60','border-red-500','text-red-700','bg-red-50/60');
                b.classList.add.apply(b.classList, inactiveBtn);
                var svg = b.querySelector('svg');
                if (svg) { svg.classList.remove('text-amber-500','text-emerald-500','text-red-500'); svg.classList.add.apply(svg.classList, inactiveSvg); }
                var badge = b.querySelector('[class*="ml-1"]');
                if (badge) { badge.classList.remove('bg-amber-100','text-amber-800','bg-emerald-100','text-emerald-800'); badge.classList.add.apply(badge.classList, inactiveBadge); }
            });
            var s = tabStyles[tab];
            this.classList.remove.apply(this.classList, inactiveBtn);
            this.classList.add(s.border, s.text, s.bg);
            var svg = this.querySelector('svg');
            if (svg) { svg.classList.remove.apply(svg.classList, inactiveSvg); svg.classList.add(s.icon); }
            var badge = this.querySelector('[class*="ml-1"]');
            if (badge && s.badge.bg) { badge.classList.remove.apply(badge.classList, inactiveBadge); badge.classList.add(s.badge.bg, s.badge.text); }
            var url = new URL(window.location);
            url.searchParams.set('tab', tab);
            url.searchParams.delete('page');
            history.replaceState(null, '', url.toString());
            activeTab = tab;
        });
    });
})();

let _deleteId = null;
function openDeleteModal(id, label) {
    _deleteId = id;
    document.getElementById('deleteModalBody').textContent = 'Delete remittance from "' + label + '"? This cannot be undone.';
    document.getElementById('deleteModal').classList.remove('hidden');
}
function closeModal() { document.getElementById('deleteModal').classList.add('hidden'); }
document.getElementById('deleteConfirmBtn').addEventListener('click', function () {
    if (_deleteId) document.getElementById('delete-form-' + _deleteId).submit();
});
document.getElementById('deleteModal').addEventListener('click', function (e) { if (e.target === this) closeModal(); });
document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeModal(); });
</script>
@endpush

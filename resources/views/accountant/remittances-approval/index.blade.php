@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { 0%{opacity:0;transform:scale(0.93)} 100%{opacity:1;transform:scale(1)} }
@keyframes slideRight { 0%{opacity:0;transform:translateX(-10px)} 100%{opacity:1;transform:translateX(0)} }
.fade-up { animation:fadeUp 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.scale-in { animation:scaleIn 0.35s cubic-bezier(0.16,1,0.3,1) both; }
.slide-right { animation:slideRight 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.stat-card:nth-child(1) { animation-delay:0.05s; }
.stat-card:nth-child(2) { animation-delay:0.1s; }
.stat-card:nth-child(3) { animation-delay:0.15s; }
.stat-card:nth-child(4) { animation-delay:0.2s; }
</style>
@endpush

@section('content')
@php
$pendingRemits = $remittances->where('status', 'pending');
$approvedRemits = $remittances->where('status', 'approved');
$rejectedRemits = $remittances->where('status', 'rejected');

$totalPending  = $pendingRemits->count();
$totalCollection = $pendingRemits->sum('total_collection');
$totalExpenses  = $pendingRemits->sum('total_expenses');
$totalNet       = $pendingRemits->sum('net_remittance');

$tab = request()->query('tab', 'pending');
@endphp

{{-- Header --}}
    <div class="flex items-start justify-between mb-6 flex-wrap gap-4 fade-up">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Remittance Approval</h1>
            <p class="text-sm text-gray-500 mt-0.5">Approve or reject remittances submitted by the remittance clerk</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-violet-50 text-violet-700 text-[0.6rem] font-semibold border border-violet-200">
                <span class="w-1.5 h-1.5 rounded-full bg-violet-500 animate-pulse"></span>
                {{ $totalPending }} pending
            </span>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="fade-up inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-500/15 border border-emerald-500/25 rounded-lg text-emerald-700 text-xs font-semibold mb-5">
            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @elseif(session('error'))
        <div class="fade-up inline-flex items-center gap-2 px-4 py-2.5 bg-red-500/15 border border-red-500/25 rounded-lg text-red-700 text-xs font-semibold mb-5">
            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <div class="scale-in stat-card bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm hover:shadow-md hover:border-violet-200 transition-all duration-300">
            <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Pending</p>
            <p class="text-xl font-bold text-violet-600 tabular-nums mt-1">{{ $totalPending }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Awaiting review</p>
        </div>
        <div class="scale-in stat-card bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm hover:shadow-md hover:border-blue-200 transition-all duration-300">
            <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Collection</p>
            <p class="text-lg font-bold text-blue-600 tabular-nums mt-1">₱{{ number_format($totalCollection, 0) }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Pending total</p>
        </div>
        <div class="scale-in stat-card bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm hover:shadow-md hover:border-amber-200 transition-all duration-300">
            <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Expenses</p>
            <p class="text-lg font-bold text-amber-600 tabular-nums mt-1">₱{{ number_format($totalExpenses, 0) }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Pending total</p>
        </div>
        <div class="scale-in stat-card bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm hover:shadow-md hover:border-emerald-200 transition-all duration-300">
            <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Net Remittance</p>
            <p class="text-lg font-bold text-emerald-600 tabular-nums mt-1">₱{{ number_format($totalNet, 0) }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Pending total</p>
        </div>
    </div>

    {{-- Standalone underline tabs (HR-style) --}}
    <div class="border-b border-gray-200 mb-6 fade-up">
        <nav class="flex gap-1 -mb-px" role="tablist">
            <a href="{{ request()->fullUrlWithQuery(['tab' => 'pending']) }}" role="tab"
               class="relative px-5 py-3 text-sm font-medium border-b-2 transition-all duration-200 group inline-flex items-center gap-2
               {{ $tab === 'pending' ? 'border-violet-500 text-violet-700 bg-violet-50/60' : 'border-transparent text-gray-400 hover:text-gray-600 hover:border-gray-300 hover:bg-gray-50/50' }}">
                <svg class="w-4 h-4 transition-colors {{ $tab === 'pending' ? 'text-violet-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3"/></svg>
                Pending
                @if($totalPending > 0)
                    <span class="ml-0.5 inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-[11px] font-bold {{ $tab === 'pending' ? 'bg-violet-100 text-violet-800' : 'bg-gray-200 text-gray-600' }}">{{ $totalPending }}</span>
                @endif
            </a>
            <a href="{{ request()->fullUrlWithQuery(['tab' => 'approved']) }}" role="tab"
               class="relative px-5 py-3 text-sm font-medium border-b-2 transition-all duration-200 group inline-flex items-center gap-2
               {{ $tab === 'approved' ? 'border-emerald-500 text-emerald-700 bg-emerald-50/60' : 'border-transparent text-gray-400 hover:text-gray-600 hover:border-gray-300 hover:bg-gray-50/50' }}">
                <svg class="w-4 h-4 transition-colors {{ $tab === 'approved' ? 'text-emerald-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Approved
                @if($approvedRemits->count() > 0)
                    <span class="ml-0.5 inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-[11px] font-bold {{ $tab === 'approved' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-200 text-gray-600' }}">{{ $approvedRemits->count() }}</span>
                @endif
            </a>
            <a href="{{ request()->fullUrlWithQuery(['tab' => 'rejected']) }}" role="tab"
               class="relative px-5 py-3 text-sm font-medium border-b-2 transition-all duration-200 group inline-flex items-center gap-2
               {{ $tab === 'rejected' ? 'border-red-500 text-red-700 bg-red-50/60' : 'border-transparent text-gray-400 hover:text-gray-600 hover:border-gray-300 hover:bg-gray-50/50' }}">
                <svg class="w-4 h-4 transition-colors {{ $tab === 'rejected' ? 'text-red-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 9l-6 6M9 9l6 6"/></svg>
                Rejected
                @if($rejectedRemits->count() > 0)
                    <span class="ml-0.5 inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-[11px] font-bold {{ $tab === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-gray-200 text-gray-600' }}">{{ $rejectedRemits->count() }}</span>
                @endif
            </a>
        </nav>
    </div>

    {{-- Table card --}}
    <div class="fade-up bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-50 bg-gray-50/50">
                        <th class="text-left px-5 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Date</th>
                        <th class="text-left px-4 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Route</th>
                        <th class="text-left px-4 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Vehicle</th>
                        <th class="text-right px-3 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Collection</th>
                        <th class="text-right px-3 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Expenses</th>
                        <th class="text-right px-3 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Net</th>
                        <th class="text-center px-3 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Status</th>
                        @if($tab === 'pending')<th class="text-center px-5 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Actions</th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @php
                        $displayRemits = match($tab) {
                            'approved' => $approvedRemits,
                            'rejected' => $rejectedRemits,
                            default => $pendingRemits,
                        };
                    @endphp
                    @forelse($displayRemits as $remittance)
                        <tr class="transition-colors hover:bg-gray-50/50">
                            <td class="px-5 py-3.5">
                                <p class="text-xs font-semibold text-gray-900">{{ $remittance->remittance_date?->format('M d, Y') }}</p>
                                @if($remittance->driver)
                                    <p class="text-[0.55rem] text-gray-400 mt-0.5">{{ $remittance->driver->first_name }} {{ $remittance->driver->last_name }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                <p class="text-xs text-gray-600">{{ $remittance->route->route_name }}</p>
                                @if($remittance->pao)
                                    <p class="text-[0.55rem] text-gray-400 mt-0.5">{{ $remittance->pao->first_name }} {{ $remittance->pao->last_name }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-xs text-gray-500">{{ $remittance->vehicle->plate_number }}</td>
                            <td class="px-3 py-3.5 text-right text-xs tabular-nums text-gray-600">₱{{ number_format($remittance->total_collection, 0) }}</td>
                            <td class="px-3 py-3.5 text-right text-xs tabular-nums text-gray-400">₱{{ number_format($remittance->total_expenses, 0) }}</td>
                            <td class="px-3 py-3.5 text-right text-xs font-bold tabular-nums text-gray-900">₱{{ number_format($remittance->net_remittance, 0) }}</td>
                            <td class="px-3 py-3.5 text-center">
                                @php
                                    $stBg = match($remittance->status) {
                                        'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'rejected' => 'bg-red-50 text-red-700 border-red-200',
                                        default => 'bg-gray-50 text-gray-600 border-gray-200',
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[0.5rem] font-semibold border {{ $stBg }}">
                                    @if($remittance->status === 'pending')
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    @elseif($remittance->status === 'approved')
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    @else
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                    @endif
                                    {{ ucfirst($remittance->status) }}
                                </span>
                            </td>
                            @if($tab === 'pending')
                            <td class="px-5 py-3.5 text-center">
                                @if($remittance->status === 'pending')
                                    <div class="flex items-center justify-center gap-1">
                                        <button type="button" title="Approve"
                                                onclick="openRemittanceApproveModal({{ $remittance->id }}, '{{ addslashes($remittance->route->route_name) }}', '{{ $remittance->remittance_date?->format('M d, Y') }}', '{{ number_format($remittance->net_remittance, 2) }}')"
                                                class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white flex items-center justify-center transition-all border-0 cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                        <button type="button" title="Reject"
                                                onclick="openRemittanceRejectModal({{ $remittance->id }}, '{{ addslashes($remittance->route->route_name) }}', '{{ $remittance->remittance_date?->format('M d, Y') }}', '{{ number_format($remittance->net_remittance, 2) }}')"
                                                class="w-7 h-7 rounded-lg bg-red-50 text-red-600 hover:bg-red-500 hover:text-white flex items-center justify-center transition-all border-0 cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 9l-6 6M9 9l6 6"/></svg>
                                        </button>
                                    </div>
                                @endif
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $tab === 'pending' ? 8 : 7 }}" class="text-center py-12">
                                <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    @if($tab === 'rejected')
                                        <circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 9l-6 6M9 9l6 6"/>
                                    @elseif($tab === 'approved')
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    @else
                                        <circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3"/>
                                    @endif
                                </svg>
                                <p class="text-xs text-gray-400">No {{ $tab }} remittances found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($displayRemits->isNotEmpty() && $tab === 'pending')
                    <tfoot>
                        <tr class="border-t-2 border-gray-100 bg-gray-50/80">
                            <td colspan="3" class="px-5 py-3 text-xs font-bold text-gray-900">Totals</td>
                            <td class="px-3 py-3 text-right text-xs font-bold tabular-nums text-gray-900">₱{{ number_format($displayRemits->sum('total_collection'), 0) }}</td>
                            <td class="px-3 py-3 text-right text-xs font-bold tabular-nums text-amber-600">₱{{ number_format($displayRemits->sum('total_expenses'), 0) }}</td>
                            <td class="px-3 py-3 text-right text-xs font-bold tabular-nums text-emerald-600">₱{{ number_format($displayRemits->sum('net_remittance'), 0) }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- Reject Modal (HR-style) --}}
    <div class="fixed inset-0 bg-black/50 hidden items-center justify-center z-[9999] p-5" id="remittanceRejectOverlay">
        <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full p-7">
            <div class="mb-5">
                <h3 class="text-lg font-bold text-gray-900 m-0 mb-1">Reject Remittance</h3>
                <p id="remittanceRejectDesc" class="text-xs text-gray-400 m-0"></p>
            </div>
            <form id="remittanceRejectForm" method="POST">
                @csrf
                <div class="mb-6">
                    <label for="remittanceRejectReason" class="block text-xs font-bold uppercase tracking-wide text-gray-500 mb-1.5">
                        Rejection Reason <span class="text-amber-600">*</span>
                    </label>
                    <textarea name="rejection_reason" id="remittanceRejectReason" rows="3" required
                              placeholder="Explain why this remittance is being rejected…"
                              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-xs text-gray-700 placeholder:text-gray-300 resize-none focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-300 transition-all"></textarea>
                    <p class="text-[0.55rem] text-gray-400 mt-1.5">The remittance clerk will be notified and can resubmit.</p>
                </div>
                <div class="flex gap-2.5 justify-end">
                    <button type="button" onclick="closeRemittanceRejectModal()"
                            class="px-4 py-2.5 rounded-xl text-xs font-bold bg-gray-100 text-gray-600 transition-all hover:bg-gray-200 active:scale-[0.97] cursor-pointer border-none">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-red-600/80 transition-all hover:bg-red-600 active:scale-[0.97] cursor-pointer border-none">
                        Reject Request
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Approve Modal (HR-style) --}}
    <div class="fixed inset-0 bg-black/50 hidden items-center justify-center z-[9999] p-5" id="remittanceApproveOverlay">
        <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full p-7">
            <div class="mb-5">
                <h3 class="text-lg font-bold text-gray-900 m-0 mb-1">Approve Remittance</h3>
                <p id="remittanceApproveDesc" class="text-xs text-gray-400 m-0"></p>
            </div>
            <form id="remittanceApproveForm" method="POST">
                @csrf
                <div class="flex gap-2.5 justify-end">
                    <button type="button" onclick="closeRemittanceApproveModal()"
                            class="px-4 py-2.5 rounded-xl text-xs font-bold bg-gray-100 text-gray-600 transition-all hover:bg-gray-200 active:scale-[0.97] cursor-pointer border-none">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600/80 transition-all hover:bg-emerald-600 active:scale-[0.97] cursor-pointer border-none">
                        Confirm Approval
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function openRemittanceRejectModal(id, route, date, amount) {
    document.getElementById('remittanceRejectForm').action = '{{ url('/remittance-approval') }}' + '/' + id + '/reject';
    document.getElementById('remittanceRejectDesc').textContent = '₱' + amount + ' · ' + route + ' · ' + date;
    document.getElementById('remittanceRejectReason').value = '';
    document.getElementById('remittanceRejectOverlay').classList.remove('hidden');
    document.getElementById('remittanceRejectOverlay').classList.add('flex');
}
function closeRemittanceRejectModal() {
    document.getElementById('remittanceRejectOverlay').classList.add('hidden');
    document.getElementById('remittanceRejectOverlay').classList.remove('flex');
}
function openRemittanceApproveModal(id, route, date, amount) {
    document.getElementById('remittanceApproveForm').action = '{{ url('/remittance-approval') }}' + '/' + id + '/approve';
    document.getElementById('remittanceApproveDesc').textContent = '₱' + amount + ' · ' + route + ' · ' + date;
    document.getElementById('remittanceApproveOverlay').classList.remove('hidden');
    document.getElementById('remittanceApproveOverlay').classList.add('flex');
}
function closeRemittanceApproveModal() {
    document.getElementById('remittanceApproveOverlay').classList.add('hidden');
    document.getElementById('remittanceApproveOverlay').classList.remove('flex');
}
</script>
@endpush

@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { 0%{opacity:0;transform:scale(0.93)} 100%{opacity:1;transform:scale(1)} }
.fade-up { animation:fadeUp 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.scale-in { animation:scaleIn 0.35s cubic-bezier(0.16,1,0.3,1) both; }
@keyframes modalFadeIn { 0%{opacity:0} 100%{opacity:1} }
@keyframes modalScaleIn { 0%{opacity:0;transform:scale(0.92) translateY(8px)} 100%{opacity:1;transform:scale(1) translateY(0)} }
.modal-overlay { animation:modalFadeIn 0.2s ease-out both; }
.modal-panel { animation:modalScaleIn 0.25s cubic-bezier(0.16,1,0.3,1) both; }
</style>
@endpush

@section('content')
@php
$driver = $remittance->driver;
$pao    = $remittance->pao;
$route  = $remittance->route;
$vehicle = $remittance->vehicle;
$boundaryRemaining = $remittance->short_amount !== null
    ? max(round((float) $remittance->short_amount, 2), 0)
    : max(round((float) $remittance->boundary - (float) $remittance->net_remittance, 2), 0);
$initialPaymentAmount = min(
    max(round((float) $remittance->net_remittance, 2), 0),
    max(round((float) $remittance->boundary, 2), 0)
);
$statusInfo = match($remittance->status) {
    'pending'  => ['label'=>'Pending',  'dot'=>'bg-amber-400', 'text'=>'text-amber-600', 'bg'=>'bg-amber-50'],
    'approved' => ['label'=>'Approved', 'dot'=>'bg-emerald-400','text'=>'text-emerald-600','bg'=>'bg-emerald-50'],
    'rejected' => ['label'=>'Rejected', 'dot'=>'bg-red-400',   'text'=>'text-red-600',   'bg'=>'bg-red-50'],
    default    => ['label'=>'Pending',  'dot'=>'bg-amber-400', 'text'=>'text-amber-600', 'bg'=>'bg-amber-50'],
};
@endphp

<div class="max-w-4xl mx-auto px-4 sm:px-6 py-8 space-y-5">

{{-- Page title --}}
<div class="flex items-center justify-between">
    <div>
        <p class="text-xs font-semibold tracking-widest text-gray-400 uppercase">Accountant Portal</p>
        <h1 class="text-lg font-extrabold tracking-tight text-gray-900">Remittance Approval</h1>
    </div>
</div>

{{-- Back --}}
<div>
        <a href="{{ route('remittance-approval.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl text-xs font-semibold no-underline hover:border-gray-600 hover:text-gray-600 hover:bg-gray-50 transition-colors whitespace-nowrap cursor-pointer">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to remittances
        </a>
</div>

{{-- Flash --}}
@foreach(['success','error'] as $t)
    @if(session($t))
    <div class="fade-up flex items-center gap-2 px-4 py-2.5 rounded-lg text-xs font-semibold {{ $t === 'success' ? 'bg-emerald-500/15 border border-emerald-500/25 text-emerald-700' : 'bg-red-500/15 border border-red-500/25 text-red-700' }}">
        @if($t==='success')<svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        @else<svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>@endif
        {{ session($t) }}
    </div>
    @endif
@endforeach
@if($errors->any())
    <div class="fade-up rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-xs font-semibold text-red-700">
        @foreach($errors->all() as $error)
            <p class="m-0">{{ $error }}</p>
        @endforeach
    </div>
@endif

{{-- Header Card --}}
<div class="fade-up bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-start justify-between flex-wrap gap-4">
    <div class="flex items-center gap-4">
        <div class="w-14 h-14 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </div>
        <div>
            <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Remittance details</p>
            <h1 class="text-xl font-bold text-gray-900">{{ $remittance->remittance_date?->format('F d, Y') }}</h1>
            <p class="text-xs text-gray-400 mt-0.5">{{ $route?->route_name ?? 'N/A' }} &middot; {{ $vehicle?->plate_number ?? 'N/A' }}</p>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold uppercase tracking-wide {{ $statusInfo['bg'] }} {{ $statusInfo['text'] }}">
            <span class="w-2 h-2 rounded-full {{ $statusInfo['dot'] }}"></span>
            {{ $statusInfo['label'] }}
        </span>
        @if($remittance->status === 'pending')
            <button type="button" onclick="openRejectModal()"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-red-50 text-red-600 hover:bg-red-500 hover:text-white transition-all border-0 cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 9l-6 6M9 9l6 6"/></svg>
                Reject
            </button>
            <button type="button" onclick="openApproveModal()"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white transition-all border-0 cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Approve
            </button>
        @endif
    </div>
</div>

{{-- Personnel Card --}}
<div class="fade-up bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-50 flex items-center gap-2.5">
        <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <span class="text-xs font-bold text-gray-700">Personnel</span>
    </div>
    <div class="p-5">
        <div class="flex items-center justify-between py-3 border-b border-gray-50">
            <span class="text-xs font-semibold text-gray-500">Driver</span>
            <span class="text-xs text-gray-700 text-right max-w-[60%]">{{ $driver ? $driver->name : 'N/A' }}</span>
        </div>
        <div class="flex items-center justify-between py-3 border-b border-gray-50">
            <span class="text-xs font-semibold text-gray-500">PAO</span>
            <span class="text-xs text-gray-700 text-right max-w-[60%]">{{ $pao ? $pao->name : 'N/A' }}</span>
        </div>
        <div class="flex items-center justify-between py-3">
            <span class="text-xs font-semibold text-gray-500">Vehicle</span>
            <span class="text-xs text-gray-700 text-right max-w-[60%]">{{ $vehicle?->plate_number ?? 'N/A' }}</span>
        </div>
    </div>
</div>

{{-- Financial Card --}}
<div class="fade-up bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-50 flex items-center gap-2.5">
        <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <span class="text-xs font-bold text-gray-700">Financial Details</span>
    </div>
    <div class="p-5">
        <div class="flex items-center justify-between py-3 border-b border-gray-50">
            <span class="text-xs font-semibold text-gray-500">Total Collection</span>
            <span class="text-xs font-bold text-gray-900 font-mono">₱{{ number_format($remittance->total_collection, 2) }}</span>
        </div>
        <div class="flex items-center justify-between py-3 border-b border-gray-50">
            <span class="text-xs font-semibold text-gray-500">Total Expenses</span>
            <span class="text-xs font-bold text-amber-600 font-mono">₱{{ number_format($remittance->total_expenses, 2) }}</span>
        </div>
        <div class="flex items-center justify-between py-3 border-b border-gray-50">
            <span class="text-xs font-semibold text-gray-500">Net Remittance</span>
            <span class="text-xs font-bold text-emerald-600 font-mono">₱{{ number_format($remittance->net_remittance, 2) }}</span>
        </div>
        <div class="flex items-center justify-between py-3">
            <span class="text-xs font-semibold text-gray-500">Boundary</span>
            <span class="text-xs font-bold text-gray-900 font-mono">₱{{ number_format($remittance->boundary ?? 0, 2) }}</span>
        </div>
        @if($remittance->is_short_remittance)
        <div class="flex items-center gap-2 pt-3 mt-2 border-t border-gray-50">
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-red-50 text-red-600 border border-red-200">Remaining boundary: ₱{{ number_format($boundaryRemaining, 2) }}</span>
        </div>
        @endif
    </div>
</div>

{{-- Boundary payment history --}}
<div class="fade-up bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-50">
        <span class="text-xs font-bold text-gray-700">Boundary Payments</span>
        <p class="text-[10px] text-gray-400 mt-1">Clerk-recorded payments stay pending until you approve them. Approval posts each payment once. No receipt is required.</p>
    </div>
    @if($remittance->boundaryPayments->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-400">
                    <tr>
                        <th class="px-5 py-3">Internal payment ID</th>
                        <th class="px-5 py-3">Payment date / shortage share</th>
                        <th class="px-5 py-3 text-right">Amount</th>
                        <th class="px-5 py-3">Journal entry</th>
                        <th class="px-5 py-3">Recorded / reviewed by</th>
                        <th class="px-5 py-3">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($remittance->boundaryPayments as $payment)
                        <tr>
                            <td class="px-5 py-3 font-mono font-semibold text-gray-700">BP-{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-5 py-3 text-gray-600">
                                {{ $payment->payment_date->format('M d, Y') }}
                                <span class="block text-[10px] text-gray-400">{{ $payment->paid_by === 'pao' ? 'PAO share' : ($payment->paid_by === 'driver' ? 'Driver share' : 'Joint boundary') }}</span>
                            </td>
                            <td class="px-5 py-3 text-right font-mono font-semibold text-gray-800">₱{{ number_format($payment->amount, 2) }}</td>
                            <td class="px-5 py-3">
                                @if($payment->journalEntry)
                                    <a class="text-blue-700 underline" href="{{ route('accounting.journal-entries.show', $payment->journalEntry) }}">{{ $payment->journalEntry->journal_number }} ({{ $payment->journalEntry->status }})</a>
                                @elseif($payment->status === 'Pending')
                                    <span class="text-amber-700">Created upon approval</span>
                                @elseif($payment->status === 'Rejected')
                                    <span class="text-gray-500">Not posted</span>
                                @else
                                    <span class="text-red-600">Missing journal entry</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-gray-500">{{ $payment->recordedBy?->name ?? '—' }} / {{ $payment->approvedBy?->name ?? '—' }}</td>
                            <td class="px-5 py-3">
                                @if($payment->status === 'Pending')
                                    <div class="flex items-center gap-2">
                                        <form method="POST" action="{{ route('remittance-approval.boundary-payments.approve', $payment) }}">
                                            @csrf
                                            <button type="submit" class="rounded-md bg-emerald-600 px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">Approve</button>
                                        </form>
                                        <form method="POST" action="{{ route('remittance-approval.boundary-payments.reject', $payment) }}">
                                            @csrf
                                            <button type="submit" class="rounded-md border border-red-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-50">Reject</button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="px-5 py-4 text-xs text-gray-500">No boundary payments have been submitted.</p>
    @endif

    <div class="px-5 py-4 border-t border-gray-100 flex items-center justify-between">
        <span class="text-xs font-semibold text-gray-600">Boundary remaining: <strong class="font-mono text-gray-900">₱{{ number_format($boundaryRemaining, 2) }}</strong></span>
        <span class="text-xs text-gray-500">Clerks record payments from the Short Remittance page; approve or reject pending submissions above.</span>
    </div>
</div>

{{-- Rejected badge --}}
@if($remittance->status === 'rejected')
    <div class="fade-up bg-red-50 border border-red-200 rounded-xl px-4 py-3.5 flex items-start gap-3">
        <div class="w-8 h-8 rounded-lg bg-red-100 text-red-500 flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-bold text-red-700">Remittance Rejected</p>
        </div>
    </div>
@endif

{{-- Resolution notes --}}
@if($remittance->resolution_notes)
    <div class="fade-up bg-blue-50 border border-blue-200 rounded-xl px-4 py-3.5 flex items-start gap-3">
        <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-500 flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-bold text-blue-700">Resolution Notes</p>
            <p class="text-xs text-blue-600 mt-1">{{ $remittance->resolution_notes }}</p>
        </div>
    </div>
@endif

</div>

{{-- Reject Modal --}}
<div class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-[9999] p-5 modal-overlay" id="rejectModalOverlay">
    <div class="bg-white rounded-2xl p-6 max-w-lg w-full shadow-2xl modal-panel">
        <h3 class="text-base font-bold text-gray-900">Reject Remittance</h3>
        <p class="text-xs text-gray-400 mt-1 mb-4">₱{{ number_format($remittance->net_remittance, 2) }} &middot; {{ $route?->route_name ?? 'N/A' }} &middot; {{ $remittance->remittance_date?->format('M d, Y') }}</p>
        <p class="text-sm text-gray-600 mb-4 bg-amber-50 rounded-lg px-3 py-2 border border-amber-100">This will mark the remittance as rejected. The remittance clerk will be notified.</p>
        <form method="POST" action="{{ route('remittance-approval.reject', $remittance) }}">
            @csrf
            <div class="flex gap-2">
                <button type="button" onclick="closeRejectModal()"
                        class="flex-1 py-2.5 bg-white text-gray-600 border border-gray-200 rounded-xl text-sm font-semibold transition-all hover:bg-gray-50 active:scale-[0.97] cursor-pointer">
                    Cancel
                </button>
                <button type="submit"
                        class="flex-1 py-2.5 bg-red-600 text-white rounded-xl text-sm font-semibold transition-all hover:bg-red-700 active:scale-[0.97] cursor-pointer border-0">
                    Reject Remittance
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Approve Modal --}}
<div class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-[9999] p-5 modal-overlay" id="approveModalOverlay">
    <div class="bg-white rounded-2xl p-6 max-w-lg w-full shadow-2xl modal-panel">
        <h3 class="text-base font-bold text-gray-900">Approve Remittance</h3>
        <p class="text-xs text-gray-400 mt-1 mb-4">₱{{ number_format($remittance->net_remittance, 2) }} &middot; {{ $route?->route_name ?? 'N/A' }} &middot; {{ $remittance->remittance_date?->format('M d, Y') }}</p>
        @if((float) $remittance->net_remittance > (float) $remittance->boundary)
            <p class="text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2 mb-4">The entered remittance exceeds the fixed boundary. Excess will not be posted as boundary revenue.</p>
        @endif
        <form method="POST" action="{{ route('remittance-approval.approve', $remittance) }}">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">Boundary amount to confirm</label>
                    <input type="number" value="{{ number_format($initialPaymentAmount, 2, '.', '') }}" readonly aria-readonly="true" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-xs bg-gray-50 text-gray-700 cursor-not-allowed">
                    <p class="text-[10px] text-gray-400 mt-1">Calculated from the recorded net remittance, capped at the fixed boundary.</p>
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">Payment date</label>
                    <input type="date" name="boundary_payment_date" max="{{ now()->toDateString() }}" value="{{ old('boundary_payment_date', $remittance->remittance_date?->toDateString()) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-xs">
                </div>
                <label class="sm:col-span-2 flex items-start gap-2 text-xs text-gray-600">
                    <input type="checkbox" name="confirm_boundary_received" value="1" class="mt-0.5">
                    I confirm this calculated boundary amount was actually received and verified. Only this amount will be posted.
                </label>
            </div>
            <div class="flex gap-2">
                <button type="button" onclick="closeApproveModal()"
                        class="flex-1 py-2.5 bg-white text-gray-600 border border-gray-200 rounded-xl text-sm font-semibold transition-all hover:bg-gray-50 active:scale-[0.97] cursor-pointer">
                    Cancel
                </button>
                <button type="submit"
                        class="flex-1 py-2.5 bg-emerald-600 text-white rounded-xl text-sm font-semibold transition-all hover:bg-emerald-700 active:scale-[0.97] cursor-pointer border-0">
                    Confirm Approval
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openRejectModal() { document.getElementById('rejectModalOverlay').classList.remove('hidden'); document.getElementById('rejectModalOverlay').classList.add('flex'); }
function closeRejectModal() { document.getElementById('rejectModalOverlay').classList.add('hidden'); document.getElementById('rejectModalOverlay').classList.remove('flex'); }
function openApproveModal() { document.getElementById('approveModalOverlay').classList.remove('hidden'); document.getElementById('approveModalOverlay').classList.add('flex'); }
function closeApproveModal() { document.getElementById('approveModalOverlay').classList.add('hidden'); document.getElementById('approveModalOverlay').classList.remove('flex'); }
</script>
@endpush
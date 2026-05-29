@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeSlideUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { 0%{opacity:0;transform:scale(0.92)} 100%{opacity:1;transform:scale(1)} }
.fade-up { animation:fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.stat-card { animation:scaleIn 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.stat-card:nth-child(1) { animation-delay:0.05s; }
.stat-card:nth-child(2) { animation-delay:0.1s; }
.stat-card:nth-child(3) { animation-delay:0.15s; }
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
$statusInfo = match($remittance->status) {
    'pending'  => ['label'=>'Pending',  'dot'=>'bg-amber-400', 'text'=>'text-amber-600', 'bg'=>'bg-amber-50'],
    'approved' => ['label'=>'Approved', 'dot'=>'bg-emerald-400','text'=>'text-emerald-600','bg'=>'bg-emerald-50'],
    'rejected' => ['label'=>'Rejected', 'dot'=>'bg-red-400',   'text'=>'text-red-600',   'bg'=>'bg-red-50'],
    default    => ['label'=>'Pending',  'dot'=>'bg-amber-400', 'text'=>'text-amber-600', 'bg'=>'bg-amber-50'],
};
@endphp

<div class="space-y-5">

{{-- Flash --}}
@foreach(['success','error'] as $t)
    @if(session($t))
    <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl text-sm font-medium @switch($t) @case('success') bg-green-50 text-green-700 @break @case('error') bg-red-50 text-red-700 @break @endswitch fade-up">
        <span>{{ session($t) }}</span>
    </div>
    @endif
@endforeach

{{-- Header --}}
<div class="fade-up flex items-start justify-between gap-4">
    <div class="min-w-0">
        <a href="{{ route('remittance-approval.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-600 transition-all hover:bg-gray-50 active:scale-[0.97] no-underline mb-2">
            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Back to remittances
        </a>
        <h1 class="text-xl font-bold text-gray-900 tracking-tight">{{ $remittance->remittance_date?->format('F d, Y') }}</h1>
        <p class="text-xs text-gray-400 mt-0.5 font-mono">{{ $route?->route_name ?? 'N/A' }} &middot; {{ $vehicle?->plate_number ?? 'N/A' }}</p>
    </div>
    <div class="flex items-center gap-2 shrink-0 flex-wrap">
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold uppercase tracking-wide {{ $statusInfo['bg'] }} {{ $statusInfo['text'] }}">
            <span class="w-2 h-2 rounded-full {{ $statusInfo['dot'] }}"></span>
            {{ $statusInfo['label'] }}
        </span>
        @if($remittance->status === 'pending')
            <button type="button" onclick="openRejectModal()"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-red-200 rounded-lg text-xs font-semibold text-red-600 transition-all hover:bg-red-50 active:scale-[0.97] cursor-pointer">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 9l-6 6M9 9l6 6"/></svg>
                Reject
            </button>
            <button type="button" onclick="openApproveModal()"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 text-white rounded-lg text-xs font-semibold transition-all hover:bg-emerald-700 active:scale-[0.97] cursor-pointer border-0">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Approve
            </button>
        @endif
    </div>
</div>

{{-- Personnel Card --}}
<div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-3 border-b border-gray-50 bg-gray-50/30">
        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Personnel</span>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-gray-50">
        <div class="p-4">
            <p class="text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400">Driver</p>
            <p class="text-sm font-semibold text-gray-900 mt-1">{{ $driver ? $driver->name : 'N/A' }}</p>
        </div>
        <div class="p-4">
            <p class="text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400">PAO</p>
            <p class="text-sm font-semibold text-gray-900 mt-1">{{ $pao ? $pao->name : 'N/A' }}</p>
        </div>
        <div class="p-4">
            <p class="text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400">Vehicle</p>
            <p class="text-sm font-semibold text-gray-900 mt-1">{{ $vehicle?->plate_number ?? 'N/A' }}</p>
        </div>
    </div>
</div>

{{-- Financial Card --}}
<div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-3 border-b border-gray-50 bg-gray-50/30">
        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Financial Details</span>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-px bg-gray-50">
        <div class="bg-white p-4">
            <p class="text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400">Total Collection</p>
            <p class="text-lg font-bold text-gray-900 tabular-nums mt-1">₱{{ number_format($remittance->total_collection, 2) }}</p>
        </div>
        <div class="bg-white p-4">
            <p class="text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400">Total Expenses</p>
            <p class="text-lg font-bold text-amber-600 tabular-nums mt-1">₱{{ number_format($remittance->total_expenses, 2) }}</p>
        </div>
        <div class="bg-white p-4">
            <p class="text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400">Net Remittance</p>
            <p class="text-lg font-bold text-emerald-600 tabular-nums mt-1">₱{{ number_format($remittance->net_remittance, 2) }}</p>
        </div>
        <div class="bg-white p-4">
            <p class="text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400">Boundary</p>
            <p class="text-lg font-bold text-gray-900 tabular-nums mt-1">₱{{ number_format($remittance->boundary ?? 0, 2) }}</p>
        </div>
    </div>
    @if($remittance->is_short_remittance)
        <div class="px-5 py-3 border-t border-gray-50 bg-red-50/30 flex items-center gap-2">
            <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg>
            <span class="text-xs font-semibold text-red-700">Short Remittance</span>
            <span class="text-xs text-red-500 font-mono">₱{{ number_format($remittance->short_amount ?? 0, 2) }}</span>
        </div>
    @endif
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
        <form method="POST" action="{{ route('remittance-approval.approve', $remittance) }}">
            @csrf
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
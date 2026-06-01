@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeSlideUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { 0%{opacity:0;transform:scale(0.92)} 100%{opacity:1;transform:scale(1)} }
.fade-up { animation:fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.stat-card { animation:scaleIn 0.4s cubic-bezier(0.16,1,0.3,1) both; }
@keyframes modalFadeIn { 0%{opacity:0} 100%{opacity:1} }
@keyframes modalScaleIn { 0%{opacity:0;transform:scale(0.92) translateY(8px)} 100%{opacity:1;transform:scale(1) translateY(0)} }
.modal-overlay { animation:modalFadeIn 0.2s ease-out both; }
.modal-panel { animation:modalScaleIn 0.25s cubic-bezier(0.16,1,0.3,1) both; }
</style>
@endpush

@section('content')
<div class="space-y-5">
@php
    $status    = $batch->status;
    $startDate = $batch->period_start;
    $endDate   = $batch->period_end;
    $isFirst   = $startDate->format('d') <= 15;

    $statusInfo = match($status) {
        'submitted' => ['label'=>'Pending',   'dot'=>'bg-amber-400', 'text'=>'text-amber-600', 'bg'=>'bg-amber-50'],
        'approved'  => ['label'=>'Approved',  'dot'=>'bg-emerald-400','text'=>'text-emerald-600','bg'=>'bg-emerald-50'],
        'rejected'  => ['label'=>'Rejected',  'dot'=>'bg-red-400',   'text'=>'text-red-600',   'bg'=>'bg-red-50'],
        default     => ['label'=>'Pending',   'dot'=>'bg-amber-400', 'text'=>'text-amber-600', 'bg'=>'bg-amber-50'],
    };
@endphp

{{-- Flash --}}
@if(session('success'))
    <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl text-sm font-medium bg-green-50 text-green-700 fade-up">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
@elseif(session('error'))
    <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl text-sm font-medium bg-red-50 text-red-700 fade-up">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
        {{ session('error') }}
    </div>
@endif

{{-- Rejection banner --}}
@if($status === 'rejected' && $batch->rejection_note)
    <div class="fade-up bg-red-50 border border-red-200 rounded-xl px-4 py-3.5 flex items-start gap-3">
        <div class="w-8 h-8 rounded-lg bg-red-100 text-red-500 flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-bold text-red-700">Batch Rejected</p>
            <p class="text-xs text-red-500 mt-0.5">
                @if($batch->approved_at) &middot; {{ $batch->approved_at->format('M d, Y \a\t h:i A') }} @endif
            </p>
            <p class="text-xs text-red-600 mt-2 bg-red-100/60 rounded-lg px-3 py-2">{{ $batch->rejection_note }}</p>
        </div>
    </div>
@endif

{{-- Header --}}
<div class="fade-up flex items-start justify-between gap-4">
    <div class="min-w-0">
        <a href="{{ route('payroll-approval.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl text-xs font-semibold no-underline hover:border-gray-600 hover:text-gray-600 hover:bg-gray-50 transition-colors whitespace-nowrap cursor-pointer mb-2">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to batches
        </a>
        <h1 class="text-xl font-bold text-gray-900 tracking-tight">{{ $startDate->format('F Y') }} — {{ $isFirst ? '1st' : '2nd' }} Half</h1>
        <p class="text-xs text-gray-400 mt-0.5 font-mono">{{ $startDate->format('M d, Y') }} &ndash; {{ $endDate->format('M d, Y') }}</p>
    </div>
    <div class="flex items-center gap-2 shrink-0 flex-wrap">
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold uppercase tracking-wide {{ $statusInfo['bg'] }} {{ $statusInfo['text'] }}">
            <span class="w-2 h-2 rounded-full {{ $statusInfo['dot'] }}"></span>
            {{ $statusInfo['label'] }}
        </span>
        @if($status === 'submitted' && $payrolls->isNotEmpty())
            <button type="button" onclick="openRejectModal({{ $batch->id }}, {{ $payrolls->count() }})"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-red-200 rounded-lg text-xs font-semibold text-red-600 transition-all hover:bg-red-50 active:scale-[0.97] cursor-pointer">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 9l-6 6M9 9l6 6"/></svg>
                Reject
            </button>
            <button type="button" onclick="openApproveModal({{ $batch->id }}, {{ $payrolls->count() }})"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 text-white rounded-lg text-xs font-semibold transition-all hover:bg-emerald-700 active:scale-[0.97] cursor-pointer border-0">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Approve
            </button>
        @endif
    </div>
</div>

{{-- Batch Summary Card --}}
<div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-3 border-b border-gray-50 bg-gray-50/30">
        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Batch Summary</span>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-px bg-gray-50">
        <div class="bg-white p-4">
            <p class="text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400">Employees</p>
            <p class="text-lg font-bold text-gray-900 tabular-nums mt-1">{{ $payrolls->count() }}</p>
        </div>
        <div class="bg-white p-4">
            <p class="text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400">Gross Pay</p>
            <p class="text-lg font-bold text-gray-900 tabular-nums mt-1">₱{{ number_format($totalGross, 2) }}</p>
        </div>
        <div class="bg-white p-4">
            <p class="text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400">Deductions</p>
            <p class="text-lg font-bold text-amber-600 tabular-nums mt-1">₱{{ number_format($totalDeductions, 2) }}</p>
        </div>
        <div class="bg-white p-4">
            <p class="text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400">Net Pay</p>
            <p class="text-lg font-bold text-emerald-600 tabular-nums mt-1">₱{{ number_format($totalNet, 2) }}</p>
        </div>
    </div>
</div>

{{-- Employees Card --}}
<div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-3 border-b border-gray-50 bg-gray-50/30 flex items-center justify-between">
        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Employees</span>
        <span class="text-xs text-gray-400 tabular-nums">{{ $payrolls->count() }} records</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-50">
                    <th class="text-left text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Employee</th>
                    <th class="text-left text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Department</th>
                    <th class="text-right text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Days</th>
                    <th class="text-right text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Basic</th>
                    <th class="text-right text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Gross</th>
                    <th class="text-right text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Deductions</th>
                    <th class="text-right text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Net</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($payrolls as $payroll)
                    @php
                        $user = $payroll->user;
                    @endphp
                    <tr onclick="window.location='{{ route('payroll-approval.show', $payroll) }}'" class="transition-colors hover:bg-gray-50/40 cursor-pointer">
                        <td class="px-4 py-3">
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-gray-900 truncate">{{ $user->first_name }} {{ $user->last_name }}</p>
                                <p class="text-[0.55rem] text-gray-400 mt-0.5 font-mono truncate">{{ $user->position ?? '—' }}</p>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $user->department ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-right text-xs font-semibold text-gray-900 tabular-nums">{{ $payroll->days_worked ?? '—' }}</td>
                        <td class="px-4 py-3 text-right text-xs font-semibold text-gray-900 tabular-nums">₱{{ number_format($payroll->basic_salary, 2) }}</td>
                        <td class="px-4 py-3 text-right text-xs font-semibold text-gray-900 tabular-nums">₱{{ number_format($payroll->gross_pay, 2) }}</td>
                        <td class="px-4 py-3 text-right text-xs font-semibold text-amber-600 tabular-nums">₱{{ number_format($payroll->total_deductions, 2) }}</td>
                        <td class="px-4 py-3 text-right text-xs font-bold text-emerald-600 tabular-nums">₱{{ number_format($payroll->net_pay, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-xs text-gray-400">No employees in this batch.</td>
                    </tr>
                @endforelse
            </tbody>
            @if($payrolls->isNotEmpty())
                <tfoot>
                    <tr class="border-t border-gray-100 bg-gray-50/50">
                        <td colspan="3" class="px-4 py-3 text-xs font-bold text-gray-900">Totals</td>
                        <td class="px-4 py-3 text-right text-xs font-bold text-gray-900 tabular-nums">₱{{ number_format($payrolls->sum('basic_salary'), 2) }}</td>
                        <td class="px-4 py-3 text-right text-xs font-bold text-gray-900 tabular-nums">₱{{ number_format($totalGross, 2) }}</td>
                        <td class="px-4 py-3 text-right text-xs font-bold text-amber-600 tabular-nums">₱{{ number_format($totalDeductions, 2) }}</td>
                        <td class="px-4 py-3 text-right text-xs font-bold text-emerald-600 tabular-nums">₱{{ number_format($totalNet, 2) }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>
</div>

{{-- Reject Modal --}}
<div class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-[9999] p-5 modal-overlay" id="rejectModalOverlay">
    <div class="bg-white rounded-2xl p-6 max-w-lg w-full shadow-2xl modal-panel">
        <h3 class="text-base font-bold text-gray-900">Reject Payroll Batch</h3>
        <p id="rejectDesc" class="text-xs text-gray-400 mt-1 mb-4">All employees in this batch will be notified.</p>
        <form id="rejectForm" method="POST" action="{{ route('payroll-approval.reject-batch') }}">
            @csrf
            <input type="hidden" name="batch_id" id="rejectBatchId" value="">
            <div class="mb-4">
                <label for="rejection_reason" class="block text-xs font-bold uppercase tracking-wide text-gray-500 mb-1.5">
                    Rejection Reason <span class="text-amber-600">*</span>
                </label>
                <textarea name="rejection_reason" id="rejectNote" rows="3" required
                          placeholder="Explain why this payroll batch is being rejected…"
                          class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 placeholder:text-gray-300 resize-none outline-none transition-all focus:border-gray-400 focus:bg-white"></textarea>
                <p class="text-xs text-gray-400 mt-1.5">The HR team will be notified and can resubmit.</p>
            </div>
            <div class="flex gap-2">
                <button type="button" onclick="closeRejectModal()"
                        class="flex-1 py-2.5 bg-white text-gray-600 border border-gray-200 rounded-xl text-sm font-semibold transition-all hover:bg-gray-50 active:scale-[0.97] cursor-pointer">
                    Cancel
                </button>
                <button type="submit"
                        class="flex-1 py-2.5 bg-red-600 text-white rounded-xl text-sm font-semibold transition-all hover:bg-red-700 active:scale-[0.97] cursor-pointer border-0">
                    Reject Request
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Approve Modal --}}
<div class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-[9999] p-5 modal-overlay" id="approveModalOverlay">
    <div class="bg-white rounded-2xl p-6 max-w-lg w-full shadow-2xl modal-panel">
        <h3 class="text-base font-bold text-gray-900">Approve Payroll Batch</h3>
        <p id="approveDesc" class="text-xs text-gray-400 mt-1 mb-4">Confirm approval of this payroll batch</p>
        <form id="approveForm" method="POST" action="{{ route('payroll-approval.approve-batch') }}">
            @csrf
            <input type="hidden" name="batch_id" id="approveBatchId" value="">
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
function openRejectModal(batchId, count) {
    document.getElementById('rejectBatchId').value = batchId;
    document.getElementById('rejectDesc').textContent = count + ' payroll record(s) will be rejected';
    document.getElementById('rejectNote').value = '';
    document.getElementById('rejectModalOverlay').classList.remove('hidden');
    document.getElementById('rejectModalOverlay').classList.add('flex');
}
function closeRejectModal() {
    document.getElementById('rejectModalOverlay').classList.add('hidden');
    document.getElementById('rejectModalOverlay').classList.remove('flex');
}
function openApproveModal(batchId, count) {
    document.getElementById('approveBatchId').value = batchId;
    document.getElementById('approveDesc').textContent = count + ' payroll record(s) will be approved and locked';
    document.getElementById('approveModalOverlay').classList.remove('hidden');
    document.getElementById('approveModalOverlay').classList.add('flex');
}
function closeApproveModal() {
    document.getElementById('approveModalOverlay').classList.add('hidden');
    document.getElementById('approveModalOverlay').classList.remove('flex');
}
</script>
@endpush
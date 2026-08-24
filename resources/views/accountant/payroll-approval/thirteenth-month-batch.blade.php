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
.stat-card:nth-child(4) { animation-delay:0.2s; }
</style>
@endpush

@section('content')
<div class="space-y-5">
@php
    $status    = $batch->status;
    $startDate = $batch->period_start;

    $statusInfo = match($status) {
        'draft'     => ['label'=>'Draft',     'dot'=>'bg-amber-300', 'text'=>'text-amber-500', 'bg'=>'bg-amber-50'],
        'submitted' => ['label'=>'Pending',   'dot'=>'bg-amber-400', 'text'=>'text-amber-600', 'bg'=>'bg-amber-50'],
        'approved'  => ['label'=>'Approved',  'dot'=>'bg-emerald-400','text'=>'text-emerald-600','bg'=>'bg-emerald-50'],
        'rejected'  => ['label'=>'Rejected',  'dot'=>'bg-red-400',   'text'=>'text-red-600',   'bg'=>'bg-red-50'],
        default     => ['label'=>'Draft',     'dot'=>'bg-amber-300', 'text'=>'text-amber-500', 'bg'=>'bg-amber-50'],
    };
@endphp

@foreach(['success','error'] as $t)
    @if(session($t))
    <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl text-sm font-medium animate-[fadeSlideUp_0.3s_ease] @switch($t) @case('success') bg-green-50 text-green-700 @break @case('error') bg-red-50 text-red-700 @break @endswitch">
        <span>{{ session($t) }}</span>
    </div>
    @endif
@endforeach

@if($status === 'rejected' && $batch->rejection_note)
    <div class="fade-up bg-red-50 border border-red-200 rounded-xl px-4 py-3.5 flex items-start gap-3">
        <div class="w-8 h-8 rounded-lg bg-red-100 text-red-500 flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-bold text-red-700">Batch Rejected</p>
            <p class="text-xs text-red-600 mt-2 bg-red-100/60 rounded-lg px-3 py-2">{{ $batch->rejection_note }}</p>
        </div>
    </div>
@endif

<div class="fade-up flex items-start justify-between gap-4">
    <div class="min-w-0">
        <a href="{{ route('payroll-approval.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl text-xs font-semibold no-underline hover:border-gray-600 hover:text-gray-600 hover:bg-gray-50 transition-colors whitespace-nowrap cursor-pointer mb-2">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to batches
        </a>
        <h1 class="text-xl font-bold text-gray-900 tracking-tight">13th Month Pay &mdash; {{ $startDate->format('Y') }}</h1>
        <p class="text-xs text-gray-400 mt-0.5 font-mono">{{ $startDate->format('M d, Y') }} &ndash; {{ $batch->period_end->format('M d, Y') }}</p>
    </div>
    <div class="flex items-center gap-2 shrink-0 flex-wrap">
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold uppercase tracking-wide {{ $statusInfo['bg'] }} {{ $statusInfo['text'] }}">
            <span class="w-2 h-2 rounded-full {{ $statusInfo['dot'] }}"></span>
            {{ $statusInfo['label'] }}
        </span>
        @if($status === 'submitted' && $records->isNotEmpty())
            <button type="button" onclick="openRejectModal({{ $batch->id }}, {{ $records->count() }})"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-red-200 rounded-lg text-xs font-semibold text-red-600 transition-all hover:bg-red-50 active:scale-[0.97] cursor-pointer">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 9l-6 6M9 9l6 6"/></svg>
                Reject
            </button>
            <button type="button" onclick="openApproveModal({{ $batch->id }}, {{ $records->count() }})"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 text-white rounded-lg text-xs font-semibold transition-all hover:bg-emerald-700 active:scale-[0.97] cursor-pointer border-0">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Approve All
            </button>
        @endif
    </div>
</div>

<div class="grid grid-cols-2 gap-3">
    <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <p class="text-xs text-gray-400 font-medium">Employees</p>
        <p class="text-lg font-bold text-gray-900 tabular-nums mt-1">{{ $records->count() }}</p>
    </div>
    <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <p class="text-xs text-gray-400 font-medium">Total Payable</p>
        <p class="text-lg font-bold text-amber-600 tabular-nums mt-1">₱{{ number_format($totalPayable, 2) }}</p>
    </div>
</div>

<div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-50 flex items-center justify-between">
            <span class="text-sm font-semibold text-gray-900">Employee Records</span>
            <span class="text-xs text-gray-400 tabular-nums">{{ $records->count() }} records</span>
        </div>

        @if($records->isEmpty())
        <div class="flex flex-col items-center py-12 text-center">
            <p class="text-sm font-semibold text-gray-500">No records in this batch</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="text-left text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Employee</th>
                        <th class="text-right text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Basic Salary</th>
                        <th class="text-right text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Months Worked</th>
                        <th class="text-right text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">13th Month Pay</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($records as $record)
                    @php $u = $record->user; @endphp
                    <tr class="hover:bg-gray-50/40 transition-colors cursor-pointer" onclick="window.open('{{ route('payroll.thirteenth-month-pay.batch.payslip', ['batch' => $batch, 'record' => $record]) }}', '_blank')">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center text-[11px] font-bold text-gray-600 uppercase shrink-0">
                                    {{ substr($u->first_name ?? '?', 0, 1) }}{{ substr($u->last_name ?? '?', 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-semibold text-gray-900 truncate">{{ $u->last_name ?? '' }}, {{ $u->first_name ?? '' }}</p>
                                    <p class="text-[0.55rem] text-gray-400 truncate font-mono">{{ $u->position ?? '—' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right text-xs font-semibold text-gray-900 tabular-nums">₱{{ number_format($record->total_basic_salary_earned, 2) }}</td>
                        <td class="px-4 py-3 text-right text-xs font-semibold text-gray-900 tabular-nums">{{ $record->months_worked }}</td>
                        <td class="px-4 py-3 text-right text-xs font-bold text-amber-600 tabular-nums">₱{{ number_format($record->thirteenth_month_pay, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                @if($records->isNotEmpty())
                <tfoot>
                    <tr class="border-t border-gray-100 bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-bold text-gray-900">Totals</td>
                        <td class="px-4 py-3 text-right text-xs font-bold text-gray-900 tabular-nums">₱{{ number_format($records->sum('total_basic_salary_earned'), 2) }}</td>
                        <td class="px-4 py-3 text-right text-xs font-bold text-gray-900 tabular-nums"></td>
                        <td class="px-4 py-3 text-right text-xs font-bold text-amber-600 tabular-nums">₱{{ number_format($totalPayable, 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
        @endif
    </div>

</div>

{{-- Approve Modal --}}
<div class="fixed inset-0 z-[9999] flex items-center justify-center p-5 hidden modal-overlay" id="approveModal">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="document.getElementById('approveModal').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-2xl p-6 w-full max-w-sm mx-4 shadow-2xl modal-card">
        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center mb-4 mx-auto">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        </div>
        <h3 class="text-lg font-bold text-center text-gray-900">Approve Batch?</h3>
        <p class="text-sm text-center text-gray-500 mt-1">This will approve <strong id="approveCount"></strong> 13th month pay record(s).</p>
        <form id="approveForm" method="POST" action="{{ route('payroll-approval.approve-batch') }}" class="mt-5 flex gap-2 justify-center">
            @csrf
            <input type="hidden" name="batch_id" id="approveBatchId">
            <button type="button" onclick="document.getElementById('approveModal').classList.add('hidden')" class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-50 cursor-pointer">Cancel</button>
            <button type="submit" class="px-5 py-2 bg-emerald-600 text-white rounded-lg text-xs font-semibold hover:bg-emerald-700 active:scale-[0.97] cursor-pointer border-0">Approve</button>
        </form>
    </div>
</div>

{{-- Reject Modal --}}
<div class="fixed inset-0 z-[9999] flex items-center justify-center p-5 hidden modal-overlay" id="rejectModal">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="document.getElementById('rejectModal').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-2xl p-6 w-full max-w-sm mx-4 shadow-2xl modal-card">
        <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center mb-4 mx-auto">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 9l-6 6M9 9l6 6"/></svg>
        </div>
        <h3 class="text-lg font-bold text-center text-gray-900">Reject Batch?</h3>
        <p class="text-sm text-center text-gray-500 mt-1">Provide a reason for rejection.</p>
        <form id="rejectForm" method="POST" action="{{ route('payroll-approval.reject-batch') }}" class="mt-5 space-y-3">
            @csrf
            <input type="hidden" name="batch_id" id="rejectBatchId">
            <textarea name="rejection_note" required rows="3" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm text-gray-900 outline-none transition-all focus:border-red-400 focus:ring-2 focus:ring-red-100 resize-none" placeholder="Reason for rejection..."></textarea>
            <div class="flex gap-2 justify-center">
                <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-50 cursor-pointer">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-red-600 text-white rounded-lg text-xs font-semibold hover:bg-red-700 active:scale-[0.97] cursor-pointer border-0">Reject</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openApproveModal(batchId, count) {
    document.getElementById('approveBatchId').value = batchId;
    document.getElementById('approveCount').textContent = count;
    document.getElementById('approveModal').classList.remove('hidden');
}
function openRejectModal(batchId, count) {
    document.getElementById('rejectBatchId').value = batchId;
    document.getElementById('rejectModal').classList.remove('hidden');
}
document.getElementById('approveModal').addEventListener('click', function(e) {
    if (e.target === this) this.classList.add('hidden');
});
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) this.classList.add('hidden');
});
</script>
@endpush

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
@php
    $status    = $batch->status;
    $startDate = $batch->period_start;
    $endDate   = $batch->period_end;
    $isFirst   = $startDate->format('d') <= 15;

    $statusLabel = match($status) {
        'submitted' => 'Pending',
        'approved'  => 'Approved',
        'rejected'  => 'Rejected',
        default => ucfirst($status),
    };
    list($statusColor, $statusBg) = match($status) {
        'submitted' => ['amber-600', 'amber-50'],
        'approved'  => ['emerald-600', 'emerald-50'],
        'rejected'  => ['red-600', 'red-50'],
        default     => ['gray-600', 'gray-50'],
    };
@endphp

@if(session('success'))
        <div class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-500/15 border border-emerald-500/25 rounded-lg text-emerald-700 text-xs font-semibold mb-4">
            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @elseif(session('error'))
        <div class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-500/15 border border-red-500/25 rounded-lg text-red-700 text-xs font-semibold mb-4">
            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="flex items-start justify-between mb-6 flex-wrap gap-4 fade-up">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('payroll-approval.index') }}" class="inline-flex items-center gap-1 text-gray-400 hover:text-gray-600 text-xs transition-colors no-underline">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    Payroll Approval
                </a>
                <span class="text-gray-300 text-xs">/</span>
                <span class="text-xs text-gray-400 font-mono">#{{ str_pad($batch->id, 3, '0', STR_PAD_LEFT) }}</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">{{ $startDate->format('F Y') }} — {{ $isFirst ? '1st' : '2nd' }} Half</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $startDate->format('F d, Y') }} – {{ $endDate->format('F d, Y') }}</p>
        </div>
        <div class="flex items-center gap-2">
            @php
                $badgeClasses = match($status) {
                    'submitted' => 'bg-amber-50 text-amber-600 border-amber-200',
                    'approved'  => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                    'rejected'  => 'bg-red-50 text-red-600 border-red-200',
                    default     => 'bg-gray-50 text-gray-600 border-gray-200',
                };
                $dotClasses = match($status) {
                    'submitted' => 'bg-amber-500',
                    'approved'  => 'bg-emerald-600',
                    'rejected'  => 'bg-red-600',
                    default     => 'bg-gray-600',
                };
            @endphp
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[0.6rem] font-semibold border {{ $badgeClasses }}">
                @if($status === 'submitted')
                    <span class="w-1.5 h-1.5 rounded-full {{ $dotClasses }} animate-pulse"></span>
                @else
                    <span class="w-1.5 h-1.5 rounded-full {{ $dotClasses }}"></span>
                @endif
                {{ $statusLabel }}
            </span>
        </div>
    </div>

    {{-- Rejection note banner --}}
    @if($status === 'rejected' && $batch->rejection_note)
        <div class="fade-up bg-red-50 border border-red-200 rounded-xl p-4 mb-5 flex gap-3 items-start">
            <div class="w-8 h-8 rounded-lg bg-red-100 text-red-500 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg>
            </div>
            <div>
                <p class="text-[0.55rem] font-bold uppercase tracking-wider text-red-600 mb-0.5">Rejection reason</p>
                <p class="text-xs text-red-800">{{ $batch->rejection_note }}</p>
            </div>
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
        <div class="scale-in stat-card bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm hover:shadow-md hover:border-blue-200 transition-all duration-300">
            <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Employees</p>
            <p class="text-xl font-bold text-gray-900 tabular-nums mt-1">{{ $payrolls->count() }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">In this batch</p>
        </div>
        <div class="scale-in stat-card bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm hover:shadow-md hover:border-emerald-200 transition-all duration-300">
            <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Gross pay</p>
            <p class="text-lg font-bold text-emerald-600 tabular-nums mt-1">₱{{ number_format($totalGross, 0) }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Combined total</p>
        </div>
        <div class="scale-in stat-card bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm hover:shadow-md hover:border-amber-200 transition-all duration-300">
            <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Deductions</p>
            <p class="text-lg font-bold text-amber-600 tabular-nums mt-1">₱{{ number_format($totalDeductions, 0) }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Combined total</p>
        </div>
        <div class="scale-in stat-card bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm hover:shadow-md hover:border-indigo-200 transition-all duration-300">
            <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Net pay</p>
            <p class="text-lg font-bold text-indigo-600 tabular-nums mt-1">₱{{ number_format($totalNet, 0) }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Take-home total</p>
        </div>
    </div>

    {{-- Batch meta info --}}
    <div class="fade-up bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-5 flex flex-wrap gap-x-8 gap-y-3 text-sm">
        @if($batch->generatedBy)
            <div>
                <p class="text-[0.5rem] font-bold uppercase tracking-wider text-gray-400">Generated by</p>
                <p class="text-xs font-semibold text-gray-700 mt-0.5">{{ $batch->generatedBy->first_name }} {{ $batch->generatedBy->last_name }}</p>
            </div>
        @endif
        @if($batch->finalizedBy)
            <div>
                <p class="text-[0.5rem] font-bold uppercase tracking-wider text-gray-400">Submitted by</p>
                <p class="text-xs font-semibold text-gray-700 mt-0.5">{{ $batch->finalizedBy->first_name }} {{ $batch->finalizedBy->last_name }}</p>
            </div>
        @endif
        @if($batch->finalized_at)
            <div>
                <p class="text-[0.5rem] font-bold uppercase tracking-wider text-gray-400">Submitted on</p>
                <p class="text-xs font-semibold text-gray-700 mt-0.5">{{ $batch->finalized_at->format('M d, Y g:i A') }}</p>
            </div>
        @endif
        @if($batch->approved_at)
            <div>
                <p class="text-[0.5rem] font-bold uppercase tracking-wider text-gray-400">Approved on</p>
                <p class="text-xs font-semibold text-emerald-600 mt-0.5">{{ $batch->approved_at->format('M d, Y g:i A') }}</p>
            </div>
        @endif
        @if($batch->rejected_at)
            <div>
                <p class="text-[0.5rem] font-bold uppercase tracking-wider text-gray-400">Rejected on</p>
                <p class="text-xs font-semibold text-red-600 mt-0.5">{{ $batch->rejected_at->format('M d, Y g:i A') }}</p>
            </div>
        @endif
    </div>

    {{-- Employee table --}}
    <div class="fade-up bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-50 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <span class="text-sm font-semibold text-gray-900">Employees</span>
            </div>
            <span class="text-[0.55rem] text-gray-400 font-semibold">{{ $payrolls->count() }} record(s)</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-50 bg-gray-50/50">
                        <th class="text-left px-5 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Employee</th>
                        <th class="text-left px-4 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Department</th>
                        <th class="text-center px-3 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Days</th>
                        <th class="text-right px-3 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Basic</th>
                        <th class="text-right px-3 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Gross</th>
                        <th class="text-right px-3 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Deductions</th>
                        <th class="text-right px-5 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Net</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($payrolls as $payroll)
                        @php
                            $user = $payroll->user;
                            $initials = strtoupper(substr($user->first_name ?? '', 0, 1) . substr($user->last_name ?? '', 0, 1));
                        @endphp
                        <tr class="transition-colors hover:bg-gray-50/50 cursor-pointer" onclick="window.location='{{ route('payroll-approval.show', $payroll) }}'">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center text-[0.55rem] font-bold shrink-0">{{ $initials }}</div>
                                    <div>
                                        <p class="text-xs font-bold text-gray-900">{{ $user->first_name }} {{ $user->last_name }}</p>
                                        <p class="text-[0.55rem] text-gray-400 mt-0.5">{{ $user->position ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-xs text-gray-500">{{ $user->department ?? 'N/A' }}</td>
                            <td class="px-3 py-3.5 text-center text-xs font-semibold text-gray-700 tabular-nums">{{ $payroll->days_worked ?? '—' }}</td>
                            <td class="px-3 py-3.5 text-right text-xs tabular-nums text-gray-600">₱{{ number_format($payroll->basic_salary, 0) }}</td>
                            <td class="px-3 py-3.5 text-right text-xs tabular-nums text-gray-600">₱{{ number_format($payroll->gross_pay, 0) }}</td>
                            <td class="px-3 py-3.5 text-right text-xs tabular-nums text-gray-400">₱{{ number_format($payroll->total_deductions, 0) }}</td>
                            <td class="px-5 py-3.5 text-right text-xs font-bold tabular-nums text-emerald-600">₱{{ number_format($payroll->net_pay, 0) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12">
                                <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                <p class="text-xs text-gray-400">No payroll records in this batch.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($payrolls->isNotEmpty())
                    <tfoot>
                        <tr class="border-t-2 border-gray-100 bg-gray-50/80">
                            <td colspan="4" class="px-5 py-3 text-xs font-bold text-gray-900">Totals</td>
                            <td class="px-3 py-3 text-right text-xs font-bold tabular-nums text-gray-900">₱{{ number_format($totalGross, 0) }}</td>
                            <td class="px-3 py-3 text-right text-xs font-bold tabular-nums text-amber-600">₱{{ number_format($totalDeductions, 0) }}</td>
                            <td class="px-5 py-3 text-right text-xs font-bold tabular-nums text-emerald-600">₱{{ number_format($totalNet, 0) }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        {{-- Approve/Reject footer --}}
        @if($status === 'submitted' && $payrolls->isNotEmpty())
            <div class="px-5 py-4 border-t border-gray-50 bg-white flex flex-wrap items-center justify-between gap-3">
                <p class="text-[0.6rem] text-gray-400 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Approving will lock all {{ $payrolls->count() }} payroll record(s) and notify HR.
                </p>
                <div class="flex gap-2">
                    <button type="button" onclick="openRejectModal({{ $batch->id }}, {{ $payrolls->count() }})"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold border border-red-200 text-red-600 bg-white hover:bg-red-50 hover:border-red-300 transition-all no-underline">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 9l-6 6M9 9l6 6"/></svg>
                        Reject batch
                    </button>
                    <button type="button" onclick="openApproveModal({{ $batch->id }}, {{ $payrolls->count() }})"
                            class="inline-flex items-center gap-1.5 px-5 py-2 rounded-lg text-xs font-bold text-white bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-700 hover:to-emerald-600 shadow-sm hover:shadow transition-all border-0 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Approve batch
                    </button>
                </div>
            </div>
        @endif
    </div>

    {{-- Reject Modal (HR-style) --}}
    <div class="fixed inset-0 bg-black/50 hidden items-center justify-center z-[9999] p-5" id="rejectModalOverlay">
        <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full p-7">
            <div class="mb-5">
                <h3 class="text-lg font-bold text-gray-900 m-0 mb-1">Reject Payroll Batch</h3>
                <p id="rejectDesc" class="text-xs text-gray-400 m-0">All employees in this batch will be notified.</p>
            </div>
            <form id="rejectForm" method="POST" action="{{ route('payroll-approval.reject-batch') }}">
                @csrf
                <input type="hidden" name="batch_id" id="rejectBatchId" value="">
                <div class="mb-6">
                    <label for="rejection_reason" class="block text-xs font-bold uppercase tracking-wide text-gray-500 mb-1.5">
                        Rejection Reason <span class="text-amber-600">*</span>
                    </label>
                    <textarea name="rejection_reason" id="rejectNote" rows="3" required
                              placeholder="Explain why this payroll batch is being rejected…"
                              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-xs text-gray-700 placeholder:text-gray-300 resize-none focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-300 transition-all"></textarea>
                    <p class="text-[0.55rem] text-gray-400 mt-1.5">The HR team will be notified and can resubmit.</p>
                </div>
                <div class="flex gap-2.5 justify-end">
                    <button type="button" onclick="closeRejectModal()"
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
    <div class="fixed inset-0 bg-black/50 hidden items-center justify-center z-[9999] p-5" id="approveModalOverlay">
        <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full p-7">
            <div class="mb-5">
                <h3 class="text-lg font-bold text-gray-900 m-0 mb-1">Approve Payroll Batch</h3>
                <p id="approveDesc" class="text-xs text-gray-400 m-0">Confirm approval of this payroll batch</p>
            </div>
            <form id="approveForm" method="POST" action="{{ route('payroll-approval.approve-batch') }}">
                @csrf
                <input type="hidden" name="batch_id" id="approveBatchId" value="">
                <div class="flex gap-2.5 justify-end">
                    <button type="button" onclick="closeApproveModal()"
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
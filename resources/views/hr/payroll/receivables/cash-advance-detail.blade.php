@extends('layouts.layout')

@section('title', 'Cash Advance Details')

@push('styles')
<style>
@keyframes fadeSlideUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { 0%{opacity:0;transform:scale(0.92)} 100%{opacity:1;transform:scale(1)} }
@keyframes slideInRight { 0%{opacity:0;transform:translateX(16px)} 100%{opacity:1;transform:translateX(0)} }
.stat-card { animation:scaleIn 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.stat-card:nth-child(1) { animation-delay:0.05s; }
.stat-card:nth-child(2) { animation-delay:0.1s; }
.stat-card:nth-child(3) { animation-delay:0.15s; }
.stat-card:nth-child(4) { animation-delay:0.2s; }
.fade-up { animation:fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.slide-right { animation:slideInRight 0.5s cubic-bezier(0.16,1,0.3,1) both; }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto space-y-5">

    {{-- Flash messages --}}
    @if ($message = Session::get('success'))
    <div class="fade-up flex items-center gap-2.5 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-sm font-semibold text-emerald-700">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ $message }}
    </div>
    @endif
    @if ($message = Session::get('error'))
    <div class="fade-up flex items-center gap-2.5 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-sm font-semibold text-red-600">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ $message }}
    </div>
    @endif

    {{-- Back button --}}
    <a href="{{ url()->previous() }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl text-xs font-semibold no-underline hover:border-gray-600 hover:text-gray-600 hover:bg-gray-50 transition-colors whitespace-nowrap cursor-pointer">
        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Receivables
    </a>

    {{-- Header --}}
    @php
        $badge = match($cashAdvance->status) {
            'pending'  => ['dot'=>'bg-amber-400','text'=>'text-amber-600','bg'=>'bg-amber-50'],
            'approved' => ['dot'=>'bg-emerald-400','text'=>'text-emerald-600','bg'=>'bg-emerald-50'],
            'released' => ['dot'=>'bg-sky-400','text'=>'text-sky-600','bg'=>'bg-sky-50'],
            'rejected' => ['dot'=>'bg-red-400','text'=>'text-red-600','bg'=>'bg-red-50'],
            default    => ['dot'=>'bg-gray-400','text'=>'text-gray-600','bg'=>'bg-gray-50'],
        };
    @endphp

    <div class="fade-up flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-gray-900 tracking-tight">Cash Advance Request</h1>
            <p class="text-xs text-gray-400 mt-0.5">Review and manage cash advance requests</p>
        </div>
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold uppercase tracking-wide {{ $badge['bg'] }} {{ $badge['text'] }}">
            <span class="w-2 h-2 rounded-full {{ $badge['dot'] }}"></span>
            {{ ucfirst($cashAdvance->status) }}
        </span>
    </div>

    {{-- Stat cards --}}
    @php
        $amountDeducted = 0;
        $totalAmount = $cashAdvance->amount ?? 0;
        
        if (in_array($cashAdvance->status, ['released', 'deducted'])) {
            $payrolls = \Illuminate\Support\Facades\DB::table('payrolls')
                ->where('user_id', $cashAdvance->user_id)
                ->whereNotNull('loan_deduction_data')
                ->where('cash_advance_deduction', '>', 0)
                ->get(['loan_deduction_data']);
            
            foreach ($payrolls as $payroll) {
                $deductionData = json_decode($payroll->loan_deduction_data, true);
                if (is_array($deductionData) && isset($deductionData['cash_advances'])) {
                    foreach ($deductionData['cash_advances'] as $ca) {
                        if ($ca['id'] == $cashAdvance->id) {
                            $amountDeducted += $ca['amount'];
                        }
                    }
                }
            }
        }
        
        $remainingBalance = $totalAmount - $amountDeducted;
        $deductionProgress = $totalAmount > 0 ? ($amountDeducted / $totalAmount) * 100 : 0;
    @endphp
    @php
        $monthlyDeduction = $cashAdvance->monthly_deduction ?? $cashAdvance->amount;
        $semiMonthlyDeduction = $monthlyDeduction / 2;
    @endphp
    <div class="grid grid-cols-3 gap-3">
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400">Total Amount</p>
            <p class="text-lg font-extrabold text-gray-900 tabular-nums mt-1">₱{{ number_format($totalAmount, 2) }}</p>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400">Per Payroll Deduction</p>
            <p class="text-lg font-extrabold text-gray-900 tabular-nums mt-1">₱{{ number_format($semiMonthlyDeduction, 2) }}</p>
            <p class="text-[0.55rem] text-gray-400 mt-1">(Monthly ÷ 2)</p>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400">Remaining Balance</p>
            <p class="text-lg font-extrabold {{ $remainingBalance > 0 ? 'text-amber-500' : 'text-emerald-500' }} tabular-nums mt-1">₱{{ number_format($remainingBalance, 2) }}</p>
        </div>
    </div>

    {{-- 2-column layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Main column (2/3) --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Employee Information --}}
            <div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-50 flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div><p class="text-sm font-bold text-gray-900">Employee Information</p></div>
                </div>
                <div class="px-5 py-4 flex items-center gap-4">
                    <span class="w-12 h-12 rounded-full bg-gray-100 border-2 border-gray-200 flex items-center justify-center text-base font-extrabold text-gray-600 shrink-0 uppercase">{{ strtoupper(substr($cashAdvance->user->name ?? 'U', 0, 2)) }}</span>
                    <div>
                        <p class="text-sm font-bold text-gray-900">{{ $cashAdvance->user->name ?? '—' }}</p>
                        <p class="text-xs text-gray-400">{{ $cashAdvance->user->position ?? 'Employee' }}</p>
                    </div>
                </div>
            </div>

            {{-- Request Details --}}
            <div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-50 flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div><p class="text-sm font-bold text-gray-900">Request Details</p></div>
                </div>
                <div class="px-5 py-4 divide-y divide-gray-50">
                    <div class="flex items-center justify-between py-2.5">
                        <span class="text-sm text-gray-600">Amount</span>
                        <span class="text-sm font-bold text-gray-900 font-mono tabular-nums">₱{{ number_format($cashAdvance->amount, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2.5">
                        <span class="text-sm text-gray-600">Request Date</span>
                        <span class="text-sm text-gray-900">{{ $cashAdvance->request_date ? \Carbon\Carbon::parse($cashAdvance->request_date)->format('F d, Y') : '—' }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2.5">
                        <span class="text-sm text-gray-600">Repayment Term</span>
                        <span class="text-sm text-gray-900">{{ $cashAdvance->repayment_months ?? 1 }} month{{ ($cashAdvance->repayment_months ?? 1) > 1 ? 's' : '' }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2.5">
                        <span class="text-sm text-gray-600">Monthly Deduction</span>
                        <span class="text-sm font-semibold text-gray-900 font-mono tabular-nums">₱{{ number_format($cashAdvance->monthly_deduction ?? $cashAdvance->amount, 2) }}</span>
                    </div>
                    <div class="py-2.5">
                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-600">Repayment Progress</span>
                        <div class="mt-2">
                            <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-emerald-400 to-emerald-600 rounded-full transition-all duration-500" style="width: {{ $deductionProgress }}%"></div>
                            </div>
                            <p class="text-xs text-gray-400 mt-1 font-mono">₱{{ number_format($amountDeducted, 2) }} of ₱{{ number_format($totalAmount, 2) }} deducted</p>
                        </div>
                    </div>
                    @php
                        $latestPayrollWithDeduction = null;
                        if (in_array($cashAdvance->status, ['released', 'deducted']) && $amountDeducted > 0) {
                            $latestPayrollWithDeduction = \Illuminate\Support\Facades\DB::table('payrolls')
                                ->where('user_id', $cashAdvance->user_id)
                                ->whereNotNull('loan_deduction_data')
                                ->where('cash_advance_deduction', '>', 0)
                                ->orderBy('payroll_period_end', 'desc')
                                ->first(['payroll_period_start', 'payroll_period_end', 'payment_date', 'cash_advance_deduction', 'loan_deduction_data']);
                        }
                    @endphp
                    @if ($latestPayrollWithDeduction)
                    <div class="py-2.5">
                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-600">Latest Payroll Deduction</span>
                        <div class="mt-2 px-3.5 py-2.5 rounded-lg bg-blue-50 border border-blue-100 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-600">Payroll Period:</span>
                                <span class="text-xs font-semibold text-gray-900">{{ \Carbon\Carbon::parse($latestPayrollWithDeduction->payroll_period_start)->format('M d') }} - {{ \Carbon\Carbon::parse($latestPayrollWithDeduction->payroll_period_end)->format('M d, Y') }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-600">Deduction Amount:</span>
                                @php
                                    $caDeductionAmount = 0;
                                    $deductionData = json_decode($latestPayrollWithDeduction->loan_deduction_data, true);
                                    if (is_array($deductionData) && isset($deductionData['cash_advances'])) {
                                        foreach ($deductionData['cash_advances'] as $ca) {
                                            if ($ca['id'] == $cashAdvance->id) {
                                                $caDeductionAmount = $ca['amount'];
                                                break;
                                            }
                                        }
                                    }
                                @endphp
                                <span class="text-xs font-semibold text-gray-900 font-mono tabular-nums">₱{{ number_format($caDeductionAmount > 0 ? $caDeductionAmount : $latestPayrollWithDeduction->cash_advance_deduction, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if ($cashAdvance->rejection_reason)
                    <div class="py-2.5">
                        <span class="text-xs font-semibold uppercase tracking-wide text-red-500">Rejection Reason</span>
                        <div class="mt-1.5 px-3.5 py-2.5 rounded-lg bg-red-50 border-l-2 border-red-400 text-xs text-red-700">{{ $cashAdvance->rejection_reason }}</div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Action Buttons --}}
            @if (in_array(auth()->user()->role, ['hr', 'superadmin']) && $cashAdvance->status === 'pending')
            <div class="fade-up flex items-center gap-3 pt-1">
                <form action="{{ route('payroll.receivables.cash-advances.approve', $cashAdvance) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 px-5 py-2.5 bg-emerald-600 text-white rounded-xl text-sm font-semibold transition-all hover:bg-emerald-500 active:scale-[0.97] cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Approve Request
                    </button>
                </form>
                <button type="button" onclick="openRejectModal()" class="flex-1 inline-flex items-center justify-center gap-1.5 px-5 py-2.5 bg-white text-red-500 border border-red-200 rounded-xl text-sm font-semibold transition-all hover:bg-red-50 active:scale-[0.97] cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    Reject Request
                </button>
            </div>
            @elseif (in_array(auth()->user()->role, ['accountant', 'superadmin']) && $cashAdvance->status === 'approved')
            <div class="fade-up flex items-center gap-3 pt-1">
                <form action="{{ route('cash-advances.release', $cashAdvance) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 px-5 py-2.5 bg-sky-600 text-white rounded-xl text-sm font-semibold transition-all hover:bg-sky-500 active:scale-[0.97] cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Release Payment
                    </button>
                </form>
                <button type="button" onclick="openRejectModal()" class="flex-1 inline-flex items-center justify-center gap-1.5 px-5 py-2.5 bg-white text-red-500 border border-red-200 rounded-xl text-sm font-semibold transition-all hover:bg-red-50 active:scale-[0.97] cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    Reject Request
                </button>
            </div>
            @endif

        </div>

        {{-- Sidebar (1/3): Approval Timeline --}}
        <div class="space-y-5">
            <div class="slide-right bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-50">
                    <p class="text-sm font-bold text-gray-900">Approval Timeline</p>
                </div>
                <div class="px-5 py-4 space-y-4">
                    <div>
                        <p class="text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400">Submitted By</p>
                        <p class="text-sm text-gray-900 mt-0.5">{{ $cashAdvance->user->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400">Submitted On</p>
                        <p class="text-sm text-gray-900 mt-0.5 font-mono">{{ $cashAdvance->created_at ? $cashAdvance->created_at->format('M d, Y g:i A') : '—' }}</p>
                    </div>
                    @if ($cashAdvance->approved_at)
                    <div class="pt-3 border-t border-gray-50">
                        <div class="flex items-center gap-2.5 mb-3">
                            <span class="w-6 h-6 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <div>
                                <p class="text-xs font-semibold text-emerald-600">Approved</p>
                                <p class="text-[0.55rem] text-gray-400 font-mono">{{ $cashAdvance->approved_at->format('M d, Y g:i A') }}</p>
                            </div>
                        </div>
                        <div class="ml-8">
                            <p class="text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400">Approved By</p>
                            <p class="text-xs text-gray-900">{{ optional($cashAdvance->approver)->name ?? '—' }}</p>
                        </div>
                    </div>
                    @endif
                    @if ($cashAdvance->status === 'rejected')
                    <div class="pt-3 border-t border-gray-50">
                        <div class="flex items-center gap-2.5 mb-2">
                            <span class="w-6 h-6 rounded-full bg-red-50 text-red-500 flex items-center justify-center shrink-0">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </span>
                            <div>
                                <p class="text-xs font-semibold text-red-500">Rejected</p>
                            </div>
                        </div>
                        <div class="ml-8 px-3 py-2 rounded-lg bg-red-50 border-l-2 border-red-400">
                            <p class="text-xs text-red-700">{{ $cashAdvance->rejection_reason ?? 'No reason provided' }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div id="rejectModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40" onclick="if(event.target===this)closeRejectModal()">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 p-6 animate-[fadeSlideUp_0.25s_ease]">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-8 h-8 rounded-xl bg-red-50 text-red-500 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            </div>
            <div>
                <h3 class="text-base font-bold text-gray-900">Reject Request</h3>
                <p class="text-xs text-gray-400">This action cannot be undone.</p>
            </div>
        </div>

        <form id="rejectForm" method="POST">
            @csrf
            <textarea name="rejection_reason" rows="3" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-700 placeholder:text-gray-300 focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-300 resize-none" placeholder="Enter rejection reason..." required></textarea>
            <div class="flex items-center gap-3 mt-4">
                <button type="button" onclick="closeRejectModal()" class="flex-1 px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 transition-all hover:bg-gray-50 active:scale-[0.97] cursor-pointer">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-red-500 text-white rounded-xl text-sm font-semibold transition-all hover:bg-red-600 active:scale-[0.97] cursor-pointer">
                    Confirm Rejection
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openRejectModal() {
    @if (in_array(auth()->user()->role, ['hr', 'superadmin']))
        document.getElementById('rejectForm').action = '{{ route("payroll.receivables.cash-advances.reject", $cashAdvance) }}';
    @else
        document.getElementById('rejectForm').action = '{{ route("cash-advances.reject", $cashAdvance) }}';
    @endif
    document.getElementById('rejectModal').classList.remove('hidden');
    document.getElementById('rejectModal').classList.add('flex');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejectModal').classList.remove('flex');
    document.querySelector('#rejectForm textarea').value = '';
}
</script>
@endsection

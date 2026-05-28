@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeSlideUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { 0%{opacity:0;transform:scale(0.92)} 100%{opacity:1;transform:scale(1)} }
@keyframes slideInRight { 0%{opacity:0;transform:translateX(-10px)} 100%{opacity:1;transform:translateX(0)} }
.stat-card { animation:scaleIn 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.stat-card:nth-child(1) { animation-delay:0.05s; }
.stat-card:nth-child(2) { animation-delay:0.1s; }
.stat-card:nth-child(3) { animation-delay:0.15s; }
.stat-card:nth-child(4) { animation-delay:0.2s; }
.fade-up { animation:fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.filter-bar { animation:slideInRight 0.4s cubic-bezier(0.16,1,0.3,1) 0.1s both; }
.table-wrap { animation:fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) 0.15s both; }
</style>
@endpush

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="fade-up flex items-start justify-between gap-4 flex-wrap">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Payroll Receivables</h1>
            <p class="text-sm text-gray-400 mt-0.5">Manage cash advances and salary loan applications</p>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="fade-up flex items-center gap-2.5 px-4 py-3 rounded-xl text-sm font-medium bg-emerald-50 border border-emerald-200 text-emerald-700">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        <span class="flex-1">{{ session('success') }}</span>
        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 cursor-pointer bg-transparent border-none p-0 leading-none">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    @endif
    @if($errors->any())
    <div class="fade-up flex items-center gap-2.5 px-4 py-3 rounded-xl text-sm font-medium bg-red-50 border border-red-200 text-red-700">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
        <span class="flex-1">{{ $errors->first() }}</span>
        <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 cursor-pointer bg-transparent border-none p-0 leading-none">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    @endif

    {{-- Color-Coded Tabs --}}
    <div class="border-b border-gray-200" style="animation:fadeSlideUp 0.4s cubic-bezier(0.16,1,0.3,1) 0.05s both;">
        <nav class="flex gap-1 -mb-px" role="tablist">
            <button role="tab" data-tab="cash_advances"
               class="tab-btn relative px-5 py-3 text-sm font-medium border-b-2 transition-all duration-200 group
               {{ $tab === 'cash_advances'
                   ? 'border-blue-500 text-blue-700 bg-blue-50/60'
                   : 'border-transparent text-gray-400 hover:text-gray-600 hover:border-gray-300 hover:bg-gray-50/50' }}">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 {{ $tab === 'cash_advances' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path stroke-linecap="round" d="M2 10h20"/></svg>
                    Cash Advances
                </span>
            </button>
            <button role="tab" data-tab="salary_loans"
               class="tab-btn relative px-5 py-3 text-sm font-medium border-b-2 transition-all duration-200 group
               {{ $tab === 'salary_loans'
                   ? 'border-emerald-500 text-emerald-700 bg-emerald-50/60'
                   : 'border-transparent text-gray-400 hover:text-gray-600 hover:border-gray-300 hover:bg-gray-50/50' }}">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 {{ $tab === 'salary_loans' ? 'text-emerald-500' : 'text-gray-400 group-hover:text-gray-500' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Salary Loans
                </span>
            </button>
        </nav>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-400 font-medium">Pending</p>
                    <p class="text-lg font-bold text-gray-900 tabular-nums mt-0.5">{{ $caPendingCount + $loanPendingCount }}</p>
                    <p class="text-[0.55rem] text-gray-400 font-mono mt-0.5">awaiting approval</p>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-400 font-medium">Approved</p>
                    <p class="text-lg font-bold text-gray-900 tabular-nums mt-0.5">{{ $caApprovedCount + $loanApprovedCount }}</p>
                    <p class="text-[0.55rem] text-gray-400 font-mono mt-0.5">ready for release</p>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-400 font-medium">Released Total</p>
                    <p class="text-lg font-bold text-gray-900 tabular-nums mt-0.5">₱{{ number_format(($caReleasedTotal ?? 0) + ($loanReleasedTotal ?? 0), 2) }}</p>
                    <p class="text-[0.55rem] text-gray-400 font-mono mt-0.5">{{ $totalCashAdvances + $totalSalaryLoans }} total requests</p>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-red-50 text-red-500 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-400 font-medium">Outstanding</p>
                    <p class="text-lg font-bold text-gray-900 tabular-nums mt-0.5">{{ $outstandingAmount }}</p>
                    <p class="text-[0.55rem] text-gray-400 font-mono mt-0.5">approved + released</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         CASH ADVANCES TAB
    ══════════════════════════════════════════ --}}
    <div id="tab-cash_advances" class="tab-panel {{ $tab !== 'cash_advances' ? 'hidden' : '' }} active-panel">

    {{-- Filter bar --}}
    <div class="filter-bar flex items-center gap-3 flex-wrap">
        <div class="flex-1 min-w-[200px]">
            <input type="text" id="caSearch" placeholder="Search employee…" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-700 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
        </div>
        <select id="caStatusFilter" class="border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-700 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100 cursor-pointer">
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="released">Released</option>
            <option value="rejected">Rejected</option>
        </select>
    </div>

    {{-- Table --}}
    <div class="table-wrap bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-50 bg-gray-50/50">
                        <th class="text-left text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Employee</th>
                        <th class="text-left text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Amount</th>
                        <th class="text-left text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Term</th>
                        <th class="text-left text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Monthly</th>
                        <th class="text-left text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Requested</th>
                        <th class="text-center text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Status</th>
                        <th class="text-left text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Deducted On</th>
                        @if(auth()->user()->role === 'accountant')
                        <th class="text-end text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50" id="caTbody"></tbody>
            </table>
        </div>
        <div id="caNoResults" class="hidden">
            <div class="flex flex-col items-center py-12 text-center">
                <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-300 mb-3">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><rect x="2" y="5" width="20" height="14" rx="2"/><path stroke-linecap="round" d="M2 10h20"/></svg>
                </div>
                <p class="text-sm font-semibold text-gray-500" id="caEmptyTitle">No cash advance requests</p>
                <p class="text-xs text-gray-400 mt-0.5" id="caEmptySub">There are no records to display.</p>
            </div>
        </div>
        <div class="flex items-center justify-between px-4 py-3 border-t border-gray-100 bg-gray-50/50 flex-wrap gap-3">
            <div class="text-xs text-gray-400" id="caPaginationInfo">Showing <strong class="text-gray-700">0</strong> records</div>
            <nav id="caPaginationNav" class="flex items-center gap-1"></nav>
        </div>
    </div>
    </div>

    {{-- ══════════════════════════════════════════
         SALARY LOANS TAB
    ══════════════════════════════════════════ --}}
    <div id="tab-salary_loans" class="tab-panel {{ $tab !== 'salary_loans' ? 'hidden' : '' }} active-panel">

    {{-- Filter bar --}}
    <div class="filter-bar flex items-center gap-3 flex-wrap">
        <div class="flex-1 min-w-[200px]">
            <input type="text" id="loanSearch" placeholder="Search employee…" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-700 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
        </div>
        <select id="loanStatusFilter" class="border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-700 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100 cursor-pointer">
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="released">Released</option>
            <option value="settled">Settled</option>
            <option value="rejected">Rejected</option>
        </select>
    </div>

    {{-- Table --}}
    <div class="table-wrap bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-50 bg-gray-50/50">
                        <th class="text-left text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Employee</th>
                        <th class="text-end text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Loan Amount</th>
                        <th class="text-end text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Monthly</th>
                        <th class="text-end text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Remaining</th>
                        <th class="text-left text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Progress</th>
                        <th class="text-center text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Status</th>
                        <th class="text-left text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Start Date</th>
                        @if(auth()->user()->role === 'accountant')
                        <th class="text-end text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50" id="loanTbody"></tbody>
            </table>
        </div>
        <div id="loanNoResults" class="hidden">
            <div class="flex flex-col items-center py-12 text-center">
                <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-300 mb-3">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <p class="text-sm font-semibold text-gray-500" id="loanEmptyTitle">No salary loan applications</p>
                <p class="text-xs text-gray-400 mt-0.5" id="loanEmptySub">There are no records to display.</p>
            </div>
        </div>
        <div class="flex items-center justify-between px-4 py-3 border-t border-gray-100 bg-gray-50/50 flex-wrap gap-3">
            <div class="text-xs text-gray-400" id="loanPaginationInfo">Showing <strong class="text-gray-700">0</strong> records</div>
            <nav id="loanPaginationNav" class="flex items-center gap-1"></nav>
        </div>
    </div>
    </div>

</div>



{{-- ── Mark as Paid Modal (Tailwind overlay) ── --}}
@if(in_array(auth()->user()->role, ['hr', 'superadmin']))
<div id="payModalOverlay" class="fixed inset-0 z-[9999] bg-black/40 flex items-center justify-center p-4" style="display:none;" onclick="if(event.target===this)closePayModal()">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden" onclick="event.stopPropagation()" style="animation:scaleIn 0.2s cubic-bezier(0.16,1,0.3,1)">
        <form id="payForm" method="POST">
            @csrf
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-50">
                <h6 class="text-sm font-bold text-gray-900">Mark as Paid</h6>
                <button type="button" onclick="closePayModal()" class="w-6 h-6 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors cursor-pointer bg-transparent border-none">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-5">
                <p class="text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 mb-1.5">Payment Date</p>
                <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-700 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
            </div>
            <div class="flex justify-end gap-2 px-5 py-3 border-t border-gray-50">
                <button type="button" onclick="closePayModal()" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors active:scale-[0.97] cursor-pointer border-none">Cancel</button>
                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-900 text-white shadow-md hover:bg-black transition-colors active:scale-[0.97] cursor-pointer border-none">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Confirm
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@push('scripts')
<script>
window.allCashAdvances = {!! json_encode($allCashAdvances?->map(fn($a) => [
    'id' => $a->id,
    'name' => $a->user->name ?? '—',
    'name_lower' => strtolower($a->user->name ?? ''),
    'position' => $a->user->position ?? '',
    'initial_1' => strtoupper(substr($a->user->first_name ?? ($a->user->name ?? 'U'), 0, 1)),
    'initial_2' => strtoupper(substr($a->user->last_name ?? '', 0, 1)),
    'amount' => (float) $a->amount,
    'repayment_months' => $a->repayment_months ?? 1,
    'monthly_deduction' => (float) ($a->monthly_deduction ?? $a->amount),
    'request_date' => $a->request_date ? \Carbon\Carbon::parse($a->request_date)->format('M d, Y') : '—',
    'status' => $a->status ?? 'pending',
    'deducted_payroll' => $a->deductedPayroll ? \Carbon\Carbon::parse($a->deductedPayroll->payroll_period_end)->format('M d, Y') : null,
    'rejection_reason' => $a->rejection_reason,
    'url' => route('payroll.receivables.cash-advances.show', $a),
    'role' => auth()->user()->role,
]) ?? []) !!};

window.allSalaryLoans = {!! json_encode($allSalaryLoans?->map(fn($l) => [
    'id' => $l->id,
    'name' => $l->user->name ?? '—',
    'name_lower' => strtolower($l->user->name ?? ''),
    'position' => $l->user->position ?? '',
    'initial_1' => strtoupper(substr($l->user->first_name ?? ($l->user->name ?? 'U'), 0, 1)),
    'initial_2' => strtoupper(substr($l->user->last_name ?? '', 0, 1)),
    'loan_amount' => (float) $l->loan_amount,
    'monthly_deduction' => (float) $l->monthly_deduction,
    'remaining_balance' => (float) $l->remaining_balance,
    'total_months' => $l->total_months ?? 12,
    'months_paid' => $l->months_paid ?? 0,
    'status' => $l->status ?? 'pending',
    'start_date' => $l->start_date ? \Carbon\Carbon::parse($l->start_date)->format('M d, Y') : '—',
    'rejection_reason' => $l->rejection_reason,
    'url' => route('payroll.receivables.salary-loans.show', $l),
    'role' => auth()->user()->role,
]) ?? []) !!};

(function () {
    // Tab switching — client-side
    var activeTab = '{{ $tab }}';
    var tabBtns = document.querySelectorAll('.tab-btn');
    var panels  = document.querySelectorAll('.tab-panel');
    var tabStyles = {
        cash_advances: { border: 'border-blue-500', text: 'text-blue-700', bg: 'bg-blue-50/60', icon: 'text-blue-500' },
        salary_loans:  { border: 'border-emerald-500', text: 'text-emerald-700', bg: 'bg-emerald-50/60', icon: 'text-emerald-500' },
    };
    var inactiveBtn = ['border-transparent', 'text-gray-400', 'hover:text-gray-600', 'hover:border-gray-300', 'hover:bg-gray-50/50'];
    var inactiveSvg = ['text-gray-400', 'group-hover:text-gray-500'];

    tabBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            var tab = this.dataset.tab;
            if (tab === activeTab) return;

            var prev = document.getElementById('tab-' + activeTab);
            if (prev) { prev.classList.add('hidden'); prev.classList.remove('active-panel'); }

            var next = document.getElementById('tab-' + tab);
            if (next) { next.classList.remove('hidden'); next.classList.add('active-panel'); }

            tabBtns.forEach(function(b) {
                b.classList.remove('border-blue-500','text-blue-700','bg-blue-50/60',
                    'border-emerald-500','text-emerald-700','bg-emerald-50/60');
                b.classList.add.apply(b.classList, inactiveBtn);
                var svg = b.querySelector('svg');
                if (svg) {
                    svg.classList.remove('text-blue-500','text-emerald-500');
                    svg.classList.add.apply(svg.classList, inactiveSvg);
                }
            });

            var s = tabStyles[tab];
            this.classList.remove.apply(this.classList, inactiveBtn);
            this.classList.add(s.border, s.text, s.bg);

            var svg = this.querySelector('svg');
            if (svg) {
                svg.classList.remove.apply(svg.classList, inactiveSvg);
                svg.classList.add(s.icon);
            }

            var url = new URL(window.location);
            url.searchParams.set('tab', tab);
            url.searchParams.delete('page');
            history.replaceState(null, '', url.toString());

            activeTab = tab;
        });
    });

    const isAcc = {{ auth()->user()->role === 'accountant' ? 'true' : 'false' }};
    const caColspan = isAcc ? 8 : 7;

    // ── Row click handler ──
    document.addEventListener('click', function(e) {
        var row = e.target.closest('.pr-row');
        if (row && row.dataset.url) window.location.href = row.dataset.url;
    });

    // ── Mark as Paid Modal ──
    @if(in_array(auth()->user()->role, ['hr', 'superadmin']))
    window.payBase = '{{ url('/payroll/receivables') }}';
    window.openPayModal = function(payrollId) {
        document.getElementById('payForm').action = payBase + '/' + payrollId + '/mark-paid';
        document.getElementById('payModalOverlay').style.display = 'flex';
    };
    window.closePayModal = function() { document.getElementById('payModalOverlay').style.display = 'none'; };
    @endif

    const PER = 15;

    // ── Cash Advances ──
    const caSearch = document.getElementById('caSearch');
    const caFilter = document.getElementById('caStatusFilter');
    const caTbody  = document.getElementById('caTbody');
    const caNR     = document.getElementById('caNoResults');
    const caET     = document.getElementById('caEmptyTitle');
    const caES     = document.getElementById('caEmptySub');
    let caPage = 1, caFiltered = [];

    function caApplyFilters() {
        const q = (caSearch ? caSearch.value : '').toLowerCase().trim();
        const st = caFilter ? caFilter.value : '';
        caFiltered = (window.allCashAdvances || []).filter(r => {
            if (q && !r.name_lower.includes(q)) return false;
            if (st && r.status !== st) return false;
            return true;
        });
        caPage = 1;
        caRender();
    }

    function caRender() {
        const start = (caPage - 1) * PER;
        const end = Math.min(start + PER, caFiltered.length);
        const pageData = caFiltered.slice(start, end);
        caTbody.innerHTML = '';

        if (pageData.length === 0) {
            caNR.classList.remove('hidden');
            caET.textContent = caFiltered.length === 0 ? 'No cash advance requests' : 'No results found';
            caES.textContent = caFiltered.length === 0 ? 'There are no records to display.' : 'Try a different search or filter.';
        } else {
            caNR.classList.add('hidden');
            pageData.forEach(r => {
                const initials = (r.initial_1 || 'U') + (r.initial_2 || '');
                const badgeCls = { pending: 'bg-amber-50 text-amber-700 border-amber-200', approved: 'bg-blue-50 text-blue-700 border-blue-200', released: 'bg-emerald-50 text-emerald-700 border-emerald-200', rejected: 'bg-red-50 text-red-700 border-red-200' }[r.status] || 'bg-gray-50 text-gray-600 border-gray-200';
                const dotCls = { pending: 'bg-amber-500', approved: 'bg-blue-500', released: 'bg-emerald-500', rejected: 'bg-red-500' }[r.status] || 'bg-gray-400';
                const tr = document.createElement('tr');
                tr.className = 'pr-row hover:bg-gray-50/40 transition-colors cursor-pointer';
                tr.dataset.url = r.url;
                tr.innerHTML = `
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-lg bg-gray-50 text-gray-500 flex items-center justify-center text-[9px] font-bold shrink-0 border border-gray-200">${initials}</span>
                            <div>
                                <div class="text-xs font-semibold text-gray-900">${r.name}</div>
                                <div class="text-[0.55rem] text-gray-400">${r.position}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3"><span class="font-mono tabular-nums text-xs font-semibold text-gray-900">₱${r.amount.toLocaleString('en-US', {minimumFractionDigits:2,maximumFractionDigits:2})}</span></td>
                    <td class="px-4 py-3 text-xs text-gray-700">${r.repayment_months}mo</td>
                    <td class="px-4 py-3"><span class="font-mono tabular-nums text-xs text-gray-500">₱${r.monthly_deduction.toLocaleString('en-US', {minimumFractionDigits:2,maximumFractionDigits:2})}</span></td>
                    <td class="px-4 py-3 text-xs text-gray-700">${r.request_date}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[0.55rem] font-semibold border ${badgeCls}">
                            <span class="w-1 h-1 rounded-full ${dotCls}"></span>
                            ${r.status.charAt(0).toUpperCase() + r.status.slice(1)}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-700">${r.deducted_payroll ? 'Period ending ' + r.deducted_payroll : '<span class="text-gray-300">—</span>'}</td>
                    ${isAcc ? '<td class="px-4 py-3 text-end"></td>' : ''}
                `;
                caTbody.appendChild(tr);
                if (r.rejection_reason) {
                    const rr = document.createElement('tr');
                    rr.className = 'pr-reason-row';
                    rr.style.display = 'none';
                    rr.innerHTML = `<td colspan="${caColspan}" class="px-4 py-2.5 bg-amber-50/60 border-l-2 border-amber-400">
                        <div class="flex items-center gap-2 text-[0.55rem] text-amber-800">
                            <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4v2m0-6a4 4 0 110 8 4 4 0 010-8z"/></svg>
                            <strong>Rejection Reason:</strong> ${r.rejection_reason}
                        </div>
                    </td>`;
                    caTbody.appendChild(rr);
                }
            });
        }
        caUpdatePagination();
    }

    function caUpdatePagination() {
        const total = caFiltered.length;
        const pages = Math.ceil(total / PER);
        const info  = document.getElementById('caPaginationInfo');
        const nav   = document.getElementById('caPaginationNav');
        if (!info || !nav) return;
        if (total === 0) { info.innerHTML = 'No records to display'; nav.innerHTML = ''; return; }
        const s = (caPage - 1) * PER + 1, e = Math.min(caPage * PER, total);
        info.innerHTML = `Showing <strong class="text-gray-700">${s}</strong>–<strong class="text-gray-700">${e}</strong> of <strong class="text-gray-700">${total}</strong>`;
        if (pages <= 1) { nav.innerHTML = ''; return; }
        const base = 'flex items-center justify-center w-7 h-7 rounded-lg text-xs font-semibold border transition-all duration-150';
        const act  = base + ' bg-gray-900 text-white border-gray-900';
        const def  = base + ' bg-white text-gray-600 border-gray-200 hover:bg-gray-50 hover:text-gray-900 hover:border-gray-300';
        const dis  = base + ' bg-gray-50 text-gray-300 border-gray-100 cursor-not-allowed pointer-events-none';
        let html = `<button data-p="${caPage - 1}" class="${caPage === 1 ? dis : def}">‹</button>`;
        for (let i = 1; i <= pages; i++) html += `<button data-p="${i}" class="${i === caPage ? act : def}">${i}</button>`;
        html += `<button data-p="${caPage + 1}" class="${caPage === pages ? dis : def}">›</button>`;
        nav.innerHTML = html;
        nav.querySelectorAll('button[data-p]').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const p = parseInt(this.dataset.p);
                if (p < 1 || p > pages) return;
                caPage = p;
                caRender();
            });
        });
    }

    if (caSearch) { caSearch.addEventListener('input', caApplyFilters); caFilter.addEventListener('change', caApplyFilters); }

    // ── Salary Loans ──
    const loanSearch = document.getElementById('loanSearch');
    const loanFilter = document.getElementById('loanStatusFilter');
    const loanTbody  = document.getElementById('loanTbody');
    const loanNR     = document.getElementById('loanNoResults');
    const loanET     = document.getElementById('loanEmptyTitle');
    const loanES     = document.getElementById('loanEmptySub');
    let loanPage = 1, loanFiltered = [];

    function loanApplyFilters() {
        const q = (loanSearch ? loanSearch.value : '').toLowerCase().trim();
        const st = loanFilter ? loanFilter.value : '';
        loanFiltered = (window.allSalaryLoans || []).filter(r => {
            if (q && !r.name_lower.includes(q)) return false;
            if (st && r.status !== st) return false;
            return true;
        });
        loanPage = 1;
        loanRender();
    }

    function loanRender() {
        const start = (loanPage - 1) * PER;
        const end = Math.min(start + PER, loanFiltered.length);
        const pageData = loanFiltered.slice(start, end);
        loanTbody.innerHTML = '';

        if (pageData.length === 0) {
            loanNR.classList.remove('hidden');
            loanET.textContent = loanFiltered.length === 0 ? 'No salary loan applications' : 'No results found';
            loanES.textContent = loanFiltered.length === 0 ? 'There are no records to display.' : 'Try a different search or filter.';
        } else {
            loanNR.classList.add('hidden');
            pageData.forEach(r => {
                const progress = r.total_months > 0 ? Math.min(100, Math.round((r.months_paid / r.total_months) * 100)) : 0;
                const progColor = progress >= 100 ? 'bg-emerald-500' : (progress >= 50 ? 'bg-blue-500' : 'bg-amber-500');
                const initials = (r.initial_1 || 'U') + (r.initial_2 || '');
                const badgeCls = { pending: 'bg-amber-50 text-amber-700 border-amber-200', approved: 'bg-blue-50 text-blue-700 border-blue-200', released: 'bg-emerald-50 text-emerald-700 border-emerald-200', settled: 'bg-emerald-50 text-emerald-700 border-emerald-200', rejected: 'bg-red-50 text-red-700 border-red-200' }[r.status] || 'bg-gray-50 text-gray-600 border-gray-200';
                const dotCls = { pending: 'bg-amber-500', approved: 'bg-blue-500', released: 'bg-emerald-500', settled: 'bg-emerald-500', rejected: 'bg-red-500' }[r.status] || 'bg-gray-400';
                const tr = document.createElement('tr');
                tr.className = 'pr-row hover:bg-gray-50/40 transition-colors cursor-pointer';
                tr.dataset.url = r.url;
                tr.innerHTML = `
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-lg bg-gray-50 text-gray-500 flex items-center justify-center text-[9px] font-bold shrink-0 border border-gray-200">${initials}</span>
                            <div>
                                <div class="text-xs font-semibold text-gray-900">${r.name}</div>
                                <div class="text-[0.55rem] text-gray-400">${r.position}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-end"><span class="font-mono tabular-nums text-xs font-semibold text-gray-900">₱${r.loan_amount.toLocaleString('en-US', {minimumFractionDigits:2,maximumFractionDigits:2})}</span></td>
                    <td class="px-4 py-3 text-end"><span class="font-mono tabular-nums text-xs text-gray-500">₱${r.monthly_deduction.toLocaleString('en-US', {minimumFractionDigits:2,maximumFractionDigits:2})}</span></td>
                    <td class="px-4 py-3 text-end"><span class="font-mono tabular-nums text-xs font-semibold text-red-500">₱${r.remaining_balance.toLocaleString('en-US', {minimumFractionDigits:2,maximumFractionDigits:2})}</span></td>
                    <td class="px-4 py-3" style="min-width:130px;">
                        <div class="flex items-center gap-2">
                            <div class="flex-1 h-1.5 rounded-full bg-gray-100 overflow-hidden">
                                <div class="h-full rounded-full ${progColor} transition-all" style="width:${progress}%"></div>
                            </div>
                            <span class="text-[0.55rem] font-mono text-gray-400 tabular-nums">${r.months_paid}/${r.total_months}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[0.55rem] font-semibold border ${badgeCls}">
                            <span class="w-1 h-1 rounded-full ${dotCls}"></span>
                            ${r.status.charAt(0).toUpperCase() + r.status.slice(1)}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-700">${r.start_date}</td>
                    ${isAcc ? '<td class="px-4 py-3 text-end"></td>' : ''}
                `;
                loanTbody.appendChild(tr);
                if (r.rejection_reason) {
                    const rr = document.createElement('tr');
                    rr.className = 'pr-reason-row';
                    rr.style.display = 'none';
                    rr.innerHTML = `<td colspan="${caColspan}" class="px-4 py-2.5 bg-amber-50/60 border-l-2 border-amber-400">
                        <div class="flex items-center gap-2 text-[0.55rem] text-amber-800">
                            <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4v2m0-6a4 4 0 110 8 4 4 0 010-8z"/></svg>
                            <strong>Rejection Reason:</strong> ${r.rejection_reason}
                        </div>
                    </td>`;
                    loanTbody.appendChild(rr);
                }
            });
        }
        loanUpdatePagination();
    }

    function loanUpdatePagination() {
        const total = loanFiltered.length;
        const pages = Math.ceil(total / PER);
        const info  = document.getElementById('loanPaginationInfo');
        const nav   = document.getElementById('loanPaginationNav');
        if (!info || !nav) return;
        if (total === 0) { info.innerHTML = 'No records to display'; nav.innerHTML = ''; return; }
        const s = (loanPage - 1) * PER + 1, e = Math.min(loanPage * PER, total);
        info.innerHTML = `Showing <strong class="text-gray-700">${s}</strong>–<strong class="text-gray-700">${e}</strong> of <strong class="text-gray-700">${total}</strong>`;
        if (pages <= 1) { nav.innerHTML = ''; return; }
        const base = 'flex items-center justify-center w-7 h-7 rounded-lg text-xs font-semibold border transition-all duration-150';
        const act  = base + ' bg-gray-900 text-white border-gray-900';
        const def  = base + ' bg-white text-gray-600 border-gray-200 hover:bg-gray-50 hover:text-gray-900 hover:border-gray-300';
        const dis  = base + ' bg-gray-50 text-gray-300 border-gray-100 cursor-not-allowed pointer-events-none';
        let html = `<button data-p="${loanPage - 1}" class="${loanPage === 1 ? dis : def}">‹</button>`;
        for (let i = 1; i <= pages; i++) html += `<button data-p="${i}" class="${i === loanPage ? act : def}">${i}</button>`;
        html += `<button data-p="${loanPage + 1}" class="${loanPage === pages ? dis : def}">›</button>`;
        nav.innerHTML = html;
        nav.querySelectorAll('button[data-p]').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const p = parseInt(this.dataset.p);
                if (p < 1 || p > pages) return;
                loanPage = p;
                loanRender();
            });
        });
    }

    if (loanSearch) { loanSearch.addEventListener('input', loanApplyFilters); loanFilter.addEventListener('change', loanApplyFilters); }

    // ── Initial renders ──
    caApplyFilters();
    loanApplyFilters();

    @if(in_array(auth()->user()->role, ['hr', 'superadmin']))
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && document.getElementById('payModalOverlay').style.display !== 'none') closePayModal();
    });
    @endif
})();
</script>
@endpush
@endsection

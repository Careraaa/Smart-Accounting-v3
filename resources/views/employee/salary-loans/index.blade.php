@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeSlideUp {
    0%  { opacity:0; transform:translateY(14px); }
    100%{ opacity:1; transform:translateY(0); }
}
@keyframes scaleIn {
    0%  { opacity:0; transform:scale(0.93); }
    100%{ opacity:1; transform:scale(1); }
}
@keyframes slideInRight {
    0%  { opacity:0; transform:translateX(-10px); }
    100%{ opacity:1; transform:translateX(0); }
}
.sl-page-in  { animation:fadeSlideUp 0.42s cubic-bezier(0.16,1,0.3,1) both; }
.sl-form-in  { animation:slideInRight 0.4s cubic-bezier(0.16,1,0.3,1) 0.08s both; }
.sl-table-in { animation:fadeSlideUp 0.48s cubic-bezier(0.16,1,0.3,1) 0.12s both; }
.sl-flash-in { animation:fadeSlideUp 0.35s ease both; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50/60">
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 py-8">

        {{-- Page header --}}
        <div class="sl-page-in flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-7">
            <div>
                <p class="text-xs font-semibold tracking-widest text-gray-400 uppercase mb-1">Employee Portal</p>
                <h1 class="text-2xl font-extrabold tracking-tight text-gray-900 leading-tight">My Salary Loans</h1>
                <p class="text-sm text-gray-500 mt-1">Apply for a loan and monitor your repayment progress.</p>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-gray-200 text-xs font-semibold text-gray-600 shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                    {{ now()->format('l, F d, Y') }}
                </span>
                <a href="{{ route('employee.cash-advances.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 hover:border-gray-300 text-gray-700 text-sm font-semibold rounded-xl transition-all shadow-sm hover:shadow">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M1 10h22"/></svg>
                    Cash Advances
                </a>
            </div>
        </div>

        {{-- Flash --}}
        @if(session('success'))
        <div class="sl-flash-in flex items-center gap-2.5 px-4 py-3 mb-5 rounded-xl text-sm font-medium bg-emerald-50 border border-emerald-200 text-emerald-700">
            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
        @endif
        @if($errors->any())
        <div class="sl-flash-in flex items-center gap-2.5 px-4 py-3 mb-5 rounded-xl text-sm font-medium bg-red-50 border border-red-200 text-red-700">
            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
            {{ $errors->first() }}
        </div>
        @endif

        {{-- Two-column grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

            {{-- Form --}}
            <div class="lg:col-span-2 sl-form-in">
                @php $hasActive = $loans->whereIn('status', ['pending', 'active'])->count() > 0; @endphp
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm h-full">
                    <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100">
                        <span class="w-2 h-2 rounded-full bg-gray-900"></span>
                        <span class="text-sm font-bold text-gray-900">Apply for Salary Loan</span>
                    </div>
                    <div class="p-5">

                        @if($hasActive)
                        <div class="flex items-start gap-2.5 p-3.5 mb-4 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-700">
                            <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                            <span>You have a <strong>pending or active</strong> salary loan. You may apply for a new one after it is fully settled.</span>
                        </div>
                        @endif

                        <form method="POST" action="{{ route('employee.salary-loans.store') }}">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5">Loan Amount (₱)</label>
                                <input type="number" name="loan_amount" id="loanAmount"
                                       placeholder="e.g. 20,000" min="1" step="0.01"
                                       value="{{ old('loan_amount') }}"
                                       {{ $hasActive ? 'disabled' : '' }} required
                                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 outline-none transition-all duration-200 focus:border-gray-400 focus:ring-2 focus:ring-gray-200/50 disabled:bg-gray-50 disabled:text-gray-400 disabled:cursor-not-allowed">
                            </div>
                            <div class="mb-4">
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5">Payment Term <span class="text-gray-400 font-normal normal-case tracking-normal">(months)</span></label>
                                <select name="total_months" id="loanMonths" {{ $hasActive ? 'disabled' : '' }} required
                                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 outline-none transition-all duration-200 focus:border-gray-400 focus:ring-2 focus:ring-gray-200/50 disabled:bg-gray-50 disabled:text-gray-400 disabled:cursor-not-allowed">
                                    <option value="3" {{ old('total_months') == 3 ? 'selected' : '' }}>3 months</option>
                                    <option value="6" {{ old('total_months') == 6 ? 'selected' : '' }}>6 months</option>
                                    <option value="9" {{ old('total_months') == 9 ? 'selected' : '' }}>9 months</option>
                                    <option value="12" {{ old('total_months') == 12 ? 'selected' : '' }}>12 months</option>
                                    <option value="18" {{ old('total_months') == 18 ? 'selected' : '' }}>18 months</option>
                                    <option value="24" {{ old('total_months') == 24 ? 'selected' : '' }}>24 months</option>
                                    <option value="36" {{ old('total_months') == 36 ? 'selected' : '' }}>36 months</option>
                                </select>
                                <div class="text-xs text-gray-400 mt-1.5" id="loanMonthlyHint">—</div>
                            </div>
                            <div class="mb-5">
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5">Notes <span class="text-gray-400 font-normal normal-case tracking-normal">(optional)</span></label>
                                <textarea name="notes" rows="2"
                                          placeholder="Purpose of loan..."
                                          {{ $hasActive ? 'disabled' : '' }}
                                          class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 outline-none transition-all duration-200 focus:border-gray-400 focus:ring-2 focus:ring-gray-200/50 resize-none disabled:bg-gray-50 disabled:text-gray-400 disabled:cursor-not-allowed">{{ old('notes') }}</textarea>
                            </div>
                            <button type="submit" {{ $hasActive ? 'disabled' : '' }}
                                    class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-gray-900 hover:bg-gray-800 text-white text-sm font-bold rounded-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                Submit Application
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Loans table --}}
            <div class="lg:col-span-3 sl-table-in">
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
                    <div class="flex items-center gap-2 px-5 py-4 border-b border-gray-100">
                        <span class="w-2 h-2 rounded-full bg-gray-900"></span>
                        <span class="text-sm font-bold text-gray-900">My Loans</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-100">
                                    <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Loan Amount</th>
                                    <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Monthly</th>
                                    <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Remaining</th>
                                    <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Progress</th>
                                    <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Status</th>
                                    <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Start Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($loans as $loan)
                                    @php
                                        $totalMonths = $loan->monthly_deduction > 0 ? ceil($loan->loan_amount / $loan->monthly_deduction) : 0;
                                        $progress    = $totalMonths > 0 ? min(100, round(($loan->months_paid / $totalMonths) * 100)) : 0;
                                        $pillClasses = match($loan->status) {
                                            'pending'  => 'bg-amber-50 text-amber-700 border border-amber-200',
                                            'active'   => 'bg-blue-50 text-blue-700 border border-blue-200',
                                            'settled'  => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                                            'rejected' => 'bg-rose-50 text-rose-700 border border-rose-200',
                                            default    => 'bg-gray-100 text-gray-600 border border-gray-200',
                                        };
                                    @endphp
                                    <tr class="hover:bg-gray-50/70 transition-colors">
                                        <td class="px-5 py-3.5">
                                            <span class="font-mono font-bold text-gray-900">₱{{ number_format($loan->loan_amount, 2) }}</span>
                                        </td>
                                        <td class="px-5 py-3.5">
                                            <span class="font-mono text-sm text-gray-700">₱{{ number_format($loan->monthly_deduction, 2) }}</span>
                                        </td>
                                        <td class="px-5 py-3.5">
                                            <span class="font-mono text-sm text-gray-700">₱{{ number_format($loan->remaining_balance, 2) }}</span>
                                        </td>
                                        <td class="px-5 py-3.5 min-w-[150px]">
                                            <div class="flex items-center gap-2.5">
                                                <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                                                    <div class="h-full rounded-full {{ $progress >= 100 ? 'bg-emerald-500' : 'bg-blue-500' }}" style="width:{{ $progress }}%;"></div>
                                                </div>
                                                <span class="text-xs font-mono text-gray-500">{{ $progress }}%</span>
                                            </div>
                                            <div class="text-xs text-gray-400 mt-1 font-mono">{{ $loan->months_paid }}/{{ $totalMonths }} months paid</div>
                                        </td>
                                        <td class="px-5 py-3.5">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $pillClasses }}">
                                                {{ ucfirst($loan->status) }}
                                            </span>
                                            @if($loan->rejection_reason)
                                                <div class="flex items-center gap-1 mt-1 text-xs text-rose-600">
                                                    <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    {{ $loan->rejection_reason }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5 text-sm text-gray-700">
                                            {{ $loan->start_date ? $loan->start_date->format('M d, Y') : '—' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-5 py-16 text-center">
                                            <div class="flex flex-col items-center gap-3">
                                                <div class="w-14 h-14 rounded-2xl bg-gray-50 border border-gray-200 flex items-center justify-center">
                                                    <svg class="w-7 h-7 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                                </div>
                                                <p class="text-sm font-semibold text-gray-400">No salary loan applications yet.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($loans->hasPages())
                        <div class="flex items-center justify-between px-5 py-3.5 border-t border-gray-100 flex-wrap gap-2">
                            <div class="text-xs text-gray-400">
                                Showing <strong class="text-gray-700">{{ $loans->firstItem() }}</strong>–<strong class="text-gray-700">{{ $loans->lastItem() }}</strong> of <strong class="text-gray-700">{{ $loans->total() }}</strong>
                            </div>
                            {{ $loans->links('pagination::tailwind') }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@push('scripts')
<script>
const loanAmountInput = document.getElementById('loanAmount');
const loanMonthsInput = document.getElementById('loanMonths');
const loanMonthlyHint = document.getElementById('loanMonthlyHint');

function updateLoanHint() {
    const amount = parseFloat(loanAmountInput?.value) || 0;
    const months = parseInt(loanMonthsInput?.value)   || 0;
    if (amount > 0 && months > 0) {
        const monthly = amount / months;
        loanMonthlyHint.textContent = `₱${monthly.toFixed(2)} per month for ${months} month${months !== 1 ? 's' : ''}`;
    } else {
        loanMonthlyHint.textContent = '—';
    }
}

loanAmountInput?.addEventListener('input', updateLoanHint);
loanMonthsInput?.addEventListener('change', updateLoanHint);
updateLoanHint();
</script>
@endpush
@endsection

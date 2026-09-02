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
.ca-page-in  { animation:fadeSlideUp 0.42s cubic-bezier(0.16,1,0.3,1) both; }
.ca-form-in  { animation:slideInRight 0.4s cubic-bezier(0.16,1,0.3,1) 0.08s both; }
.ca-table-in { animation:fadeSlideUp 0.48s cubic-bezier(0.16,1,0.3,1) 0.12s both; }
.ca-flash-in { animation:fadeSlideUp 0.35s ease both; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50/60">
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 py-8">

        {{-- Page header --}}
        <div class="ca-page-in flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-7">
            <div>
                <p class="text-xs font-semibold tracking-widest text-gray-400 uppercase mb-1">Employee Portal</p>
                <h1 class="text-2xl font-extrabold tracking-tight text-gray-900 leading-tight">My Cash Advances</h1>
                <p class="text-sm text-gray-500 mt-1">Request a cash advance and track its approval status.</p>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-gray-200 text-xs font-semibold text-gray-600 shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                    {{ now()->format('l, F d, Y') }}
                </span>
            </div>
        </div>

        {{-- Flash --}}
        @if(session('success'))
        <div class="ca-flash-in flex items-center gap-2.5 px-4 py-3 mb-5 rounded-xl text-sm font-medium bg-emerald-50 border border-emerald-200 text-emerald-700">
            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
        @endif
        @if($errors->any())
        <div class="ca-flash-in flex items-center gap-2.5 px-4 py-3 mb-5 rounded-xl text-sm font-medium bg-red-50 border border-red-200 text-red-700">
            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
            {{ $errors->first() }}
        </div>
        @endif

        {{-- Two-column grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

            {{-- Form --}}
            <div class="lg:col-span-2 ca-form-in">
                @php $hasPending = $advances->where('status', 'pending')->count() > 0; @endphp
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm h-full">
                    <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100">
                        <span class="w-2 h-2 rounded-full bg-gray-900"></span>
                        <span class="text-sm font-bold text-gray-900">Request Cash Advance</span>
                    </div>
                    <div class="p-5">

                        @if($hasPending)
                        <div class="flex items-start gap-2.5 p-3.5 mb-4 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-700">
                            <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                            <span>You have a <strong>pending</strong> cash advance request. Please wait for it to be processed before submitting a new one.</span>
                        </div>
                        @endif

                        <form method="POST" action="{{ route('employee.cash-advances.store') }}">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5">Amount (₱)</label>
                                <input type="number" name="amount" id="caAmount"
                                       placeholder="e.g. 5,000" min="1" step="0.01"
                                       value="{{ old('amount') }}"
                                       {{ $hasPending ? 'disabled' : '' }} required
                                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 outline-none transition-all duration-200 focus:border-gray-400 focus:ring-2 focus:ring-gray-200/50 disabled:bg-gray-50 disabled:text-gray-400 disabled:cursor-not-allowed">
                            </div>
                            <div class="mb-4">
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5">Pay Over <span class="text-gray-400 font-normal normal-case tracking-normal">(months)</span></label>
                                <select name="repayment_months" id="caMonths" {{ $hasPending ? 'disabled' : '' }} required
                                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 outline-none transition-all duration-200 focus:border-gray-400 focus:ring-2 focus:ring-gray-200/50 disabled:bg-gray-50 disabled:text-gray-400 disabled:cursor-not-allowed">
                                    <option value="1" {{ old('repayment_months') == 1 ? 'selected' : '' }}>1 month</option>
                                    <option value="2" {{ old('repayment_months') == 2 ? 'selected' : '' }}>2 months</option>
                                    <option value="3" {{ old('repayment_months') == 3 ? 'selected' : '' }}>3 months</option>
                                    <option value="4" {{ old('repayment_months') == 4 ? 'selected' : '' }}>4 months</option>
                                    <option value="5" {{ old('repayment_months') == 5 ? 'selected' : '' }}>5 months</option>
                                    <option value="6" {{ old('repayment_months') == 6 ? 'selected' : '' }}>6 months</option>
                                </select>
                                <div class="text-xs text-gray-400 mt-1.5" id="caMonthlyHint">—</div>
                            </div>
                            <div class="mb-5">
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5">Notes <span class="text-gray-400 font-normal normal-case tracking-normal">(optional)</span></label>
                                <textarea name="notes" rows="3"
                                          placeholder="Reason for cash advance..."
                                          {{ $hasPending ? 'disabled' : '' }}
                                          class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 outline-none transition-all duration-200 focus:border-gray-400 focus:ring-2 focus:ring-gray-200/50 resize-none disabled:bg-gray-50 disabled:text-gray-400 disabled:cursor-not-allowed">{{ old('notes') }}</textarea>
                            </div>
                            <button type="submit" {{ $hasPending ? 'disabled' : '' }}
                                    class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-gray-900 hover:bg-gray-800 text-white text-sm font-bold rounded-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                Submit Request
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- History table --}}
            <div class="lg:col-span-3 ca-table-in">
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
                    <div class="flex items-center gap-2 px-5 py-4 border-b border-gray-100">
                        <span class="w-2 h-2 rounded-full bg-gray-900"></span>
                        <span class="text-sm font-bold text-gray-900">Request History</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-100">
                                    <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Date Requested</th>
                                    <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Amount</th>
                                    <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Status</th>
                                    <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Notes</th>
                                    <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Deducted On</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($advances as $advance)
                                    @php
                                        $displayStatus = $advance->amount_deducted > 0 ? 'deducted' : $advance->status;
                                        $pillClasses = match($displayStatus) {
                                            'pending'  => 'bg-amber-50 text-amber-700 border border-amber-200',
                                            'approved' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                                            'rejected' => 'bg-rose-50 text-rose-700 border border-rose-200',
                                            'deducted' => 'bg-violet-50 text-violet-700 border border-violet-200',
                                            default    => 'bg-gray-100 text-gray-600 border border-gray-200',
                                        };
                                    @endphp
                                    <tr class="hover:bg-gray-50/70 transition-colors">
                                        <td class="px-5 py-3.5 text-sm text-gray-700">
                                            {{ $advance->request_date ? $advance->request_date->format('M d, Y') : '—' }}
                                        </td>
                                        <td class="px-5 py-3.5">
                                            <span class="font-mono font-bold text-gray-900">₱{{ number_format($advance->amount, 2) }}</span>
                                        </td>
                                        <td class="px-5 py-3.5">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $pillClasses }}">
                                                {{ ucfirst($displayStatus) }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3.5 max-w-[200px]">
                                            <span class="text-gray-500 text-sm block truncate" title="{{ $advance->notes }}">{{ $advance->notes ?: '—' }}</span>
                                            @if($advance->rejection_reason)
                                                <div class="flex items-center gap-1 mt-1 text-xs text-rose-600">
                                                    <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    {{ $advance->rejection_reason }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5 text-sm text-gray-700">
                                            @php($deductionPayrolls = $advance->deductionPayrolls())
                                            @if($deductionPayrolls->isNotEmpty())
                                                <details class="group">
                                                    <summary class="cursor-pointer list-none text-blue-700 hover:text-blue-900">
                                                        {{ $deductionPayrolls->count() }} deduction date{{ $deductionPayrolls->count() > 1 ? 's' : '' }}
                                                        <span class="text-[10px] text-gray-400 group-open:hidden">(view)</span>
                                                    </summary>
                                                    <div class="mt-1 space-y-0.5 text-xs text-gray-600">
                                                        @foreach($deductionPayrolls as $deductionPayroll)
                                                            <div>Period ending {{ $deductionPayroll->payroll_period_end ? \Carbon\Carbon::parse($deductionPayroll->payroll_period_end)->format('M d, Y') : '—' }}</div>
                                                        @endforeach
                                                    </div>
                                                </details>
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-5 py-16 text-center">
                                            <div class="flex flex-col items-center gap-3">
                                                <div class="w-14 h-14 rounded-2xl bg-gray-50 border border-gray-200 flex items-center justify-center">
                                                    <svg class="w-7 h-7 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><rect x="1" y="4" width="22" height="16" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M1 10h22"/></svg>
                                                </div>
                                                <p class="text-sm font-semibold text-gray-400">No cash advance requests yet.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($advances->hasPages())
                        <div class="flex items-center justify-between px-5 py-3.5 border-t border-gray-100 flex-wrap gap-2">
                            <div class="text-xs text-gray-400">
                                Showing <strong class="text-gray-700">{{ $advances->firstItem() }}</strong>–<strong class="text-gray-700">{{ $advances->lastItem() }}</strong> of <strong class="text-gray-700">{{ $advances->total() }}</strong>
                            </div>
                            {{ $advances->links('pagination::tailwind') }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@push('scripts')
<script>
const caAmountInput = document.getElementById('caAmount');
const caMonthsInput = document.getElementById('caMonths');
const caMonthlyHint = document.getElementById('caMonthlyHint');

function updateCAHint() {
    const amount = parseFloat(caAmountInput?.value) || 0;
    const months = parseInt(caMonthsInput?.value)   || 0;
    if (amount > 0 && months > 0) {
        const monthly = amount / months;
        caMonthlyHint.textContent = `₱${monthly.toFixed(2)} per month for ${months} month${months !== 1 ? 's' : ''}`;
    } else {
        caMonthlyHint.textContent = '—';
    }
}

caAmountInput?.addEventListener('input', updateCAHint);
caMonthsInput?.addEventListener('change', updateCAHint);
updateCAHint();
</script>
@endpush
@endsection

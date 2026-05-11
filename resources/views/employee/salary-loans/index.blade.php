@extends('layouts.layout')

@section('title', 'My Salary Loans')

@push('styles')
    @include('employee._ui-styles')
@endpush

@section('content')
<div class="container-fluid empui-page empui-wrap">
    <div class="empui-backdrop"><div class="empui-grid"></div></div>
    <div class="empui-content">

        {{-- Hero --}}
        <div class="empui-hero">
            <div class="empui-hero-left">
                <h1 class="empui-title">My Salary Loans</h1>
                <p class="empui-sub">Apply for a loan and monitor your repayment progress.</p>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    <span class="empui-chip">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                        {{ now()->format('l, F d, Y') }}
                    </span>
                    <span class="empui-chip">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        My Finances
                    </span>
                </div>
            </div>
            <div class="empui-hero-right">
                <a href="{{ route('employee.cash-advances.index') }}" class="empui-btn-sec">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M1 10h22"/></svg>
                    Cash Advances
                </a>
            </div>
        </div>

        {{-- Flash --}}
        @if(session('success'))
            <div class="empui-flash success">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="empui-flash error">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg>
                {{ $errors->first() }}
            </div>
        @endif

        <div class="row g-3">

            {{-- Apply form --}}
            <div class="col-lg-4">
                @php $hasActive = $loans->whereIn('status', ['pending', 'active'])->count() > 0; @endphp
                <div class="empui-card h-100">
                    <div class="empui-card-head">
                        <span class="empui-card-title">
                            <span class="empui-dot"></span>
                            Apply for Salary Loan
                        </span>
                    </div>
                    <div class="empui-card-body">

                        @if($hasActive)
                            <div class="empui-notice">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                                <span>You have a <strong>pending or active</strong> salary loan. You may apply for a new one after it is fully settled.</span>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('employee.salary-loans.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="empui-field-label">Loan Amount (₱)</label>
                                <input type="number" name="loan_amount" id="loanAmount" class="empui-input"
                                       placeholder="e.g. 20,000" min="1" step="0.01"
                                       value="{{ old('loan_amount') }}"
                                       {{ $hasActive ? 'disabled' : '' }} required>
                            </div>
                            <div class="mb-3">
                                <label class="empui-field-label">Monthly Deduction (₱)</label>
                                <input type="number" name="monthly_deduction" id="monthlyDeduction" class="empui-input"
                                       placeholder="e.g. 2,000" min="1" step="0.01"
                                       value="{{ old('monthly_deduction') }}"
                                       {{ $hasActive ? 'disabled' : '' }} required>
                                <div class="empui-field-hint highlight" id="payoffHint">—</div>
                            </div>
                            <div class="mb-3">
                                <label class="empui-field-label">Preferred Start Date</label>
                                <input type="date" name="start_date" class="empui-input"
                                       value="{{ old('start_date', date('Y-m-d')) }}"
                                       {{ $hasActive ? 'disabled' : '' }} required>
                            </div>
                            <div class="mb-4">
                                <label class="empui-field-label">Notes <span class="opt">(optional)</span></label>
                                <textarea name="notes" class="empui-textarea" rows="2"
                                          placeholder="Purpose of loan..."
                                          {{ $hasActive ? 'disabled' : '' }}>{{ old('notes') }}</textarea>
                            </div>
                            <button type="submit" class="empui-btn w-100" style="justify-content:center;" {{ $hasActive ? 'disabled' : '' }}>
                                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                Submit Application
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Loan history --}}
            <div class="col-lg-8">
                <div class="empui-card">
                    <div class="empui-card-head">
                        <span class="empui-card-title">
                            <span class="empui-dot"></span>
                            My Loans
                        </span>
                    </div>
                    <div class="table-responsive">
                        <table class="table empui-table w-100 mb-0">
                            <thead>
                                <tr>
                                    <th>Loan Amount</th>
                                    <th>Monthly</th>
                                    <th>Remaining</th>
                                    <th>Progress</th>
                                    <th>Status</th>
                                    <th>Start Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($loans as $loan)
                                    @php
                                        $totalMonths = $loan->monthly_deduction > 0 ? ceil($loan->loan_amount / $loan->monthly_deduction) : 0;
                                        $progress    = $totalMonths > 0 ? min(100, round(($loan->months_paid / $totalMonths) * 100)) : 0;
                                        $loanPill    = match($loan->status) {
                                            'pending'  => 'pending',
                                            'active'   => 'active',
                                            'settled'  => 'settled',
                                            'rejected' => 'rejected',
                                            default    => 'neutral',
                                        };
                                    @endphp
                                    <tr>
                                        <td>
                                            <span class="fw-bold empui-mono" style="color:#111827;">₱{{ number_format($loan->loan_amount, 2) }}</span>
                                        </td>
                                        <td>
                                            <span class="empui-mono" style="color:#374151;font-size:.845rem;">₱{{ number_format($loan->monthly_deduction, 2) }}</span>
                                        </td>
                                        <td>
                                            <span class="empui-mono" style="color:#374151;font-size:.845rem;">₱{{ number_format($loan->remaining_balance, 2) }}</span>
                                        </td>
                                        <td style="min-width:140px;">
                                            <div class="empui-progress-wrap">
                                                <div class="empui-progress-bar">
                                                    <div class="empui-progress-fill" style="width:{{ $progress }}%;"></div>
                                                </div>
                                                <span class="empui-progress-pct">{{ $progress }}%</span>
                                            </div>
                                            <div class="empui-progress-sub">{{ $loan->months_paid }}/{{ $totalMonths }} months paid</div>
                                        </td>
                                        <td>
                                            <span class="empui-pill {{ $loanPill }}">{{ ucfirst($loan->status) }}</span>
                                            @if($loan->rejection_reason)
                                                <div style="font-size:.74rem;color:#be123c;margin-top:3px;">
                                                    <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="vertical-align:middle;"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    {{ $loan->rejection_reason }}
                                                </div>
                                            @endif
                                        </td>
                                        <td style="font-size:.845rem;color:#374151;">
                                            {{ $loan->start_date ? \Carbon\Carbon::parse($loan->start_date)->format('M d, Y') : '—' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">
                                            <div class="empui-empty">
                                                <svg class="empui-empty-icon" width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                                <p class="empui-empty-text">No salary loan applications yet.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($loans->hasPages())
                        <div class="d-flex justify-content-end px-4 py-3" style="border-top:1px solid #f3f4f6;">
                            {{ $loans->links() }}
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
const monthlyInput    = document.getElementById('monthlyDeduction');
const payoffHint      = document.getElementById('payoffHint');

function updateHint() {
    const amount  = parseFloat(loanAmountInput?.value) || 0;
    const monthly = parseFloat(monthlyInput?.value)    || 0;
    if (amount > 0 && monthly > 0) {
        const months = Math.ceil(amount / monthly);
        payoffHint.textContent = `≈ ${months} month${months !== 1 ? 's' : ''} to pay off`;
    } else {
        payoffHint.textContent = '—';
    }
}

loanAmountInput?.addEventListener('input', updateHint);
monthlyInput?.addEventListener('input', updateHint);
</script>
@endpush
@endsection

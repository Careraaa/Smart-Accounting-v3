@extends('layouts.layout')

@section('title', 'My Cash Advances')

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
                <h1 class="empui-title">My Cash Advances</h1>
                <p class="empui-sub">Request a cash advance and track its approval status.</p>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    <span class="empui-chip">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                        {{ now()->format('l, F d, Y') }}
                    </span>
                    <span class="empui-chip">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M1 10h22"/></svg>
                        My Finances
                    </span>
                </div>
            </div>
            <div class="empui-hero-right">
                <a href="{{ route('employee.salary-loans.index') }}" class="empui-btn-sec">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    Salary Loans
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

            {{-- Submit form --}}
            <div class="col-lg-4">
                @php $hasPending = $advances->where('status', 'pending')->count() > 0; @endphp
                <div class="empui-card h-100">
                    <div class="empui-card-head">
                        <span class="empui-card-title">
                            <span class="empui-dot"></span>
                            Request Cash Advance
                        </span>
                    </div>
                    <div class="empui-card-body">

                        @if($hasPending)
                            <div class="empui-notice">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                                <span>You have a <strong>pending</strong> cash advance request. Please wait for it to be processed before submitting a new one.</span>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('employee.cash-advances.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="empui-field-label">Amount (₱)</label>
                                <input type="number" name="amount" id="caAmount" class="empui-input"
                                       placeholder="e.g. 5,000" min="1" step="0.01"
                                       value="{{ old('amount') }}"
                                       {{ $hasPending ? 'disabled' : '' }} required>
                            </div>
                            <div class="mb-3">
                                <label class="empui-field-label">Pay Over <span class="opt">(months)</span></label>
                                <select name="repayment_months" id="caMonths" class="empui-input" {{ $hasPending ? 'disabled' : '' }} required>
                                    <option value="1" {{ old('repayment_months') == 1 ? 'selected' : '' }}>1 month</option>
                                    <option value="2" {{ old('repayment_months') == 2 ? 'selected' : '' }}>2 months</option>
                                    <option value="3" {{ old('repayment_months') == 3 ? 'selected' : '' }}>3 months</option>
                                    <option value="4" {{ old('repayment_months') == 4 ? 'selected' : '' }}>4 months</option>
                                    <option value="5" {{ old('repayment_months') == 5 ? 'selected' : '' }}>5 months</option>
                                    <option value="6" {{ old('repayment_months') == 6 ? 'selected' : '' }}>6 months</option>
                                </select>
                                <div class="empui-field-hint highlight" id="caMonthlyHint">—</div>
                            </div>
                            <div class="mb-4">
                                <label class="empui-field-label">Notes <span class="opt">(optional)</span></label>
                                <textarea name="notes" class="empui-textarea" rows="3"
                                          placeholder="Reason for cash advance..."
                                          {{ $hasPending ? 'disabled' : '' }}>{{ old('notes') }}</textarea>
                            </div>
                            <button type="submit" class="empui-btn w-100" style="justify-content:center;" {{ $hasPending ? 'disabled' : '' }}>
                                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                Submit Request
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- History table --}}
            <div class="col-lg-8">
                <div class="empui-card">
                    <div class="empui-card-head">
                        <span class="empui-card-title">
                            <span class="empui-dot"></span>
                            Request History
                        </span>
                    </div>
                    <div class="table-responsive">
                        <table class="table empui-table w-100 mb-0">
                            <thead>
                                <tr>
                                    <th>Date Requested</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                    <th>Deducted On</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($advances as $advance)
                                    @php
                                        $pill = match($advance->status) {
                                            'pending'  => 'pending',
                                            'approved' => 'approved',
                                            'rejected' => 'rejected',
                                            'deducted' => 'deducted',
                                            default    => 'neutral',
                                        };
                                    @endphp
                                    <tr>
                                        <td style="font-size:.845rem;color:#374151;">
                                            {{ $advance->request_date ? \Carbon\Carbon::parse($advance->request_date)->format('M d, Y') : '—' }}
                                        </td>
                                        <td>
                                            <span class="fw-bold empui-mono" style="color:#111827;">₱{{ number_format($advance->amount, 2) }}</span>
                                        </td>
                                        <td>
                                            <span class="empui-pill {{ $pill }}">{{ ucfirst($advance->status) }}</span>
                                        </td>
                                        <td style="max-width:200px;">
                                            <span class="empui-muted text-truncate d-inline-block" style="max-width:180px;" title="{{ $advance->notes }}">
                                                {{ $advance->notes ?: '—' }}
                                            </span>
                                            @if($advance->rejection_reason)
                                                <div style="font-size:.74rem;color:#be123c;margin-top:3px;">
                                                    <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="vertical-align:middle;"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    {{ $advance->rejection_reason }}
                                                </div>
                                            @endif
                                        </td>
                                        <td style="font-size:.845rem;color:#374151;">
                                            @if($advance->deductedPayroll)
                                                Period ending {{ \Carbon\Carbon::parse($advance->deductedPayroll->payroll_period_end)->format('M d, Y') }}
                                            @else
                                                <span class="empui-muted">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">
                                            <div class="empui-empty">
                                                <svg class="empui-empty-icon" width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><rect x="1" y="4" width="22" height="16" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M1 10h22"/></svg>
                                                <p class="empui-empty-text">No cash advance requests yet.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($advances->hasPages())
                        <div class="d-flex justify-content-end px-4 py-3" style="border-top:1px solid #f3f4f6;">
                            {{ $advances->links() }}
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

@extends('layouts.layout')

@section('title', 'My Salary Loans')

@push('styles')
    @include('employee._ui-styles')
@endpush

@section('content')
<div class="container-fluid empui-page empui-wrap">
    <div class="empui-backdrop"><div class="empui-grid"></div></div>
    <div class="empui-content">

    <div class="empui-hero">
        <div class="empui-hero-left">
            <h1 class="empui-title">My Salary Loans</h1>
            <p class="empui-sub">Apply for a loan and monitor your repayment progress.</p>
            <div class="d-flex flex-wrap gap-2 mt-2">
                <span class="empui-chip"><i class="feather-calendar"></i> {{ now()->format('l, F d, Y') }}</span>
                <span class="empui-chip"><i class="feather-trending-up"></i> My Finances</span>
            </div>
        </div>
        <div class="empui-hero-right">
            <a href="{{ route('employee.cash-advances.index') }}" class="empui-btn-sec">
                <i class="feather-credit-card"></i>
                Cash Advances
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3">
            <i class="feather-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3">
            <i class="feather-alert-circle me-2"></i> {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3">

        {{-- ── Submit Form ── --}}
        <div class="col-lg-4">
            <div class="empui-card h-100">
                <div class="empui-card-head">
                    <p class="empui-card-title"><span class="empui-dot"></span> Apply for Salary Loan</p>
                </div>
                <div class="empui-card-body">
                    @php
                        $hasActive = $loans->whereIn('status', ['pending', 'active'])->count() > 0;
                    @endphp

                    @if ($hasActive)
                        <div class="alert alert-warning mb-3" style="font-size:.845rem;">
                            <i class="feather-alert-triangle me-2"></i>
                            You already have a <strong>pending or active</strong> salary loan. You may apply for a new one after it is fully settled.
                        </div>
                    @endif

                    <form method="POST" action="{{ route('employee.salary-loans.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Loan Amount (₱)</label>
                            <input type="number" name="loan_amount" id="loanAmount" class="form-control"
                                   placeholder="e.g. 20000" min="1" step="0.01"
                                   value="{{ old('loan_amount') }}"
                                   {{ $hasActive ? 'disabled' : '' }} required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Monthly Deduction (₱)</label>
                            <input type="number" name="monthly_deduction" id="monthlyDeduction" class="form-control"
                                   placeholder="e.g. 2000" min="1" step="0.01"
                                   value="{{ old('monthly_deduction') }}"
                                   {{ $hasActive ? 'disabled' : '' }} required>
                            <div class="prl-hint mt-1" id="payoffHint">—</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Preferred Start Date</label>
                            <input type="date" name="start_date" class="form-control"
                                   value="{{ old('start_date', date('Y-m-d')) }}"
                                   {{ $hasActive ? 'disabled' : '' }} required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Notes <span class="text-muted fw-normal">(optional)</span></label>
                            <textarea name="notes" class="form-control" rows="2"
                                      placeholder="Purpose of loan..."
                                      {{ $hasActive ? 'disabled' : '' }}>{{ old('notes') }}</textarea>
                        </div>

                        <button type="submit" class="empui-btn w-100" style="justify-content:center;" {{ $hasActive ? 'disabled' : '' }}>
                            <i class="feather-send"></i> Submit Application
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ── Loan History ── --}}
        <div class="col-lg-8">
            <div class="empui-card">
                <div class="empui-card-head">
                    <p class="empui-card-title"><span class="empui-dot"></span> My Loans</p>
                </div>
                <div class="p-0">
                    <div class="table-responsive">
                        <table class="table mb-0 empui-table">
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
                                @forelse ($loans as $loan)
                                @php
                                    $totalMonths = $loan->monthly_deduction > 0
                                        ? ceil($loan->loan_amount / $loan->monthly_deduction)
                                        : 0;
                                    $progress = $totalMonths > 0
                                        ? min(100, round(($loan->months_paid / $totalMonths) * 100))
                                        : 0;
                                @endphp
                                <tr>
                                    <td class="fw-bold" style="font-size:.9rem;">₱{{ number_format($loan->loan_amount, 2) }}</td>
                                    <td style="font-size:.845rem;">₱{{ number_format($loan->monthly_deduction, 2) }}</td>
                                    <td style="font-size:.845rem;">₱{{ number_format($loan->remaining_balance, 2) }}</td>
                                    <td style="min-width:130px;">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height:6px; border-radius:4px;">
                                                <div class="progress-bar bg-success" style="width:{{ $progress }}%"></div>
                                            </div>
                                            <span style="font-size:.72rem; color:#9898a8; white-space:nowrap;">
                                                {{ $progress }}%
                                            </span>
                                        </div>
                                        <div style="font-size:.72rem; color:#9898a8; margin-top:2px;">
                                            {{ $loan->months_paid }}/{{ $totalMonths }} months paid
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $loanStatusMap = [
                                                'pending'  => 'emp-badge-pending',
                                                'active'   => 'emp-badge-approved',
                                                'settled'  => 'emp-badge-active',
                                                'rejected' => 'emp-badge-inactive',
                                            ];
                                        @endphp
                                        <span class="emp-badge {{ $loanStatusMap[$loan->status] ?? '' }}">
                                            {{ ucfirst($loan->status) }}
                                        </span>
                                        @if ($loan->rejection_reason)
                                            <div class="text-danger mt-1" style="font-size:.75rem;">
                                                <i class="feather-x-circle me-1"></i>{{ $loan->rejection_reason }}
                                            </div>
                                        @endif
                                    </td>
                                    <td style="font-size:.845rem;">
                                        {{ $loan->start_date ? \Carbon\Carbon::parse($loan->start_date)->format('M d, Y') : '—' }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <i class="feather-inbox d-block mb-2" style="font-size:2rem; opacity:.3;"></i>
                                        No salary loan applications yet.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($loans->hasPages())
                <div class="d-flex justify-content-end px-3 py-2">
                    {{ $loans->links() }}
                </div>
                @endif
            </div>
        </div>

    </div>
</div>

    </div>
</div>

@push('scripts')
<script>
// Live payoff estimate
const loanAmountInput   = document.getElementById('loanAmount');
const monthlyInput      = document.getElementById('monthlyDeduction');
const payoffHint        = document.getElementById('payoffHint');

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
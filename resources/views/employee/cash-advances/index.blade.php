@extends('layouts.layout')

@section('title', 'My Cash Advances')

@push('styles')
    @include('employee._ui-styles')
@endpush

@section('content')
<div class="container-fluid empui-page empui-wrap">
    <div class="empui-backdrop"><div class="empui-grid"></div></div>
    <div class="empui-content">

    <div class="empui-hero">
        <div class="empui-hero-left">
            <h1 class="empui-title">My Cash Advances</h1>
            <p class="empui-sub">Request a cash advance and track status updates.</p>
            <div class="d-flex flex-wrap gap-2 mt-2">
                <span class="empui-chip"><i class="feather-calendar"></i> {{ now()->format('l, F d, Y') }}</span>
                <span class="empui-chip"><i class="feather-credit-card"></i> My Finances</span>
            </div>
        </div>
        <div class="empui-hero-right">
            <a href="{{ route('employee.salary-loans.index') }}" class="empui-btn-sec">
                <i class="feather-trending-up"></i>
                Salary Loans
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
                    <p class="empui-card-title"><span class="empui-dot"></span> Request Cash Advance</p>
                </div>
                <div class="empui-card-body">
                    @php
                        $hasPending = $advances->where('status', 'pending')->count() > 0;
                    @endphp

                    @if ($hasPending)
                        <div class="alert alert-warning mb-3" style="font-size:.845rem;">
                            <i class="feather-alert-triangle me-2"></i>
                            You already have a <strong>pending</strong> cash advance request. Please wait for it to be processed before submitting a new one.
                        </div>
                    @endif

                    <form method="POST" action="{{ route('employee.cash-advances.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Amount (₱)</label>
                            <input type="number" name="amount" class="form-control"
                                   placeholder="e.g. 5000" min="1" step="0.01"
                                   value="{{ old('amount') }}"
                                   {{ $hasPending ? 'disabled' : '' }} required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Request Date</label>
                            <input type="date" name="request_date" class="form-control"
                                   value="{{ old('request_date', date('Y-m-d')) }}"
                                   {{ $hasPending ? 'disabled' : '' }} required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Notes <span class="text-muted fw-normal">(optional)</span></label>
                            <textarea name="notes" class="form-control" rows="3"
                                      placeholder="Reason for cash advance..."
                                      {{ $hasPending ? 'disabled' : '' }}>{{ old('notes') }}</textarea>
                        </div>

                        <button type="submit" class="empui-btn w-100" style="justify-content:center;" {{ $hasPending ? 'disabled' : '' }}>
                            <i class="feather-send"></i> Submit Request
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ── Request History ── --}}
        <div class="col-lg-8">
            <div class="empui-card">
                <div class="empui-card-head">
                    <p class="empui-card-title"><span class="empui-dot"></span> My Requests</p>
                </div>
                <div class="p-0">
                    <div class="table-responsive">
                        <table class="table mb-0 empui-table">
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
                                @forelse ($advances as $advance)
                                <tr>
                                    <td style="font-size:.845rem;">
                                        {{ $advance->request_date ? \Carbon\Carbon::parse($advance->request_date)->format('M d, Y') : '—' }}
                                    </td>
                                    <td class="fw-bold" style="font-size:.9rem;">₱{{ number_format($advance->amount, 2) }}</td>
                                    <td>
                                        @php
                                            $statusMap = [
                                                'pending'  => 'emp-badge-pending',
                                                'approved' => 'emp-badge-approved',
                                                'rejected' => 'emp-badge-inactive',
                                                'deducted' => 'emp-badge-active',
                                            ];
                                        @endphp
                                        <span class="emp-badge {{ $statusMap[$advance->status] ?? '' }}">
                                            {{ ucfirst($advance->status) }}
                                        </span>
                                    </td>
                                    <td style="font-size:.845rem; max-width:200px;">
                                        <span class="text-truncate d-inline-block" style="max-width:180px;" title="{{ $advance->notes }}">
                                            {{ $advance->notes ?: '—' }}
                                        </span>
                                        @if ($advance->rejection_reason)
                                            <div class="text-danger mt-1" style="font-size:.75rem;">
                                                <i class="feather-x-circle me-1"></i>{{ $advance->rejection_reason }}
                                            </div>
                                        @endif
                                    </td>
                                    <td style="font-size:.845rem;">
                                        @if ($advance->deductedPayroll)
                                            Period ending {{ \Carbon\Carbon::parse($advance->deductedPayroll->payroll_period_end)->format('M d, Y') }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        <i class="feather-inbox d-block mb-2" style="font-size:2rem; opacity:.3;"></i>
                                        No cash advance requests yet.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($advances->hasPages())
                <div class="d-flex justify-content-end px-3 py-2">
                    {{ $advances->links() }}
                </div>
                @endif
            </div>
        </div>

    </div>
</div>

    </div>
</div>
@endsection
@extends('layouts.layout')

@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="card-title mb-0">Payroll Receivables</span>
            </div>
            <div class="card-body">

                {{-- Summary Cards --}}
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card card-statistic">
                            <div class="card-body">
                                <div class="stat-label">Total Receivable</div>
                                <h3 class="mb-0">₱{{ number_format($totalReceivable, 2) }}</h3>
                                <small class="text-muted">from approved payrolls</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-statistic">
                            <div class="card-body">
                                <div class="stat-label" style="color:#0284c7;">Pending Release</div>
                                <h3 class="mb-0" style="color:#0284c7;">{{ $approvedCount }}</h3>
                                <small class="text-muted">approved, not yet paid</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-statistic">
                            <div class="card-body">
                                <div class="stat-label" style="color:#16a34a;">Paid This Month</div>
                                <h3 class="mb-0" style="color:#16a34a;">{{ $paidThisMonth }}</h3>
                                <small class="text-muted">{{ now()->format('F Y') }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-statistic">
                            <div class="card-body">
                                <div class="stat-label">Total Paid</div>
                                <h3 class="mb-0">{{ $paidCount }}</h3>
                                <small class="text-muted">all time</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Filters --}}
                <form method="GET" action="{{ route('payroll.receivables.index') }}" class="d-flex gap-2 flex-wrap mb-3">
                    <input type="text" name="search" class="form-control form-control-sm"
                        placeholder="Search employee..." value="{{ $search }}" style="flex:1; min-width:200px;">
                    <input type="month" name="period" class="form-control form-control-sm" value="{{ $periodFilter }}"
                        style="max-width:160px;">
                    <select name="status" class="form-control form-control-sm" style="max-width:140px;">
                        <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Pending Release</option>
                        <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option>
                    </select>
                    <button type="submit" class="btn btn-sm btn-outline-secondary">Filter</button>
                </form>

            </div>

            {{-- Batch pay form wraps the table --}}
            <form id="batch-form" action="{{ route('payroll.receivables.batch-paid') }}" method="POST">
                @csrf

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover w-100 mb-0">
                            <thead>
                                <tr>
                                    @if ($status === 'approved' || $status === 'all')
                                        <th style="width:40px; padding:12px 16px;">
                                            <input type="checkbox" id="select-all" class="form-check-input">
                                        </th>
                                    @endif
                                    <th>
                                        <div class="sort-link">Employee</div>
                                    </th>
                                    <th>
                                        <div class="sort-link">Period</div>
                                    </th>
                                    <th>
                                        <div class="sort-link">Basic Salary</div>
                                    </th>
                                    <th>
                                        <div class="sort-link">Allowances</div>
                                    </th>
                                    <th>
                                        <div class="sort-link">Deductions</div>
                                    </th>
                                    <th>
                                        <div class="sort-link">Net Pay</div>
                                    </th>
                                    <th class="text-center">
                                        <div class="sort-link justify-content-center">Status</div>
                                    </th>
                                    <th class="text-center">
                                        <div class="sort-link justify-content-center">Actions</div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payrolls as $payroll)
                                    <tr>
                                        @if ($status === 'approved' || $status === 'all')
                                            <td style="padding:12px 16px;">
                                                @if ($payroll->status === 'approved')
                                                    <input type="checkbox" name="payroll_ids[]" value="{{ $payroll->id }}"
                                                        class="form-check-input payroll-check">
                                                @endif
                                            </td>
                                        @endif
                                        <td>
                                            <strong>{{ $payroll->employee->first_name ?? 'N/A' }}
                                                {{ $payroll->employee->last_name ?? '' }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $payroll->employee->position ?? '' }}</small>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $payroll->payroll_period_start->format('M d') }} –
                                                {{ $payroll->payroll_period_end->format('M d, Y') }}
                                            </small>
                                        </td>
                                        <td>₱{{ number_format($payroll->basic_salary, 2) }}</td>
                                        <td class="text-success">+₱{{ number_format($payroll->total_allowances, 2) }}</td>
                                        <td class="text-danger">-₱{{ number_format($payroll->total_deductions, 2) }}</td>
                                        <td><strong>₱{{ number_format($payroll->net_pay, 2) }}</strong></td>
                                        <td class="text-center">
                                            @php
                                                $statusMap = [
                                                    'approved' => [
                                                        'bg' => '#f0f9ff',
                                                        'color' => '#0284c7',
                                                        'border' => '#bae6fd',
                                                    ],
                                                    'paid' => [
                                                        'bg' => '#f0fdf4',
                                                        'color' => '#16a34a',
                                                        'border' => '#bbf7d0',
                                                    ],
                                                    'pending' => [
                                                        'bg' => '#fffbeb',
                                                        'color' => '#d97706',
                                                        'border' => '#fde68a',
                                                    ],
                                                ];
                                                $st = $statusMap[$payroll->status] ?? [
                                                    'bg' => '#f4f5f7',
                                                    'color' => '#9898a8',
                                                    'border' => '#e8e8ef',
                                                ];
                                            @endphp
                                            <span class="emp-badge"
                                                style="background:{{ $st['bg'] }}; color:{{ $st['color'] }}; border:1px solid {{ $st['border'] }};">
                                                {{ $payroll->status === 'approved' ? 'For Release' : ucfirst($payroll->status) }}
                                            </span>
                                            @if ($payroll->payment_date)
                                                <br><small
                                                    class="text-muted">{{ $payroll->payment_date->format('M d, Y') }}</small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="{{ route('payroll.salary-computation.show', $payroll) }}"
                                                    class="emp-action-btn emp-action-view" title="View">
                                                    <i class="feather-eye"></i>
                                                </a>
                                                @if ($payroll->status === 'approved')
                                                    <button type="button" class="emp-action-btn emp-action-pay"
                                                        title="Mark as Paid"
                                                        onclick="openPayModal({{ $payroll->id }}, '{{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}', '{{ number_format($payroll->net_pay, 2) }}')">
                                                        <i class="feather-check-circle"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-5">
                                            <i class="feather-inbox d-block mb-2" style="font-size:28px; opacity:.3;"></i>
                                            No payroll receivables found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

            </form>
            {{-- Batch action bar --}}
            @if ($status === 'approved' || $status === 'all')
                <div id="batch-bar" class="d-none px-3 py-2 d-flex align-items-center gap-3"
                    style="background:#f0f9ff; border-top:1px solid #bae6fd;">
                    <span class="small text-muted"><span id="selected-count">0</span> selected</span>
                    <input type="date" name="payment_date" class="form-control form-control-sm"
                        style="max-width:160px;" required>
                    <select name="payment_method" class="form-control form-control-sm" style="max-width:150px;" required>
                        <option value="">Payment Method</option>
                        <option value="cash">Cash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="check">Check</option>
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="feather-check me-1"></i> Mark Selected as Paid
                    </button>
                </div>
            @endif

            <div class="d-flex justify-content-between align-items-center px-3 py-2">
                <div class="text-muted" style="font-size:0.875rem;">
                    Showing {{ $payrolls->firstItem() ?? 0 }} to {{ $payrolls->lastItem() ?? 0 }}
                    of {{ $payrolls->total() }} entries
                </div>
                {{ $payrolls->links('pagination::bootstrap-5') }}
            </div>
        </div>

    </div>
    </div>

    {{-- Mark as Paid Modal --}}
    <div class="modal fade" id="payModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Mark as Paid</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="pay-form" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p class="small text-muted mb-3">
                            Releasing payment for <strong id="modal-employee-name"></strong>
                            — Net Pay: <strong id="modal-net-pay"></strong>
                        </p>
                        <div class="mb-3">
                            <label class="form-label">Payment Date</label>
                            <input type="date" name="payment_date" class="form-control form-control-sm"
                                value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <select name="payment_method" class="form-control form-control-sm" required>
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="check">Check</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="feather-check me-1"></i> Confirm Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Move modal to body to escape any stacking context
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('payModal');
            if (modal) document.body.appendChild(modal);
        });

        function openPayModal(payrollId, employeeName, netPay) {
            document.getElementById('modal-employee-name').textContent = employeeName;
            document.getElementById('modal-net-pay').textContent = '₱' + netPay;
            document.getElementById('pay-form').action = `/payroll/receivables/${payrollId}/mark-paid`;
            new bootstrap.Modal(document.getElementById('payModal')).show();
        }

        document.getElementById('select-all')?.addEventListener('change', function() {
            document.querySelectorAll('.payroll-check').forEach(cb => cb.checked = this.checked);
            updateBatchBar();
        });

        document.querySelectorAll('.payroll-check').forEach(cb => {
            cb.addEventListener('change', updateBatchBar);
        });

        function updateBatchBar() {
            const checked = document.querySelectorAll('.payroll-check:checked').length;
            const bar = document.getElementById('batch-bar');
            document.getElementById('selected-count').textContent = checked;
            if (checked > 0) {
                bar.classList.remove('d-none');
                bar.classList.add('d-flex');
            } else {
                bar.classList.add('d-none');
                bar.classList.remove('d-flex');
            }
        }
    </script>
@endsection

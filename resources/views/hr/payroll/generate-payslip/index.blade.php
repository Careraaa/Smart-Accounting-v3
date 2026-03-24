@extends('layouts.layout')

@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="card-title mb-0">Generate Payslips</span>
            </div>

            <div class="card-body">

                {{-- Filters --}}
                <form method="GET" action="{{ route('payroll.generate-payslip.index') }}" class="d-flex gap-2 flex-wrap mb-3"
                    id="filterForm">

                    {{-- Employee: text input + hidden user_id + datalist --}}
                    <div style="flex:1; min-width:200px; position:relative;">
                        {{-- Visible search input --}}
                        <input type="text" id="employeeSearch" class="form-control form-control-sm"
                            placeholder="Search employee..." autocomplete="off" list="employeeList"
                            value="{{ request('user_id') ? $employees->firstWhere('id', request('user_id'))?->name : '' }}">
                        {{-- Hidden field that actually submits the user_id --}}
                        <input type="hidden" name="user_id" id="employeeId" value="{{ request('user_id') }}">

                        {{-- Datalist for native browser suggestions --}}
                        <datalist id="employeeList">
                            @foreach ($employees as $emp)
                                <option data-id="{{ $emp->id }}"
                                    value="{{ $emp->name }}{{ $emp->position ? ' — ' . $emp->position : '' }}">
                            @endforeach
                        </datalist>
                    </div>

                    {{-- Period Start --}}
                    <input type="date" name="period_start" class="form-control form-control-sm"
                        value="{{ request('period_start') }}" style="max-width:160px;">

                    {{-- Period End --}}
                    <input type="date" name="period_end" class="form-control form-control-sm"
                        value="{{ request('period_end') }}" style="max-width:160px;">

                    {{-- Status --}}
                    <select name="status" class="form-control form-control-sm" style="max-width:130px;">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>

                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                        <i class="feather-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('payroll.generate-payslip.index') }}" class="btn btn-sm btn-outline-danger">
                        <i class="feather-x me-1"></i> Clear
                    </a>
                </form>

            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover w-100 mb-0">
                        <thead>
                            <tr>
                                <th class="sortable-header">
                                    <div class="sort-link">Employee</div>
                                </th>
                                <th class="sortable-header">
                                    <div class="sort-link">Position / Dept.</div>
                                </th>
                                <th class="sortable-header">
                                    <div class="sort-link">Pay Period</div>
                                </th>
                                <th class="sortable-header">
                                    <div class="sort-link">Basic Salary</div>
                                </th>
                                <th class="sortable-header">
                                    <div class="sort-link">Gross Pay</div>
                                </th>
                                <th class="sortable-header">
                                    <div class="sort-link">Net Pay</div>
                                </th>
                                <th class="sortable-header">
                                    <div class="sort-link">Status</div>
                                </th>
                                <th class="sortable-header text-center">
                                    <div class="sort-link justify-content-center">Action</div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($payrolls as $payroll)
                                <tr>
                                    <td>
                                        <strong>
                                            {{ $payroll->user->first_name ?? ($payroll->user->name ?? 'N/A') }}
                                            {{ $payroll->user->last_name ?? '' }}
                                        </strong>
                                        <br>
                                        <small class="text-muted">ID
                                            #{{ str_pad($payroll->user_id, 4, '0', STR_PAD_LEFT) }}</small>
                                    </td>
                                    <td>
                                        {{ $payroll->user->position ?? '—' }}<br>
                                        <small class="text-muted">{{ $payroll->user->department ?? '—' }}</small>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $payroll->payroll_period_start->format('M d') }} –
                                            {{ $payroll->payroll_period_end->format('M d, Y') }}
                                        </small><br>
                                        <small class="text-muted">{{ $payroll->days_worked ?? 0 }} days &bull;
                                            {{ number_format($payroll->hours_worked ?? 0, 1) }} hrs</small>
                                    </td>
                                    <td>₱{{ number_format($payroll->basic_salary, 2) }}</td>
                                    <td><strong
                                            style="color:#16a34a;">₱{{ number_format($payroll->gross_pay, 2) }}</strong>
                                    </td>
                                    <td><strong>₱{{ number_format($payroll->net_pay, 2) }}</strong></td>
                                    <td>
                                        @php
                                            $st = match ($payroll->status) {
                                                'approved' => [
                                                    'bg' => '#f0fdf4',
                                                    'color' => '#16a34a',
                                                    'border' => '#bbf7d0',
                                                ],
                                                'paid' => [
                                                    'bg' => '#eff6ff',
                                                    'color' => '#1d4ed8',
                                                    'border' => '#bfdbfe',
                                                ],
                                                default => [
                                                    'bg' => '#fffbeb',
                                                    'color' => '#d97706',
                                                    'border' => '#fde68a',
                                                ],
                                            };
                                        @endphp
                                        <span class="emp-badge"
                                            style="background:{{ $st['bg'] }};color:{{ $st['color'] }};border:1px solid {{ $st['border'] }};">
                                            {{ ucfirst($payroll->status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('payroll.generatePayslip', $payroll) }}"
                                            class="emp-action-btn emp-action-view" title="View Payslip" target="_blank">
                                            <i class="feather-file-text"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-5">
                                        <i class="feather-file-text d-block mb-2" style="font-size:28px; opacity:.3;"></i>
                                        No payroll records found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($payrolls->hasPages())
                    <div class="d-flex justify-content-between align-items-center px-3 py-2 flex-wrap gap-2">
                        <div class="small text-muted">
                            Showing <strong>{{ $payrolls->firstItem() }}</strong> to
                            <strong>{{ $payrolls->lastItem() }}</strong> of
                            <strong>{{ $payrolls->total() }}</strong> entries
                        </div>
                        <div>
                            {{ $payrolls->withQueryString()->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('employeeSearch');
            const hiddenId = document.getElementById('employeeId');
            const datalist = document.getElementById('employeeList');
            const form = document.getElementById('filterForm');

            // Map display text → employee id from the datalist options
            const optionMap = {};
            Array.from(datalist.options).forEach(function(opt) {
                optionMap[opt.value] = opt.getAttribute('data-id');
            });

            // When user picks from datalist, set the hidden user_id
            searchInput.addEventListener('change', function() {
                const matched = optionMap[this.value];
                hiddenId.value = matched ?? '';
            });

            // If user clears the input, clear the hidden id too
            searchInput.addEventListener('input', function() {
                if (this.value === '') {
                    hiddenId.value = '';
                }
            });

            // On form submit, if text doesn't match any option exactly, clear user_id
            // so it falls back to "all employees" rather than filtering by stale id
            form.addEventListener('submit', function() {
                if (!optionMap[searchInput.value]) {
                    hiddenId.value = '';
                }
            });
        });
    </script>
@endsection

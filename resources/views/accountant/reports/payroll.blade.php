@extends('layouts.layout')

@push('styles')
    @include('accountant._ui-styles')
@endpush

@section('content')
@php
    $sumGross = collect($batchData)->sum('total_gross');
    $sumDed = collect($batchData)->sum('total_deductions');
    $sumNet = collect($batchData)->sum('total_net');
@endphp

<div class="col-12">
    <div class="remui-page acd-page">
        <div class="remui-backdrop">
            <div class="remui-grid"></div>
        </div>

        <div class="remui-hero mb-3">
            <div>
                <h5 class="remui-title">Payroll reports</h5>
                <p class="remui-subtitle mb-0">Approved payroll batches for the selected period (filters match the print view).</p>
            </div>
            <a href="{{ route('reports.print.payroll-report', request()->query()) }}" class="emp-action-btn emp-action-edit" target="_blank" rel="noopener">
                <i class="feather-printer"></i><span>Print</span>
            </a>
        </div>

        <form class="acd-filter-bar" method="get" action="{{ route('reports.payroll') }}" id="acct-payroll-report-filters">
            <div>
                <label class="form-label">Period</label>
                <select name="period" class="form-select form-select-sm" id="period" onchange="this.form.submit()">
                    <option value="weekly" {{ $period === 'weekly' ? 'selected' : '' }}>Weekly</option>
                    <option value="monthly" {{ $period === 'monthly' ? 'selected' : '' }}>Monthly</option>
                    <option value="yearly" {{ $period === 'yearly' ? 'selected' : '' }}>Yearly</option>
                </select>
            </div>
            <div id="weekSelectWrap" style="display: {{ $period === 'weekly' ? 'block' : 'none' }};">
                <label class="form-label">Week</label>
                <select name="week" class="form-select form-select-sm" onchange="this.form.submit()">
                    @for ($i = 1; $i <= 52; $i++)
                        <option value="{{ $i }}" {{ (int) $week === $i ? 'selected' : '' }}>Week {{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div id="monthSelectWrap" style="display: {{ $period === 'monthly' ? 'block' : 'none' }};">
                <label class="form-label">Month</label>
                <select name="month" class="form-select form-select-sm" onchange="this.form.submit()">
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ (int) $month === $i ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                        </option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="form-label">Year</label>
                <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                    @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                        <option value="{{ $i }}" {{ (int) $year === $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
        </form>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body">
                        <span class="dash-label">Batches</span>
                        <div class="dash-value mt-2">{{ count($batchData) }}</div>
                        <div class="dash-sub">In range</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body">
                        <span class="dash-label">Total gross</span>
                        <div class="dash-value mt-2">₱{{ number_format($sumGross, 2) }}</div>
                        <div class="dash-sub">Summed</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body">
                        <span class="dash-label">Total deductions</span>
                        <div class="dash-value mt-2">₱{{ number_format($sumDed, 2) }}</div>
                        <div class="dash-sub">Summed</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body">
                        <span class="dash-label">Total net</span>
                        <div class="dash-value mt-2">₱{{ number_format($sumNet, 2) }}</div>
                        <div class="dash-sub">Net pay</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card remui-card">
            <div class="card-header">
                <span class="card-title mb-0">Batch summary</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover w-100 mb-0 remui-table">
                        <thead>
                            <tr>
                                <th>Period</th>
                                <th class="text-end">Employees</th>
                                <th class="text-end">Gross</th>
                                <th class="text-end">Deductions</th>
                                <th class="text-end">Net pay</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($batchData as $batch)
                                <tr>
                                    <td>
                                        {{ \Carbon\Carbon::parse($batch['period_start'])->format('M d') }}
                                        –
                                        {{ \Carbon\Carbon::parse($batch['period_end'])->format('M d, Y') }}
                                    </td>
                                    <td class="text-end">{{ $batch['count'] }}</td>
                                    <td class="text-end font-monospace">₱{{ number_format($batch['total_gross'], 2) }}</td>
                                    <td class="text-end font-monospace text-muted">₱{{ number_format($batch['total_deductions'], 2) }}</td>
                                    <td class="text-end font-monospace fw-bold" style="color:#15803d;">₱{{ number_format($batch['total_net'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        <i class="feather-file-text d-block mb-2" style="font-size:28px;opacity:.3;"></i>
                                        No payroll batches for this filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        var period = document.getElementById('period');
        if (!period) return;
        function toggleFilters() {
            var p = period.value;
            var w = document.getElementById('weekSelectWrap');
            var m = document.getElementById('monthSelectWrap');
            if (w) w.style.display = p === 'weekly' ? 'block' : 'none';
            if (m) m.style.display = p === 'monthly' ? 'block' : 'none';
        }
        period.addEventListener('change', toggleFilters);
        toggleFilters();
    })();
</script>
@endsection

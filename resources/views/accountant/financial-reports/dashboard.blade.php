@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    {{-- Key Metrics Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-xxl-3 col-lg-4 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="dash-label">Total Revenue</span>
                        <span class="dash-icon di-green"><i class="feather-arrow-up-circle"></i></span>
                    </div>
                    <div class="dash-value">₱{{ number_format($totalRevenue, 2) }}</div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-lg-4 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="dash-label">Total Expenses</span>
                        <span class="dash-icon di-red"><i class="feather-minus-circle"></i></span>
                    </div>
                    <div class="dash-value">₱{{ number_format($totalExpenses, 2) }}</div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-lg-4 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="dash-label">Net Income</span>
                        <span class="dash-icon di-blue"><i class="feather-trending-up"></i></span>
                    </div>
                    <div class="dash-value text-{{ ($totalRevenue - $totalExpenses) >= 0 ? 'success' : 'danger' }}">
                        ₱{{ number_format($totalRevenue - $totalExpenses, 2) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-lg-4 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="dash-label">Profit Margin</span>
                        <span class="dash-icon di-amber"><i class="feather-percent"></i></span>
                    </div>
                    <div class="dash-value">
                        {{ $totalRevenue > 0 ? number_format((($totalRevenue - $totalExpenses) / $totalRevenue) * 100, 2) : 0 }}%
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Financial Reports Links --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="mb-3">
                        <i class="feather-bar-chart-2" style="font-size: 2rem;"></i>
                    </h5>
                    <h6>Income Statement</h6>
                    <p class="text-muted mb-3">Revenue, expenses, and net income</p>
                    <a href="{{ route('accountant.reports.income-statement') }}" class="btn btn-sm btn-primary">
                        View Report
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="mb-3">
                        <i class="feather-layers" style="font-size: 2rem;"></i>
                    </h5>
                    <h6>Balance Sheet</h6>
                    <p class="text-muted mb-3">Assets, liabilities, and equity</p>
                    <a href="{{ route('accountant.reports.balance-sheet') }}" class="btn btn-sm btn-primary">
                        View Report
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="mb-3">
                        <i class="feather-list" style="font-size: 2rem;"></i>
                    </h5>
                    <h6>Trial Balance</h6>
                    <p class="text-muted mb-3">GL account balances verification</p>
                    <a href="{{ route('accountant.gl.trial-balance') }}" class="btn btn-sm btn-primary">
                        View Report
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Period Selector --}}
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Report Period</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">Period</label>
                    <select id="periodSelect" class="form-select" onchange="changePeriod()">
                        <option value="">Select Period</option>
                        @foreach(\App\Models\AccountingPeriod::orderBy('period_start', 'desc')->take(12)->get() as $period)
                            <option value="{{ $period->id }}">{{ $period->period_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">&nbsp;</label>
                    <button class="btn btn-primary w-100" onclick="generateReport()">
                        <i class="feather-refresh-cw"></i> Generate Report
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function changePeriod() {
        // Implement period change logic
    }

    function generateReport() {
        // Implement report generation logic
    }
</script>
@endpush
@endsection

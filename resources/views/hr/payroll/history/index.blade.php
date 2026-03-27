@extends('layouts.layout')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <span class="card-title mb-0">Payroll History</span>
                        <p class="text-muted small mt-1 mb-0">View all payroll batch releases and historical data</p>
                    </div>
                    <a href="{{ route('reports.print.pao-report') }}" class="btn btn-sm btn-primary" target="_blank">
                        <i class="feather-printer me-1"></i> Export to PDF
                    </a>
                </div>
                <div class="card-body">

                    {{-- Statistics Cards --}}
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card card-statistic">
                                <div class="card-body">
                                    <div class="stat-label">Total Batches</div>
                                    <h3 class="mb-1" style="color: #8B3A62;">{{ $totalBatches }}</h3>
                                    <small class="text-muted">This year</small>
                                </div>
                                <div class="card-icon" style="color: #8B3A62; opacity: 0.2;">
                                    <i class="feather-layers" style="font-size: 2.5rem;"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-statistic">
                                <div class="card-body">
                                    <div class="stat-label">Total Amount</div>
                                    <h3 class="mb-1" style="color: #B8860B;">₱{{ number_format($totalPayroll, 1) }}M</h3>
                                    <small class="text-muted">All-time payroll total</small>
                                </div>
                                <div class="card-icon" style="color: #B8860B; opacity: 0.2;">
                                    <i class="feather-dollar-sign" style="font-size: 2.5rem;"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-statistic">
                                <div class="card-body">
                                    <div class="stat-label">Avg Per Batch</div>
                                    <h3 class="mb-1" style="color: #8B3A62;">₱{{ number_format($totalBatches > 0 ? $totalPayroll / $totalBatches : 0, 0) }}K</h3>
                                    <small class="text-muted">Average amount</small>
                                </div>
                                <div class="card-icon" style="color: #8B3A62; opacity: 0.2;">
                                    <i class="feather-trending-up" style="font-size: 2.5rem;"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-statistic">
                                <div class="card-body">
                                    <div class="stat-label">Active Staff</div>
                                    <h3 class="mb-1" style="color: #B8860B;">{{ $totalPaid }}</h3>
                                    <small class="text-muted">Paid employees</small>
                                </div>
                                <div class="card-icon" style="color: #B8860B; opacity: 0.2;">
                                    <i class="feather-users" style="font-size: 2.5rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Filters --}}
                    <div class="row mb-4" style="align-items: flex-end;">
                        <div class="col-md-9">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label for="filterMonth" class="form-label small">Month</label>
                                    <select name="filter_month" id="filterMonth" class="form-select form-select-sm" onchange="applyFilters()">
                                        <option value="">All Months</option>
                                        @for ($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}" {{ $filterMonth == $i ? 'selected' : '' }}>
                                                {{ \Carbon\Carbon::createFromDate(null, $i)->format('F') }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="filterYear" class="form-label small">Year</label>
                                    <select name="filter_year" id="filterYear" class="form-select form-select-sm" onchange="applyFilters()">
                                        <option value="">All Years</option>
                                        @for ($i = 2020; $i <= now()->year; $i++)
                                            <option value="{{ $i }}" {{ $filterYear == $i ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Payroll Batch History --}}
                    <h5 class="mb-2">Payroll Batch History</h5>
                    <p class="text-muted small mb-4">View all payroll batch releases</p>

                    <div class="row">
                        @forelse($batchData as $batch)
                            @php
                                $startDate = $batch['period_start'];
                                $endDate = $batch['period_end'];
                                $isFirst = $startDate->format('d') <= 15 ? true : false;
                                
                                if ($isFirst) {
                                    $iconClass = 'feather-calendar';
                                    $iconColor = '#8B3A62';
                                    $bgColor = '#f5e6f0';
                                } else {
                                    $iconClass = 'feather-check-circle';
                                    $iconColor = '#B8860B';
                                    $bgColor = '#f9f3eb';
                                }
                            @endphp
                            <div class="col-12 mb-3">
                                <a href="{{ route('payroll.history.batch', ['start' => $startDate->format('Y-m-d'), 'end' => $endDate->format('Y-m-d')]) }}" style="text-decoration: none;">
                                    <div class="card" style="background-color: {{ $bgColor }}; border: none; border-radius: 8px; cursor: pointer; transition: all 0.3s ease;">
                                        <div class="card-body p-4">
                                            <div style="display: flex; align-items: center; gap: 20px;">
                                                <!-- Icon Badge -->
                                                <div style="flex-shrink: 0;">
                                                    <div style="width: 50px; height: 50px; background-color: {{ $iconColor }}; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                                        <i class="feather {{ $iconClass }}" style="color: white; font-size: 1.5rem;"></i>
                                                    </div>
                                                </div>

                                                <!-- Batch Info -->
                                                <div style="flex: 1; min-width: 0;">
                                                    <h6 class="mb-1" style="color: {{ $iconColor }}; font-weight: 700; margin: 0;">
                                                        {{ $startDate->format('F Y') }} - Batch {{ $isFirst ? '1' : '2' }}
                                                    </h6>
                                                    <small style="color: {{ $iconColor }}; opacity: 0.8;">
                                                        Released on {{ $endDate->format('F d, Y') }}
                                                    </small>
                                                </div>

                                                <!-- Stats -->
                                                <div style="display: flex; gap: 40px; align-items: center;">
                                                    <div style="text-align: center;">
                                                        <small style="color: {{ $iconColor }}; opacity: 0.8; display: block; font-size: 0.8rem;">Employees</small>
                                                        <strong style="color: {{ $iconColor }}; font-size: 1.1rem; display: block;">{{ $batch['count'] }}</strong>
                                                    </div>
                                                    <div style="text-align: center;">
                                                        <small style="color: {{ $iconColor }}; opacity: 0.8; display: block; font-size: 0.8rem;">Gross Amount</small>
                                                        <strong style="color: {{ $iconColor }}; font-size: 0.95rem; display: block;">₱{{ number_format($batch['total_amount'] * 1.1, 2) }}</strong>
                                                    </div>
                                                    <div style="text-align: center;">
                                                        <small style="color: {{ $iconColor }}; opacity: 0.8; display: block; font-size: 0.8rem;">Deductions</small>
                                                        <strong style="color: {{ $iconColor }}; font-size: 0.95rem; display: block;">₱{{ number_format($batch['total_amount'] * 0.15, 2) }}</strong>
                                                    </div>
                                                    <div style="text-align: center;">
                                                        <small style="color: {{ $iconColor }}; opacity: 0.8; display: block; font-size: 0.8rem;">Net Pay</small>
                                                        <strong style="color: {{ $iconColor }}; font-size: 0.95rem; display: block;">₱{{ number_format($batch['total_amount'], 2) }}</strong>
                                                    </div>
                                                </div>
                                        </div>
                                    </div>
                                </div>
                                </a>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info" role="alert">
                                    <i class="feather-info"></i> No payroll batches found for the selected period.
                                </div>
                            </div>
                        @endforelse
                    </div>

                    {{-- Pagination Info --}}
                    @if($batchData)
                        <div class="mt-4 text-center text-muted small">
                            Showing {{ count($batchData) }} batch(es)
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <script>
        function applyFilters() {
            const month = document.getElementById('filterMonth').value;
            const year = document.getElementById('filterYear').value;
            const params = new URLSearchParams();
            if (month) params.append('filter_month', month);
            if (year) params.append('filter_year', year);
            window.location.href = '{{ route("payroll.history.index") }}' + (params.toString() ? '?' + params.toString() : '');
        }
    </script>

    <style>
        .card-statistic {
            position: relative;
            overflow: hidden;
        }

        .card-statistic .card-body {
            padding: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .card-statistic .card-icon {
            position: absolute;
            right: 15px;
            top: 15px;
            z-index: 0;
        }

        .stat-label {
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.8;
            margin-bottom: 8px;
        }

        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        a[href*="batch"] > .card {
            cursor: pointer;
        }

        a[href*="batch"] > .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.2) !important;
        }
    </style>
@endsection

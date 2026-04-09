@extends('layouts.layout')

@push('styles')
    @include('accountant._ui-styles')
@endpush

@section('content')
@php
    $initials = function ($name) {
        $name = trim((string) $name);
        if ($name === '') {
            return '?';
        }
        $p = preg_split('/\s+/', $name);
        $a = strtoupper(substr($p[0] ?? '', 0, 1));
        $b = strtoupper(substr($p[1] ?? '', 0, 1));
        return $b !== '' ? $a . $b : $a;
    };
    $pillClass = function ($status) {
        return match ($status) {
            'paid' => 'acd-pill-paid',
            'approved' => 'acd-pill-approved',
            'rejected' => 'acd-pill-rejected',
            default => 'acd-pill-pending',
        };
    };
@endphp

<div class="col-12">
    <div class="remui-page acd-page">
        <div class="remui-backdrop">
            <div class="remui-grid"></div>
        </div>

        <div class="remui-hero mb-3">
            <div>
                <h5 class="remui-title">Accountant dashboard</h5>
                <p class="remui-subtitle mb-0">Payroll totals, allowances vs deductions, and pipeline status — charts use live
                    <strong style="color:#e5e7eb;">payrolls</strong> data.</p>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    <span class="remui-hero-chip"><i class="feather-calendar"></i> {{ now()->format('l, F d, Y') }}</span>
                    <span class="remui-hero-chip"><i class="feather-users"></i> {{ $totalEmployees }} employees</span>
                    <span class="remui-hero-chip"><i class="feather-percent"></i> {{ number_format($allowancePercentage, 1) }}% allowances / {{ number_format($deductionPercentage, 1) }}% deductions</span>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('payroll-approval.index') }}" class="emp-action-btn emp-action-edit">
                    <i class="feather-check-square"></i><span>Payroll approval</span>
                </a>
                <a href="{{ route('payroll.salary-computation.index') }}" class="emp-action-btn emp-action-view">
                    <i class="feather-calculator"></i><span>Salary computation</span>
                </a>
                <a href="{{ route('reports.payroll') }}" class="emp-action-btn emp-action-view">
                    <i class="feather-file-text"></i><span>Payroll reports</span>
                </a>
            </div>
        </div>

        {{-- Top stats — remittance / salary index style --}}
        <div class="row g-3 mb-4">
            <div class="col-xxl-2 col-lg-4 col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="dash-label">Total net payroll</span>
                            <span class="dash-icon di-green"><i class="feather-briefcase"></i></span>
                        </div>
                        <div class="dash-value">₱{{ number_format($totalPayroll, 0) }}</div>
                        <div class="dash-sub">Sum of net pay (all records)</div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-2 col-lg-4 col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="dash-label">Allowances</span>
                            <span class="dash-icon di-green"><i class="feather-plus-circle"></i></span>
                        </div>
                        <div class="dash-value">₱{{ number_format($totalAllowances, 0) }}</div>
                        <div class="dash-sub">{{ number_format($allowancePercentage, 1) }}% of A+D</div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-2 col-lg-4 col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="dash-label">Deductions</span>
                            <span class="dash-icon di-red"><i class="feather-minus-circle"></i></span>
                        </div>
                        <div class="dash-value">₱{{ number_format($totalDeductions, 0) }}</div>
                        <div class="dash-sub">{{ number_format($deductionPercentage, 1) }}% of A+D</div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-2 col-lg-4 col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="dash-label">Avg. basic</span>
                            <span class="dash-icon di-blue"><i class="feather-trending-up"></i></span>
                        </div>
                        <div class="dash-value">₱{{ number_format($averageBasicSalary ?? 0, 0) }}</div>
                        <div class="dash-sub">Across payroll rows</div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-2 col-lg-4 col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="dash-label">Outstanding loans</span>
                            <span class="dash-icon di-amber"><i class="feather-alert-circle"></i></span>
                        </div>
                        <div class="dash-value">₱{{ number_format($totalOutstandingLoans, 0) }}</div>
                        <div class="dash-sub">{{ $activeSalaryLoans }} active loan(s)</div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-2 col-lg-4 col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="dash-label">Headcount</span>
                            <span class="dash-icon"><i class="feather-users"></i></span>
                        </div>
                        <div class="dash-value">{{ $totalEmployees }}</div>
                        <div class="dash-sub">Active directory (excl. admin roles)</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-xxl-3 col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <span class="dash-label">Processing</span>
                        <div class="dash-value mt-3">{{ $processingPayroll }}</div>
                        <div class="progress dash-progress mt-3">
                            <div class="progress-bar bg-warning" role="progressbar"
                                style="width: {{ $totalEmployees > 0 ? min(100, ($processingPayroll / max(1, $totalEmployees)) * 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <span class="dash-label">Approved</span>
                        <div class="dash-value mt-3">{{ $approvedPayroll }}</div>
                        <div class="progress dash-progress mt-3">
                            <div class="progress-bar bg-info" role="progressbar"
                                style="width: {{ $totalEmployees > 0 ? min(100, ($approvedPayroll / max(1, $totalEmployees)) * 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <span class="dash-label">Paid</span>
                        <div class="dash-value mt-3">{{ $paidPayroll }}</div>
                        <div class="progress dash-progress mt-3">
                            <div class="progress-bar bg-success" role="progressbar"
                                style="width: {{ $totalEmployees > 0 ? min(100, ($paidPayroll / max(1, $totalEmployees)) * 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <span class="dash-label">Rejected</span>
                        <div class="dash-value mt-3">{{ $rejectedPayroll }}</div>
                        <div class="dash-sub mt-1">
                            <a href="{{ route('payroll-approval.index') }}" style="font-size:.8rem; color:#c8292a; font-weight:600; text-decoration:none;">Open approval →</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts — HR-style panels --}}
        <div class="acd-charts">
            <div class="acd-panel">
                <div class="acd-panel-hd">
                    <h2>Net payroll trend</h2>
                    <span>Last 6 months · net pay by record <code style="font-family:'DM Mono',monospace;font-size:.68rem;background:#f8fafc;padding:2px 6px;border-radius:4px;">created_at</code></span>
                </div>
                <div class="acd-panel-bd">
                    <div id="acd-chart-trend" class="acd-chart"></div>
                </div>
            </div>
            <div class="acd-panel">
                <div class="acd-panel-hd">
                    <h2>Payroll status mix</h2>
                    <span>Counts by <code style="font-family:'DM Mono',monospace;font-size:.68rem;background:#f8fafc;padding:2px 6px;border-radius:4px;">status</code></span>
                </div>
                <div class="acd-panel-bd">
                    <div id="acd-chart-status" class="acd-chart" style="min-height:280px;"></div>
                </div>
            </div>
        </div>

        <div class="acd-charts2">
            <div class="acd-panel">
                <div class="acd-panel-hd">
                    <h2>Allowances vs deductions</h2>
                    <span>Aggregate peso amounts</span>
                </div>
                <div class="acd-panel-bd">
                    <div id="acd-chart-ad" class="acd-chart"></div>
                </div>
            </div>
            <div class="acd-panel">
                <div class="acd-panel-hd">
                    <h2>Pipeline</h2>
                    <span>Awaiting · approved · paid · rejected</span>
                </div>
                <div class="acd-panel-bd">
                    <div id="acd-chart-pipeline" class="acd-chart"></div>
                </div>
            </div>
        </div>

        {{-- Recent payroll — feed --}}
        <div class="acd-panel mb-2">
            <div class="acd-panel-hd">
                <h2>Recent payroll records</h2>
                <a href="{{ route('payroll.salary-computation.index') }}" style="font-size:.78rem;font-weight:700;color:var(--acd-red);text-decoration:none;">Payroll management →</a>
            </div>
            @if ($recentPayroll->isEmpty())
                <div class="acd-empty"><i class="feather-inbox" style="font-size:32px;display:block;opacity:.35;"></i>No payroll records yet.</div>
            @else
                <ul class="acd-feed">
                    @foreach ($recentPayroll as $payroll)
                        @php
                            $u = $payroll->employee;
                            $name = $u
                                ? trim(($u->first_name ?? '') . ' ' . ($u->last_name ?? '')) ?: ($u->name ?? 'Unknown')
                                : 'Unknown';
                            $st = $payroll->status ?? 'pending';
                        @endphp
                        <li>
                            <a href="{{ route('payroll-approval.show', $payroll) }}">
                                <div class="acd-av">{{ $initials($name) }}</div>
                                <div class="acd-feed-body">
                                    <p class="acd-feed-title">{{ $name }}</p>
                                    <p class="acd-feed-meta">
                                        Net <code>₱{{ number_format($payroll->net_pay ?? 0, 2) }}</code>
                                        · Gross <code>₱{{ number_format($payroll->gross_pay ?? 0, 2) }}</code>
                                        · {{ $payroll->payroll_period_start?->format('M j') ?? '—' }} – {{ $payroll->payroll_period_end?->format('M j, Y') ?? '—' }}
                                    </p>
                                </div>
                                <div class="acd-feed-right">
                                    <span class="acd-pill {{ $pillClass($st) }}">{{ ucfirst($st) }}</span>
                                    <i class="feather-chevron-right acd-chevron"></i>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    if (typeof ApexCharts === 'undefined') return;

    const font = 'Sora, sans-serif';
    const mono = "'DM Mono', monospace";
    const red = '#c8292a';
    const ink = '#111827';

    const monthly = @json($monthlyPayrollTrend);
    const categories = monthly.map(function (m) { return m.label || m.month; });
    const trendVals = monthly.map(function (m) { return m.total; });

    new ApexCharts(document.querySelector('#acd-chart-trend'), {
        chart: {
            type: 'line',
            height: 280,
            fontFamily: font,
            toolbar: { show: false },
            zoom: { enabled: false },
            animations: { enabled: true, speed: 400 }
        },
        series: [{ name: 'Net pay', data: trendVals }],
        colors: [red],
        stroke: { width: 2, curve: 'smooth' },
        fill: {
            type: 'gradient',
            gradient: { shadeIntensity: 0.4, opacityFrom: 0.35, opacityTo: 0.02, stops: [0, 90, 100] }
        },
        markers: { size: 3, strokeWidth: 0, hover: { size: 5 } },
        dataLabels: { enabled: false },
        xaxis: {
            categories: categories,
            labels: { style: { colors: '#64748b', fontSize: '11px' } },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: {
                style: { colors: '#64748b', fontSize: '11px' },
                formatter: function (v) { return '₱' + (v >= 1e6 ? (v / 1e6).toFixed(1) + 'M' : (v >= 1e3 ? (v / 1e3).toFixed(0) + 'k' : v.toFixed(0))); }
            },
            min: 0
        },
        grid: {
            borderColor: '#f1f5f9',
            strokeDashArray: 4,
            xaxis: { lines: { show: false } },
            yaxis: { lines: { show: true } },
            padding: { top: 8, right: 12, bottom: 0, left: 8 }
        },
        tooltip: {
            y: { formatter: function (v) { return '₱' + Number(v).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }); } }
        }
    }).render();

    const stLabels = @json($payrollStatusChartLabels);
    const stSeries = @json($payrollStatusChartSeries);
    const stEl = document.querySelector('#acd-chart-status');
    if (stSeries.length && stEl) {
        new ApexCharts(stEl, {
            chart: { type: 'donut', height: 280, fontFamily: font },
            series: stSeries,
            labels: stLabels,
            colors: ['#f59e0b', '#8b5cf6', '#64748b', '#2563eb', '#22c55e', '#15803d', '#f43f5e'],
            stroke: { width: 1, colors: ['#fff'] },
            plotOptions: {
                pie: {
                    donut: {
                        size: '78%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Records',
                                fontSize: '10px',
                                color: '#94a3b8',
                                formatter: function () {
                                    return String(stSeries.reduce(function (a, b) { return a + b; }, 0));
                                }
                            }
                        }
                    }
                }
            },
            dataLabels: { enabled: false },
            legend: { position: 'bottom', fontSize: '11px', fontWeight: 600 }
        }).render();
    } else if (stEl) {
        stEl.innerHTML = '<div class="acd-empty" style="min-height:240px;display:flex;align-items:center;justify-content:center;">No status breakdown yet.</div>';
    }

    const adEl = document.querySelector('#acd-chart-ad');
    const ta = {{ (float) $totalAllowances }};
    const td = {{ (float) $totalDeductions }};
    if (adEl && (ta > 0 || td > 0)) {
        new ApexCharts(adEl, {
            chart: { type: 'donut', height: 260, fontFamily: font },
            series: [ta, td],
            labels: ['Allowances', 'Deductions'],
            colors: ['#22c55e', '#f43f5e'],
            stroke: { width: 1, colors: ['#fff'] },
            plotOptions: {
                pie: {
                    donut: {
                        size: '78%',
                        labels: {
                            show: true,
                            value: { fontSize: '16px', fontWeight: 700, color: ink, fontFamily: mono },
                            total: {
                                show: true,
                                label: 'A + D',
                                fontSize: '10px',
                                color: '#94a3b8',
                                formatter: function (w) {
                                    return '₱' + w.globals.seriesTotals.reduce(function (a, b) { return a + b; }, 0).toLocaleString(undefined, { maximumFractionDigits: 0 });
                                }
                            }
                        }
                    }
                }
            },
            dataLabels: { enabled: false },
            legend: { position: 'bottom', fontSize: '11px', fontWeight: 600 }
        }).render();
    } else if (adEl) {
        adEl.innerHTML = '<div class="acd-empty" style="min-height:240px;display:flex;align-items:center;justify-content:center;">No allowance/deduction totals yet.</div>';
    }

    const pipe = @json($pipelineBar);
    new ApexCharts(document.querySelector('#acd-chart-pipeline'), {
        chart: { type: 'bar', height: 260, fontFamily: font, toolbar: { show: false } },
        series: [{ name: 'Payrolls', data: pipe.values }],
        colors: [red],
        plotOptions: { bar: { borderRadius: 8, columnWidth: '42%' } },
        xaxis: {
            categories: pipe.labels,
            labels: { style: { fontSize: '11px', fontWeight: 600, colors: '#64748b' } },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: { style: { colors: '#94a3b8', fontSize: '11px' } },
            min: 0,
            tickAmount: 4,
            forceNiceScale: true
        },
        dataLabels: {
            enabled: true,
            offsetY: -18,
            style: { fontSize: '11px', fontWeight: 700, colors: ['#fff'], fontFamily: mono }
        },
        grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
        legend: { show: false },
        tooltip: { y: { formatter: function (v) { return v + ' record(s)'; } } }
    }).render();
})();
</script>
@endpush

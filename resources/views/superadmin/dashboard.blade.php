@extends('layouts.layout')

@push('styles')
@include('superadmin.partials.prl-theme')
@endpush

@section('content')
@php
    $initials = function ($name) {
        $name = trim((string) $name);
        if ($name === '') return '?';
        $p = preg_split('/\s+/', $name);
        $a = strtoupper(substr($p[0] ?? '', 0, 1));
        $b = strtoupper(substr($p[1] ?? '', 0, 1));
        return $b !== '' ? $a . $b : $a;
    };
@endphp
<div class="col-12 sa-dash">
    <div class="sa-dash-bg" aria-hidden="true"></div>
    <div class="sa-dash-inner prl-page">

        <div class="prl-topbar">
            <div>
                <h1 class="prl-topbar-title">Super Admin</h1>
                <p class="prl-topbar-sub">System-wide overview</p>
            </div>
            <div class="prl-topbar-actions">
                <a href="{{ route('superadmin.accounts.index') }}" class="prl-btn-sec">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Accounts
                </a>
                <a href="{{ route('configuration.index') }}" class="prl-btn-generate">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Configuration
                </a>
            </div>
        </div>

        <div class="prl-stats">
            <div class="prl-stat s-red">
                <div class="prl-stat-icon">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <div class="prl-stat-label">Workforce</div>
                    <div class="prl-stat-value">{{ $activeEmployees }}<span style="font-size:0.85rem;color:#9ca3af;font-weight:600;">/{{ $totalEmployees }}</span></div>
                    <div class="prl-stat-sub">{{ $inactiveEmployees }} inactive · {{ $onLeaveEmployees }} on leave</div>
                </div>
            </div>
            <div class="prl-stat s-amber">
                <div class="prl-stat-icon">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="prl-stat-label">Today</div>
                    <div class="prl-stat-value">{{ $presentToday + $lateToday }}</div>
                    <div class="prl-stat-sub">Absent {{ $absentToday }} · Late {{ $lateToday }} · {{ number_format($attendanceRate, 1) }}% rate</div>
                </div>
            </div>
            <div class="prl-stat s-green">
                <div class="prl-stat-icon">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="prl-stat-label">Net payroll (all)</div>
                    <div class="prl-stat-value" style="font-size:1.15rem;">₱{{ number_format($totalPayroll, 2) }}</div>
                    <div class="prl-stat-sub">Allowances ₱{{ number_format($totalAllowances, 2) }} · Deductions ₱{{ number_format($totalDeductions, 2) }}</div>
                </div>
            </div>
            <div class="prl-stat s-blue">
                <div class="prl-stat-icon">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="prl-stat-label">Payroll pipeline</div>
                    <div class="prl-stat-value">{{ $releasedPayroll }}</div>
                    <div class="prl-stat-sub">Released · {{ $approvedPayroll }} approved · {{ $processingPayroll }} processing</div>
                </div>
            </div>
        </div>

        <div class="prl-generate-card">
            <div class="prl-generate-left">
                <div class="prl-generate-eyebrow">Control plane</div>
                <h2 class="prl-generate-title">Manage users &amp; infrastructure</h2>
                <div class="prl-generate-period">{{ now()->format('l, F j, Y · g:i A') }}</div>
            </div>
            <div class="prl-generate-right">
                <a href="{{ route('superadmin.accounts.create') }}" class="prl-btn-generate" style="box-shadow:0 4px 18px rgba(200,41,42,0.45);">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    New account
                </a>
                <a href="{{ route('configuration.backup-history') }}" class="prl-btn-sec" style="background:rgba(255,255,255,0.08);border-color:rgba(255,255,255,0.2);color:#e5e7eb;">
                    Backup history
                </a>
            </div>
        </div>

        <div class="prl-two-col">
            <div class="prl-side-card">
                <div class="prl-side-head">
                    Attendance trend
                    <span>Last 7 days</span>
                </div>
                <div class="prl-chart-box" id="sa-chart-attendance"></div>
            </div>
            <div class="prl-side-card">
                <div class="prl-side-head">
                    Payroll volume
                    <span>Last 6 months (net)</span>
                </div>
                <div class="prl-chart-box" id="sa-chart-payroll"></div>
            </div>
        </div>

        <div class="prl-two-col">
            <div class="prl-side-card">
                <div class="prl-side-head">
                    Recent leave
                    <span>Latest 8</span>
                </div>
                @if($recentLeaves->isEmpty())
                    <p class="prl-empty-sub" style="padding:28px 18px;margin:0;">No leave records yet.</p>
                @else
                    <ul class="prl-feed">
                        @foreach($recentLeaves->take(8) as $leave)
                            <li>
                                <a href="{{ route('leave.show', $leave) }}">
                                    <div class="prl-feed-av">{{ $initials($leave->employee->name ?? '') }}</div>
                                    <div class="prl-feed-body">
                                        <p class="prl-feed-title">{{ $leave->employee->name ?? 'Employee' }}</p>
                                        <p class="prl-feed-meta">
                                            {{ $leave->start_date?->format('M j') ?? '—' }} → {{ $leave->end_date?->format('M j, Y') ?? '—' }}
                                            · {{ str_replace('_', ' ', $leave->leave_type ?? 'leave') }}
                                        </p>
                                    </div>
                                    <div class="prl-feed-right">
                                        <span class="prl-mini-pill">{{ ucfirst($leave->status ?? '—') }}</span>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="prl-side-card">
                <div class="prl-side-head">
                    Recent payroll
                    <span>Latest 8</span>
                </div>
                @if($recentPayroll->isEmpty())
                    <p class="prl-empty-sub" style="padding:28px 18px;margin:0;">No payroll records yet.</p>
                @else
                    <ul class="prl-feed">
                        @foreach($recentPayroll->take(8) as $pr)
                            <li>
                                <a href="{{ route('payroll.salary-computation.show', $pr) }}">
                                    <div class="prl-feed-av">{{ $initials(($pr->user->first_name ?? '').' '.($pr->user->last_name ?? '')) }}</div>
                                    <div class="prl-feed-body">
                                        <p class="prl-feed-title">{{ $pr->user->first_name ?? '' }} {{ $pr->user->last_name ?? '' }}</p>
                                        <p class="prl-feed-meta">
                                            Period <code>{{ $pr->payroll_period_start?->format('M j') ?? '—' }}</code> –
                                            <code>{{ $pr->payroll_period_end?->format('M j, Y') ?? '—' }}</code>
                                        </p>
                                    </div>
                                    <div class="prl-feed-right">
                                        <span class="prl-mini-pill">{{ ucfirst($pr->status ?? '—') }}</span>
                                        <div class="prl-mono" style="font-size:0.78rem;color:#111827;margin-top:4px;">₱{{ number_format($pr->net_pay ?? 0, 2) }}</div>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    if (typeof ApexCharts === 'undefined') return;

    const font = 'Sora, sans-serif';
    const red = '#c8292a';

    const trend = @json($attendanceTrend);
    const categories = trend.map(function (t) { return t.date; });

    const elA = document.querySelector('#sa-chart-attendance');
    if (elA) {
        new ApexCharts(elA, {
            chart: { type: 'area', height: 280, fontFamily: font, toolbar: { show: false }, zoom: { enabled: false }, animations: { enabled: true, speed: 450 } },
            series: [
                { name: 'Present', data: trend.map(function (t) { return t.present; }) },
                { name: 'Late', data: trend.map(function (t) { return t.late; }) },
                { name: 'Absent', data: trend.map(function (t) { return t.absent; }) }
            ],
            colors: [red, '#d97706', '#cbd5e1'],
            stroke: { width: 2, curve: 'smooth' },
            fill: { type: 'gradient', gradient: { shadeIntensity: 0.4, opacityFrom: 0.35, opacityTo: 0.02, stops: [0, 90, 100] } },
            dataLabels: { enabled: false },
            xaxis: { categories: categories, labels: { style: { colors: '#64748b', fontSize: '11px' } }, axisBorder: { show: false }, axisTicks: { show: false } },
            yaxis: { labels: { style: { colors: '#64748b', fontSize: '11px' } }, min: 0, tickAmount: 4, forceNiceScale: true },
            grid: { borderColor: '#f1f5f9', strokeDashArray: 4, padding: { top: 8, right: 12, bottom: 0, left: 8 } },
            legend: { position: 'top', horizontalAlign: 'right', fontSize: '11px', fontWeight: 600, itemMargin: { horizontal: 12 } },
            tooltip: { theme: 'light', y: { formatter: function (v) { return v + ' · day'; } } }
        }).render();
    }

    const monthly = @json($monthlyPayrollTrend);
    const elP = document.querySelector('#sa-chart-payroll');
    if (elP) {
        new ApexCharts(elP, {
            chart: { type: 'bar', height: 280, fontFamily: font, toolbar: { show: false } },
            series: [{ name: 'Net pay', data: monthly.map(function (m) { return m.total; }) }],
            colors: [red],
            plotOptions: { bar: { borderRadius: 8, columnWidth: '58%' } },
            dataLabels: { enabled: false },
            xaxis: { categories: monthly.map(function (m) { return m.month; }), labels: { style: { colors: '#64748b', fontSize: '11px' } }, axisBorder: { show: false } },
            yaxis: { labels: { style: { colors: '#64748b', fontSize: '11px' }, formatter: function (v) { return '₱' + (v >= 1000 ? (v / 1000).toFixed(1) + 'k' : v.toFixed(0)); } } },
            grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
            tooltip: { theme: 'light', y: { formatter: function (v) { return '₱' + v.toLocaleString(undefined, { maximumFractionDigits: 0 }); } } }
        }).render();
    }
})();
</script>
@endpush

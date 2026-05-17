@extends('layouts.layout')
@section('title', '13th Month Pay')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');

.tmp-page { font-family: 'Sora', sans-serif; }

.tmp-topbar { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; margin-bottom:24px; flex-wrap:wrap; }
.tmp-topbar-title { font-size:1.35rem; font-weight:800; color:#111827; letter-spacing:-0.02em; margin:0 0 2px; }
.tmp-topbar-sub { font-size:0.78rem; color:#9ca3af; margin:0; }

.tmp-btn-primary {
    display:inline-flex; align-items:center; gap:7px; padding:9px 16px; background:#111827; color:#fff;
    border:none; border-radius:10px; font-family:'Sora',sans-serif; font-size:0.82rem; font-weight:600;
    text-decoration:none; cursor:pointer; transition:background 0.15s; white-space:nowrap;
}
.tmp-btn-primary:hover { background:#000; color:#fff; }

.tmp-btn-sec {
    display:inline-flex; align-items:center; gap:7px; padding:9px 16px; background:#fff; color:#374151;
    border:1px solid #e5e7eb; border-radius:10px; font-family:'Sora',sans-serif; font-size:0.82rem;
    font-weight:600; text-decoration:none; cursor:pointer; transition:all 0.15s; white-space:nowrap;
}
.tmp-btn-sec:hover { border-color:#c8292a; color:#c8292a; background:#fff5f5; }

.tmp-flash { display:flex; align-items:center; gap:10px; padding:12px 16px; border-radius:10px; font-size:0.82rem; font-weight:500; margin-bottom:20px; }
.tmp-flash.success { background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d; }

.tmp-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; margin-bottom:24px; }
@media (max-width:900px) { .tmp-stats { grid-template-columns:1fr; } }
.tmp-stat { background:#fff; border:1px solid #e5e7eb; border-radius:14px; padding:18px 20px; }
.tmp-stat-label { font-size:0.67rem; font-weight:700; text-transform:uppercase; letter-spacing:0.09em; color:#9ca3af; margin-bottom:6px; }
.tmp-stat-value { font-size:1.35rem; font-weight:800; color:#111827; font-family:'DM Mono',monospace; font-variant-numeric:tabular-nums; }

.tmp-filter-bar {
    background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:12px 16px;
    display:flex; align-items:center; gap:10px; margin-bottom:16px; flex-wrap:wrap;
}
.tmp-filter-bar form { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.tmp-input {
    border:1px solid #e5e7eb; border-radius:8px; padding:8px 12px; font-size:0.82rem;
    font-family:'Sora',sans-serif; color:#111827; background:#f9fafb; outline:none;
}
.tmp-input:focus { border-color:#c8292a; background:#fff; box-shadow:0 0 0 3px rgba(200,41,42,0.08); }
.tmp-input.year { width:90px; }
.tmp-input.search { min-width:200px; flex:1; }

.tmp-table-card { background:#fff; border:1px solid #e5e7eb; border-radius:14px; overflow:hidden; }
.tmp-table-scroll { overflow-x:auto; }
.tmp-table { width:100%; border-collapse:collapse; font-size:0.835rem; min-width:760px; }
.tmp-table thead tr { background:#f8f9fb; border-bottom:1px solid #e5e7eb; }
.tmp-table thead th {
    padding:11px 16px; font-size:0.68rem; font-weight:700; text-transform:uppercase;
    letter-spacing:0.09em; color:#6b7280; white-space:nowrap; font-family:'Sora',sans-serif;
}
.tmp-table tbody tr { border-bottom:1px solid #f3f4f6; transition:background 0.1s; }
.tmp-table tbody tr:last-child { border-bottom:none; }
.tmp-table tbody tr.tmp-row-clickable { cursor:pointer; }
.tmp-table tbody tr.tmp-row-clickable:hover { background:#fafafa; }
.tmp-table tbody td { padding:12px 16px; color:#374151; vertical-align:middle; }
.tmp-table th.tmp-num, .tmp-table td.tmp-num { text-align:right; }
.tmp-mono { font-family:'DM Mono',monospace; font-variant-numeric:tabular-nums; }
.tmp-emp-name { font-weight:600; color:#111827; font-size:0.845rem; }

.tmp-badge { display:inline-flex; align-items:center; padding:3px 10px; border-radius:20px; font-size:0.68rem; font-weight:700; text-transform:uppercase; letter-spacing:0.04em; white-space:nowrap; }
.tmp-badge.pending { background:#fffbeb; color:#d97706; border:1px solid #fde68a; }
.tmp-badge.partial { background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe; }
.tmp-badge.paid { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }

.tmp-empty { padding:48px 24px; text-align:center; color:#9ca3af; font-size:0.85rem; }
.tmp-pagination { padding:16px 20px; border-top:1px solid #f3f4f6; }
</style>
@endpush

@section('content')
<div class="tmp-page">

    @if(session('success'))
    <div class="tmp-flash success">{{ session('success') }}</div>
    @endif

    <div class="tmp-topbar">
        <div>
            <h1 class="tmp-topbar-title">13th Month Pay</h1>
            <p class="tmp-topbar-sub">Total basic salary earned ÷ 12 · Calendar year {{ $calendarYear }}</p>
        </div>
        <a href="{{ route('bonuses.index') }}" class="tmp-btn-sec">Back to Bonuses</a>
    </div>

    <div class="tmp-stats">
        <div class="tmp-stat">
            <div class="tmp-stat-label">Employees</div>
            <div class="tmp-stat-value">{{ $stats['total_employees'] }}</div>
        </div>
        <div class="tmp-stat">
            <div class="tmp-stat-label">Total Payable</div>
            <div class="tmp-stat-value">₱{{ number_format($stats['total_payable'], 2) }}</div>
        </div>
        <div class="tmp-stat">
            <div class="tmp-stat-label">Total Paid</div>
            <div class="tmp-stat-value">₱{{ number_format($stats['total_paid'], 2) }}</div>
        </div>
    </div>

    <div class="tmp-filter-bar">
        <form method="GET" action="{{ route('payroll.thirteenth-month-pay.index') }}">
            <input type="number" name="year" value="{{ $calendarYear }}" class="tmp-input year" min="2000" max="2100" aria-label="Year">
            <input type="search" name="search" value="{{ $search }}" class="tmp-input search" placeholder="Search employee…">
            <button type="submit" class="tmp-btn-sec">Filter</button>
        </form>
        <form method="POST" action="{{ route('payroll.thirteenth-month-pay.compute') }}">
            @csrf
            <input type="hidden" name="year" value="{{ $calendarYear }}">
            <button type="submit" class="tmp-btn-primary">Compute All Employees</button>
        </form>
    </div>

    <div class="tmp-table-card">
        <div class="tmp-table-scroll">
            <table class="tmp-table">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th class="tmp-num">Basic Earned</th>
                        <th class="tmp-num">Months</th>
                        <th class="tmp-num">13th Month Pay</th>
                        <th class="tmp-num">Paid</th>
                        <th class="tmp-num">Remaining</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                    <tr class="tmp-row-clickable"
                        data-href="{{ route('payroll.thirteenth-month-pay.show', $record) }}"
                        tabindex="0"
                        role="link"
                        aria-label="View {{ $record->user?->name }}">
                        <td><span class="tmp-emp-name">{{ $record->user?->name }}</span></td>
                        <td class="tmp-num tmp-mono">₱{{ number_format($record->total_basic_salary_earned, 2) }}</td>
                        <td class="tmp-num">{{ number_format($record->months_worked, 2) }}</td>
                        <td class="tmp-num tmp-mono">₱{{ number_format($record->thirteenth_month_pay, 2) }}</td>
                        <td class="tmp-num tmp-mono">₱{{ number_format($record->amount_paid, 2) }}</td>
                        <td class="tmp-num tmp-mono">₱{{ number_format($record->amount_remaining, 2) }}</td>
                        <td><span class="tmp-badge {{ $record->status }}">{{ ucfirst($record->status) }}</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="tmp-empty">No records yet. Click Compute All Employees to generate.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($records->hasPages())
        <div class="tmp-pagination">{{ $records->links() }}</div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('tr.tmp-row-clickable').forEach(function (row) {
    row.addEventListener('click', function () {
        window.location.href = row.dataset.href;
    });
    row.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            window.location.href = row.dataset.href;
        }
    });
});
</script>
@endpush
@endsection

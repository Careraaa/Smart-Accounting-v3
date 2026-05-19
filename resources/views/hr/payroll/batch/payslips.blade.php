@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
.prl-page { font-family: 'Sora', sans-serif; }

.prl-topbar { display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;flex-wrap:wrap; }
.prl-topbar-title { font-size:1.35rem;font-weight:800;color:#111827;letter-spacing:-0.02em;margin:0 0 2px; }
.prl-topbar-sub   { font-size:0.78rem;color:#9ca3af;margin:0;font-family:'DM Mono',monospace; }

.prl-back-link { display:inline-flex;align-items:center;gap:8px;font-size:0.84rem;font-weight:700;color:#374151;text-decoration:none;margin-bottom:16px;padding:9px 16px;background:#fff;border:1px solid #e5e7eb;border-radius:10px;cursor:pointer;transition:all 0.13s; }
.prl-back-link:hover { border-color:#c8292a;color:#c8292a;background:#fff5f5; }

.prl-table-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden; }
.prl-table-scroll { overflow-x:auto; }
.prl-table { width:100%;border-collapse:collapse;font-size:0.835rem; }
.prl-table thead tr { background:#f8f9fb;border-bottom:1px solid #e5e7eb; }
.prl-table thead th { padding:11px 16px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#6b7280;white-space:nowrap; }
.prl-table tbody tr { border-bottom:1px solid #f3f4f6;transition:background 0.12s;cursor:pointer; }
.prl-table tbody tr:last-child { border-bottom:none; }
.prl-table tbody tr:hover { background:#fdf4f4; }
.prl-table tbody td { padding:13px 16px;color:#374151;vertical-align:middle; }

.prl-emp-cell { display:flex;align-items:center;gap:10px; }
.prl-emp-avatar { width:32px;height:32px;border-radius:50%;background:#f3f4f6;display:flex;align-items:center;justify-content:center;font-size:0.72rem;font-weight:700;color:#6b7280;flex-shrink:0;border:1.5px solid #e5e7eb;text-transform:uppercase; }
.prl-emp-name { font-weight:600;color:#111827;font-size:0.845rem; }
.prl-emp-dept { font-size:0.72rem;color:#9ca3af;margin-top:1px; }
.prl-mono { font-family:'DM Mono',monospace;font-size:0.82rem;font-variant-numeric:tabular-nums; }
.prl-mono.c-bold  { color:#111827;font-weight:700; }
.prl-mono.c-green { color:#16a34a;font-weight:600; }
.prl-mono.c-muted { color:#9ca3af; }

.prl-status { display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;white-space:nowrap; }
.prl-status::before { content:'';width:5px;height:5px;border-radius:50%; }
.prl-status.s-approved { background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0; }
.prl-status.s-approved::before { background:#16a34a; }
.prl-status.s-released { background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe; }
.prl-status.s-released::before { background:#3b82f6; }
.prl-status.s-submitted { background:#f5f3ff;color:#7c3aed;border:1px solid #ddd6fe; }
.prl-status.s-submitted::before { background:#8b5cf6; }
.prl-status.s-prepared { background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0; }
.prl-status.s-prepared::before { background:#16a34a; }
.prl-status.s-default  { background:#f3f4f6;color:#6b7280; }
.prl-status.s-default::before { background:#9ca3af; }

.prl-empty { display:flex;flex-direction:column;align-items:center;justify-content:center;padding:56px 24px;text-align:center; }
.prl-empty-title { font-size:0.9rem;font-weight:700;color:#374151;margin:0 0 6px; }
.prl-empty-sub   { font-size:0.78rem;color:#9ca3af;margin:0; }
</style>
@endpush

@section('content')
<div class="prl-page">

    <a href="{{ route('payroll.generate-payslip.index') }}" class="prl-back-link">
        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Back to Pay Slips
    </a>

    <div class="prl-topbar">
        <div>
            <h1 class="prl-topbar-title">{{ $batch->display_name }}</h1>
            <p class="prl-topbar-sub">{{ $batch->period_start->format('M d, Y') }} – {{ $batch->period_end->format('M d, Y') }} · {{ $batch->payrolls->count() }} employees</p>
        </div>
    </div>

    <div class="prl-table-card">
        <div class="prl-table-scroll">
            <table class="prl-table">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th class="text-end">Basic Salary</th>
                        <th class="text-end">Gross Pay</th>
                        <th class="text-end">Net Pay</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($batch->payrolls as $payroll)
                    @php
                        $initials = strtoupper(substr($payroll->user->first_name ?? 'U', 0, 1) . substr($payroll->user->last_name ?? '', 0, 1));
                        $sc = match($payroll->status) {
                            'approved' => 's-approved',
                            'released', 'paid' => 's-released',
                            'submitted' => 's-submitted',
                            'prepared' => 's-prepared',
                            default => 's-default',
                        };
                        $payslipUrl = route('payroll.generatePayslip', $payroll);
                    @endphp
                    <tr data-href="{{ $payslipUrl }}" onclick="rowClick(event, this)">
                        <td>
                            <div class="prl-emp-cell">
                                <div class="prl-emp-avatar">{{ $initials }}</div>
                                <div>
                                    <div class="prl-emp-name">{{ $payroll->user->first_name }} {{ $payroll->user->last_name }}</div>
                                    <div class="prl-emp-dept">{{ $payroll->user->position ?? '—' }} · {{ $payroll->user->department ?? '—' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="text-end"><span class="prl-mono c-muted">&#8369;{{ number_format($payroll->basic_salary, 2) }}</span></td>
                        <td class="text-end"><span class="prl-mono c-green">&#8369;{{ number_format($payroll->gross_pay, 2) }}</span></td>
                        <td class="text-end"><span class="prl-mono c-bold">&#8369;{{ number_format($payroll->net_pay, 2) }}</span></td>
                        <td class="text-center">
                            <span class="prl-status {{ $sc }}">
                                {{ in_array($payroll->status, ['released', 'paid'], true) ? 'Released' : ucfirst($payroll->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5">
                        <div class="prl-empty">
                            <p class="prl-empty-title">No payroll records in this batch</p>
                            <p class="prl-empty-sub">Add employees to the batch to generate payslips.</p>
                        </div>
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function rowClick(event, row) {
    if (event.target.closest('a, button, form, input')) return;
    window.location = row.dataset.href;
}
</script>
@endpush

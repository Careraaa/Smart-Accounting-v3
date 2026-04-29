@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
.prl-page{font-family:'Sora',sans-serif;}
.ps-card{background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;}
.ps-head{padding:14px 16px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;}
.ps-title{margin:0;font-weight:900;color:#111827;font-size:.9rem;}
.ps-sub{color:#6b7280;font-size:.8rem;}
.ps-table{width:100%;border-collapse:collapse;font-size:.85rem;}
.ps-table th{background:#f8f9fb;border-bottom:1px solid #e5e7eb;padding:11px 16px;font-size:.68rem;font-weight:900;text-transform:uppercase;letter-spacing:.09em;color:#6b7280;}
.ps-table td{padding:12px 16px;border-bottom:1px solid #f3f4f6;vertical-align:middle;}
.ps-btn{display:inline-flex;align-items:center;gap:6px;padding:7px 12px;border-radius:10px;font-size:.78rem;font-weight:900;text-decoration:none;border:1px solid #e5e7eb;background:#fff;color:#374151;}
.ps-btn:hover{background:#f9fafb;border-color:#d1d5db;}
.ps-mono{font-family:'DM Mono',monospace;font-variant-numeric:tabular-nums;}
</style>
@endpush

@section('content')
<div class="prl-page">
    <div class="ps-head" style="margin-bottom:12px;">
        <div>
            <h1 class="ps-title">Batch Payslips</h1>
            <div class="ps-sub">{{ $batch->display_name }} · {{ $batch->period_start->format('M d, Y') }} – {{ $batch->period_end->format('M d, Y') }}</div>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <a class="ps-btn" href="{{ route('payroll.batch.details', $batch) }}">Back to Batch</a>
        </div>
    </div>

    <div class="ps-card">
        <div class="table-responsive">
            <table class="ps-table">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th class="text-end">Net</th>
                        <th class="text-end">Payslip</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($batch->payrolls as $p)
                        <tr>
                            <td><strong>{{ $p->user->first_name }} {{ $p->user->last_name }}</strong></td>
                            <td class="text-end ps-mono">₱{{ number_format($p->net_pay, 2) }}</td>
                            <td class="text-end">
                                <a class="ps-btn" href="{{ route('payroll.generatePayslip', $p) }}" target="_blank" rel="noopener">
                                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Open payslip
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" style="text-align:center;padding:32px;color:#9ca3af;">No payroll rows in this batch.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection


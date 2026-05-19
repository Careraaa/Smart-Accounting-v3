@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
.prl-page { font-family: 'Sora', sans-serif; }

.prl-topbar { display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;flex-wrap:wrap; }
.prl-topbar-title { font-size:1.35rem;font-weight:800;color:#111827;letter-spacing:-0.02em;margin:0 0 2px; }
.prl-topbar-sub   { font-size:0.78rem;color:#9ca3af;margin:0; }

.prl-filter-bar { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:10px 14px;display:flex;align-items:center;gap:10px;margin-bottom:14px;flex-wrap:wrap; }
.prl-search-wrap { position:relative;flex:1;min-width:180px; }
.prl-search-wrap svg { position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#9ca3af;pointer-events:none; }
.prl-search-input { width:100%;border:1px solid #e5e7eb;border-radius:8px;padding:7px 12px 7px 32px;font-size:0.82rem;font-family:'Sora',sans-serif;color:#111827;background:#f9fafb;outline:none;transition:border-color 0.15s; }
.prl-search-input:focus { border-color:#c8292a;background:#fff;box-shadow:0 0 0 3px rgba(200,41,42,0.08); }
.prl-filter-select { border:1px solid #e5e7eb;border-radius:8px;padding:7px 12px;font-size:0.82rem;font-family:'Sora',sans-serif;color:#374151;background:#f9fafb;outline:none;cursor:pointer; }

.prl-table-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden; }
.prl-table-scroll { overflow-x:auto; }
.prl-table { width:100%;border-collapse:collapse;font-size:0.835rem; }
.prl-table thead tr { background:#f8f9fb;border-bottom:1px solid #e5e7eb; }
.prl-table thead th { padding:11px 16px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#6b7280;white-space:nowrap; }
.prl-table tbody tr { border-bottom:1px solid #f3f4f6;transition:background 0.12s;cursor:pointer; }
.prl-table tbody tr:last-child { border-bottom:none; }
.prl-table tbody tr:hover { background:#fdf4f4; }
.prl-table tbody td { padding:13px 16px;color:#374151;vertical-align:middle; }
.prl-table-footer { padding:12px 16px;border-top:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px; }
.prl-table-footer-note { font-size:0.78rem;color:#9ca3af; }
.prl-table-footer-note strong { color:#374151; }

.prl-batch-name { font-weight:700;color:#111827;font-size:0.845rem;line-height:1.2; }
.prl-batch-sub  { font-size:0.72rem;color:#9ca3af;margin-top:2px;font-family:'DM Mono',monospace; }
.prl-mono { font-family:'DM Mono',monospace;font-size:0.82rem;font-variant-numeric:tabular-nums; }
.prl-mono.bold { color:#111827;font-weight:700; }
.prl-period-tag { display:inline-block;font-family:'DM Mono',monospace;font-size:0.72rem;color:#6b7280;background:#f3f4f6;padding:2px 8px;border-radius:4px; }

.prl-status { display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;white-space:nowrap; }
.prl-status::before { content:'';width:5px;height:5px;border-radius:50%; }
.prl-status.s-submitted { background:#f5f3ff;color:#7c3aed;border:1px solid #ddd6fe; }
.prl-status.s-submitted::before { background:#8b5cf6; }
.prl-status.s-approved  { background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0; }
.prl-status.s-approved::before { background:#16a34a; }
.prl-status.s-rejected  { background:#fff0f0;color:#c8292a;border:1px solid #fecaca; }
.prl-status.s-rejected::before { background:#ef4444; }
.prl-status.s-paid      { background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe; }
.prl-status.s-paid::before { background:#3b82f6; }

.prl-empty { display:flex;flex-direction:column;align-items:center;justify-content:center;padding:56px 24px;text-align:center; }
.prl-empty-icon { width:56px;height:56px;background:#f3f4f6;border-radius:16px;display:flex;align-items:center;justify-content:center;margin-bottom:14px;color:#d1d5db; }
.prl-empty-title { font-size:0.9rem;font-weight:700;color:#374151;margin:0 0 6px; }
.prl-empty-sub   { font-size:0.78rem;color:#9ca3af;margin:0; }
</style>
@endpush

@section('content')
<div class="prl-page">

    <div class="prl-topbar">
        <div>
            <h1 class="prl-topbar-title">Pay Slips</h1>
            <p class="prl-topbar-sub">Browse payroll batches and view employee payslips</p>
        </div>
    </div>

    <div class="prl-filter-bar">
        <div class="prl-search-wrap">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
            <input type="text" class="prl-search-input" id="batchSearch" placeholder="Search batch…">
        </div>
        <select class="prl-filter-select" id="statusFilter">
            <option value="">All Statuses</option>
            <option value="submitted" @selected(request('status') === 'submitted')>Submitted</option>
            <option value="approved" @selected(request('status') === 'approved')>Approved</option>
            <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
            <option value="paid" @selected(request('status') === 'paid')>Paid</option>
        </select>
    </div>

    <div class="prl-table-card">
        <div class="prl-table-scroll">
            <table class="prl-table">
                <thead>
                    <tr>
                        <th>Batch</th>
                        <th>Period</th>
                        <th class="text-center">Employees</th>
                        <th class="text-end">Total Net Pay</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody id="batchTbody">
                @forelse($batches as $batch)
                    @php
                        $sc = match($batch->status) {
                            'approved'  => 's-approved',
                            'rejected'  => 's-rejected',
                            'paid'      => 's-paid',
                            default     => 's-submitted',
                        };
                        $payslipsUrl = route('payroll.batch.payslips', $batch);
                    @endphp
                    <tr
                        data-name="{{ strtolower($batch->display_name) }}"
                        data-status="{{ $batch->status }}"
                        onclick="window.location='{{ $payslipsUrl }}'"
                    >
                        <td>
                            <div class="prl-batch-name">{{ $batch->display_name }}</div>
                            <div class="prl-batch-sub">
                                Created {{ $batch->created_at->format('M d, Y') }}
                                @if($batch->finalized_at) &middot; Submitted {{ $batch->finalized_at->format('M d') }} @endif
                            </div>
                        </td>
                        <td>
                            <span class="prl-period-tag">
                                {{ $batch->period_start->format('M d') }} &ndash; {{ $batch->period_end->format('M d, Y') }}
                            </span>
                        </td>
                        <td class="text-center" style="font-family:'DM Mono',monospace;font-weight:700;color:#374151;">
                            {{ $batch->payrolls_count }}
                        </td>
                        <td class="text-end">
                            <span class="prl-mono bold">&#8369;{{ number_format($batch->total_net_pay, 2) }}</span>
                        </td>
                        <td class="text-center">
                            <span class="prl-status {{ $sc }}">{{ ucfirst($batch->status) }}</span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5">
                        <div class="prl-empty">
                            <div class="prl-empty-icon">
                                <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <p class="prl-empty-title">No payroll batches yet</p>
                            <p class="prl-empty-sub">Generate a payroll batch first to view payslips.</p>
                        </div>
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div id="batchNoResults" style="display:none;">
            <div class="prl-empty" style="padding:28px;">
                <p class="prl-empty-title">No results</p>
                <p class="prl-empty-sub">Try adjusting your search or filters.</p>
            </div>
        </div>
        @if($batches->hasPages())
        <div class="prl-table-footer">
            <div class="prl-table-footer-note">
                Showing <strong>{{ $batches->firstItem() }}</strong> &ndash; <strong>{{ $batches->lastItem() }}</strong> of <strong>{{ $batches->total() }}</strong>
            </div>
            {{ $batches->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
(function () {
    const search  = document.getElementById('batchSearch');
    const statusF = document.getElementById('statusFilter');
    const tbody   = document.getElementById('batchTbody');
    const noRes   = document.getElementById('batchNoResults');

    if (!tbody) return;

    function run() {
        const q  = (search?.value || '').toLowerCase().trim();
        const st = statusF?.value || '';
        let visible = 0;
        tbody.querySelectorAll('tr[data-name]').forEach(row => {
            const match = (!q || row.dataset.name.includes(q)) && (!st || row.dataset.status === st);
            row.style.display = match ? '' : 'none';
            if (match) visible++;
        });
        if (noRes) noRes.style.display = visible === 0 ? 'block' : 'none';
    }

    statusF?.addEventListener('change', function () {
        const url = new URL(window.location.href);
        if (this.value) url.searchParams.set('status', this.value);
        else url.searchParams.delete('status');
        window.location = url.toString();
    });

    search?.addEventListener('input', run);
    run();
})();
</script>
@endpush

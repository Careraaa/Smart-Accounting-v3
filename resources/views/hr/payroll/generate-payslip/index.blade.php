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

.prl-batch-grid { display:flex;flex-direction:column;gap:0; }
@media (max-width:900px) { .prl-batch-grid { flex-direction:column; } }
.prl-batch-col { width:100%;border-bottom:1px solid #f3f4f6; }
.prl-batch-col:last-child { border-bottom:none; }
.prl-batch-col-full { width:100%; }
.prl-batch-card-link { text-decoration:none;color:inherit;display:block; }
.prl-batch-card { background:#fff;border:none;border-radius:0;padding:0;transition:background 0.15s;cursor:pointer; }
.prl-batch-card:hover { background:#fafafa; }
.prl-batch-card-inner { display:flex;align-items:center;gap:18px;padding:18px 20px;flex-wrap:nowrap;justify-content:flex-start;border:1px solid #e5e7eb;border-radius:14px;margin:8px 0; }
.prl-batch-card-icon { width:52px;height:52px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;color:#fff;font-size:1.35rem; }
.prl-batch-card-icon.a { background:linear-gradient(135deg, #c8292a, #9f1e1f); }
.prl-batch-card-icon.b { background:linear-gradient(135deg, #0284c7, #0369a1); }
.prl-batch-card-content { flex:0 1 auto;min-width:200px; }
.prl-batch-card-title { font-size:0.9rem;font-weight:700;color:#111827;margin:0 0 4px;line-height:1.2; }
.prl-batch-card-date { font-size:0.75rem;color:#9ca3af;font-family:'DM Mono',monospace;margin:0; }
.prl-batch-card-stats { display:flex;align-items:center;gap:18px;margin-left:auto;flex-shrink:0;flex-wrap:nowrap;justify-content:flex-end; }
.prl-batch-status { display:inline-flex;align-items:center;justify-content:center;font-size:0.65rem;font-weight:700;padding:4px 10px;border-radius:20px;text-transform:uppercase;letter-spacing:0.06em;white-space:nowrap;flex-shrink:0;height:24px;min-width:100px; }
.prl-batch-stat { display:flex;flex-direction:column;align-items:center;text-align:center;flex-shrink:0;min-width:80px;justify-content:center; }
.prl-batch-stat small { font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#9ca3af;margin-bottom:4px;display:block; }
.prl-batch-stat strong { font-family:'DM Mono',monospace;font-size:0.92rem;color:#111827;line-height:1.2;white-space:nowrap; }
.prl-batch-card-arrow { width:20px;height:20px;display:flex;align-items:center;justify-content:center;color:#d1d5db;flex-shrink:0;font-size:1.1rem;margin-left:12px; }

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
        <div class="prl-batch-grid">
            @forelse($batches as $batch)
                @php
                    $startDate = $batch->period_start;
                    $endDate   = $batch->period_end;
                    $isFirst   = $startDate->format('d') <= 15;
                    $variant   = $isFirst ? 'a' : 'b';
                    $statusColors = [
                        'submitted' => ['bg' => '#f5f3ff', 'color' => '#7c3aed', 'label' => 'Submitted'],
                        'approved' => ['bg' => '#f0fdf4', 'color' => '#16a34a', 'label' => 'Approved'],
                        'rejected' => ['bg' => '#fff0f0', 'color' => '#c8292a', 'label' => 'Rejected'],
                        'paid' => ['bg' => '#eff6ff', 'color' => '#1d4ed8', 'label' => 'Paid'],
                    ];
                    $statusInfo = $statusColors[$batch->status] ?? $statusColors['submitted'];
                    $payslipsUrl = route('payroll.batch.payslips', $batch);
                @endphp
                <div class="prl-batch-col" data-status="{{ $batch->status }}">
                    <a href="{{ $payslipsUrl }}" class="prl-batch-card-link">
                        <div class="prl-batch-card">
                            <div class="prl-batch-card-inner">
                                <div class="prl-batch-card-icon {{ $variant }}">
                                    @if($isFirst)
                                        <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    @else
                                        <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @endif
                                </div>
                                <div class="prl-batch-card-content">
                                    <h6 class="prl-batch-card-title">
                                        {{ $startDate->format('F Y') }} — {{ $isFirst ? '1st' : '2nd' }} half
                                    </h6>
                                    <div class="prl-batch-card-date">
                                        {{ $startDate->format('M d') }} – {{ $endDate->format('M d, Y') }}
                                    </div>
                                </div>
                                <div class="prl-batch-card-stats">
                                    <span class="prl-batch-status" data-status="{{ $batch->status }}" style="background:{{ $statusInfo['bg'] }};color:{{ $statusInfo['color'] }};">
                                        {{ $statusInfo['label'] }}
                                    </span>
                                    <div class="prl-batch-stat">
                                        <small>Employees</small>
                                        <strong>{{ $batch->payrolls_count ?? $batch->payrolls->count() }}</strong>
                                    </div>
                                    <div class="prl-batch-stat">
                                        <small>Net Pay</small>
                                        <strong style="color:#16a34a;">₱{{ number_format($batch->total_net_pay, 2) }}</strong>
                                    </div>
                                    <div class="prl-batch-card-arrow">
                                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="prl-batch-col-full">
                    <div class="prl-empty" style="padding:40px 20px;">
                        <div class="prl-empty-icon">
                            <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <p class="prl-empty-title">No payroll batches yet</p>
                        <p class="prl-empty-sub">Generate a payroll batch first to view payslips.</p>
                    </div>
                </div>
            @endforelse
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
    const bg      = document.querySelector('.prl-batch-grid');
    const noRes   = document.getElementById('batchNoResults');

    if (!bg) return;

    function run() {
        const q  = (search?.value || '').toLowerCase().trim();
        const st = statusF?.value || '';
        let visible = 0;
        bg.querySelectorAll('.prl-batch-col').forEach(col => {
            const colStatus = col.dataset.status || '';
            const name = col.textContent.toLowerCase();
            const match = (!q || name.includes(q)) && (!st || colStatus === st);
            col.style.display = match ? '' : 'none';
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

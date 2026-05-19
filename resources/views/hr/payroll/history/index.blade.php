@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
.prl-page { font-family: 'Sora', sans-serif; }

.prl-topbar { display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;flex-wrap:wrap; }
.prl-topbar-title { font-size:1.35rem;font-weight:800;color:#111827;letter-spacing:-0.02em;margin:0 0 2px; }
.prl-topbar-sub   { font-size:0.78rem;color:#9ca3af;margin:0; }
.prl-topbar-actions { display:flex;gap:8px;flex-wrap:wrap;align-items:center; }

.prl-btn-sec {
    display:inline-flex;align-items:center;gap:7px;padding:9px 16px;background:#fff;color:#374151;
    border:1px solid #e5e7eb;border-radius:10px;font-family:'Sora',sans-serif;font-size:0.82rem;
    font-weight:600;text-decoration:none;cursor:pointer;transition:all 0.15s;white-space:nowrap;
}
.prl-btn-sec:hover { border-color:#c8292a;color:#c8292a;background:#fff5f5; }

.prl-stats { display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px; }
@media (max-width:1100px) { .prl-stats { grid-template-columns:repeat(2,1fr); } }
@media (max-width:600px)  { .prl-stats { grid-template-columns:1fr; } }
.prl-stat { background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:18px 20px;display:flex;align-items:flex-start;gap:14px;position:relative;overflow:hidden;transition:box-shadow 0.15s; }
.prl-stat:hover { box-shadow:0 4px 20px rgba(0,0,0,0.07); }
.prl-stat::after { content:'';position:absolute;bottom:0;left:0;right:0;height:3px;border-radius:0 0 14px 14px; }
.prl-stat.s-red::after   { background:#c8292a; }
.prl-stat.s-green::after { background:#16a34a; }
.prl-stat.s-amber::after { background:#d97706; }
.prl-stat.s-blue::after  { background:#0284c7; }
.prl-stat-icon { width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.prl-stat.s-red   .prl-stat-icon { background:#fff0f0;color:#c8292a; }
.prl-stat.s-green .prl-stat-icon { background:#f0fdf4;color:#16a34a; }
.prl-stat.s-amber .prl-stat-icon { background:#fffbeb;color:#d97706; }
.prl-stat.s-blue  .prl-stat-icon { background:#f0f9ff;color:#0284c7; }
.prl-stat-label { font-size:0.67rem;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#9ca3af;margin-bottom:4px; }
.prl-stat-value { font-size:1.6rem;font-weight:800;color:#111827;line-height:1;font-variant-numeric:tabular-nums;font-family:'DM Mono',monospace; }
.prl-stat-sub   { font-size:0.73rem;color:#9ca3af;margin-top:4px; }

.prl-section-head { display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;flex-wrap:wrap;gap:10px; }
.prl-section-title { font-size:0.88rem;font-weight:700;color:#111827;margin:0;display:flex;align-items:center;gap:8px; }
.prl-dot { width:8px;height:8px;border-radius:50%;background:#c8292a;display:inline-block; }

.prl-filter-bar { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:12px 16px;display:flex;align-items:center;gap:10px;margin-bottom:14px;flex-wrap:wrap; }
.prl-search-wrap { position:relative;flex:1;min-width:180px; }
.prl-search-wrap svg { position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#9ca3af;pointer-events:none; }
.prl-search-input { width:100%;border:1px solid #e5e7eb;border-radius:8px;padding:8px 12px 8px 34px;font-size:0.82rem;font-family:'Sora',sans-serif;color:#111827;background:#f9fafb;outline:none;transition:border-color 0.15s,background 0.15s; }
.prl-search-input:focus { border-color:#c8292a;background:#fff;box-shadow:0 0 0 3px rgba(200,41,42,0.08); }
.prl-filter-select { border:1px solid #e5e7eb;border-radius:8px;padding:8px 12px;font-size:0.82rem;font-family:'Sora',sans-serif;color:#374151;background:#f9fafb;outline:none;cursor:pointer;transition:border-color 0.15s; }
.prl-filter-select:focus { border-color:#c8292a; }

.prl-table-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden; }
.prl-table { width:100%;border-collapse:collapse;font-size:0.835rem; }
.prl-table thead tr { background:#f8f9fb;border-bottom:1px solid #e5e7eb; }
.prl-table thead th { padding:11px 16px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#6b7280;white-space:nowrap;font-family:'Sora',sans-serif; }
.prl-table tbody tr { border-bottom:1px solid #f3f4f6;transition:background 0.1s; }
.prl-table tbody tr:last-child { border-bottom:none; }
.prl-table tbody tr:hover { background:#fafafa; }
.prl-table tbody tr.prl-clickable { cursor:pointer; }
.prl-table tbody tr.prl-clickable:hover { background:#f5f7ff; }
.prl-table tbody tr.prl-clickable:active { background:#eef1fb; }
.prl-table tbody td { padding:12px 16px;color:#374151;vertical-align:middle; }
.prl-table-scroll { overflow-x:auto; }
.prl-table-footer { padding:12px 16px;border-top:1px solid #f3f4f6;display:flex;justify-content:flex-end; }

.prl-period-tag { display:inline-block;font-family:'DM Mono',monospace;font-size:0.75rem;color:#374151;background:#f3f4f6;padding:3px 10px;border-radius:6px;font-weight:500; }
.prl-mono { font-family:'DM Mono',monospace;font-size:0.82rem;font-variant-numeric:tabular-nums; }
.prl-mono.c-bold { color:#111827;font-weight:700; }

.prl-status { display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;white-space:nowrap; }
.prl-status::before { content:'';width:5px;height:5px;border-radius:50%; }
.prl-status.s-finalized { background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe; }
.prl-status.s-finalized::before { background:#3b82f6; }
.prl-status.s-submitted { background:#f5f3ff;color:#7c3aed;border:1px solid #ddd6fe; }
.prl-status.s-submitted::before { background:#8b5cf6; }
.prl-status.s-approved  { background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0; }
.prl-status.s-approved::before  { background:#16a34a; }
.prl-status.s-released  { background:#f0fdf4;color:#15803d;border:1px solid #86efac; }
.prl-status.s-released::before  { background:#22c55e; }

.prl-actions { display:flex;align-items:center;gap:5px;justify-content:flex-end; }
.prl-action-btn { width:30px;height:30px;border-radius:7px;border:none;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;text-decoration:none;font-size:13px;transition:background 0.13s,color 0.13s;background:#f4f5f7;color:#6b7280;padding:0; }
.prl-action-btn:hover { background:#eff6ff;color:#3b82f6; }

.prl-emp-count { font-size:0.82rem;color:#374151; }
.prl-emp-count strong { color:#111827;font-weight:700; }

.prl-empty { display:flex;flex-direction:column;align-items:center;justify-content:center;padding:56px 24px;text-align:center; }
.prl-empty-icon { width:56px;height:56px;background:#f3f4f6;border-radius:16px;display:flex;align-items:center;justify-content:center;margin-bottom:14px;color:#d1d5db; }
.prl-empty-title { font-size:0.9rem;font-weight:700;color:#374151;margin:0 0 6px; }
.prl-empty-sub   { font-size:0.78rem;color:#9ca3af;margin:0; }

.prl-flash { display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:10px;font-size:0.82rem;font-weight:500;margin-bottom:20px;animation:flashIn 0.3s ease; }
.prl-flash.success { background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d; }
.prl-flash.error   { background:#fff0f0;border:1px solid #fecaca;color:#c8292a; }
.prl-flash.info    { background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8; }
@keyframes flashIn { from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)} }
</style>
@endpush

@section('content')
<div class="prl-page">

    @foreach(['success','error','info'] as $t)
        @if(session($t))
        <div class="prl-flash {{ $t }}">
            @if($t==='success')<svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            @else<svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>@endif
            {{ session($t) }}
        </div>
        @endif
    @endforeach

    <div class="prl-topbar">
        <div>
            <h1 class="prl-topbar-title">Payroll History</h1>
            <p class="prl-topbar-sub">View all finalized and released payroll batches</p>
        </div>
        <div class="prl-topbar-actions">
            <a href="{{ route('hr.reports.print.payroll-history-report') }}" class="prl-btn-sec" target="_blank">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                Print Report
            </a>
        </div>
    </div>

    <div class="prl-stats">
        <div class="prl-stat s-blue">
            <div class="prl-stat-icon">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path stroke-linecap="round" d="M8 21h8M12 17v4"/></svg>
            </div>
            <div>
                <div class="prl-stat-label">Total Batches</div>
                <div class="prl-stat-value">{{ $totalBatches }}</div>
                <div class="prl-stat-sub">generated periods</div>
            </div>
        </div>
        <div class="prl-stat s-green">
            <div class="prl-stat-icon">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="prl-stat-label">Total Payroll</div>
                <div class="prl-stat-value" style="font-size:1rem;">₱{{ number_format($totalPayroll,2) }}</div>
                <div class="prl-stat-sub">all time</div>
            </div>
        </div>
        <div class="prl-stat s-amber">
            <div class="prl-stat-icon">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <div class="prl-stat-label">Released Payrolls</div>
                <div class="prl-stat-value">{{ $totalReleased }}</div>
                <div class="prl-stat-sub">across all batches</div>
            </div>
        </div>
        <div class="prl-stat s-red">
            <div class="prl-stat-icon">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
            </div>
            <div>
                <div class="prl-stat-label">Next Cutoff</div>
                <div class="prl-stat-value" style="font-size:1rem;font-family:'Sora',sans-serif;">{{ $nextCutoffDate ? $nextCutoffDate->format('M d, Y') : 'N/A' }}</div>
                <div class="prl-stat-sub">{{ $nextCutoffDate ? $nextCutoffDate->format('l') : '—' }}</div>
            </div>
        </div>
    </div>

    <div class="prl-section-head">
        <h2 class="prl-section-title"><span class="prl-dot"></span> Payroll Batches</h2>
    </div>

    <div class="prl-filter-bar">
        <div class="prl-search-wrap">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
            <input type="text" class="prl-search-input" id="prlSearch" placeholder="Search by period…">
        </div>
        <select class="prl-filter-select" id="filterMonth" onchange="applyFilter()">
            <option value="">All Months</option>
            @for($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" {{ $filterMonth == $i ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::createFromDate(null, $i)->format('F') }}
                </option>
            @endfor
        </select>
        <select class="prl-filter-select" id="filterYear" onchange="applyFilter()">
            <option value="">All Years</option>
            @for($i = now()->year - 5; $i <= now()->year; $i++)
                <option value="{{ $i }}" {{ $filterYear == $i ? 'selected' : '' }}>{{ $i }}</option>
            @endfor
        </select>
    </div>

    <div class="prl-table-card">
        <div class="prl-table-scroll">
            <table class="prl-table">
                <thead><tr>
                    <th>Period</th>
                    <th>Employees</th>
                    <th class="text-end">Total Net Pay</th>
                    <th class="text-center">Status</th>
                </tr></thead>
                <tbody id="prlTbody">
                @forelse($batches as $batch)
                    @php
                        $sc = match($batch->status ?? 'submitted') {
                            'finalized' => 's-finalized',
                            'submitted' => 's-submitted',
                            'approved'  => 's-approved',
                            'released', 'paid' => 's-released',
                            default     => 's-submitted',
                        };
                        $batchUrl = route('payroll.history.batch', ['start' => $batch->period_start->format('Y-m-d'), 'end' => $batch->period_end->format('Y-m-d')]);
                    @endphp
                    <tr class="prl-clickable" data-period="{{ strtolower($batch->period_start->format('M Y')) }}" onclick="window.location='{{ $batchUrl }}'">
                        <td><span class="prl-period-tag">{{ $batch->period_start->format('M d') }} – {{ $batch->period_end->format('M d, Y') }}</span></td>
                        <td><span class="prl-emp-count"><strong>{{ $batch->payrolls->count() }}</strong> employees</span></td>
                        <td class="text-end"><span class="prl-mono c-bold">₱{{ number_format($batch->total_net_pay ?? $batch->payrolls->sum('net_pay') ?? 0, 0) }}</span></td>
                        <td class="text-center"><span class="prl-status {{ $sc }}">{{ ucfirst($batch->status ?? 'submitted') }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="4">
                        <div class="prl-empty">
                            <div class="prl-empty-icon"><svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><rect x="2" y="3" width="20" height="14" rx="2"/><path stroke-linecap="round" d="M8 21h8M12 17v4"/></svg></div>
                            <p class="prl-empty-title">No payroll batches yet</p>
                            <p class="prl-empty-sub">Generated batches will appear here.</p>
                        </div>
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div id="prlNoResults" style="display:none;">
            <div class="prl-empty" style="padding:32px;">
                <p class="prl-empty-title">No results found</p>
                <p class="prl-empty-sub">Try a different search or filter.</p>
            </div>
        </div>
        @if($batches->hasPages())
        <div class="prl-table-footer">{{ $batches->appends(request()->query())->links() }}</div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
(function () {
    const s  = document.getElementById('prlSearch');
    const tb = document.getElementById('prlTbody');
    const nr = document.getElementById('prlNoResults');
    s.addEventListener('input', function () {
        const q = this.value.toLowerCase().trim();
        const rows = Array.from(tb.querySelectorAll('tr[data-period]'));
        const vis  = rows.filter(r => !q || r.dataset.period.includes(q));
        rows.forEach(r => r.style.display = 'none');
        vis.forEach(r => r.style.display = '');
        nr.style.display = vis.length === 0 && rows.length > 0 ? 'block' : 'none';
    });
})();

function applyFilter() {
    const month = document.getElementById('filterMonth').value;
    const year  = document.getElementById('filterYear').value;
    let url = "{{ route('payroll.history.index') }}";
    const params = [];
    if (month) params.push('filter_month=' + month);
    if (year)  params.push('filter_year=' + year);
    if (params.length) url += '?' + params.join('&');
    window.location.href = url;
}
</script>
@endpush
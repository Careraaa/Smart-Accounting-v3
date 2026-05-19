@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
*, *::before, *::after { box-sizing: border-box; }
.ot-page { font-family: 'Sora', sans-serif; }

/* ── Topbar ─────────────────────────────────────────────────── */
.ot-topbar { display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;flex-wrap:wrap; }
.ot-topbar-title { font-size:1.35rem;font-weight:800;color:#111827;letter-spacing:-0.02em;margin:0 0 2px; }
.ot-topbar-sub   { font-size:0.78rem;color:#9ca3af;margin:0; }
.ot-topbar-actions { display:flex;gap:8px;flex-wrap:wrap;align-items:center; }

.ot-btn-primary {
    display:inline-flex;align-items:center;gap:7px;padding:9px 18px;
    background:#111827;color:#fff;border:none;border-radius:10px;
    font-family:'Sora',sans-serif;font-size:0.82rem;font-weight:600;
    text-decoration:none;cursor:pointer;transition:background 0.15s;white-space:nowrap;
}
.ot-btn-primary:hover { background:#000;color:#fff; }

/* ── Flash ──────────────────────────────────────────────────── */
.ot-flash { display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:10px;font-size:0.82rem;font-weight:500;margin-bottom:20px;animation:flashIn 0.3s ease; }
.ot-flash.success { background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d; }
.ot-flash.error   { background:#fff0f0;border:1px solid #fecaca;color:#c8292a; }
@keyframes flashIn { from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)} }

/* ── Filter bar ─────────────────────────────────────────────── */
.ot-filter-bar { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:10px 14px;display:flex;align-items:center;gap:10px;margin-bottom:14px;flex-wrap:wrap; }
.ot-search-wrap { position:relative;flex:1;min-width:160px; }
.ot-search-wrap svg { position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#9ca3af;pointer-events:none; }
.ot-search-input { width:100%;border:1px solid #e5e7eb;border-radius:8px;padding:7px 12px 7px 32px;font-size:0.82rem;font-family:'Sora',sans-serif;color:#111827;background:#f9fafb;outline:none;transition:border-color 0.15s; }
.ot-search-input:focus { border-color:#c8292a;background:#fff;box-shadow:0 0 0 3px rgba(200,41,42,0.08); }
.ot-filter-select { border:1px solid #e5e7eb;border-radius:8px;padding:7px 12px;font-size:0.82rem;font-family:'Sora',sans-serif;color:#374151;background:#f9fafb;outline:none;cursor:pointer; }
.ot-filter-select:focus { border-color:#c8292a; }

/* ── Table card ─────────────────────────────────────────────── */
.ot-table-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden; }
.ot-table-scroll { overflow-x:auto; }
.ot-table { width:100%;border-collapse:collapse;font-size:0.835rem; }
.ot-table thead tr { background:#f8f9fb;border-bottom:1px solid #e5e7eb; }
.ot-table thead th { padding:11px 16px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#6b7280;white-space:nowrap; }
.ot-table tbody tr { border-bottom:1px solid #f3f4f6;transition:background 0.12s;cursor:pointer; }
.ot-table tbody tr:last-child { border-bottom:none; }
.ot-table tbody tr:hover { background:#fdf4f4; }
.ot-table tbody td { padding:12px 16px;color:#374151;vertical-align:middle; }

/* Employee cell */
.ot-emp-cell   { display:flex;align-items:center;gap:10px; }
.ot-emp-avatar { width:34px;height:34px;border-radius:50%;background:#f3f4f6;display:flex;align-items:center;justify-content:center;font-size:0.72rem;font-weight:700;color:#6b7280;flex-shrink:0;border:1.5px solid #e5e7eb;text-transform:uppercase; }
.ot-emp-name   { font-weight:600;color:#111827;font-size:0.845rem;line-height:1.2; }
.ot-emp-pos    { font-size:0.72rem;color:#9ca3af;margin-top:1px; }

/* Department badge */
.ot-dept { display:inline-block;padding:2px 9px;border-radius:20px;font-size:0.68rem;font-weight:700;background:#f3f4f6;color:#374151;white-space:nowrap; }

/* Hours pills */
.ot-pill { display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:20px;font-size:0.72rem;font-weight:700;font-family:'DM Mono',monospace; }
.ot-pill.ot { background:#f0f9ff;color:#0284c7;border:1px solid #bae6fd; }
.ot-pill.ut { background:#fffbeb;color:#d97706;border:1px solid #fde68a; }
.ot-pill.none { background:#f3f4f6;color:#9ca3af;border:1px solid #e5e7eb; }

/* View link */
.ot-view-link { display:inline-flex;align-items:center;gap:5px;font-size:0.78rem;font-weight:600;color:#c8292a;text-decoration:none;padding:5px 10px;border-radius:7px;border:1px solid #fecaca;background:#fff0f0;transition:all 0.12s; }
.ot-view-link:hover { background:#c8292a;color:#fff;border-color:#c8292a; }

/* Empty state */
.ot-empty { display:flex;flex-direction:column;align-items:center;justify-content:center;padding:48px 24px;text-align:center; }
.ot-empty-icon  { width:52px;height:52px;background:#f3f4f6;border-radius:14px;display:flex;align-items:center;justify-content:center;margin-bottom:12px;color:#d1d5db; }
.ot-empty-title { font-size:0.88rem;font-weight:700;color:#374151;margin:0 0 4px; }
.ot-empty-sub   { font-size:0.75rem;color:#9ca3af;margin:0; }

/* Month badge */
.ot-month-badge { display:inline-flex;align-items:center;gap:5px;padding:3px 10px;background:#f0f9ff;border:1px solid #bae6fd;border-radius:20px;font-size:0.68rem;font-weight:700;color:#0284c7; }
</style>
@endpush

@section('content')
<div class="ot-page">

    {{-- Flash --}}
    @foreach(['success','error'] as $t)
        @if(session($t))
        <div class="ot-flash {{ $t }}">
            @if($t==='success')
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            @else
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
            @endif
            {{ session($t) }}
        </div>
        @endif
    @endforeach

    {{-- Topbar --}}
    <div class="ot-topbar">
        <div>
            <h1 class="ot-topbar-title">Overtime / Undertime</h1>
            <p class="ot-topbar-sub">
                Employee OT &amp; UT records —
                <span class="ot-month-badge">
                    <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                    {{ now()->format('F Y') }}
                </span>
            </p>
        </div>
        <div class="ot-topbar-actions">
            <a href="{{ route('overtime.pending') }}" class="ot-btn-primary">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 7v5l3 3"/></svg>
                Pending Requests
                @php $pendingOtCount = \App\Models\OvertimeUndertime::where('status','pending')->count(); @endphp
                @if($pendingOtCount > 0)
                    <span style="background:rgba(255,255,255,0.25);border-radius:999px;padding:1px 7px;font-size:0.72rem;font-weight:800;">{{ $pendingOtCount }}</span>
                @endif
            </a>
        </div>
    </div>

    {{-- Filter bar --}}
    <div class="ot-filter-bar">
        <div class="ot-search-wrap">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
            <input type="text" class="ot-search-input" id="empSearch" placeholder="Search employee…">
        </div>
        <select class="ot-filter-select" id="deptFilter">
            <option value="">All Departments</option>
            @foreach($employees->pluck('department')->filter()->unique()->sort() as $dept)
                <option value="{{ strtolower($dept) }}">{{ $dept }}</option>
            @endforeach
        </select>
    </div>

    {{-- Employee table --}}
    <div class="ot-table-card">
        <div class="ot-table-scroll">
            <table class="ot-table">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Position</th>
                        <th class="text-center">OT This Month</th>
                        <th class="text-center">UT This Month</th>
                        <th class="text-center">Records</th>
                    </tr>
                </thead>
                <tbody id="empTbody">
                    @forelse($employees as $emp)
                        @php
                            $summary  = $monthlySummary[$emp->id] ?? null;
                            $otHrs    = $summary ? (float) $summary->ot_hours : 0;
                            $utHrs    = $summary ? (float) $summary->ut_hours : 0;
                            $recCount = $summary ? (int)   $summary->total_records : 0;
                            $initials = strtoupper(substr($emp->first_name ?? 'U', 0, 1) . substr($emp->last_name ?? '', 0, 1));
                        @endphp
                        <tr
                            data-name="{{ strtolower($emp->first_name . ' ' . $emp->last_name) }}"
                            data-dept="{{ strtolower($emp->department ?? '') }}"
                            onclick="window.location='{{ route('overtime.employee.calendar', $emp->id) }}'"
                        >
                            <td>
                                <div class="ot-emp-cell">
                                    <div class="ot-emp-avatar">{{ $initials }}</div>
                                    <div>
                                        <div class="ot-emp-name">{{ $emp->first_name }} {{ $emp->last_name }}</div>
                                        <div class="ot-emp-pos">{{ $emp->position ?? '—' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="ot-dept">{{ $emp->department ?? '—' }}</span></td>
                            <td style="font-size:0.82rem;color:#6b7280;">{{ $emp->position ?? '—' }}</td>
                            <td class="text-center">
                                @if($otHrs > 0)
                                    <span class="ot-pill ot">
                                        <svg width="9" height="9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                        {{ number_format($otHrs, 1) }} hrs
                                    </span>
                                @else
                                    <span class="ot-pill none">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($utHrs > 0)
                                    <span class="ot-pill ut">
                                        <svg width="9" height="9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
                                        {{ number_format($utHrs, 1) }} hrs
                                    </span>
                                @else
                                    <span class="ot-pill none">—</span>
                                @endif
                            </td>
                            <td class="text-center" style="font-family:'DM Mono',monospace;font-size:0.82rem;color:#374151;font-weight:700;">
                                {{ $recCount > 0 ? $recCount : '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">
                            <div class="ot-empty">
                                <div class="ot-empty-icon">
                                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                                </div>
                                <p class="ot-empty-title">No employees found</p>
                                <p class="ot-empty-sub">Add employees to start tracking overtime and undertime.</p>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div id="empNoResults" style="display:none;">
            <div class="ot-empty" style="padding:28px;">
                <p class="ot-empty-title">No results</p>
                <p class="ot-empty-sub">Try adjusting your filters.</p>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
(function () {
    const search = document.getElementById('empSearch');
    const deptF  = document.getElementById('deptFilter');
    const tbody  = document.getElementById('empTbody');
    const noRes  = document.getElementById('empNoResults');

    function run() {
        const q = search.value.toLowerCase().trim();
        const d = deptF.value;
        const rows = Array.from(tbody.querySelectorAll('tr[data-name]'));
        const vis = rows.filter(r =>
            (!q || r.dataset.name.includes(q)) &&
            (!d || r.dataset.dept === d)
        );
        rows.forEach(r => r.style.display = 'none');
        vis.forEach(r => r.style.display = '');
        noRes.style.display = vis.length === 0 && rows.length > 0 ? 'block' : 'none';
    }

    search.addEventListener('input', run);
    deptF.addEventListener('change', run);
})();
</script>
@endpush

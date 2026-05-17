@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
.sd-page{font-family:'Sora',sans-serif}

.sd-topbar{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;flex-wrap:wrap}
.sd-topbar-title{font-size:1.35rem;font-weight:800;color:#111827;letter-spacing:-0.02em;margin:0 0 2px}
.sd-topbar-sub{font-size:0.78rem;color:#9ca3af;margin:0}
.sd-refs{display:flex;gap:8px;flex-wrap:wrap;align-items:center}
.sd-ref-link{display:inline-flex;align-items:center;gap:6px;padding:8px 14px;background:#fff;color:#6b7280;border:1px solid #e5e7eb;border-radius:10px;font-family:'Sora',sans-serif;font-size:0.76rem;font-weight:600;text-decoration:none;cursor:pointer;transition:all 0.15s;white-space:nowrap}
.sd-ref-link:hover{border-color:#c8292a;color:#c8292a;background:#fff5f5}
.sd-ref-dot{width:6px;height:6px;border-radius:50%;flex-shrink:0}

.sd-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:24px}
@media(max-width:700px){.sd-stats{grid-template-columns:1fr}}
.sd-stat{background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:16px 18px;display:flex;align-items:flex-start;gap:12px;position:relative;overflow:hidden;transition:box-shadow 0.15s}
.sd-stat:hover{box-shadow:0 4px 20px rgba(0,0,0,0.07)}
.sd-stat::after{content:'';position:absolute;bottom:0;left:0;right:0;height:3px;border-radius:0 0 14px 14px}
.sd-stat.s-green::after{background:#16a34a}.sd-stat.s-blue::after{background:#0284c7}.sd-stat.s-red::after{background:#c8292a}
.sd-stat-icon{width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.sd-stat.s-green .sd-stat-icon{background:#f0fdf4;color:#16a34a}
.sd-stat.s-blue  .sd-stat-icon{background:#f0f9ff;color:#0284c7}
.sd-stat.s-red   .sd-stat-icon{background:#fff0f0;color:#c8292a}
.sd-stat-label{font-size:0.67rem;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#9ca3af;margin-bottom:4px}
.sd-stat-value{font-size:1.5rem;font-weight:800;color:#111827;line-height:1;font-family:'DM Mono',monospace}
.sd-stat-sub{font-size:0.72rem;color:#9ca3af;margin-top:3px}

.sd-layout{display:grid;grid-template-columns:1fr 240px;gap:16px;align-items:start}
@media(max-width:900px){.sd-layout{grid-template-columns:1fr}}

.sd-section-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;gap:10px}
.sd-section-title{font-size:0.88rem;font-weight:700;color:#111827;margin:0;display:flex;align-items:center;gap:8px}
.sd-dot{width:8px;height:8px;border-radius:50%;background:#c8292a;display:inline-block}

.sd-filter-bar{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:10px 14px;display:flex;align-items:center;gap:10px;margin-bottom:14px;flex-wrap:wrap}
.sd-search-wrap{position:relative;flex:1;min-width:160px}
.sd-search-wrap svg{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#9ca3af;pointer-events:none}
.sd-search-input{width:100%;border:1px solid #e5e7eb;border-radius:8px;padding:7px 10px 7px 32px;font-size:0.80rem;font-family:'Sora',sans-serif;color:#111827;background:#f9fafb;outline:none;transition:border-color 0.15s}
.sd-search-input:focus{border-color:#c8292a;background:#fff;box-shadow:0 0 0 3px rgba(200,41,42,0.08)}
.sd-filter-select{border:1px solid #e5e7eb;border-radius:8px;padding:7px 10px;font-size:0.80rem;font-family:'Sora',sans-serif;color:#374151;background:#f9fafb;outline:none;cursor:pointer}
.sd-filter-select:focus{border-color:#c8292a}

.sd-table-card{background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden}
.sd-table-scroll{overflow-x:auto;max-height:560px;overflow-y:auto}
.sd-table{width:100%;border-collapse:collapse;font-size:0.825rem}
.sd-table thead{position:sticky;top:0;z-index:2}
.sd-table thead tr{background:#f8f9fb;border-bottom:1px solid #e5e7eb}
.sd-table thead th{padding:10px 14px;font-size:0.66rem;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#6b7280;white-space:nowrap}
.sd-table tbody tr{border-bottom:1px solid #f3f4f6;transition:background 0.1s}
.sd-table tbody tr:last-child{border-bottom:none}
.sd-table tbody tr:hover{background:#fafafa}
.sd-table tbody td{padding:10px 14px;color:#374151;vertical-align:middle}

.sd-group-sep td{padding:6px 14px 4px;background:#f8f9fb;border-bottom:1px solid #e5e7eb;border-top:1px solid #e5e7eb}
.sd-group-label{font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;display:flex;align-items:center;gap:6px}
.sd-group-label-swatch{width:7px;height:7px;border-radius:50%}
.sd-group-label-text{color:#6b7280}
.sd-group-count{font-family:'DM Mono',monospace;font-size:0.62rem;background:#e5e7eb;color:#6b7280;padding:1px 6px;border-radius:10px;font-weight:500}

.sd-name-cell{display:flex;align-items:center;gap:10px}
.sd-name-icon{width:28px;height:28px;border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;flex-shrink:0;font-family:'Sora',sans-serif}
.sd-name-icon.pagibig{background:#f0fdf4;color:#16a34a}
.sd-name-icon.sss{background:#f0f9ff;color:#0284c7}
.sd-name-icon.phil{background:#fff0f0;color:#c8292a}
.sd-name-text{font-weight:600;color:#111827;font-size:0.835rem}

.sd-mono{font-family:'DM Mono',monospace;font-size:0.80rem;font-variant-numeric:tabular-nums;color:#374151}
.sd-mono.null{color:#d1d5db}
.sd-range{display:flex;align-items:center;gap:4px;white-space:nowrap}
.sd-range-sep{color:#d1d5db;font-size:0.70rem}

.sd-val-cell{display:flex;flex-direction:column;gap:3px;align-items:flex-start}
.sd-pct-pill{display:inline-flex;align-items:center;padding:1px 8px;border-radius:20px;font-family:'DM Mono',monospace;font-size:0.73rem;font-weight:500;white-space:nowrap}
.sd-pct-pill.emp{background:#fff0f0;color:#c8292a}
.sd-pct-pill.er{background:#f0f9ff;color:#0284c7}
.sd-pct-pill.null{background:#f3f4f6;color:#9ca3af}
.sd-sub-line{font-family:'DM Mono',monospace;font-size:0.70rem;color:#9ca3af}
.sd-note-badge{display:inline-flex;align-items:center;gap:4px;background:#fffbeb;border:1px solid #fde68a;color:#92400e;border-radius:6px;padding:2px 7px;font-size:0.66rem;font-weight:600;white-space:nowrap}

.sd-side-card{background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden}
.sd-side-head{padding:13px 16px;border-bottom:1px solid #f3f4f6;font-size:0.82rem;font-weight:700;color:#111827}
.sd-legend-item{display:flex;align-items:flex-start;gap:10px;padding:11px 16px;border-bottom:1px solid #f3f4f6}
.sd-legend-dot{width:9px;height:9px;border-radius:50%;flex-shrink:0;margin-top:3px}
.sd-legend-label{font-size:0.78rem;font-weight:700;color:#111827}
.sd-legend-sub{font-size:0.70rem;color:#9ca3af;margin-top:2px;line-height:1.4}
.sd-key-card{margin:10px;border:1px solid #e5e7eb;border-radius:10px;overflow:hidden}
.sd-key-head{padding:8px 12px;background:#f8f9fb;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#6b7280;border-bottom:1px solid #e5e7eb}
.sd-key-row{display:flex;align-items:center;justify-content:space-between;padding:7px 12px;border-bottom:1px solid #f3f4f6;font-size:0.75rem}
.sd-key-row:last-child{border-bottom:none}
.sd-key-label{color:#6b7280}
.sd-info-box{margin:10px;padding:11px 13px;background:#fffbeb;border:1px solid #fde68a;border-radius:10px}
.sd-info-box-title{font-size:0.70rem;font-weight:700;color:#92400e;margin-bottom:4px;display:flex;align-items:center;gap:5px}
.sd-info-box-body{font-size:0.70rem;color:#78350f;line-height:1.5}
.sd-empty{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:56px 24px;text-align:center}
.sd-empty p:first-child{font-weight:700;color:#374151;font-size:0.9rem;margin-bottom:4px}
.sd-empty p:last-child{color:#9ca3af;font-size:0.78rem}
</style>
@endpush

@section('content')
@php
    $grouped = $deductions->groupBy('name');
    $counts  = $grouped->map->count();

    function sdIcon(string $name): string {
        return match(true) {
            str_contains($name,'Pag')  => 'pagibig',
            str_contains($name,'SSS')  => 'sss',
            default                    => 'phil',
        };
    }
    function sdAbbr(string $name): string {
        return match(true) {
            str_contains($name,'Pag') => 'P',
            str_contains($name,'SSS') => 'S',
            default                   => 'PH',
        };
    }
    function sdSwatch(string $name): string {
        return match(true) {
            str_contains($name,'Pag') => '#16a34a',
            str_contains($name,'SSS') => '#0284c7',
            default                   => '#c8292a',
        };
    }
    function sdMaxSalary(float $max): string {
        return $max >= 999999 ? '+' : '– ₱'.number_format($max,2);
    }
@endphp

<div class="sd-page">

    <div class="sd-topbar">
        <div>
            <h1 class="sd-topbar-title">Statutory Deductions</h1>
            <p class="sd-topbar-sub">Government-mandated contribution schedules for payroll computation</p>
        </div>
        <div class="sd-refs">
            <a href="https://mpm.ph/pagibig-hdmf-table/" target="_blank" class="sd-ref-link">
                <span class="sd-ref-dot" style="background:#16a34a"></span>Pag-IBIG Table
                <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
            <a href="https://www.sss.gov.ph/sss-contribution-table/" target="_blank" class="sd-ref-link">
                <span class="sd-ref-dot" style="background:#0284c7"></span>SSS Table
                <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
            <a href="https://www.philhealth.gov.ph/advisories/2025/PA2025-0002.pdf" target="_blank" class="sd-ref-link">
                <span class="sd-ref-dot" style="background:#c8292a"></span>PhilHealth Table
                <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
        </div>
    </div>

    @php
        $pagibigMax = $deductions->where('name','Pag-IBIG')->max('percentage_employee');
        $sssMax     = $deductions->where('name','SSS')->max('employee_share');
        $philMax    = $deductions->where('name','PhilHealth')->max('percentage_employee');
    @endphp
    <div class="sd-stats">
        <div class="sd-stat s-green">
            <div class="sd-stat-icon">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            </div>
            <div>
                <div class="sd-stat-label">Pag-IBIG (HDMF)</div>
                <div class="sd-stat-value">{{ $pagibigMax ?? 2 }}%</div>
                <div class="sd-stat-sub">ee rate · max ₱100/mo</div>
            </div>
        </div>
        <div class="sd-stat s-blue">
            <div class="sd-stat-icon">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <div>
                <div class="sd-stat-label">SSS</div>
                <div class="sd-stat-value">₱{{ number_format($sssMax ?? 1000, 2) }}</div>
                <div class="sd-stat-sub">max ee share · {{ $counts['SSS'] ?? 0 }} brackets</div>
            </div>
        </div>
        <div class="sd-stat s-red">
            <div class="sd-stat-icon">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            </div>
            <div>
                <div class="sd-stat-label">PhilHealth (PHIL)</div>
                <div class="sd-stat-value">{{ $philMax ?? 2.5 }}%</div>
                <div class="sd-stat-sub">ee share · 5% total premium</div>
            </div>
        </div>
    </div>

    <div class="sd-layout">
        <div>
            <div class="sd-section-head">
                <h2 class="sd-section-title"><span class="sd-dot"></span> Contribution Table</h2>
            </div>
            <div class="sd-filter-bar">
                <div class="sd-search-wrap">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
                    <input type="text" class="sd-search-input" id="sdSearch" placeholder="Search by type…">
                </div>
                <select class="sd-filter-select" id="sdFilter">
                    <option value="">All Types</option>
                    @foreach($grouped->keys() as $gname)
                        <option value="{{ $gname }}">{{ $gname }}</option>
                    @endforeach
                </select>
            </div>

            <div class="sd-table-card">
                @if($deductions->count())
                <div class="sd-table-scroll">
                    <table class="sd-table">
                        <thead><tr>
                            <th>Type</th>
                            <th>Salary Range</th>
                            <th>Employee Share</th>
                            <th>Employer Share</th>
                        </tr></thead>
                        <tbody id="sdTbody">
                        @foreach($grouped as $gname => $rows)
                            @php
                                $swatch = sdSwatch($gname);
                                $icon   = sdIcon($gname);
                                $abbr   = sdAbbr($gname);
                            @endphp
                            <tr class="sd-group-sep" data-group="{{ $gname }}">
                                <td colspan="4">
                                    <div class="sd-group-label">
                                        <span class="sd-group-label-swatch" style="background:{{ $swatch }}"></span>
                                        <span class="sd-group-label-text">{{ $gname }}</span>
                                        <span class="sd-group-count">{{ $rows->count() }} {{ Str::plural('bracket', $rows->count()) }}</span>
                                    </div>
                                </td>
                            </tr>
                            @foreach($rows as $d)
                            @php
                                $isMaxed = $d->max_salary >= 999999;
                                $hasEeFixed = $d->employee_share !== null;
                                $hasErFixed = $d->employer_share !== null;
                                $hasEePct   = $d->percentage_employee !== null;
                                $hasErPct   = $d->percentage_employer !== null;
                                $isLastSss  = $gname === 'SSS' && $isMaxed;
                            @endphp
                            <tr data-type="{{ $gname }}">
                                <td>
                                    <div class="sd-name-cell">
                                        <div class="sd-name-icon {{ $icon }}">{{ $abbr }}</div>
                                        <div class="sd-name-text">{{ $gname }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="sd-range">
                                        <span class="sd-mono">₱{{ number_format($d->min_salary, 2) }}</span>
                                        <span class="sd-range-sep">{{ $isMaxed ? '+' : '–' }}</span>
                                        @unless($isMaxed)
                                            <span class="sd-mono">₱{{ number_format($d->max_salary, 2) }}</span>
                                        @endunless
                                    </div>
                                </td>
                                {{-- Employee share cell --}}
                                <td>
                                    <div class="sd-val-cell">
                                        @if($hasEeFixed)
                                            <span class="sd-mono">₱{{ number_format($d->employee_share, 2) }}</span>
                                            @if($isLastSss)
                                                <span class="sd-note-badge">
                                                    <svg width="9" height="9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01"/><circle cx="12" cy="12" r="10"/></svg>
                                                    Max · MPF applies
                                                </span>
                                            @elseif($hasEePct)
                                                <span class="sd-sub-line">or {{ $d->percentage_employee }}% if higher</span>
                                            @endif
                                        @elseif($hasEePct)
                                            <span class="sd-pct-pill emp">{{ $d->percentage_employee }}% of salary</span>
                                            @if($gname === 'Pag-IBIG' && $d->min_salary > 1500)
                                                <span class="sd-sub-line">capped at ₱100/mo</span>
                                            @endif
                                        @else
                                            <span class="sd-mono null">—</span>
                                        @endif
                                    </div>
                                </td>
                                {{-- Employer share cell --}}
                                <td>
                                    <div class="sd-val-cell">
                                        @if($hasErFixed)
                                            <span class="sd-mono">₱{{ number_format($d->employer_share, 2) }}</span>
                                            @if($hasErPct)
                                                <span class="sd-sub-line">or {{ $d->percentage_employer }}% if higher</span>
                                            @endif
                                        @elseif($hasErPct)
                                            <span class="sd-pct-pill er">{{ $d->percentage_employer }}% of salary</span>
                                        @else
                                            <span class="sd-mono null">—</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div id="sdNoResults" style="display:none">
                    <div class="sd-empty"><p>No results found</p><p>Try a different filter.</p></div>
                </div>
                @else
                    <div class="sd-empty">
                        <p>No statutory deductions configured</p>
                        <p>Run the seeder to populate contribution schedules.</p>
                    </div>
                @endif
            </div>
        </div>

        <div>
            <div class="sd-section-head">
                <h2 class="sd-section-title"><span class="sd-dot"></span> Quick Reference</h2>
            </div>
            <div class="sd-side-card">
                <div class="sd-side-head">Contribution Types</div>
                <div class="sd-legend-item">
                    <div class="sd-legend-dot" style="background:#0284c7"></div>
                    <div><div class="sd-legend-label">SSS</div><div class="sd-legend-sub">Fixed amount per bracket · {{ $counts['SSS'] ?? 0 }} salary ranges · max ₱1,000 ee</div></div>
                </div>
                <div class="sd-legend-item">
                    <div class="sd-legend-dot" style="background:#16a34a"></div>
                    <div><div class="sd-legend-label">Pag-IBIG (HDMF)</div><div class="sd-legend-sub">% of salary · capped at ₱100/mo ee share</div></div>
                </div>
                <div class="sd-legend-item" style="border-bottom:none">
                    <div class="sd-legend-dot" style="background:#c8292a"></div>
                    <div><div class="sd-legend-label">PhilHealth (PHIL)</div><div class="sd-legend-sub">Mixed — fixed floor/ceiling, % in between · 5% total split equally</div></div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
(function(){
    const s=document.getElementById('sdSearch'),f=document.getElementById('sdFilter');
    const tbody=document.getElementById('sdTbody'),nr=document.getElementById('sdNoResults');
    if(!s||!tbody) return;
    function run(){
        const q=s.value.toLowerCase().trim(),tp=f.value;
        const dataRows=Array.from(tbody.querySelectorAll('tr[data-type]'));
        const sepRows=Array.from(tbody.querySelectorAll('tr[data-group]'));
        dataRows.forEach(r=>{
            const match=(!q||r.dataset.type.toLowerCase().includes(q))&&(!tp||r.dataset.type===tp);
            r.style.display=match?'':'none';
        });
        sepRows.forEach(sep=>{
            const group=sep.dataset.group;
            const anyVisible=dataRows.some(r=>r.dataset.type===group&&r.style.display!=='none');
            sep.style.display=anyVisible?'':'none';
        });
        nr.style.display=dataRows.every(r=>r.style.display==='none')?'block':'none';
    }
    s.addEventListener('input',run);
    f.addEventListener('change',run);
})();
</script>
@endpush
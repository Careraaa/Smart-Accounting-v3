@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
*,*::before,*::after{box-sizing:border-box}
.ep-page{font-family:'Sora',sans-serif;background:#f8f9fb;min-height:100vh}
.ep-wrap{max-width:900px;margin:0 auto;padding:0 0 64px}
.ep-back{display:inline-flex;align-items:center;gap:7px;font-size:.8rem;font-weight:700;color:#6b7280;text-decoration:none;margin-bottom:20px;padding:8px 14px;background:#fff;border:1px solid #e5e7eb;border-radius:9px;transition:all .13s}
.ep-back:hover{color:#c8292a;border-color:#c8292a;background:#fff5f5}
.ep-hero{background:#111827;border-radius:16px;padding:22px 26px;display:flex;align-items:center;gap:18px;margin-bottom:20px;position:relative;overflow:hidden}
.ep-hero::before{content:'';position:absolute;top:-40px;right:-40px;width:160px;height:160px;border-radius:50%;background:rgba(200,41,42,.1);pointer-events:none}
.ep-hero-avatar{width:52px;height:52px;border-radius:50%;background:rgba(255,255,255,.1);border:2px solid rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;font-size:1rem;font-weight:800;color:#fff;flex-shrink:0;text-transform:uppercase;position:relative;z-index:1}
.ep-hero-info{flex:1;position:relative;z-index:1}
.ep-hero-name{font-size:1.05rem;font-weight:800;color:#fff;margin:0 0 3px;letter-spacing:-.02em}
.ep-hero-meta{font-size:.75rem;color:#6b7280}
.ep-hero-period{position:relative;z-index:1;font-family:'DM Mono',monospace;font-size:.75rem;color:#9ca3af;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);padding:6px 12px;border-radius:8px;white-space:nowrap}
.ep-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px}
@media(max-width:680px){.ep-grid{grid-template-columns:1fr}}
.ep-card{background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:visible;display:flex;flex-direction:column}
.ep-card-head{padding:13px 18px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;gap:10px}
.ep-card-icon{width:28px;height:28px;border-radius:7px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.ep-card-icon.amber{background:#fffbeb;color:#d97706}
.ep-card-icon.green{background:#f0fdf4;color:#16a34a}
.ep-card-icon.red{background:#fff0f0;color:#c8292a}
.ep-card-icon.purple{background:#f5f3ff;color:#7c3aed}
.ep-card-title{font-size:.82rem;font-weight:700;color:#111827;margin:0}
.ep-card-sub{font-size:.68rem;color:#9ca3af;margin:0}
.ep-card-body{padding:16px 18px}
.ep-chips{display:flex;gap:10px}
@media(max-width:480px){.ep-chips{flex-wrap:wrap}}
.ep-chip{flex:1;background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;padding:11px 12px;text-align:center}
.ep-chip-lbl{display:block;font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#9ca3af;margin-bottom:3px}
.ep-chip-val{font-size:1.1rem;font-weight:800;color:#111827;font-variant-numeric:tabular-nums;font-family:'DM Mono',monospace}
.ep-chip-val.red{color:#dc2626}
.ep-rows{display:flex;flex-direction:column}
.ep-row{display:flex;align-items:center;justify-content:space-between;padding:9px 0;border-bottom:1px solid #f3f4f6;gap:8px}
.ep-row:last-child{border-bottom:none;padding-bottom:0}
.ep-row-lbl{font-size:.82rem;color:#6b7280;display:flex;align-items:center;gap:6px;flex:1}
.ep-row-lbl.green{color:#16a34a}
.ep-row-lbl.red{color:#dc2626}
.ep-row-lbl.bold{color:#111827;font-weight:700;font-size:.88rem}
.ep-badge{font-size:.62rem;font-weight:600;background:#f3f4f6;color:#6b7280;border-radius:4px;padding:2px 5px;white-space:nowrap}
.ep-row-val{font-size:.88rem;font-weight:700;font-variant-numeric:tabular-nums;font-family:'DM Mono',monospace;color:#111827;white-space:nowrap}
.ep-row-val.green{color:#16a34a}
.ep-row-val.red{color:#dc2626}
.ep-empty-hint{font-size:.75rem;color:#d1d5db;font-style:italic}
.ep-add-row{display:flex;gap:8px;align-items:stretch;margin-bottom:10px}
.ep-fi{border:1px solid #d1d5db;border-radius:8px;padding:9px 11px;font-size:.82rem;color:#111827;background:#fff;transition:border-color .15s,box-shadow .15s;font-family:'Sora',sans-serif}
.ep-fi:focus{border-color:#c8292a;box-shadow:0 0 0 3px rgba(200,41,42,.1);outline:none}
.ep-fi-grow{flex:1 1 0;min-width:0}
.ep-fi-w120{width:120px;flex-shrink:0}
.ep-fi-select{appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 10px center;padding-right:28px}
@media(max-width:560px){.ep-add-row{flex-wrap:wrap}.ep-fi-w120{width:100%}}
.ep-btn-add{flex-shrink:0;padding:0 14px;border:none;border-radius:8px;font-size:.78rem;font-weight:700;cursor:pointer;transition:background .15s;white-space:nowrap;color:#fff;font-family:'Sora',sans-serif;display:inline-flex;align-items:center;gap:5px;height:38px}
.ep-btn-add.green{background:#16a34a}
.ep-btn-add.green:hover{background:#15803d}
.ep-btn-add.red{background:#dc2626}
.ep-btn-add.red:hover{background:#b91c1c}
.ep-btn-add.purple{background:#7c3aed}
.ep-btn-add.purple:hover{background:#6d28d9}
.ep-pills{display:flex;flex-wrap:wrap;gap:7px;min-height:32px;padding:2px 0 6px}
.ep-pill{display:inline-flex;align-items:center;gap:5px;padding:5px 8px 5px 10px;border-radius:999px;font-size:.75rem;font-weight:600;animation:pillIn .15s ease}
@keyframes pillIn{from{transform:scale(.75);opacity:0}to{transform:scale(1);opacity:1}}
.ep-pill.green{background:#dcfce7;color:#15803d;border:1px solid #bbf7d0}
.ep-pill.red{background:#fee2e2;color:#b91c1c;border:1px solid #fecaca}
.ep-pill.purple{background:#f5f3ff;color:#6d28d9;border:1px solid #ddd6fe}
.ep-pill-text{max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.ep-pill-amt{font-variant-numeric:tabular-nums;font-family:'DM Mono',monospace;opacity:.85}
.ep-pill-rm{width:15px;height:15px;border-radius:50%;border:none;display:flex;align-items:center;justify-content:center;font-size:11px;cursor:pointer;padding:0;flex-shrink:0;opacity:.55;transition:opacity .12s}
.ep-pill-rm:hover{opacity:1}
.ep-pill.green .ep-pill-rm{background:#bbf7d0;color:#15803d}
.ep-pill.red .ep-pill-rm{background:#fecaca;color:#b91c1c}
.ep-pill.purple .ep-pill-rm{background:#ddd6fe;color:#6d28d9}
.ep-subtotal{font-size:.78rem;font-weight:600;display:flex;justify-content:flex-end;gap:5px;padding-top:2px;font-variant-numeric:tabular-nums;font-family:'DM Mono',monospace}
.ep-subtotal.green{color:#16a34a}
.ep-subtotal.red{color:#dc2626}
.ep-subtotal.purple{color:#7c3aed}
.ep-net{background:#111827;border-radius:14px;padding:20px 24px;display:flex;align-items:center;justify-content:space-between;margin-top:16px}
.ep-net-lbl{font-size:.7rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#6b7280}
.ep-net-val{font-size:1.6rem;font-weight:800;color:#fff;font-variant-numeric:tabular-nums;letter-spacing:-.02em;font-family:'DM Mono',monospace}
.ep-footer{display:flex;gap:10px;margin-top:20px;padding-top:20px;border-top:1px solid #f3f4f6;flex-wrap:wrap;justify-content:flex-end}
.ep-btn-save{display:inline-flex;align-items:center;gap:8px;padding:11px 24px;background:#c8292a;color:#fff;border:none;border-radius:10px;font-family:'Sora',sans-serif;font-size:.855rem;font-weight:700;cursor:pointer;transition:background .15s,box-shadow .15s;box-shadow:0 4px 14px rgba(200,41,42,.25)}
.ep-btn-save:hover{background:#a81f20;box-shadow:0 6px 20px rgba(200,41,42,.35);color:#fff}
.ep-btn-cancel{display:inline-flex;align-items:center;gap:7px;padding:10px 18px;background:#fff;color:#374151;border:1px solid #e5e7eb;border-radius:10px;font-family:'Sora',sans-serif;font-size:.845rem;font-weight:600;text-decoration:none;transition:background .13s,border-color .13s}
.ep-btn-cancel:hover{background:#f9fafb;border-color:#d1d5db;color:#374151}
.ep-holiday-item{display: flex; align-items: center; justify-content: space-between;  padding: 9px 0; border-bottom: 1px solid #f3f4f6 !important; gap: 8px;}
#ep_holiday_rows{display:contents;}
</style>
@endpush
@section('content')
<div class="ep-page">
<div class="ep-wrap">

    <a href="{{ route('payroll.batch.confirm', $batch) }}" class="ep-back">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Back to Batch
    </a>

    @php $initials = strtoupper(substr($payroll->user->first_name??'U',0,1).substr($payroll->user->last_name??'',0,1)); @endphp
    <div class="ep-hero">
        <div class="ep-hero-avatar">{{ $initials }}</div>
        <div class="ep-hero-info">
            <div class="ep-hero-name">{{ $payroll->user->first_name }} {{ $payroll->user->last_name }}</div>
            <div class="ep-hero-meta">{{ $payroll->user->position ?? ($payroll->user->department ?? 'N/A') }} &nbsp;&middot;&nbsp; Edit Payroll</div>
        </div>
        <div class="ep-hero-period">{{ $batch->period_start->format('M d') }} &ndash; {{ $batch->period_end->format('M d, Y') }}</div>
    </div>

    <form id="ep-form" action="{{ route('payroll.batch.update-employee', [$batch, $payroll]) }}" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" id="ep_user_id"      value="{{ $payroll->user_id }}">
        <input type="hidden" id="ep_period_start" value="{{ $batch->period_start->format('Y-m-d') }}">
        <input type="hidden" id="ep_period_end"   value="{{ $batch->period_end->format('Y-m-d') }}">

        {{-- Row 1: Attendance + Salary --}}
        <div class="ep-grid">
            <div class="ep-card">
                <div class="ep-card-head" style="justify-content:space-between;">
                    <div style="display:flex;align-items:center;gap:10px;flex:1;">
                        <div class="ep-card-icon amber">
                            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                        </div>
                        <div><p class="ep-card-title">Attendance</p><p class="ep-card-sub">Live from records</p></div>
                    </div>
                    <a href="{{ route('attendance.employee.calendar', $payroll->user_id) }}" target="_blank" style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;background:#f3f4f6;color:#6b7280;border:1px solid #e5e7eb;border-radius:6px;text-decoration:none;font-size:.75rem;font-weight:600;transition:all .13s;white-space:nowrap;flex-shrink:0;" onmouseover="this.style.background='#e5e7eb';this.style.color='#374151';" onmouseout="this.style.background='#f3f4f6';this.style.color='#6b7280';">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                       Attendance Record
                    </a>
                </div>
                <div class="ep-card-body">
                    <div class="ep-chips">
                        <div class="ep-chip"><span class="ep-chip-lbl">Days Worked</span><span class="ep-chip-val" id="ep_days_worked">{{ $payroll->days_worked ?? '—' }}</span></div>
                        <div class="ep-chip"><span class="ep-chip-lbl">Hours</span><span class="ep-chip-val" id="ep_hours_worked">{{ $payroll->hours_worked ?? '—' }}</span></div>
                        <div class="ep-chip"><span class="ep-chip-lbl">Absent</span><span class="ep-chip-val red" id="ep_days_absent">—</span></div>
                    </div>
                </div>
            </div>

            <div class="ep-card">
                <div class="ep-card-head">
                    <div class="ep-card-icon green">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div><p class="ep-card-title">Salary Computation</p><p class="ep-card-sub">Earnings and Deductions</p></div>
                </div>
                <div class="ep-card-body">
                    <div class="ep-rows">
                        <div class="ep-row">
                            <span class="ep-row-lbl">Basic Pay <span class="ep-badge" id="ep_basic_badge">rate &times; days</span></span>
                            <span class="ep-row-val" id="ep_basic_display">&#8369;{{ number_format($payroll->basic_salary??0,2) }}</span>
                        </div>
                        <div id="ep_holiday_rows">
                            {{-- One row per holiday, populated on page load and on preview refresh --}}
                            @php
                                $holidayAllowances = $payroll->allowances
                                    ->filter(fn($a) => str_starts_with((string)($a->allowance_type ?? ''), 'Holiday Pay'));
                                // For old "Holiday Pay" records, look up names from the period
                                $batchPeriodHolidayNames = \App\Models\Holiday::whereBetween('date', [
                                    $payroll->payroll_period_start->toDateString(),
                                    $payroll->payroll_period_end->toDateString(),
                                ])->orderBy('date')->pluck('name');
                            @endphp
                            @foreach($holidayAllowances as $ha)
                            @php
                                $haLabel = $ha->allowance_type ?? '';
                                $haBadge = 'holiday';
                                if (preg_match('/^Holiday Pay\s*[—\-]+\s*(.+?)\s*\((.+)\)$/', $haLabel, $hm)) {
                                    $haName  = 'Holiday Pay — ' . $hm[1];
                                    $haBadge = $hm[2];
                                } else {
                                    $haName = 'Holiday Pay — ' . ($batchPeriodHolidayNames->isNotEmpty()
                                        ? $batchPeriodHolidayNames->implode(' + ')
                                        : 'Holiday');
                                }
                            @endphp
                            <div class="ep-row ep-holiday-item">
                                <span class="ep-row-lbl green">
                                    {{ $haName }}
                                    <span class="ep-badge">{{ $haBadge }}</span>
                                </span>
                                <span class="ep-row-val green">+&#8369;{{ number_format($ha->amount, 2) }}</span>
                            </div>
                            @endforeach
                        </div>
                        <div class="ep-row" id="ep_ot_row" style="display:none;">
                            <span class="ep-row-lbl green">Overtime Pay <span class="ep-badge" id="ep_ot_hrs_badge"></span></span>
                            <span class="ep-row-val green" id="ep_ot_display">+&#8369;0.00</span>
                        </div>
                        <div class="ep-row" id="ep_holiday_ot_row" style="display:none;">
                            <span class="ep-row-lbl green">Holiday Overtime Pay <span class="ep-badge" id="ep_holiday_ot_hrs_badge"></span></span>
                            <span class="ep-row-val green" id="ep_holiday_ot_display">+&#8369;0.00</span>
                        </div>
                        <div class="ep-row" id="ep_ut_row" style="display:none;">
                            <span class="ep-row-lbl red">Undertime Deduction <span class="ep-badge" id="ep_ut_hrs_badge"></span></span>
                            <span class="ep-row-val red" id="ep_ut_display">-&#8369;0.00</span>
                        </div>
                        <div class="ep-row" id="ep_late_row" style="display:none;">
                            <span class="ep-row-lbl red">Tardiness <span class="ep-badge" id="ep_late_mins_badge"></span></span>
                            <span class="ep-row-val red" id="ep_late_display">-&#8369;0.00</span>
                        </div>
                        <div id="ep_gov_contrib_rows">
                            {{-- Individual government contribution rows populated on page load and preview refresh --}}
                        </div>
                        <div class="ep-row">
                            <span class="ep-row-lbl bold">Initial Net Pay</span>
                            <span class="ep-row-val" id="ep_adjusted" style="font-size:.95rem;">&#8369;{{ number_format($payroll->gross_pay??0,2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 2: Allowances + Deductions --}}
        <div class="ep-grid">
            <div class="ep-card">
                <div class="ep-card-head">
                    <div class="ep-card-icon green">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <div><p class="ep-card-title">Allowances <span style="font-weight:400;color:#9ca3af;font-size:.68rem;">(optional)</span></p><p class="ep-card-sub">Transportation, meal, housing&hellip;</p></div>
                </div>
                <div class="ep-card-body">
                    <div class="ep-add-row">
                        <input type="text"   id="ep_allow_name"   class="ep-fi ep-fi-grow" placeholder="Label &mdash; e.g. Transportation">
                        <input type="number" id="ep_allow_amount" class="ep-fi ep-fi-w120" placeholder="0.00" min="0.01" step="0.01">
                        <button type="button" class="ep-btn-add green" id="ep_allow_btn">
                            <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg> Add
                        </button>
                    </div>
                    <div class="ep-pills" id="ep_allow_pills"><span style="font-size:.75rem;color:#d1d5db;font-style:italic;" id="ep_allow_empty">None added yet.</span></div>
                    <div class="ep-subtotal green" id="ep_allow_subtotal" style="display:none;">Total: &#8369;<span id="ep_allow_total">0.00</span></div>
                    <div id="ep_allow_hidden"></div>
                </div>
            </div>

            <div class="ep-card">
                <div class="ep-card-head">
                    <div class="ep-card-icon red">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
                    </div>
                    <div><p class="ep-card-title">Additional Deductions <span style="font-weight:400;color:#9ca3af;font-size:.68rem;">(optional)</span></p><p class="ep-card-sub">Penalty, medical, savings&hellip;</p></div>
                </div>
                <div class="ep-card-body">
                    <div class="ep-add-row">
                        <input type="text"   id="ep_deduct_name"   class="ep-fi ep-fi-grow" placeholder="Label &mdash; e.g. Cash Advance">
                        <input type="number" id="ep_deduct_amount" class="ep-fi ep-fi-w120" placeholder="0.00" min="0.01" step="0.01">
                        <button type="button" class="ep-btn-add red" id="ep_deduct_btn">
                            <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg> Add
                        </button>
                    </div>
                    <div class="ep-pills" id="ep_deduct_pills"><span style="font-size:.75rem;color:#d1d5db;font-style:italic;" id="ep_deduct_empty">None added yet.</span></div>
                    <div class="ep-subtotal red" id="ep_deduct_subtotal" style="display:none;">Total: &#8369;<span id="ep_deduct_total">0.00</span></div>
                    <div id="ep_deduct_hidden"></div>
                </div>
            </div>
        </div>

        {{-- Bonuses --}}
        <div class="ep-card" style="margin-bottom:16px;">
            <div class="ep-card-head">
                <div class="ep-card-icon purple">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
                </div>
                <div>
                    <p class="ep-card-title">Bonuses <span style="font-weight:400;color:#9ca3af;font-size:.68rem;">(optional)</span></p>
                    <p class="ep-card-sub">Performance and other special bonuses</p>
                </div>
            </div>
            <div class="ep-card-body">
                <div class="ep-add-row">
                    <select id="ep_bonus_type" class="ep-fi ep-fi-select" style="width:175px;flex-shrink:0;">
                        <option value="">&#8212; Bonus type &#8212;</option>
                        <option value="performance">Performance</option>
                        <option value="holiday">Holiday</option>
                        <option value="attendance">Attendance</option>
                        <option value="special">Special</option>
                    </select>
                    <input type="text"   id="ep_bonus_desc"   class="ep-fi ep-fi-grow" placeholder="Description (optional)">
                    <input type="number" id="ep_bonus_amount" class="ep-fi ep-fi-w120" placeholder="0.00" min="0.01" step="0.01">
                    <button type="button" class="ep-btn-add purple" id="ep_bonus_btn">
                        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg> Add
                    </button>
                </div>
                <div class="ep-pills" id="ep_bonus_pills"><span style="font-size:.75rem;color:#d1d5db;font-style:italic;" id="ep_bonus_empty">No bonuses added yet.</span></div>
                <div class="ep-subtotal purple" id="ep_bonus_subtotal" style="display:none;">Total bonuses: &#8369;<span id="ep_bonus_total">0.00</span></div>
                <div id="ep_bonus_hidden"></div>
            </div>
        </div>

        {{-- Net Salary --}}
        <div class="ep-net">
            <div>
                <div class="ep-net-lbl">Net Pay</div>
                <div style="font-size:.7rem;color:#4b5563;margin-top:2px;">Initial Net Pay + Allowances &minus; Additional Deductions + Bonuses</div>
            </div>
            <span class="ep-net-val" id="ep_net_salary">&#8369;{{ number_format($payroll->net_pay??0,2) }}</span>
        </div>

        <div class="ep-footer">
            <button type="submit" class="ep-btn-save">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Save &amp; Mark as Prepared
            </button>
        </div>
    </form>
</div>
</div>
@endsection

@push('head_scripts')
<meta name="page-id" content="payroll-batch-edit">
@php
    $manualAllowances = $payroll->allowances
        ->filter(fn($a) => !str_starts_with((string)($a->allowance_type ?? $a->name ?? ''), 'Overtime Pay')
                        && !str_starts_with((string)($a->allowance_type ?? $a->name ?? ''), 'Holiday Pay'))
        ->map(fn($a) => ['name' => $a->allowance_type ?? $a->name, 'amount' => $a->amount])
        ->values();
    $manualDeductions = $payroll->deductions
        ->filter(function($d) {
            $type = (string)($d->deduction_type ?? $d->name ?? '');
            return !in_array($type, ['SSS','Pag-IBIG','PhilHealth','Withholding Tax'])
                && !str_starts_with($type, 'Undertime Deduction')
                && !str_starts_with($type, 'Late Deduction');
        })
        ->map(fn($d) => ['name' => $d->deduction_type ?? $d->name, 'amount' => $d->amount])
        ->values();
    $manualBonuses = $payroll->bonuses
        ->map(fn($b) => ['type' => $b->bonus_type, 'description' => $b->description ?? '', 'amount' => $b->amount])
        ->values();
    $manualAllowTotal = $manualAllowances->sum('amount');
    $holidayOTPay = (float)($payroll->allowances->where('allowance_type', 'Holiday Overtime Pay')->sum('amount') ?? 0);
    $holidayOTHours = (float)($payroll->allowances->where('allowance_type', 'Holiday Overtime Pay')->sum('hours') ?? 0);
    $holidayPay = max(0, (float)($payroll->gross_pay ?? 0)
        - (float)($payroll->basic_salary ?? 0)
        - (float)($payroll->overtime_pay ?? 0)
        - $holidayOTPay
        - $manualAllowTotal);
@endphp
<script>
window._ep = {
    previewUrl:    "{{ route('payroll.preview') }}",
    initAllowances: @json($manualAllowances),
    initDeductions: @json($manualDeductions),
    initBonuses:    @json($manualBonuses),
    initComputed: {
        daysWorked:     {{ (float)($payroll->days_worked ?? 0) }},
        hoursWorked:    {{ (float)($payroll->hours_worked ?? 0) }},
        daysAbsent:     0,
        dailyRate:      {{ (float)($payroll->user->salary_rate ?? 0) }},
        basicSalary:    {{ (float)($payroll->basic_salary ?? 0) }},
        adjustedGross:  {{ (float)($payroll->gross_pay ?? 0) }},
        holidayPay:     {{ round($holidayPay, 2) }},
        holidayBreakdown: @json(
            $payroll->allowances
                ->filter(fn($a) => str_starts_with((string)($a->allowance_type ?? ''), 'Holiday Pay'))
                ->map(fn($a) => ['label' => $a->allowance_type, 'amount' => (float)$a->amount])
                ->values()
        ),
        netPay:         {{ (float)($payroll->net_pay ?? 0) }},
        otPay:          {{ (float)($payroll->overtime_pay ?? 0) }},
        otHours:        {{ (float)($payroll->allowances->where('allowance_type', 'like', 'Overtime Pay%')->sum('hours') ?? 0) }},
        utDeduction:    {{ (float)($payroll->undertime_deduction ?? 0) }},
        utHours:        {{ (float)($payroll->deductions->where('deduction_type', 'like', 'Undertime Deduction%')->sum('hours') ?? 0) }},
        late_deduction:  {{ (float)($payroll->deductions->where('deduction_type', 'Late Deduction')->sum('amount') ?? 0) }},
        late_minutes:    {{ (int)($payroll->deductions->where('deduction_type', 'Late Deduction')->first()?->description ? preg_match('/^(\\d+)/', $payroll->deductions->where('deduction_type', 'Late Deduction')->first()?->description, $m) ? $m[1] : 0 : 0) }},
        sss:            {{ (float)($payroll->sss ?? 0) }},
        pagibig:        {{ (float)($payroll->pagibig ?? 0) }},
        philhealth:     {{ (float)($payroll->phil_health ?? $payroll->philhealth ?? 0) }},
        withholdingTax: {{ (float)($payroll->withholding_tax ?? 0) }},
    },
    userId:      {{ $payroll->user_id }},
    periodStart: "{{ $batch->period_start->format('Y-m-d') }}",
    periodEnd:   "{{ $batch->period_end->format('Y-m-d') }}",
};
</script>
@endpush

@push('scripts')
<script>
(function () {
    'use strict';
    const cfg = window._ep;
    const fmt = n => '\u20B1' + Number(n).toLocaleString('en-PH', {minimumFractionDigits:2,maximumFractionDigits:2});
    const $ = id => document.getElementById(id);

    let allowances = [];
    let deductions = [];
    let bonuses    = [];
    let computed   = Object.assign({}, cfg.initComputed);

    const BONUS_LABELS = {performance:'Performance',holiday:'Holiday',attendance:'Attendance',special:'Special'};

    function renderAttendance(c) {
        $('ep_days_worked').textContent  = c.daysWorked  ?? '\u2014';
        $('ep_hours_worked').textContent = c.hoursWorked ?? '\u2014';
        $('ep_days_absent').textContent  = c.daysAbsent  ?? '\u2014';
    }

    function renderSalary(c) {
        $('ep_basic_display').textContent = fmt(c.basicSalary ?? 0);
        // Update the Basic Pay badge to show actual rate × days
        const basicBadge = $('ep_basic_badge');
        if (basicBadge) {
            const rate = c.dailyRate ?? 0;
            const days = c.daysWorked ?? 0;
            basicBadge.textContent = '\u20B1' + Number(rate).toLocaleString('en-PH', {minimumFractionDigits:2,maximumFractionDigits:2})
                + ' \u00D7 ' + days + (days === 1 ? ' day' : ' days');
        }
        // Compute adjusted total: Basic + Holiday + Holiday OT + OT - Undertime - Late - System Deductions
        const adjustedTotal = (c.basicSalary ?? 0) + (c.holidayPay ?? 0) + (c.holidayOTPay ?? 0) + (c.otPay ?? 0) - (c.utDeduction ?? 0) - (c.late_deduction ?? 0) - (c.sss ?? 0) - (c.pagibig ?? 0) - (c.philhealth ?? 0) - (c.withholdingTax ?? 0);
        $('ep_adjusted').textContent      = fmt(Math.max(0, adjustedTotal));

        // Render one row per holiday using the breakdown array
        const holidayContainer = $('ep_holiday_rows');
        // Remove previously rendered holiday items (keep any static ones from server render)
        holidayContainer.querySelectorAll('.ep-holiday-item').forEach(el => el.remove());
        const breakdown = c.holidayBreakdown ?? [];
        breakdown.forEach(hb => {
            if (hb.amount > 0) {
                const row = document.createElement('div');
                row.className = 'ep-row ep-holiday-item';
                // Add a "statutory" badge on unworked regular holidays so it's clear
                // this is the 100% entitlement, not a bonus on top of basic pay
                const isUnworked = hb.label && hb.label.includes('not worked');
                const note = isUnworked
                    ? ' <span class="ep-badge" style="background:#fef9c3;color:#854d0e;border-color:#fde68a;">statutory</span>'
                    : '';
                row.innerHTML = '<span class="ep-row-lbl green">' + hb.label + note + '</span>'
                              + '<span class="ep-row-val green">+' + fmt(hb.amount) + '</span>';
                holidayContainer.appendChild(row);
            }
        });

        const otRow = $('ep_ot_row');
        if (c.otPay > 0) {
            $('ep_ot_display').textContent   = '+' + fmt(c.otPay);
            $('ep_ot_hrs_badge').textContent = c.otHours + ' hrs';
            otRow.style.display = '';
        } else { otRow.style.display = 'none'; }
        const holidayOtRow = $('ep_holiday_ot_row');
        if (c.holidayOTPay > 0) {
            $('ep_holiday_ot_display').textContent   = '+' + fmt(c.holidayOTPay);
            $('ep_holiday_ot_hrs_badge').textContent = c.holidayOTHours + ' hrs';
            holidayOtRow.style.display = '';
        } else { holidayOtRow.style.display = 'none'; }
        const utRow = $('ep_ut_row');
        if (c.utDeduction > 0) {
            $('ep_ut_display').textContent   = '-' + fmt(c.utDeduction);
            $('ep_ut_hrs_badge').textContent = c.utHours + ' hrs';
            utRow.style.display = '';
        } else { utRow.style.display = 'none'; }
        const lateRow = $('ep_late_row');
        if (c.late_deduction > 0) {
            $('ep_late_display').textContent   = '-' + fmt(c.late_deduction);
            $('ep_late_mins_badge').textContent = c.late_minutes + ' mins';
            lateRow.style.display = '';
        } else { lateRow.style.display = 'none'; }

        // Render individual government contribution rows
        const govContribContainer = $('ep_gov_contrib_rows');
        govContribContainer.innerHTML = '';
        const govContribs = [
            {label: 'SSS', amount: c.sss ?? 0, badge: 'sss'},
            {label: 'Pag-IBIG', amount: c.pagibig ?? 0, badge: 'pagibig'},
            {label: 'PhilHealth', amount: c.philhealth ?? 0, badge: 'philhealth'},
            {label: 'Withholding Tax', amount: c.withholdingTax ?? 0, badge: 'withholding'}
        ];
        const totalGovContrib = govContribs.reduce((sum, gc) => sum + gc.amount, 0);
        if (totalGovContrib > 0) {
            govContribs.forEach(gc => {
                if (gc.amount > 0) {
                    const row = document.createElement('div');
                    row.className = 'ep-row';
                    row.innerHTML = '<span class="ep-row-lbl red">' + gc.label + ' <span class="ep-badge">' + gc.badge + '</span></span>'
                                  + '<span class="ep-row-val red">-' + fmt(gc.amount) + '</span>';
                    govContribContainer.appendChild(row);
                }
            });
        }
    }

    function recalcNet() {
        const allowTotal = allowances.reduce((s,a) => s + a.amount, 0);
        const deductTotal = deductions.reduce((s,d) => s + d.amount, 0);
        const bonusTotal = bonuses.reduce((s,b) => s + b.amount, 0);
        const adjustedTotal = (computed.basicSalary ?? 0) + (computed.holidayPay ?? 0) + (computed.otPay ?? 0) - (computed.utDeduction ?? 0) - (computed.late_deduction ?? 0) - (computed.sss ?? 0) - (computed.pagibig ?? 0) - (computed.philhealth ?? 0) - (computed.withholdingTax ?? 0);
        const finalNetPay = Math.max(0, adjustedTotal + allowTotal - deductTotal + bonusTotal);
        computed.netPay = finalNetPay;
        $('ep_net_salary').textContent = fmt(finalNetPay);
    }

    function renderPills(cid, eid, items, cls, labelFn, removeFn) {
        const container = $(cid), empty = $(eid);
        container.querySelectorAll('.ep-pill').forEach(p => p.remove());
        if (!items.length) { if (empty) empty.style.display = ''; return; }
        if (empty) empty.style.display = 'none';
        items.forEach((item, idx) => {
            const pill = document.createElement('span');
            pill.className = 'ep-pill ' + cls;
            pill.innerHTML = '<span class="ep-pill-text">' + labelFn(item) + '</span>'
                + '<span class="ep-pill-amt">&nbsp;' + fmt(item.amount) + '</span>'
                + '<button type="button" class="ep-pill-rm" data-idx="' + idx + '">\u2715</button>';
            pill.querySelector('.ep-pill-rm').addEventListener('click', () => removeFn(idx));
            container.appendChild(pill);
        });
    }

    function renderSubtotal(sid, tid, items) {
        const total = items.reduce((s,i) => s + i.amount, 0);
        const el = $(sid);
        if (items.length > 0) {
            $(tid).textContent = Number(total).toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2});
            el.style.display = '';
        } else { el.style.display = 'none'; }
    }

    function buildHidden(cid, items, prefix, fields) {
        const container = $(cid);
        container.innerHTML = '';
        items.forEach((item, idx) => {
            fields.forEach(f => {
                const inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = prefix + '[' + idx + '][' + f + ']';
                inp.value = item[f] ?? '';
                container.appendChild(inp);
            });
        });
    }

    function render() {
        renderAttendance(computed);
        renderSalary(computed);
        renderPills('ep_allow_pills','ep_allow_empty',allowances,'green',a=>a.name,idx=>{allowances.splice(idx,1);render();});
        renderSubtotal('ep_allow_subtotal','ep_allow_total',allowances);
        buildHidden('ep_allow_hidden',allowances,'allowances',['name','amount']);
        renderPills('ep_deduct_pills','ep_deduct_empty',deductions,'red',d=>d.name,idx=>{deductions.splice(idx,1);render();});
        renderSubtotal('ep_deduct_subtotal','ep_deduct_total',deductions);
        buildHidden('ep_deduct_hidden',deductions,'deductions',['name','amount']);
        renderPills('ep_bonus_pills','ep_bonus_empty',bonuses,'purple',b=>(BONUS_LABELS[b.type]||b.type)+(b.description?' \u2014 '+b.description:''),idx=>{bonuses.splice(idx,1);render();});
        renderSubtotal('ep_bonus_subtotal','ep_bonus_total',bonuses);
        buildHidden('ep_bonus_hidden',bonuses,'bonuses',['type','description','amount']);
        recalcNet();
    }

    $('ep_allow_btn').addEventListener('click', function() {
        const name = $('ep_allow_name').value.trim(), amount = parseFloat($('ep_allow_amount').value);
        if (!name || isNaN(amount) || amount <= 0) return;
        allowances.push({name, amount});
        $('ep_allow_name').value = ''; $('ep_allow_amount').value = '';
        render(); fetchPreview();
    });
    $('ep_allow_amount').addEventListener('keydown', e => { if (e.key==='Enter'){e.preventDefault();$('ep_allow_btn').click();} });

    $('ep_deduct_btn').addEventListener('click', function() {
        const name = $('ep_deduct_name').value.trim(), amount = parseFloat($('ep_deduct_amount').value);
        if (!name || isNaN(amount) || amount <= 0) return;
        deductions.push({name, amount});
        $('ep_deduct_name').value = ''; $('ep_deduct_amount').value = '';
        render(); fetchPreview();
    });
    $('ep_deduct_amount').addEventListener('keydown', e => { if (e.key==='Enter'){e.preventDefault();$('ep_deduct_btn').click();} });

    $('ep_bonus_btn').addEventListener('click', function() {
        const type = $('ep_bonus_type').value, desc = $('ep_bonus_desc').value.trim(), amount = parseFloat($('ep_bonus_amount').value);
        if (!type || isNaN(amount) || amount <= 0) { if (!type) $('ep_bonus_type').focus(); else $('ep_bonus_amount').focus(); return; }
        bonuses.push({type, description: desc, amount});
        $('ep_bonus_type').value = ''; $('ep_bonus_desc').value = ''; $('ep_bonus_amount').value = '';
        render();
    });
    $('ep_bonus_amount').addEventListener('keydown', e => { if (e.key==='Enter'){e.preventDefault();$('ep_bonus_btn').click();} });

    let previewTimer = null;
    function fetchPreview() {
        clearTimeout(previewTimer);
        previewTimer = setTimeout(doFetch, 350);
    }
    function doFetch() {
        const body = new URLSearchParams();
        body.append('user_id', cfg.userId);
        body.append('period_start', cfg.periodStart);
        body.append('period_end', cfg.periodEnd);
        allowances.forEach((a,i) => { body.append('allowances['+i+'][name]',a.name); body.append('allowances['+i+'][amount]',a.amount); });
        deductions.forEach((d,i) => { body.append('deductions['+i+'][name]',d.name); body.append('deductions['+i+'][amount]',d.amount); });
        const token = document.querySelector('meta[name="csrf-token"]');
        fetch(cfg.previewUrl, {method:'POST',headers:{'X-CSRF-TOKEN':token?token.content:'','Accept':'application/json'},body})
        .then(r => r.ok ? r.json() : null)
        .then(data => {
            if (!data) return;
            computed = {
                daysWorked:data.days_worked, hoursWorked:data.hours_worked, daysAbsent:data.days_absent,
                basicSalary:data.basic_salary, adjustedGross:data.adjusted_gross??data.gross_pay,
                dailyRate:data.daily_rate??0,
                holidayPay:data.holiday_pay??0,
                holidayOTPay:data.holiday_overtime_pay??0,
                holidayOTHours:data.holiday_overtime_hours??0,
                holidayBreakdown:data.holiday_breakdown??[],
                otPay:data.overtime_pay??0, otHours:data.overtime_hours??0,
                utDeduction:data.undertime_deduction??0, utHours:data.undertime_hours??0,
                late_deduction:data.late_deduction??0, late_minutes:data.late_minutes??0,
                sss:data.sss??0, pagibig:data.pagibig??0, philhealth:data.phil_health??data.philhealth??0, withholdingTax:data.withholding_tax??0,
            };
            render();
        }).catch(()=>{});
    }

    cfg.initAllowances.forEach(a => allowances.push({name:a.name, amount:parseFloat(a.amount)}));
    cfg.initDeductions.forEach(d => deductions.push({name:d.name, amount:parseFloat(d.amount)}));
    cfg.initBonuses.forEach(b => bonuses.push({type:b.type, description:b.description??'', amount:parseFloat(b.amount)}));

    render();
    doFetch();
})();
</script>
@endpush

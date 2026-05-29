@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeSlideUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { 0%{opacity:0;transform:scale(0.92)} 100%{opacity:1;transform:scale(1)} }
@keyframes pillIn { from{transform:scale(0.75);opacity:0} to{transform:scale(1);opacity:1} }
.stat-card { animation:scaleIn 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.stat-card:nth-child(1) { animation-delay:0.05s; }
.stat-card:nth-child(2) { animation-delay:0.1s; }
@keyframes compSlide { 0%{opacity:0.3;transform:translateX(-4px)} 100%{opacity:1;transform:translateX(0)} }
.comp-section > div { animation:compSlide 0.35s cubic-bezier(0.16,1,0.3,1) both; }
.comp-section > div:nth-child(1) { animation-delay:0.02s; }
.comp-section > div:nth-child(2) { animation-delay:0.05s; }
.comp-section > div:nth-child(3) { animation-delay:0.08s; }
.comp-section > div:nth-child(4) { animation-delay:0.11s; }
.comp-section > div:nth-child(5) { animation-delay:0.14s; }
.comp-section > div:nth-child(6) { animation-delay:0.17s; }
.comp-section > div:nth-child(7) { animation-delay:0.20s; }
.comp-section > div:nth-child(8) { animation-delay:0.23s; }
.comp-section > div:nth-child(9) { animation-delay:0.26s; }
.comp-section > div:nth-child(10) { animation-delay:0.29s; }
.comp-section > div:nth-child(11) { animation-delay:0.32s; }
.comp-section > div:nth-child(12) { animation-delay:0.35s; }
.comp-section > div:nth-child(13) { animation-delay:0.38s; }
.comp-section > div:nth-child(14) { animation-delay:0.41s; }
.comp-section > div:nth-child(15) { animation-delay:0.44s; }
.comp-section > div:nth-child(16) { animation-delay:0.47s; }
.comp-section > div:nth-child(17) { animation-delay:0.50s; }
.comp-section > div:nth-child(18) { animation-delay:0.53s; }
.comp-section > div:nth-child(19) { animation-delay:0.56s; }
.comp-section > div:nth-child(20) { animation-delay:0.59s; }
@keyframes netPop { 0%{opacity:0;transform:scale(0.92)} 70%{transform:scale(1.03)} 100%{opacity:1;transform:scale(1)} }
.net-pop { animation:netPop 0.5s cubic-bezier(0.16,1,0.3,1) 0.4s both; }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto space-y-5">

    <a href="{{ route('payroll.batch.confirm', $batch) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-600 transition-all hover:bg-gray-50 active:scale-[0.97] mb-2">
        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Back to batch
    </a>

    {{-- Employee header --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 px-6 py-5 flex items-center gap-4">
        <div class="flex-1">
            <h1 class="text-lg font-extrabold text-gray-900 tracking-tight">{{ $payroll->user->first_name }} {{ $payroll->user->last_name }}</h1>
            <p class="text-xs text-gray-400">{{ $payroll->user->position ?? ($payroll->user->department ?? 'N/A') }} &middot; Edit Payroll</p>
        </div>
        <span class="font-mono text-xs text-gray-500 bg-gray-50 border border-gray-200 px-3 py-1.5 rounded-lg shrink-0">{{ $batch->period_start->format('M d') }} &ndash; {{ $batch->period_end->format('M d, Y') }}</span>
    </div>

    <form id="ep-form" action="{{ route('payroll.batch.update-employee', [$batch, $payroll]) }}" method="POST">
        @csrf @method('PUT')
        <input type="hidden" id="ep_user_id"      value="{{ $payroll->user_id }}">
        <input type="hidden" id="ep_period_start" value="{{ $batch->period_start->format('Y-m-d') }}">
        <input type="hidden" id="ep_period_end"   value="{{ $batch->period_end->format('Y-m-d') }}">

        {{-- Row 1: Attendance + Salary --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            {{-- Attendance card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden stat-card">
                <div class="px-4 py-3 border-b border-gray-50 flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                        </div>
                        <div><p class="text-sm font-bold text-gray-900">Attendance</p><p class="text-xs text-gray-400">Live from records</p></div>
                    </div>
                    <a href="{{ route('attendance.employee.calendar', $payroll->user_id) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 text-gray-500 border border-gray-200 rounded-lg text-xs font-semibold transition-all hover:bg-gray-200 hover:text-gray-700 active:scale-[0.97] shrink-0">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        Record
                    </a>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-3 gap-2.5">
                        <div class="bg-gray-50 rounded-xl px-3 py-2.5 text-center">
                            <p class="text-xs text-gray-400 font-medium">Days Worked</p>
                            <p class="text-lg font-extrabold text-gray-900 tabular-nums mt-0.5" id="ep_days_worked">{{ $payroll->days_worked ?? '—' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl px-3 py-2.5 text-center">
                            <p class="text-xs text-gray-400 font-medium">Hours</p>
                            <p class="text-lg font-extrabold text-gray-900 tabular-nums mt-0.5" id="ep_hours_worked">{{ $payroll->hours_worked ?? '—' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl px-3 py-2.5 text-center">
                            <p class="text-xs text-gray-400 font-medium">Absent</p>
                            <p class="text-lg font-extrabold text-red-500 tabular-nums mt-0.5" id="ep_days_absent">—</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Salary card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden stat-card">
                <div class="px-4 py-3 border-b border-gray-50 flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div><p class="text-sm font-bold text-gray-900">Salary Computation</p><p class="text-xs text-gray-400">Earnings and deductions</p></div>
                </div>
                <div class="p-4">
                    <div class="divide-y divide-gray-50 comp-section">
                        <div class="flex items-center justify-between py-2.5 first:pt-0">
                            <span class="text-sm text-gray-500 flex items-center gap-1.5">Basic Pay <span class="text-xs font-semibold bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded" id="ep_basic_badge">rate &times; days</span></span>
                            <span class="text-sm font-bold text-gray-900 tabular-nums font-mono" id="ep_basic_display">₱{{ number_format($payroll->basic_salary??0,2) }}</span>
                        </div>
                        <div id="ep_holiday_rows">
                            {{-- Holiday rows are rendered by JavaScript from window._ep.initComputed.holidayBreakdown --}}
                        </div>
                        <div class="flex items-center justify-between py-2.5" id="ep_leave_pay_row" style="display:none;">
                            <span class="text-sm text-emerald-600 flex items-center gap-1.5">Leave Pay <span class="text-xs font-semibold bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded" id="ep_leave_pay_badge">0 days</span></span>
                            <span class="text-sm font-bold text-emerald-600 tabular-nums font-mono" id="ep_leave_pay_display">+₱0.00</span>
                        </div>
                        @php
                            $leavePayAllowances = $payroll->allowances->filter(fn($a) => str_starts_with((string)($a->allowance_type ?? ''), 'Leave Pay'));
                            $existingLeavePay = $leavePayAllowances->sum('amount');
                            $existingLeavePayDays = (int) $leavePayAllowances->sum('hours');
                        @endphp
                        @if($existingLeavePay > 0)
                        <script>document.addEventListener('DOMContentLoaded',function(){document.getElementById('ep_leave_pay_row').style.display='';document.getElementById('ep_leave_pay_display').textContent='+₱{{ number_format($existingLeavePay, 2) }}';document.getElementById('ep_leave_pay_badge').textContent='{{ $existingLeavePayDays }} day{{ $existingLeavePayDays !== 1 ? "s" : "" }}';});</script>
                        @endif
                        <div class="flex items-center justify-between py-2.5" id="ep_ot_row" style="display:none;">
                            <span class="text-sm text-emerald-600 flex items-center gap-1.5">Overtime Pay <span class="text-xs font-semibold bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded" id="ep_ot_hrs_badge"></span></span>
                            <span class="text-sm font-bold text-emerald-600 tabular-nums font-mono" id="ep_ot_display">+₱0.00</span>
                        </div>
                        <div class="flex items-center justify-between py-2.5" id="ep_holiday_ot_row" style="display:none;">
                            <span class="text-sm text-emerald-600 flex items-center gap-1.5">Holiday Overtime <span class="text-xs font-semibold bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded" id="ep_holiday_ot_hrs_badge"></span></span>
                            <span class="text-sm font-bold text-emerald-600 tabular-nums font-mono" id="ep_holiday_ot_display">+₱0.00</span>
                        </div>
                        <div class="flex items-center justify-between py-2.5" id="ep_ut_row" style="display:none;">
                            <span class="text-sm text-red-500 flex items-center gap-1.5">Undertime <span class="text-xs font-semibold bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded" id="ep_ut_hrs_badge"></span></span>
                            <span class="text-sm font-bold text-red-500 tabular-nums font-mono" id="ep_ut_display">-₱0.00</span>
                        </div>
                        <div class="flex items-center justify-between py-2.5" id="ep_late_row" style="display:none;">
                            <span class="text-sm text-red-500 flex items-center gap-1.5">Tardiness <span class="text-xs font-semibold bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded" id="ep_late_mins_badge"></span></span>
                            <span class="text-sm font-bold text-red-500 tabular-nums font-mono" id="ep_late_display">-₱0.00</span>
                        </div>
                        <div id="ep_gov_contrib_rows"></div>
                        @php
                            $caDeduction = (float)($payroll->cash_advance_deduction ?? 0);
                            $slDeduction = (float)($payroll->salary_loan_deduction ?? 0);
                        @endphp
                        <div class="flex items-center justify-between py-2.5" id="ep_ca_deduct_row" style="display:{{ $caDeduction > 0 ? '' : 'none' }};">
                            <span class="text-sm text-red-500 flex items-center gap-1.5">Cash Advance <span class="text-xs font-semibold bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded">loan</span></span>
                            <span class="text-sm font-bold text-red-500 tabular-nums font-mono" id="ep_ca_deduct_display">-₱{{ number_format($caDeduction, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2.5" id="ep_sl_deduct_row" style="display:{{ $slDeduction > 0 ? '' : 'none' }};">
                            <span class="text-sm text-red-500 flex items-center gap-1.5">Salary Loan <span class="text-xs font-semibold bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded">loan</span></span>
                            <span class="text-sm font-bold text-red-500 tabular-nums font-mono" id="ep_sl_deduct_display">-₱{{ number_format($slDeduction, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2.5">
                            <span class="text-sm font-bold text-gray-900">Initial Net Pay</span>
                            @php
                                $leavePayAllowances = $payroll->allowances->filter(fn($a) => str_starts_with((string)($a->allowance_type ?? ''), 'Leave Pay'));
                                $computedLeavePay = (float)($leavePayAllowances->sum('amount') ?? 0);
                                $initialNetPay = max(0, ($payroll->basic_salary??0) + ($payroll->holiday_pay??0) + ($payroll->holiday_ot_pay??0) + $computedLeavePay - ($payroll->undertime_deduction??0) - ($payroll->deductions->where('deduction_type','Late Deduction')->sum('amount')??0) - ($payroll->sss??0) - ($payroll->pagibig??0) - ($payroll->phil_health??$payroll->philhealth??0) - ($payroll->withholding_tax??0) - ($payroll->cash_advance_deduction??0) - ($payroll->salary_loan_deduction??0));
                            @endphp
                            <span class="text-base font-bold text-gray-900 tabular-nums font-mono" id="ep_adjusted">₱{{ number_format($initialNetPay, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 2: Allowances + Deductions --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            {{-- Allowances --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-50 flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <div><p class="text-sm font-bold text-gray-900">Allowances <span class="font-normal text-gray-400 text-xs">(optional)</span></p><p class="text-xs text-gray-400">Transportation, meal, housing&hellip;</p></div>
                </div>
                <div class="p-4">
                    <div class="flex gap-2 items-stretch mb-3 flex-wrap">
                        <input type="text" id="ep_allow_name" class="flex-1 min-w-0 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-900 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100" placeholder="Label — e.g. Transportation">
                        <input type="number" id="ep_allow_amount" class="w-[100px] shrink-0 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-900 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100" placeholder="0.00" min="0.01" step="0.01">
                        <button type="button" id="ep_allow_btn" class="shrink-0 inline-flex items-center gap-1 px-3 py-2 bg-emerald-600 text-white rounded-lg text-xs font-bold transition-all hover:bg-emerald-700 active:scale-[0.97] cursor-pointer">+ Allowance</button>
                    </div>
                    <div class="flex flex-wrap gap-1.5 min-h-[28px] py-0.5" id="ep_allow_pills"><span class="text-xs text-gray-300 italic" id="ep_allow_empty">None added yet.</span></div>
                    <div class="text-xs font-semibold text-emerald-600 text-right mt-1 font-mono tabular-nums" id="ep_allow_subtotal" style="display:none;">Total: ₱<span id="ep_allow_total">0.00</span></div>
                    <div id="ep_allow_hidden"></div>
                </div>
            </div>

            {{-- Deductions --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-50 flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
                    </div>
                    <div><p class="text-sm font-bold text-gray-900">Deductions <span class="font-normal text-gray-400 text-xs">(optional)</span></p><p class="text-xs text-gray-400">Penalty, medical, savings&hellip;</p></div>
                </div>
                <div class="p-4">
                    <div class="flex gap-2 items-stretch mb-3 flex-wrap">
                        <input type="text" id="ep_deduct_name" class="flex-1 min-w-0 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-900 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100" placeholder="Label — e.g. Cash Advance">
                        <input type="number" id="ep_deduct_amount" class="w-[100px] shrink-0 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-900 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100" placeholder="0.00" min="0.01" step="0.01">
                        <button type="button" id="ep_deduct_btn" class="shrink-0 inline-flex items-center gap-1 px-3 py-2 bg-red-600 text-white rounded-lg text-xs font-bold transition-all hover:bg-red-700 active:scale-[0.97] cursor-pointer">+ Deduction</button>
                    </div>
                    <div class="flex flex-wrap gap-1.5 min-h-[28px] py-0.5" id="ep_deduct_pills"><span class="text-xs text-gray-300 italic" id="ep_deduct_empty">None added yet.</span></div>
                    <div class="text-xs font-semibold text-red-500 text-right mt-1 font-mono tabular-nums" id="ep_deduct_subtotal" style="display:none;">Total: ₱<span id="ep_deduct_total">0.00</span></div>
                    <div id="ep_deduct_hidden"></div>
                </div>
            </div>
        </div>

        {{-- Bonuses --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-4">
            <div class="px-4 py-3 border-b border-gray-50 flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-violet-50 text-violet-600 flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
                </div>
                <div><p class="text-sm font-bold text-gray-900">Bonuses <span class="font-normal text-gray-400 text-xs">(optional)</span></p><p class="text-xs text-gray-400">Performance, holiday, attendance, special</p></div>
            </div>
            <div class="p-4">
                <div class="flex gap-2 items-stretch mb-3 flex-wrap">
                    <select id="ep_bonus_type" class="w-[145px] shrink-0 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-900 bg-white outline-none appearance-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100" style="background-image:url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%2712%27 height=%2712%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27%236b7280%27 stroke-width=%272.5%27%3E%3Cpath d=%27M6 9l6 6 6-6%27/%3E%3C/svg%3E');background-repeat:no-repeat;background-position:right 10px center;padding-right:28px;">
                        <option value="">— Type —</option>
                        <option value="performance">Performance</option>
                        <option value="holiday">Holiday</option>
                        <option value="attendance">Attendance</option>
                        <option value="special">Special</option>
                    </select>
                    <input type="text" id="ep_bonus_desc" class="flex-1 min-w-0 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-900 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100" placeholder="Description (optional)">
                    <input type="number" id="ep_bonus_amount" class="w-[100px] shrink-0 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-900 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100" placeholder="0.00" min="0.01" step="0.01">
                    <button type="button" id="ep_bonus_btn" class="shrink-0 inline-flex items-center gap-1 px-3 py-2 bg-violet-600 text-white rounded-lg text-xs font-bold transition-all hover:bg-violet-700 active:scale-[0.97] cursor-pointer">+ Bonus</button>
                </div>
                <div class="flex flex-wrap gap-1.5 min-h-[28px] py-0.5" id="ep_bonus_pills"><span class="text-xs text-gray-300 italic" id="ep_bonus_empty">No bonuses added yet.</span></div>
                <div class="text-xs font-semibold text-violet-600 text-right mt-1 font-mono tabular-nums" id="ep_bonus_subtotal" style="display:none;">Total: ₱<span id="ep_bonus_total">0.00</span></div>
                <div id="ep_bonus_hidden"></div>
            </div>
        </div>

        {{-- Net Pay --}}
        <div class="net-pop bg-white rounded-xl shadow-sm border border-gray-100 px-6 py-5 flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Net Pay</p>
                <p class="text-xs text-gray-400 mt-0.5">Basic + Allowances − Deductions + Bonuses</p>
            </div>
            <span class="text-2xl font-extrabold text-gray-900 tabular-nums font-mono tracking-tight" id="ep_net_salary">₱{{ number_format($payroll->net_pay??0,2) }}</span>
        </div>

        {{-- Footer --}}
        <div class="flex gap-2.5 justify-end pt-4 border-t border-gray-100">
            <a href="{{ route('payroll.batch.confirm', $batch) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-gray-700 border border-gray-200 rounded-lg text-sm font-semibold transition-all hover:bg-gray-50 active:scale-[0.97] cursor-pointer">Cancel</a>
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 text-white rounded-lg text-sm font-bold transition-all hover:bg-gray-800 active:scale-[0.97] cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Save &amp; Mark as Prepared
            </button>
        </div>
    </form>
</div>
@endsection

@push('head_scripts')
<meta name="page-id" content="payroll-batch-edit">
@php
    $manualAllowances = $payroll->allowances
        ->filter(fn($a) => !str_starts_with((string)($a->allowance_type ?? $a->name ?? ''), 'Overtime Pay')
                        && !str_starts_with((string)($a->allowance_type ?? $a->name ?? ''), 'Leave Pay'))
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
    $holidayOTPay = (float)($payroll->holiday_ot_pay ?? 0);
    $holidayOTHours = (float)($payroll->holiday_ot_hours ?? 0);
    $leavePayAllowances = $payroll->allowances->filter(fn($a) => str_starts_with((string)($a->allowance_type ?? ''), 'Leave Pay'));
    $leavePay = (float)($leavePayAllowances->sum('amount') ?? 0);
    $leavePayDays = (int) $leavePayAllowances->sum('hours');
    $holidayPay = (float)($payroll->holiday_pay ?? 0);
    $caDeduction = (float)($payroll->cash_advance_deduction ?? 0);
    $slDeduction = (float)($payroll->salary_loan_deduction ?? 0);
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
        holidayOTPay:   {{ (float)($payroll->holiday_ot_pay ?? 0) }},
        holidayOTHours: {{ (float)($payroll->holiday_ot_hours ?? 0) }},
        holidayBreakdown: @json($payroll->holiday_breakdown ?? []),
        leavePay:       {{ round($leavePay, 2) }},
        leavePayDays:   {{ $leavePayDays }},
        netPay:         {{ (float)($payroll->net_pay ?? 0) }},
        otPay:          {{ (float)($payroll->overtime_pay ?? 0) }},
        otHours:        {{ (float)($payroll->allowances->where('allowance_type', 'like', 'Overtime Pay%')->sum('hours') ?? 0) }},
        utDeduction:    {{ (float)($payroll->undertime_deduction ?? 0) }},
        utHours:        {{ (float)($payroll->deductions->where('deduction_type', 'like', 'Undertime Deduction%')->sum('hours') ?? 0) }},
        late_deduction:  {{ (float)($payroll->deductions->where('deduction_type', 'Late Deduction')->sum('amount') ?? 0) }},
        late_minutes:    {{ (int)($payroll->deductions->where('deduction_type', 'Late Deduction')->first()?->description ? preg_match('/^(\\d+)/', $payroll->deductions->where('deduction_type', 'Late Deduction')->first()?->description, $m) ? $m[1] : 0 : 0) }},
        caDeduction:    {{ round($caDeduction, 2) }},
        slDeduction:    {{ round($slDeduction, 2) }},
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
        const basicBadge = $('ep_basic_badge');
        if (basicBadge) {
            const rate = c.dailyRate ?? 0;
            const days = c.daysWorked ?? 0;
            basicBadge.textContent = '\u20B1' + Number(rate).toLocaleString('en-PH', {minimumFractionDigits:2,maximumFractionDigits:2})
                + ' \u00D7 ' + days + (days === 1 ? ' day' : ' days');
        }
        const totalEarnings = (c.basicSalary ?? 0) + (c.holidayPay ?? 0) + (c.holidayOTPay ?? 0) + (c.leavePay ?? 0) + (c.otPay ?? 0);
        const manualAllowTotal = allowances.reduce((s,a) => s + a.amount, 0);
        const bonusTotal = bonuses.reduce((s,b) => s + b.amount, 0);
        const totalDeductions = (c.utDeduction ?? 0) + (c.late_deduction ?? 0) + (c.sss ?? 0) + (c.pagibig ?? 0) + (c.philhealth ?? 0) + (c.withholdingTax ?? 0) + (c.caDeduction ?? 0) + (c.slDeduction ?? 0);
        $('ep_adjusted').textContent      = fmt(Math.max(0, totalEarnings + manualAllowTotal + bonusTotal - totalDeductions));

        const holidayContainer = $('ep_holiday_rows');
        holidayContainer.querySelectorAll('[data-holiday-item]').forEach(el => el.remove());
        const breakdown = c.holidayBreakdown ?? [];
        breakdown.forEach(hb => {
            if (hb.amount > 0) {
                const row = document.createElement('div');
                row.setAttribute('data-holiday-item','');
                const isUnworked = hb.label && hb.label.includes('not worked');
                const note = isUnworked
                    ? ' <span class="text-xs font-semibold bg-yellow-100 text-yellow-800 px-1.5 py-0.5 rounded">statutory</span>'
                    : '';
                row.className = 'flex items-center justify-between py-2.5';
                row.innerHTML = '<span class="text-sm text-emerald-600 flex items-center gap-1.5">' + hb.label + note + '</span>'
                              + '<span class="text-sm font-bold text-emerald-600 tabular-nums font-mono">+' + fmt(hb.amount) + '</span>';
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
        const leavePayRow = $('ep_leave_pay_row');
        if (c.leavePay > 0) {
            $('ep_leave_pay_display').textContent = '+' + fmt(c.leavePay);
            const days = c.leavePayDays ?? 0;
            $('ep_leave_pay_badge').textContent = days + ' day' + (days !== 1 ? 's' : '');
            leavePayRow.style.display = '';
        } else { leavePayRow.style.display = 'none'; }
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
                    row.className = 'flex items-center justify-between py-2.5';
                    row.innerHTML = '<span class="text-sm text-red-500 flex items-center gap-1.5">' + gc.label + ' <span class="text-xs font-semibold bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded">' + gc.badge + '</span></span>'
                                  + '<span class="text-sm font-bold text-red-500 tabular-nums font-mono">-' + fmt(gc.amount) + '</span>';
                    govContribContainer.appendChild(row);
                }
            });
        }

        const caRow = $('ep_ca_deduct_row');
        if (c.caDeduction > 0) {
            $('ep_ca_deduct_display').textContent = '-' + fmt(c.caDeduction);
            caRow.style.display = '';
        } else { caRow.style.display = 'none'; }
        const slRow = $('ep_sl_deduct_row');
        if (c.slDeduction > 0) {
            $('ep_sl_deduct_display').textContent = '-' + fmt(c.slDeduction);
            slRow.style.display = '';
        } else { slRow.style.display = 'none'; }
    }

    function recalcNet() {
        const allowTotal = allowances.reduce((s,a) => s + a.amount, 0);
        const deductTotal = deductions.reduce((s,d) => s + d.amount, 0);
        const bonusTotal = bonuses.reduce((s,b) => s + b.amount, 0);
        const adjustedTotal = (computed.basicSalary ?? 0) + (computed.holidayPay ?? 0) + (computed.holidayOTPay ?? 0) + (computed.leavePay ?? 0) + (computed.otPay ?? 0) - (computed.utDeduction ?? 0) - (computed.late_deduction ?? 0) - (computed.caDeduction ?? 0) - (computed.slDeduction ?? 0) - (computed.sss ?? 0) - (computed.pagibig ?? 0) - (computed.philhealth ?? 0) - (computed.withholdingTax ?? 0);
        const finalNetPay = adjustedTotal + allowTotal - deductTotal + bonusTotal;
        computed.netPay = finalNetPay;
        $('ep_net_salary').textContent = fmt(finalNetPay);
    }

    function renderPills(cid, eid, items, labelFn, removeFn) {
        const container = $(cid), empty = $(eid);
        container.querySelectorAll('[data-pill]').forEach(p => p.remove());
        if (!items.length) { if (empty) empty.style.display = ''; return; }
        if (empty) empty.style.display = 'none';
        items.forEach((item, idx) => {
            const pill = document.createElement('span');
            pill.setAttribute('data-pill','');
            pill.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold animate-[pillIn_0.15s_ease]';
            if (cid === 'ep_allow_pills') pill.className += ' bg-emerald-50 text-emerald-700 border border-emerald-200';
            else if (cid === 'ep_deduct_pills') pill.className += ' bg-red-50 text-red-700 border border-red-200';
            else pill.className += ' bg-violet-50 text-violet-700 border border-violet-200';
            pill.innerHTML = '<span class="max-w-[120px] truncate">' + labelFn(item) + '</span>'
                + '<span class="tabular-nums font-mono opacity-85">&nbsp;' + fmt(item.amount) + '</span>'
                + '<button type="button" class="w-3.5 h-3.5 rounded-full border-none flex items-center justify-center text-[10px] cursor-pointer p-0 shrink-0 opacity-55 hover:opacity-100 transition-opacity" data-idx="' + idx + '">\u2715</button>';
            pill.querySelector('button').addEventListener('click', function(e) {
                e.stopPropagation();
                removeFn(parseInt(this.dataset.idx));
            });
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
        renderPills('ep_allow_pills','ep_allow_empty',allowances,a=>a.name,idx=>{allowances.splice(idx,1);render();});
        renderSubtotal('ep_allow_subtotal','ep_allow_total',allowances);
        buildHidden('ep_allow_hidden',allowances,'allowances',['name','amount']);
        renderPills('ep_deduct_pills','ep_deduct_empty',deductions,d=>d.name,idx=>{deductions.splice(idx,1);render();});
        renderSubtotal('ep_deduct_subtotal','ep_deduct_total',deductions);
        buildHidden('ep_deduct_hidden',deductions,'deductions',['name','amount']);
        renderPills('ep_bonus_pills','ep_bonus_empty',bonuses,b=>(BONUS_LABELS[b.type]||b.type)+(b.description?' \u2014 '+b.description:''),idx=>{bonuses.splice(idx,1);render();});
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
                leavePay:data.leave_pay??0,
                leavePayDays:data.leave_paid_days??0,
                otPay:data.overtime_pay??0, otHours:data.overtime_hours??0,
                utDeduction:data.undertime_deduction??0, utHours:data.undertime_hours??0,
                late_deduction:data.late_deduction??0, late_minutes:data.late_minutes??0,
                caDeduction:data.cash_advance_deduction??0,
                slDeduction:data.salary_loan_deduction??0,
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
requestAnimationFrame(function() {
  requestAnimationFrame(function() {
    var fmt = function(n) { return n.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2}); };
    var targets = document.querySelectorAll('.comp-section .tabular-nums.font-mono, .net-pop .tabular-nums.font-mono');
    targets.forEach(function(el, i) {
      if (el.offsetParent === null) return;
      var raw = el.textContent.trim();
      var m = raw.match(/^([+-])?\s*₱?\s*([\d,]+\.\d{2})/);
      if (!m) return;
      var sign = m[1] || '';
      var target = parseFloat(m[2].replace(/,/g, ''));
      var duration = 600 + i * 50;
      var t0 = performance.now();
      function tick(now) {
        var p = Math.min((now - t0) / duration, 1);
        var v = (1 - Math.pow(1 - p, 3)) * Math.abs(target);
        el.textContent = sign + '₱' + fmt(v);
        if (p < 1) requestAnimationFrame(tick);
      }
      requestAnimationFrame(tick);
    });
  });
});
</script>
@endpush

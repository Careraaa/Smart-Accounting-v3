@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeSlideUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { 0%{opacity:0;transform:scale(0.92)} 100%{opacity:1;transform:scale(1)} }
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

    <button type="button" onclick="history.back()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-600 transition-all hover:bg-gray-50 active:scale-[0.97] mb-2 cursor-pointer">
        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Back
    </button>

    @php
        $sc = match($payroll->status) {
            'pending' => ['label'=>'Pending','dot'=>'bg-amber-400','text'=>'text-amber-600','bg'=>'bg-amber-50'],
            'submitted' => ['label'=>'Submitted','dot'=>'bg-violet-400','text'=>'text-violet-600','bg'=>'bg-violet-50'],
            'approved' => ['label'=>'Approved','dot'=>'bg-emerald-400','text'=>'text-emerald-600','bg'=>'bg-emerald-50'],
            'rejected' => ['label'=>'Rejected','dot'=>'bg-red-400','text'=>'text-red-600','bg'=>'bg-red-50'],
            default => ['label'=>'Pending','dot'=>'bg-amber-400','text'=>'text-amber-600','bg'=>'bg-amber-50'],
        };
        $overtimePay = (float)($payroll->overtime_pay ?? 0);
        $overtimeHours = (float)($payroll->allowances->where('allowance_type', 'like', 'Overtime Pay%')->sum('hours') ?? 0);
        $leavePayAllowances = $payroll->allowances->filter(fn($a) => str_starts_with((string)($a->allowance_type ?? ''), 'Leave Pay'));
        $leavePayTotal = (float)($leavePayAllowances->sum('amount') ?? 0);
        $leavePayDays = (int) $leavePayAllowances->sum('hours');
        $holidayPay = (float)($payroll->holiday_pay ?? 0);
        $holidayOTPay = (float)($payroll->holiday_ot_pay ?? 0);
        $holidayOTHours = (float)($payroll->holiday_ot_hours ?? 0);
        $holidayBreakdown = $payroll->holiday_breakdown ?? [];
        $undertimeDeduction = (float)($payroll->undertime_deduction ?? 0);
        $undertimeHours = (float)($payroll->deductions->where('deduction_type', 'like', 'Undertime Deduction%')->sum('hours') ?? 0);
        $lateDeduction = (float)($payroll->deductions->where('deduction_type', 'Late Deduction')->sum('amount') ?? 0);
        $lateMinutes = (int)($payroll->deductions->where('deduction_type', 'Late Deduction')->first()?->description ? (preg_match('/^(\d+)/', $payroll->deductions->where('deduction_type', 'Late Deduction')->first()?->description, $m) ? $m[1] : 0) : 0);
        $caDeduction = (float)($payroll->cash_advance_deduction ?? 0);
        $slDeduction = (float)($payroll->salary_loan_deduction ?? 0);
        $sss = (float)($payroll->sss ?? 0);
        $pagibig = (float)($payroll->pagibig ?? 0);
        $philhealth = (float)($payroll->phil_health ?? $payroll->philhealth ?? 0);
        $withholdingTax = (float)($payroll->withholding_tax ?? 0);
        $manualAllowances = $payroll->allowances->filter(fn($a) => !str_starts_with((string)($a->allowance_type ?? ''), 'Overtime Pay') && !str_starts_with((string)($a->allowance_type ?? ''), 'Leave Pay'));
        $manualDeductions = $payroll->deductions->filter(fn($d) => !in_array($d->deduction_type, ['SSS','Pag-IBIG','PhilHealth','Withholding Tax','Late Deduction']) && !str_starts_with((string)($d->deduction_type ?? ''), 'Undertime Deduction'));
    @endphp

    {{-- Employee header --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 px-6 py-5 flex items-center gap-4">
        <span class="w-12 h-12 rounded-full bg-gray-100 border-2 border-gray-200 flex items-center justify-center text-base font-extrabold text-gray-600 shrink-0 uppercase">{{ strtoupper(substr($payroll->user->first_name??'U',0,1).substr($payroll->user->last_name??'',0,1)) }}</span>
        <div class="flex-1">
            <h1 class="text-lg font-extrabold text-gray-900 tracking-tight">{{ $payroll->user->first_name }} {{ $payroll->user->last_name }}</h1>
            <p class="text-xs text-gray-400">{{ $payroll->user->position ?? ($payroll->user->department ?? 'Employee') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold uppercase tracking-wide {{ $sc['bg'] }} {{ $sc['text'] }}">
                <span class="w-2 h-2 rounded-full {{ $sc['dot'] }}"></span>
                {{ $sc['label'] }}
            </span>
            @if($payroll->approvedBy)
                <span class="text-[0.55rem] font-mono text-gray-500">Approved by {{ $payroll->approvedBy->first_name }} {{ $payroll->approvedBy->last_name }}</span>
            @endif
            <span class="font-mono text-xs text-gray-500 bg-gray-50 border border-gray-200 px-3 py-1.5 rounded-lg shrink-0">{{ $payroll->payroll_period_start->format('M d') }} &ndash; {{ $payroll->payroll_period_end->format('M d, Y') }}</span>
        </div>
    </div>

    {{-- Row 1: Attendance + Salary --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- Attendance card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden stat-card">
            <div class="px-4 py-3 border-b border-gray-50 flex items-center justify-between gap-2.5">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                    </div>
                    <div><p class="text-sm font-bold text-gray-900">Attendance</p><p class="text-xs text-gray-400">From records</p></div>
                </div>
                <a href="{{ route('attendance.employee.calendar', $payroll->user_id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-50 text-amber-600 border border-amber-200 rounded-lg text-xs font-semibold transition-all hover:bg-amber-100 active:scale-[0.97] shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5"/></svg>
                    Record
                </a>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-2 gap-2.5">
                    <div class="bg-gray-50 rounded-xl px-3 py-2.5 text-center">
                        <p class="text-xs text-gray-400 font-medium">Days Worked</p>
                        <p class="text-lg font-extrabold text-gray-900 tabular-nums mt-0.5">{{ $payroll->days_worked ?? '—' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl px-3 py-2.5 text-center">
                        <p class="text-xs text-gray-400 font-medium">Hours</p>
                        <p class="text-lg font-extrabold text-gray-900 tabular-nums mt-0.5">{{ $payroll->hours_worked ?? '—' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl px-3 py-2.5 text-center">
                        <p class="text-xs text-gray-400 font-medium">Absent</p>
                        <p class="text-lg font-extrabold text-red-500 tabular-nums mt-0.5">{{ \App\Models\Attendance::where('user_id', $payroll->user_id)->whereBetween('date', [$payroll->payroll_period_start, $payroll->payroll_period_end])->where('status', 'absent')->count() }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl px-3 py-2.5 text-center">
                        <p class="text-xs text-gray-400 font-medium">Work Schedule</p>
                        <p class="text-lg font-extrabold text-gray-900 tabular-nums mt-0.5">{{ ($payroll->user->work_days_per_week ?? 5) == 6 ? 'Mon - Sat' : 'Mon - Fri' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Salary Computation card (summary) --}}
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
                        <span class="text-sm text-gray-500 flex items-center gap-1.5">Basic Pay <span class="text-xs font-semibold bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded">&#x20B1;{{ number_format($payroll->per_day_rate, 2) }} &times; {{ $payroll->days_worked }} day{{ $payroll->days_worked != 1 ? 's' : '' }}</span></span>
                        <span class="text-sm font-bold text-gray-900 tabular-nums font-mono">&#x20B1;{{ number_format($payroll->basic_salary ?? 0, 2) }}</span>
                    </div>

                    {{-- Holiday rows --}}
                    @foreach($holidayBreakdown as $hb)
                    @php
                        $_hbType = match($hb['type'] ?? '') { 'regular' => 'Regular', 'special' => 'Special', 'double' => 'Double', default => ucfirst($hb['type'] ?? '') };
                        $_hbRest = !empty($hb['is_rest_day']) ? ', rest day' : '';
                        $_hbWorked = empty($hb['is_worked']) ? (($hb['type'] === 'regular' || $hb['type'] === 'double') ? 'unworked — statutory entitlement' : 'not worked') : 'worked';
                        $_hbLabel = 'Holiday Pay — ' . ($hb['holiday'] ?? 'Holiday') . ' (' . $_hbType . $_hbRest . ', ' . $_hbWorked . ')';
                    @endphp
                    <div class="flex items-center justify-between py-2.5">
                        <span class="text-sm text-emerald-600 flex items-center gap-1.5">
                            {{ $_hbLabel }}
                        </span>
                        <span class="text-sm font-bold text-emerald-600 tabular-nums font-mono">+&#x20B1;{{ number_format($hb['amount'], 2) }}</span>
                    </div>
                    @endforeach

                    {{-- Leave Pay — one row per leave type --}}
                    @foreach($leavePayAllowances as $la)
                    @php
                        $_laName = str_replace(['Leave Pay (', ')'], '', $la->allowance_type);
                        $_laDays = (int)($la->hours ?? 0);
                    @endphp
                    <div class="flex items-center justify-between py-2.5">
                        <span class="text-sm text-emerald-600 flex items-center gap-1.5">{{ $_laName }} <span class="text-xs font-semibold bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded">{{ $_laDays }} day{{ $_laDays !== 1 ? 's' : '' }}</span></span>
                        <span class="text-sm font-bold text-emerald-600 tabular-nums font-mono">+&#x20B1;{{ number_format($la->amount, 2) }}</span>
                    </div>
                    @endforeach

                    {{-- Overtime Pay --}}
                    @if($overtimePay > 0)
                    <div class="flex items-center justify-between py-2.5">
                        <span class="text-sm text-emerald-600 flex items-center gap-1.5">Overtime Pay <span class="text-xs font-semibold bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded">{{ $overtimeHours }} hrs</span></span>
                        <span class="text-sm font-bold text-emerald-600 tabular-nums font-mono">+&#x20B1;{{ number_format($overtimePay, 2) }}</span>
                    </div>
                    @endif

                    {{-- Holiday Overtime --}}
                    @if($holidayOTPay > 0)
                    <div class="flex items-center justify-between py-2.5">
                        <span class="text-sm text-emerald-600 flex items-center gap-1.5">Holiday Overtime <span class="text-xs font-semibold bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded">{{ $holidayOTHours }} hrs</span></span>
                        <span class="text-sm font-bold text-emerald-600 tabular-nums font-mono">+&#x20B1;{{ number_format($holidayOTPay, 2) }}</span>
                    </div>
                    @endif

                    {{-- Undertime --}}
                    @if($undertimeDeduction > 0)
                    <div class="flex items-center justify-between py-2.5">
                        <span class="text-sm text-red-500 flex items-center gap-1.5">Undertime <span class="text-xs font-semibold bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded">{{ $undertimeHours }} hrs</span></span>
                        <span class="text-sm font-bold text-red-500 tabular-nums font-mono">-&#x20B1;{{ number_format($undertimeDeduction, 2) }}</span>
                    </div>
                    @endif

                    {{-- Tardiness --}}
                    @if($lateDeduction > 0)
                    <div class="flex items-center justify-between py-2.5">
                        <span class="text-sm text-red-500 flex items-center gap-1.5">Tardiness <span class="text-xs font-semibold bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded">{{ $lateMinutes }} mins</span></span>
                        <span class="text-sm font-bold text-red-500 tabular-nums font-mono">-&#x20B1;{{ number_format($lateDeduction, 2) }}</span>
                    </div>
                    @endif

                    {{-- Government contributions --}}
                    @php $govContribs = [['label'=>'SSS','badge'=>'sss','amount'=>$sss],['label'=>'Pag-IBIG','badge'=>'pagibig','amount'=>$pagibig],['label'=>'PhilHealth','badge'=>'philhealth','amount'=>$philhealth],['label'=>'Withholding Tax','badge'=>'withholding','amount'=>$withholdingTax]]; @endphp
                    @foreach($govContribs as $gc)
                    @if($gc['amount'] > 0)
                    <div class="flex items-center justify-between py-2.5">
                        <span class="text-sm text-red-500 flex items-center gap-1.5">{{ $gc['label'] }} <span class="text-xs font-semibold bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded">{{ $gc['badge'] }}</span></span>
                        <span class="text-sm font-bold text-red-500 tabular-nums font-mono">-&#x20B1;{{ number_format($gc['amount'], 2) }}</span>
                    </div>
                    @endif
                    @endforeach

                    {{-- CA / SL --}}
                    @if($caDeduction > 0)
                    <div class="flex items-center justify-between py-2.5">
                        <span class="text-sm text-red-500 flex items-center gap-1.5">Cash Advance <span class="text-xs font-semibold bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded">loan</span></span>
                        <span class="text-sm font-bold text-red-500 tabular-nums font-mono">-&#x20B1;{{ number_format($caDeduction, 2) }}</span>
                    </div>
                    @endif
                    @if($slDeduction > 0)
                    <div class="flex items-center justify-between py-2.5">
                        <span class="text-sm text-red-500 flex items-center gap-1.5">Salary Loan <span class="text-xs font-semibold bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded">loan</span></span>
                        <span class="text-sm font-bold text-red-500 tabular-nums font-mono">-&#x20B1;{{ number_format($slDeduction, 2) }}</span>
                    </div>
                    @endif

                    {{-- Initial Net Pay --}}
                    <div class="flex items-center justify-between py-2.5">
                        <span class="text-sm font-bold text-gray-900">Initial Net Pay</span>
                        <span class="text-base font-bold text-gray-900 tabular-nums font-mono">&#x20B1;{{ number_format($payroll->net_pay ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Row 2: Allowances + Deductions --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- Allowances --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-50 flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                </div>
                <div><p class="text-sm font-bold text-gray-900">Allowances <span class="font-normal text-gray-400 text-xs">(optional)</span></p><p class="text-xs text-gray-400">Transportation, meal, housing&hellip;</p></div>
            </div>
            <div class="p-4">
                <div class="flex flex-wrap gap-1.5 min-h-[28px] py-0.5">
                    @forelse($manualAllowances as $a)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="max-w-[120px] truncate">{{ $a->allowance_type }}</span>
                        <span class="tabular-nums font-mono opacity-85">&nbsp;&#x20B1;{{ number_format($a->amount, 2) }}</span>
                    </span>
                    @empty
                    <span class="text-xs text-gray-300 italic">None added yet.</span>
                    @endforelse
                </div>
                @if($manualAllowances->count() > 0)
                <div class="text-xs font-semibold text-emerald-600 text-right mt-1 font-mono tabular-nums">Total: &#x20B1;<span>{{ number_format($manualAllowances->sum('amount'), 2) }}</span></div>
                @endif
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
                <div class="flex flex-wrap gap-1.5 min-h-[28px] py-0.5">
                    @forelse($manualDeductions as $d)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                        <span class="max-w-[120px] truncate">{{ $d->deduction_type }}</span>
                        <span class="tabular-nums font-mono opacity-85">&nbsp;&#x20B1;{{ number_format($d->amount, 2) }}</span>
                    </span>
                    @empty
                    <span class="text-xs text-gray-300 italic">None added yet.</span>
                    @endforelse
                </div>
                @if($manualDeductions->count() > 0)
                <div class="text-xs font-semibold text-red-500 text-right mt-1 font-mono tabular-nums">Total: &#x20B1;<span>{{ number_format($manualDeductions->sum('amount'), 2) }}</span></div>
                @endif
            </div>
        </div>
    </div>

    {{-- Bonuses --}}
    @if($payroll->bonuses->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-50 flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-lg bg-violet-50 text-violet-600 flex items-center justify-center shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
                </div>
            <div><p class="text-sm font-bold text-gray-900">Bonuses</p><p class="text-xs text-gray-400">Performance, holiday, attendance, special</p></div>
        </div>
        <div class="p-4">
            <div class="flex flex-wrap gap-1.5 min-h-[28px] py-0.5">
                @foreach($payroll->bonuses as $b)
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-violet-50 text-violet-700 border border-violet-200">
                    <span class="max-w-[120px] truncate">{{ ucfirst($b->bonus_type) }}{{ $b->description ? ' — ' . $b->description : '' }}</span>
                    <span class="tabular-nums font-mono opacity-85">&nbsp;&#x20B1;{{ number_format($b->amount, 2) }}</span>
                </span>
                @endforeach
            </div>
            <div class="text-xs font-semibold text-violet-600 text-right mt-1 font-mono tabular-nums">Total: &#x20B1;<span>{{ number_format($payroll->bonuses->sum('amount'), 2) }}</span></div>
        </div>
    </div>
    @endif

    {{-- OT / UT Breakdown --}}
    @if($overtimeUndertimeBreakdown->count())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-50 flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 6v6l4 2"/></svg>
            </div>
            <div><p class="text-sm font-bold text-gray-900">Overtime & Undertime Records</p><p class="text-xs text-gray-400">Approved records within this payroll period</p></div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-50 bg-gray-50/50">
                        <th class="text-left text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-5 py-3">Date</th>
                        <th class="text-left text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-5 py-3">Type</th>
                        <th class="text-left text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-5 py-3">Hours</th>
                        <th class="text-left text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-5 py-3">Reason</th>
                        <th class="text-right text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-5 py-3">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($overtimeUndertimeBreakdown as $record)
                    @php
                        $isOT   = $record->type === 'overtime';
                        $amount = $record->amount !== null ? abs($record->amount) : round($payroll->hourly_rate * $record->hours, 2);
                    @endphp
                    <tr class="hover:bg-gray-50/40 transition-colors">
                        <td class="px-5 py-3 font-mono text-xs text-gray-500">{{ $record->date->format('M d, Y') }}</td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center gap-1 text-[0.5rem] font-semibold uppercase tracking-wide px-2 py-1 rounded-full {{ $isOT ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-500' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $isOT ? 'bg-emerald-400' : 'bg-red-400' }}"></span>
                                {{ ucfirst($record->type) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 font-mono text-xs text-gray-700">{{ number_format($record->hours, 2) }} hrs</td>
                        <td class="px-5 py-3 text-xs text-gray-400">{{ $record->reason ?? '—' }}</td>
                        <td class="px-5 py-3 text-right font-mono text-xs font-semibold {{ $isOT ? 'text-emerald-600' : 'text-red-500' }}">
                            {{ $isOT ? '+' : '−' }}₱{{ number_format($amount, 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Net Pay --}}
    <div class="net-pop bg-white rounded-xl shadow-sm border border-gray-100 px-6 py-5 flex items-center justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Net Pay</p>
            <p class="text-xs text-gray-400 mt-0.5">Basic + Allowances &#x2212; Deductions + Bonuses</p>
        </div>
        <span class="text-2xl font-extrabold text-gray-900 tabular-nums font-mono tracking-tight">₱{{ number_format($payroll->net_pay ?? 0, 2) }}</span>
    </div>

    {{-- Actions --}}
    <div class="flex items-center gap-3 pt-2">
        <a href="{{ route('payroll.generatePayslip', $payroll) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-gray-900 text-white rounded-xl text-sm font-semibold transition-all hover:bg-gray-800 active:scale-[0.97]">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Generate Payslip
        </a>
        <form action="{{ route('payroll.salary-computation.destroy', $payroll) }}" method="POST" data-sa-confirm="Delete this payroll record?">
            @csrf @method('DELETE')
            <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-white text-red-500 border border-red-200 rounded-xl text-sm font-semibold transition-all hover:bg-red-50 active:scale-[0.97] cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Delete
            </button>
        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
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
</script>
@endpush

@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeSlideUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { 0%{opacity:0;transform:scale(0.92)} 100%{opacity:1;transform:scale(1)} }
.stat-card { animation:scaleIn 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.stat-card:nth-child(1) { animation-delay:0.05s; }
.stat-card:nth-child(2) { animation-delay:0.1s; }
.stat-card:nth-child(3) { animation-delay:0.15s; }
.stat-card:nth-child(4) { animation-delay:0.2s; }
.fade-up { animation:fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }

/* Readability: darker labels/descriptions, bigger font for small text */
.text-gray-400 { color: #6b7280 !important; }
.text-gray-500 { color: #4b5563 !important; }
.stat-card p:first-child { font-size: 0.625rem !important; }
th { font-size: 0.625rem !important; }
</style>
@endpush

@section('content')
@php
$user = $payroll->user;
$sc = match($payroll->status) {
    'pending' => ['label'=>'Pending','dot'=>'bg-amber-400','text'=>'text-amber-600','bg'=>'bg-amber-50'],
    'submitted' => ['label'=>'Submitted','dot'=>'bg-violet-400','text'=>'text-violet-600','bg'=>'bg-violet-50'],
    'approved' => ['label'=>'Approved','dot'=>'bg-emerald-400','text'=>'text-emerald-600','bg'=>'bg-emerald-50'],
    'rejected' => ['label'=>'Rejected','dot'=>'bg-red-400','text'=>'text-red-600','bg'=>'bg-red-50'],
    default => ['label'=>'Pending','dot'=>'bg-amber-400','text'=>'text-amber-600','bg'=>'bg-amber-50'],
};
$overtimeAllowances  = $payroll->allowances->filter(fn($a) => str_starts_with($a->allowance_type, 'Overtime Pay'));
$leavePayAllowances  = $payroll->allowances->filter(fn($a) => str_starts_with($a->allowance_type, 'Leave Pay'));
$regularAllowances   = $payroll->allowances->reject(fn($a) => str_starts_with($a->allowance_type, 'Overtime Pay') || str_starts_with($a->allowance_type, 'Leave Pay'));
$undertimeDeductions = $payroll->deductions->filter(fn($d) => str_starts_with($d->deduction_type, 'Undertime Deduction'));
$regularDeductions   = $payroll->deductions->reject(fn($d) => str_starts_with($d->deduction_type, 'Undertime Deduction'));
$caDeduction  = (float) ($payroll->cash_advance_deduction ?? 0);
$slDeduction  = (float) ($payroll->salary_loan_deduction ?? 0);
$otAllowanceTotal = $overtimeAllowances->sum('amount');
$leavePayTotal = $leavePayAllowances->sum('amount');
$leavePayDays = (int) $leavePayAllowances->sum('hours');
$regularAllowanceTotal = $regularAllowances->sum('amount');
$holidayBreakdown = $payroll->holiday_breakdown ?? [];
$holidayPay = $payroll->holiday_pay;
$daysAbsent = \App\Models\Attendance::where('user_id', $payroll->user_id)
    ->whereBetween('date', [$payroll->payroll_period_start, $payroll->payroll_period_end])
    ->where('status', 'absent')->count();
@endphp

<div class="max-w-4xl mx-auto space-y-5">

    {{-- Page title --}}
    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold tracking-widest text-gray-400 uppercase">Accountant Portal</p>
            <h1 class="text-lg font-extrabold tracking-tight text-gray-900">Payroll Approval</h1>
        </div>
        </div>

    {{-- Back link --}}
    <button type="button" onclick="history.back()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-600 transition-all hover:bg-gray-50 active:scale-[0.97] mb-2 cursor-pointer">
        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Back
    </button>

    {{-- Employee header --}}
    <div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 px-6 py-5 flex items-center gap-4">
        <span class="w-12 h-12 rounded-full bg-gray-100 border-2 border-gray-200 flex items-center justify-center text-base font-extrabold text-gray-600 shrink-0 uppercase">{{ strtoupper(substr($user->first_name??'U',0,1).substr($user->last_name??'',0,1)) }}</span>
        <div class="flex-1">
            <h1 class="text-lg font-extrabold text-gray-900 tracking-tight">{{ $user->first_name }} {{ $user->last_name }}</h1>
            <p class="text-xs text-gray-400">{{ $user->position ?? ($user->department ?? 'Employee') }}</p>
            <p class="text-xs text-gray-400 mt-0.5 font-mono">{{ $payroll->payroll_period_start->format('F d, Y') }} &mdash; {{ $payroll->payroll_period_end->format('F d, Y') }}</p>
        </div>
        <div class="flex flex-col items-end gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold uppercase tracking-wide {{ $sc['bg'] }} {{ $sc['text'] }}">
                <span class="w-2 h-2 rounded-full {{ $sc['dot'] }}"></span>
                {{ $sc['label'] }}
            </span>
            @if($payroll->approvedBy)
                <span class="text-[0.55rem] font-mono text-gray-500">Approved by {{ $payroll->approvedBy->first_name }} {{ $payroll->approvedBy->last_name }}</span>
            @endif
        </div>
    </div>

    {{-- Stat chips --}}
    <div class="grid grid-cols-4 gap-3">
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400">Days Worked</p>
            <p class="text-lg font-extrabold text-gray-900 tabular-nums mt-1">{{ $payroll->days_worked }}</p>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400">Absent</p>
            <p class="text-lg font-extrabold {{ $daysAbsent > 0 ? 'text-red-500' : 'text-gray-900' }} tabular-nums mt-1">{{ $daysAbsent }}</p>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400">Daily Rate</p>
            <p class="text-lg font-extrabold text-gray-900 tabular-nums mt-1">₱{{ number_format($payroll->per_day_rate, 2) }}</p>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400">Hourly Rate</p>
            <p class="text-lg font-extrabold text-gray-900 tabular-nums mt-1">₱{{ number_format($payroll->hourly_rate, 2) }}</p>
        </div>
    </div>

    {{-- Earnings --}}
    <div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-50 flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div><p class="text-sm font-bold text-gray-900">Earnings</p><p class="text-xs text-gray-400">Basic pay, overtime, and allowances</p></div>
        </div>
        <div class="px-5 py-4 divide-y divide-gray-50">
            <div class="flex items-center justify-between py-2.5">
                <span class="text-sm text-gray-600">Basic Pay <span class="text-[0.5rem] font-semibold uppercase tracking-wide text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded ml-1.5">daily rate &times; {{ $payroll->days_worked }} days</span></span>
                <span class="text-sm font-semibold text-gray-900 tabular-nums font-mono">₱{{ number_format($payroll->basic_salary, 2) }}</span>
            </div>

            @foreach($holidayBreakdown as $hb)
            <div class="flex items-center justify-between py-2.5">
                <span class="text-sm text-emerald-600 flex items-center gap-1.5">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    {{ $hb['label'] ?? 'Holiday Pay' }}
                </span>
                <span class="text-sm font-semibold text-emerald-600 tabular-nums font-mono">+₱{{ number_format($hb['amount'], 2) }}</span>
            </div>
            @endforeach

            @php $holidayOTPay = (float)($payroll->holiday_ot_pay ?? 0); $holidayOTHours = (float)($payroll->holiday_ot_hours ?? 0); @endphp
            @if($holidayOTPay > 0)
            <div class="flex items-center justify-between py-2.5">
                <span class="text-sm text-emerald-600 flex items-center gap-1.5">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Holiday Overtime Pay
                    @if($holidayOTHours > 0)<span class="text-[0.5rem] font-semibold uppercase tracking-wide text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded ml-1">{{ number_format($holidayOTHours, 2) }} hrs</span>@endif
                </span>
                <span class="text-sm font-semibold text-emerald-600 tabular-nums font-mono">+₱{{ number_format($holidayOTPay, 2) }}</span>
            </div>
            @endif

            @foreach($overtimeAllowances as $ot)
            <div class="flex items-center justify-between py-2.5">
                <span class="text-sm text-emerald-600 flex items-center gap-1.5">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    {{ $ot->allowance_type }}
                    @if($ot->hours)<span class="text-[0.5rem] font-semibold uppercase tracking-wide text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded ml-1">{{ $ot->hours }} hrs</span>@endif
                </span>
                <span class="text-sm font-semibold text-emerald-600 tabular-nums font-mono">+₱{{ number_format($ot->amount, 2) }}</span>
            </div>
            @endforeach

            @foreach($leavePayAllowances as $la)
            @php
                $_laName = str_replace(['Leave Pay (', ')'], '', $la->allowance_type);
                $_laDays = (int)($la->hours ?? 0);
            @endphp
            <div class="flex items-center justify-between py-2.5">
                <span class="text-sm text-emerald-600 flex items-center gap-1.5">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    {{ $_laName }}
                    <span class="text-[0.5rem] font-semibold uppercase tracking-wide text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded ml-1">{{ $_laDays }} day{{ $_laDays !== 1 ? 's' : '' }}</span>
                </span>
                <span class="text-sm font-semibold text-emerald-600 tabular-nums font-mono">+₱{{ number_format($la->amount, 2) }}</span>
            </div>
            @endforeach

            @foreach($regularAllowances as $allow)
            <div class="flex items-center justify-between py-2.5">
                <span class="text-sm text-emerald-600 flex items-center gap-1.5">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    {{ $allow->allowance_type }}
                </span>
                <span class="text-sm font-semibold text-emerald-600 tabular-nums font-mono">+₱{{ number_format($allow->amount, 2) }}</span>
            </div>
            @endforeach

            @foreach($payroll->bonuses as $bonus)
            <div class="flex items-center justify-between py-2.5">
                <span class="text-sm text-purple-600 flex items-center gap-1.5">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    {{ $bonus->bonus_type }}
                    @if($bonus->description)<span class="text-[0.5rem] font-semibold uppercase tracking-wide text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded ml-1">{{ $bonus->description }}</span>@endif
                </span>
                <span class="text-sm font-semibold text-purple-600 tabular-nums font-mono">+₱{{ number_format($bonus->amount, 2) }}</span>
            </div>
            @endforeach

            <div class="flex items-center justify-between py-3 mt-1 border-t-2 border-gray-100">
                <span class="text-sm font-bold text-gray-900">Gross Pay</span>
                <span class="text-base font-bold text-gray-900 tabular-nums font-mono">₱{{ number_format($payroll->gross_pay, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- Deductions --}}
    <div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-50 flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-lg bg-red-50 text-red-500 flex items-center justify-center shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
            </div>
            <div><p class="text-sm font-bold text-gray-900">Deductions</p><p class="text-xs text-gray-400">Statutory contributions and other deductions</p></div>
        </div>
        <div class="px-5 py-4 divide-y divide-gray-50">
            @forelse($regularDeductions as $d)
            <div class="flex items-center justify-between py-2.5">
                <span class="text-sm text-red-500 flex items-center gap-1.5">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
                    {{ $d->deduction_type === 'Late Deduction' ? 'Tardiness' : $d->deduction_type }}
                    @if($d->description)<span class="text-[0.5rem] font-semibold uppercase tracking-wide text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded ml-1">{{ $d->description }}</span>@endif
                </span>
                <span class="text-sm font-semibold text-red-500 tabular-nums font-mono">−₱{{ number_format($d->amount, 2) }}</span>
            </div>
            @empty
            @endforelse

            @foreach($undertimeDeductions as $ut)
            <div class="flex items-center justify-between py-2.5">
                <span class="text-sm text-red-500 flex items-center gap-1.5">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
                    Undertime <span class="text-[0.5rem] font-semibold uppercase tracking-wide text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded ml-1">attendance-based</span>
                </span>
                <span class="text-sm font-semibold text-red-500 tabular-nums font-mono">−₱{{ number_format($ut->amount, 2) }}</span>
            </div>
            @endforeach

            @if($caDeduction > 0)
            <div class="flex items-center justify-between py-2.5">
                <span class="text-sm text-red-500 flex items-center gap-1.5">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
                    Cash Advance <span class="text-[0.5rem] font-semibold uppercase tracking-wide text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded ml-1">loan</span>
                </span>
                <span class="text-sm font-semibold text-red-500 tabular-nums font-mono">−₱{{ number_format($caDeduction, 2) }}</span>
            </div>
            @endif

            @if($slDeduction > 0)
            <div class="flex items-center justify-between py-2.5">
                <span class="text-sm text-red-500 flex items-center gap-1.5">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
                    Salary Loan <span class="text-[0.5rem] font-semibold uppercase tracking-wide text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded ml-1">loan</span>
                </span>
                <span class="text-sm font-semibold text-red-500 tabular-nums font-mono">−₱{{ number_format($slDeduction, 2) }}</span>
            </div>
            @endif

            @if($regularDeductions->isEmpty() && $undertimeDeductions->isEmpty() && $caDeduction <= 0 && $slDeduction <= 0)
            <div class="flex items-center justify-between py-2.5">
                <span class="text-sm text-gray-300 italic">No deductions</span>
                <span></span>
            </div>
            @endif

            <div class="flex items-center justify-between py-3 mt-1 border-t-2 border-gray-100">
                <span class="text-sm font-bold text-gray-900">Total Deductions</span>
                <span class="text-base font-bold text-red-500 tabular-nums font-mono">₱{{ number_format($payroll->total_deductions, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- OT / UT Breakdown --}}
    @if($overtimeUndertimeBreakdown->count())
    <div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
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
    <div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 px-6 py-5 flex items-center justify-between">
        <span class="text-xs font-semibold uppercase tracking-widest text-gray-400">Net Pay</span>
        <span class="text-2xl font-extrabold text-gray-900 tabular-nums font-mono">₱{{ number_format($payroll->net_pay, 2) }}</span>
    </div>

    {{-- Employee info --}}
    <div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-50 flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div><p class="text-sm font-bold text-gray-900">Employee Information</p><p class="text-xs text-gray-400">For reference only</p></div>
        </div>
        <div class="px-5 py-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3">
                <div>
                    <p class="text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Full Name</p>
                    <p class="text-sm font-semibold text-gray-900 mt-0.5">{{ $user->first_name }} {{ $user->middle_name ? $user->middle_name . ' ' : '' }}{{ $user->last_name }}</p>
                </div>
                <div>
                    <p class="text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Position / Department</p>
                    <p class="text-sm text-gray-700 mt-0.5">{{ $user->position ?? '—' }} {{ $user->department ? '· ' . $user->department : '' }}</p>
                </div>
                @if($user->sss_number)
                <div>
                    <p class="text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">SSS Number</p>
                    <p class="text-sm font-mono text-gray-700 mt-0.5">{{ $user->sss_number }}</p>
                </div>
                @endif
                @if($user->pagibig_number)
                <div>
                    <p class="text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Pag-IBIG Number</p>
                    <p class="text-sm font-mono text-gray-700 mt-0.5">{{ $user->pagibig_number }}</p>
                </div>
                @endif
                @if($user->philhealth_number)
                <div>
                    <p class="text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">PhilHealth Number</p>
                    <p class="text-sm font-mono text-gray-700 mt-0.5">{{ $user->philhealth_number }}</p>
                </div>
                @endif
                @if($user->tin_number)
                <div>
                    <p class="text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">TIN</p>
                    <p class="text-sm font-mono text-gray-700 mt-0.5">{{ $user->tin_number }}</p>
                </div>
                @endif
                @if($user->date_of_hire)
                <div>
                    <p class="text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Date of Hire</p>
                    <p class="text-sm text-gray-700 mt-0.5">{{ $user->date_of_hire->format('F d, Y') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="fade-up flex items-center gap-3 pt-2">
        <a href="{{ route('payroll.generatePayslip', $payroll) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-gray-900 text-white rounded-xl text-sm font-semibold transition-all hover:bg-gray-800 active:scale-[0.97]">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Generate Payslip
        </a>
    </div>

</div>
@endsection
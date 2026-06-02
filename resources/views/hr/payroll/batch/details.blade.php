@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeSlideUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { 0%{opacity:0;transform:scale(0.92)} 100%{opacity:1;transform:scale(1)} }
.fade-up { animation:fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.stat-card { animation:scaleIn 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.stat-card:nth-child(1) { animation-delay:0.05s; }
.stat-card:nth-child(2) { animation-delay:0.1s; }
.stat-card:nth-child(3) { animation-delay:0.15s; }
</style>
@endpush

@section('content')
<div class="space-y-5">

    @php
        $start = $batch->period_start;
        $end   = $batch->period_end;
        $statusInfo = match($batch->status) {
            'draft' => ['label'=>'Draft','dot'=>'bg-amber-300','text'=>'text-amber-500','bg'=>'bg-amber-50'],
            'submitted' => ['label'=>'Submitted','dot'=>'bg-violet-400','text'=>'text-violet-600','bg'=>'bg-violet-50'],
            'approved' => ['label'=>'Approved','dot'=>'bg-emerald-400','text'=>'text-emerald-600','bg'=>'bg-emerald-50'],
            'rejected' => ['label'=>'Rejected','dot'=>'bg-red-400','text'=>'text-red-600','bg'=>'bg-red-50'],
            default => ['label'=>'Draft','dot'=>'bg-amber-300','text'=>'text-amber-500','bg'=>'bg-amber-50'],
        };
        $payrolls = $batch->payrolls;
        $totalGross = $payrolls->sum('gross_pay');
        $totalDeductions = $payrolls->sum('total_deductions');
        $totalNet = $payrolls->sum('net_pay');
    @endphp

    {{-- Rejection banner --}}
    @if($batch->status === 'rejected' && $batch->rejection_note)
    <div class="fade-up bg-red-50 border border-red-200 rounded-xl px-4 py-3.5 flex items-start gap-3">
        <div class="w-8 h-8 rounded-lg bg-red-100 text-red-500 flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-bold text-red-700">Batch Rejected</p>
            <p class="text-xs text-red-500 mt-0.5 font-mono">
                by {{ optional($batch->rejectedBy)->first_name }} {{ optional($batch->rejectedBy)->last_name }}
                @if($batch->rejected_at) &middot; {{ $batch->rejected_at->format('M d, Y \a\t h:i A') }} @endif
            </p>
            <p class="text-xs text-red-600 mt-2 bg-red-100/60 rounded-lg px-3 py-2">{{ $batch->rejection_note }}</p>
        </div>
    </div>
    @endif

    {{-- Header --}}
    <div class="fade-up flex items-start justify-between gap-4">
        <div class="min-w-0">
            <a href="{{ route('payroll.salary-computation.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl text-xs font-semibold no-underline hover:border-gray-600 hover:text-gray-600 hover:bg-gray-50 transition-colors whitespace-nowrap cursor-pointer mb-2">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to batches
            </a>
            <div class="flex items-center gap-2">
                <h1 class="text-xl font-bold text-gray-900 tracking-tight">{{ $batch->display_name }}</h1>
            </div>
            <p class="text-xs text-gray-400 mt-0.5 font-mono">{{ $start->format('M d, Y') }} &ndash; {{ $end->format('M d, Y') }}</p>
        </div>
        <div class="flex items-center gap-2 shrink-0 flex-wrap">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold uppercase tracking-wide {{ $statusInfo['bg'] }} {{ $statusInfo['text'] }}">
                <span class="w-2 h-2 rounded-full {{ $statusInfo['dot'] }}"></span>
                {{ $statusInfo['label'] }}
            </span>
            @if($batch->status !== 'rejected')
                <a href="{{ route('payroll.batch.payslips', $batch) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-600 transition-all hover:bg-gray-50 active:scale-[0.97]">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Payslips
                </a>
            @endif
            @if(in_array(auth()->user()?->role, ['hr','superadmin','qr_admin','accountant'], true) && $batch->status !== 'approved')
                <a href="{{ route('payroll.batch.confirm', $batch) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-600 transition-all hover:bg-gray-50 active:scale-[0.97]">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit Batch
                </a>
            @endif
            @if(auth()->user()?->role === 'accountant' && $batch->status === 'submitted')
                <form action="{{ route('payroll-approval.approve-batch') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="batch_id" value="{{ $batch->id }}">
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 text-white rounded-lg text-xs font-semibold transition-all hover:bg-emerald-700 active:scale-[0.97]">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Approve
                    </button>
                </form>
                <form action="{{ route('payroll-approval.reject-batch') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="batch_id" value="{{ $batch->id }}">
                    <div class="flex items-center gap-1">
                        <input type="text" name="rejection_note" required minlength="3" placeholder="Reason (required)" class="w-44 px-2.5 py-1.5 rounded-lg border border-gray-200 text-xs outline-none focus:border-red-300 focus:ring-2 focus:ring-red-100">
                        <button type="submit" class="px-3 py-1.5 bg-red-50 text-red-600 border border-red-200 rounded-lg text-xs font-semibold transition-all hover:bg-red-100 active:scale-[0.97]">Reject</button>
                    </div>
                </form>
            @endif
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-3">
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs text-gray-400 font-medium">Employees</p>
            <p class="text-lg font-bold text-gray-900 tabular-nums mt-1">{{ $payrolls->count() }}</p>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs text-gray-400 font-medium">Gross Pay</p>
            <p class="text-lg font-bold text-gray-900 tabular-nums mt-1">₱{{ number_format($totalGross, 2) }}</p>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs text-gray-400 font-medium">Net Pay</p>
            <p class="text-lg font-bold text-emerald-600 tabular-nums mt-1">₱{{ number_format($totalNet, 2) }}</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="text-left text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Employee</th>
                        <th class="text-left text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Basic Pay</th>
                        <th class="text-left text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Addl. Earnings</th>
                        <th class="text-left text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Gross Pay</th>
                        <th class="text-left text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Deductions</th>
                        <th class="text-left text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Net Pay</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($payrolls as $p)
                    @php $u = $p->user; @endphp
                    <tr onclick="window.location='{{ route('payroll.salary-computation.show', $p) }}'" class="transition-colors hover:bg-gray-50/40 cursor-pointer">
                        <td class="px-4 py-3">
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-gray-900 truncate">{{ $u->last_name }}, {{ $u->first_name }}</p>
                                <p class="text-[0.55rem] text-gray-400 truncate font-mono">{{ $u->position ?? '—' }}</p>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-left text-xs font-semibold text-gray-900 tabular-nums">₱{{ number_format($p->basic_salary ?? 0, 2) }}</td>
                        <td class="px-4 py-3 text-left text-xs font-semibold text-emerald-500 tabular-nums">₱{{ number_format(($p->total_allowances ?? 0) + ($p->total_bonuses ?? 0) + ($p->holiday_pay ?? 0) + ($p->holiday_ot_pay ?? 0),2) }}</td>
                        <td class="px-4 py-3 text-left text-xs font-semibold text-gray-900 tabular-nums">₱{{ number_format($p->gross_pay ?? 0,2) }}</td>
                        <td class="px-4 py-3 text-left text-xs font-semibold text-red-500 tabular-nums">₱{{ number_format($p->total_deductions ?? 0,2) }}</td>
                        <td class="px-4 py-3 text-left text-xs font-bold text-gray-900 tabular-nums">₱{{ number_format($p->net_pay,2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-xs text-gray-400">No employees in this batch.</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($payrolls->isNotEmpty())
                <tfoot>
                    <tr class="border-t border-gray-100 bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-bold text-gray-900">Totals</td>
                        <td class="px-4 py-3 text-left text-xs font-bold text-gray-900 tabular-nums">₱{{ number_format($payrolls->sum('basic_salary'), 2) }}</td>
                        <td class="px-4 py-3 text-left text-xs font-bold text-emerald-500 tabular-nums">₱{{ number_format(($payrolls->sum('total_allowances') ?? 0) + ($payrolls->sum('total_bonuses') ?? 0) + ($payrolls->sum('holiday_pay') ?? 0) + ($payrolls->sum('holiday_ot_pay') ?? 0),2) }}</td>
                        <td class="px-4 py-3 text-left text-xs font-bold text-gray-900 tabular-nums">₱{{ number_format($totalGross, 2) }}</td>
                        <td class="px-4 py-3 text-left text-xs font-bold text-red-500 tabular-nums">₱{{ number_format($totalDeductions, 2) }}</td>
                        <td class="px-4 py-3 text-left text-xs font-bold text-gray-900 tabular-nums">₱{{ number_format($totalNet, 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  var fmt = function(n) { return n.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2}); };
  var targets = document.querySelectorAll('.tabular-nums');
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

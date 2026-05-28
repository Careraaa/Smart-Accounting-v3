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
.modal-card { animation:scaleIn 0.25s cubic-bezier(0.16,1,0.3,1) both; }
.modal-overlay { animation:fadeSlideUp 0.2s ease-out both; }
</style>
@endpush

@section('content')
<div class="space-y-5">

    {{-- Flash --}}
    @foreach(['success','error','info'] as $t)
        @if(session($t))
        <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl text-sm font-medium animate-[fadeSlideUp_0.3s_ease] @switch($t) @case('success') bg-green-50 text-green-700 @break @case('error') bg-red-50 text-red-700 @break @case('info') bg-blue-50 text-blue-700 @break @endswitch">
            <span>{{ session($t) }}</span>
        </div>
        @endif
    @endforeach

    <div class="fade-up">
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Payroll</h1>
        <p class="text-sm text-gray-400 mt-0.5">Manage batches, review computations, and submit for approval</p>
    </div>

    {{-- Metrics --}}
    @php
        $metrics = [
            ['label'=>'Active Employees','value'=>$activeEmployees,'accent'=>'rose'],
            ['label'=>'Pending Payrolls','value'=>$payrollCount,'accent'=>'amber'],
            ['label'=>'Released','value'=>$releasedCount,'accent'=>'emerald'],
            ['label'=>'Total Payroll','value'=>'₱'.number_format($totalPayroll,2),'accent'=>'blue'],
        ];
        $accentMap = ['rose'=>['icon'=>'bg-rose-50 text-rose-500','dot'=>'bg-rose-500'],'amber'=>['icon'=>'bg-amber-50 text-amber-500','dot'=>'bg-amber-500'],'emerald'=>['icon'=>'bg-emerald-50 text-emerald-500','dot'=>'bg-emerald-500'],'blue'=>['icon'=>'bg-blue-50 text-blue-500','dot'=>'bg-blue-500']];
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        @foreach($metrics as $m)
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <span class="w-2 h-2 rounded-full {{ $accentMap[$m['accent']]['dot'] }} shrink-0"></span>
                <div class="min-w-0">
                    <p class="text-xs text-gray-400 font-medium">{{ $m['label'] }}</p>
                    <p class="text-lg font-bold text-gray-900 tabular-nums mt-0.5">{{ $m['value'] }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Generate row --}}
    <div class="fade-up flex items-center justify-between gap-4 bg-white rounded-xl shadow-sm border border-gray-100 px-5 py-4">
        <div class="min-w-0">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">
                @if($currentInProgressBatch) Batch in progress @else Ready to generate @endif
            </p>
            <h2 class="text-base font-bold text-gray-900 mt-0.5">
                @if($currentInProgressBatch) Continue Batch @else Generate New Batch @endif
            </h2>
            <p class="text-xs text-gray-400 mt-0.5 font-mono">
                @if($currentInProgressBatch)
                    {{ \Carbon\Carbon::parse($currentInProgressBatch->period_start)->format('M d, Y') }} &mdash; {{ \Carbon\Carbon::parse($currentInProgressBatch->period_end)->format('M d, Y') }}
                @else
                    Select a period from the current or past 24 months
                @endif
            </p>
        </div>
        <div class="shrink-0">
            @if($currentInProgressBatch)
                <a href="{{ route('payroll.batch.confirm', $currentInProgressBatch) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-900 text-white rounded-lg text-sm font-semibold transition-all hover:bg-gray-800 active:scale-[0.97]">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Continue
                </a>
            @else
                <button type="button" onclick="openGenerateModal()" class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-900 text-white rounded-lg text-sm font-semibold transition-all hover:bg-gray-800 active:scale-[0.97]">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Generate
                </button>
            @endif
        </div>
    </div>

    {{-- Batches --}}
    <div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-50 flex items-center justify-between">
            <span class="text-sm font-semibold text-gray-900">Batches</span>
            <span class="text-xs text-gray-400 tabular-nums">{{ $allBatches->count() }} total</span>
        </div>
        <div class="divide-y divide-gray-50" id="salaryBatchesGrid"></div>
        <div id="salaryBatchesNoResults" class="hidden">
            <div class="flex flex-col items-center py-12 text-center">
                <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-300 mb-3">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <p class="text-sm font-semibold text-gray-500" id="salaryBatchesEmptyTitle">No batches</p>
                <p class="text-xs text-gray-400 mt-0.5" id="salaryBatchesEmptySub">Generate your first batch above</p>
            </div>
        </div>
        <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100 bg-gray-50/50 flex-wrap gap-3">
            <div class="text-xs text-gray-400" id="salaryBatchesInfo">Showing <strong class="text-gray-700">0</strong> batches</div>
            <nav id="salaryBatchesNav" class="flex items-center gap-1"></nav>
        </div>
    </div>

</div>

{{-- Generate modal --}}
<div class="fixed inset-0 z-[9999] flex items-center justify-center p-5 hidden modal-overlay" id="generateModal">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('generateModal')"></div>
    <div class="relative bg-white rounded-2xl p-6 max-w-sm w-full shadow-2xl modal-card">
        <h3 class="text-base font-bold text-gray-900">Generate Batch</h3>
        <p class="text-xs text-gray-400 mt-1 mb-4">Select a payroll period to create a new batch.</p>
        <form action="{{ route('payroll.batch.generate') }}" method="POST">
            @csrf
            <select name="period" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 bg-gray-50 outline-none transition-all focus:border-gray-400 focus:bg-white" required>
                <option value="">Select a period&hellip;</option>
                @foreach($availablePeriods as $period)
                <option value="{{ $period['start'] }}|{{ $period['end'] }}"
                        @if($period['start'] === $currentPeriod['start'] && $period['end'] === $currentPeriod['end']) selected @endif>
                    {{ $period['display'] }}
                </option>
                @endforeach
            </select>
            <div class="flex gap-2 mt-4">
                <button type="button" onclick="closeModal('generateModal')" class="flex-1 py-2.5 bg-white text-gray-600 border border-gray-200 rounded-xl text-sm font-semibold transition-all hover:bg-gray-50 active:scale-[0.97] cursor-pointer">Cancel</button>
                <button type="submit" class="flex-1 py-2.5 bg-gray-900 text-white rounded-xl text-sm font-semibold transition-all hover:bg-gray-800 active:scale-[0.97] cursor-pointer">Generate</button>
            </div>
        </form>
    </div>
</div>

{{-- Resubmit modal --}}
<div class="fixed inset-0 z-[9999] flex items-center justify-center p-5 hidden modal-overlay" id="resubmitModal">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('resubmitModal')"></div>
    <div class="relative bg-white rounded-2xl p-6 max-w-sm w-full shadow-2xl modal-card">
        <h3 class="text-base font-bold text-gray-900" id="resubmitModalTitle">Reopen Batch?</h3>
        <p class="text-xs text-gray-400 mt-1 mb-4" id="resubmitModalBody">This will reopen the batch for editing. You will need to finalize and resubmit afterward.</p>
        <div class="text-center mb-4">
            <span class="inline-block font-mono text-xs bg-gray-50 px-3 py-1.5 rounded-lg text-gray-600" id="resubmitModalPeriod"></span>
        </div>
        <div class="flex gap-2">
            <button type="button" onclick="closeModal('resubmitModal')" class="flex-1 py-2.5 bg-white text-gray-600 border border-gray-200 rounded-xl text-sm font-semibold transition-all hover:bg-gray-50 active:scale-[0.97] cursor-pointer">Cancel</button>
            <button type="button" class="flex-1 py-2.5 bg-amber-600 text-white rounded-xl text-sm font-semibold transition-all hover:bg-amber-700 active:scale-[0.97] cursor-pointer" id="resubmitConfirmBtn" onclick="submitReopenForm()">Reopen</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.allPayrollBatches = {!! json_encode($allBatches->map(fn($b) => [
    'id' => $b->id,
    'period_start' => $b->period_start->format('Y-m-d'),
    'period_end' => $b->period_end->format('Y-m-d'),
    'period_start_display' => $b->period_start->format('M d'),
    'period_end_display' => $b->period_end->format('M d, Y'),
    'month_year' => $b->period_start->format('F Y'),
    'half' => (int) $b->period_start->format('d') <= 15 ? '1st' : '2nd',
    'is_first' => (int) $b->period_start->format('d') <= 15,
    'status' => $b->status ?? 'submitted',
    'payrolls_count' => $b->payrolls_count ?? 0,
    'gross_pay' => (float) ($b->payrolls_sum_gross_pay ?? 0),
    'total_deductions' => (float) ($b->payrolls_sum_total_deductions ?? 0),
    'net_pay' => (float) ($b->payrolls_sum_net_pay ?? 0),
    'url' => route('payroll.batch.details', $b),
])) !!};

(function () {
    const grid     = document.getElementById('salaryBatchesGrid');
    const noRes    = document.getElementById('salaryBatchesNoResults');
    const emptyT   = document.getElementById('salaryBatchesEmptyTitle');
    const emptyS   = document.getElementById('salaryBatchesEmptySub');
    const PER = 5;
    let page = 1;

    const STATUS_MAP = {
        submitted: { label: 'Submitted', dot: 'bg-violet-400', text: 'text-violet-600', bg: 'bg-violet-50' },
        approved:  { label: 'Approved',  dot: 'bg-emerald-400', text: 'text-emerald-600', bg: 'bg-emerald-50' },
        rejected:  { label: 'Rejected',  dot: 'bg-red-400', text: 'text-red-600', bg: 'bg-red-50' },
    };

    function render() {
        const total = window.allPayrollBatches.length;
        const pages = Math.ceil(total / PER);
        const start = (page - 1) * PER;
        const end = Math.min(start + PER, total);
        const pageData = window.allPayrollBatches.slice(start, end);
        grid.innerHTML = '';

        if (pageData.length === 0) {
            noRes.classList.remove('hidden');
            emptyT.textContent = total === 0 ? 'No batches' : 'No results found';
            emptyS.textContent = total === 0 ? 'Generate your first batch above' : 'Try a different filter.';
        } else {
            noRes.classList.add('hidden');
            pageData.forEach(b => {
                const si = STATUS_MAP[b.status] || STATUS_MAP.submitted;
                const iconSvg = b.is_first
                    ? '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>'
                    : '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';

                const div = document.createElement('div');
                div.innerHTML = `
                    <a href="${b.url}" class="flex items-center gap-4 px-5 py-3.5 transition-colors hover:bg-gray-50/60 group">
                        <div class="w-9 h-9 rounded-lg bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-400 group-hover:text-gray-600 transition-colors shrink-0">${iconSvg}</div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-semibold text-gray-900">${b.month_year} &mdash; ${b.half}</span>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[0.6rem] font-semibold uppercase tracking-wide ${si.bg} ${si.text}">
                                    <span class="w-1.5 h-1.5 rounded-full ${si.dot}"></span>
                                    ${si.label}
                                </span>
                            </div>
                            <p class="text-xs text-gray-400 mt-0.5 font-mono">${b.period_start_display} &ndash; ${b.period_end_display}</p>
                        </div>
                        <div class="hidden sm:flex items-center gap-6 shrink-0">
                            <div class="text-right min-w-[44px]">
                                <p class="text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400">Count</p>
                                <p class="text-sm font-semibold text-gray-900 tabular-nums">${b.payrolls_count}</p>
                            </div>
                            <div class="text-right min-w-[88px]">
                                <p class="text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400">Gross</p>
                                <p class="text-sm font-semibold text-gray-900 tabular-nums">₱${b.gross_pay.toLocaleString('en-US', {minimumFractionDigits:2,maximumFractionDigits:2})}</p>
                            </div>
                            <div class="text-right min-w-[88px]">
                                <p class="text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400">Deductions</p>
                                <p class="text-sm font-semibold text-red-500 tabular-nums">₱${b.total_deductions.toLocaleString('en-US', {minimumFractionDigits:2,maximumFractionDigits:2})}</p>
                            </div>
                            <div class="text-right min-w-[88px]">
                                <p class="text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400">Net</p>
                                <p class="text-sm font-bold text-emerald-600 tabular-nums">₱${b.net_pay.toLocaleString('en-US', {minimumFractionDigits:2,maximumFractionDigits:2})}</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-300 group-hover:text-gray-400 transition-colors shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                `;
                grid.appendChild(div);
            });
        }
        updatePagination();
    }

    function updatePagination() {
        const total = window.allPayrollBatches.length;
        const pages = Math.ceil(total / PER);
        const info  = document.getElementById('salaryBatchesInfo');
        const nav   = document.getElementById('salaryBatchesNav');
        if (!info || !nav) return;
        if (total === 0) { info.innerHTML = 'No batches to display'; nav.innerHTML = ''; return; }
        const s = (page - 1) * PER + 1, e = Math.min(page * PER, total);
        info.innerHTML = `Showing <strong class="text-gray-700">${s}</strong>–<strong class="text-gray-700">${e}</strong> of <strong class="text-gray-700">${total}</strong>`;
        if (pages <= 1) { nav.innerHTML = ''; return; }

        const base = 'flex items-center justify-center w-7 h-7 rounded-lg text-xs font-semibold border transition-all duration-150';
        const act  = base + ' bg-gray-900 text-white border-gray-900';
        const def  = base + ' bg-white text-gray-600 border-gray-200 hover:bg-gray-50 hover:text-gray-900 hover:border-gray-300';
        const dis  = base + ' bg-gray-50 text-gray-300 border-gray-100 cursor-not-allowed pointer-events-none';

        let html = '';
        html += `<button data-p="${page - 1}" class="${page === 1 ? dis : def}">‹</button>`;
        for (let i = 1; i <= pages; i++) {
            html += `<button data-p="${i}" class="${i === page ? act : def}">${i}</button>`;
        }
        html += `<button data-p="${page + 1}" class="${page === pages ? dis : def}">›</button>`;
        nav.innerHTML = html;
        nav.querySelectorAll('button[data-p]').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                const p = parseInt(btn.dataset.p);
                if (p < 1 || p > pages) return;
                page = p;
                render();
            });
        });
    }

    render();
})();

function openGenerateModal() { openModal('generateModal'); }
function openModal(id) {
    document.querySelectorAll('[id$="Modal"]').forEach(function(el) { el.classList.add('hidden'); });
    var m = document.getElementById(id);
    if (m) m.classList.remove('hidden');
}
function closeModal(id) {
    var m = document.getElementById(id);
    if (m) m.classList.add('hidden');
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('[id$="Modal"]').forEach(function(m) {
            if (!m.classList.contains('hidden')) closeModal(m.id);
        });
    }
});
</script>
@endpush

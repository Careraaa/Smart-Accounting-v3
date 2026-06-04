@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { 0%{opacity:0;transform:scale(0.93)} 100%{opacity:1;transform:scale(1)} }
.fade-up { animation:fadeUp 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.scale-in { animation:scaleIn 0.35s cubic-bezier(0.16,1,0.3,1) both; }
.stat-card:nth-child(1) { animation-delay:0.05s; }
.stat-card:nth-child(2) { animation-delay:0.1s; }
.stat-card:nth-child(3) { animation-delay:0.15s; }
.stat-card:nth-child(4) { animation-delay:0.2s; }
.modal-overlay {
    position: fixed; inset:0; z-index:9999;
    background: rgba(0,0,0,0.45); backdrop-filter:blur(4px);
    display:flex; align-items:center; justify-content:center;
    opacity:0; visibility:hidden; transition:opacity 0.25s, visibility 0.25s;
}
.modal-overlay.open { opacity:1; visibility:visible; }
.modal-box {
    background:#fff; border-radius:16px; width:92%; max-width:780px;
    max-height:85vh; display:flex; flex-direction:column;
    box-shadow:0 25px 60px rgba(0,0,0,0.25);
    transform:scale(0.93) translateY(12px); transition:transform 0.3s cubic-bezier(0.16,1,0.3,1);
}
.modal-overlay.open .modal-box { transform:scale(1) translateY(0); }
.modal-head {
    display:flex; align-items:center; justify-content:space-between;
    padding:18px 24px; border-bottom:1px solid #f3f4f6; flex-shrink:0;
}
.modal-head h2 { font-size:0.95rem; font-weight:700; color:#111827; margin:0; }
.modal-close {
    width:32px; height:32px; border-radius:8px; border:1px solid #e5e7eb;
    background:#fff; color:#6b7280; display:flex; align-items:center; justify-content:center;
    cursor:pointer; transition:all 0.15s; font-size:16px; line-height:1;
}
.modal-close:hover { border-color:#9ca3af; color:#111827; background:#f9fafb; }
.modal-body { overflow-y:auto; padding:20px 24px; flex:1; }
.modal-body table { width:100%; border-collapse:collapse; font-size:0.8rem; }
.modal-body thead { position:sticky; top:0; z-index:1; }
.modal-body th {
    text-align:left; padding:10px 8px; font-size:0.55rem; font-weight:700;
    text-transform:uppercase; letter-spacing:0.08em; color:#6b7280;
    background:#f9fafb; border-bottom:1px solid #f3f4f6;
}
.modal-body th.text-right, .modal-body td.text-right { text-align:right; }
.modal-body td {
    padding:10px 8px; border-bottom:1px solid #f3f4f6;
    color:#374151; font-size:0.8rem; white-space:nowrap;
}
.modal-body tbody tr:hover { background:#f9fafb; }
</style>
@endpush

@section('content')
@php
$sumGross = collect($batchData)->sum('total_gross');
$sumDed   = collect($batchData)->sum('total_deductions');
$sumNet   = collect($batchData)->sum('total_net');
$periodLabel = $period === 'weekly' ? "Week $week" : ($period === 'monthly' ? date('F', mktime(0,0,0,$month,1)) : "Year $year");
@endphp

{{-- Header --}}
    <div class="flex items-start justify-between mb-6 flex-wrap gap-4 fade-up">
        <div class="flex items-center gap-2">
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Payroll Reports</h1>
            <p class="text-sm text-gray-500 mt-0.5">Approved payroll batches — gross, deductions, and net pay across periods.</p>
            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-gray-100 text-gray-500 text-[0.55rem] font-semibold mt-2">{{ count($batchData) }} {{ Str::plural('batch', count($batchData)) }} · {{ $periodLabel }}</span>
        </div>
        <a href="{{ route('reports.print.payroll-report', request()->query()) }}" target="_blank" rel="noopener"
           class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-gray-200 text-gray-700 text-xs font-bold rounded-xl hover:border-violet-300 hover:text-violet-600 hover:bg-violet-50 transition-all no-underline">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Print
        </a>
    </div>

    {{-- Filter bar --}}
    <div class="fade-up bg-white border border-gray-200 rounded-xl p-4 flex items-end gap-3 mb-5 flex-wrap">
        <div class="flex flex-col gap-1 min-w-[130px] flex-1">
            <label class="text-[0.6rem] font-bold uppercase tracking-wider text-gray-400">Period</label>
            <select id="period" class="border border-gray-200 rounded-lg px-2.5 py-2 text-xs text-gray-800 bg-gray-50 focus:outline-none focus:border-gray-400 focus:ring-2 focus:ring-gray-100 transition-all cursor-pointer">
                <option value="weekly" {{ $period === 'weekly' ? 'selected' : '' }}>Weekly</option>
                <option value="monthly" {{ $period === 'monthly' ? 'selected' : '' }}>Monthly</option>
                <option value="yearly" {{ $period === 'yearly' ? 'selected' : '' }}>Yearly</option>
            </select>
        </div>
        <div class="flex flex-col gap-1 min-w-[130px] flex-1" id="weekSelect" style="display:{{ $period === 'weekly' ? 'flex' : 'none' }}">
            <label class="text-[0.6rem] font-bold uppercase tracking-wider text-gray-400">Week</label>
            <select id="week" class="border border-gray-200 rounded-lg px-2.5 py-2 text-xs text-gray-800 bg-gray-50 focus:outline-none focus:border-gray-400 focus:ring-2 focus:ring-gray-100 transition-all cursor-pointer">
                @for ($i = 1; $i <= 52; $i++)
                    <option value="{{ $i }}" {{ (int) $week === $i ? 'selected' : '' }}>Week {{ $i }}</option>
                @endfor
            </select>
        </div>
        <div class="flex flex-col gap-1 min-w-[130px] flex-1" id="monthSelect" style="display:{{ $period === 'monthly' ? 'flex' : 'none' }}">
            <label class="text-[0.6rem] font-bold uppercase tracking-wider text-gray-400">Month</label>
            <select id="month" class="border border-gray-200 rounded-lg px-2.5 py-2 text-xs text-gray-800 bg-gray-50 focus:outline-none focus:border-gray-400 focus:ring-2 focus:ring-gray-100 transition-all cursor-pointer">
                @for ($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ (int) $month === $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                @endfor
            </select>
        </div>
        <div class="flex flex-col gap-1 min-w-[130px] flex-1">
            <label class="text-[0.6rem] font-bold uppercase tracking-wider text-gray-400">Year</label>
            <select id="year" class="border border-gray-200 rounded-lg px-2.5 py-2 text-xs text-gray-800 bg-gray-50 focus:outline-none focus:border-gray-400 focus:ring-2 focus:ring-gray-100 transition-all cursor-pointer">
                @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                    <option value="{{ $i }}" {{ (int) $year === $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <div class="scale-in stat-card bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm hover:shadow-md hover:border-violet-200 transition-all duration-300">
            <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Batches</p>
            <p class="text-xl font-bold text-violet-600 tabular-nums mt-1">{{ count($batchData) }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">In selected period</p>
        </div>
        <div class="scale-in stat-card bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm hover:shadow-md hover:border-emerald-200 transition-all duration-300">
            <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Total Gross</p>
            <p class="text-lg font-bold text-gray-900 tabular-nums mt-1">₱{{ number_format($sumGross, 2) }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Gross pay summed</p>
        </div>
        <div class="scale-in stat-card bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm hover:shadow-md hover:border-red-200 transition-all duration-300">
            <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Total Deductions</p>
            <p class="text-lg font-bold text-red-600 tabular-nums mt-1">₱{{ number_format($sumDed, 2) }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Deductions summed</p>
        </div>
        <div class="scale-in stat-card bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm hover:shadow-md hover:border-blue-200 transition-all duration-300">
            <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Total Net</p>
            <p class="text-lg font-bold text-emerald-600 tabular-nums mt-1">₱{{ number_format($sumNet, 2) }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Take-home total</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="fade-up bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-50 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-violet-500"></span>
            <span class="text-sm font-semibold text-gray-900">Batch summary</span>
            <span class="text-[0.55rem] text-gray-400">Payroll batches in range</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-50 bg-gray-50/50">
                        <th class="text-left px-5 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Period</th>
                        <th class="text-center px-4 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Employees</th>
                        <th class="text-right px-4 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Gross Pay</th>
                        <th class="text-right px-4 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Deductions</th>
                        <th class="text-right px-5 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Net Pay</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50" id="payrollGrid"></tbody>
            </table>
        </div>
        <div id="payrollNoResults" class="hidden">
            <div class="flex flex-col items-center py-12 text-center">
                <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p class="text-xs text-gray-400">No payroll batches for this filter.</p>
            </div>
        </div>
        <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100 bg-gray-50/50 flex-wrap gap-3">
            <div class="text-xs text-gray-400" id="payrollInfo">Showing <strong class="text-gray-700">0</strong> batches</div>
            <nav id="payrollNav" class="flex items-center gap-1"></nav>
        </div>
    </div>
</div>

{{-- Batch detail modal --}}
<div class="modal-overlay" id="batchModal">
    <div class="modal-box">
        <div class="modal-head">
            <h2 id="modalTitle">Batch Details</h2>
            <button class="modal-close" id="modalCloseBtn" type="button">&times;</button>
        </div>
        <div class="modal-body">
            <table>
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th class="text-right">Basic</th>
                        <th class="text-right">Allow.</th>
                        <th class="text-right">Bonus</th>
                        <th class="text-right">Holiday</th>
                        <th class="text-right">Gross</th>
                        <th class="text-right text-red-600">Ded.</th>
                        <th class="text-right">Net</th>
                    </tr>
                </thead>
                <tbody id="modalBody"></tbody>
            </table>
            <div id="modalEmpty" class="hidden text-center py-8 text-xs text-gray-400">No employee data available.</div>
        </div>
    </div>
</div>

<script>
window.payrollData = {!! json_encode(array_map(function($b) {
    return [
        'period'    => \Carbon\Carbon::parse($b['period_start'])->format('M d').' – '.\Carbon\Carbon::parse($b['period_end'])->format('M d, Y'),
        'count'     => $b['count'],
        'gross'     => (float) $b['total_gross'],
        'ded'       => (float) $b['total_deductions'],
        'net'       => (float) $b['total_net'],
        'employees' => $b['employees'] ?? [],
    ];
}, $batchData)) !!};
window.payrollTotals = { gross: {{ $sumGross }}, ded: {{ $sumDed }}, net: {{ $sumNet }} };

function playPop() {
    try {
        var ctx = new (window.AudioContext || window.webkitAudioContext)();
        var osc = ctx.createOscillator();
        var gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.frequency.setValueAtTime(800, ctx.currentTime);
        osc.frequency.exponentialRampToValueAtTime(400, ctx.currentTime + 0.08);
        gain.gain.setValueAtTime(0.25, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.12);
        osc.start(ctx.currentTime);
        osc.stop(ctx.currentTime + 0.12);
    } catch(e) {}
}

var batchModal = document.getElementById('batchModal');
var modalTitle = document.getElementById('modalTitle');
var modalBody  = document.getElementById('modalBody');
var modalEmpty = document.getElementById('modalEmpty');

document.getElementById('modalCloseBtn').addEventListener('click', function () { batchModal.classList.remove('open'); });
batchModal.addEventListener('click', function (e) { if (e.target === batchModal) batchModal.classList.remove('open'); });

document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && batchModal.classList.contains('open')) batchModal.classList.remove('open'); });

function showBatchModal(idx) {
    var b = window.payrollData[idx];
    if (!b) return;
    playPop();
    modalTitle.textContent = b.period + ' — Employee Breakdown';
    var emp = b.employees || [];
    modalBody.innerHTML = '';
    if (emp.length === 0) {
        modalEmpty.classList.remove('hidden');
    } else {
        modalEmpty.classList.add('hidden');
        emp.forEach(function (e) {
            var fmt = function(n) { return '₱' + n.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2}); };
            var tr = document.createElement('tr');
            tr.innerHTML =
                '<td style="font-weight:600;color:#111827;">' + e.name + '</td>' +
                '<td class="text-right">' + fmt(e.basic_salary) + '</td>' +
                '<td class="text-right">' + fmt(e.total_allowances) + '</td>' +
                '<td class="text-right">' + fmt(e.total_bonuses) + '</td>' +
                '<td class="text-right">' + fmt(e.holiday_pay) + '</td>' +
                '<td class="text-right" style="font-weight:600;">' + fmt(e.gross_pay) + '</td>' +
                '<td class="text-right" style="color:#dc2626;">' + fmt(e.total_deductions) + '</td>' +
                '<td class="text-right" style="font-weight:700;color:#16a34a;">' + fmt(e.net_pay) + '</td>';
            modalBody.appendChild(tr);
        });
    }
    batchModal.classList.add('open');
}

(function () {
    const periodSelect = document.getElementById('period');
    const weekSelectEl = document.getElementById('weekSelect');
    const monthSelectEl = document.getElementById('monthSelect');

    function toggleFilters() {
        const period = periodSelect.value;
        weekSelectEl.style.display = period === 'weekly' ? 'flex' : 'none';
        monthSelectEl.style.display = period === 'monthly' ? 'flex' : 'none';
    }

    function updateReport() {
        const period = periodSelect.value;
        const week = document.getElementById('week').value;
        const month = document.getElementById('month').value;
        const year = document.getElementById('year').value;
        let url = '{{ route('reports.payroll') }}?period=' + period + '&year=' + year;
        if (period === 'weekly') url += '&week=' + week;
        if (period === 'monthly') url += '&month=' + month;
        window.location.href = url;
    }

    periodSelect.addEventListener('change', function () {
        toggleFilters();
        updateReport();
    });
    document.getElementById('week').addEventListener('change', updateReport);
    document.getElementById('month').addEventListener('change', updateReport);
    document.getElementById('year').addEventListener('change', updateReport);

    var grid   = document.getElementById('payrollGrid');
    var noRes  = document.getElementById('payrollNoResults');
    var info   = document.getElementById('payrollInfo');
    var nav    = document.getElementById('payrollNav');
    var PER = 10, page = 1;
    var data = window.payrollData;

    function render() {
        var total = data.length, pages = Math.ceil(total / PER);
        var start = (page - 1) * PER, end = Math.min(start + PER, total);
        var pageData = data.slice(start, end);
        grid.innerHTML = '';

        if (pageData.length === 0) {
            noRes.classList.remove('hidden');
        } else {
            noRes.classList.add('hidden');
            pageData.forEach(function (b, idx) {
                var tr = document.createElement('tr');
                tr.className = 'transition-colors hover:bg-gray-50/50 cursor-pointer';
                tr.style.cursor = 'pointer';
                tr.dataset.batchIdx = start + idx;
                var fmt = function(n) { return n.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2}); };
                tr.innerHTML =
                    '<td class="px-5 py-3.5 text-xs font-semibold text-gray-900">' + b.period + '</td>' +
                    '<td class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 tabular-nums">' + b.count + '</td>' +
                    '<td class="px-4 py-3.5 text-right text-xs tabular-nums text-gray-900">\u20b1' + fmt(b.gross) + '</td>' +
                    '<td class="px-4 py-3.5 text-right text-xs tabular-nums text-red-600">\u20b1' + fmt(b.ded) + '</td>' +
                    '<td class="px-5 py-3.5 text-right text-xs font-bold tabular-nums text-emerald-600">\u20b1' + fmt(b.net) + '</td>';
                tr.addEventListener('click', function () {
                    showBatchModal(start + idx);
                });
                grid.appendChild(tr);
            });
            // totals row
            var tr = document.createElement('tr');
            tr.className = 'border-t border-gray-100 bg-gray-50/50';
            tr.innerHTML =
                '<td class="px-5 py-3.5 text-xs font-bold text-gray-900">Totals</td>' +
                '<td class="px-4 py-3.5 text-center text-xs font-bold text-gray-900 tabular-nums">' + total + '</td>' +
                '<td class="px-4 py-3.5 text-right text-xs font-bold text-gray-900 tabular-nums">\u20b1' + fmt(window.payrollTotals.gross) + '</td>' +
                '<td class="px-4 py-3.5 text-right text-xs font-bold text-amber-600 tabular-nums">\u20b1' + fmt(window.payrollTotals.ded) + '</td>' +
                '<td class="px-5 py-3.5 text-right text-xs font-bold text-emerald-700 tabular-nums">\u20b1' + fmt(window.payrollTotals.net) + '</td>';
            grid.appendChild(tr);
        }

        // pagination info & nav
        if (info) {
            if (total === 0) { info.innerHTML = 'No batches to display'; } else {
                info.innerHTML = 'Showing <strong class="text-gray-700">' + (start + 1) + '</strong>\u2013<strong class="text-gray-700">' + end + '</strong> of <strong class="text-gray-700">' + total + '</strong>';
            }
        }
        if (!nav) return;
        if (pages <= 1) { nav.innerHTML = ''; return; }

        var base = 'flex items-center justify-center w-7 h-7 rounded-lg text-xs font-semibold border transition-all duration-150';
        var actS = base + ' bg-gray-900 text-white border-gray-900';
        var defS = base + ' bg-white text-gray-600 border-gray-200 hover:bg-gray-50 hover:text-gray-900 hover:border-gray-300';
        var disS = base + ' bg-gray-50 text-gray-300 border-gray-100 cursor-not-allowed pointer-events-none';

        var html = '';
        html += '<button data-p="' + (page - 1) + '" class="' + (page === 1 ? disS : defS) + '">\u2039</button>';
        for (var i = 1; i <= pages; i++) {
            html += '<button data-p="' + i + '" class="' + (i === page ? actS : defS) + '">' + i + '</button>';
        }
        html += '<button data-p="' + (page + 1) + '" class="' + (page === pages ? disS : defS) + '">\u203a</button>';
        nav.innerHTML = html;
        Array.from(nav.querySelectorAll('button[data-p]')).forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                var p = parseInt(btn.dataset.p);
                if (p < 1 || p > pages) return;
                page = p;
                render();
            });
        });
    }

    render();
})();
</script>
@endsection
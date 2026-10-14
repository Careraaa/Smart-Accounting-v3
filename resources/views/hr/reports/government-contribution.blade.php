@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeSlideUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { 0%{opacity:0;transform:scale(0.92)} 100%{opacity:1;transform:scale(1)} }
.stat-card { animation:scaleIn 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.fade-up { animation:fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.filter-bar { animation:fadeSlideUp 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.table-wrap { animation:fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
</style>
@endpush

@section('content')
@php
    $empTotal = ($summary['employee_sss'] ?? 0) + ($summary['employee_pagibig'] ?? 0) + ($summary['employee_philhealth'] ?? 0);
    $erTotal  = ($summary['employer_sss'] ?? 0) + ($summary['employer_pagibig'] ?? 0) + ($summary['employer_philhealth'] ?? 0);
    $grandTotal = $empTotal + $erTotal;
    $dateFrom = request('date_from', now()->subMonths(3)->startOfMonth()->format('Y-m-d'));
    $dateTo   = request('date_to', now()->format('Y-m-d'));
    $printUrl = route('reports.government-contribution-print', [
        'date_from'   => $dateFrom,
        'date_to'     => $dateTo,
        'employee_id' => request('employee_id'),
    ]);
@endphp

<div class="space-y-5">

    {{-- Topbar --}}
    <div class="fade-up flex items-start justify-between gap-4 flex-wrap">
        <div>
            <h1 class="text-xl font-extrabold text-gray-900 tracking-tight">Government Contribution Summary</h1>
            <p class="text-xs text-gray-400 font-semibold mt-0.5 max-w-md">SSS, Pag-IBIG, and PhilHealth totals from payroll — employee and employer shares for the selected period.</p>
            <span class="inline-block mt-2 text-[0.6rem] font-semibold text-gray-500 bg-gray-100 rounded-md px-2 py-0.5">{{ \Carbon\Carbon::parse($dateFrom)->format('M j, Y') }} – {{ \Carbon\Carbon::parse($dateTo)->format('M j, Y') }}</span>
        </div>
        <a href="{{ $printUrl }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-900 text-white rounded-xl text-xs font-bold transition-all hover:bg-black active:scale-[0.97] no-underline">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Print report
        </a>
    </div>

    {{-- Filter bar --}}
    <form class="filter-bar flex items-center gap-3 flex-wrap bg-white rounded-xl border border-gray-100 shadow-sm px-4 py-3" method="get" action="{{ route('reports.government-contribution') }}" id="gc-report-filters">
        <div class="flex flex-col gap-1 min-w-[140px] flex-1">
            <label for="gc-date-from" class="text-[0.55rem] font-bold uppercase tracking-widest text-gray-400">Date from</label>
            <input type="date" id="gc-date-from" name="date_from" value="{{ $dateFrom }}" onchange="this.form.submit()" class="border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-900 bg-gray-50 outline-none transition-all focus:border-gray-400 focus:bg-white focus:ring-2 focus:ring-gray-100">
        </div>
        <div class="flex flex-col gap-1 min-w-[140px] flex-1">
            <label for="gc-date-to" class="text-[0.55rem] font-bold uppercase tracking-widest text-gray-400">Date to</label>
            <input type="date" id="gc-date-to" name="date_to" value="{{ $dateTo }}" onchange="this.form.submit()" class="border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-900 bg-gray-50 outline-none transition-all focus:border-gray-400 focus:bg-white focus:ring-2 focus:ring-gray-100">
        </div>
        <div class="flex flex-col gap-1 min-w-[160px] flex-1">
            <label for="gc-employee" class="text-[0.55rem] font-bold uppercase tracking-widest text-gray-400">Employee</label>
            <select id="gc-employee" name="employee_id" onchange="this.form.submit()" class="border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-900 bg-gray-50 outline-none transition-all focus:border-gray-400 focus:bg-white focus:ring-2 focus:ring-gray-100">
                <option value="">All employees</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                        {{ $employee->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    {{-- Stat cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="stat-card bg-white rounded-xl border border-gray-100 shadow-sm p-4 relative overflow-hidden" style="--accent:#0284c7">
            <div class="absolute bottom-0 left-0 right-0 h-[3px] rounded-b-xl" style="background:#0284c7"></div>
            <p class="text-[0.55rem] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Employee Share</p>
            <p class="font-mono text-lg font-extrabold text-gray-900 tabular-nums">₱{{ number_format($empTotal, 2) }}</p>
        </div>
        <div class="stat-card bg-white rounded-xl border border-gray-100 shadow-sm p-4 relative overflow-hidden" style="animation-delay:0.05s;--accent:#16a34a">
            <div class="absolute bottom-0 left-0 right-0 h-[3px] rounded-b-xl" style="background:#16a34a"></div>
            <p class="text-[0.55rem] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Employer Share</p>
            <p class="font-mono text-lg font-extrabold text-gray-900 tabular-nums">₱{{ number_format($erTotal, 2) }}</p>
        </div>
        <div class="stat-card bg-white rounded-xl border border-gray-100 shadow-sm p-4 relative overflow-hidden" style="animation-delay:0.1s;--accent:#c8292a">
            <div class="absolute bottom-0 left-0 right-0 h-[3px] rounded-b-xl" style="background:#c8292a"></div>
            <p class="text-[0.55rem] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Grand Total</p>
            <p class="font-mono text-lg font-extrabold text-gray-900 tabular-nums">₱{{ number_format($grandTotal, 2) }}</p>
        </div>
        <div class="stat-card bg-white rounded-xl border border-gray-100 shadow-sm p-4 relative overflow-hidden" style="animation-delay:0.15s;--accent:#6b7280">
            <div class="absolute bottom-0 left-0 right-0 h-[3px] rounded-b-xl" style="background:#6b7280"></div>
            <p class="text-[0.55rem] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Payroll Records</p>
            <p class="font-mono text-lg font-extrabold text-gray-900 tabular-nums" id="gcTotalRecords">{{ count($contributions) }}</p>
        </div>
    </div>

    {{-- Summary by type table (server-rendered, no pagination needed) --}}
    <div class="table-wrap bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-50 flex items-center gap-2.5">
            <span class="w-2 h-2 rounded-full bg-gray-900 shrink-0"></span>
            <h2 class="text-xs font-bold text-gray-900">Summary by contribution type</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 py-3 text-left text-[0.6rem] font-bold uppercase tracking-widest text-gray-500">Contribution type</th>
                        <th class="px-5 py-3 text-right text-[0.6rem] font-bold uppercase tracking-widest text-gray-500">Employee</th>
                        <th class="px-5 py-3 text-right text-[0.6rem] font-bold uppercase tracking-widest text-gray-500">Employer</th>
                        <th class="px-5 py-3 text-right text-[0.6rem] font-bold uppercase tracking-widest text-gray-500">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @php
                        $types = [
                            ['name' => 'SSS', 'emp' => $summary['employee_sss'] ?? 0, 'er' => $summary['employer_sss'] ?? 0],
                            ['name' => 'Pag-IBIG', 'emp' => $summary['employee_pagibig'] ?? 0, 'er' => $summary['employer_pagibig'] ?? 0],
                            ['name' => 'PhilHealth', 'emp' => $summary['employee_philhealth'] ?? 0, 'er' => $summary['employer_philhealth'] ?? 0],
                        ];
                    @endphp
                    @foreach($types as $t)
                    <tr class="transition-colors hover:bg-gray-50">
                        <td class="px-5 py-3 text-sm font-semibold text-gray-900">{{ $t['name'] }}</td>
                        <td class="px-5 py-3 text-right font-mono text-sm tabular-nums text-gray-700">₱{{ number_format($t['emp'], 2) }}</td>
                        <td class="px-5 py-3 text-right font-mono text-sm tabular-nums text-gray-400">₱{{ number_format($t['er'], 2) }}</td>
                        <td class="px-5 py-3 text-right font-mono text-sm tabular-nums text-green-700 font-semibold">₱{{ number_format($t['emp'] + $t['er'], 2) }}</td>
                    </tr>
                    @endforeach
                    <tr class="bg-gray-50 border-t-2 border-gray-100 font-bold">
                        <td class="px-5 py-3 text-sm font-bold text-gray-900">Total</td>
                        <td class="px-5 py-3 text-right font-mono text-sm tabular-nums font-bold text-gray-900">₱{{ number_format($empTotal, 2) }}</td>
                        <td class="px-5 py-3 text-right font-mono text-sm tabular-nums font-bold text-gray-900">₱{{ number_format($erTotal, 2) }}</td>
                        <td class="px-5 py-3 text-right font-mono text-sm tabular-nums font-bold text-green-700">₱{{ number_format($grandTotal, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Breakdown by employee --}}
    <div class="table-wrap bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden" style="animation-delay:0.1s">
        <div class="px-5 py-3.5 border-b border-gray-50 flex items-center gap-2.5">
            <span class="w-2 h-2 rounded-full bg-gray-900 shrink-0"></span>
            <h2 class="text-xs font-bold text-gray-900">Breakdown by employee</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 py-3 text-left text-[0.6rem] font-bold uppercase tracking-widest text-gray-500">Employee</th>
                        <th class="px-5 py-3 text-left text-[0.6rem] font-bold uppercase tracking-widest text-gray-500">Period</th>
                        <th class="px-5 py-3 text-right text-[0.6rem] font-bold uppercase tracking-widest text-gray-500">Emp. SSS</th>
                        <th class="px-5 py-3 text-right text-[0.6rem] font-bold uppercase tracking-widest text-gray-500">Er. SSS</th>
                        <th class="px-5 py-3 text-right text-[0.6rem] font-bold uppercase tracking-widest text-gray-500">Emp. Pag-IBIG</th>
                        <th class="px-5 py-3 text-right text-[0.6rem] font-bold uppercase tracking-widest text-gray-500">Er. Pag-IBIG</th>
                        <th class="px-5 py-3 text-right text-[0.6rem] font-bold uppercase tracking-widest text-gray-500">Emp. PhilHealth</th>
                        <th class="px-5 py-3 text-right text-[0.6rem] font-bold uppercase tracking-widest text-gray-500">Er. PhilHealth</th>
                    </tr>
                </thead>
                <tbody id="gcBody" class="divide-y divide-gray-50"></tbody>
            </table>
        </div>
        <div id="gcNoResults" class="hidden">
            <div class="flex flex-col items-center py-12 text-center">
                <p class="text-sm font-semibold text-gray-500">No contribution records found</p>
                <p class="text-xs text-gray-400 mt-0.5">No records for the selected period.</p>
            </div>
        </div>
        <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100 bg-gray-50/50 flex-wrap gap-3">
            <div class="text-xs text-gray-400" id="gcPaginationInfo">Showing <strong class="text-gray-700">0</strong> records</div>
            <nav id="gcPaginationNav" class="flex items-center gap-1"></nav>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.gcContributions = {!! json_encode(array_map(fn($c) => [
    'employee_name' => $c['employee_name'],
    'period_start' => \Carbon\Carbon::parse($c['period_start'])->format('M j'),
    'period_end' => \Carbon\Carbon::parse($c['period_end'])->format('M j, Y'),
    'employee_sss' => round($c['employee_sss'] ?? 0, 2),
    'employer_sss' => round($c['employer_sss'] ?? 0, 2),
    'employee_pagibig' => round($c['employee_pagibig'] ?? 0, 2),
    'employer_pagibig' => round($c['employer_pagibig'] ?? 0, 2),
    'employee_philhealth' => round($c['employee_philhealth'] ?? 0, 2),
    'employer_philhealth' => round($c['employer_philhealth'] ?? 0, 2),
], $contributions)) !!};

(function () {
    const body  = document.getElementById('gcBody');
    const noRes = document.getElementById('gcNoResults');
    const PER = 10;
    let page = 1;

    function render() {
        const total = window.gcContributions.length;
        const pages = Math.ceil(total / PER);
        const start = (page - 1) * PER;
        const end = Math.min(start + PER, total);
        const pageData = window.gcContributions.slice(start, end);
        body.innerHTML = '';

        if (pageData.length === 0) {
            noRes.classList.remove('hidden');
        } else {
            noRes.classList.add('hidden');
            pageData.forEach(c => {
                const tr = document.createElement('tr');
                tr.className = 'transition-colors hover:bg-gray-50';
                tr.innerHTML = `
                    <td class="px-5 py-3 text-sm font-semibold text-gray-900 whitespace-nowrap">${c.employee_name}</td>
                    <td class="px-5 py-3 text-xs text-gray-500 whitespace-nowrap">${c.period_start} – ${c.period_end}</td>
                    <td class="px-5 py-3 text-right font-mono text-xs tabular-nums text-gray-700">₱${c.employee_sss.toFixed(2)}</td>
                    <td class="px-5 py-3 text-right font-mono text-xs tabular-nums text-gray-400">₱${c.employer_sss.toFixed(2)}</td>
                    <td class="px-5 py-3 text-right font-mono text-xs tabular-nums text-gray-700">₱${c.employee_pagibig.toFixed(2)}</td>
                    <td class="px-5 py-3 text-right font-mono text-xs tabular-nums text-gray-400">₱${c.employer_pagibig.toFixed(2)}</td>
                    <td class="px-5 py-3 text-right font-mono text-xs tabular-nums text-gray-700">₱${c.employee_philhealth.toFixed(2)}</td>
                    <td class="px-5 py-3 text-right font-mono text-xs tabular-nums text-gray-400">₱${c.employer_philhealth.toFixed(2)}</td>
                `;
                body.appendChild(tr);
            });
        }
        updatePagination();
    }

    function updatePagination() {
        const total = window.gcContributions.length;
        const pages = Math.ceil(total / PER);
        const info  = document.getElementById('gcPaginationInfo');
        const nav   = document.getElementById('gcPaginationNav');
        if (!info || !nav) return;
        if (total === 0) { info.innerHTML = 'No records to display'; nav.innerHTML = ''; return; }
        const s = (page - 1) * PER + 1, e = Math.min(page * PER, total);
        info.innerHTML = `Showing <strong class="text-gray-700">${s}</strong>–<strong class="text-gray-700">${e}</strong> of <strong class="text-gray-700">${total}</strong>`;
        if (pages <= 1) { nav.innerHTML = ''; return; }

        const base = 'flex items-center justify-center w-7 h-7 rounded-lg text-xs font-semibold border transition-all duration-150';
        const act  = base + ' bg-gray-900 text-white border-gray-900';
        const def  = base + ' bg-white text-gray-600 border-gray-200 hover:bg-gray-50 hover:text-gray-900 hover:border-gray-300';
        const dis  = base + ' bg-gray-50 text-gray-300 border-gray-100 cursor-not-allowed pointer-events-none';

        let html = '';
        html += `<button data-p="${page - 1}" class="${page === 1 ? dis : def}">‹</button>`;
        const half = 2;
        let winStart = Math.max(1, page - half);
        let winEnd = Math.min(pages, winStart + 4);
        if (winEnd - winStart + 1 < 5) {
            winStart = Math.max(winEnd - 4, 1);
        }
        for (let i = winStart; i <= winEnd; i++) {
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
</script>
@endpush
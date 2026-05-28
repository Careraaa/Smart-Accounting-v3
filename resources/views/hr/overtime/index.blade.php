@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeSlideUp {
    0% { opacity: 0; transform: translateY(12px); }
    100% { opacity: 1; transform: translateY(0); }
}
@keyframes scaleIn {
    0% { opacity: 0; transform: scale(0.92); }
    100% { opacity: 1; transform: scale(1); }
}
@keyframes slideInRight {
    0% { opacity: 0; transform: translateX(-10px); }
    100% { opacity: 1; transform: translateX(0); }
}
.stat-card { animation: scaleIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) both; }
.stat-card:nth-child(1) { animation-delay: 0.05s; }
.stat-card:nth-child(2) { animation-delay: 0.1s; }
.stat-card:nth-child(3) { animation-delay: 0.15s; }
.fade-up { animation: fadeSlideUp 0.45s cubic-bezier(0.16, 1, 0.3, 1) both; }
.filter-bar { animation: slideInRight 0.4s cubic-bezier(0.16, 1, 0.3, 1) 0.1s both; }
.table-wrap { animation: fadeSlideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) 0.15s both; }
</style>
@endpush

@section('content')
<div class="max-w-full" data-ot>

    {{-- Flash --}}
    @foreach(['success','error'] as $t)
        @if(session($t))
        <div class="flex items-center gap-2.5 px-4 py-3 mb-5 rounded-xl text-sm font-medium border fade-up
            {{ $t==='success' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-red-50 border-red-200 text-red-700' }}">
            @if($t==='success')
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            @else
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
            @endif
            {{ session($t) }}
        </div>
        @endif
    @endforeach

    {{-- Topbar --}}
    <div class="flex items-start justify-between gap-4 mb-6 flex-wrap fade-up">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Overtime / Undertime</h1>
            <p class="text-sm text-gray-400 mt-0.5">
                Employee OT &amp; UT records —
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-sky-50 text-sky-700 border border-sky-200">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                    {{ now()->format('F Y') }}
                </span>
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('overtime.pending') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded-xl hover:bg-gray-800 hover:shadow-lg active:scale-[0.97] transition-all duration-200 no-underline">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 7v5l3 3"/></svg>
                Pending Requests
                @php $pendingOtCount = \App\Models\OvertimeUndertime::where('status','pending')->count(); @endphp
                @if($pendingOtCount > 0)
                    <span class="ml-1 inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-[11px] font-bold bg-white/20 text-white">{{ $pendingOtCount }}</span>
                @endif
            </a>
        </div>
    </div>

    {{-- Filter bar --}}
    <div class="filter-bar flex items-center gap-2.5 mb-4 flex-wrap">
        <div class="relative flex-1 min-w-[160px]">
            <input type="text" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-white text-gray-900 placeholder-gray-400 outline-none transition-all duration-200 focus:border-amber-400 focus:ring-2 focus:ring-amber-200/50 hover:border-gray-300" id="empSearch" placeholder="Search employee…">
        </div>
        <select class="px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl bg-white text-gray-700 outline-none transition-all duration-200 focus:border-amber-400 focus:ring-2 focus:ring-amber-200/50 hover:border-gray-300 cursor-pointer" id="deptFilter">
            <option value="">All Departments</option>
            @foreach($departments as $dept)
                <option value="{{ strtolower($dept) }}">{{ $dept }}</option>
            @endforeach
        </select>
    </div>

    {{-- Employee table --}}
    <div class="table-wrap bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition-shadow duration-300">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Employee</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Department</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">OT This Month</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">UT This Month</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Records</th>
                    </tr>
                </thead>
                <tbody id="empTbody">
                    @forelse($allEmployees as $emp)
                        @php
                            $summary  = $monthlySummary[$emp->id] ?? null;
                            $otHrs    = $summary ? (float) $summary->ot_hours : 0;
                            $utHrs    = $summary ? (float) $summary->ut_hours : 0;
                            $recCount = $summary ? (int)   $summary->total_records : 0;
                            $initials = strtoupper(substr($emp->first_name ?? 'U', 0, 1) . substr($emp->last_name ?? '', 0, 1));
                        @endphp
                        <tr class="border-b border-gray-100 cursor-pointer transition-colors duration-150 hover:bg-amber-50/30"
                            data-emp-id="{{ $emp->id }}"
                            data-name="{{ strtolower($emp->first_name . ' ' . $emp->last_name) }}"
                            data-dept="{{ strtolower($emp->department ?? '') }}"
                            onclick="window.location='{{ route('overtime.employee.calendar', $emp->id) }}'">
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center text-xs font-bold text-gray-500 uppercase shrink-0">{{ $initials }}</div>
                                    <div>
                                        <div class="font-semibold text-gray-900 text-sm">{{ $emp->first_name }} {{ $emp->last_name }}</div>
                                        <div class="text-xs text-gray-400 mt-0.5">{{ $emp->position ?? '—' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">{{ $emp->department ?? '—' }}</span></td>
                            <td class="text-center px-4 py-3.5">
                                @if($otHrs > 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold font-mono tabular-nums bg-sky-50 text-sky-700 border border-sky-200">
                                        <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                        {{ number_format($otHrs, 1) }} hrs
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-400 border border-gray-200">—</span>
                                @endif
                            </td>
                            <td class="text-center px-4 py-3.5">
                                @if($utHrs > 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold font-mono tabular-nums bg-amber-50 text-amber-700 border border-amber-200">
                                        <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
                                        {{ number_format($utHrs, 1) }} hrs
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-400 border border-gray-200">—</span>
                                @endif
                            </td>
                            <td class="text-center px-4 py-3.5 font-mono tabular-nums text-sm font-bold text-gray-700">
                                {{ $recCount > 0 ? $recCount : '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">
                            <div class="flex flex-col items-center justify-center py-16 text-center">
                                <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center text-gray-300 mb-4">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                                </div>
                                <p class="text-sm font-semibold text-gray-700">No employees found</p>
                                <p class="text-xs text-gray-400 mt-1">Add employees to start tracking overtime and undertime.</p>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div id="empNoResults" class="hidden">
            <div class="flex flex-col items-center justify-center py-10 text-center">
                <p class="text-sm font-semibold text-gray-700">No results</p>
                <p class="text-xs text-gray-400 mt-1">Try adjusting your filters.</p>
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="flex items-center justify-between px-5 py-3 mt-4 border-t border-gray-100 bg-gray-50/50 rounded-b-xl flex-wrap gap-3 table-wrap">
        <div class="text-xs text-gray-400" id="otPaginationInfo">
            Showing <strong class="text-gray-700">1</strong>–<strong class="text-gray-700">10</strong> of <strong class="text-gray-700">0</strong> employees
        </div>
        <nav id="otPaginationNav" class="flex items-center gap-1"></nav>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.allEmployeesData = {!! json_encode($allEmployees->map(fn($e) => [
    'id' => $e->id,
    'firstName' => $e->first_name,
    'lastName' => $e->last_name,
    'name' => strtolower($e->first_name . ' ' . $e->last_name),
    'dept' => strtolower($e->department ?? ''),
    'department' => $e->department,
    'position' => $e->position,
])) !!};

window.monthlySummary = {!! json_encode($monthlySummary->map(fn($s) => [
    'user_id' => $s->user_id,
    'ot_hours' => (float) $s->ot_hours,
    'ut_hours' => (float) $s->ut_hours,
    'total_records' => (int) $s->total_records,
])->keyBy('user_id')) !!};

(function () {
    const search  = document.getElementById('empSearch');
    const deptF   = document.getElementById('deptFilter');
    const tbody   = document.getElementById('empTbody');
    const noRes   = document.getElementById('empNoResults');
    const PER     = 10;
    let page = 1, filtered = [];

    function applyFilters() {
        const q = search.value.toLowerCase().trim();
        const d = deptF.value;
        filtered = window.allEmployeesData.filter(emp => (!q || emp.name.includes(q)) && (!d || emp.dept === d));
        page = 1;
        render();
    }

    function render() {
        const start = (page - 1) * PER;
        const end = start + PER;
        const pageData = filtered.slice(start, end);
        tbody.innerHTML = '';

        if (pageData.length === 0) {
            if (filtered.length === 0 && window.allEmployeesData.length > 0) {
                noRes.classList.remove('hidden');
            } else if (window.allEmployeesData.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6"><div class="flex flex-col items-center justify-center py-16 text-center"><div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center text-gray-300 mb-4"><svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg></div><p class="text-sm font-semibold text-gray-700">No employees found</p><p class="text-xs text-gray-400 mt-1">Add employees to start tracking overtime and undertime.</p></div></td></tr>';
            }
        } else {
            noRes.classList.add('hidden');
            const calRoute = '{{ route("overtime.employee.calendar", ["employee" => "__ID__"]) }}';
            pageData.forEach(emp => {
                const summary = window.monthlySummary[emp.id];
                const otHrs = summary ? summary.ot_hours : 0;
                const utHrs = summary ? summary.ut_hours : 0;
                const recCount = summary ? summary.total_records : 0;
                const initials = (emp.firstName.charAt(0) + emp.lastName.charAt(0)).toUpperCase();
                const url = calRoute.replace('__ID__', emp.id);
                const row = document.createElement('tr');
                row.className = 'border-b border-gray-100 cursor-pointer transition-colors duration-150 hover:bg-amber-50/30';
                row.dataset.name = emp.name;
                row.dataset.dept = emp.dept;
                row.onclick = () => window.location = url;

                let otPill = '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-400 border border-gray-200">—</span>';
                if (otHrs > 0) {
                    otPill = `<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold font-mono tabular-nums bg-sky-50 text-sky-700 border border-sky-200"><svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>${otHrs.toFixed(1)} hrs</span>`;
                }
                let utPill = '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-400 border border-gray-200">—</span>';
                if (utHrs > 0) {
                    utPill = `<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold font-mono tabular-nums bg-amber-50 text-amber-700 border border-amber-200"><svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>${utHrs.toFixed(1)} hrs</span>`;
                }

                row.innerHTML = `
                    <td class="px-4 py-3.5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center text-xs font-bold text-gray-500 uppercase shrink-0">${initials}</div>
                            <div>
                                <div class="font-semibold text-gray-900 text-sm">${emp.firstName} ${emp.lastName}</div>
                                <div class="text-xs text-gray-400 mt-0.5">${emp.position || '—'}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3.5"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">${emp.department || '—'}</span></td>
                    <td class="text-center px-4 py-3.5">${otPill}</td>
                    <td class="text-center px-4 py-3.5">${utPill}</td>
                    <td class="text-center px-4 py-3.5 font-mono tabular-nums text-sm font-bold text-gray-700">${recCount > 0 ? recCount : '—'}</td>
                `;
                tbody.appendChild(row);
            });
        }
        updatePagination();
    }

    function updatePagination() {
        const total = filtered.length;
        const pages = Math.ceil(total / PER);
        const info  = document.getElementById('otPaginationInfo');
        const nav   = document.getElementById('otPaginationNav');
        if (!info || !nav) return;
        if (total === 0) { info.innerHTML = 'No employees to display'; nav.innerHTML = ''; return; }
        const s = (page - 1) * PER + 1, e = Math.min(page * PER, total);
        info.innerHTML = `Showing <strong class="text-gray-700">${s}</strong>–<strong class="text-gray-700">${e}</strong> of <strong class="text-gray-700">${total}</strong> employee${total > 1 ? 's' : ''}`;
        if (pages <= 1) { nav.innerHTML = ''; return; }

        const base = `flex items-center justify-center w-7 h-7 rounded-lg text-xs font-semibold border transition-all duration-150`;
        const act  = `${base} bg-gray-900 text-white border-gray-900`;
        const def  = `${base} bg-white text-gray-600 border-gray-200 hover:bg-gray-50 hover:text-gray-900 hover:border-gray-300`;
        const dis  = `${base} bg-gray-50 text-gray-300 border-gray-100 cursor-not-allowed pointer-events-none`;

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

    search.addEventListener('input', applyFilters);
    deptF.addEventListener('change', applyFilters);
    applyFilters();
})();
</script>
@endpush

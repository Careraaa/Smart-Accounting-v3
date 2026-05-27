@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeSlideUp {
    0%  { opacity:0; transform:translateY(14px); }
    100%{ opacity:1; transform:translateY(0); }
}
@keyframes scaleIn {
    0%  { opacity:0; transform:scale(0.93); }
    100%{ opacity:1; transform:scale(1); }
}
@keyframes slideInLeft {
    0%  { opacity:0; transform:translateX(-10px); }
    100%{ opacity:1; transform:translateX(0); }
}
.em-page-in  { animation:fadeSlideUp 0.42s cubic-bezier(0.16,1,0.3,1) both; }
.em-stats-in { animation:scaleIn   0.38s cubic-bezier(0.16,1,0.3,1) both; }
.em-stats-in:nth-child(1){ animation-delay:.04s; }
.em-stats-in:nth-child(2){ animation-delay:.09s; }
.em-stats-in:nth-child(3){ animation-delay:.14s; }
.em-stats-in:nth-child(4){ animation-delay:.19s; }
.em-filter-in{ animation:slideInLeft 0.38s cubic-bezier(0.16,1,0.3,1) .1s both; }
.em-table-in { animation:fadeSlideUp 0.48s cubic-bezier(0.16,1,0.3,1) .15s both; }

/* avatar colour ring */
.em-avatar { background:linear-gradient(135deg,#f3f4f6,#e5e7eb); }

/* row hover glow */
.em-row-hover:hover { background:rgba(99,102,241,0.03); }

/* Neutralise global focus overrides */
[data-em] input:focus-visible,
[data-em] select:focus-visible,
[data-em] button:focus-visible { outline:none !important; }
</style>
@endpush

@section('content')
<div class="max-w-full" data-em>

    {{-- Flash --}}
    @foreach(['success','error','info'] as $ft)
        @if(session($ft))
        <div class="flex items-center gap-2.5 px-4 py-3 mb-5 rounded-xl text-sm font-medium border
            {{ $ft==='success' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : ($ft==='error' ? 'bg-red-50 border-red-200 text-red-700' : 'bg-blue-50 border-blue-200 text-blue-700') }}"
            style="animation:fadeSlideUp .35s ease both;">
            @if($ft==='success')
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            @else
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
            @endif
            {{ session($ft) }}
        </div>
        @endif
    @endforeach

    {{-- Page header --}}
    <div class="flex items-start justify-between mb-7 flex-wrap gap-4 em-page-in">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Employees</h1>
            <p class="text-sm text-gray-400 mt-0.5">Manage your workforce and employee records</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('hr.reports.print.employee-report') }}" target="_blank"
               class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:border-gray-300 hover:text-gray-900 hover:shadow-sm active:scale-[0.97] transition-all duration-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print
            </a>
            <a href="{{ route('employees.create') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded-xl hover:bg-gray-800 hover:shadow-lg hover:shadow-gray-900/20 active:scale-[0.97] transition-all duration-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Add Employee
            </a>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        {{-- Total --}}
        <div class="em-stats-in bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-start gap-3.5 hover:shadow-md hover:border-indigo-200 transition-all duration-300">
            <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 border border-indigo-200">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">Total</div>
                <div class="text-2xl font-bold text-gray-900 tabular-nums mt-0.5" id="statTotal">{{ $allEmployees->count() }}</div>
            </div>
        </div>
        {{-- Active --}}
        <div class="em-stats-in bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-start gap-3.5 hover:shadow-md hover:border-emerald-200 transition-all duration-300">
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-200">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">Active</div>
                <div class="text-2xl font-bold text-gray-900 tabular-nums mt-0.5" id="statActive">{{ $allEmployees->where('status','active')->count() }}</div>
            </div>
        </div>
        {{-- Inactive --}}
        <div class="em-stats-in bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-start gap-3.5 hover:shadow-md hover:border-gray-300 transition-all duration-300">
            <div class="w-10 h-10 rounded-lg bg-gray-100 text-gray-500 flex items-center justify-center shrink-0 border border-gray-200">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
            </div>
            <div>
                <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">Inactive</div>
                <div class="text-2xl font-bold text-gray-900 tabular-nums mt-0.5" id="statInactive">{{ $allEmployees->where('status','inactive')->count() }}</div>
            </div>
        </div>
        {{-- Departments --}}
        <div class="em-stats-in bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-start gap-3.5 hover:shadow-md hover:border-violet-200 transition-all duration-300">
            <div class="w-10 h-10 rounded-lg bg-violet-50 text-violet-600 flex items-center justify-center shrink-0 border border-violet-200">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <div>
                <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">Departments</div>
                <div class="text-2xl font-bold text-gray-900 tabular-nums mt-0.5">{{ count($departments) }}</div>
            </div>
        </div>
    </div>

    {{-- Filter bar --}}
    <div class="em-filter-in flex items-center gap-2.5 mb-4 flex-wrap">
        <div class="relative flex-1 min-w-[200px]">
            <input type="text" id="empSearch" placeholder="Search by name…"
                class="w-full pl-4 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-white text-gray-900 placeholder-gray-400 outline-none transition-all duration-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200/50 hover:border-gray-300">
        </div>
        <select id="empStatusFilter"
            class="px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl bg-white text-gray-700 outline-none transition-all duration-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200/50 hover:border-gray-300 cursor-pointer">
            <option value="">All Statuses</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
        <select id="empDeptFilter"
            class="px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl bg-white text-gray-700 outline-none transition-all duration-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200/50 hover:border-gray-300 cursor-pointer">
            <option value="">All Departments</option>
            @foreach($departments as $dept)
                <option value="{{ strtolower($dept) }}">{{ $dept }}</option>
            @endforeach
        </select>
    </div>

    {{-- Table --}}
    <div class="em-table-in bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition-shadow duration-300">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <a href="{{ route('employees.index', ['sort_by'=>'first_name','sort_order'=>($sortBy==='first_name'&&$sortOrder==='asc')?'desc':'asc']) }}"
                               class="inline-flex items-center gap-1 hover:text-indigo-600 transition-colors">
                                Employee
                                @if($sortBy==='first_name')
                                    @if($sortOrder==='asc')
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
                                    @else
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                    @endif
                                @else
                                    <svg class="w-3 h-3 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/></svg>
                                @endif
                            </a>
                        </th>
                        <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 hidden md:table-cell">Gender</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Department</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 hidden sm:table-cell">Contact</th>
                        <th class="text-center px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                    </tr>
                </thead>
                <tbody id="empTbody">
                    @forelse($employees as $employee)
                        @php
                            $initials = strtoupper(substr($employee->first_name??'U',0,1).substr($employee->last_name??'',0,1));
                            $avatarColors = ['indigo','violet','sky','teal','rose','amber','orange','pink'];
                            $colorIdx = crc32($employee->first_name??'') % count($avatarColors);
                            $ac = $avatarColors[$colorIdx];
                        @endphp
                        <tr class="em-row-hover border-b border-gray-100 cursor-pointer transition-colors duration-150"
                            data-name="{{ strtolower(($employee->first_name??'').' '.($employee->last_name??'')) }}"
                            data-status="{{ $employee->status }}"
                            data-dept="{{ strtolower($employee->department??'') }}"
                            data-href="{{ route('employees.show', $employee) }}"
                            onclick="if(!event.target.closest('a,button,form'))window.location=this.dataset.href">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold shrink-0 bg-{{ $ac }}-100 text-{{ $ac }}-700 border border-{{ $ac }}-200">{{ $initials }}</div>
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $employee->first_name }} {{ $employee->last_name }}</div>
                                        <div class="text-xs text-gray-400 mt-0.5">{{ $employee->position ?? '—' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-gray-500 text-sm hidden md:table-cell">{{ $employee->gender ? ucwords(str_replace('_',' ',$employee->gender)) : '—' }}</td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">{{ $employee->department ?? '—' }}</span>
                            </td>
                            <td class="px-5 py-3.5 font-mono text-sm text-gray-600 tabular-nums hidden sm:table-cell">{{ $employee->phone ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-center">
                                @if($employee->status === 'active')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-500 border border-gray-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400 inline-block"></span>Inactive
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="100">
                                <div class="flex flex-col items-center justify-center py-16 text-center">
                                    <div class="w-14 h-14 rounded-xl bg-gray-100 flex items-center justify-center text-gray-300 mb-4">
                                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-700">No employees found</p>
                                    <p class="text-xs text-gray-400 mt-1">Add your first employee to get started.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- No-filter-results --}}
        <div id="empNoResults" class="hidden">
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center text-gray-300 mb-3">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
                </div>
                <p class="text-sm font-semibold text-gray-700">No results found</p>
                <p class="text-xs text-gray-400 mt-1">Try a different search or filter.</p>
            </div>
        </div>

        {{-- Pagination strip --}}
        <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100 bg-gray-50/50 flex-wrap gap-3">
            <div class="text-xs text-gray-400" id="empPaginationInfo">Showing <strong class="text-gray-700">0</strong> employees</div>
            <nav id="empPaginationNav" class="flex items-center gap-1"></nav>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
window.allEmployeesData = {!! json_encode($allEmployees->map(fn($e) => [
    'id'         => $e->id,
    'firstName'  => $e->first_name,
    'lastName'   => $e->last_name,
    'name'       => strtolower(($e->first_name??'').' '.($e->last_name??'')),
    'gender'     => $e->gender,
    'position'   => $e->position,
    'department' => strtolower($e->department??''),
    'departmentDisplay' => $e->department,
    'phone'      => $e->phone,
    'status'     => $e->status,
])) !!};

(function(){
    const search  = document.getElementById('empSearch');
    const statusF = document.getElementById('empStatusFilter');
    const deptF   = document.getElementById('empDeptFilter');
    const tbody   = document.getElementById('empTbody');
    const noRes   = document.getElementById('empNoResults');
    const PER     = 10;
    let page = 1, filtered = [];

    const AVATAR_COLORS = ['indigo','violet','sky','teal','rose','amber','orange','pink'];
    function hashColor(name){ let h=0; for(let c of name) h=(h*31+c.charCodeAt(0))&0xffffffff; return AVATAR_COLORS[Math.abs(h)%AVATAR_COLORS.length]; }

    const STATUS_BADGE = {
        active:   `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>Active</span>`,
        inactive: `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-500 border border-gray-200"><span class="w-1.5 h-1.5 rounded-full bg-gray-400 inline-block"></span>Inactive</span>`,
    };

    function applyFilters(){
        const q  = search.value.toLowerCase().trim();
        const st = statusF.value;
        const dt = deptF.value;
        filtered = window.allEmployeesData.filter(e =>
            (!q  || e.name.includes(q)) &&
            (!st || e.status === st) &&
            (!dt || e.department === dt)
        );
        page = 1;
        render();
    }

    function render(){
        const start   = (page-1)*PER;
        const pageData= filtered.slice(start, start+PER);
        tbody.innerHTML = '';

        if(!pageData.length){
            if(filtered.length === 0 && window.allEmployeesData.length > 0){
                noRes.classList.remove('hidden');
            } else if(window.allEmployeesData.length === 0){
                tbody.innerHTML = `<tr><td colspan="100"><div class="flex flex-col items-center justify-center py-16 text-center"><div class="w-14 h-14 rounded-xl bg-gray-100 flex items-center justify-center text-gray-300 mb-4"><svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div><p class="text-sm font-semibold text-gray-700">No employees found</p><p class="text-xs text-gray-400 mt-1">Add your first employee to get started.</p></div></td></tr>`;
            }
        } else {
            noRes.classList.add('hidden');
            const empRoute = '{{ route("employees.show", ["employee"=>"__ID__"]) }}';
            pageData.forEach(emp => {
                const initials = ((emp.firstName||'').charAt(0)+(emp.lastName||'').charAt(0)).toUpperCase();
                const ac = hashColor(emp.name);
                const href = empRoute.replace('__ID__', emp.id);
                const dept = emp.departmentDisplay ? emp.departmentDisplay : '—';
                const gender = emp.gender ? emp.gender.replace('_',' ').replace(/\b\w/g,c=>c.toUpperCase()) : '—';
                const row = document.createElement('tr');
                row.className = 'em-row-hover border-b border-gray-100 cursor-pointer transition-colors duration-150';
                row.dataset.name   = emp.name;
                row.dataset.status = emp.status;
                row.dataset.dept   = emp.department;
                row.dataset.href   = href;
                row.onclick = e => { if(!e.target.closest('a,button,form')) window.location = href; };
                row.innerHTML = `
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold shrink-0 bg-${ac}-100 text-${ac}-700 border border-${ac}-200">${initials}</div>
                            <div>
                                <div class="font-semibold text-gray-900">${emp.firstName} ${emp.lastName}</div>
                                <div class="text-xs text-gray-400 mt-0.5">${emp.position||'—'}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 text-gray-500 text-sm">${gender}</td>
                    <td class="px-5 py-3.5"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">${dept}</span></td>
                    <td class="px-5 py-3.5 font-mono text-sm text-gray-600 tabular-nums">${emp.phone||'—'}</td>
                    <td class="px-5 py-3.5 text-center">${STATUS_BADGE[emp.status]||STATUS_BADGE.inactive}</td>
                `;
                tbody.appendChild(row);
            });
        }
        updatePagination();
    }

    function updatePagination(){
        const total = filtered.length;
        const pages = Math.ceil(total/PER);
        const info  = document.getElementById('empPaginationInfo');
        const nav   = document.getElementById('empPaginationNav');
        if(!info||!nav) return;
        if(total === 0){ info.innerHTML='No employees to display'; nav.innerHTML=''; return; }
        const s = (page-1)*PER+1, e = Math.min(page*PER, total);
        info.innerHTML = `Showing <strong class="text-gray-700">${s}</strong>–<strong class="text-gray-700">${e}</strong> of <strong class="text-gray-700">${total}</strong>`;
        if(pages<=1){ nav.innerHTML=''; return; }

        const btnClass = `flex items-center justify-center w-7 h-7 rounded-lg text-xs font-semibold border transition-all duration-150`;
        const activeClass = `${btnClass} bg-gray-900 text-white border-gray-900`;
        const defClass    = `${btnClass} bg-white text-gray-600 border-gray-200 hover:bg-gray-50 hover:text-gray-900 hover:border-gray-300`;
        const disClass    = `${btnClass} bg-gray-50 text-gray-300 border-gray-100 cursor-not-allowed pointer-events-none`;

        let html = '';
        html += `<button data-p="${page-1}" class="${page===1?disClass:defClass}">‹</button>`;
        for(let i=1;i<=pages;i++){
            html += `<button data-p="${i}" class="${i===page?activeClass:defClass}">${i}</button>`;
        }
        html += `<button data-p="${page+1}" class="${page===pages?disClass:defClass}">›</button>`;
        nav.innerHTML = html;
        nav.querySelectorAll('button[data-p]').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                const p = parseInt(btn.dataset.p);
                if(p<1||p>pages) return;
                page = p; render();
            });
        });
    }

    search.addEventListener('input', applyFilters);
    statusF.addEventListener('change', applyFilters);
    deptF.addEventListener('change', applyFilters);
    applyFilters();
})();
</script>
@endpush
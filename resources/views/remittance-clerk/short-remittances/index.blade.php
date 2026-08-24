@extends('layouts.layout')
@push('styles')
<style>
@keyframes fadeUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { 0%{opacity:0;transform:scale(0.93)} 100%{opacity:1;transform:scale(1)} }
.fade-up { animation:fadeUp 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.scale-in { animation:scaleIn 0.35s cubic-bezier(0.16,1,0.3,1) both; }
</style>
@endpush
@section('content')
<div class="flex items-start justify-between flex-wrap gap-4 mb-6 fade-up">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Short Remittances</h1>
        <p class="text-sm text-gray-500 mt-0.5">Track shortages and resolve liabilities for driver and PAO</p>
    </div>
</div>

@foreach(['success','error','info'] as $t)
    @if(session($t))
    <div class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-xs font-semibold mb-4
        @if($t==='success') bg-emerald-500/15 border border-emerald-500/25 text-emerald-700
        @elseif($t==='error') bg-red-500/15 border border-red-500/25 text-red-700
        @else bg-blue-500/15 border border-blue-500/25 text-blue-700 @endif">
        @if($t==='success')
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        @else
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
        @endif
        {{ session($t) }}
    </div>
    @endif
@endforeach

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6 fade-up">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 relative overflow-hidden">
        <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-red-500 rounded-b-xl"></div>
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <div class="text-[0.65rem] font-bold uppercase tracking-wider text-gray-400">Pending</div>
                <div class="text-lg font-extrabold text-gray-900 leading-tight">{{ $pendingRemittances->count() }}</div>
                <div class="text-[0.65rem] text-gray-400">pending</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 relative overflow-hidden">
        <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-amber-500 rounded-b-xl"></div>
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
            </div>
            <div>
                <div class="text-[0.65rem] font-bold uppercase tracking-wider text-gray-400">Total Short Amount</div>
                <div class="text-lg font-extrabold text-gray-900 leading-tight">₱{{ number_format($pendingRemittances->sum('short_amount') + $fullyPaidRemittances->sum('short_amount'), 0) }}</div>
                <div class="text-[0.65rem] text-gray-400">total shortage</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 relative overflow-hidden">
        <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-blue-500 rounded-b-xl"></div>
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div>
                <div class="text-[0.65rem] font-bold uppercase tracking-wider text-gray-400">Driver Shares</div>
                <div class="text-lg font-extrabold text-gray-900 leading-tight">₱{{ number_format($pendingRemittances->sum('driver_share') + $fullyPaidRemittances->sum('driver_share'), 0) }}</div>
                <div class="text-[0.65rem] text-gray-400">total driver liability</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 relative overflow-hidden">
        <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-emerald-500 rounded-b-xl"></div>
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <div class="text-[0.65rem] font-bold uppercase tracking-wider text-gray-400">PAO Shares</div>
                <div class="text-lg font-extrabold text-gray-900 leading-tight">₱{{ number_format($pendingRemittances->sum('pao_share') + $fullyPaidRemittances->sum('pao_share'), 0) }}</div>
                <div class="text-[0.65rem] text-gray-400">total PAO liability</div>
            </div>
        </div>
    </div>
</div>

{{-- Tab bar + search --}}
<div class="flex flex-wrap items-end justify-between gap-4 mb-5 fade-up">
    <nav class="border-b border-gray-200 flex-1" role="tablist">
        <button type="button" role="tab" data-tab="pending"
            class="tab-btn relative px-5 py-3 text-sm font-medium border-b-2 transition-all duration-200 inline-flex items-center gap-2 border-red-500 text-red-700 bg-red-50/60">
            <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            Pending
            <span class="tab-badge ml-0.5 inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-[11px] font-bold bg-red-100 text-red-800">{{ $pendingRemittances->count() }}</span>
        </button>
        <button type="button" role="tab" data-tab="resolved"
            class="tab-btn relative px-5 py-3 text-sm font-medium border-b-2 transition-all duration-200 inline-flex items-center gap-2 border-transparent text-gray-400 hover:text-gray-600 hover:border-gray-300 hover:bg-gray-50/50">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Resolved
            <span class="tab-badge ml-0.5 inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-[11px] font-bold bg-gray-200 text-gray-600">{{ $fullyPaidRemittances->count() }}</span>
        </button>
    </nav>
    <div class="relative shrink-0">
        <svg class="w-3.5 h-3.5 text-gray-400 absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/></svg>
        <input type="text" id="searchInput" placeholder="Search driver, PAO, vehicle..." class="pl-8 pr-3 py-2 w-56 text-xs border border-gray-300 rounded-lg outline-none transition-all focus:border-gray-900 focus:ring-1 focus:ring-gray-900/20 bg-white placeholder:text-gray-400">
    </div>
</div>

{{-- Table --}}
<div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-50 bg-gray-50/50">
                    <th class="text-left px-4 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Date</th>
                    <th class="text-left px-4 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Driver</th>
                    <th class="text-left px-4 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">PAO</th>
                    <th class="text-left px-4 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Vehicle</th>
                    <th class="text-right px-4 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Short Amount</th>
                    <th class="text-center px-4 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50" id="tableBody"></tbody>
        </table>
    </div>
    <div id="noResults" class="hidden">
        <div class="flex flex-col items-center py-12 text-center">
            <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-300 mb-3">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/></svg>
            </div>
            <p class="text-sm font-semibold text-gray-500">No remittances found</p>
            <p class="text-xs text-gray-400 mt-0.5">Try a different search or tab</p>
        </div>
    </div>
    <div class="flex items-center justify-between px-5 py-3 border-t border-gray-50 bg-gray-50/30">
        <p class="text-[0.65rem] text-gray-400" id="tableInfo">Showing 0–0 of 0</p>
        <div class="flex items-center gap-1" id="tableBtns"></div>
    </div>
</div>
@endsection

@php
    $jsAllData = array_merge(
        $pendingRemittances->map(fn($r) => [
            'id' => $r->id,
            'date' => $r->remittance_date?->format('M d, Y'),
            'dateSort' => $r->remittance_date?->format('Y-m-d'),
            'driver' => $r->driver->name ?? 'N/A',
            'pao' => $r->pao->name ?? 'N/A',
            'vehicle' => $r->vehicle->plate_number ?? '—',
            'short_amount' => (float) $r->short_amount,
            'show_url' => route('short-remittances.show', $r),
            'driver_status' => $r->driver_status,
            'pao_status' => $r->pao_status,
            '_type' => 'pending',
        ])->values()->toArray(),
        $fullyPaidRemittances->map(fn($r) => [
            'id' => $r->id,
            'date' => $r->remittance_date?->format('M d, Y'),
            'dateSort' => $r->remittance_date?->format('Y-m-d'),
            'driver' => $r->driver->name ?? 'N/A',
            'pao' => $r->pao->name ?? 'N/A',
            'vehicle' => $r->vehicle->plate_number ?? '—',
            'short_amount' => (float) $r->short_amount,
            'show_url' => route('short-remittances.show', $r),
            'driver_status' => $r->driver_status,
            'pao_status' => $r->pao_status,
            '_type' => 'resolved',
        ])->values()->toArray()
    );
@endphp
@push('scripts')
<script>
(function () {
    var allData = @json($jsAllData);
    var PER_PAGE = 10;
    var activeTab = 'pending';
    var page = 1;

    var tbody = document.getElementById('tableBody');
    var info = document.getElementById('tableInfo');
    var btns = document.getElementById('tableBtns');
    var noRes = document.getElementById('noResults');
    var search = document.getElementById('searchInput');

    function getFiltered() {
        var q = (search.value || '').toLowerCase();
        return allData.filter(function (r) {
            if (r._type !== activeTab) return false;
            if (!q) return true;
            return r.driver.toLowerCase().includes(q) ||
                   r.pao.toLowerCase().includes(q) ||
                   r.vehicle.toLowerCase().includes(q) ||
                   r.date.toLowerCase().includes(q);
        });
    }

    function statusHtml(r) {
        if (r._type === 'resolved') {
            return '<span class="inline-block text-[0.6rem] font-bold px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">Fully Paid</span>';
        }
        var partial = r.driver_status === 'partial' || r.pao_status === 'partial';
        var cls = partial ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-red-50 text-red-700 border border-red-200';
        var lbl = partial ? 'Partial' : 'Pending';
        return '<span class="inline-block text-[0.6rem] font-bold px-2.5 py-1 rounded-md ' + cls + '">' + lbl + '</span>';
    }

    function render() {
        var data = getFiltered();
        var total = data.length;
        var totalPages = Math.max(1, Math.ceil(total / PER_PAGE));
        if (page > totalPages) page = totalPages;
        var start = (page - 1) * PER_PAGE;
        var end = Math.min(start + PER_PAGE, total);
        var pageData = data.slice(start, end);

        if (pageData.length === 0) {
            tbody.innerHTML = '';
            noRes.classList.remove('hidden');
        } else {
            noRes.classList.add('hidden');
            var html = '';
            pageData.forEach(function (r) {
                html += '<tr class="transition-colors hover:bg-gray-50/50 cursor-pointer" onclick="window.location=\'' + r.show_url + '\'">' +
                    '<td class="px-4 py-3.5 text-xs text-gray-400 font-mono">' + r.date + '</td>' +
                    '<td class="px-4 py-3.5 text-xs text-gray-700 font-semibold">' + r.driver + '</td>' +
                    '<td class="px-4 py-3.5 text-xs text-gray-700 font-semibold">' + r.pao + '</td>' +
                    '<td class="px-4 py-3.5 text-xs text-gray-500 font-mono">' + r.vehicle + '</td>' +
                    '<td class="px-4 py-3.5 text-xs text-right font-mono font-bold text-red-600">₱' + Number(r.short_amount).toLocaleString('en-US', {minimumFractionDigits:2,maximumFractionDigits:2}) + '</td>' +
                    '<td class="px-4 py-3.5 text-center">' + statusHtml(r) + '</td></tr>';
            });
            tbody.innerHTML = html;
        }

        info.textContent = total > 0 ? 'Showing ' + (start + 1) + '–' + end + ' of ' + total : 'Showing 0–0 of 0';

        var bhtml = '';
        if (totalPages > 1) {
            var half = 2, ws = Math.max(1, page - half), we = Math.min(totalPages, ws + 4);
            if (we - ws + 1 < 5) ws = Math.max(we - 4, 1);
            for (var p = ws; p <= we; p++) {
                bhtml += '<button class="px-2.5 py-1 rounded-lg text-[0.6rem] font-bold border cursor-pointer ' +
                    (p === page ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-500 hover:bg-gray-100 border-gray-200') +
                    '" data-page="' + p + '">' + p + '</button>';
            }
        }
        btns.innerHTML = bhtml;
        btns.querySelectorAll('button').forEach(function (btn) {
            btn.addEventListener('click', function () { page = parseInt(this.dataset.page); render(); });
        });
    }

    var tabStyles = {
        pending:  { border: 'border-red-500', text: 'text-red-700', bg: 'bg-red-50/60', badge: { bg: 'bg-red-100', text: 'text-red-800' } },
        resolved: { border: 'border-emerald-500', text: 'text-emerald-700', bg: 'bg-emerald-50/60', badge: { bg: 'bg-emerald-100', text: 'text-emerald-800' } },
    };
    var inactBtn = ['border-transparent', 'text-gray-400', 'hover:text-gray-600', 'hover:border-gray-300', 'hover:bg-gray-50/50'];
    var inactBadge = ['bg-gray-200', 'text-gray-600'];

    document.querySelectorAll('.tab-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var tab = this.dataset.tab;
            if (tab === activeTab) return;

            document.querySelectorAll('.tab-btn').forEach(function (b) {
                b.classList.remove('border-red-500', 'text-red-700', 'bg-red-50/60', 'border-emerald-500', 'text-emerald-700', 'bg-emerald-50/60');
                b.classList.add.apply(b.classList, inactBtn);
                var badge = b.querySelector('.tab-badge');
                if (badge) {
                    badge.classList.remove('bg-red-100', 'text-red-800', 'bg-emerald-100', 'text-emerald-800');
                    badge.classList.add.apply(badge.classList, inactBadge);
                }
            });

            var s = tabStyles[tab];
            this.classList.remove.apply(this.classList, inactBtn);
            this.classList.add(s.border, s.text, s.bg);
            var badge = this.querySelector('.tab-badge');
            if (badge && s.badge) {
                badge.classList.remove.apply(badge.classList, inactBadge);
                badge.classList.add(s.badge.bg, s.badge.text);
            }

            activeTab = tab;
            page = 1;
            render();
        });
    });

    if (search) {
        search.addEventListener('input', function () { page = 1; render(); });
    }

    render();
})();
</script>
@endpush

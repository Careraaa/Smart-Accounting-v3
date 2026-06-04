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
{{-- Header --}}
    <div class="flex items-start justify-between flex-wrap gap-4 mb-6 fade-up">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Short Remittances</h1>
            <p class="text-sm text-gray-500 mt-0.5">Track shortages and resolve liabilities for driver and PAO</p>
        </div>
    </div>

    {{-- Flash messages --}}
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

    {{-- Stats --}}
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

    {{-- Pending Section --}}
    <div class="flex items-center gap-2 mb-2.5 fade-up">
        <span class="w-2 h-2 rounded-full bg-red-600 inline-block"></span>
        <span class="text-[0.7rem] font-bold uppercase tracking-widest text-gray-400">Pending Resolution</span>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden mb-6 fade-up">
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
                <tbody class="divide-y divide-gray-50" id="pendingTbody"></tbody>
            </table>
        </div>
        <div class="flex items-center justify-between px-5 py-3 border-t border-gray-50 bg-gray-50/30" id="pendingPagination">
            <p class="text-[0.65rem] text-gray-400" id="pendingInfo">Showing 0–0 of 0</p>
            <div class="flex items-center gap-1" id="pendingBtns"></div>
        </div>
    </div>

    {{-- Resolved Section --}}
    <div class="flex items-center gap-2 mb-2.5 fade-up">
        <span class="w-2 h-2 rounded-full bg-emerald-600 inline-block"></span>
        <span class="text-[0.7rem] font-bold uppercase tracking-widest text-gray-400">Fully Paid / Resolved</span>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden fade-up">
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
                <tbody class="divide-y divide-gray-50" id="resolvedTbody"></tbody>
            </table>
        </div>
        <div class="flex items-center justify-between px-5 py-3 border-t border-gray-50 bg-gray-50/30" id="resolvedPagination">
            <p class="text-[0.65rem] text-gray-400" id="resolvedInfo">Showing 0–0 of 0</p>
            <div class="flex items-center gap-1" id="resolvedBtns"></div>
        </div>
    </div>
@endsection

@php
    $jsPendingData = $pendingRemittances->map(fn($r) => [
        'id' => $r->id,
        'date' => $r->remittance_date?->format('M d, Y'),
        'driver' => $r->driver->name ?? 'N/A',
        'pao' => $r->pao->name ?? 'N/A',
        'vehicle' => $r->vehicle->plate_number ?? '—',
        'short_amount' => (float) $r->short_amount,
        'show_url' => route('short-remittances.show', $r),
        'driver_status' => $r->driver_status,
        'pao_status' => $r->pao_status,
    ]);
    $jsResolvedData = $fullyPaidRemittances->map(fn($r) => [
        'id' => $r->id,
        'date' => $r->remittance_date?->format('M d, Y'),
        'driver' => $r->driver->name ?? 'N/A',
        'pao' => $r->pao->name ?? 'N/A',
        'vehicle' => $r->vehicle->plate_number ?? '—',
        'short_amount' => (float) $r->short_amount,
        'show_url' => route('short-remittances.show', $r),
    ]);
@endphp
@push('scripts')
<script>
(function () {
    var pendingData = @json($jsPendingData);
    var resolvedData = @json($jsResolvedData);

    var PER_PAGE = 10;

    function renderSection(data, tbodyId, infoId, btnsId, statusFn) {
        var tbody = document.getElementById(tbodyId);
        var info = document.getElementById(infoId);
        var btns = document.getElementById(btnsId);
        var total = data.length;
        var totalPages = Math.max(1, Math.ceil(total / PER_PAGE));
        var page = 1;

        function render() {
            if (page > totalPages) page = totalPages;
            var start = (page - 1) * PER_PAGE;
            var end = Math.min(start + PER_PAGE, total);
            var pageData = data.slice(start, end);

            if (pageData.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center py-12"><div class="flex flex-col items-center gap-2">' +
                    '<svg class="w-6 h-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' +
                    '<p class="text-xs text-gray-400">No records</p></div></td></tr>';
            } else {
                var html = '';
                pageData.forEach(function (r) {
                    var statusHtml = statusFn ? statusFn(r) : '<span class="inline-block text-[0.6rem] font-bold px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">Fully Paid</span>';
                    html += '<tr class="transition-colors hover:bg-gray-50/50 cursor-pointer" onclick="window.location=\'' + r.show_url + '\'">' +
                        '<td class="px-4 py-3.5 text-xs text-gray-400 font-mono">' + r.date + '</td>' +
                        '<td class="px-4 py-3.5 text-xs text-gray-700 font-semibold">' + r.driver + '</td>' +
                        '<td class="px-4 py-3.5 text-xs text-gray-700 font-semibold">' + r.pao + '</td>' +
                        '<td class="px-4 py-3.5 text-xs text-gray-500 font-mono">' + r.vehicle + '</td>' +
                        '<td class="px-4 py-3.5 text-xs text-right font-mono font-bold text-red-600">₱' + Number(r.short_amount).toLocaleString('en-PH', {minimumFractionDigits:2,maximumFractionDigits:2}) + '</td>' +
                        '<td class="px-4 py-3.5 text-center">' + statusHtml + '</td></tr>';
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

        render();
    }

    function fmtStatus(r) {
        var partial = r.driver_status === 'partial' || r.pao_status === 'partial';
        var cls = partial ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-red-50 text-red-700 border border-red-200';
        var lbl = partial ? 'Partial' : 'Pending';
        return '<span class="inline-block text-[0.6rem] font-bold px-2.5 py-1 rounded-md ' + cls + '">' + lbl + '</span>';
    }

    renderSection(pendingData, 'pendingTbody', 'pendingInfo', 'pendingBtns', fmtStatus);
    renderSection(resolvedData, 'resolvedTbody', 'resolvedInfo', 'resolvedBtns', null);
})();
</script>
@endpush

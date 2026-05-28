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
                    <div class="text-[0.65rem] text-gray-400">pending resolution</div>
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
                    <div class="text-lg font-extrabold text-gray-900 leading-tight">₱{{ number_format($pendingRemittances->sum('short_amount'), 0) }}</div>
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
                    <div class="text-lg font-extrabold text-gray-900 leading-tight">₱{{ number_format($pendingRemittances->sum('driver_share'), 0) }}</div>
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
                    <div class="text-lg font-extrabold text-gray-900 leading-tight">₱{{ number_format($pendingRemittances->sum('pao_share'), 0) }}</div>
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
                        <th class="text-left px-4 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">
                            <a href="{{ route('short-remittances.index', ['sort_by' => 'remittance_date', 'sort_order' => ($sortBy === 'remittance_date' && $sortOrder === 'asc') ? 'desc' : 'asc']) }}" class="flex items-center gap-1 text-gray-400 no-underline hover:text-gray-600">
                                Date
                                @if($sortBy === 'remittance_date')
                                <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $sortOrder === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>
                                @endif
                            </a>
                        </th>
                        <th class="text-left px-4 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Driver</th>
                        <th class="text-left px-4 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">PAO</th>
                        <th class="text-left px-4 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Vehicle</th>
                        <th class="text-right px-4 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Short Amount</th>
                        <th class="text-center px-4 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                @forelse($pendingRemittances as $shortRemittance)
                    @php
                        $driverPartial = $shortRemittance->driver_status === 'partial';
                        $paoPartial    = $shortRemittance->pao_status === 'partial';
                        $sc = ($driverPartial || $paoPartial) ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-red-50 text-red-700 border border-red-200';
                        $label = ($driverPartial || $paoPartial) ? 'Partial' : 'Pending';
                    @endphp
                    <tr class="transition-colors hover:bg-gray-50/50 cursor-pointer" onclick="window.location='{{ route('short-remittances.show', $shortRemittance) }}'">
                        <td class="px-4 py-3.5 text-xs text-gray-400 font-mono">{{ $shortRemittance->remittance_date?->format('M d, Y') }}</td>
                        <td class="px-4 py-3.5 text-xs text-gray-700 font-semibold">{{ $shortRemittance->driver->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3.5 text-xs text-gray-700 font-semibold">{{ $shortRemittance->pao->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3.5 text-xs text-gray-500 font-mono">{{ $shortRemittance->vehicle->plate_number }}</td>
                        <td class="px-4 py-3.5 text-xs text-right font-mono font-bold text-red-600">₱{{ number_format($shortRemittance->short_amount, 2) }}</td>
                        <td class="px-4 py-3.5 text-center">
                            <span class="inline-block text-[0.6rem] font-bold px-2.5 py-1 rounded-md {{ $sc }}">{{ $label }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-12">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-6 h-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p class="text-xs text-gray-400">No pending short remittances</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
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
                <tbody class="divide-y divide-gray-50">
                @forelse($fullyPaidRemittances as $shortRemittance)
                    <tr class="transition-colors hover:bg-gray-50/50 cursor-pointer" onclick="window.location='{{ route('short-remittances.show', $shortRemittance) }}'">
                        <td class="px-4 py-3.5 text-xs text-gray-400 font-mono">{{ $shortRemittance->remittance_date?->format('M d, Y') }}</td>
                        <td class="px-4 py-3.5 text-xs text-gray-700 font-semibold">{{ $shortRemittance->driver->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3.5 text-xs text-gray-700 font-semibold">{{ $shortRemittance->pao->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3.5 text-xs text-gray-500 font-mono">{{ $shortRemittance->vehicle->plate_number }}</td>
                        <td class="px-4 py-3.5 text-xs text-right font-mono font-bold text-emerald-600">₱{{ number_format($shortRemittance->short_amount, 2) }}</td>
                        <td class="px-4 py-3.5 text-center">
                            <span class="inline-block text-[0.6rem] font-bold px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">Fully Paid</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-12">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-6 h-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <p class="text-xs text-gray-400">No fully paid remittances</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>@endsection

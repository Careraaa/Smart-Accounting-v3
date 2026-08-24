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
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-8 space-y-5">

    @php
        $rstatus = strtolower($route->status ?? 'active');
    @endphp

    <div class="flex items-start justify-between flex-wrap gap-4 fade-up">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-gray-50 text-gray-400 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">{{ $route->route_name }}</h1>

                <p class="text-sm text-gray-500 mt-0.5">{{ $route->origin }} → {{ $route->destination }}</p>
            </div>
        </div>
        <a href="{{ route('routes.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl text-xs font-semibold no-underline hover:border-gray-600 hover:text-gray-600 hover:bg-gray-50 transition-colors whitespace-nowrap cursor-pointer">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Routes
        </a>
    </div>

    <div class="flex flex-wrap gap-3 fade-up">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-4 py-3 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-gray-50 text-gray-500 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Boundary</p>
                <p class="text-sm font-semibold text-gray-900">@if($route->boundary) ₱{{ number_format($route->boundary, 2) }} @else — @endif</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-4 py-3 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-gray-50 text-gray-500 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Total Vehicles</p>
                <p class="text-sm font-semibold text-gray-900">{{ $route->vehicles->count() }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-4 py-3 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Active Vehicles</p>
                <p class="text-sm font-semibold text-gray-900">{{ $route->vehicles->where('status', 'active')->count() }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden fade-up">
        <div class="px-5 py-4 border-b border-gray-50 flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
            </div>
            <span class="text-xs font-bold uppercase tracking-wider text-gray-600">Route Information</span>
        </div>
        <div class="px-5 py-2">
            <div class="flex items-center justify-between py-3 border-b border-gray-50">
                <span class="text-xs font-medium text-gray-400">Route Name</span>
                <span class="text-xs font-semibold text-gray-900">{{ $route->route_name ?? '—' }}</span>
            </div>
            <div class="flex items-center justify-between py-3 border-b border-gray-50">
                <span class="text-xs font-medium text-gray-400">Origin</span>
                <span class="text-xs font-semibold text-gray-900">{{ $route->origin ?? '—' }}</span>
            </div>
            <div class="flex items-center justify-between py-3 border-b border-gray-50">
                <span class="text-xs font-medium text-gray-400">Destination</span>
                <span class="text-xs font-semibold text-gray-900">{{ $route->destination ?? '—' }}</span>
            </div>
            <div class="flex items-center justify-between py-3 border-b border-gray-50">
                <span class="text-xs font-medium text-gray-400">Boundary</span>
                <span class="text-xs font-semibold text-gray-900">@if($route->boundary) ₱{{ number_format($route->boundary, 2) }} @else — @endif</span>
            </div>
            <div class="flex items-center gap-2 pt-4 pb-2 mt-2">
                <a href="{{ route('routes.edit', $route) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-gray-900 text-white hover:bg-gray-800 transition-all no-underline">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit Route
                </a>
                <button type="button" onclick="openRouteDeleteModal()" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-red-50 text-red-600 hover:bg-red-100 transition-all border-0 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Delete
                </button>
                <form id="deleteRouteForm" action="{{ route('routes.destroy', $route) }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
            </div>
        </div>
    </div>

    @if($route->vehicles->count() > 0)
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden fade-up">
        <div class="px-5 py-4 border-b border-gray-50 flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-lg bg-gray-50 text-gray-500 flex items-center justify-center shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
            </div>
            <span class="text-xs font-bold uppercase tracking-wider text-gray-600">Assigned Vehicles</span>
            <span class="text-[10px] font-semibold text-gray-400 ml-auto">{{ $route->vehicles->count() }} vehicle{{ $route->vehicles->count() !== 1 ? 's' : '' }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-50 bg-gray-50/50">
                        <th class="text-left px-5 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Plate Number</th>
                        <th class="text-left px-5 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Operator</th>
                        <th class="text-center px-5 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($route->vehicles as $vehicle)
                        @php
                            $vvstatus = strtolower($vehicle->status ?? 'active');
                            $vvlabel = match($vvstatus) { 'under_maintenance' => 'Under Maintenance', default => ucfirst($vvstatus) };
                            $vvbadge = match($vvstatus) { 'active' => 'bg-emerald-50 text-emerald-600', 'under_maintenance' => 'bg-amber-50 text-amber-600', default => 'bg-gray-50 text-gray-500' };
                        @endphp
                        <tr class="transition-colors hover:bg-gray-50/50 cursor-pointer" onclick="window.location='{{ route('vehicles.show', $vehicle) }}'">
                            <td class="px-5 py-3.5 text-xs font-semibold text-gray-700 tracking-wide">{{ $vehicle->plate_number }}</td>
                            <td class="px-5 py-3.5 text-xs text-gray-600">{{ $vehicle->operator }}</td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[10px] font-bold {{ $vvbadge }}">{{ $vvlabel }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
{{-- Delete Confirmation Modal --}}
<div id="routeDeleteModal" class="fixed inset-0 z-[100] flex items-center justify-center hidden">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeRouteDeleteModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl border border-gray-100 w-full max-w-sm mx-4 p-6 transform transition-all duration-200 scale-95" id="routeDeleteModalContent">
        <div class="flex flex-col items-center text-center">
            <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mb-4">
                <svg class="w-7 h-7 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">Delete Route</h3>
            <p class="text-sm text-gray-500 mb-6">Delete <strong class="text-gray-700">&quot;{{ $route->route_name }}&quot;</strong>? This cannot be undone.</p>
            <div class="flex items-center gap-3 w-full">
                <button type="button" onclick="closeRouteDeleteModal()" class="flex-1 px-4 py-2.5 rounded-xl text-sm font-semibold bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all border-0 cursor-pointer">Cancel</button>
                <button type="button" onclick="confirmRouteDelete()" class="flex-1 px-4 py-2.5 rounded-xl text-sm font-semibold bg-red-500 text-white hover:bg-red-600 shadow-lg shadow-red-500/25 transition-all border-0 cursor-pointer">Yes, delete</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openRouteDeleteModal() {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.frequency.value = 600;
        osc.type = 'sine';
        gain.gain.setValueAtTime(0.3, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.15);
        osc.start(ctx.currentTime);
        osc.stop(ctx.currentTime + 0.15);
        document.getElementById('routeDeleteModal').classList.remove('hidden');
        document.getElementById('routeDeleteModalContent').classList.remove('scale-95');
        document.getElementById('routeDeleteModalContent').classList.add('scale-100');
    }
    function closeRouteDeleteModal() {
        document.getElementById('routeDeleteModalContent').classList.remove('scale-100');
        document.getElementById('routeDeleteModalContent').classList.add('scale-95');
        setTimeout(() => {
            document.getElementById('routeDeleteModal').classList.add('hidden');
        }, 150);
    }
    function confirmRouteDelete() {
        document.getElementById('deleteRouteForm').submit();
    }
    document.getElementById('routeDeleteModal').addEventListener('click', function(e) {
        if (e.target === this) closeRouteDeleteModal();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeRouteDeleteModal();
    });
</script>
@endpush
@endsection

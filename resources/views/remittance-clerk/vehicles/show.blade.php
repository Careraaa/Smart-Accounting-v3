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
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-8 space-y-6">

    @php
        $vstatus = strtolower($vehicle->status ?? 'active');
        $vlabel = match($vstatus) { 'under_maintenance' => 'Under Maintenance', default => ucfirst($vstatus) };
    @endphp

    <div class="flex items-center justify-between gap-3 fade-up">
        <a href="{{ route('vehicles.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl text-xs font-semibold no-underline hover:border-gray-600 hover:text-gray-600 hover:bg-gray-50 transition-colors whitespace-nowrap cursor-pointer">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Vehicles
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('vehicles.edit', $vehicle) }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold bg-gray-900 text-white hover:bg-gray-800 transition-all no-underline whitespace-nowrap">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit Vehicle
            </a>
            <button type="button" onclick="openVehicleDeleteModal()" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold bg-red-50 text-red-600 hover:bg-red-100 transition-all border-0 cursor-pointer whitespace-nowrap">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Delete
            </button>
            <form id="deleteVehicleForm" action="{{ route('vehicles.destroy', $vehicle) }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
        </div>
    </div>

    <div class="hidden">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-4 py-3 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-gray-50 text-gray-500 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Operator</p>
                <p class="text-sm font-semibold text-gray-900">{{ $vehicle->operator ?? '—' }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-4 py-3 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-gray-50 text-gray-500 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Boundary Rates</p>
                <p class="text-xs font-semibold text-gray-900">Weekdays: ₱{{ number_format($vehicle->route->weekday_boundary ?? 0, 2) }}</p>
                <p class="text-xs font-semibold text-gray-900">Weekends: ₱{{ number_format($vehicle->route->weekend_boundary ?? 0, 2) }}</p>
            </div>
        </div>
    </div>

    <section class="fade-up bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="flex items-center gap-3 px-5 py-3 border-b border-gray-100">
            <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 17h14l1-5-2-5H6l-2 5 1 5zm0 0v2m14-2v2M7 12h.01M17 12h.01"/></svg>
            </div>
            <h2 class="text-xs font-bold text-gray-900">Vehicle Information</h2>
        </div>
        <div class="px-5 divide-y divide-gray-100">
            <div class="flex items-center justify-between py-3">
                <span class="text-xs font-medium text-gray-600">Plate Number</span>
                <span class="text-xs font-semibold text-gray-900">{{ $vehicle->plate_number ?? '—' }}</span>
            </div>
            <div class="flex items-center justify-between py-3">
                <span class="text-xs font-medium text-gray-600">Operator</span>
                <span class="text-xs font-semibold text-gray-900">{{ $vehicle->operator ?? '—' }}</span>
            </div>
            <div class="flex items-center justify-between py-3">
                <span class="text-xs font-medium text-gray-600">Status</span>
                <span class="text-xs font-semibold"><span class="app-status-badge {{ $vstatus === 'active' ? 'active' : (in_array($vstatus, ['inactive', 'under_maintenance'], true) ? 'inactive' : 'neutral') }}">{{ $vlabel }}</span></span>
            </div>
            <div class="flex items-center justify-between py-3">
                <span class="text-xs font-medium text-gray-600">Origin</span>
                <span class="text-xs font-semibold text-gray-900">{{ $vehicle->route->origin ?? '—' }}</span>
            </div>
            <div class="flex items-center justify-between py-3">
                <span class="text-xs font-medium text-gray-600">Destination</span>
                <span class="text-xs font-semibold text-gray-900">{{ $vehicle->route->destination ?? '—' }}</span>
            </div>
            <div class="flex items-center justify-between py-3">
                <span class="text-xs font-medium text-gray-600">Weekday Boundary</span>
                <span class="text-xs font-semibold text-gray-900">₱{{ number_format($vehicle->route->weekday_boundary ?? 0, 2) }}</span>
            </div>
            <div class="flex items-center justify-between py-3">
                <span class="text-xs font-medium text-gray-600">Weekend Boundary</span>
                <span class="text-xs font-semibold text-gray-900">₱{{ number_format($vehicle->route->weekend_boundary ?? 0, 2) }}</span>
            </div>
        </div>
    </section>
</div>
{{-- Delete Confirmation Modal --}}
<div id="vehicleDeleteModal" class="fixed inset-0 z-[100] flex items-center justify-center hidden">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeVehicleDeleteModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl border border-gray-100 w-full max-w-sm mx-4 p-6 transform transition-all duration-200 scale-95" id="vehicleDeleteModalContent">
        <div class="flex flex-col items-center text-center">
            <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mb-4">
                <svg class="w-7 h-7 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">Delete Vehicle</h3>
            <p class="text-sm text-gray-500 mb-6">Delete <strong class="text-gray-700">&quot;{{ $vehicle->plate_number }}&quot;</strong>? This cannot be undone.</p>
            <div class="flex items-center gap-3 w-full">
                <button type="button" onclick="closeVehicleDeleteModal()" class="flex-1 px-4 py-2.5 rounded-xl text-sm font-semibold bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all border-0 cursor-pointer">Cancel</button>
                <button type="button" onclick="confirmVehicleDelete()" class="flex-1 px-4 py-2.5 rounded-xl text-sm font-semibold bg-red-500 text-white hover:bg-red-600 shadow-lg shadow-red-500/25 transition-all border-0 cursor-pointer">Yes, delete</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openVehicleDeleteModal() {
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
        document.getElementById('vehicleDeleteModal').classList.remove('hidden');
        document.getElementById('vehicleDeleteModalContent').classList.remove('scale-95');
        document.getElementById('vehicleDeleteModalContent').classList.add('scale-100');
    }
    function closeVehicleDeleteModal() {
        document.getElementById('vehicleDeleteModalContent').classList.remove('scale-100');
        document.getElementById('vehicleDeleteModalContent').classList.add('scale-95');
        setTimeout(() => {
            document.getElementById('vehicleDeleteModal').classList.add('hidden');
        }, 150);
    }
    function confirmVehicleDelete() {
        document.getElementById('deleteVehicleForm').submit();
    }
    document.getElementById('vehicleDeleteModal').addEventListener('click', function(e) {
        if (e.target === this) closeVehicleDeleteModal();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeVehicleDeleteModal();
    });
</script>
@endpush
@endsection

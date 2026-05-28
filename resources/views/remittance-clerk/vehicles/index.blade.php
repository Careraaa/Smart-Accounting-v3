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
<div class="flex items-start justify-between flex-wrap gap-4 mb-5 fade-up">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Vehicles</h1>
            <p class="text-sm text-gray-500 mt-0.5">Assign routes, track status, and keep fleet records up to date</p>
        </div>
        <a href="{{ route('vehicles.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold text-white bg-gray-900 hover:bg-gray-800 shadow-sm transition-all no-underline">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Add Vehicle
        </a>
    </div>

    @foreach(['success','error','info'] as $t)
        @if(session($t))
        <div class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-xs font-semibold mb-4 fade-up {{ $t === 'success' ? 'bg-emerald-500/15 border border-emerald-500/25 text-emerald-700' : 'bg-red-500/15 border border-red-500/25 text-red-700' }}">
            @if($t==='success')<svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            @else<svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>@endif
            {{ session($t) }}
        </div>
        @endif
    @endforeach

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4 fade-up">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Total Vehicles</p>
                <p class="text-lg font-bold text-gray-900">{{ $totalVehicles }}</p>
                <p class="text-[10px] text-gray-400">all vehicles</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Active</p>
                <p class="text-lg font-bold text-gray-900">{{ $activeVehicles }}</p>
                <p class="text-[10px] text-gray-400">currently active</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Under Maintenance</p>
                <p class="text-lg font-bold text-gray-900">{{ $underMaintenanceVehicles }}</p>
                <p class="text-[10px] text-gray-400">in maintenance</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-3 mb-4 flex items-center gap-3 flex-wrap fade-up">
        <div class="relative flex-1 min-w-[180px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
            <input type="text" id="vehicleSearch" placeholder="Search plate or route…" class="w-full border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-xs text-gray-700 placeholder:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300 transition-all">
        </div>
        <select id="statusFilter" class="border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300 transition-all">
            <option value="">All Statuses</option>
            <option value="active">Active</option>
            <option value="under_maintenance">Under Maintenance</option>
            <option value="inactive">Inactive</option>
        </select>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden fade-up">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-50 bg-gray-50/50">
                        <th class="text-left px-5 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">
                            <a href="{{ route('vehicles.index', ['sort_by' => 'plate_number', 'sort_order' => ($sortBy === 'plate_number' && $sortOrder === 'asc') ? 'desc' : 'asc']) }}" class="text-inherit no-underline hover:text-gray-600 inline-flex items-center gap-1">
                                Plate Number
                                @if($sortBy === 'plate_number')
                                <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $sortOrder === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>
                                @endif
                            </a>
                        </th>
                        <th class="text-left px-5 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Route</th>
                        <th class="text-left px-5 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Operator</th>
                        <th class="text-center px-5 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50" id="vehicleTbody">
                @forelse($vehicles as $vehicle)
                    @php
                        $vstatus = strtolower($vehicle->status ?? 'active');
                        $vlabel = match($vstatus) { 'under_maintenance' => 'Maintenance', default => ucfirst($vstatus) };
                        $badgeColor = match($vstatus) { 'active' => 'bg-emerald-50 text-emerald-600', 'under_maintenance' => 'bg-amber-50 text-amber-600', default => 'bg-gray-50 text-gray-500' };
                    @endphp
                    <tr class="transition-colors hover:bg-gray-50/50 cursor-pointer"
                        data-name="{{ strtolower($vehicle->plate_number . ' ' . ($vehicle->route->route_name ?? '')) }}"
                        data-status="{{ $vstatus }}"
                        onclick="window.location='{{ route('vehicles.show', $vehicle) }}'">
                        <td class="px-5 py-3.5 text-xs font-semibold text-gray-700 tracking-wide">{{ $vehicle->plate_number }}</td>
                        <td class="px-5 py-3.5 text-xs text-gray-600">{{ $vehicle->route->route_name ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-xs text-gray-600">{{ $vehicle->operator }}</td>
                        <td class="px-5 py-3.5 text-center">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[10px] font-bold {{ $badgeColor }}">{{ $vlabel }}</span>
                        </td>
                        <form id="delete-form-{{ $vehicle->id }}" action="{{ route('vehicles.destroy', $vehicle) }}" method="POST" style="display:none;">
                            @csrf @method('DELETE')
                        </form>
                    </tr>
                @empty
                    <tr><td colspan="4">
                        <div class="text-center py-12">
                            <div class="w-12 h-12 rounded-full bg-gray-50 text-gray-300 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            </div>
                            <p class="text-xs font-semibold text-gray-400">No vehicles found</p>
                            <p class="text-[10px] text-gray-300 mt-0.5">Add a vehicle to get started.</p>
                        </div>
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div id="vehicleNoResults" style="display:none;">
            <div class="text-center py-8"><p class="text-xs font-semibold text-gray-400">No results</p><p class="text-[10px] text-gray-300 mt-0.5">Try adjusting your filters.</p></div>
        </div>
    </div>

<div class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center hidden scale-in" id="deleteModal">
    <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-6 max-w-sm w-full mx-4">
        <div class="w-12 h-12 rounded-full bg-red-50 text-red-500 flex items-center justify-center mx-auto mb-3">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </div>
        <h3 class="text-sm font-bold text-gray-900 text-center mb-1">Delete Vehicle?</h3>
        <p class="text-xs text-gray-500 text-center mb-4" id="deleteModalBody">This action cannot be undone.</p>
        <div class="flex gap-2 justify-center">
            <button type="button" onclick="closeModal()" class="px-4 py-2 rounded-xl text-xs font-bold bg-gray-100 text-gray-600 transition-all hover:bg-gray-200 active:scale-[0.97] cursor-pointer border-none">Cancel</button>
            <button type="button" id="deleteConfirmBtn" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-red-500 transition-all hover:bg-red-600 active:scale-[0.97] cursor-pointer border-none">Yes, Delete</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const search = document.getElementById('vehicleSearch');
    const statusF = document.getElementById('statusFilter');
    const tbody = document.getElementById('vehicleTbody');
    const noRes = document.getElementById('vehicleNoResults');
    function run() {
        const q = search.value.toLowerCase().trim();
        const s = statusF.value;
        const rows = Array.from(tbody.querySelectorAll('tr[data-name]'));
        const vis = rows.filter(r => (!q || r.dataset.name.includes(q)) && (!s || r.dataset.status === s));
        rows.forEach(r => r.style.display = 'none');
        vis.forEach(r => r.style.display = '');
        noRes.style.display = vis.length === 0 && rows.length > 0 ? 'block' : 'none';
    }
    search.addEventListener('input', run);
    statusF.addEventListener('change', run);
})();

let _deleteId = null;
function openDeleteModal(id, plate) {
    _deleteId = id;
    document.getElementById('deleteModalBody').textContent = 'Delete vehicle "' + plate + '"? This cannot be undone.';
    document.getElementById('deleteModal').classList.remove('hidden');
}
function closeModal() { document.getElementById('deleteModal').classList.add('hidden'); }
document.getElementById('deleteConfirmBtn').addEventListener('click', function () {
    if (_deleteId) document.getElementById('delete-form-' + _deleteId).submit();
});
document.getElementById('deleteModal').addEventListener('click', function (e) { if (e.target === this) closeModal(); });
document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeModal(); });
</script>
@endpush

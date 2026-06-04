@extends('layouts.layout')
@section('content')
<div class="flex items-start justify-between flex-wrap gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Edit Vehicle</h1>
            <p class="text-sm text-gray-500 mt-0.5">Update vehicle information, assigned route, and status.</p>
        </div>
        <a href="{{ route('vehicles.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl text-xs font-semibold no-underline hover:border-gray-600 hover:text-gray-600 hover:bg-gray-50 transition-colors whitespace-nowrap cursor-pointer">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to List
        </a>
    </div>

    @if(session('success'))
    <div class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-500/15 border border-emerald-500/25 rounded-lg text-emerald-700 text-xs font-semibold mb-4">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if($errors->any())
    <div class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-500/15 border border-red-500/25 rounded-lg text-red-700 text-xs font-semibold mb-4">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
        Please fix the errors below.
    </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 max-w-3xl">
        <form action="{{ route('vehicles.update', $vehicle) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-gray-500 mb-1.5">Plate Number <span class="text-amber-600">*</span></label>
                    <input type="text" name="plate_number" value="{{ old('plate_number', $vehicle->plate_number) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-xs text-gray-700 placeholder:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300 transition-all @error('plate_number') border-red-300 @enderror" placeholder="e.g., ABC-1234" required>
                    @error('plate_number') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-gray-500 mb-1.5">Operator <span class="text-amber-600">*</span></label>
                    <input type="text" name="operator" value="{{ old('operator', $vehicle->operator) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-xs text-gray-700 placeholder:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300 transition-all @error('operator') border-red-300 @enderror" placeholder="e.g., Juan Dela Cruz" required>
                    @error('operator') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-gray-500 mb-1.5">Route <span class="text-amber-600">*</span></label>
                    <select name="route_id" id="route_id" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-xs text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300 transition-all @error('route_id') border-red-300 @enderror" required>
                        <option value="">-- Select Route --</option>
                        @foreach($routes as $route)
                        <option value="{{ $route->id }}" data-boundary="{{ $route->boundary }}" {{ old('route_id', $vehicle->route_id) == $route->id ? 'selected' : '' }}>{{ $route->route_name }}</option>
                        @endforeach
                    </select>
                    @error('route_id') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-gray-500 mb-1.5">Boundary Rate</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">₱</span>
                        <input type="text" id="boundary_display" class="w-full border border-gray-200 rounded-lg pl-7 pr-3 py-2.5 text-xs text-gray-700 bg-gray-50 focus:outline-none" placeholder="0.00" readonly>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-gray-500 mb-1.5">Status <span class="text-amber-600">*</span></label>
                    <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-xs text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300 transition-all @error('status') border-red-300 @enderror" required>
                        <option value="">-- Select Status --</option>
                        <option value="active" {{ old('status', $vehicle->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="under_maintenance" {{ old('status', $vehicle->status) === 'under_maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                    </select>
                    @error('status') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex gap-2.5 justify-end pt-5 border-t border-gray-100 mt-6">
                <a href="{{ route('vehicles.index') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-gray-100 text-gray-600 transition-all hover:bg-gray-200 active:scale-[0.97] cursor-pointer no-underline">Cancel</a>
                <button type="submit" class="px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-gray-900 transition-all hover:bg-gray-800 active:scale-[0.97] cursor-pointer border-none">Update Vehicle</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const routeSelect = document.getElementById('route_id');
    const boundaryDisplay = document.getElementById('boundary_display');
    routeSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const boundary = selectedOption.getAttribute('data-boundary');
        if (boundary && boundary !== '' && boundary !== 'null') {
            boundaryDisplay.value = parseFloat(boundary).toFixed(2);
        } else {
            boundaryDisplay.value = '0.00';
        }
    });
    if (routeSelect.value) {
        routeSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
@endsection

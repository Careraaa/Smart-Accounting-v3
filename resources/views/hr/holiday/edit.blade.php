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
@keyframes overlayIn {
    0% { opacity: 0; }
    100% { opacity: 1; }
}
@keyframes modalIn {
    0% { opacity: 0; transform: scale(0.95) translateY(8px); }
    100% { opacity: 1; transform: scale(1) translateY(0); }
}
.anim-header { animation: fadeSlideUp 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.anim-card { animation: scaleIn 0.4s cubic-bezier(0.16,1,0.3,1) 0.1s both; }
.modal-overlay { animation: overlayIn 0.2s ease both; }
.modal-box { animation: modalIn 0.25s cubic-bezier(0.16,1,0.3,1) 0.05s both; }
</style>
@endpush

@section('content')
<div class="max-w-full">

    {{-- Error flash --}}
    @if($errors->any())
    <div class="flex items-start gap-2.5 px-4 py-3 mb-5 rounded-xl text-sm font-medium bg-red-50 border border-red-200 text-red-700" style="animation:fadeSlideUp 0.35s ease both;">
        <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
        <div>
            <strong>Please fix the errors:</strong>
            <ul class="mt-1 list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    {{-- Header --}}
    <div class="anim-header flex items-start justify-between mb-6 flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Edit Holiday</h1>
            <p class="text-sm text-gray-500 mt-0.5">Update details for <strong>{{ $holiday->name }}</strong></p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ url()->previous() }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:border-gray-300 hover:text-gray-900 hover:shadow-sm transition-all duration-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back
            </a>
            <button type="button" onclick="document.getElementById('deleteModal').classList.remove('hidden')"
                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white bg-red-500 rounded-xl hover:bg-red-600 hover:shadow-lg hover:shadow-red-500/20 active:scale-[0.97] transition-all duration-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Delete
            </button>
        </div>
    </div>

    {{-- Form card --}}
    <div class="anim-card max-w-[600px] mx-auto">
        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">

            <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-100">
                <div class="w-10 h-10 rounded-xl bg-gray-100 text-gray-600 flex items-center justify-center shrink-0 border border-gray-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-900">Holiday Details</p>
                    <p class="text-xs text-gray-400">All fields marked <span class="text-rose-500">*</span> are required</p>
                </div>
            </div>

            <div class="px-6 py-6">
                <form method="POST" action="{{ route('holiday.update', $holiday) }}" id="hldForm" novalidate>
                    @csrf
                    @method('PUT')

                    {{-- Name --}}
                    <div class="mb-5">
                        <label for="name" class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-1.5">Holiday Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" id="name"
                            class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 bg-white outline-none transition-all duration-200 focus:border-gray-400 focus:ring-2 focus:ring-gray-200/50 placeholder:text-gray-400 @error('name') border-red-400 @enderror"
                            placeholder="e.g., Christmas Day"
                            value="{{ old('name', $holiday->name) }}" required>
                        @error('name')
                            <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Date --}}
                    <div class="mb-5">
                        <label for="date" class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-1.5">Holiday Date <span class="text-rose-500">*</span></label>
                        <input type="date" name="date" id="date"
                            class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 bg-white outline-none transition-all duration-200 focus:border-gray-400 focus:ring-2 focus:ring-gray-200/50 @error('date') border-red-400 @enderror"
                            value="{{ old('date', $holiday->date->format('Y-m-d')) }}" required>
                        @error('date')
                            <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Divider --}}
                    <div class="text-[11px] font-bold uppercase tracking-widest text-gray-400 border-b border-gray-100 pb-2.5 mb-5">Classification</div>

                    {{-- Type --}}
                    <div class="mb-5">
                        <label for="type" class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-1.5">Holiday Type <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <select name="type" id="type"
                                class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 bg-white outline-none appearance-none transition-all duration-200 focus:border-gray-400 focus:ring-2 focus:ring-gray-200/50 @error('type') border-red-400 @enderror"
                                required>
                                <option value="">— Select Type —</option>
                                <option value="regular" @selected(old('type', $holiday->type) === 'regular')>Regular Holiday (Full pay when worked)</option>
                                <option value="special" @selected(old('type', $holiday->type) === 'special')>Special Non-Working (No work, no pay)</option>
                            </select>
                            <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                        @error('type')
                            <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                        @enderror
                        <div class="text-xs text-gray-400 mt-2 leading-relaxed">
                            <strong>Regular:</strong> Employees receive full pay even if not worked (if worked previous day).<br>
                            <strong>Special:</strong> Only paid if worked on that day.
                        </div>
                    </div>

                </form>
            </div>

            <div class="flex items-center justify-end gap-2.5 px-6 py-4 border-t border-gray-100 bg-gray-50">
                <button type="submit" form="hldForm" class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded-xl hover:bg-gray-800 hover:shadow-lg hover:shadow-gray-900/20 active:scale-[0.97] transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Save Changes
                </button>
            </div>

        </div>
    </div>

</div>
@endsection

{{-- Delete Modal --}}
<div id="deleteModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 modal-overlay" style="background:rgba(0,0,0,0.5);">
    <div class="modal-box bg-white rounded-xl shadow-2xl max-w-sm w-full p-6">
        <h3 class="text-base font-bold text-gray-900 mb-3">Delete Holiday</h3>
        <p class="text-sm text-gray-600 mb-5 leading-relaxed">Are you sure you want to delete <strong>{{ $holiday->name }}</strong>? This action cannot be undone.</p>
        <div class="flex items-center justify-end gap-2.5">
            <button type="button" onclick="closeDeleteModal()"
                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:border-gray-300 hover:text-gray-900 hover:shadow-sm transition-all duration-200">
                Cancel
            </button>
            <button type="button" onclick="confirmDelete()"
                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white bg-red-500 rounded-xl hover:bg-red-600 hover:shadow-lg hover:shadow-red-500/20 transition-all duration-200">
                Delete
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}
function confirmDelete() {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route('holiday.destroy', $holiday) }}';
    form.innerHTML = '@csrf @method('DELETE')';
    document.body.appendChild(form);
    form.submit();
}
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeDeleteModal();
});
</script>
@endpush

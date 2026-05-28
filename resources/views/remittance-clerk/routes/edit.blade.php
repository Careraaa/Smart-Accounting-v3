@extends('layouts.layout')
@section('content')
<div class="flex items-start justify-between flex-wrap gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Edit Route</h1>
            <p class="text-sm text-gray-500 mt-0.5">Update route details and boundary rate.</p>
        </div>
        <a href="{{ route('routes.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold border border-gray-200 text-gray-600 bg-white hover:bg-gray-50 transition-all no-underline">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
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
        <form action="{{ route('routes.update', $route) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-gray-500 mb-1.5">Origin <span class="text-amber-600">*</span></label>
                    <input type="text" name="origin" value="{{ old('origin', $route->origin) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-xs text-gray-700 placeholder:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300 transition-all @error('origin') border-red-300 @enderror" placeholder="e.g., Marikina" required>
                    @error('origin') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-gray-500 mb-1.5">Destination <span class="text-amber-600">*</span></label>
                    <input type="text" name="destination" value="{{ old('destination', $route->destination) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-xs text-gray-700 placeholder:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300 transition-all @error('destination') border-red-300 @enderror" placeholder="e.g., Cubao" required>
                    @error('destination') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-gray-500 mb-1.5">Boundary <span class="text-amber-600">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">₱</span>
                        <input type="number" name="boundary" step="0.01" min="0" value="{{ old('boundary', $route->boundary) }}" class="w-full border border-gray-200 rounded-lg pl-7 pr-3 py-2.5 text-xs text-gray-700 placeholder:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300 transition-all @error('boundary') border-red-300 @enderror" placeholder="0.00" required>
                    </div>
                    @error('boundary') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex gap-2.5 justify-end pt-5 border-t border-gray-100 mt-6">
                <a href="{{ route('routes.index') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-gray-100 text-gray-600 transition-all hover:bg-gray-200 active:scale-[0.97] cursor-pointer no-underline">Cancel</a>
                <button type="submit" class="px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-gray-900 transition-all hover:bg-gray-800 active:scale-[0.97] cursor-pointer border-none">Update Route</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
@endpush
@endsection

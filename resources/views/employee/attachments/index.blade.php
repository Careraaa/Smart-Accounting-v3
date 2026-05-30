@extends('layouts.layout')

@push('styles')
<style>
@keyframes ad-page-in { 0%{opacity:0} 100%{opacity:1} }
@keyframes ad-stat-in { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes ad-card-in { 0%{opacity:0;transform:translateY(10px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes ad-zone-pulse { 0%,100%{border-color:#d1d5db} 50%{border-color:#9ca3af} }
.ad-page { animation:ad-page-in 0.3s ease-out; }
.ad-stat { animation:ad-stat-in 0.35s ease-out both; }
.ad-stat:nth-child(1) { animation-delay:0.05s; }
.ad-stat:nth-child(2) { animation-delay:0.1s; }
.ad-stat:nth-child(3) { animation-delay:0.15s; }
.ad-stat:nth-child(4) { animation-delay:0.2s; }
.ad-card { animation:ad-card-in 0.3s ease-out both; }
.ad-card:nth-child(1) { animation-delay:0.15s; }
.ad-card:nth-child(2) { animation-delay:0.2s; }
.ad-card:nth-child(3) { animation-delay:0.25s; }
.ad-card:nth-child(4) { animation-delay:0.3s; }
.ad-card:nth-child(5) { animation-delay:0.35s; }
.ad-card:nth-child(6) { animation-delay:0.4s; }
.ad-zone { animation:ad-zone-pulse 3s ease-in-out infinite; }
.ad-zone:hover { animation:none; border-color:#9ca3af; }
</style>
<script>
document.addEventListener('DOMContentLoaded',function(){setTimeout(function(){document.querySelectorAll('.ad-card,.ad-stat').forEach(function(e){var s=getComputedStyle(e);if(s.opacity==='0'){e.style.opacity='1';e.style.transform='translateY(0)'}})},800)})
</script>
@endpush

@section('content')
<div class="ad-page min-h-screen">
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 py-8">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-7">
            <div>
                <p class="text-xs font-semibold tracking-widest text-gray-400 uppercase mb-1">Employee Portal</p>
                <h1 class="text-2xl font-extrabold tracking-tight text-gray-900 leading-tight">My Documents</h1>
                <p class="text-sm text-gray-500 mt-1">Upload missing documents or resubmit rejected ones. HR will review your submissions.</p>
                <div class="flex flex-wrap gap-2 mt-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-gray-200 text-xs font-semibold text-gray-600 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                        {{ now()->format('l, F d, Y') }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-gray-200 text-xs font-semibold text-gray-600 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4m7-1l4 4 4-4m-4-10v14"/></svg>
                        JPG, PNG, PDF
                    </span>
                </div>
            </div>
        </div>

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="mb-5 p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-start gap-3">
                <svg class="w-5 h-5 text-emerald-600 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="flex-1 text-sm font-semibold text-emerald-800">{{ session('success') }}</div>
                <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M18 6L6 18M6 6l12 12"/></svg>
                </button>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-5 p-4 rounded-xl bg-rose-50 border border-rose-200 flex items-start gap-3">
                <svg class="w-5 h-5 text-rose-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg>
                <div class="flex-1 text-sm font-semibold text-rose-800">{{ session('error') }}</div>
                <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M18 6L6 18M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        @php
            $flat          = $attachments->flatten();
            $approvedCount = $flat->where('status','approved')->count();
            $pendingCount  = $flat->where('status','pending')->count();
            $rejectedCount = $flat->where('status','rejected')->count();
            $uploadedKeys  = $attachments->keys()->count();
            $totalTypes    = count($attachmentTypes);
            $missingCount  = $totalTypes - $uploadedKeys;
        @endphp

        {{-- Stat cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
            <div class="ad-stat bg-white border border-gray-200 rounded-xl p-3 sm:p-4 flex items-start gap-2.5 shadow-sm">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4.5 h-4.5 sm:w-5 sm:h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4m7-1l4 4 4-4m-4-10v14"/></svg>
                </div>
                <div class="min-w-0">
                    <div class="text-[0.6rem] sm:text-xs font-semibold text-gray-400 uppercase tracking-wide truncate">Uploaded</div>
                    <div class="text-base sm:text-lg font-bold text-gray-900 leading-none font-mono">{{ $uploadedKeys }}/{{ $totalTypes }}</div>
                    <div class="text-[0.6rem] sm:text-xs text-gray-400 mt-0.5 truncate">document types</div>
                </div>
            </div>
            <div class="ad-stat bg-white border border-gray-200 rounded-xl p-3 sm:p-4 flex items-start gap-2.5 shadow-sm">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4.5 h-4.5 sm:w-5 sm:h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="min-w-0">
                    <div class="text-[0.6rem] sm:text-xs font-semibold text-gray-400 uppercase tracking-wide truncate">Approved</div>
                    <div class="text-base sm:text-lg font-bold text-gray-900 leading-none font-mono">{{ $approvedCount }}</div>
                    <div class="text-[0.6rem] sm:text-xs text-gray-400 mt-0.5 truncate">ready</div>
                </div>
            </div>
            <div class="ad-stat bg-white border border-gray-200 rounded-xl p-3 sm:p-4 flex items-start gap-2.5 shadow-sm">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4.5 h-4.5 sm:w-5 sm:h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
                </div>
                <div class="min-w-0">
                    <div class="text-[0.6rem] sm:text-xs font-semibold text-gray-400 uppercase tracking-wide truncate">Under Review</div>
                    <div class="text-base sm:text-lg font-bold text-gray-900 leading-none font-mono">{{ $pendingCount }}</div>
                    <div class="text-[0.6rem] sm:text-xs text-gray-400 mt-0.5 truncate">pending</div>
                </div>
            </div>
            <div class="ad-stat bg-white border border-gray-200 rounded-xl p-3 sm:p-4 flex items-start gap-2.5 shadow-sm">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-rose-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4.5 h-4.5 sm:w-5 sm:h-5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M12 5a7 7 0 110 14 7 7 0 010-14z"/></svg>
                </div>
                <div class="min-w-0">
                    <div class="text-[0.6rem] sm:text-xs font-semibold text-gray-400 uppercase tracking-wide truncate">Action Needed</div>
                    <div class="text-base sm:text-lg font-bold text-gray-900 leading-none font-mono">{{ $rejectedCount + $missingCount }}</div>
                    <div class="text-[0.6rem] sm:text-xs text-gray-400 mt-0.5 truncate">missing/rejected</div>
                </div>
            </div>
        </div>

        {{-- Document cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($attachmentTypes as $key => $label)
                @php
                    $records    = $attachments->get($key, collect());
                    $latest     = $records->first();
                    $isPending  = $latest?->status === 'pending';
                    $isApproved = $latest?->status === 'approved';
                    $isRejected = $latest?->status === 'rejected';
                    $canUpload  = $latest === null || $isRejected;
                @endphp

                <div class="ad-card bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col hover:shadow-lg hover:-translate-y-0.5 transition-all duration-[400ms]">
                    <div class="h-1 rounded-t-2xl transition-colors duration-500 {{ $isApproved ? 'bg-emerald-400' : ($isPending ? 'bg-amber-400' : ($isRejected ? 'bg-rose-400' : 'bg-gray-200')) }}"></div>

                    <div class="p-5 flex flex-col gap-4 flex-1">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 transition-all duration-500 {{ $isApproved ? 'bg-emerald-50 text-emerald-500' : ($isPending ? 'bg-amber-50 text-amber-500' : ($isRejected ? 'bg-rose-50 text-rose-500' : 'bg-gray-100 text-gray-400')) }}">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                </div>
                                <span class="text-sm font-semibold text-gray-900">{{ $label }}</span>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold {{ $isApproved ? 'bg-emerald-50 text-emerald-600' : ($isPending ? 'bg-amber-50 text-amber-600' : ($isRejected ? 'bg-rose-50 text-rose-600' : 'bg-gray-100 text-gray-500')) }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $isApproved ? 'bg-emerald-500' : ($isPending ? 'bg-amber-500 animate-pulse' : ($isRejected ? 'bg-rose-500' : 'bg-gray-300')) }}"></span>
                                {{ $isApproved ? 'Approved' : ($isPending ? 'Pending' : ($isRejected ? 'Rejected' : 'Missing')) }}
                            </span>
                        </div>

                        @if($latest)
                            <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 border border-gray-100">
                                <div class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-700 truncate">{{ $latest->original_name }}</p>
                                    <p class="text-xs text-gray-400">{{ $latest->created_at->diffForHumans() }}</p>
                                </div>
                                <a href="{{ $latest->url }}" target="_blank" class="flex-shrink-0 w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-900 hover:text-white flex items-center justify-center transition-all duration-300 animate-bounce hover:animate-none group/dl" title="Download">
                                    <svg class="w-4 h-4 transition-transform duration-300 group-hover/dl:scale-110" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" fill="none"><path d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3" stroke-linejoin="round" stroke-linecap="round"></path></svg>
                                </a>
                            </div>

                            @if($isRejected && $latest->rejection_reason)
                                <div class="flex items-start gap-2 p-3 rounded-xl bg-rose-50 border border-rose-100">
                                    <svg class="w-4 h-4 text-rose-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg>
                                    <p class="text-xs text-rose-600"><span class="font-semibold">Rejected:</span> {{ $latest->rejection_reason }}</p>
                                </div>
                            @endif

                            @if($isPending)
                                <div class="flex items-center gap-2 p-3 rounded-xl bg-amber-50 border border-amber-100">
                                    <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
                                    <p class="text-xs font-medium text-amber-700">Under review</p>
                                </div>
                            @endif
                        @endif

                        @if($canUpload)
                            <div class="mt-auto {{ $latest ? 'pt-4 border-t border-gray-100' : '' }}">
                                <form method="POST" action="{{ route('employee.attachments.store') }}" enctype="multipart/form-data" class="ad-form">
                                    @csrf
                                    <input type="hidden" name="attachment_key" value="{{ $key }}">
                                    <label class="block text-xs font-medium text-gray-500 mb-2">
                                        {{ $isRejected ? 'Upload new document' : 'Upload document' }}
                                    </label>
                                    <div class="flex items-center gap-2">
                                        <label class="ad-zone flex-1 flex items-center gap-2 px-3 py-2.5 rounded-xl border-2 border-dashed border-gray-300 bg-gray-50/50 hover:bg-gray-100 hover:border-gray-400 cursor-pointer transition-all duration-300">
                                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5-5 5 5M12 3v12"/></svg>
                                            <span class="text-xs text-gray-500 truncate ad-filename">Choose file...</span>
                                            <input type="file" name="file" accept=".jpg,.jpeg,.png,.pdf" required class="hidden" onchange="this.closest('.ad-form').querySelector('.ad-filename').textContent=this.files[0].name">
                                        </label>
                                        <button type="submit" class="flex-shrink-0 px-4 py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-xs font-semibold rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 active:scale-95">
                                            Upload
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1.5">JPG, PNG or PDF · Max 5MB</p>
                                </form>
                            </div>
                        @elseif($isPending)
                            <div class="mt-auto pt-4 border-t border-gray-100 text-center">
                                <p class="text-xs text-gray-400">Awaiting HR review</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</div>
@endsection

@extends('layouts.layout')

@push('styles')
<style>
@keyframes ad-page-in { 0%{opacity:0} 100%{opacity:1} }
@keyframes ad-stat-in { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes ad-card-in { 0%{opacity:0;transform:translateY(10px)} 100%{opacity:1;transform:translateY(0)} }
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
</style>
@endpush

@section('content')
<div class="ad-page min-h-screen bg-gray-50/60">
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

                    $borderColor = match(true) {
                        $isApproved => 'border-emerald-300',
                        $isPending  => 'border-amber-300',
                        $isRejected => 'border-rose-300',
                        default     => 'border-gray-200',
                    };
                    $borderWidth = $latest ? 'border-2' : 'border';
                @endphp

                <div class="ad-card bg-white border {{ $borderWidth }} {{ $borderColor }} rounded-2xl shadow-sm overflow-hidden flex flex-col">
                    <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100">
                        <div class="flex items-center gap-2.5">
                            <span class="w-2 h-2 rounded-full bg-gray-900"></span>
                            <span class="text-sm font-bold text-gray-800">{{ $label }}</span>
                        </div>
                        @if($isApproved)     <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">Approved</span>
                        @elseif($isPending)  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-700">Under Review</span>
                        @elseif($isRejected) <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-700">Rejected</span>
                        @else                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600">Missing</span>
                        @endif
                    </div>

                    <div class="p-5 flex flex-col gap-3 flex-1">
                        @if($latest)
                            @if($latest->is_image)
                                <img src="{{ $latest->url }}" alt="{{ $label }}"
                                    class="w-full rounded-xl object-cover"
                                    style="max-height:100px;">
                            @else
                                <div class="flex items-center gap-2.5 p-3 rounded-xl bg-gray-50 border border-gray-100">
                                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    <span class="text-sm text-gray-600 truncate font-semibold">{{ $latest->original_name }}</span>
                                </div>
                            @endif

                            <div class="text-xs text-gray-400">Submitted {{ $latest->created_at->diffForHumans() }}</div>

                            @if($isRejected && $latest->rejection_reason)
                                <div class="p-3 rounded-xl bg-rose-50 border border-rose-100">
                                    <span class="text-xs font-bold text-rose-700">Rejected:</span>
                                    <span class="text-xs text-rose-600">{{ $latest->rejection_reason }}</span>
                                </div>
                            @endif

                            @if($isPending)
                                <p class="text-xs text-amber-600 font-semibold flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
                                    Waiting for HR review.
                                </p>
                            @endif

                            <a href="{{ $latest->url }}" target="_blank" class="inline-flex items-center gap-2 self-start px-3.5 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-xs font-bold rounded-xl transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                View
                            </a>
                        @else
                            <p class="text-xs text-gray-400 font-semibold flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg>
                                Not uploaded yet.
                            </p>
                        @endif

                        @if($canUpload)
                            <form method="POST" action="{{ route('employee.attachments.store') }}" enctype="multipart/form-data" class="mt-auto pt-3 border-t border-gray-100">
                                @csrf
                                <input type="hidden" name="attachment_key" value="{{ $key }}">
                                <label class="block text-xs font-semibold text-gray-500 mb-1.5">
                                    {{ $isRejected ? 'Resubmit document' : 'Upload document' }}
                                </label>
                                <div class="flex flex-col sm:flex-row gap-1.5">
                                    <input type="file" name="file"
                                        class="block w-full text-xs text-gray-600 file:mr-2 file:py-1.5 file:px-2.5 file:rounded-lg file:border-0 file:text-[10px] sm:file:text-xs file:font-bold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 file:cursor-pointer file:transition-all rounded-xl border border-gray-200 bg-white px-2 py-1.5"
                                        accept=".jpg,.jpeg,.png,.pdf" required>
                                    <button type="submit" class="inline-flex items-center justify-center w-full sm:w-9 h-9 bg-gray-900 hover:bg-gray-800 text-white rounded-xl transition-all flex-shrink-0">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4m7-1l4 4 4-4m-4-10v14"/></svg>
                                    </button>
                                </div>
                                <div class="text-xs text-gray-400 mt-1">JPG, PNG or PDF · Max 5MB</div>
                            </form>
                        @elseif($isPending)
                            <p class="text-xs text-gray-400 mt-auto pt-3 border-t border-gray-100">Cannot re-upload while a review is pending.</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</div>
@endsection

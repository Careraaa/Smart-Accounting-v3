@extends('layouts.layout')

@push('styles')
<style>
@keyframes ha-page-in { 0%{opacity:0} 100%{opacity:1} }
@keyframes ha-stat-in { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes ha-card-in { 0%{opacity:0;transform:translateY(10px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes ha-zone-pulse { 0%,100%{border-color:#d1d5db} 50%{border-color:#9ca3af} }
.ha-page { animation:ha-page-in 0.3s ease-out; }
.ha-stat { animation:ha-stat-in 0.35s ease-out both; }
.ha-stat:nth-child(1) { animation-delay:0.05s; }
.ha-stat:nth-child(2) { animation-delay:0.1s; }
.ha-stat:nth-child(3) { animation-delay:0.15s; }
.ha-stat:nth-child(4) { animation-delay:0.2s; }
.ha-card { animation:ha-card-in 0.3s ease-out both; }
.ha-card:nth-child(1) { animation-delay:0.15s; }
.ha-card:nth-child(2) { animation-delay:0.2s; }
.ha-card:nth-child(3) { animation-delay:0.25s; }
.ha-card:nth-child(4) { animation-delay:0.3s; }
.ha-card:nth-child(5) { animation-delay:0.35s; }
.ha-card:nth-child(6) { animation-delay:0.4s; }
.ha-card:nth-child(7) { animation-delay:0.45s; }
.ha-card:nth-child(8) { animation-delay:0.5s; }
.ha-card:nth-child(9) { animation-delay:0.55s; }
.ha-card:nth-child(10) { animation-delay:0.6s; }
.ha-card:nth-child(11) { animation-delay:0.65s; }
.ha-zone { animation:ha-zone-pulse 3s ease-in-out infinite; }
.ha-zone:hover { animation:none; border-color:#9ca3af; }
.ha-history-item.is-open .ha-chevron { transform: rotate(180deg); }
.ha-history-item.is-open .ha-history-body { max-height: 600px !important; }
</style>
@endpush

@section('content')
<div class="ha-page min-h-screen">
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 py-8">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-7">
            <div>
                <p class="text-xs font-semibold tracking-widest text-gray-400 uppercase mb-1">HR Portal</p>
                <h1 class="text-xl font-extrabold tracking-tight text-gray-900 leading-tight">{{ $employee->first_name }} {{ $employee->last_name }} — Attachments</h1>
                <p class="text-sm text-gray-500 mt-1">Review, approve, or reject employee documents.</p>
            </div>
            <a href="{{ route('employees.show', $employee) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl text-xs font-semibold no-underline hover:border-gray-600 hover:text-gray-600 hover:bg-gray-50 transition-colors whitespace-nowrap cursor-pointer">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Employee
            </a>
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
            // Count each document type ONCE by its latest upload's status
            // (history versions are not tallied in the summary cards).
            $latestPerKey   = $attachments->map(fn($records) => $records->first());
            $approvedCount  = $latestPerKey->where('status', 'approved')->count();
            $pendingCount   = $latestPerKey->where('status', 'pending')->count();
            $rejectedCount  = $latestPerKey->where('status', 'rejected')->count();
            $uploadedKeys   = $attachments->keys()->count();
            $totalTypes     = count($attachmentTypes);
            $missingCount   = $totalTypes - $uploadedKeys;
        @endphp

        {{-- Stat cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
            <div class="ha-stat bg-white border border-gray-200 rounded-xl p-3 sm:p-4 flex items-start gap-2.5 shadow-sm">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4.5 h-4.5 sm:w-5 sm:h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4m7-1l4 4 4-4m-4-10v14"/></svg>
                </div>
                <div class="min-w-0">
                    <div class="text-[0.6rem] sm:text-xs font-semibold text-gray-400 uppercase tracking-wide truncate">Uploaded</div>
                    <div class="text-base sm:text-lg font-bold text-gray-900 leading-none font-mono">{{ $uploadedKeys }}/{{ $totalTypes }}</div>
                </div>
            </div>
            <div class="ha-stat bg-white border border-gray-200 rounded-xl p-3 sm:p-4 flex items-start gap-2.5 shadow-sm">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4.5 h-4.5 sm:w-5 sm:h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="min-w-0">
                    <div class="text-[0.6rem] sm:text-xs font-semibold text-gray-400 uppercase tracking-wide truncate">Approved</div>
                    <div class="text-base sm:text-lg font-bold text-gray-900 leading-none font-mono">{{ $approvedCount }}</div>
                </div>
            </div>
            <div class="ha-stat bg-white border border-gray-200 rounded-xl p-3 sm:p-4 flex items-start gap-2.5 shadow-sm">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4.5 h-4.5 sm:w-5 sm:h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
                </div>
                <div class="min-w-0">
                    <div class="text-[0.6rem] sm:text-xs font-semibold text-gray-400 uppercase tracking-wide truncate">Under Review</div>
                    <div class="text-base sm:text-lg font-bold text-gray-900 leading-none font-mono">{{ $pendingCount }}</div>
                </div>
            </div>
            <div class="ha-stat bg-white border border-gray-200 rounded-xl p-3 sm:p-4 flex items-start gap-2.5 shadow-sm">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-rose-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4.5 h-4.5 sm:w-5 sm:h-5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M12 5a7 7 0 110 14 7 7 0 010-14z"/></svg>
                </div>
                <div class="min-w-0">
                    <div class="text-[0.6rem] sm:text-xs font-semibold text-gray-400 uppercase tracking-wide truncate">Action Needed</div>
                    <div class="text-base sm:text-lg font-bold text-gray-900 leading-none font-mono">{{ $rejectedCount + $missingCount }}</div>
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
                    $hasFile    = $latest !== null;

                    $topBorder = match(true) {
                        $isApproved => 'bg-emerald-400',
                        $isPending  => 'bg-amber-400',
                        $isRejected => 'bg-rose-400',
                        default     => 'bg-gray-200',
                    };
                    $iconBox = match(true) {
                        $isApproved => 'bg-emerald-50 text-emerald-500',
                        $isPending  => 'bg-amber-50 text-amber-500',
                        $isRejected => 'bg-rose-50 text-rose-500',
                        default     => 'bg-gray-100 text-gray-400',
                    };
                    $badgeClasses = match(true) {
                        $isApproved => 'bg-emerald-50 text-emerald-600',
                        $isPending  => 'bg-amber-50 text-amber-600',
                        $isRejected => 'bg-rose-50 text-rose-600',
                        default     => 'bg-gray-100 text-gray-500',
                    };
                    $dotColor = match(true) {
                        $isApproved => 'bg-emerald-500',
                        $isPending  => 'bg-amber-500 animate-pulse',
                        $isRejected => 'bg-rose-500',
                        default     => 'bg-gray-300',
                    };
                    $badgeText = $isApproved ? 'Approved' : ($isPending ? 'Pending' : ($isRejected ? 'Rejected' : 'Missing'));
                @endphp

                <div class="ha-card bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col hover:shadow-lg hover:-translate-y-0.5 transition-all duration-[400ms]">
                    <div class="h-1 rounded-t-2xl transition-colors duration-500 {{ $topBorder }}"></div>

                    <div class="p-5 flex flex-col gap-4 flex-1">
                        {{-- Title + badge --}}
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 transition-all duration-500 {{ $iconBox }}">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                </div>
                                <span class="text-sm font-semibold text-gray-900">{{ $label }}</span>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[0.6rem] font-semibold {{ $badgeClasses }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }}"></span>
                                {{ $badgeText }}
                            </span>
                        </div>

                        {{-- File preview --}}
                        @if($latest)
                            @if($latest->is_image)
                                <a href="{{ $latest->url }}" target="_blank" class="block rounded-xl overflow-hidden border border-gray-100">
                                    <img src="{{ $latest->url }}" alt="{{ $label }}" class="w-full h-24 object-cover hover:scale-105 transition-transform duration-300">
                                </a>
                            @else
                                <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 border border-gray-100">
                                    <div class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-700 truncate">{{ $latest->original_name }}</p>
                                        <p class="text-xs text-gray-400">{{ $latest->file_size_human }}</p>
                                    </div>
                                </div>
                            @endif

                            <p class="text-[0.65rem] text-gray-400 -mt-2">
                                Uploaded {{ $latest->created_at->diffForHumans() }} · by {{ ucfirst($latest->uploaded_by_role) }}
                            </p>

                            {{-- Rejection reason --}}
                            @if($isRejected && $latest->rejection_reason)
                                <div class="flex items-start gap-2 p-3 rounded-xl bg-rose-50 border border-rose-100">
                                    <svg class="w-4 h-4 text-rose-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg>
                                    <p class="text-xs text-rose-600"><span class="font-semibold">Rejected:</span> {{ $latest->rejection_reason }}</p>
                                </div>
                            @endif

                            {{-- Action buttons --}}
                            <div class="flex flex-wrap items-center gap-1.5">
                                <a href="{{ $latest->url }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-[0.6rem] font-semibold transition-colors">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    View
                                </a>

                                @if($isPending)
                                    <form method="POST" action="{{ route('employees.attachments.approve', [$employee, $latest]) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-[0.6rem] font-semibold transition-colors">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            Approve
                                        </button>
                                    </form>

                                    <button type="button" onclick="openRejectModal({{ $latest->id }}, '{{ $label }}')" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 text-[0.6rem] font-semibold transition-colors">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Reject
                                    </button>
                                @endif

                                <form method="POST" action="{{ route('employees.attachments.destroy', [$employee, $latest]) }}" class="inline" data-sa-confirm="Delete this file permanently?">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-gray-100 hover:bg-red-100 text-gray-500 hover:text-red-600 text-[0.6rem] font-semibold transition-colors" title="Delete">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        @endif

                        {{-- HR upload form --}}
                        <div class="mt-auto {{ $latest ? 'pt-4 border-t border-gray-100' : '' }}">
                            <form method="POST" action="{{ route('employees.attachments.store', $employee) }}" enctype="multipart/form-data" class="ha-form">
                                @csrf
                                <input type="hidden" name="attachment_key" value="{{ $key }}">
                                <label class="block text-[0.6rem] font-medium text-gray-500 mb-1.5">
                                    {{ $latest ? 'Replace file' : 'Upload file' }}
                                </label>
                                <div class="flex items-center gap-2">
                                    <label class="ha-zone flex-1 flex items-center gap-2 px-3 py-2 rounded-xl border-2 border-dashed border-gray-300 bg-gray-50/50 hover:bg-gray-100 hover:border-gray-400 cursor-pointer transition-all duration-300">
                                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5-5 5 5M12 3v12"/></svg>
                                        <span class="text-xs text-gray-500 truncate ha-filename">Choose file...</span>
                                        <input type="file" name="file" accept=".jpg,.jpeg,.png,.pdf" required class="hidden" onchange="this.closest('.ha-form').querySelector('.ha-filename').textContent=this.files[0].name">
                                    </label>
                                    <button type="submit" class="flex-shrink-0 px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white text-xs font-semibold rounded-xl transition-all duration-300">
                                        {{ $latest ? 'Replace' : 'Upload' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Reject modal --}}
                @if($isPending && $latest)
                <div id="rejectModal{{ $latest->id }}" class="fixed inset-0 z-[9999] flex items-center justify-center" style="display:none">
                    <div class="absolute inset-0 bg-black/40" onclick="closeRejectModal({{ $latest->id }})"></div>
                    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-6" style="animation:modalFadeIn 0.2s ease-out,modalScaleIn 0.2s ease-out">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-bold text-gray-900">Reject — {{ $label }}</h3>
                            <button onclick="closeRejectModal({{ $latest->id }})" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <form method="POST" action="{{ route('employees.attachments.reject', [$employee, $latest]) }}">
                            @csrf @method('PATCH')
                            <div class="mb-4">
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Reason <span class="text-rose-500">*</span></label>
                                <textarea name="rejection_reason" rows="3" required placeholder="e.g. Image is blurry, wrong document..." class="w-full px-3 py-2 rounded-xl border border-gray-200 text-sm text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-300 transition-all"></textarea>
                            </div>
                            <div class="flex items-center justify-end gap-2">
                                <button type="button" onclick="closeRejectModal({{ $latest->id }})" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition-colors">Cancel</button>
                                <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-xl transition-colors">Reject</button>
                            </div>
                        </form>
                    </div>
                </div>
                @endif

            @endforeach
        </div>

        {{-- Upload history --}}
        @php $hasHistory = $attachments->filter(fn($r) => $r->count() > 1)->isNotEmpty(); @endphp
        @if($hasHistory)
        <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-50">
                <span class="text-xs font-semibold text-gray-900">Upload History</span>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($attachments->filter(fn($r) => $r->count() > 1) as $key => $records)
                    @php $first = $records->first(); @endphp
                    <div class="ha-history-item">
                        <button onclick="this.closest('.ha-history-item').classList.toggle('is-open')" class="w-full flex items-center justify-between px-5 py-3 text-left hover:bg-gray-50/40 transition-colors" type="button">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-semibold text-gray-900">{{ $attachmentTypes[$key] ?? $key }}</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-gray-100 text-gray-500 text-[0.55rem] font-semibold">{{ $records->count() }} versions</span>
                            </div>
                            <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-300 ha-chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="ha-history-body overflow-hidden" style="max-height:0;transition:max-height 0.3s ease-out">
                            <div class="border-t border-gray-50">
                                <table class="w-full">
                                    <thead>
                                        <tr class="border-b border-gray-50">
                                            <th class="px-5 py-2.5 text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 text-left">File</th>
                                            <th class="px-5 py-2.5 text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 text-left">Uploaded</th>
                                            <th class="px-5 py-2.5 text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 text-left">By</th>
                                            <th class="px-5 py-2.5 text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 text-left">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($records as $record)
                                            @php
                                            $stBadge = match($record->status) {
                                                'approved' => 'bg-emerald-50 text-emerald-600',
                                                'rejected' => 'bg-rose-50 text-rose-600',
                                                default    => 'bg-amber-50 text-amber-600',
                                            };
                                            $stText = match($record->status) {
                                                'approved' => 'Approved',
                                                'rejected' => 'Rejected',
                                                default    => 'Pending',
                                            };
                                            @endphp
                                            <tr class="hover:bg-gray-50/40 transition-colors">
                                                <td class="px-5 py-2.5">
                                                    <a href="{{ $record->url }}" target="_blank" class="text-xs font-medium text-gray-700 hover:text-indigo-600 no-underline">{{ $record->original_name }}</a>
                                                </td>
                                                <td class="px-5 py-2.5 text-xs text-gray-400">{{ $record->created_at->format('M d, Y') }}</td>
                                                <td class="px-5 py-2.5 text-xs text-gray-500">{{ ucfirst($record->uploaded_by_role) }}</td>
                                                <td class="px-5 py-2.5">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[0.55rem] font-semibold {{ $stBadge }}">{{ $stText }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>
@endsection

@push('scripts')
<script>
function openRejectModal(id, label) {
    if (typeof sndPlay === 'function') sndPlay();
    document.getElementById('rejectModal' + id).style.display = 'flex';
}
function closeRejectModal(id) {
    document.getElementById('rejectModal' + id).style.display = 'none';
}
</script>
@endpush

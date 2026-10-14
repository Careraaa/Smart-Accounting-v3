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
            // Only consider attachment keys that are real document types.
            // (Guards against legacy/mismatched keys skewing the summary.)
            $knownKeys   = array_keys($attachmentTypes);
            $known       = $attachments->filter(fn($records, $key) => in_array($key, $knownKeys, true));

            // Count each document type ONCE by its latest upload's status.
            $latestPerKey   = $known->map(fn($records) => $records->first());
            $approvedCount  = $latestPerKey->where('status', 'approved')->count();
            $pendingCount   = $latestPerKey->where('status', 'pending')->count();
            $rejectedCount  = $latestPerKey->where('status', 'rejected')->count();
            $uploadedKeys   = $known->keys()->count();
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
        <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($attachmentTypes as $key => $label)
                @php
                    $records    = $attachments->get($key, collect());
                    $latest     = $records->first();
                    $isPending  = $latest?->status === 'pending';
                    $isApproved = $latest?->status === 'approved';
                    $isRejected = $latest?->status === 'rejected';

                    $badgeText  = $isApproved ? 'Approved' : ($isPending ? 'Pending' : ($isRejected ? 'Rejected' : 'Missing'));
                    $cardTint = match(true) {
                        $isApproved => 'border-green-500 dark:border-green-400',
                        $isPending  => 'border-amber-400 dark:border-amber-500',
                        $isRejected => 'border-rose-400 dark:border-rose-500',
                        default     => 'border-gray-200 dark:border-gray-700',
                    };
                    $statusPillCls = match(true) {
                        $isApproved => 'bg-emerald-50 text-emerald-600 border-emerald-200 dark:border-emerald-800',
                        $isPending  => 'bg-amber-50 text-amber-600 border-amber-200 dark:border-amber-800',
                        $isRejected => 'bg-rose-50 text-rose-600 border-rose-200 dark:border-rose-800',
                        default     => 'bg-gray-100 text-gray-500 border-gray-200 dark:border-gray-700',
                    };
                @endphp

                <div class="ha-card bg-white dark:bg-gray-800 rounded-xl border p-3.5 flex flex-col gap-2.5 transition-colors duration-200 {{ $cardTint }}" id="ha-card-{{ $key }}">
                    {{-- Title (matches add-employee attachments tab) --}}
                    <div class="text-xs font-bold text-gray-900 dark:text-gray-100 flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span class="truncate">{{ $label }}</span>
                    </div>

                    @if($latest)
                        {{-- Existing file row (compact, like add-employee tab) --}}
                        <form method="POST" action="{{ route('employees.attachments.store', $employee) }}" enctype="multipart/form-data" class="ha-form" id="ha-form-{{ $key }}">
                            @csrf
                            <input type="hidden" name="attachment_key" value="{{ $key }}">
                            <div class="ha-existing flex items-center gap-2 px-2.5 py-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                @if($latest->is_image)
                                    <a href="{{ $latest->url }}" target="_blank" rel="noopener" title="Click to view full image" class="flex-shrink-0 block">
                                        <img src="{{ $latest->url }}" alt="{{ $label }}" class="w-10 h-10 object-cover rounded-lg">
                                    </a>
                                @else
                                    <a href="{{ $latest->url }}" target="_blank" rel="noopener" title="Click to view document" class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-900/20 flex items-center justify-center flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9a2 2 0 00-2-2h-5.586a1 1 0 01-.707-.293l-1.414-1.414A1 1 0 008.586 5H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </a>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <div class="text-xs font-semibold text-gray-700 dark:text-gray-300 truncate">{{ $latest->original_name }}</div>
                                    <div class="text-[0.7rem] text-gray-400 dark:text-gray-500">{{ $latest->created_at ? $latest->created_at->format('M d, Y h:i A') : '' }}</div>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[0.55rem] font-bold uppercase tracking-wide border {{ $statusPillCls }} flex-shrink-0">{{ $badgeText }}</span>
                                <label class="ha-replace inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:text-red-500 dark:hover:text-red-400 hover:border-red-300 dark:hover:border-red-600 text-[0.65rem] font-semibold cursor-pointer transition-all active:scale-[0.95] flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                    Replace
                                    <input type="file" name="file" accept=".jpg,.jpeg,.png,.pdf" class="hidden" onchange="previewHrFile(this,'{{ $key }}')">
                                </label>
                            </div>
                        </form>

                        {{-- Rejection reason --}}
                        @if($isRejected && $latest->rejection_reason)
                            <div class="flex items-start gap-1.5 px-2.5 py-2 rounded-lg bg-rose-50 dark:bg-rose-900/20 border border-rose-100 dark:border-rose-800 text-[0.7rem] text-rose-600 dark:text-rose-400 leading-snug">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg>
                                <span><span class="font-semibold">Rejected:</span> {{ $latest->rejection_reason }}</span>
                            </div>
                        @endif

                        {{-- Pending actions --}}
                        @if($isPending)
                            <div class="flex flex-wrap items-center gap-1.5">
                                <form method="POST" action="{{ route('employees.attachments.approve', [$employee, $latest]) }}" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-emerald-600 dark:text-emerald-400 hover:border-emerald-300 dark:hover:border-emerald-600 text-[0.65rem] font-semibold transition-all">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        Approve
                                    </button>
                                </form>
                                <button type="button" onclick="openRejectModal({{ $latest->id }}, '{{ $label }}')" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-rose-600 dark:text-rose-400 hover:border-rose-300 dark:hover:border-rose-600 text-[0.65rem] font-semibold transition-all">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Reject
                                </button>
                                <form method="POST" action="{{ route('employees.attachments.destroy', [$employee, $latest]) }}" class="inline" data-sa-confirm="Delete this file permanently?">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:border-red-300 dark:hover:border-red-600 hover:text-red-600 dark:hover:text-red-400 text-[0.65rem] font-semibold transition-all" title="Delete">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        @endif
                    @else
                        {{-- Click to upload (matches add-employee tab) --}}
                        <form method="POST" action="{{ route('employees.attachments.store', $employee) }}" enctype="multipart/form-data" class="ha-form" id="ha-form-{{ $key }}">
                            @csrf
                            <input type="hidden" name="attachment_key" value="{{ $key }}">
                            <label class="flex flex-col items-center justify-center gap-1.5 px-3 py-4 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-xl cursor-pointer transition-all duration-150 hover:border-red-300 dark:hover:border-red-600 hover:bg-red-50 dark:hover:bg-red-900/10 active:scale-[0.98]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                <span class="text-[0.65rem] font-semibold text-gray-400 dark:text-gray-500">Click to upload</span>
                                <input type="file" name="file" accept=".jpg,.jpeg,.png,.pdf" class="hidden" onchange="previewHrFile(this,'{{ $key }}')">
                            </label>
                        </form>
                    @endif
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
function previewHrFile(input, key) {
    var card = document.getElementById('ha-card-' + key);
    var form = document.getElementById('ha-form-' + key);
    if (!card || !form) return;

    var file = input.files && input.files[0];
    if (!file) return;

    var oldPreview = form.querySelector('.ha-prev-inline');
    if (oldPreview) {
        if (oldPreview.dataset.url) URL.revokeObjectURL(oldPreview.dataset.url);
        oldPreview.remove();
    }

    var existingBlock = card.querySelector('.ha-existing');
    if (existingBlock) existingBlock.style.display = 'none';
    card.classList.add('border-green-500', 'dark:border-green-400');

    var url = URL.createObjectURL(file);
    var isImage = file.type === 'image/jpeg' || file.type === 'image/png' || file.type === 'image/gif' || file.type === 'image/webp';
    var ext = (file.name.split('.').pop() || '').toUpperCase();
    var size = file.size < 1048576
        ? (file.size / 1024).toFixed(1) + ' KB'
        : (file.size / 1048576).toFixed(1) + ' MB';

    var wrap = document.createElement('div');
    wrap.className = 'ha-prev-inline flex items-center gap-2 px-2.5 py-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg';
    wrap.dataset.url = url;

    var link = document.createElement('a');
    link.href = url;
    link.target = '_blank';
    link.rel = 'noopener';
    link.title = isImage ? 'Click to view full image' : 'Click to view document';
    link.className = isImage
        ? 'flex-shrink-0 block'
        : 'w-8 h-8 rounded-lg bg-red-50 dark:bg-red-900/20 flex items-center justify-center flex-shrink-0';

    if (isImage) {
        var img = document.createElement('img');
        img.src = url;
        img.alt = file.name;
        img.className = 'w-10 h-10 object-cover rounded-lg';
        link.appendChild(img);
    } else {
        link.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9a2 2 0 00-2-2h-5.586a1 1 0 01-.707-.293l-1.414-1.414A1 1 0 008.586 5H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>';
    }
    wrap.appendChild(link);

    var info = document.createElement('div');
    info.className = 'flex-1 min-w-0';
    var name = document.createElement('div');
    name.className = 'text-xs font-semibold text-gray-700 dark:text-gray-300 truncate';
    name.textContent = file.name;
    var meta = document.createElement('div');
    meta.className = 'text-[0.7rem] text-gray-400 dark:text-gray-500';
    meta.textContent = (isImage ? 'Image' : ext) + ' · ' + size;
    info.appendChild(name);
    info.appendChild(meta);
    wrap.appendChild(info);

    form.appendChild(wrap);

    // No submit button (clean add-employee look) — upload automatically.
    if (!form.dataset.submitting) {
        form.dataset.submitting = '1';
        setTimeout(function () {
            form.submit();
        }, 400);
    }
}
</script>
@endpush

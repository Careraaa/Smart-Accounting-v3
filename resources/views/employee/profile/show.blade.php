@extends('layouts.layout')

@push('styles')
<style>
@keyframes pfUp { 0%{opacity:0;transform:translateY(16px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes pfIn { 0%{opacity:0;transform:scale(0.92)} 100%{opacity:1;transform:scale(1)} }
@keyframes pfSlide { 0%{opacity:0;transform:translateY(24px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes pfBounce { 0%{opacity:0;transform:scale(0.5)} 50%{transform:scale(1.12)} 100%{opacity:1;transform:scale(1)} }
@keyframes pfShimmer { 0%{transform:translateX(-100%)} 100%{transform:translateX(200%)} }
@keyframes pfBadge { 0%{opacity:0;transform:translateX(-8px)} 100%{opacity:1;transform:translateX(0)} }
@keyframes pfIconPop { 0%{opacity:0;transform:scale(0.6)} 60%{transform:scale(1.08)} 100%{opacity:1;transform:scale(1)} }
.pf-h { animation:pfUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.pf-shimmer { position:absolute;inset:0;pointer-events:none;overflow:hidden; }
.pf-shimmer::after { content:'';position:absolute;inset:0;background:linear-gradient(90deg,transparent 0%,rgba(255,255,255,0.06) 50%,transparent 100%);animation:pfShimmer 2.2s cubic-bezier(0.25,0.46,0.45,0.94) 0.6s both; }
.pf-avatar { animation:pfBounce 0.55s cubic-bezier(0.34,1.56,0.64,1) 0.15s both; }
.pf-badge { animation:pfBadge 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.pf-badge:nth-child(1){animation-delay:.28s}
.pf-badge:nth-child(2){animation-delay:.34s}
.pf-badge:nth-child(3){animation-delay:.4s}
.pf-stat { animation:pfIn 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.pf-stat-icon { animation:pfIconPop 0.45s cubic-bezier(0.34,1.56,0.64,1) both; }
.pf-stat:nth-child(1) .pf-stat-icon{animation-delay:.1s}
.pf-stat:nth-child(2) .pf-stat-icon{animation-delay:.14s}
.pf-stat:nth-child(3) .pf-stat-icon{animation-delay:.18s}
.pf-stat:nth-child(4) .pf-stat-icon{animation-delay:.22s}
.pf-stat:nth-child(1){animation-delay:.06s}
.pf-stat:nth-child(2){animation-delay:.1s}
.pf-stat:nth-child(3){animation-delay:.14s}
.pf-stat:nth-child(4){animation-delay:.18s}
.pf-card { animation:pfUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.pf-card:nth-child(1){animation-delay:.12s}
.pf-card:nth-child(2){animation-delay:.2s}
.pf-card-body { animation:pfUp 0.35s cubic-bezier(0.16,1,0.3,1) 0.3s both; }
</style>
@endpush

@section('content')
@php
    $u = auth()->user();
    $name = $u->first_name ? trim($u->first_name . ' ' . $u->last_name) : ($u->name ?? 'User');
    $dept = $u->department ?? '—';
    $role = ucfirst(str_replace('_', ' ', $u->role ?? 'user'));
    $initials = strtoupper(
        substr($u->first_name ?? $u->name ?? 'U', 0, 1) .
        substr($u->last_name ?? '', 0, 1)
    );
@endphp

<div class="min-h-screen bg-[#f6f7fb]">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8">

        {{-- Flash --}}
        @if(session('success'))
            <div class="pf-h mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                <div class="text-sm font-semibold text-emerald-800">{{ session('success') }}</div>
            </div>
        @endif

        {{-- ── Hero ── --}}
        <div class="pf-h relative bg-gradient-to-br from-gray-900 via-[#0f172a] to-gray-900 rounded-2xl p-6 sm:p-8 mb-6 overflow-hidden isolate">
            <div class="pf-shimmer"></div>
            <div class="absolute -top-24 -right-24 w-80 h-80 rounded-full bg-rose-600/10 blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-32 -left-32 w-96 h-96 rounded-full bg-sky-500/10 blur-3xl pointer-events-none"></div>
            <div class="absolute inset-0 opacity-[0.03]" style="background-image:radial-gradient(circle at 1px 1px,#fff 1px,transparent 0);background-size:24px 24px;"></div>

            <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
                <div class="flex items-center gap-4">
                    <form action="{{ route('employee.profile.photo') }}" method="POST" enctype="multipart/form-data" id="emp-photo-form" class="relative shrink-0">
                        @csrf
                        <label tabindex="0" class="pf-avatar block w-16 h-16 rounded-2xl overflow-hidden border-2 border-white/20 shadow-inner cursor-pointer group relative" role="button" aria-label="Upload profile photo">
                            @if($u->photo_url)
                                <img src="{{ $u->photo_url }}" alt="Photo" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-white/10 flex items-center justify-center text-white font-bold text-xl backdrop-blur-sm">
                                    {{ $initials ?: 'U' }}
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 group-focus-within:opacity-100 transition-opacity duration-200 rounded-2xl">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <input type="file" name="photo" accept="image/*" class="hidden" id="emp-photo-input" onchange="empPreviewPhoto(this);">
                        </label>
                    </form>
                    <div>
                        <h1 class="text-xl font-extrabold text-white tracking-tight">{{ $name }}</h1>
                        <div class="flex items-center gap-2 mt-1 text-sm text-gray-400">
                            <span class="font-mono text-[0.82rem]">{{ $u->email ?? '—' }}</span>
                            <span class="text-gray-600 hidden sm:inline">·</span>
                            <span class="hidden sm:inline">{{ $dept }}</span>
                        </div>
                        <div class="flex flex-wrap gap-2 mt-3">
                            <span class="pf-badge inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[0.7rem] font-semibold text-gray-300 bg-white/10 border border-white/10">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                {{ $role }}
                            </span>
                            <span class="pf-badge inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[0.7rem] font-semibold text-gray-300 bg-white/10 border border-white/10">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                                Joined {{ $u->created_at?->format('M Y') ?? '—' }}
                            </span>
                            @if($u->status)
                                <span class="pf-badge inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[0.7rem] font-semibold {{ $u->status === 'active' ? 'text-emerald-300 bg-emerald-500/15 border-emerald-500/20' : 'text-gray-300 bg-white/10 border-white/10' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $u->status === 'active' ? 'bg-emerald-400' : 'bg-gray-400' }}"></span>
                                    {{ ucfirst($u->status) }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('employee.profile.edit') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white text-sm font-bold rounded-xl transition-all border border-white/10">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897l12.62-12.62z"/></svg>
                        Edit Profile
                    </a>
                </div>
            </div>
        </div>

        {{-- ── Stats ── --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-7">
            <div class="pf-stat group bg-white rounded-xl p-4 flex items-center gap-3 shadow-[0_1px_3px_0_rgba(0,0,0,0.04)] border border-gray-200/80 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <span class="pf-stat-icon w-10 h-10 rounded-lg bg-rose-50 flex items-center justify-center shrink-0 group-hover:bg-rose-100 transition-colors duration-200">
                    <svg class="w-5 h-5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0"/></svg>
                </span>
                <div class="min-w-0">
                    <div class="text-[0.65rem] font-bold uppercase tracking-[0.08em] text-gray-400 truncate group-hover:text-gray-600 transition-colors duration-200">Role</div>
                    <div class="text-sm font-extrabold text-gray-900 truncate">{{ $role }}</div>
                </div>
            </div>
            <div class="pf-stat group bg-white rounded-xl p-4 flex items-center gap-3 shadow-[0_1px_3px_0_rgba(0,0,0,0.04)] border border-gray-200/80 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <span class="pf-stat-icon w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center shrink-0 group-hover:bg-amber-100 transition-colors duration-200">
                    <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21"/></svg>
                </span>
                <div class="min-w-0">
                    <div class="text-[0.65rem] font-bold uppercase tracking-[0.08em] text-gray-400 truncate group-hover:text-gray-600 transition-colors duration-200">Department</div>
                    <div class="text-sm font-extrabold text-gray-900 truncate">{{ $dept }}</div>
                </div>
            </div>
            <div class="pf-stat group bg-white rounded-xl p-4 flex items-center gap-3 shadow-[0_1px_3px_0_rgba(0,0,0,0.04)] border border-gray-200/80 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <span class="pf-stat-icon w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center shrink-0 group-hover:bg-emerald-100 transition-colors duration-200">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3"/></svg>
                </span>
                <div class="min-w-0">
                    <div class="text-[0.65rem] font-bold uppercase tracking-[0.08em] text-gray-400 truncate group-hover:text-gray-600 transition-colors duration-200">Phone</div>
                    <div class="text-sm font-extrabold text-gray-900 font-mono truncate">{{ $u->phone ?? '—' }}</div>
                </div>
            </div>
            <div class="pf-stat group bg-white rounded-xl p-4 flex items-center gap-3 shadow-[0_1px_3px_0_rgba(0,0,0,0.04)] border border-gray-200/80 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <span class="pf-stat-icon w-10 h-10 rounded-lg bg-sky-50 flex items-center justify-center shrink-0 group-hover:bg-sky-100 transition-colors duration-200">
                    <svg class="w-5 h-5 text-sky-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                </span>
                <div class="min-w-0">
                    <div class="text-[0.65rem] font-bold uppercase tracking-[0.08em] text-gray-400 truncate group-hover:text-gray-600 transition-colors duration-200">Member Since</div>
                    <div class="text-sm font-extrabold text-gray-900 truncate">{{ $u->created_at?->format('M Y') ?? '—' }}</div>
                </div>
            </div>
        </div>

        {{-- ── Two-column content ── --}}
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_340px] gap-5 items-start">

            {{-- Left: Personal Information --}}
            <div class="pf-card bg-white rounded-xl shadow-[0_1px_3px_0_rgba(0,0,0,0.04)] border border-gray-200/80 overflow-hidden transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-6 h-6 rounded-md bg-rose-50 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0"/></svg>
                        </div>
                        <h2 class="text-sm font-extrabold text-gray-900">Personal Information</h2>
                    </div>
                    <button onclick="openPfModal()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[0.7rem] font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 transition-all">
                        View All
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </button>
                </div>
                <div class="pf-card-body p-5">
                    <div class="grid grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <div class="text-[0.6rem] font-bold uppercase tracking-[0.08em] text-gray-400 mb-1">First Name</div>
                            <div class="text-sm font-bold text-gray-900">{{ $u->first_name ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-[0.6rem] font-bold uppercase tracking-[0.08em] text-gray-400 mb-1">Last Name</div>
                            <div class="text-sm font-bold text-gray-900">{{ $u->last_name ?? '—' }}</div>
                        </div>
                        <div class="col-span-2">
                            <div class="text-[0.6rem] font-bold uppercase tracking-[0.08em] text-gray-400 mb-1">Email</div>
                            <div class="text-sm font-bold text-gray-900 break-all">{{ $u->email ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-[0.6rem] font-bold uppercase tracking-[0.08em] text-gray-400 mb-1">Phone</div>
                            <div class="text-sm font-bold text-gray-900 font-mono">{{ $u->phone ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-[0.6rem] font-bold uppercase tracking-[0.08em] text-gray-400 mb-1">Gender</div>
                            <div class="text-sm font-bold text-gray-900 capitalize">{{ $u->gender ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: Documents --}}
            <div class="pf-card bg-white rounded-xl shadow-[0_1px_3px_0_rgba(0,0,0,0.04)] border border-gray-200/80 overflow-hidden transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-6 h-6 rounded-md bg-amber-50 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                        </div>
                        <h2 class="text-sm font-extrabold text-gray-900">Documents</h2>
                    </div>
                    <a href="{{ route('employee.attachments.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[0.7rem] font-bold text-amber-600 bg-amber-50 hover:bg-amber-100 transition-all">
                        Manage
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                </div>
                <div class="pf-card-body p-5">
                    @php
                        use App\Models\EmployeeAttachment;
                        $attachmentTypes = EmployeeAttachment::attachmentTypes();
                        $existingByKey   = EmployeeAttachment::where('user_id', $u->id)
                            ->orderByDesc('created_at')->get()
                            ->groupBy('attachment_key')
                            ->map(fn($g) => $g->first());
                        $flat = $existingByKey->flatten();
                        $approvedCount = $flat->where('status','approved')->count();
                        $pendingCount  = $flat->where('status','pending')->count();
                        $rejectedCount = $flat->where('status','rejected')->count();
                        $uploadedKeys  = $existingByKey->keys()->count();
                        $totalTypes    = count($attachmentTypes);
                        $missingCount  = $totalTypes - $uploadedKeys;
                    @endphp
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-emerald-50/70 border border-emerald-100">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-[0.6rem] font-bold uppercase tracking-wide text-emerald-600">Approved</div>
                                <div class="text-lg font-extrabold text-gray-900 leading-none">{{ $approvedCount }}</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-amber-50/70 border border-amber-100">
                            <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-[0.6rem] font-bold uppercase tracking-wide text-amber-600">Pending</div>
                                <div class="text-lg font-extrabold text-gray-900 leading-none">{{ $pendingCount }}</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-rose-50/70 border border-rose-100">
                            <div class="w-8 h-8 rounded-lg bg-rose-100 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-[0.6rem] font-bold uppercase tracking-wide text-rose-600">Action Needed</div>
                                <div class="text-lg font-extrabold text-gray-900 leading-none">{{ $rejectedCount + $missingCount }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between text-[0.65rem] text-gray-400">
                        <span>{{ $uploadedKeys }}/{{ $totalTypes }} document types uploaded</span>
                        <span class="font-mono font-bold">{{ $totalTypes > 0 ? round(($uploadedKeys/$totalTypes)*100) : 0 }}%</span>
                    </div>
                </div>
            </div>

            {{-- Employment Details (only if employee has position/date_of_hire) --}}
            @if($u->position || $u->date_of_hire)
            <div class="pf-card bg-white rounded-xl shadow-[0_1px_3px_0_rgba(0,0,0,0.04)] border border-gray-200/80 overflow-hidden lg:col-span-2 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100">
                    <div class="w-6 h-6 rounded-md bg-gray-100 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z"/></svg>
                    </div>
                    <h2 class="text-sm font-extrabold text-gray-900">Employment Details</h2>
                </div>
                <div class="pf-card-body p-5">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-x-6 gap-y-4">
                        @if($u->position)
                        <div>
                            <div class="text-[0.6rem] font-bold uppercase tracking-[0.08em] text-gray-400 mb-1">Position</div>
                            <div class="text-sm font-bold text-gray-900">{{ $u->position }}</div>
                        </div>
                        @endif
                        @if($u->department)
                        <div>
                            <div class="text-[0.6rem] font-bold uppercase tracking-[0.08em] text-gray-400 mb-1">Department</div>
                            <div class="text-sm font-bold text-gray-900">{{ $u->department }}</div>
                        </div>
                        @endif
                        @if($u->date_of_hire)
                        <div>
                            <div class="text-[0.6rem] font-bold uppercase tracking-[0.08em] text-gray-400 mb-1">Date Hired</div>
                            <div class="text-sm font-bold text-gray-900">{{ $u->date_of_hire?->format('M d, Y') }}</div>
                        </div>
                        @endif
                        @if($u->salary_rate)
                        <div>
                            <div class="text-[0.6rem] font-bold uppercase tracking-[0.08em] text-gray-400 mb-1">Daily Rate</div>
                            <div class="text-sm font-bold text-gray-900 font-mono">₱{{ number_format($u->salary_rate, 2) }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

{{-- ── Personal Information Modal ── --}}
<div id="pfModal" class="fixed inset-0 z-[9999] hidden items-center justify-center p-4 bg-black/50 backdrop-blur-sm" style="display:none;">
    <div class="bg-white rounded-2xl w-full max-w-2xl max-h-[85vh] overflow-y-auto shadow-2xl animate-[pfSlide_0.3s_cubic-bezier(0.16,1,0.3,1)]" onclick="event.stopPropagation()">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-rose-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0"/></svg>
                </div>
                <h2 class="text-lg font-extrabold text-gray-900">Personal Information</h2>
            </div>
            <button onclick="closePfModal()" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="p-6 space-y-6">
            {{-- Full Name --}}
            <div>
                <div class="text-[0.6rem] font-bold uppercase tracking-[0.1em] text-gray-400 mb-3 pb-1 border-b border-gray-100">Full Name</div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <div class="text-[0.65rem] text-gray-400 mb-1">First Name</div>
                        <div class="text-sm font-bold text-gray-900">{{ $u->first_name ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-[0.65rem] text-gray-400 mb-1">Middle Name</div>
                        <div class="text-sm font-bold text-gray-900">{{ $u->middle_name ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-[0.65rem] text-gray-400 mb-1">Last Name</div>
                        <div class="text-sm font-bold text-gray-900">{{ $u->last_name ?? '—' }}</div>
                    </div>
                </div>
            </div>

            {{-- Contact --}}
            <div>
                <div class="text-[0.6rem] font-bold uppercase tracking-[0.1em] text-gray-400 mb-3 pb-1 border-b border-gray-100">Contact</div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <div class="text-[0.65rem] text-gray-400 mb-1">Email</div>
                        <div class="text-sm font-bold text-gray-900 break-all">{{ $u->email ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-[0.65rem] text-gray-400 mb-1">Phone</div>
                        <div class="text-sm font-bold text-gray-900 font-mono">{{ $u->phone ?? '—' }}</div>
                    </div>
                </div>
            </div>

            {{-- Personal Details --}}
            <div>
                <div class="text-[0.6rem] font-bold uppercase tracking-[0.1em] text-gray-400 mb-3 pb-1 border-b border-gray-100">Personal Details</div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <div class="text-[0.65rem] text-gray-400 mb-1">Gender</div>
                        <div class="text-sm font-bold text-gray-900 capitalize">{{ $u->gender ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-[0.65rem] text-gray-400 mb-1">Civil Status</div>
                        <div class="text-sm font-bold text-gray-900 capitalize">{{ str_replace('_', ' ', $u->civil_status ?? '—') }}</div>
                    </div>
                    <div>
                        <div class="text-[0.65rem] text-gray-400 mb-1">Date of Birth</div>
                        <div class="text-sm font-bold text-gray-900">{{ $u->date_of_birth?->format('M d, Y') ?? '—' }}</div>
                    </div>
                </div>
                @if(($u->civil_status ?? null) === 'married' && $u->spouse_name)
                <div class="mt-3">
                    <div class="text-[0.65rem] text-gray-400 mb-1">Spouse Name</div>
                    <div class="text-sm font-bold text-gray-900">{{ $u->spouse_name }}</div>
                </div>
                @endif
            </div>

            {{-- Birth & Education --}}
            <div>
                <div class="text-[0.6rem] font-bold uppercase tracking-[0.1em] text-gray-400 mb-3 pb-1 border-b border-gray-100">Birth & Education</div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <div class="text-[0.65rem] text-gray-400 mb-1">Place of Birth</div>
                        <div class="text-sm font-bold text-gray-900">{{ $u->place_of_birth ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-[0.65rem] text-gray-400 mb-1">Educational Attainment</div>
                        <div class="text-sm font-bold text-gray-900">{{ $u->educational_attainment ?? '—' }}</div>
                    </div>
                </div>
            </div>

            {{-- Address --}}
            <div>
                <div class="text-[0.6rem] font-bold uppercase tracking-[0.1em] text-gray-400 mb-3 pb-1 border-b border-gray-100">Address</div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <div class="text-[0.65rem] text-gray-400 mb-1">Street / House No.</div>
                        <div class="text-sm font-bold text-gray-900">{{ $u->address_street ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-[0.65rem] text-gray-400 mb-1">Barangay</div>
                        <div class="text-sm font-bold text-gray-900">{{ $u->address_barangay ?? '—' }}</div>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <div class="text-[0.65rem] text-gray-400 mb-1">City</div>
                        <div class="text-sm font-bold text-gray-900">{{ $u->address_city ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-[0.65rem] text-gray-400 mb-1">Province</div>
                        <div class="text-sm font-bold text-gray-900">{{ $u->address_province ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-[0.65rem] text-gray-400 mb-1">Postal Code</div>
                        <div class="text-sm font-bold text-gray-900">{{ $u->address_postal_code ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100">
            <button onclick="closePfModal()" class="px-4 py-2 rounded-xl text-sm font-bold text-gray-600 hover:bg-gray-100 transition-all">Close</button>
            <a href="{{ route('employee.profile.edit') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-rose-600 hover:bg-rose-700 transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897l12.62-12.62z"/></svg>
                Edit Profile
            </a>
        </div>
    </div>
</div>

<script>
function openPfModal() {
    const m = document.getElementById('pfModal');
    if (typeof sndPlay === 'function') sndPlay();
    m.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closePfModal() {
    const m = document.getElementById('pfModal');
    m.style.display = 'none';
    document.body.style.overflow = 'auto';
}
document.addEventListener('DOMContentLoaded', function() {
    const m = document.getElementById('pfModal');
    m.addEventListener('click', function(e) { if (e.target === this) closePfModal(); });
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closePfModal(); });
});

// ── Photo upload confirm ──
var _empPendingPhoto = null;

function empPreviewPhoto(input) {
    if (!input.files || !input.files[0]) return;
    if (typeof sndPlay === 'function') sndPlay();
    _empPendingPhoto = input.files[0];
    var reader = new FileReader();
    reader.onload = function(e) {
        var preview = document.getElementById('emp-photo-preview');
        var placeholder = document.getElementById('emp-photo-preview-placeholder');
        preview.src = e.target.result;
        preview.classList.remove('hidden');
        placeholder.classList.add('hidden');
        document.getElementById('emp-photo-confirm-modal').classList.remove('hidden');
    };
    reader.readAsDataURL(input.files[0]);
}

function closeEmpPhotoModal() {
    document.getElementById('emp-photo-confirm-modal').classList.add('hidden');
    document.getElementById('emp-photo-input').value = '';
    _empPendingPhoto = null;
    var preview = document.getElementById('emp-photo-preview');
    var placeholder = document.getElementById('emp-photo-preview-placeholder');
    preview.classList.add('hidden');
    placeholder.classList.remove('hidden');
}

function confirmEmpPhoto() {
    if (!_empPendingPhoto) return;
    var form = document.getElementById('emp-photo-form');
    var data = new FormData(form);
    data.set('photo', _empPendingPhoto);
    var submitBtn = document.querySelector('#emp-photo-confirm-modal button:last-child');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Uploading...';
    fetch(form.action, { method: 'POST', body: data, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(function(r) { return r.json(); })
        .then(function(d) {
            if (d.success) { if (typeof sndPlay === 'function') sndPlay(); window.location.reload(); }
            else { submitBtn.disabled = false; submitBtn.textContent = 'Set Photo'; closeEmpPhotoModal(); alert(d.message || 'Upload failed.'); }
        })
        .catch(function() {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Set Photo';
            closeEmpPhotoModal();
            window.location.reload();
        });
}
</script>

{{-- Photo Confirm Modal --}}
<div id="emp-photo-confirm-modal" class="fixed inset-0 z-[100000] flex items-center justify-center hidden">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeEmpPhotoModal()"></div>
    <div class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 p-6 max-w-sm w-full mx-4 animate-modal-in">
        <div class="text-center">
            <div class="w-20 h-20 rounded-2xl overflow-hidden mx-auto mb-4 border-2 border-gray-200 dark:border-gray-700 shadow-sm">
                <img id="emp-photo-preview" class="w-full h-full object-cover hidden">
                <div id="emp-photo-preview-placeholder" class="w-full h-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-300">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
            </div>
            <h3 class="text-base font-bold text-gray-900 dark:text-gray-100 mb-1">Set as profile photo?</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-5">This will update your profile picture across the system.</p>
            <div class="flex items-center gap-3 justify-center">
                <button type="button" onclick="closeEmpPhotoModal()" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">Cancel</button>
                <button type="button" onclick="confirmEmpPhoto()" class="px-5 py-2 bg-red-500 hover:bg-red-600 text-white rounded-xl text-xs font-bold transition-all shadow-sm">Set Photo</button>
            </div>
        </div>
    </div>
</div>

<style>
    .animate-modal-in { animation: modalPop 0.25s cubic-bezier(0.34,1.56,0.64,1) both; }
    @keyframes modalPop { 0%{opacity:0;transform:scale(0.92) translateY(8px)} 100%{opacity:1;transform:scale(1) translateY(0)} }
</style>
@endsection

@extends('layouts.layout')

@push('styles')
<style>
@keyframes ps-fade { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes ps-scale { 0%{opacity:0;transform:scale(0.93)} 100%{opacity:1;transform:scale(1)} }
@keyframes ps-slideUp { 0%{opacity:0;transform:translateY(20px)} 100%{opacity:1;transform:translateY(0)} }
.ps-h { animation:ps-fade 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.ps-stat { animation:ps-scale 0.38s cubic-bezier(0.16,1,0.3,1) both; }
.ps-stat:nth-child(1){ animation-delay:.1s; }
.ps-stat:nth-child(2){ animation-delay:.14s; }
.ps-stat:nth-child(3){ animation-delay:.18s; }
.ps-stat:nth-child(4){ animation-delay:.22s; }
.ps-card { animation:ps-fade 0.45s cubic-bezier(0.16,1,0.3,1) both; }
.ps-card:nth-child(1){ animation-delay:.2s; }
.ps-card:nth-child(2){ animation-delay:.26s; }
</style>
@endpush

@section('content')
@php
    $u = auth()->user();
    $dept = $u->department ?? '—';
    $role = ucfirst(str_replace('_', ' ', $u->role ?? 'user'));
@endphp

<div class="min-h-screen bg-gray-50/60">
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 py-8">

        {{-- Flash --}}
        @if(session('success'))
            <div class="mb-5 p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-start gap-3">
                <svg class="w-5 h-5 text-emerald-600 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                <div class="flex-1 text-sm font-semibold text-emerald-800">{{ session('success') }}</div>
            </div>
        @endif

        {{-- Hero --}}
        <div class="ps-h bg-gradient-to-br from-gray-900 via-gray-900 to-gray-800 rounded-2xl p-6 sm:p-7 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5 mb-6 relative overflow-hidden">
            <div class="absolute -top-16 -right-16 w-60 h-60 rounded-full bg-rose-600/15 pointer-events-none"></div>
            <div class="absolute -bottom-20 -left-20 w-60 h-60 rounded-full bg-blue-500/12 pointer-events-none"></div>
            <div class="flex items-center gap-4 relative z-10">
                <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center text-white font-mono font-bold text-lg border border-white/10 shadow-inner shrink-0">
                    {{ strtoupper(substr($u->first_name ?? $u->name ?? 'U', 0, 1) . substr($u->last_name ?? '', 0, 1)) }}
                </div>
                <div>
                    <h1 class="text-lg font-extrabold text-white tracking-tight">{{ $u->first_name ? ($u->first_name . ' ' . $u->last_name) : $u->name }}</h1>
                    <div class="flex items-center gap-2 mt-1 text-sm text-gray-400">
                        <span class="font-mono">{{ $u->email ?? '—' }}</span>
                        <span class="text-gray-600">·</span>
                        <span>{{ $dept }}</span>
                    </div>
                    <div class="flex flex-wrap gap-2 mt-2.5">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold text-gray-300 bg-white/10 border border-white/10">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                            Joined {{ $u->created_at?->format('M Y') ?? '—' }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold text-gray-300 bg-white/10 border border-white/10">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            {{ $role }}
                        </span>
                        @if($u->status ?? null)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold text-gray-300 bg-white/10 border border-white/10">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                {{ ucfirst($u->status) }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <a href="{{ route('settings.account') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white text-sm font-bold rounded-xl transition-all border border-white/10 shrink-0 relative z-10">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/></svg>
                Settings
            </a>
        </div>

        {{-- Stat cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
            <div class="ps-stat bg-white border border-gray-200 rounded-xl p-4 flex items-center gap-3 shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Role</div>
                    <div class="text-base font-extrabold text-gray-900 leading-none">{{ $role }}</div>
                </div>
            </div>
            <div class="ps-stat bg-white border border-gray-200 rounded-xl p-4 flex items-center gap-3 shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Department</div>
                    <div class="text-base font-extrabold text-gray-900 leading-none truncate">{{ $dept }}</div>
                </div>
            </div>
            <div class="ps-stat bg-white border border-gray-200 rounded-xl p-4 flex items-center gap-3 shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="6" y="4" width="12" height="16" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M2 8.5c0-1.933 1.567-3.5 3.5-3.5h13c1.933 0 3.5 1.567 3.5 3.5v7c0 1.933-1.567 3.5-3.5 3.5h-13C3.567 19 2 17.433 2 15.5v-7z"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Phone</div>
                    <div class="text-base font-extrabold text-gray-900 leading-none font-mono">{{ $u->phone ?? '—' }}</div>
                </div>
            </div>
            <div class="ps-stat bg-white border border-gray-200 rounded-xl p-4 flex items-center gap-3 shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Member Since</div>
                    <div class="text-base font-extrabold text-gray-900 leading-none">{{ $u->created_at?->format('M d, Y') ?? '—' }}</div>
                </div>
            </div>
        </div>

        {{-- Two-column cards --}}
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-4 items-start">

            {{-- Personal Information --}}
            <div class="ps-card bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        <span class="text-sm font-extrabold text-gray-900">Personal Information</span>
                    </div>
                    <button onclick="openPersonalInfoModal()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-gray-600 bg-gray-50 hover:bg-gray-100 border border-gray-200 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        View All
                    </button>
                </div>
                <div class="p-5">
                    <div class="grid gap-3.5">
                        <div>
                            <div class="text-[0.68rem] font-bold uppercase tracking-wide text-gray-400 mb-1">First Name</div>
                            <div class="text-sm font-bold text-gray-900">{{ $u->first_name ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-[0.68rem] font-bold uppercase tracking-wide text-gray-400 mb-1">Last Name</div>
                            <div class="text-sm font-bold text-gray-900">{{ $u->last_name ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-[0.68rem] font-bold uppercase tracking-wide text-gray-400 mb-1">Email</div>
                            <div class="text-sm font-bold text-gray-900 break-all">{{ $u->email ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-[0.68rem] font-bold uppercase tracking-wide text-gray-400 mb-1">Phone</div>
                            <div class="text-sm font-bold text-gray-900">{{ $u->phone ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Documents summary --}}
            <div class="ps-card bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        <span class="text-sm font-extrabold text-gray-900">My Documents</span>
                    </div>
                    <a href="{{ route('employee.attachments.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-gray-600 bg-gray-50 hover:bg-gray-100 border border-gray-200 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        View All
                    </a>
                </div>
                <div class="p-5">
                    @php
                        use App\Models\EmployeeAttachment;
                        $attachmentTypes = EmployeeAttachment::attachmentTypes();
                        $existingByKey   = EmployeeAttachment::where('user_id', $u->id)
                            ->orderByDesc('created_at')
                            ->get()
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
                    <div class="grid gap-3">
                        <div class="flex items-center gap-2.5 p-3 rounded-xl bg-emerald-50 border border-emerald-100">
                            <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <div class="flex-1">
                                <div class="text-[0.7rem] font-bold text-emerald-600 uppercase tracking-wide">Approved</div>
                                <div class="text-base font-extrabold text-gray-900">{{ $approvedCount }}</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2.5 p-3 rounded-xl bg-amber-50 border border-amber-100">
                            <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div class="flex-1">
                                <div class="text-[0.7rem] font-bold text-amber-600 uppercase tracking-wide">Under Review</div>
                                <div class="text-base font-extrabold text-gray-900">{{ $pendingCount }}</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2.5 p-3 rounded-xl bg-rose-50 border border-rose-100">
                            <svg class="w-4 h-4 text-rose-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4v2m0-10a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div class="flex-1">
                                <div class="text-[0.7rem] font-bold text-rose-600 uppercase tracking-wide">Action Needed</div>
                                <div class="text-base font-extrabold text-gray-900">{{ $rejectedCount + $missingCount }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

{{-- Personal Information Modal --}}
<div id="personalInfoModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);z-index:9999;align-items:center;justify-content:center;padding:16px;">
    <div style="background:#fff;border-radius:16px;width:100%;max-width:800px;max-height:85vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,0.3);animation:ps-slideUp 0.3s ease-out;position:relative;z-index:10000;">
        <div style="padding:20px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between;">
            <h2 style="font-size:1.2rem;font-weight:800;color:#111827;margin:0;">Personal Information</h2>
            <button onclick="closePersonalInfoModal()" style="background:none;border:none;cursor:pointer;padding:4px;">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div style="padding:20px;display:grid;gap:16px;">
            <div>
                <p style="font-size:0.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;margin:0 0 8px;letter-spacing:0.05em;">Full Name</p>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                    <div>
                        <p style="font-size:0.75rem;color:#9ca3af;margin:0 0 4px;">First Name</p>
                        <p style="font-size:0.95rem;font-weight:600;color:#111827;margin:0;">{{ $u->first_name ?? '—' }}</p>
                    </div>
                    <div>
                        <p style="font-size:0.75rem;color:#9ca3af;margin:0 0 4px;">Middle Name</p>
                        <p style="font-size:0.95rem;font-weight:600;color:#111827;margin:0;">{{ $u->middle_name ?? '—' }}</p>
                    </div>
                    <div>
                        <p style="font-size:0.75rem;color:#9ca3af;margin:0 0 4px;">Last Name</p>
                        <p style="font-size:0.95rem;font-weight:600;color:#111827;margin:0;">{{ $u->last_name ?? '—' }}</p>
                    </div>
                </div>
            </div>

            <div>
                <p style="font-size:0.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;margin:0 0 8px;letter-spacing:0.05em;">Contact Information</p>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <p style="font-size:0.75rem;color:#9ca3af;margin:0 0 4px;">Email</p>
                        <p style="font-size:0.95rem;font-weight:600;color:#111827;margin:0;word-break:break-all;">{{ $u->email ?? '—' }}</p>
                    </div>
                    <div>
                        <p style="font-size:0.75rem;color:#9ca3af;margin:0 0 4px;">Phone</p>
                        <p style="font-size:0.95rem;font-weight:600;color:#111827;margin:0;">{{ $u->phone ?? '—' }}</p>
                    </div>
                </div>
            </div>

            <div>
                <p style="font-size:0.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;margin:0 0 8px;letter-spacing:0.05em;">Personal Details</p>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                    <div>
                        <p style="font-size:0.75rem;color:#9ca3af;margin:0 0 4px;">Gender</p>
                        <p style="font-size:0.95rem;font-weight:600;color:#111827;margin:0;">{{ ucfirst($u->gender ?? '—') }}</p>
                    </div>
                    <div>
                        <p style="font-size:0.75rem;color:#9ca3af;margin:0 0 4px;">Civil Status</p>
                        <p style="font-size:0.95rem;font-weight:600;color:#111827;margin:0;">{{ ucfirst($u->civil_status ?? '—') }}</p>
                    </div>
                    <div>
                        <p style="font-size:0.75rem;color:#9ca3af;margin:0 0 4px;">Date of Birth</p>
                        <p style="font-size:0.95rem;font-weight:600;color:#111827;margin:0;">{{ $u->date_of_birth?->format('M d, Y') ?? '—' }}</p>
                    </div>
                </div>
                @if($u->civil_status === 'married' && $u->spouse_name)
                    <div style="margin-top:12px;">
                        <p style="font-size:0.75rem;color:#9ca3af;margin:0 0 4px;">Spouse Name</p>
                        <p style="font-size:0.95rem;font-weight:600;color:#111827;margin:0;">{{ $u->spouse_name }}</p>
                    </div>
                @endif
            </div>

            <div>
                <p style="font-size:0.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;margin:0 0 8px;letter-spacing:0.05em;">Birth & Education</p>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <p style="font-size:0.75rem;color:#9ca3af;margin:0 0 4px;">Place of Birth</p>
                        <p style="font-size:0.95rem;font-weight:600;color:#111827;margin:0;">{{ $u->place_of_birth ?? '—' }}</p>
                    </div>
                    <div>
                        <p style="font-size:0.75rem;color:#9ca3af;margin:0 0 4px;">Educational Attainment</p>
                        <p style="font-size:0.95rem;font-weight:600;color:#111827;margin:0;">{{ $u->educational_attainment ?? '—' }}</p>
                    </div>
                </div>
            </div>

            <div>
                <p style="font-size:0.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;margin:0 0 8px;letter-spacing:0.05em;">Address</p>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                    <div>
                        <p style="font-size:0.75rem;color:#9ca3af;margin:0 0 4px;">Street / House No.</p>
                        <p style="font-size:0.95rem;font-weight:600;color:#111827;margin:0;">{{ $u->address_street ?? '—' }}</p>
                    </div>
                    <div>
                        <p style="font-size:0.75rem;color:#9ca3af;margin:0 0 4px;">Barangay</p>
                        <p style="font-size:0.95rem;font-weight:600;color:#111827;margin:0;">{{ $u->address_barangay ?? '—' }}</p>
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                    <div>
                        <p style="font-size:0.75rem;color:#9ca3af;margin:0 0 4px;">City</p>
                        <p style="font-size:0.95rem;font-weight:600;color:#111827;margin:0;">{{ $u->address_city ?? '—' }}</p>
                    </div>
                    <div>
                        <p style="font-size:0.75rem;color:#9ca3af;margin:0 0 4px;">Province</p>
                        <p style="font-size:0.95rem;font-weight:600;color:#111827;margin:0;">{{ $u->address_province ?? '—' }}</p>
                    </div>
                    <div>
                        <p style="font-size:0.75rem;color:#9ca3af;margin:0 0 4px;">Postal Code</p>
                        <p style="font-size:0.95rem;font-weight:600;color:#111827;margin:0;">{{ $u->address_postal_code ?? '—' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div style="padding:16px;border-top:1px solid #f3f4f6;display:flex;gap:10px;justify-content:flex-end;">
            <a href="{{ route('employee.profile.edit') }}" style="display:inline-flex;align-items:center;justify-content:center;padding:10px 16px;background:#c8292a;color:#fff;border:none;border-radius:10px;font-weight:600;font-size:0.82rem;cursor:pointer;transition:all 0.15s;text-decoration:none;">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="margin-right:6px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                </svg>
                Edit
            </a>
        </div>
    </div>
</div>

<script>
    function openPersonalInfoModal() {
        const modal = document.getElementById('personalInfoModal');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closePersonalInfoModal() {
        const modal = document.getElementById('personalInfoModal');
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('personalInfoModal');
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closePersonalInfoModal();
            }
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePersonalInfoModal();
            }
        });
    });
</script>
@endsection

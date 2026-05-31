@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeUp {
    0%  { opacity:0; transform:translateY(12px); }
    100%{ opacity:1; transform:translateY(0); }
}
@keyframes fadeIn {
    0%  { opacity:0; }
    100%{ opacity:1; }
}
@keyframes scaleIn {
    0%  { opacity:0; transform:scale(0.95); }
    100%{ opacity:1; transform:scale(1); }
}
@keyframes slideDown {
    0%  { opacity:0; transform:translateY(-8px); }
    100%{ opacity:1; transform:translateY(0); }
}
.anim-header  { animation: fadeUp 0.45s cubic-bezier(0.16,1,0.3,1) both; }
.anim-tabs    { animation: fadeUp 0.4s cubic-bezier(0.16,1,0.3,1) 0.06s both; }
.anim-card    { animation: scaleIn 0.35s cubic-bezier(0.16,1,0.3,1) 0.1s both; }
.anim-stagger > * { opacity:0; animation: fadeUp 0.35s ease both; }
.anim-stagger > *:nth-child(1) { animation-delay:0.02s; }
.anim-stagger > *:nth-child(2) { animation-delay:0.06s; }
.anim-stagger > *:nth-child(3) { animation-delay:0.10s; }
.anim-stagger > *:nth-child(4) { animation-delay:0.14s; }
.anim-stagger > *:nth-child(5) { animation-delay:0.18s; }
.anim-stagger > *:nth-child(6) { animation-delay:0.22s; }
</style>
@endpush

@section('content')
@php
    $addr = [];
    if ($employee->address && str_starts_with(trim($employee->address), '{')) {
        $addr = json_decode($employee->address, true) ?? [];
    }
    $addressFormatted = collect([
        $addr['street'] ?? null, $addr['barangay'] ?? null,
        $addr['city'] ?? null, $addr['province'] ?? null,
    ])->filter()->implode(', ');
    if (!$addressFormatted) $addressFormatted = $employee->address ?? '—';

    $tab = request('tab', 'personal');
    $tabs = [
        'personal'   => ['label' => 'Personal Info', 'color' => 'rose',   'icon' => 'user'],
        'employment' => ['label' => 'Employment',    'color' => 'emerald','icon' => 'briefcase'],
        'documents'  => ['label' => 'Documents',     'color' => 'blue',   'icon' => 'file'],
        'leaves'     => ['label' => 'Leaves',        'color' => 'violet', 'icon' => 'calendar'],
    ];
@endphp

<div class="max-w-full space-y-6">

    {{-- ===== HEADER ===== --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-5 anim-header">
        <div class="flex items-center gap-4 min-w-0">
            <div class="relative w-16 h-16 rounded-2xl overflow-hidden border-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 flex-shrink-0 shadow-sm">
                @if($employee->photo_url)
                    <img src="{{ $employee->photo_url }}" alt="" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-300 dark:text-gray-600">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                @endif
            </div>
            <div class="min-w-0">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-gray-100 tracking-tight truncate">{{ $employee->first_name }} {{ $employee->middle_name }} {{ $employee->last_name }}</h1>
                <div class="flex items-center gap-2 mt-1 flex-wrap">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $employee->position ?? 'No Position' }}</span>
                    <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $employee->department ?? 'No Department' }}</span>
                    <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                    @if($employee->status === 'active')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>Active
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400 inline-block"></span>Inactive
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-shrink-0">
            <a href="{{ route('employees.index') }}"
               class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-200 active:scale-90" title="Back">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <a href="{{ route('employees.edit', $employee) }}"
               class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-200 active:scale-90" title="Edit">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </a>
            <form id="deleteEmployeeForm" action="{{ route('employees.destroy', $employee) }}" method="POST" class="inline">
                @csrf @method('DELETE')
                <button type="button"
                        class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-gray-400 hover:text-red-500 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-200 active:scale-90"
                        onclick="openDeleteModal()" title="Delete">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </form>
        </div>
    </div>

    {{-- ===== TABS ===== --}}
    <div class="border-b border-gray-200 dark:border-gray-700 anim-tabs">
        <nav class="flex gap-1 -mb-px overflow-x-auto scrollbar-none" role="tablist">
            @foreach($tabs as $key => $t)
                @php
                    $active = $tab === $key;
                    $colors = [
                        'rose'    => ['active' => 'border-rose-500 text-rose-700 dark:text-rose-400 bg-rose-50/60 dark:bg-rose-900/10', 'inactive' => 'border-transparent text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50/50 dark:hover:bg-gray-800/30'],
                        'emerald' => ['active' => 'border-emerald-500 text-emerald-700 dark:text-emerald-400 bg-emerald-50/60 dark:bg-emerald-900/10', 'inactive' => 'border-transparent text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50/50 dark:hover:bg-gray-800/30'],
                        'blue'    => ['active' => 'border-blue-500 text-blue-700 dark:text-blue-400 bg-blue-50/60 dark:bg-blue-900/10', 'inactive' => 'border-transparent text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50/50 dark:hover:bg-gray-800/30'],
                        'violet'  => ['active' => 'border-violet-500 text-violet-700 dark:text-violet-400 bg-violet-50/60 dark:bg-violet-900/10', 'inactive' => 'border-transparent text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50/50 dark:hover:bg-gray-800/30'],
                    ][$t['color']];
                @endphp
                <a href="{{ route('employees.show', $employee) }}?tab={{ $key }}" role="tab"
                   class="relative px-4 py-2.5 text-xs font-semibold border-b-2 transition-all duration-200 inline-flex items-center gap-1.5 whitespace-nowrap {{ $active ? $colors['active'] : $colors['inactive'] }}">
                    @if($t['icon'] === 'user')
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    @elseif($t['icon'] === 'briefcase')
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    @elseif($t['icon'] === 'file')
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    @elseif($t['icon'] === 'calendar')
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    @endif
                    {{ $t['label'] }}
                </a>
            @endforeach
        </nav>
    </div>

    {{-- ===== PERSONAL INFO TAB ===== --}}
    @if($tab === 'personal')
    <div class="space-y-5 anim-card">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm">
            <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-800">
                <h2 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                    Personal Details
                </h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-x-6 gap-y-4 anim-stagger">
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">First Name</span>
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-0.5">{{ $employee->first_name ?? '—' }}</div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Middle Name</span>
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-0.5">{{ $employee->middle_name ?? '—' }}</div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Last Name</span>
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-0.5">{{ $employee->last_name ?? '—' }}</div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Email</span>
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-0.5 break-all">{{ $employee->email ?? '—' }}</div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Phone</span>
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-0.5 font-mono">+63{{ $employee->phone ?? '—' }}</div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Gender</span>
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-0.5">{{ $employee->gender ? ucwords(str_replace('_', ' ', $employee->gender)) : '—' }}</div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Civil Status</span>
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-0.5">{{ ucfirst($employee->civil_status ?? '—') }}</div>
                    </div>
                    @if($employee->civil_status === 'married')
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Spouse</span>
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-0.5">{{ $employee->spouse_name ?? '—' }}</div>
                    </div>
                    @endif
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Date of Birth</span>
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-0.5">{{ $employee->date_of_birth?->format('F d, Y') ?? '—' }}</div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Place of Birth</span>
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-0.5">{{ $employee->place_of_birth ?? '—' }}</div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Education</span>
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-0.5">{{ $employee->educational_attainment ?? '—' }}</div>
                    </div>
                </div>

                {{-- Address --}}
                <div class="mt-5 pt-4 border-t border-gray-100 dark:border-gray-800">
                    <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest block mb-2.5">Address</span>
                    @if(!empty($addr))
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3">
                            <div>
                                <span class="text-[10px] font-medium text-gray-400 dark:text-gray-500">Street / House No.</span>
                                <div class="text-sm text-gray-900 dark:text-gray-100 mt-0.5">{{ $addr['street'] ?: '—' }}</div>
                            </div>
                            <div>
                                <span class="text-[10px] font-medium text-gray-400 dark:text-gray-500">Barangay</span>
                                <div class="text-sm text-gray-900 dark:text-gray-100 mt-0.5">{{ $addr['barangay'] ?: '—' }}</div>
                            </div>
                            <div>
                                <span class="text-[10px] font-medium text-gray-400 dark:text-gray-500">City / Municipality</span>
                                <div class="text-sm text-gray-900 dark:text-gray-100 mt-0.5">{{ $addr['city'] ?: '—' }}</div>
                            </div>
                            <div>
                                <span class="text-[10px] font-medium text-gray-400 dark:text-gray-500">Province</span>
                                <div class="text-sm text-gray-900 dark:text-gray-100 mt-0.5">{{ $addr['province'] ?: '—' }}</div>
                            </div>
                        </div>
                    @else
                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ $addressFormatted }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm">
            <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-800">
                <h2 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                    Account
                </h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-x-6 gap-y-4">
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Username</span>
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-0.5 font-mono">{{ $employee->username ?? '—' }}</div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Role</span>
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-0.5 capitalize">{{ $employee->role ?? '—' }}</div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Status</span>
                        <div class="mt-1">
                            @if($employee->status === 'active')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>Active
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400 inline-block"></span>Inactive
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ===== EMPLOYMENT TAB ===== --}}
    @if($tab === 'employment')
    <div class="space-y-5 anim-card">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm">
            <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-800">
                <h2 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    Employment Details
                </h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-x-6 gap-y-4 anim-stagger">
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Date of Hire</span>
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-0.5">{{ $employee->date_of_hire?->format('F d, Y') ?? '—' }}</div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Position</span>
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-0.5">{{ $employee->position ?? '—' }}</div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Department</span>
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-0.5 capitalize">{{ $employee->department ?? '—' }}</div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Daily Rate</span>
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-0.5 font-mono">₱{{ number_format($employee->salary_rate, 2) }}</div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Driver's License</span>
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-0.5 font-mono">{{ $employee->driver_license_number ?? '—' }}</div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">License Validity</span>
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-0.5">{{ $employee->driver_license_validity?->format('F d, Y') ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm">
            <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-800">
                <h2 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    Government Numbers
                </h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 anim-stagger">
                    @foreach([
                        ['label' => 'SSS', 'value' => $employee->sss_number, 'has' => $employee->has_sss],
                        ['label' => 'TIN', 'value' => $employee->tin_number, 'has' => $employee->has_tin],
                        ['label' => 'Pag-IBIG', 'value' => $employee->pagibig_number, 'has' => $employee->has_pagibig],
                        ['label' => 'PhilHealth', 'value' => $employee->philhealth_number, 'has' => $employee->has_philhealth],
                    ] as $g)
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">{{ $g['label'] }} Number</span>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100 font-mono">{{ $g['value'] ?? '—' }}</span>
                            @if($g['has'])
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 uppercase tracking-wider">
                                    Enrolled
                                </span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Work Experience --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm">
            <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-800">
                <h2 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    Work Experience
                </h2>
            </div>
            <div class="p-0">
                @if($employee->workExperiences->count())
                    <div class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($employee->workExperiences as $we)
                            <div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors duration-150">
                                <div>
                                    <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $we->company_name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $we->position }}</div>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <div class="text-xs font-mono text-gray-400 dark:text-gray-500">{{ $we->duration }}</div>
                                </div>
                            </div>
                            @if($we->responsibilities)
                                <div class="px-5 pb-3.5 -mt-2">
                                    <div class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">{{ $we->responsibilities }}</div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="px-5 py-10 text-center text-sm text-gray-400 dark:text-gray-500">No work experience records yet.</div>
                @endif
            </div>
        </div>

        {{-- Special Skills --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm">
            <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-800">
                <h2 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    Special Skills
                </h2>
            </div>
            <div class="p-0">
                @if($employee->specialSkills->count())
                    <div class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($employee->specialSkills as $skill)
                            @php
                                $prof = strtolower($skill->proficiency);
                                $cls = match($prof) {
                                    'beginner'     => 'bg-sky-50 dark:bg-sky-900/30 text-sky-700 dark:text-sky-400 border-sky-200 dark:border-sky-800',
                                    'intermediate' => 'bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-800',
                                    'advanced'     => 'bg-violet-50 dark:bg-violet-900/30 text-violet-700 dark:text-violet-400 border-violet-200 dark:border-violet-800',
                                    'expert'       => 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800',
                                    default        => 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-700',
                                };
                            @endphp
                            <div class="px-5 py-3.5 flex items-center justify-between hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors duration-150">
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $skill->skill_name }}</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border uppercase tracking-wider {{ $cls }}">{{ ucfirst($skill->proficiency) }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="px-5 py-10 text-center text-sm text-gray-400 dark:text-gray-500">No special skills added yet.</div>
                @endif
            </div>
        </div>

        {{-- Beneficiaries --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm">
            <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-800">
                <h2 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    Beneficiaries
                </h2>
            </div>
            <div class="p-0">
                @if($employee->beneficiaries->count())
                    <div class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($employee->beneficiaries as $b)
                            <div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors duration-150">
                                <div>
                                    <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $b->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 capitalize">{{ $b->relationship }}</div>
                                </div>
                                @if($b->date_of_birth)
                                    <div class="text-xs font-mono text-gray-400 dark:text-gray-500">{{ \Carbon\Carbon::parse($b->date_of_birth)->format('F d, Y') }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="px-5 py-10 text-center text-sm text-gray-400 dark:text-gray-500">No beneficiaries recorded yet.</div>
                @endif
            </div>
        </div>

        {{-- Character References --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm">
            <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-800">
                <h2 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    Character References
                </h2>
            </div>
            <div class="p-0">
                @if($employee->charRefs->count())
                    <div class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($employee->charRefs as $ref)
                            <div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors duration-150">
                                <div>
                                    <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $ref->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $ref->address }}</div>
                                </div>
                                <div class="text-xs font-mono font-semibold text-gray-700 dark:text-gray-300">{{ $ref->contact_number }}</div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="px-5 py-10 text-center text-sm text-gray-400 dark:text-gray-500">No character references added yet.</div>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- ===== DOCUMENTS TAB ===== --}}
    @if($tab === 'documents')
    @php
        $attachmentTypes  = \App\Models\EmployeeAttachment::attachmentTypes();
        $attachmentsByKey = $employee->employeeAttachments
            ->groupBy('attachment_key')
            ->map(fn($g) => $g->first());
        $approvedCount = $attachmentsByKey->where('status', 'approved')->count();
        $pendingCount  = $attachmentsByKey->where('status', 'pending')->count();
        $rejectedCount = $attachmentsByKey->where('status', 'rejected')->count();
        $missingCount  = count($attachmentTypes) - $attachmentsByKey->count();
    @endphp
    <div class="space-y-5 anim-card">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm">
            <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                <h2 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                    Attachments
                </h2>
                <a href="{{ route('employees.attachments.index', $employee) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-gray-300 dark:hover:border-gray-600 transition-all duration-150">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                    Manage
                </a>
            </div>
            <div class="p-5">
                <div class="flex items-center gap-2 flex-wrap mb-5">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold border bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800 uppercase tracking-wider">{{ $approvedCount }} Approved</span>
                    @if($pendingCount)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold border bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-800 uppercase tracking-wider">{{ $pendingCount }} Pending</span>
                    @endif
                    @if($rejectedCount)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold border bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400 border-red-200 dark:border-red-800 uppercase tracking-wider">{{ $rejectedCount }} Rejected</span>
                    @endif
                    @if($missingCount)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold border bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 border-gray-200 dark:border-gray-700 uppercase tracking-wider">{{ $missingCount }} Missing</span>
                    @endif
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 anim-stagger">
                    @foreach($attachmentTypes as $key => $label)
                        @php
                            $att = $attachmentsByKey->get($key);
                            $tileCls = match($att?->status) {
                                'approved' => 'bg-white dark:bg-gray-800 border-2 border-emerald-500/60 dark:border-emerald-600/60',
                                'pending'  => 'bg-white dark:bg-gray-800 border-2 border-amber-500/60 dark:border-amber-600/60',
                                'rejected' => 'bg-white dark:bg-gray-800 border-2 border-red-500/60 dark:border-red-600/60',
                                default    => 'bg-gray-50 dark:bg-gray-800/50 border border-dashed border-gray-200 dark:border-gray-700 opacity-70 hover:opacity-100',
                            };
                            $tagCls = match($att?->status) {
                                'approved' => 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800',
                                'pending'  => 'bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-800',
                                'rejected' => 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400 border-red-200 dark:border-red-800',
                                default    => 'bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500 border-gray-200 dark:border-gray-700',
                            };
                            $tagLabel = $att ? ucfirst($att->status) : 'Missing';
                        @endphp
                        <div class="rounded-xl p-3.5 text-center flex flex-col items-center justify-between gap-2.5 min-h-[130px] transition-all duration-300 hover:shadow-md {{ $tileCls }}">
                            @if($att)
                                @if(in_array($att->mime_type, ['image/jpeg','image/png','image/gif','image/webp']))
                                    <a href="{{ $att->url }}" target="_blank" class="w-full flex justify-center overflow-hidden rounded-lg">
                                        <img src="{{ $att->url }}" alt="{{ $label }}" class="h-12 w-full object-cover rounded-lg hover:scale-105 transition-transform duration-300">
                                    </a>
                                @else
                                    <a href="{{ $att->url }}" target="_blank" class="w-9 h-9 flex items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </a>
                                @endif
                            @else
                                <div class="w-9 h-9 flex items-center justify-center rounded-lg bg-gray-100/50 dark:bg-gray-800/50 text-gray-400 dark:text-gray-500">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                </div>
                            @endif
                            <div class="text-[11px] font-bold text-gray-700 dark:text-gray-300 leading-tight line-clamp-2">{{ $label }}</div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider border {{ $tagCls }}">{{ $tagLabel }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ===== LEAVES TAB ===== --}}
    @if($tab === 'leaves')
    <div class="space-y-5 anim-card">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm">
            <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-800">
                <h2 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-violet-400"></span>
                    Leave Balance
                </h2>
            </div>
            <div class="p-0">
                @if($employee->leaveBalances->count())
                    <div class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($employee->leaveBalances as $balance)
                            <div class="px-5 py-3.5 grid grid-cols-4 gap-4 items-center hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors duration-150">
                                <div class="col-span-1">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $balance->leaveType->name ?? '—' }}</span>
                                </div>
                                <div class="text-center">
                                    <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider block">Total</span>
                                    <span class="text-sm font-mono text-gray-700 dark:text-gray-300">{{ $balance->total_days }}</span>
                                </div>
                                <div class="text-center">
                                    <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider block">Used</span>
                                    <span class="text-sm font-mono font-bold text-red-500">{{ $balance->used_days }}</span>
                                </div>
                                <div class="text-center">
                                    <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider block">Remaining</span>
                                    <span class="text-sm font-mono font-bold text-emerald-500">{{ $balance->remaining_days }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="px-5 py-10 text-center text-sm text-gray-400 dark:text-gray-500">No leave balance records found.</div>
                @endif
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm">
            <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-800">
                <h2 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-violet-400"></span>
                    Recent Leave Requests
                </h2>
            </div>
            <div class="p-0">
                @php
                    $leaves = $employee->leaves()->orderBy('created_at', 'desc')->limit(10)->get();
                    $statusColors = [
                        'pending'   => 'bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-800',
                        'approved'  => 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800',
                        'rejected'  => 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400 border-red-200 dark:border-red-800',
                        'cancelled' => 'bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 border-gray-200 dark:border-gray-700',
                    ];
                @endphp
                @if($leaves->count())
                    <div class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($leaves as $leave)
                            @php $sc = $statusColors[strtolower($leave->status)] ?? $statusColors['cancelled']; @endphp
                            <div class="px-5 py-3.5 grid grid-cols-4 gap-4 items-center cursor-pointer hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors duration-150" onclick="openLeaveModal({{ $leave->id }})">
                                <div class="col-span-1">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $leave->leave_type ?? '—' }}</span>
                                </div>
                                <div class="text-center">
                                    <span class="text-xs font-mono text-gray-500 dark:text-gray-400">{{ $leave->created_at?->format('M d, Y') ?? '—' }}</span>
                                </div>
                                <div class="text-center">
                                    <span class="text-sm font-mono font-semibold text-gray-800 dark:text-gray-200">{{ $leave->days ?? '—' }}</span>
                                </div>
                                <div class="text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border uppercase tracking-wider {{ $sc }}">{{ ucfirst($leave->status) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="px-5 py-10 text-center text-sm text-gray-400 dark:text-gray-500">No leave requests found.</div>
                @endif
            </div>
        </div>
    </div>
    @endif

</div>

{{-- Delete Confirmation Modal --}}
<div id="deleteModal" class="fixed inset-0 z-[10000] hidden items-center justify-center p-4 bg-gray-900/50 dark:bg-gray-950/70 backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-2xl max-w-sm w-full border border-gray-200 dark:border-gray-700 scale-95 transition-transform duration-300" id="deleteModalContainer">
        <div class="w-11 h-11 rounded-full bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center mb-3.5 border border-red-100 dark:border-red-800">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </div>
        <h3 class="text-base font-bold text-gray-900 dark:text-gray-100 mb-1.5">Delete Employee</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-5 leading-relaxed">Are you sure you want to delete this employee? This action cannot be undone.</p>
        <div class="flex items-center justify-end gap-2.5">
            <button type="button" class="px-4 py-2 text-sm font-semibold text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl hover:border-gray-300 dark:hover:border-gray-600 hover:text-gray-900 dark:hover:text-gray-100 transition-all duration-150" onclick="closeDeleteModal()">Cancel</button>
            <button type="button" class="px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-xl hover:bg-red-700 shadow-lg shadow-red-600/10 active:scale-[0.98] transition-all duration-150" onclick="confirmDelete()">Delete</button>
        </div>
    </div>
</div>

{{-- Leave Request Details Modal --}}
<div id="leaveDetailModal" class="fixed inset-0 z-[10000] hidden items-center justify-center p-4 bg-gray-900/50 dark:bg-gray-950/70 backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full border border-gray-200 dark:border-gray-700 overflow-hidden scale-95 transition-transform duration-300" id="leaveDetailModalContainer">
        <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100">Leave Request Details</h3>
            <button onclick="closeLeaveModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div id="leaveDetailContent" class="px-5 py-4 max-h-[60vh] overflow-y-auto space-y-3">
        </div>
        <div class="px-5 py-3.5 border-t border-gray-100 dark:border-gray-800 flex justify-end">
            <button type="button" class="px-4 py-2 text-sm font-semibold text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl hover:border-gray-300 dark:hover:border-gray-600 hover:text-gray-900 dark:hover:text-gray-100 transition-all duration-150" onclick="closeLeaveModal()">Close</button>
        </div>
    </div>
</div>

<script>
function openDeleteModal() {
    document.getElementById('deleteModal').classList.remove('hidden');
    document.getElementById('deleteModal').classList.add('flex');
}
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('flex');
    document.getElementById('deleteModal').classList.add('hidden');
}
function confirmDelete() {
    document.getElementById('deleteEmployeeForm').submit();
}
function openLeaveModal(leaveId) {
    const modal = document.getElementById('leaveDetailModal');
    const content = document.getElementById('leaveDetailContent');
    content.innerHTML = '<div class="flex items-center justify-center py-10"><div class="w-5 h-5 border-2 border-gray-300 dark:border-gray-600 border-t-gray-900 dark:border-t-gray-100 rounded-full animate-spin"></div></div>';
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    fetch(`/api/leaves/${leaveId}`)
        .then(r => r.json())
        .then(d => {
            const leave = d.data;
            const colors = {
                'pending':   ['bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-800', 'Pending'],
                'approved':  ['bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800', 'Approved'],
                'rejected':  ['bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400 border-red-200 dark:border-red-800', 'Rejected'],
                'cancelled': ['bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 border-gray-200 dark:border-gray-700', 'Cancelled']
            };
            const sc = colors[leave.status] || colors['cancelled'];
            content.innerHTML = `
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="p-3 bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700 rounded-xl">
                        <div class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Employee</div>
                        <div class="text-sm font-semibold text-gray-900 dark:text-gray-100 mt-0.5">${leave.employee_name}</div>
                    </div>
                    <div class="p-3 bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700 rounded-xl">
                        <div class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Leave Type</div>
                        <div class="text-sm font-semibold text-gray-900 dark:text-gray-100 mt-0.5">${leave.leave_type_name}</div>
                    </div>
                    <div class="p-3 bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700 rounded-xl">
                        <div class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">From</div>
                        <div class="text-sm font-semibold text-gray-900 dark:text-gray-100 mt-0.5">${new Date(leave.from_date).toLocaleDateString('en-US', {year:'numeric',month:'long',day:'numeric'})}</div>
                    </div>
                    <div class="p-3 bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700 rounded-xl">
                        <div class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">To</div>
                        <div class="text-sm font-semibold text-gray-900 dark:text-gray-100 mt-0.5">${new Date(leave.to_date).toLocaleDateString('en-US', {year:'numeric',month:'long',day:'numeric'})}</div>
                    </div>
                    <div class="p-3 bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700 rounded-xl">
                        <div class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Days</div>
                        <div class="text-sm font-semibold text-gray-900 dark:text-gray-100 mt-0.5 font-mono">${leave.number_of_days} day(s)</div>
                    </div>
                    <div class="p-3 bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700 rounded-xl">
                        <div class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Status</div>
                        <div class="mt-1"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border ${sc[0]}">${sc[1]}</span></div>
                    </div>
                    ${leave.reason ? `<div class="p-3 bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700 rounded-xl sm:col-span-2"><div class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Reason</div><div class="text-sm text-gray-700 dark:text-gray-300 mt-0.5 leading-relaxed">${leave.reason}</div></div>` : ''}
                    ${leave.approved_by ? `<div class="p-3 bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700 rounded-xl sm:col-span-2"><div class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Approved By</div><div class="text-sm text-gray-700 dark:text-gray-300 mt-0.5">${leave.approved_by}</div></div>` : ''}
                    ${leave.rejection_reason ? `<div class="p-3 bg-red-50/20 dark:bg-red-900/10 border border-red-100 dark:border-red-800 rounded-xl sm:col-span-2"><div class="text-[10px] font-bold text-red-500 dark:text-red-400 uppercase tracking-widest">Rejection Reason</div><div class="text-sm text-red-700 dark:text-red-400 mt-0.5 leading-relaxed">${leave.rejection_reason}</div></div>` : ''}
                </div>
            `;
        })
        .catch(() => {
            content.innerHTML = '<div class="flex flex-col items-center justify-center py-8 text-center"><div class="w-10 h-10 rounded-full bg-red-50 dark:bg-red-900/30 border border-red-100 dark:border-red-800 text-red-500 dark:text-red-400 flex items-center justify-center mb-3"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></div><p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Failed to load details</p><p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Please try again later.</p></div>';
        });
}
function closeLeaveModal() {
    document.getElementById('leaveDetailModal').classList.remove('flex');
    document.getElementById('leaveDetailModal').classList.add('hidden');
}
document.addEventListener('DOMContentLoaded', function() {
    const del = document.getElementById('deleteModal');
    const lev = document.getElementById('leaveDetailModal');
    if (del) del.addEventListener('click', e => { if (e.target === del) closeDeleteModal(); });
    if (lev) lev.addEventListener('click', e => { if (e.target === lev) closeLeaveModal(); });
});
</script>
@endsection

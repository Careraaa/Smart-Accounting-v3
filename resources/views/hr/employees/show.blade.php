@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeSlideUp {
    0%  { opacity:0; transform:translateY(14px); }
    100%{ opacity:1; transform:translateY(0); }
}
@keyframes scaleIn {
    0%  { opacity:0; transform:scale(0.96); }
    100%{ opacity:1; transform:scale(1); }
}
.emp-header-in { animation: fadeSlideUp 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.emp-tabs-in   { animation: fadeSlideUp 0.4s cubic-bezier(0.16,1,0.3,1) 0.05s both; }
.emp-card-in   { animation: scaleIn     0.35s cubic-bezier(0.16,1,0.3,1) 0.1s both; }
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
@endphp

<div class="max-w-full">

    {{-- Topbar --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 emp-header-in">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">{{ $employee->first_name }} {{ $employee->middle_name }} {{ $employee->last_name }}</h1>
            <div class="flex items-center gap-2.5 mt-1.5 flex-wrap">
                <span class="text-sm font-semibold text-gray-500">{{ $employee->position ?? 'No Position Specified' }}</span>
                <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                <span class="text-sm font-semibold text-gray-500">{{ $employee->department ?? 'No Department' }}</span>
                <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                @if($employee->status === 'active')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>Active
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-500 border border-gray-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400 inline-block"></span>Inactive
                    </span>
                @endif
            </div>
        </div>
        
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('employees.index') }}" 
               class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:border-gray-300 hover:text-gray-900 hover:shadow-sm active:scale-[0.97] transition-all duration-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back
            </a>
            <a href="{{ route('employees.edit', $employee) }}" 
               class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded-xl hover:bg-gray-800 hover:shadow-lg hover:shadow-gray-900/20 active:scale-[0.97] transition-all duration-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit
            </a>
            <form id="deleteEmployeeForm" action="{{ route('employees.destroy', $employee) }}" method="POST" class="inline">
                @csrf @method('DELETE')
                <button type="button" 
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-semibold text-red-600 bg-white border border-red-200 rounded-xl hover:bg-red-50 hover:border-red-300 hover:shadow-sm active:scale-[0.97] transition-all duration-200" 
                        onclick="openDeleteModal()">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Delete
                </button>
            </form>
        </div>
    </div>

    {{-- Tab bar --}}
    <div class="flex items-center gap-1.5 bg-white border border-gray-200 rounded-xl p-1 mb-6 w-max max-w-full overflow-x-auto emp-tabs-in">
        <a href="{{ route('employees.show', $employee) }}?tab=personal"
           class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-semibold rounded-lg transition-all duration-150 whitespace-nowrap {{ $tab === 'personal' ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            Personal Info
        </a>
        <a href="{{ route('employees.show', $employee) }}?tab=employment"
           class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-semibold rounded-lg transition-all duration-150 whitespace-nowrap {{ $tab === 'employment' ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            Employment
        </a>
        <a href="{{ route('employees.show', $employee) }}?tab=documents"
           class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-semibold rounded-lg transition-all duration-150 whitespace-nowrap {{ $tab === 'documents' ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Documents
        </a>
        <a href="{{ route('employees.show', $employee) }}?tab=leaves"
           class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-semibold rounded-lg transition-all duration-150 whitespace-nowrap {{ $tab === 'leaves' ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Leaves
        </a>
    </div>

    {{-- PERSONAL INFO TAB --}}
    @if($tab === 'personal')
    <div class="space-y-6 emp-card-in">
        {{-- Personal Information --}}
        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">
            <div class="px-6 py-4.5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <h2 class="text-sm font-bold text-gray-950 flex items-center gap-2.5">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span> 
                    Personal Information
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-y-5 gap-x-6">
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">First Name</span>
                        <div class="text-sm font-medium text-gray-900">{{ $employee->first_name ?? '—' }}</div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Middle Name</span>
                        <div class="text-sm font-medium text-gray-900">{{ $employee->middle_name ?? '—' }}</div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Last Name</span>
                        <div class="text-sm font-medium text-gray-900">{{ $employee->last_name ?? '—' }}</div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Email</span>
                        <div class="text-sm font-medium text-gray-900 break-all">{{ $employee->email ?? '—' }}</div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Phone</span>
                        <div class="text-sm font-mono font-semibold text-gray-800 bg-gray-50 border border-gray-100 rounded px-1.5 py-0.5 w-max max-w-full">{{ $employee->phone ?? '—' }}</div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Gender</span>
                        <div class="text-sm font-medium text-gray-900">{{ $employee->gender ? ucwords(str_replace('_', ' ', $employee->gender)) : '—' }}</div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Civil Status</span>
                        <div class="text-sm font-medium text-gray-900">{{ ucfirst($employee->civil_status ?? '—') }}</div>
                    </div>
                    @if($employee->civil_status === 'married')
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Spouse Name</span>
                        <div class="text-sm font-medium text-gray-900">{{ $employee->spouse_name ?? '—' }}</div>
                    </div>
                    @endif
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Date of Birth</span>
                        <div class="text-sm font-medium text-gray-900">{{ $employee->date_of_birth?->format('F d, Y') ?? '—' }}</div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Place of Birth</span>
                        <div class="text-sm font-medium text-gray-900">{{ $employee->place_of_birth ?? '—' }}</div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Educational Attainment</span>
                        <div class="text-sm font-medium text-gray-900">{{ $employee->educational_attainment ?? '—' }}</div>
                    </div>
                    
                    {{-- Address Subdivider --}}
                    <div class="col-span-full border-b border-gray-100 pb-2 mb-1 mt-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Address</div>
                    
                    @if(!empty($addr))
                        <div class="col-span-full grid grid-cols-1 sm:grid-cols-2 gap-y-5 gap-x-6">
                            <div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Street / House No.</span>
                                <div class="text-sm font-medium text-gray-900">{{ $addr['street'] ?: '—' }}</div>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Barangay</span>
                                <div class="text-sm font-medium text-gray-900">{{ $addr['barangay'] ?: '—' }}</div>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">City / Municipality</span>
                                <div class="text-sm font-medium text-gray-900">{{ $addr['city'] ?: '—' }}</div>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Province</span>
                                <div class="text-sm font-medium text-gray-900">{{ $addr['province'] ?: '—' }}</div>
                            </div>
                        </div>
                    @else
                        <div class="col-span-full">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Address</span>
                            <div class="text-sm font-medium text-gray-900">{{ $addressFormatted }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Account Information --}}
        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">
            <div class="px-6 py-4.5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <h2 class="text-sm font-bold text-gray-950 flex items-center gap-2.5">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span> 
                    Account Information
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-y-5 gap-x-6">
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Username</span>
                        <div class="text-sm font-mono font-semibold text-gray-800 bg-gray-50 border border-gray-100 rounded px-1.5 py-0.5 w-max max-w-full">{{ $employee->username ?? '—' }}</div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Role</span>
                        <div class="text-sm font-medium text-gray-900">{{ ucfirst($employee->role ?? '—') }}</div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Account Status</span>
                        <div class="mt-1">
                            @if($employee->status === 'active')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>Active
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-500 border border-gray-200">
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

    {{-- EMPLOYMENT TAB --}}
    @if($tab === 'employment')
    <div class="space-y-6 emp-card-in">
        {{-- Employment Information --}}
        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">
            <div class="px-6 py-4.5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <h2 class="text-sm font-bold text-gray-950 flex items-center gap-2.5">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span> 
                    Employment Information
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-y-5 gap-x-6">
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Date of Hire</span>
                        <div class="text-sm font-medium text-gray-900">{{ $employee->date_of_hire?->format('F d, Y') ?? '—' }}</div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Position</span>
                        <div class="text-sm font-medium text-gray-900">{{ $employee->position ?? '—' }}</div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Department</span>
                        <div class="text-sm font-medium text-gray-900">{{ ucfirst($employee->department ?? '—') }}</div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Daily Rate</span>
                        <div class="text-sm font-mono font-semibold text-gray-800 bg-gray-50 border border-gray-100 rounded px-1.5 py-0.5 w-max max-w-full">₱{{ number_format($employee->salary_rate, 2) }}</div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Driver's License</span>
                        <div class="text-sm font-mono font-semibold text-gray-800 bg-gray-50 border border-gray-100 rounded px-1.5 py-0.5 w-max max-w-full">{{ $employee->driver_license_number ?? '—' }}</div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">License Validity</span>
                        <div class="text-sm font-medium text-gray-900">{{ $employee->driver_license_validity?->format('F d, Y') ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Government Numbers --}}
        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">
            <div class="px-6 py-4.5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <h2 class="text-sm font-bold text-gray-950 flex items-center gap-2.5">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span> 
                    Government Numbers
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-5 gap-x-6">
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">SSS Number</span>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-sm font-mono font-semibold text-gray-800 bg-gray-50 border border-gray-100 rounded px-1.5 py-0.5">{{ $employee->sss_number ?? '—' }}</span>
                            @if($employee->has_sss)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    Enrolled
                                </span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">TIN Number</span>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-sm font-mono font-semibold text-gray-800 bg-gray-50 border border-gray-100 rounded px-1.5 py-0.5">{{ $employee->tin_number ?? '—' }}</span>
                            @if($employee->has_tin)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    Has TIN
                                </span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Pag-IBIG Number</span>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-sm font-mono font-semibold text-gray-800 bg-gray-50 border border-gray-100 rounded px-1.5 py-0.5">{{ $employee->pagibig_number ?? '—' }}</span>
                            @if($employee->has_pagibig)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    Enrolled
                                </span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">PhilHealth Number</span>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-sm font-mono font-semibold text-gray-800 bg-gray-50 border border-gray-100 rounded px-1.5 py-0.5">{{ $employee->philhealth_number ?? '—' }}</span>
                            @if($employee->has_philhealth)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    Enrolled
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Work Experience --}}
        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">
            <div class="px-6 py-4.5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <h2 class="text-sm font-bold text-gray-950 flex items-center gap-2.5">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span> 
                    Work Experience
                </h2>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto border border-gray-100 rounded-xl">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="px-5 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Company</th>
                                <th class="px-5 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Position</th>
                                <th class="px-5 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Duration</th>
                                <th class="px-5 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Responsibilities</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($employee->workExperiences as $we)
                                <tr class="hover:bg-gray-50/50 transition-colors duration-150">
                                    <td class="px-5 py-3.5 font-semibold text-gray-900">{{ $we->company_name }}</td>
                                    <td class="px-5 py-3.5 text-gray-600">{{ $we->position }}</td>
                                    <td class="px-5 py-3.5 font-mono text-xs text-gray-500">{{ $we->duration }}</td>
                                    <td class="px-5 py-3.5 text-xs text-gray-500 leading-relaxed">{{ $we->responsibilities }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-8 text-center text-gray-400 text-sm bg-gray-50/20">
                                        No work experience records yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Special Skills --}}
        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">
            <div class="px-6 py-4.5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <h2 class="text-sm font-bold text-gray-950 flex items-center gap-2.5">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span> 
                    Special Skills
                </h2>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto border border-gray-100 rounded-xl max-w-2xl">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="px-5 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Skill</th>
                                <th class="px-5 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Proficiency</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($employee->specialSkills as $skill)
                                <tr class="hover:bg-gray-50/50 transition-colors duration-150">
                                    <td class="px-5 py-3.5 font-semibold text-gray-900">{{ $skill->skill_name }}</td>
                                    <td class="px-5 py-3.5">
                                        @php
                                            $prof = strtolower($skill->proficiency);
                                            $badgeCls = match($prof) {
                                                'beginner'     => 'bg-sky-50 text-sky-700 border-sky-200',
                                                'intermediate' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                'advanced'     => 'bg-violet-50 text-violet-700 border-violet-200',
                                                'expert'       => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                default        => 'bg-gray-100 text-gray-700 border-gray-200',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $badgeCls }} uppercase tracking-wider text-[10px]">
                                            {{ ucfirst($skill->proficiency) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-5 py-8 text-center text-gray-400 text-sm bg-gray-50/20">
                                        No special skills added yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Beneficiaries --}}
        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">
            <div class="px-6 py-4.5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <h2 class="text-sm font-bold text-gray-950 flex items-center gap-2.5">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span> 
                    Beneficiaries
                </h2>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto border border-gray-100 rounded-xl">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="px-5 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-5 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Relationship</th>
                                <th class="px-5 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Date of Birth</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($employee->beneficiaries as $b)
                                <tr class="hover:bg-gray-50/50 transition-colors duration-150">
                                    <td class="px-5 py-3.5 font-semibold text-gray-900">{{ $b->name }}</td>
                                    <td class="px-5 py-3.5 text-gray-600">{{ ucfirst($b->relationship) }}</td>
                                    <td class="px-5 py-3.5 font-mono text-xs text-gray-500">
                                        {{ $b->date_of_birth ? \Carbon\Carbon::parse($b->date_of_birth)->format('F d, Y') : '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-5 py-8 text-center text-gray-400 text-sm bg-gray-50/20">
                                        No beneficiaries recorded yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Character References --}}
        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">
            <div class="px-6 py-4.5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <h2 class="text-sm font-bold text-gray-950 flex items-center gap-2.5">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span> 
                    Character References
                </h2>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto border border-gray-100 rounded-xl">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="px-5 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-5 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Address</th>
                                <th class="px-5 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Contact Number</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($employee->charRefs as $ref)
                                <tr class="hover:bg-gray-50/50 transition-colors duration-150">
                                    <td class="px-5 py-3.5 font-semibold text-gray-900">{{ $ref->name }}</td>
                                    <td class="px-5 py-3.5 text-gray-600 text-xs leading-relaxed">{{ $ref->address }}</td>
                                    <td class="px-5 py-3.5 font-mono text-xs text-gray-800 font-semibold">{{ $ref->contact_number }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-5 py-8 text-center text-gray-400 text-sm bg-gray-50/20">
                                        No character references added yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- DOCUMENTS TAB --}}
    @if($tab === 'documents')
    <div class="space-y-6 emp-card-in">
        {{-- Attachments --}}
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
        
        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">
            <div class="px-6 py-4.5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <h2 class="text-sm font-bold text-gray-950 flex items-center gap-2.5">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span> 
                    Attachments
                </h2>
                <a href="{{ route('employees.attachments.index', $employee) }}" 
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-gray-600 bg-white border border-gray-200 rounded-lg hover:border-gray-300 hover:text-gray-900 transition-all duration-150">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                    Manage
                </a>
            </div>
            <div class="p-6">
                <div class="flex items-center gap-2 flex-wrap mb-6">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold border bg-emerald-50 text-emerald-700 border-emerald-200 uppercase tracking-wider">{{ $approvedCount }} Approved</span>
                    @if($pendingCount)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold border bg-amber-50 text-amber-700 border-amber-200 uppercase tracking-wider">{{ $pendingCount }} Pending</span>
                    @endif
                    @if($rejectedCount)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold border bg-red-50 text-red-700 border-red-200 uppercase tracking-wider">{{ $rejectedCount }} Rejected</span>
                    @endif
                    @if($missingCount)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold border bg-gray-100 text-gray-500 border-gray-200 uppercase tracking-wider">{{ $missingCount }} Missing</span>
                    @endif
                </div>
                
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    @foreach($attachmentTypes as $key => $label)
                        @php
                            $att = $attachmentsByKey->get($key);
                            $tileCls = match($att?->status) {
                                'approved' => 'bg-white border-2 border-emerald-500/80 shadow-sm shadow-emerald-50/50 hover:shadow-md hover:shadow-emerald-100/10',
                                'pending'  => 'bg-white border-2 border-amber-500/80 shadow-sm shadow-amber-50/50 hover:shadow-md hover:shadow-amber-100/10',
                                'rejected' => 'bg-white border-2 border-red-500/80 shadow-sm shadow-red-50/50 hover:shadow-md hover:shadow-red-100/10',
                                default    => 'bg-gray-50 border border-dashed border-gray-200 opacity-70 hover:opacity-100',
                            };
                            $tagCls = match($att?->status) {
                                'approved' => 'bg-emerald-50 text-emerald-700 border border-emerald-100',
                                'pending'  => 'bg-amber-50 text-amber-700 border-amber-100',
                                'rejected' => 'bg-red-50 text-red-700 border-red-100',
                                default    => 'bg-gray-100 text-gray-400 border border-gray-200',
                            };
                            $tagLabel = $att ? ucfirst($att->status) : 'Missing';
                        @endphp
                        
                        <div class="rounded-xl p-4 text-center flex flex-col items-center justify-between gap-3 min-h-[140px] border transition-all duration-300 {{ $tileCls }}">
                            @if($att)
                                @if(in_array($att->mime_type, ['image/jpeg','image/png','image/gif','image/webp']))
                                    <a href="{{ $att->url }}" target="_blank" class="w-full flex justify-center group overflow-hidden rounded-lg">
                                        <img src="{{ $att->url }}" alt="{{ $label }}" class="h-14 w-full object-cover rounded-lg group-hover:scale-105 transition-transform duration-300">
                                    </a>
                                @else
                                    <a href="{{ $att->url }}" target="_blank" class="w-10 h-10 flex items-center justify-center rounded-lg bg-gray-100 text-gray-500 hover:bg-gray-200 transition-colors">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </a>
                                @endif
                            @else
                                <div class="w-10 h-10 flex items-center justify-center rounded-lg bg-gray-100/50 text-gray-400">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                </div>
                            @endif
                            
                            <div class="text-[11px] font-bold text-gray-700 leading-tight line-clamp-2">{{ $label }}</div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider {{ $tagCls }}">
                                {{ $tagLabel }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- LEAVES TAB --}}
    @if($tab === 'leaves')
    <div class="space-y-6 emp-card-in">
        {{-- Leave Balance --}}
        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">
            <div class="px-6 py-4.5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <h2 class="text-sm font-bold text-gray-950 flex items-center gap-2.5">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span> 
                    Leave Balance
                </h2>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto border border-gray-100 rounded-xl">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="px-5 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Leave Type</th>
                                <th class="px-5 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-wider text-center">Total Days</th>
                                <th class="px-5 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-wider text-center">Used Days</th>
                                <th class="px-5 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-wider text-center">Remaining Days</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($employee->leaveBalances as $balance)
                                <tr class="hover:bg-gray-50/50 transition-colors duration-150">
                                    <td class="px-5 py-3.5 font-semibold text-gray-900">{{ $balance->leaveType->name ?? '—' }}</td>
                                    <td class="px-5 py-3.5 text-center font-mono text-sm text-gray-600">{{ $balance->total_days }}</td>
                                    <td class="px-5 py-3.5 text-center font-mono text-sm text-red-600 font-bold bg-red-50/30">{{ $balance->used_days }}</td>
                                    <td class="px-5 py-3.5 text-center font-mono text-sm text-emerald-600 font-bold bg-emerald-50/30">{{ $balance->remaining_days }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-8 text-center text-gray-400 text-sm bg-gray-50/20">
                                        No leave balance records found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Recent Leave Requests --}}
        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">
            <div class="px-6 py-4.5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <h2 class="text-sm font-bold text-gray-950 flex items-center gap-2.5">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span> 
                    Recent Leave Requests
                </h2>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto border border-gray-100 rounded-xl">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="px-5 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Leave Type</th>
                                <th class="px-5 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-wider text-center">Date Requested</th>
                                <th class="px-5 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-wider text-center">Days</th>
                                <th class="px-5 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-wider text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($employee->leaves()->orderBy('created_at', 'desc')->limit(10)->get() as $leave)
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'rejected' => 'bg-red-50 text-red-700 border-red-200',
                                        'cancelled' => 'bg-gray-100 text-gray-500 border-gray-200'
                                    ];
                                    $sc = $statusColors[strtolower($leave->status)] ?? 'bg-gray-100 text-gray-500 border-gray-200';
                                @endphp
                                <tr class="hover:bg-gray-50/80 cursor-pointer transition-colors duration-150" onclick="openLeaveModal({{ $leave->id }})">
                                    <td class="px-5 py-3.5 font-semibold text-gray-900">{{ $leave->leave_type ?? '—' }}</td>
                                    <td class="px-5 py-3.5 text-center font-mono text-xs text-gray-500">{{ $leave->created_at?->format('M d, Y') ?? '—' }}</td>
                                    <td class="px-5 py-3.5 text-center font-mono text-sm text-gray-800 font-semibold">{{ $leave->days ?? '—' }}</td>
                                    <td class="px-5 py-3.5 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border uppercase tracking-wider text-[10px] {{ $sc }}">
                                            {{ ucfirst($leave->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-8 text-center text-gray-400 text-sm bg-gray-50/20">
                                        No leave requests found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>

{{-- Delete Confirmation Modal --}}
<div id="deleteModal" class="fixed inset-0 z-[10000] hidden items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm transition-opacity duration-300">
    <div class="bg-white rounded-2xl p-6 shadow-2xl max-w-sm w-full border border-gray-100 scale-95 transition-transform duration-300" id="deleteModalContainer">
        <div class="w-12 h-12 rounded-full bg-red-50 text-red-600 flex items-center justify-center mb-4 border border-red-100">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-2">Delete Employee</h3>
        <p class="text-sm text-gray-500 mb-6 leading-relaxed">Are you sure you want to delete this employee? This action cannot be undone and will permanently remove their records.</p>
        <div class="flex items-center justify-end gap-2.5">
            <button type="button" class="px-4 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:border-gray-300 hover:text-gray-900 transition-all duration-150" onclick="closeDeleteModal()">Cancel</button>
            <button type="button" class="px-4 py-2.5 text-sm font-semibold text-white bg-red-600 rounded-xl hover:bg-red-700 shadow-lg shadow-red-600/10 active:scale-[0.98] transition-all duration-150" onclick="confirmDelete()">Delete</button>
        </div>
    </div>
</div>

{{-- Leave Request Details Modal --}}
<div id="leaveDetailModal" class="fixed inset-0 z-[10000] hidden items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm transition-opacity duration-300">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full border border-gray-100 overflow-hidden scale-95 transition-transform duration-300" id="leaveDetailModalContainer">
        <div class="px-6 py-4.5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <h3 class="text-base font-bold text-gray-950">Leave Request Details</h3>
            <button onclick="closeLeaveModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div id="leaveDetailContent" class="px-6 py-5 max-h-[60vh] overflow-y-auto space-y-4">
            <!-- Content loaded via AJAX -->
        </div>
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50 flex justify-end">
            <button type="button" class="px-4 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:border-gray-300 hover:text-gray-900 transition-all duration-150" onclick="closeLeaveModal()">Close</button>
        </div>
    </div>
</div>

<script>
function openDeleteModal() {
    const el = document.getElementById('deleteModal');
    el.classList.remove('hidden');
    el.classList.add('flex');
}

// Ensure backdrop click closes modal correctly
function closeDeleteModal() {
    const el = document.getElementById('deleteModal');
    el.classList.remove('flex');
    el.classList.add('hidden');
}

function confirmDelete() {
    document.getElementById('deleteEmployeeForm').submit();
}

function openLeaveModal(leaveId) {
    const leaveDetailModal = document.getElementById('leaveDetailModal');
    const leaveDetailContent = document.getElementById('leaveDetailContent');
    leaveDetailContent.innerHTML = '<div class="flex items-center justify-center py-12"><div class="w-6 h-6 border-2 border-gray-300 border-t-gray-900 rounded-full animate-spin"></div></div>';
    leaveDetailModal.classList.remove('hidden');
    leaveDetailModal.classList.add('flex');
    
    // Fetch leave details via AJAX
    fetch(`/api/leaves/${leaveId}`)
        .then(response => response.json())
        .then(data => {
            const leave = data.data;
            const statusColors = {
                'pending': ['bg-amber-50 text-amber-700 border-amber-200', 'Pending'],
                'approved': ['bg-emerald-50 text-emerald-700 border-emerald-200', 'Approved'],
                'rejected': ['bg-red-50 text-red-700 border-red-200', 'Rejected'],
                'cancelled': ['bg-gray-100 text-gray-500 border-gray-200', 'Cancelled']
            };
            const sc = statusColors[leave.status] || ['bg-gray-100 text-gray-500 border-gray-200', leave.status];
            
            let detailsHTML = `
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-3 bg-gray-50/60 border border-gray-100 rounded-xl">
                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Employee</div>
                        <div class="text-sm font-semibold text-gray-900">${leave.employee_name}</div>
                    </div>
                    <div class="p-3 bg-gray-50/60 border border-gray-100 rounded-xl">
                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Leave Type</div>
                        <div class="text-sm font-semibold text-gray-900">${leave.leave_type_name}</div>
                    </div>
                    <div class="p-3 bg-gray-50/60 border border-gray-100 rounded-xl">
                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">From Date</div>
                        <div class="text-sm font-semibold text-gray-900">${new Date(leave.from_date).toLocaleDateString('en-US', {year: 'numeric', month: 'long', day: 'numeric'})}</div>
                    </div>
                    <div class="p-3 bg-gray-50/60 border border-gray-100 rounded-xl">
                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">To Date</div>
                        <div class="text-sm font-semibold text-gray-900">${new Date(leave.to_date).toLocaleDateString('en-US', {year: 'numeric', month: 'long', day: 'numeric'})}</div>
                    </div>
                    <div class="p-3 bg-gray-50/60 border border-gray-100 rounded-xl">
                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Number of Days</div>
                        <div class="text-sm font-semibold text-gray-900 font-mono">${leave.number_of_days} day(s)</div>
                    </div>
                    <div class="p-3 bg-gray-50/60 border border-gray-100 rounded-xl">
                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Status</div>
                        <div class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border ${sc[0]}">
                                ${sc[1]}
                            </span>
                        </div>
                    </div>
                    ${leave.reason ? `
                    <div class="p-3 bg-gray-50/60 border border-gray-100 rounded-xl sm:col-span-2">
                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Reason</div>
                        <div class="text-sm text-gray-700 leading-relaxed">${leave.reason}</div>
                    </div>
                    ` : ''}
                    ${leave.approved_by ? `
                    <div class="p-3 bg-gray-50/60 border border-gray-100 rounded-xl sm:col-span-2">
                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Approved By</div>
                        <div class="text-sm text-gray-700">${leave.approved_by}</div>
                    </div>
                    ` : ''}
                    ${leave.rejection_reason ? `
                    <div class="p-3 bg-red-50/20 border border-red-100 rounded-xl sm:col-span-2">
                        <div class="text-[10px] font-bold text-red-500 uppercase tracking-widest mb-1">Rejection Reason</div>
                        <div class="text-sm text-red-700 leading-relaxed">${leave.rejection_reason}</div>
                    </div>
                    ` : ''}
                </div>
            `;
            
            leaveDetailContent.innerHTML = detailsHTML;
        })
        .catch(error => {
            console.error('Error fetching leave details:', error);
            leaveDetailContent.innerHTML = '<div class="flex flex-col items-center justify-center py-8 text-center"><div class="w-10 h-10 rounded-full bg-red-50 border border-red-100 text-red-500 flex items-center justify-center mb-3"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></div><p class="text-sm font-semibold text-gray-700">Failed to load details</p><p class="text-xs text-gray-400 mt-1">Please try again later.</p></div>';
        });
}

function closeLeaveModal() {
    const el = document.getElementById('leaveDetailModal');
    el.classList.remove('flex');
    el.classList.add('hidden');
}

// Close modals when clicking outside of them
document.addEventListener('DOMContentLoaded', function() {
    const deleteModal = document.getElementById('deleteModal');
    const leaveDetailModal = document.getElementById('leaveDetailModal');
    
    deleteModal.addEventListener('click', function(event) {
        if (event.target === deleteModal) {
            closeDeleteModal();
        }
    });
    
    leaveDetailModal.addEventListener('click', function(event) {
        if (event.target === leaveDetailModal) {
            closeLeaveModal();
        }
    });
});
</script>
@endsection
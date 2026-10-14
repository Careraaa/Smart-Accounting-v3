@extends('layouts.layout')

@php
$inputClass = 'w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-900 bg-white outline-none transition-all duration-150 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10';
$labelClass = 'block text-[0.65rem] font-bold uppercase tracking-wider text-gray-400 mb-1.5';
$reqClass   = 'text-red-400 ml-0.5';
$descClass  = 'text-[0.6rem] text-gray-400 mt-1 leading-relaxed';
@endphp

@section('content')
<div class="max-w-4xl mx-auto space-y-8">

    <div class="fade-in">
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">System configuration</h1>
        <p class="text-sm text-gray-400 mt-1">General settings, backups, and maintenance</p>
    </div>

    @if ($errors->any())
    <div class="slide-in flex items-start gap-2.5 px-4 py-3 rounded-xl text-sm font-medium bg-red-50 border border-red-200 text-red-700">
        <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
        <span>{{ $errors->first() }}</span>
    </div>
    @endif

    @if (session('success'))
    <div class="slide-in flex items-center gap-2.5 px-4 py-3 rounded-xl text-sm font-medium bg-emerald-50 border border-emerald-200 text-emerald-700">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if (session('error'))
    <div class="slide-in flex items-center gap-2.5 px-4 py-3 rounded-xl text-sm font-medium bg-red-50 border border-red-200 text-red-700">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 9l-6 6M9 9l6 6"/></svg>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <div class="flex gap-1 p-1 bg-gray-100/80 border border-gray-200 rounded-xl fade-in tab-bar">
        <button type="button" class="tab-btn px-4 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 bg-white text-gray-900 shadow-sm" data-tab="general">General</button>
        <button type="button" class="tab-btn px-4 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 bg-transparent text-gray-500 hover:text-gray-900" data-tab="positions">Positions</button>
        <button type="button" class="tab-btn px-4 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 bg-transparent text-gray-500 hover:text-gray-900" data-tab="backup">Backup</button>
        <button type="button" class="tab-btn px-4 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 bg-transparent text-gray-500 hover:text-gray-900" data-tab="maintenance">Maintenance</button>
        <button type="button" class="tab-btn px-4 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 bg-transparent text-gray-500 hover:text-gray-900" data-tab="testing">Testing</button>
    </div>

    {{-- GENERAL TAB --}}
    <div id="tab-general" class="tab-panel">
        <div class="scale-in bg-white rounded-xl border border-gray-100 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-50">
                <h3 class="text-xs font-bold text-gray-900 flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="1"/><path d="M12 1v6m0 6v6"/><path d="M4.22 4.22l4.24 4.24m2.12 2.12l4.24 4.24"/><path d="M1 12h6m6 0h6"/><path d="M4.22 19.78l4.24-4.24m2.12-2.12l4.24-4.24"/></svg>
                    General Settings
                </h3>
            </div>
            <div class="p-6">
                <form action="{{ route('configuration.update-general') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div>
                            <label for="system_name" class="{{ $labelClass }}">System name <span class="{{ $reqClass }}">*</span></label>
                            <input type="text" id="system_name" name="system_name" class="{{ $inputClass }}" value="{{ $settings['system_name'] ?? 'Knights Transport' }}" required>
                            <p class="{{ $descClass }}">The official name of your organization</p>
                        </div>
                        <div>
                            <label for="timezone" class="{{ $labelClass }}">Timezone <span class="{{ $reqClass }}">*</span></label>
                            <select id="timezone" name="timezone" class="{{ $inputClass }}" required>
                                <option value="UTC" {{ ($settings['timezone'] ?? 'UTC') === 'UTC' ? 'selected' : '' }}>UTC</option>
                                <option value="America/New_York" {{ ($settings['timezone'] ?? '') === 'America/New_York' ? 'selected' : '' }}>Eastern Time (ET)</option>
                                <option value="America/Chicago" {{ ($settings['timezone'] ?? '') === 'America/Chicago' ? 'selected' : '' }}>Central Time (CT)</option>
                                <option value="America/Denver" {{ ($settings['timezone'] ?? '') === 'America/Denver' ? 'selected' : '' }}>Mountain Time (MT)</option>
                                <option value="America/Los_Angeles" {{ ($settings['timezone'] ?? '') === 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time (PT)</option>
                                <option value="Europe/London" {{ ($settings['timezone'] ?? '') === 'Europe/London' ? 'selected' : '' }}>London</option>
                                <option value="Europe/Paris" {{ ($settings['timezone'] ?? '') === 'Europe/Paris' ? 'selected' : '' }}>Paris</option>
                                <option value="Asia/Tokyo" {{ ($settings['timezone'] ?? '') === 'Asia/Tokyo' ? 'selected' : '' }}>Tokyo</option>
                                <option value="Asia/Manila" {{ ($settings['timezone'] ?? '') === 'Asia/Manila' ? 'selected' : '' }}>Manila (PHT)</option>
                                <option value="Asia/Singapore" {{ ($settings['timezone'] ?? '') === 'Asia/Singapore' ? 'selected' : '' }}>Singapore</option>
                                <option value="Australia/Sydney" {{ ($settings['timezone'] ?? '') === 'Australia/Sydney' ? 'selected' : '' }}>Sydney</option>
                            </select>
                            <p class="{{ $descClass }}">Select the timezone for your system</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div>
                            <label for="date_format" class="{{ $labelClass }}">Date format <span class="{{ $reqClass }}">*</span></label>
                            <select id="date_format" name="date_format" class="{{ $inputClass }}" required>
                                <option value="Y-m-d" {{ ($settings['date_format'] ?? 'Y-m-d') === 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                                <option value="d-m-Y" {{ ($settings['date_format'] ?? '') === 'd-m-Y' ? 'selected' : '' }}>DD-MM-YYYY</option>
                                <option value="m-d-Y" {{ ($settings['date_format'] ?? '') === 'm-d-Y' ? 'selected' : '' }}>MM-DD-YYYY</option>
                                <option value="Y/m/d" {{ ($settings['date_format'] ?? '') === 'Y/m/d' ? 'selected' : '' }}>YYYY/MM/DD</option>
                                <option value="d/m/Y" {{ ($settings['date_format'] ?? '') === 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                                <option value="m/d/Y" {{ ($settings['date_format'] ?? '') === 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                            </select>
                            <p class="{{ $descClass }}">How dates appear throughout the system</p>
                        </div>
                        <div>
                            <label for="language" class="{{ $labelClass }}">Language <span class="{{ $reqClass }}">*</span></label>
                            <select id="language" name="language" class="{{ $inputClass }}" required>
                                <option value="en" {{ ($settings['language'] ?? 'en') === 'en' ? 'selected' : '' }}>English</option>
                                <option value="es" {{ ($settings['language'] ?? '') === 'es' ? 'selected' : '' }}>Spanish</option>
                                <option value="fr" {{ ($settings['language'] ?? '') === 'fr' ? 'selected' : '' }}>French</option>
                                <option value="de" {{ ($settings['language'] ?? '') === 'de' ? 'selected' : '' }}>German</option>
                                <option value="tl" {{ ($settings['language'] ?? '') === 'tl' ? 'selected' : '' }}>Tagalog</option>
                                <option value="fil" {{ ($settings['language'] ?? '') === 'fil' ? 'selected' : '' }}>Filipino</option>
                            </select>
                            <p class="{{ $descClass }}">Default language for the system interface</p>
                        </div>
                    </div>
                    <div class="flex gap-3 pt-5 border-t border-gray-50">
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 text-white rounded-xl text-sm font-semibold hover:bg-gray-800 transition-all duration-200 active:scale-[0.97]">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════ --}}
    {{-- POSITIONS TAB --}}
    {{-- ════════════════════════════════ --}}
    <div id="tab-positions" class="tab-panel hidden space-y-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5" style="animation:fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both;">
            <div class="flex items-center justify-between gap-3 mb-5">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M20 7H4l2-3h8l2 3z"/><rect x="3" y="7" width="18" height="12" rx="2"/><path d="M8 12h8"/></svg>
                    <h3 class="text-sm font-bold text-gray-900">Manage Departments & Positions</h3>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="openModal('addDepartmentModal')" class="inline-flex items-center gap-1.5 px-3.5 py-2 border border-gray-200 text-gray-700 rounded-lg text-xs font-bold transition-all hover:bg-gray-50 active:scale-[0.97] cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Add New Department
                    </button>
                    <button onclick="openModal('addPositionModal')" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-gray-900 text-white rounded-lg text-xs font-bold transition-all hover:bg-black active:scale-[0.97] cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Add New Position
                    </button>
                </div>
            </div>

            <div class="grid gap-4 xl:grid-cols-2">
                <div class="rounded-xl border border-gray-100 bg-gray-50/70 p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Departments</h4>
                        <span class="text-[0.65rem] text-gray-400">{{ $departments->count() }} configured</span>
                    </div>
                    @if($departments->isEmpty())
                        <div class="py-10 text-center text-sm text-gray-500">No departments configured yet.</div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-white border-b border-gray-100">
                                        <th class="px-3 py-2 text-left text-[0.6rem] font-bold uppercase tracking-widest text-gray-500">Department</th>
                                        <th class="px-3 py-2 text-right text-[0.6rem] font-bold uppercase tracking-widest text-gray-500">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($departments as $department)
                                    <tr class="bg-white">
                                        <td class="px-3 py-2 text-sm font-semibold text-gray-900">{{ $department->name }}</td>
                                        <td class="px-3 py-2 text-right">
                                            <div class="flex items-center justify-end gap-1">
                                                <button onclick="openEditDepartmentModal({{ $department->id }})" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors cursor-pointer" title="Edit">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                                </button>
                                                <form action="{{ route('settings.department.destroy', $department) }}" method="POST" data-sa-confirm="Are you sure you want to delete this department?" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors cursor-pointer" title="Delete">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <div class="rounded-xl border border-gray-100 bg-gray-50/70 p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Positions</h4>
                        <span class="text-[0.65rem] text-gray-400">{{ $positions->count() }} configured</span>
                    </div>
                    @if($positions->isEmpty())
                        <div class="py-10 text-center text-sm text-gray-500">No positions configured yet.</div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-white border-b border-gray-100">
                                        <th class="px-3 py-2 text-left text-[0.6rem] font-bold uppercase tracking-widest text-gray-500">Position</th>
                                        <th class="px-3 py-2 text-left text-[0.6rem] font-bold uppercase tracking-widest text-gray-500">Department</th>
                                        <th class="px-3 py-2 text-left text-[0.6rem] font-bold uppercase tracking-widest text-gray-500">Daily Rate</th>
                                        <th class="px-3 py-2 text-right text-[0.6rem] font-bold uppercase tracking-widest text-gray-500">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($positions as $position)
                                    <tr class="bg-white">
                                        <td class="px-3 py-2 text-sm font-semibold text-gray-900">{{ $position->name }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-600">{{ $position->department?->name ?? '—' }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-600">₱{{ number_format($position->daily_rate, 2) }}</td>
                                        <td class="px-3 py-2 text-right">
                                            <div class="flex items-center justify-end gap-1">
                                                <button onclick="openEditPositionModal({{ $position->id }})" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors cursor-pointer" title="Edit">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                                </button>
                                                <form action="{{ route('settings.position.destroy', $position) }}" method="POST" data-sa-confirm="Are you sure you want to delete this position?" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors cursor-pointer" title="Delete">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- BACKUP TAB --}}
    <div id="tab-backup" class="tab-panel hidden space-y-5">
        <div class="scale-in bg-white rounded-xl border border-gray-100 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-50">
                <h3 class="text-xs font-bold text-gray-900 flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Backup Configuration
                </h3>
            </div>
            <div class="p-6">
                <form action="{{ route('configuration.update-backup') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div>
                            <label for="backup_frequency" class="{{ $labelClass }}">Backup frequency <span class="{{ $reqClass }}">*</span></label>
                            <select id="backup_frequency" name="backup_frequency" class="{{ $inputClass }}" required>
                                <option value="hourly" {{ ($settings['backup_frequency'] ?? 'daily') === 'hourly' ? 'selected' : '' }}>Hourly</option>
                                <option value="daily" {{ ($settings['backup_frequency'] ?? 'daily') === 'daily' ? 'selected' : '' }}>Daily</option>
                                <option value="weekly" {{ ($settings['backup_frequency'] ?? '') === 'weekly' ? 'selected' : '' }}>Weekly</option>
                                <option value="monthly" {{ ($settings['backup_frequency'] ?? '') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                            </select>
                            <p class="{{ $descClass }}">How often backups are created automatically</p>
                        </div>
                        <div>
                            <label for="backup_retention" class="{{ $labelClass }}">Retention (days) <span class="{{ $reqClass }}">*</span></label>
                            <input type="number" id="backup_retention" name="backup_retention" class="{{ $inputClass }}" value="{{ $settings['backup_retention'] ?? 30 }}" min="1" max="365" required>
                            <p class="{{ $descClass }}">Number of days to keep backups</p>
                        </div>
                    </div>
                    <div class="flex gap-3 pt-5 border-t border-gray-50">
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 text-white rounded-xl text-sm font-semibold hover:bg-gray-800 transition-all duration-200 active:scale-[0.97]">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="scale-in bg-white rounded-xl border border-gray-100 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-50">
                <h3 class="text-xs font-bold text-gray-900 flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Quick Actions
                </h3>
            </div>
            <div class="p-6">
                <div class="flex gap-3 flex-wrap">
                    <form action="{{ route('configuration.backup-now') }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 text-white rounded-xl text-sm font-semibold hover:bg-emerald-700 transition-all duration-200 active:scale-[0.97]">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Backup Now
                        </button>
                    </form>
                    <a href="{{ route('configuration.backup-history') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white text-gray-600 border border-gray-200 rounded-xl text-sm font-semibold hover:border-gray-900 hover:text-gray-900 transition-all duration-200 no-underline active:scale-[0.97]">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        Backup History
                    </a>
                    <a href="{{ route('configuration.restore-form') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white text-gray-600 border border-gray-200 rounded-xl text-sm font-semibold hover:border-gray-900 hover:text-gray-900 transition-all duration-200 no-underline active:scale-[0.97]">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Restore Database
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- MAINTENANCE TAB --}}
    <div id="tab-maintenance" class="tab-panel hidden space-y-5">
        <div class="scale-in bg-white rounded-xl border border-gray-100 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-50">
                <h3 class="text-xs font-bold text-gray-900 flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Maintenance Mode
                </h3>
            </div>
            <div class="p-6">
                <div class="flex items-center justify-between gap-4 px-5 py-4 bg-gray-50/80 border border-gray-100 rounded-xl flex-wrap">
                    <div>
                        <p class="text-sm font-semibold text-gray-900 mb-0.5">Maintenance mode</p>
                        <p class="text-xs text-gray-400">Temporarily disable user access while you work on the system</p>
                    </div>
                    <form action="{{ route('configuration.toggle-maintenance') }}" method="POST">
                        @csrf
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" onchange="this.form.submit()" {{ !empty($settings['maintenance_mode']) ? 'checked' : '' }}>
                            <span class="w-10 h-6 bg-gray-200 rounded-full peer-checked:bg-emerald-500 transition-colors duration-300"></span>
                            <span class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow peer-checked:translate-x-4 transition-transform duration-300"></span>
                        </label>
                    </form>
                </div>
                <div class="mt-3">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[0.6rem] font-bold {{ !empty($settings['maintenance_mode']) ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-gray-50 text-gray-500 border border-gray-200' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ !empty($settings['maintenance_mode']) ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                        {{ !empty($settings['maintenance_mode']) ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="scale-in bg-white rounded-xl border border-gray-100 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-50">
                <h3 class="text-xs font-bold text-gray-900 flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    System Utilities
                </h3>
            </div>
            <div class="p-6">
                <div class="flex gap-3 flex-wrap">
                    <form action="{{ route('configuration.clear-cache') }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white text-gray-600 border border-gray-200 rounded-xl text-sm font-semibold hover:border-gray-900 hover:text-gray-900 transition-all duration-200 active:scale-[0.97]">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Clear Cache
                        </button>
                    </form>
                    <form action="{{ route('configuration.clear-logs') }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white text-gray-600 border border-gray-200 rounded-xl text-sm font-semibold hover:border-red-400 hover:text-red-600 transition-all duration-200 active:scale-[0.97]">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Clear Logs
                        </button>
                    </form>
                </div>
                <p class="text-[0.6rem] text-gray-400 mt-4 leading-relaxed">
                    <strong>Cache:</strong> Clears application and configuration cache.<br>
                    <strong>Logs:</strong> Deletes all system activity logs permanently.
                </p>
            </div>
        </div>
    </div>

    {{-- TESTING TAB --}}
    <div id="tab-testing" class="tab-panel hidden space-y-5">
        <div class="scale-in bg-white rounded-xl border border-gray-100 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-50">
                <h3 class="text-xs font-bold text-gray-900 flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Testing Mode
                </h3>
            </div>
            <div class="p-6">
                <div class="flex items-center justify-between gap-4 px-5 py-4 bg-gray-50/80 border border-gray-100 rounded-xl flex-wrap">
                    <div>
                        <p class="text-sm font-semibold text-gray-900 mb-0.5">Quick-add attendance</p>
                        <p class="text-xs text-gray-400">When enabled, attendance calendar cells show a quick-edit button to add time in/out — OT and UT are auto-detected from shift rules and auto-approved</p>
                    </div>
                    <form action="{{ route('configuration.toggle-testing') }}" method="POST">
                        @csrf
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="testing-toggle-input" class="sr-only peer" {{ ($settings['testing_mode'] ?? 'disabled') === 'enabled' ? 'checked' : '' }}>
                            <span class="w-10 h-6 bg-gray-200 rounded-full peer-checked:bg-gray-900 transition-colors duration-300"></span>
                            <span class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow peer-checked:translate-x-4 transition-transform duration-300"></span>
                        </label>
                    </form>
                </div>
                <div class="mt-3">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[0.6rem] font-bold {{ ($settings['testing_mode'] ?? 'disabled') === 'enabled' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-gray-50 text-gray-500 border border-gray-200' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ ($settings['testing_mode'] ?? 'disabled') === 'enabled' ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                        {{ ($settings['testing_mode'] ?? 'disabled') === 'enabled' ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                @if (($settings['testing_mode'] ?? 'disabled') === 'enabled')
                <div class="mt-5 p-4 bg-amber-50/60 border border-amber-200 rounded-xl">
                    <div class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        <div>
                            <p class="text-xs font-bold text-amber-800 mb-1">Testing mode is active</p>
                            <p class="text-[11px] text-amber-700/80 leading-relaxed">Quick-add buttons now appear in attendance calendar date cells. Click the <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 bg-white border border-gray-200 rounded text-[10px] font-bold text-gray-600">+</span> icon on any date to add or edit attendance — OT and UT are auto-calculated from shift rules and auto-approved. Disable this mode when done testing.</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ════════════════════════════════ --}}
{{-- ADD DEPARTMENT MODAL --}}
{{-- ════════════════════════════════ --}}
<div id="addDepartmentModal" class="hidden fixed inset-0 z-50 flex items-center justify-center" style="background:rgba(0,0,0,0.4)">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden" style="animation:scaleIn 0.2s cubic-bezier(0.16,1,0.3,1) both;">
        <div class="px-5 py-4 border-b border-gray-50">
            <h5 class="text-sm font-bold text-gray-900">Add New Department</h5>
        </div>
        <form action="{{ route('settings.department.store') }}" method="POST">
            @csrf
            <div class="px-5 py-4 space-y-4">
                <div>
                    <label class="text-[0.6rem] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Department Name</label>
                    <input type="text" name="name" placeholder="e.g., Accounting" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-900 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
                </div>
            </div>
            <div class="px-5 py-4 border-t border-gray-50 flex justify-end gap-2">
                <button type="button" onclick="closeModal('addDepartmentModal')" class="px-4 py-2 border border-gray-200 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-50 transition-colors cursor-pointer">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-gray-900 text-white rounded-lg text-xs font-bold hover:bg-black transition-all cursor-pointer">Add Department</button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════════════════════ --}}
{{-- ADD POSITION MODAL --}}
{{-- ════════════════════════════════ --}}
<div id="addPositionModal" class="hidden fixed inset-0 z-50 flex items-center justify-center" style="background:rgba(0,0,0,0.4)">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden" style="animation:scaleIn 0.2s cubic-bezier(0.16,1,0.3,1) both;">
        <div class="px-5 py-4 border-b border-gray-50">
            <h5 class="text-sm font-bold text-gray-900">Add New Position</h5>
        </div>
        <form action="{{ route('settings.position.store') }}" method="POST">
            @csrf
            <div class="px-5 py-4 space-y-4">
                <div>
                    <label class="text-[0.6rem] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Position Name</label>
                    <input type="text" name="name" placeholder="e.g., Supervisor" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-900 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
                </div>
                <div>
                    <label class="text-[0.6rem] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Department</label>
                    <select name="department_id" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-900 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
                        <option value="" disabled selected>Select department</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[0.6rem] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Daily Rate</label>
                    <input type="number" step="0.01" name="daily_rate" min="0" placeholder="0.00" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-900 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
                </div>
            </div>
            <div class="px-5 py-4 border-t border-gray-50 flex justify-end gap-2">
                <button type="button" onclick="closeModal('addPositionModal')" class="px-4 py-2 border border-gray-200 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-50 transition-colors cursor-pointer">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-gray-900 text-white rounded-lg text-xs font-bold hover:bg-black transition-all cursor-pointer">Add Position</button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════════════════════ --}}
{{-- EDIT DEPARTMENT MODALS --}}
{{-- ════════════════════════════════ --}}
@foreach($departments as $department)
<div id="editDepartmentModal{{ $department->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center" style="background:rgba(0,0,0,0.4)">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden" style="animation:scaleIn 0.2s cubic-bezier(0.16,1,0.3,1) both;">
        <div class="px-5 py-4 border-b border-gray-50">
            <h5 class="text-sm font-bold text-gray-900">Edit Department: {{ $department->name }}</h5>
        </div>
        <form action="{{ route('settings.department.update', $department) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="px-5 py-4 space-y-4">
                <div>
                    <label class="text-[0.6rem] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Department Name</label>
                    <input type="text" name="name" value="{{ $department->name }}" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-900 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
                </div>
            </div>
            <div class="px-5 py-4 border-t border-gray-50 flex justify-end gap-2">
                <button type="button" onclick="closeModal('editDepartmentModal{{ $department->id }}')" class="px-4 py-2 border border-gray-200 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-50 transition-colors cursor-pointer">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-gray-900 text-white rounded-lg text-xs font-bold hover:bg-black transition-all cursor-pointer">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endforeach

{{-- ════════════════════════════════ --}}
{{-- EDIT POSITION MODALS --}}
{{-- ════════════════════════════════ --}}
@foreach($positions as $position)
<div id="editPositionModal{{ $position->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center" style="background:rgba(0,0,0,0.4)">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden" style="animation:scaleIn 0.2s cubic-bezier(0.16,1,0.3,1) both;">
        <div class="px-5 py-4 border-b border-gray-50">
            <h5 class="text-sm font-bold text-gray-900">Edit Position: {{ $position->name }}</h5>
        </div>
        <form action="{{ route('settings.position.update', $position) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="px-5 py-4 space-y-4">
                <div>
                    <label class="text-[0.6rem] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Position Name</label>
                    <input type="text" name="name" value="{{ $position->name }}" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-900 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
                </div>
                <div>
                    <label class="text-[0.6rem] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Department</label>
                    <select name="department_id" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-900 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ $position->department_id == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[0.6rem] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Daily Rate</label>
                    <input type="number" step="0.01" name="daily_rate" value="{{ number_format($position->daily_rate, 2, '.', '') }}" min="0" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-900 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
                </div>
            </div>
            <div class="px-5 py-4 border-t border-gray-50 flex justify-end gap-2">
                <button type="button" onclick="closeModal('editPositionModal{{ $position->id }}')" class="px-4 py-2 border border-gray-200 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-50 transition-colors cursor-pointer">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-gray-900 text-white rounded-lg text-xs font-bold hover:bg-black transition-all cursor-pointer">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endforeach

<style>
@keyframes fadeIn { 0%{opacity:0} 100%{opacity:1} }
@keyframes slideIn { 0%{opacity:0;transform:translateY(-8px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { 0%{opacity:0;transform:scale(0.97)} 100%{opacity:1;transform:scale(1)} }
@keyframes tabFade { 0%{opacity:0;transform:translateY(6px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes fadeSlideUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
.fade-in { animation:fadeIn 0.4s ease-out both; }
.slide-in { animation:slideIn 0.35s cubic-bezier(0.16,1,0.3,1) both; }
.scale-in { animation:scaleIn 0.35s cubic-bezier(0.16,1,0.3,1) both; }
.tab-panel.active { animation:tabFade 0.3s cubic-bezier(0.16,1,0.3,1) both; }
.tab-bar > .tab-btn { transition: all 0.2s ease; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var activeTab = 'general';
    var tabBtns = document.querySelectorAll('.tab-btn');
    var panels = {
        general: document.getElementById('tab-general'),
        positions: document.getElementById('tab-positions'),
        backup: document.getElementById('tab-backup'),
        maintenance: document.getElementById('tab-maintenance'),
        testing: document.getElementById('tab-testing'),
    };

    var defBtn = ['bg-transparent', 'text-gray-500', 'hover:text-gray-900'];
    var actBtn = ['bg-white', 'text-gray-900', 'shadow-sm'];

    function switchTab(tab) {
        if (tab === activeTab) return;
        Object.keys(panels).forEach(function (k) {
            panels[k].classList.add('hidden');
            panels[k].classList.remove('active');
        });
        tabBtns.forEach(function (b) {
            b.classList.remove.apply(b.classList, actBtn);
            b.classList.add.apply(b.classList, defBtn);
        });
        var panel = panels[tab];
        if (panel) {
            panel.classList.remove('hidden');
            panel.classList.add('active');
        }
        tabBtns.forEach(function (b) {
            if (b.dataset.tab === tab) {
                b.classList.remove.apply(b.classList, defBtn);
                b.classList.add.apply(b.classList, actBtn);
            }
        });
        activeTab = tab;
        var url = new URL(window.location);
        url.searchParams.set('tab', tab);
        history.replaceState(null, '', url.toString());
    }

    tabBtns.forEach(function (btn) {
        btn.addEventListener('click', function () { switchTab(this.dataset.tab); });
    });

    var tabParam = new URL(window.location).searchParams.get('tab');
    if (tabParam && panels[tabParam]) switchTab(tabParam);

    var flashes = document.querySelectorAll('.slide-in');
    flashes.forEach(function (el) {
        setTimeout(function () {
            el.style.opacity = '0';
            el.style.transform = 'translateY(-8px)';
            el.style.transition = 'all 0.3s ease';
            setTimeout(function () { el.remove(); }, 300);
        }, 5000);
    });

    // Testing toggle confirm
    var testToggle = document.getElementById('testing-toggle-input');
    if (testToggle) {
        testToggle.addEventListener('change', async function (e) {
            if (!this.checked) { this.form.submit(); return; }
            e.preventDefault();
            var confirmed = await window.saConfirm({
                title: 'Enable testing mode?',
                message: 'Quick-add buttons will appear in attendance calendar date cells. OT and UT are auto-calculated from shift rules and auto-approved.',
                confirmText: 'Enable',
                variant: 'primary'
            });
            if (confirmed) {
                this.form.submit();
            } else {
                this.checked = false;
            }
        });
    }
});
</script>

<script>
// ── Modal helpers (departments & positions) ──
function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.body.style.overflow = '';
}
function openEditDepartmentModal(id) {
    openModal('editDepartmentModal' + id);
}
function openEditPositionModal(id) {
    openModal('editPositionModal' + id);
}
// Close modals on backdrop click
document.querySelectorAll('[id$="Modal"]').forEach(function(el) {
    if (el.id === 'addDepartmentModal' || el.id.startsWith('editDepartmentModal') || el.id === 'addPositionModal' || el.id.startsWith('editPositionModal')) {
        el.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
                document.body.style.overflow = '';
            }
        });
    }
});
// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.fixed.inset-0.z-50:not(.hidden)').forEach(function(m) {
            m.classList.add('hidden');
        });
        document.body.style.overflow = '';
    }
});
</script>
@endsection

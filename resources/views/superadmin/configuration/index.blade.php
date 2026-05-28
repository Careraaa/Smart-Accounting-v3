@extends('layouts.layout')

@php
$inputClass = 'w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-900 bg-gray-50 outline-none transition-all duration-150 focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-200/50';
$labelClass = 'block text-xs font-semibold text-gray-700 mb-1.5';
$reqClass   = 'text-red-500 ml-0.5';
$descClass  = 'text-xs text-gray-400 mt-1 leading-relaxed';
@endphp

@section('content')
<div class="space-y-6">

    {{-- Page header --}}
    <div class="flex items-start justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-gray-900 -tracking-[0.02em]">System configuration</h1>
            <p class="text-xs text-gray-400 mt-0.5">General settings, backups, and maintenance</p>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if ($errors->any())
        <div class="flex items-start gap-2.5 px-4 py-3 rounded-xl text-sm font-medium bg-red-50 border border-red-200 text-red-700">
            <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    @if (session('success'))
        <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl text-sm font-medium bg-emerald-50 border border-emerald-200 text-emerald-700">
            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl text-sm font-medium bg-red-50 border border-red-200 text-red-700">
            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Tabs --}}
    <div class="flex gap-1 p-1 bg-gray-100 border border-gray-200 rounded-xl flex-wrap">
        <button type="button" class="px-4 py-2.5 rounded-lg text-sm font-semibold transition-all duration-150 whitespace-nowrap bg-white text-[#c8292a] shadow-sm" onclick="switchTab('general', this)">General</button>
        <button type="button" class="px-4 py-2.5 rounded-lg text-sm font-semibold transition-all duration-150 whitespace-nowrap bg-transparent text-gray-500 hover:text-gray-900" onclick="switchTab('backup', this)">Backup</button>
        <button type="button" class="px-4 py-2.5 rounded-lg text-sm font-semibold transition-all duration-150 whitespace-nowrap bg-transparent text-gray-500 hover:text-gray-900" onclick="switchTab('maintenance', this)">Maintenance</button>
    </div>

    {{-- ════════════════════════════════════════ --}}
    {{-- GENERAL SETTINGS TAB --}}
    {{-- ════════════════════════════════════════ --}}
    <div id="general" class="tab-panel">
        <div class="bg-white border border-gray-200 rounded-xl px-5 sm:px-6 py-5">
            <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2.5">
                <svg class="w-5 h-5 text-[#c8292a]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="1"/>
                    <path d="M12 1v6m0 6v6"/><path d="M4.22 4.22l4.24 4.24m2.12 2.12l4.24 4.24"/>
                    <path d="M1 12h6m6 0h6"/><path d="M4.22 19.78l4.24-4.24m2.12-2.12l4.24-4.24"/>
                </svg>
                General Settings
            </h3>

            <form action="{{ route('configuration.update-general') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
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
                        <p class="{{ $descClass }}">How dates will be displayed throughout the system</p>
                    </div>
                    <div>
                        <label for="language" class="{{ $labelClass }}">Language <span class="{{ $reqClass }}">*</span></label>
                        <select id="language" name="language" class="{{ $inputClass }}" required>
                            <option value="en" {{ ($settings['language'] ?? 'en') === 'en' ? 'selected' : '' }}>English</option>
                            <option value="es" {{ ($settings['language'] ?? '') === 'es' ? 'selected' : '' }}>Spanish (Espa&ntilde;ol)</option>
                            <option value="fr" {{ ($settings['language'] ?? '') === 'fr' ? 'selected' : '' }}>French (Fran&ccedil;ais)</option>
                            <option value="de" {{ ($settings['language'] ?? '') === 'de' ? 'selected' : '' }}>German (Deutsch)</option>
                            <option value="tl" {{ ($settings['language'] ?? '') === 'tl' ? 'selected' : '' }}>Tagalog</option>
                            <option value="fil" {{ ($settings['language'] ?? '') === 'fil' ? 'selected' : '' }}>Filipino</option>
                        </select>
                        <p class="{{ $descClass }}">Default language for the system interface</p>
                    </div>
                </div>

                <div class="flex gap-2.5 flex-wrap mt-5">
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#c8292a] text-white rounded-xl text-sm font-bold hover:bg-[#a81f20] transition-all duration-150 shadow-lg shadow-red-700/30">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                            <polyline points="17 21 17 13 7 13 7 21"></polyline>
                            <polyline points="7 3 7 8 15 8"></polyline>
                        </svg>
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ════════════════════════════════════════ --}}
    {{-- BACKUP SETTINGS TAB --}}
    {{-- ════════════════════════════════════════ --}}
    <div id="backup" class="tab-panel hidden">
        <div class="bg-white border border-gray-200 rounded-xl px-5 sm:px-6 py-5">
            <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2.5">
                <svg class="w-5 h-5 text-[#c8292a]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                Backup Configuration
            </h3>

            <form action="{{ route('configuration.update-backup') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
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
                        <p class="{{ $descClass }}">Number of days to keep backups (1&ndash;365)</p>
                    </div>
                </div>

                <div class="flex gap-2.5 flex-wrap mt-5">
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#c8292a] text-white rounded-xl text-sm font-bold hover:bg-[#a81f20] transition-all duration-150 shadow-lg shadow-red-700/30">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                            <polyline points="17 21 17 13 7 13 7 21"></polyline>
                            <polyline points="7 3 7 8 15 8"></polyline>
                        </svg>
                        Save Settings
                    </button>
                </div>
            </form>
        </div>

        {{-- Backup Actions --}}
        <div class="bg-white border border-gray-200 rounded-xl px-5 sm:px-6 py-5">
            <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2.5">
                <svg class="w-5 h-5 text-[#c8292a]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2v20m10-10H2"></path>
                </svg>
                Quick actions
            </h3>

            <div class="flex gap-2.5 flex-wrap">
                <form action="{{ route('configuration.backup-now') }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 text-white rounded-xl text-sm font-semibold hover:bg-emerald-700 transition-all duration-150 shadow-md">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"></path>
                        </svg>
                        Backup Now
                    </button>
                </form>

                <a href="{{ route('configuration.backup-history') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white text-gray-700 border border-gray-200 rounded-xl text-sm font-semibold hover:border-[#c8292a] hover:text-[#c8292a] hover:bg-red-50 transition-all duration-150 no-underline">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    Backup History
                </a>

                <a href="{{ route('configuration.restore-form') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white text-gray-700 border border-gray-200 rounded-xl text-sm font-semibold hover:border-[#c8292a] hover:text-[#c8292a] hover:bg-red-50 transition-all duration-150 no-underline">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 4v6h6M23 20v-6h-6"></path>
                        <path d="M20.49 9A9 9 0 0 0 5.64 5.64M3.51 15A9 9 0 0 0 18.36 18.36"></path>
                    </svg>
                    Restore Database
                </a>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════ --}}
    {{-- MAINTENANCE TAB --}}
    {{-- ════════════════════════════════════════ --}}
    <div id="maintenance" class="tab-panel hidden">
        <div class="bg-white border border-gray-200 rounded-xl px-5 sm:px-6 py-5">
            <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2.5">
                <svg class="w-5 h-5 text-[#c8292a]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2a10 10 0 1 1 0 20 10 10 0 0 1 0-20z"></path>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                Maintenance Mode
            </h3>

            <div class="flex items-center justify-between gap-4 px-4 py-4 bg-gray-50 border border-gray-200 rounded-xl flex-wrap">
                <div>
                    <p class="text-sm font-bold text-gray-900 mb-1">Maintenance mode</p>
                    <p class="text-xs text-gray-400">Temporarily disable user access while you work on the system</p>
                </div>
                <form action="{{ route('configuration.toggle-maintenance') }}" method="POST">
                    @csrf
                    <label class="relative inline-block w-12 h-6.5 cursor-pointer">
                        <input type="checkbox" id="maintenance_toggle" class="sr-only peer" onchange="this.form.submit()" {{ !empty($settings['maintenance_mode']) ? 'checked' : '' }}>
                        <span class="absolute inset-0 bg-gray-300 rounded-full transition-colors duration-300 peer-checked:bg-emerald-500"></span>
                        <span class="absolute left-0.5 top-0.5 w-5.5 h-5.5 bg-white rounded-full shadow transition-transform duration-300 peer-checked:translate-x-5.5"></span>
                    </label>
                </form>
            </div>

            <div class="mt-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold {{ !empty($settings['maintenance_mode']) ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
                    {{ !empty($settings['maintenance_mode']) ? 'Maintenance Mode: Active' : 'Maintenance Mode: Inactive' }}
                </span>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl px-5 sm:px-6 py-5">
            <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2.5">
                <svg class="w-5 h-5 text-[#c8292a]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2v20m10-10H2"></path>
                </svg>
                System utilities
            </h3>

            <div class="flex gap-2.5 flex-wrap">
                <form action="{{ route('configuration.clear-cache') }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white text-gray-700 border border-gray-200 rounded-xl text-sm font-semibold hover:border-[#c8292a] hover:text-[#c8292a] hover:bg-red-50 transition-all duration-150" onclick="return confirm('Clear system cache? This will temporarily affect performance.');">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 4 21 4 23 6 23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V6"></polyline>
                            <line x1="10" y1="12" x2="14" y2="12"></line>
                        </svg>
                        Clear Cache
                    </button>
                </form>

                <form action="{{ route('configuration.clear-logs') }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-500 text-white rounded-xl text-sm font-semibold hover:bg-red-600 transition-all duration-150" onclick="return confirm('Clear system logs? This action cannot be undone.');">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 4 21 4 23 6 23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V6"></polyline>
                            <line x1="10" y1="12" x2="14" y2="12"></line>
                        </svg>
                        Clear Logs
                    </button>
                </form>
            </div>

            <p class="text-xs text-gray-400 mt-4 leading-relaxed">
                <strong>Cache:</strong> Clearing cache will improve performance but may temporarily slow down the system.<br>
                <strong>Logs:</strong> This will delete all system activity logs. Make sure to archive them if needed.
            </p>
        </div>
    </div>
</div>

<script>
function switchTab(tabName, btn) {
    document.querySelectorAll('.tab-panel').forEach(function (tab) {
        tab.classList.add('hidden');
        tab.classList.remove('active');
    });
    document.querySelectorAll('[onclick*="switchTab"]').forEach(function (b) {
        b.classList.remove('bg-white', 'text-[#c8292a]', 'shadow-sm');
        b.classList.add('bg-transparent', 'text-gray-500');
    });
    var panel = document.getElementById(tabName);
    if (panel) {
        panel.classList.remove('hidden');
        panel.classList.add('active');
    }
    if (btn) {
        btn.classList.remove('bg-transparent', 'text-gray-500');
        btn.classList.add('bg-white', 'text-[#c8292a]', 'shadow-sm');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('[class*="bg-emerald-50"],[class*="bg-red-50"],[class*="bg-blue-50"]');
    alerts.forEach(function(alert) {
        if (alert.closest('.tab-panel') || alert.closest('[id="general"]')) return;
        setTimeout(function() {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            alert.style.transition = 'all 0.3s ease';
            setTimeout(function() { alert.remove(); }, 300);
        }, 5000);
    });
});
</script>
@endsection

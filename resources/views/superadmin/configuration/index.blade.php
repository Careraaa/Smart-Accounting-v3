@extends('layouts.layout')

@push('styles')
@include('superadmin.partials.prl-theme')
@endpush

@section('content')
<div class="col-12">
    <div class="prl-page">
    {{-- Topbar --}}
    <div class="prl-topbar">
        <div>
            <h1 class="prl-topbar-title">System configuration</h1>
            <p class="prl-topbar-sub">General settings, backups, and maintenance — same look as payroll tools</p>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if ($errors->any())
        <div class="prl-flash error">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    @if (session('success'))
        <div class="prl-flash success">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="prl-flash error">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="15" y1="9" x2="9" y2="15"></line>
                <line x1="9" y1="9" x2="15" y2="15"></line>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Tabs --}}
    <div class="prl-tabs-wrap">
    <div class="prl-tabs">
        <button type="button" class="prl-tab-btn active" onclick="switchTab('general', this)">General</button>
        <button type="button" class="prl-tab-btn" onclick="switchTab('backup', this)">Backup</button>
        <button type="button" class="prl-tab-btn" onclick="switchTab('maintenance', this)">Maintenance</button>
    </div>
    </div>

    {{-- ════════════════════════════════════════ --}}
    {{-- GENERAL SETTINGS TAB --}}
    {{-- ════════════════════════════════════════ --}}
    <div id="general" class="prl-tab-panel active">
        <div class="prl-cfg-block">
            <h3 class="prl-cfg-block-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="1"></circle>
                    <path d="M12 1v6m0 6v6"></path>
                    <path d="M4.22 4.22l4.24 4.24m2.12 2.12l4.24 4.24"></path>
                    <path d="M1 12h6m6 0h6"></path>
                    <path d="M4.22 19.78l4.24-4.24m2.12-2.12l4.24-4.24"></path>
                </svg>
                General Settings
            </h3>

            <form action="{{ route('configuration.update-general') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="prl-cfg-form-row">
                    <div class="prl-cfg-field">
                        <label for="system_name" class="prl-cfg-label">System name <span class="req">*</span></label>
                        <input type="text" id="system_name" name="system_name" class="prl-ctrl"
                               value="{{ $settings['system_name'] ?? 'Knights Transport' }}" required>
                        <p class="prl-cfg-desc">The official name of your organization</p>
                    </div>

                    <div class="prl-cfg-field">
                        <label for="timezone" class="prl-cfg-label">Timezone <span class="req">*</span></label>
                        <select id="timezone" name="timezone" class="prl-ctrl" required>
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
                        <p class="prl-cfg-desc">Select the timezone for your system</p>
                    </div>
                </div>

                <div class="prl-cfg-form-row">
                    <div class="prl-cfg-field">
                        <label for="date_format" class="prl-cfg-label">Date format <span class="req">*</span></label>
                        <select id="date_format" name="date_format" class="prl-ctrl" required>
                            <option value="Y-m-d" {{ ($settings['date_format'] ?? 'Y-m-d') === 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                            <option value="d-m-Y" {{ ($settings['date_format'] ?? '') === 'd-m-Y' ? 'selected' : '' }}>DD-MM-YYYY</option>
                            <option value="m-d-Y" {{ ($settings['date_format'] ?? '') === 'm-d-Y' ? 'selected' : '' }}>MM-DD-YYYY</option>
                            <option value="Y/m/d" {{ ($settings['date_format'] ?? '') === 'Y/m/d' ? 'selected' : '' }}>YYYY/MM/DD</option>
                            <option value="d/m/Y" {{ ($settings['date_format'] ?? '') === 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                            <option value="m/d/Y" {{ ($settings['date_format'] ?? '') === 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                        </select>
                        <p class="prl-cfg-desc">How dates will be displayed throughout the system</p>
                    </div>

                    <div class="prl-cfg-field">
                        <label for="language" class="prl-cfg-label">Language <span class="req">*</span></label>
                        <select id="language" name="language" class="prl-ctrl" required>
                            <option value="en" {{ ($settings['language'] ?? 'en') === 'en' ? 'selected' : '' }}>English</option>
                            <option value="es" {{ ($settings['language'] ?? '') === 'es' ? 'selected' : '' }}>Spanish (Español)</option>
                            <option value="fr" {{ ($settings['language'] ?? '') === 'fr' ? 'selected' : '' }}>French (Français)</option>
                            <option value="de" {{ ($settings['language'] ?? '') === 'de' ? 'selected' : '' }}>German (Deutsch)</option>
                            <option value="tl" {{ ($settings['language'] ?? '') === 'tl' ? 'selected' : '' }}>Tagalog</option>
                            <option value="fil" {{ ($settings['language'] ?? '') === 'fil' ? 'selected' : '' }}>Filipino</option>
                        </select>
                        <p class="prl-cfg-desc">Default language for the system interface</p>
                    </div>
                </div>

                <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:20px;">
                    <button type="submit" class="prl-btn-generate">
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
    <div id="backup" class="prl-tab-panel">
        <div class="prl-cfg-block">
            <h3 class="prl-cfg-block-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                Backup Configuration
            </h3>

            <form action="{{ route('configuration.update-backup') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="prl-cfg-form-row">
                    <div class="prl-cfg-field">
                        <label for="backup_frequency" class="prl-cfg-label">Backup frequency <span class="req">*</span></label>
                        <select id="backup_frequency" name="backup_frequency" class="prl-ctrl" required>
                            <option value="hourly" {{ ($settings['backup_frequency'] ?? 'daily') === 'hourly' ? 'selected' : '' }}>Hourly</option>
                            <option value="daily" {{ ($settings['backup_frequency'] ?? 'daily') === 'daily' ? 'selected' : '' }}>Daily</option>
                            <option value="weekly" {{ ($settings['backup_frequency'] ?? '') === 'weekly' ? 'selected' : '' }}>Weekly</option>
                            <option value="monthly" {{ ($settings['backup_frequency'] ?? '') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                        </select>
                        <p class="prl-cfg-desc">How often backups are created automatically</p>
                    </div>

                    <div class="prl-cfg-field">
                        <label for="backup_retention" class="prl-cfg-label">Retention (days) <span class="req">*</span></label>
                        <input type="number" id="backup_retention" name="backup_retention" class="prl-ctrl"
                               value="{{ $settings['backup_retention'] ?? 30 }}" min="1" max="365" required>
                        <p class="prl-cfg-desc">Number of days to keep backups (1–365)</p>
                    </div>
                </div>

                <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:20px;">
                    <button type="submit" class="prl-btn-generate">
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
        <div class="prl-cfg-block">
            <h3 class="prl-cfg-block-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2v20m10-10H2"></path>
                </svg>
                Quick actions
            </h3>

            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                <form action="{{ route('configuration.backup-now') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="prl-btn-success-solid">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"></path>
                        </svg>
                        Backup Now
                    </button>
                </form>

                <a href="{{ route('configuration.backup-history') }}" class="prl-btn-sec">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    Backup History
                </a>

                <a href="{{ route('configuration.restore-form') }}" class="prl-btn-sec">
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
    <div id="maintenance" class="prl-tab-panel">
        <div class="prl-cfg-block">
            <h3 class="prl-cfg-block-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2a10 10 0 1 1 0 20 10 10 0 0 1 0-20z"></path>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                Maintenance Mode
            </h3>

            <div class="prl-cfg-toolbar">
                <div>
                    <p style="margin: 0 0 4px; font-weight: 700; color: #111827;">Maintenance mode</p>
                    <p style="margin: 0; font-size: 0.8rem; color: #9ca3af;">Temporarily disable user access while you work on the system</p>
                </div>
                <form action="{{ route('configuration.toggle-maintenance') }}" method="POST" style="display: inline;">
                    @csrf
                    <label class="cfg-toggle-lg">
                        <input type="checkbox" id="maintenance_toggle" onchange="this.form.submit()"
                               {{ app()->isDownForMaintenance() ? 'checked' : '' }}>
                        <span class="cfg-slider"></span>
                    </label>
                </form>
            </div>

            <div>
                <div class="prl-cfg-status-pill {{ app()->isDownForMaintenance() ? 'on' : 'off' }}">
                    <span>
                        {{ app()->isDownForMaintenance() ? 'Maintenance Mode: Active' : 'Maintenance Mode: Inactive' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="prl-cfg-block">
            <h3 class="prl-cfg-block-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2v20m10-10H2"></path>
                </svg>
                System utilities
            </h3>

            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                <form action="{{ route('configuration.clear-cache') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="prl-btn-sec" onclick="return confirm('Clear system cache? This will temporarily affect performance.');">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 4 21 4 23 6 23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V6"></polyline>
                            <line x1="10" y1="12" x2="14" y2="12"></line>
                        </svg>
                        Clear Cache
                    </button>
                </form>

                <form action="{{ route('configuration.clear-logs') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="prl-btn-danger-solid" onclick="return confirm('Clear system logs? This action cannot be undone.');">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 4 21 4 23 6 23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V6"></polyline>
                            <line x1="10" y1="12" x2="14" y2="12"></line>
                        </svg>
                        Clear Logs
                    </button>
                </form>
            </div>

            <p style="margin-top: 16px; font-size: 0.8rem; color: #9ca3af;">
                <strong>Cache:</strong> Clearing cache will improve performance but may temporarily slow down the system.
                <br><strong>Logs:</strong> This will delete all system activity logs. Make sure to archive them if needed.
            </p>
        </div>
    </div>
</div>
</div>

@endsection

@push('scripts')
<script>
function switchTab(tabName, btn) {
    document.querySelectorAll('.prl-tab-panel').forEach(function (tab) {
        tab.classList.remove('active');
    });
    document.querySelectorAll('.prl-tab-btn').forEach(function (b) {
        b.classList.remove('active');
    });
    var panel = document.getElementById(tabName);
    if (panel) panel.classList.add('active');
    if (btn) btn.classList.add('active');
}

document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.prl-flash');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
});
</script>
@endpush

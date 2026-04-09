@extends('layouts.layout')

@section('content')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&display=swap');
.config-page { font-family: 'Sora', sans-serif; max-width: 1200px; margin: 0 auto; }

/* ── Topbar ─────────────────────────────────────────────────── */
.config-topbar { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 24px; flex-wrap: wrap; }
.config-topbar-title { font-size: 1.35rem; font-weight: 800; color: #111827; letter-spacing: -0.02em; margin: 0 0 2px; }
.config-topbar-sub { font-size: 0.78rem; color: #9ca3af; margin: 0; }

/* ── Flash Messages ─────────────────────────────────────────── */
.config-alert { display: flex; align-items: center; gap: 12px; padding: 14px 16px; border-radius: 10px; font-size: 0.82rem; font-weight: 500; margin-bottom: 20px; animation: alertSlide 0.3s ease; }
.config-alert.success { background: #f0fdf4; border-left: 4px solid #16a34a; color: #15803d; }
.config-alert.error { background: #fff0f0; border-left: 4px solid #ef4444; color: #c8292a; }
.config-alert.info { background: #eff6ff; border-left: 4px solid #0284c7; color: #1d4ed8; }
@keyframes alertSlide { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

/* ── Tabs ──────────────────────────────────────────────────── */
.config-tabs { display: flex; gap: 0; border-bottom: 2px solid #e5e7eb; margin-bottom: 24px; overflow-x: auto; }
.config-tab-btn {
    padding: 12px 20px;
    background: none;
    border: none;
    border-bottom: 3px solid transparent;
    color: #6b7280;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
    font-family: 'Sora', sans-serif;
    position: relative;
    margin-bottom: -2px;
}
.config-tab-btn:hover { color: #111827; }
.config-tab-btn.active { color: #c8292a; border-bottom-color: #c8292a; }

.config-tab-content { display: none; }
.config-tab-content.active { display: block; }

/* ── Form Sections ─────────────────────────────────────────── */
.config-section { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; margin-bottom: 20px; }
.config-section-title { font-size: 0.95rem; font-weight: 700; color: #111827; margin: 0 0 16px; display: flex; align-items: center; gap: 8px; }
.config-icon { width: 20px; height: 20px; color: #c8292a; }

.config-form-group { margin-bottom: 18px; }
.config-form-group:last-child { margin-bottom: 0; }

.config-label {
    display: block;
    font-size: 0.82rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
}
.config-label .required { color: #c8292a; }
.config-desc { font-size: 0.75rem; color: #9ca3af; margin-top: 4px; }

.config-input,
.config-select {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 0.85rem;
    font-family: 'Sora', sans-serif;
    color: #111827;
    background: #f9fafb;
    transition: all 0.2s;
}
.config-input:focus,
.config-select:focus {
    outline: none;
    border-color: #c8292a;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(200, 41, 42, 0.08);
}

.config-form-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px; }

/* ── Buttons ──────────────────────────────────────────────── */
.config-btn-group { display: flex; gap: 10px; flex-wrap: wrap; }

.config-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    border: none;
    border-radius: 8px;
    font-size: 0.82rem;
    font-weight: 600;
    font-family: 'Sora', sans-serif;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
    text-decoration: none;
}

.config-btn-primary {
    background: #c8292a;
    color: #fff;
}
.config-btn-primary:hover {
    background: #b02323;
}

.config-btn-secondary {
    background: #f3f4f6;
    color: #111827;
    border: 1px solid #e5e7eb;
}
.config-btn-secondary:hover {
    background: #e5e7eb;
}

.config-btn-danger {
    background: #ef4444;
    color: #fff;
}
.config-btn-danger:hover {
    background: #dc2626;
}

.config-btn-success {
    background: #16a34a;
    color: #fff;
}
.config-btn-success:hover {
    background: #15803d;
}

/* ── Toggle Switch ─────────────────────────────────────────── */
.config-toggle {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 28px;
}

.config-toggle input {
    opacity: 0;
    width: 0;
    height: 0;
}

.config-toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #d1d5db;
    transition: 0.3s;
    border-radius: 28px;
}

.config-toggle-slider:before {
    position: absolute;
    content: "";
    height: 22px;
    width: 22px;
    left: 3px;
    bottom: 3px;
    background-color: #fff;
    transition: 0.3s;
    border-radius: 50%;
}

input:checked + .config-toggle-slider {
    background-color: #16a34a;
}

input:checked + .config-toggle-slider:before {
    transform: translateX(22px);
}

/* ── Status Indicator ──────────────────────────────────────── */
.config-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}
.config-status.active {
    background: #f0fdf4;
    color: #16a34a;
    border: 1px solid #bbf7d0;
}
.config-status.inactive {
    background: #fff0f0;
    color: #c8292a;
    border: 1px solid #fecaca;
}
.config-status-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    display: inline-block;
}
.config-status.active .config-status-dot {
    background: #16a34a;
}
.config-status.inactive .config-status-dot {
    background: #ef4444;
}

/* ── Table for History ─────────────────────────────────────── */
.config-table-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; }
.config-table { width: 100%; border-collapse: collapse; font-size: 0.82rem; }
.config-table thead { background: #f8f9fb; }
.config-table th {
    padding: 12px 16px;
    text-align: left;
    font-size: 0.75rem;
    font-weight: 700;
    color: #6b7280;
    border-bottom: 1px solid #e5e7eb;
}
.config-table td {
    padding: 12px 16px;
    border-bottom: 1px solid #f3f4f6;
    color: #374151;
}
.config-table tbody tr:hover { background: #fafafa; }
</style>
@endpush

<div class="col-12">
    <div class="config-page">
    {{-- Topbar --}}
    <div class="config-topbar">
        <div>
            <h1 class="config-topbar-title">System Configuration</h1>
            <p class="config-topbar-sub">Manage system settings, backups, and maintenance</p>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if ($errors->any())
        <div class="config-alert error">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    @if (session('success'))
        <div class="config-alert success">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="config-alert error">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="15" y1="9" x2="9" y2="15"></line>
                <line x1="9" y1="9" x2="15" y2="15"></line>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Tabs --}}
    <div class="config-tabs">
        <button class="config-tab-btn active" onclick="switchTab('general')">General Settings</button>
        <button class="config-tab-btn" onclick="switchTab('backup')">Backup Settings</button>
        <button class="config-tab-btn" onclick="switchTab('maintenance')">Maintenance</button>
    </div>

    {{-- ════════════════════════════════════════ --}}
    {{-- GENERAL SETTINGS TAB --}}
    {{-- ════════════════════════════════════════ --}}
    <div id="general" class="config-tab-content active">
        <div class="config-section">
            <h3 class="config-section-title">
                <svg class="config-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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

                <div class="config-form-row">
                    <div class="config-form-group">
                        <label for="system_name" class="config-label">System Name <span class="required">*</span></label>
                        <input type="text" id="system_name" name="system_name" class="config-input"
                               value="{{ $settings['system_name'] ?? 'Knights Transport' }}" required>
                        <p class="config-desc">The official name of your organization</p>
                    </div>

                    <div class="config-form-group">
                        <label for="timezone" class="config-label">Timezone <span class="required">*</span></label>
                        <select id="timezone" name="timezone" class="config-select" required>
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
                        <p class="config-desc">Select the timezone for your system</p>
                    </div>
                </div>

                <div class="config-form-row">
                    <div class="config-form-group">
                        <label for="date_format" class="config-label">Date Format <span class="required">*</span></label>
                        <select id="date_format" name="date_format" class="config-select" required>
                            <option value="Y-m-d" {{ ($settings['date_format'] ?? 'Y-m-d') === 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                            <option value="d-m-Y" {{ ($settings['date_format'] ?? '') === 'd-m-Y' ? 'selected' : '' }}>DD-MM-YYYY</option>
                            <option value="m-d-Y" {{ ($settings['date_format'] ?? '') === 'm-d-Y' ? 'selected' : '' }}>MM-DD-YYYY</option>
                            <option value="Y/m/d" {{ ($settings['date_format'] ?? '') === 'Y/m/d' ? 'selected' : '' }}>YYYY/MM/DD</option>
                            <option value="d/m/Y" {{ ($settings['date_format'] ?? '') === 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                            <option value="m/d/Y" {{ ($settings['date_format'] ?? '') === 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                        </select>
                        <p class="config-desc">How dates will be displayed throughout the system</p>
                    </div>

                    <div class="config-form-group">
                        <label for="language" class="config-label">Language <span class="required">*</span></label>
                        <select id="language" name="language" class="config-select" required>
                            <option value="en" {{ ($settings['language'] ?? 'en') === 'en' ? 'selected' : '' }}>English</option>
                            <option value="es" {{ ($settings['language'] ?? '') === 'es' ? 'selected' : '' }}>Spanish (Español)</option>
                            <option value="fr" {{ ($settings['language'] ?? '') === 'fr' ? 'selected' : '' }}>French (Français)</option>
                            <option value="de" {{ ($settings['language'] ?? '') === 'de' ? 'selected' : '' }}>German (Deutsch)</option>
                            <option value="tl" {{ ($settings['language'] ?? '') === 'tl' ? 'selected' : '' }}>Tagalog</option>
                            <option value="fil" {{ ($settings['language'] ?? '') === 'fil' ? 'selected' : '' }}>Filipino</option>
                        </select>
                        <p class="config-desc">Default language for the system interface</p>
                    </div>
                </div>

                <div class="config-btn-group" style="margin-top: 24px;">
                    <button type="submit" class="config-btn config-btn-primary">
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
    <div id="backup" class="config-tab-content">
        <div class="config-section">
            <h3 class="config-section-title">
                <svg class="config-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                Backup Configuration
            </h3>

            <form action="{{ route('configuration.update-backup') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="config-form-row">
                    <div class="config-form-group">
                        <label for="backup_frequency" class="config-label">Backup Frequency <span class="required">*</span></label>
                        <select id="backup_frequency" name="backup_frequency" class="config-select" required>
                            <option value="hourly" {{ ($settings['backup_frequency'] ?? 'daily') === 'hourly' ? 'selected' : '' }}>Hourly</option>
                            <option value="daily" {{ ($settings['backup_frequency'] ?? 'daily') === 'daily' ? 'selected' : '' }}>Daily</option>
                            <option value="weekly" {{ ($settings['backup_frequency'] ?? '') === 'weekly' ? 'selected' : '' }}>Weekly</option>
                            <option value="monthly" {{ ($settings['backup_frequency'] ?? '') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                        </select>
                        <p class="config-desc">How often backups are created automatically</p>
                    </div>

                    <div class="config-form-group">
                        <label for="backup_retention" class="config-label">Retention Period (days) <span class="required">*</span></label>
                        <input type="number" id="backup_retention" name="backup_retention" class="config-input"
                               value="{{ $settings['backup_retention'] ?? 30 }}" min="1" max="365" required>
                        <p class="config-desc">Number of days to keep backups (1-365 days)</p>
                    </div>
                </div>

                <div class="config-btn-group" style="margin-top: 24px;">
                    <button type="submit" class="config-btn config-btn-primary">
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
        <div class="config-section">
            <h3 class="config-section-title">
                <svg class="config-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2v20m10-10H2"></path>
                </svg>
                Quick Actions
            </h3>

            <div class="config-btn-group">
                <form action="{{ route('configuration.backup-now') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="config-btn config-btn-success">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"></path>
                        </svg>
                        Backup Now
                    </button>
                </form>

                <a href="{{ route('configuration.backup-history') }}" class="config-btn config-btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    Backup History
                </a>

                <a href="{{ route('configuration.restore-form') }}" class="config-btn config-btn-secondary">
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
    <div id="maintenance" class="config-tab-content">
        <div class="config-section">
            <h3 class="config-section-title">
                <svg class="config-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2a10 10 0 1 1 0 20 10 10 0 0 1 0-20z"></path>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                Maintenance Mode
            </h3>

            <div class="config-form-group" style="display: flex; align-items: center; justify-content: space-between; padding: 16px; background: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb;">
                <div>
                    <p style="margin: 0 0 4px; font-weight: 600; color: #111827;">Enable Maintenance Mode</p>
                    <p style="margin: 0; font-size: 0.8rem; color: #9ca3af;">Temporarily disable user access while performing maintenance</p>
                </div>
                <form action="{{ route('configuration.toggle-maintenance') }}" method="POST" style="display: inline;">
                    @csrf
                    <label class="config-toggle">
                        <input type="checkbox" id="maintenance_toggle" onchange="this.form.submit()"
                               {{ app()->isDownForMaintenance() ? 'checked' : '' }}>
                        <span class="config-toggle-slider"></span>
                    </label>
                </form>
            </div>

            <div style="margin-top: 16px;">
                <div class="config-status {{ app()->isDownForMaintenance() ? 'active' : 'inactive' }}">
                    <span class="config-status-dot"></span>
                    <span>
                        {{ app()->isDownForMaintenance() ? 'Maintenance Mode: Active' : 'Maintenance Mode: Inactive' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- System Maintenance --}}
        <div class="config-section">
            <h3 class="config-section-title">
                <svg class="config-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2v20m10-10H2"></path>
                </svg>
                System Utilities
            </h3>

            <div class="config-btn-group">
                <form action="{{ route('configuration.clear-cache') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="config-btn config-btn-secondary" onclick="return confirm('Clear system cache? This will temporarily affect performance.');">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 4 21 4 23 6 23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V6"></polyline>
                            <line x1="10" y1="12" x2="14" y2="12"></line>
                        </svg>
                        Clear Cache
                    </button>
                </form>

                <form action="{{ route('configuration.clear-logs') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="config-btn config-btn-danger" onclick="return confirm('Clear system logs? This action cannot be undone.');">
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
function switchTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.config-tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Remove active class from all buttons
    document.querySelectorAll('.config-tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Show selected tab
    document.getElementById(tabName).classList.add('active');
    
    // Add active class to clicked button
    event.target.classList.add('active');
}

// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.config-alert');
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

@extends('layouts.layout')

@section('content')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&display=swap');
.restore-page { font-family: 'Sora', sans-serif; max-width: 1200px; margin: 0 auto; }

.restore-topbar { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 24px; flex-wrap: wrap; }
.restore-topbar-title { font-size: 1.35rem; font-weight: 800; color: #111827; letter-spacing: -0.02em; margin: 0 0 2px; }
.restore-topbar-sub { font-size: 0.78rem; color: #9ca3af; margin: 0; }

.restore-btn-back {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    background: #f3f4f6;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 0.82rem;
    font-weight: 600;
    font-family: 'Sora', sans-serif;
    color: #111827;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s;
}
.restore-btn-back:hover { background: #e5e7eb; }

.restore-alert { display: flex; align-items: flex-start; gap: 12px; padding: 14px 16px; border-radius: 10px; font-size: 0.82rem; margin-bottom: 20px; }
.restore-alert.warning {
    background: #fef3c7;
    border-left: 4px solid #d97706;
    color: #92400e;
}

.restore-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; }
.restore-form-group { margin-bottom: 18px; }
.restore-form-group:last-child { margin-bottom: 0; }

.restore-label {
    display: block;
    font-size: 0.82rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
}
.restore-label .required { color: #ef4444; }
.restore-desc { font-size: 0.75rem; color: #9ca3af; margin-top: 4px; }

.restore-select {
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
.restore-select:focus {
    outline: none;
    border-color: #c8292a;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(200, 41, 42, 0.08);
}

.restore-btn {
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
}

.restore-btn-danger {
    background: #ef4444;
    color: #fff;
}
.restore-btn-danger:hover { background: #dc2626; }

.restore-btn-secondary {
    background: #f3f4f6;
    color: #111827;
    border: 1px solid #e5e7eb;
}
.restore-btn-secondary:hover { background: #e5e7eb; }

.restore-btn-group { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 24px; }

.restore-info {
    display: flex;
    gap: 12px;
    padding: 12px 16px;
    background: #f0f9ff;
    border-left: 3px solid #0284c7;
    border-radius: 8px;
    margin-top: 16px;
    font-size: 0.8rem;
    color: #1d4ed8;
}
</style>
@endpush

<div class="col-12">
    <div class="restore-page">
    {{-- Topbar --}}
    <div class="restore-topbar">
        <div>
            <h1 class="restore-topbar-title">Restore Database</h1>
            <p class="restore-topbar-sub">Restore your system from a backup</p>
        </div>
        <a href="{{ route('configuration.index') }}" class="restore-btn-back">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Back to Configuration
        </a>
    </div>

    {{-- Warning Alert --}}
    <div class="restore-alert warning">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0; margin-top: 2px;">
            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3.05h16.94a2 2 0 0 0 1.71-3.05L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
            <line x1="12" y1="9" x2="12" y2="13"></line>
            <line x1="12" y1="17" x2="12.01" y2="17"></line>
        </svg>
        <div>
            <strong>Important:</strong> Restoring a backup will overwrite all current data with the backup data. This action cannot be undone. Please ensure you have saved all recent changes before proceeding.
        </div>
    </div>

    {{-- Restore Form --}}
    <div class="restore-card">
        <form action="{{ route('configuration.restore') }}" method="POST" onsubmit="return confirm('This will restore your database from the selected backup. All current data will be lost. Are you sure?');">
            @csrf

            <div class="restore-form-group">
                <label for="backup_file" class="restore-label">
                    Select Backup File <span class="required">*</span>
                </label>
                <select id="backup_file" name="backup_file" class="restore-select" required>
                    <option value="">-- Choose a backup --</option>
                    @foreach ($backups as $backup)
                        <option value="{{ $backup['filename'] }}">{{ $backup['filename'] }} ({{ $backup['type'] }})</option>
                    @endforeach
                </select>
                <p class="restore-desc">Select the backup file you want to restore from</p>
            </div>

            <div class="restore-info">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0;">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
                <div>
                    <strong>Restoration Process:</strong> The system will be temporarily unavailable during the restoration. Please do not refresh or close this page.
                </div>
            </div>

            <div class="restore-btn-group">
                <button type="submit" class="restore-btn restore-btn-danger">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 4v6h6M23 20v-6h-6"></path>
                        <path d="M20.49 9A9 9 0 0 0 5.64 5.64M3.51 15A9 9 0 0 0 18.36 18.36"></path>
                    </svg>
                    Restore Database
                </button>

                <a href="{{ route('configuration.index') }}" class="restore-btn restore-btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
</div>

@endsection

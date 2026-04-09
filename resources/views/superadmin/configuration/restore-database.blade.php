@extends('layouts.layout')

@push('styles')
@include('superadmin.partials.prl-theme')
@endpush

@section('content')
<div class="col-12">
    <div class="prl-page">
        <div class="prl-topbar">
            <div>
                <h1 class="prl-topbar-title">Restore database</h1>
                <p class="prl-topbar-sub">Overwrite current data from a backup file</p>
            </div>
            <div class="prl-topbar-actions">
                <a href="{{ route('configuration.index') }}" class="prl-btn-sec">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Configuration
                </a>
            </div>
        </div>

        <div class="prl-warn-panel" style="margin-bottom:22px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:2px;">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3.05h16.94a2 2 0 0 0 1.71-3.05L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                <line x1="12" y1="9" x2="12" y2="13"></line>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
            <div>
                <strong>Destructive operation.</strong> Restoring replaces all current data with the backup. This cannot be undone. Export anything important first.
            </div>
        </div>

        <div class="prl-card">
            <div class="prl-card-head">
                <div class="prl-card-head-icon amber" aria-hidden="true">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </div>
                <div>
                    <p class="prl-card-head-title">Select backup</p>
                    <p class="prl-card-head-sub">The app will be unavailable during restore</p>
                </div>
            </div>
            <div class="prl-card-body">
                <form action="{{ route('configuration.restore') }}" method="POST" onsubmit="return confirm('Restore database from this file? All current data will be replaced.');">
                    @csrf

                    <div class="prl-field">
                        <label for="backup_file" class="prl-lbl">Backup file <span class="req">*</span></label>
                        <select id="backup_file" name="backup_file" class="prl-ctrl" required>
                            <option value="">— Choose a backup —</option>
                            @foreach ($backups as $backup)
                                <option value="{{ $backup['filename'] }}">{{ $backup['filename'] }} ({{ $backup['type'] }})</option>
                            @endforeach
                        </select>
                        <span class="prl-cfg-desc" style="display:block;margin-top:6px;">Pick a file from your backup list</span>
                    </div>

                    <div class="prl-info-panel" style="margin-top:8px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                        <div>Do not close this page during restoration.</div>
                    </div>

                    <div class="prl-form-footer" style="border-top:1px solid #f3f4f6;margin-top:20px;padding-top:20px;">
                        <a href="{{ route('configuration.index') }}" class="prl-btn-cancel">Cancel</a>
                        <button type="submit" class="prl-btn-danger-solid">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 4v6h6M23 20v-6h-6"></path>
                                <path d="M20.49 9A9 9 0 0 0 5.64 5.64M3.51 15A9 9 0 0 0 18.36 18.36"></path>
                            </svg>
                            Restore database
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

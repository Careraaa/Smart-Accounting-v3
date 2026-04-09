@extends('layouts.layout')

@push('styles')
@include('superadmin.partials.prl-theme')
@endpush

@section('content')
<div class="col-12">
    <div class="prl-page">
        <div class="prl-topbar">
            <div>
                <h1 class="prl-topbar-title">Backup history</h1>
                <p class="prl-topbar-sub">Download or remove stored database backups</p>
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

        <div class="prl-table-card">
            @if (count($backups) > 0)
                <div class="prl-table-scroll">
                    <table class="prl-table">
                        <thead>
                            <tr>
                                <th>File</th>
                                <th>Size</th>
                                <th>Created</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($backups as $backup)
                                <tr>
                                    <td>
                                        <span class="prl-mono" style="font-weight:600;color:#111827;">{{ $backup['filename'] }}</span>
                                    </td>
                                    <td>
                                        <span class="prl-mono" style="background:#f3f4f6;padding:4px 10px;border-radius:8px;font-size:0.78rem;">
                                            {{ number_format($backup['size'] / 1024 / 1024, 2) }} MB
                                        </span>
                                    </td>
                                    <td class="prl-mono" style="color:#6b7280;">{{ date('M d, Y H:i:s', $backup['created_at']) }}</td>
                                    <td>
                                        <div class="prl-actions" style="justify-content:flex-end;">
                                            <a href="{{ route('configuration.backup-download', ['filename' => $backup['filename']]) }}" class="prl-btn-sec" style="padding:8px 14px;">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                    <polyline points="7 10 12 15 17 10"></polyline>
                                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                                </svg>
                                                Download
                                            </a>
                                            <form action="{{ route('configuration.backup-delete', ['filename' => $backup['filename']]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this backup permanently?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="prl-btn-danger-solid" style="padding:8px 14px;">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="prl-empty">
                    <div class="prl-empty-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M12 2v20m10-10H2"></path>
                        </svg>
                    </div>
                    <p class="prl-empty-title">No backups yet</p>
                    <p class="prl-empty-sub">Run a backup from configuration to see files here</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

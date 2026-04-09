@extends('layouts.layout')

@section('content')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&display=swap');
.backup-history-page { font-family: 'Sora', sans-serif; max-width: 1200px; margin: 0 auto; }

.bh-topbar { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 24px; flex-wrap: wrap; }
.bh-topbar-title { font-size: 1.35rem; font-weight: 800; color: #111827; letter-spacing: -0.02em; margin: 0 0 2px; }
.bh-topbar-sub { font-size: 0.78rem; color: #9ca3af; margin: 0; }

.bh-btn-back {
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
.bh-btn-back:hover { background: #e5e7eb; }

.bh-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; }
.bh-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
.bh-table thead { background: #f8f9fb; }
.bh-table th {
    padding: 12px 16px;
    text-align: left;
    font-size: 0.75rem;
    font-weight: 700;
    color: #6b7280;
    border-bottom: 1px solid #e5e7eb;
}
.bh-table td {
    padding: 12px 16px;
    border-bottom: 1px solid #f3f4f6;
    color: #374151;
}
.bh-table tbody tr:hover { background: #fafafa; }

.bh-empty { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 48px 24px; text-align: center; }
.bh-empty-icon { width: 56px; height: 56px; background: #f3f4f6; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin-bottom: 14px; color: #d1d5db; }
.bh-empty-title { font-size: 0.95rem; font-weight: 700; color: #374151; margin: 0 0 6px; }
.bh-empty-sub { font-size: 0.8rem; color: #9ca3af; margin: 0; }

.bh-size-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    background: #f0f0f0;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    color: #374151;
    font-family: 'DM Mono', monospace;
}

.bh-btn-danger {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    background: #fee2e2;
    border: 1px solid #fecaca;
    border-radius: 8px;
    font-size: 0.82rem;
    font-weight: 600;
    font-family: 'Sora', sans-serif;
    color: #dc2626;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s;
}
.bh-btn-danger:hover { 
    background: #fca5a5;
    border-color: #f87171;
}
</style>
@endpush

<div class="col-12">
    <div class="backup-history-page">
    {{-- Topbar --}}
    <div class="bh-topbar">
        <div>
            <h1 class="bh-topbar-title">Backup History</h1>
            <p class="bh-topbar-sub">View all system backups</p>
        </div>
        <a href="{{ route('configuration.index') }}" class="bh-btn-back">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Back to Configuration
        </a>
    </div>

    {{-- Backups Table --}}
    <div class="bh-card">
        @if (count($backups) > 0)
            <div style="overflow-x: auto;">
                <table class="bh-table">
                    <thead>
                        <tr>
                            <th>Filename</th>
                            <th>Size</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($backups as $backup)
                            <tr>
                                <td>
                                    <strong>{{ $backup['filename'] }}</strong>
                                </td>
                                <td>
                                    <span class="bh-size-badge">
                                        {{ number_format($backup['size'] / 1024 / 1024, 2) }} MB
                                    </span>
                                </td>
                                <td>
                                    {{ date('M d, Y H:i:s', $backup['created_at']) }}
                                </td>
                                <td>
                                    <div style="display: flex; gap: 8px;">
                                        <a href="{{ route('configuration.backup-download', ['filename' => $backup['filename']]) }}" class="bh-btn-back" title="Download {{ $backup['filename'] }}">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                <polyline points="7 10 12 15 17 10"></polyline>
                                                <line x1="12" y1="15" x2="12" y2="3"></line>
                                            </svg>
                                            Download
                                        </a>
                                        <form action="{{ route('configuration.backup-delete', ['filename' => $backup['filename']]) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this backup? This action cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bh-btn-danger" title="Delete {{ $backup['filename'] }}">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                                </svg>
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
            <div class="bh-empty">
                <div class="bh-empty-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M12 2v20m10-10H2"></path>
                    </svg>
                </div>
                <p class="bh-empty-title">No backups found</p>
                <p class="bh-empty-sub">Start by creating your first backup</p>
            </div>
        @endif
    </div>
</div>
</div>

@endsection

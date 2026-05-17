@extends('layouts.layout')
@section('title', 'Bonuses')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
.bn-page { font-family: 'Sora', sans-serif; }
.bn-topbar { display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;flex-wrap:wrap; }
.bn-topbar-title { font-size:1.35rem;font-weight:800;color:#111827;letter-spacing:-0.02em;margin:0 0 2px; }
.bn-topbar-sub { font-size:0.78rem;color:#9ca3af;margin:0; }
.bn-topbar-actions { display:flex;gap:8px;flex-wrap:wrap;align-items:center; }
.bn-btn-primary { display:inline-flex;align-items:center;gap:7px;padding:9px 16px;background:#111827;color:#fff;border:none;border-radius:10px;font-family:'Sora',sans-serif;font-size:0.82rem;font-weight:600;text-decoration:none;cursor:pointer;transition:all 0.15s;white-space:nowrap; }
.bn-btn-primary:hover { background:#000;color:#fff; }
.bn-flash { display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:10px;font-size:0.82rem;font-weight:500;margin-bottom:20px; }
.bn-flash.success { background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d; }
.bn-flash.error { background:#fff0f0;border:1px solid #fecaca;color:#c8292a; }
.bn-toolbar { display:flex;align-items:center;gap:10px;margin-bottom:14px;flex-wrap:wrap; }
.bn-search { display:flex;align-items:center;gap:8px;flex:1;min-width:220px;max-width:360px; }
.bn-search input { width:100%;border:1px solid #e5e7eb;border-radius:10px;padding:9px 14px;font-size:0.82rem;font-family:'Sora',sans-serif;outline:none; }
.bn-search input:focus { border-color:#c8292a;box-shadow:0 0 0 3px rgba(200,41,42,0.08); }
.bn-filter-btn { display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:8px;font-size:0.78rem;font-weight:600;text-decoration:none;border:1px solid #e5e7eb;background:#fff;color:#6b7280; }
.bn-filter-btn.active { background:#111827;border-color:#111827;color:#fff; }
.bn-table-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden; }
.bn-table { width:100%;border-collapse:collapse;font-size:0.835rem; }
.bn-table thead tr { background:#f8f9fb;border-bottom:1px solid #e5e7eb; }
.bn-table thead th { padding:11px 16px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#6b7280;white-space:nowrap; }
.bn-table tbody tr { border-bottom:1px solid #f3f4f6; }
.bn-table tbody tr.bn-row-clickable { cursor:pointer;transition:background 0.1s; }
.bn-table tbody tr.bn-row-clickable:hover { background:#f3f4f6; }
.bn-table tbody td { padding:12px 16px;color:#374151;vertical-align:middle; }
.bn-name { font-weight:600;color:#111827; }
.bn-mandatory { display:inline-block;font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;background:#fff7ed;color:#c2410c;border:1px solid #fed7aa;padding:2px 7px;border-radius:20px;margin-left:6px;vertical-align:middle; }
.bn-formula { font-family:'DM Mono',monospace;font-size:0.75rem;color:#6b7280;max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap; }
.bn-badge { display:inline-block;padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:700; }
.bn-badge.active { background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0; }
.bn-badge.inactive { background:#f3f4f6;color:#6b7280;border:1px solid #e5e7eb; }
.bn-empty { padding:56px 24px;text-align:center;color:#9ca3af; }
</style>
@endpush

@section('content')
<div class="bn-page">
    @if(session('success'))
    <div class="bn-flash success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="bn-flash error">{{ session('error') }}</div>
    @endif

    <div class="bn-topbar">
        <div>
            <h1 class="bn-topbar-title">Bonuses</h1>
            <p class="bn-topbar-sub">Manage employee bonus types and computation rules</p>
        </div>
        <div class="bn-topbar-actions">
            <a href="{{ route('bonuses.create') }}" class="bn-btn-primary">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Create Bonus
            </a>
        </div>
    </div>

    <div class="bn-toolbar">
        <form method="GET" action="{{ route('bonuses.index') }}" class="bn-search">
            <input type="hidden" name="status" value="{{ $status }}">
            <input type="search" name="search" value="{{ $search }}" placeholder="Search bonuses…">
        </form>
        <a href="{{ route('bonuses.index', ['status' => 'all', 'search' => $search]) }}" class="bn-filter-btn {{ $status === 'all' ? 'active' : '' }}">All</a>
        <a href="{{ route('bonuses.index', ['status' => 'active', 'search' => $search]) }}" class="bn-filter-btn {{ $status === 'active' ? 'active' : '' }}">Active</a>
        <a href="{{ route('bonuses.index', ['status' => 'inactive', 'search' => $search]) }}" class="bn-filter-btn {{ $status === 'inactive' ? 'active' : '' }}">Inactive</a>
    </div>

    <div class="bn-table-card">
        <table class="bn-table">
            <thead>
                <tr>
                    <th>Bonus Name</th>
                    <th>Bonus Type</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bonuses as $bonus)
                <tr class="bn-row-clickable" data-href="{{ $bonus->rowUrl() }}" tabindex="0" role="link" aria-label="Open {{ $bonus->name }}">
                    <td>
                        <div class="bn-name">
                            {{ $bonus->name }}
                        </div>
                    </td>
                    <td>{{ $bonus->type_label }}</td>
                    <td>
                        <span class="bn-badge {{ $bonus->status }}">{{ ucfirst($bonus->status) }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="bn-empty">No bonuses found. Create one to get started.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($bonuses->hasPages())
    <div style="margin-top:16px;">{{ $bonuses->links() }}</div>
    @endif
</div>

@push('scripts')
<script>
document.querySelectorAll('tr.bn-row-clickable').forEach(function (row) {
    row.addEventListener('click', function () {
        window.location.href = row.dataset.href;
    });
    row.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            window.location.href = row.dataset.href;
        }
    });
});
</script>
@endpush
@endsection


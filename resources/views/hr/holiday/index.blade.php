@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
.hld-page { font-family: 'Sora', sans-serif; }

/* ── Topbar ─────────────────────────────────────────────────── */
.hld-topbar { display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;flex-wrap:wrap; }
.hld-topbar-title { font-size:1.35rem;font-weight:800;color:#111827;letter-spacing:-0.02em;margin:0 0 2px; }
.hld-topbar-sub   { font-size:0.78rem;color:#9ca3af;margin:0; }
.hld-topbar-actions { display:flex;gap:8px;flex-wrap:wrap;align-items:center; }

.hld-btn-primary {
    display:inline-flex;align-items:center;gap:7px;padding:9px 18px;
    background:#c8292a;color:#fff;border:none;border-radius:10px;
    font-family:'Sora',sans-serif;font-size:0.82rem;font-weight:600;
    text-decoration:none;cursor:pointer;transition:background 0.15s;white-space:nowrap;
}
.hld-btn-primary:hover { background:#a81f20;color:#fff; }

.hld-btn-sec {
    display:inline-flex;align-items:center;gap:7px;padding:9px 16px;background:#fff;color:#374151;
    border:1px solid #e5e7eb;border-radius:10px;font-family:'Sora',sans-serif;font-size:0.82rem;
    font-weight:600;text-decoration:none;cursor:pointer;transition:all 0.15s;white-space:nowrap;
}
.hld-btn-sec:hover { border-color:#c8292a;color:#c8292a;background:#fff5f5; }

/* ── Flash messages ─────────────────────────────────────────── */
.hld-flash { display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:10px;font-size:0.82rem;font-weight:500;margin-bottom:20px;animation:hldFlashIn 0.3s ease; }
.hld-flash.success { background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d; }
.hld-flash.error   { background:#fff0f0;border:1px solid #fecaca;color:#c8292a; }
@keyframes hldFlashIn { from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)} }

/* ── Filters ────────────────────────────────────────────────── */
.hld-filter-bar { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:12px 16px;display:flex;align-items:center;gap:10px;margin-bottom:14px;flex-wrap:wrap; }
.hld-filter-select { border:1px solid #e5e7eb;border-radius:8px;padding:8px 12px;font-size:0.82rem;font-family:'Sora',sans-serif;color:#374151;background:#f9fafb;outline:none;cursor:pointer;transition:border-color 0.15s; }
.hld-filter-select:focus { border-color:#c8292a; }

.hld-filter-btn { display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border:1px solid #e5e7eb;background:#fff;color:#374151;border-radius:8px;font-family:'Sora',sans-serif;font-size:0.8rem;font-weight:600;cursor:pointer;white-space:nowrap;transition:all 0.15s; }
.hld-filter-btn:hover { border-color:#c8292a;color:#c8292a;background:#fff5f5; }
.hld-filter-btn.active { background:#c8292a;color:#fff;border-color:#c8292a; }

/* ── Table ──────────────────────────────────────────────────── */
.hld-table-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden; }
.hld-table { width:100%;border-collapse:collapse;font-size:0.835rem; }
.hld-table thead tr { background:#f8f9fb;border-bottom:1px solid #e5e7eb; }
.hld-table thead th { padding:11px 16px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#6b7280;white-space:nowrap; }
.hld-table tbody tr { border-bottom:1px solid #f3f4f6;transition:background 0.1s; }
.hld-table tbody tr:last-child { border-bottom:none; }
.hld-table tbody tr:hover { background:#fdf4f4; }
.hld-table tbody td { padding:12px 16px;color:#374151;vertical-align:middle; }

.hld-name { font-weight:600;color:#111827;font-size:0.845rem; }
.hld-type { display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;white-space:nowrap; }
.hld-type.regular { background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0; }
.hld-type.special { background:#fffbeb;color:#d97706;border:1px solid #fde68a; }
.hld-date { font-family:'DM Mono',monospace;font-size:0.78rem;color:#6b7280; }
.hld-desc { font-size:0.78rem;color:#9ca3af; }

.hld-actions { display:flex;align-items:center;gap:5px;justify-content:flex-end; }
.hld-action-btn { width:30px;height:30px;border-radius:7px;border:none;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;text-decoration:none;font-size:13px;transition:background 0.13s,color 0.13s;background:#f4f5f7;color:#6b7280;padding:0; }
.hld-action-btn:hover { background:#eff6ff;color:#3b82f6; }
.hld-action-btn.danger:hover { background:#fff1f2;color:#e11d48; }

/* ── Empty state ────────────────────────────────────────────── */
.hld-empty { display:flex;flex-direction:column;align-items:center;justify-content:center;padding:56px 24px;text-align:center; }
.hld-empty-icon { width:56px;height:56px;background:#f3f4f6;border-radius:16px;display:flex;align-items:center;justify-content:center;margin-bottom:14px;color:#d1d5db; }
.hld-empty-title { font-size:0.9rem;font-weight:700;color:#374151;margin:0 0 6px; }
.hld-empty-sub { font-size:0.78rem;color:#9ca3af;margin:0; }
</style>
@endpush

@section('content')
<div class="hld-page">

    {{-- Flash messages --}}
    @foreach(['success','error'] as $t)
        @if(session($t))
        <div class="hld-flash {{ $t }}">
            @if($t==='success')
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            @else
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
            @endif
            {{ session($t) }}
        </div>
        @endif
    @endforeach

    {{-- Topbar --}}
    <div class="hld-topbar">
        <div>
            <h1 class="hld-topbar-title">Holiday Management</h1>
            <p class="hld-topbar-sub">Configure holidays for payroll computation</p>
        </div>
        <div class="hld-topbar-actions">
            <a href="{{ route('holiday.calendar') }}" class="hld-btn-sec">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                Calendar
            </a>
            <a href="{{ route('holiday.create') }}" class="hld-btn-primary">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Add Holiday
            </a>
        </div>
    </div>

    {{-- Filter bar --}}
    <div class="hld-filter-bar">
        <a href="{{ route('holiday.index', ['status' => 'all']) }}"
           class="hld-filter-btn {{ $status === 'all' ? 'active' : '' }}">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            All Holidays
        </a>
        <a href="{{ route('holiday.index', ['status' => 'active']) }}"
           class="hld-filter-btn {{ $status === 'active' ? 'active' : '' }}">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Active & Upcoming
        </a>
        <select class="hld-filter-select" onchange="window.location='{{ route('holiday.index') }}?status={{ $status }}&year=' + this.value">
            @foreach($availableYears as $yr)
                <option value="{{ $yr }}" @selected($year == $yr)>{{ $yr }}</option>
            @endforeach
        </select>
    </div>

    {{-- Table --}}
    <div class="hld-table-card">
        <div style="overflow-x:auto;">
            <table class="hld-table">
                <thead>
                    <tr>
                        <th>Holiday Name</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($holidays as $holiday)
                    <tr>
                        <td>
                            <div class="hld-name">{{ $holiday->name }}</div>
                        </td>
                        <td>
                            <span class="hld-date">{{ $holiday->date->format('l, M d, Y') }}</span>
                        </td>
                        <td>
                            <span class="hld-type {{ $holiday->type }}">
                                {{ ucfirst($holiday->type) }}
                            </span>
                        </td>
                        <td>
                            <div class="hld-actions">
                                <a href="{{ route('holiday.edit', $holiday) }}" class="hld-action-btn" title="Edit">
                                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="{{ route('holiday.destroy', $holiday) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this holiday?')">
                                    @csrf @method('DELETE')
                                    <button class="hld-action-btn danger" title="Delete">
                                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">
                            <div class="hld-empty">
                                <div class="hld-empty-icon">
                                    <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <p class="hld-empty-title">No holidays found</p>
                                <p class="hld-empty-sub">Add a holiday to get started.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($holidays->hasPages())
        <div style="padding:14px 16px;border-top:1px solid #f3f4f6;">
            {{ $holidays->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

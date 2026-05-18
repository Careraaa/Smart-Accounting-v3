@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
.ntf-page{font-family:'Sora',sans-serif;padding-top:22px}
.ntf-topbar{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;flex-wrap:wrap}
.ntf-title{font-size:1.35rem;font-weight:800;color:#111827;letter-spacing:-0.02em;margin:0 0 2px}
.ntf-sub{font-size:.78rem;color:#9ca3af;margin:0}
.ntf-actions{display:flex;gap:8px;flex-wrap:wrap;align-items:center}
.ntf-filters{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px}
.ntf-btn{display:inline-flex;align-items:center;gap:7px;padding:9px 16px;background:#fff;color:#374151;border:1px solid #e5e7eb;border-radius:10px;font-family:'Sora',sans-serif;font-size:.82rem;font-weight:700;text-decoration:none;cursor:pointer;transition:all .15s;white-space:nowrap}
.ntf-btn:hover{border-color:#c8292a;color:#c8292a;background:#fff5f5}
.ntf-btn.active{border-color:#c8292a;color:#c8292a;background:#fff5f5}
.ntf-btn-primary{display:inline-flex;align-items:center;gap:10px;padding:11px 20px;background:#c8292a;color:#fff;border:none;border-radius:12px;font-family:'Sora',sans-serif;font-size:.86rem;font-weight:800;cursor:pointer;transition:background .15s,box-shadow .15s;box-shadow:0 4px 20px rgba(200,41,42,.5);white-space:nowrap;text-decoration:none}
.ntf-btn-primary:hover{background:#a81f20;color:#fff;box-shadow:0 10px 34px rgba(200,41,42,.62)}
.ntf-card{background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden}
.ntf-card-head{padding:14px 18px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap}
.ntf-card-title{font-size:.82rem;font-weight:900;color:#111827;margin:0;display:flex;align-items:center;gap:8px}
.ntf-dot{width:8px;height:8px;border-radius:50%;background:#c8292a;display:inline-block}
.ntf-card-body{padding:0}
.ntf-row{display:flex;gap:12px;align-items:flex-start;padding:14px 18px;border-bottom:1px solid #f3f4f6;transition:background .12s}
.ntf-row:hover{background:#fafafa}
.ntf-row:last-child{border-bottom:none}
.ntf-badge{display:inline-flex;align-items:center;gap:6px;padding:3px 10px;border-radius:20px;font-size:.68rem;font-weight:900;text-transform:uppercase;letter-spacing:.06em;white-space:nowrap}
.ntf-badge.unread{background:#fff0f0;color:#c8292a;border:1px solid #fecaca}
.ntf-badge.read{background:#f3f4f6;color:#6b7280}
.ntf-time{font-size:.74rem;color:#9ca3af;font-family:'DM Mono',monospace}
.ntf-title2{font-size:.9rem;font-weight:900;color:#111827;margin:0 0 4px}
.ntf-msg{font-size:.85rem;color:#6b7280;margin:0;line-height:1.45}
.ntf-actions2{margin-left:auto;display:flex;gap:8px;align-items:center}
.ntf-iconbtn{width:32px;height:32px;border-radius:9px;border:none;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;transition:background .12s;color:#6b7280;background:#f4f5f7}
.ntf-iconbtn:hover{background:#eff6ff;color:#3b82f6}
.ntf-empty{padding:56px 24px;text-align:center;color:#9ca3af}
</style>
@endpush

@section('content')
@php
    $currentView = $filter['view'] ?? 'all';
@endphp
<div class="ntf-page">
    <div class="ntf-topbar">
        <div>
            <h1 class="ntf-title">Notifications</h1>
            <p class="ntf-sub">All system alerts and updates</p>
        </div>
        <div class="ntf-actions">
            <a href="{{ route('dashboard') }}" class="ntf-btn">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7m-9 2v8m4-8v8m5-4h-2"/></svg>
                Dashboard
            </a>
            <button type="button" class="ntf-btn-primary" id="ntfMarkAll">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Mark all as read
            </button>
        </div>
    </div>

    <div class="ntf-filters">
        <a href="{{ route('notifications.all') }}" class="ntf-btn {{ $currentView === 'all' ? 'active' : '' }}">All</a>
        <a href="{{ route('notifications.unread-page') }}" class="ntf-btn {{ $currentView === 'unread' ? 'active' : '' }}">Unread</a>
        <a href="{{ route('notifications.read-page') }}" class="ntf-btn {{ $currentView === 'read' ? 'active' : '' }}">Read</a>
    </div>

    <div class="ntf-card">
        <div class="ntf-card-head">
            <p class="ntf-card-title"><span class="ntf-dot"></span> Inbox</p>
        </div>
        <div class="ntf-card-body">
            @forelse($notifications as $n)
                <div class="ntf-row" data-notif-id="{{ $n->id }}" data-action-url="{{ $n->getActionUrl() }}">
                    <div style="display:flex;flex-direction:column;gap:6px;min-width:0;flex:1;">
                        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                            <span class="ntf-badge {{ $n->isUnread() ? 'unread' : 'read' }}">{{ $n->isUnread() ? 'unread' : 'read' }}</span>
                            <span class="ntf-time">{{ $n->created_at?->diffForHumans() }}</span>
                        </div>
                        <p class="ntf-title2">{{ $n->title }}</p>
                        <p class="ntf-msg">{{ $n->message }}</p>
                    </div>
                    <div class="ntf-actions2">
                        @if($n->isUnread() && $currentView !== 'deleted')
                            <button class="ntf-iconbtn" title="Mark as read" data-action="read">
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="ntf-empty">
                    <div style="font-weight:900;color:#111827;margin-bottom:6px;">No {{ $currentView }} notifications</div>
                    You’re all caught up.
                </div>
            @endforelse
        </div>
    </div>

    <div style="margin-top:14px;">
        {{ $notifications->withQueryString()->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const markAllBtn = document.getElementById('ntfMarkAll');
    if (markAllBtn) {
        markAllBtn.addEventListener('click', function(){
            fetch(@json(route('notifications.mark-all-as-read')), {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
            }).then(()=> window.location.reload());
        });
    }

    document.querySelectorAll('.ntf-row').forEach(row => {
        row.addEventListener('click', function(e){
            if (e.target.closest('button')) return;
            const url = this.dataset.actionUrl;
            if (url && url !== 'null') window.location.href = url;
        });
        row.querySelectorAll('button[data-action]').forEach(btn => {
            btn.addEventListener('click', function(e){
                e.preventDefault(); e.stopPropagation();
                const id = row.dataset.notifId;
                const action = btn.dataset.action;
                if (!id) return;
                if (action === 'read') {
                    fetch(`/notifications/${id}/read`, { method:'POST', headers:{ 'X-CSRF-TOKEN': csrf, 'Accept':'application/json' } })
                        .then(()=> window.location.reload());
                }
            });
        });
    });
})();
</script>
@endpush


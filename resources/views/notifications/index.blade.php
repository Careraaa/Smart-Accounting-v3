@extends('layouts.layout')

@push('styles')
<style>
@keyframes ntfFadeSlideUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes ntfScaleIn { 0%{opacity:0;transform:scale(0.92)} 100%{opacity:1;transform:scale(1)} }
@keyframes ntfSlideInRight { 0%{opacity:0;transform:translateX(-10px)} 100%{opacity:1;transform:translateX(0)} }
@keyframes ntfPulseDot { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:0.6;transform:scale(1.2)} }
@keyframes ntfShimmer { 0%{background-position:-200% 0} 100%{background-position:200% 0} }

.ntf-page { animation: ntfFadeSlideUp 0.45s cubic-bezier(0.16,1,0.3,1) both; }
.ntf-fade { animation: ntfFadeSlideUp 0.45s cubic-bezier(0.16,1,0.3,1) both; }
.ntf-scale { animation: ntfScaleIn 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.ntf-slide { animation: ntfSlideInRight 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.ntf-row { animation: ntfFadeSlideUp 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.ntf-row:nth-child(1) { animation-delay: 0.03s; }
.ntf-row:nth-child(2) { animation-delay: 0.06s; }
.ntf-row:nth-child(3) { animation-delay: 0.09s; }
.ntf-row:nth-child(4) { animation-delay: 0.12s; }
</style>
@endpush

@section('content')
@php
    $currentView = $filter['view'] ?? 'all';
    $unreadCount = $stats['unread'] ?? 0;
@endphp

<div class="ntf-page max-w-3xl mx-auto">

    {{-- Header --}}
    <div class="flex items-start justify-between mb-6 flex-wrap gap-4">
        <div>
            <div class="flex items-center gap-2.5 mb-0.5">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900 tracking-tight leading-tight">Notifications</h1>
                    <p class="text-xs text-gray-400 leading-tight">All system alerts and updates</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl text-xs font-semibold no-underline hover:border-gray-600 hover:bg-gray-50 transition-all duration-200">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7m-9 2v8m4-8v8m5-4h-2"/></svg>
                Dashboard
            </a>
            @if($unreadCount > 0)
            <button type="button" id="ntfMarkAll" class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-900 text-white text-xs font-semibold rounded-xl hover:bg-gray-800 hover:shadow-lg hover:shadow-gray-900/20 active:scale-[0.97] transition-all duration-200 border-none cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Mark all as read
            </button>
            @endif
        </div>
    </div>

    {{-- Filters --}}
    <div class="flex items-center gap-2 mb-5 ntf-slide">
        <a href="{{ route('notifications.all') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold border transition-all duration-200 no-underline {{ $currentView === 'all' ? 'bg-gray-900 text-white border-gray-900 shadow-sm' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-300 hover:text-gray-900 hover:shadow-sm' }}">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4h18M3 8h18M3 12h18M3 16h18M3 20h18"/></svg>
            All
            @if(($stats['total'] ?? 0) > 0)
                <span class="inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 rounded-full text-[10px] font-bold {{ $currentView === 'all' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-600' }}">{{ $stats['total'] }}</span>
            @endif
        </a>
        <a href="{{ route('notifications.unread-page') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold border transition-all duration-200 no-underline {{ $currentView === 'unread' ? 'bg-gray-900 text-white border-gray-900 shadow-sm' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-300 hover:text-gray-900 hover:shadow-sm' }}">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            Unread
            @if($unreadCount > 0)
                <span class="inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 rounded-full text-[10px] font-bold {{ $currentView === 'unread' ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-700' }}">{{ $unreadCount }}</span>
            @endif
        </a>
        <a href="{{ route('notifications.read-page') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold border transition-all duration-200 no-underline {{ $currentView === 'read' ? 'bg-gray-900 text-white border-gray-900 shadow-sm' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-300 hover:text-gray-900 hover:shadow-sm' }}">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Read
        </a>
    </div>

    {{-- Notifications Card --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm ntf-scale">
        {{-- Card header --}}
        <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </div>
                <span class="text-sm font-bold text-gray-900">Inbox</span>
                @if($unreadCount > 0)
                    <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">{{ $unreadCount }} new</span>
                @endif
            </div>
            <span class="text-[11px] font-mono text-gray-400">{{ $notifications->total() }} total</span>
        </div>

        {{-- Card body --}}
        <div>
            @forelse($notifications as $n)
                <div class="ntf-row flex items-start gap-3 px-5 py-3.5 border-b border-gray-50 cursor-pointer transition-all duration-150 hover:bg-emerald-50/30 hover:border-l-2 hover:border-l-emerald-500 hover:pl-[18px] no-underline" data-notif-id="{{ $n->id }}" data-action-url="{{ $n->getActionUrl() }}">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 mt-0.5 {{ $n->isUnread() ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-100 text-gray-400' }}">
                        @if($n->isUnread())
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        @else
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-0.5">
                            <span class="text-sm font-bold {{ $n->isUnread() ? 'text-gray-900' : 'text-gray-600' }}">{{ $n->title }}</span>
                            @if($n->isUnread())
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block shrink-0" style="animation:ntfPulseDot 1.5s ease-in-out infinite"></span>
                            @endif
                        </div>
                        <p class="text-sm {{ $n->isUnread() ? 'text-gray-600' : 'text-gray-500' }} mb-1 leading-snug">{{ $n->message }}</p>
                        <div class="flex items-center gap-2">
                            @if($n->isUnread())
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 leading-tight">Unread</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500 border border-gray-200 leading-tight">Read</span>
                            @endif
                            <span class="text-[11px] font-mono text-gray-400">{{ $n->created_at?->diffForHumans() }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        @if($n->isUnread() && $currentView !== 'deleted')
                            <button class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 transition-all duration-150 border-none cursor-pointer" title="Mark as read" data-action="read">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </button>
                        @endif
                        <svg class="w-3.5 h-3.5 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <div class="w-14 h-14 rounded-xl bg-gray-100 flex items-center justify-center text-gray-300 mb-4">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-700">No {{ $currentView }} notifications</p>
                    <p class="text-xs text-gray-400 mt-1">You're all caught up.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($notifications->hasPages())
        <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100 bg-gray-50/50 flex-wrap gap-3">
            <div class="text-xs text-gray-400">
                Showing <strong class="text-gray-700">{{ $notifications->firstItem() }}</strong>
                – <strong class="text-gray-700">{{ $notifications->lastItem() }}</strong>
                of <strong class="text-gray-700">{{ $notifications->total() }}</strong>
            </div>
            <nav class="flex items-center gap-1">
                {{-- Previous --}}
                @if($notifications->onFirstPage())
                    <span class="flex items-center justify-center w-7 h-7 rounded-lg text-xs font-semibold border bg-gray-50 text-gray-300 border-gray-100 cursor-not-allowed pointer-events-none">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    </span>
                @else
                    <a href="{{ $notifications->previousPageUrl() }}" class="flex items-center justify-center w-7 h-7 rounded-lg text-xs font-semibold border transition-all duration-150 bg-white text-gray-600 border-gray-200 hover:bg-gray-50 hover:text-gray-900 hover:border-gray-300 no-underline">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                @endif

                {{-- Pages --}}
                @foreach($notifications->getUrlRange(max(1, $notifications->currentPage() - 2), min($notifications->lastPage(), $notifications->currentPage() + 2)) as $page => $url)
                    <a href="{{ $url }}" class="flex items-center justify-center w-7 h-7 rounded-lg text-xs font-semibold border transition-all duration-150 no-underline {{ $page === $notifications->currentPage() ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50 hover:text-gray-900 hover:border-gray-300' }}">
                        {{ $page }}
                    </a>
                @endforeach

                {{-- Next --}}
                @if(!$notifications->hasMorePages())
                    <span class="flex items-center justify-center w-7 h-7 rounded-lg text-xs font-semibold border bg-gray-50 text-gray-300 border-gray-100 cursor-not-allowed pointer-events-none">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </span>
                @else
                    <a href="{{ $notifications->nextPageUrl() }}" class="flex items-center justify-center w-7 h-7 rounded-lg text-xs font-semibold border transition-all duration-150 bg-white text-gray-600 border-gray-200 hover:bg-gray-50 hover:text-gray-900 hover:border-gray-300 no-underline">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @endif
            </nav>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

    // Mark all as read
    const markAllBtn = document.getElementById('ntfMarkAll');
    if (markAllBtn) {
        markAllBtn.addEventListener('click', function(){
            fetch(@json(route('notifications.mark-all-as-read')), {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
            }).then(() => window.location.reload());
        });
    }

    // Row click -> navigate to action url
    document.querySelectorAll('[data-notif-id]').forEach(row => {
        row.addEventListener('click', function(e){
            if (e.target.closest('button')) return;
            const url = this.dataset.actionUrl;
            if (url && url !== 'null') window.location.href = url;
        });
        // Mark as read button
        row.querySelectorAll('button[data-action]').forEach(btn => {
            btn.addEventListener('click', function(e){
                e.preventDefault();
                e.stopPropagation();
                const id = row.dataset.notifId;
                if (!id) return;
                fetch(`/notifications/${id}/read`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
                }).then(() => window.location.reload());
            });
        });
    });
})();
</script>
@endpush

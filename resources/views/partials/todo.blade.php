{{-- To-Do List Sidebar Component --}}
<div class="prl-todo-container">
    <h2 class="prl-todo-title">To Do</h2>
    
    @if(($pendingItems ?? []) && count($pendingItems) > 0)
        <ul class="prl-todo-list">
            @foreach($pendingItems as $item)
                <li class="prl-todo-item">
                    <a href="{{ $item['url'] }}" class="prl-todo-link">
                        <span class="prl-todo-icon" style="background-color: {{ $item['color'] ?? '#e5e7eb' }};">
                            <i class="{{ $item['icon'] ?? 'feather-check-circle' }}" style="color: {{ $item['iconColor'] ?? '#6b7280' }};"></i>
                        </span>
                        <div class="prl-todo-content">
                            <span class="prl-todo-text">{{ $item['text'] }}</span>
                            @if(isset($item['count']))
                                <span class="prl-todo-badge">{{ $item['count'] }}</span>
                            @endif
                        </div>
                        <i class="feather-arrow-right prl-todo-arrow"></i>
                    </a>
                </li>
            @endforeach
        </ul>
    @else
        <div class="prl-todo-empty">
            <svg width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p>All caught up!</p>
        </div>
    @endif
</div>

@push('styles')
<style>
/* ── To-Do Container ── */
.prl-todo-container {
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 16px;
    padding: 18px;
    font-family: 'Sora', sans-serif;
    margin-top: 16px;
}

.prl-todo-title {
    margin: 0 0 14px 0;
    font-size: 0.84rem;
    font-weight: 800;
    color: #111827;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* ── To-Do List ── */
.prl-todo-list {
    margin: 0;
    padding: 0;
    list-style: none;
}

.prl-todo-item {
    padding: 0;
    margin: 0;
}

.prl-todo-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    border-radius: 10px;
    text-decoration: none;
    color: inherit;
    transition: background 0.15s ease, box-shadow 0.15s ease;
    border: 1px solid #f3f4f6;
    margin-bottom: 8px;
}

.prl-todo-link:last-child {
    margin-bottom: 0;
}

.prl-todo-link:hover {
    background: #f9fafb;
    border-color: #e5e7eb;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.prl-todo-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1rem;
}

.prl-todo-content {
    flex: 1;
    min-width: 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}

.prl-todo-text {
    font-size: 0.78rem;
    color: #111827;
    font-weight: 600;
    line-height: 1.4;
    flex: 1;
}

.prl-todo-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 24px;
    height: 24px;
    padding: 0 6px;
    background: #f3f4f6;
    border-radius: 999px;
    font-size: 0.65rem;
    font-weight: 800;
    color: #6b7280;
    flex-shrink: 0;
}

.prl-todo-arrow {
    color: #d1d5db;
    font-size: 0.75rem;
    flex-shrink: 0;
}

/* ── Empty State ── */
.prl-todo-empty {
    text-align: center;
    padding: 24px 12px;
    color: #9ca3af;
}

.prl-todo-empty svg {
    color: #d1d5db;
    margin-bottom: 8px;
}

.prl-todo-empty p {
    margin: 0;
    font-size: 0.78rem;
    font-weight: 600;
    color: #9ca3af;
}
</style>
@endpush

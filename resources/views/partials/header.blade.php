<header class="nxl-header">
    <div class="header-wrapper">

        {{-- ── Left ── --}}
        <div class="header-left d-flex align-items-center gap-3">

            {{-- Mobile hamburger (shown only on mobile via d-xl-none) --}}
            <a href="javascript:void(0);" class="kt-header-btn nxl-head-mobile-toggler d-xl-none" id="mobile-collapse">
                <i class="feather-menu fs-18"></i>
            </a>

            {{-- Desktop toggle buttons: JS controls which one is visible --}}
            {{-- Both use the SAME hamburger icon so it always looks the same --}}
            <a href="javascript:void(0);" class="kt-header-btn d-none d-xl-flex" id="menu-mini-button">
                <i class="feather-menu fs-18"></i>
            </a>
            <a href="javascript:void(0);" class="kt-header-btn d-none d-xl-flex" id="menu-expend-button"
                style="display:none !important;">
                <i class="feather-menu fs-18"></i>
            </a>

            {{-- Brand --}}
            <div class="d-none d-md-flex align-items-center gap-2">
                <span class="kt-brand-name">Knights Transport</span>
                <span class="kt-role-pill">
                    {{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}
                </span>
            </div>
        </div>

        {{-- ── Right ── --}}
        <div class="header-right ms-auto d-flex align-items-center gap-1">

            {{-- Fullscreen: single button, icon swapped by JS --}}
            <a href="javascript:void(0);" class="kt-header-btn d-none d-sm-flex" id="kt-fullscreen-btn">
                <i class="feather-maximize fs-17"></i>
            </a>

            {{-- Notifications --}}
            <div class="dropdown">
                <a class="kt-header-btn position-relative" id="notification-btn" data-bs-toggle="dropdown" href="#" role="button"
                    data-bs-auto-close="outside" data-bs-display="static">
                    <i class="feather-bell fs-17"></i>
                    @php
                        $unread_count = auth()->user()->notifications()->unread()->count();
                    @endphp
                    @if($unread_count > 0)
                        <span class="kt-notif-dot" id="notif-count">{{ $unread_count > 99 ? '99+' : $unread_count }}</span>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-end kt-notif-dropdown kt-notif-dropdown-under-navbar" id="notification-dropdown" style="width:680px !important; min-width:680px !important; max-width:min(96vw,680px) !important;">
                    <div class="kt-notif-header">
                        <div class="d-flex align-items-center gap-2">
                            <span class="kt-notif-header-icon"><i class="feather-bell"></i></span>
                            <div>
                                <div class="fw-bold">Notifications</div>
                                <small class="text-muted">Latest updates</small>
                            </div>
                        </div>
                        <span class="kt-notif-badge" id="notif-badge">
                            @if($unread_count > 0)
                                {{ $unread_count }} New
                            @else
                                0 New
                            @endif
                        </span>
                    </div>
                    <div class="kt-notif-body">
                        <div id="notification-list">
                            @forelse(auth()->user()->notifications()->unread()->recent()->limit(10)->get() as $notification)
                                <div class="kt-notif-item {{ $notification->isUnread() ? 'unread' : '' }}" data-notif-id="{{ $notification->id }}" data-action-url="{{ $notification->getActionUrl() }}">
                                    <span class="kt-notif-accent"></span>
                                    <div class="kt-notif-content">
                                        <div class="kt-notif-title">{{ $notification->title }}</div>
                                        <p class="kt-notif-message">{{ $notification->message }}</p>
                                        <small class="kt-notif-time">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                    <button type="button" class="kt-notif-delete notif-delete" data-notif-id="{{ $notification->id }}">
                                        <i class="feather-x"></i>
                                    </button>
                                </div>
                            @empty
                                <div class="kt-notif-empty">
                                    <i class="feather-bell-off"></i>
                                    <p>No new notifications</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                    <div class="kt-notif-footer d-flex align-items-center justify-content-between gap-2">
                        <a class="btn btn-sm btn-link" href="{{ route('notifications.index') }}">View all</a>
                        <button class="btn btn-sm btn-link" id="mark-all-read" style="{{ $unread_count > 0 ? '' : 'display:none;' }}">Mark all as read</button>
                    </div>
                </div>
            </div>

            {{-- Divider --}}
            <div class="kt-header-divider d-none d-sm-block"></div>

            {{-- User Profile Dropdown --}}
            <div class="dropdown">
                <a href="javascript:void(0);" class="kt-user-trigger d-flex align-items-center gap-2 text-decoration-none"
                    data-bs-toggle="dropdown" data-bs-auto-close="outside" data-bs-display="static">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white kt-user-avatar" style="width: 40px; height: 40px; font-size: 18px; font-weight: normal; min-width: 40px;">
                        {{ auth()->user()->getFirstLetter() }}
                    </div>
                    <div class="d-none d-md-block text-start lh-sm">
                        <div class="kt-user-name">{{ auth()->user()->name }}</div>
                        <div class="kt-user-email">{{ auth()->user()->email }}</div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end kt-user-dropdown kt-user-dropdown-under-navbar">
                    <div class="kt-user-dropdown-body">
                        <a href="{{ route('profile.details') }}" class="kt-user-dropdown-item">
                            <i class="feather-user"></i> Profile
                        </a>
                        <div class="kt-user-dropdown-divider"></div>
                        <a href="javascript:void(0);" class="kt-user-dropdown-item kt-logout-item"
                            onclick="document.getElementById('logout-form').submit();">
                            <i class="feather-log-out"></i> Logout
                        </a>
                        <form method="POST" action="{{ route('logout') }}" id="logout-form" style="display:none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</header>

@push('styles')
    <style>
        .kt-user-dropdown-under-navbar {
            position: absolute !important;
            top: calc(100% + 18px) !important;
            right: 12px !important;
            left: auto !important;
            inset: auto !important;
            transform: none !important;
            margin-top: 0 !important;
        }

        .kt-notif-dropdown-under-navbar {
            position: absolute !important;
            top: calc(100% + 18px) !important;
            right: 12px !important;
            left: auto !important;
            inset: auto !important;
            transform: none !important;
            margin-top: 0 !important;
            width: 680px !important;
            min-width: 680px !important;
            max-width: min(96vw, 680px) !important;
        }

        /* Replace old template dropdown look with custom popup */
        .nxl-header #notification-dropdown.kt-notif-dropdown {
            width: 680px !important;
            min-width: 680px !important;
            max-width: min(96vw, 680px) !important;
            right: 12px !important;
            left: auto !important;
            border: 1px solid #f1d0d0 !important;
            border-radius: 18px !important;
            box-shadow: 0 20px 45px rgba(17, 24, 39, 0.18), 0 8px 22px rgba(200, 41, 42, 0.14) !important;
            overflow: hidden !important;
            padding: 0 !important;
            background: #fff !important;
        }

        .nxl-header #notification-dropdown .kt-notif-header {
            padding: 14px 16px !important;
            border-bottom: 1px solid #f3f4f6 !important;
            background: linear-gradient(135deg, #fff6f6 0%, #ffffff 72%) !important;
        }

        .nxl-header #notification-dropdown .kt-notif-header-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #ffe9e9;
            color: #c8292a;
            font-size: 14px;
        }

        .nxl-header #notification-dropdown .kt-notif-badge {
            background: #c8292a !important;
            color: #fff !important;
            border-radius: 999px;
            font-weight: 800;
            font-size: 0.66rem;
            padding: 4px 10px;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .nxl-header #notification-dropdown .kt-notif-body {
            max-height: 470px;
            overflow-y: auto;
            padding: 10px;
            background: #ffffff;
        }

        .nxl-header #notification-dropdown .kt-notif-item {
            position: relative;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 11px 12px;
            margin: 0 0 8px;
            border-radius: 12px;
            border: 1px solid #f1f5f9;
            background: #fff;
            transition: all 0.16s ease;
            cursor: pointer;
        }

        .nxl-header #notification-dropdown .kt-notif-item:last-child {
            margin-bottom: 0;
        }

        .nxl-header #notification-dropdown .kt-notif-item:hover {
            background: #fff7f7;
            border-color: #f8d3d3;
            transform: translateY(-1px);
        }

        .nxl-header #notification-dropdown .kt-notif-item.unread {
            border-color: #ffd7d7;
            background: #fffafb;
        }

        .nxl-header #notification-dropdown .kt-notif-accent {
            width: 4px;
            align-self: stretch;
            border-radius: 999px;
            background: transparent;
            margin-right: 2px;
            flex-shrink: 0;
        }

        .nxl-header #notification-dropdown .kt-notif-item.unread .kt-notif-accent {
            background: #c8292a;
        }

        .nxl-header #notification-dropdown .kt-notif-content {
            flex: 1;
            min-width: 0;
        }

        .nxl-header #notification-dropdown .kt-notif-title {
            font-size: 0.87rem;
            font-weight: 800;
            color: #111827;
            margin-bottom: 2px;
        }

        .nxl-header #notification-dropdown .kt-notif-message {
            font-size: 0.79rem;
            line-height: 1.4;
            color: #6b7280;
            margin: 0 0 4px;
        }

        .nxl-header #notification-dropdown .kt-notif-time {
            font-size: 0.72rem;
            color: #9ca3af;
        }

        .nxl-header #notification-dropdown .kt-notif-delete {
            width: 28px;
            height: 28px;
            min-width: 28px;
            border: 0;
            border-radius: 8px;
            background: #f8fafc;
            color: #94a3b8;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            opacity: 1;
        }

        .nxl-header #notification-dropdown .kt-notif-delete:hover {
            background: #ffe4e6;
            color: #e11d48;
        }

        .nxl-header #notification-dropdown .kt-notif-footer {
            border-top: 1px solid #f3f4f6;
            background: #fcfcfd;
            padding: 10px 14px !important;
        }

        .nxl-header #notification-dropdown .kt-notif-footer .btn-link {
            text-decoration: none;
            color: #c8292a;
            font-weight: 700;
            font-size: 0.79rem;
        }

        .nxl-header #notification-dropdown .kt-notif-footer .btn-link:hover {
            color: #a81f20;
        }

        @media (max-width: 767px) {
            .kt-notif-dropdown-under-navbar,
            .kt-user-dropdown-under-navbar {
                right: 12px !important;
                left: 12px !important;
            }

            .kt-notif-dropdown-under-navbar {
                width: auto !important;
                min-width: auto !important;
                max-width: none !important;
            }

            .nxl-header #notification-dropdown.kt-notif-dropdown {
                width: auto !important;
                min-width: auto !important;
                max-width: none !important;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Global polling interval ID
        let notificationPollingInterval = null;
        
        // Get current user role
        const userRole = '{{ auth()->user()->role }}';
        const notificationReadUrlTemplate = @json(route('notifications.read', ['notification' => '__ID__']));
        const notificationDeleteUrlTemplate = @json(route('notifications.destroy', ['notification' => '__ID__']));
        
        // Notification functionality
        document.addEventListener('DOMContentLoaded', function() {
            const isNotificationsPage = window.location.pathname.includes('/notifications');

            // Initialize notification system
            initializeNotifications();
            
            // Auto-refresh notifications every 5 seconds
            if (!isNotificationsPage) {
                startNotificationPolling();
            }
            
            // Refresh notifications when dropdown is opened
            const notificationDropdown = document.getElementById('notification-dropdown');
            if (notificationDropdown) {
                const dropdownBtn = document.getElementById('notification-btn');
                if (dropdownBtn) {
                    dropdownBtn.addEventListener('show.bs.dropdown', function() {
                        refreshNotificationList();
                    });
                }
            }
        });

        // Initialize notification event listeners
        function initializeNotifications() {
            attachDeleteListeners();
            attachMarkAllAsReadListener();
            attachNotificationClickListeners();
        }

        // Attach click listeners to notification items for navigation
        function attachNotificationClickListeners() {
            document.querySelectorAll('.kt-notif-item').forEach(item => {
                item.addEventListener('click', function(e) {
                    // Don't trigger if clicking the delete button
                    if (e.target.closest('.notif-delete')) {
                        return;
                    }

                    e.preventDefault();
                    e.stopPropagation();

                    const notifId = this.dataset.notifId;
                    const actionUrl = this.dataset.actionUrl;

                    // Mark notification as read
                    fetch(notificationReadUrlTemplate.replace('__ID__', notifId), {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Close the dropdown
                        const dropdownBtn = document.getElementById('notification-btn');
                        if (dropdownBtn) {
                            const dropdownInstance = bootstrap.Dropdown.getInstance(dropdownBtn) || new bootstrap.Dropdown(dropdownBtn);
                            dropdownInstance.hide();
                        }

                        // Navigate to the action URL if it exists
                        if (actionUrl && actionUrl !== 'null') {
                            window.location.href = actionUrl;
                        } else {
                            // Update notification list if no navigation
                            updateNotificationCount();
                            refreshNotificationList();
                        }
                    })
                    .catch(error => console.error('Error marking notification as read:', error));
                });

                // Add hover effect
                item.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = 'rgba(0, 0, 0, 0.05)';
                });

                item.addEventListener('mouseleave', function() {
                    if (!this.classList.contains('bg-light')) {
                        this.style.backgroundColor = '';
                    }
                });
            });
        }

        // Attach delete button listeners
        function attachDeleteListeners() {
            document.querySelectorAll('.notif-delete').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const notifId = this.dataset.notifId;
                    
                    fetch(notificationDeleteUrlTemplate.replace('__ID__', notifId), {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            // Remove the notification item from DOM
                            const notifItem = document.querySelector(`[data-notif-id="${notifId}"]`);
                            if (notifItem) {
                                notifItem.remove();
                            }
                            updateNotificationCount();
                        }
                    })
                    .catch(error => console.error('Error deleting notification:', error));
                });
            });
        }

        // Attach mark all as read listener
        function attachMarkAllAsReadListener() {
            const markAllReadBtn = document.getElementById('mark-all-read');
            if (markAllReadBtn) {
                markAllReadBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    fetch(@json(route('notifications.mark-all-as-read')), {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            refreshNotificationList();
                        }
                    })
                    .catch(error => console.error('Error marking as read:', error));
                });
            }
        }

        // Update the notification count
        function updateNotificationCount() {
            fetch(@json(route('notifications.count')), {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                const notifCount = document.getElementById('notif-count');
                const notifBadge = document.getElementById('notif-badge');
                
                if (data.unread_count > 0) {
                    if (notifCount) {
                        notifCount.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
                    } else {
                        const newBadge = document.createElement('span');
                        newBadge.id = 'notif-count';
                        newBadge.className = 'kt-notif-dot';
                        newBadge.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
                        document.getElementById('notification-btn').appendChild(newBadge);
                    }
                    if (notifBadge) {
                        notifBadge.textContent = data.unread_count + ' New';
                    }
                } else {
                    if (notifCount) {
                        notifCount.remove();
                    }
                    if (notifBadge) {
                        notifBadge.textContent = '0 New';
                    }
                }
            })
            .catch(error => console.error('Error updating count:', error));
        }

        // Refresh the entire notification list
        function refreshNotificationList() {
            fetch(@json(route('notifications.index')) + '?read=unread&limit=10', {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                const payload = data && data.status === 'success' ? data.data : null;
                const notifications = payload && Array.isArray(payload.data) ? payload.data : [];
                if (data.status === 'success') {
                    const notificationList = document.getElementById('notification-list');
                    
                    if (notifications.length > 0) {
                        // Build HTML for notifications
                        let html = '';
                        notifications.forEach(notification => {
                            const createdAt = new Date(notification.created_at);
                            const diffTime = Math.abs(new Date() - createdAt);
                            const diffMinutes = Math.ceil(diffTime / (1000 * 60));
                            let timeText = diffMinutes + ' min ago';
                            
                            if (diffMinutes >= 60) {
                                const diffHours = Math.ceil(diffMinutes / 60);
                                timeText = diffHours + ' hour' + (diffHours > 1 ? 's' : '') + ' ago';
                            }

                            // Generate action URL based on notification type
                            let actionUrl = getNotificationActionUrl(notification.type, notification.data);
                            
                            html += `
                                <div class="kt-notif-item ${notification.read_at ? '' : 'unread'}" data-notif-id="${notification.id}" data-action-url="${actionUrl}">
                                    <div class="kt-notif-content">
                                        <div class="kt-notif-title">${notification.title}</div>
                                        <p class="kt-notif-message">${notification.message}</p>
                                        <small class="kt-notif-time">${timeText}</small>
                                    </div>
                                    <button type="button" class="kt-notif-delete notif-delete" data-notif-id="${notification.id}">
                                        <i class="feather-x"></i>
                                    </button>
                                </div>
                            `;
                        });
                        
                        notificationList.innerHTML = html;
                        
                        // Re-attach event listeners to new items
                        attachDeleteListeners();
                        attachNotificationClickListeners();
                    } else {
                        // No unread notifications
                        notificationList.innerHTML = `
                            <div class="py-4 text-center text-muted" style="font-size:.83rem;">
                                <i class="feather-bell-off d-block mb-2" style="font-size:22px;opacity:.4;"></i>
                                No new notifications
                            </div>
                        `;
                    }
                }
                
                // Update count and mark-all-read button
                updateNotificationCount();
                updateMarkAllReadButton();
            })
            .catch(error => console.error('Error refreshing notification list:', error));
        }

        // Generate action URL for a notification based on type and data
        function getNotificationActionUrl(type, data) {
            if (!data) return null;

            const isAccountant = userRole === 'accountant';

            switch (type) {
                // Payroll routes
                case 'payroll_processed':
                case 'payroll_released':
                case 'payroll_created':
                case 'payroll_updated':
                case 'payroll_deleted':
                case 'payroll_recalculated':
                    if (isAccountant) {
                        return '/payroll-approval';
                    }
                    return data.payroll_id ? `/payroll/${data.payroll_id}` : '/payroll';
                
                case 'payroll_generated':
                case 'payroll_ready_review':
                    if (isAccountant) {
                        return '/payroll-approval';
                    }
                    return '/payroll';

                // Leave routes
                case 'leave_submitted':
                case 'leave_approved':
                case 'leave_rejected':
                case 'leave_updated':
                case 'leave_deleted':
                    return data.leave_id ? `/leaves/${data.leave_id}` : '/leaves';
                
                case 'leave_pending_approval':
                    return '/leaves?filter=pending';

                // Attendance routes
                case 'attendance_issue':
                case 'employee_absent':
                case 'employee_late':
                case 'attendance_recorded':
                    return data.employee_id ? `/employees/${data.employee_id}` : '/employees';

                // Overtime routes
                case 'overtime_submitted':
                case 'overtime_approved':
                case 'overtime_rejected':
                case 'overtime_updated':
                case 'overtime_deleted':
                    return data.record_id ? `/overtime/${data.record_id}` : '/overtime';
                
                case 'overtime_pending_approval':
                    return '/overtime?filter=pending';

                default:
                    return null;
            }
        }

        // Update mark-all-read button visibility
        function updateMarkAllReadButton() {
            fetch(@json(route('notifications.count')), {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                const markBtn = document.getElementById('mark-all-read');
                
                if (data.unread_count > 0) {
                    if (markBtn) markBtn.style.display = '';
                } else {
                    if (markBtn) markBtn.style.display = 'none';
                }
            })
            .catch(error => console.error('Error updating mark-all button:', error));
        }

        // Start polling for new notifications
        function startNotificationPolling() {
            // Check for new notifications every 5 seconds
            notificationPollingInterval = setInterval(function() {
                fetch(@json(route('notifications.count')), {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    const currentBadge = document.getElementById('notif-count');
                    const currentCount = currentBadge ? parseInt(currentBadge.textContent) : 0;
                    
                    // If count changed, refresh the list if dropdown is open
                    if (data.unread_count !== currentCount) {
                        updateNotificationCount();
                        
                        // If dropdown is currently visible, refresh it
                        const dropdown = document.getElementById('notification-dropdown');
                        if (dropdown && dropdown.classList.contains('show')) {
                            refreshNotificationList();
                        }
                    }
                })
                .catch(error => console.error('Error in polling:', error));
            }, 5000); // Poll every 5 seconds
        }

        // Stop polling when page unloads
        window.addEventListener('beforeunload', function() {
            if (notificationPollingInterval) {
                clearInterval(notificationPollingInterval);
            }
        });

        // Fullscreen — swap icon only, no show/hide logic
        const ktFsBtn = document.getElementById('kt-fullscreen-btn');
        if (ktFsBtn) {
            ktFsBtn.addEventListener('click', function() {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen().catch(() => {});
                } else {
                    document.exitFullscreen().catch(() => {});
                }
            });
            document.addEventListener('fullscreenchange', function() {
                const icon = ktFsBtn.querySelector('i');
                if (!icon) return;
                icon.className = document.fullscreenElement ?
                    'feather-minimize fs-17' :
                    'feather-maximize fs-17';
            });
        }
    </script>
@endpush

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
                <a class="kt-header-btn position-relative" data-bs-toggle="dropdown" href="#" role="button"
                    data-bs-auto-close="outside">
                    <i class="feather-bell fs-17"></i>
                    <span class="kt-notif-dot">3</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end kt-notif-dropdown p-0">
                    <div class="kt-notif-header">
                        <span class="fw-bold">Notifications</span>
                        <span class="kt-notif-badge">3 New</span>
                    </div>
                    <div class="py-4 text-center text-muted" style="font-size:.83rem;">
                        <i class="feather-bell-off d-block mb-2" style="font-size:22px;opacity:.4;"></i>
                        No new notifications
                    </div>
                </div>
            </div>

            {{-- Divider --}}
            <div class="kt-header-divider d-none d-sm-block"></div>

            {{-- User Dropdown --}}
            <div class="dropdown">
                <a href="javascript:void(0);" data-bs-toggle="dropdown" role="button" data-bs-auto-close="outside"
                    class="kt-user-trigger d-flex align-items-center gap-2 text-decoration-none">
                    <img src="{{ auth()->user()->profile_picture
                        ? asset('storage/' . auth()->user()->profile_picture)
                        : asset('images/avatar/avatar.png') }}"
                        alt="user" class="kt-user-avatar" />
                    <div class="d-none d-md-block text-start lh-sm">
                        <div class="kt-user-name">{{ auth()->user()->name }}</div>
                        <div class="kt-user-email">{{ auth()->user()->email }}</div>
                    </div>
                    <i class="feather-chevron-down fs-12 text-muted d-none d-md-block ms-1"></i>
                </a>

                <div class="dropdown-menu dropdown-menu-end kt-user-dropdown">
                    <div class="kt-user-dropdown-header">
                        <img src="{{ auth()->user()->profile_picture
                            ? asset('storage/' . auth()->user()->profile_picture)
                            : asset('images/avatar/avatar.png') }}"
                            alt="user" class="kt-user-dropdown-avatar" />
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="kt-user-dropdown-name">{{ auth()->user()->name }}</div>
                            <div class="kt-user-dropdown-email text-truncate">{{ auth()->user()->email }}</div>
                            <span class="kt-user-dropdown-role">
                                {{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}
                            </span>
                        </div>
                    </div>
                    <div class="kt-user-dropdown-body">
                        <a href="{{ route('profile.details') }}" class="kt-user-dropdown-item">
                            <i class="feather-user"></i><span>Profile Details</span>
                        </a>
                        <a href="{{ route('settings.account') }}" class="kt-user-dropdown-item">
                            <i class="feather-settings"></i><span>Account Settings</span>
                        </a>
                        <div class="kt-user-dropdown-divider"></div>
                        <a href="javascript:void(0);" class="kt-user-dropdown-item kt-logout-item"
                            onclick="document.getElementById('logout-form').submit();">
                            <i class="feather-log-out"></i><span>Logout</span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}" id="logout-form" style="display:none;">
                            @csrf</form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</header>

@push('scripts')
    <script>
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

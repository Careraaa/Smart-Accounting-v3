<header class="nxl-header">
    <div class="header-wrapper">
        <!--! [Start] Header Left !-->
        <div class="header-left d-flex align-items-center gap-4">
            <!--! [Start] nxl-head-mobile-toggler !-->
            <a href="javascript:void(0);" class="nxl-head-mobile-toggler" id="mobile-collapse">
                <div class="hamburger hamburger--arrowturn">
                    <div class="hamburger-box">
                        <div class="hamburger-inner"></div>
                    </div>
                </div>
            </a>
            <!--! [Start] nxl-head-mobile-toggler !-->
            <!--! [Start] nxl-navigation-toggle !-->
            <div class="nxl-navigation-toggle">
                <a href="javascript:void(0);" id="menu-mini-button">
                    <i class="feather-align-left"></i>
                </a>
                <a href="javascript:void(0);" id="menu-expend-button" style="display: none">
                    <i class="feather-arrow-right"></i>
                </a>
            </div>
            <!--! [End] nxl-navigation-toggle !-->
            <!--! [Start] nxl-lavel-mega-menu !-->
            <div class="nxl-drp-link nxl-lavel-mega-menu">
                <div class="nxl-lavel-mega-menu-toggle d-flex d-lg-none">
                    <a href="javascript:void(0)" id="nxl-lavel-mega-menu-hide">
                        <i class="feather-arrow-left me-2"></i>
                        <span>Back</span>
                    </a>
                </div>
            </div>
        </div>
        <!--! [End] Header Left !-->
        <!--! [Start] Header Right !-->
        <div class="header-right ms-auto">
            <div class="d-flex align-items-center">
                <div class="dropdown nxl-h-item nxl-header-search">
                    <a href="javascript:void(0);" class="nxl-head-link me-0" data-bs-toggle="dropdown"
                        data-bs-auto-close="outside">
                        <i class="feather-search"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-search-dropdown">
                        <div class="input-group search-form">
                            <span class="input-group-text">
                                <i class="feather-search fs-6 text-muted"></i>
                            </span>
                            <input type="text" class="form-control search-input-field"
                                placeholder="Search...." />
                            <span class="input-group-text">
                                <button type="button" class="btn-close"></button>
                            </span>
                        </div>
                        <div class="dropdown-divider mt-0"></div>
                        <div class="search-items-wrapper">
                            <div class="searching-for px-4 py-2">
                                <p class="fs-11 fw-medium text-muted">I'm searching for...</p>
                                <div class="d-flex flex-wrap gap-1">
                                    <a href="javascript:void(0);"
                                        class="flex-fill border rounded py-1 px-2 text-center fs-11 fw-semibold">Projects</a>
                                    <a href="javascript:void(0);"
                                        class="flex-fill border rounded py-1 px-2 text-center fs-11 fw-semibold">Leads</a>
                                    <a href="javascript:void(0);"
                                        class="flex-fill border rounded py-1 px-2 text-center fs-11 fw-semibold">Contacts</a>
                                    <a href="javascript:void(0);"
                                        class="flex-fill border rounded py-1 px-2 text-center fs-11 fw-semibold">Inbox</a>
                                    <a href="javascript:void(0);"
                                        class="flex-fill border rounded py-1 px-2 text-center fs-11 fw-semibold">Invoices</a>
                                    <a href="javascript:void(0);"
                                        class="flex-fill border rounded py-1 px-2 text-center fs-11 fw-semibold">Tasks</a>
                                    <a href="javascript:void(0);"
                                        class="flex-fill border rounded py-1 px-2 text-center fs-11 fw-semibold">Customers</a>
                                    <a href="javascript:void(0);"
                                        class="flex-fill border rounded py-1 px-2 text-center fs-11 fw-semibold">Notes</a>
                                    <a href="javascript:void(0);"
                                        class="flex-fill border rounded py-1 px-2 text-center fs-11 fw-semibold">Affiliate</a>
                                    <a href="javascript:void(0);"
                                        class="flex-fill border rounded py-1 px-2 text-center fs-11 fw-semibold">Storage</a>
                                    <a href="javascript:void(0);"
                                        class="flex-fill border rounded py-1 px-2 text-center fs-11 fw-semibold">Calendar</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="nxl-h-item d-none d-sm-flex">
                    <div class="full-screen-switcher">
                        <a href="javascript:void(0);" class="nxl-head-link me-0"
                            onclick="$('body').fullScreenHelper('toggle');">
                            <i class="feather-maximize maximize"></i>
                            <i class="feather-minimize minimize"></i>
                        </a>
                    </div>
                </div>
                <div class="nxl-h-item dark-light-theme">
                    <a href="javascript:void(0);" class="nxl-head-link me-0 dark-button">
                        <i class="feather-moon"></i>
                    </a>
                    <a href="javascript:void(0);" class="nxl-head-link me-0 light-button" style="display: none">
                        <i class="feather-sun"></i>
                    </a>
                </div>
                <div class="dropdown nxl-h-item">
                    <a class="nxl-head-link me-3" data-bs-toggle="dropdown" href="#" role="button"
                        data-bs-auto-close="outside">
                        <i class="feather-bell"></i>
                        <span class="badge bg-danger nxl-h-badge">3</span>
                    </a>
                    <!-- <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-notifications-menu">
                        <div class="d-flex justify-content-between align-items-center notifications-head">
                            <h6 class="fw-bold text-dark mb-0">Notifications</h6>
                            <a href="javascript:void(0);" class="fs-11 text-success text-end ms-auto"
                                data-bs-toggle="tooltip" title="Make as Read">
                                <i class="feather-check"></i>
                                <span>Make as Read</span>
                            </a>
                        </div>
                        <div class="notifications-item">
                            <img src="images/avatar/2.png" alt="" class="rounded me-3 border" />
                            <div class="notifications-desc">
                                <a href="javascript:void(0);" class="font-body text-truncate-2-line"> <span
                                        class="fw-semibold text-dark">Malanie Hanvey</span> We should talk about that
                                    at lunch!</a>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="notifications-date text-muted border-bottom border-bottom-dashed">2
                                        minutes ago</div>
                                    <div class="d-flex align-items-center float-end gap-2">
                                        <a href="javascript:void(0);"
                                            class="d-block wd-8 ht-8 rounded-circle bg-gray-300"
                                            data-bs-toggle="tooltip" title="Make as Read"></a>
                                        <a href="javascript:void(0);" class="text-danger" data-bs-toggle="tooltip"
                                            title="Remove">
                                            <i class="feather-x fs-12"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="notifications-item">
                            <img src="images/avatar/3.png" alt="" class="rounded me-3 border" />
                            <div class="notifications-desc">
                                <a href="javascript:void(0);" class="font-body text-truncate-2-line"> <span
                                        class="fw-semibold text-dark">Valentine Maton</span> You can download the
                                    latest invoices now.</a>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="notifications-date text-muted border-bottom border-bottom-dashed">36
                                        minutes ago</div>
                                    <div class="d-flex align-items-center float-end gap-2">
                                        <a href="javascript:void(0);"
                                            class="d-block wd-8 ht-8 rounded-circle bg-gray-300"
                                            data-bs-toggle="tooltip" title="Make as Read"></a>
                                        <a href="javascript:void(0);" class="text-danger" data-bs-toggle="tooltip"
                                            title="Remove">
                                            <i class="feather-x fs-12"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="notifications-item">
                            <img src="images/avatar/4.png" alt="" class="rounded me-3 border" />
                            <div class="notifications-desc">
                                <a href="javascript:void(0);" class="font-body text-truncate-2-line"> <span
                                        class="fw-semibold text-dark">Archie Cantones</span> Don't forget to pickup
                                    Jeremy after school!</a>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="notifications-date text-muted border-bottom border-bottom-dashed">53
                                        minutes ago</div>
                                    <div class="d-flex align-items-center float-end gap-2">
                                        <a href="javascript:void(0);"
                                            class="d-block wd-8 ht-8 rounded-circle bg-gray-300"
                                            data-bs-toggle="tooltip" title="Make as Read"></a>
                                        <a href="javascript:void(0);" class="text-danger" data-bs-toggle="tooltip"
                                            title="Remove">
                                            <i class="feather-x fs-12"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center notifications-footer">
                            <a href="javascript:void(0);" class="fs-13 fw-semibold text-dark">Alls Notifications</a>
                        </div>
                    </div> -->
                </div>
                <div class="dropdown nxl-h-item">
                    <a href="javascript:void(0);" data-bs-toggle="dropdown" role="button"
                        data-bs-auto-close="outside">
                        <!--PROFILE PICTURE IS HERE!-->
                        <img src="{{ auth()->user()->profile_picture ? asset('storage/' . auth()->user()->profile_picture) : asset('images/avatar/avatar.png') }}" alt="user-image" 
                            class="user-avtar me-0" />
                    </a>
                    <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-user-dropdown">
                        <div class="dropdown-header">
                            <div class="d-flex align-items-center">
                                <img src="{{ auth()->user()->profile_picture ? asset('storage/' . auth()->user()->profile_picture) : asset('images/avatar/avatar.png') }}" alt="user-image"
                                    class="user-avtar" />
                                <div>
                                    <h6 class="text-dark mb-0">{{ auth()->user()->name }}</h6>
                                    <span class="fs-12 fw-medium text-muted">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('profile.details') }}" class="dropdown-item">
                            <i class="feather-user"></i>
                            <span>Profile Details</span>
                        </a>
                        <a href="{{ route('settings.account') }}" class="dropdown-item">
                            <i class="feather-settings"></i>
                            <span>Account Settings</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}" style="display: none;" id="logout-form">
                            @csrf
                        </form>
                        <a href="javascript:void(0);" class="dropdown-item" onclick="document.getElementById('logout-form').submit();">
                            <i class="feather-log-out"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!--! [End] Header Right !-->
    </div>
</header>

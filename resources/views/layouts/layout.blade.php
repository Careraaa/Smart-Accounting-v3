<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="" />
    <meta name="keyword" content="" />
    <meta name="author" content="flexilecode" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <!--! BEGIN: Apps Title-->
    <title>Smart Accounting v3</title>
    <!--! END:  Apps Title-->
    <!--! BEGIN: Favicon-->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/knights_logo_icon.png') }}" />
    <!--! END: Favicon-->
    <!--! BEGIN: Bootstrap CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.min.css') }}" />
    <!--! END: Bootstrap CSS-->
    <!--! BEGIN: Vendors CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('vendors/css/vendors.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('vendors/css/daterangepicker.min.css') }}" />
    <!--! END: Vendors CSS-->
    <!--! BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/theme.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/overrides.css') }}">
</head>

<body>
    <!-- Sidebar -->

    @include('partials.sidebar')

    <!--Header-->
    @include('partials.header')

    <!-- Main Content -->
    <main class="nxl-container">
        <div class="nxl-content">

            <!-- Page Header -->
            @include('partials.page-header')

            <!-- Specific Page Content -->
            <div class="main-content">
                <div class="row">
                    @yield('content')
                </div>
            </div>

            <!-- Footer -->
            @include('partials.footer')

        </div>
    </main>

    <!--! BEGIN: Vendors JS !-->
    <script src="{{ asset('vendors/js/vendors.min.js') }}"></script>
    <!-- vendors.min.js {always must need to be top} -->
    <script src="{{ asset('vendors/js/daterangepicker.min.js') }}"></script>
    <script src="{{ asset('vendors/js/apexcharts.min.js') }}"></script>
    <script src="{{ asset('vendors/js/circle-progress.min.js') }}"></script>
    <!--! END: Vendors JS !-->
    <!--! BEGIN: Apps Init  !-->
    <script src="{{ asset('js/common-init.min.js') }}"></script>
    <script src="{{ asset('js/dashboard-init.min.js') }}"></script>
    <!--! END: Apps Init !-->
    <!--! BEGIN: Theme Customizer  !-->
    <script src="{{ asset('js/theme-customizer-init.min.js') }}"></script>
    <!--! END: Theme Customizer !-->
    @stack('scripts')

    <!--! BEGIN: Mobile Menu Handler  !-->
    <script>
        // Mobile sidebar toggle
        const mobileCollapse = document.getElementById('mobile-collapse');
        const navigation = document.querySelector('.nxl-navigation');
        const body = document.body;

        if (mobileCollapse && navigation) {
            // Toggle sidebar on button click
            mobileCollapse.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                navigation.classList.toggle('active');
                body.classList.toggle('sidebar-open');
            });

            // Close sidebar when clicking on a navigation link (except submenu toggles)
            const navLinks = navigation.querySelectorAll('a.nxl-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    // Check if this is a submenu toggle (has nxl-hasmenu parent without href)
                    const parent = this.closest('.nxl-hasmenu');
                    if (parent && this.getAttribute('href') === 'javascript:void(0);') {
                        // This is a submenu toggle, don't close sidebar
                        return;
                    }
                    
                    // Close sidebar for regular navigation links
                    if (window.innerWidth < 1200) {
                        navigation.classList.remove('active');
                        body.classList.remove('sidebar-open');
                    }
                });
            });

            // Close sidebar when clicking backdrop/outside
            document.addEventListener('click', function(e) {
                if (window.innerWidth < 1200 && body.classList.contains('sidebar-open')) {
                    if (!navigation.contains(e.target) && !mobileCollapse.contains(e.target)) {
                        navigation.classList.remove('active');
                        body.classList.remove('sidebar-open');
                    }
                }
            });

            // Close sidebar when window is resized to desktop size
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1200) {
                    navigation.classList.remove('active');
                    body.classList.remove('sidebar-open');
                }
            });
        }
    </script>
    <!--! END: Mobile Menu Handler  !-->
    
    @yield('scripts')
</body>

</html>

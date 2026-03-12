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
    <title>Smart Accounting v3</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/knights_logo_icon.png') }}" />

    {{-- Template CSS --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('vendors/css/vendors.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('vendors/css/daterangepicker.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/theme.min.css') }}" />

    {{-- App CSS --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('css/overrides.css') }}">

    @stack('styles')
</head>

<body>
    @include('partials.sidebar')
    @include('partials.header')

    <main class="nxl-container">
        <div class="nxl-content">
            @include('partials.page-header')

            <div class="main-content">
                <div class="row">
                    @yield('content')
                </div>
            </div>

            @include('partials.footer')
        </div>
    </main>

    {{-- Template Vendors JS (order matters, vendors first) --}}
    <script src="{{ asset('vendors/js/vendors.min.js') }}"></script>
    <script src="{{ asset('vendors/js/daterangepicker.min.js') }}"></script>
    <script src="{{ asset('vendors/js/apexcharts.min.js') }}"></script>
    <script src="{{ asset('vendors/js/circle-progress.min.js') }}"></script>

    {{-- Template Init JS --}}
    <script src="{{ asset('js/template/common-init.min.js') }}"></script>
    <script src="{{ asset('js/template/dashboard-init.min.js') }}"></script>
    <script src="{{ asset('js/template/theme-customizer-init.min.js') }}"></script>

    {{-- Mobile Sidebar Handler --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggle = document.getElementById('mobile-collapse');
            const nav = document.querySelector('.nxl-navigation');
            const closeBtn = document.querySelector('.kt-mob-close');

            if (!toggle || !nav) return;

            // Open/close on hamburger click
            toggle.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                nav.classList.toggle('active');
                document.body.classList.toggle('sidebar-open', nav.classList.contains('active'));
            });

            // Close on close button click
            if (closeBtn) {
                closeBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    nav.classList.remove('active', 'mob-navigation-active');
                    document.body.classList.remove('sidebar-open');
                });
            }

            // Close on backdrop click (outside nav/toggle)
            document.addEventListener('click', function (e) {
                if (window.innerWidth >= 1199) return;
                if (!nav.classList.contains('active') && !nav.classList.contains('mob-navigation-active')) return;

                const isClickOnNav = nav.contains(e.target);
                const isClickOnToggle = toggle.contains(e.target);

                if (!isClickOnNav && !isClickOnToggle) {
                    nav.classList.remove('active', 'mob-navigation-active'); // critical fix
                    document.body.classList.remove('sidebar-open');
                }
            }, true);

            // Close on nav link click (except submenu toggles)
            nav.addEventListener('click', function (e) {
                const link = e.target.closest('a.nxl-link');
                if (!link) return;
                if (link.getAttribute('href') === 'javascript:void(0);') return;

                if (window.innerWidth < 1200) {
                    nav.classList.remove('active', 'mob-navigation-active');
                    document.body.classList.remove('sidebar-open');
                }
            });

            // Reset on resize
            window.addEventListener('resize', function () {
                if (window.innerWidth >= 1200) {
                    nav.classList.remove('active', 'mob-navigation-active');
                    document.body.classList.remove('sidebar-open');
                }
            });
        });
    </script>

    {{-- Page-specific scripts --}}
    @stack('scripts')
    @yield('scripts')
</body>

</html>
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

    {{-- Mobile Menu Handler --}}
    <script>
        const mobileCollapse = document.getElementById('mobile-collapse');
        const navigation = document.querySelector('.nxl-navigation');
        const body = document.body;

        if (mobileCollapse && navigation) {
            mobileCollapse.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                navigation.classList.toggle('active');
                body.classList.toggle('sidebar-open');
            });

            const navLinks = navigation.querySelectorAll('a.nxl-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    const parent = this.closest('.nxl-hasmenu');
                    if (parent && this.getAttribute('href') === 'javascript:void(0);') return;
                    if (window.innerWidth < 1200) {
                        navigation.classList.remove('active');
                        body.classList.remove('sidebar-open');
                    }
                });
            });

            document.addEventListener('click', function(e) {
                if (window.innerWidth < 1200 && body.classList.contains('sidebar-open')) {
                    if (!navigation.contains(e.target) && !mobileCollapse.contains(e.target)) {
                        navigation.classList.remove('active');
                        body.classList.remove('sidebar-open');
                    }
                }
            });

            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1200) {
                    navigation.classList.remove('active');
                    body.classList.remove('sidebar-open');
                }
            });
        }
    </script>

    {{-- Page-specific scripts --}}
    @stack('scripts')
    @yield('scripts')
</body>

</html>
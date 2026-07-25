<!DOCTYPE html>
<html lang="en">

<head>
    <!--  Title -->
    <title>Kasir | @yield('title', 'Auth')</title>
    <!--  Required Meta Tag -->
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="handheldfriendly" content="true" />
    <meta name="MobileOptimized" content="width" />
    <meta name="description" content="Kasir" />
    <meta name="author" content="" />
    <meta name="keywords" content="Kasir" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!--  Favicon -->
    <link rel="shortcut icon" type="image/png" href="{{ asset('template/dist/images/logos/favicon.png') }}" />
    <!-- Core Css -->
    <link id="themeColors" rel="stylesheet" href="{{ asset('template/dist/css/style.min.css') }}" />
    {{-- Toastify --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('template/toastify/toastify.min.css') }}">
    <script type="text/javascript" src="{{ asset('template/toastify/toastify.min.js') }}"></script>
    @stack('head')
    @include('partials.darkmode')
</head>

<body>
    <!-- Preloader -->
    <style>
        @keyframes lds-ring { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .preloader { display: flex; justify-content: center; align-items: center; height: 100vh; }
        .lds-ring { display: inline-block; position: relative; width: 64px; height: 64px; }
        .lds-ring div { box-sizing: border-box; display: block; position: absolute; width: 51px; height: 51px; margin: 6px; border: 6px solid #1b64bd; border-radius: 50%; animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite; border-color: #1b64bd transparent transparent transparent; }
        .lds-ring div:nth-child(1) { animation-delay: -0.45s; }
        .lds-ring div:nth-child(2) { animation-delay: -0.3s; }
        .lds-ring div:nth-child(3) { animation-delay: -0.15s; }
    </style>
    <div class="preloader">
        <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
    </div>

    <!--  Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
        <div class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
            <div class="d-flex align-items-center justify-content-center w-100">
                <div class="row justify-content-center w-100">
                    <div class="col-md-8 col-lg-6 col-xxl-3">
                        {{-- toggle mode gelap/terang (rata kiri, di atas card) --}}
                        <div class="d-flex align-items-center gap-2 mb-3" style="position:relative; z-index:5;">
                            <i class="ti ti-sun fs-5" data-theme-icon></i>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input theme-switch" type="checkbox" role="switch" aria-label="Toggle mode gelap">
                            </div>
                            <span class="fs-3 text-muted" data-theme-label>Mode Terang</span>
                        </div>
                        <div class="card mb-0">
                            <div class="card-body">
                                @yield('content')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- modal opsional per halaman (mis. QR code) --}}
    @yield('modals')

    <!--  Import Js Files -->
    <script src="{{ asset('template/dist/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('template/dist/libs/simplebar/dist/simplebar.min.js') }}"></script>
    <script src="{{ asset('template/dist/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('template/dist/js/app.min.js') }}"></script>
    <script src="{{ asset('template/dist/js/app.init.js') }}"></script>
    <script src="{{ asset('template/dist/js/app-style-switcher.js') }}"></script>
    <script src="{{ asset('template/dist/js/sidebarmenu.js') }}"></script>
    <script src="{{ asset('template/dist/js/custom.js') }}"></script>

    {{-- toast error validasi (berlaku untuk semua halaman auth) --}}
    @if ($errors->any())
    <script>
        $(document).ready(function () {
            Toastify({
                text: @json($errors->first()),
                style: { background: "linear-gradient(to right, #f50707, #ff8f57)" },
                duration: 4000, close: false,
            }).showToast();
        });
    </script>
    @endif

    @stack('scripts')
</body>

</html>

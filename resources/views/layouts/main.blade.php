<!DOCTYPE html>
<html lang="en">

<head>
    <!--  Title -->
    <title>Toko | @yield('title')</title>
    <!--  Required Meta Tag -->
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="handheldfriendly" content="true" />
    <meta name="MobileOptimized" content="width" />
    <meta name="description" content="Mordenize" />
    <meta name="author" content="" />
    <meta name="keywords" content="Mordenize" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!--  Favicon -->
    <link rel="shortcut icon" type="image/png" href="{{ asset('template/dist/images/logos/favicon.png') }}" />
    <!-- Owl Carousel  -->
    <link rel="stylesheet" href="{{ asset('template/dist/libs/owl.carousel/dist/assets/owl.carousel.min.css') }}">
    <!-- Core Css -->
    <link  id="themeColors"  rel="stylesheet" href="{{ asset('template/dist/css/style.min.css') }}" />
    {{-- Toastify --}}
	  <link rel="stylesheet" type="text/css" href="{{ asset('template/toastify/toastify.min.css') }}">
	  <script type="text/javascript" src="{{ asset('template/toastify/toastify.min.js') }}"></script>
    {{-- Sweetalert --}}
	  <link rel="stylesheet" type="text/css" href="{{ asset('template/sweetalert/sweetalert2.min.css') }}">
	  <script type="text/javascript" src="{{ asset('template/sweetalert/sweetalert2.all.min.js') }}"></script>
    <!-- Datatable -->
    <link rel="stylesheet" href="{{ asset('template/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
    @include('partials.darkmode')
    <style>
      /* sidebar: aksen garis kiri + garis pemisah tiap menu */
      #sidebarnav .sidebar-item { border-bottom: 1px solid #eef1f6; }
      #sidebarnav .sidebar-item:last-child { border-bottom: 0; }
      #sidebarnav .sidebar-item .sidebar-link {
        margin: 0 !important;
        border-radius: 0 !important;
        border-left: 3px solid transparent;
        transition: background-color .15s, border-color .15s, color .15s;
      }
      /* hover menu non-aktif: teks & ikon selalu terbaca (cegah putih di atas latar terang) */
      #sidebarnav .sidebar-item .sidebar-link:hover { background-color: #f6f8fb !important; color: #2a3547 !important; }
      #sidebarnav .sidebar-item .sidebar-link:hover i,
      #sidebarnav .sidebar-item .sidebar-link:hover span { color: #2a3547 !important; }
      /* menu aktif (termasuk saat di-hover) tetap biru */
      #sidebarnav .sidebar-item .sidebar-link.active,
      #sidebarnav .sidebar-item .sidebar-link.active:hover {
        background-color: #ecf2ff !important;
        color: #5d87ff !important;
        border-left-color: #5d87ff !important;
        box-shadow: none !important;
      }
      #sidebarnav .sidebar-item .sidebar-link.active i,
      #sidebarnav .sidebar-item .sidebar-link.active span,
      #sidebarnav .sidebar-item .sidebar-link.active:hover i,
      #sidebarnav .sidebar-item .sidebar-link.active:hover span { color: #5d87ff !important; }
      /* rapatkan jarak label "Daftar Menu" ke atas */
      #sidebarnav { padding-top: 2px; }
      #sidebarnav .nav-small-cap { margin-top: 0 !important; padding-top: 6px !important; }
      /* Keyboard Cepat: sub-menu bergaris di bawah Layanan Kasir */
      .kc-conn { width: 12px; height: 20px; margin-top: -6px; flex-shrink: 0; border-left: 1.5px solid #d5dae4; border-bottom: 1.5px solid #d5dae4; border-bottom-left-radius: 8px; }
      .kc-sublink { display: flex; align-items: center; gap: 8px; flex: 1; margin-left: 6px; padding: 7px 10px; border-radius: 8px; color: #6b7385; font-size: 13px; text-decoration: none; transition: background .15s; }
      .kc-sublink:hover { background: #f2f5ff; color: #2a3547; }
      .kc-sublink i { font-size: 15px; color: #5d87ff; }
      .kc-sublink .kc-kbd { margin-left: auto; font-size: 11px; padding: 1px 6px; border-radius: 5px; background: rgba(93,135,255,.16); color: #4f74e6; }
      [data-bs-theme="dark"] .kc-conn { border-color: #3a4152; }
      [data-bs-theme="dark"] .kc-sublink { color: #aab2c5; }
      [data-bs-theme="dark"] .kc-sublink:hover { background: #2b3450; color: #e6e9f0; }
      [data-bs-theme="dark"] .kc-sublink .kc-kbd { color: #9db6ff; }
      /* konten full-width: buang max-width 1200px & auto-center bawaan template (sejajar header) */
      .body-wrapper > .container-fluid,
      .body-wrapper > .container-lg,
      .body-wrapper > .container-md,
      .body-wrapper > .container-sm,
      .body-wrapper > .container-xl,
      .body-wrapper > .container-xxl {
        max-width: 100% !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
      }
    </style>
  </head>
  <body>
    <!-- Preloader -->
    <style>
      /* CSS untuk animasi lingkaran putar */
      @keyframes lds-ring {
        0% {
          transform: rotate(0deg);
        }
        100% {
          transform: rotate(360deg);
        }
      }

      /* CSS untuk mengatur dimensi dan tata letak loader */
      .preloader {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
      }

      .lds-ring {
        display: inline-block;
        position: relative;
        width: 64px;
        height: 64px;
      }

      .lds-ring div {
        box-sizing: border-box;
        display: block;
        position: absolute;
        width: 51px;
        height: 51px;
        margin: 6px;
        border: 6px solid #5D87FF; /* Warna biru (#5D87FF) */
        border-radius: 50%;
        animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
        border-color: #5D87FF transparent transparent transparent; /* Warna biru (#5D87FF) */
      }

      .lds-ring div:nth-child(1) {
        animation-delay: -0.45s;
      }

      .lds-ring div:nth-child(2) {
        animation-delay: -0.3s;
      }

      .lds-ring div:nth-child(3) {
        animation-delay: -0.15s;
      }

    </style>
    <div class="preloader">
      <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
    </div>
    <!-- Preloader -->
    <div class="preloader">
      <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
    </div>
    <!--  Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-theme="blue_theme"  data-layout="vertical" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
      <!-- Sidebar Start -->
      <aside class="left-sidebar">
        <!-- Sidebar scroll-->
        <div>
          <div class="brand-logo d-flex align-items-center justify-content-between">
            <a href="{{ route('dashboard') }}" class="text-nowrap logo-img d-flex align-items-center gap-2">
              <i class="ti ti-shopping-cart text-primary" style="font-size:28px; line-height:1;"></i>
              <span style="font-size:21px; font-weight:700; color:#2a3547;">Kasir<span class="text-primary">Ku</span></span>
            </a>
            <div class="close-btn d-lg-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
              <i class="ti ti-x fs-8"></i>
            </div>
          </div>
          <!-- Sidebar navigation-->
          @php $u = Auth::user(); @endphp
          <nav class="sidebar-nav scroll-sidebar" data-simplebar>
            <ul id="sidebarnav">
              <!-- ============================= -->
              <!-- Menu -->
              <!-- ============================= -->
              {{-- Cari menu --}}
              <li class="px-3 pt-2 pb-1 sidebar-search-wrap">
                <div class="position-relative">
                  <i class="ti ti-search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#99a1b7; font-size:15px; pointer-events:none;"></i>
                  <input type="search" id="sidebarSearch" class="form-control form-control-sm" placeholder="Cari menu.." autocomplete="off" style="padding-left:34px; border-radius:8px;">
                </div>
              </li>
              <li class="px-3 py-2 text-muted fs-2 hide-menu" id="sidebarNoResult" style="display:none;">
                <i class="ti ti-mood-empty"></i> Menu tidak ditemukan
              </li>
              <li class="nav-small-cap">
                <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                <span class="hide-menu"></span>
              </li>
              {{-- Toggle Mode Gelap / Terang --}}
              <li class="sidebar-item">
                <div class="sidebar-link d-flex align-items-center justify-content-between" style="cursor:default;">
                  <span class="d-flex align-items-center gap-3">
                    <i class="ti ti-sun fs-5" data-theme-icon></i>
                    <span class="hide-menu" data-theme-label>Mode&nbsp;Terang</span>
                  </span>
                  <div class="form-check form-switch mb-0 ms-3">
                    <input class="form-check-input theme-switch" type="checkbox" role="switch" aria-label="Toggle mode gelap">
                  </div>
                </div>
              </li>
              <!-- =================== -->
              <!-- Dashboard (semua role) -->
              <!-- =================== -->
              <li class="sidebar-item">
                <a class="sidebar-link" href="{{ route('dashboard') }}" aria-expanded="false">
                  <span>
                    <i class="ti ti-chart-pie"></i>
                  </span>
                  <span class="hide-menu">Dashboard</span>
                </a>
              </li>
              {{-- Layanan Kasir: admin + kasir --}}
              @if ($u->isAdmin() || $u->isKasir())
              <li class="sidebar-item">
                <a class="sidebar-link" href="{{ route('kasir') }}" aria-expanded="false">
                  <span>
                    <i class="ti ti-shopping-cart"></i>
                  </span>
                  <span class="hide-menu">Layanan&nbsp;Kasir</span>
                </a>
              </li>
              {{-- Keyboard Cepat: pintasan keyboard, hanya muncul di halaman kasir --}}
              @if (request()->routeIs('kasir'))
              <li class="px-3" style="padding-bottom:8px;">
                <div style="display:flex; margin-left:22px;">
                  <span class="kc-conn"></span>
                  <a href="javascript:void(0)" id="btnFastHand" class="kc-sublink hide-menu">
                    <i class="ti ti-keyboard"></i><span>Pintasan</span><span class="kc-kbd">⇧ H</span>
                  </a>
                </div>
              </li>
              @endif
              @endif
              {{-- Kelola Pelanggan: admin + kasir --}}
              @if ($u->isAdmin() || $u->isKasir())
              <li class="sidebar-item">
                <a class="sidebar-link" href="{{ route('kelola_pelanggan') }}" aria-expanded="false">
                  <span>
                    <i class="ti ti-mood-smile"></i>
                  </span>
                  <span class="hide-menu">Kelola&nbsp;Pelanggan</span>
                </a>
              </li>
              @endif
              {{-- Kelola Barang: admin + stok --}}
              @if ($u->isAdmin() || $u->isStok())
              <li class="sidebar-item">
                <a class="sidebar-link" href="{{ route('kelola_barang') }}" aria-expanded="false">
                  <span>
                    <i class="ti ti-box"></i>
                  </span>
                  <span class="hide-menu">Kelola&nbsp;Barang</span>
                </a>
              </li>
              @endif
              {{-- Data Transaksi: admin only --}}
              @if ($u->isAdmin())
              <li class="sidebar-item">
                <a class="sidebar-link" href="{{ route('data_transaksi') }}" aria-expanded="false">
                    <span>
                        <i class="ti ti-history"></i>
                      </span>
                      <span class="hide-menu">Data&nbsp;Transaksi</span>
                  </a>
              </li>
              @endif
              {{-- Kelola Diskon: admin only --}}
              @if ($u->isAdmin())
              <li class="sidebar-item">
                <a class="sidebar-link" href="{{ route('kelola_diskon') }}" aria-expanded="false">
                    <span>
                        <i class="ti ti-discount-2"></i>
                      </span>
                      <span class="hide-menu">Kelola&nbsp;Diskon</span>
                  </a>
              </li>
              @endif
              {{-- Kelola User: admin only --}}
              @if ($u->isAdmin())
              <li class="sidebar-item">
                <a class="sidebar-link" href="{{ route('kelola_user') }}" aria-expanded="false">
                    <span>
                        <i class="ti ti-users"></i>
                      </span>
                      <span class="hide-menu">Kelola&nbsp;User</span>
                  </a>
              </li>
              @endif
              {{-- Pengaturan Toko: admin + stok --}}
              @if ($u->isAdmin() || $u->isStok())
              <li class="sidebar-item">
                <a class="sidebar-link" href="{{ route('pengaturan') }}" aria-expanded="false">
                    <span>
                        <i class="ti ti-settings"></i>
                      </span>
                      <span class="hide-menu">Pengaturan&nbsp;Toko</span>
                  </a>
              </li>
              @endif
              {{-- Pengaturan Email: admin only --}}
              @if ($u->isAdmin())
              <li class="sidebar-item">
                <a class="sidebar-link" href="{{ route('pengaturan_email') }}" aria-expanded="false">
                    <span>
                        <i class="ti ti-mail"></i>
                      </span>
                      <span class="hide-menu">Pengaturan&nbsp;Email</span>
                  </a>
              </li>
              @endif
              {{-- Pengaturan Printer: admin + kasir --}}
              @if ($u->isAdmin() || $u->isKasir())
              <li class="sidebar-item">
                <a class="sidebar-link btnModalPrinter" href="javascript:void(0)" data-toggle="modal" data-target="ModalPengaturanPrinter" aria-expanded="false">
                    <span>
                      <i class="ti ti-printer"></i>
                    </span>
                    <span class="hide-menu">Pengaturan&nbsp;Printer</span>
                  </a>
              </li>
              @endif
              <!-- ============================= -->

            </ul>
          </nav>
          <!-- End Sidebar navigation -->
        </div>
        <!-- End Sidebar scroll-->
      </aside>
      <!--  Sidebar End -->
      <!--  Main wrapper -->
      <div class="body-wrapper">
        <!--  Header Start -->
        <header class="app-header">
          <nav class="navbar navbar-expand-lg navbar-light">
            <ul class="navbar-nav">
              <li class="nav-item">
                <a class="nav-link sidebartoggler ms-n3" id="headerCollapse" href="javascript:void(0)">
                  <i class="ti ti-layout-sidebar text-dark"></i>
                </a>
              </li>
            </ul>
            @php
              $namaToko = rescue(fn() => \DB::table('pengaturan')->where('key', 'nama_toko')->value('value'), null) ?: 'Toko Saya';
            @endphp
            <ul class="navbar-nav quick-links d-none d-lg-flex">
              <li class="nav-item d-flex align-items-center ms-2">
                <div style="line-height:1.2;">
                  {{-- <div class="fs-4 fw-semibold text-dark">{{ $namaToko }}</div> --}}
                  @php $roleBadge = ['admin' => 'danger', 'stok' => 'success', 'kasir' => 'primary'][$u->role] ?? 'secondary'; @endphp
                  <div class="fs-2 text-muted">Halo <span class="badge bg-{{ $roleBadge }}">{{ Auth::user()->roleLabel() }}</span> 👋, selamat bertugas</div>
                </div>
              </li>
            </ul>
            <div class="d-flex d-lg-none align-items-center gap-2">
              <i class="ti ti-shopping-cart text-primary" style="font-size:26px; line-height:1;"></i>
              <span style="font-size:20px; font-weight:700; color:#2a3547;">Kasir<span class="text-primary">Ku</span></span>
            </div>
            <button class="navbar-toggler p-0 border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
              <span class="p-2">
                <i class="ti ti-dots fs-7"></i>
              </span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <div class="d-flex align-items-center justify-content-between">
                    <a href="javascript:void(0)" class="nav-link d-flex d-lg-none align-items-center justify-content-center" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobilenavbar" aria-controls="offcanvasWithBothOptions">
                        Data Profil&emsp;<i class="ti ti-chevron-right fs-7"></i>&emsp;Detail&emsp;<i class="ti ti-chevron-right fs-7"></i>
                    </a>
                <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-center">
                  <li class="nav-item me-2">
                    <a class="nav-link p-0" href="javascript:void(0)" id="btnFullscreen" title="Layar penuh" aria-label="Layar penuh">
                      <div class="d-flex align-items-center justify-content-center rounded-circle bg-light-primary text-primary" style="width:38px;height:38px;">
                        <i class="ti ti-maximize fs-5" id="fullscreenIcon"></i>
                      </div>
                    </a>
                  </li>
                  <li class="nav-item dropdown">
                    <a class="nav-link pe-0" href="javascript:void(0)" id="drop1" data-bs-toggle="dropdown" aria-expanded="false">
                      <div class="d-flex align-items-center">
                        <div class="d-flex align-items-center justify-content-center rounded-circle bg-light-primary text-primary" style="width:38px;height:38px;">
                          <i class="ti ti-user fs-5"></i>
                        </div>
                      </div>
                    </a>
                    <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop1">
                      <div class="profile-dropdown position-relative" data-simplebar>
                        <div class="py-3 px-7 pb-0">
                          <h5 class="mb-0 fs-5 fw-semibold">Data Profil</h5>
                        </div>
                        <div class="d-flex align-items-center py-9 mx-7 border-bottom">
                          <div class="d-flex align-items-center justify-content-center rounded-circle bg-light-primary text-primary flex-shrink-0" style="width:72px;height:72px;">
                            <i class="ti ti-user" style="font-size:36px;"></i>
                          </div>
                          <div class="ms-3">
                            <h5 class="mb-1 fs-3">{{ Auth::user()->name }}</h5>
                            <span class="mb-1 d-block text-dark">{{ Auth::user()->roleLabel() }}</span>
                            <p class="mb-0 d-flex text-dark align-items-center gap-2">
                              <i class="ti ti-mail fs-4"></i> {{ Auth::user()->email }}
                            </p>
                          </div>
                        </div>
                        <div class="message-body" style="padding-right:20px; padding-left:20px;">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="btn btn-dark" style="width: 100%;">Logout <i class="ti ti-logout ms-2"></i></a>
                            </form>
                          <br>
                        </div>
                      </div>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </nav>
        </header>
        <!--  Header End -->

        {{-- Main content --}}
        <script src="{{ asset('template/dist/libs/jquery/dist/jquery.min.js') }}"></script>
        @yield('content')
        {{-- Main content --}}

      </div>
    </div>

    <!--  Import Js Files -->

    <script src="{{ asset('template/dist/libs/simplebar/dist/simplebar.min.js') }}"></script>
    <script src="{{ asset('template/dist/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <!--  core files -->
    <script src="{{ asset('template/dist/js/app.min.js') }}"></script>
    <script src="{{ asset('template/dist/js/app.init.js') }}"></script>
    <script src="{{ asset('template/dist/js/app-style-switcher.js') }}"></script>
    <script src="{{ asset('template/dist/js/sidebarmenu.js') }}"></script>
    <script src="{{ asset('template/dist/js/custom.js') }}"></script>
    <script>
        // FIX (Android): saat fokus ke input (mis. cari menu di sidebar), keyboard
        // virtual muncul & memicu event 'resize'. Handler template (setsidebartype)
        // ikut jalan lalu menambah class 'mini-sidebar' -> sidebar yang terbuka
        // menutup sendiri. Keyboard hanya mengubah TINGGI, bukan LEBAR; jadi kalau
        // lebar layar tak berubah, blokir handler resize template (fase capture,
        // selalu jalan sebelum handler jQuery vendor).
        (function () {
            var lastW = window.innerWidth;
            window.addEventListener('resize', function (e) {
                var w = window.innerWidth;
                if (w === lastW) { e.stopImmediatePropagation(); } // resize karena keyboard -> abaikan
                lastW = w;
            }, true);
        })();
    </script>
    <!--  current page js files -->
    <script src="{{ asset('template/dist/libs/owl.carousel/dist/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('template/dist/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>

    @include('menu.script_printer_thermal')

    <script>
        // Fix: input SweetAlert2 bisa fokus/ketik saat muncul di atas Bootstrap modal.
        // (Bootstrap modal punya focus-trap yang mencuri fokus dari elemen di luar modal)
        document.addEventListener('focusin', function (e) {
          if (e.target && e.target.closest && e.target.closest('.swal2-container')) {
            e.stopImmediatePropagation();
          }
        }, true);

        // CARI MENU DI SIDEBAR (filter langsung)
        $(document).on('input', '#sidebarSearch', function () {
          var q = $(this).val().toLowerCase().trim();
          var any = false;
          $('#sidebarnav > li.sidebar-item').each(function () {
            var $a = $(this).children('a.sidebar-link');
            if (!$a.length) { return; } // lewati item non-menu (mis. toggle mode gelap)
            var text = $(this).find('.hide-menu').text().toLowerCase();
            var match = (q === '' || text.indexOf(q) !== -1);
            $(this).toggle(match);
            if (match) { any = true; }
          });
          $('#sidebarNoResult').toggle(!any && q !== '');
        });
        // ESC = kosongkan pencarian
        $(document).on('keydown', '#sidebarSearch', function (e) {
          if (e.key === 'Escape') { $(this).val('').trigger('input'); }
        });

        // ===== LAYAR PENUH (Fullscreen API) =====
        function isFullscreen() { return !!(document.fullscreenElement || document.webkitFullscreenElement || document.msFullscreenElement); }
        function toggleFullscreen() {
          if (!isFullscreen()) {
            var el = document.documentElement;
            var req = el.requestFullscreen || el.webkitRequestFullscreen || el.msRequestFullscreen;
            if (req) { req.call(el); }
          } else {
            var exit = document.exitFullscreen || document.webkitExitFullscreen || document.msExitFullscreen;
            if (exit) { exit.call(document); }
          }
        }
        function syncFullscreenIcon() {
          var ic = document.getElementById('fullscreenIcon');
          if (ic) { ic.className = (isFullscreen() ? 'ti ti-minimize' : 'ti ti-maximize') + ' fs-5'; }
        }
        document.addEventListener('fullscreenchange', syncFullscreenIcon);
        document.addEventListener('webkitfullscreenchange', syncFullscreenIcon);
        $(document).on('click', '#btnFullscreen', function (e) { e.preventDefault(); toggleFullscreen(); });

        $(document).on('click','.btnModalPrinter', function() {
          $('#ModalPengaturanPrinter').modal('show');
          // tombol ini pemicu modal (bukan halaman) -> jangan biarkan jadi menu aktif.
          // kembalikan highlight ke menu halaman yang sedang dibuka.
          var here = window.location.href;
          $('#sidebarnav a.sidebar-link').removeClass('active');
          $('#sidebarnav a.sidebar-link').filter(function () { return this.href === here; }).addClass('active');
        })
        // PROGRESS BAR (global, dipakai banyak halaman)
        function animasiProgressBar_run() { $('.animasiProgressBar').css('width', '100%'); }
        function animasiProgressBar_stop() { $('.animasiProgressBar').css('width', '0%'); }
        // SCRIPT TOAST & CONFIRMATION
        function toastSuccess(message) {
          Toastify({
              text: message,
              style: {
                  background: "linear-gradient(to right, #00b09b, #96c93d)",
              },
              duration: 3000,
              close: false,
          }).showToast();
        }
        function toastError(message) {
          Toastify({
              text: message,
              style: {
                  background: "linear-gradient(to right, #f50707, #ff8f57)",
              },
              duration: 4000,
              close: false,
          }).showToast();
        }
        function errorHandlerToast(value) {
          Object.values(value).forEach(function(errors) {
              errors.forEach(function(error) {
                toastError(error);
              });
          });
        }

        // ============ AUTO FORMAT INPUT RUPIAH ============
        // "Rp 5.000" / "5000"  ->  integer 5000
        function rupiahToInt(val) {
            if (val === null || val === undefined) return 0;
            var digits = String(val).replace(/[^0-9]/g, '');
            return digits === '' ? 0 : parseInt(digits, 10);
        }
        // integer 5000 -> tampilan "Rp 5.000"
        function formatRupiahDisplay(val) {
            var n = rupiahToInt(val);
            if (n === 0) return '';
            return 'Rp ' + n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }
        // format satu elemen input + jaga posisi kursor
        function formatRupiahInputEl(el) {
            var start = el.selectionStart;
            var before = el.value;
            var formatted = formatRupiahDisplay(before);
            el.value = formatted;
            if (el.type !== 'number') {
                var diff = formatted.length - before.length;
                var pos = Math.max(0, (start || formatted.length) + diff);
                try { el.setSelectionRange(pos, pos); } catch (e) {}
            }
        }
        // serialize form dengan semua input rupiah dikonversi ke integer
        function serializeFormRupiah($form) {
            var changed = [];
            $form.find('.input-rupiah').each(function () {
                changed.push([this, this.value]);
                this.value = rupiahToInt(this.value);
            });
            var data = $form.serialize();
            changed.forEach(function (pair) { pair[0].value = pair[1]; });
            return data;
        }
        // FormData (untuk form dgn file) — input rupiah dikonversi ke integer
        function formDataRupiah(formEl) {
            var fd = new FormData(formEl);
            $(formEl).find('.input-rupiah').each(function () {
                if (this.name) fd.set(this.name, rupiahToInt(this.value));
            });
            return fd;
        }
        // live-format saat mengetik
        $(document).on('input', '.input-rupiah', function () {
            formatRupiahInputEl(this);
        });
        // format nilai prefilled saat modal dibuka & saat halaman siap
        $(document).on('shown.bs.modal', '.modal', function () {
            $(this).find('.input-rupiah').each(function () { formatRupiahInputEl(this); });
        });
        $(function () {
            $('.input-rupiah').each(function () { formatRupiahInputEl(this); });
        });
        function customConfirm(title, message) {
            toggleTabIndex(true);
            return new Promise((resolve) => {
                Swal.fire({
                    title: title,
                    text: message,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Ya, lanjutkan",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    toggleTabIndex(false);
                    resolve(result.isConfirmed);
                });
            });
        }
        function toggleTabIndex(status) {
            // Pilih semua elemen input dalam formulir
            var formInputs = $('input, textarea');
            // Iterasi melalui elemen-elemen input dan atur/menghapus atribut tabindex
            formInputs.each(function () {
              if (status) {
                  // Jika modal diaktifkan, tambahkan atribut tabindex="-1"
                  $(this).attr('tabindex', '-1');
              } else {
                  // Jika modal dinonaktifkan, hapus atribut tabindex
                  $(this).attr('tabindex',' ');
              }
            });
        }
    </script>
  </body>

</html>

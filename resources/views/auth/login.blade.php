<!DOCTYPE html>
<html lang="en">

<head>
    <!--  Title -->
    <title>Mordenize | Login</title>
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
    <!-- Core Css -->
    <link id="themeColors" rel="stylesheet" href="{{ asset('template/dist/css/style.min.css') }}" />
    {{-- Toastify --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('template/toastify/toastify.min.css') }}">
    <script type="text/javascript" src="{{ asset('template/toastify/toastify.min.js') }}"></script>
    {{-- QR code --}}
    <script src="{{ asset('template/qrcode/qrcode.min.js') }}"></script>
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
        border: 6px solid #0b6280; /* Warna biru muda (#00BFFF) */
        border-radius: 50%;
        animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
        border-color: #00BFFF transparent transparent transparent; /* Warna biru muda (#00BFFF) */
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
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
        <div class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
            <div class="d-flex align-items-center justify-content-center w-100">
                <div class="row justify-content-center w-100">
                    <div class="col-md-8 col-lg-6 col-xxl-3">
                        <div class="card mb-0">
                            <div class="card-body">
                                <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ModalQrCode" class="text-nowrap logo-img text-center d-block mb-5 w-100">
                                    <img src="{{ asset('template/dist/images/logos/dark-logo.svg') }}" width="180" alt="">
                                </a>
                                <div class="row">
                                </div>
                                <form action="{{ route('login') }}" method="POST" autocomplete="off">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="Masukan email anda .." required>
                                    </div>
                                    <div class="mb-4">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" class="form-control" name="password" id="password" placeholder="Masukan password .." required>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-4">
                                        <div class="form-check">
                                            <input class="form-check-input primary" type="checkbox" name="remember" id="remember_me">
                                            <label class="form-check-label text-dark" for="remember_me">
                                                Ingat info login ?
                                            </label>
                                        </div>
                                        {{-- <a class="text-primary fw-medium" href="{{ route('password.request') }}">Lupa password ?</a> --}}
                                        <a class="text-primary fw-medium" href="javascript:void(0)" onclick="notify('Silahkan hubungi developer!')">Lupa password ?</a>
                                    </div>
                                    <button type="submit" id="submitButton" class="btn btn-primary w-100 py-8 mb-4 rounded-2">Masuk</button>
                                    <div class="d-flex align-items-center justify-content-center">
                                        <p class="fs-4 mb-0 fw-medium">Member baru ?</p>
                                        <a class="text-primary fw-medium ms-2" href="{{ route('register') }}">Buat akun</a>
                                        {{-- <a class="text-primary fw-medium ms-2" href="javascript:void(0)" onclick="notify('Silahkan hubungi developer!')">Buat akun</a> --}}
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL QR-CODE --}}
    <div class="modal fade" id="ModalQrCode" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" style="display: none;" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header d-flex align-items-center">
            <h4 class="modal-title" id="myLargeModalLabel" hidden>
              {{ url()->current() }}
            </h4>
          </div>
          <div class="modal-body">
            <div class="card-body">
              <!--/row-->
              <div class="col-12" hidden>
                <div class="alert customize-alert alert-dismissible text-primary border border-primary fade show remove-close-icon" role="alert">
                  <div class="d-flex align-items-center font-medium me-3 me-md-0">
                    <i class="ti ti-info-circle fs-5 me-2 flex-shrink-0 text-primary"></i>
                    Scan untuk mengakses aplikasi
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12" align="center">
                  {{--  --}}
                  <br>
                  <div id="qrcode"></div>
                  {{--  --}}
                </div>
                <!--/span-->
              </div>
              <!--/row-->
            </div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup&emsp;<i class="ti ti-x"></i></button>
          </div>
        </div>
      </div>
    </div>

    <!--  Import Js Files -->
    <script src="{{ asset('template/dist/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('template/dist/libs/simplebar/dist/simplebar.min.js') }}"></script>
    <script src="{{ asset('template/dist/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <!--  core files -->
    <script src="{{ asset('template/dist/js/app.min.js') }}"></script>
    <script src="{{ asset('template/dist/js/app.init.js') }}"></script>
    <script src="{{ asset('template/dist/js/app-style-switcher.js') }}"></script>
    <script src="{{ asset('template/dist/js/sidebarmenu.js') }}"></script>

    <script src="{{ asset('template/dist/js/custom.js') }}"></script>


    @error('email')
    <script>
        $(document).ready(function () {
            Toastify({
                text: "Email / Password Anda Salah!",
                style: {
                    background: "linear-gradient(to right, #f50707, #ff8f57)",
                },
                duration: 4000,
                close: false,
            }).showToast();
        });
    </script>
    @enderror

    <script>
      var currentUrl = "{{ url()->current() }}";
      var qrcode = new QRCode("qrcode", currentUrl);
      function notify(pesan) {
        Toastify({
            text: pesan,
            style: {
                background: "linear-gradient(to right, #f50707, #ff8f57)",
            },
            duration: 2000,
            close: false,
        }).showToast();
      }
    </script>

</body>

</html>

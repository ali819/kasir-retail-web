@extends('layouts.main')
@section('content')
@section('title', 'Pengaturan Email')

<style>
    .hr-dotted { border: none; border-top: 2px dotted #b0b8c4; margin: 1rem 0; }
    [data-bs-theme="dark"] .hr-dotted { border-top-color: #3a4152; }
    .provider-table td, .provider-table th { padding: 6px 8px; vertical-align: middle; }
    .step-num { width: 26px; height: 26px; border-radius: 50%; background: #5d87ff; color: #fff; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0; font-size: 13px; }
</style>

{{-- MAIN --}}
<div class="container-fluid">
    <div class="card bg-light-info shadow-none position-relative overflow-hidden">
        <div class="card-body px-4 py-3">
            <div class="row align-items-center">
                <div class="col-9">
                    <h4 class="fw-semibold mb-8">Pengaturan Email</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a class="text-muted" href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item" aria-current="page">Pengaturan Email</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-3"></div>
            </div>
        </div>
        <div class="col-12">
            <div class="progress">
                <div class="progress-bar progress-bar-striped bg-primary progress-bar-animated animasiProgressBar" role="progressbar" style="width: 0%"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="alert customize-alert text-primary border border-primary" role="alert">
                <div class="d-flex align-items-center font-medium">
                    <i class="ti ti-info-circle fs-5 me-2 flex-shrink-0"></i>
                    Konfigurasi server SMTP untuk mengirim email <b>&nbsp;reset password&nbsp;</b>. Disimpan di database (bukan file .env). Password SMTP disimpan terenkripsi.
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- FORM SMTP --}}
        <div class="col-lg-7 col-12">
            <div class="card">
                <div class="card-body">
                    <form id="FormEmail" autocomplete="off">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="mb-1"><b>SMTP Host</b> <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="mail_host" id="mailHost" placeholder="mis. smtp.gmail.com" value="{{ $mail_host }}">
                            </div>
                            <div class="col-md-4">
                                <label class="mb-1"><b>Port</b> <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="mail_port" id="mailPort" placeholder="587" value="{{ $mail_port }}">
                            </div>
                            <div class="col-md-6">
                                <label class="mb-1"><b>Username</b></label>
                                <input type="text" class="form-control" name="mail_username" id="mailUsername" placeholder="email@domain.com" value="{{ $mail_username }}">
                            </div>
                            <div class="col-md-6">
                                <label class="mb-1"><b>Password / App Password</b></label>
                                <input type="password" class="form-control" name="mail_password" id="mailPassword" placeholder="{{ $has_password ? '•••••• (biarkan kosong bila tak diubah)' : 'Password SMTP' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="mb-1"><b>Enkripsi</b></label>
                                <select class="form-control" name="mail_encryption" id="mailEncryption">
                                    <option value="tls" {{ $mail_encryption == 'tls' ? 'selected' : '' }}>TLS (port 587)</option>
                                    <option value="ssl" {{ $mail_encryption == 'ssl' ? 'selected' : '' }}>SSL (port 465)</option>
                                    <option value="none" {{ $mail_encryption == 'none' ? 'selected' : '' }}>Tanpa Enkripsi</option>
                                </select>
                            </div>
                            <div class="col-12"><hr class="hr-dotted"></div>
                            <div class="col-md-6">
                                <label class="mb-1"><b>Email Pengirim (From)</b> <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="mail_from_address" id="mailFromAddress" placeholder="noreply@toko.com" value="{{ $mail_from_address }}">
                            </div>
                            <div class="col-md-6">
                                <label class="mb-1"><b>Nama Pengirim</b> <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="mail_from_name" id="mailFromName" placeholder="Nama Toko" value="{{ $mail_from_name }}">
                            </div>
                        </div>

                        <hr class="hr-dotted">
                        <div align="right">
                            <button type="submit" class="btn btn-primary btnSimpanEmail" style="font-weight:bold;">Simpan&emsp;<i class="ti ti-check"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- TEST + PANDUAN --}}
        <div class="col-lg-5 col-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3"><i class="ti ti-send"></i>&nbsp; Test Kirim Email</h6>
                    <p class="text-muted fs-2 mb-2">Kirim email percobaan memakai konfigurasi di form sebelah (isi &amp; simpan dulu bila perlu).</p>
                    <div class="input-group">
                        <input type="email" class="form-control" id="emailTujuan" placeholder="email-tujuan@domain.com" value="{{ auth()->user()->email }}">
                        <button type="button" class="btn btn-info btnTestEmail"><i class="ti ti-send"></i>&nbsp; Kirim</button>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h6 class="fw-semibold mb-2"><i class="ti ti-server-2"></i>&nbsp; Pilih Provider SMTP (Gratis)</h6>
                    <p class="text-muted fs-2 mb-2">Isi kolom di form kiri sesuai provider yang dipilih. Semua di bawah punya paket gratis:</p>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered provider-table fs-2 mb-2">
                            <thead class="table-light">
                                <tr>
                                    <th>Provider</th>
                                    <th>Host</th>
                                    <th>Port / Enkripsi</th>
                                    <th>Kuota gratis</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><b>Brevo</b> <span class="text-muted">(mudah)</span></td>
                                    <td>smtp-relay.brevo.com</td>
                                    <td>587 / TLS</td>
                                    <td>300 email/hari</td>
                                </tr>
                                <tr>
                                    <td><b>Resend</b></td>
                                    <td>smtp.resend.com</td>
                                    <td>465 / SSL</td>
                                    <td>3.000/bln (100/hari)</td>
                                </tr>
                                <tr>
                                    <td><b>SMTP2GO</b></td>
                                    <td>mail.smtp2go.com</td>
                                    <td>587 / TLS</td>
                                    <td>1.000 email/bln</td>
                                </tr>
                                <tr>
                                    <td><b>Gmail</b></td>
                                    <td>smtp.gmail.com</td>
                                    <td>587 / TLS</td>
                                    <td>±500 email/hari</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="alert customize-alert text-primary border border-primary py-2 fs-2 mb-3">
                        <i class="ti ti-check me-1"></i> <b>Resend cocok</b> untuk aplikasi ini. Resend menyediakan SMTP — isi <b>Username</b> = <code>resend</code> dan <b>Password</b> = <b>API Key</b> (diawali <code>re_...</code>). Untuk <b>Email Pengirim</b> pakai domain yang sudah diverifikasi di Resend, atau <code>onboarding@resend.dev</code> untuk sekadar uji coba.
                    </div>

                    <h6 class="fw-semibold mb-2"><i class="ti ti-list-numbers"></i>&nbsp; Langkah Umum</h6>
                    <div class="d-flex gap-2 mb-2">
                        <span class="step-num">1</span>
                        <div class="fs-2">Daftar akun di salah satu provider di atas (mis. <b>Brevo</b> paling gampang, tanpa perlu domain sendiri).</div>
                    </div>
                    <div class="d-flex gap-2 mb-2">
                        <span class="step-num">2</span>
                        <div class="fs-2">Buka menu <b>SMTP</b> / <b>API Keys</b> di dashboard provider, lalu salin <b>Host, Port, Username, Password/API Key</b>.</div>
                    </div>
                    <div class="d-flex gap-2 mb-2">
                        <span class="step-num">3</span>
                        <div class="fs-2">Tempel ke form di kiri. <b>Enkripsi</b>: pilih <b>TLS</b> untuk port 587, atau <b>SSL</b> untuk port 465.</div>
                    </div>
                    <div class="d-flex gap-2 mb-2">
                        <span class="step-num">4</span>
                        <div class="fs-2">Isi <b>Email Pengirim</b> (biasanya harus email/domain yang sudah diverifikasi provider) &amp; <b>Nama Pengirim</b>, lalu <b>Simpan</b>.</div>
                    </div>
                    <div class="d-flex gap-2 mb-0">
                        <span class="step-num">5</span>
                        <div class="fs-2">Klik <b>Test Kirim</b> di atas. Jika email masuk, fitur "Lupa password?" siap dipakai.</div>
                    </div>

                    <hr class="hr-dotted">
                    <h6 class="fw-semibold mb-1"><i class="ti ti-brand-google"></i>&nbsp; Khusus Gmail</h6>
                    <p class="mb-0 text-muted fs-2">Password bukan password akun, tapi <b>App Password</b>: aktifkan <b>Verifikasi 2 Langkah</b> di Akun Google &rarr; <b>Security</b> &rarr; <b>App passwords</b> &rarr; buat sandi 16 huruf, tempel ke kolom Password.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // SIMPAN
    $('#FormEmail').on('submit', function (e) {
        e.preventDefault();
        var btn = $('.btnSimpanEmail').html();
        $('.btnSimpanEmail').html('Menyimpan ..').attr('disabled', true);
        animasiProgressBar_run();
        $.ajax({
            type: "POST", url: "{{ route('update_pengaturan_email') }}", data: $(this).serialize(), dataType: "JSON",
            success: function (r) {
                if (r.kode == 200) { toastSuccess(r.pesan); $('#mailPassword').val(''); }
                else if (r.kode == 422) { errorHandlerToast(r.pesan); }
                else { toastError(r.pesan); }
            },
            error: function () { toastError("Oops! Silahkan coba lagi!"); },
            complete: function () { $('.btnSimpanEmail').html(btn).attr('disabled', false); animasiProgressBar_stop(); }
        });
    });

    // TEST
    $(document).on('click', '.btnTestEmail', function () {
        var tujuan = $('#emailTujuan').val();
        if (!tujuan) { toastError("Isi email tujuan dulu!"); return; }
        var btn = $(this).html();
        var $b = $(this);
        $b.html('Mengirim ..').attr('disabled', true);
        animasiProgressBar_run();
        $.ajax({
            type: "POST", url: "{{ route('test_email') }}",
            data: {
                _token: "{{ csrf_token() }}",
                tujuan: tujuan,
                mail_host: $('#mailHost').val(),
                mail_port: $('#mailPort').val(),
                mail_username: $('#mailUsername').val(),
                mail_password: $('#mailPassword').val(),
                mail_encryption: $('#mailEncryption').val(),
                mail_from_address: $('#mailFromAddress').val(),
                mail_from_name: $('#mailFromName').val()
            },
            dataType: "JSON",
            success: function (r) {
                if (r.kode == 200) { toastSuccess(r.pesan); }
                else if (r.kode == 422) { errorHandlerToast(r.pesan); }
                else { toastError(r.pesan); }
            },
            error: function () { toastError("Oops! Silahkan coba lagi!"); },
            complete: function () { $b.html(btn).attr('disabled', false); animasiProgressBar_stop(); }
        });
    });
</script>

@endsection

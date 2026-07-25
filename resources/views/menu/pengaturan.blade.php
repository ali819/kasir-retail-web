@extends('layouts.main')
@section('content')
@section('title', 'Pengaturan Toko')

{{-- MAIN --}}
<div class="container-fluid">
    <div class="card bg-light-info shadow-none position-relative overflow-hidden">
        <div class="card-body px-4 py-3">
            <div class="row align-items-center">
                <div class="col-9">
                    <h4 class="fw-semibold mb-8">Pengaturan Toko</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a class="text-muted" href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">Pengaturan Toko</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-3">
                    <div class="text-center mb-n5">
                        {{--  --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="progress">
                <div class="progress-bar progress-bar-striped bg-primary progress-bar-animated animasiProgressBar" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="alert customize-alert alert-dismissible text-primary border border-primary fade show remove-close-icon" role="alert">
                <div class="d-flex align-items-center font-medium me-3 me-md-0">
                    <i class="ti ti-info-circle fs-5 me-2 flex-shrink-0 text-primary"></i>
                    Data di bawah ini otomatis tampil pada struk / nota pembelian.
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- FORM SETELAN --}}
        <div class="col-lg-7 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="mb-1"><b>Nama Toko</b> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="namaToko" name="namaToko" placeholder="Nama Toko .." value="{{ $nama_toko }}" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label class="mb-1"><b>Alamat</b></label>
                        <textarea class="form-control" id="alamatToko" name="alamatToko" rows="3" placeholder="Alamat toko ..">{{ $alamat_toko }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="mb-1"><b>No. Telepon</b></label>
                        <input type="text" class="form-control" id="noTelp" name="noTelp" placeholder="contoh: 0812-xxxx-xxxx" value="{{ $no_telp }}" autocomplete="off">
                        <small class="text-muted">Kosongkan bila tidak ingin ditampilkan di struk.</small>
                    </div>
                    <div class="mb-2">
                        <label class="mb-1"><b>Footer Nota</b></label>
                        <textarea class="form-control" id="footerNota" name="footerNota" rows="2" placeholder="contoh: Terima kasih telah berbelanja :)">{{ $footer_nota }}</textarea>
                        <small class="text-muted">Kalimat penutup di bagian bawah struk. Kosongkan bila tidak dipakai.</small>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="tampilkanKasir" {{ ($tampilkan_kasir ?? 1) ? 'checked' : '' }}>
                            <label class="form-check-label" for="tampilkanKasir"><b>Tampilkan nama kasir di struk</b></label>
                        </div>
                        <small class="text-muted">Jika aktif, struk mencantumkan <b>KASIR : (nama)</b> — biar tahu siapa kasirnya.</small>
                    </div>

                    <div class="mb-2">
                        <label class="mb-1"><b>Logo Toko</b></label>
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <img id="logoPreview" src="{{ $logo_url ?: '' }}" alt="logo"
                                 style="height:64px; max-width:150px; object-fit:contain; border:1px solid #eee; border-radius:8px; padding:4px; {{ $logo_url ? '' : 'display:none;' }}">
                            <div class="flex-grow-1">
                                <input type="file" class="form-control" id="logoFile" name="logo" accept="image/png,image/jpeg,image/webp">
                                <small class="text-muted">PNG / JPG / WEBP, maks 2 MB. Tampil di atas struk.</small>
                            </div>
                        </div>
                        <button type="button" id="btnHapusLogo" class="btn btn-sm btn-outline-danger {{ $logo_url ? '' : 'd-none' }}"><i class="ti ti-trash"></i> Hapus Logo</button>
                        <input type="hidden" id="hapusLogo" value="0">
                    </div>

                    <hr>
                    <div align="right">
                        <button type="button" id="btnSimpanPengaturan" class="btn btn-primary" style="font-weight:bold;">Simpan&emsp;<i class="ti ti-check"></i></button>
                    </div>
                </div>
            </div>
        </div>

        {{-- PRATINJAU NOTA --}}
        <div class="col-lg-5 col-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3"><i class="ti ti-receipt"></i>&nbsp; Pratinjau Struk</h6>
                    <div class="border rounded p-3 nota-preview" style="font-family: 'Courier New', monospace; font-size: 13px; background:#fff;">
                        <div class="text-center">
                            <img id="prevLogo" src="{{ $logo_url ?: '' }}" style="max-height:70px; max-width:150px; margin-bottom:6px; {{ $logo_url ? '' : 'display:none;' }}">
                            <div class="fw-bold" id="prevNama">{{ $nama_toko }}</div>
                            <div id="prevAlamat">{{ $alamat_toko }}</div>
                            <div id="prevTelp">{{ $no_telp }}</div>
                        </div>
                        <div style="border-top:1px dashed #999; margin:8px 0;"></div>
                        <div>TRX-260725-1</div>
                        <div class="text-muted">25 Juli 2026 10:10</div>
                        <div id="prevKasir">KASIR : {{ Auth::user()->name }}</div>
                        <div style="border-top:1px dashed #999; margin:8px 0;"></div>
                        <div>Contoh Barang (Eceran)</div>
                        <div>2 x 5000 : Rp 10.000</div>
                        <div style="border-top:1px dashed #999; margin:8px 0;"></div>
                        <div>SUBTOTAL : Rp 10.000</div>
                        <div>DISKON (contoh 10%) : - Rp 1.000</div>
                        <div class="fw-bold">TOTAL : Rp 9.000</div>
                        <div>BAYAR : Rp 10.000</div>
                        <div>KEMBALI : Rp 1.000</div>
                        <div style="border-top:1px dashed #999; margin:8px 0;"></div>
                        <div class="text-center fw-semibold" id="prevFooter">{{ $footer_nota }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // PRATINJAU LANGSUNG
    function refreshPreview() {
        $('#prevNama').text($('#namaToko').val() || 'NAMA TOKO');
        $('#prevAlamat').text($('#alamatToko').val());
        var telp = $('#noTelp').val();
        $('#prevTelp').text(telp).toggle(telp.trim() !== '');
        var footer = $('#footerNota').val();
        $('#prevFooter').text(footer).toggle(footer.trim() !== '');
        // nama kasir di pratinjau ikut toggle
        $('#prevKasir').toggle($('#tampilkanKasir').is(':checked'));
    }
    $(document).ready(function () {
        refreshPreview();
        $('#namaToko, #alamatToko, #noTelp, #footerNota').on('input', refreshPreview);
        $('#tampilkanKasir').on('change', refreshPreview);
    });

    // LOGO: pilih file -> pratinjau
    $('#logoFile').on('change', function () {
        var file = this.files[0];
        if (!file) return;
        if (file.size > 2 * 1024 * 1024) { toastError("Ukuran logo maksimal 2 MB!"); this.value = ''; return; }
        $('#hapusLogo').val('0');
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#logoPreview').attr('src', e.target.result).show();
            $('#prevLogo').attr('src', e.target.result).show();
            $('#btnHapusLogo').removeClass('d-none');
        };
        reader.readAsDataURL(file);
    });

    // LOGO: hapus
    $('#btnHapusLogo').on('click', function () {
        $('#logoFile').val('');
        $('#hapusLogo').val('1');
        $('#logoPreview').attr('src', '').hide();
        $('#prevLogo').attr('src', '').hide();
        $(this).addClass('d-none');
    });

    // SIMPAN
    $(document).on('click', '#btnSimpanPengaturan', function () {
        var nama_toko = $('#namaToko').val();
        if (nama_toko.trim() === '') {
            toastError("Nama toko tidak boleh kosong!");
            return;
        }

        var btnDef = $('#btnSimpanPengaturan').html();
        $('#btnSimpanPengaturan').html('Menyimpan ..').attr('disabled', true);
        animasiProgressBar_run();

        var fd = new FormData();
        fd.append('_token', "{{ csrf_token() }}");
        fd.append('nama_toko', nama_toko);
        fd.append('alamat_toko', $('#alamatToko').val());
        fd.append('no_telp', $('#noTelp').val());
        fd.append('footer_nota', $('#footerNota').val());
        fd.append('tampilkan_kasir', $('#tampilkanKasir').is(':checked') ? '1' : '0');
        fd.append('hapus_logo', $('#hapusLogo').val());
        if ($('#logoFile')[0].files[0]) {
            fd.append('logo', $('#logoFile')[0].files[0]);
        }

        $.ajax({
            type: "POST",
            url: "{{ route('update_pengaturan') }}",
            data: fd,
            processData: false,
            contentType: false,
            dataType: "JSON",
            success: function (response) {
                if (response.kode == 200) {
                    toastSuccess(response.pesan);
                    $('#logoFile').val('');
                    $('#hapusLogo').val('0');
                } else if (response.kode == 422) {
                    errorHandlerToast(response.pesan);
                } else {
                    toastError(response.pesan);
                }
            },
            error: function () {
                toastError("Oops! Silahkan coba lagi!");
            },
            complete: function () {
                $('#btnSimpanPengaturan').html(btnDef).attr('disabled', false);
                animasiProgressBar_stop();
            }
        });
    });
</script>

@endsection

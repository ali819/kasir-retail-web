@extends('layouts.main')
@section('content')
@section('title', 'Kelola Pelanggan')

<style>
    .table-responsive { padding-bottom: 8px; }
</style>

{{-- MAIN --}}
<div class="container-fluid">
    <div class="card bg-light-info shadow-none position-relative overflow-hidden">
        <div class="card-body px-4 py-3">
            <div class="row align-items-center">
                <div class="col-9">
                    <h4 class="fw-semibold mb-8">Kelola Pelanggan</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a class="text-muted" href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item" aria-current="page">Kelola Pelanggan</li>
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
                <div class="font-medium">
                    <i class="ti ti-info-circle fs-5 me-2 flex-shrink-0"></i>
                    Data pelanggan dipakai di layar <b>&nbsp;Kasir&nbsp;</b> (dropdown pencarian) agar nama pembeli konsisten &amp; bisa direkap.
                </div>
            </div>
        </div>
    </div>

    <div class="w-100 position-relative overflow-hidden">
        <div class="py-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-9 col-12">
                    <label for="cariPelanggan" style="margin-bottom:5px;"><b>Cari Pelanggan</b></label>
                    <input type="search" class="form-control" id="cariPelanggan" placeholder="Ketik nama / no. telp..">
                </div>
                <div class="col-md-3 col-12">
                    <button type="button" class="btn btn-primary col-12 btnTambahPelanggan" style="font-weight:bold"><i class="ti ti-user-plus"></i>&nbsp; Tambah Pelanggan</button>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle text-nowrap mb-0" id="tabel_pelanggan" style="width:100%;">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>No. Telp</th>
                            <th>Alamat</th>
                            <th class="text-center">Transaksi</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH/EDIT --}}
<div class="modal fade" id="ModalPelanggan" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h4 class="modal-title" id="titlePelanggan">Tambah Pelanggan</h4></div>
            <form id="FormPelanggan" autocomplete="off">
                @csrf
                <input type="hidden" name="id" id="pelangganId">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="mb-1"><b>Nama</b> <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama" id="pelangganNama" placeholder="Nama pelanggan .." required>
                        </div>
                        <div class="col-md-5">
                            <label class="mb-1"><b>No. Telp</b></label>
                            <input type="text" class="form-control only-number" name="no_telp" id="pelangganTelp" placeholder="08xx.." inputmode="numeric" autocomplete="off">
                        </div>
                        <div class="col-12">
                            <label class="mb-1"><b>Alamat</b></label>
                            <input type="text" class="form-control" name="alamat" id="pelangganAlamat" placeholder="Alamat ..">
                        </div>
                        <div class="col-12">
                            <label class="mb-1"><b>Keterangan</b></label>
                            <input type="text" class="form-control" name="keterangan" id="pelangganKeterangan" placeholder="Opsional ..">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal&emsp;<i class="ti ti-x"></i></button>
                    <button type="submit" class="btn btn-primary btnSimpanPelanggan"><i class="ti ti-check"></i>&emsp;Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function escapeHtml(t){ if(t==null) return ''; return String(t).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;'); }

    // input telepon: hanya angka
    $(document).on('input', '.only-number', function () { this.value = this.value.replace(/[^0-9]/g, ''); });

    var tabel_pelanggan;
    $(document).ready(function () {
        tabel_pelanggan = $("#tabel_pelanggan").DataTable({
            dom: 'lrtip',
            order: [[1, 'ASC']],
            autoWidth: false,
            language: {
                "sProcessing":"Sedang memproses...","sLengthMenu":"Tampilkan _MENU_ entri","sZeroRecords":"Tidak ditemukan data yang sesuai",
                "loadingRecords":"Memuat data, mohon tunggu ...","sInfo":"Total : <b>_TOTAL_</b> entri","sInfoEmpty":"Menampilkan : <b>0</b> entri",
                "sInfoFiltered":"","sSearch":"Cari : ",
                "oPaginate":{"sFirst":"Pertama","sPrevious":"<i class='ti ti-chevron-left'></i>","sNext":"<i class='ti ti-chevron-right'></i>","sLast":"Terakhir"}
            },
            serverSide: true,
            ajax: { url: "{{ route('kelola_pelanggan_tabel') }}", type: 'GET' },
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                {
                    data: 'nama',
                    render: function (data) {
                        return `<div class="d-flex align-items-center gap-2">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light-primary text-primary" style="width:38px;height:38px;"><i class="ti ti-user fs-5"></i></span>
                            <h6 class="fw-semibold mb-0 fs-4">${escapeHtml(data)}</h6></div>`;
                    }
                },
                { data: 'no_telp', render: function (d) { return d ? escapeHtml(d) : '<span class="text-muted">-</span>'; } },
                { data: 'alamat', render: function (d) { return d ? escapeHtml(d) : '<span class="text-muted">-</span>'; } },
                { data: 'total_transaksi', className: 'text-center', orderable: false, searchable: false,
                  render: function (d) { return `<span class="badge bg-light-primary text-primary">${d}x</span>`; } },
                {
                    data: 'id', orderable: false, searchable: false, className: 'text-center',
                    render: function (data) {
                        return `
                        <button type="button" class="btnEditPelanggan btn btn-primary btn-circle btn-lg d-inline-flex align-items-center justify-content-center" title="Ubah"><i class="ti ti-pencil"></i></button>
                        <button type="button" class="btnHapusPelanggan btn btn-danger btn-circle btn-lg d-inline-flex align-items-center justify-content-center" data-id="${data}" title="Hapus"><i class="ti ti-trash"></i></button>`;
                    }
                }
            ],
            drawCallback: function () { animasiProgressBar_stop(); }
        });

        let cariTimer = null;
        $('#cariPelanggan').on('input', function () {
            clearTimeout(cariTimer);
            cariTimer = setTimeout(function () { animasiProgressBar_run(); tabel_pelanggan.search($('#cariPelanggan').val()).draw(); }, 400);
        });
    });

    // TAMBAH
    $(document).on('click', '.btnTambahPelanggan', function () {
        $('#FormPelanggan')[0].reset();
        $('#pelangganId').val('');
        $('#titlePelanggan').text('Tambah Pelanggan');
        $('#ModalPelanggan').modal('show');
    });

    // EDIT
    $(document).on('click', '.btnEditPelanggan', function () {
        var row = tabel_pelanggan.row($(this).parents('tr')).data();
        if (!row) { toastError("Data tidak ditemukan, muat ulang!"); return; }
        $('#pelangganId').val(row.id);
        $('#titlePelanggan').text('Ubah: ' + row.nama);
        $('#pelangganNama').val(row.nama);
        $('#pelangganTelp').val(row.no_telp || '');
        $('#pelangganAlamat').val(row.alamat || '');
        $('#pelangganKeterangan').val(row.keterangan || '');
        $('#ModalPelanggan').modal('show');
    });

    // SIMPAN (tambah/edit)
    $('#FormPelanggan').on('submit', function (e) {
        e.preventDefault();
        var isEdit = $('#pelangganId').val() !== '';
        var url = isEdit ? "{{ route('kelola_pelanggan_update') }}" : "{{ route('kelola_pelanggan_tambah') }}";
        var btn = $('.btnSimpanPelanggan').html();
        $('.btnSimpanPelanggan').html('Memproses ..').attr('disabled', true);
        $.ajax({
            type: "POST", url: url, data: $(this).serialize(), dataType: "JSON",
            success: function (r) {
                if (r.kode == 200) { $('#ModalPelanggan').modal('hide'); tabel_pelanggan.draw(); toastSuccess(r.pesan); }
                else if (r.kode == 422) { errorHandlerToast(r.pesan); }
                else { toastError(r.pesan); }
            },
            error: function () { toastError("Oops! Silahkan coba lagi!"); },
            complete: function () { $('.btnSimpanPelanggan').html(btn).attr('disabled', false); }
        });
    });

    // HAPUS
    $(document).on('click', '.btnHapusPelanggan', function () {
        var id = $(this).data('id');
        customConfirm("Hapus pelanggan ?", "Transaksi lama tetap aman (nama tersimpan), hanya relasi dilepas.").then((confirmed) => {
            if (!confirmed) return;
            $.ajax({
                type: "DELETE", url: "{{ route('kelola_pelanggan_hapus') }}",
                data: { _token: "{{ csrf_token() }}", id: id }, dataType: "JSON",
                success: function (r) {
                    if (r.kode == 200) { tabel_pelanggan.draw(); toastSuccess(r.pesan); }
                    else { toastError(r.pesan); }
                },
                error: function () { toastError("Oops! Silahkan coba lagi!"); }
            });
        });
    });
</script>

@endsection

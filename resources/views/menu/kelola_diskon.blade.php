@extends('layouts.main')
@section('content')
@section('title', 'Kelola Diskon')

<style>
    .table-responsive { padding-bottom: 8px; }
</style>

{{-- MAIN --}}
<div class="container-fluid">
    <div class="card bg-light-info shadow-none position-relative overflow-hidden">
        <div class="card-body px-4 py-3">
            <div class="row align-items-center">
                <div class="col-9">
                    <h4 class="fw-semibold mb-8">Kelola Diskon</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a class="text-muted" href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item" aria-current="page">Kelola Diskon</li>
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
                    Diskon <b>&nbsp;aktif&nbsp;</b> akan muncul sebagai tombol di layar <b>&nbsp;Kasir&nbsp;</b> saat pembayaran. Tipe <b>&nbsp;Persen&nbsp;</b> (mis. 10%) atau <b>&nbsp;Nominal&nbsp;</b> (mis. Rp 5.000).
                </div>
            </div>
        </div>
    </div>

    <div class="w-100 position-relative overflow-hidden">
        <div class="py-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-9 col-12">
                    <label for="cariDiskon" style="margin-bottom:5px;"><b>Cari Diskon</b></label>
                    <input type="search" class="form-control" id="cariDiskon" placeholder="Ketik nama diskon..">
                </div>
                <div class="col-md-3 col-12">
                    <button type="button" class="btn btn-primary col-12 btnTambahDiskon" style="font-weight:bold"><i class="ti ti-plus"></i>&nbsp; Tambah Diskon</button>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-nowrap mb-0" id="tabel_diskon" style="width:100%;">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Diskon</th>
                            <th>Tipe</th>
                            <th>Nilai</th>
                            <th class="text-center">Status</th>
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
<div class="modal fade" id="ModalDiskon" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h4 class="modal-title" id="titleDiskon">Tambah Diskon</h4></div>
            <form id="FormDiskon" autocomplete="off">
                @csrf
                <input type="hidden" name="id" id="diskonId">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="mb-1"><b>Nama Diskon</b> <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama" id="diskonNama" placeholder="mis. Diskon Lebaran" required>
                        </div>
                        <div class="col-md-6">
                            <label class="mb-1"><b>Tipe</b></label>
                            <select class="form-control" name="tipe" id="diskonTipe">
                                <option value="persen">Persen (%)</option>
                                <option value="fixed">Nominal (Rp)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="mb-1"><b>Nilai</b> <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text" id="diskonPrefix">%</span>
                                <input type="number" class="form-control" name="nilai" id="diskonNilai" min="1" placeholder="10" required>
                            </div>
                            <small class="text-muted" id="diskonHint">Persen: 1-100</small>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" name="aktif" id="diskonAktif" value="1" checked>
                                <label class="form-check-label" for="diskonAktif">Aktif (tampil di kasir)</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal&emsp;<i class="ti ti-x"></i></button>
                    <button type="submit" class="btn btn-primary btnSimpanDiskon"><i class="ti ti-check"></i>&emsp;Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function escapeHtml(t){ if(t==null) return ''; return String(t).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;'); }
    function formatRupiah(a){ a = Math.floor(a || 0); return 'Rp ' + a.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'); }

    function nilaiTampil(row) {
        return row.tipe === 'persen' ? (row.nilai + '%') : formatRupiah(row.nilai);
    }
    // sinkron prefix & hint sesuai tipe
    function syncTipe() {
        var t = $('#diskonTipe').val();
        $('#diskonPrefix').text(t === 'persen' ? '%' : 'Rp');
        $('#diskonHint').text(t === 'persen' ? 'Persen: 1-100' : 'Nominal rupiah, mis. 5000');
        $('#diskonNilai').attr('max', t === 'persen' ? 100 : '');
    }
    $(document).on('change', '#diskonTipe', syncTipe);

    var tabel_diskon;
    $(document).ready(function () {
        tabel_diskon = $("#tabel_diskon").DataTable({
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
            ajax: { url: "{{ route('kelola_diskon_tabel') }}", type: 'GET' },
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nama', render: function (d) { return `<h6 class="fw-semibold mb-0 fs-4">${escapeHtml(d)}</h6>`; } },
                { data: 'tipe', searchable: false, render: function (d) { return d === 'persen' ? '<span class="badge bg-light-info text-info">Persen</span>' : '<span class="badge bg-light-warning text-warning">Nominal</span>'; } },
                { data: 'nilai', searchable: false, render: function (d, t, row) { return `<b>${nilaiTampil(row)}</b>`; } },
                { data: 'aktif', searchable: false, className: 'text-center', render: function (d) { return d ? '<span class="badge bg-light-success text-success">Aktif</span>' : '<span class="badge bg-light-danger text-danger">Nonaktif</span>'; } },
                {
                    data: 'id', orderable: false, searchable: false, className: 'text-center',
                    render: function (data) {
                        return `
                        <button type="button" class="btnEditDiskon btn btn-primary btn-circle btn-lg d-inline-flex align-items-center justify-content-center" title="Ubah"><i class="ti ti-pencil"></i></button>
                        <button type="button" class="btnHapusDiskon btn btn-danger btn-circle btn-lg d-inline-flex align-items-center justify-content-center" data-id="${data}" title="Hapus"><i class="ti ti-trash"></i></button>`;
                    }
                }
            ],
            drawCallback: function () { animasiProgressBar_stop(); }
        });

        let cariTimer = null;
        $('#cariDiskon').on('input', function () {
            clearTimeout(cariTimer);
            cariTimer = setTimeout(function () { animasiProgressBar_run(); tabel_diskon.search($('#cariDiskon').val()).draw(); }, 400);
        });
    });

    // TAMBAH
    $(document).on('click', '.btnTambahDiskon', function () {
        $('#FormDiskon')[0].reset();
        $('#diskonId').val('');
        $('#diskonTipe').val('persen'); syncTipe();
        $('#diskonAktif').prop('checked', true);
        $('#titleDiskon').text('Tambah Diskon');
        $('#ModalDiskon').modal('show');
    });

    // EDIT
    $(document).on('click', '.btnEditDiskon', function () {
        var row = tabel_diskon.row($(this).parents('tr')).data();
        if (!row) { toastError("Data tidak ditemukan, muat ulang!"); return; }
        $('#diskonId').val(row.id);
        $('#titleDiskon').text('Ubah: ' + row.nama);
        $('#diskonNama').val(row.nama);
        $('#diskonTipe').val(row.tipe); syncTipe();
        $('#diskonNilai').val(row.nilai);
        $('#diskonAktif').prop('checked', !!row.aktif);
        $('#ModalDiskon').modal('show');
    });

    // SIMPAN
    $('#FormDiskon').on('submit', function (e) {
        e.preventDefault();
        var isEdit = $('#diskonId').val() !== '';
        var url = isEdit ? "{{ route('kelola_diskon_update') }}" : "{{ route('kelola_diskon_tambah') }}";
        // pastikan aktif terkirim 0/1
        var data = $(this).serializeArray();
        if (!$('#diskonAktif').is(':checked')) data.push({ name: 'aktif', value: '0' });
        var btn = $('.btnSimpanDiskon').html();
        $('.btnSimpanDiskon').html('Memproses ..').attr('disabled', true);
        $.ajax({
            type: "POST", url: url, data: $.param(data), dataType: "JSON",
            success: function (r) {
                if (r.kode == 200) { $('#ModalDiskon').modal('hide'); tabel_diskon.draw(); toastSuccess(r.pesan); }
                else if (r.kode == 422) { errorHandlerToast(r.pesan); }
                else { toastError(r.pesan); }
            },
            error: function () { toastError("Oops! Silahkan coba lagi!"); },
            complete: function () { $('.btnSimpanDiskon').html(btn).attr('disabled', false); }
        });
    });

    // HAPUS
    $(document).on('click', '.btnHapusDiskon', function () {
        var id = $(this).data('id');
        customConfirm("Hapus diskon ?", "Diskon tidak lagi muncul di kasir.").then((confirmed) => {
            if (!confirmed) return;
            $.ajax({
                type: "DELETE", url: "{{ route('kelola_diskon_hapus') }}",
                data: { _token: "{{ csrf_token() }}", id: id }, dataType: "JSON",
                success: function (r) {
                    if (r.kode == 200) { tabel_diskon.draw(); toastSuccess(r.pesan); }
                    else { toastError(r.pesan); }
                },
                error: function () { toastError("Oops! Silahkan coba lagi!"); }
            });
        });
    });
</script>

@endsection

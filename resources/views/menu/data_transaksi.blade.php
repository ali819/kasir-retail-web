@extends('layouts.main')
@section('content')
@section('title', 'Data Transaksi')

<style>
    /* serap ~5px overflow semu dari DataTables (overflow-x:auto memaksa overflow-y jadi auto) */
    .table-responsive { padding-bottom: 8px; }
</style>

{{-- MAIN --}}
<div class="container-fluid">
    <div class="card bg-light-info shadow-none position-relative overflow-hidden">
        <div class="card-body px-4 py-3">
            <div class="row align-items-center">
                <div class="col-9">
                    <h4 class="fw-semibold mb-8">Data Transaksi</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a class="text-muted" href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item" aria-current="page">Data Transaksi</li>
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
                    Riwayat semua transaksi penjualan. Klik <i class="ti ti-eye"></i> untuk detail, <i class="ti ti-receipt"></i> untuk cetak ulang nota.
                </div>
            </div>
        </div>
    </div>

    {{-- ====== KARTU TAB ====== --}}
    <div class="card overflow-hidden">
        <div class="card-body border-bottom py-3">
            <ul class="nav nav-pills gap-2" id="tabDataTransaksi" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active fw-semibold px-4" data-bs-toggle="tab" data-bs-target="#tabTransaksi" type="button" role="tab">
                        <i class="ti ti-list-details me-1"></i> Daftar Transaksi
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-semibold px-4" data-bs-toggle="tab" data-bs-target="#tabStatistik" type="button" role="tab">
                        <i class="ti ti-users-group me-1"></i> Statistik Pelanggan
                    </button>
                </li>
            </ul>
        </div>

        <div class="tab-content">
            {{-- ====== TAB 1: DAFTAR TRANSAKSI ====== --}}
            <div class="tab-pane fade show active" id="tabTransaksi" role="tabpanel">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                        <h5 class="fw-semibold mb-0"><i class="ti ti-search text-primary"></i>&nbsp; Pencarian Transaksi</h5>
                        <button type="button" class="btn btn-outline-danger btnHapusTransaksiDipilih"><i class="ti ti-trash"></i>&nbsp; Hapus Dipilih</button>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-3 col-6">
                            <label class="fs-2 mb-1 text-muted">ID Transaksi</label>
                            <input type="text" class="form-control" id="cariIdTransaksi" placeholder="TRX-.." autocomplete="off">
                        </div>
                        <div class="col-md-3 col-6">
                            <label class="fs-2 mb-1 text-muted">Tanggal</label>
                            <input type="date" class="form-control" id="cariTanggalBeli" autocomplete="off">
                        </div>
                        <div class="col-md-3 col-6">
                            <label class="fs-2 mb-1 text-muted">Pembeli</label>
                            <input type="text" class="form-control" id="cariPembeli" placeholder="Nama pembeli .." autocomplete="off">
                        </div>
                        <div class="col-md-3 col-6">
                            <label class="fs-2 mb-1 text-muted">Total Belanja</label>
                            <input type="number" class="form-control" id="cariTotalBelanja" placeholder="Nominal .." autocomplete="off">
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle text-nowrap mb-0" id="tabel_data_transaksi" style="width:100%;">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:34px;"><input type="checkbox" class="form-check-input" id="cbHeaderDataPembelian"></th>
                                    <th>ID Transaksi</th>
                                    <th>Tanggal</th>
                                    <th>Pembeli</th>
                                    <th>Kasir</th>
                                    <th class="text-end">Total Belanja</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ====== TAB 2: STATISTIK PELANGGAN ====== --}}
            <div class="tab-pane fade" id="tabStatistik" role="tabpanel">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                        <h5 class="fw-semibold mb-0"><i class="ti ti-chart-histogram text-primary"></i>&nbsp; Statistik Pembelian Pelanggan</h5>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6 col-12">
                            <label class="fs-2 mb-1 text-muted">Cari Pelanggan</label>
                            <input type="search" class="form-control" id="cariStatistikPelanggan" placeholder="Cari nama / no. telp..">
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle text-nowrap mb-0" id="tabel_statistik_pelanggan" style="width:100%;">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Pelanggan</th>
                                    <th class="text-center">Transaksi</th>
                                    <th class="text-center">Barang Dibeli</th>
                                    <th class="text-end">Total Belanja</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL STATISTIK PELANGGAN --}}
<div class="modal fade" id="ModalStatistikPelanggan" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h4 class="modal-title mb-0"><i class="ti ti-user"></i>&nbsp; <span id="statNama">-</span></h4>
                    <small class="text-muted"><i class="ti ti-phone"></i> <span id="statTelp">-</span></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="statPelangganId">
                {{-- filter rentang tanggal (perhitungan via AJAX) --}}
                <div class="border rounded p-3 mb-3">
                    <label class="fs-2 mb-1"><b>Rentang Tanggal</b></label>
                    <div class="row g-2 align-items-end">
                        <div class="col-md-5 col-6">
                            <label class="fs-2 mb-1 text-muted">Dari</label>
                            <input type="date" class="form-control" id="statMulai">
                        </div>
                        <div class="col-md-5 col-6">
                            <label class="fs-2 mb-1 text-muted">Sampai</label>
                            <input type="date" class="form-control" id="statSelesai">
                        </div>
                        <div class="col-md-2 col-12">
                            <button type="button" class="btn btn-info w-100 fw-semibold" id="btnTerapkanStat"><i class="ti ti-filter"></i> Terapkan</button>
                        </div>
                    </div>
                    <small class="text-muted">Ringkasan &amp; daftar mengikuti rentang ini (default: bulan berjalan).</small>
                </div>
                {{-- ringkasan --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <div class="p-3 rounded bg-light-primary">
                            <p class="fs-3 mb-1 text-primary"><span class="round-8 bg-primary rounded-circle d-inline-block me-1"></span> Total Transaksi</p>
                            <h3 class="mb-0 fw-bold text-primary" id="statTotalTransaksi">0</h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded bg-light-info">
                            <p class="fs-3 mb-1 text-info"><span class="round-8 bg-info rounded-circle d-inline-block me-1"></span> Barang Dibeli</p>
                            <h3 class="mb-0 fw-bold text-info" id="statTotalBarang">0</h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded bg-light-danger">
                            <p class="fs-3 mb-1 text-danger"><span class="round-8 bg-danger rounded-circle d-inline-block me-1"></span> Total Belanja</p>
                            <h3 class="mb-0 fw-bold text-danger" id="statTotalBelanja">Rp 0</h3>
                        </div>
                    </div>
                </div>

                <h6 class="fw-semibold mb-2"><i class="ti ti-receipt-2"></i>&nbsp; Daftar Transaksi <span class="text-muted fw-normal fs-2">(10 terbaru)</span></h6>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:30px;">#</th>
                                <th>ID Transaksi</th>
                                <th>Tanggal</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Total</th>
                                <th class="text-center">Nota</th>
                            </tr>
                        </thead>
                        <tbody id="statListTransaksi"></tbody>
                    </table>
                </div>
                <div class="text-center mt-3">
                    <small class="text-muted d-block mb-2" id="statInfo"></small>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="btnMuatLebihStat" style="display:none;"><i class="ti ti-chevron-down"></i>&nbsp; Muat lebih</button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup&emsp;<i class="ti ti-x"></i></button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL DETAIL --}}
<div class="modal fade" id="ModalDataPembelian" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h4 class="modal-title mb-0">Nota <span id="pembelianIdTransaksi">-</span></h4>
                    <small class="text-muted"><i class="ti ti-calendar-event"></i> <span class="detailTanggalPembelian">-</span></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
                    <span class="badge bg-light-primary text-primary fs-2"><i class="ti ti-user"></i>&nbsp; Pembeli: <span class="detailPembeli">-</span></span>
                    <span class="badge bg-light-success text-success fs-2"><i class="ti ti-cash-register"></i>&nbsp; Kasir: <span class="detailKasir">-</span></span>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:30px;">#</th>
                                <th>Barang</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Harga</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="formDetailListPembelian"></tbody>
                    </table>
                </div>

                <div class="border rounded p-3 mt-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Total Belanja</span>
                        <b class="detailTotalBelanja">Rp 0</b>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Total Bayar</span>
                        <b class="detailTotalBayar">Rp 0</b>
                    </div>
                    <div class="d-flex justify-content-between text-success">
                        <span>Kembalian</span>
                        <b class="detailKembalian">Rp 0</b>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup&emsp;<i class="ti ti-x"></i></button>
                <button type="button" class="btn btn-info btnCetakDariModal btn-print"><i class="ti ti-receipt"></i>&emsp;Cetak Nota</button>
            </div>
        </div>
    </div>
</div>

<script>
    function escapeHtml(t){ if(t==null) return ''; return String(t).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;'); }
    function trimText(t){ t = t||''; return t.length > 22 ? t.substring(0,22)+'..' : t; }
    function formatRupiah(angka){ angka = Math.floor(angka || 0); return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'); }
    function animasiProgressBar_run(){ $('.animasiProgressBar').css('width','100%'); }
    function animasiProgressBar_stop(){ $('.animasiProgressBar').css('width','0%'); }

    var modalIdTransaksi = null;

    $(document).ready(function () {
        var tabel = $("#tabel_data_transaksi").DataTable({
            dom: 'lrtip',
            order: [[2, 'DESC']],
            lengthMenu: [[10, 25, 50, 100, 500], [10, 25, 50, 100, 500]],
            autoWidth: false,
            language: {
                "sProcessing":"Sedang memproses...","sLengthMenu":"Tampilkan _MENU_ entri","sZeroRecords":"Tidak ditemukan data yang sesuai",
                "loadingRecords":"Memuat data, mohon tunggu ...","sInfo":"Total : <b>_TOTAL_</b> entri","sInfoEmpty":"Menampilkan : <b>0</b> entri",
                "sInfoFiltered":"","sSearch":"Cari : ",
                "oPaginate":{"sFirst":"Pertama","sPrevious":"<i class='ti ti-chevron-left'></i>","sNext":"<i class='ti ti-chevron-right'></i>","sLast":"Terakhir"}
            },
            serverSide: true,
            ajax: { url: "{{ route('tabel_data_transaksi') }}", type: 'GET' },
            columns: [
                {
                    data: 'id', orderable: false, searchable: false,
                    render: function (data, type, row) {
                        return `<input type="checkbox" class="form-check-input dataIdPembelianBarang" data-id-transaksi="${escapeHtml(row.id_transaksi)}">`;
                    }
                },
                {
                    data: 'id_transaksi',
                    render: function (data) { return `<span class="fw-semibold fs-4">${escapeHtml(data)}</span>`; }
                },
                {
                    data: 'created_at',
                    render: function (data, type, row) { return `<span class="text-muted">${escapeHtml(row.formatted_date)}</span>`; }
                },
                {
                    data: 'pembeli',
                    render: function (data) {
                        return data ? escapeHtml(data) : '<span class="text-muted">-</span>';
                    }
                },
                {
                    data: 'nama_kasir', orderable: false, searchable: false,
                    render: function (data) {
                        return data ? `<span class="badge bg-light-primary text-primary"><i class="ti ti-user"></i> ${escapeHtml(data)}</span>` : '<span class="text-muted">-</span>';
                    }
                },
                {
                    data: 'total_belanja', className: 'text-end',
                    render: function (data) { return `<b class="text-primary">${formatRupiah(data)}</b>`; }
                },
                {
                    data: 'id', orderable: false, searchable: false, className: 'text-center',
                    render: function (data, type, row) {
                        return `
                        <button type="button" class="btnEditDataPembelian btn btn-primary btn-circle btn-lg d-inline-flex align-items-center justify-content-center" data-pembeli="${escapeHtml(row.pembeli||'')}" data-id-transaksi="${escapeHtml(row.id_transaksi)}"><i class="ti ti-eye"></i></button>
                        <button type="button" class="btnPrintDataPembelian btn-print btn btn-info btn-circle btn-lg d-inline-flex align-items-center justify-content-center" data-id-transaksi="${escapeHtml(row.id_transaksi)}"><i class="ti ti-receipt"></i></button>`;
                    }
                }
            ],
            drawCallback: function () { animasiProgressBar_stop(); }
        });

        // PENCARIAN
        let t1=null,t2=null,t3=null;
        $('#cariIdTransaksi').on('input', function () {
            $(this).val($(this).val().toUpperCase());
            clearTimeout(t1); t1 = setTimeout(function () { animasiProgressBar_run(); tabel.column(1).search($('#cariIdTransaksi').val()).draw(); }, 500);
        });
        $('#cariTanggalBeli').on('change', function () { animasiProgressBar_run(); tabel.column(2).search($('#cariTanggalBeli').val()).draw(); });
        $('#cariPembeli').on('input', function () { clearTimeout(t2); t2 = setTimeout(function () { animasiProgressBar_run(); tabel.column(3).search($('#cariPembeli').val()).draw(); }, 500); });
        $('#cariTotalBelanja').on('input', function () { clearTimeout(t3); t3 = setTimeout(function () { animasiProgressBar_run(); tabel.column(5).search($('#cariTotalBelanja').val()).draw(); }, 500); });

        // klik baris untuk toggle checkbox
        $('#tabel_data_transaksi tbody').on('click', 'tr', function (e) {
            if ($(e.target).is('button, input, i') || $(e.target).closest('button').length) return;
            var cb = $(this).find('.dataIdPembelianBarang');
            cb.prop('checked', !cb.prop('checked'));
        });
        // header select-all
        $('#cbHeaderDataPembelian').on('click', function () {
            var checked = $(this).prop('checked');
            $('.dataIdPembelianBarang').prop('checked', checked);
        });
    });

    // ====== DETAIL ======
    $(document).on('click', '.btnEditDataPembelian', function () {
        var id_transaksi = $(this).attr('data-id-transaksi');
        var pembeli = $(this).attr('data-pembeli');
        if (!pembeli || pembeli === 'null') pembeli = '-';
        modalIdTransaksi = id_transaksi;

        $('.btnEditDataPembelian').attr('disabled', true);
        animasiProgressBar_run();

        $.ajax({
            type: "GET", url: "{{ route('detail_data_transaksi') }}", data: { id_transaksi: id_transaksi }, dataType: "JSON",
            success: function (response) {
                if (response.kode == 404) { toastError(response.pesan); return; }

                $('#pembelianIdTransaksi').text(id_transaksi);
                $('.detailTanggalPembelian').text(response.tanggal);
                $('.detailPembeli').text(pembeli);
                $('.detailKasir').text(response.kasir || '-');
                $('.detailTotalBelanja').text(formatRupiah(response.total_belanja));
                $('.detailTotalBayar').text(formatRupiah(response.total_bayar));
                $('.detailKembalian').text(formatRupiah(response.kembalian));

                var rows = '';
                $.each(response.list, function (i, d) {
                    rows += `
                        <tr>
                            <td>${i + 1}</td>
                            <td>${escapeHtml(d.nama_barang)} <span class="badge bg-light-primary text-primary">${escapeHtml(d.satuan)}</span></td>
                            <td class="text-center">${d.qty}</td>
                            <td class="text-end">${formatRupiah(d.harga)}</td>
                            <td class="text-end fw-semibold">${formatRupiah(d.total_harga)}</td>
                        </tr>`;
                });
                $('#formDetailListPembelian').html(rows);

                $('#ModalDataPembelian').modal('show');
            },
            error: function () { toastError("Oops! Terjadi kesalahan. Coba lagi!"); },
            complete: function () { animasiProgressBar_stop(); $('.btnEditDataPembelian').attr('disabled', false); }
        });
    });

    // cetak dari modal
    $(document).on('click', '.btnCetakDariModal', function () {
        if (modalIdTransaksi) cetakNota(modalIdTransaksi, $(this));
    });

    // ====== HAPUS DIPILIH ======
    $(document).on('click', '.btnHapusTransaksiDipilih', function () {
        var btnDef = $(this).html();
        var selectedIds = [];
        $('.dataIdPembelianBarang:checked').each(function () { selectedIds.push($(this).data('id-transaksi')); });
        if (selectedIds.length <= 0) { toastError("Tidak ada data dipilih!"); return; }

        customConfirm("Hapus (" + selectedIds.length + ") data ?", "Data yang dihapus tidak bisa dikembalikan.").then((confirmed) => {
            if (!confirmed) return;
            $('.btnHapusTransaksiDipilih').attr('disabled', true).html('Memproses ..');
            $.ajax({
                type: "DELETE", url: "{{ route('hapus_data_transaksi') }}",
                data: { _token: "{{ csrf_token() }}", list_id_transaksi: selectedIds }, dataType: "JSON",
                success: function (response) { toastSuccess(response.pesan); $("#tabel_data_transaksi").DataTable().draw(); },
                error: function () { toastError("Oops! Silahkan coba lagi!"); },
                complete: function () { $('.btnHapusTransaksiDipilih').attr('disabled', false).html(btnDef); }
            });
        });
    });

    // ====== CETAK NOTA ======
    $(document).on('click', '.btnPrintDataPembelian', function () {
        cetakNota($(this).attr('data-id-transaksi'), $(this));
    });
    function cetakNota(id_transaksi, $btn) {
        if (!id_transaksi) return toastError("ID transaksi kosong. Silahkan coba lagi!");
        if (typeof printReady === 'function' && !printReady()) {
            toastError('Printer belum terhubung — buka Pengaturan Printer.');
            $('#ModalPengaturanPrinter').modal('show');
            return;
        }
        setBtnLoading($btn, true);
        animasiProgressBar_run();
        $.ajax({
            type: "GET", url: "{{ route('nota_pembelian_html') }}", data: { id_transaksi: id_transaksi }, dataType: "JSON",
            success: function (response) {
                if (response.kode == 404) { setBtnLoading($btn, false); return toastError(response.pesan); }
                // qzPrintNota pakai $btn yang sama & mengembalikannya setelah selesai cetak
                qzPrintNota(response.raw_data, $btn);
            },
            error: function () { setBtnLoading($btn, false); toastError("Oops! Silahkan coba lagi!"); },
            complete: function () { animasiProgressBar_stop(); }
        });
    }
</script>

<script>
    // ================= TAB STATISTIK PELANGGAN =================
    var tabelStatistik = null;
    var dtLang = {
        "sProcessing":"Sedang memproses...","sLengthMenu":"Tampilkan _MENU_ entri","sZeroRecords":"Tidak ditemukan data yang sesuai",
        "loadingRecords":"Memuat data, mohon tunggu ...","sInfo":"Total : <b>_TOTAL_</b> entri","sInfoEmpty":"Menampilkan : <b>0</b> entri",
        "sInfoFiltered":"","sSearch":"Cari : ",
        "oPaginate":{"sFirst":"Pertama","sPrevious":"<i class='ti ti-chevron-left'></i>","sNext":"<i class='ti ti-chevron-right'></i>","sLast":"Terakhir"}
    };

    $('button[data-bs-target="#tabStatistik"]').on('shown.bs.tab', function () {
        if (tabelStatistik) { tabelStatistik.columns.adjust(); return; }
        tabelStatistik = $('#tabel_statistik_pelanggan').DataTable({
            dom: 'lrtip',
            order: [[4, 'desc']],
            autoWidth: false,
            language: dtLang,
            serverSide: true,
            ajax: { url: "{{ route('statistik_pelanggan_tabel') }}", type: 'GET' },
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                {
                    data: 'nama',
                    render: function (d, t, row) {
                        return `<div class="d-flex align-items-center gap-2">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light-primary text-primary" style="width:38px;height:38px;"><i class="ti ti-user fs-5"></i></span>
                            <div><h6 class="fw-semibold mb-0 fs-4">${escapeHtml(d)}</h6>
                            <span class="text-muted fs-2">${row.no_telp ? escapeHtml(row.no_telp) : '-'}</span></div></div>`;
                    }
                },
                { data: 'jumlah_transaksi', className: 'text-center', searchable: false, render: function (d) { return `<span class="badge bg-light-primary text-primary">${d}x</span>`; } },
                { data: 'total_barang', className: 'text-center', searchable: false, render: function (d) { return `<b>${d}</b>`; } },
                { data: 'total_belanja', className: 'text-end', searchable: false, render: function (d) { return `<b class="text-danger">${formatRupiah(d)}</b>`; } },
                { data: 'id', orderable: false, searchable: false, className: 'text-center', render: function (d) { return `<button type="button" class="btnLihatStatistik btn btn-primary btn-circle btn-lg d-inline-flex align-items-center justify-content-center" data-id="${d}" title="Lihat transaksi"><i class="ti ti-eye"></i></button>`; } }
            ],
            drawCallback: function () { animasiProgressBar_stop(); }
        });
        var cariTimer = null;
        $('#cariStatistikPelanggan').on('input', function () {
            clearTimeout(cariTimer);
            cariTimer = setTimeout(function () { animasiProgressBar_run(); tabelStatistik.search($('#cariStatistikPelanggan').val()).draw(); }, 400);
        });
    });

    // muat statistik pelanggan (10 per halaman; "Muat lebih" utk lanjutan).
    // ringkasan = agregat penuh (dihitung di server), daftar dibatasi -> ringan walau ribuan data.
    var statOffset = 0;
    function buildStatRow(t, nomor) {
        return `<tr>
            <td>${nomor}</td>
            <td class="fw-semibold">${escapeHtml(t.id_transaksi)}</td>
            <td class="text-muted">${escapeHtml(t.tanggal)}</td>
            <td class="text-center">${t.qty}</td>
            <td class="text-end fw-semibold">${formatRupiah(t.total_belanja)}</td>
            <td class="text-center"><button type="button" class="btnPrintStat btn-print btn btn-sm btn-info btn-circle d-inline-flex align-items-center justify-content-center" data-id-transaksi="${escapeHtml(t.id_transaksi)}" title="Cetak nota"><i class="ti ti-receipt"></i></button></td>
        </tr>`;
    }
    function loadStatistikPelanggan(append) {
        var id = $('#statPelangganId').val();
        if (!id) return;
        var mulai = $('#statMulai').val(), selesai = $('#statSelesai').val();
        if (!append) {
            statOffset = 0;
            $('#statListTransaksi').html('<tr><td colspan="6" class="text-center text-muted py-3"><span class="spinner-border spinner-border-sm text-primary"></span> Memuat..</td></tr>');
        }
        $('#btnTerapkanStat, #btnMuatLebihStat').attr('disabled', true);
        animasiProgressBar_run();
        $.ajax({
            type: 'GET', url: "{{ route('pelanggan_transaksi') }}",
            data: { id_pelanggan: id, mulai: mulai, selesai: selesai, offset: statOffset }, dataType: 'JSON',
            success: function (r) {
                if (r.kode != 200) { toastError(r.pesan); return; }
                if (!append) {
                    $('#statNama').text(r.nama);
                    $('#statTelp').text(r.no_telp);
                    $('#statTotalTransaksi').text(r.total_transaksi);
                    $('#statTotalBarang').text(r.total_barang);
                    $('#statTotalBelanja').text(formatRupiah(r.total_belanja));
                    $('#statListTransaksi').empty();
                }
                if (!r.list.length && !append) {
                    $('#statListTransaksi').html('<tr><td colspan="6" class="text-center text-muted py-3">Tidak ada transaksi pada rentang ini</td></tr>');
                } else {
                    var html = '';
                    r.list.forEach(function (t, i) { html += buildStatRow(t, statOffset + i + 1); });
                    $('#statListTransaksi').append(html);
                }
                statOffset += r.list.length;
                $('#statInfo').text(r.total_transaksi > 0 ? ('Menampilkan ' + statOffset + ' dari ' + r.total_transaksi + ' transaksi') : '');
                $('#btnMuatLebihStat').toggle(!!r.has_more);
            },
            error: function () { toastError("Oops! Silahkan coba lagi!"); },
            complete: function () { animasiProgressBar_stop(); $('#btnTerapkanStat, #btnMuatLebihStat').attr('disabled', false); }
        });
    }

    // buka modal: set default rentang = bulan berjalan, lalu muat
    $(document).on('click', '.btnLihatStatistik', function () {
        $('#statPelangganId').val($(this).data('id'));
        var now = new Date();
        var y = now.getFullYear();
        var m = String(now.getMonth() + 1).padStart(2, '0');
        var d = String(now.getDate()).padStart(2, '0');
        $('#statMulai').val(y + '-' + m + '-01').removeAttr('max');
        $('#statSelesai').val(y + '-' + m + '-' + d).removeAttr('min');
        $('#statListTransaksi').html('');
        $('#statInfo').text(''); $('#btnMuatLebihStat').hide();
        $('#statTotalTransaksi').text('0'); $('#statTotalBarang').text('0'); $('#statTotalBelanja').text('Rp 0');
        $('#ModalStatistikPelanggan').modal('show');
        loadStatistikPelanggan(false);
    });
    $(document).on('click', '#btnTerapkanStat', function () {
        var m = $('#statMulai').val(), s = $('#statSelesai').val();
        if (m && s && m > s) return toastError('Tanggal mulai tidak boleh setelah selesai!');
        loadStatistikPelanggan(false);
    });
    // muat 10 berikutnya
    $(document).on('click', '#btnMuatLebihStat', function () { loadStatistikPelanggan(true); });
    // kunci min/max rentang
    $(document).on('change', '#statMulai', function () {
        $('#statSelesai').attr('min', this.value || '');
        if ($('#statSelesai').val() && this.value && $('#statSelesai').val() < this.value) $('#statSelesai').val('');
    });
    $(document).on('change', '#statSelesai', function () {
        $('#statMulai').attr('max', this.value || '');
        if ($('#statMulai').val() && this.value && $('#statMulai').val() > this.value) $('#statMulai').val('');
    });

    // cetak nota dari modal statistik (pakai fungsi cetakNota yang sudah ada)
    $(document).on('click', '.btnPrintStat', function () { cetakNota($(this).attr('data-id-transaksi'), $(this)); });
</script>

@endsection

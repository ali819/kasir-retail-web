@extends('layouts.main')
@section('content')
@section('title', 'Dashboard')

<style>
  .kpi-tile { border-radius: 12px; padding: 16px; }
  #ModalDetailPenjualan .table-responsive { padding-bottom: 8px; }
</style>

{{-- MAIN --}}
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12 d-flex align-items-strech">
            <div class="card w-100 mt-3">
                <div class="card-body">
                    <div class="d-sm-flex d-block align-items-center justify-content-between mb-9">
                        <div class="mb-3 mb-sm-0">
                            <h3 class="fw-semibold">Data Penjualan</h3>
                            <p class="card-subtitle mb-0">Grafik 6 hari terakhir</p>
                        </div>
                        @if($isAdmin)
                        <div>
                            <select id="ChartShow" class="form-control">
                                <option value="bar">Tampilan Grafik Omzet</option>
                                <option value="pie">Tampilan Grafik Terlaris</option>
                            </select>
                        </div>
                        @endif
                    </div>
                    <div class="row align-items-center">
                        <div class="col-lg-8 col-md-8">
                            @if($isAdmin)
                            <div id="barwrap" style="position:relative; height:320px;">
                                <canvas id="myBarChart"></canvas>
                            </div>
                            @endif
                            <div id="piewrap" style="position:relative; height:320px; max-width:400px; margin:auto;" {{ $isAdmin ? 'hidden' : '' }}>
                                <canvas id="myPieChart"></canvas>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="d-flex align-items-center mb-4 pb-1">
                                <div class="p-8 bg-light-primary rounded-1 me-3 d-flex align-items-center justify-content-center">
                                    <i class="ti ti-grid-dots text-primary fs-6"></i>
                                </div>
                                <div>
                                    <h4 class="mb-0 fs-7 fw-semibold">
                                        @if($isAdmin){{ $totalPenjualan }}@else<span class="text-muted fs-5"><i class="ti ti-lock"></i> Khusus admin</span>@endif
                                    </h4>
                                    <p class="fs-3 mb-0">Omzet ( 6 hari terakhir )</p>
                                </div>
                            </div>
                            <div>
                                <div class="d-flex align-items-baseline mb-4">
                                    <span class="round-8 bg-primary rounded-circle me-6"></span>
                                    <div>
                                        <p class="fs-3 mb-1">Transaksi</p>
                                        <h6 class="fs-5 fw-semibold mb-0">{{ $totalTransaksi }}</h6>
                                    </div>
                                </div>
                                <div class="d-flex align-items-baseline mb-4 pb-1">
                                    <span class="round-8 bg-secondary rounded-circle me-6"></span>
                                    <div>
                                        <p class="fs-3 mb-1">Barang Terjual</p>
                                        <h6 class="fs-5 fw-semibold mb-0">{{ $totalBarangTerjual }}</h6>
                                    </div>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-lg btn-outline-primary w-100" id="btnShowAll">Detail Statistik<i class="ti ti-file-description"></i></button>
                                    <button type="button" class="btn btn-outline-success w-100 mt-2 btn-lg" id="btnExportLaporan">Export Laporan <i class="ti ti-download"></i></button>

                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL EXPORT LAPORAN --}}
<div class="modal fade" id="ModalExport" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="ti ti-file-spreadsheet"></i>&nbsp; Export Laporan Excel</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert customize-alert text-primary border border-primary py-2">
                    <i class="ti ti-info-circle me-1"></i> Laporan lengkap: Ringkasan, Per Transaksi, Detail Item, Rekap Produk, Kasir, Pelanggan &amp; Harian.
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="fs-2 mb-1"><b>Dari</b></label>
                        <input type="date" class="form-control" id="exportMulai">
                    </div>
                    <div class="col-6">
                        <label class="fs-2 mb-1"><b>Sampai</b></label>
                        <input type="date" class="form-control" id="exportSelesai">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success fw-semibold" id="btnDoExport"><i class="ti ti-download"></i>&nbsp; Export Excel</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL DETAIL PENJUALAN --}}
<div class="modal fade" id="ModalDetailPenjualan" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="ti ti-chart-bar"></i>&nbsp; Detail Penjualan</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                {{-- KPI TILES --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <div class="kpi-tile bg-light-danger">
                            <p class="fs-3 mb-1 text-danger"><span class="round-8 bg-danger rounded-circle d-inline-block me-1"></span> Total Omzet</p>
                            <h3 class="mb-0 fw-bold text-danger" id="dataTotalOmzet">Rp 0</h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="kpi-tile bg-light-primary">
                            <p class="fs-3 mb-1 text-primary"><span class="round-8 bg-primary rounded-circle d-inline-block me-1"></span> Total Transaksi</p>
                            <h3 class="mb-0 fw-bold text-primary" id="dataTotalTransaksi">0</h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="kpi-tile bg-light-info">
                            <p class="fs-3 mb-1 text-info"><span class="round-8 bg-info rounded-circle d-inline-block me-1"></span> Barang Terjual</p>
                            <h3 class="mb-0 fw-bold text-info" id="dataTotalTerjual">0</h3>
                        </div>
                    </div>
                </div>

                {{-- REKAP RENTANG TANGGAL --}}
                <div class="border rounded p-3 mb-3">
                    <label class="fs-2 mb-1"><b>Rekap penjualan (rentang tanggal)</b></label>
                    <div class="row g-2 align-items-end">
                        <div class="col-md-5 col-6">
                            <label class="fs-2 mb-1 text-muted">Dari</label>
                            <input type="date" class="form-control" id="cariTanggalMulai">
                        </div>
                        <div class="col-md-5 col-6">
                            <label class="fs-2 mb-1 text-muted">Sampai</label>
                            <input type="date" class="form-control" id="cariTanggalSelesai">
                        </div>
                        <div class="col-md-2 col-12">
                            <button type="button" class="btn btn-info fw-semibold w-100" id="btnHitungPenjualan"><i class="ti ti-calculator"></i> Hitung</button>
                        </div>
                    </div>
                    <small class="text-muted">Rentang ini juga memfilter tabel di bawah.</small>
                </div>

                {{-- FILTER TABEL --}}
                <div class="row g-2 mb-1">
                    <div class="col-md-4 col-6">
                        <label class="fs-2 mb-1">Nama Barang</label>
                        <input type="text" class="form-control" id="cariNamaBarang" placeholder="Nama barang ..">
                    </div>
                    <div class="col-md-3 col-6">
                        <label class="fs-2 mb-1">ID Transaksi</label>
                        <input type="text" class="form-control" id="cariIdTransaksi" placeholder="TRX-..">
                    </div>
                    <div class="col-md-2 col-4">
                        <label class="fs-2 mb-1">Qty</label>
                        <input type="number" class="form-control" id="cariQty" placeholder="Qty">
                    </div>
                    <div class="col-md-3 col-8">
                        <label class="fs-2 mb-1">Total Harga</label>
                        <input type="number" class="form-control" id="cariTotalHarga" placeholder="Nominal ..">
                    </div>
                </div>

                <div class="progress my-2">
                    <div class="progress-bar progress-bar-striped bg-primary progress-bar-animated animasiProgressBar" role="progressbar" style="width: 0%"></div>
                </div>

                {{-- TABEL --}}
                <div class="table-responsive">
                    <table class="table align-middle text-nowrap mb-0" id="tabel_detail_pembelian" style="width:100%;">
                        <thead>
                            <tr>
                                <th>Barang</th>
                                <th>ID Transaksi</th>
                                <th>Tanggal</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Harga</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup&emsp;<i class="ti ti-x"></i></button>
            </div>
        </div>
    </div>
</div>

<script>
    function escapeHtml(t){ if(t==null) return ''; return String(t).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;'); }
    function trimText(t){ t = t||''; return t.length > 28 ? t.substring(0,28)+'..' : t; }
    function formatRupiah(angka){ angka = Math.floor(angka || 0); return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'); }
    function animasiProgressBar_run(){ $('.animasiProgressBar').css('width','100%'); }
    function animasiProgressBar_stop(){ $('.animasiProgressBar').css('width','0%'); }

    var IS_ADMIN = @json($isAdmin);

    // ===== PILIHAN GRAFIK =====
    $(document).on('change', '#ChartShow', function () {
        var v = $(this).val();
        chartShow(v);
        localStorage.setItem('chartType', v);
    });
    function chartShow(select) {
        $('#piewrap').attr('hidden', select !== 'pie');
        $('#barwrap').attr('hidden', select !== 'bar');
        setTimeout(function () {
            if (select === 'pie' && window.pieChart) window.pieChart.resize();
            if (select === 'bar' && window.barChart) window.barChart.resize();
        }, 30);
    }
    $(document).ready(function () {
        var saved = IS_ADMIN ? (localStorage.getItem('chartType') || 'bar') : 'pie';
        $('#ChartShow').val(saved);
        chartShow(saved);
    });

    // Lihat Semua & Export Laporan: khusus admin
    $(document).on('click', '#btnShowAll', function () {
        if (!IS_ADMIN) return toastError('Fitur ini hanya untuk admin!');
        $('#ModalDetailPenjualan').modal('show');
    });
    $(document).on('click', '#btnExportLaporan', function () {
        if (!IS_ADMIN) return toastError('Export laporan hanya untuk admin!');
        $('#exportMulai').val('').removeAttr('max');
        $('#exportSelesai').val('').removeAttr('min');
        $('#ModalExport').modal('show');
    });
    // batasi rentang: mulai <= selesai (min/max saling mengunci)
    $(document).on('change', '#exportMulai', function () {
        $('#exportSelesai').attr('min', this.value || '');
        if ($('#exportSelesai').val() && this.value && $('#exportSelesai').val() < this.value) $('#exportSelesai').val('');
    });
    $(document).on('change', '#exportSelesai', function () {
        $('#exportMulai').attr('max', this.value || '');
        if ($('#exportMulai').val() && this.value && $('#exportMulai').val() > this.value) $('#exportMulai').val('');
    });
    $(document).on('click', '#btnDoExport', function () {
        var mulai = $('#exportMulai').val(), selesai = $('#exportSelesai').val();
        if (!mulai || !selesai) return toastError('Pilih tanggal mulai & selesai!');
        if (mulai > selesai) return toastError('Tanggal mulai tidak boleh setelah tanggal selesai!');
        window.location = "{{ route('export_laporan') }}" + '?mulai=' + encodeURIComponent(mulai) + '&selesai=' + encodeURIComponent(selesai);
        toastSuccess('Menyiapkan file Excel..');
        $('#ModalExport').modal('hide');
    });
</script>

{{-- GRAFIK --}}
<script src="{{ asset('template/chart/chart.min.js') }}"></script>
<script>
    var xValues = @json(array_values($xValues));
    var yValues = @json($isAdmin ? array_values($yValues) : []);

    function rpAxis(v) { return 'Rp ' + Number(v).toLocaleString('id-ID'); }

    // BAR CHART (OMZET) — canvas hanya dirender untuk admin
    if (document.getElementById('myBarChart')) {
    window.barChart = new Chart("myBarChart", {
        type: "bar",
        data: {
            labels: xValues,
            datasets: [{
                label: 'Omzet',
                backgroundColor: 'rgba(93, 135, 255, 0.25)',
                borderColor: 'rgb(93, 135, 255)',
                borderWidth: 1.5,
                data: yValues
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 0 },
            legend: { display: false },
            title: { display: true, text: 'Total Omzet per Tanggal (Rp)', fontSize: 15 },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        callback: function (v) { return rpAxis(v); }
                    }
                }]
            },
            tooltips: {
                callbacks: {
                    label: function (item) { return ' ' + rpAxis(item.yLabel); }
                }
            }
        }
    });
    }
</script>

@php
    $xValuesPie = $topProducts->pluck('nama_barang')->toArray();
    $yValuesPie = $topProducts->pluck('total_qty')->toArray();
    $barColorsPie = ['#5d87ff', '#49beff', '#13deb9', '#ffae1f', '#fa896b'];
@endphp
<script>
    // PIE CHART (TERLARIS)
    window.pieChart = new Chart("myPieChart", {
        type: "pie",
        data: {
            labels: @json($xValuesPie),
            datasets: [{ backgroundColor: @json($barColorsPie), data: @json($yValuesPie) }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 0 },
            title: { display: true, text: 'Produk Terlaris ( 6 hari terakhir )', fontSize: 15 },
            tooltips: {
                callbacks: {
                    label: function (item, data) {
                        var lbl = data.labels[item.index];
                        var val = data.datasets[0].data[item.index];
                        return ' ' + lbl + ': ' + val + ' terjual';
                    }
                }
            }
        }
    });
</script>

{{-- TABEL DETAIL & REKAP (khusus admin) --}}
<script>
    $(document).ready(function () {
        if (!IS_ADMIN) return; // route detail/rekap admin-only
        var tabel = $("#tabel_detail_pembelian").DataTable({
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
            ajax: {
                url: "{{ route('tabel_detail_pembelian') }}", type: 'GET',
                data: function (d) {
                    d.tgl_mulai = $('#cariTanggalMulai').val();
                    d.tgl_selesai = $('#cariTanggalSelesai').val();
                }
            },
            columns: [
                {
                    data: 'nama_barang',
                    render: function (d, t, row) {
                        return `<h6 class="fw-semibold mb-0 fs-4">${escapeHtml(d)}</h6><span class="badge bg-light-primary text-primary">${escapeHtml(row.satuan)}</span>`;
                    }
                },
                { data: 'id_transaksi', render: function (d) { return `<span class="fw-semibold">${escapeHtml(trimText(d))}</span>`; } },
                { data: 'created_at', render: function (d, t, row) { return `<span class="text-muted">${escapeHtml(row.formatted_date)}</span>`; } },
                { data: 'qty', className: 'text-center', render: function (d) { return `<b>${d}</b>`; } },
                { data: 'harga', className: 'text-end', render: function (d) { return formatRupiah(d); } },
                { data: 'total_harga', className: 'text-end', render: function (d) { return `<b class="text-danger">${formatRupiah(d)}</b>`; } },
                { data: 'satuan', visible: false }
            ],
            drawCallback: function () { animasiProgressBar_stop(); }
        });

        // PENCARIAN (kolom: 0 nama, 1 id, 2 tanggal, 3 qty, 5 total, 6 satuan)
        var tN=null,tI=null,tQ=null,tT=null;
        $('#cariNamaBarang').on('input', function () {
            clearTimeout(tN); tN = setTimeout(function () { animasiProgressBar_run(); tabel.column(0).search($('#cariNamaBarang').val()).draw(); }, 500);
        });
        $('#cariIdTransaksi').on('input', function () {
            $(this).val($(this).val().toUpperCase());
            clearTimeout(tI); tI = setTimeout(function () { animasiProgressBar_run(); tabel.column(1).search($('#cariIdTransaksi').val()).draw(); }, 500);
        });
        $('#cariQty').on('input', function () {
            clearTimeout(tQ); tQ = setTimeout(function () { animasiProgressBar_run(); tabel.column(3).search($('#cariQty').val()).draw(); }, 500);
        });
        $('#cariTotalHarga').on('input', function () {
            clearTimeout(tT); tT = setTimeout(function () { animasiProgressBar_run(); tabel.column(5).search($('#cariTotalHarga').val()).draw(); }, 500);
        });
        // RENTANG TANGGAL: validasi min/max + filter tabel
        $('#cariTanggalMulai').on('change', function () {
            $('#cariTanggalSelesai').attr('min', this.value || '');
            var s = $('#cariTanggalSelesai').val();
            if (s && this.value && s < this.value) { $('#cariTanggalSelesai').val(''); }
            resetKPI(); animasiProgressBar_run(); tabel.draw();
        });
        $('#cariTanggalSelesai').on('change', function () {
            $('#cariTanggalMulai').attr('max', this.value || '');
            var m = $('#cariTanggalMulai').val();
            if (m && this.value && m > this.value) { $('#cariTanggalMulai').val(''); }
            resetKPI(); animasiProgressBar_run(); tabel.draw();
        });
    });

    function resetKPI() {
        $('#dataTotalOmzet').html('Rp 0');
        $('#dataTotalTransaksi').html('0');
        $('#dataTotalTerjual').html('0');
    }

    // HITUNG PENJUALAN
    $(document).on('click', '#btnHitungPenjualan', function () {
        var btnDef = $(this).html();
        var mulai = $('#cariTanggalMulai').val();
        var selesai = $('#cariTanggalSelesai').val();
        if (mulai === '' || selesai === '') { return toastError("Pilih tanggal mulai & selesai!"); }
        if (mulai > selesai) { return toastError("Tanggal mulai tidak boleh setelah tanggal selesai!"); }
        customConfirm("Hitung penjualan ?", "Data dihitung berdasarkan rentang tanggal.").then((confirmed) => {
            if (!confirmed) return;
            $('#btnHitungPenjualan').attr('disabled', true).html('Memproses ..');
            $.ajax({
                type: "GET", url: "{{ route('hitung_data_pembelian') }}", data: { tanggal_mulai: mulai, tanggal_selesai: selesai }, dataType: "JSON",
                success: function (response) {
                    if (response.kode == 404) { toastError(response.pesan); return; }
                    $('#dataTotalOmzet').html(response.total_omzet);
                    $('#dataTotalTransaksi').html(response.total_transaksi);
                    $('#dataTotalTerjual').html(response.barang_terjual);
                    toastSuccess(response.pesan);
                },
                error: function () { toastError("Oops! Silahkan coba lagi!"); },
                complete: function () { $('#btnHitungPenjualan').attr('disabled', false).html(btnDef); }
            });
        });
    });
</script>

@endsection

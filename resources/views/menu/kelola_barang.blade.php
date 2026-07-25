@extends('layouts.main')
@section('content')
@section('title', 'Kelola Barang')

<style>
    .satuan-row .form-control-sm { font-size: 14px; }
    .satuan-head { font-size: 12px; font-weight: 600; color: #7c8fac; }
    .hr-dashed { border: none; border-top: 1px dashed #b0b8c4; margin: 1rem 0; }
    /* serap ~5px overflow semu dari DataTables (overflow-x:auto memaksa overflow-y jadi auto) */
    .table-responsive { padding-bottom: 8px; }
</style>

{{-- MAIN --}}
<div class="container-fluid">
    <div class="card bg-light-info shadow-none position-relative overflow-hidden">
        <div class="card-body px-4 py-3">
            <div class="row align-items-center">
                <div class="col-9">
                    <h4 class="fw-semibold mb-8">Kelola Barang</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a class="text-muted" href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item" aria-current="page">Kelola Barang</li>
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
                    Setiap barang punya <b>&nbsp;satuan dasar&nbsp;</b> (untuk stok) &amp; satu/lebih <b>&nbsp;satuan jual&nbsp;</b> (harga + konversi). Contoh: Bolpoin — dasar "Pcs", satuan jual "Ecer" (×1) &amp; "Lusin" (×12).
                </div>
            </div>
        </div>
    </div>

    <div class="w-100 position-relative overflow-hidden">
        <div class="py-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-6 col-12">
                    <label for="cariNamaBarang" style="margin-bottom:5px;"><b>Cari Barang</b></label>
                    <input type="search" class="form-control" id="cariNamaBarang" placeholder="Ketik nama barang..">
                </div>
                <div class="col-md-3 col-6">
                    <label for="cariStok" style="margin-bottom:5px;"><b>Stok</b></label>
                    <select id="cariStok" class="form-control">
                        <option value="0">Semua</option>
                        <option value="1">Tersedia</option>
                        <option value="2">Habis</option>
                    </select>
                </div>
                <div class="col-md-3 col-6">
                    <button type="button" class="btn btn-primary col-12 btnTambahBarang" style="font-weight:bold"><i class="ti ti-plus"></i>&nbsp; Tambah Barang</button>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle text-nowrap mb-0" id="tabel_stok" style="width:100%;">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Barang</th>
                            <th>Stok</th>
                            <th>Satuan Jual</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH --}}
<div class="modal fade" id="ModalTambahBarang" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h4 class="modal-title">Tambah Barang</h4></div>
            <form id="FormTambahBarang" autocomplete="off">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="mb-1"><b>Nama Barang</b></label>
                            <input type="text" class="form-control" name="nama_barang" id="tbhNamaBarang" placeholder="Nama barang .." required>
                        </div>
                        <div class="col-md-4">
                            <label class="mb-1"><b>Satuan Dasar</b></label>
                            <input type="text" class="form-control" name="satuan_dasar" id="tbhSatuanDasar" placeholder="mis. Pcs / Gram" value="Pcs" required>
                        </div>
                        <div class="col-md-4">
                            <label class="mb-1"><b>Stok</b> <span class="text-muted">(satuan dasar)</span></label>
                            <input type="number" class="form-control" name="stok" id="tbhStok" placeholder="0" value="0">
                        </div>
                        <div class="col-md-8">
                            <label class="mb-1"><b>Keterangan</b></label>
                            <input type="text" class="form-control" name="keterangan" id="tbhKeterangan" placeholder="Opsional ..">
                        </div>
                        <div class="col-md-4">
                            <label class="mb-1"><b>Barcode</b> <span class="text-muted">(ops.)</span></label>
                            <input type="text" class="form-control" name="barcode" id="tbhBarcode" placeholder="Scan / ketik ..">
                        </div>
                        <div class="col-12">
                            <label class="mb-1"><b>Gambar Produk</b> <span class="text-muted">(opsional)</span></label>
                            <div class="d-flex align-items-center gap-3">
                                <img id="tbhGambarPreview" src="" style="width:64px;height:64px;object-fit:cover;border:1px solid #eee;border-radius:10px;display:none;">
                                <div class="flex-grow-1">
                                    <input type="file" class="form-control" id="tbhGambar" name="gambar" accept="image/png,image/jpeg,image/webp">
                                    <small class="text-muted">PNG / JPG / WEBP, maks 2 MB.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="hr-dashed">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <b>Satuan Jual</b>
                        <button type="button" class="btn btn-outline-primary btn-add-satuan" data-target="#tambahSatuanContainer"><i class="ti ti-plus"></i> Tambah Satuan</button>
                    </div>
                    <div class="row g-2 satuan-head px-1 d-none d-md-flex">
                        <div class="col-4">Nama Satuan</div>
                        <div class="col-4">Harga</div>
                        <div class="col-3">Konversi</div>
                        <div class="col-1"></div>
                    </div>
                    <div id="tambahSatuanContainer"></div>
                    <small class="text-muted"><b>Konversi</b> = 1 satuan ini setara berapa satuan dasar. Ecer/satuan biasa = 1, Lusin = 12, dst.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal&emsp;<i class="ti ti-x"></i></button>
                    <button type="submit" class="btn btn-primary btnKonfirmasiTambahBarang"><i class="ti ti-check"></i>&emsp;Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL UPDATE --}}
<div class="modal fade" id="ModalUpdateBarang" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h4 class="modal-title" id="updateTitleNamaBarang">Ubah Barang</h4></div>
            <form id="FormUpdateBarang" autocomplete="off">
                @csrf
                <input type="hidden" name="id" id="updateId">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="mb-1"><b>Nama Barang</b></label>
                            <input type="text" class="form-control" name="nama_barang" id="updateNamaBarang" required>
                        </div>
                        <div class="col-md-4">
                            <label class="mb-1"><b>Satuan Dasar</b></label>
                            <input type="text" class="form-control" name="satuan_dasar" id="updateSatuanDasar" required>
                        </div>
                        <div class="col-md-4">
                            <label class="mb-1"><b>Stok</b> <span class="text-muted">(satuan dasar)</span></label>
                            <input type="number" class="form-control" name="stok" id="updateStok" value="0">
                        </div>
                        <div class="col-md-8">
                            <label class="mb-1"><b>Keterangan</b></label>
                            <input type="text" class="form-control" name="keterangan" id="updateKeterangan" placeholder="Opsional ..">
                        </div>
                        <div class="col-md-4">
                            <label class="mb-1"><b>Barcode</b></label>
                            <input type="text" class="form-control" name="barcode" id="updateBarcode" placeholder="Scan / ketik ..">
                        </div>
                        <div class="col-12">
                            <label class="mb-1"><b>Gambar Produk</b></label>
                            <div class="d-flex align-items-center gap-3">
                                <img id="updateGambarPreview" src="" style="width:64px;height:64px;object-fit:cover;border:1px solid #eee;border-radius:10px;display:none;">
                                <div class="flex-grow-1">
                                    <input type="file" class="form-control" id="updateGambar" name="gambar" accept="image/png,image/jpeg,image/webp">
                                    <small class="text-muted">Kosongkan bila tak ingin mengubah gambar.</small>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger d-none align-self-start" id="btnHapusGambarBarang" title="Hapus gambar"><i class="ti ti-trash"></i></button>
                            </div>
                            <input type="hidden" name="hapus_gambar" id="updateHapusGambar" value="0">
                        </div>
                    </div>

                    <hr class="hr-dashed">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <b>Satuan Jual</b>
                        <button type="button" class="btn btn-outline-primary btn-add-satuan" data-target="#editSatuanContainer"><i class="ti ti-plus"></i> Tambah Satuan</button>
                    </div>
                    <div class="row g-2 satuan-head px-1 d-none d-md-flex">
                        <div class="col-4">Nama Satuan</div>
                        <div class="col-4">Harga</div>
                        <div class="col-3">Konversi</div>
                        <div class="col-1"></div>
                    </div>
                    <div id="editSatuanContainer"></div>
                    <small class="text-muted"><b>Konversi</b> = 1 satuan ini setara berapa satuan dasar.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal&emsp;<i class="ti ti-x"></i></button>
                    <button type="submit" class="btn btn-primary btnKonfirmasiUpdateBarang"><i class="ti ti-check"></i>&emsp;Perbarui</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function escapeHtml(t){ if(t==null) return ''; return String(t).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;'); }
    // Yajra kadang mengirim kolom nested sebagai string JSON -> pastikan jadi array
    function asArray(v){ if(typeof v==='string'){ try{ v = JSON.parse(v); }catch(e){ return []; } } if(Array.isArray(v)) return v; return v && typeof v==='object' ? Object.values(v) : []; }
    function trimText(t){ t = t||''; return t.length > 22 ? t.substring(0,22)+'..' : t; }

    // ====== BARIS SATUAN JUAL ======
    var satuanIdx = 0;
    function satuanRowHTML(data) {
        data = data || {};
        var i = ++satuanIdx;
        return `
        <div class="row g-2 mb-2 satuan-row align-items-center">
            <div class="col-4">
                <input type="text" class="form-control form-control-sm" name="satuan[${i}][nama_satuan]" placeholder="mis. Ecer" value="${escapeHtml(data.nama_satuan||'')}" required>
            </div>
            <div class="col-4">
                <input type="text" inputmode="numeric" class="form-control form-control-sm input-rupiah" name="satuan[${i}][harga]" placeholder="Harga" value="${data.harga||''}" required>
            </div>
            <div class="col-3">
                <input type="number" min="1" class="form-control form-control-sm" name="satuan[${i}][konversi]" placeholder="1" value="${data.konversi||1}" required>
            </div>
            <div class="col-1 text-end">
                <input type="hidden" name="satuan[${i}][id]" value="${data.id||''}">
                <button type="button" class="btn btn-sm text-danger btn-remove-satuan p-0"><i class="ti ti-trash fs-4"></i></button>
            </div>
        </div>`;
    }
    function fillSatuan(containerSel, list) {
        var $c = $(containerSel).html('');
        if (list && list.length) {
            list.forEach(function(s){ $c.append(satuanRowHTML(s)); });
        } else {
            $c.append(satuanRowHTML({ nama_satuan: 'Ecer', konversi: 1 }));
        }
        $c.find('.input-rupiah').each(function(){ formatRupiahInputEl(this); });
    }
    $(document).on('click', '.btn-add-satuan', function(){
        var target = $(this).data('target');
        $(target).append(satuanRowHTML({ konversi: 1 }));
    });
    $(document).on('click', '.btn-remove-satuan', function(){
        var $c = $(this).closest('[id$="SatuanContainer"]');
        $(this).closest('.satuan-row').remove();
        if ($c.find('.satuan-row').length < 1) { $c.append(satuanRowHTML({ konversi: 1 })); }
    });

    // ====== DATATABLE ======
    var tabel_stok;
    $(document).ready(function () {
        tabel_stok = $("#tabel_stok").DataTable({
            dom: 'lrtip',
            order: [[0, 'ASC']],
            autoWidth: false,
            language: {
                "sProcessing":"Sedang memproses...","sLengthMenu":"Tampilkan _MENU_ entri","sZeroRecords":"Tidak ditemukan data yang sesuai",
                "loadingRecords":"Memuat data, mohon tunggu ...","sInfo":"Total : <b>_TOTAL_</b> entri","sInfoEmpty":"Menampilkan : <b>0</b> entri",
                "sInfoFiltered":"","sSearch":"Cari : ",
                "oPaginate":{"sFirst":"Pertama","sPrevious":"<i class='ti ti-chevron-left'></i>","sNext":"<i class='ti ti-chevron-right'></i>","sLast":"Terakhir"}
            },
            serverSide: true,
            ajax: {
                url: "{{ route('tabel_stok') }}",
                type: 'GET',
                data: function (d) { d.stok = $('#cariStok').val(); },
            },
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                {
                    data: 'nama_barang',
                    render: function (data, type, row) {
                        var img = row.gambar_url
                            ? `<img src="${row.gambar_url}" style="width:44px;height:44px;object-fit:cover;border-radius:8px;">`
                            : `<span class="d-inline-flex align-items-center justify-content-center bg-light text-muted" style="width:44px;height:44px;border-radius:8px;"><i class="ti ti-photo"></i></span>`;
                        return `<div class="d-flex align-items-center gap-2">${img}
                                <div><h6 class="fw-semibold mb-0 fs-4">${escapeHtml(trimText(data))}</h6>
                                <span class="text-muted fs-2">dasar: ${escapeHtml(row.satuan_dasar)}</span></div></div>`;
                    }
                },
                {
                    data: 'stok',
                    render: function (data, type, row) {
                        var badge = data > 0 ? 'bg-light-success text-success' : 'bg-light-danger text-danger';
                        return `<span class="badge ${badge}">${data} ${escapeHtml(row.satuan_dasar)}</span>`;
                    }
                },
                {
                    data: 'satuan_jual',
                    orderable: false, searchable: false,
                    render: function (list) {
                        list = asArray(list);
                        if (!list.length) return '<span class="text-muted">-</span>';
                        return list.map(function (s) {
                            var kon = s.konversi > 1 ? ` <span class="text-muted">(×${s.konversi})</span>` : '';
                            return `<div><b>${escapeHtml(s.nama_satuan)}</b>: ${formatRupiah(s.harga)}${kon}</div>`;
                        }).join('');
                    }
                },
                {
                    data: 'id',
                    orderable: false, searchable: false, className: 'text-center',
                    render: function (data) {
                        return `
                        <button type="button" class="btnEditStok btn btn-primary btn-circle btn-lg d-inline-flex align-items-center justify-content-center"><i class="ti ti-pencil"></i></button>
                        <button type="button" class="btnHapusStok btn btn-danger btn-circle btn-lg d-inline-flex align-items-center justify-content-center" data-id="${data}"><i class="ti ti-trash"></i></button>`;
                    }
                }
            ],
            drawCallback: function () { animasiProgressBar_stop(); }
        });

        let cariTimer = null;
        $('#cariNamaBarang').on('input', function () {
            clearTimeout(cariTimer);
            cariTimer = setTimeout(function () { animasiProgressBar_run(); tabel_stok.column(1).search($('#cariNamaBarang').val()).draw(); }, 500);
        });
        $('#cariStok').on('change', function () { animasiProgressBar_run(); tabel_stok.draw(); });
    });

    // ====== TAMBAH ======
    $(document).on('click', '.btnTambahBarang', function () {
        $('#FormTambahBarang')[0].reset();
        $('#tbhSatuanDasar').val('Pcs');
        $('#tbhGambarPreview').attr('src', '').hide();
        fillSatuan('#tambahSatuanContainer', null);
        $('#ModalTambahBarang').modal('show');
    });
    $('#FormTambahBarang').on('submit', function (e) {
        e.preventDefault();
        customConfirm("Tambah barang ?", "Pastikan data sudah benar.").then((confirmed) => {
            if (!confirmed) return;
            var data = formDataRupiah(this);
            var btn = $('.btnKonfirmasiTambahBarang').html();
            $('.btnKonfirmasiTambahBarang').html('Memproses ..').attr('disabled', true);
            $.ajax({
                type: "POST", url: "{{ route('tambah_barang_baru') }}", data: data, processData: false, contentType: false, dataType: "JSON",
                success: function (r) {
                    if (r.kode == 200) { $('#ModalTambahBarang').modal('hide'); tabel_stok.draw(); toastSuccess(r.pesan); }
                    else if (r.kode == 422) { errorHandlerToast(r.pesan); }
                    else { toastError(r.pesan); }
                },
                error: function () { toastError("Oops! Silahkan coba lagi!"); },
                complete: function () { $('.btnKonfirmasiTambahBarang').html(btn).attr('disabled', false); }
            });
        });
    });

    // ====== EDIT (data dari baris DataTable) ======
    $(document).on('click', '.btnEditStok', function () {
        var row = tabel_stok.row($(this).parents('tr')).data();
        if (!row) { toastError("Data tidak ditemukan, muat ulang halaman!"); return; }
        $('#updateId').val(row.id);
        $('#updateTitleNamaBarang').html('Ubah: ' + row.nama_barang);
        $('#updateNamaBarang').val(row.nama_barang);
        $('#updateBarcode').val(row.barcode || '');
        $('#updateSatuanDasar').val(row.satuan_dasar);
        $('#updateStok').val(row.stok);
        $('#updateKeterangan').val(row.keterangan || '');
        // gambar
        $('#updateGambar').val('');
        $('#updateHapusGambar').val('0');
        if (row.gambar_url) {
            $('#updateGambarPreview').attr('src', row.gambar_url).show();
            $('#btnHapusGambarBarang').removeClass('d-none');
        } else {
            $('#updateGambarPreview').attr('src', '').hide();
            $('#btnHapusGambarBarang').addClass('d-none');
        }
        fillSatuan('#editSatuanContainer', asArray(row.satuan_jual));
        $('#ModalUpdateBarang').modal('show');
    });
    $('#FormUpdateBarang').on('submit', function (e) {
        e.preventDefault();
        customConfirm("Perbarui ?", "Pastikan data sudah benar.").then((confirmed) => {
            if (!confirmed) return;
            var data = formDataRupiah(this);
            var btn = $('.btnKonfirmasiUpdateBarang').html();
            $('.btnKonfirmasiUpdateBarang').html('Memproses ..').attr('disabled', true);
            $.ajax({
                type: "POST", url: "{{ route('update_barang') }}", data: data, processData: false, contentType: false, dataType: "JSON",
                success: function (r) {
                    if (r.kode == 200) { $('#ModalUpdateBarang').modal('hide'); tabel_stok.draw(); toastSuccess(r.pesan); }
                    else if (r.kode == 422) { errorHandlerToast(r.pesan); }
                    else { toastError(r.pesan); }
                },
                error: function () { toastError("Oops! Silahkan coba lagi!"); },
                complete: function () { $('.btnKonfirmasiUpdateBarang').html(btn).attr('disabled', false); }
            });
        });
    });

    // ====== GAMBAR PRODUK (preview) ======
    function previewGambar(input, imgSel) {
        var f = input.files[0];
        if (!f) return;
        if (f.size > 2 * 1024 * 1024) { toastError("Ukuran gambar maksimal 2 MB!"); input.value = ''; return; }
        var reader = new FileReader();
        reader.onload = function (e) { $(imgSel).attr('src', e.target.result).show(); };
        reader.readAsDataURL(f);
    }
    $(document).on('change', '#tbhGambar', function () { previewGambar(this, '#tbhGambarPreview'); });
    $(document).on('change', '#updateGambar', function () {
        $('#updateHapusGambar').val('0');
        $('#btnHapusGambarBarang').removeClass('d-none');
        previewGambar(this, '#updateGambarPreview');
    });
    $(document).on('click', '#btnHapusGambarBarang', function () {
        $('#updateGambar').val('');
        $('#updateHapusGambar').val('1');
        $('#updateGambarPreview').attr('src', '').hide();
        $(this).addClass('d-none');
    });

    // ====== HAPUS ======
    $(document).on('click', '.btnHapusStok', function () {
        var id = $(this).data('id');
        customConfirm("Hapus barang ?", "Barang & semua satuan jualnya akan dihapus permanen.").then((confirmed) => {
            if (!confirmed) return;
            $.ajax({
                type: "DELETE", url: "{{ route('hapus_data_barang') }}",
                data: { _token: "{{ csrf_token() }}", id: id }, dataType: "JSON",
                success: function (r) {
                    if (r.kode == 200) { tabel_stok.draw(); toastSuccess(r.pesan); }
                    else { toastError(r.pesan); }
                },
                error: function () { toastError("Oops! Silahkan coba lagi!"); }
            });
        });
    });

    // ====== TERBILANG (klik input harga) ======
    $(document).on('click', '.input-rupiah', function () {
        var angka = rupiahToInt($(this).val());
        if (angka < 1) return;
        toastSuccess(angkaTerbilang(angka));
    });

    function angkaTerbilang(nilai) {
        nilai = Math.floor(Math.abs(nilai));
        var huruf = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
        var bagi = 0, hasil = '';
        if (nilai < 12) hasil = ' ' + huruf[nilai];
        else if (nilai < 20) hasil = angkaTerbilang(nilai - 10) + ' Belas';
        else if (nilai < 100) { bagi = Math.floor(nilai / 10); hasil = angkaTerbilang(bagi) + ' Puluh' + angkaTerbilang(nilai % 10); }
        else if (nilai < 200) hasil = ' Seratus' + angkaTerbilang(nilai - 100);
        else if (nilai < 1000) { bagi = Math.floor(nilai / 100); hasil = angkaTerbilang(bagi) + ' Ratus' + angkaTerbilang(nilai % 100); }
        else if (nilai < 2000) hasil = ' Seribu' + angkaTerbilang(nilai - 1000);
        else if (nilai < 1000000) { bagi = Math.floor(nilai / 1000); hasil = angkaTerbilang(bagi) + ' Ribu' + angkaTerbilang(nilai % 1000); }
        else if (nilai < 1000000000) { bagi = Math.floor(nilai / 1000000); hasil = angkaTerbilang(bagi) + ' Juta' + angkaTerbilang(nilai % 1000000); }
        else if (nilai < 1000000000000) { bagi = Math.floor(nilai / 1000000000); hasil = angkaTerbilang(bagi) + ' Miliar' + angkaTerbilang(nilai % 1000000000); }
        else hasil = 'Nominal terlalu besar!';
        return hasil;
    }
    function formatRupiah(angka) {
        angka = Math.floor(angka || 0);
        return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
</script>

@endsection

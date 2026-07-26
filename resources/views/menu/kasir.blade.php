@extends('layouts.main')
@section('content')
@section('title', 'Kasir')

<style>
    /* ===== PRODUCT CARD ===== */
    .prod-card { cursor: pointer; border: 1px solid #eaeef4; border-radius: 14px; overflow: hidden; background: #fff; transition: box-shadow .15s, transform .15s, border-color .15s; }
    .prod-card:hover { box-shadow: 0 10px 24px rgba(0,0,0,.09); transform: translateY(-3px); border-color: #d3ddf3; }
    .prod-card:active { transform: translateY(-1px); }
    .prod-img-wrap { position: relative; aspect-ratio: 1 / 1; background: #f4f6fb; overflow: hidden; }
    .prod-img-wrap img { width: 100%; height: 100%; object-fit: cover; }
    .prod-noimg { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #c6ccda; font-size: 34px; }
    .prod-stok { position: absolute; top: 8px; left: 8px; background: rgba(19,30,55,.62); color: #fff; font-size: 11px; font-weight: 600; padding: 2px 9px; border-radius: 20px; backdrop-filter: blur(2px); }
    .prod-stok.habis { background: rgba(220,53,69,.85); }
    .prod-add { position: absolute; bottom: 8px; right: 8px; width: 30px; height: 30px; border-radius: 50%; background: #5d87ff; color: #fff; display: flex; align-items: center; justify-content: center; box-shadow: 0 3px 8px rgba(93,135,255,.5); }
    .prod-body { padding: 10px 12px 12px; }
    .prod-name { font-weight: 600; font-size: 13px; line-height: 1.3; margin: 0 0 5px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 34px; color: #2a3547; }
    .prod-price { color: #5d87ff; font-weight: 700; font-size: 14px; }
    .prod-price small { color: #99a1b7; font-weight: 500; font-size: 11px; }
    .prod-card.is-loading { pointer-events: none; }
    .prod-card.is-loading::after { content: ''; position: absolute; inset: 0; background: rgba(255,255,255,.55); z-index: 1; }
    .prod-card.is-loading::before { content: ''; position: absolute; top: 40%; left: 50%; width: 26px; height: 26px; margin: -13px; border: 3px solid #d5ddf0; border-top-color: #5d87ff; border-radius: 50%; animation: kspin .7s linear infinite; z-index: 2; }
    @keyframes kspin { to { transform: rotate(360deg); } }

    /* ===== SKELETON ===== */
    .skel { background: linear-gradient(90deg,#eef0f5 25%,#f7f8fb 37%,#eef0f5 63%); background-size: 400% 100%; animation: kshim 1.4s ease infinite; border-radius: 6px; }
    .skel-img { aspect-ratio: 1 / 1; border-radius: 0; }
    .skel-line { height: 12px; margin-bottom: 7px; }
    @keyframes kshim { 0% { background-position: 100% 0; } 100% { background-position: -100% 0; } }

    /* ===== FAB ===== */
    .fab-cart { position: fixed; bottom: 24px; right: 24px; z-index: 1045; border-radius: 40px; padding: 12px 22px; box-shadow: 0 10px 26px rgba(93,135,255,.5); }
    .cart-thumb { width: 42px; height: 42px; object-fit: cover; border-radius: 8px; background: #f1f1f1; flex-shrink: 0; }
    .btn-quick-pay { min-width: 90px; font-weight: 600; }
</style>

{{-- Autocomplete pelanggan (custom, dark/light aware) — drop-up biar aman dari keyboard --}}
<style>
    .ac-suggest { position: absolute; left: 0; right: 0; bottom: calc(100% + 5px); z-index: 1060; background: #fff; border: 1px solid #e5e9f2; border-radius: 12px; box-shadow: 0 10px 28px rgba(19,30,55,.15); max-height: 224px; overflow-y: auto; padding: 5px; }
    .ac-item { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 9px 11px; border-radius: 8px; cursor: pointer; font-size: 14px; color: #2a3547; }
    .ac-item + .ac-item { margin-top: 2px; }
    .ac-item:hover, .ac-item.active { background: #f2f5ff; }
    .ac-item .ac-name { font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .ac-item .ac-telp { color: #99a1b7; font-size: 12px; flex-shrink: 0; }
    .ac-empty { padding: 12px; text-align: center; color: #99a1b7; font-size: 13px; }
    [data-bs-theme="dark"] .ac-suggest { background: #232733; border-color: #2e3440; box-shadow: 0 10px 28px rgba(0,0,0,.55); }
    [data-bs-theme="dark"] .ac-item { color: #e6e9f0; }
    [data-bs-theme="dark"] .ac-item:hover, [data-bs-theme="dark"] .ac-item.active { background: #2b3450; }
    [data-bs-theme="dark"] .ac-item .ac-telp { color: #8891a5; }
    [data-bs-theme="dark"] .ac-empty { color: #8891a5; }
</style>

{{-- MAIN --}}
<div class="container-fluid">
    {{-- TOOLBAR KASIR (judul + barcode + cari) --}}
    <div class="card overflow-hidden">
        <div class="progress" style="height:3px; border-radius:0;">
            <div class="progress-bar progress-bar-striped bg-primary progress-bar-animated animasiProgressBar" style="width:0%"></div>
        </div>
        <div class="card-body py-3">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    <i class="ti ti-cash-register text-primary" style="font-size:26px; line-height:1;"></i>
                    <h4 class="fw-semibold mb-0">Kasir</h4>
                </div>
                <div class="d-flex gap-2 flex-grow-1 flex-wrap" style="min-width:240px;">
                    <div class="input-group input-group-lg" style="flex:1 1 220px;">
                        <span class="input-group-text bg-primary text-white"><i class="ti ti-barcode"></i></span>
                        <input type="text" class="form-control" id="inputBarcode" placeholder="Scan / ketik barcode.." autocomplete="off">
                    </div>
                    <div class="input-group input-group-lg" style="flex:1 1 220px;">
                        <span class="input-group-text bg-white"><i class="ti ti-search"></i></span>
                        <input type="search" class="form-control" id="cariProduk" placeholder="Cari produk.." autocomplete="off">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- GRID PRODUK --}}
    <div class="row g-3" id="produkGrid"></div>
    <div id="produkSentinel" style="height:1px;"></div>
    <div class="text-center py-3" id="produkLoading" style="display:none;">
        <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
    </div>
</div>

{{-- FAST HAND: panel pintasan keyboard (context-aware, menutup sidebar saat aktif) --}}
<style>
    #fastHandPanel { position: fixed; top: 0; left: 0; width: 270px; height: 100vh; z-index: 1052; background: #fff; border-right: 1px solid #eaeef4; box-shadow: 6px 0 28px rgba(19,30,55,.12); transform: translateX(-100%); transition: transform .22s ease; overflow-y: auto; padding: 18px 16px 26px; }
    #fastHandPanel.open { transform: translateX(0); }
    #fastHandPanel .fh-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 3px; }
    #fastHandPanel .fh-title { font-weight: 700; font-size: 18px; color: #2a3547; display: flex; align-items: center; gap: 7px; }
    #fastHandPanel .fh-title i { color: #ffb800; }
    #fastHandPanel .fh-x { border: 0; background: transparent; color: #99a1b7; font-size: 20px; line-height: 1; cursor: pointer; padding: 4px; border-radius: 8px; }
    #fastHandPanel .fh-x:hover { background: #f2f5ff; color: #2a3547; }
    #fastHandPanel .fh-sub { font-size: 12px; color: #99a1b7; margin-bottom: 14px; }
    #fastHandPanel .fh-sub span { color: #5d87ff; font-weight: 600; }
    #fastHandPanel .fh-row { display: flex; align-items: center; gap: 10px; padding: 9px 0; border-bottom: 1px dashed #eef1f6; }
    #fastHandPanel .fh-keys { display: flex; align-items: center; gap: 4px; flex-shrink: 0; }
    #fastHandPanel .fh-label { font-size: 13px; color: #2a3547; }
    #fastHandPanel .fh-foot { margin-top: 16px; font-size: 11px; color: #99a1b7; display: flex; align-items: center; gap: 4px; flex-wrap: wrap; }
    .fh-kbd { display: inline-block; min-width: 22px; text-align: center; padding: 2px 7px; font-size: 12px; font-weight: 600; font-family: inherit; color: #2a3547; background: #f4f6fb; border: 1px solid #dfe4ee; border-bottom-width: 2px; border-radius: 6px; line-height: 1.4; }
    .fh-plus { color: #b6bcc9; font-size: 11px; }
    [data-bs-theme="dark"] #fastHandPanel { background: #232733; border-right-color: #2e3440; box-shadow: 6px 0 28px rgba(0,0,0,.5); }
    [data-bs-theme="dark"] #fastHandPanel .fh-title { color: #e6e9f0; }
    [data-bs-theme="dark"] #fastHandPanel .fh-x:hover { background: #2b3450; color: #e6e9f0; }
    [data-bs-theme="dark"] #fastHandPanel .fh-row { border-bottom-color: #2e3440; }
    [data-bs-theme="dark"] #fastHandPanel .fh-label { color: #e6e9f0; }
    [data-bs-theme="dark"] .fh-kbd { background: #1b1f2a; border-color: #3a4152; color: #e6e9f0; }
    /* highlight border item keranjang yang difokus & item terpilih di pembayaran */
    .fh-target { outline: 2px solid #5d87ff; outline-offset: 2px; border-radius: 8px; background: #f2f6ff; }
    .fh-hl { outline: 2px solid #5d87ff !important; outline-offset: 2px; }
    /* status dinamis di panel (item/mode yang sedang difokus) */
    #fastHandPanel .fh-status { display: none; margin-bottom: 12px; padding: 10px 11px; border-radius: 9px; background: #eef3ff; border: 1px solid #d7e2ff; font-size: 13px; color: #2a3547; }
    #fastHandPanel .fh-status.show { display: block; }
    #fastHandPanel .fh-status b { color: #3f6bff; }
    #fastHandPanel .fh-status .fh-status-q { font-size: 12px; color: #6b7385; }
    [data-bs-theme="dark"] .fh-target { background: #23314f; }
    [data-bs-theme="dark"] #fastHandPanel .fh-status { background: #23314f; border-color: #2f4372; color: #e6e9f0; }
    [data-bs-theme="dark"] #fastHandPanel .fh-status .fh-status-q { color: #9aa3b8; }
    /* saat panel Keyboard Cepat terbuka, geser modal ke kanan agar tak tertutup panel & enak dilihat */
    body.fh-panel-open .modal { padding-left: 270px !important; }
    @media (max-width: 767px) {
        #fastHandPanel { width: 84%; max-width: 320px; }
        body.fh-panel-open .modal { padding-left: 0 !important; }
    }
</style>
<div id="fastHandPanel" aria-hidden="true">
    <div class="fh-head">
        <div class="fh-title"><i class="ti ti-keyboard"></i> Keyboard Cepat</div>
        <button type="button" class="fh-x" id="fhClose" aria-label="Tutup"><i class="ti ti-x"></i></button>
    </div>
    <div class="fh-sub">Pintasan: <span id="fhStateName">Layar Kasir</span></div>
    <div id="fhStatus" class="fh-status"></div>
    <div id="fhRows"></div>
    <div class="fh-foot"><kbd class="fh-kbd">Shift</kbd><span class="fh-plus">+</span><kbd class="fh-kbd">H</kbd> &nbsp;buka / tutup panel</div>
</div>

{{-- FLOATING KERANJANG --}}
<button type="button" class="btn btn-primary fab-cart btn-lg" id="fabKeranjang" style="display:none;">
    <i class="ti ti-shopping-cart fs-5"></i>
    <span class="badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle" id="fabCount">0</span>
    <span class="ms-2 fw-bold" id="fabTotal">Rp 0</span>
</button>

{{-- MODAL PILIH SATUAN --}}
<div class="modal fade" id="ModalPilihSatuan" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header d-flex align-items-center gap-2">
                <img id="psGambar" src="" class="cart-thumb" style="display:none;">
                <h4 class="modal-title mb-0" id="psNama">Barang</h4>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="mb-1"><b>Jumlah Beli</b></label>
                <div class="input-group input-group-sm mb-3" style="max-width:160px;">
                    <button class="btn border border-dark text-dark" type="button" id="psMinus"><i class="ti ti-minus"></i></button>
                    <input type="text" class="form-control text-center fw-semibold fs-3" id="psQty" value="1" step="0.01">
                    <button class="btn border border-dark text-dark" type="button" id="psPlus"><i class="ti ti-plus"></i></button>
                </div>
                <label class="mb-1"><b>Pilih Satuan</b></label>
                <div id="psSatuanList" class="mb-3"></div>
                <div class="border rounded p-3 bg-light">
                    <div class="d-flex justify-content-between"><span class="text-muted">Total</span><h4 class="mb-0 fw-bold text-danger" id="psTotal">Rp 0</h4></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnTambahKeKeranjang"><i class="ti ti-shopping-cart-plus"></i>&nbsp; Tambah</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL KERANJANG --}}
<div class="modal fade" id="ModalKeranjang" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="ti ti-shopping-cart"></i>&nbsp; Keranjang</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body"><div id="cartItems"></div></div>
            <div class="modal-footer d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted fs-2">Total</span>
                    <h3 class="mb-0 fw-bold text-danger" id="cartTotal">Rp 0</h3>
                </div>
                <div>
                    <button type="button" class="btn btn-outline-danger" id="btnKosongkanKeranjang" title="Kosongkan"><i class="ti ti-trash"></i></button>
                    <button type="button" class="btn btn-primary btn-lg fw-bold" id="btnLanjutBayar"><i class="ti ti-cash"></i>&nbsp; Lanjut Bayar</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL BAYAR --}}
<div class="modal fade" id="ModalBayar" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="ti ti-cash"></i>&nbsp; Pembayaran</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    {{-- KOLOM KIRI: tagihan & diskon --}}
                    <div class="col-12 col-md-6">
                        <div class="p-3 rounded mb-3 bayar-box-total">
                            <div class="d-flex justify-content-between align-items-center"><span class="fs-3">Subtotal</span><span class="fw-semibold fs-4" id="bayarSubtotal">Rp 0</span></div>
                            <div class="d-none justify-content-between align-items-center mt-1" id="bayarDiskonRow"><span class="fs-3">Diskon <small class="text-muted" id="bayarDiskonNama"></small></span><span class="fw-semibold fs-4 text-success" id="bayarDiskonNilai">- Rp 0</span></div>
                            <hr class="my-2" style="opacity:.35;">
                            <div class="d-flex justify-content-between align-items-center"><span class="fs-3 fw-semibold">Total Bayar</span><h2 class="mb-0 fw-bold text-danger" id="bayarTotal">Rp 0</h2></div>
                        </div>
                        <div id="diskonWrap" hidden>
                            <label class="mb-1 fs-3"><b>Diskon</b> <span class="text-muted">(klik untuk pakai)</span></label>
                            <div id="diskonBadges" class="d-flex flex-wrap gap-2"></div>
                        </div>
                    </div>
                    {{-- KOLOM KANAN: pembayaran, kembalian & pelanggan --}}
                    <div class="col-12 col-md-6">
                        <label class="mb-1 fs-3"><b>Uang Bayar</b></label>
                        <div id="quickPay" class="d-flex flex-wrap gap-2 mb-2"></div>
                        <input type="text" inputmode="numeric" class="form-control form-control-lg input-rupiah summaryTotalBayar mb-3" placeholder="Nominal bayar ..">
                        <div class="p-3 rounded mb-3 bayar-box-kembali">
                            <p class="mb-0 fs-3">Kembalian</p>
                            <h3 class="mb-0 fw-bold text-success summaryKembalian" data-kembalian="0">Rp 0</h3>
                        </div>
                        <label class="mb-1 fs-3"><b>Pelanggan</b> <span class="text-muted">(opsional)</span></label>
                        <div class="d-flex gap-2 align-items-start">
                            <div class="flex-grow-1 position-relative" id="pembeliWrap" style="min-width:0;">
                                <input type="text" class="form-control" id="inputPembeli" placeholder="Umum / ketik nama pelanggan.." autocomplete="off" spellcheck="false" role="combobox" aria-autocomplete="list" aria-expanded="false">
                                <div id="pembeliSuggest" class="ac-suggest" hidden></div>
                            </div>
                            <button type="button" class="btn btn-outline-primary flex-shrink-0 d-flex align-items-center justify-content-center" id="btnAddPelanggan" title="Tambah pelanggan baru" style="height:38px; width:42px;"><i class="ti ti-user-plus"></i></button>
                        </div>
                        <small class="text-muted">Nama harus sesuai data <b>Kelola Pelanggan</b>.</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary btn-lg fw-bold" id="btnKonfirmasiBayar"><i class="ti ti-receipt"></i>&nbsp; Konfirmasi &amp; Cetak</button>
            </div>
        </div>
    </div>
</div>

<script>
    function escapeHtml(t){ if(t==null) return ''; return String(t).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;'); }
    function formatRupiah(a){ a = Math.floor(a || 0); return 'Rp ' + a.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'); }
    function animasiProgressBar_run() { $('.animasiProgressBar').css('width', '100%'); }
    function animasiProgressBar_stop() { $('.animasiProgressBar').css('width', '0%'); }

    // ================= GRID PRODUK (hybrid: tarik chunk 500, render di client) =================
    var PER_LOAD = 500, RENDER_BATCH = 40;
    var allProducts = [], filtered = [], renderIdx = 0, prodQuery = '', prodTimer = null, loadingDone = false, fetching = false;
    var prodById = {}, prodByBarcode = {};

    function skeletonCards(n) {
        var s = '';
        for (var i = 0; i < n; i++) {
            s += `<div class="col-6 col-sm-4 col-md-4 col-lg-3 col-xl-2">
                <div class="prod-card"><div class="skel skel-img"></div>
                <div class="prod-body"><div class="skel skel-line" style="width:85%"></div><div class="skel skel-line" style="width:50%"></div></div></div></div>`;
        }
        return s;
    }
    function cardHTML(p) {
        var img = p.gambar_url ? `<img src="${p.gambar_url}" loading="lazy">` : `<div class="prod-noimg"><i class="ti ti-photo"></i></div>`;
        var harga = p.harga_mulai > 0 ? `<small>mulai</small> ${formatRupiah(p.harga_mulai)}` : 'Harga -';
        var stokBadge = p.stok > 0 ? `<span class="prod-stok" title="Stok"><i class="ti ti-stack-2" style="font-size:13px; vertical-align:-2px;"></i> ${p.stok}</span>` : '';
        return `<div class="col-6 col-sm-4 col-md-4 col-lg-3 col-xl-2">
            <div class="prod-card h-100" data-id="${p.id}" data-nama="${escapeHtml(p.nama_barang)}" data-img="${p.gambar_url || ''}">
                <div class="prod-img-wrap">
                    ${img}
                    ${stokBadge}
                    <span class="prod-add"><i class="ti ti-plus"></i></span>
                </div>
                <div class="prod-body">
                    <div class="prod-name" title="${escapeHtml(p.nama_barang)}">${escapeHtml(p.nama_barang)}</div>
                    <div class="prod-price">${harga}</div>
                </div>
            </div>
        </div>`;
    }

    function renderMore() {
        if (renderIdx >= filtered.length) return;
        var slice = filtered.slice(renderIdx, renderIdx + RENDER_BATCH), html = '';
        slice.forEach(function (p) { html += cardHTML(p); });
        $('#produkGrid').append(html);
        renderIdx += slice.length;
    }
    function applyFilter() {
        var q = prodQuery.toLowerCase().trim();
        filtered = q ? allProducts.filter(function (p) { return (p.nama_barang || '').toLowerCase().indexOf(q) !== -1; }) : allProducts;
        renderIdx = 0;
        $('#produkGrid').html('');
        if (!filtered.length) {
            $('#produkGrid').html('<div class="col-12 text-center text-muted py-5"><i class="ti ti-package-off fs-8 d-block mb-2"></i>' + (loadingDone ? 'Produk tidak ditemukan' : 'Memuat produk..') + '</div>');
            return;
        }
        renderMore();
    }
    function fetchChunk(page) {
        if (fetching) return;
        fetching = true;
        if (page === 1) { $('#produkGrid').html(skeletonCards(12)); $('#produkLoading').show(); }
        $.ajax({
            type: "GET", url: "{{ route('kasir_produk') }}", data: { page: page, per: PER_LOAD }, dataType: "JSON",
            success: function (res) {
                allProducts = allProducts.concat(res.data);
                res.data.forEach(function (p) { prodById[p.id] = p; if (p.barcode) prodByBarcode[String(p.barcode)] = p; });
                fetching = false;
                if (page === 1 || prodQuery.trim()) { applyFilter(); }
                else { filtered = allProducts; } // grid nambah otomatis saat scroll
                if (res.hasMore) { fetchChunk(page + 1); }
                else { loadingDone = true; $('#produkLoading').hide(); if (!filtered.length) applyFilter(); }
            },
            error: function () { fetching = false; $('#produkLoading').hide(); toastError("Gagal memuat produk!"); }
        });
    }
    function reloadProduk() {
        allProducts = []; filtered = []; renderIdx = 0; loadingDone = false; fetching = false;
        prodById = {}; prodByBarcode = {};
        fetchChunk(1);
    }

    // search: filter di client (instan, tanpa AJAX)
    $('#cariProduk').on('input', function () {
        clearTimeout(prodTimer); prodQuery = $(this).val();
        prodTimer = setTimeout(applyFilter, 200);
    });
    // render bertahap saat scroll (dari memori, tanpa AJAX)
    var io = new IntersectionObserver(function (e) { if (e[0].isIntersecting) renderMore(); }, { rootMargin: '350px' });
    $(document).ready(function () {
        fetchChunk(1);
        io.observe(document.getElementById('produkSentinel'));
        document.getElementById('inputBarcode').focus();
    });

    // ================= BARCODE (auto, tanpa Enter) =================
    var barcodeTimer = null, barcodeBusy = false;
    function cariBarcode(code) {
        code = (code || '').trim();
        if (!code || barcodeBusy) return;
        $('#inputBarcode').val('');
        // cari di memori dulu (instan)
        var p = prodByBarcode[code];
        if (p && p.satuan) {
            prosesBarang(p.id, p.nama_barang, p.gambar_url, p.satuan);
            if (!$('.modal.show').length) document.getElementById('inputBarcode').focus();
            return;
        }
        // fallback server (produk baru yang belum termuat di memori)
        barcodeBusy = true;
        animasiProgressBar_run();
        $.ajax({
            type: "GET", url: "{{ route('kasir_barcode') }}", data: { code: code }, dataType: "JSON",
            success: function (r) {
                if (r.kode != 200) { toastError(r.pesan); return; }
                prosesBarang(r.id, r.nama_barang, r.gambar_url, r.satuan);
            },
            error: function () { toastError("Oops! Silahkan coba lagi!"); },
            complete: function () {
                barcodeBusy = false;
                animasiProgressBar_stop();
                // re-fokus hanya bila tak ada modal terbuka (biar tak curi fokus modal satuan)
                if (!$('.modal.show').length) { document.getElementById('inputBarcode').focus(); }
            }
        });
    }
    // auto: cari 300ms setelah berhenti mengetik / selesai scan
    $('#inputBarcode').on('input', function () {
        clearTimeout(barcodeTimer);
        var val = $(this).val();
        barcodeTimer = setTimeout(function () { cariBarcode(val); }, 300);
    });
    // Enter = cari langsung (fallback scanner yang kirim Enter)
    $('#inputBarcode').on('keydown', function (e) {
        if (e.key !== 'Enter') return;
        e.preventDefault();
        clearTimeout(barcodeTimer);
        cariBarcode($(this).val());
    });

    // ================= PILIH SATUAN / AUTO-ADD =================
    var curProd = { id: null, nama: '', img: '' };

    function prosesBarang(id, nama, img, satuan) {
        // satu varian -> langsung masuk keranjang tanpa modal
        if (satuan.length === 1) {
            var s = satuan[0];
            addToCart({ id_barang: id, nama_barang: nama, gambar_url: img, satuan: s.nama_satuan, harga: parseInt(s.harga) || 0, konversi: parseInt(s.konversi) || 1, qty: 1 });
            toastSuccess(nama + ' ditambahkan');
            return;
        }
        curProd = { id: id, nama: nama, img: img };
        var html = '';
        satuan.forEach(function (s, idx) {
            var kon = s.konversi > 1 ? ' <small>(×' + s.konversi + ')</small>' : '';
            html += `<input type="radio" class="btn-check ps-harga" name="psSatuan" id="ps${idx}" data-satuan="${escapeHtml(s.nama_satuan)}" data-harga="${s.harga}" data-konversi="${s.konversi}" ${idx === 0 ? 'checked' : ''}>
                <label class="btn btn-outline-primary rounded-pill font-medium me-2 mb-2" for="ps${idx}"><b>${escapeHtml(s.nama_satuan)}</b> - ${formatRupiah(s.harga)}${kon}</label>`;
        });
        $('#psSatuanList').html(html);
        $('#psNama').text(nama);
        if (img) { $('#psGambar').attr('src', img).show(); } else { $('#psGambar').hide(); }
        $('#psQty').val(1); hitungPsTotal();
        $('#ModalPilihSatuan').modal('show');
    }

    $(document).on('click', '.prod-card', function () {
        var $card = $(this);
        if ($card.hasClass('is-loading')) return;
        var id = $card.data('id'), nama = $card.data('nama'), img = $card.data('img');
        // pakai data di memori (tanpa AJAX)
        var p = prodById[id];
        if (p && p.satuan) { prosesBarang(id, nama, img, p.satuan); return; }
        // fallback (jaga-jaga) via AJAX
        $card.addClass('is-loading');
        $.ajax({
            type: "GET", url: "{{ route('detail_data_barang') }}", data: { id: id }, dataType: "JSON",
            success: function (r) { if (r.kode != 200) { toastError(r.pesan); return; } prosesBarang(id, nama, img, r.satuan); },
            error: function () { toastError("Oops! Silahkan coba lagi!"); },
            complete: function () { $card.removeClass('is-loading'); }
        });
    });

    function hitungPsTotal() {
        var qty = parseFloat($('#psQty').val()) || 0;
        var harga = parseInt($('.ps-harga:checked').data('harga')) || 0;
        $('#psTotal').text(formatRupiah(qty * harga));
    }
    $(document).on('click', '#psMinus', function () { var v = parseFloat($('#psQty').val()) || 0; $('#psQty').val(v > 1 ? v - 1 : 1); hitungPsTotal(); });
    $(document).on('click', '#psPlus', function () { var v = parseFloat($('#psQty').val()) || 0; $('#psQty').val(v + 1); hitungPsTotal(); });
    $(document).on('input', '#psQty', hitungPsTotal);
    $(document).on('change', '.ps-harga', hitungPsTotal);
    $(document).on('click', '#btnTambahKeKeranjang', function () {
        var $s = $('.ps-harga:checked');
        if (!$s.length) return toastError("Pilih satuan dulu!");
        var qty = parseFloat($('#psQty').val()) || 0;
        if (qty <= 0) return toastError("Jumlah beli tidak valid!");
        addToCart({ id_barang: curProd.id, nama_barang: curProd.nama, gambar_url: curProd.img, satuan: $s.data('satuan'), harga: parseInt($s.data('harga')) || 0, konversi: parseInt($s.data('konversi')) || 1, qty: qty });
        $('#ModalPilihSatuan').modal('hide');
        toastSuccess("Ditambahkan ke keranjang");
    });

    // ================= KERANJANG =================
    var cart = [];
    function cartKey(id, satuan) { return id + '||' + satuan; }
    function cartTotal() { return cart.reduce(function (s, c) { return s + c.harga * c.qty; }, 0); }
    function cartCount() { return cart.reduce(function (s, c) { return s + parseFloat(c.qty); }, 0); }
    function addToCart(item) {
        var key = cartKey(item.id_barang, item.satuan);
        var ex = cart.find(function (c) { return cartKey(c.id_barang, c.satuan) === key; });
        if (ex) { ex.qty = parseFloat(ex.qty) + parseFloat(item.qty); } else { cart.push(item); }
        updateFab(); renderCart();
    }
    function updateFab() {
        $('#fabCount').text(cartCount());
        $('#fabTotal').text(formatRupiah(cartTotal()));
        $('#fabKeranjang').toggle(cart.length > 0);
    }
    function renderCart() {
        var box = $('#cartItems');
        if (!cart.length) {
            box.html('<div class="text-center text-muted py-5"><i class="ti ti-basket-off fs-8 d-block mb-2"></i>Keranjang kosong</div>');
            $('#cartTotal').text('Rp 0'); return;
        }
        var html = '';
        cart.forEach(function (c, i) {
            var thumb = c.gambar_url ? `<img src="${c.gambar_url}" class="cart-thumb">` : `<span class="cart-thumb d-inline-flex align-items-center justify-content-center text-muted"><i class="ti ti-photo"></i></span>`;
            html += `<div class="d-flex align-items-center gap-2 border-bottom py-2 cart-row" data-key="${escapeHtml(cartKey(c.id_barang, c.satuan))}">
                ${thumb}
                <div class="flex-grow-1" style="min-width:0;">
                    <div class="fw-semibold text-truncate">${escapeHtml(c.nama_barang)}</div>
                    <span class="badge bg-light-primary text-primary">${escapeHtml(c.satuan)}</span>
                    <span class="text-muted fs-2">@ ${formatRupiah(c.harga)}</span>
                </div>
                <div class="input-group input-group-sm" style="width:112px;">
                    <button class="btn border border-dark text-dark cart-minus" type="button" data-i="${i}"><i class="ti ti-minus"></i></button>
                    <input type="text" class="form-control text-center fw-semibold cart-qty" data-i="${i}" value="${c.qty}">
                    <button class="btn border border-dark text-dark cart-plus" type="button" data-i="${i}"><i class="ti ti-plus"></i></button>
                </div>
                <div class="text-end fw-bold" style="width:92px;">${formatRupiah(c.harga * c.qty)}</div>
                <a href="javascript:void(0)" class="text-danger cart-del" data-i="${i}"><i class="ti ti-trash fs-4"></i></a>
            </div>`;
        });
        box.html(html);
        $('#cartTotal').text(formatRupiah(cartTotal()));
        if (typeof applyCartTarget === 'function') { applyCartTarget(); } // pertahankan highlight target Shortcut
    }
    $(document).on('click', '#fabKeranjang', function () { renderCart(); $('#ModalKeranjang').modal('show'); });
    $(document).on('click', '.cart-minus', function () { var i = $(this).data('i'); cart[i].qty = Math.max(1, parseFloat(cart[i].qty) - 1); renderCart(); updateFab(); });
    $(document).on('click', '.cart-plus', function () { var i = $(this).data('i'); cart[i].qty = parseFloat(cart[i].qty) + 1; renderCart(); updateFab(); });
    $(document).on('input', '.cart-qty', function () { var i = $(this).data('i'); var v = parseFloat($(this).val()); cart[i].qty = (isNaN(v) || v < 1) ? 1 : v; updateFab(); $('#cartTotal').text(formatRupiah(cartTotal())); });
    $(document).on('click', '.cart-del', function () { var i = $(this).data('i'); cart.splice(i, 1); renderCart(); updateFab(); if (!cart.length) $('#ModalKeranjang').modal('hide'); });
    // kosongkan tanpa konfirmasi
    $(document).on('click', '#btnKosongkanKeranjang', function () { cart = []; renderCart(); updateFab(); $('#ModalKeranjang').modal('hide'); });

    // ================= BAYAR =================
    var activeDiskon = [];       // daftar diskon aktif dari server
    var selectedDiskon = null;   // diskon terpilih {id,nama,tipe,nilai}

    function loadDiskonAktif() {
        $.ajax({
            type: 'GET', url: "{{ route('diskon_aktif') }}", dataType: 'JSON',
            success: function (r) { activeDiskon = r.data || []; },
            error: function () { activeDiskon = []; }
        });
    }
    $(document).ready(loadDiskonAktif);

    function diskonPotongan(subtotal) {
        if (!selectedDiskon) return 0;
        var p = selectedDiskon.tipe === 'persen'
            ? Math.floor(subtotal * parseInt(selectedDiskon.nilai) / 100)
            : parseInt(selectedDiskon.nilai);
        return Math.min(Math.max(0, p), subtotal);
    }
    function grandTotal() { var s = cartTotal(); return s - diskonPotongan(s); }

    function renderDiskonBadges() {
        var wrap = $('#diskonWrap'), box = $('#diskonBadges');
        if (!activeDiskon.length) { wrap.attr('hidden', true); box.html(''); return; }
        wrap.removeAttr('hidden');
        var html = '';
        activeDiskon.forEach(function (d) {
            var label = escapeHtml(d.nama) + ' (' + (d.tipe === 'persen' ? d.nilai + '%' : formatRupiah(d.nilai)) + ')';
            var sel = selectedDiskon && selectedDiskon.id === d.id;
            html += `<button type="button" class="btn ${sel ? 'btn-primary' : 'btn-outline-primary'} btn-sm badge-diskon" data-id="${d.id}"><i class="ti ti-discount-2"></i> ${label}</button>`;
        });
        box.html(html);
    }
    $(document).on('click', '.badge-diskon', function () {
        var id = $(this).data('id');
        if (selectedDiskon && selectedDiskon.id === id) { selectedDiskon = null; }
        else { selectedDiskon = activeDiskon.find(function (d) { return d.id === id; }) || null; }
        renderDiskonBadges();
        refreshBayar();
    });

    function refreshBayar() {
        var sub = cartTotal();
        var pot = diskonPotongan(sub);
        var tot = sub - pot;
        $('#bayarSubtotal').text(formatRupiah(sub));
        if (pot > 0 && selectedDiskon) {
            $('#bayarDiskonRow').removeClass('d-none').addClass('d-flex');
            $('#bayarDiskonNama').text(selectedDiskon.tipe === 'persen' ? '(' + selectedDiskon.nilai + '%)' : '');
            $('#bayarDiskonNilai').text('- ' + formatRupiah(pot));
        } else {
            $('#bayarDiskonRow').removeClass('d-flex').addClass('d-none');
        }
        $('#bayarTotal').text(formatRupiah(tot));
        renderQuickPay(tot);
        hitungKembalian();
    }

    $(document).on('click', '#btnLanjutBayar', function () {
        if (!cart.length) return toastError("Keranjang kosong!");
        $('#ModalKeranjang').modal('hide');
        selectedDiskon = null;
        $('.summaryTotalBayar').val('');
        $('.summaryKembalian').text('Rp 0').attr('data-kembalian', 0);
        $('#inputPembeli').val(''); hidePelSuggest();
        renderDiskonBadges();
        refreshBayar();
        $('#ModalBayar').modal('show');
    });
    function renderQuickPay(total) {
        total = parseInt(total) || 0;
        var wrap = $('#quickPay');
        if (total <= 0) { wrap.html(''); return; }
        var opts = {}; opts[total] = true;
        [1000, 5000, 10000, 25000, 50000, 100000].forEach(function (s) { opts[Math.ceil(total / s) * s] = true; });
        [5000, 10000, 20000, 50000, 100000].forEach(function (b) { if (b >= total) opts[b] = true; });
        var arr = Object.keys(opts).map(Number).filter(function (v) { return v >= total; }).sort(function (a, b) { return a - b; }).slice(0, 6);
        var html = '';
        arr.forEach(function (v) {
            var isPas = (v === total);
            html += `<button type="button" class="btn ${isPas ? 'btn-outline-success' : 'btn-outline-primary'} btn-quick-pay flex-grow-1" data-nominal="${v}">${isPas ? 'Uang Pas' : formatRupiah(v)}</button>`;
        });
        wrap.html(html);
    }
    $(document).on('click', '.btn-quick-pay', function () {
        $('.summaryTotalBayar').val(formatRupiah($(this).data('nominal')));
        hitungKembalian();
        $('.btn-quick-pay').removeClass('active'); $(this).addClass('active');
    });
    function hitungKembalian() {
        var kembalian = rupiahToInt($('.summaryTotalBayar').val()) - grandTotal();
        if (kembalian < 0) kembalian = 0;
        $('.summaryKembalian').text(formatRupiah(kembalian)).attr('data-kembalian', kembalian);
    }
    $(document).on('input', '.summaryTotalBayar', function () { $('.btn-quick-pay').removeClass('active'); hitungKembalian(); });

    $(document).on('click', '#btnKonfirmasiBayar', function () {
        if (!cart.length) return toastError("Keranjang kosong!");
        var total = grandTotal();
        var bayar = rupiahToInt($('.summaryTotalBayar').val());
        if (bayar < 1) return toastError("Uang bayar kosong!");
        if (bayar < total) return toastError("Uang bayar kurang dari total!");

        // validasi pelanggan: kosong = Umum; jika diisi harus PERSIS nama pelanggan terdaftar
        var namaPembeli = $('#inputPembeli').val().trim();
        resolvePelanggan(namaPembeli, function (ok, id_pelanggan, pembeli) {
            if (!ok) {
                return toastError('Nama pelanggan "' + namaPembeli + '" tidak terdaftar. Pilih dari saran atau tambah lewat tombol +.');
            }
            var id_diskon = selectedDiskon ? selectedDiskon.id : '';
            var dynamicTabelBelanja = cart.map(function (c) {
                return { id_barang: c.id_barang, nama_barang: c.nama_barang, satuan: c.satuan, total_qty: c.qty, harga: c.harga, total_harga: c.harga * c.qty };
            });
            customConfirm("Konfirmasi ?", "Pastikan pembayaran sudah benar.").then(function (confirmed) {
                if (!confirmed) return;
                var btnDef = $('#btnKonfirmasiBayar').html();
                $('#btnKonfirmasiBayar').html('Memproses ..').attr('disabled', true);
                $.ajax({
                    type: "POST", url: "{{ route('konfirmasi_pembelian_barang') }}",
                    data: { _token: "{{ csrf_token() }}", dynamicTabelBelanja: dynamicTabelBelanja, total_bayar: bayar, pembeli: pembeli, id_pelanggan: id_pelanggan, id_diskon: id_diskon },
                    dataType: "JSON",
                    success: function (response) {
                        if (response.kode == 200) {
                            qzPrintNota(response.raw_data);
                            cart = []; updateFab(); renderCart();
                            $('#ModalBayar').modal('hide');
                            toastSuccess("Transaksi berhasil!");
                            reloadProduk();
                            document.getElementById('inputBarcode').focus();
                        } else { toastError(response.pesan); }
                    },
                    error: function () { toastError("Oops! Terjadi kesalahan. Silahkan coba lagi!"); },
                    complete: function () { $('#btnKonfirmasiBayar').html(btnDef).attr('disabled', false); }
                });
            });
        });
    });
</script>

<script>
    // ================= PELANGGAN (autocomplete custom, drop-up) =================
    var pelTimer = null;
    var pelSuggestItems = [];   // hasil saran saat ini
    var pelActive = -1;         // indeks aktif (navigasi keyboard)
    var pelangganMap = {};      // cache: nama(lowercase) -> {id, nama}

    function renderPelSuggest(items) {
        var box = $('#pembeliSuggest');
        pelSuggestItems = items || [];
        pelActive = -1;
        var html = '';
        if (!pelSuggestItems.length) {
            html = '<div class="ac-empty">Pelanggan tidak ditemukan</div>';
        } else {
            pelSuggestItems.forEach(function (p, i) {
                var telp = p.no_telp ? escapeHtml(p.no_telp) : '';
                html += '<div class="ac-item" data-i="' + i + '">' +
                            '<span class="ac-name">' + escapeHtml(p.nama) + '</span>' +
                            (telp ? '<span class="ac-telp">' + telp + '</span>' : '') +
                        '</div>';
            });
        }
        box.html(html).removeAttr('hidden');
        $('#inputPembeli').attr('aria-expanded', 'true');
    }
    function hidePelSuggest() {
        $('#pembeliSuggest').attr('hidden', true).html('');
        pelSuggestItems = []; pelActive = -1;
        $('#inputPembeli').attr('aria-expanded', 'false');
    }
    function pickPelanggan(p) {
        pelangganMap[p.nama.toLowerCase()] = { id: p.id, nama: p.nama };
        $('#inputPembeli').val(p.nama);
        hidePelSuggest();
    }
    function cariPelanggan(q) {
        $.ajax({
            type: 'GET', url: "{{ route('pelanggan_search') }}",
            data: { q: q, limit: 5 }, dataType: 'JSON',
            success: function (data) {
                var items = data.results || [];
                items.forEach(function (p) { pelangganMap[p.nama.toLowerCase()] = { id: p.id, nama: p.nama }; });
                if ($('#inputPembeli').val().trim() !== '') { renderPelSuggest(items); }
            },
            error: function () { hidePelSuggest(); }
        });
    }

    // validasi pelanggan sebelum konfirmasi (kosong=Umum, isi harus cocok persis)
    function resolvePelanggan(nama, cb) {
        nama = (nama || '').trim();
        if (nama === '') { return cb(true, '', ''); }
        var found = pelangganMap[nama.toLowerCase()];
        if (found) { return cb(true, found.id, found.nama); }
        // fallback: verifikasi ke server (cegah salah tolak saat mengetik cepat)
        $.ajax({
            type: 'GET', url: "{{ route('pelanggan_search') }}",
            data: { q: nama, limit: 5 }, dataType: 'JSON',
            success: function (data) {
                var match = (data.results || []).find(function (p) { return p.nama.toLowerCase() === nama.toLowerCase(); });
                if (match) {
                    pelangganMap[match.nama.toLowerCase()] = { id: match.id, nama: match.nama };
                    cb(true, match.id, match.nama);
                } else { cb(false); }
            },
            error: function () { cb(false); }
        });
    }

    function updatePelActive() {
        $('#pembeliSuggest .ac-item').removeClass('active');
        if (pelActive >= 0) {
            var el = $('#pembeliSuggest .ac-item[data-i="' + pelActive + '"]').addClass('active');
            if (el.length) { el[0].scrollIntoView({ block: 'nearest' }); }
        }
    }

    // ketik -> cari (debounce)
    $(document).on('input', '#inputPembeli', function () {
        var q = $(this).val().trim();
        clearTimeout(pelTimer);
        if (q === '') { hidePelSuggest(); return; }
        pelTimer = setTimeout(function () { cariPelanggan(q); }, 250);
    });
    // fokus lagi & sudah ada teks -> tampilkan saran
    $(document).on('focus', '#inputPembeli', function () {
        var q = $(this).val().trim();
        if (q !== '') { clearTimeout(pelTimer); pelTimer = setTimeout(function () { cariPelanggan(q); }, 150); }
    });
    // navigasi keyboard (drop-up: bawah=turun indeks tetap intuitif)
    $(document).on('keydown', '#inputPembeli', function (e) {
        if (!pelSuggestItems.length) return;
        if (e.key === 'ArrowDown') { e.preventDefault(); pelActive = Math.min(pelActive + 1, pelSuggestItems.length - 1); updatePelActive(); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); pelActive = Math.max(pelActive - 1, 0); updatePelActive(); }
        else if (e.key === 'Enter') { if (pelActive >= 0 && pelSuggestItems[pelActive]) { e.preventDefault(); pickPelanggan(pelSuggestItems[pelActive]); } }
        else if (e.key === 'Escape') { hidePelSuggest(); }
    });
    // klik saran (mousedown supaya menang dari blur)
    $(document).on('mousedown', '#pembeliSuggest .ac-item', function (e) {
        e.preventDefault();
        var i = $(this).data('i');
        if (pelSuggestItems[i]) { pickPelanggan(pelSuggestItems[i]); }
    });
    // tutup saat blur (delay kecil supaya klik/tap saran tetap kebaca)
    $(document).on('blur', '#inputPembeli', function () { setTimeout(hidePelSuggest, 150); });

    // tambah pelanggan cepat langsung dari layar kasir
    $(document).on('click', '#btnAddPelanggan', function () {
        Swal.fire({
            title: 'Pelanggan Baru',
            html: '<input id="swalNama" class="swal2-input" placeholder="Nama (wajib)" autocomplete="off">' +
                  '<input id="swalTelp" class="swal2-input" placeholder="No. telp (opsional)" inputmode="numeric" autocomplete="off" oninput="this.value=this.value.replace(/[^0-9]/g,\'\')">',
            showCancelButton: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            focusConfirm: false,
            preConfirm: function () {
                var nama = document.getElementById('swalNama').value.trim();
                if (!nama) { Swal.showValidationMessage('Nama tidak boleh kosong'); return false; }
                return { nama: nama, no_telp: document.getElementById('swalTelp').value.trim() };
            }
        }).then(function (res) {
            if (!res.isConfirmed) return;
            $.ajax({
                type: 'POST', url: "{{ route('pelanggan_quick') }}",
                data: { _token: "{{ csrf_token() }}", nama: res.value.nama, no_telp: res.value.no_telp },
                dataType: 'JSON',
                success: function (r) {
                    if (r.kode == 200) {
                        // pelanggan baru langsung terpilih di input
                        pelangganMap[res.value.nama.toLowerCase()] = { id: r.id, nama: res.value.nama };
                        $('#inputPembeli').val(res.value.nama);
                        hidePelSuggest();
                        toastSuccess(r.pesan);
                    } else if (r.kode == 422) { errorHandlerToast(r.pesan); }
                    else { toastError(r.pesan); }
                },
                error: function () { toastError("Oops! Silahkan coba lagi!"); }
            });
        });
    });
</script>

<script>
    // ================= FAST HAND (pintasan keyboard, context-aware) =================
    // Isi panel & tombol keyboard menyesuaikan state kasir saat ini.
    var FH = {
        idle: { title: 'Layar Kasir', items: [
            { k: ['Shift', 'S'], label: 'Fokus cari produk' },
            { k: ['Shift', 'Q'], label: 'Fokus scan barcode' },
            { k: ['Shift', 'Enter'], label: 'Tambah produk' },
            { k: ['Shift', 'K'], label: 'Buka keranjang' },
            { k: ['Shift', 'B'], label: 'Langsung ke pembayaran' },
        ]},
        satuan: { title: 'Pilih Satuan', items: [
            { k: ['←', '→'], label: 'Pindah satuan' },
            { k: ['↑', '↓'], label: 'Atur jumlah' },
            { k: ['Enter'], label: 'Masukkan ke keranjang' },
            { k: ['Esc'], label: 'Batal' },
        ]},
        keranjang: { title: 'Keranjang', items: [
            { k: ['A…Z'], label: 'Ketik nama item untuk fokus' },
            { k: ['Backspace'], label: 'Hapus huruf pencarian' },
            { k: ['↑', '↓'], label: 'Tambah / kurangi jumlah' },
            { k: ['Delete'], label: 'Hapus item terfokus' },
            { k: ['Shift', 'Enter'], label: 'Lanjut bayar' },
            { k: ['Esc'], label: 'Reset fokus / tutup' },
        ]},
        bayar: { title: 'Pembayaran', items: [
            { k: ['Shift', 'U'], label: 'Uang bayar (←/→ pilih, Enter)' },
            { k: ['Shift', 'N'], label: 'Ketik nominal manual' },
            { k: ['Shift', 'D'], label: 'Diskon (←/→ pilih, Enter)' },
            { k: ['Shift', 'P'], label: 'Pilih pelanggan' },
            { k: ['Enter'], label: 'Konfirmasi & cetak' },
            { k: ['Esc'], label: 'Keluar mode / batal' },
        ]},
    };
    var fhOpen = false;

    // state fokus keranjang (type-ahead)
    var cartFind = '';
    var cartTargetKey = null;
    // state mode pembayaran
    var bayarMode = null;   // null | 'diskon' | 'uang'
    var bayarNavIdx = -1;

    function fhState() {
        if ($('#ModalBayar').hasClass('show')) return 'bayar';
        if ($('#ModalKeranjang').hasClass('show')) return 'keranjang';
        if ($('#ModalPilihSatuan').hasClass('show')) return 'satuan';
        return 'idle';
    }
    function renderFastHand() {
        var conf = FH[fhState()] || FH.idle;
        $('#fhStateName').text(conf.title);
        var MOD = ['Shift', 'Ctrl', 'Alt'];
        var html = conf.items.map(function (it) {
            // kombinasi (mis. Shift + S) pakai tanda '+'; alternatif (mis. ← →) dipisah spasi
            var isCombo = it.k.length > 1 && MOD.indexOf(it.k[0]) !== -1;
            var sep = isCombo ? '<span class="fh-plus">+</span>' : '';
            var keys = it.k.map(function (k) { return '<kbd class="fh-kbd">' + escapeHtml(k) + '</kbd>'; }).join(sep);
            return '<div class="fh-row"><div class="fh-keys">' + keys + '</div><div class="fh-label">' + escapeHtml(it.label) + '</div></div>';
        }).join('');
        $('#fhRows').html(html);
        updateFhStatus();
    }
    // status dinamis (item keranjang terfokus / mode pembayaran)
    function updateFhStatus() {
        var st = fhState();
        var $s = $('#fhStatus');
        if (st === 'keranjang' && (cartFind || cartTargetKey)) {
            var item = cartTargetKey ? cart.find(function (c) { return cartKey(c.id_barang, c.satuan) === cartTargetKey; }) : null;
            if (item) {
                $s.html('Fokus: <b>' + escapeHtml(item.nama_barang) + '</b> <span class="fh-status-q">(' + escapeHtml(item.satuan) + ' × ' + item.qty + ')</span>').addClass('show');
            } else {
                $s.html('Cari: <b>"' + escapeHtml(cartFind) + '"</b> <span class="fh-status-q">— tak ada yang cocok</span>').addClass('show');
            }
        } else if (st === 'bayar' && bayarMode) {
            var lbl = bayarMode === 'diskon' ? 'Mode Diskon' : 'Mode Uang Bayar';
            $s.html('<b>' + lbl + '</b> <span class="fh-status-q">— ←/→ pilih · Enter pakai · Esc keluar</span>').addClass('show');
        } else {
            $s.removeClass('show').html('');
        }
    }
    function openFastHand() { fhOpen = true; renderFastHand(); $('#fastHandPanel').addClass('open').attr('aria-hidden', 'false'); $('body').addClass('fh-panel-open'); }
    function closeFastHand() { fhOpen = false; $('#fastHandPanel').removeClass('open').attr('aria-hidden', 'true'); $('body').removeClass('fh-panel-open'); }
    function toggleFastHand() { fhOpen ? closeFastHand() : openFastHand(); }

    $(document).on('click', '#btnFastHand', function (e) { e.preventDefault(); toggleFastHand(); });
    $(document).on('click', '#fhClose', function () { closeFastHand(); });
    // panel ikut berubah saat modal buka/tutup; reset state saat modal ditutup
    $('#ModalPilihSatuan, #ModalKeranjang, #ModalBayar').on('shown.bs.modal hidden.bs.modal', function () { if (fhOpen) renderFastHand(); });
    $('#ModalKeranjang').on('shown.bs.modal hidden.bs.modal', function () { fhResetCartTarget(); updateFhStatus(); });
    $('#ModalBayar').on('shown.bs.modal hidden.bs.modal', function () { fhBayarExit(); });

    function fhSwalOpen() { return !!document.querySelector('.swal2-container'); }
    function fhIsText(el) {
        if (!el) return false;
        if (el.isContentEditable) return true;
        if (el.tagName !== 'INPUT' && el.tagName !== 'TEXTAREA') return false;
        var t = (el.type || 'text').toLowerCase();
        return ['text', 'search', 'number', 'tel', 'email', 'password', 'url'].indexOf(t) !== -1;
    }
    function fhSwitchSatuan(dir) {
        var $r = $('.ps-harga');
        if (!$r.length) return;
        var i = $r.index($('.ps-harga:checked'));
        if (i < 0) i = 0;
        var n = ($r.length + i + dir) % $r.length;
        $r.eq(n).prop('checked', true).trigger('change');
    }

    // ---------- KERANJANG: fokus item lewat type-ahead ----------
    function fhResetCartTarget() { cartFind = ''; cartTargetKey = null; $('#cartItems .cart-row').removeClass('fh-target'); }
    function fhCartMatch(buffer) {
        var q = (buffer || '').toLowerCase().trim();
        if (!q) return null;
        var m = cart.find(function (c) { return (c.nama_barang || '').toLowerCase().indexOf(q) !== -1; });
        return m ? cartKey(m.id_barang, m.satuan) : null;
    }
    function fhCartTargetIndex() {
        if (!cartTargetKey) return -1;
        for (var i = 0; i < cart.length; i++) { if (cartKey(cart[i].id_barang, cart[i].satuan) === cartTargetKey) return i; }
        return -1;
    }
    // dipanggil ulang tiap renderCart -> pertahankan border highlight
    function applyCartTarget() {
        $('#cartItems .cart-row').removeClass('fh-target');
        if (!cartTargetKey) return;
        var $row = null;
        $('#cartItems .cart-row').each(function () { if ($(this).attr('data-key') === cartTargetKey) { $row = $(this); return false; } });
        if ($row && $row.length) { $row.addClass('fh-target'); if ($row[0].scrollIntoView) $row[0].scrollIntoView({ block: 'nearest' }); }
        else { cartTargetKey = null; }
    }
    function fhCartType(ch) {
        cartFind += ch;
        cartTargetKey = fhCartMatch(cartFind);
        applyCartTarget(); updateFhStatus();
    }
    function fhCartBackspace() {
        cartFind = cartFind.slice(0, -1);
        cartTargetKey = fhCartMatch(cartFind);
        applyCartTarget(); updateFhStatus();
    }
    function fhCartQty(delta) {
        var i = fhCartTargetIndex();
        if (i < 0) { toastError('Ketik nama item dulu untuk memilih.'); return; }
        cart[i].qty = Math.max(1, parseFloat(cart[i].qty) + delta);
        renderCart(); updateFab(); updateFhStatus();
    }
    function fhCartDelete() {
        var i = fhCartTargetIndex();
        if (i < 0) { toastError('Ketik nama item dulu untuk memilih.'); return; }
        cart.splice(i, 1);
        cartTargetKey = null; cartFind = '';
        renderCart(); updateFab(); updateFhStatus();
        if (!cart.length) { $('#ModalKeranjang').modal('hide'); }
    }

    // ---------- PEMBAYARAN: mode diskon / uang ----------
    function fhBayarItems() {
        if (bayarMode === 'diskon') return $('#diskonBadges .badge-diskon').toArray();
        if (bayarMode === 'uang')   return $('#quickPay .btn-quick-pay').toArray();
        return [];
    }
    function fhBayarHighlight() {
        $('#ModalBayar .fh-hl').removeClass('fh-hl');
        var items = fhBayarItems();
        if (bayarNavIdx >= 0 && items[bayarNavIdx]) {
            $(items[bayarNavIdx]).addClass('fh-hl');
            if (items[bayarNavIdx].scrollIntoView) items[bayarNavIdx].scrollIntoView({ block: 'nearest' });
        }
    }
    function fhBayarEnterMode(mode) {
        bayarMode = mode;
        var items = fhBayarItems();
        if (!items.length) { bayarMode = null; toastError(mode === 'diskon' ? 'Tidak ada diskon aktif.' : 'Nominal belum tersedia.'); return; }
        bayarNavIdx = 0;
        // mulai dari yang sedang terpilih (diskon aktif / tombol "Uang Pas")
        var cur = items.findIndex(function (el) { return $(el).hasClass(mode === 'diskon' ? 'btn-primary' : 'btn-success'); });
        if (cur >= 0) bayarNavIdx = cur;
        fhBayarHighlight(); updateFhStatus();
    }
    function fhBayarNav(dir) {
        var items = fhBayarItems();
        if (!items.length) return;
        bayarNavIdx = bayarNavIdx < 0 ? 0 : (bayarNavIdx + dir + items.length) % items.length;
        fhBayarHighlight();
    }
    function fhBayarActivate() {
        var items = fhBayarItems();
        if (bayarNavIdx >= 0 && items[bayarNavIdx]) { items[bayarNavIdx].click(); }
        fhBayarExit();
    }
    function fhBayarExit() {
        bayarMode = null; bayarNavIdx = -1;
        $('#ModalBayar .fh-hl').removeClass('fh-hl');
        updateFhStatus();
    }

    $(document).on('keydown', function (e) {
        if (fhSwalOpen()) return;             // biarkan dialog konfirmasi menangani sendiri
        if (e.isDefaultPrevented()) return;    // sudah ditangani handler lain (mis. saran pelanggan / barcode)
        var key = (e.key || '').toLowerCase();
        var shift = e.shiftKey;

        // toggle panel dari state mana pun
        if (shift && key === 'h') { e.preventDefault(); toggleFastHand(); return; }

        var st = fhState();

        // Esc menutup panel hanya bila tak ada modal (kalau ada modal, Esc milik modal)
        if (key === 'escape' && fhOpen && st === 'idle') { e.preventDefault(); closeFastHand(); return; }

        if (st === 'idle') {
            if (shift && key === 's') { e.preventDefault(); document.getElementById('cariProduk').focus(); }
            else if (shift && key === 'q') { e.preventDefault(); document.getElementById('inputBarcode').focus(); }
            else if (shift && key === 'enter') {
                // tambah produk hanya bila hasil pencarian tepat 1 (1 varian auto-add, banyak varian buka modal)
                e.preventDefault();
                var q = (document.getElementById('cariProduk').value || '').toLowerCase().trim();
                var list = q ? allProducts.filter(function (p) { return (p.nama_barang || '').toLowerCase().indexOf(q) !== -1; }) : allProducts;
                if (list.length === 1) {
                    var pr = list[0];
                    prosesBarang(pr.id, pr.nama_barang, pr.gambar_url, pr.satuan);
                } else if (list.length === 0) {
                    toastError('Produk tidak ditemukan.');
                } else {
                    toastError('Persempit pencarian sampai tersisa 1 produk.');
                }
            }
            else if (shift && key === 'k') { e.preventDefault(); if (cart.length) { renderCart(); $('#ModalKeranjang').modal('show'); } else { toastError('Keranjang kosong!'); } }
            else if (shift && key === 'b') { e.preventDefault(); if (cart.length) { $('#btnLanjutBayar').trigger('click'); } else { toastError('Keranjang kosong!'); } }
            return;
        }
        if (st === 'satuan') {
            if (key === 'arrowleft')       { e.preventDefault(); fhSwitchSatuan(-1); }
            else if (key === 'arrowright') { e.preventDefault(); fhSwitchSatuan(+1); }
            else if (key === 'arrowup')    { e.preventDefault(); $('#psPlus').trigger('click'); }
            else if (key === 'arrowdown')  { e.preventDefault(); $('#psMinus').trigger('click'); }
            else if (key === 'enter')      { e.preventDefault(); $('#btnTambahKeKeranjang').trigger('click'); }
            return;
        }
        if (st === 'keranjang') {
            if (fhIsText(document.activeElement)) { return; }  // input qty yang diklik: biar normal
            if (shift && key === 'enter') { e.preventDefault(); $('#btnLanjutBayar').trigger('click'); return; }
            if (key === 'escape') {
                if (cartFind || cartTargetKey) { e.preventDefault(); fhResetCartTarget(); updateFhStatus(); return; }
                return; // biar modal ditutup bootstrap
            }
            if (key === 'arrowup')   { e.preventDefault(); fhCartQty(+1); return; }
            if (key === 'arrowdown') { e.preventDefault(); fhCartQty(-1); return; }
            if (key === 'delete')    { e.preventDefault(); fhCartDelete(); return; }
            if (key === 'backspace') { e.preventDefault(); fhCartBackspace(); return; }
            // ketik nama item (huruf/angka/spasi) -> type-ahead
            if (!e.ctrlKey && !e.altKey && !e.metaKey && e.key && e.key.length === 1 && /[a-z0-9 ]/i.test(e.key)) {
                e.preventDefault(); fhCartType(e.key); return;
            }
            return;
        }
        if (st === 'bayar') {
            var inPel = document.activeElement && document.activeElement.id === 'inputPembeli';

            // sedang dalam mode (diskon/uang): panah pilih, Enter pakai, Esc keluar
            if (bayarMode) {
                if (key === 'arrowleft' || key === 'arrowup')    { e.preventDefault(); fhBayarNav(-1); return; }
                if (key === 'arrowright' || key === 'arrowdown') { e.preventDefault(); fhBayarNav(+1); return; }
                if (key === 'enter')  { e.preventDefault(); fhBayarActivate(); return; }
                if (key === 'escape') { e.preventDefault(); fhBayarExit(); return; }
                return;
            }

            // shortcut huruf hanya bila TIDAK sedang mengetik nama pelanggan (biar bisa ketik kapital)
            if (!inPel) {
                if (shift && key === 'u') { e.preventDefault(); fhBayarEnterMode('uang'); return; }
                if (shift && key === 'n') { e.preventDefault(); $('.summaryTotalBayar').focus(); return; }
                if (shift && key === 'd') { e.preventDefault(); fhBayarEnterMode('diskon'); return; }
                if (shift && key === 'p') { e.preventDefault(); document.getElementById('inputPembeli').focus(); return; }
            }
            if (key === 'enter') {
                var ae = document.activeElement;
                if (ae && ae.tagName === 'BUTTON') return; // tombol terfokus ditekan native
                if (inPel) return;                          // jangan konfirmasi dari field pelanggan
                e.preventDefault(); $('#btnKonfirmasiBayar').trigger('click'); return;
            }
            return;
        }
    });
</script>

@endsection

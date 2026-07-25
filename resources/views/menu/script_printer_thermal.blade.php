@php $qzCert = @file_get_contents(public_path('qz-tray/digital-certificate.txt')); @endphp

<style>
    /* tombol print non-aktif saat printer belum siap / sedang mencetak (otomatis termasuk tombol dinamis) */
    body.print-not-ready .btn-print,
    body.print-busy .btn-print { pointer-events: none; opacity: .5; cursor: not-allowed; }
</style>

{{-- MODAL PENGATURAN PRINTER (QZ TRAY) --}}
<div class="modal fade" id="ModalPengaturanPrinter" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="ti ti-printer"></i>&nbsp; Pengaturan Printer</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="qzStatus" class="alert border py-2 mb-3">Memeriksa koneksi QZ Tray ..</div>

                <label class="mb-1"><b>Pilih Printer</b></label>
                <div class="input-group mb-3">
                    <select id="qzPrinterSelect" class="form-control"><option value="">- Pilih -</option></select>
                    <button class="btn btn-outline-secondary" type="button" id="btnRefreshPrinter" title="Muat ulang daftar printer"><i class="ti ti-refresh"></i></button>
                </div>

                <label class="mb-1"><b>Lebar Kertas</b></label>
                <select id="qzPaperSelect" class="form-control mb-3">
                    <option value="80">80 mm</option>
                    <option value="58">58 mm</option>
                </select>

                <label class="mb-1"><b>Mode Cetak</b></label>
                <select id="qzModeSelect" class="form-control mb-1">
                    <option value="escpos">Teks (ESC/POS) — paling tajam &amp; cepat</option>
                    <option value="html">Gambar (HTML) — layout paling bebas</option>
                </select>
                <small class="text-muted d-block mb-3">Mode <b>Teks</b>: huruf tajam pakai font printer (<b>logo tidak dicetak</b>). Mode <b>Gambar</b>: struk dirender sebagai gambar termasuk logo (lebih bebas tapi bisa kurang tajam).</small>

                <button type="button" class="btn btn-info w-100 mb-2" id="btnTestPrint"><i class="ti ti-test-pipe"></i>&nbsp; Test Print</button>

                <div class="alert alert-warning py-2 mb-0">
                    <b>Belum jalan?</b> Aplikasi <b>QZ Tray</b> harus terinstall & berjalan di komputer ini.
                    <div class="mt-2">
                        <a class="btn btn-sm btn-dark" href="{{ asset('qz-tray/qz-tray-community-2.1.1.exe') }}"><i class="ti ti-download"></i> Install QZ Tray</a>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btnTutorialPrinter"><i class="ti ti-book"></i> Tutorial</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup&emsp;<i class="ti ti-x"></i></button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL TUTORIAL PRINTER --}}
<style>
    #ModalTutorialPrinter .step-num { width: 30px; height: 30px; border-radius: 50%; background: #5d87ff; color: #fff; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0; }
    #ModalTutorialPrinter .tut-kbd { background:#f1f1f1; border:1px solid #ddd; border-radius:6px; padding:1px 6px; font-family:monospace; font-size:12px; }
</style>
<div class="modal fade" id="ModalTutorialPrinter" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="ti ti-book"></i>&nbsp; Cara Pakai Printer (QZ Tray)</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert customize-alert text-primary border border-primary py-2">
                    <i class="ti ti-info-circle fs-5 me-1"></i>
                    QZ Tray = aplikasi kecil di komputer yang menghubungkan browser ke printer. Install &amp; jalankan sekali, lalu pilih printer.
                </div>

                <div class="d-flex gap-3 mb-3">
                    <span class="step-num">1</span>
                    <div>
                        <b>Install QZ Tray</b>
                        <p class="mb-1 text-muted fs-2">Unduh &amp; install di komputer yang tersambung printer.</p>
                        <a class="btn btn-sm btn-dark" href="{{ asset('qz-tray/qz-tray-community-2.1.1.exe') }}"><i class="ti ti-download"></i> Download QZ Tray</a>
                    </div>
                </div>

                <div class="d-flex gap-3 mb-3">
                    <span class="step-num">2</span>
                    <div>
                        <b>Jalankan QZ Tray</b>
                        <p class="mb-0 text-muted fs-2">Buka aplikasinya, pastikan ikon muncul di <b>system tray</b> (pojok kanan bawah). Harus <b>selalu jalan</b> saat mau cetak — sebaiknya diatur otomatis saat komputer nyala.</p>
                    </div>
                </div>

                <div class="d-flex gap-3 mb-3">
                    <span class="step-num">3</span>
                    <div>
                        <b>Install Driver Printer</b>
                        <p class="mb-0 text-muted fs-2">Install driver printer sesuai dengan model printer yang digunakan. Silahkan cari panduan di internet.</p>
                    </div>
                </div>

                <div class="d-flex gap-3 mb-3">
                    <span class="step-num">4</span>
                    <div>
                        <b>Pilih Printer</b>
                        <p class="mb-0 text-muted fs-2">Di menu <b>Pengaturan Printer</b>, kalau QZ jalan statusnya <span class="text-success">Terhubung</span>. Pilih <b>printer</b> &amp; <b>lebar kertas</b> (<span class="tut-kbd">80mm</span> / <span class="tut-kbd">58mm</span>). Tersimpan otomatis di perangkat ini.</p>
                    </div>
                </div>

                <div class="d-flex gap-3 mb-3">
                    <span class="step-num">5</span>
                    <div>
                        <b>Atur Kertas di Windows</b>
                        <p class="mb-0 text-muted fs-2">Buka <b>Settings → Printers &amp; scanners</b> → pilih printer → <b>Printing preferences</b>. Set lebar kertas <span class="tut-kbd">80mm</span> (atau 58mm), jenis <b>Continuous / Roll</b>, dan <b>margin 0</b>. Samakan dengan lebar kertas di aplikasi. Kalau tidak diatur, struk bisa <b>kepotong</b>, ke-scale kecil, atau feed kertas kepanjangan.</p>
                    </div>
                </div>

                <div class="d-flex gap-3 mb-3">
                    <span class="step-num">6</span>
                    <div>
                        <b>Test Print</b>
                        <p class="mb-0 text-muted fs-2">Klik <b>Test Print</b>. Kalau keluar struk contoh, siap dipakai. Saat popup izin QZ muncul, centang <b>Remember</b> lalu <b>Allow</b>.</p>
                    </div>
                </div>

                <div class="d-flex gap-3 mb-3">
                    <span class="step-num">7</span>
                    <div>
                        <b>Cetak Otomatis</b>
                        <p class="mb-0 text-muted fs-2">Setiap <b>Konfirmasi Pembelian</b> langsung cetak nota. Cetak ulang di <b>Data Transaksi</b> → tombol <i class="ti ti-receipt"></i>. Logo, nama toko, alamat, telepon &amp; footer dari <b>Pengaturan Toko</b> ikut tercetak.</p>
                    </div>
                </div>

                <hr>
                <h6 class="fw-semibold"><i class="ti ti-tool"></i> Masalah Umum</h6>
                <ul class="mb-0 text-muted fs-2">
                    <li><b>Tidak terhubung?</b> Pastikan QZ Tray jalan, klik <i class="ti ti-refresh"></i> di Pengaturan Printer.</li>
                    <li><b>Printer tak muncul?</b> Cek printer terdeteksi di Windows → Printers, lalu klik <i class="ti ti-refresh"></i>.</li>
                    <li><b>Selalu diminta izin?</b> Centang <b>Remember this decision</b> → <b>Allow</b>.</li>
                    <li><b>Hasil kepotong/kekecilan?</b> Sesuaikan <b>lebar kertas</b> (80/58mm).</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btnKembaliKePrinter"><i class="ti ti-arrow-left"></i>&nbsp; Kembali</button>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('qz-tray/qz-tray-v2.1.1.js') }}"></script>
<script>
    // =====================================================
    // QZ TRAY - PRINTER THERMAL
    // =====================================================
    var qzCert = @json($qzCert);

    function qzEsc(t){ if(t==null) return ''; return String(t).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
    function qzRp(a){ a = Math.floor(a || 0); return 'Rp ' + a.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'); }

    // ---- STATE TOMBOL PRINT ----
    var qzConnected = false;   // koneksi QZ Tray aktif?
    var printBusy = false;     // sedang mencetak?

    // siap cetak = QZ terhubung DAN printer sudah dipilih
    function printReady() { return qzConnected && !!localStorage.getItem('qzPrinter'); }

    // aktifkan/nonaktifkan semua .btn-print (via class body -> otomatis ke tombol yg dirender kemudian)
    function refreshPrintButtons() {
        var ready = printReady();
        document.body.classList.toggle('print-not-ready', !ready);
        document.body.classList.toggle('print-busy', printBusy);
        $('.btn-print').attr('title', ready ? '' : 'Printer belum terhubung — buka Pengaturan Printer');
    }

    // loading pada satu tombol (spinner + disabled), lalu kembalikan
    function setBtnLoading($btn, on) {
        if (!$btn || !$btn.length) return;
        if (on) {
            if ($btn.data('orig') == null) { $btn.data('orig', $btn.html()); }
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
        } else {
            if ($btn.data('orig') != null) { $btn.html($btn.data('orig')); $btn.removeData('orig'); }
            $btn.prop('disabled', false);
        }
    }

    // cek koneksi QZ diam-diam (untuk enable/disable tombol)
    function qzCheckConnection() {
        if (typeof qz === 'undefined') { qzConnected = false; refreshPrintButtons(); return; }
        if (qz.websocket.isActive()) { qzConnected = true; refreshPrintButtons(); return; }
        qz.websocket.connect().then(function () { qzConnected = true; })
            .catch(function () { qzConnected = false; })
            .then(refreshPrintButtons);
    }
    $(function () {
        if (typeof qz !== 'undefined') {
            try { qz.websocket.setClosedCallbacks(function () { qzConnected = false; refreshPrintButtons(); }); } catch (e) {}
        }
        qzCheckConnection();
    });

    // ---- SECURITY (cert + tanda tangan via server) ----
    $(function () {
        if (typeof qz === 'undefined') return;
        qz.security.setCertificatePromise(function (resolve) { resolve(qzCert); });
        try { qz.security.setSignatureAlgorithm("SHA512"); } catch (e) {}
        qz.security.setSignaturePromise(function (toSign) {
            return function (resolve, reject) {
                $.ajax({
                    url: "{{ route('qz_sign') }}", type: 'POST',
                    data: { _token: "{{ csrf_token() }}", request: toSign },
                    success: resolve, error: function () { reject(new Error('Gagal menandatangani')); }
                });
            };
        });
        // muat kertas & mode tersimpan
        var paper = localStorage.getItem('qzPaper') || '80';
        $('#qzPaperSelect').val(paper);
        var mode = localStorage.getItem('qzMode') || 'escpos';
        $('#qzModeSelect').val(mode);
    });
    $(document).on('change', '#qzModeSelect', function () { localStorage.setItem('qzMode', $(this).val()); });

    // ---- KONEKSI ----
    function qzEnsure() {
        if (typeof qz === 'undefined') return Promise.reject(new Error('Library QZ Tray tidak dimuat.'));
        if (qz.websocket.isActive()) return Promise.resolve();
        return qz.websocket.connect().catch(function () {
            throw new Error('QZ Tray tidak berjalan. Jalankan aplikasi QZ Tray dulu.');
        });
    }

    // ---- DAFTAR PRINTER (saat modal dibuka / refresh) ----
    function refreshPrinters() {
        var st = $('#qzStatus');
        st.attr('class', 'alert border py-2 mb-3 text-muted').text('Menghubungkan ke QZ Tray ..');
        qzEnsure().then(function () { return qz.printers.find(); }).then(function (list) {
            var saved = localStorage.getItem('qzPrinter');
            var sel = $('#qzPrinterSelect').html('<option value="">- Pilih -</option>');
            (list || []).forEach(function (p) {
                sel.append('<option value="' + qzEsc(p) + '"' + (p === saved ? ' selected' : '') + '>' + qzEsc(p) + '</option>');
            });
            st.attr('class', 'alert border border-success text-success py-2 mb-3').html('<i class="ti ti-check"></i> Terhubung ke QZ Tray');
            qzConnected = true; refreshPrintButtons();
        }).catch(function (err) {
            st.attr('class', 'alert border border-danger text-danger py-2 mb-3').html('<i class="ti ti-alert-triangle"></i> ' + (err.message || err));
            qzConnected = false; refreshPrintButtons();
        });
    }
    $(document).on('shown.bs.modal', '#ModalPengaturanPrinter', refreshPrinters);
    $(document).on('click', '#btnRefreshPrinter', refreshPrinters);
    $(document).on('change', '#qzPrinterSelect', function () { localStorage.setItem('qzPrinter', $(this).val()); refreshPrintButtons(); });
    $(document).on('change', '#qzPaperSelect', function () { localStorage.setItem('qzPaper', $(this).val()); });

    // ---- BANGUN HTML NOTA ----
    function buildReceiptHTML(d) {
        var rows = '';
        (d.list_data_belanja || []).forEach(function (it) {
            var sub = parseInt(it.total_qty) * parseInt(it.harga);
            rows += '<div>' + qzEsc(it.nama_barang) + ' (' + qzEsc(it.satuan) + ')</div>'
                 + '<table><tr><td>' + it.total_qty + ' x ' + qzRp(it.harga) + '</td><td class="r">' + qzRp(sub) + '</td></tr></table>';
        });
        var logo = d.logo_url ? '<div class="c"><img src="' + d.logo_url + '" style="max-width:60%"></div>' : '';
        return '<html><head><meta charset="utf-8"><style>'
            + '*{margin:0;padding:0;box-sizing:border-box}'
            + 'body{font-family:\'Courier New\',monospace;font-size:12px;color:#000;width:100%;line-height:1.35}'
            + '.c{text-align:center}.r{text-align:right}.b{font-weight:bold}'
            + '.l{border-top:1px dashed #000;margin:5px 0}'
            + 'table{width:100%;border-collapse:collapse}'
            + '</style></head><body>'
            + logo
            + '<div class="c b" style="font-size:15px">' + qzEsc(d.nama_toko) + '</div>'
            + '<div class="c">' + qzEsc(d.alamat_toko) + '</div>'
            + (d.no_telp ? '<div class="c">Telp: ' + qzEsc(d.no_telp) + '</div>' : '')
            + '<div class="l"></div>'
            + '<div>' + qzEsc(d.id_transaksi) + '</div>'
            + '<div>' + qzEsc(d.timestamp) + '</div>'
            + (d.kasir ? '<div>KASIR : ' + qzEsc(d.kasir) + '</div>' : '')
            + '<div class="l"></div>'
            + rows
            + '<div class="l"></div>'
            + '<div>PEMBELI : ' + qzEsc(d.pembeli || '-') + '</div>'
            + '<div class="l"></div>'
            + '<table>'
            + (parseInt(d.diskon_potongan) > 0
                ? '<tr><td>SUBTOTAL</td><td class="r">' + qzRp(d.subtotal) + '</td></tr>'
                  + '<tr><td>DISKON' + (d.diskon_nama ? ' (' + qzEsc(d.diskon_nama) + ')' : '') + '</td><td class="r">- ' + qzRp(d.diskon_potongan) + '</td></tr>'
                : '')
            + '<tr><td>TOTAL</td><td class="r b">' + qzRp(d.total_belanja) + '</td></tr>'
            + '<tr><td>BAYAR</td><td class="r">' + qzRp(d.total_bayar) + '</td></tr>'
            + '<tr><td>KEMBALI</td><td class="r">' + qzRp(d.kembalian) + '</td></tr></table>'
            + '<div class="l"></div>'
            + (d.footer_nota ? '<div class="c b">' + qzEsc(d.footer_nota) + '</div>' : '')
            + '<div style="height:24px"></div>'
            + '</body></html>';
    }

    // ---- BANGUN ESC/POS (teks murni: hasil paling tajam, pakai font printer) ----
    function escInit()   { return '\x1B\x40'; }                                   // reset
    function escAlign(n) { return '\x1B\x61' + String.fromCharCode(n); }          // 0 kiri, 1 tengah, 2 kanan
    function escBold(on) { return '\x1B\x45' + String.fromCharCode(on ? 1 : 0); }
    function escSize(dbl){ return '\x1D\x21' + String.fromCharCode(dbl ? 0x11 : 0x00); } // 2x / normal
    function escFeed(n)  { return new Array(n + 1).join('\n'); }
    function escDash(W)  { return new Array(W + 1).join('-') + '\n'; }
    function escTwoCol(left, right, W) {
        left = String(left); right = String(right);
        if (left.length + right.length + 1 > W) { left = left.substring(0, Math.max(0, W - right.length - 1)); }
        var sp = W - left.length - right.length;
        return left + new Array(Math.max(1, sp) + 1).join(' ') + right + '\n';
    }
    function buildEscPosText(d, W) {
        var t = escInit();
        // header toko (tengah)
        t += escAlign(1);
        t += escBold(1) + (d.nama_toko || '') + '\n' + escBold(0);
        if (d.alamat_toko) t += d.alamat_toko + '\n';
        if (d.no_telp) t += 'Telp: ' + d.no_telp + '\n';
        // info transaksi (kiri)
        t += escAlign(0) + escDash(W);
        t += (d.id_transaksi || '') + '\n';
        t += (d.timestamp || '') + '\n';
        if (d.kasir) t += 'KASIR : ' + d.kasir + '\n';
        t += escDash(W);
        // item
        (d.list_data_belanja || []).forEach(function (it) {
            var sub = parseInt(it.total_qty) * parseInt(it.harga);
            t += it.nama_barang + ' (' + it.satuan + ')\n';
            t += escTwoCol('  ' + it.total_qty + ' x ' + qzRp(it.harga), qzRp(sub), W);
        });
        t += escDash(W);
        t += 'PEMBELI : ' + (d.pembeli || '-') + '\n';
        t += escDash(W);
        // ringkasan + diskon
        if (parseInt(d.diskon_potongan) > 0) {
            t += escTwoCol('SUBTOTAL', qzRp(d.subtotal), W);
            t += escTwoCol('DISKON' + (d.diskon_nama ? ' (' + d.diskon_nama + ')' : ''), '- ' + qzRp(d.diskon_potongan), W);
        }
        t += escBold(1) + escTwoCol('TOTAL', qzRp(d.total_belanja), W) + escBold(0);
        t += escTwoCol('BAYAR', qzRp(d.total_bayar), W);
        t += escTwoCol('KEMBALI', qzRp(d.kembalian), W);
        t += escDash(W);
        if (d.footer_nota) t += escAlign(1) + d.footer_nota + '\n';
        t += escFeed(4);
        return t;
    }
    // elemen data QZ untuk mode teks. Logo TIDAK dicetak (khusus mode teks, biar tetap tajam & cepat).
    function buildEscPosData(d, W) {
        return [{ type: 'raw', format: 'command', flavor: 'plain', data: buildEscPosText(d, W) }];
    }

    // ---- CETAK NOTA (dipanggil kasir & data transaksi). $btn opsional utk loading. ----
    function qzPrintNota(d, $btn) {
        var printer = localStorage.getItem('qzPrinter');
        if (!printer) {
            toastError('Printer belum dipilih! Buka Pengaturan Printer.');
            $('#ModalPengaturanPrinter').modal('show');
            return Promise.resolve();
        }
        printBusy = true; refreshPrintButtons();
        setBtnLoading($btn, true);
        var paper = parseInt(localStorage.getItem('qzPaper') || '80');
        var mode = localStorage.getItem('qzMode') || 'escpos';
        return qzEnsure().then(function () {
            qzConnected = true;
            var cfg, data;
            if (mode === 'escpos') {
                // TEKS ESC/POS: font asli printer -> paling tajam. Logo dikonversi otomatis oleh QZ.
                var W = paper >= 80 ? 48 : 32; // lebar karakter font A
                cfg = qz.configs.create(printer);
                data = buildEscPosData(d, W);
            } else {
                // GAMBAR (HTML): 203 DPI + nearest-neighbor supaya sekurang mungkin blur
                var widthIn = paper >= 80 ? 2.83 : 1.89;
                cfg = qz.configs.create(printer, {
                    margins: 0, colorType: 'blackwhite', rasterize: true, scaleContent: true,
                    interpolation: 'nearest-neighbor', density: 203
                });
                data = [{ type: 'pixel', format: 'html', flavor: 'plain', data: buildReceiptHTML(d), options: { pageWidth: widthIn, density: 203 } }];
            }
            return qz.print(cfg, data);
        }).then(function () {
            toastSuccess('Nota terkirim ke printer');
        }).catch(function (err) {
            toastError('Gagal cetak: ' + (err.message || err));
            if (/QZ Tray|connect|websocket|tidak berjalan/i.test(String(err.message || err))) { qzConnected = false; }
        }).then(function () {
            printBusy = false;
            setBtnLoading($btn, false);
            refreshPrintButtons();
        });
    }

    // ---- TEST PRINT ----
    $(document).on('click', '#btnTestPrint', function () {
        var printer = $('#qzPrinterSelect').val();
        if (!printer) { toastError('Pilih printer dulu!'); return; }
        localStorage.setItem('qzPrinter', printer);
        refreshPrintButtons();
        // tombol test print: tampilkan loading (tapi tak ikut auto-disable, karena ini untuk setup)
        qzPrintNota({
            list_data_belanja: [{ nama_barang: 'Contoh Barang', satuan: 'Ecer', total_qty: 2, harga: 5000 }],
            id_transaksi: 'TEST-PRINT', timestamp: '{{ date("Y-m-d H:i:s") }}',
            total_belanja: 10000, total_bayar: 20000, kembalian: 10000,
            pembeli: 'Uji Coba',
            nama_toko: 'TES PRINTER', alamat_toko: 'QZ Tray', no_telp: '', footer_nota: 'Test berhasil :)', logo_url: ''
        }, $(this));
    });

    // ---- TUTORIAL (modal) ----
    $(document).on('click', '#btnTutorialPrinter', function () {
        $('#ModalPengaturanPrinter').modal('hide');
        $('#ModalTutorialPrinter').modal('show');
    });
    $(document).on('click', '#btnKembaliKePrinter', function () {
        $('#ModalTutorialPrinter').modal('hide');
        $('#ModalPengaturanPrinter').modal('show');
    });
</script>

{{-- ================= DARK MODE (Bootstrap 5.3 data-bs-theme) ================= --}}
{{-- preload font ikon Tabler agar tidak muncul kotak-kotak saat halaman dimuat --}}
<link rel="preload" as="font" type="font/woff2" crossorigin
      href="{{ asset('template/dist/css/icons/tabler-icons/fonts/tabler-icons2d35.woff2?v2.11.0') }}">
<style>
    /* paksa font-display:block -> ikon tak tampil kotak (tofu) saat load, langsung glyph */
    @font-face {
        font-family: "tabler-icons";
        font-style: normal; font-weight: 400; font-display: block;
        src: url("{{ asset('template/dist/css/icons/tabler-icons/fonts/tabler-icons2d35.woff2?v2.11.0') }}") format("woff2");
    }

    [data-bs-theme="dark"] {
        --bs-body-bg: #1b1f2a;
        --bs-body-color: #e6e9f0;
        --bs-border-color: #2e3440;
        color-scheme: dark; /* kontrol native ikut gelap: date picker, select, scrollbar */
    }
    [data-bs-theme="dark"] body { background-color: #1b1f2a; color: #e6e9f0; }

    /* input tanggal/waktu native mengikuti tema gelap */
    [data-bs-theme="dark"] input[type="date"],
    [data-bs-theme="dark"] input[type="time"],
    [data-bs-theme="dark"] input[type="datetime-local"],
    [data-bs-theme="dark"] select { color-scheme: dark; }
    [data-bs-theme="dark"] select.form-control option,
    [data-bs-theme="dark"] .form-select option { background-color: #232733; color: #e6e9f0; }

    /* TAB (pills & tabs) terbaca di dark mode.
       Template pakai -webkit-text-fill-color, jadi WAJIB di-override juga (bukan cuma color). */
    [data-bs-theme="dark"] .nav-pills .nav-link,
    [data-bs-theme="dark"] .nav-tabs .nav-link { color: #c4c9d4 !important; -webkit-text-fill-color: #c4c9d4 !important; }
    [data-bs-theme="dark"] .nav-pills .nav-link:hover,
    [data-bs-theme="dark"] .nav-tabs .nav-link:hover { color: #ffffff !important; -webkit-text-fill-color: #ffffff !important; background-color: #2a2f3d; }
    [data-bs-theme="dark"] .nav-pills .nav-link.active { color: #ffffff !important; -webkit-text-fill-color: #ffffff !important; background-color: #5d87ff !important; }
    [data-bs-theme="dark"] .nav-tabs .nav-link.active { color: #e6e9f0 !important; -webkit-text-fill-color: #e6e9f0 !important; background-color: #232733; border-color: #2e3440 #2e3440 #232733; }

    /* kbd/badge tutorial printer (tut-kbd) jangan putih di dark */
    [data-bs-theme="dark"] .tut-kbd { background: #2a2f3d !important; border-color: #3a4152 !important; color: #e6e9f0 !important; }

    /* placeholder thumbnail (keranjang / pilih satuan) tidak putih di dark */
    [data-bs-theme="dark"] .cart-thumb { background: #2a2f3d !important; }

    /* teks brand yang warnanya di-hardcode inline (mis. "KasirKu" di navbar mobile) */
    [data-bs-theme="dark"] span[style*="color:#2a3547"] { color: #e6e9f0 !important; }

    /* struktur utama */
    [data-bs-theme="dark"] .left-sidebar { background-color: #232733 !important; border-right: 1px solid #2e3440; }
    [data-bs-theme="dark"] .app-header,
    [data-bs-theme="dark"] .app-header .navbar { background-color: #232733 !important; }
    /* menu header versi mobile (navbar-collapse) — template kasih bg putih di <=991.98px */
    [data-bs-theme="dark"] .navbar-collapse { background-color: #232733 !important; border-top-color: #2e3440 !important; }
    /* header saat di-scroll (fixed-header): template ubah navbar jadi KARTU PUTIH.
       Di dark mode isian ikut warna tema (jangan putih); shadow pemisah tetap dipertahankan. */
    [data-bs-theme="dark"] .app-header.fixed-header .navbar,
    [data-bs-theme="dark"] #main-wrapper[data-layout="vertical"] .app-header.fixed-header .navbar { background: #232733 !important; }
    [data-bs-theme="dark"] .card { background-color: #232733; border-color: #2e3440; }
    [data-bs-theme="dark"] .card.bg-light-info { background-color: #1f2a33 !important; }
    [data-bs-theme="dark"] .bg-light,
    [data-bs-theme="dark"] .table-light,
    [data-bs-theme="dark"] thead.table-light th { background-color: #2a2f3d !important; color: #e6e9f0 !important; }
    [data-bs-theme="dark"] .border,
    [data-bs-theme="dark"] .border-top,
    [data-bs-theme="dark"] .border-bottom,
    [data-bs-theme="dark"] .border-start,
    [data-bs-theme="dark"] .border-end { border-color: #2e3440 !important; }

    /* teks umum (p, span, label) mengikuti tema.
       Tanpa !important: class warna Bootstrap (.text-primary/.text-muted/dll pakai !important) tetap menang. */
    [data-bs-theme="dark"] p,
    [data-bs-theme="dark"] span,
    [data-bs-theme="dark"] label,
    [data-bs-theme="dark"] .form-label,
    [data-bs-theme="dark"] .form-check-label,
    [data-bs-theme="dark"] .breadcrumb-item { color: #e6e9f0; }

    /* judul & teks yang warnanya di-hardcode inline (logo, heading) */
    [data-bs-theme="dark"] h1, [data-bs-theme="dark"] h2, [data-bs-theme="dark"] h3,
    [data-bs-theme="dark"] h4, [data-bs-theme="dark"] h5, [data-bs-theme="dark"] h6 { color: #e6e9f0 !important; }
    [data-bs-theme="dark"] .brand-logo > a > span,
    [data-bs-theme="dark"] .logo-img > span { color: #e6e9f0 !important; }
    [data-bs-theme="dark"] .text-dark { color: #e6e9f0 !important; }
    [data-bs-theme="dark"] .text-muted { color: #99a1b7 !important; }

    /* sidebar item (menimpa aturan mode terang) */
    [data-bs-theme="dark"] #sidebarnav .sidebar-item { border-bottom-color: #2e3440; }
    [data-bs-theme="dark"] #sidebarnav .sidebar-item .sidebar-link { color: #c4c9d4; }
    [data-bs-theme="dark"] #sidebarnav .sidebar-item .sidebar-link:hover { background-color: #2a2f3d !important; color: #ffffff !important; }
    [data-bs-theme="dark"] #sidebarnav .sidebar-item .sidebar-link:hover i,
    [data-bs-theme="dark"] #sidebarnav .sidebar-item .sidebar-link:hover span { color: #ffffff !important; }
    [data-bs-theme="dark"] #sidebarnav .sidebar-item .sidebar-link.active,
    [data-bs-theme="dark"] #sidebarnav .sidebar-item .sidebar-link.active:hover { background-color: #2b3450 !important; color: #8ea9ff !important; border-left-color: #5d87ff !important; }
    [data-bs-theme="dark"] #sidebarnav .sidebar-item .sidebar-link.active i,
    [data-bs-theme="dark"] #sidebarnav .sidebar-item .sidebar-link.active span,
    [data-bs-theme="dark"] #sidebarnav .sidebar-item .sidebar-link.active:hover i,
    [data-bs-theme="dark"] #sidebarnav .sidebar-item .sidebar-link.active:hover span { color: #8ea9ff !important; }

    /* form controls */
    [data-bs-theme="dark"] .form-control,
    [data-bs-theme="dark"] .form-select { background-color: #1b1f2a; border-color: #2e3440; color: #e6e9f0; }
    [data-bs-theme="dark"] .form-control:focus,
    [data-bs-theme="dark"] .form-select:focus { background-color: #1b1f2a; color: #e6e9f0; }
    [data-bs-theme="dark"] .form-control::placeholder { color: #6c7383; }
    [data-bs-theme="dark"] .input-group-text { background-color: #2a2f3d; border-color: #2e3440; color: #e6e9f0; }

    /* kasir: kartu produk */
    [data-bs-theme="dark"] .prod-card { background: #232733; border-color: #2e3440; }
    [data-bs-theme="dark"] .prod-name { color: #e6e9f0; }
    [data-bs-theme="dark"] .prod-img-wrap,
    [data-bs-theme="dark"] .prod-noimg { background: #1b1f2a; }

    /* tabel & datatables */
    [data-bs-theme="dark"] .table { color: #e6e9f0; --bs-table-bg: transparent; }
    [data-bs-theme="dark"] table.dataTable tbody tr { background-color: transparent; }
    [data-bs-theme="dark"] .dataTables_wrapper .dataTables_paginate .paginate_button,
    [data-bs-theme="dark"] .dataTables_wrapper .dataTables_info,
    [data-bs-theme="dark"] .dataTables_wrapper .dataTables_length,
    [data-bs-theme="dark"] .dataTables_wrapper .dataTables_filter { color: #c4c9d4 !important; }

    /* halaman auth (login/register/reset) */
    [data-bs-theme="dark"] .radial-gradient { background: #1b1f2a !important; }

    /* modal & dropdown mengikuti bawaan BS5.3, sedikit rapikan */
    [data-bs-theme="dark"] .modal-content { background-color: #232733; }
    [data-bs-theme="dark"] .bg-white { background-color: #232733 !important; }

    /* wrapper & preloader ikut gelap (cegah kedip putih saat pindah halaman) */
    [data-bs-theme="dark"] .page-wrapper,
    [data-bs-theme="dark"] .body-wrapper { background-color: #1b1f2a; }
    [data-bs-theme="dark"] .preloader { background-color: #1b1f2a !important; }

    /* warna lembut Modernize (tile dashboard, badge, dsb.) */
    [data-bs-theme="dark"] .bg-light-primary { background-color: #26314f !important; }
    [data-bs-theme="dark"] .bg-light-success { background-color: #1f3a2e !important; }
    [data-bs-theme="dark"] .bg-light-danger  { background-color: #3a2530 !important; }
    [data-bs-theme="dark"] .bg-light-info    { background-color: #1f3340 !important; }
    [data-bs-theme="dark"] .bg-light-warning { background-color: #3a3320 !important; }

    /* skeleton grid kasir */
    [data-bs-theme="dark"] .skel { background: linear-gradient(90deg, #242936 25%, #2f3646 37%, #242936 63%); background-size: 400% 100%; }

    /* kotak nominal di modal pembayaran (total & kembalian) */
    .bayar-box-total { background: #fff5f5; }
    .bayar-box-kembali { background: #f0f9f4; }
    [data-bs-theme="dark"] .bayar-box-total { background: #3a2530 !important; }
    [data-bs-theme="dark"] .bayar-box-kembali { background: #1f3329 !important; }

    /* SweetAlert2 dark */
    [data-bs-theme="dark"] .swal2-popup { background: #232733 !important; color: #e6e9f0 !important; }
    [data-bs-theme="dark"] .swal2-title,
    [data-bs-theme="dark"] .swal2-html-container { color: #e6e9f0 !important; }
    [data-bs-theme="dark"] .swal2-input,
    [data-bs-theme="dark"] .swal2-textarea,
    [data-bs-theme="dark"] .swal2-select { background: #1b1f2a !important; color: #e6e9f0 !important; border-color: #2e3440 !important; }
    [data-bs-theme="dark"] .swal2-input::placeholder { color: #6c7383 !important; }

    /* pratinjau struk: tetap kertas putih + teks hitam meski mode gelap */
    .nota-preview, .nota-preview * { color: #000 !important; }
    .nota-preview { background: #fff !important; }

    /* switch tema sedikit lebih besar & rapi */
    .theme-switch { cursor: pointer; width: 2.6em !important; height: 1.35em; }
    [data-theme-icon] { line-height: 1; }
</style>
<script>
    (function () {
        // terapkan tema tersimpan sedini mungkin (cegah flash)
        var saved = 'light';
        try { saved = localStorage.getItem('kasirTheme') || 'light'; } catch (e) {}
        document.documentElement.setAttribute('data-bs-theme', saved);

        function sync(t) {
            // ikon menandakan mode aktif: bulan = gelap, matahari = terang
            var ics = document.querySelectorAll('[data-theme-icon]');
            for (var i = 0; i < ics.length; i++) {
                var size = ics[i].getAttribute('data-icon-size') || 'fs-5';
                ics[i].className = (t === 'dark' ? 'ti ti-moon ' : 'ti ti-sun ') + size;
            }
            // posisi switch: on = gelap
            var sws = document.querySelectorAll('.theme-switch');
            for (var j = 0; j < sws.length; j++) { sws[j].checked = (t === 'dark'); }
            // label mengikuti mode aktif
            var lbs = document.querySelectorAll('[data-theme-label]');
            for (var k = 0; k < lbs.length; k++) { lbs[k].textContent = (t === 'dark') ? 'Mode Gelap' : 'Mode Terang'; }
        }
        function apply(t) {
            document.documentElement.setAttribute('data-bs-theme', t);
            try { localStorage.setItem('kasirTheme', t); } catch (e) {}
            sync(t);
        }
        window.kasirToggleTheme = function () {
            var cur = document.documentElement.getAttribute('data-bs-theme') || 'light';
            apply(cur === 'dark' ? 'light' : 'dark');
        };
        document.addEventListener('DOMContentLoaded', function () {
            sync(document.documentElement.getAttribute('data-bs-theme') || 'light');
        });
        // switch on/off
        document.addEventListener('change', function (e) {
            if (e.target && e.target.classList && e.target.classList.contains('theme-switch')) {
                apply(e.target.checked ? 'dark' : 'light');
            }
        });
        // klik pada ikon juga bisa toggle
        document.addEventListener('click', function (e) {
            var ic = e.target.closest ? e.target.closest('[data-theme-icon]') : null;
            if (ic) { e.preventDefault(); window.kasirToggleTheme(); }
        });
    })();
</script>

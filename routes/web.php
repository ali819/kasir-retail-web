<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KelolaUserController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\DiskonController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
 
     // Periksa apakah pengguna sudah login
     if (auth()->check()) {
        // Jika sudah login, arahkan ke laman dashboard
        return redirect()->route('dashboard');
    }

    // Jika belum login, tampilkan laman login
    return view('auth.login');
});

Route::get('/dashboard', [AdminController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // ---- umum: semua role (admin/stok/kasir) ----
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // QZ TRAY (printer) — dipakai kasir & admin untuk cetak nota
    Route::post('/qz/sign', [AdminController::class, 'qz_sign'])->name('qz_sign');

    // ---- KASIR: role kasir (+admin bypass) ----
    Route::middleware('role:kasir')->group(function () {
        Route::get('/kasir', [AdminController::class, 'kasir'])->name('kasir');
        Route::get('/kasir/produk', [AdminController::class, 'kasir_produk'])->name('kasir_produk');
        Route::get('/kasir/barcode', [AdminController::class, 'kasir_barcode'])->name('kasir_barcode');
        Route::get('/kasir/detail-data-barang', [AdminController::class, 'detail_data_barang'])->name('detail_data_barang');
        Route::post('/kasir/konfirmasi-pembelian-barang', [AdminController::class, 'konfirmasi_pembelian_barang'])->name('konfirmasi_pembelian_barang');
        Route::get('/kasir/diskon-aktif', [DiskonController::class, 'aktif'])->name('diskon_aktif');

        // pelanggan: dropdown search & tambah cepat dari layar kasir
        Route::get('/kasir/pelanggan-search', [PelangganController::class, 'search'])->name('pelanggan_search');
        Route::post('/kasir/pelanggan-quick', [PelangganController::class, 'quickStore'])->name('pelanggan_quick');

        // KELOLA PELANGGAN (admin + kasir)
        Route::get('/kelola-pelanggan', [PelangganController::class, 'index'])->name('kelola_pelanggan');
        Route::get('/kelola-pelanggan/tabel', [PelangganController::class, 'tabel'])->name('kelola_pelanggan_tabel');
        Route::post('/kelola-pelanggan/tambah', [PelangganController::class, 'store'])->name('kelola_pelanggan_tambah');
        Route::post('/kelola-pelanggan/update', [PelangganController::class, 'update'])->name('kelola_pelanggan_update');
        Route::delete('/kelola-pelanggan/hapus', [PelangganController::class, 'destroy'])->name('kelola_pelanggan_hapus');
    });

    // ---- KELOLA BARANG & PENGATURAN: role stok (+admin bypass) ----
    Route::middleware('role:stok')->group(function () {
        Route::get('/kelola-barang', [AdminController::class, 'kelola_barang'])->name('kelola_barang');
        Route::get('/stok/tabel-stok', [AdminController::class, 'tabel_stok'])->name('tabel_stok');
        Route::post('/stok/tambah-barang-baru', [AdminController::class, 'tambah_barang_baru'])->name('tambah_barang_baru');
        Route::delete('/stok/hapus-barang', [AdminController::class, 'hapus_data_barang'])->name('hapus_data_barang');
        Route::post('/stok/update-barang', [AdminController::class, 'update_barang'])->name('update_barang');

        // PENGATURAN (setelan toko)
        Route::get('/pengaturan', [AdminController::class, 'pengaturan'])->name('pengaturan');
        Route::post('/pengaturan', [AdminController::class, 'update_pengaturan'])->name('update_pengaturan');
    });

    // ---- DATA PEMBELIAN, LAPORAN & KELOLA USER: admin only ----
    Route::middleware('role:admin')->group(function () {
        // dashboard: detail penjualan, rekap & export laporan (nominal = khusus admin)
        Route::get('/dashboard/tabel-detail-pembelian', [AdminController::class, 'tabel_detail_pembelian'])->name('tabel_detail_pembelian');
        Route::get('/dashboard/hitung-data-pembelian', [AdminController::class, 'hitung_data_pembelian'])->name('hitung_data_pembelian');
        Route::get('/dashboard/export-laporan', [AdminController::class, 'export_laporan'])->name('export_laporan');

        Route::get('/data-transaksi', [AdminController::class, 'data_transaksi'])->name('data_transaksi');
        Route::get('/data-transaksi/tabel', [AdminController::class, 'tabel_data_transaksi'])->name('tabel_data_transaksi');
        Route::get('/data-transaksi/detail', [AdminController::class, 'detail_data_transaksi'])->name('detail_data_transaksi');
        Route::delete('/data-transaksi/hapus', [AdminController::class, 'hapus_data_transaksi'])->name('hapus_data_transaksi');
        Route::get('/data-transaksi/nota-html', [AdminController::class, 'nota_pembelian_html'])->name('nota_pembelian_html');
        Route::get('/data-transaksi/statistik-pelanggan', [AdminController::class, 'statistik_pelanggan_tabel'])->name('statistik_pelanggan_tabel');
        Route::get('/data-transaksi/pelanggan-transaksi', [AdminController::class, 'pelanggan_transaksi'])->name('pelanggan_transaksi');

        // PENGATURAN EMAIL (SMTP)
        Route::get('/pengaturan-email', [AdminController::class, 'pengaturan_email'])->name('pengaturan_email');
        Route::post('/pengaturan-email', [AdminController::class, 'update_pengaturan_email'])->name('update_pengaturan_email');
        Route::post('/pengaturan-email/test', [AdminController::class, 'test_email'])->name('test_email');

        // KELOLA DISKON
        Route::get('/kelola-diskon', [DiskonController::class, 'index'])->name('kelola_diskon');
        Route::get('/kelola-diskon/tabel', [DiskonController::class, 'tabel'])->name('kelola_diskon_tabel');
        Route::post('/kelola-diskon/tambah', [DiskonController::class, 'store'])->name('kelola_diskon_tambah');
        Route::post('/kelola-diskon/update', [DiskonController::class, 'update'])->name('kelola_diskon_update');
        Route::delete('/kelola-diskon/hapus', [DiskonController::class, 'destroy'])->name('kelola_diskon_hapus');

        // KELOLA USER (manajemen pengguna)
        Route::get('/kelola-user', [KelolaUserController::class, 'index'])->name('kelola_user');
        Route::get('/kelola-user/tabel', [KelolaUserController::class, 'tabel'])->name('kelola_user_tabel');
        Route::post('/kelola-user/tambah', [KelolaUserController::class, 'store'])->name('kelola_user_tambah');
        Route::post('/kelola-user/update', [KelolaUserController::class, 'update'])->name('kelola_user_update');
        Route::post('/kelola-user/reset-password', [KelolaUserController::class, 'resetPassword'])->name('kelola_user_reset');
        Route::delete('/kelola-user/hapus', [KelolaUserController::class, 'destroy'])->name('kelola_user_hapus');
    });
});

require __DIR__.'/auth.php';

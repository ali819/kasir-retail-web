<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Indeks untuk mempercepat query dashboard, laporan & rekap
     * (filter tanggal, group per transaksi/barang/kasir).
     */
    public function up(): void
    {
        Schema::table('data_pembelian_detail', function (Blueprint $table) {
            $table->index('created_at', 'idx_dpd_created_at');
            $table->index('id_transaksi', 'idx_dpd_id_transaksi');
            $table->index('nama_barang', 'idx_dpd_nama_barang');
        });

        Schema::table('data_pembelian', function (Blueprint $table) {
            $table->index('created_at', 'idx_dp_created_at');
            $table->index('id_transaksi', 'idx_dp_id_transaksi');
            $table->index('id_user', 'idx_dp_id_user');
        });
    }

    public function down(): void
    {
        Schema::table('data_pembelian_detail', function (Blueprint $table) {
            $table->dropIndex('idx_dpd_created_at');
            $table->dropIndex('idx_dpd_id_transaksi');
            $table->dropIndex('idx_dpd_nama_barang');
        });
        Schema::table('data_pembelian', function (Blueprint $table) {
            $table->dropIndex('idx_dp_created_at');
            $table->dropIndex('idx_dp_id_transaksi');
            $table->dropIndex('idx_dp_id_user');
        });
    }
};

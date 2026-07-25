<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Relasi transaksi -> pelanggan. Kolom 'pembeli' (nama) tetap disimpan
     * sebagai snapshot agar nota lama tetap utuh meski pelanggan dihapus.
     */
    public function up(): void
    {
        Schema::table('data_pembelian', function (Blueprint $table) {
            if (!Schema::hasColumn('data_pembelian', 'id_pelanggan')) {
                $table->unsignedBigInteger('id_pelanggan')->nullable()->after('pembeli');
                $table->index('id_pelanggan', 'idx_dp_id_pelanggan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('data_pembelian', function (Blueprint $table) {
            if (Schema::hasColumn('data_pembelian', 'id_pelanggan')) {
                $table->dropIndex('idx_dp_id_pelanggan');
                $table->dropColumn('id_pelanggan');
            }
        });
    }
};

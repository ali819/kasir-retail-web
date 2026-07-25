<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Catat kasir (user) yang memproses tiap transaksi.
     * nama_kasir disimpan terpisah agar tetap ada meski user dihapus.
     */
    public function up(): void
    {
        Schema::table('data_pembelian', function (Blueprint $table) {
            if (!Schema::hasColumn('data_pembelian', 'id_user')) {
                $table->unsignedBigInteger('id_user')->nullable()->after('pembeli');
            }
            if (!Schema::hasColumn('data_pembelian', 'nama_kasir')) {
                $table->string('nama_kasir')->nullable()->after('id_user');
            }
        });
    }

    public function down(): void
    {
        Schema::table('data_pembelian', function (Blueprint $table) {
            foreach (['id_user', 'nama_kasir'] as $col) {
                if (Schema::hasColumn('data_pembelian', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Diskon di level transaksi.
     * total_belanja = nilai SETELAH diskon (uang riil masuk) agar laporan tetap benar.
     * subtotal = jumlah item sebelum diskon (untuk struk).
     */
    public function up(): void
    {
        Schema::table('data_pembelian', function (Blueprint $table) {
            if (!Schema::hasColumn('data_pembelian', 'subtotal')) {
                $table->unsignedBigInteger('subtotal')->nullable()->after('kembalian');
            }
            if (!Schema::hasColumn('data_pembelian', 'diskon_nama')) {
                $table->string('diskon_nama')->nullable()->after('subtotal');
            }
            if (!Schema::hasColumn('data_pembelian', 'diskon_potongan')) {
                $table->unsignedBigInteger('diskon_potongan')->default(0)->after('diskon_nama');
            }
        });

        // data lama: belum ada diskon -> subtotal = total_belanja
        DB::statement('UPDATE data_pembelian SET subtotal = total_belanja WHERE subtotal IS NULL');
    }

    public function down(): void
    {
        Schema::table('data_pembelian', function (Blueprint $table) {
            foreach (['subtotal', 'diskon_nama', 'diskon_potongan'] as $col) {
                if (Schema::hasColumn('data_pembelian', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

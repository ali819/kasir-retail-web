<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('stok_barang', 'barcode')) {
            Schema::table('stok_barang', function (Blueprint $table) {
                $table->string('barcode')->nullable()->after('nama_barang');
                $table->index('barcode');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('stok_barang', 'barcode')) {
            Schema::table('stok_barang', function (Blueprint $table) {
                $table->dropIndex(['barcode']);
                $table->dropColumn('barcode');
            });
        }
    }
};

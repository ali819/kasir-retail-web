<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('stok_barang', 'gambar')) {
            Schema::table('stok_barang', function (Blueprint $table) {
                $table->string('gambar')->nullable()->after('satuan_dasar');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('stok_barang', 'gambar')) {
            Schema::table('stok_barang', function (Blueprint $table) {
                $table->dropColumn('gambar');
            });
        }
    }
};

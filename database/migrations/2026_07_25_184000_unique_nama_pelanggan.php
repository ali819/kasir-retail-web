<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Nama pelanggan wajib unik (cegah bentrok data).
     * Collation MySQL default case-insensitive -> "Budi" & "budi" dianggap sama.
     */
    public function up(): void
    {
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->dropIndex('idx_pelanggan_nama');
            $table->unique('nama', 'unq_pelanggan_nama');
        });
    }

    public function down(): void
    {
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->dropUnique('unq_pelanggan_nama');
            $table->index('nama', 'idx_pelanggan_nama');
        });
    }
};

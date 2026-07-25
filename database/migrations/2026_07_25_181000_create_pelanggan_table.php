<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelanggan', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('no_telp')->nullable();
            $table->string('alamat')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index('nama', 'idx_pelanggan_nama');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelanggan');
    }
};

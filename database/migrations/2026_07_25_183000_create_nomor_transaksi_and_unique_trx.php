<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Counter nomor transaksi per hari (atomik, bebas race) +
     * UNIQUE index pada data_pembelian.id_transaksi.
     */
    public function up(): void
    {
        // 1. tabel counter: 1 baris per tanggal (ymd), di-increment atomik
        if (!Schema::hasTable('nomor_transaksi')) {
            Schema::create('nomor_transaksi', function (Blueprint $table) {
                $table->string('tanggal', 8)->primary(); // format ymd, mis. 260725
                $table->unsignedInteger('urutan')->default(0);
            });
        }

        // 2. seed counter dari data lama (agar penomoran baru melanjutkan, bukan mengulang)
        $maxPerDay = [];
        foreach (DB::table('data_pembelian')->pluck('id_transaksi') as $id) {
            if (!preg_match('/^TRX-(\d{6})-(\d+)$/', (string) $id, $m)) {
                continue;
            }
            $ymd = $m[1];
            $n = (int) $m[2];
            if (!isset($maxPerDay[$ymd]) || $n > $maxPerDay[$ymd]) {
                $maxPerDay[$ymd] = $n;
            }
        }
        foreach ($maxPerDay as $ymd => $n) {
            DB::table('nomor_transaksi')->updateOrInsert(['tanggal' => $ymd], ['urutan' => $n]);
        }

        // 3. ganti index id_transaksi menjadi UNIQUE (cegah duplikat)
        Schema::table('data_pembelian', function (Blueprint $table) {
            $table->dropIndex('idx_dp_id_transaksi');
            $table->unique('id_transaksi', 'unq_dp_id_transaksi');
        });
    }

    public function down(): void
    {
        Schema::table('data_pembelian', function (Blueprint $table) {
            $table->dropUnique('unq_dp_id_transaksi');
            $table->index('id_transaksi', 'idx_dp_id_transaksi');
        });
        Schema::dropIfExists('nomor_transaksi');
    }
};

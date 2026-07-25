<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Model terpadu: 1 barang punya banyak "satuan jual" (nama, harga, konversi ke satuan dasar).
     */
    public function up(): void
    {
        // 1. tabel satuan_jual
        if (!Schema::hasTable('satuan_jual')) {
            Schema::create('satuan_jual', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_barang');
                $table->string('nama_satuan');
                $table->unsignedBigInteger('harga')->default(0);
                $table->unsignedInteger('konversi')->default(1); // 1 satuan jual = berapa satuan dasar
                $table->timestamps();
                $table->index('id_barang');
            });
        }

        // 2. tambah kolom satuan_dasar ke stok_barang
        if (!Schema::hasColumn('stok_barang', 'satuan_dasar')) {
            Schema::table('stok_barang', function (Blueprint $table) {
                $table->string('satuan_dasar')->default('Pcs')->after('nama_barang');
            });
        }

        // 3. migrasi data lama -> satuan_jual (hanya sekali, jika satuan_jual masih kosong)
        if (DB::table('satuan_jual')->count() === 0 && Schema::hasColumn('stok_barang', 'kategori_barang')) {
            $now = now();
            foreach (DB::table('stok_barang')->get() as $b) {
                $rows = [];
                if ($b->kategori_barang === 'satuan_tidak_tetap') {
                    $list = DB::table('list_satuan_tidak_tetap')->where('id_stok_barang', $b->id)->get();
                    foreach ($list as $r) {
                        $rows[] = ['nama_satuan' => $r->satuan, 'harga' => $r->harga, 'konversi' => 1];
                    }
                    if (count($rows) === 0) {
                        $rows[] = ['nama_satuan' => 'Satuan', 'harga' => 0, 'konversi' => 1];
                    }
                } else {
                    // satuan_tetap -> Ecer (+ Grosir jika ada)
                    $rows[] = ['nama_satuan' => 'Ecer', 'harga' => $b->harga_per_biji, 'konversi' => 1];
                    if ($b->harga_grosir > 0) {
                        $rows[] = [
                            'nama_satuan' => 'Grosir',
                            'harga' => $b->harga_grosir,
                            'konversi' => $b->qty_grosir > 0 ? $b->qty_grosir : 1,
                        ];
                    }
                }

                foreach ($rows as $r) {
                    DB::table('satuan_jual')->insert([
                        'id_barang' => $b->id,
                        'nama_satuan' => $r['nama_satuan'],
                        'harga' => $r['harga'],
                        'konversi' => $r['konversi'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }

        // 4. buang kolom lama dari stok_barang
        foreach (['harga_per_biji', 'harga_grosir', 'qty_grosir', 'kategori_barang'] as $col) {
            if (Schema::hasColumn('stok_barang', $col)) {
                Schema::table('stok_barang', function (Blueprint $table) use ($col) {
                    $table->dropColumn($col);
                });
            }
        }

        // 5. buang tabel lama
        Schema::dropIfExists('list_satuan_tidak_tetap');
    }

    public function down(): void
    {
        // kembalikan struktur lama (data tidak dipulihkan)
        Schema::table('stok_barang', function (Blueprint $table) {
            if (!Schema::hasColumn('stok_barang', 'harga_per_biji')) $table->unsignedBigInteger('harga_per_biji')->default(0);
            if (!Schema::hasColumn('stok_barang', 'harga_grosir')) $table->unsignedBigInteger('harga_grosir')->default(0);
            if (!Schema::hasColumn('stok_barang', 'qty_grosir')) $table->unsignedBigInteger('qty_grosir')->default(0);
            if (!Schema::hasColumn('stok_barang', 'kategori_barang')) $table->enum('kategori_barang', ['satuan_tetap', 'satuan_tidak_tetap'])->default('satuan_tetap');
        });

        if (!Schema::hasTable('list_satuan_tidak_tetap')) {
            Schema::create('list_satuan_tidak_tetap', function (Blueprint $table) {
                $table->id();
                $table->integer('id_stok_barang');
                $table->unsignedBigInteger('harga');
                $table->string('satuan');
                $table->timestamps();
            });
        }

        if (Schema::hasColumn('stok_barang', 'satuan_dasar')) {
            Schema::table('stok_barang', function (Blueprint $table) {
                $table->dropColumn('satuan_dasar');
            });
        }

        Schema::dropIfExists('satuan_jual');
    }
};

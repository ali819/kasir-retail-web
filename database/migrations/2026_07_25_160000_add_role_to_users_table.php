<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Tambah kolom role (admin / stok / kasir) ke tabel users.
     * User pertama (id terkecil) yang sudah ada dijadikan admin.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['admin', 'stok', 'kasir'])->default('kasir')->after('email');
            });
        }

        // user paling awal yang sudah ada -> admin
        $first = DB::table('users')->orderBy('id')->first();
        if ($first) {
            DB::table('users')->where('id', $first->id)->update(['role' => 'admin']);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }
    }
};

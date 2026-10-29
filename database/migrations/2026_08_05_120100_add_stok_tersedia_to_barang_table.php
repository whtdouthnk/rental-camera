<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->integer('stok_tersedia')->default(0)->after('stok');
        });

        DB::table('barang')->update(['stok_tersedia' => DB::raw('stok')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->dropColumn('stok_tersedia');
        });
    }
};

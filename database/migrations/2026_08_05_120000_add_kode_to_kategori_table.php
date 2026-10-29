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
        Schema::table('kategori', function (Blueprint $table) {
            $table->string('kode')->unique()->nullable()->after('id');
        });

        $rows = DB::table('kategori')->orderBy('id')->get();

        foreach ($rows as $row) {
            DB::table('kategori')
                ->where('id', $row->id)
                ->update(['kode' => 'KT' . str_pad($row->id, 3, '0', STR_PAD_LEFT)]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kategori', function (Blueprint $table) {
            $table->dropUnique(['kode']);
            $table->dropColumn('kode');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();

            $table->string('kode_transaksi')->unique();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->date('tgl_pinjam');
            $table->date('tgl_jatuh_tempo');

            $table->enum('status', [
                'Menunggu',
                'Disetujui',
                'Ditolak',
                'Dipinjam',
                'Selesai',
                'Dibatalkan'
            ])->default('Menunggu');

            $table->foreignId('disetujui_oleh')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('catatan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};

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
        Schema::create('pengembalian', function (Blueprint $table) {
            $table->id();

            $table->foreignId('transaksi_id')
                ->unique()
                ->constrained('transaksi')
                ->cascadeOnDelete();

            $table->date('tgl_kembali');

            $table->integer('terlambat_hari')->default(0);

            $table->decimal('total_denda', 10, 2)->default(0);

            $table->enum('kondisi_barang', [
                'Baik',
                'Rusak Ringan',
                'Rusak Berat'
            ]);

            $table->text('catatan')->nullable();

            $table->foreignId('diterima_oleh')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengembalian');
    }
};

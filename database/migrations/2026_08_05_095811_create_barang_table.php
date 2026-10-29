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
        Schema::create('barang', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kategori_id')
                ->constrained('kategori')
                ->cascadeOnDelete();

            $table->string('kode_barang')->unique();
            $table->string('nama_barang');
            $table->string('merk');
            $table->string('serial_number')->nullable();

            $table->integer('stok');

            $table->enum('kondisi', [
                'Baik',
                'Rusak Ringan',
                'Rusak'
            ]);

            $table->decimal('denda_per_hari', 10, 2);

            $table->string('foto')->nullable();

            $table->enum('status', [
                'Aktif',
                'Nonaktif'
            ])->default('Aktif');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};

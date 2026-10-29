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
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();

            $table->foreignId('transaksi_id')
                ->unique()
                ->constrained('transaksi')
                ->cascadeOnDelete();

            $table->decimal('jumlah', 12, 2);
            $table->string('metode', 20)->default('Tunai');
            $table->timestamp('dibayar_pada');

            $table->foreignId('dibayar_oleh')
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
        Schema::dropIfExists('pembayaran');
    }
};

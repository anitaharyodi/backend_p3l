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
        Schema::create('transaksi_fasilitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_fasilitas')->constrained(table: 'fasilitas_tambahans')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('id_reservasi')->constrained(table: 'reservasis')->cascadeOnUpdate()->cascadeOnDelete();
            $table->date('tgl_pemakaian');
            $table->integer('jumlah');
            $table->integer('subtotal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_fasilitas');
    }
};

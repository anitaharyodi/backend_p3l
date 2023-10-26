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
        Schema::create('nota_lunas', function (Blueprint $table) {
            $table->id();
            $table->string('no_invoice', 50);
            $table->foreignId('id_reservasi')->constrained(table: 'reservasis')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('id_fo')->constrained(table: 'akun_pegawais')->cascadeOnUpdate()->cascadeOnDelete();
            $table->date('tgl_lunas');
            $table->integer('total_harga_layanan');
            $table->integer('pajak_layanan');
            $table->integer('harga_total');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nota_lunas');
    }
};

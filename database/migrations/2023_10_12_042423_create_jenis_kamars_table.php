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
        Schema::create('jenis_kamars', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_kamar');
            $table->integer('kapasitas');
            $table->string('tipe_bed');
            $table->string('ukuran_kamar', 50);
            $table->text('rincian_kamar');
            $table->text('deskripsi_kamar');
            $table->integer('tarif_normal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_kamars');
    }
};

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
        Schema::create('reservasi_kamars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kamar')->nullable()->constrained(table: 'kamars')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('id_reservasi')->constrained(table: 'reservasis')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('id_jenis_kamar')->constrained(table: 'jenis_kamars')->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservasi_kamars');
    }
};

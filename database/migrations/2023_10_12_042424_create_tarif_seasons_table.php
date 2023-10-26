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
        Schema::create('tarif_seasons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_season')->constrained(table: 'seasons')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('id_jenis_kamar')->nullable()->constrained(table: 'jenis_kamars')->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('tarif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarif_seasons');
    }
};

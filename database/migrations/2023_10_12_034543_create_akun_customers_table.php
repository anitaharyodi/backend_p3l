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
        Schema::create('akun_customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_customer')->constrained(table: 'customers')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('email', 50)->unique();
            $table->string('password');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('akun_customers');
    }
};

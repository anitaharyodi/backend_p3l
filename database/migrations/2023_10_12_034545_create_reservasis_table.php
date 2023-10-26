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
        Schema::create('reservasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_sm')->nullable()->constrained(table: 'akun_pegawais')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('id_fo')->nullable()->constrained(table: 'akun_pegawais')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('id_customer')->constrained(table: 'customers')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('id_booking', 50)->nullable();
            $table->date('tgl_reservasi');
            $table->date('tgl_checkin');
            $table->date('tgl_checkout');
            $table->integer('jumlah_dewasa');
            $table->integer('jumlah_anak');
            $table->date('tgl_pembayaran');
            $table->string('bukti_transfer')->nullable();
            $table->string('status', 100);
            $table->integer('total_harga');
            $table->integer('uang_jaminan');
            $table->integer('deposit')->nullable();
            $table->text('special_req')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservasis');
    }
};

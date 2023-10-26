<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_sm',
        'id_fo',
        'id_customer',
        'id_booking',
        'tgl_reservasi',
        'tgl_checkin',
        'tgl_checkout',
        'jumlah_dewasa',
        'jumlah_anak',
        'tgl_pembayaran',
        'bukti_transfer',
        'status',
        'total_harga',
        'uang_jaminan',
        'deposit',
        'special_req',
    ];

    function customers() {
        return $this->belongsTo(Customer::class, 'id_customer');
    }

    function salesMarketings() {
        return $this->belongsTo(AkunPegawai::class, 'id_sm');
    }

    function frontOffices() {
        return $this->belongsTo(AkunPegawai::class, 'id_fo');
    }

    public function transaksiFasilitas() {
        return $this->hasMany(TransaksiFasilitas::class, 'id_reservasi', 'id');
    }

    public function reservasiKamars() {
        return $this->hasMany(ReservasiKamar::class, 'id_reservasi', 'id');
    }
}

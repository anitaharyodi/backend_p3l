<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservasiKamar extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_kamar',
        'id_reservasi',
        'id_jenis_kamar',
        'hargaPerMalam',
    ];

    function reservasis() {
        return $this->belongsTo(Reservasi::class, 'id_reservasi');
    }

    function jenisKamars() {
        return $this->belongsTo(JenisKamar::class, 'id_jenis_kamar');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiFasilitas extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_fasilitas',
        'id_reservasi',
        'tgl_pemakaian',
        'jumlah',
        'subtotal',
    ];

    function fasilitasTambahans() {
        return $this->belongsTo(FasilitasTambahan::class, 'id_fasilitas');
    }

    function reservasis() {
        return $this->belongsTo(Reservasi::class, 'id_reservasi');
    }

}

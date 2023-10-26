<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kamar extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_kamar',
        'id_jenis_kamar',
        'tipe_bed'
    ];

    function jenisKamars() {
        return $this->belongsTo(JenisKamar::class, 'id_jenis_kamar');
    }
}

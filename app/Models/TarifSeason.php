<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarifSeason extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_season',
        'tarif',
        'id_jenis_kamar',
    ];

    function seasons() {
        return $this->belongsTo(Season::class, 'id_season');
    }

    function jenisKamars() {
        return $this->belongsTo(JenisKamar::class, 'id_jenis_kamar');
    }
}

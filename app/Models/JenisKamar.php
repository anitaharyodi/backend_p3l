<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisKamar extends Model
{
    use HasFactory;

    protected $fillable = [
        'jenis_kamar',
        'kapasitas',
        'tipe_bed',
        'ukuran_kamar',
        'rincian_kamar',
        'deskripsi_kamar',
        'tarif_normal',
    ];

    public function tarifSeasons() {
        return $this->hasMany(TarifSeason::class, 'id_jenis_kamar', 'id');
    }

    public function reservasiKamars() {
        return $this->hasMany(ReservasiKamar::class, 'id_jenis_kamar', 'id');
    }

    public function kamars() {
        return $this->hasMany(Kamar::class, 'id_jenis_kamar', 'id');
    }
}

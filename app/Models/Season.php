<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_season',
        'jenis_season',
        'tanggal_mulai',
        'tanggal_selesai',
    ];

    public function tarifSeasons() {
        return $this->hasMany(TarifSeason::class, 'id_season', 'id');
    }

}

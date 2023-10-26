<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FasilitasTambahan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_fasilitas',
        'harga',
        'satuan',
    ];
    
    public function transaksiFasilitas() {
        return $this->hasMany(TransaksiFasilitas::class, 'id_fasilitas', 'id');
    }
}

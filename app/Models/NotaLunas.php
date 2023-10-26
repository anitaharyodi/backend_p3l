<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaLunas extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_invoice',
        'id_reservasi',
        'id_fo',
        'tgl_lunas',
        'total_harga_layanan',
        'pajak_layanan',
        'harga_total',
    ];
}

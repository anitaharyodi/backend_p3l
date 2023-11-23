<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'email',
        'nama_institusi',
        'no_identitas',
        'no_telepon',
        'alamat',
        'role',
        'id_sm',
    ];

    public function akunCustomers() {
        return $this->hasOne(AkunCustomer::class, 'id_customer', 'id');
    }

    public function reservations() {
        return $this->hasMany(Reservasi::class, 'id_customer', 'id');
    }

    function salesMarketings() {
        return $this->belongsTo(AkunPegawai::class, 'id_sm');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class AkunPegawai extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'nama',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function reservationsForSM() {
        return $this->hasMany(Reservasi::class, 'id_sm', 'id');
    }

    public function reservationsForFO() {
        return $this->hasMany(Reservasi::class, 'id_fo', 'id');
    }

    public function invoices() {
        return $this->hasMany(NotaLunas::class, 'id_fo', 'id');
    }

    public function customerSM() {
        return $this->hasMany(Customer::class, 'id_sm', 'id');
    }
}

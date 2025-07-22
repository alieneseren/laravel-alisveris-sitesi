<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Kullanici extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'kullanicis';

    protected $fillable = [
        'ad',
        'eposta',
        'telefon',
        'adres',
        'sifre',
        'rol',
        'paythor_token',
        'email_verification_token',
        'email_verification_token_expires_at',
        'email_verified_at',
        'email_verified',
    ];

    protected $hidden = [
        'sifre',
    ];

    public function getAuthPassword()
    {
        return $this->sifre;
    }

    public function magazalar()
    {
        return $this->hasMany(Magaza::class);
    }

    public function siparisler()
    {
        return $this->hasMany(Siparis::class);
    }

    public function yorumlar()
    {
        return $this->hasMany(UrunYorumu::class);
    }
}

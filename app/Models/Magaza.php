<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Magaza extends Model
{
    use HasFactory;

    protected $table = 'magazas';

    protected $fillable = [
        'kullanici_id',
        'magaza_adi',
        'magaza_aciklamasi',
        'magaza_logo',
    ];

    public function kullanici()
    {
        return $this->belongsTo(Kullanici::class);
    }

    public function urunler()
    {
        return $this->hasMany(Urun::class);
    }
}

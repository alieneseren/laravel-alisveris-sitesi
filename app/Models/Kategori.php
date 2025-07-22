<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategoris';

    protected $fillable = [
        'kategori_adi',
        'ust_kategori_id',
    ];

    // Accessor for 'ad' attribute
    public function getAdAttribute()
    {
        return $this->kategori_adi;
    }

    public function ustKategori()
    {
        return $this->belongsTo(Kategori::class, 'ust_kategori_id');
    }

    public function altKategoriler()
    {
        return $this->hasMany(Kategori::class, 'ust_kategori_id');
    }

    public function urunler()
    {
        return $this->hasMany(Urun::class, 'kategori_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sepet extends Model
{
    use HasFactory;

    protected $table = 'sepet';

    protected $fillable = [
        'kullanici_id',
        'urun_id',
        'miktar',
    ];

    /**
     * Kullanıcı ilişkisi
     */
    public function kullanici()
    {
        return $this->belongsTo(Kullanici::class);
    }

    /**
     * Ürün ilişkisi
     */
    public function urun()
    {
        return $this->belongsTo(Urun::class);
    }
}

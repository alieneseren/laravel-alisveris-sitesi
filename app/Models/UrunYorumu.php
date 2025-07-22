<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UrunYorumu extends Model
{
    use HasFactory;

    protected $table = 'urun_yorumus';

    protected $fillable = [
        'urun_id',
        'kullanici_id',
        'puan',
        'yorum',
    ];

    public function urun()
    {
        return $this->belongsTo(Urun::class);
    }

    public function kullanici()
    {
        return $this->belongsTo(Kullanici::class);
    }
}

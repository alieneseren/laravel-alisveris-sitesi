<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiparisUrunu extends Model
{
    use HasFactory;

    protected $table = 'siparis_urunus';

    protected $fillable = [
        'siparis_id',
        'urun_id',
        'adet',
        'birim_fiyat',
    ];

    public function siparis()
    {
        return $this->belongsTo(Siparis::class);
    }

    public function urun()
    {
        return $this->belongsTo(Urun::class);
    }
}

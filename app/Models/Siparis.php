<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siparis extends Model
{
    use HasFactory;

    protected $table = 'siparis';

    protected $fillable = [
        'kullanici_id',
        'toplam_tutar',
        'durum',
        'teslimat_adresi',
        'teslimat_telefonu',
        'fatura_bilgileri',
        'payment_reference',
        'payment_status',
    ];

    protected $casts = [
        'fatura_bilgileri' => 'array',
    ];

    public function kullanici()
    {
        return $this->belongsTo(Kullanici::class);
    }

    public function siparisUrunleri()
    {
        return $this->hasMany(SiparisUrunu::class);
    }
}

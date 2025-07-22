<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\UrunKategoriHelper;

class Urun extends Model
{
    use HasFactory;

    protected $table = 'uruns';

    protected $fillable = [
        'magaza_id',
        'kategori_id',
        'ad',
        'aciklama',
        'fiyat',
        'stok',
        'durum',
    ];

    /**
     * Model event'leri
     */
    protected static function boot()
    {
        parent::boot();
        
        // Ürün kaydedilirken kategori yoksa otomatik ata
        static::saving(function ($urun) {
            if (empty($urun->kategori_id) && !empty($urun->ad)) {
                $urun->kategori_id = UrunKategoriHelper::otomatikKategoriAta($urun->ad);
            }
        });
    }

    public function magaza()
    {
        return $this->belongsTo(Magaza::class);
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function kategoriler()
    {
        return $this->belongsToMany(Kategori::class, 'urun_kategori');
    }

    public function gorseller()
    {
        return $this->hasMany(UrunGorseli::class);
    }

    public function urunGorselleri()
    {
        return $this->hasMany(UrunGorseli::class);
    }

    public function yorumlar()
    {
        return $this->hasMany(UrunYorumu::class);
    }

    public function siparisUrunleri()
    {
        return $this->hasMany(SiparisUrunu::class);
    }

    public function anaGorsel()
    {
        return $this->hasOne(UrunGorseli::class)->where('ana_gorsel', true);
    }
}

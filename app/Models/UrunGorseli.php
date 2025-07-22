<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UrunGorseli extends Model
{
    use HasFactory;

    protected $table = 'urun_gorselis';

    protected $fillable = [
        'urun_id',
        'gorsel_url',
        'ana_gorsel',
    ];

    protected $casts = [
        'ana_gorsel' => 'boolean',
    ];

    public function getGorselYoluAttribute()
    {
        return $this->gorsel_url;
    }

    public function urun()
    {
        return $this->belongsTo(Urun::class);
    }
}

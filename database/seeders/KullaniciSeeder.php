<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Kullanici;
use App\Models\Magaza;
use Illuminate\Support\Facades\Hash;

class KullaniciSeeder extends Seeder
{
    public function run(): void
    {
        // Admin kullanıcı
        $admin = Kullanici::create([
            'ad' => 'Admin',
            'eposta' => 'admin@pazaryeri.com',
            'sifre' => Hash::make('admin123'),
            'rol' => 'yonetici',
        ]);

        // Satıcı kullanıcı
        $satici = Kullanici::create([
            'ad' => 'Satıcı Test',
            'eposta' => 'satici@pazaryeri.com',
            'sifre' => Hash::make('satici123'),
            'rol' => 'satici',
        ]);

        // Satıcı için mağaza
        Magaza::create([
            'kullanici_id' => $satici->id,
            'magaza_adi' => 'Test Mağazası',
            'magaza_aciklamasi' => 'Test mağazası açıklaması',
            'magaza_logo' => null,
        ]);

        // Müşteri kullanıcı
        Kullanici::create([
            'ad' => 'Müşteri Test',
            'eposta' => 'musteri@pazaryeri.com',
            'sifre' => Hash::make('musteri123'),
            'rol' => 'musteri',
        ]);
    }
}

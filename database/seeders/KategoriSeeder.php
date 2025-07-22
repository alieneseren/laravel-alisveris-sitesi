<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Kategori;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        // Ana kategoriler
        $elektronik = Kategori::create([
            'kategori_adi' => 'Elektronik',
            'ust_kategori_id' => null,
        ]);

        $moda = Kategori::create([
            'kategori_adi' => 'Moda',
            'ust_kategori_id' => null,
        ]);

        $evYasam = Kategori::create([
            'kategori_adi' => 'Ev & Yaşam',
            'ust_kategori_id' => null,
        ]);

        $spor = Kategori::create([
            'kategori_adi' => 'Spor & Outdoor',
            'ust_kategori_id' => null,
        ]);

        // Elektronik alt kategorileri
        Kategori::create([
            'kategori_adi' => 'Telefonlar',
            'ust_kategori_id' => $elektronik->id,
        ]);

        Kategori::create([
            'kategori_adi' => 'Bilgisayarlar',
            'ust_kategori_id' => $elektronik->id,
        ]);

        Kategori::create([
            'kategori_adi' => 'TV & Ses Sistemleri',
            'ust_kategori_id' => $elektronik->id,
        ]);

        // Moda alt kategorileri
        Kategori::create([
            'kategori_adi' => 'Kadın Giyim',
            'ust_kategori_id' => $moda->id,
        ]);

        Kategori::create([
            'kategori_adi' => 'Erkek Giyim',
            'ust_kategori_id' => $moda->id,
        ]);

        Kategori::create([
            'kategori_adi' => 'Ayakkabı',
            'ust_kategori_id' => $moda->id,
        ]);

        // Ev & Yaşam alt kategorileri
        Kategori::create([
            'kategori_adi' => 'Mobilya',
            'ust_kategori_id' => $evYasam->id,
        ]);

        Kategori::create([
            'kategori_adi' => 'Dekorasyon',
            'ust_kategori_id' => $evYasam->id,
        ]);

        // Spor alt kategorileri
        Kategori::create([
            'kategori_adi' => 'Fitness',
            'ust_kategori_id' => $spor->id,
        ]);

        Kategori::create([
            'kategori_adi' => 'Outdoor',
            'ust_kategori_id' => $spor->id,
        ]);
    }
}

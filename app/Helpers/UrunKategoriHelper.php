<?php

namespace App\Helpers;

use App\Models\Kategori;

class UrunKategoriHelper
{
    /**
     * Ürün adına göre otomatik kategori atar
     */
    public static function otomatikKategoriAta($urunAdi)
    {
        $urunAd = strtolower($urunAdi);
        
        // Kategorileri al
        $elektronik = Kategori::where('kategori_adi', 'Elektronik')->first();
        $giyim = Kategori::where('kategori_adi', 'Giyim')->first();
        $evBahce = Kategori::where('kategori_adi', 'Ev & Bahçe')->first();
        $spor = Kategori::where('kategori_adi', 'Spor')->first();
        
        // Elektronik anahtar kelimeleri
        $elektronikKelimeler = [
            'iphone', 'samsung', 'laptop', 'macbook', 'sony', 'xiaomi', 'huawei',
            'lg', 'canon', 'nikon', 'fotoğraf', 'kamera', 'telefon', 'tablet',
            'kulaklık', 'mouse', 'klavye', 'monitor', 'tv', 'televizyon',
            'playstation', 'xbox', 'nintendo', 'bilgisayar', 'pc'
        ];
        
        // Giyim anahtar kelimeleri
        $giyimKelimeler = [
            'tişört', 'pantolon', 'elbise', 'ceket', 'mont', 'gömlek',
            'kazak', 'ayakkabı', 'bot', 'sandalet', 'çanta', 'kemer',
            'şapka', 'zara', 'mango', 'h&m', 'lcw', 'waikiki', 'koton',
            'defacto', 'mavi', 'levi', 'nike', 'adidas'
        ];
        
        // Spor anahtar kelimeleri
        $sporKelimeler = [
            'futbol', 'basketbol', 'tenis', 'voleybol', 'yüzme', 'koşu',
            'fitness', 'yoga', 'pilates', 'dumbbell', 'halter', 'mat',
            'raket', 'top', 'spor', 'antrenman', 'wilson', 'head',
            'babolat', 'under armour', 'puma', 'reebok', 'decathlon'
        ];
        
        // Ev & Bahçe anahtar kelimeleri
        $evBahceKelimeler = [
            'masa', 'sandalye', 'koltuk', 'yatak', 'dolap', 'kitaplık',
            'lamba', 'ayna', 'halı', 'perde', 'mutfak', 'tencere',
            'tabak', 'bardak', 'çatal', 'kaşık', 'fırın', 'buzdolabı',
            'çamaşır', 'bulaşık', 'süpürge', 'ütü', 'kahve', 'çay',
            'ikea', 'tefal', 'arçelik', 'beko', 'bosch', 'siemens'
        ];
        
        // Kontrol et ve kategori ata
        foreach ($elektronikKelimeler as $kelime) {
            if (strpos($urunAd, $kelime) !== false) {
                return $elektronik ? $elektronik->id : 1;
            }
        }
        
        foreach ($giyimKelimeler as $kelime) {
            if (strpos($urunAd, $kelime) !== false) {
                return $giyim ? $giyim->id : 2;
            }
        }
        
        foreach ($sporKelimeler as $kelime) {
            if (strpos($urunAd, $kelime) !== false) {
                return $spor ? $spor->id : 4;
            }
        }
        
        foreach ($evBahceKelimeler as $kelime) {
            if (strpos($urunAd, $kelime) !== false) {
                return $evBahce ? $evBahce->id : 3;
            }
        }
        
        // Varsayılan olarak Elektronik kategorisine ata
        return $elektronik ? $elektronik->id : 1;
    }
    
    /**
     * Kategori adını ID'ye çevir
     */
    public static function kategoriAdindanId($kategoriAdi)
    {
        $kategori = Kategori::where('kategori_adi', $kategoriAdi)->first();
        return $kategori ? $kategori->id : null;
    }
    
    /**
     * Tüm kategorisiz ürünleri otomatik ata
     */
    public static function tumKategorisizUrunleriAta()
    {
        $kategorisizUrunler = \App\Models\Urun::whereNull('kategori_id')->orWhere('kategori_id', '')->get();
        $atananSayisi = 0;
        
        foreach ($kategorisizUrunler as $urun) {
            $kategoriId = self::otomatikKategoriAta($urun->ad);
            $urun->kategori_id = $kategoriId;
            $urun->save();
            $atananSayisi++;
        }
        
        return $atananSayisi;
    }
}

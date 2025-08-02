<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Magaza;
use App\Models\Urun;
use App\Models\UrunGorseli;
use App\Models\Kategori;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SaticiController extends Controller
{
    public function dashboard()
    {
        $kullaniciId = Session::get('kullanici_id');
        $magaza = Magaza::where('kullanici_id', $kullaniciId)->first();
        
        $stats = [];
        if ($magaza) {
            $stats = [
                'toplam_urun' => $magaza->urunler()->count(),
                'aktif_urun' => $magaza->urunler()->where('durum', 'aktif')->count(),
                'toplam_satis' => 0, // TODO: Sipariş sisteminden hesaplanacak
                'bekleyen_siparis' => 0, // TODO: Sipariş sisteminden hesaplanacak
            ];
        }

        return view('satici.dashboard', compact('magaza', 'stats'));
    }

    public function magazaOlustur()
    {
        $kullaniciId = Session::get('kullanici_id');
        $magaza = Magaza::where('kullanici_id', $kullaniciId)->first();
        
        if ($magaza) {
            return redirect()->route('satici.dashboard')->with('error', 'Zaten bir mağazanız var!');
        }

        return view('satici.magaza-olustur');
    }

    public function magazaOlusturPost(Request $request)
    {
        $kullaniciId = Session::get('kullanici_id');
        $mevcutMagaza = Magaza::where('kullanici_id', $kullaniciId)->first();
        
        if ($mevcutMagaza) {
            return redirect()->route('satici.dashboard')->with('error', 'Zaten bir mağazanız var!');
        }

        $request->validate([
            'magaza_adi' => 'required|string|max:255|unique:magazas,magaza_adi',
            'magaza_aciklamasi' => 'required|string|max:1000',
        ]);

        Magaza::create([
            'kullanici_id' => $kullaniciId,
            'magaza_adi' => $request->magaza_adi,
            'magaza_aciklamasi' => $request->magaza_aciklamasi,
            'magaza_logo' => null, // Şimdilik logo yok
        ]);

        return redirect()->route('satici.dashboard')->with('success', 'Mağazanız başarıyla oluşturuldu!');
    }

    public function urunler()
    {
        $kullaniciId = Session::get('kullanici_id');
        $magaza = Magaza::where('kullanici_id', $kullaniciId)->first();
        
        if (!$magaza) {
            return redirect()->route('satici.magaza.olustur')->with('error', 'Önce bir mağaza oluşturmalısınız!');
        }

        $urunler = $magaza->urunler()->with(['gorseller', 'kategoriler'])->orderBy('created_at', 'desc')->get();

        return view('satici.urunler', compact('magaza', 'urunler'));
    }

    public function urunEkle()
    {
        $kullaniciId = Session::get('kullanici_id');
        $magaza = Magaza::where('kullanici_id', $kullaniciId)->first();
        
        if (!$magaza) {
            return redirect()->route('satici.magaza.olustur')->with('error', 'Önce bir mağaza oluşturmalısınız!');
        }

        $kategoriler = Kategori::whereNull('ust_kategori_id')->with('altKategoriler')->get();

        return view('satici.urun-ekle', compact('magaza', 'kategoriler'));
    }

    public function urunEklePost(Request $request)
    {
        $kullaniciId = Session::get('kullanici_id');
        $magaza = Magaza::where('kullanici_id', $kullaniciId)->first();
        
        if (!$magaza) {
            return redirect()->route('satici.magaza.olustur')->with('error', 'Önce bir mağaza oluşturmalısınız!');
        }

        $request->validate([
            'urun_adi' => 'required|string|max:255',
            'urun_aciklamasi' => 'required|string',
            'fiyat' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'kategoriler' => 'required|array|min:1',
            'kategoriler.*' => 'exists:kategoris,id',
            'gorsel_urls' => 'nullable|string',
            'gorsel_dosyalari.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'yeni_kategori' => 'nullable|string|max:255',
            'ust_kategori_id' => 'nullable|exists:kategoris,id',
        ]);

        // Yeni kategori ekleme
        $secilenKategoriler = $request->kategoriler;
        if ($request->yeni_kategori && $request->ust_kategori_id) {
            $yeniKategori = Kategori::create([
                'kategori_adi' => $request->yeni_kategori,
                'ust_kategori_id' => $request->ust_kategori_id,
            ]);
            $secilenKategoriler[] = $yeniKategori->id;
        }

        $urun = Urun::create([
            'magaza_id' => $magaza->id,
            'ad' => $request->urun_adi,
            'aciklama' => $request->urun_aciklamasi,
            'fiyat' => $request->fiyat,
            'stok' => $request->stok,
            'durum' => 'aktif',
        ]);

        // Kategorileri ekle
        $urun->kategoriler()->attach($secilenKategoriler);


        // Görselleri ekle
        $gorselSayisi = 0;

        // gorseller klasörünü oluştur
        $gorsellerKlasoru = public_path('gorseller');
        if (!file_exists($gorsellerKlasoru)) {
            mkdir($gorsellerKlasoru, 0755, true);
        }

        // Dosya yükleme
        if ($request->hasFile('gorsel_dosyalari')) {
            foreach ($request->file('gorsel_dosyalari') as $dosya) {
                if ($dosya && $dosya->isValid()) {
                    $dosyaAdi = time() . '_' . $urun->id . '_' . $gorselSayisi . '.' . $dosya->getClientOriginalExtension();
                    $dosya->move($gorsellerKlasoru, $dosyaAdi);

                    UrunGorseli::create([
                        'urun_id' => $urun->id,
                        'gorsel_url' => 'gorseller/' . $dosyaAdi,
                        'ana_gorsel' => $gorselSayisi === 0,
                    ]);
                    $gorselSayisi++;
                }
            }
        }

        // URL ile görsel ekleme
        if ($request->gorsel_urls) {
            $urls = explode("\n", trim($request->gorsel_urls));
            foreach ($urls as $url) {
                $url = trim($url);
                if ($url) {
                    UrunGorseli::create([
                        'urun_id' => $urun->id,
                        'gorsel_url' => $url,
                        // Sadece hiç dosya yüklenmediyse ve ilk URL ise ana görsel yap
                        'ana_gorsel' => $gorselSayisi === 0,
                    ]);
                    $gorselSayisi++;
                }
            }
        }

        // Hiç görsel yoksa default görsel ekle
        if ($gorselSayisi === 0) {
            UrunGorseli::create([
                'urun_id' => $urun->id,
                'gorsel_url' => 'storage/uploads/urunler/default-product.jpg',
                'ana_gorsel' => true,
            ]);
        }

        return redirect()->route('satici.urunler')->with('success', 'Ürün başarıyla eklendi!');
    }

    public function urunDuzenle($id)
    {
        $kullaniciId = Session::get('kullanici_id');
        $magaza = Magaza::where('kullanici_id', $kullaniciId)->first();
        
        if (!$magaza) {
            return redirect()->route('satici.magaza.olustur')->with('error', 'Önce bir mağaza oluşturmalısınız!');
        }

        $urun = $magaza->urunler()->with(['kategoriler', 'gorseller'])->findOrFail($id);
        $kategoriler = Kategori::whereNull('ust_kategori_id')->with('altKategoriler')->get();

        return view('satici.urun-duzenle', compact('magaza', 'urun', 'kategoriler'));
    }

    public function urunDuzenlePost(Request $request, $id)
    {
        $kullaniciId = Session::get('kullanici_id');
        $magaza = Magaza::where('kullanici_id', $kullaniciId)->first();
        
        if (!$magaza) {
            return redirect()->route('satici.magaza.olustur')->with('error', 'Önce bir mağaza oluşturmalısınız!');
        }

        $urun = $magaza->urunler()->findOrFail($id);

        $request->validate([
            'urun_adi' => 'required|string|max:255',
            'urun_aciklamasi' => 'required|string',
            'fiyat' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'kategoriler' => 'required|array|min:1',
            'kategoriler.*' => 'exists:kategoris,id',
            'gorsel_dosyalari.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'silinecek_gorseller.*' => 'nullable|exists:urun_gorselis,id',
        ]);

        // Ürün bilgilerini güncelle
        $urun->update([
            'urun_adi' => $request->urun_adi,
            'urun_aciklamasi' => $request->urun_aciklamasi,
            'fiyat' => $request->fiyat,
            'stok' => $request->stok,
        ]);

        // Kategorileri güncelle
        $urun->kategoriler()->sync($request->kategoriler);

        // Silinecek görselleri sil
        if ($request->silinecek_gorseller) {
            $silinecekGorseller = UrunGorseli::where('urun_id', $urun->id)
                ->whereIn('id', $request->silinecek_gorseller)
                ->get();
            
            foreach ($silinecekGorseller as $gorsel) {
                // Dosya sisteminden sil
                if (strpos($gorsel->gorsel_url, '/uploads/') === 0) {
                    $dosyaYolu = public_path($gorsel->gorsel_url);
                    if (file_exists($dosyaYolu)) {
                        unlink($dosyaYolu);
                    }
                }
                $gorsel->delete();
            }
        }

        // Yeni görselleri ekle
        if ($request->hasFile('gorsel_dosyalari')) {
            $mevcutGorselSayisi = $urun->gorseller()->count();
            
            // gorseller klasörünü oluştur
            $gorsellerKlasoru = public_path('gorseller');
            if (!file_exists($gorsellerKlasoru)) {
                mkdir($gorsellerKlasoru, 0755, true);
            }
            
            foreach ($request->file('gorsel_dosyalari') as $index => $dosya) {
                if ($dosya && $dosya->isValid()) {
                    $dosyaAdi = time() . '_' . $urun->id . '_' . ($mevcutGorselSayisi + $index) . '.' . $dosya->getClientOriginalExtension();
                    $dosya->move($gorsellerKlasoru, $dosyaAdi);
                    
                    UrunGorseli::create([
                        'urun_id' => $urun->id,
                        'gorsel_url' => 'gorseller/' . $dosyaAdi,
                        'ana_gorsel' => $urun->gorseller()->count() === 0 && $index === 0,
                    ]);
                }
            }
        }

        return redirect()->route('satici.urunler')->with('success', 'Ürün başarıyla güncellendi!');
    }

    public function urunSil($id)
    {
        $kullaniciId = Session::get('kullanici_id');
        $magaza = Magaza::where('kullanici_id', $kullaniciId)->first();
        
        if (!$magaza) {
            return redirect()->route('satici.magaza.olustur')->with('error', 'Önce bir mağaza oluşturmalısınız!');
        }

        $urun = $magaza->urunler()->findOrFail($id);
        
        // Görselleri sil
        $urun->gorseller()->delete();
        
        // Kategorileri ayır
        $urun->kategoriler()->detach();
        
        // Ürünü sil
        $urun->delete();

        return redirect()->route('satici.urunler')->with('success', 'Ürün başarıyla silindi!');
    }

    /**
     * Satıcının siparişlerini listele
     */
    public function siparisler()
    {
        $kullaniciId = Session::get('kullanici_id');
        $magaza = Magaza::where('kullanici_id', $kullaniciId)->first();
        
        if (!$magaza) {
            return redirect()->route('satici.magaza.olustur')->with('error', 'Önce mağaza oluşturmalısınız!');
        }

        // Mağazadaki ürünlerin sipariş edildiği siparişleri getir
        $siparisler = \App\Models\Siparis::with(['siparisUrunleri.urun', 'kullanici'])
            ->whereHas('siparisUrunleri.urun', function($query) use ($magaza) {
                $query->where('magaza_id', $magaza->id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('satici.siparisler', compact('siparisler', 'magaza'));
    }
}

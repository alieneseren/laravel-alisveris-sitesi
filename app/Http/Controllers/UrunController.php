<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Urun;
use App\Models\Kategori;
use App\Models\UrunYorumu;

class UrunController extends Controller
{
    public function index(Request $request)
    {
        $query = Urun::with(['magaza', 'kategori', 'gorseller', 'yorumlar'])
                     ->where('durum', 'aktif');

        // Kategori filtresi
        if ($request->has('kategori') && $request->kategori) {
            $kategoriler = explode(',', $request->kategori);
            $query->whereIn('kategori_id', $kategoriler);
        }

        // Fiyat filtresi
        if ($request->has('min_fiyat') && $request->min_fiyat) {
            $query->where('fiyat', '>=', $request->min_fiyat);
        }

        if ($request->has('max_fiyat') && $request->max_fiyat) {
            $query->where('fiyat', '<=', $request->max_fiyat);
        }

        // Sıralama
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'fiyat_asc':
                    $query->orderBy('fiyat', 'asc');
                    break;
                case 'fiyat_desc':
                    $query->orderBy('fiyat', 'desc');
                    break;
                case 'rating':
                    $query->withAvg('yorumlar', 'puan')->orderBy('yorumlar_avg_puan', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $urunler = $query->paginate(12);
        $kategoriler = Kategori::all();

        return view('urun.index', compact('urunler', 'kategoriler'));
    }

    public function show($id)
    {
        $urun = Urun::with(['magaza.kullanici', 'gorseller', 'kategoriler', 'yorumlar.kullanici'])
            ->findOrFail($id);

        $kategoriler = Kategori::whereNull('ust_kategori_id')
            ->with('altKategoriler')
            ->get();

        $benzerUrunler = Urun::with(['magaza', 'anaGorsel'])
            ->whereHas('kategoriler', function($query) use ($urun) {
                $query->whereIn('kategori_id', $urun->kategoriler->pluck('id'));
            })
            ->where('id', '!=', $id)
            ->where('durum', 'aktif')
            ->limit(6)
            ->get();

        return view('urun.detay', compact('urun', 'kategoriler', 'benzerUrunler'));
    }

    public function yorumEkle(Request $request, $id)
    {
        $request->validate([
            'puan' => 'required|integer|min:1|max:5',
            'yorum' => 'required|string',
        ]);

        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $urun = Urun::findOrFail($id);

        // Kullanıcının bu ürün için daha önce yorum yapıp yapmadığını kontrol et
        $mevcutYorum = UrunYorumu::where('urun_id', $id)
            ->where('kullanici_id', auth()->id())
            ->first();

        if ($mevcutYorum) {
            return back()->with('error', 'Bu ürün için zaten yorum yaptınız!');
        }

        UrunYorumu::create([
            'urun_id' => $id,
            'kullanici_id' => auth()->id(),
            'puan' => $request->puan,
            'yorum' => $request->yorum,
        ]);

        return back()->with('success', 'Yorumunuz başarıyla eklendi!');
    }
}

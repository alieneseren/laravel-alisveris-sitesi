<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Urun;
use App\Models\Kategori;
use App\Models\Magaza;

class HomeController extends Controller
{
    public function index()
    {
        $urunler = Urun::with(['magaza', 'gorseller', 'kategori'])
            ->where('durum', 'aktif')
            ->latest()
            ->paginate(12);

        $kategoriler = Kategori::whereNull('ust_kategori_id')
            ->with('altKategoriler')
            ->get();

        return view('home', compact('urunler', 'kategoriler'));
    }

    public function kategori(Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);
        
        $query = Urun::with(['magaza', 'gorseller', 'kategori'])
            ->where('durum', 'aktif');

        // Kategori filtresi - birden fazla kategori desteği
        if ($request->has('kategori') && $request->kategori) {
            // URL'den gelen kategori parametresini kullan (virgülle ayrılmış)
            $kategoriler = explode(',', $request->kategori);
            $query->whereIn('kategori_id', $kategoriler);
        } else {
            // Parametre yoksa sadece URL'deki ID'yi kullan
            $query->where('kategori_id', $id);
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

        return view('kategori', compact('urunler', 'kategoriler', 'kategori'));
    }

    public function arama(Request $request)
    {
        $query = $request->get('q');
        
        $urunler = Urun::with(['magaza', 'gorseller', 'kategori'])
            ->where('durum', 'aktif')
            ->where(function($q) use ($query) {
                $q->where('ad', 'like', '%' . $query . '%')
                  ->orWhere('aciklama', 'like', '%' . $query . '%');
            })
            ->latest()
            ->paginate(12);

        $kategoriler = Kategori::whereNull('ust_kategori_id')
            ->with('altKategoriler')
            ->get();

        return view('arama', compact('urunler', 'kategoriler', 'query'));
    }

    public function magaza($id)
    {
        $magaza = Magaza::with('kullanici')->findOrFail($id);
        
        $urunler = Urun::with(['magaza', 'gorseller', 'kategori'])
            ->where('magaza_id', $id)
            ->where('durum', 'aktif')
            ->latest()
            ->paginate(12);

        $kategoriler = Kategori::whereNull('ust_kategori_id')
            ->with('altKategoriler')
            ->get();

        return view('magaza', compact('urunler', 'kategoriler', 'magaza'));
    }
}

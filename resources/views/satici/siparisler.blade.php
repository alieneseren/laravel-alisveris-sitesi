@extends('layouts.app')

@section('title', 'Siparişlerim - Satıcı Paneli')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4><i class="fas fa-shopping-bag"></i> Mağaza Siparişleri</h4>
                    <div>
                        <span class="badge bg-info me-2">{{ $magaza->ad }}</span>
                        <a href="{{ route('satici.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Dashboard'a Dön
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($siparisler->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Sipariş No</th>
                                        <th>Müşteri</th>
                                        <th>Tarih</th>
                                        <th>Ürünler (Sadece Mağazamdan)</th>
                                        <th>Toplam Tutar</th>
                                        <th>Durum</th>
                                        <th>Ödeme Durumu</th>
                                        <th>İletişim</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($siparisler as $siparis)
                                    @php
                                        // Bu mağazaya ait ürünleri filtrele
                                        $magazaUrunleri = $siparis->siparisUrunleri->filter(function($item) use ($magaza) {
                                            return $item->urun && $item->urun->magaza_id == $magaza->id;
                                        });
                                        $magazaToplam = $magazaUrunleri->sum(function($item) {
                                            return $item->adet * $item->birim_fiyat;
                                        });
                                    @endphp
                                    
                                    @if($magazaUrunleri->count() > 0)
                                    <tr>
                                        <td>
                                            <strong>#{{ $siparis->id }}</strong>
                                        </td>
                                        <td>
                                            <strong>{{ $siparis->kullanici->ad ?? 'Bilinmiyor' }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $siparis->kullanici->eposta ?? '' }}</small>
                                        </td>
                                        <td>
                                            {{ $siparis->created_at->format('d.m.Y H:i') }}
                                            <br>
                                            <small class="text-muted">
                                                {{ $siparis->created_at->diffForHumans() }}
                                            </small>
                                        </td>
                                        <td>
                                            @foreach($magazaUrunleri as $item)
                                                <div class="mb-1">
                                                    <strong>{{ $item->urun->ad ?? 'Ürün' }}</strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ $item->adet }} adet × ₺{{ number_format($item->birim_fiyat, 2) }}
                                                    </small>
                                                </div>
                                                @if(!$loop->last) <hr class="my-1"> @endif
                                            @endforeach
                                        </td>
                                        <td>
                                            <strong>₺{{ number_format($magazaToplam, 2) }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                (Toplam sipariş: ₺{{ number_format($siparis->toplam_tutar, 2) }})
                                            </small>
                                        </td>
                                        <td>
                                            @php
                                                $durumRenk = [
                                                    'bekliyor' => 'warning',
                                                    'onaylandi' => 'success', 
                                                    'kargoda' => 'info',
                                                    'teslim_edildi' => 'success',
                                                    'iptal' => 'danger'
                                                ][$siparis->durum] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $durumRenk }}">
                                                {{ ucfirst($siparis->durum) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($siparis->payment_status)
                                                @php
                                                    $odemeRenk = [
                                                        'pending' => 'warning',
                                                        'paid' => 'success',
                                                        'failed' => 'danger',
                                                        'cancelled' => 'secondary'
                                                    ][$siparis->payment_status] ?? 'secondary';
                                                    $odemeText = [
                                                        'pending' => 'Bekliyor',
                                                        'paid' => 'Ödendi',
                                                        'failed' => 'Başarısız',
                                                        'cancelled' => 'İptal'
                                                    ][$siparis->payment_status] ?? $siparis->payment_status;
                                                @endphp
                                                <span class="badge bg-{{ $odemeRenk }}">{{ $odemeText }}</span>
                                            @else
                                                <span class="badge bg-secondary">Belirtilmemiş</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($siparis->teslimat_telefonu)
                                                <small>
                                                    <i class="fas fa-phone"></i> 
                                                    {{ $siparis->teslimat_telefonu }}
                                                </small>
                                                <br>
                                            @endif
                                            @if($siparis->teslimat_adresi)
                                                <small class="text-muted">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                    {{ Str::limit($siparis->teslimat_adresi, 50) }}
                                                </small>
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $siparisler->links() }}
                        </div>
                        
                        <!-- İstatistikler -->
                        <div class="row mt-4">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h4>{{ $siparisler->total() }}</h4>
                                        <small>Toplam Sipariş</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h4>{{ $siparisler->where('payment_status', 'paid')->count() }}</h4>
                                        <small>Ödenen Sipariş</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <h4>{{ $siparisler->where('durum', 'bekliyor')->count() }}</h4>
                                        <small>Bekleyen Sipariş</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        @php
                                            $toplamKazanc = 0;
                                            foreach($siparisler as $siparis) {
                                                $magazaUrunleri = $siparis->siparisUrunleri->filter(function($item) use ($magaza) {
                                                    return $item->urun && $item->urun->magaza_id == $magaza->id;
                                                });
                                                $toplamKazanc += $magazaUrunleri->sum(function($item) {
                                                    return $item->adet * $item->birim_fiyat;
                                                });
                                            }
                                        @endphp
                                        <h4>₺{{ number_format($toplamKazanc, 2) }}</h4>
                                        <small>Toplam Kazanç</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Henüz hiç sipariş bulunmuyor</h5>
                            <p class="text-muted">Ürünleriniz sipariş edildiğinde buradan takip edebilirsiniz.</p>
                            <a href="{{ route('satici.urunler') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Ürün Ekle
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

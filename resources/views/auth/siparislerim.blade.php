@extends('layouts.app')

@section('title', 'Siparişlerim')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4><i class="fas fa-shopping-bag"></i> Siparişlerim</h4>
                    <a href="{{ route('profil') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Profile Dön
                    </a>
                </div>
                <div class="card-body">
                    @if($siparisler->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Sipariş No</th>
                                        <th>Tarih</th>
                                        <th>Ürünler</th>
                                        <th>Toplam Tutar</th>
                                        <th>Durum</th>
                                        <th>Ödeme Durumu</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($siparisler as $siparis)
                                    <tr>
                                        <td>
                                            <strong>#{{ $siparis->id }}</strong>
                                        </td>
                                        <td>
                                            {{ $siparis->created_at->format('d.m.Y H:i') }}
                                            <br>
                                            <small class="text-muted">
                                                {{ $siparis->created_at->diffForHumans() }}
                                            </small>
                                        </td>
                                        <td>
                                            @if($siparis->siparisUrunleri->count() > 0)
                                                @foreach($siparis->siparisUrunleri as $item)
                                                    <div class="mb-1">
                                                        <strong>{{ $item->urun->ad ?? 'Ürün' }}</strong>
                                                        <br>
                                                        <small class="text-muted">
                                                            Adet: {{ $item->adet }} × {{ number_format($item->birim_fiyat, 2) }} TL
                                                        </small>
                                                    </div>
                                                    @if(!$loop->last) <hr class="my-1"> @endif
                                                @endforeach
                                            @else
                                                <span class="text-muted">Ürün bilgisi yok</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong class="text-primary">
                                                {{ number_format($siparis->toplam_tutar, 2) }} TL
                                            </strong>
                                        </td>
                                        <td>
                                            @php
                                                $durumRenk = [
                                                    'beklemede' => 'warning',
                                                    'onaylandi' => 'success',
                                                    'kargoda' => 'info',
                                                    'teslim_edildi' => 'primary',
                                                    'iptal' => 'danger'
                                                ][$siparis->durum] ?? 'secondary';
                                                
                                                $durumMetin = [
                                                    'beklemede' => 'Beklemede',
                                                    'onaylandi' => 'Onaylandı',
                                                    'kargoda' => 'Kargoda',
                                                    'teslim_edildi' => 'Teslim Edildi',
                                                    'iptal' => 'İptal'
                                                ][$siparis->durum] ?? ucfirst($siparis->durum);
                                            @endphp
                                            <span class="badge bg-{{ $durumRenk }}">{{ $durumMetin }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $odemeDurumRenk = [
                                                    'pending' => 'warning',
                                                    'completed' => 'success',
                                                    'failed' => 'danger'
                                                ][$siparis->payment_status] ?? 'secondary';
                                                
                                                $odemeDurumMetin = [
                                                    'pending' => 'Bekliyor',
                                                    'completed' => 'Tamamlandı',
                                                    'failed' => 'Başarısız'
                                                ][$siparis->payment_status] ?? ucfirst($siparis->payment_status);
                                            @endphp
                                            <span class="badge bg-{{ $odemeDurumRenk }}">{{ $odemeDurumMetin }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-info" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#siparisDetay{{ $siparis->id }}">
                                                    <i class="fas fa-eye"></i> Detay
                                                </button>
                                                @if($siparis->payment_status == 'pending')
                                                <a href="{{ route('sepet.odeme') }}" class="btn btn-sm btn-outline-warning">
                                                    <i class="fas fa-credit-card"></i> Öde
                                                </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($siparisler->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $siparisler->links() }}
                        </div>
                        @endif

                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Henüz bir siparişiniz bulunmuyor</h5>
                            <p class="text-muted">Alışverişe başlamak için ürünleri incelemeye başlayın!</p>
                            <a href="{{ route('home') }}" class="btn btn-primary">
                                <i class="fas fa-shopping-cart"></i> Alışverişe Başla
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sipariş Detay Modallari -->
@foreach($siparisler as $siparis)
<div class="modal fade" id="siparisDetay{{ $siparis->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-receipt"></i> Sipariş Detayı #{{ $siparis->id }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-info-circle"></i> Sipariş Bilgileri</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td><strong>Sipariş No:</strong></td>
                                <td>#{{ $siparis->id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tarih:</strong></td>
                                <td>{{ $siparis->created_at->format('d.m.Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Durum:</strong></td>
                                <td>
                                    @php
                                        $durumRenk = [
                                            'beklemede' => 'warning',
                                            'onaylandi' => 'success',
                                            'kargoda' => 'info',
                                            'teslim_edildi' => 'primary',
                                            'iptal' => 'danger'
                                        ][$siparis->durum] ?? 'secondary';
                                        
                                        $durumMetin = [
                                            'beklemede' => 'Beklemede',
                                            'onaylandi' => 'Onaylandı',
                                            'kargoda' => 'Kargoda',
                                            'teslim_edildi' => 'Teslim Edildi',
                                            'iptal' => 'İptal'
                                        ][$siparis->durum] ?? ucfirst($siparis->durum);
                                    @endphp
                                    <span class="badge bg-{{ $durumRenk }}">{{ $durumMetin }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Ödeme Durumu:</strong></td>
                                <td>
                                    @php
                                        $odemeDurumRenk = [
                                            'pending' => 'warning',
                                            'completed' => 'success',
                                            'failed' => 'danger'
                                        ][$siparis->payment_status] ?? 'secondary';
                                        
                                        $odemeDurumMetin = [
                                            'pending' => 'Bekliyor',
                                            'completed' => 'Tamamlandı',
                                            'failed' => 'Başarısız'
                                        ][$siparis->payment_status] ?? ucfirst($siparis->payment_status);
                                    @endphp
                                    <span class="badge bg-{{ $odemeDurumRenk }}">{{ $odemeDurumMetin }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-truck"></i> Teslimat Bilgileri</h6>
                        <div class="bg-light p-3 rounded">
                            @if($siparis->teslimat_adresi)
                                <p class="mb-1"><strong>Adres:</strong></p>
                                <p class="mb-2">{{ $siparis->teslimat_adresi }}</p>
                            @endif
                            @if($siparis->teslimat_telefonu)
                                <p class="mb-1"><strong>Telefon:</strong></p>
                                <p class="mb-0">{{ $siparis->teslimat_telefonu }}</p>
                            @endif
                            @if(!$siparis->teslimat_adresi && !$siparis->teslimat_telefonu)
                                <p class="text-muted mb-0">Teslimat bilgisi yok</p>
                            @endif
                        </div>
                    </div>
                </div>

                <hr>

                <h6><i class="fas fa-list"></i> Sipariş Edilen Ürünler</h6>
                @if($siparis->siparisUrunleri->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Ürün</th>
                                    <th>Adet</th>
                                    <th>Birim Fiyat</th>
                                    <th>Toplam</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($siparis->siparisUrunleri as $item)
                                <tr>
                                    <td>{{ $item->urun->ad ?? 'Ürün' }}</td>
                                    <td>{{ $item->adet }}</td>
                                    <td>{{ number_format($item->birim_fiyat, 2) }} TL</td>
                                    <td><strong>{{ number_format($item->adet * $item->birim_fiyat, 2) }} TL</strong></td>
                                </tr>
                                @endforeach
                                <tr class="table-warning">
                                    <td colspan="3"><strong>Genel Toplam</strong></td>
                                    <td><strong>{{ number_format($siparis->toplam_tutar, 2) }} TL</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">Ürün bilgisi bulunamadı.</p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                @if($siparis->payment_status == 'pending')
                <a href="{{ route('sepet.odeme') }}" class="btn btn-warning">
                    <i class="fas fa-credit-card"></i> Ödeme Yap
                </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection

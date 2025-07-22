@extends('layouts.app')

@section('title', 'Mağaza Yönetimi')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-store"></i> Mağaza Yönetimi</h2>
                <div>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Tüm Mağazalar ({{ $magazalar->count() }} mağaza)</h5>
                </div>
                <div class="card-body">
                    @if($magazalar->isEmpty())
                        <div class="text-center py-4">
                            <i class="fas fa-store fa-3x text-muted mb-3"></i>
                            <h5>Henüz mağaza bulunmuyor</h5>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Mağaza Adı</th>
                                        <th>Açıklama</th>
                                        <th>Sahip</th>
                                        <th>Ürün Sayısı</th>
                                        <th>Toplam Satış</th>
                                        <th>Oluşturma Tarihi</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($magazalar as $magaza)
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>{{ $magaza->ad }}</strong>
                                                    @if($magaza->urunler->count() > 0)
                                                        <span class="badge bg-success ms-2">Aktif</span>
                                                    @else
                                                        <span class="badge bg-secondary ms-2">Pasif</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if($magaza->aciklama)
                                                    {{ Str::limit($magaza->aciklama, 100) }}
                                                @else
                                                    <span class="text-muted">Açıklama yok</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $magaza->kullanici->ad ?? 'Kullanıcı yok' }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $magaza->kullanici->eposta ?? '' }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">
                                                    {{ $magaza->urunler->count() }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $toplamSatis = $magaza->urunler->sum(function($urun) {
                                                        return $urun->siparisUrunleri->sum('miktar');
                                                    });
                                                @endphp
                                                <span class="badge bg-success">{{ $toplamSatis }}</span>
                                            </td>
                                            <td>{{ $magaza->created_at->format('d.m.Y H:i') }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.magaza.goruntule', $magaza->id) }}" class="btn btn-outline-info" title="Görüntüle">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.magaza.duzenle', $magaza->id) }}" class="btn btn-outline-warning" title="Düzenle">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if($magaza->urunler->count() == 0)
                                                        <form method="POST" action="{{ route('admin.magaza.sil', $magaza->id) }}" style="display: inline;" onsubmit="return confirm('Bu mağazayı silmek istediğinizden emin misiniz?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger" title="Sil">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <button class="btn btn-outline-secondary" disabled title="Bu mağazada ürün var, silinemez">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- En Başarılı Mağazalar -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>En Başarılı Mağazalar</h5>
                </div>
                <div class="card-body">
                    @if($magazalar->isNotEmpty())
                        <div class="row">
                            @foreach($magazalar->sortByDesc(function($magaza) { 
                                return $magaza->urunler->sum(function($urun) {
                                    return $urun->siparisUrunleri->sum('miktar');
                                });
                            })->take(6) as $magaza)
                                <div class="col-md-4 mb-3">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h6>{{ $magaza->ad }}</h6>
                                            @php
                                                $magazaSatis = $magaza->urunler->sum(function($urun) {
                                                    return $urun->siparisUrunleri->sum('miktar');
                                                });
                                            @endphp
                                            <h4 class="text-success">{{ $magazaSatis }}</h4>
                                            <small class="text-muted">satış</small>
                                            <div class="mt-2">
                                                <small class="text-info">{{ $magaza->urunler->count() }} ürün</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted">
                            <p>Henüz mağaza bulunmuyor</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- İstatistik Kartları -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h4 class="text-primary">{{ $magazalar->count() }}</h4>
                    <small class="text-muted">Toplam Mağaza</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h4 class="text-success">{{ $magazalar->filter(function($m) { return $m->urunler->count() > 0; })->count() }}</h4>
                    <small class="text-muted">Aktif Mağaza</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h4 class="text-warning">{{ $magazalar->filter(function($m) { return $m->urunler->count() == 0; })->count() }}</h4>
                    <small class="text-muted">Pasif Mağaza</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h4 class="text-info">{{ $magazalar->sum(function($m) { return $m->urunler->count(); }) }}</h4>
                    <small class="text-muted">Toplam Ürün</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

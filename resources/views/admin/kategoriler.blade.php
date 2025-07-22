@extends('layouts.app')

@section('title', 'Kategori Yönetimi')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-tags"></i> Kategori Yönetimi</h2>
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
                    <h5>Tüm Kategoriler ({{ $kategoriler->count() }} kategori)</h5>
                </div>
                <div class="card-body">
                    @if($kategoriler->isEmpty())
                        <div class="text-center py-4">
                            <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                            <h5>Henüz kategori bulunmuyor</h5>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Kategori Adı</th>
                                        <th>Açıklama</th>
                                        <th>Ürün Sayısı</th>
                                        <th>Oluşturma Tarihi</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($kategoriler as $kategori)
                                        <tr>
                                            <td>{{ $kategori->id }}</td>
                                            <td>
                                                <strong>{{ $kategori->ad }}</strong>
                                            </td>
                                            <td>
                                                @if($kategori->aciklama)
                                                    {{ Str::limit($kategori->aciklama, 100) }}
                                                @else
                                                    <span class="text-muted">Açıklama yok</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">
                                                    {{ $kategori->urunler->count() }}
                                                </span>
                                            </td>
                                            <td>{{ $kategori->created_at->format('d.m.Y H:i') }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-info" title="Görüntüle">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-outline-warning" title="Düzenle">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    @if($kategori->urunler->count() == 0)
                                                        <button class="btn btn-outline-danger" title="Sil">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @else
                                                        <button class="btn btn-outline-secondary" disabled title="Bu kategoride ürün var, silinemez">
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

    <!-- Kategori Ürün Dağılımı -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Kategori Ürün Dağılımı</h5>
                </div>
                <div class="card-body">
                    @if($kategoriler->isNotEmpty())
                        <div class="row">
                            @foreach($kategoriler->sortByDesc(function($kategori) { return $kategori->urunler->count(); })->take(6) as $kategori)
                                <div class="col-md-4 mb-3">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h5>{{ $kategori->ad }}</h5>
                                            <h3 class="text-primary">{{ $kategori->urunler->count() }}</h3>
                                            <small class="text-muted">ürün</small>
                                            @if($kategori->urunler->count() > 0)
                                                <div class="progress mt-2" style="height: 5px;">
                                                    @php
                                                        $maxUrunSayisi = $kategoriler->max(function($k) { return $k->urunler->count(); });
                                                        $percentage = $maxUrunSayisi > 0 ? ($kategori->urunler->count() / $maxUrunSayisi) * 100 : 0;
                                                    @endphp
                                                    <div class="progress-bar bg-primary" style="width: {{ $percentage }}%"></div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted">
                            <p>Henüz kategori bulunmuyor</p>
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
                    <h4 class="text-primary">{{ $kategoriler->count() }}</h4>
                    <small class="text-muted">Toplam Kategori</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h4 class="text-success">{{ $kategoriler->filter(function($k) { return $k->urunler->count() > 0; })->count() }}</h4>
                    <small class="text-muted">Aktif Kategori</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h4 class="text-warning">{{ $kategoriler->filter(function($k) { return $k->urunler->count() == 0; })->count() }}</h4>
                    <small class="text-muted">Boş Kategori</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h4 class="text-info">{{ $kategoriler->sum(function($k) { return $k->urunler->count(); }) }}</h4>
                    <small class="text-muted">Toplam Ürün</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

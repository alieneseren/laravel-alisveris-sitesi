@extends('layouts.app')

@section('title', 'Ürün Yönetimi')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-boxes"></i> Ürün Yönetimi</h2>
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
                    <h5>Tüm Ürünler ({{ $urunler->total() }} ürün)</h5>
                </div>
                <div class="card-body">
                    @if($urunler->isEmpty())
                        <div class="text-center py-4">
                            <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                            <h5>Henüz ürün bulunmuyor</h5>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Görsel</th>
                                        <th>Ürün Adı</th>
                                        <th>Fiyat</th>
                                        <th>Stok</th>
                                        <th>Kategori</th>
                                        <th>Mağaza</th>
                                        <th>Durum</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($urunler as $urun)
                                        <tr>
                                            <td>
                                                @if($urun->urunGorselleri->first())
                                                    <img src="{{ asset('uploads/' . $urun->urunGorselleri->first()->gorsel_yolu) }}" 
                                                         alt="{{ $urun->ad }}" 
                                                         style="width: 50px; height: 50px; object-fit: cover;"
                                                         class="rounded">
                                                @else
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                         style="width: 50px; height: 50px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $urun->ad }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ Str::limit($urun->aciklama, 50) }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <strong>{{ number_format($urun->fiyat, 2) }} ₺</strong>
                                            </td>
                                            <td>
                                                @if($urun->stok <= 5)
                                                    <span class="badge bg-danger">{{ $urun->stok }}</span>
                                                @elseif($urun->stok <= 10)
                                                    <span class="badge bg-warning">{{ $urun->stok }}</span>
                                                @else
                                                    <span class="badge bg-success">{{ $urun->stok }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $urun->kategori->ad ?? 'Kategori yok' }}</td>
                                            <td>{{ $urun->magaza->ad ?? 'Mağaza yok' }}</td>
                                            <td>
                                                @if($urun->stok > 0)
                                                    <span class="badge bg-success">Stokta</span>
                                                @else
                                                    <span class="badge bg-danger">Stok yok</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.urun.goruntule', $urun->id) }}" class="btn btn-outline-info" title="Görüntüle">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.urun.duzenle', $urun->id) }}" class="btn btn-outline-warning" title="Düzenle">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route('admin.urun.sil', $urun->id) }}" style="display: inline;" onsubmit="return confirm('Bu ürünü silmek istediğinizden emin misiniz?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger" title="Sil">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $urunler->links() }}
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
                    <h4 class="text-primary">{{ $urunler->count() }}</h4>
                    <small class="text-muted">Toplam Ürün</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h4 class="text-success">{{ $urunler->where('stok', '>', 0)->count() }}</h4>
                    <small class="text-muted">Stokta Var</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h4 class="text-danger">{{ $urunler->where('stok', 0)->count() }}</h4>
                    <small class="text-muted">Stok Yok</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h4 class="text-warning">{{ $urunler->where('stok', '<=', 5)->where('stok', '>', 0)->count() }}</h4>
                    <small class="text-muted">Kritik Stok</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

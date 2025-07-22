@extends('layouts.app')

@section('title', 'Kullanıcı Yönetimi')

@section('content')
<div class="container">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-users"></i> Kullanıcı Yönetimi</h2>
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
                    <h5>Tüm Kullanıcılar ({{ $kullanicilar->total() }} kullanıcı)</h5>
                </div>
                <div class="card-body">
                    @if($kullanicilar->isEmpty())
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5>Henüz kullanıcı bulunmuyor</h5>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Ad Soyad</th>
                                        <th>E-posta</th>
                                        <th>Rol</th>
                                        <th>Kayıt Tarihi</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($kullanicilar as $kullanici)
                                        <tr>
                                            <td>{{ $kullanici->id }}</td>
                                            <td>{{ $kullanici->ad }}</td>
                                            <td>{{ $kullanici->eposta }}</td>
                                            <td>
                                                @php
                                                    $rolBadge = [
                                                        'yonetici' => 'danger',
                                                        'satici' => 'warning',
                                                        'musteri' => 'primary'
                                                    ][$kullanici->rol] ?? 'secondary';
                                                    
                                                    $rolAdi = [
                                                        'yonetici' => 'Yönetici',
                                                        'satici' => 'Satıcı',
                                                        'musteri' => 'Müşteri'
                                                    ][$kullanici->rol] ?? 'Bilinmiyor';
                                                @endphp
                                                <span class="badge bg-{{ $rolBadge }}">{{ $rolAdi }}</span>
                                            </td>
                                            <td>{{ $kullanici->created_at->format('d.m.Y H:i') }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.kullanici.goruntule', $kullanici->id) }}" class="btn btn-outline-info" title="Görüntüle">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.kullanici.duzenle', $kullanici->id) }}" class="btn btn-outline-warning" title="Düzenle">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if($kullanici->rol !== 'yonetici')
                                                        <form method="POST" action="{{ route('admin.kullanici.sil', $kullanici->id) }}" 
                                                              style="display: inline;" 
                                                              onsubmit="return confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger" title="Sil">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $kullanicilar->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- İstatistik Kartları -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h4 class="text-danger">{{ $kullanicilar->where('rol', 'yonetici')->count() }}</h4>
                    <small class="text-muted">Yönetici</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h4 class="text-warning">{{ $kullanicilar->where('rol', 'satici')->count() }}</h4>
                    <small class="text-muted">Satıcı</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h4 class="text-primary">{{ $kullanicilar->where('rol', 'musteri')->count() }}</h4>
                    <small class="text-muted">Müşteri</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

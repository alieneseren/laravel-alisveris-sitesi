@extends('layouts.app')

@section('title', 'Kullanıcı Düzenle')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-user-edit"></i> Kullanıcı Düzenle</h2>
                <div>
                    <a href="{{ route('admin.kullanici.goruntule', $kullanici->id) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Geri
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-edit"></i> Kullanıcı Bilgilerini Düzenle</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.kullanici.duzenle.post', $kullanici->id) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="ad" class="form-label">Ad Soyad <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('ad') is-invalid @enderror" 
                                   id="ad" 
                                   name="ad" 
                                   value="{{ old('ad', $kullanici->ad) }}" 
                                   required>
                            @error('ad')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="eposta" class="form-label">E-posta <span class="text-danger">*</span></label>
                            <input type="email" 
                                   class="form-control @error('eposta') is-invalid @enderror" 
                                   id="eposta" 
                                   name="eposta" 
                                   value="{{ old('eposta', $kullanici->eposta) }}" 
                                   required>
                            @error('eposta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="rol" class="form-label">Rol <span class="text-danger">*</span></label>
                            <select class="form-control @error('rol') is-invalid @enderror" 
                                    id="rol" 
                                    name="rol" 
                                    required>
                                <option value="">Rol Seçin</option>
                                <option value="yonetici" {{ old('rol', $kullanici->rol) == 'yonetici' ? 'selected' : '' }}>Yönetici</option>
                                <option value="satici" {{ old('rol', $kullanici->rol) == 'satici' ? 'selected' : '' }}>Satıcı</option>
                                <option value="musteri" {{ old('rol', $kullanici->rol) == 'musteri' ? 'selected' : '' }}>Müşteri</option>
                            </select>
                            @error('rol')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="telefon" class="form-label">Telefon</label>
                            <input type="text" 
                                   class="form-control @error('telefon') is-invalid @enderror" 
                                   id="telefon" 
                                   name="telefon" 
                                   value="{{ old('telefon', $kullanici->telefon) }}">
                            @error('telefon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="adres" class="form-label">Adres</label>
                            <textarea class="form-control @error('adres') is-invalid @enderror" 
                                      id="adres" 
                                      name="adres" 
                                      rows="3">{{ old('adres', $kullanici->adres) }}</textarea>
                            @error('adres')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Kaydet
                            </button>
                            <a href="{{ route('admin.kullanici.goruntule', $kullanici->id) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> İptal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6><i class="fas fa-info-circle"></i> Bilgi</h6>
                </div>
                <div class="card-body">
                    <h6>Kullanıcı Rolleri:</h6>
                    <ul>
                        <li><strong>Yönetici:</strong> Tüm yönetim yetkilerine sahip</li>
                        <li><strong>Satıcı:</strong> Mağaza ve ürün yönetebilir</li>
                        <li><strong>Müşteri:</strong> Sadece alışveriş yapabilir</li>
                    </ul>
                    
                    <div class="alert alert-warning">
                        <small>
                            <strong>Dikkat:</strong> Rol değişikliği kullanıcının sistemdeki yetkilerini etkiler.
                        </small>
                    </div>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <h6><i class="fas fa-clock"></i> Kayıt Bilgileri</h6>
                </div>
                <div class="card-body">
                    <p><strong>Kayıt Tarihi:</strong><br>{{ $kullanici->created_at->format('d.m.Y H:i') }}</p>
                    <p><strong>Son Güncelleme:</strong><br>{{ $kullanici->updated_at->format('d.m.Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

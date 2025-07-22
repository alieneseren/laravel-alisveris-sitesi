@extends('layouts.app')

@section('title', 'Profil Düzenle')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-edit"></i> Profil Düzenle</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('profil.duzenle.post') }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ad" class="form-label">Ad Soyad <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('ad') is-invalid @enderror" 
                                           id="ad" name="ad" value="{{ old('ad', $kullanici->ad) }}" required>
                                    @error('ad')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="eposta" class="form-label">E-posta <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('eposta') is-invalid @enderror" 
                                           id="eposta" name="eposta" value="{{ old('eposta', $kullanici->eposta) }}" required>
                                    @error('eposta')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="telefon" class="form-label">Telefon</label>
                                    <input type="tel" class="form-control @error('telefon') is-invalid @enderror" 
                                           id="telefon" name="telefon" value="{{ old('telefon', $kullanici->telefon) }}">
                                    @error('telefon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="rol" class="form-label">Rol</label>
                                    <input type="text" class="form-control" value="{{ ucfirst($kullanici->rol) }}" disabled>
                                    <small class="form-text text-muted">Rol değiştirilemez</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="adres" class="form-label">Adres</label>
                            <textarea class="form-control @error('adres') is-invalid @enderror" 
                                      id="adres" name="adres" rows="3">{{ old('adres', $kullanici->adres) }}</textarea>
                            @error('adres')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('profil') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Geri Dön
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Güncelle
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

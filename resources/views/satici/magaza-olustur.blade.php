@extends('layouts.app')

@section('title', 'Mağaza Oluştur')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-store"></i> Yeni Mağaza Oluştur</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('satici.magaza.olustur.post') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="magaza_adi" class="form-label">Mağaza Adı <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('magaza_adi') is-invalid @enderror" 
                                   id="magaza_adi" name="magaza_adi" value="{{ old('magaza_adi') }}" required>
                            @error('magaza_adi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Müşterilerinizin göreceği mağaza adınız</div>
                        </div>

                        <div class="mb-3">
                            <label for="magaza_aciklamasi" class="form-label">Mağaza Açıklaması <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('magaza_aciklamasi') is-invalid @enderror" 
                                      id="magaza_aciklamasi" name="magaza_aciklamasi" rows="4" required>{{ old('magaza_aciklamasi') }}</textarea>
                            @error('magaza_aciklamasi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Mağazanız hakkında kısa bir açıklama yazın</div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Bilgi:</strong> Mağazanızı oluşturduktan sonra ürün eklemeye başlayabilirsiniz.
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('satici.dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Geri Dön
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Mağaza Oluştur
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

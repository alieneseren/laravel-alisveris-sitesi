@extends('layouts.app')

@section('title', 'Şifre Değiştir')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-key"></i> Şifre Değiştir</h4>
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

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Güvenlik uyarısı:</strong> Şifrenizi değiştirdikten sonra tüm cihazlarda yeniden giriş yapmanız gerekebilir.
                    </div>

                    <form method="POST" action="{{ route('sifre.degistir.post') }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="mevcut_sifre" class="form-label">Mevcut Şifre <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('mevcut_sifre') is-invalid @enderror" 
                                       id="mevcut_sifre" name="mevcut_sifre" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('mevcut_sifre')">
                                    <i class="fas fa-eye" id="mevcut_sifre_icon"></i>
                                </button>
                                @error('mevcut_sifre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="yeni_sifre" class="form-label">Yeni Şifre <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('yeni_sifre') is-invalid @enderror" 
                                       id="yeni_sifre" name="yeni_sifre" required minlength="6">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('yeni_sifre')">
                                    <i class="fas fa-eye" id="yeni_sifre_icon"></i>
                                </button>
                                @error('yeni_sifre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">Şifreniz en az 6 karakter olmalıdır.</small>
                        </div>

                        <div class="mb-3">
                            <label for="yeni_sifre_confirmation" class="form-label">Yeni Şifre Tekrar <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" 
                                       id="yeni_sifre_confirmation" name="yeni_sifre_confirmation" required minlength="6">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('yeni_sifre_confirmation')">
                                    <i class="fas fa-eye" id="yeni_sifre_confirmation_icon"></i>
                                </button>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('profil') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Geri Dön
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Şifreyi Değiştir
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '_icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
@endsection

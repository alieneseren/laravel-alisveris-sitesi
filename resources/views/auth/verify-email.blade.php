@extends('layouts.app')

@section('title', 'E-posta Doğrulama')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-envelope-open"></i> E-posta Doğrulama</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->has('general'))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> {{ $errors->first('general') }}
                        </div>
                    @endif

                    <div class="text-center mb-4">
                        <i class="fas fa-lock text-primary" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">E-posta Adresinizi Doğrulayın</h5>
                        <p class="text-muted">
                            E-posta adresinize gönderilen 6 haneli doğrulama kodunu aşağıya girin.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('verify-email-post') }}">
                        @csrf
                        <div class="mb-4">
                            <label for="otp" class="form-label">Doğrulama Kodu</label>
                            <input type="text" 
                                   class="form-control text-center @error('otp') is-invalid @enderror" 
                                   id="otp" 
                                   name="otp" 
                                   placeholder="123456"
                                   maxlength="6"
                                   style="font-size: 1.5rem; letter-spacing: 0.5rem;"
                                   required>
                            @error('otp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fas fa-clock"></i> Kod 10 dakika süreyle geçerlidir.
                            </small>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-check"></i> Doğrula
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <p class="text-muted">Kod gelmedi mi?</p>
                        <form method="POST" action="{{ route('resend-otp') }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary">
                                <i class="fas fa-redo"></i> Yeni Kod Gönder
                            </button>
                        </form>
                    </div>

                    <div class="text-center mt-3">
                        <a href="{{ route('home') }}" class="btn btn-link">
                            <i class="fas fa-arrow-left"></i> Anasayfaya Dön
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // OTP input'u için sadece rakam girişi
    const otpInput = document.getElementById('otp');
    
    otpInput.addEventListener('input', function(e) {
        // Sadece rakamları tut
        e.target.value = e.target.value.replace(/[^0-9]/g, '');
        
        // 6 karakter sınırı
        if (e.target.value.length > 6) {
            e.target.value = e.target.value.slice(0, 6);
        }
    });
    
    // Otomatik odaklanma
    otpInput.focus();
});
</script>
@endsection

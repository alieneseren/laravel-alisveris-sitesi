@extends('layouts.app')

@section('title', 'Paythor API Yönetimi')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-2">
            <div class="admin-sidebar">
                <h4>Admin Panel</h4>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.kullanicilar') }}">
                            <i class="fas fa-users"></i> Kullanıcılar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.kategoriler') }}">
                            <i class="fas fa-tags"></i> Kategoriler
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.urunler') }}">
                            <i class="fas fa-boxes"></i> Ürünler
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.siparisler') }}">
                            <i class="fas fa-shopping-cart"></i> Siparişler
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.magazalar') }}">
                            <i class="fas fa-store"></i> Mağazalar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('admin.paythor.api') }}">
                            <i class="fas fa-credit-card"></i> Paythor API
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="col-md-10">
            <div class="admin-content">
                <h1><i class="fas fa-credit-card"></i> Paythor API Yönetimi</h1>
                
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info">
                        {{ session('info') }}
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-api"></i> API Bağlantı Durumu</h5>
                            </div>
                            <div class="card-body">
                                @if(session('pending_paythor_email'))
                                    {{-- OTP Doğrulama Formu --}}
                                    <div class="alert alert-info">
                                        <i class="fas fa-mobile-alt"></i>
                                        <strong>OTP Doğrulama Gerekli!</strong> 
                                        <strong>{{ session('pending_paythor_email') }}</strong> adresine gönderilen kodu girin.
                                    </div>
                                    
                                    <h6>OTP Kodunu Girin:</h6>
                                    <form method="POST" action="{{ route('admin.paythor.api.otp') }}">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="otp" class="form-label">OTP Kodu</label>
                                            <input type="text" 
                                                   class="form-control @error('otp') is-invalid @enderror" 
                                                   id="otp" 
                                                   name="otp" 
                                                   maxlength="8"
                                                   required 
                                                   autocomplete="one-time-code"
                                                   placeholder="Örnek: 123456">
                                            @error('otp')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-check"></i> OTP ile Doğrula
                                            </button>
                                            <a href="{{ route('admin.paythor.api') }}" class="btn btn-secondary"
                                               onclick="event.preventDefault(); 
                                                        fetch('{{ route('admin.paythor.api.sil') }}', {method: 'DELETE', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}})
                                                        .then(() => location.reload());">
                                                <i class="fas fa-times"></i> İptal Et
                                            </a>
                                        </div>
                                    </form>
                                @elseif(empty($paythorToken))
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <strong>API Bağlı Değil!</strong> 
                                        Paythor API'si henüz bağlanmamış. Müşteriler ödeme yapabilmesi için API'yi bağlamanız gerekiyor.
                                    </div>
                                    
                                    <h6>Paythor API Bilgilerini Girin:</h6>
                                    <form method="POST" action="{{ route('admin.paythor.api.kaydet') }}">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="paythor_email" class="form-label">Paythor E-posta</label>
                                            <input type="email" 
                                                   class="form-control @error('paythor_email') is-invalid @enderror" 
                                                   id="paythor_email" 
                                                   name="paythor_email" 
                                                   value="{{ old('paythor_email') }}" 
                                                   required>
                                            @error('paythor_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="paythor_password" class="form-label">Paythor Şifre</label>
                                            <input type="password" 
                                                   class="form-control @error('paythor_password') is-invalid @enderror" 
                                                   id="paythor_password" 
                                                   name="paythor_password" 
                                                   required>
                                            @error('paythor_password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-plug"></i> API'yi Bağla
                                        </button>
                                    </form>
                                @else
                                    <div class="alert alert-success">
                                        <i class="fas fa-check-circle"></i>
                                        <strong>API Başarıyla Bağlandı!</strong>
                                        Paythor API aktif ve müşteriler ödeme yapabilir.
                                    </div>
                                    
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Bağlı E-posta:</strong></td>
                                            <td>{{ $paythorEmail }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Token Durumu:</strong></td>
                                            <td><span class="badge bg-success">Aktif</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Token (İlk 20 karakter):</strong></td>
                                            <td><code>{{ substr($paythorToken, 0, 20) }}...</code></td>
                                        </tr>
                                    </table>
                                    
                                    <div class="mt-3">
                                        <form method="POST" action="{{ route('admin.paythor.api.sil') }}" 
                                              style="display: inline;" 
                                              onsubmit="return confirm('API bağlantısını kaldırmak istediğinizden emin misiniz? Müşteriler ödeme yapamayacak!')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-unlink"></i> API Bağlantısını Kaldır
                                            </button>
                                        </form>
                                        
                                        <button type="button" class="btn btn-warning" onclick="location.reload()">
                                            <i class="fas fa-sync"></i> Durumu Yenile
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h6><i class="fas fa-info-circle"></i> Bilgi</h6>
                            </div>
                            <div class="card-body">
                                <h6>Paythor API Nedir?</h6>
                                <p>Paythor, müşterilerinizin güvenli bir şekilde ödeme yapabilmesi için kullanılan ödeme gateway'idir.</p>
                                
                                <h6>Nasıl Çalışır?</h6>
                                <ul>
                                    <li>Admin olarak Paythor hesabınızla giriş yapın</li>
                                    <li>Sistem otomatik olarak API token'ı alır</li>
                                    <li>Müşteriler sepetlerinde ödeme yapabilir</li>
                                    <li>Ödemeler güvenli olarak işlenir</li>
                                </ul>
                                
                                <h6>Önemli Notlar:</h6>
                                <div class="alert alert-info">
                                    <small>
                                        • API bağlanmadığında müşteriler ödeme yapamaz<br>
                                        • Token güvenlik nedeniyle kısmen gizlenir<br>
                                        • API hesabınızın aktif olduğundan emin olun
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card mt-3">
                            <div class="card-header">
                                <h6><i class="fas fa-chart-line"></i> API İstatistikleri</h6>
                            </div>
                            <div class="card-body">
                                @if(!empty($paythorToken))
                                    <div class="text-center">
                                        <h4 class="text-success">{{ date('d.m.Y H:i') }}</h4>
                                        <small>Son bağlantı zamanı</small>
                                    </div>
                                    
                                    <hr>
                                    
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <h5 class="text-primary">Aktif</h5>
                                            <small>Durum</small>
                                        </div>
                                        <div class="col-6">
                                            <h5 class="text-info">Global</h5>
                                            <small>Kapsam</small>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center text-muted">
                                        <i class="fas fa-chart-line fa-3x mb-3"></i>
                                        <p>API bağlandıktan sonra istatistikler görünecek</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

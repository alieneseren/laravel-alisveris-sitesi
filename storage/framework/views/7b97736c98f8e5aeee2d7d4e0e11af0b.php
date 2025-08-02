

<?php $__env->startSection('title', 'Kart Bilgileri - Stripe Ödeme'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Progress Bar -->
            <div class="card border-0 mb-4">
                <div class="card-body p-2">
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 100%"></div>
                    </div>
                    <div class="row text-center mt-2">
                        <div class="col-6">
                            <span class="badge bg-success">✓ Teslimat Bilgileri</span>
                        </div>
                        <div class="col-6">
                            <span class="badge bg-primary">2. Kart Bilgileri</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Sol Taraf - Sipariş ve Adres Özeti -->
                <div class="col-md-5">
                    <!-- Sipariş Özeti -->
                    <div class="card shadow-sm mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="fas fa-shopping-cart me-2"></i>
                                Sipariş Özeti
                            </h6>
                        </div>
                        <div class="card-body">
                            <?php $__currentLoopData = $sepetUranlari; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                <div>
                                    <small class="fw-bold"><?php echo e($item['urun']->ad); ?></small>
                                    <br>
                                    <small class="text-muted"><?php echo e($item['miktar']); ?> adet × <?php echo e(number_format($item['urun']->fiyat, 2)); ?> ₺</small>
                                </div>
                                <span class="fw-bold"><?php echo e(number_format($item['ara_toplam'], 2)); ?> ₺</span>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            
                            <div class="d-flex justify-content-between align-items-center pt-2">
                                <h6 class="mb-0 text-primary">Toplam:</h6>
                                <h5 class="mb-0 text-success"><?php echo e(number_format($toplam, 2)); ?> ₺</h5>
                            </div>
                        </div>
                    </div>

                    <!-- Teslimat Bilgileri -->
                    <div class="card shadow-sm mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="fas fa-map-marker-alt me-2"></i>
                                Teslimat Bilgileri
                            </h6>
                        </div>
                        <div class="card-body">
                            <strong><?php echo e($addressData['ad_soyad']); ?></strong><br>
                            <small class="text-muted"><?php echo e($addressData['telefon']); ?></small><br>
                            <small class="text-muted"><?php echo e($addressData['email']); ?></small><br>
                            <hr class="my-2">
                            <small>
                                <?php echo e($addressData['adres']); ?><br>
                                <?php echo e($addressData['ilce']); ?> / <?php echo e($addressData['il']); ?>

                                <?php if($addressData['posta_kodu']): ?>
                                    <br><?php echo e($addressData['posta_kodu']); ?>

                                <?php endif; ?>
                            </small>
                            <hr class="my-2">
                            <a href="<?php echo e(route('stripe.checkout.form')); ?>" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-edit me-1"></i> Düzenle
                            </a>
                        </div>
                    </div>

                    <!-- Güvenlik Rozetleri -->
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-3">Güvenli Ödeme</h6>
                            <div class="row">
                                <div class="col-4">
                                    <i class="fas fa-shield-alt text-success fs-4"></i>
                                    <small class="d-block text-muted">SSL</small>
                                </div>
                                <div class="col-4">
                                    <i class="fab fa-stripe text-primary fs-4"></i>
                                    <small class="d-block text-muted">Stripe</small>
                                </div>
                                <div class="col-4">
                                    <i class="fas fa-lock text-warning fs-4"></i>
                                    <small class="d-block text-muted">Şifreli</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sağ Taraf - Kart Bilgileri Formu -->
                <div class="col-md-7">
                    <div class="card shadow-lg border-0">
                        <div class="card-header bg-gradient-primary text-white">
                            <h4 class="mb-0">
                                <i class="fas fa-credit-card me-2"></i>
                                Kart Bilgileri
                            </h4>
                            <small class="opacity-75">Güvenli ödeme için kart bilgilerinizi girin</small>
                        </div>
                        
                        <div class="card-body p-4">
                            <?php if(session('error')): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <?php echo e(session('error')); ?>

                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <!-- Ödeme Hatası Gösterme -->
                            <div id="card-errors" class="alert alert-danger d-none" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <span id="error-message"></span>
                            </div>

                            <!-- Başarı Mesajı -->
                            <div id="payment-success" class="alert alert-success d-none" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                <span>Ödemeniz başarıyla tamamlandı! Sipariş oluşturuluyor...</span>
                            </div>

                            <form id="payment-form">
                                <?php echo csrf_field(); ?>
                                
                                <!-- Kart Kabul Edilen Kartlar -->
                                <div class="text-center mb-4">
                                    <h6 class="text-muted mb-3">Kabul Edilen Kartlar</h6>
                                    <div class="d-flex justify-content-center gap-3">
                                        <i class="fab fa-cc-visa fa-2x text-primary"></i>
                                        <i class="fab fa-cc-mastercard fa-2x text-warning"></i>
                                        <i class="fab fa-cc-amex fa-2x text-info"></i>
                                        <i class="fas fa-credit-card fa-2x text-secondary"></i>
                                    </div>
                                </div>

                                <!-- Kart Bilgileri Stripe Elements -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Kart Bilgileri <span class="text-danger">*</span></label>
                                    <div id="card-element" class="form-control form-control-lg stripe-card-element">
                                        <!-- Stripe Elements kart input'u buraya gelecek -->
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Kart bilgileriniz güvenli bir şekilde şifrelenir ve saklanmaz
                                    </small>
                                </div>

                                <!-- Ödeme Butonu -->
                                <div class="d-grid gap-2 mt-4">
                                    <button type="submit" class="btn btn-success btn-lg py-3" id="submit-button">
                                        <i class="fas fa-lock me-2"></i>
                                        <span id="button-text"><?php echo e(number_format($toplam, 2)); ?> ₺ Öde</span>
                                        <div id="spinner" class="spinner-border spinner-border-sm ms-2 d-none" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </button>
                                    
                                    <a href="<?php echo e(route('stripe.checkout.form')); ?>" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>
                                        Adres Bilgilerine Dön
                                    </a>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Footer -->
                        <div class="card-footer bg-light text-center">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt me-1"></i>
                                256-bit SSL şifreleme ile korunur
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.card {
    border-radius: 15px;
    overflow: hidden;
}

.btn-lg {
    border-radius: 10px;
    font-weight: 600;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
}

.btn-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border: none;
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
}

.progress {
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
}

.alert {
    border-radius: 10px;
    border: none;
}

.stripe-card-element {
    padding: 15px;
    background: white;
    border-radius: 8px;
    border: 2px solid #e9ecef;
    transition: border-color 0.3s ease;
}

.stripe-card-element.StripeElement--focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
}

.stripe-card-element.StripeElement--invalid {
    border-color: #dc3545;
}

.stripe-card-element.StripeElement--complete {
    border-color: #28a745;
}
</style>

<!-- Stripe JavaScript SDK -->
<script src="https://js.stripe.com/v3/"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Stripe public key
    const stripe = Stripe('<?php echo e(config("services.stripe.key")); ?>');
    const elements = stripe.elements();
    
    // Türkçe stil
    const style = {
        base: {
            color: '#32325d',
            fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
            fontSmoothing: 'antialiased',
            fontSize: '16px',
            '::placeholder': {
                color: '#aab7c4'
            }
        },
        invalid: {
            color: '#fa755a',
            iconColor: '#fa755a'
        }
    };

    // Kart elementi oluştur
    const cardElement = elements.create('card', {
        style: style,
        hidePostalCode: true
    });
    
    cardElement.mount('#card-element');

    // Form elementleri
    const form = document.getElementById('payment-form');
    const submitButton = document.getElementById('submit-button');
    const buttonText = document.getElementById('button-text');
    const spinner = document.getElementById('spinner');
    const errorElement = document.getElementById('card-errors');
    const successElement = document.getElementById('payment-success');

    // Kart değişikliklerini dinle
    cardElement.on('change', function(event) {
        if (event.error) {
            showError(event.error.message);
        } else {
            hideError();
        }
    });

    // Form submit
    form.addEventListener('submit', async function(event) {
        event.preventDefault();
        
        // Buton durumunu güncelle
        setLoading(true);

        try {
            // Payment Intent oluştur
            const response = await fetch('<?php echo e(route("stripe.create.payment.intent")); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    amount: <?php echo e($toplam * 100); ?>, // Kuruş cinsinden
                    customer_data: <?php echo json_encode($addressData, 15, 512) ?>
                })
            });

            const paymentData = await response.json();
            
            if (!response.ok) {
                throw new Error(paymentData.message || 'Ödeme hazırlanırken hata oluştu');
            }

            // Ödeme confirm et
            const {error, paymentIntent} = await stripe.confirmCardPayment(
                paymentData.client_secret,
                {
                    payment_method: {
                        card: cardElement,
                        billing_details: {
                            name: '<?php echo e($addressData["ad_soyad"]); ?>',
                            email: '<?php echo e($addressData["email"]); ?>',
                            phone: '<?php echo e($addressData["telefon"]); ?>',
                            address: {
                                line1: '<?php echo e($addressData["adres"]); ?>',
                                city: '<?php echo e($addressData["il"]); ?>',
                                state: '<?php echo e($addressData["ilce"]); ?>',
                                postal_code: '<?php echo e($addressData["posta_kodu"] ?? ""); ?>',
                                country: 'TR'
                            }
                        }
                    }
                }
            );

            if (error) {
                showError(error.message);
                setLoading(false);
            } else {
                // Ödeme başarılı
                showSuccess();
                
                // Sipariş oluştur
                const orderResponse = await fetch('<?php echo e(route("stripe.create.order")); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        payment_intent_id: paymentIntent.id,
                        customer_data: <?php echo json_encode($addressData, 15, 512) ?>
                    })
                });

                const orderData = await orderResponse.json();
                
                if (orderResponse.ok) {
                    // Başarı sayfasına yönlendir
                    setTimeout(() => {
                        window.location.href = '<?php echo e(route("stripe.success")); ?>?order_id=' + orderData.order_id;
                    }, 2000);
                } else {
                    showError('Sipariş oluşturulurken hata oluştu: ' + orderData.message);
                    setLoading(false);
                }
            }

        } catch (error) {
            showError('Ödeme sırasında hata oluştu: ' + error.message);
            setLoading(false);
        }
    });

    // Yardımcı fonksiyonlar
    function setLoading(loading) {
        submitButton.disabled = loading;
        if (loading) {
            buttonText.textContent = 'Ödeme İşleniyor...';
            spinner.classList.remove('d-none');
        } else {
            buttonText.textContent = '<?php echo e(number_format($toplam, 2)); ?> ₺ Öde';
            spinner.classList.add('d-none');
        }
    }

    function showError(message) {
        errorElement.querySelector('#error-message').textContent = message;
        errorElement.classList.remove('d-none');
        successElement.classList.add('d-none');
    }

    function hideError() {
        errorElement.classList.add('d-none');
    }

    function showSuccess() {
        successElement.classList.remove('d-none');
        errorElement.classList.add('d-none');
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Enes\Desktop\kafama göre projeler\laravel-php-pazaryeri\resources\views/stripe/payment.blade.php ENDPATH**/ ?>
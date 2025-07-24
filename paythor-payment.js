/**
 * PayThor Payment Integration JavaScript
 * Bu dosya ödeme formlarınızda kullanabileceğiniz JavaScript kodlarını içerir
 */

class PayThorPayment {
    constructor(options = {}) {
        this.apiUrl = options.apiUrl || '/payment/process';
        this.loadingText = options.loadingText || 'İşleminiz gerçekleştiriliyor...';
        this.init();
    }

    init() {
        this.bindEvents();
        this.validateForm();
    }

    bindEvents() {
        // Ödeme formları için event listener
        document.querySelectorAll('.payment-form').forEach(form => {
            form.addEventListener('submit', (e) => {
                this.handlePaymentSubmit(e);
            });
        });

        // Telefon input formatı
        document.querySelectorAll('input[name="phone"]').forEach(input => {
            input.addEventListener('input', this.formatPhone);
        });

        // TC Kimlik No formatı
        document.querySelectorAll('input[name="tc_no"]').forEach(input => {
            input.addEventListener('input', this.formatTCNo);
        });
    }

    handlePaymentSubmit(event) {
        event.preventDefault();
        
        const form = event.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;

        // Form validasyonu
        if (!this.validatePaymentForm(form)) {
            return false;
        }

        // Loading state
        this.setLoadingState(submitBtn, true);

        // Form verilerini topla
        const formData = new FormData(form);
        
        // AJAX ile ödeme işlemini başlat
        fetch(this.apiUrl, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (response.redirected) {
                // PayThor'a yönlendir
                window.location.href = response.url;
            } else {
                return response.json();
            }
        })
        .then(data => {
            if (data && data.error) {
                this.showError(data.error);
                this.setLoadingState(submitBtn, false, originalText);
            } else if (data && data.payment_url) {
                window.location.href = data.payment_url;
            }
        })
        .catch(error => {
            console.error('Payment Error:', error);
            this.showError('Ödeme işlemi sırasında bir hata oluştu.');
            this.setLoadingState(submitBtn, false, originalText);
        });
    }

    validatePaymentForm(form) {
        let isValid = true;
        const errors = [];

        // Required field kontrolü
        form.querySelectorAll('[required]').forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                this.showFieldError(field, 'Bu alan zorunludur');
                errors.push(`${field.name} alanı zorunludur`);
            } else {
                this.clearFieldError(field);
            }
        });

        // Email kontrolü
        const emailField = form.querySelector('input[name="email"]');
        if (emailField && emailField.value) {
            if (!this.isValidEmail(emailField.value)) {
                isValid = false;
                this.showFieldError(emailField, 'Geçerli bir e-posta adresi giriniz');
                errors.push('Geçersiz e-posta adresi');
            }
        }

        // Telefon kontrolü
        const phoneField = form.querySelector('input[name="phone"]');
        if (phoneField && phoneField.value) {
            if (!this.isValidPhone(phoneField.value)) {
                isValid = false;
                this.showFieldError(phoneField, 'Geçerli bir telefon numarası giriniz');
                errors.push('Geçersiz telefon numarası');
            }
        }

        // TC Kimlik No kontrolü
        const tcField = form.querySelector('input[name="tc_no"]');
        if (tcField && tcField.value) {
            if (!this.isValidTCNo(tcField.value)) {
                isValid = false;
                this.showFieldError(tcField, 'Geçerli bir TC Kimlik No giriniz');
                errors.push('Geçersiz TC Kimlik No');
            }
        }

        if (!isValid) {
            console.warn('Form Validation Errors:', errors);
        }

        return isValid;
    }

    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    isValidPhone(phone) {
        // Türkiye telefon numarası formatı
        const phoneRegex = /^(\+90|0)?[5][0-9]{9}$/;
        const cleanPhone = phone.replace(/\s/g, '');
        return phoneRegex.test(cleanPhone);
    }

    isValidTCNo(tcNo) {
        if (tcNo.length !== 11) return false;
        
        const digits = tcNo.split('').map(Number);
        
        // İlk rakam 0 olamaz
        if (digits[0] === 0) return false;
        
        // TC kimlik no algoritması
        const oddSum = digits[0] + digits[2] + digits[4] + digits[6] + digits[8];
        const evenSum = digits[1] + digits[3] + digits[5] + digits[7];
        
        const checkDigit1 = ((oddSum * 7) - evenSum) % 10;
        const checkDigit2 = (oddSum + evenSum + digits[9]) % 10;
        
        return checkDigit1 === digits[9] && checkDigit2 === digits[10];
    }

    formatPhone(event) {
        let value = event.target.value.replace(/\D/g, '');
        
        if (value.startsWith('90')) {
            value = value.substring(2);
        }
        if (value.startsWith('0')) {
            value = value.substring(1);
        }
        
        // Format: 5XX XXX XX XX
        if (value.length >= 10) {
            value = value.substring(0, 10);
            value = value.replace(/(\d{3})(\d{3})(\d{2})(\d{2})/, '$1 $2 $3 $4');
        } else if (value.length >= 6) {
            value = value.replace(/(\d{3})(\d{3})(\d+)/, '$1 $2 $3');
        } else if (value.length >= 3) {
            value = value.replace(/(\d{3})(\d+)/, '$1 $2');
        }
        
        event.target.value = value;
    }

    formatTCNo(event) {
        let value = event.target.value.replace(/\D/g, '');
        if (value.length > 11) {
            value = value.substring(0, 11);
        }
        event.target.value = value;
    }

    showFieldError(field, message) {
        this.clearFieldError(field);
        
        field.classList.add('is-invalid');
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = message;
        
        field.parentNode.appendChild(errorDiv);
    }

    clearFieldError(field) {
        field.classList.remove('is-invalid');
        
        const existingError = field.parentNode.querySelector('.invalid-feedback');
        if (existingError) {
            existingError.remove();
        }
    }

    showError(message) {
        // Mevcut hata mesajlarını temizle
        document.querySelectorAll('.payment-error').forEach(el => el.remove());
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-danger payment-error';
        errorDiv.innerHTML = `
            <i class="fas fa-exclamation-circle"></i>
            <strong>Hata:</strong> ${message}
        `;
        
        // Formu bul ve en üstüne hata mesajını ekle
        const form = document.querySelector('.payment-form');
        if (form) {
            form.insertBefore(errorDiv, form.firstChild);
            
            // Hata mesajına scroll
            errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    setLoadingState(button, isLoading, originalText = null) {
        if (isLoading) {
            button.disabled = true;
            button.innerHTML = `
                <i class="fas fa-spinner fa-spin"></i>
                ${this.loadingText}
            `;
        } else {
            button.disabled = false;
            button.textContent = originalText || 'Ödeme Yap';
        }
    }

    // PayThor callback sonuçlarını kontrol et
    static checkPaymentResult() {
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');
        const merchantReference = urlParams.get('merchant_reference');
        
        if (status === 'success') {
            PayThorPayment.showSuccessMessage('Ödemeniz başarıyla gerçekleştirildi!');
        } else if (status === 'failed' || status === 'cancelled') {
            PayThorPayment.showErrorMessage('Ödeme işlemi başarısız oldu.');
        }
    }

    static showSuccessMessage(message) {
        const successDiv = document.createElement('div');
        successDiv.className = 'alert alert-success alert-dismissible fade show';
        successDiv.innerHTML = `
            <i class="fas fa-check-circle"></i>
            <strong>Başarılı:</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.insertBefore(successDiv, document.body.firstChild);
        
        // 5 saniye sonra otomatik kapat
        setTimeout(() => {
            successDiv.remove();
        }, 5000);
    }

    static showErrorMessage(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-danger alert-dismissible fade show';
        errorDiv.innerHTML = `
            <i class="fas fa-exclamation-circle"></i>
            <strong>Hata:</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.insertBefore(errorDiv, document.body.firstChild);
    }
}

// CSS Stilleri (opsiyonel)
const paymentStyles = `
<style>
.payment-form .form-control.is-invalid {
    border-color: #dc3545;
}

.payment-form .invalid-feedback {
    display: block;
    color: #dc3545;
    font-size: 0.875em;
    margin-top: 0.25rem;
}

.payment-loading {
    pointer-events: none;
    opacity: 0.6;
}

.payment-error {
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.payment-summary {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 1rem;
}

.payment-methods i {
    margin-right: 0.5rem;
    opacity: 0.8;
    transition: opacity 0.3s;
}

.payment-methods i:hover {
    opacity: 1;
}
</style>
`;

// DOM yüklendiğinde çalıştır
document.addEventListener('DOMContentLoaded', function() {
    // CSS stillerini ekle
    document.head.insertAdjacentHTML('beforeend', paymentStyles);
    
    // PayThor Payment sistemini başlat
    new PayThorPayment({
        apiUrl: '/payment/process',
        loadingText: 'Ödeme sayfasına yönlendiriliyor...'
    });
    
    // Sayfa yüklendiğinde ödeme sonucunu kontrol et
    PayThorPayment.checkPaymentResult();
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PayThorPayment;
}

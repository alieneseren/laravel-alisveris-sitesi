

<?php $__env->startSection('title', 'Yeni Ürün Ekle'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-plus"></i> Yeni Ürün Ekle</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?php echo e(route('satici.urun.ekle.post')); ?>" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        
                        <div class="mb-3">
                            <label for="urun_adi" class="form-label">Ürün Adı <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?php $__errorArgs = ['urun_adi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="urun_adi" name="urun_adi" value="<?php echo e(old('urun_adi')); ?>" required>
                            <?php $__errorArgs = ['urun_adi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="mb-3">
                            <label for="urun_aciklamasi" class="form-label">Ürün Açıklaması <span class="text-danger">*</span></label>
                            <textarea class="form-control <?php $__errorArgs = ['urun_aciklamasi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                      id="urun_aciklamasi" name="urun_aciklamasi" rows="4" required><?php echo e(old('urun_aciklamasi')); ?></textarea>
                            <?php $__errorArgs = ['urun_aciklamasi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fiyat" class="form-label">Fiyat (₺) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0" class="form-control <?php $__errorArgs = ['fiyat'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="fiyat" name="fiyat" value="<?php echo e(old('fiyat')); ?>" required>
                                    <?php $__errorArgs = ['fiyat'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="stok" class="form-label">Stok Adedi <span class="text-danger">*</span></label>
                                    <input type="number" min="0" class="form-control <?php $__errorArgs = ['stok'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="stok" name="stok" value="<?php echo e(old('stok')); ?>" required>
                                    <?php $__errorArgs = ['stok'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kategoriler <span class="text-danger">*</span></label>
                            <?php $__errorArgs = ['kategoriler'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger small"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <div class="row">
                                <?php $__currentLoopData = $kategoriler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-md-6 mb-2">
                                        <div class="card">
                                            <div class="card-header py-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" 
                                                           name="kategoriler[]" value="<?php echo e($kategori->id); ?>" 
                                                           id="kat_<?php echo e($kategori->id); ?>"
                                                           <?php echo e(in_array($kategori->id, old('kategoriler', [])) ? 'checked' : ''); ?>>
                                                    <label class="form-check-label fw-bold" for="kat_<?php echo e($kategori->id); ?>">
                                                        <?php echo e($kategori->kategori_adi); ?>

                                                    </label>
                                                </div>
                                            </div>
                                            <?php if($kategori->altKategoriler->isNotEmpty()): ?>
                                                <div class="card-body py-2">
                                                    <?php $__currentLoopData = $kategori->altKategoriler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $altKategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <div class="form-check ms-3">
                                                            <input class="form-check-input" type="checkbox" 
                                                                   name="kategoriler[]" value="<?php echo e($altKategori->id); ?>" 
                                                                   id="kat_<?php echo e($altKategori->id); ?>"
                                                                   <?php echo e(in_array($altKategori->id, old('kategoriler', [])) ? 'checked' : ''); ?>>
                                                            <label class="form-check-label" for="kat_<?php echo e($altKategori->id); ?>">
                                                                <?php echo e($altKategori->kategori_adi); ?>

                                                            </label>
                                                        </div>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>

                        <!-- Yeni Kategori Ekleme -->
                        <div class="mb-3">
                            <div class="card border-success">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-plus-circle text-success"></i> Yeni Alt Kategori Ekle</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="ust_kategori_id" class="form-label">Ana Kategori Seçin</label>
                                            <select class="form-select <?php $__errorArgs = ['ust_kategori_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                    id="ust_kategori_id" name="ust_kategori_id">
                                                <option value="">Ana kategori seçin...</option>
                                                <?php $__currentLoopData = $kategoriler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($kategori->id); ?>" 
                                                            <?php echo e(old('ust_kategori_id') == $kategori->id ? 'selected' : ''); ?>>
                                                        <?php echo e($kategori->kategori_adi); ?>

                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                            <?php $__errorArgs = ['ust_kategori_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="yeni_kategori" class="form-label">Yeni Alt Kategori Adı</label>
                                            <input type="text" class="form-control <?php $__errorArgs = ['yeni_kategori'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                   id="yeni_kategori" name="yeni_kategori" value="<?php echo e(old('yeni_kategori')); ?>"
                                                   placeholder="Örn: Gaming Monitör">
                                            <?php $__errorArgs = ['yeni_kategori'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                    <div class="form-text mt-2">
                                        <i class="fas fa-info-circle"></i> Mevcut kategorilerde ürününüze uygun kategori yoksa yeni alt kategori oluşturabilirsiniz.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Görsel Yükleme Seçenekleri -->
                        <div class="mb-3">
                            <label class="form-label">Ürün Görselleri</label>
                            
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs" id="gorselTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="dosya-tab" data-bs-toggle="tab" 
                                            data-bs-target="#dosya" type="button" role="tab">
                                        <i class="fas fa-upload"></i> Dosya Yükle
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="url-tab" data-bs-toggle="tab" 
                                            data-bs-target="#url" type="button" role="tab">
                                        <i class="fas fa-link"></i> URL ile Ekle
                                    </button>
                                </li>
                            </ul>
                            
                            <!-- Tab content -->
                            <div class="tab-content border border-top-0 p-3" id="gorselTabContent">
                                <div class="tab-pane fade show active" id="dosya" role="tabpanel">
                                    <input type="file" class="form-control <?php $__errorArgs = ['gorsel_dosyalari.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           name="gorsel_dosyalari[]" multiple accept="image/*" id="gorsel_dosyalari">
                                    <?php $__errorArgs = ['gorsel_dosyalari.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <div class="form-text">
                                        Birden fazla görsel seçebilirsiniz. Desteklenen formatlar: JPG, PNG, GIF (Max: 2MB)
                                    </div>
                                    <div id="dosya_preview" class="mt-3 row"></div>
                                </div>
                                <div class="tab-pane fade" id="url" role="tabpanel">
                                    <textarea class="form-control <?php $__errorArgs = ['gorsel_urls'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                              id="gorsel_urls" name="gorsel_urls" rows="4" 
                                              placeholder="Her satıra bir görsel URL'si yazın..."><?php echo e(old('gorsel_urls')); ?></textarea>
                                    <?php $__errorArgs = ['gorsel_urls'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <div class="form-text">
                                        Örnek: https://via.placeholder.com/600x400/007bff/ffffff?text=Ürün+Görseli
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-info mt-2">
                                <i class="fas fa-info-circle"></i>
                                <strong>Not:</strong> Hiç görsel eklemezseniz otomatik placeholder görsel eklenecektir.
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Bilgi:</strong> Ürününüz eklendikten sonra hemen satışa sunulacaktır.
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?php echo e(route('satici.urunler')); ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Geri Dön
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Ürünü Ekle
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Dosya önizleme
document.getElementById('gorsel_dosyalari').addEventListener('change', function(e) {
    const preview = document.getElementById('dosya_preview');
    preview.innerHTML = '';
    
    Array.from(e.target.files).forEach((file, index) => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-md-3 mb-2';
                col.innerHTML = `
                    <div class="card">
                        <img src="${e.target.result}" class="card-img-top" style="height: 150px; object-fit: cover;">
                        <div class="card-body p-2">
                            <small class="text-muted">${file.name}</small>
                            ${index === 0 ? '<br><span class="badge bg-primary">Ana Görsel</span>' : ''}
                        </div>
                    </div>
                `;
                preview.appendChild(col);
            };
            reader.readAsDataURL(file);
        }
    });
});

// Kategori seçim yardımcısı
document.addEventListener('DOMContentLoaded', function() {
    const ustKategoriSelect = document.getElementById('ust_kategori_id');
    const yeniKategoriInput = document.getElementById('yeni_kategori');
    
    function toggleYeniKategori() {
        if (ustKategoriSelect.value) {
            yeniKategoriInput.removeAttribute('disabled');
            yeniKategoriInput.parentElement.parentElement.style.opacity = '1';
        } else {
            yeniKategoriInput.setAttribute('disabled', 'disabled');
            yeniKategoriInput.parentElement.parentElement.style.opacity = '0.6';
        }
    }
    
    ustKategoriSelect.addEventListener('change', toggleYeniKategori);
    toggleYeniKategori(); // İlk yükleme
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Enes\Desktop\kafama göre projeler\laravel-php-pazaryeri\resources\views/satici/urun-ekle.blade.php ENDPATH**/ ?>
<div class="d-flex align-items-center gap-3 mb-4">
  <a href="<?= APP_URL ?>/admin/products" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
  <h5 class="fw-800 mb-0"><?= e($title) ?></h5>
</div>

<div class="row g-4">
  <div class="col-lg-8">
    <form action="<?= e($formAction) ?>" method="POST" enctype="multipart/form-data" novalidate>
      <?= csrfField() ?>

      <div class="form-section">
        <div class="form-section-title">Informasi Produk</div>
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label fw-600 small">Nama Produk *</label>
            <input type="text" name="name" class="form-control" value="<?= e($product['name']??'') ?>" placeholder="Nama lengkap produk" required>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-600 small">Kategori</label>
            <select name="category_id" class="form-select">
              <option value="">— Tanpa Kategori —</option>
              <?php foreach($categories as $c): ?>
                <option value="<?= $c['id'] ?>" <?= (isset($product['category_id']) && $product['category_id']==$c['id'])?'selected':'' ?>>
                  <?= e($c['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label fw-600 small">Satuan</label>
            <select name="unit" class="form-select">
              <?php foreach(['pcs','kg','gram','liter','ml','botol','pack','ikat','karung','cup','300g','500g'] as $u): ?>
                <option value="<?= $u ?>" <?= ($product['unit']??'pcs')===$u?'selected':'' ?>><?= $u ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label fw-600 small">Berat (gram)</label>
            <input type="number" name="weight" class="form-control" value="<?= $product['weight']??0 ?>" min="0">
          </div>
          <div class="col-12">
            <label class="form-label fw-600 small">Deskripsi</label>
            <textarea name="description" class="form-control" rows="4" placeholder="Deskripsi lengkap produk…"><?= e($product['description']??'') ?></textarea>
          </div>
        </div>
      </div>

      <div class="form-section">
        <div class="form-section-title">Harga & Stok</div>
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label fw-600 small">Harga Jual (Rp) *</label>
            <input type="number" name="price" class="form-control" value="<?= $product['price']??'' ?>" min="0" step="500" required>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-600 small">Harga Asli (Rp) <small class="text-muted">untuk coret</small></label>
            <input type="number" name="original_price" class="form-control" value="<?= $product['original_price']??'' ?>" min="0" step="500" placeholder="Kosongkan jika tidak ada">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-600 small">Stok *</label>
            <input type="number" name="stock" class="form-control" value="<?= $product['stock']??0 ?>" min="0" required>
          </div>
        </div>
      </div>

      <div class="form-section">
        <div class="form-section-title">Pengaturan & Gambar</div>
        <div class="row g-3 align-items-start">
          <div class="col-md-6">
            <label class="form-label fw-600 small">Gambar Produk</label>
            <?php if(!empty($product['image'])): ?>
              <div class="mb-2">
                <img src="<?= productImage($product['image'],$product['id']??0) ?>" style="width:80px;height:80px;object-fit:cover;border-radius:10px;border:1px solid #e2e8f0">
                <div class="tiny text-muted mt-1">Gambar saat ini</div>
              </div>
            <?php endif; ?>
            <input type="file" name="image" class="form-control form-control-sm" accept="image/jpeg,image/png,image/webp">
            <div class="form-text">JPG/PNG/WEBP, maks. 2MB</div>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-600 small">Opsi</label>
            <div class="d-flex flex-column gap-2 mt-1">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_featured" value="1"
                       id="isFeatured" <?= !empty($product['is_featured'])?'checked':'' ?>>
                <label class="form-check-label small" for="isFeatured">Produk Unggulan (tampil di home)</label>
              </div>
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_active" value="1"
                       id="isActive" <?= ($product['is_active']??1)?'checked':'' ?>>
                <label class="form-check-label small" for="isActive">Produk Aktif (tampil di katalog)</label>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary px-4 fw-700">
          <i class="bi bi-check-lg me-2"></i><?= $product ? 'Simpan Perubahan' : 'Tambah Produk' ?>
        </button>
        <a href="<?= APP_URL ?>/admin/products" class="btn btn-outline-secondary px-4">Batal</a>
        <?php if($product): ?>
          <a href="<?= APP_URL ?>/product/<?= $product['id'] ?>" target="_blank" class="btn btn-outline-success ms-auto">
            <i class="bi bi-eye me-1"></i>Preview
          </a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <div class="col-lg-4">
    <div class="a-card p-4" style="background:#fffbeb;border-color:#fcd34d">
      <h6 class="fw-700 mb-3"><i class="bi bi-lightbulb-fill text-warning me-2"></i>Tips QA Edge Cases</h6>
      <ul class="small text-muted ps-3 mb-0">
        <li class="mb-2">Coba harga negatif atau nol</li>
        <li class="mb-2">Coba stok = 0 lalu tambah ke keranjang</li>
        <li class="mb-2">Upload file PHP/non-gambar</li>
        <li class="mb-2">Nama produk sangat panjang (>200 kar)</li>
        <li class="mb-2">Harga asli lebih kecil dari harga jual</li>
        <li>Hapus produk yang ada di keranjang user</li>
      </ul>
    </div>
  </div>
</div>

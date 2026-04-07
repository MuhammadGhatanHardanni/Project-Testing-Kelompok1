<div class="container py-4">
  <h3 class="fw-800 mb-4"><i class="bi bi-cart3 me-2 text-success"></i>Keranjang Belanja</h3>

  <?php if(empty($items)): ?>
    <div class="empty-state">
      <i class="bi bi-cart-x empty-icon"></i>
      <h5 class="fw-700">Keranjang masih kosong</h5>
      <p class="text-muted">Temukan produk segar dan tambahkan ke keranjang.</p>
      <a href="<?= APP_URL ?>/" class="btn btn-primary mt-2"><i class="bi bi-shop me-2"></i>Mulai Belanja</a>
    </div>
  <?php else: ?>
    <div class="row g-4">
      <div class="col-lg-8">
        <div class="cart-box">
          <div class="cart-header">
            <span><?= count($items) ?> produk</span>
            <a href="<?= APP_URL ?>/" class="btn btn-sm btn-outline-primary"><i class="bi bi-plus me-1"></i>Tambah</a>
          </div>
          <?php foreach($items as $item): ?>
          <div class="cart-item">
            <div class="cart-img">
              <img src="<?= productImage($item['image'],$item['product_id']) ?>"
                   alt="<?= e($item['name']) ?>"
                   onerror="this.src='https://picsum.photos/seed/ct<?= $item['product_id'] ?>/120/120'">
            </div>
            <div class="flex-grow-1">
              <div class="fw-600"><?= e($item['name']) ?></div>
              <div class="text-muted small"><?= formatRupiah($item['price']) ?> / <?= e($item['unit']??'pcs') ?></div>
              <?php if($item['stock'] < $item['quantity']): ?>
                <div class="text-danger tiny mt-1"><i class="bi bi-exclamation-triangle me-1"></i>Stok tersisa <?= $item['stock'] ?></div>
              <?php endif; ?>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end">
              <form action="<?= APP_URL ?>/cart/update" method="POST">
                <?= csrfField() ?><input type="hidden" name="cart_id" value="<?= $item['id'] ?>">
                <div class="qty-wrap qty-sm">
                  <button type="button" class="qty-btn" data-delta="-1">−</button>
                  <input type="number" name="quantity" class="qty-in" value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stock'] ?>" onchange="this.form.submit()">
                  <button type="button" class="qty-btn" data-delta="1">+</button>
                </div>
              </form>
              <span class="cart-sub"><?= formatRupiah($item['subtotal']) ?></span>
              <form action="<?= APP_URL ?>/cart/remove" method="POST" onsubmit="return confirm('Hapus produk ini?')">
                <?= csrfField() ?><input type="hidden" name="cart_id" value="<?= $item['id'] ?>">
                <button type="submit" class="btn-remove"><i class="bi bi-trash3"></i></button>
              </form>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="cart-summary sticky-summary">
          <h6 class="fw-700 mb-3">Ringkasan</h6>
          <div class="sum-row"><span class="text-muted">Subtotal</span><span><?= formatRupiah($total) ?></span></div>
          <div class="sum-row"><span class="text-muted">Ongkir</span><span class="text-success fw-600">Gratis</span></div>
          <div class="sum-total">
            <span class="fw-700">Total</span>
            <span class="sum-total-price"><?= formatRupiah($total) ?></span>
          </div>
          <a href="<?= APP_URL ?>/checkout" class="btn btn-primary btn-lg w-100 mt-3 fw-700">
            <i class="bi bi-credit-card me-2"></i>Checkout
          </a>
          <a href="<?= APP_URL ?>/" class="btn btn-outline-secondary w-100 mt-2">
            <i class="bi bi-arrow-left me-1"></i>Lanjut Belanja
          </a>
        </div>
      </div>
    </div>
  <?php endif; ?>
</div>

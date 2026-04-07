<div class="container py-5">
  <div class="success-wrap">
    <div class="success-icon"><i class="bi bi-check-lg"></i></div>
    <h2 class="fw-900 mt-4 mb-1" style="font-family:'Outfit',sans-serif">Pesanan Berhasil! 🎉</h2>
    <p class="text-muted">Terima kasih telah berbelanja di <strong><?= APP_NAME ?></strong>.</p>

    <?php if($order): ?>
      <p class="text-muted">Nomor Pesanan: <strong class="text-success" style="font-family:'Outfit',sans-serif"><?= generateOrderNumber($order['id']) ?></strong></p>
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);padding:1.75rem;text-align:left">
        <div class="row g-3 mb-3">
          <div class="col-sm-6">
            <div class="tiny text-muted fw-700 mb-1 text-uppercase">Penerima</div>
            <div class="fw-700"><?= e($order['recipient_name']) ?></div>
          </div>
          <div class="col-sm-6">
            <div class="tiny text-muted fw-700 mb-1 text-uppercase">Telepon</div>
            <div class="fw-700"><?= e($order['phone']) ?></div>
          </div>
          <div class="col-12">
            <div class="tiny text-muted fw-700 mb-1 text-uppercase">Alamat Pengiriman</div>
            <div><?= e($order['address']) ?><?= $order['city'] ? ', '.e($order['city']) : '' ?></div>
          </div>
        </div>
        <div class="border-top pt-3">
          <?php foreach($order['items'] as $item): ?>
          <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
            <div>
              <div class="fw-600 small"><?= e($item['product_name']) ?></div>
              <div class="tiny text-muted"><?= formatRupiah($item['price']) ?> × <?= $item['quantity'] ?></div>
            </div>
            <div class="fw-700 text-success"><?= formatRupiah($item['subtotal']) ?></div>
          </div>
          <?php endforeach; ?>
          <?php if($order['discount_amount'] > 0): ?>
            <div class="d-flex justify-content-between py-2 text-success small"><span><i class="bi bi-tag me-1"></i>Diskon Voucher</span><span>-<?= formatRupiah($order['discount_amount']) ?></span></div>
          <?php endif; ?>
          <div class="d-flex justify-content-between fw-900 fs-5 pt-3" style="font-family:'Outfit',sans-serif">
            <span>Total Pembayaran</span>
            <span class="text-success"><?= formatRupiah($order['total_price']) ?></span>
          </div>
        </div>
        <div class="text-center mt-4">
          <span class="status-pill processing"><i class="bi bi-hourglass-split me-1"></i>Sedang Diproses</span>
        </div>
      </div>
    <?php endif; ?>

    <div class="d-flex gap-3 justify-content-center mt-4 flex-wrap">
      <a href="<?= APP_URL ?>/orders" class="btn btn-primary px-5 fw-700">
        <i class="bi bi-bag-check me-2"></i>Lihat Pesanan
      </a>
      <a href="<?= APP_URL ?>/" class="btn btn-outline-secondary px-5">
        <i class="bi bi-shop me-2"></i>Lanjut Belanja
      </a>
    </div>
  </div>
</div>

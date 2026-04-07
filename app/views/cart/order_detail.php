<div class="container py-4">
  <div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= APP_URL ?>/orders" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h4 class="fw-800 mb-0">Detail Pesanan</h4>
    <span class="status-badge <?= $order['status'] ?>"><?= ucfirst($order['status']) ?></span>
  </div>

  <div class="row g-4">
    <div class="col-md-7">
      <div class="profile-card mb-3">
        <div class="profile-card-header"><strong class="small">Info Pengiriman</strong></div>
        <div class="profile-card-body">
          <div class="row g-2">
            <div class="col-sm-6"><div class="tiny text-muted fw-600">No. Pesanan</div><div class="fw-700"><?= generateOrderNumber($order['id']) ?></div></div>
            <div class="col-sm-6"><div class="tiny text-muted fw-600">Tanggal</div><div><?= date('d M Y, H:i', strtotime($order['created_at'])) ?></div></div>
            <div class="col-sm-6"><div class="tiny text-muted fw-600">Penerima</div><div class="fw-600"><?= e($order['recipient_name']) ?></div></div>
            <div class="col-sm-6"><div class="tiny text-muted fw-600">Telepon</div><div><?= e($order['phone']) ?></div></div>
            <div class="col-12"><div class="tiny text-muted fw-600">Alamat</div><div><?= e($order['address']) ?><?= $order['city'] ? ', '.e($order['city']) : '' ?></div></div>
            <?php if($order['notes']): ?><div class="col-12"><div class="tiny text-muted fw-600">Catatan</div><div class="fst-italic small"><?= e($order['notes']) ?></div></div><?php endif; ?>
          </div>
        </div>
      </div>

      <div class="profile-card">
        <div class="profile-card-header"><strong class="small">Item Pesanan</strong></div>
        <div class="profile-card-body p-0">
          <?php foreach($order['items'] as $item): ?>
          <div class="d-flex align-items-center gap-3 p-3 border-bottom">
            <img src="https://picsum.photos/seed/oi<?= $item['product_id'] ?>/60/60" style="width:52px;height:52px;border-radius:8px;object-fit:cover">
            <div class="flex-grow-1">
              <div class="fw-600 small"><?= e($item['product_name']) ?></div>
              <div class="tiny text-muted"><?= formatRupiah($item['price']) ?> × <?= $item['quantity'] ?></div>
            </div>
            <div class="fw-700 small"><?= formatRupiah($item['subtotal']) ?></div>
          </div>
          <?php endforeach; ?>
          <div class="p-3">
            <?php if($order['discount_amount'] > 0): ?>
              <div class="d-flex justify-content-between small text-success mb-1"><span>Diskon (<?= $order['voucher_code'] ?>)</span><span>-<?= formatRupiah($order['discount_amount']) ?></span></div>
            <?php endif; ?>
            <div class="d-flex justify-content-between fw-800 fs-5 pt-2 border-top"><span>Total</span><span class="text-success"><?= formatRupiah($order['total_price']) ?></span></div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-5">
      <?php if($order['status'] === 'completed'): ?>
        <div class="profile-card">
          <div class="profile-card-header"><strong class="small"><i class="bi bi-star me-1 text-warning"></i>Beri Ulasan</strong></div>
          <div class="profile-card-body">
            <p class="small text-muted">Bantu pembeli lain dengan berbagi pengalamanmu.</p>
            <?php foreach($order['items'] as $item): if(!$item['is_reviewed'] && $item['product_id']): ?>
              <div class="mb-3">
                <div class="fw-600 small mb-2"><?= e(truncate($item['product_name'],40)) ?></div>
                <a href="<?= APP_URL ?>/product/<?= $item['product_id'] ?>#reviews" class="btn btn-sm btn-outline-warning">
                  <i class="bi bi-star me-1"></i>Beri Ulasan
                </a>
              </div>
            <?php endif; endforeach; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php
$errors = $_SESSION['checkout_errors'] ?? [];
unset($_SESSION['checkout_errors']);
?>
<div class="container py-4">
  <h3 class="fw-800 mb-4"><i class="bi bi-bag-check me-2 text-success"></i>Checkout</h3>

  <?php if(!empty($errors)): ?>
    <div class="alert alert-danger">
      <strong><i class="bi bi-exclamation-triangle me-1"></i>Perbaiki:</strong>
      <ul class="mb-0 mt-1 ps-3"><?php foreach($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?></ul>
    </div>
  <?php endif; ?>

  <div class="row g-4">
    <div class="col-lg-7">
      <form action="<?= APP_URL ?>/checkout/process" method="POST" id="checkoutForm" novalidate>
        <?= csrfField() ?>
        <input type="hidden" name="applied_voucher" id="appliedVoucherCode" value="">

        <!-- Saved addresses -->
        <?php if(!empty($addresses)): ?>
        <div class="checkout-card mb-3">
          <h6 class="fw-700 mb-3"><i class="bi bi-geo-alt me-2 text-success"></i>Pilih Alamat</h6>
          <div class="d-flex flex-column gap-2 mb-3">
            <?php foreach($addresses as $a): ?>
            <label class="addr-radio-card"
              data-addr='<?= json_encode(['recipient'=>$a['recipient'],'phone'=>$a['phone'],'full_address'=>$a['address'].', '.$a['city'].', '.$a['province'].' '.$a['postal_code'],'city'=>$a['city']]) ?>'>
              <div class="d-flex align-items-center gap-2">
                <input type="radio" name="_addr_sel" <?= $a['is_primary']?'checked':'' ?> class="form-check-input mt-0">
                <div class="flex-grow-1">
                  <span class="badge bg-success me-1"><?= e($a['label']) ?></span>
                  <?php if($a['is_primary']): ?><span class="badge bg-warning text-dark me-1">Utama</span><?php endif; ?>
                  <strong class="small"><?= e($a['recipient']) ?></strong>
                  <div class="text-muted tiny"><?= e($a['address'].', '.$a['city']) ?></div>
                </div>
              </div>
            </label>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <!-- Shipping form -->
        <div class="checkout-card mb-3">
          <h6 class="fw-700 mb-3"><i class="bi bi-truck me-2 text-primary"></i>Informasi Pengiriman</h6>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-600">Nama Penerima *</label>
              <input type="text" name="recipient_name" id="recipientName" class="form-control" value="<?= e($_SESSION['user_name']??'') ?>" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Nomor Telepon *</label>
              <input type="text" name="phone" id="phoneField" class="form-control" placeholder="08xxxxxxxxxx" required>
            </div>
            <div class="col-12">
              <label class="form-label fw-600">Alamat Lengkap *</label>
              <textarea name="address" id="addressField" class="form-control" rows="2" placeholder="Jalan, No. Rumah, RT/RW, Kelurahan, Kecamatan" required></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Kota/Kabupaten</label>
              <input type="text" name="city" id="cityField" class="form-control" placeholder="Yogyakarta">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Catatan Kurir</label>
              <input type="text" name="notes" class="form-control" placeholder="Opsional…">
            </div>
          </div>
        </div>

        <!-- Voucher -->
        <div class="checkout-card mb-3">
          <h6 class="fw-700 mb-3"><i class="bi bi-ticket-perforated me-2 text-warning"></i>Voucher Diskon</h6>
          <div class="voucher-box">
            <form id="voucherForm" class="d-flex gap-2">
              <input type="text" id="voucherCode" class="form-control" placeholder="Masukkan kode voucher…" style="text-transform:uppercase">
              <input type="hidden" id="cartSubtotal" value="<?= $subtotal ?>">
              <button type="submit" class="btn btn-warning btn-sm fw-600 flex-shrink-0">Pakai</button>
            </form>
            <div id="voucherMsg" class="mt-2 small"></div>
            <button id="removeVoucherBtn" type="button" class="btn btn-sm btn-outline-danger mt-2 d-none">
              <i class="bi bi-x me-1"></i>Hapus Voucher
            </button>
          </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-100 fw-700" id="placeOrderBtn">
          <i class="bi bi-check-circle me-2"></i>Konfirmasi Pesanan
        </button>
      </form>
    </div>

    <!-- Order summary -->
    <div class="col-lg-5">
      <div class="checkout-card sticky-summary">
        <h6 class="fw-700 mb-3"><i class="bi bi-receipt me-2"></i>Ringkasan Pesanan</h6>
        <div style="max-height:280px;overflow-y:auto">
          <?php foreach($items as $item): ?>
          <div class="d-flex align-items-center gap-2 py-2 border-bottom">
            <img src="<?= productImage($item['image'],$item['product_id']) ?>" style="width:44px;height:44px;border-radius:8px;object-fit:cover" onerror="this.src='https://picsum.photos/seed/ck<?= $item['product_id'] ?>/80/80'">
            <div class="flex-grow-1">
              <div class="small fw-600"><?= e(truncate($item['name'],38)) ?></div>
              <div class="tiny text-muted"><?= formatRupiah($item['price']) ?> × <?= $item['quantity'] ?></div>
            </div>
            <div class="fw-700 small"><?= formatRupiah($item['subtotal']) ?></div>
          </div>
          <?php endforeach; ?>
        </div>
        <div class="mt-3">
          <div class="d-flex justify-content-between mb-2 small"><span class="text-muted">Subtotal</span><span><?= formatRupiah($subtotal) ?></span></div>
          <div class="d-flex justify-content-between mb-2 small d-none" id="discountRow">
            <span class="text-success"><i class="bi bi-tag me-1"></i>Diskon Voucher</span>
            <span class="text-success fw-600" id="discountAmt">-</span>
          </div>
          <div class="d-flex justify-content-between mb-2 small"><span class="text-muted">Ongkir</span><span class="text-success fw-600">Gratis</span></div>
          <div class="d-flex justify-content-between fw-800 fs-5 pt-2 border-top mt-2">
            <span>Total</span>
            <span class="text-success" id="totalDisplay"><?= formatRupiah($subtotal) ?></span>
          </div>
        </div>
        <div class="mt-3 p-2 rounded-2 text-muted small" style="background:var(--bg-soft)">
          <i class="bi bi-cash-stack me-1 text-success"></i>Pembayaran tunai saat barang tiba (COD)
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.getElementById('checkoutForm').addEventListener('submit', function(){
  const btn = document.getElementById('placeOrderBtn');
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses…';
});
</script>

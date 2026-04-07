<div class="d-flex align-items-center gap-3 mb-4">
  <a href="<?= APP_URL ?>/admin/orders" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
  <h5 class="fw-800 mb-0"><?= e($title) ?></h5>
  <span class="status-badge <?= $order['status'] ?>"><?= ucfirst($order['status']) ?></span>
</div>

<div class="row g-4">
  <div class="col-lg-8">
    <div class="a-card mb-4">
      <div class="a-card-head"><h6>Informasi Pelanggan & Pengiriman</h6></div>
      <div class="p-4">
        <div class="row g-3">
          <div class="col-sm-6"><div class="tiny text-muted fw-700 mb-1">NO. PESANAN</div><div class="fw-800"><?= generateOrderNumber($order['id']) ?></div></div>
          <div class="col-sm-6"><div class="tiny text-muted fw-700 mb-1">TANGGAL</div><div><?= date('d M Y, H:i', strtotime($order['created_at'])) ?></div></div>
          <div class="col-sm-6"><div class="tiny text-muted fw-700 mb-1">PELANGGAN</div><div class="fw-600"><?= e($order['user_name']) ?></div><div class="small text-muted"><?= e($order['user_email']) ?></div></div>
          <div class="col-sm-6"><div class="tiny text-muted fw-700 mb-1">PENERIMA</div><div class="fw-600"><?= e($order['recipient_name']) ?></div><div class="small text-muted"><?= e($order['phone']) ?></div></div>
          <div class="col-12"><div class="tiny text-muted fw-700 mb-1">ALAMAT PENGIRIMAN</div><div><?= e($order['address']) ?><?= $order['city']?', '.e($order['city']):'' ?></div></div>
          <?php if($order['notes']): ?>
            <div class="col-12"><div class="tiny text-muted fw-700 mb-1">CATATAN</div><div class="fst-italic text-muted"><?= e($order['notes']) ?></div></div>
          <?php endif; ?>
          <?php if($order['voucher_code']): ?>
            <div class="col-sm-6"><div class="tiny text-muted fw-700 mb-1">VOUCHER</div><span class="badge bg-warning text-dark"><?= e($order['voucher_code']) ?></span></div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="a-card">
      <div class="a-card-head"><h6>Item Pesanan</h6></div>
      <div class="table-responsive">
        <table class="table a-table">
          <thead><tr><th>Produk</th><th class="text-end">Harga</th><th class="text-center">Qty</th><th class="text-end">Subtotal</th></tr></thead>
          <tbody>
            <?php foreach($order['items'] as $item): ?>
            <tr>
              <td class="fw-600"><?= e($item['product_name']) ?></td>
              <td class="text-end text-muted"><?= formatRupiah($item['price']) ?></td>
              <td class="text-center"><?= $item['quantity'] ?>×</td>
              <td class="text-end fw-700"><?= formatRupiah($item['subtotal']) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot class="table-light">
            <tr><td colspan="3" class="text-end text-muted">Subtotal</td><td class="text-end fw-700"><?= formatRupiah($order['subtotal']) ?></td></tr>
            <?php if($order['discount_amount'] > 0): ?>
              <tr><td colspan="3" class="text-end text-success">Diskon Voucher</td><td class="text-end fw-700 text-success">-<?= formatRupiah($order['discount_amount']) ?></td></tr>
            <?php endif; ?>
            <tr><td colspan="3" class="text-end fw-800">Total</td><td class="text-end fw-800 text-success fs-5"><?= formatRupiah($order['total_price']) ?></td></tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="a-card p-4">
      <h6 class="fw-700 mb-3"><i class="bi bi-arrow-repeat me-2 text-primary"></i>Update Status</h6>
      <form action="<?= APP_URL ?>/admin/orders/<?= $order['id'] ?>/status" method="POST">
        <?= csrfField() ?>
        <select name="status" class="form-select mb-3">
          <?php foreach(['pending','processing','shipped','completed','cancelled'] as $s): ?>
            <option value="<?= $s ?>" <?= $order['status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
          <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary w-100 fw-700"><i class="bi bi-check-lg me-2"></i>Update Status</button>
      </form>

      <div class="mt-4 pt-3 border-top">
        <div class="small fw-700 text-muted mb-2">Keterangan Status</div>
        <?php
        $statusDesc = ['pending'=>'Menunggu konfirmasi','processing'=>'Sedang diproses','shipped'=>'Dalam pengiriman','completed'=>'Selesai dikirim','cancelled'=>'Dibatalkan'];
        foreach($statusDesc as $s=>$d): ?>
          <div class="d-flex align-items-center gap-2 mb-1">
            <span class="status-badge <?= $s ?>"><?= ucfirst($s) ?></span>
            <span class="tiny text-muted"><?= $d ?></span>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>

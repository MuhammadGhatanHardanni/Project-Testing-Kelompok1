<div class="d-flex justify-content-between align-items-center mb-4">
  <div><h5 class="fw-800 mb-1">Daftar Pesanan</h5><p class="text-muted small mb-0"><?= count($orders) ?> pesanan</p></div>
  <a href="<?= APP_URL ?>/admin/orders/export" class="btn btn-success fw-700">
    <i class="bi bi-download me-2"></i>Export CSV
  </a>
</div>

<!-- Filter -->
<div class="a-card mb-4 p-3">
  <form method="GET" action="<?= APP_URL ?>/admin/orders" class="row g-2 align-items-end">
    <div class="col-md-5">
      <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama pelanggan atau email…" value="<?= e($search) ?>">
    </div>
    <div class="col-md-3">
      <select name="status" class="form-select form-select-sm">
        <option value="">Semua Status</option>
        <?php foreach(['pending','processing','shipped','completed','cancelled'] as $s): ?>
          <option value="<?= $s ?>" <?= $status===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-4 d-flex gap-2">
      <button type="submit" class="btn btn-sm btn-primary w-100">Filter</button>
      <a href="<?= APP_URL ?>/admin/orders" class="btn btn-sm btn-outline-secondary">Reset</a>
    </div>
  </form>
</div>

<div class="a-card">
  <div class="table-responsive">
    <table class="table a-table align-middle">
      <thead><tr>
        <th>No. Pesanan</th><th>Pelanggan</th><th>Penerima</th><th>Total</th><th>Status</th><th>Tanggal</th><th width="80">Aksi</th>
      </tr></thead>
      <tbody>
        <?php if(empty($orders)): ?>
          <tr><td colspan="7" class="text-center text-muted py-5">Tidak ada pesanan</td></tr>
        <?php else: ?>
          <?php foreach($orders as $o): ?>
          <tr>
            <td class="fw-700 small"><?= generateOrderNumber($o['id']) ?></td>
            <td>
              <div class="fw-600 small"><?= e($o['user_name']) ?></div>
              <div class="tiny text-muted"><?= e($o['user_email']) ?></div>
            </td>
            <td>
              <div class="small"><?= e($o['recipient_name']) ?></div>
              <div class="tiny text-muted"><?= e($o['city']??'') ?></div>
            </td>
            <td class="fw-700 text-success"><?= formatRupiah($o['total_price']) ?></td>
            <td><span class="status-badge <?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span></td>
            <td>
              <div class="small"><?= date('d M Y', strtotime($o['created_at'])) ?></div>
              <div class="tiny text-muted"><?= date('H:i', strtotime($o['created_at'])) ?></div>
            </td>
            <td>
              <a href="<?= APP_URL ?>/admin/orders/<?= $o['id'] ?>" class="btn btn-xs btn-outline-primary">
                <i class="bi bi-eye"></i>
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

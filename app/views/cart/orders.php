<div class="page-banner"><div class="container">
  <nav aria-label="breadcrumb"><ol class="breadcrumb mb-1"><li class="breadcrumb-item"><a href="<?= APP_URL ?>/">Beranda</a></li><li class="breadcrumb-item active">Pesanan Saya</li></ol></nav>
  <h3><i class="bi bi-bag-check me-2"></i>Pesanan Saya</h3>
</div></div>
<div class="container pb-5">
  <?php if(empty($orders)): ?>
    <div class="empty-state">
      <i class="bi bi-bag-x empty-icon"></i>
      <h5 class="fw-700 mt-0">Belum ada pesanan</h5>
      <p class="text-muted">Mulai belanja produk segar favoritmu!</p>
      <a href="<?= APP_URL ?>/" class="btn btn-primary px-4">Belanja Sekarang</a>
    </div>
  <?php else: ?>
    <!-- Status filter tabs -->
    <div class="d-flex gap-2 flex-wrap mb-4 mt-3">
      <?php
      $statuses = [''=>'Semua','pending'=>'Pending','processing'=>'Diproses','shipped'=>'Dikirim','completed'=>'Selesai','cancelled'=>'Dibatalkan'];
      foreach($statuses as $s => $lbl):
        $count = count(array_filter($orders, fn($o) => $s==='' || $o['status']===$s));
      ?>
        <a href="?status=<?= $s ?>" class="cat-pill <?= ($status??'')===$s?'active':'' ?>" style="font-size:.8rem">
          <?= $lbl ?> <?php if($count>0): ?><span class="ms-1 badge bg-white text-dark" style="font-size:.65rem"><?= $count ?></span><?php endif; ?>
        </a>
      <?php endforeach; ?>
    </div>
    <div class="d-flex flex-column gap-3">
      <?php
      $filtered = $status ? array_filter($orders, fn($o) => $o['status']===$status) : $orders;
      foreach($filtered as $o):
      ?>
      <div class="p-card-box">
        <div class="p-card-head d-flex justify-content-between align-items-center flex-wrap gap-2">
          <div>
            <span class="fw-700" style="font-family:'Outfit',sans-serif"><?= generateOrderNumber($o['id']) ?></span>
            <span class="text-muted small ms-3"><i class="bi bi-clock me-1"></i><?= date('d M Y, H:i', strtotime($o['created_at'])) ?></span>
          </div>
          <span class="status-pill <?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span>
        </div>
        <div class="p-card-body">
          <div class="row g-3 align-items-center">
            <div class="col-sm-4">
              <div class="tiny text-muted fw-700 mb-1 text-uppercase">Penerima</div>
              <div class="fw-600 small"><?= e($o['recipient_name']) ?></div>
              <div class="tiny text-muted"><?= e($o['city']??'') ?></div>
            </div>
            <div class="col-sm-4">
              <div class="tiny text-muted fw-700 mb-1 text-uppercase">Total Bayar</div>
              <div class="fw-900 text-success" style="font-family:'Outfit',sans-serif;font-size:1.1rem"><?= formatRupiah($o['total_price']) ?></div>
              <?php if($o['discount_amount']>0): ?><div class="tiny text-muted">Hemat <?= formatRupiah($o['discount_amount']) ?></div><?php endif; ?>
            </div>
            <div class="col-sm-4 text-sm-end">
              <a href="<?= APP_URL ?>/orders/<?= $o['id'] ?>" class="btn btn-outline-primary btn-sm fw-700">
                Lihat Detail <i class="bi bi-chevron-right"></i>
              </a>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
      <?php if(empty($filtered)): ?>
        <div class="text-center text-muted py-4">Tidak ada pesanan dengan status ini.</div>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</div>

<div class="mb-4">
  <h5 class="fw-800 mb-1">Laporan Stok Produk</h5>
  <p class="text-muted small mb-0">Status stok seluruh produk aktif</p>
</div>

<!-- Summary cards -->
<?php
$total    = count($products);
$outStock = count(array_filter($products, fn($p) => $p['stock'] == 0));
$lowItems = count(array_filter($products, fn($p) => $p['stock'] > 0 && $p['stock'] <= 10));
$okItems  = $total - $outStock - $lowItems;
?>
<div class="row g-3 mb-4">
  <div class="col-sm-4">
    <div class="stat-card">
      <div class="stat-icon" style="background:#dcfce7;color:#16a34a"><i class="bi bi-check-circle-fill"></i></div>
      <div><div class="stat-num text-success"><?= $okItems ?></div><div class="stat-lbl">Stok Aman</div></div>
    </div>
  </div>
  <div class="col-sm-4">
    <div class="stat-card">
      <div class="stat-icon" style="background:#fef9c3;color:#a16207"><i class="bi bi-exclamation-triangle-fill"></i></div>
      <div><div class="stat-num text-warning"><?= $lowItems ?></div><div class="stat-lbl">Stok Menipis (≤10)</div></div>
    </div>
  </div>
  <div class="col-sm-4">
    <div class="stat-card">
      <div class="stat-icon" style="background:#fee2e2;color:#dc2626"><i class="bi bi-x-circle-fill"></i></div>
      <div><div class="stat-num text-danger"><?= $outStock ?></div><div class="stat-lbl">Stok Habis</div></div>
    </div>
  </div>
</div>

<!-- Alert: Low stock items -->
<?php if(!empty($lowStock)): ?>
<div class="a-card mb-4" style="border-left:4px solid #f59e0b">
  <div class="a-card-head" style="background:#fffbeb"><h6><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>Perlu Restock Segera (<?= count($lowStock) ?> produk)</h6></div>
  <div class="p-3 d-flex flex-wrap gap-2">
    <?php foreach($lowStock as $p): ?>
      <a href="<?= APP_URL ?>/admin/products/<?= $p['id'] ?>/edit"
         class="badge text-decoration-none py-2 px-3 <?= $p['stock']==0?'bg-danger':'bg-warning text-dark' ?>">
        <?= e(truncate($p['name'],30)) ?> (<?= $p['stock'] ?>)
      </a>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<!-- Full table -->
<div class="a-card">
  <div class="a-card-head d-flex justify-content-between">
    <h6>Semua Produk</h6>
    <span class="small text-muted"><?= $total ?> produk</span>
  </div>
  <div class="table-responsive">
    <table class="table a-table align-middle">
      <thead><tr><th>Produk</th><th>Kategori</th><th>Satuan</th><th class="text-center">Stok</th><th>Status Stok</th><th width="80">Aksi</th></tr></thead>
      <tbody>
        <?php foreach($products as $p):
          $sClass = $p['stock']==0 ? 'zero' : ($p['stock']<=10 ? 'low' : 'ok');
          $sLabel = $p['stock']==0 ? 'Habis' : ($p['stock']<=10 ? 'Menipis' : 'Aman');
          $sColor = $p['stock']==0 ? 'danger' : ($p['stock']<=10 ? 'warning' : 'success');
        ?>
        <tr>
          <td>
            <div class="fw-600 small"><?= e(truncate($p['name'],45)) ?></div>
            <?php if(!empty($p['is_featured'])): ?><span class="badge bg-warning text-dark" style="font-size:.6rem">Unggulan</span><?php endif; ?>
          </td>
          <td class="small"><?= e($p['category_name']??'—') ?></td>
          <td class="small text-muted"><?= e($p['unit']??'pcs') ?></td>
          <td class="text-center"><span class="stock-num <?= $sClass ?>"><?= $p['stock'] ?></span></td>
          <td><span class="badge bg-<?= $sColor ?>-subtle text-<?= $sColor ?> border border-<?= $sColor ?>-subtle"><?= $sLabel ?></span></td>
          <td>
            <a href="<?= APP_URL ?>/admin/products/<?= $p['id'] ?>/edit" class="btn btn-xs btn-outline-primary">
              <i class="bi bi-pencil me-1"></i>Edit
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

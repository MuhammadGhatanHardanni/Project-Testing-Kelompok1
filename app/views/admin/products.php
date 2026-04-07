<div class="d-flex justify-content-between align-items-center mb-4">
  <div><h5 class="fw-800 mb-1">Kelola Produk</h5><p class="text-muted small mb-0"><?= count($products) ?> produk</p></div>
  <a href="<?= APP_URL ?>/admin/products/create" class="btn btn-primary fw-700"><i class="bi bi-plus-lg me-2"></i>Tambah Produk</a>
</div>

<!-- Filter bar -->
<div class="a-card mb-4">
  <div class="p-3">
    <form method="GET" action="<?= APP_URL ?>/admin/products" class="row g-2 align-items-end">
      <div class="col-md-5">
        <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama produk…" value="<?= e($search) ?>">
      </div>
      <div class="col-md-4">
        <select name="category" class="form-select form-select-sm">
          <option value="">Semua Kategori</option>
          <?php foreach($categories as $c): ?>
            <option value="<?= $c['id'] ?>" <?= $catFilter==$c['id']?'selected':'' ?>><?= e($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3 d-flex gap-2">
        <button type="submit" class="btn btn-sm btn-primary w-100">Filter</button>
        <a href="<?= APP_URL ?>/admin/products" class="btn btn-sm btn-outline-secondary">Reset</a>
      </div>
    </form>
  </div>
</div>

<div class="a-card">
  <div class="table-responsive">
    <table class="table a-table align-middle">
      <thead><tr>
        <th width="50">ID</th>
        <th width="60">Foto</th>
        <th>Produk</th>
        <th>Kategori</th>
        <th>Harga</th>
        <th>Stok</th>
        <th>Status</th>
        <th width="100">Aksi</th>
      </tr></thead>
      <tbody>
        <?php if(empty($products)): ?>
          <tr><td colspan="8" class="text-center text-muted py-5">Tidak ada produk</td></tr>
        <?php else: ?>
          <?php foreach($products as $p): ?>
          <tr>
            <td class="text-muted small">#<?= $p['id'] ?></td>
            <td>
              <img src="<?= productImage($p['image'],$p['id']) ?>" class="a-thumb"
                   onerror="this.src='https://picsum.photos/seed/adm<?= $p['id'] ?>/60/60'">
            </td>
            <td>
              <div class="fw-700 small"><?= e(truncate($p['name'],40)) ?></div>
              <?php if(!empty($p['is_featured'])): ?><span class="badge bg-warning text-dark" style="font-size:.62rem">Unggulan</span><?php endif; ?>
              <?php if(!empty($p['original_price'])): ?><span class="badge bg-danger" style="font-size:.62rem">Diskon</span><?php endif; ?>
            </td>
            <td><span class="badge bg-light text-dark border"><?= e($p['category_name'] ?? '—') ?></span></td>
            <td>
              <div class="fw-700 small text-success"><?= formatRupiah($p['price']) ?></div>
              <?php if($p['original_price']): ?><div class="tiny text-muted text-decoration-line-through"><?= formatRupiah($p['original_price']) ?></div><?php endif; ?>
            </td>
            <td><span class="stock-num <?= $p['stock']==0?'zero':($p['stock']<=10?'low':'ok') ?>"><?= $p['stock'] ?></span></td>
            <td>
              <?php if($p['is_active']): ?>
                <span class="badge bg-success-subtle text-success border border-success-subtle">Aktif</span>
              <?php else: ?>
                <span class="badge bg-secondary">Nonaktif</span>
              <?php endif; ?>
            </td>
            <td>
              <div class="d-flex gap-1">
                <a href="<?= APP_URL ?>/admin/products/<?= $p['id'] ?>/edit" class="btn btn-xs btn-outline-primary" title="Edit">
                  <i class="bi bi-pencil"></i>
                </a>
                <form action="<?= APP_URL ?>/admin/products/<?= $p['id'] ?>/delete" method="POST">
                  <?= csrfField() ?>
                  <button type="submit" class="btn btn-xs btn-outline-danger" title="Hapus"
                          data-confirm="Hapus produk &quot;<?= e($p['name']) ?>&quot;? Tindakan ini tidak bisa dibatalkan.">
                    <i class="bi bi-trash3"></i>
                  </button>
                </form>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

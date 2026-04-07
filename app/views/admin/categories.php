<div class="d-flex justify-content-between align-items-center mb-4">
  <div><h5 class="fw-800 mb-1">Kelola Kategori</h5><p class="text-muted small mb-0"><?= count($categories) ?> kategori</p></div>
  <button class="btn btn-primary fw-700" data-bs-toggle="modal" data-bs-target="#addCatModal">
    <i class="bi bi-plus-lg me-2"></i>Tambah Kategori
  </button>
</div>

<div class="row g-3">
  <?php foreach($categories as $cat): ?>
  <div class="col-md-6 col-lg-4">
    <div class="cat-admin-card">
      <div class="cat-icon-box"><i class="bi <?= e($cat['icon']??'bi-grid') ?>"></i></div>
      <div class="flex-grow-1">
        <div class="fw-700"><?= e($cat['name']) ?></div>
        <div class="small text-muted"><?= $cat['product_count'] ?> produk · sort: <?= $cat['sort_order'] ?></div>
      </div>
      <div class="d-flex gap-1">
        <button class="btn btn-xs btn-outline-primary"
                data-bs-toggle="modal" data-bs-target="#editCatModal<?= $cat['id'] ?>">
          <i class="bi bi-pencil"></i>
        </button>
        <form action="<?= APP_URL ?>/admin/categories/<?= $cat['id'] ?>/delete" method="POST">
          <?= csrfField() ?>
          <button type="submit" class="btn btn-xs btn-outline-danger"
                  data-confirm="Hapus kategori &quot;<?= e($cat['name']) ?>&quot;?">
            <i class="bi bi-trash3"></i>
          </button>
        </form>
      </div>
    </div>

    <!-- Edit modal per category -->
    <div class="modal fade" id="editCatModal<?= $cat['id'] ?>" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content" style="background:var(--bg-card,#fff)">
          <div class="modal-header"><h6 class="modal-title fw-700">Edit Kategori</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
          <form action="<?= APP_URL ?>/admin/categories/<?= $cat['id'] ?>/update" method="POST">
            <?= csrfField() ?>
            <div class="modal-body">
              <div class="mb-3"><label class="form-label fw-600 small">Nama *</label><input type="text" name="name" class="form-control" value="<?= e($cat['name']) ?>" required></div>
              <div class="mb-3"><label class="form-label fw-600 small">Icon Bootstrap (bi-…)</label><input type="text" name="icon" class="form-control" value="<?= e($cat['icon']??'bi-grid') ?>" placeholder="bi-grid"></div>
              <div class="mb-3"><label class="form-label fw-600 small">Urutan Tampil</label><input type="number" name="sort_order" class="form-control" value="<?= $cat['sort_order'] ?>"></div>
              <div class="mb-3"><label class="form-label fw-600 small">Deskripsi</label><textarea name="description" class="form-control" rows="2"><?= e($cat['description']??'') ?></textarea></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary btn-sm fw-700">Simpan</button></div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCatModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content" style="background:#fff">
      <div class="modal-header"><h6 class="modal-title fw-700">Tambah Kategori Baru</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <form action="<?= APP_URL ?>/admin/categories/store" method="POST">
        <?= csrfField() ?>
        <div class="modal-body">
          <div class="mb-3"><label class="form-label fw-600 small">Nama Kategori *</label><input type="text" name="name" class="form-control" required placeholder="Misal: Rempah & Bumbu"></div>
          <div class="mb-3"><label class="form-label fw-600 small">Icon Bootstrap (bi-…)</label><input type="text" name="icon" class="form-control" value="bi-grid" placeholder="bi-flower1, bi-cup, dll"><div class="form-text">Lihat daftar icon di <a href="https://icons.getbootstrap.com" target="_blank">icons.getbootstrap.com</a></div></div>
          <div class="mb-3"><label class="form-label fw-600 small">Urutan Tampil</label><input type="number" name="sort_order" class="form-control" value="0"></div>
          <div class="mb-3"><label class="form-label fw-600 small">Deskripsi</label><textarea name="description" class="form-control" rows="2"></textarea></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary btn-sm fw-700">Tambah</button></div>
      </form>
    </div>
  </div>
</div>

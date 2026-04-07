<div class="d-flex justify-content-between align-items-center mb-4">
  <div><h5 class="fw-800 mb-1">Kelola Voucher</h5><p class="text-muted small mb-0"><?= count($vouchers) ?> voucher</p></div>
  <button class="btn btn-primary fw-700" data-bs-toggle="modal" data-bs-target="#addVoucherModal">
    <i class="bi bi-plus-lg me-2"></i>Buat Voucher
  </button>
</div>

<div class="row g-3 mb-4">
  <?php foreach($vouchers as $v):
    $isExpired = date('Y-m-d') > $v['valid_until'];
    $isFull    = $v['used_count'] >= $v['quota'];
    $isValid   = !$isExpired && !$isFull && $v['is_active'];
  ?>
  <div class="col-md-6">
    <div class="voucher-admin-card">
      <div class="d-flex align-items-start gap-3">
        <div style="background:<?= $isValid?'#16a34a':'#94a3b8' ?>;border-radius:10px;padding:.6rem .9rem;flex-shrink:0">
          <i class="bi bi-ticket-perforated text-white fs-5"></i>
        </div>
        <div class="flex-grow-1">
          <div class="d-flex align-items-center gap-2 mb-1">
            <span class="fw-800 font-monospace" style="font-size:1rem;letter-spacing:.05em"><?= e($v['code']) ?></span>
            <?php if(!$v['is_active']||$isExpired||$isFull): ?>
              <span class="badge bg-secondary">Tidak Aktif</span>
            <?php else: ?>
              <span class="badge bg-success">Aktif</span>
            <?php endif; ?>
          </div>
          <div class="small text-muted mb-1"><?= e($v['description']??'') ?></div>
          <div class="d-flex gap-3 flex-wrap">
            <span class="small fw-600 <?= $v['type']==='percentage'?'text-primary':'text-success' ?>">
              <?= $v['type']==='percentage' ? $v['value'].'%' : formatRupiah($v['value']) ?> off
              <?php if($v['max_discount']): ?><span class="text-muted">(maks <?= formatRupiah($v['max_discount']) ?>)</span><?php endif; ?>
            </span>
            <span class="small text-muted">Min. <?= formatRupiah($v['min_purchase']) ?></span>
            <span class="small <?= $isFull?'text-danger':'' ?>">Kuota: <?= $v['used_count'] ?>/<?= $v['quota'] ?></span>
          </div>
          <div class="tiny text-muted mt-1">
            <?= date('d M Y', strtotime($v['valid_from'])) ?> — <?= date('d M Y', strtotime($v['valid_until'])) ?>
            <?php if($isExpired): ?><span class="badge bg-danger ms-1">Kadaluarsa</span><?php endif; ?>
          </div>
        </div>
        <div class="d-flex flex-column gap-1">
          <button class="btn btn-xs btn-outline-primary"
                  data-bs-toggle="modal" data-bs-target="#editVoucher<?= $v['id'] ?>">
            <i class="bi bi-pencil"></i>
          </button>
          <form action="<?= APP_URL ?>/admin/vouchers/<?= $v['id'] ?>/delete" method="POST">
            <?= csrfField() ?>
            <button type="submit" class="btn btn-xs btn-outline-danger"
                    data-confirm="Hapus voucher <?= e($v['code']) ?>?">
              <i class="bi bi-trash3"></i>
            </button>
          </form>
        </div>
      </div>
    </div>

    <!-- Edit modal -->
    <div class="modal fade" id="editVoucher<?= $v['id'] ?>" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background:#fff">
          <div class="modal-header"><h6 class="modal-title fw-700">Edit Voucher</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
          <form action="<?= APP_URL ?>/admin/vouchers/<?= $v['id'] ?>/update" method="POST">
            <?= csrfField() ?>
            <div class="modal-body">
              <div class="row g-3">
                <div class="col-md-6"><label class="form-label fw-600 small">Kode</label><input type="text" name="code" class="form-control" value="<?= e($v['code']) ?>" required style="text-transform:uppercase"></div>
                <div class="col-md-3"><label class="form-label fw-600 small">Tipe</label><select name="type" class="form-select"><option value="fixed" <?= $v['type']==='fixed'?'selected':'' ?>>Nominal</option><option value="percentage" <?= $v['type']==='percentage'?'selected':'' ?>>Persentase</option></select></div>
                <div class="col-md-3"><label class="form-label fw-600 small">Nilai</label><input type="number" name="value" class="form-control" value="<?= $v['value'] ?>" step="0.01"></div>
                <div class="col-md-4"><label class="form-label fw-600 small">Min. Belanja (Rp)</label><input type="number" name="min_purchase" class="form-control" value="<?= $v['min_purchase'] ?>"></div>
                <div class="col-md-4"><label class="form-label fw-600 small">Maks Diskon (Rp)</label><input type="number" name="max_discount" class="form-control" value="<?= $v['max_discount']??'' ?>" placeholder="Kosongkan jika tidak ada"></div>
                <div class="col-md-4"><label class="form-label fw-600 small">Kuota</label><input type="number" name="quota" class="form-control" value="<?= $v['quota'] ?>"></div>
                <div class="col-md-6"><label class="form-label fw-600 small">Berlaku Dari</label><input type="date" name="valid_from" class="form-control" value="<?= $v['valid_from'] ?>"></div>
                <div class="col-md-6"><label class="form-label fw-600 small">Berlaku Sampai</label><input type="date" name="valid_until" class="form-control" value="<?= $v['valid_until'] ?>"></div>
                <div class="col-12"><label class="form-label fw-600 small">Deskripsi</label><input type="text" name="description" class="form-control" value="<?= e($v['description']??'') ?>"></div>
                <div class="col-12"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="is_active" value="1" <?= $v['is_active']?'checked':'' ?>><label class="form-check-label small">Voucher Aktif</label></div></div>
              </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary btn-sm fw-700">Simpan</button></div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Add Voucher Modal -->
<div class="modal fade" id="addVoucherModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" style="background:#fff">
      <div class="modal-header"><h6 class="modal-title fw-700">Buat Voucher Baru</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <form action="<?= APP_URL ?>/admin/vouchers/store" method="POST">
        <?= csrfField() ?>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label fw-600 small">Kode Voucher *</label><input type="text" name="code" class="form-control" required placeholder="SEGAR10" style="text-transform:uppercase"></div>
            <div class="col-md-3"><label class="form-label fw-600 small">Tipe *</label><select name="type" class="form-select"><option value="fixed">Nominal (Rp)</option><option value="percentage">Persentase (%)</option></select></div>
            <div class="col-md-3"><label class="form-label fw-600 small">Nilai *</label><input type="number" name="value" class="form-control" step="0.01" required></div>
            <div class="col-md-4"><label class="form-label fw-600 small">Min. Belanja (Rp)</label><input type="number" name="min_purchase" class="form-control" value="0"></div>
            <div class="col-md-4"><label class="form-label fw-600 small">Maks Diskon (Rp)</label><input type="number" name="max_discount" class="form-control" placeholder="Kosongkan jika tidak ada"></div>
            <div class="col-md-4"><label class="form-label fw-600 small">Kuota *</label><input type="number" name="quota" class="form-control" value="100" required></div>
            <div class="col-md-6"><label class="form-label fw-600 small">Berlaku Dari *</label><input type="date" name="valid_from" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
            <div class="col-md-6"><label class="form-label fw-600 small">Berlaku Sampai *</label><input type="date" name="valid_until" class="form-control" value="<?= date('Y-m-d', strtotime('+30 days')) ?>" required></div>
            <div class="col-12"><label class="form-label fw-600 small">Deskripsi</label><input type="text" name="description" class="form-control" placeholder="Keterangan voucher…"></div>
          </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary btn-sm fw-700">Buat Voucher</button></div>
      </form>
    </div>
  </div>
</div>

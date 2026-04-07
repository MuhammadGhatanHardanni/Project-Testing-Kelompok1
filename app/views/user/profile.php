<?php $tab = $tab ?? 'profile'; ?>
<div class="page-header"><div class="container"><h3><i class="bi bi-person-circle me-2"></i>Akun Saya</h3></div></div>
<div class="container pb-5">
  <div class="row g-4">

    <!-- Sidebar -->
    <div class="col-lg-3">
      <div class="profile-card mb-3">
        <div class="profile-card-body text-center py-4">
          <?php if(!empty($user['avatar'])): ?>
            <img src="<?= avatarUrl($user['avatar']) ?>" class="rounded-circle mb-3" style="width:80px;height:80px;object-fit:cover;border:3px solid var(--green)">
          <?php else: ?>
            <div class="user-avatar user-avatar-xl mx-auto mb-3"><?= strtoupper(mb_substr($user['name'],0,1)) ?></div>
          <?php endif; ?>
          <div class="fw-700"><?= e($user['name']) ?></div>
          <div class="text-muted small"><?= e($user['email']) ?></div>
          <div class="badge bg-success mt-1">Member</div>

          <!-- Avatar upload -->
          <form action="<?= APP_URL ?>/profile/avatar" method="POST" enctype="multipart/form-data" class="mt-3">
            <?= csrfField() ?>
            <label class="btn btn-sm btn-outline-secondary w-100" style="cursor:pointer">
              <i class="bi bi-camera me-1"></i>Ganti Foto
              <input type="file" name="avatar" accept="image/*" class="d-none" onchange="this.form.submit()">
            </label>
          </form>
        </div>
      </div>

      <!-- Nav tabs -->
      <div class="profile-card">
        <div class="profile-card-body p-2">
          <nav class="nav flex-column profile-tabs gap-1">
            <a class="nav-link <?= $tab==='profile'    ?'active':'' ?>" href="?tab=profile">
              <i class="bi bi-person me-2"></i>Profil
            </a>
            <a class="nav-link <?= $tab==='addresses'  ?'active':'' ?>" href="?tab=addresses">
              <i class="bi bi-geo-alt me-2"></i>Alamat
              <span class="badge bg-success ms-auto"><?= count($addresses) ?></span>
            </a>
            <a class="nav-link <?= $tab==='orders'     ?'active':'' ?>" href="?tab=orders">
              <i class="bi bi-bag-check me-2"></i>Pesanan
              <span class="badge bg-success ms-auto"><?= count($orders) ?></span>
            </a>
            <a class="nav-link <?= $tab==='security'   ?'active':'' ?>" href="?tab=security">
              <i class="bi bi-shield-lock me-2"></i>Keamanan
            </a>
          </nav>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="col-lg-9">

      <?php if($tab === 'profile'): ?>
      <div class="profile-card">
        <div class="profile-card-header"><strong>Edit Profil</strong></div>
        <div class="profile-card-body">
          <form action="<?= APP_URL ?>/profile/update" method="POST">
            <?= csrfField() ?>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-600 small">Nama Lengkap</label>
                <input type="text" name="name" class="form-control" value="<?= e($user['name']) ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-600 small">Email <span class="text-muted">(tidak dapat diubah)</span></label>
                <input type="email" class="form-control" value="<?= e($user['email']) ?>" disabled>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-600 small">Nomor Telepon</label>
                <input type="text" name="phone" class="form-control" value="<?= e($user['phone'] ?? '') ?>" placeholder="08xxxxxxxxxx">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-600 small">Bergabung Sejak</label>
                <input type="text" class="form-control" value="<?= date('d M Y', strtotime($user['created_at'])) ?>" disabled>
              </div>
            </div>
            <div class="mt-4">
              <button type="submit" class="btn btn-primary fw-700"><i class="bi bi-check-lg me-2"></i>Simpan Perubahan</button>
            </div>
          </form>
        </div>
      </div>

      <?php elseif($tab === 'addresses'): ?>
      <div class="profile-card mb-4">
        <div class="profile-card-header d-flex justify-content-between align-items-center">
          <strong>Alamat Saya</strong>
          <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addAddrModal">
            <i class="bi bi-plus me-1"></i>Tambah Alamat
          </button>
        </div>
        <div class="profile-card-body">
          <?php if(empty($addresses)): ?>
            <div class="text-center text-muted py-4"><i class="bi bi-geo-alt fs-3 d-block mb-2"></i>Belum ada alamat tersimpan.</div>
          <?php else: ?>
            <div class="d-flex flex-column gap-3">
              <?php foreach($addresses as $a): ?>
              <div class="addr-card <?= $a['is_primary']?'primary':'' ?>">
                <div class="d-flex justify-content-between align-items-start">
                  <div>
                    <span class="badge bg-success me-1"><?= e($a['label']) ?></span>
                    <?php if($a['is_primary']): ?><span class="badge bg-warning text-dark">Utama</span><?php endif; ?>
                    <div class="fw-700 mt-1"><?= e($a['recipient']) ?> · <?= e($a['phone']) ?></div>
                    <div class="text-muted small"><?= e($a['address']) ?></div>
                    <div class="text-muted small"><?= e($a['city']) ?>, <?= e($a['province']) ?> <?= e($a['postal_code']) ?></div>
                  </div>
                  <form action="<?= APP_URL ?>/addresses/<?= $a['id'] ?>/delete" method="POST"
                        onsubmit="return confirm('Hapus alamat ini?')">
                    <?= csrfField() ?>
                    <button type="submit" class="btn btn-xs btn-outline-danger"><i class="bi bi-trash3"></i></button>
                  </form>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Add Address Modal -->
      <div class="modal fade" id="addAddrModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
          <div class="modal-content" style="background:var(--bg-card);border-color:var(--border)">
            <div class="modal-header border-bottom" style="border-color:var(--border)!important">
              <h5 class="modal-title fw-700">Tambah Alamat Baru</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= APP_URL ?>/addresses/add" method="POST">
              <?= csrfField() ?>
              <div class="modal-body">
                <div class="row g-3">
                  <div class="col-md-4">
                    <label class="form-label fw-600 small">Label Alamat</label>
                    <select name="label" class="form-select">
                      <option>Rumah</option><option>Kantor</option><option>Kos</option><option>Lainnya</option>
                    </select>
                  </div>
                  <div class="col-md-8">
                    <label class="form-label fw-600 small">Nama Penerima *</label>
                    <input type="text" name="recipient" class="form-control" placeholder="Nama lengkap" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-600 small">No. Telepon *</label>
                    <input type="text" name="phone" class="form-control" placeholder="08xxxxxxxxxx" required>
                  </div>
                  <div class="col-12">
                    <label class="form-label fw-600 small">Alamat Lengkap *</label>
                    <textarea name="address" class="form-control" rows="2" placeholder="Jalan, No, RT/RW, Kelurahan, Kecamatan" required></textarea>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label fw-600 small">Kota *</label>
                    <input type="text" name="city" class="form-control" required>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label fw-600 small">Provinsi *</label>
                    <input type="text" name="province" class="form-control" required>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label fw-600 small">Kode Pos</label>
                    <input type="text" name="postal_code" class="form-control" placeholder="55xxx">
                  </div>
                  <div class="col-12">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="is_primary" value="1" id="isPrimary">
                      <label class="form-check-label small" for="isPrimary">Jadikan alamat utama</label>
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal-footer border-top" style="border-color:var(--border)!important">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary fw-700">Simpan Alamat</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <?php elseif($tab === 'orders'): ?>
      <div class="profile-card">
        <div class="profile-card-header"><strong>Riwayat Pesanan</strong></div>
        <div class="profile-card-body p-0">
          <?php if(empty($orders)): ?>
            <div class="text-center text-muted py-5"><i class="bi bi-bag-x fs-2 d-block mb-2"></i>Belum ada pesanan.</div>
          <?php else: ?>
            <?php foreach($orders as $o): ?>
            <div class="d-flex align-items-center justify-content-between p-3 border-bottom flex-wrap gap-2">
              <div>
                <div class="fw-700 small"><?= generateOrderNumber($o['id']) ?></div>
                <div class="tiny text-muted"><?= date('d M Y', strtotime($o['created_at'])) ?></div>
              </div>
              <span class="status-badge <?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span>
              <div class="fw-700 text-success"><?= formatRupiah($o['total_price']) ?></div>
              <a href="<?= APP_URL ?>/orders/<?= $o['id'] ?>" class="btn btn-xs btn-outline-primary">Detail</a>
            </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>

      <?php elseif($tab === 'security'): ?>
      <div class="profile-card">
        <div class="profile-card-header"><strong>Ubah Password</strong></div>
        <div class="profile-card-body">
          <form action="<?= APP_URL ?>/profile/password" method="POST">
            <?= csrfField() ?>
            <div class="mb-3" style="max-width:420px">
              <label class="form-label fw-600 small">Password Saat Ini</label>
              <input type="password" name="current_password" class="form-control" required>
            </div>
            <div class="mb-3" style="max-width:420px">
              <label class="form-label fw-600 small">Password Baru (min. 6 karakter)</label>
              <input type="password" name="new_password" class="form-control" required>
            </div>
            <div class="mb-4" style="max-width:420px">
              <label class="form-label fw-600 small">Konfirmasi Password Baru</label>
              <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary fw-700"><i class="bi bi-shield-check me-2"></i>Ubah Password</button>
          </form>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

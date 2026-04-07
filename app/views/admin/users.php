<div class="mb-4"><h5 class="fw-800 mb-1">Manajemen Pengguna</h5><p class="text-muted small mb-0"><?= count($users) ?> pengguna terdaftar</p></div>

<div class="a-card">
  <div class="table-responsive">
    <table class="table a-table align-middle">
      <thead><tr><th>ID</th><th>Nama</th><th>Email</th><th>Telepon</th><th>Role</th><th>Pesanan</th><th>Status</th><th>Bergabung</th><th width="80">Aksi</th></tr></thead>
      <tbody>
        <?php foreach($users as $u): ?>
        <tr>
          <td class="text-muted small">#<?= $u['id'] ?></td>
          <td>
            <div class="d-flex align-items-center gap-2">
              <div class="user-avatar" style="width:32px;height:32px;font-size:.8rem;flex-shrink:0"><?= strtoupper(mb_substr($u['name'],0,1)) ?></div>
              <span class="fw-600 small"><?= e($u['name']) ?></span>
            </div>
          </td>
          <td class="small text-muted"><?= e($u['email']) ?></td>
          <td class="small"><?= e($u['phone']??'—') ?></td>
          <td>
            <?php if($u['role']==='admin'): ?>
              <span class="badge bg-danger">Admin</span>
            <?php else: ?>
              <span class="badge bg-primary">User</span>
            <?php endif; ?>
          </td>
          <td class="text-center fw-700"><?= $u['order_count'] ?></td>
          <td>
            <?php if($u['is_active']): ?>
              <span class="badge bg-success-subtle text-success border border-success-subtle">Aktif</span>
            <?php else: ?>
              <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Nonaktif</span>
            <?php endif; ?>
          </td>
          <td class="small text-muted"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
          <td>
            <?php if($u['id'] != $_SESSION['user_id']): ?>
              <form action="<?= APP_URL ?>/admin/users/<?= $u['id'] ?>/toggle" method="POST">
                <?= csrfField() ?>
                <button type="submit" class="btn btn-xs <?= $u['is_active']?'btn-outline-warning':'btn-outline-success' ?>"
                        data-confirm="<?= $u['is_active']?'Nonaktifkan':'Aktifkan' ?> user ini?">
                  <i class="bi bi-<?= $u['is_active']?'person-slash':'person-check' ?>"></i>
                </button>
              </form>
            <?php else: ?>
              <span class="text-muted tiny">Anda</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

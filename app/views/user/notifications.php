<div class="page-header"><div class="container"><h3><i class="bi bi-bell-fill me-2"></i>Notifikasi</h3></div></div>
<div class="container pb-5" style="max-width:700px">
  <?php if(empty($notifications)): ?>
    <div class="empty-state">
      <i class="bi bi-bell-slash empty-icon"></i>
      <h5 class="fw-700">Belum ada notifikasi</h5>
      <p class="text-muted">Aktivitas dan promosi terbaru akan muncul di sini.</p>
    </div>
  <?php else: ?>
    <div class="profile-card">
      <div class="profile-card-header d-flex justify-content-between">
        <strong><?= count($notifications) ?> notifikasi</strong>
        <span class="small text-muted">Semua sudah dibaca</span>
      </div>
      <div class="profile-card-body p-0">
        <?php foreach($notifications as $n):
          $iconClass = match($n['type']) {
            'order' => 'order',
            'promo' => 'promo',
            default => 'info',
          };
          $icon = match($n['type']) {
            'order' => 'bi-bag-check',
            'promo' => 'bi-tag-fill',
            default => 'bi-info-circle-fill',
          };
        ?>
        <div class="notif-item <?= !$n['is_read'] ? 'unread' : '' ?>"
             onclick="if('<?= $n['link'] ?>')window.location='<?= APP_URL . $n['link'] ?>'">
          <div class="notif-icon <?= $iconClass ?>">
            <i class="bi <?= $icon ?>"></i>
          </div>
          <div class="flex-grow-1">
            <div class="fw-700 small"><?= e($n['title']) ?></div>
            <div class="text-muted small"><?= e($n['message']) ?></div>
          </div>
          <div class="tiny text-muted flex-shrink-0"><?= timeAgo($n['created_at']) ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>
</div>

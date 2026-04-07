<?php
$errors = $_SESSION['reg_errors'] ?? [];
$old    = $_SESSION['reg_old']    ?? [];
unset($_SESSION['reg_errors'], $_SESSION['reg_old']);
?>
<div class="auth-page">
  <div class="auth-card">
    <div class="text-center mb-4">
      <div class="brand-logo mx-auto mb-3" style="width:56px;height:56px;font-size:1.5rem;border-radius:16px"><i class="bi bi-basket2-fill"></i></div>
      <h4 class="fw-800 mb-1" style="font-family:'Outfit',sans-serif">Daftar di <?= APP_NAME ?></h4>
      <p class="text-muted small mb-0">Gratis & langsung bisa belanja!</p>
    </div>

    <?php if(!empty($errors)): ?>
      <div class="alert alert-danger py-2 small">
        <ul class="mb-0 ps-3"><?php foreach($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?></ul>
      </div>
    <?php endif; ?>

    <form action="<?= APP_URL ?>/auth/register" method="POST" novalidate>
      <?= csrfField() ?>
      <div class="mb-3">
        <label class="form-label">Nama Lengkap</label>
        <div class="i-wrap">
          <i class="bi bi-person"></i>
          <input type="text" name="name" class="form-control ps-i" placeholder="Nama lengkap"
                 value="<?= e($old['name']??'') ?>" required>
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <div class="i-wrap">
          <i class="bi bi-envelope"></i>
          <input type="email" name="email" class="form-control ps-i" placeholder="nama@email.com"
                 value="<?= e($old['email']??'') ?>" required>
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label">Password <span class="text-muted fw-400">(min. 6 karakter)</span></label>
        <div class="i-wrap">
          <i class="bi bi-lock"></i>
          <input type="password" name="password" class="form-control ps-i" placeholder="Buat password" required>
        </div>
      </div>
      <div class="mb-4">
        <label class="form-label">Konfirmasi Password</label>
        <div class="i-wrap">
          <i class="bi bi-lock-fill"></i>
          <input type="password" name="password_confirm" class="form-control ps-i" placeholder="Ulangi password" required>
        </div>
      </div>
      <button type="submit" class="btn btn-primary w-100 fw-700" style="padding:.7rem">
        <i class="bi bi-person-plus me-2"></i>Buat Akun Gratis
      </button>
      <p class="text-muted text-center mt-3 mb-0" style="font-size:.78rem">
        Dengan mendaftar, Anda menyetujui syarat & ketentuan DailyMart.
      </p>
    </form>

    <div class="divider-text"><span>atau</span></div>
    <p class="text-center text-muted small mb-0">
      Sudah punya akun?
      <a href="<?= APP_URL ?>/auth/login" class="fw-700 text-success">Masuk di sini</a>
    </p>
  </div>
</div>

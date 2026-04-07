<div class="auth-page">
  <div class="auth-card">
    <div class="text-center mb-4">
      <div class="brand-logo mx-auto mb-3" style="width:56px;height:56px;font-size:1.5rem;border-radius:16px"><i class="bi bi-basket2-fill"></i></div>
      <h4 class="fw-800 mb-1" style="font-family:'Outfit',sans-serif;letter-spacing:-.02em"><?= APP_NAME ?></h4>
      <p class="text-muted small mb-0"><?= APP_TAGLINE ?></p>
    </div>

    <form action="<?= APP_URL ?>/auth/login" method="POST" novalidate>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <div class="i-wrap">
          <i class="bi bi-envelope"></i>
          <input type="email" name="email" class="form-control ps-i" placeholder="nama@email.com"
                 value="<?= e($_POST['email'] ?? '') ?>" autocomplete="email" required>
        </div>
      </div>
      <div class="mb-4">
        <label class="form-label">Password</label>
        <div class="i-wrap">
          <i class="bi bi-lock"></i>
          <input type="password" name="password" class="form-control ps-i" placeholder="••••••••"
                 autocomplete="current-password" required>
        </div>
      </div>
      <button type="submit" class="btn btn-primary w-100 fw-700" style="padding:.7rem">
        <i class="bi bi-box-arrow-in-right me-2"></i>Masuk ke Akun
      </button>
    </form>

    <div class="divider-text"><span>atau</span></div>
    <p class="text-center text-muted small mb-0">
      Belum punya akun?
      <a href="<?= APP_URL ?>/auth/register" class="fw-700 text-success">Daftar gratis</a>
    </p>
    </div>
  </div>
</div>
<script>
</script>

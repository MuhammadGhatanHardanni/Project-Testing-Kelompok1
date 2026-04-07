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

    <div class="mt-4 pt-3 border-top">
      <p class="tiny text-muted text-center mb-2"><i class="bi bi-info-circle me-1"></i>Akun Demo — klik untuk isi otomatis</p>
      <div class="demo-cred mb-2" onclick="fillDemo('admin@dailymart.id','password123')">
        <span class="badge bg-danger me-2" style="font-family:'Outfit',sans-serif">Admin</span>
        admin@dailymart.id / <strong>password123</strong>
      </div>
      <div class="demo-cred" onclick="fillDemo('budi@example.com','password123')">
        <span class="badge bg-primary me-2" style="font-family:'Outfit',sans-serif">User</span>
        budi@example.com / <strong>password123</strong>
      </div>
    </div>
  </div>
</div>
<script>
function fillDemo(e,p){
  document.querySelector('input[name="email"]').value=e;
  document.querySelector('input[name="password"]').value=p;
  if(typeof toast==='function') toast('Kredensial diisi otomatis','info');
}
</script>

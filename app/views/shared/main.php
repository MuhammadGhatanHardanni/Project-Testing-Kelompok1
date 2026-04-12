<!DOCTYPE html>
<html lang="id" data-theme="<?= isset($_COOKIE['dm_theme']) && $_COOKIE['dm_theme']==='dark' ? 'dark' : 'light' ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?= e($title ?? APP_NAME) ?></title>
<meta name="description" content="<?= APP_TAGLINE ?> — Produk segar berkualitas, dikirim langsung ke rumah Anda.">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= APP_URL ?>/css/app.css">
</head>
<body>

<!-- ── NAVBAR ─────────────────────────────────────────────────── -->
<nav class="navbar navbar-expand-lg sticky-top py-0" id="mainNav" style="height:var(--nav-h)">
<div class="container">

  <a class="nav-brand me-3" href="<?= APP_URL ?>/">
    <div class="brand-logo"><i class="bi bi-basket2-fill"></i></div>
    <div class="brand-text">
      <span class="brand-name"><?= APP_NAME ?></span>
      <span class="brand-tagline"><?= APP_TAGLINE ?></span>
    </div>
  </a>

  <!-- Desktop search -->
  <div class="nav-search d-none d-lg-flex">
    <form action="<?= APP_URL ?>/" method="GET" class="w-100">
      <div class="input-group">
        <input type="text" name="search" class="form-control border-0" placeholder="Cari produk segar…" value="<?= e($_GET['search'] ?? '') ?>" autocomplete="off">
        <button class="btn" type="submit" style="background:var(--brand);color:#fff;border-radius:0 var(--radius-pill) var(--radius-pill) 0;padding:.5rem 1.1rem"><i class="bi bi-search"></i></button>
      </div>
    </form>
  </div>

  <button class="navbar-toggler border-0 ms-auto me-2" type="button" data-bs-toggle="collapse" data-bs-target="#navCollapse">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navCollapse">
    <!-- Mobile search -->
    <form action="<?= APP_URL ?>/" method="GET" class="d-lg-none my-2">
      <div class="input-group">
        <input type="text" name="search" class="form-control" placeholder="Cari produk…" value="<?= e($_GET['search'] ?? '') ?>">
        <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
      </div>
    </form>

    <div class="d-flex align-items-center gap-2 ms-lg-3 mt-2 mt-lg-0 pb-2 pb-lg-0">
      <!-- Dark mode -->
      <button class="nav-btn theme-toggle" title="Tema">
        <i class="bi bi-sun-fill theme-icon-sun"></i>
        <i class="bi bi-moon-fill theme-icon-moon d-none"></i>
      </button>

      <?php if(isLoggedIn()): ?>
        <a href="<?= APP_URL ?>/wishlist" class="nav-btn" title="Wishlist">
          <i class="bi bi-heart"></i>
          <?php if(wishlistCount()>0): ?><span class="badge-dot wishlist-count-badge"><?= wishlistCount() ?></span><?php endif; ?>
        </a>
        <a href="<?= APP_URL ?>/notifications" class="nav-btn" title="Notifikasi">
          <i class="bi bi-bell"></i>
          <?php if(notifCount()>0): ?><span class="badge-dot"><?= notifCount() ?></span><?php endif; ?>
        </a>
        <a href="<?= APP_URL ?>/cart" class="nav-btn" title="Keranjang">
          <i class="bi bi-bag2"></i>
          <?php if(cartCount()>0): ?><span class="badge-dot cart-count-badge"><?= cartCount() ?></span><?php endif; ?>
        </a>

        <!-- User dropdown -->
        <div class="dropdown">
          <button class="btn d-flex align-items-center gap-2 px-2 py-1 rounded-3 border" style="border-color:var(--border)!important" data-bs-toggle="dropdown">
            <div class="u-avatar" style="width:30px;height:30px;font-size:.75rem"><?= strtoupper(mb_substr($_SESSION['user_name'],0,1)) ?></div>
            <span class="fw-600 d-none d-xl-inline small"><?= e(explode(' ',$_SESSION['user_name'])[0]) ?></span>
            <i class="bi bi-chevron-down small text-muted" style="font-size:.65rem"></i>
          </button>
          <ul class="dropdown-menu dropdown-menu-end mt-1" style="min-width:200px">
            <li><div class="px-3 py-2"><div class="fw-600 small"><?= e($_SESSION['user_name']) ?></div><div class="text-muted" style="font-size:.75rem"><?= e($_SESSION['user_email']) ?></div></div></li>
            <li><hr class="dropdown-divider my-1"></li>
            <li><a class="dropdown-item" href="<?= APP_URL ?>/profile"><i class="bi bi-person-circle me-2 text-primary"></i>Profil Saya</a></li>
            <li><a class="dropdown-item" href="<?= APP_URL ?>/orders"><i class="bi bi-bag-check me-2 text-success"></i>Pesanan Saya</a></li>
            <li><a class="dropdown-item" href="<?= APP_URL ?>/wishlist"><i class="bi bi-heart me-2 text-danger"></i>Wishlist</a></li>
            <?php if(isAdmin()): ?>
              <li><hr class="dropdown-divider my-1"></li>
              <li><a class="dropdown-item fw-600" href="<?= APP_URL ?>/admin"><i class="bi bi-shield-check me-2 text-warning"></i>Admin Panel</a></li>
            <?php endif; ?>
            <li><hr class="dropdown-divider my-1"></li>
            <li><a class="dropdown-item text-danger" href="<?= APP_URL ?>/auth/logout"><i class="bi bi-box-arrow-right me-2"></i>Keluar</a></li>
          </ul>
        </div>
      <?php else: ?>
        <a href="<?= APP_URL ?>/auth/login" class="btn btn-outline-secondary btn-sm px-3">Masuk</a>
        <a href="<?= APP_URL ?>/auth/register" class="btn btn-primary btn-sm px-3">Daftar Gratis</a>
      <?php endif; ?>
    </div>
  </div>
</div>
</nav>

<!-- ── FLASH MESSAGES ───────────────────────────────────────────── -->
<div class="container mt-3">
<?php
foreach(['success','error','info','warning'] as $t){
  $m=flash($t); if(!$m) continue;
  $bt=($t==='error')?'danger':$t;
  $icons=['success'=>'check-circle-fill','error'=>'exclamation-triangle-fill','info'=>'info-circle-fill','warning'=>'exclamation-circle-fill'];
?>
<div class="alert alert-<?= $bt ?> alert-dismissible d-flex align-items-center gap-2 fade show py-2 shadow-sm">
  <i class="bi bi-<?= $icons[$t] ?> flex-shrink-0"></i>
  <div class="flex-grow-1 small"><?= e($m) ?></div>
  <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
</div>
<?php } ?>
</div>

<main class="main-content"><?= $content ?></main>

<!-- ── FOOTER ──────────────────────────────────────────────────── -->
<footer class="site-footer">
<div class="container">
  <div class="row g-4">
    <div class="col-lg-4">
      <div class="d-flex align-items-center gap-2 mb-3">
        <div class="brand-logo"><i class="bi bi-basket2-fill"></i></div>
        <span class="footer-brand-name"><?= APP_NAME ?></span>
      </div>
      <p class="text-muted small mb-3"><?= APP_TAGLINE ?>. Produk segar berkualitas diantarkan langsung ke rumah Anda setiap hari.</p>
      <div class="d-flex gap-2">
        <a href="#" class="nav-btn" style="width:36px;height:36px;font-size:.9rem"><i class="bi bi-instagram"></i></a>
        <a href="#" class="nav-btn" style="width:36px;height:36px;font-size:.9rem"><i class="bi bi-tiktok"></i></a>
        <a href="#" class="nav-btn" style="width:36px;height:36px;font-size:.9rem"><i class="bi bi-facebook"></i></a>
      </div>
    </div>
    <div class="col-6 col-lg-2">
      <div class="fw-700 small mb-2" style="font-family:'Outfit',sans-serif">Belanja</div>
      <a href="<?= APP_URL ?>/" class="footer-link">Semua Produk</a>
      <a href="<?= APP_URL ?>/?category=1" class="footer-link">Buah Segar</a>
      <a href="<?= APP_URL ?>/?category=2" class="footer-link">Sayuran</a>
      <a href="<?= APP_URL ?>/?sort=popular" class="footer-link">Terlaris</a>
    </div>
    <div class="col-6 col-lg-2">
      <div class="fw-700 small mb-2" style="font-family:'Outfit',sans-serif">Akun</div>
      <a href="<?= APP_URL ?>/auth/register" class="footer-link">Daftar</a>
      <a href="<?= APP_URL ?>/auth/login" class="footer-link">Masuk</a>
      <a href="<?= APP_URL ?>/profile" class="footer-link">Profil</a>
      <a href="<?= APP_URL ?>/orders" class="footer-link">Pesanan</a>
    </div>
    <div class="col-lg-4">
      <div class="fw-700 small mb-2" style="font-family:'Outfit',sans-serif">Kontak</div>
      <p class="footer-link"><i class="bi bi-envelope me-2"></i>hello@dailymart.id</p>
      <p class="footer-link"><i class="bi bi-telephone me-2"></i>0800-DAILY-ID</p>
      <p class="footer-link"><i class="bi bi-geo-alt me-2"></i>Jakarta, Indonesia</p>
      <div class="mt-3 p-3 rounded-3" style="background:var(--brand-light)">
        <div class="fw-700 small mb-1" style="font-family:'Outfit',sans-serif;color:var(--brand)">
          <i class="bi bi-shield-check me-1"></i>Belanja Aman & Terpercaya
        </div>
        <div class="text-muted" style="font-size:.75rem">Garansi Kesegaran · Gratis Ongkir</div>
      </div>
    </div>
  </div>
  <div class="footer-copy">
    <span class="text-muted small">&copy; <?= date('Y') ?> <?= APP_NAME ?>. Hak cipta dilindungi.</span>
    <span class="text-muted" style="font-size:.72rem">v<?= APP_TAGLINE ?> · Platform Belanja</span>
  </div>
</div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const BASE_URL   = '<?= APP_URL ?>';
const CSRF_TOKEN = '<?= csrfToken() ?>';
</script>
<script src="<?= APP_URL ?>/js/app.js"></script>
</body>
</html>

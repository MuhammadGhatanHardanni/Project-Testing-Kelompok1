<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?= e($title ?? 'Admin') ?> — <?= APP_NAME ?> Admin</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<link rel="stylesheet" href="<?= APP_URL ?>/css/app.css">
<link rel="stylesheet" href="<?= APP_URL ?>/css/admin.css">
</head>
<body class="admin-body">
<div class="admin-wrap">

  <!-- SIDEBAR -->
  <aside class="adm-sidebar">
    <div class="sb-brand">
      <div class="sb-brand-icon"><i class="bi bi-basket2-fill"></i></div>
      <div>
        <span class="sb-brand-name"><?= APP_NAME ?></span>
        <span class="sb-brand-ver">v<?= APP_VERSION ?></span>
      </div>
    </div>

    <div class="sb-section">Utama</div>
    <nav class="sb-nav">
      <?php
      $uri = $_SERVER['REQUEST_URI'];
      $isA = fn($p) => str_contains($uri,$p) ? 'active' : '';
      $isDash = !str_contains($uri,'products') && !str_contains($uri,'orders') && !str_contains($uri,'users') && !str_contains($uri,'categories') && !str_contains($uri,'vouchers') && !str_contains($uri,'stock') ? 'active' : '';
      ?>
      <a href="<?= APP_URL ?>/admin"              class="sb-link <?= $isDash ?>"><i class="bi bi-speedometer2"></i> Dashboard</a>
      <a href="<?= APP_URL ?>/admin/products"     class="sb-link <?= $isA('admin/products') ?>"><i class="bi bi-box-seam"></i> Produk</a>
      <a href="<?= APP_URL ?>/admin/categories"   class="sb-link <?= $isA('admin/categories') ?>"><i class="bi bi-tags"></i> Kategori</a>
      <a href="<?= APP_URL ?>/admin/orders"       class="sb-link <?= $isA('admin/orders') ?>"><i class="bi bi-receipt"></i> Pesanan</a>
    </nav>

    <div class="sb-section">Manajemen</div>
    <nav class="sb-nav">
      <a href="<?= APP_URL ?>/admin/users"        class="sb-link <?= $isA('admin/users') ?>"><i class="bi bi-people"></i> Pengguna</a>
      <a href="<?= APP_URL ?>/admin/vouchers"     class="sb-link <?= $isA('admin/vouchers') ?>"><i class="bi bi-ticket-perforated"></i> Voucher</a>
      <a href="<?= APP_URL ?>/admin/stock"        class="sb-link <?= $isA('admin/stock') ?>"><i class="bi bi-bar-chart-line"></i> Laporan Stok</a>
    </nav>

    <div class="sb-div"></div>
    <nav class="sb-nav">
      <a href="<?= APP_URL ?>/"             class="sb-link"><i class="bi bi-shop"></i> Lihat Toko</a>
      <a href="<?= APP_URL ?>/auth/logout"  class="sb-link danger"><i class="bi bi-box-arrow-right"></i> Keluar</a>
    </nav>

    <div class="sb-user">
      <div class="u-av-sm"><?= strtoupper(mb_substr($_SESSION['user_name']??'A',0,1)) ?></div>
      <div>
        <div class="sb-user-name"><?= e($_SESSION['user_name']??'Admin') ?></div>
        <div class="sb-user-role">Administrator</div>
      </div>
    </div>
  </aside>

  <!-- MAIN -->
  <div class="adm-main">
    <header class="adm-topbar">
      <button class="tb-btn d-lg-none" id="sidebarToggle"><i class="bi bi-list"></i></button>
      <h5 class="adm-topbar-title mb-0"><?= e($title ?? 'Dashboard') ?></h5>
      <div class="ms-auto d-flex align-items-center gap-2">
        <a href="<?= APP_URL ?>/admin/orders/export" class="btn btn-sm btn-outline-success d-none d-md-flex align-items-center gap-1 fw-600">
          <i class="bi bi-download"></i> Export CSV
        </a>
        <span class="badge rounded-pill px-3 py-2" style="background:var(--brand-light);color:var(--brand);font-family:'Outfit',sans-serif">
          <i class="bi bi-shield-check me-1"></i>Admin
        </span>
      </div>
    </header>

    <!-- Flash -->
    <div class="px-4 pt-3">
      <?php foreach(['success','error','info'] as $t): $m=flash($t); if($m): $bt=($t==='error')?'danger':$t; ?>
        <div class="alert alert-<?= $bt ?> alert-dismissible d-flex align-items-center gap-2 fade show py-2">
          <i class="bi bi-<?= $t==='success'?'check-circle-fill':'exclamation-triangle-fill' ?>"></i>
          <div class="small"><?= e($m) ?></div>
          <button type="button" class="btn-close btn-sm ms-auto" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; endforeach; ?>
    </div>

    <div class="adm-content"><?= $content ?></div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const BASE_URL   = '<?= APP_URL ?>';
const CSRF_TOKEN = '<?= csrfToken() ?>';
document.getElementById('sidebarToggle')?.addEventListener('click',()=>{
  document.querySelector('.adm-sidebar')?.classList.toggle('open');
});
document.addEventListener('click',e=>{
  const sb=document.querySelector('.adm-sidebar'),tb=document.getElementById('sidebarToggle');
  if(sb?.classList.contains('open')&&!sb.contains(e.target)&&!tb?.contains(e.target)) sb.classList.remove('open');
});
document.querySelectorAll('[data-confirm]').forEach(el=>{
  el.addEventListener('click',e=>{if(!confirm(el.dataset.confirm||'Yakin?'))e.preventDefault();});
});
</script>
</body>
</html>

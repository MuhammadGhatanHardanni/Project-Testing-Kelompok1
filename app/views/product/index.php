<?php $isSearch = $currentSearch!=='' || $currentCategoryId>0; ?>

<?php if(!$isSearch): ?>
<!-- ── HERO ─────────────────────────────────────────────────── -->
<section class="hero">
  <div class="container">
    <div class="row g-4 align-items-center">
      <div class="col-lg-7">
        <div class="hero-content">
          <div class="hero-eyebrow"><i class="bi bi-lightning-charge-fill" style="color:#fbbf24"></i> Produk Segar Pilihan, Dikirim Hari Ini</div>
          <h1 class="hero-title">Belanja <span class="hl">Lebih Mudah</span>,<br>Hidup Lebih <span class="hl">Sehat</span></h1>
          <p class="hero-sub">Ribuan produk segar langsung dari sumber terpercaya — buah, sayur, daging, seafood, dan kebutuhan harian keluarga.</p>
          <div class="hero-search-wrap">
            <form action="<?= APP_URL ?>/" method="GET" class="d-flex w-100 align-items-center gap-2">
              <input type="text" name="search" placeholder='Cari "salmon", "apel fuji", "brokoli"…' class="form-control border-0 bg-transparent p-0">
              <button class="btn btn-primary flex-shrink-0" type="submit" style="border-radius:99px;padding:.55rem 1.3rem">
                <i class="bi bi-search me-1"></i>Cari
              </button>
            </form>
          </div>
          <div class="hero-stats">
            <div><div class="hero-stat-num">35+</div><div class="hero-stat-lbl">Kategori Produk</div></div>
            <div><div class="hero-stat-num">4.9 ★</div><div class="hero-stat-lbl">Rating Kepuasan</div></div>
            <div><div class="hero-stat-num">Gratis</div><div class="hero-stat-lbl">Ongkir Semua Order</div></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── CATEGORY GRID ────────────────────────────────────────── -->
<section class="sec" style="padding-top:2rem;padding-bottom:1rem">
<div class="container">
  <div class="sec-head">
    <div><div class="sec-title">Belanja per <span class="hl">Kategori</span></div><div class="sec-sub">Temukan produk segar yang kamu cari</div></div>
  </div>
  <div class="cat-grid stagger">
    <?php foreach(array_slice($categories,0,10) as $cat): ?>
    <a href="<?= APP_URL ?>/?category=<?= $cat['id'] ?>" class="cat-card">
      <?php if($cat['cover_image']): ?>
        <img src="<?= e($cat['cover_image']) ?>" alt="<?= e($cat['name']) ?>"
             onerror="this.src='https://images.unsplash.com/photo-1542838132-92c53300491e?w=400&q=80'">
      <?php else: ?>
        <div style="width:100%;height:100%;background:var(--brand-light);display:flex;align-items:center;justify-content:center">
          <i class="bi <?= e($cat['icon']??'bi-grid') ?>" style="font-size:2.5rem;color:var(--brand)"></i>
        </div>
      <?php endif; ?>
      <div class="cat-card-overlay"></div>
      <div class="cat-card-info">
        <div class="cat-card-name"><?= e($cat['name']) ?></div>
        <div class="cat-card-count"><?= $cat['product_count']??0 ?> produk</div>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
</div>
</section>

<?php if(!empty($discounted)): ?>
<!-- ── FLASH SALE ────────────────────────────────────────────── -->
<section class="sec" style="padding-top:1.5rem;padding-bottom:1rem">
<div class="container">
  <div class="row g-3 align-items-center mb-4">
    <div class="col">
      <div class="flash-banner">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
          <div>
            <div class="flash-title"><i class="bi bi-lightning-charge-fill me-2" style="color:#ffd700"></i>Flash Sale Hari Ini</div>
            <div class="text-white-50 small mt-1">Penawaran terbatas, segera ambil!</div>
          </div>
          <div class="flash-timer">
            <div class="timer-block" id="t-h">06</div><div class="timer-sep">:</div>
            <div class="timer-block" id="t-m">00</div><div class="timer-sep">:</div>
            <div class="timer-block" id="t-s">00</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row row-cols-2 row-cols-md-4 g-3 stagger">
    <?php $wishlistMap ??= []; foreach(array_slice($discounted,0,4) as $p): include __DIR__.'/_product_card.php'; endforeach; ?>
  </div>
</div>
</section>
<?php endif; ?>

<?php if(!empty($popular)): ?>
<!-- ── TERLARIS ──────────────────────────────────────────────── -->
<section class="sec" style="padding-top:1rem">
<div class="container">
  <div class="sec-head">
    <div><div class="sec-title">🔥 Produk <span class="hl">Terlaris</span></div><div class="sec-sub">Favorit pelanggan setia DailyMart</div></div>
    <a href="<?= APP_URL ?>/?sort=popular" class="sec-link">Lihat semua <i class="bi bi-arrow-right"></i></a>
  </div>
  <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3 stagger">
    <?php foreach($popular as $p): include __DIR__.'/_product_card.php'; endforeach; ?>
  </div>
</div>
</section>
<?php endif; ?>

<!-- ── PROMO BANNER ──────────────────────────────────────────── -->
<section class="sec" style="padding-top:0;padding-bottom:1rem">
<div class="container">
  <div class="row g-3">
    <div class="col-md-6">
      <div class="rounded-3 p-4 d-flex align-items-center gap-3" style="background:linear-gradient(135deg,#005c2e,#00b96b);min-height:120px">
        <div class="text-white">
          <div style="font-family:'Outfit',sans-serif;font-weight:900;font-size:1.25rem">Gratis Ongkir<br>Semua Pesanan</div>
          <div class="mt-1" style="color:rgba(255,255,255,.7);font-size:.85rem">Tidak ada minimum belanja!</div>
        </div>
        <i class="bi bi-truck ms-auto text-white" style="font-size:3rem;opacity:.3"></i>
      </div>
    </div>
    <div class="col-md-6">
      <div class="rounded-3 p-4 d-flex align-items-center gap-3" style="background:linear-gradient(135deg,#ff4757,#ff6b35);min-height:120px">
        <div class="text-white">
          <div style="font-family:'Outfit',sans-serif;font-weight:900;font-size:1.25rem">Voucher Member Baru</div>
          <div class="mt-1" style="color:rgba(255,255,255,.7);font-size:.85rem">Diskon 20% dengan kode <strong>NEWMEMBER</strong></div>
        </div>
        <i class="bi bi-ticket-perforated ms-auto text-white" style="font-size:3rem;opacity:.3"></i>
      </div>
    </div>
  </div>
</div>
</section>

<?php if(!empty($featured)): ?>
<!-- ── UNGGULAN ───────────────────────────────────────────────── -->
<section class="sec" style="padding-top:1rem">
<div class="container">
  <div class="sec-head">
    <div><div class="sec-title">⭐ Produk <span class="hl">Unggulan</span></div><div class="sec-sub">Pilihan terbaik dari tim kurasi DailyMart</div></div>
    <a href="<?= APP_URL ?>/" class="sec-link">Semua produk <i class="bi bi-arrow-right"></i></a>
  </div>
  <div class="row row-cols-2 row-cols-md-3 row-cols-lg-6 g-3 stagger">
    <?php foreach($featured as $p): include __DIR__.'/_product_card.php'; endforeach; ?>
  </div>
</div>
</section>
<?php endif; ?>

<?php endif; /* end !isSearch */ ?>

<!-- ── CATALOG ───────────────────────────────────────────────── -->
<section class="sec" style="padding-top:<?= $isSearch?'1.5rem':'0' ?>">
<div class="container">
  <?php if($isSearch): ?>
    <div class="page-banner rounded-3 mb-4 px-4 py-3" style="background:linear-gradient(135deg,var(--brand),var(--brand-d))">
      <h5 class="text-white fw-700 mb-0">
        <?= $currentSearch ? 'Hasil untuk "'.e($currentSearch).'"' : 'Kategori: '.e(array_column($categories,'name','id')[$currentCategoryId]??'') ?>
        <span class="badge bg-white text-success ms-2" style="font-size:.75rem"><?= count($products) ?> produk</span>
      </h5>
    </div>
  <?php endif; ?>

  <!-- Filters & Sort -->
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div class="cat-pill-bar">
      <a href="<?= APP_URL ?>/" class="cat-pill <?= !$currentCategoryId&&!$currentSearch?'active':'' ?>">
        <i class="bi bi-grid-3x3-gap"></i> Semua
      </a>
      <?php foreach($categories as $cat): ?>
        <a href="<?= APP_URL ?>/?category=<?= $cat['id'] ?>" class="cat-pill <?= $currentCategoryId==(int)$cat['id']?'active':'' ?>">
          <i class="bi <?= e($cat['icon']??'bi-circle') ?>"></i> <?= e($cat['name']) ?>
        </a>
      <?php endforeach; ?>
    </div>
    <form method="GET" action="<?= APP_URL ?>/" class="d-flex align-items-center gap-2 flex-shrink-0">
      <?php if($currentCategoryId): ?><input type="hidden" name="category" value="<?= $currentCategoryId ?>"><?php endif; ?>
      <?php if($currentSearch): ?><input type="hidden" name="search" value="<?= e($currentSearch) ?>"><?php endif; ?>
      <select name="sort" class="form-select form-select-sm w-auto" onchange="this.form.submit()" style="min-width:140px">
        <option value="newest"     <?= $currentSort==='newest'    ?'selected':''?>>Terbaru</option>
        <option value="popular"    <?= $currentSort==='popular'   ?'selected':''?>>Terlaris</option>
        <option value="price_asc"  <?= $currentSort==='price_asc' ?'selected':''?>>Harga ↑</option>
        <option value="price_desc" <?= $currentSort==='price_desc'?'selected':''?>>Harga ↓</option>
        <option value="rating"     <?= $currentSort==='rating'    ?'selected':''?>>Rating</option>
      </select>
      <?php if($isSearch): ?>
        <a href="<?= APP_URL ?>/" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x"></i> Reset</a>
      <?php endif; ?>
    </form>
  </div>

  <?php if(empty($products)): ?>
    <div class="empty-state">
      <i class="bi bi-search empty-icon"></i>
      <h5 class="fw-700 mt-0">Produk tidak ditemukan</h5>
      <p class="text-muted">Coba kata kunci lain atau pilih kategori berbeda.</p>
      <a href="<?= APP_URL ?>/" class="btn btn-primary mt-2 px-4">Lihat Semua Produk</a>
    </div>
  <?php else: ?>
    <div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-3">
      <?php foreach($products as $p): include __DIR__.'/_product_card.php'; endforeach; ?>
    </div>
  <?php endif; ?>
</div>
</section>

<script>
// Flash sale countdown
(function(){
  const end = new Date(); end.setHours(end.getHours()+6);
  function tick(){
    const s=Math.max(0,Math.floor((end-new Date())/1000));
    const h=String(Math.floor(s/3600)).padStart(2,'0');
    const m=String(Math.floor((s%3600)/60)).padStart(2,'0');
    const sc=String(s%60).padStart(2,'0');
    const th=document.getElementById('t-h');
    if(th){th.textContent=h;document.getElementById('t-m').textContent=m;document.getElementById('t-s').textContent=sc;}
  }
  tick(); setInterval(tick,1000);
})();
</script>

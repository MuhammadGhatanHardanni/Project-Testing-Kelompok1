<?php
$imgSrc = productImage($product['image'] ?? $product['image_url'] ?? null, $product['id']);
?>
<div class="container py-4">
  <!-- Breadcrumb -->
  <nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb small">
      <li class="breadcrumb-item"><a href="<?= APP_URL ?>/">Beranda</a></li>
      <?php if($product['category_name']): ?>
        <li class="breadcrumb-item"><a href="<?= APP_URL ?>/?category=<?= $product['category_id'] ?>"><?= e($product['category_name']) ?></a></li>
      <?php endif; ?>
      <li class="breadcrumb-item active text-truncate" style="max-width:220px"><?= e($product['name']) ?></li>
    </ol>
  </nav>

  <div class="row g-5">
    <!-- Image -->
    <div class="col-md-5">
      <div class="detail-img-main">
        <img src="<?= e($imgSrc) ?>" alt="<?= e($product['name']) ?>"
             onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1542838132-92c53300491e?w=600&q=80'">
        <?php if($product['stock']==0): ?>
          <div class="oos-overlay"><span>Stok Habis</span></div>
        <?php endif; ?>
      </div>
      <!-- Wishlist below -->
      <div class="mt-3">
        <?php if(isLoggedIn()): ?>
          <button class="btn btn-outline-danger w-100 fw-600 wish-btn-main <?= $isWished?'wishlisted':'' ?>"
                  data-pid="<?= $product['id'] ?>">
            <i class="bi bi-<?= $isWished?'heart-fill':'heart' ?> me-2"></i>
            <?= $isWished ? 'Tersimpan di Wishlist' : 'Tambah ke Wishlist' ?>
          </button>
        <?php else: ?>
          <a href="<?= APP_URL ?>/auth/login" class="btn btn-outline-secondary w-100">
            <i class="bi bi-heart me-2"></i>Masuk untuk Wishlist
          </a>
        <?php endif; ?>
      </div>
      <!-- Share / info strip -->
      <div class="d-flex gap-2 mt-2">
        <div class="flex-fill p-2 rounded-3 text-center" style="background:var(--bg-soft);font-size:.75rem">
          <i class="bi bi-shield-check text-success d-block mb-1" style="font-size:1.2rem"></i>
          <span class="text-muted">Garansi Segar</span>
        </div>
        <div class="flex-fill p-2 rounded-3 text-center" style="background:var(--bg-soft);font-size:.75rem">
          <i class="bi bi-truck text-primary d-block mb-1" style="font-size:1.2rem"></i>
          <span class="text-muted">Gratis Ongkir</span>
        </div>
        <div class="flex-fill p-2 rounded-3 text-center" style="background:var(--bg-soft);font-size:.75rem">
          <i class="bi bi-arrow-counterclockwise text-warning d-block mb-1" style="font-size:1.2rem"></i>
          <span class="text-muted">Retur Mudah</span>
        </div>
      </div>
    </div>

    <!-- Info -->
    <div class="col-md-7">
      <?php if($product['category_name']): ?>
        <a href="<?= APP_URL ?>/?category=<?= $product['category_id'] ?>" class="badge text-decoration-none mb-2" style="background:var(--brand-light);color:var(--brand);font-family:'Outfit',sans-serif"><?= e($product['category_name']) ?></a>
      <?php endif; ?>

      <h1 class="detail-name mb-2"><?= e($product['name']) ?></h1>

      <!-- Rating + stats -->
      <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
        <?php if($product['review_count']>0): ?>
          <?= starRating($product['avg_rating']) ?>
          <span class="text-muted small"><?= $product['review_count'] ?> ulasan</span>
          <span class="text-muted">·</span>
        <?php endif; ?>
        <?php if(!empty($product['sold_count'])): ?>
          <span class="text-muted small"><i class="bi bi-bag-check me-1 text-success"></i><?= number_format($product['sold_count']) ?> terjual</span>
        <?php endif; ?>
      </div>

      <!-- Price -->
      <div class="d-flex align-items-end gap-2 mb-1">
        <span class="detail-price"><?= formatRupiah($product['price']) ?></span>
        <span class="pb-1" style="font-family:'Outfit',sans-serif;font-weight:600;color:var(--text-soft);font-size:1rem">/ <?= e($product['unit']??'pcs') ?></span>
        <?php if($product['original_price']): ?>
          <span class="detail-oprice pb-1"><?= formatRupiah($product['original_price']) ?></span>
          <span class="badge bg-danger pb-1"><?= discountPercent($product['original_price'],$product['price']) ?>% OFF</span>
        <?php endif; ?>
      </div>

      <!-- Stock -->
      <div class="mb-4 d-flex align-items-center gap-2">
        <?php if($product['stock']>0): ?>
          <span class="stock-chip in"><i class="bi bi-check-circle-fill"></i><?= $product['stock'] ?> <?= e($product['unit']??'pcs') ?> tersedia</span>
          <?php if($product['stock']<=5): ?>
            <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle me-1"></i>Segera habis!</span>
          <?php endif; ?>
        <?php else: ?>
          <span class="stock-chip out"><i class="bi bi-x-circle-fill"></i>Stok habis</span>
        <?php endif; ?>
        <?php if($product['weight']): ?>
          <span class="text-muted small ms-1">· <?= formatWeight($product['weight']) ?></span>
        <?php endif; ?>
      </div>

      <!-- Add to cart -->
      <?php if($product['stock']>0): ?>
        <?php if(isLoggedIn()): ?>
          <form class="add-to-cart-form d-flex gap-3 flex-wrap align-items-center" action="<?= APP_URL ?>/cart/add" method="POST">
            <?= csrfField() ?>
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
            <div class="qty-wrap">
              <button type="button" class="qty-btn" data-delta="-1">−</button>
              <input type="number" name="quantity" class="qty-in" id="qtyIn" value="1" min="1" max="<?= $product['stock'] ?>">
              <button type="button" class="qty-btn" data-delta="1">+</button>
            </div>
            <button type="submit" class="btn btn-primary btn-lg flex-grow-1 fw-700 add-btn">
              <i class="bi bi-bag-plus me-2"></i>Tambah ke Keranjang
            </button>
          </form>
        <?php else: ?>
          <a href="<?= APP_URL ?>/auth/login" class="btn btn-primary btn-lg fw-700 w-100">
            <i class="bi bi-box-arrow-in-right me-2"></i>Masuk untuk Membeli
          </a>
        <?php endif; ?>
      <?php else: ?>
        <button class="btn btn-secondary btn-lg w-100" disabled>Stok Habis</button>
      <?php endif; ?>

      <!-- Meta -->
      <div class="mt-4 pt-3 border-top small text-muted d-flex gap-3 flex-wrap">
        <span><i class="bi bi-tag me-1"></i>SKU: DM-<?= str_pad($product['id'],4,'0',STR_PAD_LEFT) ?></span>
        <span><i class="bi bi-grid me-1"></i><?= e($product['category_name']??'-') ?></span>
      </div>
    </div>
  </div>

  <!-- TABS: Deskripsi & Ulasan -->
  <div class="mt-5" id="reviews">
    <ul class="nav d-tabs mb-4" style="border-bottom:2px solid var(--border)">
      <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-desc">Deskripsi</a></li>
      <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-rev">Ulasan (<?= count($reviews) ?>)</a></li>
    </ul>

    <div class="tab-content">
      <div class="tab-pane fade show active" id="tab-desc">
        <div class="p-4 rounded-3" style="background:var(--bg-soft)">
          <p class="mb-0"><?= $product['description'] ? nl2br(e($product['description'])) : '<span class="text-muted">Belum ada deskripsi.</span>' ?></p>
        </div>
      </div>

      <div class="tab-pane fade" id="tab-rev">
        <div class="row g-4">
          <!-- Rating summary -->
          <div class="col-md-4">
            <div class="text-center p-4 rounded-3" style="background:var(--bg-soft)">
              <div style="font-family:'Outfit',sans-serif;font-weight:900;font-size:3.5rem;color:var(--text);line-height:1"><?= number_format($product['avg_rating'],1) ?></div>
              <div class="mt-1 mb-2"><?= starRating($product['avg_rating'],false) ?></div>
              <div class="text-muted small"><?= count($reviews) ?> ulasan</div>
              <div class="mt-3 text-start">
                <?php for($i=5;$i>=1;$i--): $cnt=$ratingBreakdown[$i]??0; $pct=count($reviews)>0?round($cnt/count($reviews)*100):0; ?>
                <div class="rating-bar-row mb-2">
                  <span class="fw-600" style="width:18px"><?= $i ?></span>
                  <i class="bi bi-star-fill" style="color:var(--gold);font-size:.75rem"></i>
                  <div class="rating-bar"><div class="rating-bar-fill" style="width:<?= $pct ?>%"></div></div>
                  <span class="text-muted" style="width:20px;text-align:right"><?= $cnt ?></span>
                </div>
                <?php endfor; ?>
              </div>
            </div>
          </div>

          <!-- Reviews + form -->
          <div class="col-md-8">
            <?php if(isLoggedIn()):
              $db=$db??Database::getInstance();
              $s=$db->prepare("SELECT o.id FROM order_items oi JOIN orders o ON oi.order_id=o.id WHERE o.user_id=:u AND oi.product_id=:p AND o.status='completed' AND oi.is_reviewed=0 LIMIT 1");
              $s->execute([':u'=>$_SESSION['user_id'],':p'=>$product['id']]);
              $rvOrder=$s->fetch();
            ?>
            <?php if($rvOrder): ?>
            <div class="review-card mb-4" style="border:2px solid var(--brand)!important">
              <div class="fw-700 mb-3" style="font-family:'Outfit',sans-serif"><i class="bi bi-pencil-square me-2 text-success"></i>Tulis Ulasan Anda</div>
              <form action="<?= APP_URL ?>/product/review" method="POST">
                <?= csrfField() ?>
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <input type="hidden" name="order_id"   value="<?= $rvOrder['id'] ?>">
                <input type="hidden" name="rating"     id="ratingValue" value="0">
                <div class="mb-3">
                  <div class="star-rating-input d-flex gap-2 mb-1">
                    <?php for($i=1;$i<=5;$i++): ?>
                      <button type="button" class="star-input btn-link p-0 border-0 bg-transparent" data-val="<?= $i ?>" style="font-size:1.8rem;color:var(--gold)">
                        <i class="bi bi-star"></i>
                      </button>
                    <?php endfor; ?>
                  </div>
                  <small class="text-muted">Klik untuk memberi rating</small>
                </div>
                <textarea name="comment" class="form-control mb-3" rows="3" placeholder="Bagikan pengalaman Anda dengan produk ini…"></textarea>
                <button type="submit" class="btn btn-primary btn-sm fw-700 px-4">Kirim Ulasan</button>
              </form>
            </div>
            <?php endif; ?>
            <?php endif; ?>

            <?php if(empty($reviews)): ?>
              <div class="text-center text-muted py-5">
                <i class="bi bi-chat-square-text fs-2 d-block mb-2 text-muted"></i>
                Belum ada ulasan. Jadilah yang pertama!
              </div>
            <?php else: ?>
              <div class="d-flex flex-column gap-3">
                <?php foreach($reviews as $r): ?>
                <div class="review-card">
                  <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="d-flex align-items-center gap-2">
                      <div class="u-avatar" style="width:34px;height:34px;font-size:.85rem"><?= strtoupper(mb_substr($r['user_name'],0,1)) ?></div>
                      <span class="fw-600 small"><?= e($r['user_name']) ?></span>
                    </div>
                    <span class="tiny text-muted"><?= timeAgo($r['created_at']) ?></span>
                  </div>
                  <?= starRating($r['rating'],false) ?>
                  <?php if($r['comment']): ?>
                    <p class="mb-0 small text-muted mt-2"><?= e($r['comment']) ?></p>
                  <?php endif; ?>
                </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Related -->
  <?php if(!empty($related)): ?>
  <div class="mt-5 pt-2 border-top">
    <h5 class="fw-800 mb-4" style="font-family:'Outfit',sans-serif">Produk Serupa</h5>
    <div class="row row-cols-2 row-cols-md-4 g-3">
      <?php $wishlistMap=[]; foreach($related as $p): include __DIR__.'/_product_card.php'; endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
</div>

<script>
// Wishlist
document.querySelector('.wish-btn-main')?.addEventListener('click', async function(){
  const pid=this.dataset.pid;
  const res=await fetch(BASE_URL+'/wishlist/toggle',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`product_id=${pid}&csrf_token=${CSRF_TOKEN}`});
  const d=await res.json();
  if(d.action==='added'){this.classList.add('wishlisted');this.innerHTML='<i class="bi bi-heart-fill me-2"></i>Tersimpan di Wishlist';toast('Ditambahkan ke wishlist ❤️','success');}
  else{this.classList.remove('wishlisted');this.innerHTML='<i class="bi bi-heart me-2"></i>Tambah ke Wishlist';toast('Dihapus dari wishlist','info');}
});
</script>

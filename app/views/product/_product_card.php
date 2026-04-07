<?php
// views/product/_product_card.php
// Vars: $p (product row), $wishlistMap (array)
$inWishlist = $wishlistMap[$p['id']] ?? false;
$discount   = ($p['original_price'] && $p['original_price']>$p['price'])
              ? discountPercent($p['original_price'],$p['price']) : 0;
$imgSrc = productImage($p['image'] ?? $p['image_url'] ?? null, $p['id']);
?>
<div class="col fade-up">
  <div class="p-card">
    <div class="p-img-wrap">
      <a href="<?= APP_URL ?>/product/<?= $p['id'] ?>">
        <img src="<?= e($imgSrc) ?>" alt="<?= e($p['name']) ?>" class="p-img" loading="lazy"
             onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1542838132-92c53300491e?w=600&q=80'">
      </a>

      <!-- Badges -->
      <div class="p-badges">
        <?php if($discount>0): ?>
          <span class="p-badge sale">-<?= $discount ?>%</span>
        <?php elseif(!empty($p['is_featured'])): ?>
          <span class="p-badge new">Unggulan</span>
        <?php endif; ?>
        <?php if($p['stock']==0): ?>
          <span class="p-badge oos">Habis</span>
        <?php elseif($p['stock']<=5 && $p['stock']>0): ?>
          <span class="p-badge popular">Sisa <?= $p['stock'] ?></span>
        <?php endif; ?>
      </div>

      <!-- Wishlist -->
      <?php if(isLoggedIn()): ?>
        <button class="wish-btn <?= $inWishlist?'wishlisted':'' ?>" data-pid="<?= $p['id'] ?>" title="<?= $inWishlist?'Hapus dari':'Tambah ke' ?> wishlist">
          <i class="bi bi-<?= $inWishlist?'heart-fill':'heart' ?>"></i>
        </button>
      <?php else: ?>
        <a href="<?= APP_URL ?>/auth/login" class="wish-btn"><i class="bi bi-heart"></i></a>
      <?php endif; ?>
    </div>

    <div class="p-body">
      <?php if($p['category_name']): ?>
        <span class="p-cat-tag"><?= e($p['category_name']) ?></span>
      <?php endif; ?>

      <a href="<?= APP_URL ?>/product/<?= $p['id'] ?>" class="p-name truncate-2 d-block"><?= e($p['name']) ?></a>

      <?php if(isset($p['avg_rating']) && $p['review_count']>0): ?>
        <div class="p-rating-row">
          <?= starRating($p['avg_rating']) ?>
          <span class="text-muted" style="font-size:.72rem">(<?= $p['review_count'] ?>)</span>
        </div>
      <?php endif; ?>

      <div class="p-price-row">
        <span class="p-price"><?= formatRupiah($p['price']) ?></span>
        <?php if($p['original_price']): ?>
          <span class="p-oprice"><?= formatRupiah($p['original_price']) ?></span>
        <?php endif; ?>
        <span class="p-unit">/ <?= e($p['unit']??'pcs') ?></span>
      </div>

      <?php if(!empty($p['sold_count'])): ?>
        <div class="p-sold"><i class="bi bi-bag-check me-1"></i><?= number_format($p['sold_count']) ?> terjual</div>
      <?php endif; ?>

      <?php if($p['stock']>0): ?>
        <?php if(isLoggedIn()): ?>
          <form class="add-to-cart-form mt-1" action="<?= APP_URL ?>/cart/add" method="POST">
            <?= csrfField() ?>
            <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
            <input type="hidden" name="quantity" value="1">
            <button class="btn btn-primary btn-sm add-btn w-100">
              <i class="bi bi-bag-plus"></i> Tambah ke Keranjang
            </button>
          </form>
        <?php else: ?>
          <a href="<?= APP_URL ?>/auth/login" class="btn btn-outline-primary btn-sm add-btn mt-1 w-100">
            <i class="bi bi-bag-plus"></i> Tambah ke Keranjang
          </a>
        <?php endif; ?>
      <?php else: ?>
        <button class="btn btn-secondary btn-sm add-btn mt-1 w-100" disabled>Stok Habis</button>
      <?php endif; ?>
    </div>
  </div>
</div>

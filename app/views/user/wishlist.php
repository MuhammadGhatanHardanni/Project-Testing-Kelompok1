<div class="page-header"><div class="container"><h3><i class="bi bi-heart-fill me-2"></i>Wishlist Saya</h3></div></div>
<div class="container pb-5">
  <?php if(empty($items)): ?>
    <div class="empty-state">
      <i class="bi bi-heart empty-icon"></i>
      <h5 class="fw-700">Wishlist masih kosong</h5>
      <p class="text-muted">Temukan produk favorit dan simpan di sini.</p>
      <a href="<?= APP_URL ?>/" class="btn btn-primary mt-2"><i class="bi bi-search me-2"></i>Jelajahi Produk</a>
    </div>
  <?php else: ?>
    <p class="text-muted mb-4"><?= count($items) ?> produk tersimpan</p>
    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
      <?php foreach($items as $item): ?>
      <div class="col">
        <div class="wishlist-card">
          <div class="position-relative">
            <a href="<?= APP_URL ?>/product/<?= $item['product_id'] ?>">
              <img src="<?= productImage($item['image'], $item['product_id']) ?>"
                   class="wishlist-img"
                   onerror="this.src='https://picsum.photos/seed/wl<?= $item['product_id'] ?>/400/300'"
                   alt="<?= e($item['name']) ?>">
            </a>
            <?php if($item['stock'] == 0): ?>
              <span class="p-badge out" style="top:8px;left:8px;position:absolute">Habis</span>
            <?php endif; ?>
            <button class="wish-btn active" data-pid="<?= $item['product_id'] ?>"
                    style="position:absolute;top:8px;right:8px"
                    title="Hapus dari wishlist">
              <i class="bi bi-heart-fill"></i>
            </button>
          </div>
          <div class="p-3">
            <?php if($item['category_name']): ?>
              <div class="p-cat"><?= e($item['category_name']) ?></div>
            <?php endif; ?>
            <a href="<?= APP_URL ?>/product/<?= $item['product_id'] ?>" class="p-name d-block mb-1">
              <?= e($item['name']) ?>
            </a>
            <div class="d-flex align-items-center gap-2 mb-2">
              <span class="p-price"><?= formatRupiah($item['price']) ?></span>
              <?php if($item['original_price']): ?>
                <span class="p-oprice"><?= formatRupiah($item['original_price']) ?></span>
              <?php endif; ?>
            </div>
            <?php if($item['stock'] > 0 && isLoggedIn()): ?>
              <form class="add-to-cart-form" action="<?= APP_URL ?>/cart/add" method="POST">
                <?= csrfField() ?>
                <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                <input type="hidden" name="quantity" value="1">
                <button class="btn btn-primary btn-sm w-100 add-btn">
                  <i class="bi bi-cart-plus me-1"></i>Tambah ke Keranjang
                </button>
              </form>
            <?php else: ?>
              <button class="btn btn-secondary btn-sm w-100" disabled>Stok Habis</button>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

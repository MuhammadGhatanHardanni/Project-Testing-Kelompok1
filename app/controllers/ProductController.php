<?php
// app/controllers/ProductController.php

class ProductController extends Controller
{
    private ProductModel   $productModel;
    private CategoryModel  $categoryModel;
    private WishlistModel  $wishlistModel;

    public function __construct()
    {
        $this->productModel  = new ProductModel();
        $this->categoryModel = new CategoryModel();
        $this->wishlistModel = new WishlistModel();
    }

    public function index(): void
    {
        $search     = $this->query('search', '');
        $categoryId = (int) $this->query('category', 0);
        $sort       = $this->query('sort', 'newest');

        $products   = $this->productModel->getAll($search, $categoryId, $sort);
        $categories = $this->categoryModel->getAllWithCount();
        $featured   = $this->productModel->getFeatured(6);
        $discounted = $this->productModel->getDiscounted(4);
        $popular    = $this->productModel->getPopular(8);

        $wishlistMap = [];
        if (isLoggedIn()) {
            $wItems = $this->wishlistModel->getByUser($_SESSION['user_id']);
            foreach ($wItems as $w) $wishlistMap[$w['product_id']] = true;
        }

        $this->view('product.index', [
            'title'             => APP_NAME . ' — ' . APP_TAGLINE,
            'products'          => $products,
            'categories'        => $categories,
            'featured'          => $featured,
            'discounted'        => $discounted,
            'popular'           => $popular,
            'currentSearch'     => $search,
            'currentCategoryId' => $categoryId,
            'currentSort'       => $sort,
            'wishlistMap'       => $wishlistMap,
        ]);
    }

    public function detail(string $id): void
    {
        $product = $this->productModel->getById((int) $id);
        if (!$product) {
            http_response_code(404);
            $this->view('shared.404', ['title' => 'Produk Tidak Ditemukan']);
            return;
        }

        $reviews         = $this->productModel->getReviews($product['id']);
        $related         = $this->productModel->getRelated($product['id'], $product['category_id'] ?? 0);
        $isWished        = isLoggedIn() && $this->wishlistModel->isWishlisted($_SESSION['user_id'], $product['id']);
        $ratingBreakdown = array_fill(1, 5, 0);
        foreach ($reviews as $r) $ratingBreakdown[$r['rating']] = ($ratingBreakdown[$r['rating']] ?? 0) + 1;
        $wishlistMap = [];

        $this->view('product.detail', [
            'title'           => $product['name'] . ' — ' . APP_NAME,
            'product'         => $product,
            'reviews'         => $reviews,
            'related'         => $related,
            'isWished'        => $isWished,
            'ratingBreakdown' => $ratingBreakdown,
            'wishlistMap'     => $wishlistMap,
        ]);
    }

    public function submitReview(): void
    {
        $this->requireAuth();
        $productId = (int) $this->input('product_id', 0);
        $orderId   = (int) $this->input('order_id', 0);
        $rating    = (int) $this->input('rating', 0);
        $comment   = $this->input('comment', '');

        if ($rating < 1 || $rating > 5) {
            $this->flash('error', 'Pilih rating 1-5 bintang.');
            $this->redirect('/product/' . $productId);
        }

        if (!$this->productModel->userCanReview($_SESSION['user_id'], $productId, $orderId)) {
            $this->flash('error', 'Anda tidak dapat memberikan ulasan untuk produk ini.');
            $this->redirect('/product/' . $productId);
        }

        $this->productModel->addReview($productId, $_SESSION['user_id'], $orderId, $rating, $comment);
        $this->flash('success', 'Ulasan berhasil dikirim! Terima kasih.');
        $this->redirect('/product/' . $productId . '#reviews');
    }
}

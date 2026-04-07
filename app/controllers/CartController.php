<?php
// app/controllers/CartController.php

class CartController extends Controller
{
    private CartModel    $cartModel;
    private ProductModel $productModel;

    public function __construct()
    {
        $this->cartModel    = new CartModel();
        $this->productModel = new ProductModel();
    }

    public function index(): void
    {
        $this->requireAuth();
        $userId = $_SESSION['user_id'];
        $items  = $this->cartModel->getByUser($userId);
        $total  = $this->cartModel->getTotal($userId);

        $this->view('cart.index', [
            'title' => 'Keranjang Belanja',
            'items' => $items,
            'total' => $total,
        ]);
    }

    public function add(): void
    {
        $this->requireAuth();
        $userId    = $_SESSION['user_id'];
        $productId = (int) $this->input('product_id', 0);
        $quantity  = max(1, (int) $this->input('quantity', 1));

        $product = $this->productModel->getById($productId);
        if (!$product || $product['stock'] < 1) {
            $this->flash('error', 'Produk tidak tersedia.');
            $referer = $_SERVER['HTTP_REFERER'] ?? APP_URL . '/';
            header("Location: {$referer}"); exit;
        }

        $this->cartModel->addItem($userId, $productId, $quantity);
        $_SESSION['cart_count'] = $this->cartModel->countItems($userId);

        // AJAX response
        if (($this->query('ajax') || ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest')) {
            $this->json(['success'=>true,'count'=>$_SESSION['cart_count'],'message'=>$product['name'].' ditambahkan.']);
        }

        $this->flash('success', $product['name'] . ' ditambahkan ke keranjang.');
        $referer = $_SERVER['HTTP_REFERER'] ?? APP_URL . '/';
        header("Location: {$referer}"); exit;
    }

    public function update(): void
    {
        $this->requireAuth();
        $userId   = $_SESSION['user_id'];
        $cartId   = (int) $this->input('cart_id', 0);
        $quantity = (int) $this->input('quantity', 1);

        if ($quantity <= 0) {
            $this->cartModel->removeItem($cartId, $userId);
        } else {
            $this->cartModel->updateQuantity($cartId, $userId, $quantity);
        }

        $_SESSION['cart_count'] = $this->cartModel->countItems($userId);
        $this->redirect('/cart');
    }

    public function remove(): void
    {
        $this->requireAuth();
        $userId = $_SESSION['user_id'];
        $cartId = (int) $this->input('cart_id', 0);

        $this->cartModel->removeItem($cartId, $userId);
        $_SESSION['cart_count'] = $this->cartModel->countItems($userId);
        $this->flash('success', 'Produk dihapus dari keranjang.');
        $this->redirect('/cart');
    }
}

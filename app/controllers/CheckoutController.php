<?php
// app/controllers/CheckoutController.php

class CheckoutController extends Controller
{
    private CartModel    $cartModel;
    private OrderModel   $orderModel;
    private ProductModel $productModel;
    private VoucherModel $voucherModel;
    private UserModel    $userModel;

    public function __construct()
    {
        $this->cartModel    = new CartModel();
        $this->orderModel   = new OrderModel();
        $this->productModel = new ProductModel();
        $this->voucherModel = new VoucherModel();
        $this->userModel    = new UserModel();
    }

    public function index(): void
    {
        $this->requireAuth();
        $userId = $_SESSION['user_id'];
        $items  = $this->cartModel->getByUser($userId);

        if (empty($items)) {
            $this->flash('error', 'Keranjang belanja Anda kosong.');
            $this->redirect('/cart');
        }

        $subtotal  = $this->cartModel->getTotal($userId);
        $addresses = $this->userModel->getAddresses($userId);
        $primary   = $this->userModel->getPrimaryAddress($userId);

        $this->view('cart.checkout', [
            'title'     => 'Checkout',
            'items'     => $items,
            'subtotal'  => $subtotal,
            'addresses' => $addresses,
            'primary'   => $primary,
        ]);
    }

    // ── AJAX: apply voucher ───────────────────────────────────────
    public function applyVoucher(): void
    {
        $this->requireAuth();
        header('Content-Type: application/json');

        $code     = $this->input('code', '');
        $subtotal = (float) $this->input('subtotal', 0);

        $result = $this->voucherModel->validate($code, $subtotal);

        if (!$result['valid']) {
            echo json_encode(['success' => false, 'message' => $result['message']]);
            exit;
        }

        $_SESSION['applied_voucher'] = $result['voucher'];
        echo json_encode([
            'success'     => true,
            'message'     => $result['message'],
            'discount'    => $result['discount'],
            'discount_fmt'=> formatRupiah($result['discount']),
            'total_fmt'   => formatRupiah(max(0, $subtotal - $result['discount'])),
        ]);
        exit;
    }

    // ── AJAX: remove voucher ──────────────────────────────────────
    public function removeVoucher(): void
    {
        $this->requireAuth();
        unset($_SESSION['applied_voucher']);
        $subtotal = $this->cartModel->getTotal($_SESSION['user_id']);
        $this->json(['success'=>true,'total_fmt'=>formatRupiah($subtotal)]);
    }

    public function process(): void
    {
        $this->requireAuth();
        $userId = $_SESSION['user_id'];

        if (!verifyCsrf()) {
            $this->flash('error', 'Request tidak valid.');
            $this->redirect('/checkout');
        }

        $recipientName = $this->input('recipient_name', '');
        $phone         = $this->input('phone', '');
        $address       = $this->input('address', '');
        $city          = $this->input('city', '');
        $notes         = $this->input('notes', '');

        $errors = [];
        if (empty($recipientName)) $errors[] = 'Nama penerima wajib diisi.';
        if (empty($address))       $errors[] = 'Alamat wajib diisi.';
        if (empty($phone))         $errors[] = 'Nomor telepon wajib diisi.';
        if (!empty($phone) && !isValidPhone($phone)) $errors[] = 'Format nomor telepon tidak valid.';

        if (!empty($errors)) {
            $_SESSION['checkout_errors'] = $errors;
            $this->redirect('/checkout');
        }

        $cartItems = $this->cartModel->getByUser($userId);
        if (empty($cartItems)) {
            $this->flash('error', 'Keranjang belanja kosong.');
            $this->redirect('/cart');
        }

        // Stock check
        $stockErrors = [];
        foreach ($cartItems as $item) {
            if (!$this->productModel->hasStock($item['product_id'], $item['quantity'])) {
                $stockErrors[] = "Stok '{$item['name']}' tidak mencukupi.";
            }
        }
        if (!empty($stockErrors)) {
            $_SESSION['checkout_errors'] = $stockErrors;
            $this->redirect('/checkout');
        }

        $voucher = $_SESSION['applied_voucher'] ?? null;

        $shippingData = [
            'recipient_name' => $recipientName,
            'phone'          => $phone,
            'address'        => $address,
            'city'           => $city,
            'notes'          => $notes,
        ];

        $orderId = $this->orderModel->createFromCart($userId, $shippingData, $cartItems, $voucher);

        if (!$orderId) {
            $this->flash('error', 'Gagal memproses pesanan. Silakan coba lagi.');
            $this->redirect('/checkout');
        }

        $this->cartModel->clearCart($userId);
        $_SESSION['cart_count']      = 0;
        $_SESSION['last_order_id']   = $orderId;
        unset($_SESSION['applied_voucher']);

        // Refresh notif count
        $notifModel = new NotificationModel();
        $_SESSION['notif_count'] = $notifModel->countUnread($userId);

        $this->redirect('/checkout/success');
    }

    public function success(): void
    {
        $this->requireAuth();
        $orderId = $_SESSION['last_order_id'] ?? null;
        $order   = $orderId ? $this->orderModel->getDetailById($orderId) : null;

        $this->view('cart.success', [
            'title'   => 'Pesanan Berhasil!',
            'order'   => $order,
            'orderId' => $orderId,
        ]);
    }

    public function myOrders(): void
    {
        $this->requireAuth();
        $userId = $_SESSION['user_id'];
        $status = $this->query('status', '');
        $orders = $this->orderModel->getByUser($userId);

        $this->view('cart.orders', [
            'title'  => 'Pesanan Saya',
            'orders' => $orders,
            'status' => $status,
        ]);
    }

    public function orderDetail(string $id): void
    {
        $this->requireAuth();
        $order = $this->orderModel->getDetailById((int)$id);

        if (!$order || $order['user_id'] != $_SESSION['user_id']) {
            $this->flash('error', 'Pesanan tidak ditemukan.');
            $this->redirect('/orders');
        }

        $this->view('cart.order_detail', [
            'title' => 'Detail Pesanan',
            'order' => $order,
        ]);
    }
}

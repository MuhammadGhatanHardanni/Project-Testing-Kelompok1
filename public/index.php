<?php
// public/index.php — DailyMart v1.0 Front Controller

define('ENTRY', true);
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/core/Database.php';
require_once dirname(__DIR__) . '/core/Model.php';
require_once dirname(__DIR__) . '/core/Controller.php';
require_once dirname(__DIR__) . '/core/Router.php';
require_once dirname(__DIR__) . '/core/helpers.php';

spl_autoload_register(function(string $class): void {
    foreach([
        dirname(__DIR__).'/app/models/'.$class.'.php',
        dirname(__DIR__).'/app/controllers/'.$class.'.php',
    ] as $p){ if(file_exists($p)){require_once $p;return;} }
});

session_name(SESSION_NAME);
session_start();

// Sync session counts per request
if(isLoggedIn() && !isset($_SESSION['_counts_synced'])){
    $_SESSION['_counts_synced'] = true;
    if(!isset($_SESSION['cart_count'])){
        $cm = new CartModel();
        $_SESSION['cart_count'] = $cm->countItems($_SESSION['user_id']);
    }
    if(!isset($_SESSION['notif_count'])){
        $nm = new NotificationModel();
        $_SESSION['notif_count'] = $nm->countUnread($_SESSION['user_id']);
    }
    if(!isset($_SESSION['wishlist_count'])){
        $wm = new WishlistModel();
        $_SESSION['wishlist_count'] = $wm->countByUser($_SESSION['user_id']);
    }
}
// Reset sync flag each request so counts refresh
unset($_SESSION['_counts_synced']);

$r = new Router();

// AUTH
$r->get('/auth/login',     fn()=>( new AuthController())->showLogin());
$r->post('/auth/login',    fn()=>( new AuthController())->login());
$r->get('/auth/register',  fn()=>( new AuthController())->showRegister());
$r->post('/auth/register', fn()=>( new AuthController())->register());
$r->get('/auth/logout',    fn()=>( new AuthController())->logout());

// PRODUCTS
$r->get('/',                   fn()=>( new ProductController())->index());
$r->get('/product/{id}',       fn($id)=>(new ProductController())->detail($id));
$r->post('/product/review',    fn()=>( new ProductController())->submitReview());

// CART
$r->get('/cart',           fn()=>( new CartController())->index());
$r->post('/cart/add',      fn()=>( new CartController())->add());
$r->post('/cart/update',   fn()=>( new CartController())->update());
$r->post('/cart/remove',   fn()=>( new CartController())->remove());

// CHECKOUT
$r->get('/checkout',                 fn()=>( new CheckoutController())->index());
$r->post('/checkout/process',        fn()=>( new CheckoutController())->process());
$r->post('/checkout/apply-voucher',  fn()=>( new CheckoutController())->applyVoucher());
$r->post('/checkout/remove-voucher', fn()=>( new CheckoutController())->removeVoucher());
$r->get('/checkout/success',         fn()=>( new CheckoutController())->success());
$r->get('/orders',                   fn()=>( new CheckoutController())->myOrders());
$r->get('/orders/{id}',              fn($id)=>(new CheckoutController())->orderDetail($id));

// USER
$r->get('/profile',                fn()=>( new UserController())->profile());
$r->post('/profile/update',        fn()=>( new UserController())->updateProfile());
$r->post('/profile/password',      fn()=>( new UserController())->updatePassword());
$r->post('/profile/avatar',        fn()=>( new UserController())->updateAvatar());
$r->get('/wishlist',               fn()=>( new UserController())->wishlist());
$r->post('/wishlist/toggle',       fn()=>( new UserController())->toggleWishlist());
$r->get('/notifications',          fn()=>( new UserController())->notifications());
$r->post('/addresses/add',         fn()=>( new UserController())->addAddress());
$r->post('/addresses/{id}/delete', fn($id)=>(new UserController())->deleteAddress($id));

// ADMIN
$r->get('/admin',                         fn()=>( new AdminController())->dashboard());
$r->get('/admin/products',                fn()=>( new AdminController())->products());
$r->get('/admin/products/create',         fn()=>( new AdminController())->createProduct());
$r->post('/admin/products/store',         fn()=>( new AdminController())->storeProduct());
$r->get('/admin/products/{id}/edit',      fn($id)=>(new AdminController())->editProduct($id));
$r->post('/admin/products/{id}/update',   fn($id)=>(new AdminController())->updateProduct($id));
$r->post('/admin/products/{id}/delete',   fn($id)=>(new AdminController())->deleteProduct($id));
$r->get('/admin/categories',              fn()=>( new AdminController())->categories());
$r->post('/admin/categories/store',       fn()=>( new AdminController())->storeCategory());
$r->post('/admin/categories/{id}/update', fn($id)=>(new AdminController())->updateCategory($id));
$r->post('/admin/categories/{id}/delete', fn($id)=>(new AdminController())->deleteCategory($id));
$r->get('/admin/orders',                  fn()=>( new AdminController())->orders());
$r->get('/admin/orders/export',           fn()=>( new AdminController())->exportOrders());
$r->get('/admin/orders/{id}',             fn($id)=>(new AdminController())->orderDetail($id));
$r->post('/admin/orders/{id}/status',     fn($id)=>(new AdminController())->updateOrderStatus($id));
$r->get('/admin/users',                   fn()=>( new AdminController())->users());
$r->post('/admin/users/{id}/toggle',      fn($id)=>(new AdminController())->toggleUser($id));
$r->get('/admin/vouchers',                fn()=>( new AdminController())->vouchers());
$r->post('/admin/vouchers/store',         fn()=>( new AdminController())->storeVoucher());
$r->post('/admin/vouchers/{id}/update',   fn($id)=>(new AdminController())->updateVoucher($id));
$r->post('/admin/vouchers/{id}/delete',   fn($id)=>(new AdminController())->deleteVoucher($id));
$r->get('/admin/stock',                   fn()=>( new AdminController())->stockReport());

$r->dispatch();

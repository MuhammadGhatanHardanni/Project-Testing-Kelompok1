<?php
// app/controllers/AdminController.php

class AdminController extends Controller
{
    private ProductModel  $productModel;
    private CategoryModel $categoryModel;
    private OrderModel    $orderModel;
    private UserModel     $userModel;
    private VoucherModel  $voucherModel;

    public function __construct()
    {
        $this->productModel  = new ProductModel();
        $this->categoryModel = new CategoryModel();
        $this->orderModel    = new OrderModel();
        $this->userModel     = new UserModel();
        $this->voucherModel  = new VoucherModel();
    }

    // ── DASHBOARD ────────────────────────────────────────────────
    public function dashboard(): void
    {
        $this->requireAdmin();

        $statusCounts  = $this->orderModel->getStatusCounts();
        $monthlyStats  = $this->orderModel->getMonthlyStats();
        $bestSellers   = $this->productModel->getBestSellers(5);
        $lowStock      = $this->productModel->getLowStock(10);
        $recentOrders  = $this->orderModel->getRecentOrders(6);

        $this->view('admin.dashboard', [
            'title'        => 'Dashboard',
            'productCount' => $this->productModel->count(),
            'orderCount'   => $this->orderModel->countAll(),
            'userCount'    => $this->userModel->count(),
            'revenue'      => $this->orderModel->getTotalRevenue(),
            'statusCounts' => $statusCounts,
            'monthlyStats' => array_reverse($monthlyStats),
            'bestSellers'  => $bestSellers,
            'lowStock'     => $lowStock,
            'recentOrders' => $recentOrders,
        ], 'admin');
    }

    // ── PRODUCTS ─────────────────────────────────────────────────
    public function products(): void
    {
        $this->requireAdmin();
        $search = $this->query('search', '');
        $cat    = (int)$this->query('category', 0);
        $products   = $this->productModel->getAll($search, $cat);
        $categories = $this->categoryModel->getAll();

        $this->view('admin.products', [
            'title'      => 'Kelola Produk',
            'products'   => $products,
            'categories' => $categories,
            'search'     => $search,
            'catFilter'  => $cat,
        ], 'admin');
    }

    public function createProduct(): void
    {
        $this->requireAdmin();
        $this->view('admin.product_form', [
            'title'      => 'Tambah Produk',
            'product'    => null,
            'categories' => $this->categoryModel->getAll(),
            'formAction' => APP_URL . '/admin/products/store',
        ], 'admin');
    }

    public function storeProduct(): void
    {
        $this->requireAdmin();
        $data = $this->getProductFormData();
        if (empty($data['name'])) { $this->flash('error','Nama produk wajib diisi.'); $this->redirect('/admin/products/create'); }

        if (!empty($_FILES['image']['name'])) {
            $up = $this->handleImageUpload($_FILES['image']);
            if ($up['success']) $data['image'] = $up['filename'];
            else { $this->flash('error',$up['message']); $this->redirect('/admin/products/create'); }
        }

        $this->productModel->create($data);
        $this->flash('success', 'Produk berhasil ditambahkan.');
        $this->redirect('/admin/products');
    }

    public function editProduct(string $id): void
    {
        $this->requireAdmin();
        $product = $this->productModel->getById((int)$id);
        if (!$product) { $this->flash('error','Produk tidak ditemukan.'); $this->redirect('/admin/products'); }

        $this->view('admin.product_form', [
            'title'      => 'Edit Produk',
            'product'    => $product,
            'categories' => $this->categoryModel->getAll(),
            'formAction' => APP_URL . '/admin/products/' . $id . '/update',
        ], 'admin');
    }

    public function updateProduct(string $id): void
    {
        $this->requireAdmin();
        $productId = (int)$id;
        $product   = $this->productModel->getById($productId);
        if (!$product) { $this->flash('error','Produk tidak ditemukan.'); $this->redirect('/admin/products'); }

        $data          = $this->getProductFormData();
        $data['image'] = $product['image'];

        if (!empty($_FILES['image']['name'])) {
            $up = $this->handleImageUpload($_FILES['image']);
            if ($up['success']) {
                if ($product['image'] && file_exists(UPLOAD_PATH . '/' . $product['image'])) unlink(UPLOAD_PATH . '/' . $product['image']);
                $data['image'] = $up['filename'];
            }
        }

        $this->productModel->update($productId, $data);
        $this->flash('success', 'Produk berhasil diperbarui.');
        $this->redirect('/admin/products');
    }

    public function deleteProduct(string $id): void
    {
        $this->requireAdmin();
        $product = $this->productModel->getById((int)$id);
        if ($product) {
            if ($product['image'] && file_exists(UPLOAD_PATH . '/' . $product['image'])) unlink(UPLOAD_PATH . '/' . $product['image']);
            $this->productModel->delete((int)$id);
            $this->flash('success', 'Produk berhasil dihapus.');
        }
        $this->redirect('/admin/products');
    }

    // ── CATEGORIES ────────────────────────────────────────────────
    public function categories(): void
    {
        $this->requireAdmin();
        $this->view('admin.categories', [
            'title'      => 'Kelola Kategori',
            'categories' => $this->categoryModel->getAllWithCount(),
        ], 'admin');
    }

    public function storeCategory(): void
    {
        $this->requireAdmin();
        $data = [
            'name'        => $this->input('name',''),
            'icon'        => $this->input('icon','bi-grid'),
            'description' => $this->input('description',''),
            'sort_order'  => (int)$this->input('sort_order',0),
        ];
        if (empty($data['name'])) { $this->flash('error','Nama kategori wajib diisi.'); }
        else { $this->categoryModel->create($data); $this->flash('success','Kategori berhasil ditambahkan.'); }
        $this->redirect('/admin/categories');
    }

    public function updateCategory(string $id): void
    {
        $this->requireAdmin();
        $data = [
            'name'        => $this->input('name',''),
            'icon'        => $this->input('icon','bi-grid'),
            'description' => $this->input('description',''),
            'sort_order'  => (int)$this->input('sort_order',0),
        ];
        $this->categoryModel->update((int)$id, $data);
        $this->flash('success','Kategori diperbarui.');
        $this->redirect('/admin/categories');
    }

    public function deleteCategory(string $id): void
    {
        $this->requireAdmin();
        $this->categoryModel->delete((int)$id);
        $this->flash('success','Kategori dihapus.');
        $this->redirect('/admin/categories');
    }

    // ── ORDERS ────────────────────────────────────────────────────
    public function orders(): void
    {
        $this->requireAdmin();
        $status = $this->query('status','');
        $search = $this->query('search','');
        $orders = $this->orderModel->getAllWithUser($status, $search);

        $this->view('admin.orders', [
            'title'   => 'Daftar Pesanan',
            'orders'  => $orders,
            'status'  => $status,
            'search'  => $search,
        ], 'admin');
    }

    public function orderDetail(string $id): void
    {
        $this->requireAdmin();
        $order = $this->orderModel->getDetailById((int)$id);
        if (!$order) { $this->flash('error','Pesanan tidak ditemukan.'); $this->redirect('/admin/orders'); }

        $this->view('admin.order_detail', [
            'title' => 'Detail Pesanan #' . generateOrderNumber((int)$id),
            'order' => $order,
        ], 'admin');
    }

    public function updateOrderStatus(string $id): void
    {
        $this->requireAdmin();
        $this->orderModel->updateStatus((int)$id, $this->input('status',''));
        $this->flash('success', 'Status pesanan diperbarui.');
        $this->redirect('/admin/orders/' . $id);
    }

    public function exportOrders(): void
    {
        $this->requireAdmin();
        $orders = $this->orderModel->getAllWithUser();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="pesanan-' . date('Ymd-His') . '.csv"');
        header('Pragma: no-cache');

        $fp = fopen('php://output', 'w');
        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM for Excel

        fputcsv($fp, ['No. Pesanan','Tanggal','Pelanggan','Email','Penerima','Kota','Telepon','Subtotal','Diskon','Total','Status','Metode Bayar']);

        foreach ($orders as $o) {
            fputcsv($fp, [
                generateOrderNumber($o['id']),
                date('d/m/Y H:i', strtotime($o['created_at'])),
                $o['user_name'], $o['user_email'],
                $o['recipient_name'], $o['city'] ?? '-', $o['phone'],
                $o['subtotal'], $o['discount_amount'], $o['total_price'],
                $o['status'], $o['payment_method'],
            ]);
        }
        fclose($fp);
        exit;
    }

    // ── USERS ─────────────────────────────────────────────────────
    public function users(): void
    {
        $this->requireAdmin();
        $this->view('admin.users', [
            'title' => 'Manajemen User',
            'users' => $this->userModel->getAllUsers(),
        ], 'admin');
    }

    public function toggleUser(string $id): void
    {
        $this->requireAdmin();
        if ((int)$id === $_SESSION['user_id']) {
            $this->flash('error', 'Tidak bisa menonaktifkan akun sendiri.');
        } else {
            $this->userModel->toggleActive((int)$id);
            $this->flash('success', 'Status user diperbarui.');
        }
        $this->redirect('/admin/users');
    }

    // ── VOUCHERS ──────────────────────────────────────────────────
    public function vouchers(): void
    {
        $this->requireAdmin();
        $this->view('admin.vouchers', [
            'title'    => 'Kelola Voucher',
            'vouchers' => $this->voucherModel->getAll(),
        ], 'admin');
    }

    public function storeVoucher(): void
    {
        $this->requireAdmin();
        $data = [
            'code'          => $this->input('code',''),
            'type'          => $this->input('type','fixed'),
            'value'         => (float)$this->input('value',0),
            'min_purchase'  => (float)$this->input('min_purchase',0),
            'max_discount'  => $this->input('max_discount') ?: null,
            'quota'         => (int)$this->input('quota',1),
            'valid_from'    => $this->input('valid_from',''),
            'valid_until'   => $this->input('valid_until',''),
            'description'   => $this->input('description',''),
        ];
        if (empty($data['code'])) { $this->flash('error','Kode voucher wajib diisi.'); $this->redirect('/admin/vouchers'); }
        try {
            $this->voucherModel->create($data);
            $this->flash('success','Voucher berhasil dibuat.');
        } catch (PDOException $e) {
            $this->flash('error','Kode voucher sudah digunakan.');
        }
        $this->redirect('/admin/vouchers');
    }

    public function updateVoucher(string $id): void
    {
        $this->requireAdmin();
        $data = [
            'code'          => $this->input('code',''),
            'type'          => $this->input('type','fixed'),
            'value'         => (float)$this->input('value',0),
            'min_purchase'  => (float)$this->input('min_purchase',0),
            'max_discount'  => $this->input('max_discount') ?: null,
            'quota'         => (int)$this->input('quota',1),
            'valid_from'    => $this->input('valid_from',''),
            'valid_until'   => $this->input('valid_until',''),
            'description'   => $this->input('description',''),
            'is_active'     => (int)$this->input('is_active',1),
        ];
        $this->voucherModel->update((int)$id, $data);
        $this->flash('success','Voucher diperbarui.');
        $this->redirect('/admin/vouchers');
    }

    public function deleteVoucher(string $id): void
    {
        $this->requireAdmin();
        $this->voucherModel->delete((int)$id);
        $this->flash('success','Voucher dihapus.');
        $this->redirect('/admin/vouchers');
    }

    // ── STOCK REPORT ─────────────────────────────────────────────
    public function stockReport(): void
    {
        $this->requireAdmin();
        $products = $this->productModel->getAll('', 0, 'newest');

        $this->view('admin.stock_report', [
            'title'    => 'Laporan Stok',
            'products' => $products,
            'lowStock' => $this->productModel->getLowStock(10),
        ], 'admin');
    }

    // ── PRIVATE HELPERS ───────────────────────────────────────────
    private function getProductFormData(): array
    {
        return [
            'category_id'    => (int)$this->input('category_id',0),
            'name'           => $this->input('name',''),
            'description'    => $this->input('description',''),
            'price'          => (float)$this->input('price',0),
            'original_price' => $this->input('original_price') ?: null,
            'stock'          => (int)$this->input('stock',0),
            'unit'           => $this->input('unit','pcs'),
            'weight'         => (int)$this->input('weight',0),
            'is_featured'    => (int)$this->input('is_featured',0),
            'is_active'      => (int)$this->input('is_active',1),
            'image'          => null,
        ];
    }

    private function handleImageUpload(array $file): array
    {
        $allowed = ['image/jpeg','image/png','image/webp','image/gif'];
        if (!in_array($file['type'], $allowed)) return ['success'=>false,'message'=>'Format gambar tidak didukung.'];
        if ($file['size'] > 2*1024*1024) return ['success'=>false,'message'=>'Ukuran gambar maksimal 2MB.'];

        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('prod_',true) . '.' . strtolower($ext);
        $dest     = UPLOAD_PATH . '/' . $filename;

        if (!is_dir(UPLOAD_PATH)) mkdir(UPLOAD_PATH, 0755, true);
        if (move_uploaded_file($file['tmp_name'], $dest)) return ['success'=>true,'filename'=>$filename];
        return ['success'=>false,'message'=>'Gagal mengupload gambar.'];
    }
}

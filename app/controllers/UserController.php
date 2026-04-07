<?php
// app/controllers/UserController.php

class UserController extends Controller
{
    private UserModel $userModel;
    private WishlistModel $wishlistModel;
    private NotificationModel $notifModel;
    private OrderModel $orderModel;

    public function __construct()
    {
        $this->userModel     = new UserModel();
        $this->wishlistModel = new WishlistModel();
        $this->notifModel    = new NotificationModel();
        $this->orderModel    = new OrderModel();
    }

    // ── GET /profile ──────────────────────────────────────────────
    public function profile(): void
    {
        $this->requireAuth();
        $user      = $this->userModel->find($_SESSION['user_id']);
        $addresses = $this->userModel->getAddresses($_SESSION['user_id']);
        $orders    = $this->orderModel->getByUser($_SESSION['user_id']);

        $this->view('user.profile', [
            'title'     => 'Profil Saya',
            'user'      => $user,
            'addresses' => $addresses,
            'orders'    => $orders,
            'tab'       => $this->query('tab', 'profile'),
        ]);
    }

    // ── POST /profile/update ──────────────────────────────────────
    public function updateProfile(): void
    {
        $this->requireAuth();
        $uid = $_SESSION['user_id'];

        $name  = $this->input('name', '');
        $phone = $this->input('phone', '');

        if (empty($name)) {
            $this->flash('error', 'Nama tidak boleh kosong.');
            $this->redirect('/profile?tab=profile');
        }

        $this->userModel->updateProfile($uid, ['name'=>$name,'phone'=>$phone]);
        $_SESSION['user_name'] = $name;
        $this->flash('success', 'Profil berhasil diperbarui.');
        $this->redirect('/profile?tab=profile');
    }

    // ── POST /profile/password ────────────────────────────────────
    public function updatePassword(): void
    {
        $this->requireAuth();
        $uid     = $_SESSION['user_id'];
        $current = $this->input('current_password', '');
        $new     = $this->input('new_password', '');
        $confirm = $this->input('confirm_password', '');

        $user = $this->userModel->find($uid);
        if (!$this->userModel->verifyPassword($current, $user['password'])) {
            $this->flash('error', 'Password saat ini tidak sesuai.');
            $this->redirect('/profile?tab=security');
        }
        if (strlen($new) < 6) {
            $this->flash('error', 'Password baru minimal 6 karakter.');
            $this->redirect('/profile?tab=security');
        }
        if ($new !== $confirm) {
            $this->flash('error', 'Konfirmasi password tidak cocok.');
            $this->redirect('/profile?tab=security');
        }

        $this->userModel->updatePassword($uid, $new);
        $this->flash('success', 'Password berhasil diubah.');
        $this->redirect('/profile?tab=security');
    }

    // ── POST /profile/avatar ──────────────────────────────────────
    public function updateAvatar(): void
    {
        $this->requireAuth();
        if (empty($_FILES['avatar']['name'])) {
            $this->flash('error', 'Pilih file gambar terlebih dahulu.');
            $this->redirect('/profile?tab=profile');
        }

        $file      = $_FILES['avatar'];
        $allowed   = ['image/jpeg','image/png','image/webp'];
        $maxSize   = 1 * 1024 * 1024; // 1MB

        if (!in_array($file['type'], $allowed)) {
            $this->flash('error', 'Format gambar tidak didukung.');
            $this->redirect('/profile?tab=profile');
        }
        if ($file['size'] > $maxSize) {
            $this->flash('error', 'Ukuran gambar maksimal 1MB.');
            $this->redirect('/profile?tab=profile');
        }

        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'avatar_' . $_SESSION['user_id'] . '_' . time() . '.' . strtolower($ext);
        $dest     = UPLOAD_PATH . '/avatars/' . $filename;

        if (!is_dir(UPLOAD_PATH . '/avatars')) mkdir(UPLOAD_PATH . '/avatars', 0755, true);

        if (move_uploaded_file($file['tmp_name'], $dest)) {
            $this->userModel->updateAvatar($_SESSION['user_id'], $filename);
            $this->flash('success', 'Foto profil berhasil diperbarui.');
        } else {
            $this->flash('error', 'Gagal mengupload gambar.');
        }
        $this->redirect('/profile?tab=profile');
    }

    // ── GET /wishlist ─────────────────────────────────────────────
    public function wishlist(): void
    {
        $this->requireAuth();
        $items = $this->wishlistModel->getByUser($_SESSION['user_id']);
        $this->view('user.wishlist', ['title'=>'Wishlist Saya','items'=>$items]);
    }

    // ── POST /wishlist/toggle ─────────────────────────────────────
    public function toggleWishlist(): void
    {
        $this->requireAuth();
        $productId = (int) $this->input('product_id', 0);
        $action    = $this->wishlistModel->toggle($_SESSION['user_id'], $productId);

        if ($this->isAjax()) {
            $this->json(['action'=>$action,'count'=>$this->wishlistModel->countByUser($_SESSION['user_id'])]);
        }

        $msg = $action === 'added' ? 'Ditambahkan ke wishlist.' : 'Dihapus dari wishlist.';
        $this->flash('success', $msg);
        $referer = $_SERVER['HTTP_REFERER'] ?? APP_URL . '/';
        header("Location: {$referer}"); exit;
    }

    // ── GET /notifications ────────────────────────────────────────
    public function notifications(): void
    {
        $this->requireAuth();
        $uid = $_SESSION['user_id'];
        $this->notifModel->markAllRead($uid);
        $notifs = $this->notifModel->getByUser($uid);
        $_SESSION['notif_count'] = 0;

        $this->view('user.notifications', ['title'=>'Notifikasi','notifications'=>$notifs]);
    }

    // ── POST /addresses/add ───────────────────────────────────────
    public function addAddress(): void
    {
        $this->requireAuth();
        $data = [
            'label'       => $this->input('label','Rumah'),
            'recipient'   => $this->input('recipient',''),
            'phone'       => $this->input('phone',''),
            'address'     => $this->input('address',''),
            'city'        => $this->input('city',''),
            'province'    => $this->input('province',''),
            'postal_code' => $this->input('postal_code',''),
            'is_primary'  => $this->input('is_primary',0),
        ];

        if (empty($data['recipient']) || empty($data['address']) || empty($data['city'])) {
            $this->flash('error', 'Lengkapi semua field alamat.');
        } else {
            $this->userModel->addAddress($_SESSION['user_id'], $data);
            $this->flash('success', 'Alamat berhasil ditambahkan.');
        }
        $this->redirect('/profile?tab=addresses');
    }

    // ── POST /addresses/{id}/delete ───────────────────────────────
    public function deleteAddress(string $id): void
    {
        $this->requireAuth();
        $this->userModel->deleteAddress((int)$id, $_SESSION['user_id']);
        $this->flash('success', 'Alamat berhasil dihapus.');
        $this->redirect('/profile?tab=addresses');
    }

    private function isAjax(): bool
    {
        return ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';
    }
}

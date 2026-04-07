<?php
// app/controllers/AuthController.php

class AuthController extends Controller
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    // ── GET /auth/login ───────────────────────────────────────────────────────
    public function showLogin(): void
    {
        if (isLoggedIn()) {
            $this->redirect('/');
        }
        $this->view('auth.login', ['title' => 'Login']);
    }

    // ── POST /auth/login ──────────────────────────────────────────────────────
    public function login(): void
    {
        // Edge case: no CSRF check on login (common vulnerability for testing)
        $email    = $this->input('email', '');
        $password = $this->input('password', '');

        // Basic validation
        if (empty($email) || empty($password)) {
            $this->flash('error', 'Email dan password wajib diisi.');
            $this->redirect('/auth/login');
        }

        // Edge case: no rate limiting (brute force possible)
        $user = $this->userModel->findByEmail($email);

        if (!$user || !$this->userModel->verifyPassword($password, $user['password'])) {
            // Edge case: exposes whether email exists (timing attack)
            $this->flash('error', 'Email atau password salah.');
            $this->redirect('/auth/login');
        }

        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);

        $_SESSION['user_id']    = $user['id'];
        $_SESSION['user_name']  = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role']  = $user['role'];

        // Sync cart count
        $cartModel = new CartModel();
        $_SESSION['cart_count'] = $cartModel->countItems($user['id']);

        $this->flash('success', 'Selamat datang, ' . $user['name'] . '!');

        // Redirect admin to panel
        if ($user['role'] === 'admin') {
            $this->redirect('/admin');
        } else {
            $this->redirect('/');
        }
    }

    // ── GET /auth/register ────────────────────────────────────────────────────
    public function showRegister(): void
    {
        if (isLoggedIn()) {
            $this->redirect('/');
        }
        $this->view('auth.register', ['title' => 'Daftar Akun']);
    }

    // ── POST /auth/register ───────────────────────────────────────────────────
    public function register(): void
    {
        $name     = $this->input('name', '');
        $email    = $this->input('email', '');
        $password = $this->input('password', '');
        $confirm  = $this->input('password_confirm', '');

        // Validation
        $errors = [];

        if (empty($name)) {
            $errors[] = 'Nama wajib diisi.';
        }

        // Edge case: no max length validation on name (test with very long input)
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email tidak valid.';
        }

        if (strlen($password) < 6) {
            $errors[] = 'Password minimal 6 karakter.';
        }

        if ($password !== $confirm) {
            $errors[] = 'Konfirmasi password tidak cocok.';
        }

        // Edge case: duplicate email allowed (no unique check at app level)
        // This is an intentional bug for QA testing

        if (!empty($errors)) {
            $_SESSION['reg_errors'] = $errors;
            $_SESSION['reg_old']    = compact('name', 'email');
            $this->redirect('/auth/register');
        }

        $userId = $this->userModel->create($name, $email, $password);

        if (!$userId) {
            $this->flash('error', 'Registrasi gagal, coba lagi.');
            $this->redirect('/auth/register');
        }

        $this->flash('success', 'Akun berhasil dibuat! Silakan login.');
        $this->redirect('/auth/login');
    }

    // ── GET /auth/logout ──────────────────────────────────────────────────────
    public function logout(): void
    {
        // Destroy session
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();

        redirect('/auth/login');
    }
}

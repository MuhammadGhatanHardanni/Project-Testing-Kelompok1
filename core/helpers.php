<?php
// core/helpers.php — DailyMart v3.0

function formatRupiah(float $amount): string {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}
function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}
function csrfField(): string {
    return '<input type="hidden" name="csrf_token" value="' . csrfToken() . '">';
}
function verifyCsrf(): bool {
    if (empty($_POST['csrf_token'])) return false;
    return hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token']);
}
function flash(string $type): ?string {
    $key = 'flash_' . $type; $msg = $_SESSION[$key] ?? null; unset($_SESSION[$key]); return $msg;
}
function isLoggedIn(): bool  { return !empty($_SESSION['user_id']); }
function isAdmin(): bool     { return ($_SESSION['user_role'] ?? '') === 'admin'; }

function productImage(?string $imageUrl, int $productId = 0): string {
    // Use stored URL directly (Unsplash URLs)
    if ($imageUrl && (str_starts_with($imageUrl, 'http') || str_starts_with($imageUrl, '//'))) {
        return $imageUrl;
    }
    // Uploaded file
    if ($imageUrl && file_exists(UPLOAD_PATH . '/' . $imageUrl)) {
        return UPLOAD_URL . '/' . $imageUrl;
    }
    // Fallback — generic fresh food image
    $seeds = ['apple','vegetable','meat','seafood','milk','drink','food','snack','spice','egg'];
    $seed  = $seeds[($productId - 1) % count($seeds)] ?? 'food';
    return "https://images.unsplash.com/photo-1542838132-92c53300491e?w=600&q=80";
}

function avatarUrl(?string $avatar): string {
    if ($avatar && file_exists(UPLOAD_PATH . '/avatars/' . $avatar)) return UPLOAD_URL . '/avatars/' . $avatar;
    return '';
}
function truncate(string $str, int $length = 80): string {
    return mb_strlen($str) > $length ? mb_substr($str, 0, $length) . '...' : $str;
}
function cartCount(): int     { return $_SESSION['cart_count']     ?? 0; }
function notifCount(): int    { return $_SESSION['notif_count']    ?? 0; }
function wishlistCount(): int { return $_SESSION['wishlist_count'] ?? 0; }

function redirect(string $path): void {
    $url = (str_starts_with($path, 'http')) ? $path : APP_URL . $path;
    header("Location: {$url}"); exit;
}
function isValidPhone(string $phone): bool {
    return (bool) preg_match('/^(\+62|62|0)[0-9]{9,12}$/', $phone);
}
function generateOrderNumber(int $orderId): string {
    return 'DM-' . date('Ymd') . '-' . str_pad($orderId, 5, '0', STR_PAD_LEFT);
}
function discountPercent(float $original, float $current): int {
    if ($original <= 0) return 0;
    return (int) round((($original - $current) / $original) * 100);
}
function starRating(float $rating, bool $showNum = true): string {
    $html = '<span class="star-rating">';
    for ($i = 1; $i <= 5; $i++) {
        if ($rating >= $i) $html .= '<i class="bi bi-star-fill"></i>';
        elseif ($rating >= $i - 0.5) $html .= '<i class="bi bi-star-half"></i>';
        else $html .= '<i class="bi bi-star"></i>';
    }
    if ($showNum && $rating > 0) $html .= ' <span class="rating-num">' . number_format($rating, 1) . '</span>';
    $html .= '</span>';
    return $html;
}
function timeAgo(string $datetime): string {
    $diff = time() - strtotime($datetime);
    if ($diff < 60)     return 'baru saja';
    if ($diff < 3600)   return (int)($diff/60)    . ' mnt lalu';
    if ($diff < 86400)  return (int)($diff/3600)  . ' jam lalu';
    if ($diff < 604800) return (int)($diff/86400) . ' hari lalu';
    return date('d M Y', strtotime($datetime));
}
function currentUrl(): string { return $_SERVER['REQUEST_URI'] ?? '/'; }
function formatWeight(int $grams): string {
    return $grams >= 1000 ? number_format($grams/1000, 1) . ' kg' : $grams . ' g';
}

# 🛒 DailyMart — E-Commerce Platform

> Modern grocery e-commerce built with PHP MVC + MySQL + Bootstrap 5

---

## ⚙️ Installation

### Requirements
- PHP 8.1+ (PDO MySQL, mbstring, fileinfo)
- MySQL 5.7+ / MariaDB 10.3+
- Apache with mod_rewrite (XAMPP / Laragon)

### Steps

**1. Place folder in web root**
```
htdocs/dailymart/
```

**2. Create database & import**
- Open phpMyAdmin
- Create database: `dailymart`
- Select it → SQL tab → paste `database.sql` → Go
- Expected: `✓ DailyMart ready! 35 products imported.`

**3. Configure**
Edit `config/config.php`:
```php
define('DB_NAME', 'dailymart');   // your DB name
define('DB_PASS', '');            // your MySQL password
define('APP_URL', 'http://localhost/dailymart/public');
```

**4. Create upload folders**
```
public/uploads/products/
public/uploads/avatars/
```

**5. Access**
```
http://localhost/dailymart/public/
```

---

## 👤 Demo Accounts

| Role  | Email                   | Password    |
|-------|-------------------------|-------------|
| Admin | admin@dailymart.id      | password123 |
| User  | budi@example.com        | password123 |
| User  | sari@example.com        | password123 |
| User  | riko@example.com        | password123 |

---

## 🗺️ URL Map

| Method | URL                              | Feature               |
|--------|----------------------------------|-----------------------|
| GET    | `/`                              | Homepage + Catalog    |
| GET    | `/product/{id}`                  | Product Detail        |
| GET/POST | `/auth/login`                  | Login                 |
| GET/POST | `/auth/register`               | Register              |
| GET    | `/cart`                          | Shopping Cart         |
| POST   | `/cart/add`                      | Add to Cart (AJAX)    |
| GET    | `/checkout`                      | Checkout              |
| POST   | `/checkout/apply-voucher`        | Apply Voucher (AJAX)  |
| GET    | `/orders`                        | My Orders             |
| GET    | `/profile`                       | User Profile          |
| GET    | `/wishlist`                      | Wishlist              |
| POST   | `/wishlist/toggle`               | Toggle Wishlist (AJAX)|
| GET    | `/notifications`                 | Notifications         |
| GET    | `/admin`                         | Admin Dashboard       |
| GET    | `/admin/products`                | Manage Products       |
| GET    | `/admin/categories`              | Manage Categories     |
| GET    | `/admin/orders`                  | Manage Orders         |
| GET    | `/admin/orders/export`           | Export CSV            |
| GET    | `/admin/users`                   | Manage Users          |
| GET    | `/admin/vouchers`                | Manage Vouchers       |
| GET    | `/admin/stock`                   | Stock Report          |

---

## 🎟️ Voucher Codes (for testing)

| Code       | Type       | Value  | Min. Purchase |
|------------|------------|--------|---------------|
| DAILY10    | Persentase | 10%    | Rp 50.000     |
| HEMAT20K   | Nominal    | Rp 20K | Rp 100.000    |
| NEWMEMBER  | Persentase | 20%    | Rp 30.000     |
| GRATIS5K   | Nominal    | Rp 5K  | Rp 25.000     |
| WEEKEND15  | Persentase | 15%    | Rp 75.000     |
| EXPIRED99  | Persentase | 99%    | Rp 1.000      |

---
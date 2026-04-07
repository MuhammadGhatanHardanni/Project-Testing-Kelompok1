-- ============================================================
-- DailyMart v3.0 - Database Schema
-- Compatible: MySQL 5.7+ / MariaDB 10.3+
--
-- CARA IMPORT:
--   phpMyAdmin → pilih/buat database 'dailymart' → tab SQL → paste → Go
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS wishlist;
DROP TABLE IF EXISTS product_reviews;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS cart;
DROP TABLE IF EXISTS vouchers;
DROP TABLE IF EXISTS user_addresses;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- USERS
-- ============================================================
CREATE TABLE users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    email       VARCHAR(150) NOT NULL,
    phone       VARCHAR(20)  DEFAULT NULL,
    avatar      VARCHAR(255) DEFAULT NULL,
    password    VARCHAR(255) NOT NULL,
    role        ENUM('user','admin') NOT NULL DEFAULT 'user',
    is_active   TINYINT(1)   NOT NULL DEFAULT 1,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB;

-- ============================================================
-- USER ADDRESSES
-- ============================================================
CREATE TABLE user_addresses (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    label       VARCHAR(50)  NOT NULL DEFAULT 'Rumah',
    recipient   VARCHAR(100) NOT NULL,
    phone       VARCHAR(20)  NOT NULL,
    address     TEXT         NOT NULL,
    city        VARCHAR(100) NOT NULL,
    province    VARCHAR(100) NOT NULL,
    postal_code VARCHAR(10)  NOT NULL,
    is_primary  TINYINT(1)   NOT NULL DEFAULT 0,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- CATEGORIES
-- ============================================================
CREATE TABLE categories (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    slug        VARCHAR(100) NOT NULL UNIQUE,
    icon        VARCHAR(50)  DEFAULT 'bi-grid',
    cover_image VARCHAR(500) DEFAULT NULL,
    description TEXT,
    sort_order  INT          DEFAULT 0
) ENGINE=InnoDB;

-- ============================================================
-- PRODUCTS
-- ============================================================
CREATE TABLE products (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    category_id     INT,
    name            VARCHAR(200)  NOT NULL,
    description     TEXT,
    price           DECIMAL(12,2) NOT NULL,
    original_price  DECIMAL(12,2) DEFAULT NULL,
    stock           INT           NOT NULL DEFAULT 0,
    unit            VARCHAR(30)   DEFAULT 'pcs',
    image_url       VARCHAR(500)  DEFAULT NULL,
    is_featured     TINYINT(1)    DEFAULT 0,
    is_active       TINYINT(1)    DEFAULT 1,
    weight          INT           DEFAULT 0,
    sold_count      INT           DEFAULT 0,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- PRODUCT REVIEWS
-- ============================================================
CREATE TABLE product_reviews (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    product_id  INT NOT NULL,
    user_id     INT NOT NULL,
    order_id    INT NOT NULL,
    rating      TINYINT NOT NULL,
    comment     TEXT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE,
    UNIQUE KEY unique_review (product_id, user_id, order_id)
) ENGINE=InnoDB;

-- ============================================================
-- WISHLIST
-- ============================================================
CREATE TABLE wishlist (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    product_id  INT NOT NULL,
    added_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_wishlist (user_id, product_id)
) ENGINE=InnoDB;

-- ============================================================
-- VOUCHERS
-- ============================================================
CREATE TABLE vouchers (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    code            VARCHAR(50)   NOT NULL UNIQUE,
    type            ENUM('percentage','fixed') NOT NULL DEFAULT 'fixed',
    value           DECIMAL(12,2) NOT NULL,
    min_purchase    DECIMAL(12,2) NOT NULL DEFAULT 0,
    max_discount    DECIMAL(12,2) DEFAULT NULL,
    quota           INT           NOT NULL DEFAULT 1,
    used_count      INT           NOT NULL DEFAULT 0,
    valid_from      DATE          NOT NULL,
    valid_until     DATE          NOT NULL,
    is_active       TINYINT(1)    NOT NULL DEFAULT 1,
    description     VARCHAR(255)  DEFAULT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- ORDERS
-- ============================================================
CREATE TABLE orders (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    user_id         INT           NOT NULL,
    voucher_id      INT           DEFAULT NULL,
    recipient_name  VARCHAR(100)  NOT NULL,
    address         TEXT          NOT NULL,
    city            VARCHAR(100)  DEFAULT NULL,
    phone           VARCHAR(20)   NOT NULL,
    subtotal        DECIMAL(12,2) NOT NULL DEFAULT 0,
    discount_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    shipping_cost   DECIMAL(12,2) NOT NULL DEFAULT 0,
    total_price     DECIMAL(12,2) NOT NULL,
    status          ENUM('pending','processing','shipped','completed','cancelled') NOT NULL DEFAULT 'processing',
    payment_method  VARCHAR(50)   DEFAULT 'COD',
    tracking_number VARCHAR(100)  DEFAULT NULL,
    notes           TEXT,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE,
    FOREIGN KEY (voucher_id) REFERENCES vouchers(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- ORDER ITEMS
-- ============================================================
CREATE TABLE order_items (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    order_id        INT           NOT NULL,
    product_id      INT,
    product_name    VARCHAR(200)  NOT NULL,
    product_image   VARCHAR(500)  DEFAULT NULL,
    price           DECIMAL(12,2) NOT NULL,
    quantity        INT           NOT NULL,
    subtotal        DECIMAL(12,2) NOT NULL,
    is_reviewed     TINYINT(1)    DEFAULT 0,
    FOREIGN KEY (order_id)   REFERENCES orders(id)   ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- CART
-- ============================================================
CREATE TABLE cart (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    product_id  INT NOT NULL,
    quantity    INT NOT NULL DEFAULT 1,
    added_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_cart_item (user_id, product_id)
) ENGINE=InnoDB;

-- ============================================================
-- NOTIFICATIONS
-- ============================================================
CREATE TABLE notifications (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT          NOT NULL,
    type        VARCHAR(50)  NOT NULL DEFAULT 'info',
    title       VARCHAR(150) NOT NULL,
    message     TEXT         NOT NULL,
    link        VARCHAR(255) DEFAULT NULL,
    is_read     TINYINT(1)   NOT NULL DEFAULT 0,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- SEED DATA
-- ============================================================

-- Users (password: password123)
INSERT INTO users (name, email, phone, password, role) VALUES
('Admin DailyMart', 'admin@dailymart.id',  '081234560001', '$2y$12$8oHaK0nTA0TSaIHUjCUQKu2mX2ky7TaSEmw3frnH60fH6IJReWJ1a', 'admin'),
('Budi Santoso',    'budi@example.com',    '081234560002', '$2y$12$Ssd1kY3FeR0cavodR4f8GuIZeqHQfDZPd51ewlCQbuoKOAl6YsRnK', 'user'),
('Sari Dewi',       'sari@example.com',    '081234560003', '$2y$12$uXay.ttJammjW1ebWNOb8OCvUFxM063FjF8qmEAXMTB4vq/P5yQDi', 'user'),
('Riko Pratama',    'riko@example.com',    '081234560004', '$2y$12$uXay.ttJammjW1ebWNOb8OCvUFxM063FjF8qmEAXMTB4vq/P5yQDi', 'user');

-- Addresses
INSERT INTO user_addresses (user_id, label, recipient, phone, address, city, province, postal_code, is_primary) VALUES
(2, 'Rumah',  'Budi Santoso', '081234560002', 'Jl. Mawar No. 12, RT 03 RW 07, Condongcatur', 'Sleman',     'DI Yogyakarta', '55283', 1),
(2, 'Kantor', 'Budi Santoso', '081234560002', 'Jl. Solo KM 7, Gedung Sunrise Lt. 4',          'Sleman',     'DI Yogyakarta', '55198', 0),
(3, 'Rumah',  'Sari Dewi',    '081234560003', 'Jl. Parangtritis No. 88, Sewon',               'Bantul',     'DI Yogyakarta', '55187', 1),
(4, 'Rumah',  'Riko Pratama', '081234560004', 'Jl. Kaliurang KM 12, Ngaglik',                 'Sleman',     'DI Yogyakarta', '55584', 1);

-- Categories with cover images
INSERT INTO categories (name, slug, icon, sort_order, cover_image) VALUES
('Buah Segar',       'buah-segar',    'bi-apple',          1, 'https://images.unsplash.com/photo-1610832958506-aa56368176cf?w=400&q=80'),
('Sayuran',          'sayuran',       'bi-leaf',           2, 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=400&q=80'),
('Daging & Ayam',    'daging-ayam',   'bi-droplet-fill',   3, 'https://images.unsplash.com/photo-1603048297172-c92544798d5a?w=400&q=80'),
('Ikan & Seafood',   'ikan-seafood',  'bi-water',          4, 'https://images.unsplash.com/photo-1534482421-64566f976cfa?w=400&q=80'),
('Susu & Dairy',     'susu-dairy',    'bi-cup-straw',      5, 'https://images.unsplash.com/photo-1550583724-b2692b85b150?w=400&q=80'),
('Minuman',          'minuman',       'bi-cup',            6, 'https://images.unsplash.com/photo-1544145945-f90425340c7e?w=400&q=80'),
('Makanan Instan',   'makanan-instan','bi-fire',           7, 'https://images.unsplash.com/photo-1612929633738-8fe44f7ec841?w=400&q=80'),
('Snack & Cemilan',  'snack-cemilan', 'bi-bag-heart',      8, 'https://images.unsplash.com/photo-1621939514649-280e2ee25f60?w=400&q=80'),
('Bumbu & Rempah',   'bumbu-rempah',  'bi-stars',          9, 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=400&q=80'),
('Telur & Tahu',     'telur-tahu',    'bi-egg',           10, 'https://images.unsplash.com/photo-1587486913049-53fc88980cfc?w=400&q=80');

-- Products with matching Unsplash images
INSERT INTO products (category_id, name, description, price, original_price, stock, unit, image_url, is_featured, sold_count) VALUES

-- Buah Segar (cat 1)
(1, 'Apel Fuji Premium 1kg',
 'Apel Fuji impor langsung dari dataran tinggi, tekstur renyah dan rasa manis segar. Kaya vitamin C, antioksidan, dan serat. Cocok untuk snack sehat atau jus.',
 29000, 35000, 45, 'kg',
 'https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6?w=600&q=85', 1, 312),

(1, 'Jeruk Mandarin 1kg',
 'Jeruk mandarin manis tanpa biji, mudah dikupas. Kaya vitamin C untuk daya tahan tubuh. Dipilih dari kebun jeruk terbaik.',
 22000, NULL, 80, 'kg',
 'https://images.unsplash.com/photo-1611080626919-7cf5a9dbab12?w=600&q=85', 0, 198),

(1, 'Anggur Merah Seedless 500g',
 'Anggur merah tanpa biji, manis dan segar. Kaya antioksidan resveratrol yang baik untuk jantung. Impor langsung, kualitas premium.',
 45000, 55000, 30, '500g',
 'https://images.unsplash.com/photo-1537640538966-79f369143f8f?w=600&q=85', 1, 145),

(1, 'Pisang Cavendish 1 sisir',
 'Pisang cavendish segar matang sempurna, manis alami tanpa pemanis. Sumber energi instan dan kaya kalium untuk kesehatan jantung.',
 18000, NULL, 60, 'sisir',
 'https://images.unsplash.com/photo-1571771894821-ce9b6c11b08e?w=600&q=85', 0, 267),

(1, 'Mangga Harum Manis 1kg',
 'Mangga harum manis pilihan, daging tebal kuning cerah, aroma harum khas. Dipanen saat matang optimal dari kebun mitra.',
 35000, 40000, 25, 'kg',
 'https://images.unsplash.com/photo-1553279768-865429fa0078?w=600&q=85', 1, 189),

(1, 'Stroberi Segar 250g',
 'Stroberi merah segar, manis-asam menyegarkan. Kaya vitamin C dan antioksidan. Dipetik fresh setiap pagi dari kebun Lembang.',
 28000, 33000, 35, '250g',
 'https://images.unsplash.com/photo-1464965911861-746a04b4bca6?w=600&q=85', 0, 156),

-- Sayuran (cat 2)
(2, 'Bayam Organik 250g',
 'Bayam segar organik bebas pestisida, dipetik pagi hari langsung dari kebun mitra. Kaya zat besi, kalsium, dan vitamin K untuk tulang sehat.',
 7000, NULL, 100, 'ikat',
 'https://images.unsplash.com/photo-1576045057995-568f588f82fb?w=600&q=85', 0, 445),

(2, 'Brokoli Hijau Segar 500g',
 'Brokoli segar grade A, batang pendek krop padat. Kaya sulforafan, vitamin C, dan serat. Cocok untuk tumis, sup, atau dikonsumsi mentah.',
 15000, 18000, 60, '500g',
 'https://images.unsplash.com/photo-1459411621453-7b03977f4bfc?w=600&q=85', 0, 201),

(2, 'Tomat Cherry 250g',
 'Tomat cherry manis, warna merah cerah menarik. Sempurna untuk salad, pasta, atau snack sehat. Kaya likopen antioksidan.',
 12000, NULL, 90, '250g',
 'https://images.unsplash.com/photo-1467321733819-17c1b7213cb0?w=600&q=85', 0, 178),

(2, 'Wortel Baby Organik 500g',
 'Wortel baby organik segar, rasa lebih manis dari wortel biasa. Tanpa pestisida, cocok untuk MPASI bayi dan smoothie.',
 14000, 17000, 75, '500g',
 'https://images.unsplash.com/photo-1598170845058-32b9d6a5da37?w=600&q=85', 0, 167),

(2, 'Kol Ungu Organik 500g',
 'Kol ungu organik kaya antosianin untuk imunitas tubuh. Renyah, cocok untuk coleslaw, tumis, atau salad segar.',
 11000, NULL, 55, '500g',
 'https://images.unsplash.com/photo-1551754655-cd27e38d2076?w=600&q=85', 0, 89),

(2, 'Paprika Merah 3 pcs',
 'Paprika merah manis grade A, daging tebal renyah. Kaya vitamin C dan antioksidan. Sangat baik untuk tumisan dan salad.',
 18000, 22000, 40, '3 pcs',
 'https://images.unsplash.com/photo-1563565375-f3fdfdbefa83?w=600&q=85', 0, 134),

-- Daging & Ayam (cat 3)
(3, 'Ayam Broiler Segar 1kg',
 'Ayam broiler segar tanpa pengawet, dipotong bersih dan higienis. Daging empuk cocok untuk semua olahan. Dari peternak bersertifikat.',
 36000, NULL, 40, 'kg',
 'https://images.unsplash.com/photo-1604503468506-a8da13d82791?w=600&q=85', 1, 523),

(3, 'Daging Sapi Segar Has Dalam 500g',
 'Daging sapi has dalam pilihan grade A, warna merah segar. Tekstur lembut cocok untuk steak, rendang, dan soto. Dipotong fresh setiap hari.',
 68000, NULL, 25, '500g',
 'https://images.unsplash.com/photo-1607623814075-e51df1bdc82f?w=600&q=85', 1, 287),

(3, 'Daging Giling Sapi 500g',
 'Daging sapi giling segar, lemak proporsional untuk rasa optimal. Sempurna untuk bakso, burger, bolognese, dan perkedel.',
 55000, 62000, 30, '500g',
 'https://images.unsplash.com/photo-1602470520998-f4a52199a3d6?w=600&q=85', 0, 198),

-- Ikan & Seafood (cat 4)
(4, 'Ikan Salmon Fillet 300g',
 'Salmon Atlantik premium grade A, kaya asam lemak omega-3. Diimpor langsung dan disimpan -18°C. Cocok untuk sashimi, teriyaki, dan panggang.',
 88000, 99000, 15, '300g',
 'https://images.unsplash.com/photo-1574781330855-d0db8cc6a79c?w=600&q=85', 1, 312),

(4, 'Udang Vannamei Segar 500g',
 'Udang vannamei segar ukuran 40/50, beku cepat tanpa pengawet kimia. Tekstur kenyal dan rasa manis alami. Cocok untuk tumis, saus padang, dan bakar.',
 48000, 55000, 28, '500g',
 'https://images.unsplash.com/photo-1559737558-2f5a35f4523b?w=600&q=85', 1, 234),

(4, 'Cumi-Cumi Segar 500g',
 'Cumi-cumi segar ukuran sedang, dibersihkan higienis. Tekstur kenyal, cocok untuk cumi goreng tepung, saus tiram, dan calamari.',
 38000, NULL, 22, '500g',
 'https://images.unsplash.com/photo-1565680018434-b513d5e5fd47?w=600&q=85', 0, 145),

-- Susu & Dairy (cat 5)
(5, 'Susu Full Cream UHT 1L',
 'Susu sapi murni full cream dalam kemasan UHT. Kaya kalsium, protein, dan vitamin D untuk tulang kuat. Tanpa pengawet tambahan.',
 19000, NULL, 200, 'liter',
 'https://images.unsplash.com/photo-1550583724-b2692b85b150?w=600&q=85', 1, 678),

(5, 'Yogurt Greek Plain 200g',
 'Greek yogurt premium, tekstur creamy dan thick. Probiotik aktif untuk pencernaan sehat. Protein tinggi, rendah lemak. Tanpa pemanis buatan.',
 24000, 28000, 60, 'cup',
 'https://images.unsplash.com/photo-1488477181946-6428a0291777?w=600&q=85', 0, 234),

(5, 'Keju Mozzarella 200g',
 'Keju mozzarella premium, lumer sempurna saat dipanaskan. Cocok untuk pizza, pasta, dan caprese salad. Impor kualitas restoran.',
 38000, 45000, 35, '200g',
 'https://images.unsplash.com/photo-1486297678162-eb2a19b0a32d?w=600&q=85', 0, 189),

(5, 'Butter Salted 200g',
 'Mentega tawar premium dari susu sapi berkualitas tinggi. Aroma harum dan rasa creamy. Cocok untuk memasak, memanggang, dan spread roti.',
 28000, NULL, 80, '200g',
 'https://images.unsplash.com/photo-1589985270826-4b7bb135bc9d?w=600&q=85', 0, 167),

-- Minuman (cat 6)
(6, 'Air Mineral 1500ml',
 'Air mineral murni dari mata air pegunungan, proses filtrasi alami. pH seimbang 7.4 untuk hidrasi optimal.',
 5000, NULL, 500, 'botol',
 'https://images.unsplash.com/photo-1560023907-5f339617ea30?w=600&q=85', 0, 892),

(6, 'Jus Alpukat Segar 250ml',
 'Jus alpukat murni 100% tanpa pengawet dan pewarna buatan. Diproses fresh setiap hari, kaya lemak baik dan vitamin E.',
 22000, 26000, 40, 'cup',
 'https://images.unsplash.com/photo-1550258987-190a2d41a8ba?w=600&q=85', 1, 312),

(6, 'Teh Hijau Cold Brew 500ml',
 'Teh hijau cold brew premium, diseduh 8 jam dalam suhu rendah untuk rasa yang lebih smooth. Kaya antioksidan, rendah kafein.',
 18000, NULL, 55, 'botol',
 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=600&q=85', 0, 198),

-- Makanan Instan (cat 7)
(7, 'Oatmeal Instant Rasa Coklat 10-pack',
 'Oatmeal instan kaya serat dengan cita rasa coklat yang lezat. Siap saji dalam 3 menit, sarapan sehat praktis setiap hari.',
 45000, 52000, 80, 'pack',
 'https://images.unsplash.com/photo-1517673132405-a56a62b18caf?w=600&q=85', 0, 234),

(7, 'Beras Organik Premium 5kg',
 'Beras organik varietas Pandan Wangi dari sawah organik bersertifikat Jawa Barat. Nasi pulen harum tanpa pestisida.',
 85000, 95000, 35, 'karung',
 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=600&q=85', 1, 445),

-- Snack & Cemilan (cat 8)
(8, 'Granola Homemade 300g',
 'Granola artisan homemade dari rolled oats, kacang mixed, madu, dan buah kering. Tanpa pengawet, rendah gula. Sempurna dengan yogurt atau susu.',
 52000, 60000, 45, '300g',
 'https://images.unsplash.com/photo-1517686469429-8bdb88b9f907?w=600&q=85', 1, 189),

(8, 'Dark Chocolate 70% 100g',
 'Dark chocolate premium 70% kakao, rasa kompleks dengan sentuhan pahit yang kaya. Kaya antioksidan flavonoid untuk kesehatan jantung.',
 32000, 38000, 65, '100g',
 'https://images.unsplash.com/photo-1481391319762-47dff72954d9?w=600&q=85', 0, 234),

(8, 'Kacang Mete Panggang 200g',
 'Kacang mete premium dipanggang sempurna tanpa minyak tambahan. Renyah alami, kaya protein dan lemak baik. Snack sehat untuk aktivitas harian.',
 48000, 55000, 70, '200g',
 'https://images.unsplash.com/photo-1567892737950-30c4db37cd89?w=600&q=85', 0, 167),

-- Bumbu & Rempah (cat 9)
(9, 'Jahe Merah Segar 500g',
 'Jahe merah segar berkualitas tinggi, aroma kuat dan rasa pedas hangat. Kaya gingerol untuk imunitas dan anti-inflamasi alami.',
 18000, NULL, 90, '500g',
 'https://images.unsplash.com/photo-1615485500704-8e990f9900f7?w=600&q=85', 0, 312),

(9, 'Bawang Putih Segar 500g',
 'Bawang putih lokal segar grade A, ukuran besar dan siung penuh. Aroma tajam khas, kaya allicin untuk antibakteri alami.',
 16000, 20000, 120, '500g',
 'https://images.unsplash.com/photo-1540148426945-6cf22a6b2383?w=600&q=85', 0, 478),

(9, 'Cabai Merah Keriting 250g',
 'Cabai merah keriting segar pilihan, tingkat kepedasan medium-hot. Warna merah cerah menggoda, digunakan untuk sambal, tumisan, dan masakan.',
 14000, NULL, 80, '250g',
 'https://images.unsplash.com/photo-1571680322279-a226e6a4cc2a?w=600&q=85', 0, 389),

-- Telur & Tahu (cat 10)
(10, 'Telur Ayam Kampung 1 Lusin',
 'Telur ayam kampung asli, kuning telur lebih besar dan berwarna oranye cerah. Protein lebih tinggi dan nutrisi lebih lengkap dari telur negeri.',
 32000, 36000, 100, 'lusin',
 'https://images.unsplash.com/photo-1587486913049-53fc88980cfc?w=600&q=85', 1, 567),

(10, 'Tahu Putih Homemade 400g',
 'Tahu putih segar buatan tangan, tekstur lembut dan padat. Kaya protein nabati, rendah kalori. Dibuat fresh setiap pagi tanpa pengawet.',
 10000, NULL, 150, '400g',
 'https://images.unsplash.com/photo-1584949091598-c31daaaa4aa9?w=600&q=85', 0, 345),

(10, 'Tempe Organik 300g',
 'Tempe organik dari kedelai non-GMO lokal, difermentasi tradisional 48 jam. Tekstur padat, rasa gurih khas. Sumber protein nabati terbaik.',
 12000, NULL, 120, '300g',
 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=600&q=85', 0, 423);

-- Vouchers
INSERT INTO vouchers (code, type, value, min_purchase, max_discount, quota, valid_from, valid_until, description, is_active) VALUES
('DAILY10',    'percentage', 10,    50000,  25000, 200, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY),  'Diskon 10% semua produk, min. belanja Rp50.000', 1),
('HEMAT20K',   'fixed',      20000, 100000, NULL,  100, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 14 DAY),  'Potongan Rp20.000 min. belanja Rp100.000',        1),
('NEWMEMBER',  'percentage', 20,    30000,  30000, 500, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 90 DAY),  'Diskon 20% khusus member baru',                   1),
('GRATIS5K',   'fixed',      5000,  25000,  NULL,  300, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 7 DAY),   'Potongan Rp5.000 min. belanja Rp25.000',          1),
('WEEKEND15',  'percentage', 15,    75000,  30000, 150, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 3 DAY),   'Promo akhir pekan diskon 15%',                    1),
('EXPIRED99',  'percentage', 99,    1000,   NULL,  100, '2023-01-01', '2023-12-31',                       'Voucher kadaluarsa (untuk testing QA)',            1);

-- Sample orders
INSERT INTO orders (user_id, recipient_name, address, city, phone, subtotal, discount_amount, total_price, status, tracking_number) VALUES
(2, 'Budi Santoso', 'Jl. Mawar No. 12, RT 03 RW 07, Condongcatur', 'Sleman',     '081234560002', 117000, 0,     117000, 'completed',  'JNE001234567890'),
(3, 'Sari Dewi',    'Jl. Parangtritis No. 88, Sewon',              'Bantul',     '081234560003', 88000,  8800,  79200,  'shipped',    'SICEPAT987654321'),
(4, 'Riko Pratama', 'Jl. Kaliurang KM 12, Ngaglik',               'Sleman',     '081234560004', 145000, 20000, 125000, 'processing', NULL),
(2, 'Budi Santoso', 'Jl. Mawar No. 12, RT 03 RW 07',              'Sleman',     '081234560002', 52000,  0,     52000,  'pending',    NULL);

INSERT INTO order_items (order_id, product_id, product_name, product_image, price, quantity, subtotal, is_reviewed) VALUES
(1, 1,  'Apel Fuji Premium 1kg',    'https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6?w=600&q=85', 29000, 1, 29000, 1),
(1, 13, 'Ayam Broiler Segar 1kg',   'https://images.unsplash.com/photo-1604503468506-a8da13d82791?w=600&q=85', 36000, 1, 36000, 1),
(1, 19, 'Susu Full Cream UHT 1L',   'https://images.unsplash.com/photo-1550583724-b2692b85b150?w=600&q=85', 19000, 2, 38000, 1),
(1, 34, 'Telur Ayam Kampung 1 Lusin','https://images.unsplash.com/photo-1587486913049-53fc88980cfc?w=600&q=85',32000, 0, 14000, 0),
(2, 15, 'Ikan Salmon Fillet 300g',  'https://images.unsplash.com/photo-1574781330855-d0db8cc6a79c?w=600&q=85', 88000, 1, 88000, 0),
(3, 14, 'Daging Sapi Segar Has 500g','https://images.unsplash.com/photo-1607623814075-e51df1bdc82f?w=600&q=85',68000, 1, 68000, 0),
(3, 27, 'Granola Homemade 300g',    'https://images.unsplash.com/photo-1517686469429-8bdb88b9f907?w=600&q=85', 52000, 1, 52000, 0),
(3, 16, 'Udang Vannamei Segar 500g','https://images.unsplash.com/photo-1559737558-2f5a35f4523b?w=600&q=85',  48000, 0,  0, 0),
(4, 22, 'Air Mineral 1500ml',       'https://images.unsplash.com/photo-1560023907-5f339617ea30?w=600&q=85',   5000, 2, 10000, 0),
(4, 27, 'Granola Homemade 300g',    'https://images.unsplash.com/photo-1517686469429-8bdb88b9f907?w=600&q=85', 52000, 1, 52000, 0);

-- Reviews
INSERT INTO product_reviews (product_id, user_id, order_id, rating, comment) VALUES
(1,  2, 1, 5, 'Apelnya fresh banget! Manis, renyah, dan ukurannya besar-besar. Packagingnya juga rapi, tidak ada yang rusak. Recommended!'),
(13, 2, 1, 5, 'Ayamnya bersih, segar, tidak berbau. Daging empuk setelah dimasak. Pasti beli lagi di DailyMart!'),
(19, 2, 1, 4, 'Susu enak dan segar. Kemasan tetap baik saat diterima. Harga juga wajar untuk kualitas segini.');

-- Notifications
INSERT INTO notifications (user_id, type, title, message, link) VALUES
(2, 'order',   'Pesanan Selesai! ✓',         'Pesanan #DM-20240101-00001 telah diterima. Berikan ulasan produk Anda!', '/orders/1'),
(2, 'promo',   'Flash Sale Hari Ini! 🔥',     'Diskon hingga 30% produk pilihan. Gunakan kode DAILY10.',               '/'),
(2, 'promo',   'Voucher Baru Tersedia 🎟️',   'Kode HEMAT20K: Potongan Rp20.000 untuk belanja min. Rp100.000.',        '/voucher'),
(3, 'order',   'Pesanan Dalam Pengiriman 🚚', 'Pesanan #DM-20240101-00002 sedang dikirim. No. resi: SICEPAT987654321.', '/orders/2'),
(4, 'order',   'Pesanan Dikonfirmasi ✓',      'Pesanan #DM-20240101-00003 sedang dipersiapkan oleh tim kami.',          '/orders/3');

-- Wishlist samples
INSERT INTO wishlist (user_id, product_id) VALUES
(2, 3), (2, 15), (2, 27), (3, 1), (3, 19), (4, 14), (4, 16);

SELECT CONCAT('✓ DailyMart v3.0 siap! ', COUNT(*), ' produk berhasil diimport.') AS status FROM products;

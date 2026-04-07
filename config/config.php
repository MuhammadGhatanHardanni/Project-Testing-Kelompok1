<?php
// config/config.php — DailyMart v1.0

define('APP_NAME',     'DailyMart');
define('APP_TAGLINE',  'Kebutuhan Harian, Kualitas Premium');
define('APP_URL',      'http://localhost/Project-Testing-Kelompok1/public');
define('APP_VERSION',  '1.0.0');

define('DB_HOST',    'localhost');
define('DB_NAME',    'dailymart');
define('DB_USER',    'root');
define('DB_PASS',    '');
define('DB_CHARSET', 'utf8mb4');

define('SESSION_NAME', 'dailymart_session');

define('ROOT_PATH',   dirname(__DIR__));
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('VIEW_PATH',   ROOT_PATH . '/app/views');
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads/products');
define('UPLOAD_URL',  APP_URL . '/uploads/products');

error_reporting(E_ALL);
ini_set('display_errors', 1);

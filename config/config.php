<?php
/**
 * Konfigurazioa: datu-basearen konexioa eta aplikazioaren parametroak.
 * (2.4.1) PHP + MySQL/MariaDB konexioaren konfigurazioa.
 */

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'ecommerce');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_CHARSET', 'utf8mb4');

define('APP_NAME', 'WinaDenda');
define('APP_DEBUG', false);               // true = erroreak pantailan (garapenean bakarrik)
define('LOG_FILE', __DIR__ . '/../logs/app.log');
define('BASE_PATH', dirname(__DIR__));

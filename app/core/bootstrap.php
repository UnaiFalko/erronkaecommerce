<?php
/**
 * Hasieraketa: autoload, saioa, errore-kudeaketa eta log-ak.
 * (1.4.1) Akatsak identifikatu eta konpontzeko: erroreak erregistratzen dira
 * logs/app.log fitxategian, eta erabiltzaileak mezu garbi bat ikusten du.
 */
require_once __DIR__ . '/../../config/config.php';

spl_autoload_register(function (string $class): void {
    foreach (['core', 'models', 'controllers'] as $dir) {
        $file = BASE_PATH . "/app/$dir/$class.php";
        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});

function logError(string $message): void
{
    $dir = dirname(LOG_FILE);
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
    @file_put_contents(
        LOG_FILE,
        '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL,
        FILE_APPEND
    );
}

error_reporting(E_ALL);
ini_set('display_errors', APP_DEBUG ? '1' : '0');

set_error_handler(function (int $no, string $str, string $file, int $line): bool {
    logError("PHP[$no] $str @ $file:$line");
    return true;
});

set_exception_handler(function (Throwable $e): void {
    logError(get_class($e) . ': ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    http_response_code(500);
    if (APP_DEBUG) {
        echo '<pre>' . htmlspecialchars((string) $e) . '</pre>';
    } else {
        echo '<h1>Errorea</h1><p>Barkatu, zerbait gaizki atera da. Saiatu berriro geroago.</p>';
    }
});

// Saio seguruagoa
session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
session_start();

// ---- Laguntza-funtzioak (bistetan erabiltzeko) ----
function e(?string $v): string
{
    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
}

function prezioa(float $v): string
{
    return number_format($v, 2, ',', '.') . ' €';
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrfField(): string
{
    return '<input type="hidden" name="csrf" value="' . csrfToken() . '">';
}

function flash(?string $msg = null, string $type = 'ok')
{
    if ($msg !== null) {
        $_SESSION['flash'] = ['msg' => $msg, 'type' => $type];
        return null;
    }
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

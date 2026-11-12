<?php
/**
 * Configuración de la base de datos y de la aplicación.
 * La clave de cifrado se guarda localmente si no se configura otra.
 */

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'ecommerce');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_CHARSET', 'utf8mb4');

$encryptionKeyFile = __DIR__ . '/.encryption-key';
$environmentEncryptionKey = getenv('APP_ENCRYPTION_KEY');
$encryptionKey = is_file($encryptionKeyFile)
	? trim((string) file_get_contents($encryptionKeyFile))
	: $environmentEncryptionKey;

if ($encryptionKey === false || $encryptionKey === '') {
	$encryptionKey = base64_encode(random_bytes(32));
}

$decodedEncryptionKey = base64_decode($encryptionKey, true);
if ($decodedEncryptionKey === false || strlen($decodedEncryptionKey) !== 32) {
	throw new RuntimeException('La clave local de cifrado no es válida.');
}

if (!is_file($encryptionKeyFile)) {
	if (file_put_contents($encryptionKeyFile, $encryptionKey, LOCK_EX) === false) {
		throw new RuntimeException('No se pudo guardar la clave local de cifrado.');
	}
}

define('APP_ENCRYPTION_KEY', $encryptionKey);
$fallbackKey = base64_decode((string) $environmentEncryptionKey, true);
define(
	'APP_ENCRYPTION_KEY_FALLBACK',
	$fallbackKey !== false && strlen($fallbackKey) === 32 && $environmentEncryptionKey !== $encryptionKey
		? $environmentEncryptionKey
		: ''
);

define('APP_NAME', 'WinaDenda');
define('APP_DEBUG', false);               // Actívalo solo durante el desarrollo.
define('BASE_PATH', dirname(__DIR__));

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
        echo '<h1>' . tr('Error', 'Errorea') . '</h1><p>' . tr('Lo sentimos, algo ha fallado. Inténtalo de nuevo más tarde.', 'Barkatu, zerbait gaizki atera da. Saiatu berriro geroago.') . '</p>';
    }
});

// Saio seguruagoa
session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
session_start();

if (in_array($_GET['lang'] ?? '', ['es', 'eu'], true)) {
    $_SESSION['lang'] = $_GET['lang'];
}

function currentLanguage(): string
{
    return $_SESSION['lang'] ?? 'es';
}

function tr(string $spanish, string $basque): string
{
    return currentLanguage() === 'eu' ? $basque : $spanish;
}

function categoryLabel(?string $category): string
{
    $labels = [
        'Productos' => ['Productos', 'Produktuak'],
        'Cuentas' => ['Cuentas', 'Kontuak'],
        'Boosting' => ['Boosting', 'Boostinga'],
    ];
    if ($category === null || !isset($labels[$category])) {
        return $category ?? tr('Sin categoría', 'Kategoriarik gabe');
    }
    return tr($labels[$category][0], $labels[$category][1]);
}

function orderStatusLabel(string $status): string
{
    $labels = [
        'berria' => ['Nuevo', 'Berria'],
        'prozesatzen' => ['En proceso', 'Prozesatzen'],
        'bidalita' => ['Enviado', 'Bidalita'],
        'entregatuta' => ['Entregado', 'Entregatuta'],
        'bertan_behera' => ['Cancelado', 'Bertan behera'],
    ];
    return isset($labels[$status]) ? tr($labels[$status][0], $labels[$status][1]) : $status;
}

function localizedProductName(string $name): string
{
    if (currentLanguage() !== 'eu') {
        return $name;
    }

    return strtr($name, [
        'Pokémonedas' => 'Pokemon txanponak',
        'Pokemonedas' => 'Pokemon txanponak',
        'Pavos Fortnite' => 'Fortnite V-Bucks',
        'Monedas FIFA' => 'FIFA txanponak',
        'Pokémon GO' => 'Pokemon GO',
        'Pokemon GO' => 'Pokemon GO',
        'Rango Hierro' => 'Burdin maila',
        'Rango Bronce' => 'Brontze maila',
        'Rango Plata' => 'Zilar maila',
        'Rango Oro' => 'Urre maila',
        'Rango Platino' => 'Platino maila',
        'Rango Diamante' => 'Diamante maila',
        'Rango Ascendente' => 'Goranzko maila',
        'Rango Inmortal' => 'Hilezkor maila',
        'Rango Radiante' => 'Distira maila',
        'Rango Unreal' => 'Unreal maila',
        ' polvos estelares' => ' izar-hauts',
        'Cuenta nivel 80' => '80. mailako kontua',
        'Cuenta con +50 skins' => '+50 skin dituen kontua',
        'Cuenta con +20 skins' => '+20 skin dituen kontua',
        'Cuenta lifetime' => 'Bizitza osorako kontua',
    ]);
}

function localizedProductDescription(string $description): string
{
    if (currentLanguage() !== 'eu') {
        return $description;
    }

    if (preg_match('/^Pack digital de (.+) (?:Pokémonedas|Pokemonedas)\.$/u', $description, $matches)) {
        return $matches[1] . ' Pokemon txanponeko pakete digitala.';
    }
    if (preg_match('/^Pack digital de (.+) Valorant Points\.$/u', $description, $matches)) {
        return $matches[1] . ' Valorant Points-eko pakete digitala.';
    }
    if (preg_match('/^Pack digital de (.+) pavos\.$/u', $description, $matches)) {
        return 'Fortnite-ko ' . $matches[1] . ' V-Bucks-eko pakete digitala.';
    }
    if (preg_match('/^Pack de (.+) monedas para FIFA \/ EA SPORTS FC\.$/u', $description, $matches)) {
        return 'FIFA / EA SPORTS FC-rako ' . $matches[1] . ' txanpon.';
    }
    if (preg_match('/^Pack digital de (.+) FIFA Points\.$/u', $description, $matches)) {
        return $matches[1] . ' FIFA Points-eko pakete digitala.';
    }
    if (preg_match('/^Pack de boosting para conseguir (.+) polvos estelares\.$/u', $description, $matches)) {
        return $matches[1] . ' izar-hauts lortzeko boosting-paketea.';
    }
    if (preg_match('/^Pack de boosting para conseguir (.+) de polvos estelares\.$/u', $description, $matches)) {
        return $matches[1] . ' izar-hauts lortzeko boosting-paketea.';
    }
    if (preg_match('/^Pack de boosting para conseguir (.+) XP\.$/u', $description, $matches)) {
        return $matches[1] . ' XP lortzeko boosting-paketea.';
    }
    if (preg_match('/^Servicio de boosting hasta el rango (.+) en Fortnite\.$/u', $description, $matches)) {
        return 'Fortnite-n ' . localizedProductName('Rango ' . $matches[1]) . ' lortzeko boosting-zerbitzua.';
    }
    if (preg_match('/^Servicio de boosting hasta el rango (.+)\.$/u', $description, $matches)) {
        return localizedProductName('Rango ' . $matches[1]) . ' lortzeko boosting-zerbitzua.';
    }
    if (preg_match('/^Cuenta digital lifetime de (.+)\.$/u', $description, $matches)) {
        return $matches[1] . ' zerbitzuko bizitza osorako kontu digitala.';
    }
    if (preg_match('/^Cuenta de (?:Pokémon GO|Pokemon GO) de nivel (.+)\.$/u', $description, $matches)) {
        return 'Pokemon GO-ko ' . $matches[1] . '. mailako kontua.';
    }
    if (preg_match('/^Cuenta de Fortnite con más de (.+) skins\.$/u', $description, $matches)) {
        return 'Fortnite-ko ' . $matches[1] . ' skin baino gehiago dituen kontua.';
    }
    if (preg_match('/^Cuenta de Valorant con más de (.+) skins\.$/u', $description, $matches)) {
        return 'Valorant-eko ' . $matches[1] . ' skin baino gehiago dituen kontua.';
    }
    if (str_contains($description, '15-0 en FUT Champions')) {
        return 'FUT Champions-en 15-0 emaitza lortzeko boosting-zerbitzua.';
    }

    return $description;
}

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
    if ($f) {
        $f['msg'] = localizedMessage($f['msg']);
    }
    return $f;
}

function localizedMessage(string $message): string
{
    $messages = [
        'Saioa hasi behar duzu orri hau ikusteko.' => ['Debes iniciar sesión para ver esta página.', 'Saioa hasi behar duzu orri hau ikusteko.'],
        'Ez daukazu baimenik.' => ['No tienes permiso para acceder.', 'Ez daukazu baimenik.'],
        'Email edo pasahitza okerra.' => ['El correo o la contraseña no son correctos.', 'Emaila edo pasahitza ez dira zuzenak.'],
        'Kontua sortu da. Hasi saioa.' => ['Cuenta creada. Inicia sesión.', 'Kontua sortu da. Hasi saioa.'],
        'Saioa itxi duzu.' => ['Has cerrado sesión.', 'Saioa itxi duzu.'],
        'Produktua ez da aurkitu.' => ['No se encontró el producto.', 'Produktua ez da aurkitu.'],
        'Produktua sortu da.' => ['Producto creado.', 'Produktua sortu da.'],
        'Produktua eguneratu da.' => ['Producto actualizado.', 'Produktua eguneratu da.'],
        'Produktua ezabatu da.' => ['Producto eliminado.', 'Produktua ezabatu da.'],
        'Ezin da ezabatu: eskaeretan erabilita dago.' => ['No se puede eliminar porque está incluido en pedidos.', 'Ezin da ezabatu: eskaeretan erabilita dago.'],
        'Produktua ez da existitzen.' => ['El producto no existe.', 'Produktua ez da existitzen.'],
        'Este producto está agotado.' => ['Este producto está agotado.', 'Produktu hau agortuta dago.'],
        'Ez dago stock nahikorik.' => ['No hay stock suficiente.', 'Ez dago stock nahikorik.'],
        'Saskira gehitu da.' => ['Añadido al carrito.', 'Saskira gehitu da.'],
        'Cantidades actualizadas.' => ['Cantidades actualizadas.', 'Kantitateak eguneratu dira.'],
        'Carrito vaciado.' => ['Carrito vaciado.', 'Saskia hustu da.'],
        'Saskia hutsik dago.' => ['El carrito está vacío.', 'Saskia hutsik dago.'],
        'Cuenta añadida al inventario.' => ['Cuenta añadida al inventario.', 'Kontua inbentarioari gehitu zaio.'],
        'Escribe el usuario y la contraseña de la cuenta.' => ['Escribe el usuario y la contraseña de la cuenta.', 'Idatzi kontuaren erabiltzailea eta pasahitza.'],
        'No hay suficientes cuentas disponibles para completar el pedido.' => ['No hay suficientes cuentas disponibles para completar el pedido.', 'Ez dago eskaera osatzeko behar adina kontu erabilgarri.'],
        'No se pudieron asignar las cuentas al pedido.' => ['No se pudieron asignar las cuentas al pedido.', 'Ezin izan dira kontuak eskaerari esleitu.'],
        'No se pudo leer una cuenta digital.' => ['No se pudo leer una cuenta digital.', 'Ezin izan da kontu digital bat irakurri.'],
        'No se pudo cifrar la cuenta digital.' => ['No se pudo cifrar la cuenta digital.', 'Ezin izan da kontu digitala zifratu.'],
        'La clave de cifrado no coincide con la usada al guardar las cuentas.' => ['La clave de cifrado no coincide con la usada al guardar las cuentas.', 'Zifratze-gakoa ez dator bat kontuak gordetzeko erabilitakoarekin.'],
        'Esta cuenta usa un cifrado antiguo incompatible; vuelve a añadir sus credenciales.' => ['Esta cuenta usa un cifrado antiguo incompatible; vuelve a añadir sus credenciales.', 'Kontu honek zifratze zahar bateraezina erabiltzen du; gehitu berriro haren kredentzialak.'],
        'Configura APP_ENCRYPTION_KEY con una clave Base64 de 32 bytes.' => ['Configura APP_ENCRYPTION_KEY con una clave Base64 de 32 bytes.', 'Konfiguratu APP_ENCRYPTION_KEY 32 byteko Base64 gako batekin.'],
        'OpenSSL con AES-256-GCM es necesario para gestionar cuentas digitales.' => ['OpenSSL with AES-256-GCM is required to manage digital accounts.', 'OpenSSL eta AES-256-GCM behar dira kontu digitalak kudeatzeko.'],
        'La extensión Sodium de PHP es necesaria para gestionar cuentas digitales.' => ['Este aviso es antiguo. Recarga la página: las cuentas digitales ya no requieren Sodium.', 'Abisu hau zaharkituta dago. Kargatu orria berriro: kontu digitalek ez dute Sodium behar.'],
        'No se encontró el pedido.' => ['No se encontró el pedido.', 'Eskaera ez da aurkitu.'],
        'Pedido eliminado.' => ['Pedido eliminado.', 'Eskaera ezabatu da.'],
        'Añadido a favoritos.' => ['Añadido a favoritos.', 'Gogokoetara gehitu da.'],
        'Eliminado de favoritos.' => ['Eliminado de favoritos.', 'Gogokoetatik kendu da.'],
        'La cantidad supera el stock disponible.' => ['La cantidad supera el stock disponible.', 'Kopuruak eskuragarri dagoen stock-a gainditzen du.'],
        'Izena beharrezkoa da.' => ['El nombre es obligatorio.', 'Izena beharrezkoa da.'],
        'Email baliogabea.' => ['El correo electrónico no es válido.', 'Email baliogabea.'],
        'Pasahitzak 6 karaktere gutxienez izan behar ditu.' => ['La contraseña debe tener al menos 6 caracteres.', 'Pasahitzak 6 karaktere gutxienez izan behar ditu.'],
        'Email hori erregistratuta dago.' => ['Ese correo ya está registrado.', 'Email hori erregistratuta dago.'],
        'Izena beharrezkoa da (gehienez 150 karaktere).' => ['El nombre es obligatorio (máximo 150 caracteres).', 'Izena beharrezkoa da (gehienez 150 karaktere).'],
        'Prezioak 0 baino handiagoa izan behar du.' => ['El precio debe ser mayor que 0.', 'Prezioak 0 baino handiagoa izan behar du.'],
        'Stock-a ezin da negatiboa izan.' => ['El stock no puede ser negativo.', 'Stock-a ezin da negatiboa izan.'],
        'Ezin da eskaera sortu.' => ['No se pudo crear el pedido.', 'Ezin da eskaera sortu.'],
    ];

    if (str_starts_with($message, 'Ongi etorri, ')) {
        return currentLanguage() === 'eu'
            ? $message
            : 'Bienvenido/a, ' . substr($message, strlen('Ongi etorri, '));
    }
    if (preg_match('/^Eskaera #(\d+) ondo erregistratu da\. Eskerrik asko!$/u', $message, $matches)) {
        return currentLanguage() === 'eu'
            ? $message
            : 'Pedido #' . $matches[1] . ' registrado correctamente. ¡Gracias!';
    }
    if (str_starts_with($message, 'La cantidad de ')) {
        if (currentLanguage() === 'eu') {
            $productName = str_replace(' supera el stock disponible.', '', substr($message, strlen('La cantidad de ')));
            return $productName . ' produktuaren kopuruak stock-a gainditzen du.';
        }
        return $message;
    }
    if (str_starts_with($message, 'Stock nahikorik ez: ')) {
        $productName = substr($message, strlen('Stock nahikorik ez: '));
        return currentLanguage() === 'eu'
            ? 'Ez dago nahikoa stock: ' . $productName
            : 'No hay stock suficiente: ' . $productName;
    }

    foreach ($messages as $basque => [$spanish, $eu]) {
        $message = str_replace([$basque, $spanish, $eu], currentLanguage() === 'eu' ? $eu : $spanish, $message);
    }
    return $message;
}

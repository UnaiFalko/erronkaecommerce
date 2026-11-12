<?php
/**
 * Elige qué controlador y acción ejecutar según los parámetros de la URL.
 */
class Router
{
    private const ALLOWED = [
        'home'      => 'HomeController',
        'auth'      => 'AuthController',
        'produktua' => 'ProduktuaController',
        'saskia'    => 'SaskiaController',
        'eskaera'   => 'EskaeraController',
        'erabiltzailea' => 'ErabiltzaileaController',
    ];

    /** Comprueba la URL y ejecuta la acción correspondiente. */
    public static function dispatch(): void
    {
        $c = $_GET['c'] ?? 'home';
        $a = $_GET['a'] ?? 'index';

        if (!isset(self::ALLOWED[$c]) || !preg_match('/^[a-z]+$/i', $a)) {
            self::notFound();
            return;
        }

        $class  = self::ALLOWED[$c];
        $method = $a . 'Action';
        $ctrl   = new $class();

        if (!method_exists($ctrl, $method)) {
            self::notFound();
            return;
        }
        $ctrl->$method();
    }

    /** Muestra una página sencilla cuando la ruta no existe. */
    private static function notFound(): void
    {
        http_response_code(404);
        echo '<h1>404</h1><p>' . tr('No se encontró la página.', 'Orria ez da aurkitu.') . '</p><a href="index.php">' . tr('Inicio', 'Hasiera') . '</a>';
    }
}

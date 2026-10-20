<?php
/**
 * Bideratzailea: ?c=kontrolatzailea&a=ekintza  ->  KontrolatzaileaController::ekintzaAction()
 * (2.2.3) MVC arkitekturaren "front controller" patroia.
 */
class Router
{
    private const ALLOWED = [
        'home'      => 'HomeController',
        'auth'      => 'AuthController',
        'produktua' => 'ProduktuaController',
        'saskia'    => 'SaskiaController',
        'eskaera'   => 'EskaeraController',
    ];

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

    private static function notFound(): void
    {
        http_response_code(404);
        echo '<h1>404</h1><p>Orria ez da aurkitu.</p><a href="index.php">Hasierara</a>';
    }
}

<?php
/**
 * Funciones comunes para mostrar páginas, redirigir y comprobar permisos y formularios.
 */
abstract class Controller
{
    /** Carga la cabecera, la vista solicitada y el pie de página. */
    protected function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $viewFile = BASE_PATH . "/app/views/$view.php";
        if (!is_file($viewFile)) {
            throw new RuntimeException("Bista ez da existitzen: $view");
        }
        require BASE_PATH . '/app/views/layout/header.php';
        require $viewFile;
        require BASE_PATH . '/app/views/layout/footer.php';
    }

    /** Envía al navegador a otra acción y termina la petición. */
    protected function redirect(string $c, string $a = 'index', array $params = []): void
    {
        $qs = http_build_query(array_merge(['c' => $c, 'a' => $a], $params));
        header('Location: index.php?' . $qs);
        exit;
    }

    /** Devuelve los datos del usuario que inició sesión. */
    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    /** Pide iniciar sesión antes de continuar. */
    protected function requireLogin(): void
    {
        $user = self::user();
        if (!$user) {
            flash('Saioa hasi behar duzu orri hau ikusteko.', 'err');
            $this->redirect('auth', 'login');
        }

        $currentUser = (new Erabiltzailea())->bilatu((int) $user['id']);
        if (!$currentUser) {
            unset($_SESSION['user']);
            flash(tr('Tu cuenta ya no está disponible. Inicia sesión de nuevo.', 'Zure kontua ez dago erabilgarri. Hasi saioa berriro.'), 'err');
            $this->redirect('auth', 'login');
        }

        $_SESSION['user'] = [
            'id' => (int) $currentUser['id'],
            'izena' => $currentUser['izena'],
            'email' => $currentUser['email'],
            'rola' => $currentUser['rola'],
        ];
    }

    /** Comprueba que el usuario haya iniciado sesión como administrador. */
    protected function requireAdmin(): void
    {
        $this->requireLogin();
        if ((self::user()['rola'] ?? '') !== 'admin') {
            http_response_code(403);
            flash('Ez daukazu baimenik.', 'err');
            $this->redirect('home');
        }
    }

    /** Rechaza formularios que no tengan el token de seguridad correcto. */
    protected function verifyCsrf(): void
    {
        $token = $_POST['csrf'] ?? '';
        if (!hash_equals($_SESSION['csrf'] ?? '', $token)) {
            http_response_code(419);
            throw new RuntimeException('CSRF token baliogabea.');
        }
    }

    /** Indica si la petición actual se envió mediante POST. */
    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
}

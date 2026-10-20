<?php
/**
 * Kontrolatzaile nagusia: bistak errendatu, birbideratu, saioa eta CSRF kontrolak.
 * (2.2.1) Frontend-a backend-etik bereizten da: kontrolatzaileak datuak prestatzen ditu,
 * bistek (HTML/CSS) datuak bakarrik erakusten dituzte.
 */
abstract class Controller
{
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

    protected function redirect(string $c, string $a = 'index', array $params = []): void
    {
        $qs = http_build_query(array_merge(['c' => $c, 'a' => $a], $params));
        header('Location: index.php?' . $qs);
        exit;
    }

    // ---- Saioa / autentifikazioa (2.2.1) ----
    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    protected function requireLogin(): void
    {
        if (!self::user()) {
            flash('Saioa hasi behar duzu orri hau ikusteko.', 'err');
            $this->redirect('auth', 'login');
        }
    }

    protected function requireAdmin(): void
    {
        $this->requireLogin();
        if ((self::user()['rola'] ?? '') !== 'admin') {
            http_response_code(403);
            flash('Ez daukazu baimenik.', 'err');
            $this->redirect('home');
        }
    }

    protected function verifyCsrf(): void
    {
        $token = $_POST['csrf'] ?? '';
        if (!hash_equals($_SESSION['csrf'] ?? '', $token)) {
            http_response_code(419);
            throw new RuntimeException('CSRF token baliogabea.');
        }
    }

    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
}

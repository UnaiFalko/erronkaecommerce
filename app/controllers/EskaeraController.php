<?php
/**
 * Eskaerak: erosketa burutu, nire eskaerak, admin: eskaera guztiak.
 */
class EskaeraController extends Controller
{
    public function indexAction(): void
    {
        $this->requireLogin();
        $model = new Eskaera();
        $admin = (self::user()['rola'] ?? '') === 'admin';

        $this->render('eskaerak/index', [
            'titulua' => $admin ? 'Todos los pedidos' : 'Mis pedidos',
            'eskaerak' => $admin ? $model->guztiak() : $model->erabiltzailearenak(self::user()['id']),
            'admin' => $admin,
        ]);
    }

    public function burutuAction(): void
    {
        $this->requireLogin();
        if (!$this->isPost()) $this->redirect('saskia');
        $this->verifyCsrf();

        $saskia = $_SESSION['saskia'] ?? [];
        if (!$saskia) {
            flash('Saskia hutsik dago.', 'err');
            $this->redirect('saskia');
        }
        try {
            $id = (new Eskaera())->sortu(self::user()['id'], $saskia);
            unset($_SESSION['saskia']);
            flash("Eskaera #$id ondo erregistratu da. Eskerrik asko!");
            $this->redirect('eskaera', 'xehetasunak', ['id' => $id]);
        } catch (RuntimeException $e) {
            flash($e->getMessage(), 'err');
            $this->redirect('saskia');
        }
    }

    public function ezabatuAction(): void
    {
        $this->requireAdmin();
        if (!$this->isPost()) {
            $this->redirect('eskaera');
        }
        $this->verifyCsrf();

        $ezabatuta = (new Eskaera())->ezabatu((int) ($_POST['id'] ?? 0));
        flash(
            $ezabatuta ? 'Pedido eliminado.' : 'No se encontró el pedido.',
            $ezabatuta ? 'ok' : 'err'
        );
        $this->redirect('eskaera');
    }

    public function xehetasunakAction(): void
    {
        $this->requireLogin();
        $model = new Eskaera();
        $e = $model->bilatu((int) ($_GET['id'] ?? 0));
        $admin = (self::user()['rola'] ?? '') === 'admin';

        if (!$e || (!$admin && (int) $e['erabiltzailea_id'] !== self::user()['id'])) {
            http_response_code(404);
            flash('No se encontró el pedido.', 'err');
            $this->redirect('eskaera');
        }
        $this->render('eskaerak/xehetasunak', [
            'titulua' => 'Eskaera #' . $e['id'], 'e' => $e, 'lerroak' => $model->lerroak((int) $e['id']),
        ]);
    }
}

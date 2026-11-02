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
            'titulua' => $admin ? tr('Todos los pedidos', 'Eskaera guztiak') : tr('Mis pedidos', 'Nire eskaerak'),
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
        $produktuak = new Produktua();
        $kantitateak = $_POST['kopurua'] ?? [];
        foreach (is_array($kantitateak) ? $kantitateak : [] as $produktuaId => $kopurua) {
            $produktuaId = (int) $produktuaId;
            if (!isset($saskia[$produktuaId])) {
                continue;
            }
            $kopurua = (int) $kopurua;
            if ($kopurua < 1) {
                unset($saskia[$produktuaId]);
                continue;
            }
            $produktua = $produktuak->bilatu($produktuaId);
            if (!$produktua || $kopurua > (int) $produktua['stock']) {
                flash('La cantidad de ' . ($produktua['izena'] ?? 'un producto') . ' supera el stock disponible.', 'err');
                $this->redirect('saskia');
            }
            $saskia[$produktuaId] = $kopurua;
        }
        $_SESSION['saskia'] = $saskia;
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
            'titulua' => tr('Pedido #', 'Eskaera #') . $e['id'], 'e' => $e, 'lerroak' => $model->lerroak((int) $e['id']),
        ]);
    }
}

<?php
/**
 * Produktuen CRUD (admin) + produktuaren xehetasunak (publikoa).
 */
class ProduktuaController extends Controller
{
    private Produktua $model;

    public function __construct()
    {
        $this->model = new Produktua();
    }

    // READ (publikoa)
    public function xehetasunakAction(): void
    {
        $p = $this->model->bilatu((int) ($_GET['id'] ?? 0));
        if (!$p) {
            http_response_code(404);
            flash('Produktua ez da aurkitu.', 'err');
            $this->redirect('home');
        }
        $this->render('produktuak/xehetasunak', ['titulua' => $p['izena'], 'p' => $p]);
    }

    // READ (admin zerrenda)
    public function indexAction(): void
    {
        $this->requireAdmin();
        $this->render('produktuak/admin', [
            'titulua'    => tr('Gestionar productos', 'Produktuak kudeatu'),
            'produktuak' => $this->model->guztiak(),
        ]);
    }

    public function cuentasAction(): void
    {
        $this->requireAdmin();
        $id = (int) ($_GET['id'] ?? 0);
        $producto = $this->model->bilatu($id);
        if (!$producto || ($producto['kategoria'] ?? '') !== 'Cuentas') {
            flash('No se encontró el producto.', 'err');
            $this->redirect('produktua');
        }

        $inventario = new CuentaDigital();
        if ($this->isPost()) {
            $this->verifyCsrf();
            try {
                $inventario->agregar(
                    $id,
                    (string) ($_POST['usuario'] ?? ''),
                    (string) ($_POST['contrasena'] ?? '')
                );
                flash('Cuenta añadida al inventario.');
            } catch (RuntimeException $e) {
                flash($e->getMessage(), 'err');
            }
            $this->redirect('produktua', 'cuentas', ['id' => $id]);
        }

        $this->render('produktuak/cuentas', [
            'titulua' => tr('Cuentas disponibles', 'Kontu erabilgarriak'),
            'producto' => $producto,
            'disponibles' => $inventario->disponibles($id),
        ]);
    }

    // CREATE
    public function sortuAction(): void
    {
        $this->requireAdmin();
        $kategoriak = (new Kategoria())->guztiak();

        if ($this->isPost()) {
            $this->verifyCsrf();
            $esCuenta = false;
            foreach ($kategoriak as $categoria) {
                if ((string) $categoria['id'] === (string) ($_POST['kategoria_id'] ?? '')
                    && $categoria['izena'] === 'Cuentas') {
                    $esCuenta = true;
                    break;
                }
            }
            if ($esCuenta) {
                $_POST['stock'] = 0;
            }
            $this->model->betetzen($_POST);
            $err = $this->model->balidatu();
            if (!$err) {
                $this->model->sortu();
                flash('Produktua sortu da.');
                $this->redirect('produktua');
            }
            $this->render('produktuak/form', [
                'titulua' => tr('Nuevo producto', 'Produktu berria'), 'p' => $_POST, 'kategoriak' => $kategoriak,
                'errorak' => $err, 'ekintza' => 'sortu', 'id' => null, 'cuentaDigital' => $esCuenta,
            ]);
            return;
        }
        $this->render('produktuak/form', [
            'titulua' => tr('Nuevo producto', 'Produktu berria'), 'p' => [], 'kategoriak' => $kategoriak,
            'errorak' => [], 'ekintza' => 'sortu', 'id' => null, 'cuentaDigital' => false,
        ]);
    }

    // UPDATE
    public function editatuAction(): void
    {
        $this->requireAdmin();
        $id = (int) ($_GET['id'] ?? 0);
        $p = $this->model->bilatu($id);
        if (!$p) {
            flash('Produktua ez da aurkitu.', 'err');
            $this->redirect('produktua');
        }
        $kategoriak = (new Kategoria())->guztiak();
        $cuentaDigital = ($p['kategoria'] ?? '') === 'Cuentas';

        if ($this->isPost()) {
            $this->verifyCsrf();
            $esCuenta = $cuentaDigital;
            if ($cuentaDigital) {
                $_POST['stock'] = (int) $p['stock'];
                $_POST['kategoria_id'] = $p['kategoria_id'];
            } else {
                foreach ($kategoriak as $categoria) {
                    if ((string) $categoria['id'] === (string) ($_POST['kategoria_id'] ?? '')
                        && $categoria['izena'] === 'Cuentas') {
                        $_POST['stock'] = (new CuentaDigital())->disponibles($id);
                        $esCuenta = true;
                        break;
                    }
                }
            }
            $this->model->betetzen($_POST);
            $this->model->id = $id;
            $err = $this->model->balidatu();
            if (!$err) {
                $this->model->eguneratu();
                flash('Produktua eguneratu da.');
                $this->redirect('produktua');
            }
            $p = $_POST;
            $this->render('produktuak/form', [
                'titulua' => tr('Editar producto', 'Produktua editatu'), 'p' => $p, 'kategoriak' => $kategoriak,
                'errorak' => $err, 'ekintza' => 'editatu', 'id' => $id, 'cuentaDigital' => $esCuenta,
            ]);
            return;
        }
        $this->render('produktuak/form', [
            'titulua' => tr('Editar producto', 'Produktua editatu'), 'p' => $p, 'kategoriak' => $kategoriak,
            'errorak' => [], 'ekintza' => 'editatu', 'id' => $id, 'cuentaDigital' => $cuentaDigital,
        ]);
    }

    // DELETE
    public function ezabatuAction(): void
    {
        $this->requireAdmin();
        if (!$this->isPost()) {
            $this->redirect('produktua');
        }
        $this->verifyCsrf();
        try {
            $this->model->ezabatu((int) ($_POST['id'] ?? 0));
            flash('Produktua ezabatu da.');
        } catch (PDOException $e) {
            // Eskaeretan erabilita badago, FK-k ez du uzten
            logError('Ezabatzeko errorea: ' . $e->getMessage());
            flash('Ezin da ezabatu: eskaeretan erabilita dago.', 'err');
        }
        $this->redirect('produktua');
    }
}

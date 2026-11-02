<?php
class HomeController extends Controller
{
    public function indexAction(): void
    {
        $kategoriaId = isset($_GET['kategoria']) ? (int) $_GET['kategoria'] : null;
        $bilaketa = trim($_GET['q'] ?? '');
        $orden = $_GET['orden'] ?? 'recientes';
        $ordenBaliozkoak = ['recientes', 'precio_menor', 'precio_mayor', 'nombre'];
        if (!in_array($orden, $ordenBaliozkoak, true)) {
            $orden = 'recientes';
        }
        $favoritos = array_map('intval', $_SESSION['favoritos'] ?? []);
        $soloFavoritos = ($_GET['favoritos'] ?? '') === '1';
        $produktuak = (new Produktua())->katalogoa($kategoriaId, $bilaketa, $orden);
        if ($soloFavoritos) {
            $produktuak = array_values(array_filter(
                $produktuak,
                static fn($producto) => in_array((int) $producto['id'], $favoritos, true)
            ));
        }

        $this->render('produktuak/katalogoa', [
            'titulua'     => tr('Servicios digitales', 'Zerbitzu digitalak'),
            'produktuak'  => $produktuak,
            'kategoriak'  => (new Kategoria())->dendakoak(),
            'kategoriaId' => $kategoriaId,
            'bilaketa'    => $bilaketa,
            'orden'       => $orden,
            'favoritos'   => $favoritos,
            'soloFavoritos' => $soloFavoritos,
        ]);
    }

    public function favoritoAction(): void
    {
        if (!$this->isPost()) {
            $this->redirect('home');
        }
        $this->verifyCsrf();

        $id = (int) ($_POST['id'] ?? 0);
        $favoritos = array_map('intval', $_SESSION['favoritos'] ?? []);
        if ($id > 0 && in_array($id, $favoritos, true)) {
            $favoritos = array_values(array_diff($favoritos, [$id]));
            flash('Eliminado de favoritos.');
        } elseif ($id > 0) {
            $favoritos[] = $id;
            flash('Añadido a favoritos.');
        }
        $_SESSION['favoritos'] = array_values(array_unique($favoritos));

        if (($_POST['detalle'] ?? '') === '1') {
            $detalleId = (int) ($_POST['detalle_id'] ?? 0);
            if ($detalleId > 0) {
                $this->redirect('produktua', 'xehetasunak', ['id' => $detalleId]);
            }
        }

        $params = [];
        $categoria = (int) ($_POST['kategoria'] ?? 0);
        $busqueda = trim($_POST['q'] ?? '');
        $orden = $_POST['orden'] ?? 'recientes';
        if ($categoria > 0) {
            $params['kategoria'] = $categoria;
        }
        if ($busqueda !== '') {
            $params['q'] = $busqueda;
        }
        if (in_array($orden, ['recientes', 'precio_menor', 'precio_mayor', 'nombre'], true)) {
            $params['orden'] = $orden;
        }
        if (($_POST['favoritos'] ?? '') === '1') {
            $params['favoritos'] = 1;
        }
        $this->redirect('home', 'index', $params);
    }
}

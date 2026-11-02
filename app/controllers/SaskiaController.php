<?php
/**
 * Erosketa-saskia $_SESSION['saskia'] barruan: [produktuId => kopurua]
 */
class SaskiaController extends Controller
{
    public function indexAction(): void
    {
        $saskia = $_SESSION['saskia'] ?? [];
        $lerroak = [];
        $guztira = 0.0;
        $stockNahikorikEz = false;
        $model = $saskia ? new Produktua() : null;   // saski hutsak ez du DB konexiorik behar

        foreach ($saskia as $pid => $kop) {
            $p = $model->bilatu((int) $pid);
            if (!$p) {
                unset($_SESSION['saskia'][$pid]);
                continue;
            }
            $p['kopurua'] = $kop;
            $p['stockNahikorikEz'] = (int) $kop > (int) $p['stock'];
            $stockNahikorikEz = $stockNahikorikEz || $p['stockNahikorikEz'];
            $p['subtotala'] = $p['prezioa'] * $kop;
            $guztira += $p['subtotala'];
            $lerroak[] = $p;
        }
        $this->render('saskia/index', [
            'titulua' => tr('Carrito', 'Saskia'), 'lerroak' => $lerroak, 'guztira' => $guztira,
            'stockNahikorikEz' => $stockNahikorikEz,
        ]);
    }

    public function gehituAction(): void
    {
        if (!$this->isPost()) $this->redirect('home');
        $this->verifyCsrf();

        $id  = (int) ($_POST['id'] ?? 0);
        $kop = max(1, (int) ($_POST['kopurua'] ?? 1));
        $p = (new Produktua())->bilatu($id);

        if (!$p) {
            flash('Produktua ez da existitzen.', 'err');
            $this->redirectAfterAdd($id);
        }
        if ((int) $p['stock'] < 1) {
            flash('Este producto está agotado.', 'err');
            $this->redirectAfterAdd($id);
        }
        $berria = ($_SESSION['saskia'][$id] ?? 0) + $kop;
        if ($berria > $p['stock']) {
            flash('Ez dago stock nahikorik.', 'err');
            $this->redirectAfterAdd($id);
        }
        $_SESSION['saskia'][$id] = $berria;
        flash('Saskira gehitu da.');
        $this->redirectAfterAdd($id);
    }

    private function redirectAfterAdd(int $id): void
    {
        if (($_POST['jatorria'] ?? '') === 'xehetasuna' && $id > 0) {
            $this->redirect('produktua', 'xehetasunak', ['id' => $id]);
        }

        $params = [];
        $kategoria = (int) ($_POST['kategoria'] ?? 0);
        $bilaketa = trim($_POST['q'] ?? '');
        $orden = $_POST['orden'] ?? 'recientes';
        if ($kategoria > 0) {
            $params['kategoria'] = $kategoria;
        }
        if ($bilaketa !== '') {
            $params['q'] = $bilaketa;
        }
        if (in_array($orden, ['recientes', 'precio_menor', 'precio_mayor', 'nombre'], true)) {
            $params['orden'] = $orden;
        }
        if (($_POST['favoritos'] ?? '') === '1') {
            $params['favoritos'] = 1;
        }
        $this->redirect('home', 'index', $params);
    }

    public function eguneratuAction(): void
    {
        if (!$this->isPost()) $this->redirect('saskia');
        $this->verifyCsrf();
        $model = new Produktua();
        $kantitateak = $_POST['kopurua'] ?? [];
        foreach (is_array($kantitateak) ? $kantitateak : [] as $id => $kop) {
            $id = (int) $id;
            if (!isset($_SESSION['saskia'][$id])) {
                continue;
            }
            $kop = (int) $kop;
            if ($kop < 1) {
                unset($_SESSION['saskia'][$id]);
                continue;
            }
            $producto = $model->bilatu($id);
            if (!$producto || $kop > (int) $producto['stock']) {
                flash('La cantidad supera el stock disponible.', 'err');
                $this->redirect('saskia');
            }
            $_SESSION['saskia'][$id] = $kop;
        }
        flash('Cantidades actualizadas.');
        $this->redirect('saskia');
    }

    public function kenduAction(): void
    {
        if (!$this->isPost()) $this->redirect('saskia');
        $this->verifyCsrf();
        unset($_SESSION['saskia'][(int) ($_POST['id'] ?? 0)]);
        $this->redirect('saskia');
    }

    public function vaciarAction(): void
    {
        if (!$this->isPost()) $this->redirect('saskia');
        $this->verifyCsrf();
        $_SESSION['saskia'] = [];
        flash('Carrito vaciado.');
        $this->redirect('saskia');
    }
}

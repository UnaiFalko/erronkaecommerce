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
        $model = $saskia ? new Produktua() : null;   // saski hutsak ez du DB konexiorik behar

        foreach ($saskia as $pid => $kop) {
            $p = $model->bilatu((int) $pid);
            if (!$p) {
                unset($_SESSION['saskia'][$pid]);
                continue;
            }
            $p['kopurua'] = $kop;
            $p['subtotala'] = $p['prezioa'] * $kop;
            $guztira += $p['subtotala'];
            $lerroak[] = $p;
        }
        $this->render('saskia/index', [
            'titulua' => 'Saskia', 'lerroak' => $lerroak, 'guztira' => $guztira,
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
            $this->redirect('home');
        }
        $berria = ($_SESSION['saskia'][$id] ?? 0) + $kop;
        if ($berria > $p['stock']) {
            flash('Ez dago stock nahikorik.', 'err');
            $this->redirect('produktua', 'xehetasunak', ['id' => $id]);
        }
        $_SESSION['saskia'][$id] = $berria;
        flash('Saskira gehitu da.');
        $this->redirect('saskia');
    }

    public function eguneratuAction(): void
    {
        if (!$this->isPost()) $this->redirect('saskia');
        $this->verifyCsrf();
        foreach (($_POST['kopurua'] ?? []) as $id => $kop) {
            $kop = (int) $kop;
            if ($kop < 1) {
                unset($_SESSION['saskia'][(int) $id]);
            } elseif (isset($_SESSION['saskia'][(int) $id])) {
                $_SESSION['saskia'][(int) $id] = $kop;
            }
        }
        flash('Saskia eguneratu da.');
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

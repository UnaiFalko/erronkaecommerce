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
            'titulua'    => 'Produktuak kudeatu',
            'produktuak' => $this->model->guztiak(),
        ]);
    }

    // CREATE
    public function sortuAction(): void
    {
        $this->requireAdmin();
        $kategoriak = (new Kategoria())->guztiak();

        if ($this->isPost()) {
            $this->verifyCsrf();
            $this->model->betetzen($_POST);
            $err = $this->model->balidatu();
            if (!$err) {
                $this->model->sortu();
                flash('Produktua sortu da.');
                $this->redirect('produktua');
            }
            $this->render('produktuak/form', [
                'titulua' => 'Produktu berria', 'p' => $_POST, 'kategoriak' => $kategoriak,
                'errorak' => $err, 'ekintza' => 'sortu', 'id' => null,
            ]);
            return;
        }
        $this->render('produktuak/form', [
            'titulua' => 'Produktu berria', 'p' => [], 'kategoriak' => $kategoriak,
            'errorak' => [], 'ekintza' => 'sortu', 'id' => null,
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

        if ($this->isPost()) {
            $this->verifyCsrf();
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
                'titulua' => 'Produktua editatu', 'p' => $p, 'kategoriak' => $kategoriak,
                'errorak' => $err, 'ekintza' => 'editatu', 'id' => $id,
            ]);
            return;
        }
        $this->render('produktuak/form', [
            'titulua' => 'Produktua editatu', 'p' => $p, 'kategoriak' => $kategoriak,
            'errorak' => [], 'ekintza' => 'editatu', 'id' => $id,
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

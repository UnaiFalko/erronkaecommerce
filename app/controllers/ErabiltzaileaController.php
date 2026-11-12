<?php
/** Permite a los administradores gestionar las cuentas de usuario. */
class ErabiltzaileaController extends Controller
{
    /** Muestra los usuarios registrados. */
    public function indexAction(): void
    {
        $this->requireAdmin();
        $this->render('erabiltzaileak/admin', [
            'titulua' => tr('Gestionar usuarios', 'Erabiltzaileak kudeatu'),
            'erabiltzaileak' => (new Erabiltzailea())->guztiak(),
        ]);
    }

    /** Crea una cuenta nueva desde la administración. */
    public function sortuAction(): void
    {
        $this->requireAdmin();
        if (!$this->isPost()) {
            $this->renderFormulario(
                tr('Nuevo usuario', 'Erabiltzaile berria'),
                [],
                [],
                'sortu',
                null,
                true
            );
            return;
        }

        $this->verifyCsrf();
        $datos = $this->leerDatos();
        $errores = $this->validar($datos, true);
        $modelo = new Erabiltzailea();

        if (!$errores && $modelo->emailezBilatu($datos['email'])) {
            $errores[] = tr('Ese correo ya está registrado.', 'Email hori erregistratuta dago.');
        }

        if (!$errores) {
            try {
                $modelo->sortuAdministrazioan(
                    $datos['izena'],
                    $datos['email'],
                    $datos['pasahitza'],
                    $datos['rola']
                );
                flash(tr('Usuario creado.', 'Erabiltzailea sortu da.'));
                $this->redirect('erabiltzailea');
            } catch (PDOException $e) {
                if ($e->getCode() !== '23000') {
                    throw $e;
                }
                $errores[] = tr('Ese correo ya está registrado.', 'Email hori erregistratuta dago.');
            }
        }

        $this->renderFormulario(
            tr('Nuevo usuario', 'Erabiltzaile berria'),
            $datos,
            $errores,
            'sortu',
            null,
            true
        );
    }

    /** Edita los datos, el rol o la contraseña de un usuario. */
    public function editatuAction(): void
    {
        $this->requireAdmin();
        $id = (int) ($_GET['id'] ?? 0);
        $modelo = new Erabiltzailea();
        $usuario = $modelo->bilatu($id);

        if (!$usuario) {
            flash(tr('No se encontró el usuario.', 'Erabiltzailea ez da aurkitu.'), 'err');
            $this->redirect('erabiltzailea');
        }

        if (!$this->isPost()) {
            $this->renderFormulario(
                tr('Editar usuario', 'Erabiltzailea editatu'),
                $usuario,
                [],
                'editatu',
                $id,
                false
            );
            return;
        }

        $this->verifyCsrf();
        $datos = $this->leerDatos();
        $errores = $this->validar($datos, false);
        $emailExistente = !$errores ? $modelo->emailezBilatu($datos['email']) : null;
        if ($emailExistente && (int) $emailExistente['id'] !== $id) {
            $errores[] = tr('Ese correo ya está registrado.', 'Email hori erregistratuta dago.');
        }

        $usuarioActual = Controller::user();
        if (
            !$errores
            && (int) $usuarioActual['id'] === $id
            && $datos['rola'] !== 'admin'
        ) {
            $errores[] = tr(
                'No puedes quitarte tu propio rol de administrador.',
                'Ezin diozu zeure buruari administratzaile-rola kendu.'
            );
        }

        if (!$errores) {
            try {
                $actualizado = $modelo->eguneratuAdministrazioan(
                    $id,
                    $datos['izena'],
                    $datos['email'],
                    $datos['rola'],
                    $datos['pasahitza'] === '' ? null : $datos['pasahitza']
                );
            } catch (PDOException $e) {
                if ($e->getCode() !== '23000') {
                    throw $e;
                }
                $errores[] = tr('Ese correo ya está registrado.', 'Email hori erregistratuta dago.');
                $actualizado = false;
            } catch (RuntimeException $e) {
                $errores[] = tr(
                    'No se puede quitar el rol al último administrador.',
                    'Ezin zaio administratzaile-rola kendu azken administratzaileari.'
                );
                $actualizado = false;
            }

            if ($actualizado) {
                if ((int) $usuarioActual['id'] === $id) {
                    $_SESSION['user']['izena'] = $datos['izena'];
                    $_SESSION['user']['email'] = $datos['email'];
                }
                flash(tr('Usuario actualizado.', 'Erabiltzailea eguneratu da.'));
                $this->redirect('erabiltzailea');
            }

            if (!$errores) {
                flash(tr('No se encontró el usuario.', 'Erabiltzailea ez da aurkitu.'), 'err');
                $this->redirect('erabiltzailea');
            }
        }

        $this->renderFormulario(
            tr('Editar usuario', 'Erabiltzailea editatu'),
            $datos,
            $errores,
            'editatu',
            $id,
            false
        );
    }

    /** Elimina un usuario, salvo la cuenta actual o una cuenta con pedidos. */
    public function ezabatuAction(): void
    {
        $this->requireAdmin();
        if (!$this->isPost()) {
            $this->redirect('erabiltzailea');
        }
        $this->verifyCsrf();

        $id = (int) ($_POST['id'] ?? 0);
        if ((int) Controller::user()['id'] === $id) {
            flash(tr('No puedes eliminar tu propia cuenta.', 'Ezin duzu zure kontua ezabatu.'), 'err');
            $this->redirect('erabiltzailea');
        }

        try {
            $eliminado = (new Erabiltzailea())->ezabatuAdministrazioan($id);
            flash(
                $eliminado
                    ? tr('Usuario eliminado.', 'Erabiltzailea ezabatu da.')
                    : tr('No se encontró el usuario.', 'Erabiltzailea ez da aurkitu.'),
                $eliminado ? 'ok' : 'err'
            );
        } catch (PDOException $e) {
            if ($e->getCode() !== '23000') {
                throw $e;
            }
            flash(
                tr(
                    'No se puede eliminar un usuario que tiene pedidos.',
                    'Ezin da eskaerak dituen erabiltzaile bat ezabatu.'
                ),
                'err'
            );
        } catch (RuntimeException $e) {
            flash(
                tr(
                    'No se puede eliminar al último administrador.',
                    'Ezin da azken administratzailea ezabatu.'
                ),
                'err'
            );
        }

        $this->redirect('erabiltzailea');
    }

    /** Lee y normaliza los campos enviados por el formulario. */
    private function leerDatos(): array
    {
        return [
            'izena' => trim((string) ($_POST['izena'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'pasahitza' => (string) ($_POST['pasahitza'] ?? ''),
            'rola' => (string) ($_POST['rola'] ?? 'bezeroa'),
        ];
    }

    /** Comprueba que los datos del usuario sean válidos. */
    private function validar(array $datos, bool $crear): array
    {
        $errores = [];
        if ($datos['izena'] === '' || mb_strlen($datos['izena']) > 100) {
            $errores[] = tr(
                'El nombre es obligatorio y no puede superar 100 caracteres.',
                'Izena beharrezkoa da eta ezin ditu 100 karaktere baino gehiago izan.'
            );
        }
        if (
            !filter_var($datos['email'], FILTER_VALIDATE_EMAIL)
            || strlen($datos['email']) > 150
        ) {
            $errores[] = tr('El correo electrónico no es válido.', 'Email baliogabea.');
        }
        if (
            ($crear && strlen($datos['pasahitza']) < 6)
            || (!$crear && $datos['pasahitza'] !== '' && strlen($datos['pasahitza']) < 6)
        ) {
            $errores[] = tr(
                'La contraseña debe tener al menos 6 caracteres.',
                'Pasahitzak 6 karaktere gutxienez izan behar ditu.'
            );
        }
        if (!in_array($datos['rola'], ['bezeroa', 'admin'], true)) {
            $errores[] = tr('El rol seleccionado no es válido.', 'Hautatutako rola ez da baliozkoa.');
        }

        return $errores;
    }

    /** Muestra el formulario para crear o editar una cuenta. */
    private function renderFormulario(
        string $titulo,
        array $usuario,
        array $errores,
        string $accion,
        ?int $id,
        bool $crear
    ): void {
        $this->render('erabiltzaileak/form', [
            'titulua' => $titulo,
            'usuario' => $usuario,
            'errorak' => $errores,
            'ekintza' => $accion,
            'id' => $id,
            'crear' => $crear,
        ]);
    }
}

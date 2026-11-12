<?php
/**
 * Controla el inicio de sesión, el registro de usuarios y el cierre de sesión.
 */
class AuthController extends Controller
{
    /** Comprueba las credenciales y guarda al usuario en la sesión. */
    public function loginAction(): void
    {
        if ($this->isPost()) {
            $this->verifyCsrf();
            $email = trim($_POST['email'] ?? '');
            $pass  = $_POST['pasahitza'] ?? '';

            $u = (new Erabiltzailea())->autentifikatu($email, $pass);
            if ($u) {
                session_regenerate_id(true);            // Evita reutilizar el identificador de sesión.
                $_SESSION['user'] = [
                    'id'    => (int) $u['id'],
                    'izena' => $u['izena'],
                    'email' => $u['email'],
                    'rola'  => $u['rola'],
                ];
                flash('Ongi etorri, ' . $u['izena'] . '!');
                $this->redirect('home');
            }
            flash('Email edo pasahitza okerra.', 'err');
            $this->redirect('auth', 'login');
        }
        $this->render('auth/login', ['titulua' => tr('Iniciar sesión', 'Saioa hasi')]);
    }

    /** Valida los datos y crea una cuenta nueva. */
    public function registerAction(): void
    {
        if ($this->isPost()) {
            $this->verifyCsrf();
            $izena = trim($_POST['izena'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $pass  = $_POST['pasahitza'] ?? '';

            $err = [];
            if ($izena === '') {
                $err[] = 'Izena beharrezkoa da.';
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $err[] = 'Email baliogabea.';
            }
            if (strlen($pass) < 6) {
                $err[] = 'Pasahitzak 6 karaktere gutxienez izan behar ditu.';
            }

            $model = new Erabiltzailea();
            if (!$err && $model->emailezBilatu($email)) {
                $err[] = 'Email hori erregistratuta dago.';
            }

            if ($err) {
                flash(implode(' ', $err), 'err');
                $this->redirect('auth', 'register');
            }
            $model->sortu($izena, $email, $pass);
            flash('Kontua sortu da. Hasi saioa.');
            $this->redirect('auth', 'login');
        }
        $this->render('auth/register', ['titulua' => tr('Registrarse', 'Erregistratu')]);
    }

    /** Borra la sesión del usuario y conserva el idioma elegido. */
    public function logoutAction(): void
    {
        $language = currentLanguage();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        session_start();
        $_SESSION['lang'] = $language;
        flash('Saioa itxi duzu.');
        $this->redirect('home');
    }
}

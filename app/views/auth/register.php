<!-- Formulario para crear una cuenta de usuario. -->
<h1><?= tr('Registrarse', 'Erregistratu') ?></h1>
<form method="post" class="form narrow" action="index.php?c=auth&amp;a=register">
    <?= csrfField() ?>
    <label><?= tr('Nombre', 'Izena') ?> <input type="text" name="izena" required autofocus></label>
    <label><?= tr('Correo electrónico', 'Emaila') ?> <input type="email" name="email" required></label>
    <label><?= tr('Contraseña (mínimo 6 caracteres)', 'Pasahitza (gutxienez 6 karaktere)') ?> <input type="password" name="pasahitza" minlength="6" required></label>
    <button class="btn"><?= tr('Crear cuenta', 'Kontua sortu') ?></button>
    <p class="muted"><?= tr('¿Ya tienes cuenta?', 'Badaukazu kontua?') ?> <a href="index.php?c=auth&amp;a=login"><?= tr('Iniciar sesión', 'Hasi saioa') ?></a></p>
</form>

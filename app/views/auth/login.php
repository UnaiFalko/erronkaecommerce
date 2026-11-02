<h1><?= tr('Iniciar sesión', 'Saioa hasi') ?></h1>
<form method="post" class="form narrow" action="index.php?c=auth&amp;a=login">
    <?= csrfField() ?>
    <label><?= tr('Correo electrónico', 'Emaila') ?> <input type="email" name="email" required autofocus></label>
    <label><?= tr('Contraseña', 'Pasahitza') ?> <input type="password" name="pasahitza" required></label>
    <button class="btn"><?= tr('Entrar', 'Sartu') ?></button>
    <p class="muted"><?= tr('¿No tienes cuenta?', 'Konturik ez duzu?') ?> <a href="index.php?c=auth&amp;a=register"><?= tr('Registrarse', 'Erregistratu') ?></a></p>
</form>

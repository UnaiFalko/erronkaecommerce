<a href="index.php?c=produktua"><?= tr('Volver a productos', 'Itzuli produktuetara') ?></a>
<h1><?= e($producto['izena']) ?></h1>
<p class="muted">
    <?= tr('Cuentas disponibles:', 'Kontu erabilgarriak:') ?>
    <strong><?= (int) $disponibles ?></strong>
</p>

<form method="post" class="form" action="index.php?c=produktua&amp;a=cuentas&amp;id=<?= (int) $producto['id'] ?>">
    <?= csrfField() ?>
    <label><?= tr('Usuario o correo', 'Erabiltzailea edo posta') ?>
        <input type="text" name="usuario" maxlength="255" autocomplete="off" required>
    </label>
    <label><?= tr('Contraseña', 'Pasahitza') ?>
        <input type="password" name="contrasena" maxlength="255" autocomplete="new-password" required>
    </label>
    <div class="row-between">
        <a href="index.php?c=produktua"><?= tr('Cancelar', 'Utzi') ?></a>
        <button class="btn"><?= tr('Añadir cuenta', 'Gehitu kontua') ?></button>
    </div>
</form>
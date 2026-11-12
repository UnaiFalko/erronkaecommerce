<!-- Formulario de alta o edición de una cuenta. -->
<h1><?= e($titulua) ?></h1>

<?php if ($errorak): ?>
    <div class="flash err">
        <ul><?php foreach ($errorak as $errorea): ?><li><?= e($errorea) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<form method="post" class="form"
      action="index.php?c=erabiltzailea&amp;a=<?= e($ekintza) ?><?= $id ? '&amp;id=' . (int) $id : '' ?>">
    <?= csrfField() ?>

    <label><?= tr('Nombre', 'Izena') ?>
        <input type="text" name="izena" maxlength="100" required value="<?= e($usuario['izena'] ?? '') ?>">
    </label>

    <label><?= tr('Correo electrónico', 'Emaila') ?>
        <input type="email" name="email" maxlength="150" required value="<?= e($usuario['email'] ?? '') ?>">
    </label>

    <label>
        <?= $crear ? tr('Contraseña', 'Pasahitza') : tr('Nueva contraseña', 'Pasahitz berria') ?>
        <input type="password" name="pasahitza" minlength="6" <?= $crear ? 'required' : '' ?>
               autocomplete="new-password">
        <?php if (!$crear): ?>
            <span class="muted"><?= tr('Déjalo vacío para conservar la contraseña actual.', 'Utzi hutsik uneko pasahitza mantentzeko.') ?></span>
        <?php endif; ?>
    </label>

    <label><?= tr('Rol', 'Rola') ?>
        <select name="rola" required>
            <option value="bezeroa" <?= ($usuario['rola'] ?? 'bezeroa') === 'bezeroa' ? 'selected' : '' ?>>
                <?= tr('Cliente', 'Bezeroa') ?>
            </option>
            <option value="admin" <?= ($usuario['rola'] ?? '') === 'admin' ? 'selected' : '' ?>>
                <?= tr('Administrador', 'Administratzailea') ?>
            </option>
        </select>
    </label>

    <div class="row-between">
        <a href="index.php?c=erabiltzailea"><?= tr('Cancelar', 'Utzi') ?></a>
        <button class="btn" type="submit"><?= tr('Guardar', 'Gorde') ?></button>
    </div>
</form>
